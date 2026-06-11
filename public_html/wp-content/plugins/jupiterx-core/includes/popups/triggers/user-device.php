<?php
namespace JupiterX_Core\Popup\Triggers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class User Device.
 *
 * @since 3.7.0
 */
class User_Device extends Triggers_Base {
	/**
	 * Get trigger name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'user_device';
	}

	/**
	 * Get trigger label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'User Device', 'jupiterx-core' );
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
		$devices = [
			'desktop' => esc_html__( 'Desktop', 'jupiterx-core' ),
			'tablet' => esc_html__( 'Tablet', 'jupiterx-core' ),
			'mobile' => esc_html__( 'Mobile', 'jupiterx-core' ),
		];

		$options = [];

		foreach ( $devices as $key => $device ) {
			$options[] = [
				'id' => $key,
				'name' => $device,
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
			'type' => 'multi-select',
		];
	}
}
