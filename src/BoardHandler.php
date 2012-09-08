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
		'Board'   => null,
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
		parent::__construct();
		if ($this->memcacheConnection === null) {
			$this->getMemcacheConnection();
			$this->setStartView();
		}
		$this->boardInformation = $this->getBoardInformation();
	}

	private function setStartView()
	{
		$this->savePlayerField(0, 0);
		return true;
	}

	public function getSourceColor()
	{
		return $this->sSourceColor;
	}

	private function calcWithString($int, $string)
	{
		$tmp = Array();
		$operatorMap = Array('+', '-');
		foreach ($operatorMap as $operator) {
			if (strpos($string, $operator) !== false) {
				$tmp = explode($operator, $string);
			}
			if (is_array($tmp) && !empty($tmp[1])) {
				break;
			}
		}

		switch ($operator) {
			case '+':
				return $int + $tmp[1];
				break;
			case '-':
				return $int - $tmp[1];
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
		$player   = $this->getPlayer();
		$destinationMap = $this->getDestinationMap($player['fields'], $sourceColor);
		$key            = 0;
		$this->saveDestinationMap($destinationMap);
		if (!is_array($destinationMap) || !is_array($player['fields'])) {
			return false;
		}
		foreach ($player['fields'] as $coordinates) {
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
		foreach ($fields2Draw as $field => $coordinates) {
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
		$this->logger->addRecord('PlayerFields - '.serialize($player['fields']));
		$availableColors = array();
		//startcords player1 0 & 0
		foreach ($player['fields'] as $fieldNr => $coordinates) {
			$coordinates[0] = trim($coordinates[0]) == '' ? 0 : $coordinates[0];
			$coordinates[1] = trim($coordinates[1]) == '' ? 0 : $coordinates[1];
			foreach ($this->colidingMethric as $direction => $connectingInfo) {
				$connectingMap             = explode('/', $connectingInfo);
				$connectingTabIndex['row'] = $connectingMap[0] == 0 ? $coordinates[0] : $this->calcWithString($coordinates[0], $connectingMap[0]);
				$connectingTabIndex['col'] = $connectingMap[1] == 0 ? $coordinates[1] : $this->calcWithString($coordinates[1], $connectingMap[1]);
				if ($connectingTabIndex['row'] >= 0 && $connectingTabIndex['col'] >= 0) {
					if (!$availableColors || !in_array($this->boardInformation['Board'][$connectingTabIndex['row']][$connectingTabIndex['col']], $availableColors)) {
						$availableColors[] = $this->boardInformation['Board'][$connectingTabIndex['row']][$connectingTabIndex['col']];
					}
				}
			}
		}
		$this->logger->addRecord('AvailableColors - '.serialize($availableColors));
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
		$flipingTabs = array();
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
		$i                = 0;
		foreach ($playerFields as $iKey => $coordinates) {
			foreach ($this->colidingMethric as $direction => $colisionInfo) {
				$colision                    = explode('/', $colisionInfo);
				$row                         = trim($coordinates[0]) == '' ? 0 : $coordinates[0];
				$col                         = trim($coordinates[1]) == '' ? 0 : $coordinates[1];

				$colidingTabDummy['row'] = $colision[0] == 0 ? $row : $this->calcWithString($row, $colision[0]);
				$colidingTabDummy['col'] = $colision[1] == 0 ? $col : $this->calcWithString($col, $colision[1]);
				if ($colidingTabDummy['row'] >= 0 && $colidingTabDummy['col'] >= 0) {
					if (!in_array($colidingTabDummy, $colidingTabIndex)) {
						$colidingTabIndex[$i] = $colidingTabDummy;
						$i++;
					}
				}
			}
		}
		return $colidingTabIndex;
	}
}