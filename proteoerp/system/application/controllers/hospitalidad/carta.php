<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class Carta extends validaciones {
	
	function Carta(){
		parent::Controller(); 
		$this->load->library("rapyd");

	}
	
	function index(){
	  $this->datasis->modulo_id(806,1);
		redirect("hospitalidad/carta/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		//$this->rapyd->uri->keep_persistence();
	
		$filter = new DataFilter("Filtro de Carta", 'menu');
	 	
		$filter->codigo = new inputField("C&oacute;digo", "codigo");	
		$filter->codigo->size=15;		
		
		$filter->descri1 = new inputField("Descripci&oacute;n", "descri1");
		$filter->descri1->size=35;

		$filter->grupo = new dropdownField("Grupo","grupo");
		$filter->grupo->option("","");
		$filter->grupo->options("SELECT grupo,descri1 FROM grme ORDER BY grupo ");
		$filter->grupo->style="width:180px";

		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('hospitalidad/carta/dataedit/show/<#codigo#>','<#codigo#>');
			
		$grid = new DataGrid("Lista de Carta");
		$grid->order_by("codigo","asc");
		$grid->per_page = 10;

		$grid->column("C&oacute;digo",$uri);
		$grid->column("Descripci&oacute;n ","descri1");
		$grid->column("Grupo","descgru");
		$grid->column("Base","base");
		$grid->column("Impuesto", "impuesto");
		$grid->column("Precio", "precio");
		$grid->column("Servicio", "servicio");
	
		$grid->add("hospitalidad/carta/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Carta</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit(){ 
		$this->rapyd->load("dataedit");
		
		$edit = new DataEdit("Carta", "menu");
		$edit->back_url = site_url("hospitalidad/carta/filteredgrid");
		
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
		
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_insert');	
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
					
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->rule="trim|required|callback_chexiste";
		$edit->codigo->mode = "autohide";
		$edit->codigo->size=11;
		$edit->codigo->maxlength=8;
		
		$edit->descri1 = new inputField("Descripci&oacute;n", "descri1");	
		$edit->descri1->rule="trim|required";
		$edit->descri1->size=50;
		$edit->descri1->maxlength=40;
		
		$edit->descri2 = new inputField("", "descri2");
		$edit->descri2->rule="trim";	
		$edit->descri2->size=50;
		$edit->descri2->maxlength=40;
		
		$edit->barras = new inputField("Barras", "barras");
		$edit->barras->rule="trim";
		$edit->barras->size=15;
		$edit->barras->maxlength=15;
		
		$edit->grupo = new dropdownField("Grupo","grupo");
		$edit->grupo->option("","");
		$edit->grupo->options("SELECT grupo,descri1 FROM grme ORDER BY grupo ");
		$edit->grupo->style="width:180px";
		
		$edit->costo = new inputField("Costo", "costo");
		$edit->costo->size=25;
		$edit->costo->css_class='inputnum';
		$edit->costo->rule='trim|numeric';
		
	  $edit->base = new inputField("Precio Base", "base");
		$edit->base->size=20;
		$edit->base->maxlength=17;
		$edit->base->css_class='inputnum';
		$edit->base->rule='trim|numeric';
				
		$edit->impuesto = new inputField("Impuesto %", "impuesto");
		$edit->impuesto->size=20;
		$edit->impuesto->maxlength=17;
		$edit->impuesto->css_class='inputnum';
		$edit->impuesto->rule='trim|numeric';
		
		$edit->precio = new inputField("Precio al p&uacute;blico", "precio");
		$edit->precio->maxlength=17;
		$edit->precio->size=20;
		$edit->precio->css_class='inputnum';
		$edit->precio->rule='trim|numeric';
		
		$edit->editar = new dropdownField("Editar", "editar");
		$edit->editar->option("N","No");  
		$edit->editar->option("S","Si");
		$edit->editar->rule = "required";
		
		$edit->hfelizi = new inputField("Inicio", "hfelizi");	
		$edit->hfelizi->group='Hora feliz';
		$edit->hfelizi->size=8;
		$edit->hfelizi->append('hh:mm:ss');
		$edit->hfelizi->rule='trim|callback_chhora';
		
		$edit->hfelizf = new inputField("Final", "hfelizf");	
		$edit->hfelizf->group='Hora feliz';
		$edit->hfelizf->append('hh:mm:ss');
		$edit->hfelizf->size=8;
		$edit->hfelizi->rule='trim|callback_chhora';
		
		$edit->activard = new inputField("Inicio", "activard");	
		$edit->activard->group='Hora Activa';
		$edit->activard->append('hh:mm:ss');
		$edit->activard->size=8;
		$edit->activard->rule='trim|callback_chhora';
		
		$edit->activarh = new inputField("Final", "activarh");	
		$edit->activarh->group='Hora Activa';
		$edit->activarh->append('hh:mm:ss');
		$edit->activarh->size=8;
		$edit->activarh->rule='trim|callback_chhora';
			
		$edit->ubica = new inputField("Impresora de Comanda", "ubica");	
		$edit->ubica->size=11;
		$edit->ubica->maxlength=8;
		$edit->ubica->rule='trim';
		
		$edit->departamento = new inputField("Departamento", "departa");	
		$edit->departamento->size=11;
		$edit->departamento->maxlength=8;
		$edit->departamento->rule='trim';
		
		$edit->cuenta = new inputField("Cuenta Contable", "cuenta");	
		$edit->cuenta->size=18;
		$edit->cuenta->maxlength=15;
		$edit->cuenta->rule='trim|existecpla';
		$edit->cuenta->append($bcpla);
		
		$dias=$edit->_dataobject->get('activardia');
		$s=array();
		
		for($i=0;$i<8;$i++){
			//echo 'hola '.strrchr($dias,strval($i));
			if(strrchr($dias,strval($i))===FALSE){
				$s[]=0;
			}else{
				$s[]=1;
			}
		}
		$s0 = form_checkbox('s0', '0', $s[0]);//new checkboxField("Lunes"    , "s0", "0",'-1');
		$s1 = form_checkbox('s1', '1', $s[1]);//new checkboxField("Martes"   , "s1", "1",'-1');
		$s2 = form_checkbox('s2', '2', $s[2]);//new checkboxField("Miercoles", "s2", "2",'-1');
		$s3 = form_checkbox('s3', '3', $s[3]);//new checkboxField("Jueves"   , "s3", "3",'-1'); 
		$s4 = form_checkbox('s4', '4', $s[4]);//new checkboxField("Viernes"  , "s4", "4",'-1');
		$s5 = form_checkbox('s5', '5', $s[6]);//new checkboxField("Sabado"   , "s5", "5",'-1');
		$s6 = form_checkbox('s6', '6', $s[7]);//new checkboxField("Domingo"  , "s6", "6",'-1');
			
		$s0='Lunes '.$s0;
		$s1='Martes '.$s1;
		$s2='Miercoles '.$s2;
		$s3='Jueves '.$s3;
		$s4='Viernes '.$s4;
		$s5='Sabado '.$s5;
		$s6='Domingo '.$s6;
		
		$edit->html = new containerField("Dias",$s0.$s1.$s2.$s3.$s4.$s5.$s6);
		$edit->html->cols = 50;
		$edit->html->rows = 10;
		$edit->html->when = array("create","modify");  
		
		
		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();
		
    $data['content'] = $edit->output;           
    $data['title']   = "<h1>Carta</h1>";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	
	function _pre_insert($do) {
		$acu='';
		for($i=0;$i<7;$i++){
			$sema=$this->input->post("s$i");
			if($sema!==FALSE)
				$acu.=$sema;
		}
		$do->set('activardia', $acu);
		return True;
	}
	function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('descri1');
		logusu('menu',"CARTA $codigo DESCRIPCION  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo'); 
		$nombre=$do->get('descri1');
		logusu('menu',"CARTA $codigo DESCRIPCION  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo'); 
		$nombre=$do->get('descri1');
		logusu('menu',"CARTA $codigo DESCRIPCION $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM menu WHERE codigo='$codigo'");
		if ($chek > 0){
			$nombre=$this->datasis->dameval("SELECT descri1 FROM menu WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para la carta $nombre");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	function instalar(){
		$mSQL="ALTER TABLE `menu` ADD activard CHAR(5) DEFAULT '00:00'";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `menu` ADD activarh CHAR(5) DEFAULT '99:99'";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `menu` ADD activardia CHAR(7) DEFAULT '0123456'";
		$this->db->simple_query($mSQL);
	} 
}   
?>