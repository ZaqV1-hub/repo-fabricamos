<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Text_Marquee extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'content_list';
	}

	public function get_fields() {
		return [ 'text' ];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'text':
				return esc_html__( 'Raven Text Marquee: Text', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'text':
				return 'LINE';

			default:
				return '';
		}
	}
}
