<?php
if(count($parametros)==0)
	show_error('Faltan parametros ');
$numero=$parametros[0];
$dbnumero=$this->db->escape($numero);
$mSQL=$this->db->query("SELECT a.tipo, a.fecha,a.cajero,b.nombre, a.caja,a.recibido, a.ingreso,a.observa, a.usuario FROM rcaj AS a  JOIN scaj AS b  ON a.cajero=b.cajero WHERE numero=$dbnumero");
$row = $mSQL->row();

$fecha    = dbdate_to_human($row->fecha);
$cajero   = $row->cajero;
$caja     = $row->caja;
$observa  = $row->observa;
$usuario  = $row->usuario;
$scajdes  = $row->nombre;

$titulo= $this->datasis->traevalor('TITULO1');

$this->db->select(array('a.numero','a.nombre','a.totalg',));
$this->db->from('sfac AS a');
$this->db->where('a.cajero',$row->cajero);
$this->db->where('a.fecha',$row->fecha);
$this->db->where('a.tipo_doc','D');
$qdev = $this->db->get();
?>
<html>
<head>
<title>Arqueo de Caja<?php echo $numero ?></title>
<link rel="STYLESHEET" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
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
	<table style="width: 100%;">
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
								<td><h1 style="text-align: left">Cierre de caja </h1></td>
								<td><h1 style="text-align: right">N&uacute;mero: <?php echo $numero ?></h1></td>
							</tr>
						</table>
						<table style="width: 100%; font-size: 14pt;">
							<tr>
								<td>Caja:<strong><?php echo $caja;    ?></strong></td>
								<td>Fecha: <strong><?php echo $fecha; ?></strong></td>
							</tr>
							<tr>
								<td>Cajero: <strong><?php echo $cajero; ?></strong></td>
								<td>Nombre: <strong><?php echo $scajdes; ?></strong></td>
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
					$mSQLs[]="SELECT a.tipo, b.nombre,a.recibido, a.sistema, a.diferencia
						 FROM itrcaj AS a
						 JOIN tarjeta AS b ON a.tipo=b.tipo
						 WHERE a.numero=$dbnumero AND cierre='N'";

					$mSQLs[]="SELECT a.tipo, b.nombre,a.recibido, a.sistema, a.diferencia
						 FROM itrcaj AS a
						 JOIN tarjeta AS b ON a.tipo=b.tipo
						 WHERE a.numero=$dbnumero AND cierre='S'";
							$titulo=array('Seg&uacute;n Pre-cierre',' Seg&uacute;n Cierre');

					foreach($mSQLs AS $id=>$mSQL){
						$recibido =$ingreso =0;
						$qquery = $this->db->query($mSQL);
						if ($qquery->num_rows() > 0){
					?>
						<table class="change_order_items">
							<thead>
								 <tr>
									<th colspan='4'><h1><?php echo $titulo[$id]; ?></h1></th>
								 </tr>
								<tr>
									<th style="font-size: 16px;">Forma de pago</th>
									<th style="font-size: 16px;">Monto recibido</th>
									<th style="font-size: 16px;">Monto sistema</th>
									<th style="font-size: 16px;">Diferencia</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($qquery->result() as $i=>$rrow){
									$class=($i%2) ? 'even_row' :  'odd_row';
									echo "<tr class='$class'  style='font-size: 12pt' >";
									echo '<td>('.$rrow->tipo.') '.$rrow->nombre.'</td>';
									echo '<td style="text-align: right">'.nformat($rrow->recibido).'</td>';
									echo '<td style="text-align: right">'.nformat($rrow->sistema).'</td>';
									echo '<td style="text-align: right">'.nformat($rrow->recibido-$rrow->sistema).'</td>';
									echo '</tr>';
									$recibido +=$rrow->recibido;
									$ingreso +=$rrow->sistema;
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<td ></td>
									<td ></td>
									<td style="text-align: right; font-size: 16px;"><b>TOTAL:</b></td>
									<td style="text-align: right; font-size: 16px;" class="change_order_total_col"><b><?php echo nformat($ingreso) ?></b></td>
								</tr>
								<tr>
									<td ></td>
									<td ></td>
									<td style="text-align: right; font-size: 16px;"><b>RECIBIDO:</b></td>
									<td style="text-align: right; font-size: 16px;" class="change_order_total_col"><b><?php echo nformat($recibido) ?></b></td>
								</tr>
								<tr>
									<td ></td>
									<td ></td>
									<td style="text-align: right; font-size: 16px;"><b>DIFERENCIA:</b></td>
									<td style="text-align: right; font-size: 16px;" class="change_order_total_col"><b><?php echo nformat(-$ingreso+$recibido) ?></b></td>
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

	<div class="page" style="font-size: 14pt">
	<table class="change_order_items">
		<thead>
		<tr>
			<th colspan='3'><h1>Devoluciones</h1></th>
		</tr>
		<tr>
			<th style="font-size: 16px;">N&uacute;mero</th>
			<th style="font-size: 16px;">Nombre</th>
			<th style="font-size: 16px;">Monto</th>
		</tr>
		</thead>
		<tbody>
			<?php
			$total=0;
			foreach ($qdev->result() as $i=>$rrow){
				$class=($i%2) ? 'even_row' :  'odd_row';
				echo "<tr class='$class'  style='font-size: 12pt' >";
				echo '<td>'.$rrow->numero.'</td>';
				echo '<td style="text-align: left">'.$rrow->nombre.'</td>';
				echo '<td style="text-align: right">'.nformat($rrow->totalg).'</td>';
				echo '</tr>';
				$total +=$rrow->totalg;
			}
			?>
		</tbody>
		<tfoot>
		<tr>
			<td></td>
			<td style="text-align: right; font-size: 16px;"><b>TOTAL:</b></td>
			<td style="text-align: right; font-size: 16px;" class="change_order_total_col"><b><?php echo nformat($total) ?></b></</td>
		</tr>
		</tfoot>
	</table>
	</div>
</div>
</body>
</html>
