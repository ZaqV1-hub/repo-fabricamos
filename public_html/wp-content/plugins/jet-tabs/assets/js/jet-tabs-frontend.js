// Promise polyfill
!function(e,n){"object"==typeof exports&&"undefined"!=typeof module?n():"function"==typeof define&&define.amd?define(n):n()}(0,function(){"use strict";function e(e){var n=this.constructor;return this.then(function(t){return n.resolve(e()).then(function(){return t})},function(t){return n.resolve(e()).then(function(){return n.reject(t)})})}function n(e){return!(!e||"undefined"==typeof e.length)}function t(){}function o(e){if(!(this instanceof o))throw new TypeError("Promises must be constructed via new");if("function"!=typeof e)throw new TypeError("not a function");this._state=0,this._handled=!1,this._value=undefined,this._deferreds=[],c(e,this)}function r(e,n){for(;3===e._state;)e=e._value;0!==e._state?(e._handled=!0,o._immediateFn(function(){var t=1===e._state?n.onFulfilled:n.onRejected;if(null!==t){var o;try{o=t(e._value)}catch(r){return void f(n.promise,r)}i(n.promise,o)}else(1===e._state?i:f)(n.promise,e._value)})):e._deferreds.push(n)}function i(e,n){try{if(n===e)throw new TypeError("A promise cannot be resolved with itself.");if(n&&("object"==typeof n||"function"==typeof n)){var t=n.then;if(n instanceof o)return e._state=3,e._value=n,void u(e);if("function"==typeof t)return void c(function(e,n){return function(){e.apply(n,arguments)}}(t,n),e)}e._state=1,e._value=n,u(e)}catch(r){f(e,r)}}function f(e,n){e._state=2,e._value=n,u(e)}function u(e){2===e._state&&0===e._deferreds.length&&o._immediateFn(function(){e._handled||o._unhandledRejectionFn(e._value)});for(var n=0,t=e._deferreds.length;t>n;n++)r(e,e._deferreds[n]);e._deferreds=null}function c(e,n){var t=!1;try{e(function(e){t||(t=!0,i(n,e))},function(e){t||(t=!0,f(n,e))})}catch(o){if(t)return;t=!0,f(n,o)}}var a=setTimeout;o.prototype["catch"]=function(e){return this.then(null,e)},o.prototype.then=function(e,n){var o=new this.constructor(t);return r(this,new function(e,n,t){this.onFulfilled="function"==typeof e?e:null,this.onRejected="function"==typeof n?n:null,this.promise=t}(e,n,o)),o},o.prototype["finally"]=e,o.all=function(e){return new o(function(t,o){function r(e,n){try{if(n&&("object"==typeof n||"function"==typeof n)){var u=n.then;if("function"==typeof u)return void u.call(n,function(n){r(e,n)},o)}i[e]=n,0==--f&&t(i)}catch(c){o(c)}}if(!n(e))return o(new TypeError("Promise.all accepts an array"));var i=Array.prototype.slice.call(e);if(0===i.length)return t([]);for(var f=i.length,u=0;i.length>u;u++)r(u,i[u])})},o.resolve=function(e){return e&&"object"==typeof e&&e.constructor===o?e:new o(function(n){n(e)})},o.reject=function(e){return new o(function(n,t){t(e)})},o.race=function(e){return new o(function(t,r){if(!n(e))return r(new TypeError("Promise.race accepts an array"));for(var i=0,f=e.length;f>i;i++)o.resolve(e[i]).then(t,r)})},o._immediateFn="function"==typeof setImmediate&&function(e){setImmediate(e)}||function(e){a(e,0)},o._unhandledRejectionFn=function(e){void 0!==console&&console&&console.warn("Possible Unhandled Promise Rejection:",e)};var l=function(){if("undefined"!=typeof self)return self;if("undefined"!=typeof window)return window;if("undefined"!=typeof global)return global;throw Error("unable to locate global object")}();"Promise"in l?l.Promise.prototype["finally"]||(l.Promise.prototype["finally"]=e):l.Promise=o});

( function( $, elementor, settings ) {

	'use strict';

	var JetTabs = {

		addedScripts: {},

		addedStyles: {},

		addedAssetsPromises: [],

		init: function() {
			var widgets = {
				'jet-tabs.default': JetTabs.tabsInit,
				'jet-accordion.default': JetTabs.accordionInit,
				'jet-image-accordion.default': JetTabs.imageAccordionInit,
				'jet-switcher.default': JetTabs.switcherInit,
			};

			$.each( widgets, function( widget, callback ) {
				elementor.hooks.addAction( 'frontend/element_ready/' + widget, callback );
			});

			// Add an action when the Swiper Loop Carousel widget is ready on the frontend
			elementorFrontend.hooks.addAction('frontend/element_ready/loop-carousel.post', function($scope, $) {

				$(window).on('load', function() {

					var loopCarousel = $scope.find('.swiper'),
					swiperInstance = loopCarousel.data( 'swiper' ),
					$toggle  = $scope.find( '.jet-toggle__control'),
					$switcher  = $scope.find( '.jet-switcher__control-instance');

					if( swiperInstance && ($toggle || $switcher) ) {

						swiperInstance.on('slideChange', function () {
							$toggle.off('click.jetAccordion');
							$switcher.off('click.jetSwitcher');
							JetTabs.initLoopCarouselHandlers( $scope );
						});
					}
				});
			});
		},

		initLoopCarouselHandlers: function( $selector ) {
			$selector.find( '.elementor-widget-jet-accordion, .elementor-widget-jet-switcher' ).each( function() {

				var $this  = $( this ),

				elementType = $this.data( 'element_type' );

				if ( !elementType ) {
					return;
				}

				if ( 'widget' === elementType ) {
					elementType = $this.data( 'widget_type' );
					window.elementorFrontend.hooks.doAction( 'frontend/element_ready/widget', $this, $ );
				}

				window.elementorFrontend.hooks.doAction( 'frontend/element_ready/global', $this, $ );
				window.elementorFrontend.hooks.doAction( 'frontend/element_ready/' + elementType, $this, $ );


			} );
		},

		tabsInit: async function( $scope ) {
			var $target         = $( '.jet-tabs', $scope ).first(),
				$widgetId       = $target.data( 'id' ),
				$window         = $( window ),
				$controlWrapper = $( '.jet-tabs__control-wrapper', $target ).first(),
				$contentWrapper = $( '.jet-tabs__content-wrapper', $target ).first(),
				$controlList    = $( '.jet-tabs__control', $controlWrapper ),
				$contentList    = $( '> .jet-tabs__content', $contentWrapper ),
				settings        = $.extend( $target.data( 'settings' ) || {}, JetTabs.getElementorElementSettings( $scope ) ),
				anchorSelectors = [],
				toogleEvents    = 'mouseenter mouseleave',
				scrollOffset,
				autoSwitchInterval = null,
				resumeTimeout   = null,
				curentHash      = window.location.hash || false,
				tabsArray       = curentHash ? curentHash.replace( '#', '' ).split( '&' ) : false,
				$tabsPosition = settings['tabsPosition'],
				$tabsPositionClassList = [],
				$tabsPositionBreakpoints = [],
				prevDevice,
				currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),
				activeBreakpoints = elementor.config.responsive.activeBreakpoints,
				swiperSettings 	  = $target.data( 'swiper-settings' ) || {},
				swiper;

			JetTabs.prepareVideoIframes( $contentWrapper );
			JetTabs.observeVideoIframes( $contentWrapper );

			function syncSwiperToIndex( index, immediate = false ) {
				if ( ! swiper ) return;
				try {
					swiper.slideToLoop( index, immediate ? 0 : swiperOptions.speed, true );
				} catch ( e ) {
				}
			}

			prevDevice = 'desktop';

			$tabsPositionBreakpoints['desktop'] = '' != settings['tabs_position'] ? settings['tabs_position'] : 'top';
			$tabsPositionClassList['desktop']   = "jet-tabs-position-" + $tabsPositionBreakpoints['desktop'];

			Object.keys( activeBreakpoints ).reverse().forEach( function( breakpointName ) {

				if ( 'widescreen' === breakpointName ) {
					$tabsPositionBreakpoints[breakpointName] = ( settings['tabs_position_' + breakpointName] && '' != settings['tabs_position_' + breakpointName] ) ? settings['tabs_position_' + breakpointName] : 'top';
					$tabsPositionClassList[breakpointName]   =  "jet-tabs-position-" + $tabsPositionBreakpoints[breakpointName];
				} else {
					$tabsPositionBreakpoints[breakpointName] = ( settings['tabs_position_' + breakpointName] && '' != settings['tabs_position_' + breakpointName] ) ? settings['tabs_position_' + breakpointName] : $tabsPositionBreakpoints[prevDevice];
					$tabsPositionClassList[breakpointName]   = "jet-tabs-position-" + $tabsPositionBreakpoints[breakpointName];

					prevDevice = breakpointName;
				}
			} );

			if ( !$target.hasClass( $tabsPositionClassList[currentDeviceMode] ) ) {
				for ( const [key, value] of Object.entries( $tabsPositionClassList ) ) {
					$target.removeClass( value );
				}

				$target.addClass( $tabsPositionClassList[currentDeviceMode] );
			}

			if ( 'click' === settings['event'] ) {
				addClickEvent();
			} else {
				addMouseEvent();
			}

			$window.load(function() {

				var currentActiveContent = $contentList.eq( [settings['activeIndex']] ),
					currentActiveContentHeight = currentActiveContent.outerHeight( true );

				if ( 'yes' != settings['no_active_tabs'] ) {
					currentActiveContentHeight += parseInt( $contentWrapper.css( 'border-top-width' ) ) + parseInt( $contentWrapper.css( 'border-bottom-width' ) );
					$contentWrapper.css( 'min-height', currentActiveContentHeight );
				}

			});

			if ( 'left' !== $tabsPositionBreakpoints[currentDeviceMode] && 'right' !== $tabsPositionBreakpoints[currentDeviceMode] ) {
				var observerConfig = {childList: true, subtree: true },
					observerTarget = $( ".jet-tabs__content.active-content", $scope );

				if ( observerTarget[0] ) {
					var observerCallback = ( mutationList, observer ) => {
						for ( var mutation of mutationList ) {
							if ( mutation.type === 'childList' ) {
								observerTarget.closest( '.jet-tabs__content-wrapper' ).css( 'min-height', 'auto' );

								var activeContentHeight = observerTarget.outerHeight( true );

								activeContentHeight += parseInt( observerTarget.css( 'border-top-width' ) ) + parseInt( observerTarget.css( 'border-bottom-width' ) );
								observerTarget.closest( '.jet-tabs__content-wrapper' ).css( 'min-height', activeContentHeight );
							}
						}
					};

					var observer = new MutationObserver( observerCallback );

					observer.observe( observerTarget[0], observerConfig );
				}
			}

			const swiperOptions = {
				slidesPerView: 'auto',
				centeredSlides: swiperSettings.centeredSlides || false,
				loop: swiperSettings.loop || false,
				loopFillGroupWithBlank: true,
				speed: 300,
				navigation: {
					nextEl: $target.find('.swiper-button-next')[0],
					prevEl: $target.find('.swiper-button-prev')[0],
				},
				keyboard: true,
				allowTouchMove: true,
				slideToClickedSlide: false,
				autoplay: settings['autoSwitch'] ? {
					delay: settings['autoSwitchDelay'],
					disableOnInteraction: false,
					stopOnLastSlide: true,
				} : false,
			};

			const $swiper     = $scope.find( '.jet-tabs-swiper' ),
				adjustedDelay = settings['autoSwitchDelay'] ? +settings['autoSwitchDelay'] + ( $swiper.length ? swiperOptions.speed : 0 ) : 0;

			function scheduleAutoSwitch( fromIndex ) {
				if ( ! settings[ 'autoSwitch' ] ) return;

				var controlListLength = $controlList.length;

				clearInterval( autoSwitchInterval );
				if ( resumeTimeout ) clearTimeout( resumeTimeout );

				var nextIndex = ( typeof fromIndex === 'number' )
					? fromIndex
					: ( settings[ 'activeIndex' ] === -1 ? -1 : settings[ 'activeIndex' ] );

				autoSwitchInterval = setInterval( function() {
					nextIndex = ( nextIndex + 1 + controlListLength ) % controlListLength;

					if ( settings[ 'ajaxTemplate' ] ) {
						ajaxLoadTemplate( nextIndex );
					}
					switchTab( nextIndex );
					if ( $swiper.length ) {
						syncSwiperToIndex( nextIndex );
						const $swiperContainer = $swiper.find( '.jet-tabs-swiper-container' );
						$swiperContainer.find( '.swiper-slide' ).removeClass( 'active-tab' );
						$swiperContainer.find( '.swiper-slide[data-swiper-slide-index="' + nextIndex + '"]' ).addClass( 'active-tab' );
					}
				}, adjustedDelay );
			}

			function resumeAutoSwitch( fromIndex ) {
				if ( ! settings[ 'autoSwitch' ] ) return;

				clearInterval( autoSwitchInterval );
				if ( resumeTimeout ) clearTimeout( resumeTimeout );

				resumeTimeout = setTimeout( function() {
					scheduleAutoSwitch( fromIndex );
				}, adjustedDelay );
			}

			function stopAutoplay() {
				if ( swiper.autoplay && swiper.autoplay.running ) {
					swiper.autoplay.stop();
					clearInterval( autoSwitchInterval );
				}
			}

			if ( $swiper.length ) {
				const $swiperContainer = $swiper.find( '.jet-tabs-swiper-container' );

				if ( swiperSettings.slidesPerView === 'fixed' ) {
					$swiperContainer.find( '.swiper-slide' ).css( 'width', swiperSettings.itemWidth );
				}

				if ( $swiperContainer.length && $swiperContainer.hasClass( 'swiper' ) ) {
					swiper = await new window.elementorFrontend.utils.swiper( $swiperContainer[0], swiperOptions );

					const formFieldSelector = [
						'.jet-tabs__content-wrapper input',
						'.jet-tabs__content-wrapper textarea',
						'.jet-tabs__content-wrapper select',
						'.jet-tabs__content-wrapper [contenteditable="true"]',
					].join(', ');

					$target.on('keydown.jetTabsSwiperFix', formFieldSelector, function( e ) {
						var code = e.which || e.keyCode;

						if ( code === 37 || code === 38 || code === 39 || code === 40 ) {
							e.stopPropagation();
						}
					});

					const initialIndex = settings['activeIndex'] === -1 ? 0 : settings['activeIndex'];

					swiper.slideToLoop( initialIndex, 0, false );

					if ( settings['autoSwitch'] && settings['activeIndex'] === -1 ) {
						swiper.autoplay.stop();
						setTimeout( () => swiper.autoplay.start(), adjustedDelay );
					}

					swiper.on( 'reachEnd', function () {

						if ( settings['autoSwitch'] && swiper.isEnd && ! swiperSettings.loop ) {
							const lastSlide = $swiperContainer.find( '.swiper-slide' ).last()[0];

							const observer = new MutationObserver( ( mutationsList ) => {

								for ( const mutation of mutationsList ) {
									if ( mutation.type === 'attributes' && lastSlide.classList.contains('active-tab') ) {
										observer.disconnect();

										setTimeout( () => {
											swiper.slideTo( 0, swiperOptions.speed, true );
											swiper.autoplay.start();
										}, adjustedDelay );

									}
								}

							});

							observer.observe( lastSlide, { attributes: true, attributeFilter: ['class'] } );
						}

					});

					function handleTabSwitch( eventSlide ) {
						const realIndex = parseInt( eventSlide.getAttribute( 'data-swiper-slide-index' ), 10 );

						$swiperContainer.find('.swiper-slide.active-tab').removeClass('active-tab');
						$( eventSlide ).addClass('active-tab');

						switchTab( realIndex );
						stopAutoplay();
						clearInterval( autoSwitchInterval );
					}

					function addHoverAndTouchEvents() {
						const $allSlides = $swiperContainer.find( '.swiper-slide' );

						$allSlides.off( 'mouseenter touchend' );

						$allSlides.on( 'mouseenter', function () {
							stopAutoplay();
							handleTabSwitch( this );
						});

						$allSlides.on( 'touchend', function () {

							if ( scrollOffset !== $( window ).scrollTop() ) {
								return false;
							}

							handleTabSwitch( this );
						});
					}

					if ( swiperSettings.loop ) {
						if ( settings['event'] === 'click' ) {
							swiper.on( 'click', function () {

								if ( swiper.clickedSlide ) {
									handleTabSwitch( swiper.clickedSlide) ;
								}

							});
						} else if ( settings['event'] === 'hover' ) {
							addHoverAndTouchEvents();

							swiper.on( 'transitionEnd', function () {
								addHoverAndTouchEvents();
							});
						}
					}

					$swiperContainer.on( 'click', function() {
						stopAutoplay();
						clearInterval( autoSwitchInterval );
					});

					if ( settings['event'] === 'hover' ) {
						$swiperContainer.on('mouseenter', function () {
							stopAutoplay();
						});
					}

					swiper.on( 'autoplay', function () {
						$swiperContainer.find('.swiper-slide').removeClass('active-tab');

						const currentIndex = swiper.realIndex;
						$swiperContainer.find('.swiper-slide[data-swiper-slide-index="' + currentIndex + '"]').addClass('active-tab');
					});

					swiper.on( 'navigationPrev', stopAutoplay );
					swiper.on( 'navigationNext', stopAutoplay );
					swiper.on( 'touchStart', stopAutoplay );
				}
			}

			if ( settings[ 'autoSwitch' ] ) {
				var currentIndex   = -1;
				autoSwitchInterval = setInterval( function() {
					var $visibleControls = $controlWrapper.find( '.jet-tabs__control:visible' );
					var len              = $visibleControls.length;

					if ( ! len ) return;

					currentIndex = ( currentIndex + 1 ) % len;

					var $activeControl = $visibleControls.eq( currentIndex );
					var tabId          = parseInt( $activeControl.data( 'tab' ), 10 ) - 1;

					if ( settings[ 'ajaxTemplate' ] && $activeControl.data( 'template-id' ) ) {
						ajaxLoadTemplate( tabId );
					}

					switchTab( tabId );

				}, adjustedDelay );

			}

			if ( settings['ajaxTemplate'] ) {
				ajaxLoadTemplate( settings['activeIndex'] );
			}

			$( window ).on( 'resize.jetTabs orientationchange.jetTabs', JetTabs.debounce( 50, function() {
				currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

				for ( const [key, value] of Object.entries( $tabsPositionClassList ) ) {
					$target.removeClass( value );
				}

				$target.addClass( $tabsPositionClassList[currentDeviceMode] );
			} ) );

			/**
			 * [addClickEvent description]
			 */
			function addClickEvent() {
				$controlList.on( 'click.jetTabs', function() {
					var $this = $( this ),
						tabId = +$this.data( 'tab' ) - 1,
						templateId = $this.data( 'template-id' );

					clearInterval( autoSwitchInterval );

					if ( settings['ajaxTemplate'] && templateId ) {
						ajaxLoadTemplate( tabId );
					}

					switchTab( tabId );
					resumeAutoSwitch( tabId );
				});
			}

			/**
			 * [addMouseEvent description]
			 */
			function addMouseEvent() {
				if ( 'ontouchend' in window || 'ontouchstart' in window ) {
					$controlList.on( 'touchstart', function( event ) {
						scrollOffset = $( window ).scrollTop();
					} );

					$controlList.on( 'touchend', function( event ) {
						var $this = $( this ),
							tabId = +$this.data( 'tab' ) - 1,
							templateId = $this.data( 'template-id' );

						if ( scrollOffset !== $( window ).scrollTop() ) {
							return false;
						}

						clearInterval( autoSwitchInterval );

						if ( settings['ajaxTemplate'] && templateId ) {
							ajaxLoadTemplate( tabId );
						}

						switchTab( tabId );
						if ( $swiper.length ) syncSwiperToIndex( tabId );
						resumeAutoSwitch( tabId );
					} );

				} else {
					$controlList.on( 'mouseenter', function( event ) {
						var $this = $( this ),
							tabId = +$this.data( 'tab' ) - 1,
							templateId = $this.data( 'template-id' );

						clearInterval( autoSwitchInterval );

						if ( settings['ajaxTemplate'] && templateId ) {
							ajaxLoadTemplate( tabId );
						}

						switchTab( tabId );
					} );
				}
			}

			$( '.jet-tabs__control', $scope ).keydown( function( e ) {
				var $this  = $( this ),
					$which = e.which || e.keyCode;

					if ( $which == 13 || $which == 32 ) {
						if ( !$this.hasClass( 'active-tab' ) ) {
							$this.click();
							return false;
						}
					}

					if ( $which == 37 ) {
						var prevTabId  = $this.prev().data('tab'),
							templateId = $this.prev().data( 'template-id' );

						if ( undefined != prevTabId ) {
							clearInterval( autoSwitchInterval );

							if ( settings['ajaxTemplate'] && templateId ) {
								ajaxLoadTemplate( prevTabId - 1 );
							}

							switchTab( prevTabId - 1);
							$this.prev().focus();
						} else {
							$this.focus();
						}
					}

					if ( $which == 39 ) {
						var nextTabId  = $this.next().data('tab'),
							templateId = $this.next().data( 'template-id' );

						if ( undefined != nextTabId ) {
							clearInterval( autoSwitchInterval );

							if ( settings['ajaxTemplate'] && templateId ) {
								ajaxLoadTemplate( nextTabId - 1 );
							}

							switchTab( nextTabId - 1 );
							$this.next().focus();
						} else {
							$this.focus();
						}
					}
			} );

			/**
			 * [switchTab description]
			 * @param  {[type]} curentIndex [description]
			 * @return {[type]}             [description]
			 */
			function switchTab( curentIndex ) {
				var activeContentHeight   = 'auto',
					timer,
					$controlWrapperHeight = $controlWrapper.outerHeight( true ),
					currentDeviceMode     = elementorFrontend.getCurrentDeviceMode(),
					controlsHeight        = 0;

				$controlList = $controlWrapper.find( '.jet-tabs__control' );
				$contentList = $contentWrapper.children( '.jet-tabs__content' );

				var $prevActiveContent = $contentWrapper.children( '.jet-tabs__content.active-content' );
				JetTabs.pauseMediaInContainer( $prevActiveContent );

				var $activeControl = $controlList.filter( '[data-tab="' + ( curentIndex + 1 ) + '"]' ).first();

				if ( ! $activeControl.length ) {
					$activeControl = $controlList.eq( curentIndex );
				}

				var controlId      = $activeControl.attr( 'id' );
				var $activeContent = controlId
					? $contentList.filter( '[aria-labelledby="' + controlId + '"]' )
					: $();

				if ( ! $activeContent.length ) {
					$activeContent = $contentList.filter( '[data-tab="' + ( curentIndex + 1 ) + '"]' );
				}
				if ( ! $activeContent.length ) {
					$activeContent = $contentList.eq( curentIndex );
				}

				$controlList.removeClass( 'active-tab' ).attr( 'aria-expanded', 'false' );
				$contentList.removeClass( 'active-content' ).attr( 'aria-hidden', 'true' );

				$activeControl.addClass( 'active-tab' ).attr( 'aria-expanded', 'true' );
				$activeContent.addClass( 'active-content' ).attr( 'aria-hidden', 'false' );
				JetTabs.restoreMediaInContainer( $activeContent );


				if ( $controlWrapper.css( 'align-self' ) === 'stretch' ) {
					$( '.jet-tabs__control', $controlWrapper ).each( function() {
						controlsHeight += $( this ).outerHeight( true );
					} );
					$controlWrapperHeight = controlsHeight;
				}

				activeContentHeight = $activeContent.outerHeight( true );
				activeContentHeight += parseInt( $contentWrapper.css( 'border-top-width' ) ) + parseInt( $contentWrapper.css( 'border-bottom-width' ) );

				if ( 'left' === $tabsPositionBreakpoints[ currentDeviceMode ] || 'right' === $tabsPositionBreakpoints[ currentDeviceMode ] ) {
					if ( activeContentHeight < $controlWrapperHeight ) {
						$target.css( { 'min-height': 'auto' } );
						$contentWrapper.css( { 'min-height': $controlWrapperHeight } );
						$target.css( { 'min-height': $controlWrapperHeight } );
					} else if ( activeContentHeight < $contentWrapper.outerHeight( true ) ) {
						$contentWrapper.css( { 'min-height': activeContentHeight } );
						$target.css( { 'min-height': activeContentHeight } );
					}
				} else {
					$contentWrapper.css( { 'min-height': activeContentHeight } );

					var observerConfig = { childList: true, subtree: true },
						observerTarget = $contentWrapper;

					if ( observerTarget[ 0 ] ) {
						var observerCallback = ( mutationList, observer ) => {
							for ( var mutation of mutationList ) {
								if ( mutation.type === 'childList' ) {
									activeContentHeight = $activeContent.outerHeight( true );
									activeContentHeight += parseInt( $contentWrapper.css( 'border-top-width' ) ) + parseInt( $contentWrapper.css( 'border-bottom-width' ) );
									$contentWrapper.css( { 'min-height': activeContentHeight } );
								}
							}
						};

						var observer = new MutationObserver( observerCallback );

						observer.observe( observerTarget[ 0 ], observerConfig );
					}
				}

				$window.trigger( 'jet-tabs/tabs/show-tab-event/before', {
					target: $target,
					tabIndex: curentIndex,
				} );

				if ( timer ) {
					clearTimeout( timer );
				}

				timer = setTimeout( function() {
					$window.trigger( 'jet-tabs/tabs/show-tab-event/after', {
						target: $target,
						tabIndex: curentIndex,
					} );

					if ( true === settings['switchScrolling'] ) {
						$( 'html, body' ).animate( {
							scrollTop: $contentWrapper.offset().top - settings['switchScrollingOffset']['size']
						}, 300 );
					}
				}, 500 );
			}

			/**
			 * [ajaxLoadTemplate description]
			 * @param  {[type]} $index [description]
			 * @return {[type]}        [description]
			 */
			function ajaxLoadTemplate( $index ) {
				var $contentHolder = $contentList.eq( $index ),
					templateLoaded = $contentHolder.data( 'template-loaded' ) || false,
					templateId     = $contentHolder.data( 'template-id' ),
					loader         = $( '.jet-tabs-loader', $contentHolder ),
					ajaxData = {
						'id': templateId,
						'dev': window.JetTabsSettings.devMode,
					};

				if ( templateLoaded || false === templateId ) {
					return false;
				}

				$window.trigger( 'jet-tabs/ajax-load-template/before', {
					toggleIndex: $index,
					target: $target,
					contentHolder: $contentHolder
				} );

				$contentHolder.data( 'template-loaded', true );

				if ( window.JetTabsSettings.isSelfRequest ) {
					ajaxData['jet_tabs_self'] = 1;
					ajaxData['no-cache'] 	  = 'true';
					ajaxData['timeStamp']     = Date.now();
				}

				$.ajax( {
					type: 'GET',
					url: window.JetTabsSettings.templateApiUrl,
					dataType: 'json',
					data: ajaxData,
					success: function( responce, textStatus, jqXHR ) {
						var templateContent     = responce['template_content'],
							templateScripts     = responce['template_scripts'],
							templateStyles      = responce['template_styles'];

						for ( var scriptHandler in templateScripts ) {
							JetTabs.addedAssetsPromises.push( JetTabs.loadScriptAsync( scriptHandler, templateScripts[ scriptHandler ] ) );
						}

						for ( var styleHandler in templateStyles ) {
							JetTabs.addedAssetsPromises.push( JetTabs.loadStyle( styleHandler, templateStyles[ styleHandler ] ) );
						}

						Promise.all( JetTabs.addedAssetsPromises ).then( function( value ) {
							loader.remove();
							$contentHolder.append( templateContent );
							JetTabs.prepareVideoIframes( $contentHolder );
							JetTabs.observeVideoIframes( $contentWrapper );
							JetTabs.elementorFrontendInit( $contentHolder );

							$window.trigger( 'jet-tabs/ajax-load-template/after', {
								toggleIndex: $index,
								target: $target,
								contentHolder: $contentHolder,
								responce: responce
							} );
						}, function( reason ) {
							console.log( 'Script Loaded Error' );
						});
					}
				} );//end

			}

			// Hash Watch Handler
			if ( tabsArray ) {

				$controlList.each( function( index ) {
					var $this      = $( this ),
						id         = $this.attr( 'id' ),
						templateId = $this.data( 'template-id' ),
						tabIndex   = index;

					tabsArray.forEach( function( itemHash, i ) {

						if ( itemHash === id ) {

							if ( settings['ajaxTemplate'] && templateId ) {
								ajaxLoadTemplate( tabIndex );
							}

							switchTab( tabIndex );
						}
					} );

				} );
			}

			$controlList.each( function() {
				anchorSelectors.push( 'a[href*="#' + $( this ).attr( 'id' ) + '"]' );
			} );

			$( document ).on( 'click.jetTabAnchor', anchorSelectors.join( ',' ), function( event ) {
				var $hash = $( this.hash );

				if ( ! $hash.closest( $scope )[0] ) {
					return;
				}

				var tabInx = $hash.data( 'tab' ) - 1;

				if ( settings['ajaxTemplate'] ) {
					ajaxLoadTemplate( tabInx );
				}

				switchTab( tabInx );
			} );

		},// tabsInit end

		switcherInit: function( $scope ) {
			var $target          = $( '.jet-switcher', $scope ).first(),
				$widgetId        = $target.data( 'id' ),
				$window          = $( window ),
				$controlWrapper  = $( '.jet-switcher__control-wrapper', $target ).first(),
				$contentWrapper  = $( '.jet-switcher__content-wrapper', $target ).first(),
				$controlInstance = $( '> .jet-switcher__control-instance', $controlWrapper ),
				$controlList     = $( '> .jet-switcher__control-instance > .jet-switcher__control, > .jet-switcher__control', $controlWrapper ),
				$contentList     = $( '> .jet-switcher__content', $contentWrapper ),
				$disableContent  = $( '> .jet-switcher__content--disable', $contentWrapper ),
				$enableContent   = $( '> .jet-switcher__content--enable', $contentWrapper ),
				state            = $target.hasClass( 'jet-switcher--disable' ),
				settings         = $target.data( 'settings' ) || {},
				toogleEvents     = 'mouseenter mouseleave',
				scrollOffset;

			JetTabs.observeVideoIframes( $contentWrapper );

			if ( 'ontouchend' in window || 'ontouchstart' in window ) {
				addTouchEvent();
			} else {
				addClickEvent();
			}

			$( window ).on( 'resize.jetSwitcher orientationchange.jetSwitcher', function() {
				$contentWrapper.css( { 'height': 'auto' } );
			} );

			$( '.jet-switcher__control', $scope ).keydown( function( e ) {
				var $this  = $( this ),
					$which = e.which || e.keyCode;

				if ( $which == 13 || $which == 32 ) {
					switchTab();
					$( '[aria-expanded="true"]', $scope ).focus();
				}

				if ( $which == 37 ) {
					if ( 0 != $this.prev().length && $this.prev().hasClass( 'jet-switcher__control' ) && $target.hasClass( 'jet-switcher--preset-1' ) ) {
						$this.prev().focus();
						switchTab();
					} else if ( $target.hasClass( 'jet-switcher--preset-2' ) ) {
						if ( $this.hasClass( 'jet-switcher__control--disable' ) ) {
							return false;
						} else if ( $this.hasClass( 'jet-switcher__control--enable' ) ) {
							$( '.jet-switcher__control--disable', $scope ).focus();
							switchTab();
						}
					}
				}

				if ( $which == 39 ) {
					if ( 0 != $this.next().length && $this.next().hasClass( 'jet-switcher__control' ) && $target.hasClass( 'jet-switcher--preset-1' ) ) {
						$this.next().focus();
						switchTab();
					} else if ( $target.hasClass( 'jet-switcher--preset-2' ) ) {
						if ( $this.hasClass( 'jet-switcher__control--disable' ) ) {
							$( '.jet-switcher__control--enable', $scope ).focus();
							switchTab();
						} else if ( $this.hasClass( 'jet-switcher__control--enable' ) ) {
							return false;
						}
					}
				}
			} );

			function addClickEvent() {
				$controlInstance.on( 'click.jetSwitcher', function() {
					switchTab();
				});
			}

			function addTouchEvent() {

				$controlInstance.on( 'touchstart', function( event ) {
					scrollOffset = $( window ).scrollTop();
				} );

				$controlInstance.on( 'touchend', function( event ) {
					if ( scrollOffset !== $( window ).scrollTop() ) {
						return false;
					}

					switchTab();

				} );
			}

			function switchTab( curentIndex ) {
				var $activeControl, $activeContent,
					activeContentHeight = 'auto',
					timer;

				$contentWrapper.css( { 'height': $contentWrapper.outerHeight( true ) } );

				$target.toggleClass( 'jet-switcher--disable jet-switcher--enable' );

				if ( $target.hasClass( 'jet-switcher--disable' ) ) {
					state = false;
				} else {
					state = true;
				}

				$activeControl = ! state ? $controlList.eq(0) : $controlList.eq(1);
				$activeContent = ! state ? $contentList.eq(0) : $contentList.eq(1);

				$contentList.removeClass( 'active-content' );
				activeContentHeight = $activeContent.outerHeight( true );
				activeContentHeight += parseInt( $contentWrapper.css( 'border-top-width' ) ) + parseInt( $contentWrapper.css( 'border-bottom-width' ) );
				$activeContent.addClass( 'active-content' );

				$controlList.attr( 'aria-expanded', 'false' );
				$activeControl.attr( 'aria-expanded', 'true' );

				$contentList.attr( 'aria-hidden', 'true' );
				$activeContent.attr( 'aria-hidden', 'false' );

				$contentWrapper.css( { 'height': activeContentHeight } );

				$window.trigger( 'jet-tabs/switcher/show-case-event/before', {
					target: $target,
					caseIndex: curentIndex,
				} );

				if ( timer ) {
					clearTimeout( timer );
				}

				timer = setTimeout( function() {
					$window.trigger( 'jet-tabs/switcher/show-case-event/after', {
						target: $target,
						caseIndex: curentIndex,
					} );

					$contentWrapper.css( { 'height': 'auto' } );
				}, 500 );
			}
		},

		recalcReadMoreBox: function($anyChild) {
			var $box = $anyChild.closest('.jet-view-more-section.view-more-visible');
			if (!$box.length) {
				$box = $anyChild.closest('.jet-view-more__content');
			}
			if (!$box.length) {
				$box = $anyChild.closest('[data-jet-view-more]');
			}
			if (!$box.length) return;

			var content = $box.find('.jet-view-more__content, .elementor-widget-container, .e-con-inner').first()[0] || $box[0];

			requestAnimationFrame(function(){
				$box[0].style.height = '';
				var h = content.scrollHeight;
				$box.css('max-height', h + 'px');
			});
		},

		accordionInit: function( $scope ) {
			var $target              = $( '.jet-accordion', $scope ).first(),
				$widgetId            = $target.data( 'id' ),
				$window              = $( window ),
				$controlsList        = $( '> .jet-accordion__inner > .jet-toggle > .jet-toggle__control', $target ),
				settings             = $target.data( 'settings' ),
				$toggleList          = $( '> .jet-accordion__inner > .jet-toggle', $target ),
				anchorSelectors      = [],
				timer, timer2, timerСlick,
				curentHash           = window.location.hash || false,
				togglesArray         = curentHash ? curentHash.replace( '#', '' ).split( '&' ) : false;

			$toggleList.each( function() {
				if ( $( this ).hasClass( 'active-toggle' ) && settings['ajaxTemplate'] ) {
					var activeIndex = $( this ).find( '.jet-toggle__control' ).data( 'toggle' ) - 1;
					ajaxLoadTemplate( activeIndex );
				}
			} );


			$( window ).on( 'resize.jetAccordion orientationchange.jetAccordion', function() {
				var activeToggle        = $( '> .jet-accordion__inner > .active-toggle', $target ),
					activeToggleContent = $( '> .jet-toggle__content', activeToggle );

				activeToggleContent.css( { 'height': 'auto' } );
			} );

			$( '.jet-toggle__control', $scope ).keydown( function( e ) {
				var $this   = $( this ),
					$which  = e.which || e.keyCode;

				if ( $which == 13 || $which == 32 ) {
					$this.click();
					return false;
				}

				if ( $which == 37 ) {
					if ( 0 != $this.closest( '.jet-accordion__item' ).prev().length ) {
						$this.closest( '.jet-accordion__item' ).prev().find( '.jet-toggle__control' ).focus();
					}
				}

				if ( $which == 39 ) {
					if ( 0 != $this.closest( '.jet-accordion__item' ).next().length ) {
						$this.closest( '.jet-accordion__item' ).next().find( '.jet-toggle__control' ).focus();
					}
				}
			} );

			$controlsList.on( 'click.jetAccordion', function() {
				var $this               = $( this ),
					$toggle             = $this.closest( '.jet-toggle' ),
					toggleIndex         = +$this.data( 'toggle' ) - 1,
					currentDeviceMode   = elementorFrontend.getCurrentDeviceMode();

				if ( typeof timer !== 'undefined' && timer ) clearTimeout( timer );
				if ( typeof timer2 !== 'undefined' && timer2 ) clearTimeout( timer2 );
				if ( typeof timerСlick !== 'undefined' && timerСlick ) clearTimeout( timerСlick );

				if ( $toggle.data('animating') ) return;
				$toggle.data('animating', true);
				setTimeout(function(){ $toggle.removeData('animating'); }, 100);

				if ( settings['collapsible'] ) {
					var $toggleControl  = $( '> .jet-toggle__control', $toggle ),
						$toggleContent  = $( '> .jet-toggle__content', $toggle );

					if ( ! $toggleControl.length || ! $toggleContent.length ) {
						return;
					}
					const classIsOpen = 'active-toggle';
					const isOpened = $toggle.hasClass( classIsOpen );

					// Capture scroll start position.
					const togglerInitialY = $toggleControl[0].getBoundingClientRect().top + window.scrollY;

					// Find currently open item (if any).
					let currentlyOpenItem    = null;
					let currentlyOpenControl = null;
					let currentlyOpenContent = null;
					$toggleList.each( function() {
						if (
							this !== $toggle[0] &&
							this.classList.contains( classIsOpen )
						) {
							currentlyOpenItem    = this;
							currentlyOpenControl = this.querySelector( '.jet-toggle__control' );
							currentlyOpenContent = this.querySelector( '.jet-toggle__content' );
						}
					} );

					// Measure content shift (closing content).
					const closingHeight = currentlyOpenContent?.scrollHeight || 0;

					if ( !isOpened && currentlyOpenControl && currentlyOpenContent ) {
						currentlyOpenItem.classList.remove( classIsOpen );
						currentlyOpenContent.style.height = '0';
						currentlyOpenControl.setAttribute( 'aria-expanded', 'false' );

						// Calculate the predicted scroll position after layout shift.
						if ( closingHeight ) {
							const currentTogglerY =
									  currentlyOpenControl.getBoundingClientRect().top +
									  window.scrollY;

							// Only scroll if clicked toggler is below the currently open one.
							if ( togglerInitialY > currentTogglerY ) {
								const predictedY = togglerInitialY - closingHeight;

								if ( window.scrollY > predictedY) {
									requestAnimationFrame( () => {
										let offset = 0;

										if ( true === settings['switchScrolling'] ) {
											offset = settings['switchScrollingOffset']['size'];
										}
										window.scrollTo( {
											top: predictedY - offset,
											behavior: 'auto',
										} );
									} );
								}
							}
						}
					}

					$toggleList.each( function( index ) {
						var $this                = $( this ),
							$toggleControl       = $( '> .jet-toggle__control', $this ),
							$toggleContent       = $( '> .jet-toggle__content', $this ),
							$toggleContentHeight = $( '> .jet-toggle__content > .jet-toggle__content-inner', $this ).outerHeight();

						$toggleContentHeight += parseInt( $toggleContent.css( 'border-top-width' ) ) + parseInt( $toggleContent.css( 'border-bottom-width' ) );

						if ( index === toggleIndex && ! $toggle.hasClass( 'active-toggle' ) ) {
							$this.addClass( 'active-toggle' );
							$toggleContent.css( { 'height': $toggleContentHeight } );

							$toggleControl.attr( 'aria-expanded', 'true' );

							if ( settings['ajaxTemplate'] ) {
								ajaxLoadTemplate( toggleIndex );
							}

							$window.trigger( 'jet-tabs/accordion/show-toggle-event/before', {
								target: $target,
								toggleIndex: toggleIndex,
							} );

							if ( timer ) {
								clearTimeout( timer );
							}

							timer = setTimeout( function() {

								$window.trigger( 'jet-tabs/accordion/show-toggle-event/after', {
									target: $target,
									toggleIndex: toggleIndex,
								} );

								$toggleContent.css( { 'height': 'auto' } );
								JetTabs.recalcReadMoreBox($target);

								if ( true === settings['switchScrolling'] ) {
									$( 'html, body' ).animate( {
										scrollTop: $this.offset().top - settings['switchScrollingOffset']['size']
									}, settings['switchScrollingDelay'] );
								}
							}, 500 );

						} else {
							if ( $this.hasClass( 'active-toggle' ) ) {
								$toggleContent.css( { 'height': $toggleContent.outerHeight() } );
								$this.removeClass( 'active-toggle' );

								$toggleControl.attr( 'aria-expanded', 'false' );

								if ( timer2 ) {
									clearTimeout( timer2 );
								}

								timer2 = setTimeout( function() {
									$toggleContent.css( { 'height': 0 } );
								}, 5 );
							}
						}
					} );

				} else {

					var $toggleContent       = $( '> .jet-toggle__content', $toggle ),
						$toggleContentHeight = $( '> .jet-toggle__content > .jet-toggle__content-inner', $toggle ).outerHeight();

					$toggleContentHeight += parseInt( $toggleContent.css( 'border-top-width' ) ) + parseInt( $toggleContent.css( 'border-bottom-width' ) );

					if ( timerСlick ) {
						clearTimeout( timerСlick );
					}

					timerСlick = setTimeout( function() {

					$toggle.toggleClass( 'active-toggle' );

					if ( $toggle.hasClass( 'active-toggle') ) {

						$toggleContent.css( { 'height': $toggleContentHeight } );

						$this.attr( 'aria-expanded', 'true' );

						if ( settings['ajaxTemplate'] ) {
							ajaxLoadTemplate( toggleIndex );
						}

						$window.trigger( 'jet-tabs/accordion/show-toggle-event/before', {
							target: $target,
							toggleIndex: toggleIndex,
						} );

						if ( timer ) {
							clearTimeout( timer );
						}

						timer = setTimeout( function() {
							$window.trigger( 'jet-tabs/accordion/show-toggle-event/after', {
								target: $target,
								toggleIndex: toggleIndex,
							} );

							$toggleContent.css( { 'height': 'auto' } );
							JetTabs.recalcReadMoreBox($target);

							if ( true === settings['switchScrolling'] && settings['collapsible'] !== true ) {
								$( 'html, body' ).animate( {
									scrollTop: $this.offset().top - settings['switchScrollingOffset']['size']
								}, settings['switchScrollingDelay'] );
							}
						}, 200 );

					} else {
						$toggleContent.css( { 'height': $toggleContent.outerHeight() } );

						$this.attr( 'aria-expanded', 'false' );

						if ( timer2 ) {
							clearTimeout( timer2 );
						}

						timer2 = setTimeout( function() {
							$toggleContent.css( { 'height': 0 } );
							var $box = $target.closest('.jet-view-more-section.view-more-visible');
							if ($box.length) requestAnimationFrame(function(){ $box.css('max-height', $box[0].scrollHeight + 'px'); });
						}, 5 );
					}

					}, 200 );
				}

			});

			/**
			 * [ajaxLoadTemplate description]
			 * @param  {[type]} $index [description]
			 * @return {[type]}        [description]
			 */
			function ajaxLoadTemplate( $index ) {
				var $toggle        = $toggleList.eq( $index ),
					$contentHolder = $( '> .jet-toggle__content', $toggle ),
					$contentHolderInner = $( '> .jet-toggle__content > .jet-toggle__content-inner', $toggle ),
					templateLoaded = $contentHolder.data( 'template-loaded' ) || false,
					templateId     = $contentHolder.data( 'template-id' ),
					loader         = $( '.jet-tabs-loader', $contentHolderInner );

				if ( templateLoaded || false === templateId ) {
					return false;
				}

				$window.trigger( 'jet-tabs/ajax-load-template/before', {
					toggleIndex: $index,
					target: $target,
					contentHolder: $contentHolder
				} );

				$contentHolder.data( 'template-loaded', true );

				$.ajax( {
					type: 'GET',
					url: window.JetTabsSettings.templateApiUrl,
					dataType: 'json',
					data: {
						'id': templateId,
						'dev': window.JetTabsSettings.devMode
					},
					success: function( responce, textStatus, jqXHR ) {
						var templateContent     = responce['template_content'],
							templateScripts     = responce['template_scripts'],
							templateStyles      = responce['template_styles'];

						for ( var scriptHandler in templateScripts ) {
							JetTabs.addedAssetsPromises.push( JetTabs.loadScriptAsync( scriptHandler, templateScripts[ scriptHandler ] ) );
						}

						for ( var styleHandler in templateStyles ) {
							JetTabs.addedAssetsPromises.push( JetTabs.loadStyle( styleHandler, templateStyles[ styleHandler ] ) );
						}

						Promise.all( JetTabs.addedAssetsPromises ).then( function( value ) {
							loader.remove();
							$contentHolderInner.append( templateContent );
							JetTabs.elementorFrontendInit( $contentHolderInner );

							$window.trigger( 'jet-tabs/ajax-load-template/after', {
								toggleIndex: $index,
								target: $target,
								contentHolder: $contentHolder,
								responce: responce
							} );
						}, function( reason ) {
							console.log( 'Script Loaded Error' );
						});
					}
				} );//end
			}

			// Hash Watch Handler
			if ( togglesArray ) {

				$controlsList.each( function( index ) {
					var $this    = $( this ),
						id       = $this.attr( 'id' ),
						toggleIndex = index;

					togglesArray.forEach( function( itemHash, i ) {
						if ( itemHash === id ) {
							$this.trigger('click.jetAccordion');
						}
					} );

				} );
			}

			$controlsList.each( function() {
				anchorSelectors.push( 'a[href*="#' + $( this ).attr( 'id' ) + '"]' );
			} );

			$( document ).on( 'click.jetAccordionAnchor', anchorSelectors.join( ',' ), function( event ) {
				var $hash = $( this.hash );

				if ( ! $hash.closest( $scope )[0] ) {
					return;
				}

				$hash.trigger( 'click.jetAccordion' );
			} );

		},// accordionInit end

		imageAccordionInit: function( $scope) {
			var $target  = $( '.jet-image-accordion', $scope ),
				instance = null,
				settings = {};

			if ( ! $target.length ) {
				return;
			}

			settings = $target.data( 'settings' );

			instance = new jetImageAccordion( $target, settings );
			instance.init();
		},// imageAccordionInit end

		loadScriptAsync: function( script, uri ) {

			if ( JetTabs.addedScripts.hasOwnProperty( script ) ) {
				return script;
			}

			if ( !uri ) {
				return;
			}

			JetTabs.addedScripts[ script ] = uri;

			return new Promise( function( resolve, reject ) {
				var tag = document.createElement( 'script' );

				tag.src    = uri;
				tag.async  = true;
				tag.onload = function() {
					resolve( script );
				};

				document.head.appendChild( tag );
			});
		},

		loadStyle: function( style, uri ) {

			if ( JetTabs.addedStyles.hasOwnProperty( style ) && JetTabs.addedStyles[ style ] ===  uri) {
				return style;
			}

			if ( !uri ) {
				return;
			}

			JetTabs.addedStyles[ style ] = uri;

			return new Promise( function( resolve, reject ) {
				var tag = document.createElement( 'link' );

				tag.id      = style;
				tag.rel     = 'stylesheet';
				tag.href    = uri;
				tag.type    = 'text/css';
				tag.media   = 'all';
				tag.onload  = function() {
					resolve( style );
				};

				document.head.appendChild( tag );
			});
		},

		elementorFrontendInit: function( $container ) {

			$container.find( '[data-element_type]' ).each( function() {
				var $this       = $( this ),
					elementType = $this.data( 'element_type' );

				if ( ! elementType ) {
					return;
				}

				try {
					if ( 'widget' === elementType ) {
						elementType = $this.data( 'widget_type' );
						window.elementorFrontend.hooks.doAction( 'frontend/element_ready/widget', $this, $ );
					}

					window.elementorFrontend.hooks.doAction( 'frontend/element_ready/global', $this, $ );
					window.elementorFrontend.hooks.doAction( 'frontend/element_ready/' + elementType, $this, $ );

				} catch ( err ) {
					console.log(err);

					$this.remove();

					return false;
				}
			} );

		},

		getElementorElementSettings: function( $scope ) {

			if ( window.elementorFrontend && window.elementorFrontend.isEditMode() && $scope.hasClass( 'elementor-element-edit-mode' ) ) {
				return JetTabs.getEditorElementSettings( $scope );
			}

			return $scope.data( 'settings' ) || {};
		},

		getEditorElementSettings: function( $scope ) {
			var modelCID = $scope.data( 'model-cid' ),
				elementData;

			if ( ! modelCID ) {
				return {};
			}

			if ( ! elementor.hasOwnProperty( 'config' ) ) {
				return {};
			}

			if ( ! elementor.config.hasOwnProperty( 'elements' ) ) {
				return {};
			}

			if ( ! elementor.config.elements.hasOwnProperty( 'data' ) ) {
				return {};
			}

			elementData = elementor.config.elements.data[ modelCID ];

			if ( ! elementData ) {
				return {};
			}

			return elementData.toJSON();
		},

		prepareVideoIframes: function( $container ) {
			if ( ! $container || ! $container.length ) return;

			$container.find( 'iframe' ).each( function() {
				var $iframe = $( this );

				var srcAttr = $iframe.attr( 'src' ) ? 'src'
					: ( $iframe.attr( 'data-src' ) ? 'data-src'
						: ( $iframe.attr( 'data-lazy-src' ) ? 'data-lazy-src'
							: ( $iframe.attr( 'data-lazy-load' ) ? 'data-lazy-load' : null ) ) );

				if ( ! srcAttr ) return;

				var src = $iframe.attr( srcAttr );
				if ( ! src ) return;

				var isYouTube = /youtube\.com|youtube-nocookie\.com|youtu\.be/i.test( src );
				var isVimeo   = /vimeo\.com/i.test( src );

				if ( ! isYouTube && ! isVimeo ) return;

				try {
					var fixedSrc = src.indexOf( '//' ) === 0 ? window.location.protocol + src : src;
					var url      = new URL( fixedSrc );

					if ( isYouTube ) {
						if ( ! url.searchParams.has( 'enablejsapi' ) ) url.searchParams.set( 'enablejsapi', '1' );
						if ( ! url.searchParams.has( 'origin' ) ) url.searchParams.set( 'origin', window.location.origin );
					}

					if ( isVimeo ) {
						if ( ! url.searchParams.has( 'api' ) ) url.searchParams.set( 'api', '1' );
					}

					var newSrc = url.toString();

					if ( src.indexOf( '//' ) === 0 ) {
						newSrc = newSrc.replace( window.location.protocol, '' );
					}

					if ( newSrc !== src ) {
						$iframe.attr( srcAttr, newSrc );
					}
				} catch ( e ) {
				}
			} );
		},

		pauseMediaInContainer: function( $container ) {
			if ( ! $container || ! $container.length ) return;

			$container.find( 'video, audio' ).each( function() {
				try {
					this.pause();
				} catch ( e ) {
				}
			} );

			function getUrlAndAttr( $iframe ) {
				if ( $iframe.attr( 'src' ) ) return { attr: 'src', url: $iframe.attr( 'src' ) };
				if ( $iframe.attr( 'data-src' ) ) return { attr: 'data-src', url: $iframe.attr( 'data-src' ) };
				if ( $iframe.attr( 'data-lazy-src' ) ) return {
					attr: 'data-lazy-src',
					url: $iframe.attr( 'data-lazy-src' )
				};
				if ( $iframe.attr( 'data-lazy-load' ) ) return {
					attr: 'data-lazy-load',
					url: $iframe.attr( 'data-lazy-load' )
				};
				return { attr: null, url: '' };
			}

			$container.find( 'iframe' ).each( function() {
				var iframe = this;
				var $iframe = jQuery( iframe );

				var info = getUrlAndAttr( $iframe );
				var url  = info.url || '';
				if ( ! url ) return;

				var isYouTube = /youtube\.com|youtube-nocookie\.com|youtu\.be/i.test( url );
				var isVimeo   = /vimeo\.com/i.test( url );
				if ( ! isYouTube && ! isVimeo ) return;

				var hasYTApi = isYouTube && /enablejsapi=1/i.test( url );
				var hasVmApi = isVimeo && /api=1/i.test( url );

				if ( ( hasYTApi || hasVmApi ) && iframe.contentWindow ) {
					try {
						if ( hasYTApi ) {
							iframe.contentWindow.postMessage( JSON.stringify( {
								event: 'command',
								func: 'pauseVideo',
								args: ''
							} ), '*' );
							return;
						}

						if ( hasVmApi ) {
							iframe.contentWindow.postMessage( JSON.stringify( {
								method: 'pause'
							} ), '*' );
							return;
						}
					} catch ( e ) {
					}
				}

				try {
					if ( ! $iframe.attr( 'data-jet-tabs-restore-src' ) ) {
						$iframe.attr( 'data-jet-tabs-restore-src', url );
					}

					$iframe.attr( 'src', 'about:blank' );
					$iframe.removeAttr( 'srcdoc' );
				} catch ( e ) {
				}
			} );
		},

		observeVideoIframes: function( $root ) {
			if ( ! $root || ! $root.length ) return;

			if ( $root.data( 'jetTabsVideoObserver' ) ) return;

			var observer = new MutationObserver( function( mutations ) {
				mutations.forEach( function( m ) {

					if ( m.type === 'attributes' ) {
						var el = m.target;

						if ( el && el.nodeType === 1 && el.tagName === 'IFRAME' ) {
							JetTabs.prepareVideoIframes( jQuery( el ).parent() );
						}

						return;
					}

					if ( m.type === 'childList' ) {
						if ( ! m.addedNodes || ! m.addedNodes.length ) return;

						m.addedNodes.forEach( function( node ) {
							if ( node.nodeType !== 1 ) return;

							var $node = jQuery( node );

							if ( $node.is( 'iframe' ) ) {
								JetTabs.prepareVideoIframes( $node.parent() );
								return;
							}

							var $iframes = $node.find ? $node.find( 'iframe' ) : jQuery();
							if ( $iframes.length ) {
								JetTabs.prepareVideoIframes( $node );
							}
						} );
					}

				} );
			} );

			observer.observe( $root[ 0 ], {
				childList: true,
				subtree: true,
				attributes: true,
				attributeFilter: [ 'src', 'data-src', 'data-lazy-src', 'data-lazy-load' ],
			} );
			$root.data( 'jetTabsVideoObserver', observer );
		},

		restoreMediaInContainer: function( $container ) {
			if ( ! $container || ! $container.length ) return;

			$container.find( 'iframe[data-jet-tabs-restore-src]' ).each( function() {
				var $iframe    = jQuery( this );
				var restoreUrl = $iframe.attr( 'data-jet-tabs-restore-src' );
				if ( ! restoreUrl ) return;

				$iframe.attr( 'src', restoreUrl );
				$iframe.removeAttr( 'data-jet-tabs-restore-src' );

				JetTabs.prepareVideoIframes( $iframe.parent() );
			} );
		},

		debounce: function( threshold, callback ) {
			var timeout;

			return function debounced( $event ) {
				function delayed() {
					callback.call( this, $event );
					timeout = null;
				}

				if ( timeout ) {
					clearTimeout( timeout );
				}

				timeout = setTimeout( delayed, threshold );
			};
		}

	};

	/**
	 * jetImageAccordion Class
	 *
	 * @return {void}
	 */
	window.jetImageAccordion = function( $selector, settings ) {
		var self            = this,
			$instance       = $selector,
			$itemsList      = $( '.jet-image-accordion__item', $instance ),
			itemslength     = $itemsList.length,
			defaultSettings = {
				orientation: 'vertical',
				activeSize:  {
					size: 50,
					unit: '%'
				},
				duration: 500,
				activeItem: -1
			},
			settings        = settings || {},
			activeItem      = -1;

		/**
		 * Checking options, settings and options merging
		 */
		settings = $.extend( defaultSettings, settings );

		activeItem = settings['activeItem'];

		/**
		 * Layout Build
		 */
		this.layoutBuild = function( ) {

			$itemsList.css( {
				'transition-duration': settings.duration + 'ms'
			} );

			$itemsList.each( function( index ) {
				if ( index === activeItem ) {
					$( this ).addClass( 'active-accordion' );
					self.layoutRender();
				}
			} );

			$( '.jet-image-accordion__image-instance', $itemsList ).imagesLoaded().progress( function( instance, image ) {
				var $image      = $( image.img ),
					$parentItem = $image.closest( '.jet-image-accordion__item' ),
					$loader     = $( '.jet-image-accordion__item-loader', $parentItem );

				$image.addClass( 'loaded' );

				$loader.fadeTo( 250, 0, function() {
					$( this ).remove();
				} );
			});

			self.layoutRender();
			self.addEvents();
		}

		/**
		 * Layout Render
		 */
		this.layoutRender = function( $accordionItem ) {
			var $accordionItem = $accordionItem || false,
				activeSize     = settings.activeSize.size,
				basis          = ( 100 / itemslength ).toFixed(2),
				grow           = activeSize / ( ( 100 - activeSize  ) / ( itemslength - 1 ) );

			$( '.jet-image-accordion__item:not(.active-accordion)', $instance ).css( {
				'flex-grow': 1
			} );

			$( '.active-accordion', $instance ).css( {
				'flex-grow': grow
			} );
		}

		this.addEvents = function() {
			var toogleEvents = 'mouseenter',
				scrollOffset = $( window ).scrollTop();

			if ( 'ontouchend' in window || 'ontouchstart' in window ) {
				$itemsList.on( 'touchstart.jetImageAccordion', function( event ) {
					scrollOffset = $( window ).scrollTop();
				} );

				$itemsList.on( 'touchend.jetImageAccordion', function( event ) {
					event.stopPropagation();

					var $this = $( this );

					if ( scrollOffset !== $( window ).scrollTop() ) {
						return false;
					}

					if ( ! $this.hasClass( 'active-accordion' ) ) {
						$itemsList.removeClass( 'active-accordion' );
						$this.addClass( 'active-accordion' );
					} else {
						$itemsList.removeClass( 'active-accordion' );
					}

					self.layoutRender();
				} );
			} else {
				$itemsList.on( 'mouseenter', function( event ) {
					var $this = $( this );

					if ( ! $this.hasClass( 'active-accordion' ) ) {
						$itemsList.removeClass( 'active-accordion' );
						$this.addClass( 'active-accordion' );
					}

					self.layoutRender();
				} );

				$( '.jet-image-accordion__item', $instance ).keydown( function( e ) {
					var $this  = $( this ),
						$which = e.which || e.keyCode;

						if ( $which == 13 || $which == 32 ) {
							if ( ! $this.hasClass( 'active-accordion' ) ) {
								$itemsList.removeClass( 'active-accordion' );
								$this.addClass( 'active-accordion' );
							} else {
								$itemsList.removeClass( 'active-accordion' );

								if ( -1 !== activeItem ) {
									$itemsList.eq( activeItem ).addClass( 'active-accordion' );
								}

								self.layoutRender();
							}

							self.layoutRender();
						}

						if ( $which == 37 ) {
							if ( 0 != $this.prev().length ) {
								$itemsList.removeClass( 'active-accordion' );
								$this.prev().focus();
								$this.prev().addClass( 'active-accordion' );
								self.layoutRender();
							}
						}

						if ( $which == 39 ) {
							if ( 0 != $this.next().length ) {
								$itemsList.removeClass( 'active-accordion' );
								$this.next().focus();
								$this.next().addClass( 'active-accordion' );
								self.layoutRender();
							}
						}
				} );
			}

			$instance.on( 'mouseleave.jetImageAccordion', function( event ) {
				$itemsList.removeClass( 'active-accordion' );

				if ( -1 !== activeItem ) {
					$itemsList.eq( activeItem ).addClass( 'active-accordion' );
				}

				self.layoutRender();
			} );

			/*$( document ).on( 'touchend.jetImageAccordion', function( event ) {
				$itemsList.removeClass( 'active-accordion' );
				self.layoutRender();
			} );*/
		}

		/**
		 * Init
		 */
		this.init = function() {
			self.layoutBuild();
		}
	}

	jQuery(document).on('jet-filter-content-rendered', function(  ) {
		jQuery( window ).trigger( 'elementor/frontend/init' );
	});

	$( window ).on( 'elementor/frontend/init', JetTabs.init );

	window.JetTabs = JetTabs;

}( jQuery, window.elementorFrontend, window.JetTabsSettings ) );
