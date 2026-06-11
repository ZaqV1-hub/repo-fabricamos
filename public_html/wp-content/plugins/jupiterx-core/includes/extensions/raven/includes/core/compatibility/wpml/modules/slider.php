<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Slider extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'slides';
	}

	public function get_fields() {
		return [
			'heading',
			'description',
			'button_text',
			'link' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'heading':
				return esc_html__( 'Raven Slider: Item Title', 'jupiterx-core' );

			case 'description':
				return esc_html__( 'Raven Slider: Item Description', 'jupiterx-core' );

			case 'button_text':
				return esc_html__( 'Raven Slider: Item Button Text', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Slider: Item Link', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'heading':
				return 'LINE';

			case 'description':
				return 'AREA';

			case 'button_text':
				return 'LINE';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
