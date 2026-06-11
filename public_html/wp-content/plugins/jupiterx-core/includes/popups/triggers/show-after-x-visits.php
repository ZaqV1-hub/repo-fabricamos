<?php
namespace JupiterX_Core\Popup\Triggers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class After X Visits.
 *
 * @since 3.7.0
 */
class Show_After_X_Visits extends Triggers_Base {
	/**
	 * Get trigger name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'show_after_x_visits';
	}

	/**
	 * Get trigger label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Show After X Visits', 'jupiterx-core' );
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
			'label' => esc_html__( 'Visits', 'jupiterx-core' ),
			'default' => 1,
			'min' => 1,
			'step' => 1,
		];
	}
}
