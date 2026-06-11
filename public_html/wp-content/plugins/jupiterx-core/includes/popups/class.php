<?php
/**
 * Class to register JupiterX popups post type.
 *
 * @package JupiterX_Core\Post_Type
 * @since 3.7.0
 */

use Elementor\Plugin;
use Elementor\Utils as ElementorUtils;

defined( 'ABSPATH' ) || die();
/**
 * Handle the JupiterX popups post type.
 *
 * @since 3.7.0
 * @package JupiterX_Core\Post_Type
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class JupiterX_Popups {
	/**
	 * Jupiterx Popup Post type name.
	 *
	 * @var string
	 */
	const POST_TYPE = 'jupiterx-popups';

	/**
	 * Jupiterx Popup taxonomy name.
	 *
	 * @var string
	 */
	const POPUP_TAXONOMY = 'jupiterx-popup-category';

	/**
	 * Jupiterx Popup bulk export action name.
	 *
	 * @var string
	 */
	const BULK_EXPORT_ACTION = 'jupiterx_popup_export_multiple_templates';

	/**
	 * Each instance is stored separately in this array.
	 *
	 * @static
	 * @access private
	 * @var array
	 */
	private static $valid_popups = [];

	/**
	 * Loaded popup list.
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $loaded_popups = [];

	/**
	 * JupiterX_Popups Constructor.
	 *
	 * @since 3.7.0
	 */
	public function __construct() {
		$post_type = self::POST_TYPE;

		add_action( 'init', [ $this, 'register_post_type' ] );
		add_action( 'restrict_manage_posts', [ $this, 'filter_by_category' ] );
		add_filter( "bulk_actions-edit-{$post_type}", [ $this, 'bulk_export_action' ] );
		add_filter( "handle_bulk_actions-edit-{$post_type}", [ $this, 'bulk_export_multiple_templates' ], 10, 3 );
		add_action( "manage_{$post_type}_posts_columns", [ $this, 'admin_posts_columns_header' ], 10, 2 );
		add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'admin_posts_columns_content' ], 10, 2 );
		add_action( 'admin_init', [ $this, 'export_popup_action' ] );
		add_action( 'elementor/editor/footer', [ $this, 'add_editor_conditions_template' ] );
		add_action( 'template_include', [ $this, 'popup_template' ], 9999 );
		add_action( 'wp_footer', [ $this, 'popup_render' ], 10 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );

		// Change labels for jupiterx popup.
		add_filter( "post_type_labels_{$post_type}", [ $this, 'change_jupiterx_labels' ] );

		//Condition ajax.
		add_filter( 'option_elementor_cpt_support', [ $this, 'add_elementor_support' ] );
		add_filter( 'default_option_elementor_cpt_support', [ $this, 'add_elementor_support' ] );
		add_filter( 'post_row_actions', [ $this, 'export_action' ], 10, 2 );

		add_action( 'wp_ajax_jupiterx_popup_get_content', [ $this, 'jupiterx_popup_get_content' ] );
		add_action( 'wp_ajax_nopriv_jupiterx_popup_get_content', [ $this, 'jupiterx_popup_get_content' ] );

		// Handle popup transienst.
		add_action( 'updated_post_meta', [ $this, 'delete_popup_transients' ], 10, 2 );
		add_action( 'wp_trash_post', [ $this, 'delete_popup_transient' ], 10, 1 );
		add_action( 'untrash_post', [ $this, 'delete_popup_transient' ], 10, 1 );
	}

	/**
	 * Add a wrapper to elementor editor.
	 *
	 * @since 3.7.0
	 * @return void
	 */
	public function add_editor_conditions_template() {
		if ( 'jupiterx-popups' !== $this->get_post_type_name() ) {
			return;
		}
		?>
		<div id="jupiterx-conditons-root" class="jupiterx-conditons-root"></div>
		<?php
	}

	/**
	 * Check the post type by post id.
	 *
	 * @since 4.1.0
	 * @return string
	 */
	public function get_post_type_name() {
		$post = ! empty( $_GET['post'] ) && ! is_array( $_GET['post'] ) ? htmlspecialchars( $_GET['post'] ) : ''; // phpcs:ignore

		if ( empty( $post ) ) {
			$post = get_the_ID();
		}

		return get_post_type( $post );
	}


	/**
	 * Register popups post type and taxonomy.
	 *
	 * @since 3.7.0
	 * @return void
	 */
	public function register_post_type() {
		$args = [
			'hierarchical' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'show_admin_column' => true,
			'query_var' => is_admin(),
			'rewrite' => false,
			'public' => false,
			'labels' => [
				'name' => esc_html_x( 'Categories', 'Popup', 'jupiterx-core' ),
				'singular_name' => esc_html_x( 'Category', 'Popup', 'jupiterx-core' ),
				'all_items' => esc_html_x( 'All Categories', 'Popup', 'jupiterx-core' ),
			],
		];

		register_taxonomy( self::POPUP_TAXONOMY, self::POST_TYPE, $args );

		$args = [
			'label'               => esc_html__( 'Popups', 'jupiterx-core' ),
			'description'         => '',
			'labels'              => [],
			'supports'            => [ 'title' ],
			'taxonomies'          => [ self::POPUP_TAXONOMY ],
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'can_export'          => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest'        => true,
		];

		register_post_type( self::POST_TYPE, $args );
	}

	/**
	 * Change labels of jupipterx popup.
	 *
	 * @param object $labels Object of jupiterx popup labels.
	 * @since 3.7.0
	 * @return object
	 */
	public function change_jupiterx_labels( $labels ) {
		if ( isset( $labels->not_found ) ) {
			$labels->not_found = esc_html__( 'No popups found.', 'jupiterx-core' );
		}

		if ( isset( $labels->not_found_in_trash ) ) {
			$labels->not_found_in_trash = esc_html__( 'No popups found in trash.', 'jupiterx-core' );
		}

		return $labels;
	}

	/**
	 * Add filter by category.
	 *
	 * @param string $post_type Current screen post type slug.
	 * @since 3.7.0
	 * @return void
	 */
	public function filter_by_category( $post_type ) {
		if ( self::POST_TYPE !== $post_type ) {
			return;
		}

		$all_items = get_taxonomy( self::POPUP_TAXONOMY )->labels->all_items;

		$terms = get_terms( [
			'taxonomy'   => self::POPUP_TAXONOMY,
			'hide_empty' => false,
		] );

		if ( empty( $terms ) ) {
			return;
		}

		$dropdown_options = array(
			'show_option_all' => $all_items,
			'hide_empty' => 0,
			'hierarchical' => 1,
			'show_count' => 0,
			'orderby' => 'name',
			'value_field' => 'slug',
			'taxonomy' => self::POPUP_TAXONOMY,
			'name' => self::POPUP_TAXONOMY,
			'selected' => empty( $_GET[ self::POPUP_TAXONOMY ] ) ? '' : sanitize_text_field( $_GET[ self::POPUP_TAXONOMY ] ), //phpcs:ignore
		);

		echo '<label class="screen-reader-text" for="cat">' . esc_html_x( 'Filter by category', 'Popup', 'jupiterx-core' ) . '</label>';

		wp_dropdown_categories( $dropdown_options );
	}

	/**
	 * Add export to bulk action dropdown.
	 *
	 * @param array $actions array of available bulk actions.
	 * @since 3.7.0
	 * @return array
	 */
	public function bulk_export_action( $actions ) {
		$actions[ self::BULK_EXPORT_ACTION ] = esc_html__( 'Export', 'jupiterx-core' );

		return $actions;
	}

	/**
	 * Add jupiterx popup bulk export action.
	 *
	 * @param string $redirect_to The redirect URL.
	 * @param string $action      Selected bulk action.
	 * @param array  $popup_ids   Selected items for action.
	 * @since 3.7.0
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function bulk_export_multiple_templates( $redirect_to, $action, $popup_ids ) {
		if ( self::BULK_EXPORT_ACTION === $action ) {
			$files = [];

			$wp_upload_dir = wp_upload_dir();

			$temp_path = $wp_upload_dir['basedir'] . '/elementor/tmp';

			// Create temp path if it doesn't exist
			wp_mkdir_p( $temp_path );

			// Create all json files
			foreach ( $popup_ids as $popup_id ) {
				$file_data = $this->popup_export_data( $popup_id );

				if ( is_wp_error( $file_data ) ) {
					continue;
				}

				$complete_path = $temp_path . '/' . $file_data['name'];

				$put_contents = file_put_contents( $complete_path, $file_data['content'] ); // phpcs:ignore

				if ( ! $put_contents ) {
					return new \WP_Error( '404', sprintf( 'Cannot create file "%s".', $file_data['name'] ) );
				}

				$files[] = [
					'path' => $complete_path,
					'name' => $file_data['name'],
				];
			}

			if ( ! $files ) {
				return new \WP_Error( 'empty_files', 'There is no files to export (probably all the requested templates are empty).' );
			}

			$zip_archive_filename = 'jupiterx-popup-templates-' . gmdate( 'Y-m-d' ) . '.zip';

			$zip_archive = new \ZipArchive();

			$zip_complete_path = $temp_path . '/' . $zip_archive_filename;

			$zip_archive->open( $zip_complete_path, \ZipArchive::CREATE );

			foreach ( $files as $file ) {
				$zip_archive->addFile( $file['path'], $file['name'] );
			}

			$zip_archive->close();

			foreach ( $files as $file ) {
				unlink( $file['path'] );
			}

			$this->send_file_headers( $zip_archive_filename, filesize( $zip_complete_path ) );

			@ob_end_flush();

			@readfile( $zip_complete_path ); // phpcs:ignore

			unlink( $zip_complete_path );

			die;
		}
	}

	/**
	 * Add posts columns header.
	 *
	 * @param array $posts_columns array of posts columns.
	 * @since 3.7.0
	 * @return array
	 */
	public function admin_posts_columns_header( $posts_columns ) {
		$types = [
			'type' => [
				'name' => esc_html__( 'Type', 'jupiterx-core' ),
				'offset' => 2,
			],
			'instances' => [
				'name' => esc_html__( 'Instances', 'jupiterx-core' ),
				'offset' => 3,
			],
			'author' => [
				'name' => esc_html__( 'Author', 'jupiterx-core' ),
				'offset' => 4,
			],
			'shortcode' => [
				'name' => esc_html__( 'Shortcode', 'jupiterx-core' ),
				'offset' => 7,
			],
		];

		foreach ( $types as $key => $type ) {
			$offset = $type['offset'];
			$value  = [ $key => $type['name'] ];

			$posts_columns = array_slice( $posts_columns, 0, $offset, true ) + $value + array_slice( $posts_columns, $offset, null, true );
		}

		return $posts_columns;
	}

	/**
	 * Add posts columns content.
	 *
	 * @param string $column_name column name.
	 * @since 3.7.0
	 * @return string
	 */
	public function admin_posts_columns_content( $column_name, $post_id ) {
		static $content_echoed = [];

		// Check if content for this column and post ID has already been echoed.
		if ( isset( $content_echoed[ $column_name ][ $post_id ] ) && $content_echoed[ $column_name ][ $post_id ] ) {
			return;
		}

		$html = '';

		if ( 'type' === $column_name ) {
			$url = add_query_arg(
				[
					'post_type' => self::POST_TYPE,
				],
				admin_url( 'edit.php' )
			);

			$html = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( $url ),
				esc_html__( 'Popup', 'jupiterx-core' )
			);
		}

		if ( 'shortcode' === $column_name ) {
			$html = '<input class="elementor-shortcode-input" style="width: 100%;" type="text" readonly="" onfocus="this.select()" value="[elementor-template id=&quot;' . esc_attr( $post_id ) . '&quot;]">';
		}

		if ( 'instances' === $column_name && class_exists( 'JupiterX_Popups_Conditions_Manager' ) ) {
			$popup_conditions = new JupiterX_Popups_Conditions_Manager();

			$html = implode( '<br />', $popup_conditions->get_popup_coditions( $post_id ) );
		}

		// Echo content if it's not empty and mark it as echoed.
		if ( ! empty( $html ) ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$content_echoed[ $column_name ][ $post_id ] = true;
		}
	}

	/**
	 * Add elementor theme support for popup post type.
	 *
	 * @param array $value array of supported post types.
	 * @since 3.7.0
	 * @return array
	 */
	public function add_elementor_support( $value ) {
		if ( empty( $value ) ) {
			$value = [];
		}

		return array_merge( $value, [ self::POST_TYPE ] );
	}

	/**
	 * Add export template to popup posts.
	 *
	 * @param array $actions array of post action.
	 * @param object $post post data.
	 * @since 3.7.0
	 * @return array
	 */
	public function export_action( $actions, \WP_Post $post ) {
		if ( 'jupiterx-popups' !== $post->post_type || empty( $post ) ) {
			return $actions;
		}

		$export_link = add_query_arg(
			[
				'action' => 'jupiterx_export_popup',
				'template_id' => $post->ID,
				'nonce' => wp_create_nonce( 'jupiterx_export_popup' ),
			],
			admin_url( 'admin-ajax.php' )
		);

		$actions['export-template'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $export_link ),
			esc_html__( 'Export Popup', 'jupiterx-core' )
		);

		return $actions;
	}

	/**
	 * Send file headers.
	 *
	 * @param string $name File name.
	 * @param int    $size File size.
	 * @since 3.7.0
	 */
	public function send_file_headers( $name, $size ) {
		header( 'Pragma: public' );
		header( 'Expires: 0' );
		header( 'Cache-Control: public' );
		header( 'Content-Description: File Transfer' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename="' . $name . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Content-Length: ' . $size );
	}

	/**
	 * Export popup template event.
	 *
	 * @since 3.7.0
	 */
	public function export_popup_action() {
		if ( empty( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'jupiterx_export_popup' ) ) { // phpcs:ignore
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( empty( $_GET['action'] ) ) { // phpcs:ignore
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_GET['action'] ) );

		if ( 'jupiterx_export_popup' !== $action ) {
			return;
		}

		if ( empty( $_GET['template_id'] ) ) { // phpcs:ignore
			return;
		}

		$popup_id = sanitize_text_field( wp_unslash( $_GET['template_id'] ) );

		$data = $this->popup_export_data( $popup_id );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		$this->send_file_headers( $data['name'], strlen( $data['content'] ) );

		session_write_close();

		echo $data['content']; //phpcs:ignore

		die();
	}

	/**
	 * Get popup export data.
	 *
	 * @param int $popup_id current popup template id.
	 * @since 3.7.0
	 * @return array|\WP_Error
	 */
	public function popup_export_data( $popup_id ) {
		$content    = Plugin::$instance->documents->get( $popup_id )->get_elements_raw_data( null, true );
		$popup_data = [];

		if ( empty( $content ) ) {
			return new \WP_Error( 'empty_template', 'The template is empty' );
		}

		$popup_data['content'] = $content;

		if ( get_post_meta( $popup_id, '_elementor_page_settings', true ) ) {
			$page_settings = get_post_meta( $popup_id, '_elementor_page_settings', true );

			if ( ! empty( $page_settings ) ) {
				$popup_data['page_settings'] = $page_settings;
			}
		}

		if ( get_post_meta( $popup_id, '_jupiterx_popup_conditions', true ) ) {
			$popup_condition = get_post_meta( $popup_id, '_jupiterx_popup_conditions', true );

			if ( ! empty( $popup_condition ) ) {
				$popup_data['popup_conditions'] = $popup_condition;
			}
		}

		if ( get_post_meta( $popup_id, '_jupiterx_popup_triggers', true ) ) {
			$popup_condition = get_post_meta( $popup_id, '_jupiterx_popup_triggers', true );

			if ( ! empty( $popup_condition ) ) {
				$popup_data['popup_triggers'] = $popup_condition;
			}
		}

		$export_data = [
			'version' => Elementor\DB::DB_VERSION,
			'title'   => get_the_title( $popup_id ),
		];

		$export_data = array_merge( $export_data, $popup_data );
		$exprot_date = date( 'Y-m-d' );

		return [
			'name'    => "jupiterx-popup-{$popup_id}-{$exprot_date}.json",
			'content' => wp_json_encode( $export_data ),
		];
	}

	/**
	 * Change popup builder template.
	 *
	 * @param string $template content.
	 * @since 3.7.0
	 * @return string
	 */
	public function popup_template( $template ) {
		if ( is_singular( self::POST_TYPE ) ) {
			$elementor = Plugin::$instance;

			if ( $elementor->preview->is_preview_mode() ) {
				$template = $this->get_popup_template( 'editor.php' );

				return $template;
			}

			$template = $this->get_popup_template( 'preview.php' );

			return $template;
		}

		return $template;
	}

	/**
	 * Render popup in frontend.
	 *
	 * @since 3.7.0
	 * @return void
	 */
	public function popup_render() {
		if ( $this->is_maintenance_mode_enabled() ) {
			return;
		}

		$elementor = Plugin::$instance;

		$layout_builder_preview = isset( $_REQUEST['jupiterx-layout-builder-preview'] ) ? htmlspecialchars( $_REQUEST['jupiterx-layout-builder-preview'] ) : '';  // phpcs:ignore

		if (
			is_admin() ||
			$elementor->preview->is_preview_mode() ||
			'true' === $layout_builder_preview
		) {
			return;
		}

		$conditions = $this->get_popups_by_condition();

		if ( empty( $conditions ) ) {
			return;
		};

		foreach ( $conditions as $key => $value ) {
			if ( in_array( $key, self::$loaded_popups, true ) ) {
				continue;
			}

			self::$loaded_popups[] = $key;

			$frontend = $this->get_popup_template( 'frontend.php', $key );
		}

		if ( ! $frontend ) {
			return;
		}
	}

	/**
	 * Check if maintenance mode is enabled in elementor settings or jupiterx theme customizer.
	 *
	 * @since 4.4.0
	 * @return bool
	 */
	public function is_maintenance_mode_enabled() {
		if ( get_theme_mod( 'jupiterx_maintenance', false ) && ! is_user_logged_in() && ! jupiterx_check_default() ) {
			return true;
		}

		$maintenance_mode = Elementor\Plugin::$instance->maintenance_mode->get( 'mode' );
		$exclude_mode     = Elementor\Plugin::$instance->maintenance_mode->get( 'exclude_mode' );
		$exclude_roles    = Elementor\Plugin::$instance->maintenance_mode->get( 'exclude_roles' );
		$template_id      = Elementor\Plugin::$instance->maintenance_mode->get( 'template_id' );

		if ( ! empty( $maintenance_mode ) && ! empty( $template_id ) ) {
			if ( 'logged_in' === $exclude_mode && is_user_logged_in() ) {
				return false;
			}

			$current_user = wp_get_current_user();
			$user_roles   = $current_user->roles;

			foreach ( $user_roles as $role ) {
				if ( in_array( $role, $exclude_roles, true ) ) {
					return false;
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * Get popups base on the condition.
	 *
	 * @todo invetigate about alternative for get_posts.
	 * @since 3.7.0
	 * @return array
	 */
	private function get_popups_by_condition() {
		if ( ! empty( self::$valid_popups ) ) {
			return self::$valid_popups;
		}

		$popups = get_transient( 'jupiterx_popups_list' );

		if ( false === $popups ) {
			$args = [
				'post_type'   => self::POST_TYPE,
				'numberposts' => -1,
				'post_status' => 'publish',
			];

			$popups = get_posts( $args );

			set_transient( 'jupiterx_popups_list', $popups );
		}

		if ( empty( $popups ) ) {
			return;
		}

		$conditions_manager = new JupiterX_Popups_Conditions_Manager();
		self::$valid_popups = $conditions_manager->handle_conditions( $popups );

		return self::$valid_popups;
	}

	/**
	 * Delete transients on meta update.
	 *
	 * @param string  $meta_id Meta Id.
	 * @param integer $post_id Post Id.
	 * @since 4.2.0
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function delete_popup_transients( $meta_id, $post_id ) {
		$this->delete_popup_transient( $post_id );
	}

	/**
	 * Delete transients.
	 *
	 * @param integer $post_id Post Id.
	 * @since 4.2.0
	 */
	public function delete_popup_transient( $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( 'jupiterx-popups' !== $post_type ) {
			return;
		}

		delete_transient( 'jupiterx_popups_list' );
	}

	/**
	 * Enqueue popups styles.
	 *
	 * @since 3.7.0
	 * @return void
	 */
	public function enqueue_styles() {
		$suffix = ElementorUtils::is_script_debug() ? '' : '.min';
		$rtl    = is_rtl() ? '-rtl' : '';

		wp_enqueue_style(
			'jupiterx-popups-animation',
			jupiterx_core()->plugin_url() . 'includes/extensions/raven/assets/lib/animate/animate' . $suffix . '.css',
			[],
			JUPITERX_VERSION
		);

		if ( is_singular( self::POST_TYPE ) ) {
			wp_enqueue_style(
				'jupiterx-popups-preview',
				jupiterx_core()->plugin_url() . 'includes/extensions/raven/assets/css/popup-preview' . $rtl . $suffix . '.css',
				[],
				JUPITERX_VERSION
			);

			return;
		}

		wp_register_style(
			'jupiterx-popups-frontend',
			jupiterx_core()->plugin_url() . 'includes/extensions/raven/assets/css/popup-frontend' . $rtl . $suffix . '.css',
			[],
			JUPITERX_VERSION
		);

		if ( empty( $this->get_popups_by_condition() ) ) {
			return;
		}

		wp_enqueue_style( 'jupiterx-popups-frontend' );
	}

	/**
	 * Get popup template.
	 *
	 * @since 3.7.0
	 * @return void
	 */
	public function get_popup_template( $name, $key = null ) {
		$path = jupiterx_core()->plugin_dir() . 'includes/popups/popup-templates/';

		jupiterx_core()->load_files( [
			'popups/popup-templates/base',
		] );

		$file_path = $path . $name;

		if ( ! file_exists( $file_path ) ) {
			return;
		}

		$file_name  = str_replace( '.php', '', basename( $file_path ) );
		$class_name = str_replace( ' ', '_', ucwords( $file_name ) );
		$class_name = "JupiterX_Core\Popup\Templates\\{$class_name}";

		jupiterx_core()->load_files( [
			"popups/popup-templates/{$file_name}",
		] );

		if ( ! class_exists( $class_name ) ) {
			return;
		}

		$template = new $class_name( $key );

		echo $template->get_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	/**
	 * Get popup content.
	 *
	 * @since 3.7.0
	 */
	public function jupiterx_popup_get_content() {
		// phpcs:disable
		$data = ( ! empty( filter_var_array( $_POST['data'] ) ) ) ? filter_var_array( $_POST['data'] ) : false;
		// phpcs:enable

		if ( ! $data ) {
			wp_send_json_error( [
				'type' => esc_html__( 'error', 'jupiterx-core' ),
				'message' => esc_html__( 'Server error. Please, try again later', 'jupiterx-core' ),
			] );
		}

		$popup_data = apply_filters( 'jupiterx-popup/ajax-request/post-data', $data );
		$content    = apply_filters( 'jupiterx-popup/ajax-request/get-elementor-content', false, $popup_data );
		$popup_data = apply_filters( 'jupiterx-popup/ajax-request/after-content-define/post-data', $popup_data );

		if ( ! $content ) {
			$content = $this->get_popup_content( $popup_data );
		}

		if ( empty( $content ) ) {
			wp_send_json( [
				'type'    => esc_html__( 'error', 'jupiterx-core' ),
				'message' => esc_html__( 'Server error. Please, try again later.', 'jupiterx-core' ),
			] );
		}

		$popup_data = ( ! empty( $popup_data ) ) ? $popup_data : false;

		wp_send_json(
			[
				'type'    => 'success',
				'content' => $content,
				'data'    => $popup_data,
			]
		);
	}

	/**
	 * Get popup content from elementor.
	 *
	 * @param array $popup_data Array containing the popup_id and forceload value.
	 *
	 * @since 3.7.0
	 */
	public function get_popup_content( $popup_data ) {
		$popup_id = $popup_data['popup_id'];

		if ( empty( $popup_id ) ) {
			return false;
		}

		$plugin = Elementor\Plugin::instance();

		$content = $plugin->frontend->get_builder_content( $popup_id );

		return $content;
	}
}

new JupiterX_Popups();
