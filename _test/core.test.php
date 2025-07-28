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

    public function detectAnimal($sapi = null)
    {
        parent::detectAnimal($sapi);
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function getAnimal()
    {
        return $this->animal;
    }

    public function wasNotfound()
    {
        return $this->notfound;
    }

    public function isHostbased()
    {
        return $this->hostbased;
    }

    public function resetState()
    {
        $this->animal = false;
        $this->notfound = false;
        $this->hostbased = false;
    }

    public function injectServerEnvironment(array $urlparts)
    {
        parent::injectServerEnvironment($urlparts);
    }
}


/**
 * @group plugin_farmer
 * @group plugins
 */
class core_plugin_farmer_test extends \DokuWikiTest
{

    protected $pluginsEnabled = ['farmer'];


    /**
     * Test the getAnimalNamesForHost method
     */
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

    /**
     * Data provider for detectAnimal tests
     */
    public function detectAnimalProvider()
    {
        return [
            'animal via GET parameter - exists' => [
                'get_params' => ['animal' => 'testanimal'],
                'server_params' => [],
                'sapi' => 'apache2handler',
                'expected_animal' => 'testanimal',
                'expected_notfound' => false,
                'expected_hostbased' => false,
                'create_dirs' => ['/tmp/farm/testanimal/conf'],
            ],
            'animal via GET parameter - not found' => [
                'get_params' => ['animal' => 'nonexistent'],
                'server_params' => [],
                'sapi' => 'apache2handler',
                'expected_animal' => false,
                'expected_notfound' => true,
                'expected_hostbased' => false,
            ],
            'animal via GET parameter - invalid path' => [
                'get_params' => ['animal' => '../badpath'],
                'server_params' => [],
                'sapi' => 'apache2handler',
                'expected_animal' => false,
                'expected_notfound' => true,
                'expected_hostbased' => false,
            ],
            'farmer host' => [
                'get_params' => [],
                'server_params' => ['HTTP_HOST' => 'farm.example.com'],
                'sapi' => 'apache2handler',
                'expected_animal' => false,
                'expected_notfound' => false,
                'expected_hostbased' => false,
            ],
            'host-based animal - exists' => [
                'get_params' => [],
                'server_params' => ['HTTP_HOST' => 'sub.example.com'],
                'sapi' => 'apache2handler',
                'expected_animal' => 'sub.example.com',
                'expected_notfound' => false,
                'expected_hostbased' => true,
                'create_dirs' => ['/tmp/farm/sub.example.com/conf'],
            ],
            'host-based animal - not found' => [
                'get_params' => [],
                'server_params' => ['HTTP_HOST' => 'unknown.example.com'],
                'sapi' => 'apache2handler',
                'expected_animal' => false,
                'expected_notfound' => true,
                'expected_hostbased' => true,
            ],
            'CLI animal parameter - name only' => [
                'get_params' => [],
                'server_params' => ['animal' => 'clianimal'],
                'sapi' => 'cli',
                'expected_animal' => 'clianimal',
                'expected_notfound' => false,
                'expected_hostbased' => false,
                'create_dirs' => ['/tmp/farm/clianimal/conf'],
            ],
            'CLI animal parameter - URL with query' => [
                'get_params' => [],
                'server_params' => ['animal' => 'https://example.com/path?animal=urlanimal&other=param'],
                'sapi' => 'cli',
                'expected_animal' => 'urlanimal',
                'expected_notfound' => false,
                'expected_hostbased' => false,
                'create_dirs' => ['/tmp/farm/urlanimal/conf'],
            ],
            'CLI animal parameter - URL with bang path' => [
                'get_params' => [],
                'server_params' => ['animal' => 'https://example.com/!banganimal/page'],
                'sapi' => 'cli',
                'expected_animal' => 'banganimal',
                'expected_notfound' => false,
                'expected_hostbased' => false,
                'create_dirs' => ['/tmp/farm/banganimal/conf'],
            ],
            'CLI animal parameter - URL with bang path in subdir' => [
                'get_params' => [],
                'server_params' => ['animal' => 'https://example.com/dokuwiki/!banganimal/page'],
                'sapi' => 'cli',
                'expected_animal' => 'banganimal',
                'expected_notfound' => false,
                'expected_hostbased' => false,
                'create_dirs' => ['/tmp/farm/banganimal/conf'],
            ],
            'CLI animal parameter - URL with hostname' => [
                'get_params' => [],
                'server_params' => ['animal' => 'https://hostanimal.example.com/page'],
                'sapi' => 'cli',
                'expected_animal' => 'hostanimal.example.com',
                'expected_notfound' => false,
                'expected_hostbased' => true,
                'create_dirs' => ['/tmp/farm/hostanimal.example.com/conf'],
            ],
            'CLI no animal parameter' => [
                'get_params' => [],
                'server_params' => [],
                'sapi' => 'cli',
                'expected_animal' => false,
                'expected_notfound' => false,
                'expected_hostbased' => false,
            ],
            'CLI animal parameter - URL with bang and query' => [
                'get_params' => [],
                'server_params' => ['animal' => 'https://example.com/!bangquery/page?param=value'],
                'sapi' => 'cli',
                'expected_animal' => 'bangquery',
                'expected_notfound' => false,
                'expected_hostbased' => false,
                'create_dirs' => ['/tmp/farm/bangquery/conf'],
            ],
            'HTTP no host header' => [
                'get_params' => [],
                'server_params' => ['HTTP_HOST' => ''], // our test environment sets one
                'sapi' => 'apache2handler',
                'expected_animal' => false,
                'expected_notfound' => false,
                'expected_hostbased' => false,
            ],
        ];
    }

    /**
     * @dataProvider detectAnimalProvider
     * @param array $get_params GET parameters to set in $_GET
     * @param array $server_params SERVER parameters to set in $_SERVER
     * @param string $sapi SAPI to simulate
     * @param string|false $expected_animal Expected animal name or false
     * @param bool $expected_notfound Expected notfound state
     * @param bool $expected_hostbased Expected hostbased state
     * @param array $create_dirs Directories to create for the test
     */
    public function test_detectAnimal($get_params, $server_params, $sapi, $expected_animal, $expected_notfound, $expected_hostbased, $create_dirs = [])
    {
        // Create temporary directories if needed
        foreach ($create_dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        // Backup original values
        $original_get = $_GET;
        $original_server = $_SERVER;
        $original_query_string = $_SERVER['QUERY_STRING'] ?? '';

        try {
            // Set up test environment
            $_GET = array_merge($_GET, $get_params);
            $_SERVER = array_merge($_SERVER, $server_params);
            if (!empty($get_params)) {
                $_SERVER['QUERY_STRING'] = http_build_query($get_params);
            }

            $config = ['base' => ['farmdir' => '/tmp/farm', 'farmhost' => 'farm.example.com']];
            $core = new DokuWikiFarmCore();
            $core->setConfig($config);
            $core->resetState();
            $core->detectAnimal($sapi);

            $this->assertEquals($expected_animal, $core->getAnimal(), 'Animal detection failed');
            $this->assertEquals($expected_notfound, $core->wasNotfound(), 'Notfound state incorrect');
            $this->assertEquals($expected_hostbased, $core->isHostbased(), 'Hostbased state incorrect');

        } finally {
            // Restore original values
            $_GET = $original_get;
            $_SERVER = $original_server;
            $_SERVER['QUERY_STRING'] = $original_query_string;

            // Clean up created directories
            foreach ($create_dirs as $dir) {
                if (is_dir($dir)) {
                    rmdir($dir);
                    // Also remove parent directories if they're empty
                    $parent = dirname($dir);
                    while ($parent !== '/' && $parent !== '.' && is_dir($parent) && count(scandir($parent)) === 2) {
                        rmdir($parent);
                        $parent = dirname($parent);
                    }
                }
            }
        }
    }

    /**
     * Data provider for injectServerEnvironment tests
     */
    public function injectServerEnvironmentProvider()
    {
        return [
            'HTTPS URL with port' => [
                'url' => 'https://example.com:8443/dokuwiki/doku.php',
                'expected_baseurl' => 'https://example.com:8443',
                'expected_basedir' => '/dokuwiki/',
            ],
            'HTTP URL with default port' => [
                'url' => 'http://test.example.com/doku.php',
                'expected_baseurl' => 'http://test.example.com',
                'expected_basedir' => '/',
            ],
            'HTTPS URL with bang path' => [
                'url' => 'https://farm.example.com/!animal/doku.php',
                'expected_baseurl' => 'https://farm.example.com',
                'expected_basedir' => '/',
            ],
            'HTTP URL with subdirectory and bang path' => [
                'url' => 'http://wiki.example.com/dokuwiki/!testanimal/start',
                'expected_baseurl' => 'http://wiki.example.com',
                'expected_basedir' => '/dokuwiki/',
            ],
            'HTTPS URL with custom port and path' => [
                'url' => 'https://secure.example.com:9443/wiki',
                'expected_baseurl' => 'https://secure.example.com:9443',
                'expected_basedir' => '/wiki/',
            ],
            'HTTP URL with root path' => [
                'url' => 'http://simple.example.com/',
                'expected_baseurl' => 'http://simple.example.com',
                'expected_basedir' => '/',
            ],
            'HTTP URL with no path' => [
                'url' => 'http://simple.example.com',
                'expected_baseurl' => 'http://simple.example.com',
                'expected_basedir' => '/',
            ],
        ];
    }

    /**
     * @dataProvider injectServerEnvironmentProvider
     * @param string $url The URL to parse and inject
     * @param string $expected_baseurl Expected base URL after injection
     * @param string $expected_basedir Expected base directory after injection
     */
    public function test_injectServerEnvironment($url, $expected_baseurl, $expected_basedir)
    {
        // Clear relevant server variables
        unset($_SERVER['HTTPS'], $_SERVER['HTTP_HOST'], $_SERVER['SCRIPT_NAME']);

        $core = new DokuWikiFarmCore();
        $urlparts = parse_url($url);
        $core->injectServerEnvironment($urlparts);

        $base_dir = getBaseURL(false);
        $base_url = getBaseURL(true);

        // first check that the directory was detected correctly…
        $this->assertEquals($expected_basedir, $base_dir, 'Base directory does not match expected value');
        // …then check that the expected base URL plus the directory matches the absolute URL
        $this->assertEquals($expected_baseurl . $base_dir, $base_url, 'Absolute URL does not match expected value');


    }
}
