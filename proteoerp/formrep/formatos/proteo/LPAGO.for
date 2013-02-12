<?php
$maxlin=16; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL_1 = $this->db->query('SELECT a.numche,
a.numero, b.nombre,TRIM(b.nomfis) AS nomfis, a.proveed, b.rif,a.montopago,a.fecha,a.tipo
FROM lpago AS a
JOIN sprv  AS b ON a.proveed=b.proveed
WHERE a.id='.$dbid);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$numero   = $row->numero;
$proveed  = $row->proveed;
$nombre   = (empty($row->nomfis))? htmlspecialchars(trim($row->nombre)) : htmlspecialchars($row->nomfis);
$rifci    = trim($row->rif);
$monto    = nformat($row->montopago);
$montole  = strtoupper(numletra($row->montopago));
$fecha    = dbdate_to_human($row->fecha);
$tipo     = $row->tipo;

if(preg_match("/[0-9]+/i",$row->numche)){
	$numche = $row->numche;
}else{
	$numche = '';
}
$dbproveed= $this->db->escape($proveed);

$totcosto= 0;
$lineas  = 0;
$uline   = array();

if($tipo=='T' || $tipo=='A'){
	$tit = 'POR TRANSPORTE';
	$mSQL1="SELECT a.fecha, DATE_FORMAT(a.fecha,'%w') AS sem, SUM(a.lista) AS litros, b.tarifa, SUM(ROUND(a.lista*b.tarifa,2)) AS totmon
	FROM lrece AS a
	JOIN lruta AS b ON a.ruta=b.codigo
	JOIN sprv  AS c ON b.codprv=c.proveed
	WHERE a.pago=${dbid}
	GROUP BY a.fecha";
	$mSQL = $mSQL1;
}

if($tipo=='P' || $tipo=='A'){
	$tit = 'POR PRODUCTOR';
	$mSQL2="SELECT b.fecha, DATE_FORMAT(b.fecha,'%w') AS sem, SUM(a.lista) AS litros, '0' AS tarifa,
		SUM(ROUND(a.lista*if(c.tipolec=\"F\",k.ultimo,e.ultimo),2)+ROUND(a.lista*(IF(c.zona='0112',l.ultimo,f.ultimo)+g.ultimo+h.ultimo)*(c.tipolec=\"F\")+ROUND(a.lista*IF(c.animal=\"B\",if(c.tipolec=\"F\",i.ultimo,j.ultimo), 0 ),2),2))  AS totmon
	FROM itlrece AS a
	JOIN lrece AS b ON  a.id_lrece=b.id
	JOIN lvaca AS c ON a.id_lvaca=c.id
	JOIN sprv  AS d ON c.codprv=d.proveed
	LEFT JOIN sinv  AS e ON e.codigo='ZLCALIENTE'
	LEFT JOIN sinv  AS f ON f.codigo='ZMANFRIO'
	LEFT JOIN sinv  AS g ON g.codigo='ZPGRASA'
	LEFT JOIN sinv  AS h ON h.codigo='ZBACTE'
	LEFT JOIN sinv  AS i ON i.codigo='ZBUFALA'
	LEFT JOIN sinv  AS j ON j.codigo='ZBUFALAC'
	LEFT JOIN sinv  AS k ON k.codigo='ZLFRIA'
	LEFT JOIN sinv  AS l ON l.codigo='ZLMACHI'
	WHERE a.pago=${dbid} AND MID(b.ruta,1,1) <>'G' AND a.lista>0
	GROUP BY b.fecha";
	$mSQL = $mSQL2;
}

if($tipo=='A'){
	$tit = '';
	$mSQL = $mSQL1.' UNION ALL '.$mSQL2;
}

//////////////////////////////////////////////////////////////
$mSQL="
SELECT fecha, sem,
sum(llitros) llitros, round(sum(totleche)/sum(llitros),4) ltarifa, sum(totleche) totleche,
sum(tlitros) tlitros, round(sum(totransp)/sum(tlitros),4) ttarifa, sum(totransp) tottransp,
sum(totleche)+sum(totransp) total
FROM (
	SELECT
		a.fecha, DATE_FORMAT(a.fecha,'%w') AS sem,
		SUM(a.lista) AS tlitros,
		b.tarifa,
		SUM(ROUND(a.lista*b.tarifa,2)) AS totransp,
		0 AS llitros,
		0 totleche
	FROM lrece AS a
		JOIN lruta AS b ON a.ruta=b.codigo
		JOIN sprv  AS c ON b.codprv=c.proveed
	WHERE a.pago=${dbid}
	GROUP BY a.fecha
UNION ALL
	SELECT
		b.fecha, DATE_FORMAT(b.fecha,'%w') AS sem,
		0 AS litros,
		0 AS tarifa,
		0 totrasp,
		SUM(a.lista) llitros,
		SUM(ROUND(a.lista*if(c.tipolec='F', if(c.animal='V', e.tarifa1, e.tarifa3), if(c.animal='V', e.tarifa2, e.tarifa4) ),2)) AS totleche
	FROM itlrece  AS a
		JOIN lrece    AS b ON a.id_lrece=b.id
		JOIN lvaca    AS c ON a.id_lvaca=c.id
		JOIN sprv     AS d ON c.codprv=d.proveed
		JOIN lprecio  AS e ON e.tarifa1=e.tarifa1
	WHERE a.pago=${dbid} AND MID(b.ruta,1,1) <>'G' AND a.lista>0
	GROUP BY b.fecha
) fff
GROUP BY fecha";
///////////////////////////////////////////////////////////////////////

$mSQL_2  = $this->db->query($mSQL);

if ($mSQL_2->num_rows() == 0)
{
	echo "Consulta Vacia!!";
}
$detalle = $mSQL_2->result();

$mSQL   = "SELECT a.* FROM lgasto AS a WHERE a.pago=${dbid}";
$mSQL_3 = $this->db->query($mSQL);
$detalle2  = $mSQL_3->result();

$ngasto =$mSQL_3->num_rows();


if($ngasto>0){
	$det3encab = 5; //Tamanio del encadezado de la segunda tabla
	$nlgasto=$ngasto+$det3encab;
}else{
	$det3encab = 0;
	$nlgasto   = 0;
}

$semana=array('DOMINGO','LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO');

$ittot = array('lmonto'=>0,'llitros'=>0,'tlitros'=>0,'tmonto'=>0, 'total'=>0 );

?><html>
<head>
<title>Pago a proveedor <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" />
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


	/*if (isset($pdf)) {
		$texto = array();
		$font  = Font_Metrics::get_font("verdana");
		$size  = 6;
		$color = array(0,0,0);
		$text_height = Font_Metrics::get_font_height($font, $size);
		$w     = $pdf->get_width();
		$h     = $pdf->get_height();
		$y     = $h - $text_height - 24;

		$text = "PP {PAGE_NUM} de {PAGE_COUNT}";

		// Center the text
		$width = Font_Metrics::get_text_width('PP 1 de 2', $font, $size);
		$pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);
	}*/
</script>
<table width='100%'>
<tr><td>
<?php
//************************
//     Encabezado
//************************
$encabezado = <<<encabezado
	<table style="width:100%;" border='1'>
		<tr><td>
			<table style="width:100%;border: 1px solid black;" class="header">
				<tr>
					<td><span style="text-align: left">RECIBO DE PAGO Nro. <b>${numero}</b></span></td>
					<td><span style="text-align: right">Fecha: <b>${fecha}</b></span></td>
					<td style="text-align: center">Por Bs.: <b>*${monto}*</b></td>
				</tr>
			</table>
		</td></tr>
		<tr><td>
			<table style="font-size:12pt;">
				<tr>
					<td>Pagado a:</td>
					<td><b>${nombre} </b> </td>
					<td>RIF o CI: <b>${rifci}</b> (${proveed})</td>
				</tr>
			</table>
		</td></tr>
	</table>
encabezado;


//								<td><b>La cantidad de:</b></td>
//								<td>${montole} Bs.</td>


// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla="
			<table class=\"change_order_items\" style=\"padding-top:0; \">
				<thead>
					<tr>
						<th ${estilo}' >D&iacute;a   </th>
						<th ${estilo}' >Leche</th>
						<!-- th ${estilo}' >Precio       </th -->
						<th ${estilo}' >Monto Leche  </th>
						<th ${estilo}' >Trans.   </th>
						<!-- th ${estilo}' >Tarifa       </th -->
						<!-- th ${estilo}' >Monto Trans  </th -->
						<th ${estilo}' >Total Pagar  </th>
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
						<td>Totales...</td>
						<td style="text-align:right;font-size:10pt;font-weight:bold;">%s</td>
						<!-- td style="text-align:right;font-size:10pt;font-weight:bold;"></td -->
						<td style="text-align:right;font-size:10pt;font-weight:bold;">%s</td>
						<td style="text-align:right;font-size:10pt;font-weight:bold;">%s</td>
						<!-- td style="text-align:right;font-size:10pt;font-weight:bold;"></td -->
						<!-- td style="text-align:right;font-size:10pt;font-weight:bold;">%s</td -->
						<td style="text-align:right;font-size:10pt;font-weight:bold;">%s</td>
					</tr>
				</tfoot>
			</table>

piefinal;

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5" style="text-align: right;">CONTINUA...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

$mod     = $clinea = false;
$npagina = true;
$i       = 0;

$this->incluir('X_CINTILLO');
echo "</td></tr>\n";
echo "<tr><td>\n";
echo $encabezado;
echo "\n</td></tr>\n";
echo "<tr><td>\n";
echo "	<table width='100%'>\n";
echo "		<tr><td>\n";
echo $encabezado_tabla;
$npagina=false;

foreach ( $detalle AS $items ){
?>
					<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
						<td style="text-align:left;font-size: 0.7em"><?php echo dbdate_to_human($items->fecha).' '.$semana[$items->sem]; ?></td>
						<td style="text-align:right" ><?php $ittot['llitros'] += $items->llitros; echo nformat($items->llitros ,0); ?></td>
						<!-- td style="text-align: right" ><?php echo nformat($items->ltarifa,4); ?></td -->
						<td style="text-align:right" ><?php $ittot['lmonto'] += $items->totleche; echo nformat($items->totleche ,2); ?></td>
						<td style="text-align:right" ><?php $ittot['tlitros'] += $items->tlitros; echo nformat($items->tlitros ,0); ?></td>
						<!-- td style="text-align:right" ><?php echo nformat($items->ttarifa,4); ?></td -->
						<!-- td style="text-align:right" ><?php $ittot['tmonto'] += $items->tottransp; echo nformat($items->tottransp ,2); ?></td -->
						<td style="text-align:right" ><?php $ittot['total'] += $items->total; echo nformat($items->total ,2); ?></td>
					</tr>
<?php
		$mod = ! $mod;
}

echo sprintf($pie_final,nformat($ittot['llitros'],0),nformat($ittot['lmonto']),nformat($ittot['tlitros'],0),nformat($ittot['tmonto']), nformat($ittot['total']));

if(!empty($numche)){
	echo 'CHEQUE Nro. '.$numche;
}


echo "		</td>\n";
echo "		<td>\n";



$lineas+=$det3encab;
//******************************
//Detalle de las deducciones
//******************************
$ittot = array('tlgasto'=>0);
if($ngasto>0){
//**********************************
// Encabezado Tabla Deducciones
//**********************************
$encabezado_tabla="
	<table class='change_order_items' style='padding-top:0;'>
		<thead>
			<tr><td colspan='4'>DEDUCCIONES Y/O ADICCONES</td>
			</tr>
			<tr>
				<th ${estilo}' >Descripci&oacute;n</th>
				<!-- th ${estilo}' >Fecha</th -->
				<th ${estilo}' >Cant.</th>
				<th ${estilo}' >Precio</th>
				<th ${estilo}' >Total</th>
			</tr>
		</thead>
		<tbody>
";

//Fin Encabezado Tabla

//************************
// Pie Pagina Deducciones
//************************
$pie_final='
		</tbody>
		<tfoot style=\'border:1px solid;background:#EEEEEE;\'>
			<tr>
				<td colspan="3" style="text-align: right;">Total...</td>
				<td style="text-align:right;font-size:10pt;font-weight:bold;">%s</td>
			</tr>
		</tfoot>
	</table>
';

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

echo $encabezado_tabla;
foreach ($detalle2 AS $items2){ $i++;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}

if($items2->tipo=='D'){
	$items2->precio =(-1)*$items2->precio;
	$items2->total  =(-1)*$items2->total;
}
?>
					<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
						<td style="text-align: left;font-size: 0.7em"  ><?php echo $items2->descrip;               ?></td>
						<!-- td style="text-align: center"><?php echo dbdate_to_human($items2->fecha);?></td -->
						<td style="text-align: right" ><?php echo nformat($items2->cantidad,2);   ?></td>
						<td style="text-align: right" ><?php echo nformat($items2->precio  ,2);   ?></td>
						<td style="text-align: right" ><?php $ittot['tlgasto']+=$items2->total ; echo nformat($items2->total   ,2);   ?></td>
					</tr>
<?php

		//if($npagina){
		//	echo $pie_continuo;
		//}else{
			$mod = ! $mod;
		//}
	} while ($clinea);
}

echo sprintf($pie_final,nformat($ittot['tlgasto']));
}

echo "\n		</td></tr>\n";
echo "	</table>\n";
echo "\n</td></tr>\n";
echo "<tr><td>\n";

/*
<table  style="width: 100%%; height : 50px;">
	<tr>
		<td style="font-size: 8pt; text-align:center;" valign="bottom"><b>Recibido por:</b></td>
		<td style="font-size: 8pt; text-align:center;" valign="bottom"><b>CI:</b></td>
		<td style="font-size: 8pt; text-align:center;" valign="bottom"><b>Fecha: ____/____/______</b></td>
	</tr>
</table>'
*/
?>

</td></tr>
</table>
</body>
</html>
