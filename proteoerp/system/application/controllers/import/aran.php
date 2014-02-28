<?php require_once(BASEPATH.'application/controllers/validaciones.php');
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/

class Aran extends Controller {
	var $mModulo = 'ARAN';
	var $titp    = 'Aranceles de aduana';
	var $tits    = 'Aranceles de aduana';
	var $url     = 'import/aran/';

	function Aran(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'ARAN', $ventana=0 );
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
		$param['listados']    = $this->datasis->listados('ARAN', 'JQ');
		$param['otros']       = $this->datasis->otros('ARAN', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('aran', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'aran', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'aran', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('aran', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '300', '500' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '500' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

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
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
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


		$grid->addField('tarifa');
		$grid->label('Tarifa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('unidad');
		$grid->label('Unidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('dolar');
		$grid->label('Dolar');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
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
		$grid->setAdd(    $this->datasis->sidapuede('ARAN','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('ARAN','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('ARAN','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('ARAN','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: aranadd, editfunc: aranedit, delfunc: arandel, viewfunc: aranshow');

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
		$mWHERE = $grid->geneTopWhere('aran');

		$response   = $grid->getData('aran', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){

	}

	function dataedit(){
		$this->rapyd->load('dataobject','dataedit');

		$script='
		$(function() {
			$(".inputnum").numeric(".");
		});';

		$edit = new DataEdit('','aran');
		$edit->on_save_redirect=false;
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo =  new inputField('C&oacute;digo', 'codigo');
		$edit->codigo->mode='autohide';
		$edit->codigo->rule ='trim|strtoupper|required';
		$edit->codigo->size = '20';
		$edit->codigo->maxlength=15;

		$edit->descrip =  new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->rule ='trim|strtoupper|required';

		$edit->unidad = new dropdownField('Unidad','unidad');
		$edit->unidad->rule ='required';
		$edit->unidad->style='width:180px;';
		$edit->unidad->option('','Seleccionar');
		$edit->unidad->options('SELECT TRIM(unidades) AS cod, unidades AS valor FROM unidad ORDER BY unidades');

		$edit->tarifa = new inputField('Tarifa', 'tarifa');
		$edit->tarifa->size = 10;
		$edit->tarifa->maxlength=10;
		$edit->tarifa->css_class='inputnum';
		$edit->tarifa->rule='callback_positivo|numeric|required';

		$edit->dolar = new inputField('D&oacute;lar', 'dolar');
		$edit->dolar->size = 10;
		$edit->dolar->maxlength=10;
		$edit->dolar->css_class='inputnum';
		$edit->dolar->rule='callback_positivo|numeric';

		//$edit->buttons('modify','save','undo','delete','back');
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

	function _pre_delete($do) {
		$codigo=$this->db->escape($do->get('codigo'));
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM itordi WHERE codaran=${codigo}");
		if ($chek > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El arancel a borra contiene productos relacionados, por ello no puede ser eliminado.';
			return false;
		}
		return true;
	}

	function _pre_insert($do){
		//$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_update($do){
		//$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _post_delete($do){
		$codigo=$do->get('codigo');
		logusu('aran',"ARANCEL ${codigo} ELIMINADO");
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');
		logusu('aran',"ARANCEL ${codigo} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');
		logusu('aran',"ARANCEL ${codigo} MODIFICADO");
	}


	function instalar(){
		if (!$this->db->table_exists('aran')) {
			$mSQL="CREATE TABLE `aran` (
			  `codigo` varchar(15) NOT NULL DEFAULT '',
			  `descrip` text,
			  `tarifa` decimal(8,2) DEFAULT '0.00',
			  `unidad` varchar(20) DEFAULT NULL,
			  `dolar` decimal(8,2) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codigo` (`codigo`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
	}
}
