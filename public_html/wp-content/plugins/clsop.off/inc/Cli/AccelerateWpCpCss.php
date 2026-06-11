<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Cli;

use Exception;
use WP_CLI;

/**
 * AccelerateWp CpCss
 */
class AccelerateWpCpCss {
	/**
	 * CPCSS.
	 *
	 * @param  array $args  args.
	 * @param  array $assoc_args  args.
	 *
	 * @return void
	 * @throws Exception Something went wrong.
	 */
	public function run( $args = [], $assoc_args = [] ) {
		$subcommand = '';
		if ( array_key_exists( 0, $args ) ) {
			$subcommand = $args[0];
		}

		if ( ! empty( $subcommand ) && __METHOD__ !== $subcommand && method_exists( $this, $subcommand ) ) {
			$this->$subcommand( $args, $assoc_args );
		} else {
			WP_CLI::error( 'Subcommand not found' );
		}
	}

	/**
	 * Validation.
	 *
	 * @param array $assoc_args args.
	 *
	 * @return array
	 * @throws Exception Something went wrong.
	 */
	private function validation( $assoc_args = [] ) {
		$unique_id = array_key_exists( 'unique_id', $assoc_args ) ? sanitize_text_field( wp_unslash( $assoc_args['unique_id'] ) ) : '';

		if ( empty( $unique_id ) ) {
			throw new Exception( '--unique_id is empty' );
		}

		return [
			'unique_id' => (string) $unique_id,
		];
	}

	/**
	 * Feature activate.
	 *
	 * @param  array $args  args.
	 * @param  array $assoc_args  args.
	 *
	 * @return void
	 * @throws Exception Something went wrong.
	 */
	private function enable( $args = [], $assoc_args = [] ) {
		try {
			$validated = $this->validation( $assoc_args );

			do_action(
				'rocket_cpcss_accelerate_wp_cli_enable',
				$validated['unique_id']
			);
			WP_CLI::success( 'Enabled.' );
		} catch ( Exception $e ) {
			$code = $e->getCode() > 0 ? $e->getCode() : 1;
			WP_CLI::error( $e->getMessage(), $code );
		}
	}

	/**
	 * Feature deactivate.
	 *
	 * @param  array $args  args.
	 * @param  array $assoc_args  args.
	 *
	 * @return void
	 * @throws Exception Something went wrong.
	 */
	private function disable( $args = [], $assoc_args = [] ) { // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		try {
			do_action( 'rocket_cpcss_accelerate_wp_cli_disable' );
			WP_CLI::success( 'Disabled.' );
		} catch ( Exception $e ) {
			$code = $e->getCode() > 0 ? $e->getCode() : 1;
			WP_CLI::error( $e->getMessage(), $code );
		}
	}
}
