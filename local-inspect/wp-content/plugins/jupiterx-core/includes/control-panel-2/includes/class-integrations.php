<?php
defined( 'ABSPATH' ) || die();

/**
 * Integrations settings handler for JupiterX Control Panel.
 * Reads/writes the same option keys used by Elementor's raven tab,
 * so no DB migration is required.
 *
 * @package JupiterX_Core\Control_Panel_2
 * @since 4.9.0
 */
class JupiterX_Core_Control_Panel_Integrations {

	/**
	 * All integration option keys (stored with elementor_ prefix in WP options).
	 */
	const OPTION_KEYS = [
		// Google
		'raven_google_api_key',
		'raven_google_client_id',
		// reCAPTCHA v2
		'raven_recaptcha_site_key',
		'raven_recaptcha_secret_key',
		// reCAPTCHA v3
		'raven_recaptcha_v3_site_key',
		'raven_recaptcha_v3_secret_key',
		'raven_recaptcha_v3_threshold',
		// Stripe
		'sellkit_stripe_test_secret_key',
		'sellkit_stripe_live_secret_key',
		// Facebook
		'raven_facebook_app_id',
		'raven_facebook_client_secret',
		// Twitter / X
		'raven_twitter_api_key',
		'raven_twitter_api_secret',
		'raven_twitter_access_token',
		'raven_twitter_access_token_secret',
		// Email marketing
		'raven_mailchimp_api_key',
		'raven_activecampaign_api_key',
		'raven_activecampaign_api_url',
		'raven_mailerlite_api_key',
		'raven_getresponse_api_key',
		'raven_drip_api_key',
		'raven_convertkit_api_key',
	];

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_jupiterx_get_integrations', [ $this, 'get_integrations' ] );
		add_action( 'wp_ajax_jupiterx_save_integrations', [ $this, 'save_integrations' ] );
	}

	/**
	 * Return all integration options to the frontend.
	 */
	public function get_integrations() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized.', 'jupiterx-core' ) ], 403 );
		}

		$values = [];

		foreach ( self::OPTION_KEYS as $key ) {
			$values[ $key ] = get_option( 'elementor_' . $key, '' );
		}

		wp_send_json_success( $values );
	}

	/**
	 * Save integration options submitted from the frontend.
	 * Only whitelisted keys are accepted; values are sanitised as text.
	 */
	public function save_integrations() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Unauthorized.', 'jupiterx-core' ) ], 403 );
		}

		$data = isset( $_POST['data'] ) ? (array) $_POST['data'] : []; // phpcs:ignore WordPress.Security.NonceVerification

		foreach ( self::OPTION_KEYS as $key ) {
			if ( array_key_exists( $key, $data ) ) {
				update_option( 'elementor_' . $key, sanitize_text_field( wp_unslash( $data[ $key ] ) ) );
			}
		}

		wp_send_json_success( [ 'message' => __( 'Settings saved.', 'jupiterx-core' ) ] );
	}
}

new JupiterX_Core_Control_Panel_Integrations();
