<?php
$maxlin=33; //Maximo de lineas de items.

if(count($parametros) < 0) show_error('Faltan parametros');
$id=$parametros[0];

$sel=array('a.tipo','a.fecha','a.monto','a.facturas','a.vende'
,'b.nombre','a.observa');
$this->db->select($sel);
$this->db->from('rcobro AS a');
$this->db->join('vend   AS b'  ,'a.vende=b.vendedor');
$this->db->where('a.id'   , $id);

$mSQL_1 = $this->db->get();
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');

$row = $mSQL_1->row();
$tipo     = trim($row->tipo);
$numero   = str_pad($id, 8, '0', STR_PAD_LEFT);
$vende    = $this->us_ascii2html($row->vende);
$fecha    = $row->fecha;
$hfecha   = dbdate_to_human($row->fecha);
$monto    = nformat($row->monto);
$nombre   = $this->us_ascii2html($row->nombre);
$observa  = wordwrap(trim(str_replace(',',', ',$this->us_ascii2html($row->observa))), 100, '<br>');

if($tipo=='P'){
	$htipo='Pediente';
}else{
	$htipo='Otro';
}

$sel=array('a.tipo_doc','a.numero','a.fecha','a.monto','a.abonos AS abono','a.vence');
$this->db->select($sel);
$this->db->from('smov AS a');
$this->db->where('a.rcobro'  ,$id);
$mSQL_2 = $this->db->get();
$detalle= $mSQL_2->result();

$ittot['monto']=$ittot['reten']=$ittot['ppago']=$ittot['cambio']=$ittot['mora']=$ittot['reteiva']=$ittot['abono']=$lineas=0;
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Relacion de cobro <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">

<script type="text/php">
	if (isset($pdf)) {
		$texto = array();
		$font  = Font_Metrics::get_font('verdana');
		$size  = 6;
		$color = array(0,0,0);
		$text_height = Font_Metrics::get_font_height($font, $size);
		$w     = $pdf->get_width();
		$h     = $pdf->get_height();
		$y     = $h - $text_height - 24;

		//***Inicio cuadro
		//**************VARIABLES MODIFICABLES***************
		$texto[]='Recibido por';
		$texto[]='Firma y sello';

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

<?php
//************************
//     Encabezado
//************************
$encabezado = <<<encabezado
						<table style="width: 100%;" class="header">
							<tr>
								<td><h1 style="text-align: left">RELACI&Oacute;N DE COBRO No. ${numero}</h1></td>
								<td><h1 style="text-align: right">Fecha: ${hfecha}</h1></td>
							</tr>
							<tr>
								<td>Tipo: <b>${htipo}</b></td>
								<td>Cobrador: <b>(${vende}) ${nombre}</b></td>
							</tr>
							<tr>
								<td colspan='2'>Observaci&oacute;n: <b>${observa}</b></td>
							</tr>
						</table>
encabezado;
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla="
	<h2>Efectos a cobrar:</h2>
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr>
				<th ${estilo}' >Documento  </th>
				<th ${estilo}' >Fecha      </th>
				<th ${estilo}' >Vencimiento</th>
				<th ${estilo}' >Monto      </th>
				<th ${estilo}' >Abonado    </th>
				<th ${estilo}' >Saldo      </th>
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
				<td colspan='3' >Totales...</td>
				<td style="text-align: right">%s</td>
				<td style="text-align: right">%s</td>
				<td style="text-align: right; font-size:1.5em; font-weight:bold;">%s</td>
			</tr>
		</tfoot>

	</table>
piefinal;

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6" style="text-align: right;">CONTINUA...</td>
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

				<td style="text-align: left"  ><?php echo $items->tipo_doc.$items->numero; ?></td>
				<td style="text-align: center"><?php echo dbdate_to_human($items->fecha);  ?></td>
				<td style="text-align: center"><?php echo dbdate_to_human($items->vence);  ?></td>
				<td style="text-align: right" ><?php $ittot['monto']   += $items->monto  ; echo nformat($items->monto  ,2); ?></td>
				<td style="text-align: right" ><?php $ittot['abono']   += $items->abono  ; echo nformat($items->abono  ,2); ?></td>
				<td style="text-align: right" ><b><?php echo nformat($items->monto-$items->abono,2); ?></b></td>
				<?php
				$lineas++;
				if($lineas >= $maxlin){
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
for(1; $lineas<$maxlin;$lineas++){ ?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
<?php
	$mod = ! $mod;

}
echo sprintf($pie_final,nformat($ittot['monto']),nformat($ittot['abono']),nformat($ittot['monto']-$ittot['abono']));
?></body>
</html>
