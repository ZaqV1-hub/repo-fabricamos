<?php
namespace JupiterX_Core\Popup\Triggers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class After Inactivity.
 *
 * @since 3.7.0
 */
class After_Inactivity extends Triggers_Base {
	/**
	 * Get trigger name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'after_inactivity';
	}

	/**
	 * Get trigger label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'After Inactivity', 'jupiterx-core' );
	}

	/**
	 * Get trigger control.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function add_control() {
		return [
			'type' => 'number',
			'label' => esc_html__( 'Within (sec)', 'jupiterx-core' ),
			'default' => 0,
			'min' => 0,
			'step' => 1,
		];
	}
}
