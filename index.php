<?php
ini_set('session.save_handler', 'memcache');
//ini_set('session.save_path', 'tcp://127.0.0.1:11211');
session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', '1');

require('src/BoardGenerator.php');
require('src/MenuBar.php');

$boardGenerator = new BoardGenerator();
$menuBar        = new MenuBar();

$menuItems = $menuBar->getMenuItems();
$board     = $boardGenerator->getBoard();
?>

<html>
	<head>
	    <title>FlipIt!</title>
	    <script src="js/jquery-1.7.2.min.js"></script>
	    <script type="text/javascript">
			$(document).ready(function () {
				reloadColorSwitcher();
			});

			function startNewGame() {
				$.ajax({
					dataType:'json',
					type:"POST",
					url:'menuHandler.php',
					data:'action=newGame',
					success:function (rsp) {
						if (rsp.status === 'success') {
							<?php
								header('Location: http://www.devgarden.com/FlipIt');
							?>
						}
					}
				});
			}

			function reloadColorSwitcher() {
				$.ajax({
					dataType:'json',
					type:"POST",
					url:'functionHandler.php',
					data:'action=colorswitcher',
					success:function (rsp) {
						if (rsp.status == 'success') {
							$.each(rsp.data, function (i, v) {
								if (v !== null) {
									$('#' + v).show();
								}
							});
						}
						else {
							alert('ColorPicker konnte nicht geladen werden.');
						}
					}
				});
			}

			function reloadScoreTable(score) {
				$.ajax({
					dataType:'json',
					type:"POST",
					url:'functionHandler.php',
					data:'action=calc&score=' + score,
					success:function (rsp) {
						if (rsp.status == 'success') {
						}
						else {
							alert('ColorPicker konnte nicht geladen werden.');
						}
					}
				});
			}

			function flip(color) {
				$.ajax({
					dataType:'json',
					type:"POST",
					url:'functionHandler.php',
					data:'action=flip&color=' + color,
					beforeSend:function (data) {
					},
					success:function (rsp) {
						if (rsp.status == 'success') {
							//reloadScoreTable(rsp.count);
							$.each(rsp.data, function (i, v) {
								var iRow = v.row == null || v.row == '' ? 0 : v.row;
								var iCol = v.col == null || v.col == '' ? 0 : v.col;
								var sId = iRow + 'X' + iCol;
								$('#' + sId).css('background-color', color);
							});
						}
						else {
							alert('Fehler beim Berechnen der Kollision.');
						}
					}
				});
				reloadColorSwitcher();
			}

			function flipit(iRow, iColumn) {
				var index = iRow + '/' + iColumn;
				alert(index);
				$.ajax({
					dataType:'json',
					type:"POST",
					url:'functionHandler.php',
					data: 		'action=flip&index='+index,
					beforeSend:function (data) {
					},
					success:function (rsp) {
						$.each(rsp.data, function (i, v) {
							var sId = v.row + '/' + v.col;
							$('#' + sId).attribute('background-color', rsp.sourcecolor);
						});
					}
				});
			}
	    </script>
	</head>
	<body>
		<form action="" method="POST">

		</form>
		<table width="100%">
		    <tr>
				<?php
				foreach ($menuItems as $menuItem) {
					echo '<td style="border:1px solid black;padding:3px 1px 3px 1px;max-width:100px;"><input type="button" value="'.$menuItem.'" onclick="startNewGame()"></td>';
				}
				?>
		    </tr>
		</table>
		<hr>
		<table width="100%">
			<?php
			foreach ($board as $iRowNr => $aRow) {
				echo '<tr>';
				foreach ($aRow as $iKey => $sColumn) {
					echo '<td id="' . $iRowNr . 'X' . $iKey . '" style="background-color:' . $sColumn . ';" onClick="flipit(' . $iRowNr . ',' . $iKey . ')">&nbsp;</td>';
				}
				echo '</tr>';
			}
			?>
		</table>
		<table width="100%">
		    <tr>
				<!-- 'red','green','yellow','blue','purple','pink','cyan',-->
		        <td width="50px" height="50px" id="red" style="display:none;background-color:red;" onclick="flip('red')">
		            &nbsp;</td>
		        <td width="50px" height="50px" id="green" style="display:none;background-color:green;" onclick="flip('green')">
		            &nbsp;</td>
		        <td width="50px" height="50px" id="yellow" style="display:none;background-color:yellow;"
		            onclick="flip('yellow')">&nbsp;</td>
		        <td width="50px" height="50px" id="blue" style="display:none;background-color:blue;" onclick="flip('blue')">
		            &nbsp;</td>
		        <td width="50px" height="50px" id="purple" style="display:none;background-color:purple;"
		            onclick="flip('purple')">&nbsp;</td>
		        <td width="50px" height="50px" id="pink" style="display:none;background-color:pink;" onclick="flip('pink')">
		            &nbsp;</td>
		        <td width="50px" height="50px" id="cyan" style="display:none;background-color:cyan;" onclick="flip('cyan')">
		            &nbsp;</td>
		    </tr>
		</table>
	</body>
</html>