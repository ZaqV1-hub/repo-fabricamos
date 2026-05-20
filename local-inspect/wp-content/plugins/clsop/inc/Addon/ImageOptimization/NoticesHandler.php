<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization;

use WP_Rocket\Addon\ImageOptimization\Database\Queries\ImageOptimization as Query;
use WP_Rocket\Addon\ImageOptimization\Database\Tables\ImageOptimization as Table;

/**
 * Image optimization notices handler.
 *
 * @since 3.12.6.1_1.1-1
 */
class NoticesHandler {

	/**
	 * Instance of options manager.
	 *
	 * @var OptionsManager
	 */
	protected $options;

	/**
	 * Instance of the queue table.
	 *
	 * @var Table instance of the queue table.
	 */
	private $table;

	/**
	 * Instance of the queue query.
	 *
	 * @var Query instance of the queue table.
	 */
	private $query;

	/**
	 * Responsible for dealing with image minification APIs.
	 *
	 * @var APIClient API client instance.
	 */
	private $api_client;

	/**
	 * Instance of the file manager.
	 *
	 * @var FileManager
	 */
	private $file_manager;

	/**
	 * Number of already displayed (generated) notices.
	 *
	 * @var int
	 */
	private $already_displayed_count = 0;

	/**
	 * Creates an instance of the image minification notices handler.
	 *
	 * @param OptionsManager $options Options manager instance.
	 * @param Table          $table Instance of the queue table.
	 * @param Query          $query instance of the queue table.
	 * @param APIClient      $api_client API Client instance to deal with image minification APIs.
	 * @param FileManager    $file_manager Instance of the file manager.
	 */
	public function __construct( OptionsManager $options, Table $table, Query $query, APIClient $api_client, FileManager $file_manager ) {
		$this->options      = $options;
		$this->table        = $table;
		$this->query        = $query;
		$this->api_client   = $api_client;
		$this->file_manager = $file_manager;
	}

	/**
	 * Checks if the current user has already dismissed the notice.
	 *
	 * @param ?string $dismiss_button notification.
	 *
	 * @return bool
	 */
	private function is_notice_dismissed( $dismiss_button = null ) {

		if ( ! empty( $dismiss_button ) ) {
			$boxes = get_user_meta( get_current_user_id(), 'rocket_boxes', true );

			if ( in_array( $dismiss_button, (array) $boxes, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Display notices.
	 *
	 * @return void
	 */
	public function display_notices() {

		if ( ! $this->options->is_image_optimization_enabled() ) {
			return;
		}

		// @phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( ! current_user_can( 'rocket_image_optimization' ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( 'settings_page_clsop' !== $screen->id ) {
			return;
		}

		$this->display_dirs_permissions_notice();
		$this->display_no_table_notice();
		$this->display_missing_api_config_notice();

		if ( $this->already_displayed_count > 0 ) {
			// Stop here if we already displayed at least one of the blocking notices.
			return;
		}

		if ( $this->display_postponed_notice() ) {
			return;
		}

		if ( $this->display_stopped_notice() ) {
			return;
		}

		$this->display_progress_notice();
	}

	/**
	 * This notice displays the progress of the image minification.
	 *
	 * It shows progress while the optimization is in progress and a confirmation message when it's done.
	 *
	 * @return void
	 * @since 3.12.6.1_1.1-1
	 */
	private function display_progress_notice() {

		$queue_size = $this->query->get_queue_original_size();
		if ( 0 === $queue_size ) {
			// Show optimization results.
			$process_info = get_transient( 'rocket_image_optimization_process_completed' );
			if ( ! is_array( $process_info ) || empty( $process_info ) ) {
				return;
			}

			$message = '<p>' . esc_html__( 'Image minification successfully completed!', 'rocket' ) . '</p>';

			$this->render_notice_html(
				[
					'message'        => $message,
					'status'         => 'success',
					'dismissible'    => '',
					// Using the actual transient name here will delete the transient when user clicks the link.
					'dismiss_button' => 'rocket_image_optimization_process_completed',
				]
			);

			return;
		}

		$message = '<p>' . sprintf(
				// Translators: %1$d = number of images in the optimization queue.
				esc_html__( 'Image minification in progress. There are currently %d images in the queue. (Refresh this page to view progress)', 'rocket' ),
				$queue_size
			) . '</p>';

		$message .= '<p><i>' . esc_html__( 'The page cache and CDN will be flushed automatically after image optimization is complete. If you are using a standalone CDN plugin, you will need to reset the cache yourself.', 'rocket' ) . '</i></p>';

		$this->render_notice_html(
			[
				'status'      => 'info',
				'message'     => $message,
				'dismissible' => '',
			]
		);
	}

	/**
	 * This warning is displayed when one or more the image minification related directories aren't writeable.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	private function display_dirs_permissions_notice() {
		$dismiss_button = 'clsop_image_optimization_dirs_permissions';

		if ( ! $this->is_notice_dismissed( $dismiss_button ) ) {
			return;
		}

		if ( $this->file_manager->dirs_available() ) {
			return;
		}

		$message = sprintf(
			// translators: %s = plugin name.
			esc_html__( '%s: Could not create the following folder(s) due to missing writing permissions. These are necessary for the Image Optimization feature to work.', 'rocket' ),
			'<strong>' . WP_ROCKET_PLUGIN_NAME . '</strong>'
		);

		$message .= '<br />';

		if ( ! $this->file_manager->backup_dir_available() ) {
			$path     = trim( str_replace( ABSPATH, '', $this->file_manager->backup_path() ), '/' );
			$message .= '<br>&rarr;&nbsp;<code>' . $path . '</code>';
		}

		if ( ! $this->file_manager->download_dir_available() ) {
			$path     = trim( str_replace( ABSPATH, '', $this->file_manager->download_path() ), '/' );
			$message .= '<br>&rarr;&nbsp;<code>' . $path . '</code>';
		}

		$message .= '<br /><br />';
		$message .= '<em>' . esc_html__( 'Please go to your hosting panel and re-enable Image Optimization feature after fixing the file permissions.', 'rocket' ) . '</em>';

		$this->render_notice_html(
			[
				'status'         => 'error',
				'dismissible'    => '',
				'message'        => $message,
				'dismiss_button' => $dismiss_button,
			]
		);
	}

	/**
	 * Display a notice on table missing.
	 *
	 * @return void
	 */
	private function display_no_table_notice() {
		$dismiss_button = 'clsop_image_optimization_no_table';

		if ( ! $this->is_notice_dismissed( $dismiss_button ) ) {
			return;
		}

		if ( $this->table->exists() ) {
			return;
		}

		$message = sprintf(
			// translators: %1$s = plugin name, %2$s = table name.
			esc_html__( '%1$s: Could not create the %2$s table in the database which is necessary for the Image optimization feature to work.', 'rocket' ),
			'<strong>AccelerateWP</strong>',
			'<code>' . $this->table->get_name() . '</code>'
		);

		$message .= '<br /><br />';
		$message .= '<em>' . esc_html__( 'Please go to your hosting panel and re-enable Image Optimization feature after fixing the database permissions.', 'rocket' ) . '</em>';

		$this->render_notice_html(
			[
				'status'         => 'error',
				'dismissible'    => '',
				'message'        => $message,
				'dismiss_button' => $dismiss_button,
			]
		);
	}

	/**
	 * Display a notice when API configuration is missing of incorrect.
	 *
	 * @return void
	 */
	private function display_missing_api_config_notice() {
		$dismiss_button = 'clsop_image_optimization_misconfigured_api';

		if ( ! $this->is_notice_dismissed( $dismiss_button ) ) {
			return;
		}

		$validation_errors = $this->api_client->get_configuration_errors();
		if ( empty( $validation_errors ) ) {
			return;
		}

		$message = sprintf(
			// translators: %s = plugin name.
			esc_html__( '%s: Image optimization API is not configured correctly.', 'rocket' ),
			'<strong>AccelerateWP</strong>'
		);

		$message .= '<br />';

		foreach ( $validation_errors as $validation_error ) {
			$message .= '<br>&rarr;&nbsp;' . $validation_error->get_error_message();
		}

		$message .= '<br /><br />';
		$message .= '<em>' . esc_html__( 'Please go to your hosting panel and re-enable Image Optimization feature to fix the problem.', 'rocket' ) . '</em>';

		$this->render_notice_html(
			[
				'status'         => 'error',
				'dismissible'    => '',
				'message'        => $message,
				'dismiss_button' => $dismiss_button,
			]
		);
	}

	/**
	 * Display a notice about postponed image optimization process.
	 *
	 * @return bool True if the notice was displayed, false otherwise.
	 */
	private function display_postponed_notice() {
		$transient_name = 'rocket_image_optimization_process_postponed';

		$postponed_info = get_transient( $transient_name );
		if ( ! is_array( $postponed_info ) ) {
			return false;
		}

		$supported_reasons = [
			'saas_not_available',
			'saas_server_error',
			'quota_exceeded',
		];

		if ( ! array_key_exists( 'reason', $postponed_info ) || ! in_array( $postponed_info['reason'], $supported_reasons, true ) ) {
			return false;
		}

		switch ( $postponed_info['reason'] ) {
			case 'quota_exceeded':
				$message = esc_html__( 'Image optimization is temporarily paused. Your site has reached the monthly usage limit.', 'rocket' );
				break;
			case 'saas_not_available':
			case 'saas_server_error':
				$message = esc_html__( 'Image optimization is temporarily paused. The image minification service is not available at the moment. It should be resumed shortly.', 'rocket' );
				break;
			default:
				$message = '';
		}

		if ( 0 === strlen( $message ) ) {
			return false;
		}

		$status = array_key_exists( 'severity', $postponed_info ) ? $postponed_info['severity'] : 'warning';
		$this->render_notice_html(
			[
				'status'         => $status,
				'dismissible'    => '',
				'message'        => $message,
				'dismiss_button' => $transient_name,
			]
		);

		return true;
	}

	/**
	 * Display a notice about stopped image optimization process.
	 *
	 * @return bool True if the notice was displayed, false otherwise.
	 */
	private function display_stopped_notice() {
		$transient_name = 'rocket_image_optimization_process_stopped_by_error';

		$stopped_info = get_transient( $transient_name );
		if ( ! is_array( $stopped_info ) ) {
			return false;
		}

		// The process can only by stopped by authentication error based on responses from SaaS.
		if ( ! array_key_exists( 'reason', $stopped_info ) || 'auth_failed_401' !== $stopped_info['reason'] ) {
			return false;
		}

		$status = array_key_exists( 'severity', $stopped_info ) ? $stopped_info['severity'] : 'warning';
		$this->render_notice_html(
			[
				'status'         => $status,
				'dismissible'    => '',
				'message'        => esc_html__( 'Image optimization stopped. The site was unable to authenticate against the image minification service for more than 24 hours. Contact your system administrator.', 'rocket' ),
				'dismiss_button' => $transient_name,
			]
		);

		return true;
	}

	/**
	 * Renders the notice HTML and bumps the local counter.
	 *
	 * @param array $args An array of arguments used to determine the notice output.
	 *
	 * @return void
	 */
	private function render_notice_html( $args ) {
		++$this->already_displayed_count;
		rocket_notice_html( $args );
	}
}
