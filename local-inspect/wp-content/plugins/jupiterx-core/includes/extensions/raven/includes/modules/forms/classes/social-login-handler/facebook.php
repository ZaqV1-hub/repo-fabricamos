<?php

namespace JupiterX_Core\Raven\Modules\Forms\Classes\Social_Login_Handler;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Utils;
use Elementor\Settings;

/**
 * Facebook
 * Handle Social Login Process with Facebook.
 *
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @since 2.0.0
*/
class Facebook {
	const APP_ID     = 'elementor_raven_facebook_app_id';
	const APP_SECRET = 'elementor_raven_facebook_client_secret';

	/**
	 * Required actions.
	 *
	 * @since 2.0.0
	*/
	public function __construct() {
		add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_fields' ], 20 );
	}

	/**
	 * Create Setting fields to save user facebook app id
	 *
	 * @param object $settings
	 * @since 2.0.0
	 */
	public function register_admin_fields( $settings ) {
		$settings->add_section( 'raven', 'raven_facebook_app_id', [
			'callback' => function() {
				echo '<hr><h2>' . esc_html__( 'Facebook App ID', 'jupiterx-core' ) . '</h2>';
			},
			'fields' => [
				'raven_facebook_app_id' => [
					'label' => __( 'APP ID', 'jupiterx-core' ),
					'field_args' => [
						'type' => 'text',
						/* translators: %s: Facebook Developer URL  */
						'desc' => sprintf( __( 'This App ID will be used for facebook login. <a href="%s" target="_blank">Get your App ID</a>.', 'jupiterx-core' ), 'https://developers.facebook.com/' ),
					],
				],
				'raven_facebook_client_secret' => [
					'label' => __( 'APP Secret', 'jupiterx-core' ),
					'field_args' => [
						'type' => 'text',
						/* translators: %s: Facebook Developer URL  */
						'desc' => sprintf( __( 'App secret will be used for facebook login verification. <a href="%s" target="_blank">Get your App Secret</a>.', 'jupiterx-core' ), 'https://developers.facebook.com/docs/facebook-login/security/#appsecret' ),
					],
				],
			],
		] );
	}

	/**
	 * Social media render HTML.
	 *
	 * @param array $settings
	 * @param object $widget
	 * @return void
	 * @since 2.0.0
	 */
	public static function html() {
		$app_id = get_option( self::APP_ID );
		?>
			<script>
				var jxRavenFacebookAppId = '<?php echo esc_js( $app_id ); ?>';
			</script>
		<?php
	}

	/**
	 * Handle Login Process.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function ajax_handler( $ajax_handler ) {
		// Get requirements.
		$email        = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );
		$name         = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$access_token = filter_input( INPUT_POST, 'access_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$error        = true;

		if ( empty( $name ) || empty( $access_token ) ) {
			wp_send_json_error( __( 'Wrong Details.', 'jupiterx-core' ) );
		}

		if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			wp_send_json_error( __( 'Not a Valid Email.', 'jupiterx-core' ) );
		}

		$client_id     = get_option( self::APP_ID, '' );
		$client_secret = get_option( self::APP_SECRET, '' );

		if ( empty( $client_id ) || empty( $client_secret ) ) {
			wp_send_json_error( __( 'Facebook App ID or App Secret Are Not Provided.', 'jupiterx-core' ) );
		}

		/**
		 * Send request to get an access token using APP credentials.
		 *
		 * @since 3.4.2
		 */
		$url = add_query_arg(
			[
				'grant_type'    => 'client_credentials',
				'client_secret' => $client_secret,
				'client_id'     => $client_id,
			],
			'https://graph.facebook.com/oauth/access_token'
		);

		$response = wp_remote_get( $url );
		$response = json_decode( wp_remote_retrieve_body( $response ) );

		/**
		 * Send a request to verify user input token using access token.
		 *
		 * @since 3.4.2
		 */
		$url = add_query_arg(
			[
				'input_token'  => $access_token,
				'access_token' => $response->access_token,
			],
			'https://graph.facebook.com/debug_token'
		);

		$response = wp_remote_get( $url );
		$response = json_decode( wp_remote_retrieve_body( $response ) );
		$api_fbid = $response->data->user_id;

		if ( true === $response->data->is_valid || 1 === $response->data->is_valid ) {
			$error = false;
		}

		/**
		 * Send a request to get user details using access token.
		 *
		 * @since 3.4.2
		 */
		$url = add_query_arg(
			[
				'access_token' => $access_token,
				'fields'       => 'id,name,email',
			],
			'https://graph.facebook.com/' . $api_fbid . '/'
		);

		$response  = wp_remote_get( $url );
		$response  = json_decode( wp_remote_retrieve_body( $response ) );
		$api_email = $response->email;

		if ( empty( $api_email ) || $api_email !== $email ) {
			$error = true;
		}

		if ( true === $error ) {
			wp_send_json_error( esc_html__( 'Unauthorized request', 'jupiterx-core' ) );
		}

		if ( empty( $api_fbid ) ) {
			wp_send_json_error( esc_html__( 'Unauthorized request', 'jupiterx-core' ) );
		}

		// Search in users for the Email retrieved from API.
		$user_id = email_exists( $api_email );

		// Email is not registered.
		if ( false === $user_id ) {
			$user_id = $this->create_user( $email );
		}

		if ( ! empty( $user_id ) ) {
			wp_clear_auth_cookie();
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id );
		}

		$login = [
			'siteURL' => site_url(),
		];

		if ( ! empty( $ajax_handler->form['settings']['redirect_url']['url'] ) ) {
			$login['redirectUrl'] = $ajax_handler->form['settings']['redirect_url']['url'];
		}

		wp_send_json_success( $login );
	}

	/**
	 * Create User By Given Email
	 *
	 * @param string $email
	 * @return int user_id
	 * @since 2.0.0
	 */
	private function create_user( $email ) {
		$user_data = [
			'user_login' => $email,
			'user_pass'  => wp_generate_password(),
			'user_email' => $email,
			'role'       => 'subscriber',
		];

		$user_id = wp_insert_user( $user_data );

		return $user_id;
	}
}
