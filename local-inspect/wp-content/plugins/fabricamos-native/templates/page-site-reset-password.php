<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos      = Fabricamos_Native::instance();
$password_error  = isset( $_GET['password_error'] ) ? sanitize_text_field( wp_unslash( $_GET['password_error'] ) ) : '';
$page_title      = 'Redefinir senha';
$show_user       = false;
$redirect_to     = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : home_url( '/catalogo/' );
$login_url       = $fabricamos->site_login_url( $redirect_to );
$lost_url        = $fabricamos->site_lost_password_url( $redirect_to );
$login_name      = isset( $_GET['login'] ) ? sanitize_text_field( wp_unslash( $_GET['login'] ) ) : '';
$reset_key       = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
$reset_user      = $fabricamos->validate_public_password_reset_request( $login_name, $reset_key );
$is_valid_key    = ! is_wp_error( $reset_user );

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--login">
	<div class="fab-login-wrap fab-login-wrap--wide">
		<div class="fab-login-card">
			<div class="fab-page-intro fab-page-intro--compact">
				<div class="fab-title-line-wrap">
					<h1 class="fab-screen-title">Redefinir senha</h1>
					<span class="fab-line"></span>
				</div>
				<p class="fab-page-copy">Defina sua nova senha para voltar a acessar o Fabricamos.</p>
			</div>

			<?php if ( ! $is_valid_key ) : ?>
				<div class="fab-alert fab-alert--error">
					Este link de redefinicao e invalido ou expirou. Solicite um novo link para continuar.
				</div>
				<p class="fab-login-alt">
					<a href="<?php echo esc_url( $lost_url ); ?>">Solicitar novo link</a>
				</p>
			<?php else : ?>
				<?php if ( $password_error ) : ?>
					<div class="fab-alert fab-alert--error">
						<?php
						if ( 'required' === $password_error ) {
							echo esc_html( 'Preencha os dois campos de senha.' );
						} elseif ( 'password_length' === $password_error ) {
							echo esc_html( 'A senha precisa ter pelo menos 8 caracteres.' );
						} elseif ( 'confirm' === $password_error ) {
							echo esc_html( 'As senhas informadas nao conferem.' );
						} else {
							echo esc_html( 'Nao foi possivel redefinir a senha.' );
						}
						?>
					</div>
				<?php endif; ?>

				<form action="<?php echo esc_url( $fabricamos->site_reset_password_url() ); ?>" method="post" class="fab-login-form">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>" />
					<input type="hidden" name="login" value="<?php echo esc_attr( $login_name ); ?>" />
					<input type="hidden" name="key" value="<?php echo esc_attr( $reset_key ); ?>" />
					<?php wp_nonce_field( 'fabricamos_site_reset_password', 'fabricamos_site_reset_password_nonce' ); ?>

					<label for="fab-site-reset-pass1">Nova senha <span class="fab-req">*</span></label>
					<input id="fab-site-reset-pass1" class="fab-input fab-input--lg" type="password" name="pass1" minlength="8" required />

					<label for="fab-site-reset-pass2">Confirmar nova senha <span class="fab-req">*</span></label>
					<input id="fab-site-reset-pass2" class="fab-input fab-input--lg" type="password" name="pass2" minlength="8" required />

					<button class="fab-button fab-button--primary" type="submit">Salvar nova senha</button>
				</form>

				<p class="fab-login-alt">
					<a href="<?php echo esc_url( $login_url ); ?>">Voltar para entrar</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
