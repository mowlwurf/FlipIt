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
	}
}
