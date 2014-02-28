<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Ccont extends Controller {
	var $mModulo = 'CCONT';
	var $titp    = 'Contratos';
	var $tits    = 'Contratos';
	var $url     = 'finanzas/ccont/';

	function Ccont(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'CCONT', $ventana=0 );
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
		//$grid->wbotonadd(array("id"=>"edocta",  "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
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
		$param['listados']    = $this->datasis->listados('CCONT', 'JQ');
		$param['otros']       = $this->datasis->otros('CCONT', 'JQ');
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
		function ccontadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function ccontedit(){
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
		function ccontshow(){
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
		function ccontdel() {
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/CCONT').'/\'+res.id+\'/id\'').';
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

		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid o Tabla
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
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


		$grid->addField('obrap');
		$grid->label('Obrap');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
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


		$grid->addField('fecha_inicio');
		$grid->label('Fecha Inicio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('fecha_final');
		$grid->label('Fecha Final');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('detalle');
		$grid->label('Detalles');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('ccosto');
		$grid->label('C.Costo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('cod_prv');
		$grid->label('Proveedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:120, maxlength: 120 }',
		));


		$grid->addField('direccion');
		$grid->label('Direcci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:150, maxlength: 150 }',
		));


		$grid->addField('base');
		$grid->label('Base');
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


		$grid->addField('impuesto');
		$grid->label('Impuesto');
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


		$grid->addField('tota');
		$grid->label('Total');
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


		$grid->addField('tipo');
		$grid->label('Tipo');
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


		$grid->addField('rif');
		$grid->label('RIF');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('email');
		$grid->label('Email');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('telefono');
		$grid->label('Tel&eacute;fono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));


		$grid->addField('retencion');
		$grid->label('Retenci&oacute;n');
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
		$grid->setAdd(    $this->datasis->sidapuede('CCONT','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('CCONT','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('CCONT','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('CCONT','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: ccontadd, editfunc: ccontedit, delfunc: ccontdel, viewfunc: ccontshow');

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
		$mWHERE = $grid->geneTopWhere('ccont');

		$response   = $grid->getData('ccont', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM ccont WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('ccont', $data);
					echo "Registro Agregado";

					logusu('CCONT',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM ccont WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM ccont WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE ccont SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("ccont", $data);
				logusu('CCONT',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('ccont', $data);
				logusu('CCONT',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM ccont WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM ccont WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM ccont WHERE id=$id ");
				logusu('CCONT',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$modbus=array(
		'tabla'   =>'obpa',
		'columnas'=>array(
			'codigo'  =>'C&oacute;digo',
			'descrip' =>'descrip'
		),
		'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'descrip'),
		'retornar'=>array('codigo'=>'partida_<#i#>','descrip'=>'descrip_<#i#>'),
		'p_uri'   =>array(4=>'<#i#>'),
		'titulo'  =>'Buscar Partidas');

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'  =>'Nombre',
				'rif'     =>'RIF',
				'telefono'=>'Telefono',
				'email'   =>'Email'
			),
			'filtro'  =>array('proveed' =>'C&oacute;digo Proveedor','nombre'=>'Nombre','rif'=>'RIF'),
			'retornar'=>array('proveed'=>'cod_prv','nombre'=>'nombre','rif'=>'rif','telefono'=>'telefono'),
			'titulo'  =>'Buscar Proveedor');
		$bsprv =$this->datasis->modbus($mSPRV);

		$mccont=array(
		'tabla'   =>'ccont',
		'columnas'=>array(
			'numero'  =>'N&uacute;mero de Contrato',
			'cod_prv' =>'Proveedor',
			'nombre'  =>'Nombre'
		),
		'filtro'  =>array('numero' =>'Número de Contrato','titulo'=>'Titulo'),
		'retornar'=>array('numero'=>'obrap'),
		'titulo'  =>'Buscar Contrato');
		$bccont =$this->datasis->modbus($mccont);

		$script="
		function post_add_itccont(id){
			$('#cantidad_'+id).numeric('.');
			return true;
		}";

		$do = new DataObject('ccont');
		$do->rel_one_to_many('itccont', 'itccont', 'numero');

		$edit = new DataDetails('Contratos', $do);
		$edit->back_url = site_url('finanzas/ccont');
		$edit->set_rel_title('itccont','Partida <#o#>');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->obrap = new inputField('Obra Principal', 'obrap');
		$edit->obrap->size=12;
		$edit->obrap->maxlength=20;
		$edit->obrap->append($bccont);
		$edit->obrap->readonly=true;
		//$edit->obrap->rule = 'required';

		$edit->numero = new inputField('N&uacute;mero de contrato', 'numero');
		$edit->numero->when = array("show");
		$edit->numero->size = 10;
		$edit->numero->maxlength=20;
		$edit->numero->readonly=true;

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 12;
		$edit->fecha->rule = 'chfecha';
		$edit->fecha->rule = 'required';

		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->option('','Seleccione');
		$edit->tipo->options("SELECT codigo,descrip  FROM tipoc ORDER BY descrip");
		$edit->tipo->rule = 'required';

		$edit->cod_prv = new inputField('Proveedor', 'cod_prv');
		$edit->cod_prv->size=12;
		$edit->cod_prv->maxlength=20;
		$edit->cod_prv->append($bsprv);
		$edit->cod_prv->readonly=true;
		//$edit->cod_prv->rule = 'required';

		$lriffis='<a href="javascript:consulrif();" title="Consultar RIF en el SENIAT" onclick=""> Consultar RIF en el SENIAT</a>';
		$edit->rif =  new inputField('R.I.F.', 'rif');
		//$edit->rif->mode='autohide';
		$edit->rif->rule = 'strtoupper|callback_chci';
		$edit->rif->append($lriffis);
		$edit->rif->maxlength=20;
		$edit->rif->size =18;

		$edit->telefono = new inputField('Tel&eacute;fono', 'telefono');
		$edit->telefono->size = 30;
		$edit->telefono->maxlength =100;

		$edit->email = new inputField('E-mail', 'email');
		$edit->email->rule = 'trim|valid_email';
		$edit->email->size =30;
		$edit->email->maxlength =50;

		$edit->nombre = new inputField('Nombre o Raz&oacute;n Social','nombre');
		$edit->nombre->size=50;
		$edit->nombre->maxlength =100;
		$edit->nombre->rule = 'required|strtoupper';

		$edit->direccion = new textareaField('Direcci&oacute;n o Domicilio','direccion');
		$edit->direccion->rows = 3;
		$edit->direccion->cols = 60;
		$edit->direccion->rule = 'required|strtoupper';

		$edit->detalles = new textareaField('Detalle del Objeto Contractual','detalle');
		$edit->detalles->size=50;
		$edit->detalles->rows = 3;
		$edit->detalles->cols = 60;
		$edit->detalles->rule = 'required|strtoupper';

		$edit->base  = new inputField('Sub Total sin IVA', 'base');
		$edit->base->size = 20;
		$edit->base->css_class='inputnum';
		$edit->base->rule='numeric';
		$edit->base->group='Totales';
		//$edit->base->rule = 'required';
		$edit->base->readonly=true;

		$edit->impuesto  = new inputField('IVA', 'impuesto');
		$edit->impuesto->size = 20;
		$edit->impuesto->css_class='inputnum';
		$edit->impuesto->rule='numeric';
		$edit->impuesto->group="Totales";
		//$edit->impuesto->rule = 'required';
		$edit->impuesto->readonly=true;

		$edit->tota  = new inputField('Total Bs', 'tota');
		$edit->tota->size = 20;
		$edit->tota->css_class='inputnum';
		$edit->tota->rule='numeric';
		$edit->tota->group="Totales";
		//$edit->tota->rule = 'required';
		$edit->tota->readonly=true;

		$edit->retencion  = new inputField("Retenci&oacute;n por Garant&iacute;a", 'retencion');
		$edit->retencion->size =10;
		$edit->retencion->css_class='inputnum';
		$edit->retencion->rule='numeric';
		$edit->retencion->append(' %');

		$edit->fecha_inicio = new DateonlyField('Fecha de Inicio', 'fecha_inicio','d/m/Y');
		$edit->fecha_inicio->insertValue = date('Y-m-d');
		$edit->fecha_inicio->size = 12;
		$edit->fecha_inicio->rule='chfecha';
		$edit->fecha_inicio->rule = 'required';

		$edit->fecha_final = new DateonlyField('Fecha de Culminaci&oacute;n', 'fecha_final','d/m/Y');
		$edit->fecha_final->insertValue = date('Y-m-d');
		$edit->fecha_final->size = 12;
		$edit->fecha_final->rule='required|chfecha';

		$numero=$edit->_dataobject->get('numero');

		$edit->itccont = new containerField('numero',$this->_detalle($numero));
		$edit->itccont->when = array('show','modify');
		$edit->itccont->group = 'Totales';

		//Campos para el detalle

		$edit->partida = new inputField('Partida', 'partida_<#i#>');
		$edit->partida->size=18;
		$edit->partida->db_name='partida';
		$edit->partida->rel_id='itccont';
		$edit->partida->append($this->datasis->p_modbus($modbus,'<#i#>'));
		$edit->partida->readonly=TRUE;

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip_<#i#>');
		$edit->descrip->size=30;
		$edit->descrip->db_name='descrip';
		$edit->descrip->rel_id='itccont';
		$edit->descrip->readonly=TRUE;

		$edit->unidad  = new inputField('Unidad de Medida', 'unidad_<#i#>');
		$edit->unidad->size=12;
		$edit->unidad->rel_id='itccont';
		$edit->unidad->db_name='unidad';

		$edit->cantidad = new inputField('Cantidad', 'cantidad_<#i#>');
		$edit->cantidad->size=10;
		$edit->cantidad->db_name='cantidad';
		$edit->cantidad->maxlength=7;
		$edit->cantidad->rel_id='itccont';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->onchange ='cal_monto(<#i#>);';

		$edit->precio = new inputField('Precio Unitario', 'precio_<#i#>');
		$edit->precio->css_class='inputnum';
		$edit->precio->size=15;
		$edit->precio->rel_id='itccont';
		$edit->precio->db_name='precio';
		$edit->precio->onchange ='cal_monto(<#i#>);';

		$edit->monto = new inputField("Importe Total Bs", "monto_<#i#>");
		$edit->monto->db_name='monto';
		$edit->monto->size=15;
		$edit->monto->rel_id='itccont';
		$edit->monto->css_class='inputnum';
		$edit->monto->readonly=true;

		$edit->buttons('add_rel');
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
		$numero=$do->get('numero');
		$nombre=$do->get('nombre');
		$cod_prv=$do->get('cod_prv');
		$usuario=$this->session->userdata['usuario'];
		$Sql=$this->db->query("UPDATE ccont SET usuario=${usuario} WHERE numero='${numero}'");
		logusu('ccont',"CONTRATO ${numero} ${nombre} ${cod_prv} CREADO");
	}

	function _post_update($do){
		$numero=$do->get('numero');
		$nombre=$do->get('nombre');
		$cod_prv=$do->get('cod_prv');
		logusu('ccont',"CONTRATO ${numero} ${nombre} ${cod_prv} MODIFICADO");
	}

	function _post_delete($do){
		$numero =$do->get('numero');
		$nombre =$do->get('nombre');
		$cod_prv=$do->get('cod_prv');
		logusu('ccont',"CONTRATO ${numero} ${nombre} ${cod_prv} ELIMINADO");
	}


	function _actualiza_detalle($do){
		$this->_borra_detalle($do);
		$this->_guarda_detalle($do);
	}

	function _borra_detalle($do){
		$numero=$do->get('numero');
		$sql = "DELETE FROM itccont WHERE numero='${numero}'";
		$this->db->query($sql);
	}

	function _detalle($numero){
		$salida='No hay Observaciones';
		if(!empty($numero)){
			$this->rapyd->load('datagrid');

			$grid = new DataGrid('Lista de Observaciones');
			$select=array('id','numero','contenido','fecha','usuario');
			$grid->db->select($select);
			$grid->db->from('itccontb');
			$grid->db->where('numero',$numero);

			$grid->order_by('id','asc');
			$grid->per_page = 10;

			$grid->column('Fecha'      ,'<dbdate_to_human><#fecha#></dbdate_to_human>',"align='center'");
			$grid->column('Contenido'  ,'contenido',"align='left'");

			$grid->build();
			$salida=$grid->output;
			//Echo $grid->db->last_query();
		}
		return $salida;
	}

	function _guarda_detalle($do) {
		$cant=$this->input->post('cant_0');
		$i=$o=0;
		while($o<$cant){
			if(isset($_POST["partida$i"])){
				if($this->input->post("partida$i")){

					$sql = "INSERT INTO itccont (numero,partida,descrip,unidad,cantidad,precio,monto) VALUES(?,?,?,?,?,?,?)";
					$llena=array(
							0 =>$do->get('numero'),
							1 =>$this->input->post("partida$i"),
							2 =>$this->input->post("descrip$i"),
							3 =>$this->input->post("unidad$i"),
							4 =>$this->input->post("cantidad$i"),
							5 =>$this->input->post("precio$i"),
							6 =>$this->input->post("monto$i"));

					$this->db->query($sql,$llena);
				}
				$o++;
			}
			$i++;
		}
	}

	function observa($estado='',$numero=''){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit(' ','itccont');
		$edit->back_url = site_url('finanzas/ccont/');

		$edit->post_process('insert','_post_insert2');
		$edit->post_process('update','_post_update2');
		$edit->post_process('delete','_post_delete2');

		$edit->id = new inputField2('N&uacute;mero de Observación', 'id');
		$edit->id->when = array('show');

		$edit->numero = new inputField2('N&uacute;mero de Contrato', 'numero');
		$edit->numero->when = array("show","create");
		$edit->numero->readonly=TRUE;
		$edit->numero->insertValue ="$numero";
		$edit->numero->size = 12;

		$edit->fecha = new DateonlyField("Fecha ", "fecha","d/m/Y");
		$edit->fecha->insertValue = date("Y-m-d");
		$edit->fecha->size = 12;
		$edit->fecha->rule='required|chfecha';

		$edit->contenido = new textareaField("Contenido","contenido");
		$edit->contenido->rows = 3;
		$edit->contenido->cols = 60;
		$edit->contenido->rule = 'required|strtoupper';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		//$smenu['link']=barra_menu('230');
		//$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		$data['title']   = "<h1>Observaciones</h1>";
		$data['head']    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function instalar(){
		if(!$this->db->table_exists('ccont')){
			$mSQL="CREATE TABLE `ccont` (
			  `numero` int(10) NOT NULL AUTO_INCREMENT,
			  `obrap` varchar(20) DEFAULT NULL,
			  `fecha` date DEFAULT NULL,
			  `fecha_inicio` date DEFAULT NULL,
			  `fecha_final` date DEFAULT NULL,
			  `detalle` text,
			  `ccosto` varchar(50) DEFAULT NULL,
			  `cod_prv` varchar(20) DEFAULT NULL,
			  `nombre` varchar(120) DEFAULT NULL,
			  `direccion` varchar(150) DEFAULT NULL,
			  `usuario` varchar(20) DEFAULT NULL,
			  `base` decimal(10,2) DEFAULT NULL,
			  `impuesto` decimal(10,2) DEFAULT NULL,
			  `tota` decimal(10,2) DEFAULT NULL,
			  `tipo` int(3) DEFAULT NULL,
			  `rif` varchar(12) DEFAULT NULL,
			  `email` varchar(50) DEFAULT NULL,
			  `telefono` varchar(100) DEFAULT NULL,
			  `retencion` decimal(10,2) DEFAULT NULL,
			  PRIMARY KEY (`numero`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('itccont')){
			$mSQL="CREATE TABLE `itccont` (
				`partida` VARCHAR(50) NULL DEFAULT NULL,
				`descrip` VARCHAR(150) NULL DEFAULT NULL,
				`unidad` VARCHAR(15) NULL DEFAULT NULL,
				`cantidad` DECIMAL(10,2) NULL DEFAULT NULL,
				`monto` DECIMAL(10,2) NULL DEFAULT NULL,
				`precio` DECIMAL(10,2) NULL DEFAULT NULL,
				`numero` INT(10) NULL DEFAULT NULL,
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('itccontb')){
			$mSQL="CREATE TABLE `itccontb` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`numero` INT(10) NULL DEFAULT NULL,
				`contenido` VARCHAR(100) NULL DEFAULT NULL,
				`usuario` VARCHAR(20) NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
		}

		if(!$this->db->table_exists('obpa')){
			$mSQL="CREATE TABLE `obpa` (
			  `codigo` char(4) NOT NULL DEFAULT '',
			  `descrip` varchar(40) DEFAULT NULL,
			  `grupo` char(4) DEFAULT NULL,
			  `comision` decimal(5,2) DEFAULT NULL,
			  `nomgrup` varchar(30) DEFAULT NULL,
			  PRIMARY KEY (`codigo`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('tipoc')){
			$mSQL="CREATE TABLE `tipoc` (
			  `codigo` int(10) NOT NULL AUTO_INCREMENT,
			  `descrip` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`codigo`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
	}
}
