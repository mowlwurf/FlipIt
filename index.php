<?php

include('inc/class.DBController.php');
include('inc/class.TableGen.inc.php');
include('inc/class.Players.inc.php');
$oTab  = new TableGen(True);
$oPlayer1 = new Players();
$aBoardMatrix = $oTab->getBoardMatrix();
?>

<html>
	<head>
		<title>FlipIt!</title>
		<script src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript">
        $(document).ready(function() {
            $.ajax({
                dataType:	'json',
                type: 		"POST",
                url: 		'tabhandler.php',
                data: 		'action=colorswitcher',
                beforeSend: function( data ) {
                },
                success: function(rsp)
                {
                    alert(rsp.status);
                    $.each(rsp.data, function(i, v) {
                        if(v !== null)
                        {
                            $('#'+v).show();
                        }
                    });
                }
            });
        });

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
					alert(rsp.status);
                    alert(rsp.sourcecolor);
                    $.each(rsp.data, function(i, v) {
                        var sId = v.row+'/'+v.col;
                        $('#'+sId).attribute('background-color',rsp.sourcecolor);
                    });
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
						echo '<td id="'.$iRowNr.'/'.$iKey.'" style="background-color:'.$sColumn.';" onClick="flipit('.$iRowNr.','.$iKey.')">&nbsp;</td>';
					}
					echo '</tr>';
				}		
			?>
		</table>
        <table width="100%">
            <tr>
                <!-- 'red','green','yellow','blue','purple','pink','cyan',-->
                <td width="50px" height="50px" id="red" style="display:none;background-color:red;">&nbsp;</td>
                <td width="50px" height="50px" id="green" style="display:none;background-color:green;">&nbsp;</td>
                <td width="50px" height="50px" id="yellow" style="display:none;background-color:yellow;">&nbsp;</td>
                <td width="50px" height="50px" id="blue" style="display:none;background-color:blue;">&nbsp;</td>
                <td width="50px" height="50px" id="purple" style="display:none;background-color:purple;">&nbsp;</td>
                <td width="50px" height="50px" id="pink" style="display:none;background-color:pink;">&nbsp;</td>
                <td width="50px" height="50px" id="cyan" style="display:none;background-color:cyan;">&nbsp;</td>
            </tr>
        </table>
	</body>
</html>