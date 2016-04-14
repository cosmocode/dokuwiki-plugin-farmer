(function(exports) {
    'use strict';

    jQuery(document).ready(function () {
        // Plugin Management for all Animals
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

        // Plugin Management of a single Animal
        var animalSelector = jQuery('#farmer__animalSelect');
        if (jQuery().chosen) {
            animalSelector.chosen({
                width:           '100%',
                search_contains: true,
                "placeholder_text_single": LANG.plugins.farmer.animalSelect
            });
        }
        animalSelector.change(function () {
            var animal = animalSelector.val();
            jQuery.post(
                DOKU_BASE + 'lib/exe/ajax.php',
                {
                    call: 'plugin_farmer_getPlugins_' + animal
                },
                function(data) {
                    var submitButton = jQuery('<button type="submit" value="updateSingleAnimal" name="plugin_farmer[submit_type]">'+LANG.plugins.farmer.submit+'</button>');
                    var resetButton = jQuery('<button type="reset">'+LANG.plugins.farmer.reset+'</button>');
                    var pluginContainer = jQuery('#farmer__animalPlugins');
                    pluginContainer.html('');
                    pluginContainer.append(submitButton, resetButton);
                    jQuery.each(data[0], function(index, value) {
                        var checked = 'checked';
                        if (typeof data[1][value] !== 'undefined' && data[1][value] === 0) {
                            checked = '';
                        }
                        var pluginLabel = jQuery('<label></label>')
                            .append(jQuery('<span></span>').text(value))
                            .append(jQuery('<input class="edit" type="checkbox"  name="plugin_farmer_plugins[' + value + ']" ' + checked + '>'))
                        ;
                        pluginContainer.append(pluginLabel);
                    });
                    pluginContainer.append(submitButton.clone(), resetButton.clone());

                    // data is array you returned with action.php
                },
                'json'
            );
        });

        // make sure there's enough space for the dropdown
        jQuery('select').on('chosen:showing_dropdown', function(evt, params) {
            jQuery(evt.target).parent('fieldset').animate({
                "padding-bottom": '20em'
            }, 400);
        }).on('chosen:hiding_dropdown', function(evt, params) {
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
