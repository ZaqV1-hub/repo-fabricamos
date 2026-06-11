<?php

namespace JupiterX_Core\Raven\Core\Compatibility\Wpml\Modules;

defined( 'ABSPATH' ) || die();

class Register_Form extends \WPML_Elementor_Module_With_Items {
	public function get_items_field() {
		return 'fields';
	}

	public function get_fields() {
		return [ 'label', 'placeholder', 'field_options', 'acceptance_text', 'confirm_password_label', 'confirm_password_placeholder' ];
	}

	protected function get_title( $field ) {
		switch ( $field ) {
			case 'label':
				return esc_html__( 'Raven Register Form: Item Label', 'jupiterx-core' );

			case 'placeholder':
				return esc_html__( 'Raven Register Form: Item Placeholder', 'jupiterx-core' );

			case 'field_options':
				return esc_html__( 'Raven Register Form: Item Options', 'jupiterx-core' );

			case 'acceptance_text':
				return esc_html__( 'Raven Register Form: Item Acceptance Text', 'jupiterx-core' );

			case 'confirm_password_label':
				return esc_html__( 'Raven Register Form: Item Confirm Password Label', 'jupiterx-core' );

			case 'confirm_password_placeholder':
				return esc_html__( 'Raven Register Form: Item Confirm Password Placeholder', 'jupiterx-core' );

			default:
				return '';
		}
	}

	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'label':
				return 'LINE';

			case 'placeholder':
				return 'LINE';

			case 'field_options':
				return 'AREA';

			case 'acceptance_text':
				return 'AREA';

			case 'confirm_password_label':
				return 'LINE';

			case 'confirm_password_placeholder':
				return 'LINE';

			default:
				return '';
		}
	}
}
