<?php

defined( 'ABSPATH' ) || die();

$post_id           = absint( $_REQUEST['post'] ); // phpcs:ignore
$is_layout_builder = filter_input( INPUT_GET, 'layout-builder', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$section           = get_post_meta( $post_id, 'jx-layout-type', true );
$conditions        = get_post_meta( $post_id, 'jupiterx-condition-rules', true );
$topbar            = get_option( 'elementor_experiment-editor_v2', 'default' );

// Just enabled for layout builder templates.
if ( 'true' !== $is_layout_builder ) {
	return;
}

if ( empty( $section ) ) {
	$section = get_post_meta( $post_id, '_elementor_template_type', true );
}

// Take care of php warning.
if ( ! is_array( $conditions ) ) {
	$conditions = [];
}

// Putting section conditions in variables once to be used everywhere.
define( 'JX_EDITOR_WOO_CONDITIONS', JupiterX_Core_Condition_Manager::get_instance()->get_data( 'woocommerce', $section )['list'] );
define( 'JX_EDITOR_USERS_CONDITIONS', JupiterX_Core_Condition_Manager::get_instance()->get_data( 'users', $section )['list'] );
define( 'JX_EDITOR_SINGULAR_CONDITIONS', JupiterX_Core_Condition_Manager::get_instance()->get_data( 'singular', $section )['list'] );
define( 'JX_EDITOR_ARCHIVE_CONDITIONS', JupiterX_Core_Condition_Manager::get_instance()->get_data( 'archive', $section )['list'] );

// Define WPML conditions only if WPML is active
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
	define( 'JX_EDITOR_WPML_CONDITIONS', JupiterX_Core_Condition_Manager::get_instance()->get_data( 'wpml', $section )['list'] );
} else {
	define( 'JX_EDITOR_WPML_CONDITIONS', [] );
}

// Enabled conditions for header - footer - page titlebar
$second_condition = [
	''            => esc_html__( 'Select an option', 'jupiterx-core' ),
	'entire'      => esc_html__( 'Entire Site', 'jupiterx-core' ),
	'archive'     => esc_html__( 'Archive', 'jupiterx-core' ),
	'singular'    => esc_html__( 'Singular', 'jupiterx-core' ),
	'woocommerce' => esc_html__( 'Woocommerce', 'jupiterx-core' ),
	'users'       => esc_html__( 'User Attributes', 'jupiterx-core' ),
];

if ( 'singular' === $section ) {
	$second_condition = [
		''            => esc_html__( 'Select an option', 'jupiterx-core' ),
		'entire'      => esc_html__( 'Entire Site', 'jupiterx-core' ),
		'singular'    => esc_html__( 'Singular', 'jupiterx-core' ),
		'woocommerce' => esc_html__( 'Woocommerce', 'jupiterx-core' ),
		'users'       => esc_html__( 'User Attributes', 'jupiterx-core' ),
	];
}

if ( 'archive' === $section ) {
	$second_condition = [
		''            => esc_html__( 'Select an option', 'jupiterx-core' ),
		'archive'     => esc_html__( 'Archive', 'jupiterx-core' ),
		'users'       => esc_html__( 'User Attributes', 'jupiterx-core' ),
	];
}

if ( 'product' === $section || 'product-archive' === $section ) {
	$second_condition = [
		''            => esc_html__( 'Select an option', 'jupiterx-core' ),
		'woocommerce' => esc_html__( 'Woocommerce', 'jupiterx-core' ),
		'users'       => esc_html__( 'User Attributes', 'jupiterx-core' ),
	];
}

// Add WPML to second_condition array only if WPML is active
if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
	$second_condition['wpml'] = esc_html__( 'WPML', 'jupiterx-core' );
}

$first_condition = [
	'include' => esc_html__( 'Include', 'jupiterx-core' ),
	'exclude' => esc_html__( 'Exclude', 'jupiterx-core' ),
];

/**
 * Create select options.
 *
 * @since 2.5.0
 */
function jx_editor_conditions_create_select_options( $data, $condition3 ) {
	if ( empty( $data ) ) {
		$data = [];
	}

	foreach ( $data as $key => $value ) :
		$selected = ( $condition3 === $key ) ? 'selected' : '';

		if ( is_array( $value ) ) :
			echo '<optgroup label="' . esc_attr( $key ) . '">';
				foreach ( $value as $key => $val ) {
					$selected2 = ( $condition3 === $key ) ? 'selected' : '';

					$key = esc_attr( $key );
					echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected2 ) . '>' . esc_html( $val ) . '</option>';
				}
			echo '</optgroup>';
		else :
			$key = esc_attr( $key );
			echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $value ) . '</option>';
		endif;
	endforeach;
}

/**
 * Create condition row
 *
 * @since 2.5.0
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
function jx_editor_conditions_create_condition_row( $value1, $value2, $value3, $value4, $first_condition, $second_condition ) {
	$hidden3    = 'jx-condition-hide';
	$third_data = [];
	$by_child   = [ 'singular', 'archive', 'users', 'woocommerce', 'wpml' ];
	$hidden4    = '';

	// Make decision for condition select 3 and its values.
	if ( in_array( $value2, $by_child, true ) ) {
		switch ( $value2 ) :
			case 'users':
				$third_data = JX_EDITOR_USERS_CONDITIONS;
				break;
			case 'woocommerce':
				$third_data = JX_EDITOR_WOO_CONDITIONS;
				break;
			case 'singular':
				$third_data = JX_EDITOR_SINGULAR_CONDITIONS;
				break;
			case 'archive':
				$third_data = JX_EDITOR_ARCHIVE_CONDITIONS;
				break;
			case 'wpml':
				$third_data = JX_EDITOR_WPML_CONDITIONS;
				break;
		endswitch;

		$hidden3 = '';
	}

	// Make decision for condition select 4.
	$excludes = [ 'all', 'front_page', 'error_404', 'date', 'search', 'woo_search', 'all_product_archive', 'shop_archive', 'shop_manager' ];
	if ( in_array( $value3, $excludes, true ) || strpos( $value3, '_' ) === false ) {
		$hidden4 = 'jx-condition-hide';
	}

	$icon = 'eicon-plus-square';

	if ( 'exclude' === $value1 ) {
		$icon = 'eicon-minus-square';
	}

	?>
		<div class="jupiterx-editor-condition-single-row-wrapper">
			<div class="jupiterx-editor-single-row-inner-wrapper">
			<div class="jupiterx-editor-conditions-first-condition-wrapper">
				<i class="elementor-icon left-icon <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
				<select class="jx-first-condition">
					<?php
						foreach ( $first_condition as $key => $value ) {
							$selected = ( $value1 === $key ) ? 'selected' : '';

							echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $value ) . '</option>';
						}
					?>
				</select>
				<i class="elementor-icon eicon-caret-down" aria-hidden="true" ></i>
			</div>
			<div class="jupiterx-editor-conditions-second-condition-wrapper">
				<select class="jx-second-condition white-select">
					<?php
						foreach ( $second_condition as $key => $value ) {
							$selected = ( $value2 === $key ) ? 'selected' : '';

							echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $value ) . '</option>';
						}
					?>
				</select>
				<i class="elementor-icon eicon-caret-down" aria-hidden="true" ></i>
			</div>
			<div class="jupiterx-editor-conditions-third-condition-wrapper <?php echo esc_attr( $hidden3 ); ?>">
				<select class="jx-third-condition white-select">
					<?php
						if ( ! empty( $third_data ) ) :
							jx_editor_conditions_create_select_options( $third_data, $value3 );
						endif;
					?>
				</select>
				<i class="elementor-icon eicon-caret-down" aria-hidden="true" ></i>
			</div>
			<div class="jupiterx-editor-conditions-fourth-condition-wrapper <?php echo esc_attr( $hidden4 ); ?>">
				<select class="jx-fourth-condition white-select">
					<?php if ( empty( $hidden4 ) ) : ?>
						<option value="<?php echo esc_attr( $value4[0] ); ?>"><?php echo esc_html( $value4[1] ); ?></option>
					<?php endif; ?>
				</select>
				<div class="item-4th-special-select2">
					<?php if ( empty( $hidden4 ) ) : ?>
						<?php echo esc_html( $value4[1] ); ?>
					<?php endif; ?>
				</div>
				<i class="elementor-icon eicon-editor-close jx-editor-condition-clear-forth" aria-hidden="true"></i>
				<i class="elementor-icon eicon-caret-down jx-editor-condition-fourth-dropdown-icon" aria-hidden="true" ></i>
				<div class="jx-condition-search">
					<div class="value-holder">
						<input type="text" class="jx-editor-conditions-4th-search-box" >
					</div>
					<ul class="jx-condition-editor-response-list">
						<li class="jx-ec-result jx-ec-default"><?php echo esc_html__( 'Please enter 1 or more characters', 'jupiterx-core' ); ?></li>
						<li class="jx-ec-result jx-ec-default jx-ec-hidden-item"><?php echo esc_html__( 'Searching...', 'jupiterx-core' ); ?></li>
					</ul>
				</div>
			</div>
			</div>
			<div class="elementor-repeater-row-tool elementor-repeater-tool-remove jupiterx-editor-conditions-remove-row">
				<i class="eicon-close" aria-hidden="true"></i>
				<span class="elementor-screen-only"><?php echo esc_html__( 'Remove this item', 'jupiterx-core' ); ?> </span>
			</div>
			<div class="jx-editor-row-show-conflict-error"></div>
		</div>
	<?php
}

?>

<script type="text/html" id="jupiterx-editor-conditions-response-list-default-items">
	<li class="jx-ec-item jx-ec-result jx-ec-default jx-ec-default-visible"><?php echo esc_html__( 'Please enter 1 or more characters', 'jupiterx-core' ); ?></li>
	<li class="jx-ec-item jx-ec-result jx-ec-default jx-ec-hidden-item"><?php echo esc_html__( 'Searching...', 'jupiterx-core' ); ?></li>
</script>

<?php if ( in_array( $topbar, [ 'default', 'active' ], true ) ) : ?>
	<script type="text/html" id="jupiterx-editor-condition-show-conditions-button" >
		<div id="jupiterx-editor-conditions-trigger" class="elementor-panel-footer-sub-menu-item">
			<svg class="MuiSvgIcon-root" focusable="false" aria-hidden="true" viewBox="0 0 24 24"><path fill-rule="evenodd" clip-rule="evenodd" d="M11 3.75C10.3096 3.75 9.75 4.30964 9.75 5V7C9.75 7.69036 10.3096 8.25 11 8.25H13C13.6904 8.25 14.25 7.69036 14.25 7V5C14.25 4.30964 13.6904 3.75 13 3.75H11ZM12.75 9.75H13C14.5188 9.75 15.75 8.51878 15.75 7V5C15.75 3.48122 14.5188 2.25 13 2.25H11C9.48122 2.25 8.25 3.48122 8.25 5V7C8.25 8.51878 9.48122 9.75 11 9.75H11.25V11.25H8C7.27065 11.25 6.57118 11.5397 6.05546 12.0555C5.53973 12.5712 5.25 13.2707 5.25 14V14.25H5C3.48122 14.25 2.25 15.4812 2.25 17V19C2.25 20.5188 3.48122 21.75 5 21.75H7C8.51878 21.75 9.75 20.5188 9.75 19V17C9.75 15.4812 8.51878 14.25 7 14.25H6.75V14C6.75 13.6685 6.8817 13.3505 7.11612 13.1161C7.35054 12.8817 7.66848 12.75 8 12.75H16C16.3315 12.75 16.6495 12.8817 16.8839 13.1161C17.1183 13.3505 17.25 13.6685 17.25 14V14.25H17C15.4812 14.25 14.25 15.4812 14.25 17V19C14.25 20.5188 15.4812 21.75 17 21.75H19C20.5188 21.75 21.75 20.5188 21.75 19V17C21.75 15.4812 20.5188 14.25 19 14.25H18.75V14C18.75 13.2707 18.4603 12.5712 17.9445 12.0555C17.4288 11.5397 16.7293 11.25 16 11.25H12.75V9.75ZM17 15.75C16.3096 15.75 15.75 16.3096 15.75 17V19C15.75 19.6904 16.3096 20.25 17 20.25H19C19.6904 20.25 20.25 19.6904 20.25 19V17C20.25 16.3096 19.6904 15.75 19 15.75H17ZM5 15.75C4.30964 15.75 3.75 16.3096 3.75 17V19C3.75 19.6904 4.30964 20.25 5 20.25H7C7.69036 20.25 8.25 19.6904 8.25 19V17C8.25 16.3096 7.69036 15.75 7 15.75H5Z"></path></svg>
			<span class="elementor-title"><?php echo esc_html__( 'Display Conditions', 'jupiterx-core' ); ?></span>
		</div>
	</script>
<?php else : ?>
	<script type="text/html" id="jupiterx-editor-condition-show-conditions-button" >
		<div id="jupiterx-editor-conditions-trigger" class="elementor-panel-footer-sub-menu-item">
			<i class="elementor-icon eicon-flow" aria-hidden="true"></i>
			<span class="elementor-title"><?php echo esc_html__( 'Display Conditions', 'jupiterx-core' ); ?></span>
		</div>
	</script>
<?php endif; ?>

<script type="text/html" id="jupiterx-conditions-modal-header">
	<div class="dialog-header dialog-lightbox-header">
		<div class="elementor-templates-modal__header">
			<div class="elementor-templates-modal__header__logo-area"><div class="elementor-templates-modal__header__logo">
				<span class="elementor-templates-modal__header__logo__icon-wrapper e-logo-wrapper">
					<svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
						<path fill-rule="evenodd" clip-rule="evenodd" d="M8.00578 5.19137L11.6662 0H15.6374L9.98564 8L9.98563 8L8.00577 10.8086L8.00578 10.8086L4.34535 16H0.36261L6.01441 8L6.0144 8L0.36261 0H4.34535L8.00577 5.19136L8.00578 5.19137ZM9.98564 8L15.6374 16H11.6662L8.00578 10.8086L9.98564 8Z" fill="white"/>
					</svg>
				</span>
				<span class="elementor-templates-modal__header__logo__title">
					<?php echo esc_html__( 'JupiterX Conditions', 'jupiterx-core' ); ?>
				</span>
			</div>
		</div>
		<div class="elementor-templates-modal__header__menu-area"></div>
		<div class="elementor-templates-modal__header__items-area">
			<div id="jupiterx-conditions-close-modal" class="elementor-templates-modal__header__close elementor-templates-modal__header__close--normal elementor-templates-modal__header__item">
				<i class="eicon-close" aria-hidden="true" title="Close"></i>
				<span class="elementor-screen-only jupiterx-condition-modal-close">
					<?php echo esc_html__( 'Close', 'jupiterx-core' ); ?>
				</span>
			</div>
			<div id="elementor-template-library-header-tools"></div>
		</div>
	</div>
</script>

<script type="text/html" id="jupiterx-condition-modal-description">
	<div class="elementor-template-library-blank-icon">
		<i class="elementor-icon eicon-sitemap" aria-hidden="true"></i>
	</div>
	<div class="elementor-template-library-blank-title">
		<?php echo esc_Html__( 'Where do you want to display this template?', 'jupiterx-core' ); ?>
	</div>
	<div class="elementor-template-library-blank-message">
		<?php echo esc_html__( 'Set the conditions that determine where your Template is used throughout your site.', 'jupiterx-core' ); ?>
		<br>
		<?php echo esc_html__( "For example, choose 'Entire Site' to display the template across your site.", 'jupiterx-core' ); ?>
	</div>
	<div id="jupiterx-editor-conditions-list" class="jupiterx-editor-conditions-list">
		<?php
			foreach ( $conditions as $condition ) :
				jx_editor_conditions_create_condition_row( $condition['conditionA'], $condition['conditionB'], $condition['conditionC'], $condition['conditionD'], $first_condition, $second_condition );
			endforeach;
		?>
	</div>
	<div id="jupiterx-editor-condition-add-new" class="elementor-button-wrapper">
		<button class="elementor-button elementor-button-default" type="button" id="jupiterx-editor-condition-add-new-btn">
			<?php echo esc_html__( 'Add Condition', 'jupiterx-core' ); ?>
		</button>
	</div>
</script>

<script type="text/html" id="jupiterx-conditions-editor-row" >
	<?php
		jx_editor_conditions_create_condition_row( 'include', 'entire', '', '', $first_condition, $second_condition );
	?>
</script>

<script type="text/html" id="jupiterx-editor-conditions-woocommerce">
	<?php
		jx_editor_conditions_create_select_options( JX_EDITOR_WOO_CONDITIONS, 'entire-shop' );
	?>
</script>

<script type="text/html" id="jupiterx-editor-conditions-singular">
	<?php
		jx_editor_conditions_create_select_options( JX_EDITOR_SINGULAR_CONDITIONS, 'all' );
	?>
</script>

<script type="text/html" id="jupiterx-editor-conditions-archive">
	<?php
		jx_editor_conditions_create_select_options( JX_EDITOR_ARCHIVE_CONDITIONS, 'all' );
	?>
</script>

<script type="text/html" id="jupiterx-editor-conditions-users">
	<?php
		jx_editor_conditions_create_select_options( JX_EDITOR_USERS_CONDITIONS, 'all' );
	?>
</script>

<?php if ( defined( 'ICL_SITEPRESS_VERSION' ) ) : ?>
<script type="text/html" id="jupiterx-editor-conditions-wpml">
	<?php jx_editor_conditions_create_select_options( JX_EDITOR_WPML_CONDITIONS, '' ); ?>
</script>
<?php endif; ?>
