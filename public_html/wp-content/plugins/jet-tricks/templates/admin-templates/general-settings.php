<div
	class="jet-tricks-settings-page jet-tricks-settings-page__general"
>
	<cx-vui-select
		name="widgets-load-level"
		label="<?php esc_attr_e( 'Editor Load Level', 'jet-tricks' ); ?>"
		description="<?php esc_attr_e( 'Choose a certain set of options in the widget’s Style tab by moving the slider, and improve your Elementor editor performance by selecting appropriate style settings fill level (from None to Full level)', 'jet-tricks' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		size="fullwidth"
		:options-list="pageOptions.widgets_load_level.options"
		v-model="pageOptions.widgets_load_level.value">
	</cx-vui-select>

    <cx-vui-select
        name="particles-version"
        label="<?php esc_attr_e( 'Particles Library Version', 'jet-tricks' ); ?>"
        description="<?php esc_attr_e( 'Choose which version of the tsParticles library to use.', 'jet-tricks' ); ?>"
        :wrapper-css="[ 'equalwidth' ]"
        size="fullwidth"
        :options-list="pageOptions.particles_version.options"
        v-model="pageOptions.particles_version.value">
    </cx-vui-select>
</div>
