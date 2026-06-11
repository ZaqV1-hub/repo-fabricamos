<?php
/**
 * Playlist item title template
 */

$show_duration = ! empty( $settings['show_item_duration'] ) ? $settings['show_item_duration'] : false;

if ( ! $show_duration ) {
	return;
}

$duration = ! empty( $video_data['duration'] ) ? $video_data['duration'] : false;

$video_time = explode(":", $duration);

if ( $video_time[0] != '' && !isset($video_time[1] )) {
	$duration = '00:' . $duration;
}

if ( ! $duration ) {
	return;
}

?>
<div class="jet-blog-playlist__item-duration"><?php echo $duration; // phpcs:ignore ?></div>