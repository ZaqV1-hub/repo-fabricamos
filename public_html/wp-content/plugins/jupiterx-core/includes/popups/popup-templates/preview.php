<?php
namespace JupiterX_Core\Popup\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * JupiterX popup preview.
 *
 * @todo We have to get data from settings
 * so we also need update the html codes as well.
 *
 * @since 3.7.0
 */
class Preview extends Jupiterx_Popup_Template_Base {
	/**
	 * Get popup classes.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_classes() {
		$classes = [
			'jupiterx-popup',
			'jupiterx-popup-preview-mode',
		];

		$classes = apply_filters( 'jupiterx-core/preview-popup/wrappers', $classes );

		if ( ! empty( $classes ) ) {
			$classes = implode( ' ', $classes );
		}

		return $classes;
	}

	/**
	 * Get popup content.
	 *
	 * @since 3.7.0
	 * @return void
	 */
	public function get_content() {
		$data    = $this->data;
		$classes = $this->get_classes();

		ob_start();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?> >
			<head>
				<meta charset="<?php bloginfo( 'charset' ); ?>">
				<meta name="viewport" content="width=device-width, initial-scale=1.0" />
				<title><?php echo esc_html( $data['title'] ); ?></title>
				<?php wp_head(); ?>
			</head>
			<body <?php body_class(); ?>>
				<div id="<?php echo esc_attr( $data['uniqe_id'] ); ?>" class="<?php echo esc_attr( $classes ); ?>">
					<div class="jupiterx-popup__inner">
						<div class="jupiterx-popup__overlay"></div>
						<div class="jupiterx-popup__container">
							<div class="jupiterx-popup__close-button">&times;</div>
							<div class="jupiterx-popup__container-inner">
								<?php
								do_action( 'jupiterx-core/preview-popup/before-content', $data['id'] );

								while ( have_posts() ) :
									the_post();
									the_content();
								endwhile;

								do_action( 'jupiterx-core/preview-popup/after-content', $data['id'] );
								?>
							</div>
						</div>
					</div>
				</div>
			</body>
		</html>
		<?php

		return ob_get_clean();
	}
}
