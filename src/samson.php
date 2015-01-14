<?php
/**
 * SamsonPHP initialization class
 */
//[PHPCOMPRESSOR(remove,start)]
// Subscribe to core started event to load all possible module configurations
\samsonphp\event\Event::subscribe('core.composer.create', array(new \samsonphp\composer\Composer(), 'create'));
//[PHPCOMPRESSOR(remove,end)]