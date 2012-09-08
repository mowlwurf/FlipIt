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

$boardHandler = new BoardHandler();

switch ($action) {
	case 'colorswitcher':
	{
		$colors2Chose = $boardHandler->getColorSwitcher();
		$jSONAnswer   = Array('status' => 'success', 'data' => $colors2Chose);
		echo json_encode($jSONAnswer);
		break;
	}
	case 'flip':
	{
		$colisionTabs = $boardHandler->getColidingTabs($color);
		foreach($colisionTabs as $tabs2Check) {
			$board = $boardHandler->getBoard();
			$fieldColor = $board[$tabs2Check['row']][$tabs2Check['col']];
			if ($color == $fieldColor) {
				$boardHandler->getColidingTabs($fieldColor);
			}
		}
		$boardHandler->setPlayerPoints(count($colisionTabs));
		$sJSONAnswer  = Array('status' => 'success', 'data' => $colisionTabs, 'count' => count($colisionTabs));
		echo json_encode($sJSONAnswer);
		break;
	}
	case 'calc':
	{
		$boardHandler->setPlayerPoints($points);
		$sJSONAnswer = Array('status' => 'success', 'data' => $points);
		echo json_encode($sJSONAnswer);
		break;
	}
}





