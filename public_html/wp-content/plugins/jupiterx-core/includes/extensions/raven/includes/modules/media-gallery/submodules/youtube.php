<?php

namespace JupiterX_Core\Raven\Modules\Media_Gallery\Submodules;

use Elementor\Embed;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;

defined( 'ABSPATH' ) || die();

class YouTube extends Base {
	public static function render_item( $data, $settings ) {
		$video_properties = Embed::get_video_properties( $data['youtube_url']['url'] );
		$meta_data        = self::get_meta_data( $data, 'youtube_poster' );
		$lazy             = self::is_lazy_load( $settings ) ? 'loading=lazy' : '';

		$url = add_query_arg(
			[
				'feature'        => 'oembed',
				'wmode'          => 'opaque',
				'loop'           => '0',
				'controls'       => '1',
				'mute'           => '0',
				'rel'            => '0',
				'modestbranding' => '0',
			],
			'https://www.youtube.com/embed/' . esc_html( $video_properties['video_id'] )
		);

		ob_start();
		?>
		<a class="gallery-item"
			href="<?php echo esc_attr( $data['youtube_poster']['url'] ); ?>"
			data-elementor-open-lightbox="yes"
			data-elementor-lightbox-slideshow="<?php echo esc_attr( $data['lightbox_id'] ); ?>"
			data-elementor-lightbox-video="<?php echo esc_attr( esc_url( $url ) ); ?>"
		>
			<div class="type-video youtube">
				<?php
				if ( 'player' !== $settings['video_preview'] ) {
					echo self::poster_image( $data, $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					?>
					<iframe
						<?php echo esc_attr( $lazy ); ?>
						class="elementor-video-iframe"
						src="<?php echo esc_attr( esc_url( $url ) ); ?>"
						title="<?php esc_html_e( 'YouTube video player', 'jupiterx-core' ); ?>"
						frameborder="0"
						allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
						allowfullscreen
					>
					</iframe>
				<?php } ?>
			</div>
			<?php echo self::render_overlay( $meta_data, $settings ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</a>
		<?php
		return ob_get_clean();
	}

	private static function poster_image( $data, $settings ) {
		if ( 'player' === $settings['video_preview'] ) {
			return '';
		}

		// WPML compatibility.
		$data['youtube_poster']['id']  = apply_filters( 'wpml_object_id', $data['youtube_poster']['id'], 'attachment', true );
		$data['youtube_poster']['alt'] = get_post_meta( $data['youtube_poster']['id'], '_wp_attachment_image_alt', true );

		$lazy       = self::is_lazy_load( $settings ) ? 'loading="lazy"' : '';
		$poster_url = Group_Control_Image_Size::get_attachment_image_src( $data['youtube_poster']['id'], 'thumbnail_image', $settings );
		$play_icon  = self::render_play_icon( $settings );
		$zoom_img   = '';

		if ( 'zoom' === $settings['image_hover_animation'] && ! empty( $data['youtube_poster']['id'] ) ) {
			$full_poster = wp_get_attachment_image_url( $data['youtube_poster']['id'], 'full' );
			$zoom_img    = sprintf( '<img alt="zoomImg" class="zoom-animation-image" src="%s">', $full_poster );
		}

		if ( empty( $poster_url ) ) {
			$poster_url = Utils::get_placeholder_image_src();
		}

		return sprintf(
			'<div class="poster">%1$s%2$s<img src="%3$s" alt="%4$s" %5$s></div>',
			$play_icon,
			$zoom_img,
			esc_url( $poster_url ),
			! empty( $data['youtube_poster']['alt'] ) ? esc_html( $data['youtube_poster']['alt'] ) : '',
			$lazy
		);
	}
}
