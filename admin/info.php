<?php
/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class admin_plugin_farmer_info extends DokuWiki_Admin_Plugin {



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
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $animal = $helper->getAnimal();
        $config = $helper->getConfig();

        echo '<table class="inline">';

        $this->line('thisis', $animal ? $this->getLang('thisis.animal') : $this->getLang('thisis.farmer'));
        if($animal) {
            $this->line('animal', $animal);
        }

        $this->line('baseinstall', DOKU_INC);
        $this->line('farm host', $config['base']['farmhost']);
        $this->line('farm dir', DOKU_FARMDIR);
        $this->line('animals', count($helper->getAllAnimals()));


        foreach($config['inherit'] as $key => $value) {
            $this->line('conf_inherit_'.$key, $this->getLang($value ? 'conf_inherit_yes' : 'conf_inherit_no'));
        }

        echo '</table>';
    }

    protected function line($langkey, $value) {
        echo '<tr>';
        echo '<th>'.$this->getLang($langkey).'</th>';
        echo '<td>'.$value.'</td>';
        echo '</tr>';
    }

}

// vim:ts=4:sw=4:et:
