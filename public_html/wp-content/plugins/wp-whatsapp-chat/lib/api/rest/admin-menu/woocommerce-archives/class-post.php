<?php

namespace QuadLayers\QLWAPP\Api\Rest\Admin_Menu\WooCommerce_Archives;

use QuadLayers\QLWAPP\Api\Rest\Admin_Menu\Base;
use QuadLayers\QLWAPP\Models\WooCommerce_Archives as Models_WooCommerceArchives;

class Post extends Base {
	protected static $route_path = 'woocommerce_archives';

	public function callback( \WP_REST_Request $request ) {
		try {

			$body = json_decode( $request->get_body(), true );

			$woocommerce_archives = Models_WooCommerceArchives::instance();

			$status = $woocommerce_archives->save( $body );

			return $this->handle_response( $status );
		} catch ( \Throwable $error ) {
			$response = array(
				'code'    => $error->getCode(),
				'message' => $error->getMessage(),
			);
			return $this->handle_response( $response );
		}
	}

	public static function get_rest_method() {
		return \WP_REST_Server::CREATABLE;
	}

	public static function get_rest_args() {
		return array();
	}
}
