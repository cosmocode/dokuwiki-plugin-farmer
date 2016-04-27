/**
 * DokuWiki Plugin farmer (JS for plugin management)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
(function () {
    'use strict';

    jQuery(function () {
        // general animal select
        jQuery('select.farmer_choosen_animals').chosen({
            width: '100%',
            search_contains: true,
            allow_single_deselect: true,
            "placeholder_text_single": LANG.plugins.farmer.animalSelect
        });


        // Plugin Management for all Animals
        var $formAllAnimals = jQuery('#farmer__pluginsforall');
        $formAllAnimals.find('select')
            .change(function () {
                $formAllAnimals.find('button').prop('disabled', false);
            })
            .chosen({
                width: '100%',
                search_contains: true,
                "placeholder_text_single": LANG.plugins.farmer.pluginSelect
            })
        ;

        // Plugin Management for single Animals
        var $formSingleAnimal = jQuery('#farmer__pluginsforone');
        $formSingleAnimal.find('select')
            .change(function () {
                var animal = jQuery(this).val();
                $formSingleAnimal.find('button').prop('disabled', true);
                jQuery.post(
                    DOKU_BASE + 'lib/exe/ajax.php',
                    {
                        call: 'plugin_farmer_getPlugins_' + animal
                    },
                    function (data) {
                        $formSingleAnimal.find('div.output').html(data);
                        $formSingleAnimal.find('button').prop('disabled', false);
                    },
                    'html'
                )}
            )
            .chosen({
                width: '100%',
                search_contains: true,
                "placeholder_text_single": LANG.plugins.farmer.animalSelect
            })
        ;



        // make sure there's enough space for the dropdown
        jQuery('select').on('chosen:showing_dropdown', function (evt, params) {
            jQuery(evt.target).parent('fieldset').animate({
                "padding-bottom": '20em'
            }, 400);
        }).on('chosen:hiding_dropdown', function (evt, params) {
            jQuery(evt.target).parent('fieldset').animate({
                "padding-bottom": '7px'
            }, 400);
        });


        jQuery("input[name=bulkSingleSwitch]:radio").change(function () {
            if (jQuery('#farmer__bulk').prop("checked")) {
                $formAllAnimals.show();
                $formSingleAnimal.hide();
            } else {
                $formAllAnimals.hide();
                $formSingleAnimal.show();


            }
        });
        jQuery('#farmer__bulk').click();


    });

})();
