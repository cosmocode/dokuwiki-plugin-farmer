<?php
/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class admin_plugin_farmer_createAnimal extends DokuWiki_Admin_Plugin {

    private $preloadPHPMissing = false;
    private $errorMessages = array();

    /** @var helper_plugin_farmer $helper */
    private $helper;

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

    public function createNewAnimal($name, $adminSetup, $adminPassword, $subdomain) {
        //DOKU_FARMDIR
        if (DOKU_FARMTYPE === 'subdomain') {
            $animaldir = DOKU_FARMDIR . $subdomain;
        } elseif (DOKU_FARMTYPE === 'htaccess') {
            $animaldir = DOKU_FARMDIR . $name;
        } else {
            throw new Exception('invalid value for $serverSetup');
        }

        if (!file_exists(DOKU_FARMDIR . '_animal')) {
            $this->helper->downloadTemplate(DOKU_FARMDIR);
        }

        try {
            $this->helper->io_copyDir(DOKU_FARMDIR . '_animal', $animaldir);
        } catch (Exception $e) {
            dbglog(dbg_backtrace());
            return false;
        }

        $confFile = file_get_contents($animaldir . '/conf/local.php');
        $confFile = str_replace('Animal Wiki Title', $name, $confFile);
        io_saveFile($animaldir . '/conf/local.php', $confFile);

        if ($adminSetup === 'newAdmin') {
            $cryptAdminPassword = auth_cryptPassword($adminPassword);
            $usersAuth = file_get_contents($animaldir . '/conf/users.auth.php');
            $usersAuth = str_replace('$1$cce258b2$U9o5nK0z4MhTfB5QlKF23/', $cryptAdminPassword, $usersAuth);
            io_saveFile($animaldir . '/conf/users.auth.php', $usersAuth);
        } elseif ($adminSetup === 'importUsers') {
            copy(DOKU_CONF . 'users.auth.php', $animaldir . '/conf/users.auth.php');
        } elseif ($adminSetup === 'currentAdmin') {
            $masterUsers = file_get_contents(DOKU_CONF . 'users.auth.php');
            $user = $_SERVER['REMOTE_USER'];
            $masterUsers = trim(strstr($masterUsers,"\n". $user . ":"));
            $newAdmin = substr($masterUsers,0,strpos($masterUsers,"\n")+1);
            io_saveFile($animaldir . '/conf/users.auth.php', $newAdmin);
        } else {
            throw new Exception('invalid value for $adminSetup');
        }

        return true;
    }

    public function createPreloadPHP($animalpath, $setuptype) {
        $this->helper->downloadTemplate($animalpath);

        $content = "<?php\n";
        $content .= "if(!defined('DOKU_FARMDIR')) define('DOKU_FARMDIR', '$animalpath');\n";
        $content .= "if(!defined('DOKU_FARMTYPE')) define('DOKU_FARMTYPE', '$setuptype');\n";
        $content .= "include(fullpath(dirname(__FILE__)).'/farm.php');\n";

        return io_saveFile(DOKU_INC . 'inc/preload.php',$content);
    }

    /**
     * Should carry out any processing required by the plugin.
     */
    public function handle() {

        $this->helper = plugin_load('helper','farmer');

        // Is preload.php already enabled?
        if (!file_exists(DOKU_INC . 'inc/preload.php')) {
            $this->preloadPHPMissing = true;
            if (isset($_REQUEST['farmdir'])) {
                if (empty($_REQUEST['farmdir'])) {
                    $this->errorMessages['farmdir'] = $this->getLang('farmdir_missing');
                } else {
                    $farmdir = rtrim(hsc(trim($_REQUEST['farmdir'])),'/');
                    if ($this->helper->isInPath($farmdir, DOKU_INC) !== false) {
                        $this->errorMessages['farmdir'] = $this->getLang('farmdir_in_dokuwiki');
                    } elseif (!io_mkdir_p($farmdir)) {
                        $this->errorMessages['farmdir'] = $this->getLang('farmdir_uncreatable');
                    } elseif (!is_writeable($farmdir)) {
                        $this->errorMessages['farmdir'] = $this->getLang('farmdir_unwritable');
                    } elseif (count(scandir($farmdir)) > 2) {
                        $this->errorMessages['farmdir'] = $this->getLang('farmdir_notEmpty');
                    }
                }

                if (empty($_REQUEST['serversetup'])) {
                    $this->errorMessages['serversetup'] = $this->getLang('serversetup_missing');
                }

                if (empty($this->errorMessages)) {
                    $ret = $this->createPreloadPHP(realpath($farmdir) . "/", $_REQUEST['serversetup']);
                    if ($ret === true) {
                        msg('inc/preload.php has been succesfully created', 1);
                        $this->helper->reloadAdminPage();
                    } else {
                        msg('there was an error creating inc/preload.php',-1);
                    }
                }
            }
        } else {
            if (isset($_REQUEST['farmer__submit'])) {
                $animalsubdomain = null;
                $animalname = null;
                if (empty($_REQUEST['animalname'])) {
                    $this->errorMessages['animalname'] = $this->getLang('animalname_missing');
                } else {
                    $animalname = hsc(trim($_REQUEST['animalname']));
                    if (!preg_match("/^[a-z0-9]+(-[a-z0-9]+)*$/i",$animalname)) { //@todo: tests for regex
                        $this->errorMessages['animalname'] = $this->getLang('animalname_invalid');
                    }
                }

                if ($_REQUEST['adminsetup'] === 'newAdmin') {
                    if (empty($_REQUEST['adminPassword'])) {
                        $this->errorMessages['adminPassword'] = $this->getLang('adminPassword_empty');
                    }
                }

                if (DOKU_FARMTYPE === 'subdomain') {
                    if (empty($_REQUEST['animalsubdomain'])) {
                        $this->errorMessages['animalsubdomain'] = $this->getLang('animalsubdomain_missing');
                    } else {
                        $animalsubdomain = hsc(trim($_REQUEST['animalsubdomain']));
                        if (!preg_match("/^[a-z0-9]+([\.-][a-z0-9]+)*$/i",$animalsubdomain)) { //@todo: tests for regex
                            $this->errorMessages['animalsubdomain'] =  $this->getLang('animalsubdomain_invalid');
                        } elseif (file_exists(DOKU_FARMDIR . $animalsubdomain)) {
                            $this->errorMessages['animalsubdomain'] =  $this->getLang('animalsubdomain_preexisting');
                        }
                    }
                } elseif ($_REQUEST['serversetup'] === 'htaccess') {
                    if (file_exists(DOKU_FARMDIR . $animalname)) {
                        $this->errorMessages['animalname'] =  $this->getLang('animalname_preexisting');
                    }
                }

                if (empty($this->errorMessages)) {
                    $ret = $this->createNewAnimal($animalname, $_REQUEST['adminsetup'], $_REQUEST['adminPassword'], $animalsubdomain);
                    if ($ret === true) {
                        msg(sprintf($this->getLang('animal creation success'),$animalname), 1);
                        $this->helper->reloadAdminPage();
                    } else {
                        // should never happen
                        msg('there has been an error creating the animal', -1);
                    }
                }
            }
        }
    }

    /**
     * Render HTML output, e.g. helpful text and a form
     */
    public function html() {

        if ($this->preloadPHPMissing) {
            echo sprintf($this->locale_xhtml('preload'),realpath(DOKU_INC.'..') . '/animals/');
            $form = new \dokuwiki\Form\Form();
            $form->addClass('plugin_farmer');
            $form->addFieldsetOpen($this->getLang('preloadPHPForm'));
            $form->addTextInput('farmdir', $this->getLang('farm dir'))->addClass('block edit')->attr('placeholder','farm dir');

            $form->addRadioButton('serversetup', $this->getLang('htaccess setup'))->val('htaccess')->attr('type','radio')->addClass('block edit');
            $form->addRadioButton('serversetup', $this->getLang('subdomain setup'))->val('subdomain')->attr('type','radio')->addClass('block edit');

            $form->addButton('farmer__submit',$this->getLang('submit'))->attr('type','submit');

            $form->addFieldsetClose();
            $this->helper->addErrorsToForm($form, $this->errorMessages);

            echo $form->toHTML();
        } else {
            $form = new \dokuwiki\Form\Form();
            $form->addClass('plugin_farmer');
            $form->addFieldsetOpen($this->getLang('animal configuration'));
            $form->addTextInput('animalname',$this->getLang('animal name'))->addClass('block edit')->attr('placeholder',$this->getLang('animal name placeholder'));
            if (DOKU_FARMTYPE === 'subdomain') {
                $form->addTextInput('animalsubdomain', $this->getLang('animal subdomain'))->addClass('block edit')->attr('placeholder', $this->getLang('animal subdomain placeholder'));
            }
            $form->addFieldsetClose();
            $form->addTag('br');

            $form->addFieldsetOpen($this->getLang('animal administrator'));
            $form->addRadioButton('adminsetup',$this->getLang('importUsers'))->val('importUsers')->addClass('block');
            $form->addRadioButton('adminsetup', $this->getLang('currentAdmin'))->val('currentAdmin')->addClass('block');
            $form->addRadioButton('adminsetup', $this->getLang('newAdmin'))->val('newAdmin')->addClass('block')->attr('checked','checked');
            $form->addPasswordInput('adminPassword',$this->getLang('admin password'))->addClass('block edit')->attr('placeholder',$this->getLang('admin password placeholder'));
            $form->addFieldsetClose();
            $form->addTag('br');

            $form->addButton('farmer__submit',$this->getLang('submit'))->attr('type','submit')->val('newAnimal');

            $this->helper->addErrorsToForm($form, $this->errorMessages);

            echo $form->toHTML();
        }

    }
}

// vim:ts=4:sw=4:et:
