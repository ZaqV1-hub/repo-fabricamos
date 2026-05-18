<?php

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'clsop_is_apache2nginx' ) ) {
	/**
	 * Detect it is apache2nginx web server
	 *
	 * @note CL.
	 * @return bool
	 */
	function clsop_is_apache2nginx() {
		$result = false;
		if (
			isset( $_SERVER['SERVER_SOFTWARE_EXTRA'] )
			&&
			strpos( $_SERVER['SERVER_SOFTWARE_EXTRA'], 'CloudLinux MAx Webserver' ) !== false // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		) {
			$result = true;
		}

		return apply_filters( 'rocket_clsop_is_apache2nginx', $result );
	}
}

if ( ! function_exists( 'clsop_is_litespeed' ) ) {
	/**
	 * Detect it is LiteSpeed web server
	 *
	 * @note CL.
	 * @return bool
	 */
	function clsop_is_litespeed() {
		$result = false;
		if (
			isset( $_SERVER['SERVER_SOFTWARE'] )
			&&
			strpos( $_SERVER['SERVER_SOFTWARE'], 'LiteSpeed' ) !== false // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		) {
			$result = true;
		}

		return apply_filters( 'rocket_clsop_is_litespeed', $result );
	}
}
