<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization;

use WP_Rocket\Engine\Admin\Settings\Settings as AdminSettings;
use WP_Rocket\Event_Management\Subscriber_Interface;
use WP_Rocket\Addon\ImageOptimization\Database\Tables\ImageOptimization as Table;

/**
 * Image optimization Subscriber.
 *
 * @since 3.12.6.1_1.1-1
 */
class Subscriber implements Subscriber_Interface {
	/**
	 * Instance of image optimization add-on.
	 *
	 * @var Manager
	 */
	protected $manager;

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
	 * Instance of the rest wp.
	 *
	 * @var RESTWP instance of the rest wp.
	 */
	private $rest_api;

	/**
	 * Instance of the file manager.
	 *
	 * @var FileManager
	 */
	private $file_manager;

	/**
	 * Instance of the notices' handler.
	 *
	 * @var NoticesHandler
	 */
	private $notices_handler;

	/**
	 * List of new attachment IDs.
	 *
	 * @var int[]
	 */
	private $new_attachments = [];

	/**
	 * Creates an instance of the image minification subscriber.
	 *
	 * @param OptionsManager $options Options manager instance.
	 * @param Manager        $manager Image optimization manager instance.
	 * @param Table          $table Instance of the queue table.
	 * @param RESTWP         $rest_api Instance of the Rest API.
	 * @param FileManager    $file_manager Instance of the file manager.
	 * @param NoticesHandler $notices_handler Instance of the notices' handler.
	 */
	public function __construct( OptionsManager $options, Manager $manager, Table $table, RESTWP $rest_api, FileManager $file_manager, NoticesHandler $notices_handler ) {
		$this->options         = $options;
		$this->manager         = $manager;
		$this->table           = $table;
		$this->rest_api        = $rest_api;
		$this->file_manager    = $file_manager;
		$this->notices_handler = $notices_handler;
	}

	/**
	 * Return an array of events that this subscriber wants to listen to.
	 *
	 * @return array
	 * @since 3.12.6.1_1.1-1
	 */
	public static function get_subscribed_events() {
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return [
			'init' => 'schedule_cron_jobs',

			'admin_notices' => [ 'display_notices' ],

			'cron_schedules' => [ 'add_wp_cron_schedule' ],

			'rest_api_init' => [ 'register_routes' ],

			'rocket_image_optimization_accelerate_wp_cli_enable' => [ 'enable' ],

			'rocket_image_optimization_accelerate_wp_cli_disable' => [ 'disable' ],

			'rocket_first_install_options' => [ 'add_options_first_time', 12 ],

			'rocket_before_rollback' => [ 'restart_image_minification', 9 ],

			'wp_rocket_upgrade' => [ 'restart_image_minification', 9 ],

			'rocket_deactivation' => [ 'deactivation' ],

			'rocket_input_sanitize' => [ 'sanitize_options', 15, 2 ],

			'rocket_image_optimization_complete' => 'complete',

			'rocket_image_optimization_run_queue_worker_process' => 'run_queue_worker_process',

			'rocket_image_optimization_run_rescan_process' => 'run_rescan_process',

			'rocket_image_optimization_add_file_to_queue_table' => [ 'add_file_to_queue_table', 10, 2 ],

			'add_attachment' => 'process_new_attachment',

			'shutdown' => 'submit_new_attachments_to_queue',

			'rocket_image_optimization_postpone' => 'postpone',

			'rocket_image_optimization_stop' => 'stop',
		];
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
	}

	/**
	 * Enable image optimization.
	 *
	 * @param string $unique_id user.
	 *
	 * @return void
	 */
	public function enable( string $unique_id ) {
		// Check database.
		$this->table->maybe_trigger_recreate_table();
		$this->table->maybe_upgrade();

		// Check folders.
		$this->file_manager->create_dirs();

		// Update option.
		$this->options->enable( $unique_id );

		// Stop process.
		$this->manager->interrupt( true );

		// Flush rewrite rules to make sure our REST API endpoint is available.
		flush_rewrite_rules();

		// Run scanner.
		$this->run_rescan_process();
	}

	/**
	 * Plugin deactivation event.
	 *
	 * @return void
	 */
	public function deactivation() {
		$this->disable( true );
	}

	/**
	 * Disable image optimization.
	 *
	 * @param bool $is_deactivation Is plugin deactivation event.
	 * @return void
	 */
	public function disable( $is_deactivation = false ) {
		// Update option.
		$this->options->disable( $is_deactivation );

		// Stop process.
		$this->manager->interrupt( true );

		// Clear cron jobs.
		wp_clear_scheduled_hook( 'rocket_image_optimization_run_rescan_process' );
		wp_clear_scheduled_hook( 'rocket_image_optimization_run_queue_worker_process' );
	}

	/**
	 * Rescan process.
	 *
	 * @return void
	 */
	public function run_rescan_process() {
		// Start scanner.
		$this->manager->run_rescan_process();
	}

	/**
	 * Run queue worker process if not already running.
	 *
	 * @return void
	 */
	public function run_queue_worker_process() {
		// Start queue worker.
		$this->manager->run_queue_worker_process();
	}

	/**
	 * Add the image minification options to the WP Rocket options array.
	 *
	 * @param array $options WP Rocket options array.
	 *
	 * @return array
	 * @since 3.12.6.1_1.1-1
	 */
	public function add_options_first_time( $options ): array {
		$options = (array) $options;

		$options['awp_image_optimization']           = 0;
		$options['awp_image_optimization_unique_id'] = '';

		return $options;
	}

	/**
	 * Display notices.
	 *
	 * @return void
	 */
	public function display_notices() {
		$this->notices_handler->display_notices();
	}
	/**
	 * Restart the image minification.
	 *
	 * @return void
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	public function restart_image_minification() {
		$this->manager->interrupt();
		if ( $this->options->is_image_optimization_enabled() ) {
			$this->manager->run_rescan_process();
			$this->manager->run_queue_worker_process();
		}
	}

	/**
	 * Registers routes in the API.
	 *
	 * @return void
	 * @since 3.12.6.1_1.1-1
	 */
	public function register_routes() {
		$this->rest_api->register_process_notification_route();
	}

	/**
	 * Sanitizes options
	 *
	 * @param array         $input Array of values submitted from the form.
	 * @param AdminSettings $settings Settings class instance.
	 *
	 * @return array
	 */
	public function sanitize_options( $input, $settings ): array {
		if ( $this->options->is_image_optimization_enabled() ) {
			$input['awp_image_optimization_unique_id'] = $this->options->get_unique_id();
			$input['awp_image_optimization']           = 1;
			$input['cache_webp']                       = 1;
		}

		return $input;
	}

	/**
	 * Complete event, clear all cache for new images.
	 *
	 * @return void
	 */
	public function complete() {
		try {
			// Default, GoDaddy, Siteground.
			rocket_clean_domain();

			// AWP/Rocket Cdn.
			if ( 1 === $this->options->get_cdn() ) {
				do_action( 'rocketcdn_accelerate_wp_purge_cache' );
			}

			// CloudFlare.
			do_action( 'rocket_purge_cloudflare' );

			// WP Engine.
			if ( class_exists( 'WpeCommon' ) && function_exists( 'wpe_param' ) ) {
				wpe_param( 'purge-all' );
			}
		} catch ( \Throwable $e ) { // phpcs:disable Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Nothing.
		}
	}

	/**
	 * Schedules cron jobs to run the file scanner and the queue worker.
	 *
	 * @return void
	 */
	public function schedule_cron_jobs() {

		$image_optimization_enabled = $this->options->is_image_optimization_enabled();
		$events                     = [
			'rocket_image_optimization_run_rescan_process',
			'rocket_image_optimization_run_queue_worker_process',
		];

		foreach ( $events as $event ) {

			$event_scheduled = wp_next_scheduled( $event );
			if ( ! $image_optimization_enabled && $event_scheduled ) {
				wp_clear_scheduled_hook( $event );
				continue;
			}

			if ( ! $image_optimization_enabled ) {
				continue;
			}

			if ( $event_scheduled ) {
				continue;
			}

			wp_schedule_event( time(), 'daily', $event );
		}
	}

	/**
	 * Adds the new attachment ID to local list that will be processed at the end of the current request. We can't add
	 * the images to the queue here because the image subsizes are not yet generated. There is no suitable hook for us
	 * to use when subsizes are generated.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	public function process_new_attachment( $post_id ) {
		if ( ! $this->options->is_image_optimization_enabled() ) {
			return;
		}

		$mime_type = get_post_mime_type( $post_id );
		if ( preg_match( '!^image/(gif|jpg|jpeg|png)$!', $mime_type ) ) {
			$this->new_attachments[] = $post_id;
		}
	}

	/**
	 * Adds the new attachments to the queue.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	public function submit_new_attachments_to_queue() {
		if ( ! empty( $this->new_attachments ) ) {
			$this->manager->add_attachments_to_queue( $this->new_attachments, 10 );
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
	public function add_file_to_queue_table( $url, $priority ) {
		$this->manager->add_file_to_queue_table( $url, $priority );
	}

	/**
	 * Causes the image optimization process to be postponed by setting a dedicated transient.
	 *
	 * The queue worker process will check this transient next time it runs and act accordingly.
	 *
	 * @param array $args Array of arguments to be stored in the transient.
	 *
	 * @return void
	 */
	public function postpone( $args ) {

		$value = get_transient( 'rocket_image_optimization_process_postponed' );
		if ( is_array( $value ) ) {
			$args = array_merge( $value, $args );
		} else {
			$args['created_at'] = time();
			$args['retries']    = 0;
		}

		set_transient( 'rocket_image_optimization_process_postponed', $args );
	}

	/**
	 * Causes the image optimization process to stop by setting a dedicated transient.
	 *
	 * The queue worker process will check this transient next time it runs and act accordingly.
	 *
	 * @param array $args Array of arguments to be stored in the transient.
	 *
	 * @return void
	 */
	public function stop( $args ) {

		$args['created_at'] = time();

		set_transient( 'rocket_image_optimization_process_stopped_by_error', $args );
	}

	/**
	 * Add the cron schedule.
	 *
	 * @param array $schedules Array of current schedules.
	 *
	 * @return array
	 */
	public function add_wp_cron_schedule( $schedules ) {
		if ( isset( $schedules['every_minute'] ) ) {
			return $schedules;
		}

		$schedules['every_minute'] = [
			'interval' => 60, // in seconds.
			'display'  => __( 'Every minute', 'rocket' ),
		];

		return $schedules;
	}
}
