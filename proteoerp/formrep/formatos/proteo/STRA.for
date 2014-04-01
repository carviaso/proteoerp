<?php
$maxlin=38; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

if ($this->db->field_exists('proveed', 'stra')){
	$rma=true;
	$mSQL = 'SELECT
	a.numero,a.fecha,a.envia,a.recibe,CONCAT_WS("",TRIM(a.observ1),a.observ2) AS observa
	,b.ubides AS enviades, c.ubides AS recibedes,TRIM(a.proveed) AS proveed,d.nombre,TRIM(d.nomfis) AS nomfis, a.condiciones
	FROM stra AS a
	LEFT JOIN caub AS b ON a.envia =b.ubica
	LEFT JOIN caub AS c ON a.recibe=c.ubica
	LEFT JOIN sprv AS d ON a.proveed=d.proveed
	WHERE a.id='.$dbid;
}else{
	$rma=false;
	$mSQL = 'SELECT
	a.numero,a.fecha,a.envia,a.recibe,CONCAT_WS("",TRIM(a.observ1),a.observ2) AS observa
	,b.ubides AS enviades, c.ubides AS recibedes,"" AS proveed,"" AS nombre,"" AS nomfis,"" AS condiciones
	FROM stra AS a
	LEFT JOIN caub AS b ON a.envia =b.ubica
	LEFT JOIN caub AS c ON a.recibe=c.ubica
	WHERE a.id='.$dbid;

}

$mSQL_1 = $this->db->query($mSQL);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha     = dbdate_to_human($row->fecha);
$numero    = $row->numero;
$envia     = $this->us_ascii2html(trim($row->envia));
$enviades  = $this->us_ascii2html(trim($row->enviades));
$recibe    = $this->us_ascii2html(trim($row->recibe));
$recibedes = $this->us_ascii2html(trim($row->recibedes));
$observa   = $this->us_ascii2html(trim($row->observa));
$dbnumero  = $this->db->escape($numero);
$sprv      = $this->us_ascii2html(trim($row->proveed));
$nombre    = (empty($row->nomfis))? htmlspecialchars(trim($row->nombre)) : htmlspecialchars($row->nomfis);
$condi     = htmlspecialchars(trim($row->condiciones));
$vacio     = '';

if($envia=='INFI'){
	$invfis = true;
	$titulo = 'INVENTARIO FISICO';
	$titulo2= '';
	$oob    = 'vacio';
}else{
	$invfis = false;
	if(empty($sprv)){
		$titulo = 'TRANSFERENCIA';
		$titulo2= 'Observaci&oacute;n';
		$oob    ='observa';
	}else{
		$titulo = 'TRANSFERENCIA POR RMA';
		$titulo2= 'Condiciones:';
		$oob    ='condi';
	}
}

$totcosto= 0;
$lineas  = 0;
$uline   = array();

$mSQL_2 = $this->db->query('SELECT
a.codigo,b.descrip AS desca,a.cantidad AS cana,a.costo,a.anteri
FROM itstra AS a
JOIN sinv AS b ON a.codigo=b.codigo
WHERE a.numero='.$dbnumero);
$detalle  = $mSQL_2->result();
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title><?php echo $titulo.' '.$numero ?></title>
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
			<td valign='bottom'><h1 style='text-align:left;border-bottom:1px solid;font-size:12pt;'>${titulo} Nro. ${numero}</h1></td>
			<td valign='bottom'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>Fecha: ${fecha}</h1></td>
		</tr><tr>
			<td>Almac&eacute;n que env&iacute;a:<b>(${envia}) ${enviades}</b></td>
			<td>Almac&eacute;n que recibe:<b>(${recibe}) ${recibedes}</b></td>
		</tr>
		<tr>
			<td colspan='2' style='text-align:center;'><b>${titulo2}</b></td>
		</tr>
		<tr>
			<td colspan='2' style='text-align:center;'>".$$oob."</td>
		</tr>
";
if(!empty($sprv)){
	$encabezado.="
		<tr>
			<td colspan='2'>Proveedor:<b>(${sprv}) ${nombre}</b></td>
		</tr>";
}
$encabezado .= '</table><br />';
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
				<th ${estilo}' >Costo</th>";
if($invfis){
	$encabezado_tabla.="<th ${estilo}' >Anterior</th>";
	$encabezado_tabla.="<th ${estilo}' >Contado</th>";
	$colspan=6;
}else{
	$colspan=4;
}
$encabezado_tabla.="			</tr>
		</thead>
		<tbody>
";

//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$ccolspan=$colspan-1;
$pie_final=<<<piefinal
		</tbody>
		<tfoot style='border:1px solid;background:#EEEEEE;'>
			<tr style='border-top: 1px solid;background:#AAAAAA;'>
				<td style="text-align: right;font-size:20px;" colspan="${ccolspan}">Total...</td>
				<td style="text-align: right;font-size:20px;font-weight:bold;"> %s </td>
			</tr>
		</tfoot>

	</table>
piefinal;


$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="${colspan}" style="text-align:right;font-size:20px;">CONTINUA...</td>
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
	$totcosto += $items->costo*$items->cana;
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
				<td style="text-align: right;"><?php echo ($clinea)? '': nformat($items->costo*$items->cana); ?></td>

				<?php if($invfis){ ?>
				<td style="text-align: right;"><?php echo ($clinea)? '': nformat($items->anteri); ?></td>
				<td style="text-align: right;"><?php echo ($clinea)? '': nformat($items->anteri+$items->cana); ?></td>
				<?php } ?>
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
			<?php if($invfis){ ?>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<?php } ?>
		</tr>
<?php
	$mod = ! $mod;
}
echo sprintf($pie_final,nformat($totcosto));
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
