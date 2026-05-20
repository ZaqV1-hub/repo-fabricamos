<?php

/**
 * Handles theme update modal.
 *
 * @package JupiterX_Core\Control_Panel_2\Enable_Widgets
 *
 * @since 4.0.0
 */
if ( ! class_exists( 'JupiterX_Core_Control_Panel_Theme_Update' ) ) {

	/**
	 * Enable theme update modal.
	 *
	 * @since 4.0.0
	 */
	class JupiterX_Core_Control_Panel_Theme_Update {

		/**
		 * Class constructor.
		 *
		 * @since 4.0.0
		 */
		public function __construct() {
			add_action( 'wp_ajax_jupiterx_dismiss_theme_update_modal', [ $this, 'dismiss_modal' ] );
			add_action( 'wp_ajax_jupiterx_theme_update_modal', [ $this, 'update_theme' ] );
		}

		/**
		 * Handle dismiss modal.
		 *
		 * @since 4.0.0
		 */
		public function dismiss_modal() {
			check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
			}

			update_option( 'jupiterx_theme_update_modal', 'dismiss' );

			wp_send_json_success();
		}

		/**
		 * Handle update theme.
		 *
		 * @since 4.0.0
		 * @SuppressWarnings(PHPMD.NPathComplexity)
		 */
		public function update_theme() {
			check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'You do not have access to this section.', 'jupiterx-core' );
			}

			$release_note = ( new JupiterX_Core_Control_Panel_Theme_Updrades_Downgrades() )->get_release_notes();

			if ( ! is_array( $release_note ) ) {
				wp_send_json_error( 'Could not get theme update list.', 'jupiterx-core' );
			}

			if ( empty( $release_note[0] ) ) {
				wp_send_json_error( 'There is no update.', 'jupiterx-core' );
			}

			$api_key         = jupiterx_get_option( 'api_key' );
			$release_package = 'jupiterx';
			$release_id      = $release_note[0]->ID;
			$server_name     = ! empty( $_SERVER['SERVER_NAME'] ) ? sanitize_title( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : '';
			$release_version = trim( str_replace( 'V', '', $release_note[0]->post_title ) );

			if ( empty( $api_key ) ) {
				wp_send_json_error( esc_html__( 'API Key is missing.', 'jupiterx' ) );
			}

			$raw_response = wp_remote_post( 'https://artbees.net/api/v1/update-theme', [
				'body' => [
					'action'          => 'get_release_download_link',
					'apikey'          => $api_key,
					'domain'          => $server_name,
					'release_id'      => $release_id,
					'release_package' => $release_package,
				],
				'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . esc_url( home_url( '/' ) ),
			] );

			if ( is_wp_error( $raw_response ) ) {
				wp_send_json_error( $raw_response->get_error_message() );
			}

			if ( 200 !== $raw_response['response']['code'] ) {
				wp_send_json_error( $raw_response['response']['code'] );
			}

			$json_response = json_decode( json_decode( $raw_response['body'], JSON_FORCE_OBJECT ) );

			if ( ! is_object( $json_response ) ) {
				wp_send_json_error( esc_html__( 'The response is not a valid JSON object.', 'jupiterx-core' ) );
			}

			if ( ! $json_response->success ) {
				wp_send_json_error( $json_response->message );
			}

			if ( $json_response->download_link ) {
				$transient_array = [];

				$transient_array['package_url']     = $json_response->download_link;
				$transient_array['release_version'] = $release_version;
				$transient_array['release_id']      = $release_id;

				set_transient( 'jupiterx_modify_auto_update', $transient_array, 60 );
				add_filter( 'site_transient_update_themes', array( $this, 'check_for_update' ), 1 );

				update_option( 'jupiterx_theme_update_modal', 'dismiss' );

				wp_send_json_success( $json_response->download_link );
			}
		}

		/**
		 * Hook into WP check update data and inject custom array for theme WP updater
		 *
		 * @since 4.0.0
		 * @param array  $checked_data
		 * @return array $checked_data
		 */
		public function check_for_update( $checked_data ) {
			if ( ! is_object( $checked_data ) ) {
				return $checked_data;
			}

			$transient_array = get_transient( 'jupiterx_modify_auto_update' );

			if ( $transient_array ) {
				// Extract method array into variables.
				$theme_data          = ( new JupiterX_Core_Control_Panel_Theme_Updrades_Downgrades() )->get_theme_data();
				$response['theme']   = $theme_data['theme_base'];
				$response['package'] = $transient_array['package_url'];

				$response['new_version'] = $transient_array['release_version'];
				$response['url']         = 'https://themes.artbees.net/support/jupiterx/release-notes/';

				$checked_data->response[ $theme_data['theme_base'] ] = $response;
			}

			return $checked_data;
		}
	}
}

new JupiterX_Core_Control_Panel_Theme_Update();
