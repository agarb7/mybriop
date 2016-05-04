(function ($) {

    $.fn.zanyatieGrid = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.zanyatieGrid');
            return false;
        }
    };

    var methods = {
        init: function (options) {
            var $grid = this;
            var $temaPicker = $(options.temaPicker);
            var zanyatieUpdateUrl = options.zanyatieUpdateUrl;
            var zanyatieDeleteAction = options.zanyatieDeleteAction;

            var deleteZanyatie = function ($tr) {
                var url = makeUrl(zanyatieDeleteAction, getDataNomer($tr));
                
                $.post(url).done(function () {
                    showBlankCell($tr, true);
                    clearValues($tr);
                });
            };

            var updateZanyatie = function ($tr, data) {
                var url = makeUrl(zanyatieUpdateUrl, getDataNomer($tr));

                $.post(url, data).done(function (data) {
                    showBlankCell($tr, false);
                    updateValues($tr, data)
                });
            };

            var pickTema = function (e) {
                var $tr = getRow(e.target);

                $temaPicker.temaPicker('pick').done(function (tema) {
                    var data = {
                        'tema': tema.id,
                        'chast_temy': tema.chast
                    };
                    updateZanyatie($tr, data);
                });

                e.preventDefault();
            };

            var deleteBtnHandler = function (e) {
                var $tr = getRow(e.target);
                
                deleteZanyatie($tr);

                e.preventDefault();
            };

            var valueChangeHandler = function (e) {
                var $input = $(this);
                var attribute = $input.data('attribute');
                var data = {};
                data[attribute] = $input.val();

                updateZanyatie(getRow($input), data);
            };

            return $grid
                .on('click.zanyatieGrid', '.tema-picking-cell', pickTema)
                .on('click.zanyatieGrid', '.zanyatie-delete-btn', deleteBtnHandler)
                .on('change.zanyatieGrid', '[data-attribute]', valueChangeHandler);
        },

        destroy: function () {
            return this.off('.zanyatieGrid');
        }
    };

    function getRow(elem) {
        return $(elem).closest('tr');
    }

    function getDataNomer($tr) {
        var data = $tr.data();

        return {
            'data': data.data,
            'nomer': data.nomer
        };
    }

    function showBlankCell($tr, show) {
        $tr.find('.blank-cell').toggle(show);
        $tr.find('.data-cell').toggle(!show);
    }
    
    function clearValues($tr) {
        setValue($tr.find('[data-attribute]'), '');
    }

    function updateValues($tr, data) {
        $.each(data, function (prop, val) {
            var $elem = $tr.find('[data-attribute="' + prop + '"]');
            setValue($elem, val);
        });
    }

    function setValue($elem, value) {
        if ($elem.is('input, select'))
            return $elem.val(value);

        return $elem.text(value);
    }
    
    function makeUrl(url, params) {
        var glue = url.indexOf('?') === -1 ? '?' : '&';
        return url + glue + $.param(params);
    }

})(jQuery);
