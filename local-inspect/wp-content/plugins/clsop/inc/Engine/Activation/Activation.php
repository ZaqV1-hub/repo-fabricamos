<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */

namespace WP_Rocket\Engine\Activation;

use WP_Rocket\Admin\Options;
use WP_Rocket\Dependencies\League\Container\Argument\Literal\StringArgument;
use WP_Rocket\Dependencies\League\Container\Container;
use WP_Rocket\Engine\Common\PerformanceHints\Activation\ServiceProvider as PerformanceHintsActivationServiceProvider;
use WP_Rocket\Engine\License\ServiceProvider as LicenseServiceProvider;
use WP_Rocket\Engine\Preload\Activation\ServiceProvider as PreloadActivationServiceProvider;
use WP_Rocket\Logger\ServiceProvider as LoggerServiceProvider;
use WP_Rocket\ServiceProvider\Options as OptionsServiceProvider;
use WP_Rocket\ThirdParty\Hostings\HostResolver;
use WP_Rocket\ThirdParty\Hostings\ServiceProvider as HostingsServiceProvider;
use WP_Rocket\Event_Management\Event_Manager;

/**
 * Plugin activation controller
 *
 * @since 3.6.3
 */
class Activation {
	const ACTIVATION_ENDPOINT = 'https://cloudlinux.com/api/clsop/activate-licence.php';

	/**
	 * Aliases in the container for each class that needs to call its activate method
	 *
	 * @var array
	 */
	private static $activators = [
		'advanced_cache',
		'capabilities_manager',
		'wp_cache',
		'action_scheduler_check',
		'preload_activation',
		'performance_hints_activation',
	];

	/**
	 * Performs these actions during the plugin activation
	 *
	 * @return void
	 */
	public static function activate_plugin() {
		$container     = new Container();
		$event_manager = new Event_Manager();

		$container->add( 'template_path', new StringArgument( rocket_get_constant( 'WP_ROCKET_PATH', '' ) . 'views' ) );
		$options_api = new Options( 'wp_rocket_' );
		$container->add( 'options_api', $options_api );
		$container->addServiceProvider( new OptionsServiceProvider() );
		$container->addServiceProvider( new PreloadActivationServiceProvider() );
		$container->addServiceProvider( new ServiceProvider() );
		$container->addServiceProvider( new HostingsServiceProvider() );
		$container->addServiceProvider( new LicenseServiceProvider() );
		$container->addServiceProvider( new LoggerServiceProvider() );
		$container->get( 'logger' );
		$container->addServiceProvider( new PerformanceHintsActivationServiceProvider() );
		$event_manager->add_subscriber( $container->get( 'performance_hints_warmup_subscriber' ) );

		$host_type = HostResolver::get_host_service();

		if ( ! empty( $host_type ) ) {
			array_unshift( self::$activators, $host_type );
		}

		foreach ( self::$activators as $activator ) {
			$container->get( $activator );
		}

		// Last constants.
		define( 'WP_ROCKET_PLUGIN_NAME', 'AccelerateWP' );
		define( 'WP_ROCKET_PLUGIN_SLUG', 'clsop' );

		if ( defined( 'SUNRISE' ) && SUNRISE === 'on' && function_exists( 'domain_mapping_siteurl' ) ) {
			require WP_ROCKET_INC_PATH . 'domain-mapping.php';
		}

		require WP_ROCKET_FUNCTIONS_PATH . 'options.php';
		require WP_ROCKET_FUNCTIONS_PATH . 'formatting.php';
		require WP_ROCKET_FUNCTIONS_PATH . 'i18n.php';
		require WP_ROCKET_FUNCTIONS_PATH . 'htaccess.php';
		require WP_ROCKET_FUNCTIONS_PATH . 'api.php';
		require WP_ROCKET_FUNCTIONS_PATH . 'admin.php';
		require WP_ROCKET_3RD_PARTY_PATH . 'plugins/wp-rest-api.php'; // CL.

		/**
		 * WP Rocket activation.
		 *
		 * @since  3.1.5
		 */
		do_action( 'rocket_activation' );

		if ( rocket_valid_key() ) {
			// Add All WP Rocket rules of the .htaccess file.
			flush_rocket_htaccess();
		}

		// Create the cache folders (wp-rocket & min).
		rocket_init_cache_dir();

		// Create the config folder (wp-rocket-config).
		rocket_init_config_dir();

		/**
		 * Create config file when WP CLI.
		 *
		 * @note CL
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			require WP_ROCKET_ADMIN_PATH . 'upgrader.php';
			// @phpstan-ignore-next-line
			rocket_upgrader();
			rocket_generate_config_file();
		}

		// Update customer key & licence.

		/*
		CL: Disable license request function
		wp_remote_get(
			self::ACTIVATION_ENDPOINT,
			[
				'blocking' => false,
			]
		);
		*/

		/**
		 * Fires after WP Rocket is activated
		 */
		do_action( 'rocket_after_activation' );
	}
}
