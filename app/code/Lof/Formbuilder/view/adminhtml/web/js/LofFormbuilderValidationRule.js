define([
    'jquery',
    'jquery/ui',
    'jquery/validate',
    'mage/translate'
], function ($) {
    'use strict';
    return function (dataUrl) {
        $.validator.addMethod(
            "ip",
            function (value, element) {
                var ip = $('#blacklist_ip').val();
                var email = $('#blacklist_email').val();
                if (ip == '' && email == '') {
                    return false;
                } else {
                    return true;
                }
            },
            $.mage.__("Missing email or ip. You should input one of them"),
        );
        $.validator.addMethod(
            "email",
            function (value, element) {
                var ip = $('#blacklist_ip').val();
                var email = $('#blacklist_email').val();
                if (ip == '' && email == '') {
                    return false;
                } else {
                    return true;
                }
            },
            $.mage.__("Missing email or ip. You should input one of them"),
        );
    }
});