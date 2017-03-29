<?php

declare(strict_types=1);

namespace Phaba\Configuration;

use Phaba\Configuration\Exception\InvalidElementException;
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

    private function getCurrentConfigurationArray()
    {
        $commonConfig = Yaml::parse(file_get_contents($this->configPath.'/config.yaml'));
        $environmentConfig = Yaml::parse(file_get_contents($this->configPath.'/config_'.ENVIRONMENT.'.yaml'));

        return array_merge($commonConfig, $environmentConfig);
    }

    public function getElement(string $name)
    {
        if (!array_key_exists($name, $this->currentConfig)) {
            throw new InvalidElementException("Invalid $name Element in common configuration");
        }

        return $this->currentConfig[$name];
    }
}
