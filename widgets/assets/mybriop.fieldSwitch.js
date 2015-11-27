(function ($) {

    $.fn.mybriopFieldSwitch = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.mybriopFieldSwitch');
            return false;
        }
    };

    var methods = {
        init: function (options) {
            var fromCont = $(options.from);
            var fromInput = findInput(fromCont);

            var toCont = $(options.to);
            var toInput = findInput(toCont);

            return findLink(this).on('click.mybriopFieldSwitch', function () {
                fromCont.hide();
                fromInput.prop('disabled', true);
                toInput.prop('disabled', false);
                toCont.show();
                return false;
            });
        },

        destroy: function () {
            return findLink(this).off('.mybriopFieldSwitch');
        }
    };

    function findLink(cont)
    {
        return cont.children('a');
    }

    function findInput(cont)
    {
        return cont.find('.form-control');
    }

})(jQuery);