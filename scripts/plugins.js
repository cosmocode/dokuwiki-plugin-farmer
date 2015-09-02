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
                        var pluginCheckbox;
                        if (typeof data[1][value] !== 'undefined' && data[1][value] === 0) {
                            checked = '';
                        }
                        pluginCheckbox = jQuery('<input class="edit" type="checkbox" id="farmer__plugin_' + value + '" name="plugin_farmer_plugins[' + value + ']" ' + checked + '>');
                        pluginContainer.append(pluginCheckbox);
                        jQuery('#farmer__plugin_' + value).wrap('<label class="block"></label>').parent().prepend(value);
                    });
                    pluginContainer.append(submitButton.clone(), resetButton.clone());

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

        jQuery("input[name=serversetup]:radio").change(function () {
            if (jQuery('#htaccess__setup').prop("checked")) {
                jQuery('label.htaccess').css('display','block');
            } else {
                jQuery('label.htaccess').css('display','none');
            }
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


        jQuery.fn.exists = function(){return this.length>0;};
        if(jQuery('#farmer__create_animal_form').exists()) {
            var get_address;
            if (typeof JSINFO['FARMRELDIR'] === 'unbekannt') {
                JSINFO['FARMRELDIR'] = '';
            }
            if(jQuery('#plugin__farmer_animalCreation_success_msg').exists()) {

                var animalname = jQuery('div.success #animal__name').html();
                get_address = JSINFO['FARMRELDIR'] + animalname + '/lib/plugins/farmer/plugin.info.txt';
                jQuery.get(
                    get_address
                )
                    .done(function() {
                        jQuery('#plugin__farmer_animalCreation_success_msg').append(jQuery('<span> '+ LANG.plugins.farmer['animal ajax success'] +'</span>'));
                    })
                    .fail(function () {
                        alert(LANG.plugins.farmer['animal ajax failure']);
                    });
            }
            if(jQuery('#plugin__farmer_preload_success_msg').exists()) {
                get_address = JSINFO['FARMRELDIR'] + 'invalid_name' + '/lib/exe/ajax.php';
                jQuery.get(
                    get_address,
                    {
                        call: 'plugin_farmer_checkSetup'
                    },
                    null,
                    'json'
                )
                    .done(function() {
                        jQuery('#plugin__farmer_preload_success_msg').append(jQuery('<span> '+ LANG.plugins.farmer['preload ajax success'] +'</span>'));
                    })
                    .fail(function () {
                        alert(LANG.plugins.farmer['preload ajax failure']);
                    });
            }
        }

    });

})(this.farmer__plugin = {});
