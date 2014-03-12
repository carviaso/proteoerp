<?php
$maxlin=15; //Maximo de lineas de items.

$cana = $this->datasis->dameval('SELECT COUNT(*) FROM prenom');
if(empty($cana)) show_error('No existe ninguna prenomina');

$rif             = htmlspecialchars(trim($this->datasis->traevalor('RIF')));
$cintillo_titulo1= $this->us_ascii2html(trim($this->datasis->traevalor('TITULO1')));
$cintillo_titulo2= $this->us_ascii2html(trim($this->datasis->traevalor('TITULO2')));
$cintillo_titulo3= $this->us_ascii2html(trim($this->datasis->traevalor('TITULO3')));

$cintillo_titulo2= htmlspecialchars(preg_replace('/[Rr][Ii][Ff] *:? *[VJPGvjpg][0-9\-]+/', ' ', $cintillo_titulo2));
$cintillo_titulo3= htmlspecialchars(preg_replace('/[Rr][Ii][Ff] *:? *[VJPGvjpg][0-9\-]+/', ' ', $cintillo_titulo3));

?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Recibo de Nomina</title>
</head>
<body style="margin-left: 30px; margin-right: 30px; margin-top: 20px; margin-bottom: 20px;">

<?php
$eol = false;
$_query = $this->db->query('SELECT codigo FROM prenom  WHERE valor<>0 GROUP BY codigo');
foreach ($_query->result() as $_row){
	echo ($eol)? '<br>' : '';
	$dbcodigo  = $this->db->escape($_row->codigo);

	$mSQL = "SELECT
		a.contrato,
		b.nombre AS ncontrato,
		CONCAT_WS(' ',TRIM(c.nombre),TRIM(c.apellido)) AS nombre ,
		c.nacional,
		c.cedula,
		c.ingreso,
		a.codigo,
		d.descrip AS cargo,
		a.fecha,
		c.enlace,
		b.tipo
	FROM prenom AS a
	JOIN noco   AS b ON a.contrato=b.codigo
	JOIN pers   AS c ON a.codigo=c.codigo
	JOIN carg   AS d ON c.cargo=d.cargo
	WHERE a.codigo=${dbcodigo} AND a.valor<>0
	AND MID(a.concepto,1,1)<>'9'
	LIMIT 1";

	$mSQL_1 = $this->db->query($mSQL);
	if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
	$row = $mSQL_1->row();

	$tipo      = trim($row->tipo);

	$contrato  = $this->us_ascii2html(trim($row->contrato));
	$ncontrato = $this->us_ascii2html(trim($row->ncontrato));
	$nombre    = $this->us_ascii2html(trim($row->nombre  ));
	$nacional  = $this->us_ascii2html(trim($row->nacional));
	$cedula    = $this->us_ascii2html(trim($row->cedula  ));
	$ingreso   = $row->ingreso;
	$hingreso  = dbdate_to_human($ingreso);
	$fecha     = $row->fecha;
	$enlace    = $row->enlace;
	$hfecha    = dbdate_to_human($fecha);
	$codigo    = $this->us_ascii2html(trim($row->codigo  ));
	$cargo     = $this->us_ascii2html(trim($row->cargo   ));

	$datetime1 =  new DateTime($ingreso);
	$datetime2 =  new DateTime($fecha);
	$interval  = date_diff($datetime1, $datetime2);

	$antiguedad = '';
	if($interval->y >1 ) $antiguedad .= $interval->y.' A&ntilde;os '; else $antiguedad .= $interval->y.' A&ntilde;o ';
	if($interval->m >1 ) $antiguedad .= $interval->m.' meses y '    ; else $antiguedad .= $interval->m.' mes y ';
	if($interval->d >1 ) $antiguedad .= $interval->d.' d&iacute;as '; else $antiguedad .= $interval->d.' d&iacute;a ';

	if($tipo=='M'){
		$dias = 30;
	}elseif($tipo=='Q'){
		$dias = 15;
	}elseif($tipo=='S'){
		$dias = 7;
	}elseif($tipo=='B'){
		$dias = 14;
	}elseif($tipo=='O'){
		$dias = 0;
	}else{
		$dias = 0;
	}

	if($dias>0){
		$datetime2->sub(new DateInterval('P'.$dias.'D'));
		$datetime2->format('d');
		$periodo   = 'PERIODO '.$datetime2->format('d').' AL '.$hfecha;
	}

	$dbfecha   = $this->db->escape($fecha);
	$dbenlace  = $this->db->escape($enlace);

	$lineas = 0;
	$uline  = array();

	$mSQL="SELECT
		concepto,
		IF( monto=valor,0,monto) AS monto,
		descrip,
		(valor>0)*valor AS asigna,
		(valor<0)*valor AS deduc
	FROM prenom
	WHERE codigo=${dbcodigo} AND valor<>0
	AND MID(concepto,1,1)<>'9'";
	$mSQL_2 = $this->db->query($mSQL);
	if($mSQL_2->num_rows()==0) show_error('Error en registro');
	$detalle  = $mSQL_2->result();

	$mSQL = "SELECT
		a.tipo_doc,
		a.numero,
		b.monto,
		b.abonos,
		a.cuota,
		b.monto-b.abonos AS saldo
	FROM pres AS a
	JOIN smov AS b ON a.cod_cli=b.cod_cli AND a.tipo_doc=b.tipo_doc AND a.numero=b.numero
	WHERE a.cod_cli=${dbenlace} AND b.monto>b.abonos AND a.apartir<=${dbfecha}";
	$mSQL_3   = $this->db->query($mSQL);
	$detalle3 = $mSQL_3->result();

	$totales=array('asigna'=>0,'deduc'=>0,'pres'=>0);

?>

<div style='font-size:0.6em; font-family: "verdana", "sans-serif";'>
<?php
//************************
//     Encabezado
//
//************************
$encabezado = "<br>
	<table style='width:100%; border-collapse: collapse;'>
		<tr style='font-weight:bold;font-size:2em;'>
			<td style='text-align:left ;'>${cintillo_titulo1}</td>
			<td style='text-align:right;'>PAGO DE NOMINA DEL </td>
		</tr>
		<tr>
			<td style='text-align:left ;' >${cintillo_titulo2}<br>${cintillo_titulo3}</td>
			<td style='text-align:right;font-weight:bold;font-size:2em;'>${periodo}</td>
		</tr>
	</table>
	<table style='width:100%; border-collapse: collapse;'>
		<tr>
			<td>Obra/Contrato</td>
			<td><b>${ncontrato}</b></td>
			<td>Fecha</td>
			<td><b>${hfecha}</b></td>
			<td></td>
			<td></td>
		</tr>

		<tr>
			<td>Trabajador</td>
			<td><b style='font-size:1.2em;'>${nombre}</b></td>
			<td>C&eacute;dula</td>
			<td><b>${nacional}-${cedula}</b></td>
			<td>Cod.</td>
			<td><b>${codigo}</b></td>
		</tr>

		<tr>
			<td>Cargo</td>
			<td><b>${cargo}</b></td>
			<td>Antiguedad</td>
			<td><b>${antiguedad}</b></td>
			<td>Ingreso</td>
			<td><b>${hingreso}</b></td>
		</tr>
	</table>
";
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$encabezado_tabla="
	<table style='padding-top:0; border-collapse: collapse; width:100%;'>
		<thead>
			<tr style='background: #EEEEEE;border: 1px solid black; font-size: 1.1em;' >
				<th>C&oacute;digo</th>
				<th>Cantidad</th>
				<th>Descripci&oacute;n del pago</th>
				<th>Asignaci&oacute;n</th>
				<th>Deducci&oacute;n</th>
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
			echo $encabezado;
			echo $encabezado_tabla;
			$npagina=false;
		}
?>
			<tr>
				<td style="text-align: center;"><?php echo trim($items->concepto); ?></td>
				<td style="text-align: center;"><?php echo nformat($items->monto); ?></td>
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
						echo $this->us_ascii2html($uline).'<br>';
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
				<td style="text-align: right;"><?php echo ($clinea)? '': ($items->asigna==0)? '-': nformat($items->asigna);; $totales['asigna'] += $items->asigna; ?></td>
				<td style="text-align: right;"><?php echo ($clinea)? '': ($items->deduc ==0)? '-': nformat($items->deduc) ;; $totales['deduc']  += $items->deduc; ?></td>
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
			<tr>
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

<table width="100%" style="border-collapse: collapse;" >
	<tr>
		<td style="text-align:center; border-style:solid; border-width:1px;width:30%;" valign="bottom"><br><br><br><br><br><br><br>
			Recibi Conforme:
		</td>
		<td style="text-align:center; border-style:solid; border-width:1px;width:30%;">
			<table>
				<tr>
					<td colspan='3'>Pr&eacute;stamos:</td>
				</tr>
				<?php
				foreach ($detalle3 AS $pres){ $i++;
				?>
				<tr>
					<td><?php echo trim($pres->tipo_doc).trim($pres->numero); ?></td>
					<td><?php echo nformat($pres->saldo); ?></td>
					<td><?php echo '-'.nformat($pres->cuota); $totales['pres'] += $pres->cuota; ?></td>
				</tr>
				<?php } ?>
			</table>
		</td>
		<td style="text-align: right; border-style:solid; border-width:1px;background: #EEEEEE;">
			<table style='margin-left:auto'>
				<tr>
					<td>Total Asignaciones</td>
					<td style="text-align: right;"><?php echo nformat($totales['asigna']); ?>&nbsp;</td>
				</tr>
				<tr>
					<td>Total Deducciones</td>
					<td style="text-align: right;"><?php echo nformat($totales['deduc']); ?>&nbsp;</td>
				</tr>
				<tr style='font-weight:bold;font-size:1.05em;'>
					<td>TOTAL NOMINA</td>
					<td style="text-align: right;"><?php echo nformat($totales['asigna']-abs($totales['deduc'])); ?>&nbsp;</td>
				</tr>
				<tr>
					<td>Desc. de Pr&eacute;stamos</td>
					<td style="text-align: right;"><?php echo nformat($totales['pres']); ?>&nbsp;</td>
				</tr>
				<tr style='font-weight:bold;font-size:1.8em;'>
					<td>TOTAL A PAGAR&nbsp;&nbsp;</td>
					<td style="text-align: right;"><?php echo nformat($totales['asigna']-abs($totales['deduc'])-abs($totales['pres'])); ?>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<?php
	echo ($eol)? '<div style="page-break-before: always;"></div>' : '';
	$eol = !$eol;
} ?>
</body>
</html>
