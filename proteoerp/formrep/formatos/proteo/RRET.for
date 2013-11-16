<?php
if(count($parametros)==0) show_error('Faltan parametros');
$id = $parametros[0];
$dbid=$this->db->escape($id);

$mSQL=$this->db->query("SELECT a.estampa,a.cajero,b.nombre FROM rret AS a JOIN scaj AS b ON a.cajero=b.cajero WHERE a.id=${dbid}");
if($mSQL->num_rows()==0) show_error('Registro no encontrado');

$row      = $mSQL->row();
$fecha    = dbdate_to_human($row->estampa,'d/m/Y H:i:s');
$cajero   = htmlspecialchars(trim($row->cajero));
$ncajero  = $this->us_ascii2html($row->nombre);
$dbcajero = $this->db->escape($row->cajero);
$dbestampa= $this->db->escape($row->estampa);

$mSQL_2="SELECT a.tipo,a.monto,b.nombre FROM rret AS a JOIN tarjeta AS b ON a.tipo=b.tipo
	WHERE estampa=${dbestampa} AND cajero=${dbcajero}";
?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Retiro de Cajero</title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" />
</head>
<body>
<script type="text/php">
if(isset($pdf)){
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
		<td>
			<?php $this->incluir('X_CINTILLO'); ?>
			<div class="page" style="font-size: 7pt">
			<table style="width: 100%;" class="header">
				<tr>
					<td valign='bottom'><h1 style="text-align: left">Retiro de cajero <?php echo $cajero.' - '.$ncajero ?></h1></td>
					<td valign='bottom'><h1 style="text-align: right">Fecha: <?php echo $fecha ?></h1></td>
				</tr>
			</table>
			</div>
		</td>
	  </tr>
	</thead>
	<tr>
	  <td><div id="content">
		  <div class="page" style="font-size: 14pt">
			<table class="change_order_items">
			  <thead>
				<tr>
				  <th style="font-size: 16px;">Forma de pago</th>
				  <th style="font-size: 16px;">Monto</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$montotal=0;
				$qq = $this->db->query($mSQL_2);
				$mod=false;

				foreach($qq->result() as $rrow){
					$mod=!$mod;
					$class=($mod) ? 'even_row' :  'odd_row';
					$montotal += $rrow->monto;
					echo "<tr class='$class'>";
					echo '	<td style="font-size: 16px;">'.$rrow->tipo.' - '.$rrow->nombre.'</td>';
					echo '	<td style="text-align: right; font-size: 16px;">'.$rrow->monto.'</td>';
					echo '</tr>';
				}
				?>
				</tbody>
				<tfoot>
				<tr>
					<td style="text-align: right; font-size: 16px;"><b>TOTAL:</b></td>
					<td style="text-align: right; font-size: 16px;" class="change_order_total_col"><b><?php echo nformat($montotal) ?></b></td>
				</tr>
				</tfoot>
			</table>
		  </div>
		</div>
		</td>
	</tr>
		<tr>
			<td>
				<table style="width: 100%;">
					<tr>
						<td style="text-align: center; font-size: 12px; border-style:solid; width: 50%;">
							<br><br><br><br><br><b>Cajero</b>
						</td>
						<td style="text-align: center; font-size: 12px; border-style:solid; width: 50%;">
							<br><br><br><br><br><b>Recibido</b>
						</td>
					</tr>
				</table>
			</td>
		</tr>
  </table>
</div>
</body>
</html>
