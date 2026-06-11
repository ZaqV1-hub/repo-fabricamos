'use strict';

let jetTabsSettinsMixin = {
	data: function() {
		return {
			pageOptions: window.jetTabsSettingsConfig.settingsData,
			preparedOptions: {},
			savingStatus: false,
			ajaxSaveHandler: null,
		};
	},

	watch: {
		pageOptions: {
			handler( options ) {
				let prepared = {};
				for ( let option in options ) {
					if ( options.hasOwnProperty( option ) ) {
						if ( options[option] && typeof options[option] === 'object' && 'value' in options[option] ) {
							prepared[option] = options[option]['value'];
						} else {
							prepared[option] = options[option];
						}
					}
				}
				this.preparedOptions = prepared;
				this.saveOptions();
			},
			deep: true
		}
	},

	methods: {
		saveOptions: function() {
			var self = this;

			self.savingStatus = true;

			wp.apiFetch( {
				method: 'post',
				path: window.jetTabsSettingsConfig.settingsApiUrl,
				data: self.preparedOptions
			} ).then( function( response ) {

				self.savingStatus = false;

				if ( 'success' === response.status ) {
					self.$CXNotice.add( {
						message: response.message,
						type: 'success',
						duration: 3000,
					} );
				}

				if ( 'error' === response.status ) {
					self.$CXNotice.add( {
						message: response.message,
						type: 'error',
						duration: 3000,
					} );
				}
				
			} ).catch( function( response ) {
				self.$CXNotice.add( {
					message: response.message,
					type: 'error',
					duration: 3000,
				} );
			} );

		},
	}
}

Vue.component( 'jet-tabs-general-settings', {
	template: '#jet-dashboard-jet-tabs-general-settings',

	mixins: [ jetTabsSettinsMixin ],

	data: function() {
		return {
			clearCacheStatus: false,
		};
	},

	computed: {
		cacheTimeoutOptions: function () {
			return window.jetTabsSettingsConfig.cacheTimeoutOptions
		}
	},

	methods: {
		clearCache: function() {
			this.clearCacheStatus = true;

			wp.apiFetch( {
				method: 'post',
				path: window.jetTabsSettingsConfig.clearTabsCachePath,
				data: {},
			} ).then( ( response ) => {
				this.clearCacheStatus = false;

				switch ( response.status ) {
					case 'success':
						this.$CXNotice.add( {
							message: response.message,
							type: 'success',
							duration: 3000,
						} );
						break;
					case 'error':
						this.$CXNotice.add( {
							message: response.message,
							type: 'error',
							duration: 3000,
						} );
						break;
				}

			} );

		},
	}

} );

Vue.component( 'jet-tabs-avaliable-addons', {
	template: '#jet-dashboard-jet-tabs-avaliable-addons',
	mixins: [ jetTabsSettinsMixin ],
} );
