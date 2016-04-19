<?php
/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
use dokuwiki\Form\Form;

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

/**
 * Configuration Interface for farm.ini
 */
class admin_plugin_farmer_config extends DokuWiki_Admin_Plugin {

    /** @var  helper_plugin_farmer */
    protected $helper;

    /**
     * @return bool admin only!
     */
    public function forAdminOnly() {
        return false;
    }

    /**
     * admin_plugin_farmer_config constructor.
     */
    public function __construct() {
        $this->helper = plugin_load('helper', 'farmer');
    }

    /**
     * Should carry out any processing required by the plugin.
     */
    public function handle() {
        global $INPUT;
        global $ID;
        if(!$INPUT->has('farmconf')) return;
        if(!checkSecurityToken()) return;

        $farmconf = $this->helper->getConfig();
        $farmdir = $farmconf['base']['farmdir'];
        $farmconf = array_merge($farmconf, $INPUT->arr('farmconf'));
        $farmconf['base']['farmdir'] = $farmdir;

        $farmconf['base']['basedomain'] = trim(trim($farmconf['base']['basedomain'], '.'));

        $ini = DOKU_INC . 'conf/farm.ini';
        $data = "; Farm config created by the farmer plugin\n";
        $data .= $this->createIni($farmconf);
        io_saveFile($ini, $data);

        $self = wl($ID, array('do' => 'admin', 'page' => 'farmer', 'sub' => 'config'), true, '&');
        send_redirect($self);
    }

    /**
     * Render HTML output, e.g. helpful text and a form
     */
    public function html() {
        $farmconf = $this->helper->getConfig();

        $form = new Form(array('method' => 'post'));

        $form->addFieldsetOpen($this->getLang('base'));
        $form->addHTML('<label><span>' . $this->getLang('farm dir') . '</span>' . DOKU_FARMDIR);
        $form->addTextInput('farmconf[base][farmhost]', $this->getLang('farm host'))->val($farmconf['base']['farmhost']);
        $form->addTextInput('farmconf[base][basedomain]', $this->getLang('base domain'))->val($farmconf['base']['basedomain']);
        $form->addFieldsetClose();

        $form->addFieldsetOpen($this->getLang('conf_inherit'));
        foreach($farmconf['inherit'] as $key => $val) {
            $form->setHiddenField("farmconf[inherit][$key]", 0);
            $chk = $form->addCheckbox("farmconf[inherit][$key]", $this->getLang('conf_inherit_' . $key))->useInput(false);
            if($val) $chk->attr('checked', 'checked');
        }
        $form->addFieldsetClose();

        $options = array(
            'farmer' => $this->getLang('conf_notfound_farmer'),
            '404' => $this->getLang('conf_notfound_404'),
            'list' => $this->getLang('conf_notfound_list'),
            'redirect' => $this->getLang('conf_notfound_redirect')
        );

        $form->addFieldsetOpen($this->getLang('conf_notfound'));
        $form->addDropdown('farmconf[notfound][show]', $options, $this->getLang('conf_notfound'))->val($farmconf['notfound']['show']);
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
