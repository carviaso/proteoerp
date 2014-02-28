<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Sucu extends Controller {
	var $mModulo = 'SUCU';
	var $titp    = 'Sucursales';
	var $tits    = 'Sucursales';
	var $url     = 'supervisor/sucu/';

	function Sucu(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SUCU', $ventana=0 );
	}

	function index(){
		$this->instalar();
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
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

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		//$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('SUCU', 'JQ');
		$param['otros']       = $this->datasis->otros('SUCU', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid      = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('sucu', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'sucu', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'sucu', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('sucu', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '430', '460' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '400', '460' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '200', '400' );

		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 45,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('sucursal');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('url');
		$grid->label('Url');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:200, maxlength: 200 }',
		));


		$grid->addField('prefijo');
		$grid->label('Prefijo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('proteo');
		$grid->label('Proteo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('serie');
		$grid->label('Serie');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('odbc');
		$grid->label('Odbc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('clave');
		$grid->label('Clave');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('puerto');
		$grid->label('Puerto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('db_nombre');
		$grid->label('DB');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('SUCU','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('SUCU','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('SUCU','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('SUCU','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: sucuadd, editfunc: sucuedit, delfunc: sucudel, viewfunc: sucushow');

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
		$mWHERE = $grid->geneTopWhere('sucu');

		$response   = $grid->getData('sucu', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});';

		$edit = new DataEdit('', 'sucu');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->codigo = new inputField('C&oacute;digo', 'codigo');
		$edit->codigo->rule = 'required|max_length[4]|alpha_numeric';
		$edit->codigo->mode = 'autohide';
		$edit->codigo->size = 4;
		$edit->codigo->maxlength = 2;

		$edit->sucursal = new inputField('Nombre de la Sucursal','sucursal');
		$edit->sucursal->rule = 'strtoupper|max_length[45]|required';
		$edit->sucursal->size = 40;
		$edit->sucursal->maxlength = 45;

		$edit->url = new inputField('Direcci&oacute;n URL','url');
		$edit->url->size =40;
		$edit->url->maxlength =200;
		$edit->url->append('Ej: www.example.com o www.example.com:8080');

		$edit->prefijo = new inputField('Prefijo','prefijo');
		$edit->prefijo->size = 5;
		$edit->prefijo->maxlength = 3;
		$edit->prefijo->rule='required';
		$edit->prefijo->append('Prefijo de las transacciones en la sucursal');

		$edit->proteo = new inputField('Direcctorio Proteo','proteo');
		$edit->proteo->maxlength =50;

		$edit->serie = new inputField('Serie','serie');
		$edit->serie->rule='';
		$edit->serie->size =3;
		$edit->serie->maxlength =1;

		$edit->odbc = new inputField('Odbc','odbc');
		$edit->odbc->rule='';
		$edit->odbc->size =40;
		$edit->odbc->maxlength =100;

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		$edit->clave = new inputField('Clave','clave');
		$edit->clave->rule='';
		$edit->clave->size =17;
		$edit->clave->maxlength =15;

		$edit->puerto = new inputField('Puerto','puerto');
		$edit->puerto->rule='';
		$edit->puerto->size =8;
		$edit->puerto->maxlength =6;

		$edit->db_nombre = new inputField('Nombre DB','db_nombre');
		$edit->db_nombre->rule='';
		$edit->db_nombre->size =40;
		$edit->db_nombre->maxlength =100;

		$edit->identifi = new inputField('Identificaci&oacute;n','identifi');
		$edit->identifi->rule='';
		$edit->identifi->size =40;
		$edit->identifi->maxlength =100;

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			echo $edit->output;
		}
	}

	function _pre_insert($do){
		$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
		return true;
	}

	function _post_insert($do){
		$codigo = $do->get('codigo');
		logusu($do->table,"SUCURSAL ${codigo} CREADA");
	}

	function _post_update($do){
		$codigo = $do->get('codigo');
		logusu($do->table,"SUCURSAL ${codigo} MODIFICADA");
	}

	function _post_delete($do){
		$codigo = $do->get('codigo');
		logusu($do->table,"SUCURSAL ${codigo} ELIMINADA");
	}

	function sucubusca() {
		$start    = isset($_REQUEST['start'])  ? $_REQUEST['start']  :  0;
		$limit    = isset($_REQUEST['limit'])  ? $_REQUEST['limit']  : 25;
		$sucursal = isset($_REQUEST['sucursal']) ? $_REQUEST['sucursal'] : '';
		$semilla  = isset($_REQUEST['query'])  ? $_REQUEST['query']  : '';

		$semilla = trim($semilla);

		$mSQL = "SELECT codigo item, CONCAT(codigo, ' ', sucursal) valor FROM sucu WHERE codigo IS NOT NULL ";
		if ( strlen($semilla)>0 ){
			$mSQL .= " AND ( codigo LIKE '$semilla%' OR sucursal LIKE '%$semilla%' ) ";
		} else {
			if ( strlen($sucursal)>0 ) $mSQL .= " AND ( codigo LIKE '$sucursal%' OR sucursal LIKE '%$sucursal%' ) ";
		}
		$mSQL .= "ORDER BY sucursal ";
		$results = $this->db->count_all('sucu');

		if ( empty($mSQL)) {
			echo '{success:true, message:"mSQL vacio, Loaded data", results: 0, data:'.json_encode(array()).'}';
		} else {
			$mSQL .= " limit $start, $limit ";
			$query = $this->db->query($mSQL);
			/*
			$arr = array();
			foreach ($query->result_array() as $row)
			{
				$meco = array();
				foreach( $row as $idd=>$campo ) {
					$meco[$idd] = utf8_encode($campo);
				}
				$arr[] = $meco;
			}*/
			$arr = $this->datasis->codificautf8($query->result_array());
			echo '{success:true, message:"'.$mSQL.'", results:'. $results.', data:'.json_encode($arr).'}';
		}
	}

	function instalar(){
		//if (!$this->db->table_exists('sucu')) {
		//	$mSQL="CREATE TABLE `sucu` (
		//	  `codigo` char(2) NOT NULL DEFAULT '',
		//	  `sucursal` varchar(30) DEFAULT NULL,
		//	  `url` varchar(200) DEFAULT NULL,
		//	  `prefijo` char(1) DEFAULT NULL,
		//	  `proteo` varchar(50) DEFAULT NULL,
		//	  `serie` char(1) DEFAULT NULL,
		//	  `odbc` varchar(100) DEFAULT NULL,
		//	  `usuario` char(20) DEFAULT NULL,
		//	  `clave` char(15) DEFAULT NULL,
		//	  `puerto` char(6) DEFAULT NULL,
		//	  `DB` varchar(100) DEFAULT NULL,
		//	  PRIMARY KEY (`codigo`)
		//	) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		//	$this->db->simple_query($mSQL);
		//}
		$campos=$this->db->list_fields('sucu');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE sucu DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE sucu ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE sucu ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		$campos=$this->db->list_fields('sucu');
		if(!in_array('url',$campos)){
			$mSQL="ALTER TABLE `sucu` ADD `url` VARCHAR(200) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('prefijo',$campos)){
			$mSQL="ALTER TABLE `sucu` ADD `prefijo` VARCHAR(3) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('proteo',$campos)){
			$mSQL="ALTER TABLE `sucu` ADD `proteo` VARCHAR(50) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('codigo',$campos)){
			$mSQL="ALTER TABLE `sucu` ADD PRIMARY KEY (`codigo`)";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('db_nombre',$campos)){
			$mSQL="ALTER TABLE `sucu`  ADD COLUMN `db_nombre` VARCHAR(50) NULL DEFAULT NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('identifi',$campos)){
			$mSQL="ALTER TABLE `sucu` ADD COLUMN `identifi` VARCHAR(50) NULL DEFAULT NULL";
			$this->db->simple_query($mSQL);
		}
	}
}
