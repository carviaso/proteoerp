<?php
class Valores extends Controller {
	var $mModulo='VALORES';
	var $titp='Variables del Sistema';
	var $tits='Variables del Sistema';
	var $url ='supervisor/valores/';

	function Valores(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		if(!$this->datasis->iscampo('valores','id') ) {
			$this->db->simple_query('ALTER TABLE valores DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE valores ADD UNIQUE INDEX nombre (nombre)');
			$this->db->simple_query('ALTER TABLE valores ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!$this->datasis->iscampo('tmenus','proteo')){
			$this->db->simple_query('ALTER TABLE tmenus ADD COLUMN proteo VARCHAR(250) NULL ');
		}

		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	// Layout en la Ventana
	//
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = '';

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$WestPanel = '';
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'));

		//$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados'] = $this->datasis->listados('VALORES', 'JQ');
		$param['otros']    = $this->datasis->otros('VALORES', 'JQ');
		$param['tema1']     = 'darkness';
		$param['anexos']    = 'anexos1';
		$param['bodyscript'] = $bodyscript;
		$param['tabs'] = false;
		$param['encabeza'] = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'true';

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 60,
			'editable' => 'false',
			'search'   => 'false',
			'hidden'   => 'true'
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 170,
			'edittype'      => "'text'"
		));

		$grid->addField('valor');
		$grid->label('Valor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 280,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:30, maxlength: 200 }',
		));

		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 650,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:30, maxlength: 200 }',
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('390');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('
			closeAfterEdit:true, mtype: "POST", width: 520, height:200, closeOnEscape: true, top: 50, left:20, recreateForm:true,
			afterSubmit: function(a,b) {
				if (a.responseText.length > 0)
					$.prompt(a.responseText);
				return [true, a ];
			},
			beforeShowForm: function(frm) {
					$(\'#nombre\').attr(\'readonly\',\'readonly\');
			}
		');

		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:200, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];}');


		$grid->setAfterSubmit('$.prompt(\'Respuesta:\'+a.responseText); return [true, a ];');

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('valores');

		$response   = $grid->getData('valores', array(array()), array(), false, $mWHERE, 'nombre' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('valores', $data);
			}
			echo "Registro Agregado";

		} elseif($oper == 'edit') {
			$nombre = $this->datasis->dameval("SELECT nombre FROM valores WHERE id=$id");
			unset($data['nombre']);
			$this->db->where('id', $id);
			$this->db->update('valores', $data);
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			$nombre = $this->datasis->dameval("SELECT nombre FROM valores WHERE id=$id");
			$this->db->simple_query("DELETE FROM valores WHERE id=$id ");
			logusu('valores',"Registro $nombre ELIMINADO");
			echo "Registro Eliminado";
		};
	}
}

/*
class valores extends Controller {
	function valores(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->datasis->modulo_id('902',1);
	}

	function index(){
		redirect("supervisor/valores/filteredgrid");
	}

	function filteredgrid(){

		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro de Valores", 'valores');

		$filter->nombre = new inputField("Nombre","nombre");
		$filter->nombre->size=35;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('supervisor/valores/dataedit/show/<#nombre#>','<#nombre#>');

		$grid = new DataGrid("Lista de Valores");
		$grid->order_by("nombre","asc");

		$grid->column("Nombre",$uri );
		$grid->column("Valor", "valor");
		$grid->column("Descripci&oacute;n","descrip");

		$grid->add("supervisor/valores/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Valores</h1>";
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Valor","valores");
		$edit->back_url = site_url("supervisor/valores/filteredgrid");

		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->rule = "required";
		$edit->nombre->mode = "autohide";
		$edit->nombre->size=35;

		$edit->valor = new inputField("Valor", "valor");
		$edit->valor->size=45;

		$edit->descrip = new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size=45;

		$edit->buttons("modify", "save", "undo", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Valores</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function moneda(){
		$mSQL="INSERT INTO `valores` (`nombre`, `valor`, `descrip`) VALUES ('MONEDA', '$', 'Tipo de Moneda con la cual trabaja la empresa') ;";
		$this->db->simple_query($mSQL);
	}
}
*/
