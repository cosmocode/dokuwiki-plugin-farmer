<?php
/**
 * DokuWiki Plugin farmer (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <grosse@cosmocode.de>
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class helper_plugin_farmer extends DokuWiki_Plugin {

    private $allPlugins = array();

    /**
     * Returns the name of the current animal if any, false otherwise
     *
     * @return string|false
     */
    public function getAnimal() {
        if(!isset($GLOBALS['FARMCORE'])) return false;
        return $GLOBALS['FARMCORE']->getAnimal();
    }

    /**
     * Get the farm config
     *
     * @return array
     */
    public function getConfig() {
        if(!isset($GLOBALS['FARMCORE'])) return array();
        return $GLOBALS['FARMCORE']->getConfig();
    }

    /**
     * Was the current animal requested by host?
     *
     * @return bool
     */
    public function isHostbased() {
        if(!isset($GLOBALS['FARMCORE'])) return false;
        return $GLOBALS['FARMCORE']->isHostbased();
    }

    /**
     * Was an animal requested that could not be found?
     *
     * @return bool
     */
    public function wasNotfound() {
        if(!isset($GLOBALS['FARMCORE'])) return false;
        return $GLOBALS['FARMCORE']->wasNotfound();
    }

    /**
     * Guess the URL for an animal
     *
     * @param $animal
     * @return string
     */
    public function getAnimalURL($animal) {
        $config = $this->getConfig();

        if(strpos($animal, '.') !== false) {
            return 'http://' . $animal;
        } elseif($config['base']['basedomain']) {
            return 'http://' . $animal . '.' . $config['base']['basedomain'];
        } else {
            return DOKU_URL . '!' . $animal . '/';
        }
    }

    /**
     * List of all animals, i.e. directories within DOKU_FARMDIR without the template.
     *
     * @return array
     */
    public function getAllAnimals() {
        $animals = array();
        $list = glob(DOKU_FARMDIR . '/*/conf/', GLOB_ONLYDIR);
        foreach($list as $path) {
            $animal = basename(dirname($path));
            if($animal == '_animal') continue; // old template
            $animals[] = $animal;
        }
        sort($animals);
        return $animals;
    }

    /**
     * checks wether $path is in under $container
     *
     * @param string $path
     * @param string $container
     * @return bool
     */
    public function isInPath($path, $container) {
        return (strpos(fullpath($path), fullpath($container)) === 0);
    }

    /**
     * Check if the farm is correctly configured for this farmer plugin
     *
     * @return bool
     */
    public function checkFarmSetup() {
        return defined('DOKU_FARMDIR') && isset($GLOBALS['FARMCORE']);
    }

    /**
     * @param string $animalname
     *
     * @return bool
     */
    public function validateAnimalName($animalname) {
        return preg_match("/^[a-z0-9]+(\\.-[a-z0-9]+)*$/i", $animalname) === 1;
    }

    /**
     * Copy a file, or recursively copy a folder and its contents. Adapted for DokuWiki.
     *
     * @todo: needs tests
     *
     * @author      Aidan Lister <aidan@php.net>
     * @author      Michael Große <grosse@cosmocode.de>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     *
     * @param       string $source Source path
     * @param       string $destination Destination path
     *
     * @return      bool     Returns TRUE on success, FALSE on failure
     */
    function io_copyDir($source, $destination) {
        if(is_link($source)) {
            io_lock($destination);
            $result = symlink(readlink($source), $destination);
            io_unlock($destination);
            return $result;
        }

        if(is_file($source)) {
            io_lock($destination);
            $result = copy($source, $destination);
            io_unlock($destination);
            return $result;
        }

        if(!is_dir($destination)) {
            io_mkdir_p($destination);
        }

        $dir = @dir($source);
        if($dir === false) return false;
        while(false !== ($entry = $dir->read())) {
            if($entry == '.' || $entry == '..') {
                continue;
            }

            // recurse into directories
            $this->io_copyDir("$source/$entry", "$destination/$entry");
        }

        $dir->close();
        return true;
    }

    /**
     * get a list of all Plugins installed in the farmer wiki, regardless whether they are active or not.
     *
     * @return array
     */
    public function getAllPlugins() {
        $dir = dir(DOKU_PLUGIN);
        $plugins = array();
        while(false !== ($entry = $dir->read())) {
            if($entry == '.' || $entry == '..' || $entry == 'testing' || $entry == 'farmer') {
                continue;
            }
            if(!is_dir(DOKU_PLUGIN . "/$entry")) {
                continue;
            }
            $plugins[] = $entry;
        }
        sort($plugins);
        return $plugins;
    }

    /**
     * Actiate a specific plugin in a specific animal
     *
     * @param string $plugin Name of the plugin to be activated
     * @param string $animal Directory of the animal within DOKU_FARMDIR
     */
    public function activatePlugin($plugin, $animal) {
        if(isset($this->allPlugins[$animal])) {
            $plugins = $this->allPlugins[$animal];
        } else {
            include(DOKU_FARMDIR . $animal . '/conf/plugins.local.php');
        }
        if(isset($plugins[$plugin]) && $plugins[$plugin] === 0) {
            unset($plugins[$plugin]);
            $this->writePluginConf($plugins, $animal);
        }
        $this->allPlugins[$animal] = $plugins;
    }

    /**
     * @param string $plugin Name of the plugin to be deactivated
     * @param string $animal Directory of the animal within DOKU_FARMDIR
     */
    public function deactivatePlugin($plugin, $animal) {
        if(isset($this->allPlugins[$animal])) {
            $plugins = $this->allPlugins[$animal];
        } else {
            include(DOKU_FARMDIR . $animal . '/conf/plugins.local.php');
        }
        if(!isset($plugins[$plugin]) || $plugins[$plugin] !== 0) {
            $plugins[$plugin] = 0;
            $this->writePluginConf($plugins, $animal);
        }
        $this->allPlugins[$animal] = $plugins;
    }

    /**
     * Write the list of (deactivated) plugins as plugin configuration of an animal to file
     *
     * @param array $plugins associative array with the key being the plugin name and the value 0 or 1
     * @param string $animal Directory of the animal within DOKU_FARMDIR
     */
    public function writePluginConf($plugins, $animal) {
        $pluginConf = '<?php' . "\n";
        foreach($plugins as $plugin => $status) {
            $pluginConf .= '$plugins["' . $plugin . '"] = ' . $status . ";\n";
        }
        io_saveFile(DOKU_FARMDIR . $animal . '/conf/plugins.local.php', $pluginConf);
        touch(DOKU_FARMDIR . $animal . '/conf/local.php');
    }

    /**
     * Show a message for all errors which occured during form validation
     *
     * @param \dokuwiki\Form\Form $form The form to which the errors should be added.
     * @param array $errorArray An associative array with the key being the name of the element at fault
     *                                         and the value being the associated error message.
     */
    public function addErrorsToForm(\dokuwiki\Form\Form &$form, $errorArray) {
        foreach($errorArray as $elementName => $errorMessage) {
            $offset = 0;
            msg($errorMessage, -1);
            while($form->findPositionByAttribute('name', $elementName, $offset)) {
                $offset = $form->findPositionByAttribute('name', $elementName, $offset);
                $form->getElementAt($offset)->addClass('error');
                ++$offset;
            }
        }
    }

    /**
     * @param string|null $page load adminpage $page, reload the current page if $page is ommited or null
     */
    public function reloadAdminPage($page = null) {
        global $ID;
        $get = $_GET;
        if(isset($get['id'])) unset($get['id']);
        if($page !== null) {
            $get['page'] = $page;
        }
        $self = wl($ID, $get, false, '&');
        send_redirect($self);
    }

    /**
     * Download and extract the animal template
     *
     * @param string $animalpath
     *
     * @throws \splitbrain\PHPArchive\ArchiveIOException
     */
    public function downloadTemplate($animalpath) {
        file_put_contents($animalpath . '/_animal.zip', fopen('https://www.dokuwiki.org/_media/dokuwiki_farm_animal.zip', 'r'));
        $zip = new splitbrain\PHPArchive\Zip();
        $zip->open($animalpath . '/_animal.zip');
        $zip->extract($animalpath);
        $zip->close();
        unlink($animalpath . '/_animal.zip');
    }

    /**
     * The subdomain must contain at least two dots
     *
     * @link http://stackoverflow.com/questions/17986371/regular-expression-to-validate-fqdn-in-c-sharp-and-javascript
     *
     * @param string $subdomain
     *
     * @return bool
     */
    public function validateSubdomain($subdomain) {
        return preg_match("/^(?=.{1,254}$)((?=[a-z0-9-]{1,63}\.)(xn--+)?[a-z0-9]+(-[a-z0-9]+)*\.){2,}[a-z]{2,63}$/i", $subdomain) === 1;
    }

}
