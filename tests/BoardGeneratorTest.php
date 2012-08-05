<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dnaumann
 * Date: 31.07.12
 * Time: 13:08
 * To change this template use File | Settings | File Templates.
 */

require_once('../src/BoardGenerator.php');

Class TableGeneratorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var object
	 */
	public $reflection;

	/**
	 * @var object // classobject
	 */
	public $object;

	/**
	 * PROPERTIES
	 */

	/**
	 * METHODS
	 */

	/**
	 * @reflectionMethod
	 */
	public $reflectionGenerateBoard;

	/**
	 * @reflectionMethod
	 */
	public $reflectionRndColor;

	public function setUp()
	{
		$this->object     = new BoardGenerator();
		$this->reflection = new ReflectionClass($this->object);
	}

	public function testGenerateBoard()
	{
		$params = array();

		$this->reflectionGenerateBoard = $this->reflection->getMethod('generateBoard');
		$this->reflectionGenerateBoard->setAccessible(true);
		$isGenerated = $this->reflectionGenerateBoard->invokeArgs($this->object,$params);

		$this->assertTrue($isGenerated);

		$board = $this->object->getBoard();
		if ($board === false){
			$this->fail('Couldn\'t generate Board');
		} elseif (!is_array($board) || empty($board)) {
			$this->fail('Board not formated');
		} else {

			$this->assertTrue(true);
		}
	}

	public function testRndColor()
	{
		$possibleColors = array('red','green','yellow','blue','purple','pink','cyan');

		$this->reflectionRndColor = $this->reflection->getMethod('rndColor');
		$this->reflectionRndColor->setAccessible(true);
		$randomColor = $this->reflectionRndColor->invokeArgs($this->object, array());
		$this->assertNotNull($randomColor);
		if (!in_array($randomColor, $possibleColors)) {
			$this->fail();
		}
	}

	public function testSetBoardSize()
	{
		$this->reflectionSetBoardSize = $this->reflection->getMethod('setBoardSize');
		$this->reflectionSetBoardSize->setAccessible(true);
		$isSet = $this->reflectionSetBoardSize->invokeArgs($this->object, array(7));
		$this->assertTrue($isSet);

		$this->reflectionBoardSize = $this->reflection->getProperty('boardSize');
		$this->reflectionBoardSize->setAccessible(true);
		$this->assertEquals(7, $this->reflectionBoardSize->getValue($this->object));
	}
}