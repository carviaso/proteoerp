<?php
class Lprueba2 extends Controller {

	var $cargo=0;

	function Lprueba2(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		$this->load->library('PDFReporte2');
		$this->rapyd->load("datafilter");
		$repo =$this->uri->segment(3);
		$esta =$this->uri->segment(4);


//**************************
$filter = new DataFilter("Filtro del Reporte");
$filter->attributes=array('onsubmit'=>'is_loaded()');

$filter->db->select("fecha, numero, tipo_op,concepto, (tipo_op NOT IN ('CH','ND'))*monto as debitos, (tipo_op IN ('CH','ND'))*monto as creditos,' ' as grupo");
$filter->db->from('bmov as a');
$filter->db->join('banc as b','a.codbanc=b.codbanc');
$filter->db->orderby('fecha');

$filter->fecha = new dateonlyField("Desde", "fechad",'m/Y');
$filter->fecha->clause  ='where';
$filter->fecha->db_name ="EXTRACT(YEAR_MONTH FROM fecha)";
$filter->fecha->insertValue = date("Y-m-d");
$filter->fecha->operator="=";
$filter->fecha->dbformat='Ym';
$filter->fecha->size=7;
$filter->fecha->append(' mes/a&ntilde;o');
$filter->fecha->rule = "required";

$filter->banco = new dropdownField("Caja/Banco", "codbanc");
$filter->banco->db_name="a.codbanc";
$filter->banco->option("","");
$filter->banco->options("SELECT codbanc, banco FROM bmov ORDER BY banco ");
$filter->banco->rule = "required";

$filter->buttons("search");
$filter->build();
if($this->rapyd->uri->is_set("search")  AND $filter->is_valid()){
	$mSQL=$filter->db->_compile_select();
	//echo $mSQL;

	$mSALDOANT=$this->datasis->dameval("SELECT SUM(IF(tipo_op IN ('CH', 'ND'),-1,1)*monto) AS saldo FROM bmov WHERE EXTRACT(YEAR_MONTH FROM fecha) <".$filter->fecha->newValue."  AND codbanc = '".$filter->banco->newValue."'");
	$mSALDOACT=0;

	$sobretabla='';
	if (!empty($filter->banco->newValue)) $sobretabla.='Banco: '.$filter->banco->newValue;

	$pdf = new PDFReporte2($mSQL);
	$pdf->setHeadValores('TITULO1');
	$pdf->setSubHeadValores('TITULO2','TITULO3');
	$pdf->setTitulo("Estado de Cuenta");
	$pdf->setSobreTabla($sobretabla);
	$pdf->setSubTitulo("Para la fecha: ".$this->input->post('fechad'));
	$pdf->AddPage();
	$pdf->setTableTitu(8,'Times');
	$pdf->AddCol('fecha' ,20 ,'Fecha' ,'L',8);
	$pdf->AddCol('numero' ,25 ,'N&uacute;mero' ,'L',8);
	$pdf->AddCol('tipo_op' ,15 ,'Tipo Op' ,'L',8);
	$pdf->AddCol('concepto' ,70 ,'Concepto' ,'L',8);
	$pdf->AddCol('debitos' ,30 ,'D&eacute;bitos' ,'R',8);
	$pdf->AddCol('creditos',30 ,'Cr&eacute;ditos' ,'R',8);
	$pdf->setTotalizar('debitos','creditos');
	$pdf->setGrupoLabel("<#grupo#>Saldo Anterior: $mSALDOANT, Saldo Actual: $mSALDOACT");
	$pdf->setGrupo('grupo');
	$pdf->Table();
	$pdf->Output();

}else{
	if (strlen($filter->error_string)) $data["error"]=$filter->error_string;
	$data["filtro"] = $filter->output;
	$data["titulo"] = '<h2 class="mainheader">Estado de Cuenta<h2>';
	$data["head"] = $this->rapyd->get_head();
	$this->load->view('view_freportes', $data);
}

//**************************
//**************************
	}
}
?>
