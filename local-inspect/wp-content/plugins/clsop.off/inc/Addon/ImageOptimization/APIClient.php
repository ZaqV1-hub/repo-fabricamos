<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization;

use stdClass;
use WP_Error;
use WP_Rocket\Engine\AccelerateWp\AbstractAPIClient;
use WP_Rocket\Engine\AccelerateWp\Sentry;
use WpOrg\Requests\Utility\CaseInsensitiveDictionary;

class APIClient extends AbstractAPIClient {

	/**
	 * Image minification API endpoint.
	 *
	 * @var string
	 */
	const ENDPOINT = 'v1/image-minification';

	/**
	 * Total number of image minification requests allowed per site at any time. This is a safeguard to prevent a site
	 * from sending too many images to the API.
	 *
	 * @var int
	 */
	const CONCURRENCY_REQUESTS_LIMIT = 20;

	/**
	 * Options manager instance.
	 *
	 * @var OptionsManager
	 */
	private $options;

	/**
	 * Creates an instance of image minification API client.
	 *
	 * @param OptionsManager $options Instance of Options manager instance.
	 */
	public function __construct( OptionsManager $options ) {
		$this->options = $options;
	}

	/**
	 * Sends a image minification request to the Image Minification API.
	 *
	 * @param string $url The URL to send a image minification request for.
	 * @param string $format format.
	 * @param string $secret secret.
	 * @param string $return_url return_url.
	 *
	 * @return stdClass|WP_Error
	 * @since 3.12.6.1_1.1-1
	 *
	 * @note CL
	 */
	public function send_generation_request( $url, $format, $secret, $return_url ) {
		$params = [
			'url'        => $url,
			'format'     => $format,
			'secret'     => $secret,
			'return_url' => $return_url,
		];

		$response = $this->remote_post( self::ENDPOINT, $params );

		return $this->prepare_response( $response );
	}

	/**
	 * Prepare the response to be returned.
	 *
	 * @since 3.12.6.1_1.1-1
	 * @since 3.12.6.1_1.1-2 Removed error reporting as it's handled already in QueueWorkerProcess::handle_failed_job(). Also removed $url and $format parameters.
	 *
	 * @param array|WP_Error $response The response or WP_Error on failure.
	 *
	 * @return stdClass|WP_Error
	 *
	 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	 */
	private function prepare_response( $response ) {

		if ( is_wp_error( $response ) ) {

			if ( in_array(
				$response->get_error_code(),
				[
					'apw_sass_api_jwt_configuration',
					'apw_sass_api_unique_id_missing',
				],
				true
			) ) {

				// This is an error related to the SaaS service configuration in our plugin.
				do_action(
					'rocket_image_optimization_stop',
					[
						'reason'   => 'auth_failed_config',
						'severity' => 'error',
					]
				);

			} else {

				// This is an error related to network level communication with SaaS service.
				do_action(
					'rocket_image_optimization_postpone',
					[
						'reason'        => 'saas_not_available',
						'severity'      => 'warning',
						'next_retry_in' => 300, // 5 minutes
					]
				);

			}

			return $response;
		}

		$response_data        = $this->get_response_data( $response );
		$response_status_code = $this->get_response_status( $response, ( isset( $response_data->status ) ) ? $response_data->status : null );

		// Check if the usage quota has been reached.
		if ( isset( $response_data->data->usage ) && 'exceeded' === $response_data->data->usage ) {
			// $response_data->date->usage_count holds the used quota count

			// Calculate time until the beginning of next month (first day of next month at 00:04:00).
			$next_month               = strtotime( 'first day of next month 4am' );
			$seconds_until_next_month = $next_month - time();

			// Pause image optimization until the beginning of next month.
			do_action(
				'rocket_image_optimization_postpone',
				[
					'reason'        => 'quota_exceeded',
					'severity'      => 'warning',
					'next_retry_in' => $seconds_until_next_month,
				]
			);
		}

		$headers = wp_remote_retrieve_headers( $response );
		if ( $headers instanceof CaseInsensitiveDictionary ) {
			$headers = $headers->getAll();
		}

		$this->handle_non_success_response_codes( $response_status_code, $headers );

		// Save concurrency limit.
		if ( isset( $response_data->data->concurrency_limit ) ) {
			set_transient( 'rocket_image_optimization_concurrency_limit', (int) $response_data->data->concurrency_limit );
		}

		$succeeded = $this->get_response_success( $response_status_code, $response_data );
		if ( $succeeded ) {
			return $response_data;
		}

		$response_message = $this->get_response_message( $response_status_code, $response_data );

		if ( 200 === $response_status_code ) {
			$response_status_code = 400;
		}

		return Sentry::enrich_wp_error(
			new WP_Error(
				$this->get_response_code( $response ),
				$response_message,
				[
					'status' => $response_status_code,
				]
			)
		);
	}

	/**
	 * Get response message.
	 *
	 * @param int      $response_status_code Response status code.
	 * @param stdClass $response_data Object of data returned from request.
	 *
	 * @since 3.12.6.1_1.1-1
	 * @since 3.12.6.1_1.1-2 Simplified error messages and removed $url and $format parameters.
	 *
	 * @return string
	 */
	private function get_response_message( $response_status_code, $response_data ) {
		$message = '';

		switch ( $response_status_code ) {
			case 200:
				if ( ! isset( $response_data->data->id ) ) {
					$message .= esc_html__( 'The API returned an empty response.', 'rocket' );
				}
				break;
			case 401:
				$message .= esc_html__( 'API authentication failed.', 'rocket' );
				break;
			case 429:
				$message .= esc_html__( 'Request rate limit reached.', 'rocket' );
				break;
			default:
				$message .= sprintf(
					/* translators: %d = response status code. */
					esc_html__( 'The API returned an unexpected response code: %d.', 'rocket' ),
					$response_status_code
				);
				break;
		}

		if ( isset( $response_data->message ) ) {
			// translators: %1$s = error message.
			$message .= ' ' . $response_data->message;
		}

		return $message;
	}

	/**
	 * Get our internal response code [Not the standard HTTP codes].
	 *
	 * @param array|WP_Error $response The response or WP_Error on failure.
	 *
	 * @return string response code.
	 * @since 3.12.6.1_1.1-1
	 */
	private function get_response_code( $response ) {
		return 'image_minification_request';
	}

	/**
	 * Get job details by calling API with job ID.
	 *
	 * @param string $job_id ID for the job to get details.
	 *
	 * @return mixed|WP_Error Details for job.
	 * @since 3.12.6.1_1.1-1
	 */
	public function get_job_details( $job_id ) {
		$response = $this->remote_get( self::ENDPOINT . "/$job_id/" );

		return $this->prepare_response( $response );
	}

	/**
	 * Call a dedicated API endpoint to let the SAAS service know that the job was completed and minified image was
	 * downloaded.
	 *
	 * @since 3.12.6.1_1.1-1
	 *
	 * @param int $job_id ID for the job to get details.
	 *
	 * @return mixed|WP_Error Details for job.
	 */
	public function acknowledge_job_completion( $job_id ) {
		$response = $this->remote_post( self::ENDPOINT . '/ack/', [ 'id' => $job_id ] );

		return $this->prepare_response( $response );
	}

	/**
	 * Get the number of concurrency image minification requests allowed from the site.
	 *
	 * @return int
	 * @since 3.12.6.1_1.1-1
	 */
	public function get_concurrency_requests_limit() {
		$concurrency_limit = get_transient( 'rocket_image_optimization_concurrency_limit' );

		if ( true === $concurrency_limit || is_integer( $concurrency_limit ) ) {
			return (int) $concurrency_limit;
		}

		return self::CONCURRENCY_REQUESTS_LIMIT;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_awp_unique_id() {
		return $this->options->get_unique_id();
	}

	/**
	 * Get api download url.
	 *
	 * @param string|int $job_id ID for the job to get details.
	 */
	public function get_api_download_url( $job_id ) {
		return $this->get_saas_url() . self::ENDPOINT . '/download/' . $job_id . '/';
	}

	/**
	 * Downloads a file from given URL while adding correct authentication headers.
	 *
	 * @param string $url URL of the file to download.
	 *
	 * @return string|WP_Error The function returns the read data, WP_Error if the API is misconfigured or the file download itself failed.
	 */
	public function get_file_contents( $url ) {

		$auth_header = $this->get_auth_header();
		if ( is_wp_error( $auth_header ) ) {
			return $auth_header;
		}

		wp_raise_memory_limit( 'image' );

		$result = wp_remote_get(
			$url,
			[
				'timeout'   => $this->get_max_timeout(),
				'headers'   => [
					self::AUTH_HEADER_NAME => $auth_header,
					'Cache-Control'        => 'no-cache',
				],
				'sslverify' => false,
			]
		);

		if ( is_wp_error( $result ) ) {
			return Sentry::enrich_wp_error(
				new WP_Error(
					'image_download_failed',
					$result->get_error_message(),
					$result->get_error_data()
				)
			);
		}

		if ( isset( $result['response']['code'] ) && 200 !== $result['response']['code'] ) { // @phpstan-ignore-line
			$status_code = (int) $result['response']['code'];
			$headers     = $result['headers'] ?? []; // @phpstan-ignore-line
			$this->handle_non_success_response_codes( $status_code, $headers );

			return Sentry::enrich_wp_error(
				new WP_Error(
					'image_download_failed',
					esc_html__( 'wp_remote_get() failed to download image', 'rocket' ),
					[
						'response_code' => $status_code,
						'headers'       => $headers,
					]
				)
			);
		}

		if ( ! isset( $result['body'] ) || empty( $result['body'] ) ) { // @phpstan-ignore-line
			return Sentry::enrich_wp_error(
				new WP_Error(
					'image_download_failed',
					esc_html__( 'wp_remote_get() returned an empty response', 'rocket' )
				)
			);
		}

		return $result['body'];
	}

	/**
	 * Handles non-success response codes.
	 *
	 * The image optimization process is postponed for some selected response code - 401, 429 and 5xx.
	 *
	 * @param int   $status_code Status code.
	 * @param array $headers HTTP headers.
	 *
	 * @return void
	 */
	private function handle_non_success_response_codes( $status_code, $headers ) {
		// Check if request rate limit has been reached.
		if ( 429 === $status_code ) {
			// Postpone image optimization. Use 'Retry-After' header  to set delay or delay by 5 minutes if missing.
			$retry_after   = array_key_exists( 'Retry-After', $headers ) ? (string) $headers['Retry-After'] : ''; // We only support seconds for now.
			$next_retry_in = strlen( $retry_after ) > 0 ? (int) $retry_after : 300; // 5 minutes by default.

			do_action(
				'rocket_image_optimization_postpone',
				[
					'reason'        => 'rate_limit',
					'next_retry_in' => $next_retry_in,
				]
			);
		} else if ( 401 === $status_code ) {
			// API authentication is correctly setup on WordPress side, but SaaS service rejected request with status code 401 Unauthorized.

			// Postpone the image optimization process and retry every 15 minutes. If the authentication is failing for more than 24 hours, we stop the process and show an admin notice.
			do_action(
				'rocket_image_optimization_postpone',
				[
					'reason'        => 'auth_failed_401',
					'severity'      => 'warning',
					'next_retry_in' => 900, // 15 minutes
					'max_retries'   => 96, // 24 hours
				]
			);

		} else if ( 500 <= $status_code && $status_code < 600 ) {
			do_action(
				'rocket_image_optimization_postpone',
				[
					'reason'        => 'saas_not_available',
					'severity'      => 'warning',
					'next_retry_in' => 300, // 5 minutes
				]
			);
		}
	}
}
