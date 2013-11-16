<?php
$maxlin=39; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id = $parametros[0];
$dbid=$this->db->escape($id);

$mSQL_1 = $this->db->query('SELECT a.numero,a.nombre,a.cod_cli,a.fecha,a.factura,a.fechafa,a.peso,b.rifci,
CONCAT_WS("",TRIM(b.dire11),b.dire12) AS direccion, a.observa1,a.observa2
	FROM snot AS a
	JOIN scli AS b ON a.cod_cli=b.cliente
	WHERE a.id='.$dbid);

if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    = dbdate_to_human($row->fecha);
$numero   = htmlspecialchars(trim($row->numero));
$factura  = htmlspecialchars(trim($row->factura));
$cod_cli  = $this->us_ascii2html(trim($row->cod_cli));
$rifci    = htmlspecialchars(trim($row->rifci));
$nombre   = $this->us_ascii2html(trim($row->nombre));
$observa  = $this->us_ascii2html(trim($row->observa1).' '.trim($row->observa2));

$fechafa  = dbdate_to_human($row->fechafa);
$fechaac  = date('d/m/Y');
$peso     = nformat($row->peso);
$direccion= htmlspecialchars(trim($row->direccion));

$dbnumero = $this->db->escape($row->numero);
$lineas = 0;
$uline  = array();

$mSQL_2 = $this->db->query('SELECT a.codigo,a.descrip,a.cant,a.saldo,a.entrega FROM itsnot AS a WHERE a.numero='.$dbnumero);
$detalle  = $mSQL_2->result();
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Nota de entrega <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body>

<script type="text/php">
	if (isset($pdf)) {
		$font = Font_Metrics::get_font('verdana');
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
			<td valign='bottom'><h1 style="text-align: left ;border-bottom:1px solid;">Nota de entrega </h1></td>
			<td valign='bottom'><h1 style="text-align: right;border-bottom:1px solid;">N&uacute;mero: ${numero}</h1></td>
		</tr><tr>
			<td>Cliente:<b>${cod_cli}</b></td>
			<td>Fecha:  <b>${fecha}</b></td>
		</tr><tr>
			<td>Nombre: <b>${nombre}</b></td>
			<td>Rif/CI: <b>${rifci}</b></td>
		</tr><tr>
			<td>Factura: <b>${factura}</b></td>
			<td>Fecha de la factura:<b>${fechafa}</b></td>
		</tr>
	</table>
encabezado;
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$encabezado_tabla=<<<encabezado_tabla
	<table class="change_order_items">
		<thead>
			<tr>
				<th style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;' >C&oacute;digo</th>
				<th style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;' >Descripci&oacute;n</th>
				<th style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;' >Faltan</th>
				<th style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;' >Cantidad</th>
			</tr>
		</thead>
		<tbody>
encabezado_tabla;
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final=<<<piefinal
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" style="text-align: right;">Peso: ${peso}</td>
			</tr>
		</tfoot>
	</table>
piefinal;

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
						$descrip = trim($items->descrip);
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
				<td style="text-align: right;" ><?php echo ($clinea)? '': nformat($items->saldo-$items->entrega); ?></td>
				<td style="text-align: right;" ><?php echo ($clinea)? '': nformat($items->entrega);  ?></td>
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
			</tr>
<?php
	$mod = ! $mod;
}
echo $pie_final;
echo $observa;
?>
</body>
</html>
