<?php
if(count($parametros) < 0) show_error('Faltan parametros');
$id=$parametros[0];

$sel=array('a.tipo','a.numero','a.fecha','a.tarjeta','a.tdebito','a.cheques','a.efectivo'
,'a.comision','a.islr','a.monto','a.envia','TRIM(a.bancoe) AS bancoe','a.tipoe','a.numeroe'
,'a.recibe','TRIM(a.bancor) AS bancor','a.tipor','a.numeror','CONCAT_WS(" ",TRIM(a.concepto),TRIM(a.concep2)) AS concepto'
,'TRIM(a.benefi) AS benefi','a.transac','b.numcuent AS bnumcuent','c.numcuent AS cnumcuent');
$this->db->select($sel);
$this->db->from('bcaj AS a');
$this->db->join('banc AS b','a.envia=b.codbanc' ,'left');
$this->db->join('banc AS c','a.recibe=c.codbanc','left');
$this->db->where('a.id'   , $id);
$this->db->where('a.tipo','DE');

$mSQL_1 = $this->db->get();

if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');

$row = $mSQL_1->row();

$tipo     = htmlspecialchars(trim($row->tipo));
$numero   = htmlspecialchars(trim($row->numero));
$hfecha    = dbdate_to_human($row->fecha);
$tarjeta  = $row->tarjeta;
$tdebito  = $row->tdebito;
$cheques  = $row->cheques;
$efectivo = $row->efectivo;
$comision = $row->comision;
$islr     = $row->islr;
$monto    = $row->monto;
$envia    = htmlspecialchars(trim($row->envia));
$bancoe   = htmlspecialchars(trim($row->bancoe));
$tipoe    = htmlspecialchars(trim($row->tipoe));
$numeroe  = htmlspecialchars(trim($row->numeroe));
$recibe   = $row->recibe;
$bancor   = htmlspecialchars(trim($row->bancor));
$tipor    = htmlspecialchars(trim($row->tipor));
$numeror  = htmlspecialchars(trim($row->numeror));
$concepto = htmlspecialchars(trim($row->concepto));
$benefi   = htmlspecialchars(trim($row->benefi));
$transac  = $row->transac;
$bnumcuent= htmlspecialchars(trim($row->bnumcuent));
$cnumcuent= htmlspecialchars(trim($row->cnumcuent));

?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Deposito <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">

<?php $this->incluir('X_CINTILLO'); ?>

<div style="width: 100%; text-align:center;">
	<table  class="header">
		<tr>
			<td valign='bottom'><h1 style="text-align: left">COMPROBANTE DE DEPOSITO No. <?php echo $numero; ?></h1></td>
			<td valign='bottom'><h1 style="text-align: right">Fecha: <?php echo $hfecha; ?></h1></td>
		</tr>
	</table>
</div>
<div style="width: 100%; text-align:center;">
	<table align='center' style="width: 90%; font-size: 8pt;">
		<tr>
			<td colspan="2"><h2>EFECTIVO CONSIGNADO A BANCOS:</h2></td>
		</tr>
		<?php if($efectivo>0){ ?>
		<tr>
			<td style="text-indent: 1.5em;"><b>Monto en Billetes y Monedas</b></td>
			<td style="text-align: right;" ><?php echo nformat($efectivo); ?></td>
		</tr>
		<?php } ?>
		<?php if($cheques>0){ ?>
		<tr>
			<td style="text-indent: 1.5em;"><b>Monto en Cheques</b></td>
			<td style="text-align: right;" ><?php echo nformat($cheques); ?></td>
		</tr>
		<?php } ?>
		<?php if($tarjeta>0){ ?>
		<tr>
			<td style="text-indent: 1.5em;"><b>Monto en Tarjeta de Cr&eacute;dito</b></td>
			<td style="text-align: right;" ><?php echo nformat($tarjeta); ?></td>
		</tr>
		<?php } ?>
		<?php if($tdebito>0){ ?>
		<tr>
			<td style="text-indent: 1.5em;"><b>Monto en Tarjeta de D&eacute;bito</b></td>
			<td style="text-align: right;border-bottom-style:dotted;" ><?php echo nformat($tdebito); ?></td>
		</tr>
		<?php } ?>
		<tr style="font-size:1.5em;border-bottom-style:solid;">
			<td style="text-indent: 1.5em;"><b>Monto total del dep&oacute;sito</b></td>
			<td style="text-align: right;" ><b><?php echo nformat($efectivo+$cheques+$tarjeta+$tdebito); ?></b></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<?php if($tdebito+$tarjeta>0){ ?>
		<tr>
			<td colspan="2"><h2>MENOS DEDUCCIONES:</h2></td>
		</tr>
		<tr>
			<td style="text-indent: 1.5em;"><b>Por comisi&oacute;n de tarjetas</b></td>
			<td style="text-align: right;" ><?php echo nformat($comision); ?></td>
		</tr>
		<tr>
			<td style="text-indent: 1.5em;"><b>Por retenci&oacute;n ISLR</b></td>
			<td style="text-align: right;border-bottom-style:dotted;" ><?php echo nformat($islr); ?></td>
		</tr>
		<tr style="font-size:1.5em;border-bottom-style:solid;">
			<td style="text-indent: 1.5em;"><b>Total deducciones</b></td>
			<td style="text-align: right;" ><b><?php echo nformat($comision+$islr); ?></b></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan="2" style="font-size:2em;text-align:center;"><b>Monto neto del deposito <?php echo nformat($monto); ?></b></td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
	</table>
</div>
<div style="width: 100%; text-align:center;">
	<table align='center' style="font-size: 8pt;">
		<tr>
			<td><b>Caja que env&iacute;a:</b></td>
			<td><?php echo "${envia} ${bancoe} C.C.No.: ${bnumcuent}"; ?></td>
		</tr>
		<tr>
			<td><b>Egreso n&uacute;mero:</b></td>
			<td><?php echo "${tipoe}: ${numeroe}"; ?></td>
		</tr>
		<tr>
			<td><b>Banco que recibe:</b></td>
			<td><?php echo "${recibe} ${bancor} C.C.No.: ${cnumcuent}"; ?></td>
		</tr>
		<tr>
			<td><b>Ingreso n&uacute;mero:</b></td>
			<td><?php echo "${tipor}: ${numeror}"; ?></td>
		</tr>
		<tr>
			<td><b>Por concepto de:</b></td>
			<td><?php echo $concepto; ?></td>
		</tr>
	</table>
</div>
<br>
<div>
	<table width="100%" style="border-collapse: collapse;" >
		<tr>
			<td style="text-align:center; border-style:solid; border-width:2px;font-size:8pt" valign="bottom"><br><br><br>Recibido por:</td>
			<td style="text-align:center; border-style:solid; border-width:2px;font-size:8pt" valign="bottom">C.I.:        </td>
			<td style="text-align:center; border-style:solid; border-width:2px;font-size:8pt" valign="bottom">Fecha:       </td>
		</tr>
		<tr>
			<td style="text-align:center; border-style:solid; border-width:2px;font-size:8pt" valign="bottom"><br><br><br>Elaborado por:</td>
			<td style="text-align:center; border-style:solid; border-width:2px;font-size:8pt" valign="bottom">Autor&iacute;a:</td>
			<td style="text-align:center; border-style:solid; border-width:2px;font-size:8pt" valign="bottom">Autorizado por:</td>
		</tr>
	</table>
</div>
</body>
</html>
