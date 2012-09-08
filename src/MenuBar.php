<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dnaumann
 * Date: 05.08.12
 * Time: 23:50
 * To change this template use File | Settings | File Templates.
 */
class MenuBar
{
	/**
	 * contains menu items
	 *
	 * to be continued ..
	 *
	 * @var array
	 */
	private $menuItems = array();

	private $menuPlugins = array();

	public function __construct()
	{
		$this->setMenuItem('Neues Spiel', 'startNewGame');
		$this->renderScoreTable();
	}

	public function getMenuItems()
	{
		return $this->menuItems;
	}

	private function setMenuItem($name, $function)
	{
		$this->menuItems[$name] = $function;
	}

	public function getMenuPlugins()
	{
		return $this->menuPlugins;
	}

	private function setMenuPlugins($name, $form)
	{
		$this->menuPlugins[$name] = $form;
	}

	private function renderScoreTable()
	{
		$name = 'Punktestand';
		$mask = $this->generateScoreTable();
		$this->setMenuPlugins($name, $mask);
		return true;
	}

	private function generateScoreTable()
	{
		$form = '
					<td>Player1</td>
					<td><input type="text" id="scorePlayer1" value="0" /></td>
					<td>Player2</td>
					<td><input type="text" id="scorePlayer2" value="0" readonly="true"/></td>
				';
		return $form;
	}
}
