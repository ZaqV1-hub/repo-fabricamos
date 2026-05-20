<?php
namespace JupiterX_Core\Popup\Triggers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class User Type.
 *
 * @since 3.7.0
 */
class User_Type extends Triggers_Base {
	public function __construct() {
		if ( session_status() !== PHP_SESSION_ACTIVE && ! is_user_logged_in() && ! is_admin() && ! headers_sent() ) {
			session_start( [
				'read_and_close' => true,
			] );
		}
	}

	/**
	 * Get trigger name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'user_type';
	}

	/**
	 * Get trigger label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'User Type', 'jupiterx-core' );
	}

	/**
	 * Get trigger operators.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function operators() {
		return [
			'is',
			'is-not',
		];
	}

	/**
	 * Get trigger options.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_options() {
		$types = [
			'logged_in' => esc_html__( 'Logged in User', 'jupiterx-core' ),
			'first_time' => esc_html__( 'First Time Visitor', 'jupiterx-core' ),
			'repeat' => esc_html__( 'Repeat Visitor', 'jupiterx-core' ),
		];

		$options = [];

		foreach ( $types as $key => $type ) {
			$options[] = [
				'id' => $key,
				'name' => $type,
			];
		}

		return $options;
	}

	/**
	 * Get trigger control.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function add_control() {
		return [
			'type' => 'drop-down',
		];
	}

	/**
	 * Operator validation.
	 *
	 * @since 3.7.0
	 * @param mixed $triggers triggers value.
	 */
	public function is_valid( $triggers ) {
		if ( is_user_logged_in() ) {
			// Save the user type in the user meta for logged-in users.
			$current_user   = wp_get_current_user();
			$user_id        = $current_user->ID;
			$user_type_meta = get_user_meta( $user_id, 'jupiterx_popup_user_type', true );

			if ( ! empty( $user_type_meta ) ) {
				$user_type = $user_type_meta;
			} else {
				$user_type = 'logged_in';
				update_user_meta( $user_id, 'jupiterx_popup_user_type', $user_type );
			}
		} else {
			if ( isset( $_SESSION['jupiterx_popup_user_type'] ) ) {
				$user_type = 'repeat';
			} else {
				$user_type = 'first_time';
			}

			$_SESSION['jupiterx_popup_user_type'] = $user_type;
		}

		if ( 'is' === $triggers['user_type']['operator'] && $user_type === $triggers['user_type']['control'] ) {
			return true;
		}

		if ( 'is-not' === $triggers['user_type']['operator'] && $user_type !== $triggers['user_type']['control'] ) {
			return true;
		}

		return false;
	}
}
