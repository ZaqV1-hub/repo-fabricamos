<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */
namespace WP_Rocket\Engine\CDN\RocketCDN;

use WP_Rocket\Event_Management\Subscriber_Interface;
use WP_Rocket\Admin\Options_Data;

/**
 * Subscriber for RocketCDN REST API Integration
 *
 * @note CL
 * @since 3.5
 */
class RESTSubscriber implements Subscriber_Interface {
	const ROUTE_NAMESPACE = 'clsop/v1'; // CL.

	/**
	 * CDNOptionsManager instance
	 *
	 * @var CDNOptionsManager
	 */
	private $cdn_options;

	/**
	 * WP Rocket Options instance
	 *
	 * @var Options_Data
	 */
	private $options;

	/**
	 * RocketCDN API Client instance.
	 *
	 * @note CL.
	 * @var APIClient
	 */
	private $api_client;

	/**
	 * Constructor
	 *
	 * @param CDNOptionsManager $cdn_options CDNOptionsManager instance.
	 * @param Options_Data      $options     WP Rocket Options instance.
	 * @param APIClient         $api_client RocketCDN API Client instance.
	 */
	public function __construct( CDNOptionsManager $cdn_options, Options_Data $options, APIClient $api_client ) {
		$this->cdn_options = $cdn_options;
		$this->options     = $options;
		$this->api_client  = $api_client; // CL.
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_subscribed_events() {
		return [
			'rest_api_init' => [
				[ 'register_enable_route' ],
				[ 'register_disable_route' ],
				[ 'register_notify_limit_route' ], // CL.
			],
		];
	}

	/**
	 * Register Enable route in the WP REST API
	 *
	 * @since 3.5
	 *
	 * @note CL
	 *
	 * @return void
	 */
	public function register_enable_route() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'cdn/enable',
			[
				'methods'             => 'PUT',
				'callback'            => [ $this, 'enable' ],
				'args'                => [
					// CL.
					'cdn_url' => [
						'required'          => true,
						'validate_callback' => function ( $param ) {
							return ! empty( $param ) && filter_var( $param, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME );
						},
						'sanitize_callback' => function ( $param ) {
							return $param;
						},
					],
					// CL.
					'api_key' => [
						'required'          => true,
						'validate_callback' => [ $this, 'validate_key' ],
					],
				],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Register Disable route in the WP REST API
	 *
	 * @since 3.5
	 *
	 * @note CL
	 * @return void
	 */
	public function register_disable_route() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'cdn/disable',
			[
				'methods'             => 'PUT',
				'callback'            => [ $this, 'disable' ],
				'args'                => [
					// CL.
					'api_key' => [
						'required'          => true,
						'validate_callback' => [ $this, 'validate_key' ],
					],
				],
				'permission_callback' => '__return_true',
			]
		);
	}

	/**
	 * Register CDN Limit route in the WP REST API
	 *
	 * @since 3.5
	 *
	 * @note CL.
	 * @return void
	 */
	public function register_notify_limit_route() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			'cdn/notify/limit',
			[
				'methods'             => 'PUT',
				'callback'            => [ $this, 'notify_limit' ],
				'args'                => [
					'api_key' => [
						'required'          => true,
						'validate_callback' => [ $this, 'validate_key' ],
					],
					'limit'   => [
						'required'          => true,
						'validate_callback' => [ $this, 'validate_limit' ],
					],
				],
				'permission_callback' => function () {
					return $this->options->get( 'cdn', 0 );
				},
			]
		);
	}

	/**
	 * Enable CDN and add RocketCDN URL to WP Rocket options
	 *
	 * @since 3.5
	 *
	 * @param \WP_REST_Request $request the WP REST Request object.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function enable( \WP_REST_Request $request ) {
		$params = $request->get_body_params();

		$this->cdn_options->enable( $params['cdn_url'] ); // CL.

		$response = [
			'code'    => 'success',
			'message' => __( 'CDN enabled', 'rocket' ),
			'data'    => [
				'status' => 200,
			],
		];

		return rest_ensure_response( $response );
	}

	/**
	 * Disable the CDN and remove the RocketCDN URL from WP Rocket options
	 *
	 * @since 3.5
	 *
	 * @param \WP_REST_Request $request the WP Rest Request object.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function disable( \WP_REST_Request $request ) {
		$this->cdn_options->disable();

		$response = [
			'code'    => 'success',
			'message' => __( 'CDN disabled', 'rocket' ),
			'data'    => [
				'status' => 200,
			],
		];

		return rest_ensure_response( $response );
	}

	/**
	 * Send notification limit
	 *
	 * @param \WP_REST_Request $request the WP Rest Request object.
	 *
	 * @note CL.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function notify_limit( \WP_REST_Request $request ) {
		$params = $request->get_body_params();

		$options_json = $this->api_client->api_socket()->options();
		if ( ! empty( $options_json ) ) {
			set_transient( 'rocket_rocketcdn_user_info', json_decode( $options_json, true ) );
		}

		set_transient( 'rocket_rocketcdn_limit_reached', $params['limit'] );
		do_action( 'rocket_cl_cdn_send_limit_reached_mail' );

		$response = [
			'code'    => 'success',
			'message' => __( 'Notification sent', 'rocket' ),
			'data'    => [
				'status' => 200,
			],
		];

		return rest_ensure_response( $response );
	}

	/**
	 * Checks that the email sent along the request corresponds to the one saved in the DB
	 *
	 * @since 3.5
	 *
	 * @param string $param Parameter value to validate.
	 *
	 * @return bool
	 */
	public function validate_email( $param ) {
		return ! empty( $param ) && $param === $this->options->get( 'consumer_email' );
	}

	/**
	 * Checks that the key sent along the request corresponds to the one saved in the DB
	 *
	 * @since 3.5
	 *
	 * @param string $param Parameter value to validate.
	 * @note CL.
	 * @return bool
	 */
	public function validate_key( $param ) {
		return ! empty( $param ) && $param === $this->cdn_options->get_awp_cdn_api_key();
	}

	/**
	 * Validates format of the limit parameter. "ddd ss" (1 GB, 100 GB, 2.5 TB)
	 *
	 * @since 3.12.6.1-1.1-2
	 *
	 * @param string $param Parameter value to validate.
	 * @note CL.
	 * @return bool
	 */
	public function validate_limit( $param ) {
		$parts = explode( ' ', $param );
		if ( count( $parts ) !== 2 ) {
			return false;
		}
		return is_numeric( $parts[0] ) && 2 === strlen( $parts[1] );
	}
}
