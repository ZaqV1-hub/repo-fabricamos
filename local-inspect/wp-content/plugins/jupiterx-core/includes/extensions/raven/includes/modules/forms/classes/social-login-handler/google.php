<?php

namespace JupiterX_Core\Raven\Modules\Forms\Classes\Social_Login_Handler;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Utils;

/**
 * Google
 * Handle Social Login Process with Google.
 *
 * @since 2.0.0
*/
class Google {
	/**
	 * Ajax handler.
	 *
	 * @return void
	 * @since 2.0.0
	 */
	public function ajax_handler( $ajax_handler ) {
		$token    = filter_input( INPUT_POST, 'token', FILTER_SANITIZE_SPECIAL_CHARS );
		$url      = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $token;
		$response = wp_remote_get( $url );

		if ( ! is_array( $response ) || is_wp_error( $response ) ) {
			wp_send_json_error( __( 'Google API Error', 'jupiterx-core' ) );
		}

		$body        = $response['body'];
		$information = json_decode( $body, true );

		if ( 'true' !== $information['email_verified'] ) {
			wp_send_json_error( __( 'We could not get user email from google api', 'jupiterx-core' ) );
		}

		$email            = $information['email'];
		$return_client_id = $information['aud'];
		$user_client_id   = get_option( 'elementor_raven_google_client_id' );

		if ( $user_client_id !== $return_client_id ) {
			wp_send_json_error( __( 'Verify process has failed.', 'jupiterx-core' ) );
		}

		$user_id = email_exists( $email );

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
	 * @param [String] $email
	 * @return int
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

	/**
	 * Social media render HTML.
	 *
	 * @param array $settings
	 * @param object $widget
	 * @return void
	 * @since 2.0.0
	 */
	public static function html() {
		$user_client_id = get_option( 'elementor_raven_google_client_id' );
		?>
			<script>
				var jxRavenSocialWidgetGoogleClient = '<?php echo esc_js( $user_client_id ); ?>';
			</script>
		<?php
	}
}
