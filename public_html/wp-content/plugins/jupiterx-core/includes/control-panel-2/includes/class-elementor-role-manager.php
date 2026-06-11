<?php
defined( 'ABSPATH' ) || die();

/**
 * Elementor Role Manager bridge for Jupiter X Control Panel.
 *
 * Reads and writes the same options as Elementor → Settings → Role Manager
 * (`elementor_exclude_user_roles`, `elementor_role-manager`).
 *
 * @package JupiterX_Core\Control_Panel_2
 * @since 4.15.0
 */
class JupiterX_Core_Control_Panel_Elementor_Role_Manager {

	/**
	 * Option keys (Elementor core).
	 */
	const EXCLUDE_OPTION = 'elementor_exclude_user_roles';

	const ADVANCED_OPTION = 'elementor_role-manager';

	/**
	 * Advanced permission slugs managed by this UI (Elementor core + typical overrides).
	 */
	const MANAGED_ADVANCED = [ 'json-upload', 'custom-html', 'design' ];

	/**
	 * Class instance.
	 *
	 * @var JupiterX_Core_Control_Panel_Elementor_Role_Manager|null
	 */
	private static $instance = null;

	/**
	 * Get instance.
	 *
	 * @return JupiterX_Core_Control_Panel_Elementor_Role_Manager
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'wp_ajax_jupiterx_elementor_role_manager', [ $this, 'handle_ajax' ] );
	}

	/**
	 * AJAX entry.
	 *
	 * @return void
	 */
	public function handle_ajax() {
		check_ajax_referer( 'jupiterx_control_panel', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => __( 'Permission denied.', 'jupiterx-core' ) ] );
		}

		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			wp_send_json_error( [ 'message' => __( 'Elementor is not active.', 'jupiterx-core' ) ] );
		}

		$sub = isset( $_POST['sub_action'] ) ? sanitize_text_field( wp_unslash( $_POST['sub_action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( 'get' === $sub ) {
			$this->send_roles_data();
		} elseif ( 'save' === $sub ) {
			$this->save_roles();
		} else {
			wp_send_json_error( [ 'message' => __( 'Invalid action.', 'jupiterx-core' ) ] );
		}
	}

	/**
	 * Return roles and current Elementor role-manager settings.
	 *
	 * @return void
	 */
	private function send_roles_data() {
		$excluded     = get_option( self::EXCLUDE_OPTION, [] );
		$advanced_raw = get_option( self::ADVANCED_OPTION, [] );
		$roles_out    = [];

		if ( ! is_array( $excluded ) ) {
			$excluded = [];
		}

		if ( ! is_array( $advanced_raw ) ) {
			$advanced_raw = [];
		}

		foreach ( get_editable_roles() as $slug => $data ) {
			if ( 'administrator' === $slug ) {
				continue;
			}

			$adv = isset( $advanced_raw[ $slug ] ) && is_array( $advanced_raw[ $slug ] )
				? $advanced_raw[ $slug ]
				: [];

			$roles_out[] = [
				'slug'             => $slug,
				'name'             => translate_user_role( $data['name'] ),
				'noEditorAccess'   => in_array( $slug, $excluded, true ),
				'jsonUpload'       => in_array( 'json-upload', $adv, true ),
				'customHtml'       => in_array( 'custom-html', $adv, true ),
				'contentOnly'      => in_array( 'design', $adv, true ),
			];
		}

		wp_send_json_success(
			[
				'roles' => $roles_out,
			]
		);
	}

	/**
	 * Persist settings using the same shape Elementor expects.
	 *
	 * @return void
	 */
	private function save_roles() {
		$raw = isset( $_POST['roles'] ) ? wp_unslash( $_POST['roles'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( '' === $raw ) {
			wp_send_json_error( [ 'message' => __( 'No data.', 'jupiterx-core' ) ] );
		}

		$payload = json_decode( $raw, true );

		if ( ! is_array( $payload ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid data.', 'jupiterx-core' ) ] );
		}

		$editable = get_editable_roles();
		unset( $editable['administrator'] );

		$exclude  = [];
		$advanced = get_option( self::ADVANCED_OPTION, [] );

		if ( ! is_array( $advanced ) ) {
			$advanced = [];
		}

		foreach ( $payload as $row ) {
			if ( empty( $row['slug'] ) || ! isset( $editable[ $row['slug'] ] ) ) {
				continue;
			}

			$slug = sanitize_key( $row['slug'] );

			if ( ! empty( $row['noEditorAccess'] ) ) {
				$exclude[] = $slug;
				// Keep third-party / Pro restriction keys; strip only the toggles we manage.
				$existing = isset( $advanced[ $slug ] ) && is_array( $advanced[ $slug ] ) ? $advanced[ $slug ] : [];
				$advanced[ $slug ] = array_values(
					array_filter(
						$existing,
						function ( $v ) {
							return ! in_array( $v, self::MANAGED_ADVANCED, true );
						}
					)
				);
				continue;
			}

			$perms = [];
			if ( ! empty( $row['jsonUpload'] ) ) {
				$perms[] = 'json-upload';
			}
			if ( ! empty( $row['customHtml'] ) ) {
				$perms[] = 'custom-html';
			}
			if ( ! empty( $row['contentOnly'] ) ) {
				$perms[] = 'design';
			}

			$existing = isset( $advanced[ $slug ] ) && is_array( $advanced[ $slug ] ) ? $advanced[ $slug ] : [];
			$extra    = array_values(
				array_filter(
					$existing,
					function ( $v ) {
						return ! in_array( $v, self::MANAGED_ADVANCED, true );
					}
				)
			);

			$advanced[ $slug ] = array_values( array_unique( array_merge( $perms, $extra ) ) );
		}

		$exclude = array_values( array_unique( array_filter( $exclude ) ) );

		update_option( self::EXCLUDE_OPTION, $exclude );
		update_option( self::ADVANCED_OPTION, $advanced );

		wp_send_json_success( [ 'message' => __( 'Settings saved.', 'jupiterx-core' ) ] );
	}
}
