<?php
/**
 * Add Jupiter required cutsom fields without acf.
 *
 * @package JupiterX\Framework\Admin\Custom_Fields
 * @since   3.8.0
 */

/**
 * Initializing required custom fields.
 *
 * @since 3.8.0
 */
class JupiterX_Custom_Fields_Without_Acf {
	/**
	 * Required custom fields.
	 *
	 * @var array
	 */
	private $feilds = [];

	/**
	 * Valid taxonomies to add cutsom fields.
	 *
	 * @var array
	 */
	private $valid_fields = [
		'category',
		'portfolio_category',
		'portfolio_tag',
		'post_tag',
	];

	/**
	 * JupiterX_Custom_Fields_Without_Acf constructor.
	 *
	 * @since 3.8.0
	 */
	public function __construct() {
		$this->run_user_actions();
		$this->run_taxonomy_actions();
	}

	/**
	 * Run taxonomy hooks to add and save meta boxes.
	 *
	 * @since 3.8.0
	 */
	public function run_taxonomy_actions() {
		$this->feilds = [
			'jupiterx_layout' => [
				'label' => esc_html__( 'Layout', 'jupiterx' ),
				'type' => 'select',
			],
			'jupiterx_taxonomy_order_number' => [
				'label' => esc_html__( 'Order', 'jupiterx' ),
				'type' => 'number',
			],
			'jupiterx_taxonomy_thumbnail_id' => [
				'label' => esc_html__( 'Thumbnail', 'jupiterx' ),
				'type' => 'image',
			],
		];

		foreach ( $this->valid_fields as $field ) {
			// Add meta boxes.
			add_action( "{$field}_add_form_fields", [ $this, 'add_taxonomy_fields' ] );
			add_action( "{$field}_edit_form_fields", [ $this, 'edit_taxonomy_fields' ] );

			// Save meta boxes.
			add_action( "create_{$field}", [ $this, 'save_taxonomy_fields' ] );
			add_action( "edited_{$field}", [ $this, 'save_taxonomy_fields' ] );
		}
	}

	/**
	 * Save taxonomy meta boxes.
	 *
	 * @param integer $term_id Current term id.
	 * @since 3.8.0
	 */
	public function save_taxonomy_fields( $term_id ) {
		foreach ( $this->feilds as $field => $value ) {
			if ( isset( $_POST[ $field ] ) ) { // phpcs:ignore
				update_term_meta( $term_id, $field, htmlspecialchars( $_POST[ $field ] ) ); // phpcs:ignore
				continue;
			}

			delete_term_meta( $term_id, $field );
		}
	}

	/**
	 * Add taxonomy meta boxes in edit page.
	 *
	 * @param object $taxonomy Object of current taxonomy.
	 * @since 3.8.0
	 */
	public function edit_taxonomy_fields( $taxonomy ) {
		$html = '';

		foreach ( $this->feilds as $key => $field ) {
			if ( $this->handle_tags_fields( $taxonomy->taxonomy, $key ) ) {
				continue;
			}

			$default = '';

			$html .= '<tr class="form-field term-' . esc_attr( $key ) . '-wrap jupiterx-taxonomy-edit-meta-box">';
			$html .= sprintf(
				'<th scope="row"><label for="%1$s">%2$s</label></th>',
				esc_attr( $taxonomy->taxonomy . '_' . $key ),
				esc_html( $field['label'] )
			);
			$html .= '<td>';

			$function_name = 'render_' . $field['type'];

			$default = get_term_meta( $taxonomy->term_id, $key, true );

			$html .= $this->$function_name( $key, $taxonomy->taxonomy, $default );
			$html .= '</td>';
			$html .= '</tr>';
		}

		echo $html; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Echoes HTML output.
	}

	/**
	 * Add taxonomy meta boxes in add new page.
	 *
	 * @param string $taxonomy_name Current taxonomy name.
	 * @since 3.8.0
	 */
	public function add_taxonomy_fields( $taxonomy_name ) {
		$html = '';

		foreach ( $this->feilds as $key => $field ) {
			if ( $this->handle_tags_fields( $taxonomy_name, $key ) ) {
				continue;
			}

			$html .= '<div class="form-field term-' . esc_attr( $key ) . '-wrap jupiterx-taxonomy-meta-box">';
			$html .= sprintf(
				'<label for="%1$s">%2$s</label>',
				esc_attr( $taxonomy_name . '_' . $key ),
				esc_html( $field['label'] )
			);

			$function_name = 'render_' . $field['type'];

			$html .= $this->$function_name( $key, $taxonomy_name, '' );
			$html .= '</div>';
		}

		echo $html; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Echoes HTML output.
	}

	/**
	 * Render select meta boxes.
	 *
	 * @param string $key           Meta box name.
	 * @param string $taxonomy_name Current taxonomy name.
	 * @param string $default       Meta box default value.
	 * @since 3.8.0
	 */
	public function render_select( $key, $taxonomy_name, $default ) {
		$html = sprintf(
			'<select id="%1$s" name="%2$s">',
			esc_attr( $taxonomy_name . '_' . $key ),
			esc_attr( $key )
		);

		foreach ( jupiterx_default_get_layouts() as $index => $layout ) {
			$html .= sprintf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $index ),
				$default === $index ? 'selected' : '',
				esc_html( $layout )
			);
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * Render number meta boxes.
	 *
	 * @param string $key           Meta box name.
	 * @param string $taxonomy_name Current taxonomy name.
	 * @param string $default       Meta box default value.
	 * @since 3.8.0
	 */
	public function render_number( $key, $taxonomy_name, $default ) {

		$value = ! empty( $default ) ? $default : 0;

		return sprintf(
			'<input id="%1$s" type="number" name="%2$s" min="0" max="999" value="%3$s" autocomplete="off">',
			esc_attr( $taxonomy_name . '_' . $key ),
			esc_attr( $key ),
			esc_attr( $value )
		);
	}

	/**
	 * Render image meta boxes.
	 *
	 * @param string $key           Meta box name.
	 * @param string $taxonomy_name Current taxonomy name.
	 * @param string $default       Meta box default value.
	 * @since 3.8.0
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function render_image( $key, $taxonomy_name, $default ) {
		$html = '';

		$value = ! empty( $default ) ? $default : '';

		$data_class    = empty( $value ) ? 'hide' : '';
		$control_class = ! empty( $value ) ? 'hide' : '';
		$img_src       = ! empty( $value ) ? wp_get_attachment_image_src( $value, 'medium' )[0] : '';

		$html .= sprintf(
			'<input id="%1$s" type="hidden" name="%2$s" value="%3$s" autocomplete="off">',
			esc_attr( $key ),
			esc_attr( $key ),
			esc_attr( $value )
		);

		$html .= '<div class="jupiterx-taxonomy-meta-box-thumbnail-data ' . esc_attr( $data_class ) . '">';
		$html .= '<img src="' . $img_src . '">';
		$html .= '<div class="jupiterx-taxonomy-meta-box-thumbnail-actions"><button class="jupiterx-taxonomy-meta-box-thumbnail-edit"></button><button class="jupiterx-taxonomy-meta-box-thumbnail-remove"></button></div>';
		$html .= '</div>';

		$html .= '<div class="jupiterx-taxonomy-meta-box-thumbnail ' . esc_attr( $control_class ) . '"><p>' . esc_html__( 'No image selected', 'jupiterx' ) . '</p>';
		$html .= '<a class="jupiterx-taxonomy-meta-box-thumbnail-button button" href="#">' . esc_html__( 'Add Image', 'jupiterx' ) . '</a></div>';

		return $html; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Echoes HTML output.
	}

	/**
	 * Remove order meta box for tags.
	 *
	 * @param string $name Current taxonomy name.
	 * @param string $key  Meta box name.
	 * @since 3.8.0
	 */
	public function handle_tags_fields( $name, $key ) {
		if ( ! in_array( $name, [ 'post_tag', 'portfolio_tag' ], true ) ) {
			return false;
		}

		if ( 'jupiterx_taxonomy_order_number' === $key ) {
			return true;
		}

		return false;
	}

	/**
	 * Run user hooks to add and save meta boxes.
	 *
	 * @since 3.8.0
	 */
	public function run_user_actions() {
		// Add meta boxes.
		add_action( 'show_user_profile', [ $this, 'add_user_fields' ] );
		add_action( 'edit_user_profile', [ $this, 'add_user_fields' ] );
		add_action( 'user_new_form', [ $this, 'add_user_fields' ] );

		// Save/Update meta boxes.
		add_action( 'personal_options_update', [ $this, 'update_user_fields' ] );
		add_action( 'edit_user_profile_update', [ $this, 'update_user_fields' ] );
		add_action( 'user_register', [ $this, 'update_user_fields' ] );
	}

	/**
	 * Add user meta boxes.
	 *
	 * @param object|string $user List of user data or new user parameter.
	 * @since 3.8.0
	 */
	public function add_user_fields( $user ) {
		$email    = empty( $user->ID ) ? '' : get_the_author_meta( 'jupiterx_user_email', $user->ID );
		$facebook = empty( $user->ID ) ? '' : get_the_author_meta( 'jupiterx_user_facebook', $user->ID );
		$twitter  = empty( $user->ID ) ? '' : get_the_author_meta( 'jupiterx_user_twitter', $user->ID );

		$html = '<h3>' . esc_html__( 'Social Networks', 'jupiterx' ) . '</h3>';

		$html .= '<table class="form-table jupiterx-user-custom-meta-fields"><tr>';
		// Email.
		$html .= '<tr><th><label for="jupiterx_user_email">' . esc_html__( 'Email', 'jupiterx' ) . '</label></th>';
		$html .= sprintf(
			'<td><label><input type="checkbox" name="jupiterx_user_email" autocomplete="off" %2$s><span class="message">%1$s</span></label></td></tr>',
			esc_html__( 'Show Email icon', 'jupiterx' ),
			! empty( $email ) ? 'checked="checked"' : ''
		);

		// Facebook.
		$html .= '<tr><th><label for="jupiterx_user_facebook">' . esc_html__( 'Facebook', 'jupiterx' ) . '</label></th>';
		$html .= '<td><input type="text" name="jupiterx_user_facebook" autocomplete="off" placeholder="https://www.facebook.com/username" value="' . esc_attr( $facebook ) . '"></td>';

		// Twitter.
		$html .= '<tr><th><label for="jupiterx_user_twitter">' . esc_html__( 'Twitter', 'jupiterx' ) . '</label></th>';
		$html .= '<td><input type="text" name="jupiterx_user_twitter" autocomplete="off" placeholder="https://twitter.com/username" value="' . esc_attr( $twitter ) . '"></td>';

		$html .= '</tr></table>';

		echo $html; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped -- Echoes HTML output.
	}

	/**
	 * Update user data.
	 *
	 * @param integer $user_id Current user id.
	 * @since 3.8.0
	 */
	public function update_user_fields( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$user_fields = [
			'jupiterx_user_email',
			'jupiterx_user_facebook',
			'jupiterx_user_twitter',
		];

		foreach ( $user_fields as $feild ) {
			if ( isset( $_POST[ $feild ] ) ) { // phpcs:ignore
				update_user_meta( $user_id, $feild, htmlspecialchars( $_POST[ $feild ] ) ); // phpcs:ignore
				continue;
			}

			delete_user_meta( $user_id, $feild );
		}
	}
}

new JupiterX_Custom_Fields_Without_Acf();
