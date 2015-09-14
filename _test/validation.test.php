<?php

/**
 * Tests for the validation functionality of the farmer plugin
 *
 * @group plugin_farmer
 * @group plugins
 */
class validation_plugin_farmer_test extends DokuWikiTest {

    protected $pluginsEnabled = array('farmer',);

    public function test_validateSubdomain_valid () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $testdomain = 'abc.def.gh';
        $this->assertTrue($helper->validateSubdomain($testdomain), $testdomain);
    }

    public function test_validateSubdomain_noDot () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $testdomain = 'abc';
        $this->assertFalse($helper->validateSubdomain($testdomain), $testdomain);
    }

    public function test_validateSubdomain_oneDot () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $testdomain = 'abc.de';
        $this->assertFalse($helper->validateSubdomain($testdomain), $testdomain);
    }

    public function test_validateSubdomain_threeDots () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $testdomain = 'abc.def.ghi.jk';
        $this->assertTrue($helper->validateSubdomain($testdomain), $testdomain);
    }
}
