<?php
namespace JupiterX_Core\Popup\Triggers;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class User Browser.
 *
 * @since 3.7.0
 */
class User_Browser extends Triggers_Base {
	/**
	 * Get trigger name.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_name() {
		return 'user_browser';
	}

	/**
	 * Get trigger label.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'User Browser', 'jupiterx-core' );
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
		$browsers = [
			'ie' => esc_html__( 'Internet Explorer', 'jupiterx-core' ),
			'chrome' => esc_html__( 'Chrome', 'jupiterx-core' ),
			'edge' => esc_html__( 'Edge', 'jupiterx-core' ),
			'firefox' => esc_html__( 'Firefox', 'jupiterx-core' ),
			'safari' => esc_html__( 'Safari', 'jupiterx-core' ),
		];

		$options = [];

		foreach ( $browsers as $key => $browser ) {
			$options[] = [
				'id' => $key,
				'name' => $browser,
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
