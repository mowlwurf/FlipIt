<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dnaumann
 * Date: 05.08.12
 * Time: 13:27
 *
 *
 * Board
 *
 * parent of BoardGenerator, BoardHandler
 *
 * handles memcache connection for childclasses
 */

if (file_exists('../inc/Logger.php')) {
	require_once('../inc/Logger.php');
} elseif (file_exists('inc/Logger.php')) {
	require_once('inc/Logger.php');
}


class Board
{
	protected $memcacheConnection = null;

	public function __construct()
	{
		$this->logger = new Logger();
	}

	/**
	 * open memcache cc if theres none
	 *
	 * @return bool
	 */
	protected function getMemcacheConnection()
	{
		if ($this->memcacheConnection === null) {
			$this->memcacheConnection = new Memcached();
			$this->memcacheConnection->addServer("localhost", 11211);
			return true;
		} else {
			$this->logger->addRecord('Couldn\'t connect to memcache');
			return false;
		}
	}

	public function getBoardInformation()
	{
		$boardInformations            = array();
		$boardInformations['Board']   = $this->memcacheConnection->get('Board');
		$boardInformations['Player1'] = $this->memcacheConnection->get('Player1');
		return $boardInformations;
	}

	public function saveBoard($boardMatrix)
	{
		if (false === $this->memcacheConnection) {
			$this->logger->addRecord('Couldn\'t save boardmatrix to memcache');
			return false;
		}
		$this->memcacheConnection->set('Board', $boardMatrix);
	}

	public function getBoard()
	{
		return $this->memcacheConnection->get('Board');
	}

	protected function savePlayerField($iRow, $iCol)
	{
		$actualPlayer                                = $this->getPlayer();
		$actualPlayer['fields'][$iRow . '/' . $iCol] = array($iRow, $iCol);
		$this->memcacheConnection->set('Player1', $actualPlayer);
	}

	protected function savePlayer($actualPlayer)
	{
		$this->memcacheConnection->set('Player1', $actualPlayer);
	}

	protected function getPlayer()
	{
		return $this->memcacheConnection->get('Player1');
	}

	public function getPlayerPoints()
	{
		$player = $this->memcacheConnection->get('Player1');
		return $player['points'];
	}

	public function setPlayerPoints($points = 0)
	{
		$player = $this->getPlayer();
		$player['points'] += $points;
		$this->savePlayer($player);
	}
}
