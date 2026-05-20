<?php
/**
 * SVG Sanitizer
 *
 * @package JupiterX_Core\SVG_Sanitizer
 * source: https://github.com/darylldoyle/svg-sanitizer
 */


/**
 * Sanitizes SVG content to remove potentially harmful elements and attributes.
 *
 * @param mixed $svg_content SVG content to sanitize.
 *
 * @return bool|string
 * @since 4.9.1
 */
function jupiterx_svg_sanitizer( $svg_content ) {
	jupiterx_core()->load_files( [ 'svg-sanitizer/vendors/autoload' ] );

	$sanitizer = new JupiterX_Core\enshrined\svgSanitize\Sanitizer();

	return $sanitizer->sanitize( $svg_content );
}
