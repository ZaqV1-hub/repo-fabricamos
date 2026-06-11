<?php

namespace QuadLayers\QLWAPP\Controllers;

/**
 * REST API Controller for managing QuickBot auth tokens via HTTP-only cookies
 *
 * This provides a fallback mechanism for Safari's Intelligent Tracking Prevention (ITP).
 * When Safari detects cross-origin iframes, it partitions localStorage, causing tokens to be
 * cleared on page reload. HTTP-only cookies set by PHP on the WordPress domain are immune
 * to this behavior.
 */
class Auth_Token_API {

	protected static $instance;

	/**
	 * Cookie name for storing the JWT token
	 */
	const COOKIE_NAME = 'quickbot_auth_token';

	/**
	 * Cookie expiration time (7 days in seconds)
	 */
	const COOKIE_EXPIRATION = 7 * 24 * 60 * 60;

	/**
	 * REST API namespace
	 */
	const REST_NAMESPACE = 'qlwapp/v1';

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register REST API routes
	 */
	public function register_routes() {
		// Set token in HTTP-only cookie
		register_rest_route(
			self::REST_NAMESPACE,
			'/auth/token',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'set_token' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'args'                => array(
					'token' => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => array( $this, 'validate_token' ),
					),
				),
			)
		);

		// Get token from HTTP-only cookie
		register_rest_route(
			self::REST_NAMESPACE,
			'/auth/token',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_token' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		// Delete token (clear cookie)
		register_rest_route(
			self::REST_NAMESPACE,
			'/auth/token',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'delete_token' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);
	}

	/**
	 * Check if user has permission to manage auth tokens
	 *
	 * @return bool
	 */
	public function check_permissions() {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Validate JWT token format
	 *
	 * @param string $token The token to validate
	 * @return bool
	 */
	public function validate_token( $token ) {
		$parts = explode( '.', $token );
		return count( $parts ) === 3;
	}

	/**
	 * Set auth token in HTTP-only cookie
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function set_token( $request ) {
		$token = $request->get_param( 'token' );

		$site_url      = get_site_url();
		$parsed_url    = wp_parse_url( $site_url );
		$cookie_domain = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';

		$result = setcookie(
			self::COOKIE_NAME,
			$token,
			array(
				'expires'  => time() + self::COOKIE_EXPIRATION,
				'path'     => '/',
				'domain'   => $cookie_domain,
				'secure'   => is_ssl(),
				'httponly' => true,
				'samesite' => 'Lax',
			)
		);

		if ( ! $result ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Failed to set cookie. Headers may have already been sent.',
				),
				500
			);
		}

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => 'Token saved successfully',
			),
			200
		);
	}

	/**
	 * Get auth token from HTTP-only cookie
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function get_token( $request ) {
		if ( isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			$token = sanitize_text_field( wp_unslash( $_COOKIE[ self::COOKIE_NAME ] ) );

			return new \WP_REST_Response(
				array(
					'success' => true,
					'token'   => $token,
				),
				200
			);
		}

		return new \WP_REST_Response(
			array(
				'success' => false,
				'message' => 'No token found',
			),
			200
		);
	}

	/**
	 * Delete auth token (clear cookie)
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response
	 */
	public function delete_token( $request ) {
		$site_url      = get_site_url();
		$parsed_url    = wp_parse_url( $site_url );
		$cookie_domain = isset( $parsed_url['host'] ) ? $parsed_url['host'] : '';

		setcookie(
			self::COOKIE_NAME,
			'',
			array(
				'expires'  => time() - 3600,
				'path'     => '/',
				'domain'   => $cookie_domain,
				'secure'   => is_ssl(),
				'httponly' => true,
				'samesite' => 'Lax',
			)
		);

		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => 'Token deleted successfully',
			),
			200
		);
	}

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
