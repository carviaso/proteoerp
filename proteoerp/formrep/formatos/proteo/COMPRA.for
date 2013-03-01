<?php
if(count($parametros)==0) show_error('Faltan parametros ');
$id  = $parametros[0];
$dbid= $this->db->escape($id);
if(count($parametros)>1){
	$control = $this->datasis->dameval('SELECT control  FROM scst  WHERE id='.$dbid);
}

//ENCABEZADO
$moneda = $this->datasis->traevalor('MONEDA');
$mSQL_1 = $this->db->query("SELECT
a.numero,a.fecha,a.vence,a.actuali,a.depo,a.proveed,b.nombre,TRIM(b.nomfis) AS nomfis,a.montotot,a.montoiva,a.montonet,a.peso,
if(a.actuali>=a.fecha,'CARGADA','PENDIENTE') cargada, a.control
FROM scst AS a
JOIN sprv AS b ON a.proveed=b.proveed
WHERE a.id=${dbid}");
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    =dbdate_to_human($row->fecha);
$numero   =$row->numero;
$depo     =trim($row->depo);
$proveed  =htmlspecialchars($row->proveed);
$nombre   =(empty($row->nomfis))? htmlspecialchars(trim($row->nombre)) : htmlspecialchars($row->nomfis);
$montotot =$row->montotot;
$montoiva =$row->montoiva;
$montonet =$row->montonet;
$peso     =$row->peso;
$cargada  =$row->cargada;
$control  =$row->control;
$vence    =dbdate_to_human($row->vence);
$actuali  =dbdate_to_human($row->actuali);

$dbcontrol=$this->db->escape($control);
//ARTICULOS
$mSQL_2 = $this->db->query("SELECT numero,codigo,descrip,cantidad,costo,importe, precio2, if(costo>=precio2,'===>>','     ') alerta FROM itscst WHERE control=${dbcontrol}");
$detalle =$mSQL_2->result();

$pagina = 0;
$maxlinea = 28;

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
			<td><h1 style="text-align: left">Compra '.$cargada.'</h1></td>
			<td><h1 style="text-align: right">N&uacute;mero: '.$numero.'</h1></td>
		</tr>
	</table>
</div>
';
// ENCABEZADO PRIMERA PAGINA
$encabeza1p = '
				<table style="width: 100%; font-size: 8pt;">
				<tr>
					<td>Almac&eacute;n: <strong>'.$depo.'</strong></td>
					<td>Actualizado: <strong>'.$actuali.'</strong></td>
				</tr><tr>
					<td>Fecha: <strong>'.$fecha.'</strong></td>
					<td>Vencimiento: <strong>'.$vence.'</strong></td>
				</tr><tr>
					<td>Proveedor: <strong>('.$proveed.') '.$nombre.'</strong></td>
					<td>Peso: <strong>'.$peso.'</strong></td>
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
<title>Compra <?php echo $numero ?></title>
<link rel="STYLESHEET" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" />
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
foreach ($detalle AS $items){ $i++;
	if ( $pagina == 0 ) {
?>
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
		$pagina = $pagina+1;
	};
?>
				<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
					<td style="text-align:left"><?php echo $items->codigo ?></td>
					<td><?php echo htmlspecialchars($items->descrip) ?></td>
					<td style="text-align: center"><?php echo nformat($items->cantidad,0)  ?></td>
					<td style="text-align: right;"><?php echo nformat($items->costo).$moneda  ?></td>
					<td style="text-align: right;"><?php echo "<b>".$items->alerta."</b>".nformat($items->precio2) ?></td>
					<td class="change_order_total_col"><?php echo  nformat($items->importe).$moneda   ?></td>
				</tr>
<?php
	if($i%$maxlinea == 0) {
		$pagina = $pagina+1;
?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="6" style="text-align: right;font-size:16px"><strong>Continua.........</strong></td>
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
?>
			</tbody>
			<tfoot>
			<tr><td colspan="7">
			<table width="100%">
				<tr>
					<td style="text-align:center;"><strong>Preparado por:</strong></td>
					<td style="text-align:center;"><strong>Autorizado por:</strong></td>
					<td style="text-align: right;"><strong>SUB-TOTAL:</strong></td>
					<td style="border-style:solid;" class="change_order_total_col"><strong><?php echo  nformat($montotot).$moneda?></strong></td>
				</tr	><tr>
					<td style="text-align:center;"></td>
					<td style="text-align:center;"></td>
					<td style="text-align: right;"><strong>IMPUESTO:</strong></td>
					<td style="border-style:solid;" class="change_order_total_col"><strong><?php echo  nformat($montoiva).$moneda ?></strong></td>
				</tr><tr>
					<td style="border-bottom-style:solid;text-align:center;"></td>
					<td style="border-bottom-style:solid;text-align:center;"></td>
					<td style="text-align: right;"><strong>TOTAL:</strong></td>
					<td style="border-style:solid;" class="change_order_total_col"><strong><?php echo  nformat($montonet).$moneda ?></strong></td>
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
