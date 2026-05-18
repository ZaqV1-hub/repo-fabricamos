<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos  = Fabricamos_Native::instance();
$context     = $fabricamos->get_catalog_context( 'fabricante' );
$catalog_url = home_url( '/fabricamos-fabricante/' );
$page_title  = 'Fabricamos - Fabricante';
$show_user   = false;
$my_company  = $fabricamos->get_current_user_manufacturer();
$detail      = $my_company ? $fabricamos->get_manufacturer_detail( $my_company ) : null;
$session_name = '';
$logout_url   = $fabricamos->logout_url( 'manufacturer' );

if ( $detail ) {
	$session_name = ! empty( $detail['editor_name'] ) ? $detail['editor_name'] : $detail['contact_name'];
	$session_name = $session_name ? $session_name : $detail['title'];
}
$edit_url    = $my_company ? home_url( '/meu-fabricante/' ) : '';
$detail_text = $my_company ? 'Edite o seu cadastro e mantenha as substâncias sempre atualizadas.' : 'Seu usuário ainda não está vinculado a um fabricante publicado.';

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--catalog">
	<div class="fab-container-wide">
		<?php if ( $session_name ) : ?>
			<div class="fab-session-chip" aria-label="Sessão do fabricante">
				<div class="fab-session-chip__avatar" aria-hidden="true"></div>
				<div class="fab-session-chip__meta">
					<span>Olá, <strong><?php echo esc_html( $session_name ); ?></strong></span>
					<a class="fab-session-chip__logout" href="<?php echo esc_url( $logout_url ); ?>">Sair</a>
				</div>
			</div>
		<?php endif; ?>

		<div class="fab-grid-layout">
			<aside class="fab-sidebar">
				<div class="fab-section-title">
					<span>Filtros</span>
					<span class="fab-line"></span>
				</div>

				<form method="get" action="<?php echo esc_url( $catalog_url ); ?>" class="fab-filter-form">
					<div class="fab-field">
						<label class="fab-label-sm" for="fab-company">Empresa:</label>
						<input id="fab-company" class="fab-input" name="empresa" value="<?php echo esc_attr( $context['filters']['company'] ); ?>" placeholder="Pesquise o nome da Empresa" autocomplete="off" data-fab-company-search />
						<div class="fab-suggestions" data-fab-company-suggestions></div>
					</div>

					<div class="fab-field">
						<label class="fab-label-sm" for="fab-process">Processo:</label>
						<select id="fab-process" class="fab-input fab-select" name="processo">
							<option value="">Selecione</option>
							<?php foreach ( $context['processes'] as $process ) : ?>
								<option value="<?php echo esc_attr( $process ); ?>" <?php selected( $context['filters']['process'], $process ); ?>>
									<?php echo esc_html( $process ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="fab-field">
						<label class="fab-label-sm" for="fab-substance">Substância:</label>
						<input id="fab-substance" class="fab-input" name="substancia" value="<?php echo esc_attr( $context['filters']['substance'] ); ?>" placeholder="Pesquise o nome da Substância" autocomplete="off" data-fab-catalog-substance-search />
						<div class="fab-suggestions" data-fab-catalog-substance-suggestions></div>
					</div>

					<div class="fab-filter-actions">
						<button class="fab-button fab-button--primary fab-button--block" type="submit">Pesquisar</button>
						<a class="fab-button fab-button--ghost fab-button--block" href="<?php echo esc_url( $catalog_url ); ?>">Limpar filtros</a>
					</div>
				</form>

				<div class="fab-section-title fab-section-title--spaced">
					<span>Detalhes</span>
					<span class="fab-line"></span>
				</div>

				<div class="fab-detail-box">
					<?php echo esc_html( $detail_text ); ?>
				</div>

				<?php if ( $edit_url ) : ?>
					<a class="fab-button fab-button--primary fab-button--block" href="<?php echo esc_url( $edit_url ); ?>">Editar fabricante</a>
				<?php else : ?>
					<span class="fab-button fab-button--disabled fab-button--block">Editar fabricante</span>
				<?php endif; ?>
			</aside>

			<section class="fab-content">
				<div class="fab-section-title fab-section-title--content">
					<span>Fabricantes</span>
					<span class="fab-line"></span>
				</div>

				<?php if ( empty( $context['manufacturers'] ) ) : ?>
					<div class="fab-empty-state">
						<h2>Nenhum fabricante encontrado</h2>
						<p>Não há resultados para os filtros aplicados no momento.</p>
					</div>
				<?php else : ?>
					<?php
					$total_pages  = max( 1, (int) $context['total_pages'] );
					$current_page = max( 1, (int) $context['current_page'] );
					$start_page   = max( 1, $current_page - 2 );
					$end_page     = min( $total_pages, $start_page + 4 );

					if ( ( $end_page - $start_page + 1 ) < 5 ) {
						$start_page = max( 1, $end_page - 4 );
					}

					$build_page_url = static function ( $page ) use ( $catalog_url, $context ) {
						return add_query_arg(
							array_filter(
								array(
									'empresa'    => $context['filters']['company'],
									'processo'   => $context['filters']['process'],
									'substancia' => $context['filters']['substance'],
									'pagina'     => $page > 1 ? $page : null,
								),
								static function ( $value ) {
									return null !== $value && '' !== $value;
								}
							),
							$catalog_url
						);
					};
					?>
					<div class="fab-cards-grid">
						<?php foreach ( $context['primary_cards'] as $manufacturer ) : ?>
							<?php $card = $fabricamos->get_manufacturer_card_data( $manufacturer ); ?>
							<?php $card['url'] = $fabricamos->get_manufacturer_view_url( $manufacturer, 'fabricante' ); ?>
							<a class="fab-card" href="<?php echo esc_url( $card['url'] ); ?>">
								<img class="fab-card__image<?php echo $card['has_image'] ? '' : ' fab-card__image--placeholder'; ?>" src="<?php echo esc_attr( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" />
								<div class="fab-card__body">
									<h2 class="fab-card__title"><?php echo esc_html( $card['title'] ); ?></h2>
									<span class="fab-card__cta">Abrir detalhes</span>
								</div>
							</a>
						<?php endforeach; ?>
					</div>

					<div class="fab-footer-row fab-footer-row--catalog">
						<div class="fab-pagination">
							<?php if ( $current_page > 1 ) : ?>
								<a class="fab-pagination__item fab-pagination__item--nav" href="<?php echo esc_url( $build_page_url( $current_page - 1 ) ); ?>" aria-label="Página anterior">←</a>
							<?php else : ?>
								<span class="fab-pagination__item fab-pagination__item--nav is-disabled" aria-hidden="true">←</span>
							<?php endif; ?>

							<?php for ( $page = $start_page; $page <= $end_page; $page++ ) : ?>
								<a class="fab-pagination__item<?php echo $page === $current_page ? ' is-active' : ''; ?>" href="<?php echo esc_url( $build_page_url( $page ) ); ?>"><?php echo esc_html( $page ); ?></a>
							<?php endfor; ?>

							<?php if ( $current_page < $total_pages ) : ?>
								<a class="fab-pagination__item fab-pagination__item--nav" href="<?php echo esc_url( $build_page_url( $current_page + 1 ) ); ?>" aria-label="Próxima página">→</a>
							<?php else : ?>
								<span class="fab-pagination__item fab-pagination__item--nav is-disabled" aria-hidden="true">→</span>
							<?php endif; ?>
						</div>
						<div class="fab-page-info">
							<?php
							printf(
								'P&aacute;gina %1$d de %2$d',
								(int) $context['current_page'],
								(int) $context['total_pages']
							);
							?>
						</div>
					</div>
				<?php endif; ?>
			</section>
		</div>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
