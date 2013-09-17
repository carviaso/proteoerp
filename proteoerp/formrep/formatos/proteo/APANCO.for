<?php
$maxlin=33; //Maximo de lineas de items.

if(count($parametros) < 0) show_error('Faltan parametros');
$id=$parametros[0];

$sel=array('a.numero','a.fecha','a.tipo','a.clipro','a.nombre','a.monto','a.observa1','a.observa2','a.transac');
$this->db->select($sel);
$this->db->from('apan AS a');
$this->db->where('a.id', $id);

$mSQL_1 = $this->db->get();
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');

$row = $mSQL_1->row();
$numero  = $row->numero;
$clipro  = htmlspecialchars(trim($row->clipro));
$tipo    = trim($row->tipo);
$fecha   = $row->fecha;
$hfecha  = dbdate_to_human($row->fecha);
$monto   = nformat($row->monto);
$nombre  = $this->us_ascii2html(trim($row->nombre));
$observa = wordwrap(trim(str_replace(',',', ',$this->us_ascii2html(trim($row->observa1).trim($row->observa2)))), 100, '<br>');
$transac = $row->transac;

if($tipo=='P'){
	$tit1 = 'Proveedor';
	$sel=array('a.tipoppro AS tipoan','a.numppro AS numan','a.tipo_doc','a.numero','a.fecha','a.abono AS monto');
	$this->db->select($sel);
	$this->db->from('itppro AS a');
	$this->db->where('a.transac' ,$transac);
	$this->db->where('a.cod_prv' ,$row->clipro);
	$mSQL_2 = $this->db->get();
}elseif($tipo=='C'){
	$tit1 = 'Cliente';
	$sel=array('a.tipoccli AS tipoan','a.numccli AS numan','a.tipo_doc','a.numero','a.fecha','a.abono AS monto');
	$this->db->select($sel);
	$this->db->from('itccli AS a');
	$this->db->where('a.transac' ,$transac);
	$this->db->where('a.cod_cli' ,$row->clipro);
	$mSQL_2 = $this->db->get();
}else{
	show_error('Problemas con el registro');
}
$detalle  = $mSQL_2->result();

$ittot['monto']=$lineas=0;
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Aplicaci&oacute;n de anticipo <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">
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
		//$texto[]='Elaborado por';
		//$texto[]='Auditoria';
		//$texto[]='Autorizado por:';
		//$texto[]='Fecha ____/____/_______';
        //
		//$cuadros = 0;   //Cantidad de cuadros (en caso de ser 0 calcula la cantidad)
		//$margenh = 40;  //Distancia desde el borde derecho e izquierdo
		//$margenv = 80;  //Distancia desde el borde inferior
		//$alto    = 50;  //Altura de los cuadros
		//$size    = 9;   //Tamanio del texto en los cuadros
		//$color   = array(0,0,0); //Color del marco
		//$lcolor  = array(0,0,0); //Color de la letra
		////**************************************************
        //
		//$cuadros = ($cuadros>0) ? $cuadros : count($texto);
		//$cuadro  = $pdf->open_object();
		//$margenl = $margenv-$alto+$text_height+5;    //Margen de la letra desde el borde inferior
		//$ancho   = intval(($w-2*$margenh)/$cuadros); //Ancho de cada cuadro
		//for($i=0;$i<$cuadros;$i++){
		//	$pdf->rectangle($margenh+$i*$ancho, $h-$margenv, $ancho, $alto,$color, 1);
		//	if(isset($texto[$i])){
		//		$width = Font_Metrics::get_text_width($texto[$i],$font,$size);
		//		$pdf->text($margenh+$i*$ancho+intval($ancho/2)-intval($width/2), $h-$margenl, $texto[$i], $font, $size, $lcolor);
		//	}
		//}
		//***Fin del cuadro

		//$pdf->close_object();
		//$pdf->add_object($cuadro,'add');

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
$encabezado = "
	<table style='width:100%;font-size: 9pt;' class='header' cellpadding='0' cellspacing='0'>
		<tr>
			<td valign='bottom'><h1 style='text-align:left; border-bottom:1px solid;font-size:12pt;'>APLICACI&Oacute;N DE ANTICIPO A ".strtoupper($tit1)." Nro. ${numero}</h1></td>
			<td valign='bottom' style='text-align:right;'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>FECHA: ${hfecha}</h1></td>
		</tr><tr>
			<td><b>${tit1}:</b> (${clipro}) ${nombre}</td>
			<td><b>Monto: </b> ${monto}</td>
		</tr><tr>
			<td colspan='2'><b>Por concepto de:</b> ${observa}</td>
		</tr>
	</table><br>
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
				<th ${estilo}' >Anticipo</th>
				<th ${estilo}' >Efecto</th>
				<th ${estilo}' >Fecha</th>
				<th ${estilo}' >Monto</th>
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
				<td style="text-align: right;" colspan='3'><b>TOTAL</b></td>
				<td style="text-align: right;font-size:16px;font-weight:bold;" > %s </td>
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

$this->incluir('X_CINTILLO');
echo $encabezado;
echo $encabezado_tabla;
$npagina = false;
$i       = 0;
foreach ($detalle AS $items2){ $i++;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">

				<td style="text-align: left"  ><?php echo $items2->tipoan.$items2->numan; ?></td>
				<td style="text-align: left"  ><?php echo $items2->tipo_doc.$items2->numero; ?></td>
				<td style="text-align: center"><?php echo dbdate_to_human($items2->fecha); ?></td>
				<td style="text-align: right" ><?php echo nformat($items2->monto); ?></td>
				<?php
				$ittot['monto'] += $items2->monto;
				$lineas++;
				if($lineas > $maxlin){
					$lineas =0;
					$npagina=true;
					echo $pie_continuo;
					break;
				}
				?>
			</tr>
<?php

		$mod = ! $mod;
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
echo sprintf($pie_final,nformat($ittot['monto']));
?></body>
</html>
