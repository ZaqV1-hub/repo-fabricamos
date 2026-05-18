<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Engine\AccelerateWp;

/**
 * Class for determining server load by LVE limits.
 */
class LveLimits {
	const CPU_THRESHOLD = 0.5;

	const EP_THRESHOLD = 0.5;

	const MEMPHY_THRESHOLD = 0.5;

	const IO_THRESHOLD = 0.5;

	const IOPS_THRESHOLD = 0.5;

	const NPROC_THRESHOLD = 0.5;

	const PRELOAD_CACHE_PAGE = 10;

	/**
	 * Api.
	 *
	 * @var ApiSocket instance.
	 */
	private $api;

	/**
	 * Constructor.
	 *
	 * @param ApiSocket $api instance.
	 */
	public function __construct( ApiSocket $api ) {
		$this->api = $api;
	}

	/**
	 * Get api instance.
	 *
	 * @return ApiSocket
	 */
	public function api() {
		return $this->api;
	}

	/**
	 * Get server resource limits.
	 *
	 * @param bool $use_cache try to get by cache.
	 *
	 * @return array
	 *
	 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	 */
	public function get_limits( bool $use_cache = false ): array {
		if ( $use_cache ) {
			$data = get_transient( 'rocket_awp_limits' );
			if ( ! empty( $data ) && is_array( $data ) ) {
				return $data;
			}
		}

		try {
			$response = $this->api()->get_limits();
		} catch ( \Throwable $e ) {
			$response = '';
			// Pass.
		}

		$data = json_decode( $response, true );
		if ( ! is_array( $data ) ) {
			$data = [];
		} elseif ( false !== array_key_exists( 'error', $data ) ) {
			do_action(
				'accelerate_wp_set_error',
				E_WARNING,
				$data['error'],
				__FILE__,
				__LINE__,
				[],
				[]
				);
			$data = [];
		} else {
			$data = array_map(
				function ( $item ) {
					$item['limit'] = (float) $item['limit'];

					return $item;
				},
				$data
			);
		}

		set_transient( 'rocket_awp_limits', $data, HOUR_IN_SECONDS );

		return $data;
	}

	/**
	 * Get server resource usage.
	 *
	 * @param bool $use_cache try to get by cache.
	 *
	 * @return array
	 *
	 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	 */
	public function get_usage( bool $use_cache = false ): array {
		if ( $use_cache ) {
			$data = get_transient( 'rocket_awp_usage' );
			if ( ! empty( $data ) && is_array( $data ) ) {
				return $data;
			}
		}

		try {
			$response = $this->api()->get_usage();
		} catch ( \Throwable $e ) {
			$response = '';
			// Pass.
		}

		$data = json_decode( $response, true );
		if ( ! is_array( $data ) ) {
			$data = [];
		} elseif ( false !== array_key_exists( 'error', $data ) ) {
			do_action(
				'accelerate_wp_set_error',
				E_WARNING,
				$data['error'],
				__FILE__,
				__LINE__,
				[],
				[]
				);
			$data = [];
		} else {
			$data = array_map(
				function ( $item ) {
					$item['usage'] = (float) $item['usage'];

					return $item;
				},
				$data
			);
		}

		set_transient( 'rocket_awp_usage', $data, HOUR_IN_SECONDS );

		return $data;
	}

	/**
	 * Check if server overload.
	 *
	 * @param bool $use_limits_cache try to get lve limits from cache.
	 *
	 * @return bool
	 */
	public function is_server_overloaded( bool $use_limits_cache = false ): bool {
		$limits = $this->get_limits( $use_limits_cache );
		$usage  = $this->get_usage();

		$cpu_ratio = 0;
		if ( isset( $limits['lve_cpu']['limit'] ) && $limits['lve_cpu']['limit'] > 0 ) {
			$cpu_ratio = ( $usage['lve_cpu']['usage'] ?? 0 ) / $limits['lve_cpu']['limit'];
		}

		$ep_ratio = 0;
		if ( isset( $limits['lve_ep']['limit'] ) && $limits['lve_ep']['limit'] > 0 ) {
			$ep_ratio = ( $usage['lve_ep']['usage'] ?? 0 ) / $limits['lve_ep']['limit'];
		}

		$memphy_ratio = 0;
		if ( isset( $limits['lve_pmem']['limit'] ) && $limits['lve_pmem']['limit'] > 0 ) {
			$memphy_ratio = ( $usage['lve_pmem']['usage'] ?? 0 ) / $limits['lve_pmem']['limit'];
		}

		$io_ratio = 0;
		if ( isset( $limits['lve_io']['limit'] ) && $limits['lve_io']['limit'] > 0 ) {
			$io_ratio = ( $usage['lve_io']['usage'] ?? 0 ) / $limits['lve_io']['limit'];
		}

		$iops_ratio = 0;
		if ( isset( $limits['lve_iops']['limit'] ) && $limits['lve_iops']['limit'] > 0 ) {
			$iops_ratio = ( $usage['lve_iops']['usage'] ?? 0 ) / $limits['lve_iops']['limit'];
		}

		$nproc_ratio = 0;
		if ( isset( $limits['lve_nproc']['limit'] ) && $limits['lve_nproc']['limit'] > 0 ) {
			$nproc_ratio = ( $usage['lve_nproc']['usage'] ?? 0 ) / $limits['lve_nproc']['limit'];
		}

		return (
			self::CPU_THRESHOLD < $cpu_ratio ||
			self::EP_THRESHOLD < $ep_ratio ||
			self::MEMPHY_THRESHOLD < $memphy_ratio ||
			self::IO_THRESHOLD < $io_ratio ||
			self::IOPS_THRESHOLD < $iops_ratio ||
			self::NPROC_THRESHOLD < $nproc_ratio
		);
	}

	/**
	 * Determine the number of pages to cache based on current server metrics.
	 *
	 * @param bool $use_cache try to get lve limits/usage from cache.
	 *
	 * @return int Number of pages to cache.
	 */
	public function determine_pages_to_cache( bool $use_cache = false ): int {
		$limits = $this->get_limits( $use_cache );
		$usage  = $this->get_usage( $use_cache );

		$free_ep      = 0;
		$lve_ep_limit = $limits['lve_ep']['limit'] ?? 0;
		if ( $lve_ep_limit > 0 ) {
			$free_ep = $lve_ep_limit - ( $usage['lve_ep']['usage'] ?? 0 );
		}

		$free_nproc = 0;
		$lve_nproc  = $limits['lve_nproc']['limit'] ?? 0;
		if ( $lve_nproc > 0 ) {
			$free_nproc = $lve_nproc - ( $usage['lve_nproc']['usage'] ?? 0 );
		}

		// Use only 50% of the free EP && NPROC.
		$allowable_free_ep    = round( $free_ep * self::EP_THRESHOLD );
		$allowable_free_nproc = round( $free_nproc * self::NPROC_THRESHOLD );

		$data = [ self::PRELOAD_CACHE_PAGE ]; // By default.

		if ( $allowable_free_ep > 0 ) {
			$data[] = $allowable_free_ep;
		}

		if ( $allowable_free_nproc > 0 ) {
			$data[] = $allowable_free_nproc;
		}

		// Return the minimum value of allowable_free_ep, allowable_free_nproc, && the maximum number of pages to cache (10).
		return min( $data );
	}
}
