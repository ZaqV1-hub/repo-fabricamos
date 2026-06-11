<?php
/**
 * API License.
 *
 * @package JupiterX_Core\Admin\License
 *
 * @since 4.10.1
 */
class JupiterX_Core_API_License {

	private static $instance        = null;
	const ENVATO_ITEM_ID            = '5177775';
	const PURCHASE_CODE_OPTION_NAME = 'envato_purchase_code_' . self::ENVATO_ITEM_ID;
	const ACCESS_TOKEN_OPTION_NAME  = 'api_access_token';
	const API_KEY_OPTION_NAME       = 'api_key';
	const EMAIL_OPTION_NAME         = 'api_email';
	const EXPIRY_OPTION_NAME        = 'api_expiry';

	/**
	 * Get instance.
	 *
	 * @since 4.10.1
	 *
	 * @return JupiterX_Core_API_License
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 4.10.1
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Register routes.
	 *
	 * @since 4.10.1
	 */
	public function register_routes() {
		register_rest_route( 'jupiterx-license', 'deactivate', [
			'methods' => 'POST',
			'callback' => [ $this, 'remote_deactivate' ],
			'permission_callback' => '__return_true',
		] );
	}

	/**
	 * Remote deactivate.
	 *
	 * @since 4.10.1
	 *
	 * @param object $request Request.
	 *
	 * @return \WP_REST_Response
	 */
	public function remote_deactivate( $request ) {
		$site_key     = $request->get_param( 'site_key' );
		$access_token = $request->get_param( 'access_token' );

		if ( empty( $site_key ) || empty( $access_token ) ) {
			return new \WP_REST_Response( [ 'message' => __( 'Invalid request.', 'jupiterx-core' ) ], 400 );
		}

		if ( $this->get_option( self::API_KEY_OPTION_NAME ) === $site_key && $this->get_option( self::ACCESS_TOKEN_OPTION_NAME ) === $access_token ) {
			$this->remove_option( self::PURCHASE_CODE_OPTION_NAME );
			$this->remove_option( self::ACCESS_TOKEN_OPTION_NAME );
			$this->remove_option( self::API_KEY_OPTION_NAME );
			$this->remove_option( self::EMAIL_OPTION_NAME );
			$this->remove_option( self::EXPIRY_OPTION_NAME );

			return new \WP_REST_Response( [ 'message' => __( 'License deactivated', 'jupiterx-core' ) ], 200 );
		}

		return new \WP_REST_Response( [ 'message' => __( 'Invalid request.', 'jupiterx-core' ) ], 400 );
	}

	/**
	 * Get option value.
	 *
	 * @since 4.10.1
	 *
	 * @param string $name Option name.
	 *
	 * @return string Option value.
	 */
	private function get_option( $name ) {
		return jupiterx_get_option( $name, false );
	}

	/**
	 * Remove option value.
	 *
	 * @since 4.10.1
	 *
	 * @param string $name Option name.
	 *
	 * @return boolean Remove status.
	 */
	private function remove_option( $name ) {
		if ( $this->get_option( $name ) ) {
			return jupiterx_delete_option( $name );
		}
		return true;
	}
}

JupiterX_Core_API_License::get_instance();
