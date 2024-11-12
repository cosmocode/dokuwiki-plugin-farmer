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

    /**
     * Get the configured farm host
     *
     * @return string
     */
    public function getFarmhost(): string
    {
        return $this->helper->getConfig()['base']['farmhost'];
    }

    /**
     * Get the configured base domain of the farmer
     * This could be an empty string, then farmhost will be used to determine an animal url
     *
     * @return string
     */
    public function getBaseDomain(): string
    {
        return $this->helper->getConfig()['base']['basedomain'];
    }

    /**
     * Get a list of all animal names
     *
     * @return array
     */
    public function listAnimals(): array
    {
        return $this->helper->getAllAnimals();
    }

    /**
     * Get a list of all animal urls
     *
     * @return array
     */
    public function listAnimalUrls(): array
    {
        foreach($this->helper->getAllAnimals() as $animal) {
            $animalUrls[] = $this->helper->getAnimalURL($animal);
        }
        return $animalUrls;
    }

    /**
     * Get configuration details of farmer plugin enriched by list of animals
     *
     * @return array
     */
    public function getFarmerConfig(): array
    {
        $farmerConfig = $this->helper->getConfig();
        foreach($this->helper->getAllAnimals() as $index=>$animal) {
            $farmerConfig['animals'][$index]["name"] =$animal;
            $farmerConfig['animals'][$index]["url"] = $this->helper->getAnimalURL($animal);
        }
        return $farmerConfig;
    }
}
