if(!window.mybriop)
    window.mybriop = {};

mybriop.planProspektEditor = (function ($) {
    return {
        gridActionButtonsInit: function (buttons, container) {
            $(buttons).off('.planProspekt').on('click.planProspekt', function (event) {
                var url = $(event.target).prop('href');
                $.pjax({
                    url:url,
                    container: container,
                    scrollTo: false,
                    timeout: 3500
                });
                event.preventDefault();
            });
        },

        gridNazvanieColumnInit: function (column) {
            $(column).each(function () {
                var $column = $(this);
                var $showSwitch = $column.find('.annotaciya-show');
                var $hideSwitch = $column.find('.annotaciya-hide');
                var $annotaciya = $column.find('.annotaciya');

                $showSwitch.off('.planProspekt').on('click.planProspekt', function (event) {
                    $annotaciya.show();
                    $showSwitch.hide();
                    event.preventDefault();
                });

                $hideSwitch.off('.planProspekt').on('click.planProspekt', function (event) {
                    $annotaciya.hide();
                    $showSwitch.show();
                    event.preventDefault();
                });
            });
        },

        gridSearchInit: function (container) {
            var $container = $(container);
            var $switch = $container.find('.grid-search-switch');
            var $form = $container.find('.grid-search');

            $switch.off('.planProspekt').on('click.planProspekt', function (event) {
                $form.toggle();
                event.preventDefault();
            });
        },

        modalHiddenHandlerInit: function (modal, container, url) {
            $(modal).off('hidden.bs.modal.planProspekt').on('hidden.bs.modal.planProspekt', function () {
                $.pjax({
                    url: url,
                    container: container,
                    scrollTo: false,
                    timeout: 3500
                });
            });
        },

        modalDynamicOptionsInit: function (modal) {
            $(modal).off('show.bs.modal.planProspekt').on('show.bs.modal.planProspekt', function () {
                var $this = $(this);
                var modalData = $this.find('[data-modal]').data('modal');
                var $modalDialog = $this.find('.modal-dialog');

                $modalDialog.removeClass('modal-lg modal-sm');
                $modalDialog.addClass(modalData.size);

                $this.find('.modal-header h4').text(modalData.title);
            });
        },

        pjaxLoadingIndicatorInit: function (document) {
            var $loading = $('#pjax-loading');
            var $document = $(document);
            $document.on('pjax:send', function() {
                $loading.show()
            });
            $document.on('pjax:complete', function() {
                $loading.hide()
            });
        }
    };
})(jQuery);
