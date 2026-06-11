<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization;

use WP_Rocket\Addon\ImageOptimization\Database\Queries\ImageOptimization as Query;
use WP_Rocket\Logger\Logger;

/**
 * Handles the image minification process.
 *
 * @since 3.12.6.1_1.1-1
 */
class Manager {

	/**
	 * Background file scanner process instance.
	 *
	 * @var FileScannerProcess
	 */
	public $file_scanner_process;

	/**
	 * Background queue worker process instance.
	 *
	 * @var QueueWorkerProcess
	 */
	private $queue_worker_process;

	/**
	 * Database query.
	 *
	 * @var Query database.
	 */
	private $query;

	/**
	 * Instance of the WP REST API.
	 *
	 * @var RESTWP instance of the rest wp.
	 */
	private $rest_api;

	/**
	 * Creates an instance of ImageOptimization.
	 *
	 * @param FileScannerProcess $file_scanner_process Background scanner process instance.
	 * @param QueueWorkerProcess $queue_worker_process Background upload process instance.
	 * @param Query              $query database.
	 * @param RESTWP             $rest_api Instance of the Rest API.
	 */
	public function __construct( FileScannerProcess $file_scanner_process, QueueWorkerProcess $queue_worker_process, Query $query, RESTWP $rest_api ) {
		$this->file_scanner_process = $file_scanner_process;
		$this->queue_worker_process = $queue_worker_process;
		$this->query                = $query;
		$this->rest_api             = $rest_api;
	}

	/**
	 * Log debug message
	 *
	 * @param string $message The log message.
	 * @param array  $context The log context.
	 */
	public static function debug( string $message, array $context = [] ) {
		Logger::debug( '[AWP Image Optimization] ' . $message, $context );
	}

	/**
	 * Run rescan process if not running.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	public function run_rescan_process() {
		// Run scanner process.
		if ( ! $this->file_scanner_process->is_running() ) {
			$last_mtime_file    = (int) get_transient( 'rocket_image_optimization_scanner_mtime_file' );
			$last_relative_path = (string) get_transient( 'rocket_image_optimization_scanner_last_relative_path' );
			$this->file_scanner_process->push_to_queue(
				[
					'last_mtime_file'    => $last_mtime_file,
					'last_relative_path' => $last_relative_path,
				]
			)->save()->dispatch();
		}
	}

	/**
	 * Run queue worker process if not running or postponed.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	public function run_queue_worker_process() {
		// Run optimization process.
		if (
			! $this->queue_worker_process->is_running()
			&&
			! $this->queue_worker_process->is_process_postponed( true )
		) {
			$this->queue_worker_process->push_to_queue(
				[
					'started_at'      => current_time( 'mysql', true ),
					'execution_count' => 0,
					'return_url'      => $this->rest_api->get_return_url(),
				]
			)->save()->dispatch();
		}
	}

	/**
	 * Interrupt the image optimization process.
	 *
	 * @param bool $remove queue.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	public function interrupt( bool $remove = false ) {
		if ( $this->file_scanner_process->is_running() ) {
			$this->file_scanner_process->cancel_process();
		}

		if ( $this->queue_worker_process->is_running() ) {
			$this->queue_worker_process->cancel_process();
		}

		if ( true === $remove ) {
			$this->query->delete_all();
			delete_transient( 'rocket_image_optimization_scanner_mtime_file' );
			delete_transient( 'rocket_image_optimization_scanner_last_relative_path' );
		} else {
			$this->query->make_status_new_for_all();
		}

		delete_transient( 'rocket_image_optimization_concurrency_limit' );
		delete_transient( 'rocket_image_optimization_process_postponed' );
		delete_transient( 'rocket_image_optimization_process_stopped_by_error' );
		delete_transient( 'rocket_image_optimization_process_info' );
		delete_transient( 'rocket_image_optimization_process_completed' );
	}

	/**
	 * Adds attachments to the queue and restarts the process if necessary.
	 *
	 * @param int[] $attachment_ids Attachment IDs.
	 * @param int   $priority Job priority.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	public function add_attachments_to_queue( array $attachment_ids, int $priority = 0 ) {
		$sizes = wp_get_registered_image_subsizes();
		foreach ( $attachment_ids as $attachment_id ) {
			$this->add_attachment_to_queue( $attachment_id, $sizes, $priority );
		}

		$this->run_queue_worker_process();
	}

	/**
	 * Adds an attachment to the queue.
	 *
	 * @param int   $attachment_id Attachment ID.
	 * @param array $sizes Image sizes to process. See wp_get_registered_image_subsizes() result for the format.
	 * @param int   $priority Job priority.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	private function add_attachment_to_queue( int $attachment_id, array $sizes, int $priority = 0 ) {
		foreach ( $sizes as $size_name => $size_data ) {
			$image = wp_get_attachment_image_src( $attachment_id, $size_name );

			if ( $image ) {
				[ $url ] = $image;

				$this->add_file_to_queue_table( $url, $priority );
			}
		}
	}

	/**
	 * Adds a file to the queue table.
	 *
	 * @param string $url file Url.
	 * @param int    $priority Job priority.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	public function add_file_to_queue_table( string $url, int $priority = 0 ) {
		foreach ( [ 'original', 'webp' ] as $format ) {
			// Skip files already in the queue.
			if ( 0 === $this->query->get_count_by_url_and_format( $url, $format ) ) {
				self::debug( 'Adding image to the database queue: ' . $url . ' (' . $format . ')' );
				$this->query->create_new( $url, $format, $priority );
			}
		}
	}
}
