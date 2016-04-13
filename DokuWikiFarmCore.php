<?php

class DokuWikiFarmCore {
    /**
     * @var array The default config - changed by loadConfig
     */
    protected $config = array (
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
            'userstyle' => 0,
            'userscript' => 0
        )
    );

    /**
     * DokuWikiFarmCore constructor.
     *
     * This initializes the whole farm
     */
    public function __construct() {
        $this->loadConfig();
    }

    /**
     * @return array the current farm configuration
     */
    public function getConfig() {
        return $this->config;
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
    }


}

// initialize it globally
global $FARMCORE;
$FARMCORE = new DokuWikiFarmCore();
