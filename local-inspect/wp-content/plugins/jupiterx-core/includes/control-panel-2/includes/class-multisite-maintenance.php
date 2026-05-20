<?php
defined( 'ABSPATH' ) || die();

/**
 * Multisite Maintenance Tool.
 *
 * Provides batched asset clearing, Elementor DB upgrades, and cache flushing
 * across all sites in a WordPress multisite network. Designed to handle
 * networks with 500+ sites by processing in small configurable batches with
 * per-site error isolation, memory cleanup, and generous time limits.
 *
 * The frontend orchestrates batching: it sends one AJAX request per batch,
 * retries failures with exponential backoff, persists progress to localStorage
 * for resume, and calculates an ETA.
 *
 * @package JupiterX_Core\Framework\Control_Panel\Multisite_Maintenance
 *
 * @since 4.8.0
 */

if ( ! class_exists( 'JupiterX_Core_Multisite_Maintenance' ) ) {

	class JupiterX_Core_Multisite_Maintenance {

		const DEFAULT_BATCH_SIZE = 5;

		const AJAX_ACTION = 'jupiterx_multisite_maintenance';

		/**
		 * Per-batch PHP time limit in seconds. Generous to allow heavy ops
		 * like Elementor CSS regeneration on large sites.
		 */
		const BATCH_TIME_LIMIT = 300;

		/**
		 * Elementor CSS files deleted per AJAX request (chunked clear).
		 */
		const ELEMENTOR_CSS_FILES_CHUNK = 40;

		public function __construct() {
			add_action( 'wp_ajax_' . self::AJAX_ACTION, [ $this, 'ajax_handler' ] );
		}

		/**
		 * Raise PHP memory for maintenance AJAX (Elementor regenerate / large stacks can exceed 512M).
		 *
		 * @return void
		 */
		private function boost_memory_limit() {
			if ( function_exists( 'wp_raise_memory_limit' ) ) {
				wp_raise_memory_limit( 'admin' );
			}

			$current = ini_get( 'memory_limit' );
			if ( ! $current || '-1' === $current || ! function_exists( 'wp_convert_hr_to_bytes' ) ) {
				return;
			}

			$bytes  = wp_convert_hr_to_bytes( $current );
			$target = 1024 * 1024 * 1024;

			if ( $bytes > 0 && $bytes < $target ) {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@ini_set( 'memory_limit', '1024M' );
			}
		}

		/**
		 * Multisite: super admins only. Single site: administrators with manage_options.
		 *
		 * @return bool
		 */
		private function current_user_can_run_maintenance() {
			if ( is_multisite() ) {
				return is_super_admin();
			}

			return current_user_can( 'manage_options' );
		}

		public function ajax_handler() {
			check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

			$maintenance_shutdown_log = function () {
				$last = error_get_last();
				if ( ! is_array( $last ) || empty( $last['message'] ) ) {
					return;
				}
				$fatal_types = [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ];
				if ( ! in_array( (int) $last['type'], $fatal_types, true ) ) {
					return;
				}
				if ( ! function_exists( 'error_log' ) ) {
					return;
				}
				error_log(
					sprintf(
						'[JupiterX Cache / maintenance] Fatal: %s in %s:%d',
						$last['message'],
						isset( $last['file'] ) ? $last['file'] : '',
						isset( $last['line'] ) ? (int) $last['line'] : 0
					)
				);
			};

			register_shutdown_function( $maintenance_shutdown_log );

			if ( ! $this->current_user_can_run_maintenance() ) {
				wp_send_json_error(
					is_multisite()
						? __( 'Only network super administrators can run network maintenance.', 'jupiterx-core' )
						: __( 'You do not have permission to run site maintenance.', 'jupiterx-core' )
				);
			}

			$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';

			switch ( $type ) {
				case 'get_sites':
					$this->handle_get_sites();
					break;

				case 'process_batch':
					$this->handle_process_batch();
					break;

				case 'elementor_css_chunk':
					$this->handle_elementor_css_chunk();
					break;

				default:
					wp_send_json_error( __( 'Invalid request type.', 'jupiterx-core' ) );
			}
		}

		/**
		 * Return the full list of site IDs and metadata for the frontend to
		 * orchestrate batching.
		 */
		private function handle_get_sites() {
			$this->boost_memory_limit();

			$sites      = $this->get_all_sites();
			$operations = $this->detect_available_operations();

			wp_send_json_success( [
				'sites'      => $sites,
				'batch_size' => self::DEFAULT_BATCH_SIZE,
				'operations' => $operations,
			] );
		}

		/**
		 * Process a single batch of site IDs with the requested operations.
		 *
		 * Each site is processed inside its own try/catch so a failure on one
		 * site does not abort the rest of the batch. Memory is cleaned between
		 * sites to prevent OOM on large batches.
		 */
		private function handle_process_batch() {
			try {
				$this->boost_memory_limit();

				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@set_time_limit( self::BATCH_TIME_LIMIT );

				$site_ids   = isset( $_POST['site_ids'] ) ? array_map( 'intval', (array) $_POST['site_ids'] ) : [];
				$operations = isset( $_POST['operations'] ) ? array_map( 'sanitize_text_field', (array) $_POST['operations'] ) : [];

				if ( empty( $site_ids ) || empty( $operations ) ) {
					wp_send_json_error( __( 'Missing site IDs or operations.', 'jupiterx-core' ) );
				}

				$results = [];

				foreach ( $site_ids as $site_id ) {
					$site_result = $this->process_single_site( $site_id, $operations );
					$results[]   = $site_result;

					$this->cleanup_between_sites();
				}

				wp_send_json_success( [
					'results' => $results,
				] );
			} catch ( \Throwable $e ) {
				if ( function_exists( 'error_log' ) ) {
					error_log(
						sprintf(
							'[JupiterX Cache / maintenance] %s in %s:%d',
							$e->getMessage(),
							$e->getFile(),
							$e->getLine()
						)
					);
				}

				wp_send_json_error(
					sprintf(
						/* translators: %s: error message */
						__( 'Maintenance request failed: %s', 'jupiterx-core' ),
						$e->getMessage()
					),
					500
				);
			}
		}

		/**
		 * Chunked Elementor CSS clear: delete files in small batches, then one
		 * finalize step (post meta + options via Files_Manager::clear_cache).
		 * Avoids generate_css() which times out on large sites.
		 *
		 * POST: site_id (int), step (files|finalize), chunk_size (optional).
		 */
		private function handle_elementor_css_chunk() {
			$site_id = isset( $_POST['site_id'] ) ? (int) $_POST['site_id'] : 0;
			$step    = isset( $_POST['step'] ) ? sanitize_text_field( wp_unslash( $_POST['step'] ) ) : 'files';

			if ( $site_id < 1 ) {
				wp_send_json_error( __( 'Invalid site ID.', 'jupiterx-core' ) );
			}

			if ( ! in_array( $step, [ 'files', 'finalize' ], true ) ) {
				wp_send_json_error( __( 'Invalid chunk step.', 'jupiterx-core' ) );
			}

			$switched_blog = false;

			try {
				$this->boost_memory_limit();

				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@set_time_limit( 120 );

				if ( ! $this->current_user_can_run_maintenance() ) {
					wp_send_json_error(
						is_multisite()
							? __( 'Only network super administrators can run network maintenance.', 'jupiterx-core' )
							: __( 'You do not have permission to run site maintenance.', 'jupiterx-core' )
					);
				}

				if ( is_multisite() ) {
					$site = get_site( $site_id );
					if ( ! $site ) {
						wp_send_json_error( __( 'Site not found.', 'jupiterx-core' ) );
					}
					switch_to_blog( $site_id );
					$switched_blog = true;
				} elseif ( (int) get_current_blog_id() !== $site_id ) {
					wp_send_json_error( __( 'Invalid site ID.', 'jupiterx-core' ) );
				}

				if ( ! class_exists( '\Elementor\Plugin' ) ) {
					if ( $switched_blog ) {
						restore_current_blog();
					}
					wp_send_json_error( __( 'Elementor not active on this site.', 'jupiterx-core' ) );
				}

				if ( 'finalize' === $step ) {
					$payload = $this->elementor_css_chunk_finalize();
					if ( $switched_blog ) {
						restore_current_blog();
					}
					wp_send_json_success( $payload );
				}

				$chunk_size = isset( $_POST['chunk_size'] ) ? (int) $_POST['chunk_size'] : self::ELEMENTOR_CSS_FILES_CHUNK;
				$chunk_size = (int) apply_filters( 'jupiterx_maintenance/elementor_css_chunk_size', $chunk_size );
				$chunk_size = max( 5, min( 200, $chunk_size ) );

				$payload = $this->elementor_css_chunk_delete_files( $chunk_size );

				if ( $switched_blog ) {
					restore_current_blog();
				}

				wp_send_json_success( $payload );
			} catch ( \Throwable $e ) {
				if ( $switched_blog ) {
					restore_current_blog();
				}

				if ( function_exists( 'error_log' ) ) {
					error_log(
						sprintf(
							'[JupiterX Cache / maintenance] elementor_css_chunk: %s in %s:%d',
							$e->getMessage(),
							$e->getFile(),
							$e->getLine()
						)
					);
				}

				wp_send_json_error(
					sprintf(
						/* translators: %s: error message */
						__( 'Chunk request failed: %s', 'jupiterx-core' ),
						$e->getMessage()
					),
					500
				);
			}
		}

		/**
		 * Delete up to $chunk_size Elementor CSS files from uploads (sorted).
		 *
		 * @param int $chunk_size Max files to unlink this request.
		 * @return array
		 */
		private function elementor_css_chunk_delete_files( $chunk_size ) {
			if ( ! class_exists( '\Elementor\Core\Files\Base' ) ) {
				return [
					'step'              => 'files',
					'removed'           => 0,
					'remaining_after'   => 0,
					'done'              => true,
					'samples'           => [],
					'skipped_api_error' => true,
				];
			}

			$dir     = \Elementor\Core\Files\Base::get_base_uploads_dir() . \Elementor\Core\Files\Base::DEFAULT_FILES_DIR;
			$pattern = $dir . '*';
			$files   = glob( $pattern );

			if ( ! is_array( $files ) ) {
				$files = [];
			}

			$files = array_values(
				array_filter(
					$files,
					function ( $path ) {
						return is_string( $path ) && is_file( $path );
					}
				)
			);

			sort( $files, SORT_STRING );

			$n = count( $files );

			if ( 0 === $n ) {
				return [
					'step'            => 'files',
					'removed'         => 0,
					'remaining_after' => 0,
					'done'            => true,
					'samples'         => [],
				];
			}

			$take  = min( $chunk_size, $n );
			$slice = array_slice( $files, 0, $take );
			$samples = [];

			foreach ( $slice as $path ) {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				@unlink( $path );
				if ( count( $samples ) < 5 ) {
					$samples[] = basename( $path );
				}
			}

			$removed           = count( $slice );
			$remaining_after   = $n - $removed;

			return [
				'step'            => 'files',
				'removed'         => $removed,
				'remaining_after' => $remaining_after,
				'done'            => ( 0 === $remaining_after ),
				'samples'         => $samples,
			];
		}

		/**
		 * Run Elementor meta / options cleanup (same as clear_cache after files are gone).
		 *
		 * @return array
		 */
		private function elementor_css_chunk_finalize() {
			try {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			} catch ( \Throwable $th ) {
				return [
					'step'    => 'finalize',
					'status'  => 'error',
					'message' => $th->getMessage(),
				];
			}

			return [
				'step'    => 'finalize',
				'status'  => 'success',
				'message' => __( 'Elementor CSS files, post meta, and related caches cleared. CSS will regenerate when pages are viewed.', 'jupiterx-core' ),
			];
		}

		/**
		 * Process all operations for a single site, fully isolated.
		 *
		 * @param int   $site_id    Blog ID to process.
		 * @param array $operations Operation keys to run.
		 * @return array
		 */
		private function process_single_site( $site_id, $operations ) {
			$site_result = [
				'site_id' => $site_id,
				'name'    => '',
				'url'     => '',
				'actions' => [],
			];

			try {
				if ( is_multisite() ) {
					switch_to_blog( $site_id );
				}

				$site_result['name'] = get_bloginfo( 'name' );
				$site_result['url']  = home_url();

				foreach ( $operations as $operation ) {
					try {
						$site_result['actions'][ $operation ] = $this->run_operation( $operation );
					} catch ( \Throwable $th ) {
						$site_result['actions'][ $operation ] = [
							'status'  => 'error',
							'message' => $th->getMessage(),
						];
					}

					$this->cleanup_between_sites();
				}

				if ( is_multisite() ) {
					restore_current_blog();
				}
			} catch ( \Throwable $th ) {
				if ( is_multisite() ) {
					restore_current_blog();
				}

				$site_result['actions']['_fatal'] = [
					'status'  => 'error',
					'message' => sprintf(
						/* translators: %s: error message */
						__( 'Fatal error processing site: %s', 'jupiterx-core' ),
						$th->getMessage()
					),
				];
			}

			return $site_result;
		}

		/**
		 * Free accumulated memory between sites.
		 *
		 * switch_to_blog() and Elementor's Plugin::$instance accumulate data in
		 * global caches. Flushing the WP object cache between sites prevents OOM
		 * on large networks.
		 */
		private function cleanup_between_sites() {
			wp_cache_flush();

			if ( function_exists( 'gc_collect_cycles' ) ) {
				gc_collect_cycles();
			}
		}

		/**
		 * Execute a single operation in the current blog context.
		 *
		 * @param string $operation Operation key.
		 * @return array{status: string, message: string}
		 */
		private function run_operation( $operation ) {
			switch ( $operation ) {
				case 'jupiterx_flush':
					return $this->op_jupiterx_flush();

				case 'elementor_flush_css':
					return $this->op_elementor_flush_css();

				case 'elementor_regenerate_css':
					return $this->op_elementor_regenerate_css();

				case 'elementor_update_db':
					return $this->op_elementor_update_db();

				case 'elementor_pro_update_db':
					return $this->op_elementor_pro_update_db();

				case 'crocoblock_flush':
					return $this->op_crocoblock_flush();

				case 'page_cache_flush':
					return $this->op_page_cache_flush();

				default:
					return [
						'status'  => 'skipped',
						'message' => __( 'Unknown operation.', 'jupiterx-core' ),
					];
			}
		}

		// ------------------------------------------------------------------
		// Operations
		// ------------------------------------------------------------------

		private function op_jupiterx_flush() {
			if ( ! function_exists( 'jupiterx_remove_dir' ) || ! function_exists( 'jupiterx_get_compiler_dir' ) ) {
				return [
					'status'  => 'skipped',
					'message' => __( 'JupiterX functions not available.', 'jupiterx-core' ),
				];
			}

			jupiterx_remove_dir( jupiterx_get_compiler_dir() );
			jupiterx_remove_dir( jupiterx_get_compiler_dir( true ) );

			if ( function_exists( 'jupiterx_get_images_dir' ) ) {
				jupiterx_remove_dir( jupiterx_get_images_dir() );
			}

			if ( function_exists( 'jupiterx_flush_crocoblock_cache' ) ) {
				jupiterx_flush_crocoblock_cache();
			}

			return [
				'status'  => 'success',
				'message' => __( 'JupiterX assets cleared.', 'jupiterx-core' ),
			];
		}

		private function op_elementor_flush_css() {
			if ( ! class_exists( '\Elementor\Plugin' ) ) {
				return [
					'status'  => 'skipped',
					'message' => __( 'Elementor not active on this site.', 'jupiterx-core' ),
				];
			}

			try {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			} catch ( \Throwable $th ) {
				return [
					'status'  => 'error',
					'message' => $th->getMessage(),
				];
			}

			return [
				'status'  => 'success',
				'message' => __( 'Elementor CSS cache cleared.', 'jupiterx-core' ),
			];
		}

		private function op_elementor_regenerate_css() {
			// Full regenerate (generate_css) is omitted — it times out on large sites.
			// The control panel uses elementor_css_chunk for logged, chunked clears.
			$result = $this->op_elementor_flush_css();

			if ( 'success' === $result['status'] ) {
				$result['message'] = __( 'Elementor CSS cache cleared (full regenerate was removed; use “Clear Elementor CSS & data (chunked)” in the panel for detailed logs).', 'jupiterx-core' );
			}

			return $result;
		}

		private function op_elementor_update_db() {
			if ( ! class_exists( '\Elementor\Core\Upgrade\Manager' ) ) {
				return [
					'status'  => 'skipped',
					'message' => __( 'Elementor upgrade manager not available.', 'jupiterx-core' ),
				];
			}

			try {
				return $this->do_elementor_db_upgrade( '\Elementor\Core\Upgrade\Manager', 'Elementor' );
			} catch ( \Throwable $th ) {
				return [
					'status'  => 'error',
					'message' => $th->getMessage(),
				];
			}
		}

		private function op_elementor_pro_update_db() {
			if ( ! class_exists( '\ElementorPro\Core\Upgrade\Manager' ) ) {
				return [
					'status'  => 'skipped',
					'message' => __( 'Elementor Pro upgrade manager not available.', 'jupiterx-core' ),
				];
			}

			try {
				return $this->do_elementor_db_upgrade( '\ElementorPro\Core\Upgrade\Manager', 'Elementor Pro' );
			} catch ( \Throwable $th ) {
				return [
					'status'  => 'error',
					'message' => $th->getMessage(),
				];
			}
		}

		/**
		 * Mirrors Elementor WP-CLI `update db --force`.
		 *
		 * @param string $manager_class Fully qualified class name.
		 * @param string $label         Human-readable label.
		 * @return array{status: string, message: string}
		 */
		private function do_elementor_db_upgrade( $manager_class, $label ) {
			/** @var \Elementor\Core\Base\DB_Upgrades_Manager $manager */
			$manager = new $manager_class();

			if ( ! $manager->should_upgrade() ) {
				return [
					'status'  => 'success',
					/* translators: %s: plugin label */
					'message' => sprintf( __( '%s DB already up to date.', 'jupiterx-core' ), $label ),
				];
			}

			$updater   = $manager->get_task_runner();
			$callbacks = $manager->get_upgrade_callbacks();
			$did_tasks = false;

			if ( ! empty( $callbacks ) ) {
				$updater->handle_immediately( $callbacks );
				$did_tasks = true;
			}

			$manager->on_runner_complete( $did_tasks );

			return [
				'status'  => 'success',
				/* translators: 1: plugin label, 2: number of migrations */
				'message' => sprintf( __( '%1$s DB updated (%2$d migrations).', 'jupiterx-core' ), $label, count( $callbacks ) ),
			];
		}

		/**
		 * Legacy operation key for resumed runs (previously a separate checkbox).
		 * New UI clears this via op_jupiterx_flush(); do not duplicate full JupiterX flush here.
		 *
		 * @return array{status: string, message: string}
		 */
		private function op_crocoblock_flush() {
			if ( ! function_exists( 'jupiterx_flush_crocoblock_cache' ) ) {
				return [
					'status'  => 'skipped',
					'message' => __( 'Additional style cache flush is not available on this site.', 'jupiterx-core' ),
				];
			}

			jupiterx_flush_crocoblock_cache();

			return [
				'status'  => 'success',
				'message' => __( 'Style caches cleared.', 'jupiterx-core' ),
			];
		}

		private function op_page_cache_flush() {
			$flushed = [];

			if ( function_exists( 'w3tc_pgcache_flush' ) ) {
				w3tc_pgcache_flush();
				$flushed[] = 'W3 Total Cache';
			}

			if ( function_exists( 'wp_cache_clear_cache' ) ) {
				wp_cache_clear_cache();
				$flushed[] = 'WP Super Cache';
			}

			if ( function_exists( 'rocket_clean_domain' ) ) {
				rocket_clean_domain();
				$flushed[] = 'WP Rocket';
			}

			if ( class_exists( 'WpFastestCache' ) && isset( $GLOBALS['wp_fastest_cache'] ) ) {
				$GLOBALS['wp_fastest_cache']->deleteCache();
				$flushed[] = 'WP Fastest Cache';
			}

			if ( class_exists( 'autoptimizeCache' ) ) {
				\autoptimizeCache::clearall();
				$flushed[] = 'Autoptimize';
			}

			if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
				sg_cachepress_purge_cache();
				$flushed[] = 'SiteGround Optimizer';
			}

			if ( class_exists( 'LiteSpeed_Cache_API' ) ) {
				\LiteSpeed_Cache_API::purge_all();
				$flushed[] = 'LiteSpeed Cache';
			}

			if ( empty( $flushed ) ) {
				return [
					'status'  => 'skipped',
					'message' => __( 'No page cache plugin was detected that can be purged from here.', 'jupiterx-core' ),
				];
			}

			return [
				'status'  => 'success',
				/* translators: %s: comma-separated cache plugin names */
				'message' => sprintf( __( 'Page caches flushed: %s.', 'jupiterx-core' ), implode( ', ', $flushed ) ),
			];
		}

		// ------------------------------------------------------------------
		// Helpers
		// ------------------------------------------------------------------

		/**
		 * Get all sites in the network, or a single-site fallback.
		 *
		 * @return array
		 */
		private function get_all_sites() {
			if ( ! is_multisite() ) {
				return [
					[
						'id'   => get_current_blog_id(),
						'name' => get_bloginfo( 'name' ),
						'url'  => home_url(),
					],
				];
			}

			$wp_sites = get_sites( [
				'number' => 0,
			] );

			$sites = [];

			foreach ( $wp_sites as $site ) {
				$blog_id = (int) $site->blog_id;
				$name    = get_blog_option( $blog_id, 'blogname' );
				if ( ! is_string( $name ) || '' === $name ) {
					$name = $site->domain . $site->path;
				}
				$sites[] = [
					'id'   => $blog_id,
					'name' => $name,
					'url'  => get_home_url( $blog_id, '/' ),
				];
			}

			return $sites;
		}

		/**
		 * Detect which operations are available based on active plugins.
		 *
		 * @return array
		 */
		private function detect_available_operations() {
			$ops = [
				[
					'id'          => 'jupiterx_flush',
					'label'       => __( 'Clear JupiterX Assets', 'jupiterx-core' ),
					'description' => __( 'Removes compiled CSS, JS and image caches from the JupiterX compiler, and clears related generated style data when supported.', 'jupiterx-core' ),
					'available'   => function_exists( 'jupiterx_remove_dir' ) && function_exists( 'jupiterx_get_compiler_dir' ),
					'default'     => true,
				],
				[
					'id'          => 'elementor_flush_css',
					'label'       => __( 'Clear Elementor CSS', 'jupiterx-core' ),
					'description' => __( 'Deletes all cached Elementor CSS files. New CSS is generated on next page visit.', 'jupiterx-core' ),
					'available'   => class_exists( '\Elementor\Plugin' ),
					'default'     => true,
				],
				[
					'id'          => 'elementor_regenerate_css',
					'label'       => __( 'Clear Elementor CSS & data (chunked)', 'jupiterx-core' ),
					'description' => __(
						'Removes Elementor CSS files in small server batches (each step is logged), then clears Elementor CSS-related post meta and caches. Does not pre-build CSS for every page — files are recreated on page view. Safer than the old “regenerate all” on large sites.',
						'jupiterx-core'
					),
					'available'   => class_exists( '\Elementor\Plugin' ),
					'default'     => true,
				],
				[
					'id'          => 'elementor_update_db',
					'label'       => __( 'Upgrade Elementor Database', 'jupiterx-core' ),
					'description' => __( 'Runs pending Elementor database migrations (e.g. after an Elementor version update). Not a cache clear — enable only when migrations are pending.', 'jupiterx-core' ),
					'available'   => class_exists( '\Elementor\Core\Upgrade\Manager' ),
					'default'     => true,
				],
				[
					'id'          => 'elementor_pro_update_db',
					'label'       => __( 'Upgrade Elementor Pro Database', 'jupiterx-core' ),
					'description' => __( 'Runs pending Elementor Pro database migrations. Not a cache clear — enable only when migrations are pending.', 'jupiterx-core' ),
					'available'   => class_exists( '\ElementorPro\Core\Upgrade\Manager' ),
					'default'     => false,
				],
				[
					'id'          => 'page_cache_flush',
					'label'       => __( 'Flush Page Cache Plugins', 'jupiterx-core' ),
					'description' => __( 'Purges WP Rocket, W3TC, WP Super Cache, LiteSpeed and other page caches.', 'jupiterx-core' ),
					'available'   => true,
					'default'     => true,
				],
			];

			return array_values( array_filter( $ops, function ( $op ) {
				return $op['available'];
			} ) );
		}
	}

	new JupiterX_Core_Multisite_Maintenance();
}
