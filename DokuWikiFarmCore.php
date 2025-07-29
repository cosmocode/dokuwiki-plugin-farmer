<?php

// phpcs:disable PSR1.Files.SideEffects

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
class DokuWikiFarmCore
{
    /**
     * @var array The default config - changed by loadConfig
     */
    protected $config = [
        'base' => [
            'farmdir' => '',
            'farmhost' => '',
            'basedomain' => ''
        ],
        'notfound' => [
            'show' => 'farmer',
            'url' => ''
        ],
        'inherit' => [
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
            'userscript' => 0,
            'styleini' => 0
        ]
    ];

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
    public function __construct()
    {
        $this->loadConfig();
        if ($this->config['base']['farmdir'] === '') return; // farm setup not complete
        $this->config['base']['farmdir'] = rtrim($this->config['base']['farmdir'], '/') . '/'; // trailing slash always
        define('DOKU_FARMDIR', $this->config['base']['farmdir']);

        // animal?
        $this->detectAnimal();

        // setup defines
        define('DOKU_FARM_ANIMAL', $this->animal);
        if ($this->animal) {
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
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return false|string
     */
    public function getAnimal()
    {
        return $this->animal;
    }

    /**
     * @return boolean
     */
    public function isHostbased()
    {
        return $this->hostbased;
    }

    /**
     * @return boolean
     */
    public function wasNotfound()
    {
        return $this->notfound;
    }

    /**
     * @return string
     */
    public function getAnimalDataDir()
    {
        return DOKU_FARMDIR . $this->getAnimal() . '/data/';
    }

    /**
     * @return string
     */
    public function getAnimalBaseDir()
    {
        if ($this->isHostbased()) return '/';
        return getBaseURL() . '!' . $this->getAnimal();
    }

    /**
     * Set the animal
     *
     * Checks if the animal exists and is a valid directory name.
     *
     * @param mixed $animal the animal name
     * @return bool returns true if the animal was set successfully, false otherwise
     */
    protected function setAnimal($animal)
    {
        $farmdir = $this->config['base']['farmdir'];

        // invalid animal stuff is always a not found
        if (!is_string($animal) || strpbrk($animal, '\\/') !== false) {
            $this->notfound = true;
            return false;
        }
        $animal = strtolower($animal);

        // check if animal exists
        if (is_dir("$farmdir/$animal/conf")) {
            $this->animal = $animal;
            $this->notfound = false;
            return true;
        } else {
            $this->notfound = true;
            return false;
        }
    }

    /**
     * Detect the animal from the given query string
     *
     * This removes the animal parameter from the given string and sets the animal
     *
     * @param string $queryString The query string to extract the animal from, will be modified
     * @return bool true if the animal was set successfully, false otherwise
     */
    protected function detectAnimalFromQueryString(string &$queryString): bool
    {
        $params = [];
        parse_str($queryString, $params);
        if (!isset($params['animal'])) return false;
        $animal = $params['animal'];
        unset($params['animal']);
        $queryString = http_build_query($params);

        $this->hostbased = false;
        return $this->setAnimal($animal);
    }

    /**
     * Detect the animal from the bang path
     *
     * This is used to detect the animal from a bang path like `/!animal/my:page` or '/dokuwiki/!animal/my:page'.
     *
     * @param string $path The bang path to extract the animal from
     * @return bool true if the animal was set successfully, false otherwise
     */
    protected function detectAnimalFromBangPath(string $path): bool
    {
        $bangregex = '#^(/(?:[^/]*/)*)!([^/]+)/#';
        if (preg_match($bangregex, $path, $matches)) {
            // found a bang path
            $animal = $matches[2];

            $this->hostbased = false;
            return $this->setAnimal($animal);
        }
        return false;
    }

    /**
     * Detect the animal from the host name
     *
     * @param string $host The hostname
     * @return bool true if the animal was set successfully, false otherwise
     */
    protected function detectAnimalFromHostName(string $host): bool
    {
        $possible = $this->getAnimalNamesForHost($host);
        foreach ($possible as $animal) {
            if ($this->setAnimal($animal)) {
                $this->hostbased = true;
                return true;
            }
        }
        return false;
    }

    /**
     * Detect the current animal
     *
     * Sets internal members $animal, $notfound and $hostbased
     *
     * This borrows form DokuWiki's inc/farm.php but does not support a default conf dir
     *
     * @params string|null $sapi the SAPI to use. Only changed during testing
     */
    protected function detectAnimal($sapi = null)
    {
        $sapi = $sapi ?: PHP_SAPI;
        $farmhost = $this->config['base']['farmhost'];

        if ('cli' == $sapi) {
            if (!isset($_SERVER['animal'])) return; // no animal parameter given - we're the farmer

            if (preg_match('#^https?://#i', $_SERVER['animal'])) {
                // CLI animal parameter is a URL
                $urlparts = parse_url($_SERVER['animal']);
                $urlparts['query'] ??= '';

                // detect the animal from the URL
                $this->detectAnimalFromQueryString($urlparts['query']) ||
                $this->detectAnimalFromBangPath($urlparts['path']) ||
                $this->detectAnimalFromHostName($urlparts['host']);

                // fake baseurl etc.
                $this->injectServerEnvironment($urlparts);
            } else {
                // CLI animal parameter is just a name
                $this->setAnimal(strtolower($_SERVER['animal']));
            }
        } else {
            // an animal url parameter has been set
            if (isset($_GET['animal'])) {
                $this->detectAnimalFromQueryString($_SERVER['QUERY_STRING']);
                unset($_GET['animal']);
                return;
            }

            // no host - no host based setup. if we're still here then it's the farmer
            if (empty($_SERVER['HTTP_HOST'])) return;

            // is this the farmer?
            if (strtolower($_SERVER['HTTP_HOST']) == $farmhost) {
                return;
            }

            // we're in host based mode now
            $this->hostbased = true;

            // we should get an animal now
            if (!$this->detectAnimalFromHostName($_SERVER['HTTP_HOST'])) {
                $this->notfound = true;
            }
        }
    }

    /**
     * Create Server environment variables for the current animal
     *
     * This is called when the animal is initialized on the command line using a full URL.
     * Since the initialization is running before any configuration is loaded, we instead
     * set the $_SERVER variables that will later be used to autodetect the base URL. This
     * way a manually set base URL will still take precedence.
     *
     * @param array $urlparts A parse_url() result array
     * @return void
     * @see is_ssl()
     * @see getBaseURL()
     */
    protected function injectServerEnvironment(array $urlparts)
    {
        // prepare data for DOKU_REL
        $path = $urlparts['path'] ?? '/';
        if (($bangpos = strpos($path, '!')) !== false) {
            // strip from the bang path
            $path = substr($path, 0, $bangpos);
        }
        if (!str_ends_with($path, '.php')) {
            // make sure we have a script name
            $path = rtrim($path, '/') . '/doku.php';
        }
        $_SERVER['SCRIPT_NAME'] = $path;

        // prepare data for is_ssl()
        if (($urlparts['scheme'] ?? '') === 'https') {
            $_SERVER['HTTPS'] = 'on';
        } else {
            $_SERVER['HTTPS'] = 'off';
        }

        // prepare data for DOKU_URL
        $_SERVER['HTTP_HOST'] = $urlparts['host'] ?? '';
        if (isset($urlparts['port'])) {
            $_SERVER['HTTP_HOST'] .= ':' . $urlparts['port'];
        }
    }

    /**
     * Return a list of possible animal names for the given host
     *
     * @param string $host the HTTP_HOST header
     * @return array
     */
    protected function getAnimalNamesForHost($host)
    {
        $animals = [];
        $parts = explode('.', implode('.', explode(':', rtrim($host, '.'))));
        for ($j = count($parts); $j > 0; $j--) {
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
                if ($ret != 0) return $ret;
                return $a <=> $b;
            }
        );
        return $animals;
    }

    /**
     * This sets up the default farming config cascade
     */
    protected function setupCascade()
    {
        global $config_cascade;
        $config_cascade = [
            'main' => [
                'default' => [DOKU_INC . 'conf/dokuwiki.php'],
                'local' => [DOKU_CONF . 'local.php'],
                'protected' => [DOKU_CONF . 'local.protected.php']
            ],
            'acronyms' => [
                'default' => [DOKU_INC . 'conf/acronyms.conf'],
                'local' => [DOKU_CONF . 'acronyms.local.conf']
            ],
            'entities' => [
                'default' => [DOKU_INC . 'conf/entities.conf'],
                'local' => [DOKU_CONF . 'entities.local.conf']
            ],
            'interwiki' => [
                'default' => [DOKU_INC . 'conf/interwiki.conf'],
                'local' => [DOKU_CONF . 'interwiki.local.conf']
            ],
            'license' => [
                'default' => [DOKU_INC . 'conf/license.php'],
                'local' => [DOKU_CONF . 'license.local.php']
            ],
            'manifest' => [
                'default' => [DOKU_INC . 'conf/manifest.json'],
                'local' => [DOKU_CONF . 'manifest.local.json']
            ],
            'mediameta' => [
                'default' => [DOKU_INC . 'conf/mediameta.php'],
                'local' => [DOKU_CONF . 'mediameta.local.php']
            ],
            'mime' => [
                'default' => [DOKU_INC . 'conf/mime.conf'],
                'local' => [DOKU_CONF . 'mime.local.conf']
            ],
            'scheme' => [
                'default' => [DOKU_INC . 'conf/scheme.conf'],
                'local' => [DOKU_CONF . 'scheme.local.conf']
            ],
            'smileys' => [
                'default' => [DOKU_INC . 'conf/smileys.conf'],
                'local' => [DOKU_CONF . 'smileys.local.conf']
            ],
            'wordblock' => [
                'default' => [DOKU_INC . 'conf/wordblock.conf'],
                'local' => [DOKU_CONF . 'wordblock.local.conf']
            ],
            'acl' => [
                'default' => DOKU_CONF . 'acl.auth.php'
            ],
            'plainauth.users' => [
                'default' => DOKU_CONF . 'users.auth.php'
            ],
            'plugins' => [
                'default' => [DOKU_INC . 'conf/plugins.php'],
                'local' => [DOKU_CONF . 'plugins.local.php'],
                'protected' => [
                    DOKU_INC . 'conf/plugins.required.php',
                    DOKU_CONF . 'plugins.protected.php'
                ]
            ],
            'userstyle' => [
                'screen' => [
                    DOKU_CONF . 'userstyle.css',
                    DOKU_CONF . 'userstyle.less'
                ],
                'print' => [
                    DOKU_CONF . 'userprint.css',
                    DOKU_CONF . 'userprint.less'
                ],
                'feed' => [
                    DOKU_CONF . 'userfeed.css',
                    DOKU_CONF . 'userfeed.less'
                ],
                'all' => [
                    DOKU_CONF . 'userall.css',
                    DOKU_CONF . 'userall.less'
                ]
            ],
            'userscript' => [
                'default' => [DOKU_CONF . 'userscript.js']
            ],
            'styleini' => [
                'default' => [DOKU_INC . 'lib/tpl/%TEMPLATE%/' . 'style.ini'],
                'local' => [DOKU_CONF . 'tpl/%TEMPLATE%/' . 'style.ini']
            ]
        ];
    }

    /**
     * This adds additional files to the config cascade based on the inheritence settings
     *
     * These are only added for animals, not the farmer
     */
    protected function adjustCascade()
    {
        // nothing to do when on the farmer:
        if (!$this->animal) return;

        global $config_cascade;
        foreach ($this->config['inherit'] as $key => $val) {
            if (!$val) continue;

            // prepare what is to append or prepend
            $append = [];
            $prepend = [];
            if ($key == 'main') {
                $prepend = [
                    'protected' => [DOKU_INC . 'conf/local.protected.php']
                ];
                $append = [
                    'default' => [DOKU_INC . 'conf/local.php'],
                    'protected' => [DOKU_INC . 'lib/plugins/farmer/includes/config.php']
                ];
            } elseif ($key == 'license') {
                $append = [
                    'default' => [DOKU_INC . 'conf/' . $key . '.local.php']
                ];
            } elseif ($key == 'userscript') {
                $prepend = [
                    'default' => [DOKU_INC . 'conf/userscript.js']
                ];
            } elseif ($key == 'userstyle') {
                $prepend = [
                    'screen' => [
                        DOKU_INC . 'conf/userstyle.css',
                        DOKU_INC . 'conf/userstyle.less'
                    ],
                    'print' => [
                        DOKU_INC . 'conf/userprint.css',
                        DOKU_INC . 'conf/userprint.less'
                    ],
                    'feed' => [
                        DOKU_INC . 'conf/userfeed.css',
                        DOKU_INC . 'conf/userfeed.less'
                    ],
                    'all' => [
                        DOKU_INC . 'conf/userall.css',
                        DOKU_INC . 'conf/userall.less'
                    ]
                ];
            } elseif ($key == 'styleini') {
                $append = [
                    'local' => [DOKU_INC . 'conf/tpl/%TEMPLATE%/style.ini']
                ];
            } elseif ($key == 'users') {
                $config_cascade['plainauth.users']['protected'] = DOKU_INC . 'conf/users.auth.php';
            } elseif ($key == 'plugins') {
                $prepend = [
                    'protected' => [DOKU_INC . 'conf/local.protected.php']
                ];
                $append = [
                    'default' => [DOKU_INC . 'conf/plugins.local.php']
                ];
            } else {
                $append = [
                    'default' => [DOKU_INC . 'conf/' . $key . '.local.conf']
                ];
            }

            // add to cascade
            foreach ($prepend as $section => $data) {
                $config_cascade[$key][$section] = array_merge($data, $config_cascade[$key][$section]);
            }
            foreach ($append as $section => $data) {
                $config_cascade[$key][$section] = array_merge($config_cascade[$key][$section], $data);
            }
        }

        // add plugin overrides
        $config_cascade['plugins']['protected'][] = DOKU_INC . 'lib/plugins/farmer/includes/plugins.php';
    }

    /**
     * Loads the farm config
     */
    protected function loadConfig()
    {
        $ini = DOKU_INC . 'conf/farm.ini';
        if (file_exists($ini)) {
            $config = parse_ini_file($ini, true);
            foreach (array_keys($this->config) as $section) {
                if (isset($config[$section])) {
                    $this->config[$section] = array_merge(
                        $this->config[$section],
                        $config[$section]
                    );
                }
            }
        }

        // farmdir setup can be done via environment
        if ($this->config['base']['farmdir'] === '' && isset($_ENV['DOKU_FARMDIR'])) {
            $this->config['base']['farmdir'] = $_ENV['DOKU_FARMDIR'];
        }

        $this->config['base']['farmdir'] = trim($this->config['base']['farmdir']);
        $this->config['base']['farmhost'] = strtolower(trim($this->config['base']['farmhost']));
    }
}

// initialize it globally
if (!defined('DOKU_UNITTEST')) {
    global $FARMCORE;
    $FARMCORE = new DokuWikiFarmCore();
}
