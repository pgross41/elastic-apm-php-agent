<?php

namespace Nipwaayoni;

use Nipwaayoni\Exception\ConfigurationFileNotFoundException;
use Nipwaayoni\Exception\ConfigurationFileNotValidException;
use Nipwaayoni\Exception\Helper\UnsupportedConfigurationValueException;
use Nipwaayoni\Exception\MissingAppNameException;

/**
 *
 * Agent Config Store
 *
 */
class Config
{
    /**
     * Config Set
     *
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = [], string $configFilePath = null)
    {
        if (null !== $configFilePath && !file_exists($configFilePath)) {
            throw new ConfigurationFileNotFoundException($configFilePath);
        }

        $configFilePath = $configFilePath ?? 'elastic-apm.php';

        if (file_exists($configFilePath)) {
            try{
                $fileConfig = require($configFilePath);
            } catch (\ParseError $e) {
                throw new ConfigurationFileNotValidException($e->getMessage());
            }

            if (!is_array($fileConfig)) {
                throw new ConfigurationFileNotValidException(sprintf('Specified configuration file (%s) did not return an array.', $configFilePath));
            }

            $config = array_merge($fileConfig, $config);
        }

        if (isset($config['appName']) === false) {
            throw new MissingAppNameException();
        }

        foreach (['httpClient', 'env', 'cookies'] as $removedKey) {
            if (array_key_exists($removedKey, $config)) {
                throw new UnsupportedConfigurationValueException($removedKey);
            }
        }

        // Register Merged Config
        $this->config = array_merge($this->getDefaultConfig(), $config);
        $this->config['serverUrl'] = rtrim($this->config['serverUrl'], "/");
    }

    /**
     * Get Config Value
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed: value | null
     */
    public function get(string $key, $default = null)
    {
        return ($this->config[$key]) ?? $default;
    }

    /**
     * Get the all Config Set as array
     *
     * @return array
     */
    public function asArray(): array
    {
        return $this->config;
    }

    /**
     * Get the Default Config of the Agent
     *
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/55
     *
     * @return array
     */
    private function getDefaultConfig(): array
    {
        return [
            'serverUrl'      => 'http://127.0.0.1:8200',
            'secretToken'    => null,
            'hostname'       => gethostname(),
            'appVersion'     => '',
            'active'         => true,
            'timeout'        => 10,
            'environment'    => 'development',
            'backtraceLimit' => 0,
        ];
    }
}
