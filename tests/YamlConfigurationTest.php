<?php

declare(strict_types=1);

namespace Phaba\Configuration\Tests;

use Phaba\Configuration\Exception\InvalidElementException;
use Phaba\Configuration\Exception\NotExistingFileException;
use Phaba\Configuration\Exception\NotFoundParameterException;
use Phaba\Configuration\YamlConfigurationImp;
use PHPUnit\Framework\TestCase;

class YamlConfigurationTest extends TestCase
{
    public function testCanGetCommonConfiguration(): void
    {
        $config = new YamlConfigurationImp('tests/app/config');
        $this->assertEquals('2ML2010', $config->getElement('common')['text']);
    }

    public function testCanGetTestConfiguration(): void
    {
        $config = new YamlConfigurationImp('tests/app/config');
        $this->assertEquals('2ML2010Test', $config->getElement('testing')['text']);
    }

    public function testCanGetConfigurationWithoutSpecifiedEnvironment(): void
    {
        unset($GLOBALS['env']);
        $config = new YamlConfigurationImp('tests/app/config');
        $GLOBALS['env'] = 'test';
        $this->assertEquals('2ML2010', $config->getElement('common')['text']);
    }

    public function testThrowExceptionWhenElementDoesNotExist(): void
    {
        $config = new YamlConfigurationImp('tests/app/config');
        $this->expectException(InvalidElementException::class);
        $config->getElement('FakeElement');
    }

    public function testCanImportFileFromCommonConfiguration(): void
    {
        $config = new YamlConfigurationImp('tests/app/config');
        $this->assertEquals('AmigaCommodore500', $config->getElement('common_imported_data')['computer']);
    }

    public function testCanImportFileFromEnvironmentConfiguration(): void
    {
        $config = new YamlConfigurationImp('tests/app/config');
        $this->assertEquals('Spectrum48k', $config->getElement('env_imported_data')['computer']);
    }

    public function testCanImportNestedFiles(): void
    {
        $config = new YamlConfigurationImp('tests/app/config');
        $this->assertEquals('Monkey', $config->getElement('island1'));
        $this->assertEquals('Mêlée', $config->getElement('island2'));
        $this->assertEquals('Booty', $config->getElement('island3'));
        $this->assertEquals('Scabb', $config->getElement('island4'));
        $this->assertEquals('Phatt', $config->getElement('island5'));
        $this->assertEquals('Dinky', $config->getElement('island6'));
    }

    public function testCanGetCommonConfigWhenEnvironmentConfigIsNotExisting(): void
    {
        $config = new YamlConfigurationImp('tests/app/config2');
        $this->assertEquals('Guybrush Threepwood', $config->getElement('common')['user']);

    }

    public function testCanThrowExceptionWhenImportedFileIsNotExisting(): void
    {
        $this->expectException(NotExistingFileException::class);
        $config = new YamlConfigurationImp('tests/app/config3');
    }

    public function testCanThrowExceptionMessageWhenImportedFileIsNotExisting(): void
    {
        $this->expectExceptionMessage('File not_existing_file.yaml for importing is not existing');
        $config = new YamlConfigurationImp('tests/app/config3');
    }

    public function testCanUseParameterValues(): void
    {
        $config = new YamlConfigurationImp('tests/app/config_with_params');
        $this->assertEquals('SCRUMM1', $config->getElement('simple'));
        $this->assertEquals('SCRUMM2', $config->getElement('nested')['value']);
        $this->assertEquals('SCRUMM3', $config->getElement('more_nested')['data']['value']);
    }

    public function testCanSearchInIntegerFieldValueForReplacingWithParameter(): void
    {
        $config = new YamlConfigurationImp('tests/app/config_with_integer_values');
        $this->assertEquals('Guybrush', $config->getElement('user'));
    }

    public function testCanThrowExceptionWhenParameterIsNotExisting(): void
    {
        $this->expectException(NotFoundParameterException::class);
        $config = new YamlConfigurationImp('tests/app/config_params_exception');
    }

    public function testCanThrowExceptionMessageWhenParameterIsNotExisting(): void
    {
        $this->expectExceptionMessage("Not Found parameter 'value'.");
        $config = new YamlConfigurationImp('tests/app/config_params_exception');
    }
}
