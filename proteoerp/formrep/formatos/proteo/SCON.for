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

$mSQL = "SELECT a.numero,a.clipro,
	COALESCE(c.nombre,b.nombre) AS nombre,
	COALESCE(c.nomfis,b.nomfis) AS nomfis,COALESCE(c.rifci,b.rif) AS rifci,
	CONCAT_WS('',TRIM(c.dire11),c.dire12,TRIM(b.direc1), b.direc2) AS direccion,a.fecha,
	a.impuesto AS iva,a.stotal AS totals,a.gtotal AS totalg, COALESCE(c.telefono,b.telefono) AS telefono,
	a.peso, CONCAT_WS('',a.observ1,a.observ2) AS observa,a.status,a.tipod,a.tipo
FROM scon AS a
LEFT JOIN scli AS c ON a.clipro=c.cliente AND a.tipo='C'
LEFT JOIN sprv AS b ON a.clipro=b.proveed AND a.tipo='P'
WHERE a.id=${dbid}";

$mSQL_1 = $this->db->query($mSQL);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    = dbdate_to_human($row->fecha);
$numero   = $row->numero;
$clipro   = htmlspecialchars($row->clipro);
$rifci    = $this->us_ascii2html($row->rifci);
$nombre   = $this->us_ascii2html($row->nombre);
$stotal   = nformat($row->totals);
$gtotal   = nformat($row->totalg);
$iva      = nformat($row->iva);
$observa  = $this->us_ascii2html($row->observa);
$status   = trim($row->status);
$tipo     = trim($row->tipo);
$tipod    = trim($row->tipod);

$peso     = nformat($row->peso);
$impuesto = nformat($row->iva);
$direc    = $this->us_ascii2html($row->direccion);
$telefono = $this->us_ascii2html($row->telefono);
$ttitu    = ($tipo=='C') ? 'Cliente':'Proveedor';
$ttitu2   = ($tipod=='R') ? 'RECIBIDA':'ENVIADA';
$dbnumero   = $this->db->escape($numero);

$lineas = 0;
$uline  = array();

$mSQL="SELECT a.codigo,b.descrip AS desca,a.cana,a.precio AS preca,a.importe,a.iva
FROM itscon AS a
JOIN sinv AS b ON a.codigo=b.codigo
WHERE a.numero=${dbnumero}";

$mSQL_2 = $this->db->query($mSQL);
$detalle  = $mSQL_2->result();
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>NOTA DE CONSIGNACI&Oacute;N <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">
<?php
//************************
//     Encabezado
//
//************************
$encabezado = "
	<table style='width:100%;font-size: 9pt;' class='header' cellpadding='0' cellspacing='0'>
		<tr>
			<td valign='bottom'><h1 style='text-align:left;border-bottom:1px solid;font-size:12pt;'>CONSIGNACI&Oacute;N ${ttitu2} DE ".strtoupper($ttitu)." Nro. ${numero}</h1></td>
			<td valign='bottom'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>FECHA: ${fecha}</h1></td>
		</tr><tr>
			<td>${ttitu}: <b>(${clipro}) ${nombre}</b></td>
			<td>RIF/CI: <b>${rifci}</b></td>
		</tr><tr>
			<td>Direcci&oacute;n: <b>${direc}</b></td>
			<td>Tel&eacute;fono:  <b>${telefono}</b></td>
		</tr><tr>
			<td colspan='2'>Observaci&oacute;n: <b>${observa}</bb></td>
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
				<th ${estilo}' >C&oacute;digo</th>
				<th ${estilo}' >Descripci&oacute;n</th>
				<th ${estilo}' >Cant.</th>
				<th ${estilo}' >Precio U.</th>
				<th ${estilo}' >Monto</th>
				<th ${estilo}' >IVA%</th>
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
				<td  style="text-align: right;"></td>
				<td colspan="2" style="text-align: right;"><b>Sub-Total:</b></td>
				<td colspan="3" style="text-align: right;font-size:16px;font-weight:bold;" >${stotal}</td>
			</tr>
			<tr>
				<td style="text-align: right;"></td>
				<td colspan="2" style="text-align: right;"><b>Impuesto</b></td>
				<td colspan="3" style="text-align: right;font-size:16px;font-weight:bold;">${iva}</td>
			</tr>
			<tr style='border-top: 1px solid;background:#AAAAAA;'>
				<td style="text-align: right;"></td>
				<td colspan="2" style="text-align: right;"><b>MONTO TOTAL:</b></td>
				<td colspan="3" style="text-align: right;font-size:2em;font-weight:bold;">${gtotal}</td>
			</tr>
		</tfoot>

	</table>
piefinal;


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
			$this->incluir('X_CINTILLO');
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
						$descrip = trim($items->desca);
						$descrip = str_replace("\r",'',$descrip);
						$descrip = str_replace(array("\t"),' ',$descrip);
						$descrip = wordwrap($descrip,40,"\n");
						$arr_des = explode("\n",$descrip);
					}

					while(count($arr_des)>0){
						$uline   = array_shift($arr_des);
						echo $this->us_ascii2html($uline).'<br />';
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
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
<?php
	$mod = ! $mod;
}
echo $pie_final;
?>
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
		$texto[]="APROBADO:";
		$texto[]="RECIBIDO POR:";

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
</body>
</html>
