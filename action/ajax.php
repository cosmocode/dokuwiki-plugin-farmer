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
 * Manage AJAX features
 */
class action_plugin_farmer_ajax extends DokuWiki_Action_Plugin {

    /**
     * plugin should use this method to register its handlers with the DokuWiki's event controller
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object. Also available as global $EVENT_HANDLER
     *
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this, '_ajax_call');
    }

    /**
     * handle ajax requests
     *
     * @param Doku_Event $event
     * @param $param
     */
    public function _ajax_call(Doku_Event $event, $param) {
        if(substr($event->data, 0, 13) !== 'plugin_farmer') {
            return;
        }
        //no other ajax call handlers needed
        $event->stopPropagation();
        $event->preventDefault();

        if(substr($event->data, 14, 10) === 'getPlugins') {
            $this->get_animal_plugins($event, $param);
            return;
        }
        if(substr($event->data, 14, 10) === 'checkSetup') {
            $this->check_setup($event, $param);
        }
    }

    /**
     * This function exists in order to provide a positive (i.e. 200) response to an ajax request to a non-existing animal.
     *
     * @param Doku_Event $event
     * @param            $param
     */
    public function check_setup(Doku_Event $event, $param) {
        $data = '';
        $json = new JSON();
        header('Content-Type: application/json');
        echo $json->encode($data);
    }

    /**
     * @param Doku_Event $event
     * @param            $param
     */
    public function get_animal_plugins(Doku_Event $event, $param) {
        $animal = substr($event->data, 25);
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $allPlugins = $helper->getAllPlugins();
        $plugins = array();

        // FIXME do we need to check other files as well? refer to config cascade
        $local = DOKU_FARMDIR . '/' . $animal . '/conf/plugins.local.php';
        if(file_exists($local)) include($local);
        $data = array($allPlugins, $plugins,);

        //json library of DokuWiki
        $json = new JSON();

        //set content type
        header('Content-Type: application/json');
        echo $json->encode($data);
    }

}

