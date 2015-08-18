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

    var $output = 'world';

    /**
     * handle user request
     */
    function handle() {

        if (!file_exists(DOKU_INC . 'inc/preload.php')) {
            global $ID;
            $get = $_GET;
            if(isset($get['id'])) unset($get['id']);
            $get['page'] = 'farmer_foo';
            $self = wl($ID, $get, false, '&');
            send_redirect($self);
        }

        if (!isset($_REQUEST['cmd'])) return;   // first time - nothing to do

        $this->output = 'invalid';
        if (!checkSecurityToken()) return;
        if (!is_array($_REQUEST['cmd'])) return;

        // verify valid values
        switch (key($_REQUEST['cmd'])) {
            case 'hello' : $this->output = 'again'; break;
            case 'goodbye' : $this->output = 'goodbye'; break;
        }
        //send_redirect();
    }

    public function getAllPlugins() {
        $dir = dir(DOKU_PLUGIN);
        $plugins = array();
        while (false !== ($entry = $dir->read())) {
            if($entry == '.' || $entry == '..') {
                continue;
            }
            if (!is_dir($entry)) {
                continue;
            }
            $plugins[] = $entry;
        }
        return $plugins;
    }

    /**
     * output appropriate html
     */
    function html() {
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.min.js"></script>';
        echo '<link href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.min.css" type="text/css" rel="stylesheet" />';
        $form = new \dokuwiki\Form\Form();
        $form->addTagOpen('select')->id('farmer__animalSelect');
        $dir = dir(DOKU_FARMDIR);
        while (false !== ($entry = $dir->read())) {
            if ($entry == '.' || $entry == '..' || $entry == '_animal') {
                continue;
            }
            $form->addTagOpen('option');
            $form->addHTML($entry);
            $form->addTagClose('option');
        }
        $dir->close();
        $form->addTagClose('select');
        echo $form->toHTML();


    }

    public function getMenuText() {
        return 'Farmer: Change animal plugins';
    }

    public function getMenuSort() {
        return 42;
    }

}

