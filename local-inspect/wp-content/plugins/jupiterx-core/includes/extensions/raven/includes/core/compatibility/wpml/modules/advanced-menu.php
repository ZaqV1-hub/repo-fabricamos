<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Advanced_Menu extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'menu';
	}

	public function get_fields() {
		return [
			'text',
			'link' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'text':
				return esc_html__( 'Raven Advanced Menu: Item Text', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Advanced Menu: Item Link', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'text':
				return 'LINE';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
