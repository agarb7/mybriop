(function ($) {

    $.fn.appComboWidget = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.appComboWidget');
            return false;
        }
    };

    var methods = {
        init: function (options) {
            var hiddeninput;
            var switch_;
            var select2;
            var textinput;

            var stateListSwitchText = options.switchTexts[STATE_LIST];
            var stateTextSwitchText = options.switchTexts[STATE_TEXT];

            function setValue(state)
            {
                var value;

                if (state === STATE_LIST)
                    value = select2.val();
                else if (state === STATE_TEXT)
                    value = textinput.val();

                hiddeninput.val(JSON.stringify([state, value]));
            }

            function getState()
            {
                return $.parseJSON(hiddeninput.attr('value'))[0];
            }

            return this.each( function () {
                var id = $(this).attr('id');
                hiddeninput = $('#'+id);
                switch_ = $('.combowidget-switch[data-target="'+id+'"]');
                select2 = $('.combowidget-select2[data-target="'+id+'"]');
                textinput = $('.combowidget-textinput[data-target="'+id+'"]');

                switch_.on('click.appComboWidget', function () {
                    var prevState = getState();

                    if (prevState === STATE_LIST) {
                        setValue(STATE_TEXT);
                        select2.parent().hide();
                        textinput.show();
                        switch_.text(stateTextSwitchText);
                    } else if (prevState === STATE_TEXT) {
                        setValue(STATE_LIST);
                        textinput.hide();
                        select2.parent().show();
                        switch_.text(stateListSwitchText);
                    }

                    return false;
                });

                textinput.on('change.appComboWidget', function () {
                    if (getState() === STATE_TEXT)
                        setValue(STATE_TEXT);
                });

                select2.on('change.appComboWidget', function () {
                    if (getState() === STATE_LIST)
                        setValue(STATE_LIST);
                });
            });
        },

        destroy: function () {
            return this.each( function () {
                $(this).off('.appComboWidget');
            });
        }
    };

    //if change change also in PHP class ComboWidget
    var STATE_LIST = 1;
    var STATE_TEXT = 2;

})(jQuery);
