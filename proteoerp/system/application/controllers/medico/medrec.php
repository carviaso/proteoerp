<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Medrec extends Controller {
	var $mModulo = 'MEDREC';
	var $titp    = 'RECURSOS DE ATENCION MEDICA';
	var $tits    = 'RECURSOS DE ATENCION MEDICA';
	var $url     = 'medico/medrec/';

	function Medrec(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'MEDREC', $ventana=0, $this->titp  );
	}

	function index(){
		$this->instalar();
		$this->datasis->creaintramenu(array('modulo'=>'173','titulo'=>'Recursos','mensaje'=>'Recursos','panel'=>'SALUD','ejecutar'=>'ventas/medrec','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	// Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"especial", "img"=>"images/engrana.png", "alt"=>"Especialidades", "label"=>"Especialidades"));
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
		$param['listados']    = $this->datasis->listados('MEDREC', 'JQ');
		$param['otros']       = $this->datasis->otros('MEDREC', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	// Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('medrec', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'medrec', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'medrec', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('medrec', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '300', '500' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );


		// Variables
		$bodyscript .= '
		$("#especial").click(function(){
			$.post("'.site_url('medico/medrec/espform').'",
			function(data){
				$("#fshow").html(data);
				$("#fshow").dialog( { title:"ESPECIALIDADES", width: 370, height: 400, modal: true } );
				$("#fshow").dialog( "open" );
			});
		});';

		$bodyscript .= '});';
		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Forma de Variables
	//
	function espform(){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false',
			'hidden'        => 'true'
		));

		$grid->addField('especialidad');
		$grid->label('Especialidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'left'",
			'edittype'      => "'text'",
			'width'         => 80,
		));

		$grid->showpager(true);
		$grid->setViewRecords(false);
		$grid->setWidth('350');
		$grid->setHeight('220');

		$grid->setUrlget(site_url('medico/medrec/espgd/'));
		$grid->setUrlput(site_url('medico/medrec/espsd/'));

		$mgrid = $grid->deploy();

		$msalida  = '<script type="text/javascript">'."\n";
		$msalida .= '
		$("#newapi'.$mgrid['gridname'].'").jqGrid({
			ajaxGridOptions : {type:"POST"}
			,jsonReader : { root:"data", repeatitems: false }
			'.$mgrid['table'].'
			,scroll: true
			,pgtext: null, pgbuttons: false, rowList:[]
		})
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'navGrid\',  "#pnewapi'.$mgrid['gridname'].'",{edit:false, add:false, del:true, search: false});
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'inlineNav\',"#pnewapi'.$mgrid['gridname'].'");
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'filterToolbar\');
		';

		$msalida .= "\n</script>\n";
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';

		echo $msalida;

	}

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function espgd(){
		$grid       = $this->jqdatagrid;
		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('medesp');
		$response   = $grid->getData('medesp', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function espsd(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM medesp WHERE especialidad=".$this->db->escape($data['especialidad']));
				if ( $check == 0 ){
					$this->db->insert('medesp', $data);
					echo "Registro Agregado";
					logusu('MEDESP',"Registro  INCLUIDO");
				} else
					echo "Ya existe un registro con ese nombre";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$this->db->where("id", $id);
			$this->db->update('medhgrup', $data);
			logusu('MEDESP',"Especialidades  ".$data['especialidad']." MODIFICADO");
			echo "$mcodp Modificado";

		} elseif($oper == 'del') {
			$check = $this->datasis->dameval("SELECT count(*) FROM medrec WHERE especialidad=$id");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene recursos asignados ";
			} else {
				$this->db->query("DELETE FROM medesp WHERE id=$id ");
				logusu('MEDHGRUP',"Registro $id ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


	//******************************************************************
	// Definicion del Grid o Tabla 
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('cedula');
		$grid->label('Cedula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 130,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:13, maxlength: 13 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('colegio');
		$grid->label('Colegio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 130,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:13, maxlength: 13 }',
		));


		$grid->addField('sanidad');
		$grid->label('Sanidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 130,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:13, maxlength: 13 }',
		));


		$grid->addField('especialidad');
		$grid->label('Especialidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
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
		$grid->setAdd(    $this->datasis->sidapuede('MEDREC','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('MEDREC','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('MEDREC','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('MEDREC','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: medrecadd, editfunc: medrecedit, delfunc: medrecdel, viewfunc: medrecshow");

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

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('medrec');

		$response   = $grid->getData('medrec', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$mcodp  = "??????";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM medrec WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('medrec', $data);
					echo "Registro Agregado";

					logusu('MEDREC',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM medrec WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM medrec WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE medrec SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("medrec", $data);
				logusu('MEDREC',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('medrec', $data);
				logusu('MEDREC',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM medrec WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM medrec WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM medrec WHERE id=$id ");
				logusu('MEDREC',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion 
	//
	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit('', 'medrec');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule='';
		$edit->codigo->size =12;
		$edit->codigo->maxlength =10;

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('ME','MEDICO');
		$edit->tipo->option('EN','ENFERMERO/A');
		$edit->tipo->option('AU','AUXILIAR');
		$edit->tipo->option('IN','INSTALACION');
		$edit->tipo->rule ='required';
		$edit->tipo->style='width:180px;';

		$edit->cedula = new inputField('Cedula','cedula');
		$edit->cedula->rule='';
		$edit->cedula->size =15;
		$edit->cedula->maxlength = 13;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule = '';
		$edit->nombre->size = 40;
		$edit->nombre->maxlength = 100;

		$edit->colegio = new inputField('Nro.Colegio','colegio');
		$edit->colegio->rule = '';
		$edit->colegio->size = 15;
		$edit->colegio->maxlength = 13;

		$edit->sanidad = new inputField('Permiso Sanidad','sanidad');
		$edit->sanidad->rule = '';
		$edit->sanidad->size = 15;
		$edit->sanidad->maxlength = 13;

		$edit->especialidad = new dropdownField('Especialidad','especialidad');
		$edit->especialidad->option('','Seleccionar');
		$edit->especialidad->options('SELECT id, especialidad FROM medesp ORDER BY especialidad');
		$edit->especialidad->rule ='required';
		$edit->especialidad->style='width:180px;';

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
		return false;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		if (!$this->db->table_exists('medrec')) {
			$mSQL="CREATE TABLE medrec (
			  codigo       CHAR(10)  DEFAULT NULL COMMENT 'Codigo',
			  tipo         CHAR(2)   DEFAULT NULL COMMENT 'Tipo de Recurso',
			  cedula       CHAR(13)  DEFAULT NULL COMMENT 'Cedula',
			  nombre       CHAR(100) DEFAULT NULL COMMENT 'Nombre del Recurso',
			  colegio      CHAR(13)  DEFAULT NULL COMMENT 'Nro de Colegio medico',
			  sanidad      CHAR(13)  DEFAULT NULL COMMENT 'Nro de Sanidad',
			  especialidad INT(10)   DEFAULT NULL COMMENT 'Especialidad',
			  id           INT(11)   NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (id)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Medicos, ayudantes y especialista'";
			$this->db->query($mSQL);
		}
		//$campos=$this->db->list_fields('medrec');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

?>
