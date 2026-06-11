=== Jupiter X Core ===
Contributors: artbees
Tags: jupiter, jupiterx
Requires at least: 5.6
Tested up to: 6.9
Stable tag: 4.14.1
Requires PHP: 7.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Core functionalities for Jupiter X theme.

== Description ==

Jupiter X Core plugin provides the necessary core functionality for [Jupiter X](https://jupiterx.artbees.net/) theme. All Jupiter X users need to install and activate this plugin to use the full feature of the theme.

- Advanced Control Panel to handle template and plugin installation, manage settings, and more.
- Advanced Customizer to customize different parts of the website.
- Header, footer, single, archive builders.
- New Elementor widgets.
- New Elementor Dynamic tags.
- and more.

It's worth mentioning that, some of the features are disabled for the Jupiter X Lite theme.

== Installation ==

1. Install using the WordPress built-in Plugin installer, or Extract the zip file and drop the contents in the `wp-content/plugins/` directory of your WordPress installation.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Changelog ==
= 4.14.1 =
* Fixed incompatibilities with new ready-made templates.
* Fixed Swiper autoloop bug in Testimonial Carousel widget.

= 4.14.0 =
* Added "Use Site's Logo" toggle option in Site Logo widget to improve UX and visibility, replacing the legacy dynamic tag.
* Added "Close Menu On Scroll" option in Advanced Menu widget.
* Added "ID" field rendering to form frontend.
* Fixed redirection to parent item link on mobile menu for Advanced menu Widget.
* Fixed Post Navigation widget RTL layout, grid wrapping, arrow positioning, grid collapse, and navigation state detection issues.
* Fixed Motion Effects incompatibility with Elementor's Optimized Markup option.
* Fixed popup Conditions/Triggers visibility issue on save dropdown menu for small screens.
* Fixed Custom Search Templates issues with Products Widget and product carousel Widget.
* Fixed form widget multiple file upload issue when having several File Upload fields.
* Fixed fatal error on inline SVG.
* Improved development mode tooltip in general settings for clarity.

= 4.13.0 =
* Added Related to Current Post option in Advanced Posts widget.
* Added Scroll to Beginning option in Advanced Tab widget.
* Added promotion banner system to Control Panel.
* Fixed Navigation Menu vertical mode alignment issue.
* Fixed disappearing Custom Snippets content issue.
* Fixed unintentional scrolling when switching between tabs in Advanced Tab widget.
* Fixed Overlay issue on Off-Canvas layout in Advanced Menu widget.
* Fixed behavior of Toggle Menu Icon on mobile and tablet devices in Advanced Menu Widget.

= 4.12.0 =
* Added AND logic between popup triggers.
* Fixed issues related to popup triggers behavior.
* Fixed column count in Counter widget.
* Fixed Elementor cache and shortcode context for JupiterX Global Widgets.
* Added option to exclude repetitive posts in Advanced Posts widget.
* Fixed couple of warnings in Advanced Posts widget.
* Fixed warning in post widget.

= 4.11.1 =
* Fixed a security issue.
* Added Play Icon section to the Media Carousel widget style tab.
* Added empty state handling for advanced posts widget.

= 4.11.0 =
* Improved Animated Heading widget: clip animation, layout/width handling, animation fixes, SCSS refinements.
* Improved Advanced Posts to support custom fields across widgets and carousel; improved taxonomy/tag generation and rendering when default meta disabled.
* Improved Advanced Nav Menu: better mobile submenu behavior, arrow rotation/state, and guard for empty href links.
* Fixed Stripe and payments: stricter tax rate validation/unserialization; updated tax rate handling in Stripe widget.
* Fixed Video widget autoplay on iPhone.
* Fixed WooCommerce My Account Addresses tab style issue.
* Fixed a security vulnerability.

= 4.10.1 =
* Added a New Feature to Support new bulk license.
* Fixed keeping history of pagination in Advanced Post.
* Fixed displaying of Bundle & virtual product on overlay layout on product carousel.
* Fixed closing Condition modal in elementor editor.
* Fixed displaying WPML condition on Elementor editor.
* Added recaptcha V3 to login form widget.
* Fixed overflow issue on Animated Heading widget.
* Fixed conflict between Event manager Plugin & JupiterX.
* Enabled query result number in Advanced Post widget for archive template.
* Fixed displaying `Condition Display` in Elementor editor for other language than english.
* Fixed Advanced menu to accept Download Link.
* Fixed Product Short Description dynamic tag in the Price List widget.

= 4.9.2 =
* Fixed conflict between JupiterX & Elementor.
* Improved form widget to prevent the form label from being included in sent messages.
* Improveed loading of product gallery image on variable product.
* Compatible Layout Builder with WPML.
* Fixed conflict between two product widget in single page.

= 4.9.1 =
* Fixed some security issues.
* Fixed behavior of After inactivity trigger for popup.
* Added Product brand to woocommerce condition.

= 4.8.12 =
* Fixed some security issues.
* Fixed Author Box Widget in Simplicity Mode.
* Fixed video position on media carousel lightbox.

= 4.8.11 =
* Added Brands option to categories & tags filter in product carousel & Products widgets.
* Added Search Result filter to Products Widget.
* Integrated Product meta with WooCommerce Brand Option.
* Compatible Advanced menu and Advanced Tab with WPML.
* Removed Mask Image option on Team member widget.
* Fixed some security issues.
* Fixed Content position on Classic Call To Action Widget.
* Fixed displaying arrow on Photo Album widget.

= 4.8.10 =
* Compatible JupiterX with Elementor 3.28.0.
* Compatible JupiterX Popup with WPML.
* Improve Form and Email Actions.
* Fixed issue of Product carousel.
* Fixed displaying Arrow on Media gallery lightbox.
* Fixed strips the forward slash on Advanced Menu.

= 4.8.9 =
* Added new settings ( `Post Meta` label ) for Advanced Posts widget.
* Added new option to open a new tab to the link of socials.
* Added Default padding to Products item when set layout to overlay
* Fixed conflict in Layout builder display for removed templates.
* Fixed issue on Shopping Cart - Close on Overly Click.
* Fixed link issue in team member widget when for creative & full overlay.
* Fixed Link to `All Posts` in Post Navigation widget.
* Fixed display eicons in Testimonial Carousel & Testimonial Marquee.
* Fixed responsive layout option on Product carousel widget.
* Fixed typo in Post navigation widget.
* Fixed Content loading of products widget when apply filters.
* Fixed fatal error o product review widget.
* Fixed some security issues.
* Fixed displaying Sticky posts on Search Results regardless match search criteria.

= 4.8.8 =
* Fixed Stripe Button issues.
* Fixed issue on Advanced post.
* Fixed some security issues.
* Fixed issueof Advanced Posts Widget in search layout.
* Fixed conflict between Raven plugin & JupiterX Core.

= 4.8.7 =
* Added excluding the specific heading in to the Table of Content widget.
* Fixed displaying Popups in draft & trash status.
* Fixed conflict between Ribbon & button on call to action widget.
* Fixed displaying title on Media Gallery on overlay layout.
* Fixed updating Jet Plugins from JupiterX update page.

= 4.8.6 =
* Fixed issues of incompatibility with Elementor 3.26.0.
* Fixed some security issues.
* Fixed post excerpt issue on post & advanced posts widgets.
* Fixed color parent item in navigation menu.

= 4.8.5 =
* Fixed issues in Social Login widget.
* Fixed issues in Advanced Menu widget.
* Fixed issues in My Account widget.
* Fixed UI issues in Layout Builder.
* Fixed missing Alt Text in Product Gallery widget.
* Fixed page change on sub-menu close issue in Navigation Menu widget.
* Improved file upload in Form widget with max file size restriction.
* Added Overlay feature to Flip Box widget.
* Added scrolling to content feature in Advanced Accordion widget.

= 4.7.8 =
* Improved Shopping Cart widget to be compatible with WooCommerce's `Ajax add to cart` setting.
* Removed `waypoints.js` library dependency from the widgets.
* Improved Social Login widget functionality.
* Added Custom Excerpt setting to Advanced Posts, Posts, and Posts Carousel widgets.

= 4.7.5 =
* Fixed an issue where the Page Title Bar content was being overridden.
* Fixed issues with the My Account widget.
* Fixed the icon size issue on the Add to Cart button in the Products and Products Carousel widgets.
* Fixed issue with displaying SVG icons in the Products Carousel.
* Fixed various issues in the Posts Carousel widget.
* Fixed various issues in the Media Carousel widget.
* Fixed various issues in the PayPal Button widget.
* Fixed some security issues.
* Fixed PHP warning in the Testimonial Carousel widget.
* Fixed PHP warnings and submit button animation issues in the Form widget.
* Improved settings for the Testimonial Marquee widget.
* Added pagination to the Product Review widget.

= 4.6.9 =
* Fixed an issue with the shortcode dynamic tag in the Testimonial Carousel widget.
* Fixed issues with custom fonts.
* Improved file upload functionality in the Form widget.
* Removed the redundant aria-hidden attribute from HTML tags.
* Added divider color setting for meta in the Advanced Posts widget.
* Added template selector setting for content in the Testimonial Carousel widget.

= 4.6.6 =
* Fixed a vulnerability issue in the file upload of the Form widget.
* Fixed Layout Builder issues.

= 4.6.5 =
* Fixed vulnerability issues.

= 4.6.0 =
* Fixed style issue on Product Data Tabs widget.
* Fixed active page color issue on Navigation Menu widget.
* Fixed Post Content widget issues.
* Fixed PHP deprecation notice on Tooltip feature.
* Fixed Space Between setting issue on Media Carousel widget.
* Fixed JupiterX Popups entrance animation issue.
* Improved the Layout Builder to be compatible with Elementor Pro Post Content widget.
* Added HTML field type to Form widget.

= 4.5.0 =
* Fixed poster issue on Media Gallery widget.
* Fixed Gallery widget issues.
* Fixed PHP fatal error on Custom Scripts feature.
* Fixed condition issue on Popup Builder.
* Fixed Custom Fonts feature issues.
* Fixed Form widget issues.
* Fixed CTA button issue on Post Carousel widget.
* Fixed Product Gallery widget issues.
* Fixed Advanced Tab widget issues.
* Fixed Advanced Video widget issues.
* Fixed Animated Gradient feature issues.
* Improved the Layout Builder to be compatible with block editor pages.
* Improved the widgets' links to be compatible with the WPML plugin.

= 4.4.0 =
* Fixed PHP deprecated notices on Product Review widget.
* Fixed fatal error on Form widget Popup action.
* Fixed Advanced Menu widget issues.
* Fixed Jupiterx Popup Dynamic Tag issues.
* Fixed scroll and empty cart content issues on the Shopping Cart widget quick view.
* Fixed Search Form and Products Carousel widgets incompatibilities with inline font Icons.
* Fixed link and sales badge issues on Products widget while using Sellkit Product Filter.
* Improved widgets to be compatible with WPML plugin.
* Improved saving method in Layout Builder feature.
* Improved Layout Builder feature to be compatible with Maintenance mode.
* Improved Popup Builder feature to be compatible with Maintenance mode.
* Added Video Playlist widget.

= 4.3.0 =
* Fixed PHP warning on Layout Builder feature.
* Fixed Product Gallery widget on RTL sites.
* Fixed depreciated codes on Cart widget.
* Fixed issues on Shopping Cart widget.
* Improved Setup Wizard feature.
* Updated Twitter icon to X on widgets.
* Added Convert to Header toolbar feature to JupiterX Popup.
* Added JupiterX Popup dynamic tag.
* Added JupiterX Popup action to Form widget.

= 4.2.0 =
* Fixed Shopping Cart widget issues.
* Fixed Layout Builder issues.
* Fixed custom icon issue on Navigation Menu widget.
* Fixed duplication content on JupiterX Popups list.
* Removed Addons and Deals from Dashboard.
* Removed Maintenace condition from Layout Builder.
* Removed Custom CSS/JS from post settings on Simplicity activation.
* Improved Post Comment widget to be compatible with Layout Builder.
* Improved Shopping Cart widget to be compatible with Lottery for WooCommerce plugin.
* Improved Shopping Cart widget to be compatible with WooCommerce Product Bundles plugin.
* Improved Shopping Cart widget to be compatible with Cart and Checkout pages.
* Improved JupiterX Popups performance on frontend.
* Improved Layout Builder to show created templates directly inside Elementor templates.
* Improved OAuth method on Social Login widget.
* Improved JupiterX Popups to be compatible with Preview Settings.
* Improved JupiterX Popups not to work without Triggers.
* Added Image Accordion widget.
* Added Shop page to Elementor WooCommerce Pages.
* Added Enable Ajax setting to Shopping Cart widget.
* Added Label to Search Form widget.

= 4.1.0 =
* Fixed Title link issue in Team Member widget.
* Fixed order issue in Slider Widget.
* Fixed Arrows issue in Posts Carousel widget.
* Fixed Background Activities issue on closing JupiterX Popup.
* Fixed fatal error in My Account widget.
* Fixed fatal error in Dashboard on Elementor deactivation.
* Improved scrolling performance by using passive listeners.
* Improved Post Comment widget to be enabled by Elementor Post Settings.
* Improved the Simplicity Mode feature.
* Improved JupiterX Display feature to be compatible with the Elementor Alignments.
* Improved Elementor settings on a fresh install to enable Grid Container and Editor Topbar in settings.
* Added Reading Time feature to Advanced Posts widget.
* Added Reading Time feature to Posts widget.
* Added Reading Time feature to Posts Carousel widget.

= 4.0.0 =
* Fixed incompatibilities with PHP 8.2.
* Fixed PHP warning on My Account widget.
* Fixed Email action issue on Form widget.
* Fixed Icon size incompatibility with the Inline Font Icons feature on the Search Form widget.
* Fixed issue on Elementor Library modal in editor.
* Fixed Publish Button issue on Layout Builder.
* Fixed User Attributes condition issue on Layout Builder.
* Fixed fatal error on Product Title widget.
* Fixed Fixed Column’s Vertical Alignment Issue with Display Option.
* Improved Jupiterx elementor widgets default colors on a fresh install
* Improved Advanced Posts & Posts widgets to show custom taxonomies on meta list and sortable feature.
* Improved import readymade-website functionality to prevent Comments from Being Imported.
* Improved Products widget to be compatible with Sellkit Product Filter widget.
* Improved the Simplicity Mode feature.
* Added Site Logo dynamic tag to Site Logo widget.
* Added Site Logo feature to advanced menu widget on enabling Simplicity Mode.
* Added Setup Wizard feature.

= 3.8.6 =
* Fixed Slideshow effect issue in the Media Carousel widget.
* Fixed placeholder issue in form widget date picker.
* Improved the Simplicity Mode feature
* Improved a few parts of the dashboard.

= 3.8.4 =
* Fixed Post Content widget deprecated notice.
* Fixed Media Carousel widget loading issue.
* Fixed Layout Builder issues.
* Fixed Control Panel issues.
* Fixed User Type trigger issue in Popup Builder.
* Fixed Simplicity Mode issues.
* Improved Layout Builder to be compatible with customizer.
* Improved Form widget upload file to be compatible with PHP 8.2.
* Improved My Account widget to be compatible with all permalink structures.
* Improved Cart widget to be compatible with Sellkit Dynamic Discount.
* Improved Advanced Menu widget to be compatible with Elementor Pro dynamic tags.

= 3.8.0 =
* Fixed custom snippet feature issues.
* Improved Shopping Cart widget to be compatible with RTL sites.
* Improved Add to Cart widget to be compatible with Sellkit Swatches.
* Added Simplicity Mode feature.
* Added new settings for Button widget in Jupiterx Popup.

= 3.7.0 =
* Fixed pagination color issue on Post Carousel widget.
* Fixed margin bottom issue on Advanced Video widget.
* Fixed Posts Per View issue on Products Carousel widget.
* Fixed Slides Per View issue on Media Carousel widget.
* Fixed Custom Tab Content issue on My Account widget.
* Fixed Submenu style issues on Advanced Menu widget.
* Fixed overflow issue in mobile view on Post Content & Post Comments widgets.
* Fixed Link option issue on Elementor column.
* Fixed dropdown arrow issue on Add To Cart widget.
* Fixed Counter issue on Shopping Cart widget.
* Fixed rendering issue in editor on Product Gallery widget.
* Fixed Tooltip feature issues.
* Improved Tooltip feaure to be compatible with Jet Tricks plugin.
* Improved WooCommerce Settings feature to be compatible with WooCommerce native settings.
* Improved Layout Builder conditions to show active templates on top.
* Improved Shopping Cart widget to be compatible with WooCommerce Product Add-ons plugin.
* Added Popup Builder feature.
* Added Circle Progress widget.
* Added Category Count option to Categories widget.

= 3.5.6 =
* Fixed Product Gallery widget issues.
* Fixed Call to Action widget issues.
* Fixed Shopping Cart widget issues.
* Fixed Add to Cart widget issues.
* Fixed box-shadow and flip and swap animation issues on Products widget.
* Fixed PHP notices issues on Animated Gradient feature.
* Fixed Animations setting issues on Button widget.
* Fixed loading video issue on Advanced Video widget.
* Fixed incompatibility issues with MasterStudy LMS plugin.
* Fixed incompatibility issues with Phlox Core plugin.
* Fixed Add Template button issues on Elementor editor page.
* Improved Shopping Cart widget to be compatible with Sellkit Variation Swatches.
* Improved Posts widget to be compatible with WooCommerce Products post type.
* Improved Archive Title widget to be enabled on Page Title Bar template.
* Improved Add Template to install template without elementor sign-up.
* Added Decimal Slides Per View setting to Testimonial Carousel widget.
* Added new settings to Add to Cart widget.
* Added custom filter for Cart and Checkout pages condition to Shopping Cart widget.

= 3.4.6 =
* Fixed PHP notice on Product Data Tabs widget.
* Fixed "Fade and Swap" issue Products widget.
* Fixed Shopping Cart widget conflict.
* Improved Products widget to be compatible with Sellkit Product Filter widget.
* Improved Shopping Cart widget to be compatible Sticky Header and Motion Effect.

= 3.4.3 =
* Fixed vulnerability issues.
* Fixed JS error on checkout page.
* Fixed JS error on Product Gallery widget.
* Fixed add to cart ajax issue on Shopping Cart widget.
* Fixed overflow issue with inline variations on Add to Cart widget.
* Fixed box-shadow issue on Cart widget.
* Fixed issues on Products widget.
* Fixed issues on search page.
* Improved Layout Builder condition modal with new design.
* Improved add to cart ajax on Shopping Cart widget to be compatible with group products.
* Added filter to customize excerpt on Advanced Posts widget.

= 3.3.8 =
* Fixed vulnerability issues.
* Fixed icon issue on My Account widget.
* Fixed Add to Cart widget issues.
* Fixed Table View issues on Product Meta widget.
* Added Margin setting to Meta section on Advanced Post widget.

= 3.3.5 =
* Fixed vulnerability issues.
* Fixed WooCommerce Settings feature issues.
* Added App Secret to Facebook Social Login widget.

= 3.3.0 =
* Fixed Product Carousel issue on Dashboard Settings.
* Fixed Order Received page issue on Layout Builder.
* Fixed pagination issue on Products widget.
* Fixed Advanced Menu widget issues.
* Fixed Table of Content widget issues.
* Fixed My Account widget issues.
* Fixed Cart widget issues.
* Fixed Toggle Icon size issue on Advanced Accordion widget.
* Fixed Layout builder issues.
* Fixed variation issue on Add to Cart widget.
* Improved Add to Cart widget on editor.
* Improved Testimonial Carousel widget to be compatible with wp-rocket plugin.
* Improved Site Logo widget to be compatible with retina logo.
* Added WooCommerce Notices widgets.
* Added WooCommerce Settings feature.
* Added button icon selector to Add to Cart widget.
* Added new settings to Cart widget.
* Added new settings to Shopping Cart widget.
* Added edit Message content option to Form widget.

= 3.2.0 =
* Fixed Custom Message on Login Form widget.
* Fixed Forgot Password extra padding issue on Login Form widget.
* Fixed price color issue on Product Price widget.
* Fixed height calculation issue on Products widget.
* Fixed Add to Cart button animation while using Flip and Swap on Products widget.
* Fixed image size issue on Products widget.
* Fixed background gradient transition issue on Button widget.
* Fixed Hover Effects issue on Button widget.
* Fixed Custom CSS/JS incompatibilities with PHP 8.1.
* Fixed Content Effects issues on Advanced Menu widget.
* Fixed Excerpt Length issue on Advanced Posts widget.
* Fixed undefined function ctype_digit fatal error.
* Fixed default value issue on Product Short Description widget.
* Fixed white-space issue on Content Marquee widget.
* Improved Layout Builder.
* Improved Posts widget to be compatible with Polylang plugin.
* Improved Sorting and Result Count on Products widget.
* Added Depicter as new partner.
* Added Post Meta for Header and Footer templates on Layout Builder.
* Added new settings for Select and Text swatches on Add to Cart widget.
* Added Checkmark to Color swatch on Add to Cart widget.

= 3.1.0 =
* Fixed some security issues.
* Fixed Products widget issues.
* Fixed Custom Font issue in Elementor Global settings.
* Fixed Advanced Menu widget issues.
* Fixed Advanced Posts widget issues.
* Fixed Image Comparison widget issues with wp-rocket plugin.
* Fixed Advanced Tab widget issues.
* Fixed Short Description widget issues.
* Fixed Table of Content widget issues.
* Fixed Product Reviews widget avatar issue.
* Fixed Login Form widget Forgot Password style issue.
* Fixed Page Title Bar issue.
* Fixed swatches issues on Add to Cart widget.
* Fixed get_page_by_title deprecated function.
* Improved Call To Action widget settings.
* Improved Testimonial Carousel widget settings.
* Improved Media Carousel widget settings.
* Improved Reviews widget settings.
* Improved Slider widget settings.
* Improved Products Carousel widget settings.
* Improved Sticky Media Scroller widget settings.
* Improved Products widget to be compatible with Sellkit Product Filter widget.
* Added new settings for Product Gallery widget.
* Added Box Shadow for Flip Box widget.
* Added responsive Aspect Ratio & Media Position for Posts Carousel widget.
* Added responsive Aspect Ratio & Media Position for Advanced Posts widget.
* Added responsive Aspect Ratio & Media Position for Media Gallery widget.
* Added responsive Aspect Ratio & Media Position for Products Carousel widget.
* Added responsive Aspect Ratio & Media Position for Products widget.
* Added responsive Aspect Ratio & Media Position for Sticky Media Scroller widget.
* Added responsive Aspect Ratio & Media Position for Video widget.
* Added Image Gallery to WooCommerce product variations.
* Added Content Effects to Advanced Menu wdiget.

= 3.0.0 =
* Fixed Testimonial Carousel, Media Carousel and Reviews issues.
* Fixed Product Meta widget spacing issue.
* Fixed Product Gallery widget lightbox issue.
* Fixed Register and Login widgets issues.
* Fixed Navigation Menu widget issues.
* Fixed Product Price widget font family issue.
* Fixed Content Switch widget issues.
* Fixed Cart widget issues.
* Fixed Code Highlight widget issues.
* Fixed Progress Tracker widget issues.
* Fixed import template issue.
* Fixed 404 template issues on Layout Builder.
* Improved Products widget.
* Improved Video widget.
* Improved Post Comments widget.
* Improved Posts Carousel widget.
* Improved Media Carousel widget.
* Improved Button widget.
* Improved Image widget.
* Improved Photo Roller widget.
* Improved Layout Builder to be compatible with WPML plugin.
* Added Products Carousel widget.
* Added Advanced Posts widget.
* Added Post Terms widget.
* Added Media Gallery widget.
* Added Image Comparison widget.
* Added Table of Contents widget.
* Added Wrapper Link feature.
* Added Animated Gradient feature.
* Added Text Marquee widget.
* Added Content Marquee widget.
* Added Content Marquee widget.
* Added Testimonial Marquee widget.
* Added Sticky Media Scroller widget.
* Added Advanced Accordion widget.
* Added Added Team Members widget.

= 2.7.0 =
* Fixed Social Share widget icon issues.
* Fixed Advanced Tab widget issues.
* Fixed Cart widget issues.
* Fixed Counter widget issues.
* Fixed Motion Effects issues.
* Fixed Animated Heading widget issues.
* Improved Layout Builder performance.
* Improved Button widget Padding option.
* Improved Image widget max-width option.
* Improved JupiterX Dashboard.
* Added Mobile Stacking option to the Advanced Tab widget.

= 2.6.4 =
* Fixed Layout Builder loading assets issue.
* Fixed Motion Effect issue.
* Fixed Social Login widget issues.
* Fixed incompatibilities with PHP 8.1.3.
* Fixed Post Meta widget issue.
* Fixed custom control UI issue in Elementor.
* Fixed Preview Settings issues.
* Fixed Author Box widget issue on Layout Builder.
* Fixed Custom Snippet conditions issue.
* Fixed Elementor deprecated notices.
* Improved Register and Login widgets.
* Improved AJAX calls on Dashboard.
* Added Reading Time to Post Meta widget.

= 2.5.9 =
* Fixed Incompatibility issue with PHP 8.1 in the nav menu.
* Fixed dark icons in business hours widget.
* Fixed deprecated error in previous release.
* Fixed preview settings issues.
* Added Social Share widget.
* Added Paypal and Stripe buttons widget.
* Added Inline SVG widget.
* Added Progress Tracker widget.
* Added Code Highlight widget.

= 2.5.5 =
* Fixed critical errors that happened while using child themes.

= 2.5.4 =
* Fixed some features' redundant codes.

= 2.5.3 =
* Added cart widget.
* Added scroll snap feature.
* Added media carousel widget's revision.
* Added post navigation widget.
* Added testimonial carousel widget's revision.
* Added a switch to disable the custom quantity field.
* Added a loader for load more button.
* Improved widgets availability.
* Improved post comment compatibility with customizer.
* Improved global widget attributes.
* Fixed a small bug in heading of Ornaments theme.
* Fixed checkbox bug in the form widget.
* Fixed the Elementor extra ads.
* Fixed importing issue in the WooCommerce.
* Fixed php notice in the logs.
* Fixed the error after deactivating the theme.
* Fixed advanced menu submenu bug.

= 2.5.2 =
* Fixed global widget issue in fresh installation.
* Fixed layout builder importing issue.
* Fixed advanced menu small control issue.
* Fixed custom icon typos.
* Fixed product rating link issue.
* Fixed media attachment compatibility issue.
* Fixed product additional info errors.

= 2.5.1 =
* Fixed Layout builder remove button issue.
* Fixed undefined array key error in nav menu.
* Fixed removing empty font type URL from css.
* Fixed file upload issue in form widget.
* Fixed incompatibilities with php 7.
* Fixed alignment controls in slides.
* Fixed custom snippet js error.
* Fixed Layout builder admin js issues.

= 2.5.0 =
* Fixed elementor pro ads.
* Fixed header issue with custom template.
* Fixed corocobloc plugin conflict.
* Fixed pot file generation small issue.
* Fixed search form error.
* Fixed white label issue.
* Fixed some conditions functionality in layout builder.
* Fixed menu icons conflict.
* Fixed lite theme version rollback issue.
* Fixed SellKit warning.
* Fixed Product widget tag issue.
* Added Product title widget.
* Added Product gallery widget.
* Added Product reviews widget.
* Added Additional information widget.
* Added Add to cart widget.
* Added Product data tabs widget.
* Added Global widget feature.
* Added Button widget extra options.
* Added Motion effect feature.
* Added Custom attribute feature.
* Added Role manager feature.
* Added Product price widget.
* Added Product rating widget.
* Added Product meta widget.
* Added Product content widget.
* Added Product short description widget.
* Added Product short description widget.
* Added Tooltip feature.
* Added My account widget.
* Added Custom css feature.
* Added Dynamic content feature.
* Added Site title widget.
* Added Post title widget.
* Added Author box widget.
* Added Woocommerce breadcrumb widget.
* Added Advanced menu widget.
* Added Custom fonts feature.
* Added Custom icons feature.
* Added Pricing table widget.
* Added Animated heading widget.
* Added Flip box widget.
* Added Price list widget.
* Added Archive title widget.
* Added Description widget.
* Added Lottie widget.
* Added Call to action widget.
* Added Hotspot widget.
* Added Media carousel widget.
* Added Testimonial carousel widget.
* Added Slider widget.
* Added Reviews widget.
* Added Business hours widget.
* Added Product media attachment feature.
* Added Content switch widget.
* Improved template importing process.
* Improved Form elementor widget.
* Improved Layout builder issues.
* Improved location of some conditions in layout builder.
* Improved showing widgets based on the type of page.
* Improved Layout builder UX.
* Improved Video widget.
* Improved Layout builder compatibility with JetWooBuilder.

= 2.1.1 =
* Fixed some security issues.

= 2.1.0 =
* Fixed some security issues.

= 2.0.9 =
* Fixed security issues.
* Improved control panel.
* Improved customizer.

= 1.0.0 =
* Initial release.
