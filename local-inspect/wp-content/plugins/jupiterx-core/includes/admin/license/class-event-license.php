<?php
/**
 * The file class that handles events of license.
 *
 * @package JupiterX_Core\Admin\License
 *
 * @since 4.10.1
 */

class JupiterX_Core_Event_License {

	const ARTBEES_THEMES_API                          = 'https://my.artbees.net/wp-json/artbees_license';
	const ENVATO_ITEM_ID                              = '5177775';
	const PURCHASE_CODE_OPTION_NAME                   = 'envato_purchase_code_' . self::ENVATO_ITEM_ID;
	const ACCESS_TOKEN_OPTION_NAME                    = 'api_access_token';
	const API_KEY_OPTION_NAME                         = 'api_key';
	const EMAIL_OPTION_NAME                           = 'api_email';
	const EXPIRY_OPTION_NAME                          = 'api_expiry';
	const IS_REGISTERED_ON_ANOTHER_DOMAIN_OPTION_NAME = 'is_registered_on_another_domain';

	/**
	 * Class instance.
	 *
	 * @since 4.10.1
	 *
	 * @var JupiterX_Core_Event_License Class instance.
	 */
	private static $instance = null;

	/**
	 * Get a class instance.
	 *
	 * @since 4.10.1
	 *
	 * @return JupiterX_Core_Event_License Class instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 4.10.1
	 */
	public function __construct() {
		add_action( 'jupiterx_license_checks', [ $this, 'validate_license' ] );
	}

	/**
	 * Validate license.
	 *
	 * @since 4.10.1
	 */
	public function validate_license() {
		if ( ! $this->has_api_key() || ! $this->has_access_token() ) {
			return;
		}

		$result = wp_remote_post( static::ARTBEES_THEMES_API . '/validate_license', [
			'body' => [
				'api_key' => $this->get_option( self::API_KEY_OPTION_NAME ),
				'domain' => $this->get_domain(),
			],
		]);

		if ( is_wp_error( $result ) ) {
			return;
		}

		$body = json_decode( wp_remote_retrieve_body( $result ) );

		if ( false === $body->status ) {
			$this->update_option( self::IS_REGISTERED_ON_ANOTHER_DOMAIN_OPTION_NAME, true );

			//TODO: Remove License in next release
		} else {
			$this->update_option( self::IS_REGISTERED_ON_ANOTHER_DOMAIN_OPTION_NAME, false );
		}
	}

	/**
	 * Check API key from the database.
	 *
	 * @since 4.10.1
	 *
	 * @return boolean API key status.
	 */
	private function has_api_key() {
		return ! empty( $this->get_option( self::API_KEY_OPTION_NAME ) );
	}

	/**
	 * Check access token from the database.
	 *
	 * @since 4.10.1
	 *
	 * @return boolean Access token status.
	 */
	private function has_access_token() {
		return ! empty( $this->get_option( self::ACCESS_TOKEN_OPTION_NAME ) );
	}

	/**
	 * Get email.
	 *
	 * @since 4.10.1
	 *
	 * @return string License email.
	 */
	private function get_email() {
		return $this->get_option( self::EMAIL_OPTION_NAME );
	}

	/**
	 * Get expiry.
	 *
	 * @since 4.10.1
	 *
	 * @return string License expiry.
	 */
	private function get_expiry() {
		return $this->get_option( self::EXPIRY_OPTION_NAME );
	}

	/**
	 * Check license status.
	 *
	 * @since 4.10.1
	 *
	 * @return boolean License status.
	 */
	private function is_registered() {
		$access_token = $this->has_access_token() ? $this->has_access_token() : true;
		$email        = ! empty( $this->get_email() ) ? $this->get_email() : true;
		$expiry       = ! empty( $this->get_expiry() ) ? $this->get_expiry() : true;

		return (
			$this->has_api_key() &&
			$access_token &&
			$email &&
			$expiry
		);
	}

	/**
	 * Get license details.
	 *
	 * @since 4.10.1
	 */
	public function get_details() {
		return [
			'is_registered'    => $this->is_registered(),
			'has_access_token' => $this->has_access_token(),
			'has_api_key'      => $this->has_api_key(),
			'email'            => $this->get_email(),
			'expiry'           => $this->get_expiry(),
		];
	}

	/**
	 * Update option.
	 *
	 * @since 4.10.1
	 *
	 * @param string $name Option name.
	 * @param mixed $value Update value.
	 *
	 * @return string Updated value.
	 */
	private function update_option( $name, $value ) {
		return jupiterx_update_option( $name, $value );
	}

	/**
	 * Get option value.
	 *
	 * @since 4.10.1
	 *
	 * @return string|null Option value.
	 */
	private function get_option( $name ) {
		if ( function_exists( 'jupiterx_get_option' ) ) {
			return jupiterx_get_option( $name, false );
		}

		return null;
	}

	/**
	 * Extract the domain (sub-domain) from URL.
	 *
	 * We keep this function here as we may change our approach for sending data of domain.
	 *
	 * @since 4.10.1
	 *
	 * @return string Domain name.
	 */
	private function get_domain() {
		return get_site_url();
	}
}

JupiterX_Core_Event_License::get_instance();
