<?php 
include('marc.php');
class Unidad extends Controller{
	var $genesal=true;
	var $url ='inventario/unidad/';
	var $titp = 'Unidades';
	var $tits = 'Unidades';

	function unidad(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('30C',1);
	}

	function index(){
		//redirect("inventario/unidad/filteredgrid");
		if ( !$this->datasis->iscampo('unidad','id') ) {
			$this->db->simple_query('ALTER TABLE unidad DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE unidad ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE unidad ADD UNIQUE INDEX unidades (unidades)');
		}
		redirect($this->url.'jqdatag');
	}


	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
		array("id"=>"fedita",  "title"=>"Agregar/Editar Registro")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		//$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('UNIDAD', 'JQ');
		$param['otros']       = $this->datasis->otros('UNIDAD', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}


	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = "";
		return $bodyscript;
	}


	//******************************************************************
	//
	//
	function defgrid($deployed = false ){
		//$mecho = $this->uri->segment($this->uri->total_segments());
		
		$url ='inventario/unidad/';
		$titp = 'Unidades';

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('ID');
		$grid->params(array('align' => "'center'",
							'width' => 20,
							'editable' => 'false',
							'editoptions' => '{readonly:true,size:10}'
			)
		);

		$grid->addField('unidades');
		$grid->label('Nombre');
		$grid->params(array('width' => 180,
							'editable' => 'true',
							'edittype' => "'text'",
							'editrules' => '{required:true}'
			)
		);

		#show paginator
		$grid->showpager(true);
		
		$grid->setViewRecords(true);

		#width
		$grid->setWidth('250');
		#height
		$grid->setHeight('260');
		#table title
		$grid->setTitle($titp);

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setView(false);
		$grid->setRowNum(20);
		//$grid->setRowList('[]');
		#export buttons
		//$grid->setPdf(true,array('title' => 'Test pdf'));

		#GET url
		$grid->setUrlget(site_url($url.'getdata/'));

		#Set url
		$grid->setUrlput(site_url($url.'setdata/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Get data result as json
	*/
	function getData()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('unidad');

		$response   = $grid->getData('unidad', array(array()), array(), false, $mWHERE, 'unidades' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Put information
	*/
	function setData()
	{
		$this->load->library('jqdatagrid');
		$oper  = $this->input->post('oper');
		$id    = $this->input->post('id');
		$data  = $_POST;
		$check = 0;

		$id    = str_replace('jqg','',$id);

		// ver si puede borrar
		if ($oper == 'del') {
			// si tiene registros no puede borrar
			$id   = $this->input->post('id');
			$mSQL = "SELECT COUNT(*) FROM sinv a JOIN unidad b ON a.unidad=b.unidades WHERE b.id=$id";
			if ($this->datasis->dameval($mSQL) == 0 ){
				$grid      = $this->jqdatagrid;
				$response  = $grid->operations('unidad','id');
				echo 'Registro Borrado!!!';
			} else {
				echo 'No se puede borrar, existen productos con esta unidad';
			}			
		} else {
			$grid       = $this->jqdatagrid;
			$response   = $grid->operations('unidad','id');
			echo 'Registro Actualizado';
		}
	}


	//******************************************************************
	//
	//
	function dataedit(){
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Unidad","unidad");
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->back_url = site_url("inventario/unidad/filteredgrid");

		$edit->unidades =  new inputField("Unidad",'unidades');
		$edit->unidades ->size = 15;
		$edit->unidades ->maxlength=30;
		$edit->unidades ->rule = "trim|strtoupper|required";

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		if($this->genesal){
			$edit->build();
			$data['content'] = $edit->output;
			$data['title']   = heading('Unidad');
			$data['head']    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}else{
			$edit->on_save_redirect=false;
			$edit->build();

			if($edit->on_success()){
				echo 'Pedido Guardado';
			}elseif($edit->on_error()){
				echo html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
		}
	}

	function modifica(){
		$valor = $this->uri->segment($this->uri->total_segments());
		$campo = $this->uri->segment($this->uri->total_segments()-1);
		$grupo = $this->uri->segment($this->uri->total_segments()-2);
		if ( $grupo == '__VACIO__') $grupo = "";
		// Si ya exsite se borra
		$mSQL = "SELECT COUNT(*) FROM unidad WHERE unidades='".addslashes($valor)."' ";
		if ( $this->datasis->dameval($mSQL) == 0 ) {
			$mSQL = "UPDATE unidad SET ".$campo."='".addslashes($valor)."' WHERE unidades='".addslashes($grupo)."' ";
			$this->db->simple_query($mSQL);
		};
		$mSQL = "UPDATE sinv SET unidad='".addslashes($valor)."' WHERE unidad='".addslashes($grupo)."' ";

		$this->db->simple_query($mSQL);
		
		echo "$valor $campo $grupo";
	}
}
