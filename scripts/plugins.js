(function(exports) {
    'use strict';
    var names = ["Sunday", "Monday", "Tuesday", "Wednesday",
        "Thursday", "Friday", "Saturday"];

    exports.name = function(number) {
        return names[number];
    };
    exports.number = function(name) {
        return names.indexOf(name);
    };

    jQuery(document).ready(function () {
        var bulkPluginSelector = jQuery('#farmer__bulkPluginSelect');
        bulkPluginSelector.chosen({
            width: '100%',
            search_contains: true
        });
        bulkPluginSelector.change(function () {
            jQuery(".bulkButton").prop('disabled',false);
        });

        var animalSelector = jQuery('#farmer__animalSelect');
        animalSelector.chosen({
            width: '100%',
            search_contains: true
        });
        animalSelector.change(function () {
            console.log('change event received');
            var animal = animalSelector.val();
            jQuery.post(
                DOKU_BASE + 'lib/exe/ajax.php',
                {
                    call: 'plugin_farmer_' + animal
                },
                function(data) {
                    jQuery.each(data[0], function(index, value) {
                        var checked = 'checked';
                        var pluginCheckbox;
                        if (typeof data[1][value] !== 'undefined' && data[1][value] === 0) {
                            checked = '';
                        }
                        console.log(checked);
                        pluginCheckbox = jQuery('<input type="checkbox" id="farmer__plugin_' + value + '" name="' + value + '" ' + checked + '>');
                        jQuery('#farmer__animalPlugins').append(pluginCheckbox);
                        jQuery('#farmer__plugin_' + value).wrap('<label class="block"></label>').parent().prepend(value);
                    });

                    // data is array you returned with action.php
                },
                'json'
            );
        });

        jQuery('select').on('chosen:showing_dropdown', function(evt, params) {
            jQuery(evt.target).parent('fieldset').animate({
                "padding-bottom": '20em'
            }, 400);
        });
        jQuery('select').on('chosen:hiding_dropdown', function(evt, params) {
            jQuery(evt.target).parent('fieldset').animate({
                "padding-bottom": '7px'
            }, 400);
        });

        jQuery("input[name=bulkSingleSwitch]:radio").change(function () {
            if (jQuery('#farmer__bulk').prop("checked")) {
                jQuery('#farmer__bulkForm').css('display','initial');
            } else {
                jQuery('#farmer__bulkForm').css('display','none');
            }
            if (jQuery('#farmer__single').prop("checked")) {
                jQuery('#farmer__singlePluginForm').css('display','initial');
            } else {
                jQuery('#farmer__singlePluginForm').css('display','none');
            }
        });
    });

})(this.farmer__plugins = {});
