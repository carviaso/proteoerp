<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Prmo extends Controller {
	var $mModulo = 'PRMO';
	var $titp    = 'Otros Movimientos';
	var $tits    = 'Otros Movimientos';
	var $url     = 'finanzas/prmo/';

	function Prmo(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PRMO', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->creaintramenu(array('modulo'=>'52C','titulo'=>'Otros Movimientos','mensaje'=>'Otros Movimientos','panel'=>'TESORERIA','ejecutar'=>'finanzas/prmo','target'=>'popu','visible'=>'S','pertenece'=>'5','ancho'=>900,'alto'=>600));
		//$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('230');

		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		$readyLayout = $grid->readyLayout2( 212	, 115, $param['grids'][0]['gridname']);

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'imprime','img'=>'assets/default/images/print.png','alt' => 'Reimprimir',         'label'=>'Reimprimir Documento'));
		$grid->wbotonadd(array('id'=>'prmo1'  ,'img'=>'images/mano.png',          'alt' => 'Prestamo Otorgado',        'label'=>'Prestamo Otorgado',     'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'prmo2'  ,'img'=>'images/check.png',         'alt' => 'Prestamo Recibido',        'label'=>'Prestamo Recibido',     'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'prmo3'  ,'img'=>'images/face-sad.png',      'alt' => 'Cheq Devuelto Cliente',    'label'=>'Cheq Devuelto Cliente', 'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'prmo4'  ,'img'=>'images/face-surprise.png', 'alt' => 'Cheq Devuelto Proveed',    'label'=>'Cheq Devuelto Proveed', 'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'prmo5'  ,'img'=>'images/dinero.png',        'alt' => 'Deposito por Analizar',    'label'=>'Deposito por Analizar', 'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'prmo6'  ,'img'=>'images/retencion.gif',     'alt' => 'Cargos Indebidos en Banco','label'=>'Cargos Indebidos',      'tema'=>'anexos'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita', 'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' , 'title'=>'Mostrar Registro'),
			array('id'=>'fborra', 'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'] );

		$funciones = '
		function ltransac(el, val, opts){
			var link=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return link;
		};';

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['centerpanel'] = $centerpanel;
		$param['readyLayout'] = $readyLayout;
		$param['listados']    = $this->datasis->listados('PRMO', 'JQ');
		$param['otros']       = $this->datasis->otros('PRMO', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1','blitzer');
		$param['bodyscript']  = $bodyscript;
		$param['funciones']   = $funciones;
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
		function tconsulta(transac){
			if (transac)	{
				window.open(\''.site_url('contabilidad/casi/localizador/transac/procesar').'/\'+transac, \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Transaccion invalida</h1>");
			}
		};';

		$bodyscript .= '
		function prmoadd(){
			$.prompt("<h1>Opcion no disponible</h1>");
		};';

		$bodyscript .= '
		function prmoedit(){
			$.prompt("<h1>Opcion no disponible</h1>");
		};';

		$bodyscript .= '
		function prmoshow(){
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
		function prmodel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'deprmodel/do_delete').'/"+id, function(data){
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

		// Prestamo Otorgado CxC
		$bodyscript .= '
		$("#prmo1").click( function() {
			$.post("'.site_url($this->url.'deprmo1/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 620, title: "Prestamo Otorgado"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

		// Prestamo Recibido
		$bodyscript .= '
		$("#prmo2").click( function() {
			$.post("'.site_url($this->url.'deprmo2/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 610, title: "Prestamo Recibido"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

		// Cheque devuelto de Cliente
		$bodyscript .= '
		$("#prmo3").click( function() {
			$.post("'.site_url($this->url.'deprmo3/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 610, title: "Prestamo Recibido"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

		// Cheque devuelto de Proveedor
		$bodyscript .= '
		$("#prmo4").click( function() {
			$.post("'.site_url($this->url.'deprmo4/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 610, title: "Prestamo Recibido"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

		// Depositos por Analizar
		$bodyscript .= '
		$("#prmo5").click( function() {
			$.post("'.site_url($this->url.'deprmo5/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 610, title: "Prestamo Recibido"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

		// Cargos Indebidos
		$bodyscript .= '
		$("#prmo6").click( function() {
			$.post("'.site_url($this->url.'deprmo6/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 610, title: "Prestamo Recibido"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

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
									$.prompt("Registro Guardado");
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									window.open(\''.site_url($this->url.'prmoprint').'/\'+json.pk.id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
									return true;
								} else {
									$.prompt(json.mensaje);
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

		$bodyscript .= '
			jQuery("#imprime").click( function(){
				var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				if(id){
					var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
					window.open(\''.site_url($this->url.'prmoprint').'/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
				}else{
					$.prompt("<h1>Por favor Seleccione un Movimiento</h1>");
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

		$grid->addField('tipop');
		$grid->label('Tip.Op');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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


		$grid->addField('codban');
		$grid->label('C.Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
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


		$grid->addField('numche');
		$grid->label('Num.Cheque');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('benefi');
		$grid->label('Beneficiario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('comprob');
		$grid->label('Comprobante');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('clipro');
		$grid->label('Cli/Pro');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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


		$grid->addField('docum');
		$grid->label('Documento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('banco');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('monto');
		$grid->label('Monto');
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


		$grid->addField('cuotas');
		$grid->label('Cuotas');
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


		$grid->addField('vence');
		$grid->label('Vence');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('observa1');
		$grid->label('Observaci&oacute;n 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('observa2');
		$grid->label('Observaci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('transac');
		$grid->label('Transaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
			'formatter'     => 'ltransac',
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


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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


		$grid->addField('cadano');
		$grid->label('Cadano');
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


		$grid->addField('apartir');
		$grid->label('Apartir');
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


		$grid->addField('negreso');
		$grid->label('N.Egreso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('ningreso');
		$grid->label('N.Ingreso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('retencion');
		$grid->label('Retenci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:14, maxlength: 14 }',
		));


		$grid->addField('factura');
		$grid->label('Factura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('remision');
		$grid->label('Remisi&oacute;n');
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
		$grid->setHeight('200');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    false);
		$grid->setEdit(   false);
		$grid->setDelete( $this->datasis->sidapuede('PRMO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PRMO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: prmoadd, editfunc: prmoedit, delfunc: prmodel, viewfunc: prmoshow');

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
		$mWHERE = $grid->geneTopWhere('prmo');

		$response   = $grid->getData('prmo', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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
			echo 'Deshabilitado';
		} elseif($oper == 'edit') {
			//$nuevo  = $data[$mcodp];
			//$anterior = $this->datasis->dameval("SELECT $mcodp FROM prmo WHERE id=$id");
			//if ( $nuevo <> $anterior ){
			//	//si no son iguales borra el que existe y cambia
			//	$this->db->query("DELETE FROM prmo WHERE $mcodp=?", array($mcodp));
			//	$this->db->query("UPDATE prmo SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
			//	$this->db->where("id", $id);
			//	$this->db->update("prmo", $data);
			//	logusu('PRMO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
			//	echo "Grupo Cambiado/Fusionado en clientes";
			//} else {
			//	unset($data[$mcodp]);
			//	$this->db->where("id", $id);
			//	$this->db->update('prmo', $data);
			//	logusu('PRMO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
			//	echo "$mcodp Modificado";
			//}
			echo "Deshabilitado";
		} elseif($oper == 'del') {
			echo 'Deshabilitado';
		}
	}


	function prmoprint($id){
		$dbid = $this->db->escape($id);
		$tipo = $this->datasis->dameval('SELECT tipop FROM prmo WHERE id='.$dbid);

		switch($tipo){
			case '1':
				//Prestamo otrogado
				redirect('formatos/descargar/PRMOOD/'.$id);
				break;
			case '2':
				//Prestamo recibido
				redirect('formatos/descargar/PRMOR/'.$id);
				break;
			case '3':
				//Cheq Devuelto cliente
				redirect('formatos/descargar/PRMOCC/'.$id);
				break;
			case '4':
				//Cheq Devuelto proveedor
				redirect('formatos/descargar/PRMOCP/'.$id);
				break;
			case '5':
				//Deposito por analizar
				redirect('formatos/descargar/PRMODA/'.$id);
				break;
			case '6':
				//Cargos indebidos Banco
				redirect('formatos/descargar/PRMOCI/'.$id);
				break;
			default:
				echo 'Formato no definido';
		}
	}

	//******************************************************************
	// Dataedit para Todos
	//
	function deprmo(){
		$this->rapyd->load('dataedit');

		$script = '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#vence").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit('', 'prmo');

		$edit->on_save_redirect=false;
		$edit->back_url = '';

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );


		// Campos Ocultos
		$edit->tipop = new hiddenField('Tipop','tipop');
		$edit->tipop->insertValue = '1';
		$edit->numero = new hiddenField('Numero','numero');

		$edit->clipro = new inputField('Cliente','clipro');
		$edit->clipro->size =7;
		$edit->clipro->rule  = 'required';
		$edit->clipro->maxlength =15;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='';
		$edit->nombre->size =40;
		$edit->nombre->maxlength =40;
		$edit->nombre->readonly = true;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =12;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->calendar = false;

		$edit->codban = new dropdownField('Caja o Banco','codban');
		$edit->codban->option('','Seleccionar');
		$edit->codban->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND codbanc>'00' ORDER BY tbanco='CAJ' , codbanc");
		$edit->codban->rule  = 'required';
		$edit->codban->style = 'width:250px;';
/*
		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('ND','Nota Debito');
		$edit->tipo->option('CH','Cheque');  // solo si es banco
		$edit->tipo->style = 'width:120px';
*/
		$edit->numche = new inputField('N&uacute;mero','numche');
		$edit->numche->rule='';
		$edit->numche->size =13;
		$edit->numche->maxlength =12;

		$edit->benefi = new inputField('Beneficiario','benefi');
		$edit->benefi->rule='';
		$edit->benefi->size =32;
		$edit->benefi->maxlength =30;

		$edit->comprob = new inputField('Comprobante','comprob');
		$edit->comprob->rule='';
		$edit->comprob->size =8;
		$edit->comprob->maxlength =6;

		$edit->docum = new inputField('Documento','docum');
		$edit->docum->rule='';
		$edit->docum->size =13;
		$edit->docum->maxlength =12;

		$edit->banco = new inputField('Banco','banco');
		$edit->banco->rule='';
		$edit->banco->size =12;
		$edit->banco->maxlength =10;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='required|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =10;
		$edit->monto->maxlength =13;

		$edit->cuotas = new inputField('Cuotas','cuotas');
		$edit->cuotas->rule='integer';
		$edit->cuotas->css_class='inputonlynum';
		$edit->cuotas->size =4;
		$edit->cuotas->maxlength =2;

		$edit->vence = new dateonlyField('Fecha de Vencimiento','vence');
		$edit->vence->rule        = 'chfecha';
		$edit->vence->size        = 12;
		$edit->vence->maxlength   =  8;
		$edit->vence->calendar    = false;
		$edit->vence->insertValue =  date('Y-m-d',mktime(0, 0, 0, date('m'), date('d')+30, date('Y')));

		$edit->observa1 = new inputField('Observaciones','observa1');
		$edit->observa1->rule='';
		$edit->observa1->size =52;
		$edit->observa1->maxlength =50;

		$edit->observa2 = new inputField('','observa2');
		$edit->observa2->rule='';
		$edit->observa2->size =52;
		$edit->observa2->maxlength =50;

		$edit->transac = new inputField('Transac','transac');
		$edit->transac->rule='';
		$edit->transac->size =10;
		$edit->transac->maxlength =8;

		$edit->cadano = new inputField('Cadano','cadano');
		$edit->cadano->rule='integer';
		$edit->cadano->css_class='inputonlynum';
		$edit->cadano->size =8;
		$edit->cadano->maxlength =6;

		$edit->apartir = new inputField('Apartir','apartir');
		$edit->apartir->rule='integer';
		$edit->apartir->css_class='inputonlynum';
		$edit->apartir->size =8;
		$edit->apartir->maxlength =6;

		$edit->negreso = new inputField('Negreso','negreso');
		$edit->negreso->rule='';
		$edit->negreso->size =10;
		$edit->negreso->maxlength =8;

		$edit->ningreso = new inputField('Ningreso','ningreso');
		$edit->ningreso->rule='';
		$edit->ningreso->size =10;
		$edit->ningreso->maxlength =8;

		$edit->retencion = new inputField('Retenci&oacute;n','retencion');
		$edit->retencion->rule='';
		$edit->retencion->size =16;
		$edit->retencion->maxlength =14;

		$edit->factura = new inputField('Factura','factura');
		$edit->factura->rule='';
		$edit->factura->size =14;
		$edit->factura->maxlength =12;

		$edit->remision = new dateonlyField('Remision','remision');
		$edit->remision->rule='chfecha';
		$edit->remision->size =10;
		$edit->remision->maxlength =8;

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		return $edit;
	}

	function deprmodel(){
		$this->rapyd->load('dataedit');
		$edit = $this->deprmo();
		$this->dataedit($edit);
	}

	//******************************************************************
	// Dataedit para Prestamo Otorgado
	//
	function deprmo1(){

		$this->rapyd->load('dataedit');
		$edit = $this->deprmo();

		// script para buscar cheque
		$script= '
		$("#numche").change( function() {
			$("#observa2").val("BANCO/CAJA ("+$("#codban").val()+") "+$("#tipo").val()+" "+$("#numche").val() );
		});
		$("#clipro").change( function() {
			$("#observa1").val("PRESTAMO OTORGADO A ("+$("#clipro").val()+") "+$("#nombre").val() );
		});';

		$edit->script($this->scriptscli().$script,'modify');
		$edit->script($this->scriptscli().$script,'create');

		$edit->tipop = new hiddenField('Tipo','tipop');
		$edit->tipop->insertValue = '1';

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('CH','Cheque');  // solo si es banco
		$edit->tipo->option('ND','Nota Debito');
		$edit->tipo->style = 'width:120px';

		$edit->clipro = new inputField('Cliente','clipro');
		$edit->clipro->rule='';
		$edit->clipro->size =7;
		$edit->clipro->maxlength =15;
		$edit->clipro->rule  = 'required';

		$this->dataedit($edit);
	}

	//******************************************************************
	// Dataedit para Prestamo Recibido
	//
	function deprmo2(){

		$this->rapyd->load('dataedit');
		$edit = $this->deprmo();

		// script para buscar cheque
		$script= '
		$("#numche").change( function() {
			$("#observa2").val("BANCO/CAJA ("+$("#codban").val()+") "+$("#tipo").val()+" "+$("#numche").val() );
		});
		$("#clipro").change( function() {
			$("#observa1").val("PRESTAMO RECIBIDO DE ("+$("#clipro").val()+") "+$("#nombre").val() );
		});
		';

		$edit->script($this->scriptsprv().$script,'modify');
		$edit->script($this->scriptsprv().$script,'create');

		$edit->tipop = new hiddenField('Tipop','tipop');
		$edit->tipop->insertValue = '2';

		$edit->clipro = new inputField('Proveedor','clipro');
		$edit->clipro->rule  = 'required';
		$edit->clipro->size  = 7;
		$edit->clipro->maxlength =15;
		$edit->clipro->rule  = 'required';

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('DE','Deposito');
		$edit->tipo->option('NC','Nota Credito');  // solo si es banco
		$edit->tipo->style = 'width:120px';

		$this->dataedit($edit);
	}


	//******************************************************************
	// Dataedit para Cheque devuelto de Cliente
	//
	function deprmo3(){

		// script para buscar cheque
		$script= '
		$("#docum").autocomplete({
			delay: 600,
			autoFocus: true,
			source: function(req, add){
			$.ajax({
				url:  "'.site_url('ajax/buscachequecli').'",
				type: "POST",
				dataType: "json",
				data: { q: req.term, cod_cli: $("#clipro").val() },
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$("#docum").val("");
						}else{
							$.each(data, function(i, val){ sugiere.push( val );});
						}
						add(sugiere);
					},
			})
			},
			minLength: 2,
			select: function( event, ui ) {
				$("#docum").attr("readonly", "readonly");
				$("#monto").attr("readonly", "readonly");
				$("#monto").val(ui.item.monto);
				$("#docum").val(ui.item.num_ref);
				setTimeout(function() {  $("#docum").removeAttr("readonly"); }, 1500);
			}
		});
		$("#numche").change( function() {
			$("#observa2").val("BANCO ("+$("#codban").val()+") "+$("#numche").val() );
		});
		$("#clipro").change( function() {
			$("#observa1").val("CHEQUE DEVUELTO ("+$("#clipro").val()+") "+$("#nombre").val() );
		});';


		$this->rapyd->load('dataedit');
		$edit = $this->deprmo();

		$edit->script($this->scriptscli().$script,'modify');
		$edit->script($this->scriptscli().$script,'create');

		$edit->tipop = new hiddenField('Tipop','tipop');
		$edit->tipop->insertValue = '3';

		$edit->clipro = new inputField('Cliente','clipro');
		$edit->clipro->rule  = 'required';
		$edit->clipro->size =7;
		$edit->clipro->maxlength =15;

		$edit->tipo = new hiddenField('Tipo','tipo');
		$edit->tipo->insertValue = 'ND';

		$edit->numche->label  = 'Nota Debito';
		$edit->docum->label   = 'Nro de Cheque';

		$edit->codban = new dropdownField('Depositado en','codban');
		$edit->codban->option('','Seleccionar');
		$edit->codban->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND codbanc>'00' AND tbanco!='CAJ' ORDER BY codbanc");
		$edit->codban->style = "width:210px;";

		$this->dataedit($edit);
	}


	//******************************************************************
	// Dataedit para Cheque devuelto a Proveedor
	//
	function deprmo4(){

		$this->rapyd->load('dataedit');
		$edit = $this->deprmo();

		// script para buscar cheque
		$script= '
		$("#docum").autocomplete({
			delay: 600,
			autoFocus: true,
			source: function(req, add){
			$.ajax({
				url:  "'.site_url('ajax/buscachequeprv').'",
				type: "POST",
				dataType: "json",
				data: { q: req.term, cod_prv: $("#clipro").val(), codbanc: $("#codban").val() },
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$("#docum").val("");
						}else{
							$.each(data, function(i, val){ sugiere.push( val );});
						}
						add(sugiere);
					},
			})},
			minLength: 2,
			select: function( event, ui ) {
				$("#docum").attr("readonly", "readonly");
				$("#monto").attr("readonly", "readonly");
				$("#monto").val(ui.item.monto);
				$("#docum").val(ui.item.num_ref);
				setTimeout(function() {  $("#docum").removeAttr("readonly"); }, 1500);
			}
		});
		$("#docum").keyup( function() {
			$("#observa1").val("CHEQUE DEVUELTO DE PROVEEDOR # "+$("#docum").val() );
			$("#observa2").val($("#codban").val() );
		});';


		$edit->script($this->scriptsprv().$script,'modify');
		$edit->script($this->scriptsprv().$script,'create');

		$edit->tipop = new hiddenField('Tipop','tipop');
		$edit->tipop->insertValue = '4';

		$edit->clipro = new inputField('Proveedor','clipro');
		$edit->clipro->rule  = 'required';
		$edit->clipro->size =7;
		$edit->clipro->maxlength =15;

		$edit->tipo = new hiddenField('Tipo','tipo');
		$edit->tipo->insertValue = 'NC';

		$edit->numche->label  = 'Nota Credito';
		$edit->docum->label   = 'Nro de Cheque';

		$edit->codban = new dropdownField('Banco','codban');
		$edit->codban->option('','Seleccionar');
		$edit->codban->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND codbanc>'00' AND tbanco!='CAJ' ORDER BY codbanc");
		$edit->codban->style = "width:210px;";


		$this->dataedit($edit);
	}

	//******************************************************************
	// Dataedit para Depositos por Analizar
	//
	function deprmo5(){

		$this->rapyd->load('dataedit');
		$edit = $this->deprmo();

		// script para la observacion
		$script= '
		$("#numche").change( function() {
			$("#observa1").val("DEPOSITO NO APLICADO # "+$("#numche").val()+" DEL BANCO "+$("#codban").val() );
		});
		';

		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->tipop = new hiddenField('Tipop','tipop');
		$edit->tipop->insertValue = '5';

		$edit->clipro = new inputField('Proveedor','clipro');
		$edit->clipro->rule  = '';
		$edit->clipro->size =7;
		$edit->clipro->maxlength =15;

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('DE','Deposito');
		$edit->tipo->option('NC','Nota Credito');  // solo si es banco
		$edit->tipo->style = 'width:100px';

		$edit->codban = new dropdownField('Banco','codban');
		$edit->codban->option('','Seleccionar');
		$edit->codban->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND codbanc>'00' AND tbanco!='CAJ' ORDER BY codbanc");
		$edit->codban->style = "width:210px;";

		$edit->docum->rule  = '';


		$this->dataedit($edit);
	}

	//******************************************************************
	// Dataedit para Cargos Indebidos en Banco
	//
	function deprmo6(){

		$this->rapyd->load('dataedit');
		$edit = $this->deprmo();

		// script para la observacion
		$script= '
		$("#numche").change( function() {
			$("#observa1").val("CARGO INDEBIDO # "+$("#numche").val()+" DEL BANCO "+$("#codban").val() );
		});
		';

		$edit->script($this->scriptscli().$script,'modify');
		$edit->script($this->scriptscli().$script,'create');

		$edit->tipop = new hiddenField('Tipop','tipop');
		$edit->tipop->insertValue = '6';

		$edit->clipro = new inputField('Cliente','clipro');
		$edit->clipro->rule  = 'required';
		$edit->clipro->size =7;
		$edit->clipro->maxlength =15;

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('ND','Nota Debito');
		$edit->tipo->option('CH','Cheque');  // solo si es banco
		$edit->tipo->style = 'width:110px';


		$this->dataedit($edit);
	}

	//******************************************************************
	// Dataedit para todos
	//
	function dataedit($edit){
		$this->rapyd->load('dataedit');

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
			$this->load->view('view_prmo', $conten);
		}
	}


	function _pre_insert($do){

		$numche = $do->get('numche');
		$monto  = $do->get('monto');
		$tipop  = $do->get('tipop');
		$docum  = $do->get('docum');
		$codban = $do->get('codban');

		$escaja = $this->datasis->dameval("SELECT tbanco FROM banc WHERE codbanc=".$this->db->escape($codban));

		$atipop = array('1','2','3','4','5','6');
		if ( !in_array( $tipop, $atipop ) ){
			$do->error_message_ar['pre_ins']='Tipo de Movimiento errado';
			return false;
		}

		if ( $monto <= 0 ){
			$do->error_message_ar['pre_ins']='Falta colocar el Monto';
			return false;
		}

		if ( empty($escaja) ){
			$do->error_message_ar['pre_ins']='Error con el banco o la caja, reintente...';
			return false;
		}

		//Validaciones PRESTAMO OTORGADO
		if ( $tipop == '1' ){
			if ( $escaja == 'CAJ' )
				$numche = $this->datasis->fprox_numero('ncaja'.$codban);
			else {
				if ( empty( $numche ) ){
					$do->error_message_ar['pre_ins']='Falta colocar el Numero de Documento';
					return false;
				}
				// Busca si ya esta en bmov
				$numche = str_pad($numche,12,'0',STR_PAD_LEFT);
				$esta   = $this->bmovrepe($do->get('codban'), $do->get('tipo'), $numche);

				if ( !empty($esta) ) {
					$do->error_message_ar['pre_ins']='Movimiento ya existe en bancos ('.$esta.')';
					return false;
				}
			}

			$numero  = $this->datasis->fprox_numero('nprmo');
			$transac = $this->datasis->fprox_numero('ntransa');
			$negreso = $this->datasis->fprox_numero('negreso');
			$numche = str_pad($numche,12,'0',STR_PAD_LEFT);

			$do->set('numero', $numero);
			$do->set('transac',$transac);
			$do->set('negreso',$negreso);
			$do->set('numche', $numche);

		//Validaciones PRESTAMO RECIBIDO
		} elseif ( $tipop == '2' ){
			if ( $escaja == 'CAJ' )
				$numche = $this->datasis->fprox_numero('ncaja'.$codban);
			else {
				if ( empty( $numche ) ){
					$do->error_message_ar['pre_ins']='Falta colocar el Numero de Documento';
					return false;
				}
				// Busca si ya esta en bmov
				$numche = str_pad($numche,12,'0',STR_PAD_LEFT);
				$esta   = $this->bmovrepe($do->get('codban'), $do->get('tipo'), $numche);

				if ( !empty($esta) ) {
					$do->error_message_ar['pre_ins']='Movimiento ya existe en bancos ('.$esta.')';
					return false;
				}
			}
			$numero   = $this->datasis->fprox_numero('nprmo');
			$transac  = $this->datasis->fprox_numero('ntransa');
			$ningreso = $this->datasis->fprox_numero('ningreso');
			$numche = str_pad($numche,12,'0',STR_PAD_LEFT);

			$do->set('numero',   $numero   );
			$do->set('transac',  $transac  );
			$do->set('ningreso', $ningreso );
			$do->set('numche', $numche);


		//Validaciones CHEQUE DEVUELTO CLIENTE
		} elseif ( $tipop == '3' ){

			if ( $escaja == 'CAJ' )
				$numche = $this->datasis->fprox_numero('ncaja'.$codban);
			else {
				if ( empty( $numche ) ){
					$do->error_message_ar['pre_ins']='Falta colocar el Numero de Documento';
					return false;
				}
				// Busca si ya esta en bmov
				$numche = str_pad($numche,12,'0',STR_PAD_LEFT);

				$esta   = $this->bmovrepe($do->get('codban'), $do->get('tipo'), $numche);

				if ( !empty($esta) ) {
					$do->error_message_ar['pre_ins']='Movimiento ya existe en bancos ('.$esta.')';
					return false;
				}
			}

			$numero  = $this->datasis->fprox_numero('nprmo');
			$transac = $this->datasis->fprox_numero('ntransa');
			$numche = str_pad($numche,12,'0',STR_PAD_LEFT);

			$do->set('vence',  $do->get('fecha'));
			$do->set('numche', $numche);
			$do->set('numero', $numero);
			$do->set('transac',$transac);

		//Validaciones CHEQUE DEVUELTO PROVEEDOR
		} elseif ( $tipop == '4' ){

			if ( empty( $numche ) ){
				$numche  = $this->datasis->fprox_numero('nprmocd');
			}

			if ( empty( $docum ) ){
				$do->error_message_ar['pre_ins']='Falta colocar el Numero de Documento';
				return false;
			}
			// Busca si ya esta en bmov
			$numche = str_pad($numche,12,'0',STR_PAD_LEFT);

			$esta   = $this->bmovrepe($do->get('codban'), $do->get('tipo'), $numche);
				if ( !empty($esta) ) {
				$do->error_message_ar['pre_ins']='Movimiento ya existe en bancos ('.$esta.')';
				return false;
			}

			$numero  = $this->datasis->fprox_numero('nprmo');
			$transac = $this->datasis->fprox_numero('ntransa');
			$numche = str_pad($numche,12,'0',STR_PAD_LEFT);

			$do->set('vence',  $do->get('fecha'));
			$do->set('numche', $numche);
			$do->set('numero', $numero);
			$do->set('transac',$transac);

		//Validaciones DEPOSITOS POR ANALIZAR
		} elseif ( $tipop == '5' ){

			if ( empty( $numche ) ){
				$do->error_message_ar['pre_ins']='Falta colocar el Numero del Deposito';
				return false;
			}

			// Busca si ya esta en bmov
			$numche = str_pad($numche,12,'0',STR_PAD_LEFT);

			$esta   = $this->bmovrepe($do->get('codban'), $do->get('tipo'), $numche);
			if ( !empty($esta) ) {
				$do->error_message_ar['pre_ins']='Movimiento ya existe en bancos ('.$esta.')';
				return false;
			}

			// revisa si el bco tiene proveedor
			$clipro = $this->datasis->dameval("SELECT a.codprv FROM banc a JOIN sprv b ON a.codprv=b.proveed WHERE a.codbanc=".$this->db->escape($codban));

			if (empty($clipro)) {
				$do->error_message_ar['pre_ins']='El Banco no tiene asignado proveedor';
				return false;
			}
			$nombre = $this->datasis->dameval("SELECT b.nombre FROM banc a JOIN sprv b ON a.codprv=b.proveed WHERE a.codbanc=".$this->db->escape($codban));

			$numero  = $this->datasis->fprox_numero('nprmo');
			$transac = $this->datasis->fprox_numero('ntransa');
			$numche = str_pad($numche,12,'0',STR_PAD_LEFT);

			$do->set('clipro', $clipro);
			$do->set('nombre', $nombre);

			$do->set('vence',  $do->get('fecha'));
			$do->set('numche', $numche);
			$do->set('numero', $numero);
			$do->set('transac',$transac);

		//Validaciones CARGOS INDEBIDOS EN BANCOS
		} elseif ( $tipop == '6' ){

			if ( empty( $numche ) ){
				$do->error_message_ar['pre_ins']='Falta colocar el Numero de Documento';
				return false;
			}
			// Busca si ya esta en bmov
			$numche = str_pad($numche,12,'0',STR_PAD_LEFT);

			$esta   = $this->bmovrepe($do->get('codban'), $do->get('tipo'), $numche);
				if ( !empty($esta) ) {
				$do->error_message_ar['pre_ins']='Movimiento ya existe en bancos ('.$esta.')';
				return false;
			}

			$numero  = $this->datasis->fprox_numero('nprmo');
			$transac = $this->datasis->fprox_numero('ntransa');
			$numche = str_pad($numche,12,'0',STR_PAD_LEFT);

			$do->set('vence',  $do->get('fecha'));
			$do->set('numche', $numche);
			$do->set('numero', $numero);
			$do->set('transac',$transac);
		}

		return true;
	}

	function bmovrepe($codbanc, $tipo_op, $numero){
		$mSQL  = "SELECT count(*) FROM bmov WHERE codbanc=".$this->db->escape($codbanc);
		$mSQL .= " AND tipo_op='".$tipo_op."' AND numero='".$numero."'  AND anulado<>'S' ";
		$esta  = $this->datasis->dameval($mSQL);
		if ( $esta > 0 ) {
			$mSQL  = "SELECT CONCAT_WS(' ',fecha, tipo_op , numero, nombre) jojo FROM bmov WHERE codbanc=".$this->db->escape($codbanc);
			$mSQL .= " AND tipo_op='".$tipo_op."' AND numero='".$numero."'  AND anulado<>'S' ";
			$esta  = $this->datasis->dameval($mSQL);
			$do->error_message_ar['pre_ins']='Documento ya existe ('.$esta.')';
			return 'Documento ya existe ('.$esta.')';
		}
		return '';
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='Opcion no disponible';
		return false;
	}

	function _pre_delete($do){
		$transac =$do->get('transac');

		$do->error_message_ar['pre_del']='Registro no se puede elimnar';
		return false;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);

		$tipop   = $do->get('tipop');
		$codban  = $do->get('codban');
		$monto   = $do->get('monto');
		$fecha   = $do->get('fecha');
		$negreso = $do->get('negreso');
		$tipo    = $do->get('tipo');

		//GUARDA PRESTAMO OTORGADO
		if($tipop == '1'){
			// Crea bmov egreso
			$this->datasis->actusal($codban, $fecha, -1*$monto );

			$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='".$codban."'");

			$mCUENTA  = $mREG['numcuent'];
			$mBANCO   = $mREG['banco'];
			$mSALDO   = $mREG['saldo'];
			$mTBANCO  = $mREG['tbanco'];
			if($mTBANCO == 'CAJ') $tipo = 'ND';

			$data = array();
			$data['codbanc']  = $codban;
			$data['numcuent'] = $mCUENTA;
			$data['banco']    = $mBANCO;
			$data['saldo']    = $mSALDO;
			$data['fecha']    = $fecha;
			$data['tipo_op']  = $tipo;
			$data['numero']   = $do->get('numche');
			$data['concepto'] = 'PRESTAMO OTORGADO '.$do->get('numero');
			$data['clipro']   = 'C';
			$data['concep2']  = $do->get('observa1');
			$data['concep3']  = $do->get('observa2');
			$data['monto']    = $monto;
			$data['codcp']    = $do->get('clipro');
			$data['nombre']   = $do->get('nombre');
			$data['benefi']   = $do->get('benefi');
			//$data['comprob']= $COMPROB;
			$data['negreso']  = $do->get('negreso');
			$data['posdata']  = $fecha;
			$data['liable']   = 'S';
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('bmov', $data);
			$ban = $this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'bmov');}

			// Crea smov el pasivo
			$mNUMERO  = $this->datasis->fprox_numero('ndcli');
			$mSQL = "SELECT COUNT(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";

			while($this->datasis->dameval($mSQL) > 0){
				$mNUMERO  = $this->datasis->fprox_numero('ndcli');
				$mSQL = "SELECT COUNT(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";
			}

			$data = array();
			$data['cod_cli']  = $do->get('clipro');
			$data['nombre']   = $do->get('nombre');
			$data['tipo_doc'] = 'ND';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $fecha;
			$data['monto']    = $do->get('monto');
			$data['impuesto'] = 0;
			$data['vence']    = $do->get('vence');
			$data['tipo_ref'] = 'PR';
			$data['num_ref']  = $do->get('numero');
			$data['observa1'] = $do->get('observa1');
			$data['observa2'] = $do->get('observa2');
			$data['banco']    = $do->get('codban');
			$data['fecha_op'] = $do->get('fecha');
			$data['num_op']   = $do->get('numche');
			$data['tipo_op']  = $do->get('tipo');
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('smov', $data);
			$ban = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'smov');}

		//GUARDA PRESTAMO RECIBIDO
		} elseif ( $tipop == '2' ){
			$COMPROB = $do->get('comprob');

			// Crea bmov ingreso
			$this->datasis->actusal($codban, $fecha, $monto );

			$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='".$codban."'");
			$mCUENTA  = $mREG['numcuent'];
			$mBANCO   = $mREG['banco'];
			$mSALDO   = $mREG['saldo'];
			$mTBANCO  = $mREG['tbanco'];
			if ( $mTBANCO == 'CAJ' ) $tipo = 'NC';

			$data = array();
			$data['codbanc']  = $codban;
			$data['numcuent'] = $mCUENTA;
			$data['banco']    = $mBANCO;
			$data['saldo']    = $mSALDO;
			$data['fecha']    = $fecha;
			$data['tipo_op']  = $tipo;
			$data['numero']   = $do->get('numche');
			$data['concepto'] = 'PRESTAMO RECIBIDO '.$do->get('numero');
			$data['clipro']   = 'P';
			$data['concep2']  = $do->get('observa1');
			$data['concep3']  = $do->get('observa2');
			$data['monto']    = $monto;
			$data['codcp']    = $do->get('clipro');
			$data['nombre']   = $do->get('nombre');
			$data['benefi']   = $do->get('benefi');
			$data['comprob']  = $COMPROB;
			$data['negreso']  = $do->get('ningreso');
			$data['posdata']  = $fecha;
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('bmov', $data);
			$ban  = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'bmov');}

			// Crea sprm CxP
			$mNUMERO  = $this->datasis->fprox_numero('num_nd');
			$mSQL = "SELECT count(*) FROM sprm WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";

			while ( $this->datasis->dameval($mSQL) > 0 ) {
				$mNUMERO  = $this->datasis->fprox_numero('num_nd');
				$mSQL = "SELECT count(*) FROM sprm WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";
			}

			$data = array();
			$data['cod_prv']  = $do->get('clipro');
 			$data['nombre']   = $do->get('nombre');
			$data['tipo_doc'] = 'ND';
 			$data['numero']   = $mNUMERO;
   			$data['fecha']    = $fecha;
   			$data['monto']    = $do->get('monto');
			$data['impuesto'] = 0;
   			$data['vence']    = $do->get('vence');
			$data['tipo_ref'] = 'PR';
			$data['num_ref']  = $do->get('numero');
			$data['observa1'] = $do->get('observa1');
			$data['observa2'] = $do->get('observa2');
   			$data['banco']    = $do->get('codban');
			$data['numche']   = $do->get('numche');
 			$data['tipo_op']  = $do->get('tipo');
			$data['benefi']   = $do->get('benefi');
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('sprm', $data);
			$ban = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'sprm');}

		//CHEQUE DEVUELTO CLIENTE
		}elseif($tipop == '3'){

			// Crea bmov egreso
			$this->datasis->actusal($codban, $fecha, -1*$monto );

			$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='".$codban."'");

			$mCUENTA  = $mREG['numcuent'];
			$mBANCO   = $mREG['banco'];
			$mSALDO   = $mREG['saldo'];
			$mTBANCO  = $mREG['tbanco'];
			$COMPROB  = $this->datasis->fprox_numero('ncomprob');

			$data = array();
			$data['codbanc']  = $codban;
			$data['numcuent'] = $mCUENTA;
			$data['banco']    = $mBANCO;
			$data['saldo']    = $mSALDO;
			$data['fecha']    = $fecha;
			$data['tipo_op']  = $do->get('tipo');
			$data['numero']   = $do->get('numche');
			$data['concepto'] = "CHEQUE DEVUELTO CLIENTE ".$do->get('numero');
			$data['clipro']   = 'C';
			$data['concep2']  = $do->get('observa1');
			$data['concep3']  = $do->get('observa2');
			$data['monto']    = $monto;
			$data['codcp']    = $do->get('clipro');
			$data['nombre']   = $do->get('nombre');
			$data['benefi']   = $do->get('benefi');
			$data['comprob']  = $COMPROB;
			$data['negreso']  = '';
			$data['posdata']  = $fecha;
			$data['liable']   = 'S';
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('bmov', $data);
			$ban = $this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'bmov');}


			// Crea smov CxC
			$mNUMERO  = $this->datasis->fprox_numero('ndcli');
			$mSQL = "SELECT COUNT(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";

			while ( $this->datasis->dameval($mSQL) > 0 ) {
				$mNUMERO  = $this->datasis->fprox_numero('ndcli');
				$mSQL = "SELECT COUNT(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";
			}

			$data = array();
			$data['cod_cli']  = $do->get('clipro');
			$data['nombre']   = $do->get('nombre');
			$data['tipo_doc'] = 'ND';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $fecha;
			$data['monto']    = $do->get('monto');
			$data['impuesto'] = 0;
			$data['vence']    = $do->get('vence');
			$data['tipo_ref'] = 'PR';
			$data['num_ref']  = $do->get('numero');
			$data['observa1'] = $do->get('observa1');
			$data['observa2'] = $do->get('observa2');
			$data['banco']    = $do->get('codban');
			$data['fecha_op'] = $do->get('fecha');
			$data['num_op']   = $do->get('numche');
			$data['tipo_op']  = $do->get('tipo');
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('smov', $data);
			$ban  = $this->db->simple_query($mSQL);
			if($ban == false){ memowrite($mSQL,'smov');}

		//GUARDA CHEQUE DEVUELTO A PROVEEDOR
		} elseif ( $tipop == '4' ){

			// Crea bmov egreso
			$this->datasis->actusal($codban, $fecha, $monto );

			$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='".$codban."'");
			$mCUENTA  = $mREG['numcuent'];
			$mBANCO   = $mREG['banco'];
			$mSALDO   = $mREG['saldo'];
			$mTBANCO  = $mREG['tbanco'];

			$data = array();
			$data['codbanc']  = $codban;
			$data['numcuent'] = $mCUENTA;
			$data['banco']    = $mBANCO;
			$data['saldo']    = $mSALDO;
			$data['fecha']    = $fecha;
			$data['tipo_op']  = $do->get('tipo');
			$data['numero']   = $do->get('numche');
			$data['concepto'] = 'CHEQUE O NOTA DEVUELTO DE PROVEEDOR '.$do->get('numero');
			$data['clipro']   = 'P';
			$data['concep2']  = $do->get('observa1');
			$data['concep3']  = $do->get('observa2');
			$data['monto']    = $monto;
			$data['codcp']    = $do->get('clipro');
			$data['nombre']   = $do->get('nombre');
			$data['benefi']   = $do->get('benefi');
			//$data['comprob']  = $COMPROB;
			$data['posdata']  = $fecha;
			$data['negreso']  = '';
			$data['posdata']  = $fecha;
			$data['liable']   = 'N';
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('bmov', $data);
			$ban  = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'bmov');}

			// Crea sprm CxP
			$mNUMERO  = $this->datasis->fprox_numero('num_nd');
			$mSQL = "SELECT COUNT(*) FROM sprm WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";

			while ( $this->datasis->dameval($mSQL) > 0 ) {
				$mNUMERO  = $this->datasis->fprox_numero('num_nd');
				$mSQL = "SELECT COUNT(*) FROM sprm WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";
			}

			$data = array();
			$data['cod_prv']  = $do->get('clipro');
 			$data['nombre']   = $do->get('nombre');
			$data['tipo_doc'] = 'ND';
 			$data['numero']   = $mNUMERO;
   			$data['fecha']    = $fecha;
   			$data['monto']    = $do->get('monto');
			$data['impuesto'] = 0;
   			$data['vence']    = $do->get('vence');
			$data['tipo_ref'] = 'PR';
			$data['num_ref']  = $do->get('numero');
			$data['observa1'] = $do->get('observa1');
			$data['observa2'] = $do->get('observa2');
   			$data['banco']    = $do->get('codban');
			$data['numche']   = $do->get('docum');
 			$data['tipo_op']  = $do->get('tipo');
			$data['benefi']   = $do->get('benefi');
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('sprm', $data);
			$ban = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'sprm');}

			$mTRAN = $this->datasis->dameval("SELECT transac FROM bmov WHERE tipo_op='CH' AND numero='".$do->get('docum')."' AND codbanc='".$codban."'");
			$this->db->simple_query("UPDATE bmov SET liable='N' WHERE tipo_op='CH' AND numero='".$do->get('docum')."' AND codbanc='".$codban."'");


		//GUARDA DEPOSITO POR ANALIZAR
		} elseif ( $tipop == '5' ){

			// Crea bmov ingreso
			$this->datasis->actusal($codban, $fecha, $monto );

			$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='".$codban."'");
			$mCUENTA  = $mREG['numcuent'];
			$mBANCO   = $mREG['banco'];
			$mSALDO   = $mREG['saldo'];
			$mTBANCO  = $mREG['tbanco'];

			$data = array();
			$data['codbanc']  = $codban;
			$data['numcuent'] = $mCUENTA;
			$data['banco']    = $mBANCO;
			$data['saldo']    = $mSALDO;
			$data['fecha']    = $fecha;
			$data['tipo_op']  = $do->get('tipo');
			$data['numero']   = $do->get('numche');
			$data['concepto'] = 'DEPOSITO POR ANALIZAR '.$do->get('numero');
			$data['clipro']   = 'P';
			$data['concep2']  = $do->get('observa1');
			$data['concep3']  = $do->get('observa2');
			$data['monto']    = $monto;
			$data['codcp']    = $do->get('clipro');
			$data['nombre']   = $do->get('nombre');
			$data['benefi']   = $do->get('benefi');
			$data['posdata']  = $fecha;
			$data['negreso']  = '';
			$data['posdata']  = $fecha;
			$data['liable']   = 'S';
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('bmov', $data);
			$ban  = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'bmov');}

			// Crea sprm CxP
			$mNUMERO  = $this->datasis->fprox_numero('num_nd');
			$mSQL = "SELECT COUNT(*) FROM sprm WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";

			while ( $this->datasis->dameval($mSQL) > 0 ) {
				$mNUMERO  = $this->datasis->fprox_numero('num_nd');
				$mSQL = "SELECT COUNT(*) FROM sprm WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";
			}

			$data = array();
			$data['cod_prv']  = $do->get('clipro');
 			$data['nombre']   = $do->get('nombre');
			$data['tipo_doc'] = 'ND';
 			$data['numero']   = $mNUMERO;
   			$data['fecha']    = $fecha;
   			$data['monto']    = $do->get('monto');
			$data['impuesto'] = 0;
   			$data['vence']    = $do->get('vence');
			$data['tipo_ref'] = 'PR';
			$data['num_ref']  = $do->get('numero');
			$data['observa1'] = $do->get('observa1');
			$data['observa2'] = $do->get('observa2');
   			$data['banco']    = $do->get('codban');
			$data['numche']   = $do->get('numche');
 			$data['tipo_op']  = $do->get('tipo');
			//$data['benefi']   = $do->get('benefi');
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('sprm', $data);
			$ban = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'sprm');}

		//GUARDAR CARGOS INDEBIDOS
		}elseif( $tipop == '6' ){

			// Crea bmov egreso
			$this->datasis->actusal($codban, $fecha, -1*$monto );

			$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='".$codban."'");
			$mCUENTA  = $mREG['numcuent'];
			$mBANCO   = $mREG['banco'];
			$mSALDO   = $mREG['saldo'];
			$mTBANCO  = $mREG['tbanco'];

			$data = array();
			$data['codbanc']  = $codban;
			$data['numcuent'] = $mCUENTA;
			$data['banco']    = $mBANCO;
			$data['saldo']    = $mSALDO;
			$data['fecha']    = $fecha;
			$data['tipo_op']  = $do->get('tipo');
			$data['numero']   = $do->get('numche');
			$data['concepto'] = 'CARGOS INDEBIDOS DEL BANCO  '.$do->get('numero');
			$data['clipro']   = 'C';
			$data['concep2']  = $do->get('observa1');
			$data['concep3']  = $do->get('observa2');
			$data['monto']    = $monto;
			$data['codcp']    = $do->get('clipro');
			$data['nombre']   = $do->get('nombre');
			$data['benefi']   = $do->get('benefi');
			$data['posdata']  = $fecha;
			$data['negreso']  = '';
			$data['posdata']  = $fecha;
			$data['liable']   = 'S';
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('bmov', $data);
			$ban  = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'bmov');}

			$mNUMERO  = $this->datasis->fprox_numero('ndcli');
			$mSQL = "SELECT COUNT(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";

			while ( $this->datasis->dameval($mSQL) > 0 ) {
				$mNUMERO  = $this->datasis->fprox_numero('ndcli');
				$mSQL = "SELECT COUNT(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";
			}

			$data = array();
			$data['cod_cli']  = $do->get('clipro');
			$data['nombre']   = $do->get('nombre');
			$data['tipo_doc'] = 'ND';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $fecha;
			$data['monto']    = $do->get('monto');
			$data['impuesto'] = 0;
			$data['vence']    = $do->get('vence');
			$data['tipo_ref'] = 'PR';
			$data['num_ref']  = $do->get('numero');
			$data['observa1'] = $do->get('observa1');
			$data['observa2'] = $do->get('observa2');
			$data['banco']    = $do->get('codban');
			$data['fecha_op'] = $do->get('fecha');
			$data['num_op']   = $do->get('numche');
			$data['tipo_op']  = $do->get('tipo');
			$data['usuario']  = $do->get('usuario');
			$data['transac']  = $do->get('transac');
			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');

			$mSQL = $this->db->insert_string('smov', $data);
			$ban = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'smov');}

		}

		logusu($do->table,"Creo $this->tits ${primary} ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits ${primary} ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits ${primary} ");
	}

	//******************************************************************
	// Busca cliente
	function scriptscli(){
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#vence").datepicker({dateFormat:"dd/mm/yy"});
		});

		$("#clipro").autocomplete({
			delay: 600,
			autoFocus: true,
			source: function(req, add){
			$.ajax({
				url:  "'.site_url('ajax/buscascli').'",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$("#nombre").val("");
						}else{
							$.each(data, function(i, val){ sugiere.push( val );});
						}
						add(sugiere);
					},
			})
			},
			minLength: 2,
			select: function( event, ui ) {
				$("#clipro").attr("readonly", "readonly");
				$("#nombre").val(ui.item.nombre);
				$("#clipro").val(ui.item.cod_cli);
				setTimeout(function() {  $("#clipro").removeAttr("readonly"); }, 1500);
			}
		});
		';

		return $script;
	}

	//******************************************************************
	// Busca proveedor
	function scriptsprv(){
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#vence").datepicker({dateFormat:"dd/mm/yy"});
		});

		$("#clipro").autocomplete({
			delay: 600,
			autoFocus: true,
			source: function(req, add){
				$.ajax({
					url:  "'.site_url('ajax/buscasprv').'",
					type: "POST",
					dataType: "json",
					data: "q="+req.term,
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$("#nombre").val("");
							}else{
								$.each(data, function(i, val){ sugiere.push( val );});
							}
							add(sugiere);
						},
				})
			},
			minLength: 2,
			select: function( event, ui ) {
				$("#clipro").attr("readonly", "readonly");
				$("#nombre").val(ui.item.nombre);
				$("#clipro").val(ui.item.proveed);
				setTimeout(function() {  $("#clipro").removeAttr("readonly"); }, 1500);
			}
		});';
		return $script;

	}

	//*******************************************
	//
	// Guarda cheques devueltos en PRMO
	//
	function prmochdev(){
		$id   = $this->uri->segment($this->uri->total_segments());
		$dbid = $this->db->escape($id);

		$transac = $this->datasis->fprox_numero('ntransa');
		//LOCAL aLISTA, mSQL, aVALORES, mREG, mMEG, mTBANCO

		$mSQL = 'SELECT a.*, b.recibe codban FROM sfpa a JOIN bcaj b ON a.deposito=b.numero WHERE a.id='.$dbid;
		$reg  = $this->datasis->damereg($mSQL);

		if($reg['tipo'] <> 'CH'){
			echo 'Cheque ya devuelto';
			return;
		}

		$XNEGRESO  = '        ';
		$XNINGRESO = '        ';
		$XTIPO     = 'ND';
		$XVENCE    = date('Y/m/d');
		$XFECHA    = date('Y/m/d');
		$XCODBAN   = $reg['codban'];
		$mTBANCO   = $this->datasis->dameval("SELECT tbanco FROM banc WHERE codbanc='${XCODBAN}'");
		$XNUMERO   = $this->datasis->fprox_numero('nprmo');
		$XNOMBRE   = $this->datasis->dameval("SELECT nombre FROM scli WHERE cliente='".$reg['cod_cli']."'");

		// Guarda en PRMO
		$aLISTA['tipop']    = '3';
		$aLISTA['numero']   = $XNUMERO;
		$aLISTA['fecha']    = $XFECHA;
		$aLISTA['codban']   = $XCODBAN;
		$aLISTA['tipo']     = 'ND';
		$aLISTA['numche']   = $reg['num_ref'];
		$aLISTA['benefi']   = '';
		$aLISTA['comprob']  = '';
		$aLISTA['clipro']   = $reg['cod_cli'];
		$aLISTA['nombre']   = $XNOMBRE ;
		$aLISTA['docum']    = $reg['deposito'];
		$aLISTA['banco']    = $reg['banco'] ;
		$aLISTA['monto']    = $reg['monto'] ;
		$aLISTA['cuotas']   = 1 ;
		$aLISTA['vence']    = $XVENCE ;
		$aLISTA['observa1'] = 'CHEQUE DEVUELTO';
		$aLISTA['observa2'] = '';
		$aLISTA['cadano']   = 1;
		$aLISTA['apartir']  = $XFECHA;

		$aLISTA['usuario']  = $this->secu->usuario() ;
		$aLISTA['transac']  = $transac ;
		$aLISTA['estampa']  = date('Y/m/d') ;
		$aLISTA['hora']     = date('H:i:s') ;

		//$aLISTA['negreso']  =  XNEGRESO ;
		//$aLISTA['ningreso'] = XNINGRESO ;

		$this->db->insert('prmo', $aLISTA);

  		// GUARDA EN BANCO

		//ACTUSAL(XCODBAN, XFECHA, XMONTO*IF(XTIPO$'CH,ND',-1,1) )
		$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='$XCODBAN'");

		$mCUENTA   = $mREG['numcuent'];
		$mBANCO    = $mREG['banco'];
		$mSALDO    = $mREG['saldo'];
		$mTBANCO   = $mREG['tbanco'];
		$XCOMPROB  = $this->datasis->fprox_numero("ncomprob");

		$aLISTA = array();
		$aLISTA['codbanc']  = $XCODBAN;
		$aLISTA['numcuent'] = $mCUENTA;
		$aLISTA['banco']    = $mBANCO;
		$aLISTA['saldo']    = $mSALDO;
		$aLISTA['fecha']    = $XFECHA;
		$aLISTA['tipo_op']  = 'ND';
		$aLISTA['numero']   = $reg['num_ref'];
		$aLISTA['concepto'] = "CHEQUE DEVUELTO CLIENTE ".$XNUMERO;
		$aLISTA['clipro']   = 'C';
		$aLISTA['liable']   = 'S';
		$aLISTA['concep2']  = "CHEQUE DEVUELTO CLIENTE ".$reg['cod_cli'];
		$aLISTA['concep3']  = '';
		$aLISTA['monto']    = $reg['monto'];
		$aLISTA['codcp']    = $reg['cod_cli'];
		$aLISTA['nombre']   = $XNOMBRE;
		$aLISTA['comprob']  = $XCOMPROB;
		$aLISTA['posdata']  = $XFECHA;
		//$aLISTA['benefi']   = XBENEFI;
		//$aLISTA['negreso"]  = XNEGRESO;

		$aLISTA['usuario']   = $this->secu->usuario() ;
		$aLISTA['transac']   = $transac ;
		$aLISTA['estampa']   = date('Y/m/d') ;
		$aLISTA['hora']      = date('H:i:s') ;

		$this->db->insert('bmov', $aLISTA);

		$i = 0;
		while ( $i == 0 ){
			$mNUMERO = $this->datasis->fprox_numero('ndcli');
			$mSQL    = "SELECT count(*) FROM smov WHERE tipo_doc='ND' AND numero='$mNUMERO' ";
			$i       = $this->datasis->dameval($mSQL);
		}

		$aLISTA = array();
		$aLISTA['cod_cli']   = $reg['cod_cli'];
		$aLISTA['nombre']    = $XNOMBRE ;
		$aLISTA['tipo_doc']  = 'ND';
		$aLISTA['numero']    = $mNUMERO;
		$aLISTA['fecha']     = $XFECHA;
		$aLISTA['monto']     = $reg['monto'];
		$aLISTA['impuesto']  = 0;
		$aLISTA['vence']     = $XVENCE;
		$aLISTA['tipo_ref']  = 'PR';
		$aLISTA['num_ref']   = $XNUMERO;
		$aLISTA['observa1']  = 'CHEQUE DEVUELTO CLIENTE '.$XNUMERO;
		$aLISTA['observa2']  = 'CHEQUE DEVUELTO CLIENTE '.$reg['cod_cli'];
		$aLISTA['banco']     = $XCODBAN;
		$aLISTA['fecha_op']  = $XFECHA;
		$aLISTA['num_op']    = $reg['num_ref'];
		$aLISTA['tipo_op']   = 'ND';
		$aLISTA['usuario']   = $this->secu->usuario() ;
		$aLISTA['transac']   = $transac ;
		$aLISTA['estampa']   = date('Y/m/d') ;
		$aLISTA['hora']      = date('H:i:s') ;
		$this->db->insert('smov', $aLISTA);
		$this->db->simple_query("UPDATE sfpa SET tipo='CD' WHERE id=${id}");
		echo 'Cheque Devuelto';
	}

	function instalar(){
		if(!$this->db->table_exists('prmo')){
			$mSQL="CREATE TABLE `prmo` (
				`tipop` CHAR(1) NULL DEFAULT NULL,
				`numero` VARCHAR(8) NOT NULL DEFAULT '',
				`fecha` DATE NULL DEFAULT NULL,
				`codban` CHAR(2) NULL DEFAULT NULL,
				`tipo` CHAR(2) NULL DEFAULT NULL,
				`numche` VARCHAR(12) NULL DEFAULT NULL,
				`benefi` VARCHAR(30) NULL DEFAULT NULL,
				`comprob` VARCHAR(6) NULL DEFAULT NULL,
				`clipro` VARCHAR(5) NULL DEFAULT NULL,
				`nombre` VARCHAR(30) NULL DEFAULT NULL,
				`docum` VARCHAR(12) NULL DEFAULT NULL,
				`banco` VARCHAR(10) NULL DEFAULT NULL,
				`monto` DECIMAL(13,2) NULL DEFAULT NULL,
				`cuotas` INT(2) NULL DEFAULT NULL,
				`vence` DATE NULL DEFAULT NULL,
				`observa1` VARCHAR(50) NULL DEFAULT NULL,
				`observa2` VARCHAR(50) NULL DEFAULT NULL,
				`transac` VARCHAR(8) NULL DEFAULT NULL,
				`estampa` DATE NULL DEFAULT NULL,
				`hora` VARCHAR(8) NULL DEFAULT NULL,
				`usuario` VARCHAR(12) NULL DEFAULT NULL,
				`cadano` INT(6) NULL DEFAULT NULL,
				`apartir` INT(6) NULL DEFAULT NULL,
				`negreso` CHAR(8) NULL DEFAULT NULL,
				`ningreso` CHAR(8) NULL DEFAULT NULL,
				`retencion` CHAR(14) NULL DEFAULT NULL,
				`factura` CHAR(12) NULL DEFAULT NULL,
				`remision` DATE NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `numero` (`numero`),
				INDEX `transaccion` (`transac`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('prmo');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE prmo DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE prmo ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE prmo ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}
	}
}
