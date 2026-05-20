<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Reviews extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'slides';
	}

	public function get_fields() {
		return [
			'content',
			'name',
			'title',
			'link' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'content':
				return esc_html__( 'Raven Reviews: Comment Contents', 'jupiterx-core' );

			case 'name':
				return esc_html__( 'Raven Reviews: Commenter Name', 'jupiterx-core' );

			case 'title':
				return esc_html__( 'Raven Reviews: Comment Title', 'jupiterx-core' );

			case 'image':
				return esc_html__( 'Raven Reviews: Comment Image', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Reviews: Comment Link', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'content':
				return 'LINE';

			case 'name':
				return 'LINE';

			case 'title':
				return 'LINE';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
