<?php
defined( 'ABSPATH' ) || die();

/**
 * Syncs Elementor settings (performance, CPT, maintenance, replace URL) with Jupiter X Control Panel.
 *
 * @package JupiterX_Core\Control_Panel_2
 * @since 4.15.0
 */
class JupiterX_Core_Control_Panel_Elementor_Settings_Bridge {

	/**
	 * Class instance.
	 *
	 * @var JupiterX_Core_Control_Panel_Elementor_Settings_Bridge|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return JupiterX_Core_Control_Panel_Elementor_Settings_Bridge
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'wp_ajax_jupiterx_elementor_cp', [ $this, 'handle_ajax' ] );
	}

	/**
	 * AJAX entry.
	 *
	 * @return void
	 */
	public function handle_ajax() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'jupiterx-core' ) ] );
		}

		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			wp_send_json_error( [ 'message' => __( 'Elementor is not active.', 'jupiterx-core' ) ] );
		}

		$sub = isset( $_POST['sub_action'] ) ? sanitize_text_field( wp_unslash( $_POST['sub_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		switch ( $sub ) {
			case 'get_performance':
				$this->send_performance();
				break;
			case 'save_performance':
				$this->save_performance();
				break;
			case 'get_cpt':
				$this->send_cpt();
				break;
			case 'save_cpt':
				$this->save_cpt();
				break;
			case 'get_maintenance':
				$this->send_maintenance();
				break;
			case 'save_maintenance':
				$this->save_maintenance();
				break;
			case 'create_maintenance_template':
				$this->create_maintenance_template();
				break;
			case 'replace_url':
				$this->replace_url();
				break;
			default:
				wp_send_json_error( [ 'message' => __( 'Invalid action.', 'jupiterx-core' ) ] );
		}
	}

	/**
	 * Performance option keys (Elementor).
	 *
	 * @return array
	 */
	private function performance_keys() {
		return [
			'elementor_css_print_method'           => [ 'default' => 'external' ],
			'elementor_optimized_image_loading'   => [ 'default' => '1' ],
			'elementor_optimized_gutenberg_loading' => [ 'default' => '1' ],
			'elementor_lazy_load_background_images' => [ 'default' => '1' ],
			'elementor_local_google_fonts'        => [ 'default' => '0' ],
		];
	}

	/**
	 * Send performance settings.
	 *
	 * @return void
	 */
	private function send_performance() {
		$out = [];
		foreach ( $this->performance_keys() as $key => $meta ) {
			$out[ $key ] = (string) get_option( $key, $meta['default'] );
		}

		wp_send_json_success( [ 'settings' => $out ] );
	}

	/**
	 * Save performance settings.
	 *
	 * @return void
	 */
	private function save_performance() {
		$raw = isset( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( '' === $raw ) {
			wp_send_json_error( [ 'message' => __( 'No data.', 'jupiterx-core' ) ] );
		}

		$payload = json_decode( $raw, true );

		if ( ! is_array( $payload ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid data.', 'jupiterx-core' ) ] );
		}

		$allowed = $this->performance_keys();

		foreach ( $allowed as $key => $meta ) {
			if ( ! isset( $payload[ $key ] ) ) {
				continue;
			}

			$val = $payload[ $key ];

			switch ( $key ) {
				case 'elementor_css_print_method':
					$val = in_array( $val, [ 'external', 'internal' ], true ) ? $val : 'external';
					break;
				// 0|1 strings.
				default:
					$val = in_array( (string) $val, [ '0', '1' ], true ) ? $val : $meta['default'];
					break;
			}

			update_option( $key, $val );
		}

		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		wp_send_json_success( [ 'message' => __( 'Settings saved.', 'jupiterx-core' ) ] );
	}

	/**
	 * Post types list for Elementor CPT support (same filter as Elementor settings UI).
	 *
	 * @return array
	 */
	private function get_post_type_choices() {
		$post_types_objects = get_post_types(
			[
				'public' => true,
			],
			'objects'
		);

		/**
		 * Filters the list of post type objects used by Elementor.
		 *
		 * @since 2.8.0
		 */
		$post_types_objects = apply_filters( 'elementor/settings/controls/checkbox_list_cpt/post_type_objects', $post_types_objects );

		$exclude = [ 'attachment', 'elementor_library' ];
		$choices = [];

		foreach ( $post_types_objects as $cpt_slug => $post_type ) {
			if ( in_array( $cpt_slug, $exclude, true ) ) {
				continue;
			}

			$choices[] = [
				'slug'  => $cpt_slug,
				'label' => $post_type->labels->name,
			];
		}

		return $choices;
	}

	/**
	 * Elementor general setting keys stored as WordPress options.
	 *
	 * @return array
	 */
	private function general_setting_keys() {
		return [
			'elementor_disable_color_schemes'      => [
				'default' => '',
				'values'  => [ '', 'yes' ],
			],
			'elementor_disable_typography_schemes' => [
				'default' => '',
				'values'  => [ '', 'yes' ],
			],
			'elementor_editor_break_lines'         => [
				'default' => '',
				'values'  => [ '', '1' ],
			],
			'elementor_unfiltered_files_upload'    => [
				'default' => '',
				'values'  => [ '', '1' ],
			],
			'elementor_google_font'                => [
				'default' => '1',
				'values'  => [ '0', '1' ],
			],
			'elementor_font_display'               => [
				'default' => 'auto',
				'values'  => [ 'auto', 'block', 'swap', 'fallback', 'optional' ],
			],
			'elementor_load_fa4_shim'              => [
				'default' => '',
				'values'  => [ '', 'yes' ],
			],
			'elementor_meta_generator_tag'         => [
				'default' => '',
				'values'  => [ '', '1' ],
			],
		];
	}

	/**
	 * Send CPT support.
	 *
	 * @return void
	 */
	private function send_cpt() {
		$default = [ 'page', 'post' ];
		$current = get_option( 'elementor_cpt_support', $default );

		if ( ! is_array( $current ) ) {
			$current = $default;
		}

		wp_send_json_success(
			[
				'cpt_support'      => array_values( $current ),
				'post_types'       => $this->get_post_type_choices(),
				'general_settings' => $this->get_general_settings(),
			]
		);
	}

	/**
	 * Read Elementor general settings shared with Elementor settings page.
	 *
	 * @return array
	 */
	private function get_general_settings() {
		$out = [];

		foreach ( $this->general_setting_keys() as $key => $meta ) {
			$out[ $key ] = (string) get_option( $key, $meta['default'] );
		}

		return $out;
	}

	/**
	 * Persist Elementor CPT support (AJAX and Control Panel Site Settings save).
	 *
	 * @param array $payload List of post type slugs.
	 * @return bool True when saved.
	 */
	public function persist_elementor_cpt_support( $payload ) {
		if ( ! is_array( $payload ) ) {
			return false;
		}

		$allowed_slugs = wp_list_pluck( $this->get_post_type_choices(), 'slug' );
		$clean         = [];

		foreach ( $payload as $slug ) {
			$slug = sanitize_key( $slug );
			if ( in_array( $slug, $allowed_slugs, true ) ) {
				$clean[] = $slug;
			}
		}

		$clean = array_values( array_unique( $clean ) );

		if ( class_exists( '\Elementor\Settings_Validations' ) ) {
			$clean = \Elementor\Settings_Validations::checkbox_list( $clean );
		}

		update_option( 'elementor_cpt_support', $clean );

		return true;
	}

	/**
	 * Persist Elementor general settings shared with Elementor settings page.
	 *
	 * @param array $payload Setting values keyed by Elementor option name.
	 * @return bool True when at least one supported setting is saved.
	 */
	public function persist_elementor_general_settings( $payload ) {
		if ( ! is_array( $payload ) ) {
			return false;
		}

		$saved = false;

		foreach ( $this->general_setting_keys() as $key => $meta ) {
			if ( ! array_key_exists( $key, $payload ) ) {
				continue;
			}

			$value = (string) $payload[ $key ];
			$value = in_array( $value, $meta['values'], true ) ? $value : $meta['default'];

			update_option( $key, $value );
			$saved = true;
		}

		return $saved;
	}

	/**
	 * Save CPT support.
	 *
	 * @return void
	 */
	private function save_cpt() {
		$raw = isset( $_POST['cpt_support'] ) ? wp_unslash( $_POST['cpt_support'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( '' === $raw ) {
			wp_send_json_error( [ 'message' => __( 'No data.', 'jupiterx-core' ) ] );
		}

		$payload = json_decode( $raw, true );

		if ( ! is_array( $payload ) || ! $this->persist_elementor_cpt_support( $payload ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid data.', 'jupiterx-core' ) ] );
		}

		wp_send_json_success( [ 'message' => __( 'Settings saved.', 'jupiterx-core' ) ] );
	}

	/**
	 * Page templates for maintenance mode (Elementor library).
	 *
	 * @return array
	 */
	private function get_maintenance_template_options() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return [];
		}

		$templates = \Elementor\Plugin::$instance->templates_manager->get_source( 'local' )->get_items(
			[
				'type' => 'page',
			]
		);

		$options = [];
		foreach ( $templates as $template ) {
			$options[] = [
				'value' => (string) $template['template_id'],
				'label' => $template['title'],
			];
		}

		return $options;
	}

	/**
	 * Send maintenance mode settings.
	 *
	 * @return void
	 */
	private function send_maintenance() {
		$mode = (string) get_option( 'elementor_maintenance_mode_mode', '' );

		$exclude_mode = (string) get_option( 'elementor_maintenance_mode_exclude_mode', 'logged_in' );
		$exclude_roles = get_option( 'elementor_maintenance_mode_exclude_roles', [] );

		if ( ! is_array( $exclude_roles ) ) {
			$exclude_roles = [];
		}

		$template_id = (string) get_option( 'elementor_maintenance_mode_template_id', '' );

		$roles_out = [];
		foreach ( get_editable_roles() as $slug => $data ) {
			$roles_out[] = [
				'slug'  => $slug,
				'name'  => translate_user_role( $data['name'] ),
				'checked' => in_array( $slug, $exclude_roles, true ),
			];
		}

		wp_send_json_success(
			[
				'mode'                  => $mode,
				'exclude_mode'          => $exclude_mode,
				'exclude_roles'         => $roles_out,
				'template_id'           => $template_id,
				'template_options'      => $this->get_maintenance_template_options(),
			]
		);
	}

	/**
	 * Save maintenance mode settings.
	 *
	 * @return void
	 */
	private function save_maintenance() {
		$raw = isset( $_POST['maintenance'] ) ? wp_unslash( $_POST['maintenance'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( '' === $raw ) {
			wp_send_json_error( [ 'message' => __( 'No data.', 'jupiterx-core' ) ] );
		}

		$payload = json_decode( $raw, true );

		if ( ! is_array( $payload ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid data.', 'jupiterx-core' ) ] );
		}

		$mode = isset( $payload['mode'] ) ? sanitize_text_field( $payload['mode'] ) : '';

		if ( ! in_array( $mode, [ '', 'maintenance', 'coming_soon' ], true ) ) {
			$mode = '';
		}

		update_option( 'elementor_maintenance_mode_mode', $mode );

		$exclude_mode = isset( $payload['exclude_mode'] ) ? sanitize_text_field( $payload['exclude_mode'] ) : 'logged_in';

		if ( ! in_array( $exclude_mode, [ 'logged_in', 'custom' ], true ) ) {
			$exclude_mode = 'logged_in';
		}

		update_option( 'elementor_maintenance_mode_exclude_mode', $exclude_mode );

		$roles_in = isset( $payload['exclude_roles'] ) && is_array( $payload['exclude_roles'] ) ? $payload['exclude_roles'] : [];
		$editable = array_keys( get_editable_roles() );
		$roles_clean = [];

		foreach ( $roles_in as $slug ) {
			$slug = sanitize_key( $slug );
			if ( in_array( $slug, $editable, true ) ) {
				$roles_clean[] = $slug;
			}
		}

		$roles_clean = array_values( array_unique( $roles_clean ) );

		if ( class_exists( '\Elementor\Settings_Validations' ) ) {
			$roles_clean = \Elementor\Settings_Validations::checkbox_list( $roles_clean );
		}

		update_option( 'elementor_maintenance_mode_exclude_roles', $roles_clean );

		$template_id = isset( $payload['template_id'] ) ? absint( $payload['template_id'] ) : 0;
		update_option( 'elementor_maintenance_mode_template_id', $template_id );

		wp_send_json_success( [ 'message' => __( 'Settings saved.', 'jupiterx-core' ) ] );
	}

	/**
	 * Create a new Elementor library page template and return its editor URL (for maintenance mode).
	 *
	 * @return void
	 */
	private function create_maintenance_template() {
		$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( '' === trim( $title ) ) {
			wp_send_json_error( [ 'message' => __( 'Please enter a template name.', 'jupiterx-core' ) ] );
		}

		$source = \Elementor\Plugin::$instance->templates_manager->get_source( 'local' );

		$template_id = $source->save_item(
			[
				'title'         => $title,
				'type'          => 'page',
				'content'       => [],
				'page_settings' => [],
			]
		);

		if ( is_wp_error( $template_id ) ) {
			wp_send_json_error( [ 'message' => $template_id->get_error_message() ] );
		}

		$document = \Elementor\Plugin::$instance->documents->get( $template_id );

		if ( ! $document ) {
			wp_send_json_error( [ 'message' => __( 'Could not load the new template.', 'jupiterx-core' ) ] );
		}

		wp_send_json_success(
			[
				'template_id' => (string) $template_id,
				'edit_url'    => $document->get_edit_url(),
			]
		);
	}

	/**
	 * Replace URLs (Elementor tools).
	 *
	 * @return void
	 */
	private function replace_url() {
		$from = isset( $_POST['from'] ) ? trim( wp_unslash( $_POST['from'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$to   = isset( $_POST['to'] ) ? trim( wp_unslash( $_POST['to'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		try {
			$message = \Elementor\Utils::replace_urls( $from, $to );
			wp_send_json_success( [ 'message' => $message ] );
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'message' => $e->getMessage() ] );
		}
	}
}
