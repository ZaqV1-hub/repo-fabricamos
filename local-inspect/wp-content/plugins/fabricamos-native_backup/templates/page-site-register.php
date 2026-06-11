<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos       = Fabricamos_Native::instance();
$register_error   = isset( $_GET['register_error'] ) ? sanitize_text_field( wp_unslash( $_GET['register_error'] ) ) : '';
$page_title       = 'Cadastro';
$show_user        = false;
$is_logged_in     = $fabricamos->is_public_site_authenticated();
$redirect_to      = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : home_url( '/catalogo/' );
$login_url        = $fabricamos->site_login_url( $redirect_to );
$privacy_page_id  = (int) get_option( 'wp_page_for_privacy_policy' );
$privacy_url      = $privacy_page_id > 0 ? get_permalink( $privacy_page_id ) : '';
$terms_page       = null;
foreach ( array( 'termos-de-uso', 'termos-e-condicoes-gerais-de-uso', 'termos-e-condicoes', 'termos' ) as $terms_slug ) {
	$terms_page = get_page_by_path( $terms_slug, OBJECT, 'page' );
	if ( $terms_page instanceof WP_Post ) {
		break;
	}
}
$terms_url        = $terms_page ? get_permalink( $terms_page ) : $privacy_url;
$activity_options = $fabricamos->get_dictionary_activity_options();
$department_options = $fabricamos->get_dictionary_department_options();
$job_title_options  = $fabricamos->get_dictionary_job_title_options();

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--login">
	<div class="fab-login-wrap fab-login-wrap--wide">
		<div class="fab-login-card">
			<div class="fab-page-intro fab-page-intro--compact">
				<div class="fab-title-line-wrap">
					<h1 class="fab-screen-title">Cadastro</h1>
					<span class="fab-line"></span>
				</div>
			</div>

			<?php if ( $is_logged_in ) : ?>
				<div class="fab-alert fab-alert--success">
					Você já possui uma conta ativa. <a href="<?php echo esc_url( home_url( '/catalogo/' ) ); ?>">Ir para o catálogo</a>.
				</div>
			<?php else : ?>
				<?php if ( $register_error ) : ?>
					<div class="fab-alert fab-alert--error">
						<?php
						if ( 'required' === $register_error ) {
							echo esc_html( 'Preencha todos os campos obrigatórios.' );
						} elseif ( 'email' === $register_error ) {
							echo esc_html( 'Informe um e-mail válido.' );
						} elseif ( 'password_length' === $register_error ) {
							echo esc_html( 'A senha precisa ter pelo menos 8 caracteres.' );
						} elseif ( 'phone' === $register_error ) {
							echo esc_html( 'Informe um celular ou WhatsApp válido.' );
						} elseif ( 'choice' === $register_error ) {
							echo esc_html( 'Selecione opções válidas para ramo, departamento e cargo.' );
						} elseif ( 'privacy' === $register_error ) {
							echo esc_html( 'É necessário aceitar os Termos de Uso para continuar.' );
						} elseif ( 'exists' === $register_error ) {
							echo esc_html( 'Já existe uma conta com este e-mail.' );
						} else {
							echo esc_html( 'Não foi possível criar a conta.' );
						}
						?>
					</div>
				<?php endif; ?>

				<form action="<?php echo esc_url( $fabricamos->site_register_url() ); ?>" method="post" class="fab-login-form">
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>" />
					<?php wp_nonce_field( 'fabricamos_site_register', 'fabricamos_site_register_nonce' ); ?>

					<div class="fab-two-cols">
						<div class="fab-form-stack">
							<label for="fab-register-name">Nome <span class="fab-req">*</span></label>
							<input id="fab-register-name" class="fab-input fab-input--lg" type="text" name="register_name" placeholder="Digite seu nome" required />
						</div>
						<div class="fab-form-stack">
							<label for="fab-register-last-name">Sobrenome <span class="fab-req">*</span></label>
							<input id="fab-register-last-name" class="fab-input fab-input--lg" type="text" name="register_last_name" placeholder="Digite seu sobrenome" required />
						</div>
					</div>

					<div class="fab-form-stack">
						<label for="fab-register-email">E-mail <span class="fab-req">*</span></label>
						<input id="fab-register-email" class="fab-input fab-input--lg" type="email" name="register_email" required />
					</div>

					<div class="fab-form-stack">
						<label for="fab-register-password">Senha <span class="fab-req">*</span></label>
						<input id="fab-register-password" class="fab-input fab-input--lg" type="password" name="register_password" minlength="8" required />
					</div>

					<div class="fab-form-stack">
						<label for="fab-register-institution">Nome da Instituição <span class="fab-req">*</span></label>
						<input id="fab-register-institution" class="fab-input fab-input--lg" type="text" name="register_institution" placeholder="Empresa ou instituição" required />
					</div>

					<div class="fab-two-cols">
						<div class="fab-form-stack">
							<label for="fab-register-phone">Celular / WhatsApp <span class="fab-req">*</span></label>
							<input id="fab-register-phone" class="fab-input fab-input--lg" type="tel" name="register_phone" placeholder="(00) 00000-0000" required />
						</div>
						<div class="fab-form-stack">
							<label for="fab-register-activity">Ramo de Atividade <span class="fab-req">*</span></label>
							<select id="fab-register-activity" class="fab-input fab-select fab-input--lg" name="register_activity_sector" required>
								<option value="">Escolha uma opção</option>
								<?php foreach ( $activity_options as $option ) : ?>
									<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="fab-two-cols">
						<div class="fab-form-stack">
							<label for="fab-register-department">Departamento <span class="fab-req">*</span></label>
							<select id="fab-register-department" class="fab-input fab-select fab-input--lg" name="register_department" required>
								<option value="">Escolha uma opção</option>
								<?php foreach ( $department_options as $option ) : ?>
									<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="fab-form-stack">
							<label for="fab-register-job-title">Cargo <span class="fab-req">*</span></label>
							<select id="fab-register-job-title" class="fab-input fab-select fab-input--lg" name="register_job_title" required>
								<option value="">Escolha uma opção</option>
								<?php foreach ( $job_title_options as $option ) : ?>
									<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<label class="fab-legal-check">
						<input type="checkbox" name="register_privacy_accept" value="1" required />
						<span>
							Li e aceito os
							<?php if ( $terms_url ) : ?>
								<a href="<?php echo esc_url( $terms_url ); ?>" target="_blank" rel="noopener">Termos de Uso</a>
							<?php else : ?>
								Termos de Uso
							<?php endif; ?>
							e autorizo o Dicionário de Substâncias Farmacêuticas (DSF) a coletar e armazenar os dados enviados neste formulário.
							<span class="fab-req">*</span>
						</span>
					</label>

					<div class="fab-actions fab-actions--auth">
						<button class="fab-button fab-button--primary" type="submit">Criar conta</button>
						<a class="fab-button fab-button--ghost" href="<?php echo esc_url( $login_url ); ?>">Entrar</a>
					</div>
				</form>

				<p class="fab-login-alt">
					<a href="<?php echo esc_url( $fabricamos->site_lost_password_url( $redirect_to ) ); ?>">Esqueceu sua senha?</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
