<?php

declare(strict_types=1);

namespace Phaba\Configuration\Tests;

use Phaba\Configuration\Exception\InvalidElementException;
use Phaba\Configuration\YamlConfigurationImp;
use PHPUnit\Framework\TestCase;

class YamlConfigurationTest extends TestCase
{
    /**
     * @var YamlConfigurationImp
     */
    private $config;

    public function setUp()
    {
        $this->config = new YamlConfigurationImp('tests/app/config');
    }

    public function testCanGetCommonConfiguration(): void
    {
        $this->assertEquals('2ML2010', $this->config->getElement('common')['text']);
    }

    public function testCanGetTestConfiguration(): void
    {
        $this->assertEquals('2ML2010Test', $this->config->getElement('testing')['text']);
    }

    public function testThrowExceptionWhenElementDoesNotExist(): void
    {
        $this->expectException(InvalidElementException::class);
        $this->config->getElement('FakeElement');
    }
}
