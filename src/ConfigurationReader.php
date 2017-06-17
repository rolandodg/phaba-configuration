<?php

declare(strict_types=1);

namespace Phaba\Configuration;

/**
 * Interface for implementing configuration readers from distinct file types.
 *
 * For example, for implement a reader for reading configuration data from .txt file
 * we can create TextPlainConfigurationImp concrete class and implementing function for getting
 * an specified element value
 *
 * @package Phaba\Configuration
 */
interface ConfigurationReader
{
    /**
     * Get value of wanted configuration element
     *
     * @param string $name The name of the element which value is looked for
     * @return mixed Value of wanted configuration element
     */
    public function getElement(string $name);
}
