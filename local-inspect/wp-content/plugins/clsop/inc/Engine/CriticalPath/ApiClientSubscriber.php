<?php
namespace WP_Rocket\Engine\CriticalPath;

use Exception;
use WP_Rocket\Event_Management\Subscriber_Interface;

/**
 * Subscriber for the ApiClient
 *
 * @note CL
 */
class ApiClientSubscriber implements Subscriber_Interface {
	/**
	 * RocketCPCSS API Client instance.
	 *
	 * @var APIClient
	 */
	private $api_client;

	/**
	 * CCSSOptionsManager instance
	 *
	 * @var CCSSAWPOptionsManager
	 * @phpstan-ignore-next-line
	 */
	private $ccss_options;

	/**
	 * Constructor
	 *
	 * @param APIClient             $api_client   RocketCPCSS API Client instance.
	 * @param CCSSAWPOptionsManager $ccss_options CCSSOptionsManager instance.
	 */
	public function __construct( APIClient $api_client, CCSSAWPOptionsManager $ccss_options ) {
		$this->api_client   = $api_client;
		$this->ccss_options = $ccss_options;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_subscribed_events() {
		return [
			'rocket_cpcss_accelerate_wp_cli_enable'  => [ 'cli_cpcss_enable', 10, 1 ],
			'rocket_cpcss_accelerate_wp_cli_disable' => 'cli_cpcss_disable',
		];
	}

	/**
	 * Enable the CPCSS
	 *
	 * @param string $unique_id Unique CLN user id.
	 *
	 * @return void
	 * @throws Exception Data error.
	 */
	public function cli_cpcss_enable( $unique_id ) {
		$this->api_client->enable( $unique_id );
	}

	/**
	 * Disable the CPCSS
	 *
	 * @return bool
	 */
	public function cli_cpcss_disable() {
		$this->api_client->disable();

		return true;
	}
}
