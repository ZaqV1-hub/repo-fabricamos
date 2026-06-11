<?php
namespace JupiterX_Core\Popup\Triggers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class User Role.
 *
 * @since 3.7.0
 */
class User_Role extends Triggers_Base {
	/**
	 * Get trigger name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'user_role';
	}

	/**
	 * Get trigger label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'WP User Role', 'jupiterx-core' );
	}

	/**
	 * Get trigger operators.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function operators() {
		return [
			'is-any-of',
			'is-none-of',
		];
	}

	/**
	 * Get trigger options.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function get_options() {
		global $wp_roles;

		$neat_roles = [];

		$all_roles = $wp_roles->roles;

		foreach ( $all_roles as $role_key => $role ) {
			$neat_roles[] = [
				'id' => $role_key,
				'name' => $role['name'],
			];
		}

		return $neat_roles;
	}

	/**
	 * Get trigger control.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function add_control() {
		return [
			'type' => 'multi-select',
		];
	}

	/**
	 * Operator validation.
	 *
	 * @since 3.7.0
	 * @param mixed $triggers triggers value.
	 */
	public function is_valid( $triggers ) {
		$current_role = '';

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$current_role = $current_user->roles[0];
		}

		$role_match = false;

		foreach ( $triggers['user_role']['control'] as $role ) {
			if ( $current_role === $role['value'] ) {
				$role_match = true;
				break;
			}
		}

		if ( 'is-any-of' === $triggers['user_role']['operator'] && $role_match ) {
			return true;
		}

		if ( 'is-none-of' === $triggers['user_role']['operator'] && ! $role_match ) {
			return true;
		}

		return false;
	}
}
