<?php
$maxlin=48; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL = "
SELECT a.comprob,a.fecha,a.descrip,a.total,a.debe,a.haber,a.status,a.tipo,a.origen
FROM casi AS a
WHERE a.id=${dbid}";

$mSQL_1 = $this->db->query($mSQL);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    = dbdate_to_human($row->fecha);
$numero   = htmlspecialchars(trim($row->comprob));
$descrip  = htmlspecialchars(trim($row->descrip));
$status   = htmlspecialchars(trim($row->status));
$tipo     = htmlspecialchars(trim($row->tipo));
$origen   = htmlspecialchars(trim($row->origen));
$haber    = nformat($row->haber);
$debe     = nformat($row->debe);
$total    = nformat($row->total);
$saldo    = nformat($row->debe-$row->haber);

$dbnumero   = $this->db->escape($numero);

$lineas = 0;
$uline  = array();

$mSQL="SELECT a.cuenta,a.referen,a.concepto,a.debe,a.haber
FROM itcasi AS a
WHERE a.comprob=${dbnumero}";
$mSQL_2 = $this->db->query($mSQL);
if($mSQL_2->num_rows()==0) show_error('Error en registro');
$detalle  = $mSQL_2->result();
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>ASIENTO CONTABLE <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">
<?php
//************************
//     Encabezado
//
//************************
$encabezado = "
	<table style='width:100%;font-size: 9pt;' class='header' cellpadding='0' cellspacing='0'>
		<tr>
			<td valign='bottom'><h1 style='text-align:left;border-bottom:1px solid;font-size:12pt;'>ASIENTO Nro. ${numero}</h1></td>
			<td valign='bottom'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>FECHA: ${fecha}</h1></td>
		</tr><tr>
			<td>Estatus: <b>${status}</b></td>
			<td>Descripci&oacute;n: <b>${descrip}</b></td>
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
				<th ${estilo}' >C&oacute;digo</th>
				<th ${estilo}' >Referencia</th>
				<th ${estilo}' >Concepto</th>
				<th ${estilo}' >Debe</th>
				<th ${estilo}' >Haber</th>
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
				<td colspan="3" style="text-align: right;"><b>Totales</b></td>
				<td style="text-align: right;font-weight:bold;">${debe}</td>
				<td style="text-align: right;font-weight:bold;">${haber}</td>
			</tr>
			<tr>
				<td colspan="4" style="text-align: right;"><b>Saldo</b></td>
				<td style="text-align: right;font-size:16px;font-weight:bold;">${saldo}</td>
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
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td style="text-align: center;"><?php echo trim($items->cuenta); ?></td>
				<td style="text-align: center;"><?php echo trim($items->referen); ?></td>
				<td>
					<?php
					if(!$clinea){
						$descrip = trim($items->concepto);
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
				<td style="text-align: right;"><?php  echo ($clinea)? '': nformat($items->debe);  ?></td>
				<td style="text-align: right;" ><?php echo ($clinea)? '': nformat($items->haber); ?></td>
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
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
<?php
	$mod = ! $mod;
}
echo $pie_final;
?>
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
</body>
</html>
