<?php if(count($parametros) < 1) show_error('Faltan parametros');
$id = $parametros[0];

$sel=array('a.emision','a.periodo','a.tipo_doc','a.fecha','a.numero','a.nfiscal','a.afecta', 'c.serie serie1','d.serie serie2'
,'a.clipro','TRIM(b.nombre) AS nombre','TRIM(b.nomfis) AS nomfis','a.rif','a.exento','CONCAT_WS(\' \',TRIM(b.direc1),b.direc2) AS direc'
,'a.tasa'    ,'a.general'  ,'a.geneimpu'
,'a.tasaadic','a.adicional','adicimpu'
,'a.tasaredu','a.reducida' ,'a.reduimpu'
,'a.stotal','a.impuesto','a.gtotal','a.reiva','a.nrocomp','a.transac');
$this->db->select($sel);
$this->db->from('riva AS a');
$this->db->join('sprv AS b','a.clipro=b.proveed');
$this->db->join('gser AS c','a.transac=c.transac','LEFT');
$this->db->join('scst AS d','a.transac=d.transac','LEFT');

$this->db->where('a.id' , $id);
$mSQL_1 = $this->db->get();

if ($mSQL_1->num_rows() == 0){ show_error('Retención no encontrada');}

$row = $mSQL_1->row();

$nrocomp   = htmlspecialchars(trim($row->nrocomp));
$emision   = dbdate_to_human($row->emision);
$periodo   = trim($row->periodo)  ;
$tipo_doc  = htmlspecialchars(trim($row->tipo_doc));
$transac   = $row->transac;
$fecha     = dbdate_to_human($row->fecha);
$nfiscal   = htmlspecialchars(trim($row->nfiscal));
$afecta    = htmlspecialchars(trim($row->afecta)) ;
$clipro    = $this->us_ascii2html($row->clipro) ;
$nombre    = (empty($row->nomfis))? $this->us_ascii2html($row->nombre) : $this->us_ascii2html($row->nomfis);
$direc     = $this->us_ascii2html($row->direc);

//$numero    = trim($row->numero);
$nnumero   = trim($row->serie1.$row->serie2);
if(empty($nnumero)){
	$dbtipo_doc= $this->db->escape($tipo_doc);
	$dbproveed = $this->db->escape($clipro);
	$dbnumero  = $this->db->escape(trim($row->numero));
	$dbtransac = $this->db->escape($transac);
	$nnumero=$this->datasis->dameval("SELECT serie FROM sprm WHERE tipo_doc=${dbtipo_doc} AND transac=${dbtransac} AND cod_prv=${dbproveed} AND numero=${dbnumero}");
	if(empty($nnumero)){
		$nnumero=trim($row->numero);
	}
}
$numero    = htmlspecialchars($nnumero) ;

$rif       = htmlspecialchars(trim($row->rif));
$exento    = $row->exento   ;
$tasa      = $row->tasa     ;
$general   = $row->general  ;
$geneimpu  = $row->geneimpu ;
$tasaadic  = $row->tasaadic ;
$adicional = $row->adicional;
$adicimpu  = $row->adicimpu ;
$tasaredu  = $row->tasaredu ;
$reducida  = $row->reducida ;
$reduimpu  = $row->reduimpu ;
$stotal    = $row->stotal   ;
$impuesto  = $row->impuesto ;
$gtotal    = $row->gtotal   ;
$reiva     = $row->reiva    ;
$tipotra   = '01';
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Comprobante de retenci&oacute;n de IVA <?php echo $numero ?></title>
<link rel="STYLESHEET" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body>
<script type="text/php">
if ( isset($pdf) ) {

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
					<div class="page" style="font-size: 7pt">
						<table style="width: 100%;" class="header">
							<tr>
								<td colspan='2'><h1 style="text-align: center">COMPROBANTE DE RETENCI&Oacute;N DEL IMPUESTO AL VALOR AGREGADO</h1></td>
							</tr>
							<tr>
								<td align='center' colspan='2'>(Ley IVA - Art. 11: La Administraci&oacute;n Tributaria podr&aacute; designar como responsables del pago del impuesto, en calidad de agentes de
								retenci&oacute;n, a quienes por sus funciones p&uacute;blicas o por raz&oacute;n de sus actividades privadas
								intervengan en operaciones gravadas con el impuesto establecido en esta Ley.)</td>
							</tr>
						</table>
						<br />
						<table style="width: 100%;">
							<tr>
								<td style="text-align:center;font-size:12pt; ">N&uacute;mero: <b><?php echo str_replace('-','',$periodo).$nrocomp ?></b></td>
								<td style="text-align:center;font-size:12pt;">Per&iacute;odo F&iacute;scal: <b><?php echo $periodo; ?></b></td>
								<td style="text-align:center;font-size:12pt;">Fecha de Emisi&oacute;n: <b><?php echo $emision; ?></b></td>
							</tr>
						</table>
						</div>
						<br>
						<span style="text-align: left">Agente de Retenci&oacute;n</span>
						<div class="page" style="font-size: 7pt">
						<p style="font-size: 10pt;font-weight:bold;">
							<?php echo $this->datasis->traevalor('TITULO1'); ?><br>
							RIF: <?php echo str_replace ('-','',$this->datasis->traevalor('RIF')); ?>
						</p>
						<p style="font-size: 10pt;">
							<?php echo $this->datasis->traevalor('TITULO2').' '.$this->datasis->traevalor('TITULO3'); ?>
						</p>
						</div>
						<br />
						<span style="text-align: left">Sujeto de Retenci&oacute;n</span>
						<div class="page" >
						<p style="font-size: 10pt;font-weight:bold;">
							<?php echo $nombre;  ?><br>
							<b>RIF: <?php echo $rif; ?></b>
						</p>
						<p style="font-size: 10pt;">
							<?php echo $direc ?>
						</p>
						<br />
						<br />

						<table align='center' style="font-size: 11pt;">
							<tr>
								<td><b>Fecha del Documento:</b></td>
								<td><?php echo $fecha; ?></td>
							</tr>

					<?php if ($tipo_doc=='FC'){
						$tipotra   = '01';
					?>
							<tr>
								<td><b>N&uacute;mero de Factura:</b></td>
								<td><?php echo ($tipo_doc=='FC')? $numero:' '; ?></td>
							</tr>
					<?php }; ?>

					<?php if ( $tipo_doc=='ND'){
						$tipotra   = '02';
					?>
							<tr>
								<td><b>N&uacute;mero de Nota de D&eacute;bito:</b></td>
								<td><?php echo ($tipo_doc=='ND')? $numero:' '; ?></td>
							</tr>
					<?php }; ?>

					<?php if ( $tipo_doc=='NC'){
						$tipotra   = '03';
					?>
							<tr>
								<td><b>N&uacute;mero de Nota de Cr&eacute;dito:</b></td>
								<td><?php echo ($tipo_doc=='NC')? $numero:' '; ?></td>
							</tr>
					<?php }; ?>

							<tr>
								<td><b>N&uacute;mero de control:</b></td>
								<td><?php echo $nfiscal; ?></td>
							</tr>

							<tr>
								<td><b>Tipo de Transacci&oacute;n:</b></td>
								<td><?php echo $tipotra; ?></td>
							</tr>

							<?php if ( !empty($afecta) ){ ?>
							<tr>
								<td><b>N&uacute;mero de Documento Afectado:</b></td>
								<td><?php echo $afecta; ?></td>
							</tr>
							<?php }; ?>

						</table>
					</div>
				</td>
			</tr>
		</thead>
		<tr>
			<td>
				<div id="content">
					<div class="page" style="font-size: 11pt;">
						<table class="change_order_items">
							<thead>
								<tr style="font-size: 10pt;background:#FAFAFA;">
									<th>Total Compra con I.V.A.</th>
									<th>Total Compra Exenta</th>
									<th>Base Imponible</th>
									<th>Alicuota %</th>
									<th>Impuesto I.V.A.</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="text-align: right;font-size: 10pt" rowspan="3"><?php echo nformat($gtotal); ?></td>
									<td style="text-align: right;font-size: 10pt" rowspan="3"><?php echo nformat($exento); ?></td>
									<td style="text-align: right;font-size: 10pt"><?php echo nformat($reducida);  ?></td>
									<td style="text-align: right;font-size: 10pt"><?php echo nformat($tasaredu);  ?></td>
									<td style="text-align: right;font-size: 10pt"><?php echo nformat($reduimpu);  ?></td>
								</tr>
								<tr>
									<td style="text-align: right;font-size: 10pt"><?php echo nformat($general);   ?></td>
									<td style="text-align: right;font-size: 10pt"><?php echo nformat($tasa);      ?></td>
									<td style="text-align: right;font-size: 10pt"><?php echo nformat($geneimpu);  ?></td>
								</tr>
								<tr>
									<td style="text-align: right;font-size: 10pt"><?php echo nformat($adicional); ?></td>
									<td style="text-align: right;font-size: 10pt"><?php echo nformat($tasaadic);  ?></td>
									<td style="text-align: right;font-size: 10pt"><?php echo nformat($adicimpu);  ?></td>
								</tr>
								</tbody><tfoot>
								<tr>
									<td colspan='4' style="text-align:right;font-size:12pt">Total I.V.A. CAUSADO Bs:</td>
									<td style="text-align: right;font-size:12pt"><?php echo nformat($impuesto);  ?></td>
								</tr>
								<tr>
									<td colspan='4' style="text-align:right;font-size:12pt;font-weight:bold;" >Total I.V.A. RETENIDO Bs:</td>
									<td style="text-align: right;font-size:12pt;font-weight:bold;"><?php echo nformat($reiva);  ?></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</td>
		</tr>
</table>
Firma Y Sello:

<br />
<br />
<br />
<br />
<br />
<br />

<!--
<div style='position: absolute;left:<?php echo ceil(rand(20,180))?>px;bottom:210px;transform:rotate(-<?php echo ceil(rand(0,4))?>deg);'>
	<img src="<?php echo $this->_direccion.'/images/sello.jpg'; ?>"  width="260" alt="firma">
</div>
-->
<table align='center' style="width: 90%;" colspacing='4' colspan='4'>
		<tr>
			<td><hr></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td><hr></td>
		</tr>
		<tr>
			<td><b><div align="center" style="font-size: 9pt">Agente de Retenci&oacute;n</div></b></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td><b><div align="center" style="font-size: 9pt">Sujeto de Retenci&oacute;n</div></b></td>
		</tr>
</table>
<div style="font-size: 11pt;text-align:center">Fecha de Entrega ____/____/______</div>

</div>
</body>
</html>
