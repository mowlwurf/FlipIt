<?php
/**
 * TableHandler
 * berechnet Spielkollisionen und liefert Spielfeldreaktionen
 * @author d.naumann
 * @package flipit
 */
include('inc/class.LogDoc.inc.php');

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
        $this->oDBController = new DbController();
        $this->oLog = new LogDoc();

        if($bDebug)
        {
            //$oUtils -> UnitTest('0/0');
        }
	}

    function setStartView()
    {
        require('cnf/config.inc.php');
        if(!is_array($aConfig))
        {
            return NULL;
        }
        $this->_readConfig($aConfig);
        $this->aBoardMatrix = $this->__getBoardMatrix();
        $this->_setPlayerField(0,0);
        if(!is_array($this->aBoardMatrix))
        {
            return NULL;
        }
        return true;
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
        $this->oDBController = new DbController();
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

	function getColidingTabs($sSourceColor)
	{
        $this->aBoardMatrix = $this->__getBoardMatrix();
		// Unit Test & paramcheck
		//if( !$this->_getColisitionSource('0/0') || !$sSourcePosition )
		{
			//return false;
		}
        $aPlayerFields      = $this->_getPlayerFields();
		$aDestinationMap	= $this->_getDestinationMap($aPlayerFields,$sSourceColor);
        $this->_saveDestinationMap($aDestinationMap);
        foreach($aPlayerFields as $iKey => $aCoords)
        {
            $aFields2Draw[$iKey]['row']         = $aCoords[0];
            $aFields2Draw[$iKey]['col']         = $aCoords[1];
            $aFields2Draw[$iKey]['sourcecolor'] = $sSourceColor;
        }
		if(!is_array($aDestinationMap) || !is_array($aFields2Draw))
		{
			return false;
		}
        $this->_updateBoardMatrix($aFields2Draw,$sSourceColor);
		return $aFields2Draw;
	}

    private function _updateBoardMatrix($aFields2Draw,$sSourceColor)
    {
        $this->oLog->log(__FILE__,__FUNCTION__,'process-22 (must be $aFields2Draw)',print_r($aFields2Draw,true));
        foreach($aFields2Draw as $aCoords)
        {
            $aCoords['row'] = $aCoords['row'] == '' ? 0 : $aCoords['row'];
            $aCoords['col'] = $aCoords['col'] == '' ? 0 : $aCoords['col'];
            $this->oLog->log(__FILE__,__FUNCTION__,'process-22 (must be $aCoords)',$aCoords['row'].'-'.$aCoords['col']);
            $this->aBoardMatrix[$aCoords['row']][$aCoords['col']] = $sSourceColor;
        }
        $sUpdatedBoardMatrix = serialize($this->aBoardMatrix);
        $this->oLog->log(__FILE__,__FUNCTION__,'process-22 (must be json)',$sUpdatedBoardMatrix);
        $this->oDBController->getConnection($this->dbUser,$this->dbPassword,$this->dbName,$this->dbServer);
        $this->oDBController->query('UPDATE '.$this->active_table.' SET board = \''.$sUpdatedBoardMatrix.'\'');
    }

    private function _saveDestinationMap($aDestinationMap)
    {
        foreach($aDestinationMap as $iKey => $aValue)
        {
            $this->oLog->log(__FILE__,__FUNCTION__,'process-11 (must be colormap)',print_r($aValue,true));
            $this->_setPlayerField($aValue['row'],$aValue['col']);
        }
    }

    private function _getAvailableColors()
    {
        $aPlayerFields    = $this->_getPlayerFields();
        if(!is_array($aPlayerFields))
            return false;

        if(!is_array($this->aBoardMatrix[0]))
            return NULL;


        $aAvailableColors = false;
        //startcords player1 0 & 0
        foreach($aPlayerFields as $iFieldNr => $aCoords)
        {
            $aCoords[0] = trim($aCoords[0]) == '' ? 0 : $aCoords[0];
            $aCoords[1] = trim($aCoords[1]) == '' ? 0 : $aCoords[1];
            foreach($this->aColidingMethric as $sDirection => $sConnectingInfo)
            {
                $aConnecting                = explode('/',$sConnectingInfo);
                $aConnectingTabIndex['row'] = $aConnecting[0] == 0 ? $aCoords[0] 	: $this->calcWithString($aCoords[0],$aConnecting[0]);
                $aConnectingTabIndex['col'] = $aConnecting[1] == 0 ? $aCoords[1]    : $this->calcWithString($aCoords[1],$aConnecting[1]);
                if(!$aAvailableColors || !in_array($this->aBoardMatrix[$aConnectingTabIndex['row']][$aConnectingTabIndex['col']],$aAvailableColors))
                {
                    $aAvailableColors[] = $this->aBoardMatrix[$aConnectingTabIndex['row']][$aConnectingTabIndex['col']];
                }
            }
        }
        return $aAvailableColors;
    }

    //TODO PlayerClass functions going to be outsourced
    private function _getPlayerFields()
    {
        $this->oDBController->getConnection($this->dbUser,$this->dbPassword,$this->dbName,$this->dbServer);
        $this->oDBController->query('SELECT `fields_player1` FROM '.$this->active_table);
        $aJson = $this->oDBController->getResult(); // todo SELECT one statment in dbhandler
        $aActualPlayerFields=unserialize($aJson[0]['fields_player1']);
        return $aActualPlayerFields;
    }

    private function _setPlayerField($iRow,$iCol)
    {
        $aActualPlayerFields=$this->_getPlayerFields();
        if(!is_array($aActualPlayerFields))
        {
            $aActualPlayerFields = array('0' => array('0'=>$iRow,'1'=>$iCol));
        }
        else
        {
            $aActualPlayerFields[] = array('0'=>$iRow,'1'=>$iCol);
        }
        $this->oDBController->query('UPDATE '.$this->active_table.' SET `fields_player1` = \''.serialize($aActualPlayerFields).'\', `points_player1` = `points_player1` + 1');
    }
    // TODO END
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
	private function _getDestinationMap($aPlayerFields,$sSourceColor)
	{
		$aColidedTabs 	= $this->_setColidingTabs($aPlayerFields);
		$aTabs2Flip		= $this->_setFlipingTabs($aColidedTabs,$sSourceColor);
		return $aTabs2Flip;
	}
	
	private function _setFlipingTabs($aColidedTabs,$sSourceColor)
	{
		foreach($aColidedTabs as $iKey => $aTabInfo)
		{
			if($sSourceColor == $this->aBoardMatrix[$aTabInfo['row']][$aTabInfo['col']])
			{
                $aFlipingTabs[] = $aColidedTabs[$iKey];
			}
		}
		return $aFlipingTabs;
	}
	
	private function _setColidingTabs($aPlayerFields)
	{
		$aColidingTabIndex= array();
		$i = 0;
        foreach($aPlayerFields as $iKey => $aCoords)
        {
            foreach($this->aColidingMethric as $sDirection => $sColidisionInfo)
            {
                $aColision = explode('/',$sColidisionInfo);
                $iRow      = trim($aCoords[0]) == '' ? 0 : $aCoords[0];
                $iCol      = trim($aCoords[1]) == '' ? 0 : $aCoords[1];
                $aColidingTabIndex[$i]['row'] 	= $aColision[0] == 0 ? $iRow : $this->calcWithString($iRow,$aColision[0]);
                $aColidingTabIndex[$i]['col'] 	= $aColision[1] == 0 ? $iCol : $this->calcWithString($iCol,$aColision[1]);
                $i++;
            }
        }
		return $aColidingTabIndex;
	}
}