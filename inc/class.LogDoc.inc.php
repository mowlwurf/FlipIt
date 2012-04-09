<?php
include_once('class.DBController.php');
Class LogDoc{

    /**
     * initialise LogDoc and clear logtable
     */
    function __construct()
    {
        $this->oDBController = new DbController();
        $this->oDBController->getConnection('root','sonada86','flipit');
        $this->oDBController->query('Truncate Table log');
    }

	/**
	 * <b>log</b><br/>
	 * logs any activity in logtable
	 * @param String $sSourceFile	// file sends call
	 * @param String $sType			// process,error etc.
	 * @param String $sLogMessage   // containing 
	 */
	function log($sSourceFile,$sFunction,$sType,$sLogMessage)
	{
        $this->oDBController = new DbController();
        $this->oDBController->getConnection('root','sonada86','flipit');
        $this->oDBController->query('INSERT INTO log (`file`,`type`,`function`,`message`) VALUES (\''.mysql_real_escape_string($sSourceFile).'\',\''.$sType.'\',\''.$sFunction.'\',\''.$sLogMessage.'\')');
        $this->oDBController->clearCache();
	}

}