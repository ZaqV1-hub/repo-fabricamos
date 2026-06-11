<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Hotspot extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'hotspot';
	}

	public function get_fields() {
		return [
			'hotspot_label',
			'hotspot_tooltip_content',
			'hotspot_link' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'hotspot_label':
				return esc_html__( 'Raven Hotspot: Label', 'jupiterx-core' );

			case 'hotspot_tooltip_content':
				return esc_html__( 'Raven Hotspot: Tooltip Content', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Hotspot: Link', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'hotspot_label':
				return 'LINE';

			case 'hotspot_tooltip_content':
				return 'VISUAL';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
