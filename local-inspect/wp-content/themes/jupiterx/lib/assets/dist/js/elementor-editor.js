'use strict';

(function ($) {

  if (typeof elementor === 'undefined' || typeof elementorCommonConfig.finder === 'undefined') {
    return;
  }

  /**
   * Add menu items.
   */
  function addMenuItems() {
    var items = [{
      name: 'jupiterx-control-panel',
      icon: '',
      title: 'Layout Builder',
      type: 'link',
      link: elementorCommonConfig.finder.data.site.items['wordpress-dashboard'].url + 'admin.php?page=jupiterx#/layout-builder',
      newTab: true
    }];

    items.forEach(function (item) {
      elementor.modules.layouts.panel.pages.menu.Menu.addItem(item, 'more', 'exit-to-dashboard');
    });
  }

  elementor.on('panel:init', addMenuItems);
})(jQuery);