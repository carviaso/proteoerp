<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>ProteoERP<?php if(isset($title)) echo ':'.preg_replace('/<[^>]*>/', '', $title); ?></title>
<?php 
echo style("ventanas.css");
if (isset($style))  echo $style;
if (!isset($target)) $target='popu'; 
if (isset($filtro)) { ?>
<style type="text/css">
#cajafiltro {width: 100%;display: block;padding: 5px;border-bottom: 1px solid #2067B5;background-color: #D7DEF0;}
#mostrafiltro {display: block;width: 100%;padding: 5px;border-bottom: 1px solid #2067B5;background-color: #F5F5F5;background:url(<?php echo base_url();?>images/huellaazul.gif);}
</style>
<?php }; ?>

<?php if(!isset($tabla)) $tabla='';    ?>
<?php if(isset($head))   echo $head;   ?>
<?php if(isset($script)) echo $script; ?>

<?php if (isset($filtro)) { ?>
<script type="text/javascript">
$(function(){
	$("#mostrafiltro").click(function(event) {
		estado = $("#cajafiltro").css("display");
		if(estado == "none"){ $("#cajafiltro").show();
		}else{ $("#cajafiltro").hide();}
	});
	$("#cajafiltro").hide();
});
</script>
<?php }; ?>
</head>
<body>
<?php 
if ( $target != 'dialogo' ) { 
?>
<div id='encabe'>
<?php if (isset($title)) { ?>
<table width="98%">
	<tr>
		<td><?php echo $title ?></td>
		<td align="right" width="40"><?php echo image('cerrar.png','Cerrar Ventana',array('onclick'=>'window.close()','height'=>'20')); ?></td>
	</tr>
</table>
<?php }; ?>
</div>
<?php 
} 
if(isset($smenu)) echo '<div id="smenu">'.$smenu.'</div>'; 
?>
<div id='contenido'>
	<table width="100%" border=0 align="center">
		<?php if (isset($filtro)) { ?>
		<tr>
			<td colspan='2'>
				<div>
					<a style="text-decoration:none;color:#4F1010;font: bold 18px Verdana;" href="#" id="mostrafiltro"><?php echo image("huella.jpg", "#", array("border"=>"none")); ?> Buscar y Filtrar </a>
				</div>
				<div id="cajafiltro"><?php echo $filtro.$tabla; ?></div>
			</td>
		</tr>
		<?php } ?>

		<?php if (isset($subtitle)) { ?>
		<tr>
			<td colspan='2'><?php echo $subtitle; ?></td>
		</tr>
		<?php }; ?>
		<tr>
			<?php if (isset($lista)) { ?>
			<td valign=top><?php if (isset($lista)) echo $lista; ?></td>
			<td><?php if (isset($content)) echo $content; ?></td>
			<?php } else { ?>
			<td colspan='2'><?php if (isset($content)) echo $content; ?></td>
			<?php }; ?>
		</tr>
	</table>
</div>
<div class="footer">
	<p>Tiempo de la consulta {elapsed_time} seg | Proteo ERP </p>
</div>
<?php if (isset($extras)) echo $extras; ?>
</body>
</html>
