/**
 * Promotion Banner dismiss functionality.
 *
 * @package JupiterX_Core
 */

( function( $, settings ) {
	'use strict';

	if ( ! settings || ! settings.ajaxUrl ) {
		return;
	}

	$( function() {
		const $banners = $( '.jx-promotion-banner[data-jx-promotion-id]' );

		if ( ! $banners.length ) {
			return;
		}

		/**
		 * Send AJAX request to dismiss a promotion banner.
		 *
		 * @param {HTMLElement} bannerEl The banner element.
		 */
		const sendDismiss = ( bannerEl ) => {
			const $banner = $( bannerEl );
			const promotionId = $banner.data( 'jx-promotion-id' );
			const nonce = $banner.data( 'jx-promotion-nonce' );

			if ( ! promotionId || ! nonce ) {
				return;
			}

			$.ajax( {
				url: settings.ajaxUrl,
				type: 'POST',
				data: {
					action: 'jupiterx_dismiss_admin_promotion',
					promotionId: promotionId,
					nonce: nonce,
				},
			} );
		};

		$banners.each( function() {
			const $banner = $( this );
			const $dismissBtn = $banner.find( '.jx-promotion-banner__dismiss' );

			if ( ! $dismissBtn.length ) {
				return;
			}

			$dismissBtn.on( 'click', function( event ) {
				event.preventDefault();
				$banner.fadeOut( 200, function() {
					$banner.remove();
				} );
				sendDismiss( $banner[ 0 ] );
			} );
		} );
	} );
}( window.jQuery, window.jxPromotionBanner || {} ) );
