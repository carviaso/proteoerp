<?php
$_arch_nombre='sfac.fis';

$tipo_iva = 'I'; //'I' manda iva incluido, 'A' manda el iva adicional

if(count($parametros)==0) show_error('Faltan parametros');
$id   = $parametros[0];
$dbid = $this->db->escape($id);

$con=$this->db->query('SELECT tasa,redutasa,sobretasa FROM civa ORDER BY fecha desc LIMIT 1');
if($con->num_rows() > 0){
	$t=$con->row('tasa');$rt=$con->row('redutasa');$st=$con->row('sobretasa');
}else{
	show_error('Debe cargar la tabla de IVA.');
}

$mSQL = "SELECT If(a.referen='E','CONTADO',IF( a.referen='C','CREDITO',IF(a.referen='M','MIXTO','Pendiente'))) AS referen,
	a.tipo_doc,a.numero,a.cod_cli,a.nombre,a.rifci,CONCAT(a.direc,a.dire1) AS direccion,a.factura,a.fecha,a.vence,a.vd,
	a.iva,a.totals,a.totalg, a.exento,b.nombre AS nomvend,tipo_doc, a.numero,a.peso,c.telefono,a.observa
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
$direccion= trim($row->direccion);
$tipo_doc = $row->tipo_doc;
$referen  = $row->referen;
$telefono = trim($row->telefono);
$nomvend  = $row->nomvend;
$factura  = ($tipo_doc=='D')? $row->factura :'';
$vd       = $row->vd;

$dbtipo_doc = $this->db->escape($tipo_doc);
$dbnumero   = $this->db->escape($numero);

if($tipo_doc == 'F'){
	$doc  = '';
	$tasa = '!';
	$redu = '"';
	$adic = '#';
	$exeb = ' ';
}elseif ($tipo_doc == 'D'){
	$doc  = 'd';
	$tasa = '1';
	$redu = '2';
	$adic = '3';
	$exeb = '0';
}else{
	show_error('Factura anulada o no cobrada');
}

$lineas = 0;
$uline  = array();

$mSQL="SELECT
	codigoa AS codigo,desca,cana,preca,tota AS importe,iva,detalle
FROM sitems
WHERE numa=${dbnumero} AND tipoa=${dbtipo_doc}";

$mSQL_2 = $this->db->query($mSQL);
$detalle  = $mSQL_2->result();

$encab  = 'CLIENTE: '.str_pad($cod_cli,9).' REF: '.$numero."\n";
$encab .= $nombre."\n";
$encab .= 'RIF:'.str_pad($rifci,14).' Vende: '.$vd."\n";
$encab .= $direccion."\n";
$encab .= $row->observa;

$encab  = wordwrap($encab,40,"\n");
$arr_lin= explode("\n",$encab);
foreach($arr_lin as $i=>$linea){
	$o=$i+1;
	if(strlen(trim($linea))>0){
		echo 'i'.str_pad($o,2,'0', STR_PAD_LEFT).$linea."\n";
	}
}

foreach ($detalle AS $items){

	//Tasa
	echo $doc;
	if($items->iva == $t){
		echo $tasa;
	}elseif($items->iva == $rt){
		echo $redu;
	}elseif($items->iva == $st){
		echo $adic;
	}else{
		echo $exeb;
	}

	//precio y cantidad
	if($tipo_iva=='I'){
		$precio = number_format($items->preca*((100+$items->iva)/100),2,'','');
	}else{
		$precio = number_format($items->preca,2,'','');
	}
	$cana   = number_format($items->cana,3,'','');
	echo str_pad($precio,10,'0',0).str_pad($cana,8,'0',0);

	//Descripcion
	$descrip = trim($items->desca);
	echo substr($descrip,0,38)."\n";

	$ddetall = trim($items->detalle);

	if(strlen($ddetall) > 0){
		$descrip = $ddetall;
		$descrip = str_replace("\r",'',$descrip);
		$descrip = str_replace(array("\t"),' ',$descrip);
		$descrip = wordwrap($descrip,40,"\n");
		$arr_des = explode("\n",$descrip);

		foreach($arr_des as $linea){
			if(strlen($linea)>0)
				echo "@${linea}\n";
		}
	}
}
echo "101\n";
?>
