<?php
//include_once('class.DBController.php');
//$oUtils = new Utils();
/**
 * TableGen
 * generiert Spielfeld
 * @author d.naumann
 * @package flipit
 */
Class TableGen{
	private $aBoardMatrix 	= Array(); 	// BoardMap containing colordata of fields
	
	private $iBoardSize		= 25;		// BoardSize in fields
	
	private $iColorCount	= 7;		// Count of different colors
	
	private $aColorMap		= Array(	// Set of ingame colors
		'red','green','yellow','blue','purple','pink','cyan', 				// standard colors
		'brown','black','white','grey','rose','orange','violete',			// ext1 colors
		'silver','gold','grey','lightcyan','lightred','darkred','darkcyan'	// ext2 colors
	);	
	//TODO
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
	
	function __construct($bGenerate = False,$aConfig = Array())
	{
		if(!$this->is_empty($aConfig))
		{
			$this->_readConfig($aConfig);
		}
		if($bGenerate)
		{
			$this->_generateBoard();
		}
        $this->oDBController = new DbController();
        $this->__saveBoardMatrix($this->aBoardMatrix);
	}
	
	/** 
	 * <b>BoardConfigHandler</b><br/>
	 * BoardConfig handled by constructor
	 */
	function getBoardMatrix()
	{
		return $this->aBoardMatrix;
	}

    function __saveBoardMatrix($aBoardMatrix)
    {
        $sBoardMatrix = serialize($aBoardMatrix);
        $iBoardSize   = $this->iBoardSize;
        $iColors      = $this->iColorCount;
        $this->oDBController->getConnection('root','sonada86','flipit');
        //TRUNCATE TABLE  `actual_board`
        $this->oDBController->query('TRUNCATE TABLE  `actual_board`');
        $this->oDBController->query('INSERT INTO actual_board VALUES (\''.$sBoardMatrix.'\',\''.$iBoardSize.'\',\''.$iColors.'\')');
        $this->oDBController->clearCache();
    }

	private function _generateBoard()
	{
		for($iRow=0;$iRow<$this->iBoardSize;$iRow++)
		{
			for($iColumn=0;$iColumn<$this->iBoardSize;$iColumn++)
			{
				$this->aBoardMatrix[$iRow][$iColumn] = $this->_rndColor();
			}
		}
		//$_SESSION['aBoardMatrix'] = $this->aBoardMatrix;
	}
	
	private function _readConfig($aConfig)
	{
		if(!is_array($aConfig) || $this->is_empty($aConfig))
		{
			return false;
		}
		foreach($aConfig as $sKey => $sValue)
		{
			$this->$sKey = $sValue;
		}
		return true;
	}
	
	private function _rndColor()
	{
		if(!isset($this->iColorCount))	return false;
		$iRand = rand(0,$this->iColorCount-1);
		return $this->aColorMap[$iRand];
	}

}