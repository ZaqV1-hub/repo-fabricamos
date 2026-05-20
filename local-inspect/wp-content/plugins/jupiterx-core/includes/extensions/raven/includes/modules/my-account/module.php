<?php

namespace JupiterX_Core\Raven\Modules\My_Account;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Module_base;

/**
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Module extends Module_Base {

	public function get_widgets() {
		return [ 'my-account' ];
	}

	public static function is_active() {
		return function_exists( 'WC' );
	}

	public function __construct() {
		parent::__construct();

		// Define an endpoint for all custom My Account tabs.
		define( 'JX_MY_ACCOUNT_CUSTOM_ENDPOINT', 'tab' );

		// On Editor: Register WooCommerce frontend hooks before the Editor init.
		add_action( 'init', [ $this, 'include_wc_frontend' ], 0 );

		// Register the defined endpoint("JX_MY_ACCOUNT_CUSTOM_ENDPOINT") to WC My Account endpoints.
		add_action( 'init', [ $this, 'add_custom_templates_endpoint' ], 5 );

		// Ajax action that sends WC My Account tabs to frontend (used for syncing tabs with 3rd party plugins activation/deactivation).
		add_action( 'wp_ajax_raven_my_account_nav_items', [ $this, 'register_ajax_action_nav_items' ] );

	}

	public function include_wc_frontend() {
		if ( ! empty( $_REQUEST['action'] ) && 'elementor' === $_REQUEST['action'] && is_admin() ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
			wc()->frontend_includes();
		}
	}

	public function add_custom_templates_endpoint() {

		add_action( 'pre_get_posts', function() {
			if ( is_admin() ) {
				return;
			}

			$is_account_page = is_account_page();
			$queries         = '';
			$current_url     = '';

			if ( ! $is_account_page ) {
				return;
			}

			$queries = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ); //phpcs:ignore

			if ( empty( $queries ) ) {
				$queries = '';
			}

			parse_str( $queries, $queries );

			// URL has no parameters. then we get tab from URL.
			if ( empty( $queries ) && $is_account_page ) {
				$current_url = home_url( add_query_arg( null, null ) );
			}

			if (
				! empty( $current_url ) &&
				strpos( $current_url, JX_MY_ACCOUNT_CUSTOM_ENDPOINT . '/' ) !== false
			) {
				$tab = explode( '/' . JX_MY_ACCOUNT_CUSTOM_ENDPOINT, $current_url )[1];
				$tab = str_replace( '/', '', $tab );

				$this->enable_custom_tab_endpoint( $tab );

				return;
			}

			// URL has parameters.
			foreach ( $queries as $query => $value ) {
				if ( strpos( $query, JX_MY_ACCOUNT_CUSTOM_ENDPOINT . '/' ) !== false ) {
					$tab = explode( '/', $query )[1];
					$this->enable_custom_tab_endpoint( $tab );
				}
			}
		} );

		add_filter( 'woocommerce_get_query_vars', function( $queries ) {
			$return_default = false;
			$arguments      = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_QUERY ); //phpcs:ignore

			if ( empty( $arguments ) ) {
				$arguments = '';
			}

			parse_str( $arguments, $arguments );

			if ( ! empty( $arguments ) && is_array( $arguments ) ) {
				foreach ( $arguments as $query => $value ) {
					if ( JX_MY_ACCOUNT_CUSTOM_ENDPOINT === $query ) {
						$return_default = true;
					}
				}
			}

			if ( $return_default ) {
				return $queries;
			}

			$queries[ JX_MY_ACCOUNT_CUSTOM_ENDPOINT ] = JX_MY_ACCOUNT_CUSTOM_ENDPOINT;
			return $queries;
		} );

		// Force update rewrite rules only once.
		$current_rewrite_rule = get_option( 'jx_my_account_custom_tab' );

		if ( ! $current_rewrite_rule || JX_MY_ACCOUNT_CUSTOM_ENDPOINT !== $current_rewrite_rule ) {
			flush_rewrite_rules( true );
			update_option( 'jx_my_account_custom_tab', JX_MY_ACCOUNT_CUSTOM_ENDPOINT );
		}
	}

	/**
	 * Lets WooCommerce knows current URL is an endpoint.
	 *
	 * @param string $tab The tab slug.
	 * @since 4.0.0
	 */
	private function enable_custom_tab_endpoint( $tab ) {
		set_query_var( 'jupiterx-my-account-tab-integrate-by-permalink', true );
		set_query_var( 'jupiterx-my-account-tab', $tab );

		global $wp;
		$wp->query_vars[ JX_MY_ACCOUNT_CUSTOM_ENDPOINT ] = '';
	}

	public function register_ajax_action_nav_items() {
		$result = wc_get_account_menu_items();

		wp_send_json_success( $result );
	}
}
