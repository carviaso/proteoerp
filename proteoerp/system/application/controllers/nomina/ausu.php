<?php
class Ausu extends Controller {
	var $mModulo = 'AUSU';
	var $titp    = 'Ajuste de sueldo';
	var $tits    = 'Ajuste de sueldo';
	var $url     = 'nomina/ausu/';
	var $genesal = true;
	var $msj     = '';

	function Ausu(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'AUSU', true);
	}

	function index(){
		$this->instalar();
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
		if($this->datasis->sidapuede('AUSU','INCLUIR%')){
			$grid->wbotonadd(array('id'=>'ausucol',   'img'=>'images/circulogris.png',  'alt' => '-', 'label'=>'Ajuste colectivo'));
		}
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
		$param['listados']    = $this->datasis->listados('AUSU', 'JQ');
		$param['otros']       = $this->datasis->otros('AUSU', 'JQ');
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
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function ausuadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function ausuedit(){
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
		$("#ausucol").click(function() {
			$.post("'.site_url($this->url.'masivo/').'", function(data){
				$("#fedita").html(data);
				$("#fedita").dialog("open");
			});
		});';

		$bodyscript .= '
		function ausushow(){
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
		function ausudel() {
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
			autoOpen: false, height: 360, width: 600, modal: true,
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

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
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
			'editoptions'   => '{ size:30, maxlength: 30 }',
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


		$grid->addField('sueldoa');
		$grid->label('Sueldo Anterior');
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


		$grid->addField('sueldo');
		$grid->label('Sueldo Nuevo');
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


		$grid->addField('observ1');
		$grid->label('Obervaci&oacute;nes');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:46, maxlength: 46 }',
		));


		$grid->addField('oberv2');
		$grid->label('Obervaci&oacute;nes');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:46, maxlength: 46 }',
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

		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('AUSU','INCLUIR%' ));
		$grid->setEdit(false);
		$grid->setDelete( $this->datasis->sidapuede('AUSU','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('AUSU','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: ausuadd, editfunc: ausuedit, delfunc: ausudel, viewfunc: ausushow');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if($deployed){
			return $grid->deploy();
		}else{
			return $grid;
		}
	}

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function getdata(){
		$grid = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('ausu');

		$response   = $grid->getData('ausu', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function setData(){

	}

	//******************************************************************
	// Edicion

	function dataedit(){
		$this->rapyd->load('dataedit');

		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");

			$("#codigo").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function(req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscapers').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#nombre").val("");
									$("#nombre_val").text("");

									$("#sueldoa").val("");
									$("#sueldoa_val").text("");
									//$("#rifci").val("");
									//$("#rifci_val").text("");
								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
								}
								add(sugiere);
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#codigo").attr("readonly", "readonly");

					$("#nombre").val(ui.item.nombre);
					$("#nombre_val").text(ui.item.nombre);

					$("#sueldoa").val(ui.item.sueldo);
					$("#sueldoa_val").text(nformat(ui.item.sueldo,2));

					//$("#rifci").val(ui.item.rifci);
					//$("#rifci_val").text(ui.item.rifci);

					setTimeout(function() {  $("#codigo").removeAttr("readonly"); }, 1500);
				}
			});
		});';

		//$pers=array(
		//	'tabla'   =>'pers',
		//	'columnas'=>array(
		//		'codigo'  =>'Codigo',
		//		'cedula'  =>'Cedula',
		//		'nombre'  =>'Nombre',
		//		'apellido'=>'Apellido'),
		//	'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
		//	'retornar'=>array('codigo'=>'codigo','nombre'=>'nombre'),
		//	'titulo'  =>'Buscar Personal'
		//);
		//$boton=$this->datasis->modbus($pers);

		$edit = new DataEdit('', 'ausu');
		$edit->on_save_redirect=false;
		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->size = 15;
		//$edit->codigo->append($boton);
		$edit->codigo->mode='autohide';
		$edit->codigo->maxlength=15;
		$edit->codigo->rule='required|callback_chexiste';
		$edit->codigo->group='Datos del Trabajador';

		$edit->nombre =  new inputField('Nombre', 'nombre');
		$edit->nombre->size =40;
		$edit->nombre->maxlength=30;
		$edit->nombre->type='inputhidden';
		$edit->nombre->group='Datos del Trabajador';

		$edit->sueldoa = new inputField('Sueldo actual', 'sueldoa');
		$edit->sueldoa->size = 14;
		$edit->sueldoa->type='inputhidden';
		$edit->sueldoa->css_class='inputnum';
		$edit->sueldoa->rule='positive';
		$edit->sueldoa->maxlength=11;
		$edit->sueldoa->group='Datos del Trabajador';

		$edit->fecha = new dateField('Apartir de la nomina', 'fecha');
		$edit->fecha->mode='autohide';
		$edit->fecha->size = 12;
		$edit->fecha->dbformat = 'Ymd';
		$edit->fecha->rule ='required|callback_fpositiva';
		$edit->fecha->calendar=false;

		$edit->sueldo = new inputField('Sueldo nuevo', 'sueldo');
		$edit->sueldo->size = 14;
		$edit->sueldo->css_class='inputnum';
		$edit->sueldo->rule='positive';
		$edit->sueldo->maxlength=11;

		$edit->observ1 = new inputField('Raz&oacute;n del ajuste', 'observ1');
		$edit->observ1->size = 51;
		$edit->observ1->maxlength=46;
		$edit->observ1->rule='required|strtoupper';

		$edit->oberv2 = new inputField('Observaciones', 'oberv2');
		$edit->oberv2->size =51;
		$edit->oberv2->maxlength=46;
		$edit->oberv2->rule = 'strtoupper';
		$edit->build();

		if($edit->on_success()){
			if($this->genesal){
				$rt=array(
					'status' =>'A',
					'mensaje'=>'Registro guardado',
					'pk'     =>$edit->_dataobject->pk
				);
				echo json_encode($rt);
			}else{
				return true;
			}
		}else{
			if($this->genesal){
				echo $edit->output;
			}else{
				$this->msj=$edit->error_string;
				return false;
			}
		}
	}

	function _pre_insert($do){
		$codigo  = $do->get('codigo');
		$fecha   = $do->get('fecha');
		$dbcodigo= $this->db->escape($codigo);
		$dbfecha = $this->db->escape($fecha);
		$sueldoa = $this->datasis->dameval("SELECT sueldo FROM pers  WHERE codigo=${dbcodigo}");
		$sueldob = $this->datasis->dameval("SELECT sueldo FROM ausu  WHERE fecha<=${dbfecha} ORDER BY fecha DESC LIMIT 1");

		$do->set('sueldoa',empty($sueldob)? $sueldoa: $sueldob);

		$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM ausu WHERE codigo=${dbcodigo} AND fecha=${dbfecha}"));
		if($cana>0){
			$do->error_message_ar['pre_ins']='Ya existe un ajuste de sueldo para la fecha seleccionada.';
			return false;
		}else{
			return true;
		}
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='No se puede modificar este tipo de registro, debe eliminarlo y volverlo a crear.';
		return false;
	}

	function _pre_delete($do){
		$fecha = $do->get('fecha');
		$codigo= $do->get('codigo');

		$dbfecha  = $this->db->escape($fecha );
		$dbcodigo = $this->db->escape($codigo);

		$cana = intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM ausu WHERE fecha>${dbfecha} AND codigo=${dbcodigo}"));
		if($cana>0){
			$do->error_message_ar['pre_del']='No se puede revesar un pago cuando existe un aumento con fecha posterior';
			return false;
		}
		return true;
	}

	function _post_insert($do){
		$codigo  = $do->get('codigo');
		$nombre  = $do->get('nombre');
		$fecha   = $do->get('fecha');
		$sueldo  = $do->get('sueldo');
		$dbcodigo= $this->db->escape($codigo);

		if(intval(date('Ymd'))>=intval(str_replace('-','',$fecha))){
			$mSQL = "UPDATE pers SET sueldo=${sueldo} WHERE codigo=${dbcodigo}";
			$this->db->simple_query($mSQL);
		}

		logusu('ausu',"AUMENTO DE SUELDO A ${codigo} NOMBRE  ${nombre} FECHA ${fecha} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		$fecha =$do->get('fecha');
		logusu('ausu',"AUMENTO DE SUELDO A ${codigo} NOMBRE  ${nombre} FECHA ${fecha} MODIFICADO");
	}

	function _post_delete($do){
		$codigo = $do->get('codigo');
		$nombre = $do->get('nombre');
		$fecha  = $do->get('fecha');
		$sueldoa= $do->get('sueldoa');
		$dbcodigo= $this->db->escape($codigo);

		if(intval(date('Ymd'))>=intval(str_replace('-','',$fecha))){
			$mSQL = "UPDATE pers SET sueldo=${sueldoa} WHERE codigo=${dbcodigo}";
			$this->db->simple_query($mSQL);
		}

		logusu('ausu',"AUMENTO DE SUELDO A ${codigo} NOMBRE  ${nombre} FECHA ${fecha} ELIMINADO ");
	}

	function masivo(){
		$this->rapyd->load('dataedit');

		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});';

		$edit = new DataForm($this->url.'masivo/insert');
		$edit->on_save_redirect=false;
		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->porcen = new inputField('Porcentaje de aumento', 'porcen');
		$edit->porcen->size = 12;
		$edit->porcen->css_class='inputnum';
		$edit->porcen->rule='numeric|required|floatval|nocero';
		$edit->porcen->append('% Coloque una cantidad positiva para aumentar y negativa para disminuir.');
		$edit->porcen->maxlength=11;

		$edit->fecha = new dateField('Apartir de la nómina', 'fecha');
		$edit->fecha->mode='autohide';
		$edit->fecha->size = 12;
		$edit->fecha->insertValue=date('Y-m-d',mktime(0, 0, 0, date('n'), 1));
		$edit->fecha->dbformat = 'Ymd';
		$edit->fecha->rule ='required|chfecha';
		$edit->fecha->calendar=false;

		$edit->tipo = new dropdownField('Frecuencia de pago','tipo');
		$edit->tipo->options(array(''=>'Todas','Q'=> 'Quincenal','M'=>'Mensual','S'=>'Semanal','B'=>'BiSemanal'));
		$edit->tipo->style = 'width:100px;';

		$edit->observ1 = new inputField('Raz&oacute;n', 'observ1');
		$edit->observ1->size = 51;
		$edit->observ1->maxlength=46;
		$edit->observ1->rule='required|strtoupper';

		$edit->container = new containerField('info','Esta opción aumentará o disminurá el sueldo según sea el caso a los trabajadores de manera líneal.');

		$edit->build_form();

		if($edit->on_success()){
			$porcen = $edit->porcen->newValue;
			$fecha  = $edit->fecha->value;
			$observ1= $edit->observ1->newValue;
			$this->genesal=false;

			if(empty($edit->tipo->newValue)){
				$tipo='';
			}else{
				$tipo='AND tipo='.$this->db->escape($edit->tipo->newValue);
			}

			$msj='';
			$error=0;
			$mSQL="SELECT codigo,nombre,sueldo FROM pers WHERE status='A' ${tipo}";
			$query = $this->db->query($mSQL);
			foreach ($query->result() as $row){
				if($porcen>0){
					$nsueldo=round($row->sueldo*(100+$porcen)/100,2);
				}else{
					$nsueldo=round($row->sueldo/(1+abs($porce/100)),2);
				}
				$_POST['codigo']  =	$row->codigo;
				$_POST['nombre']  = $row->nombre;
				$_POST['fecha']   =	$fecha;
				$_POST['sueldoa'] = $row->sueldo;
				$_POST['sueldo']  = $nsueldo;
				$_POST['observ1'] = $observ1;
				$_POST['oberv2']  = 'CAMBIO COLECTIVO';

				$rt=$this->dataedit();
				if(!$rt){
					$msj .= 'No se pudo ajustar el sueldo al trabajador '.$row->codigo.' '.$row->nombre.' '.$this->msj;
					$error++;
				}
			}

			$rt=array(
				'status' =>($error>0)? 'B': 'A',
				'mensaje'=>$msj,
				'pk'     =>''
			);
			echo json_encode($rt);
		}else{
			echo $edit->output;
		}
	}


	function chexiste($fecha){
		$fecha   = $this->input->post('fecha');
		$codigo  = $this->input->post('codigo');
		$dbfecha = $this->db->escape($fecha);
		$dbcodigo= $this->db->escape($codigo);

		$check=$this->datasis->dameval("SELECT COUNT(*) FROM ausu WHERE codigo=${dbcodigo} AND fecha=${dbfecha}");
		if($check > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM ausu WHERE codigo=${dbcodigo}");
			$this->validation->set_message('chexiste',"El aumento para ${codigo} ${nombre} fecha ${fecha} ya existe");
			return false;
		}else{
			return true;
		}
	}

	function fpositiva($valor){
		$codigo   = $this->input->post('codigo');
		$dbcodigo = $this->db->escape($codigo);

		$maxfecha = intval(str_replace('-','',$this->datasis->dameval("SELECT MAX(fecha) FROM nomina WHERE codigo=${dbcodigo}")));
		$valor    = intval(str_replace('-','',substr(human_to_dbdate($valor),0,10)));

		if($maxfecha>=$valor){
			$this->validation->set_message('fpositiva',"No puede aumentarle el sueldo con una fecha posterior a una nomina ya pagada");
			return false;
		}
		return true;
	}

	function instalar(){
		$campos=$this->db->list_fields('ausu');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE ausu DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE ausu ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
			$this->db->simple_query('ALTER TABLE ausu ADD UNIQUE INDEX codigo (codigo, fecha)');
		}
	}
}
