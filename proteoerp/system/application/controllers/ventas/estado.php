<?php
class Estado extends Controller {
	var $mModulo = 'ESTADO';
	var $titp    = 'ENTIDADES';
	var $tits    = 'ENTIDADES';
	var $url     = 'ventas/estado/';

	function Estado(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'ESTADO', $ventana=0 );
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

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('ESTADO', 'JQ');
		$param['otros']       = $this->datasis->otros('ESTADO', 'JQ');
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
		function estadosadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function estadosedit(){
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
		function estadosshow(){
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
		function estadosdel() {
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
			autoOpen: false, height: 320, width: 500, modal: true,
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


		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('entidad');
		$grid->label('Entidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:80, maxlength: 80 }',
		));


		$grid->addField('capital');
		$grid->label('Capital');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:80, maxlength: 80 }',
		));


		$grid->addField('superficie');
		$grid->label('Superficie');
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


		$grid->addField('poblacion');
		$grid->label('Poblacion');
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


		$grid->addField('municipios');
		$grid->label('Municipios');
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


		$grid->addField('parroquias');
		$grid->label('Parroquias');
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
		$grid->setAdd(    $this->datasis->sidapuede('ESTADO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('ESTADO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('ESTADO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('ESTADO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: estadosadd, editfunc: estadosedit, delfunc: estadosdel, viewfunc: estadosshow");

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
		$mWHERE = $grid->geneTopWhere('estado');

		$response   = $grid->getData('estado', array(array()), array(), false, $mWHERE, 'codigo' );
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
		$mcodp  = "??????";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM estado WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('estado', $data);
					echo "Registro Agregado";

					logusu('ESTADO',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM estado WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM estado WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE estado SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("estado", $data);
				logusu('ESTADO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('estado', $data);
				logusu('ESTADO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM estado WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM estado WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM estado WHERE id=$id ");
				logusu('ESTADO',"Registro ????? ELIMINADO");
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

		$edit = new DataEdit('', 'estado');

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

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule='integer';
		$edit->codigo->css_class='inputonlynum';
		$edit->codigo->size =6;
		$edit->codigo->maxlength =10;
		$edit->codigo->mode = 'autohide';

		$edit->entidad = new inputField('Entidad','entidad');
		$edit->entidad->rule='';
		$edit->entidad->size =30;
		$edit->entidad->maxlength =80;

		$edit->capital = new inputField('Capital','capital');
		$edit->capital->rule='';
		$edit->capital->size =30;
		$edit->capital->maxlength =80;

		$edit->superficie = new inputField('Superficie','superficie');
		$edit->superficie->rule='numeric';
		$edit->superficie->css_class='inputnum';
		$edit->superficie->size =12;
		$edit->superficie->maxlength =10;

		$edit->poblacion = new inputField('Poblacion','poblacion');
		$edit->poblacion->rule='integer';
		$edit->poblacion->css_class='inputonlynum';
		$edit->poblacion->size =13;
		$edit->poblacion->maxlength =11;

		$edit->municipios = new inputField('Municipios','municipios');
		$edit->municipios->rule='integer';
		$edit->municipios->css_class='inputonlynum';
		$edit->municipios->size =13;
		$edit->municipios->maxlength =11;

		$edit->parroquias = new inputField('Parroquias','parroquias');
		$edit->parroquias->rule='integer';
		$edit->parroquias->css_class='inputonlynum';
		$edit->parroquias->size =13;
		$edit->parroquias->maxlength =11;

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
		if (!$this->db->table_exists('estado')) {
			$mSQL="
			CREATE TABLE IF NOT EXISTS `estado` (
				id          int(10) NOT NULL AUTO_INCREMENT,
				codigo      int(10) NOT NULL DEFAULT '0',
				entidad     varchar(80) DEFAULT NULL,
				capital     varchar(80) DEFAULT NULL,
				superficie  decimal(10,2) DEFAULT NULL,
				poblacion   int(11) DEFAULT NULL,
				municipios  int(11) DEFAULT NULL,
				parroquias  int(11) DEFAULT NULL,
				PRIMARY KEY (id),
				UNIQUE KEY `codigo` (`codigo`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
			$this->db->query($mSQL);

			$mSQL="
			INSERT INTO `estado` (`id`, `codigo`, `entidad`, `capital`, `superficie`, `poblacion`, `municipios`, `parroquias`) VALUES
				( 1, 22, 'AMAZONAS ', 'Puerto Ayacucho', 180145.00, 144398, 7, 23),
				( 2,  2, 'ANZOÁTEGUI', 'Barcelona', 43000.00, 1464578, 21, 49),
				( 3,  3, 'APURE', 'San Fernando de Apure', 76500.00, 458369, 7, 26),
				( 4,  4, 'ARAGUA', 'Maracay', 7014.00, 1627141, 18, 44),
				( 5,  5, 'BARINAS', 'Barinas', 35200.00, 814288, 12, 52),
				( 6,  6, 'BOLÍVAR ', 'Bolívar ', 238000.00, 1405064, 11, 44),
				( 7,  7, 'CARABOBO', 'Valencia ', 4650.00, 2239222, 14, 38),
				( 8,  8, 'COJEDES', 'San Carlos', 14800.00, 322843, 9, 15),
				( 9, 23, 'DELTA AMACURO ', 'Tucupita', 40200.00, 167522, 4, 21),
				(26, 99, 'EMBAJADAS', '', 0.00, 0, 0, 0),
				(11,  1, 'DISTRITO CAPITAL ', 'Caracas', 433.00, 1933186, 1, 22),
				(12,  9, 'FALCÓN ', 'Coro ', 24800.00, 900211, 25, 78),
				(13, 10, 'GUÁRICO', 'San Juan de los Morros ', 64986.00, 746174, 15, 38),
				(14, 11, 'LARA ', 'Barquisimeto', 19800.00, 1769763, 9, 58),
				(15, 12, 'MÉRIDA', 'Mérida', 11300.00, 826720, 23, 55),
				(16, 13, 'MIRANDA', 'Los Teques', 7950.00, 2665596, 21, 31),
				(17, 14, 'MONAGAS ', 'Maturín ', 28900.00, 901161, 13, 67),
				(18, 15, 'NUEVA ESPARTA ', 'La Asunción', 1150.00, 490494, 11, 11),
				(19, 16, 'PORTUGUESA', 'Guanare ', 15200.00, 875000, 14, 27),
				(20, 17, 'SUCRE ', 'Cumaná ', 11800.00, 892990, 15, 55),
				(21, 18, 'TÁCHIRA ', 'San Cristóbal', 11100.00, 1163593, 29, 93),
				(22, 19, 'TRUJILLO ', 'Trujillo ', 7400.00, 684555, 20, 38),
				(23, 24, 'VARGAS ', 'La Guaira ', 1496.00, 352087, 1, 11),
				(24, 20, 'YARACUY ', 'San Felipe ', 7100.00, 599345, 14, 7),
				(25, 21, 'ZULIA ', 'Maracaibo ', 63100.00, 3703640, 21, 106),
				(27, 98, 'FRONTERA', '', 0.00, 0, 0, 0);";
			$this->db->query($mSQL);

		}
		//$campos=$this->db->list_fields('estados');
		//if(!in_array('<#campo#>',$campos)){ }


	}
}

?>



