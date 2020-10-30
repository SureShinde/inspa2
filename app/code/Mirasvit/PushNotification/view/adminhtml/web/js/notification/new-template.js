define([
    'Magento_Ui/js/form/element/ui-select'
], function (Select) {
    'use strict';

    return Select.extend({
        setParsed: function (data) {
            var option = this.parseData(data);

            if (data.error) {
                return this;
            }

            var options = this.options();
            options.push(option);
            this.options(options);
            this.setOption(option, options);
            this.set('newOption', option);
        },

        parseData: function (data) {
            return {
                value:     data.template['template_id'],
                label:     data.template['name'],
                level:     1,
                parent:    "",
                is_active: 1
            };
        }
    });
});
