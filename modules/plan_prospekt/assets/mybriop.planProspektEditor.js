if(!window.mybriop)
    window.mybriop = {};

mybriop.planProspektEditor = (function ($) {
    return {
        gridActionButtonsInit: function (buttons, container) {
            $(buttons).click(function (event) {
                var url = $(event.target).prop('href');
                $.pjax({url:url, container: container, scrollTo: false});
                event.preventDefault();
            });
        },

        modalHiddenHandlerInit: function (modal, container, url) {
            $(modal).on('hidden.bs.modal', function () {
                $.pjax({
                    url: url,
                    container: container,
                    scrollTo: false
                });
            });
        }
    };
})(jQuery);
