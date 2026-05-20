'use strict';

Vue.component( 'jet-blog-general-settings', {

	template: '#jet-dashboard-jet-blog-general-settings',

	data: function() {
		return {
			pageOptions: window.JetBlogSettingsConfig.settingsData,
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
						prepared[ option ] = options[option]['value'];
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
				path: window.JetBlogSettingsConfig.settingsApiUrl,
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
} );

Vue.component( 'jet-blog-available-widgets', {

	template: '#jet-dashboard-jet-blog-available-widgets',

	data: function() {
		return {
			pageOptions: window.JetBlogSettingsConfig.settingsData,
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
						prepared[ option ] = options[option]['value'];
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
				path: window.JetBlogSettingsConfig.settingsApiUrl,
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
} );
