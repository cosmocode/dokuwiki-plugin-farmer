<?php

/**
 * Tests for the validation functionality of the farmer plugin
 *
 * @group plugin_farmer
 * @group plugins
 */
class helper_plugin_farmer_test extends DokuWikiTest {

    protected $pluginsEnabled = array('farmer',);

    public function validationProvider() {
        return array(
            array('ant', true),
            array('ant.lion', true),
            array('ant.lion.cow', true),
            array('ant-lion', true),
            array('ant-lion.cow', true),
            array('4ant', true),
            array('ant4', true),
            array('ant44lion', true),
            array('44', true),

            array('ant.', false),
            array('.ant', false),
            array('ant-', false),
            array('-ant', false),
            array('ant--lion', false),
            array('ant..lion', false),
            array('ant.-lion', false),
            array('ant/lion', false),
            array('!ant', false),
            array('ant lion', false),
        );
    }

    /**
     * @dataProvider validationProvider
     * @param $input
     * @param $expect
     */
    public function test_validateAnimalName($input, $expect) {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $this->assertEquals($expect, $helper->validateAnimalName($input));
    }

    public function test_isInPath() {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');

        $this->assertTrue($helper->isInPath('/var/www/foo', '/var/www'));
        $this->assertFalse($helper->isInPath('/var/www/../foo', '/var/www'));

        // same dir should return false, too
        $this->assertFalse($helper->isInPath('/var/www/foo', '/var/www/foo'));
        $this->assertFalse($helper->isInPath('/var/www/foo/', '/var/www/foo'));
        $this->assertFalse($helper->isInPath('/var/www/foo/bar/../', '/var/www/foo'));

        // https://github.com/cosmocode/dokuwiki-plugin-farmer/issues/30
        $this->assertFalse($helper->isInPath('/var/lib/dokuwiki.animals', '/var/lib/dokuwiki'));
    }
}
