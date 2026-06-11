<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 * Compatibility with Premium SEO Pack
 *
 * @link http://premiumseopack.com
 */

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'psp' ) ) {

	/**
	 * Dequeue the stylesheet of Premium SEO Pack on WP Rocket settings page.
	 *
	 * @since 2.11.6
	 * @author Arun Basil Lal
	 */
	function rocket_dequeue_premium_seo_pack_stylesheet() {

		// Return on all pages but WP Rocket settings page.
		$screen = get_current_screen();
		if ( 'settings_page_clsop' !== $screen->id ) {
			return;
		}

		// Dequeueing this stylesheet unfreezes WP Rocket.
		wp_dequeue_style( 'psp-main-style' );
	}
	add_action( 'admin_print_styles', 'rocket_dequeue_premium_seo_pack_stylesheet', 11 );
}
