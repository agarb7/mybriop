if(!window.mybriop)
    window.mybriop = {};

mybriop.validation = (function ($) {
    function squeezeLine (str) {
        return jQuery.trim(str).replace(/\s+/g,' ');
    }

    return {
        squeezeLine: function ($form, attribute) {
            var $input = $form.find(attribute.input);
            var value = squeezeLine($input.val());

            $input.val(value);
            return value;
        },

        toLower: function ($form, attribute) {
            var $input = $form.find(attribute.input);
            var value = $input.val().toLowerCase();

            $input.val(value);
            return value;
        }
    };
})(jQuery);
