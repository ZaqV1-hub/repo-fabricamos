<?php
/**
 * This class is responsible to managing all plugins & theme updates.
 *
 * @package JupiterX_Core\Control_Panel_2
 */

/**
 * Updates Manager class.
 *
 * @since 1.18.0
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class JupiterX_Core_Control_Panel_Updates_Manager {
	/**
	 * Transient to remember when update was checked last time.
	 *
	 * @since 1.18.0
	 */
	const LAST_CHECKED_TRANSIENT_KEY = 'jupiterx_core_cp_updates_last_checked';

	/**
	 * Transient to remember updates.
	 *
	 * @since 1.18.0
	 */
	const UPDATES_TRANSIENT_KEY = 'jupiterx_core_cp_updates';

	/**
	 * Transient to get products.
	 *
	 * @since 4.8.7
	 */
	const PRODUCTS_TRANSIENT_KEY = 'jupiterx_cp_settings_products';

	/**
	 * Artbees themes products API.
	 *
	 * @since 4.8.7
	 */
	const THEMES_PRODUCTS_API = 'https://my.artbees.net/wp-json/artbees-portal-products/v1/products';

	/**
	 * Updates Manager Constructor
	 *
	 * @since 1.18.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_jupiterx_core_cp_get_updates', [ $this, 'get_updates' ] );
		add_action( 'upgrader_process_complete', [ $this, 'clear_transients' ] );
	}

	/**
	 * Get updates.
	 *
	 * @since 1.18.0
	 *
	 * @return string
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public function get_updates() {
		check_ajax_referer( 'jupiterx_control_panel' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
		}

		try {
			$force = false;

			if ( ! empty( $_POST['force'] ) ) {
				$force = sanitize_text_field( wp_unslash( $_POST['force'] ) ) === 'true';
			}

			$timestamp = get_transient( self::LAST_CHECKED_TRANSIENT_KEY );
			$updates   = get_transient( self::UPDATES_TRANSIENT_KEY );

			$products = get_transient( self::PRODUCTS_TRANSIENT_KEY );

			if ( empty( $products ) ) {
				$force = true;
			}

			if ( $force || false === $timestamp || false === $updates ) {
				$force     = true;
				$timestamp = time();

				$this->clear_plugin_transients();

				set_transient( self::LAST_CHECKED_TRANSIENT_KEY, $timestamp, DAY_IN_SECONDS );
			}

			if ( $force ) {
				$updates = [];
				$updates = array_merge( $updates, $this->get_theme_latest_update() );
				$updates = array_merge( $updates, $this->get_plugins_updates() );

				set_transient( self::UPDATES_TRANSIENT_KEY, $updates, DAY_IN_SECONDS );
			}

			if ( empty( $products ) || $force ) {
				$products = $this->set_products_transient();
			}

			$updates_ids = array_column( $updates, 'post_id' );

			if ( count( $updates_ids ) !== count( $updates ) ) {
				$updates_theme = array_filter( $updates, function( $item ) {
					return ! array_key_exists( 'post_id', $item );
				} );

				$updates = array_filter( $updates, function( $item ) {
					return array_key_exists( 'post_id', $item );
				} );

				// Re-index the array to maintain sequential indexes
				$updates = array_values( $updates );

				$merged_updates_theme = [];

				foreach ( $updates_theme as $update_theme ) {
					if ( 'theme' === $update_theme['type'] ) {
						$product_theme = array_filter( $products, function( $item ) use ( $update_theme ) {
							return 'theme' === $item->type && $item->slug === $update_theme['slug'];
						} );

						$merged_updates_theme[] = (object) array_merge( $update_theme, (array) $product_theme[0] );
					}
				}
			}

			$updates_lookup = array_combine( $updates_ids, $updates );

			$merged_update = array_filter( $products, function( $item ) use ( $updates_lookup ) {
				return isset( $updates_lookup[ $item->ID ] );
			} );

			$merged_update = array_map( function( $item ) use ( $updates_lookup ) {
				return (object) array_merge( $updates_lookup[ $item->ID ], (array) $item );
			}, $merged_update );

			if ( ! empty( $updates_theme ) ) {
				$merged_update = array_merge( $merged_updates_theme, $merged_update );
			}

			$updates = [];
			foreach ( $merged_update as $product ) {
				$product->active_version = $product->current_version;

				$selected_version = [];

				foreach ( $product->versions as $version ) {
					if ( ! empty( $version ) && ! empty( $product ) && isset( $version->name ) && isset( $product->new_version ) && $version->name === $product->new_version ) {
						$selected_version = $version;
						break;
					}
				}

				if ( empty( $selected_version ) && isset( $updates_lookup[ $product->ID ]['source'] ) ) {
					$selected_version = [
						'url' => $updates_lookup[ $product->ID ]['source'],
						'name' => $product->new_version,
					];
				}

				unset( $product->versions );

				$product->selected_version = $selected_version;

				$updates[] = $product;
			}

			$auto_updater_state = 'disabled';

			if ( class_exists( 'JupiterX_Core_Auto_Updates' ) ) {
				$auto_updater_state = JupiterX_Core_Auto_Updates::get_auto_updater_state();
			}

			wp_send_json_success( [
				'last_checked' => $timestamp,
				'updates' => $updates,
				'auto_updater_state' => $auto_updater_state,
			] );
		} catch ( Exception $e ) {
			wp_send_json_error();
		}
	}

	/**
	 * Get plugin updates.
	 *
	 * @since 1.18.0
	 *
	 * @return array
	 */
	public function get_plugins_updates() {
		$plugins = jupiterx_core_get_plugins_from_api();
		$plugins = jupiterx_core_update_plugins_status( $plugins );

		$updates = [];

		$id = 2;
		foreach ( $plugins as $plugin ) {
			if ( $plugin['update_needed'] ) {
				$updates[] = $this->plugin_update_format( $plugin, $id );

				$id++;
			}
		}

		return $updates;
	}

	/**
	 * Get theme latest update only.
	 *
	 * @since 1.18.0
	 *
	 * @return array
	 */
	public function get_theme_latest_update() {
		$updates  = new JupiterX_Core_Control_Panel_Theme_Updrades_Downgrades();
		$releases = $updates->get_release_notes();

		if ( ! is_array( $releases ) || count( $releases ) === 0 ) {
			return [];
		}

		$new_version = $this->get_theme_new_version( $releases );

		if ( false === $new_version ) {
			return [];
		}

		$release_id = $this->get_release_id( $releases, $new_version );

		if ( false === $release_id ) {
			return [];
		}

		return [
			[
				'id' => 1,
				'title' => __( 'Jupiter X' ),
				'current_version' => JUPITERX_VERSION,
				'new_version' => $new_version,
				'type' => 'theme',
				'slug' => 'jupiterx',
				'release_id' => $release_id,
				'img_url' => trailingslashit( jupiterx_core()->plugin_assets_url() ) . 'images/control-panel/jupiterx-updates-thumb.png',
			],
		];
	}

	/**
	 * Get release id.
	 *
	 * @since 1.18.0
	 *
	 * @param array $releases Available releases.
	 * @param string $version Release version.
	 *
	 * @return mixed
	 */
	public function get_release_id( $releases, $version ) {
		foreach ( $releases as $release ) {
			if ( 'V' . $version === $release->post_title ) {
				return $release->ID;
			}
		}

		return false;
	}

	/**
	 * Get theme latest version from available releases.
	 *
	 * @since 1.18.0
	 * @param array $releases Available released
	 *
	 * @return mixed
	 */
	public function get_theme_new_version( $releases ) {
		$new_version = JUPITERX_VERSION;

		foreach ( $releases as $index => $release ) {
			$release_version = trim( str_replace( 'V', '', $release->post_title ) );

			if ( version_compare( $release_version, JUPITERX_INITIAL_FREE_VERSION, '<' ) && ! jupiterx_is_premium() ) {
				return [];
			}

			$has_changelog = true;

			$version_compare = version_compare( $release_version, $new_version );

			if ( 1 === $version_compare ) {
				$new_version = $release_version;
			}
		}

		if ( version_compare( $new_version, JUPITERX_VERSION ) <= 0 ) {
			return false;
		}

		return $new_version;
	}

	/**
	 * Get plugin update in common format.
	 *
	 * @since 1.18.0
	 *
	 * @param array $plugin Plugin data.
	 * @param int $id Update Id.
	 *
	 * @return array
	 */
	private function plugin_update_format( $plugin, $id ) {
		return [
			'id' => $id,
			'title' => $plugin['name'],
			'post_id' => $plugin['id'],
			'source' => $plugin['source'],
			'slug' => $plugin['slug'],
			'current_version' => $plugin['version'],
			'new_version' => $plugin['server_version'],
			'type' => 'plugin',
			'update_url' => $plugin['update_url'],
			'activate_url' => $plugin['activate_url'],
			'img_url' => $plugin['img_url'],
			'basename' => ! empty( $plugin['basename'] ) ? $plugin['basename'] : '',
		];
	}

	/**
	 * Clear transients.
	 *
	 * @since 1.18.0
	 */
	public function clear_transients() {
		delete_transient( self::LAST_CHECKED_TRANSIENT_KEY );
		delete_transient( self::UPDATES_TRANSIENT_KEY );
	}

	/**
	 * Clear plugin transients.
	 *
	 * @since 1.18.0
	 */
	private function clear_plugin_transients() {
		delete_site_transient( 'update_plugins' );
		delete_site_transient( 'jupiterx_managed_plugins' );
		delete_transient( 'jupiterx_tgmpa_plugins' );
		delete_transient( 'jupiterx_tgmpa_plugins_check' );
	}

	/**
	 * Set products transient.
	 *
	 * @since 4.8.7
	 *
	 * @return array
	 *
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	private function set_products_transient() {
		$products = [];

		$products = wp_remote_retrieve_body(
			wp_remote_post( self::THEMES_PRODUCTS_API, [
				'body' => [
					'product' => 'jupiterx',
				],
			] )
		);

		$products = json_decode( $products );

		if ( ! is_array( $products ) || empty( $products ) ) {
			wp_send_json_error( __( 'There\'s a problem in fetching the products.', 'jupiterx-core' ) );
		}

		if ( ! jupiterx_is_pro() ) {
			$wp_theme = wp_remote_retrieve_body(
				wp_remote_get( 'https://api.wordpress.org/themes/info/1.1/?action=theme_information&request[slug]=jupiterx-lite&request[fields][versions]=true' )
			);

			$wp_theme = json_decode( $wp_theme );

			if ( ! is_object( $wp_theme ) || empty( $wp_theme ) ) {
				wp_send_json_error( __( 'There\'s a problem in fetching the products.', 'jupiterx-core' ) );
			}

			// Get jupitrx-lite versions from WP api.
			foreach ( $products as $product_key => $product ) {
				if ( 'theme' !== $product->type ) {
					continue;
				}

				if ( empty( $product->source ) ) {
					$product->source = 'wp-repo';
				}

				$product->versions = null;

				foreach ( $wp_theme->versions as $name => $url ) {
					if ( preg_match( '/trunk|-.*/m', $name ) ) {
						continue;
					}

					$products[ $product_key ]->versions[] = (object) [
						'name' => $name,
						'url'  => $url,
					];
				}
			}
		}

		// Get wp-repo plugins versions from WP api.
		foreach ( $products as $product_key => $product ) {
			if ( 'wp-repo' !== $product->source ) {
				continue;
			}

			$wp_plugin = wp_remote_retrieve_body(
				wp_remote_get( 'https://api.wordpress.org/plugins/info/1.0/' . $product->slug . '.json' )
			);

			$wp_plugin = json_decode( $wp_plugin );

			if ( ! is_object( $wp_plugin ) || empty( $wp_plugin ) ) {
				wp_send_json_error( __( 'There\'s a problem in fetching the wp-repo plugins\' versions.', 'jupiterx-core' ) );
			}

			foreach ( $wp_plugin->versions as $name => $url ) {
				if ( preg_match( '/trunk|-.*/m', $name ) ) {
					continue;
				}

				$products[ $product_key ]->versions[] = (object) [
					'name' => $name,
					'url'  => $url,
				];
			}
		}

		set_transient( self::PRODUCTS_TRANSIENT_KEY, $products, WEEK_IN_SECONDS );

		return $products;
	}
}

new JupiterX_Core_Control_Panel_Updates_Manager();
