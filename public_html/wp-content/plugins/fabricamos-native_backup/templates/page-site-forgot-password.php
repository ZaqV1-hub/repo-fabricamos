<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos      = Fabricamos_Native::instance();
$password_error  = isset( $_GET['password_error'] ) ? sanitize_text_field( wp_unslash( $_GET['password_error'] ) ) : '';
$password_notice = isset( $_GET['password_notice'] ) ? sanitize_text_field( wp_unslash( $_GET['password_notice'] ) ) : '';
$login_hint      = isset( $_GET['login_hint'] ) ? sanitize_text_field( wp_unslash( $_GET['login_hint'] ) ) : '';
$page_title      = 'Esqueceu sua senha';
$show_user       = false;
$is_logged_in    = $fabricamos->is_public_site_authenticated();
$redirect_to     = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : home_url( '/catalogo/' );
$login_url       = $fabricamos->site_login_url( $redirect_to );

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--login">
	<div class="fab-login-wrap fab-login-wrap--wide">
		<div class="fab-login-card">
			<div class="fab-page-intro fab-page-intro--compact">
				<div class="fab-title-line-wrap">
					<h1 class="fab-screen-title">Esqueceu sua senha?</h1>
					<span class="fab-line"></span>
				</div>
				<p class="fab-page-copy">Informe seu e-mail para receber um link de redefinicao de senha.</p>
			</div>

			<?php if ( $is_logged_in ) : ?>
				<div class="fab-alert fab-alert--success">
					Voce ja esta autenticado. <a href="<?php echo esc_url( home_url( '/catalogo/' ) ); ?>">Ir para o catalogo</a>.
				</div>
			<?php else : ?>
				<?php if ( 'sent' === $password_notice ) : ?>
					<div class="fab-alert fab-alert--success">
						Se existir uma conta publica com este e-mail, enviaremos um link para redefinir a senha.
					</div>
				<?php endif; ?>

				<?php if ( $password_error ) : ?>
					<div class="fab-alert fab-alert--error">
						<?php
						if ( 'required' === $password_error ) {
							echo esc_html( 'Informe seu e-mail para continuar.' );
						} elseif ( 'email' === $password_error ) {
							echo esc_html( 'Informe um e-mail valido.' );
						} elseif ( 'invalid_key' === $password_error ) {
							echo esc_html( 'O link informado e invalido ou expirou. Solicite um novo link.' );
						} elseif ( 'send' === $password_error ) {
							echo esc_html( 'Nao foi possivel enviar o e-mail agora. Tente novamente.' );
						} else {
							echo esc_html( 'Nao foi possivel processar a solicitacao.' );
						}
						?>
					</div>
				<?php endif; ?>

				<form action="<?php echo esc_url( $fabricamos->site_lost_password_url() ); ?>" method="post" class="fab-login-form">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>" />
					<?php wp_nonce_field( 'fabricamos_site_lost_password', 'fabricamos_site_lost_password_nonce' ); ?>

					<label for="fab-site-lost-email">E-mail <span class="fab-req">*</span></label>
					<input id="fab-site-lost-email" class="fab-input fab-input--lg" type="email" name="user_login" value="<?php echo esc_attr( $login_hint ); ?>" required />

					<button class="fab-button fab-button--primary" type="submit">Enviar link de redefinicao</button>
				</form>

				<p class="fab-login-alt">
					<a href="<?php echo esc_url( $login_url ); ?>">Voltar para entrar</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
