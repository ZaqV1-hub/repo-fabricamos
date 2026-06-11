<?php
namespace JupiterX_Core\Popup\Triggers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class After X Page Views.
 *
 * @since 3.7.0
 */
class Show_After_X_Page_Views extends Triggers_Base {
	/**
	 * Get trigger name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'show_after_x_page_views';
	}

	/**
	 * Get trigger label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Show After X Page Views', 'jupiterx-core' );
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
			'label' => esc_html__( 'Page views', 'jupiterx-core' ),
			'default' => 1,
			'min' => 1,
			'step' => 1,
		];
	}
}
