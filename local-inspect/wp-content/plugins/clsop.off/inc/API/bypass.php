<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */

defined( 'ABSPATH' ) || exit;

/**
 * Indicate to bypass rocket optimizations.
 *
 * Checks for "noclsop" query string in the url to bypass rocket processes.
 *
 * @since 3.7
 *
 * @return bool True to indicate should bypass; false otherwise.
 */
function rocket_bypass() {
	static $bypass = null;

	if ( rocket_get_constant( 'WP_ROCKET_IS_TESTING', false ) ) {
		$bypass = null;
	}

	if ( ! is_null( $bypass ) ) {
		return $bypass;
	}

	$bypass = isset( $_GET['noclsop'] ) && 0 !== $_GET['noclsop']; // phpcs:ignore WordPress.Security.NonceVerification

	return $bypass;
}
