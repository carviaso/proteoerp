<?php
$maxlin=30; //Maximo de lineas de items.

if(count($parametros) < 0) show_error('Faltan parametros');
$id=$parametros[0];

$sel=array('a.tipo_doc','a.numero','a.cod_prv','a.fecha','a.monto','a.abonos','a.banco','a.codigo','a.descrip'
,'b.nombre','TRIM(b.nomfis) AS nomfis','TRIM(b.direc1) AS direc','b.rif','a.tipo_op','a.numche'
,'CONCAT_WS(\' \',observa1,observa2) AS observa','b.rif AS rifci','a.transac');
$this->db->select($sel);
$this->db->from('sprm AS a');
$this->db->join('sprv AS b'  ,'a.cod_prv=b.proveed');
$this->db->where('a.id'   , $id);
$this->db->where('a.tipo_doc','NC');

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
$nombre   = (empty($row->nomfis))? $this->us_ascii2html(trim($row->nombre)) : $this->us_ascii2html($row->nomfis);
$rifci    = htmlspecialchars(trim($row->rifci));
$direc    = $this->us_ascii2html(trim($row->direc));
$observa  = wordwrap(trim(str_replace(',',', ',$this->us_ascii2html($row->observa))), 100, '<br>');
$transac  = $row->transac;
$tipo_op  = trim($row->tipo_op);
$banco    = $this->us_ascii2html(trim($row->banco));
$numche   = trim($row->numche);
$codigo   = trim($row->codigo);
$descrip  = $this->us_ascii2html($row->descrip);


$sel=array('b.tipo_doc','b.numero','b.fecha','b.monto','b.abono','b.reten','b.ppago','b.cambio','b.mora','b.reteiva');
$this->db->select($sel);
$this->db->from('sprm AS a');
$this->db->join('itppro AS b','a.tipo_doc = b.tipoppro AND a.numero=b.numppro AND a.cod_prv=b.cod_prv');
$this->db->where('a.cod_prv' ,$proveed);
$this->db->where('a.tipo_doc',$tipo_doc);
$this->db->where('a.numero'  ,$numero);
$this->db->where('a.fecha'   ,$row->fecha);
$mSQL_2 = $this->db->get();
$detalle  = $mSQL_2->result();


$ittot['monto']=$ittot['reten']=$ittot['ppago']=$ittot['cambio']=$ittot['mora']=$ittot['reteiva']=$ittot['abono']=$lineas=0;
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Pago a proveedor <?php echo $numero ?></title>
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
						<table style="width: 100%;" class="header" border='0'>
							<tr>
								<td><h1 style="text-align: left">NOTA DE CREDITO EN CUENTAS POR PAGAR No. ${numero}</h1></td>
								<td><h1 style="text-align: right">Fecha: ${hfecha}</h1></td>
							</tr><tr>
								<td colspan='2' style="text-align: center;font-size:18pt;font-weight:bold;">Por Bs.: ***${monto}***</td>
							</tr><tr>
								<td colspan='2' style="text-align: center;font-size:8pt;">(Son: ${montole} Bs.)</td>
							</tr>
						</table>
						<br>
						<table align='center' style="font-size: 8pt;width:95%;" border='0'>
							<tr>
								<td width='70'>Con cargo a:</td>
								<td style="font-size:11pt;">(${codigo}) <b>${descrip}</b></td>
							</tr>
							<tr>
								<td>Proveedor:</td>
								<td style="font-size:11pt;">(${proveed}) <b>${nombre}</b></td>
							</tr>
							<tr>
								<td>RIF o C.I.:</td>
								<td style="font-size:11pt;"><b>${rifci}</b></td>
							</tr>
							<tr>
								<td>Concepto de:</td>
								<td><b>${observa}</b></td>
							</tr>
						</table>
encabezado;

// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla="
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr>
				<th ${estilo}' >Documento   </th>
				<th ${estilo}' >Fecha       </th>
				<th ${estilo}' >Monto       </th>
				<th ${estilo}' >Ret/ISLR    </th>
				<th ${estilo}' >Desc./P/Pago</th>
				<th ${estilo}' >Abono neto  </th>
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
				<td style="text-align: right">%s</td>
				<td style="text-align: right">%s</td>
				<td style="text-align: right">%s</td>
			</tr>
		</tfoot>
	</table>

	<table  style="width: 100%%; height : 50px;">
		<tr>
			<td style="font-size: 8pt; text-align:center;" valign='bottom'><b>Recibido por:</b></td>
			<td style="font-size: 8pt; text-align:center;" valign='bottom'><b>CI:</b></td>
			<td style="font-size: 8pt; text-align:center;" valign='bottom'><b>Fecha: ____/____/______</b></td>
		</tr>
	</table>
piefinal;


$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="9" style="text-align: right;">CONTINUA...</td>
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

				<td style="text-align: left" ><?php echo $items->tipo_doc.$items->numero; ?></td>
				<td style="text-align: left" ><?php echo dbdate_to_human($items->fecha);  ?></td>
				<td style="text-align: right"><?php $ittot['monto']   += $items->monto  ; echo nformat($items->monto  ,2); ?></td>
				<td style="text-align: right"><?php $ittot['reten']   += $items->reten  ; echo nformat($items->reten  ,2); ?></td>
				<td style="text-align: right"><?php $ittot['ppago']   += $items->ppago  ; echo nformat($items->ppago  ,2); ?></td>
				<td style="text-align: right"><?php $ittot['abono']   += $items->abono  ; echo nformat($items->abono  ,2); ?></td>
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

//				<!-- td style="text-align: right"><?php $ittot['cambio']  += $items->cambio ; echo nformat($items->cambio ,2); ? ></td>
//				<td style="text-align: right"><?php $ittot['mora']    += $items->mora   ; echo nformat($items->mora   ,2); ? ></td>
//				<td style="text-align: right"><?php $ittot['reteiva'] += $items->reteiva; echo nformat($items->reteiva,2); ? ></td -->


		$mod = ! $mod;
	} while ($clinea);
}

for(1; $lineas<$maxlin;$lineas++){ ?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>&nbsp;</td><td>&nbsp;</td>
			</tr>
<?php
	$mod = ! $mod;
}
echo sprintf($pie_final,nformat($ittot['monto']),nformat($ittot['reten']),nformat($ittot['ppago']),nformat($ittot['abono']));
?></body>
</html>
