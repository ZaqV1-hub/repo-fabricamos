<?php

/**
 * JupiterX WPML Conditions.
 *
 * @since 4.9.2
 */
class Jupiterx_WPML_Condition {

	/**
	 * Check WPML conditions if match current WordPress page.
	 *
	 * @param array $condition The condition array.
	 * @param int $post_id The template post ID.
	 * @return boolean
	 */
	public function sub_condition( $condition, $post_id = null ) {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return false;
		}

		// Get the template type to check context
		$template_type = get_post_meta( $post_id, 'jx-layout-type', true );

		// If no template type is set, try to get it from elementor template type
		if ( empty( $template_type ) ) {
			$template_type = get_post_meta( $post_id, '_elementor_template_type', true );
		}

		// Check if the current page context matches the template type
		if ( ! $this->is_context_match( $template_type ) ) {
			return false;
		}

		$current_language = apply_filters( 'wpml_current_language', null );

		if ( isset( $condition[2][0] ) && 'all' === $condition[2][0] ) {
			return true;
		}

		if ( isset( $condition[2][0] ) && $condition[2][0] === $current_language ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if the current page context matches the template type.
	 *
	 * @param string $template_type The template type.
	 * @return boolean
	 */
	private function is_context_match( $template_type ) {
		// For general templates (header, footer, page-title-bar), always allow
		$general_templates = [ 'header', 'footer', 'page-title-bar' ];
		if ( in_array( $template_type, $general_templates, true ) ) {
			return true;
		}

		// For single templates, check if we're on a singular page (but not product)
		if ( 'single' === $template_type ) {
			return is_singular() && ! is_product();
		}

		// For archive templates, check if we're on an archive page (but not WooCommerce)
		if ( 'archive' === $template_type ) {
			return is_archive() && ! is_woocommerce();
		}

		// For product templates, check if we're on a single product page
		if ( 'product' === $template_type ) {
			return is_product();
		}

		// For product-archive templates, check if we're on a WooCommerce archive
		if ( 'product-archive' === $template_type ) {
			return is_woocommerce() && ! is_product();
		}

		// For other template types, allow by default
		return true;
	}
}
