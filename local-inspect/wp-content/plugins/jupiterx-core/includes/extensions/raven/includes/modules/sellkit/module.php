<?php
namespace JupiterX_Core\Raven\Modules\Sellkit;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Module_base;

class Module extends Module_Base {
	public function __construct() {
		parent::__construct();

		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'editor_enqueue_scripts' ], 1 );
	}

	/**
	 * Check if Woocommerce plugin is active.
	 *
	 * @return bool
	 */
	public static function is_active() {
		return function_exists( 'WC' );
	}

	public function get_widgets() {
		$widgets = [];

		if ( ! function_exists( 'sellkit' ) ) {
			$widgets = [
				'sellkit-checkout-preview',
				'sellkit-order-cart-details-preview',
				'sellkit-order-details-preview',
				'sellkit-personalised-coupons-preview',
				'sellkit-product-filter-preview',
			];
		}

		if ( function_exists( 'sellkit' ) && ! function_exists( 'sellkit_pro' ) ) {
			$widgets = [
				'sellkit-personalised-coupons-preview',
				'sellkit-product-filter-preview',
			];
		}

		return $widgets;
	}

	public function editor_enqueue_scripts() {
		wp_localize_script(
			'jupiterx-core-raven-editor',
			'hasSellkitPro',
			[ 'active' => function_exists( 'sellkit_pro' ) ]
		);
	}
}
