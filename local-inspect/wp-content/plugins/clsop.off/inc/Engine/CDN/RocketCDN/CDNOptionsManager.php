<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */
namespace WP_Rocket\Engine\CDN\RocketCDN;

use WP_Rocket\Admin\Options;
use WP_Rocket\Admin\Options_Data;

/**
 * Manager for WP Rocket CDN options
 *
 * @note CL
 * @since 3.5
 */
class CDNOptionsManager {
	/**
	 * WP Options API instance
	 *
	 * @var Options
	 */
	private $options_api;

	/**
	 * WP Rocket Options instance
	 *
	 * @var Options_Data
	 */
	private $options;

	/**
	 * Constructor
	 *
	 * @param Options      $options_api WP Options API instance.
	 * @param Options_Data $options     WP Rocket Options instance.
	 */
	public function __construct( Options $options_api, Options_Data $options ) {
		$this->options_api = $options_api;
		$this->options     = $options;
	}

	/**
	 * Enable CDN option, save CDN URL & delete RocketCDN status transient
	 *
	 * @since 3.5
	 *
	 * @param string $cdn_url CDN URL.
	 * @return void
	 */
	public function enable( $cdn_url ) {
		$this->options->set( 'cdn', 1 );
		$this->options->set( 'cdn_cnames', [ $cdn_url ] );
		$this->options->set( 'cdn_zone', [ 'all' ] );
		$this->options->set( 'cdn_awp_cdn_url', $cdn_url ); // CL.

		$this->options_api->set( 'settings', $this->options->get_options() );

		delete_transient( 'rocketcdn_status' );
		rocket_clean_domain();
	}

	/**
	 * Disable CDN option, remove CDN URL & user token, delete RocketCDN status transient
	 *
	 * @since 3.5
	 *
	 * @return void
	 */
	public function disable() {
		$this->options->set( 'cdn', 0 );
		$this->options->set( 'cdn_cnames', [] );
		$this->options->set( 'cdn_zone', [] );
		$this->options->set( 'cdn_awp_cdn_url', '' ); // CL.

		$this->options_api->set( 'settings', $this->options->get_options() );

		delete_option( 'rocketcdn_user_token' );
		delete_transient( 'rocketcdn_status' );
		rocket_clean_domain();
	}

	/**
	 * AccelerateWP data
	 *
	 * @param  int    $account_id  Account id.
	 * @param  string $api_key  Api key.
	 *
	 * @note CL.
	 * @return void
	 */
	public function awp( $account_id, $api_key ) {
		$this->options->set( 'cdn_awp_account_id', $account_id );
		$this->options->set( 'cdn_awp_api_key', $api_key );

		$this->options_api->set( 'settings', $this->options->get_options() );
	}

	/**
	 * Get current CDN status.
	 *
	 * @note CL.
	 * @return int
	 */
	public function get_cdn() {
		return $this->options->get( 'cdn', 0 );
	}

	/**
	 * Get current CDN cnames.
	 *
	 * @return array
	 */
	public function get_cdn_cnames() {
		return $this->options->get( 'cdn_cnames', [] );
	}

	/**
	 * Get AWP account id.
	 *
	 * @note CL.
	 * @return int
	 */
	public function get_awp_account_id() {
		return $this->options->get( 'cdn_awp_account_id', 0 );
	}

	/**
	 * Get AWP cdn url.
	 *
	 * @note CL.
	 * @return string
	 */
	public function get_awp_cdn_url() {
		return $this->options->get( 'cdn_awp_cdn_url', '' );
	}

	/**
	 * Get AWP cdn api key.
	 *
	 * @note CL.
	 * @return string
	 */
	public function get_awp_cdn_api_key() {
		return $this->options->get( 'cdn_awp_api_key', '' );
	}
}
