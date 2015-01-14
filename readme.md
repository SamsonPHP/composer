#[SamsonPHP](http://samsonphp.com/) - composer packages list generator

Module creates an ordered list of projects composer packages sorted by priority.
Priority is automatically determined by the dependencies between packages, this dependecies usually located at ```composer.json``` in project root folder. If a package ```package_A``` requires package ```package_B```, then package ```package_B``` priority is higher then package ```package_A``` priority. 

This approach gives ability to build dependency tree from all composer loaded packages and represent it as a list. This is usefull when you tring to customly build package loading logic based on composer.

[![Latest Stable Version](https://poser.pugx.org/samsonos/php_composer/v/stable.svg)](https://packagist.org/packages/samsonos/php_composer)
[![Build Status](https://travis-ci.org/samsonos/php_composer.png)](https://travis-ci.org/samsonos/php_composer)
[![Coverage Status](https://img.shields.io/coveralls/samsonos/php_composer.svg)](https://coveralls.io/r/samsonos/php_composer?branch=master)
[![Code Climate](https://codeclimate.com/github/samsonos/php_composer/badges/gpa.svg)](https://codeclimate.com/github/samsonos/php_composer)
[![Total Downloads](https://poser.pugx.org/samsonos/php_composer/downloads.svg)](https://packagist.org/packages/samsonos/php_composer)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/samsonos/php_composer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/samsonos/php_composer/?branch=master)

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
