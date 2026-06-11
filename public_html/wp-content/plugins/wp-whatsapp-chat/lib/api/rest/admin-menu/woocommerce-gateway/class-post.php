<?php

namespace QuadLayers\QLWAPP\Api\Rest\Admin_Menu\WooCommerce_Gateway;

use QuadLayers\QLWAPP\Api\Rest\Admin_Menu\Base;
use QuadLayers\QLWAPP\Models\WooCommerce_Gateway as Models_WooCommerce_Gateway;

/**
 * POST endpoint para guardar configuración del gateway
 */
class Post extends Base {
	protected static $route_path = 'woocommerce_gateway';

	/**
	 * Guardar configuración
	 */
	public function callback( \WP_REST_Request $request ) {
		try {
			$body = json_decode( $request->get_body(), true );

			$woocommerce_gateway = Models_WooCommerce_Gateway::instance();
			$status              = $woocommerce_gateway->save( $body );

			return $this->handle_response( $status );
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
		return \WP_REST_Server::CREATABLE;
	}

	/**
	 * Schema de parámetros
	 */
	public static function get_rest_args() {
		return array();
	}
}
