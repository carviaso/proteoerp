<?php  
//require_once(BASEPATH.'application/controllers/validaciones.php');
class Tarjeta extends Controller {
	var $mModulo='TARJETA';
	var $titp='Formas de Pago';
	var $tits='Formas de Pago';
	var $url ='ventas/tarjeta/';

	function Tarjeta(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'TARJETA', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 700, 500, substr($this->url,0,-1) );
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
		$param['listados']    = $this->datasis->listados('TARJETA', 'JQ');
		$param['otros']       = $this->datasis->otros('TARJETA', 'JQ');
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
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('tarjeta', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'tarjeta', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'tarjeta', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('tarjeta', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '250', '430' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '240', '500' );
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
		$grid->addField('tipo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 2 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 170,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 20 }',
		));

		$grid->addField('activo');
		$grid->label('Activo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: {"S":"Activo","N":"Inactivo" }, style:"width:100px" }'
		));

		$grid->addField('comision');
		$grid->label('Comisi&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('impuesto');
		$grid->label('Impuesto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('mensaje');
		$grid->label('Mensaje');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 60 }',
		));


		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'        => 'true',
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('235');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('TARJETA','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('TARJETA','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('TARJETA','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('TARJETA','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: tarjetaadd, editfunc: tarjetaedit, delfunc: tarjetadel, viewfunc: tarjetashow');

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
		$mWHERE = $grid->geneTopWhere('tarjeta');

		$response   = $grid->getData('tarjeta', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		//$this->load->library('jqdatagrid');
		//$oper   = $this->input->post('oper');
		//$id     = $this->input->post('id');
		//$data   = $_POST;
		//$mcodp  = '??????';
		//$check  = 0;
        //
		//unset($data['oper']);
		//unset($data['id']);
		//if($oper == 'add'){
		//	if(false == empty($data)){
		//		$check = $this->datasis->dameval("SELECT count(*) FROM tarjeta WHERE $mcodp=".$this->db->escape($data[$mcodp]));
		//		if ( $check == 0 ){
		//			$this->db->insert('tarjeta', $data);
		//			echo "Registro Agregado";
        //
		//			logusu('TARJETA',"Registro ????? INCLUIDO");
		//		} else
		//			echo "Ya existe un registro con ese $mcodp";
		//	} else
		//		echo "Fallo Agregado!!!";
        //
		//} elseif($oper == 'edit') {
		//	$nuevo  = $data[$mcodp];
		//	$anterior = $this->datasis->dameval("SELECT $mcodp FROM tarjeta WHERE id=$id");
		//	if ( $nuevo <> $anterior ){
		//		//si no son iguales borra el que existe y cambia
		//		$this->db->query("DELETE FROM tarjeta WHERE $mcodp=?", array($mcodp));
		//		$this->db->query("UPDATE tarjeta SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
		//		$this->db->where("id", $id);
		//		$this->db->update("tarjeta", $data);
		//		logusu('TARJETA',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
		//		echo "Grupo Cambiado/Fusionado en clientes";
		//	} else {
		//		unset($data[$mcodp]);
		//		$this->db->where("id", $id);
		//		$this->db->update('tarjeta', $data);
		//		logusu('TARJETA',"Grupo de Cliente  ".$nuevo." MODIFICADO");
		//		echo "$mcodp Modificado";
		//	}
        //
		//} elseif($oper == 'del') {
		//	$meco = $this->datasis->dameval("SELECT $mcodp FROM tarjeta WHERE id=$id");
		//	//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tarjeta WHERE id='$id' ");
		//	if ($check > 0){
		//		echo " El registro no puede ser eliminado; tiene movimiento ";
		//	} else {
		//		$this->db->simple_query("DELETE FROM tarjeta WHERE id=$id ");
		//		logusu('TARJETA',"Registro ????? ELIMINADO");
		//		echo "Registro Eliminado";
		//	}
		//};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});';

		$edit = new DataEdit('', 'tarjeta');
		$edit->on_save_redirect=false;
		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->pre_process( 'delete','_pre_delete');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->tipo = new inputField('C&oacute;digo', 'tipo');
		$edit->tipo->maxlength=2;
		$edit->tipo->size= 3;
		$edit->tipo->mode= 'autohide';
		$edit->tipo->rule= 'strtoupper|required|callback_chexiste|alpha_numeric';

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->maxlength=20;
		$edit->nombre->size=25;
		$edit->nombre->rule = 'strtoupper|required|trim';

		$edit->comision = new inputField('Comisi&oacute;n', 'comision');
		$edit->comision->maxlength=8;
		$edit->comision->size=10;
		$edit->comision->css_class='inputnum';
		$edit->comision->rule='numeric';

		$edit->impuesto = new inputField('Impuesto', 'impuesto');
		$edit->impuesto->maxlength=8;
		$edit->impuesto->size=10;
		$edit->impuesto->css_class='inputnum';
		$edit->impuesto->rule='numeric';

		$edit->mensaje  = new inputField('Mensaje', 'mensaje');
		$edit->mensaje->maxlength=60;
		$edit->mensaje->size=40;

		$edit->activo = new dropdownField('Activo', 'activo');
		$edit->activo->option('S','Si');
		$edit->activo->option('N','No');
		$edit->activo->rule ='enum[S,N]';
		$edit->activo->style='width:80px;';

		//$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
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
		$tipo  =$do->get('tipo');
		$dbtipo=$this->db->escape($tipo);
		$check = $this->datasis->dameval("SELECT COUNT(*) FROM sfpa WHERE tipo=${dbtipo}");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Forma de pago con Movimiento no puede ser Borrado, solo puede desactivarlo';
			return false;
		}else{
			return true;
		}
	}

	function _post_insert($do){
		$codigo=$do->get('tipo');
		$nombre=$do->get('nombre');
		logusu('tarjeta',"FORMA DE PAGO ${codigo} NOMBRE ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('tipo');
		$nombre=$do->get('nombre');
		logusu('tarjeta',"FORMA DE PAGO ${codigo} NOMBRE ${nombre} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('tipo');
		$nombre=$do->get('nombre');
		logusu('tarjeta',"FORMA DE PAGO ${codigo} NOMBRE ${nombre} ELIMINADO");
	}

	function chexiste($codigo){
		$codigo  =$this->input->post('tipo');
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM tarjeta WHERE tipo=${dbcodigo}");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM tarjeta WHERE tipo=${dbcodigo}");
			$this->validation->set_message('chexiste',"El tipo ${codigo} ya existe para la forma de pago ${nombre}");
			return false;
		}else {
		return true;
		}
	}

	function instalar(){
		$campos=$this->db->list_fields('tarjeta');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE tarjeta DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE tarjeta ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE tarjeta ADD UNIQUE INDEX tipo (tipo)');
		}

		if(!in_array('activo',$campos)){
			$mSQL="ALTER TABLE `tarjeta` ADD COLUMN `activo`  CHAR(1) NULL DEFAULT 'S' AFTER `mensaje`";
			$this->db->query($mSQL);
		}
	}
}
