<?php
/**
 * Scroll Navigation item template
 */
$settings = $this->get_settings();

$hint_classes_array = array( 'jet-scroll-navigation__item-hint' );


if ( filter_var( $settings['desktop_hint_hide'], FILTER_VALIDATE_BOOLEAN ) ) {
	$hint_classes_array[] = 'elementor-hidden-desktop';
}

if ( filter_var( $settings['tablet_hint_hide'], FILTER_VALIDATE_BOOLEAN ) ) {
	$hint_classes_array[] = 'elementor-hidden-tablet';
}

if ( filter_var( $settings['mobile_hint_hide'], FILTER_VALIDATE_BOOLEAN ) ) {
	$hint_classes_array[] = 'elementor-hidden-phone';
}

$hint_classes = implode( ' ', $hint_classes_array );

$section_id_attr = $this->_loop_item( array( 'item_section_id' ), 'data-anchor="%s"' );
$section_invert = $this->_loop_item( array( 'item_section_invert' ), 'data-invert="%s"' );

?><div class="jet-scroll-navigation__item" <?php echo $section_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo $section_invert; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="jet-scroll-navigation__dot"><?php
		echo $this->_icon( 'item_dot_icon', '<span class="jet-elements-icon">%s</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<div class="<?php echo esc_attr( $hint_classes ); ?>"><?php
		echo $this->_icon( 'item_icon', '<span class="jet-scroll-navigation__icon jet-elements-icon">%s</span>' ); // phpcs:ignore
		echo $this->_loop_item( array( 'item_label' ), '<span class="jet-scroll-navigation__label">%s</span>' ); // phpcs:ignore ?>
	</div>
</div>

