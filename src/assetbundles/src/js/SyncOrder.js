(function($) {

if (typeof Craft.Translations === 'undefined') {
    Craft.Translations = {};
}

/**
 * Order index class
 */
Craft.Translations.SyncOrder = Garnish.Base.extend(
{
    $trigger: null,
    $form: null,

    init: function(formId) {
        this.$form = $('#' + formId);
        this.$trigger = $('input.submit', this.$form);
        this.$status = $('.sync-status', this.$form);

        this.addListener(this.$form, 'submit', 'onSubmit');
    },

    onSubmit: function(ev) {
        ev.preventDefault();

        if (!this.$trigger.hasClass('disabled')) {
            if (!this.progressBar) {
                this.progressBar = new Craft.ProgressBar(this.$status);
            }
            else {
                this.progressBar.resetProgressBar();
            }

            this.progressBar.$progressBar.removeClass('hidden');

            this.progressBar.$progressBar.velocity('stop').velocity(
                {
                    opacity: 1
                },
                {
                    complete: $.proxy(function() {
                        var postData = Garnish.getPostData(this.$form),
                            params = Craft.expandPostArray(postData);

                        var data = {
                            params: params
                        };
                        Craft.postActionRequest(params.action, data, $.proxy(function(response, textStatus) {
                                console.log('textStatus: ', textStatus);
                                if(textStatus === 'success')
                                {
                                    if (response && response.error) {
                                        alert(response.error);
                                    }

                                    this.updateProgressBar();
                                    if (response && response.orderId) {
                                        var $iframe = $('<iframe/>', {'src': Craft.getActionUrl('translations/base/sync-order', {'orderId': response.orderId})}).hide();
                                        this.$form.append($iframe);
                                    }

                                    setTimeout($.proxy(this, 'onComplete'), 300);
                                }
                                else
                                {
                                    Craft.cp.displayError(Craft.t('app', 'There was a problem syncing your order.'));

                                    this.onComplete(false);
                                }

                            }, this),
                            {
                                complete: $.noop
                            });

                    }, this)
                });

            if (this.$allDone) {
                this.$allDone.css('opacity', 0);
            }

            this.$trigger.addClass('disabled');
            this.$trigger.trigger('blur');
        }
    },

    updateProgressBar: function() {
        var width = 100;
        this.progressBar.setProgressPercentage(width);
    },

    onComplete: function(showAllDone) {
        this.progressBar.$progressBar.velocity({opacity: 0}, {
            duration: 'fast', complete: $.proxy(function() {

                this.$trigger.removeClass('disabled');
                this.$trigger.trigger('focus');
            },
            this)
        });

        window.location.reload();
    }
});

})(jQuery);