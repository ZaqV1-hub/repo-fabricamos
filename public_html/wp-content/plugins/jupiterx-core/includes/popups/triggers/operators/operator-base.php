<?php
/**
 * Abstract class for popup operators.
 *
 * @since 3.7.0
 */
namespace JupiterX_Core\Popup\Triggers;

defined( 'ABSPATH' ) || die();

abstract class Operator_Base {

	/**
	 * Get title.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	abstract public function get_title();

	/**
	 * Get name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	abstract public function get_name();

	/**
	 * Condition title.
	 *
	 * @since 3.7.0
	 * @param mixed $value      The value of current value.
	 * @param mixed $condition_value The value of condition input.
	 * @todo this method will be migrated to js codes.
	 */
	abstract public function is_valid( $value, $condition_value );
}
