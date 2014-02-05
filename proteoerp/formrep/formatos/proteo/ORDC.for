<?php
$maxlin=39; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id = $parametros[0];
$dbid=$this->db->escape($id);

//Para esconder o no los precios
if(isset($parametros[1])){
	if($parametros[1]=='S'){
		$mprec=false;
	}else{
		$mprec=true;
	}
}else{
	$mprec=true;
}

$mSQL_1 = $this->db->query('SELECT a.fecha,a.numero,a.peso,a.arribo,a.proveed,
	a.nombre,a.condi,
	a.montotot AS totals,
	a.montoiva AS iva,
	a.montonet AS totalg,
	b.direc1 AS direccion,
	b.rif
	FROM ordc AS a
	JOIN sprv AS b ON a.proveed=b.proveed
	WHERE  a.id='.$dbid);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    = dbdate_to_human($row->fecha);
$numero   = htmlspecialchars(trim($row->numero));
$proveed  = htmlspecialchars(trim($row->proveed));
$rifci    = htmlspecialchars(trim($row->rif));
$nombre   = htmlspecialchars(trim($row->nombre));
$stotal   = nformat($row->totals);
$gtotal   = nformat($row->totalg);
$peso     = nformat($row->peso);
$impuesto = nformat($row->iva);
$direccion= htmlspecialchars(trim($row->direccion));

$dbnumero = $this->db->escape($numero);
$lineas   = 0;
$uline    = array();
$mSQL_2   = $this->db->query('SELECT codigo,descrip AS desca,cantidad AS cana,costo AS preca,importe FROM itordc WHERE numero='.$dbnumero);
$detalle  = $mSQL_2->result();
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Orden de Compra <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body>

<script type="text/php">
	if (isset($pdf)) {
		$font = Font_Metrics::get_font("verdana");;
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
//************************
$encabezado = <<<encabezado
	<table style="width: 100%;" class="header">
		<tr>
			<td valign='bottom'><h1 style="text-align: left">Orden de compra</h1></td>
			<td valign='bottom'><h1 style="text-align: right">N&uacute;mero: ${numero}</h1></td>
		</tr><tr>
			<td>Proveedor:<b>${proveed}</b></td>
			<td>Fecha:  <b>${fecha}</b></td>
		</tr><tr>
			<td>Nombre: <b>${nombre}</b></td>
			<td>Rif: <b>${rifci}</b></td>
		</tr><tr>
			<td>Direcci&oacute;n: <b>${direccion}</b></td>
			<td>Peso:      <b>${peso}</b></td>
		</tr>
	</table>
encabezado;
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$encabezado_tabla="
	<table class='change_order_items'>
		<thead>
			<tr>
				<th style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;' >C&oacute;digo</th>
				<th style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;' >Descripci&oacute;n</th>
				<th style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;' >Cantidad</th>";
if($mprec){
	$encabezado_tabla.="
				<th style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;' >Precio</th>
				<th style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;' >Importe</th>";
}
$encabezado_tabla.="
			</tr>
		</thead>
		<tbody>";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
if($mprec){
	$pie_final=<<<piefinal
			</tbody>

			<tfoot style='border:1px solid;background:#EEEEEE;'>
				<tr>
					<td colspan="2" style="text-align: right;"></td>
					<td colspan="2" style="text-align: right;"><b>SUB-TOTAL:</b></td>
					<td class="change_order_total_col"><b>${stotal}</b></td>
				</tr><tr>
					<td colspan="2" style="text-align: right;"></td>
					<td colspan="2" style="text-align: right;"><b>IMPUESTO:</b></td>
					<td class="change_order_total_col"><b>${impuesto}</b></td>
				</tr><tr  style='border-top: 1px solid;background:#AAAAAA;'>
					<td colspan="2" style="text-align: right;"></td>
					<td colspan="2" style="text-align: right;font-size:2em;"><b>TOTAL:</b></td>
					<td class="change_order_total_col" style="font-size:2em;" ><b>${gtotal}</b></td>
				</tr>
			</tfoot>
		</table>
		<table width="100%">
			<tr>
				<td style="text-align:center;"><b>Preparado por: </b></td>
				<td style="text-align:center;"><b>Autorizado por:</b></td>
			</tr>
		</table>
piefinal;
}else{
	$pie_final=<<<piefinal
			</tbody>
			<tfoot style='border:1px solid;background:#EEEEEE;'>
				<tr style='border-top: 1px solid;background:#AAAAAA;'>
					<td colspan="3" style="text-align: right;">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
		<table width="100%">
			<tr>
				<td style="text-align:center;"><b>Preparado por: </b></td>
				<td style="text-align:center;"><b>Autorizado por:</b></td>
			</tr>
		</table>
piefinal;
}

$colspan= ($mprec)? 5: 2;
$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="${colspan}" style="text-align: right;">CONTINUA...</td>
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
				<td style="text-align: center"><?php echo ($clinea)? '': $items->codigo; ?></td>
				<td>
					<?php
					if(!$clinea){
						$descrip = trim($items->desca);
						$descrip = str_replace("\r",'',$descrip);
						$descrip = str_replace(array("\t"),' ',$descrip);
						$descrip = wordwrap($descrip,40,"\n");
						$arr_des = explode("\n",$descrip);
					}

					while(count($arr_des)>0){
						$uline   = array_shift($arr_des);
						echo $uline.'<br>';
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
				<td style="text-align: center;"><?php echo ($clinea)? '': nformat($items->cana,3); ?></td>
				<?php if($mprec){ ?>
				<td style="text-align: right;" ><?php echo ($clinea)? '': nformat($items->preca); ?></td>
				<td class="change_order_total_col"><?php echo ($clinea)? '':nformat($items->preca*$items->cana); ?></td>
				<?php } ?>
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
				<?php if($mprec){ ?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<?php } ?>
			</tr>
<?php
	$mod = ! $mod;
}
echo $pie_final;
?>
</body>
</html>
