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
 * Information about the farm and the current instance
 */
class admin_plugin_farmer_info extends DokuWiki_Admin_Plugin {

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
        return false;
    }

    /**
     * Should carry out any processing required by the plugin.
     */
    public function handle() {
    }

    /**
     * Render HTML output, e.g. helpful text and a form
     */
    public function html() {
        global $conf;
        global $INPUT;

        $animal = $this->helper->getAnimal();
        $config = $this->helper->getConfig();

        echo '<table class="inline">';

        $this->line('thisis', $animal ? $this->getLang('thisis.animal') : $this->getLang('thisis.farmer'));
        if($animal) {
            $this->line('animal', $animal);
        }
        $this->line('confdir', fullpath(DOKU_CONF) . '/');
        $this->line('savedir', fullpath($conf['savedir']) . '/');
        $this->line('baseinstall', DOKU_INC);
        $this->line('farm host', $config['base']['farmhost']);
        $this->line('farm dir', DOKU_FARMDIR);

        $this->line('animals', $this->animals($INPUT->bool('list')));

        foreach($config['inherit'] as $key => $value) {
            $this->line('conf_inherit_' . $key, $this->getLang($value ? 'conf_inherit_yes' : 'conf_inherit_no'));
        }

        $this->line('plugins', join(', ', $this->helper->getAllPlugins(false)));

        echo '</table>';
    }

    /**
     * List or count the animals
     *
     * @param bool $list
     * @return string
     */
    protected function animals($list) {
        global $ID;

        $animals = $this->helper->getAllAnimals();
        $html = '';
        if(!$list) {
            $html = count($animals);
            $self = wl($ID, array('do' => 'admin', 'page' => 'farmer', 'sub' => 'info', 'list' => 1));
            $html .= ' [<a href="' . $self . '">' . $this->getLang('conf_notfound_list') . '</a>]';
            return $html;
        }

        $html .= '<ol>';
        foreach($animals as $animal) {
            $link = $this->helper->getAnimalURL($animal);
            $html .= '<li><div class="li"><a href="' . $link . '">' . $animal . '</a></div></li>';
        }
        $html .= '</ol>';
        return $html;
    }

    /**
     * Output a table line
     *
     * @param string $langkey
     * @param string $value
     */
    protected function line($langkey, $value) {
        echo '<tr>';
        echo '<th>' . $this->getLang($langkey) . '</th>';
        echo '<td>' . $value . '</td>';
        echo '</tr>';
    }

}

// vim:ts=4:sw=4:et:
