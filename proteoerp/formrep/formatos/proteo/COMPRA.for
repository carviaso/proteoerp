<?php
if(count($parametros)==0) show_error('Faltan parametros ');
$id  = $parametros[0];
$dbid= $this->db->escape($id);

//ENCABEZADO
$moneda = $this->datasis->traevalor('MONEDA');
$mSQL_1 = $this->db->query("SELECT
a.tipo_doc,a.numero,a.fecha,a.vence,a.control,a.actuali,a.depo,a.proveed,b.nombre,TRIM(b.nomfis) AS nomfis,a.montotot,a.montoiva,a.montonet,a.peso, a.transac,
if(a.actuali>=a.fecha,'CARGADA','PENDIENTE') cargada, a.control, a.cexento, a.cgenera, a.creduci, a.cadicio, a.cimpuesto, a.ctotal, a.reten, a.reteiva, a.cstotal,
observa1,observa2,observa3,a.recep
FROM scst AS a
JOIN sprv AS b ON a.proveed=b.proveed
WHERE a.id=${dbid}");
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    =dbdate_to_human($row->fecha);
$numero   =$row->numero;
$control  =$row->control;
$depo     =trim($row->depo);
$proveed  =$this->us_ascii2html($row->proveed);
$nombre   =(empty($row->nomfis))? $this->us_ascii2html(trim($row->nombre)) : $this->us_ascii2html($row->nomfis);
$montotot =$row->montotot;
$montoiva =$row->montoiva;
$montonet =$row->montonet;
$peso     =$row->peso;
$cargada  =$row->cargada;
$control  =$row->control;
$recep    =$row->recep;
$vence    =dbdate_to_human($row->vence);
$actuali  =dbdate_to_human($row->actuali);
$observa  =$this->us_ascii2html(trim($row->observa1).' '.trim($row->observa2).' '.trim($row->observa3));

$cexento   =$row->cexento;
$cgenera   =$row->cgenera;
$creduci   =$row->creduci;
$cadicio   =$row->cadicio;
$cimpuesto =$row->cimpuesto;
$ctotal    =$row->ctotal;
$reten     =$row->reten;
$reteiva   =$row->reteiva;
$cstotal   =$row->cstotal;

$crecep='';
if($cargada=='CARGADA'){
	$crecep=' Recepcionada: <b>'.dbdate_to_human($recep).'</b>';
}

$reten = $this->datasis->dameval("SELECT SUM(monto) FROM sprm WHERE cod_prv='RETEN' AND transac=".$row->transac)+0;

if($row->tipo_doc=='FC'){
	$tit1 = 'Compra';
}elseif($row->tipo_doc=='NC'){
	$tit1 = 'Nota de Cr&eacute;dito';
}elseif($row->tipo_doc=='NE'){
	$tit1 = 'Nota de Entrega';
}else{
	$tit1 = 'Documento';
}
$dbcontrol=$this->db->escape($control);
//ARTICULOS
$mSQL_2 = $this->db->query("SELECT numero,codigo,descrip,cantidad,costo,importe, precio2, if(costo>=precio2,'===>>','     ') alerta FROM itscst WHERE control=${dbcontrol}");
$detalle =$mSQL_2->result();

$pagina = 0;
$maxlinea = 30;

//ENCABEZADO PRINCIPAL
$encabeza = '
<div id="section_header">
	<table style="width: 100%;" class="header">
		<tr>
			<td width=140 rowspan="2"><img src="'.$this->_direccion.'/images/logo.jpg" width="127"></td>
			<td><h1 style="text-align: right">'.$this->datasis->traevalor('TITULO1').'</h1></td>
		</tr><tr>
			<td>
			<div class="page" style="font-size: 7pt">'.$this->datasis->traevalor('TITULO2').' '.$this->datasis->traevalor('TITULO3').'<br>
				<b>RIF: '.$this->datasis->traevalor('RIF').'</b>
			</div>
			</td>
		</tr>
</table>
</div>
	<div class="page" style="font-size: 7pt">
		<table style="width:100%;font-size:7pt;" class="header">
		<tr>
			<td valign=\'bottom\'><h1 style="text-align: left">'.$tit1.' '.$cargada.'</h1></td>
			<td valign=\'bottom\'><h1 style="text-align: right">N&uacute;mero: '.$numero.'</h1></td>
		</tr>
	</table>
</div>
';
// ENCABEZADO PRIMERA PAGINA
$encabeza1p = '
				<table style="width: 100%; font-size: 8pt;">
				<tr>
					<td>Almac&eacute;n: <b>'.$depo.'</b></td>
					<td>Actualizado: <b>'.$actuali.'</b></td>
				</tr><tr>
					<td>Fecha: <b>'.$fecha.'</b> '.$crecep.'</td>
					<td>Vencimiento: <b>'.$vence.'</b></td>
				</tr><tr>
					<td>Proveedor: <b>('.$proveed.') '.$nombre.'</b></td>
					<td>Peso: <b>'.$peso.'</b></td>
				</tr>
				<tr>
					<td colspan=\'2\'>Observaci&oacute;n: <b>'.$observa.'</b></td>
				</tr>
				</table>
';

$encatabla = '
			<tr style="background-color:black;border-style:solid;color:white;font-weight:bold">
				<th>C&oacute;digo</th>
				<th>Descripci&oacute;n</th>
				<th>Cantidad</th>
				<th>Costo</th>
				<th>Asignado</th>
				<th>Importe</th>
			</tr>
';

?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title><?php echo $tit1.' '.$numero; ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body>
<script type="text/php">
if ( isset($pdf) ) {
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
	$pdf->add_object($foot, "all");
	$text = "PP {PAGE_NUM} de {PAGE_COUNT}";
	// Center the text
	$width = Font_Metrics::get_text_width("PP 1 de 2", $font, $size);
	$pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);
}
</script>

<div id="body">

<?php
$mod=FALSE;
$i=0;
$pagina = 0 ;
foreach ($detalle AS $items){
	$i++;
	if ( $pagina == 0 ) {
?>
<table style="width: 100%;">
		<thead><tr>
			<td><?php echo $encabeza.' '.$encabeza1p ?></td>
		</tr></thead>
		<tr>
			<td><div id="content"><div class="page" style="font-size: 7pt">
				<table class="change_order_items">
					<thead><?php echo $encatabla ?></thead>
					<tbody>
<?php
		$pagina = $pagina+1;
	};
?>
				<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
					<td style="text-align:left"><?php echo $this->us_ascii2html($items->codigo) ?></td>
					<td><?php echo $this->us_ascii2html($items->descrip) ?></td>
					<td style="text-align: center"><?php echo nformat($items->cantidad,0)  ?></td>
					<td style="text-align: right;"><?php echo nformat($items->costo).$moneda  ?></td>
					<td style="text-align: right;"><?php echo "<b>".$items->alerta."</b>".nformat($items->precio2) ?></td>
					<td style="text-align: right;"><?php echo  nformat($items->importe).$moneda;   ?></td>
				</tr>
<?php
	if($i%$maxlinea == 0) {
		$pagina = $pagina+1;
?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="6" style="text-align: right;font-size:16px"><b>Continua.........</b></td>
			</tr>
			</tfoot>
			</table>
			</div>
		</div></td>
	</tr>
</table>
<p STYLE='page-break-after: always'></p>
<table style="width: 100%;">
		<thead><tr>
			<td><?php echo $encabeza." ".$encabeza1p ?></td>
		</tr></thead>
		<tr>
			<td><div id="content"><div class="page" style="font-size: 7pt">
				<table class="change_order_items">
					<thead><?php echo $encatabla ?></thead>
					<tbody>
<?php
	};
	$mod = ! $mod;
}
while ( $i%$maxlinea != 0) {
	$i++;
?>
				<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
					<td style="text-align:left"> </td>
					<td> </td>
					<td style="text-align: center;"> </td>
					<td style="text-align: center;"> </td>
					<td style="text-align: center;"> </td>
					<td class="change_order_total_col"> . </td>
				</tr>
<?php
	$mod = ! $mod;

}
?>
			</tbody>
			<tfoot>
			<tr><td colspan="7">
			<table width="100%">
				<tr>
					<td style="text-align:center;"><b>Preparado por:</b></td>
					<td style="text-align:center;"><b>Autorizado por:</b></td>
					<td style="text-align: right;"><b>SUB-TOTAL:</b></td>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($montotot).$moneda?></b></td>
				</tr	><tr>
					<td style="text-align:center;"></td>
					<td style="text-align:center;"></td>
					<td style="text-align: right;"><b>IMPUESTO:</b></td>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($montoiva).$moneda ?></b></td>
				</tr><tr>
					<td style="border-bottom-style:solid;text-align:center;"></td>
					<td style="border-bottom-style:solid;text-align:center;"></td>
					<td style="text-align: right;"><b>TOTAL:</b></td>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($montonet).$moneda ?></b></td>
				</tr>
			</table>
			</td></tr>
			<tr><td colspan="6">
			<table width="100%">
				<tr>
					<td style="text-align:center;"><b>Exento</b></td>
					<td style="text-align:center;"><b>Base</b></td>
					<td style="text-align:center;"><b>Impuesto</b></td>
					<td style="text-align:center;"><b>Total</b></td>
					<td style="text-align:center;"><b>R. IVA</b></td>
					<td style="text-align:center;"><b>ISLR</b></td>
					<td style="text-align:center;"><b>Neto CxP</b></td>
					<td style="text-align:center;"><b>Desc. por Aprovechar</b></td>
				</tr><tr>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($cexento).$moneda?></b></td>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($cstotal).$moneda?></b></td>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($cimpuesto).$moneda?></b></td>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($ctotal).$moneda?></b></td>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($reteiva).$moneda?></b></td>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($reten).$moneda?></b></td>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($ctotal-$reteiva-$reten).$moneda?></b></td>
					<td style="border-style:solid;" class="change_order_total_col"><b><?php echo  nformat($ctotal-$montonet).$moneda?></b></td>
				</tr>
			</table>
			</td></tr>

			</tfoot>
			</table>
			</div>
		</div></td>
	</tr>
</table>

</div>
</body>
</html>
