<?php
$maxlin = 24; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$mSQL = "SELECT a.nfiscal,
	a.tipo_doc,a.numero,a.cod_cli,TRIM(c.nomfis) AS nomfis,c.nombre,c.rifci,CONCAT_WS('',TRIM(c.dire11),c.dire12) AS direccion,a.fecha,
	a.iva,a.totals,a.totalg, a.exento,a.tasa, a.montasa, a.reducida, a.monredu, a.sobretasa,a.monadic,
	c.telefono, a.observa1,a.observa2,vence,a.afecta
FROM otin AS a
JOIN scli AS c ON a.cod_cli=c.cliente
WHERE a.id=${dbid} AND a.tipo_doc='ND'";

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
$impuesto = nformat($row->iva);
$direccion= wordwrap(trim($row->direccion),40,"\n");
$tipo_doc = $row->tipo_doc;
$telefono = trim($row->telefono);
$afecta   = $row->afecta;
$base     = nformat($row->totals - $row->exento );
$exento   = nformat($row->exento);

$ivaaplica = $this->datasis->ivaplica($row->fecha);
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

$lineas = 0;
$uline  = array();

$mSQL="SELECT codigo,descrip AS desca,precio AS preca,importe, larga AS detalle, ROUND(impuesto*100/precio,2) AS iva
FROM itotin
WHERE numero=${dbnumero} AND tipo_doc=${dbtipo_doc}";

$mSQL_2   = $this->db->query($mSQL);
$detalle  = $mSQL_2->result();
$art_cana = $mSQL_2->num_rows();
$separador='Ä';

//************************
//     Encabezado
//************************

$encabezado  = "\n\n\n\n\n";
$encabezado .=" Nombre   : ".chr(15).str_pad($nombre,69).chr(18).str_pad('Nota de Debito: ',13,' ',0).chr(15).chr(14).$numero.chr(18)."\n";
$encabezado .=" RIF/C.I. : ".str_pad("${rifci}",40)." Fecha      :${fecha}\n";
$encabezado .=" Direccion: ".chr(15).str_pad($direc0.$direc1,70).chr(18)."Vence      :${vence}\n";
$encabezado .="     Telf.: ".str_pad($telefono,40).chr(14).CHR(15).CHR(18)."\n";
//$encabezado .=" TELEFONO : ".str_pad($telefono,40).chr(14).$referen.CHR(15).CHR(18)."\n";
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
//$encabezado_tabla  = str_pad('', 80, $separador)."\n";
$encabezado_tabla = CHR(18)." CODIGO   DESCRIPCION                                               MONTO   IVA".CHR(18)."\n";
$encabezado_tabla .= str_pad('', 80, $separador)."\n";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final  = CHR(18).str_pad('', 80, $separador)."\n";
$pie_final .= " Factura Afectada: ${afecta}".str_pad('MONTO EXENTO : ',                   36,' ',0).chr(15).chr(14).str_pad($exento , 13,' ',0).chr(18)."\n";
$pie_final .= str_pad('BASE IMPONIBLE SEGUN ALICUOTA DEL '.nformat($ivaaplica['tasa'],0).'%: ',      64,' ',0).chr(15).chr(14).str_pad($base,    13,' ',0).chr(18)."\n";
$pie_final .= str_pad('MONTO TOTAL DEL IMPUESTO SEGUN ALICUOTA '.nformat($ivaaplica['tasa'],0).'%: ',64,' ',0).chr(15).chr(14).str_pad($impuesto,13,' ',0).chr(18)."\n";
$pie_final .= str_pad('MONTO TOTAL :' ,     64,' ',0).chr(15).chr(14).str_pad($gtotal,  13,'*',0).chr(18)."\n";
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

			$ddetall = $this->us_ascii2html(trim($items->detalle));
			$descrip = $this->us_ascii2html(trim($items->desca));
			if(strlen($ddetall) > 0 ) {
				if(strlen($descrip)>0 ){
					if(strpos($ddetall,$descrip)!==false){
						$descrip = $ddetall;
					}else{
						$descrip .= "\n".$ddetall;
					}
				}else{
					$descrip .= $ddetall;
				}
			}

			$descrip = str_replace("\r",'',$descrip);
			$descrip = str_replace(array("\t"),' ',$descrip);

			$descrip = wordwrap($descrip,80,"\n");
			$arr_des = explode("\n",$descrip);
			$uline   = array_shift($arr_des);
			echo chr(15)." ".sprintf('%-13s %-84s %25s %10s',$items->codigo,$uline,nformat($items->preca),nformat($items->iva,2));
			echo "\n";
			$art_cana--;
			$lineas++;
		}

		while(count($arr_des)>0){
			$uline   = array_shift($arr_des);

			echo  chr(15)." ".sprintf('%-13s %-84s %25s %10s', '', '',$uline,'');
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
