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
		'samsonos/php_fs' => 112,
		'samsonos/php_fs_local' => 79,
		'samsonos/php_activerecord' => 35,
		'samsonos/cms_app' => 27,
		'samsonos/js_core' => 11,
		'samsonos/cms_api' => 8,
		'samsonos/php_jquery' => 7,
		'samsonos/cms_input' => 5,
		'samsonos/social_core' => 5,
		'samsonos/php_resourcer' => 5,
		'samsonos/php_jquery_ui' => 3,
		'samsonos/php_treeview' => 3,
		'samsonos/php_email' => 2,
		'samsonos/php_less' => 2,
		'samsonos/php_upload' => 2,
		'samsonos/social_email' => 2,
		'samsonos/php_i18n' => 2,
		'samsonos/js_md5' => 1,
		'samsonos/js_lightbox' => 1,
		'samsonos/js_tabs' => 1,
		'samsonos/php_fs_aws' => 1,
		'samsonos/php_pager' => 1,
		'samsonos/php_parse' => 1,
		'samsonos/php_scale' => 1,
		'samsonos/php_deploy' => 1,
		'samsonos/php_compressor' => 1,
		'samsonos/js_fixedheader' => 1,
		'samsonos/js_tinybox' => 1,
		'samsonos/js_translit' => 1,
		'samsonos/js_select' => 1,
		'samsonos/cms_input_date' => 1,
		'samsonos/cms_app_help' => 1,
		'samsonos/cms_app_material' => 1,
		'samsonos/cms_app_gallery' => 1,
		'samsonos/cms_app_field' => 1,
		'samsonos/cms_app_cleaner' => 1,
		'samsonos/cms_app_export' => 1,
		'samsonos/cms_app_navigation' => 1,
		'samsonos/cms_app_product' => 1,
		'samsonos/cms_input_upload' => 1,
		'samsonos/cms_input_wysiwyg' => 1,
		'samsonos/cms_input_select' => 1,
		'samsonos/cms_app_user' => 1,
		'samsonos/cms_app_relatedmaterial' => 1,
		'samsonos/cms_app_signin' => 1,
		'samsonos/cms_table' => 1
	);

	public $noVendorExample = array(
		'samsonos/php_event' => 21532,
		'samsonos/php_composer' => 21532,
		'samsonos/php_core' => 945,
		'samsonos/php_fs' => 112,
		'samsonos/php_fs_local' => 79,
		'samsonos/php_activerecord' => 35,
		'samsonos/cms_app' => 27,
		'samsonos/js_core' => 11,
		'symfony/event-dispatcher' => 8,
		'samsonos/cms_api' => 8,
		'phpunit/php-text-template' => 8,
		'samsonos/php_jquery' => 7,
		'samsonos/php_resourcer' => 5,
		'samsonos/social_core' => 5,
		'phpunit/php-file-iterator' => 5,
		'samsonos/cms_input' => 5,
		'sebastian/environment' => 5,
		'sebastian/version' => 5,
		'guzzle/guzzle' => 4,
		'phpunit/php-token-stream' => 4,
		'samsonos/php_treeview' => 3,
		'samsonos/php_jquery_ui' => 3,
		'phpunit/php-timer' => 2,
		'aws/aws-sdk-php' => 2,
		'samsonos/social_email' => 2,
		'samsonos/php_i18n' => 2,
		'samsonos/php_email' => 2,
		'phpunit/php-code-coverage' => 2,
		'samsonos/php_less' => 2,
		'symfony/yaml' => 2,
		'phpoffice/phpexcel' => 2,
		'sebastian/exporter' => 2,
		'sebastian/diff' => 2,
		'samsonos/php_upload' => 2,
		'phpunit/phpunit-mock-objects' => 2,
		'samsonos/js_md5' => 1,
		'samsonos/js_select' => 1,
		'samsonos/js_lightbox' => 1,
		'samsonos/php_pager' => 1,
		'samsonos/js_fixedheader' => 1,
		'samsonos/php_parse' => 1,
		'samsonos/php_scale' => 1,
		'squizlabs/php_codesniffer' => 1,
		'samsonos/php_fs_aws' => 1,
		'samsonos/php_deploy' => 1,
		'samsonos/js_tinybox' => 1,
		'samsonos/js_translit' => 1,
		'samsonos/php_compressor' => 1,
		'samsonos/js_tabs' => 1,
		'samsonos/cms_app_product' => 1,
		'samsonos/cms_app_gallery' => 1,
		'samsonos/cms_app_help' => 1,
		'samsonos/cms_app_material' => 1,
		'samsonos/cms_app_field' => 1,
		'samsonos/cms_app_export' => 1,
		'phpunit/phpunit' => 1,
		'samsonos/cms_app_cleaner' => 1,
		'samsonos/cms_app_navigation' => 1,
		'samsonos/cms_app_relatedmaterial' => 1,
		'samsonos/cms_input_upload' => 1,
		'samsonos/cms_input_wysiwyg' => 1,
		'samsonos/cms_input_select' => 1,
		'samsonos/cms_input_date' => 1,
		'samsonos/cms_app_signin' => 1,
		'samsonos/cms_app_user' => 1,
		'samsonos/cms_table' => 1
	);

	public $includeKeyExample = array (
		'samsonos/php_fs' => 112,
		'samsonos/php_fs_local' => 79,
		'samsonos/php_activerecord' => 35,
		'samsonos/cms_app' => 27,
		'samsonos/js_core' => 11,
		'samsonos/cms_api' => 8,
		'samsonos/php_jquery' => 7,
		'samsonos/php_resourcer' => 5,
		'samsonos/social_core' => 5,
		'samsonos/cms_input' => 5,
		'samsonos/php_treeview' => 3,
		'samsonos/php_jquery_ui' => 3,
		'phpoffice/phpexcel' => 2,
		'samsonos/php_email' => 2,
		'samsonos/php_upload' => 2,
		'samsonos/php_less' => 2,
		'samsonos/social_email' => 2,
		'samsonos/php_i18n' => 2,
		'samsonos/js_md5' => 1,
		'samsonos/js_tabs' => 1,
		'samsonos/js_select' => 1,
		'samsonos/js_translit' => 1,
		'samsonos/php_fs_aws' => 1,
		'samsonos/php_pager' => 1,
		'samsonos/php_parse' => 1,
		'samsonos/php_scale' => 1,
		'samsonos/php_event' => 1,
		'samsonos/php_deploy' => 1,
		'samsonos/js_lightbox' => 1,
		'samsonos/php_composer' => 1,
		'samsonos/php_compressor' => 1,
		'samsonos/js_tinybox' => 1,
		'samsonos/cms_input_select' => 1,
		'samsonos/cms_app_help' => 1,
		'samsonos/cms_app_material' => 1,
		'samsonos/cms_app_navigation' => 1,
		'samsonos/cms_app_gallery' => 1,
		'samsonos/cms_app_field' => 1,
		'samsonos/cms_app_cleaner' => 1,
		'samsonos/cms_app_export' => 1,
		'samsonos/cms_app_product' => 1,
		'samsonos/cms_app_relatedmaterial' => 1,
		'samsonos/cms_input_wysiwyg' => 1,
		'samsonos/cms_table' => 1,
		'samsonos/cms_input_upload' => 1,
		'samsonos/cms_input_date' => 1,
		'samsonos/cms_app_signin' => 1,
		'samsonos/cms_app_user' => 1,
		'samsonos/js_fixedheader' => 1
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
		$this->composer->create($composerModules, 'tests/', 'composer.test');
		$this->assertEquals($composerModules, $this->createExample);
	}

	public function testEmpty()
	{
		$this->composer->vendor('samsonostest');
		$composerModules = array();
		$this->composer->create($composerModules, 'tests/', 'composer.test');
		$modulesExample = array();
		$this->assertEquals($composerModules, $modulesExample);
	}
	public function testNoFile()
	{
		$this->composer->vendor('samsonos')->ignoreKey('samson_module_ignore')->ignorePackage('samsonos/php_core');
		$composerModules = array();
		$this->composer->create($composerModules, 'tests/', 'composer.lock');
		$modulesExample = array();
		$this->assertEquals($composerModules, $modulesExample);
	}

	public function testNoVendor()
	{
		$composerModules = array();
		$this->composer->create($composerModules, 'tests/', 'composer.test');
		$this->assertEquals($composerModules, $this->noVendorExample);
	}
	public function testIncludeKey()
	{
		$this->composer->vendor('samsonos')->includeKey('samson_module_include')->ignorePackage('samsonos/php_core');
		$composerModules = array();
		$this->composer->create($composerModules, 'tests/', 'composer.test');
		$this->assertEquals($composerModules, $this->includeKeyExample);
	}
}
