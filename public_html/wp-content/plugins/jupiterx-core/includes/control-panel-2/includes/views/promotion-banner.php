<?php
/**
 * Template for the global admin promotion banner.
 *
 * @package JupiterX_Core\Control_Panel_2\Promotion_Banner
 *
 * @since 4.13.0
 */

?>
<div
	class="jx-promotion-banner<?php echo empty( $description ) ? ' jx-promotion-banner--compact' : ''; ?><?php echo ! empty( $image_url ) ? ' jx-promotion-banner--has-image' : ''; ?><?php echo ! empty( $banner_id ) ? ' jx-promotion-banner--' . esc_attr( sanitize_html_class( $banner_id ) ) : ''; ?>"
	data-jx-promotion-id="<?php echo esc_attr( $promotion_id ); ?>"
	data-jx-promotion-nonce="<?php echo esc_attr( $nonce ); ?>"
	<?php echo ! empty( $bg_style ) ? 'style="' . esc_attr( $bg_style ) . '"' : ''; ?>
>
	<?php if ( ! empty( $image_url ) ) : ?>
		<div class="jx-promotion-banner__image">
			<img src="<?php echo esc_url( $image_url ); ?>" alt="" />
		</div>
	<?php endif; ?>

	<div class="jx-promotion-banner__inner">
		<div class="jx-promotion-banner__content">
			<div class="jx-promotion-banner__top-row">
				<?php if ( ! empty( $heading ) ) : ?>
					<div class="jx-promotion-banner__title">
						<?php echo esc_html( $heading ); ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $description ) || $has_code ) : ?>
					<div class="jx-promotion-banner__description-cell">
					<?php if ( ! empty( $description ) ) : ?>
						<div class="jx-promotion-banner__description">
							<?php echo nl2br( esc_html( $description ) ); ?>
						</div>
					<?php endif; ?>

						<?php if ( $has_code ) : ?>
							<div class="jx-promotion-banner__code">
								<?php echo esc_html( "CODE {$coupon_code}" ); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $has_cta ) : ?>
					<div class="jx-promotion-banner__cta">
						<a
							href="<?php echo esc_url( $cta_url ); ?>"
							target="_blank"
							rel="noopener noreferrer"
							class="button"
						>
							<span class="cta-text"><?php echo esc_html( $cta_text ); ?></span>
							<span class="cta-subtext"><?php echo esc_html( $cta_subtext ); ?></span>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ( $is_dismissible ) : ?>
		<button
			type="button"
			class="jx-promotion-banner__dismiss"
			aria-label="<?php echo esc_attr_x( 'Dismiss promotion', 'admin promotion banner', 'jupiterx-core' ); ?>"
		></button>
	<?php endif; ?>
</div>
