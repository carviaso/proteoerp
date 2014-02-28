<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Calcomi extends Controller {

	var $titp  = 'Penalizaci&oacute;n';
	var $tits  = 'Penalizaci&oacute;n';
	var $url   = 'ventas/calcomi/';

	function Calcomi(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id('12C',1);
	}

	function index() {
		redirect($this->url."filteredgrid");
	}

	function filteredgrid(){

		$this->rapyd->load("dataform");

		$filter = new DataForm($this->url."actualiza");

		//$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		//$filter->fechad->db_name     = "fechah";
		//$filter->fechad->dbformat    = 'ymd';
		//$filter->fechad->insertValue = date("Ymd");
		//$filter->fechad->append(' mes/año');
		//
		//$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		//$filter->fechah->db_name     = "fechad";
		//$filter->fechah->dbformat    = 'Ymd';
		//$filter->fechah->insertValue = date("Ymd");
		//$filter->fechah->append(' mes/año');

		$filter->vd = new dropdownField("Vendedor", "vd");
		$filter->vd->db_name = 'vd';
		$filter->vd->clause="where";
		$filter->vd->operator="=";
		$filter->vd->options("SELECT vendedor, CONCAT_WS(' ',vendedor,nombre)a FROM vend ORDER BY vendedor");

		$porcomi1 =$this->datasis->traevalor("PORCOMI1");
		$porcomi2 =$this->datasis->traevalor("PORCOMI2");
		$porcomi3 =$this->datasis->traevalor("PORCOMI3");
		$diacomi1 =$this->datasis->traevalor("DIACOMI1");
		$diacomi2 =$this->datasis->traevalor("DIACOMI2");
		$diacomi3 =$this->datasis->traevalor("DIACOMI3");

		$filter->diacomi1 = new inputField("Hasta ", "diacomi1");
		$filter->diacomi1->css_class='inputnum';
		$filter->diacomi1->size=3;
		$filter->diacomi1->maxlength=3;
		$filter->diacomi1->rule="required";
		$filter->diacomi1->insertValue = $diacomi1;

		$filter->p1 = new freeField("","","Porcentaje ");
		$filter->p1->in="diacomi1";

		$filter->porcomi1 = new inputField("Porcent ", "porcomi1");
		$filter->porcomi1->css_class='inputnum';
		$filter->porcomi1->size=3;
		$filter->porcomi1->maxlength=3;
		$filter->porcomi1->in="diacomi1";
		$filter->porcomi1->rule="required";
		$filter->porcomi1->insertValue = $porcomi1;

		$filter->diacomi2 = new inputField("Hasta", "diacomi2");
		$filter->diacomi2->css_class='inputnum';
		$filter->diacomi2->size=3;
		$filter->diacomi2->maxlength=3;
		$filter->diacomi2->rule="required";
		$filter->diacomi2->insertValue = $diacomi2;

		$filter->p2 = new freeField("","","Porcentaje ");
		$filter->p2->in="diacomi2";

		$filter->porcomi2 = new inputField("Porcent ", "porcomi2");
		$filter->porcomi2->css_class='inputnum';
		$filter->porcomi2->size=3;
		$filter->porcomi2->maxlength=3;
		$filter->porcomi2->in="diacomi2";
		$filter->porcomi2->rule="required";
		$filter->porcomi2->insertValue = $porcomi2;

		$filter->diacomi3 = new inputField("Hasta ", "diacomi3");
		$filter->diacomi3->css_class='inputnum';
		$filter->diacomi3->size=3;
		$filter->diacomi3->maxlength=3;
		$filter->diacomi3->rule="required";
		$filter->diacomi3->insertValue = $diacomi3;

		$filter->p3 = new freeField("","","Porcentaje ");
		$filter->p3->in="diacomi3";

		$filter->porcomi3 = new inputField("Porcent ", "porcomi3");
		$filter->porcomi3->css_class='inputnum';
		$filter->porcomi3->size=3;
		$filter->porcomi3->maxlength=3;
		$filter->porcomi3->in="diacomi3";
		$filter->porcomi3->rule="required";
		$filter->porcomi3->insertValue = $porcomi3;

		$filter->submit('btnsubmit','Calcular');

		$filter->build_form();

		$data['content'] = $filter->output;
		$data['title']   = "<h1>$this->tits</h1>";
		$data['head']    = script('jquery.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function vista($vd='') {
		$this->rapyd->load('datagrid2');

		$select=array('vd','tipo_doc','numero','fecha','vence','pagada','dias','comision','comical','cod_cli','nombre','sepago','comision');
		$grid = new DataGrid2('Lista de '.$this->titp);
		$grid->db->select($select);
		$grid->db->from('sfac');
		$grid->db->where('tipo_doc <>','X');
		if(!empty($vd))
		$grid->db->where('vd',$vd);
		$grid->db->where('sepago','N');
		$grid->db->where('pagada >= fecha');
		$grid->order_by('numero','desc');
		$grid->per_page = 1000;
		$grid->use_function('substr','str_pad','comi');
		$grid->use_function('sta');

		$grid->column('Vendedor'       ,'<#vd#>');
		$grid->column('Tipo'           ,'<colum><#tipo_doc#></colum>');
		$grid->column('N&uacute;mero'  ,'<#numero#>');
		$grid->column('Fecha'          ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
		$grid->column('Vence'          ,'<dbdate_to_human><#vence#></dbdate_to_human>');
		$grid->column('Pagada'         ,'<dbdate_to_human><#pagada#></dbdate_to_human>');
		$grid->column('Dias'           ,'dias'        ,"align='right'");
		$grid->column('Comisi&oacute;n','<number_format><#comision#>|2|,|.</number_format>',"align='right'");
		$grid->column('Comisi&oacute;n Calculada','<nformat><#comical#></nformat>'         ,"align='right'");
		$grid->column('Cliente'        ,'<#cod_cli#>');
		$grid->column('Nombre'         ,'<#nombre#>' );
		//$grid->totalizar('reiva');

		$grid->build();
		$data['content'] = anchor($this->url."filteredgrid",'Atras').$grid->output;
		$data['title']   = "<h1>$this->titp</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function procesar(){
		foreach($_POST['calculada'] as $key=>$value){
			$a=explode("AA",$key);
			$mSQL="UPDATE sfac SET comical=$value WHERE numero='".$a[1]."' AND tipo_doc='".$a[0]."' ";
			$this->db->simple_query($mSQL);
			//exit();
		}
		redirect($this->url."filteredgrid");
	}

	function actualiza(){
		$porcomi1 = $this->input->post('porcomi1');
		$porcomi2 = $this->input->post('porcomi2');
		$porcomi3 = $this->input->post('porcomi3');
		$diacomi1 = $this->input->post('diacomi1');
		$diacomi2 = $this->input->post('diacomi2');
		$diacomi3 = $this->input->post('diacomi3');
		$vd       = $this->input->post('vd'      );

		$this->db->simple_query("UPDATE sfac SET comical=comision WHERE vd='$vd' AND sepago='N' AND pagada>=fecha");
		$this->db->simple_query("UPDATE sfac SET comical=comision * (100-$porcomi1)/100 WHERE vd='$vd' AND sepago='N' AND dias >$diacomi1 AND pagada>=fecha");
		$this->db->simple_query("UPDATE sfac SET comical=comision * (100-$porcomi2)/100 WHERE vd='$vd' AND sepago='N' AND dias >$diacomi2 AND pagada>=fecha");
		$this->db->simple_query("UPDATE sfac SET comical=comision * (100-$porcomi3)/100 WHERE vd='$vd' AND sepago='N' AND dias >$diacomi3 AND pagada>=fecha");

		$this->db->simple_query("UPDATE valores SET valor='$porcomi1' WHERE nombre='porcomi1'");
		$this->db->simple_query("UPDATE valores SET valor='$porcomi2' WHERE nombre='porcomi2'");
		$this->db->simple_query("UPDATE valores SET valor='$porcomi3' WHERE nombre='porcomi3'");
		$this->db->simple_query("UPDATE valores SET valor='$diacomi1' WHERE nombre='diacomi1'");
		$this->db->simple_query("UPDATE valores SET valor='$diacomi2' WHERE nombre='diacomi2'");
		$this->db->simple_query("UPDATE valores SET valor='$diacomi3' WHERE nombre='diacomi3'");
		//exit('as');

		redirect($this->url."vista/$vd");
	}

}
