<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Cli;

use Exception;
use WP_CLI;

/**
 * AccelerateWp CDN
 */
class AccelerateWpCdn {
	/**
	 * Run.
	 *
	 * @param array $args args.
	 * @param array $assoc_args args.
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
	 * @param bool  $check_api_key api key.
	 *
	 * @return array
	 * @throws Exception Something went wrong.
	 */
	private function validation( $assoc_args = [], $check_api_key = false ) {
		$account_id = array_key_exists( 'account_id', $assoc_args ) ? sanitize_text_field( wp_unslash( $assoc_args['account_id'] ) ) : 0;
		$cdn_url    = array_key_exists( 'cdn_url', $assoc_args ) ? sanitize_text_field( wp_unslash( $assoc_args['cdn_url'] ) ) : '';
		$api_key    = array_key_exists( 'api_key', $assoc_args ) ? sanitize_text_field( wp_unslash( $assoc_args['api_key'] ) ) : '';

		if ( empty( $account_id ) ) {
			throw new Exception( '--account_id is empty' );
		} elseif ( empty( $cdn_url ) || is_numeric( $cdn_url ) ) {
			throw new Exception( '--cdn_url is empty' );
		} elseif ( ! filter_var( $cdn_url, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME ) ) {
			throw new Exception( '--cdn_url is not valid domain, don\'t pass protocol' );
		} elseif ( empty( $api_key ) && $check_api_key ) {
			throw new Exception( '--api_key is empty' );
		}

		return [
			'account_id' => (int) $account_id,
			'cdn_url'    => $cdn_url,
			'api_key'    => $api_key,
		];
	}

	/**
	 * Cdn activate.
	 *
	 * @param array $args args.
	 * @param array $assoc_args args.
	 *
	 * @return void
	 * @throws Exception Something went wrong.
	 */
	private function enable( $args = [], $assoc_args = [] ) {
		try {
			$validated = $this->validation( $assoc_args, true );

			$this->await_pull_zone( $validated['cdn_url'] );

			do_action(
				'rocketcdn_accelerate_wp_cli_cdn_enable',
				$validated['account_id'],
				$validated['cdn_url'],
				$validated['api_key']
			);

			if ( ! array_key_exists( 'skip-check', $assoc_args ) ) {
				$this->check( $args, $assoc_args );
			}

			WP_CLI::success( 'Enabled.' );
		} catch ( Exception $e ) {
			$code = $e->getCode() > 0 ? $e->getCode() : 1;
			WP_CLI::error( $e->getMessage(), $code );
		}
	}

	/**
	 * Cdn deactivate.
	 *
	 * @param array $args args.
	 * @param array $assoc_args args.
	 *
	 * @return void
	 * @throws Exception Something went wrong.
	 */
	private function disable( $args = [], $assoc_args = [] ) { // @phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		try {
			do_action( 'rocketcdn_accelerate_wp_cli_cdn_disable' );
			WP_CLI::success( 'Disabled.' );
		} catch ( Exception $e ) {
			$code = $e->getCode() > 0 ? $e->getCode() : 1;
			WP_CLI::error( $e->getMessage(), $code );
		}
	}

	/**
	 * Cdn post-check.
	 *
	 * @param array $args args.
	 * @param array $assoc_args args.
	 *
	 * @return void
	 * @throws Exception Something went wrong.
	 */
	private function check( $args = [], $assoc_args = [] ) {
		try {
			$validated = $this->validation( $assoc_args );
			$checker   = new AccelerateWpCdnCheck( $validated['account_id'], $validated['cdn_url'] );
			if ( false === $checker->check() ) {
				$checker->report();
				$reason = $checker->reason();
				WP_CLI::error( 'Post-check failed: ' . $reason );
			}
		} catch ( Exception $e ) {
			$code = $e->getCode() > 0 ? $e->getCode() : 1;
			WP_CLI::error( 'Post-check failed: ' . $e->getMessage(), $code );
		}
	}

	/**
	 * Await pull zone
	 *
	 * @param string $cdn_url PullZone.
	 *
	 * @return void
	 */
	private function await_pull_zone( $cdn_url ) {
		for ( $i = 0; $i < 5; $i ++ ) {
			$response = wp_remote_get(
				'https://' . $cdn_url,
				[
					'timeout' => 4,
				]
			);

			if ( false === is_wp_error( $response ) ) {
				break;
			} elseif ( false === strpos( $response->get_error_message(), 'SSL' ) ) {
				break;
			}

			sleep( 2 );
		}
	}
}
