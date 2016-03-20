(function ($) {

    $.fn.kategoriiSlushatelejInputFields = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.kategoriiSlushatelejInputFields');
            return false;
        }
    };

    var defaults = {
        field: undefined,
        firstAddTogglerText: undefined,
        addTogglerText: undefined,
        values: undefined
    };

    var methods = {
        init: function (options) {
            options = $.extend({}, defaults, options || {});

            return this.each( function () {
                var $widget = $(this);
                var $toggler = $widget.find('.add-toggler');

                var updateTogglerText = function () {
                    var text = $widget.find('.form-group').length === 0
                        ? options.firstAddTogglerText
                        : options.addTogglerText;

                    $toggler.text(text);
                };

                var insertFormGroup = function (value) {
                    var $formGroup = $($.parseHTML($.trim(options.field)));

                    if (value !== undefined)
                        $formGroup.find('input').val(value);

                    $formGroup.find('.input-field-clear').on('click.kategoriiSlushatelejInputFields', function (e) {
                        $formGroup.remove();
                        updateTogglerText();
                        e.preventDefault();
                    });

                    $toggler.before($formGroup);
                    updateTogglerText();
                };

                $toggler.on('click.kategoriiSlushatelejInputFields', function (e) {
                    insertFormGroup();
                    e.preventDefault();
                });

                if (options.values)
                    $.each(options.values, function (i, value) {insertFormGroup(value);});
                else
                    updateTogglerText();
            });
        },

        destroy: function () {
            return $(this).off('.kategoriiSlushatelejInputFields');
        }
    };

})(jQuery);

