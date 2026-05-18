<?php
/**
 * Copyright (с) Cloud Linux GmbH & Cloud Linux Software, Inc 2010-2023 All Rights Reserved
 */

namespace WP_Rocket\Addon\ImageOptimization;

use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

use WP_Rocket\Addon\ImageOptimization\Database\Queries\ImageOptimization as Query;

/**
 * Class RESTWP
 *
 * @package WP_Rocket\Addon\ImageOptimization
 */
class RESTWP {

	/**
	 * Namespace for REST Route.
	 */
	const ROUTE_NAMESPACE = 'clsop/v1';

	/**
	 * REST endpoint route.
	 */
	const ENDPOINT_ROUTE = 'imagemin';

	/**
	 * Options manager instance.
	 *
	 * @var OptionsManager
	 */
	private $options;

	/**
	 * Database query.
	 *
	 * @var Query database.
	 */
	private $query;

	/**
	 * RESTWP constructor.
	 *
	 * @param OptionsManager $options Instance of options manage.
	 * @param Query          $query database.
	 *
	 * @since 3.12.6.1_1.1-1
	 */
	public function __construct( OptionsManager $options, Query $query ) {
		$this->options = $options;
		$this->query   = $query;
	}

	/**
	 * Registers the route to process job notification in the WP REST API
	 *
	 * @return void
	 * @since 3.12.6.1_1.1-1
	 */
	public function register_process_notification_route() {
		register_rest_route(
			self::ROUTE_NAMESPACE,
			self::ENDPOINT_ROUTE,
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'process_notification' ],
				'args'                => [
					'id'     => [
						'description' => __( 'Job ID', 'rocket' ),
						'type'        => 'integer',
						'required'    => true,
					],
					'secret' => [
						'description' => __( 'Secret', 'rocket' ),
						'type'        => 'string',
						'required'    => true,
					],
				],
				'permission_callback' => function () {
					if ( ! $this->options->is_image_optimization_enabled() ) {
						return new WP_Error(
							'image_optimization_not_enabled',
							__( 'Image optimization is not enabled.', 'rocket' ),
							[
								'status' => 400,
							]
						);
					}

					return true;
				},
			]
		);
	}

	/**
	 * Processes the notification for the requested job ID.
	 *
	 * @param WP_REST_Request $request WP REST request response.
	 *
	 * @return WP_REST_Response|WP_Error
	 * @since 3.12.6.1_1.1-1
	 */
	public function process_notification( WP_REST_Request $request ) {

		$job_id = $request->get_param( 'id' );
		$item   = $this->query->find_by_job_id( $job_id );
		if ( empty( $item ) ) {
			return new WP_Error(
				'image_optimization_job_not_found',
				__( 'Image optimization job not found.', 'rocket' ),
				[
					'status' => 404,
				]
			);
		}

		$secret = $request->get_param( 'secret' );
		if ( $secret !== $item->secret ) {
			return new WP_Error(
				'image_optimization_secret_mismatch',
				__( 'Image optimization secret is not correct.', 'rocket' ),
				[
					'status' => 400,
				]
			);
		}

		$this->query->make_status_to_download( $item->id );

		do_action( 'rocket_image_optimization_run_queue_worker_process' );

		return rest_ensure_response(
			[
				'success' => true,
				'code'    => 'image_download_queued',
				'message' => 'Image download added to a queue.',
				'data'    => [
					'status' => 200,
				],
			]
		);
	}

	/**
	 * Retrieves the return URL that points to this REST endpoint.
	 *
	 * @return string
	 * @since 3.12.6.1_1.1-1
	 */
	public function get_return_url() {
		return get_rest_url( null, self::ROUTE_NAMESPACE . '/' . self::ENDPOINT_ROUTE );
	}
}
