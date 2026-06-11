<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Media_Carousel extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'slides';
	}

	public function get_fields() {
		return [
			'image_link_to' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'url':
				return esc_html__( 'Raven Media Carousel: Image Link', 'jupiterx-core' );

			case 'video':
				return esc_html__( 'Raven Media Carousel: Video Link', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'url':
				return 'LINK';

			case 'video':
				return 'LINK';

			default:
				return '';
		}
	}
}
