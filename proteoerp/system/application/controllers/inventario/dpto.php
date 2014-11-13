<?php require_once(BASEPATH.'application/controllers/validaciones.php');

class Dpto extends Controller {
	var $mModulo = 'DPTO';
	var $titp    = 'Departamentos';
	var $tits    = 'Departamentos';
	var $url     = 'inventario/dpto/';

	function Dpto(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'DPTO', $ventana=0 );
	}

	function index(){
		/*if ( !$this->datasis->iscampo('dpto','id') ) {
			$this->db->simple_query('ALTER TABLE dpto DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE dpto ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE dpto ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};*/
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

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('DPTO', 'JQ');
		$param['otros']       = $this->datasis->otros('DPTO', 'JQ');
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

		$bodyscript .= '
		function dptoadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function dptoedit(){
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
		function dptoshow(){
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
		function dptodel() {
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
			autoOpen: false, height: 350, width: 450, modal: true,
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
			autoOpen: false, height: 350, width: 450, modal: true,
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

		$grid->addField('depto');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 3 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			//'editoptions'   => '{ size:1, maxlength: 1 }',
			'editoptions'   => '{value: {"I":"Inventario","G":"Gasto"}, style:"width:100px" }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('cu_venta');
		$grid->label('Cta. Venta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_inve');
		$grid->label('Cta. Inventario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_cost');
		$grid->label('Cta. de Costo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_devo');
		$grid->label('Cta. Devoluci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
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
		$grid->setAdd(    $this->datasis->sidapuede('DPTO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('DPTO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('DPTO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('DPTO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: dptoadd, editfunc: dptoedit, delfunc: dptodel, viewfunc: dptoshow');

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
		$mWHERE = $grid->geneTopWhere('dpto');

		$response   = $grid->getData('dpto', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$mcodp  = '??????';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM dpto WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('dpto', $data);
					echo "Registro Agregado";

					logusu('DPTO',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM dpto WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM dpto WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE dpto SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("dpto", $data);
				logusu('DPTO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('dpto', $data);
				logusu('DPTO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM dpto WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM dpto WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM dpto WHERE id=$id ");
				logusu('DPTO',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit($status='',$id=''){
		$this->rapyd->load('dataobject','dataedit');

		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link=site_url('inventario/dpto/ultimo');
		$link2=site_url('inventario/common/sugerir_dpto');

		$script='
		function ultimo(){
			$.ajax({
				url: "'.$link.'",
				success: function(msg){
				  alert( "El ultimo codigo ingresado fue: " + msg );
				}
			});
		}

		function sugerir(){
			$.ajax({
					url: "'.$link2.'",
					success: function(msg){
						if(msg){
							$("#depto").val(msg);
						}
						else{
							alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
						}
					}
				});
		}
		';

		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			'p_uri'=>array(4=>'<#i#>')
		);

		$bcu_venta = $this->datasis->p_modbus($modbus,'cu_venta');
		$bcu_inve  = $this->datasis->p_modbus($modbus,'cu_inve' );
		$bcu_cost  = $this->datasis->p_modbus($modbus,'cu_cost' );
		$bcu_devo  = $this->datasis->p_modbus($modbus,'cu_devo' );

		$do = new DataObject('dpto');
		$do->set('tipo', 'I');
		if($status=='create' && !empty($id)){
			$do->load($id);
			$do->set('depto', '');
		}

		$edit = new DataEdit('', $do);
		$edit->on_save_redirect=false;
		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'delete','_pre_delete' );

		$ultimo ='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->depto = new inputField('C&oacute;digo Departamento', 'depto');
		$edit->depto->mode='autohide';
		$edit->depto->size=5;
		$edit->depto->maxlength=2;
		$edit->depto->rule ='trim|strtoupper|required|callback_chexiste|alpha_numeric';
		$edit->depto->append($sugerir);
		$edit->depto->append($ultimo);

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->size =35;
		$edit->descrip->maxlength=30;
		$edit->descrip->rule ='trim|required|strtoupper';

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->style='width:140px;';
		$edit->tipo->option('I','Inventario');
		$edit->tipo->option('G','Gasto');
		$edit->tipo->option('A','Ambos');

		$edit->cu_inve =new inputField('Cuenta Inventario', 'cu_inve');
		$edit->cu_inve->size = 18;
		$edit->cu_inve->maxlength=15;
		$edit->cu_inve->rule ='trim|existecpla';
		$edit->cu_inve->append($bcu_inve);

		$edit->cu_cost =new inputField('Cuenta Costo', 'cu_cost');
		$edit->cu_cost->size = 18;
		$edit->cu_cost->maxlength=15;
		$edit->cu_cost->rule ='trim|existecpla';
		$edit->cu_cost->append($bcu_cost);

		$edit->cu_venta  =new inputField('Cuenta Venta', 'cu_venta');
		$edit->cu_venta->size =18;
		$edit->cu_venta->maxlength=15;
		$edit->cu_venta->rule ='trim|existecpla';
		$edit->cu_venta->append($bcu_venta);

		$edit->cu_devo = new inputField('Cuenta Devoluci&oacute;n','cu_devo');
		$edit->cu_devo->size = 18;
		$edit->cu_devo->maxlength=15;
		$edit->cu_devo->rule ='trim|existecpla';
		$edit->cu_devo->append($bcu_devo);

		//$edit->buttons("modify","delete", "save", "undo", "back");
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

	function _post_insert($do){
		$codigo=$do->get('depto');
		$nombre=$do->get('descrip');
		logusu('dpto',"DEPARTAMENTO ${codigo} NOMBRE  ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('depto');
		$nombre=$do->get('descrip');
		logusu('dpto',"DEPARTAMENTO ${codigo} NOMBRE  ${nombre}  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('depto');
		$nombre=$do->get('descrip');
		logusu('dpto',"DEPARTAMENTO ${codigo} NOMBRE  ${nombre}  ELIMINADO ");
	}

	function _pre_delete($do) {
		$codigo  = $do->get('depto');
		$dbcodigo= $this->db->escape($codigo);
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM line WHERE depto=${dbcodigo}");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El departamento contiene lineas, por ello no puede ser eliminado. Elimine primero todas las l&iacute;neas que pertenezcan a este departamento';
			return false;
		}
		return true;
	}

	function _pre_insert($do){
		$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function chexiste($codigo){
		$codigo  = $this->input->post('depto');
		$dbcodigo= $this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM dpto WHERE depto=${dbcodigo}");
		if ($check > 0){
			$depto=$this->datasis->dameval("SELECT descrip FROM dpto WHERE depto=${dbcodigo}");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el departamento ${depto}");
			return false;
		}else {
  		return true;
		}
	}

	function ultimo(){
		$ultimo=$this->datasis->dameval('SELECT depto FROM dpto WHERE depto<95 ORDER BY depto DESC LIMIT 1');
		echo $ultimo;
	}

	function instalar(){
		//if (!$this->db->table_exists('dpto')) {
		//	$mSQL="CREATE TABLE `dpto` (
		//	  `tipo` char(1) NOT NULL DEFAULT 'I',
		//	  `depto` char(3) NOT NULL DEFAULT '',
		//	  `descrip` varchar(30) DEFAULT NULL,
		//	  `cu_venta` varchar(15) DEFAULT NULL,
		//	  `cu_inve` varchar(15) DEFAULT NULL,
		//	  `cu_cost` varchar(15) DEFAULT NULL,
		//	  `cu_devo` varchar(15) DEFAULT NULL,
		//	  `id` int(11) NOT NULL AUTO_INCREMENT,
		//	  PRIMARY KEY (`id`),
		//	  UNIQUE KEY `depto` (`depto`),
		//	  KEY `depto_2` (`depto`)
		//	) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COMMENT='Departamentos de Inv'";
		//	$this->db->simple_query($mSQL);
		//}

		$campos=$this->db->list_fields('dpto');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE dpto DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE dpto ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE dpto ADD UNIQUE INDEX depto (depto)');
		}

		//$this->db->simple_query("INSERT IGNORE INTO dpto (depto,tipo,descrip) VALUES ('99','G','INVERSION EN ACTIVOS')ON DUPLICATE KEY UPDATE depto='99', tipo='G',descrip='INVERSION EN ACTIVOS'");
		//$this->db->simple_query("INSERT IGNORE INTO dpto (depto,tipo,descrip) VALUES ('98','G','GASTOS FINANCIEROS')ON DUPLICATE KEY UPDATE depto='98', tipo='G',descrip='GASTOS FINANCIEROS'");
		//$this->db->simple_query("INSERT IGNORE INTO dpto (depto,tipo,descrip) VALUES ('97','G','GASTOS DE ADMINISTRACION')ON DUPLICATE KEY UPDATE depto='97', tipo='G',descrip='GASTOS DE ADMINISTRACION'");
		//$this->db->simple_query("INSERT IGNORE INTO dpto (depto,tipo,descrip) VALUES ('96','G','GASTOS DE VENTA')ON DUPLICATE KEY UPDATE depto='96', tipo='G',descrip='GASTOS DE VENTA'");
		//$this->db->simple_query("INSERT IGNORE INTO dpto (depto,tipo,descrip) VALUES ('95','G','GASTOS DE COMPRA')ON DUPLICATE KEY UPDATE depto='95', tipo='G',descrip='GASTOS DE COMPRA'");

	}
}
