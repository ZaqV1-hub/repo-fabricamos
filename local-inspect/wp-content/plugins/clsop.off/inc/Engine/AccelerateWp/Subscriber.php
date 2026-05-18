<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Engine\AccelerateWp;

use WP_Rocket\Event_Management\Subscriber_Interface;

/**
 * Subscriber for the AccelerateWP
 */
class Subscriber implements Subscriber_Interface {
	/**
	 * Sentry
	 *
	 * @var Sentry
	 */
	private $sentry;

	/**
	 * Constructor
	 *
	 * @param Sentry $sentry Sentry instance.
	 */
	public function __construct( Sentry $sentry ) {
		$this->sentry = $sentry;
	}

	/**
	 * Return an array of events that this subscriber wants to listen to.
	 *
	 * @return array
	 * @since  3.4
	 */
	public static function get_subscribed_events() {
		return [
			'accelerate_wp_set_error'             => [ 'debug', 10, 6 ],
			'accelerate_wp_set_error_handler'     => 'set_error_handler',
			'accelerate_wp_restore_error_handler' => 'restore_error_handler',
		];
	}

	/**
	 * Send error.
	 *
	 * @param int         $errno number.
	 * @param string      $errstr message.
	 * @param string|null $errfile file.
	 * @param int|null    $errline line.
	 * @param array       $extra params.
	 * @param array       $tags tags.
	 *
	 * @return void
	 */
	public function debug( $errno, $errstr, $errfile = null, $errline = null, $extra = [], $tags = [] ) {
		$this->sentry->error( $errno, $errstr, $errfile, $errline, $extra, $tags );
	}

	/**
	 * Custom error handler.
	 *
	 * @param int         $errno number.
	 * @param string      $errstr message.
	 * @param string|null $errfile file.
	 * @param int|null    $errline line.
	 *
	 * @return void
	 */
	public function custom_error_handler( $errno, $errstr, $errfile = null, $errline = null ) {
		$this->sentry->error( $errno, $errstr, $errfile, $errline, [], [] );
	}

	/**
	 * Set error handler.
	 *
	 * @return void
	 */
	public function set_error_handler() {
		set_error_handler( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_set_error_handler
			[
				$this,
				'custom_error_handler',
			]
		);
	}

	/**
	 * Restore error handler.
	 *
	 * @return void
	 */
	public function restore_error_handler() {
		restore_error_handler();
	}
}
