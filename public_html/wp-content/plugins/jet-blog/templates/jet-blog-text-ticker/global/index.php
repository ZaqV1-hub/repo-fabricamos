<?php
/**
 * Smart tiles main template
 */

global $post;

$settings  = $this->get_settings();
$is_typing = isset( $settings['typing_effect'] ) && 'yes' === $settings['typing_effect'] ? 1 : 0;

$content_classes = array( 'jet-text-ticker__item-content' );

if ( $is_typing ) {
	$content_classes[] = 'jet-use-typing';

	if ( isset( $settings['multiline_typing'] ) && 'yes' === $settings['multiline_typing'] ) {
		$content_classes[] = 'jet-multiline-typing';
	}
}

?>
<div class="jet-text-ticker">
	<?php $this->_get_current_date( $settings ); ?>
	<?php $this->_get_widget_title( $settings ); ?>
	<div class="jet-text-ticker__posts-wrap">
		<div class="jet-text-ticker__posts" <?php $this->_slider_atts(); ?> dir="ltr" data-typing="<?php echo esc_attr( $is_typing ); ?>"><?php

            $is_post_data_set = false;

			foreach ( $this->_get_query() as $post ) {

				if ( 'yes' !== $settings['use_rss_feed'] || ! is_object( $post ) ) {
					setup_postdata( $post );
					$is_post_data_set = true;
				}

				?>
				<div class="jet-text-ticker__item">
					<div class="<?php echo esc_attr( implode( ' ', $content_classes ) ); ?>">
						<?php $this->_post_thumbnail( $settings ); ?>
						<?php $this->_post_author( $settings ); ?>
						<?php $this->_post_date( $settings ); ?>
						<div class="jet-text-ticker__item-typed-wrap">
							<?php $this->_post_title( $settings ); ?>
						</div>
					</div>
				</div>
				<?php
			}

            if ( $is_post_data_set ) {
	            wp_reset_postdata();
            }

		?></div>
	</div>
</div>