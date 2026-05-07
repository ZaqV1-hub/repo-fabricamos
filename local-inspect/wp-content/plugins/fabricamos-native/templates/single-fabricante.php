<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos = Fabricamos_Native::instance();
$post       = get_queried_object();
$detail     = $fabricamos->get_manufacturer_detail( $post );
$view_context = $fabricamos->get_manufacturer_view_context();
$back_url   = $fabricamos->get_manufacturer_back_url( $view_context );
$edit_url   = $fabricamos->get_manufacturer_edit_url( $post instanceof WP_Post ? $post->ID : 0, $view_context );
$page_title = $detail['title'];
$show_user  = true;

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--narrow">
	<div class="fab-container">
		<div class="fab-head-row">
			<a class="fab-back" href="<?php echo esc_url( $back_url ); ?>" aria-label="Voltar"><span aria-hidden="true">&larr;</span></a>
			<div class="fab-title-line-wrap">
				<h1 class="fab-screen-title"><?php echo esc_html( $detail['title'] ); ?></h1>
				<span class="fab-line"></span>
			</div>
			<?php if ( $edit_url ) : ?>
				<a class="fab-button fab-button--primary fab-button--top" href="<?php echo esc_url( $edit_url ); ?>">Editar Informações</a>
			<?php endif; ?>
		</div>

		<div class="fab-hero-card">
			<img class="fab-hero-image fab-hero-image--detail<?php echo $detail['has_hero_image'] ? '' : ' fab-hero-image--placeholder'; ?>" src="<?php echo esc_attr( $detail['hero_image'] ); ?>" alt="<?php echo esc_attr( $detail['title'] ); ?>" />
		</div>

		<div class="fab-panel fab-panel--soft">
			<p class="fab-description"><?php echo esc_html( $detail['description'] ? $detail['description'] : 'Sem descrição cadastrada para este fabricante.' ); ?></p>
		</div>

		<div class="fab-panel fab-contact-panel">
			<div class="fab-contact-grid">
				<div class="fab-contact-row">
					<strong>Nome/Departamento:</strong>
					<span><?php echo esc_html( $detail['contact_name'] ); ?></span>
				</div>
				<div class="fab-contact-row">
					<strong>Telefone:</strong>
					<span><?php echo esc_html( $detail['phone'] ); ?></span>
				</div>
				<div class="fab-contact-row">
					<strong>E-mail:</strong>
					<span><?php echo esc_html( $detail['email'] ); ?></span>
				</div>
			</div>
			<?php if ( $detail['site'] ) : ?>
				<a class="fab-button fab-button--primary fab-button--detail-site" href="<?php echo esc_url( $detail['site'] ); ?>" target="_blank" rel="noopener noreferrer">Acessar o site da Empresa</a>
			<?php endif; ?>
		</div>

		<div class="fab-panel fab-substances-panel">
			<?php if ( empty( $detail['substances'] ) ) : ?>
				<p class="fab-empty-copy">Este fabricante ainda não possui substâncias vinculadas.</p>
			<?php else : ?>
				<div class="fab-substance-list">
					<?php foreach ( $detail['substances'] as $substance ) : ?>
						<?php
						$headline      = $substance['title'];
						$summary_parts = array();
						if ( ! empty( $substance['meta']['inn'] ) && 0 !== strcasecmp( $substance['meta']['inn'], $headline ) ) {
							$headline .= ' (' . $substance['meta']['inn'] . ')';
						}
						if ( ! empty( $substance['meta']['dcb'] ) && 0 !== strcasecmp( $substance['meta']['dcb'], $substance['title'] ) ) {
							$summary_parts[] = 'DCB: ' . $substance['meta']['dcb'];
						}
						if ( ! empty( $substance['meta']['inn'] ) ) {
							$summary_parts[] = 'INN: ' . $substance['meta']['inn'];
						}
						if ( ! empty( $substance['meta']['cas'] ) ) {
							$summary_parts[] = 'CAS: ' . $substance['meta']['cas'];
						}
						if ( ! empty( $substance['meta']['ncm'] ) ) {
							$summary_parts[] = 'NCM: ' . $substance['meta']['ncm'];
						}
						if ( ! empty( $substance['meta']['cbpf'] ) ) {
							$summary_parts[] = 'CBPF: ' . $substance['meta']['cbpf'];
						}
						if ( ! empty( $substance['meta']['validade'] ) ) {
							$summary_parts[] = 'Validade: ' . $substance['meta']['validade'];
						}
						$summary_text = $headline;
						if ( ! empty( $summary_parts ) ) {
							$summary_text .= ' - ' . implode( ' | ', array_unique( $summary_parts ) );
						}
						?>
						<article class="fab-substance-item">
							<button class="fab-substance-toggle" type="button" aria-expanded="false">
								<span><?php echo esc_html( $substance['title'] ); ?></span>
								<span class="fab-substance-chevron" aria-hidden="true"></span>
							</button>
							<div class="fab-substance-content" hidden>
								<?php if ( $summary_text ) : ?>
									<p class="fab-substance-summary"><?php echo esc_html( $summary_text ); ?></p>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
