<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */

namespace WP_Rocket\Engine\License\API;

use WP_Rocket\Admin\Options_Data;
use WP_Rocket\Engine\Common\JobManager\APIHandler\AbstractSafeAPIClient;

class UserClient extends AbstractSafeAPIClient {
	const USER_ENDPOINT = 'https://cloudlinux.com/stat/1.0/clsop/user.php';

	/**
	 * WP Rocket options instance
	 *
	 * @var Options_Data
	 * @phpstan-ignore-next-line
	 */
	private $options;

	/**
	 * Get the transient key for plugin updates.
	 *
	 * This method returns the transient key used for caching plugin updates.
	 *
	 * @return string The transient key for plugin updates.
	 */
	protected function get_transient_key() {
		return 'wpr_user_information';
	}

	/**
	 * Get the API URL for plugin updates.
	 *
	 * This method returns the API URL used for fetching plugin updates.
	 *
	 * @return string The API URL for plugin updates.
	 */
	protected function get_api_url() {
		return self::USER_ENDPOINT;
	}

	/**
	 * Instantiate the class
	 *
	 * @param Options_Data $options WP Rocket options instance.
	 */
	public function __construct( Options_Data $options ) {
		$this->options = $options;
	}

	/**
	 * Gets user data from cache if it exists, else gets it from the user endpoint
	 *
	 * Cache the user data for 24 hours in a transient
	 *
	 * @since 3.7.3
	 *
	 * @return bool|object
	 */
	public function get_user_data() {
		$cached_data = get_transient( 'wp_rocket_customer_data' );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		$data = $this->get_raw_user_data();

		if ( false === $data ) {
			return false;
		}

		set_transient( 'wp_rocket_customer_data', $data, DAY_IN_SECONDS );

		return $data;
	}

	/**
	 * Gets the user data from the user endpoint
	 *
	 * @since 3.7.3
	 *
	 * @return bool
	 */
	private function get_raw_user_data() {
		// CL.
		return false; // @phpstan-ignore-line
	}
}
