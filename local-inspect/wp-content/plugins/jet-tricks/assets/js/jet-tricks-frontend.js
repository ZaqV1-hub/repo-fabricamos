( function( $, elementor ) {

	'use strict';

	var JetTricks = {

		init: function() {
			elementor.hooks.addAction( 'frontend/element_ready/section', JetTricks.elementorSection );
			elementor.hooks.addAction( 'frontend/element_ready/section', JetTricks.elementorColumn );
			elementor.hooks.addAction( 'frontend/element_ready/container', JetTricks.elementorSection );
			elementor.hooks.addAction( 'frontend/element_ready/container', JetTricks.elementorColumn );
			elementor.hooks.addAction( 'frontend/element_ready/column', JetTricks.elementorColumn );
			elementor.hooks.addAction( 'frontend/element_ready/widget', JetTricks.elementorWidget );

			var widgets = {
				'jet-view-more.default' : JetTricks.widgetViewMore,
				'jet-unfold.default' : JetTricks.widgetUnfold,
				'jet-hotspots.default' : JetTricks.widgetHotspots
			};

			$.each( widgets, function( widget, callback ) {
				elementor.hooks.addAction( 'frontend/element_ready/' + widget, callback );
			});

			// Re-init widgets in nested tabs
			window.elementorFrontend.elements.$window.on(
				'elementor/nested-tabs/activate',
				( event, content ) => {

					const $content = $( content );

					var $button  = $content.find( '.jet-unfold__button' );

					$button.off( 'click.jetUnfold' );

					JetTricks.initWidgetsHandlers( $content );
					JetTricks.elementorSection( $content );

				}
			);

			// Add an action when the Swiper Loop Carousel widget is ready on the frontend
			var loopCarouselTypes = [
				'loop-carousel.post',
				'loop-carousel.product',
				'loop-carousel.post_taxonomy',
				'loop-carousel.product_taxonomy'
			];
			
			loopCarouselTypes.forEach( function( carouselType ) {
				elementorFrontend.hooks.addAction( 'frontend/element_ready/' + carouselType, function( $scope, $ ) {
					
					$( window ).on( 'load', function() {
						var loopCarousel = $scope.find( '.swiper' ),
							swiperInstance = loopCarousel.data( 'swiper' ),
							$button = $scope.find( '.jet-unfold__button' );
						
						if ( swiperInstance && $button ) {
				
							$button.off( 'click.jetUnfold' );
							JetTricks.initLoopCarouselHandlers( $scope );
				
							swiperInstance.on( 'slideChange', function() {
								$button.off( 'click.jetUnfold' );
								JetTricks.initLoopCarouselHandlers( $scope );
							});
						}
					});
				});
			});
		},

		initLoopCarouselHandlers: function( $selector ) {
			$selector.find( '.elementor-widget-jet-unfold' ).each( function() {

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

		initWidgetsHandlers: function( $selector ) {

				$selector.find( '[data-element_type]' ).each( function() {

					var excludeWidgets = [
						'jet-woo-product-gallery-slider.default',
						'accordion.default',
						'jet-form-builder-form.default',
						'nav-menu.default'
					];

					var $this  = $( this ),

					elementType = $this.data( 'element_type' );

					if ( !elementType ) {
						return;
					}

					if ( 'widget' === elementType ) {

						elementType = $this.data( 'widget_type' );
						
						if ( excludeWidgets.includes( elementType ) ) {
							return;
						}
						
						window.elementorFrontend.hooks.doAction( 'frontend/element_ready/widget', $this, $ );
					}

					window.elementorFrontend.hooks.doAction( 'frontend/element_ready/global', $this, $ );
					window.elementorFrontend.hooks.doAction( 'frontend/element_ready/' + elementType, $this, $ );


				} );
		},

		elementorSection: function( $scope ) {
			var $target        = $scope,
				sectionId      = $scope.data( 'id' ),
				editMode       = Boolean( elementor.isEditMode() ),
				jetListing     = $target.parents( '.elementor-widget-jet-listing-grid' ).data( 'id' ),
				settings       = {};

			if ( window.JetTricksSettings && window.JetTricksSettings.elements_data.sections.hasOwnProperty( sectionId ) ) {
				settings = window.JetTricksSettings.elements_data.sections[ sectionId ];
			}

			if ( editMode ) {
				settings = JetTricks.sectionEditorSettings( $scope );
			}

			if ( ! settings ) {
				return false;
			}

			if ( jQuery.isEmptyObject( settings ) ) {
				return false;
			}

			if ( 'false' === settings.particles || '' === settings.particles_json ) {
				return false;
			}

			// Compatibility jet tricks particles with jet listing

			if ( jetListing && $target.parent().data( 'elementor-type' ) === 'jet-listing-items' ){
				sectionId += jetListing + $target.parents( '.jet-listing-grid__item' ).data( 'post-id' );
			} 

			var particlesId   = 'jet-tricks-particles-instance-' + sectionId,
				particlesJson = JSON.parse( settings.particles_json );

			$scope.prepend( '<div id="' + particlesId + '" class="jet-tricks-particles-section__instance"></div>' );

			// Initialize particles based on library version

			if ( typeof tsParticles !== 'undefined' && tsParticles.load && tsParticles.version && tsParticles.version.startsWith('3.') ) {
				tsParticles.load({
					id: particlesId,
					options: particlesJson
				});
			} else if ( typeof tsParticles !== 'undefined' && tsParticles.load ) {
				tsParticles.load( particlesId, particlesJson );
			}

		},

		elementorColumn: function( $scope ) {
			var $target  = $scope,
				$parentSection = $scope.closest( '.elementor-section' ),
				isLegacyModeActive = !!$target.find( '> .elementor-column-wrap' ).length,
				$window  = $( window ),
				columnId = $target.data( 'id' ),
				editMode = Boolean( elementor.isEditMode() ),
				settings = {},
				stickyInstance = null,
				stickyInstanceOptions = {
					topSpacing: 50,
					bottomSpacing: 50,
					containerSelector: isLegacyModeActive ? '.elementor-row' : '.elementor-container, .e-con-inner',
					innerWrapperSelector: isLegacyModeActive ? '.elementor-column-wrap' : '.elementor-widget-wrap',
				},
				$observerTarget = $target.find('.elementor-element');

			if ( ! editMode ) {
				settings = $target.data( 'jet-settings' );

				if ( $target.hasClass( 'jet-sticky-column' ) ) {
					if ( -1 !== settings['stickyOn'].indexOf( elementorFrontend.getCurrentDeviceMode() ) ) {
						$target.each( function() {
							var $this  = $( this ),
								elementType = $this.data( 'element_type' );
		
							if ( settings['behavior'] === 'fixed' ) {
								initFixedSticky( $this, settings );
							} else if ( elementType !== 'container' && elementType !== 'section' ) {
								initSidebarSticky( $this, settings, stickyInstanceOptions );
							} else if ( settings['behavior'] === 'scroll_until_end' ) {
								initScrollUntilEndSticky( $this, settings );
							} else {
								initDefaultSticky( $this, settings );
							}
						});
					}
				}
			}

			function initFixedSticky($element, settings) {
				var offsetTop = parseInt(settings['topSpacing']) || 0;
				var bottomSpacing = parseInt(settings['bottomSpacing']) || 0;
				var $window = $(window);
				var elementId = $element.data('id');
				var originalOffsetTop = $element.offset().top;
				var originalHeight = $element.outerHeight();
			
				var $allStickyElements = $('.jet-sticky-column').filter(function() {
					var $this = $(this);
					var elementSettings = $this.data('jet-settings');

					return elementSettings && elementSettings.stickyOn.indexOf(elementorFrontend.getCurrentDeviceMode()) !== -1;
				});

				var currentIndex = $allStickyElements.index($element);
				var $nextSticky = currentIndex + 1 < $allStickyElements.length ? $allStickyElements.eq(currentIndex + 1) : null;
				var $stopper = null;
				if ($nextSticky) {
					$stopper = $nextSticky.closest('.elementor-top-section, .e-parent');
					if (!$stopper.length) {
						$stopper = $nextSticky;
					}
				}
			
				const $placeholder = $('<div></div>')
					.addClass('jet-sticky-placeholder')
					.css({
						display: 'none',
						height: originalHeight,
						width: $element.outerWidth(),
						visibility: 'hidden'
					});
			
				$element.before($placeholder);				
			
				function enableSticky() {
					$placeholder.show();
					$element.addClass('jet-sticky-container--stuck');
			
					var stopperTop = $stopper?.offset()?.top;
					var stopPoint = stopperTop ? (stopperTop - $element.outerHeight() - offsetTop - bottomSpacing) : null;
					var diff = 0;
			
					if (stopPoint && stopPoint < $window.scrollTop()) {
						diff = (stopPoint - $window.scrollTop());
					}
			
					$element.css({
						position: 'fixed',
						top: diff + 'px',
						transform: `translateY(${offsetTop}px)`,
						left: $placeholder.offset().left + 'px',
						width: $placeholder.outerWidth() + 'px'
					});
				}
			
				function disableSticky() {
					$placeholder.hide();
					$element.removeClass('jet-sticky-container--stuck');
			
					$element.css({
						position: '',
						top: '',
						transform: '',
						left: '',
						width: ''
					});
				}
			
				function onScroll() {
					var scrollTop = $window.scrollTop();
					if (scrollTop >= originalOffsetTop) {
						enableSticky();
					} else {
						disableSticky();
					}
				}
			
				function onResize() {
					originalOffsetTop = $placeholder.offset().top;
					originalHeight = $element.outerHeight();
			
					$placeholder.css({
						height: originalHeight,
						width: $element.outerWidth()
					});
			
					onScroll();
				}
			
				let ticking = false;
				$window.on('scroll.jetStickyHeader-' + elementId, function() {
					if (!ticking) {
						requestAnimationFrame(function() {
							onScroll();
							ticking = false;
						});
						ticking = true;
					}
				});
			
				$window.on('resize.jetStickyHeader-' + elementId, JetTricksTools.debounce(100, onResize));
				onScroll();
			
				$window.on('resize.jetStickyHeader-' + elementId, JetTricksTools.debounce(100, function() {
					if (-1 === settings['stickyOn'].indexOf(elementorFrontend.getCurrentDeviceMode())) {
						cleanupSticky($element, $placeholder, elementId);
					}
				}));
			}
			
			function cleanupSticky( $element, $placeholder, elementId ) {
				$placeholder.remove();
				$element.css({
					position: '',
					top: '',
					transform: '',
					left: '',
					width: '',
					zIndex: '',
					transition: '',
					willChange: ''
				});
				$element.removeClass('jet-sticky-container--stuck');
				$window.off('scroll.jetStickyHeader-' + elementId);
				$window.off('resize.jetStickyHeader-' + elementId);
			}

			function initSidebarSticky( $element, settings, options ) {
				options.topSpacing = settings['topSpacing'];
				options.bottomSpacing = settings['bottomSpacing'];

				imagesLoaded( $parentSection, function() {
					$target.data( 'stickyColumnInit', true );
					stickyInstance = new StickySidebar( $target[0], options );
				});

				var targetMutation = $target[0],
					config = { attributes: true, childList: true, subtree: true };

				var observer = new MutationObserver( function( mutations ) {
					for( var mutation of mutations ) {
						if ( 'attributes' === mutation.type && mutation.attributeName !== 'style' ) {
							$target[0].style.height = 'auto';
						}
					}
				});

				observer.observe( targetMutation, config );

				$window.on( 'resize.JetTricksStickyColumn orientationchange.JetTricksStickyColumn', 
					JetTricksTools.debounce( 50, resizeDebounce ) );

				var observer = new MutationObserver( function( mutations ) {
					if ( stickyInstance ) {
						mutations.forEach( function(mutation){
							if (mutation.attributeName === 'class') {
								setTimeout( function() {
									stickyInstance.destroy();
									stickyInstance = new StickySidebar( $target[0], options );
								}, 100 );
							}
						});
					}
				});

				$observerTarget.each( function(){
					observer.observe( $( this )[0], {
						attributes: true
					});
				});
			}

			function initScrollUntilEndSticky( $element, settings ) {
				const stickyHeight = $element.outerHeight();
				const stickyContentBottom = $element.offset().top + stickyHeight;
				const stickyViewportOffset = $window.height() - stickyHeight - settings['bottomSpacing'];

				$('body').addClass('jet-sticky-container');

				$window.on( 'scroll.jetSticky', function () {
					const scrollPosition = $window.scrollTop();

					if ( scrollPosition + $window.height() >= stickyContentBottom ) {
						$element.css({
							position: 'sticky',
							top: stickyViewportOffset + 'px',
							bottom: 'auto',
							left: 'auto',
							zIndex: settings['zIndex'],
						});
					}
				});

				$observerTarget.on( 'destroy.jetSticky', function () {
					$window.off( 'scroll.jetSticky' );
					$('body').removeClass('jet-sticky-container');
				});
			}

			function initDefaultSticky( $element, settings ) {
				$('body').addClass('jet-sticky-container');
				$element.addClass( 'jet-sticky-container-sticky' );
				$element.css({ 
					'top': settings['topSpacing'], 
					'bottom': settings['bottomSpacing']
				});
			}

			function resizeDebounce() {
				var currentDeviceMode = elementorFrontend.getCurrentDeviceMode(),
					availableDevices  = settings['stickyOn'] || [],
					isInit            = $target.data( 'stickyColumnInit' );

				if ( -1 !== availableDevices.indexOf( currentDeviceMode ) ) {
					if ( ! isInit ) {
						$target.data( 'stickyColumnInit', true );
						stickyInstance = new StickySidebar( $target[0], stickyInstanceOptions );
						stickyInstance.updateSticky();
					}
				} else {
					$target.data( 'stickyColumnInit', false );
					stickyInstance.destroy();
				}
			}
		},

		elementorWidget: function( $scope ) {
			var parallaxInstance = null,
				satelliteInstance = null,
				tooltipInstance = null;

			parallaxInstance = new jetWidgetParallax( $scope );
			parallaxInstance.init();

			satelliteInstance = new jetWidgetSatellite( $scope );
			satelliteInstance.init();

			tooltipInstance = new jetWidgetTooltip( $scope );
			tooltipInstance.init();

		},

		getElementorElementSettings: function( $scope ) {

			if ( window.elementorFrontend && window.elementorFrontend.isEditMode() && $scope.hasClass( 'elementor-element-edit-mode' ) ) {
				return JetTricks.getEditorElementSettings( $scope );
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

		widgetViewMore: function( $scope ) {
			var $target         = $scope.find( '.jet-view-more' ),
				instance        = null,
				settings        = $target.data( 'settings' );

			instance = new jetViewMore( $target, settings );
			instance.init();
		},

		widgetUnfold: function( $scope ) {
			var $target                = $scope.find( '.jet-unfold' ),
				$button                = $( '.jet-unfold__button', $target ),
				$mask                  = $( '.jet-unfold__mask', $target ),
				$content               = $( '.jet-unfold__content', $target ),
				$contentInner          = $( '.jet-unfold__content-inner', $target),
				$trigger               = $( '.jet-unfold__trigger', $target),
				$separator             = $( '.jet-unfold__separator', $target ),
				settings               = $.extend( {}, $target.data( 'settings' ), JetTricks.getElementorElementSettings( $scope ) ),
				maskBreakpointsHeights = [],
				prevBreakpoint         = '',
				unfoldDuration         = settings['unfoldDuration'] || settings['unfold_duration'],
				foldDuration           = settings['foldDuration'] || settings['fold_duration'],
				unfoldEasing           = settings['unfoldEasing'] || settings['unfold_easing'],
				foldEasing             = settings['foldEasing'] || settings['fold_easing'],
				maskHeightAdv          = 20,
				heightCalc             = '',
				autoHide               = settings['autoHide'] || false,
				autoHideTime           = settings['autoHideTime'] && 0 != settings['autoHideTime']['size'] ? settings['autoHideTime']['size'] : 5,
				hideOutsideClick       = settings['hideOutsideClick'] || false,
				heightControlType      = settings['heightControlType'] || 'height',
				wordCount              = settings['wordCount'] || 20,
				autoHideTrigger,
				activeBreakpoints      = elementor.config.responsive.activeBreakpoints,
				initialLoaded          = false;

			function updateMaskGradientClass() {
				if (settings.separatorType === 'gradient') {
					if ($target.hasClass('jet-unfold-state') || $trigger.is(':hidden')) {
						$mask.removeClass('jet-unfold__mask-gradient');
					} else {
						$mask.addClass('jet-unfold__mask-gradient');
					}
				}
			}

			function calculateHeightByWordCount() {
				
				var text = $contentInner.text().trim();
				if (!text) {
					return 0; 
				}

				var words = text.split(/\s+/);
				var wordsToShow = Math.min(getDeviceWordCount(), words.length);
				var visibleText = words.slice(0, wordsToShow).join(' ');
				
								
				var $tempElement = $contentInner.clone();
				$tempElement
					.css({
						position: 'absolute',
						visibility: 'hidden',
						height: 'auto',
						overflow: 'visible'
					})
					.text(visibleText);
				
				$contentInner.after($tempElement);
				var height = $tempElement.outerHeight();
				
				$tempElement.remove();
				
				return height; 
			}

			maskBreakpointsHeights['desktop']    = [];
			maskBreakpointsHeights['widescreen'] = [];

			maskBreakpointsHeights['desktop']['maskHeight'] = (settings['mask_height'] && settings['mask_height']['size'] && '' != settings['mask_height']['size']) ? settings['mask_height']['size'] : 50;

			prevBreakpoint = 'desktop';

			Object.keys(  activeBreakpoints ).reverse().forEach(  function( breakpointName ) {

				if ( 'widescreen' === breakpointName ) {
					maskBreakpointsHeights['widescreen']['maskHeight'] = (settings['mask_height_widescreen'] && settings['mask_height_widescreen']['size'] && '' != settings['mask_height_widescreen']['size']) ? settings['mask_height_widescreen']['size'] : maskBreakpointsHeights['desktop']['maskHeight'];
				} else {
					maskBreakpointsHeights[breakpointName] = [];

					var breakpointSetting = settings['mask_height_' + breakpointName];
					maskBreakpointsHeights[breakpointName]['maskHeight'] = (breakpointSetting && breakpointSetting['size'] && '' != breakpointSetting['size']) ? breakpointSetting['size'] : maskBreakpointsHeights[prevBreakpoint]['maskHeight'];

					prevBreakpoint = breakpointName;
				}
			} );

			onLoaded();

			if ( typeof ResizeObserver !== 'undefined' ) {
				new ResizeObserver( function( entries ) {
					if ( $target.hasClass( 'jet-unfold-state' ) ) {
						$mask.css( {
							'height': $contentInner.outerHeight()
						} );
					}
				} ).observe( $contentInner[0] );
			}
			
			if ( 'true' === hideOutsideClick ) {
				$( window ).on( 'mouseup', function( event ) {
					let container = $target;
					if ( !container.is( event.target ) && 0 === container.has( event.target ).length && $target.hasClass( 'jet-unfold-state' ) ) {
						$button.trigger( 'click' );
					}
				} )
			}

			$target.one('transitionend webkitTransitionEnd oTransitionEnd', function() {
				if ( !initialLoaded ) {
					onLoaded();
					initialLoaded = true;
				}
			});

			function onLoaded() {
				initialLoaded = true;

				var deviceHeight = getDeviceHeight();

				heightCalc = +deviceHeight + maskHeightAdv;

				if ( heightCalc < $contentInner.height() ) {

					if ( ! $target.hasClass( 'jet-unfold-state' ) ) {
						$separator.css( {
							'opacity': '1'
						} );
					}

					if ( ! $target.hasClass( 'jet-unfold-state' ) ) {
						$mask.css( {
							'height': deviceHeight
						} );
					} else {
						$mask.css( {
							'height': $contentInner.outerHeight()
						} );
					}
					$trigger.css( 'display', 'flex' );
					updateMaskGradientClass();
				} else {
					$trigger.hide();
					$mask.css( {
						'height': '100%'
					} );
					$content.css( {
						'max-height': 'none'
					} );
					$separator.css( {
						'opacity': '0'
					} );
					updateMaskGradientClass();
				}
			}

			$( window ).on( 'resize.jetWidgetUnfold orientationchange.jetWidgetUnfold', JetTricksTools.debounce( 50, function(){
				initialLoaded = false;
				onLoaded();
			} ) );

			$button.keypress( function( e ) {
				if ( e.which == 13 ) {
					$button.click();
					return false;
				}
			} );

			$button.on( 'click.jetUnfold', function(e) {
				var $this         = $( this ),
					$buttonText   = $( '.jet-unfold__button-text', $this ),
					unfoldText    = $this.data( 'unfold-text' ),
					foldText      = $this.data( 'fold-text' ),
					$buttonIcon   = $( '.jet-unfold__button-icon', $this ),
					unfoldIcon    = $this.data( 'unfold-icon' ),
					foldIcon      = $this.data( 'fold-icon' ),
					contentHeight = $contentInner.outerHeight(),
					deviceHeight  = getDeviceHeight();

				e.preventDefault();

				if ( ! $target.hasClass( 'jet-unfold-state' ) ) {
					$target.addClass( 'jet-unfold-state' );

					$separator.css( {
						'opacity': '0'
					} );

					$buttonIcon.html( foldIcon );
					$buttonText.html( foldText );

					// Force recalculation of content height after state change
					setTimeout(function() {
						contentHeight = $contentInner.outerHeight();
						
						anime( {
							targets: $mask[0],
							height: contentHeight,
							duration: unfoldDuration['size'],
							easing: unfoldEasing,
							complete: function( anim ) {
								// Recalculate listing masonry.
								$( document ).trigger( 'jet-engine/listing/recalculate-masonry' );
							}
						} );
					}, 0);

					if ( 'true' === autoHide ) {
						autoHideTrigger = setTimeout( function() {
							$button.trigger( 'click' );
						}, autoHideTime * 1000 );
					}
				} else {
					clearTimeout( autoHideTrigger );

					$target.removeClass( 'jet-unfold-state' );

					$separator.css( {
						'opacity': '1'
					} );

					$buttonIcon.html( unfoldIcon );
					$buttonText.html( unfoldText );

					anime( {
						targets: $mask[0],
						height: deviceHeight,
						duration: foldDuration['size'],
						easing: foldEasing,
						complete: function( anim ) {
							if ( 'true' === settings['foldScrolling'] ) {
								$( 'html, body' ).animate( {
									scrollTop: $target.offset().top - settings['foldScrollOffset']['size']
								}, 'slow' );
							}

							// Recalculate listing masonry.
							$( document ).trigger( 'jet-engine/listing/recalculate-masonry' );
						}
					} );
				}
				
				updateMaskGradientClass();
			} );

			function getDeviceHeight() {
				if (heightControlType === 'word_count') {
					return calculateHeightByWordCount();
				}

				let device = elementorFrontend.getCurrentDeviceMode();
				let heightSettings;

				switch ( device ) {

					case 'mobile':
						heightSettings = settings.mask_height_mobile;
						break;

					case 'tablet':
						heightSettings = settings.mask_height_tablet;
						break;

					default:
						heightSettings = settings.mask_height;

				}

				if ( ! heightSettings || ! heightSettings.size || ! heightSettings.unit ) {
					heightSettings = settings.mask_height;
				}

				switch ( heightSettings.unit ) {

					case 'vh':
						return ( window.innerHeight * heightSettings.size ) / 100;

					case '%':
						let parentHeight = $contentInner.parent().height();
						return ( parentHeight * heightSettings.size ) / 100;

					default:
						return heightSettings.size;
				}
			}

			function getDeviceWordCount() {
				let device = elementorFrontend.getCurrentDeviceMode();
				let value;

				switch ( device ) {
					case 'mobile':
						value = settings.word_count_mobile;
						break;
					case 'tablet':
						value = settings.word_count_tablet;
						break;
					default:
						value = settings.word_count;
				}

				if ( value !== null && value !== undefined ) {
					return parseInt( value, 10 );
				}

				return 20;
			}
		},

		widgetHotspots: function( $scope ) {
			var $target   = $scope.find( '.jet-hotspots' ),
				$hotspots = $( '.jet-hotspots__item', $target),
				settings  = $target.data( 'settings' ),
				editMode  = Boolean( elementor.isEditMode() ),
				itemActiveClass = 'jet-hotspots__item--active';

			$target.imagesLoaded().progress( function() {
				$target.addClass( 'image-loaded' );
			} );

			$hotspots.each( function( index ) {
				var $this          = $( this ),
					horizontal     = $this.data( 'horizontal-position' ),
					vertical       = $this.data( 'vertical-position' ),
					tooltipWidth   = $this.data( 'tooltip-width' ) || null,
					showOnInit     = $this.data( 'show-on-init' ),
					itemSelector   = $this[0],
					options        = {};

				$this.css( {
					'left': horizontal + '%',
					'top': vertical + '%'
				} );

				if ( itemSelector._tippy ) {
					itemSelector._tippy.destroy();
				}

				options = {
					content: $this.data('tippy-content'),
					arrow: settings['tooltipArrow'] ? true : false,
					placement: settings['tooltipPlacement'],
					trigger: settings['tooltipTrigger'],
					appendTo: editMode ? document.body : $target[0],
					hideOnClick: 'manual' !== settings['tooltipTrigger'],
					maxWidth: 'none',
					offset: [0, settings['tooltipDistance']['size']],
					allowHTML: true,
					interactive: settings['tooltipInteractive'] ? true : false,
					onShow( instance ) {
						$( itemSelector ).addClass( itemActiveClass );

						if ( tooltipWidth ) {
							instance.popper.querySelector( '.tippy-box' ).style.width = tooltipWidth;
						}

					},
					onHidden( instance ) {
						$( itemSelector ).removeClass( itemActiveClass );
					}
				}

				if ( 'manual' != settings['tooltipTrigger'] ) {
					options['duration']  = [ settings['tooltipShowDuration']['size'], settings['tooltipHideDuration']['size'] ];
					options['animation'] = settings['tooltipAnimation'];
					options['delay']     = settings['tooltipDelay'];
				}

				tippy( [ itemSelector ], options );

				if ( 'manual' === settings['tooltipTrigger'] && itemSelector._tippy ) {
					itemSelector._tippy.show();
				}
			
				if ( ( showOnInit === 'yes' || settings['tooltipShowOnInit'] ) && itemSelector._tippy ) {
					itemSelector._tippy.show();
				}

			} );
		},

		columnEditorSettings: function( columnId ) {
			var editorElements = null,
				columnData     = {};

			if ( ! window.elementor.hasOwnProperty( 'elements' ) ) {
				return false;
			}

			editorElements = window.elementor.elements;

			if ( ! editorElements.models ) {
				return false;
			}

			$.each( editorElements.models, function( index, obj ) {

				$.each( obj.attributes.elements.models, function( index, obj ) {
					if ( columnId == obj.id ) {
						columnData = obj.attributes.settings.attributes;
					}
				} );

			} );

			return {
				'sticky': columnData['jet_tricks_column_sticky'] || false,
				'topSpacing': columnData['jet_tricks_top_spacing'] || 50,
				'bottomSpacing': columnData['jet_tricks_bottom_spacing'] || 50,
				'stickyOn': columnData['jet_tricks_column_sticky_on'] || [ 'desktop', 'tablet', 'mobile']
			}

		},

		sectionEditorSettings: function( $scope ) {
			var editorElements = null,
				sectionData     = {};

			if ( ! window.elementor.hasOwnProperty( 'elements' ) ) {
				return false;
			}

			sectionData = JetTricks.getElementorElementSettings( $scope );

			return {
				'particles': sectionData['section_jet_tricks_particles'] || 'false',
				'particles_json': sectionData['section_jet_tricks_particles_json'] || '',
			}

		}

	};

	$( window ).on( 'elementor/frontend/init', JetTricks.init );

	var JetTricksTools = {
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
		},

		widgetEditorSettings: function( widgetId ) {
			var editorElements = null,
				widgetData = {};
		
			if ( !window.elementor.hasOwnProperty( 'elements' ) || !window.elementor.elements.models ) {
				return false;
			}
		
			editorElements = window.elementor.elements;
		
			function findWidgetById( models, widgetId ) {
				let foundData = null;
		
				$.each( models, function( index, obj ) {
					if ( obj.id === widgetId ) {
						foundData = obj.attributes.settings.attributes;
						return false;
					}
					if ( obj.attributes.elements && obj.attributes.elements.models ) {
						foundData = findWidgetById( obj.attributes.elements.models, widgetId );
						if ( foundData ) {
							return false;
						}
					}
				});
		
				return foundData;
			}
		
			widgetData = findWidgetById( editorElements.models, widgetId ) || {};

			return {
				'speed': widgetData['jet_tricks_widget_parallax_speed'] || { 'size': 50, 'unit': '%'},
				'parallax': widgetData['jet_tricks_widget_parallax'] || 'false',
				'invert': widgetData['jet_tricks_widget_parallax_invert'] || 'false',
				'stickyOn': widgetData['jet_tricks_widget_parallax_on'] || [ 'desktop', 'tablet', 'mobile'],
				'satellite': widgetData['jet_tricks_widget_satellite'] || 'false',
				'satelliteType': widgetData['jet_tricks_widget_satellite_type'] || 'text',
				'satellitePosition': widgetData['jet_tricks_widget_satellite_position'] || 'top-center',
				'satelliteText': widgetData['jet_tricks_widget_satellite_text'] || 'Lorem Ipsum',
				'satelliteIcon': widgetData['selected_jet_tricks_widget_satellite_icon'] || '',
				'satelliteImage': widgetData['jet_tricks_widget_satellite_image'] || '',
				'satelliteLink': widgetData['jet_tricks_widget_satellite_link'] || '',
				'tooltip': widgetData['jet_tricks_widget_tooltip'] || 'false',
				'tooltipDescription': widgetData['jet_tricks_widget_tooltip_description'] || 'Lorem Ipsum',
				'tooltipPlacement': widgetData['jet_tricks_widget_tooltip_placement'] || 'top',
				'tooltipArrow': 'true' === widgetData['jet_tricks_widget_tooltip_arrow'] ? true : false,
				'xOffset': widgetData['jet_tricks_widget_tooltip_x_offset'] || 0,
				'yOffset': widgetData['jet_tricks_widget_tooltip_y_offset'] || 0,
				'tooltipAnimation': widgetData['jet_tricks_widget_tooltip_animation'] || 'shift-toward',
				'tooltipTrigger': widgetData['jet_tricks_widget_tooltip_trigger'] || 'mouseenter',
				'customSelector': widgetData['jet_tricks_widget_tooltip_custom_selector'] || '',
				'zIndex': widgetData['jet_tricks_widget_tooltip_z_index'] || '999',
				'delay': widgetData['jet_tricks_widget_tooltip_delay'] || '0',
				'followCursor': widgetData['jet_tricks_widget_tooltip_follow_cursor'] || 'false'
			}
		}
	}

	/**
	 * Jet jetViewMore Class
	 *
	 * @return {void}
	 */
	window.jetViewMore = function( $selector, settings ) {
		var self            = this,
			$window         = $( window ),
			$button         = $( '.jet-view-more__button', $selector ),
			defaultSettings = {
				sections: {},
				effect: 'move-up',
				showall: false
			},
			settings        = $.extend( {}, defaultSettings, settings ),
			sections        = settings['sections'],
			sectionsData    = {},
			editMode        = Boolean( elementor.isEditMode() ),
			readLess      = settings['read_less'] || false,
			readMoreLabel   = settings['read_more_label'],
			readLessLabel   = settings['read_less_label'],
			readMoreIcon    = settings['read_more_icon'],
			readLessIcon    = settings['read_less_icon'],
			hideAll         = settings['hide_all'] || false,
			isOpened        = false;

		/**
		 * Init
		 */
		self.init = function() {
			self.setSectionsData();

			if ( editMode ) {
				return false;
			}			

			function hideSection($section) {
				// Apply hide effect if available
				if (settings['hide_effect'] && settings['hide_effect'] !== 'none') {
					$section.addClass('view-more-hiding');
					$section.addClass('jet-tricks-' + settings['hide_effect'] + '-hide-effect');
					
					// Remove classes after animation completes
					(function($currentSection) {
						$currentSection.on('animationend', function animationEndHandler() {
							$currentSection.off('animationend', animationEndHandler);
							$currentSection.removeClass('view-more-hiding');
							$currentSection.removeClass('jet-tricks-' + settings['hide_effect'] + '-hide-effect');
							$currentSection.css('height', '');
							$currentSection.removeClass('view-more-visible');
							$currentSection.removeClass('jet-tricks-' + settings['effect'] + '-effect');
						});
					})($section);
				} else {
					$section.css('height', '');
					$section.removeClass('view-more-visible');
					$section.removeClass('jet-tricks-' + settings['effect'] + '-effect');
				}
			}

			function showAllSections() {
				for ( var section in sectionsData ) {
					var $section = sectionsData[ section ]['selector'];
					sectionsData[ section ]['visible'] = true;
					$section.css('height', $section[0].scrollHeight + 'px');
					$section.addClass( 'view-more-visible' );
					$section.addClass( 'jet-tricks-' + settings['effect'] + '-effect' );
				}
			}

			function hideAllSections() {
				for ( var section in sectionsData ) {
					var $section = sectionsData[ section ]['selector'];
					sectionsData[ section ]['visible'] = false;
					hideSection($section);
				}
			}

			function showNextSection() {
				for ( var section in sectionsData ) {
					var $section = sectionsData[ section ]['selector'];
					if ( !sectionsData[ section ]['visible'] ) {
						sectionsData[ section ]['visible'] = true;
						$section.css('height', $section[0].scrollHeight + 'px');
						$section.addClass( 'view-more-visible' );
						$section.addClass( 'jet-tricks-' + settings['effect'] + '-effect' );
						break;
					}
				}
			}

			function hideNextSection() {
				var sectionKeys = Object.keys(sectionsData).reverse();
				for (var i = 0; i < sectionKeys.length; i++) {
					var sectionKey = sectionKeys[i];
					var $section = sectionsData[sectionKey]['selector'];
					if (sectionsData[sectionKey]['visible']) {
						sectionsData[sectionKey]['visible'] = false;
						hideSection($section);
						break;
					}
				}
			}

			$button.on('click', function() {
				if (readLess) {
					if (!isOpened) {
						if (!settings.showall) {
							showNextSection();
							var allVisible = true;
							for (var section in sectionsData) {
								if (!sectionsData[section]['visible']) {
									allVisible = false;
									break;
								}
							}
							if (allVisible) {
								$button.find('.jet-view-more__label').text(readLessLabel);
								if (readLessIcon && readLessIcon.value) {
									$button.find('.jet-view-more__icon').html('<i class="' + readLessIcon.value + '"></i>');
								}
								$button.addClass('jet-view-more__button--read-less');
								isOpened = true;
							}
						} else {
							showAllSections();
							$button.find('.jet-view-more__label').text(readLessLabel);
							if (readLessIcon && readLessIcon.value) {
								$button.find('.jet-view-more__icon').html('<i class="' + readLessIcon.value + '"></i>');
							}
							$button.addClass('jet-view-more__button--read-less');
							isOpened = true;
						}
					} else {
						if (hideAll) {
							hideAllSections();
							$button.find('.jet-view-more__label').text(readMoreLabel);
							if (readMoreIcon && readMoreIcon.value) {
								$button.find('.jet-view-more__icon').html('<i class="' + readMoreIcon.value + '"></i>');
							}
							$button.removeClass('jet-view-more__button--read-less');
							isOpened = false;
						} else {
							hideNextSection();
							var allHidden = true;
							for (var section in sectionsData) {
								if (sectionsData[section]['visible']) {
									allHidden = false;
									break;
								}
							}
							if (allHidden) {
								$button.find('.jet-view-more__label').text(readMoreLabel);
								if (readMoreIcon && readMoreIcon.value) {
									$button.find('.jet-view-more__icon').html('<i class="' + readMoreIcon.value + '"></i>');
								}
								$button.removeClass('jet-view-more__button--read-less');
								isOpened = false;
							}
						}
					}
				} else {
					if (!settings.showall) {
						showNextSection();
					} else {
						showAllSections();
					}
					var allVisible = true;
					for (var section in sectionsData) {
						if (!sectionsData[section]['visible']) {
							allVisible = false;
							break;
						}
					}
					if (allVisible) {
						$button.css({ 'display': 'none' });
					}
				}
			});

			$button.keydown(function(e) {
				var $which = e.which || e.keyCode;
				if ($which == 13 || $which == 32) {
					e.preventDefault();
					if (readLess) {
						$button.trigger('click');
					} else {
						if (!settings.showall) {
							showNextSection();
						} else {
							showAllSections();
						}
						var allVisible = true;
						for (var section in sectionsData) {
							if (!sectionsData[section]['visible']) {
								allVisible = false;
								break;
							}
						}
						if (allVisible) {
							$button.css({ 'display': 'none' });
						}
					}
				}
			});
		};

		self.setSectionsData = function() {

			for ( var section in sections ) {
				var $selector = $( '#' + sections[ section ] );

				if ( ! editMode ) {
					$selector.addClass( 'jet-view-more-section' );
				} else {
					$selector.addClass( 'jet-view-more-section-edit-mode' );
				}

				sectionsData[ section ] = {
					'section_id': sections[ section ],
					'selector': $selector,
					'visible': false,
				}
			}
		};
	};

	/**
	 * [jetWidgetParallax description]
	 * @param  {[type]} $scope [description]
	 * @return {[type]}        [description]
	 */
	window.jetWidgetParallax = function( $scope ) {
		var self         = this,
			$target      = $scope,
			$section     = $scope.closest( '.elementor-top-section' ),
			widgetId     = $scope.data('id'),
			settings     = {},
			editMode     = Boolean( elementor.isEditMode() ),
			$window      = $( window ),
			isSafari     = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/),
			platform     = navigator.platform,
			safariClass  = isSafari ? 'is-safari' : '',
			macClass    = 'MacIntel' == platform ? ' is-mac' : '';

		/**
		 * Init
		 */
		self.init = function() {

			$scope.addClass( macClass );

			if ( ! editMode ) {
				settings = $scope.data( 'jet-tricks-settings' );
			} else {
				settings = JetTricksTools.widgetEditorSettings( widgetId );
			}

			if ( ! settings ) {
				return false;
			}

			if ( 'undefined' === typeof settings ) {
				return false;
			}

			if ( 'false' === settings['parallax'] || 'undefined' === typeof settings['parallax'] ) {
				return false;
			}

			$window.on( 'scroll.jetWidgetParallax resize.jetWidgetParallax', self.scrollHandler ).trigger( 'resize.jetWidgetParallax' );
		};

		self.scrollHandler = function( event ) {
			var speed             = +settings['speed']['size'] * 0.01,
				invert            = 'true' == settings['invert'] ? -1 : 1,
				winHeight         = $window.height(),
				winScrollTop      = $window.scrollTop(),
				offsetTop         = $scope.offset().top,
				thisHeight        = $scope.outerHeight(),
				sectionHeight     = $section.outerHeight(),
				positionDelta     = winScrollTop - offsetTop + ( winHeight / 2 ),
				abs               = positionDelta > 0 ? 1 : -1,
				posY              = abs * Math.pow( Math.abs( positionDelta ), 0.85 ),
				availableDevices  = settings['stickyOn'] || [],
				currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

			posY = invert * Math.ceil( speed * posY );

			if ( -1 !== availableDevices.indexOf( currentDeviceMode ) ) {
				$target.css( {
					'transform': 'translateY(' + posY + 'px)'
				} );
			} else {
				$target.css( {
					'transform': 'translateY(0)'
				} );
			}
		};
	};

	/**
	 * [jetWidgetSatellite description]
	 * @param  {[type]} $scope [description]
	 * @return {[type]}        [description]
	 */
	window.jetWidgetSatellite = function( $scope ) {
		var self     = this,
			widgetId = $scope.data('id'),
			settings = {},
			editMode = Boolean( elementor.isEditMode() );

		/**
		 * Init
		 */
		self.init = function() {

			if ( ! editMode ) {
				settings = $scope.data( 'jet-tricks-settings' );
			} else {
				settings = JetTricksTools.widgetEditorSettings( widgetId );
			}

			if ( ! settings || typeof settings !== 'object' ) {
				return false;
			}

			if ( 'false' === settings['satellite'] || 'undefined' === typeof settings['satellite'] ) {
				return false;
			}

			$scope.addClass( 'jet-satellite-widget' );

			$( '.jet-tricks-satellite', $scope ).addClass( 'jet-tricks-satellite--' + settings['satellitePosition'] );

			//satellite display in edit mode
			if ( editMode && $scope.find( '.jet-tricks-satellite' ).length === 0 ) {
				var html = '';
				var pos = settings['satellitePosition'] || 'top-center';

				var link = settings['satelliteLink'] || {};
				var linkStart = '', linkEnd = '';
				if ( link.url ) {
					linkStart = '<a class="jet-tricks-satellite__link">';
					linkEnd = '</a>';
				}

				if ( settings['satelliteType'] === 'text' && settings['satelliteText'] ) {
					html = '<div class="jet-tricks-satellite jet-tricks-satellite--' + pos + '"><div class="jet-tricks-satellite__inner"><div class="jet-tricks-satellite__text">' + linkStart + '<span>' + settings['satelliteText'] + '</span>' + linkEnd + '</div></div></div>';
				} else if ( settings['satelliteType'] === 'icon' && settings['satelliteIcon'] && settings['satelliteIcon'].value ) {
					html = '<div class="jet-tricks-satellite jet-tricks-satellite--' + pos + '"><div class="jet-tricks-satellite__inner"><div class="jet-tricks-satellite__icon">' + linkStart + '<div class="jet-tricks-satellite__icon-instance jet-tricks-icon"><i class="' + settings['satelliteIcon'].value + '"></i></div>' + linkEnd + '</div></div></div>';
				} else if ( settings['satelliteType'] === 'image' && settings['satelliteImage'] && settings['satelliteImage'].url ) {
					html = '<div class="jet-tricks-satellite jet-tricks-satellite--' + pos + '"><div class="jet-tricks-satellite__inner"><div class="jet-tricks-satellite__image">' + linkStart + '<img class="jet-tricks-satellite__image-instance" src="' + settings['satelliteImage'].url + '" alt="">' + linkEnd + '</div></div></div>';
				}

				if ( html ) {
					$scope.prepend(html);
				}
			}
		};
	};

	/**
	 * [jetWidgetTooltip description]
	 * @param  {[type]} $scope [description]
	 * @return {[type]}        [description]
	 */
	window.jetWidgetTooltip = function( $scope ) {
		var self            = this,
			widgetId        = $scope.data('id'),
			widgetSelector  = $scope[0],
			tooltipSelector = widgetSelector,
			settings        = {},
			editMode        = Boolean( elementor.isEditMode() ),
			delay,
			tooltipEvent    = editMode ? 'click' : 'mouseenter';

		/**
		 * Init
		 */
		self.init = function() {

			if ( ! editMode ) {
				settings = $scope.data( 'jet-tricks-settings' );
			} else {
				settings = JetTricksTools.widgetEditorSettings( widgetId );
			}

			if ( widgetSelector._tippy ) {
				widgetSelector._tippy.destroy();
			}

			if ( ! settings ) {
				return false;
			}

			if ( 'undefined' === typeof settings ) {
				return false;
			}

			if ( 'false' === settings['tooltip'] || 'undefined' === typeof settings['tooltip'] || '' === settings['tooltipDescription'] ) {
				return false;
			}

			$scope.addClass( 'jet-tooltip-widget' );

			if ( settings['customSelector'] ) {
				tooltipSelector = $( '.' + settings['customSelector'], $scope )[0];
			}

			if ( editMode && ! $( '#jet-tricks-tooltip-content-' + widgetId )[0] ) {
				var template = $( '<div>', {
					id: 'jet-tricks-tooltip-content-' + widgetId,
					class: 'jet-tooltip-widget__content'
				} );

				template.html( settings['tooltipDescription'] );
				$scope.append( template );
			}

			tippy(
				[ tooltipSelector ],
				{
					content: $scope.find( '.jet-tooltip-widget__content' )[0].innerHTML,
					allowHTML: true,
					//appendTo: widgetSelector,
					appendTo: editMode ? document.body : widgetSelector,
					arrow: settings['tooltipArrow'] ? true : false,
					placement: settings['tooltipPlacement'],
					offset: [ settings['xOffset'], settings['yOffset'] ],
					animation: settings['tooltipAnimation'],
					trigger: settings['tooltipTrigger'],
					interactive: settings['followCursor'] === 'false' || settings['followCursor'] === 'initial',
					zIndex: settings['zIndex'],
					maxWidth: 'none',
					delay: settings['delay']['size'] ? settings['delay']['size'] : 0,
					followCursor: settings['followCursor'] === 'false' ? false : (settings['followCursor'] === 'true' ? true : settings['followCursor']),
					onCreate: function (instance) {
						if ( editMode ) {
							var dataId = tooltipSelector.getAttribute('data-id');
							if ( dataId ) {
								instance.popper.classList.add( 'tippy-' + dataId );
							}
						}
					}
				}
			);

			if ( editMode && widgetSelector._tippy ) {
				widgetSelector._tippy.show();
			}

		};
	};
	
}( jQuery, window.elementorFrontend ) );
