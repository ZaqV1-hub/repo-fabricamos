<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */
declare(strict_types=1);

namespace WP_Rocket\Addon;

use WP_Rocket\Addon\ImageOptimization\NoticesHandler as ImageOptimizationNoticesHandler;
use WP_Rocket\Addon\ImageOptimization\RESTWP as ImageOptimizationRestWp;
use WP_Rocket\Addon\ImageOptimization\FileManager as ImageOptimizationFileManager;
use WP_Rocket\Addon\ImageOptimization\FileScanner as ImageOptimizationFileScanner;
use WP_Rocket\Addon\ImageOptimization\Manager as ImageOptimizationManager;
use WP_Rocket\Addon\ImageOptimization\OptionsManager as ImageOptimizationOptionsManager;
use WP_Rocket\Addon\ImageOptimization\FileScannerProcess as ImageOptimizationFileScannerProcess;
use WP_Rocket\Addon\ImageOptimization\QueueWorkerProcess as ImageOptimizationQueueWorkerProcess;
use WP_Rocket\Addon\ImageOptimization\Subscriber as ImageOptimizationSubscriber;
use WP_Rocket\Addon\ImageOptimization\APIClient as ImageOptimizationAPIClient;
use WP_Rocket\Addon\ImageOptimization\Database\Tables\ImageOptimization as ImageOptimizationTable;
use WP_Rocket\Addon\ImageOptimization\Database\Queries\ImageOptimization as ImageOptimizationQuery;

use WP_Rocket\Addon\Sucuri\Subscriber as SucuriSubscriber;
use WP_Rocket\Addon\WebP\AdminSubscriber as WebPAdminSubscriber;
use WP_Rocket\Addon\WebP\Subscriber as WebPSubscriber;
use WP_Rocket\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Service provider for WP Rocket addons.
 */
class ServiceProvider extends AbstractServiceProvider {
	/**
	 * Array of services provided by this service provider
	 *
	 * @var array
	 */
	protected $provides = [
		'sucuri_subscriber',
		'webp_subscriber',
		'webp_admin_subscriber',
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
	 */
	public function register(): void {
		$this->getContainer()->addShared( 'sucuri_subscriber', SucuriSubscriber::class )
			->addArgument( 'options' );

		$this->getContainer()->addShared( 'webp_admin_subscriber', WebPAdminSubscriber::class )
			->addArguments(
				[
					'options',
					'cdn_subscriber',
					'beacon',
				]
			);

		$this->getContainer()->addShared( 'webp_subscriber', WebPSubscriber::class )
			->addArguments(
				[
					'options',
					'options_api',
					'cdn_subscriber',
				]
			);

		// Image Optimization Addon.
		$this->addon_image_optimization();
	}

	/**
	 * Adds Image Optimization Addon into the Container when the addon is enabled.
	 *
	 * @note CL.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	protected function addon_image_optimization() {
		$options = $this->getContainer()->get( 'options' );

		$this->provides[] = 'image_optimization_subscriber';
		$this->provides[] = 'image_optimization_query';
		$this->provides[] = 'image_optimization_table';

		$filesystem    = rocket_direct_filesystem();
		$download_path = rocket_get_constant( 'WP_ROCKET_IMAGE_OPTIMIZATION_DOWNLOAD_PATH' );
		$backup_path   = rocket_get_constant( 'WP_ROCKET_IMAGE_OPTIMIZATION_BACKUP_PATH' );
		$upload_config = wp_upload_dir();
		$source_path   = trailingslashit( WP_CONTENT_DIR );
		$source_folder = str_replace( $source_path, '', $upload_config['basedir'] );
		$source_url    = trailingslashit( rtrim( $upload_config['baseurl'], $source_folder ) );

		$this->getContainer()->addShared( 'image_optimization_table', ImageOptimizationTable::class );

		$table = $this->getContainer()->get( 'image_optimization_table' );

		$this->getContainer()->addShared( 'image_optimization_query', ImageOptimizationQuery::class );

		$query = $this->getContainer()->get( 'image_optimization_query' );

		$this->getContainer()->addShared( 'image_optimization_options_manager', ImageOptimizationOptionsManager::class )
			->addArgument( $this->getContainer()->get( 'options_api' ) )
			->addArgument( $options );

		$options_manager = $this->getContainer()->get( 'image_optimization_options_manager' );

		$this->getContainer()->addShared( 'image_optimization_api_client', ImageOptimizationAPIClient::class )
			->addArgument( $options_manager );

		$api_client = $this->getContainer()->get( 'image_optimization_api_client' );

		$this->getContainer()->addShared( 'image_optimization_file_manager', ImageOptimizationFileManager::class )
			->addArgument( $download_path )
			->addArgument( $backup_path )
			->addArgument( $filesystem );

		$file_manager = $this->getContainer()->get( 'image_optimization_file_manager' );

		$this->getContainer()->addShared( 'image_optimization_file_scanner', ImageOptimizationFileScanner::class );

		$this->getContainer()->addShared( 'image_optimization_file_scanner_process', ImageOptimizationFileScannerProcess::class )
			->addArgument( $this->getContainer()->get( 'image_optimization_file_scanner' ) )
			->addArgument( $backup_path )
			->addArgument( $source_path )
			->addArgument( $source_folder )
			->addArgument( $source_url );

		$this->getContainer()->addShared( 'image_optimization_queue_worker_process', ImageOptimizationQueueWorkerProcess::class )
			->addArgument( $api_client )
			->addArgument( $query )
			->addArgument( $file_manager )
			->addArgument( $options_manager );

		$this->getContainer()->addShared( 'image_optimization_rest_wp', ImageOptimizationRestWp::class )
			->addArgument( $options_manager )
			->addArgument( $query );

		$restwp = $this->getContainer()->get( 'image_optimization_rest_wp' );

		$this->getContainer()->addShared( 'image_optimization_manager', ImageOptimizationManager::class )
			->addArgument( $this->getContainer()->get( 'image_optimization_file_scanner_process' ) )
			->addArgument( $this->getContainer()->get( 'image_optimization_queue_worker_process' ) )
			->addArgument( $query )
			->addArgument( $restwp );

		$this->getContainer()->addShared( 'image_optimization_notices_handler', ImageOptimizationNoticesHandler::class )
			->addArgument( $options_manager )
			->addArgument( $table )
			->addArgument( $query )
			->addArgument( $api_client )
			->addArgument( $file_manager );

		$this->getContainer()->addShared( 'image_optimization_subscriber', ImageOptimizationSubscriber::class )
			->addArgument( $options_manager )
			->addArgument( $this->getContainer()->get( 'image_optimization_manager' ) )
			->addArgument( $table )
			->addArgument( $restwp )
			->addArgument( $file_manager )
			->addArgument( $this->getContainer()->get( 'image_optimization_notices_handler' ) )
			->addTag( 'common_subscriber' );
	}
}
