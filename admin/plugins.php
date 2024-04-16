<?php

use dokuwiki\Extension\AdminPlugin;
use dokuwiki\Form\Form;

/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * Manage Animal Plugin settings
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
class admin_plugin_farmer_plugins extends AdminPlugin
{
    /** @var helper_plugin_farmer $helper */
    private $helper;

    public function __construct()
    {
        $this->helper = plugin_load('helper', 'farmer');
    }

    /** @inheritdoc */
    public function showInMenu()
    {
        return false;
    }

    /** @inheritdoc */
    public function handle()
    {
        global $INPUT;
        global $ID;

        $self = wl($ID, ['do' => 'admin', 'page' => 'farmer', 'sub' => 'plugins'], true, '&');

        if ($INPUT->has('bulk_plugin') && $INPUT->has('state')) {
            $animals = $this->helper->getAllAnimals();
            $plugin = $INPUT->str('bulk_plugin');
            foreach ($animals as $animal) {
                $this->helper->setPluginState($plugin, $animal, $INPUT->int('state'));
            }
            msg($this->getLang('plugindone'), 1);
            send_redirect($self);
        }

        if ($INPUT->has('bulk_animal') && $INPUT->has('bulk_plugins')) {
            $animal = $INPUT->str('bulk_animal');
            $activePlugins = $INPUT->arr('bulk_plugins');
            foreach ($activePlugins as $plugin => $state) {
                $this->helper->setPluginState($plugin, $animal, $state);
            }
            msg($this->getLang('plugindone'), 1);
            send_redirect($self);
        }
    }

    /** @inheritdoc */
    public function html()
    {

        echo $this->locale_xhtml('plugins');
        $switchForm = new Form();
        $switchForm->addClass('plugin_farmer');
        $switchForm->addFieldsetOpen($this->getLang('bulkSingleSwitcher'));
        $switchForm->addRadioButton('bulkSingleSwitch', $this->getLang('bulkEdit'))
            ->id('farmer__bulk')
            ->attr('type', 'radio');
        $switchForm->addRadioButton('bulkSingleSwitch', $this->getLang('singleEdit'))
            ->id('farmer__single')
            ->attr('type', 'radio');
        $switchForm->addRadioButton('bulkSingleSwitch', $this->getLang('matrixEdit'))
            ->id('farmer__matrix')
            ->attr('type', 'radio');
        $switchForm->addFieldsetClose();
        echo $switchForm->toHTML();

        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $plugins = $helper->getAllPlugins();
        array_unshift($plugins, '');

        // All Animals at once
        $bulkForm = new Form();
        $bulkForm->id('farmer__pluginsforall');
        $bulkForm->addFieldsetOpen($this->getLang('bulkEditForm'));
        $bulkForm->addDropdown('bulk_plugin', $plugins);
        $bulkForm->addButton('state', $this->getLang('default'))
            ->attr('value', '-1')
            ->attr('type', 'submit')
            ->attr('disabled', 'disabled');
        $bulkForm->addButton('state', $this->getLang('activate'))
            ->attr('value', '1')
            ->attr('type', 'submit')
            ->attr('disabled', 'disabled');
        $bulkForm->addButton('state', $this->getLang('deactivate'))
            ->attr('value', '0')
            ->attr('type', 'submit')
            ->attr('disabled', 'disabled');
        $bulkForm->addFieldsetClose();
        echo $bulkForm->toHTML();

        $animals = $helper->getAllAnimals();
        array_unshift($animals, '');

        // One Animal, all the plugins
        $singleForm = new Form();
        $singleForm->id('farmer__pluginsforone');
        $singleForm->addFieldsetOpen($this->getLang('singleEditForm'));
        $singleForm->addDropdown('bulk_animal', $animals);
        $singleForm->addTagOpen('div')->addClass('output');
        $singleForm->addTagClose('div');
        $singleForm->addButton('save', $this->getLang('save'))
            ->attr('disabled', 'disabled');

        echo $singleForm->toHTML();


        echo '<div id="farmer__pluginmatrix"></div>';
    }
}
