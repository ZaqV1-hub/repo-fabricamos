<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Cli;

use Exception;
use WP_CLI;

/**
 * AccelerateWp clean-up command.
 *
 * @since 3.13.4_1.1-8
 */
class AccelerateWpClean {
	/**
	 * Run.
	 *
	 * @param array $args args.
	 * @param array $assoc_args args.
	 *
	 * @return void
	 * @throws Exception Something went wrong.
	 */
	public function run( $args = [], $assoc_args = [] ) { //@phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed

		if ( ! function_exists( 'rocket_purge_all_cache_domain' ) ) {
			WP_CLI::error( 'The plugin AccelerateWP seems not enabled on this site.' );
		}

		if ( rocket_purge_all_cache_domain() ) {
			WP_CLI::success( 'Cache cleared.' );
		} else {
			WP_CLI::error( 'Something went wrong.' );
		}
	}
}
