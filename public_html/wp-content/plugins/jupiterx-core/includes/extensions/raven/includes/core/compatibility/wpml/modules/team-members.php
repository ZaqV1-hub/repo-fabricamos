<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Team_Members extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'team_members';
	}

	public function get_fields() {
		return [
			'name',
			'position',
			'description',
			'facebook',
			'twitter',
			'instagram',
			'linkedin',
			'youtube',
			'pinterest',
			'dribbble',
			'github',
			'email',
			'member_link' => [ 'url' ],
		];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'name':
				return esc_html__( 'Raven Team Members: Item Name', 'jupiterx-core' );

			case 'position':
				return esc_html__( 'Raven Team Members: Item Position', 'jupiterx-core' );

			case 'description':
				return esc_html__( 'Raven Team Members: Item Description', 'jupiterx-core' );

			case 'facebook':
				return esc_html__( 'Raven Team Members: Item Facebook', 'jupiterx-core' );

			case 'twitter':
				return esc_html__( 'Raven Team Members: Item Twitter', 'jupiterx-core' );

			case 'instagram':
				return esc_html__( 'Raven Team Members: Item Instagram', 'jupiterx-core' );

			case 'linkedin':
				return esc_html__( 'Raven Team Members: Item Linkedin', 'jupiterx-core' );

			case 'youtube':
				return esc_html__( 'Raven Team Members: Item YouTube', 'jupiterx-core' );

			case 'pinterest':
				return esc_html__( 'Raven Team Members: Item Pinterest', 'jupiterx-core' );

			case 'dribbble':
				return esc_html__( 'Raven Team Members: Item Dribbble', 'jupiterx-core' );

			case 'github':
				return esc_html__( 'Raven Team Members: Item Github', 'jupiterx-core' );

			case 'email':
				return esc_html__( 'Raven Team Members: Item Email', 'jupiterx-core' );

			case 'url':
				return esc_html__( 'Raven Team Members: Item Link', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'name':
				return 'LINE';

			case 'position':
				return 'LINE';

			case 'description':
				return 'AREA';

			case 'facebook':
				return 'LINE';

			case 'twitter':
				return 'LINE';

			case 'instagram':
				return 'LINE';

			case 'linkedin':
				return 'LINE';

			case 'youtube':
				return 'LINE';

			case 'pinterest':
				return 'LINE';

			case 'dribbble':
				return 'LINE';

			case 'github':
				return 'LINE';

			case 'email':
				return 'LINE';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
