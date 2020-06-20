<?php

namespace Nipwaayoni\Tests;

use Nipwaayoni\Exception\Helper\UnsupportedConfigurationValueException;
use Nipwaayoni\Config;

/**
 * Test Case for @see \Nipwaayoni\Config
 */
final class ConfigTest extends TestCase
{

  /**
   * @covers \Nipwaayoni\Config::__construct
   * @covers \Nipwaayoni\Agent::getConfig
   * @covers \Nipwaayoni\Config::getDefaultConfig
   * @covers \Nipwaayoni\Config::asArray
   */
    public function testControlDefaultConfig()
    {
        $appName = sprintf('app_name_%d', rand(10, 99));
        $config = (new Config([ 'appName' => $appName, 'active' => false, ]))->asArray();

        $this->assertArrayHasKey('appName', $config);
        $this->assertArrayHasKey('secretToken', $config);
        $this->assertArrayHasKey('serverUrl', $config);
        $this->assertArrayHasKey('hostname', $config);
        $this->assertArrayHasKey('active', $config);
        $this->assertArrayHasKey('timeout', $config);
        $this->assertArrayHasKey('appVersion', $config);
        $this->assertArrayHasKey('environment', $config);
        $this->assertArrayHasKey('backtraceLimit', $config);

        $this->assertEquals($config['appName'], $appName);
        $this->assertNull($config['secretToken']);
        $this->assertEquals($config['serverUrl'], 'http://127.0.0.1:8200');
        $this->assertEquals($config['hostname'], gethostname());
        $this->assertFalse($config['active']);
        $this->assertEquals($config['timeout'], 10);
        $this->assertEquals($config['environment'], 'development');
        $this->assertEquals($config['backtraceLimit'], 0);
    }

    /**
     * @depends testControlDefaultConfig
     *
     * @covers \Nipwaayoni\Config::__construct
     * @covers \Nipwaayoni\Agent::getConfig
     * @covers \Nipwaayoni\Config::getDefaultConfig
     * @covers \Nipwaayoni\Config::asArray
     */
    public function testControlInjectedConfig()
    {
        $init = [
            'appName'       => sprintf('app_name_%d', rand(10, 99)),
            'secretToken'   => hash('tiger128,3', time()),
            'serverUrl'     => sprintf('https://node%d.domain.tld:%d', rand(10, 99), rand(1000, 9999)),
            'appVersion'    => sprintf('%d.%d.42', rand(0, 3), rand(0, 10)),
            'frameworkName' => uniqid(),
            'timeout'       => rand(10, 20),
            'hostname'      => sprintf('host_%d', rand(0, 9)),
            'active'        => false,
        ];

        $config = (new Config($init))->asArray();

        foreach ($init as $key => $value) {
            $this->assertEquals($config[$key], $init[$key], 'key: ' . $key);
        }
    }

    /**
     * @depends testControlInjectedConfig
     *
     * @covers \Nipwaayoni\Config::__construct
     * @covers \Nipwaayoni\Agent::getConfig
     * @covers \Nipwaayoni\Config::getDefaultConfig
     * @covers \Nipwaayoni\Config::get
     */
    public function testGetConfig()
    {
        $init = [
            'appName' => sprintf('app_name_%d', rand(10, 99)),
            'active'  => false,
        ];

        $config = new Config($init);

        $this->assertEquals($config->get('appName'), $init['appName']);
    }

    /**
     * @depends testControlDefaultConfig
     *
     * @covers \Nipwaayoni\Config::__construct
     * @covers \Nipwaayoni\Agent::getConfig
     * @covers \Nipwaayoni\Config::getDefaultConfig
     * @covers \Nipwaayoni\Config::asArray
     */
    public function testTrimElasticServerUrl()
    {
        $init = [
            'serverUrl' => 'http://foo.bar/',
            'appName' => sprintf('app_name_%d', rand(10, 99)),
            'active'  => false,
        ];

        $config = (new Config($init))->asArray();

        foreach ($init as $key => $value) {
            if ('serverUrl' === $key) {
                $this->assertEquals('http://foo.bar', $config[$key]);
            } else {
                $this->assertEquals($config[$key], $init[$key], 'key: ' . $key);
            }
        }
    }

    /**
     * @throws UnsupportedConfigurationValueException
     * @throws \Nipwaayoni\Exception\MissingAppNameException
     *
     * @dataProvider unsupportedConfigOptions
     */
    public function testThrowsExceptionIfUnsupportedOptionIsIncluded(string $option): void
    {
        $this->expectException(UnsupportedConfigurationValueException::class);

        new Config([
            'appName' => 'Test',
            $option => ['name' => 'test'],
        ]);
    }

    public function unsupportedConfigOptions(): array
    {
        return [
            'environment' => ['env'],
            'cookies' => ['cookies'],
            'http client' => ['httpClient'],
        ];
    }

    public function testLoadsConfigurationFromFileInCurrentDirectory(): void
    {
        putenv('APP_NAME=Test Application');

        $config = new Config();

        $this->assertEquals('Test Application', $config->get('appName'));
    }
}
