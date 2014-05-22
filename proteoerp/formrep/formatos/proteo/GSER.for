<?php
$maxlin = 38; //Maximo de lineas

if(count($parametros)==0) show_error('Faltan parametros ');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL_1 = $this->db->query('SELECT
fecha,vence,ffactura,tipo_doc,numero,proveed,nombre,totpre,totneto,credito,totiva,totbruto,reteiva,anticipo,credito,
monto1,cheque1,codb1,reten,orden,cajachi,serie,transac,negreso
FROM gser WHERE id='.$dbid);

if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row    = $mSQL_1->row();

$tipo_doc = trim($row->tipo_doc);
if($tipo_doc=='GA') show_error('Gasto de nomina, debe sacarlo por los formatos de nomina');
if($tipo_doc=='AJ') show_error('Gasto por ajuste de inventario, debe imprimirlo por el modulo respectivo');
if($tipo_doc=='XX') show_error('Gasto por anulado, no se puede imprimir');

$fecha    = dbdate_to_human($row->fecha);
$vence    = dbdate_to_human($row->vence);
$ffecha   = dbdate_to_human($row->ffactura);
$numero   = trim($row->numero);
$nombre   = $this->us_ascii2html(trim($row->nombre));
$proveed  = $this->us_ascii2html(trim($row->proveed));
$totpre   = nformat($row->totpre);
$totiva   = nformat($row->totiva);
$totbruto = nformat($row->totbruto);
$reten    = nformat($row->reten);
$totneto  = nformat($row->totneto);
$reteiva  = nformat($row->reteiva);
$anticipo = nformat($row->anticipo);
$credito  = nformat($row->credito);
$monto1   = nformat($row->monto1);
$cheque1  = trim($row->cheque1);
$codb1    = trim($row->codb1);
$orden    = trim($row->orden);
$serie    = trim($row->serie);
$negreso  = trim($row->negreso);

$dbnumero  = $this->db->escape($numero);
$dbproveed = $this->db->escape($row->proveed);
$dbfecha   = $this->db->escape($row->fecha);
$dbtransac = $this->db->escape($row->transac);
if($row->cajachi=='S'){
	$cachi = true;
	$mSQL_2  = $this->db->query("SELECT numero,fecha,proveed,codigo,descrip,precio,iva,importe,rif,proveedor,numfac,fechafac,nfiscal
	FROM gitser
	WHERE numero=${dbnumero} AND proveed=${dbproveed} AND fecha=${dbfecha} AND transac=${dbtransac}");
	//WHERE idgser=${dbid}");
}else{
	$cachi = false;
	$mSQL_2  = $this->db->query("SELECT numero,fecha,proveed,codigo,descrip,precio,iva,importe
	FROM gitser
	WHERE numero=${dbnumero} AND proveed=${dbproveed} AND fecha=${dbfecha} AND transac=${dbtransac}");
	//WHERE idgser=${dbid}");
}
$ndetalle=$mSQL_2->num_rows();
if($ndetalle==0) show_error('Registro presenta inconsistencias');
$detalle = $mSQL_2->result();


if ($row->monto1>0){
	$dbcodb1  = $this->db->escape($codb1);
	$cuenta   = $this->db->query("SELECT numcuent, banco FROM banc WHERE  codbanc=${dbcodb1}");
	$row2     = $cuenta->row();
	$numcuent = htmlspecialchars(trim($row2->numcuent));
	$banco    = htmlspecialchars(trim($row2->banco));
	$fpago    = "Banco/Caja:  <b>(${codb1}) ${banco}</b> Cuenta: <b>${numcuent}</b> Monto: <b>${monto1}</b>";
}else{
	$numcuent = '';
	$banco    = '';
	$fpago    = '<b>CR&Eacute;DITO</b>';
}

if($tipo_doc=='FC'){
	if(empty($negreso)){
		$documento = 'GASTO A CR&Eacute;DITO';
	}else{
		$documento = 'COMPROBANTE DE EGRESO Nro '.$negreso;
	}
}elseif($tipo_doc=='XX'){
	$documento = 'COMPROBANTE <span style="color:red">**ANULADO**</span>';
}elseif($tipo_doc=='ND'){
	$documento = 'NOTA DE D&Eacute;BITO';
}else{
	$documento = 'COMPROBANTE';
}

$tprecio=$tiva=$timporte=$lineas=0;
$pagina = 0;
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title><?php echo $documento.' '.$numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">

<script type="text/php">
	if (isset($pdf)) {
		$font = Font_Metrics::get_font("verdana");
		$size = 6;
		$color = array(0,0,0);
		$text_height = Font_Metrics::get_font_height($font, $size);

		$foot = $pdf->open_object();

		$w = $pdf->get_width();
		$h = $pdf->get_height();

		// Draw a line along the bottom
		$y = $h - $text_height - 24;
		$pdf->line(16, $y, $w - 16, $y, $color, 0.5);

		$pdf->close_object();
		$pdf->add_object($foot, 'all');

		$text = "PP {PAGE_NUM} de {PAGE_COUNT}";

		// Center the text
		$width = Font_Metrics::get_text_width('PP 1 de 2', $font, $size);
		$pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);
	}
</script>

<?php
//************************
//     Encabezado
//
//************************

$encabezado = "
	<table style='width:100%;font-size: 9pt;' class='header' cellpadding='0' cellspacing='0'>
		<tr>
			<td colspan='4' valign='bottom'><h1 style='text-align:left;border-bottom:1px solid;font-size:12pt;'>${documento}</h1></td>
			<td colspan='2' valign='bottom'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>Fecha: ${fecha}</h1></td>
		</tr><tr>
			<td>Proveedor:</td>
			<td colspan='5'><b>${nombre} (${proveed})</b></td>
		</tr><tr>
			<td>N&uacute;mero Factura:</td>
			<td><b>${serie}</b></td>
			<td>Fecha:</td>
			<td><b>${fecha}</b></td>
			<td>Fecha Doc.:</td>
			<td><b>${ffecha}</b></td>
		</tr><tr>
			<td>Tipo Doc.:</td>
			<td><b>${tipo_doc}</b></td>
			<td>Vence:</td>
			<td><b>${vence}</b></td>
			<td>Orden/Compra:</td>
			<td><b>${orden}</b></td>
		</tr><tr>
			<td>Forma de pago:</td>
			<td colspan='5'>${fpago}</td>
		</tr>
	</table>
";
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla="
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr style='background-color:black;border-style:solid;color:white;font-weight:bold'>
				<th>C&oacute;digo</th>
				<th>Descripci&oacute;n</th>
				<th>Precio</th>
				<th>Iva</th>
				<th>Importe</th>
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
					<tfoot>
					<tr>
						<td  colspan='2' style='text-align: right;border:1px solid black;'><b>Totales </b></td>
						<td  style='text-align:right;border:1px solid black;'><b>%s</b></td>
						<td  style='text-align:right;border:1px solid black;'><b>%s</b></td>
						<td  style='text-align:right;border:1px solid black;'><b>%s</b></td>
					</tr>
					</tfoot>
				</table>
				<table style='border:1px solid black;font-size:8pt;width:100%%;'>
					<tr>
						<td style='text-align:left;'>      <b>RETENCI&Oacute;N DE I.S.L.R:</b></td>
						<td class='change_order_total_col'><b>${reten}</b></td>
						<td style='text-align: right;'><b>ANTICIPOS RECIBIDOS:</b></td>
						<td class='change_order_total_col'><b>${anticipo}</b></td>
						<td style='text-align:right;'><b>MONTO NETO:</b></td>
						<td class='change_order_total_col'><b>${totneto}</b></td>
					</tr>
					<tr>
						<td style='text-align:left;'><b>RETENCI&Oacute;N DE I.V.A:</b></td>
						<td class='change_order_total_col'><b>${reteiva}</b></td>
						<td style='text-align:right;'><b>MONTO PAGADO:</b></td>
						<td class='change_order_total_col'><b>${monto1}</b></td>
						<td style='text-align: right;'><b>MONTO A CR&Eacute;DITO:</b></td>
						<td class='change_order_total_col'><b>${credito}</b></td>
					</tr>
				</table>
				<br>
				<table style='width:100%%;border:1px solid grey;text-align:center;font-size:8pt;' class='header'>
					<tr style='height:100px;'>
						<td>&nbsp;<br><br><br></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr style='background-color:#EAEAEA;'>
						<td><b>Elaborado por: </b></td>
						<td><b>Auditoria:     </b></td>
						<td><b>Autorizado por:</b></td>
						<td><b>Aprobado:      </b></td>
					</tr>
				</table>

				<table style='width:100%%;border:1px solid grey;text-align:center;font-size:8pt;' class='header'>
					<tr style='height:100px;'>
						<td>&nbsp;<br><br><br></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr style='background-color:#EAEAEA;'>
						<td><b>Recibido por: </b></td>
						<td><b>C.I.:</b></td>
						<td><b>Fecha:</b></td>
					</tr>
				</table>";

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan='5' style='text-align: right;font-size:16px'>Continua...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

$mod     = $clinea = false;
$npagina = true;
$i       = 0;

foreach ($detalle AS $items){ $i++; $ndetalle=$ndetalle-1;
	$tprecio  += $items->precio;
	$tiva     += $items->iva;
	$timporte += $items->importe;

	if($cachi){
		$cachi_desc  = trim($items->rif).' ';
		$cachi_desc .= trim($items->proveedor).' ';
		$cachi_desc .= 'Factura: '.trim($items->numfac).' ';
		$nfiscachi   = trim($items->nfiscal);
		if(!empty($nfiscachi)){
			$cachi_desc .= 'Control: '.$nfiscachi.' ';
		}
		$cachi_desc .= 'Fecha: '.dbdate_to_human($items->fechafac).' ';
		$lineas++;
	}else{
		$cachi_desc = '';
	}

	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td style="text-align: center;"><?php echo trim($items->codigo); ?></td>
				<td>
					<?php
					if(!$clinea){
						$descrip = trim($items->descrip);
						$descrip = str_replace("\r",'',$descrip);
						$descrip = str_replace(array("\t"),' ',$descrip);
						$descrip = wordwrap($descrip,40,"\n");
						$arr_des = explode("\n",$descrip);
					}

					while(count($arr_des)>0){
						$uline   = array_shift($arr_des);
						echo $this->us_ascii2html($uline).'<br />';
						$lineas++;
						if($lineas >= $maxlin){
							if($ndetalle>0){
								$npagina=true;
								$lineas =0;
							}
							if(count($arr_des)>0){
								$clinea = true;
							}else{
								$clinea = false;
							}
							break;
						}
					}
					if(count($arr_des)==0 && $clinea) $clinea=false;
					?>
				</td>
				<td style="text-align: right;" ><?php    echo ($clinea)? '': nformat($items->precio);  ?></td>
				<td style="text-align: right;" ><?php    echo ($clinea)? '': nformat($items->iva);     ?></td>
				<td class="change_order_total_col"><?php echo ($clinea)? '': nformat($items->importe); ?></td>
			</tr>
			<?php if(!empty($cachi_desc)){ $lineas++; ?>
			<tr class='<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>'>
				<td colspan='5' style='text-align: center;font-size:0.7em'><b><?php echo $this->us_ascii2html($cachi_desc); ?></b></td>
			</tr>
			<?php } ?>

<?php
		if($npagina && $ndetalle>0){
			echo $pie_continuo;
		}else{
			$mod = ! $mod;
		}
	} while ($clinea);
}

for(1;$lineas<$maxlin;$lineas++){ ?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
<?php
	$mod = ! $mod;
}
echo sprintf($pie_final,nformat($tprecio),nformat($tiva),nformat($timporte));
?>
</body>
</html>
