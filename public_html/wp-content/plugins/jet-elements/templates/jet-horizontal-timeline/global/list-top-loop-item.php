<?php
/**
 * Timeline list item template
 */
$settings      = $this->get_settings_for_display();
$desktop_layout = $settings['vertical_layout'];
$mobile_layout = isset( $settings['mobile_vertical_layout'] ) ? $settings['mobile_vertical_layout'] : $settings['vertical_layout'];
$item_settings = $this->_processed_item;

$is_editor_mode = \Elementor\Plugin::$instance->editor->is_edit_mode();

if ( $is_editor_mode ) {
	$this->add_render_attribute(
		'item_top_' . $item_settings['_id'],
		array(
			'class' => array(
				'jet-hor-timeline-item',
				'elementor-repeater-item-' . esc_attr( $item_settings['_id'] )
			),
			'data-item-id' => esc_attr( $item_settings['_id'] ),
			'data-desktop-layout' => esc_attr( $desktop_layout ),
			'data-mobile-layout' => esc_attr( $mobile_layout )
		)
	);
} else {	
	$layout = true === wp_is_mobile() ? $mobile_layout : $desktop_layout;
	$this->add_render_attribute(
		'item_top_' . $item_settings['_id'],
		array(
			'class' => array(
				'jet-hor-timeline-item',
				'elementor-repeater-item-' . esc_attr( $item_settings['_id'] )
			),
			'data-item-id' => esc_attr( $item_settings['_id'] )
		)
	);
}

if ( filter_var( $item_settings['is_item_active'], FILTER_VALIDATE_BOOLEAN ) ) {
	$this->add_render_attribute( 'item_top_' . $item_settings['_id'], 'class', 'is-active' );
}
?>

<div <?php $this->print_render_attribute_string( 'item_top_' . $item_settings['_id'] ) ?>>
	<?php if ( $is_editor_mode ) : ?>
		<div class="jet-hor-timeline-desktop-content">
			<?php
			switch ( $desktop_layout ) {
				case 'top':
					include $this->_get_global_template( 'card' );
					break;
				case 'chess':
					if ( $this->_processed_index % 2 ) {
						include $this->_get_global_template( 'meta' );
					} else {
						include $this->_get_global_template( 'card' );
					}
					break;
				case 'bottom':
					include $this->_get_global_template( 'meta' );
					break;
			}
			?>
		</div>
		<div class="jet-hor-timeline-mobile-content">
			<?php
			switch ( $mobile_layout ) {
				case 'top':
					include $this->_get_global_template( 'card' );
					break;
				case 'chess':
					if ( $this->_processed_index % 2 ) {
						include $this->_get_global_template( 'meta' );
					} else {
						include $this->_get_global_template( 'card' );
					}
					break;
				case 'bottom':
					include $this->_get_global_template( 'meta' );
					break;
			}
			?>
		</div>
	<?php else : ?>
		<?php
		switch ( $layout ) {
			case 'top':
				include $this->_get_global_template( 'card' );
				break;
			case 'chess':
				if ( $this->_processed_index % 2 ) {
					include $this->_get_global_template( 'meta' );
				} else {
					include $this->_get_global_template( 'card' );
				}
				break;
			case 'bottom':
				include $this->_get_global_template( 'meta' );
				break;
		}
		?>
	<?php endif; ?>
</div>
