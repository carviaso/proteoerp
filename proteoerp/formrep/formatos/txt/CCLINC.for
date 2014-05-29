<?php
$maxlin = 24; //Maximo de lineas de items.

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);


$sel=array('a.tipo_doc','a.numero','a.cod_cli','a.fecha','a.monto','a.abonos','a.exento','a.montasa','a.tasa'
,'b.nombre','TRIM(b.nomfis) AS nomfis','CONCAT_WS(\'\',TRIM(b.dire11),b.dire12) AS direc','b.rifci','b.telefono'
,'CONCAT_WS(\' \',observa1,observa2) AS observa','b.rifci','a.transac','a.codigo','a.descrip');
$this->db->select($sel);
$this->db->from('smov AS a');
$this->db->join('scli AS b'  ,'a.cod_cli=b.cliente');
$this->db->where('a.id'   , $id);
$this->db->where('a.tipo_doc','NC');
$mSQL_1 = $this->db->get();

if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row = $mSQL_1->row();

$fecha    = dbdate_to_human($row->fecha);
$vence    = '';
$numero   = $row->numero;
$cod_cli  = trim($row->cod_cli);
$rifci    = trim($row->rifci);
$nombre   = trim($row->nombre);
$stotal   = nformat($row->montasa);
$gtotal   = nformat($row->monto);
$impuesto = nformat($row->tasa);
$direccion= wordwrap(trim($row->direc),40,"\n");
$tipo_doc = $row->tipo_doc;
$telefono = trim($row->telefono);
$nomvend  = '';
$factura  = ($tipo_doc=='D')? $row->factura :'';
$base     = nformat($row->montasa);
$exento   = nformat($row->exento);
$observa  = wordwrap(str_replace(',',', ',$row->observa), 100, "\n");
$codigo   = trim($row->codigo);
$descrip  = $row->descrip;
$monto    = nformat($row->monto);

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

$sel=array('b.tipo_doc','b.numero','b.fecha','b.monto','b.abono','b.reten','b.ppago','b.cambio','b.mora','b.reteiva');
$this->db->select($sel);
$this->db->from('smov AS a');
$this->db->join('itccli AS b','a.tipo_doc = b.tipoccli AND a.numero=b.numccli AND a.cod_cli=b.cod_cli');
$this->db->where('a.cod_cli' ,$row->cod_cli);
$this->db->where('a.tipo_doc',$row->tipo_doc);
$this->db->where('a.numero'  ,$row->numero);
$this->db->where('a.fecha'   ,$row->fecha);
$mSQL_2 = $this->db->get();
$detalle  = $mSQL_2->result();
$art_cana = $mSQL_2->num_rows();

if($art_cana==0){
	$sel=array('b.tipo_doc','b.numero','b.fecha','b.totalg AS monto','a.monto AS abono');
	$this->db->select($sel);
	$this->db->from('smov AS a');
	$this->db->join('sfac AS b','b.tipo_doc = a.tipo_ref AND b.numero=a.num_ref AND a.cod_cli=b.cod_cli');
	$this->db->where('a.cod_cli' ,$row->cod_cli);
	$this->db->where('a.tipo_doc',$row->tipo_doc);
	$this->db->where('a.numero'  ,$row->numero);
	$this->db->where('a.fecha'   ,$row->fecha);
	$mSQL_2 = $this->db->get();
	$detalle  = $mSQL_2->result();
	$art_cana = $mSQL_2->num_rows();
}

$separador='Ä';

//************************
//     Encabezado
//************************

$encabezado  = "\n\n\n\n\n";
$encabezado .=" Nombre   : ".chr(15).str_pad($nombre,69).chr(18).str_pad('Nota de credito:',13,' ',0).chr(15).chr(14).$numero.chr(18)."\n";
$encabezado .=" RIF/C.I. : ".str_pad("${rifci}",40)." Fecha      :${fecha}\n";
$encabezado .=" Direccion: ".chr(15).str_pad($direc0.$direc1.' '.$telefono,80).chr(18)."\n";
$encabezado .="Hemos acreditado en su cuenta la suma de Bs... ".chr(14)."${monto}\n".chr(18);
$encabezado .="Imputable a: ${codigo} ${descrip}\n";
$encabezado .="Concepto de: ".chr(15)."${observa}\n".chr(18);
// Fin  Encabezado

//************************
//   Encabezado Tabla
//************************
//$encabezado_tabla  = str_pad('', 80, $separador)."\n";
$encabezado_tabla = CHR(18)." DOCUMENTO          FECHA                                       MONTO/CREDITO ".CHR(18)."\n";
$encabezado_tabla .= str_pad('', 80, $separador)."\n";
//Fin Encabezado Tabla

//************************
//     Pie Pagina
//************************
$pie_final  = CHR(18).str_pad('', 80, $separador)."\n";
$pie_final .= str_pad('MONTO EXENTO: ',                   64,' ',0).chr(15).chr(14).str_pad($exento , 13,' ',0).chr(18)."\n";
$pie_final .= str_pad('BASE IMPONIBLE SEGUN ALICUOTA DEL 12%: ',      64,' ',0).chr(15).chr(14).str_pad($base,    13,' ',0).chr(18)."\n";
$pie_final .= str_pad('MONTO TOTAL DEL IMPUESTO SEGUN ALICUOTA 12%: ',64,' ',0).chr(15).chr(14).str_pad($impuesto,13,' ',0).chr(18)."\n";
$pie_final .= str_pad('MONTO TOTAL: ' ,     64,' ',0).chr(15).chr(14).str_pad($gtotal,  13,'*',0).chr(18)."\n";
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
			echo " ".sprintf('%-13s %-49s %9s',$items->tipo_doc.$items->numero,dbdate_to_human($items->fecha),nformat($items->abono,2));
			echo "\n";
			$lineas++;
		}

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
