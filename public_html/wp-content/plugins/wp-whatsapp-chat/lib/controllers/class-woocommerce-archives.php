<?php

namespace QuadLayers\QLWAPP\Controllers;

use QuadLayers\QLWAPP\Models\WooCommerce_Archives as Models_WooCommerceArchives;
use QuadLayers\QLWAPP\Models\Box as Models_Box;
use QuadLayers\QLWAPP\Models\Display as Models_Display;
use QuadLayers\QLWAPP\Models\Scheme as Models_Scheme;

class WooCommerce_Archives {

	protected static $instance;

	private function __construct() {
		add_action( 'wp', array( $this, 'woocommerce_archives_init' ) );
	}

	public function woocommerce_archives_init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$woocommerce_archives = Models_WooCommerceArchives::instance()->get();
		$position             = (string) $woocommerce_archives['position'];
		$position_priority    = (int) $woocommerce_archives['position_priority'];
		$devices              = (string) $woocommerce_archives['devices'];

		if ( 'none' === $position || 'hide' === $devices ) {
			return;
		}

		// Check if we should show in shop page
		if ( 'yes' === $woocommerce_archives['show_in_shop'] && is_shop() ) {
			add_action( $position, array( $this, 'archive_button' ), $position_priority );
			return;
		}

		// Check if we should show in category archive
		if ( 'yes' === $woocommerce_archives['show_in_category'] && is_product_category() ) {
			add_action( $position, array( $this, 'archive_button' ), $position_priority );
			return;
		}

		// Check if we should show in tag archive
		if ( 'yes' === $woocommerce_archives['show_in_tag'] && is_product_tag() ) {
			add_action( $position, array( $this, 'archive_button' ), $position_priority );
			return;
		}

		// Check if we should show in brand archive (for plugins that support it)
		if ( 'yes' === $woocommerce_archives['show_in_brand'] && ( is_tax( 'product_brand' ) || ( function_exists( 'is_product_brand' ) && is_product_brand() ) ) ) {
			add_action( $position, array( $this, 'archive_button' ), $position_priority );
			return;
		}
	}

	public function archive_button() {

		$button  = Models_WooCommerceArchives::instance()->get();
		$display = Models_Display::instance()->get();
		$box     = Models_Box::instance()->get();
		$scheme  = Models_Scheme::instance()->get();

		$style = Frontend::get_scheme_css_properties( $scheme );

		$display = htmlentities( wp_json_encode( $display ), ENT_QUOTES, 'UTF-8' );
		$button  = htmlentities( wp_json_encode( $button ), ENT_QUOTES, 'UTF-8' );
		$box     = htmlentities( wp_json_encode( $box ), ENT_QUOTES, 'UTF-8' );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="qlwapp qlwapp--woocommerce" style="' . $style . '" data-display="' . $display . '" data-button="' . $button . '" data-box="' . $box . '"></div>';
	}

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
