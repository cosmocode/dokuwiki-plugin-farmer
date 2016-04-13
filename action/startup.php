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
class action_plugin_farmer_startup extends DokuWiki_Action_Plugin {

    /**
     * plugin should use this method to register its handlers with the DokuWiki's event controller
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object. Also available as global $EVENT_HANDLER
     *
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE',  $this, 'before_start');
    }


    public function before_start(Doku_Event $event, $param) {
        if(!isset($GLOBALS['FARMCORE'])) return;
        global $FARMCORE;

        if($FARMCORE->wasNotfound()) $this->handleNotFound();
    }

    /**
     * Handles the animal not found case
     *
     * Will abort the current script unless the farmer is wanted
     */
    protected function handleNotFound() {
        global $FARMCORE;
        global $conf;
        global $lang;
        $config = $FARMCORE->getConfig();
        $show = $config['notfound']['show'];
        $url = $config['notfound']['url'];
        if($show == 'farmer') return;

        if($show == '404' || $show == 'list') {
            http_status(404);
            $body = $this->locale_xhtml('notfound_'.$show);
            $title = '404';
            if($show == 'list') {
                $body .= $this->animalList();
            }

            include __DIR__ . '/../template.php';
            exit;
        }

        if($show == 'redirect' && $url) {
            send_redirect($url);
        }
    }

    /**
     * Retrun a HTML list of animals
     *
     * @return string
     */
    protected function animalList() {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        global $FARMCORE;

        $html = '<ul>';
        $animals = $helper->getAllAnimals();
        foreach($animals as $animal) {
            if($FARMCORE->isHostbased()) {
                $link = '//:'.$animal.'/';
            } else {
                $link = DOKU_BASE.'!'.$animal.'/';
            }

            $html .= '<li><div class="li"><a href="'.$link.'">'.hsc($animal).'</a></div></li>';
        }
        $html .= '</ul>';
        return $html;
    }

}

