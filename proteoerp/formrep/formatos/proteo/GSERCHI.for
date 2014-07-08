<?php
$maxlin = 38; //Maximo de lineas

if(count($parametros)==0) show_error('Faltan parametros ');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL_1 = $this->db->query('SELECT codbanc AS caja FROM gserchi WHERE id='.$dbid);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row    = $mSQL_1->row();

$caja  = trim($row->caja);
$dbcaja= $this->db->escape($caja);

$mSQL_2  = $this->db->query("SELECT
	fechafac,numfac,rif,proveedor,codigo,descrip,importe,
	monredu+montasa+monadic+tasa+reducida+exento+sobretasa AS monto,aceptado
	FROM gserchi
	WHERE codbanc=${dbcaja} AND ngasto IS NULL");

$ndetalle=$mSQL_2->num_rows();
if($ndetalle==0) show_error('Registro presenta inconsistencias');
$detalle = $mSQL_2->result();

$tprecio=$tiva=$timporte=$lineas=0;
$pagina = 0;
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Relacion de caja chica <?php echo $caja ?></title>
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
			<td colspan='4' valign='bottom'><h1 style='text-align:left;border-bottom:1px solid;font-size:12pt;'>Relacion de caja chica</h1></td>
			<td colspan='2' valign='bottom'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>Caja: ${caja}</h1></td>
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
				<th>Fecha</th>
				<th>Factura</th>
				<th>Descripci&oacute;n</th>
				<th>Monto</th>
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
						<td  style='text-align:right;border:1px solid black;'></td>
						<td  style='text-align:right;border:1px solid black;'><b>%s</b></td>
					</tr>
					</tfoot>
				</table>";

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan='4' style='text-align: right;font-size:16px'>Continua...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

$mod     = $clinea = false;
$npagina = true;
$i       = $tmonto=0;

foreach ($detalle AS $items){ $i++; $ndetalle=$ndetalle-1;
	$tmonto   += $items->monto;


	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td style="text-align: center;"><?php echo trim($items->fechafac); ?></td>
				<td style="text-align: center;"><?php echo trim($items->numfac); ?></td>
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
				<td style="text-align: right;" ><?php    echo ($clinea)? '': nformat($items->monto);  ?></td>
			</tr>

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
			</tr>
<?php
	$mod = ! $mod;
}
echo sprintf($pie_final,nformat($tmonto));
?>
</body>
</html>
