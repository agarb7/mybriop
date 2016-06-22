(function ($) {

    $.fn.prepodavatelPeresechenieModal = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.prepodavatelPeresechenieModal');
            return false;
        }
    };

    var methods = {
        init: function (options) {
            return this.data('url', options.url);
        },

        show: function (options) {
            var $modal = this;
            var url = makeUrl($modal.data('url'), options);

            $modal.find('.modal-body').load(url, function () {
                $modal.modal('show');
            });

            return $modal;
        },

        destroy: function () {
            return this.removeData('url');
        }
    };

    function makeUrl(url, params) {
        var glue = url.indexOf('?') === -1 ? '?' : '&';
        return url + glue + $.param(params);
    }

})(jQuery);
