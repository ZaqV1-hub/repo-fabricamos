<?php

defined( 'ABSPATH' ) || exit;
?>
<div id="about" class="wpr-Page">
	<div class="wpr-sectionHeader">
		<h2 class="wpr-title1 wpr-icon-about"><?php esc_html_e( 'About', 'rocket' ); ?></h2>
	</div>
	<div class="wpr-Page-row">
		<div class="wpr-Page-col">
			<div class="wpr-optionHeader">
				<h3 class="wpr-title2"><?php esc_html_e( 'Responsible distribution', 'rocket' ); ?></h3>
			</div>
			<div class="wpr-fieldsContainer-fieldset">
				<div class="wpr-field">
					<p class="wpr-field-description">
						<?php
							// translators: %1$s = opening <a> tag, %2$s = closing </a> tag, %3$s = opening <a> tag, %4$s = closing </a> tag.
							printf(
								esc_html( 'This plugin is based on GPL licensed software by %1$sWP Media%2$s - the original author. We believe it is imperative to strictly comply with the terms open source licensing and to support the %3$sGPL enforcement principles%4$s.' ),
								'<a href="https://github.com/wp-media/wp-rocket/blob/develop/LICENSE" target="_blank">',
								'</a>',
								'<a href="https://sfconservancy.org/copyleft-compliance/principles.html" target="_blank">',
								'</a>'
							);
						?>
						<?php
							// translators: %1$s = opening <a> tag, %2$s = closing </a> tag.
							printf(
								esc_html(
									' As required by the license of this project, you can access the full source code with our contributions %1$shere%2$s.'
								),
								'<a href="' . esc_html( WP_ROCKET_URL ) . '" target="_blank">',
								'</a>'
							);
						?>
						<?php esc_html_e( ' We encourage others to contribute to the project as we have done in our code contributions.', 'rocket' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
