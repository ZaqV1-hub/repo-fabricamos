<?php
/**
 * Plugin Name: Fabricamos Native
 * Description: Implementacao nativa do fluxo Fabricamos para comprador e fabricante.
 * Version: 1.0.0
 * Author: OpenAI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/includes/class-fabricamos-native.php';

register_activation_hook( __FILE__, array( 'Fabricamos_Native', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Fabricamos_Native', 'deactivate' ) );

Fabricamos_Native::instance();
