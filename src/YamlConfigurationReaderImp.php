<?php

declare(strict_types=1);

namespace Phaba\Configuration;

use Phaba\Configuration\Exception\InvalidElementException;
use Phaba\Configuration\Exception\NotFoundFileException;
use Phaba\Configuration\Exception\NotFoundParameterException;
use Symfony\Component\Yaml\Yaml;

/**
 * Configuration reader from .yaml configuration file.
 *
 * @package Phaba\Configuration
 */
class YamlConfigurationReaderImp implements ConfigurationReader
{
    /**
     * @var string
     */
    private $configPath;

    /**
     * @var array
     */
    private $currentConfig;

    /**
     * @var YamlConfigurationReaderImp
     */
    private static $instance = null;

    private function __construct(string $configurationPath)
    {
        $this->configPath = $configurationPath;
        $this->currentConfig = $this->getCurrentConfigurationArray();
    }

    public static function getInstance(string $configurationPath = null): YamlConfigurationReaderImp
    {
        if (self::$instance == null) {
            self::$instance = new self($configurationPath);
        }

        return self::$instance;
    }

    /**
     * Get current entire configuration data (common & environment configuration data).
     *
     * @return array
     */
    private function getCurrentConfigurationArray(): array
    {
        $commonConfig = $this->getConfigurationData($this->configPath.'/config.yaml');
        $environmentConfig = [];
        if (isset($GLOBALS['env'])) {
            $environmentConfig = $this->getConfigurationData($this->configPath.'/config_'.$GLOBALS['env'].'.yaml');
        }
        $currentConfiguration = array_merge($commonConfig, $environmentConfig);
        $this->replaceValuesWithParameters($currentConfiguration);
        return $currentConfiguration;
    }
    /**
     * Get data from an specified configuration file.
     *
     * @param string $filePath Specified configuration file path
     * @return array Specified configuration file data
     */
    private function getConfigurationData(string $filePath): array
    {
        $configData = [];
        if (file_exists($filePath)) {
            $configData = Yaml::parse(file_get_contents($filePath));
            $configData = array_merge($configData, $this->getImportedData($configData));
            unset($configData['import']);
        }
        return $configData;
    }
    /**
     * Get imported data for an specified configuration data.
     *
     * @param array $configData Configuration data which imported data will be gotten
     * @return array Imported configuration data
     * @throws NotFoundFileException
     */
    private function getImportedData(array $configData): array
    {
        $importedData = [];
        if (array_key_exists('import', $configData)) {
            foreach ($configData['import'] as $index => $file) {
                if (file_exists($this->configPath.'/'.$file['resource'])) {
                    $resourceData = Yaml::parse(file_get_contents($this->configPath.'/'.$file['resource']));
                    $importedData = array_merge($importedData, $this->getImportedData($resourceData), $resourceData);
                } else {
                    throw new NotFoundFileException('File '.$file['resource'].' for importing is not existing');
                }
            }
        }
        return $importedData;
    }
    /**
     * Replace configuration element value with its corresponding parameter value
     *
     * @param array $replacingData Configuration data for being replaced
     * @param array|null $configData Entire configuration data. This data must contain parameters data=>value.
     * @throws NotFoundParameterException
     */
    private function replaceValuesWithParameters(array &$replacingData, array $configData = null)
    {
        //TODO: Refactor for doing function more readable
        $configData = (null === $configData)? $replacingData:$configData;
        foreach ($replacingData as $element => &$data) {
            if (is_array($data)) {
                $this->replaceValuesWithParameters($data, $configData);
            } elseif (strpos(strval($data), '%') == 0 && strrpos(strval($data), '%') == strlen(strval($data)) - 1) {
                $parameter = substr($data, 1, strlen($data)-2);
                if (!array_key_exists('parameters', $configData)
                    || !array_key_exists($parameter, $configData['parameters'])) {
                    throw new Exception\NotFoundParameterException("Not Found parameter '$parameter'.");
                } else {
                    $replacingData[$element] = $configData['parameters'][$parameter];
                }
            }
        }
    }
    /**
     * Getting value of an specified configuration element.
     *
     * @throws InvalidElementException
     */
    public function getElement(string $name)
    {
        if (!array_key_exists($name, $this->currentConfig)) {
            throw new InvalidElementException("Invalid $name Element in configuration");
        }
        return $this->currentConfig[$name];
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}
