# JetTabs For Elementor

JetTabs is a plugin that allows adding stylish tabs and accordion widgets with vertical and horizontal layouts and building content inside them using Elementor live page builder widgets.

The plugin makes it simple to create a template with Elementor and add it to the Accordion or Tabs widget. It helps organize content and style it up according to one’s needs and preferences.

# ChangeLog

## 2.2.13
* Added: Videos now auto-pause when switching between tabs.
* Fixed: Auto Switch no longer reveals content of tabs hidden by the Hide Empty Items add-on.
* Fixed: Arrow key navigation is now blocked inside inputs/textareas to prevent unintended tab/listing navigation (including Review Listing).

## 2.2.12.1
* Fixed: Potential XSS vulnerability in Tabs/Accordion widgets.
* Fixed: Broken Access Control issue.

## 2.2.12
* Fixed: Scrolling tabs navigation issue — improved scroll behavior and active state handling for tab controls.
* Fixed: Accordion content hidden under the container border — adjusted height calculation so content is fully visible inside “View More” and similar wrappers.
* Fixed: Classic Accordion content disappearing after rapid clicks — added animation/timeout protection to prevent empty panels on fast toggling.
* Fixed: Auto Switch in Tabs widget stopping after manual interaction — autoswitch now resumes correctly, including when using slider-based tab navigation.
* Fixed: JetMenu + JetTabs integration — templates used inside Mega Menu tabs are now rendered properly in the editor and remain accessible for editing.

## 2.2.11
* Fixed: Compatibility with Hide Empty Tabs addon — now works correctly with Elementor Tabs / JetTabs.
* Fixed: Missing styles for nested tabs (tabs inside tabs).
* Fixed: Repeated “Jet Tabs Data Update” banner — update notice now appears only when a real DB migration is required.

## 2.2.10
* Fixed: Escaped dynamic output in templates and widgets for better XSS protection.

## 2.2.9.2
* Fixed: Escaped icon class attribute in widget output for XSS protection.

## 2.2.9.1
* Fixed: Cross-Site Scripting (XSS) vulnerability in the plugin interface, which could potentially allow an attacker to inject malicious scripts.
* Fixed: Sensitive Data Exposure vulnerability.

## 2.2.9
* Fixed: unexpected scroll behavior in Accordion widget when "Collapsible" is enabled;
* Fixed: conflict with "Hide Empty Items" addon – now hidden tabs no longer break Tabs widget rendering;
* Fixed: compatibility with filters when using the "Hide Empty Items" addon;
* Updated: only the first "Active" tab is respected in the Tabs widget when multiple active tabs are enabled.

## 2.2.8
* Added: caching for Elementor templates in Tabs widget;
* Updated: Compatibility with Elementor 3.26.
* Fixed: minor issues.

## 2.2.7
* Added: `Item Custom CSS Class` control in the Accordion widget - https://github.com/Crocoblock/suggestions/issues/7705;
* Fixed: Tabs Alignment in the Tabs widget. - https://github.com/Crocoblock/suggestions/issues/7735.

## 2.2.6.2
* Fixed: [compatibility with JetEngine Query Gateway](https://github.com/Crocoblock/issues-tracker/issues/13589)

## 2.2.6.1
* Fixed: content display for nested Tabs in the Tabs widget;

## 2.2.6
* Added: Self-request functionality for loading template content via AJAX in the Tabs widget;
* Added: `Looped` option for the slider in the Tabs widget;
* Fixed: array key `instance_orientation` in the Image Accordion widget.

## 2.2.5
* Added: Swiper slider functionality for the Tabs widget;
* Added: JSON-LD FAQ schema support for multiple Classic Accordion widgets;
* Updated: compatibility with Elementor 3.24.

## 2.2.4
* Added: additional size units for controls in widgets;
* Added: Title Default option for Image Accordion widget;
* Updated: JetDashboard module to 2.2.0 version;
* Updated: gulp script.

## 2.2.3.1
* Fixed: Sanitize preset name for the Switcher widget.

## [2.2.3](https://github.com/ZemezLab/jet-tabs/releases/tag/2.2.3) - 12.06.2023
* Added: [Crocoblock/suggestions#7343](https://github.com/Crocoblock/suggestions/issues/7343)
* Added: new icons of widgets
* Fixed: condition for `Alignment` tabs item in the Tabs widget
* Fixed: minor issues

## [2.2.2](https://github.com/ZemezLab/jet-tabs/releases/tag/2.2.2)
* Fixed: loading wpml modules
* Fixed: minor issues
* Updated: JetDashboard module

## [2.2.1](https://github.com/ZemezLab/jet-tabs/releases/tag/2.2.1)
* Added: [Crocoblock/suggestions#5854](https://github.com/Crocoblock/suggestions/issues/5854)
* Added: Compatibility Elementor Loop Carousel with Switcher and Accordion widgets
* Added: Compatibility Imagify plugin with Image Accordion widget
* Fixed: `Tabs Alignment` option in the Tabs widget
* Fixed: minor issues

## [2.2.0](https://github.com/ZemezLab/jet-tabs/releases/tag/2.2.0)
* Added: `Scrolling Tabs Navigation` option in the tabs widget
* Fixed: WPML critical error
* Fixed: elements with an ARIA [role] for tabs and accordion widget
* Fixed: switcher in flex container
* Fixed: minor issues
* Updated: Vue js module

## [2.1.25.3](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.25.2)
* Fixed: elementor-template Rest API endpoint

## [2.1.25.2](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.25.2)
* Fixed: minor fixes

## [2.1.25.1](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.25.1)
* Updated: security issue

## [2.1.25](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.25) - 14.09.2023
* Updated: JetDashboard Module

## [2.1.24](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.24) - 10.08.2023
* Fixed: Change template render method for Elementor to avoid errors in the editor

## [2.1.23](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.23) - 03.08.2023
* Fixed: Сompatibility global styles for elementor 3.15.0

## [2.1.22](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.22) - 03.07.2023
* Added: [Crocoblock/suggestions#6387](https://github.com/Crocoblock/suggestions/issues/6387)
* Added: `Scroll animation speed` control in the Accordion widget
* Fixed: minor issues

## [2.1.21](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.21) - 23.02.2023
* Fixed: minor issues

## [2.1.20](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.20) - 21.11.2022
* Updated: JetDashboard Module to v2.0.9
* Fixed: widgets Accessibility
* Fixed: minor issues

## [2.1.19](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.19) - 15.08.2022
* Fixed: Tabs widget content height issue

## [2.1.18](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.18) - 04.08.2022
* Added: [Crocoblock/suggestions#5454](https://github.com/Crocoblock/suggestions/issues/5454)
* Added: [Crocoblock/suggestions#5289](https://github.com/Crocoblock/suggestions/issues/5289)
* Fixed: сompatibility with Elementor 3.7
* Fixed: minor issues

## [2.1.17](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.17) - 14.04.2022
* Added: [Crocoblock/suggestions#3941](https://github.com/Crocoblock/suggestions/issues/3941)
* Fixed: wpml compatibility
* Fixed: minor issues

## [2.1.16](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.16) - 23.03.2022
* Fixed: elementor 3.6 compatibility

## [2.1.15](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.15) - 20.12.2021
* Fixed: rtl compatibility
* Fixed: minor issues

## [2.1.14](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.14) - 02.12.2021
* Added: [Crocoblock/suggestions#2911](https://github.com/Crocoblock/suggestions/issues/2911)
* Added: [Crocoblock/suggestions#3351](https://github.com/Crocoblock/suggestions/issues/3351)
* Fixed: widgets compatibility with new breakpoints
* Fixed: rtl compatibility
* Fixed: minor issues

## [2.1.13](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.13)
* Added: [Crocoblock/suggestions#2584](https://github.com/Crocoblock/suggestions/issues/2584)
* Fixed: prevent php warning

## [2.1.12](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.12) - 04.03.2021
* Fixed: [Crocoblock/suggestions#2751](https://github.com/Crocoblock/suggestions/issues/2751)

## [2.1.11](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.11) - 03.03.2021
* Added: JS triggers on ajax load template
* Fixed: image alt attribute in the Image accordion widget
* Fixed: Elementor 3.1.2 compatibility issue

## [2.1.10](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.10) - 14.11.2020
* Update: JetDashboard Module to v2.0.4

## [2.1.9](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.9) - 11.11.2020
* Update: JetDashboard Module to v2.0.3

## [2.1.8](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.8) - 06.10.2020
* Update: JetDashboard Module to v2.0.0

## [2.1.7](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.7) - 17.07.2020
* Added: ability using dynamic tags in the Image Accordion widget
* Added: [Crocoblock/suggestions#651](https://github.com/Crocoblock/suggestions/issues/651)
* Updated: JetDashboard Module to v1.0.16
* Fixed: prevent Rest API error notice

## [2.1.6](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.6) - 25.05.2020
* Added: filter hooks for compatibility with the JetPlugin Dynamic Data Addon plugin

## [2.1.5](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.5) - 14.05.2020
* Added: multiple improvements
* Fixed: the ability to change the tab by clicking on the anchor link

## [2.1.4](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.4) - 30.03.2020
* Added: `Control CSS ID` control in the Tabs and Accordion widgets
* Added: responsive control to the Orientation control in the Image Accordion widget
* Fixed: minor js issues

## [2.1.3](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.3) - 12.03.2020
* Added: support for Font Awesome 5 and SVG icons
* Fixed: minor js issues

## [2.1.2](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.2) - 21.02.2020
* Updated: Jet Dashboard module to v1.0.10
* Added: compatibility with Custom Link Attributes in Elementor 2.9
* Fixed: compatibility the Switcher widget with the JetPopup
* Fixed: not working section handlers on ajax loading

## [2.1.1](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.1) - 06.12.2019
* Updated: Jet Dashboard to v1.0.7

## [2.1.0](https://github.com/ZemezLab/jet-tabs/releases/tag/2.1.0) - 02.12.2019
* Added: Jet Dashboard

## [2.0.6](https://github.com/ZemezLab/jet-tabs/releases/tag/2.0.6) - 29.11.2019
* Added: better compatibility with JetPopup plugin
* Fixed: prevent php error on update DB

## [2.0.5](https://github.com/ZemezLab/jet-tabs/releases/tag/2.0.5) - 15.10.2019
* Added: compatibility with WPML and Polylang plugins
* Added: multiple performance improvements

## [2.0.4](https://github.com/ZemezLab/jet-tabs/releases/tag/2.0.4) - 02.10.2019
* Added: compatibility for the upcoming release of JetStylesManager plugin
* Added: better accessibility in the Tabs and Accordion widgets
* Fixed: ajax loading in the Tabs widget

## [2.0.3](https://github.com/ZemezLab/jet-tabs/releases/tag/2.0.3) - 22.08.2019
* Fixed: prevent PHP errors

## [2.0.2](https://github.com/ZemezLab/jet-tabs/releases/tag/2.0.2) - 16.07.2019
* Fixed: animation of elements in the templates

## [2.0.1](https://github.com/ZemezLab/jet-tabs/releases/tag/2.0.1) - 15.07.2019
* Fixed: minimize frontend scripts file

## [2.0.0](https://github.com/ZemezLab/jet-tabs/releases/tag/2.0.0) - 11.07.2019
* Added: Ajax template loading for tabs and accoddion widgets
* Added: Editor Load Level Option
* Fixed: minor issues

## [1.1.9](https://github.com/ZemezLab/jet-tabs/releases/tag/1.1.9) - 15.06.2019
* Added: no active tabs option
* Added: tabs bottom position
* Added: draft template is not avaliable for frontend now

## [1.1.8](https://github.com/ZemezLab/jet-tabs/releases/tag/1.1.8) - 30.05.2019
* Added: Screen Reader Compliance ([#288](https://github.com/CrocoBlock/suggestions/issues/288))
* Fixed: minor issue in the Accordion Widget

## [1.1.7](https://github.com/ZemezLab/jet-tabs/releases/tag/1.1.7) - 24.04.2019
* Added: the ability to change the tab by clicking on the anchor link

## [1.1.6](https://github.com/ZemezLab/jet-tabs/releases/tag/1.1.6)
* Added: typography and text color controls for tabs content
* Added: js triggers
* Fixed: missing the close button for embedded editor
* Fixed: RTL compatibility in the Switcher Widget

## [1.1.5](https://github.com/ZemezLab/jet-tabs/releases/tag/1.1.5)
* Added: location hash in the Tabs&Accordion widgets

## [1.1.4](https://github.com/ZemezLab/jet-tabs/releases/tag/1.1.4)
* Added: RU localization
* Added: ability using dynamic tags in widgets
* Added: responsive tab controls position in the Tabs Widget
* Added: multiple performance improvements and bug fixes

## [1.1.3](https://github.com/ZemezLab/jet-tabs/releases/tag/1.1.2)
* Added: multiple performance improvements and bug fixes

## [1.1.1](https://github.com/ZemezLab/jet-tabs/releases/tag/1.1.1)
* Added: Elementor 2.1 compatibility

## [1.1.0](https://github.com/ZemezLab/jet-tabs/releases/tag/1.1.0)
* Added: Switcher Widget(NEW)
* Added: JetTabs widget / auto tab switching option

## [1.0.5](https://github.com/ZemezLab/jet-tabs/releases/tag/1.0.5)
* Added: toggle icon position control in Accordion widget
* Updated: edit mode ui styles

## [1.0.4](https://github.com/ZemezLab/jet-tabs/releases/tag/1.0.4)
* Added: multiple performance improvements and bug fixes
* Added: toggle icon position control in Accordion widget
* Updated: animation for better compatibility from third-party js scripts

## [1.0.3](https://github.com/ZemezLab/jet-tabs/releases/tag/1.0.3)
* Added: multiple performance improvements and bug fixes

## [1.0.2](https://github.com/ZemezLab/jet-tabs/releases/tag/1.0.2)
* Fixed: Template edit mode

## [1.0.1](https://github.com/ZemezLab/jet-tabs/releases/tag/1.0.1)

* Fixed: image accordion item url

## [1.0.0](https://github.com/ZemezLab/jet-tabs/releases/tag/1.0.0)

* Init
