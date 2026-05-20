<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 * Plugin Name: AccelerateWP
 * Plugin URI: https://cloudlinux.com
 * Description: The best WordPress performance plugin.
 * Version: 3.19.0.1-1.1-27
 * Requires at least: 5.8
 * Requires PHP: 7.3
 * Code Name: Iego
 * Author: CloudLinux
 * Author URI: https://cloudlinux.com
 * Licence: GPLv2 or later
 *
 * Text Domain: rocket
 * Domain Path: /languages
 *
 * Copyright 2013-2025 WP Rocket
 */

defined( 'ABSPATH' ) || exit;

// Rocket defines.
define( 'WP_ROCKET_VERSION',               '3.19.0.1-1.1-27' );
define( 'WP_ROCKET_WP_VERSION',            '5.8' );
define( 'WP_ROCKET_WP_VERSION_TESTED',     '6.3.1' );
define( 'WP_ROCKET_PHP_VERSION',           '7.3' );
define( 'WP_ROCKET_PRIVATE_KEY',           false );
define( 'WP_ROCKET_SLUG',                  'wp_rocket_settings' );
define( 'WP_ROCKET_WEB_MAIN',              'https://cloudlinux.com/' );
define( 'WP_ROCKET_WEB_API',               WP_ROCKET_WEB_MAIN . 'api/clsop/' ); // only used in deprecated code.
define( 'WP_ROCKET_WEB_CHECK',             WP_ROCKET_WEB_MAIN . 'check_update.php' ); // only used in deprecated code.
define( 'WP_ROCKET_WEB_VALID',             WP_ROCKET_WEB_MAIN . 'valid_key.php' ); // only used in deprecated code.
define( 'WP_ROCKET_WEB_INFO',              WP_ROCKET_WEB_MAIN . 'plugin_information.php' ); // only used in deprecated code.
define( 'WP_ROCKET_FILE',                  __FILE__ );
define( 'WP_ROCKET_PATH',                  realpath( plugin_dir_path( WP_ROCKET_FILE ) ) . '/' );
define( 'WP_ROCKET_INC_PATH',              realpath( WP_ROCKET_PATH . 'inc/' ) . '/' );

require_once WP_ROCKET_INC_PATH . 'constants.php';

define( 'WP_ROCKET_DEPRECATED_PATH',       realpath( WP_ROCKET_INC_PATH . 'deprecated/' ) . '/' );
define( 'WP_ROCKET_FRONT_PATH',            realpath( WP_ROCKET_INC_PATH . 'front/' ) . '/' );
define( 'WP_ROCKET_ADMIN_PATH',            realpath( WP_ROCKET_INC_PATH . 'admin' ) . '/' );
define( 'WP_ROCKET_ADMIN_UI_PATH',         realpath( WP_ROCKET_ADMIN_PATH . 'ui' ) . '/' );
define( 'WP_ROCKET_ADMIN_UI_MODULES_PATH', realpath( WP_ROCKET_ADMIN_UI_PATH . 'modules' ) . '/' );
define( 'WP_ROCKET_COMMON_PATH',           realpath( WP_ROCKET_INC_PATH . 'common' ) . '/' );
define( 'WP_ROCKET_FUNCTIONS_PATH',        realpath( WP_ROCKET_INC_PATH . 'functions' ) . '/' );
define( 'WP_ROCKET_VENDORS_PATH',          realpath( WP_ROCKET_INC_PATH . 'vendors' ) . '/' );
define( 'WP_ROCKET_3RD_PARTY_PATH',        realpath( WP_ROCKET_INC_PATH . '3rd-party' ) . '/' );
if ( ! defined( 'WP_ROCKET_CONFIG_PATH' ) ) {
	define( 'WP_ROCKET_CONFIG_PATH',       WP_CONTENT_DIR . '/wp-rocket-config/' );
}
define( 'WP_ROCKET_URL',                   plugin_dir_url( WP_ROCKET_FILE ) );
define( 'WP_ROCKET_INC_URL',               WP_ROCKET_URL . 'inc/' );
define( 'WP_ROCKET_ADMIN_URL',             WP_ROCKET_INC_URL . 'admin/' );
define( 'WP_ROCKET_ASSETS_URL',            WP_ROCKET_URL . 'assets/' );
define( 'WP_ROCKET_ASSETS_PATH',            WP_ROCKET_PATH . 'assets/' );
define( 'WP_ROCKET_ASSETS_JS_URL',         WP_ROCKET_ASSETS_URL . 'js/' );
define( 'WP_ROCKET_ASSETS_JS_PATH',         WP_ROCKET_ASSETS_PATH . 'js/' );
define( 'WP_ROCKET_ASSETS_CSS_URL',        WP_ROCKET_ASSETS_URL . 'css/' );
define( 'WP_ROCKET_ASSETS_IMG_URL',        WP_ROCKET_ASSETS_URL . 'img/' );

if ( ! defined( 'WP_ROCKET_CACHE_ROOT_PATH' ) ) {
	define( 'WP_ROCKET_CACHE_ROOT_PATH', WP_CONTENT_DIR . '/cache/' );
}
define( 'WP_ROCKET_CACHE_PATH',         WP_ROCKET_CACHE_ROOT_PATH . 'wp-rocket/' );
define( 'WP_ROCKET_MINIFY_CACHE_PATH',  WP_ROCKET_CACHE_ROOT_PATH . 'min/' );
define( 'WP_ROCKET_CACHE_BUSTING_PATH', WP_ROCKET_CACHE_ROOT_PATH . 'busting/' );
define( 'WP_ROCKET_CRITICAL_CSS_PATH',  WP_ROCKET_CACHE_ROOT_PATH . 'critical-css/' );

define( 'WP_ROCKET_USED_CSS_PATH',  WP_ROCKET_CACHE_ROOT_PATH . 'used-css/' );

if ( ! defined( 'WP_ROCKET_CACHE_ROOT_URL' ) ) {
	define( 'WP_ROCKET_CACHE_ROOT_URL', WP_CONTENT_URL . '/cache/' );
}
define( 'WP_ROCKET_CACHE_URL',         WP_ROCKET_CACHE_ROOT_URL . 'wp-rocket/' );
define( 'WP_ROCKET_MINIFY_CACHE_URL',  WP_ROCKET_CACHE_ROOT_URL . 'min/' );
define( 'WP_ROCKET_CACHE_BUSTING_URL', WP_ROCKET_CACHE_ROOT_URL . 'busting/' );

define( 'WP_ROCKET_USED_CSS_URL', WP_ROCKET_CACHE_ROOT_URL . 'used-css/' );

if ( ! defined( 'CHMOD_WP_ROCKET_CACHE_DIRS' ) ) {
	define( 'CHMOD_WP_ROCKET_CACHE_DIRS', 0755 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
}
if ( ! defined( 'WP_ROCKET_LASTVERSION' ) ) {
	define( 'WP_ROCKET_LASTVERSION', '3.18.3' );
}

// CloudLinux defines.
if ( ! defined( 'WP_ROCKET_UPDATE_PATH' ) ) {
	define( 'WP_ROCKET_UPDATE_PATH', '/opt/cloudlinux-site-optimization-module' );
}

if ( ! defined( 'WP_ROCKET_IMAGE_OPTIMIZATION_BACKUP_PATH' ) ) {
	define( 'WP_ROCKET_IMAGE_OPTIMIZATION_BACKUP_PATH', WP_CONTENT_DIR . '/accelerate-wp/images/backup/' );
}

if ( ! defined( 'WP_ROCKET_IMAGE_OPTIMIZATION_DOWNLOAD_PATH' ) ) {
	define( 'WP_ROCKET_IMAGE_OPTIMIZATION_DOWNLOAD_PATH', WP_CONTENT_DIR . '/accelerate-wp/images/temp/' );
}

if ( ! defined( 'WP_ROCKET_SAAS_KEY' ) ) {
	define( 'WP_ROCKET_SAAS_KEY', 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAu1SU1LfVLPHCozMxH2Mo' );
}

/**
 * We use is_readable() with @ silencing as WP_Filesystem() can use different methods to access the filesystem.
 *
 * This is more performant and more compatible. It allows us to work around file permissions and missing credentials.
 */
if ( @is_readable( WP_ROCKET_PATH . 'licence-data.php' ) ) { //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	@include WP_ROCKET_PATH . 'licence-data.php'; //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
}

require WP_ROCKET_INC_PATH . 'compat.php';
require WP_ROCKET_INC_PATH . 'classes/class-wp-rocket-requirements-check.php';

/**
 * Loads WP Rocket translations
 *
 * @since 3.0
 * @author Remy Perona
 *
 * @return void
 */
function rocket_load_textdomain() {
	load_plugin_textdomain( 'rocket', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'rocket_load_textdomain' );

$wp_rocket_requirement_checks = new WP_Rocket_Requirements_Check(
	[
		'plugin_name'         => 'AccelerateWP',
		'plugin_file'         => WP_ROCKET_FILE,
		'plugin_version'      => WP_ROCKET_VERSION,
		'plugin_last_version' => WP_ROCKET_LASTVERSION,
		'wp_version'          => WP_ROCKET_WP_VERSION,
		'php_version'         => WP_ROCKET_PHP_VERSION,
	]
);

if ( $wp_rocket_requirement_checks->check() ) {
	require WP_ROCKET_INC_PATH . 'main.php';
}

unset( $wp_rocket_requirement_checks );

/**
 * Deactivate AccelerateWP if there are incompatibilities
 *
 * @param string $action The nonce action.
 *
 * @return bool
 *
 * @note CL.
 * @since 3.11.4
 */
function clsop_deactivate_with_incompatibilities( $action ) {
	if ( 'activate-plugin_wp-rocket/wp-rocket.php' === $action ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		// @codingStandardsIgnoreLine
		$query_string = ! empty( $_SERVER['QUERY_STRING'] ) ? wp_unslash( $_SERVER['QUERY_STRING'] ) : '';
		wp_safe_redirect( self_admin_url( 'plugins.php?' . $query_string ) );
		return true;
	}

	return false;
}

/**
 * Check admin referer.
 *
 * @note CL.
 * @param string    $action The nonce action.
 * @param false|int $result Result.
 *
 * @since 3.11.4
 */
function rocket_check_admin_referer( $action, $result ) { // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$redirect = clsop_deactivate_with_incompatibilities( $action );
	if ( $redirect ) {
		exit;
	}
}
add_action( 'check_admin_referer', 'rocket_check_admin_referer', 1, 2 );

/**
 * Update .htaccess when server software changed.
 *
 * @note CL.
 * @since 3.11.5.1
 */
function clsop_update_htaccess_when_server_changed() {
	try {
		$last_server_software       = get_option( 'clsop_last_server_software' );
		$last_server_software_extra = get_option( 'clsop_last_server_software_extra' );

		$server_software       = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';
		$server_software_extra = isset( $_SERVER['SERVER_SOFTWARE_EXTRA'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE_EXTRA'] ) ) : '';

		$same_by_extra  = ( clsop_is_apache2nginx() && 'CloudLinux MAx Webserver' === $last_server_software_extra );
		$server_changed = ( $last_server_software && $server_software && false === strpos( $last_server_software, $server_software ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		if ( ( ! empty( $server_software_extra ) && ! $same_by_extra ) || ( empty( $server_software_extra ) && $server_changed ) ) {
			// Regenerate the config file.
			rocket_generate_config_file();

			// Regenerate .htaccess file.
			flush_rocket_htaccess();

			// Purge homepage cache.
			rocket_clean_home();
		}

		if ( ! $last_server_software || ! $server_software || false === strpos( $last_server_software, $server_software ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			update_option( 'clsop_last_server_software', $server_software, true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		}
		if ( ! $last_server_software_extra || ! $server_software_extra || false === strpos( $last_server_software_extra, $server_software_extra ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			update_option( 'clsop_last_server_software_extra', $server_software_extra, true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		}
	} catch ( Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		// Catch exceptions and remain silent.
	}
}
add_action( 'init', 'clsop_update_htaccess_when_server_changed' );

/**
 * Cli commands.
 *
 * @note CL.
 * @since 3.12.0.5
 */
try {
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::add_command( 'accelerate-wp', WP_Rocket\Cli\AccelerateWp::class );
	}
} catch ( \Exception $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
	// Doesn't support CLI.
}
