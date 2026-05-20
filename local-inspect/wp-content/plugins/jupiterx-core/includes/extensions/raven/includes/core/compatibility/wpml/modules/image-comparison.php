<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Image_Comparison extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'item_list';
	}

	public function get_fields() {
		return [ 'item_before_label', 'item_after_label' ];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'item_before_label':
				return esc_html__( 'Raven Image Comparison: Before Label', 'jupiterx-core' );

			case 'item_after_label':
				return esc_html__( 'Raven Image Comparison: After Label', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'item_before_label':
				return 'LINE';

			case 'item_after_label':
				return 'LINE';

			default:
				return '';
		}
	}
}
