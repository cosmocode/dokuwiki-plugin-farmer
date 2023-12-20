<?php

use dokuwiki\Extension\ActionPlugin;
use dokuwiki\Extension\EventHandler;
use dokuwiki\Extension\Event;
use dokuwiki\Extension\PluginController;

/**
 * DokuWiki Plugin farmer (Action Component)
 *
 * Disable Plugins on install
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
class action_plugin_farmer_disable extends ActionPlugin
{
    /**
     * plugin should use this method to register its handlers with the DokuWiki's event controller
     *
     * @param EventHandler $controller DokuWiki's event controller object. Also available as global $EVENT_HANDLER
     */
    public function register(EventHandler $controller)
    {
        /** @var helper_plugin_farmer $farmer */
        $farmer = plugin_load('helper', 'farmer');
        if ($farmer->getAnimal()) return;

        if ($this->getConf('disable_new_plugins')) {
            $controller->register_hook('PLUGIN_EXTENSION_CHANGE', 'AFTER', $this, 'handleInstall');
        }
    }

    /**
     * handle install of new plugin
     *
     * @param Event $event
     * @param $param
     */
    public function handleInstall(Event $event, $param)
    {
        if ($event->data['action'] != 'install') return;

        /* @var Doku_Plugin_Controller $plugin_controller */
        global $plugin_controller;
        $plugin_controller = new PluginController(); // we need to refresh the status

        /** @var helper_plugin_extension_extension $ext */
        $ext = $event->data['extension'];
        $disabled = $ext->disable();
        if ($disabled === true) {
            msg($this->getLang('disable_new_plugins'));
        } else {
            msg(hsc($disabled), -1);
        }
    }
}
