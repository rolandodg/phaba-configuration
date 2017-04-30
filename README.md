# Phaba-Configuration

Phaba-configuration is a PHP library for managing configuration data of your projects.

The aim of Phaba-configuration is easier application configuration management, following **safety practices**.

## Pre-requirements

- PHP >=7.1

## Getting Started

### Installation

- Composer

Use composer for installing phaba-configuration.

`composer require phaba/phaba-configuration`

### Simple use

For using phaba-configuration is necessary only a config.yaml file with needed configuration data.

Getting configuration data is possible thank to *YamlConfigurationImp* class, as shown below:

`$config = new YamlConfigurationImp('path/to/config/files/folder');`
`$config->getElement('configuration_field')`

### Nested configuration field

Using nested configuration data is possible, in which case getElement function returns the value of first parent field.

In case below, 

    common:
        user: Guybrush Threepwood

getElement function returns team (array) like value, therefore for getting team value we have to code the next

`$config = new YamlConfigurationImp('path/to/config/files/folder');`
`$config->getElement('common')['user']`

### Use separate configuration files for various environment

Phaba-Configuration allows you to use an extra configuration file for the current environment, i. e, for test environment you can use config_test.yaml file in additon to config.yaml.

For that the below is neccesary:

1. Define 'env' global variable.

This variable will determine the current environment. For test environment a good place for define $env variable should be the test bootstrap file

\# tests/bootstrap.php

`$env = 'test'`

2. Created corresponding .yaml configuration file.

For example config_test.yaml is $env variable value is 'test'. This configuration file has to be in the same folder than config.yaml file.

We can use test configuration file for storing test database connection data, for example.

### Import configuration files

Importing configuration data from external .yaml file is possible.
You can import those data with the **import** field in your configuration file (common and/or environment file), as shown below:

\# path/to/config/files/folder/config.yaml

    import:        
        - {resource: file_for_importing_1.yaml}            
        - {resource: file_for_importing_2.yaml}
        - {resource: file_for_importing_n.yaml}

In this way we can get configuration data from separete files. 

Separate files could be useful for better configuration data structuring, for example separing services config data putting it in services.yaml file. In this case we would have to import services.yaml file

### Using parameter

Parameters is useful for store data which pushing to repository isn't a good idea (e.g. connection data, credentials, etc...).

In this way we store that sensitive data just in our local machine (within parameters.yaml file, for example). 

For using parameters, the below is needed:

**a) Create parameters file** (parameters.yaml file, for exampe)

\# path/to/config/files/folder/parameters.yaml
    
    parameters:        
        one_user: Guybrush Threepwood        
        other_player: Lechuck

**b) Import parameters file** (see Import configuration files)

**c) Use parameter name (between '%') as config field value**

\# path/to/config/files/folder/config.yaml

    common:
        user1: "%one_user%"
        user2: "%other_user%"

If we use parameters files for this reason it's a **good practice** use a file (parameters.yaml.dist) for showing to user the parameters data structure, such as shown below:

\# parameters.yaml.dist
    
    parameters:        
        parameter1: ~        
        parameter2: ~

### Overriding configuration data

In the case that you use different configurations files (specified environment files or imported files), if those files have first parent field with the same name the values will be **overwritten**.

For example, in case below

\# path/to/config/files/folder/config.yaml

    common:
        user1: Michelangelo
        user2: Donatello
        
\# path/to/config/files/folder/config_dev.yaml

    common:
        user3: Raphael
        user4: Leonardo
        
common field will contain an array with 'Raphael' and 'Leonardo' keys as value. 

## Build and Test

For testing code run local (vendor) phpunit

`path/to/project/root$ vendor/bin/phpunit tests/`


## Contribute

Jesús Hernando Sancha <jhernando@laliga.es>