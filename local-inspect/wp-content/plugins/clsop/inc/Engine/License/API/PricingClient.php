<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */

namespace WP_Rocket\Engine\License\API;

use WP_Rocket\Engine\Common\JobManager\APIHandler\AbstractSafeAPIClient;

class PricingClient extends AbstractSafeAPIClient {
	const PRICING_ENDPOINT = 'https://cloudlinux.com/stat/1.0/wp-rocket/pricing-2023.php';

	/**
	 * Get the transient key for plugin updates.
	 *
	 * This method returns the transient key used for caching plugin updates.
	 *
	 * @return string The transient key for plugin updates.
	 */
	protected function get_transient_key() {
		return 'wp_rocket_pricing';
	}

	/**
	 * Get the API URL for plugin updates.
	 *
	 * This method returns the API URL used for fetching plugin updates.
	 *
	 * @return string The API URL for plugin updates.
	 */
	protected function get_api_url() {
		return self::PRICING_ENDPOINT;
	}

	/**
	 * Gets pricing data from cache if it exists, else gets it from the pricing endpoint
	 *
	 * Cache the pricing data for 6 hours in a transient
	 *
	 * @since 3.7.3
	 *
	 * @return bool|object
	 */
	public function get_pricing_data() {
		$cached_data = get_transient( 'wp_rocket_pricing' );

		if ( false !== $cached_data ) {
			return $cached_data;
		}

		$data = $this->get_raw_pricing_data();

		if ( false === $data ) {
			return false;
		}

		set_transient( 'wp_rocket_pricing', $data, 12 * HOUR_IN_SECONDS );

		return $data;
	}

	/**
	 * Gets the pricing data from the pricing endpoint
	 *
	 * @since 3.7.3
	 *
	 * @return bool
	 */
	private function get_raw_pricing_data() {
		// CL.
		return false;
	}
}
