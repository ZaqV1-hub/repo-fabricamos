<?php
/**
 * SVG circle template
 */

$settings   = $this->get_settings_for_display();
$size       = is_array( $settings['circle_size'] ) ? $settings['circle_size']['size'] : $settings['circle_size'];
$radius     = $size / 2;
$center     = $radius;
$viewbox    = sprintf( '0 0 %1$s %1$s', $size );
$val_stroke = is_array( $settings['value_stroke'] ) ? $settings['value_stroke']['size'] : $settings['value_stroke'];
$bg_stroke  = is_array( $settings['bg_stroke'] ) ? $settings['bg_stroke']['size'] : $settings['bg_stroke'];

// Fix radius relative to stroke
$max    = ( $val_stroke >= $bg_stroke ) ? $val_stroke : $bg_stroke;
$radius = $radius - ( $max / 2 );

$value = 0;

if ( 'percent' === $settings['values_type'] ) {
	$value = 100 <= $settings['percent_value']['size'] ? 100 : $settings['percent_value']['size'];
} elseif ( 0 !== absint( $settings['absolute_value_max'] ) ) {
	$max   = $settings['absolute_value_max'];
	$curr  = is_string( $settings['absolute_value_curr'] ) ? str_replace(",", "", $settings['absolute_value_curr']) : $settings['absolute_value_curr'];
	$value = round( ( ( floatval( $curr ) * 100 ) / floatval( $max ) ), 0 );
}

$circumference = 2 * M_PI * $radius;

$meter_stroke = ( 'color' === $settings['bg_stroke_type'] ) ? $settings['val_bg_color'] : 'url(#circle-progress-meter-gradient-' . esc_attr( $this->get_id() ) . ')';
$value_stroke = ( 'color' === $settings['val_stroke_type'] ) ? $settings['val_stroke_color'] : 'url(#circle-progress-value-gradient-' . esc_attr( $this->get_id() ) . ')';

$val_bg_gradient_angle     = ! empty( $settings['val_bg_gradient_angle'] ) ? $settings['val_bg_gradient_angle'] : 0;
$val_stroke_gradient_angle = ! empty( $settings['val_stroke_gradient_angle'] ) ? $settings['val_stroke_gradient_angle'] : 0;

?>


<svg class="circle-progress" width="<?php echo esc_attr( $size ); ?>" height="<?php echo esc_attr( $size ); ?>" viewBox="<?php echo esc_attr( $viewbox ); ?>" data-radius="<?php echo esc_attr( $radius ); ?>" data-circumference="<?php echo esc_attr( $circumference ); ?>">
	<linearGradient id="circle-progress-meter-gradient-<?php echo esc_attr( $this->get_id() ); ?>" gradientUnits="objectBoundingBox" gradientTransform="rotate(<?php echo esc_attr( $val_bg_gradient_angle ); ?> 0.5 0.5)" x1="-0.25" y1="0.5" x2="1.25" y2="0.5">
		<stop class="circle-progress-meter-gradient-a" offset="0%" stop-color="<?php echo esc_attr(  $settings['val_bg_gradient_color_a'] ); ?>"/>
		<stop class="circle-progress-meter-gradient-b" offset="100%" stop-color="<?php echo esc_attr( $settings['val_bg_gradient_color_b'] ); ?>"/>
	</linearGradient>
	<linearGradient id="circle-progress-value-gradient-<?php echo esc_attr( $this->get_id() ); ?>" gradientUnits="objectBoundingBox" gradientTransform="rotate(<?php echo esc_attr( $val_stroke_gradient_angle ); ?> 0.5 0.5)" x1="-0.25" y1="0.5" x2="1.25" y2="0.5">
		<stop class="circle-progress-value-gradient-a" offset="0%" stop-color="<?php echo esc_attr( $settings['val_stroke_gradient_color_a'] ); ?>"/>
		<stop class="circle-progress-value-gradient-b" offset="100%" stop-color="<?php echo esc_attr( $settings['val_stroke_gradient_color_b'] ); ?>"/>
	</linearGradient>
	<circle
		class="circle-progress__meter"
		cx="<?php echo esc_attr( $center ); ?>"
		cy="<?php echo esc_attr( $center ); ?>"
		r="<?php echo esc_attr( $radius ); ?>"
		stroke="<?php echo esc_attr( $meter_stroke ); ?>"
		stroke-width="<?php echo esc_attr( $bg_stroke ); ?>"
		fill="none"
	/>
	<circle
		class="circle-progress__value"
		cx="<?php echo esc_attr( $center ); ?>"
		cy="<?php echo esc_attr( $center ); ?>"
		r="<?php echo esc_attr( $radius ); ?>"
		stroke="<?php echo esc_attr( $value_stroke ); ?>"
		stroke-width="<?php echo esc_attr( $val_stroke ); ?>"
		data-value="<?php echo esc_attr( $value ); ?>"
		style="stroke-dasharray: <?php echo esc_attr( $circumference ); ?>; stroke-dashoffset: <?php echo esc_attr( $circumference ); ?>;"
		fill="none"
	/>
</svg>
