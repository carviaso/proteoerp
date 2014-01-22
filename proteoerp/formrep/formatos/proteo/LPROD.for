<?php
$maxlin=40; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id = $parametros[0];
$dbid=$this->db->escape($id);

$mSQL_1 = $this->db->query("SELECT
	a.fecha,a.codigo,b.descrip,a.status,
	a.peso
FROM lprod AS a
JOIN sinv AS b ON a.codigo=b.codigo
WHERE a.id=${dbid}");
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha     = dbdate_to_human($row->fecha);
$numero    = str_pad($id, 8, '0', STR_PAD_LEFT);
$peso      = nformat($row->peso);
$codigo    = htmlspecialchars(trim($row->codigo ));
$descrip   = htmlspecialchars(trim($row->descrip));
$dbcodigo  = $this->db->escape($codigo);
$status    =($row->status=='C')? 'Cerrado' : ($row->status=='A')? 'Abierto' : ($row->status=='F')? 'Finalizado' : 'Desconocido';

$lineas = 0;
$uline  = array();

$mSQL_3 = $this->db->query("SELECT
a.codrut AS codigo, a.nombre AS descrip, a.litros
FROM itlprod AS a
WHERE a.id_lprod=${dbid}
ORDER BY a.codrut");
$detalle2 = $mSQL_3->result();


$mSQL_2 = $this->db->query("SELECT
a.codigo,a.descrip,a.cantidad
FROM sinvpitem AS a
WHERE a.producto=${dbcodigo}");
$detalle  = $mSQL_2->result();

$det2encab = 5; //Tamanio del encadezado de la segunda tabla
$nsitems=$mSQL_2->num_rows();
?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Reparto <?php echo $numero ?></title>
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
			<td valign='bottom'><h1 style="text-align: left">Orden de producci&oacute;n</h1></td>
			<td valign='bottom'><h1 style="text-align: right">N&uacute;mero: ${numero}</h1></td>
		</tr><tr>
			<td>Producto:<b>(${codigo}) ${descrip}</b></td>
			<td>Fecha: <b>${fecha}</b></td>
		</tr><tr>
			<td>Estado: <b>${status}</b></td>
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
				<th ${estilo}'>Ruta</th>
				<th ${estilo}'>Nombre</th>
				<th ${estilo}'>Litros</th>
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
				<td style="text-align: right;">&nbsp;</td>
				<td style="text-align: right;">&nbsp;</td>
				<td style="text-align: right;"><b>%s</b></td>
			</tr>
		</tfoot>
	</table>
piefinal;

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3" style="text-align: right;">CONTINUA...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

$mod     = $clinea = false;
$npagina = true;
$litrost   = $pesot = 0;
$i       = 0;
foreach ($detalle2 AS $items){ $i++;
	$litrost += $items->litros;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;

		}
?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td style="text-align: center;"><?php echo ($clinea)? '': $items->codigo; ?></td>
				<td>
				<?php
					if(!$clinea){
						$descrip = trim($items->descrip);
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
				<td style="text-align: right;"><?php echo ($clinea)? '': nformat($items->litros,2); ?></td>
			</tr>
<?php
		if($npagina){
			echo $pie_continuo;
		}else{
			$mod = ! $mod;
		}
	} while ($clinea);
}
echo sprintf($pie_final,nformat($litrost));

//************************************
// Lista de articulos
//************************************

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
		<tfoot>
			<tr>
				<td colspan="3" style="text-align: right;">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
piefinal;

$pie_continuo=<<<piecontinuo
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3" style="text-align: right;">CONTINUA...</td>
			</tr>
		</tfoot>
	</table>
<div style="page-break-before: always;"></div>
piecontinuo;
//Fin Pie Pagina

$lineas+=$det2encab;
$i = 0;
echo '<h2>Lista de Insumos</h2>';
echo $encabezado_tabla;

foreach ($detalle AS $items){ $i++; $nsitems=$nsitems-1;
	do {
		if($npagina){
			$this->incluir('X_CINTILLO');
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>
			<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
				<td style="text-align: left;"  ><?php echo ($clinea)? '': $items->codigo; ?></td>
				<td>
				<?php
					if(!$clinea){
						$descrip = trim($items->descrip);
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
							if($nsitems>0){
								$npagina=true;
								$lineas =0;
							}
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
				<td style="text-align: right;"><?php echo ($clinea)? '': nformat($items->cantidad  ,2); ?></td>
			</tr>
<?php
		if($npagina && $nsitems>0){
			echo $pie_continuo.$nsitems;
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
			</tr>
<?php
	$mod = ! $mod;
}
echo $pie_final;
?>
</body>
</html>
