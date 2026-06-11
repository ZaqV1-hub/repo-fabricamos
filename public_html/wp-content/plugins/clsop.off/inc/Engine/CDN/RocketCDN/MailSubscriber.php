<?php
/**
 * Source file was changed on the Sat May 13 11:44:48 2023 +0200
 */

namespace WP_Rocket\Engine\CDN\RocketCDN;

use WP_Rocket\Abstract_Render;
use WP_Rocket\Event_Management\Subscriber_Interface;

/**
 * Mail Subscriber
 *
 * @note CL
 * @since 3.12.6.1-1.1-2
 */
class MailSubscriber extends Abstract_Render implements Subscriber_Interface {
	const TEMPLATES = [
		'limit_reached' => 'letter.cdn.limit',
	];

	/**
	 * A cPanel user type string.
	 *
	 * @var string
	 */
	const PANEL_CPANEL_TYPE = 'cpanel';

	/**
	 * A Plesk user type string.
	 *
	 * @var string
	 */
	const PANEL_PLESK_TYPE = 'plesk';

	/**
	 * Panel user type string.
	 *
	 * @var string
	 */
	const USER_PANEL_TYPE = 'panel';

	/**
	 * WordPress user type string.
	 *
	 * @var string
	 */
	const USER_WP_TYPE = 'wp';

	/**
	 * {@inheritdoc}
	 */
	public static function get_subscribed_events() {
		return [
			'rocket_cl_cdn_send_limit_reached_mail' => 'send_limit_reached_mail',
		];
	}

	/**
	 * Send e-mails.
	 *
	 * @return void
	 */
	public function send_limit_reached_mail() {
		$panel_emails = self::get_user_info( 'panel_emails' ) ?? '';
		$home         = get_rocket_i18n_home_url();
		$limit        = get_transient( 'rocket_rocketcdn_limit_reached' );
		$recipients   = $this->recipients( $panel_emails );
		$template     = self::TEMPLATES['limit_reached'];
		$subject      = $this->subject( $home, $limit );
		$headers      = $this->headers();
		foreach ( $recipients as $user_type => $emails ) {
			$attr = $this->attr( $user_type, $home, $limit );
			$body = $this->body( $template, $attr );

			foreach ( $emails as $email ) {
				$this->send( $user_type, $email, $subject, $body, $headers );
			}
		}
	}

	/**
	 * Merge WP users and Panel with duplicate filtering
	 *
	 * @param string $panel_user_emails List of emails.
	 *
	 * @return array
	 */
	public function recipients( $panel_user_emails ) {
		$admin_emails = get_users(
			[
				'role__in' => [ 'Administrator' ],
				'fields'   => 'user_email',
			]
		);

		$panel_emails = explode( ',', $panel_user_emails );
		$panel_emails = array_map( 'trim', $panel_emails );
		$panel_emails = array_diff( $panel_emails, [ '' ] );
		$panel_emails = array_diff( $panel_emails, $admin_emails );

		return [
			self::USER_PANEL_TYPE => $panel_emails,
			self::USER_WP_TYPE    => $admin_emails,
		];
	}

	/**
	 * Headers.
	 *
	 * @return string[]
	 */
	public function headers() {
		return [ 'Content-Type: text/html; charset=UTF-8' ];
	}

	/**
	 * Subject.
	 *
	 * @param string $site_url url.
	 * @param string $limit limit string.
	 *
	 * @return string
	 */
	public function subject( $site_url, $limit ) {
		return sprintf(
			// translators: %1$s = limit; %2$s = site_url.
			esc_html__( 'You have reached your %1$s limit. Please upgrade your subscription for %2$s', 'rocket' ),
			$limit,
			$site_url
		);
	}

	/**
	 * Render body
	 *
	 * @param string $template template.
	 * @param array  $attr data.
	 *
	 * @return string
	 */
	public function body( $template, $attr ) {
		return $this->generate( $template, $attr );
	}

	/**
	 * Template attributes.
	 *
	 * @param string $user_type Type of user.
	 * @param string $home home URL.
	 * @param string $limit limit.
	 *
	 * @return array
	 */
	public function attr( $user_type, $home, $limit ) {
		$attr = [
			'site_url'   => $home,
			'panel_link' => get_admin_url(),
			'limit'      => $limit,
		];

		$panel_url = $this->panel_url();
		if ( self::USER_PANEL_TYPE === $user_type && ! empty( $panel_url ) ) {
			$attr['panel_link'] = $panel_url;
		}
		return $attr;
	}

	/**
	 * Get user info.
	 *
	 * @param string $option_name Option name.
	 *
	 * @return mixed
	 */
	public static function get_user_info( $option_name ) {
		$options = get_transient( 'rocket_rocketcdn_user_info' );
		if ( ! is_array( $options ) ) {
			return false;
		}
		return $options[ $option_name ] ?? false;
	}

	/**
	 * Panel url.
	 *
	 * @return string|null
	 */
	public function panel_url() {
		$panel_url  = $this->get_user_info( 'panel_url' );
		$panel_url  = rtrim( $panel_url, '/' );
		$panel_type = $this->get_user_info( 'panel_type' );
		if ( ! empty( $panel_url ) ) {
			if ( self::PANEL_CPANEL_TYPE === strtolower( $panel_type ) ) {
				return $panel_url . '/cpsess0000000000/frontend/paper_lantern/lveversion/wpos.live.pl';
			}

			if ( self::PANEL_PLESK_TYPE === strtolower( $panel_type ) ) {
				return $panel_url . '/modules/plesk-lvemanager/index.php/awp/index#/';
			}
		}
		return null;
	}


	/**
	 * Send emails.
	 *
	 * @param string $user_type Type of user.
	 * @param string $email Email.
	 * @param string $subject Letter subject.
	 * @param string $body Letter body.
	 * @param array  $headers Letter header.
	 *
	 * @return void
	 */
	public function send( $user_type, $email, $subject, $body, $headers ) {
		if ( self::USER_PANEL_TYPE === $user_type ) {
			add_filter( 'wp_mail_from', [ $this, 'panelSenderMail' ] );
			add_filter( 'wp_mail_from_name', [ $this, 'panelSenderName' ] );
		}

		wp_mail( $email, $subject, $body, $headers );

		if ( self::USER_PANEL_TYPE === $user_type ) {
			remove_filter( 'wp_mail_from', [ $this, 'panelSenderMail' ] );
			remove_filter( 'wp_mail_from_name', [ $this, 'panelSenderName' ] );
		}
	}

	/**
	 * A cPanel Sender Mailbox.
	 *
	 * @return string
	 */
	public function panelSenderMail() {
		return 'AccelerateWP@' . $this->domain();
	}

	/**
	 * A cPanel Sender Name.
	 *
	 * @return string
	 */
	public function panelSenderName() {
		return 'AccelerateWP';
	}

	/**
	 * Site domain name
	 *
	 * @return string
	 */
	public function domain() {
		if ( defined( 'WP_HOME' ) ) {
			$home = (string) WP_HOME;
		} else {
			// @phpstan-ignore-next-line
			$home = (string) get_option( 'home' );
		}

		$parse = wp_parse_url( $home );

		if ( ! is_array( $parse ) ) {
			$parse = [];
		}

		return array_key_exists( 'host', $parse ) ? $parse['host'] : '';
	}
}
