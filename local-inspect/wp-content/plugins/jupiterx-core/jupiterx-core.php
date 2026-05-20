<?php
/**
 * Plugin Name: Jupiter X Core
 * Plugin URI: https://jupiterx.com
 * Description: Adds core functionality to the Jupiter X theme.
 * Version: 4.14.1
 * Author: Artbees
 * Author URI: https://artbees.net
 * Text Domain: jupiterx-core
 * License: GPL2
 *
 * @package JupiterX_Core
 */

use Elementor\Plugin;

defined( 'ABSPATH' ) || die();

/**
 * Jupiter Core class.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
if ( ! class_exists( 'JupiterX_Core' ) ) {

	/**
	 * Jupiter Core class.
	 *
	 * @since 1.0.0
	 */
	class JupiterX_Core {

		/**
		 * Jupiter Core instance.
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 * @var JupiterX_Core
		 */
		private static $instance;

		/**
		 * The plugin version number.
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 * @var string
		 */
		private static $version;

		/**
		 * The plugin basename.
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 * @var string
		 */
		private static $plugin_basename;

		/**
		 * The plugin name.
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 * @var string
		 */
		private static $plugin_name;

		/**
		 * The plugin directory.
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 * @var string
		 */
		private static $plugin_dir;

		/**
		 * The plugin URL.
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 * @var string
		 */
		private static $plugin_url;

		/**
		 * The plugin assets URL.
		 *
		 * @since 1.2.0
		 * @access public
		 *
		 * @var string
		 */
		public static $plugin_assets_url;

		/**
		 * Returns JupiterX_Core instance.
		 *
		 * @since 1.0.0
		 *
		 * @return JupiterX_Core
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->define_constants();
			$this->load();
			$this->activation_hook();
		}

		/**
		 * Defines constants used by the plugin.
		 *
		 * @since 1.0.0
		 */
		protected function define_constants() {
			$plugin_data = get_file_data( __FILE__, array( 'Plugin Name', 'Version' ), 'jupiterx-core' );

			self::$plugin_basename   = plugin_basename( __FILE__ );
			self::$plugin_name       = array_shift( $plugin_data );
			self::$version           = array_shift( $plugin_data );
			self::$plugin_dir        = trailingslashit( plugin_dir_path( __FILE__ ) );
			self::$plugin_url        = trailingslashit( plugin_dir_url( __FILE__ ) );
			self::$plugin_assets_url = trailingslashit( self::$plugin_url . 'assets' );
		}

		/**
		 * Loads the plugin.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function load() {
			$this->load_files( [
				'utilities/general',
				'utilities/options',
				'admin/class-auto-updates',
				'extensions/class',
				'admin/class-notices',
				'svg-sanitizer/functions',
				'admin/license/class-api-license',
				'admin/license/class-event-license',
			] );

			add_action( 'jupiterx_init', [ $this, 'init' ], 4 );
		}

		/**
		 * Activation hook.
		 *
		 * @since 4.10.1
		 */
		protected function activation_hook() {
			register_activation_hook( __FILE__, [ $this, 'licensing_schedules' ] );

			add_action( 'admin_init', [ $this, 'check_event_transient' ] );

			register_deactivation_hook( __FILE__, [ $this, 'clear_licensing_schedules' ] );
		}

		/**
		 * Check event transient.
		 *
		 * @since 4.10.1
		 */
		public function check_event_transient() {
			if ( ! get_transient( 'jupiterx_event_transient' ) ) {
				$this->licensing_schedules();
			}
		}

		/**
		 * Initializes the plugin.
		 *
		 * @since 1.0.0
		 * @SuppressWarnings(PHPMD.NPathComplexity)
		 */
		public function init() {
			add_action( 'admin_bar_menu', [ $this, 'extend_admin_bar_menu' ], 100 );
			add_action( 'init', [ $this, 'redirect_page' ] );
			add_action( 'admin_head', [ $this, 'inline_css' ] );
			add_action( 'admin_print_footer_scripts', [ $this, 'inline_js' ] );

			// Hubspot affiliate code for jupiterx.
			add_filter( 'leadin_impact_code', [ $this, 'jupiterx_get_hubspot_affiliate_code' ] );

			load_plugin_textdomain( 'jupiterx-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			if ( version_compare( JUPITERX_VERSION, '1.2.0', '>' ) ) {
				$this->load_files( [
					'compiler/functions',
					'compiler/class-compiler',
				] );
			}

			if ( version_compare( JUPITERX_VERSION, '1.4.1', '>' ) ) {
				$this->load_files( [
					'google-tag/functions',
				] );
			}

			if ( version_compare( JUPITERX_VERSION, '1.6.0', '>=' ) ) {
				$this->load_files( [
					'widgets/class',
					'widgets/functions',
					'admin/options',
				] );
			}

			$this->check_theme_version();

			// Load files.
			$this->load_files( [
				'parse-css/functions',
				'post-type/class',
				'post-type/custom-snippets',
				'post-type/custom-fonts',
				'fonts',
				'post-type/custom-icons',
				'icons',
				'custom-fields/title-bar',
				'updater/functions',
				'widget-area/functions',
				'templates/class',
				'woocommerce/woocommerce-load-more',
				'woocommerce/functions',
				'woocommerce/product-gallery-video',
				'utilities/load',
				'condition/class-condition-manager',
			] );

			if ( ! $this->check_default_settings() ) {
				$this->load_files( [
					'customizer/functions',
				] );
			}

			if ( class_exists( 'Elementor\Plugin' ) ) {
				$this->load_files( [
					'popups/class',
					'popups/class-conditions-manager',
					'popups/class-triggers-manager',
				] );
			}

			if ( is_admin() ) {
				if ( ! defined( 'JUPITERX_OLD_CONTROL_PANEL' ) ) {
					$this->load_files( [
						'admin/site-health/site-health',
						'admin/tgmpa/tgmpa-plugin-list',
						'control-panel-2/class',
					] );
				}

				if ( ! class_exists( 'JupiterX_Update_Plugins' ) ) {
					$this->load_files( [
						'admin/update-plugins/class-update-plugins',
					] );
				}

				$this->load_files( [
					'admin/attachment-media/class',
				] );
			}

			$this->disable_admin_bar();

			// Enable Grid Container and Editor Top Bar by default.
			if ( class_exists( 'Elementor\Plugin' ) && get_option( 'jupiterx_fresh_install', false ) ) {
				update_option( 'elementor_experiment-container_grid', 'active' );
				update_option( 'elementor_experiment-editor_v2', 'active' );
			}

			/**
			 * Fires after all files have been loaded.
			 *
			 * @since 1.0.0
			 *
			 * @param JupiterX_Core
			 */
			do_action( 'jupiterx_core_init', $this );
		}

		/**
		 * Add useful pages to admin toolbar.
		 *
		 * @since 1.16.0
		 *
		 * @param array $admin_bar The WordPress admin toolbar array.
		 *
		 * @return void
		 */
		public function extend_admin_bar_menu( $admin_bar ) {
			$this->maintenance_mode_admin_bar_alert( $admin_bar );
		}

		/**
		 * Add maintenance admin-bar Alert.
		 *
		 * @since 1.20.0
		 *
		 * @param array $admin_bar The WordPress admin toolbar array.
		 *
		 * @return void
		 */
		private function maintenance_mode_admin_bar_alert( $admin_bar ) {
			$maintenance_mode = get_theme_mod( 'jupiterx_maintenance', false );

			if ( ! $maintenance_mode ) {
				return;
			}

			$maintenance_template = get_theme_mod( 'jupiterx_maintenance_template' );

			$admin_bar->add_node( [
				'id'     => 'jupiterx-maintenance-mode-on',
				'title'  => __( 'Maintenance Mode On', 'jupiterx-core' ),
			]);

			if ( ! class_exists( 'Elementor\Plugin' ) ) {
				return;
			}

			$document = Plugin::$instance->documents->get( $maintenance_template );

			$admin_bar->add_node( [
				'id' => 'jupiterx-maintanance-mode-edit',
				'parent' => 'jupiterx-maintenance-mode-on',
				'title' => __( 'Edit Template', 'jupiterx-core' ),
				'href' => $document ? $document->get_edit_url() : '',
			] );
		}

		/**
		 * Inline styles for admin pages.
		 *
		 * @since 1.1.0
		 *
		 * @return void
		 *
		 * @todo Move to common admin CSS file.
		 */
		public function inline_css() {
			ob_start();
			?>
			<style type="text/css">
				ul#adminmenu a[href*='admin.php?page=jupiterx_upgrade'],
				ul#adminmenu a.jupiterx_upgrade_submenu_link {
					color: #e24a95;
				}

				ul#adminmenu a[href*='admin.php?page=jupiterx_upgrade'] i.jupiterx-icon-pro,
				ul#adminmenu a.jupiterx_upgrade_submenu_link i.jupiterx-icon-pro {
					position: relative;
					top: 2px;
					display: inline-block;
					width: 15px;
					height: 15px;
					margin-right: 5px;
					font-size: 15px;
				}

				ul#adminmenu a[href*='admin.php?page=jupiterx_upgrade'] i.jupiterx-icon-pro:before,
				ul#adminmenu a.jupiterx_upgrade_submenu_link i.jupiterx-icon-pro:before {
					font-weight: bold;
				}

				.dashicons-jx-dashboard {
					background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg version='1.1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' width='20px' height='30px' viewBox='0 0 500 500' style='enable-background:new 0 0 500 500;' xml:space='preserve'%3e%3cstyle type='text/css'%3e .st0%7bfill:%239CA1A8;%7d %3c/style%3e%3cpath class='st0' d='M485,3.6H362.5L249.7,163.3l61.2,86.7l-61.2,86.7l-61.2-86.7L14.4,496.3h122.5l112.8-159.7l112.8,159.7H485 L310.9,249.9L485,3.6z M136.9,3.6H14.4l174.1,246.4l61.2-86.7L136.9,3.6z'/%3e%3c/svg%3e ");
					background-repeat: no-repeat;
					background-position: center;
					background-size: 20px auto;
				}

				.wp-admin.post-type-jupiterx-popups #wpbody-content .wrap a.page-title-action,
				.wp-admin.post-type-jupiterx-popups #wpbody-content .wrap .wp-heading-inline {
					visibility: hidden !important;
				}
			</style>
			<?php
			echo ob_get_clean(); // phpcs:ignore
		}

		/**
		 * Add hubspot affiliate code for Jupiterx.
		 *
		 * @since 3.2.0
		 * @return string
		 */
		public function jupiterx_get_hubspot_affiliate_code() {
			return '9WvmE0';
		}

		/**
		 * Inline scripts for admin pages.
		 *
		 * @since 1.1.0
		 *
		 * @return void
		 *
		 * @todo Move to common admin JS file.
		 */
		public function inline_js() {
			ob_start();
			?>
			<script type="text/javascript">
				jQuery(document).ready( function($) {
					$( "ul#adminmenu a[href*='admin.php?page=jupiterx_help']" ).attr( 'target', '_blank' );
					$( "ul#adminmenu a[href*='admin.php?page=jupiterx_upgrade']" )
						.addClass('jupiterx_upgrade_submenu_link')
						.attr( 'target', '_blank' )
						.attr( 'href', 'https://themeforest.net/item/jupiter-multipurpose-responsive-theme/5177775?ref=artbees&utm_source=AdminSideBarUpgradeLink&utm_medium=AdminUpgradePopup&utm_campaign=FreeJupiterXAdminUpgradeCampaign' );
				});
			</script>
			<?php
			echo ob_get_clean(); // phpcs:ignore
		}

		/**
		 * Returns the version number of the plugin.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function version() {
			return self::$version;
		}

		/**
		 * Returns the plugin basename.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function plugin_basename() {
			return self::$plugin_basename;
		}

		/**
		 * Returns the plugin name.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function plugin_name() {
			return self::$plugin_name;
		}

		/**
		 * Returns the plugin directory.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function plugin_dir() {
			return self::$plugin_dir;
		}

		/**
		 * Returns the plugin URL.
		 *
		 * @since 1.0.0
		 *
		 * @return string
		 */
		public function plugin_url() {
			return self::$plugin_url;
		}

		/**
		 * Returns the plugin assets URL.
		 *
		 * @since 1.18.0
		 *
		 * @return string
		 */
		public function plugin_assets_url() {
			return self::$plugin_assets_url;
		}

		/**
		 * Loads all PHP files in a given directory.
		 *
		 * @since 1.0.0
		 *
		 * @param string $directory_name The directory name to load the files.
		 */
		public function load_directory( $directory_name ) {
			$path       = trailingslashit( $this->plugin_dir() . 'includes/' . $directory_name );
			$file_names = glob( $path . '*.php' );
			foreach ( $file_names as $filename ) {
				if ( file_exists( $filename ) ) {
					require_once $filename;
				}
			}
		}

		/**
		 * Loads specified PHP files from the plugin includes directory.
		 *
		 * @since 1.0.0
		 *
		 * @param array $file_names The names of the files to be loaded in the includes directory.
		 */
		public function load_files( $file_names = array() ) {
			foreach ( $file_names as $file_name ) {
				$path = $this->plugin_dir() . 'includes/' . $file_name . '.php';

				if ( file_exists( $path ) ) {
					require_once $path;
				}
			}
		}

		/**
		 * Redirect an admin page.
		 *
		 * @since 1.0.0
		 */
		public function redirect_page() {
			// phpcs:disable
			if ( ! isset( $_GET['page'] ) ) {
				return;
			}

			if ( 'customize_theme' === $_GET['page'] ) {
				wp_redirect( admin_url( 'customize.php' ) );
				exit;
			}

			if ( 'jupiterx_upgrade' === $_GET['page'] ) {
				wp_redirect( admin_url() );
				exit;
			}

			if ( 'jupiterx_help' === $_GET['page'] ) {
				wp_redirect( 'https://themes.artbees.net/support/jupiterx/' );
				exit;
			}
			// phpcs:enable
		}

		/**
		 * Registers license schedules for various periodic checks.
		 *
		 * @since 4.10.1
		 *
		 * @return void
		 */
		public function licensing_schedules() {
			wp_schedule_event( time(), 'weekly', 'jupiterx_license_checks' );

			set_transient( 'jupiterx_event_transient', true, 30 * DAY_IN_SECONDS );
		}

		/**
		 * Clears future schedules after plugin deactivations.
		 *
		 * @since 4.10.1
		 *
		 * @return void
		 */
		public function clear_licensing_schedules() {
			if ( is_multisite() ) {
				$sites = get_sites();

				foreach ( $sites as $site ) {
					switch_to_blog( $site->blog_id );

					wp_clear_scheduled_hook( 'jupiterx_license_checks' );
					delete_transient( 'jupiterx_event_transient' );

					restore_current_blog();
				}
			}

			wp_clear_scheduled_hook( 'jupiterx_license_checks' );
			delete_transient( 'jupiterx_event_transient' );
		}

		/**
		 * Check setup wizard is enabled.
		 *
		 * @since 4.0.0
		 * @return boolean.
		 */
		public function jupiterx_check_setup_wizard() {
			if ( get_option( 'jupiterx_setup_wizard_skipped', false ) ) {
				return false;
			}

			if ( get_option( 'jupiterx_setup_wizard_done', false ) ) {
				return false;
			}

			if (
				! empty( get_option( 'jupiterx_setup_wizard_hide', false ) ) &&
				get_option( 'jupiterx_setup_wizard_hide', false ) > time()
			) {
				return true;
			}

			$fresh_install = get_option( 'jupiterx_fresh_install', false );

			if ( $fresh_install ) {
				update_option( 'jupiterx_setup_wizard_hide', strtotime( '+14 days', time() ) );

				return true;
			}

			return false;
		}

		/**
		 * Disable admin bar in Elementor preview.
		 *
		 * Admin bar causes spacing issues. Elementor added the same codes but it's not working correctly.
		 * When it's fixed, the codes will be removed.
		 *
		 * @since 1.0.0
		 */
		private function disable_admin_bar() {
			if ( ! empty( $_GET['elementor-preview'] ) ) { // phpcs:ignore
				add_filter( 'show_admin_bar', '__return_false' );
			}
		}

		/**
		 * Handle theme update modal.
		 *
		 * @since 4.0.0
		 */
		private function check_theme_version() {
			if ( 'dismiss' === get_option( 'jupiterx_theme_update_modal', '' ) ) {
				return;
			}

			$version = wp_get_theme()->get( 'Version' );

			if ( is_a( wp_get_theme()->parent(), '\WP_Theme' ) ) {
				$version = wp_get_theme()->parent()->get( 'Version' );
			}

			if ( version_compare( $this->version(), $version, '<=' ) ) {
				update_option( 'jupiterx_theme_update_modal', 'dismiss' );
				return;
			}

			update_option( 'jupiterx_theme_update_modal', 'show' );
		}

		/**
		 * Check default settings are enabled.
		 *
		 * @since 3.8.0
		 */
		public function check_default_settings() {
			$version = wp_get_theme()->get( 'Version' );

			if ( is_a( wp_get_theme()->parent(), '\WP_Theme' ) ) {
				$version = wp_get_theme()->parent()->get( 'Version' );
			}

			if ( version_compare( $version, '3.8.0', '<' ) ) {
				return false;
			}

			$jx_settings = get_option( 'jupiterx', [] );

			$fresh_install = get_option( 'jupiterx_first_installation', false );

			if ( ! empty( $fresh_install ) ) {
				if ( ! isset( $jx_settings['disable_theme_default_settings'] ) ) {
					$jx_settings['disable_theme_default_settings'] = true;

					update_option( 'jupiterx', $jx_settings );
				}

				delete_option( 'jupiterx_first_installation' );
			}

			if ( ! isset( $jx_settings['disable_theme_default_settings'] ) ) {
				return false;
			}

			$default_settings = ! empty( $jx_settings['disable_theme_default_settings'] ) ? $jx_settings['disable_theme_default_settings'] : '';

			if ( empty( $default_settings ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Disable page title bar.
		 *
		 * @since 4.4.0
		 */
		public function disable_page_title_bar() {
			$transient_key = 'elementor_library_page_title_bar';

			if ( ! $this->check_default_settings() ) {
				delete_transient( $transient_key );

				jupiterx_update_option( 'has_page_title_bar', true );
				return false;
			}

			if ( ! jupiterx_get_option( 'has_page_title_bar', true ) ) {
				return true;
			}

			global $wpdb;

			$post_type   = 'elementor_library';
			$meta_key    = '_elementor_template_type';
			$meta_value  = 'page-title-bar';
			$cached_post = get_transient( $transient_key );

			if ( ! empty( $cached_post ) ) {
				jupiterx_update_option( 'has_page_title_bar', true );
				return false;
			}

			$query = $wpdb->prepare(
				"SELECT p.* FROM {$wpdb->posts} AS p
				INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
				WHERE p.post_type = %s
				AND p.post_status = 'publish'
				AND pm.meta_key = %s
				AND pm.meta_value = %s
				LIMIT 1",
				$post_type, $meta_key, $meta_value
			);

			$page_title_bar = $wpdb->get_results( $query ); // phpcs:ignore

			if ( ! empty( $page_title_bar ) ) {
				set_transient( $transient_key, $page_title_bar );

				jupiterx_update_option( 'has_page_title_bar', true );
				return false;
			}

			delete_transient( $transient_key );

			jupiterx_update_option( 'has_page_title_bar', false );
			return true;
		}
	}
}

/**
 * Returns the Jupiter Core application instance.
 *
 * @since 1.0.0
 *
 * @return JupiterX_Core
 */
function jupiterx_core() {
	return JupiterX_Core::get_instance();
}

/**
 * Initializes the Jupiter Core application.
 *
 * @since 1.0.0
 */
jupiterx_core();
