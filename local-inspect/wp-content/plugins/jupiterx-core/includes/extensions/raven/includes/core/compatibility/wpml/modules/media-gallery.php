<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Media_Gallery extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'fields';
	}

	public function get_fields() {
		return [
			'category_label',
			'url_link_to' => [ 'url' ],
			'youtube_url' => [ 'url' ],
			'vimeo_url' => [ 'url' ],
			'video_external_url' => [ 'url' ],
			'spotify_url' => [ 'url' ],
			'soundcloud_url' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'category_label':
				return esc_html__( 'Raven Media Gallery: Category Label', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Media Gallery: Item URL', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'category_label':
				return 'LINE';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
