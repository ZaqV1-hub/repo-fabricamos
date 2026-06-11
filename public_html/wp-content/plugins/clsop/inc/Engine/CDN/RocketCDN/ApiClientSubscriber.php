<?php
namespace WP_Rocket\Engine\CDN\RocketCDN;

use Exception;
use WP_Rocket\Event_Management\Subscriber_Interface;

/**
 * Subscriber for the ApiClient
 *
 * @note CL
 */
class ApiClientSubscriber implements Subscriber_Interface {
	/**
	 * RocketCDN API Client instance.
	 *
	 * @var APIClient
	 */
	private $api_client;

	/**
	 * Constructor
	 *
	 * @param APIClient $api_client    RocketCDN API Client instance.
	 */
	public function __construct( APIClient $api_client ) {
		$this->api_client = $api_client;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_subscribed_events() {
		return [
			'rocketcdn_accelerate_wp_cli_cdn_enable'  => [ 'cli_cdn_enable', 10, 3 ],
			'rocketcdn_accelerate_wp_cli_cdn_disable' => 'cli_cdn_disable',
			'rocketcdn_accelerate_wp_purge_cache'     => 'purge_cache',
		];
	}

	/**
	 * Enable CDN and add PullZone URL to WP Rocket options
	 *
	 * @param  int    $account_id  Account id.
	 * @param  string $cdn_url  Cdn url.
	 * @param  string $api_key  Api key.
	 *
	 * @return void
	 * @throws Exception Data error.
	 */
	public function cli_cdn_enable( int $account_id, string $cdn_url, string $api_key ) {
		$this->api_client->enable( $account_id, $cdn_url, $api_key );
	}

	/**
	 * Disable the CDN and remove the RocketCDN URL from WP Rocket options
	 *
	 * @return bool
	 */
	public function cli_cdn_disable() {
		$this->api_client->disable();

		return true;
	}

	/**
	 * Purge cache.
	 *
	 * @return void
	 */
	public function purge_cache() {
		$this->api_client->purge_cache_request();
	}
}
