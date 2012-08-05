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
		$this->reflectionGenerateBoard->invokeArgs($this->object,$params);

		$board = $this->object->getBoard();
		if ($board === false){
			$this->fail('Couldn\'t generate Board');
		} elseif (!is_array($board) || empty($board)) {
			$this->fail('Board not formated');
		} else {

			$this->assertTrue(true);
		}
	}
}