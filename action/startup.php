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
 * Handles Farm mechanisms on DokuWiki startup
 */
class action_plugin_farmer_startup extends DokuWiki_Action_Plugin {

    /** @var  helper_plugin_farmer */
    protected $helper;

    /**
     * action_plugin_farmer_startup constructor.
     */
    public function __construct() {
        $this->helper = plugin_load('helper', 'farmer');
    }

    /**
     * plugin should use this method to register its handlers with the DokuWiki's event controller
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object. Also available as global $EVENT_HANDLER
     *
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'before_start');
    }

    public function before_start(Doku_Event $event, $param) {
        if($this->helper->wasNotfound()) $this->handleNotFound();
    }

    /**
     * Handles the animal not found case
     *
     * Will abort the current script unless the farmer is wanted
     */
    protected function handleNotFound() {
        /** @noinspection PhpUnusedLocalVariableInspection */
        global $conf, $lang;
        $config = $this->helper->getConfig();
        $show = $config['notfound']['show'];
        $url = $config['notfound']['url'];
        if($show == 'farmer') return;

        if($show == '404' || $show == 'list') {
            http_status(404);
            $body = $this->locale_xhtml('notfound_' . $show);
            /** @noinspection PhpUnusedLocalVariableInspection */
            $title = '404';
            if($show == 'list') {
                /** @noinspection PhpUnusedLocalVariableInspection */
                $body .= $this->animalList();
            }

            include __DIR__ . '/../includes/template.php';
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
        $html = '<ul>';
        $animals = $this->helper->getAllAnimals();
        foreach($animals as $animal) {
            $link = $this->helper->getAnimalURL($animal);
            $html .= '<li><div class="li"><a href="' . $link . '">' . hsc($animal) . '</a></div></li>';
        }
        $html .= '</ul>';
        return $html;
    }

}

