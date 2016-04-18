<?php
/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class admin_plugin_farmer_new extends DokuWiki_Admin_Plugin {

    /** @var helper_plugin_farmer $helper */
    protected $helper;

    /**
     * @return bool true if only access for superuser, false is for superusers and moderators
     */
    public function forAdminOnly() {
        return true;
    }

    /**
     * admin_plugin_farmer_new constructor.
     */
    public function __construct() {
        $this->helper = plugin_load('helper', 'farmer');
    }

    /**
     * Should carry out any processing required by the plugin.
     */
    public function handle() {
        global $INPUT;
        global $ID;
        if(!$INPUT->has('farmer__submit')) return;

        $data = $this->validateAnimalData();
        if(!$data) return;
        if($this->createNewAnimal($data['name'], $data['admin'], $data['pass'])){
            msg($this->getLang('animal creation success'), 1);
            $link = wl($ID, array('do'=>'admin', 'page'=>'farmer', 'sub'=>'new'), true, '&');
            send_redirect($link);
        }
    }

    /**
     * Render HTML output, e.g. helpful text and a form
     */
    public function html() {

        $form = new \dokuwiki\Form\Form();
        $form->addClass('plugin_farmer')->id('farmer__create_animal_form');

        $form->addFieldsetOpen($this->getLang('animal configuration'));
        $form->addTextInput('animalname', $this->getLang('animal name'));
        $form->addFieldsetClose();

        $form->addFieldsetOpen($this->getLang('animal administrator'));
        $form->addRadioButton('adminsetup', $this->getLang('importUsers'))->val('importUsers');
        $form->addRadioButton('adminsetup', $this->getLang('currentAdmin'))->val('currentAdmin');
        $form->addRadioButton('adminsetup', $this->getLang('newAdmin'))->val('newAdmin')->attr('checked', 'checked');
        $form->addPasswordInput('adminPassword', $this->getLang('admin password'));
        $form->addFieldsetClose();

        $form->addButton('farmer__submit', $this->getLang('submit'))->attr('type', 'submit')->val('newAnimal');
        echo $form->toHTML();

        echo $this->locale_xhtml('tab_new_help');
    }

    /**
     * Validate the data for a new animal
     *
     * @return array|bool false on errors, clean data otherwise
     */
    protected function validateAnimalData() {
        global $INPUT;

        $animalname = $INPUT->filter('trim')->str('animalname');
        $adminsetup = $INPUT->str('adminsetup');
        $adminpass = $INPUT->filter('trim')->str('adminPassword');

        $errors = array();

        if($animalname === '') {
            $errors[] = $this->getLang('animalname_missing');
        } elseif(!$this->helper->validateAnimalName($animalname)) {
            $errors[] = $this->getLang('animalname_invalid');
        }

        if($adminsetup === 'newAdmin' && $adminpass === '') {
            $errors[] = $this->getLang('adminPassword_empty');
        }

        if($animalname !== '' && file_exists(DOKU_FARMDIR . '/' . $animalname)) {
            $errors[] = $this->getLang('animalname_preexisting');
        }

        if($errors) {
            foreach($errors as $error) {
                msg($error, -1);
            }
            return false;
        }

        return array(
            'name' => $animalname,
            'admin' => $adminsetup,
            'pass' => $adminpass
        );
    }

    /**
     * Create a new animal
     *
     * @param string $name name/title of the animal, will be the directory name for htaccess setup
     * @param string $adminSetup newAdmin, currentAdmin or importUsers
     * @param string $adminPassword required if $adminSetup is newAdmin
     * @return bool true if successful
     */
    protected function createNewAnimal($name, $adminSetup, $adminPassword) {
        $animaldir = DOKU_FARMDIR . '/' . $name;

        // copy basic template
        $ok = $this->helper->io_copyDir(__DIR__ . '/../_animal', $animaldir);
        if(!$ok) {
            msg($this->getLang('animal creation error'), -1);
            return false;
        }

        // append title to local config
        $ok &= io_saveFile($animaldir.'/conf/local.php', "\n".'$conf[\'title\'] = \''.$name.'\';'."\n", true);

        // create a random logo and favicon
        if(!class_exists('\splitbrain\RingIcon\RingIcon', false)) {
            require(__DIR__ . '/../3rdparty/RingIcon.php');
        }
        if(!class_exists('\chrisbliss18\phpico\PHPIco', false)) {
            require(__DIR__ . '/../3rdparty/PHPIco.php');
        }
        try {
            $ringicon = new \splitbrain\RingIcon\RingIcon(64);
            $ringicon->createImage($animaldir, $animaldir . '/data/media/wiki/logo.png');
            $icongen = new \chrisbliss18\phpico\PHPIco($animaldir . '/data/media/wiki/logo.png');
            $icongen->save_ico($animaldir . '/data/media/wiki/favicon.ico');
        } catch(\Exception $ignore) {
            // something went wrong, but we don't care. this is a nice to have feature only
        }

        // create admin user
        if($adminSetup === 'newAdmin') {
            $users = "# <?php exit()?>\n".$this->makeAdminLine($adminPassword)."\n";
        } elseif($adminSetup === 'currentAdmin') {
            $users = "# <?php exit()?>\n".$this->getAdminLine()."\n";
        } else {
            $users = io_readFile(DOKU_CONF . 'users.auth.php');
        }
        $ok &= io_saveFile($animaldir . '/conf/users.auth.php', $users);

        /* FIXME handle deactivated plugins
        if($this->getConf('deactivated plugins') === '') {
            $deactivatedPluginsList = array('farmer',);
        } else {
            $deactivatedPluginsList = explode(',', $this->getConf('deactivated plugins'));
            array_push($deactivatedPluginsList, 'farmer');
        }
        foreach($deactivatedPluginsList as $plugin) {
            $this->helper->deactivatePlugin(trim($plugin), $animal);
        }
        */

        return $ok;
    }

    /**
     * Creates a new user line
     *
     * @param $password
     * @return string
     */
    protected function makeAdminLine($password) {
        $pass = auth_cryptPassword($password);
        $line = join("\t", array(
            'admin',
            $pass,
            'Administrator',
            'admin@example.org',
            'admin,user'
        ));
        return $line;
    }

    /**
     * Copies the current user as new admin line
     *
     * @return string
     */
    protected function getAdminLine() {
        $currentAdmin = $_SERVER['REMOTE_USER'];
        $masterUsers = file_get_contents(DOKU_CONF . 'users.auth.php');
        $masterUsers = ltrim(strstr($masterUsers, "\n" . $currentAdmin . ":"));
        $newAdmin = substr($masterUsers, 0, strpos($masterUsers, "\n") + 1);
        return $newAdmin;
    }


}

// vim:ts=4:sw=4:et:
