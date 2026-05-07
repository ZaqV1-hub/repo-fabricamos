<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Engine\AccelerateWp;

/**
 * Sentry
 */
class Sentry {
	/**
	 * Website url.
	 *
	 * @var string
	 */
	private $website = '';

	/**
	 * User.
	 *
	 * @var string
	 */
	private $user = '';

	/**
	 * Request uri.
	 *
	 * @var string
	 */
	private $request_uri = '';

	/**
	 * Http code.
	 *
	 * @var int
	 */
	private $http_code = 0;

	/**
	 * IP address.
	 *
	 * @var string
	 */
	private $ip_address = '';

	/**
	 * Error codes.
	 *
	 * @var array<string>
	 *
	 * PHP Core Exceptions
	 */
	public $codes = [
		E_ERROR             => 'E_ERROR',
		E_WARNING           => 'E_WARNING',
		E_PARSE             => 'E_PARSE',
		E_NOTICE            => 'E_NOTICE',
		E_CORE_ERROR        => 'E_CORE_ERROR',
		E_CORE_WARNING      => 'E_CORE_WARNING',
		E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
		E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
		E_USER_ERROR        => 'E_USER_ERROR',
		E_USER_WARNING      => 'E_USER_WARNING',
		E_USER_NOTICE       => 'E_USER_NOTICE',
		E_STRICT            => 'E_STRICT',
		E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		E_DEPRECATED        => 'E_DEPRECATED',
		E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
		E_ALL               => 'E_ALL',
	];

	/**
	 * Get constant home.
	 *
	 * @return string
	 */
	protected function wp_home_constant() {
		if ( defined( 'WP_HOME' ) && WP_HOME ) {
			return (string) WP_HOME;
		}

		return '';
	}

	/**
	 * Get option home.
	 *
	 * @return string
	 */
	protected function wp_home_option() {
		if ( function_exists( 'get_option' ) ) {
			// @phpstan-ignore-next-line
			$home = get_option( 'home' );
			if ( is_string( $home ) ) {
				return $home;
			}
		}

		return '';
	}

	/**
	 * Get website.
	 *
	 * @return string
	 */
	public function website() {
		if ( ! empty( $this->website ) ) {
			return $this->website;
		}

		$wp_home_constant = $this->wp_home_constant();
		$wp_home_option   = $this->wp_home_option();

		if ( ! empty( $wp_home_constant ) ) {
			$this->website = $wp_home_constant;
		} elseif ( ! empty( $wp_home_option ) ) {
			$this->website = $wp_home_option;
			// @phpstan-ignore-next-line
		} elseif ( is_array( $_SERVER ) && array_key_exists( 'SERVER_NAME', $_SERVER ) ) {
			$this->website = esc_url_raw( wp_unslash( $_SERVER['SERVER_NAME'] ) );
		}

		return $this->website;
	}

	/**
	 * Get user.
	 *
	 * @return string
	 */
	public function user() {
		if ( ! empty( $this->user ) ) {
			return $this->user;
		}

		$parse = wp_parse_url( $this->website() );
		if ( is_array( $parse ) && array_key_exists( 'host', $parse ) ) {
			$this->user = $parse['host'];
		}

		return $this->user;
	}

	/**
	 * Get request uri.
	 *
	 * @return string
	 */
	public function request_uri() {
		if ( ! empty( $this->request_uri ) ) {
			return $this->request_uri;
		}

		// @phpstan-ignore-next-line
		if ( is_array( $_SERVER ) && array_key_exists( 'REQUEST_URI', $_SERVER ) ) {
			$this->request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}

		return $this->request_uri;
	}

	/**
	 * Get http code.
	 *
	 * @return int
	 */
	public function http_code() {
		if ( ! empty( $this->http_code ) ) {
			return $this->http_code;
		}

		if ( function_exists( 'http_response_code' ) ) {
			$this->http_code = (int) http_response_code();
		}

		return $this->http_code;
	}

	/**
	 * Get max_execution_time PHP setting.
	 *
	 * @return int|false
	 */
	public function max_execution_time() {
		if ( function_exists( 'ini_get' ) ) {
			return (int) ini_get( 'max_execution_time' );
		}
		return false;
	}

	/**
	 * Get current user IP Address.
	 *
	 * @return string
	 */
	public function ip_address() {
		if ( ! empty( $this->ip_address ) ) {
			return $this->ip_address;
		}

		foreach ( [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'SERVER_ADDR' ] as $key ) {
			if ( isset( $_SERVER[ $key ] ) ) {
				$ip_address = filter_var( sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) ), FILTER_VALIDATE_IP );
				if ( is_string( $ip_address ) ) {
					$this->ip_address = $ip_address;

					return $this->ip_address;
				}
			}
		}

		if ( function_exists( 'gethostbyname' ) && function_exists( 'gethostname' ) ) {
			$hostname = gethostname();
			if ( is_string( $hostname ) ) {
				$ip = gethostbyname( $hostname );
				if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
					$this->ip_address = $ip;
				}
			}
		}

		return $this->ip_address;
	}

	/**
	 * Send error.
	 *
	 * @since 3.12.6.1_1.1-2 Prevented logs from being sent in testing mode or if disabled using a dedicated constant.
	 *
	 * @param int     $errno number.
	 * @param string  $errstr message.
	 * @param ?string $errfile file.
	 * @param ?int    $errline line.
	 * @param array   $extra line.
	 * @param array   $tags data.
	 *
	 * @return void
	 */
	public function error( $errno, $errstr, $errfile = null, $errline = null, $extra = [], $tags = [] ) {

		if ( $this->is_logging_disabled() || $this->is_testing() ) {
			return;
		}

		$data = $this->data( $errno, $errstr, $errfile, $errline, $extra, $tags );
		$this->send( $data );
	}

	/**
	 * Checks if logging to Sentry is disabled. Based on the WP_ROCKET_DISABLE_SENTRY_LOGGING constant.
	 *
	 * @since 3.12.6.1_1.1-2
	 *
	 * @return bool True if logging is disabled, false otherwise.
	 */
	public function is_logging_disabled() {
		return defined( 'WP_ROCKET_DISABLE_SENTRY_LOGGING' ) && WP_ROCKET_DISABLE_SENTRY_LOGGING;
	}

	/**
	 * Checks if plugin is in testing mode. Based on the WP_ROCKET_IS_TESTING constant.
	 *
	 * @since 3.12.6.1_1.1-2
	 *
	 * @return bool True if plugin is in testing mode, false otherwise.
	 */
	public function is_testing() {
		return defined( 'WP_ROCKET_IS_TESTING' ) && WP_ROCKET_IS_TESTING;
	}

	/**
	 * Data.
	 *
	 * @param int     $errno number.
	 * @param string  $errstr message.
	 * @param ?string $errfile file.
	 * @param ?int    $errline line.
	 * @param array   $extra data.
	 * @param array   $tags tags.
	 *
	 * @return array
	 */
	public function data( $errno, $errstr, $errfile = null, $errline = null, $extra = [], $tags = [] ) {
		$extra = array_diff(
			array_merge(
				$extra,
				[
					'website'            => $this->website(),
					'request_uri'        => $this->request_uri(),
					'http_code'          => $this->http_code(),
					'max_execution_time' => $this->max_execution_time(),
				]
			),
			[ '' ]
		);

		$tags = array_diff(
			array_merge(
				$tags,
				[
					'php_version'    => phpversion(),
					'plugin_version' => defined( 'WP_ROCKET_VERSION' ) ? WP_ROCKET_VERSION : null,
				]
			),
			[ '' ]
		);

		$stack_trace = [
			'frames'         => [
				[
					'filename' => $errfile,
					'lineno'   => $errline,
				],
			],
			'frames_omitted' => null,
		];

		if ( isset( $extra['error_data']['stack_trace'] ) ) {

			$frames = [];
			while ( ! empty( $extra['error_data']['stack_trace'] ) ) {

				$frame    = array_pop( $extra['error_data']['stack_trace'] );
				$frames[] = [
					'filename' => $frame['file'],
					'lineno'   => $frame['line'],
				];

			}

			$stack_trace['frames'] = $frames;
			unset( $extra['error_data']['stack_trace'] );
		}

		$user = [
			'username' => $this->user(),
		];

		$ip_address = $this->ip_address();
		if ( ! empty( $ip_address ) ) {
			$user['ip_address'] = $ip_address;
		}

		return [
			'extra'                       => $extra,
			'tags'                        => $tags,
			'user'                        => $user,
			'release'                     => 'php-accelerate-wp-plugin@' . $tags['plugin_version'],
			'sentry.interfaces.Exception' => [
				'exc_omitted' => null,
				'values'      => [
					[
						'stacktrace' => $stack_trace,
						'type'       => isset( $this->codes[ $errno ] ) ? $this->codes[ $errno ] : 'Undefined: ' . $errno,
						'value'      => $errstr,
					],
				],
			],
		];
	}

	/**
	 * Send to Sentry.
	 *
	 * @since 3.12.6.1_1.1-2 Updated to automatically detect staging mode.
	 *
	 * @param array $body send.
	 *
	 * @return string|false
	 */
	public function send( $body ) {
		if ( ! function_exists( 'curl_init' ) || empty( $body ) ) {
			return false;
		}

		$sentry_key = '0eb3f13fc862441aa1cd2f47ed9091d4';
		$project_id = 25;

		if ( @file_exists( '/opt/cloudlinux/staging_mode' ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$sentry_key = '849b819df9654e6e84e4d43a391065b2';
			$project_id = 32;
		}

		$url     = 'https://' . $sentry_key . '@cl.sentry.cloudlinux.com/api/' . $project_id . '/store/';
		$headers = [
			'Content-Type: application/json',
			'X-Sentry-Auth: Sentry sentry_version=7,sentry_timestamp=' . time() . ',sentry_client=php-curl/1.0,sentry_key=' . $sentry_key,
		];

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, wp_json_encode( $body ) );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 1 );
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		$response = curl_exec( $ch );
		curl_close( $ch );

		return $response;
	}

	/**
	 * Adds the file name and line number from stack trace to the error data.
	 *
	 * @since 3.12.6.1_1.1-2
	 *
	 * @param \WP_Error $wp_error WordPress error.
	 *
	 * @return \WP_Error Update WordPress error.
	 *
	 * @phpcs:disable PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection
	 */
	public static function enrich_wp_error( \WP_Error $wp_error ) {
		if ( ! function_exists( 'debug_backtrace' ) ) {
			return $wp_error;
		}

		// Grab existing error data.
		$code       = $wp_error->get_error_code();
		$error_data = $wp_error->get_error_data( $code ) ?? [];
		if ( is_string( $error_data ) ) {
			$error_data = [];
		}

		$backtrace = debug_backtrace(); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace

		// Remove the first element of the backtrace as it's the current function.
		array_shift( $backtrace );

		// Append the stack trace to the error data.
		$error_data = array_merge(
			$error_data,
			[
				'stack_trace' => $backtrace,
			]
		);

		// @phpstan-ignore-next-line
		if ( ! is_array( $wp_error->error_data ) ) {
			$wp_error->error_data = [];
		}

		$wp_error->error_data[ $code ] = $error_data;

		return $wp_error;
	}
}
