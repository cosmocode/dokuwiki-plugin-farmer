<?php
namespace plugin\struct\test;

require_once(__DIR__ . '/../DokuWikiFarmCore.php');

class DokuWikiFarmCore extends \DokuWikiFarmCore {
    public function getAnimalNamesForHost($host) {
        return parent::getAnimalNamesForHost($host);
    }
}


/**
 * @group plugin_farmer
 * @group plugins
 */
class core_plugin_farmer_test extends \DokuWikiTest {

    protected $pluginsEnabled = array('farmer');


    public function test_hostsAnimals() {
        $core = new DokuWikiFarmCore();

        $input = 'www.foobar.example.com:8000';
        $expect = array(
            'www.foobar.example.com.8000',
            'foobar.example.com.8000',
            'www.foobar.example.com',
            'foobar.example.com',
            'www.foobar.example',
            'foobar.example',
            'www.foobar',
            'foobar',
            'www',
        );

        $this->assertEquals($expect, $core->getAnimalNamesForHost($input));
    }
}
