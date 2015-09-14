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

    public function test_validateAnimalName_valid () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $testname = 'ant';
        $this->assertTrue($helper->validateAnimalName($testname), $testname);
    }

    public function test_validateAnimalName_dot () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $testname = 'ant.';
        $this->assertFalse($helper->validateAnimalName($testname), $testname);
        $testname = '.ant';
        $this->assertFalse($helper->validateAnimalName($testname), $testname);
        $testname = 'ant.lion';
        $this->assertFalse($helper->validateAnimalName($testname), $testname);
    }

    public function test_validateAnimalName_minus () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $testname = 'ant-';
        $this->assertFalse($helper->validateAnimalName($testname), $testname);
        $testname = '-ant';
        $this->assertFalse($helper->validateAnimalName($testname), $testname);
        $testname = 'ant-lion';
        $this->assertTrue($helper->validateAnimalName($testname), $testname);
    }

    public function test_validateAnimalName_numbers () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $testname = 'ant4';
        $this->assertTrue($helper->validateAnimalName($testname), $testname);
        $testname = '4ant';
        $this->assertTrue($helper->validateAnimalName($testname), $testname);
        $testname = 'ant4lion';
        $this->assertTrue($helper->validateAnimalName($testname), $testname);
        $testname = '123';
        $this->assertTrue($helper->validateAnimalName($testname), $testname);
    }

    public function test_validateAnimalName_slash () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $testname = 'ant/';
        $this->assertFalse($helper->validateAnimalName($testname), $testname);
        $testname = '/ant';
        $this->assertFalse($helper->validateAnimalName($testname), $testname);
        $testname = 'ant/lion';
        $this->assertFalse($helper->validateAnimalName($testname), $testname);
    }
}
