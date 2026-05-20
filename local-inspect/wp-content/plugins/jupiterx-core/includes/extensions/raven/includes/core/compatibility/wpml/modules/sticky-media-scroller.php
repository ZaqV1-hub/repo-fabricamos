<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Sticky_Media_Scroller extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'sections';
	}

	public function get_fields() {
		return [
			'content_heading',
			'content',
			'content_button_text',
			'content_button_link' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'content_heading':
				return esc_html__( 'Raven Sticky Media Scroller: Item Heading', 'jupiterx-core' );

			case 'content':
				return esc_html__( 'Raven Sticky Media Scroller: Item Content', 'jupiterx-core' );

			case 'content_button_text':
				return esc_html__( 'Raven Sticky Media Scroller: Item Button Text', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Sticky Media Scroller: Item Link', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'content_heading':
				return 'LINE';

			case 'content':
				return 'VISUAL';

			case 'content_button_text':
				return 'LINE';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
