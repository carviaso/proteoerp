<?php
$maxlin=16; //Maximo de lineas de items.

$montoregular = $this->datasis->traevalor('MONTOLECHEREG','Monto de la leche regulada');
if(empty($montoregular)) $montoregular=3;

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL_1 = $this->db->query('SELECT
a.numero, b.nombre,TRIM(b.nomfis) AS nomfis, a.proveed, b.rif,a.montopago,a.fecha,a.tipo,
a.numche,c.banco AS nomban,a.id_lpagolote
FROM lpago AS a
JOIN sprv  AS b ON a.proveed=b.proveed
JOIN banc  AS c ON a.banco=c.codbanc
WHERE a.id='.$dbid);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$idlote   = (empty($row->id_lpagolote))? 0:$row->id_lpagolote;
$numero   = $row->numero;
$proveed  = $row->proveed;
$nombre   = (empty($row->nomfis))? htmlspecialchars(trim($row->nombre)) : htmlspecialchars($row->nomfis);
$rifci    = trim($row->rif);
$monto    = nformat($row->montopago);
$montole  = strtoupper(numletra($row->montopago));
$fecha    = dbdate_to_human($row->fecha);
$tipo     = $row->tipo;
$numche   = trim($row->numche);
$nomban   = trim($row->nomban);

$tit = 'PAGADO POR: '.$nomban;
if(!empty($numche) && $idlote<=0 ){
	$tit.= ' CHEQUE NRO. '.$numche;
}

$dbproveed= $this->db->escape($proveed);

$totcosto= 0;
$lineas  = 0;
$uline   = array();

// Determina la Tarifa
$quepaga = $this->datasis->dameval("SELECT MIN(b.fecha) fecha FROM itlrece a JOIN lrece b ON a.id_lrece=b.id WHERE a.pago=${dbid} ") ;
$precios = $this->datasis->dameval("SELECT id FROM lprecio WHERE fecha<='".$quepaga."' ORDER BY fecha DESC LIMIT 1 ") ;
if(empty($precios)) $precios = 1;

$mSQL="
SELECT ff.fecha, ff.sem,
SUM(ff.faltante) AS faltante,
SUM(ff.llitros)  AS llitros, ROUND(SUM(ff.totleche)/SUM(ff.llitros),4) AS ltarifa, SUM(ff.totleche) AS totleche,
SUM(ff.tlitros)  AS tlitros, ROUND(SUM(ff.totransp)/SUM(ff.tlitros),4) AS ttarifa, SUM(ff.totransp) AS tottransp,
SUM(ff.totleche)+SUM(ff.totransp) AS total
FROM (
	SELECT
		SUM((a.litros-a.lista)*(b.tipolec<>'F')) AS faltante,
		DATE_FORMAT(a.fecha,'%Y-%m-%d') AS fecha, DATE_FORMAT(a.fecha,'%w') AS sem,
		SUM(a.lista) AS tlitros,
		b.tarifa,
		SUM(a.montopago) AS totransp,
		0 AS llitros,
		0 totleche
	FROM lrece AS a
		JOIN lruta   AS b ON a.ruta=b.codigo
		JOIN sprv    AS c ON b.codprv=c.proveed
	WHERE a.pago=${dbid}
	GROUP BY a.fecha
UNION ALL
	SELECT
		0 AS faltante,
		DATE_FORMAT(b.fecha,'%Y-%m-%d') AS fecha, DATE_FORMAT(b.fecha,'%w') AS sem,
		0 AS litros,
		0 AS tarifa,
		0 totrasp,
		SUM(a.lista) llitros,
		SUM(a.montopago) AS totleche
	FROM itlrece  AS a
		JOIN lrece    AS b ON a.id_lrece=b.id
		JOIN lvaca    AS c ON a.id_lvaca=c.id
		JOIN sprv     AS d ON c.codprv=d.proveed
		JOIN lprecio  AS e ON e.id=$precios
	WHERE a.pago=${dbid} AND MID(b.ruta,1,1) <>'G' AND a.lista>0
	GROUP BY b.fecha
) ff
GROUP BY ff.fecha";
///////////////////////////////////////////////////////////////////////

//echo $mSQL;exit();

$mSQL_2  = $this->db->query($mSQL);

if ($mSQL_2->num_rows() == 0){
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

$ittot = array(
	'lmonto'   =>0,
	'llitros'  =>0,
	'tlitros'  =>0,
	'tmonto'   =>0,
	'total'    =>0,
	'incen'    =>0,
	'tfalta'   =>0,
	'tottransp'=>0
);

?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Pago a proveedor <?php echo $numero ?></title>
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
					<td><b>${nombre}</b></td>
					<td>RIF o CI: <b>${rifci}</b> (${proveed})</td>
				</tr>
			</table>
		</td></tr>
	</table>
encabezado;

// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla="
			<table class=\"change_order_items\" style=\"padding-top:0;font-size: 0.7em\">
				<thead>
					<tr>
						<th ${estilo}' >D&iacute;a</th>
						<th ${estilo}' >Leche</th>
						<th ${estilo}' >Bs. Leche</th>
						<th ${estilo}' >Trans.</th>
						<th ${estilo}' >M.Trans.</th>
						<th ${estilo}' >Fal./Sob.</th>
						<th ${estilo}' >Incentivo</th>
						<th ${estilo}' >Total Pagar</th>
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
						<td style="text-align:right;font-size:1em;font-weight:bold;">%s</td>
						<td style="text-align:right;font-size:1em;font-weight:bold;">%s</td>
						<td style="text-align:right;font-size:1em;font-weight:bold;">%s</td>
						<td style="text-align:right;font-size:1em;font-weight:bold;">%s</td>
						<td style="text-align:right;font-size:1em;font-weight:bold;">%s</td>
						<td style="text-align:right;font-size:1em;font-weight:bold;">%s</td>
						<td style="text-align:right;font-size:1em;font-weight:bold;">%s</td>
					</tr>
				</tfoot>
			</table>

piefinal;

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8" style="text-align: right;">CONTINUA...</td>
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
						<td style="text-align:right" ><?php $ittot['llitros'] += $items->llitros;  echo nformat($items->llitros ,0);  ?></td>
						<td style="text-align:right" ><?php
							$totleche = $items->llitros*$montoregular;
							$ittot['lmonto']  += $totleche;
							echo nformat($totleche,2);
						?></td>
						<td style="text-align:right" ><?php $ittot['tlitros']  += $items->tlitros;   echo nformat($items->tlitros ,0);  ?></td>
						<td style="text-align:right" ><?php $ittot['tottransp']+= $items->tottransp; echo nformat($items->tottransp);  ?></td>
						<td style="text-align:right" ><?php $ittot['tfalta']   += $items->faltante;  echo nformat($items->faltante,0);  ?></td>
						<td style="text-align:right" ><?php
							$incent = $items->totleche-$totleche;
							$ittot['incen']   += $incent;
							echo nformat($incent ,2);
						?></td>
						<td style="text-align:right" ><?php $ittot['total']   += $items->total; echo nformat($items->total ,2); ?></td>
					</tr>
<?php
		$mod = ! $mod;
}

echo sprintf($pie_final,nformat($ittot['llitros'],0),nformat($ittot['lmonto']),nformat($ittot['tlitros'],0),nformat($ittot['tottransp']),nformat($ittot['tfalta']), nformat($ittot['incen']),nformat($ittot['total']));

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
						<td style="text-align: left;font-size: 0.7em"><?php echo $items2->descrip; ?></td>
						<td style="text-align: right" ><?php echo nformat($items2->cantidad,2);    ?></td>
						<td style="text-align: right" ><?php echo nformat($items2->precio  ,2);    ?></td>
						<td style="text-align: right" ><?php $ittot['tlgasto']+=$items2->total; echo nformat($items2->total   ,2); ?></td>
					</tr>
<?php

		$mod = ! $mod;
	} while ($clinea);
}

echo sprintf($pie_final,nformat($ittot['tlgasto']));
}
echo "\n		</td></tr>\n";
echo "	</table>\n";
echo "\n</td></tr>\n";
echo "<tr><td style='font-size:7pt;' >\n";
$tarifas = $this->datasis->damereg("SELECT * FROM lprecio WHERE id=$precios");

//echo "TARIFAS: VACA F-".$tarifas['tarifa1']." C-".$tarifas['tarifa2']." BUFA F-".$tarifas['tarifa3']." C-".$tarifas['tarifa4'];
echo $tit;
?>
</td></tr>
</table>
</body>
</html>
