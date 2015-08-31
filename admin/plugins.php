<?php
/**
 * Plugin Skeleton: Displays "Hello World!"
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Christopher Smith <chris@jalakai.co.uk>
 */


/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_farmer_plugins extends DokuWiki_Admin_Plugin {

    /** @var helper_plugin_farmer $helper */
    private $helper;

    /**
     * handle user request
     */
    public function handle() {
        global $INPUT;
        $this->helper = plugin_load('helper', 'farmer');

        if (!$this->helper->checkFarmSetup()) {
            $this->helper->reloadAdminPage('farmer_createAnimal');
        }

        if ($INPUT->has('farmer__submitBulk')) {
            $animals = $this->helper->getAllAnimals();
            $plugin = $INPUT->str('farmer__bulkPluginSelect');
            foreach ($animals as $animal) {
                if ($INPUT->str('farmer__submitBulk') === 'activate') {
                    $this->helper->activatePlugin($plugin, $animal);
                } else {
                    $this->helper->deactivatePlugin($plugin, $animal);
                }
            }
        }
        if ($INPUT->has('plugin_farmer')) {
            $inputArray = $INPUT->arr('plugin_farmer');
            if ($inputArray['submit_type'] === 'updateSingleAnimal') {
                $animal = $inputArray ['selectedAnimal'];
                $allPlugins = $this->helper->getAllPlugins();
                $activePlugins = $INPUT->arr('plugin_farmer_plugins');
                foreach ($allPlugins as $plugin) {
                    if (isset($activePlugins[$plugin]) &&
                        $activePlugins[$plugin] === 'on') {
                        $this->helper->activatePlugin($plugin,$animal);
                    } else {
                        $this->helper->deactivatePlugin($plugin,$animal);
                    }
                }
            }
        }
    }

    /**
     * output appropriate html
     */
    public function html() {
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.min.js"></script>';
        echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.min.css" type="text/css" rel="stylesheet" />';

        echo $this->locale_xhtml('plugins');
        $switchForm = new \dokuwiki\Form\Form();
        $switchForm->addClass('plugin_farmer');
        $switchForm->addFieldsetOpen($this->getLang('bulkSingleSwitcher'));
        $switchForm->addRadioButton('bulkSingleSwitch', $this->getLang('bulkEdit'))->id('farmer__bulk')->attr('type','radio')->addClass('block');
        $switchForm->addRadioButton('bulkSingleSwitch', $this->getLang('singleEdit'))->id('farmer__single')->attr('type','radio')->addClass('block');
        $switchForm->addFieldsetClose();
        echo $switchForm->toHTML();

        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $plugins = $helper->getAllPlugins();

        $bulkForm = new \dokuwiki\Form\Form();
        $bulkForm->id('farmer__bulkForm');
        $bulkForm->addClass('plugin_farmer');
        $bulkForm->addFieldsetOpen($this->getLang('bulkEditForm'));
        $bulkForm->addTagOpen('select')->id('farmer__bulkPluginSelect')->attr('name','farmer__bulkPluginSelect');
        $bulkForm->addTagOpen('option')->attr('selected', 'selected')->attr('disabled', 'disabled')->attr('hidden', 'hidden')->attr('value', "");
        $bulkForm->addTagClose('option');
        foreach ($plugins as $plugin) {
            $bulkForm->addTagOpen('option')->attr('value', $plugin);
            $bulkForm->addHTML($plugin);
            $bulkForm->addTagClose('option');
        }
        $bulkForm->addTagClose('select');
        $bulkForm->addButton('farmer__submitBulk',$this->getLang('activate'))->attr('value','activate')->attr('type','submit')->attr('disabled','disabled')->addClass('bulkButton');
        $bulkForm->addButton('farmer__submitBulk',$this->getLang('deactivate'))->attr('value','deactivate')->attr('type','submit')->attr('disabled','disabled')->addClass('bulkButton');
        $bulkForm->addFieldsetClose();
        echo $bulkForm->toHTML();

        $singleForm = new \dokuwiki\Form\Form();
        $singleForm->id('farmer__singlePluginForm');
        $singleForm->addClass('plugin_farmer');
        $singleForm->addFieldsetOpen($this->getLang('singleEditForm'));
        $singleForm->addTagOpen('select')->id('farmer__animalSelect')->attr('name', 'plugin_farmer[selectedAnimal]');
        $singleForm->addTagOpen('option')->attr('selected', 'selected')->attr('disabled', 'disabled')->attr('hidden', 'hidden')->attr('value', "");
        $singleForm->addTagClose('option');
        $animals = $helper->getAllAnimals();
        foreach ($animals as $animal) {
            $singleForm->addTagOpen('option');
            $singleForm->addHTML($animal);
            $singleForm->addTagClose('option');
        }
        $singleForm->addTagClose('select');
        $singleForm->addTagOpen('div')->id('farmer__animalPlugins');
        $singleForm->addTagClose('div');
        $switchForm->addFieldsetClose();
        echo $singleForm->toHTML();
    }

    public function getMenuText() {
        return 'Farmer: Change animal plugins';
    }

    public function getMenuSort() {
        return 42;
    }

}

