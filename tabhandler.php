<?php
//error_reporting(E_ALL);
//ini_set('display_errors','1');
/**
 * Spielfeld 25*25 Felder
 * 
 * 7 verschiedene Farben
 */
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
$sIndex = isset($_POST['index']) && trim($_POST['index']) != '' ? $_POST['index'] : false;

$oLog = new LogDoc();
 		
switch($sAction)
{
	default:
	case false:		break;
					
	case 'flip':	$oTabhandler = new TableHandler();
					$aColisionTabs = $oTabhandler->getColidingTabs($sIndex);
                    $oLog->log(__FILE__,__FUNCTION__,'process-99 (must be coordinate)',print_r($aColisionTabs,true));
                    $sAnswer = $aColisionTabs[0]['row'].'/'.$aColisionTabs[0]['col'];
					$sJSONAnswer = Array('status' => 'success','data' => $aColisionTabs);
					echo json_encode($sJSONAnswer);
					break;
}





