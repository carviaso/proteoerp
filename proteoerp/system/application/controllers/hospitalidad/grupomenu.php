<?php
class grupomenu extends Controller {
	function grupomenu(){
		parent::Controller(); 
		$this->load->library("rapyd");
		//I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
		define ("THISFILE",   APPPATH."controllers/hospitalidad/". $this->uri->segment(2).EXT);
	}

	function index(){
		$this->datasis->modulo_id(807,1);
		redirect("hospitalidad/grupomenu/filteredgrid");
	}

	function filteredgrid(){

		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		$filter = new DataFilter("Filtro de Grupo Menu", 'grme');
				
		$filter->grupo = new inputField("Grupo","grupo");
		$filter->grupo->size=5;
		
		$filter->descrip = new dropdownField("Descripci&oacute;n","descri1");
		$filter->descrip->option("","");
		$filter->descrip->options("SELECT grupo,descri1 FROM grme ORDER BY grupo ");
		$filter->descrip->style="width:180px";

		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('hospitalidad/grupomenu/dataedit/show/<#grupo#>','<#grupo#>');
		
		$grid = new DataGrid("Lista de Grupo Menu");
		$grid->order_by("grupo","asc");
		$grid->per_page = 10;

		$grid->column("Grupo",$uri );
		$grid->column("Descripci&oacute;n","descri1");
		$grid->column("Cuenta", "cuenta");
				
		$grid->add("hospitalidad/grupomenu/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Grupo Menu</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
}

	function dataedit(){ 
		$this->rapyd->load("dataedit");
		$qformato=$this->qformato=$this->datasis->formato_cpla();
		
		$mCPLA=array(
		'tabla'   =>'cpla',
		'columnas'=>array(
		'codigo' =>'C&oacute;digo',
		'descrip'=>'Descripci&oacute;n'),
		'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
		'retornar'=>array('codigo'=>'cuenta'),
		'titulo'  =>'Buscar Cuenta',
		'where'=>"codigo LIKE \"$qformato\"",
		);
				
		$bcpla =$this->datasis->modbus($mCPLA);
		
		$edit = new DataEdit("Grupo Menu", "grme");
		$edit->back_url = site_url("hospitalidad/grupomenu/filteredgrid");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
				
		$edit->grupo = new inputField("Grupo", "grupo");
		$edit->grupo->rule="trim|required|callback_chexiste";
		$edit->grupo->size=8;
		$edit->grupo->maxlength=5;
		
		$edit->descri1 = new dropdownField("Descripci&oacute;n","descri1");
		$edit->descri1->option("","");
		$edit->descri1->options("SELECT grupo,descri1 FROM grme ORDER BY grupo ");
		$edit->descri1->style="width:180px";
		 
		$edit->cuenta = new inputField("Cuenta contable", "cuenta");
		$edit->cuenta->rule='trim|existecpla';
		$edit->cuenta->append($bcpla);
		$edit->cuenta->size=20;
		$edit->cuenta->maxlength =15; 
		
		$edit->observ = new inputField("Observaci&oacute;n", "observ1");
		$edit->observ->rule='trim';
		$edit->observ->size=45;                        
		
		$edit->buttons("modify", "save", "undo", "back");		
		$edit->build();
			
    $data['content'] = $edit->output;           
    $data['title']   = "<h1>Grupo Menu</h1>";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	function _post_insert($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grme',"GRUPO DE INVENTARIO $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grme',"GRUPO DE INVENTARIO $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grme',"GRUPO DE INVENTARIO $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('grupo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM grup WHERE grupo='$codigo'");
		if ($chek > 0){
			$grupo=$this->datasis->dameval("SELECT nom_grup FROM grup WHERE grupo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el grupo $grupo");
			return FALSE;
		}else {
  		return TRUE;
	 }
	}
}
?>