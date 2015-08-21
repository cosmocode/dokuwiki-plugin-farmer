(function(exports) {
    'use strict';

    jQuery(document).ready(function () {
        var bulkPluginSelector = jQuery('#farmer__bulkPluginSelect');
        if (jQuery().chosen) {
            bulkPluginSelector.chosen({
                width:           '100%',
                search_contains: true,
                "placeholder_text_single": LANG.plugins.farmer.pluginSelect
            });
        }

        bulkPluginSelector.change(function () {
            jQuery(".bulkButton").prop('disabled',false);
        });

        var animalSelector = jQuery('#farmer__animalSelect');
        if (jQuery().chosen) {
            animalSelector.chosen({
                width:           '100%',
                search_contains: true,
                "placeholder_text_single": LANG.plugins.farmer.animalSelect
            });
        }
        animalSelector.change(function () {
            console.log('change event received');
            var animal = animalSelector.val();
            jQuery.post(
                DOKU_BASE + 'lib/exe/ajax.php',
                {
                    call: 'plugin_farmer_' + animal
                },
                function(data) {
                    jQuery('#farmer__animalPlugins').html('');
                    jQuery.each(data[0], function(index, value) {
                        var checked = 'checked';
                        var pluginCheckbox;
                        if (typeof data[1][value] !== 'undefined' && data[1][value] === 0) {
                            checked = '';
                        }
                        console.log(checked);
                        pluginCheckbox = jQuery('<input type="checkbox" id="farmer__plugin_' + value + '" name="plugin_farmer_plugins[' + value + ']" ' + checked + '>');
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

})(this.farmer__plugin = {});
