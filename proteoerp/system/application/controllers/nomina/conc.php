<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
//concepto
class Conc extends Controller {
	var $mModulo='CONC';
	var $titp='Conceptos de Nomina';
	var $tits='Conceptos de Nomina';
	var $url ='nomina/conc/';

	function Conc(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		$this->instalar();

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
		$param['listados']    = $this->datasis->listados('CONC', 'JQ');
		$param['otros']       = $this->datasis->otros('CONC', 'JQ');
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
		function concadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function concedit(){
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
		function concshow(){
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
		function concdel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								$.prompt("Registro eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
							}else{
								$.prompt("Registro no se puede eliminado");
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
			autoOpen: false, height: 450, width: 700, modal: true,
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
									$.prompt("Registro Guardado");
									$("#fedita").dialog( "close" );
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
	//Funciones de los Botones
	//
	function bodyscript1( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function concadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function concedit(){
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
		function concshow(){
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
		function concdel() {
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
									//'.$this->datasis->jwinopen(site_url('formatos/ver/CONC').'/\'+json.pk.id+\'/id\'').';
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
	// Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';
		$linea   = 1;

		$grid  = new $this->jqdatagrid;

		$grid->addField('concepto');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 4 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 35 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"A":"Asignacion", "D":"Deduccion"}, style:"width:100px"} ',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));


		$grid->addField('aplica');
		$grid->label('Aplica');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"S   ":"Semanal", "Q   ":"Quincenal", "B   ":"Bisemanal", "M   ":"Mensual",  "SQ  ":"Semanal/Quincenal", "SQM ":"Sem/Quin/Mensual", "SQMB":"Sem/Quin/Men/Bisemanal"}, style:"width:200px"} ',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ label: "Aplicacion" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:5, maxlength: 4 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));


		$grid->addField('liquida');
		$grid->label('Liquidaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'editrules'     => '{ required:true}',
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"S":"Si", "N":"No"}, style:"width:80px" }',
			'formoptions'   => '{label:"Liquidaciones", rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('psueldo');
		$grid->label('Sueldo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'editrules'     => '{ required:true}',
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"S":"Si", "N":"No"}, style:"width:80px" }',
			'formoptions'   => '{label:"Liquidaciones", rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('dias');
		$grid->label('Dias');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 40,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));

		$linea = $linea + 1;
		$grid->addField('encab1');
		$grid->label('Encabezado 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('encab2');
		$grid->label('Encabezado 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('formula');
		$grid->label('Formula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ rows:4, cols: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('tipod');
		$grid->label('Deuda');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"P":"Proveedor", "G":"Gasto"}, style:"width:100px",
				dataEvents: [{
					type: "change", fn: function(e){
						var v=$(e.target).val();
						_cargo = v+_cargo.substr(1,2);
						$("input#ctade").val("");
					}
				}]
			}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$link = site_url('ajax/buscasprvmgas');

		$grid->addField('ctade');
		$grid->label('Cta. Deudor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editoptions'   => '{'.$grid->autocomplete($link.'/d', 'ctade','aaaaaa','<div id=\"aaaaaa\"><b>"+ui.item.label+"</b></div>').'}',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('tipoa');
		$grid->label('Acreedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"P":"Proveedor", "G":"Gasto"}, style:"width:100px",
				dataEvents: [{
					type: "change", fn: function(e){
						var v=$(e.target).val();
						_cargo = _cargo.substr(0,2)+v;
						$("input#ctaac").val("");
					}
				}]
			}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('ctaac');
		$grid->label('Cta.Acreed');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editoptions'   => '{'.$grid->autocomplete($link.'/a', 'ctaac','bbbbbb','<div id=\"bbbbbb\"><b>"+ui.item.label+"</b></div>').'}',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
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
		$grid->setHeight('350');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('CONC','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('CONC','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('CONC','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('CONC','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: concadd, editfunc: concedit, delfunc: concdel, viewfunc: concshow');

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
		$mWHERE = $grid->geneTopWhere('conc');

		$response   = $grid->getData('conc', array(array()), array(), false, $mWHERE, 'concepto' );
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
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$concepto  = $this->input->post('concepto');
				$this->db->insert('conc', $data);
				echo "Registro Agregado ".$concepto;
				logusu('CONC',"Registro ".$concepto." INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$concepto  = $this->input->post('concepto');
			$this->db->where('id', $id);
			$this->db->update('conc', $data);
			logusu('CONC',"Registro ".$concepto." MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			$concepto  = $this->input->post('concepto');
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE concepto='$concepto'");
			$check += $this->datasis->dameval("SELECT COUNT(*) FROM asig   WHERE concepto='$concepto'");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM conc WHERE id=$id ");
				logusu('CONC',"Registro ".$concepto." ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function getctade($tipoa=NULL){
		$this->rapyd->load('fields');
		$uadministra = new dropdownField('ctade', 'ctade');
		$uadministra->status = 'modify';
		$uadministra->style  = 'width:210px;';

		if ($tipoa!==false){
			if($tipoa=='P'){
				$uadministra->options("SELECT proveed, CONCAT_WS(' ',proveed,nombre) AS nombre  FROM sprv ORDER BY proveed");
			}else{
				if($tipoa=='G'){
					$uadministra->options("SELECT codigo,  CONCAT_WS(' ',codigo,descrip) AS descrip FROM mgas ORDER BY codigo");
				}else{
					$uadministra->options("SELECT cliente, CONCAT_WS(' ',cliente,nombre) AS nombre  FROM sprv WHERE cliente REGEXP '[A-Za-z0-9_\-]+' ORDER BY cliente");
				}
			}
		}else{
 				$uadministra->option('Seleccione una opcion');
		}
		$uadministra->build();
		echo $uadministra->output;
	}

	//******************************************************************
	//
	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$link=site_url('nomina/conc/getctade');
		$link2=site_url('nomina/conc/getctade');
		$script  .=<<<script
		function get_ctade(){
			var tipo=$("#tipod").val();
			$.ajax({
				url: "$link"+'/'+tipo,
				success: function(msg){
					$("#td_ctade").html(msg);
				}
			});
		}
		function get_ctaac(){
			var tipo=$("#tipoa").val();
			$.ajax({
				url: "$link2"+'/'+tipo,
				success: function(msg){
					$("#td_ctaac").html(msg);
				}
			});
		}
script;

		$qformato=$this->qformato=$this->datasis->formato_cpla();

		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"${qformato}\"",
			'p_uri'=>array(4=>'<#i#>')
		);

		$bcuenta  =$this->datasis->p_modbus($modbus ,'cuenta');
		$bcontra  =$this->datasis->p_modbus($modbus ,'contra');

		$edit = new DataEdit('', 'conc');
		$edit->back_url = site_url("nomina/conc/filteredgrid");

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->concepto = new inputField('Concepto', 'concepto');
		$edit->concepto->rule = 'required|callback_chexiste|alpha_numeric';
		$edit->concepto->mode = 'autohide';
		$edit->concepto->maxlength= 4;
		$edit->concepto->size = 7;

		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->style ='width:100px;';
		$edit->tipo->option('','Seleccioar');
		$edit->tipo->options(array('A'=> 'Asignaci&oacute;n','O'=>'Otros','D'=> 'Deducci&oacute;n'));
		$edit->tipo->rule='required';

		$edit->descrip =  new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->size =30;
		$edit->descrip->maxlength=35;
		$edit->descrip->rule = 'strtoupper|required';

		$edit->grupo = new inputField('Grupo', 'grupo');
		$edit->grupo->size =7;
		$edit->grupo->maxlength=4;

		$edit->encab1 = new inputField('Encabezado 1', 'encab1');
		$edit->encab1->size = 16;
		$edit->encab1->maxlength=12;
		$edit->encab1->rule='required';

		$edit->encab2 =   new inputField('Encabezado 2&nbsp;', 'encab2');
		$edit->encab2->size = 16;
		$edit->encab2->maxlength=12;

		$edit->formula = new textareaField('F&oacute;rmula','formula');
		$edit->formula->rows = 4;
		$edit->formula->cols=85;
		$edit->formula->rule='required';

		$edit->cuenta = new inputField('Debe', 'cuenta');
		$edit->cuenta->size =19;
		$edit->cuenta->maxlength=15;
		$edit->cuenta->group='Enlace Contable';
		$edit->cuenta->rule='existecpla';
		$edit->cuenta->append($bcuenta);

		$edit->contra =  new inputField('Haber', 'contra');
		$edit->contra->size = 19;
		$edit->contra->maxlength=15;
		$edit->contra->group='Enlace Contable';
		$edit->contra->rule='existecpla';
		$edit->contra->append($bcontra);

		$edit->tipod = new dropdownField ('Deudor', 'tipod');
		$edit->tipod->style ='width:100px;';
		$edit->tipod->option('','Seleccionar');
		$edit->tipod->option('G','Gasto');
		$edit->tipod->option('C','Cliente');
		$edit->tipod->option('P','Proveedor');
		$edit->tipod->onchange = 'get_ctade();';
		$edit->tipod->group='Enlace Administrativo';
		$edit->tipod->rule='required|enum[G,C,P]';

		$edit->ctade = new dropdownField('Cuenta Deudor', 'ctade');
		$edit->ctade->style ='width:400px;';
		$edit->ctade->group='Enlace Administrativo';
		$edit->ctade->style='width:210px;';

		$tipod  =$edit->getval('tipod');
		if($tipod=='P'){
			$edit->ctade->options("SELECT proveed,CONCAT_WS(' ',proveed,nombre) AS descrip FROM sprv ORDER BY proveed");
		}else{
			if($tipod=='G'){
				$edit->ctade->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) AS descrip FROM mgas ORDER BY codigo");
			}else{
				$edit->ctade->options("SELECT cliente,CONCAT_WS(' ',proveed,nombre) AS descrip FROM sprv WHERE cliente REGEXP '[A-Za-z0-9_\-]+' ORDER BY cliente");
			}
		}

		$edit->tipoa = new dropdownField ('Acreedor', 'tipoa');
		$edit->tipoa->style ='width:100px;';
		$edit->tipoa->option('','Seleccionar');
		$edit->tipoa->option('G','Gasto');
		$edit->tipoa->option('C','Cliente');
		$edit->tipoa->option('P','Proveedor');
		$edit->tipoa->group='Enlace Administrativo';
		$edit->tipoa->onchange = 'get_ctaac();';
		$edit->tipoa->rule='required|enum[G,C,P]';

		$edit->ctaac =   new dropdownField('Cuenta Acreedor', 'ctaac');
		$edit->ctaac->style ='width:400px;';
		$edit->ctaac->group='Enlace Administrativo';
		$edit->ctaac->style='width:210px;';
		$tipod  =$edit->getval('tipoa');
		if($tipod=='P'){
			$edit->ctaac->options("SELECT proveed,CONCAT_WS(' ',proveed,nombre) AS descrip FROM sprv ORDER BY proveed");
		}else{
			if($tipod=='G'){
				$edit->ctaac->options("SELECT codigo,CONCAT_WS(' ',codigo,descrip) AS descrip FROM mgas ORDER BY codigo");
			}else{
				$edit->ctaac->options("SELECT cliente,CONCAT_WS(' ',proveed,nombre) AS descrip FROM sprv ORDER BY cliente");
			}
		}

		$edit->liquida =   new dropdownField('Aplica a liquidaci&oacute;n', 'liquida');
		$edit->liquida->style ='width:50px;';
		$edit->liquida->option('S','S');
		$edit->liquida->option('N','N');
		$edit->liquida->rule='required|enum[S,N]';

		$edit->aplica = new inputField('Aplica','aplica');
		$edit->aplica->rule      = '';
		$edit->aplica->size      =  3;
		$edit->aplica->maxlength =  1;

		$edit->dias = new inputField('Afecta d&iacute;as trabajados','dias');
		$edit->dias->rule='integer';
		$edit->dias->css_class='inputonlynum';
		$edit->dias->insertValue = 0;
		$edit->dias->size        = 6;
		$edit->dias->maxlength   = 5;

		$edit->psueldo = new dropdownField('Forma parte del Sueldo','psueldo');
		$edit->psueldo->option('S','S');
		$edit->psueldo->option('N','N');
		$edit->psueldo->style ='width:50px;';
		$edit->psueldo->rule='required|enum[S,N]';

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form'] =&  $edit;
			$this->load->view('view_conc', $conten);

		}
	}


	function _post_insert($do){
		$codigo=$do->get('concepto');
		$nombre=$do->get('descrip');
		logusu('conc',"CONCEPTO ${codigo} NOMBRE  ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('concepto');
		$nombre=$do->get('descrip');
		logusu('conc',"CONCEPTO ${codigo} NOMBRE  ${nombre}  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('concepto');
		$nombre=$do->get('descrip');
		logusu('conc',"CONCEPTO ${codigo} NOMBRE  ${nombre}  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo=$this->input->post('concepto');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM conc WHERE concepto='$codigo'");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT descrip FROM conc WHERE concepto='$codigo'");
			$this->validation->set_message('chexiste',"El concepto $codigo nombre $nombre ya existe");
			return false;
		}else {
  			return true;
		}
	}

	function instalar(){
		$campos=$this->db->list_fields('conc');
		if(!in_array('id',$campos)){
			$this->db->query('ALTER TABLE conc DROP PRIMARY KEY');
			$this->db->query('ALTER TABLE conc ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->query('ALTER TABLE conc ADD UNIQUE INDEX concepto (concepto)');
		}

		if(!in_array('dias',$campos)){
			$this->db->query('ALTER TABLE conc ADD COLUMN dias INT NULL DEFAULT 0 COMMENT "Dias que afecta" AFTER liquida');
		}

		if(!in_array('psueldo',$campos)){
			$this->db->query('ALTER TABLE conc ADD COLUMN psueldo CHAR(1) NULL DEFAULT "N" COMMENT "Forma Parte del Sueldo" AFTER dias');
		}
	}
}
