<?php
/**
 * This class handles init of core plugin installer.
 *
 * @since 1.0.0
 *
 * @package Jupiter\Framework\Admin\Core_Install
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Init theme core installer.
 *
 * @since 1.0.0
 *
 * @package Jupiter\Framework\Admin\Core_Install
 */
class JupiterX_Theme_Core_Install {
	/**
	 * Tgmpa instance.
	 *
	 * @since 4.0.0
	 * @var TGM_Plugin_Activation Class instance.
	 */
	public $tgmpa;

	/**
	 * List of required plugins.
	 *
	 * @since 4.0.0
	 * @var array
	 */
	const REQUIRED_PLUGINS = [
		'sellkit-pro/sellkit-pro.php',
		'sellkit/sellkit.php',
		'advanced-custom-fields/acf.php',
		'elementor/elementor.php',
		'woocommerce/woocommerce.php',
		'jupiterx-core/jupiterx-core.php',
	];

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( class_exists( 'TGM_Plugin_Activation' ) ) {
			$this->tgmpa = isset( $GLOBALS['tgmpa'] ) ? $GLOBALS['tgmpa'] : TGM_Plugin_Activation::get_instance();
		}

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_print_scripts', 'wp_print_admin_notice_templates' );
		add_action( 'admin_notices', [ $this, 'install_notice' ], 2 );
		add_action( 'wp_ajax_jupiterx_get_plugins', [ $this, 'get_required_plugins' ] );

		add_action( 'activated_plugin', [ $this, 'check_reuqired_plugins_activation' ] );
		add_action( 'deactivated_plugin', [ $this, 'check_reuqired_plugins_deactivation' ] );
	}

	/**
	 * Delete transient on activating required plugins.
	 *
	 * @param string $plugin Activated plugin basename.
	 * @since 4.0.0
	 */
	public function check_reuqired_plugins_activation( $plugin ) {
		if ( ! in_array( $plugin, self::REQUIRED_PLUGINS, true ) ) {
			return;
		}

		delete_transient( 'jupiterx_inactive_required_plugins_list' );
	}

	/**
	 * Delete transient on deactivating required plugins.
	 *
	 * @param string $plugin Deactivated plugin basename.
	 * @since 4.0.0
	 */
	public function check_reuqired_plugins_deactivation( $plugin ) {
		if ( ! in_array( $plugin, self::REQUIRED_PLUGINS, true ) ) {
			return;
		}

		delete_transient( 'jupiterx_inactive_required_plugins_list' );
	}

	/**
	 * Save required plugins on transient,
	 *
	 * @since 4.0.0
	 * @return array
	 */
	private function set_required_plugins() {
		$enable_notices = apply_filters( 'jupiterx_disable_admin_notices', false );

		if ( $enable_notices ) {
			return [];
		}

		$inactive_plugins = get_transient( 'jupiterx_inactive_required_plugins_list' );

		if ( false !== $inactive_plugins ) {
			return $inactive_plugins;
		}

		$required_plugins = jupiterx_get_inactive_required_plugins();

		set_transient( 'jupiterx_inactive_required_plugins_list', $required_plugins );

		return $required_plugins;
	}

	/**
	 * Load scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$control_panel = jupiterx_check_setup_wizard() ? admin_url( 'admin.php?page=jupiterx-setup-wizard' ) : admin_url( 'admin.php?page=' . JUPITERX_SLUG );

		wp_enqueue_style( 'jupiterx-core-install', JUPITERX_ASSETS_URL . 'dist/css/core-install' . JUPITERX_MIN_CSS . '.css', [], JUPITERX_VERSION );
		wp_enqueue_script( 'jupiterx-core-install', JUPITERX_ASSETS_URL . 'dist/js/core-install' . JUPITERX_MIN_JS . '.js', [ 'jquery', 'wp-util', 'updates' ], JUPITERX_VERSION, true );

		wp_localize_script(
			'jupiterx-core-install',
			'jupiterxCoreInstall',
			[
				'controlPanelUrl' => $control_panel,
				'isPremium' => jupiterx_is_premium(),
				'i18n' => [
					'defaultText'    => esc_html__( 'Install and activate all required plugins', 'jupiterx' ),
					'installText' => esc_html__( 'Installing required plugins', 'jupiterx' ),
					'activateText'   => esc_html__( 'Activating required plugins', 'jupiterx' ),
					'fetchText'   => esc_html__( 'Fetching required plugins', 'jupiterx' ),
					'redirecting'   => esc_html__( 'Redirecting', 'jupiterx' ),
					'failedInstallText'   => esc_html__( 'An error occurred while downloading the plugin(s). ', 'jupiterx' ),
					'failedActivateText'   => esc_html__( 'An error occurred while installing & activating the plugin(s). ', 'jupiterx' ),
					'failedActionLinks'   => sprintf(
						// translators: 1: Site health url. 2. Team support url.
						__( 'Please check your<a href="%1$s" target="_blank"> site health </a> or contact our <a href="%2$s" target="_blank">support team</a>.', 'jupiterx' ),
						esc_url( admin_url( 'site-health.php' ) ),
						esc_url( 'https://themes.artbees.net/docs/the-new-support-platform/' )
					),
				],
			]
		);
	}

	/**
	 * Print admin notice.
	 *
	 * @since 1.0.0
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	public function install_notice() {
		if ( jupiterx_get( 'tgmpa-nonce' ) ) {
			return;
		}

		if ( empty( $this->set_required_plugins() ) ) {
			return;
		}
		?>

		<div id="jupiterx-core-install-notice" class="updated jupiterx-core-install-notice notice is-dismissible">
			<?php wp_nonce_field( 'jupiterx-core-installer-nonce', 'jupiterx-core-installer-notice-nonce' ); ?>
			<?php  ?>
			<div class="jupiterx-core-install-notice-logo">
				<img width="30" src="<?php echo esc_url( JUPITERX_ADMIN_ASSETS_URL . 'images/jupiterx-notice-logo.png' ); ?>" alt="<?php esc_html_e( 'Jupiter X', 'jupiterx' ); ?>" />
			</div>
			<?php  ?>
			<div class="jupiterx-core-install-notice-content">
			<?php
			$notice_title = __( 'Welcome to JupiterX', 'jupiterx' );
			$notice_title = __( 'Welcome to JupiterX', 'jupiterx' );
			$plugins           = $this->set_required_plugins();
			$plugin_names      = [];
			$total_plugins     = count( $plugins );
			$current_plugin    = 0;
			$sellkit_pro_index = -1;

			foreach ( $plugins as $plugin ) {
				$current_plugin++;

				if ( 'Sellkit Pro' !== $plugin['name'] ) {
					$plugin_names[] = $plugin['name'];
					continue;
				}

				--$total_plugins;
				$sellkit_pro_index = $current_plugin - 1;
			}

			// If Sellkit is activated but Sellkit Pro is not activated, display Sellkit Pro.
			if ( -1 !== $sellkit_pro_index && ! in_array( 'Sellkit', $plugin_names, true ) ) {
				$plugin_names[] = $plugins[ $sellkit_pro_index ]['name'];
				++$total_plugins;
			}

			// Add and before the last element if there is more than one plugin in the list.
			if ( $total_plugins > 1 ) {
				$last_plugin_element                      = end( $plugin_names );
				$last_plugin_element_key                  = key( $plugin_names );
				$plugin_names[ $last_plugin_element_key ] = 'and';
				$plugin_names[]                           = $last_plugin_element;
			}

			$plugin_list = implode( ' ', $plugin_names );

			if ( count( $plugin_names ) > 3 ) {
				$plugin_list = implode( ', ', $plugin_names );

				// Remove the comma after "and".
				$plugin_list = str_replace( 'and,', 'and', $plugin_list );
			}

			if ( empty( $plugin_list ) ) {
				return;
			}
			?>
				<h2><?php echo esc_html( $notice_title ); ?></h2>
				<p><?php printf( "To finish the installation process, you'll need to install & activate <strong>%s</strong>.", esc_html( $plugin_list ) ); ?></p>
				<?php $this->install_notice_button(); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Install notice button.
	 *
	 * @since 1.7.0
	 */
	private function install_notice_button() {

		if ( empty( $this->tgmpa ) ) {
			return admin_url( 'themes.php?page=tgmpa-install-plugins' );
		}

		?>
		<button class="jupiterx-core-install-button button button-primary" data-nonce="<?php echo esc_attr( wp_create_nonce( 'jupiterx_get_core_install_inactive_required_plugins' ) ); ?>">
			<span class="button-text">
			<?php
				/* translators: The install/activate action */
				echo esc_html__( 'Install & Activate Required Plugins', 'jupiterx' );
			?>
			</span>
		</button>
		<?php
	}

	/**
	 * Get inactive required plugins.
	 *
	 * @since 3.5.6
	 * @access public
	 *
	 * @return void
	 */
	public function get_required_plugins() {
		if ( ! current_user_can( 'edit_others_posts' ) || ! current_user_can( 'edit_others_pages' ) ) {
			wp_send_json_error( 'You do not have access to this section', 'jupiterx' );
		}

		check_ajax_referer( 'jupiterx_get_core_install_inactive_required_plugins' );

		$plugins = $this->set_required_plugins();

		if ( ! is_array( $plugins ) || count( $plugins ) === 0 ) {
			wp_send_json_error();
		}

		wp_send_json_success( [
			'bulk_actions' => $this->get_plugin_bulk_actions( $plugins ),
			'plugins'      => $plugins,
		] );
	}

	/**
	 * Get Plugin Bulk Actions.
	 *
	 * @since 3.5.6
	 *
	 * @param array $plugins Plugins list.
	 *
	 * @return array
	 */
	public function get_plugin_bulk_actions( $plugins ) {
		return [
			'activate_required_plugins' => [
				'url' => admin_url( 'plugins.php' ),
				'action' => 'activate-selected',
				'action2' => -1,
				'_wpnonce' => wp_create_nonce( 'bulk-plugins' ),
				'checked' => $this->get_required_plugins_slug( $plugins, 'basename' ),
			],
			'install_required_plugins' => [
				'url' => admin_url( 'themes.php?page=tgmpa-install-plugins' ),
				'action' => 'tgmpa-bulk-install',
				'action2' => -1,
				'_wpnonce' => wp_create_nonce( 'bulk-plugins' ),
				'tgmpa-page' => 'tgmpa-install-plugins',
				'plugin' => $this->get_required_plugins_slug( $plugins, 'slug' ),
			],
		];
	}

	/**
	 * Get plugin slugs for bulk action.
	 *
	 * @since 3.5.6
	 *
	 * @param array  $plugins Plugins list.
	 * @param string $field Plugin slug or basename.
	 *
	 * @return array
	 */
	private function get_required_plugins_slug( $plugins, $field ) {
		$slugs = [];

		if ( ! is_array( $plugins ) ) {
			return $slugs;
		}

		foreach ( $plugins as $plugin ) {
			if ( 'true' === $plugin['required'] ) {
				$slugs[] = $plugin[ $field ];
			}
		}

		return $slugs;
	}

}

/**
 * Run the core installer.
 *
 * Show installer notice only when logged in user can manage install plugins and required plugins is not installed or activated.
 *
 * @since 1.0.0
 */
if ( current_user_can( 'install_plugins' ) ) {
	new JupiterX_Theme_Core_Install();
}
