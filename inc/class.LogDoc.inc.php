<?php

Class LogDoc{

    /**
     * initialise LogDoc and clear logtable
     */
    function __construct()
    {
        include('cnf/config.inc.php');
        $this->_readConfig($aConfig);
        $this->oDBController = new DbController();
        $this->oDBController->getConnection($this->dbUser,$this->dbPassword,$this->dbName,$this->dbServer);
        $this->oDBController->query('Truncate Table '.$this->log_table);
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
        $this->oDBController->getConnection($this->dbUser,$this->dbPassword,$this->dbName,$this->dbServer);
        $this->oDBController->query('INSERT INTO '.$this->log_table.' (`file`,`type`,`function`,`message`) VALUES (\''.mysql_real_escape_string($sSourceFile).'\',\''.$sType.'\',\''.$sFunction.'\',\''.$sLogMessage.'\')');
        $this->oDBController->clearCache();
	}

    private function _readConfig($aConfig)
    {
        if(!is_array($aConfig))
        {
            return false;
        }
        foreach($aConfig as $sKey => $sValue)
        {
            $this->$sKey = $sValue;
        }
        return true;
    }
}