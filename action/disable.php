<?php
/**
 * DokuWiki Plugin farmer (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

if(!defined('DOKU_INC')) die();

/**
 * Disable Plugins on install
 */
class action_plugin_farmer_disable extends DokuWiki_Action_Plugin {

    /**
     * plugin should use this method to register its handlers with the DokuWiki's event controller
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object. Also available as global $EVENT_HANDLER
     *
     */
    public function register(Doku_Event_Handler $controller) {
        /** @var helper_plugin_farmer $farmer */
        $farmer = plugin_load('helper', 'farmer');
        if($farmer->getAnimal()) return;

        if($this->getConf('disable_new_plugins')) {
            $controller->register_hook('PLUGIN_EXTENSION_CHANGE', 'AFTER', $this, 'handle_install');
        }
    }

    /**
     * handle install of new plugin
     *
     * @param Doku_Event $event
     * @param $param
     */
    public function handle_install(Doku_Event $event, $param) {
        if($event->data['action'] != 'install') return;

        /* @var Doku_Plugin_Controller $plugin_controller */
        global $plugin_controller;
        $plugin_controller = new Doku_Plugin_Controller(); // we need to refresh the status

        /** @var helper_plugin_extension_extension $ext */
        $ext = $event->data['extension'];
        $disabled = $ext->disable();
        if($disabled === true) {
            msg($this->getLang('disable_new_plugins'));
        } else {
            msg(hsc($disabled), -1);
        }
    }
}

