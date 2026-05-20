<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Business_Hours extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'business_hours_list';
	}

	public function get_fields() {
		return [ 'day', 'time' ];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'day':
				return esc_html__( 'Raven Business Hours: Day', 'jupiterx-core' );

			case 'time':
				return esc_html__( 'Raven Business Hours: Time', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'day':
				return 'LINE';

			case 'time':
				return 'LINE';

			default:
				return '';
		}
	}
}
