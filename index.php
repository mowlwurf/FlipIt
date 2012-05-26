<?php

include('inc/class.DBController.php');
include('inc/class.TableGen.inc.php');
include('inc/class.Players.inc.php');
$oTab  = new TableGen(True);
$oTab->saveBoard();
$oPlayer1 = new Players();
$aBoardMatrix = $oTab->getBoardMatrix();
?>

<html>
	<head>
		<title>FlipIt!</title>
		<script src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript">
        $(document).ready(function() {
            reloadColorSwitcher();
        });

        function reloadColorSwitcher()
        {
            $.ajax({
                dataType:	'json',
                type: 		"POST",
                url: 		'tabhandler.php',
                data: 		'action=colorswitcher',
                success: function(rsp)
                {
                    if(rsp.status == 'success')
                    {
                        $.each(rsp.data, function(i, v) {
                            if(v !== null)
                            {
                                $('#'+v).show();
                            }
                        });
                    }
                    else
                    {
                        alert('ColorPicker konnte nicht geladen werden.');
                    }
                }
            });
        }

        function flip(sColor)
        {
            alert(sColor);
            $.ajax({
                dataType:	'json',
                type: 		"POST",
                url: 		'tabhandler.php',
                data: 		'action=flip&color='+sColor,
                success: function(rsp)
                {
                    if(rsp.status == 'success')
                    {
                        $.each(rsp.data, function(i, v) {
                            var iRow = v.row == null || v.row == '' ? 0 : v.row;
                            var iCol = v.col == null || v.col == '' ? 0 : v.col;
                            var sId  = iRow+'X'+iCol;
                            alert(sId);
                            $('#'+sId).css('background-color', v.sourcecolor);
                        });
                    }
                    else
                    {
                        alert('Fehler beim Berechnen der Kollision.');
                    }
                }
            });
            reloadColorSwitcher();
        }

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
                    $.each(rsp.data, function(i, v) {
                        //alert(v.sourcecolor);
                        var sId = v.row+'/'+v.col;
                        alert(sId);
                        //$('#'+sId).attribute('background-color',v.sourcecolor);
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
						echo '<td id="'.$iRowNr.'X'.$iKey.'" style="background-color:'.$sColumn.';" onClick="flipit('.$iRowNr.','.$iKey.')">&nbsp;</td>';
					}
					echo '</tr>';
				}		
			?>
		</table>
        <table width="100%">
            <tr>
                <!-- 'red','green','yellow','blue','purple','pink','cyan',-->
                <td width="50px" height="50px" id="red" style="display:none;background-color:red;" onclick="flip('red')">&nbsp;</td>
                <td width="50px" height="50px" id="green" style="display:none;background-color:green;" onclick="flip('green')">&nbsp;</td>
                <td width="50px" height="50px" id="yellow" style="display:none;background-color:yellow;" onclick="flip('yellow')">&nbsp;</td>
                <td width="50px" height="50px" id="blue" style="display:none;background-color:blue;" onclick="flip('blue')">&nbsp;</td>
                <td width="50px" height="50px" id="purple" style="display:none;background-color:purple;" onclick="flip('purple')">&nbsp;</td>
                <td width="50px" height="50px" id="pink" style="display:none;background-color:pink;" onclick="flip('pink')">&nbsp;</td>
                <td width="50px" height="50px" id="cyan" style="display:none;background-color:cyan;" onclick="flip('cyan')">&nbsp;</td>
            </tr>
        </table>
	</body>
</html>