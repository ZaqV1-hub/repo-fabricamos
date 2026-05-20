<?php
namespace JupiterX_Core\Raven\Modules\Heading;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Module_base;
use JupiterX_Core\Raven\Utils;

class Module extends Module_Base {

	public function get_widgets() {
		return [ 'heading' ];
	}

	/**
	 * Set global color as default value.
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public static function get_default_value() {
		if ( ! Utils::check_fresh_install() ) {
			return '';
		}

		return 'solid';
	}
}
