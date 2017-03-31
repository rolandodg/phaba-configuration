<?php

declare(strict_types=1);

namespace Phaba\Configuration;

interface Configuration
{
    public function __construct(string $configurationPath);
    public function getElement(string $name);
}
