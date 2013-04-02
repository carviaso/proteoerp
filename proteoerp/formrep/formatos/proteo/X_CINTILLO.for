<!-- CINTILLO -->
<?php
$rif             = trim($this->datasis->traevalor('RIF'));
$cintillo_titulo1= trim($this->datasis->traevalor('TITULO1'));
$cintillo_titulo2= trim($this->datasis->traevalor('TITULO2'));
$cintillo_titulo3= trim($this->datasis->traevalor('TITULO3'));

$cintillo_titulo2= preg_replace('/[Rr][Ii][Ff] *:? *[VJPGvjpg][0-9\-]+/', ' ', $cintillo_titulo2);
$cintillo_titulo3= preg_replace('/[Rr][Ii][Ff] *:? *[VJPGvjpg][0-9\-]+/', ' ', $cintillo_titulo3);
?>
<div>
	<table style="width: 100%;" >
		<tr>
			<td width="130" rowspan="3"><img src="<?php echo $this->_direccion.'/images/logo.jpg'; ?>" width="127" alt="Logo"></td>
			<td><span style="text-align:left;font-size:1.4em;font-style:italic;font-weight: bold;"><?php echo $cintillo_titulo1; ?></span></td>
		</tr><tr>
			<td style="font-size: 8pt"><b>RIF: <?php echo $rif; ?></b></td>
		</tr><tr>
			<td><div style="font-size: 8pt"> <?php echo $cintillo_titulo2.' '.$cintillo_titulo3; ?></div></td>
		</tr>
	</table>
</div>
<!-- FIN CINTILLO -->
