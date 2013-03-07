<?php
$maxlin = 40; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL = "SELECT If(a.referen='E','Efectivo',IF( a.referen='C','Cr&eacute;dito',IF(a.referen='M','Mixto','Pendiente'))) AS referen,
	a.tipo_doc,a.numero,a.cod_cli,a.nombre,a.rifci,CONCAT(a.direc,a.dire1) AS direccion,a.factura,a.fecha,a.vence,a.vd,
	a.iva,a.totals,a.totalg, a.exento,b.nombre AS nomvend,tipo_doc, a.numero,a.peso,c.telefono
FROM sfac a
JOIN scli AS c ON a.cod_cli=c.cliente
LEFT JOIN vend b ON a.vd=b.vendedor
WHERE a.id=${dbid}";

$mSQL_1 = $this->db->query($mSQL);
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    = dbdate_to_human($row->fecha);
$vence    = dbdate_to_human($row->vence);
$numero   = $row->numero;
$cod_cli  = trim($row->cod_cli);
$rifci    = trim($row->rifci);
$nombre   = trim($row->nombre);
$stotal   = nformat($row->totals);
$gtotal   = nformat($row->totalg);
$peso     = nformat($row->peso);
$impuesto = nformat($row->iva);
$direccion= wordwrap(trim($row->direccion),40,"\n");
$tipo_doc = $row->tipo_doc;
$referen  = strtoupper($row->referen);
$telefono = trim($row->telefono);
$nomvend  = $row->nomvend;
$factura  = ($tipo_doc=='D')? $row->factura :'';

$dd=explode("\n",$direccion);
foreach($dd as $iid=>$val){
	$obj ='direc'.$iid;
	$$obj=$val;
}
if(!isset($direc1)){
	$direc1='';
}

$dbtipo_doc = $this->db->escape($tipo_doc);
$dbnumero   = $this->db->escape($numero);

if($tipo_doc == "F")
	$documento = "FACTURA";
elseif ($tipo_doc == "D")
	$documento = "NOTA DE CREDITO";
else
	$documento = "PRE-FACTURA";

$lineas = 0;
$uline  = array();

$mSQL="SELECT
	codigoa AS codigo,desca,cana,preca,tota AS importe,iva,detalle
FROM sitems
WHERE numa=$dbnumero AND tipoa=$dbtipo_doc";

$mSQL_2   = $this->db->query($mSQL);
$detalle  = $mSQL_2->result();
$art_cana = $mSQL_2->num_rows();
$separador='Ä';

//************************
//     Encabezado
//************************

$encabezado  = "\n\n\n\n\n\n\n\n";
$encabezado .=" CODIGO   : ".str_pad("${cod_cli}  C.I/RIF: ${rifci}",40).str_pad($documento.'    :',13,' ',0).chr(15).chr(14).$numero.chr(18)."\n";
$encabezado .=" CLIENTE  : ".chr(15).str_pad($nombre,69).chr(18)." FECHA      : ${fecha}\n";
$encabezado .=" DIRECCION: ".str_pad($direc0,40)." VENCE      : $vence\n";
$encabezado .="          : ".str_pad($direc1,40)." VENDEDOR   : $nomvend\n";
$encabezado .=" TELEFONO : ".str_pad($telefono,30).chr(14).$referen.CHR(15)." PAG. NRO :  1".CHR(18)."\n";
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
$encabezado_tabla  = str_pad('', 160, $separador)."\n";
$encabezado_tabla .= " CANT     CODIGO              DESCRIPCION              PRECIO     IMPORTE    IVA\n";
$encabezado_tabla .= str_pad('', 160, $separador)."\n";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final  = str_pad('', 160, $separador)."\n";
$pie_final .= chr(18).chr(14).str_pad('BASE IMPONIBLE: '          ,26,' ',0).str_pad(nformat($stotal  ),13,'*',0).chr(18)."\n";
$pie_final .= chr(18).chr(14).str_pad('MONTO TOTAL DEL IMPUESTO: ',26,' ',0).str_pad(nformat($impuesto),13,'*',0).chr(18)."\n";
$pie_final .= chr(18).chr(14).str_pad('MONTO TOTAL DE LA VENTA: ' ,26,' ',0).str_pad(nformat($gtotal  ),13,'*',0).chr(18)."\n";
$pie_final .= str_pad('', 160, $separador)."\n";

$pie_continuo   = str_pad('', 160, $separador)."\n";
$pie_continuo  .= '  CONTINUA...'."\n";
//Fin Pie Pagina

$mod     = $clinea = false;
$npagina = true;
$i       = 0;
$sumline = 0;

foreach ($detalle AS $items){ $i++;
	do {
		if($npagina){
			echo $encabezado;
			echo $encabezado_tabla;
			$lineas =  substr_count($encabezado,"\n")+substr_count($encabezado_tabla,"\n")+substr_count($pie_continuo,"\n");;

			//if($lineas+substr_count($pie_final,"\n")+$art_cana <= $maxlin ){
			//	$lineas += substr_count($pie_final,"\n");
			//}else{
			//	$lineas += substr_count($pie_continuo,"\n");
			//}


			$npagina=false;
		}
		if(!$clinea){
			$ddetall = trim($items->detalle);
			$descrip = trim($items->desca);

			if(strlen($ddetall) > 0 ) {
				if(strpos($ddetall,$descrip)!==false){
					$descrip = $ddetall;
				}else{
					$descrip .= "\n".$ddetall;
				}
			}

			$descrip = str_replace("\r",'',$descrip);
			$descrip = str_replace(array("\t"),' ',$descrip);

			$descrip = wordwrap($descrip,40,"\n");
			$arr_des = explode("\n",$descrip);
			$uline   = array_shift($arr_des);
			echo sprintf('%\'*3d %-10s %-40s %10s %10s %5s',$items->cana,$items->codigo,$uline,nformat($items->preca),nformat($items->cana*$items->preca),nformat($items->iva,0));
			echo "\n";
			$art_cana--;
			$lineas++;
		}

		while(count($arr_des)>0){
			$uline   = array_shift($arr_des);

			echo sprintf('%3s %-10s %-40s %10s %10s %5s', '', '',$uline,'','','');
			echo "\n";

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
		if(count($arr_des)==0 && $clinea)
			$clinea=false;
		if($npagina){
			echo $pie_continuo;
		}else{
			$mod = ! $mod;
		}
	} while ($clinea);
}
for(1;$lineas<$maxlin;$lineas++){
	echo "\n";
}
echo $pie_final;
?>
