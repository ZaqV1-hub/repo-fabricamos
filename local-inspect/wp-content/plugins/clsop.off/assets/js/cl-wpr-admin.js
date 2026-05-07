window.clAccelerateWP = {
    notice: {
        close: function (button) {
            var $parent = jQuery(button).closest('div.notice')
            if ($parent.length) {
                $parent.remove();
            }
        },
        init: function () {
            jQuery('[data-cl-accelerate-wp-notice-upgrade="link"]').on('click', function (e) {
                e.preventDefault()
                clAccelerateWP.notice.close(this)
                clAccelerateWP.subscription.gateway(jQuery(this).attr('href'))
            })
            jQuery('[data-accelerate-wp-modal-close]').on('click', function (e) {
                e.preventDefault()
                clAccelerateWP.modal.close(this, e)
            })
        }
    },
    modal: {
        show: function (type) {
            jQuery('[data-accelerate-wp-modal="' + type + '"]').addClass('cl-accelerate-wp__modal_show')
        },
        hide: function (type) {
            var modal = jQuery('[data-accelerate-wp-modal="' + type + '"]')
            modal.removeClass('cl-accelerate-wp__modal_show')
        },
        close: function (element, event) {
            var type = jQuery(element).data('accelerate-wp-modal-close')
            if (jQuery(event.target).is(element) || jQuery(element).data('accelerate-wp-modal-close') === type) {
                this.hide(type)
            }
        },
        subscription_success: {
            type: 'subscription-success',
            modal: jQuery('[data-accelerate-wp-modal="subscription-success"]'),
            show: function () {
                clAccelerateWP.modal.show(this.type)
            },
            hide: function () {
                clAccelerateWP.modal.hide(this.type)
            },
        },
    },
    subscription: {
        timer: false,
        window_object: false,
        window_opened: false,

        gateway: function (url) {
            if (this.window_opened) {
                return;
            }

            this.window_object = window.open(url, '', 'toolbar=0,status=0,width=1100,height=640');
            this.window_opened = true

            this.timer = setInterval(function () {
                if (clAccelerateWP.subscription.window_object.closed) {
                    clAccelerateWP.subscription.window_opened = false;
                    clearInterval(clAccelerateWP.subscription.timer);
                }
            }, 500);
        },

        listener: function (e) {
            if (e.data === 'PAYMENT_SUCCESS') {

                if (this.window_opened) {
                    this.window_object.close()
                }

                clAccelerateWP.modal.subscription_success.show()
                jQuery.post(ajaxurl, {
                    action: 'cl_cdn_dismiss_limit_notice',
                    _ajax_nonce: cl_rocket_ajax_data.nonce
                })
            }
        }
    },

    init: function () {
        this.notice.init()

        window.addEventListener('message', function (e) {
            clAccelerateWP.subscription.listener(e)
        }, false);
    }
}

jQuery(document).ready(function () {
    clAccelerateWP.init()
})
