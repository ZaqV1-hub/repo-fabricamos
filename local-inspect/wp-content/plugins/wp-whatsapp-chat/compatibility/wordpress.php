<?php

if ( ! function_exists( 'wp_is_serving_rest_request' ) ) {
	function wp_is_serving_rest_request() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}
}
