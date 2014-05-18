<?php
$maxlin=40; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id = $parametros[0];
$dbid=$this->db->escape($id);

$mSQL_1 = $this->db->query("SELECT a.fecha,a.numero,a.cod_cli,a.nombre,a.direc,a.totals,a.iva,a.totalg,a.anticipo,a.peso,a.referen,a.observa,a.rifci,
	CONCAT(trim(c.dire11),' ', c.dire12) AS direccion,a.factura,a.fecha,a.vence,a.vd, b.nombre AS nomvend
FROM pfac a JOIN scli AS c ON a.cod_cli=c.cliente LEFT JOIN vend b ON a.vd=b.vendedor
WHERE a.id=${dbid}");
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    = dbdate_to_human($row->fecha);
$numero   = htmlspecialchars(trim($row->numero));
$cod_cli  = $this->us_ascii2html(trim($row->cod_cli));
$rifci    = htmlspecialchars(trim($row->rifci));
$nombre   = $this->us_ascii2html(trim($row->nombre));
$stotal   = $row->totals;
$gtotal   = $row->totalg;
$peso     = $row->peso;
$impuesto = $row->iva;
$direccion= $this->us_ascii2html(trim($row->direccion));
$dbnumero = $this->db->escape($row->numero);

$lineas = 0;
$uline  = array();
$mSQL_2 = $this->db->query("SELECT a.codigoa AS codigo,b.descrip AS desca,a.cana,a.preca,a.tota,a.iva
	FROM itpfac AS a
	JOIN sinv   AS b ON a.codigoa=b.codigo
	WHERE a.numa=${dbnumero} AND a.cana>0");
$detalle= $mSQL_2->result();
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Pedido <?php echo $numero ?></title>
<link rel="stylesheet" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body style="margin-left: 30px; margin-right: 30px;">

<?php
//************************
//     Encabezado
//************************
$encabezado = <<<encabezado
	<table style="width: 100%;" class="header">
		<tr>
			<td valign='bottom'><h1 style="text-align: left">Pedido de cliente</h1></td>
			<td valign='bottom'><h1 style="text-align: right">N&uacute;mero: ${numero}</h1></td>
		</tr><tr>
			<td>Cliente:<b>${cod_cli}</b></td>
			<td>Fecha:  <b>${fecha}</b></td>
		</tr><tr>
			<td>Nombre: <b>${nombre}</b></td>
			<td>Rif/CI: <b>${rifci}</b></td>
		</tr><tr>
			<td>Direcci&oacute;n: <b>${direccion}</b></td>
			<td>Peso:      <b>${peso}</b></td>
		</tr>
	</table>
encabezado;
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$estilo  = "style='color: #111111;background: #EEEEEE;border: 1px solid black;font-size: 8pt;";
$encabezado_tabla=<<<encabezado_tabla
	<table class="change_order_items">
		<thead>
			<tr>
				<th ${estilo}'>C&oacute;digo</th>
				<th ${estilo}'>Descripci&oacute;n</th>
				<th ${estilo}'>Cantidad</th>
				<th ${estilo}'>Precio</th>
				<th ${estilo}'>Importe</th>
			</tr>
		</thead>
		<tbody>
encabezado_tabla;
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final=<<<piefinal
		</tbody>
		<tfoot style='border:1px solid;background:#EEEEEE;'>
			<tr>
				<td  style="text-align: right;"></td>
				<td colspan="2" style="text-align: right;"><b>Monto Total de la Base Imponible seg&uacute;n Alicuota :</b></td>
				<td colspan="2" style="text-align: right;font-size:16px;font-weight:bold;" >${stotal}</td>
			</tr>
			<tr>
				<td style="text-align: right;"></td>
				<td colspan="2" style="text-align: right;"><b>Monto Total del Impuesto seg&uacute;n Alicuota:</b></td>
				<td colspan="2" style="text-align: right;font-size:16px;font-weight:bold;">${impuesto}</td>
			</tr>
			<tr style='border-top: 1px solid;background:#AAAAAA;'>
				<td style="text-align: right;"></td>
				<td colspan="2" style="text-align: right;"><b>VALOR TOTAL:</b></td>
				<td colspan="2" style="text-align: right;font-size:20px;font-weight:bold;">${gtotal}</td>
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
				<td style="text-align: center"><?php echo ($clinea)? '':  $this->us_ascii2html(trim($items->codigo)); ?></td>
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
						echo  $this->us_ascii2html($uline).'<br />';
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
				<td style="text-align: center;"><?php    echo ($clinea)? '': nformat($items->cana,3); ?></td>
				<td style="text-align: right;" ><?php    echo ($clinea)? '': nformat($items->preca); ?></td>
				<td class="change_order_total_col"><?php echo ($clinea)? '': nformat($items->preca*$items->cana); ?></td>
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
			</tr>
<?php
	$mod = ! $mod;
}
echo $pie_final;
?>
</body>
</html>
