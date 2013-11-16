<?php
$maxlin=20; //Maximo de lineas de items.

if(count($parametros) < 0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);
$mSQL = "SELECT
	a.numero   ,a.fecha    ,a.hora,
	a.status   ,a.proveed  ,a.nombre    ,a.agente,
	a.nomage   ,a.montofob ,a.gastosi   ,a.montocif,
	a.aranceles,a.gastosn  ,a.montotot  ,a.arribo,
	a.factura  ,a.cambioofi,a.cambioreal,a.peso,
	a.condicion,a.montoiva ,a.transac   ,a.estampa,
	a.usuario  ,a.montoexc ,a.cargoval  ,a.dua
FROM ordi AS a
WHERE a.numero=${dbid}";

$mSQL = $this->db->query($mSQL);
if($mSQL->num_rows()==0) show_error('Registro no encontrado');

$row = $mSQL->row();

$numero     = $row->numero;
$dbnumero   = $this->db->escape($numero);
$ordeni     = $row->numero;
$fecha      = $row->fecha;
$status     = $row->status;
$proveed    = htmlspecialchars(trim($row->proveed));
$nombre     = htmlspecialchars(trim($row->nombre));
$nomage     = htmlspecialchars(trim($row->nomage));
$agente     = htmlspecialchars(trim($row->agente));
$montofob   = nformat($row->montofob);
$gastosi    = nformat($row->gastosi);
$montocif   = nformat($row->montocif);
$aranceles  = nformat($row->aranceles);
$gastosn    = nformat($row->gastosn);
$montotot   = nformat($row->montotot);
$montoiva   = nformat($row->montoiva);
$cambioofi  = nformat($row->cambioofi);
$cambioreal = nformat($row->cambioreal);
$peso       = nformat($row->peso);
$montoexc   = nformat($row->montoexc);
$arribo     = dbdate_to_human($row->arribo);
$hfecha     = dbdate_to_human($row->fecha);
$factura    = htmlspecialchars(trim($row->factura));
$condicion  = htmlspecialchars(trim($row->condicion));
$dua        = htmlspecialchars(trim($row->dua));
$cargoval   = $row->cargoval;

$montobase  = $this->datasis->dameval('SELECT SUM(base) FROM ordiva WHERE ordeni='.$dbnumero);
$montototal = $row->montocif*$row->cambioreal+$row->gastosn+$row->aranceles;
$montocifbs = $row->montocif*$row->cambioreal;
$exento     = $montototal-$montobase;
$montofinal = $row->montoiva+$montototal;

$montobase  = nformat($montobase );
$montototal = nformat($montototal);
$montocifbs = nformat($montocifbs);
$exento     = nformat($exento    );
$montofinal = nformat($montofinal);


$mSQL_1    = $this->db->query('SELECT codigo,descrip,cantidad,costofob,importefob,participam,gastosi,importecif,montoaran,gastosn,montoaran,gastosn,montoaran,gastosn,cantidad FROM itordi WHERE numero='.$dbnumero);
$detalle1  = $mSQL_1->result();
$mSQL_2    = $this->db->query('SELECT fecha, numero, concepto, monto FROM gseri WHERE ordeni='.$dbnumero);
$detalle2  = $mSQL_2->result();
$mSQL_3    = $this->db->query('SELECT concepto, tasa, base, montoiva FROM ordiva WHERE ordeni='.$dbnumero);
$detalle3  = $mSQL_3->result();
$mSQL_4    = $this->db->query('SELECT fecha, numero, nombre, totpre  FROM gser  WHERE ordeni='.$dbnumero.' UNION ALL SELECT "","ESTIMACION",concepto,monto FROM ordiestima  WHERE ordeni='.$dbnumero);
$detalle4  = $mSQL_4->result();

$det3encab = 5; //Tamanio del encadezado de la segunda tabla

$ittot['monto']=$ittot['reten']=$ittot['ppago']=$ittot['cambio']=$ittot['mora']=$ittot['reteiva']=$ittot['abono']=$lineas=0;
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Orden de importaci&oacute;n <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">

<?php
//************************
//     Encabezado
//************************
$encabezado = <<<encabezado
			<table style="width: 100%;" class="header">
				<tr>
					<td valign='bottom'><h1 style="text-align: left">ORDEN DE IMPORTACI&Oacute;N No. ${numero}</h1></td>
					<td valign='bottom'><h1 style="text-align: right">Fecha: ${hfecha}</h1></td>
				</tr>
			</table>
			<table style="width: 100%; font-size: 8pt;" >
				<tr>
					<td>Proveedor:</td>
					<td><b>(${proveed}) ${nombre}</b></td>
					<td>Nro. de Factura:</td>
					<td><b>${factura}</b></td>
					<td>D.U.A.:</td>
					<td><b>${dua}</b></td>
				</tr>
				<tr>
					<td>Agente Aduanal: </td>
					<td><b>(${agente}) ${nomage}</b></td>
					<td rowspan="2" >Condiciones</td>
					<td rowspan="2" ><b>${condicion}</b></td>
				</tr>
				<tr>
					<td>Fecha de llegada: </td>
					<td><b>${arribo}</b></td>
				</tr>
			</table>
			<br>
			<table style="width: 100%; font-size: 8pt;" >
				<tr style="align: center; font-size: 10pt;" bgcolor="#DDDDDD">
					<td colspan="2" ><b>Costos en US$</b></td>
					<td colspan="2" ><b>Costos en Bs. </b></td>
					<td colspan="2" ><b>Res&uacute;men de Liquidaci&oacute;n</b></td>
				</tr>
				<tr>
					<td>Monto FOB: </td>
					<td align='right'><b>${montofob}</b></td>
					<td>Monto CIF:</td>
					<td align='right'> <b>${montocifbs}</b></td>
					<td>Monto Exento:</td>
					<td align='right'><b>${exento}</b></td>
				</tr>
				<tr>
					<td>Gastos Exterior: </td>
					<td align='right'><b>${gastosi}</b></td>
					<td>Gastos Nacionales:</td>
					<td align='right'><b>${gastosn}</b></td>
					<td>Base Imponible:</td>
					<td align='right'><b>${montobase}</b></td>
				</tr>
				<tr>
					<td>Monto CIF: </td>
					<td align='right'><b>${montocif}</b></td>
					<td>Aranceles:</td>
					<td align='right'><b>${aranceles}</b></td>
					<td>Monto IVA:</td>
					<td align='right'><b>${montoiva}</b></td>
				</tr>

				<tr>
					<td></td>
					<td></td>
					<td>Monto Final:</td>
					<td align='right'><b>${montototal}</b></td>
					<td>Monto total:</td>
					<td align='right'><b>${montofinal}</b></td>
				</tr>
			</table>
encabezado;
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla="
	<h2>LIQUIDACI&Oacute;N Y COSTEO DE PRODUCTOS</h2>
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr bgcolor='#BBBBBB'>
				<th ${estilo}'>C&oacute;digo</th>
				<th ${estilo}'>Descripci&oacute;n</th>
				<th ${estilo}'>Cant.</th>
				<th ${estilo}'>Precio FOB</th>
				<th ${estilo}'>Monto FOB</th>
				<th ${estilo}'>Part%</th>
				<th ${estilo}'>Gastos Ext</th>
				<th ${estilo}'>Monto CIF</th>
				<th ${estilo}'>Monto CIF Bs.</th>
				<th ${estilo}'>Arancel</th>
				<th ${estilo}'>Gastos Nac.</th>
				<th ${estilo}'>Importe N</th>
				<th ${estilo}'>Costo unitario</th>
			</tr>
		</thead>
		<tbody>
";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final=<<<piefinal
		</tbody>
		<tfoot style='border:1px solid;background:#EEEEEE;'>
			<tr bgcolor="#BBBBBB">
				<td></td>
				<td></td>
				<td></td>
				<td style="text-align: left;"><b>Totales:</b></td>
				<td class="change_order_total_col"><b>%s</b></td>
				<td></td>
				<td class="change_order_total_col"><b>%s</b></td>
				<td class="change_order_total_col"><b>%s</b></td>
				<td class="change_order_total_col"><b>%s</b></td>
				<td class="change_order_total_col"><b>%s</b></td>
				<td class="change_order_total_col"><b>%s</b></td>
				<td class="change_order_total_col"><b>%s</b></td>
				<td class="change_order_total_col"></td>
			</tr>
		</tfoot>

	</table>
piefinal;

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="13" style="text-align: right;">CONTINUA...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

$mod     = $clinea = false;
$npagina = true;
$i       = 0;
$t_importecifreal=$t_importenacional=0;
foreach ($detalle1 AS $items){ $i++;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
		$importecifreal=$items->importecif*$cambioreal;
		$t_importecifreal +=$importecifreal;
		$t_importenacional+=$importecifreal+$items->montoaran+$items->gastosn;
?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td style="text-align: left"><?php   echo htmlspecialchars(trim($items->codigo));  ?></td>
				<td style="font-size:6px"><?php      echo wordwrap(htmlspecialchars(trim($items->descrip)),20,"<br>"); ?></td>
				<td style="text-align: right"> <?php echo $items->cantidad?></td>
				<td style="text-align: right;"><?php echo nformat($items->costofob) ?></td>
				<td style="text-align: right;"><?php echo nformat($items->importefob) ?></td>
				<td style="text-align: center"><?php echo nformat($items->participam*100) ?></td>
				<td style="text-align: right;"><?php echo nformat($items->gastosi) ?></td>
				<td style="text-align: right;"><?php echo nformat($items->importecif) ?></td>
				<td style="text-align: right;"><?php echo nformat($importecifreal) ?></td>
				<td style="text-align: right;"><?php echo nformat($items->montoaran) ?></td>
				<td style="text-align: right;"><?php echo nformat($items->gastosn) ?></td>
				<td style="text-align: right;"><?php echo nformat($importecifreal+$items->montoaran+$items->gastosn) ?></td>
				<td style="text-align: right;"><?php echo nformat(($importecifreal+$items->montoaran+$items->gastosn)/$items->cantidad) ?></td>
			</tr>

			<?php
				$lineas++;
				if($lineas >= $maxlin){
					$lineas =0;
					$npagina=true;
					echo $pie_continuo;
					break;
				}
			?>
<?php

		$mod = ! $mod;
	} while ($clinea);
}

echo sprintf($pie_final,nformat($montofob),nformat($gastosi),nformat($montocif),nformat($t_importecifreal),nformat($aranceles),nformat($gastosn),nformat($t_importenacional));

$lineas+=$det3encab;
//******************************
// Gastos en el exterior
//******************************

//************************
//   Encabezado Tabla
//************************
$encabezado_tabla="
	<h2>GASTOS COMPARTIDOS EN EL EXTERIOR</h2>
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>

			<tr bgcolor='#BBBBBB'>
				<th ${estilo}'>Fecha</th>
				<th ${estilo}'>N&uacute;mero</th>
				<th ${estilo}'>Concepto</th>
				<th ${estilo}'>Monto</th>
			</tr>
		</thead>
		<tbody>
";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final="
	</tbody>
		<tfoot style='border:1px solid;background:#EEEEEE;'>
			<tr bgcolor='#BBBBBB'>
				<td></td>
				<td></td>
				<td style='text-align: left;'><b>Totales:</b></td>
				<td class='change_order_total_col'><b>${gastosi}</b></td>
			</tr>
		</tfoot>
</table>";

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" style="text-align: right;">CONTINUA...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

if($lineas+1 > $maxlin){
	$lineas =0;
	$npagina=true;
	echo '<div style="page-break-before: always;"></div>';
}else{
	echo $encabezado_tabla;
}
foreach ($detalle2 AS $items){ $i++;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>

			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td style="text-align: center"><?php echo dbdate_to_human($items->fecha)?></td>
				<td style="text-align: center"><?php echo htmlspecialchars(trim($items->numero)); ?></td>
				<td style="text-align: left"  ><?php echo htmlspecialchars(trim($items->concepto)); ?></td>
				<td style="text-align: right" ><?php echo nformat($items->monto) ?></td>
			</tr>

<?php
		$lineas++;
		if($lineas > $maxlin){
			$lineas =0;
			$npagina=true;
			echo $pie_continuo;
			break;
		}

		if(!$npagina){
			$mod = ! $mod;
		}
	} while ($clinea);
}
echo $pie_final;


$lineas+=$det3encab;
//******************************
// Impuestos al valor agregado
//******************************

//************************
//   Encabezado Tabla
//************************
$encabezado_tabla="
	<h2>IMPUESTO AL VALOR AGREGADO</h2>
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr bgcolor='#BBBBBB'>
				<th ${estilo}'>Concepto</th>
				<th ${estilo}'>Tasa</th>
				<th ${estilo}'>Base</th>
				<th ${estilo}'>IVA</th>
			</tr>
		</thead>
		<tbody>
";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final="
		</tbody>
		<tfoot style='border:1px solid;background:#EEEEEE;'>
			<tr bgcolor='#BBBBBB'>
				<td></td>
				<td></td>
				<td style='text-align: left;'><b>Totales:</b></td>
				<td class='change_order_total_col'><b>${montoiva}</b></td>
			</tr>
		</tfoot>
</table>";

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" style="text-align: right;">CONTINUA...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

if($lineas+1 > $maxlin){
	$lineas =0;
	$npagina=true;
	echo '<div style="page-break-before: always;"></div>';
}else{
	echo $encabezado_tabla;
}
foreach ($detalle3 AS $items){ $i++;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>
		<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
			<td style="text-align: center"><?php echo htmlspecialchars(trim($items->concepto)); ?></td>
			<td style="text-align: center"><?php echo nformat($items->tasa)     ?></td>
			<td style="text-align: left"  ><?php echo nformat($items->base)     ?></td>
			<td style="text-align: right" ><?php echo nformat($items->montoiva) ?></td>
		</tr>
<?php
		$lineas++;
		if($lineas > $maxlin){
			$lineas =0;
			$npagina=true;
			echo $pie_continuo;
			break;
		}

		if(!$npagina){
			$mod = ! $mod;
		}
	} while ($clinea);
}
echo $pie_final;


$lineas+=$det3encab;
//******************************
// GASTOS COMPARTIDOS NACIONALES
//******************************

//************************
//   Encabezado Tabla
//************************
$encabezado_tabla="
	<h2>GASTOS COMPARTIDOS NACIONALES</h2>
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr bgcolor='#BBBBBB'>
				<th ${estilo}'>Fecha</th>
				<th ${estilo}'>N&uacute;mero</th>
				<th ${estilo}'>Proveedor</th>
				<th ${estilo}'>Monto</th>
			</tr>
		</thead>
		<tbody>
";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final="
	</tbody>
		<tfoot style='border:1px solid;background:#EEEEEE;'>
			<tr bgcolor='#BBBBBB'>
				<td></td>
				<td></td>
				<td style='text-align: left;'><b>Totales:</b></td>
				<td class='change_order_total_col'><b>%s</b></td>
			</tr>
		</tfoot>
</table>";

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" style="text-align: right;">CONTINUA...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

if($lineas+1 > $maxlin){
	$lineas =0;
	$npagina=true;
	echo '<div style="page-break-before: always;"></div>';
}else{
	echo $encabezado_tabla;
}
$t_totpre=0;
foreach ($detalle4 AS $items){ $i++;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
		$t_totpre+=$items->totpre;
?>
		<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
			<td style="text-align: center"><?php echo dbdate_to_human($items->fecha)?></td>
			<td style="text-align: center"><?php echo htmlspecialchars(trim($items->numero)); ?></td>
			<td style="text-align: left"  ><?php echo htmlspecialchars(trim($items->nombre)); ?></td>
			<td style="text-align: right" ><?php echo nformat($items->totpre) ?></td>
		</tr>
<?php
		$lineas++;
		if($lineas > $maxlin){
			$lineas =0;
			$npagina=true;
			echo $pie_continuo;
			break;
		}

		if(!$npagina){
			$mod = ! $mod;
		}
	} while ($clinea);
}
echo sprintf($pie_final,nformat($t_totpre));

?></body>
</html>
