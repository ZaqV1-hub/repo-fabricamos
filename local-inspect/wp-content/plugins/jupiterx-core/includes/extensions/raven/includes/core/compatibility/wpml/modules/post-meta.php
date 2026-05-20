<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Post_Meta extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'icon_list';
	}

	public function get_fields() {
		return [
			'text_prefix',
			'string_no_comments',
			'string_one_comment',
			'string_comments',
			'custom_text',
			'custom_url' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'text_prefix':
				return esc_html__( 'Raven Post Meta: Before', 'jupiterx-core' );

			case 'string_no_comments':
				return esc_html__( 'Raven Post Meta: No Comments', 'jupiterx-core' );

			case 'string_one_comment':
				return esc_html__( 'Raven Post Meta: One Comment', 'jupiterx-core' );

			case 'string_comments':
				return esc_html__( 'Raven Post Meta: Comments', 'jupiterx-core' );

			case 'custom_text':
				return esc_html__( 'Raven Post Meta: Custom Text', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Post Meta: Custom URL', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'text_prefix':
				return 'LINE';

			case 'string_no_comments':
				return 'LINE';

			case 'string_one_comment':
				return 'LINE';

			case 'string_comments':
				return 'LINE';

			case 'custom_text':
				return 'LINE';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
