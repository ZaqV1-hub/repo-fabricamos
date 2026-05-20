<?php
/**
 * Progress Bar template
 */
$settings = $this->get_settings_for_display();
$progress_type = $settings['progress_type'];
$allowed_type = [ 'type-1', 'type-2', 'type-3', 'type-4', 'type-5', 'type-6', 'type-7' ];
$progress_type = in_array( $progress_type, $allowed_type ) ? $progress_type : 'type-1';

$this->add_render_attribute( 'main-container', 'class', array(
	'jet-progress-bar',
	'jet-progress-bar-' . $progress_type,
) );

$prefix = esc_html__( $settings['absolute_value_prefix'] );
$suffix = esc_html__( $settings['absolute_value_suffix'] );

$prefix_html = '<span class="jet-progress-bar__percent-prefix">' . $prefix . '&nbsp</span>';
$suffix_html = '<span class="jet-progress-bar__percent-suffix">&nbsp' . $suffix . '</span>';

if ( 'percent' === $settings['values_type'] ) {
	$percent_value = 100 <= $settings['percent'] ? 100 : $settings['percent'];
	$this->add_render_attribute( 'main-container', 'data-percent', $percent_value );
	$percent_html = '<span class="jet-progress-bar__percent-value">0</span><span class="jet-progress-bar__percent-suffix">&#37;</span>';
} else {
	$current_value = (int)$settings['absolute_value_curr'];
	$max_value     = (int)$settings['absolute_value_max'];

	if ( $max_value === 0 ) {
		return;
	}

	$percent       = ceil( $current_value / ( $max_value / 100 ) );
	$percent_html  = $prefix_html . '<span class="jet-progress-bar__percent-value">0/' . $max_value . '</span>' . $suffix_html;

	$this->add_render_attribute( 'main-container', 'data-percent', $percent );
	$this->add_render_attribute( 'main-container', 'data-current-value', $current_value );
	$this->add_render_attribute( 'main-container', 'data-max-value', $max_value );

	if ( $current_value >= $max_value ) {
		$this->add_render_attribute( 'main-container', 'class', 'jet-progress-bar--completed' );
	}
}

$this->add_render_attribute( 'main-container', 'data-type', $progress_type );

?>
<div <?php echo $this->get_render_attribute_string( 'main-container' ); // phpcs:ignore ?>>
	<?php include $this->_get_type_template( sanitize_file_name( $progress_type ) ); ?>
</div>

