<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Cli;

use Exception;

/**
 * AccelerateWp Check CDN
 */
class AccelerateWpCdnCheck {

	/**
	 * Url for checking.
	 *
	 * @var null|string
	 */
	protected $url = null;

	/**
	 * Http response code.
	 *
	 * @var null|int|string
	 */
	protected $url_code = null;

	/**
	 * Static css for checking.
	 *
	 * @var null|string
	 */
	protected $static_url = null;

	/**
	 * Http response code.
	 *
	 * @var null|int|string
	 */
	protected $static_code = null;

	/**
	 * Advice ID.
	 *
	 * @var null|int
	 */
	protected $advice_id = null;

	/**
	 * Error reason.
	 *
	 * @var string
	 */
	protected $reason = null;

	/**
	 * AWP Account ID.
	 *
	 * @var int
	 */
	protected $account_id;

	/**
	 * AWP CDN url.
	 *
	 * @var string
	 */
	protected $cdn_url;

	/**
	 * Constructor.
	 *
	 * @param int    $account_id Account id.
	 * @param string $cdn_url Cdn url.
	 */
	public function __construct( $account_id, $cdn_url ) {
		$this->account_id = $account_id;
		$this->cdn_url    = $cdn_url;
	}

	/**
	 * Get account id.
	 *
	 * @return int
	 */
	public function account_id() {
		return $this->account_id;
	}

	/**
	 * Get cdn url.
	 *
	 * @return string
	 */
	public function cdn_url() {
		return $this->cdn_url;
	}

	/**
	 * Find advice.
	 *
	 * @return array|null
	 */
	protected function advice() {
		$smart_advice_data = get_option( 'cl_smart_advice' );

		if ( is_array( $smart_advice_data ) && array_key_exists( 'advices', $smart_advice_data ) ) {
			foreach ( $smart_advice_data['advices'] as $advice ) {
				if ( 'CDN' === $advice['type'] ) {
					return $advice;
				}
			}
		}

		return null;
	}

	/**
	 * Request to url.
	 *
	 * @param string $url Webpage.
	 *
	 * @return string
	 */
	protected function url_request( $url ) {
		$response = wp_remote_get(
			$url,
			[
				'timeout'   => 10,
				'sslverify' => false,
			]
		);

		$this->url_code = $this->url_code( $response );

		return $this->url_body( $response );
	}

	/**
	 * Url response code.
	 *
	 * @param array|\WP_Error $response The response or WP_Error on failure.
	 *
	 * @return int|string
	 */
	protected function url_code( $response ) {
		if ( is_wp_error( $response ) ) {
			return $response->get_error_message();
		} else {
			return wp_remote_retrieve_response_code( $response );
		}
	}

	/**
	 * Url response body.
	 *
	 * @param array $response request.
	 *
	 * @return string
	 */
	protected function url_body( $response ) {
		return wp_remote_retrieve_body( $response );
	}

	/**
	 * Request to static.
	 *
	 * @param string $url Css file.
	 *
	 * @return void
	 */
	protected function static_request( $url ) {
		if ( strpos( $url, 'http' ) === false ) {
			$parse  = wp_parse_url( $this->url );
			$scheme = $parse['scheme'] ?? 'http';
			$url    = $scheme . '://' . ltrim( $url, '/' );
		}

		$response = wp_remote_get(
			$url,
			[
				'timeout' => 2,
			]
		);

		$this->static_code = $this->static_code( $response );
	}

	/**
	 * Static response code.
	 *
	 * @param array $response request.
	 *
	 * @return int
	 */
	protected function static_code( $response ) {
		return $this->url_code( $response );
	}

	/**
	 * Fine AWP Debug.
	 *
	 * @param string $body For parse.
	 *
	 * @return string|null
	 */
	protected function find_reason( $body ) {
		preg_match_all( '/<!--\sAccelerateWP Debug:\s(.*)\s-->/mi', $body, $matches );

		if ( array_key_exists( 1, $matches ) && array_key_exists( 0, $matches[1] ) ) {
			return (string) $matches[1][0];
		}

		return null;
	}

	/**
	 * Find first css file.
	 *
	 * @param string $body For parse.
	 *
	 * @return string|null
	 */
	public function find_static( $body ) {
		preg_match_all( '/\/\/' . preg_quote( $this->cdn_url(), '/' ) . '\/\S+\.css[\'"?]/mi', $body, $matches );
		if ( array_key_exists( 0, $matches ) ) {
			foreach ( $matches[0] as $url ) {
				$url = rtrim( $url, '\'"?' );
				if ( ! empty( $url ) ) {
					return $url;
				}
			}
		}

		return null;
	}

	/**
	 * Cdn post-check.
	 *
	 * @return bool
	 * @throws Exception Something went wrong.
	 */
	public function check() {
		$this->url = defined( 'WP_HOME' ) ? WP_HOME : get_home_url();

		$advice = $this->advice();
		if ( ! empty( $advice ) ) {
			if ( array_key_exists( 'requests', $advice ) && is_array( $advice['requests'] ) && ! empty( $advice['requests'] ) ) {
				$this->advice_id = $advice['id'];
				$this->url       = array_shift( $advice['requests'] );
			}
		}

		$body = $this->url_request( $this->url );

		$this->reason     = $this->find_reason( $body );
		$this->static_url = $this->find_static( $body );

		if ( ! empty( $this->static_url ) ) {
			$this->static_request( $this->static_url );
		}

		if ( false === strpos( $body, $this->cdn_url() ) ) {
			$this->reason = 'CDN url not found on page ' . $this->url;

			return false;
		} elseif ( 200 > $this->url_code || 300 <= $this->url_code ) {
			$this->reason = 'URL ' . $this->url . ' returned unexpected response status (' . $this->url_code . ')';

			return false;
		} elseif ( 200 > $this->static_code || 300 <= $this->static_code ) {
			$this->reason = 'CDN url returned unexpected response status (' . $this->static_code . ')';

			return false;
		}

		return true;
	}

	/**
	 * Reason.
	 *
	 * @return string|null
	 */
	public function reason() {
		return $this->reason;
	}

	/**
	 * Sent report.
	 *
	 * @return void
	 *
	 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	 */
	public function report() {
		do_action(
			'accelerate_wp_set_error',
			E_WARNING,
			'AccelerateWP CDN pos-check failed',
			__FILE__,
			__LINE__,
			[
				'url'         => $this->url,
				'url_code'    => $this->url_code,
				'static_url'  => $this->static_url,
				'static_code' => $this->static_code,
				'advice_id'   => $this->advice_id,
				'reason'      => $this->reason,
				'account_id'  => $this->account_id(),
				'cdn_url'     => $this->cdn_url(),
			]
		);
	}
}
