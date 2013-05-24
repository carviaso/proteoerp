<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class Servus extends validaciones {

	function Servus(){
		parent::Controller(); 
		$this->load->library("rapyd");
		//I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
		define ("THISFILE",   APPPATH."controllers/hotel/". $this->uri->segment(2).EXT);
	}

	function index(){
		$this->datasis->modulo_id(813,1);
		redirect("hospitalidad/servus/filteredgrid");
	}

	function filteredgrid(){

		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();
		
		$filter = new DataFilter("Filtro de Servicios", 'hgas');
		
		$filter->cod_gras = new inputField("Servicio","cod_gas");
		$filter->cod_gras->size=7;
		
		$filter->descrip = new inputField("Descripcion","descrip");
		$filter->descrip->size=40;
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('hospitalidad/servus/dataedit/show/<#cod_gas#>','<#cod_gas#>');

		$grid = new DataGrid("Lista de Servicios");
		$grid->order_by("cod_gas","asc");
		$grid->per_page = 7;
		
		$grid->column("C&oacute;digo", $uri);
		$grid->column("Descripci&oacute;n","descrip","descrip");
		$grid->column("Monto Fijo Bs.","monto","align='right'");
		$grid->column("IVA","iva","align='right'");
		$grid->column("Impuesto Tur&iacute;stico","imptur","align='right'");
		$grid->column("Porcentaje","porcent","align='right'");
		$grid->column("Aplicaci&oacute;n Diaria (S/N)","aplica","align='center'");
		
		$grid->add("hospitalidad/servus/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Servicios</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit(){ 
		$this->rapyd->load("dataedit");
		
		$edit = new DataEdit("Servicio", "hgas");
		$edit->back_url = site_url("hospitalidad/servus/filteredgrid");
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
		
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->script($script, "create");
		$edit->script($script, "modify");
						
		$edit->cod_gas = new inputField("C&oacute;digo", "cod_gas");
		$edit->cod_gas->size=7;
		$edit->cod_gas->maxlength=4;
		$edit->cod_gas->rule = "trim|required|callback_chexiste";
		$edit->cod_gas->mode="autohide";
		
		$edit->descrip = new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size=50;
		$edit->descrip->maxlength=40;
		$edit->descrip->rule = "trim|strtoupper|required";
		
		$edit->monto = new inputField("Monto Fijo Bs.", "monto");
		$edit->monto->size=20;
		$edit->monto->maxlength=17;
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='trim|numeric';
		
		$edit->porcent = new inputField("Porcentaje", "porcent");
		$edit->porcent->size=25;	
		$edit->porcent->maxlength=17;
		$edit->porcent->css_class='inputnum';
		$edit->porcent->rule='trim|numeric';
						
		$edit->iva = new inputField("IVA %", "iva");
		$edit->iva->size=10;
		$edit->iva->maxlength=7;
		$edit->iva->css_class='inputnum';
		$edit->iva->rule='trim|numeric';
		
		$edit->imptur = new inputField("Impuesto Tur&iacute;stico", "imptur");
		$edit->imptur->size=10;		
		$edit->imptur->maxlength=7;
		$edit->imptur->css_class='inputnum';
		$edit->imptur->rule='trim|numeric';

		$edit->aplica = new dropdownField("Aplicaci&oacute;n Diaria(S/N)", "aplica");
		$edit->aplica->option("","");
		$edit->aplica->options(array("S"=> "Si","N"=>"No"));
		$edit->aplica->style="width:70px";	
		
		$edit->cargo = new dropdownField("Cargo(S/N)", "cargo");
		$edit->cargo->option("","");
		$edit->cargo->options(array("S"=> "Si","N"=>"No"));
		$edit->cargo->style="width:70px";
		
		$edit->cuenta = new inputField("Cuenta", "cuenta");
		$edit->cuenta->size=18;		
		$edit->cuenta->maxlength=15;	
		$edit->cuenta->rule='trim|existecpla';
		$edit->cuenta->append($bcpla);
		
		$edit->titulo = new inputField("T&iacute;tulo", "titulo");
		$edit->titulo->size=35;		
		$edit->titulo->rule="trim";
		$edit->titulo->maxlength=30;
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output;           
    $data['title']   = "<h1>Servicios</h1>";        
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	function _post_insert($do){
		$codigo=$do->get('cod_gas');
		$nombre=$do->get('descrip');
		logusu('hgas',"SERVICIO $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('cod_gas');
		$nombre=$do->get('descrip');
		logusu('hgas',"SERVICIO $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('cod_gas');
		$nombre=$do->get('descrip');
		logusu('hgas',"SERVICIO $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('cod_gas');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM hgas WHERE cod_gas='$codigo'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT descrip FROM hgas WHERE cod_gas='$codigo'");
			$this->validation->set_message('chexiste',"El servicio $codigo ya existe con el nombre $nombre");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
}
?>