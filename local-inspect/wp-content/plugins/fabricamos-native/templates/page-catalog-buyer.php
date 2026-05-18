<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos  = Fabricamos_Native::instance();
$context     = $fabricamos->get_catalog_context( 'comprador' );
$catalog_url = home_url( '/catalogo/' );
$page_title  = 'Fabricamos';
$show_user   = false;
$has_access  = $fabricamos->has_catalog_access();
$form_error  = isset( $_GET[ Fabricamos_Native::QUERY_GUEST_ERROR ] ) ? sanitize_text_field( wp_unslash( $_GET[ Fabricamos_Native::QUERY_GUEST_ERROR ] ) ) : '';
$success_state = isset( $_GET[ Fabricamos_Native::QUERY_SUCCESS ] ) ? sanitize_text_field( wp_unslash( $_GET[ Fabricamos_Native::QUERY_SUCCESS ] ) ) : '';
$error_text  = $fabricamos->guest_access_error_message( $form_error );
$redirect_to = $fabricamos->public_current_request_url();
$login_url   = $fabricamos->site_login_url( $redirect_to );
$register_url = $fabricamos->site_register_url( $redirect_to );

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--catalog">
	<div class="fab-container-wide">
		<?php if ( 'registered' === $success_state ) : ?>
			<div class="fab-alert fab-alert--success">Cadastro concluido. Um e-mail de confirmacao foi enviado para o endereco informado.</div>
		<?php endif; ?>

		<div class="fab-gated-catalog<?php echo $has_access ? '' : ' is-locked'; ?>">
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
						<?php echo esc_html( $context['detail_text'] ); ?>
					</div>

					<a class="fab-button fab-button--announce fab-button--block" href="<?php echo esc_url( home_url( '/anunciar-fabricante/' ) ); ?>">
						Quero anunciar meu fabricante
					</a>
				</aside>

				<section class="fab-content">
					<div class="fab-section-title fab-section-title--content">
						<span>Fabricantes</span>
						<span class="fab-line"></span>
					</div>

					<?php if ( empty( $context['manufacturers'] ) ) : ?>
						<div class="fab-empty-state">
							<h2>Nenhum fabricante encontrado</h2>
							<p>Ajuste os filtros ou limpe a busca para visualizar os fabricantes publicados.</p>
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
								<?php $card['url'] = $fabricamos->get_manufacturer_view_url( $manufacturer, 'catalogo' ); ?>
								<a class="fab-card" href="<?php echo esc_url( $card['url'] ); ?>">
									<img class="fab-card__image<?php echo $card['has_image'] ? '' : ' fab-card__image--placeholder'; ?>" src="<?php echo esc_attr( $card['image'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?>" />
									<div class="fab-card__body">
										<h2 class="fab-card__title"><?php echo esc_html( $card['title'] ); ?></h2>
										<span class="fab-card__cta">Ver fabricante</span>
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

			<?php if ( ! $has_access ) : ?>
				<div class="fab-guest-gate" role="dialog" aria-modal="true" aria-labelledby="fab-guest-gate-title">
					<div class="fab-guest-gate__panel">
						<h2 id="fab-guest-gate-title" class="fab-guest-gate__title">Para continuar, é necessário preencher os dados</h2>

						<?php if ( $error_text ) : ?>
							<div class="fab-alert fab-alert--error"><?php echo esc_html( $error_text ); ?></div>
						<?php endif; ?>

						<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="fab-login-form fab-guest-gate__form">
							<input type="hidden" name="action" value="fabricamos_guest_access" />
							<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>" />
							<?php wp_nonce_field( 'fabricamos_guest_access', 'fabricamos_guest_access_nonce' ); ?>

							<label for="fab-guest-name">Nome:</label>
							<input id="fab-guest-name" class="fab-input fab-input--lg" type="text" name="fab_guest_name" required />

							<label for="fab-guest-phone">Telefone:</label>
							<input id="fab-guest-phone" class="fab-input fab-input--lg" type="tel" name="fab_guest_phone" required />

							<label for="fab-guest-email">Email:</label>
							<input id="fab-guest-email" class="fab-input fab-input--lg" type="email" name="fab_guest_email" required />

							<label for="fab-guest-company">Empresa:</label>
							<input id="fab-guest-company" class="fab-input fab-input--lg" type="text" name="fab_guest_company" required />

							<label for="fab-guest-job-title">Cargo:</label>
							<select id="fab-guest-job-title" class="fab-input fab-select fab-input--lg" name="fab_guest_job_title" required>
								<option value="">Escolha uma opção</option>
								<?php foreach ( $fabricamos->get_guest_job_title_options() as $option ) : ?>
									<option value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $option ); ?></option>
								<?php endforeach; ?>
							</select>

							<button class="fab-button fab-button--primary" type="submit">Continuar</button>
						</form>

						<p class="fab-login-alt">
							Ou crie uma conta para não precisar preencher novamente -
							<a href="<?php echo esc_url( $register_url ); ?>">Criar uma conta&gt;&gt;</a>
						</p>
						<p class="fab-login-alt">
							Já tem uma conta?
							<a href="<?php echo esc_url( $login_url ); ?>">Entrar&gt;&gt;</a>
						</p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
