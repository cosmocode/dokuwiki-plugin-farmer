<?php

/**
 * Core Manager for the Farm functionality
 *
 * This class is initialized before any other DokuWiki code runs. Therefore it is
 * completely selfcontained and does not use any of DokuWiki's utility functions.
 *
 * It's registered as a global $FARMCORE variable but you should not interact with
 * it directly. Instead use the Farmer plugin's helper component.
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Andreas Gohr <gohr@cosmocode.de>
 */
class DokuWikiFarmCore {
    /**
     * @var array The default config - changed by loadConfig
     */
    protected $config = array(
        'base' => array(
            'farmdir' => '',
            'farmhost' => '',
            'basedomain' => '',
        ),
        'notfound' => array(
            'show' => 'farmer',
            'url' => ''
        ),
        'inherit' => array(
            'main' => 1,
            'acronyms' => 1,
            'entities' => 1,
            'interwiki' => 1,
            'license' => 1,
            'mime' => 1,
            'scheme' => 1,
            'smileys' => 1,
            'wordblock' => 1,
            'users' => 0,
            'plugins' => 0,
            'userstyle' => 0,
            'userscript' => 0
        )
    );

    /** @var string|false The current animal, false for farmer */
    protected $animal = false;
    /** @var bool true if an animal was requested but was not found */
    protected $notfound = false;
    /** @var bool true if the current animal was requested by host */
    protected $hostbased = false;

    /**
     * DokuWikiFarmCore constructor.
     *
     * This initializes the whole farm by loading the configuration and setting
     * DOKU_CONF depending on the requested animal
     */
    public function __construct() {
        $this->loadConfig();
        if($this->config['base']['farmdir'] === '') return; // farm setup not complete
        $this->config['base']['farmdir'] = rtrim($this->config['base']['farmdir'], '/').'/'; // trailing slash always
        define('DOKU_FARMDIR', $this->config['base']['farmdir']);

        // animal?
        $this->detectAnimal();

        // setup defines
        define('DOKU_FARM_ANIMAL', $this->animal);
        if($this->animal) {
            define('DOKU_CONF', DOKU_FARMDIR . $this->animal . '/conf/');
        } else {
            define('DOKU_CONF', DOKU_INC . '/conf/');
        }

        $this->setupCascade();
        $this->adjustCascade();
    }

    /**
     * @return array the current farm configuration
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @return false|string
     */
    public function getAnimal() {
        return $this->animal;
    }

    /**
     * @return boolean
     */
    public function isHostbased() {
        return $this->hostbased;
    }

    /**
     * @return boolean
     */
    public function wasNotfound() {
        return $this->notfound;
    }

    /**
     * @return string
     */
    public function getAnimalDataDir() {
        return DOKU_FARMDIR . $this->getAnimal() . '/data/';
    }

    /**
     * @return string
     */
    public function getAnimalBaseDir() {
        if($this->isHostbased()) return '';
        return getBaseURL() . '!' . $this->getAnimal();
    }

    /**
     * Detect the current animal
     *
     * Sets internal members $animal, $notfound and $hostbased
     *
     * This borrows form DokuWiki's inc/farm.php but does not support a default conf dir
     */
    protected function detectAnimal() {
        $farmdir = $this->config['base']['farmdir'];
        $farmhost = $this->config['base']['farmhost'];

        // check if animal was set via parameter (rewrite or CLI)
        $animal = '';
        if(isset($_REQUEST['animal'])) $animal = $_REQUEST['animal'];
        if('cli' == php_sapi_name() && isset($_SERVER['animal'])) $animal = $_SERVER['animal'];
        if($animal) {
            // check that $animal is a string and just a directory name and not a path
            if(!is_string($animal) || strpbrk($animal, '\\/') !== false) {
                $this->notfound = true;
                return;
            };
            $animal = strtolower($animal);

            // check if animal exists
            if(is_dir("$farmdir/$animal/conf")) {
                $this->animal = $animal;
                return;
            } else {
                $this->notfound = true;
                return;
            }
        }

        // no host - no host based setup. if we're still here then it's the farmer
        if(!isset($_SERVER['HTTP_HOST'])) return;

        // is this the farmer?
        if(strtolower($_SERVER['HTTP_HOST']) == $farmhost) {
            return;
        }

        // still here? check for host based
        $this->hostbased = true;
        $possible = $this->getAnimalNamesForHost($_SERVER['HTTP_HOST']);
        foreach($possible as $animal) {
            if(is_dir("$farmdir/$animal/conf/")) {
                $this->animal = $animal;
                return;
            }
        }

        // no hit
        $this->notfound = true;
        return;
    }

    /**
     * Return a list of possible animal names for the given host
     *
     * @param string $host the HTTP_HOST header
     * @return array
     */
    protected function getAnimalNamesForHost($host) {
        $animals = array();
        $parts = explode('.', implode('.', explode(':', rtrim($host, '.'))));
        for($j = count($parts); $j > 0; $j--) {
            // strip from the end
            $animals[] = implode('.', array_slice($parts, 0, $j));
            // strip from the end without host part
            $animals[] = implode('.', array_slice($parts, 1, $j));
        }
        $animals = array_unique($animals);
        $animals = array_filter($animals);
        usort(
            $animals,
            // compare by length, then alphabet
            function ($a, $b) {
                $ret = strlen($b) - strlen($a);
                if($ret != 0) return $ret;
                return $a > $b;
            }
        );
        return $animals;
    }

    /**
     * This sets up the default farming config cascade
     */
    protected function setupCascade() {
        global $config_cascade;
        $config_cascade = array(
            'main' => array(
                'default' => array(DOKU_INC . 'conf/dokuwiki.php',),
                'local' => array(DOKU_CONF . 'local.php',),
                'protected' => array(DOKU_CONF . 'local.protected.php',),
            ),
            'acronyms' => array(
                'default' => array(DOKU_INC . 'conf/acronyms.conf',),
                'local' => array(DOKU_CONF . 'acronyms.local.conf',),
            ),
            'entities' => array(
                'default' => array(DOKU_INC . 'conf/entities.conf',),
                'local' => array(DOKU_CONF . 'entities.local.conf',),
            ),
            'interwiki' => array(
                'default' => array(DOKU_INC . 'conf/interwiki.conf',),
                'local' => array(DOKU_CONF . 'interwiki.local.conf',),
            ),
            'license' => array(
                'default' => array(DOKU_INC . 'conf/license.php',),
                'local' => array(DOKU_CONF . 'license.local.php',),
            ),
            'mediameta' => array(
                'default' => array(DOKU_INC . 'conf/mediameta.php',),
                'local' => array(DOKU_CONF . 'mediameta.local.php',),
            ),
            'mime' => array(
                'default' => array(DOKU_INC . 'conf/mime.conf',),
                'local' => array(DOKU_CONF . 'mime.local.conf',),
            ),
            'scheme' => array(
                'default' => array(DOKU_INC . 'conf/scheme.conf',),
                'local' => array(DOKU_CONF . 'scheme.local.conf',),
            ),
            'smileys' => array(
                'default' => array(DOKU_INC . 'conf/smileys.conf',),
                'local' => array(DOKU_CONF . 'smileys.local.conf',),
            ),
            'wordblock' => array(
                'default' => array(DOKU_INC . 'conf/wordblock.conf',),
                'local' => array(DOKU_CONF . 'wordblock.local.conf',),
            ),
            'acl' => array(
                'default' => DOKU_CONF . 'acl.auth.php',
            ),
            'plainauth.users' => array(
                'default' => DOKU_CONF . 'users.auth.php',
            ),
            'plugins' => array(
                'default' => array(DOKU_INC . 'conf/plugins.php',),
                'local' => array(DOKU_CONF . 'plugins.local.php',),
                'protected' => array(
                    DOKU_INC . 'conf/plugins.required.php',
                    DOKU_CONF . 'plugins.protected.php',
                ),
            ),
            'userstyle' => array(
                'screen' => array(DOKU_CONF . 'userstyle.css', DOKU_CONF . 'userstyle.less',),
                'print' => array(DOKU_CONF . 'userprint.css', DOKU_CONF . 'userprint.less',),
                'feed' => array(DOKU_CONF . 'userfeed.css', DOKU_CONF . 'userfeed.less',),
                'all' => array(DOKU_CONF . 'userall.css', DOKU_CONF . 'userall.less',),
            ),
            'userscript' => array(
                'default' => array(DOKU_CONF . 'userscript.js',),
            ),
        );
    }

    /**
     * This adds additional files to the config cascade based on the inheritence settings
     *
     * These are only added for animals, not the farmer
     */
    protected function adjustCascade() {
        // nothing to do when on the farmer:
        if(!$this->animal) return;

        global $config_cascade;
        foreach($this->config['inherit'] as $key => $val) {
            if(!$val) continue;

            // prepare what is to append or prepend
            $append = array();
            $prepend = array();
            if($key == 'main') {
                $append = array(
                    'default' => array(DOKU_INC . 'conf/local.php'),
                    'protected' => array(DOKU_INC . 'lib/plugins/farmer/includes/config.php')
                );
            } elseif($key == 'license') {
                $append = array('default' => array(DOKU_INC . 'conf/' . $key . '.local.php'));
            } elseif($key == 'userscript') {
                $prepend = array('default' => array(DOKU_INC . 'conf/userscript.js'));
            } elseif($key == 'userstyle') {
                $prepend = array(
                    'screen' => array(DOKU_INC . 'conf/userstyle.css', DOKU_INC . 'conf/userstyle.less',),
                    'print' => array(DOKU_INC . 'conf/userprint.css', DOKU_INC . 'conf/userprint.less',),
                    'feed' => array(DOKU_INC . 'conf/userfeed.css', DOKU_INC . 'conf/userfeed.less',),
                    'all' => array(DOKU_INC . 'conf/userall.css', DOKU_INC . 'conf/userall.less',),
                );
            } elseif($key == 'users') {
                $config_cascade['plainauth.users']['protected'] = DOKU_INC . 'conf/users.auth.php';
            } elseif($key == 'plugins') {
                $append = array('default' => array(DOKU_INC . 'conf/plugins.local.php'));
            } else {
                $append = array('default' => array(DOKU_INC . 'conf/' . $key . '.local.conf'));
            }

            // add to cascade
            foreach($prepend as $section => $data) {
                $config_cascade[$key][$section] = array_merge($data, $config_cascade[$key][$section]);
            }
            foreach($append as $section => $data) {
                $config_cascade[$key][$section] = array_merge($config_cascade[$key][$section], $data);
            }
        }

        // add plugin overrides
        $config_cascade['plugins']['protected'][] = DOKU_INC . 'lib/plugins/farmer/includes/plugins.php';
    }

    /**
     * Loads the farm config
     */
    protected function loadConfig() {
        $ini = DOKU_INC . 'conf/farm.ini';
        if(!file_exists($ini)) return;
        $config = parse_ini_file($ini, true);
        foreach(array_keys($this->config) as $section) {
            if(isset($config[$section])) {
                $this->config[$section] = array_merge(
                    $this->config[$section],
                    $config[$section]
                );
            }
        }

        $this->config['base']['farmdir'] = trim($this->config['base']['farmdir']);
        $this->config['base']['farmhost'] = strtolower(trim($this->config['base']['farmhost']));
    }

}

// initialize it globally
if(!defined('DOKU_UNITTEST')) {
    global $FARMCORE;
    $FARMCORE = new DokuWikiFarmCore();
}
