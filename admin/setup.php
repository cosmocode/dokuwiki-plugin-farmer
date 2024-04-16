<?php

use dokuwiki\Extension\AdminPlugin;
use dokuwiki\Form\Form;

/**
 * DokuWiki Plugin farmer (Admin Component)
 *
 * Setup the farm by creating preload.php etc
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
class admin_plugin_farmer_setup extends AdminPlugin
{
    /** @var helper_plugin_farmer $helper */
    private $helper;

    /** @inheritdoc */
    public function showInMenu()
    {
        return false;
    }

    /** @inheritdoc */
    public function handle()
    {
        global $INPUT;
        global $ID;

        if (!$INPUT->bool('farmdir')) return;
        if (!checkSecurityToken()) return;

        $this->helper = plugin_load('helper', 'farmer');

        $farmdir = trim($INPUT->str('farmdir', ''));
        if ($farmdir[0] !== '/') $farmdir = DOKU_INC . $farmdir;
        $farmdir = fullpath($farmdir);

        $errors = [];
        if ($farmdir === '') {
            $errors[] = $this->getLang('farmdir_missing');
        } elseif ($this->helper->isInPath($farmdir, DOKU_INC) !== false) {
            $errors[] = sprintf($this->getLang('farmdir_in_dokuwiki'), hsc($farmdir), hsc(DOKU_INC));
        } elseif (!io_mkdir_p($farmdir)) {
            $errors[] = sprintf($this->getLang('farmdir_uncreatable'), hsc($farmdir));
        } elseif (!is_writable($farmdir)) {
            $errors[] = sprintf($this->getLang('farmdir_unwritable'), hsc($farmdir));
        } elseif (count(scandir($farmdir)) > 2) {
            $errors[] = sprintf($this->getLang('farmdir_notEmpty'), hsc($farmdir));
        }

        if ($errors) {
            foreach ($errors as $error) {
                msg($error, -1);
            }
            return;
        }

        // create the files
        $ok = $this->createPreloadPHP();
        if ($ok && $INPUT->bool('htaccess')) $ok &= $this->createHtaccess();
        if ($ok) $ok &= $this->createFarmIni($farmdir);

        if ($ok) {
            msg($this->getLang('preload creation success'), 1);
            $link = wl($ID, ['do' => 'admin', 'page' => 'farmer', 'sub' => 'config'], true, '&');
            send_redirect($link);
        } else {
            msg($this->getLang('preload creation error'), -1);
        }
    }

    /** @inheritdoc */
    public function html()
    {
        // Is preload.php already enabled?
        if (file_exists(DOKU_INC . 'inc/preload.php')) {
            msg($this->getLang('overwrite_preload'), -1);
        }

        $form = new Form();
        $form->addClass('plugin_farmer');
        $form->addFieldsetOpen($this->getLang('preloadPHPForm'));
        $form->addTextInput('farmdir', $this->getLang('farm dir'));
        $form->addCheckbox('htaccess', $this->getLang('htaccess setup'))->attr('checked', 'checked');
        $form->addButton('farmer__submit', $this->getLang('submit'))->attr('type', 'submit');
        $form->addFieldsetClose();
        echo $form->toHTML();
    }

    /**
     * Creates the preload that loads our farm controller
     * @return bool true if saving was successful
     */
    protected function createPreloadPHP()
    {
        $content = "<?php\n";
        $content .= "# farm setup by farmer plugin\n";
        $content .= "if(file_exists(__DIR__ . '/../lib/plugins/farmer/DokuWikiFarmCore.php')) {\n";
        $content .= "    include(__DIR__ . '/../lib/plugins/farmer/DokuWikiFarmCore.php');\n";
        $content .= "}\n";
        return io_saveFile(DOKU_INC . 'inc/preload.php', $content);
    }

    /**
     * Prepends the needed config to the main .htaccess for htaccess type setups
     *
     * @return bool true if saving was successful
     */
    protected function createHtaccess()
    {
        // load existing or template
        if (file_exists(DOKU_INC . '.htaccess')) {
            $old = io_readFile(DOKU_INC . '.htaccess');
        } elseif (file_exists(DOKU_INC . '.htaccess.dist')) {
            $old = io_readFile(DOKU_INC . '.htaccess.dist');
        } else {
            $old = '';
        }

        $content = "# Options added for farm setup by farmer plugin:\n";
        $content .= "RewriteEngine On\n";
        $content .= 'RewriteRule ^!([^/]+)/(.*)  $2?animal=$1 [QSA,DPI]' . "\n";
        $content .= 'RewriteRule ^!([^/]+)$      ?animal=$1 [QSA,DPI]' . "\n";
        $content .= 'Options +FollowSymLinks' . "\n";
        $content .= '# end of farm configuration' . "\n\n";
        $content .= $old;
        return io_saveFile(DOKU_INC . '.htaccess', $content);
    }

    /**
     * Creates the initial configuration
     *
     * @param $animalpath
     * @return bool true if saving was successful
     */
    protected function createFarmIni($animalpath)
    {
        $content = "; farm config created by the farmer plugin\n\n";
        $content .= "[base]\n";
        $content .= "farmdir = $animalpath\n";
        $content .= "farmhost = {$_SERVER['HTTP_HOST']}\n";
        return io_saveFile(DOKU_INC . 'conf/farm.ini', $content);
    }
}
