<?php require_once(BASEPATH.'application/controllers/validaciones.php');
class Grcl extends Controller {
	var $mModulo='GRCL';
	var $titp='Grupo de Clientes';
	var $tits='Grupo de Clientes';
	var $url ='ventas/grcl/';

	function Grcl(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_nombre( $modulo, $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 750, 500, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = '';

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$WestPanel = $grid->deploywestp();

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor("TITULO1"));

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('GRCL', 'JQ');
		$param['otros']       = $this->datasis->otros('GRCL', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;

		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'true';
		$link   = site_url('ajax/buscacpla');

		$grid  = new $this->jqdatagrid;

		$grid->addField('grupo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 4 }',
		));

		$grid->addField('gr_desc');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 25 }',
		));

		$grid->addField('clase');
		$grid->label('Clase');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: {"C":"Clientes","O":"Otros","I":"Internos" }, style:"width:100px" }'
		));

		$grid->addField('cuenta');
		$grid->label('Cuenta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{'.$grid->autocomplete($link, 'cuenta','cucucu','<div id=\"cucucu\"><b>"+ui.item.descrip+"</b></div>').'}',
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true,mtype: "POST",width: 420,height:200,closeOnEscape: true,top: 50,left:20,recreateForm:true,afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});}');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 420, height:200, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

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
	function getdata()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('grcl');

		$response   = $grid->getData('grcl', array(array()), array(), false, $mWHERE, 'grupo' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData()
	{
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM grcl WHERE grupo=".$this->db->escape($data['grupo']));
				if ( $check == 0 ){
					$this->db->insert('grcl', $data);
					echo "Registro Agregado";
					logusu('GRCL',"Grupo de Cliente  ".$data['grupo']." INCLUIDO");
				} else
					echo "Ya existe un grupo con ese Codigo";

			} else
				echo "Fallo Agregado!!!";

		}elseif($oper == 'edit') {
			$grupo  = $data['grupo'];
			$grupov = $this->datasis->dameval("SELECT grupo FROM grcl WHERE id=$id");
			if ( $grupo <> $grupov ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM grcl WHERE grupo=?", array($grupo));
				$this->db->query("UPDATE scli SET grupo=? WHERE grupo=?", array( $grupo, $grupov ));
				$this->db->where('id', $id);
				$this->db->update('grcl', $data);
				logusu('GRCL',"Grupo Cambiado/Fusionado Nuevo:".$grupo." Anterior: ".$grupov." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data['grupo']);
				$this->db->where('id', $id);
				$this->db->update('grcl', $data);
				logusu('GRCL',"Grupo de Cliente  ".$grupo." MODIFICADO");
				echo "Grupo Modificado";
			}

		} elseif($oper == 'del') {
			$grupo = $this->datasis->dameval("SELECT grupo FROM grcl WHERE id=$id");
			$check = $this->datasis->dameval("SELECT count(*) FROM scli WHERE grupo=".$this->db->escape($grupo));
			if ($check > 0){
				echo " El grupo no puede ser eliminado; tiene clientes asociados ";
			} else {
				$this->db->simple_query("DELETE FROM grcl WHERE id=$id ");
				logusu('GRCL',"Grupo de Cliente ".$grupo." ELIMINADO");
				echo "Grupo Eliminado";
			}
		};
	}

	function instalar(){
		//if (!$this->db->table_exists('grcl')) {
		//	$mSQL="CREATE TABLE `grcl` (
		//	  `grupo` varchar(4) NOT NULL DEFAULT '',
		//	  `gr_desc` varchar(25) DEFAULT NULL,
		//	  `clase` char(1) DEFAULT NULL,
		//	  `cuenta` varchar(15) DEFAULT NULL,
		//	  PRIMARY KEY (`grupo`)
		//	) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		//	$this->db->simple_query($mSQL);
		//}
		$campos=$this->db->list_fields('grcl');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE grcl DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grcl ADD UNIQUE INDEX grupo (grupo)');
			$this->db->simple_query('ALTER TABLE grcl ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');

		}
	}
}

/*
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

		$edit = new DataEdit("Grupo de clientes", "grcl");
		$edit->back_url = site_url("ventas/grcl/filteredgrid");

		$edit->pre_process("delete",'_pre_del');
		$edit->post_process('insert','_post_insert');
		$edit->post_process("update",'_post_update');
		$edit->post_process("delete",'_post_delete');

		$edit->grupo = new inputField("Grupo", "grupo");
		$edit->grupo->mode ="autohide";
		$edit->grupo->rule ="trim|required|max_length[4]|callback_chexiste";
		$edit->grupo->size =5;
		$edit->grupo->maxlength =4;

		$edit->clase = new dropdownField("Clase", "clase");
		$edit->clase->option("","");
		$edit->clase->options(array("C"=> "Cliente","O"=>"Otros","I"=>"Internos"));
		$edit->clase->rule= "required";
		$edit->clase->style='width:100px;';

		$edit->gr_desc = new inputField("Descripci&oacute;n", "gr_desc");
		$edit->gr_desc->size =30;
		$edit->gr_desc->maxlength =25;
		$edit->gr_desc->rule= "required|strtoupper";

		$edit->cuenta = new inputField("Cta. Contable", "cuenta");
		$edit->cuenta->rule= "callback_chcuentac";
		$edit->cuenta->size =20;
		$edit->cuenta->maxlength =15;
		$edit->cuenta->append($bcpla);

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = "<h1>Grupos de Clientes</h1>";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _pre_del($do) {
		$grupo=$do->get('grupo');
		$check = $this->datasis->dameval("SELECT count(*) FROM scli WHERE grupo='$grupo'");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
			return False;
		}else	{
			return True;
		}
	}
	function _post_insert($do){
		$codigo=$do->get('grupo');
		logusu('grcl',"GRUPO $codigo CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('grupo');
		$limite=$do->get('limite');
		logusu('grcl',"GRUPO $codigo MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('grupo');
		$limite=$do->get('limite');
		logusu('grcl',"GRUPO $codigo ELIMINADO");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('grupo');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM grcl WHERE grupo='$codigo'");
		if ($check > 0){
			$grupo=$this->datasis->dameval("SELECT gr_desc FROM grcl WHERE grupo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el grupo $grupo");
			return FALSE;
		}else {
  		return TRUE;
		}
	}
*/
