<?php
ini_set('session.save_handler', 'memcache');
//ini_set('session.save_path', 'tcp://127.0.0.1:11211');
session_start();
error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', '1');

require('inc/Logger.php');
require('src/BoardGenerator.php');
require('src/MenuBar.php');

//$logger         = new Logger();
$boardGenerator = new BoardGenerator();
$menuBar        = new MenuBar();

$menuItems = $menuBar->getMenuItems();
$menuPlugins = $menuBar->getMenuPlugins();
$board     = $boardGenerator->getBoard();
?>

<html>
	<head>
	    <title>FlipIt!</title>
	    <script src="js/jquery-1.7.2.min.js"></script>
	    <script type="text/javascript">
			$(document).ready(function () {
				reloadColorSwitcher(null);
			});

			function startNewGame() {
				$.ajax({
					dataType:'json',
					type:"POST",
					url:'menuHandler.php',
					data:'action=newGame',
					success:function (rsp) {
						if (rsp.status === 'success') {
							window.location.replace('http://devgarden.com/FlipIt/');
						}
					}
				});
			}

			function reloadColorSwitcher(color) {
				$.ajax({
					dataType:'json',
					type:"POST",
					url:'functionHandler.php',
					data:'action=colorswitcher',
					success:function (rsp) {
						var colors = Array('red','green','yellow','blue','purple','pink','cyan');

						if (rsp.status == 'success') {
							$.each(colors, function(k, sv) {
								$('#' + sv).hide();
							});
							$.each(rsp.data, function (i, v) {
								if (v !== null && color != v) {
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
				$('#scorePlayer1').val(score);
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
							$.each(rsp.data, function (i, v) {
								var iRow = v.row == null || v.row == '' ? 0 : v.row;
								var iCol = v.col == null || v.col == '' ? 0 : v.col;
								var sId = iRow + 'X' + iCol;
								$('#' + sId).css('background-color', color);
							});
							reloadScoreTable(rsp.count);
						}
						else {
							alert('Fehler beim Berechnen der Kollision.');
						}
					}
				});
				reloadColorSwitcher(color);
			}
		</script>
	</head>
	<body>
		<form action="" method="POST">

		</form>
		<div width="100%" style="border:1px inset #00008b;">
			<div width="50%" style="float:left;">
				<table width="100%">
					<tr>
						<?php
						foreach ($menuItems as $menuItem => $function) {
							echo '<td style="padding:3px 1px 3px 1px;max-width:100px;"><input type="button" value="'.$menuItem.'" onclick="'.$function.'()"></td>';
						}
						?>
					</tr>
				</table>
			</div>
			<div width="50%" style="float:left;">
				<table width="100%">
					<tr>
						<?php
						foreach ($menuPlugins as $pluginName => $pluginMask) {
							echo $pluginMask;
						}
						?>
					</tr>
				</table>
			</div>
		</div>
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