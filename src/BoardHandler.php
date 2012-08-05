<?php
/**
 * TableHandler
 * berechnet Spielkollisionen und liefert Spielfeldreaktionen
 *
 * @author d.naumann
 * @package flipit
 */
require_once('Board.php');

Class BoardHandler extends Board
{

	private $boardInformation = array(
		'Board' => null,
		'Player1' => null
	);

	private $colidingMethric = array(
		'West'     => '0/-1',
		'North'    => '+1/0',
		'East'     => '0/+1',
		'South'    => '-1/0'
	);

	public function __construct()
	{
		$this->getMemcacheConnection();
		$this->boardInformation = $this->getBoardInformation();
	}

	function setStartView()
	{
		$this->savePlayerField(0, 0);
		return true;
	}

	function getSourceColor()
	{
		return $this->sSourceColor;
	}

	function calcWithString($iInt, $sString)
	{
		$aOperatorMap = Array('+', '-');
		foreach ($aOperatorMap as $sOperator) {
			if (strpos($sString, $sOperator) !== false) {
				$aTmp = explode($sOperator, $sString);
			}
			if (is_array($aTmp) && trim($aTmp[1]) != '') {
				break;
			}
		}

		switch ($sOperator) {
			case '+':
				return $iInt + $aTmp[1];
				break;
			case '-':
				return $iInt - $aTmp[1];
				break;
		}
		return false;
	}

	public function getColorSwitcher()
	{
		return $this->getAvailableColors();
	}

	public function getColidingTabs($sourceColor)
	{
		$playerFields     = $this->boardInformation['Player1']['fields'];
		$destinationMap   = $this->getDestinationMap($playerFields, $sourceColor);
		$key = 0;
		$this->saveDestinationMap($destinationMap);
		if (!is_array($destinationMap) || !is_array($playerFields)) {
			return false;
		}
		foreach ($playerFields as $coordinates) {
			$fields2Draw[$key]['row'] = $coordinates[0];
			$fields2Draw[$key]['col'] = $coordinates[1];
			$key++;
		}
		if (!is_array($fields2Draw)) {
			return false;
		}
		$this->updateBoardMatrix($fields2Draw, $sourceColor);
		return $fields2Draw;
	}

	private function updateBoardMatrix($fields2Draw, $sourceColor)
	{
		$board = $this->boardInformation['Board'];
		if (empty($board) || empty($fields2Draw)) {
			return false;
		}
		foreach ($fields2Draw as $field => $coordinates ) {
			$board[$coordinates['row']][$coordinates['col']] = $sourceColor;
		}
		$this->saveBoard($board);
		return true;
	}

	private function saveDestinationMap($aDestinationMap)
	{
		foreach ($aDestinationMap as $iKey => $aValue) {
			$this->savePlayerField($aValue['row'], $aValue['col']);
		}
	}

	private function getAvailableColors()
	{
		$player = $this->getPlayer();

		$availableColors = false;
		//startcords player1 0 & 0
		foreach ($player['fields'] as $fieldNr => $coordinates) {
			$coordinates[0] = trim($coordinates[0]) == '' ? 0 : $coordinates[0];
			$coordinates[1] = trim($coordinates[1]) == '' ? 0 : $coordinates[1];
			foreach ($this->colidingMethric as $direction => $connectingInfo) {
				$connectingMap                = explode('/', $connectingInfo);
				$connectingTabIndex['row'] = $connectingMap[0] == 0 ? $coordinates[0] : $this->calcWithString($coordinates[0], $connectingMap[0]);
				$connectingTabIndex['col'] = $connectingMap[1] == 0 ? $coordinates[1] : $this->calcWithString($coordinates[1], $connectingMap[1]);
				if (!$availableColors || !in_array($this->boardInformation['Board'][$connectingTabIndex['row']][$connectingTabIndex['col']], $availableColors)) {
					$availableColors[] = $this->boardInformation['Board'][$connectingTabIndex['row']][$connectingTabIndex['col']];
				}
			}
		}
		return $availableColors;
	}

	private function getDestinationMap($playerFields, $sourceColor)
	{
		$colidedTabs = $this->setColidingTabs($playerFields);
		$tabs2Flip   = $this->setFlipingTabs($colidedTabs, $sourceColor);
		return $tabs2Flip;
	}

	private function setFlipingTabs($colidedTabs, $sourceColor)
	{
		foreach ($colidedTabs as $key => $tabInfo) {
			if ($sourceColor == $this->boardInformation['Board'][$tabInfo['row']][$tabInfo['col']]) {
				$flipingTabs[] = $colidedTabs[$key];
			}
		}
		return $flipingTabs;
	}

	private function setColidingTabs($playerFields)
	{
		$colidingTabIndex = array();
		$i                 = 0;
		foreach ($playerFields as $iKey => $coordinates) {
			foreach ($this->colidingMethric as $direction => $colisionInfo) {
				$colision                    = explode('/', $colisionInfo);
				$row                         = trim($coordinates[0]) == '' ? 0 : $coordinates[0];
				$col                         = trim($coordinates[1]) == '' ? 0 : $coordinates[1];
				$colidingTabIndex[$i]['row'] = $colision[0] == 0 ? $row : $this->calcWithString($row, $colision[0]);
				$colidingTabIndex[$i]['col'] = $colision[1] == 0 ? $col : $this->calcWithString($col, $colision[1]);
				$i++;
			}
		}
		return $colidingTabIndex;
	}
}