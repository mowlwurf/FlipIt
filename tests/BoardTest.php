<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dnaumann
 * Date: 05.08.12
 * Time: 20:35
 * To change this template use File | Settings | File Templates.
 */
require_once('../src/Board.php');

Class BoardTest extends PHPUnit_Framework_TestCase
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
		$this->object     = new Board();
		$this->reflection = new ReflectionClass($this->object);
	}

	public function testDummy()
	{
		$this->assertTrue(true);
	}
}