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

    function Picker(jqo, options) {
        var self = this;

        self.jqo = jqo;
        self.temaIndexUrl = options.temaIndexUrl;
        self.temaFilterOptionsUrl = options.temaFilterOptionsUrl;
        self.filterAttributes = options.filterAttributes;
        self.picking = null;

        self.jqo.on('click.temaPicker', '.tema-picker-item', function (e) {
            self.endPick(this);
            e.preventDefault();
        });

        self.jqo.on('change.temaPicker', '.tema-picker-filter select', function (e) {
            self.load();
        });
    }

    Picker.prototype.endPick = function (tema) {
        var self = this;
        var temaData = $(tema).data();

        self.jqo.modal('hide');

        if (self.picking) {
            self.picking.resolve({
                id: temaData.id,
                chast: temaData.chast
            });
        }
    };

    Picker.prototype.beginPick = function () {
        var self = this;

        if (self.picking)
            self.picking.reject();

        self.picking = $.Deferred();

        self.load(function () {
            self.jqo.modal('show');
        });

        return self.picking.promise();
    };

    Picker.prototype.load = function (handler) {
        var self = this;

        //todo load indicator

        var url = makeUrl(self.temaIndexUrl, self.getFilter());

        self.jqo
            .find('.tema-picker-content')
            .load(url, function (content) {
                self.updateDecoration(content);

                self.updateFilter();

                if (handler)
                    handler();
            });
    };

    Picker.prototype.updateDecoration = function (content) {
        var self = this;
        var hasTemy = !!content;

        self.jqo.find('.no-temy-message, .ok-btn').toggle(!hasTemy);
        self.jqo.find('.kurs-title, .cancel-btn').toggle(hasTemy);
    };

    Picker.prototype.updateFilter = function () {
        var self = this;

        $.each(self.filterAttributes, function (i, attribute) {
            self.loadSelectOptions(attribute);
        });
    };
    
    Picker.prototype.findSelect = function (attribute) {
        var self = this;

        return self.jqo.find('#temafilter-' + attribute);
    };

    Picker.prototype.loadSelectOptions = function(attribute) {
        var self = this;

        var $select = self.findSelect(attribute); 

        var optionAdder = function (oldValue) {
            return function (i, pair) {
                var value = pair[0];
                var text = pair[1];

                var elem = '<option value="' + value + '"';

                if (value == oldValue)
                    elem += ' selected';

                elem += '>' + text + '</option>';

                $select.append(elem);
            }
        };

        var url = makeUrl(self.temaFilterOptionsUrl, {attribute: attribute});

        $select.prop("disabled", true);

        $.getJSON(url, function (data) {
            var oldVal = $select.val();
            $select.empty();
            $.each(data, optionAdder(oldVal));
            $select.prop("disabled", false);
        });
    };
    
    Picker.prototype.getFilter = function () {
        var self = this;
        
        var filter = {};
        
        $.each(self.filterAttributes, function (i, attribute) {
            filter[attribute] = self.findSelect(attribute).val();
        });
        
        return {'TemaFilter': filter};
    };

    Picker.prototype.filter = function () {
        var self = this;

        self.loadHelper();
    };

    Picker.prototype.destroy = function () {
        this.off('.temaPicker');
    };

    var methods = {
        init: function (options) {
            var pimpl = new Picker(this, options);

            return this.data('pimpl', pimpl);
        },

        pick: function () {
            return this.data('pimpl').beginPick();
        },

        destroy: function () {
            this.data('pimpl').destroy();

            return this.removeData('pimpl');
        }
    };

    function makeUrl(url, params) {
        var glue = url.indexOf('?') === -1 ? '?' : '&';
        return url + glue + $.param(params);
    }

})(jQuery);
