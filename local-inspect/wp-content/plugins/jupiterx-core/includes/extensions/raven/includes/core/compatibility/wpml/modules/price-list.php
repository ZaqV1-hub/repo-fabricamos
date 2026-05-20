<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Price_List extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'list_items';
	}

	public function get_fields() {
		return [
			'item_price',
			'item_title',
			'item_description',
			'item_link' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'item_price':
				return esc_html__( 'Raven Price List: Price', 'jupiterx-core' );

			case 'item_title':
				return esc_html__( 'Raven Price List: Item Title', 'jupiterx-core' );

			case 'item_description':
				return esc_html__( 'Raven Price List: Item Description', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Price List: Item Link', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'item_price':
				return 'LINE';

			case 'item_title':
				return 'LINE';

			case 'item_description':
				return 'AREA';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
