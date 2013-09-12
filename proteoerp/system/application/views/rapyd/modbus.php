<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=<?php if(empty($charset)) echo $this->config->item('charset'); else echo $charset; ?>" />
<title>Proteo ERP | <?php echo $encab?></title>
<?php echo style("modbus.css");?>
<?php echo $rapyd_head?>
</head>
<body>
<div id='cerrar'><?php echo image('cerrar.png','Cerrar ventana',array('onclick'=>'window.close();')); ?></div>
<div id='encab'>
	<table>
		<tr>
			<td><?php echo image('logo.png','Proteo ERP', array("height"=>"50px" )); ?></td>
			<td width='100%' align='center'><div style='font-size: 22px;font-weight: bold;'><?php echo $encab ?></div></td>
		</tr>
	</table>
</div>
<div id="content">
	<div class="left"><?php echo $lista ?></div>
	<div class="right"><?php echo $content?>
		<div class="line"></div>
		<div class="code"><?php echo $code?></div>
	</div>
	<div class="line"></div>
	<div id="footer"></div>
</div>
</body>
</html>
