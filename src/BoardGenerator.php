<?php
require_once('Board.php');
/**
 * BoardGenerator
 * generates gameboard
 * @author d.naumann
 * @package flipit
 */
Class BoardGenerator extends Board{
	
	private $boardSize		= 25;		// BoardSize in fields
	
	private $colorCount	= 7;		// Count of different colors
	
	private $colorMap		= Array(	// Set of ingame colors
		'red','green','yellow','blue','purple','pink','cyan', 				// standard colors
		'brown','black','white','grey','rose','orange','violete',			// ext1 colors
		'silver','gold','grey','lightcyan','lightred','darkred','darkcyan'	// ext2 colors
	);
	
	public function __construct($size = null, $colors = null)
	{
		$this->getMemcacheConnection();
		if($this->memcacheConnection->get('board') === false)
		{
			if (null !== $size) {
				$this->setBoardSize($size);
			}

			if (null !== $colors) {
				$this->setColourCount($colors);
			}
			$this->generateBoard();
		}
	}

	private function setBoardSize($boardSize)
	{
		$this->boardSize = $boardSize;
	}

	private function generateBoard()
	{
		for($row=0; $row<$this->boardSize; $row++)
		{
			for($column=0; $column<$this->boardSize; $column++)
			{
				$boardMatrix[$row][$column] = $this->rndColor();
			}
		}
		$this->saveBoard($boardMatrix);
	}
	
	private function rndColor()
	{
		$rand = rand(0, $this->colorCount-1);
		return $this->colorMap[$rand];
	}

}