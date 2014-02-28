<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
$maxlin=33; //Maximo de lineas de items.

if(count($parametros) < 0) show_error('Faltan parametros');
$id=$parametros[0];

$sel=array(
	'a.fecha','a.codbanc','a.numcuent',
	'a.banco','a.saldoi','a.saldof',
	'a.deposito','a.credito','a.cheque','a.estampa',
	'a.debito','a.cdeposito','a.status','a.numcuent'
);
$this->db->select($sel);
$this->db->from('bconci AS a');
$this->db->where('a.id', $id);

$mSQL_1 = $this->db->get();
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');

$this->load->helper('fecha');

$row = $mSQL_1->row();
$numero  = $id;
$codbanc = $row->codbanc;
$banco   = $this->us_ascii2html(trim($row->banco));
$fecha   = $row->fecha;
$hfecha  = dbdate_to_human($row->fecha);
$status  = $row->status;
$saldoi  = htmlnformat($row->saldoi);
$saldof  = htmlnformat($row->saldof);
$numcuent= $row->numcuent;
$estampa = dbdate_to_human($row->estampa);
$conciliado=htmlnformat($row->deposito-$row->cheque+$row->credito-$row->debito);
if($status='C'){
	$tstatus = 'PROCESADO';
}else{
	$tstatus = 'EN PROCESO';
}

$anio     = substr($fecha,0,4);
$dbfecha  = $this->db->escape($fecha);
$dbbanco  = $this->db->escape($row->codbanc);
$bsal     = floatval($this->datasis->dameval("SELECT saldo FROM bsal WHERE ano=${anio} AND codbanc=${dbbanco}"));
$mSALDOANT= floatval($this->datasis->dameval("SELECT SUM(IF(tipo_op IN ('CH', 'ND'),-1,1)*monto) AS saldo FROM bmov WHERE anulado='N' AND fecha<=$dbfecha  AND EXTRACT(YEAR_MONTH FROM fecha)>=".$anio."01 AND codbanc = ${dbbanco}"));
$ssmonto  = $bsal+$mSALDOANT;

$diff    = htmlnformat($row->saldof-$ssmonto);
$mlibro  = htmlnformat($ssmonto);
$anio    = substr($fecha,0,4);
$mes     = strtoupper(mesLetra(substr($fecha,5,2)));

$sel=array('a.numero','a.fecha','a.concepto','a.tipo_op',"IF(a.tipo_op IN ('CH','ND'),-1,1)*a.monto AS monto");
$this->db->select($sel);

$this->db->from('bmov AS a');
$this->db->where('a.fecha <=' ,$fecha);
$this->db->where('a.codbanc'  ,$codbanc);
$this->db->where('a.liable'   ,'S');
$this->db->where('a.anulado'  ,'N');
$this->db->where("(a.concilia='0000-00-00' OR a.concilia IS NULL)");
$this->db->orderby('a.tipo_op,a.fecha');
$mSQL_2 = $this->db->get();
$detalle = $mSQL_2->result();

$ittot['monto']=$lineas=0;
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Conciliacion Bancaria <?php echo $id ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">
<script type="text/php">
	if (isset($pdf)){
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
		$texto[]='Elaborado por';
		$texto[]='Auditoria';
		$texto[]='Autorizado por:';

		$cuadros = 0;   //Cantidad de cuadros (en caso de ser 0 calcula la cantidad)
		$margenh = 40;  //Distancia desde el borde derecho e izquierdo
		$margenv = 80;  //Distancia desde el borde inferior
		$alto    = 50;  //Altura de los cuadros
		$size    = 9;   //Tamanio del texto en los cuadros
		$color   = array(0,0,0); //Color del marco
		$lcolor  = array(0,0,0); //Color de la letra
		////**************************************************

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
$encabezado = "
	<table style='width:100%;font-size: 12pt;' class='header' cellpadding='0' cellspacing='0'>
		<tr>
			<td colspan='2' valign='bottom'><h1 style='text-align:left; border-bottom:1px solid;font-size:12pt;'>CONCILIACI&Oacute;N BANCARIA ${mes} $anio</h1></td>
			<td valign='bottom' style='text-align:right;'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>REALIZADO: ${estampa}</h1></td>
		</tr><tr>
			<td rowspan='3'>
				<p style='text-align:center;'>
				<span style='font-size: 0.6em;'>Banco</span><br><b>${banco}</b><br>
				<span style='font-size: 0.6em;'>Nro. Cuenta</span><br><b>${numcuent}</b><br>
				<span style='font-size: 1.3em;'>*${tstatus}*</span>
				</p>
			</td>
			<td><b>Saldo seg&uacute;n estrato bancario</b> </td>
			<td style='text-align:right;font-size:1.5em' ><b>${saldof}</b></td>
		</tr><tr>
			<td><b>Saldo seg&uacute;n libro</b> </td>
			<td style='text-align:right;font-size:1.5em;border-bottom-style: solid;' ><b>${mlibro}</b></td>
		</tr><tr>
			<td><b>Diferencia</b> </td>
			<td style='text-align:right;font-size:1.5em;' ><b>${diff}</b></td>
		</tr>
	</table>
";
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla="
	<h1>Detalles de movimientos por conciliar</h1>
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr>
				<th ${estilo}' >Fecha</th>
				<th ${estilo}' >N&uacute;mero</th>
				<th ${estilo}' >Concepto</th>
				<th ${estilo}' >Monto</th>
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
				<td style="text-align: right;font-size:1.5em;" colspan='3'><b>TOTAL GLOBAL DE MOVIMIENTOS EN TRANSITO</b></td>
				<td style="text-align: right;font-size:1.5em;font-weight:bold;" > %s </td>
			</tr>
		</tfoot>

	</table>
piefinal;

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4" style="text-align: right;">CONTINUA...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

$mod     = $clinea = false;

$titus=array(
	'DE'=>'Dep&oacute;sitos',
	'CH'=>'Cheques',
	'NC'=>'Notas de Cr&eacute;dito',
	'ND'=>'Notas de d&eacute;bito',
);
$this->incluir('X_CINTILLO');
echo $encabezado;
echo $encabezado_tabla;
$npagina = false;
$i       = 0;
$tipo_op = '';
$g_tota  = 0;
foreach ($detalle AS $items2){ $i++;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
	if($items2->tipo_op!=$tipo_op){
		$tipo_op=$items2->tipo_op;
		if($g_tota != 0){ $lineas++; ?>
	<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
		<td colspan='3' align='right'><b style='text-size:1.3em;'>Total parcial:</b></td>
		<td  align='right'><?php echo htmlnformat($g_tota); ?></td>
	</tr>
	<?php
			$g_tota  = 0;
		}
	?>
	<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
		<td colspan='4'><b style='text-size:1.3em;'><?php echo (isset($titus[$items2->tipo_op]))? $titus[$items2->tipo_op] : 'Documentos'; ?></b></td>
	</tr>

	<?php $lineas++; } ?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">

				<td style="text-align: left"  ><?php echo dbdate_to_human($items2->fecha);  ?></td>
				<td style="text-align: left"  ><?php echo $items2->tipo_op.$items2->numero; ?></td>
				<td style="text-align: left"  ><?php echo $items2->concepto; ?></td>
				<td style="text-align: right" ><?php echo htmlnformat($items2->monto); ?></td>
				<?php
				$g_tota += $items2->monto;
				$ittot['monto'] += $items2->monto;
				$lineas++;
				if($lineas > $maxlin){
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
$lineas++;

if($g_tota!=0){
?>
<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
	<td colspan='3' align='right'><b style='text-size:1.3em;'>Total parcial:</b></td>
	<td  align='right'><?php echo htmlnformat($g_tota); ?></td>
</tr>
<?php
}
for(1;$lineas<$maxlin;$lineas++){ ?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
<?php
	$mod = ! $mod;
}
echo sprintf($pie_final,htmlnformat($ittot['monto']));
?></body>
</html>
