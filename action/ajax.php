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

        $plugins = $helper->getAnimalPluginRealState($animal);

        header('Content-Type: text/html; charset=utf-8');

        echo '<table>';
        echo '<tr>';
        echo '<th>' . $this->getLang('plugin') . '</th>';
        echo '<th>' . $this->getLang('plugin_default') . '</th>';
        echo '<th>' . $this->getLang('plugin_enabled') . '</th>';
        echo '<th>' . $this->getLang('plugin_disabled') . '</th>';
        echo '</tr>';

        foreach($plugins as $plugin) {
            echo '<tr>';
            echo '<th>' . hsc($plugin['name']) . '</th>';

            echo '<td>';
            $attr = array();
            $attr['type'] = 'radio';
            $attr['name'] = 'bulk_plugins[' . $plugin['name'] . ']';
            $attr['value'] = '-1';
            if($plugin['isdefault']) {
                $attr['checked'] = 'checked';
            }
            echo '<label>';
            echo '<input ' . buildAttributes($attr) . ' />';
            if($plugin['default']) {
                echo ' (' . $this->getLang('plugin_on') . ')';
            } else {
                echo ' (' . $this->getLang('plugin_off') . ')';
            }
            echo '</label>';
            echo '</td>';

            echo '<td>';
            $attr = array();
            $attr['type'] = 'radio';
            $attr['name'] = 'bulk_plugins[' . $plugin['name'] . ']';
            $attr['value'] = '1';
            if(!$plugin['isdefault'] && $plugin['actual']) {
                $attr['checked'] = 'checked';
            }
            echo '<label>';
            echo '<input ' . buildAttributes($attr) . ' />';
            echo ' ' . $this->getLang('plugin_on');
            echo '</label>';
            echo '</td>';

            echo '<td>';
            $attr = array();
            $attr['type'] = 'radio';
            $attr['name'] = 'bulk_plugins[' . $plugin['name'] . ']';
            $attr['value'] = '0';
            if(!$plugin['isdefault'] && !$plugin['actual']) {
                $attr['checked'] = 'checked';
            }
            echo '<label>';
            echo '<input ' . buildAttributes($attr) . ' />';
            echo ' ' . $this->getLang('plugin_off');
            echo '</label>';
            echo '</td>';

            echo '</tr>';
        }
    }

}

