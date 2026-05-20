<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Testimonial_Marquee extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'content_list';
	}

	public function get_fields() {
		return [ 'label', 'heading', 'content', 'name' ];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'label':
				return esc_html__( 'Raven Testimonial Marquee: Label', 'jupiterx-core' );

			case 'heading':
				return esc_html__( 'Raven Testimonial Marquee: Heading', 'jupiterx-core' );

			case 'content':
				return esc_html__( 'Raven Testimonial Marquee: Content', 'jupiterx-core' );

			case 'name':
				return esc_html__( 'Raven Testimonial Marquee: Name', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'label':
				return 'LINE';

			case 'heading':
				return 'LINE';

			case 'content':
				return 'AREA';

			case 'name':
				return 'LINE';

			default:
				return '';
		}
	}
}
