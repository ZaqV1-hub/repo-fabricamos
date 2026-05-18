<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Cli;

use Exception;
use WP_CLI_Command;

/**
 * AccelerateWp CLI commands
 */
class AccelerateWp extends WP_CLI_Command {
	/**
	 * Cdn.
	 *
	 * @param array $args args.
	 * @param array $assoc_args args.
	 *
	 * @return void
	 * @throws Exception Something went wrong.
	 */
	public function cdn( $args = [], $assoc_args = [] ) {
		( new AccelerateWpCdn() )->run( $args, $assoc_args );
	}

	/**
	 * CPCSS.
	 *
	 * @param array $args args.
	 * @param array $assoc_args args.
	 *
	 * @return void
	 * @throws Exception Something went wrong.
	 */
	public function cpcss( $args = [], $assoc_args = [] ) {
		( new AccelerateWpCpCss() )->run( $args, $assoc_args );
	}

	/**
	 * Image Optimization.
	 *
	 * @param array $args args.
	 * @param array $assoc_args args.
	 *
	 * @return void
	 * @throws Exception Something went wrong.
	 */
	public function image_optimization( $args = [], $assoc_args = [] ) {
		( new AccelerateWpImageOptimization() )->run( $args, $assoc_args );
	}

	/**
	 * Clean-up command.
	 *
	 * @param array $args Arguments.
	 * @param array $assoc_args Arguments.
	 *
	 * @return void
	 * @throws \Exception Something went wrong.
	 *
	 * @since 3.13.4_1.1-8
	 */
	public function clean( $args = [], $assoc_args = [] ) {
		( new AccelerateWpClean() )->run( $args, $assoc_args );
	}
}
