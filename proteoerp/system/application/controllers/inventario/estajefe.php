<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
class Estajefe extends Controller {
	var $mModulo = 'ESTAJEFE';
	var $titp    = 'Encargado de estaciones';
	var $tits    = 'Encargado de estaciones';
	var $url     = 'inventario/estajefe/';

	function Estajefe(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'ESTAJEFE', $ventana=0 );
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
		$param['listados']    = $this->datasis->listados('ESTAJEFE', 'JQ');
		$param['otros']       = $this->datasis->otros('ESTAJEFE', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('estajefe', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'estajefe', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'estajefe', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('estajefe', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '400', '500' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '400', '500' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '350', '400' );

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
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('cedula');
		$grid->label('C&eacute;dula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('direc1');
		$grid->label('Direcci&oacute;n 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:35, maxlength: 35 }',
		));


		$grid->addField('direc2');
		$grid->label('Direcci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:35, maxlength: 35 }',
		));


		$grid->addField('telefono');
		$grid->label('Tel&eacute;fono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 130,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:13, maxlength: 13 }',
		));


		$grid->addField('correo');
		$grid->label('Correo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:250, maxlength: 250 }',
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

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('ESTAJEFE','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('ESTAJEFE','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('ESTAJEFE','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('ESTAJEFE','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: estajefeadd, editfunc: estajefeedit, delfunc: estajefedel, viewfunc: estajefeshow');

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
		$mWHERE = $grid->geneTopWhere('estajefe');

		$response   = $grid->getData('estajefe', array(array()), array(), false, $mWHERE );
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
		});
		';

		$edit = new DataEdit('', 'estajefe');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_jefe_insert');
		$edit->post_process('update','_post_jefe_update');
		$edit->post_process('delete','_post_jefe_delete');
		$edit->pre_process( 'insert', '_pre_jefe_insert' );
		$edit->pre_process( 'update', '_pre_jefe_update' );
		$edit->pre_process( 'delete', '_pre_jefe_delete' );

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='max_length[5]|required|unique|alpha_numeric';
		$edit->codigo->mode='autohide';
		$edit->codigo->size =7;
		$edit->codigo->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[30]|required';
		$edit->nombre->size =32;
		$edit->nombre->maxlength =30;

		$edit->cedula = new inputField('C&eacute;dula', 'cedula');
		$edit->cedula->rule = 'trim|strtoupper|callback_chci';
		$edit->cedula->maxlength =13;
		$edit->cedula->size =14;

		$edit->direc1 = new inputField('Direcci&oacute;n','direc1');
		$edit->direc1->rule='max_length[35]';
		$edit->direc1->size =37;
		$edit->direc1->maxlength =35;

		$edit->telefono = new inputField('Tel&eacute;fono','telefono');
		$edit->telefono->rule='max_length[13]';
		$edit->telefono->size =15;
		$edit->telefono->maxlength =13;

		$edit->correo = new inputField('Correo','correo');
		$edit->correo->rule='max_length[250]';
		$edit->correo->maxlength =250;

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

	function _pre_jefe_insert($do){
		$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_jefe_update($do){
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _pre_jefe_delete($do){
		$codigo  = $do->get('codigo');

		$this->db->where('jefe', $codigo);
		$this->db->from('esta');
		$cana = $this->db->count_all_results();
		if($cana!=0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Este encargado no puede ser borrado por tener estaciones relacionadas';
			return false;
		}
		return true;
	}

	function _post_jefe_insert($do){
		$primary =$do->get('codigo');
		logusu('estajefe',"ENCARGADO DE PRODUCCION ${primary} CREADO");
	}

	function _post_jefe_update($do){
		$primary =$do->get('codigo');
		logusu('estajefe',"ENCARGADO DE PRODUCCION ${primary} MODIFICADO");
	}

	function _post_jefe_delete($do){
		$primary =$do->get('codigo');
		logusu('estajefe',"ENCARGADO DE PRODUCCION ${primary} ELIMINADO1");
	}

	function instalar(){

		if (!$this->db->table_exists('estajefe')) {
			$mSQL="CREATE TABLE `estajefe` (
				`codigo` VARCHAR(5) NOT NULL DEFAULT '',
				`nombre` VARCHAR(30) NULL DEFAULT NULL,
				`cedula` VARCHAR(12) NULL DEFAULT NULL,
				`direc1` VARCHAR(35) NULL DEFAULT NULL,
				`direc2` VARCHAR(35) NULL DEFAULT NULL,
				`telefono` VARCHAR(13) NULL DEFAULT NULL,
				`correo` VARCHAR(250) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `unico` (`codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('estajefe');
		if(!in_array('cedula',$campos)){
			$mSQL="ALTER TABLE `estajefe` ADD COLUMN `cedula` VARCHAR(12) NULL DEFAULT NULL AFTER `nombre`";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('id',$campos)){
			$mSQL="ALTER TABLE `estajefe`
			ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `correo`,
			DROP PRIMARY KEY,
			ADD UNIQUE INDEX `unico` (`codigo`),
			ADD PRIMARY KEY (`id`)";
			$this->db->simple_query($mSQL);
		}
	}
}
