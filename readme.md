#[SamsonPHP](http://samsonphp.com/) - composer packages list generator

[![Latest Stable Version](https://poser.pugx.org/samsonphp/composer/v/stable.svg)](https://packagist.org/packages/samsonphp/composer) 
[![Build Status](https://travis-ci.org/SamsonPHP/composer.svg?branch=1.0.11)](https://travis-ci.org/SamsonPHP/composer)
[![Code Coverage](https://scrutinizer-ci.com/g/samsonphp/composer/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/composer/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samsonphp/composer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samsonphp/composer/?branch=master)
[![Code Climate](https://codeclimate.com/github/samsonphp/composer/badges/gpa.svg)](https://codeclimate.com/github/samsonos/composer)
[![Stories in Ready](https://badge.waffle.io/samsonphp/composer.png?label=ready&title=Ready)](https://waffle.io/samsonphp/composer)
[![Total Downloads](https://poser.pugx.org/samsonphp/composer/downloads.svg)](https://packagist.org/packages/samsonphp/composer)


Module creates an ordered list of projects composer packages sorted by priority.
Priority is automatically determined by the dependencies between packages, this dependecies usually located at ```composer.json``` in project root folder. If a package ```package_A``` requires package ```package_B```, then package ```package_B``` priority is higher then package ```package_A``` priority. 

This approach gives ability to build dependency tree from all composer loaded packages and represent it as a list. This is usefull when you tring to customly build package loading logic based on composer.

## Usage

To work with this module you should get composer instance:
```php
$composer = new \samsonos\composer\Composer($systemPath, $lockFileName);
```
  * ```$systemPath``` - Path to current web-application
  * ```$lockFileName``` - Composer lock file name (by default is set to ```'composer.lock'```)
    
To configure module there are methods:
  * ```addVendor($vendor)``` - Add available vendor (```$vendor``` is the available vendor)
  * ```setIgnoreKey($ignoreKey)``` - Set name of composer extra parameter to ignore package (```$ignoreKey``` is name). Composer usage example:```"extra": { "samson_module_ignore": "1" }``` (```$composer->setIgnoreKey('samson_module_ignore')```)  
  * ```setIncludeKey($includeKey)``` - Set name of composer extra parameter to include package (```$includeKey``` is name). Composer usage example:```"extra": { "samson_module_include": "1" }``` (```$composer->setIncludeKey('samson_module_include')```)
  * ```addIgnorePackage($package)``` - Add ignored package (```$package``` is the ignored package)
    
To create sorted list of project composer packages you can use method ```create()```.
Example usage:
```php
$composer = new \samsonos\composer\Composer($systemPath);
$composerModules = $composer
    ->addVendor('samsonos')
    ->setIgnoreKey('samson_module_ignore')
    ->addIgnorePackage('samsonos/php_core')
    ->addIgnorePackage('samsonos/php_event') 
    ->create();
```

Developed by [SamsonOS](http://samsonos.com/)
