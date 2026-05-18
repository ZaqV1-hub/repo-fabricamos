<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos    = Fabricamos_Native::instance();
$login_error   = isset( $_GET['login_error'] ) ? sanitize_text_field( wp_unslash( $_GET['login_error'] ) ) : '';
$login_notice  = isset( $_GET['login_notice'] ) ? sanitize_text_field( wp_unslash( $_GET['login_notice'] ) ) : '';
$login_hint    = isset( $_GET['login_hint'] ) ? sanitize_text_field( wp_unslash( $_GET['login_hint'] ) ) : '';
$success_state = isset( $_GET[ Fabricamos_Native::QUERY_SUCCESS ] ) ? sanitize_text_field( wp_unslash( $_GET[ Fabricamos_Native::QUERY_SUCCESS ] ) ) : '';
$page_title    = 'Entrar';
$show_user     = false;
$is_logged_in  = $fabricamos->is_public_site_authenticated();
$redirect_to   = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : home_url( '/catalogo/' );
$register_url  = $fabricamos->site_register_url( $redirect_to );
$lost_password = $fabricamos->site_lost_password_url( $redirect_to );

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--login">
	<div class="fab-login-wrap fab-login-wrap--wide">
		<div class="fab-login-card">
			<div class="fab-page-intro fab-page-intro--compact">
				<span class="fab-page-kicker">Fabricamos</span>
				<div class="fab-title-line-wrap">
					<h1 class="fab-screen-title">Entrar</h1>
					<span class="fab-line"></span>
				</div>
				<p class="fab-page-copy">Entre com seu usuário ou e-mail para acessar o catálogo do Fabricamos.</p>
			</div>

			<?php if ( $is_logged_in ) : ?>
				<div class="fab-alert fab-alert--success">
					Você já está autenticado. <a href="<?php echo esc_url( home_url( '/catalogo/' ) ); ?>">Ir para o catálogo</a>.
				</div>
			<?php else : ?>
				<?php if ( 'registered' === $success_state ) : ?>
					<div class="fab-alert fab-alert--success">
						Cadastro concluido. Um e-mail de confirmacao foi enviado e seu acesso ao Fabricamos ja esta liberado.
					</div>
				<?php endif; ?>

				<?php if ( 'existing_account' === $login_notice ) : ?>
					<div class="fab-alert fab-alert--success">
						Ja existe uma conta com este e-mail. Entre com a senha ja cadastrada no Dicionario ou redefina sua senha.
					</div>
				<?php elseif ( 'password_reset' === $login_notice ) : ?>
					<div class="fab-alert fab-alert--success">
						Senha redefinida com sucesso. Entre com a sua nova senha.
					</div>
				<?php endif; ?>

				<?php if ( $login_error ) : ?>
					<div class="fab-alert fab-alert--error">
						<?php
						if ( 'invalid' === $login_error ) {
							echo esc_html( 'Usuário, e-mail ou senha inválidos.' );
						} else {
							echo esc_html( 'Não foi possível autenticar a solicitação.' );
						}
						?>
					</div>
				<?php endif; ?>

				<form action="<?php echo esc_url( $fabricamos->site_login_url() ); ?>" method="post" class="fab-login-form">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>" />
					<?php wp_nonce_field( 'fabricamos_site_login', 'fabricamos_site_login_nonce' ); ?>

					<label for="fab-site-log">Nome de usuário ou endereço de e-mail <span class="fab-req">*</span></label>
					<input id="fab-site-log" class="fab-input fab-input--lg" type="text" name="log" value="<?php echo esc_attr( $login_hint ); ?>" required />

					<label for="fab-site-pwd">Senha <span class="fab-req">*</span></label>
					<input id="fab-site-pwd" class="fab-input fab-input--lg" type="password" name="pwd" required />

					<label class="fab-remember">
						<input type="checkbox" name="rememberme" value="forever" />
						<span>Lembrar de mim</span>
					</label>

					<button class="fab-button fab-button--primary" type="submit">Entrar</button>
				</form>

				<p class="fab-login-alt">
					Não tem conta? <a href="<?php echo esc_url( $register_url ); ?>">Crie sua conta »</a>
				</p>
				<p class="fab-login-alt">
					<a href="<?php echo esc_url( $lost_password ); ?>">Esqueceu sua senha?</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
