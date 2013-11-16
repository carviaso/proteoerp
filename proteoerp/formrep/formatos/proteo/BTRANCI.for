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
$this->db->where('a.tipo','TR');

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
			<td valign='bottom'><h1 style="text-align: left">TRANSFERENCIA DE EFECTIVO EN CAJAS No. <?php echo $numero; ?></h1></td>
			<td valign='bottom'><h1 style="text-align: right">Fecha: <?php echo $hfecha; ?></h1></td>
		</tr>
	</table>
</div>
<div style="width: 100%; text-align:center;">
	<table align='center' style="width: 90%; font-size: 8pt;">
		<tr>
			<td colspan="2" style="font-size:2em;text-align:center;"><b>Monto de la transferencia <?php echo nformat($monto); ?></b></td>
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
			<td><b>Caja que recibe:</b></td>
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
	<table width="100%">
		<tr>
			<td style="text-align:center; border-style:solid;" valign="bottom"><br><br><br><b>Recibido por:</b></td>
			<td style="text-align:center; border-style:solid;" valign="bottom"><b>C.I.:        </b></td>
			<td style="text-align:center; border-style:solid;" valign="bottom"><b>Fecha:       </b></td>
		</tr>
		<tr style="height:80px">
			<td style="text-align:center; border-style:solid;" valign="bottom"><br><br><br><b>Elaborado por: </b></td>
			<td style="text-align:center; border-style:solid;" valign="bottom"><b>Autor&iacute;a:</b></td>
			<td style="text-align:center; border-style:solid;" valign="bottom"><b>Autorizado por:</b></td>
		</tr>
	</table>
</div>
</body>
</html>
