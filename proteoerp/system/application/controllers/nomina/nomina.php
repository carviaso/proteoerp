<?php
class Nomina extends Controller {
	var $mModulo = 'NOMI';
	var $titp    = 'NOMINAS GUARDADAS';
	var $tits    = 'NOMINAS GUARDADAS';
	var $url     = 'nomina/nomina/';

	function Nomina(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'NOMI', $ventana=0 );
	}

	function index(){

		if ( !$this->db->table_exists('view_nomina') ) {
			$mSQL = "
			CREATE ALGORITHM = UNDEFINED VIEW view_nomina AS 
			SELECT `a`.`contrato` AS `contrato`, a.trabaja, `b`.`nombre` AS `nombre`,`a`.`numero` AS `numero`,`a`.`frecuencia` AS `frecuencia`,`a`.`fecha` AS `fecha`,`a`.`fechap` AS `fechap`,`a`.`estampa` AS `estampa`,`a`.`usuario` AS `usuario`,`a`.`transac` AS `transac` 
			FROM (`nomina` `a` join `noco` `b` on((`a`.`contrato` = `b`.`codigo`))) 
			GROUP BY `a`.`numero`;";
			$this->db->query($mSQL);
		}
	
		if ( !$this->datasis->iscampo('nomina','id') ) {
			$this->db->simple_query('ALTER TABLE nomina DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE nomina ADD INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE nomina ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
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
		$param['listados']    = $this->datasis->listados('NOMI', 'JQ');
		$param['otros']       = $this->datasis->otros('NOMI', 'JQ');
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
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function nominaadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function nominaedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function nominashow(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function nominadel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								apprise("Registro eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
							}else{
								apprise("Registro no se puede eliminado");
							}
						}catch(e){
							$("#fborra").html(data);
							$("#fborra").dialog( "open" );
						}
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';
		//Wraper de javascript
		$bodyscript .= '
		$(function(){
			$("#dialog:ui-dialog").dialog( "destroy" );
			var mId = 0;
			var montotal = 0;
			var ffecha = $("#ffecha");
			var grid = jQuery("#newapi'.$grid0.'");
			var s;
			var allFields = $( [] ).add( ffecha );
			var tips = $( ".validateTips" );
			s = grid.getGridParam(\'selarrrow\');
			';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Guardar": function() {
					var bValid = true;
					var murl = $("#df1").attr("action");
					allFields.removeClass( "ui-state-error" );
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url: murl,
						data: $("#df1").serialize(),
						success: function(r,s,x){
							try{
								var json = JSON.parse(r);
								if (json.status == "A"){
									apprise("Registro Guardado");
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									'.$this->datasis->jwinopen(site_url('formatos/ver/NOMINA').'/\'+res.id+\'/id\'').';
									return true;
								} else {
									apprise(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
							}
						}
					})
				},
				"Cancelar": function() {
					$("#fedita").html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$("#fedita").html("");
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fshow").html("");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				$("#fshow").html("");
			}
		});';

		$bodyscript .= '
		$("#fborra").dialog({
			autoOpen: false, height: 300, width: 400, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fborra").html("");
					jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
				$("#fborra").html("");
			}
		});';

		$bodyscript .= '});'."\n";

		$bodyscript .= "\n</script>\n";
		$bodyscript .= "";
		return $bodyscript;
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('contrato');
		$grid->label('Contrato');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('trabaja');
		$grid->label('Personal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));

		$grid->addField('frecuencia');
		$grid->label('Frecuencia');
		$grid->params(array(
			'align'         => '"center"',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('fechap');
		$grid->label('Fechap');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('transac');
		$grid->label('Transac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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

		#show/hide navigations buttons
		$grid->setAdd(    false ); //$this->datasis->sidapuede('NOMI','INCLUIR%' ));
		$grid->setEdit(   false ); //$this->datasis->sidapuede('NOMI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('NOMI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('NOMI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: nominaadd, editfunc: nominaedit, delfunc: nominadel, viewfunc: nominashow");

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
		$mWHERE = $grid->geneTopWhere('view_nomina');

		$response   = $grid->getData('view_nomina', array(array()), array(), false, $mWHERE, 'numero', 'DESC' );
		$rs = $grid->jsonresult( $response);

		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion
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
				$check = $this->datasis->dameval("SELECT count(*) FROM nomina WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('nomina', $data);
					echo "Registro Agregado";

					logusu('NOMI',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM nomina WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM nomina WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE nomina SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("nomina", $data);
				logusu('NOMINA',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('nomina', $data);
				logusu('NOMINA',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM nomina WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM nomina WHERE id=$id ");
				logusu('NOMINA',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};

	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});
		';

		$edit = new DataEdit($this->tits, 'nomina');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->script($script,'create');

		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$script= ' 
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});		';
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->numero = new inputField('Numero','numero');
		$edit->numero->rule          = '';
		$edit->numero->size          = 10;
		$edit->numero->maxlength     =  8;

		$edit->frecuencia = new inputField('Frecuencia','frecuencia');
		$edit->frecuencia->rule      = '';
		$edit->frecuencia->size      =  3;
		$edit->frecuencia->maxlength =  1;

		$edit->contrato = new inputField('Contrato','contrato');
		$edit->contrato->rule        = '';
		$edit->contrato->size        = 10;
		$edit->contrato->maxlength   =  8;

		$edit->depto = new inputField('Depto','depto');
		$edit->depto->rule           = '';
		$edit->depto->size           = 10;
		$edit->depto->maxlength      =  8;

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule          = '';
		$edit->codigo->size          = 17;
		$edit->codigo->maxlength     = 15;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule          = '';
		$edit->nombre->size          = 32;
		$edit->nombre->maxlength     = 30;

		$edit->concepto = new inputField('Concepto','concepto');
		$edit->concepto->rule        = '';
		$edit->concepto->size        =  6;
		$edit->concepto->maxlength   =  4;

		$edit->tipo = new inputField('Tipo','tipo');
		$edit->tipo->rule            = '';
		$edit->tipo->size            =  3;
		$edit->tipo->maxlength       =  1;

		$edit->descrip = new inputField('Descrip','descrip');
		$edit->descrip->rule         = '';
		$edit->descrip->size         = 37;
		$edit->descrip->maxlength    = 35;

		$edit->grupo = new inputField('Grupo','grupo');
		$edit->grupo->rule='';
		$edit->grupo->size =6;
		$edit->grupo->maxlength =4;

		$edit->formula = new inputField('Formula','formula');
		$edit->formula->rule='';
		$edit->formula->size =122;
		$edit->formula->maxlength =120;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='';
		$edit->monto->size =10;
		$edit->monto->maxlength =8;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;

		$edit->cuota = new inputField('Cuota','cuota');
		$edit->cuota->rule='integer';
		$edit->cuota->css_class='inputonlynum';
		$edit->cuota->size =13;
		$edit->cuota->maxlength =11;

		$edit->cuotat = new inputField('Cuotat','cuotat');
		$edit->cuotat->rule='integer';
		$edit->cuotat->css_class='inputonlynum';
		$edit->cuotat->size =13;
		$edit->cuotat->maxlength =11;

		$edit->valor = new inputField('Valor','valor');
		$edit->valor->rule='numeric';
		$edit->valor->css_class='inputnum';
		$edit->valor->size =19;
		$edit->valor->maxlength =17;

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		$edit->transac = new inputField('Transac','transac');
		$edit->transac->rule='';
		$edit->transac->size =10;
		$edit->transac->maxlength =8;

		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));

		$edit->fechap = new dateonlyField('Fechap','fechap');
		$edit->fechap->rule='chfecha';
		$edit->fechap->size =10;
		$edit->fechap->maxlength =8;

		$edit->trabaja = new inputField('Trabaja','trabaja');
		$edit->trabaja->rule='';
		$edit->trabaja->size =10;
		$edit->trabaja->maxlength =8;

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
		if (!$this->db->table_exists('nomina')) {
			$mSQL="CREATE TABLE `nomina` (
			  `numero` char(8) DEFAULT NULL,
			  `frecuencia` char(1) DEFAULT NULL,
			  `contrato` char(8) DEFAULT NULL,
			  `depto` char(8) DEFAULT NULL,
			  `codigo` char(15) NOT NULL DEFAULT '',
			  `nombre` char(30) DEFAULT NULL,
			  `concepto` char(4) NOT NULL DEFAULT '',
			  `tipo` char(1) DEFAULT NULL,
			  `descrip` char(35) DEFAULT NULL,
			  `grupo` char(4) DEFAULT NULL,
			  `formula` char(120) DEFAULT NULL,
			  `monto` double DEFAULT NULL,
			  `fecha` date DEFAULT NULL,
			  `cuota` int(11) DEFAULT NULL,
			  `cuotat` int(11) DEFAULT NULL,
			  `valor` decimal(17,2) DEFAULT '0.00',
			  `estampa` date DEFAULT NULL,
			  `usuario` char(12) DEFAULT NULL,
			  `transac` char(8) DEFAULT NULL,
			  `hora` char(8) DEFAULT NULL,
			  `fechap` date DEFAULT NULL,
			  `trabaja` char(8) DEFAULT NULL,
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  KEY `numero` (`numero`),
			  KEY `codigo` (`codigo`),
			  KEY `concepto` (`concepto`),
			  KEY `fecha` (`fecha`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('nomina');
		//if(!in_array('<#campo#>',$campos)){ }
	}



/*
	function Nomina(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}

	function index(){
		$this->datasis->modulo_id(712,1);
		//redirect("nomina/nomina/filteredgrid");
		redirect("nomina/nomina/extgrid");
	}

	function extgrid(){
		$script = $this->nomiextjs();
		$data["script"] = $script;
		$data['title']  = heading('Nomina');
		$this->load->view('extjs/noco',$data);
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro de Nomina", 'nomina');
		
		$filter->nombre = new inputField("Nombre", "nombre");
		
		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/nomina/dataedit/show/<#id#>','<#numero#>');

		$grid = new DataGrid("Lista de Nomina");
		$grid->order_by("numero","asc");
		$grid->per_page = 10;
		$grid->column("N&uacute;mero",$uri);
		$grid->column("Nombre","nombre");
		$grid->column("Descripci&oacute;n","descrip");
		$grid->column("Formula","formula");
		$grid->column("Fecha","fecha");
		$grid->add("nomina/nomina/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Nomina</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("clientes", "nomina");
		$edit->back_url = site_url("nomina/nomina/filteredgrid");
		//$edit->script($script, "create");
		//$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
		
		$edit->numero =  new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->maxlength=8;
		$edit->numero->size =10;
		$edit->numero->rule ="required";
		
		$edit->frecuencia = new dropdownField("Tipo de N&oacute;mina", "frecuencia");
		$edit->frecuencia->option("","");
		$edit->frecuencia->options(array("Q"=> "Quincenal","M"=>"Mensual","S"=>"Semanal"));
		$edit->frecuencia->style = "width:100px;";
		
		$edit->contrato = new dropdownField("Contrato", "contrato");
		$edit->contrato->option("","");
		$edit->contrato->options('SELECT codigo, nombre FROM noco');
		$edit->contrato->style = "width:300px;";

		$edit->depto = new dropdownField("Departamento", "depto");
		$edit->depto->option("","");
		$edit->depto->options('SELECT departa,descrip FROM depa');
		$edit->depto->style = "width:200px;";

		$edit->codigo = new dropdownField("C&oacute;digo", "codigo"); 
		//$edit->codigo->_dataobject->db_name="trim(codigo)";  
		$edit->codigo->option("","");
		$edit->codigo->options("SELECT codigo,concat(trim(apellido),' ',trim(nombre)) nombre FROM pers ORDER BY apellido");
		$edit->codigo->style = "width:100px;";
		$edit->codigo->mode="autohide";
		
		$edit->nombre =  new inputField("Nombre", "nombre");
		$edit->nombre->mode="autohide";
		$edit->nombre->maxlength=30;
		$edit->nombre->size=40;
		
		$edit->concepto = new dropdownField("Concepto", "concepto");
		$edit->concepto->option("","");
		$edit->concepto->options('SELECT concepto,descrip FROM conc ORDER BY descrip');
		$edit->concepto->style = "width:200px;";
		
		$edit->tipo =  new inputField("Tipo","tipo");
		$edit->tipo->option("A","A");
		$edit->tipo->option("D","D");
		$edit->tipo->mode="autohide";
		$edit->tipo->style = "width:50px;";
  
		$edit->descrip =  new inputField("T. Descripci&oacute;n", "descrip");
		$edit->descrip->mode="autohide";
		$edit->descrip->maxlength=35;
		$edit->descrip->size =45;
		
		$edit->grupo =  new inputField("Grupo", "grupo");
		$edit->grupo->maxlength=4;
		$edit->grupo->size =6;
		
		$edit->formula =  new inputField("Formula", "formula");
		$edit->formula->maxlength=120;
		$edit->formula->size =80;
		
		$edit->monto = new inputField("Monto","monto");
		$edit->monto->size =17;
		$edit->monto->maxlength=14;
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='numeric';
			
		$edit->fecha =  new DateonlyField("fecha", "fecha","d/m/Y");
		$edit->fecha->size = 12;
	
		$edit->cuota =  new inputField("Cuota", "cuota");
		$edit->cuota->maxlength=11;
		$edit->cuota->size =13;
		$edit->cuota->css_class='inputnum';
		$edit->cuota->rule='integer';
		
		$edit->cuotat =  new inputField("Cuota Total", "cuotat");
		$edit->cuotat->maxlength=11;
		$edit->cuotat->size =13;
		$edit->cuotat->css_class='inputnum';
		$edit->cuotat->rule='integer';
		
		$edit->valor =  new inputField("Valor", "valor");
		$edit->valor->maxlength=17;
		$edit->valor->size =20;
		$edit->valor->css_class='inputnum';
		$edit->valor->rule='numeric';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = $edit->output;
		$data['title']   = "<h1>Nomina</h1>";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function grid() {
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"fecha","direction":"DESC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;
		
		$where = $this->datasis->extjsfiltro($filters);

		$this->db->_protect_identifiers=false;
		$this->db->select('a.numero, a.fecha, a.contrato, sum(a.valor*(a.valor>0)) asigna, ABS(sum(a.valor*(a.valor<0))) deduc, b.nombre noconom');

		$this->db->from('nomina a');
		$this->db->join('noco b', 'a.contrato=b.codigo');
		$this->db->groupby('a.numero');
		if (strlen($where)>1){$this->db->where($where);	}

		$sort = json_decode($sort, true);
		for ($i=0;$i<count($sort);$i++) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);
		$query = $this->db->get();
		$results =  $this->datasis->dameval("SELECT COUNT(*) FROM (SELECT numero FROM nomina GROUP BY numero) aaa");
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function gridtraba(){
		$nomina   = isset($_REQUEST['nomina'])  ? $_REQUEST['nomina']   :  0;
		if ($nomina == 0 ) $nomina = $this->datasis->dameval("SELECT MAX(numero) FROM nomina")  ;
		$mSQL = "SELECT codigo, nombre, sum(valor*(valor>0)*(MID(concepto,1,1)<>9)) asigna,  sum(valor*(valor<0)*(MID(concepto,1,1)<>9)) deduc, sum(valor*(MID(concepto,1,1)<>9)) saldo FROM nomina WHERE numero='$nomina' GROUP BY codigo";
		$query = $this->db->query($mSQL);
		$results =  $this->datasis->dameval("SELECT COUNT(*) FROM (SELECT codigo FROM nomina WHERE numero='$nomina' GROUP BY codigo) aaa");
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function gridconc(){
		$nomina   = isset($_REQUEST['nomina'])  ? $_REQUEST['nomina']   :  0;
		$codigo   = isset($_REQUEST['codigo'])  ? $_REQUEST['codigo']   :  0;
		if ($nomina == 0 ) $nomina = $this->datasis->dameval("SELECT MAX(numero) FROM nomina")  ;
		if ($codigo == 0 ) $codigo = $this->datasis->dameval("SELECT MIN(codigo) FROM nomina WHERE numero='$nomina'")  ;
		$mSQL = "SELECT concepto, descrip, valor*(valor>0) asigna, valor*(valor<0) deduc FROM nomina WHERE numero='$nomina' AND trim(codigo)='$codigo' ORDER BY concepto ";
		$query = $this->db->query($mSQL);
		$results =  $this->datasis->dameval("SELECT COUNT(*) FROM (SELECT codigo FROM nomina WHERE numero='$nomina' AND codigo='$codigo' GROUP BY concepto) aaa");
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}



//****************************************************************
//
//
//
//****************************************************************
	function nomiextjs(){
		$encabeza='<table width="100%" bgcolor="#2067B5"><tr><td align="left" width="100px"><img src="'.base_url().'assets/default/css/templete_01.jpg" width="120"></td><td align="center"><h1 style="font-size: 20px; color: rgb(255, 255, 255);" onclick="history.back()">NOMINAS GUARDADAS</h1></td><td align="right" width="100px"><img src="'.base_url().'assets/default/images/cerrar.png" alt="Cerrar Ventana" title="Cerrar Ventana" onclick="parent.window.close()" width="25"></td></tr></table>';

		$listados= $this->datasis->listados('nomi');
		$otros=$this->datasis->otros('nomi', 'nomina/nomina');

		$script = "
<script type=\"text/javascript\">
var BASE_URL   = '".base_url()."';
var BASE_PATH  = '".base_url()."';
var BASE_ICONS = '".base_url()."assets/icons/';
var BASE_UX    = '".base_url()."assets/js/ext/ux';

Ext.Loader.setConfig({ enabled: true });
Ext.Loader.setPath('Ext.ux', BASE_UX);

var urlApp = '".base_url()."';
var numeroactual = '';

Ext.require([
	'Ext.grid.*',
	'Ext.ux.grid.FiltersFeature',
	'Ext.data.*',
	'Ext.util.*',
	'Ext.tree.*',
	'Ext.state.*',
	'Ext.form.*',
	'Ext.window.MessageBox',
	'Ext.tip.*',
	'Ext.ux.CheckColumn',
	'Ext.toolbar.Paging',
	'Ext.dd.*'
]);

var mxs = ((screen.availWidth/2)-400);
var mys = ((screen.availHeight/2)-300);

var tipos = new Ext.data.SimpleStore({
    fields: ['abre', 'todo'],
    data : [ ['Q','Quincenal'],['S','Semanal'],['B','Bisemanal'],['M','Mensual'],['O','Otros'] ]
});

//Column Model
var NomiCol = 
	[
		{ header: 'Numero',       width:  60, sortable: true,  dataIndex: 'numero',   field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Fecha',        width:  70, sortable: true,  dataIndex: 'fecha',    field: { type: 'datefield' }, filter: { type: 'date' }}, 
		{ header: 'Contrato',     width:  70, sortable: true,  dataIndex: 'contrato', field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Asignaciones', width:  80, sortable: true,  dataIndex: 'asigna',   field: { type: 'textfield' }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Deducciones',  width:  80, sortable: true,  dataIndex: 'deduc',    field: { type: 'textfield' }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Nombre',       width: 250, sortable: true,  dataIndex: 'noconom',  field: { type: 'textfield' }, filter: { type: 'string' }}
	];

//Column Model
var TrabaCol = 
	[
		{ header: 'Codigo',     width:  50, sortable: true,  dataIndex: 'codigo',   field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Nombre',     width: 170, sortable: true,  dataIndex: 'nombre',   field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Asignacion', width:  80, sortable: true,  dataIndex: 'asigna',   field: { type: 'textfield' }, filter: { type: 'string' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Deduccion',  width:  60, sortable: true,  dataIndex: 'deduc',    field: { type: 'textfield' }, filter: { type: 'string' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Saldo',      width:  80, sortable: true,  dataIndex: 'saldo',    field: { type: 'textfield' }, filter: { type: 'string' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')}
	];

//Column Model
var ConcCol = 
	[
		{ header: 'Conc.',       width:  40, sortable: true,  dataIndex: 'concepto', field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Descripcion', width: 150, sortable: true,  dataIndex: 'descrip',  field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Asignacion',  width:  80, sortable: true,  dataIndex: 'asigna',   field: { type: 'textfield' }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')},
		{ header: 'Deduccion',   width:  60, sortable: true,  dataIndex: 'deduc',    field: { type: 'textfield' }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00')}
	];

var nomina = '';
Ext.onReady(function(){
	/////////////////////////////////////////////////
	// Define los data model
	// Contratos
	Ext.define('Nomi', {
		extend: 'Ext.data.Model',
		fields: ['numero', 'fecha','contrato','noconom','asigna','deduc'],
		proxy: {
			type: 'ajax',
			noCache: false,
			api: {
				read   : urlApp + 'nomina/nomina/grid',
				method: 'POST'
			},
			reader: {
				type: 'json',
				root: 'data',
				successProperty: 'success',
				messageProperty: 'message',
				totalProperty: 'results'
			}
		}
	});	

	//////////////////////////////////////////////////////////
	// create the Data Store
	var storeNomi = Ext.create('Ext.data.Store', {
		model: 'Nomi',
		autoLoad: false,
		autoSync: true,
		method: 'POST'
	});

	/////////////////////////////////////////////////
	// Define los data model
	// Contratos
	Ext.define('Traba', {
		extend: 'Ext.data.Model',
		fields: ['codigo', 'nombre', 'saldo', 'asigna', 'deduc' ],
		proxy: {
			type: 'ajax',
			noCache: false,
			api: {
				read   : urlApp + 'nomina/nomina/gridtraba',
				method: 'POST'
			},
			reader: {
				type: 'json',
				root: 'data',
				successProperty: 'success',
				messageProperty: 'message',
				totalProperty: 'results'
			}
		}
	});

	//////////////////////////////////////////////////////////
	// create the Data Store
	var storeTraba = Ext.create('Ext.data.Store', {
		model: 'Traba',
		autoLoad: false,
		autoSync: true,
		method: 'POST'
	});

	//////////////////////////////////////////////////////////////////
	// create the grid and specify what field you want
	// to use for the editor at each column.
	var gridTraba = Ext.create('Ext.grid.Panel', {
		width: '100%',
		height: '100%',
		store: storeTraba,
		title: 'Trabajadores',
		iconCls: 'icon-grid',
		frame: true,
		columns: TrabaCol
	});

	/////////////////////////////////////////////////
	// Define los data model
	// Contratos
	Ext.define('Conc', {
		extend: 'Ext.data.Model',
		fields: ['concepto', 'descrip', 'asigna', 'deduc' ],
		proxy: {
			type: 'ajax',
			noCache: false,
			api: {
				read   : urlApp + 'nomina/nomina/gridconc',
				method: 'POST'
			},
			reader: {
				type: 'json',
				root: 'data',
				successProperty: 'success',
				messageProperty: 'message',
				totalProperty: 'results'
			}
		}
	});	

	//////////////////////////////////////////////////////////
	// create the Data Store
	var storeConc = Ext.create('Ext.data.Store', {
		model: 'Conc',
		autoLoad: false,
		autoSync: true,
		method: 'POST'
	});


	//////////////////////////////////////////////////////////////////
	// create the grid and specify what field you want
	// to use for the editor at each column.
	var gridConc = Ext.create('Ext.grid.Panel', {
		width: '100%',
		height: '100%',
		store: storeConc,
		title: 'Conceptos',
		iconCls: 'icon-grid',
		frame: true,
		columns: ConcCol
	});

	//////////////////////////////////////////////////////////////////
	// create the grid and specify what field you want
	// to use for the editor at each column.
	var gridNomi = Ext.create('Ext.grid.Panel', {
		width: '100%',
		height: '100%',
		store: storeNomi,
		title: 'Nominas Guardadas',
		iconCls: 'icon-grid',
		frame: false,
		columns: NomiCol,
		features: [ { ftype: 'filters', encode: 'json', local: false } ],
		dockedItems: [{
			xtype: 'toolbar',
			items: [{iconCls: 'icon-delete', text: 'Eliminar',  disabled: true, itemId: 'delete', scope: this, handler: this.onDeleteClick } ]
		}],
		// paging bar on the bottom
		bbar: Ext.create('Ext.PagingToolbar', {
			store: storeNomi,
			displayInfo: false,
			displayMsg: 'Pag No. {0} - Reg. {1} de {2}',
			emptyMsg: 'No se encontraron Registros.'
		}),
		onSelectChange: function(selModel, selections){	down('#delete').setDisabled(selections.length === 0); },
		onDeleteClick: function() {
			var selection = this.getView().getSelectionModel().getSelection()[0];
			Ext.MessageBox.show({
				title: 'Confirme', 
				msg: 'Esta seguro?', 
				buttons: Ext.MessageBox.YESNO, 
				fn: function(btn){ 
					if (btn == 'yes') { 
						if (selection) {
							//storeNomi.remove(selection);
						}
						storeNomi.load();
					} 
				}, 
				icon: Ext.MessageBox.QUESTION 
			});  
		},
	});

	// Al cambiar seleccion de Nomina
	gridNomi.getSelectionModel().on('selectionchange', function(sm, selectedRecord) {
		if (selectedRecord.length) {
			gridNomi.down('#delete').setDisabled(selectedRecord.length === 0);
			nomina = selectedRecord[0].data.numero;
			gridTraba.setTitle(nomina+' '+selectedRecord[0].data.noconom);
			storeTraba.load({ params: { nomina: nomina }});
			storeConc.load({ params: { nomina: nomina }});
		}
	});

	// update panel body on selection change
	gridTraba.getSelectionModel().on('selectionchange', function(sm, selectedRecord) {
		if (selectedRecord.length) {
			storeConc.load({ params: { nomina: nomina, codigo: selectedRecord[0].data.codigo }});
			gridConc.setTitle(selectedRecord[0].data.nombre);
		}
	});


//////************ MENU DE ADICIONALES /////////////////
".$listados."

//////************ FIN DE ADICIONALES /////////////////

	var viewport = new Ext.Viewport({id:'simplevp', layout:'border', border:false, items:[{region: 'north',preventHeader: true,height: 40,minHeight: 40,html: '".$encabeza."'},{region:'west',width:190,border:false,autoScroll:true,title:'Lista de Opciones',	collapsible:true,split:true,collapseMode:'mini',layoutConfig:{animate:true},layout: 'accordion',items: [{title:'Listados',border:false,layout: 'fit',items: gridListado},{title:'Otras Funciones',border:false,layout: 'fit',html: '".$otros."'}]},{region:'east',	id: 'este',width:340,items: gridConc,border:false,preventHeader: true,collapsible:true	},{cls: 'irm-column irm-center-column irm-master-detail', region: 'center', title:  'center-title', layout: 'border', preventHeader: true, border: false, items: [{itemId: 'viewport-center-master',cls: 'irm-master',region: 'center',items: gridNomi},{itemId: 'viewport-center-detail',preventHeader: true,region: 'south',height: '40%',split: true, title: 'center-detail-title',margins: '0 0 0 0',items: gridTraba}]}]});
	storeNomi.load();
	storeTraba.load();
	storeConc.load();
});

</script>
";
		return $script;	

	}

	function _post_insert($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('nomina',"NOMINA $codigo NOMBRE  $nombre CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('nomina',"NOMINA $codigo NOMBRE  $nombre  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('numero');
		$nombre=$do->get('nombre');
		logusu('nomina',"NOMINA $codigo NOMBRE  $nombre  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo=$this->input->post('codigo');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE codigo='$codigo'");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM pers WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"Personal con el codigo $codigo nombre $nombre ya existe");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
*/ 
}
?>
