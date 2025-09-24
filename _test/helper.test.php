<?php

/**
 * Tests for the validation functionality of the farmer plugin
 *
 * @group plugin_farmer
 * @group plugins
 */
class helper_plugin_farmer_test extends DokuWikiTest
{

    protected $pluginsEnabled = ['farmer',];

    public function validationProvider()
    {
        return [
            ['ant', true],
            ['ant.lion', true],
            ['ant.lion.cow', true],
            ['ant-lion', true],
            ['ant-lion.cow', true],
            ['4ant', true],
            ['ant4', true],
            ['ant44lion', true],
            ['44', true],

            ['aNt', false],
            ['anT.Lion', false],
            ['ant.Lion.cow', false],
            ['ant-Lion', false],
            ['ant-Lion.cow', false],
            ['4aNt', false],
            ['aNt4', false],
            ['ant44Lion', false],

            ['ant.', false],
            ['.ant', false],
            ['ant-', false],
            ['-ant', false],
            ['ant--lion', false],
            ['ant..lion', false],
            ['ant.-lion', false],
            ['ant/lion', false],
            ['!ant', false],
            ['ant lion', false],
        ];
    }

    /**
     * @dataProvider validationProvider
     * @param $input
     * @param $expect
     */
    public function test_validateAnimalName($input, $expect)
    {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $this->assertEquals($expect, $helper->validateAnimalName($input));
    }

    public function test_isInPath()
    {
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
