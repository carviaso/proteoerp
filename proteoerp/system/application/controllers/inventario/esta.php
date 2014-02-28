<?php 
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
require_once('estajefe.php');
class Esta extends Controller {
	var $mModulo = 'ESTA';
	var $titp    = 'Estaciones de producci&oacute;n';
	var $tits    = 'Estaciones de producci&oacute;n';
	var $url     = 'inventario/esta/';

	function Esta(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'ESTA', $ventana=0 );
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
		$grid->wbotonadd(array('id'=>'bjefe',   'img'=>'images/arrow_up.png',  'alt' => 'Agregar Encargado', 'label'=>'Agregar Encargado'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('ESTA', 'JQ');
		$param['otros']       = $this->datasis->otros('ESTA', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('esta', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'esta', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'esta', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('esta', $ngrid, $this->url );


		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= '
			jQuery("#bjefe").click( function(){
				$.post("'.site_url($this->url.'dataeditjefe/create').'",
				function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			});';

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '400', '600' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '400', '600' );
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

		$grid->addField('estacion');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
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


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('jefe');
		$grid->label('Encargado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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
		$grid->setAdd(    $this->datasis->sidapuede('ESTA','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('ESTA','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('ESTA','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('ESTA','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: estaadd, editfunc: estaedit, delfunc: estadel, viewfunc: estashow');

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
		$mWHERE = $grid->geneTopWhere('esta');

		$response   = $grid->getData('esta', array(array()), array(), false, $mWHERE );
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

		$edit = new DataEdit('', 'esta');
		$edit->on_save_redirect=false;


		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->estacion = new inputField('Estaci&oacute;n','estacion');
		$edit->estacion->rule = 'max_length[5]|unique|required|alpha_numeric';
		$edit->estacion->size = 7;
		$edit->estacion->mode = 'autohide';
		$edit->estacion->maxlength = 5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[30]|required';
		$edit->nombre->size =32;
		$edit->nombre->maxlength =30;

		$edit->descrip = new textareaField('Descripci&oacute;n','descrip');
		$edit->descrip->cols = 60;
		$edit->descrip->rows = 4;

		$edit->ubica = new textareaField('Ubicaci&oacute;n F&iacute;sica','ubica');
		$edit->ubica->cols = 60;
		$edit->ubica->rows = 4;

		$edit->jefe = new dropdownField('Jefe','jefe');
		$edit->jefe->option('','Seleccionar');
		$edit->jefe->options('SELECT codigo,nombre FROM estajefe ORDER BY nombre');
		$edit->jefe->rule='required';

		//$edit->buttons('modify', 'save', 'add','undo', 'delete', 'back');
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
		return true;
	}

	function _pre_update($do){
		return true;
	}

	function _pre_delete($do){
		$codigo  = $do->get('codigo');

		$this->db->like('estacion', $codigo);
		$this->db->from('sinvplabor');
		$cana = $this->db->count_all_results();
		if($cana!=0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Estacion con relaciones en inventario, no puede ser Borrado';
			return false;
		}
		return true;
	}

	function _post_insert($do){
		$codigo  = $do->get('codigo');
		logusu('esta',"ESTACION ${codigo} CREADA");
	}

	function _post_update($do){
		$codigo  = $do->get('codigo');
		logusu('esta',"ESTACION ${codigo} MODIFICADA");
	}

	function _post_delete($do){
		$codigo  = $do->get('codigo');
		logusu('esta',"ESTACION ${codigo} BORRADA");
	}

	function dataeditjefe(){
		estajefe::dataedit();
	}

	function _pre_jefe_insert($do){
		estajefe::_pre_jefe_insert($do);
	}

	function _pre_jefe_update($do){
		estajefe::_pre_jefe_update($do);
	}

	function _pre_jefe_delete($do){
		estajefe::_pre_jefe_delete($do);
	}

	function _post_jefe_insert($do){
		estajefe::_post_jefe_insert($do);
	}

	function _post_jefe_update($do){
		estajefe::_post_jefe_update($do);
	}

	function _post_jefe_delete($do){
		estajefe::_post_jefe_delete($do);
	}

	function instalar(){
		if (!$this->db->table_exists('esta')){
			$mSQL="CREATE TABLE `esta` (
				`estacion` VARCHAR(5) NOT NULL DEFAULT '',
				`nombre` VARCHAR(30) NULL DEFAULT NULL,
				`descrip` TEXT NULL,
				`ubica` TEXT NULL,
				`jefe` CHAR(5) NULL DEFAULT NULL COMMENT 'tecnico',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `vendedor` (`estacion`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('estajefe')){
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

		//if(!$this->db->field_exists('cedula', 'estajefe')){
		//	$mSQL="ALTER TABLE `estajefe` ADD COLUMN `cedula` VARCHAR(12) NULL DEFAULT NULL AFTER `nombre`";
		//	$this->db->simple_query($mSQL);
		//}

		$campos=$this->db->list_fields('esta');
		if(!in_array('id',$campos)){
			$mSQL="ALTER TABLE `esta` ADD COLUMN `ubica` TEXT NULL AFTER `descrip`";
			$this->db->simple_query($mSQL);
		}
	}
}
