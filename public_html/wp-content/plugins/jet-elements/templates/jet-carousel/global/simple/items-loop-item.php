<?php
	/**
	 * Loop item template
	 */
	$settings  = $this->get_settings_for_display();
	$target    = $this->_loop_item( array( 'item_link_target' ), ' target="%s"' );
	$rel       = $this->_loop_item( array( 'item_link_rel' ), ' rel="%s"' );
	$img       = $this->get_advanced_carousel_img( 'jet-carousel__item-img' );

	if( 'true' === $settings['hide_items_without_image'] && empty( $img ) ){
		return;
	}

	$item_settings = $this->_processed_item;

	$content_type = ! empty( $item_settings['item_content_type'] ) ? $item_settings['item_content_type'] : 'default';

	$lightbox = 'data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="'.$this->get_id().'"';
?>

<div class="jet-carousel__item">
	<div class="jet-carousel__item-inner"><?php
		if ( $img ) {
			if ( $settings['item_link_type'] === 'link' ) {
				echo $this->_loop_item( array( 'item_link' ), '<a href="%s" class="jet-carousel__item-link"' . $target . $rel . ' >' ); // phpcs:ignore
			} else {
				$show_lightbox_title = isset( $settings['lightbox_show_title' ] ) ? $settings['lightbox_show_title'] : "false";
				$show_lightbox_desc  = isset( $settings['lightbox_show_desc' ] ) ? $settings['lightbox_show_desc'] : "false";
				$lightbox_title = '';
				$lightbox_desc = '';

				if ( "true" === $show_lightbox_title ) {
					$lightbox_title = 'data-elementor-lightbox-title="' . $this->_loop_item( array( 'item_title' ) ) . '"';
				}

				if ( "true" === $show_lightbox_desc ) {
					$lightbox_desc = 'data-elementor-lightbox-description="' . wp_strip_all_tags( $this->_loop_item( array( 'item_text' ) ) ) . '"';
				}

				printf( '<a href="%1$s" class="jet-carousel__item-link" %2$s %3$s %4$s>', esc_url( $item_settings['item_image']['url'] ), esc_html( $lightbox ), esc_html( $lightbox_title ), esc_html( $lightbox_desc ) );
			}

			echo $img; // phpcs:ignore

			if ( $settings['item_link_type'] === 'link' ) {
				echo $this->_loop_item( array( 'item_link' ), '</a>' ); // phpcs:ignore
			} else {
				printf( '</a>' );
			}
		}
		echo '<div class="jet-carousel__content">';
			switch ( $content_type ) {
				case 'default':
					$title  = $this->_loop_item( array( 'item_title' ) );
					$text   = $this->_loop_item( array( 'item_text' ), '<div class="jet-carousel__item-text">%s</div>' );
					$button = $this->_loop_button_item( array( 'item_link', 'item_button_text' ), '<a class="elementor-button elementor-size-md jet-carousel__item-button" href="%1$s"' . $target . $rel . '>%2$s</a>' );

					$link         = $this->_loop_item( array( 'item_link' ) );
					$title_format = '<%1$s class="jet-carousel__item-title">%2$s</%1$s>';

					if ( $link_title && $link ) {
						$title_format = '<%1$s class="jet-carousel__item-title"><a href="%3$s"%4$s%5$s>%2$s</a></%1$s>';
					}

					if ( $title || $text || $button ) {
						printf( $title_format, $title_tag , esc_html( $title ), esc_url( $link ), esc_attr( $target ), esc_attr( $rel ) ); // phpcs:ignore
						echo $text; // phpcs:ignore
						echo $button; // phpcs:ignore
					}
					break;
				case 'template':
					echo $this->_loop_item_template_content(); // phpcs:ignore
					break;
			}
		echo '</div>';

?></div>
</div>
