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
	private $menuItems = array(
		'Neues Spiel',
	);

	public function __construct()
	{

	}

	public function getMenuItems()
	{
		return $this->menuItems;
	}
}
