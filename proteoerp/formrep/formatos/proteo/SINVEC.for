<?php
if(count($parametros)==0)
	show_error('Faltan parametros ');
$id=$parametros[0];
$dbid=$this->db->escape($id);

$mSQL=$this->db->query('
SELECT a.codigo, a.descrip, a.ultimo, a.existen,  a.base1, curdate() fecha,
(SELECT sum(aa.existen) FROM sinv aa WHERE aa.existen>0 AND aa.ultimo>0) texisten, 
ROUND(a.existen/(SELECT sum(aa.existen) FROM sinv aa WHERE aa.existen>0 AND aa.ultimo>0)*100,4) prorrata, 
(SELECT SUM(fijo+promedio) FROM sinvec bb WHERE bb.grupo<>"99") gmes, 
ROUND((SELECT SUM(fijo+promedio) FROM sinvec bb WHERE bb.grupo<>"99")*a.existen/(SELECT sum(aa.existen) FROM sinv aa WHERE aa.existen>0 AND aa.ultimo>0),4) progasmes, 
ROUND((SELECT SUM(fijo+promedio) FROM sinvec bb WHERE bb.grupo<>"99")/(SELECT sum(aa.existen) FROM sinv aa WHERE aa.existen>0 AND aa.ultimo>0),4) progmun, 
ROUND((SELECT SUM(fijo+promedio) FROM sinvec bb WHERE bb.grupo<>"99")/(SELECT sum(aa.existen) FROM sinv aa WHERE aa.existen>0 AND aa.ultimo>0)+a.ultimo,4) tgo, 
ROUND((SELECT SUM(fijo+promedio) FROM sinvec bb WHERE bb.grupo<>"99")/(SELECT sum(aa.existen) FROM sinv aa WHERE aa.existen>0 AND aa.ultimo>0)+a.ultimo,4)*0.3 tgo30, 
ROUND((SELECT SUM(fijo+promedio) FROM sinvec bb WHERE bb.grupo<>"99")/(SELECT sum(aa.existen) FROM sinv aa WHERE aa.existen>0 AND aa.ultimo>0)+a.ultimo,4)*1.3 tgo130, 
LEAST(ROUND((SELECT SUM(fijo+promedio) FROM sinvec bb WHERE bb.grupo<>"99")/(SELECT sum(aa.existen) FROM sinv aa WHERE aa.existen>0 AND aa.ultimo>0),4)*100/a.ultimo,12.5) porcen, 
1.3*a.ultimo*(1+LEAST(ROUND((SELECT SUM(fijo+promedio) FROM sinvec bb WHERE bb.grupo<>"99")/(SELECT sum(aa.existen) FROM sinv aa WHERE aa.existen>0 AND aa.ultimo>0),4)/a.ultimo,0.125)) pvp 
FROM sinv a 
WHERE MID(a.tipo,1,1)="A" AND a.id='.$dbid 
);

$row = $mSQL->row();

$fecha     = dbdate_to_human($row->fecha);
$codigo    = $row->codigo;
$descrip   = $row->descrip;
//$costo     = $row->costo;
$existen   = $row->existen; 
$ultimo    = $row->ultimo; 
$base1     = $row->base1;  
$texisten  = $row->texisten;
$prorrata  = $row->prorrata;
$gmes      = $row->gmes;
$progasmes = $row->progasmes;
$progmun   = $row->progmun;
$tgo       = $row->tgo;
$tgo30     = $row->tgo30;
$tgo130    = $row->tgo130;
$porcen    = $row->porcen;
$pvp       = $row->pvp;



$titulo= $this->datasis->traevalor('TITULO1');


?>
<html>
<head>
<title>Ficha Tecnica codigo <?php echo $codigo ?></title>
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
	<table style="width: 100%;" border='1'>
		<thead>
			<tr>
				<td><div id="section_header">
						<table style="width: 100%;" class="header">
							<tr>
								<td width=140 rowspan="2"><img src="<?php echo $this->_direccion ?>/images/logo.jpg" alt='<?php echo str_replace ("'","/'",$titulo); ?>'></td>
								<td><h1 style="text-align: right"><?php echo $titulo; ?></h1>
									</td>
							</tr>
							<tr>
								<td>
									<div class="page" style="font-size: 7pt">
										<?php echo $this->datasis->traevalor('TITULO2').' '.$this->datasis->traevalor('TITULO3'); ?> <br>
										<RIF: <?php echo $this->datasis->traevalor('RIF'); ?></
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div class="page" style="font-size: 7pt">
						<table style="width: 100%;" class="header">
							<tr>
								<td><h1 style="text-align: left">ESTRUCTURA DE COSTOS </h1></td>
								<td><h1 style="text-align: right">Fecha: <?php echo $fecha ?></h1></td>
							</tr>
						</table>
						<table style="width: 100%; font-size: 14pt;">
							<tr>
								<td>Producto:</td><td><strong><?php echo $codigo;    ?></strong></td>
							</tr><tr>
								<td>Descripcion:</td><td><strong><?php echo $descrip; ?></strong></td>
							</tr><tr>
								<td colspan='4'><table width='100%'><tr>
									<td>Existencia:</td><td><strong><?php echo round($existen,0); ?></strong></td>
									<td>Costo:</td><td><strong><?php echo $ultimo; ?></strong></td>
									<td>Costo Total:</td><td><strong><?php echo nformat($ultimo*$existen); ?></strong></td>
								</tr></table></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
		</thead>
		<tr>
			<td>
				<div id="content">
					<div class="page" style="font-size: 14pt">
					<?php
					$mSQLs=array();
					$mSQLs[]="
						SELECT a.grupo, a.cuenta, a.descripcion, sum(a.fijo+a.promedio) monto, b.descrip gdescrip
						FROM sinvec AS a JOIN grec b ON a.grupo=b.grupo
						WHERE a.grupo<>'99'
						GROUP BY a.grupo
						ORDER BY a.grupo";

					$grupo = 'XXXX';
					$monto = 0;
					foreach($mSQLs AS $id=>$mSQL){
						$monto = 0;
						$qquery = $this->db->query($mSQL);
						if ($qquery->num_rows() > 0){
					?>
						<table class="change_order_items">
							<thead>
								 <tr>
									<th colspan='4' style='font-size:16px;'>BASE FINANCIERA</th>
								 </tr>
								<tr>
									<th style="font-size: 16px;">Concepto</th>
									<th style="font-size: 16px;">Importe</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($qquery->result() as $i=>$rrow){

									$class=($i%2) ? 'even_row' :  'odd_row';
									echo "<tr class='$class'  style='font-size: 12pt' >";
									echo '<td>'.$rrow->gdescrip.'</td>';
									echo '<td style="text-align: right">'.nformat($rrow->monto).'</td>';
									echo '</tr>';
									$monto +=$rrow->monto;
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<td style="text-align: right; font-size: 16px;"><b>TOTAL GASTO OPERATIVO:</b></td>
									<td style="text-align: right; font-size: 16px;" class="change_order_total_col"><b><?php echo nformat($monto) ?></b></td>
								</tr>
								<tr>
									<td style="text-align: right; font-size: 14px;"><b>PROPORCION APLICABLE AL COSTO (12.5%max):</b></td>
									<td style="text-align: right; font-size: 14px;" class="change_order_total_col"><b><?php echo nformat($ultimo*(100+$porcen)/100 - $ultimo) ?></b></td>
								</tr>
								<tr>
									<td style="text-align: right; font-size: 16px;"><b>COSTO TOTAL DEL PRODUCTO:</b></td>
									<td style="text-align: right; font-size: 16px;" class="change_order_total_col"><b><?php echo nformat($ultimo*(100+$porcen)/100) ?></b></td>
								</tr>

								<tr>
									<td style="text-align: right; font-size: 14px;"><b>% GASTO OPERATIVO APLICADO:</b></td>
									<td style="text-align: right; font-size: 14px;" class="change_order_total_col"><b><?php echo nformat($porcen) ?>%</b></td>
								</tr>

							</tfoot>
						</table>               
					<?php
						}  
					}
					?>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
</body>
</html>
