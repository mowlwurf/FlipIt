<?php
include('class.TableGen.inc.php');
/**
 * Utils
 * liefert custom global functions
 * @author d.naumann
 * @package global
 */

Class Utils extends TableGen{
	/**
	 * <b>priv _is_empty</b><br/>
	 * checks if an given array is empty
	 * @param Array $aArray
	 */
	function is_empty($aArray)
	{
		if(!is_array($aArray))
		{
			return true;
		}
		foreach($aArray as $sPartString)
		{
			if(trim($sPartString) != '')
			{
				return false;
			}
		}
		return true;
	}
	
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

}