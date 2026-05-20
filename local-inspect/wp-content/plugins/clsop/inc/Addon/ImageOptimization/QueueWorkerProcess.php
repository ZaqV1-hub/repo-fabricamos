<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization;

use WP_Rocket\Addon\ImageOptimization\Database\Queries\ImageOptimization as Query;
use WP_Rocket_WP_Background_Process;

/**
 * Background process class for handling the image optimization queue.
 *
 * @since 3.12.6.1_1.1-1
 *
 * @see WP_Background_Process
 */
class QueueWorkerProcess extends WP_Rocket_WP_Background_Process {

	/**
	 * Indicates if the process was stopped by error based on a dedicated transient.
	 *
	 * @var bool
	 */
	public $stopped_by_error = false;

	/**
	 * Indicates if the process was postponed in the current request by a dedicated transient.
	 *
	 * @var bool
	 */
	public $postponed = false;

	/**
	 * Indicates if only the image uploads should be postponed.
	 *
	 * @var bool
	 */
	public $only_image_uploads_postponed = false;

	/**
	 * Max retries.
	 *
	 * @var int
	 */
	private $max_retries = 10;

	/**
	 * Process info.
	 *
	 * @var array
	 */
	private $process_info = [
		'uploaded'   => 0,
		'downloaded' => 0,
		'failed'     => 0,
	];

	/**
	 * Process prefix.
	 *
	 * @var string
	 */
	protected $prefix = 'rocket';

	/**
	 * Specific action identifier for image minification.
	 *
	 * @var string Action identifier
	 */
	protected $action = 'image_optimization_queue_worker';

	/**
	 * Responsible for dealing with image minification APIs.
	 *
	 * @var APIClient API client instance.
	 */
	private $api_client;

	/**
	 * Database query.
	 *
	 * @var Query database.
	 */
	private $query;

	/**
	 * File manager.
	 *
	 * @var FileManager file manager.
	 */
	private $file_manager;

	/**
	 * Options manager.
	 *
	 * @var OptionsManager
	 */
	private $options;

	/**
	 * Rest API return url.
	 *
	 * @var string
	 */
	private $return_url = '';

	/**
	 * Instantiate the class
	 *
	 * @since 3.12.6.1_1.1-2 Added the $options parameter.
	 *
	 * @param APIClient      $api_client API Client instance to deal with image minification APIs.
	 * @param Query          $query Data manager instance, responsible for dealing with data/database.
	 * @param FileManager    $file_manager Instance of file manager.
	 * @param OptionsManager $options Instance of Options manager instance.
	 */
	public function __construct( APIClient $api_client, Query $query, FileManager $file_manager, OptionsManager $options ) {
		parent::__construct();

		$this->api_client   = $api_client;
		$this->query        = $query;
		$this->file_manager = $file_manager;
		$this->options      = $options;
	}

	/**
	 * Saves current image optimization process info.
	 *
	 * @return void
	 */
	private function save_process_info() {
		set_transient( 'rocket_image_optimization_process_info', $this->process_info );
	}

	/**
	 * Deletes image optimization process info.
	 *
	 * @return void
	 */
	private function delete_process_info() {
		delete_transient( 'rocket_image_optimization_process_info' );
	}

	/**
	 * Retrieves the image optimization process info.
	 *
	 * If the transient is not set, it will be set.
	 *
	 * @return void
	 */
	private function load_process_info() {

		// Load transient holding the process info.
		$process_info = get_transient( 'rocket_image_optimization_process_info' );

		// If transient is not set, set it.
		if ( ! empty( $process_info ) ) {
			$this->process_info = $process_info;
		}
	}

	/**
	 * Perform the optimization corresponding to $item.
	 *
	 * @param mixed $item Item array return_url, id to iterate over.
	 *
	 * @return bool False if task was performed successfully, true otherwise to re-queue the item.
	 * @since 3.12.6.1_1.1-1
	 */
	protected function task( $item ) {

		// Load process info.
		$this->load_process_info();

		// Check if the process should be stopped.
		$stopped = $this->maybe_stop_process( $item );
		if ( $stopped ) {
			return false;
		}

		// Check if the process should be postponed.
		$postponed = $this->maybe_postpone_process( $item );
		if ( $postponed ) {
			return $this->update_item_for_next_run( $item );
		}

		// Download any image that's ready for download.
		$downloaded = $this->download_next_ready_image();
		if ( $downloaded ) {
			++$this->process_info['downloaded'];
			$this->save_process_info();

			return $this->update_item_for_next_run( $item, true );
		}

		// Check status of pending jobs that are not postponed.
		$checked = $this->check_next_pending_image();
		if ( $checked ) {
			return $this->update_item_for_next_run( $item, true );
		}

		$this->return_url = $item['return_url'] ?? '';

		// Upload the next image that's waiting to be uploaded.
		$uploaded = $this->upload_next_waiting_image();
		if ( $uploaded ) {
			++$this->process_info['uploaded'];
			$this->save_process_info();

			return $this->update_item_for_next_run( $item, true );
		}

		// Check if there are any more items in the queue that can be processed at this time.
		// These could be more items ready for download or more items waiting to be uploaded. Also pending images with postponed date in the past.
		$has_not_postponed_items = $this->query->has_more_items_to_process( $this->only_image_uploads_postponed );
		if ( $has_not_postponed_items ) {
			// There are items not postponed, so we can't finish the process.
			return $this->update_item_for_next_run( $item );
		}

		Manager::debug( 'There are no more items to process at the moment.' );

		// Check if there are some postponed items in the queue and schedule the next event based on the earliest postponed item.
		$earliest_postponed_item = $this->query->get_earliest_postponed_item();
		if ( $earliest_postponed_item ) {

			// There are postponed items, so we can't finish the process.
			// Schedule the next event based on the earliest postponed item.
			$timestamp = $earliest_postponed_item->postponed_until;
			$this->reschedule_event( $timestamp );

			Manager::debug( 'Postponed items found. Scheduled next event for ' . gmdate( 'Y-m-d H:i:s', $timestamp ) );

			return $this->update_item_for_next_run( $item );
		}

		// Check if we got here because the image uploads are postponed. It means that we cannot upload new images at this time and all other remaining items are already postponed.
		if ( $this->only_image_uploads_postponed ) {

			$postpone_process_data = $this->get_postpone_process_data();
			if ( ! empty( $postpone_process_data ) ) {
				$postponed_until_timestamp = $this->calculate_next_retry_timestamp( $postpone_process_data );
				$this->reschedule_event( $postponed_until_timestamp );
				Manager::debug( 'Image uploads are postponed because the concurrency limit was reached and all the pending jobs were already postponed. Scheduled next event for ' . gmdate( 'Y-m-d H:i:s', $postponed_until_timestamp ) );

				return $this->update_item_for_next_run( $item );
			}
		}

		Manager::debug( 'The image optimization queue is empty.' );

		// There is nothing to process and no postponed items, so we can finish the process.
		return false;
	}

	/**
	 * Download the next image that's ready for download.
	 *
	 * @return bool True if an image was downloaded, false otherwise.
	 */
	private function download_next_ready_image() : bool {

		// Load an item that's ready for download.
		$item = $this->query->get_job_ready_to_download();

		// Return false if no item found.
		if ( is_null( $item ) ) {
			Manager::debug( 'No minified images ready to be downloaded.' );

			return false;
		}

		// Check if the file still exists.
		if ( ! $this->file_manager->original_file_exists( $item->url ) ) {
			Manager::debug( 'Original file does not exist anymore. Skipping.' );
			$this->query->delete_item( $item->id );

			return false;
		}

		// Update status to downloading to prevent another process to start the download.
		$this->query->make_status_downloading( $item->id );

		$download_url = $this->api_client->get_api_download_url( $item->job_id );
		Manager::debug( 'Downloading minified image from ' . $download_url );

		// Try to download and save the image.
		$api_client = $this->api_client;
		$saved      = $this->file_manager->save_image(
			$item->url,
			$download_url,
			$item->format,
			function ( $download_url ) use ( $api_client ) {
				return $api_client->get_file_contents( $download_url );
			}
		);

		if ( is_wp_error( $saved ) ) {
			$this->handle_failed_job( $item->id, $saved->get_error_code(), $saved->get_error_message(), $saved->get_error_data() );

			return false;
		}

		// Acknowledge the job completion and delete the item from the queue.
		$this->api_client->acknowledge_job_completion( $item->job_id );
		$this->query->delete_item( $item->id );

		return true;
	}

	/**
	 * Uploads the next image that's waiting to be uploaded.
	 *
	 * @return bool True if an image was uploaded, false otherwise.
	 */
	private function upload_next_waiting_image() : bool {

		if ( $this->only_image_uploads_postponed ) {
			// The process is postponed because of reaching the concurrency limit. We cannot proceed.
			return false;
		}

		// Check the limit of concurrent image minification requests pre site.
		$limit = $this->api_client->get_concurrency_requests_limit();
		if ( $limit <= $this->query->get_pending_jobs_count() ) {
			Manager::debug( 'Cannot upload another image because the site concurrency limit was reached: ' . $limit );

			// Postpone optimization by 5 minutes.
			do_action(
				'rocket_image_optimization_postpone',
				[
					'reason'        => 'concurrency_limit',
					'severity'      => 'warning',
					'next_retry_in' => 300, // 5 minutes.
				]
			);

			return false;
		}

		// Load an item that's ready for upload.
		$item = $this->query->get_job_ready_to_upload();

		// Return false if no item found.
		if ( is_null( $item ) ) {
			Manager::debug( 'No minified images ready to be uploaded.' );
			return false;
		}

		Manager::debug( 'Sending image ' . $item->url . ' in format ' . $item->format . ' to SaaS service.' );

		$generated = $this->api_client->send_generation_request( $item->url, $item->format, $item->secret, $this->return_url );
		if ( is_wp_error( $generated ) ) {
			$this->handle_failed_job( $item->id, $generated->get_error_code(), $generated->get_error_message(), $generated->get_error_data() );

			return false;
		}

		$job_id = $generated->data->id;
		$this->query->make_status_pending( $item->id, $job_id, [ $this, 'get_wait_time' ] );

		return true;
	}

	/**
	 * Get wait time.
	 *
	 * @param int $attempt Attempt number.
	 *
	 * @return int Number of seconds to wait before next attempt.
	 *
	 * @phpcs:disable WordPress.WhiteSpace.ControlStructureSpacing.ExtraSpaceAfterCloseParenthesis
	 */
	public function get_wait_time( int $attempt ) {
		return (int) 60 * pow( 2, $attempt );
	}

	/**
	 * Check the status of the next pending image.
	 *
	 * @return bool True if an image was checked, false otherwise.
	 */
	private function check_next_pending_image() : bool {
		// Load the next pending job that is not postponed.
		$item = $this->query->get_job_pending_not_postponed();

		// Return false if no item found.
		if ( is_null( $item ) ) {
			Manager::debug( 'No pending images ready to be checked.' );

			return false;
		}

		if ( $item->retries >= $this->max_retries ) {
			$this->handle_failed_job( $item->id, 'max_retries', 'Max retries reached' );

			return false;
		}

		Manager::debug( 'Checking details of job ' . $item->job_id . ' for image ' . $item->url . ' in format ' . $item->format . '.' );

		$job_status = $this->api_client->get_job_details( $item->job_id );
		if ( is_wp_error( $job_status ) ) {
			$this->handle_failed_job( $item->id, $job_status->get_error_code(), $job_status->get_error_message(), $job_status->get_error_data() );

			return true;
		}

		$status_value = $job_status->data->state;
		if ( in_array( $status_value, [ 'new', 'processing' ], true ) ) {
			// Job is still pending.
			$this->query->postpone_item( $item->id, [ $this, 'get_wait_time' ] );

			return true;
		}

		if ( 'failed' === $status_value ) {
			// Image minification failed on the SaaS end.
			$this->handle_failed_job( $item->id, 'job_failed_in_saas', $job_status->data->error );

			return true;
		}

		if ( 'complete' === $status_value ) {
			// Image minification is complete, the image is ready to be downloaded.
			$this->query->make_status_to_download( $item->id );

			return true;
		}

		return false;
	}

	/**
	 * Set failed status.
	 *
	 * @since 3.12.6.1_1.1-2 Added unique_id and wp_job_id to error data. Also added unique_id and feature to the error tags and $data attribute.
	 *
	 * @param int     $id row.
	 * @param string  $error_code Error code.
	 * @param ?string $error_message Error message.
	 * @param ?mixed  $data Error data.
	 *
	 * @return bool
	 *
	 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
	 */
	private function handle_failed_job( int $id, string $error_code, ?string $error_message = '', $data = null ) {

		$unique_id  = $this->options->get_unique_id();
		$error_data = [
			'wp_job_id'     => $id,
			'unique_id'     => $unique_id,
			'error_code'    => $error_code,
			'error_message' => $error_message,
		];

		$job = $this->query->find( $id );
		if ( $job ) {
			if ( ! empty( $job->job_id ) ) {
				$error_data['job_id'] = $job->job_id;
			}
			$error_data['url']     = $job->url;
			$error_data['format']  = $job->format;
			$error_data['status']  = $job->status;
			$error_data['retries'] = $job->retries;
		}

		if ( is_array( $data ) && ! empty( $data ) ) {
			$error_data['error_data'] = $data;
		}

		if ( function_exists( 'debug_backtrace' ) ) {
			if ( ! array_key_exists( 'error_data', $error_data ) ) {
				$error_data['error_data'] = [];
			}

			if ( ! array_key_exists( 'stack_trace', $error_data['error_data'] ) ) {
				$error_data['error_data']['stack_trace'] = debug_backtrace();
			}
		}

		do_action(
			'accelerate_wp_set_error',
			E_WARNING,
			$error_message,
			__FILE__,
			__LINE__,
			$error_data,
			[
				'feature'   => 'image_optimization',
				'unique_id' => $unique_id,
			]
		);

		Manager::debug( 'Image Optimization job failed: ' . wp_json_encode( $error_data ) );

		// Handle a failed job that caused the process to be postponed.
		if ( did_action( 'rocket_image_optimization_postpone' ) ) {
			Manager::debug( 'Ignoring job failure because the process was postponed.', $error_data );

			if ( 'downloading' === $job->status ) {
				// Change the status back to "to_download" if the file download failed, but it's possible to try again later.
				$this->query->make_status_to_download( $id );
				Manager::debug( 'Reverted status of job ' . $id . ' back to "to_download".', $error_data );
			}

			return true;
		}

		++$this->process_info['failed'];
		$this->save_process_info();

		return $this->query->make_status_failed( $id, $error_code, $error_message );
	}

	/**
	 * Updates the process item for the next run.
	 *
	 * It also deletes the transient related to process postponing if the current item was successfully processed. This
	 * means that at least one API request was made successfully to the SaaS API.
	 *
	 * @param array $item           Background process item.
	 * @param bool  $was_successful Whether the action performed with the item was successful.
	 *
	 * @return mixed
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	private function update_item_for_next_run( $item, $was_successful = false ) {

		// Delete the transient related to process postponing if the current item was successfully processed.
		if ( $was_successful ) {
			delete_transient( 'rocket_image_optimization_process_postponed' );
		}

		// Update the execution count.
		if ( is_array( $item ) && array_key_exists( 'execution_count', $item ) ) { // @phpstan-ignore-line
			++$item['execution_count'];
		}

		return $item;
	}

	/**
	 * {$inheritDoc}
	 */
	protected function complete() {

		if ( $this->stopped_by_error ) {
			$this->handle_completion_after_stopping_by_error();
		} else {
			$this->handle_successful_completion();
		}

		$this->delete_process_info();
		parent::complete();
	}

	/**
	 * Handles the successful completion of the process.
	 */
	private function handle_successful_completion() {
		// Take a note of the time when the process completed.
		$this->process_info['finished_at'] = current_time( 'mysql', true );

		// Move the process info to a separate transient. It will be used to display the results of the process.
		set_transient( 'rocket_image_optimization_process_completed', $this->process_info );

		// Delete transients related to postponing or stopping of the process.
		delete_transient( 'rocket_image_optimization_process_postponed' );
		delete_transient( 'rocket_image_optimization_process_stopped_by_error' );

		// Broadcast the completion of the process.
		if ( $this->process_info['downloaded'] > 0 ) {
			do_action( 'rocket_image_optimization_complete' );
		}
	}

	/**
	 * Handles the completion of the process after it was stopped by an error.
	 */
	private function handle_completion_after_stopping_by_error() {
		// Take a note of the time when process was stopped by an error.
		$this->process_info['stopped_by_error_at'] = current_time( 'mysql', true );

		// Add the process info to the stop process data transient.
		$stop_data                 = $this->get_stop_process_data();
		$stop_data['process_info'] = $this->process_info;
		set_transient( 'rocket_image_optimization_process_stopped_by_error', $stop_data );

		// Delete the transients related to postponing or successful completion of the process. These might be still set from earlier.
		delete_transient( 'rocket_image_optimization_process_completed' );
		delete_transient( 'rocket_image_optimization_process_postponed' );
	}

	/**
	 * Retrieves the data that was set to stop the process.
	 *
	 * @return array
	 */
	private function get_stop_process_data() {
		$result = get_transient( 'rocket_image_optimization_process_stopped_by_error' );
		if ( ! is_array( $result ) || ! array_key_exists( 'reason', $result ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Retrieves the data that was set to postpone the process.
	 *
	 * @return array
	 */
	private function get_postpone_process_data() {
		$result = get_transient( 'rocket_image_optimization_process_postponed' );
		if ( ! is_array( $result ) || ! array_key_exists( 'reason', $result ) ) {
			return [];
		}

		return $result;
	}

	/**
	 * Stops the process if there is a transient set to stop it.
	 *
	 * @param mixed $item Task item.
	 *
	 * @return bool True if the process was stopped, false otherwise.
	 */
	private function maybe_stop_process( $item ) {

		$stop_process_data = $this->get_stop_process_data();
		if ( empty( $stop_process_data ) ) {
			return false;
		}

		// Cancel the background process.
		$this->cancel_process();

		// The rest of the cancellation process is done in the complete() method.
		$this->stopped_by_error = true;

		Manager::debug( 'The process has been stopped. Reason: ' . $stop_process_data['reason'] );

		return true;
	}

	/**
	 * Postpones the process if there is a transient set to postpone it. It also checks the timestamp in the dedicated
	 * transient to see if the process can be resumed.
	 *
	 * @param mixed $item Task item.
	 *
	 * @return bool True if the process was postponed, false otherwise.
	 */
	private function maybe_postpone_process( $item ) {

		$postpone_process_data = $this->get_postpone_process_data();
		if ( empty( $postpone_process_data ) ) {
			return false;
		}

		$reason = $postpone_process_data['reason'];
		if ( 'concurrency_limit' === $reason ) {

			// If the process is supposed to be postponed because of reaching the concurrency limit, we can still allow
			// the process to make other API calls (check status, download image and acknowledge download).
			$this->only_image_uploads_postponed = true;
			$this->postponed                    = true;

			return false;
		}

		// Check if max number of retries was reached, and if we need to stop the process.
		$should_stop = false;
		$retries     = $postpone_process_data['retries'];
		if ( array_key_exists( 'max_retries', $postpone_process_data ) ) {
			$max_retries = (int) $postpone_process_data['max_retries'];
			if ( $retries >= $max_retries ) {
				$should_stop = true;
			}
		}

		if ( $should_stop ) {

			Manager::debug( 'Maximum number of retries reached. Stopping the process.', $postpone_process_data );

			do_action(
				'rocket_image_optimization_stop',
				[
					'reason'   => $postpone_process_data['reason'],
					'severity' => 'error',
				]
			);

			return true;
		}

		// Change retry interval for "SaaS service not available" errors after the first hour from 5 minutes to 1 hour.
		if (
			in_array( $postpone_process_data['reason'], [ 'saas_not_available', 'saas_server_error' ], true )
			&& $postpone_process_data['retries'] >= 12
		) {
			$postpone_process_data['next_retry_in'] = 3600; // 1 hour
		}

		// Calculate the timestamp when the process can be resumed.
		$postponed_until_timestamp = $this->calculate_next_retry_timestamp( $postpone_process_data );

		// Update the number of retries and the last attempt timestamp. It must happen here, otherwise we would never
		// postpone again in case the timestamp has already passed (delayed or busy WP cron).
		$now = time();
		++$postpone_process_data['retries'];
		$postpone_process_data['last_attempt'] = $now;
		set_transient( 'rocket_image_optimization_process_postponed', $postpone_process_data );

		// Check if the timestamp has already passed.
		if ( $postponed_until_timestamp <= $now ) {
			Manager::debug( 'Postponing timestamp has already passed. $postponed_until_timestamp: ' . gmdate( 'Y-m-d H:i:s', $postponed_until_timestamp ) . ', $now: ' . gmdate( 'Y-m-d H:i:s', $now ) );
			return false;
		}

		// Reschedule the process.
		$this->reschedule_event( $postponed_until_timestamp );
		$this->postponed = true;

		Manager::debug( 'The process has been postponed to ' . gmdate( 'Y-m-d H:i:s', $postponed_until_timestamp ) . '. Reason: ' . $postpone_process_data['reason'] . '.' );

		return true;
	}

	/**
	 * Reschedules next event to given time.
	 *
	 * @param int $time GMT timestamp of the next event.
	 *
	 * @see parent::schedule_event()
	 */
	protected function reschedule_event( $time ) {

		$this->clear_scheduled_event();

		if ( ! wp_next_scheduled( $this->cron_hook_identifier ) ) {
			wp_schedule_event( $time, $this->cron_interval_identifier, $this->cron_hook_identifier );
		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * We use this function to prevent the background process from running the task() function again in the same
	 * request. It's done by pretending that the time has been exceeded.
	 */
	protected function time_exceeded() {
		if ( $this->postponed ) {
			return true;
		}

		return parent::time_exceeded();
	}

	/**
	 * {@inheritDoc}
	 *
	 * We overload this function to prevent the background process from triggering the task() function again by
	 * dispatching a loopback request. Without this, the background process library would keep triggering the task()
	 * endlessly because the process $item is not false.
	 */
	public function dispatch() {

		if ( $this->postponed ) {
			return; // @phpstan-ignore-line
		}

		return parent::dispatch();
	}

	/**
	 * Checks if the process is postponed.
	 *
	 * Timestamp check can be skipped in case we only want to know if the transient is set. This is useful when we want
	 * check if the process can be recreated.
	 *
	 * @param bool $skip_timestamp_check Whether to skip the timestamp check.
	 *
	 * @return bool
	 */
	public function is_process_postponed( $skip_timestamp_check = false ) {

		// Check if we have a transient that postpones the process.
		$postpone_process_data = $this->get_postpone_process_data();
		if ( empty( $postpone_process_data ) ) {
			return false;
		}

		if ( ! $skip_timestamp_check ) {

			// Check if the timestamp has already passed.
			$postponed_until_timestamp = $this->calculate_next_retry_timestamp( $postpone_process_data );
			if ( $postponed_until_timestamp <= time() ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Calculates the timestamp when the process can be resumed.
	 *
	 * @param array $postpone_process_data Postpone process data.
	 *
	 * @return int
	 */
	private function calculate_next_retry_timestamp( $postpone_process_data ) {
		$last_attempt_timestamp = array_key_exists( 'last_attempt', $postpone_process_data ) ? $postpone_process_data['last_attempt'] : $postpone_process_data['created_at'];

		return $last_attempt_timestamp + $postpone_process_data['next_retry_in'];
	}

	/**
	 * Is process running
	 *
	 * Check whether the current process is already running
	 * in a background process.
	 */
	public function is_running() {
		return parent::is_process_running();
	}
}
