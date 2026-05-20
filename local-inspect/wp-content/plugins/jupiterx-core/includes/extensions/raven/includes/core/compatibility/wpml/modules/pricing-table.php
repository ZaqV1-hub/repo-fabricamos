<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Pricing_Table extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'features_list';
	}

	public function get_fields() {
		return [ 'item_text' ];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'item_text':
				return esc_html__( 'Raven Pricing Table: Item Text', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'item_text':
				return 'LINE';

			default:
				return '';
		}
	}

}
