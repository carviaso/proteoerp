<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" />
<title><?php echo $this->datasis->traevalor("SISTEMA") ?></title>
<?php echo style("ventanas.css");?>
<?php echo script("jquery.js") ?>
<?php if (isset($style))  echo $style;   ?>
<style type="text/css">
<?php if($tabla=="") {?>
#cajafiltro {width: 100%;display: block;padding: 5px;border: 2px solid #D0D0D0;background-color: #FDFdFD;}
<?php } else{?>
#cajafiltro {width: 100%;display: block;padding: 5px;border: 2px solid #D0D0D1;background-color: #FFFFFF;}	
<?php }?>
#mostrafiltro {display: block;width: 100%;padding: 5px;border: 2px solid #D0D0D0;background-color: #F0F0F0;}
</style>
<?php if (isset($head))   echo $head;   ?>
<?php if (isset($script)) echo $script; ?>

<script type="text/javascript">
$(function(){
	$("#mostrafiltro").click(function(event) {
		estado = $("#cajafiltro").css("display");
		//abre la ventana
		if(estado == "none"){
			$("#cajafiltro").show();
		}else{
			$("#cajafiltro").hide();
		}
	});
	
//$("#mostrar").click(function(event) {event.preventDefault();$("#cajafiltro").slideToggle();});
//$("#cajafiltro a").click(function(event) {event.preventDefault();$("#cajafiltro").slideUp();});
$("#cajafiltro").hide();
});

</script>
</head>
<body>
<div id='encabe'>
<center><?php if (isset($title)) echo $title; ?></center>
</div>
<?php if(isset($smenu)) echo '<div id="smenu">'.$smenu.'</div>'; ?>

<div id='contenido'>

<table width="95%" border=0 align="center">
	<tr>
		<td></td>
		<td><?php if (isset($filtro)) { ?>
		<div><a href="#" id="mostrafiltro">Filtro<?php echo image("", "#", array("border"=>"none")); ?></a>
		</div>
		<div id="cajafiltro"><?php echo $filtro.$tabla; ?></div>
		<?php } ?></td>
	</tr>
	<tr>
		<td valign=top><?php if (isset($lista)) echo $lista; ?></td>
		<td><?php if (isset($content)) echo $content; ?></td>
	</tr>
	<tr>
		<td colspan=4 align="center">
		<div class="footer">

		<p style='font-size: 10'>Tiempo de la consulta seg | Proteo ERP  |<a
			href="#" onClick="window.close()">Cerrar</a></p>

		</div>
		</td>
	</tr>
</table>
<?php if (isset($extras)) echo $extras; ?></div>
</body>
</html>