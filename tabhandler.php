<?php
//error_reporting(E_ALL);
//ini_set('display_errors','1');
/**
 * Spielfeld 25*25 Felder
 * 
 * 7 verschiedene Farben
 */
include('inc/class.DbController.php');
include('inc/class.TableHandler.inc.php');
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
$sAction = isset($_POST['action']) && trim($_POST['action']) != '' ? $_POST['action'] : $_GET['action'];
// index tabpositionhandler
$sColor = isset($_POST['color']) && trim($_POST['color']) != '' ? $_POST['color'] : $_GET['color'];
 		
switch($sAction)
{
	case 'colorswitcher':
    {
        $oTabhandler   = new TableHandler();
        $oTabhandler->setStartView();
        $aColors2Chose = $oTabhandler->getColorSwitcher();
        $sJSONAnswer = Array('status' => 'success','data' => $aColors2Chose);
        echo json_encode($sJSONAnswer);
        break;
    }
	case 'flip':
    {
        $oTabhandler = new TableHandler();
        $aColisionTabs = $oTabhandler->getColidingTabs($sColor);
        $sJSONAnswer = Array('status' => 'success','data' => $aColisionTabs);
        echo json_encode($sJSONAnswer);
        break;
    }
}





