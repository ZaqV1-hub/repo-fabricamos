<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Testimonial_Carousel extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'slides';
	}

	public function get_fields() {
		return [ 'content', 'name', 'title' ];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'content':
				return esc_html__( 'Raven Testimonial Carousel: Content', 'jupiterx-core' );

			case 'name':
				return esc_html__( 'Raven Testimonial Carousel: Name', 'jupiterx-core' );

			case 'title':
				return esc_html__( 'Raven Testimonial Carousel: Title', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'content':
				return 'AREA';

			case 'name':
				return 'LINE';

			case 'title':
				return 'LINE';

			default:
				return '';
		}
	}
}
