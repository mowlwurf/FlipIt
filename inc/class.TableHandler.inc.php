<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
/**
 * TableHandler
 * berechnet Spielkollisionen und liefert Spielfeldreaktionen
 * @author d.naumann
 * @package flipit
 */
Class TableHandler{
	
	function __construct($aBoardMatrix,$bDebug=false)
	{
		if($bDebug)
		{
			$oUtils -> UnitTest('0/0');
		}
		print_r($aBoardMatrix);
		$this->aBoardMatrix = $aBoardMatrix;
	}
	
	//TODO
	function calcWithString($iInt,$sString)
	{
		$aOperatorMap = Array('+','-');
		foreach($aOperatorMap as $sOperator)
		{
			$iValue = str_replace($sOperator, '', $sString);
			if(is_numeric($iValue))
			{
				switch($sOperator)
				{
					case '+': 	return $iInt + $iValue;
								break;
					case '-': 	return $iInt - $iValue;
								break;
				}
			}
		}
				
		if(is_numeric($iInt+$sString))
		{
			return $iInt+$sString;
		}
		return false;
	}
	
	function getColidingTabs($sSourcePosition)
	{
		return 'bla';exit;
		// Unit Test & paramcheck
		if( !$this->_getColisitionSource('0/0') || !$sSourcePosition )
		{
			return false;
		}	
		$aSourceIndex 		= $this->_getColisitionSource($sSourcePosition);
		return $_SESSION['aBoardMatrix'][0][0];exit;
		$sSourceColor 		= $this->aBoardMatrix[$aSourceIndex['row']][$aSourceIndex['column']];
		return $sSourceColor;exit;
		$aDestinationMap	= $this->_getDestinationMap($aSourceIndex,$sSourceColor);
		print_r($aDestinationMap);
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
			if($sSourceColor !== $this->aBoardmatrix[$aTabInfo['row']][$aTabInfo['col']])
			{
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
		
		foreach($aColidingMethric as $sDirection => $sColidisionInfo)
		{
			$aColision = explode('/',$sColidisionInfo);
			$aColidingTabIndex[]['row'] 	= $aColision[0] !== 0 ? $aSourceIndex['row'] 	: $this->calcWithString($aSourceIndex['row'],$aColision[0]);
			$aColidingTabIndex[]['col'] 	= $aColision[1] !== 0 ? $aSourceIndex['column'] : $this->calcWithString($aSourceIndex['column'],$aColision[0]);					
		}
		return $aColidingTabIndex;
	}
}