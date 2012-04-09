<?php
//error_reporting(E_ALL);
//ini_set('display_errors','1');
/**
 * Spielfeld 25*25 Felder
 * 
 * 7 verschiedene Farben
 */
include('inc/class.DBController.php');
include('inc/class.LogDoc.inc.php');
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
$sAction = isset($_POST['action']) && trim($_POST['action']) != '' ? $_POST['action'] : false;
// index tabpositionhandler
$sColor = isset($_POST['color']) && trim($_POST['color']) != '' ? $_POST['color'] : false;

$oLog = new LogDoc();
 		
switch($sAction)
{
	default:
	case false:		break;
	case 'colorswitcher':
    {
        $oTabhandler   = new TableHandler();
        $aColors2Chose = $oTabhandler->getColorSwitcher();
        $sJSONAnswer = Array('status' => 'success','data' => $aColors2Chose);
        echo json_encode($sJSONAnswer);
        break;
    }
	case 'flip':
    {
        $oTabhandler = new TableHandler();
        $aColisionTabs = $oTabhandler->getColidingTabs($sColor);
        $oLog->log(__FILE__,__FUNCTION__,'process-99 (must be coordinate)',print_r($aColisionTabs,true));
        $sJSONAnswer = Array('status' => 'success','data' => $aColisionTabs);
        echo json_encode($sJSONAnswer);
        break;
    }
}





