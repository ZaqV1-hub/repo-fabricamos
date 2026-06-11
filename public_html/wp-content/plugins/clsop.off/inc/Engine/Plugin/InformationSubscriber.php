<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */
namespace WP_Rocket\Engine\Plugin;

use WP_Rocket\Event_Management\Subscriber_Interface;
use WP_Error;
use WP_Rocket\Engine\AccelerateWp\ApiSocket;

/**
 * Manages the plugin information.
 *
 * @note CL
 */
class InformationSubscriber implements Subscriber_Interface {
	use UpdaterApiTools;

	const INFORMATION_ENDPOINT = 'https://cloudlinux.com/plugin_information.php';

	/**
	 * Plugin slug.
	 *
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * Path to contact to get plugin info.
	 *
	 * @note CL.
	 * @var string
	 */
	private $local_path;

	/**
	 * An ID to use when a API request fails.
	 *
	 * @var string
	 */
	protected $request_error_id = 'plugins_api_failed';

	/**
	 * Api socket
	 *
	 * @note CL.
	 * @var ApiSocket
	 */
	private $api_socket;

	/**
	 * Constructor
	 *
	 * @note CL.
	 *
	 * @param ApiSocket $api_socket  ApiSocket instance.
	 * @param array     $args { Required arguments to populate the class properties.
	 *
	 *     @type string $plugin_file Full path to the plugin.
	 *     @type string $local_path  Path to contact to get update info.
	 * }
	 */
	public function __construct( ApiSocket $api_socket, $args ) {
		$this->api_socket = $api_socket;

		if ( isset( $args['plugin_file'] ) ) {
			$this->plugin_slug = $this->get_plugin_slug( $args['plugin_file'] );
		}
		if ( isset( $args['local_path'] ) ) {
			$this->local_path = $args['local_path'];
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_subscribed_events() {
		return [
			'plugins_api'              => [ 'exclude_rocket_from_wp_info', 10, 3 ],
			'plugins_api_result'       => [
				[ 'add_rocket_info', 10, 3 ],
				[ 'add_plugins_to_result', 11, 3 ],
			],
			'rocket_wp_tested_version' => 'add_wp_tested_version',
		];
	}

	/**
	 * Don’t ask for plugin info to the repository.
	 *
	 * @param  false|object|array $bool   The result object or array. Default false.
	 * @param  string             $action The type of information being requested from the Plugin Install API.
	 * @param  object             $args   Plugin API arguments.
	 * @return false|object|array         Empty object if slug is WP Rocket, default value otherwise.
	 */
	public function exclude_rocket_from_wp_info( $bool, $action, $args ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.boolFound
		if ( ! $this->is_requesting_rocket_info( $action, $args ) ) {
			return $bool;
		}
		return new \stdClass();
	}

	/**
	 * Insert WP Rocket plugin info.
	 *
	 * @param  object|WP_Error $res    Response object or WP_Error.
	 * @param  string          $action The type of information being requested from the Plugin Install API.
	 * @param  object          $args   Plugin API arguments.
	 * @return object|WP_Error         Updated response object or WP_Error.
	 */
	public function add_rocket_info( $res, $action, $args ) {
		if ( ! $this->is_requesting_rocket_info( $action, $args ) || empty( $res->external ) ) {
			return $res;
		}

		return $this->get_plugin_information();
	}

	/**
	 * Adds the WP tested version value from our API
	 *
	 * @param string $wp_tested_version WP tested version.
	 *
	 * @return string
	 */
	public function add_wp_tested_version( $wp_tested_version ): string {
		$info = $this->get_plugin_information();

		if ( empty( $info->tested ) ) {
			return $wp_tested_version;
		}

		return $info->tested;
	}

	/**
	 * Tell if requesting WP Rocket plugin info.
	 *
	 * @param  string $action The type of information being requested from the Plugin Install API.
	 * @param  object $args   Plugin API arguments.
	 * @return bool
	 */
	private function is_requesting_rocket_info( $action, $args ) {
		return ( 'query_plugins' === $action || 'plugin_information' === $action ) && isset( $args->slug ) && $args->slug === $this->plugin_slug;
	}

	/**
	 * Gets the plugin information data
	 *
	 * @note CL.
	 * @return object|WP_Error
	 */
	private function get_plugin_information() {
		$config_file = $this->local_path . DIRECTORY_SEPARATOR . 'clsop.ini';
		if ( @is_readable( $config_file ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$plugin_data = parse_ini_file( $config_file, true );
		} else {
			// open_basedir restriction in effect.
			do_action( 'accelerate_wp_set_error_handler' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

			$response = $this->api_socket->plugin_data();
			$response = json_decode( $response, true );
			if (
				is_array( $response )
				&&
				isset( $response['data'] )
				) {
				$plugin_data = $response['data'];
			}

			do_action( 'accelerate_wp_restore_error_handler' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

		}

		if ( ! isset( $plugin_data ) ) {
			return $this->get_request_error(
				[
					'error_code' => 500,
					'response'   => 'Cannot parse ini-file',
				]
			);
		}

		$obj               = new \stdClass();
		$obj->sections     = [ 'Description' => $plugin_data['clsop_info']['plugin_information'] ];
		$obj->name         = 'AccelerateWP';
		$obj->slug         = $this->plugin_slug;
		$obj->version      = $plugin_data['clsop_info']['version'];
		$obj->author       = 'CloudLinux';
		$obj->homepage     = 'https://cloudlinux.com';
		$obj->external     = true;
		$obj->requires     = $plugin_data['clsop_info']['wp_version'];
		$obj->tested       = $plugin_data['clsop_info']['wp_version_tested'];
		$obj->requires_php = $plugin_data['clsop_info']['php_version'];

		return $obj;
	}

	/**
	 * Filter plugin fetching API results to inject our plugins
	 *
	 * @param object|WP_Error $result Response object or WP_Error.
	 * @param string          $action The type of information being requested from the Plugin Install API.
	 * @param object          $args   Plugin API arguments.
	 *
	 * @return object|WP_Error
	 */
	public function add_plugins_to_result( $result, $action, $args ) {
		if ( ! $this->can_add_plugins( $result, $args ) ) {
			return $result;
		}

		$plugins = [
			'uk-cookie-consent' => 'uk-cookie-consent/uk-cookie-consent.php',
			'backwpup'          => 'backwpup/backwpup.php',
			'seo-by-rank-math'  => 'seo-by-rank-math/rank-math.php',
			'imagify'           => 'imagify/imagify.php',
		];

		// grab all slugs from the api results.
		$result_slugs = wp_list_pluck( $result->plugins, 'slug' );

		foreach ( $plugins as $slug => $path ) {
			if ( is_plugin_active( $path ) || is_plugin_active_for_network( $path ) ) {
				continue;
			}

			if ( in_array( $slug, $result_slugs, true ) ) {
				foreach ( $result->plugins as $index => $plugin ) {
					if ( is_object( $plugin ) ) {
						$plugin = (array) $plugin;
					}
					if ( $slug === $plugin['slug'] ) {
						$move = $plugin;
						unset( $result->plugins[ $index ] );
						array_unshift( $result->plugins, $move );
					}
				}
				continue;
			}

			$plugin_data = $this->get_plugin_data( $slug );

			if ( empty( $plugin_data ) ) {
				continue;
			}

			array_unshift( $result->plugins, $plugin_data );
		}

		return $result;
	}

	/**
	 * Checks if we can add plugins to the results
	 *
	 * @param object|WP_error $result Response object or WP_Error.
	 * @param object          $args Plugin API arguments.
	 *
	 * @return bool
	 */
	private function can_add_plugins( $result, $args ) {
		if ( is_wp_error( $result ) ) {
			return false;
		}

		if ( empty( $args->browse ) ) {
			return false;
		}

		if ( 'featured' !== $args->browse && 'recommended' !== $args->browse && 'popular' !== $args->browse ) {
			return false;
		}

		if ( ! isset( $result->info['page'] ) || 1 < $result->info['page'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns plugin data
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return array|object
	 */
	private function get_plugin_data( string $slug ) {
		$query_args = [
			'slug'   => $slug,
			'fields' => [
				'icons'             => true,
				'active_installs'   => true,
				'short_description' => true,
				'group'             => true,
			],
		];

		$plugin_data = plugins_api( 'plugin_information', $query_args );

		if ( is_wp_error( $plugin_data ) ) {
			return [];
		}

		return $plugin_data;
	}
}
