<?php

use dokuwiki\Extension\RemotePlugin;

class remote_plugin_farmer extends RemotePlugin {
    /** @var helper_plugin_farmer hlp */
    protected $helper;

    /**
     * remote_plugin_struct constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->helper = plugin_load('helper', 'farmer');
    }

    public function getHostname() {
        return $this->helper->getConfig()['base']['farmhost'];
    }

    public function getBaseDomain() {
        return $this->helper->getConfig()['base']['basedomain'];
    }

    public function listAnimals() {
        return $this->helper->getAllAnimals();
    }

    public function listAnimalUrls() {
        foreach($this->helper->getAllAnimals() as $animal) {
            $animalUrls[] = $this->helper->getAnimalURL($animal);
        }
        return $animalUrls;
    }
}
