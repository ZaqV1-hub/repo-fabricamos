<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Content_Marquee extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'content_list';
	}

	public function get_fields() {
		return [
			'link' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'url':
				return esc_html__( 'Raven Content Marquee: Link', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
