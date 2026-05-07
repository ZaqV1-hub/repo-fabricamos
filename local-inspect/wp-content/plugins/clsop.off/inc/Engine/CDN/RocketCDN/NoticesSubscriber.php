<?php
/**
 * Source file was changed by CloudLinux on Wed Jul 02 14:54:30 2025 +0000
 */
namespace WP_Rocket\Engine\CDN\RocketCDN;

use WP_Rocket\Abstract_Render;
use WP_Rocket\Engine\Admin\Beacon\Beacon;
use WP_Rocket\Event_Management\Subscriber_Interface;

/**
 * Subscriber for the RocketCDN notices on WP Rocket settings page
 *
 * @since 3.5
 */
class NoticesSubscriber extends Abstract_Render implements Subscriber_Interface {
	/**
	 * RocketCDN API Client instance.
	 *
	 * @var APIClient
	 */
	private $api_client;

	/**
	 * Beacon instance
	 *
	 * @var Beacon
	 */
	private $beacon;

	/**
	 * Constructor
	 *
	 * @param APIClient $api_client RocketCDN API Client instance.
	 * @param Beacon    $beacon  Beacon instance.
	 * @param string    $template_path Path to the templates.
	 */
	public function __construct( APIClient $api_client, Beacon $beacon, $template_path ) {
		parent::__construct( $template_path );

		$this->api_client = $api_client;
		$this->beacon     = $beacon;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_subscribed_events() {
		return [
			'admin_notices'                       => [
				// [ 'promote_rocketcdn_notice' ], CL.
				[ 'purge_cache_notice' ],
				// [ 'change_cname_notice' ], CL.
				[ 'cl_notice_cdn_limit' ], // CL.
			],
			'wp_ajax_cl_cdn_dismiss_limit_notice' => 'dismiss_limit_notice',
			// 'rocket_before_cdn_sections'       => 'display_rocketcdn_cta', CL.
			// 'wp_ajax_toggle_rocketcdn_cta'     => 'toggle_cta', CL.
			// 'wp_ajax_rocketcdn_dismiss_notice' => 'dismiss_notice', CL.
			// 'admin_footer'                     => 'add_dismiss_script', CL.
		];
	}

	/**
	 * Adds notice to promote RocketCDN on settings page
	 *
	 * @since 3.5
	 *
	 * @return void
	 */
	public function promote_rocketcdn_notice() {
		/**
		 * Filters RocketCDN promotion notice.
		 *
		 * @param bool $promotion_notice; true to display, false otherwise.
		 */
		if ( ! apply_filters( 'rocket_promote_rocketcdn_notice', true ) ) {
			return;
		}

		if ( $this->is_white_label_account() ) {
			return;
		}

		if ( ! rocket_is_live_site() ) {
			return;
		}

		if ( ! $this->should_display_notice() ) {
			return;
		}

		echo $this->generate( 'promote-notice' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Dynamic content is properly escaped in the view.
	}

	/**
	 * Adds inline script to permanently dismissing the RocketCDN promotion notice
	 *
	 * @since 3.5
	 *
	 * @return void
	 */
	public function add_dismiss_script() {
		if ( $this->is_white_label_account() ) {
			return;
		}

		if ( ! rocket_is_live_site() ) {
			return;
		}

		if ( ! $this->should_display_notice() ) {
			return;
		}

		$nonce = wp_create_nonce( 'rocketcdn_dismiss_notice' );
		?>
		<script>
		window.addEventListener( 'load', function() {
			var dismissBtn  = document.querySelectorAll( '#rocketcdn-promote-notice .notice-dismiss, #rocketcdn-promote-notice #rocketcdn-learn-more-dismiss' );

			dismissBtn.forEach(function(element) {
				element.addEventListener( 'click', function( event ) {
					var httpRequest = new XMLHttpRequest(),
						postData    = '';

					postData += 'action=rocketcdn_dismiss_notice';
					postData += '&nonce=<?php echo esc_attr( $nonce ); ?>';
					httpRequest.open( 'POST', '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>' );
					httpRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' )
					httpRequest.send( postData );
				});
			});
		});
		</script>
		<?php
	}

	/**
	 * Checks if the promotion notice should be displayed
	 *
	 * @since 3.5
	 *
	 * @return boolean
	 */
	private function should_display_notice() {
		if ( ! current_user_can( 'rocket_manage_options' ) ) {
			return false;
		}

		if ( 'settings_page_clsop' !== get_current_screen()->id ) {
			return false;
		}

		if ( get_user_meta( get_current_user_id(), 'rocketcdn_dismiss_notice', true ) ) {
			return false;
		}

		$subscription_data = $this->api_client->get_subscription_data();

		return 'running' !== $subscription_data['subscription_status'];
	}

	/**
	 * Ajax callback to save the dismiss as a user meta
	 *
	 * @since 3.5
	 *
	 * @return void
	 */
	public function dismiss_notice() {
		check_ajax_referer( 'rocketcdn_dismiss_notice', 'nonce', true );

		if ( ! current_user_can( 'rocket_manage_options' ) ) {
			wp_send_json_error( 'no permissions' );
		}

		update_user_meta( get_current_user_id(), 'rocketcdn_dismiss_notice', true );

		wp_send_json_success();
	}

	/**
	 * Displays the RocketCDN Call to Action on the CDN tab of WP Rocket settings page
	 *
	 * @since 3.5
	 *
	 * @note CL
	 * @return void
	 */
	public function display_rocketcdn_cta() {
		/**
		 * Filters the display of the RocketCDN cta banner.
		 *
		 * @param bool $display_cta_banner; true to display, false otherwise.
		 */
		if ( ! apply_filters( 'rocket_display_rocketcdn_cta', true ) ) {
			return;
		}

		if ( $this->is_white_label_account() ) {
			return;
		}

		if ( ! rocket_is_live_site() ) {
			return;
		}

		$subscription_data = $this->api_client->get_subscription_data();

		if ( 'running' === $subscription_data['subscription_status'] ) {
			return;
		}

		$pricing = $this->api_client->get_pricing_data();

		$regular_price   = '';
		$nopromo_variant = '--no-promo';
		$cta_small_class = 'wpr-isHidden';
		$cta_big_class   = '';

		if ( get_user_meta( get_current_user_id(), 'rocket_rocketcdn_cta_hidden', true ) ) {
			$cta_small_class = '';
			$cta_big_class   = 'wpr-isHidden';
		}

		$small_cta_data = [
			'container_class' => $cta_small_class,
		];

		if ( is_wp_error( $pricing ) ) {
			$beacon    = $this->beacon->get_suggest( 'rocketcdn_error' );
			$more_info = sprintf(
				// translators: %1$is = opening link tag, %2$s = closing link tag.
				__( '%1$sMore Info%2$s', 'rocket' ),
				'<a href="' . esc_url( $beacon['url'] ) . '" data-beacon-article="' . esc_attr( $beacon['id'] ) . '" rel="noopener noreferrer" target="_blank">',
				'</a>'
			);

			$message = $pricing->get_error_message(); // Temporary off . ' ' . $more_info.

			$big_cta_data = [
				'container_class' => $cta_big_class,
				'nopromo_variant' => $nopromo_variant,
				'error'           => true,
				'message'         => $message,
			];
		} else {
			$current_price      = number_format_i18n( $pricing['monthly_price'], 2 );
			$promotion_campaign = '';
			$end_date           = strtotime( $pricing['end_date'] );
			$promotion_end_date = '';

			if (
				$pricing['is_discount_active']
				&&
				$end_date > time()
			) {
				$promotion_campaign = $pricing['discount_campaign_name'];
				$regular_price      = $current_price;
				$current_price      = number_format_i18n( $pricing['discounted_price_monthly'], 2 ) . '*';
				$nopromo_variant    = '';
				$promotion_end_date = date_i18n( get_option( 'date_format' ), $end_date );
			}

			$big_cta_data = [
				'container_class'    => $cta_big_class,
				'promotion_campaign' => $promotion_campaign,
				'promotion_end_date' => $promotion_end_date,
				'nopromo_variant'    => $nopromo_variant,
				'regular_price'      => $regular_price,
				'current_price'      => $current_price,
			];
		}

		echo $this->generate( 'cta-small', $small_cta_data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Dynamic content is properly escaped in the view.
		echo $this->generate( 'cta-big', $big_cta_data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Dynamic content is properly escaped in the view.
	}

	/**
	 * Toggles display of the RocketCDN CTAs on the settings page
	 *
	 * @since 3.5
	 *
	 * @return void
	 */
	public function toggle_cta() {
		check_ajax_referer( 'rocket-ajax', 'nonce', true );

		if ( ! current_user_can( 'rocket_manage_options' ) ) {
			wp_send_json_error( 'no permissions' );
		}

		if ( ! isset( $_POST['status'] ) ) {
			wp_send_json_error( 'missing status' );
		}

		if ( 'big' === $_POST['status'] ) {
			delete_user_meta( get_current_user_id(), 'rocket_rocketcdn_cta_hidden' );
		} elseif ( 'small' === $_POST['status'] ) {
			update_user_meta( get_current_user_id(), 'rocket_rocketcdn_cta_hidden', true );
		}

		wp_send_json_success();
	}

	/**
	 * Displays a notice after purging the RocketCDN cache.
	 *
	 * @since 3.5
	 *
	 * @note CL
	 * @return void
	 */
	public function purge_cache_notice() {
		if ( ! current_user_can( 'rocket_manage_options' ) ) {
			return;
		}

		if ( 'settings_page_clsop' !== get_current_screen()->id ) {
			return;
		}

		$purge_response = get_transient( 'rocketcdn_purge_cache_response' );

		if ( ! is_array( $purge_response ) ) { // CL.
			return;
		}

		$message = $purge_response['message'];

		if ( 'error' === $purge_response['status'] ) {
			$beacon    = $this->beacon->get_suggest( 'rocketcdn_error' );
			$more_info = sprintf(
				// translators: %1$is = opening link tag, %2$s = closing link tag.
				__( '%1$sMore Info%2$s', 'rocket' ),
				'<a href="' . esc_url( $beacon['url'] ) . '" data-beacon-article="' . esc_attr( $beacon['id'] ) . '" rel="noopener noreferrer" target="_blank">',
				'</a>'
			);

			// CL. $message .= ' ' . $more_info.
		}

		delete_transient( 'rocketcdn_purge_cache_response' );

		rocket_notice_html(
			[
				'status'  => $purge_response['status'],
				'message' => $message,
			]
		);
	}

	/**
	 * Displays a notice with upgrade link.
	 *
	 * @since 3.12.6.1-1.1-2
	 *
	 * @note CL
	 * @return void
	 */
	public function cl_notice_cdn_limit() {
		if ( ! current_user_can( 'rocket_manage_options' ) ) {
			return;
		}

		$limit = get_transient( 'rocket_rocketcdn_limit_reached' );
		if ( ! $limit ) {
			return;
		}

		$options = get_transient( 'rocket_rocketcdn_user_info' );

		$message  = '<p><strong>AccelerateWP</strong><br />';
		$message .= sprintf(
			// translators: %s = limit.
			esc_html__( 'Content Delivery Network: You have reached your %s limit. Please upgrade your subscription.', 'rocket' ),
			$limit
		);

		if (
			is_array( $options )
			&&
			isset( $options['upgrade_url_cdn'] )
			&&
			! empty( $options['upgrade_url_cdn'] )
			) {
			$message .= sprintf(
				' <a href="%s" data-cl-accelerate-wp-notice-upgrade="link">%s</a></p>',
				esc_url( $options['upgrade_url_cdn'] ),
				esc_html__( 'Upgrade subscription', 'rocket' )
			);
		} else {
			$message .= '</p><p><strong>Please contact your hosting provider to upgrade.</strong></p>';
		}

		rocket_notice_html(
			[
				'status'  => 'warning',
				'message' => $message,
			]
		);

		echo $this->generate( 'modal-subscription' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Dynamic content is properly escaped in the view.
		echo $this->generate( 'modal-subscription-success' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Dynamic content is properly escaped in the view.
	}

	/**
	 * Ajax callback to delete limit transient
	 *
	 * @since 3.12.6.1-1.1-2
	 *
	 * @return void
	 */
	public function dismiss_limit_notice() {
		check_ajax_referer( 'rocket-ajax' );

		if ( ! current_user_can( 'rocket_manage_options' ) ) {
			return;
		}

		delete_transient( 'rocket_rocketcdn_limit_reached' );
	}

	/**
	 * Checks if white label is enabled
	 *
	 * @since 3.6
	 *
	 * @return bool
	 */
	private function is_white_label_account() {
		return (bool) rocket_get_constant( 'WP_ROCKET_WHITE_LABEL_ACCOUNT' );
	}

	/**
	 * Change CName admin notice contents.
	 *
	 * @return void
	 */
	public function change_cname_notice() {
		return; // CL.
	}
}
