<p>
<table align='center' width='100%' border=0>
	<tr>
		<td width='100%'>
			<table width='100%' bgcolor='#FAFFFB'>
				<tr><td align='center'><b style="font-size:24pt"><?php echo $descrip; ?></b></td></tr>
				<?php if(isset($corta)){ ?>
				<tr><td align='center'><b style="font-size:16pt;"><?php echo $corta; ?> </b></td></tr>
				<?php }?>
			</table>
		</td>
	</tr>

	<tr>
		<td width='100%' >
			<table width='100%' border='0'>
				<tr><td align='center'><b STYLE="font-size:24pt;color:blue">Precio al P&uacute;blico: <?php echo $precio1 ?></b> <b><?php echo $moneda  ?></b></td></tr>
				<?php $descufijo=(isset($descufijo)?$descufijo:0);
				if($descufijo>0){?>
				<tr><td align='center'><b STYLE="font-size:24pt;color:red">Descuento <?php echo $descufijo; ?>%:</b> &nbsp;<b STYLE="font-size:30pt;color:red"><?php echo $pdescu; ?></b><b STYLE="font-size:16pt;color:red"> <?php echo $moneda;?></b></td></tr>
				<tr><td align='center'><b STYLE="font-size:8pt;color:red"> <?php echo $descurazon;?></td></tr>
				<?php }else{ ?>
				<tr><td align='center'><b STYLE="font-size:18pt;color:black">I.V.A.: <?php echo $iva; ?><b>%</b> Precio Base : <?php echo round($precio2*100/(100+$iva),2); ?></b><b><?php echo $moneda;  ?></b></td></tr>
				<tr><td align='center'><b STYLE="font-size:32pt;color:red">Precio de venta : <?php echo $precio2; ?></b><b><?php echo $moneda;  ?></b></td></tr>
				<?php } ?>
				<?php if(isset($dvolum1) && $dvolum1>0){ ?>
				<tr><td align='right' colspan=2 ><b STYLE="font-size:20pt;color:green">+ de <?php echo $dvolum1 ?> unidades: <?php echo $precio3 ?></b> <b><?php echo $moneda  ?></b></td></tr>
				<tr><td align='right' colspan=2 ><b STYLE="font-size:20pt;color:green">+ de <?php echo $dvolum2 ?> unidades: <?php echo $precio4 ?></b> <b><?php echo $moneda  ?></b></td></tr>
				<?php } ?>
			</table>
		</td>
	</tr>

	<tr>
		<td width='100%'>
			<table width='100%' border=0>
				<tr style="font-size:14pt;">
					<td><b>C&oacute;digo:</b> <?php echo $codigo ?> </td>
					<td style="font-size:14pt;" rowspan=1 ><b>Marca:</b> <?php echo $marca ?></td>
				</tr>
				<tr>
					<td style="font-size:14pt;"><b>C&oacute;digo Barras:</b> <?php echo $barras ?></td>
					<td style="font-size:14pt;"><b>Referencia:</b> <?php echo $referen ?></td>
				</tr>
				<tr>
					<td style="font-size:18pt;" align='left'><b>Existencia:</b> <?php echo $existen ?></td>
					<td style="font-size:14pt;" align='left'><b>Fecha:</b> <?php echo dbdate_to_human($fecha) ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<?php
	if(isset($img)){
		echo "<tr><td align='center'>$img</td></tr>";
	}
	?>
<table>
</p>
