<?php


/**
 * Tests for the validation functionality of the farmer plugin
 *
 * @group plugin_farmer
 * @group plugins
 */
class getUserLine_plugin_farmer_test extends DokuWikiTest {

    protected $pluginsEnabled = array('farmer',);
    private $usersfile;

    public function setUp() {
        parent::setUp();
        $this->usersfile = DOKU_CONF . 'users.auth.php';
        copy($this->usersfile, $this->usersfile . "org");
        unlink($this->usersfile);
    }

    public function tearDown() {
        parent::tearDown();
        unlink($this->usersfile);
        copy($this->usersfile . "org", $this->usersfile);
        unlink($this->usersfile . "org");
    }


    public function test_getUserLine_oneUser () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $usersfileData = "# users.auth.php
# <?php exit()?>
# Don't modify the lines above
#
# Userfile
#
# Format:
#
# user:MD5password:Real Name:email:groups,comma,seperated
#
# testuser : testpass
testuser:179ad45c6ce2cb97cf1029e212046e81:Arthur Dent:arthur@example.com:\n";
        file_put_contents($this->usersfile,$usersfileData);

        $expected_result = 'testuser:179ad45c6ce2cb97cf1029e212046e81:Arthur Dent:arthur@example.com:' . "\n";
        $actual_result = $helper->getUserLine('testuser');

        $this->assertSame($expected_result, $actual_result);
    }

    public function test_getUserLine_manyUser () {
        /** @var helper_plugin_farmer $helper */
        $helper = plugin_load('helper', 'farmer');
        $usersfileData = "# users.auth.php
# <?php exit()?>
# Don't modify the lines above
#
# Userfile
#
# Format:
#
# user:MD5password:Real Name:email:groups,comma,seperated
#
# testuser : testpass
1testuser:179ad45c6ce43897cf1029e212046e81:Arthur Dent:brthur@example.com:admin
testuser:179ad45c6ce2cb97cf1029e212046e81:Arthur Dent:arthur@example.com:
2testuser:179ad45c6ce2cb97cf10214712046e81:Arthur inDent:crthur@example.com:admin\n";
        file_put_contents($this->usersfile,$usersfileData);

        $expected_result = 'testuser:179ad45c6ce2cb97cf1029e212046e81:Arthur Dent:arthur@example.com:' . "\n";
        $actual_result = $helper->getUserLine('testuser');

        $this->assertSame($expected_result, $actual_result);
    }
}
