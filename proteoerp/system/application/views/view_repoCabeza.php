<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" />
<style type="text/css">
#encabe{background-color:#2067B5;}
body{	background-color:#2E4B77;margin:0;padding:0;}
</style>
</head>
<body>
	<div id='encabe'>
		<table width='100%' colspacing='0' >
			<tr>
				<td align='left' width="100px" ><img src="/proteoerp/assets/default/css/templete_01.jpg" width="120" ></td>
				<td align='center'><?php echo $titulo ?></td>
				<td align="right" width="100px">
					<?php //echo anchor('reportes/ver/',image('go-previous.png','Volver al Filtro',array('border'=>0)),array('target'=>'contenido','id'=>'rgfil'));?>
					<?php echo anchor('reportes/enlistar/'.$repo,image('listado.png','Volver al Listado',array('border'=>0)),array('target'=>'contenido'));?>
					<?php echo image('cerrar.png','Cerrar Ventana',array('onclick'=>'parent.window.close()',"width"=>"25")); ?>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>
