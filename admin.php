<?php

/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * This is the main admin page. It displays the tabs and then loads the sub components
 * according to the selected tab
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
class admin_plugin_farmer extends DokuWiki_Admin_Plugin {

    /** @var helper_plugin_farmer */
    protected $helper;
    /** @var array The available pages for the current user in the current wiki */
    protected $pages;
    /** @var string The currently selected page */
    protected $page;
    /** @var DokuWiki_Admin_Plugin the plugin to use for the current page */
    protected $adminplugin;

    /**
     * @return bool we're available for managers and admins
     */
    public function forAdminOnly() {
        return false;
    }

    /**
     * Initialize current page
     */
    public function __construct() {
        global $INPUT;
        $this->helper = plugin_load('helper', 'farmer');

        // set available pages depending on user and animal
        $isanimal = (bool) $this->helper->getAnimal();
        if($isanimal || !auth_isadmin()) {
            $this->pages = array(
                'info'
            );
        } else {
            if(!$this->helper->checkFarmSetup()) {
                $this->pages = array(
                    'setup'
                );
            } else {
                $this->pages = array(
                    'info',
                    'config',
                    'new',
                    'plugins',
                    'delete'
                );
            }
        }

        // make sure current page requested is available
        $this->page = $INPUT->str('sub');
        if(!in_array($this->page, $this->pages)) {
            $this->page = $this->pages[0];
        }

        // load the sub component
        $this->adminplugin = plugin_load('admin', 'farmer_' . $this->page);
        if(!$this->adminplugin) nice_die('Something went wrong loading the plugin component for ' . hsc($this->page));
    }

    /**
     * handle user request
     */
    public function handle() {
        $this->adminplugin->handle();
    }

    /**
     * output appropriate tab
     */
    public function html() {
        global $ID;

        echo '<div id="plugin__farmer_admin">';
        echo '<h1>' . $this->getLang('menu') . '</h1>';

        echo '<ul class="tabs" id="plugin__farmer_tabs">';
        foreach($this->pages as $page) {
            $link = wl($ID, array('do' => 'admin', 'page' => 'farmer', 'sub' => $page));
            $class = ($page == $this->page) ? 'active' : '';

            echo '<li class="' . $class . '"><a href="' . $link . '">' . $this->getLang('tab_' . $page) . '</a></li>';
        }
        echo '</ul>';
        echo '<div class="panelHeader">';
        echo $this->locale_xhtml('tab_' . $this->page);
        echo '</div>';
        echo '<div class="panelMain">';
        $this->adminplugin->html();
        echo '</div>';
        echo '<div class="panelFooter">';
        echo $this->locale_xhtml('tab_' . $this->page . '_help');
        echo '</div>';
        echo '</div>';
    }

    /**
     * @return int
     */
    public function getMenuSort() {
        return 42;
    }

}

