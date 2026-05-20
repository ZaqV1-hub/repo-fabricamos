<?php

/**
 * Plugin Name:             Social Chat
 * Description:             Social Chat allows your visitors to contact you or your team through WhatsApp with a single click.
 * Plugin URI:              https://quadlayers.com/products/whatsapp-chat/
 * Version:                 8.1.7
 * Text Domain:             wp-whatsapp-chat
 * Author:                  QuadLayers
 * Author URI:              https://quadlayers.com
 * License:                 GPLv3
 * Domain Path:             /languages
 * Request at least:        4.7
 * Tested up to:            6.9
 * Requires PHP:            5.6
 * WC requires at least:    4.0
 * WC tested up to:         10.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'QLWAPP_PLUGIN_NAME', 'Social Chat' );
define( 'QLWAPP_PLUGIN_VERSION', '8.1.7' );
define( 'QLWAPP_PLUGIN_FILE', __FILE__ );
define( 'QLWAPP_PLUGIN_DIR', __DIR__ . DIRECTORY_SEPARATOR );
define( 'QLWAPP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'QLWAPP_PREFIX', 'qlwapp' );
define( 'QLWAPP_DOMAIN', QLWAPP_PREFIX );
define( 'QLWAPP_LANDING_URL', 'https://quadlayers.com/landing/whatsapp-chat/?utm_source=qlwapp_plugin&utm_medium=whatsapp' );
define( 'QLWAPP_PHONE_NUMBER', '59895761512' );
define( 'QLWAPP_GROUP_LINK', 'https://chat.whatsapp.com/EQuPUtcPzEdIZVlT8JyyNw' );

/**
 * Load composer autoload
 */
require_once __DIR__ . '/vendor/autoload.php';
/**
 * Load plugin helpers
 */
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/compatibility/old.php';
require_once __DIR__ . '/compatibility/wordpress.php';
/**
 * Load vendor_packages packages
 */
require_once __DIR__ . '/vendor_packages/wp-i18n-map.php';
require_once __DIR__ . '/vendor_packages/wp-dashboard-widget-news.php';
require_once __DIR__ . '/vendor_packages/wp-plugin-table-links.php';
require_once __DIR__ . '/vendor_packages/wp-notice-plugin-promote.php';
require_once __DIR__ . '/vendor_packages/wp-plugin-install-tab.php';
require_once __DIR__ . '/vendor_packages/wp-plugin-feedback.php';

/**
 * Load plugin classes
 */
require_once __DIR__ . '/lib/class-plugin.php';

/**
 * Plugin activation hook
 */
register_activation_hook(
	__FILE__,
	function () {
		do_action( 'qlwapp_activation' );
	}
);

/**
 * Plugin activation hook
 */
register_deactivation_hook(
	__FILE__,
	function () {
		do_action( 'qlwapp_deactivation' );
	}
);

/**
 * Declarate compatibility with WooCommerce Custom Order Tables
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);
