<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Engine\AccelerateWp;

use Firebase\JWT\JWT;
use stdClass;
use WP_Error;

/**
 * Abstract class for the AccelerateWP SAAS API service.
 *
 * Handles common types of request while adding the correct authentication headers.
 *
 * @since 3.12.6.1_1.1-1
 */
abstract class AbstractAPIClient {

	const AUTH_HEADER_NAME = 'AccessKey';

	/**
	 * JWT token for requests
	 *
	 * @var string
	 */
	private $jwt = null;

	/**
	 * Puts together a value of the authentication headers.
	 *
	 * @since 3.12.6.1_1.1-1
	 *
	 * @return string|WP_Error Authentication header value. WP_Error if there is a problem with API configuration.
	 */
	protected function get_auth_header() {

		// Check that we have non-empty JWT token.
		$jwt = $this->get_jwt();

		// Check that we have non-empty unique ID.
		$unique_id = $this->get_awp_unique_id();
		// @phpstan-ignore-next-line
		if ( ! is_string( $unique_id ) || 0 === strlen( $unique_id ) ) {
			return $this->build_error_for_missing_unique_id();
		}

		return $jwt . '.' . $unique_id;
	}

	/**
	 * Makes a remote GET request to given endpoint. It automatically prepends the API base URL and adds authentication
	 * headers.
	 *
	 * @param string $endpoint_url Relative endpoint URL.
	 *
	 * @return array|\WP_Error
	 */
	protected function remote_get( $endpoint_url ) {

		$auth_header = $this->get_auth_header();
		if ( is_wp_error( $auth_header ) ) {
			return $auth_header;
		}

		return wp_remote_get(
			trailingslashit( $this->get_saas_url() ) . $endpoint_url,
			[
				'sslverify' => false,
				'timeout'   => $this->get_max_timeout(),
				'headers'   => [
					self::AUTH_HEADER_NAME => $auth_header,
					'Accept'               => 'application/json',
				],
			]
		);
	}

	/**
	 * Makes a remote POST request to given endpoint. It automatically prepends the API base URL, adds authentication
	 * headers and include the parameters in the request body.
	 *
	 * @param string $endpoint_url Relative endpoint URL.
	 * @param array  $params       Optional. Parameters needed to be sent in the body. Default: [].
	 *
	 * @return array|\WP_Error
	 */
	protected function remote_post( $endpoint_url, $params ) {

		$auth_header = $this->get_auth_header();
		if ( is_wp_error( $auth_header ) ) {
			return $auth_header;
		}

		return wp_remote_post(
			trailingslashit( $this->get_saas_url() ) . $endpoint_url,
			[
				'body'      => $params,
				'sslverify' => false,
				'timeout'   => $this->get_max_timeout(),
				'headers'   => [
					self::AUTH_HEADER_NAME => $auth_header,
					'Accept'               => 'application/json',
				],
			]
		);
	}

	/**
	 * Get the status of response.
	 *
	 * @since 3.6
	 *
	 * @param int      $response_code Response code to check success or failure.
	 * @param stdClass $response_data Object of data returned from request.
	 *
	 * @return bool success or failed.
	 */
	protected function get_response_success( $response_code, $response_data ) {
		return (
			200 === $response_code
			&&
			! empty( $response_data ) // @phpstan-ignore-line
			&&
			(
				(
					isset( $response_data->status )
					&&
					200 === $response_data->status
				)
				||
				(
					isset( $response_data->data )
					&&
					isset( $response_data->data->id )
				)
			)
		);
	}

	/**
	 * Get response status code/number.
	 *
	 * @since 3.6
	 *
	 * @param array|WP_Error $response The response or WP_Error on failure.
	 * @param null|int       $status   Optional. Status code to overwrite the response status. Default: null.
	 *
	 * @return int status code|number of response.
	 */
	protected function get_response_status( $response, $status = null ) {
		if ( ! is_null( $status ) ) {
			return (int) $status;
		}

		return (int) wp_remote_retrieve_response_code( $response );
	}

	/**
	 * Get response data from the API.
	 *
	 * @since 3.6
	 *
	 * @param array|WP_Error $response The response or WP_Error on failure.
	 *
	 * @return mixed response of API.
	 */
	protected function get_response_data( $response ) {
		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	/**
	 * Generate JWT.
	 *
	 * @return string
	 */
	public function get_jwt() {
		if ( is_null( $this->jwt ) ) { // @phpstan-ignore-line

			$signature_data = $this->get_jwt_secret();
			$unique_id      = $this->get_awp_unique_id();
			$payload        = [
				'iss' => defined( 'WP_HOME' ) ? WP_HOME : (string) get_option( 'home' ), // @phpstan-ignore-line
				'sub' => $unique_id,
				'exp' => time() + 1200, // 20 min
			];

			$this->jwt = JWT::encode(
				$payload,
				$signature_data['key'] . $unique_id,
				'HS256'
			);

		}

		return $this->jwt;
	}

	/**
	 * Get JWT secret from ini file
	 *
	 * @return array
	 */
	public function get_jwt_secret() {
		return [ 'key' => WP_ROCKET_SAAS_KEY ];
	}

	/**
	 * Get saas url.
	 *
	 * @since 3.12.6.1_1.1-2 Updated to automatically detect staging mode.
	 *
	 * @return string
	 */
	public function get_saas_url() {
		$url = 'https://awp-saas.cloudlinux.com/';

		if ( @file_exists( '/opt/cloudlinux/staging_mode' ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$url = 'https://awp-saas.staging.cloudlinux.com/';
		}

		if ( defined( 'AWP_SAAS_URL' ) ) {
			$url = AWP_SAAS_URL;
		}

		return trailingslashit( $url );
	}

	/**
	 * Retrieves the AccelerateWP unique ID to be used for authenticating against the API.
	 *
	 * @since 3.12.6.1_1.1-1
	 *
	 * @return string
	 */
	abstract protected function get_awp_unique_id();

	/**
	 * Builds the timeout limit for queries talking with the SaaS service.
	 *
	 * Based on local php max_execution_time in php.ini
	 *
	 * @since 3.12.6.1_1.1-1
	 * @return int
	 **/
	protected function get_max_timeout() {
		$timeout = (int) ini_get( 'max_execution_time' );

		// Ensure exec time set in php.ini.
		if ( ! $timeout ) {
			$timeout = 30;
		}

		return (int) ceil( $timeout * 0.8 );
	}

	/**
	 * Validates the API configuration and generates a list of errors if any.
	 *
	 * @return WP_Error[] List of errors.
	 */
	public function get_configuration_errors() {

		$result = [];

		// Check if the unique ID is set.
		if ( 0 === strlen( $this->get_awp_unique_id() ) ) {
			$result[] = $this->build_error_for_missing_unique_id();
		}

		return $result;
	}

	/**
	 * Builds an error for a missing unique ID.
	 *
	 * @return WP_Error
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	private function build_error_for_missing_unique_id() {
		return Sentry::enrich_wp_error(
			new WP_Error(
				'apw_sass_api_unique_id_missing',
				esc_html__( 'Unique ID is missing', 'rocket' )
			)
		);
	}
}
