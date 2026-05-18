<?php
/**
 * Prepare and initialize the Jupiter framework.
 *
 * @package JupiterX\Framework
 *
 * @since   1.0.0
 */

add_action( 'jupiterx_init', 'jupiterx_define_constants', -1 );
/**
 * Define constants.
 *
 * @since 1.0.0
 * @ignore
 *
 * @return void
 */
function jupiterx_define_constants() {
	$theme_data = get_file_data( get_template_directory() . '/style.css', [ 'Version' ], 'jupiterx' );

	// Define premium.
	if ( ! defined( 'JUPITERX_PREMIUM' ) ) {
		define( 'JUPITERX_PREMIUM', true );
	}

	// Define version.
	define( 'JUPITERX_VERSION', array_shift( $theme_data ) );
	define( 'JUPITERX_INITIAL_FREE_VERSION', '1.3.0' );
	define( 'JUPITERX_NAME', 'Jupiter X' );
	define( 'JUPITERX_SLUG', 'jupiterx' );

	// Define paths.
	if ( ! defined( 'JUPITERX_THEME_PATH' ) ) {
		define( 'JUPITERX_THEME_PATH', wp_normalize_path( trailingslashit( get_template_directory() ) ) );
	}

	define( 'JUPITERX_PATH', JUPITERX_THEME_PATH . 'lib/' );
	define( 'JUPITERX_API_PATH', JUPITERX_PATH . 'api/' );
	define( 'JUPITERX_ASSETS_PATH', JUPITERX_PATH . 'assets/' );
	define( 'JUPITERX_LANGUAGES_PATH', JUPITERX_PATH . 'languages/' );
	define( 'JUPITERX_RENDER_PATH', JUPITERX_PATH . 'render/' );
	define( 'JUPITERX_TEMPLATES_PATH', JUPITERX_PATH . 'templates/' );
	define( 'JUPITERX_STRUCTURE_PATH', JUPITERX_TEMPLATES_PATH . 'structure/' );
	define( 'JUPITERX_FRAGMENTS_PATH', JUPITERX_TEMPLATES_PATH . 'fragments/' );

	// Define urls.
	if ( ! defined( 'JUPITERX_THEME_URL' ) ) {
		define( 'JUPITERX_THEME_URL', trailingslashit( get_template_directory_uri() ) );
	}

	define( 'JUPITERX_URL', JUPITERX_THEME_URL . 'lib/' );
	define( 'JUPITERX_API_URL', JUPITERX_URL . 'api/' );
	define( 'JUPITERX_ASSETS_URL', JUPITERX_URL . 'assets/' );
	define( 'JUPITERX_LESS_URL', JUPITERX_ASSETS_URL . 'less/' );
	define( 'JUPITERX_JS_URL', JUPITERX_ASSETS_URL . 'js/' );
	define( 'JUPITERX_IMAGE_URL', JUPITERX_ASSETS_URL . 'images/' );

	// Define admin paths.
	define( 'JUPITERX_ADMIN_PATH', JUPITERX_PATH . 'admin/' );

	// Define admin url.
	define( 'JUPITERX_ADMIN_URL', JUPITERX_URL . 'admin/' );
	define( 'JUPITERX_ADMIN_ASSETS_URL', JUPITERX_ADMIN_URL . 'assets/' );
	define( 'JUPITERX_ADMIN_JS_URL', JUPITERX_ADMIN_ASSETS_URL . 'js/' );

	// Define helpers.
	define( 'JUPITERX_IMAGE_SIZE_OPTION', JUPITERX_SLUG . '_image_sizes' );
}

add_action( 'jupiterx_init', 'jupiterx_load_dependencies', 5 );
/**
 * Load dependencies.
 *
 * @since 1.0.0
 * @ignore
 *
 * @return void
 */
function jupiterx_load_dependencies() {
	require_once JUPITERX_API_PATH . 'init.php';

	/**
	 * Fires before Jupiter API loads.
	 *
	 * @since 1.0.0
	 */
	do_action( 'jupiterx_before_load_api' );

	$components = [
		'api',
		'compatibility',
		'actions',
		'html',
		'post-meta',
		'image',
		'fonts',
		'custom-fields',
		'template',
		'layout',
		'header',
		'menu',
		'widget',
		'footer',
		'onboarding',
	];

	if ( ! jupiterx_check_default() ) {
		$components[] = 'customizer';
	}

	if ( class_exists( 'Elementor\Plugin' ) ) {
		$components[] = 'elementor';
	}

	if ( class_exists( 'woocommerce' ) ) {
		$components[] = 'woocommerce';
	}

	if ( class_exists( 'Rocket_Lazyload_Requirements_Check' ) || class_exists( 'WP_Rocket_Requirements_Check' ) ) {
		$components[] = 'lazy-load';
	}

	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$components[] = 'events-calendar';
	}

	// Load the necessary Jupiter components.
	jupiterx_load_api_components( $components );

	// Add third party styles and scripts compiler support.
	jupiterx_add_api_component_support( 'wp_styles_compiler' );
	jupiterx_add_api_component_support( 'wp_scripts_compiler' );

	/**
	 * Fires after Jupiter API loads.
	 *
	 * @since 1.0.0
	 */
	do_action( 'jupiterx_after_load_api' );
}

add_action( 'jupiterx_init', 'jupiterx_add_theme_support' );
/**
 * Add theme support.
 *
 * @since 1.0.0
 * @ignore
 *
 * @return void
 */
function jupiterx_add_theme_support() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

	// Gutenberg.
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );

	// Jupiter specific.
	add_theme_support( 'jupiterx-default-styling' );

	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'woocommerce' );

	jupiterx_register_image_sizes();

	if ( jupiterx_check_default() ) {
		add_theme_support(
			'custom-logo',
			[
				'height'      => 100,
				'width'       => 350,
				'flex-height' => true,
				'flex-width'  => true,
			]
		);
	}
}

add_action( 'jupiterx_get_sellkit_link_schedule_hook', 'jupiterx_get_sellkit_link_schedule' );

/**
 * Schedule event for getting sellkit pro download link.
 *
 * @param array $body array of necessary values for api.
 * @since 2.0.6
 */
function jupiterx_get_sellkit_link_schedule( $body ) {
	if ( empty( $body ) ) {
		return;
	}

	$link = jupiterx_get_sellkit_download_link( $body );

	if ( empty( $link ) ) {
		return;
	}

	set_transient( 'jupiterx_sellkit_pro_link', $link, 6 * HOUR_IN_SECONDS );
}

add_action( 'jupiterx_init', 'jupiterx_includes' );
/**
 * Include framework files.
 *
 * @since 1.0.0
 * @ignore
 *
 * @return void
 */
function jupiterx_includes() {
	if ( version_compare( JUPITERX_VERSION, '2.0.0', '>=' ) ) {
		define( 'SELLKIT_BUNDLED', true );
		define( 'SELLKIT_PARTNER_ID', 'prtid_KKmtzMGZ23GwUS' );
	}

	// Include admin.
	if ( is_admin() ) {
		require_once JUPITERX_ADMIN_PATH . 'tgmpa/class-tgm-plugin-activation.php';
		require_once JUPITERX_ADMIN_PATH . 'tgmpa/functions.php';
		require_once JUPITERX_ADMIN_PATH . 'assets.php';
		require_once JUPITERX_ADMIN_PATH . 'core-install/core-install.php';
		require_once JUPITERX_ADMIN_PATH . 'functions.php';
		require_once JUPITERX_ADMIN_PATH . 'license-manager.php';
		require_once JUPITERX_ADMIN_PATH . 'update-plugins/class-update-plugins.php';
		require_once JUPITERX_ADMIN_PATH . 'update-plugins/functions.php';
		require_once JUPITERX_ADMIN_PATH . 'update-theme/class-update-theme.php';
		require_once JUPITERX_ADMIN_PATH . 'notices/class-sellkit-notices.php';
		require_once JUPITERX_ADMIN_PATH . 'notices/feedback-notification-bar.php';
	}

	// Include assets.
	require_once JUPITERX_ASSETS_PATH . 'assets.php';

	// Include renderers.
	require_once JUPITERX_RENDER_PATH . 'template-parts.php';
	require_once JUPITERX_RENDER_PATH . 'fragments.php';
	require_once JUPITERX_RENDER_PATH . 'widget-area.php';
	require_once JUPITERX_RENDER_PATH . 'walker.php';
	require_once JUPITERX_RENDER_PATH . 'menu.php';
}

/**
 * Get download link for sellkit pro.
 *
 * @param array $body array of necessary values for api.
 * @since 2.0.6
 * @return string.
 */
function jupiterx_get_sellkit_download_link( $body ) {
	$response = wp_remote_get( 'https://my.getsellkit.com/wp-json/sellkit/v1/bundled/sellkit_pro/latest', [
		'timeout' => 10,
		'body' => $body,
	] );

	$response_code = wp_remote_retrieve_response_code( $response );

	if ( is_wp_error( $response ) || ( empty( $response['body'] ) && 200 !== (int) $response_code ) ) {
		return;
	}

	$sellkit_repo = str_replace( '"', '', stripslashes( $response['body'] ) );

	set_transient( 'jupiterx_sellkit_pro_link', $sellkit_repo, 6 * HOUR_IN_SECONDS );

	return $sellkit_repo;
}
/**
 * Handles url redirects
 *
 * @since 1.0.0
 * @ignore
 *
 * @return void
 */
function jupiterx_handle_url_redirects() {
	// @codingStandardsIgnoreStart
	if ( empty( $_GET['page'] ) ) {
		return;
	}

	if ( 'customize_theme' === $_GET['page'] ) {
		wp_redirect( admin_url( '/customize.php' ) );
		wp_die();
	}
	// @codingStandardsIgnoreEnd
}

add_action( 'jupiterx_init', 'jupiterx_load_textdomain' );
/**
 * Load text domain.
 *
 * @since 1.0.0
 * @ignore
 *
 * @return void
 */
function jupiterx_load_textdomain() {
	load_theme_textdomain( 'jupiterx', JUPITERX_LANGUAGES_PATH );
}
add_action( 'jupiterx_before_init', 'jupiterx_load_pro', -1 );
/**
 * Load pro functions.
 *
 * @since 1.6.0
 *
 * @return void
 */
function jupiterx_load_pro() {
	require_once trailingslashit( get_template_directory() ) . 'lib/pro/pro.php';
}

/**
 * Check setup wizard is enabled.
 *
 * @since 4.0.0
 */
function jupiterx_check_setup_wizard() {
	if ( method_exists( 'JupiterX_Core', 'jupiterx_check_setup_wizard' ) ) {
		return jupiterx_core()->jupiterx_check_setup_wizard();
	}

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

	$jupiterx_core_version = '';

	if ( function_exists( 'jupiterx_core' ) ) {
		$jupiterx_core_version = jupiterx_core()->version();
	}

	$fresh_install = get_option( 'jupiterx_fresh_install', false );

	if ( $fresh_install && ( '' === $jupiterx_core_version || version_compare( $jupiterx_core_version, '4.0.0', '>=' ) ) ) {
		update_option( 'jupiterx_setup_wizard_hide', strtotime( '+14 days', time() ) );

		return true;
	}

	return false;
}

/**
 * Check default settings are enabled.
 *
 * @since 3.8.0
 */
function jupiterx_check_default() {
	if ( method_exists( 'JupiterX_Core', 'check_default_settings' ) ) {
		return jupiterx_core()->check_default_settings();
	}

	$plugin_data = [];

	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	if ( is_plugin_active( 'jupiterx-core/jupiterx-core.php' ) ) {
		$jupiterx_core_path = WP_PLUGIN_DIR . '/jupiterx-core/jupiterx-core.php';
		$plugin_data        = get_file_data( $jupiterx_core_path, [ 'Version' ], 'jupiterx-core' );
	}

	if (
		isset( $plugin_data[0] ) &&
		version_compare( $plugin_data[0], '3.8.0', '<' )
	) {
		return false;
	}

	$fresh_install = get_option( 'jupiterx_first_installation', false );

	if ( $fresh_install ) {
		jupiterx_update_option( 'disable_theme_default_settings', true );
		delete_option( 'jupiterx_first_installation' );

		return true;
	}

	return false;
}

/**
 * Check if page title bar is disabled.
 *
 * @since 4.4.0
 */
function disable_page_title_bar() {
	if ( method_exists( 'JupiterX_Core', 'disable_page_title_bar' ) ) {
		return jupiterx_core()->disable_page_title_bar();
	}

	return false;
}

/**
 * Get layouts.
 *
 * @since 3.8.0
 * @param string $choices The initial option.
 * @return array List of layouts.
 */
function jupiterx_default_get_layouts( $choices = [] ) {
	$right   = is_rtl() ? esc_html__( 'Left', 'jupiterx' ) : esc_html__( 'Right', 'jupiterx' );
	$left    = is_rtl() ? esc_html__( 'Right', 'jupiterx' ) : esc_html__( 'Left', 'jupiterx' );
	$choices = $choices;

	$choices = array_merge( $choices, [
		'c'       => esc_html__( 'No sidebar', 'jupiterx' ),
		/* translators: The sidebar position */
		'sp_c'    => sprintf( esc_html__( 'Single Sidebar %s', 'jupiterx' ), $left ),
		/* translators: The sidebar position */
		'c_sp'    => sprintf( esc_html__( 'Single Sidebar %s', 'jupiterx' ), $right ),
		/* translators: The sidebar position */
		'sp_ss_c' => sprintf( esc_html__( 'Double Sidebar %s', 'jupiterx' ), $left ),
		/* translators: The sidebar position */
		'c_sp_ss' => sprintf( esc_html__( 'Double Sidebar %s', 'jupiterx' ), $right ),
		'sp_c_ss' => esc_html__( 'Opposing Sidebars', 'jupiterx' ),
	] );

	return $choices;
}

/**
 * Fires before Jupiter loads.
 *
 * @since 1.0.0
 */
do_action( 'jupiterx_before_init' );

	/**
	 * Load Jupiter framework.
	 *
	 * @since 1.0.0
	 */
	do_action( 'jupiterx_init' );

/**
 * Fires after Jupiter loads.
 *
 * @since 1.0.0
 */
do_action( 'jupiterx_after_init' );
