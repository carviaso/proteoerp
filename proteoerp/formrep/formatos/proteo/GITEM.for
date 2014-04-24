/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
$sinv=array(
	'tabla'   =>'mgas',
	'columnas'=>array(
		'codigo' =>'Código',
		'descrip'=>'descrip'),
	'filtro'  =>array('codigo' =>'Código','descrip'=>'descrip'),
	'retornar'=>array('codigo'=>'codigo'),
	'titulo'  =>'Buscar Artículo');

$iboton=$this->datasis->modbus($sinv);

$filter = new DataFilter("Filtro del Reporte");
$filter->attributes=array('onsubmit'=>'is_loaded()');

$select=array("date_format(fecha,'%d/%m/%Y') ffecha", "a.codigo","a.fecha", "a.numero", "a.proveed", "a.descrip", "sum(a.precio) as precio","sum(a.iva) as iva","sum(importe) as importe", "b.descrip as mgasdesc", "c.nom_grup as grnom","b.cuenta as mgascu", "b.grupo as grcodigo");
$filter->db->select($select);
$filter->db->from('gitser a');
$filter->db->join("mgas as b" ,"a.codigo=b.codigo");
$filter->db->join("grga as c" ,"b.grupo=c.grupo");
$filter->db->groupby('a.codigo');

$filter->fechad = new dateField("Desde", "fechad",'d/m/Y');
$filter->fechah = new dateField("Hasta", "fechah",'d/m/Y');
$filter->fechad->clause  =$filter->fechah->clause="where";
$filter->fechad->db_name =$filter->fechah->db_name="fecha";
$filter->fechad->insertValue = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30,   date("Y")));
$filter->fechah->insertValue = date("Y-m-d");
$filter->fechad->operator=">=";
$filter->fechah->operator="<=";

$filter->grupo = new dropdownField("Grupo", "grupo");
$filter->grupo->db_name = 'b.grupo';
$filter->grupo->option("","");
$filter->grupo->options("SELECT grupo, nom_grup FROM grga ORDER BY nom_grup ");

$filter->codigo = new inputField("Código", "codigo");
$filter->codigo->db_name='a.codigo';
$filter->codigo->clause="where";
$filter->codigo->operator="=";
$filter->codigo ->append($iboton);

$filter->salformat = new radiogroupField("Formato de salida","salformat");
$filter->salformat->options($this->opciones);
$filter->salformat->insertValue ='PDF';
$filter->salformat->clause = '';

$filter->buttons("search");
$filter->build();

if($this->rapyd->uri->is_set("search")){
	$mSQL=$this->rapyd->db->_compile_select();
	echo $mSQL;

	$sobretabla=$subtitu='';
	if (!empty($filter->codigo->newValue)) $sobretabla.='          Codigo: ('.$filter->codigo->newValue.') '.$this->datasis->dameval ('SELECT descrip FROM mgas WHERE codigo="'.$filter->codigo->newValue.'"');
	if (!empty($filter->grupo->newValue))  $sobretabla.='          Grupo: '.$filter->grupo->description;

	$pdf = new PDFReporte($mSQL);
	$pdf->setHeadValores('TITULO1');
	$pdf->setSubHeadValores('TITULO2','TITULO3');
	$pdf->setTitulo("Listado de Gastos por Grupo");
	$pdf->setSubTitulo("Desde la fecha: ".$_POST['fechad']." Hasta ".$_POST['fechah']);
	$pdf->setSobreTabla($sobretabla,7);
	$pdf->AddPage();
	$pdf->setTableTitu(10,'Times');

	$pdf->AddCol('codigo'  ,20,'Código'     ,'L',8);
	$pdf->AddCol('mgasdesc',70,'Descripción','L',8);
	$pdf->AddCol('precio'  ,30,'Monto'      ,'R',8);
	$pdf->AddCol('iva'     ,30,'Impuesto'   ,'R',8);
	$pdf->AddCol('importe' ,30,'Importe'    ,'R',8);

	$pdf->setGrupoLabel('(<#grcodigo#>) <#grnom#>');
	$pdf->setGrupo('grcodigo');
	$pdf->setTotalizar('precio','iva','importe');
	$pdf->Table();
	$pdf->Output();

}else{
	$data["filtro"] = $filter->output;
	$data["titulo"] = '<h2 class="mainheader">Gastos por Grupo</h2>';
	$data["head"] = $this->rapyd->get_head();
	$this->load->view('view_freportes', $data);
}
