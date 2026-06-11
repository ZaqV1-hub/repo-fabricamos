<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Tabs_Settings' ) ) {

	/**
	 * Define Jet_Tabs_Settings class
	 */
	class Jet_Tabs_Settings {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * [$key description]
		 * @var string
		 */
		public $key = 'jet-tabs-settings';

		/**
		 * [$builder description]
		 * @var null
		 */
		public $builder = null;

		/**
		 * [$settings description]
		 * @var null
		 */
		public $settings = null;

		/**
		 * Avaliable Widgets array
		 *
		 * @var array
		 */
		public $avaliable_widgets = [];

		/**
		 * [$settings_page_config description]
		 * @var [type]
		 */
		public $settings_page_config = [];

		/**
		 * Init page
		 */
		public function init() {

			foreach ( glob( jet_tabs()->plugin_path( 'includes/addons/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class'=>'Class', 'name' => 'Name', 'slug'=>'Slug' ) );

				$slug = basename( $file, '.php' );
				$this->avaliable_widgets[ $slug] = $data['name'];
			}

			add_action( 'jet-styles-manager/compatibility/register-plugin', array( $this, 'register_for_styles_manager' ) );
		}

		/**
		 * Register jet-tabs plugin for styles manager
		 *
		 * @param  object $compatibility_manager JetStyleManager->compatibility instance
		 * @return void
		 */
		public function register_for_styles_manager( $compatibility_manager ) {
			$compatibility_manager->register_plugin( 'jet-tabs', (int) $this->get( 'widgets_load_level', 100 ) );
		}

		/**
		 * Return settings page URL
		 *
		 * @return string
		 */
		public function get_settings_page_link() {

			return add_query_arg(
				array(
					'page' => $this->key,
				),
				esc_url( admin_url( 'admin.php' ) )
			);

		}

		/**
		 * [get description]
		 * @param  [type]  $setting [description]
		 * @param  boolean $default [description]
		 * @return [type]           [description]
		 */
		public function get( $setting, $default = false ) {

			if ( null === $this->settings ) {
				$this->settings = get_option( $this->key, array() );
			}

			return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : $default;
		}

		/**
         * Get time delay list 
         *
         * @param bool $options 
         * @return array
         */
        public static function get_popup_time_delay_list($options = false) {
            $list = [
				'none'    => esc_html__('None', 'jet-tabs'),
				'hour'    => esc_html__('1 Hour', 'jet-tabs'),
				'day'     => esc_html__('1 Day', 'jet-tabs'),
				'3days'   => esc_html__('3 Days', 'jet-tabs'),
				'week'    => esc_html__('1 Week', 'jet-tabs'),
				'month'   => esc_html__('1 Month', 'jet-tabs'),
			];

            if ($options) {
                $options_list = [];
                foreach ($list as $value => $label) {
                    $options_list[] = [
                        'label' => $label,
                        'value' => $value,
                    ];
                }
                return $options_list;
            }

            return $list;
        }

		/**
		 * [generate_frontend_config_data description]
		 * @return [type] [description]
		 */
		public function get_frontend_config_data() {

			$default_active_widgets = [];
		
			foreach ( $this->avaliable_widgets as $slug => $name ) {
		
				$avaliable_widgets[] = [
					'label' => $name,
					'value' => $slug,
				];
		
				$default_active_widgets[ $slug ] = 'true';
			}
		
			$active_widgets = $this->get( 'avaliable_widgets', $default_active_widgets );
		
			$rest_api_url = apply_filters( 'jet-tabs/rest/frontend/url', get_rest_url() );

			return [
				
				'messages' => [
					'saveSuccess' => esc_html__( 'Saved', 'jet-tabs' ),
					'saveError'   => esc_html__( 'Error', 'jet-tabs' ),
				],
				'settingsApiUrl' => '/jet-tabs-api/v1/plugin-settings',
				'clearTabsCachePath' => '/jet-tabs-api/v1/clear-tabs-cache',
				'cacheTimeoutOptions' => self::get_popup_time_delay_list(true),
				'settingsData' => [
					'widgets_load_level' => [
						'value'   => $this->get( 'widgets_load_level', 100 ),
						'options' => [
							[
								'label' => 'None',
								'value' => 0,
							],
							[
								'label' => 'Low',
								'value' => 25,
							],
							[
								'label' => 'Medium',
								'value' => 50,
							],
							[
								'label' => 'Advanced',
								'value' => 75,
							],
							[
								'label' => 'Full',
								'value' => 100,
							],
						],
					],
					'ajax_request_type' => [
						'value'   => $this->get( 'ajax_request_type', 'default' ),
						'options' => [
							[
								'label' => esc_html__( 'Default (REST API request)', 'jet-tabs' ),
								'value' => 'default',
							],
							[
								'label' => esc_html__( 'Self (current page)', 'jet-tabs' ),
								'value' => 'self',
							],
						],
					],
					'avaliable_widgets' => [
						'value'   => $active_widgets,
						'options' => $avaliable_widgets,
					],
					
					'useTemplateCache' => $this->get( 'useTemplateCache', [
						'enable'          => false,
						'cacheExpiration' => 'week',
					] ),
				],
			];
		}
		

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
}

/**
 * Returns instance of Jet_Tricks_Settings
 *
 * @return object
 */
function jet_tabs_settings() {
	return Jet_Tabs_Settings::get_instance();
}
