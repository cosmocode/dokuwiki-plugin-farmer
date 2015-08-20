<?php
/**
 *
 *
 * @author     Michael Große <grosse@cosmocode.de>
 */

if(!defined('DOKU_INC')) die();

/**
 * Class action_plugin_farmer_pluginSettings
 */
class action_plugin_farmer_pluginSettings extends DokuWiki_Action_Plugin {

    /**
     * plugin should use this method to register its handlers with the DokuWiki's event controller
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object. Also available as global $EVENT_HANDLER
     *
     */
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,'_ajax_call');
    }

    /**
     * handle ajax requests
     *
     * @param Doku_Event $event
     * @param $param
     */
    function _ajax_call(Doku_Event $event, $param) {
        if (substr($event->data, 0, 13) !== 'plugin_farmer') {
            return;
        }
        //no other ajax call handlers needed
        $event->stopPropagation();
        $event->preventDefault();

        $animal = substr($event->data, 14);
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper','farmer');
        $allPlugins = $helper->getAllPlugins();
        $plugins = array();
        include(DOKU_FARMDIR . $animal . '/conf/plugins.local.php');
        $data = array($allPlugins, $plugins);

        //json library of DokuWiki
        require_once DOKU_INC . 'inc/JSON.php';
        $json = new JSON();

        //set content type
        header('Content-Type: application/json');
        echo $json->encode($data);
    }

}

