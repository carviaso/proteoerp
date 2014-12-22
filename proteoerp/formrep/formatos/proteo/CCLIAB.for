<?php
$maxlin=30; //Maximo de lineas de items.

if(count($parametros) < 0) show_error('Faltan parametros');
$id=$parametros[0];

$sel=array('a.tipo_doc','a.numero','a.cod_cli','a.fecha','a.monto','a.abonos'
,'b.nombre','TRIM(b.nomfis) AS nomfis','CONCAT_WS(\'\',TRIM(b.dire11),b.dire12) AS direc','b.rifci'
,'CONCAT_WS(\' \',observa1,observa2) AS observa','b.rifci','a.transac');
$this->db->select($sel);
$this->db->from('smov AS a');
$this->db->join('scli AS b'  ,'a.cod_cli=b.cliente');
$this->db->where('a.id'   , $id);
$this->db->where('a.tipo_doc','AB');

$mSQL_1 = $this->db->get();
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');

$row = $mSQL_1->row();
$tipo_doc = trim($row->tipo_doc);
$numero   = $row->numero;
$cliente  = $this->us_ascii2html(trim($row->cod_cli));
$tipo_doc = trim($row->tipo_doc);
$fecha    = $row->fecha;
$hfecha   = dbdate_to_human($row->fecha);
$monto    = nformat($row->monto);
$montole  = strtoupper(numletra($row->monto));
$abonos   = $row->abonos;
$nombre   = (empty($row->nomfis))? $this->us_ascii2html(trim($row->nombre)) : $this->us_ascii2html($row->nomfis);
$rifci    = trim($row->rifci);
$direc    = $this->us_ascii2html(trim($row->direc));
$observa  = wordwrap(trim(str_replace(',',', ',$row->observa)), 100, '<br>');
$transac  = $row->transac;

$sel=array('b.tipo_doc','b.numero','b.fecha','b.monto','b.abono','b.reten','b.ppago','b.cambio','b.mora','b.reteiva');
$this->db->select($sel);
$this->db->from('smov AS a');
$this->db->join('itccli AS b','a.tipo_doc = b.tipoccli AND a.numero=b.numccli AND a.cod_cli=b.cod_cli');
$this->db->where('a.cod_cli' ,$cliente);
$this->db->where('a.tipo_doc',$tipo_doc);
$this->db->where('a.numero'  ,$numero);
$this->db->where('a.fecha'   ,$row->fecha);
$mSQL_2 = $this->db->get();
$detalle  = $mSQL_2->result();

$sel=array('a.tipo','a.monto','a.num_ref','a.fecha','a.cambio','COALESCE(c.nomb_banc,d.nomb_banc) AS banco');
$this->db->select($sel);
$this->db->from('sfpa AS a');
$this->db->join('banc AS b','a.banco=b.codbanc','left');
$this->db->join('tban AS c','b.tbanco=c.cod_banc','left');
$this->db->join('tban AS d','a.banco=d.cod_banc','left');
$this->db->where('a.transac',$transac);
$mSQL_3 = $this->db->get();
$detalle2 = $mSQL_3->result();

$det3encab = 6; //Tamanio del encadezado de la segunda tabla
$npagos=$mSQL_3->num_rows()+$det3encab;

$ittot['monto']=$ittot['reten']=$ittot['ppago']=$ittot['cambio']=$ittot['mora']=$ittot['reteiva']=$ittot['abono']=$lineas=0;
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Abono <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">

<script type="text/php">
	if (isset($pdf)) {
		$texto = array();
		$font  = Font_Metrics::get_font('verdana');
		$size  = 6;
		$color = array(0,0,0);
		$text_height = Font_Metrics::get_font_height($font, $size);
		$w     = $pdf->get_width();
		$h     = $pdf->get_height();
		$y     = $h - $text_height - 24;

		//***Inicio cuadro
		//**************VARIABLES MODIFICABLES***************
		$texto[]='Recibido por';
		$texto[]='Firma y sello';

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
								<td><h1 style="text-align: left">RECIBO DE INGRESO No. ${numero}</h1></td>
								<td><h1 style="text-align: right">Fecha: ${hfecha}</h1></td>
							</tr><tr>
								<td colspan='2'><h1 style="text-align: center">Por Bs.: ***${monto}***</h1></td>
							</tr>
						</table>
						<table align='center' style="font-size: 8pt;">
							<tr>
								<td><b>Hemos recibido de:</b></td>
								<td>(${cliente}) ${nombre}</td>
							</tr>
							<tr>
								<td><b>Con RIF:</b></td>
								<td>${rifci}</td>
							</tr>
							<tr>
								<td><b>Direcci&oacute;n:</b></td>
								<td>${direc}</td>
							</tr>
							<tr>
								<td><b>La cantidad de:</b></td>
								<td>${montole} Bs.</td>
							</tr>
							<tr>
								<td><b>Por concepto de:</b></td>
								<td>${observa}</td>
							</tr>
						</table>
encabezado;
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla="
	<h2>Detalle:</h2>
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr>
				<th ${estilo}' >Documento   </th>
				<th ${estilo}' >Fecha       </th>
				<th ${estilo}' >Monto/Abono </th>
				<th ${estilo}' >Ret/ISLR    </th>
				<th ${estilo}' >Desc./P/Pago</th>
				<th ${estilo}' >Dif/Cambio  </th>
				<th ${estilo}' >Int./Mora   </th>
				<th ${estilo}' >Ret/IVA     </th>
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
				<td style="text-align: right">%s</td>
				<td style="text-align: right">%s</td>
				<td style="text-align: right">%s</td>
			</tr>
		</tfoot>

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

foreach ($detalle as $items){ $i++;
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
				<td style="text-align: right"><?php $ittot['cambio']  += $items->cambio ; echo nformat($items->cambio ,2); ?></td>
				<td style="text-align: right"><?php $ittot['mora']    += $items->mora   ; echo nformat($items->mora   ,2); ?></td>
				<td style="text-align: right"><?php $ittot['reteiva'] += $items->reteiva; echo nformat($items->reteiva,2); ?></td>
				<td style="text-align: right"><?php $ittot['abono']   += $items->abono  ; echo nformat($items->abono  ,2); ?></td>
				<?php
				$lineas++;
				if($lineas >= $maxlin && count($detalle)!=$i){
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
$mm=$maxlin-$npagos;
for(1; $lineas<$mm;$lineas++){ ?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
<?php
	$mod = ! $mod;
}
echo sprintf($pie_final,nformat($ittot['monto']),nformat($ittot['reten']),nformat($ittot['ppago']),nformat($ittot['cambio']),nformat($ittot['mora']),nformat($ittot['reteiva']),nformat($ittot['abono']));

$lineas+=$det3encab;
//******************************
//detalle del pago
//******************************

//************************
//   Encabezado Tabla
//************************
$encabezado_tabla="
	<h2>Forma de pago:</h2>
	<table class=\"change_order_items\" style=\"padding-top:0; \">
		<thead>
			<tr>
				<th ${estilo}' >Tipo   </th>
				<th ${estilo}' >Fecha  </th>
				<th ${estilo}' >Banco  </th>
				<th ${estilo}' >N&uacute;mero</th>
				<th ${estilo}' >Monto  </th>
			</tr>
		</thead>
		<tbody>
";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final='</table>';

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

echo $encabezado_tabla;
foreach($detalle2 as $items2){ $i++;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">

				<td style="text-align: center"><?php echo $items2->tipo;             ?></td>
				<td style="text-align: center"><?php echo dbdate_to_human($items2->fecha); ?></td>
				<td style="text-align: left"  ><?php echo $items2->banco;            ?></td>
				<td style="text-align: left"  ><?php echo $items2->num_ref;          ?></td>
				<td style="text-align: right" ><?php echo nformat($items2->monto,2); ?></td>
				<?php
				$lineas++;
				if($lineas >= $maxlin && count($detalle2)!=$i){
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
/*
for(1;$lineas<$maxlin;$lineas++){ ?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
<?php
	$mod = ! $mod;
}
*/
echo $pie_final;
?></body>
</html>
