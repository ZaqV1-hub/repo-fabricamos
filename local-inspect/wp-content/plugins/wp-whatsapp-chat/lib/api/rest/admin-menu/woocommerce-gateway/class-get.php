<?php

namespace QuadLayers\QLWAPP\Api\Rest\Admin_Menu\WooCommerce_Gateway;

use QuadLayers\QLWAPP\Api\Rest\Admin_Menu\Base;
use QuadLayers\QLWAPP\Models\WooCommerce_Gateway as Models_WooCommerce_Gateway;

/**
 * GET endpoint para obtener configuración del gateway
 */
class Get extends Base {
	protected static $route_path = 'woocommerce_gateway';

	/**
	 * Obtener configuración
	 */
	public function callback( \WP_REST_Request $request ) {
		try {
			$models_woocommerce_gateway = Models_WooCommerce_Gateway::instance();

			$gateway_settings = $models_woocommerce_gateway->get();

			return $this->handle_response( $gateway_settings );
		} catch ( \Throwable $error ) {
			$response = array(
				'code'    => $error->getCode(),
				'message' => $error->getMessage(),
			);
			return $this->handle_response( $response );
		}
	}

	/**
	 * Método HTTP
	 */
	public static function get_rest_method() {
		return \WP_REST_Server::READABLE;
	}

	/**
	 * Schema de respuesta
	 */
	public static function get_rest_args() {
		return array();
	}
}
