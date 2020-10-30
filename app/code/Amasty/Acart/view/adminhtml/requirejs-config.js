var config = {
    map: {
        '*': {
            amasty_acart_schedule: 'Amasty_Acart/js/schedule',
            amasty_acart_test: 'Amasty_Acart/js/test',
            amasty_acart_reports: 'Amasty_Acart/js/reports'
        }
    },

    shim: {
        'Amasty_Acart/vendor/amcharts/charts': {
            deps: ['Amasty_Acart/vendor/amcharts/core']
        },

        'Amasty_Acart/vendor/amcharts/animated': {
            deps: ['Amasty_Acart/vendor/amcharts/core']
        }
    }
};