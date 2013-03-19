<?php
$base_process_uri= $this->rapyd->uri->implode_uri("base_uri","gfid","orderby");

$filter = new DataForm($this->rapyd->uri->add_clause($base_process_uri, "search"));
$filter->title('Elija un formato de salida');
$filter->attributes=array('onsubmit'=>'is_loaded()');

$filter->salformat = new radiogroupField("Formato de salida","salformat");
$filter->salformat->options($this->opciones);
$filter->salformat->insertValue ='PDF';
$filter->salformat->clause = '';

$filter->submit("btnsubmit","Descargar");
$filter->build_form();

//$mContrato = $this->datasis->dameval("SELECT CONCAT(b.codigo,' ',b.nombre) FROM prenom a JOIN noco b ON a.contrato=b.codigo LIMIT 1");
if($this->rapyd->uri->is_set("search")){

	$mSQLs=array();
	$sql=$this->db->query("SHOW TABLES LIKE 'PRENOM%'");
	foreach($sql->result_array() AS $row){
		foreach($row AS $key=>$tabla){

			$fecha =$this->datasis->dameval("SELECT DATE_FORMAT(fecha, '%d/%m/%Y') FROM ${tabla} ORDER BY fecha desc limit 1");
			$valor =$this->datasis->dameval("select IF(b.tipo='Q','15',IF(b.tipo='S','7',IF(b.tipo='M','30','0')))as periodo FROM ${tabla} AS a JOIN noco as b on a.contrato=b.codigo limit 1");
			$fechad=$this->datasis->dameval("SELECT SUBDATE('$fecha', INTERVAL  '$valor' DAY) ");

			$mSQL  = "(SELECT b.divi, b.depto, a.codigo, CONCAT(RTRIM(b.nombre),' ' ,b.apellido)  nombre, CONCAT(b.divi, b.depto) dividep ";
			$mSQL .= ", SUM(a.valor) totalnom, ";
			$mSQL .= "(SELECT SUM(IF(d.monto-d.abonos-c.cuota>0, c.cuota, d.monto-d.abonos )) neto, b.cuentab";
			$mSQL .= "FROM pres c JOIN smov d ON  c.cod_cli=d.cod_cli AND c.tipo_doc=d.tipo_doc AND c.numero=d.numero WHERE c.codigo=a.codigo AND c.apartir<=a.fecha ) prestamo, e.descrip dividesc, f.depadesc ";
			$mSQL .= "FROM ${tabla} a JOIN pers b ON a.codigo=b.codigo ";
			$mSQL .= "LEFT JOIN divi e ON b.divi  = e.division ";
			$mSQL .= "LEFT JOIN depa f ON b.depto =  f.departa ";
			$mSQL .= "WHERE MID(a.concepto,1,1) != '9' ";
			$mSQL .= "GROUP BY a.codigo)";

			$mSQLs[] = $mSQL;
		}
	}

	$mSQL= implode("\n UNION ALL \n",$mSQLs)." ORDER BY dividep, codigo";

	//echo $mSQL; exit();

	$pdf = new PDFReporte($mSQL,'L');
	$pdf->setHeadValores('TITULO1');
	$pdf->setSubHeadValores('TITULO2','TITULO3');
	$pdf->setTitulo('Nominas');
	$pdf->setSubTitulo("");

	$pdf->AddPage();
	$pdf->setTableTitu(6,'Times');

	$pdf->AddCol('codigo'   , 13, array('Código',' ')                     ,'L', 9);
	$pdf->AddCol('nombre'   , 42, array('Nombre del ','Trabajador ')      ,'L' ,9);
	$pdf->AddCol('cuentab'  , 40, array('Cuenta',' ')                     ,'L' ,9);
	$pdf->AddCol('totalnom' , 40, array('TOTAL','NOMINA')                 ,'R', 9);
	$pdf->AddCol('prestamo' , 40, array('CUOTA DE ','PRESTAMO')           ,'R', 9);
	$pdf->AddCof('<#totalnom#>-<#prestamo#>', 20,  array('NETO','A PAGAR'),'R', 9);

	$pdf->setTotalizar('totalnom','prestamo','__cC0');
	$pdf->setGrupoLabel(' (<#dividep#>) <#dividesc#> <#depadesc#>');
	$pdf->setGrupo('depto');
	$pdf->Table();

	$pdf->Output();
}else{
	$data["filtro"] = $filter->output;
	$data["titulo"] = '<h2 class="mainheader">CONTRATO </h2>';
	$data["head"] = $this->rapyd->get_head();
	$this->load->view('view_freportes', $data);
}

?>
