<?php
$maxlin=43; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL = 'SELECT
a.numero,a.fecha,a.almacen,motivo AS observa,a.cargo
,b.ubides AS almacendes,c.nombre AS cargodes
FROM ssal AS a
LEFT JOIN caub AS b ON a.almacen =b.ubica
LEFT JOIN usol AS c ON a.cargo=c.codigo
WHERE a.id='.$dbid;

$mSQL_1 = $this->db->query($mSQL);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha     = dbdate_to_human($row->fecha);
$numero    = $row->numero;
$almacen   = htmlspecialchars(trim($row->almacen));
$almacendes= $this->us_ascii2html(trim($row->almacendes));
$observa   = $this->us_ascii2html(trim($row->observa));
$cargo     = htmlspecialchars(trim($row->cargo));
$cargodes  = $this->us_ascii2html(trim($row->cargodes));
$dbnumero  = $this->db->escape($numero);

$lineas  = 0;
$uline   = array();

$mSQL_2 = $this->db->query('SELECT
a.codigo,b.descrip AS desca,a.cantidad AS cana,a.concepto
FROM itssal AS a
JOIN sinv AS b ON a.codigo=b.codigo
WHERE a.numero='.$dbnumero);
$detalle  = $mSQL_2->result();
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>AJUSTE DE INVENTARIO <?php echo $numero ?></title>
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
			<td valign='bottom'><h1 style='text-align:left;border-bottom:1px solid;font-size:12pt;'>AJUSTE DE INVENTARIO Nro. ${numero}</h1></td>
			<td valign='bottom'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>Fecha: ${fecha}</h1></td>
		</tr><tr>
			<td>Almac&eacute;n:<b>(${almacen}) ${almacendes}</b></td>
			<td>Recibe:<b>(${cargo}) ${cargodes}</b></td>
		</tr>
		<tr>
			<td colspan='2' style='text-align:center;'><b>Observaci&oacute;n</b></td>
		</tr>
		<tr>
			<td colspan='2' style='text-align:center;'>${observa}</td>
		</tr>
	</table><br />
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
				<th ${estilo}' >Descripci&oacute;n</th>
				<th ${estilo}' >Cantidad</th>
				<th ${estilo}' >Concepto</th>
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
			<tr style='border-top: 1px solid;background:#AAAAAA;'>
				<td style="text-align: right;font-size:20px;" colspan="4">&nbsp;</td>
			</tr>
		</tfoot>

	</table>
piefinal;


$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" style="text-align:right;font-size:20px;">CONTINUA...</td>
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
				<td style="text-align: center;"><?php echo trim($items->codigo); ?></td>
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
				<td style="text-align:right;" ><?php echo ($clinea)? '': nformat($items->cana); ?></td>
				<td style="text-align:center;"><?php echo ($clinea)? '': htmlspecialchars(trim($items->concepto)); ?></td>
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
echo sprintf($pie_final);
?>
<script type="text/php">
	if (isset($pdf)) {
		$texto = array();
		$font  = Font_Metrics::get_font("verdana");
		$size  = 6;
		$color = array(0,0,0);
		$text_height = Font_Metrics::get_font_height($font, $size);
		$w     = $pdf->get_width();
		$h     = $pdf->get_height();
		$y     = $h - $text_height - 24;

		//***Inicio cuadro
		//**************VARIABLES MODIFICABLES***************

		$texto[]="ELABORADO POR:";
		$texto[]="APROBADO:";
		$texto[]="RECIBIDO POR:";

		$cuadros = 0;   //Cantidad de cuadros (en caso de ser 0 calcula la cantidad)
		$margenh = 40;  //Distancia desde el borde derecho e izquierdo
		$margenv = 80;  //Distancia desde el borde inferior
		$alto    = 50;  //Altura de los cuadros
		$size    = 9;   //Tamanio del texto en los cuadros
		$color   = array(0,0,0); //Color del marco
		$lcolor  = array(0,0,0); //Color de la letra
		//**************************************************

		$cuadros = ($cuadros>0) ? $cuadros : count($texto);
		$cuadro  = $pdf->open_object();
		$margenl = $margenv-$alto+$text_height+5;    //Margen de la letra desde el borde inferior
		$ancho   = intval(($w-2*$margenh)/$cuadros); //Ancho de cada cuadro
		for($i=0;$i<$cuadros;$i++){
			$pdf->rectangle($margenh+$i*$ancho, $h-$margenv, $ancho, $alto,$color, 1);
			if(isset($texto[$i])){
				$width = Font_Metrics::get_text_width($texto[$i],$font,$size);
				$pdf->text($margenh+$i*$ancho+intval($ancho/2)-intval($width/2), $h-$margenl, $texto[$i], $font, $size, $lcolor);
			}
		}
		//***Fin del cuadro

		$pdf->close_object();
		$pdf->add_object($cuadro,'add');

		$text = "PP {PAGE_NUM} de {PAGE_COUNT}";

		// Center the text
		$width = Font_Metrics::get_text_width('PP 1 de 2', $font, $size);
		$pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);
	}
</script>
</body>
</html>
