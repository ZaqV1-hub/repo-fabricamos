<?php
/**
 * Add form html field.
 *
 * @package JupiterX_Core\Raven
 * @since 4.6.0
 */

namespace JupiterX_Core\Raven\Modules\Forms\Fields;

defined( 'ABSPATH' ) || die();

use Elementor\Plugin as Elementor;
use Elementor\Utils as ElementorUtils;

/**
 * HTML Field.
 *
 * Initializing the html field by extending field base abstract class.
 *
 * @since 4.6.0
 */
class Html extends Field_Base {

	/**
	 * Get field value.
	 *
	 * Retrieve the field value.
	 *
	 * @since 4.6.0
	 * @access public
	 *
	 * @return integer Field value.
	 */
	public function get_value() {
		return empty( $this->field['field_html'] ) ? '' : $this->field['field_html'];
	}

	/**
	 * Render content.
	 *
	 * Render the field content.
	 *
	 * @since 4.6.0
	 * @access public
	 */
	public function render_content() {
		ElementorUtils::print_unescaped_internal_string( $this->get_value() );
	}
}
