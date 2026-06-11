<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Advanced_Accordion extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'items';
	}

	public function get_fields() {
		return [ 'item_label', 'item_content_editor_content' ];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'item_label':
				return esc_html__( 'Raven Advanced Accordion: Item Label', 'jupiterx-core' );

			case 'item_content_editor_content':
				return esc_html__( 'Raven Advanced Accordion: Item Content', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'item_label':
				return 'LINE';

			case 'item_content_editor_content':
				return 'VISUAL';

			default:
				return '';
		}
	}
}
