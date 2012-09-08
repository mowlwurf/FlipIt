<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dnaumann
 * Date: 03.09.12
 * Time: 17:53
 * To change this template use File | Settings | File Templates.
 */


	require_once('../inc/Logger.php');

Class LoggerTest extends PHPUnit_Framework_TestCase
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
	public $reflectionAddRecord;

	/**
	 * @reflectionMethod
	 */
	public $reflectionLogFile;

	public function setUp()
	{
		$this->object     = new Logger();
		$this->reflection = new ReflectionClass($this->object);
	}

	public function testAddRecord ()
	{
		$testMessage = 'Test Entry';
		$params = array($testMessage);

		$this->reflectionLogFile = $this->reflection->getProperty('logFile');
		$this->reflectionLogFile->setAccessible(true);
		$fh = fopen($this->reflectionLogFile->getValue($this->object), 'a+');
		while (!feof($fh)) {
			$resultBeforeAdd = fgets($fh, 1024);
		}

		$this->reflectionAddRecord = $this->reflection->getMethod('addRecord');
		$this->reflectionAddRecord->invokeArgs($this->object, $params);

		$fh = fopen($this->reflectionLogFile->getValue($this->object), 'a+');
		while (!feof($fh)) {
			$resultAfterAdd = fgets($fh, 1024);
		}

		$this->assertEquals($resultBeforeAdd.$testMessage, $resultAfterAdd);
	}

}
