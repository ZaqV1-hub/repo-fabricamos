<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos   = Fabricamos_Native::instance();
$manufacturer = $fabricamos->get_current_user_manufacturer();
$detail       = $manufacturer ? $fabricamos->get_manufacturer_detail( $manufacturer ) : null;
$status       = isset( $_GET[ Fabricamos_Native::QUERY_SUCCESS ] ) ? sanitize_text_field( wp_unslash( $_GET[ Fabricamos_Native::QUERY_SUCCESS ] ) ) : '';
$page_title   = 'Meu Fabricante';
$show_user    = true;

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--narrow fab-page--profile">
	<div class="fab-container">
		<div class="fab-page-intro">
			<span class="fab-page-kicker">Área do fabricante</span>
			<div class="fab-title-line-wrap">
				<h1 class="fab-screen-title"><?php echo esc_html( 'Editar fabricante' ); ?></h1>
				<span class="fab-line"></span>
			</div>
		</div>

		<?php if ( 'saved' === $status ) : ?>
			<div class="fab-alert fab-alert--success">Cadastro atualizado com sucesso.</div>
		<?php elseif ( 'invalid_phone' === $status ) : ?>
			<div class="fab-alert fab-alert--error">Informe um telefone v&aacute;lido com DDD. Exemplo: (11) 4000-1000.</div>
		<?php elseif ( 'error' === $status ) : ?>
			<div class="fab-alert fab-alert--error">Não foi possível salvar as alterações.</div>
		<?php endif; ?>

		<?php if ( ! $detail ) : ?>
			<div class="fab-empty-state fab-empty-state--panel">
				<h2>Fabricante não vinculado</h2>
				<p>Este login ainda não possui um fabricante vinculado no Fabricamos.</p>
			</div>
		<?php else : ?>
			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data" class="fab-profile-form fab-profile-form--editor">
				<input type="hidden" name="action" value="fabricamos_profile" />
				<?php wp_nonce_field( 'fabricamos_profile', 'fabricamos_profile_nonce' ); ?>

				<div class="fab-hero-card fab-profile-hero-card">
					<div class="fab-profile-image-editor" data-fab-image-field>
						<img
							class="fab-hero-image fab-profile-image-editor__preview<?php echo $detail['has_hero_image'] ? '' : ' is-placeholder'; ?>"
							src="<?php echo esc_attr( $detail['has_hero_image'] ? $detail['hero_image'] : $detail['editor_placeholder'] ); ?>"
							alt="<?php echo esc_attr( $detail['title'] ); ?>"
							data-fab-image-preview
							data-default-src="<?php echo esc_attr( $detail['has_hero_image'] ? $detail['hero_image'] : $detail['editor_placeholder'] ); ?>"
							data-placeholder-src="<?php echo esc_attr( $detail['editor_placeholder'] ); ?>"
						/>
						<div class="fab-profile-image-editor__actions">
							<input id="fab-hero-image-file" type="file" name="fab_hero_image_file" accept="image/*" hidden data-fab-image-input />
							<input type="hidden" name="fab_hero_image_remove" value="0" data-fab-image-remove />
							<button class="fab-button fab-button--ghost" type="button" data-fab-image-trigger>Adicionar imagem</button>
							<button class="fab-button fab-button--ghost" type="button" data-fab-image-clear>Remover imagem</button>
						</div>
					</div>
				</div>

				<div class="fab-panel">
					<div class="fab-form-stack">
						<label class="fab-label-strong" for="fab-description">Descrição</label>
						<textarea id="fab-description" class="fab-textarea" rows="5" name="fab_description"><?php echo esc_textarea( $detail['description'] ); ?></textarea>
					</div>
				</div>

				<div class="fab-panel">
					<div class="fab-form-stack">
						<label class="fab-label-strong" for="fab-contact-name">Responsável pela edição</label>
						<input id="fab-contact-name" class="fab-input" name="fab_contact_name" value="<?php echo esc_attr( $detail['editor_name'] ); ?>" />
					</div>

					<div class="fab-two-cols">
						<div class="fab-form-stack">
							<label class="fab-label-strong" for="fab-phone">Telefone do responsável</label>
							<input id="fab-phone" class="fab-input" name="fab_phone" type="tel" inputmode="tel" placeholder="(11) 4000-1000" value="<?php echo esc_attr( $detail['editor_phone'] ); ?>" />
						</div>
						<div class="fab-form-stack">
							<label class="fab-label-strong" for="fab-email">E-mail do responsável</label>
							<input id="fab-email" class="fab-input" name="fab_email" type="email" value="<?php echo esc_attr( $detail['editor_email'] ); ?>" />
						</div>
					</div>

					<div class="fab-form-stack">
						<label class="fab-label-strong" for="fab-site">Site</label>
						<input id="fab-site" class="fab-input" name="fab_site" value="<?php echo esc_attr( $detail['site'] ); ?>" />
					</div>
				</div>

				<div class="fab-panel">
					<div class="fab-form-stack">
						<div class="fab-field">
							<label class="fab-label-strong" for="fab-substances-search">Pesquisar Insumo</label>
							<input
								id="fab-substances-search"
								class="fab-input"
								type="text"
								placeholder="Digite ao menos 1 caractere"
								autocomplete="off"
								data-fab-substance-search
							/>
							<div class="fab-suggestions" data-fab-suggestions></div>
						</div>
					</div>

					<div class="fab-chip-list" data-fab-chip-list>
						<?php foreach ( $detail['substances'] as $substance ) : ?>
							<span class="fab-chip" data-chip-id="<?php echo esc_attr( $substance['id'] ); ?>">
								<span><?php echo esc_html( $substance['title'] ); ?></span>
								<button type="button" class="fab-chip__remove" aria-label="Remover substância">×</button>
								<input type="hidden" name="fab_substance_payload[]" value="<?php echo esc_attr( wp_json_encode( array( 'display_name' => $substance['title'], 'insumo' => isset( $substance['meta']['insumo'] ) ? $substance['meta']['insumo'] : '', 'dcb' => isset( $substance['meta']['dcb'] ) ? $substance['meta']['dcb'] : '', 'inn' => isset( $substance['meta']['inn'] ) ? $substance['meta']['inn'] : '', 'cas' => isset( $substance['meta']['cas'] ) ? $substance['meta']['cas'] : '', 'ncm' => isset( $substance['meta']['ncm'] ) ? $substance['meta']['ncm'] : '', 'cbpf' => isset( $substance['meta']['cbpf'] ) ? $substance['meta']['cbpf'] : '', 'validade' => isset( $substance['meta']['validade'] ) ? $substance['meta']['validade'] : '' ) ) ); ?>" />
							</span>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="fab-actions fab-actions--profile">
					<button class="fab-button fab-button--primary fab-button--profile-save" type="submit">Salvar fabricante</button>
					<a class="fab-button fab-button--ghost fab-button--block" href="<?php echo esc_url( home_url( '/meu-fabricante/' ) ); ?>">Atualizar página</a>
				</div>
			</form>
		<?php endif; ?>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
