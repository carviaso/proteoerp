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
		$grid->wbotonadd(array('id'=>'addvar', 'img'=>'images/circuloverde.png'   ,'alt' => 'Agregar variable', 'label'=>'Agregar variable'));
		$grid->wbotonadd(array('id'=>'delvar', 'img'=>'images/circulorojo.png'    ,'alt' => 'Quitar variable' , 'label'=>'Quitar variable'));
		$grid->wbotonadd(array('id'=>'modvar', 'img'=>'images/circuloamarillo.png','alt' => 'Modificar variable' , 'label'=>'Modificar variable'));
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

		$bodyscript .= '
		$("#addvar").click(function (){
			$.post("'.site_url($this->url.'agregavar').'",
			function(data){
				$("#fedita").html(data);
				//$("#fedita").dialog( { title:"PRINCIPIOS ACTIVOS", width: 350, height: 400, modal: true } );
				$("#fedita").dialog( "open" );
			});
		});';

		$bodyscript .= '
		$("#delvar").click(function (){
			$.post("'.site_url($this->url.'quitarvar').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			});
		});';

		$bodyscript .= '
		$("#modvar").click(function (){
			$.post("'.site_url($this->url.'modivar').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			});
		});';

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

		$grid->addField('ano');
		$grid->label('A&ntilde;os');
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
		$grid->label('Meses');
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

		$grid->addField('dia');
		$grid->label('D&iacute;as');
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

		$mSQL="SHOW FULL COLUMNS FROM notabu";
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $row){
			if(in_array($row->Field,array('contrato','ano','mes','dia','id'))) continue;
			$comment=(empty($row->Comment))? $row->Field : trim($row->Comment);

			$grid->addField(trim($row->Field));
			$grid->label($comment);
			$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'align'         => "'right'",
				'edittype'      => "'text'",
				'width'         => 100,
				'editrules'     => '{ required:true }',
				'editoptions'   => '{ size:8, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
				'formatter'     => "'number'",
				'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
			));
		}

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

		$edit->ano = new inputField('A&ntilde;os','ano');
		$edit->ano->size =3;
		$edit->ano->maxlength=2;
		$edit->ano->rule='trim|numeric';
		$edit->ano->css_class='inputnum';

		$edit->mes = new inputField('Meses','mes');
		$edit->mes->size =3;
		$edit->mes->maxlength=2;
		$edit->mes->rule='trim|numeric|callback_chmes';
		$edit->mes->css_class='inputnum';
		$edit->mes->append('1...12');

		$edit->dia = new inputField('D&iacute;as','dia');
		$edit->dia->size =3;
		$edit->dia->maxlength=2;
		$edit->dia->rule='trim|numeric|callback_chdia';
		$edit->dia->css_class='inputnum';
		$edit->dia->append('1...31');

		$mSQL='SHOW FULL COLUMNS FROM notabu';
		$query = $this->db->query($mSQL);
		foreach($query->result() as $row){
			if(in_array($row->Field,array('contrato','ano','mes','dia','id'))) continue;
			$comment=(empty($row->Comment))? $row->Field : trim($row->Comment);
			$obj=trim($row->Field);

			$edit->$obj = new inputField($comment,$obj);
			$edit->$obj->size =9;
			$edit->$obj->maxlength=7;
			$edit->$obj->rule='trim|numeric';
			$edit->$obj->css_class='inputnum';
			$edit->$obj->insertvalue='0';
			$edit->$obj->append('Nombre variable: '.$obj);
		}

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

	function agregavar(){
		$this->rapyd->load('dataform');

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';

		$form = new DataForm($this->url.'agregavar/process');
		$form->script($script);

		$form->nombre = new inputField('Nombre de la variable','nombre');
		$form->nombre->rule = 'trim|strtolower|required|max_length[10]|callback_chvarnom';

		$form->titulo = new inputField('T&iacute;tulo', 'titulo');
		$form->titulo->rule = 'trim|required';

		$form->defecto = new inputField('Valor por onmision', 'defecto');
		$form->defecto->rule = 'required|numeric';
		$form->defecto->insertValue='0';

		$form->build_form();

		if($form->on_success()){
			$nombre  = $form->nombre->newValue;
			$dbtitulo= $this->db->escape($form->titulo->newValue);
			$defecto = floatval($form->defecto->newValue);
			$mSQL="ALTER TABLE `notabu` ADD COLUMN `${nombre}` DECIMAL(2,0) NULL DEFAULT '${defecto}' COMMENT ${dbtitulo} AFTER `id`";
			$ban=$this->db->simple_query($mSQL);
			if($ban){
				$rt=array(
					'status' =>'A',
					'mensaje'=>'Variable guardado',
					'pk'     =>null
				);
				logusu('notabu','Creo variable '.$nombre);
			}else{
				$rt=array(
					'status' =>'B',
					'mensaje'=>'Problemas guardado',
					'pk'     =>null
				);
			}
			echo json_encode($rt);
		}else{
			echo $form->output;
		}
	}

	function chvarnom($val){
		if(preg_match_all('/^[a-zA-Z0-9]+$/i', $val)>0){
			return true;
		}
		$this->validation->set_message('chvarnom', 'El valor introducido en el campo %s no es v&aacute;lido');
		return false;
	}

	function quitarvar(){
		$this->rapyd->load('dataform');

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});';

		$form = new DataForm($this->url.'quitarvar/process');

		$form->nombre = new dropdownField('Nombre','nombre');
		$form->nombre->style ='width:380px;';
		$form->nombre->option('','Seleccionar');
		$form->nombre->rule='required';
		$mSQL='SHOW FULL COLUMNS FROM notabu';
		$query = $this->db->query($mSQL);
		foreach($query->result() as $row){
			if(in_array($row->Field,array('contrato','ano','mes','dia','id'))) continue;
			$comment=(empty($row->Comment))? $row->Field : trim($row->Comment);
			$obj=trim($row->Field);
			$form->nombre->option($obj,"${obj}-${comment}");
		}

		$form->build_form();

		if($form->on_success()){
			$nombre  = $form->nombre->newValue;
			$mSQL="ALTER TABLE `notabu` DROP COLUMN `${nombre}`";
			$ban=$this->db->simple_query($mSQL);
			if($ban){
				$rt=array(
					'status' =>'A',
					'mensaje'=>'Variable guardado',
					'pk'     =>null
				);
				logusu('notabu','Elimino variable '.$nombre);
			}else{
				$rt=array(
					'status' =>'B',
					'mensaje'=>'Problemas eliminando',
					'pk'     =>null
				);
			}
			echo json_encode($rt);
		}else{
			echo $form->output;
		}
	}

	function modivar(){
		$this->rapyd->load('dataform');

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});';

		$form = new DataForm($this->url.'modivar/process');
		$form->script($script);

		$form->nombre = new dropdownField('Nombre','nombre');
		$form->nombre->style ='width:380px;';
		$form->nombre->option('','Seleccionar');
		$form->nombre->rule='required';
		$mSQL='SHOW FULL COLUMNS FROM notabu';
		$query = $this->db->query($mSQL);
		foreach($query->result() as $row){
			if(in_array($row->Field,array('contrato','ano','mes','dia','id'))) continue;
			$comment=(empty($row->Comment))? $row->Field : trim($row->Comment);
			$obj=trim($row->Field);
			$form->nombre->option($obj,"${obj}-${comment}");
		}

		$form->nnombre = new inputField('T&iacute;tulo de la variable','nnombre');
		$form->nnombre->rule = 'trim|strtolower|required|max_length[10]|callback_chvarnom';

		$form->defecto = new inputField('Valor por onmision', 'defecto');
		$form->defecto->rule = 'required|numeric';
		$form->defecto->insertValue='0';

		$form->build_form();

		if($form->on_success()){
			$nombre  = $form->nombre->newValue;
			$nnombre = $this->db->escape($form->nnombre->newValue);
			$defecto = floatval($form->defecto->newValue);
			$mSQL="ALTER TABLE `notabu` CHANGE COLUMN `${nombre}` `${nombre}` DECIMAL(5,2) NOT NULL DEFAULT '${defecto}' COMMENT ${nnombre}";
			$ban=$this->db->simple_query($mSQL);
			if($ban){
				$rt=array(
					'status' =>'A',
					'mensaje'=>'Variable guardada',
					'pk'     =>null
				);
				logusu('notabu','Elimino variable '.$nombre);
			}else{
				$rt=array(
					'status' =>'B',
					'mensaje'=>'Problemas eliminando',
					'pk'     =>null
				);
			}
			echo json_encode($rt);
		}else{
			echo $form->output;
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

	function chmes($mes){
		$mes=intval($mes);
		if($mes >=0 && $mes<=12){
			return true;
		}
		$this->validation->set_message('chmes', 'El valor introducido en el campo %s no es v&aacute;lido');
		return false;
	}

	function chdia($dia){
		$dia=intval($dia);
		if($dia >=0 && $dia<=31){
			return true;
		}
		$this->validation->set_message('chdia', 'El valor introducido en el campo %s no es v&aacute;lido');
		return false;
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
