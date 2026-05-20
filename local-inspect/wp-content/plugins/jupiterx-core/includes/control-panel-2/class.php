<?php
/**
 * This class adds new control panel.
 *
 * @package JupiterX_Core\Control_Panel_2
 *
 * @since 1.18.0
 */

use JupiterX_Core\Raven\Plugin;
use Elementor\Plugin as Elementor;

/**
 * New control panel.
 *
 * @package JupiterX_Core\Control_Panel_2
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @since 1.18.0
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class JupiterX_Control_Panel_2 {

	const SCREEN_ID = 'jupiterx';

	/**
	 * Components store.
	 *
	 * @since 1.18.0
	 *
	 * @var array
	 */
	private $components = [];

	/**
	 * Components store.
	 *
	 * @since 3.7.0
	 *
	 * @var array
	 */
	private static $referrer = null;

	/**
	 * Constructor.
	 *
	 * @since 1.18.0
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'init' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ] );
		add_action( 'in_admin_header', [ $this, 'register_popups_callback' ] );
		add_action( 'parent_file', [ $this, 'keep_menu_open_popups' ] );
	}

	/**
	 * Initialize.
	 *
	 * @since 1.18.0
	 */
	public function init() {
		jupiterx_core()->load_files( [
			'control-panel-2/includes/logic-messages',
			'control-panel-2/includes/class-helpers',
			'control-panel-2/includes/class-filesystem',
			'control-panel-2/includes/class-db-manager',
			'control-panel-2/includes/class-db-php-manager',
			'control-panel-2/includes/class-export-import-content',
			'control-panel-2/includes/class-install-template',
			'control-panel-2/includes/class-license',
			'control-panel-2/includes/class-theme-upgrades-downgrades',
			'control-panel-2/includes/class-install-plugins',
			'control-panel-2/includes/class-updates-manager',
			'control-panel-2/includes/class-templates',
			'control-panel-2/includes/class-settings',
			'control-panel-2/includes/class-version-control',
			'control-panel-2/includes/class-image-sizes',
			'control-panel-2/includes/class-logs',
			'control-panel-2/includes/class-layout-builder',
			'control-panel-2/includes/class-custom-snippets',
			'control-panel-2/includes/class-custom-fonts',
			'control-panel-2/includes/custom-icons/class-custom-icons',
			'control-panel-2/includes/custom-icons/icon-sets/icon-set-base',
			'control-panel-2/includes/class-sellkit-box',
			'control-panel-2/includes/class-popup',
			'control-panel-2/includes/class-enable-widgets',
			'control-panel-2/includes/class-promotion-banner',
			'control-panel-2/includes/setup-wizard/class-condition-generator',
			'control-panel-2/includes/setup-wizard/class-setup-wizard',
			'control-panel-2/includes/class-theme-update',
		] );

		$this->components['license']   = JupiterX_Core_Control_Panel_License::get_instance();
		$this->components['templates'] = JupiterX_Core_Control_Panel_Templates::get_instance();
		$this->components['logs']      = JupiterX_Core_Control_Panel_logs::get_instance();

		if ( $this->is_current_screen() ) {
			$this->back_compat();
		}
	}

	/**
	 * Run backward compatibility actions.
	 */
	private function back_compat() {
		$this->components['license']->retry_api_key();
	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @since 1.18.0
	 */
	public function enqueue_admin_scripts() {
		if ( ! $this->is_current_screen() ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'jupiterx-control-panel-2',
			jupiterx_core()->plugin_url() . 'includes/control-panel-2/dist/control-panel.js',
			[ 'lodash', 'wp-element', 'wp-i18n', 'wp-util' ],
			jupiterx_core()->version(),
			true
		);

		wp_localize_script(
			'jupiterx-control-panel-2',
			'jupiterxControlPanel2',
			$this->get_localize_data()
		);

		wp_enqueue_style(
			'jupiterx-control-panel-2',
			jupiterx_core()->plugin_url() . 'includes/control-panel-2/dist/control-panel.css',
			[],
			jupiterx_core()->version()
		);

		wp_set_script_translations( 'jupiterx-control-panel-2', 'jupiterx-core', jupiterx_core()->plugin_dir() . 'languages' );
	}

	/**
	 * Register admin menu.
	 *
	 * @since 1.18.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	public function register_admin_menu() {
		if ( ! defined( 'JUPITERX_NAME' ) ) {
			return;
		}

		$menu_icon = 'dashicons-jx-dashboard';

		if ( function_exists( 'jupiterx_is_white_label' ) ) {
			if ( jupiterx_is_white_label() && jupiterx_get_option( 'white_label_menu_icon' ) ) {
				$menu_icon = jupiterx_get_option( 'white_label_menu_icon' );
			}
		}

		$menu_name = JUPITERX_NAME;

		if ( function_exists( 'jupiterx_is_white_label' ) ) {
			if ( jupiterx_is_white_label() && jupiterx_get_option( 'white_label_text_occurence' ) ) {
				$menu_name = esc_html( jupiterx_get_option( 'white_label_text_occurence' ) );
			}
		}

		add_menu_page(
			$menu_name,
			$menu_name,
			'manage_options',
			self::SCREEN_ID,
			[ $this, 'register_admin_menu_callback' ],
			$menu_icon,
			'3.5'
		);

		add_submenu_page(
			self::SCREEN_ID,
			__( 'Dashboard', 'jupiterx-core' ),
			__( 'Dashboard', 'jupiterx-core' ) . $this->warning_badge(),
			'edit_theme_options',
			self::SCREEN_ID,
			[ $this, 'register_admin_menu_callback' ]
		);

		if ( jupiterx_is_premium() ) {
			if ( $this->is_white_label( 'layout-builder' ) ) {
				add_submenu_page(
					self::SCREEN_ID,
					esc_html__( 'Layout Builder', 'jupiterx-core' ),
					esc_html__( 'Layout Builder', 'jupiterx-core' ),
					'edit_theme_options',
					'jupiterx#/layout-builder',
					[ $this, 'register_admin_menu_callback' ]
				);
			}

			if ( $this->is_white_label( 'custom-snippets' ) ) {
				add_submenu_page(
					self::SCREEN_ID,
					esc_html__( 'Custom Snippets', 'jupiterx-core' ),
					esc_html__( 'Custom Snippets', 'jupiterx-core' ),
					'edit_theme_options',
					'jupiterx#/custom-snippets',
					[ $this, 'register_admin_menu_callback' ]
				);
			}
		}

		if ( $this->is_white_label( 'custom-fonts' ) ) {
			add_submenu_page(
				self::SCREEN_ID,
				esc_html__( 'Custom Fonts', 'jupiterx-core' ),
				esc_html__( 'Custom Fonts', 'jupiterx-core' ),
				'edit_theme_options',
				'jupiterx#/custom-fonts',
				[ $this, 'register_admin_menu_callback' ]
			);
		}

		if ( $this->is_white_label( 'custom-icons' ) ) {
			add_submenu_page(
				self::SCREEN_ID,
				esc_html__( 'Custom Icons', 'jupiterx-core' ),
				esc_html__( 'Custom Icons', 'jupiterx-core' ),
				'edit_theme_options',
				'jupiterx#/custom-icons',
				[ $this, 'register_admin_menu_callback' ]
			);
		}

		if ( class_exists( 'Elementor\Plugin' ) ) {
			add_submenu_page(
				self::SCREEN_ID,
				esc_html__( 'Popups', 'jupiterx-core' ),
				esc_html__( 'Popups', 'jupiterx-core' ),
				'publish_posts',
				$this->get_popup_url(),
				false
			);
		}

		if ( ! ( defined( 'JUPITERX_CONTROL_PANEL_SETTINGS' ) && ! JUPITERX_CONTROL_PANEL_SETTINGS ) ) {
			add_submenu_page(
				self::SCREEN_ID,
				esc_html__( 'Settings', 'jupiterx-core' ),
				esc_html__( 'Settings', 'jupiterx-core' ),
				'edit_theme_options',
				'jupiterx#/settings',
				[ $this, 'register_admin_menu_callback' ]
			);
		}

		if ( jupiterx_core()->jupiterx_check_setup_wizard() ) {
			add_submenu_page(
				self::SCREEN_ID,
				esc_html__( 'Setup Wizard', 'jupiterx-core' ),
				esc_html__( 'Setup Wizard', 'jupiterx-core' ),
				'edit_theme_options',
				'jupiterx-setup-wizard',
				[ $this, 'setup_wizard_root' ]
			);
		}

		if ( function_exists( 'jupiterx_is_white_label' ) ) {
			if ( ! jupiterx_is_white_label() || ( jupiterx_is_white_label() && jupiterx_get_option( 'white_label_menu_help', true ) ) ) {
				add_submenu_page(
					self::SCREEN_ID,
					__( 'Help', 'jupiterx-core' ),
					__( 'Help', 'jupiterx-core' ),
					'edit_theme_options',
					'jupiterx_help',
					[ $this, 'redirect_page' ]
				);
			}
		}

		if ( function_exists( 'jupiterx_is_pro' ) && ! jupiterx_is_pro() && ! jupiterx_is_premium() ) {
			add_submenu_page(
				self::SCREEN_ID,
				__( 'Upgrade', 'jupiterx-core' ),
				'<i class="jupiterx-icon-pro"></i>' . __( 'Upgrade', 'jupiterx-core' ),
				'edit_theme_options',
				'jupiterx_upgrade',
				[ $this, 'redirect_page' ]
			);
		}

		remove_submenu_page( 'themes.php', self::SCREEN_ID );
	}

	/**
	 * Get warining badge for premium users.
	 *
	 * @since 1.18.0
	 *
	 * @return string
	 */
	private function warning_badge() {
		if (
			! function_exists( 'jupiterx_is_registered' ) ||
			! function_exists( 'jupiterx_is_premium' )
		) {
			return '';
		}

		if ( ! jupiterx_is_premium() ) {
			return '';
		}

		if ( jupiterx_is_registered() ) {
			return '';
		}

		return sprintf(
			' <img class="jupiterx-premium-warning-badge" src="%1$s" alt="%2$s" width="16" height="16">',
			trailingslashit( jupiterx_core()->plugin_assets_url() ) . 'images/warning-badge.svg',
			esc_html__( 'Activate Product', 'jupiterx-core' )
		);
	}

	/**
	 * Redirect an admin page.
	 *
	 * @since 1.18.0
	 */
	public function redirect_page() {
		if ( empty( jupiterx_get( 'page' ) ) ) {
			return;
		}

		if ( 'customize_theme' === jupiterx_get( 'page' ) ) {
			wp_safe_redirect( admin_url( 'customize.php' ) );
			exit;
		}

		if ( 'jupiterx_upgrade' === jupiterx_get( 'page' ) ) {
			wp_safe_redirect( admin_url() );
			exit;
		}

		if ( 'jupiterx_help' === jupiterx_get( 'page' ) ) {
			wp_safe_redirect( 'https://help.jupiterx.com/' );
			exit;
		}
	}

	/**
	 * Register admin menu callback.
	 *
	 * @since 1.18.0
	 */
	public function register_admin_menu_callback() {
		?>
		<div id="wrap" class="wrap">
			<h1></h1>
			<div id="jx-cp-root" class="jx-cp"></div>
		</div>
		<?php
	}

	/**
	 * Register admin popup menu callback.
	 *
	 * @since 3.7.0
	 */
	public function register_popups_callback() {
		$popup = ! empty( $_GET['post_type'] ) ? htmlspecialchars( $_GET['post_type'] ) : ''; // phpcs:ignore

		if ( 'jupiterx-popups' !== $popup ) {
			return;
		}
		?>
		<div id="jx-popup-root" class="jx-popup"></div>
		<?php
	}

	/**
	 * Add enable widget modal root.
	 *
	 * @since 2.5.0
	 */
	public function enable_widget_root() {
		?>
		<div id="jx-enable-widget-root" class="jx-cp"></div>
		<?php
	}

	/**
	 * Add theme update modal root.
	 *
	 * @since 4.0.0
	 */
	public function add_theme_update_modal() {
		?>
		<div id="jx-theme-update-root" class="jx-cp"></div>
		<?php
	}

	/**
	 * Add enable widget modal root.
	 *
	 * @since 4.0.0
	 */
	public function setup_wizard_root() {
		?>
		<div id="jx-setup-wizard-root" class="jx-cp"></div>
		<?php
	}

	/**
	 * Add Popups submenu to Jupiter X menu for keeping menu open.
	 *
	 * @since 3.7.0
	 */
	public function keep_menu_open_popups( $parent_file ) {
		global $current_screen;
		$post_type = $current_screen->post_type;

		if ( 'jupiterx-popups' === $post_type ) {
			$parent_file = 'jupiterx';
		}

		return $parent_file;
	}


	/**
	 * Get localize data.
	 *
	 * @since 1.18.0
	 */
	private function get_localize_data() {
		$jx_settings = get_option( 'jupiterx', [] );
		$version     = wp_get_theme()->get( 'Version' );

		if ( is_a( wp_get_theme()->parent(), '\WP_Theme' ) ) {
			$version = wp_get_theme()->parent()->get( 'Version' );
		}

		$data = [
			'nonce' => wp_create_nonce( 'jupiterx_control_panel' ),
			'themeVersion' => $this->get_theme_data( 'Version' ),
			'jupiterxVersion' => JUPITERX_VERSION,
			'urls' => [
				'customize' => admin_url( 'customize.php' ),
				'upgrade' => jupiterx_upgrade_link(),
				'upgradeBanner' => jupiterx_upgrade_link( 'banner' ),
				'upgradeComparison' => jupiterx_upgrade_link( 'comparison' ),
				'siteHealth' => esc_url( admin_url( 'site-health.php' ) ),
				'controlPanel' => jupiterx_core()->plugin_url() . 'includes/control-panel-2/',
				'controlPanelUrl' => admin_url( 'admin.php?page=jupiterx' ),
				'imgUrl' => jupiterx_core()->plugin_url() . 'includes/control-panel-2/img',
				'siteUrl' => site_url(),
			],
			'installedPlugins' => array_keys( get_plugins() ),
			'activePlugins' => array_values( get_option( 'active_plugins' ) ),
			'options' => get_option( 'jupiterx', [] ),
			'postTypes' => array_values( jupiterx_get_custom_post_types( 'objects' ) ),
			'themeLicense' => $this->components['license']->get_details(),
			'isPremium' => jupiterx_is_premium(),
			'isPro' => jupiterx_is_pro(),
			'searchFilters' => $this->components['templates']->get_filters(),
			'templateInstalled' => $this->components['templates']->get_installed(),
			'adminAjaxURL' => admin_url( 'admin-ajax.php' ),
			'siteName' => get_bloginfo( 'name' ),
			'debug' => $this->components['logs']->get_info(),
			'tabs' => $this->get_tabs(),
			'isMultilingual' => ( function_exists( 'pll_current_language' ) || class_exists( 'SitePress' ) ),
			'layoutTemplates' => JupiterX_Core_Control_Panel_Layout_Builder::layout_templates(),
			'customSnippetsLocations' => JupiterX_Core_Control_Panel_Custom_Snippets::snippet_locations(),
			'elements' => Plugin::get_modules( true ),
			'sellkitProActive' => class_exists( 'Sellkit_Pro' ),
			'sellkitFreeActive' => class_exists( 'Sellkit' ),
			'woocommerceActive' => class_exists( 'Woocommerce' ),
			'elementorActive' => class_exists( 'Elementor\Plugin' ),
			'wpmlActive' => defined( 'ICL_SITEPRESS_VERSION' ),
			'welcomeBox' => get_option( 'jupiterx_dashboard_welcome_box' ),
			'sellkitDismiss' => get_user_meta( get_current_user_id(), 'jupiterx_dismiss_sellkit_box', true ),
			'popupConditions' => $this->get_popup_conditions(),
			'popupTriggers' => $this->get_popup_triggers(),
			'timezones' => timezone_identifiers_list(),
			'topBar' => class_exists( 'Elementor\Plugin' ) ? $this->check_elementor_top_bar() : '',
			'elementorImportSecurity' => get_option( 'elementor_unfiltered_files_upload', 0 ),
			'isRequiredPluginsActivated' => defined( 'ELEMENTOR_VERSION' ) && class_exists( 'ACF' ),
			'isSetupWizard' => get_option( 'jupiterx_setup_wizard_done', false ),
			'checkSimplicityMode' => jupiterx_core()->check_default_settings(),
			'SimplicityVersion' => version_compare( $version, '3.8.0', '<' ) ? false : true,
			'freshInstall' => ! isset( $jx_settings['disable_theme_default_settings'] ) || version_compare( $version, '3.8.0', '<' ) ? '' : 1,
			'imageSizes' => get_option( JUPITERX_IMAGE_SIZE_OPTION, [] ),
		];

		jupiterx_log(
			"[Control Panel] To view Control Panel, the following data is expected to be an array consisting of 'nonce', 'themeVersion', 'urls', ...  'tabs'.",
			$data
		);

		return $data;
	}

	/**
	 * Get control panel tabs.
	 *
	 * @since 1.18.0
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	private function get_tabs() {
		$tabs = [
			'dashboard' => [
				'id' => 'dashboard',
				'href' => '/',
				'label' => __( 'Dashboard', 'jupiterx-core' ),
				'help' => ! jupiterx_is_premium() ? 'https://themes.artbees.net/' : 'https://help.jupiterx.com/',
				'whiteLabel' => true,
				'subMenu' => false,
				'whiteLabelEnabled' => $this->is_white_label( 'dashboard' ),
			],
			'layout-builder' => [
				'id' => 'layout-builder',
				'href' => '/layout-builder',
				'label' => __( 'Layout Builder', 'jupiterx-core' ),
				'help' => 'https://help.jupiterx.com/',
				'whiteLabel' => true,
				'whiteLabelEnabled' => $this->is_white_label( 'layout-builder' ),
			],
			'custom-snippets' => [
				'id' => 'custom-snippets',
				'href' => '/custom-snippets',
				'label' => __( 'Custom Snippets', 'jupiterx-core' ),
				'help' => 'https://help.jupiterx.com/',
				'whiteLabel' => true,
				'subMenu' => false,
				'whiteLabelEnabled' => $this->is_white_label( 'custom-snippets' ),
			],
			'custom-fonts' => [
				'id' => 'custom-fonts',
				'href' => '/custom-fonts',
				'label' => __( 'Custom Fonts', 'jupiterx-core' ),
				'help' => 'https://help.jupiterx.com/',
				'whiteLabel' => true,
				'subMenu' => false,
				'whiteLabelEnabled' => $this->is_white_label( 'custom-fonts' ),
			],
			'custom-icons' => [
				'id' => 'custom-icons',
				'href' => '/custom-icons',
				'label' => __( 'Custom Icons', 'jupiterx-core' ),
				'help' => 'https://help.jupiterx.com/',
				'whiteLabel' => true,
				'subMenu' => false,
				'whiteLabelEnabled' => $this->is_white_label( 'custom-icons' ),
			],
			'ready-made-websites' => [
				'id' => 'ready-made-websites',
				'href' => '/ready-made-websites',
				'label' => __( 'Readymade Websites', 'jupiterx-core' ),
				'help' => 'https://help.jupiterx.com/',
				'whiteLabel' => true,
				'subMenu' => false,
				'whiteLabelEnabled' => $this->is_white_label( 'ready-made-websites' ),
			],
			'settings' => [
				'id' => 'settings',
				'href' => '/settings',
				'label' => __( 'Settings', 'jupiterx-core' ),
				'help' => 'https://help.jupiterx.com/',
				'whiteLabel' => false,
				'subMenu' => false,
				'subTabs' => [
					'general' => [
						'id' => 'general',
						'label' => __( 'General', 'jupiterx-core' ),
					],
					'modules' => [
						'id' => 'modules',
						'label' => __( 'Modules', 'jupiterx-core' ),
					],
					'post-types' => [
						'id' => 'post-types',
						'label' => __( 'Custom Post Types', 'jupiterx-core' ),
					],
					'white-label' => [
						'id' => 'white-label',
						'label' => __( 'White Label', 'jupiterx-core' ),
					],
					'woocommerce' => [
						'id' => 'woocommerce',
						'label' => __( 'WooCommerce', 'jupiterx-core' ),
					],
					'third-party-integration' => [
						'id' => 'third-party-integration',
						'label' => __( 'Third-Party Integration', 'jupiterx-core' ),
					],
					'tracking-codes' => [
						'id' => 'tracking-codes',
						'label' => __( 'Tracking Codes', 'jupiterx-core' ),
					],
					'image-sizes' => [
						'id' => 'image-sizes',
						'label' => __( 'Image Sizes', 'jupiterx-core' ),
					],
				],
			],
			'maintenance' => [
				'id' => 'maintenance',
				'href' => '/maintenance',
				'label' => __( 'Maintenance', 'jupiterx-core' ),
				'help' => 'https://help.jupiterx.com/',
				'whiteLabel' => true,
				'subMenu' => true,
				'whiteLabelEnabled' => $this->is_white_label( 'maintenance' ),
				'subTabs' => [
					'ready-made-websites' => [
						'id' => 'ready-made-websites',
						'label' => __( 'Readymade Websites', 'jupiterx-core' ),
						'whiteLabel' => true,
						'whiteLabelEnabled' => $this->is_white_label( 'ready-made-websites' ),
					],
					'updates' => [
						'id' => 'updates',
						'label' => __( 'Updates', 'jupiterx-core' ),
						'whiteLabel' => false,
					],
					'version-rollback' => [
						'id' => 'version-rollback',
						'label' => __( 'Version Rollback', 'jupiterx-core' ),
						'whiteLabel' => false,
					],
					'logs' => [
						'id' => 'logs',
						'label' => __( 'Logs', 'jupiterx-core' ),
						'whiteLabel' => false,
					],
					'export' => [
						'id' => 'export',
						'label' => __( 'Export', 'jupiterx-core' ),
						'whiteLabel' => false,
					],
				],
			],
			'free-vs-pro' => [
				'id' => 'freeVsPro',
				'href' => '/free-vs-pro',
				'label' => __( 'Free Vs Pro', 'jupiterx-core' ),
				'whiteLabel' => false,
				'subMenu' => false,
			],
		];

		// Hide Site Health for WP under 5.2.
		if ( version_compare( get_bloginfo( 'version' ), '5.2', '<' ) ) {
			unset( $tabs['site-health'] );
		}

		// Hide Elementor for now.
		unset( $tabs['elementor'] );

		// Hide Tools > Export if constant is not defined.
		if ( ! $this->show_tab( 'JUPITERX_CONTROL_PANEL_EXPORT_IMPORT' ) ) {
			unset( $tabs['maintenance']['subTabs']['export'] );
		}

		// Hide Free Vs Pro on premium theme.
		if ( jupiterx_is_premium() ) {
			unset( $tabs['free-vs-pro'] );
		}

		// Hide settings > third party integration if constant is not defined.
		if ( ! is_plugin_active( 'jupiter-donut/jupiter-donut.php' ) ) {
			unset( $tabs['settings']['subTabs']['third-party-integration'] );
		}

		if ( ! function_exists( 'WC' ) ) {
			unset( $tabs['settings']['subTabs']['woocommerce'] );
		}

		if ( ! jupiterx_is_premium() ) {
			unset( $tabs['layout-builder'] );
			unset( $tabs['custom-snippets'] );
			unset( $tabs['free-vs-pro'] );
		}

		if ( defined( 'JUPITERX_CONTROL_PANEL_SETTINGS' ) && ! JUPITERX_CONTROL_PANEL_SETTINGS ) {
			unset( $tabs['settings'] );
		}

		return array_values( $tabs );
	}

	/**
	 * Get current theme data.
	 *
	 * @since 1.18.0
	 *
	 * @param string $data The theme data.
	 */
	private function get_theme_data( $data ) {
		$current_theme = wp_get_theme();

		return $current_theme->get( $data );
	}

	/**
	 * Check current screen.
	 *
	 * @since 1.18.0
	 *
	 * @return boolean Control panel screen.
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	private function is_current_screen() {
		$current_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		if ( 'jupiterx-setup-wizard' === $current_page ) {
			return true;
		}

		$page  = ! empty( $_GET['page'] ) ? htmlspecialchars( $_GET['page'] ) : ''; // phpcs:ignore
		$popup = ! empty( $_GET['post_type'] ) ? htmlspecialchars( $_GET['post_type'] ) : ''; // phpcs:ignore

		if ( ! is_admin() ) {
			return false;
		}

		if ( self::SCREEN_ID === $page || 'jupiterx-popups' === $popup ) {
			return true;
		}

		$post = ! empty( $_GET['post'] ) && ! is_array( $_GET['post'] ) ? htmlspecialchars( $_GET['post'] ) : ''; // phpcs:ignore

		if ( empty( self::$referrer ) ) {
			self::$referrer = $post;
		}

		$post_type = get_post_type( self::$referrer );

		if ( 'jupiterx-popups' === $post_type ) {
			return true;
		}

		if ( ! is_array( jupiterx_get_option( 'elements' ) ) ) {
			jupiterx_update_option( 'first_installation_after_250', true );
		}

		if (
			function_exists( 'jupiterx_get_option' ) &&
			'deleted' !== jupiterx_get_option( 'enable_widgets_reminder' ) &&
			time() > jupiterx_get_option( 'enable_widgets_reminder' ) &&
			! jupiterx_get_option( 'first_installation_after_250' )
		) {
			add_action( 'admin_footer', [ $this, 'enable_widget_root' ] );

			return true;
		}

		if ( 'show' === get_option( 'jupiterx_theme_update_modal', '' ) ) {
			add_action( 'admin_footer', [ $this, 'add_theme_update_modal' ] );

			return true;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.NonceVerification.NoNonceVerification
		return is_admin() && isset( $_GET['page'] ) && self::SCREEN_ID === $_GET['page'];
	}

	/**
	 * Get show tab.
	 *
	 * @param string $constant Constant name.
	 *
	 * @return boolean Tab show.
	 */
	private function show_tab( $constant ) {
		return defined( $constant ) && constant( $constant );
	}

	/**
	 * Get edit page url.
	 *
	 * @since 3.7.0
	 * @return string url to jupiterx-popups edit page.
	 */
	private function get_popup_url() {
		return add_query_arg(
			[
				'post_type' => 'jupiterx-popups',
			],
			admin_url( 'edit.php' )
		);
	}

	/**
	 * Get popup conditions.
	 *
	 * @since 3.7.0
	 */
	private function get_popup_conditions() {
		if ( ! class_exists( 'Elementor\Plugin' ) ) {
			return;
		}

		$post_type = ( new JupiterX_Popups() )->get_post_type_name();

		if ( 'jupiterx-popups' !== $post_type || ! class_exists( 'JupiterX_Popups_Conditions_Manager' ) ) {
			return;
		}

		return JupiterX_Popups_Conditions_Manager::$control_panel;
	}

	/**
	 * Get popup triggers.
	 *
	 * @since 3.7.0
	 */
	private function get_popup_triggers() {
		if ( ! class_exists( 'Elementor\Plugin' ) ) {
			return;
		}

		$post_type = ( new JupiterX_Popups() )->get_post_type_name();

		if ( 'jupiterx-popups' !== $post_type || ! class_exists( 'JupiterX_Popups_Triggers_Manager' ) ) {
			return;
		}

		return JupiterX_Popups_Triggers_Manager::$control_panel;
	}

	/**
	 * Check white label.
	 *
	 * @since 2.5.0
	 * @param string $tab tab name.
	 * @return boolean.
	 */
	private function is_white_label( $tab ) {
		if ( function_exists( 'jupiterx_is_white_label' ) && ! jupiterx_is_white_label() ) {
			return true;
		}

		$enabled_pages = jupiterx_get_option( 'white_label_cpanel_pages', [] );

		if ( ! in_array( $tab, $enabled_pages, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if Elementor top bar is active.
	 *
	 * @return bool Whether the top bar feature is active.
	 */
	private function check_elementor_top_bar() {
		// Ensure Elementor is loaded
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return false;
		}

		// Top bar was merged into core and enabled by default in v3.30+
		if ( version_compare( ELEMENTOR_VERSION, '3.30', '>=' ) ) {
			return true;
		}

		// For versions before 3.30, check if the experiment is active
		if ( isset( Elementor::$instance->experiments ) ) {
			$experiments = Elementor::$instance->experiments;

			// Check for top bar experiment
			if (
				method_exists( $experiments, 'is_feature_active' )
				&& $experiments->is_feature_active( 'editor_v2' )
			) {
				return true;
			}
		}

		return false;
	}
}

new JupiterX_Control_Panel_2();
