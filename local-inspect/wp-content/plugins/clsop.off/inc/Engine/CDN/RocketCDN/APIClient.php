<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */
namespace WP_Rocket\Engine\CDN\RocketCDN;

use WP_Error;
use WP_Rocket\Engine\AccelerateWp\ApiSocket;

/**
 * Class to Interact with the RocketCDN API
 *
 * @note CL
 */
class APIClient {
	const ROCKETCDN_API = 'https://cloudlinux.com/api/';

	/**
	 * CDNOptionsManager instance
	 *
	 * @note CL.
	 * @var CDNOptionsManager
	 */
	private $cdn_options;

	/**
	 * Api socket
	 *
	 * @note CL.
	 * @var ApiSocket
	 */
	private $api_socket;

	/**
	 * Constructor
	 *
	 * @note CL.
	 * @param  CDNOptionsManager $cdn_options  CDNOptionsManager instance.
	 * @param  ApiSocket         $api_socket  ApiSocket instance.
	 */
	public function __construct( CDNOptionsManager $cdn_options, ApiSocket $api_socket ) {
		$this->cdn_options = $cdn_options;
		$this->api_socket  = $api_socket;
	}

	/**
	 * Gets current RocketCDN subscription data from cache if it exists
	 *
	 * Else do a request to the API to get fresh data
	 *
	 * @since 3.5
	 *
	 * @return array
	 */
	public function get_subscription_data() {
		$status = get_transient( 'rocketcdn_status' );

		if ( false !== $status ) {
			return $status;
		}

		return $this->get_remote_subscription_data();
	}

	/**
	 * Gets fresh RocketCDN subscription data from the API
	 *
	 * @since 3.5
	 *
	 * @note CL
	 * @return array
	 */
	private function get_remote_subscription_data() {
		$default = [
			'id'                            => 0,
			'is_active'                     => false,
			'cdn_url'                       => '',
			'subscription_next_date_update' => 0,
			'subscription_status'           => 'cancelled',
		];

		if ( empty( $this->cdn_options->get_awp_account_id() ) || empty( $this->cdn_options->get_awp_cdn_url() ) ) {
			// CL. $this->set_status_transient( $default, 3 * MINUTE_IN_SECONDS );.

			return $default;
		}

		$default['id']                            = $this->cdn_options->get_awp_account_id();
		$default['is_active']                     = true;
		$default['cdn_url']                       = $this->cdn_options->get_awp_cdn_url();
		$default['subscription_next_date_update'] = 0;
		$default['subscription_status']           = 'running'; // Or cancelled.

		// CL. $this->set_status_transient( $default, MONTH_IN_SECONDS );.

		return $default;

		/*
		CL. Temporary off
		$token = get_option( 'rocketcdn_user_token' );

		if ( empty( $token ) ) {
			return $default;
		}

		$args = [
			'headers' => [
				'Authorization' => 'Token ' . $token,
			],
		];

		$response = wp_remote_get(
			self::ROCKETCDN_API . 'website/search/?url=' . home_url(),
			$args
		);

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$this->set_status_transient( $default, 3 * MINUTE_IN_SECONDS );

			return $default;
		}

		$data = wp_remote_retrieve_body( $response );

		if ( empty( $data ) ) {
			$this->set_status_transient( $default, 3 * MINUTE_IN_SECONDS );

			return $default;
		}

		$data = json_decode( $data, true );
		$data = array_intersect_key( (array) $data, $default );
		$data = array_merge( $default, $data );

		$this->set_status_transient( $data, WEEK_IN_SECONDS );

		return $data;
		*/
	}

	/**
	 * Sets the RocketCDN status transient with the provided value
	 *
	 * @since 3.5
	 *
	 * @param array $value Transient value.
	 * @param int   $duration Transient duration.
	 * @return void
	 */

	/*
	CL.
	private function set_status_transient( $value, $duration ) {
		set_transient( 'rocketcdn_status', $value, $duration );
	}
	*/

	/**
	 * Gets pricing & promotion data for RocketCDN from cache if it exists
	 *
	 * Else do a request to the API to get fresh data
	 *
	 * @since 3.5
	 *
	 * @return array|WP_Error
	 */
	public function get_pricing_data() {
		$pricing = get_transient( 'rocketcdn_pricing' );

		if ( false !== $pricing ) {
			return $pricing;
		}

		return $this->get_remote_pricing_data();
	}

	/**
	 * Gets fresh pricing & promotion data for RocketCDN
	 *
	 * @since 3.5
	 *
	 * @return array|WP_Error
	 */
	private function get_remote_pricing_data() {
		$response = wp_remote_get( self::ROCKETCDN_API . 'pricing' );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return $this->get_wp_error( __( 'We could not fetch the current price because AccelerateWP API returned an unexpected error code.', 'rocket' ) ); // CL.
		}

		$data = wp_remote_retrieve_body( $response );

		if ( empty( $data ) ) {
			return $this->get_wp_error( __( 'AccelerateWP is not available at the moment. Please retry later.', 'rocket' ) ); // CL.
		}

		$data = json_decode( $data, true );

		set_transient( 'rocketcdn_pricing', $data, 6 * HOUR_IN_SECONDS );

		return $data;
	}

	/**
	 * Gets a new WP_Error instance
	 *
	 * @since 3.5
	 *
	 * @param string $message Error message.
	 *
	 * @return WP_Error
	 */
	private function get_wp_error( string $message ) {
		return new WP_Error( 'rocketcdn_error', $message );
	}

	/**
	 * Sends a request to the API to purge the CDN cache
	 *
	 * @since 3.5
	 *
	 * @note CL.
	 * @return array
	 *
	 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	 */
	public function purge_cache_request() {
		$subscription = $this->get_subscription_data();
		$status       = 'error';

		if ( ! isset( $subscription['id'] ) || 0 === $subscription['id'] ) {
			return [
				'status'  => $status,
				'message' => __( 'AccelerateWP cache purge failed: Missing identifier parameter.', 'rocket' ), // CL.
			];
		}

		do_action( 'accelerate_wp_set_error_handler' );

		$home_url  = home_url();
		$parse_url = wp_parse_url( $home_url );
		$scheme    = array_key_exists( 'scheme', $parse_url ) ? $parse_url['scheme'] : 'http';
		$host      = array_key_exists( 'host', $parse_url ) ? $parse_url['host'] : '';
		$domain    = $scheme . '://' . $host;
		$website   = ( array_key_exists( 'path', $parse_url ) && ! empty( $parse_url['path'] ) ) ? $parse_url['path'] : '/';

		$purge = $this->api_socket->purge( $domain, $website );

		do_action( 'accelerate_wp_restore_error_handler' );

		$response = json_decode( $purge, true );

		if ( ! is_array( $response ) ) {
			return [
				'status'  => $status,
				'message' => __( 'AccelerateWP cache purge failed: The API returned an unexpected response.', 'rocket' ),
			];
		}

		if ( ! array_key_exists( 'result', $response ) || 'success' !== $response['result'] ) {
			return [
				'status'  => $status,
				'message' => __( 'AccelerateWP cache purge failed: The API returned error response code.', 'rocket' ),
			];
		}

		return [
			'status'  => 'success',
			'message' => __( 'AccelerateWP cache purge successful.', 'rocket' ),
		];

		/*
		CL. Temporary off

		$token = get_option( 'rocketcdn_user_token' );

		if ( empty( $token ) ) {
			return [
				'status'  => $status,
				'message' => __( 'AccelerateWP cache purge failed: Missing user token.', 'rocket' ),
			];
		}

		$args = [
			'method'  => 'DELETE',
			'headers' => [
				'Authorization' => 'Token ' . $token,
			],
		];

		$response = wp_remote_request(
			self::ROCKETCDN_API . 'website/' . $subscription['id'] . '/purge/',
			$args
		);

		if ( is_wp_error( $response ) ) {
			return [
				'status'  => $status,
				'message' => $response->get_error_message(),
			];
		}

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return [
				'status'  => $status,
				'message' => __( 'AccelerateWP cache purge failed: The API returned an unexpected response code.', 'rocket' ),
			];
		}

		$data = wp_remote_retrieve_body( $response );

		if ( empty( $data ) ) {
			return [
				'status'  => $status,
				'message' => __( 'AccelerateWP cache purge failed: The API returned an empty response.', 'rocket' ),
			];
		}

		$data = json_decode( $data );

		if ( ! isset( $data->success ) ) {
			return [
				'status'  => $status,
				'message' => __( 'AccelerateWP cache purge failed: The API returned an unexpected response.', 'rocket' ),
			];
		}

		if ( ! $data->success ) {
			return [
				'status'  => $status,
				'message' => sprintf(
					// translators: %s = message returned by the API.
					__( 'AccelerateWP cache purge failed: %s.', 'rocket' ),
					isset( $data->message ) ? $data->message : ''
				),
			];
		}

		return [
			'status'  => 'success',
			'message' => __( 'AccelerateWP cache purge successful.', 'rocket' ),
		];
		*/
	}

	/**
	 * Filter the arguments used in an HTTP request, to make sure our user token has not been overwritten
	 * by some other plugin.
	 *
	 * @since  3.5
	 *
	 * @param  array  $args An array of HTTP request arguments.
	 * @param  string $url  The request URL.
	 * @return array
	 */
	public function preserve_authorization_token( $args, $url ) {
		if ( strpos( $url, self::ROCKETCDN_API ) === false ) {
			return $args;
		}

		if ( empty( $args['headers']['Authorization'] ) && self::ROCKETCDN_API . 'pricing' === $url ) {
			return $args;
		}

		$token = get_option( 'rocketcdn_user_token' );

		if ( empty( $token ) ) {
			return $args;
		}

		$value = 'token ' . $token;

		if ( isset( $args['headers']['Authorization'] ) && $value === $args['headers']['Authorization'] ) {
			return $args;
		}

		$args['headers']['Authorization'] = $value;

		return $args;
	}

	/**
	 * Enable CDN and add PullZone URL to WP Rocket options
	 *
	 * @param  int    $account_id  Account id.
	 * @param  string $cdn_url  Cdn url.
	 * @param  string $api_key  Api key.
	 *
	 * @note CL.
	 * @return void
	 * @throws \Exception Data error.
	 */
	public function enable( int $account_id, string $cdn_url, string $api_key ) {
		$this->cdn_options->awp( $account_id, $api_key );
		$this->cdn_options->enable( $cdn_url );

		// Update transient.
		$this->get_subscription_data();
	}

	/**
	 * Disable the CDN and remove the RocketCDN URL from WP Rocket options
	 *
	 * @note CL.
	 * @return bool
	 */
	public function disable() {
		$this->cdn_options->disable();

		return true;
	}

	/**
	 * Return api_socket instance.
	 *
	 * @note CL.
	 * @return ApiSocket
	 */
	public function api_socket() {
		return $this->api_socket;
	}
}
