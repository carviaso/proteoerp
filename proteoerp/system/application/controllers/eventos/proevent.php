<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Proevent extends Controller {
	var $mModulo = 'PROEVENT';
	var $titp    = 'EVENTOS';
	var $tits    = 'EVENTOS';
	var $url     = 'eventos/proevent/';

	function Proevent(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PROEVENT', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->creaintramenu(array('modulo'=>'161','titulo'=>'Eventos','mensaje'=>'Eventos','panel'=>'PROMOCIONES','ejecutar'=>'eventos/proevent','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
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
		$param['listados']    = $this->datasis->listados('PROEVENT', 'JQ');
		$param['otros']       = $this->datasis->otros('PROEVENT', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('proevent', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'proevent', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'proevent', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('proevent', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '450', '500' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '
		function fcampa(el, val, opts){
			var meco=\'JAJAJA\';
			if ( el == "N" ){
				meco=\'JEJEJE\';
			}
			return meco;
		};
		';


		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid o Tabla 
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

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


		$grid->addField('ncampana');
		$grid->label('Campana');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'left'",
			'edittype'      => "'text'",
			'width'         => 180,
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

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));
/*
		$grid->addField('comenta');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));
*/
		$grid->addField('cedula');
		$grid->label('Cedula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('persona');
		$grid->label('Nombre del Responsable');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('horai');
		$grid->label('Inicio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
		));

		$grid->addField('horaf');
		$grid->label('Final');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
		));

/*
		$grid->addField('activador');
		$grid->label('Activador');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));
*/

		$grid->addField('activo');
		$grid->label('Activo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));

		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
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
		$grid->setAdd(    $this->datasis->sidapuede('PROEVENT','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('PROEVENT','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PROEVENT','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PROEVENT','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: proeventadd, editfunc: proeventedit, delfunc: proeventdel, viewfunc: proeventshow");

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
		$mWHERE = $grid->geneTopWhere('proevent');

		$response   = $grid->getData('view_proevent', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM proevent WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('proevent', $data);
					echo "Registro Agregado";

					logusu('PROEVENT',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM proevent WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM proevent WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE proevent SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("proevent", $data);
				logusu('PROEVENT',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('proevent', $data);
				logusu('PROEVENT',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM proevent WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM proevent WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM proevent WHERE id=$id ");
				logusu('PROEVENT',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion 

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		// Valida RIF o CI con mensaje
		$script .= $this->datasis->validarif();
		$script .= '
		function rchrifci(value, colname) {
			value.toUpperCase();
			var patt=/((^[VEJG][0-9])|(^[P][A-Z0-9]))/;
			if( !patt.test(value) )
				return [false,"El Rif colocado no es correcto, por favor verifique con el SENIAT."];
			else
				return [true,""];
		};

		$("#cedula").focusout(function(){
			rif=$(this).val().toUpperCase();
			$(this).val(rif);
			if(!chrif(rif)){
				alert("Al parecer el RIF colocado no es correcto, por favor verifique con el SENIAT.");
				return true;
			}else{
				$.ajax({
					type: "POST",
					url: "'.site_url('ajax/traerif').'",
					dataType: "json",
					data: {rifci: rif},
					success: function(data){
						if(data.error==0){
							if($("#persona").val()==""){
								$("#persona").val(data.nombre);
							}
						}
					}
				});
			}
			return true;
		});
		';

		$edit = new DataEdit('', 'proevent');

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

		$edit->campana = new dropdownField('Campaña','campana');
		$edit->campana->option('','Seleccionar');
		$edit->campana->options('SELECT id, TRIM(campana) AS campana FROM procamp WHERE activo="S" ORDER BY campana');
		$edit->campana->rule='max_length[11]';
		$edit->campana->style='width:308px;';

		$edit->activo = new dropdownField('Status','activo');
		$edit->activo->option('S','Activo');
		$edit->activo->option('N','Inactivo');
		$edit->activo->rule='max_length[1]|required';
		$edit->activo->style='width:150px;';

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='';
		$edit->nombre->size = 50;
		$edit->nombre->maxlength =100;

		$edit->comenta = new textareaField('Descripcion','comenta');
		$edit->comenta->rule='';
		$edit->comenta->cols = 48;
		$edit->comenta->rows = 3;

		$edit->cedula = new inputField('Cedula','cedula');
		$edit->cedula->rule='';
		$edit->cedula->size =15;
		$edit->cedula->maxlength =15;

		$edit->persona = new inputField('Responsable','persona');
		$edit->persona->rule='';
		$edit->persona->size =50;
		$edit->persona->maxlength =100;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;

		$edit->horai = new inputField('Inicio','horai');
		$edit->horai->rule='';
		$edit->horai->size =10;
		$edit->horai->maxlength =8;

		$edit->horaf = new inputField('Final','horaf');
		$edit->horaf->rule='';
		$edit->horaf->size =10;
		$edit->horaf->maxlength =8;

/*
		$edit->activador = new textareaField('Activador','activador');
		$edit->activador->rule='';
		$edit->activador->cols = 50;
		$edit->activador->rows = 4;
*/
		$estado = $this->datasis->traevalor('ESTADO');

		if ( empty($estado) ) 
			$estado=12;
		else {
			$estado = $this->datasis->dameval('SELECT codigo FROM estado WHERE entidad="'.$estado.'"');
			if ( empty($estado) ) $estado=12;
		}

		$edit->entidad = new dropdownField('Estado','entidad');
		$edit->entidad->rule ='required';
		$edit->entidad->style='width:220px;';
		$edit->entidad->option('','Seleccione un Estado');
		$edit->entidad->options('SELECT codigo, entidad FROM estado ORDER BY entidad');
		$edit->entidad->insertValue = 12;

		$edit->municipio = new dropdownField('Municipio','municipio');
		$edit->municipio->style='width:220px;';
		$edo = $edit->getval('entidad');
		if($edo!==FALSE){
			$dbedo=$this->db->escape($edo);
			$edit->municipio->options("SELECT codigo, municipio FROM municipios WHERE entidad=$dbedo ORDER BY municipio");
		}else{
			$edit->municipio->option('','Seleccione una Entidad primero');
		}

		$edit->parroquia = new dropdownField('Parroquia','parroquia');
		$edit->parroquia->style='width:220px;';
		$muni = $edit->getval('municipio');
		if($muni!==FALSE){
			$dbmuni=$this->db->escape($muni);
			$edit->parroquia->options("SELECT codigo, parroquia FROM parroquias WHERE entidad=$edo AND municipio=$dbmuni ORDER BY parroquia");
		}else{
			$edit->parroquia->option('','Seleccione un Municipio primero');
		}



/*
		$edit->entidad = new inputField('Entidad','entidad');
		$edit->entidad->rule='integer';
		$edit->entidad->insertValue = $estado;
		$edit->entidad->css_class='inputonlynum';
		$edit->entidad->size =13;
		$edit->entidad->maxlength =11;

		$edit->municipio = new inputField('Municipio','municipio');
		$edit->municipio->rule='integer';
		$edit->municipio->css_class='inputonlynum';
		$edit->municipio->size =13;
		$edit->municipio->maxlength =11;

		$edit->parroquia = new inputField('Parroquia','parroquia');
		$edit->parroquia->rule='integer';
		$edit->parroquia->css_class='inputonlynum';
		$edit->parroquia->size =13;
		$edit->parroquia->maxlength =11;
*/
		$edit->sector = new inputField('Sector','sector');
		$edit->sector->rule='';
		$edit->sector->size =52;
		$edit->sector->maxlength =100;

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));

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
		if ( !$this->db->table_exists('view_proevent') ) {
			$mSQL = "
			CREATE ALGORITHM=UNDEFINED 
			DEFINER=`datasis`@`%` SQL SECURITY INVOKER 
			VIEW `view_proevent` AS 
			select a.id, a.campana, a.nombre, a.comenta, a.cedula, a.persona, a.telefono, a.fecha, a.horai, a.horaf, a.activador, a.activo, a.entidad, a.municipio, a.parroquia, a.sector, a.usuario, a.estampa, b.campana AS ncampana 
			from (`proevent` `a` join `procamp` `b` on((`a`.`campana` = `b`.`id`)))
			";
			$this->db->query($mSQL);
		}


		if (!$this->db->table_exists('proevent')) {
			$mSQL="CREATE TABLE `proevent` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `campana` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Campanna',
			  `nombre` varchar(100) NOT NULL COMMENT 'Nombre del Evento',
			  `comenta` text NOT NULL COMMENT 'Comentario',
			  `cedula` varchar(15) NOT NULL COMMENT 'Cedula del Responsable',
			  `persona` varchar(15) NOT NULL COMMENT 'Persona Responsable',
			  `fecha` date NOT NULL COMMENT 'Fecha',
			  `horai` time NOT NULL COMMENT 'Hora de Inicio',
			  `horaf` time NOT NULL COMMENT 'Hora de Finalizacion',
			  `activador` text NOT NULL,
			  `activo` char(1) NOT NULL DEFAULT 'N',
			  `usuario` varchar(12) NOT NULL,
			  `estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `nombre` (`nombre`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Promocion y eventos'";
			$this->db->query($mSQL);
		}
		//$campos=$this->db->list_fields('proevent');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

?>
