<div class="jet-tabs-settings-page jet-tabs-settings-page__general">
    <cx-vui-select
        name="widgets-load-level"
        label="<?php esc_attr_e( 'Editor Load Level', 'jet-tabs' ); ?>"
        description="<?php esc_attr_e( 'Choose a certain set of options in the widget’s Style tab by moving the slider, and improve your Elementor editor performance by selecting appropriate style settings fill level (from None to Full level)', 'jet-tricks' ); ?>"
        :wrapper-css="[ 'equalwidth' ]"
        size="fullwidth"
        :options-list="pageOptions.widgets_load_level.options"
        v-model="pageOptions.widgets_load_level.value">
    </cx-vui-select>

    <cx-vui-select
        name="ajax-request-type"
        label="<?php esc_attr_e( 'Ajax Request Type', 'jet-tabs' ); ?>"
        description="<?php esc_attr_e( 'Choose the type of AJAX request.', 'jet-tabs' ); ?>"
        :wrapper-css="[ 'equalwidth' ]"
        size="fullwidth"
        :options-list="pageOptions.ajax_request_type.options"
        v-model="pageOptions.ajax_request_type.value">
    </cx-vui-select>

    <cx-vui-switcher
    name="use-content-cache"
    label="<?php esc_attr_e( 'Use Cache for Elementor Templates', 'jet-tabs' ); ?>"
    description="<?php esc_attr_e( 'Enable caching for Elementor templates used in tab content to increase page rendering speed.', 'jet-tabs' ); ?>"
    :wrapper-css="[ 'equalwidth' ]"
    :return-true="true"
    :return-false="false"
    v-model="pageOptions.useTemplateCache.enable">
    </cx-vui-switcher>

    <cx-vui-select
        v-if="pageOptions.useTemplateCache.enable"
        name="cache-expiration"
        label="<?php esc_attr_e( 'Cache Expiration', 'jet-tabs' ); ?>"
        description="<?php esc_attr_e( 'Select a timeout for content caching. Select <b>None</b> for permanent cache. Changing this option will clear the content cache of all tabs', 'jet-tabs' ); ?>"
        :wrapper-css="[ 'equalwidth' ]"
        size="fullwidth"
        :options-list="cacheTimeoutOptions"
        v-model="pageOptions.useTemplateCache.cacheExpiration">
    </cx-vui-select>

    <div class="cx-vui-component cx-vui-component--equalwidth" v-if="pageOptions.useTemplateCache.enable">
        <div class="cx-vui-component__meta">
            <label class="cx-vui-component__label" for="cx_use-content-cache"><?php wp_kses_post( __( 'Clear Cache', 'jet-tabs' ) ); ?></label>
            <div class="cx-vui-component__desc"><?php echo wp_kses_post( __( 'Clear the templates cache for all tabs', 'jet-tabs' ) ); ?></div>
        </div>
        <div class="cx-vui-component__control">
            <cx-vui-button
                button-style="accent-border"
                size="mini"
                :loading="clearCacheStatus"
                @click="clearCache"
            >
                <span slot="label"><?php esc_html_e( 'Clear Cache', 'jet-tabs' ); ?></span>
            </cx-vui-button>
        </div>
    </div>
</div>