<?php

namespace supplyhog\JsonObjectBehavior\tests;

use supplyhog\JsonObjectBehavior\tests\models\JsonTest;

class JsonObjectModelTest extends \PHPUnit_Framework_TestCase
{
	public function testToString()
	{
		$jsonTest = new JsonTest();
		$this->assertEquals('{"hello":"world"}', (string)$jsonTest);
		$jsonTest->hello = 'Other';
		$this->assertEquals('{"hello":"Other"}', (string)$jsonTest);
	}
}