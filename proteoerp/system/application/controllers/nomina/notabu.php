<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');

class Notabu extends validaciones {
	var $mModulo = 'NOTABU';
	var $titp    = 'Utilidades de nomina';
	var $tits    = 'Utilidades de nomina';
	var $url     = 'nomina/notabu/';

	function Notabu(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'NOTABU', $ventana=0 );
	}

	function index(){
		$this->instalar();
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

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('NOTABU', 'JQ');
		$param['otros']       = $this->datasis->otros('NOTABU', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('notabu', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'notabu', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'notabu', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('notabu', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '400', '550' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '400', '500' );
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

		$grid->addField('contrato');
		$grid->label('Contrato');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$grid->addField('dia');
		$grid->label('D&iacute;a');
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


		$grid->addField('mes');
		$grid->label('Mes');
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


		$grid->addField('ano');
		$grid->label('A&ntilde;o');
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

		$grid->addField('preaviso');
		$grid->label('Pre-aviso');
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


		$grid->addField('vacacion');
		$grid->label('Vacaciones');
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


		$grid->addField('bonovaca');
		$grid->label('Bono Vacacional');
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


		$grid->addField('antiguedad');
		$grid->label('Antiguedad');
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


		$grid->addField('utilidades');
		$grid->label('Utilidades');
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


		$grid->addField('prima');
		$grid->label('Prima');
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

		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('NOTABU','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('NOTABU','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('NOTABU','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('NOTABU','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: notabuadd, editfunc: notabuedit, delfunc: notabudel, viewfunc: notabushow');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if($deployed){
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
		$mWHERE = $grid->geneTopWhere('notabu');

		$response   = $grid->getData('notabu', array(array()), array(), false, $mWHERE );
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

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit('', 'notabu');
		$edit->on_save_redirect=false;
		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->contrato = new dropdownField('Contrato','contrato');
		$edit->contrato->style ='width:380px;';
		$edit->contrato->option('','Seleccionar');
		$edit->contrato->options('SELECT TRIM(codigo) AS codigo,CONCAT(\'\',TRIM(codigo),TRIM(nombre)) AS nombre FROM noco ORDER BY codigo');
		$edit->contrato->group = 'Relaci&oacute;n Laboral';

		$edit->ano = new inputField('A&ntilde;o','ano');
		$edit->ano->size =3;
		$edit->ano->maxlength=2;
		$edit->ano->rule='trim|numeric';
		$edit->ano->css_class='inputnum';

		$edit->mes = new inputField('Mes','mes');
		$edit->mes->size =3;
		$edit->mes->maxlength=2;
		$edit->mes->rule='trim|numeric';
		$edit->mes-> css_class='inputnum';

		$edit->dia = new inputField('D&iacute;a','dia');
		$edit->dia->size =3;
		$edit->dia->maxlength=2;
		$edit->dia->rule='trim|numeric';
		$edit->dia-> css_class='inputnum';

		$edit->preaviso = new inputField('Pre-aviso','preaviso');
		$edit->preaviso->size =9;
		$edit->preaviso->maxlength=7;
		$edit->preaviso->rule='trim|numeric';
		$edit->preaviso-> css_class='inputnum';

		$edit->vacacion = new inputField('Vacaciones','vacacion');
		$edit->vacacion->size =9;
		$edit->vacacion->maxlength=7;
		$edit->vacacion->rule='trim|numeric';
		$edit->vacacion-> css_class='inputnum';

		$edit->bonovaca = new inputField('Bono Vacacional','bonovaca');
		$edit->bonovaca->size =9;
		$edit->bonovaca->maxlength=7;
		$edit->bonovaca->rule='trim|numeric';
		$edit->bonovaca-> css_class='inputnum';

		$edit->antiguedad = new inputField('Antiguedad','antiguedad');
		$edit->antiguedad->size =9;
		$edit->antiguedad->maxlength=7;
		$edit->antiguedad->rule='trim|numeric';
		$edit->antiguedad-> css_class='inputnum';

		$edit->utilidades = new inputField('Utilidades','utilidades');
		$edit->utilidades->size =9;
		$edit->utilidades->maxlength=7;
		$edit->utilidades->rule='trim|numeric';
		$edit->utilidades-> css_class='inputnum';

		//$edit->buttons('modify','save', 'undo','back');
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

	function calcautilidades(){
		$this->rapyd->load('dataform');

		$script='
			$(".inputnum").numeric(".");
		';

		$form = new DataForm('nomina/notabu/calcautilidades/process');
		$form->back_url = site_url('nomina/notabu/filteredgrid');
		$form->script($script);


		$form->contrato = new dropdownField('Contrato','contrato');
		$form->contrato->style ='width:400px;';
		$form->contrato->options("SELECT codigo,CONCAT('',codigo,nombre)as nombre FROM noco ORDER BY codigo");
		$form->contrato->rule='required';

		$form->monto = new inputField("Monto de dias anual para calcular utilidades","monto");
		$form->monto->style    ='width:400px;';
		$form->monto->options("SELECT codigo,CONCAT('',codigo,nombre)as nombre FROM noco ORDER BY codigo");
		$form->monto->rule     ='required';
		$form->monto->size     = 10;
		$form->monto->css_class='inputnum';

		$form->submit('btnsubmit','Cambiar');
		$form->build_form();

		if($form->on_success()){
			$contrato=$this->db->escape($form->contrato->newValue);
			$monto   =$form->monto->newValue;

			$query = "UPDATE notabu SET utilidades=IF(ano>=1,$monto,($monto/12)*mes+($monto/24)*IF(dia>=15,1,0)) WHERE contrato =${contrato}";
			$this->db->query($query);
		}

		$salida=anchor('nomina/notabu/filteredgrid','Regresar al filtro');

		$data['content'] = $form->output.$salida;
		$data['title']   = 'Cambiar Utilidades basado a monto anual';
		$data['head']    = script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
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
		$contrato=$do->get('contrato');
		$anio=$do->get('anio');
		$mes =$do->get('mes');
		$dia =$do->get('dia');
		logusu('notabu',"CONFIGURACION DE NOMINA ${contrato} ${anio} ${mes} ${dia} REGISTRADA");
	}

	function _post_update($do){
		$contrato=$do->get('contrato');
		$anio=$do->get('anio');
		$mes =$do->get('mes');
		$dia =$do->get('dia');
		logusu('notabu',"CONFIGURACION DE NOMINA ${contrato} ${anio} ${mes} ${dia} MODIFICADA");
	}

	function _post_delete($do){
		$contrato=$do->get('contrato');
		$anio=$do->get('anio');
		$mes =$do->get('mes');
		$dia =$do->get('dia');
		logusu('notabu',"CONFIGURACION DE NOMINA ${contrato} ${anio} ${mes} ${dia}  ELIMINADA");
	}

	function calcautil(){
		$contrato = isset($_REQUEST['contrato1'])  ? $_REQUEST['contrato1']  : '';
		$monto    = isset($_REQUEST['monto1'])     ? $_REQUEST['monto1']     :  0;

		if($contrato == '' || $monto == 0){
			echo "{ success: false, msg: 'Valores malos'}";
		}else{
			$dbcontrato=$this->db->escape($contrato);
			$query = "UPDATE notabu SET utilidades=IF(ano>=1,${monto},(${monto}/12)*mes+(${monto}/24)*IF(dia>=15,1,0)) WHERE contrato=${dbcontrato}";
			$this->db->query($query);
			echo "{ success: true, msg: 'Todo Bien'}";
		}
	}

	function instalar(){
		$this->datasis->creaintramenu(array('modulo'=>'717','titulo'=>'Tabla de utilidades','mensaje'=>'Tabla de utilidades','panel'=>'REGISTROS','ejecutar'=>'nomina/notabu','target'=>'popu','visible'=>'S','pertenece'=>'7','ancho'=>900,'alto'=>600));
		//$this->datasis->modintramenu(800, 600, substr($this->url,0,-1));

		if(!$this->db->table_exists('notabu')){
			$mSQL="CREATE TABLE `notabu` (
				`contrato` CHAR(5) NOT NULL DEFAULT '',
				`ano` DECIMAL(2,0) NOT NULL DEFAULT '0',
				`mes` DECIMAL(2,0) NOT NULL DEFAULT '0',
				`dia` DECIMAL(2,0) NOT NULL DEFAULT '0',
				`preaviso` DECIMAL(5,2) NULL DEFAULT '0.00',
				`vacacion` DECIMAL(5,2) NULL DEFAULT '0.00',
				`bonovaca` DECIMAL(5,2) NULL DEFAULT '0.00',
				`antiguedad` DECIMAL(5,2) NULL DEFAULT '0.00',
				`utilidades` DECIMAL(5,2) NULL DEFAULT '0.00',
				`prima` DECIMAL(2,0) NULL DEFAULT '0',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `princi` (`contrato`, `ano`, `mes`, `dia`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->query($mSQL);
		}

		$campos=$this->db->list_fields('notabu');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE notabu DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE notabu ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
			$this->db->simple_query('ALTER TABLE notabu ADD UNIQUE INDEX princi (contrato, ano, mes, dia)');
		}
	}
}
