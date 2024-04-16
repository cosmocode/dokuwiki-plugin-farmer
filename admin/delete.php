<?php

use dokuwiki\Extension\AdminPlugin;
use dokuwiki\Form\Form;

/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * Information about the farm and the current instance
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
class admin_plugin_farmer_delete extends AdminPlugin
{
    /** @var helper_plugin_farmer */
    protected $helper;

    /**
     * admin_plugin_farmer_info constructor.
     */
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
        if (!$INPUT->has('delete')) return;

        if ($INPUT->filter('trim')->str('delanimal') === '') {
            msg($this->getLang('delete_noanimal'), -1);
            return;
        }

        if ($INPUT->str('delanimal') != $INPUT->str('confirm')) {
            msg($this->getLang('delete_mismatch'), -1);
            return;
        }

        $animaldir = DOKU_FARMDIR . $INPUT->str('delanimal');

        if (!$this->helper->isInPath($animaldir, DOKU_FARMDIR) || !is_dir($animaldir)) {
            msg($this->getLang('delete_invalid'), -1);
            return;
        }

        // let's delete it
        $ok = io_rmdir($animaldir, true);
        if ($ok) {
            msg($this->getLang('delete_success'), 1);
        } else {
            msg($this->getLang('delete_fail'), -1);
        }

        $link = wl($ID, ['do' => 'admin', 'page' => 'farmer', 'sub' => 'delete'], true, '&');
        send_redirect($link);
    }

    /** @inheritdoc */
    public function html()
    {

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
