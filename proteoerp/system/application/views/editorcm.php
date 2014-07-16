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
.CodeMirror { border: 1px solid black; font-size:13px;height: auto; }
.ui-tabs .ui-tabs-nav li a { padding: 0 0.8em; font-size: 0.7em; }
</style>
<script>
$(function() { $( "#tabs" ).tabs();	});
</script>
</head>
<body >

<div id="tabs">
<ul>
	<li><a href="#tabs-1">Programa</a></li>
	<li><a href="#tabs-2">Vista</a></li>
	<li><a href="#tabs-3">Reporte</a></li>
</ul>

<div id="tabs-1">
	<form id='fprog' method='POST'>
	<table width='100%'>
	<tr>
		<td>Tabla: <input id='bd' name='bd' value="<?php echo $bd; ?>" /></td>
		<td>Controlador: <input id='contro' name='contro' value="<?php echo $controlador; ?>"/></td>
		<td align='right'><a href='#' id='generar' style='color:black;font-size:10pt;'>Generar</a></td>
		<td align='right'><a href='#' id='guardar' style='color:black;font-size:10pt;'>Guardar</a></td>
		<td align='right'><a href='#' id='cargar'  style='color:black;font-size:10pt;'>Cargar </a></td>
	</tr>
	</table>
	<div>
		<textarea id="code" name="code"><?php echo $programa; ?></textarea>
	</div>
	</form>
</div>

<div id="tabs-2">
	<form id='vprog' method='POST'>
	<table width='100%'>
	<tr>
		<td>Vista: <input id='vbd' name='vbd' value="<?php echo $vbd; ?>" /></td>
		<td align='right'><a href='#' id='vgenerar' style='color:black;font-size:10pt;' >Generar</a></td>
		<td align='right'><a href='#' id='vguardar' style='color:black;font-size:10pt;' >Guardar</a></td>
		<td align='right'><a href='#' id='vcargar'  style='color:black;font-size:10pt;' >Cargar </a></td>
	</tr>
	</table>
	<div>
		<textarea id="view" name="view"><?php echo $vista; ?></textarea>
	</div>
	</form>
</div>

<div id="tabs-3">
	<form id='rprog' method='POST'>
	<table width='100%'>
	<tr>
		<td>Reporte: <input id='rbd' name='rbd' value="<?php echo $rbd; ?>" /></td>
		<td align='right'><a href='#' id='rgenerar' style='color:black;font-size:10pt;' >Generar</a></td>
		<td align='right'><a href='#' id='rguardar' style='color:black;font-size:10pt;' >Guardar</a></td>
		<td align='right'><a href='#' id='rcargar'  style='color:black;font-size:10pt;' >Cargar </a></td>
	</tr>
	</table>
	<div>
		<textarea id="repo" name="repo"><?php echo $reporte; ?></textarea>
	</div>
	</form>

</div>
</div>

<script>
/*
function isFullScreen(cm) {
	return /\bCodeMirror-fullscreen\b/.test(cm.getWrapperElement().className);
}
*/
function winHeight() {
	return window.innerHeight || (document.documentElement || document.body).clientHeight;
}
/*
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
*/

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

var vista = CodeMirror.fromTextArea(document.getElementById("view"), {
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

var reporte = CodeMirror.fromTextArea(document.getElementById("repo"), {
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

$(function() {
	$( "input[type=submit], a, button" ).button();
});

$('#guardar').click(function(){

	var forma = $('#fprog');
	$('#code').val(editor.getValue()); 

	forma.submit(function(event) {
		event.preventDefault();
		$.ajax({
			type: forma.attr('method'),
			url: '<?php echo site_url('desarrollo/jqguarda'); ?>',
			data: forma.serialize(),
			success: function(resulta){ alert(resulta);}		
		}).done( function() {
			alert('Guardado');
		}).fail( function(){
			alert('Error');
		});
		return false;
	});

	forma.submit();

});

$('#generar').click(function(){
	//alert('Generar '+$('#bd').val()+" "+$('#contro').val());
	window.location = "<?php echo site_url('desarrollo/jqgrid'); ?>"+"/"+$('#bd').val()+"/"+$('#contro').val();
});

$('#cargar').click(function(){
	window.location = "<?php echo site_url('desarrollo/jqcargar'); ?>"+"/"+$('#bd').val()+"/"+$('#contro').val();
});

</script>
</body>
</html>
