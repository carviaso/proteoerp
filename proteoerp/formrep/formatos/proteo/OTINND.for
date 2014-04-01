<?php
$maxlin=39; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL = "
SELECT a.nfiscal,
	a.tipo_doc,a.numero,a.cod_cli,TRIM(c.nomfis) AS nomfis,c.nombre,c.rifci,CONCAT_WS('',TRIM(c.dire11),c.dire12) AS direccion,a.fecha,
	a.iva,a.totals,a.totalg, a.exento,a.tasa, a.montasa, a.reducida, a.monredu, a.sobretasa,a.monadic,
	c.telefono, a.observa1,a.observa2
FROM otin AS a
JOIN scli AS c ON a.cod_cli=c.cliente
WHERE a.id=${dbid} AND a.tipo_doc='ND'";

$mSQL_1 = $this->db->query($mSQL);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    = dbdate_to_human($row->fecha);
$numero   = $row->numero;
$cod_cli  = htmlspecialchars(trim($row->cod_cli));
$rifci    = htmlspecialchars(trim($row->rifci));
$nombre   = (empty($row->nomfis))? htmlspecialchars(trim($row->nombre)) : htmlspecialchars($row->nomfis);
$stotal   = nformat($row->totals);
$gtotal   = nformat($row->totalg);
$exento   = nformat($row->exento);
$observa  = htmlspecialchars(trim($row->observa1).trim($row->observa2));

$tasa      = nformat($row->tasa);
$montasa   = nformat($row->montasa);
$reducida  = nformat($row->reducida);
$monredu   = nformat($row->monredu);
$sobretasa = nformat($row->sobretasa);
$monadic   = nformat($row->monadic);

$impuesto = nformat($row->iva);
$direc    = htmlspecialchars(trim($row->direccion));
$tipo_doc = trim($row->tipo_doc);
$nfiscal  = htmlspecialchars(trim($row->nfiscal));
$telefono = htmlspecialchars(trim($row->telefono));

$dbtipo_doc = $this->db->escape($tipo_doc);
$dbnumero   = $this->db->escape($numero);

if($tipo_doc == 'OT')
	$documento = 'Ingreso';
elseif($tipo_doc == "OC")
	$documento = "CREDITO";
elseif($tipo_doc == "ND")
	$documento = "NOTA DE DEBITO";
else
	$documento = "DOCUMENTO";

$lineas = 0;
$uline  = array();

$mSQL="SELECT codigo,descrip AS desca,precio AS preca,importe, impuesto AS iva,larga AS detalle
FROM itotin
WHERE numero=$dbnumero AND tipo_doc=$dbtipo_doc";

$mSQL_2 = $this->db->query($mSQL);
$detalle  = $mSQL_2->result();
?><html>
<head>
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
	<p style='height: 50px;'> </p>
	<table style='width:100%;font-size: 9pt;' class='header' cellpadding='0' cellspacing='0'>
		<tr>
			<td valign='bottom'><h1 style='text-align:left;border-bottom:1px solid;font-size:12pt;'>${documento} Nro. ${numero}</h1></td>
			<td valign='bottom'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>Fecha de Emisi&oacute;n: ${fecha}</h1></td>
		</tr><tr>
			<td>RIF, CI o Pasaporte: <b>${rifci}</b></td>
			<td>Tel&eacute;fono:  <b>${telefono}</b></td>
		</tr><tr>
			<td>Raz&oacute;n Social: <b>${nombre}</b></td>
			<td>C&oacute;digo de Cliente: <b>${cod_cli}</b></td>
		</tr><tr>
			<td colspan='2'>Domicilio Fiscal: <b>${direc}</b></td>
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
			<tr>
				<th ${estilo}width:130px;' >C&oacute;digo</th>
				<th ${estilo}' >Descripci&oacute;n</th>
				<th ${estilo}width:90px;' >Monto</th>
				<th ${estilo}width:35px;' >IVA%</th>
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
			<tr>
				<td colspan="2" style="text-align: right;"><b>Monto Total Exento o Exonerado del IVA:</b></td>
				<td colspan="2" style="text-align: right;font-size:14px;font-weight:bold;">${exento}</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;"><b>Monto Total de la Base Imponible seg&uacute;n Alicuota :</b></td>
				<td colspan="2" style="text-align: right;font-size:16px;font-weight:bold;" >${montasa}</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;"><b>Monto Total del Impuesto seg&uacute;n Alicuota:</b></td>
				<td colspan="2" style="text-align: right;font-size:16px;font-weight:bold;">${tasa}</td>
			</tr>
			<tr style='border-top: 1px solid;background:#AAAAAA;'>
				<td colspan="2" style="text-align: right;"><b>VALOR TOTAL DE LA VENTA O SERVICIO:</b></td>
				<td colspan="2" style="text-align: right;font-size:20px;font-weight:bold;">${gtotal}</td>
			</tr>
		</tfoot>

	</table>
piefinal;


$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5" style="text-align: right;">CONTINUA...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

$mod     = $clinea = false;
$npagina = true;
$i       = 0;

foreach ($detalle AS $items){ $i++;
	do {
		if($npagina){
			//$this->incluir('X_CINTILLO');
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
						$ddetall = $this->us_ascii2html(trim($items->detalle));
						$descrip = $this->us_ascii2html(trim($items->desca));
						if(strlen($ddetall) > 0 ) {
							if(strlen($descrip)>0 ){
								if(strpos($ddetall,$descrip)!==false){
									$descrip = $ddetall;
								}else{
									$descrip .= "\n".$ddetall;
								}
							}else{
								$descrip .= $ddetall;
							}
						}

						$descrip = str_replace("\r",'',$descrip);
						$descrip = str_replace(array("\t"),' ',$descrip);
						$descrip = wordwrap($descrip,40,"\n");
						$arr_des = explode("\n",$descrip);
					}

					while(count($arr_des)>0){
						$uline   = array_shift($arr_des);
						echo htmlspecialchars($uline).'<br />';
						$lineas++;
						if($lineas >= $maxlin){
							$lineas =0;
							$npagina=true;
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
				<td style="text-align: right;" ><?php echo ($clinea)? '': nformat($items->preca); ?></td>
				<td style="text-align: right;" ><?php echo ($clinea)? '': nformat($items->iva*100/$items->preca); ?></td>
			</tr>
<?php
		if($npagina){
			echo $pie_continuo;
		}else{
			$mod = ! $mod;
		}
	} while ($clinea);
}

for(1;$lineas<$maxlin;$lineas++){ ?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
<?php
	if(!empty($observa)){
		echo "<td colspan='4' style='text-align: center;'>${observa}</td>";
		$observa='';
	}else{
?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
<?php
	}
?>
			</tr>
<?php
	$mod = ! $mod;
}
echo $pie_final;
?>
</body>
</html>
