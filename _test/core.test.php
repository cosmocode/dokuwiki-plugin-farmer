<?php

namespace plugin\farmer\test;

require_once(__DIR__ . '/../DokuWikiFarmCore.php');

class DokuWikiFarmCore extends \DokuWikiFarmCore
{
    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct()
    {
        $this->loadConfig();
        // we do not intitialize anyting else here because it's already too late and DOKU_INC is already set
    }


    public function getAnimalNamesForHost($host)
    {
        return parent::getAnimalNamesForHost($host);
    }
}


/**
 * @group plugin_farmer
 * @group plugins
 */
class core_plugin_farmer_test extends \DokuWikiTest
{

    protected $pluginsEnabled = ['farmer'];


    public function test_hostsAnimals()
    {
        $core = new DokuWikiFarmCore();

        $input = 'www.foobar.example.com:8000';
        $expect = [
            'www.foobar.example.com.8000',
            'foobar.example.com.8000',
            'www.foobar.example.com',
            'foobar.example.com',
            'www.foobar.example',
            'foobar.example',
            'www.foobar',
            'foobar',
            'www',
        ];

        $this->assertEquals($expect, $core->getAnimalNamesForHost($input));
    }
}
