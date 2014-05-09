<?php
$maxlin = 24; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL = "SELECT If(a.referen='E','Contado',IF( a.referen='C','Credito',IF(a.referen='M','Mixto','Pendiente'))) AS referen,
	a.tipo_doc,a.numero,a.cod_cli,a.nombre,a.rifci,CONCAT_WS('',TRIM(c.dire11),TRIM(c.dire12)) AS direccion,a.factura,a.fecha,a.vence,a.vd,
	a.iva,a.totals,a.totalg, a.exento,c.nomfis,b.nombre AS nomvend,tipo_doc, a.numero,a.peso,c.telefono,c.formap,a.referen AS condi
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
$nombre   = (empty($row->nomfis))? $row->nombre : $row->nomfis;
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
$base     = nformat($row->totals - $row->exento );
$exento   = nformat($row->exento);
$vd       = str_pad($row->vd,4);

$condi = $row->condi;
$formap= intval($row->formap);
if($formap <= 1 && $condi=='C'){
	$vence    = $fecha;
}

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
	a.codigoa AS codigo,a.desca,a.cana,a.preca,a.tota AS importe,a.iva,a.detalle,b.peso*a.cana AS peso
FROM sitems AS a
JOIN sinv AS b ON a.codigoa=b.codigo
WHERE numa=$dbnumero AND tipoa=$dbtipo_doc";

$mSQL_2   = $this->db->query($mSQL);
$detalle  = $mSQL_2->result();
$art_cana = $mSQL_2->num_rows();
$separador='Ä';

//************************
//     Encabezado
//************************

$encabezado  = "\n\n\n\n\n";
$encabezado .=" Nombre   : ".chr(15).str_pad($this->us_ascii($nombre),69).chr(18).str_pad($documento.'    :',13,' ',0).chr(15).chr(14).$numero.chr(18)."\n";
$encabezado .=" RIF/C.I. : ".str_pad("${rifci}",12)."Vend: ${vd} Vence :$vence FECHA      :${fecha}\n";
$encabezado .=" Direccion: ".chr(15).str_pad($this->us_ascii($direc0.$direc1),66).chr(18);
if($tipo_doc=='D'){
	$encabezado .=' Afecta: '.$factura;
}
$encabezado .= "\n";
$encabezado .="     Telf.: ".str_pad($telefono,40).chr(14).$referen.CHR(15).CHR(18)."\n";
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
//$encabezado_tabla  = str_pad('', 80, $separador)."\n";
$encabezado_tabla = CHR(18)." CODIGO   DESCRIPCION                   PESO     CANT     PRECIO    IMPORTE IVA".CHR(18)."\n";
$encabezado_tabla .= str_pad('', 80, $separador)."\n";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final  = CHR(18).str_pad('', 80, $separador)."\n";
$pie_final .= " Peso: <----------->".str_pad('MONTO EXENTO O EXONERADO: ',                   44,' ',0).chr(15).chr(14).str_pad($exento , 13,' ',0).chr(18)."\n";
$pie_final .= str_pad('BASE IMPONIBLE SEGUN ALICUOTA DEL 12%: ',      64,' ',0).chr(15).chr(14).str_pad($base,    13,' ',0).chr(18)."\n";
$pie_final .= str_pad('MONTO TOTAL DEL IMPUESTO SEGUN ALICUOTA 12%: ',64,' ',0).chr(15).chr(14).str_pad($impuesto,13,' ',0).chr(18)."\n";
$pie_final .= str_pad('VALOR TOTAL DE LA VENTA DE LOS BIENES: ' ,     64,' ',0).chr(15).chr(14).str_pad($gtotal,  13,'*',0).chr(18)."\n";
$pie_final .= str_pad('', 80, $separador)."\n\n\n\n\n";

$pie_continuo   = str_pad('', 80, $separador)."\n";
$pie_continuo  .= '  CONTINUA...'."\n";
//Fin Pie Pagina

$mod     = $clinea = false;
$npagina = true;
$i       = 0;
$sumline = 0;
$tpeso   = 0;

foreach ($detalle AS $items){ $i++;
	do {
		if($npagina){
			echo $encabezado;
			echo $encabezado_tabla;
			$lineas =  substr_count($encabezado,"\n")+substr_count($encabezado_tabla,"\n")+substr_count($pie_continuo,"\n");
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

			$descrip = wordwrap($descrip,50,"\n");
			$arr_des = explode("\n",$descrip);
			$uline   = array_shift($arr_des);
			echo chr(15)." ".sprintf('%-13s %-49s %9s %16s %17s %20s %4s',$this->us_ascii($items->codigo),$this->us_ascii($uline),nformat($items->peso),nformat($items->cana),nformat($items->preca),nformat($items->cana*$items->preca),nformat($items->iva,2));
			echo "\n";
			$tpeso += $items->peso;
			$art_cana--;
			$lineas++;
		}

		while(count($arr_des)>0){
			$uline   = array_shift($arr_des);

			echo sprintf('%-13s %-49s %9s %16s %17s %20s %4s', '', '',$this->us_ascii($uline),'','','','');
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
echo str_replace('<----------->', str_pad(nformat($tpeso), 13), $pie_final);
?>
