<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?> " />
<title>	<?=$titu; ?></title>
</head>
<body marginheight="0" marginheight="0" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0" >
	<IFRAME id="navegador" name="navegador" src="<?php echo site_url("reportes/cabeza/$repo") ?> " width="100%" height="65" scrolling="no" frameborder="0"></IFRAME>
	<IFRAME id="contenido" name="contenido" src="<?php echo site_url("reportes/enlistar/$pre") ?> " width="100%" height="100%" scrolling="auto" frameborder="0">
		El navegador no soporta iFrames o esta desactivado <A href="<?php echo site_url('reportes/enlistar/sfac') ?> ">Alternativa</A>]
	</IFRAME>
</body>
</html>
