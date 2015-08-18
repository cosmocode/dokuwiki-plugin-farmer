<?php
/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

require_once DOKU_PLUGIN . 'farmer/adminActions/createPreload.php';
require_once DOKU_PLUGIN . 'farmer/adminActions/createAnimal.php';

class admin_plugin_farmer_foo extends DokuWiki_Admin_Plugin {

    private $adminAction;

    /**
     * @return int sort number in admin menu
     */
    public function getMenuSort() {
        return 42;
    }

    /**
     * @return bool true if only access for superuser, false is for superusers and moderators
     */
    public function forAdminOnly() {
        return true;
    }

    public function createNewAnimal($name, $subdomain, $serverSetup, $adminPassword) {
        //DOKU_FARMDIR
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper','farmer');

        if ($serverSetup === 'subdomain') {
            $animaldir = DOKU_FARMDIR . $subdomain;
        } elseif ($serverSetup === 'htaccess') {
            $animaldir = DOKU_FARMDIR . $name;
        } else {
            throw new Exception('invalid value for $serverSetup');
        }
        //todo: abort if animaldir already exists

        $helper->io_copyDir(DOKU_FARMDIR . '_animal', $animaldir);

        $cryptAdminPassword = auth_cryptPassword($adminPassword);
        $usersAuth = file_get_contents($animaldir . '/conf/users.auth.php');
        $usersAuth = str_replace('$1$cce258b2$U9o5nK0z4MhTfB5QlKF23/', $cryptAdminPassword, $usersAuth);
        io_saveFile($animaldir . '/conf/users.auth.php',$usersAuth);

    }

    /**
     * Should carry out any processing required by the plugin.
     */
    public function handle() {
        // Is preload.php already enabled?
        if (!file_exists(DOKU_INC . 'inc/preload.php')) {
            $this->adminAction = new createPreload();
            if (isset($_REQUEST['farmdir'])) {
                $this->adminAction->createPreloadPHP($_REQUEST['farmdir']);
            } else {
                dbg($_REQUEST);
            }
        } else {
            dbg($_REQUEST);
            $this->adminAction = new createAnimal();
            if (isset($_REQUEST['farmer__submit'])) {
                $this->createNewAnimal($_REQUEST['animalname'], $_REQUEST['animalsubdomain'], $_REQUEST['serversetup'], $_REQUEST['adminPassword']);
            }
        }
    }

    /**
     * Render HTML output, e.g. helpful text and a form
     */
    public function html() {
        ptln('<h1>'.$this->getLang('menu').'</h1>');
        $this->adminAction->html();
    }
}

// vim:ts=4:sw=4:et:
