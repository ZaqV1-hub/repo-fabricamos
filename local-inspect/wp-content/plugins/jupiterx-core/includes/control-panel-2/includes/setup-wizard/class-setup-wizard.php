<?php
use Elementor\Plugin as Elementor;
use Elementor\Utils;

/**
 * Setup wizard class.
 *
 * @package JupiterX_Core\Control_Panel_2
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class JupiterX_Core_Setup_Wizard {
	/**
	 * Templates API URL.
	 *
	 * Holds the URL of the Templates API.
	 *
	 * @access private
	 * @static
	 * @var string API URL.
	 */
	private static $templates_url = 'https://blocks.jupiterx.com/wp-json/jupiterx/v1/templates-by-type';

	/**
	 * Template API URL.
	 *
	 * Holds the URL of the Template API.
	 *
	 * @access private
	 * @static
	 * @var string API URL.
	 */
	private static $template_url = 'https://blocks.jupiterx.com/wp-json/jupiterx/v1/template-id/';

	/**
	 * Jupiterx layout builder templates option key
	 *
	 * @access private
	 * @static
	 * @var string
	 */
	private static $template_with_conditions = 'jupiterx-posts-with-conditions';

	/**
	 * List of the static templates.
	 * Page will be created for these templates.
	 *
	 * @access private
	 * @static
	 * @var array
	 */
	private $static_templates = [
		'homepage',
		'about',
		'services',
		'benefits',
		'testimonials',
		'contact',
		'portfolio',
	];

	/**
	 * Class instance.
	 *
	 * @since 4.0.0
	 * @var JupiterX_Core_Setup_Wizard Class instance.
	 */
	private static $instance = null;

	/**
	 * Steps data.
	 *
	 * @since 4.0.0
	 * @var array
	 */
	private $steps_data = [];

	/**
	 * Step data.
	 *
	 * @since 4.0.0
	 * @var array
	 */
	private $step_data = [];

	/**
	 * Current sub steps.
	 *
	 * @since 4.0.0
	 * @var array
	 */
	private $sub_steps = [];

	/**
	 * Saved system colors.
	 *
	 * @since 4.0.0
	 * @var array
	 */
	private $system_color = [];


	/**
	 * Condition generator object
	 *
	 * @since 4.0.0
	 * @var JupiterX_Core_Condition_Generator
	 */
	private $condition_generator;

	/**
	 * Get a class instance.
	 *
	 * @since 4.0.0
	 *
	 * @return JupiterX_Core_Setup_Wizard Class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		$this->condition_generator = JupiterX_Core_Condition_Generator::get_instance();

		add_action( 'wp_ajax_jupiterx_skip_setup_wizard', [ $this, 'skip_setup_wizard' ] );
		add_action( 'wp_ajax_jupiterx_setup_wizard_get_templates', [ $this, 'get_content_step_templates' ] );
		add_action( 'wp_ajax_jupiterx_setup_start_process', [ $this, 'start_process' ] );
		add_action( 'wp_ajax_jupiterx_setup_install_plugins', [ $this, 'install_plugins' ] );
		add_action( 'wp_ajax_jupiterx_setup_wizard_get_active_kit', [ $this, 'get_active_kit' ] );
	}

	/**
	 * Skip setup wizard process.
	 *
	 * @since 4.0.0
	 */
	public function skip_setup_wizard() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
		}

		update_option( 'jupiterx_setup_wizard_skipped', true );

		wp_send_json_success();
	}

	/**
	 * Get templates from server side for preview and import purposes.
	 *
	 * @since 4.0.0
	 */
	public function get_content_step_templates() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( empty( get_transient( 'jupiterx_setup_wizard_template' ) ) ) {
			$templates = $this->get_templates_from_server();

			if ( is_wp_error( $templates ) ) {
				wp_send_json_error( esc_html__( 'Server Error', 'jupiterx-core' ) );
			}

			$final_templates = [];

			foreach ( $templates as $key => $value ) {
				$key = preg_replace( '/\s+/', '_', $key );

				$final_templates[ $key ] = $value;
			}

			set_transient( 'jupiterx_setup_wizard_template', $final_templates, 12 * HOUR_IN_SECONDS );
		}

		$templates = get_transient( 'jupiterx_setup_wizard_template' );

		if ( empty( $templates ) ) {
			$response_message = esc_html__( 'There\'s a problem in fetching the templates.', 'jupiterx-core' );
			$decoded_message  = htmlspecialchars_decode( $response_message, ENT_QUOTES );

			wp_send_json_error( $decoded_message );
		}

		wp_send_json_success( $templates );
	}

	/**
	 * Get Elementor active kit.
	 *
	 * @since 4.0.0
	 */
	public function get_active_kit() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		$active_key_id = Elementor::$instance->kits_manager->get_active_kit_for_frontend()->get_main_id();

		if ( empty( $active_key_id ) ) {
			wp_send_json_error( esc_html__( 'There\'s a problem in fetching the Elementor kit.', 'jupiterx-core' ) );
		}

		wp_send_json_success( $active_key_id );
	}

	public function get_templates_from_server() {
		$url = sprintf( self::$templates_url, '' );

		$response = wp_remote_get( $url, [
			'timeout' => 40,
		] );

		jupiterx_log(
			"[Jupiter X > Setup Wizard] To get templates, the following data is the received response from '{$url}' API.",
			$response
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( 200 !== $response_code ) {
			return new \WP_Error( 'response_code_error', sprintf( 'The request returned with a status code of %s.', $response_code ) );
		}

		$template_content = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $template_content['error'] ) ) {
			return new \WP_Error( 'response_error', $template_content['error'] );
		}

		if ( empty( $template_content ) ) {
			return new \WP_Error( 'template_data_error', 'An invalid data was returned.' );
		}

		return $template_content;
	}

	/**
	 * Start setup wizard import and install proccess.
	 *
	 * @since 4.0.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function start_process() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [
				'error' => esc_html__( 'You do not have access to this section.', 'jupiterx-core' ),
				'step' => 'colors',
			] );
		}

		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		$fields = filter_input( INPUT_POST, 'fields', FILTER_DEFAULT, FILTER_FORCE_ARRAY );
		$step   = filter_input( INPUT_POST, 'step', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$steps  = filter_input( INPUT_POST, 'steps', FILTER_DEFAULT, FILTER_FORCE_ARRAY );

		if ( empty( $fields ) || empty( $step ) ) {
			wp_send_json_error( esc_html__( 'There is an issue while doing the process please check the selected settings,', 'jupiterx-core' ) );
		}

		if ( ! $this->check_required_plugins( $fields ) ) {
			wp_send_json_error( esc_html__( 'the required plugins are not installed.', 'jupiterx-core' ) );
		}

		$this->steps_data = $fields;
		$this->step_data  = [];
		$this->sub_steps  = [];

		$function_name = "import_{$step}";

		$result = false;

		if ( empty( $fields[ $step ] ) ) {
			if ( 'templates' === $step ) {
				update_option( 'jupiterx_setup_wizard_done', true );
				wp_send_json_success( [ 'state' => 'done' ] );
			}

			wp_send_json_success( [ 'state' => 'process' ] );
		}

		if ( method_exists( $this, $function_name ) && ! empty( $fields[ $step ] ) && array_key_exists( $step, $steps ) ) {
			$this->step_data = $fields[ $step ];
			$this->sub_steps = $steps[ $step ];

			$result = $this->$function_name();
		}

		if ( 'done' === $result ) {
			update_option( 'jupiterx_setup_wizard_done', true );
			wp_send_json_success( [ 'state' => 'done' ] );
		}

		if ( empty( $result ) ) {
			wp_send_json_error( [
				'error' => esc_html__( 'There is an issue while doing the process please check the selected settings, and try again.', 'jupiterx-core' ),
				'step' => esc_html__( 'Something went wrong while importing', 'jupiterx-core' ) . ' ' . $step,
			] );
		}

		wp_send_json_success( [ 'state' => 'process' ] );
	}

	/**
	 * Installing required plugin.
	 *
	 * @since 4.0.0
	 */
	public function install_plugins() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You do not have access to this section.', 'jupiterx-core' ) );
		}

		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		$fields = filter_input( INPUT_POST, 'fields', FILTER_DEFAULT, FILTER_FORCE_ARRAY );

		$plugins = [
			'elementor' => [
				'elementor/elementor.php',
				'https://downloads.wordpress.org/plugin/elementor.latest-stable.zip',
			],
			'woocommerce' => [
				'woocommerce/woocommerce.php',
				'https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip',
			],
			'sellkit' => [
				'sellkit/sellkit.php',
				'https://downloads.wordpress.org/plugin/sellkit.latest-stable.zip',
			],
			'sellkit-pro' => [
				'sellkit-pro/sellkit-pro.php',
				get_transient( 'jupiterx_sellkit_pro_link' ),
			],
		];

		if ( 'standard' === $fields['install_plugins'] ) {
			unset( $plugins['woocommerce'] );
			unset( $plugins['sellkit'] );
			unset( $plugins['sellkit-pro'] );
		}

		foreach ( $plugins as $plugin ) {
			$install = null;

			if ( ! $this->check_is_installed( $plugin[0] ) ) {
				$install = $this->install_plugin( $plugin[1] );
			}

			if ( ! is_wp_error( $install ) && $install ) {
				activate_plugin( $plugin[0] );
			}

			if ( $this->check_is_installed( $plugin[0] ) && ! is_plugin_active( $plugin[0] ) ) {
				activate_plugin( $plugin[0] );
			}
		}

		wp_send_json_success();
	}

	/**
	 * Check plugin is installed.
	 *
	 * @param string $base Plugin base path.
	 * @since 4.0.0
	 */
	private function check_is_installed( $base ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();

		if ( ! empty( $all_plugins[ $base ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Install plugin.
	 *
	 * @param string $plugin_zip Download link of the plugin.
	 * @since 4.0.0
	 */
	private function install_plugin( $plugin_zip ) {
		if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$upgrader  = new Plugin_Upgrader();
		$installed = $upgrader->install( $plugin_zip );

		return $installed;
	}

	/**
	 * Check required plugins are installed.
	 *
	 * @param array $fields Array of logo data.
	 * @since 4.0.0
	 */
	private function check_required_plugins( $fields ) {
		if ( 'standard' === $fields['install_plugins'] && class_exists( 'Elementor\Plugin' ) ) {
			return true;
		}

		if (
			'woo' === $fields['install_plugins'] &&
			class_exists( 'Elementor\Plugin' ) &&
			function_exists( 'WC' )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Import Colors.
	 *
	 * @since 4.0.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function import_colors() {
		$this->create_elementor_kit();

		$values = [];

		if ( ! empty( $this->step_data['primary_color'] ) ) {
			array_push( $values, [
				'_id' => 'primary',
				'title' => esc_html__( 'Primary', 'jupiterx-core' ),
				'color' => $this->step_data['primary_color'],
			] );

			$this->system_color['primary_color'] = $this->step_data['primary_color'];
		}

		if ( ! empty( $this->step_data['secondary_color'] ) ) {
			array_push( $values, [
				'_id' => 'secondary',
				'title' => esc_html__( 'Secondary', 'jupiterx-core' ),
				'color' => $this->step_data['secondary_color'],
			] );

			$this->system_color['secondary'] = $this->step_data['secondary_color'];
		}

		if ( ! empty( $this->step_data['body_text_color'] ) ) {
			array_push( $values, [
				'_id' => 'text',
				'title' => esc_html__( 'Text', 'jupiterx-core' ),
				'color' => $this->step_data['body_text_color'],
			] );

			$this->system_color['text'] = $this->step_data['body_text_color'];
		}

		if ( ! empty( $this->step_data['accent_color'] ) ) {
			array_push( $values, [
				'_id' => 'accent',
				'title' => esc_html__( 'Accent', 'jupiterx-core' ),
				'color' => $this->step_data['accent_color'],
			] );

			$this->system_color['accent'] = $this->step_data['accent_color'];
		}

		$colors_to_save = [];

		if ( ! empty( $this->step_data['custom1_color'] ) ) {
			$colors_to_save[] = $this->step_data['custom1_color'];
		}

		if ( ! empty( $this->step_data['custom2_color'] ) ) {
			$colors_to_save[] = $this->step_data['custom2_color'];
		}

		$kit_document  = Elementor::$instance->kits_manager->get_active_kit_for_frontend();
		$custom_colors = $kit_document->get_settings()['custom_colors'];

		$this->import_custom_colors( $colors_to_save, $custom_colors, $kit_document );

		$update = $this->update_elementor_kit( 'system_colors', $values );

		if ( $update ) {
			return true;
		}

		return false;
	}

	/**
	 * Import button colors.
	 *
	 * @param array  $settings Elementor kit settings.
	 * @return array
	 * @since 4.0.0
	 */
	public function import_button_colors( $settings ) {
		$settings['button_text_color']       = '#fff';
		$settings['button_hover_text_color'] = '#fff';

		return $settings;
	}

	/**
	 * Import custom colors.
	 *
	 * @param array  $colors_to_save Colors form setup wizard.
	 * @param array  $custom_colors  Custom color in elementor kit.
	 * @param object $kit_document   Object of elementor kit.
	 * @since 4.0.0
	 */
	public function import_custom_colors( $colors_to_save, $custom_colors, $kit_document ) {
		if ( empty( $custom_colors ) ) {
			foreach ( $colors_to_save as $index => $color ) {
				$kit_document->add_repeater_row( 'custom_colors', [
					'_id' => "setup_wizard_cutom_color_{$index}",
					'title' => esc_html__( 'Saved Color', 'jupiterx-core' ) . ' #' . ( $index + 1 ),
					'color' => $color,
				] );
			}

			return;
		}

		$added_list = [];

		foreach ( $colors_to_save as $index => $color ) {
			$added_list[] = "setup_wizard_cutom_color_{$index}";
		}

		foreach ( $custom_colors as $custom_color ) {
			if ( in_array( $custom_color['_id'], $added_list, true ) ) {
				continue;
			}

			$kit_document->add_repeater_row( 'custom_colors', [
				'_id' => "setup_wizard_cutom_color_{$index}",
				'title' => esc_html__( 'Saved Color', 'jupiterx-core' ) . ' #' . ( $index + 1 ),
				'color' => $color,
			] );
		}
	}

	/**
	 * Import Typography.
	 *
	 * @since 4.0.0
	 */
	public function import_typography() {
		$kit_document = Elementor::$instance->kits_manager->get_active_kit_for_frontend();

		if ( empty( $kit_document ) ) {
			wp_send_json_error( [
				'error' => esc_html__( 'Error while importing typography.', 'jupiterx-core' ),
				'step' => 'typography',
			] );

			return false;
		}

		$settings = $kit_document->get_settings();

		if ( ! empty( $this->step_data['body_font_family'] ) ) {
			$settings['body_typography_typography']        = 'custom';
			$settings['body_typography_font_family']       = $this->step_data['body_font_family'];
			$settings['body_typography_font_size']['size'] = $this->step_data['body_font_size'];
			$settings['body_typography_font_size']['unit'] = 'px';

			$settings['__globals__']['body_color'] = 'globals/colors?id=text';
		}

		$heading_font_family = $this->step_data['headings_font_family'];

		foreach ( $this->step_data['headings_font_size'] as $key => $value ) {
			$typography  = "{$key}_typography_typography";
			$font_family = "{$key}_typography_font_family";
			$font_size   = "{$key}_typography_font_size";

			$settings[ $typography ]        = 'custom';
			$settings[ $font_family ]       = $heading_font_family;
			$settings[ $font_size ]['size'] = $value;
			$settings[ $font_size ]['unit'] = 'px';

			if ( 'Inter' === $heading_font_family ) {
				$settings[ "{$key}_typography_font_weight" ] = 700;
			}

			$settings['__globals__'][ $key . '_color' ] = 'globals/colors?id=primary';
		}

		foreach ( $settings['system_typography'] as $key => $system_typography ) {
			if ( 'primary' === $system_typography['_id'] ) {
				$settings['system_typography'][ $key ]['typography_typography']  = 'custom';
				$settings['system_typography'][ $key ]['typography_font_family'] = $heading_font_family;
			}

			if ( 'secondary' === $system_typography['_id'] ) {
				$settings['system_typography'][ $key ]['typography_typography']  = 'custom';
				$settings['system_typography'][ $key ]['typography_font_family'] = $heading_font_family;
			}

			if ( 'text' === $system_typography['_id'] ) {
				$settings['system_typography'][ $key ]['typography_typography']  = 'custom';
				$settings['system_typography'][ $key ]['typography_font_family'] = $this->step_data['body_font_family'];
			}

			if ( 'accent' === $system_typography['_id'] ) {
				$settings['system_typography'][ $key ]['typography_typography']  = 'custom';
				$settings['system_typography'][ $key ]['typography_font_family'] = $heading_font_family;
			}
		}

		$kit_document->save( [ 'settings' => $settings ] );

		return true;
	}

	/**
	 * Import Layout.
	 *
	 * @since 4.0.0
	 */
	public function import_layout() {
		foreach ( $this->sub_steps as $step ) {
			$function_name = "import_sub_step_{$step}";
			$result        = false;

			if ( ! empty( $this->steps_data[ $step ] ) && method_exists( $this, $function_name ) ) {
				$result = $this->$function_name( $this->steps_data[ $step ] );
			}

			if ( ! $result ) {
				wp_send_json_error( [
					'error' => esc_html__( 'Error while importing layout.', 'jupiterx-core' ),
					'step' => 'layout',
				] );

				return $result;
			}
		}

		return true;
	}

	/**
	 * Import layout.
	 *
	 * @param array $fields Array of layout data.
	 * @since 4.0.0
	 */
	public function import_sub_step_layout( $fields ) {
		foreach ( $fields as $key => $field ) {
			$result = false;

			$result = $this->import_template_by_id( $key, $field );

			if ( ! $result ) {
				wp_send_json_error( [
					'error' => esc_html__( 'Error while importing layout.', 'jupiterx-core' ),
					'step' => 'layout',
				] );

				return $result;
			}
		}

		return true;
	}

	/**
	 * Import logo.
	 *
	 * @param array $fields Array of logo data.
	 * @since 4.0.0
	 */
	public function import_sub_step_logo( $fields ) {
		if ( empty( $fields['url'] ) ) {
			return true;
		}

		$values = [
			'url' => $fields['url'],
			'id' => $fields['id'],
			'source' => 'library',
		];

		$update = $this->update_elementor_kit( 'site_logo', $values );

		if ( $update ) {
			return true;
		}

		wp_send_json_error( [
			'error' => esc_html__( 'Error while importing logo.', 'jupiterx-core' ),
			'step' => 'layout',
		] );

		return false;
	}

	/**
	 * Import Content.
	 *
	 * @since 4.0.0
	 */
	public function import_templates() {
		$type = $this->steps_data['site_type'];

		set_time_limit( 0 );

		if ( 'true' === $this->steps_data['sample_content'] ) {
			$this->import_sample_content( $type );
		}

		foreach ( $this->step_data as $key => $template ) {
			$result = false;

			if ( in_array( $key, $this->static_templates, true ) ) {
				$result = $this->handle_static_templates( $key, $template );
			}

			if ( ! in_array( $key, $this->static_templates, true ) ) {
				$result = $this->handle_dynamic_templates( $key, $template );
			}

			if ( ! $result ) {
				wp_send_json_error( [
					'error' => esc_html__( 'Error while importing content.', 'jupiterx-core' ),
					'step' => 'templates',
				] );

				return $result;
			}
		}

		return 'done';
	}

	/**
	 * Import Sample Content.
	 *
	 * @param string  $site_type Website type.
	 * @since 4.0.0
	 */
	public function import_sample_content( $site_type ) {
		$post_types = [ 'post', 'portfolio' ];

		if ( 'woo' === $site_type ) {
			$post_types = [ 'post', 'portfolio', 'product' ];
		}

		$setup_wizard_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$image_path        = $setup_wizard_path . 'dummy-content/jupiterx-dummy-placeholder.jpg';
		$image_id          = $this->attach_image_to_media_library( $image_path );

		foreach ( $post_types as $post_type ) {
			$this->import_sample_content_xml_files( $post_type, $setup_wizard_path, $image_id );
		}
	}

	/**
	 * Import XML files.
	 *
	 * @param string  $post_type Post type.
	 * @param string  $setup_wizard_path Setup wizard path.
	 * @param string  $image_id Image ID.
	 * @since 4.0.0
	 */
	public function import_sample_content_xml_files( $post_type, $setup_wizard_path, $image_id ) {
		$xml_file = $setup_wizard_path . 'dummy-content/' . $post_type . '.xml';
		$xml_data = simplexml_load_file( $xml_file );

		if ( false !== $xml_data ) {
			foreach ( $xml_data->channel->item as $item ) {
				$title      = (string) $item->title;
				$content    = (string) $item->children( 'content', true )->encoded;
				$categories = [];
				$taxonomies = [ 'category', 'portfolio_category', 'product_cat' ];

				foreach ( $item->category as $category ) {
					if ( in_array( $category->attributes()['domain'], $taxonomies, true ) ) {
						$categories[] = (string) $category;
					}
				}

				$post_data = [
					'post_title'   => $title,
					'post_content' => $content,
					'post_status'  => 'publish',
					'post_type'    => $post_type,
				];

				$post_id = wp_insert_post( $post_data );

				// Set Featured Image if the image is already attached.
				if ( $image_id ) {
					set_post_thumbnail( $post_id, $image_id );
				}

				if ( ! $post_id ) {
					wp_send_json_error( [
						'error' => esc_html__( 'Error creating post.', 'jupiterx-core' ),
						'step'  => 'templates',
					] );
				}

				// Convert category names to IDs or create categories if they don't exist.
				$category_ids = [];
				$taxonomy     = ( 'post' === $post_type ) ? 'category' : $post_type . '_category';

				if ( 'product' === $post_type ) {
					$taxonomy = 'product_cat';
				}

				foreach ( $categories as $category ) {
					$category_data = get_term_by( 'name', $category, $taxonomy );

					if ( ! $category_data ) {
						$new_category = wp_insert_term( $category, $taxonomy );

						if ( ! is_wp_error( $new_category ) ) {
							$category_ids[] = $new_category['term_id'];

							wp_set_object_terms( $post_id, $new_category['term_id'], $taxonomy );
						} else {
							wp_send_json_error( [
								'error' => esc_html__( 'Error creating category.', 'jupiterx-core' ),
								'step' => 'templates',
							] );
						}

						continue;
					}

					$category_ids[] = $category_data->term_id;

					wp_set_object_terms( $post_id, $category_data->term_id, $taxonomy );
				}
			}

			return;
		}

		wp_send_json_error( [
			'error' => esc_html__( 'Error loading XML file.', 'jupiterx-core' ),
			'step'  => 'templates',
		] );
	}

	/**
	 * Attach an image to media library if not already attached.
	 *
	 * @param string  $image_path Image path.
	 * @return int
	 * @since 4.0.0
	 */
	public function attach_image_to_media_library( $image_path ) {
		$upload_dir      = wp_upload_dir();
		$image_dest_path = $upload_dir['path'] . '/' . basename( $image_path );

		$image_id = attachment_url_to_postid( $image_dest_path );

		if ( ! $image_id && file_exists( $image_path ) ) {
			copy( $image_path, $image_dest_path );

			$attachment = [
				'guid'           => $upload_dir['url'] . '/' . basename( $image_path ),
				'post_mime_type' => mime_content_type( $image_path ),
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $image_path ) ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			];

			$image_id    = wp_insert_attachment( $attachment, $image_dest_path );
			$attach_data = wp_generate_attachment_metadata( $image_id, $image_dest_path );

			wp_update_attachment_metadata( $image_id, $attach_data );
		}

		return $image_id;
	}

	/**
	 * Import static template by id.
	 *
	 * @param string  $key Template name.
	 * @param integer $id  Template ID.
	 * @since 4.0.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	private function handle_static_templates( $key, $id ) {
		$result = $this->get_template_data( $id );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		$post_id = $this->create_template_page( $key, ucwords( $key ), $result, 'page', 'wp-page' );

		if ( empty( $post_id ) ) {
			return false;
		}

		if ( 'homepage' === $key ) {
			update_option( 'page_on_front', $post_id );
			update_option( 'show_on_front', 'page' );
		}

		return true;
	}

	/**
	 * Import dynamic template by id.
	 *
	 * @param string  $key Template name.
	 * @param integer $id  Template ID.
	 * @since 4.0.0
	 */
	private function handle_dynamic_templates( $key, $id ) {
		$result = $this->get_template_data( $id );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		$template_type = str_replace( '_post', '', $key );
		$template_type = str_replace( '_portfolio', '', $template_type );
		$title         = str_replace( '_', ' ', $key );

		$data = $this->condition_generator->get_layout_builder_template_data( $title, $key, $template_type );

		if ( 'cart' === $key && empty( $this->handle_wc_pages( $key ) ) ) {
			return false;
		}

		if ( 'checkout' === $key && empty( $this->handle_wc_pages( $key ) ) ) {
			return false;
		}

		$post_id = $this->create_template_page( $key, ucwords( $data['title'] ), $result, 'elementor_library', $data['type'] );

		if ( empty( $post_id ) ) {
			return false;
		}

		$conditions = $this->dynamic_template_condition_generator( $key );

		$this->handle_layout_builder_steps( $post_id, $conditions['rule_string'], $conditions['conditions'], $conditions['is_multi'] );

		return true;
	}

	/**
	 * Handle WooCommerce static pages.
	 *
	 * @param string $key Template key.
	 * @since 4.0.0
	 * return boolean
	 */
	private function handle_wc_pages( $key ) {
		$title = str_replace( '_', ' ', $key );

		$post_id = $this->create_template_page( $key, ucwords( $title ), [], 'page', 'wp-page' );

		if ( empty( $post_id ) ) {
			return false;
		}

		$page = str_replace( '_', '', $key );

		update_option( "woocommerce_{$page}_page_id", $post_id );

		return true;
	}

	/**
	 * Generate layout builder conditions for templates.
	 *
	 * @param string      $key  Template key.
	 * @param string|null $page Specific page to handle condition.
	 * @since 4.0.0
	 * @return array
	 */
	private function dynamic_template_condition_generator( $key ) {
		$template_name = '404_page' === $key ? 'not_found' : $key;
		$method_name   = $template_name . '_condition';

		return $this->condition_generator->get_condition( $method_name );
	}

	/**
	 * Update elementor kit data.
	 *
	 * @param string $index  Index on setting to update.
	 * @param array  $values Array of indexes and values to update.
	 * @since 4.0.0
	 */
	private function update_elementor_kit( $index, $values ) {
		$kit_document = Elementor::$instance->kits_manager->get_active_kit_for_frontend();

		if ( empty( $kit_document ) ) {
			return false;
		}

		$settings = $kit_document->get_settings();

		$settings = $this->import_scheme_colors( $index, $settings );

		$settings = $this->import_button_colors( $settings );

		if ( isset( $settings[ $index ] ) ) {
			foreach ( $values as $key => $value ) {
				$settings[ $index ][ $key ] = $value;
			}

			$kit_document->save( [ 'settings' => $settings ] );
			return true;
		}
	}

	/**
	 * Import scheme colors for a specific index in the settings.
	 *
	 * This function checks if the provided index is 'system_colors' and updates the settings accordingly.
	 * If the index is not 'system_colors', the original settings are returned unchanged.
	 *
	 * @param string $index    The index to check for scheme colors import.
	 * @param array  $settings The original settings array.
	 *
	 * @return array The updated settings array with scheme colors imported if applicable.
	 */
	public function import_scheme_colors( $index, $settings ) {
		if ( 'system_colors' !== $index ) {
			return $settings;
		}

		if ( isset( $settings['link_normal_color'] ) && ! empty( $this->system_color['primary_color'] ) ) {
			$settings['__globals__']['link_normal_color'] = 'globals/colors?id=primary';
		}

		return $settings;
	}

	/**
	 * Import added template by id.
	 *
	 * @param string  $key Template name.
	 * @param integer $id  Template ID.
	 * @since 4.0.0
	 */
	private function import_template_by_id( $key, $id ) {
		$result = $this->get_template_data( $id );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		$post_id = $this->create_template_page( $key, ucwords( $key ), $result, 'elementor_library', $key );

		if ( empty( $post_id ) ) {
			return false;
		}

		$conditions = [
			'conditionA' => 'include',
			'conditionB' => 'entire',
			'conditionC' => '',
			'conditionD' => '',
		];

		$this->handle_layout_builder_steps( $post_id, 'Entire website', $conditions );

		return true;
	}

	/**
	 * Handle layout builder steps.
	 *
	 * @param integer $id          Post ID.
	 * @param string  $rule_string Condition rule.
	 * @param array   $conditions  Array of layout builder conditions.
	 * @param boolean $is_multi    Check if there is multi conditions.
	 * @since 4.0.0
	 */
	private function handle_layout_builder_steps( $id, $rule_string, $conditions, $is_multi = false ) {
		update_post_meta( $id, 'jupiterx-condition-rules-string', $rule_string );

		$this->condition_generator->update_condition_meta( $id, $is_multi, $conditions );

		$option = get_option( self::$template_with_conditions, [] );

		if ( ! is_array( $option ) ) {
			$option = [];
		}

		// Post already added.
		if ( in_array( $id, $option, true ) ) {
			return;
		}

		array_push( $option, $id );

		update_option( self::$template_with_conditions, $option );
	}

	/**
	 * Get template data
	 *
	 * @param integer $id Template ID.
	 * @since 4.0.0
	 */
	private function get_template_data( $id ) {
		$url = self::$template_url . $id;

		$url = sprintf( $url, '' );

		$response = wp_remote_get( $url, [
			'timeout' => 40,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( 200 !== $response_code ) {
			return new \WP_Error( 'response_code_error', sprintf( 'The request returned with a status code of %s.', $response_code ) );
		}

		$template_content = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( isset( $template_content['error'] ) ) {
			return new \WP_Error( 'response_error', $template_content['error'] );
		}

		if ( empty( $template_content ) ) {
			return new \WP_Error( 'template_data_error', 'An invalid data was returned.' );
		}

		return $template_content;
	}

	/**
	 * Create template/page.
	 *
	 * @param string $key       Template key.
	 * @param string $title     Template title.
	 * @param array  $data      Template data.
	 * @param string $post_type Template post type.
	 * @since 4.0.0
	 * @return boolean|integer
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	private function create_template_page( $key, $title, $data, $post_type, $type ) {
		if ( ( empty( $data ) && 'page' === $post_type ) ) {
			$post_id = $this->create_simple_page( $title, $post_type );

			if ( empty( $post_id ) ) {
				return false;
			}

			return $post_id;
		}

		$template_type = 'single_post' === $key ? $key : $type;

		$unfiltered_files_upload_flag = false;

		if ( empty( get_option( 'elementor_unfiltered_files_upload' ) ) ) {
			$unfiltered_files_upload_flag = true;

			update_option( 'elementor_unfiltered_files_upload', 1 );
		}

		Elementor::$instance->uploads_manager->set_elementor_upload_state( true );

		$document = Elementor::$instance->documents->create(
			$type,
			[
				'post_title'  => $title,
				'post_status' => 'publish',
				'post_type'   => $post_type,
			]
		);

		$content = $this->replace_elements_ids( json_decode( $data['content'], true ) );

		$document->save( [
			'elements' => $content,
			'settings' => ! empty( $data['page_settings'] ) ? $data['page_settings'] : [],
		] );

		if ( $unfiltered_files_upload_flag ) {
			update_option( 'elementor_unfiltered_files_upload', '' );
		}

		Elementor::$instance->uploads_manager->set_elementor_upload_state( false );

		$post_id = $document->get_main_id();

		if ( empty( $post_id ) ) {
			return false;
		}

		if ( 'wp-page' !== $type ) {
			update_post_meta( $post_id, 'jx-layout-type', $template_type );
		}

		update_post_meta( $post_id, '_wp_page_template', 'full-width.php' );

		return $post_id;
	}

	/**
	 * Create simple page.
	 *
	 * @param string $title     Template title.
	 * @param string $post_type Template post type.
	 * @since 4.0.0
	 * @return boolean|integer
	 */
	private function create_simple_page( $title, $post_type ) {
		$args = [
			'post_type'   => $post_type,
			'post_title'  => $title,
			'post_status' => 'publish',
			'meta_input'  => [
				'_wp_page_template' => 'full-width.php',
			],
		];

		$post_id = wp_insert_post( $args );

		if ( empty( $post_id ) ) {
			return false;
		}

		return $post_id;
	}

	/**
	 * Replace elements ids.
	 *
	 * @param array $content Array of template content.
	 * @since 4.0.0
	 * @return array
	 */
	private function replace_elements_ids( $content ) {
		return Elementor::$instance->db->iterate_data( $content, function( $element ) {
			$element['id'] = Utils::generate_random_string();

			$has_image = ! empty( $element['settings']['background_image'] ) || ! empty( $element['settings']['image'] ) || ! empty( $element['settings']['icon_new']['value']['url'] );

			if ( ! $has_image ) {
				return $element;
			}

			$element_instance = Elementor::$instance->elements_manager->create_element_instance( $element );

			if ( ! empty( $element['settings']['background_image']['url'] ) ) {
				$control_class = Elementor::$instance->controls_manager->get_control( 'media' );

				if ( $control_class && method_exists( $control_class, 'on_import' ) ) {
					$element['settings']['background_image'] = $control_class->on_import( $element_instance->get_settings( 'background_image' ) );
				}
			}

			if ( ! empty( $element['settings']['icon_new']['value']['url'] ) ) {
				$control_class = Elementor::$instance->controls_manager->get_control( 'icons' );

				if ( $control_class && method_exists( $control_class, 'on_import' ) ) {
					$element['settings']['icon_new'] = $control_class->on_import( $element_instance->get_settings( 'icon_new' ) );
				}
			}

			return $element;
		} );
	}

	/**
	 * Create elementor kit data if not exist.
	 *
	 * @since 4.0.0
	 */
	private function create_elementor_kit() {
		$kit_id = get_option( 'elementor_active_kit', null );

		if ( ! empty( $kit_id ) ) {
			return;
		}

		\Elementor\Core\Kits\Manager::create_default_kit();
	}
}

JupiterX_Core_Setup_Wizard::get_instance();
