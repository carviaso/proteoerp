<?php
$nombre = '**'.strtoupper(utf8_encode($nombre)).'**';
$dia    = date('d'    ,$mkt);
$mes    = date('n'    ,$mkt)-1;
$anio   = date('Y'    ,$mkt);
$nmes   = $meses[$mes];
$tmonto = '#'.htmlnformat($monto);
$smonto = '##'.htmlnformat($monto).'##';
$smonto = strtoupper(numletra($monto));

$ffecha = strtoupper("${ciudad} ${dia} de ${nmes}");
?><!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title>Cheque</title>
<style type="text/css">
body {font-family: sans-serif; font-size: 10pt;margin:0px 0px 0px 0px; padding:0px 0px 0px 0px; }
div.absolute {
	#border: 2px dotted green;
	position: absolute;
	padding: 0px;
	text-align: center;
	vertical-align: middle;
}
</style>
</head>
<body>
<!--@size_paper 177x79-->

<div class="absolute" style="top: -25px; right: 40px; font-weight:bold; font-size: 12pt"><?php echo $tmonto; ?></div>

<div class="absolute" style="top: 35px; left: 65px; right: -10px;"><?php echo $nombre; ?></div>
<div class="absolute" style="top: 55px; left: 10px; right: -10px; text-align:left;text-indent: 55px;font-size: 0.8em;line-height: 1.8em"><?php echo $smonto; ?></div>

<div class="absolute" style="top: 103px; left: 10px; right: 350px text-align: right;"><?php echo $ffecha; ?></div>
<div class="absolute" style="top: 103px; left: 250px;"><?php echo $anio; ?></div>

<?php if($endosable){ ?>
<div class="absolute" style="top: 180px; right:30px;font-size: 1.3em;">NO ENDOSABLE</div>
<?php } ?>
</body>
</html>
