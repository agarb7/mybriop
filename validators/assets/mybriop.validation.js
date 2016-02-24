if(!window.mybriop)
    window.mybriop = {};

mybriop.validation = (function ($) {
    function squeezeLine (str) {
        return jQuery.trim(str).replace(/\s+/g,' ');
    }

    function squeezeText (str) {
        return jQuery.trim(str).replace(/\s*\n\s*/g, '\n').replace(/[^\n\S]+/g, ' ');
    }

    function filter($form, attribute, func) {
        var $input = $form.find(attribute.input);
        var value = func($input.val());

        $input.val(value);
        return value;
    }

    return {
        squeezeLine: function ($form, attribute) {
            return filter($form, attribute, squeezeLine);
        },

        squeezeText: function ($form, attribute) {
            return filter($form, attribute, squeezeText);
        },

        toLower: function ($form, attribute) {
            return filter($form, attribute, function (str) {
                return str.toLowerCase()
            });
        }
    };
})(jQuery);
