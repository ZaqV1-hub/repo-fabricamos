<?php
/**
 * Image Comparison item template
 */

$settings  = $this->get_settings();

if ( ! empty( $settings['handle_custom_arrows'] ) && 'true' === $settings['handle_custom_arrows'] ) {
	$prevArrow = esc_attr( $this->_render_icon( 'handle_custom_prev_arrow', '%s', '', false, true ) );
	$nextArrow = esc_attr( $this->_render_icon( 'handle_custom_next_arrow', '%s', '', false, true ) );
} else {
	$prevArrow = esc_attr( jet_elements_tools()->get_carousel_arrow( $settings['handle_prev_arrow'], 'prev', '', true ) );
	$nextArrow = esc_attr( jet_elements_tools()->get_carousel_arrow( $settings['handle_next_arrow'], 'next', '', true ) );
}

$starting_position        = $settings['starting_position'];
$starting_position_string = $starting_position['size'] . $starting_position['unit'];
$item_before_label        = $this->_loop_item( array( 'item_before_label' ), 'data-label="%s"' );
$item_before_image        = $this->_loop_item( array( 'item_before_image', 'url' ), '%s' );
$item_after_label         = $this->_loop_item( array( 'item_after_label' ), 'data-label="%s"' );
$item_after_image         = $this->_loop_item( array( 'item_after_image', 'url' ), '%s' );

$img_before_alt = Elementor\Control_Media::get_image_alt( $this->_processed_item['item_before_image'] );
$img_after_alt  = Elementor\Control_Media::get_image_alt( $this->_processed_item['item_after_image'] );

if ( empty( $item_before_image ) || empty( $item_after_image ) ) {
	return;
}

?>
<div class="jet-image-comparison__item">
	<div class="jet-image-comparison__container jet-juxtapose"
		data-prev-icon="<?php echo esc_attr( $prevArrow ); ?>"
		data-next-icon="<?php echo esc_attr( $nextArrow ); ?>"
		data-makeresponsive="true"
		data-startingposition="<?php echo esc_attr( $starting_position_string ); ?>">
		
		<img class="jet-image-comparison__before-image a3-notlazy"
			src="<?php echo esc_url( $item_before_image ); ?>"
			<?php echo wp_kses_post( $item_before_label ); ?>
			alt="<?php echo esc_attr( $img_before_alt ); ?>"
			data-no-lazy="1">

		<img class="jet-image-comparison__after-image a3-notlazy"
			src="<?php echo esc_url( $item_after_image ); ?>"
			<?php echo wp_kses_post( $item_after_label ); ?>
			alt="<?php echo esc_attr( $img_after_alt ); ?>"
			data-no-lazy="1">
	</div>
</div>

