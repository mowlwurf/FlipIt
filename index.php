<?php

include('inc/class.DBController.php');
include('inc/class.TableGen.inc.php');
$oTab = new TableGen(True);
$oDBController = new DbController();
$aBoardMatrix = $oTab->getBoardMatrix();
$oDBController->getConnection('root','sonada86','flipit');
$oDBController->query('SELECT * FROM actual_board');
$aBoardMatrixTest = $oDBController->getResult();
$oDBController->clearCache();
$test =unserialize($aBoardMatrixTest[0]['board']);
print_r($test[0][0]);
?>

<html>
	<head>
		<title>FlipIt!</title>
		<script src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript">
		function flipit(iRow,iColumn)
		{
			var index = iRow + '/' + iColumn;
			alert(index);
			$.ajax({
				dataType:	'json',
				type: 		"POST",
				url: 		'tabhandler.php',
				data: 		'action=flip&index='+index,
			 	beforeSend: function( data ) {
				},
				success: function(rsp)
				{
					//var data = eval('(' + rsp + ')');
					alert(rsp.status);
				}
			});
		}
		</script>
	</head>
	<body>
		<table width="100%">
			<?php 
				foreach($aBoardMatrix as $iRowNr => $aRow)
				{
					echo '<tr>';
					foreach($aRow as $iKey => $sColumn)
					{
						echo '<td style="background-color:'.$sColumn.';" onClick="flipit('.$iRowNr.','.$iKey.')">&nbsp;</td>';
					}
					echo '</tr>';
				}		
			?>
		</table>
	</body>
</html>