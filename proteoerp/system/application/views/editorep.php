<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Editor</title>
<?php
echo style('codemirror/codemirror.css');
echo style('codemirror/solarized.css');
echo style('themes/proteo/proteo.css');

echo script('jquery-min.js');
echo script('jquery-ui.custom.min.js');

echo script('codemirror/codemirror.js');
echo script('codemirror/matchbrackets.js');
echo script('codemirror/htmlmixed.js');

echo script('codemirror/xml.js');
echo script('codemirror/javascript.js');
echo script('codemirror/css.js');
echo script('codemirror/clike.js');
echo script('codemirror/php.js');
echo script('codemirror/search.js');
echo script('codemirror/searchcursor.js');
echo script('codemirror/dialog.js');
echo script('codemirror/fullscreen.js');

echo style('codemirror/dialog.css');
echo style('codemirror/docs.css');
?>
<style type="text/css">
.CodeMirror-fullscreen {
	display: block;
	position: absolute;
	top: 10; left: 0;bottom: 10;
	height: 100%; width: 100%;
	z-index: 9999;
}
.CodeMirror { border: 1px solid black; font-size:12px;height: 500px; width: 960px; }
.ui-tabs .ui-tabs-nav li a { padding: 0 0.8em; font-size: 0.7em; }

.btn {
  background: #3498db;
  background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
  background-image: -moz-linear-gradient(top, #3498db, #2980b9);
  background-image: -ms-linear-gradient(top, #3498db, #2980b9);
  background-image: -o-linear-gradient(top, #3498db, #2980b9);
  background-image: linear-gradient(to bottom, #3498db, #2980b9);
  -webkit-border-radius: 0;
  -moz-border-radius: 0;
  border-radius: 0px;
  font-family: Arial;
  color: #ffffff;
  font-size: 14px;
  padding: 2px 5px 2px 5px;
  text-decoration: none;
  width:100%;
}

.btn:hover {
  background: #3cb0fd;
  background-image: -webkit-linear-gradient(top, #3cb0fd, #3498db);
  background-image: -moz-linear-gradient(top, #3cb0fd, #3498db);
  background-image: -ms-linear-gradient(top, #3cb0fd, #3498db);
  background-image: -o-linear-gradient(top, #3cb0fd, #3498db);
  background-image: linear-gradient(to bottom, #3cb0fd, #3498db);
  text-decoration: none;
}
.btni {
  background: #3498db;
  background-image: -webkit-linear-gradient(top, #3498db, #2980b9);
  background-image: -moz-linear-gradient(top, #3498db, #2980b9);
  background-image: -ms-linear-gradient(top, #3498db, #2980b9);
  background-image: -o-linear-gradient(top, #3498db, #2980b9);
  background-image: linear-gradient(to bottom, #3498db, #2980b9);
  -webkit-border-radius: 0;
  -moz-border-radius: 0;
  border-radius: 0px;
  font-family: Arial;
  color: #ffffff;
  font-size: 14px;
  padding: 0px 0px 0px 0px;
  text-decoration: none;
  width:100%;
}

</style>
<script>
$(function() { $( "#tabs" ).tabs();	});
</script>
</head>
<body style='margin-top:0px;background: #000000;'>

<div id='contenido'>
	<table style='width:100%;border-collapse:collapse;padding:0px;align:center;'>
		<tr style='background: #3498db;'>
			<td style='text-align:center;background: #000000;color:#FFFF00;font-weight:bold'><?php if (isset($title)) echo $title; ?></td>
			<td><button name="btn_probar"  class="btn" type="button" onclick="window.open('/proteoerp/reportes/ver/<?php echo $title; ?>', '_blank', 'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)')" >Probar</button></td>
			<td><button name="btn_guardar" class="btn" type="button" onclick="fguardar()">Guardar a Archivo</button></td>
			<td><button name="btn_cargar"  class="btn" type="button" onclick="fcargar()">Cargar desde Archivo</button></td>
			<td><button name="btn_submit"  class="btn" type="button" onclick="guarda()" >Guardar</button></td>
			<td><button name="btn_undo"    class="btn" type="button" onclick="javascript:window.location='/proteoerp/supervisor/repomenu/filteredgrid'"><?php echo img(array('src' =>"assets/default/images/go-previous.png", 'height' => 12, 'alt'=>'Regresar', 'title' => 'Regresar', 'border'=>'0')); ?> Regresar</button></td>
		</tr>
	</table>
	<table width="100%" border=1 align="center">
		<tr>
			<td><?php if (isset($content)) echo $content; ?></td>
		</tr>
	</table>
</div>

<script>
function winHeight() {
	return window.innerHeight || (document.documentElement || document.body).clientHeight;
}

CodeMirror.on(window, "resize", function() {
	var showing = document.body.getElementsByClassName("CodeMirror-fullscreen")[0];
	if (!showing) return;
	showing.CodeMirror.getWrapperElement().style.height = winHeight() + "px";
});

var editor = CodeMirror.fromTextArea(document.getElementById("proteo"), {
	lineNumbers: true,
	theme: "solarized",
	matchBrackets: true,
	mode: "application/x-httpd-php",
	indentUnit: 3,
	indentWithTabs: true,
	enterMode: "keep",
	//tabMode: "shift",
	extraKeys: {
		"F11": function(cm) {
			cm.setOption("fullScreen", !cm.getOption("fullScreen"));
		},
		"Esc": function(cm) {
			if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
		}
	}
});

</script>
</body>
</html>
