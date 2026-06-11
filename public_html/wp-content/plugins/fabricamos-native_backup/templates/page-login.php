<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos  = Fabricamos_Native::instance();
$login_error = isset( $_GET['login_error'] ) ? sanitize_text_field( wp_unslash( $_GET['login_error'] ) ) : '';
$page_title  = 'Fabricante';
$show_user   = false;
$is_authenticated = $fabricamos->is_manufacturer_authenticated();

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--login">
	<div class="fab-login-wrap">
		<div class="fab-login-card">
			<div class="fab-page-intro fab-page-intro--compact">
				<span class="fab-page-kicker">Acesso isolado</span>
				<div class="fab-title-line-wrap">
					<h1 class="fab-screen-title">Fabricante</h1>
					<span class="fab-line"></span>
				</div>
			</div>

			<?php if ( $is_authenticated ) : ?>
				<div class="fab-alert fab-alert--success">
					Você já está autenticado. <a href="<?php echo esc_url( home_url( '/meu-fabricante/' ) ); ?>">Ir para a edição do fabricante</a>.
				</div>
			<?php else : ?>
				<?php if ( $login_error ) : ?>
					<div class="fab-alert fab-alert--error">
						<?php
						if ( 'invalid' === $login_error ) {
							echo esc_html( 'Usuário ou senha inválidos.' );
						} elseif ( 'not_allowed' === $login_error ) {
							echo esc_html( 'Este usuário não possui acesso ao Fabricamos do fabricante.' );
						} else {
							echo esc_html( 'Não foi possível autenticar a solicitação.' );
						}
						?>
					</div>
				<?php endif; ?>

				<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="fab-login-form" autocomplete="off" data-fab-exclusive-auth="manufacturer">
					<input type="hidden" name="action" value="fabricamos_login" />
					<?php wp_nonce_field( 'fabricamos_login', 'fabricamos_login_nonce' ); ?>

					<label for="fab-log">Nome de Usuário ou Endereço de e-mail:<span class="fab-req">*</span></label>
					<input id="fab-log" class="fab-input fab-input--lg" type="text" name="fabricamos_login_user" autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" data-lpignore="true" required />

					<label for="fab-pwd">Senha:<span class="fab-req">*</span></label>
					<input id="fab-pwd" class="fab-input fab-input--lg" type="password" name="fabricamos_login_pass" autocomplete="new-password" data-lpignore="true" required />

					<label class="fab-remember">
						<input type="checkbox" name="rememberme" value="forever" />
						<span>Lembre de mim</span>
					</label>

					<button class="fab-button fab-button--ghost" type="submit">Login</button>
				</form>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
