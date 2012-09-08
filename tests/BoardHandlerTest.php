<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dnaumann
 * Date: 05.08.12
 * Time: 20:32
 * To change this template use File | Settings | File Templates.
 */
require_once('../src/BoardHandler.php');

Class BoardHandlerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var object
	 */
	public $reflection;

	/**
	 * @var object // classobject
	 */
	public $object;

	public function setUp()
	{
		$this->object     = new BoardHandler();
		$this->reflection = new ReflectionClass($this->object);
		$this->tearUp();
	}

	public function tearUp()
	{
		require_once('../src/BoardGenerator.php');
		$boardResetter = new BoardGenerator(null, null, true);
		return $boardResetter;
	}

	public function testCalcWithString()
	{
		$params = array(4,'3-3');
		$expectedResult = 1;

		$reflectionCalcWithString = $this->reflection->getMethod('calcWithString');
		$reflectionCalcWithString->setAccessible(true);
		$result = $reflectionCalcWithString->invokeArgs($this->object, $params);
		$this->assertEquals($expectedResult, $result);
	}

	public function testGetAvailableColors()
	{
		$possibleColors = array('green','yellow','blue','red','pink','purple','cyan');

		$reflectionGetAvailableColors = $this->reflection->getMethod('getAvailableColors');
		$reflectionGetAvailableColors->setAccessible(true);
		$result = $reflectionGetAvailableColors->invokeArgs($this->object, array());

		if (!is_array($result) || count($result) === 0) {
			$this->fail('Couldn\'t load available colors');
		}

		$c = count($result);
		for ($i=0;$i<$c;$i++) {
			$this->assertContains($result[$i], $possibleColors);
		}
	}

	public function testSetColidingTabs()
	{
		$params = array('0/0' => array(0,0));

		$expectedResult = array(
			0 => array(
				'row' => 1,
				'col' => 0
			),
			1 => array(
				'row' => 0,
				'col' => 1
			)
		);

		$reflectionSetColidingTabs = $this->reflection->getMethod('setColidingTabs');
		$reflectionSetColidingTabs->setAccessible(true);
		$result = $reflectionSetColidingTabs->invokeArgs($this->object, $params);

		if (!is_array($result) || count($result) >= 4) {
			$this->fail('Couldn\'t calculate coliding tabs!');
		}

		$this->assertEquals($expectedResult, $result);
	}

	public function testGetColidingTabs()
	{
		$availabeColors = array('green','yellow','blue','red','pink','purple','cyan');
		$expectedFields2Draw = array(
			0 => array('row' => 0, 'col' => 0),
			1 => array('row' => 1, 'col' => 0),
			2 => array('row' => 0, 'col' => 1)
		);

		$reflectionGetColidingTabs = $this->reflection->getMethod('getColidingTabs');

		foreach ($availabeColors as $color) {
			$result = $reflectionGetColidingTabs->invokeArgs($this->object, array($color));

			if (is_array($result) && count($result) > 1) {
				break;
			}
		}

		if (!is_array($result) || count($result) == 0) {
			$this->fail('Didn\'t receive coliding tabs');
		}

		$this->assertEquals($expectedFields2Draw, $result);
	}
}
