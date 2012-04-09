<?php


/**
 * TableHandler
 * berechnet Spielkollisionen und liefert Spielfeldreaktionen
 * @author d.naumann
 * @package flipit
 */
Class TableHandler{

    private $oDbController    = false;

    private $aBoardMatrix     = array();

    private $aColidingMethric = array(
                                        'West' 	=> '0/-1',
                                        'North'	=> '+1/0',
                                        'East'	=> '0/+1',
                                        'South' => '-1/0'
                                );

	function __construct($bDebug=false)
	{
        $this->oLog = new LogDoc();
        $this->oDBController = new DbController();
        if($bDebug)
        {
            //$oUtils -> UnitTest('0/0');
        }
        include('cnf/config.inc.php');
        $this->_readConfig($aConfig);
        $this->aBoardMatrix = $this->__getBoardMatrix();
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
        $this->oDBController->getConnection($this->dbUser,$this->dbPassword,$this->dbName,$this->dbServer);
        $this->oDBController->query('SELECT * FROM '.$this->active_table);
        $aBoardMatrix = $this->oDBController->getResult();
        $this->oDBController->clearCache();
        return unserialize($aBoardMatrix[0]['board']);
    }

    function getSourceColor()
    {
        return $this->sSourceColor;
    }

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

    function getColorSwitcher()
    {
        $this->aBoardMatrix     = $this->__getBoardMatrix();
        return $this->_getAvailableColors();
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
		$this->sSourceColor 		= $this->aBoardMatrix[$aSourceIndex['row']][$aSourceIndex['col']];
        $this->oLog->log(__FILE__,__FUNCTION__,'process-1 (must be color)',$this->sSourceColor);
		$aDestinationMap	= $this->_getDestinationMap($aSourceIndex,$this->sSourceColor);
        $this->oLog->log(__FILE__,__FUNCTION__,'process-2 (must be coordinatesys)',serialize($aDestinationMap));
		if(!is_array($aDestinationMap))
		{
			return false;
		}
		return $aDestinationMap;
	}


    private function _getAvailableColors()
    {
        $aPlayerFields    = $this->_getPlayerFields();
        $this->oLog->log(__FILE__,__FUNCTION__,'process-11 (must be colormap)',print_r($aPlayerFields,true));
        $aAvailableColors = Array();
        foreach($aPlayerFields as $iFieldNr => $aCoords)
        {
            $aCoords[0] = trim($aCoords[0]) == '' ? 0 : $aCoords[0];
            $aCoords[1] = trim($aCoords[1]) == '' ? 0 : $aCoords[1];
            foreach($this->aColidingMethric as $sDirection => $sConnectingInfo)
            {
                $aConnecting                = explode('/',$sConnectingInfo);
                $aConnectingTabIndex['row'] = $aConnecting[0] == 0 ? $aCoords[0] 	: $this->calcWithString($aCoords[0],$aConnecting[0]);
                $aConnectingTabIndex['col'] = $aConnecting[1] == 0 ? $aCoords[1]    : $this->calcWithString($aCoords[1],$aConnecting[1]);
                if(!in_array($this->aBoardMatrix[$aConnectingTabIndex['row']][$aConnectingTabIndex['col']],$aAvailableColors))
                {
                    $aAvailableColors[] = $this->aBoardMatrix[$aConnectingTabIndex['row']][$aConnectingTabIndex['col']];
                }
            }
        }
        $this->oLog->log(__FILE__,__FUNCTION__,'process-12 (must be colormap)',serialize($aAvailableColors));
        return $aAvailableColors;
    }

    private function _getPlayerFields()
    {
        $this->oDBController->getConnection($this->dbUser,$this->dbPassword,$this->dbName,$this->dbServer);
        $this->oDBController->query('SELECT `fields_player1` FROM '.$this->active_table);
        $aJson = $this->oDBController->getResult(); // todo SELECT one statment in dbhandler
        $this->oLog->log(__FILE__,__FUNCTION__,'process-11 (must be colormap)',print_r($aJson,true));
        $aActualPlayerFields=unserialize($aJson[0]['fields_player1']);
        return $aActualPlayerFields;
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
			if($sSourceColor == $this->aBoardMatrix[$aTabInfo['row']][$aTabInfo['col']])
			{
                $aFlipingTabs[] = $aColidedTabs[$iKey];
			}
		}
		return $aFlipingTabs;
	}
	
	private function _setColidingTabs($aSourceIndex)
	{
		$aColidingTabIndex= array();
		$i = 0;
		foreach($this->aColidingMethric as $sDirection => $sColidisionInfo)
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