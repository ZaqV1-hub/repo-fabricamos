<?php

namespace QuadLayers\QLWAPP\Api\Rest\Admin_Menu\WooCommerce_Archives;

use QuadLayers\QLWAPP\Api\Rest\Admin_Menu\Base;
use QuadLayers\QLWAPP\Models\WooCommerce_Archives as Models_WooCommerceArchives;

class Get extends Base {
	protected static $route_path = 'woocommerce_archives';

	public function callback( \WP_REST_Request $request ) {
		try {
			$models_woocommerce_archives = Models_WooCommerceArchives::instance();

			$woocommerce_archives = $models_woocommerce_archives->get();

			return $this->handle_response( $woocommerce_archives );
		} catch ( \Throwable $error ) {
			$response = array(
				'code'    => $error->getCode(),
				'message' => $error->getMessage(),
			);
			return $this->handle_response( $response );
		}
	}

	public static function get_rest_method() {
		return \WP_REST_Server::READABLE;
	}

	public static function get_rest_args() {
		return array();
	}
}
