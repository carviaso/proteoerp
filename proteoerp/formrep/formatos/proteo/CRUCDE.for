<?php
$maxlin=38; //Maximo de lineas de items.

if(count($parametros) < 0) show_error('Faltan parametros');
$id=$parametros[0];

$sel=array('a.tipo','a.numero','a.cliente','a.proveed','a.nombre','a.nomcli',
'a.fecha','a.saldod','a.monto','a.concept1','a.concept2','a.transac');
$this->db->select($sel);
$this->db->from('cruc AS a');
$this->db->where('a.id', $id);

$mSQL_1 = $this->db->get();
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');

$row      = $mSQL_1->row();
$tipo     = trim($row->tipo);
$numero   = $row->numero;
$montolet = strtoupper(numletra($row->monto));
$cliente  =  $this->us_ascii2html(trim($row->cliente));
$nomcli   =  $this->us_ascii2html(trim($row->nomcli));
$proveed  =  $this->us_ascii2html(trim($row->proveed));
$nomprov  =  $this->us_ascii2html(trim($row->nombre));
$fecha    = $row->fecha;
$hfecha   = dbdate_to_human($row->fecha);
$monto    = nformat($row->monto);
$saldo    = nformat($row->saldod);
$concepto =  $this->us_ascii2html(trim($row->concept1).trim($row->concept2));


$sel=array('a.numero','a.tipo','a.onumero','a.ofecha','a.oregist','a.monto');
$this->db->select($sel);
$this->db->from('itcruc AS a');
$this->db->where('a.numero',$numero);
$mSQL_2 = $this->db->get();
$detalle2  = $mSQL_2->result();


switch ($tipo){
case 'P-C':
	$tit1 = 'proveedor';
	$tit2 = 'cliente';
	$tipo_tit='Proveedor - Cliente';
	break;
case 'C-C':
	$tit1 = 'cliente';
	$tit2 = 'cliente';
	$tipo_tit='Cliente';
	break;
case 'P-P':
	$tit1 = 'proveedor';
	$tit2 = 'proveedor';
	$tipo_tit='Proveedor';
	break;
case 'C-P':
	$tit1 = 'cliente';
	$tit2 = 'proveedor';
	$tipo_tit='Cliente - Proveedor';
	break;
default:
  show_error('Registro no valido');
}

$arr_tipo=explode('-',$tipo);
if(count($arr_tipo)==2){
	if($arr_tipo[0]=='C'){

	}
}

$lineas=0;
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Cruce de cuentas <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
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
		$texto[]='Elaborado por';
		$texto[]='Auditoria';
		$texto[]='Autorizado por:';
		$texto[]='Fecha ____/____/_______';

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
$encabezado = "
	<table style='width:100%;font-size: 9pt;' class='header' cellpadding='0' cellspacing='0'>
		<tr>
			<td><h1 style='text-align:left; border-bottom:1px solid;font-size:12pt;'>CRUCE DE CUENTA Nro. ${numero}</h1></td>
			<td style='text-align:right;'><h1 style='text-align:right;border-bottom:1px solid;font-size:12pt;'>FECHA: ${hfecha}</h1></td>
		</tr><tr>
			<td><b>Tipo de cruce:</b> ${tipo_tit}</td>
			<td><b>Monto</b> ${monto}</td>
		</tr><tr>
			<td><b>Del ${tit1}:</b> (${proveed}) ${nomprov}</td>
			<td><b>Al  ${tit2}:</b> (${cliente}) ${nomcli} </td>
		</tr><tr>
			<td colspan='2'><b>La cantidad de:</b>  ${montolet}</td>
		</tr><tr>
			<td colspan='2'><b>Por concepto de:</b> ${concepto}</td>
		</tr>
	</table><br>
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
				<th ${estilo}' >Documento</th>
				<th ${estilo}' >Fecha</th>
				<th ${estilo}' >D&eacute;bitos del ${tit1}</th>
				<th ${estilo}' >Cr&eacute;ditos del ${tit2}</th>
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
				<td style="text-align: right;" colspan='2'><b>TOTALES</b></td>
				<td style="text-align: right;font-size:16px;font-weight:bold;" >${monto}</td>
				<td style="text-align: right;font-size:16px;font-weight:bold;" >${monto}</td>
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

$this->incluir('X_CINTILLO');
echo $encabezado;
echo $encabezado_tabla;
$npagina = false;
$i       = 0;
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

				<td style="text-align: left"  ><?php echo $items2->onumero;?></td>
				<td style="text-align: center"><?php echo dbdate_to_human($items2->ofecha); ?></td>
				<td style="text-align: right" ><?php echo ($items2->tipo=='APA')? nformat($items2->monto):nformat(0); ?></td>
				<td style="text-align: right" ><?php echo ($items2->tipo=='ADE')? nformat($items2->monto):nformat(0); ?></td>
				<?php
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
echo $pie_final;
?></body>
</html>
