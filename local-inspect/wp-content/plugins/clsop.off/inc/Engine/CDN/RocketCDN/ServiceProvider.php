<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */
declare(strict_types=1);

namespace WP_Rocket\Engine\CDN\RocketCDN;

use WP_Rocket\Dependencies\League\Container\Argument\Literal\StringArgument;
use WP_Rocket\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;
use WP_Rocket\Engine\AccelerateWp\ApiSocket;

/**
 * Service provider for RocketCDN
 */
class ServiceProvider extends AbstractServiceProvider {
	/**
	 * Array of services provided by this service provider
	 *
	 * @var array
	 */
	protected $provides = [
		'rocketcdn_api_client',
		'rocketcdn_options_manager',
		'rocketcdn_data_manager_subscriber',
		'rocketcdn_rest_subscriber',
		'rocketcdn_admin_subscriber',
		'rocketcdn_notices_subscriber',
		'rocketcdn_api_client_subscriber', // CL.
		'awp_socket_client', // CL.
		'rocketcdn_mail_subscriber', // CL.
	];

	/**
	 * Check if the service provider provides a specific service.
	 *
	 * @param string $id The id of the service.
	 *
	 * @return bool
	 */
	public function provides( string $id ): bool {
		return in_array( $id, $this->provides, true );
	}

	/**
	 * Registers items with the container
	 *
	 * @note CL.
	 * @return void
	 */
	public function register(): void {
		// CL. RocketCDN CDN options manager.
		$this->getContainer()->add( 'rocketcdn_options_manager', CDNOptionsManager::class )
			->addArguments(
				[
					'options_api',
					'options',
				]
			);

		// CL. AWP Socket.
		$this->getContainer()->add( 'awp_socket_client', ApiSocket::class );

		// CL. RocketCDN API Client.
		$this->getContainer()->add( 'rocketcdn_api_client', APIClient::class )
			->addArguments(
				[
					'rocketcdn_options_manager',
					'awp_socket_client',
				]
			);

		// RocketCDN Data manager subscriber.
		$this->getContainer()->addShared( 'rocketcdn_data_manager_subscriber', DataManagerSubscriber::class )
			->addArguments(
				[
					'rocketcdn_api_client',
					'rocketcdn_options_manager',
					'options',
					'options_api',
				]
			);

		// CL. RocketCDN REST API Subscriber.
		$this->getContainer()->addShared( 'rocketcdn_rest_subscriber', RESTSubscriber::class )
			->addArguments(
				[
					'rocketcdn_options_manager',
					'options',
					'rocketcdn_api_client',
				]
			);

		// RocketCDN Notices Subscriber.
		$this->getContainer()->addShared( 'rocketcdn_notices_subscriber', NoticesSubscriber::class )
			->addArguments(
				[
					'rocketcdn_api_client',
					'beacon',
					new StringArgument( __DIR__ . '/views' ),
				]
			);
		// RocketCDN settings page subscriber.
		$this->getContainer()->addShared( 'rocketcdn_admin_subscriber', AdminPageSubscriber::class )
			->addArguments(
				[
					'rocketcdn_api_client',
					'options',
					'beacon',
					new StringArgument( __DIR__ . '/views' ),
				]
			);

		// CL. ApiClient subscriber.
		$this->getContainer()->addShared( 'rocketcdn_api_client_subscriber', ApiClientSubscriber::class )
			->addArguments(
				[
					'rocketcdn_api_client',
				]
			);

		// CL. CDN Mailer.
		$this->getContainer()->addShared( 'rocketcdn_mail_subscriber', MailSubscriber::class )
			->addArguments(
				[
					new StringArgument( __DIR__ . '/views' ),
				]
			);
	}
}
