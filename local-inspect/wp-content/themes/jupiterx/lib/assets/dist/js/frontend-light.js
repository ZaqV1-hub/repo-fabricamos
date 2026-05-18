// Theme
/**
 * @todo Probably place this config to other location.
 */
(function () {

  // Disable Zenscroll links smooth scroll.
  // Reference: https://github.com/zengabor/zenscroll#disabling-automatic-smoothing-on-local-links
  window.noZensmooth = true;

})();

// Check smooth scroll option status.
function is_smooth_scroll() {
  if (
    typeof jupiterxOptions === 'undefined' ||
    typeof jupiterxOptions.smoothScroll === 'undefined'
  ) {
    return null;
  }

  if (jupiterxOptions.smoothScroll == 0) {
    return false;
  }

  return true;
}

/* Simple JavaScript Inheritance
 * By John Resig https://johnresig.com/
 * MIT Licensed.
 */
// Inspired by base2 and Prototype
(function(){
  var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

  // The base Class implementation (does nothing)
  this.Class = function(){};

  // Create a new Class that inherits from this class
  Class.extend = function(prop) {
    var _super = this.prototype;

    // Instantiate a base class (but only create the instance,
    // don't run the init constructor)
    initializing = true;
    var prototype = new this();
    initializing = false;

    // Copy the properties over onto the new prototype
    for (var name in prop) {
      // Check if we're overwriting an existing function
      prototype[name] = typeof prop[name] == "function" &&
        typeof _super[name] == "function" && fnTest.test(prop[name]) ?
        (function(name, fn){
          return function() {
            var tmp = this._super;

            // Add a new ._super() method that is the same method
            // but on the super-class
            this._super = _super[name];

            // The method only need to be bound temporarily, so we
            // remove it when we're done executing
            var ret = fn.apply(this, arguments);
            this._super = tmp;

            return ret;
          };
        })(name, prop[name]) :
        prop[name];
    }

    // The dummy class constructor
    function Class() {
      // All construction is actually done in the init method
      if ( !initializing && this.init )
        this.init.apply(this, arguments);
    }

    // Populate our constructed prototype object
    Class.prototype = prototype;

    // Enforce the constructor to be what we expect
    Class.prototype.constructor = Class;

    // And make this class extendable
    Class.extend = arguments.callee;

    return Class;
  };
})();

/**
 * PubSub
 * Javascript implementation of the Publish/Subscribe pattern.
 *
 * @version 3.4.0
 * @author George Raptis <georapbox@gmail.com> (georapbox.github.io)
 * @homepage https://github.com/georapbox/PubSub#readme
 * @repository https://github.com/georapbox/PubSub.git
 * @license MIT
 */

!function(t,n,e){"use strict";"function"==typeof define&&define.amd?define(e):"undefined"!=typeof module&&module.exports?module.exports=e():n.PubSub=e("PubSub",n)}(0,this,function(t,n){"use strict";function e(t,n,e){var u;for(u in t)if(Object.prototype.hasOwnProperty.call(t,u)&&n&&!1===n.call(e,t[u],u,t))return;return t}function u(t){return function(){return this[t].apply(this,arguments)}}function r(t,n,e){for(var u,r,i=t._pubsub_topics,o=i[n]?i[n].slice(0):[],s=0,c=o.length;s<c;s+=1)r=o[s].token,(u=o[s]).callback(e,{name:n,token:r}),!0===u.once&&t.unsubscribe(r)}function i(t){var n=Array.prototype.slice.call(t,1);return n.length<=1?n[0]:n}function o(t,n,e,u){return!!t._pubsub_topics[n]&&(u?r(t,n,e):setTimeout(function(){r(t,n,e)},0),!0)}function s(){return this instanceof s?(this._pubsub_topics={},this._pubsub_uid=-1,this):new s}var c=(n||{})[t];return s.prototype.subscribe=function(t,n,e){var u=this._pubsub_topics,r=this._pubsub_uid+=1,i={};if("function"!=typeof n)throw new TypeError("When subscribing for an event, a callback function must be defined.");return u[t]||(u[t]=[]),i.token=r,i.callback=n,i.once=!!e,u[t].push(i),r},s.prototype.subscribeOnce=function(t,n){return this.subscribe(t,n,!0)},s.prototype.publish=function(t){return o(this,t,i(arguments),!1)},s.prototype.publishSync=function(t){return o(this,t,i(arguments),!0)},s.prototype.unsubscribe=function(t){var n,e,u=this._pubsub_topics,r=!1;for(n in u)if(Object.prototype.hasOwnProperty.call(u,n)&&u[n]){for(e=u[n].length;e;){if(e-=1,u[n][e].token===t)return u[n].splice(e,1),0===u[n].length&&delete u[n],t;n===t&&(u[n].splice(e,1),0===u[n].length&&delete u[n],r=!0)}if(!0===r)return t}return!1},s.prototype.unsubscribeAll=function(){return this._pubsub_topics={},this},s.prototype.hasSubscribers=function(t){var n=this._pubsub_topics,u=!1;return null==t?(e(n,function(t,n){if(n)return u=!0,!1}),u):Object.prototype.hasOwnProperty.call(n,t)},s.prototype.subscribers=function(){var t={};return e(this._pubsub_topics,function(n,e){t[e]=n.slice(0)}),t},s.prototype.subscribersByTopic=function(t){return this._pubsub_topics[t]?this._pubsub_topics[t].slice(0):[]},s.prototype.alias=function(t){return e(t,function(n,e){s.prototype[e]&&(s.prototype[t[e]]=u(e))}),this},s.noConflict=function(){return n&&(n[t]=c),s},s.version="3.4.0",s});

// Adapted from https://gist.github.com/paulirish/1579671 which derived from
// http://paulirish.com/2011/requestanimationframe-for-smart-animating/
// http://my.opera.com/emoller/blog/2011/12/20/requestanimationframe-for-smart-er-animating

// requestAnimationFrame polyfill by Erik Möller.
// Fixes from Paul Irish, Tino Zijdel, Andrew Mao, Klemen Slavič, Darius Bacon

// MIT license

(function() {
  'use strict';

  var vendors = ['webkit', 'moz'];
  for (var i = 0; i < vendors.length && !window.requestAnimationFrame; ++i) {
      var vp = vendors[i];
      window.requestAnimationFrame = window[vp+'RequestAnimationFrame'];
      window.cancelAnimationFrame = (window[vp+'CancelAnimationFrame']
      || window[vp+'CancelRequestAnimationFrame']);
  }
  if (/iP(ad|hone|od).*OS 6/.test(window.navigator.userAgent) // iOS6 is buggy
      || !window.requestAnimationFrame || !window.cancelAnimationFrame) {
      var lastTime = 0;
      window.requestAnimationFrame = function(callback) {
          var now = +new Date;
          var nextTime = Math.max(lastTime + 16, now);
          return setTimeout(function() { callback(lastTime = nextTime); },
              nextTime - now);
      };
      window.cancelAnimationFrame = clearTimeout;
  }
  else if (window.jQuery) {
      (function( $ ) {
          var animating;
          function raf() {
              if ( animating ) {
                  requestAnimationFrame( raf );
                  $.fx.tick();
              }
          }
          $.fx.timer = function( timer ) {
              if ( timer() && $.timers.push( timer ) && !animating ) {
                  animating = true;
                  raf();
              }
          };
          $.fx.stop = function() {
              animating = false;
          };
      }( jQuery ));
  }

  var hasPerformance = !!(window.performance && window.performance.now);
  // Add new wrapper for browsers that don't have performance
  if (!hasPerformance) {
      // Store reference to existing rAF and initial startTime
      var rAF = window.requestAnimationFrame,
          startTime = +new Date;

      // Override window rAF to include wrapped callback
      window.requestAnimationFrame = function (callback, element) {
          // Wrap the given callback to pass in performance timestamp
          var wrapped = function (timestamp) {
              // Get performance-style timestamp
              var performanceTimestamp = (timestamp < 1e12)
                  ? timestamp
                  : timestamp - startTime;

              return callback(performanceTimestamp);
          };

          // Call original rAF with wrapped callback
          rAF(wrapped, element);
      }
  }
}());

/* https://github.com/estrattonbailey/updwn */
!function(n,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):n.updwn=e()}(this,function(){var n=null,e=null,t=u(),i=t.x,r=t.y,o=[];function u(){return"undefined"==typeof window?{}:{x:window.innerWidth,y:window.pageYOffset}}function f(n){requestAnimationFrame(function(){for(var e=u(),t=e.x,f=e.y,d=0;d<o.length;d++)o[d]({y:f,prevY:r,x:t,prevX:i},n);r=f,i=t})}return function(t){var i=t.speed;void 0===i&&(i=20);var r=t.interval;void 0===r&&(r=100);var u,d=null,p=0,a=0,s=[],l=[];return u=function(n,e){var t=n.y,o=n.prevY;p=e.timeStamp-a,a=e.timeStamp;var u=(Math.abs(t-o)/p||0)*r>i;if(t>=o&&"down"!==d&&u){d="down";for(var f=0;f<l.length;f++)l[f]()}else if(t<=o&&"up"!==d&&u){d="up";for(var v=0;v<s.length;v++)s[v]()}},n||window.addEventListener("scroll",f),e||window.addEventListener("resize",f),o.indexOf(u)<0&&o.push(u),{up:function(n){return s.indexOf(n)<0&&s.push(n),function(){return s.splice(s.indexOf(n),1)}},down:function(n){return l.indexOf(n)<0&&l.push(n),function(){return l.splice(l.indexOf(n),1)}},get position(){return d}}}});

!function (i) { "use strict"; "function" == typeof define && define.amd ? define(["jquery"], i) : "undefined" != typeof exports ? module.exports = i(require("jquery")) : i(jQuery) }(function (i) { "use strict"; var e = window.Slick || {}; (e = function () { var e = 0; return function (t, o) { var s, n = this; n.defaults = { accessibility: !0, adaptiveHeight: !1, appendArrows: i(t), appendDots: i(t), arrows: !0, asNavFor: null, prevArrow: '<button class="slick-prev" aria-label="Previous" type="button">Previous</button>', nextArrow: '<button class="slick-next" aria-label="Next" type="button">Next</button>', autoplay: !1, autoplaySpeed: 3e3, centerMode: !1, centerPadding: "50px", cssEase: "ease", customPaging: function (e, t) { return i('<button type="button" />').text(t + 1) }, dots: !1, dotsClass: "slick-dots", draggable: !0, easing: "linear", edgeFriction: .35, fade: !1, focusOnSelect: !1, focusOnChange: !1, infinite: !0, initialSlide: 0, lazyLoad: "ondemand", mobileFirst: !1, pauseOnHover: !0, pauseOnFocus: !0, pauseOnDotsHover: !1, respondTo: "window", responsive: null, rows: 1, rtl: !1, slide: "", slidesPerRow: 1, slidesToShow: 1, slidesToScroll: 1, speed: 500, swipe: !0, swipeToSlide: !1, touchMove: !0, touchThreshold: 5, useCSS: !0, useTransform: !0, variableWidth: !1, vertical: !1, verticalSwiping: !1, waitForAnimate: !0, zIndex: 1e3 }, n.initials = { animating: !1, dragging: !1, autoPlayTimer: null, currentDirection: 0, currentLeft: null, currentSlide: 0, direction: 1, $dots: null, listWidth: null, listHeight: null, loadIndex: 0, $nextArrow: null, $prevArrow: null, scrolling: !1, slideCount: null, slideWidth: null, $slideTrack: null, $slides: null, sliding: !1, slideOffset: 0, swipeLeft: null, swiping: !1, $list: null, touchObject: {}, transformsEnabled: !1, unslicked: !1 }, i.extend(n, n.initials), n.activeBreakpoint = null, n.animType = null, n.animProp = null, n.breakpoints = [], n.breakpointSettings = [], n.cssTransitions = !1, n.focussed = !1, n.interrupted = !1, n.hidden = "hidden", n.paused = !0, n.positionProp = null, n.respondTo = null, n.rowCount = 1, n.shouldClick = !0, n.$slider = i(t), n.$slidesCache = null, n.transformType = null, n.transitionType = null, n.visibilityChange = "visibilitychange", n.windowWidth = 0, n.windowTimer = null, s = i(t).data("slick") || {}, n.options = i.extend({}, n.defaults, o, s), n.currentSlide = n.options.initialSlide, n.originalSettings = n.options, void 0 !== document.mozHidden ? (n.hidden = "mozHidden", n.visibilityChange = "mozvisibilitychange") : void 0 !== document.webkitHidden && (n.hidden = "webkitHidden", n.visibilityChange = "webkitvisibilitychange"), n.autoPlay = i.proxy(n.autoPlay, n), n.autoPlayClear = i.proxy(n.autoPlayClear, n), n.autoPlayIterator = i.proxy(n.autoPlayIterator, n), n.changeSlide = i.proxy(n.changeSlide, n), n.clickHandler = i.proxy(n.clickHandler, n), n.selectHandler = i.proxy(n.selectHandler, n), n.setPosition = i.proxy(n.setPosition, n), n.swipeHandler = i.proxy(n.swipeHandler, n), n.dragHandler = i.proxy(n.dragHandler, n), n.keyHandler = i.proxy(n.keyHandler, n), n.instanceUid = e++ , n.htmlExpr = /^(?:\s*(<[\w\W]+>)[^>]*)$/, n.registerBreakpoints(), n.init(!0) } }()).prototype.activateADA = function () { this.$slideTrack.find(".slick-active").attr({ "aria-hidden": "false" }).find("a, input, button, select").attr({ tabindex: "0" }) }, e.prototype.addSlide = e.prototype.slickAdd = function (e, t, o) { var s = this; if ("boolean" == typeof t) o = t, t = null; else if (t < 0 || t >= s.slideCount) return !1; s.unload(), "number" == typeof t ? 0 === t && 0 === s.$slides.length ? i(e).appendTo(s.$slideTrack) : o ? i(e).insertBefore(s.$slides.eq(t)) : i(e).insertAfter(s.$slides.eq(t)) : !0 === o ? i(e).prependTo(s.$slideTrack) : i(e).appendTo(s.$slideTrack), s.$slides = s.$slideTrack.children(this.options.slide), s.$slideTrack.children(this.options.slide).detach(), s.$slideTrack.append(s.$slides), s.$slides.each(function (e, t) { i(t).attr("data-slick-index", e) }), s.$slidesCache = s.$slides, s.reinit() }, e.prototype.animateHeight = function () { var i = this; if (1 === i.options.slidesToShow && !0 === i.options.adaptiveHeight && !1 === i.options.vertical) { var e = i.$slides.eq(i.currentSlide).outerHeight(!0); i.$list.animate({ height: e }, i.options.speed) } }, e.prototype.animateSlide = function (e, t) { var o = {}, s = this; s.animateHeight(), !0 === s.options.rtl && !1 === s.options.vertical && (e = -e), !1 === s.transformsEnabled ? !1 === s.options.vertical ? s.$slideTrack.animate({ left: e }, s.options.speed, s.options.easing, t) : s.$slideTrack.animate({ top: e }, s.options.speed, s.options.easing, t) : !1 === s.cssTransitions ? (!0 === s.options.rtl && (s.currentLeft = -s.currentLeft), i({ animStart: s.currentLeft }).animate({ animStart: e }, { duration: s.options.speed, easing: s.options.easing, step: function (i) { i = Math.ceil(i), !1 === s.options.vertical ? (o[s.animType] = "translate(" + i + "px, 0px)", s.$slideTrack.css(o)) : (o[s.animType] = "translate(0px," + i + "px)", s.$slideTrack.css(o)) }, complete: function () { t && t.call() } })) : (s.applyTransition(), e = Math.ceil(e), !1 === s.options.vertical ? o[s.animType] = "translate3d(" + e + "px, 0px, 0px)" : o[s.animType] = "translate3d(0px," + e + "px, 0px)", s.$slideTrack.css(o), t && setTimeout(function () { s.disableTransition(), t.call() }, s.options.speed)) }, e.prototype.getNavTarget = function () { var e = this, t = e.options.asNavFor; return t && null !== t && (t = i(t).not(e.$slider)), t }, e.prototype.asNavFor = function (e) { var t = this.getNavTarget(); null !== t && "object" == typeof t && t.each(function () { var t = i(this).slick("getSlick"); t.unslicked || t.slideHandler(e, !0) }) }, e.prototype.applyTransition = function (i) { var e = this, t = {}; !1 === e.options.fade ? t[e.transitionType] = e.transformType + " " + e.options.speed + "ms " + e.options.cssEase : t[e.transitionType] = "opacity " + e.options.speed + "ms " + e.options.cssEase, !1 === e.options.fade ? e.$slideTrack.css(t) : e.$slides.eq(i).css(t) }, e.prototype.autoPlay = function () { var i = this; i.autoPlayClear(), i.slideCount > i.options.slidesToShow && (i.autoPlayTimer = setInterval(i.autoPlayIterator, i.options.autoplaySpeed)) }, e.prototype.autoPlayClear = function () { var i = this; i.autoPlayTimer && clearInterval(i.autoPlayTimer) }, e.prototype.autoPlayIterator = function () { var i = this, e = i.currentSlide + i.options.slidesToScroll; i.paused || i.interrupted || i.focussed || (!1 === i.options.infinite && (1 === i.direction && i.currentSlide + 1 === i.slideCount - 1 ? i.direction = 0 : 0 === i.direction && (e = i.currentSlide - i.options.slidesToScroll, i.currentSlide - 1 == 0 && (i.direction = 1))), i.slideHandler(e)) }, e.prototype.buildArrows = function () { var e = this; !0 === e.options.arrows && (e.$prevArrow = i(e.options.prevArrow).addClass("slick-arrow"), e.$nextArrow = i(e.options.nextArrow).addClass("slick-arrow"), e.slideCount > e.options.slidesToShow ? (e.$prevArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"), e.$nextArrow.removeClass("slick-hidden").removeAttr("aria-hidden tabindex"), e.htmlExpr.test(e.options.prevArrow) && e.$prevArrow.prependTo(e.options.appendArrows), e.htmlExpr.test(e.options.nextArrow) && e.$nextArrow.appendTo(e.options.appendArrows), !0 !== e.options.infinite && e.$prevArrow.addClass("slick-disabled").attr("aria-disabled", "true")) : e.$prevArrow.add(e.$nextArrow).addClass("slick-hidden").attr({ "aria-disabled": "true", tabindex: "-1" })) }, e.prototype.buildDots = function () { var e, t, o = this; if (!0 === o.options.dots) { for (o.$slider.addClass("slick-dotted"), t = i("<ul />").addClass(o.options.dotsClass), e = 0; e <= o.getDotCount(); e += 1)t.append(i("<li />").append(o.options.customPaging.call(this, o, e))); o.$dots = t.appendTo(o.options.appendDots), o.$dots.find("li").first().addClass("slick-active") } }, e.prototype.buildOut = function () { var e = this; e.$slides = e.$slider.children(e.options.slide + ":not(.slick-cloned)").addClass("slick-slide"), e.slideCount = e.$slides.length, e.$slides.each(function (e, t) { i(t).attr("data-slick-index", e).data("originalStyling", i(t).attr("style") || "") }), e.$slider.addClass("slick-slider"), e.$slideTrack = 0 === e.slideCount ? i('<div class="slick-track"/>').appendTo(e.$slider) : e.$slides.wrapAll('<div class="slick-track"/>').parent(), e.$list = e.$slideTrack.wrap('<div class="slick-list"/>').parent(), e.$slideTrack.css("opacity", 0), !0 !== e.options.centerMode && !0 !== e.options.swipeToSlide || (e.options.slidesToScroll = 1), i("img[data-lazy]", e.$slider).not("[src]").addClass("slick-loading"), e.setupInfinite(), e.buildArrows(), e.buildDots(), e.updateDots(), e.setSlideClasses("number" == typeof e.currentSlide ? e.currentSlide : 0), !0 === e.options.draggable && e.$list.addClass("draggable") }, e.prototype.buildRows = function () { var i, e, t, o, s, n, r, l = this; if (o = document.createDocumentFragment(), n = l.$slider.children(), l.options.rows > 1) { for (r = l.options.slidesPerRow * l.options.rows, s = Math.ceil(n.length / r), i = 0; i < s; i++) { var d = document.createElement("div"); for (e = 0; e < l.options.rows; e++) { var a = document.createElement("div"); for (t = 0; t < l.options.slidesPerRow; t++) { var c = i * r + (e * l.options.slidesPerRow + t); n.get(c) && a.appendChild(n.get(c)) } d.appendChild(a) } o.appendChild(d) } l.$slider.empty().append(o), l.$slider.children().children().children().css({ width: 100 / l.options.slidesPerRow + "%", display: "inline-block" }) } }, e.prototype.checkResponsive = function (e, t) { var o, s, n, r = this, l = !1, d = r.$slider.width(), a = window.innerWidth || i(window).width(); if ("window" === r.respondTo ? n = a : "slider" === r.respondTo ? n = d : "min" === r.respondTo && (n = Math.min(a, d)), r.options.responsive && r.options.responsive.length && null !== r.options.responsive) { s = null; for (o in r.breakpoints) r.breakpoints.hasOwnProperty(o) && (!1 === r.originalSettings.mobileFirst ? n < r.breakpoints[o] && (s = r.breakpoints[o]) : n > r.breakpoints[o] && (s = r.breakpoints[o])); null !== s ? null !== r.activeBreakpoint ? (s !== r.activeBreakpoint || t) && (r.activeBreakpoint = s, "unslick" === r.breakpointSettings[s] ? r.unslick(s) : (r.options = i.extend({}, r.originalSettings, r.breakpointSettings[s]), !0 === e && (r.currentSlide = r.options.initialSlide), r.refresh(e)), l = s) : (r.activeBreakpoint = s, "unslick" === r.breakpointSettings[s] ? r.unslick(s) : (r.options = i.extend({}, r.originalSettings, r.breakpointSettings[s]), !0 === e && (r.currentSlide = r.options.initialSlide), r.refresh(e)), l = s) : null !== r.activeBreakpoint && (r.activeBreakpoint = null, r.options = r.originalSettings, !0 === e && (r.currentSlide = r.options.initialSlide), r.refresh(e), l = s), e || !1 === l || r.$slider.trigger("breakpoint", [r, l]) } }, e.prototype.changeSlide = function (e, t) { var o, s, n, r = this, l = i(e.currentTarget); switch (l.is("a") && e.preventDefault(), l.is("li") || (l = l.closest("li")), n = r.slideCount % r.options.slidesToScroll != 0, o = n ? 0 : (r.slideCount - r.currentSlide) % r.options.slidesToScroll, e.data.message) { case "previous": s = 0 === o ? r.options.slidesToScroll : r.options.slidesToShow - o, r.slideCount > r.options.slidesToShow && r.slideHandler(r.currentSlide - s, !1, t); break; case "next": s = 0 === o ? r.options.slidesToScroll : o, r.slideCount > r.options.slidesToShow && r.slideHandler(r.currentSlide + s, !1, t); break; case "index": var d = 0 === e.data.index ? 0 : e.data.index || l.index() * r.options.slidesToScroll; r.slideHandler(r.checkNavigable(d), !1, t), l.children().trigger("focus"); break; default: return } }, e.prototype.checkNavigable = function (i) { var e, t; if (e = this.getNavigableIndexes(), t = 0, i > e[e.length - 1]) i = e[e.length - 1]; else for (var o in e) { if (i < e[o]) { i = t; break } t = e[o] } return i }, e.prototype.cleanUpEvents = function () { var e = this; e.options.dots && null !== e.$dots && (i("li", e.$dots).off("click.slick", e.changeSlide).off("mouseenter.slick", i.proxy(e.interrupt, e, !0)).off("mouseleave.slick", i.proxy(e.interrupt, e, !1)), !0 === e.options.accessibility && e.$dots.off("keydown.slick", e.keyHandler)), e.$slider.off("focus.slick blur.slick"), !0 === e.options.arrows && e.slideCount > e.options.slidesToShow && (e.$prevArrow && e.$prevArrow.off("click.slick", e.changeSlide), e.$nextArrow && e.$nextArrow.off("click.slick", e.changeSlide), !0 === e.options.accessibility && (e.$prevArrow && e.$prevArrow.off("keydown.slick", e.keyHandler), e.$nextArrow && e.$nextArrow.off("keydown.slick", e.keyHandler))), e.$list.off("touchstart.slick mousedown.slick", e.swipeHandler), e.$list.off("touchmove.slick mousemove.slick", e.swipeHandler), e.$list.off("touchend.slick mouseup.slick", e.swipeHandler), e.$list.off("touchcancel.slick mouseleave.slick", e.swipeHandler), e.$list.off("click.slick", e.clickHandler), i(document).off(e.visibilityChange, e.visibility), e.cleanUpSlideEvents(), !0 === e.options.accessibility && e.$list.off("keydown.slick", e.keyHandler), !0 === e.options.focusOnSelect && i(e.$slideTrack).children().off("click.slick", e.selectHandler), i(window).off("orientationchange.slick.slick-" + e.instanceUid, e.orientationChange), i(window).off("resize.slick.slick-" + e.instanceUid, e.resize), i("[draggable!=true]", e.$slideTrack).off("dragstart", e.preventDefault), i(window).off("load.slick.slick-" + e.instanceUid, e.setPosition) }, e.prototype.cleanUpSlideEvents = function () { var e = this; e.$list.off("mouseenter.slick", i.proxy(e.interrupt, e, !0)), e.$list.off("mouseleave.slick", i.proxy(e.interrupt, e, !1)) }, e.prototype.cleanUpRows = function () { var i, e = this; e.options.rows > 1 && ((i = e.$slides.children().children()).removeAttr("style"), e.$slider.empty().append(i)) }, e.prototype.clickHandler = function (i) { !1 === this.shouldClick && (i.stopImmediatePropagation(), i.stopPropagation(), i.preventDefault()) }, e.prototype.destroy = function (e) { var t = this; t.autoPlayClear(), t.touchObject = {}, t.cleanUpEvents(), i(".slick-cloned", t.$slider).detach(), t.$dots && t.$dots.remove(), t.$prevArrow && t.$prevArrow.length && (t.$prevArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display", ""), t.htmlExpr.test(t.options.prevArrow) && t.$prevArrow.remove()), t.$nextArrow && t.$nextArrow.length && (t.$nextArrow.removeClass("slick-disabled slick-arrow slick-hidden").removeAttr("aria-hidden aria-disabled tabindex").css("display", ""), t.htmlExpr.test(t.options.nextArrow) && t.$nextArrow.remove()), t.$slides && (t.$slides.removeClass("slick-slide slick-active slick-center slick-visible slick-current").removeAttr("aria-hidden").removeAttr("data-slick-index").each(function () { i(this).attr("style", i(this).data("originalStyling")) }), t.$slideTrack.children(this.options.slide).detach(), t.$slideTrack.detach(), t.$list.detach(), t.$slider.append(t.$slides)), t.cleanUpRows(), t.$slider.removeClass("slick-slider"), t.$slider.removeClass("slick-initialized"), t.$slider.removeClass("slick-dotted"), t.unslicked = !0, e || t.$slider.trigger("destroy", [t]) }, e.prototype.disableTransition = function (i) { var e = this, t = {}; t[e.transitionType] = "", !1 === e.options.fade ? e.$slideTrack.css(t) : e.$slides.eq(i).css(t) }, e.prototype.fadeSlide = function (i, e) { var t = this; !1 === t.cssTransitions ? (t.$slides.eq(i).css({ zIndex: t.options.zIndex }), t.$slides.eq(i).animate({ opacity: 1 }, t.options.speed, t.options.easing, e)) : (t.applyTransition(i), t.$slides.eq(i).css({ opacity: 1, zIndex: t.options.zIndex }), e && setTimeout(function () { t.disableTransition(i), e.call() }, t.options.speed)) }, e.prototype.fadeSlideOut = function (i) { var e = this; !1 === e.cssTransitions ? e.$slides.eq(i).animate({ opacity: 0, zIndex: e.options.zIndex - 2 }, e.options.speed, e.options.easing) : (e.applyTransition(i), e.$slides.eq(i).css({ opacity: 0, zIndex: e.options.zIndex - 2 })) }, e.prototype.filterSlides = e.prototype.slickFilter = function (i) { var e = this; null !== i && (e.$slidesCache = e.$slides, e.unload(), e.$slideTrack.children(this.options.slide).detach(), e.$slidesCache.filter(i).appendTo(e.$slideTrack), e.reinit()) }, e.prototype.focusHandler = function () { var e = this; e.$slider.off("focus.slick blur.slick").on("focus.slick blur.slick", "*", function (t) { t.stopImmediatePropagation(); var o = i(this); setTimeout(function () { e.options.pauseOnFocus && (e.focussed = o.is(":focus"), e.autoPlay()) }, 0) }) }, e.prototype.getCurrent = e.prototype.slickCurrentSlide = function () { return this.currentSlide }, e.prototype.getDotCount = function () { var i = this, e = 0, t = 0, o = 0; if (!0 === i.options.infinite) if (i.slideCount <= i.options.slidesToShow)++o; else for (; e < i.slideCount;)++o, e = t + i.options.slidesToScroll, t += i.options.slidesToScroll <= i.options.slidesToShow ? i.options.slidesToScroll : i.options.slidesToShow; else if (!0 === i.options.centerMode) o = i.slideCount; else if (i.options.asNavFor) for (; e < i.slideCount;)++o, e = t + i.options.slidesToScroll, t += i.options.slidesToScroll <= i.options.slidesToShow ? i.options.slidesToScroll : i.options.slidesToShow; else o = 1 + Math.ceil((i.slideCount - i.options.slidesToShow) / i.options.slidesToScroll); return o - 1 }, e.prototype.getLeft = function (i) { var e, t, o, s, n = this, r = 0; return n.slideOffset = 0, t = n.$slides.first().outerHeight(!0), !0 === n.options.infinite ? (n.slideCount > n.options.slidesToShow && (n.slideOffset = n.slideWidth * n.options.slidesToShow * -1, s = -1, !0 === n.options.vertical && !0 === n.options.centerMode && (2 === n.options.slidesToShow ? s = -1.5 : 1 === n.options.slidesToShow && (s = -2)), r = t * n.options.slidesToShow * s), n.slideCount % n.options.slidesToScroll != 0 && i + n.options.slidesToScroll > n.slideCount && n.slideCount > n.options.slidesToShow && (i > n.slideCount ? (n.slideOffset = (n.options.slidesToShow - (i - n.slideCount)) * n.slideWidth * -1, r = (n.options.slidesToShow - (i - n.slideCount)) * t * -1) : (n.slideOffset = n.slideCount % n.options.slidesToScroll * n.slideWidth * -1, r = n.slideCount % n.options.slidesToScroll * t * -1))) : i + n.options.slidesToShow > n.slideCount && (n.slideOffset = (i + n.options.slidesToShow - n.slideCount) * n.slideWidth, r = (i + n.options.slidesToShow - n.slideCount) * t), n.slideCount <= n.options.slidesToShow && (n.slideOffset = 0, r = 0), !0 === n.options.centerMode && n.slideCount <= n.options.slidesToShow ? n.slideOffset = n.slideWidth * Math.floor(n.options.slidesToShow) / 2 - n.slideWidth * n.slideCount / 2 : !0 === n.options.centerMode && !0 === n.options.infinite ? n.slideOffset += n.slideWidth * Math.floor(n.options.slidesToShow / 2) - n.slideWidth : !0 === n.options.centerMode && (n.slideOffset = 0, n.slideOffset += n.slideWidth * Math.floor(n.options.slidesToShow / 2)), e = !1 === n.options.vertical ? i * n.slideWidth * -1 + n.slideOffset : i * t * -1 + r, !0 === n.options.variableWidth && (o = n.slideCount <= n.options.slidesToShow || !1 === n.options.infinite ? n.$slideTrack.children(".slick-slide").eq(i) : n.$slideTrack.children(".slick-slide").eq(i + n.options.slidesToShow), e = !0 === n.options.rtl ? o[0] ? -1 * (n.$slideTrack.width() - o[0].offsetLeft - o.width()) : 0 : o[0] ? -1 * o[0].offsetLeft : 0, !0 === n.options.centerMode && (o = n.slideCount <= n.options.slidesToShow || !1 === n.options.infinite ? n.$slideTrack.children(".slick-slide").eq(i) : n.$slideTrack.children(".slick-slide").eq(i + n.options.slidesToShow + 1), e = !0 === n.options.rtl ? o[0] ? -1 * (n.$slideTrack.width() - o[0].offsetLeft - o.width()) : 0 : o[0] ? -1 * o[0].offsetLeft : 0, e += (n.$list.width() - o.outerWidth()) / 2)), e }, e.prototype.getOption = e.prototype.slickGetOption = function (i) { return this.options[i] }, e.prototype.getNavigableIndexes = function () { var i, e = this, t = 0, o = 0, s = []; for (!1 === e.options.infinite ? i = e.slideCount : (t = -1 * e.options.slidesToScroll, o = -1 * e.options.slidesToScroll, i = 2 * e.slideCount); t < i;)s.push(t), t = o + e.options.slidesToScroll, o += e.options.slidesToScroll <= e.options.slidesToShow ? e.options.slidesToScroll : e.options.slidesToShow; return s }, e.prototype.getSlick = function () { return this }, e.prototype.getSlideCount = function () { var e, t, o = this; return t = !0 === o.options.centerMode ? o.slideWidth * Math.floor(o.options.slidesToShow / 2) : 0, !0 === o.options.swipeToSlide ? (o.$slideTrack.find(".slick-slide").each(function (s, n) { if (n.offsetLeft - t + i(n).outerWidth() / 2 > -1 * o.swipeLeft) return e = n, !1 }), Math.abs(i(e).attr("data-slick-index") - o.currentSlide) || 1) : o.options.slidesToScroll }, e.prototype.goTo = e.prototype.slickGoTo = function (i, e) { this.changeSlide({ data: { message: "index", index: parseInt(i) } }, e) }, e.prototype.init = function (e) { var t = this; i(t.$slider).hasClass("slick-initialized") || (i(t.$slider).addClass("slick-initialized"), t.buildRows(), t.buildOut(), t.setProps(), t.startLoad(), t.loadSlider(), t.initializeEvents(), t.updateArrows(), t.updateDots(), t.checkResponsive(!0), t.focusHandler()), e && t.$slider.trigger("init", [t]), !0 === t.options.accessibility && t.initADA(), t.options.autoplay && (t.paused = !1, t.autoPlay()) }, e.prototype.initADA = function () { var e = this, t = Math.ceil(e.slideCount / e.options.slidesToShow), o = e.getNavigableIndexes().filter(function (i) { return i >= 0 && i < e.slideCount }); e.$slides.add(e.$slideTrack.find(".slick-cloned")).attr({ "aria-hidden": "true", tabindex: "-1" }).find("a, input, button, select").attr({ tabindex: "-1" }), null !== e.$dots && (e.$slides.not(e.$slideTrack.find(".slick-cloned")).each(function (t) { var s = o.indexOf(t); i(this).attr({ role: "tabpanel", id: "slick-slide" + e.instanceUid + t, tabindex: -1 }), -1 !== s && i(this).attr({ "aria-describedby": "slick-slide-control" + e.instanceUid + s }) }), e.$dots.attr("role", "tablist").find("li").each(function (s) { var n = o[s]; i(this).attr({ role: "presentation" }), i(this).find("button").first().attr({ role: "tab", id: "slick-slide-control" + e.instanceUid + s, "aria-controls": "slick-slide" + e.instanceUid + n, "aria-label": s + 1 + " of " + t, "aria-selected": null, tabindex: "-1" }) }).eq(e.currentSlide).find("button").attr({ "aria-selected": "true", tabindex: "0" }).end()); for (var s = e.currentSlide, n = s + e.options.slidesToShow; s < n; s++)e.$slides.eq(s).attr("tabindex", 0); e.activateADA() }, e.prototype.initArrowEvents = function () { var i = this; !0 === i.options.arrows && i.slideCount > i.options.slidesToShow && (i.$prevArrow.off("click.slick").on("click.slick", { message: "previous" }, i.changeSlide), i.$nextArrow.off("click.slick").on("click.slick", { message: "next" }, i.changeSlide), !0 === i.options.accessibility && (i.$prevArrow.on("keydown.slick", i.keyHandler), i.$nextArrow.on("keydown.slick", i.keyHandler))) }, e.prototype.initDotEvents = function () { var e = this; !0 === e.options.dots && (i("li", e.$dots).on("click.slick", { message: "index" }, e.changeSlide), !0 === e.options.accessibility && e.$dots.on("keydown.slick", e.keyHandler)), !0 === e.options.dots && !0 === e.options.pauseOnDotsHover && i("li", e.$dots).on("mouseenter.slick", i.proxy(e.interrupt, e, !0)).on("mouseleave.slick", i.proxy(e.interrupt, e, !1)) }, e.prototype.initSlideEvents = function () { var e = this; e.options.pauseOnHover && (e.$list.on("mouseenter.slick", i.proxy(e.interrupt, e, !0)), e.$list.on("mouseleave.slick", i.proxy(e.interrupt, e, !1))) }, e.prototype.initializeEvents = function () { var e = this; e.initArrowEvents(), e.initDotEvents(), e.initSlideEvents(), e.$list.on("touchstart.slick mousedown.slick", { action: "start" }, e.swipeHandler), e.$list.on("touchmove.slick mousemove.slick", { action: "move" }, e.swipeHandler), e.$list.on("touchend.slick mouseup.slick", { action: "end" }, e.swipeHandler), e.$list.on("touchcancel.slick mouseleave.slick", { action: "end" }, e.swipeHandler), e.$list.on("click.slick", e.clickHandler), i(document).on(e.visibilityChange, i.proxy(e.visibility, e)), !0 === e.options.accessibility && e.$list.on("keydown.slick", e.keyHandler), !0 === e.options.focusOnSelect && i(e.$slideTrack).children().on("click.slick", e.selectHandler), i(window).on("orientationchange.slick.slick-" + e.instanceUid, i.proxy(e.orientationChange, e)), i(window).on("resize.slick.slick-" + e.instanceUid, i.proxy(e.resize, e)), i("[draggable!=true]", e.$slideTrack).on("dragstart", e.preventDefault), i(window).on("load.slick.slick-" + e.instanceUid, e.setPosition), i(e.setPosition) }, e.prototype.initUI = function () { var i = this; !0 === i.options.arrows && i.slideCount > i.options.slidesToShow && (i.$prevArrow.show(), i.$nextArrow.show()), !0 === i.options.dots && i.slideCount > i.options.slidesToShow && i.$dots.show() }, e.prototype.keyHandler = function (i) { var e = this; i.target.tagName.match("TEXTAREA|INPUT|SELECT") || (37 === i.keyCode && !0 === e.options.accessibility ? e.changeSlide({ data: { message: !0 === e.options.rtl ? "next" : "previous" } }) : 39 === i.keyCode && !0 === e.options.accessibility && e.changeSlide({ data: { message: !0 === e.options.rtl ? "previous" : "next" } })) }, e.prototype.lazyLoad = function () { function e(e) { i("img[data-lazy]", e).each(function () { var e = i(this), t = i(this).attr("data-lazy"), o = i(this).attr("data-srcset"), s = i(this).attr("data-sizes") || n.$slider.attr("data-sizes"), r = document.createElement("img"); r.onload = function () { e.animate({ opacity: 0 }, 100, function () { o && (e.attr("srcset", o), s && e.attr("sizes", s)), e.attr("src", t).animate({ opacity: 1 }, 200, function () { e.removeAttr("data-lazy data-srcset data-sizes").removeClass("slick-loading") }), n.$slider.trigger("lazyLoaded", [n, e, t]) }) }, r.onerror = function () { e.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"), n.$slider.trigger("lazyLoadError", [n, e, t]) }, r.src = t }) } var t, o, s, n = this; if (!0 === n.options.centerMode ? !0 === n.options.infinite ? s = (o = n.currentSlide + (n.options.slidesToShow / 2 + 1)) + n.options.slidesToShow + 2 : (o = Math.max(0, n.currentSlide - (n.options.slidesToShow / 2 + 1)), s = n.options.slidesToShow / 2 + 1 + 2 + n.currentSlide) : (o = n.options.infinite ? n.options.slidesToShow + n.currentSlide : n.currentSlide, s = Math.ceil(o + n.options.slidesToShow), !0 === n.options.fade && (o > 0 && o-- , s <= n.slideCount && s++)), t = n.$slider.find(".slick-slide").slice(o, s), "anticipated" === n.options.lazyLoad) for (var r = o - 1, l = s, d = n.$slider.find(".slick-slide"), a = 0; a < n.options.slidesToScroll; a++)r < 0 && (r = n.slideCount - 1), t = (t = t.add(d.eq(r))).add(d.eq(l)), r-- , l++; e(t), n.slideCount <= n.options.slidesToShow ? e(n.$slider.find(".slick-slide")) : n.currentSlide >= n.slideCount - n.options.slidesToShow ? e(n.$slider.find(".slick-cloned").slice(0, n.options.slidesToShow)) : 0 === n.currentSlide && e(n.$slider.find(".slick-cloned").slice(-1 * n.options.slidesToShow)) }, e.prototype.loadSlider = function () { var i = this; i.setPosition(), i.$slideTrack.css({ opacity: 1 }), i.$slider.removeClass("slick-loading"), i.initUI(), "progressive" === i.options.lazyLoad && i.progressiveLazyLoad() }, e.prototype.next = e.prototype.slickNext = function () { this.changeSlide({ data: { message: "next" } }) }, e.prototype.orientationChange = function () { var i = this; i.checkResponsive(), i.setPosition() }, e.prototype.pause = e.prototype.slickPause = function () { var i = this; i.autoPlayClear(), i.paused = !0 }, e.prototype.play = e.prototype.slickPlay = function () { var i = this; i.autoPlay(), i.options.autoplay = !0, i.paused = !1, i.focussed = !1, i.interrupted = !1 }, e.prototype.postSlide = function (e) { var t = this; t.unslicked || (t.$slider.trigger("afterChange", [t, e]), t.animating = !1, t.slideCount > t.options.slidesToShow && t.setPosition(), t.swipeLeft = null, t.options.autoplay && t.autoPlay(), !0 === t.options.accessibility && (t.initADA(), t.options.focusOnChange && i(t.$slides.get(t.currentSlide)).attr("tabindex", 0).focus())) }, e.prototype.prev = e.prototype.slickPrev = function () { this.changeSlide({ data: { message: "previous" } }) }, e.prototype.preventDefault = function (i) { i.preventDefault() }, e.prototype.progressiveLazyLoad = function (e) { e = e || 1; var t, o, s, n, r, l = this, d = i("img[data-lazy]", l.$slider); d.length ? (t = d.first(), o = t.attr("data-lazy"), s = t.attr("data-srcset"), n = t.attr("data-sizes") || l.$slider.attr("data-sizes"), (r = document.createElement("img")).onload = function () { s && (t.attr("srcset", s), n && t.attr("sizes", n)), t.attr("src", o).removeAttr("data-lazy data-srcset data-sizes").removeClass("slick-loading"), !0 === l.options.adaptiveHeight && l.setPosition(), l.$slider.trigger("lazyLoaded", [l, t, o]), l.progressiveLazyLoad() }, r.onerror = function () { e < 3 ? setTimeout(function () { l.progressiveLazyLoad(e + 1) }, 500) : (t.removeAttr("data-lazy").removeClass("slick-loading").addClass("slick-lazyload-error"), l.$slider.trigger("lazyLoadError", [l, t, o]), l.progressiveLazyLoad()) }, r.src = o) : l.$slider.trigger("allImagesLoaded", [l]) }, e.prototype.refresh = function (e) { var t, o, s = this; o = s.slideCount - s.options.slidesToShow, !s.options.infinite && s.currentSlide > o && (s.currentSlide = o), s.slideCount <= s.options.slidesToShow && (s.currentSlide = 0), t = s.currentSlide, s.destroy(!0), i.extend(s, s.initials, { currentSlide: t }), s.init(), e || s.changeSlide({ data: { message: "index", index: t } }, !1) }, e.prototype.registerBreakpoints = function () { var e, t, o, s = this, n = s.options.responsive || null; if ("array" === i.type(n) && n.length) { s.respondTo = s.options.respondTo || "window"; for (e in n) if (o = s.breakpoints.length - 1, n.hasOwnProperty(e)) { for (t = n[e].breakpoint; o >= 0;)s.breakpoints[o] && s.breakpoints[o] === t && s.breakpoints.splice(o, 1), o--; s.breakpoints.push(t), s.breakpointSettings[t] = n[e].settings } s.breakpoints.sort(function (i, e) { return s.options.mobileFirst ? i - e : e - i }) } }, e.prototype.reinit = function () { var e = this; e.$slides = e.$slideTrack.children(e.options.slide).addClass("slick-slide"), e.slideCount = e.$slides.length, e.currentSlide >= e.slideCount && 0 !== e.currentSlide && (e.currentSlide = e.currentSlide - e.options.slidesToScroll), e.slideCount <= e.options.slidesToShow && (e.currentSlide = 0), e.registerBreakpoints(), e.setProps(), e.setupInfinite(), e.buildArrows(), e.updateArrows(), e.initArrowEvents(), e.buildDots(), e.updateDots(), e.initDotEvents(), e.cleanUpSlideEvents(), e.initSlideEvents(), e.checkResponsive(!1, !0), !0 === e.options.focusOnSelect && i(e.$slideTrack).children().on("click.slick", e.selectHandler), e.setSlideClasses("number" == typeof e.currentSlide ? e.currentSlide : 0), e.setPosition(), e.focusHandler(), e.paused = !e.options.autoplay, e.autoPlay(), e.$slider.trigger("reInit", [e]) }, e.prototype.resize = function () { var e = this; i(window).width() !== e.windowWidth && (clearTimeout(e.windowDelay), e.windowDelay = window.setTimeout(function () { e.windowWidth = i(window).width(), e.checkResponsive(), e.unslicked || e.setPosition() }, 50)) }, e.prototype.removeSlide = e.prototype.slickRemove = function (i, e, t) { var o = this; if (i = "boolean" == typeof i ? !0 === (e = i) ? 0 : o.slideCount - 1 : !0 === e ? --i : i, o.slideCount < 1 || i < 0 || i > o.slideCount - 1) return !1; o.unload(), !0 === t ? o.$slideTrack.children().remove() : o.$slideTrack.children(this.options.slide).eq(i).remove(), o.$slides = o.$slideTrack.children(this.options.slide), o.$slideTrack.children(this.options.slide).detach(), o.$slideTrack.append(o.$slides), o.$slidesCache = o.$slides, o.reinit() }, e.prototype.setCSS = function (i) { var e, t, o = this, s = {}; !0 === o.options.rtl && (i = -i), e = "left" == o.positionProp ? Math.ceil(i) + "px" : "0px", t = "top" == o.positionProp ? Math.ceil(i) + "px" : "0px", s[o.positionProp] = i, !1 === o.transformsEnabled ? o.$slideTrack.css(s) : (s = {}, !1 === o.cssTransitions ? (s[o.animType] = "translate(" + e + ", " + t + ")", o.$slideTrack.css(s)) : (s[o.animType] = "translate3d(" + e + ", " + t + ", 0px)", o.$slideTrack.css(s))) }, e.prototype.setDimensions = function () { var i = this; !1 === i.options.vertical ? !0 === i.options.centerMode && i.$list.css({ padding: "0px " + i.options.centerPadding }) : (i.$list.height(i.$slides.first().outerHeight(!0) * i.options.slidesToShow), !0 === i.options.centerMode && i.$list.css({ padding: i.options.centerPadding + " 0px" })), i.listWidth = i.$list.width(), i.listHeight = i.$list.height(), !1 === i.options.vertical && !1 === i.options.variableWidth ? (i.slideWidth = Math.ceil(i.listWidth / i.options.slidesToShow), i.$slideTrack.width(Math.ceil(i.slideWidth * i.$slideTrack.children(".slick-slide").length))) : !0 === i.options.variableWidth ? i.$slideTrack.width(5e3 * i.slideCount) : (i.slideWidth = Math.ceil(i.listWidth), i.$slideTrack.height(Math.ceil(i.$slides.first().outerHeight(!0) * i.$slideTrack.children(".slick-slide").length))); var e = i.$slides.first().outerWidth(!0) - i.$slides.first().width(); !1 === i.options.variableWidth && i.$slideTrack.children(".slick-slide").width(i.slideWidth - e) }, e.prototype.setFade = function () { var e, t = this; t.$slides.each(function (o, s) { e = t.slideWidth * o * -1, !0 === t.options.rtl ? i(s).css({ position: "relative", right: e, top: 0, zIndex: t.options.zIndex - 2, opacity: 0 }) : i(s).css({ position: "relative", left: e, top: 0, zIndex: t.options.zIndex - 2, opacity: 0 }) }), t.$slides.eq(t.currentSlide).css({ zIndex: t.options.zIndex - 1, opacity: 1 }) }, e.prototype.setHeight = function () { var i = this; if (1 === i.options.slidesToShow && !0 === i.options.adaptiveHeight && !1 === i.options.vertical) { var e = i.$slides.eq(i.currentSlide).outerHeight(!0); i.$list.css("height", e) } }, e.prototype.setOption = e.prototype.slickSetOption = function () { var e, t, o, s, n, r = this, l = !1; if ("object" === i.type(arguments[0]) ? (o = arguments[0], l = arguments[1], n = "multiple") : "string" === i.type(arguments[0]) && (o = arguments[0], s = arguments[1], l = arguments[2], "responsive" === arguments[0] && "array" === i.type(arguments[1]) ? n = "responsive" : void 0 !== arguments[1] && (n = "single")), "single" === n) r.options[o] = s; else if ("multiple" === n) i.each(o, function (i, e) { r.options[i] = e }); else if ("responsive" === n) for (t in s) if ("array" !== i.type(r.options.responsive)) r.options.responsive = [s[t]]; else { for (e = r.options.responsive.length - 1; e >= 0;)r.options.responsive[e].breakpoint === s[t].breakpoint && r.options.responsive.splice(e, 1), e--; r.options.responsive.push(s[t]) } l && (r.unload(), r.reinit()) }, e.prototype.setPosition = function () { var i = this; i.setDimensions(), i.setHeight(), !1 === i.options.fade ? i.setCSS(i.getLeft(i.currentSlide)) : i.setFade(), i.$slider.trigger("setPosition", [i]) }, e.prototype.setProps = function () { var i = this, e = document.body.style; i.positionProp = !0 === i.options.vertical ? "top" : "left", "top" === i.positionProp ? i.$slider.addClass("slick-vertical") : i.$slider.removeClass("slick-vertical"), void 0 === e.WebkitTransition && void 0 === e.MozTransition && void 0 === e.msTransition || !0 === i.options.useCSS && (i.cssTransitions = !0), i.options.fade && ("number" == typeof i.options.zIndex ? i.options.zIndex < 3 && (i.options.zIndex = 3) : i.options.zIndex = i.defaults.zIndex), void 0 !== e.OTransform && (i.animType = "OTransform", i.transformType = "-o-transform", i.transitionType = "OTransition", void 0 === e.perspectiveProperty && void 0 === e.webkitPerspective && (i.animType = !1)), void 0 !== e.MozTransform && (i.animType = "MozTransform", i.transformType = "-moz-transform", i.transitionType = "MozTransition", void 0 === e.perspectiveProperty && void 0 === e.MozPerspective && (i.animType = !1)), void 0 !== e.webkitTransform && (i.animType = "webkitTransform", i.transformType = "-webkit-transform", i.transitionType = "webkitTransition", void 0 === e.perspectiveProperty && void 0 === e.webkitPerspective && (i.animType = !1)), void 0 !== e.msTransform && (i.animType = "msTransform", i.transformType = "-ms-transform", i.transitionType = "msTransition", void 0 === e.msTransform && (i.animType = !1)), void 0 !== e.transform && !1 !== i.animType && (i.animType = "transform", i.transformType = "transform", i.transitionType = "transition"), i.transformsEnabled = i.options.useTransform && null !== i.animType && !1 !== i.animType }, e.prototype.setSlideClasses = function (i) { var e, t, o, s, n = this; if (t = n.$slider.find(".slick-slide").removeClass("slick-active slick-center slick-current").attr("aria-hidden", "true"), n.$slides.eq(i).addClass("slick-current"), !0 === n.options.centerMode) { var r = n.options.slidesToShow % 2 == 0 ? 1 : 0; e = Math.floor(n.options.slidesToShow / 2), !0 === n.options.infinite && (i >= e && i <= n.slideCount - 1 - e ? n.$slides.slice(i - e + r, i + e + 1).addClass("slick-active").attr("aria-hidden", "false") : (o = n.options.slidesToShow + i, t.slice(o - e + 1 + r, o + e + 2).addClass("slick-active").attr("aria-hidden", "false")), 0 === i ? t.eq(t.length - 1 - n.options.slidesToShow).addClass("slick-center") : i === n.slideCount - 1 && t.eq(n.options.slidesToShow).addClass("slick-center")), n.$slides.eq(i).addClass("slick-center") } else i >= 0 && i <= n.slideCount - n.options.slidesToShow ? n.$slides.slice(i, i + n.options.slidesToShow).addClass("slick-active").attr("aria-hidden", "false") : t.length <= n.options.slidesToShow ? t.addClass("slick-active").attr("aria-hidden", "false") : (s = n.slideCount % n.options.slidesToShow, o = !0 === n.options.infinite ? n.options.slidesToShow + i : i, n.options.slidesToShow == n.options.slidesToScroll && n.slideCount - i < n.options.slidesToShow ? t.slice(o - (n.options.slidesToShow - s), o + s).addClass("slick-active").attr("aria-hidden", "false") : t.slice(o, o + n.options.slidesToShow).addClass("slick-active").attr("aria-hidden", "false")); "ondemand" !== n.options.lazyLoad && "anticipated" !== n.options.lazyLoad || n.lazyLoad() }, e.prototype.setupInfinite = function () { var e, t, o, s = this; if (!0 === s.options.fade && (s.options.centerMode = !1), !0 === s.options.infinite && !1 === s.options.fade && (t = null, s.slideCount > s.options.slidesToShow)) { for (o = !0 === s.options.centerMode ? s.options.slidesToShow + 1 : s.options.slidesToShow, e = s.slideCount; e > s.slideCount - o; e -= 1)t = e - 1, i(s.$slides[t]).clone(!0).attr("id", "").attr("data-slick-index", t - s.slideCount).prependTo(s.$slideTrack).addClass("slick-cloned"); for (e = 0; e < o + s.slideCount; e += 1)t = e, i(s.$slides[t]).clone(!0).attr("id", "").attr("data-slick-index", t + s.slideCount).appendTo(s.$slideTrack).addClass("slick-cloned"); s.$slideTrack.find(".slick-cloned").find("[id]").each(function () { i(this).attr("id", "") }) } }, e.prototype.interrupt = function (i) { var e = this; i || e.autoPlay(), e.interrupted = i }, e.prototype.selectHandler = function (e) { var t = this, o = i(e.target).is(".slick-slide") ? i(e.target) : i(e.target).parents(".slick-slide"), s = parseInt(o.attr("data-slick-index")); s || (s = 0), t.slideCount <= t.options.slidesToShow ? t.slideHandler(s, !1, !0) : t.slideHandler(s) }, e.prototype.slideHandler = function (i, e, t) { var o, s, n, r, l, d = null, a = this; if (e = e || !1, !(!0 === a.animating && !0 === a.options.waitForAnimate || !0 === a.options.fade && a.currentSlide === i)) if (!1 === e && a.asNavFor(i), o = i, d = a.getLeft(o), r = a.getLeft(a.currentSlide), a.currentLeft = null === a.swipeLeft ? r : a.swipeLeft, !1 === a.options.infinite && !1 === a.options.centerMode && (i < 0 || i > a.getDotCount() * a.options.slidesToScroll)) !1 === a.options.fade && (o = a.currentSlide, !0 !== t ? a.animateSlide(r, function () { a.postSlide(o) }) : a.postSlide(o)); else if (!1 === a.options.infinite && !0 === a.options.centerMode && (i < 0 || i > a.slideCount - a.options.slidesToScroll)) !1 === a.options.fade && (o = a.currentSlide, !0 !== t ? a.animateSlide(r, function () { a.postSlide(o) }) : a.postSlide(o)); else { if (a.options.autoplay && clearInterval(a.autoPlayTimer), s = o < 0 ? a.slideCount % a.options.slidesToScroll != 0 ? a.slideCount - a.slideCount % a.options.slidesToScroll : a.slideCount + o : o >= a.slideCount ? a.slideCount % a.options.slidesToScroll != 0 ? 0 : o - a.slideCount : o, a.animating = !0, a.$slider.trigger("beforeChange", [a, a.currentSlide, s]), n = a.currentSlide, a.currentSlide = s, a.setSlideClasses(a.currentSlide), a.options.asNavFor && (l = (l = a.getNavTarget()).slick("getSlick")).slideCount <= l.options.slidesToShow && l.setSlideClasses(a.currentSlide), a.updateDots(), a.updateArrows(), !0 === a.options.fade) return !0 !== t ? (a.fadeSlideOut(n), a.fadeSlide(s, function () { a.postSlide(s) })) : a.postSlide(s), void a.animateHeight(); !0 !== t ? a.animateSlide(d, function () { a.postSlide(s) }) : a.postSlide(s) } }, e.prototype.startLoad = function () { var i = this; !0 === i.options.arrows && i.slideCount > i.options.slidesToShow && (i.$prevArrow.hide(), i.$nextArrow.hide()), !0 === i.options.dots && i.slideCount > i.options.slidesToShow && i.$dots.hide(), i.$slider.addClass("slick-loading") }, e.prototype.swipeDirection = function () { var i, e, t, o, s = this; return i = s.touchObject.startX - s.touchObject.curX, e = s.touchObject.startY - s.touchObject.curY, t = Math.atan2(e, i), (o = Math.round(180 * t / Math.PI)) < 0 && (o = 360 - Math.abs(o)), o <= 45 && o >= 0 ? !1 === s.options.rtl ? "left" : "right" : o <= 360 && o >= 315 ? !1 === s.options.rtl ? "left" : "right" : o >= 135 && o <= 225 ? !1 === s.options.rtl ? "right" : "left" : !0 === s.options.verticalSwiping ? o >= 35 && o <= 135 ? "down" : "up" : "vertical" }, e.prototype.swipeEnd = function (i) { var e, t, o = this; if (o.dragging = !1, o.swiping = !1, o.scrolling) return o.scrolling = !1, !1; if (o.interrupted = !1, o.shouldClick = !(o.touchObject.swipeLength > 10), void 0 === o.touchObject.curX) return !1; if (!0 === o.touchObject.edgeHit && o.$slider.trigger("edge", [o, o.swipeDirection()]), o.touchObject.swipeLength >= o.touchObject.minSwipe) { switch (t = o.swipeDirection()) { case "left": case "down": e = o.options.swipeToSlide ? o.checkNavigable(o.currentSlide + o.getSlideCount()) : o.currentSlide + o.getSlideCount(), o.currentDirection = 0; break; case "right": case "up": e = o.options.swipeToSlide ? o.checkNavigable(o.currentSlide - o.getSlideCount()) : o.currentSlide - o.getSlideCount(), o.currentDirection = 1 }"vertical" != t && (o.slideHandler(e), o.touchObject = {}, o.$slider.trigger("swipe", [o, t])) } else o.touchObject.startX !== o.touchObject.curX && (o.slideHandler(o.currentSlide), o.touchObject = {}) }, e.prototype.swipeHandler = function (i) { var e = this; if (!(!1 === e.options.swipe || "ontouchend" in document && !1 === e.options.swipe || !1 === e.options.draggable && -1 !== i.type.indexOf("mouse"))) switch (e.touchObject.fingerCount = i.originalEvent && void 0 !== i.originalEvent.touches ? i.originalEvent.touches.length : 1, e.touchObject.minSwipe = e.listWidth / e.options.touchThreshold, !0 === e.options.verticalSwiping && (e.touchObject.minSwipe = e.listHeight / e.options.touchThreshold), i.data.action) { case "start": e.swipeStart(i); break; case "move": e.swipeMove(i); break; case "end": e.swipeEnd(i) } }, e.prototype.swipeMove = function (i) { var e, t, o, s, n, r, l = this; return n = void 0 !== i.originalEvent ? i.originalEvent.touches : null, !(!l.dragging || l.scrolling || n && 1 !== n.length) && (e = l.getLeft(l.currentSlide), l.touchObject.curX = void 0 !== n ? n[0].pageX : i.clientX, l.touchObject.curY = void 0 !== n ? n[0].pageY : i.clientY, l.touchObject.swipeLength = Math.round(Math.sqrt(Math.pow(l.touchObject.curX - l.touchObject.startX, 2))), r = Math.round(Math.sqrt(Math.pow(l.touchObject.curY - l.touchObject.startY, 2))), !l.options.verticalSwiping && !l.swiping && r > 4 ? (l.scrolling = !0, !1) : (!0 === l.options.verticalSwiping && (l.touchObject.swipeLength = r), t = l.swipeDirection(), void 0 !== i.originalEvent && l.touchObject.swipeLength > 4 && (l.swiping = !0, i.preventDefault()), s = (!1 === l.options.rtl ? 1 : -1) * (l.touchObject.curX > l.touchObject.startX ? 1 : -1), !0 === l.options.verticalSwiping && (s = l.touchObject.curY > l.touchObject.startY ? 1 : -1), o = l.touchObject.swipeLength, l.touchObject.edgeHit = !1, !1 === l.options.infinite && (0 === l.currentSlide && "right" === t || l.currentSlide >= l.getDotCount() && "left" === t) && (o = l.touchObject.swipeLength * l.options.edgeFriction, l.touchObject.edgeHit = !0), !1 === l.options.vertical ? l.swipeLeft = e + o * s : l.swipeLeft = e + o * (l.$list.height() / l.listWidth) * s, !0 === l.options.verticalSwiping && (l.swipeLeft = e + o * s), !0 !== l.options.fade && !1 !== l.options.touchMove && (!0 === l.animating ? (l.swipeLeft = null, !1) : void l.setCSS(l.swipeLeft)))) }, e.prototype.swipeStart = function (i) { var e, t = this; if (t.interrupted = !0, 1 !== t.touchObject.fingerCount || t.slideCount <= t.options.slidesToShow) return t.touchObject = {}, !1; void 0 !== i.originalEvent && void 0 !== i.originalEvent.touches && (e = i.originalEvent.touches[0]), t.touchObject.startX = t.touchObject.curX = void 0 !== e ? e.pageX : i.clientX, t.touchObject.startY = t.touchObject.curY = void 0 !== e ? e.pageY : i.clientY, t.dragging = !0 }, e.prototype.unfilterSlides = e.prototype.slickUnfilter = function () { var i = this; null !== i.$slidesCache && (i.unload(), i.$slideTrack.children(this.options.slide).detach(), i.$slidesCache.appendTo(i.$slideTrack), i.reinit()) }, e.prototype.unload = function () { var e = this; i(".slick-cloned", e.$slider).remove(), e.$dots && e.$dots.remove(), e.$prevArrow && e.htmlExpr.test(e.options.prevArrow) && e.$prevArrow.remove(), e.$nextArrow && e.htmlExpr.test(e.options.nextArrow) && e.$nextArrow.remove(), e.$slides.removeClass("slick-slide slick-active slick-visible slick-current").attr("aria-hidden", "true").css("width", "") }, e.prototype.unslick = function (i) { var e = this; e.$slider.trigger("unslick", [e, i]), e.destroy() }, e.prototype.updateArrows = function () { var i = this; Math.floor(i.options.slidesToShow / 2), !0 === i.options.arrows && i.slideCount > i.options.slidesToShow && !i.options.infinite && (i.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false"), i.$nextArrow.removeClass("slick-disabled").attr("aria-disabled", "false"), 0 === i.currentSlide ? (i.$prevArrow.addClass("slick-disabled").attr("aria-disabled", "true"), i.$nextArrow.removeClass("slick-disabled").attr("aria-disabled", "false")) : i.currentSlide >= i.slideCount - i.options.slidesToShow && !1 === i.options.centerMode ? (i.$nextArrow.addClass("slick-disabled").attr("aria-disabled", "true"), i.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false")) : i.currentSlide >= i.slideCount - 1 && !0 === i.options.centerMode && (i.$nextArrow.addClass("slick-disabled").attr("aria-disabled", "true"), i.$prevArrow.removeClass("slick-disabled").attr("aria-disabled", "false"))) }, e.prototype.updateDots = function () { var i = this; null !== i.$dots && (i.$dots.find("li").removeClass("slick-active").end(), i.$dots.find("li").eq(Math.floor(i.currentSlide / i.options.slidesToScroll)).addClass("slick-active")) }, e.prototype.visibility = function () { var i = this; i.options.autoplay && (document[i.hidden] ? i.interrupted = !0 : i.interrupted = !1) }, i.fn.slick = function () { var i, t, o = this, s = arguments[0], n = Array.prototype.slice.call(arguments, 1), r = o.length; for (i = 0; i < r; i++)if ("object" == typeof s || void 0 === s ? o[i].slick = new e(o[i], s) : t = o[i].slick[s].apply(o[i].slick, n), void 0 !== t) return t; return o } });

/*!
  * Stickyfill – `position: sticky` polyfill
  * v. 2.0.5 | https://github.com/wilddeer/stickyfill
  * MIT License
  */

!function(a,b){"use strict";function c(a,b){if(!(a instanceof b))throw new TypeError("Cannot call a class as a function")}function d(a,b){for(var c in b)b.hasOwnProperty(c)&&(a[c]=b[c])}function e(a){return parseFloat(a)||0}function f(a){for(var b=0;a;)b+=a.offsetTop,a=a.offsetParent;return b}var g=function(){function a(a,b){for(var c=0;c<b.length;c++){var d=b[c];d.enumerable=d.enumerable||!1,d.configurable=!0,"value"in d&&(d.writable=!0),Object.defineProperty(a,d.key,d)}}return function(b,c,d){return c&&a(b.prototype,c),d&&a(b,d),b}}(),h=!1;if(a.getComputedStyle){var i=b.createElement("div");["","-webkit-","-moz-","-ms-"].some(function(a){try{i.style.position=a+"sticky"}catch(a){}return""!=i.style.position})&&(h=!0)}else h=!0;var j="undefined"!=typeof ShadowRoot,k={top:null,left:null},l=[],m=function(){function i(a){if(c(this,i),!(a instanceof HTMLElement))throw new Error("First argument must be HTMLElement");if(l.some(function(b){return b._node===a}))throw new Error("Stickyfill is already applied to this node");this._node=a,this._stickyMode=null,this._active=!1,l.push(this),this.refresh()}return g(i,[{key:"refresh",value:function(){if(!h&&!this._removed){this._active&&this._deactivate();var c=this._node,g=getComputedStyle(c),i={top:g.top,display:g.display,marginTop:g.marginTop,marginBottom:g.marginBottom,marginLeft:g.marginLeft,marginRight:g.marginRight,cssFloat:g.cssFloat};if(!isNaN(parseFloat(i.top))&&"table-cell"!=i.display&&"none"!=i.display){this._active=!0;var k=c.parentNode,l=j&&k instanceof ShadowRoot?k.host:k,m=c.getBoundingClientRect(),n=l.getBoundingClientRect(),o=getComputedStyle(l);this._parent={node:l,styles:{position:l.style.position},offsetHeight:l.offsetHeight},this._offsetToWindow={left:m.left,right:b.documentElement.clientWidth-m.right},this._offsetToParent={top:m.top-n.top-e(o.borderTopWidth),left:m.left-n.left-e(o.borderLeftWidth),right:-m.right+n.right-e(o.borderRightWidth)},this._styles={position:c.style.position,top:c.style.top,bottom:c.style.bottom,left:c.style.left,right:c.style.right,width:c.style.width,marginTop:c.style.marginTop,marginLeft:c.style.marginLeft,marginRight:c.style.marginRight};var p=e(i.top);this._limits={start:m.top+a.pageYOffset-p,end:n.top+a.pageYOffset+l.offsetHeight-e(o.borderBottomWidth)-c.offsetHeight-p-e(i.marginBottom)};var q=o.position;"absolute"!=q&&"relative"!=q&&(l.style.position="relative"),this._recalcPosition();var r=this._clone={};r.node=b.createElement("div"),d(r.node.style,{width:m.right-m.left+"px",height:m.bottom-m.top+"px",marginTop:i.marginTop,marginBottom:i.marginBottom,marginLeft:i.marginLeft,marginRight:i.marginRight,cssFloat:i.cssFloat,padding:0,border:0,borderSpacing:0,fontSize:"1em",position:"static"}),k.insertBefore(r.node,c),r.docOffsetTop=f(r.node)}}}},{key:"_recalcPosition",value:function(){if(this._active&&!this._removed){var a=k.top<=this._limits.start?"start":k.top>=this._limits.end?"end":"middle";if(this._stickyMode!=a){switch(a){case"start":d(this._node.style,{position:"absolute",left:this._offsetToParent.left+"px",right:this._offsetToParent.right+"px",top:this._offsetToParent.top+"px",bottom:"auto",width:"auto",marginLeft:0,marginRight:0,marginTop:0});break;case"middle":d(this._node.style,{position:"fixed",left:this._offsetToWindow.left+"px",right:this._offsetToWindow.right+"px",top:this._styles.top,bottom:"auto",width:"auto",marginLeft:0,marginRight:0,marginTop:0});break;case"end":d(this._node.style,{position:"absolute",left:this._offsetToParent.left+"px",right:this._offsetToParent.right+"px",top:"auto",bottom:0,width:"auto",marginLeft:0,marginRight:0})}this._stickyMode=a}}}},{key:"_fastCheck",value:function(){this._active&&!this._removed&&(Math.abs(f(this._clone.node)-this._clone.docOffsetTop)>1||Math.abs(this._parent.node.offsetHeight-this._parent.offsetHeight)>1)&&this.refresh()}},{key:"_deactivate",value:function(){var a=this;this._active&&!this._removed&&(this._clone.node.parentNode.removeChild(this._clone.node),delete this._clone,d(this._node.style,this._styles),delete this._styles,l.some(function(b){return b!==a&&b._parent&&b._parent.node===a._parent.node})||d(this._parent.node.style,this._parent.styles),delete this._parent,this._stickyMode=null,this._active=!1,delete this._offsetToWindow,delete this._offsetToParent,delete this._limits)}},{key:"remove",value:function(){var a=this;this._deactivate(),l.some(function(b,c){if(b._node===a._node)return l.splice(c,1),!0}),this._removed=!0}}]),i}(),n={stickies:l,Sticky:m,addOne:function(a){if(!(a instanceof HTMLElement)){if(!a.length||!a[0])return;a=a[0]}for(var b=0;b<l.length;b++)if(l[b]._node===a)return l[b];return new m(a)},add:function(a){if(a instanceof HTMLElement&&(a=[a]),a.length){for(var b=[],c=0;c<a.length;c++){(function(c){var d=a[c];d instanceof HTMLElement?l.some(function(a){if(a._node===d)return b.push(a),!0})||b.push(new m(d)):b.push(void 0)})(c)}return b}},refreshAll:function(){l.forEach(function(a){return a.refresh()})},removeOne:function(a){if(!(a instanceof HTMLElement)){if(!a.length||!a[0])return;a=a[0]}l.some(function(b){if(b._node===a)return b.remove(),!0})},remove:function(a){if(a instanceof HTMLElement&&(a=[a]),a.length)for(var b=0;b<a.length;b++)!function(b){var c=a[b];l.some(function(a){if(a._node===c)return a.remove(),!0})}(b)},removeAll:function(){for(;l.length;)l[0].remove()}};h||function(){function c(){a.pageXOffset!=k.left?(k.top=a.pageYOffset,k.left=a.pageXOffset,n.refreshAll()):a.pageYOffset!=k.top&&(k.top=a.pageYOffset,k.left=a.pageXOffset,l.forEach(function(a){return a._recalcPosition()}))}function d(){f=setInterval(function(){l.forEach(function(a){return a._fastCheck()})},500)}function e(){clearInterval(f)}c(),a.addEventListener("scroll",c),a.addEventListener("resize",n.refreshAll),a.addEventListener("orientationchange",n.refreshAll);var f=void 0,g=void 0,h=void 0;"hidden"in b?(g="hidden",h="visibilitychange"):"webkitHidden"in b&&(g="webkitHidden",h="webkitvisibilitychange"),h?(b[g]||d(),b.addEventListener(h,function(){b[g]?e():d()})):d()}(),"undefined"!=typeof module&&module.exports?module.exports=n:a.Stickyfill=n}(window,document);

// https://github.com/constancecchen/object-fit-polyfill
!function(){"use strict";if("undefined"!=typeof window){var t=window.navigator.userAgent.match(/Edge\/(\d{2})\./),e=!!t&&parseInt(t[1],10)>=16;if("objectFit"in document.documentElement.style!=!1&&!e)return void(window.objectFitPolyfill=function(){return!1});var i=function(t){var e=window.getComputedStyle(t,null),i=e.getPropertyValue("position"),n=e.getPropertyValue("overflow"),o=e.getPropertyValue("display");i&&"static"!==i||(t.style.position="relative"),"hidden"!==n&&(t.style.overflow="hidden"),o&&"inline"!==o||(t.style.display="block"),0===t.clientHeight&&(t.style.height="100%"),-1===t.className.indexOf("object-fit-polyfill")&&(t.className=t.className+" object-fit-polyfill")},n=function(t){var e=window.getComputedStyle(t,null),i={"max-width":"none","max-height":"none","min-width":"0px","min-height":"0px",top:"auto",right:"auto",bottom:"auto",left:"auto","margin-top":"0px","margin-right":"0px","margin-bottom":"0px","margin-left":"0px"};for(var n in i){e.getPropertyValue(n)!==i[n]&&(t.style[n]=i[n])}},o=function(t,e,i){var n,o,l,a,d;if(i=i.split(" "),i.length<2&&(i[1]=i[0]),"x"===t)n=i[0],o=i[1],l="left",a="right",d=e.clientWidth;else{if("y"!==t)return;n=i[1],o=i[0],l="top",a="bottom",d=e.clientHeight}return n===l||o===l?void(e.style[l]="0"):n===a||o===a?void(e.style[a]="0"):"center"===n||"50%"===n?(e.style[l]="50%",void(e.style["margin-"+l]=d/-2+"px")):n.indexOf("%")>=0?(n=parseInt(n),void(n<50?(e.style[l]=n+"%",e.style["margin-"+l]=d*(n/-100)+"px"):(n=100-n,e.style[a]=n+"%",e.style["margin-"+a]=d*(n/-100)+"px"))):void(e.style[l]=n)},l=function(t){var e=t.dataset?t.dataset.objectFit:t.getAttribute("data-object-fit"),l=t.dataset?t.dataset.objectPosition:t.getAttribute("data-object-position");e=e||"cover",l=l||"50% 50%";var a=t.parentNode;i(a),n(t),t.style.position="absolute",t.style.height="100%",t.style.width="auto","scale-down"===e&&(t.style.height="auto",t.clientWidth<a.clientWidth&&t.clientHeight<a.clientHeight?(o("x",t,l),o("y",t,l)):(e="contain",t.style.height="100%")),"none"===e?(t.style.width="auto",t.style.height="auto",o("x",t,l),o("y",t,l)):"cover"===e&&t.clientWidth>a.clientWidth||"contain"===e&&t.clientWidth<a.clientWidth?(t.style.top="0",t.style.marginTop="0",o("x",t,l)):"scale-down"!==e&&(t.style.width="100%",t.style.height="auto",t.style.left="0",t.style.marginLeft="0",o("y",t,l))},a=function(t){if(void 0===t)t=document.querySelectorAll("[data-object-fit]");else if(t&&t.nodeName)t=[t];else{if("object"!=typeof t||!t.length||!t[0].nodeName)return!1;t=t}for(var i=0;i<t.length;i++)if(t[i].nodeName){var n=t[i].nodeName.toLowerCase();"img"!==n||e?"video"===n&&(t[i].readyState>0?l(t[i]):t[i].addEventListener("loadedmetadata",function(){l(this)})):t[i].complete?l(t[i]):t[i].addEventListener("load",function(){l(this)})}return!0};document.addEventListener("DOMContentLoaded",function(){a()}),window.addEventListener("resize",function(){a()}),window.objectFitPolyfill=a}}();

/**
 * Zenscroll 4.0.2
 * https://github.com/zengabor/zenscroll/
 *
 * Copyright 2015–2018 Gabor Lenard
 *
 * This is free and unencumbered software released into the public domain.
 *
 * Anyone is free to copy, modify, publish, use, compile, sell, or
 * distribute this software, either in source code form or as a compiled
 * binary, for any purpose, commercial or non-commercial, and by any
 * means.
 *
 * In jurisdictions that recognize copyright laws, the author or authors
 * of this software dedicate any and all copyright interest in the
 * software to the public domain. We make this dedication for the benefit
 * of the public at large and to the detriment of our heirs and
 * successors. We intend this dedication to be an overt act of
 * relinquishment in perpetuity of all present and future rights to this
 * software under copyright law.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
 * OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 * For more information, please refer to <http://unlicense.org>
 *
 */

/*jshint devel:true, asi:true */

/*global define, module */


(function (root, factory) {
	if (typeof define === "function" && define.amd) {
		define([], factory())
	} else if (typeof module === "object" && module.exports) {
		module.exports = factory()
	} else {
		(function install() {
			// To make sure Zenscroll can be referenced from the header, before `body` is available
			if (document && document.body) {
				root.zenscroll = factory()
			} else {
				// retry 9ms later
				setTimeout(install, 9)
			}
		})()
	}
}(this, function () {
	"use strict"

	if (is_smooth_scroll() === false) {
		return;
	}

	// Detect if the browser already supports native smooth scrolling (e.g., Firefox 36+ and Chrome 49+) and it is enabled:
	var isNativeSmoothScrollEnabledOn = function (elem) {
		return elem && "getComputedStyle" in window &&
			window.getComputedStyle(elem)["scroll-behavior"] === "smooth"
	}


	// Exit if it’s not a browser environment:
	if (typeof window === "undefined" || !("document" in window)) {
		return {}
	}


	var makeScroller = function (container, defaultDuration, edgeOffset) {

		// Use defaults if not provided
		defaultDuration = defaultDuration || 999 //ms
		if (!edgeOffset && edgeOffset !== 0) {
			// When scrolling, this amount of distance is kept from the edges of the container:
			edgeOffset = 9 //px
		}

		// Handling the life-cycle of the scroller
		var scrollTimeoutId
		var setScrollTimeoutId = function (newValue) {
			scrollTimeoutId = newValue
		}

		/**
		 * Stop the current smooth scroll operation immediately
		 */
		var stopScroll = function () {
			clearTimeout(scrollTimeoutId)
			setScrollTimeoutId(0)
		}

		var getTopWithEdgeOffset = function (elem) {
			return Math.max(0, container.getTopOf(elem) - edgeOffset)
		}

		/**
		 * Scrolls to a specific vertical position in the document.
		 *
		 * @param {targetY} The vertical position within the document.
		 * @param {duration} Optionally the duration of the scroll operation.
		 *        If not provided the default duration is used.
		 * @param {onDone} An optional callback function to be invoked once the scroll finished.
		 */
		var scrollToY = function (targetY, duration, onDone) {
			stopScroll()
			if (duration === 0 || (duration && duration < 0) || isNativeSmoothScrollEnabledOn(container.body)) {
				container.toY(targetY)
				if (onDone) {
					onDone()
				}
			} else {
				var startY = container.getY()
				var distance = Math.max(0, targetY) - startY
				var startTime = new Date().getTime()
				duration = duration || Math.min(Math.abs(distance), defaultDuration);
				(function loopScroll() {
					setScrollTimeoutId(setTimeout(function () {
						// Calculate percentage:
						var p = Math.min(1, (new Date().getTime() - startTime) / duration)
						// Calculate the absolute vertical position:
						var y = Math.max(0, Math.floor(startY + distance*(p < 0.5 ? 2*p*p : p*(4 - p*2)-1)))
						container.toY(y)
						if (p < 1 && (container.getHeight() + y) < container.body.scrollHeight) {
							loopScroll()
						} else {
							setTimeout(stopScroll, 99) // with cooldown time
							if (onDone) {
								onDone()
							}
						}
					}, 9))
				})()
			}
		}

		/**
		 * Scrolls to the top of a specific element.
		 *
		 * @param {elem} The element to scroll to.
		 * @param {duration} Optionally the duration of the scroll operation.
		 * @param {onDone} An optional callback function to be invoked once the scroll finished.
		 */
		var scrollToElem = function (elem, duration, onDone) {
			scrollToY(getTopWithEdgeOffset(elem), duration, onDone)
		}

		/**
		 * Scrolls an element into view if necessary.
		 *
		 * @param {elem} The element.
		 * @param {duration} Optionally the duration of the scroll operation.
		 * @param {onDone} An optional callback function to be invoked once the scroll finished.
		 */
		var scrollIntoView = function (elem, duration, onDone) {
			var elemHeight = elem.getBoundingClientRect().height
			var elemBottom = container.getTopOf(elem) + elemHeight
			var containerHeight = container.getHeight()
			var y = container.getY()
			var containerBottom = y + containerHeight
			if (getTopWithEdgeOffset(elem) < y || (elemHeight + edgeOffset) > containerHeight) {
				// Element is clipped at top or is higher than screen.
				scrollToElem(elem, duration, onDone)
			} else if ((elemBottom + edgeOffset) > containerBottom) {
				// Element is clipped at the bottom.
				scrollToY(elemBottom - containerHeight + edgeOffset, duration, onDone)
			} else if (onDone) {
				onDone()
			}
		}

		/**
		 * Scrolls to the center of an element.
		 *
		 * @param {elem} The element.
		 * @param {duration} Optionally the duration of the scroll operation.
		 * @param {offset} Optionally the offset of the top of the element from the center of the screen.
		 *        A value of 0 is ignored.
		 * @param {onDone} An optional callback function to be invoked once the scroll finished.
		 */
		var scrollToCenterOf = function (elem, duration, offset, onDone) {
			scrollToY(Math.max(0, container.getTopOf(elem) - container.getHeight()/2 + (offset || elem.getBoundingClientRect().height/2)), duration, onDone)
		}

		/**
		 * Changes default settings for this scroller.
		 *
		 * @param {newDefaultDuration} Optionally a new value for default duration, used for each scroll method by default.
		 *        Ignored if null or undefined.
		 * @param {newEdgeOffset} Optionally a new value for the edge offset, used by each scroll method by default. Ignored if null or undefined.
		 * @returns An object with the current values.
		 */
		var setup = function (newDefaultDuration, newEdgeOffset) {
			if (newDefaultDuration === 0 || newDefaultDuration) {
				defaultDuration = newDefaultDuration
			}
			if (newEdgeOffset === 0 || newEdgeOffset) {
				edgeOffset = newEdgeOffset
			}
			return {
				defaultDuration: defaultDuration,
				edgeOffset: edgeOffset
			}
		}

		return {
			setup: setup,
			to: scrollToElem,
			toY: scrollToY,
			intoView: scrollIntoView,
			center: scrollToCenterOf,
			stop: stopScroll,
			moving: function () { return !!scrollTimeoutId },
			getY: container.getY,
			getTopOf: container.getTopOf
		}

	}


	var docElem = document.documentElement
	var getDocY = function () { return window.scrollY || docElem.scrollTop }

	// Create a scroller for the document:
	var zenscroll = makeScroller({
		body: document.scrollingElement || document.body,
		toY: function (y) { window.scrollTo(0, y) },
		getY: getDocY,
		getHeight: function () { return window.innerHeight || docElem.clientHeight },
		getTopOf: function (elem) { return elem.getBoundingClientRect().top + getDocY() - docElem.offsetTop }
	})


	/**
	 * Creates a scroller from the provided container element (e.g., a DIV)
	 *
	 * @param {scrollContainer} The vertical position within the document.
	 * @param {defaultDuration} Optionally a value for default duration, used for each scroll method by default.
	 *        Ignored if 0 or null or undefined.
	 * @param {edgeOffset} Optionally a value for the edge offset, used by each scroll method by default.
	 *        Ignored if null or undefined.
	 * @returns A scroller object, similar to `zenscroll` but controlling the provided element.
	 */
	zenscroll.createScroller = function (scrollContainer, defaultDuration, edgeOffset) {
		return makeScroller({
			body: scrollContainer,
			toY: function (y) { scrollContainer.scrollTop = y },
			getY: function () { return scrollContainer.scrollTop },
			getHeight: function () { return Math.min(scrollContainer.clientHeight, window.innerHeight || docElem.clientHeight) },
			getTopOf: function (elem) { return elem.offsetTop }
		}, defaultDuration, edgeOffset)
	}


	// Automatic link-smoothing on achors
	// Exclude IE8- or when native is enabled or Zenscroll auto- is disabled
	if ("addEventListener" in window && !window.noZensmooth && !isNativeSmoothScrollEnabledOn(document.body)) {

		var isHistorySupported = "history" in window && "pushState" in history
		var isScrollRestorationSupported = isHistorySupported && "scrollRestoration" in history

		// On first load & refresh make sure the browser restores the position first
		if (isScrollRestorationSupported) {
			history.scrollRestoration = "auto"
		}

		window.addEventListener("load", function () {

			if (isScrollRestorationSupported) {
				// Set it to manual
				setTimeout(function () { history.scrollRestoration = "manual" }, 9)
				window.addEventListener("popstate", function (event) {
					if (event.state && "zenscrollY" in event.state) {
						zenscroll.toY(event.state.zenscrollY)
					}
				}, false)
			}

			// Add edge offset on first load if necessary
			// This may not work on IE (or older computer?) as it requires more timeout, around 100 ms
			if (window.location.hash) {
				setTimeout(function () {
					// Adjustment is only needed if there is an edge offset:
					var edgeOffset = zenscroll.setup().edgeOffset
					if (edgeOffset) {
						var targetElem = document.getElementById(window.location.href.split("#")[1])
						if (targetElem) {
							var targetY = Math.max(0, zenscroll.getTopOf(targetElem) - edgeOffset)
							var diff = zenscroll.getY() - targetY
							// Only do the adjustment if the browser is very close to the element:
							if (0 <= diff && diff < 9 ) {
								window.scrollTo(0, targetY)
							}
						}
					}
				}, 9)
			}

		}, false)

		// Handling clicks on anchors
		var RE_noZensmooth = new RegExp("(^|\\s)noZensmooth(\\s|$)")
		window.addEventListener("click", function (event) {
			var anchor = event.target
			while (anchor && anchor.tagName !== "A") {
				anchor = anchor.parentNode
			}
			// Let the browser handle the click if it wasn't with the primary button, or with some modifier keys:
			if (!anchor || event.which !== 1 || event.shiftKey || event.metaKey || event.ctrlKey || event.altKey) {
				return
			}
			// Save the current scrolling position so it can be used for scroll restoration:
			if (isScrollRestorationSupported) {
				var historyState = history.state && typeof history.state === "object" ? history.state : {}
				historyState.zenscrollY = zenscroll.getY()
				try {
					history.replaceState(historyState, "")
				} catch (e) {
					// Avoid the Chrome Security exception on file protocol, e.g., file://index.html
				}
			}
			// Find the referenced ID:
			var href = anchor.getAttribute("href") || ""
			if (href.indexOf("#") === 0 && !RE_noZensmooth.test(anchor.className)) {
				var targetY = 0
				var targetElem = document.getElementById(href.substring(1))
				if (href !== "#") {
					if (!targetElem) {
						// Let the browser handle the click if the target ID is not found.
						return
					}
					targetY = zenscroll.getTopOf(targetElem)
				}
				event.preventDefault()
				// By default trigger the browser's `hashchange` event...
				var onDone = function () { window.location = href }
				// ...unless there is an edge offset specified
				var edgeOffset = zenscroll.setup().edgeOffset
				if (edgeOffset) {
					targetY = Math.max(0, targetY - edgeOffset)
					if (isHistorySupported) {
						onDone = function () { history.pushState({}, "", href) }
					}
				}
				zenscroll.toY(targetY, null, onDone)
			}
		}, false)

	}


	return zenscroll


}));


var jupiterx = {
	components: {},
	utils: {},
};

/**
 * Base component.
 *
 * @since 1.0.0
 */
jupiterx.components.Base = Class.extend({
  /**
   * Set elements.
   *
   * @since 1.0.0
   */
  setElements: function () {
    this.elements = {}

    var $ = jQuery;

    this.elements.window = window;
    this.elements.$window = $(window);
    this.elements.$document = $(document);
    this.elements.$body = $('body');
    this.elements.$site = $('.jupiterx-site');
  },

  /**
   * Set settings.
   *
   * @since 1.0.0
   */
  setSettings: function () {
    this.settings = {}

    this.settings.windowWidth = this.elements.$window.outerWidth();
  },

  /**
   * Bind events.
   *
   * @since 1.0.0
   */
  bindEvents: function () {},

  /**
   * Initialize
   *
   * @since 1.0.0
   */
  init: function () {
    this.setElements()
    this.setSettings()
    this.bindEvents()
  }
});

window.jupiterx = jupiterx;

(function($) {

  var jupiterx = window.jupiterx || {}

  /**
   * Utilities.
   *
   * @since 1.0.0
   */
  jupiterx.utils = function () {
    /**
     * Resize.
     *
     * @since 1.0.0
     */
    this.resize = function () {
      var pubsub = jupiterx.pubsub

      $(window).on('resize', _.throttle(function() {
        var width = $(this).outerWidth()

        pubsub.publish('resize', width);
      }, 150));
    },

    /**
     * Scroll.
     *
     * @since 1.0.0
     */
    this.scroll = function () {
      var pubsub = jupiterx.pubsub
      var $dom = $('[data-jupiterx-scroll]')
      var options = _.defaults($dom.data('jupiterxScroll') || {}, {
        offset: 1000,
      })

      $(window).on('scroll', _.throttle(function() {
        var position = $(this).scrollTop()

        pubsub.publish('scroll', position);

        if (_.size($dom) < 1) return

        if (position > options.offset) {
          return $dom.addClass('jupiterx-scrolled')
        }

        $dom.removeClass('jupiterx-scrolled')
      }, 100));
    }

    this.scrollSmooth = function () {
      $(document).on('click', '[data-jupiterx-scroll-target]', function (event) {
        var target = $(this).data('jupiterxScrollTarget')
        var scrollBehavior = 'smooth'
        event.preventDefault()

        if (is_smooth_scroll() === false) {
          scrollBehavior = 'auto'
        }

        // Number.
        if (_.isNumber(target)) {
          window.scrollTo({ top: target, left: 0, behavior: scrollBehavior });
          return
        }

        // CSS selector.
        window.scrollTo({ top: $(target).offset().top, left: 0, behavior: scrollBehavior });
      })
    }

    // Scroll Up/Down detection.
    this.scrollDirection = function () {
      var pubsub = this.pubsub
      var $dom = $('[data-jupiterx-scroll-direction]')
      var scroll = updwn({ speed: 50 })

      scroll.up(function () {
        pubsub.publish('scroll-up');

        if (_.size($dom) < 1) return
        $dom.addClass('jupiterx-scroll-up')
        $dom.removeClass('jupiterx-scroll-down')
      })

      scroll.down(function () {
        pubsub.publish('scroll-down');

        if (_.size($dom) < 1) return
        $dom.addClass('jupiterx-scroll-down')
        $dom.removeClass('jupiterx-scroll-up')
      })
    }

    /**
     * Alter class.
     *
     * @see https://gist.github.com/peteboere/1517285
     *
     * @since 1.0.0
     */
    this.alterClass = function(elm, removals, additions) {
      var self = elm;

      if (removals.indexOf('*') === -1) {
        // Use native jQuery methods if there is no wildcard matching
        self.removeClass(removals);
        return !additions ? self : self.addClass(additions);
      }

      var patt = new RegExp(
        '\\s' + removals.replace(/\*/g, '[A-Za-z0-9-_]+').split(' ').join('\\s|\\s') + '\\s',
        'g'
      );

      self.each(function(i, it) {
        var cn = ' ' + it.className + ' ';
        while (patt.test(cn)) {
          cn = cn.replace(patt, ' ');
        }
        it.className = $.trim(cn);
      });

      return !additions ? self : self.addClass(additions);
    },

    /**
     * Check current screen is mobile.
     *
     * @since 1.8.0
     */
    this.onMobile = function () {
      var windowWidth = jQuery(window).width()

      return windowWidth <= 575.98
    },

    /**
     * Check current screen is tablet.
     *
     * @since 1.8.0
     */
    this.onTablet = function () {
      var windowWidth = jQuery(window).width()

      return windowWidth > 575.98 && windowWidth <= 767.98
    },

    /**
     * Check current screen is desktop.
     *
     * @since 1.8.0
     */
    this.onDesktop = function () {
      var windowWidth = jQuery(window).width()

      return windowWidth > 767.98
    }

    /**
     * Initialize.
     *
     * @since 1.0.0
     */
    this.init = function(){
      this.resize()
      this.scroll()
      this.scrollSmooth()
    }

    this.init()
  }

})( jQuery );

(function($) {

  var jupiterx = window.jupiterx || {}

  /**
   * Header component.
   *
   * @since 1.0.0
   */
  jupiterx.components.Header = jupiterx.components.Base.extend({
    /**
     * Set elements.
     *
     * @since 1.0.0
     */
    setElements: function () {
      this._super()

      var elements = this.elements
      elements.header = '.jupiterx-header'
      elements.$header = $(elements.header)
      elements.$navbar = elements.$header.find('.navbar-nav')
      elements.$collapseMenu = elements.$header.find('.navbar-collapse')
      elements.$dropdownToggler = elements.$navbar.find('.dropdown-toggle-icon')
      elements.$window = $(window)
      elements.$inPageMenuItems = elements.$navbar.find('a[href^="#"]')
    },

    /**
     * Set settings.
     *
     * @since 1.0.0
     */
    setSettings: function () {
      this._super()

      var settings = this.settings
      var headerSettings = this.elements.$header.data('jupiterxSettings')

      settings.breakpoint = headerSettings.breakpoint
      settings.template = headerSettings.template
      settings.stickyTemplate = headerSettings.stickyTemplate
      settings.behavior = headerSettings.behavior
      settings.position = headerSettings.position || 'top'
      settings.offset = parseInt(headerSettings.offset) + this.tbarHeight()
      settings.overlap = headerSettings.overlap
      settings.headerHeight = this.elements.$header.height()
    },

    /**
     * Bind events.
     *
     * @since 1.0.0
     */
    bindEvents: function() {
      var self = this
      var elements = this.elements
      var settings = this.settings

      // Accessibility.
      self.focusToggler()
      self.blurToggler()

      // Behavior.
      self.setBehavior()
      self.mobileMenuScroll()

      // Navbar.
      elements.$dropdownToggler.on('click', function (event) {
        self.initNavbarDropdown(event)
        self.setHeight()
      })

      // Resize subscribe.
      jupiterx.pubsub.subscribe('resize', function (windowWidth) {
        // Behavior.
        self.setBehavior()
        self.setHeight()

        // Navbar
        if (windowWidth > settings.breakpoint) {
          elements.$navbar.find('.dropdown-menu').removeClass('show')
        }
      })

      // Scroll subscribe.
      jupiterx.pubsub.subscribe('scroll', function (position) {
        // Sticky behavior.
        self.setBehaviorSticky(position)
      })

      self.responsiveMenuAutoClose()

      $(document).on('click', 'a.jupiterx-smooth-scroll, .jupiterx-smooth-scroll a', function (event) {
        self.handleSmoothScrollElements(event, $(this))
      })
    },

    /**
     * Calculate depth of last item in a menu item.
     *
     * @since 1.20.0
     */
    lastItemDepth: function(depth, $menu) {
      var max = depth;

      $li = $menu.find('> li:last-child');

      if ($li.hasClass('dropdown')) {
        var innerDepth = this.lastItemDepth(depth + 1, $li.find('.dropdown-menu'));

        max = innerDepth > max ? innerDepth : max;
      } else if ($menu.hasClass('dropdown-menu')) {
        var innerDepth = depth + 1;

        max = innerDepth > max ? innerDepth : max;
      }

      return max;
    },

    /**
     * Depths of last item in each top level menu item.
     *
     * @since 1.20.0
     */
    lastItemDepths: function($menu) {
      var depths = [];

      for (var i = 0; i < $menu.find('> li').length; i++) {
        $li = $menu.find('> li:nth-of-type(' + (i + 1) + ')');

        if ($li.hasClass('dropdown')) {
          depths[i] = this.lastItemDepth(1, $li.find('.dropdown-menu'));
        } else {
          depths[i] = 1;
        }
      }

      return depths;
    },

    /**
     * Add support for keyboard navigation to menu.
     * @since 1.11.0
     * @link https://github.com/wpaccessibility/a11ythemepatterns
     */
    focusToggler: function() {
      var self = this;

      // make dropdown functional on focus
      $('.jupiterx-site-navbar').find('a').on('focus', function() {
        $('.dropdown.hover, ul.dropdown-menu.hover').removeClass('hover show');
        $(this).parents('ul, li').addClass('hover show focus');
        $(this).next('ul.dropdown-menu').addClass('hover show focus');
      })
    },

    /**
     * Add support for keyboard navigation to menu.
     * @since 1.11.0
     * @link https://github.com/wpaccessibility/a11ythemepatterns
     */
    blurToggler: function() {
      var self = this;

      // make dropdown functional on focus
      $('.jupiterx-nav-primary').find('a').on('blur', function() {
        var $this = $(this);
        var depths = self.lastItemDepths($('.jupiterx-nav-primary'));
        var depth = 0;
        var isLastItem = true;

        while (!$this.hasClass('jupiterx-nav-primary')) {
          if ($this.prop('tagName').toLowerCase() === 'li') {
            isLastItem = isLastItem && ($this.next().index() === -1)

            depth++;

            if ($this.parent().hasClass('jupiterx-nav-primary') && depth === depths[$this.index()] && isLastItem) {
              $('.dropdown.hover, ul.dropdown-menu.hover').removeClass('hover show');
            }
          }

          $this = $this.parent();
        }
      })
    },

    /**
     * Auto close responsive menu after tabbing on last element.
     *
     * @since 1.11.0
     */
    responsiveMenuAutoClose: function() {
      var $collapseMenu = this.elements.$collapseMenu;
      var focusable = $collapseMenu.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
      var lastFocusable = focusable[focusable.length - 1];

      $(lastFocusable).on('blur', function(){
        $collapseMenu.removeClass('show')
      })
    },

    /**
     * Set maximum height for menu to allow scroll on menu.
     *
     * @since 1.0.1
     */
    setHeight: function() {
      var navContainer = this.elements.$header.find('.navbar-collapse')
      if ( navContainer.length ) {
        var navbar = this.elements.$navbar
        navContainer.css('max-height', document.documentElement.clientHeight - navContainer.offset().top + window.pageYOffset - parseInt(navbar.css('margin-top')) );
      }
    },

    /**
     * Prevent body scroll while scrolling mobile menu. (touch scroll only)
     *
     * @since 1.0.2
     */
    mobileMenuScroll: function () {
      var overlays = document.getElementsByClassName('navbar-collapse'),
        _clientY = null
      for (var i = 0; i < overlays.length; i++) {
        overlays[i].addEventListener('touchstart', function (event) {
          if (event.targetTouches.length === 1) {
            _clientY = event.targetTouches[0].clientY
          }
        }, { capture: false, passive: true } )

        overlays[i].addEventListener('touchmove', function (event) {
          if (event.targetTouches.length === 1) {
            var clientY = event.targetTouches[0].clientY - _clientY
            if (overlays[i].scrollTop === 0 && clientY > 0 && event.cancelable) {
              event.preventDefault()
            }
            if (overlays[i].scrollHeight - overlays[i].scrollTop <= overlays[i].clientHeight && clientY < 0 && event.cancelable) {
              event.preventDefault()
            }
          }
        }, { capture: false, passive: true } )
      }
    },

    /**
     * Set behavior.
     *
     * @since 1.0.0
     */
    setBehavior: function () {
      this.setBehaviorFixed()
      this.setBehaviorSticky()
    },

    /**
     * Set fixed behavior.
     *
     * @since 1.0.0
     */
    setBehaviorFixed: function () {
      if (this.settings.behavior === 'fixed') {
        this.setSiteSpacing()
      }
    },

    /**
     * Set sticky behavior.
     *
     * @since 1.0.0
     */
    setBehaviorSticky: function (position) {
      var elements = this.elements,
        settings = this.settings

      if ( elements.$body.find( '.jupiterx-header' ).find( '.raven-sticky' ).length > 0 ) {
        return;
      }

      if (settings.behavior !== 'sticky' || typeof position === 'undefined') {
        return
      }

      // Stick.
      if (position > settings.headerHeight) {
        elements.$body.addClass('jupiterx-header-stick')
        this.setSiteSpacing()

        var $customHeader = $('.jupiterx-header.jupiterx-header-sticky-custom.jupiterx-header-custom > .elementor:last-of-type:not(:first-of-type)')

        if ($customHeader.length && $customHeader.height() > 0) {
          elements.$header.height($customHeader.height());
        }
      } else {
        elements.$body.removeClass('jupiterx-header-stick')
        this.clearSiteSpacing()
        elements.$header.attr('style', function(i, style) {
          return style && style.replace(/height[^;]+;?/g, '');
        } );
      }

      // Sticked.
      if (position > settings.offset) {
        elements.$body.addClass('jupiterx-header-sticked')
      } else {
        elements.$body.removeClass('jupiterx-header-sticked')
      }
    },

    /**
     * Set site spacing.
     *
     * @since 1.0.0
     */
    setSiteSpacing: function () {
      var elements = this.elements,
        settings = this.settings

      if (this.isOverlap()) {
        this.clearSiteSpacing()

        if (settings.behavior === 'fixed' && $('.jupiterx-tbar').length > 0) {
          var spacing = window.jupiterx.utils.onMobile() ? '' : (this.tbarHeight() || '')

          spacing -= this.getJetScrollNavSectionOffset()

          elements.$site.css('padding-top', spacing)
        }

        return
      }

      var $header = elements.$header

      if (settings.behavior === 'fixed' && settings.position === 'bottom') {
        elements.$site.css('padding-' + settings.position, $header.outerHeight())
      } else if (settings.behavior === 'sticky') {
        var space = 0;

        var $originalHeader = $('.jupiterx-header.jupiterx-header-sticky-custom.jupiterx-header-custom > .elementor:first-of-type:not(:last-of-type)')
        if ($originalHeader.length) {
          space = $originalHeader.outerHeight()
        } else {
          space = $header.outerHeight()
        }

        elements.$site.css('padding-' + settings.position, space)
      } else {
        elements.$header.css('position', 'fixed')
        elements.$site.css('padding-' + settings.position, $header.outerHeight() + this.tbarHeight())
      }
    },

    getJetScrollNavSectionOffset: function() {
      $jetScroll = $('.jet-scroll-navigation')

      if ($jetScroll.length === 0) {
        return 0
      }

      var $jetScrollItem = $jetScroll.find('.jet-scroll-navigation__item')

      if ($jetScrollItem.length === 0) {
        return 0
      }

      var anchor = $jetScrollItem.data('anchor')

      if (!anchor || $('#' + anchor).length === 0) {
        return 0
      }

      return $('#' + anchor).offset().top
    },

    /**
     * Clear site spacing.
     *
     * @since 1.0.0
     */
    clearSiteSpacing: function () {
      this.elements.$site.css('padding-' + this.settings.position, '')
    },

    /**
     * Check if header should overlap content.
     *
     * @since 1.0.0
     *
     * @return {boolean} Overlap status.
     */
    isOverlap: function () {
      var elements = this.elements,
        windowWidth = elements.$window.outerWidth(),
        overlap = this.settings.overlap

      if (!overlap) {
        return false
      }

      var desktop = (windowWidth > 768 && overlap.indexOf('desktop') > -1),
        tablet = ((windowWidth < 767.98 && windowWidth > 576) && overlap.indexOf('tablet') > -1),
        mobile = (windowWidth < 575.98 && overlap.indexOf('mobile') > -1)

      // Check current state depending on windowWidth.
      if (desktop || tablet || mobile) {
        return true
      }

      return false
    },

    /**
     * Add dropdown behavior to navbar in responsive state.
     *
     * @since 1.0.0
     */
    initNavbarDropdown: function (event) {
      event.preventDefault()
      event.stopPropagation()

      if (this.elements.$window.outerWidth() > this.settings.breakpoint) {
        return
      }

      if ($(event.target).closest('.menu-item').hasClass('focus')) {
        $(event.target).closest('.menu-item').removeClass('focus')
        $(event.target).closest('.menu-item').find('> .dropdown-menu').removeClass('focus')

        return
      }

      $(event.target).closest('.menu-item').find('> .dropdown-menu').toggleClass('show')
    },

    /**
     * Handle click event on anchor tags with href as hash.
     *
     * @since 1.8.0
     */
    inPageMenuClick: function () {
      var self = this,
        anchorId
      var headerSettings = this.getHeaderSettings()

      this.elements.$navbar.on('click', function (e) {
        anchorId = e.target.getAttribute('href') || ''

        var url = null

        try {
          url = new window.URL($(e.target).prop('href'))
          $(e.target).parents('li').addClass('active');
        } catch (err) {
          return
        }

        if (
          url.href.replace(url.hash, '') !== window.location.href.replace(window.location.hash, '') &&
          anchorId.search(/^#/) === -1
        ) {
          return
        }

        if (url.hash.search(/^#/) === -1) {
          return
        }

        anchorId = url.hash

        e.preventDefault()

        var anchorTarget = $(anchorId)

        if (anchorTarget.length === 0) {
          if ($('#jupiterxSiteNavbar').hasClass('show') && self.isBelowDesktop()) {
            $('#jupiterxSiteNavbar').collapse('hide')
          }

          window.history.pushState(null, null, url.hash)

          return
        }

        var scrollPosition = anchorTarget.offset().top
        scrollPosition -= self.getAdminbarHeight()
        scrollPosition -= self.getBodyBorderWidth()

        if (headerSettings && headerSettings.behavior === 'sticky' && headerSettings.overlap) {
          scrollPosition -= self.isHeaderSticked() ? self.tbarHeight() : 2 * self.tbarHeight()
        } else if (headerSettings && !headerSettings.behavior) {
          scrollPosition -= self.isHeaderSticked() ? self.tbarHeight() : 2 * self.tbarHeight()
        } else {
          scrollPosition -= self.tbarHeight()
        }

        if (
          (headerSettings && headerSettings.behavior === 'fixed' && headerSettings.position === 'top') ||
          (headerSettings && headerSettings.behavior === 'sticky')
        ) {
          scrollPosition -= self.getHeaderHeight()
        }

        if (is_smooth_scroll() === false) {
          window.scroll( { top: scrollPosition, behavior: 'auto' } );
          return;
        }

        $('html, body').stop().animate({
          scrollTop: scrollPosition
        }, 500, 'swing', function() {
          if ($('#jupiterxSiteNavbar').hasClass('show') && self.isBelowDesktop()) {
            $('#jupiterxSiteNavbar').collapse('hide')
          }

          window.history.pushState(null, null, url.hash)
        })

        return false
      })
    },

    /**
     * Set menu item active based on current section visible.
     *
     * @since 1.8.0
     */
    inPageMenuScroll: function () {
      var self = this

      if (self.elements.$inPageMenuItems.length) {
        self.activateMenuItem()

        window.addEventListener('scroll', _.throttle(function () {
          self.activateMenuItem()
        }, 200))
      }
    },

    /**
     * Set menu item active.
     *
     * @since 1.8.0
     */
    activateMenuItem: function () {
      var self = this,
        anchorId,
        section,
        position = window.pageYOffset

      self.elements.$inPageMenuItems.each(function (_index, element) {
        if (element.hash < 1) {
          return true
        }

        section = document.querySelector('[id="' + element.hash.replace('#', '') + '"]')

        if (!section) {
          return true
        }

        if ( // Give some space to Firefox. As it calculates values with decimals.
          (Math.abs($(section).offset().top + $(section).outerHeight() - $(document).height()) < 10) &&
          (Math.abs($(window).scrollTop() + window.innerHeight - $(document).height()) < 10)
        ) {
          anchorId = element.hash
          return false
        }

        // Give some space to Firefox. As it calculates values with decimals.
        if (position + 10 >= $(section).offset().top - self.getHeaderHeight() - self.getAdminbarHeight()) {
          anchorId = element.hash
          return true
        }
      })

      self.elements.$inPageMenuItems.removeClass('active')
      self.elements.$navbar.find('a[href="' + anchorId + '"]').addClass('active')
    },

    /**
     * Calculate header height.
     *
     * @since 1.8.0
     */
    getHeaderHeight: function () {
      var header = $('.jupiterx-header')

      if (header.length === 0) {
        return 0
      }

      var headerSettings = header.data('jupiterx-settings')
      var behavior = headerSettings.behavior

      if (behavior === 'fixed' || behavior === 'sticky' || window.pageYOffset < header.height()) {
        return header.height()
      }

      return 0
    },

    /**
     * Check Sticky header is custom.
     *
     * @since 1.15.0
     */
    hasCustomStickyHeader: function () {
      if ($('.jupiterx-header.jupiterx-header-custom').length === 0) {
        return false
      }

      var settings = this.getHeaderSettings()

      if (!settings) {
        return false
      }

      if (!settings.behavior || settings.behavior !== 'sticky') {
        return false
      }

      return !settings.stickyTemplate || settings.stickyTemplate !== settings.template
    },

    getHeaderSettings: function () {
      var $header = $('.jupiterx-header')

      return $header.data('jupiterx-settings')
    },

    /**
     * Get Custom Sticky header height.
     *
     * @since 1.15.0
     */
    getCustomStickyHeaderHeight: function () {
      if (!this.hasCustomStickyHeader()) {
        return 0
      }

      var $stickyHeader = $('.jupiterx-header-custom .elementor:last-of-type')

      if ($stickyHeader.length === 0) {
        return 0
      }

      return $stickyHeader.outerHeight()
    },

    getBodyBorderWidth: function () {
      var $bodyBorder = $('.jupiterx-site-body-border')

      if ($bodyBorder.length === 0) {
        return 0
      }

      var width = $bodyBorder.css('border-width')

      if (!width) {
        return 0
      }

      return parseInt(width.replace('px', ''))
    },

    /**
     * Get WP Admin bar height.
     *
     * @since 1.8.0
     */
    getAdminbarHeight: function () {
      var adminbar = $('#wpadminbar')
      if (adminbar.length) {
        return adminbar.height()
      }
      return 0
    },

    /**
     * Get Template bar height.
     *
     * @since 1.11.0
     */
    tbarHeight: function () {
      var $tbar = $('.jupiterx-tbar');

      if ($tbar.css('display') === 'none') {
        return 0;
      }

      if ($tbar.length) {
        return $tbar.outerHeight()
      }

      return 0
    },

    /**
     * Check screen size is smaller than Desktop.
     *
     * @since 1.8.0
     */
    isBelowDesktop: function () {
      return window.jupiterx.utils.onMobile() || window.jupiterx.utils.onTablet()
    },

    /**
     * Handle cross page anchor tag target section overlap.
     *
     * @since 1.8.0
     */
    handlePageLoadScroll: function () {
      var self = this
      var headerSettings = this.getHeaderSettings()

      $(document).ready(function () {
        if (window.jupiterx.utils.onMobile() && $('body').hasClass('jupiterx-header-mobile-behavior-off')) {
          return
        }

        if (window.jupiterx.utils.onTablet() && $('body').hasClass('jupiterx-header-tablet-behavior-off')) {
          return
        }

        var anchorTarget = $(window.location.hash)

        if (anchorTarget.length === 0) {
          return
        }

        var scrollPosition = anchorTarget.offset().top
        scrollPosition -= self.getAdminbarHeight()
        scrollPosition -= self.getBodyBorderWidth()

        if (headerSettings && headerSettings.behavior === 'sticky' && headerSettings.overlap) {
          scrollPosition -= self.isHeaderSticked() ? self.tbarHeight() : 2 * self.tbarHeight()
        } else if (headerSettings && !headerSettings.behavior) {
          scrollPosition -= self.isTbarFixed() ? self.tbarHeight() : 2 * self.tbarHeight()
        } else {
          scrollPosition -= self.tbarHeight()
        }

        if (self.hasCustomStickyHeader()) {
          scrollPosition -= self.getCustomStickyHeaderHeight()
        } else if (
          (headerSettings && headerSettings.behavior === 'fixed' && headerSettings.position === 'top') ||
          (headerSettings && headerSettings.behavior === 'sticky')
        ) {
          scrollPosition -= self.getHeaderHeight()
        }

        $('#jupiterxSiteNavbar').find('a').each(function (_index, menuItem) {
          if (window.location.hash != '#' + $(menuItem).attr('href').split('#')[1] ) {
            return;
          }

          $(menuItem).addClass('active');
          $(menuItem).parent().addClass('active');
        })

        if (is_smooth_scroll() === false) {
          window.scroll( { top: scrollPosition, behavior: 'auto' } );
          return;
        }

        $('html, body').stop().animate({
          scrollTop: scrollPosition
        }, 500, 'swing')
      })
    },

    /**
     * Handle elements scroll to section on click.
     *
     * @since 1.15.0
     */
    handleSmoothScrollElements: function (e, $el) {
      var self = this
      var headerSettings = self.getHeaderSettings()
      var anchorId = $el.attr('href') || ''
      var url = null

      try {
        url = new window.URL($el.prop('href'))
      } catch (err) {
        return
      }

      if (
          url.href.replace(url.hash, '') !== window.location.href.replace(window.location.hash, '') &&
          anchorId.search(/^#/) === -1
      ) {
        return
      }

      if (url.hash.search(/^#/) === -1) {
        return
      }

      anchorId = url.hash

      e.preventDefault()

      var anchorTarget = $(anchorId)

      var scrollPosition = anchorTarget.offset().top
      scrollPosition -= self.getAdminbarHeight()
      scrollPosition -= self.getBodyBorderWidth()

      if (headerSettings && headerSettings.behavior === 'sticky' && headerSettings.overlap) {
        scrollPosition -= self.isHeaderSticked() ? self.tbarHeight() : 2 * self.tbarHeight()
      } else if (headerSettings && !headerSettings.behavior) {
        scrollPosition -= self.isHeaderSticked() ? self.tbarHeight() : 2 * self.tbarHeight()
      } else {
        scrollPosition -= self.tbarHeight()
      }

      if (self.hasCustomStickyHeader()) {
        scrollPosition -= self.getCustomStickyHeaderHeight()
      } else if (
        (headerSettings && headerSettings.behavior === 'fixed' && headerSettings.position === 'top') ||
        (headerSettings && headerSettings.behavior === 'sticky')
      ) {
        scrollPosition -= self.getHeaderHeight()
      }

      if (window.elementorFrontend) {
        window.elementorFrontend.hooks
        .addFilter('frontend/handlers/menu_anchor/scroll_top_distance', function() {
          return scrollPosition
        })
      }

      $('html, body').stop().animate({
        scrollTop: scrollPosition
      }, 500, 'swing', function () {
        window.history.pushState(null, null, url.hash)
      })

      return false
    },

    /*
     * Check if header in sticked mode.
     *
     * @since 1.15.0
     */
    isHeaderSticked: function () {
      return $('.jupiterx-header-sticked').length > 0
    },

    /**
     * Check if tbar is fixed.
     *
     * @since 1.15.0
     */
    isTbarFixed: function () {
      return $('.jupiterx-tbar').css('position') === 'fixed'
    },

    /**
     * Initialize
     *
     * @since 1.0.0
     */
    init: function () {
      var self = this

      self.handlePageLoadScroll()

      this.setElements()

      if (!this.elements.$header.length) {
        return;
      }

      this.setSettings()
      this.bindEvents()
      this.inPageMenuClick()
      this.inPageMenuScroll()
    }
  });

})( jQuery );

/**
 * Refactor the codes to follow same convention as header.
 */

jQuery(document).ready(function ($) {

  // Menu and WooCommerce categories.
  //
  var $widget = $('.jupiterx-widget.widget_nav_menu')
  var $menu_current_item = $widget.find('.current_page_item, .current-cat')

  // Toggle the sub-menus to show the current link.
  if ($menu_current_item.length) {
    $menu_current_item
      .parents('.sub-menu, .children').slideToggle()
      .parents('.menu-item-has-children, .cat-parent')
      .toggleClass('jupiterx-icon-plus jupiterx-icon-minus')
  }

  // Toggle the sub-menus for Menu and WooCommerce categories.
  $(document).on('click', '.jupiterx-widget .menu-item-has-children, .jupiterx-widget .cat-parent', function(e) {
    e.stopPropagation()

    if (e.target.nodeName === 'A') {
      return;
    }

    $(this)
      .toggleClass('jupiterx-icon-plus jupiterx-icon-minus')
      .find('> ul').slideToggle()
  });

});

jQuery(document).ready(function ($) {

  var jupiterx = window.jupiterx || {}

  /**
   * Initialize components.
   *
   * @since 1.0.0
   */
  jupiterx.initComponents = function () {
    for (component in this.components) {
      new this.components[component];
    }
  }

  /**
   * Initialize.
   *
   * @since 1.0.0
   */
  jupiterx.init = function () {
    this.pubsub = new PubSub()
    this.pubsub.publish('init');

    this.utils = new this.utils()
    this.initComponents()
  }

  jupiterx.init();
});

/**
 * --------------------------------------------------------------------------
 * Bootstrap (v4.0.0): index.js
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * --------------------------------------------------------------------------
 */

// Fix Bootstrap's JavaScript requires jQuery error.
if (typeof window.jQuery !== 'undefined') {
  $ = window.jQuery;
}

(function ($) {
  if (typeof $ === 'undefined') {
    throw new TypeError('Bootstrap\'s JavaScript requires jQuery. jQuery must be included before Bootstrap\'s JavaScript.');
  }

  var version = $.fn.jquery.split(' ')[0].split('.');
  var minMajor = 1;
  var ltMajor = 2;
  var minMinor = 9;
  var minPatch = 1;
  var maxMajor = 4;

  if (version[0] < ltMajor && version[1] < minMinor || version[0] === minMajor && version[1] === minMinor && version[2] < minPatch || version[0] >= maxMajor) {
    throw new Error('Bootstrap\'s JavaScript requires at least jQuery v1.9.1 but less than v4.0.0');
  }
})($);


// WooCommerce
/**
 * Author and copyright: Stefan Haack (https://shaack.com)
 * Repository: https://github.com/shaack/bootstrap-input-spinner
 * License: MIT, see file 'LICENSE'
 *
 * Modified. Please don't update. AM-3806
 */

(function ($) {
  "use strict"

  var triggerKeyPressed = false
  var originalVal = $.fn.val
  $.fn.val = function (value) {
      if (arguments.length >= 1) {
          if (typeof(this[0]) !== 'undefined' && this[0]["bootstrap-input-spinner"] && this[0].setValue) {
              this[0].setValue(value)
          }
      }
      return originalVal.apply(this, arguments)
  }

  $.fn.InputSpinner = $.fn.inputSpinner = function (options) {

      if ( jupiterxOptions.quantityFieldSwitch !== '1' && jupiterxOptions.quantityFieldSwitch !== 'undefined' ) {
          return;
      }

      var config = {
          decrementButton: "<strong>-</strong>", // button text
          incrementButton: "<strong>+</strong>", // ..
          groupClass: "", // css class of the input-group (sizing with input-group-sm, input-group-lg)
          buttonsClass: "btn-outline-secondary",
          buttonsWidth: "2.5rem",
          textAlign: "center",
          autoDelay: 500, // ms holding before auto value change
          autoInterval: 100, // speed of auto value change
          boostThreshold: 10, // boost after these steps
          boostMultiplier: "auto", // you can also set a constant number as multiplier
          locale: null // the locale for number rendering; if null, the browsers language is used
      }
      for (var option in options) {
          config[option] = options[option]
      }

      var html = '<div class="input-group ' + config.groupClass + '">' +
          '<div class="input-group-prepend">' +
          '<button style="min-width: ' + config.buttonsWidth + '" class="btn btn-decrement ' + config.buttonsClass + '" type="button">' + config.decrementButton + '</button>' +
          '</div>' +
          '<input type="text" style="text-align: ' + config.textAlign + '" class="form-control"/>' +
          '<div class="input-group-append">' +
          '<button style="min-width: ' + config.buttonsWidth + '" class="btn btn-increment ' + config.buttonsClass + '" type="button">' + config.incrementButton + '</button>' +
          '</div>' +
          '</div>'

      var locale = config.locale || navigator.language || "en-US"

      this.each(function () {

          var $original = $(this)
          $original[0]["bootstrap-input-spinner"] = true
          $original.hide()

          var autoDelayHandler = null
          var autoIntervalHandler = null
          var autoMultiplier = config.boostMultiplier === "auto"
          var boostMultiplier = autoMultiplier ? 1 : config.boostMultiplier

          var $inputGroup = $(html)
          var $buttonDecrement = $inputGroup.find(".btn-decrement")
          var $buttonIncrement = $inputGroup.find(".btn-increment")
          var $input = $inputGroup.find("input")

          var min = parseFloat($original.prop("min")) || 0
          var max = isNaN($original.prop("max")) || $original.prop("max") === "" ? Infinity : parseFloat($original.prop("max"))
          var step = parseFloat($original.prop("step")) || 1
          var stepMax = parseInt($original.attr("data-step-max")) || 0
          var decimals = parseInt($original.attr("data-decimals")) || 0

          var numberFormat = new Intl.NumberFormat(locale, {
              minimumFractionDigits: decimals,
              maximumFractionDigits: decimals
          })
          var value = parseFloat($original[0].value)
          var boostStepsCount = 0

          var prefix = $original.attr("data-prefix") || ""
          var suffix = $original.attr("data-suffix") || ""

          if (prefix) {
              var prefixElement = $('<span class="input-group-text">' + prefix + '</span>')
              $inputGroup.find(".input-group-prepend").append(prefixElement)
          }
          if (suffix) {
              var suffixElement = $('<span class="input-group-text">' + suffix + '</span>')
              $inputGroup.find(".input-group-append").prepend(suffixElement)
          }

          $original[0].setValue = function (newValue) {
              setValue(newValue)
          }

          var observer = new MutationObserver(function () {
              copyAttributes()
          })
          observer.observe($original[0], {attributes: true})
          copyAttributes()

          $original.after($inputGroup)

          setValue(value)

          $input.on("paste input change focusout", function (event) {
              var newValue = $input[0].value
              var focusOut = event.type === "focusout"
              if (!(locale === "en-US" || locale === "en-GB" || locale === "th-TH")) {
                  newValue = newValue.replace(/[. ]/g, '').replace(/,/g, '.')
              }
              setValue(newValue, focusOut)
              dispatchEvent($original, event.type)
          })

          onPointerDown($buttonDecrement[0], function () {
              stepHandling(-step)
          })
          onPointerDown($buttonIncrement[0], function () {
              stepHandling(step)
          })
          onPointerUp(document.body, function () {
              resetTimer()
          })

          function setValue(newValue, updateInput) {
              if(updateInput === undefined) {
                  updateInput = true
              }
              if (isNaN(newValue) || newValue === "") {
                  $original[0].value = ""
                  if (updateInput) {
                      $input[0].value = ""
                  }
                  value = NaN
              } else {
                  newValue = parseFloat(newValue)
                  min = parseFloat($original.prop("min")) || 0
                  max = isNaN($original.prop("max")) || $original.prop("max") === "" ? Infinity : parseFloat($original.prop("max"))
                  newValue = Math.min(Math.max(newValue, min), max)
                  newValue = Math.round(newValue * Math.pow(10, decimals)) / Math.pow(10, decimals)
                  $original[0].value = newValue
                  if (updateInput) {
                      $input[0].value = numberFormat.format(newValue)
                  }
                  value = newValue
              }
          }

          function dispatchEvent($element, type) {
              if (type) {
                  setTimeout(function () {
                      var event
                      if(typeof(Event) === 'function') {
                          event = new Event(type, {bubbles: true})
                      } else { // IE
                          event = document.createEvent('Event');
                          event.initEvent(type, true, true);
                      }
                      $element[0].dispatchEvent(event)
                  })
              }
          }

          function stepHandling(step) {
            if (!$input[0].disabled && !$input[0].readOnly) {
                  calcStep(step)
                  resetTimer()
                  autoDelayHandler = setTimeout(function () {
                      autoIntervalHandler = setInterval(function () {
                          if (boostStepsCount > config.boostThreshold) {
                              if (autoMultiplier) {
                                  calcStep(step * parseInt(boostMultiplier, 10))
                                  if(boostMultiplier < 100000000) {
                                      boostMultiplier = boostMultiplier * 1.1
                                  }
                                  if (stepMax) {
                                      boostMultiplier = Math.min(stepMax, boostMultiplier)
                                  }
                              } else {
                                  calcStep(step * boostMultiplier)
                              }
                          } else {
                              calcStep(step)
                          }
                          boostStepsCount++
                      }, config.autoInterval)
                  }, config.autoDelay)
              }
          }

          function calcStep(step) {
              if (isNaN(value)) {
                  value = 0
              }
              setValue(Math.round(value / step) * step + step)
              dispatchEvent($original, "input")
              dispatchEvent($original, "change")
          }

          function resetTimer() {
              boostStepsCount = 0
              boostMultiplier = boostMultiplier = autoMultiplier ? 1 : config.boostMultiplier
              clearTimeout(autoDelayHandler)
              clearTimeout(autoIntervalHandler)
          }

          function copyAttributes() {
              $input.prop("required", $original.prop("required"))
              $input.prop("placeholder", $original.prop("placeholder"))
              var disabled = $original.prop("disabled")
              $input.prop("disabled", disabled)
              $buttonIncrement.prop("disabled", disabled)
              $buttonDecrement.prop("disabled", disabled)
              $input.prop("class", "form-control " + $original.prop("class"))
              $inputGroup.prop("class", "input-group " + $original.prop("class") + " " + config.groupClass)
          }

      })

  }

  function isTouchDevice() {
    return ('ontouchstart' in window) ||
      (navigator.maxTouchPoints > 0) ||
      (navigator.msMaxTouchPoints > 0);
  }

  function onPointerUp(element, callback) {

    if ( isTouchDevice() ) {
        element.addEventListener("touchend", function (e) {
            callback(e)
        })

        return;
    }

    element.addEventListener("mouseup", function (e) {
        callback(e)
    })
    element.addEventListener("touchend", function (e) {
        callback(e)
    })
    element.addEventListener("keyup", function (e) {
        if ((e.keyCode === 32 || e.keyCode === 13)) {
            triggerKeyPressed = false
            callback(e)
        }
    })
}

  function onPointerDown(element, callback) {

    if ( isTouchDevice() ) {
      element.addEventListener("touchstart", function (e) {
        if (e.cancelable) {
            e.preventDefault()
        }
        callback(e)
    }, { passive: true } );

      return;
    }
    element.addEventListener("mousedown", function (e) {
        e.preventDefault()
        callback(e)
    })

    element.addEventListener("keydown", function (e) {
        if ((e.keyCode === 32 || e.keyCode === 13) && !triggerKeyPressed) {
            triggerKeyPressed = true
            callback(e)
        }
    })
}

}(jQuery))

jQuery(document).ready(function($) {

  function InputSpinnerInit() {
    $('.quantity > input').InputSpinner({
      buttonsClass: 'btn-sm btn-outline-secondary',
      buttonsWidth: 0
    });
  }

  if ( $( '.elementor[ data-elementor-type="jet-woo-builder" ], .elementor[ data-elementor-type="product" ]' ).length === 0 ) {
      InputSpinnerInit();

      $(document).on('updated_wc_div', function() {
        InputSpinnerInit();
      });
  }

  // Quick cart view.
  $(document).on('click', '.jupiterx-navbar-cart', function(e) {
    if ( '#' !== $(this).attr('href') ) {
      return
    }

    e.preventDefault()
    $('body').addClass('jupiterx-cart-quick-view-overlay');
  })

  $(document).on('click', '.jupiterx-mini-cart-close', function() {
    $('body').alterClass('jupiterx-cart-quick-view-*', '');
  })

});

jQuery(document).ready(function ($) {
  function is_video_media_enabled() {
    if (
      typeof jupiterxOptions === 'undefined' ||
      typeof jupiterxOptions.videMedia === 'undefined'
    ) {
      return null;
    }

    if (jupiterxOptions.videMedia == 0) {
      return false;
    }

    return true;
  }

  function endedVideo () {
    $( 'video' ).on( 'ended', function( event ) {
      var current = $( event.currentTarget ),
        iconTag = current.parent().find( 'i' );

      iconTag.removeClass( 'circle-pause' ).addClass( 'circle-play' );
    } );
  }

  var ProductGallery = function ($target) {

    this.$target = $target;
    this.$images = $target.find('.woocommerce-product-gallery__image');

    if (this.$target.hasClass('jupiterx-product-gallery-static')) {
      this.initZoom();
    } else {
      this.createSlickThumbnailsSlider();
      this.repositionDirectionNav();
      this.disableProductElementorLighBox();
    }

    this.preventSmoothScroll()

    if ( is_video_media_enabled() ) {
      this.playIconTrigger();
      this.handleVideo();
      this.handlePhotoswipe();
      this.handleVideoOnChangeSlide();
      this.handlePhotoswipeIcon();
      this.handleIframe();
      this.handleVideoWithoutSlider();
      this.handleVideoWithoutGallery();
      endedVideo();
    }

    this.handleWcModal();
  }

  ProductGallery.prototype.handleVideoWithoutSlider = function () {
    var $gallery = this.$target;

    $gallery.ready( function () {
      var videos = $gallery.find( 'video' ),
        iframe = $gallery.find( 'iframe' ),
        active = $gallery.find( '.flex-active-slide' );

      if ( videos.length > 0 ) {
        videos.each( function() {
          var iconTag = $(this).parent().find( 'i' );
          iconTag.removeClass( 'circle-pause' ).addClass( 'circle-play' );

          $(this).get( 0 ).pause();
        } );
      }

      if ( iframe.length > 0 ) {
        ProductGallery.prototype.resetIframes( iframe );

        iframe.on( 'load', function ( event ) {
          $( event.currentTarget ).parent().removeClass( 'iframe-on-load' );
          $( event.currentTarget ).show();
          $( event.currentTarget ).next().hide();
        } )
      }

      if ( active.length > 0 && active.find( 'video' ).length > 0 && typeof active.find( 'video' ).attr( 'autoplay' ) !== 'undefined' ) {
        var iconTag = active.find( 'video' ).parent().find( 'i' );

        iconTag.removeClass( 'circle-play' ).addClass( 'circle-pause' );
        active.find( 'video' ).get( 0 ).play();
      }
    } );

    $gallery.on( 'click', '.jupiterx-attachment-media-custom-video-icons', function ( event ) {
      var icon = $( event.currentTarget ),
        iconTag = $( event.currentTarget ).find( 'i' ),
        video = icon.prev(),
        wrapper = $gallery.find( '.jupiterx-attachment-media-iframe' ),
        videos = $gallery.find( 'video' ),
        iframe = $gallery.find( 'iframe' );

      if ( iframe.length > 0 ) {
        ProductGallery.prototype.resetIframes( iframe );
      }

      if ( ! video.get( 0 ).paused ) {
        iconTag.removeClass( 'circle-pause' ).addClass( 'circle-play' );
        video.get( 0 ).pause();

        return;
      }

      wrapper.find( 'i' ).removeClass( 'circle-pause' ).addClass( 'circle-play' );
      videos.trigger( 'pause' );

      iconTag.removeClass( 'circle-play' ).addClass( 'circle-pause' );
      video.get( 0 ).play();
    } );
  }

  ProductGallery.prototype.handleWcModal = function () {
      $( '.pswp__button--close' ).off().on( 'click touchend', function( event ) {
        $( event.target ).closest( '.pswp--open' ).removeClass( 'pswp--open' );

        setTimeout( function() {
          $( event.target ).closest( '.pswp--open' ).removeClass( 'pswp--open' );
        } );
      });

      $( '.pswp__item' ).off().on( 'click touched', function( event ) {
        if ( ! $( event.target ).closest( 'img.pswp__img' ).length ) {
          $( event.target ).closest( '.pswp--open' ).removeClass( 'pswp--open' );
        }
      });
  }

  ProductGallery.prototype.disableProductElementorLighBox = function () {
    var $imageLinks = $(this.$target).find('a');

    $($imageLinks).attr('data-elementor-open-lightbox','no');
  }

  ProductGallery.prototype.handleIframe = function () {
    var gallery = $( this.$target ),
      aciveItem = gallery.find( '.flex-active-slide' );

    if ( aciveItem.length === 0 ) {
      aciveItem = gallery.find( '.woocommerce-product-gallery__image' );
    }

    aciveItem.find( 'iframe' ).on( 'load', function ( event ) {
      $( event.currentTarget ).parent().removeClass( 'iframe-on-load' );
      $( event.currentTarget ).show();
      $( event.currentTarget ).next().hide();
    } )
  }

  ProductGallery.prototype.handlePhotoswipeIcon = function () {
    var gallery = $( this.$target ),
      loadedItem = gallery.find( '.flex-active-slide' ),
      sliderData = gallery.data('flexslider'),
      self = this;

    loadedItem.ready( function () {
      ifram = loadedItem.find( '.jupiterx-attachment-media-iframe' );

      self.initZoom();

      if ( ifram.length > 0 ) {
        gallery.find( '.woocommerce-product-gallery__trigger' ).hide(0);
        return;
      }

      gallery.find( '.woocommerce-product-gallery__trigger' ).show(0);
    } );

    if ( typeof sliderData === 'undefined' ) {
      return;
    }

    sliderData.vars.after = function () {
      var activeItem = gallery.find( '.flex-active-slide' ),
        ifram = activeItem.find( '.jupiterx-attachment-media-iframe' );

      if ( ifram.length > 0 ) {
        gallery.find( '.woocommerce-product-gallery__trigger' ).hide(0);

        activeItem.find( 'iframe' ).on( 'load', function ( event ) {
          $( event.currentTarget ).parent().removeClass( 'iframe-on-load' );
          $( event.currentTarget ).show();
          $( event.currentTarget ).next().hide();
        } );

        return;
      }

      gallery.find( '.woocommerce-product-gallery__trigger' ).show(0);
    }
  }

  ProductGallery.prototype.handleVideoOnChangeSlide = function () {
    var $gallery = this.$target,
      selectors = '.flex-direction-nav a, .flex-control-thumbs li, .woocommerce-product-gallery__image a, .woocommerce-product-gallery__trigger';

    $gallery.on('click', selectors, function (event) {
      var video = $gallery.find( 'video' ),
        iframe = $gallery.find( 'iframe' ),
        active = $gallery.find( '.flex-active-slide' );

      if ( video.length > 0 ) {
        var iconTag = video.parent().find( 'i' );

        iconTag.removeClass( 'circle-pause' ).addClass( 'circle-play' );

        video.each( function( index, item ) {
          $( item ).get( 0 ).pause();
        } );
      }

      if ( iframe.length > 0 ) {
        ProductGallery.prototype.resetIframes( iframe );

        iframe.on( 'load', function( event ) {
          $( event.currentTarget ).parent().removeClass( 'iframe-on-load' );
          $( event.currentTarget ).show();
          $( event.currentTarget ).next().hide();
        } );
      }

      if ( active.length > 0 && typeof active.find( 'video' ).attr( 'autoplay' ) !== 'undefined' ) {
        var iconTag = video.parent().find( 'i' );
        iconTag.removeClass( 'circle-play' ).addClass( 'circle-pause' );

        active.find( 'video' ).get( 0 ).play();
      }
    });
  }

  ProductGallery.prototype.resetIframes = function ( iframe ) {
    iframe.each( function( index, element ) {
      var src =  $( element ).attr( 'src' );

      $( element ).attr( 'src', src );
    } );
  }

  ProductGallery.prototype.playIconTrigger = function () {
    $(document).on('click', '.jupiterx-product-single-play-icon', function () {
      $( this ).next().click();
    });
  }

  ProductGallery.prototype.getGalleryItems = function() {
		var $slides = $( '.woocommerce-product-gallery__image' ),
			items = [];

		if ( $slides.length > 0 ) {
			$slides.each( function( i, el ) {
				var img = $( el ).find( 'img' );

				if ( img.length > 0 ) {
					var large_image_src = img.attr( 'data-large_image' ),
						large_image_w   = img.attr( 'data-large_image_width' ),
						large_image_h   = img.attr( 'data-large_image_height' ),
						alt             = img.attr( 'alt' ),
						item            = {
							alt  : alt,
							src  : large_image_src,
							w    : large_image_w,
							h    : large_image_h,
							title: img.attr( 'data-caption' ) ? img.attr( 'data-caption' ) : img.attr( 'title' )
						};

					items.push( item );
				} else {
          var iframe = $( el ).find( '.jupiterx-attachment-media-iframe' ).parent().html();

          items.push( {
          html: '<div class="jupiterx-pswp-attachment-media-iframe">' + iframe + '</div>'
        } );
        }
			} );
		}

		return items;
	}

  ProductGallery.prototype.handleVideoOnPhotoSwipe = function ( element, photoswipe ) {
    if ( ! $( element ).hasClass( 'pswp--open' ) ) {
      return;
    }

    photoswipe.listen('beforeChange', function() {
      var video = $( element ).find( 'video' ),
        iframe = $( element ).find( 'iframe' ),
        items = $( element ).find( '.pswp__item' );

      if ( video.length > 0 ) {
        var iconTag = video.parent().find( 'i' );

        iconTag.removeClass( 'circle-pause' ).addClass( 'circle-play' );
        video.get( 0 ).pause();
      }

      if ( iframe.length > 0 ) {
        var src =  iframe.attr( 'src' );

        iframe = iframe.attr( 'src', src );

        $( element ).find( 'iframe' ).on( 'load', function ( event ) {
          $( event.currentTarget ).parent().removeClass( 'iframe-on-load' );
          $( event.currentTarget ).show();
          $( event.currentTarget ).next().hide();
        } )
      }

      items.each( function ( index, item ) {
        if ( 'block' === $( item ).css( 'display' ) && typeof $( item ).find( 'video' ).attr( 'autoplay' ) !== 'undefined' ) {
          var iconTag = video.parent().find( 'i' );

          iconTag.removeClass( 'circle-play' ).addClass( 'circle-pause' );
          $( item ).find( 'video' ).get( 0 ).play();
          return;
        }
      } )
    } );
  }

  ProductGallery.prototype.openPhotoswipe = function (e) {
    if ( ! wc_single_product_params.photoswipe_enabled ) {
      return;
    }

    e.preventDefault();

		var pswpElement = $( '.pswp' )[0],
			items       = ProductGallery.prototype.getGalleryItems(),
			eventTarget = $( e.target ),
			clicked;

		if ( eventTarget.is( '.woocommerce-product-gallery__trigger' ) || eventTarget.is( '.woocommerce-product-gallery__trigger img' ) ) {
			clicked = $( '.flex-active-slide' );
		} else {
			clicked = eventTarget.closest( '.woocommerce-product-gallery__image' );
		}

		var options = $.extend( {
			index: $( clicked ).index(),
			addCaptionHTMLFn: function( item, captionEl ) {
				if ( ! item.title ) {
					captionEl.children[0].textContent = '';
					return false;
				}
				captionEl.children[0].textContent = item.title;
				return true;
			}
		}, wc_single_product_params.photoswipe_options );

		// Initializes and opens PhotoSwipe.
		var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
		photoswipe.init();

    ProductGallery.prototype.handleVideoOnPhotoSwipe( pswpElement, photoswipe );
  }

  ProductGallery.prototype.handlePhotoswipe = function () {
    // Disable all default events.
    this.$target.off( 'click', '.woocommerce-product-gallery__trigger' );
    this.$target.off( 'click', '.woocommerce-product-gallery__image a' );

    if ( wc_single_product_params.zoom_enabled ) {
			this.$target.on( 'click', '.woocommerce-product-gallery__trigger', this.openPhotoswipe );
			this.$target.on( 'click', '.woocommerce-product-gallery__image a', function( e ) {
				e.preventDefault();
			});

			// If flexslider is disabled, gallery images also need to trigger photoswipe on click.
			if ( ! wc_single_product_params.flexslider_enabled ) {
				this.$target.on( 'click', '.woocommerce-product-gallery__image a', this.openPhotoswipe );
			}
		} else {
			this.$target.on( 'click', '.woocommerce-product-gallery__image a', this.openPhotoswipe );
		}
  }

  ProductGallery.prototype.handleVideoWithoutGallery = function () {
    var $gallery = this.$target;

    if ( $gallery.find( '.flex-viewport' ).length !== 0 ) {
      return;
    }

    $gallery.on( 'click', '.jupiterx-attachment-media-custom-video-icons', function ( event ) {
      var icon = $( event.currentTarget ),
        iconTag = $( event.currentTarget ).find( 'i' ),
        video = icon.prev();

      if ( ! video.get( 0 ).paused ) {
        iconTag.removeClass( 'circle-pause' ).addClass( 'circle-play' );
        video.get( 0 ).pause();

        return;
      }

      iconTag.removeClass( 'circle-play' ).addClass( 'circle-pause' );
      video.get( 0 ).play();
    } )
  }

  ProductGallery.prototype.handleVideo = function () {
    $( document ).on( 'click', '.jupiterx-attachment-media-custom-video-icons', function ( event ) {
      var icon = $( event.currentTarget ),
        iconTag = $( event.currentTarget ).find( 'i' ),
        video = icon.prev(),
        active = video.closest( '.flex-active-slide' ).length > 0,
        modal = video.closest( '.pswp' ).length > 0;

      if (
        $( 'body' ).hasClass( 'jupiterx-product-template-9' ) ||
        $( 'body' ).hasClass( 'jupiterx-product-template-10' )
      ) {
        active = true;
      }

      if ( ! active && ! modal ) {
        return;
      }

      if ( ! video.get( 0 ).paused ) {
        iconTag.removeClass( 'circle-pause' ).addClass( 'circle-play' );
        video.get( 0 ).pause();

        return;
      }

      iconTag.removeClass( 'circle-play' ).addClass( 'circle-pause' );
      video.get( 0 ).play();
    } );
  }

  ProductGallery.prototype.createSlickThumbnailsSlider = function () {
    var $gallery = this.$target,
      options = {
        infinite: false,
        draggable: false,
        slidesToShow: 7,
        slidesToScroll: 1,
        prevArrow: '<button class="slick-prev" aria-label="Prev" type="button"><svg fill="#333333" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="7.2px" height="12px" viewBox="0 0 7.2 12" style="enable-background:new 0 0 7.2 12;" xml:space="preserve"><path class="st0" d="M2.4,6l4.5-4.3c0.4-0.4,0.4-1,0-1.4c-0.4-0.4-1-0.4-1.4,0l-5.2,5C0.1,5.5,0,5.7,0,6s0.1,0.5,0.3,0.7l5.2,5	C5.7,11.9,6,12,6.2,12c0.3,0,0.5-0.1,0.7-0.3c0.4-0.4,0.4-1,0-1.4L2.4,6z"/></svg></button>',
        nextArrow: '<button class="slick-next" aria-label="Next" type="button"><svg fill="#333333" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="7.2px" height="12px" viewBox="0 0 7.2 12" style="enable-background:new 0 0 7.2 12;" xml:space="preserve"><path class="st0" d="M4.8,6l-4.5,4.3c-0.4,0.4-0.4,1,0,1.4c0.4,0.4,1,0.4,1.4,0l5.2-5C7.1,6.5,7.2,6.3,7.2,6S7.1,5.5,6.9,5.3l-5.2-5C1.5,0.1,1.2,0,1,0C0.7,0,0.5,0.1,0.3,0.3c-0.4,0.4-0.4,1,0,1.4L4.8,6z"/></svg></button>',
      };

    if ($gallery.hasClass('jupiterx-product-gallery-vertical')) {
      options = $.extend(options, {
        vertical: true,
        slidesToShow: 5,
      });
    }

    $gallery.find('.flex-control-thumbs').slick(options);

    if ( is_video_media_enabled() ) {
      galleryItems = $gallery.find( '.woocommerce-product-gallery__image' );

      galleryItems.each(function (index, element) {
        if ( typeof $( element ).data( 'poster' ) !== 'undefined' ) {
          $( '.flex-control-nav' ).find( 'li[data-slick-index=' + index + ']' ).prepend( '<i class="jupiterx-product-single-play-icon"></i>' );
        }
      });
    }

    // Update slick on click flex direction nav.
    $gallery.on('click', '.flex-direction-nav a', function () {
      $gallery.find('.flex-control-nav').slick('slickGoTo', $gallery.find('.flex-active-slide').index());
    });
  }

  ProductGallery.prototype.repositionDirectionNav = function () {
    var $gallery = this.$target,
      positionNav;

    if (!$gallery.hasClass('jupiterx-product-gallery-vertical')) {
      return;
    }

    positionNav = function () {
      var $nav = $gallery.find('.flex-direction-nav'),
        $thumbs = $gallery.find('.flex-control-thumbs')

      $nav.css('left', $thumbs.outerWidth(true))
    }

    $(window).resize(positionNav)
    positionNav()
  }

  ProductGallery.prototype.initZoom = function () {
    if (!$.isFunction($.fn.zoom) && !wc_single_product_params.zoom_enabled) {
      return;
    }

    var $target = this.$target,
      zoomTarget = $target.find('.woocommerce-product-gallery__image');

    var galleryWidth = $target.width(),
      zoomEnabled = false;

    $(zoomTarget).each(function (index, target) {
      var image = $(target).find('img');

      if (image.data('large_image_width') > galleryWidth) {
        zoomEnabled = true;
        return false;
      }
    });

    // But only zoom if the img is larger than its container.
    if (zoomEnabled) {
      var zoom_options = {
        touch: false
      };

      if ('ontouchstart' in window) {
        zoom_options.on = 'click';
      }

      zoomTarget.trigger('zoom.destroy');
      zoomTarget.zoom(zoom_options);
    }
  }

  ProductGallery.prototype.preventSmoothScroll = function () {
    this.$target.on('click', '.flex-direction-nav a', function (event) {
      event.preventDefault();
      event.stopPropagation();
    });
  }

  if ($('.elementor[ data-elementor-type="jet-woo-builder" ]').length === 0) {
    $('.woocommerce-product-gallery').each(function (index, element) {
      new ProductGallery($(element))
    });
  }

  function VariableProduct ($variationsForm) {
    var self = this;
    this.$variationsForm = $variationsForm;
    this.$outOfStockBadge = $('.jupiterx-out-of-stock');
    this.$onSaleBadge = $('.jupiterx-product-badges .jupiterx-sale-badge');
    this.variations = this.$variationsForm.data('product_variations') || [];

    if (typeof VariableProduct._initialized == 'undefined') {

      VariableProduct.prototype.bindEvents = function () {
        this.$variationsForm.on( "woocommerce_variation_select_change", this.onVariationAttributeChange);
        $('.single_variation_wrap').on('show_variation', this.onVariationSelected);
      }

      VariableProduct.prototype.checkOutOfStockStatus = function () {
        if (this.isProductInStock()) {
          return;
        }
        this.$onSaleBadge.hide();
        this.$outOfStockBadge.show();
      }

      VariableProduct.prototype.onVariationAttributeChange = function () {
        var variationId = self.$variationsForm.find('input[name=variation_id]').val() || 0;

        if (variationId === 0) {
          if(!self.isProductInStock()) {
            self.$outOfStockBadge.show();
          } else if (self.isProductOnSale()) {
            self.$onSaleBadge.show();
            self.$outOfStockBadge.hide();
          }
        }
      }

      VariableProduct.prototype.onVariationSelected = function (event, variation) {
        if (variation) {
          self.toggleOutOfStockBadgeVisibility(variation)
        }
      }

      VariableProduct.prototype.toggleOutOfStockBadgeVisibility = function (variation) {
        if (variation.is_in_stock) {
          this.isVariationOnSale(variation.variation_id) ? this.$onSaleBadge.show() : this.$onSaleBadge.hide()
          this.$outOfStockBadge.hide();
        } else {
          this.$onSaleBadge.hide();
          this.$outOfStockBadge.show();
        }
      }

      VariableProduct.prototype.isProductOnSale = function () {
        for (var i = 0; i < this.variations.length; i++) {
          var variation = this.variations[i]
          if (variation.display_price !== variation.display_regular_price) {
            return true;
          }
        }
        return false;
      }

      VariableProduct.prototype.isVariationOnSale = function (variationId) {
        for (var i = 0; i < this.variations.length; i++) {
          var variation = this.variations[i]
          if (
            variation.display_price !== variation.display_regular_price &&
            variationId === variation.variation_id
          ) {
            return true;
          }
        }
        return false;
      }

      VariableProduct.prototype.isProductInStock = function () {
        for (var i = 0; i < this.variations.length; i++) {
          if (this.variations[i].is_in_stock) {
            return true;
          }
        }
        return false
      }

      VariableProduct.prototype.selectOptionControl = function () {
        var $outOfStock = this.$outOfStockBadge;
        var selectedOption ;
        var $selectOption = {
          selected : '.woocommerce div.product form.cart .variations select',
          outOfStockClassName : 'jupiterx-out-of-stock' ,
          outOfStockClass : $outOfStock ,
        }

        if ( !$('body').find($selectOption.outOfStockClassName) ) {
          return;
        }

        $($selectOption.selected).change( function() {
          selectedOption = $($selectOption.selected).children("option:selected").val();
          if ( selectedOption == '' ) {
            $($selectOption.outOfStockClass).css('display', 'none');
          }
        });
      }

      VariableProduct.prototype.handleVariation = function () {
        if ( ! this.$variationsForm || ! this.variations ) {
          return;
        }

        var variations = this.variations;

        this.$variationsForm.on( 'woocommerce_update_variation_values', function( event ) {
          var currentItem = null,
            content = '',
            type = '',
            poster = '',
            gallerytItem = $( '.woocommerce-product-gallery__image:first-child' ),
            defaultValues = gallerytItem.attr( 'data-default' ) ? JSON.parse( gallerytItem.attr( 'data-default' ) ) : {};

          setTimeout( function() {
            price = $( event.currentTarget ).find( '.woocommerce-variation-price' );

            if ( price.length === 0 || 'none' === price.parents( '.woocommerce-variation' ).css( 'display' ) ) {
              return;
            }

            currentItem = $( event.currentTarget ).attr( 'current-image' );

            if ( currentItem === '' ) {
              content = defaultValues.content;
              type = defaultValues.video_type;
              poster = defaultValues.poster;

              variableProduct.handleNavControl( defaultValues.enabled );
            }

            variations.forEach( function( element ) {
              if ( element.image_id === parseInt( currentItem ) ) {
                content = element.jupiterx_attached_media;
                type = element.jupiterx_attached_media_type;
                poster = element.jupiterx_attached_media_poster;

                variableProduct.handleNavControl( element.jupiterx_attached_media_enabled );
              }
            });

            if ( content && content.includes( 'jupiterx-attachment-media-iframe' ) ) {
              $( '.woocommerce-product-gallery__trigger' ).hide(0);
            } else {
              $( '.woocommerce-product-gallery__trigger' ).show(0);
            }

            gallerytItem.html( content );
            gallerytItem.attr( 'data-video-type', type );
            gallerytItem.attr( 'data-poster', poster );
            gallerytItem.find( 'a' ).attr('data-elementor-open-lightbox','no');

            $( window ).trigger( 'resize' );
            variableProduct.handleIframe( gallerytItem );
					  gallerytItem.parents( '.images' ).trigger( 'woocommerce_gallery_init_zoom' );
            endedVideo();
          }, 50 );
        } );
      }

      VariableProduct.prototype.handleIframe = function ( gallerytItem ) {
        var viewport = gallerytItem.closest( '.flex-viewport' ),
          getGalleryVideo = gallerytItem.find( 'video' );

        gallerytItem.find( 'iframe' ).on( 'load', function ( event ) {
          $( event.currentTarget ).parent().removeClass( 'iframe-on-load' );
          $( event.currentTarget ).show();
          $( event.currentTarget ).next().hide();

          viewport.height( $( event.currentTarget ).height() );
        } );

        if ( getGalleryVideo.length > 0 ) {
          viewport.height( getGalleryVideo.height() );
        }
      }

      VariableProduct.prototype.handleNavControl = function ( enabled ) {
        var navItem = $( '.slick-slide:first-child' ),
          icon = navItem.find( '.jupiterx-product-single-play-icon' );

        if ( ! enabled ) {
          icon.remove();

          return;
        }

        if ( icon.length === 0 ) {
          navItem.prepend( '<i class="jupiterx-product-single-play-icon"></i>' );
        }
      }

      VariableProduct._initialized = true;
    }
  }

  if ( $('form.variations_form').length > 0 ) {
    var variableProduct = new VariableProduct($('form.variations_form'));
    variableProduct.bindEvents();
    variableProduct.selectOptionControl();
    variableProduct.checkOutOfStockStatus();

    if ( is_video_media_enabled() ) {
      variableProduct.handleVariation();
    }
  }

  if ( $('body').find('jupiterx-out-of-stock') ) {
    var $checkOptions = {
      selected : '.woocommerce div.product form.cart .variations select',
      outOfStockClassName : 'jupiterx-out-of-stock' ,
      outOfStockClass : '.jupiterx-out-of-stock' ,
    }

    $($checkOptions.outOfStockClass).css('display', 'inline-block');
    checkOptions = $($checkOptions.selected).children("option:selected").val();

    if ( checkOptions == '' ) {
      $($checkOptions.outOfStockClass).css('display', 'none');
    }
  }

  if ( $('body').find('.woocommerce-product-gallery__trigger') ) {
    $('.pswp__button--close').attr('ontouchstart','return false;');
  }

  if ( $('body').find('.woocommerce-product-rating') && $('main .elementor').data('elementor-type') != 'jet-woo-builder' ) {
    $('.woocommerce-review-link').attr( 'href', '#jupiterx-wc-header-reviews' );
    $('#tab-title-reviews').attr( 'id', 'jupiterx-wc-header-reviews' );

    if ( '#jupiterx-wc-header-reviews' === window.location.hash ) {
      $('.wc-tabs').children('li').removeClass( 'active' );
      $('#jupiterx-wc-header-reviews').addClass( 'active' );
      $('#jupiterx-wc-header-reviews a').trigger( 'click' );
    }

    jQuery( '.woocommerce-product-rating > a' ).click( function( event ) {
      $ = jQuery;

      event.preventDefault();
      event.stopPropagation();

      var hash = $('.woocommerce-review-link').attr( 'href' );

      if ( '#jupiterx-wc-header-reviews' === hash ) {
        $('.wc-tabs').children('li').removeClass( 'active' );
        $('#jupiterx-wc-header-reviews').addClass( 'active' );
        $('#jupiterx-wc-header-reviews a').trigger( 'click' );

        history.pushState(null, null, hash);
      }

      if (window.jupiterx.utils.onMobile() && $('body').hasClass('jupiterx-header-mobile-behavior-off')) {
        return
      }

      if (window.jupiterx.utils.onTablet() && $('body').hasClass('jupiterx-header-tablet-behavior-off')) {
        return
      }

      var anchorTarget = $(hash)

      if (anchorTarget.length === 0) {
        return
      }

      var overlap = $( 'body' ).hasClass( 'jupiterx-header-overlapped' );

      var scrollPosition = anchorTarget.offset().top;
      scrollPosition -= overlap ? 2 * $( '.jupiterx-header' ).height() : $( '.jupiterx-header' ).height() + 50;

      if (is_smooth_scroll() === false) {
        $('html, body').stop().animate({
          scrollTop: scrollPosition
        }, 0 )

        return;
      }

      $('html, body').stop().animate({
        scrollTop: scrollPosition
      }, 500, 'swing')
    } );
  }

  if ( $('.elementor-jet-single-images').length > 0 ) {
    $('.elementor-jet-single-images').find( '.woocommerce-product-gallery' ).addClass( 'jupiterx-jet-woo-gallery' );
    $('.elementor-jet-single-images').find( '.flex-active' ).addClass( 'jupiterx-jet-woo-gallery-active-item' );

    $('.elementor-jet-single-images').find( '.flex-control-thumbs img' ).on( 'click', function( event ) {
      $('.elementor-jet-single-images').find( '.flex-active' ).removeClass( 'jupiterx-jet-woo-gallery-active-item' );
      $( event.currentTarget ).addClass( 'jupiterx-jet-woo-gallery-active-item' );
    } );
  }

  if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
    elementorFrontend.hooks.addAction( 'frontend/element_ready/widget', function( $scope ) {
      if ( $scope.data( 'widget_type' ) === 'jet-single-images.default' ) {
        $scope.find( '.woocommerce-product-gallery' ).addClass( 'jupiterx-jet-woo-gallery' );
        $scope.find( '.flex-active' ).addClass( 'jupiterx-jet-woo-gallery-active-item' );

        $scope.find( '.flex-control-thumbs img' ).on( 'click', function( event ) {
          $('.elementor-jet-single-images').find( '.flex-active' ).removeClass( 'jupiterx-jet-woo-gallery-active-item' );
          $( event.currentTarget ).addClass( 'jupiterx-jet-woo-gallery-active-item' );
        } );
      }
    } );
  }
});


