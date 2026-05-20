<?php
namespace JupiterX_Core\Popup\Triggers\Operators;

use JupiterX_Core\Popup\Triggers\Operator_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Starts_With for triggers operator.
 *
 * @since 3.7.0
 */
class Starts_With extends Operator_Base {
	/**
	 * Operator name.
	 *
	 * @since 3.7.0
	 */
	public function get_name() {
		return 'starts-with';
	}

	/**
	 * Operator title.
	 *
	 * @since 3.7.0
	 */
	public function get_title() {
		return esc_html__( 'Starts With', 'jupiterx-core' );
	}

	/**
	 * Operator validation.
	 *
	 * @since 3.7.0
	 * @param mixed $value            mixed The value of current value.
	 * @param mixed $condition_value  The value of condition input.
	 */
	public function is_valid( $value, $condition_value ) {
		if ( str_starts_with( $value, $condition_value ) ) {
			return true;
		}

		return false;
	}
}
