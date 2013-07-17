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

echo style('codemirror/dialog.css');
echo style('codemirror/docs.css');
?>
<style type="text/css">
.CodeMirror-fullscreen {
	display: block;
	position: absolute;
	top: 10; left: 0;
	height: 100%;
	width: 100%;
	z-index: 9999;
}
</style>
</head>
<body >

<form id='fprog' method='POST'>
<table width='100%'>
	<tr>
		<td>Tabla: <input id='bd' name='bd' value="<?php echo $bd; ?>" /></td>
		<td>Controlador: <input id='contro' name='contro' value="<?php echo $controlador; ?>"/></td>
		<td align='right'><a href='#' id='generar' style='color:black;font-size:10pt;' >Generar</a></td>
		<td align='right'><a href='#' id='guardar' style='color:black;font-size:10pt;' >Guardar al Archivo</a></td>
		<td align='right'><a href='#' id='cargar'  style='color:black;font-size:10pt;' >Cargar del Archivo</a></td>
	</tr>
</table>
<div >
<textarea id="code" name="code">
<?php echo $programa; ?>
</textarea>
</form>
</div>
<script>
function isFullScreen(cm) {
	return /\bCodeMirror-fullscreen\b/.test(cm.getWrapperElement().className);
}
function winHeight() {
	return window.innerHeight || (document.documentElement || document.body).clientHeight;
}
function setFullScreen(cm, full) {
	var wrap = cm.getWrapperElement();
	if (full) {
		wrap.className += " CodeMirror-fullscreen";
		wrap.style.height = winHeight() + "px";
		document.documentElement.style.overflow = "hidden";
	} else {
		wrap.className = wrap.className.replace(" CodeMirror-fullscreen", "");
		wrap.style.height = "";
		document.documentElement.style.overflow = "";
	}
	cm.refresh();
}
CodeMirror.on(window, "resize", function() {
	var showing = document.body.getElementsByClassName("CodeMirror-fullscreen")[0];
	if (!showing) return;
	showing.CodeMirror.getWrapperElement().style.height = winHeight() + "px";
});

var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
	lineNumbers: true,
	theme: "solarized",
	matchBrackets: true,
	mode: "application/x-httpd-php",
	indentUnit: 4,
	indentWithTabs: true,
	enterMode: "keep",
	tabMode: "shift",
	extraKeys: {
		"F6": function(cm) {
			setFullScreen(cm, !isFullScreen(cm));
		},
		"Esc": function(cm) {
			if (isFullScreen(cm)) setFullScreen(cm, false);
		}
	}
});

$(function() {
	$( "input[type=submit], a, button" ).button();
});

$('#guardar').click(function(){
	$('#fprog').attr("action","<?php echo site_url('/desarrollo/jqguarda') ?>");
	$('#fprog').submit();
});

$('#generar').click(function(){
	window.location = "<?php echo site_url('desarrollo/jqgrid'); ?>"+"/"+$('#bd').val()+"/"+$('#contro').val();
});

$('#cargar').click(function(){
	window.location = "<?php echo site_url('desarrollo/jqcargar'); ?>"+"/"+$('#bd').val()+"/"+$('#contro').val();
});


</script>
</body>
</html>
