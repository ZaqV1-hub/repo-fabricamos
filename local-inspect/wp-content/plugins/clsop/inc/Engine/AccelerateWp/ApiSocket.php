<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Engine\AccelerateWp;

/**
 * Class to Interact with the AWP socket
 */
class ApiSocket {
	/**
	 * Sock.
	 *
	 * @var string
	 */
	private $sock = 'unix:///opt/alt/php-xray/run/xray-user.sock';

	/**
	 * Socket resource.
	 *
	 * @var resource
	 */
	private $resource = null;

	/**
	 * Connect.
	 *
	 * @return bool
	 */
	private function connect() {
		$this->resource = stream_socket_client( $this->sock, $error_code, $error_msg, 10 );
		if ( ! $this->resource ) {
			// @phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error, WordPress.Security.EscapeOutput.OutputNotEscaped
			trigger_error( "Can't connect to socket [$error_code]: $error_msg", E_USER_WARNING );

			return false;
		}

		return true;
	}

	/**
	 * Send.
	 *
	 * @param  array $data  params.
	 *
	 * @return bool
	 */
	private function send( $data ) {
		$json = wp_json_encode( $data );
		if ( false === stream_socket_sendto( $this->resource, $json ) ) {
			// @phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error, WordPress.Security.EscapeOutput.OutputNotEscaped
			trigger_error( "Can't send data to " . $this->sock, E_USER_WARNING );

			return false;
		}

		return true;
	}

	/**
	 * Read.
	 *
	 * @return bool|string
	 */
	private function read() {
		$response = stream_get_contents( $this->resource, 4, 0 );
		if ( empty( $response ) ) {
			// @phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error, WordPress.Security.EscapeOutput.OutputNotEscaped
			trigger_error( "Can't read 4b", E_USER_WARNING );

			return false;
		}

		$unpack = unpack( 'N', $response );

		if ( ! is_array( $unpack ) || ! array_key_exists( 1, $unpack ) ) {
			// @phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error, WordPress.Security.EscapeOutput.OutputNotEscaped
			trigger_error( "Can't unpack response", E_USER_WARNING );

			return false;
		}

		$length = $unpack[1];
		$json   = stream_get_contents( $this->resource, $length, 4 );

		if ( false === $json ) {
			// @phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error, WordPress.Security.EscapeOutput.OutputNotEscaped
			trigger_error( "Can't read response", E_USER_WARNING );

			return false;
		}

		return $json;
	}

	/**
	 * Close.
	 *
	 * @return void
	 */
	private function close() {
		if ( is_resource( $this->resource ) ) {
			// @phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
			fclose( $this->resource );
		}
	}

	/**
	 * Exec command.
	 *
	 * @param  array $data  send.
	 * @param  bool  $read  response.
	 *
	 * @return string|bool
	 */
	public function exec( $data, $read = false ) {
		if ( ! function_exists( 'stream_socket_client' ) ) {
			return false;
		}

		$result = false;

		if ( $this->connect() && $this->send( $data ) ) {
			$result = true;

			if ( true === $read ) {
				$result = $this->read();
			}

			$this->close();
		}

		return $result;
	}

	/**
	 * Purge cdn cache.
	 *
	 * @param  string $domain  Domain.
	 * @param  string $website  WordPress path.
	 *
	 * @return string
	 */
	public function purge( $domain, $website ) {
		$data = [
			'runner'  => 'smart_advice',
			'command' => 'awp-cdn-purge',
			'domain'  => $domain,
			'website' => $website,
		];

		return $this->exec( $data, true );
	}

	/**
	 * Get user info.
	 *
	 * @return string
	 */
	public function options() {
		$data = [
			'runner'  => 'smart_advice',
			'command' => 'get-options',
		];

		return $this->exec( $data, true );
	}

	/**
	 * Get plugin info.
	 *
	 * @return string
	 */
	public function plugin_data() {
		$data = [
			'runner'      => 'smart_advice',
			'command'     => 'wp-plugin-data',
			'plugin_name' => WP_ROCKET_PLUGIN_NAME,
		];

		return $this->exec( $data, true );
	}

	/**
	 * Get plugin link.
	 *
	 * @return string
	 */
	public function get_plugin() {
		$data = [
			'runner'         => 'smart_advice',
			'command'        => 'wp-plugin-copy',
			'plugin_name'    => WP_ROCKET_PLUGIN_NAME,
			'tmp_dir'        => get_temp_dir(),
			'plugin_version' => WP_ROCKET_VERSION,
		];

		return $this->exec( $data, true );
	}

	/**
	 * Get limits.
	 *
	 * @return string
	 */
	public function get_limits() {
		$data = [
			'runner'  => 'smart_advice',
			'command' => 'get-limits',
		];

		return $this->exec( $data, true );
	}

	/**
	 * Get usage.
	 *
	 * @return string
	 */
	public function get_usage() {
		$data = [
			'runner'  => 'smart_advice',
			'command' => 'get-usage',
		];

		return $this->exec( $data, true );
	}
}
