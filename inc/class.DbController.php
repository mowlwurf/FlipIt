<?php
/**
 * SQL 2 create ManufacturerTable manualy
 * 
 * INSERT INTO `I-OnNewMedia` (SELECT * FROM `vivamaster` WHERE Match (manufacturer,product,found_by,found_in) against ('I-OnNewMedia' in boolean mode))
 */


/**
 * DbController
 * controlls generally Database Controlling
 * @author mowlwurf
 * @var int iResult count of Results
 * @var Array aResult stack of Result (assoc)
 * @var Array aErrorStack stack of Errors evtl. happened (numerical)
 */
Class DbController{
	
	private $rConnection	= false;		// Connectionressource
	private $iResult 		= 0; 			// iResult count of Results
	private $aResult 		= array(); 		// stack of Result (assoc)
	private $aErrorStack 	= array(); 		// stack of Errors evtl. happened (numerical)
	
	private function _mysqlconnect($sServer,$sUser,$sPass,$sDb){
		$this->rConnection = mysql_connect($sServer,$sUser,$sPass);
		mysql_select_db($sDb,$this->rConnection);
		$this->_check();
		return ($this->rConnection !== TRUE || $this->rConnection !== FALSE) ?	true : false;
	}
	
	function getConnection($sUser,$sPass,$sDb,$sServer = 'localhost'){
		$bGotConnection = $this->_mysqlconnect($sServer,$sUser,$sPass,$sDb);
		return $bGotConnection;
	}
	
	function selectDB($sDataBase)
	{
		return mysql_select_db($sDataBase,$this->rConnection);
	}
	
	/**
	 * error
	 * function to get mysql_error_handling by simple boolean control
	 * @param bool $bComplete 	// if true complete ErrorStack will be returned
	 * @param bool $bOutput		// if true response will be returned in HTML-Format for Debugging
	 */
	function error($bComplete = false, $bOutput = false)
	{
		return 	($bComplete && $bOutput) 	? implode(' <br/>',$this->aErrorStack) : 
				($bComplete && !$bOutput)	? $this->aErrorStack : 
				(!$bComplete && $bOutput)   ? 'DEBUG [ERROR -> '.$this->aErrorStack[count($this->aErrorStack)-1].' <br />DEBUG STOP]' : $this->aErrorStack[count($this->aErrorStack)-1];
	}
	
	/**
	 * _check
	 * write entry with actualy erroroutout into errorstack of objectinstance
	 */
	private function _check()
	{
		$this->aErrorStack[] = mysql_error();
	}
	
	/**
	 * _setResultCount
	 * get resultrows from mysql ressource and write to objectinstance
	 * @param Ressource $rResult
	 * @param Ressource $rConnectionID
	 */
	private function _setResultCount($rResult)
	{
		$this->iResult = mysql_num_rows($rResult);
	}
	
	/**
	 * getResult
	 * get resultarray
	 * @param Ressource $rResult
	 * @param Ressource $rConnectionID
	 */
	function getResult()
	{
		if(!$this->rConnection || $this->rConnection === false)
		{
			return false;
		}	
		return $this->aResult;
	}
	
	/**
	 * getResult
	 * get resultcunt
	 * @param Ressource $rResult
	 * @param Ressource $rConnectionID
	 */
	function getCount()
	{
		if(!$this->rConnection || $this->rConnection === false)
		{
			return false;
		}	
		return $this->iResult;
	}
	
	/**
	 * alias for mysql_query
	 * this function contains complete processing for all kind of sql queries and save resultinformation 2 Objectinstance
	 * @param String $sQueryString				// containing query
	 * @param Ressource $rConnectionID
	 */
	function query($sQueryString)
	{
		$sType = substr($sQueryString,0,6);
		switch($sType)
		{
			case 'SELECT': 
			{
				$rRes = mysql_query($sQueryString,$this->rConnection);
				$this->_setResultCount($rRes,$this->rConnection);
				while($aTmp = mysql_fetch_assoc($rRes))
				{
					$this->aResult[] = $aTmp;
				} 
				$this->_check();
				break;
			}
			case 'INSERT':
			case 'UPDATE':
			{
                $rRes = mysql_query($sQueryString,$this->rConnection);
				$this->_check();
				break;
			}
			case 'DELETE':
			{
				$rRes = mysql_query(mysql_real_escape_string($sQueryString),$this->rConnection) or die($this->_check());
				($rRes) ? $this->_setResultCount($rRes,$this->rConnection) : $this->_check();
				break;
			}
            default:
            {
                $rRes = mysql_query($sQueryString,$this->rConnection);
                break;
            }
		}
		$this->_check();
	}
	
	function clearCache()
	{
		if($this->iResult === Null && $this->aResult[0] === false && $this->aErrorStack[1] === false)
		{
			return false;
		}
		$this->iResult 		= Null;
		$this->aResult 		= array();
		$this->aErrorStack 	= array();
		return true;
	}
}