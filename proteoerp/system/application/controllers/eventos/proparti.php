<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Proparti extends Controller {
	var $mModulo = 'PROPARTI';
	var $titp    = 'PARTICIPANTES';
	var $tits    = 'PARTICIPANTES';
	var $url     = 'eventos/proparti/';

	function Proparti(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PROPARTI', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'162','titulo'=>'Participantes','mensaje'=>'Participantes','panel'=>'PROMOCIONES','ejecutar'=>'eventos/proparti','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		$this->instalar();
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
		$grid->wbotonadd(array("id"=>"email",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Email"));
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

		$param['listados']    = $this->datasis->listados('PROPARTI', 'JQ');
		$param['otros']       = $this->datasis->otros('PROPARTI', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('proparti', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'proparti', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'proparti', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('proparti', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '350', '500' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '
			$("#email").click( function () {$.post("'.site_url($this->url.'email').'");});';


		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Confirmar Envio
	function email(){

		$notifica  = "		
Amigos, en el siguiente enlace les dejo con mucho cariño, mi mensaje de fin de año, Feliz 2015!
\n

https://soundcloud.com/marthahernandez-2/mi-mensaje-de-fin-de-ano
\n
Un gran abrazo!
\n
Su amiga
";

		set_time_limit(0); // I added unlimited time limit here, because the records I imported were in the hundreds of thousands.

		$titulo = utf8_decode('Mi mensaje de fin de año');
		$query = $this->db->query('SELECT * FROM test.correos');
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$msj = 'Envio Confirmado ';
				$email = trim($row->correo); 
				$this->datasis->correo( $email, $titulo, utf8_decode($notifica) );
			}
		}
	
		echo 'todo bien';
	}



	//******************************************************************
	// Definicion del Grid o Tabla 
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('cedula');
		$grid->label('Cedula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
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
		$grid->setAdd(    $this->datasis->sidapuede('PROPARTI','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('PROPARTI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PROPARTI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PROPARTI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: propartiadd, editfunc: propartiedit, delfunc: propartidel, viewfunc: propartishow");

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
		$mWHERE = $grid->geneTopWhere('proparti');

		$response   = $grid->getData('proparti', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM proparti WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('proparti', $data);
					echo "Registro Agregado";

					logusu('PROPARTI',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM proparti WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM proparti WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE proparti SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("proparti", $data);
				logusu('PROPARTI',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('proparti', $data);
				logusu('PROPARTI',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM proparti WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM proparti WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM proparti WHERE id=$id ");
				logusu('PROPARTI',"Registro ????? ELIMINADO");
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

			var edo = $("#entidad").val();
			if ( $("#municipio").val() == false ){
				entidad_change();
			}
			$("#entidad").change(function(){
				entidad_change();
				edo = $(this).val();
			});
			$("#municipio").change(
				function(){ 
					$.post(\''.site_url('ajax/get_parroquia').'\',
						{ municipio:$(this).val(), entidad:edo },
						function(data){	$("#parroquia").html(data);	}
					) 
			});

		});
		function entidad_change(){
			$.post(\''.site_url('ajax/get_municipio').'\',{ estado:$("#entidad").val() },function(data){$("#municipio").html(data);})
			$.post(\''.site_url('ajax/get_parroquia').'\',{ municipio:\'\', entidad:\'\' },function(data){$("#parroquia").html(data);})
		}
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

		$edit = new DataEdit('', 'proparti');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert', '_pre_insert' );
		$edit->pre_process( 'update', '_pre_update' );
		$edit->pre_process( 'delete', '_pre_delete' );

		$edit->cedula = new inputField('Cedula','cedula');
		$edit->cedula->rule='';
		$edit->cedula->size =17;
		$edit->cedula->maxlength =15;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='';
		$edit->nombre->size =52;
		$edit->nombre->maxlength =100;

		$edit->telefono = new inputField('Telefono','telefono');
		$edit->telefono->rule='';
		$edit->telefono->size =32;
		$edit->telefono->maxlength =30;

		$edit->email = new inputField('Email','email');
		$edit->email->rule='';
		$edit->email->size =52;
		$edit->email->maxlength =100;

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

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;

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
		if (!$this->db->table_exists('proparti')) {
			$mSQL="CREATE TABLE `proparti` (
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
			) ENGINE=MyISAM";
			$this->db->query($mSQL);
		}
		//$campos=$this->db->list_fields('proparti');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

?>
