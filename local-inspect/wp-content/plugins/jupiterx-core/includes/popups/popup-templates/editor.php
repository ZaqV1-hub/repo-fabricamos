<?php
namespace JupiterX_Core\Popup\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * JupiterX popup editor template.
 *
 * @since 3.7.0
 */
class Editor extends  Jupiterx_Popup_Template_Base {
	/**
	 * Get popup content.
	 *
	 * @since 3.7.0
	 * @return void
	 */
	public function get_content() {
		$data = $this->data;

		ob_start();
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?> class="no-js">
			<head>
				<meta charset="<?php bloginfo( 'charset' ); ?>">
				<meta name="viewport" content="width=device-width, initial-scale=1.0" />
				<title><?php echo esc_html( $data['title'] ); ?></title>
				<?php wp_head(); ?>
			</head>
			<body <?php body_class(); ?>>
				<div id="jupiterx-popup-editor" class="jupiterx-popup-edit-area">
					<div id="<?php echo esc_attr( $data['uniqe_id'] ); ?>" class="jupiterx-popup jupiterx-popup--edit-mode">
						<div class="jupiterx-popup__inner">
							<div class="jupiterx-popup__overlay"></div>
							<div class="jupiterx-popup__container">
								<div class="jupiterx-popup__close-button">&times;</div>
								<div class="jupiterx-popup__container-inner">
									<div class="jupiterx-popup__container-overlay"></div>
									<?php
									do_action( 'jupiterx-core/editor-popup/before-content', $data['id'] );

									while ( have_posts() ) :
										the_post();
										the_content();
									endwhile;

									do_action( 'jupiterx-core/editor-popup/after-content', $data['id'] );
									wp_footer();
									?>
								</div>
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
