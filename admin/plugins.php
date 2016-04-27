<?php
/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * Manage Animal Plugin settings
 */
class admin_plugin_farmer_plugins extends DokuWiki_Admin_Plugin {

    /** @var helper_plugin_farmer $helper */
    private $helper;

    public function __construct() {
        $this->helper = plugin_load('helper', 'farmer');
    }

    /**
     * handle user request
     */
    public function handle() {
        global $INPUT;
        global $ID;

        $self = wl($ID, array('do' => 'admin', 'page' => 'farmer', 'sub' => 'plugins'), true, '&');

        if($INPUT->has('farmer__submitBulk')) {
            $animals = $this->helper->getAllAnimals();
            $plugin = $INPUT->str('farmer__bulkPluginSelect');
            foreach($animals as $animal) {
                if($INPUT->str('farmer__submitBulk') === 'activate') {
                    $this->helper->activatePlugin($plugin, $animal);
                } else {
                    $this->helper->deactivatePlugin($plugin, $animal);
                }
            }
            msg($this->getLang('plugindone'), 1);
            send_redirect($self);
        }
        if($INPUT->has('plugin_farmer')) {
            $inputArray = $INPUT->arr('plugin_farmer');
            if($inputArray['submit_type'] === 'updateSingleAnimal') {
                $animal = $inputArray ['selectedAnimal'];
                $allPlugins = $this->helper->getAllPlugins();
                $activePlugins = $INPUT->arr('plugin_farmer_plugins');
                foreach($allPlugins as $plugin) {
                    if(isset($activePlugins[$plugin]) &&
                        $activePlugins[$plugin] === 'on'
                    ) {
                        $this->helper->activatePlugin($plugin, $animal);
                    } else {
                        $this->helper->deactivatePlugin($plugin, $animal);
                    }
                }
            }
            msg($this->getLang('plugindone'), 1);
            send_redirect($self);
        }
    }

    /**
     * output appropriate html
     */
    public function html() {

        echo $this->locale_xhtml('plugins');
        $switchForm = new \dokuwiki\Form\Form();
        $switchForm->addClass('plugin_farmer');
        $switchForm->addFieldsetOpen($this->getLang('bulkSingleSwitcher'));
        $switchForm->addRadioButton('bulkSingleSwitch', $this->getLang('bulkEdit'))->id('farmer__bulk')->attr('type', 'radio');
        $switchForm->addRadioButton('bulkSingleSwitch', $this->getLang('singleEdit'))->id('farmer__single')->attr('type', 'radio');
        $switchForm->addFieldsetClose();
        echo $switchForm->toHTML();

        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $plugins = $helper->getAllPlugins();
        array_unshift($plugins, '');

        $bulkForm = new \dokuwiki\Form\Form();
        $bulkForm->id('farmer__bulkForm');
        $bulkForm->addClass('plugin_farmer');
        $bulkForm->addFieldsetOpen($this->getLang('bulkEditForm'));
        $bulkForm->addDropdown('farmer__bulkPluginSelect', $plugins)->id('farmer__bulkPluginSelect');
        $bulkForm->addButton('farmer__submitBulk', $this->getLang('activate'))->attr('value', 'activate')->attr('type', 'submit')->attr('disabled', 'disabled');
        $bulkForm->addButton('farmer__submitBulk', $this->getLang('deactivate'))->attr('value', 'deactivate')->attr('type', 'submit')->attr('disabled', 'disabled');
        $bulkForm->addFieldsetClose();
        echo $bulkForm->toHTML();

        $animals = $helper->getAllAnimals();
        array_unshift($animals, '');

        $singleForm = new \dokuwiki\Form\Form();
        $singleForm->id('farmer__singlePluginForm');
        $singleForm->addClass('plugin_farmer');
        $singleForm->addFieldsetOpen($this->getLang('singleEditForm'));
        $singleForm->addDropdown('plugin_farmer[selectedAnimal]', $animals)->id('farmer__animalSelect');
        $singleForm->addTagOpen('div')->id('farmer__animalPlugins');
        $singleForm->addTagClose('div');
        $switchForm->addFieldsetClose();
        echo $singleForm->toHTML();
    }
}

