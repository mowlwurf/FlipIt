<?php

/**
 * Spielfeld 25*25 Felder
 * 
 * 7 verschiedene Farben
 */

include('inc/class.TableGen.inc.php');
// optional Param $aConfig 
// aConfig = Array(
//		iBoardsize = 25
//		iColorCount= 7
// )
// this setting is used as default

switch($_POST['action'])
{
	default:
	case '':		// initialise TableGen with optional Config
					$oTab = new TableGen();	
					// get generated boardinformations
					$aBoardMatrix = $oTab->getBoardMatrix();
					break;
					
	case 'flip':	print json_result('success', $_POST['index']);
					exit; 
					break;
}




