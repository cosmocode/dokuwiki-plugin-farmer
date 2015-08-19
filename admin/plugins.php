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

    /**
     * handle user request
     */
    public function handle() {

        if (!file_exists(DOKU_INC . 'inc/preload.php')) {
            global $ID;
            $get = $_GET;
            if(isset($get['id'])) unset($get['id']);
            $get['page'] = 'farmer_foo';
            $self = wl($ID, $get, false, '&');
            send_redirect($self);
        }

        if (isset($_REQUEST['farmer_submitBulk'])) {
            /** @var helper_plugin_farmer $helper */
            $helper = plugin_load('helper', 'farmer');
            $animals = $helper->getAllAnimals();
            $plugin = $_REQUEST['farmer__bulkPluginSelect'];
            foreach ($animals as $animal) {
                $pluginConf = file(DOKU_FARMDIR . $animal . '/conf/plugins.local.php');
                if ($_REQUEST['farmer_submitBulk'] === 'activate') {
                    foreach ($pluginConf as $key => $line) {
                        if (strpos($line, '$plugins[' . $plugin . ']') !== FALSE) {
                            array_splice($pluginConf, $key, 1);
                            break; // the plugin was deactivated and the deactivation is now removed
                        }
                    }
                } else {
                    $pluginIsActive = true;
                    foreach ($pluginConf as $key => $line) {
                        if (strpos($line, '$plugins[' . $plugin . ']') !== FALSE) {
                            $pluginIsActive = false;
                            break; // the plugin is already deactivated;
                        }
                    }
                    if ($pluginIsActive) {
                        $pluginConf[] = '$plugins[' . $plugin . '] = 0';
                    }
                }
                io_saveFile(DOKU_FARMDIR . $animal . '/conf/plugins.local.php', implode('\n',$pluginConf));
                touch(DOKU_FARMDIR . $animal . '/conf/local.php');
            }
        }
    }

    /**
     * output appropriate html
     */
    public function html() {
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.min.js"></script>';
        echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.min.css" type="text/css" rel="stylesheet" />';

        $switchForm =  new \dokuwiki\Form\Form();
        $switchForm->addFieldsetOpen('edit a single animal or all at once?');
        $switchForm->addRadioButton('bulkSingleSwitch', 'bulk edit all animals')->id('farmer__bulk')->attr('type','radio')->addClass('block');
        $switchForm->addRadioButton('bulkSingleSwitch', 'edit a single animal')->id('farmer__single')->attr('type','radio')->addClass('block');
        $switchForm->addFieldsetClose();
        echo $switchForm->toHTML();

        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $plugins = $helper->getAllPlugins();

        $bulkForm = new \dokuwiki\Form\Form();
        $bulkForm->id('farmer__bulkForm');
        $bulkForm->addHTML('bulk');
        $bulkForm->addTagOpen('select')->id('farmer__bulkPluginSelect')->attr('name','farmer__bulkPlugin');
        $bulkForm->addTagOpen('option')->attr('selected', 'selected')->attr('disabled', 'disabled')->attr('hidden', 'hidden')->attr('value', "");
        $bulkForm->addTagClose('option');
        foreach ($plugins as $plugin) {
            $bulkForm->addTagOpen('option')->attr('value', $plugin);
            $bulkForm->addHTML($plugin);
            $bulkForm->addTagClose('option');
        }
        $bulkForm->addTagClose('select');
        $bulkForm->addButton('farmer__submitBulk','Activate')->attr('value','activate')->attr('type','submit')->attr('disabled','disabled')->addClass('bulkButton');
        $bulkForm->addButton('farmer__submitBulk','Deactivate')->attr('value','deactivate')->attr('type','submit')->attr('disabled','disabled')->addClass('bulkButton');
        echo $bulkForm->toHTML();

        $singleForm = new \dokuwiki\Form\Form();
        $singleForm->id('farmer__singlePluginForm');
        $singleForm->addTagOpen('select')->id('farmer__animalSelect');
        $singleForm->addTagOpen('option')->attr('selected', 'selected')->attr('disabled', 'disabled')->attr('hidden', 'hidden')->attr('value', "");
        $singleForm->addTagClose('option');
        $animals = $helper->getAllAnimals();
        foreach ($animals as $animal) {
            $singleForm->addTagOpen('option');
            $singleForm->addHTML($animal);
            $singleForm->addTagClose('option');
        }
        $singleForm->addTagClose('select');
        echo $singleForm->toHTML();
    }

    public function getMenuText() {
        return 'Farmer: Change animal plugins';
    }

    public function getMenuSort() {
        return 42;
    }

}

