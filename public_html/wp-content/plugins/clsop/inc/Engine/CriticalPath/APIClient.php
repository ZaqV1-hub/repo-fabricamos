<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */

namespace WP_Rocket\Engine\CriticalPath;

use stdClass;
use WP_Error;
use WP_Rocket\Engine\AccelerateWp\AbstractAPIClient;

class APIClient extends AbstractAPIClient {

	/**
	 * Critical Path API endpoint.
	 *
	 * @var string
	 */
	const ENDPOINT = 'v1/critical-path-css'; // CL.

	/**
	 * CCSSOptionsManager instance
	 *
	 * @note CL.
	 * @var CCSSAWPOptionsManager
	 */
	private $ccss_options;

	/**
	 * Constructor
	 *
	 * @note CL.
	 * @param  CCSSAWPOptionsManager $ccss_options  CCSSOptionsManager instance.
	 */
	public function __construct( CCSSAWPOptionsManager $ccss_options ) {
		$this->ccss_options = $ccss_options;
	}

	/**
	 * Get CCSS Awp Option Manager instance.
	 *
	 * @note CL.
	 * @return CCSSAWPOptionsManager
	 */
	protected function ccssOptions() {
		return $this->ccss_options;
	}

	/**
	 * Sends a generation request to the Critical Path API.
	 *
	 * @since 3.6
	 *
	 * @note CL.
	 * @param string $url    The URL to send a CPCSS generation request for.
	 * @param array  $params Optional. Parameters needed to be sent in the body. Default: [].
	 * @param string $item_type Optional. Type for this item if it's custom or specific type. Default: custom.
	 * @return object|array|WP_Error
	 */
	public function send_generation_request( $url, $params = [], $item_type = 'custom' ) {
		$params['url'] = $url;
		$is_mobile     = isset( $params['mobile'] ) && $params['mobile'];

		/**
		 * Filters the parameters sent to the Critical CSS generator API.
		 *
		 * @since 2.11
		 *
		 * @param array $params An array of parameters to send to the API.
		 */
		$filtered_params = apply_filters( 'rocket_cpcss_job_request', $params );

		$response = $this->remote_post( self::ENDPOINT, $filtered_params );

		return $this->prepare_response( $response, $url, $is_mobile, $item_type );
	}

	/**
	 * Prepare the response to be returned.
	 *
	 * @since 3.6
	 *
	 * @param array|WP_Error $response  The response or WP_Error on failure.
	 * @param string         $url       Url to be checked.
	 * @param bool           $is_mobile Optional. Flag for if this is cpcss for mobile or not. Default: false.
	 * @param string         $item_type Optional. Type for this item if it's custom or specific type. Default: custom.
	 *
	 * @return array|WP_Error
	 */
	private function prepare_response( $response, $url, $is_mobile = false, $item_type = 'custom' ) {

		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				$this->get_response_code( $response ),
				sprintf(
					// translators: %1$s = type of content, %2$s = error message.
					__( 'Critical CSS for %1$s not generated. Error: %2$s', 'rocket' ),
					( 'custom' === $item_type ) ? $url : $item_type,
					$response->get_error_message()
				),
				[
					'status' => 400,
				]
			);
		}

		$response_data        = $this->get_response_data( $response );
		$response_status_code = $this->get_response_status( $response, ( isset( $response_data->status ) ) ? $response_data->status : null );
		$succeeded            = $this->get_response_success( $response_status_code, $response_data );

		if ( $succeeded ) {
			return $response_data;
		}

		$response_message = $this->get_response_message( $response_status_code, $response_data, $url, $is_mobile, $item_type );

		if ( 200 === $response_status_code ) {
			$response_status_code = 400;
		}

		return new WP_Error(
			$this->get_response_code( $response ),
			$response_message,
			[
				'status' => $response_status_code,
			]
		);
	}

	/**
	 * Get response message.
	 *
	 * @since 3.6
	 *
	 * @param int      $response_status_code Response status code.
	 * @param stdClass $response_data        Object of data returned from request.
	 * @param string   $url                  Url for the web page to be checked.
	 * @param bool     $is_mobile            Optional. Flag for if this is cpcss for mobile or not. Default: false.
	 * @param string   $item_type Optional. Type for this item if it's custom or specific type. Default: custom.
	 *
	 * @return string
	 */
	private function get_response_message( $response_status_code, $response_data, $url, $is_mobile = false, $item_type = 'custom' ) {
		$message = '';

		switch ( $response_status_code ) {
			case 200:
				if ( ! isset( $response_data->data->id ) ) {
					$message .= sprintf(
						$is_mobile
							?
							// translators: %s = item URL.
							__( 'Critical CSS for %1$s on mobile not generated. Error: The API returned an empty response.', 'rocket' )
							:
							// translators: %s = item URL.
							__( 'Critical CSS for %1$s not generated. Error: The API returned an empty response.', 'rocket' ),
						( 'custom' === $item_type ) ? $url : $item_type
					);
				}
				break;
			case 403: // CL.
			case 400:
			case 440:
			case 404:
				// translators: %s = item URL.
				$message .= sprintf(
					$is_mobile
						// translators: %s = item URL.
						? __( 'Critical CSS for %1$s on mobile not generated.', 'rocket' )
						// translators: %s = item URL.
						: __( 'Critical CSS for %1$s not generated.', 'rocket' ),
					( 'custom' === $item_type ) ? $url : $item_type
					);
				break;
			default:
				$message .= sprintf(
					$is_mobile
						// translators: %s = URL.
						? __( 'Critical CSS for %1$s on mobile not generated. Error: The API returned an invalid response code.', 'rocket' )
						// translators: %s = URL.
						: __( 'Critical CSS for %1$s not generated. Error: The API returned an invalid response code.', 'rocket' ),
					( 'custom' === $item_type ) ? $url : $item_type
				);
				break;
		}

		if ( isset( $response_data->message ) ) {
			// translators: %1$s = error message.
			$message .= ' ' . sprintf( __( 'Error: %1$s', 'rocket' ), $response_data->message );
		}

		// CL.
		if ( isset( $response_data->data ) && isset( $response_data->data->usage_count ) ) {
			// translators: %1$s = error message.
			$message .= ' ' . sprintf( __( 'Total %1$s requests processed.', 'rocket' ), $response_data->data->usage_count );
		}

		return $message;
	}

	/**
	 * Get our internal response code [Not the standard HTTP codes].
	 *
	 * @since 3.6
	 *
	 * @param array|WP_Error $response The response or WP_Error on failure.
	 *
	 * @return string response code.
	 */
	private function get_response_code( $response ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found
		// Todo: we can return code based on the response status number, for example 404 not_found.
		return 'cpcss_generation_failed';
	}

	/**
	 * Get job details by calling API with job ID.
	 *
	 * @since 3.6
	 *
	 * @param string $job_id    ID for the job to get details.
	 * @param string $url       URL to be used in error messages.
	 * @param bool   $is_mobile Optional. Flag for if this is cpcss for mobile or not. Default: false.
	 * @param string $item_type Optional. Type for this item if it's custom or specific type. Default: custom.
	 *
	 * @return mixed|WP_Error Details for job.
	 */
	public function get_job_details( $job_id, $url, $is_mobile = false, $item_type = 'custom' ) {
		$response = $this->remote_get( self::ENDPOINT . "/$job_id/" );// CL.

		return $this->prepare_response( $response, $url, $is_mobile, $item_type );
	}

	/**
	 * Enable CPCSS & Asynchronous CSS loading
	 *
	 * @param string $unique_id Unique CLN user id.
	 *
	 * @note CL.
	 * @return void
	 */
	public function enable( $unique_id ) {
		$this->ccssOptions()->enable( $unique_id );
	}

	/**
	 * Disable CPCSS & Asynchronous CSS loading
	 *
	 * @note CL.
	 * @return bool
	 */
	public function disable() {
		$this->ccssOptions()->disable();

		return true;
	}

	/**
	 * Retrieves the AccelerateWP unique ID to be used for authenticating against the API.
	 *
	 * @note CL.
	 * @return string
	 */
	protected function get_awp_unique_id() {
		return $this->ccssOptions()->get_awp_unique_id();
	}
}
