<?php

declare(strict_types=1);

namespace Phaba\Configuration;

use Phaba\Configuration\Exception\InvalidElementException;
use Phaba\Configuration\Exception\NotExistingFileException;
use Phaba\Configuration\Exception\NotFoundParameterException;
use Symfony\Component\Yaml\Yaml;

/**
 * Configuration reader from .yaml configuration file.
 *
 * @package Phaba\Configuration
 */
class YamlConfigurationImp implements Configuration
{
    /**
     * @var array
     */
    private $currentConfig;

    /**
     * @var string
     */
    private $configPath;

    public function __construct(string $configurationPath)
    {
        $this->configPath = $configurationPath;
        $this->currentConfig = $this->getCurrentConfigurationArray();
    }

    /**
     * Get current entire configuration data (common & environment configuration data).
     *
     * @return array
     */
    private function getCurrentConfigurationArray(): array
    {
        $commonConfig = $this->getConfigurationData($this->configPath.'/config.yaml');
        $environmentConfig = $this->getConfigurationData($this->configPath.'/config_'.ENVIRONMENT.'.yaml');
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
     * @throws NotExistingFileException
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
                    throw new NotExistingFileException('File '.$file['resource'].' for importing is not existing');
                }

            }
        }
        return $importedData;
    }

    private function replaceValuesWithParameters(array &$replacingData, array $configData = null)
    {
        $configData = (null === $configData)? $replacingData:$configData;
        foreach ($replacingData as $element => &$data) {
            if (is_array($data)) {
                $this->replaceValuesWithParameters($data, $configData);
            } elseif (strpos($data, '%') == 0 && strrpos($data, '%') == strlen($data) - 1) {
                $parameter = substr($data, 1, strlen($data)-2);
                if (!array_key_exists('parameters', $configData)
                    || !array_key_exists($parameter, $configData['parameters'])) {
                    throw new NotFoundParameterException("Not Found parameter '$parameter'.");
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
}
