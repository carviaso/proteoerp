<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
$maxlin=39; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL = "
SELECT If(a.referen='E','Efectivo',IF( a.referen='C','Cr&eacute;dito',IF(a.referen='M','Mixto','Pendiente'))) AS referen,a.nfiscal,
	a.tipo_doc,a.numero,a.cod_cli,TRIM(c.nomfis) AS nomfis,c.nombre,c.rifci,CONCAT_WS('',TRIM(c.dire11),c.dire12) AS direccion,a.factura,a.fecha,a.vence,a.vd,
	a.iva,a.totals,a.totalg, a.exento,a.tasa, a.montasa, a.reducida, a.monredu, a.sobretasa,a.monadic, b.nombre AS nomvend,
	a.peso,c.telefono, a.observa,a.observ1
FROM sfac AS a
JOIN scli AS c ON a.cod_cli=c.cliente
LEFT JOIN vend b ON a.vd=b.vendedor
WHERE a.id=${dbid}";

$mSQL_1 = $this->db->query($mSQL);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    = dbdate_to_human($row->fecha);
$vence    = dbdate_to_human($row->vence);
$numero   = $row->numero;
$cod_cli  = $this->us_ascii2html($row->cod_cli);
$rifci    = htmlspecialchars(trim($row->rifci));
$nombre   = (empty($row->nomfis))? $this->us_ascii2html($row->nombre) : $this->us_ascii2html($row->nomfis);
$stotal   = nformat($row->totals);
$gtotal   = nformat($row->totalg);
$exento   = nformat($row->exento);
$observa  = $this->us_ascii2html(trim($row->observa).trim($row->observ1));

$tasa      = nformat($row->tasa);
$montasa   = nformat($row->montasa);
$reducida  = nformat($row->reducida);
$monredu   = nformat($row->monredu);
$sobretasa = nformat($row->sobretasa);
$monadic   = nformat($row->monadic);

$peso     = nformat($row->peso);
$impuesto = nformat($row->iva);
$direc    = $this->us_ascii2html($row->direccion);
$tipo_doc = trim($row->tipo_doc);
$referen  = trim($row->referen);
$nfiscal  = htmlspecialchars(trim($row->nfiscal));
$telefono = htmlspecialchars(trim($row->telefono));
$nomvend  = $this->us_ascii2html($row->nomvend);
$factura  = ($tipo_doc=='D')? $row->factura :'';

$dbtipo_doc = $this->db->escape($tipo_doc);
$dbnumero   = $this->db->escape($numero);

if($numero[0]=='_')
	$documento = "PRE-FACTURA";
elseif($tipo_doc == "F")
	$documento = "FACTURA";
elseif($tipo_doc == "D")
	$documento = "NOTA DE CREDITO";
elseif($tipo_doc == "X")
	$documento = "FACTURA ANULADA";
else
	$documento = "DOCUMENTO";

$ivaaplica = $this->datasis->ivaplica($row->fecha);
$lineas = 0;
$uline  = array();

$mSQL="SELECT codigoa AS codigo,desca,cana,preca,tota AS importe,iva,detalle
FROM sitems
WHERE numa=${dbnumero} AND tipoa=${dbtipo_doc}";

$mSQL_2 = $this->db->query($mSQL);
$detalle  = $mSQL_2->result();
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title><?php echo $documento.' '.$numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">

<script type="text/php">
	if (isset($pdf)) {
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
		$pdf->add_object($foot, 'all');

		$text = "PP {PAGE_NUM} de {PAGE_COUNT}";

		// Center the text
		$width = Font_Metrics::get_text_width('PP 1 de 2', $font, $size);
		$pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);

	}
</script>

<?php
//************************
//     Encabezado
//
//************************
$encabezado = "
	<p style='height: 50px;'> </p>
	<table style='width:100%;font-size: 9pt;' class='header' cellpadding='0' cellspacing='0'>
		<tr>
			<td valign='bottom'><h1 style='text-align:left;border-bottom:1px solid;font-size:12pt;'>${documento} Nro. ${numero}</h1></td>
			<td valign='bottom'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>Fecha de Emisi&oacute;n: ${fecha}</h1></td>
		</tr><tr>
			<td>RIF, CI o Pasaporte: <b>${rifci}</b></td>
			<td>Fecha de Vencimiento: <b>${vence}</b></td>
		</tr><tr>
			<td>Raz&oacute;n Social: <b>${nombre}</b></td>
			<td>C&oacute;digo de Cliente: <b>${cod_cli}</b></td>
		</tr><tr>
			<td>Domicilio Fiscal: <b>${direc}</b></td>";
if ( empty($factura) )
	$encabezado .= "			<td>&nbsp;</b></td>";
else
	$encabezado .= "			<td>Documento Afectado:  <b>${factura} </b></td>";

$encabezado .= "
		</tr><tr>
			<td>Tel&eacute;fono:  <b>${telefono}</b></td>
			<td>Condici&oacute;n: <b>${referen}</b></td>
		</tr>
	</table>
";
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla="
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr>
				<th ${estilo}width:130px;' >C&oacute;digo</th>
				<th ${estilo}' >Descripci&oacute;n de la Venta del Bien o Servicio</th>
				<th ${estilo}width:50px;' >Cant.</th>
				<th ${estilo}width:80px;' >Precio U.</th>
				<th ${estilo}width:90px;' >Monto</th>
				<th ${estilo}width:35px;' >IVA%</th>
			</tr>
		</thead>
		<tbody>
";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final="
		</tbody>
		<tfoot style='border:1px solid;background:#EEEEEE;'>";
if($row->exento>0){
	$pie_final.="<tr>
				<td style='text-align: right;'></td>
				<td colspan='2' style='text-align: right;'><b>Monto Total Exento o Exonerado del IVA:</b></td>
				<td colspan='3' style='text-align: right;font-size:14px;font-weight:bold;'>${exento}</td>
			</tr>";
}

if($row->monredu>0){
	$alicuota=nformat($ivaaplica['redutasa']);
	$pie_final.="<tr>
				<td  style='text-align: right;'></td>
				<td colspan='2' style='text-align: right;'><b>Monto Total de la Base Imponible seg&uacute;n Alicuota ${alicuota}%:</b></td>
				<td colspan='3' style='text-align: right;font-size:16px;font-weight:bold;' >${monredu}</td>
			</tr>
			<tr>
				<td style='text-align: right;'></td>
				<td colspan='2' style='text-align: right;'><b>Monto Total del Impuesto seg&uacute;n Alicuota ${alicuota}%:</b></td>
				<td colspan='3' style='text-align: right;font-size:16px;font-weight:bold;'>${reducida}</td>
			</tr>";
}

$alicuota=nformat($ivaaplica['tasa']);
$pie_final.="<tr>
			<td  style='text-align: right;'></td>
			<td colspan='2' style='text-align: right;'><b>Monto Total de la Base Imponible seg&uacute;n Alicuota ${alicuota}%:</b></td>
			<td colspan='3' style='text-align: right;font-size:16px;font-weight:bold;' >${montasa}</td>
		</tr>
		<tr>
			<td style='text-align: right;'></td>
			<td colspan='2' style='text-align: right;'><b>Monto Total del Impuesto seg&uacute;n Alicuota ${alicuota}%:</b></td>
			<td colspan='3' style='text-align: right;font-size:16px;font-weight:bold;'>${tasa}</td>
		</tr>";

if($row->monadic>0){
	$alicuota=nformat($ivaaplica['sobretasa']);
	$pie_final.="<tr>
				<td  style='text-align: right;'></td>
				<td colspan='2' style='text-align: right;'><b>Monto Total de la Base Imponible seg&uacute;n Alicuota ${alicuota}%:</b></td>
				<td colspan='3' style='text-align: right;font-size:16px;font-weight:bold;' >${monadic}</td>
			</tr>
			<tr>
				<td style='text-align: right;'></td>
				<td colspan='2' style='text-align: right;'><b>Monto Total del Impuesto seg&uacute;n Alicuota ${alicuota}%:</b></td>
				<td colspan='3' style='text-align: right;font-size:16px;font-weight:bold;'>${sobretasa}</td>
			</tr>";
}

$pie_final.=	"<tr style='border-top: 1px solid;background:#AAAAAA;'>
				<td style='text-align: right;'></td>
				<td colspan='2' style='text-align: right;'><b>VALOR TOTAL DE LA VENTA O SERVICIO:</b></td>
				<td colspan='3' style='text-align: right;font-size:20px;font-weight:bold;'>${gtotal}</td>
			</tr>
		</tfoot>
	</table>";



$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6" style="text-align: right;">CONTINUA...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

$mod     = $clinea = false;
$npagina = true;
$i       = 0;

foreach ($detalle AS $items){ $i++;
	do {
		if($npagina){
			//$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td style="text-align: center;"><?php echo trim($items->codigo); ?></td>
				<td>
					<?php
					if(!$clinea){
						$ddetall = $this->us_ascii2html(trim($items->detalle));
						$descrip = $this->us_ascii2html(trim($items->desca));
						if(strlen($ddetall) > 0 ) {
							if(strpos($ddetall,$descrip)!==false){
								$descrip = $ddetall;
							}else{
								$descrip .= "\n".$ddetall;
							}
						}
						$descrip = str_replace("\r",'',$descrip);
						$descrip = str_replace(array("\t"),' ',$descrip);
						$descrip = wordwrap($descrip,40,"\n");
						$arr_des = explode("\n",$descrip);
					}

					while(count($arr_des)>0){
						$uline   = array_shift($arr_des);
						echo $uline.'<br />';
						$lineas++;
						if($lineas >= $maxlin){
							$lineas =0;
							$npagina=true;
							if(count($arr_des)>0){
								$clinea = true;
							}else{
								$clinea = false;
							}
							break;
						}
					}
					if(count($arr_des)==0 && $clinea) $clinea=false;
					?>
				</td>
				<td style="text-align: right;"><?php echo ($clinea)? '': nformat($items->cana); ?></td>
				<td style="text-align: right;" ><?php echo ($clinea)? '': nformat($items->preca); ?></td>
				<td class="change_order_total_col"><?php echo ($clinea)? '':nformat($items->preca*$items->cana); ?></td>
				<td style="text-align: right;" ><?php echo ($clinea)? '': nformat($items->iva); ?></td>
			</tr>
<?php
		if($npagina){
			echo $pie_continuo;
		}else{
			$mod = ! $mod;
		}
	} while ($clinea);
}

for(1;$lineas<$maxlin;$lineas++){ ?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
<?php
	if(!empty($observa)){
		echo "<td colspan='6' style='text-align: center;'>${observa}</td>";
		$observa='';
	}else{
?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
<?php
	}
?>
			</tr>
<?php
	$mod = ! $mod;
}
echo $pie_final;
?>
</body>
</html>
