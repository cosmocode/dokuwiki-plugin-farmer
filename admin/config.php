<?php
/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 */

// must be run within Dokuwiki
use dokuwiki\Form\Form;

if(!defined('DOKU_INC')) die();

class admin_plugin_farmer_config extends DokuWiki_Admin_Plugin {

    /**
     * @return bool admin only!
     */
    public function forAdminOnly() {
        return false;
    }

    /**
     * Should carry out any processing required by the plugin.
     */
    public function handle() {
        global $INPUT;
        global $ID;
        if(!$INPUT->has('farmconf')) return;
        if(!checkSecurityToken()) return;

        $ini = DOKU_INC . 'conf/farm.ini';
        $data = "; Farm config created by the farmer plugin\n";
        $data .= $this->createIni($INPUT->arr('farmconf'));
        io_saveFile($ini, $data);

        $self = wl($ID, array('do' => 'admin', 'page' => 'farmer', 'sub' => 'config'), true, '&');
        send_redirect($self);
    }

    /**
     * Render HTML output, e.g. helpful text and a form
     */
    public function html() {
        global $FARMCORE;
        $farmconf = $FARMCORE->getConfig();

        $form = new Form(array('method' => 'post'));

        $form->addFieldsetOpen($this->getLang('conf_inherit'));
        foreach($farmconf['inherit'] as $key => $val) {
            $form->setHiddenField("farmconf[inherit][$key]", 0);
            $chk = $form->addCheckbox("farmconf[inherit][$key]", $this->getLang('conf_inherit_' . $key))->useInput(false);
            if($val) $chk->attr('checked', 'checked');
        }
        $form->addFieldsetClose();

        $form->addFieldsetOpen($this->getLang('conf_notfound'));
        $form->addTagOpen('select')->attr('name', 'farmconf[notfound][show]');
        foreach(array('farmer', '404', 'list', 'redirect') as $key) {
            $opt = $form->addTagOpen('option')->attr('value', $key);
            if($farmconf['notfound']['show'] == $key) $opt->attr('selected', 'selected');
            $form->addHTML($this->getLang('conf_notfound_' . $key));
            $form->addTagClose('option');
        }
        $form->addTagClose('select');
        $form->addTextInput('farmconf[notfound][url]', $this->getLang('conf_notfound_url'))->val($farmconf['notfound']['url']);
        $form->addFieldsetClose();

        $form->addButton('save', $this->getLang('save'));
        echo $form->toHTML();
    }

    /**
     * Simple function to create an ini file
     *
     * Does no escaping, but should suffice for our use case
     *
     * @link http://stackoverflow.com/a/5695202/172068
     * @param array $data The data to transform
     * @return string
     */
    public function createIni($data) {
        $res = array();
        foreach($data as $key => $val) {
            if(is_array($val)) {
                $res[] = '';
                $res[] = "[$key]";
                foreach($val as $skey => $sval) {
                    $res[] = "$skey = " . (is_numeric($sval) ? $sval : '"' . $sval . '"');
                }
            } else {
                $res[] = "$key = " . (is_numeric($val) ? $val : '"' . $val . '"');
            }
        }
        $res[] = '';
        return join("\n", $res);
    }
}

// vim:ts=4:sw=4:et:
