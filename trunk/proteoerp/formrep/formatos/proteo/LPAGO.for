<?php
$maxlin=34; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL_1 = $this->db->query('SELECT
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

$dbproveed= $this->db->escape($proveed);

$totcosto= 0;
$lineas  = 0;
$uline   = array();

if($tipo=='T' || $tipo=='A'){
	$tit = 'POR TRANSPORTE';
	$mSQL1="SELECT
		a.fecha,
		DATE_FORMAT(a.fecha,'%w') AS sem,
		SUM(IF(a.litros=0,a.lista,a.litros)) AS litros,
		b.tarifa,
		SUM(ROUND(IF(a.litros=0, a.lista, a.litros )*b.tarifa,2)) AS totmon
	FROM lrece AS a
	JOIN lruta AS b ON a.ruta=b.codigo
	JOIN sprv  AS c ON b.codprv=c.proveed
	WHERE a.pago=${dbid}
	GROUP BY a.fecha";
	$mSQL = $mSQL1;
}

if($tipo=='P' || $tipo=='A'){
	$tit = 'POR PRODUCTOR';
	$mSQL2="SELECT
		b.fecha,
		DATE_FORMAT(b.fecha,'%w') AS sem,
		SUM(a.lista) AS litros,
		'0' AS tarifa,
		SUM(ROUND(a.lista*if(c.tipolec=\"F\",k.ultimo,e.ultimo),2)+ROUND(a.lista*(f.ultimo+g.ultimo+h.ultimo)*(c.tipolec=\"F\")+ROUND(a.lista*IF(c.animal=\"B\",if(c.tipolec=\"F\",i.ultimo,j.ultimo), 0 ),2),2))  AS totmon
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
	WHERE a.pago=${dbid} AND MID(b.ruta,1,1) <>'G' AND a.lista>0
	GROUP BY b.fecha";
	$mSQL = $mSQL2;
}

if($tipo=='A'){
	$tit = '';
	$mSQL=$mSQL1.' UNION ALL '.$mSQL2;
}

$mSQL_2  = $this->db->query($mSQL);
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
$ittot = array('totmon'=>0,'litros'=>0);
?><html>
<head>
<title>Pago a proveedor <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" />
</head>
<body style="margin-left: 30px; margin-right: 30px;">

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
								<td><h1 style="text-align: left">RECIBO DE PAGO ${tit} No. ${numero}</h1></td>
								<td><h1 style="text-align: right">Fecha: ${fecha}</h1></td>
							</tr><tr>
								<td colspan='2'><h1 style="text-align: center">Por Bs.: ***${monto}***</h1></td>
							</tr>
						</table>
						<table align='center' style="font-size: 8pt;">
							<tr>
								<td><b>Hemos cancelado a:</b></td>
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
						</table>
encabezado;
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla="
	<h2>Detalle</h2>
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr>
				<th ${estilo}' >D&iacute;a  </th>
				<th ${estilo}' >Fecha       </th>
				<th ${estilo}' >Total Litros</th>
				<th ${estilo}' >Bs.Litro    </th>
				<th ${estilo}' >Total       </th>
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
				<td colspan='2' >Totales...</td>
				<td style="text-align: right">%s</td>
				<td style="text-align: right"></td>
				<td style="text-align: right">%s</td>
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

				<td style="text-align: center"><?php echo $semana[$items->sem]; ?></td>
				<td style="text-align: center"><?php echo dbdate_to_human($items->fecha);  ?></td>
				<td style="text-align: right" ><?php $ittot['litros'] += $items->litros; echo nformat($items->litros ,2); ?></td>
				<td style="text-align: right" ><?php echo nformat($items->totmon/$items->litros ,2); ?></td>
				<td style="text-align: right" ><?php $ittot['totmon'] += $items->totmon; echo nformat($items->totmon ,2); ?></td>
				<?php
				$lineas++;
				if($lineas >= $maxlin){
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

for(1; $lineas<$maxlin-$nlgasto;$lineas++){ ?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
<?php
	$mod = ! $mod;
}
echo sprintf($pie_final,nformat($ittot['litros']),nformat($ittot['totmon']));

$lineas+=$det3encab;
//******************************
//Detalle de las deducciones
//******************************
$ittot = array('tlgasto'=>0);
if($ngasto>0){
//************************
//   Encabezado Tabla
//************************
$encabezado_tabla="
	<h2>Deducciones:</h2>
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr>
				<th ${estilo}' >Descripci&oacute;n</th>
				<th ${estilo}' >Fecha</th>
				<th ${estilo}' >Referencia </th>
				<th ${estilo}' >Cantidad</th>
				<th ${estilo}' >Precio</th>
				<th ${estilo}' >Total</th>
			</tr>
		</thead>
		<tbody>
";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final='
		</tbody>
		<tfoot style=\'border:1px solid;background:#EEEEEE;\'>
			<tr>
				<td colspan="5" style="text-align: right;">Total...</td>
				<td style="text-align: right">%s</td>
			</tr>
		</tfoot>
</table>
<table  style="width: 100%%; height : 50px;">
	<tr>
		<td style="font-size: 8pt; text-align:center;" valign="bottom"><b>Recibido por:</b></td>
		<td style="font-size: 8pt; text-align:center;" valign="bottom"><b>CI:</b></td>
		<td style="font-size: 8pt; text-align:center;" valign="bottom"><b>Fecha: ____/____/______</b></td>
	</tr>
</table>';

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

echo $encabezado_tabla;
foreach ($detalle2 AS $items2){ $i++;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">

				<td style="text-align: left"  ><?php echo $items2->descrip;               ?></td>
				<td style="text-align: center"><?php echo dbdate_to_human($items2->fecha);?></td>
				<td style="text-align: left"  ><?php echo $items2->referen;               ?></td>
				<td style="text-align: right" ><?php echo nformat($items2->cantidad,2);   ?></td>
				<td style="text-align: right" ><?php echo nformat($items2->precio  ,2);   ?></td>
				<td style="text-align: right" ><?php $ittot['tlgasto']+=$items2->total ; echo nformat($items2->total   ,2);   ?></td>
				<?php
				$lineas++;
				if($lineas > $maxlin){
					$lineas =0;
					$npagina=true;
					break;
				}
				?>
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
echo sprintf($pie_final,nformat($ittot['tlgasto']));
}
?>
</body>
</html>