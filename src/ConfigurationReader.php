<?php

declare(strict_types=1);

namespace Phaba\Migrations\Core\Configuration;

interface ConfigurationReader
{
    public function __construct(string $configurationPath);
    public function getElement(string $name);
}
