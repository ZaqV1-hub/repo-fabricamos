<?php

error_reporting( 0 ); // phpcs:ignore
ini_set( 'display_errors', 0 ); // phpcs:ignore

// Disable wp-cron to prevent cron tasks from executing from this script.
define( 'DISABLE_WP_CRON', true );

require_once '../../../wp-load.php';

if ( ! function_exists( 'rocket_clean_domain' ) ) {
	$clsop_response = [
		'success' => false,
		'message' => 'The plugin AccelerateWP seems not enabled on this site.',
	];
	die( wp_json_encode( $clsop_response ) );
}

if ( rocket_clean_domain() ) {
	$clsop_response = [
		'success' => true,
		'message' => 'Cache cleared.',
	];
} else {
	$clsop_response = [
		'success' => false,
		'message' => 'Something went wrong.',
	];
}

echo wp_json_encode( $clsop_response );
