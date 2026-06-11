<?php
/**
 * Payment success modal.
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}
?>
<div class="cl-accelerate-wp__modal" data-accelerate-wp-modal="subscription-success">
	<form class="cl-accelerate-wp__modal-form">
		<div class="cl-accelerate-wp__modal-form__title">
			Your payment was successful
		</div>
		<div class="cl-accelerate-wp__modal-form__close" data-accelerate-wp-modal-close="subscription-success">
			<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd" d="M16 1.61143L14.3886 0L8 6.38857L1.61143 0L0 1.61143L6.38857 8L0 14.3886L1.61143 16L8 9.61143L14.3886 16L16 14.3886L9.61143 8L16 1.61143Z" fill="#AFAFAF"/>
			</svg>
		</div>
		<div class="cl-accelerate-wp__modal-form__subscription-status cl-accelerate-wp__modal-form__subscription-status_with-icon">
			<svg width="18" height="21" viewBox="0 0 18 21" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M6 2V0H12V2H6ZM8 13H10V7H8V13ZM9 21C7.76667 21 6.604 20.7627 5.512 20.288C4.42067 19.8127 3.46667 19.1667 2.65 18.35C1.83333 17.5333 1.18733 16.5793 0.712 15.488C0.237333 14.396 0 13.2333 0 12C0 10.7667 0.237333 9.604 0.712 8.512C1.18733 7.42067 1.83333 6.46667 2.65 5.65C3.46667 4.83333 4.42067 4.18767 5.512 3.713C6.604 3.23767 7.76667 3 9 3C10.0333 3 11.025 3.16667 11.975 3.5C12.925 3.83333 13.8167 4.31667 14.65 4.95L16.05 3.55L17.45 4.95L16.05 6.35C16.6833 7.18333 17.1667 8.075 17.5 9.025C17.8333 9.975 18 10.9667 18 12C18 13.2333 17.7627 14.396 17.288 15.488C16.8127 16.5793 16.1667 17.5333 15.35 18.35C14.5333 19.1667 13.5793 19.8127 12.488 20.288C11.396 20.7627 10.2333 21 9 21ZM9 19C10.9333 19 12.5833 18.3167 13.95 16.95C15.3167 15.5833 16 13.9333 16 12C16 10.0667 15.3167 8.41667 13.95 7.05C12.5833 5.68333 10.9333 5 9 5C7.06667 5 5.41667 5.68333 4.05 7.05C2.68333 8.41667 2 10.0667 2 12C2 13.9333 2.68333 15.5833 4.05 16.95C5.41667 18.3167 7.06667 19 9 19Z" fill="#1C1B1F"/>
			</svg>
			<div>
				The CDN Pro package you enabled will be applied to your WordPress website in several minutes. To check if features were applied, please refresh the page. You can close this window.
			</div>
		</div>
		<div class="cl-accelerate-wp__modal-form__footer">
			<a href="javascript:void(0)" data-accelerate-wp-modal-close="subscription-success" data-target="modal" class="cl-accelerate-wp__modal-form__btn cl-accelerate-wp__modal-form__btn_confirm">Confirm</a>
		</div>
	</form>
</div>
