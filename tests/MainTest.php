<?php
namespace tests;

/**
 * Created by Nikita Kotenko <kotenko@samsonos.com>
 * on 25.11.14 at 16:42
 */
class MainTest extends \PHPUnit_Framework_TestCase
{
	public $createExample = array
	(
		'samsonframework/orm' => array('samsonphp_package_compressable' => 1, 'samson_module_include' => 1),
		'samsonos/cms_app' => array(),
		'samsonos/cms_app_cleaner' => array(),
		'samsonos/cms_app_export' => array()
	);

	public $noVendorExample = array(
		'samsonframework/orm' => array('samsonphp_package_compressable' => 1, 'samson_module_include' => 1),
		'samsonos/cms_app' => array(),
		'samsonos/cms_app_cleaner' => array(),
		'samsonos/cms_app_export' => array(),
		'phpunit/php-code-coverage' => array()
	);

	public $composer;

	/** Tests init */
	public function setUp()
	{
		$this->composer = new \samsonphp\composer\Composer();
		$this->composer->clear();
	}

	public function testCreate()
	{
		$this->composer->vendor('samsonos')->ignoreKey('samson_module_ignore')->ignorePackage('samsonos/php_core');
		$composerModules = array();
		$this->composer->create($composerModules, 'tests/', array('lockFileName'=>'composer.test','includeKey' => 'samson_module_include'));
		$this->assertEquals($composerModules, $this->createExample);
	}

	public function testEmpty()
	{
		$this->composer->vendor('samsonostest');
		$composerModules = array();
		$this->composer->create($composerModules, 'tests/', array('lockFileName'=>'composer.test'));
		$modulesExample = array();
		$this->assertEquals($composerModules, $modulesExample);
	}
	public function testNoFile()
	{
		$this->composer->vendor('samsonos')->ignoreKey('samson_module_ignore')->ignorePackage('samsonos/php_core');
		$composerModules = array();
		$this->composer->create($composerModules, 'tests/');
		$modulesExample = array();
		$this->assertEquals($composerModules, $modulesExample);
	}

	public function testNoVendor()
	{
		$composerModules = array();
		$this->composer->create($composerModules, 'tests/', array('lockFileName'=>'composer.test'));
		$this->assertEquals($composerModules, $this->noVendorExample);
	}
}
