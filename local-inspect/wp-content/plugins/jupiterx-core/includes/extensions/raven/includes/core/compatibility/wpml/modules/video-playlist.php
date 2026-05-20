<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Video_Playlist extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'tabs';
	}

	public function get_fields() {
		return [
			'youtube_url',
			'vimeo_url',
			'external_url' => [ 'url' ],
			'title',
			'duration',
			'inner_tab_content_1',
			'inner_tab_content_2',
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'youtube_url':
				return esc_html__( 'Raven Video Playlist: YouTube Link', 'jupiterx-core' );

			case 'vimeo_url':
				return esc_html__( 'Raven Video Playlist: Vimeo Link', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Video Playlist: External Link', 'jupiterx-core' );

			case 'title':
				return esc_html__( 'Raven Video Playlist: Item Title', 'jupiterx-core' );

			case 'duration':
				return esc_html__( 'Raven Video Playlist: Item Duration', 'jupiterx-core' );

			case 'inner_tab_content_1':
				return esc_html__( 'Raven Video Playlist: Item Tab 1 Content', 'jupiterx-core' );

			case 'inner_tab_content_2':
				return esc_html__( 'Raven Video Playlist: Item Tab 2 Content', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'youtube_url':
				return 'LINE';

			case 'vimeo_url':
				return 'LINE';

			case 'url':
				return 'LINK';

			case 'title':
				return 'LINE';

			case 'duration':
				return 'LINE';

			case 'inner_tab_content_1':
				return 'VISUAL';

			case 'inner_tab_content_2':
				return 'VISUAL';

			default:
				return '';
		}
	}
}
