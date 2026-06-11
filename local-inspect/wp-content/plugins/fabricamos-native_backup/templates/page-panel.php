<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fabricamos   = Fabricamos_Native::instance();
$is_panel     = $fabricamos->is_panel_authenticated();
$login_error  = isset( $_GET['login_error'] ) ? sanitize_text_field( wp_unslash( $_GET['login_error'] ) ) : '';
$status       = isset( $_GET[ Fabricamos_Native::QUERY_SUCCESS ] ) ? sanitize_text_field( wp_unslash( $_GET[ Fabricamos_Native::QUERY_SUCCESS ] ) ) : '';
$view         = isset( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : '';
$edit_id      = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
$page_title   = 'Painel';
$show_user    = false;
$panel_url    = home_url( '/painel/' );
$session_name = 'Painel administrativo';
$logout_url   = $fabricamos->logout_url( 'panel' );

include __DIR__ . '/partials/page-start.php';
?>
<section class="fab-page fab-page--panel<?php echo ! $is_panel ? ' fab-page--login' : ''; ?>">
	<div class="fab-container-wide">
		<?php if ( $is_panel ) : ?>
			<div class="fab-session-chip" aria-label="Sessão do painel">
				<div class="fab-session-chip__avatar" aria-hidden="true"></div>
				<div class="fab-session-chip__meta">
					<span>Olá, <strong><?php echo esc_html( $session_name ); ?></strong></span>
					<a class="fab-session-chip__logout" href="<?php echo esc_url( $logout_url ); ?>">Sair</a>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! $is_panel ) : ?>
			<div class="fab-login-wrap">
				<div class="fab-login-card">
					<div class="fab-page-intro fab-page-intro--compact">
						<span class="fab-page-kicker">Acesso isolado</span>
						<div class="fab-title-line-wrap">
							<h1 class="fab-screen-title">Painel</h1>
							<span class="fab-line"></span>
						</div>
					</div>

					<?php if ( $login_error ) : ?>
						<div class="fab-alert fab-alert--error">
							<?php
							if ( 'invalid' === $login_error ) {
								echo esc_html( 'Usuário ou senha inválidos.' );
							} elseif ( 'not_allowed' === $login_error ) {
								echo esc_html( 'Este usuário não possui acesso ao painel.' );
							} else {
								echo esc_html( 'Não foi possível autenticar a solicitação.' );
							}
							?>
						</div>
					<?php endif; ?>

					<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="fab-login-form" autocomplete="off" data-fab-exclusive-auth="panel">
						<input type="hidden" name="action" value="fabricamos_panel_login" />
						<?php wp_nonce_field( 'fabricamos_panel_login', 'fabricamos_panel_login_nonce' ); ?>

						<label for="fab-panel-log">Nome de Usuário ou Endereço de e-mail:<span class="fab-req">*</span></label>
						<input id="fab-panel-log" class="fab-input fab-input--lg" type="text" name="fabricamos_panel_user" autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" data-lpignore="true" required />

						<label for="fab-panel-pwd">Senha:<span class="fab-req">*</span></label>
						<input id="fab-panel-pwd" class="fab-input fab-input--lg" type="password" name="fabricamos_panel_pass" autocomplete="new-password" data-lpignore="true" required />

						<label class="fab-remember">
							<input type="checkbox" name="rememberme" value="forever" />
							<span>Lembre de mim</span>
						</label>

						<button class="fab-button fab-button--ghost" type="submit">Login</button>
					</form>
				</div>
			</div>
		<?php elseif ( 'form' === $view ) : ?>
			<?php $context = $fabricamos->get_panel_form_context( $edit_id ); ?>
			<div class="fab-page-intro">
				<span class="fab-page-kicker">Painel administrativo</span>
				<div class="fab-title-line-wrap">
					<h1 class="fab-screen-title"><?php echo esc_html( $context['is_edit'] ? 'Editar fabricante' : 'Criar fabricante' ); ?></h1>
					<span class="fab-line"></span>
				</div>
			</div>

			<?php if ( 'required_fields' === $status ) : ?>
				<div class="fab-alert fab-alert--error">Preencha todos os campos obrigatórios para salvar o fabricante.</div>
			<?php elseif ( 'missing_substances' === $status ) : ?>
				<div class="fab-alert fab-alert--error">Selecione ao menos uma substância.</div>
			<?php elseif ( 'panel_missing_title' === $status ) : ?>
				<div class="fab-alert fab-alert--error">Informe o nome da empresa.</div>
			<?php elseif ( 'invalid_phone' === $status ) : ?>
				<div class="fab-alert fab-alert--error">Informe um telefone válido com DDD.</div>
			<?php elseif ( 'invalid_email' === $status ) : ?>
				<div class="fab-alert fab-alert--error">Informe e-mails válidos para contato e login do fabricante.</div>
			<?php elseif ( 'panel_user_taken' === $status ) : ?>
				<div class="fab-alert fab-alert--error">O e-mail informado já está vinculado a outro fabricante.</div>
			<?php elseif ( 'error' === $status ) : ?>
				<div class="fab-alert fab-alert--error">Não foi possível salvar as alterações.</div>
			<?php endif; ?>

			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data" class="fab-profile-form fab-profile-form--editor fab-panel-form">
				<input type="hidden" name="action" value="fabricamos_panel_save" />
				<input type="hidden" name="panel_manufacturer_id" value="<?php echo esc_attr( $context['id'] ); ?>" />
				<?php wp_nonce_field( 'fabricamos_panel_save', 'fabricamos_panel_nonce' ); ?>

				<div class="fab-panel">
					<div class="fab-form-stack">
						<label class="fab-label-strong" for="panel-title">Empresa</label>
						<input id="panel-title" class="fab-input" name="panel_title" value="<?php echo esc_attr( $context['title'] ); ?>" required />
					</div>
				</div>

				<div class="fab-hero-card fab-profile-hero-card">
					<div class="fab-profile-image-editor" data-fab-image-field>
						<img
							class="fab-hero-image fab-profile-image-editor__preview<?php echo $context['has_image'] ? '' : ' is-placeholder'; ?>"
							src="<?php echo esc_attr( $context['image'] ); ?>"
							alt="<?php echo esc_attr( $context['title'] ? $context['title'] : 'Fabricante' ); ?>"
							data-fab-image-preview
							data-default-src="<?php echo esc_attr( $context['image'] ); ?>"
							data-placeholder-src="<?php echo esc_attr( $context['placeholder'] ); ?>"
						/>
						<div class="fab-profile-image-editor__actions">
							<input id="fab-panel-hero-image-file" type="file" name="fab_hero_image_file" accept="image/*" hidden data-fab-image-input />
							<input type="hidden" name="fab_hero_image_remove" value="0" data-fab-image-remove />
							<button class="fab-button fab-button--ghost" type="button" data-fab-image-trigger>Adicionar imagem</button>
							<button class="fab-button fab-button--ghost" type="button" data-fab-image-clear>Remover imagem</button>
						</div>
					</div>
				</div>

				<div class="fab-panel">
					<div class="fab-form-stack">
						<label class="fab-label-strong" for="fab-description">Descrição</label>
						<textarea id="fab-description" class="fab-textarea" rows="5" name="fab_description" required><?php echo esc_textarea( $context['description'] ); ?></textarea>
					</div>
				</div>

				<div class="fab-panel">
					<div class="fab-form-stack">
						<label class="fab-label-strong" for="fab-editor-name">Responsável pela edição</label>
						<input id="fab-editor-name" class="fab-input" name="fab_editor_name" value="<?php echo esc_attr( $context['editor_name'] ); ?>" required />
					</div>

					<div class="fab-two-cols">
						<div class="fab-form-stack">
							<label class="fab-label-strong" for="fab-editor-phone">Telefone do responsável</label>
							<input id="fab-editor-phone" class="fab-input" name="fab_editor_phone" type="tel" inputmode="tel" placeholder="(11) 4000-1000" value="<?php echo esc_attr( $context['editor_phone'] ); ?>" minlength="10" required />
						</div>
						<div class="fab-form-stack">
							<label class="fab-label-strong" for="fab-editor-email">E-mail do responsável</label>
							<input id="fab-editor-email" class="fab-input" name="fab_editor_email" type="email" value="<?php echo esc_attr( $context['editor_email'] ); ?>" />
						</div>
					</div>
				</div>

				<div class="fab-panel">
					<div class="fab-form-stack">
						<label class="fab-label-strong" for="fab-contact-name">Nome/Departamento público</label>
						<input id="fab-contact-name" class="fab-input" name="fab_contact_name" value="<?php echo esc_attr( $context['contact_name'] ); ?>" />
					</div>

					<div class="fab-two-cols">
						<div class="fab-form-stack">
							<label class="fab-label-strong" for="fab-phone">Telefone público</label>
							<input id="fab-phone" class="fab-input" name="fab_phone" type="tel" inputmode="tel" placeholder="(11) 4000-1000" value="<?php echo esc_attr( $context['phone'] ); ?>" />
						</div>
						<div class="fab-form-stack">
							<label class="fab-label-strong" for="fab-email">E-mail público</label>
							<input id="fab-email" class="fab-input" name="fab_email" type="email" value="<?php echo esc_attr( $context['email'] ); ?>" />
						</div>
					</div>

					<div class="fab-form-stack">
						<label class="fab-label-strong" for="fab-site">Site</label>
						<input id="fab-site" class="fab-input" name="fab_site" type="url" value="<?php echo esc_attr( $context['site'] ); ?>" />
					</div>
				</div>

				<div class="fab-panel">
					<div class="fab-two-cols">
						<div class="fab-form-stack">
							<label class="fab-label-strong" for="panel-login-email">Login de edição</label>
							<input id="panel-login-email" class="fab-input" name="panel_login_email" type="email" value="<?php echo esc_attr( $context['login_email'] ); ?>" required />
						</div>
						<div class="fab-form-stack">
							<label class="fab-label-strong" for="panel-login-password">Senha de edição</label>
							<input id="panel-login-password" class="fab-input" name="panel_login_password" type="text" value="" placeholder="<?php echo esc_attr( $context['is_edit'] ? 'Deixe em branco para manter' : 'Defina uma senha' ); ?>" <?php echo $context['is_edit'] ? '' : 'required'; ?> />
						</div>
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
						<?php foreach ( $context['substances'] as $substance ) : ?>
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
					<a class="fab-button fab-button--ghost fab-button--block" href="<?php echo esc_url( $panel_url ); ?>">Cancelar</a>
				</div>
			</form>
		<?php else : ?>
			<?php $context = $fabricamos->get_panel_context(); ?>
			<div class="fab-page-intro">
				<span class="fab-page-kicker">Painel administrativo</span>
				<div class="fab-title-line-wrap">
					<h1 class="fab-screen-title">Empresas</h1>
					<span class="fab-line"></span>
				</div>
				<p class="fab-page-copy">Gerencie os fabricantes em uma interface única, mantendo o mesmo padrão visual dos perfis e acessos do Fabricamos.</p>
			</div>

			<?php if ( 'panel_saved' === $status ) : ?>
				<div class="fab-alert fab-alert--success">Fabricante salvo com sucesso.</div>
			<?php elseif ( 'panel_deleted' === $status ) : ?>
				<div class="fab-alert fab-alert--success">Fabricante excluído com sucesso.</div>
			<?php elseif ( 'error' === $status ) : ?>
				<div class="fab-alert fab-alert--error">Não foi possível concluir a ação solicitada.</div>
			<?php endif; ?>

			<?php if ( 0 === (int) $context['total_rows'] ) : ?>
				<div class="fab-alert fab-alert--error">Nenhum fabricante encontrado para os filtros atuais.</div>
			<?php endif; ?>

			<div class="fab-panel fab-panel--table">
				<div class="fab-panel-toolbar">
					<form method="get" action="<?php echo esc_url( $panel_url ); ?>" class="fab-panel-search">
						<input class="fab-input" type="search" name="empresa" placeholder="Pesquisar fabricante" value="<?php echo esc_attr( $context['search'] ); ?>" />
						<button class="fab-button fab-button--primary" type="submit">Pesquisar</button>
					</form>

					<div class="fab-panel-toolbar__actions">
						<a class="fab-button fab-button--primary" href="<?php echo esc_url( $context['create_url'] ); ?>">Criar fabricante</a>
						<a class="fab-button fab-button--primary" href="<?php echo esc_url( $context['export_url'] ); ?>">Exportar Excel</a>
						<a class="fab-button fab-button--ghost" href="<?php echo esc_url( $panel_url ); ?>">Limpar filtros</a>
					</div>
				</div>

				<div class="fab-table-wrap">
					<table class="fab-panel-table">
						<thead>
							<tr>
								<th>Empresa</th>
								<th>Associado</th>
								<th>Processo</th>
								<th>Origem</th>
								<th>Insumo</th>
								<th>DCB</th>
								<th>INN</th>
								<th>CAS</th>
								<th>NCM</th>
								<th>Certificado (CBPF)</th>
								<th>Nome</th>
								<th>Telefone</th>
								<th class="fab-panel-table__email-col">Email</th>
								<th class="fab-panel-table__password-col">Senha</th>
								<th>Ações</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $context['rows'] as $row ) : ?>
								<tr>
									<td><?php echo esc_html( $row['company'] ); ?></td>
									<td><?php echo esc_html( $row['associate'] ); ?></td>
									<td><?php echo esc_html( $row['process'] ); ?></td>
									<td><?php echo esc_html( $row['origin'] ); ?></td>
									<td><?php echo esc_html( $row['substance'] ); ?></td>
									<td><?php echo esc_html( $row['dcb'] ); ?></td>
									<td><?php echo esc_html( $row['inn'] ); ?></td>
									<td><?php echo esc_html( $row['cas'] ); ?></td>
									<td><?php echo esc_html( $row['ncm'] ); ?></td>
									<td><?php echo esc_html( $row['certificate'] ); ?></td>
									<td><?php echo esc_html( $row['contact_name'] ); ?></td>
									<td><?php echo esc_html( $row['phone'] ); ?></td>
									<td class="fab-panel-table__email-col"><?php echo esc_html( $row['email'] ); ?></td>
									<td class="fab-panel-table__password-col">
										<?php if ( '-' === $row['password'] ) : ?>
											<span class="fab-password-field__value">-</span>
										<?php else : ?>
											<div class="fab-password-field">
												<span
													class="fab-password-field__value"
													data-fab-password-value
													data-masked="<?php echo esc_attr( $row['password'] ); ?>"
													data-plain="<?php echo esc_attr( $row['password_raw'] ); ?>"
												><?php echo esc_html( $row['password'] ); ?></span>
												<?php if ( '' !== $row['password_raw'] ) : ?>
													<button class="fab-password-field__toggle" type="button" data-fab-password-toggle>Exibir</button>
												<?php endif; ?>
											</div>
										<?php endif; ?>
									</td>
									<td>
										<div class="fab-table-actions">
											<a class="fab-table-action fab-table-action--edit" href="<?php echo esc_url( $row['edit_url'] ); ?>" aria-label="Editar fabricante">✎</a>
											<button
												class="fab-table-action fab-table-action--delete"
												type="button"
												aria-label="Excluir fabricante"
												data-fab-delete-open
												data-manufacturer-id="<?php echo esc_attr( $row['id'] ); ?>"
												data-manufacturer-name="<?php echo esc_attr( $row['delete_target'] ); ?>"
											>×</button>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>

							<?php for ( $i = 0; $i < (int) $context['blank_rows']; $i++ ) : ?>
								<tr class="fab-panel-table__blank" aria-hidden="true">
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
							<?php endfor; ?>
						</tbody>
					</table>
				</div>

				<div class="fab-panel-footer">
					<?php
					$total_pages  = max( 1, (int) $context['total_pages'] );
					$current_page = max( 1, (int) $context['current_page'] );
					$window_size  = 5;
					$start_page   = max( 1, $current_page - 2 );
					$end_page     = min( $total_pages, $start_page + $window_size - 1 );

					if ( ( $end_page - $start_page + 1 ) < $window_size ) {
						$start_page = max( 1, $end_page - $window_size + 1 );
					}

					$build_page_url = static function ( $page ) use ( $context, $panel_url ) {
						return add_query_arg(
							array_filter(
								array(
									'empresa' => $context['search'],
									'pagina'  => $page > 1 ? $page : null,
								),
								static function ( $value ) {
									return null !== $value && '' !== $value;
								}
							),
							$panel_url
						);
					};
					?>
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

						<form method="get" action="<?php echo esc_url( $panel_url ); ?>" class="fab-pagination-jump">
							<?php if ( '' !== $context['search'] ) : ?>
								<input type="hidden" name="empresa" value="<?php echo esc_attr( $context['search'] ); ?>" />
							<?php endif; ?>
							<select class="fab-pagination-jump__select" name="pagina" aria-label="Escolher página">
								<?php for ( $page = 1; $page <= $total_pages; $page++ ) : ?>
									<option value="<?php echo esc_attr( $page ); ?>" <?php selected( $page, $current_page ); ?>>
										<?php echo esc_html( sprintf( 'Página %d', $page ) ); ?>
									</option>
								<?php endfor; ?>
							</select>
							<button class="fab-pagination-jump__button" type="submit">Ir</button>
						</form>
					</div>
					<div class="fab-page-info">
						<?php printf( 'P&aacute;gina %1$d de %2$d', (int) $context['current_page'], (int) $context['total_pages'] ); ?>
					</div>
				</div>
			</div>

			<div class="fab-modal fab-modal--delete" data-fab-delete-modal>
				<div class="fab-modal__box">
					<h2 class="fab-modal__title">Excluir fabricante</h2>
					<p class="fab-modal__text">Tem certeza que deseja excluir <strong data-fab-delete-name></strong>? Esta ação não pode ser desfeita.</p>
					<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" class="fab-modal__actions">
						<input type="hidden" name="action" value="fabricamos_panel_delete" />
						<input type="hidden" name="panel_manufacturer_id" value="" data-fab-delete-id />
						<?php wp_nonce_field( 'fabricamos_panel_delete', 'fabricamos_panel_delete_nonce' ); ?>
						<button class="fab-button fab-button--ghost" type="button" data-fab-delete-close>Cancelar</button>
						<button class="fab-button fab-button--primary" type="submit">Excluir</button>
					</form>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php include __DIR__ . '/partials/page-end.php'; ?>
