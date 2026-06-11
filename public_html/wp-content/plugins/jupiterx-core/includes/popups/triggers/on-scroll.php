<?php
namespace JupiterX_Core\Popup\Triggers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Scroll.
 *
 * @since 3.7.0
 */
class On_Scroll extends Triggers_Base {
	/**
	 * Get trigger name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'on_scroll';
	}

	/**
	 * Get trigger label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'On Scroll', 'jupiterx-core' );
	}

	/**
	 * Get trigger control.
	 *
	 * @since 3.7.0
	 * @return array
	 */
	public function add_control() {
		return [
			'type' => 'direction',
			'values' => [
				'down' => esc_html__( 'Down', 'jupiterx-core' ),
				'up' => esc_html__( 'Up', 'jupiterx-core' ),
			],
			'label' => esc_html__( 'Within (%)', 'jupiterx-core' ),
			'default' => 0,
			'min' => 0,
			'step' => 1,
		];
	}
}
