<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Image_Accordion extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'list';
	}

	public function get_fields() {
		return [
			'list_title',
			'list_description',
			'list_button_text',
			'list_link' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'list_title':
				return esc_html__( 'Raven Image Accordion: Item Title', 'jupiterx-core' );

			case 'list_description':
				return esc_html__( 'Raven Image Accordion: Item Description', 'jupiterx-core' );

			case 'list_button_text':
				return esc_html__( 'Raven Image Accordion: Item Button Text', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Image Accordion: Item Link', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'list_title':
				return 'LINE';

			case 'list_description':
				return 'AREA';

			case 'list_button_text':
				return 'LINE';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
