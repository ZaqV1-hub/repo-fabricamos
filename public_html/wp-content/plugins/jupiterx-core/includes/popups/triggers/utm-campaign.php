<?php
namespace JupiterX_Core\Popup\Triggers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Utm Campaign.
 *
 * @since 3.7.0
 */
class Utm_Campaign extends Triggers_Base {
	/**
	 * Get trigger name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'utm_campaign';
	}

	/**
	 * Get trigger label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'UTM Campaign', 'jupiterx-core' );
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
			'contains',
			'does-not-contains',
			'starts-with',
			'ends-with',
		];
	}

	/**
	 * Get trigger control.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function add_control() {
		return [
			'type' => 'text',
			'label' => '',
			'placeholder' => '',
		];
	}
}
