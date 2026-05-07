<?php
/**
 * Handle sellkit admin notice.
 *
 * @since 2.0.6
 *
 * @package JupiterX\Framework\Admin\Notices
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sellkit admin notice class.
 *
 * @since 2.0.6
 *
 * @package JupiterX\Framework\Admin\Notices
 */
class JupiterX_Sellkit_Admin_Notice {
	/**
	 * Current user.
	 *
	 * @var WP_User
	 */
	public $user;

	/**
	 * Meta key.
	 */
	const META_KEY = 'online_store_sellkit_install_noctice';

	/**
	 * Constructor.
	 *
	 * @since 2.0.6
	 */
	public function __construct() {
		$this->user = wp_get_current_user();

		add_action( 'admin_notices', [ $this, 'check_plugins' ] );
		add_action( 'wp_ajax_jupiterx_install_sellkit_in_notice', [ $this, 'install_plugins' ] );
		add_action( 'wp_ajax_jupiterx_dismiss_sellkit_notice', [ $this, 'dismiss_notice' ] );
	}

	/**
	 * Check the plugins and conditions to run notice.
	 *
	 * @since 2.0.6
	 */
	public function check_plugins() {
		$enable_notices = apply_filters( 'jupiterx_disable_admin_notices', false );

		if ( $enable_notices ) {
			return;
		}

		if (
			! function_exists( 'WC' ) ||
			class_exists( 'Sellkit_Pro' ) ||
			class_exists( 'Sellkit' ) ||
			! jupiterx_is_pro() ||
			strval( 1 ) === get_user_meta( $this->user->ID, self::META_KEY . '_dismissed', true )
		) {
			return;
		}

		$nonce = wp_create_nonce( 'jupiterx_install_sellkit_in_notice_nonce' );

		$this->get_notice( $nonce );
	}

	/**
	 * Fetch data on click.
	 *
	 * @since 2.0.6
	 */
	public function install_plugins() {
		if ( ! current_user_can( 'edit_others_posts' ) || ! current_user_can( 'edit_others_pages' ) ) {
			wp_send_json_error( 'You do not have access to this section', 'jupiterx' );
		}

		$plugins = [
			'sellkit' => [
				'sellkit/sellkit.php',
				'https://downloads.wordpress.org/plugin/sellkit.latest-stable.zip',
			],
			'sellkit-pro' => [
				'sellkit-pro/sellkit-pro.php',
				get_transient( 'jupiterx_sellkit_pro_link' ),
			],
		];

		foreach ( $plugins as $plugin ) {
			$install = null;

			if ( ! $this->check_is_installed( $plugin[0] ) ) {
				$install = $this->install_plugin( $plugin[1] );
			}

			if ( ! is_wp_error( $install ) && $install ) {
				activate_plugin( $plugin[0] );
			}

			if ( $this->check_is_installed( $plugin[0] ) && ! is_plugin_active( $plugin[0] ) ) {
				activate_plugin( $plugin[0] );
			}
		}

		wp_send_json_success();
	}

	/**
	 * Dismiss notice.
	 *
	 * @since 2.0.6
	 * @return void
	 */
	public function dismiss_notice() {
		if ( ! current_user_can( 'edit_others_posts' ) || ! current_user_can( 'edit_others_pages' ) ) {
			wp_send_json_error( 'You do not have access to this section', 'jupiterx' );
		}

		check_ajax_referer( 'jupiterx_install_sellkit_in_notice_nonce' );

		update_user_meta( $this->user->ID, self::META_KEY . '_dismissed', 1 );

		wp_send_json_success();
	}

	/**
	 * Install plugin.
	 *
	 * @param string $plugin_zip download link of the plugin.
	 * @since 2.0.6
	 */
	private function install_plugin( $plugin_zip ) {
		if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$upgrader  = new Plugin_Upgrader();
		$installed = $upgrader->install( $plugin_zip );

		return $installed;
	}

	/**
	 * Install plugin.
	 *
	 * @param string $base plugin base path.
	 * @since 2.0.6
	 */
	private function check_is_installed( $base ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();

		if ( ! empty( $all_plugins[ $base ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get Notice.
	 *
	 * @param string $nonce ajax nonce.
	 * @since 2.0.6
	 */
	private function get_notice( $nonce ) {
		$information = [
			'unbundled' => [
				'cta' => 'https://getsellkit.com/pricing/?utm_source=pro-plugin-promotion-banner-to-free-users&utm_medium=wp-dashboard&utm_campaign=upgrade-to-pro',
			],
		];

		?>
		<div data-nonce="<?php echo esc_attr( $nonce ); ?>" class="sellkit-notice-in-jupiterx notice is-dismissible">
			<div class="jupiterx-core-install-notice-logo">
				<img width="30" src="<?php echo esc_url( JUPITERX_ADMIN_ASSETS_URL . 'images/jupiterx-notice-logo.png' ); ?>" alt="<?php esc_html_e( 'Jupiter X', 'jupiterx' ); ?>" />
			</div>
			<div class="jupiterx-core-install-notice-content">
				<h2>
					<?php
						echo esc_html__( 'Building an Online Store with JupiterX?', 'jupiterx' );
					?>
				</h2>
				<p>
					<?php
						$contnet = sprintf(
							/* translators: 1: `<a>` opening tag, 2: `</a>` closing tag, 3: `<strong>` opening tag, 3: `</strong>` closing tag. */
							esc_html__( 'JupiterX offers a wide range of features for your online store through our in-house developed plugin, %1$sSellkit%2$s. You get an advanced checkout process, sales funnel, smart alerts, dynamic discounts, coupons, and more. This plugin, along with %3$sSellkit Pro%4$s, is completely free for JupiterX license owners.', 'jupiterx' ),
							' <a href="https://getsellkit.com/" traget="_blank">',
							'</a>',
							'<strong>',
							'</strong>'
						);

						echo wp_kses_post( $contnet );
					?>
				</p>
				<?php
					$link = '#';

					if ( ! defined( 'SELLKIT_BUNDLED' ) ) {
						$link = $information['unbundled']['cta'];
					}
				?>
				<button class="button button-primary jupiterx-notice-install-sellkit" href="<?php echo esc_url( $link ); ?>">
					<?php echo esc_html__( 'Activate Sellkit & Sellkit Pro', 'jupiterx' ); ?>
				</button>
				<button class="jupiterx-dismiss-sellkit-notice notice-dismiss" href="#"></button>
			</div>
		</div>
		<?php
	}
}

new JupiterX_Sellkit_Admin_Notice();
