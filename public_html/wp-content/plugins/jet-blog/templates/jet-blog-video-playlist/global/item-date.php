<?php
/**
 * Playlist item publication date template
 */

$show_publication_date = $settings['show_publication_date'] ?? false;

if ( ! $show_publication_date ) {
	return;
}

$human_readable_date_diff = $settings['human_readable_date_diff'] ?? false;
$date_format              = ! empty( $settings['date_format'] ) ? $settings['date_format'] : 'F j, Y';
$publication_date         = $video_data['publication_date'] ?? false;

if ( ! $publication_date ) {
	return;
}

if ( $human_readable_date_diff ) {
	$date_display = $this->_get_human_readable_date_diff( $publication_date );
} else {
	$date_display = $this->_format_date( $publication_date, $date_format );
}

?>
<div class="jet-blog-playlist__item-date"><?php echo wp_kses_post( $date_display ); ?></div>