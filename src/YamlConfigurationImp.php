<?php

declare(strict_types=1);

namespace Phaba\Configuration;

use Phaba\Configuration\Exception\InvalidElementException;
use Phaba\Configuration\Exception\NotExistingFileException;
use Symfony\Component\Yaml\Yaml;

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

    private function getCurrentConfigurationArray(): array
    {
        $commonConfig = $this->getConfigurationData($this->configPath.'/config.yaml');
        $environmentConfig = $this->getConfigurationData($this->configPath.'/config_'.ENVIRONMENT.'.yaml');

        return array_merge($commonConfig, $environmentConfig);
    }

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

    public function getElement(string $name)
    {
        if (!array_key_exists($name, $this->currentConfig)) {
            throw new InvalidElementException("Invalid $name Element in configuration");
        }

        return $this->currentConfig[$name];
    }
}
