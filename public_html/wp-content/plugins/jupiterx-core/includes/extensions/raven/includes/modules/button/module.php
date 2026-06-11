<?php

namespace JupiterX_Core\Raven\Modules\Button;

defined( 'ABSPATH' ) || die();

use JupiterX_Core\Raven\Base\Module_base;
use JupiterX_Core\Raven\Utils;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

class Module extends Module_Base {

	public function get_widgets() {
		return [ 'button' ];
	}

	/**
	 * Set global color as default value.
	 *
	 * @since 4.0.0
	 * @return array
	 */
	public static function get_default_value() {
		if ( ! Utils::check_fresh_install() ) {
			return [];
		}

		return [
			'background' => [
				'default' => 'classic',
			],
			'color' => [
				'global' => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
			],
		];
	}
}
