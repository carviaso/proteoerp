<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Proasiste extends Controller {
	var $mModulo = 'PROASISTE';
	var $titp    = 'CONTROL DE ASISTENCIA';
	var $tits    = 'CONTROL DE ASISTENCIA';
	var $url     = 'eventos/proasiste/';

	function Proasiste(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PROASISTE', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'163','titulo'=>'Asistencia','mensaje'=>'Asistencia','panel'=>'PROMOCIONES','ejecutar'=>'eventos/proasiste','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
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
		$param['listados']    = $this->datasis->listados('PROASISTE', 'JQ');
		$param['otros']       = $this->datasis->otros('PROASISTE', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('proasiste', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'proasiste', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'proasiste', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('proasiste', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '490', '520' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';
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

		$grid->addField('campana');
		$grid->label('Campana');
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


		$grid->addField('evento');
		$grid->label('Evento');
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


		$grid->addField('telefono');
		$grid->label('Telefono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('email');
		$grid->label('Email');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));

/*
		$grid->addField('entidad');
		$grid->label('Entidad');
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


		$grid->addField('municipio');
		$grid->label('Municipio');
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


		$grid->addField('parroquia');
		$grid->label('Parroquia');
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
*/

		$grid->addField('sector');
		$grid->label('Sector');
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
		$grid->setAdd(    $this->datasis->sidapuede('PROASISTE','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('PROASISTE','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PROASISTE','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PROASISTE','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: proasisteadd, editfunc: proasisteedit, delfunc: proasistedel, viewfunc: proasisteshow");

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
		$mWHERE = $grid->geneTopWhere('proasiste');

		$response   = $grid->getData('proasiste', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM proasiste WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('proasiste', $data);
					echo "Registro Agregado";

					logusu('PROASISTE',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM proasiste WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM proasiste WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE proasiste SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("proasiste", $data);
				logusu('PROASISTE',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('proasiste', $data);
				logusu('PROASISTE',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM proasiste WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM proasiste WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM proasiste WHERE id=$id ");
				logusu('PROASISTE',"Registro ????? ELIMINADO");
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
			$.post(\''.site_url('ajax/get_asislista').'\',{ campana:$("#campana").val(), evento:$("#evento").val() },function(data){$("#contenedor").html(data);})
		});
		';

		$script .= '
		function rchrifci(value, colname) {
			value.toUpperCase();
			var patt=/((^[VEJG][0-9])|(^[P][A-Z0-9]))/;
			if( !patt.test(value) )
				return [false,"El Rif colocado no es correcto, por favor verifique con el SENIAT."];
			else
				return [true,""];
		};';
		
		$script .= $this->datasis->validarif();
		$script .= '
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
							if($("#nombre").val()==""){
								$("#nombre").val(data.nombre);
							}
						}
					}
				});
			}
			return true;
		});
		';

		// Trae de personas si existe
		$script .= '
		$("#telefono").focusin(function(){
			rif=$("#cedula").val();
			if( rif.length > 2 ){
				$.ajax({
					type: "POST",
					url: "'.site_url('ajax/traeparti').'",
					dataType: "json",
					data: {rifci: rif},
					success: function(data){
						if(data.error==0){
							if($("#telefono").val()==""){ $("#telefono").val(data.telefono); }
							if($("#email").val()   ==""){ $("#email").val(data.email); }
							if($("#sector").val()  ==""){ $("#sector").val(data.sector); }
						}
					}
				});
			}
			return true;
		});
		';

		$script .= '
		$("#campana").change(function(){
			campana_change();
			campa = $(this).val();
		});
		function campana_change(){
			$.post(\''.site_url('ajax/get_evento').'\',{ campana:$("#campana").val() },function(data){$("#evento").html(data);})
		}
		';

		$script .= '
		$("#evento").change(function(){
			evento_change();
		});
		function evento_change(){
			$.post(\''.site_url('ajax/get_asislista').'\',{ campana:$("#campana").val(), evento:$("#evento").val() },function(data){$("#contenedor").html(data);})
		}
		';

		$idanterior = intval($this->uri->segment(5));
		$campana = '';
		$evento  = '';

		if ( $idanterior ){
			$ante = $this->datasis->damerow("SELECT campana, evento FROM proasiste WHERE id=$idanterior");
			$campana = $ante['campana'];
			$evento  = $ante['evento'];
		}

		$edit = new DataEdit('', 'proasiste');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		//$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$edit->campana= new dropdownField('Campana','campana');
		$edit->campana->option('','Seleccione una Campa&acute;a');
		$edit->campana->options('SELECT id, campana FROM procamp ORDER BY campana');
		$edit->campana->style='width:250px;';
		$edit->campana->insertValue = $campana;

		$edit->evento = new dropdownField('Evento','evento');
		$edit->evento->style='width:250px;';
		$campa = $edit->getval('campana');
		if ( $campana ) $campa = $campana;
		if( $campa !== FALSE ){
			$dbcampa = $this->db->escape($campa);
			$edit->evento->options("SELECT id, nombre FROM proevent WHERE campana=$dbcampa ORDER BY nombre");
		}else{
			$edit->evento->option('','Seleccione una Campa&acute;a primero');
		}
		$edit->evento->insertValue = $evento;

		$edit->cedula = new inputField('Cedula','cedula');
		$edit->cedula->rule='';
		$edit->cedula->size = 15;
		$edit->cedula->maxlength =15;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='strtoupper';
		$edit->nombre->size = 52;
		$edit->nombre->maxlength =100;

		$edit->telefono = new inputField('Telefono','telefono');
		$edit->telefono->rule='';
		$edit->telefono->size = 32;
		$edit->telefono->maxlength =30;

		$edit->email = new inputField('Email','email');
		$edit->email->rule='';
		$edit->email->size = 52;
		$edit->email->maxlength =100;

		$edit->sector = new inputField('Sector','sector');
		$edit->sector->rule='strtoupper';
		$edit->sector->size = 52;
		$edit->sector->maxlength =100;

		$div = "<br><div style='overflow:auto;border: 1px solid #9AC8DA;background: #EAEAEA;height:200px' id='contenedor'></div>";
		$edit->contenedor = new containerField('contenedor',$div);  

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
		// Busca si existe en proparti y lo crea
		$cedula = $do->get('cedula');
		$cedula = $this->db->escape($cedula);
		$esta = $this->datasis->dameval('SELECT COUNT(*) FROM proparti WHERE cedula='.$cedula);
		if ($esta == 0 ){
			$data = array();
			$data['cedula']   = $do->get('cedula');
			$data['nombre']   = $do->get('nombre');
			$data['telefono'] = $do->get('telefono');
			$data['email']    = $do->get('email');
			$data['sector']   = $do->get('sector');
			$date['fecha']    = date('Ymd');
			
			// busca en el evento el estado, municipio y parroquia
			$evento = $do->get('evento');
			$eventos = $this->datasis->damerow('SELECT entidad, municipio, parroquia FROM proevent WHERE id='.$evento);
			$data['entidad']   = $eventos['entidad'];
			$data['municipio'] = $eventos['municipio'];
			$date['parroquia'] = $eventos['parroquia'];

			$this->db->insert('proparti', $data);
		}
		
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
		if (!$this->db->table_exists('proasiste')) {
			$mSQL="CREATE TABLE `proasiste` (
			  `campana` int(11) DEFAULT NULL,
			  `evento` int(11) DEFAULT NULL,
			  `cedula` char(15) DEFAULT NULL,
			  `nombre` varchar(100) DEFAULT NULL,
			  `telefono` varchar(30) DEFAULT NULL,
			  `email` varchar(100) DEFAULT NULL,
			  `entidad` int(11) DEFAULT NULL,
			  `municipio` int(11) DEFAULT NULL,
			  `parroquia` int(11) DEFAULT NULL,
			  `sector` varchar(100) DEFAULT NULL,
			  `fecha` date DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  KEY `papellido` (`nombre`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC";
			$this->db->query($mSQL);
		}
	}
}

?>
