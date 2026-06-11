<?php
/**
 * Add Action Base.
 *
 * @package JupiterX_Core\Raven
 * @since 1.0.0
 */

namespace JupiterX_Core\Raven\Modules\Forms\Actions;

defined( 'ABSPATH' ) || die();

use Elementor\Settings;

/**
 * Action Base.
 *
 * An abstract class to register new form action.
 *
 * @since 1.0.0
 * @abstract
 */
abstract class Action_Base {

	/**
	 * Action base constructor.
	 *
	 * Initializing the action base class by hooking in widgets controls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/element/raven-form/section_settings/after_section_end', [ $this, 'update_controls' ] );
		add_action( 'elementor/element/raven-register/section_settings/after_section_end', [ $this, 'update_controls' ] );

		if ( is_admin() ) {
			add_action( 'elementor/admin/after_create_settings/' . Settings::PAGE_ID, [ $this, 'register_admin_fields' ], 20 );
		}
	}

	/**
	 * Get name.
	 *
	 * Get name of this action.
	 *
	 * @since 1.19.0
	 * @access public
	 * @abstract
	 */
	abstract public function get_name();

	/**
	 * Get title.
	 *
	 * Get title of this action.
	 *
	 * @since 1.19.0
	 * @access public
	 * @abstract
	 */
	abstract public function get_title();

	/**
	 * Is private.
	 *
	 * Determine if this action is private.
	 *
	 * @since 2.0.0
	 * @access public
	 * @abstract
	 */
	public function is_private() {
		return false;
	}

	/**
	 * Update controls.
	 *
	 * Add, remove and sort the controls in the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @abstract
	 *
	 * @param object $widget Ajax handler instance.
	 *
	 * @return void
	 */
	abstract public function update_controls( $widget );

	/**
	 * Run action.
	 *
	 * Run the main functionality of the action.
	 *
	 * @since 1.0.0
	 * @access public
	 * @abstract
	 *
	 * @param object $ajax_handler Ajax handler instance.
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public static function run( $ajax_handler ) {}

	/**
	 * Register admin fields.
	 *
	 * Register required admin settings for the field.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param object $settings Settings.
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function register_admin_fields( $settings ) {}

	/**
	 * Replace shortecodes settings.
	 *
	 * Replace shortcodes with the correct content.
	 *
	 * @since 3.3.0
	 * @access public
	 *
	 * @param string $setting Shortcode string.
	 * @param string $record_fields Fields that user filled in the form.
	 * @param array $form_settings_fields Form elementor settings.
	 * @param string $content_type Content type.
	 * @param string $line_break Line break.
	 * @param string $content_field Content field determines that if the shortcode is for the email body or another fields.
	 *
	 * @return string
	 */
	public static function replace_setting_shortcodes( $setting, $record_fields, $form_settings_fields, $content_type, $line_break, $content_field = true ) {
		// Shortcode can be `[field id="fds21fd"]`, `[field without-label id="fds21fd"]` or `[field title="Email" id="fds21fd"]`, multiple shortcodes are allowed
		return preg_replace_callback( '/(\[field[^]]*id="(\w+)"[^]]*\])/', function( $matches ) use ( $record_fields, $form_settings_fields, $content_type, $line_break, $content_field ) {
			$shortcode       = $matches[1];
			$field_custom_id = $matches[2];

			// Check if the shortcode has the without-label attribute
			$without_label = strpos( $shortcode, 'without-label' ) !== false;

			// Find the field by custom ID
			$field = self::find_field_by_custom_id( $form_settings_fields, $field_custom_id );
			if ( ! $field ) {
				return '';
			}

			$field_value = $record_fields[ $field['_id'] ] ?? '';

			// For non-content fields, return just the value
			if ( ! $content_field ) {
				return $field_value;
			}

			// For content fields, build formatted output
			return self::build_field_output( $field, $field_value, $content_type, $line_break, $without_label, $record_fields );
		}, $setting );
	}

	/**
	 * Find field by custom ID.
	 *
	 * @access private
	 * @static
	 *
	 * @param array $fields Form fields.
	 * @param string $custom_id Field custom ID.
	 * @return array|null
	 */
	private static function find_field_by_custom_id( $fields, $custom_id ) {
		foreach ( $fields as $field ) {
			if ( isset( $field['field_custom_id'] ) && $field['field_custom_id'] === $custom_id ) {
				return $field;
			}
		}
		return null;
	}

	/**
	 * Build field output for email content.
	 *
	 * @access private
	 * @static
	 *
	 * @param array $field Field configuration.
	 * @param string $value Field value.
	 * @param string $content_type Content type.
	 * @param string $line_break Line break.
	 * @param bool $without_label Whether to exclude label.
	 * @param array $all_values All form values.
	 * @return string
	 */
	private static function build_field_output( $field, $value, $content_type, $line_break, $without_label, $all_values ) {
		// Skip HTML fields
		if ( 'html' === $field['type'] ) {
			return '';
		}

		$content = self::process_field_value( $field, $value, $content_type, $all_values );
		$label   = self::get_field_display_label( $field );

		// Skip fields that have no content and no label (completely empty)
		if ( empty( trim( $content ) ) && empty( trim( $label ) ) ) {
			return '';
		}

		// Skip empty fields when without-label is true
		if ( $without_label && empty( trim( $content ) ) ) {
			return '';
		}

		// Skip fields with labels but no content (prevents "Label:" with nothing after)
		if ( ! $without_label && empty( trim( $content ) ) ) {
			return '';
		}

		if ( $without_label ) {
			return $content . $line_break;
		}

		// Only add colon if we have a label
		if ( ! empty( trim( $label ) ) ) {
			return $label . ': ' . $content . $line_break;
		} else {
			return $content . $line_break;
		}
	}

	/**
	 * Process field value based on field type.
	 *
	 * @access private
	 * @static
	 *
	 * @param array $field Field configuration.
	 * @param string $value Field value.
	 * @param string $content_type Content type.
	 * @param array $all_values All form values.
	 * @return string
	 */
	private static function process_field_value( $field, $value, $content_type, $all_values ) {
		$content = $value;

		// Handle file upload fields
		if ( 'upload' === $field['type'] ) {
			if ( ! empty( $content ) ) {
				// For file uploads, the content is usually a URL or file path
				if ( 'html' === $content_type ) {
					// Create a clickable link for HTML emails
					$content = '<a href="' . esc_url( $content ) . '">' . esc_html( basename( $content ) ) . '</a>';
				} else {
					// For plain text, just show the URL
					$content = $content;
				}
			} else {
				$content = __( 'No file uploaded', 'jupiterx-core' );
			}
		}

		// Handle textarea formatting for HTML content
		if ( 'textarea' === $field['type'] && 'html' === $content_type ) {
			$content = nl2br( $content );
		}

		// Handle acceptance field
		if ( 'acceptance' === $field['type'] ) {
			$newsletter_key = isset( $all_values['register_acceptance'] ) ? 'register_acceptance' : $field['_id'];
			$newsletter     = $all_values[ $newsletter_key ] ?? '';
			$content        = 'on' === $newsletter ? __( 'Yes', 'jupiterx-core' ) : __( 'No', 'jupiterx-core' );
		}

		return $content;
	}

	/**
	 * Get field display label.
	 *
	 * @access private
	 * @static
	 *
	 * @param array $field Field configuration.
	 * @return string
	 */
	private static function get_field_display_label( $field ) {
		$title = $field['label'] ?? '';

		// Special handling for newsletter field
		if ( 'newsletter' === ( $field['map_to'] ?? '' ) && 'acceptance' === $field['type'] ) {
			$title = empty( $title ) ? __( 'Newsletter', 'jupiterx-core' ) : $title;
		}

		return $title;
	}
}
