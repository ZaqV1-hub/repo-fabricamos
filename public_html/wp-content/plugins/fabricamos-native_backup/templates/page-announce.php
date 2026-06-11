<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos = Fabricamos_Native::instance();
$status     = isset( $_GET[ Fabricamos_Native::QUERY_SUCCESS ] ) ? sanitize_text_field( wp_unslash( $_GET[ Fabricamos_Native::QUERY_SUCCESS ] ) ) : '';
$defaults   = $fabricamos->get_announce_form_defaults();
$page_title = 'Anunciar Fabricante';
$show_user  = is_user_logged_in();

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--narrow">
	<div class="fab-container">
		<div class="fab-head-row">
			<a class="fab-back" href="<?php echo esc_url( home_url( '/catalogo/' ) ); ?>" aria-label="Voltar">←</a>
			<div class="fab-title-line-wrap">
				<h1 class="fab-screen-title">Enviar e-mail</h1>
				<span class="fab-line"></span>
			</div>
		</div>

		<?php if ( 'error' === $status ) : ?>
			<div class="fab-alert fab-alert--error">Não foi possível enviar o formulário. Tente novamente.</div>
		<?php endif; ?>

		<div class="fab-panel fab-panel--soft">
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="fab-announce-form">
				<input type="hidden" name="action" value="fabricamos_announce" />
				<?php wp_nonce_field( 'fabricamos_announce', 'fabricamos_announce_nonce' ); ?>

				<div class="fab-two-cols">
					<div>
						<label class="fab-label-strong" for="an_nome">Nome do usuário</label>
						<input id="an_nome" class="fab-input" name="an_nome" value="<?php echo esc_attr( $defaults['name'] ); ?>" required />
					</div>
					<div>
						<label class="fab-label-strong" for="an_empresa">Empresa</label>
						<input id="an_empresa" class="fab-input" name="an_empresa" required />
					</div>
				</div>

				<div class="fab-two-cols">
					<div>
						<label class="fab-label-strong" for="an_cargo">Cargo</label>
						<input id="an_cargo" class="fab-input" name="an_cargo" required />
					</div>
					<div>
						<label class="fab-label-strong" for="an_telefone">Telefone</label>
						<input id="an_telefone" class="fab-input" name="an_telefone" required />
					</div>
				</div>

				<div>
					<label class="fab-label-strong" for="an_email">E-mail</label>
					<input id="an_email" class="fab-input" name="an_email" type="email" value="<?php echo esc_attr( $defaults['email'] ); ?>" required />
				</div>

				<div>
					<label class="fab-label-strong" for="an_assunto">Assunto</label>
					<input id="an_assunto" class="fab-input" name="an_assunto" value="Quero anunciar meu fabricante" required />
				</div>

				<div>
					<label class="fab-label-strong" for="an_mensagem">Mensagem</label>
					<textarea id="an_mensagem" class="fab-textarea fab-textarea--tall" name="an_mensagem" required></textarea>
				</div>

				<button class="fab-button fab-button--primary fab-button--send" type="submit">Enviar e-mail</button>
			</form>
		</div>
	</div>

	<div class="fab-modal<?php echo 'sent' === $status ? ' is-visible' : ''; ?>" data-open="<?php echo 'sent' === $status ? '1' : '0'; ?>" <?php echo 'sent' === $status ? '' : 'hidden'; ?>>
		<div class="fab-modal__box">
			<h2 class="fab-modal__title">E-mail enviado com sucesso</h2>
			<p class="fab-modal__text">Seu pedido foi registrado. Você pode voltar para o catálogo do comprador.</p>
			<button class="fab-button fab-button--primary" type="button" data-modal-catalog>Voltar para o catálogo</button>
		</div>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
