'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

(function ($, wp) {
    var CoreInstall = function () {
        function CoreInstall() {
            _classCallCheck(this, CoreInstall);

            this.setElements();
            this.events();
        }

        _createClass(CoreInstall, [{
            key: 'setElements',
            value: function setElements() {
                this.el = {
                    $pluginInactivatedList: $('.jupiterx-core-install-rplugins-failed'),
                    $error: $('.jupiterx-core-install-rplugins-error'),
                    $activeBtn: $('.jupiterx-core-install-button')
                };
            }
        }, {
            key: 'events',
            value: function events() {
                var _this = this;

                $(document).on('click', '.jupiterx-core-install-button', function (event) {
                    event.preventDefault();
                    _this.fetchInactiveRequiredPlugins();
                });
            }
        }, {
            key: 'installPlugins',
            value: function installPlugins(bulkActions) {
                var _this2 = this;

                this.el.$activeBtn.addClass('installing').attr('disabled', 'disabled').find('span:last-child').text(jupiterxCoreInstall.i18n.installText + '...');

                $.ajax({
                    url: bulkActions.install_required_plugins.url,
                    method: 'POST',
                    data: bulkActions.install_required_plugins,
                    success: function success() {
                        _this2.el.$activeBtn.removeClass('installing').removeAttr('disabled').find('span:last-child').text(jupiterxCoreInstall.i18n.defaultText);

                        _this2.activatePlugins(bulkActions);
                    },
                    error: function error() {
                        _this2.el.$activeBtn.removeClass('installing').removeAttr('disabled').find('span:last-child').text(jupiterxCoreInstall.i18n.defaultText);

                        _this2.el.$pluginInactivatedList.find('.core-install-plugin-error div').append(jupiterxCoreInstall.i18n.failedInstallText).append(jupiterxCoreInstall.i18n.failedActionLinks);

                        _this2.el.$pluginInactivatedList.find('.core-install-plugin-error').show();

                        _this2.el.$pluginInactivatedList.show();
                    }
                });
            }
        }, {
            key: 'activatePlugins',
            value: function activatePlugins(bulkActions) {
                var _this3 = this;

                this.el.$activeBtn.addClass('activating').attr('disabled', 'disabled').find('span:last-child').text(jupiterxCoreInstall.i18n.activateText + '...');

                $.ajax({
                    url: bulkActions.activate_required_plugins.url,
                    method: 'POST',
                    data: bulkActions.activate_required_plugins,
                    success: function success() {
                        _this3.el.$activeBtn.removeClass('activating').find('span:last-child').text(jupiterxCoreInstall.i18n.redirecting + '...');

                        window.location.href = jupiterxCoreInstall.controlPanelUrl;
                    },
                    error: function error() {
                        _this3.el.$activeBtn.removeClass('activating').removeAttr('disabled').find('span:last-child').text(jupiterxCoreInstall.i18n.defaultText);

                        _this3.el.$pluginInactivatedList.find('.core-install-plugin-error div').append(jupiterxCoreInstall.i18n.failedActivateText).append(jupiterxCoreInstall.i18n.failedActionLinks);

                        _this3.el.$pluginInactivatedList.find('.core-install-plugin-error').show();

                        _this3.el.$pluginInactivatedList.show();
                    }
                });
            }
        }, {
            key: 'fetchInactiveRequiredPlugins',
            value: function fetchInactiveRequiredPlugins() {
                var _this4 = this;

                this.el.$activeBtn.addClass('activating').attr('disabled', 'disabled').find('span:last-child').text(jupiterxCoreInstall.i18n.fetchText + '...');

                wp.ajax.post('jupiterx_get_plugins', {
                    '_ajax_nonce': this.el.$activeBtn.data('nonce')
                }).done(function (data) {
                    _this4.installPlugins(data.bulk_actions);

                    if (!data || !data.bulk_actions || data.bulk_actions.length === 0) {
                        _this4.toggleError(true);

                        return;
                    }

                    if (!data || !data.plugins || data.plugins.length === 0) {
                        _this4.toggleError(true);

                        return;
                    }

                    _this4.toggleError(false);
                }).fail(function () {
                    _this4.toggleError(true);
                });
            }
        }, {
            key: 'toggleError',
            value: function toggleError(show) {
                if (show) {
                    this.el.$error.css('display', 'flex');

                    return;
                }

                this.el.$error.css('display', 'none');
            }
        }]);

        return CoreInstall;
    }();

    new CoreInstall();
})(jQuery, wp);