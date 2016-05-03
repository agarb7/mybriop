(function ($) {

    $.fn.temaPicker = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.temaPicker');
            return false;
        }
    };

    var methods = {
        init: function (options) {
            var $picker = this;
            $picker.data('temaPicker', {
                temaIndexUrl: options.temaIndexUrl,
                picking: null
            });

            var pick = function (e) {
                var $tema = $(this);
                var picking = $picker.data('temaPicker').picking;

                $picker.modal('hide');

                if (picking) {
                    var data = {
                        id: $tema.data('id'),
                        chast: $tema.data('chast')
                    };

                    picking.resolve(data);
                }

                e.preventDefault();
            };

            return $picker.on('click.temaPicker', '.tema-picker-item', pick);
        },

        pick: function () {
            var $picker = this.first();
            var data = $picker.data('temaPicker');

            var loadHandler = function (text) {
                var hasTemy = !!text;

                $picker.find('.no-temy-message').toggle(!hasTemy);
                $picker.find('.ok-btn').toggle(!hasTemy);

                $picker.find('.kurs-title').toggle(hasTemy);
                $picker.find('.cancel-btn').toggle(hasTemy);

                $picker.modal('show');
            };

            if (data.picking)
                data.picking.reject();

            data.picking = $.Deferred();

            //todo show loading indicator
            $picker
                .find('.tema-picker-content')
                .load(data.temaIndexUrl, loadHandler);

            return data.picking.promise();
        },

        destroy: function () {
            return this
                .off('.temaPicker')
                .removeData('temaPicker');
        }
    };    

})(jQuery);
