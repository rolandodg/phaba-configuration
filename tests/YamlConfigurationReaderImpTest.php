<?php

declare(strict_types=1);

namespace Phaba\Configuration\Tests;

use Phaba\Configuration\Exception\InvalidElementException;
use Phaba\Configuration\Exception\NotFoundFileException;
use Phaba\Configuration\Exception\NotFoundParameterException;
use Phaba\Configuration\YamlConfigurationReaderImp;
use PHPUnit\Framework\TestCase;

class YamlConfigurationReaderImpTest extends TestCase
{
    public function testCanGetCommonConfiguration(): void
    {
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config');
        $this->assertEquals('2ML2010', $config->getElement('common')['text']);
        YamlConfigurationReaderImp::reset();
    }

    public function testCanGetTestConfiguration(): void
    {
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config');
        $this->assertEquals('2ML2010Test', $config->getElement('testing')['text']);
        YamlConfigurationReaderImp::reset();
    }

    public function testCanGetConfigurationWithoutSpecifiedEnvironment(): void
    {
        unset($GLOBALS['env']);
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config');
        $this->assertEquals('2ML2010', $config->getElement('common')['text']);
        $GLOBALS['env'] = 'test';
        YamlConfigurationReaderImp::reset();
    }

    public function testThrowExceptionWhenElementDoesNotExist(): void
    {
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config');
        $this->expectException(InvalidElementException::class);
        $config->getElement('FakeElement');
        YamlConfigurationReaderImp::reset();
    }

    public function testCanImportFileFromCommonConfiguration(): void
    {
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config');
        $this->assertEquals('AmigaCommodore500', $config->getElement('common_imported_data')['computer']);
        YamlConfigurationReaderImp::reset();
    }

    public function testCanImportFileFromEnvironmentConfiguration(): void
    {
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config');
        $this->assertEquals('Spectrum48k', $config->getElement('env_imported_data')['computer']);
        YamlConfigurationReaderImp::reset();
    }

    public function testCanImportNestedFiles(): void
    {
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config');
        $this->assertEquals('Monkey', $config->getElement('island1'));
        $this->assertEquals('Mêlée', $config->getElement('island2'));
        $this->assertEquals('Booty', $config->getElement('island3'));
        $this->assertEquals('Scabb', $config->getElement('island4'));
        $this->assertEquals('Phatt', $config->getElement('island5'));
        $this->assertEquals('Dinky', $config->getElement('island6'));
        YamlConfigurationReaderImp::reset();
    }

    public function testCanGetCommonConfigWhenEnvironmentConfigIsNotExisting(): void
    {
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config2');
        $this->assertEquals('Guybrush Threepwood', $config->getElement('common')['user']);
        YamlConfigurationReaderImp::reset();
    }

    public function testCanThrowExceptionWhenImportedFileIsNotExisting(): void
    {
        $this->expectException(NotFoundFileException::class);
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config3');
        YamlConfigurationReaderImp::reset();
    }

    public function testCanThrowExceptionMessageWhenImportedFileIsNotExisting(): void
    {
        $this->expectExceptionMessage('File not_existing_file.yaml for importing is not existing');
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config3');
        YamlConfigurationReaderImp::reset();
    }

    public function testCanUseParameterValues(): void
    {
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config_with_params');
        $this->assertEquals('SCRUMM1', $config->getElement('simple'));
        $this->assertEquals('SCRUMM2', $config->getElement('nested')['value']);
        $this->assertEquals('SCRUMM3', $config->getElement('more_nested')['data']['value']);
        YamlConfigurationReaderImp::reset();
    }

    public function testCanSearchInIntegerFieldValueForReplacingWithParameter(): void
    {
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config_with_integer_values');
        $this->assertEquals('Guybrush', $config->getElement('user'));
        YamlConfigurationReaderImp::reset();
    }

    public function testCanThrowExceptionWhenParameterIsNotExisting(): void
    {
        $this->expectException(NotFoundParameterException::class);
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config_params_exception');
        YamlConfigurationReaderImp::reset();
    }

    public function testCanThrowExceptionMessageWhenParameterIsNotExisting(): void
    {
        $this->expectExceptionMessage("Not Found parameter 'value'.");
        $config = YamlConfigurationReaderImp::getInstance('tests/app/config_params_exception');
        YamlConfigurationReaderImp::reset();
    }
}
