<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class My_Account extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'tabs';
	}

	public function get_fields() {
		return [ 'tab_name' ];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'tab_name':
				return esc_html__( 'Raven My Account: Tab Name', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'tab_name':
				return 'LINE';

			default:
				return '';
		}
	}
}
