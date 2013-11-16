<?php
if(count($parametros) < 0) show_error('Faltan parametros');
$id=$parametros[0];

$sel=array('a.tipo_doc','a.numero','a.cod_prv','a.fecha','a.monto','a.abonos','a.banco'
,'b.nombre','TRIM(b.nomfis) AS nomfis','TRIM(b.direc1) AS direc','b.rif','a.tipo_op','a.numche'
,'CONCAT_WS(\' \',observa1,observa2) AS observa','b.rif AS rifci','a.transac');
$this->db->select($sel);
$this->db->from('sprm AS a');
$this->db->join('sprv AS b'  ,'a.cod_prv=b.proveed');
$this->db->where('a.id'   , $id);
$this->db->where('a.tipo_doc','AN');

$mSQL_1 = $this->db->get();

if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');

$row = $mSQL_1->row();
$tipo_doc = trim($row->tipo_doc);
$numero   = trim($row->numero);
$proveed  = htmlspecialchars(trim($row->cod_prv));
$tipo_doc = trim($row->tipo_doc);
$fecha    = $row->fecha;
$hfecha   = dbdate_to_human($row->fecha);
$monto    = nformat($row->monto);
$montole  = strtoupper(numletra($row->monto));
$abonos   = $row->abonos;
$nombre   = (empty($row->nomfis))? htmlspecialchars(trim($row->nombre)) : htmlspecialchars($row->nomfis);
$rifci    = htmlspecialchars(trim($row->rifci));
$direc    = htmlspecialchars(trim($row->direc));
$observa  = wordwrap(trim(str_replace(',',', ',htmlspecialchars($row->observa))), 100, '<br>');
$transac  = $row->transac;
$tipo_op  = trim($row->tipo_op);
$banco    = htmlspecialchars(trim($row->banco));
$numche   = trim($row->numche);

$sql  = 'SELECT tbanco,moneda,banco,numcuent FROM banc WHERE codbanc='.$this->db->escape($banco);
$tban = $this->datasis->damerow($sql);

if($tipo_op=='CH'){
	$tbanco = 'banco';
	$tpago  = $tban['banco'].' <b>Cuenta:</b> '.$tban['numcuent'].' <b>Cheque:</b> '.$numche;
	$titu   = 'ANTICIPO A PROVEEDOR EN CHEQUE';
}elseif($tipo_op=='ND' && $tban['tbanco']=='CAJ'){
	$tbanco = 'caja';
	$tpago  = $tban['banco'];
	$titu   = 'ANTICIPO A PROVEEDOR EN EFECTIVO';
}elseif($tipo_op=='ND' && $tban['tbanco']!='CAJ'){
	$tbanco = 'banco';
	$tpago  = $tban['banco'].' <b>Cuenta:</b> '.$tban['numcuent'].' <b>D&eacute;bito:</b> '.$numche;
	$titu   = 'ANTICIPO A PROVEEDOR POR TRANSFERENCIA BANCARIA';
}else{
	$tbanco = '';
	$tpago  = '';
	$titu   = 'ANTICIPO A PROVEEDOR ';
}

?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Anticipo a proveedor <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">
<!--@size_paper 215.9x139.7-->
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

		$texto[]="ELABORADO POR:";
		$texto[]="AUDITORIA:";
		$texto[]="AUTORIZADO POR:";
		$texto[]="APROBADO:";

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
					<td valign='bottom'><h1 style="text-align: left">${titu} No. ${numero}</h1></td>
					<td valign='bottom'><h1 style="text-align: right">Fecha: ${hfecha}</h1></td>
				</tr><tr>
					<td colspan='2'><h1 style="text-align: center">Por Bs.: ***${monto}***</h1></td>
				</tr>
			</table>
			<table align='center' style="font-size: 8pt;">
				<tr>
					<td><b>Pagado a:</b></td>
					<td>(${proveed}) ${nombre}</td>
				</tr>
				<tr>
					<td><b>Con RIF:</b></td>
					<td>${rifci}</td>
				</tr>
				<tr>
					<td><b>La cantidad de:</b></td>
					<td>${montole} Bs.</td>
				</tr>
				<tr>
					<td><b>Por ${tbanco}:</b></td>
					<td>${tpago}</td>
				</tr>
				<tr>
					<td><b>Por concepto de:</b></td>
					<td>${observa}</td>
				</tr>
			</table>
encabezado;
// Fin  Encabezado

$this->incluir('X_CINTILLO');
echo $encabezado;
?>
</body>
</html>
