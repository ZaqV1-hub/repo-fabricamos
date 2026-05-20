<?php
/**
 * Slider start template
 */

$settings = $this->get_settings_for_display();
$class_array[] = 'jet-slider__items';
$class_array[] = 'sp-slides';
$classes = implode( ' ', $class_array );
$slider_id = !empty( $settings['slider_id']) ? 'id=' . $settings['slider_id'] . '' : '';

?>

<div <?php echo esc_attr( $slider_id ); ?> class="slider-pro">
	<?php
	echo sprintf(
		'<div class="jet-slider__arrow-icon-%s hidden-html">%s</div>',
		esc_attr( $this->get_id() ),
		$this->_render_icon( 'slider_navigation_icon_arrow', '%s', '', false ) // phpcs:ignore
	);

	echo sprintf(
		'<div class="jet-slider__fullscreen-icon-%s hidden-html">%s</div>',
		esc_attr( $this->get_id() ),
		$this->_render_icon( 'slider_fullscreen_icon', '%s', '', false ) // phpcs:ignore
	);
	?>
	<div class="<?php echo esc_attr( $classes ); ?>">


