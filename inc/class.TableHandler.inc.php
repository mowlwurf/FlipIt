<?php


/**
 * TableHandler
 * berechnet Spielkollisionen und liefert Spielfeldreaktionen
 * @author d.naumann
 * @package flipit
 */
Class TableHandler{

    private $oDbController = false;

    private $aBoardMatrix  = array();

	function __construct($bDebug=false)
	{
        $this->oLog = new LogDoc();
        if($bDebug)
        {
            //$oUtils -> UnitTest('0/0');
        }
        include('cnf/config.inc.php');
        $this->_readConfig($aConfig);
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

    private function __getBoardMatrix()
    {
        $aBoardMatrix = false;
        $this->oDBController = new DbController();
        $this->oDBController->getConnection($this->dbUser,$this->dbPassword,$this->dbName,$this->dbServer);
        $this->oDBController->query('SELECT * FROM '.$this->active_table);
        $aBoardMatrix = $this->oDBController->getResult();
        $this->oDBController->clearCache();
        return unserialize($aBoardMatrix[0]['board']);
    }

	//TODO
	function calcWithString($iInt,$sString)
	{
		$aOperatorMap = Array('+','-');
		foreach($aOperatorMap as $sOperator)
		{   if(strpos($sString,$sOperator) !== false)
            {
                $aTmp = explode($sOperator,$sString);
            }
            if(is_array($aTmp) && trim($aTmp[1]) != '')
            {
                break;
            }
		}

        switch($sOperator)
        {
            case '+': return $iInt+$aTmp[1];
                      break;
            case '-': return $iInt-$aTmp[1];
                      break;
        }
		return false;
	}
	
	function getColidingTabs($sSourcePosition)
	{
        $this->aBoardMatrix = $this->__getBoardMatrix();
		// Unit Test & paramcheck
		//if( !$this->_getColisitionSource('0/0') || !$sSourcePosition )
		{
			//return false;
		}
        $this->oLog->log(__FILE__,__FUNCTION__,'process-0 (must be coordinate)',$sSourcePosition);
		$aSourceIndex 		= $this->_getColisitionSource($sSourcePosition);
		$sSourceColor 		= $this->aBoardMatrix[$aSourceIndex['row']][$aSourceIndex['col']];
        $this->oLog->log(__FILE__,__FUNCTION__,'process-1 (must be color)',$sSourceColor);
		$aDestinationMap	= $this->_getDestinationMap($aSourceIndex,$sSourceColor);
        $this->oLog->log(__FILE__,__FUNCTION__,'process-2 (must be coordinatesys)',serialize($aDestinationMap));
		if(!is_array($aDestinationMap))
		{
			return false;
		}
		return $aDestinationMap;
	}

	/**
	 * <b>_getColisitionSource</b><br/>
	 * returns array SourceIndex
	 */
	private function _getColisitionSource($sSourcePosition)
	{
		$aTmp = explode('/',$sSourcePosition);
		if(!is_array($aTmp))
		{
			return false;
		}
		$aSourceIndex['row'] = $aTmp[0];
		$aSourceIndex['col'] = $aTmp[1];
		return $aSourceIndex;
	}
	
	/**
	 * <b>_getDestinationMap</b><br/>
	 * return colided tabs with there new color
	 * @param array $aSourceIndex
	 * @param string $sSourceColor
	 */
	private function _getDestinationMap($aSourceIndex,$sSourceColor)
	{
		$aColidedTabs 	= $this->_setColidingTabs($aSourceIndex);
		$aTabs2Flip		= $this->_setFlipingTabs($aColidedTabs,$sSourceColor);
		return $aTabs2Flip;
	}
	
	private function _setFlipingTabs($aColidedTabs,$sSourceColor)
	{
		foreach($aColidedTabs as $iKey => $aTabInfo)
		{
            $this->oLog->log(__FILE__,__FUNCTION__,'process-5 (must be coordinate)',$aTabInfo['row'].'/'.$aTabInfo['col']);
            $this->oLog->log(__FILE__,__FUNCTION__,'process-6 (must be color)',$sSourceColor.' == '.$this->aBoardMatrix[$aTabInfo['row']][$aTabInfo['col']]);
			if($sSourceColor !== $this->aBoardMatrix[$aTabInfo['row']][$aTabInfo['col']])
			{
                $this->oLog->log(__FILE__,__FUNCTION__,'process-6.2 (must be bool)','TRUE');
				unset($aColidedTabs[$iKey]);
			}
		}
		return $aColidedTabs;
	}
	
	private function _setColidingTabs($aSourceIndex)
	{
		$aColidingTabIndex= array();
		$aColidingMethric = array(
				'West' 	=> '0/-1',
				'North'	=> '+1/0',
				'East'	=> '0/+1',
				'South' => '-1/0'
		);
		$i = 0;
		foreach($aColidingMethric as $sDirection => $sColidisionInfo)
		{
			$aColision = explode('/',$sColidisionInfo);
			$aColidingTabIndex[$i]['row'] 	= $aColision[0] == 0 ? $aSourceIndex['row'] 	: $this->calcWithString($aSourceIndex['row'],$aColision[0]);
			$aColidingTabIndex[$i]['col'] 	= $aColision[1] == 0 ? $aSourceIndex['col']     : $this->calcWithString($aSourceIndex['col'],$aColision[1]);
            $sLog = $aColision[0] == 0 ?  'r'.$aSourceIndex['row'] 	: $this->calcWithString($aSourceIndex['row'],$aColision[0]);
            $sLog .= '-';
            $sLog .= $aColision[1] == 0 ? 'r'.$aSourceIndex['col']  : $this->calcWithString($aSourceIndex['col'],$aColision[1]);
                $this->oLog->log(__FILE__,__FUNCTION__,'process-4 (must be coordinatesys)',$sLog);
            $i++;
		}
		return $aColidingTabIndex;
	}
}