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
    public const DEFAULT_CONFIG_FILE = 'elastic-apm.php';
    /**
     * Config Set
     *
     * @var array
     */
    private $config;

    private static $configSearchPaths = ['.'];

    public static function addSearchPath(string $path): void
    {
        // rtrim '/' from path
        // ensure $path exists
        // array_unshift path onto search paths
        array_unshift(self::$configSearchPaths, $path);
    }

    public static function resetSearchPath(): void
    {
        self::$configSearchPaths = ['.'];
    }

    /**
     * @param array $config
     * @param string|null $configFilePath
     * @throws MissingAppNameException
     * @throws UnsupportedConfigurationValueException
     */
    public function __construct(array $config = [], string $configFilePath = null)
    {
        $this->config = array_merge($this->getDefaultConfig(), $this->loadConfig($configFilePath), $config);

        if (isset($this->config['appName']) === false) {
            throw new MissingAppNameException();
        }

        foreach (['httpClient', 'env', 'cookies'] as $removedKey) {
            if (array_key_exists($removedKey, $this->config)) {
                throw new UnsupportedConfigurationValueException($removedKey);
            }
        }

        $this->config['serverUrl'] = rtrim($this->config['serverUrl'], '/');
    }

    private function loadConfig(string $configFilePath = null): array
    {
        if (null !== $configFilePath) {
            return $this->loadConfigFromInputFile($configFilePath);
        }

        if (empty($configFile = $this->findConfigFile())) {
            return [];
        }

        return $this->loadConfigFromFile($configFile);
    }

    private function loadConfigFromInputFile(string $configFilePath): array
    {
        if (!file_exists($configFilePath)) {
            throw new ConfigurationFileNotFoundException($configFilePath);
        }

        return $this->loadConfigFromFile($configFilePath);
    }

    private function findConfigFile(): ?string
    {
        foreach (self::$configSearchPaths as $searchPath) {
            $candidateFile = $searchPath . DIRECTORY_SEPARATOR . self::DEFAULT_CONFIG_FILE;
            if (!file_exists($candidateFile)) {
                continue;
            }

            return $candidateFile;
        }

        return null;
    }

    private function loadConfigFromFile(string $configFilePath): array
    {
        try {
            $fileConfig = require($configFilePath);
        } catch (\ParseError $e) {
            throw new ConfigurationFileNotValidException($e->getMessage());
        }

        if (!is_array($fileConfig)) {
            throw new ConfigurationFileNotValidException(sprintf('Specified configuration file (%s) did not return an array.', $configFilePath));
        }

        return $fileConfig;
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
