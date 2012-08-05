<?php
//error_reporting(E_ALL);
/**
 * Spielfeld 25*25 Felder
 *
 * 7 verschiedene Farben
 */
include('src/BoardHandler.php');
// optional Param $aConfig 
// aConfig = Array(
//		iBoardsize = 25
//		iColorCount= 7
// )
// this setting is used as default

/**
 * Control
 */
// action dispatcher control
$action = isset($_POST['action']) && trim($_POST['action']) != '' ? $_POST['action'] : $_GET['action'];
// index tabpositionhandler
$color = isset($_POST['color']) && trim($_POST['color']) != '' ? $_POST['color'] : false;
// points player has achieved
$points = isset($_POST['points']) && trim($_POST['points']) != '' ? $_POST['points'] : 0;

switch ($action) {
	case 'colorswitcher':
		{
		$boardHandler = new BoardHandler();
		$boardHandler->setStartView();
		$colors2Chose = $boardHandler->getColorSwitcher();
		$jSONAnswer   = Array('status' => 'success', 'data' => $colors2Chose);
		echo json_encode($jSONAnswer);
		break;
		}
	case 'flip':
		{
		$boardHandler = new BoardHandler();
		$colisionTabs = $boardHandler->getColidingTabs($color);
		$sJSONAnswer  = Array('status' => 'success', 'data' => $colisionTabs, 'count' => 2);
		echo json_encode($sJSONAnswer);
		break;
		}
	case 'calc':
		{
		$boardHandler = new BoardHandler();
		$boardHandler->setPlayerPoints($points);
		$sJSONAnswer = Array('status' => 'success', 'data' => $points);
		echo json_encode($sJSONAnswer);
		break;
		}
}





