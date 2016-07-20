<?php
/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
use dokuwiki\Form\Form;

if(!defined('DOKU_INC')) die();

/**
 * Information about the farm and the current instance
 */
class admin_plugin_farmer_delete extends DokuWiki_Admin_Plugin {

    /** @var helper_plugin_farmer */
    protected $helper;

    /**
     * admin_plugin_farmer_info constructor.
     */
    public function __construct() {
        $this->helper = plugin_load('helper', 'farmer');
    }

    /**
     * @return bool admin only!
     */
    public function forAdminOnly() {
        return true;
    }

    /**
     * Should carry out any processing required by the plugin.
     */
    public function handle() {
        global $INPUT;
        global $ID;
        if(!$INPUT->has('delete')) return;

        if($INPUT->filter('trim')->str('delanimal') === '') {
            msg($this->getLang('delete_noanimal'), -1);
            return;
        }

        if($INPUT->str('delanimal') != $INPUT->str('confirm')) {
            msg($this->getLang('delete_mismatch'), -1);
            return;
        }

        $animaldir = DOKU_FARMDIR . $INPUT->str('delanimal');

        if(!$this->helper->isInPath($animaldir, DOKU_FARMDIR) || !is_dir($animaldir)) {
            msg($this->getLang('delete_invalid'), -1);
            return;
        }

        // let's delete it
        $ok = io_rmdir($animaldir, true);
        if($ok) {
            msg($this->getLang('delete_success'), 1);
        } else {
            msg($this->getLang('delete_fail'), -1);
        }

        $link = wl($ID, array('do'=>'admin', 'page'=>'farmer', 'sub' => 'delete'), true, '&');
        send_redirect($link);
    }

    /**
     * Render HTML output, e.g. helpful text and a form
     */
    public function html() {

        $form = new Form();
        $form->addFieldsetOpen($this->getLang('delete_animal'));

        $animals = $this->helper->getAllAnimals();
        array_unshift($animals, '');
        $form->addDropdown('delanimal', $animals)->addClass('farmer_chosen_animals');
        $form->addTextInput('confirm', $this->getLang('delete_confirm'));
        $form->addButton('delete', $this->getLang('delete'));
        $form->addFieldsetClose();
        echo $form->toHTML();

    }


}

// vim:ts=4:sw=4:et:
