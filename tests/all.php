#!/usr/bin/env php
<?php

require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__.'/../vendor/lastcraft/simpletest/autorun.php');

// FIXME: autoload helper classes
require_once(__DIR__.'/RightSignature/UnitTestCase.php');

class RightSignatureTestCollector extends SimplePatternCollector
{
	protected function handle(&$test, $filename)
	{
		if (is_dir($filename))
			$test->collect($filename, $this);
		else
			parent::handle($test, $filename);
	}
}

class RightSignatureTests extends TestSuite
{
	public function __construct()
	{
		$this->collect(__DIR__, new RightSignatureTestCollector('#Test\.php$#'));
	}
}
