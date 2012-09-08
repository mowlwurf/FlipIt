<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dnaumann
 * Date: 01.09.12
 * Time: 15:00
 * To change this template use File | Settings | File Templates.
 */

class Logger
{

	private $logFile = '/var/log/flip.log';

	/**
	 * Constructor
	 *
	 * @param string $name The logging channel
	 */
	function __construct()
	{
	}

	/**
	 * @return string
	 */
	private function getLogFile()
	{
		return $this->logFile;
	}

	private function setLogFile($logFile)
	{
		$this->logFile = $logFile;
	}

	/**
	 * Adds a log record.
	 *
	 * @param integer $level The logging level
	 * @param string $message The log message
	 * @param array $context The log context
	 * @return Boolean Whether the record has been processed
	 */
	function addRecord($message)
	{
		$fh = fopen($this->getLogFile(), 'a+');
		fwrite($fh, $message."\n");
		fclose($fh);
		return true;
	}

}