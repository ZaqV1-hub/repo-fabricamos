# JetTricks For Elementor

Enjoy adding animation on the fly without coding skills

# ChangeLog

## 1.5.9
* Added: per-hotspot `Show Tooltip on Init` option;
* Added: `jet-tricks/hotspots/show_on_init` hook to filter tooltip visibility on init for each hotspot;
* Added: `jet-tricks/hotspots/icon_html` hook to filter hotspot icon HTML output;
* Added: `Word Count` option for controlling visible content height in Unfold widget;
* Fixed: prevent double initialization of JetForm inside tabs.

## 1.5.8
* Added: `Follow Cursor` control for Tooltip [Crocoblock/suggestions#2276](https://github.com/Crocoblock/suggestions/issues/2276);
* Fixed: Particles not initializing with Elementor Element Cache enabled;
* Updated: Popper.js to v2.11.8 [Crocoblock/suggestions#8261](https://github.com/Crocoblock/suggestions/issues/8261).

## 1.5.7
* Added: JetEngine Query Gateway support for Hotspots widget;
* Fixed: prevent MutationObserver recursion in sticky columns [Crocoblock/suggestions#6203](https://github.com/Crocoblock/suggestions/issues/6203);
* Fixed: shortcode processing in Tooltips [Crocoblock/suggestions#8253](https://github.com/Crocoblock/suggestions/issues/8253);
* Fixed: tooltip display positioning in editor for Hotspots widget.

## 1.5.6
* Fixed: WP Security Standard warnings.

## 1.5.5.1
* Fixed: XSS vulnerability;
* Fixed: icon display in satellite widget.

## 1.5.5
* Added: Read Less functionality to Read More widget [Crocoblock/suggestions#6769](https://github.com/Crocoblock/suggestions/issues/6769);
* Added: Style controls for Button in Unfold widget (Icon size, color, margin) [Crocoblock/suggestions#6210](https://github.com/Crocoblock/suggestions/issues/6210);
* Fixed: Separator visibility when unfold button is hidden [Crocoblock/suggestions#8181](https://github.com/Crocoblock/suggestions/issues/8181);
* Fixed: Unfold widget not resizing on content height change [Crocoblock/suggestions#4510](https://github.com/Crocoblock/suggestions/issues/4510);

## 1.5.4.2
* Fixed: sanitize user input in Satellite to prevent XSS;
* Fixed: security vulnerability in AJAX get_query_control_options endpoint.

## 1.5.4.1
* Fixed: tooltip content duplication.

## 1.5.4
* Added: `Link` control for Satellite [Crocoblock/suggestions#217](https://github.com/Crocoblock/suggestions/issues/217);
* Added: ability to use Elementor Template as Tooltip content [Crocoblock/suggestions#934](https://github.com/Crocoblock/suggestions/issues/934);
* Added: gradient mask (CSS mask-image) option for Unfold widget [Crocoblock/suggestions#8116](https://github.com/Crocoblock/suggestions/issues/8116);
* Added: `Spacing` control for the button in Unfold widget [Crocoblock/suggestions#8136](https://github.com/Crocoblock/suggestions/issues/8136);
* Fixed: layout shift on initial load in Unfold widget [Crocoblock/suggestions#8115](https://github.com/Crocoblock/suggestions/issues/8115).

## 1.5.3
* Added: `Separator Z-index` control for Unfold widget;
* Added: `Interactive Tooltip` control for Tooltip settings;
* Fixed: content height recalculation after state change in Unfold widget;
* Fixed: [Crocoblock/suggestions#3625](https://github.com/Crocoblock/suggestions/issues/3625);
* Updated: extended size units for `Closed Height` and `Separator Height` controls in Unfold widget.

## 1.5.2
* Added: `Z-index` control for Sticky Column;
* Added: `Fixed` option to Sticky Behavior control;
* Fixed: tooltip display in editor;
* Fixed: Sticky Column support for sections;
* Updated: compatibility with Elementor 3.26;
* Updated: removed unused REST API endpoint `elementor-template` for improved security and performance.

## 1.5.1.1
* Fixed: prevent unauthorized access to templates in REST API;

## 1.5.1
* Added: excluded the JetProductGallery's GallerySlider and Elementor's Accordion widgets from initialization;
* Fixed: animation glitch in Chrome when revealing sections in Read More widget;
* Fixed: correct behavior of Unfold widget inside Loop Carousel.

## 1.5.0
* Added: `Sticky Behavior` option to container for Sticky Columns;
* Added: Template selection functionality for Hotspots Widget;
* Added: support for tsParticles Library v3.0.2;
* Fixed: array key 'separator_height' in the Unfold Widget.

## 1.4.10
* Added: additional height units (% and vh) support for Unfold widget;
* Updated: JetDashboard module to 2.2.0 version;
* Updated: compatibility with Elementor 3.24;
* Fixed: 'Show Effect: None' in the Read More widget.

## [1.4.9](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.9)
* Added: Compatibility Jet Tricks particles with Jet Listing
* Added: New icons of widgets
* Fixed: Sticky compatibility with Flexbox Container
* Fixed: minor fixes

## [1.4.8](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.8)
* Added: compatibility Elementor Loop Carousel with Unfold widget
* Fixed: elements with an ARIA [role] for tooltip
* Fixed: tooltip on mobile device
* Fixed: deprecated PHP 8.1 in the unfold widget
* Fixed: minor fixes
* Updated: Vue js module

## [1.4.7](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.7)
* Added: [Crocoblock/suggestions#6635](https://github.com/Crocoblock/suggestions/issues/6635)
* Added: [Crocoblock/suggestions#6866](https://github.com/Crocoblock/suggestions/issues/6866)
* Added: Particles, Unfold compatibility with Elementor Dropdown Menu
* Added: Sticky compatibility with Flexbox Container
* Fixed: Tooltip work in inner section
* Fixed: minor fixes

## [1.4.6.3](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.6.3)
* Fixed: elementor-template Rest API endpoint

## [1.4.6.2](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.6.2)
* Fixed: minor fixes

## [1.4.6.1](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.6.1)
* Updated: security issue

## [1.4.6](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.6)
* Updated: JetDashboard Module

## [1.4.5](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.5)
* Fixed: Сompatibility global styles for elementor 3.15.0

## [1.4.4](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.4)
* Added: [Crocoblock/suggestions#6527](https://github.com/Crocoblock/suggestions/issues/6527)

## [1.4.3](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.3)
* Update: JetDashboard Module to v2.0.9
* Fixed: accessibility
* Fixed: minor issues

## [1.4.2](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.2)
* Added: [Crocoblock/suggestions#5531](https://github.com/Crocoblock/suggestions/issues/5531)
* Fixed: сompatibility with Elementor 3.7
* Fixed: minor issues

## [1.4.1](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.1)
* Fixed: elementor 3.6 compatibility

## [1.4.0](https://github.com/ZemezLab/jet-tricks/releases/tag/1.4.0)
* Added: [Crocoblock/suggestions#3991](https://github.com/Crocoblock/suggestions/issues/3991)
* Added: [Crocoblock/suggestions#4361](https://github.com/Crocoblock/suggestions/issues/4361)
* Fixed: minor issues

## [1.3.9](https://github.com/ZemezLab/jet-tricks/releases/tag/1.3.9)
* Added: [Crocoblock/suggestions#2674](https://github.com/Crocoblock/suggestions/issues/2674)
* Added: [Crocoblock/suggestions#2767](https://github.com/Crocoblock/suggestions/issues/2767)
* Fixed: widgets compatibility with new breakpoints
* Fixed: rtl compatibility
* Fixed: minor Issues

## [1.3.8](https://github.com/ZemezLab/jet-tricks/releases/tag/1.3.8)
* Added: [Crocoblock/suggestions#3282](https://github.com/Crocoblock/suggestions/issues/3282)
* Fixed: minor Issues

## [1.3.7](https://github.com/ZemezLab/jet-tricks/releases/tag/1.3.7) - 13.05.2021
* Fixed: Hot Fix & Minor Issues

## [1.3.5](https://github.com/ZemezLab/jet-tricks/releases/tag/1.3.5) - 03.03.2021
* Fixed: Elementor 3.1.2 compatibility issue

## [1.3.4](https://github.com/ZemezLab/jet-tricks/releases/tag/1.3.4) - 01.02.2021
* Update: JetDashboard Module to v2.0.6
* Added: an opportunity to set the image size in the Hotspots widget
* Added: compatibility the Hotspots widget with JetPlugins Dynamic Data Addon plugin
* Added: better compatibility with JetEngine plugin
* Added: [Crocoblock/suggestions#1700](https://github.com/Crocoblock/suggestions/issues/1700)
* Added: [Crocoblock/suggestions#1487](https://github.com/Crocoblock/suggestions/issues/1487)
* Fixed: [Crocoblock/suggestions#909](https://github.com/Crocoblock/suggestions/issues/909)

## [1.3.3](https://github.com/ZemezLab/jet-tricks/releases/tag/1.3.3) - 14.12.2020
* Added: init sticky column after images loaded
* Added: an ability to use dynamic tags for widgets and extension
* Fixed: init tooltip if widget content is empty

## [1.3.2](https://github.com/ZemezLab/jet-tricks/releases/tag/1.3.2) - 12.11.2020
* Update: JetDashboard Module to v2.0.4
* Settings manager hot fix

## [1.3.1](https://github.com/ZemezLab/jet-tricks/releases/tag/1.3.1) - 11.11.2020
* Update: JetDashboard Module to v2.0.3

## [1.3.0](https://github.com/ZemezLab/jet-tricks/releases/tag/1.3.0) - 06.10.2020
* Update: JetDashboard Module to v2.0.0

## [1.2.12](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.12) - 01.09.2020
* Fixed: the Unfold widget

## [1.2.11](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.11) - 28.08.2020
* Added: getting a list of templates in controls through ajax
* Update: JetDashboard Module to v1.1.0
* Fixed: compatibility with Elementor 3.0
* Fixed: init the Unfold widget

## [1.2.10](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.10) - 17.07.2020
* Update: JetDashboard Module to v1.0.16
* Fixed: prevent Rest API error notice

## [1.2.9](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.9) - 11.05.2020
* Added: Icon Position/Orientation/Gap controls in the View More widget
* Fixed: tooltip overlap in the Hotspot widget

## [1.2.8](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.8) - 27.03.2020
* Added: support for Font Awesome 5 and SVG icons
* Added: template content in the Unfold widget

## [1.2.7](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.7) - 21.02.2020
* Update: Jet-Dashboard module to 1.0.10
* Update: Tooltip extention in the editor
* Added: compatibility with Custom Link Attributes in Elementor 2.9

## [1.2.6](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.6) - 04.12.2019
* Updated: jet dashboard init

## [1.2.5](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.5) - 29.11.2019
* Fixed: prevent php error on update DB

## [1.2.4](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.4) - 26.11.2019
* Fixed: Hot Fix & Minor Issues

## [1.2.3](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.3) - 23.11.2019
* Fixed: Hot Fix & Minor Issues

## [1.2.2](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.2) - 22.11.2019
* Added: JetPlugins Dashboard
* Fixed: Minor Issues

## [1.2.1](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.1) - 17.09.2019
* Fixed: Minor Issues

## [1.2.0](https://github.com/ZemezLab/jet-tricks/releases/tag/1.2.0) - 11.07.2019
* Added: Editor Load Level Option
* Fixed: Minor Issues

## [1.1.9](https://github.com/ZemezLab/jet-tricks/releases/tag/1.1.9) - 07.06.2019

* Updated: Extensions assets loading
* Fixed: prevent jerking a sticky column

## [1.1.8](https://github.com/ZemezLab/jet-tricks/releases/tag/1.1.8)

* Added: minor performance improvements and fixes

## [1.1.7](https://github.com/ZemezLab/jet-tricks/releases/tag/1.1.7)

* Added: avaliable extensions option

## [1.1.6](https://github.com/ZemezLab/jet-tricks/releases/tag/1.1.6)

* Added: unfold widget / responsive mask height
* Added: hotspot / tooltip show on init option

## [1.1.5](https://github.com/ZemezLab/jet-tricks/releases/tag/1.1.5)

* Added: RU localization
* Added: compatibility with WPML String Translation plugin

## [1.1.4](https://github.com/ZemezLab/jet-tricks/releases/tag/1.1.4)

* Added: allow to filter JetTricks settings before render.

## [1.1.3](https://github.com/ZemezLab/jet-tricks/releases/tag/1.1.3)

* Added: `None` option in `Tooltip Trigger` to display tooltips always in the Hotspots Widget
* Added: `Text Alignment` control for tooltip in the Hotspots Widget
* Fixed: not working `Back to default` button in the Box-Style Group Control
* Fixed: sticky column on mobile when setting `Sticky On` is `[desktop, tablet]`
* Added: multiple performance improvements and bug fixes

## [1.1.2](https://github.com/ZemezLab/jet-tricks/releases/tag/1.1.2)

* Added: Satellite Widget Extension / icon content type
* Fixed: Tooltip Widget Extension / editor tooltip display bug

## [1.1.0](https://github.com/ZemezLab/jet-tricks/releases/tag/1.1.0)

* Added: New Satellite Widget Extension
* Added: New Tooltip Widget Extension
* Added: New Particles Section Extension

## [1.0.1](https://github.com/ZemezLab/jet-tricks/releases/tag/1.0.1)

* Updated: Elementor 2.1.0 compatibility

## [1.0.0](https://github.com/ZemezLab/jet-tricks/releases/tag/1.0.0)

* Init
