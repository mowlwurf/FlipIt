<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dnaumann
 * Date: 06.08.12
 * Time: 00:28
 * To change this template use File | Settings | File Templates.
 */

//require('inc/MemcacheTools.php');

// action dispatcher control
$action = isset($_POST['action']) && trim($_POST['action']) != '' ? $_POST['action'] : $_GET['action'];

switch ($action) {
	case 'newGame':
	{
		$memReset = new Memcached();
		$memReset->addServer("localhost", 11211);
		$memReset->flush();
		echo json_encode(array('status' => 'success'));
		break;
	}
}