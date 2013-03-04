<?php
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
		if ( !$this->datasis->iscampo('prmo','id') ) {
			$this->db->simple_query('ALTER TABLE prmo DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE prmo ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE prmo ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->creaintramenu(array('modulo'=>'52A','titulo'=>'Otros Movimientos','mensaje'=>'Otros Movimientos','panel'=>'TESORERIA','ejecutar'=>'finanzas/prmo','target'=>'popu','visible'=>'S','pertenece'=>'5','ancho'=>900,'alto'=>600));
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
		$grid->wbotonadd(array("id"=>"prmo1", "img"=>"images/mano.png",          "alt" => "Prestamo Otorgado",        "label"=>"Prestamo Otorgado",     "tema"=>"anexos"));
		$grid->wbotonadd(array("id"=>"prmo2", "img"=>"images/check.png",         "alt" => "Prestamo Recibido",        "label"=>"Prestamo Recibido",     "tema"=>"anexos"));
		$grid->wbotonadd(array("id"=>"prmo3", "img"=>"images/face-sad.png",      "alt" => "Cheq Devuelto Cliente",    "label"=>"Cheq Devuelto Cliente", "tema"=>"anexos"));
		$grid->wbotonadd(array("id"=>"prmo4", "img"=>"images/face-surprise.png", "alt" => "Cheq Devuelto Proveed",    "label"=>"Cheq Devuelto Proveed", "tema"=>"anexos"));
		$grid->wbotonadd(array("id"=>"prmo5", "img"=>"images/dinero.png",        "alt" => "Deposito por Analizar",    "label"=>"Deposito por Analizar", "tema"=>"tema1"));
		$grid->wbotonadd(array("id"=>"prmo6", "img"=>"images/retencion.gif",     "alt" => "Cargos Indebidos en Banco","label"=>"Cargos Indebidos ",     "tema"=>"anexos"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'] );


		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['centerpanel'] = $centerpanel;
		$param['readyLayout'] = $readyLayout;
		$param['listados']    = $this->datasis->listados('PRMO', 'JQ');
		$param['otros']       = $this->datasis->otros('PRMO', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1','blitzer');
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

		// Prestamo Otorgado CxC
		// Crea una ND en cliente
		// Crea un Movimiento en bmov correspondiente al cheque o efectivo entregado
		// cuando crea el egreso llena el campo negreso
		$bodyscript .= '
		$("#prmo1").click( function() {
			$.post("'.site_url($this->url.'deprmo1/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 620, title: "Prestamo Otorgado"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';

		// Prestamo Recibido
		// Crea una ND en proveedor CxP
		// Crea un Movimiento en bmov correspondiente al deposito o cheque recibdo 
		// cuando crea el ingreso llena el campo ningreso
		$bodyscript .= '
		$("#prmo2").click( function() {
			$.post("'.site_url($this->url.'deprmo2/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 610, title: "Prestamo Recibido"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';

		// Cheque devuelto de Cliente
		// Crea una ND en el cliente CxP
		// Crea un Movimiento en bmov correspondiente a ND 
		// 
		$bodyscript .= '
		$("#prmo3").click( function() {
			$.post("'.site_url($this->url.'deprmo3/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 610, title: "Prestamo Recibido"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';


		// Cheque devuelto de Proveedor
		// Crea una ND en el cliente CxP
		// Crea un Movimiento en bmov correspondiente a ND 
		// 
		$bodyscript .= '
		$("#prmo4").click( function() {
			$.post("'.site_url($this->url.'deprmo4/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 610, title: "Prestamo Recibido"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';


		// Depositos por Analizar
		// Crea una ND en el cliente CxP
		// Crea un Movimiento en bmov correspondiente a ND 
		// 
		$bodyscript .= '
		$("#prmo5").click( function() {
			$.post("'.site_url($this->url.'deprmo5/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 610, title: "Prestamo Recibido"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';

		// Cargos Indebidos
		// Crea una ND en el cliente CxP
		// Crea un Movimiento en bmov correspondiente a ND 
		// 
		$bodyscript .= '
		$("#prmo6").click( function() {
			$.post("'.site_url($this->url.'deprmo6/create').'",
			function(data){
				$("#fedita").dialog( {height: 400, width: 610, title: "Prestamo Recibido"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';


		$bodyscript .= '
		function prmoadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};
		';

		$bodyscript .= '
		function prmoedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("#newapi'.$grid0.'").getRowData(id);
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/PRMO').'/\'+res.id+\'/id\'').';
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

		$grid->addField('tipop');
		$grid->label('Tipop');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('numero');
		$grid->label('Numero');
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
		$grid->label('Codban');
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
		$grid->label('Numche');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('benefi');
		$grid->label('Benefi');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('comprob');
		$grid->label('Comprob');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('clipro');
		$grid->label('Clipro');
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
		$grid->label('Docum');
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
		$grid->label('Observa1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('observa2');
		$grid->label('Observa2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('transac');
		$grid->label('Transac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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
		$grid->label('Negreso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('ningreso');
		$grid->label('Ningreso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('retencion');
		$grid->label('Retencion');
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
		$grid->label('Remision');
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
		$grid->setAdd(    $this->datasis->sidapuede('PRMO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('PRMO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PRMO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PRMO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: prmoadd, editfunc: prmoedit, delfunc: prmodel, viewfunc: prmoshow");

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
		$mcodp  = "??????";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM prmo WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('prmo', $data);
					echo "Registro Agregado";

					logusu('PRMO',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM prmo WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM prmo WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE prmo SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("prmo", $data);
				logusu('PRMO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('prmo', $data);
				logusu('PRMO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM prmo WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM prmo WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM prmo WHERE id=$id ");
				logusu('PRMO',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
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
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->calendar = false;

		$edit->codban = new dropdownField('Caja o Banco','codban');
		$edit->codban->option('','Seleccionar');
		$edit->codban->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND codbanc>'00' ORDER BY tbanco='CAJ' , codbanc");
		$edit->codban->rule  = 'required';
		$edit->codban->style = "width:250px;";
/*
		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('ND','Nota Debito');
		$edit->tipo->option('CH','Cheque');  // solo si es banco
		$edit->tipo->style = 'width:120px';
*/
		$edit->numche = new inputField('Numero','numche');
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
		$edit->vence->size        = 10;
		$edit->vence->maxlength   =  8;
		$edit->vence->calendar    = false;
		$edit->vence->insertValue =  date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")+30, date("Y")));

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

		$edit->retencion = new inputField('Retencion','retencion');
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
		});
		';


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
		});
		';

		
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
		});
		';



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
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
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
		if ( $tipop == '1' ){

			// Crea bmov egreso
			$this->datasis->actusal($codban, $fecha, -1*$monto );
		
			$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='".$codban."'");

			$mCUENTA  = $mREG['numcuent'];
			$mBANCO   = $mREG['banco'];
			$mSALDO   = $mREG['saldo'];
			$mTBANCO  = $mREG['tbanco'];
			if ( $mTBANCO == 'CAJ' ) $tipo = 'ND';
			
			$data = array();
			$data["codbanc"]  = $codban;
			$data["numcuent"] = $mCUENTA;
			$data["banco"]    = $mBANCO;
			$data["saldo"]    = $mSALDO;
			$data["fecha"]    = $fecha;
			$data["tipo_op"]  = $tipo;
			$data["numero"]   = $do->get('numche');
			$data["concepto"] = "PRESTAMO OTORGADO ".$do->get('numero');
			$data["clipro"]   = 'C'; 
			$data["concep2"]  = $do->get('observa1');
			$data["concep3"]  = $do->get('observa2');
			$data["monto"]    = $monto;
			$data["codcp"]    = $do->get('clipro');
			$data["nombre"]   = $do->get('nombre');
			$data["benefi"]   = $do->get('benefi');
			//$data["comprob"]  = $COMPROB;
			$data["negreso"]  = $do->get('negreso');
			$data["posdata"]  = $fecha;
			$data["liable"]   = 'S';

			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

			$mSQL = $this->db->insert_string('bmov', $data);
			$ban = $this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'bmov');}

			// Crea smov el pasivo
			$mNUMERO  = $this->datasis->fprox_numero('ndcli');
			$mSQL = "SELECT count(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";

			while ( $this->datasis->dameval($mSQL) > 0 ) {
				$mNUMERO  = $this->datasis->fprox_numero('ndcli');
				$mSQL = "SELECT count(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";
			}

			$data = array();
			$data["cod_cli"]  = $do->get('clipro');
			$data["nombre"]   = $do->get('nombre');
			$data["tipo_doc"] = "ND";
			$data["numero"]   = $mNUMERO;
			$data["fecha"]    = $fecha;
			$data["monto"]    = $do->get('monto');
			$data["impuesto"] = 0;
			$data["vence"]    = $do->get('vence');
			$data["tipo_ref"] = "PR";
			$data["num_ref"]  = $do->get('numero');
			$data["observa1"] = $do->get('observa1');
			$data["observa2"] = $do->get('observa2');
			$data["banco"]    = $do->get('codban');
			$data["fecha_op"] = $do->get('fecha');
			$data["num_op"]   = $do->get('numche');
			$data["tipo_op"]  = $do->get('tipo');
			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

			$mSQL = $this->db->insert_string('smov', $data);
			$ban = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'smov');}

		//GUARDA PRESTAMO RECIBIDO
		} elseif ( $tipop == '2' ){

			// Crea bmov ingreso
			$this->datasis->actusal($codban, $fecha, $monto );

			$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='".$codban."'");
			$mCUENTA  = $mREG['numcuent'];
			$mBANCO   = $mREG['banco'];
			$mSALDO   = $mREG['saldo'];
			$mTBANCO  = $mREG['tbanco'];
			if ( $mTBANCO == 'CAJ' ) $tipo = 'NC';

			$data = array();
			$data["codbanc"]  = $codban;
			$data["numcuent"] = $mCUENTA;
			$data["banco"]    = $mBANCO;
			$data["saldo"]    = $mSALDO;
			$data["fecha"]    = $fecha;
			$data["tipo_op"]  = $tipo;
			$data["numero"]   = $do->get('numche');
			$data["concepto"] = "PRESTAMO RECIBIDO ".$do->get('numero');
			$data["clipro"]   = 'P'; 
			$data["concep2"]  = $do->get('observa1');
			$data["concep3"]  = $do->get('observa2');
			$data["monto"]    = $monto;
			$data["codcp"]    = $do->get('clipro');
			$data["nombre"]   = $do->get('nombre');
			$data["benefi"]   = $do->get('benefi');
			$data["comprob"]  = $COMPROB;
			$data["negreso"]  = $do->get('ningreso');
			$data["posdata"]  = $fecha;

			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

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
			$data["cod_prv"]  = $do->get('clipro');
 			$data["nombre"]   = $do->get('nombre');
			$data["tipo_doc"] = "ND";
 			$data["numero"]   = $mNUMERO;
   			$data["fecha"]    = $fecha;
   			$data["monto"]    = $do->get('monto');
			$data["impuesto"] = 0;
   			$data["vence"]    = $do->get('vence');
			$data["tipo_ref"] = "PR";
			$data["num_ref"]  = $do->get('numero');
			$data["observa1"] = $do->get('observa1');
			$data["observa2"] = $do->get('observa2');
   			$data["banco"]    = $do->get('codban');
			$data["numche"]   = $do->get('numche');
 			$data["tipo_op"]  = $do->get('tipo');
			$data["benefi"]   = $do->get('benefi');
			
			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

			$mSQL = $this->db->insert_string('sprm', $data);
			$ban = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'sprm');}

		//CHEQUE DEVUELTO CLIENTE
		} elseif ( $tipop == '3' ){

			// Crea bmov egreso
			$this->datasis->actusal($codban, $fecha, -1*$monto );
		
			$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='".$codban."'");

			$mCUENTA  = $mREG['numcuent'];
			$mBANCO   = $mREG['banco'];
			$mSALDO   = $mREG['saldo'];
			$mTBANCO  = $mREG['tbanco'];
			$COMPROB  = $this->datasis->fprox_numero('ncomprob');
			
			$data = array();
			$data["codbanc"]  = $codban;
			$data["numcuent"] = $mCUENTA;
			$data["banco"]    = $mBANCO;
			$data["saldo"]    = $mSALDO;
			
			$data["fecha"]    = $fecha;
			$data["tipo_op"]  = $do->get('tipo');
			$data["numero"]   = $do->get('numche');
			$data["concepto"] = "CHEQUE DEVUELTO CLIENTE ".$do->get('numero');
			$data["clipro"]   = 'C'; 
			$data["concep2"]  = $do->get('observa1');
			$data["concep3"]  = $do->get('observa2');
			$data["monto"]    = $monto;
			$data["codcp"]    = $do->get('clipro');
			$data["nombre"]   = $do->get('nombre');
			$data["benefi"]   = $do->get('benefi');
			$data["comprob"]  = $COMPROB;
			$data["negreso"]  = '';
			$data["posdata"]  = $fecha;
			$data["liable"]   = 'S';

			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

			$mSQL = $this->db->insert_string('bmov', $data);
			$ban = $this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'bmov');}


			// Crea smov CxC
			$mNUMERO  = $this->datasis->fprox_numero('ndcli');
			$mSQL = "SELECT count(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";

			while ( $this->datasis->dameval($mSQL) > 0 ) {
				$mNUMERO  = $this->datasis->fprox_numero('ndcli');
				$mSQL = "SELECT count(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";
			}

			$data = array();
			$data["cod_cli"]  = $do->get('clipro');
			$data["nombre"]   = $do->get('nombre');
			$data["tipo_doc"] = "ND";
			$data["numero"]   = $mNUMERO;
			$data["fecha"]    = $fecha;
			$data["monto"]    = $do->get('monto');
			$data["impuesto"] = 0;
			$data["vence"]    = $do->get('vence');
			$data["tipo_ref"] = "PR";
			$data["num_ref"]  = $do->get('numero');
			$data["observa1"] = $do->get('observa1');
			$data["observa2"] = $do->get('observa2');

			$data["banco"]    = $do->get('codban');
			$data["fecha_op"] = $do->get('fecha');
			$data["num_op"]   = $do->get('numche');
			$data["tipo_op"]  = $do->get('tipo');
			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

			$mSQL = $this->db->insert_string('smov', $data);
			$ban = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'smov');}

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
			$data["codbanc"]  = $codban;
			$data["numcuent"] = $mCUENTA;
			$data["banco"]    = $mBANCO;
			$data["saldo"]    = $mSALDO;

			$data["fecha"]    = $fecha;
			$data["tipo_op"]  = $do->get('tipo');
			$data["numero"]   = $do->get('numche');
			$data["concepto"] = "CHEQUE O NOTA DEVUELTO DE PROVEEDOR ".$do->get('numero');
			$data["clipro"]   = 'P'; 
			$data["concep2"]  = $do->get('observa1');
			$data["concep3"]  = $do->get('observa2');
			$data["monto"]    = $monto;
			$data["codcp"]    = $do->get('clipro');
			$data["nombre"]   = $do->get('nombre');
			$data["benefi"]   = $do->get('benefi');
			//$data["comprob"]  = $COMPROB;
			$data["posdata"]  = $fecha;
			$data["negreso"]  = '';
			$data["posdata"]  = $fecha;
			$data["liable"]   = 'N';

			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

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
			$data["cod_prv"]  = $do->get('clipro');
 			$data["nombre"]   = $do->get('nombre');
			$data["tipo_doc"] = "ND";
 			$data["numero"]   = $mNUMERO;
   			$data["fecha"]    = $fecha;
   			$data["monto"]    = $do->get('monto');
			$data["impuesto"] = 0;
   			$data["vence"]    = $do->get('vence');
			$data["tipo_ref"] = "PR";
			$data["num_ref"]  = $do->get('numero');
			$data["observa1"] = $do->get('observa1');
			$data["observa2"] = $do->get('observa2');
   			$data["banco"]    = $do->get('codban');
			$data["numche"]   = $do->get('docum');
 			$data["tipo_op"]  = $do->get('tipo');
			$data["benefi"]   = $do->get('benefi');
			
			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

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
			$data["codbanc"]  = $codban;
			$data["numcuent"] = $mCUENTA;
			$data["banco"]    = $mBANCO;
			$data["saldo"]    = $mSALDO;

			$data["fecha"]    = $fecha;
			$data["tipo_op"]  = $do->get('tipo');
			$data["numero"]   = $do->get('numche');
			$data["concepto"] = "DEPOSITO POR ANALIZAR ".$do->get('numero');

			$data["clipro"]   = 'P'; 
			$data["concep2"]  = $do->get('observa1');
			$data["concep3"]  = $do->get('observa2');
			$data["monto"]    = $monto;
			$data["codcp"]    = $do->get('clipro');
			$data["nombre"]   = $do->get('nombre');
			$data["benefi"]   = $do->get('benefi');

			$data["posdata"]  = $fecha;
			$data["negreso"]  = '';
			$data["posdata"]  = $fecha;
			$data["liable"]   = 'S';

			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

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
			$data["cod_prv"]  = $do->get('clipro');
 			$data["nombre"]   = $do->get('nombre');
			$data["tipo_doc"] = "ND";
 			$data["numero"]   = $mNUMERO;
   			$data["fecha"]    = $fecha;
   			$data["monto"]    = $do->get('monto');
			$data["impuesto"] = 0;
   			$data["vence"]    = $do->get('vence');
			$data["tipo_ref"] = "PR";
			$data["num_ref"]  = $do->get('numero');
			$data["observa1"] = $do->get('observa1');
			$data["observa2"] = $do->get('observa2');
   			$data["banco"]    = $do->get('codban');
			$data["numche"]   = $do->get('numche');
 			$data["tipo_op"]  = $do->get('tipo');
			//$data["benefi"]   = $do->get('benefi');
			
			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

			$mSQL = $this->db->insert_string('sprm', $data);
			$ban = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'sprm');}



		//GUARDAR CARGOS INDEBIDOS
		} elseif ( $tipop == '6' ){

			// Crea bmov egreso
			$this->datasis->actusal($codban, $fecha, -1*$monto );

			$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='".$codban."'");
			$mCUENTA  = $mREG['numcuent'];
			$mBANCO   = $mREG['banco'];
			$mSALDO   = $mREG['saldo'];
			$mTBANCO  = $mREG['tbanco'];
			
			$data = array();
			$data["codbanc"]  = $codban;
			$data["numcuent"] = $mCUENTA;
			$data["banco"]    = $mBANCO;
			$data["saldo"]    = $mSALDO;

			$data["fecha"]    = $fecha;
			$data["tipo_op"]  = $do->get('tipo');
			$data["numero"]   = $do->get('numche');
			$data["concepto"] = "CARGOS INDEBIDOS DEL BANCO  ".$do->get('numero');

			$data["clipro"]   = 'C'; 
			$data["concep2"]  = $do->get('observa1');
			$data["concep3"]  = $do->get('observa2');
			$data["monto"]    = $monto;
			$data["codcp"]    = $do->get('clipro');
			$data["nombre"]   = $do->get('nombre');
			$data["benefi"]   = $do->get('benefi');

			$data["posdata"]  = $fecha;
			$data["negreso"]  = '';
			$data["posdata"]  = $fecha;
			$data["liable"]   = 'S';

			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

			$mSQL = $this->db->insert_string('bmov', $data);
			$ban  = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'bmov');}


			$mNUMERO  = $this->datasis->fprox_numero('ndcli');
			$mSQL = "SELECT count(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";

			while ( $this->datasis->dameval($mSQL) > 0 ) {
				$mNUMERO  = $this->datasis->fprox_numero('ndcli');
				$mSQL = "SELECT count(*) FROM smov WHERE tipo_doc='ND' AND numero='".$mNUMERO."' ";
			}

			$data = array();
			$data["cod_cli"]  = $do->get('clipro');
			$data["nombre"]   = $do->get('nombre');
			$data["tipo_doc"] = "ND";
			$data["numero"]   = $mNUMERO;
			$data["fecha"]    = $fecha;
			$data["monto"]    = $do->get('monto');
			$data["impuesto"] = 0;
			$data["vence"]    = $do->get('vence');
			$data["tipo_ref"] = "PR";
			$data["num_ref"]  = $do->get('numero');
			$data["observa1"] = $do->get('observa1');
			$data["observa2"] = $do->get('observa2');

			$data["banco"]    = $do->get('codban');
			$data["fecha_op"] = $do->get('fecha');
			$data["num_op"]   = $do->get('numche');
			$data["tipo_op"]  = $do->get('tipo');
			$data["usuario"]  = $do->get('usuario');
			$data["transac"]  = $do->get('transac');
			$data["estampa"]  = $do->get('estampa');
			$data["hora"]     = $do->get('hora');

			$mSQL = $this->db->insert_string('smov', $data);
			$ban = $this->db->simple_query($mSQL);
			if( $ban == false ){ memowrite($mSQL,'smov');}

		}
	
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



/*

// -- Funcion de escritura Replace -- \\
// -- Lee los campos 
FUNCTION PRMORC
LOCAL mTRANSAC := PROX_SQL("ntransa")
LOCAL aLISTA, mSQL, aVALORES, mREG, mMEG, mTBANCO

	XNEGRESO  := SPACE(8)
	XNINGRESO := SPACE(8)

	IF XTIPO = 'CH' .AND. EMPTY(XBENEFI)
		XBENEFI := XNOMBRE
	ENDIF

	IF EMPTY(XVENCE)
		XVENCE := XFECHA
	ENDIF

	mTBANCO := DAMEVAL("SELECT tbanco FROM banc WHERE codbanc='"+XCODBAN+"'")
	IF mTBANCO = 'CAJ'
		XNUMCHE := BANCPROX(XCODBAN)
		// ARREGLA OBSERVA2
		XOBSERVA2 := PADR(ALLTRIM(XOBSERVA2)+XNUMCHE,50)
	ENDIF

	XNUMERO := PROX_SQL("nprmo")
	aLISTA := {}
	AADD(aLISTA, {"TIPOP",    XTIPOP })
	AADD(aLISTA, {"NUMERO",   XNUMERO })
	AADD(aLISTA, {"FECHA",    XFECHA })
	AADD(aLISTA, {"CODBAN",   XCODBAN })
	AADD(aLISTA, {"TIPO",     XTIPO })
	AADD(aLISTA, {"NUMCHE",   XNUMCHE })
	AADD(aLISTA, {"BENEFI",   XBENEFI })
	AADD(aLISTA, {"COMPROB",  XCOMPROB })
	AADD(aLISTA, {"CLIPRO",   XCLIPRO })
	AADD(aLISTA, {"NOMBRE",   XNOMBRE })
	AADD(aLISTA, {"DOCUM",    XDOCUM })
	AADD(aLISTA, {"BANCO",    XBANCO })
	AADD(aLISTA, {"MONTO",    XMONTO })
	AADD(aLISTA, {"CUOTAS",   XCUOTAS })
	AADD(aLISTA, {"VENCE",    XVENCE })
	AADD(aLISTA, {"OBSERVA1", XOBSERVA1 })
	AADD(aLISTA, {"OBSERVA2", XOBSERVA2 })

	AADD(aLISTA, {"CADANO",   XCADANO })
	AADD(aLISTA, {"APARTIR",  XAPARTIR })

	AADD(aLISTA, {"NEGRESO",  XNEGRESO })
	AADD(aLISTA, {"NINGRESO", XNINGRESO })
   
	IF XTIPOP = '1' .AND. XCLIPRO = 'REIVA'
		AADD(aLISTA, {"RETENCION", XRETENCION })
		AADD(aLISTA, {"REMISION",  XREMISION })
		AADD(aLISTA, {"FACTURA",   XFACTURA })
	ENDIF

	mSQL := "INSERT INTO prmo SET "
	aVALORES := {}
	LLENASQL(@mSQL, @aVALORES, aLISTA, mTRANSAC )

	IF mmTIPO = 'I'
		LOGUSU("OTROS MOV. DE CAJA Y BANCOS "+XTIPOP+" "+XNUMERO+" CREADO, TRANSACCION "+mTRANSAC )
		oCursor:Insert()
	ELSE
		LOGUSU("OTROS MOV. DE CAJA Y BANCOS "+XTIPOP+" "+XNUMERO+" MODIFICADO, TRANSACCION "+mTRANSAC )
	ENDIF
	GUARDAOCUR( aLISTA, mTRANSAC )

	// GUARDA EN BANCO

	ACTUSAL(XCODBAN, XFECHA, XMONTO*IF(XTIPO$'CH,ND',-1,1) )
	mREG := DAMEREG("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='"+XCODBAN+"'")

	mCUENTA := mREG[1]
	mBANCO  := mREG[2]
	mSALDO  := mREG[3]
	mTBANCO := mREG[4]
	XCOMPROB1 := PROX_SQL("ncomprob")

	aLISTA := {}
	AADD(aLISTA, {"CODBANC",  XCODBAN })
	AADD(aLISTA, {"NUMCUENT", mCUENTA })
	AADD(aLISTA, {"BANCO",    mBANCO })
	AADD(aLISTA, {"SALDO",    mSALDO })
	AADD(aLISTA, {"FECHA",    XFECHA })
	AADD(aLISTA, {"TIPO_OP",  XTIPO })
	AADD(aLISTA, {"NUMERO",   XNUMCHE })

	IF XTIPOP = '1'
		AADD(aLISTA, {"CONCEPTO", "PRESTAMO OTORGADO "+XNUMERO })
		AADD(aLISTA, {"CLIPRO",   'C' })
		XNEGRESO  := PROX_SQL("negreso")

	ELSEIF XTIPOP = '2'
		AADD(aLISTA, {"CONCEPTO", "PRESTAMO RECIBIDO "+XNUMERO })
		AADD(aLISTA, {"CLIPRO",   'P' })
		IF DAMEVAL("SELECT tbanco FROM banc WHERE codbanc='"+XCODBAN+"' ") = 'CAJ'
			IF EMPTY(XNUMCHE)
				XNUMCHE := BANCPROX(XCODBAN)
			ENDIF
		ENDIF
		IF TRAEVALOR('PAIS') = 'COLOMBIA'
			XNINGRESO  := PROX_SQL("ningreso")
		ELSE
			XNINGRESO  := PROX_SQL("nrcaja")  //NO SE PQ?
		ENDIF

	ELSEIF XTIPOP = '3'
		AADD(aLISTA, {"CONCEPTO", "CHEQUE DEVUELTO CLIENTE " + XNUMERO })
		AADD(aLISTA, {"CLIPRO",   'C' })
		AADD(aLISTA, {"LIABLE",   'S' })

	ELSEIF XTIPOP = '4'
		AADD(aLISTA, {"CONCEPTO", "CHEQUE O NOTA DEVUELTO DE PROVEEDOR " + XNUMERO })
		AADD(aLISTA, {"CLIPRO",   'P' })
		AADD(aLISTA, {"LIABLE",   'N' })

	ELSEIF XTIPOP = '5'
		AADD(aLISTA, {"CONCEPTO", "DEPOSITO POR ANALIZAR " + XNUMERO })
		AADD(aLISTA, {"CLIPRO",   'P' })

	ELSEIF XTIPOP = '6'
		AADD(aLISTA, {"CONCEPTO", "CARGOS INDEBIDOS DEL BANCO " + XNUMERO })
		AADD(aLISTA, {"CLIPRO",   'C' })
	ENDIF

	AADD(aLISTA, {"CONCEP2", XOBSERVA1 })
	AADD(aLISTA, {"CONCEP3", XOBSERVA2 })
	AADD(aLISTA, {"MONTO",   XMONTO })
	AADD(aLISTA, {"CODCP",   XCLIPRO })
	AADD(aLISTA, {"NOMBRE",  XNOMBRE })
	AADD(aLISTA, {"BENEFI",  XBENEFI })
	AADD(aLISTA, {"COMPROB", XCOMPROB })
	AADD(aLISTA, {"POSDATA", XFECHA })

	IF XTIPOP = '2'
		AADD(aLISTA, {"NEGRESO", XNINGRESO })
	ELSE
		AADD(aLISTA, {"NEGRESO", XNEGRESO })
	ENDIF

	mSQL := "INSERT INTO bmov SET "
	aVALORES := {}
	LLENASQL(@mSQL, @aVALORES, aLISTA, mTRANSAC )
	EJECUTASQL(mSQL,aVALORES)

	IF XTIPOP = "4"

		mTRAN := DAMEVAL("SELECT transac FROM bmov WHERE tipo_op='CH' AND numero='"+XDOCUM+"' AND codbanc='"+XCODBAN+"'")
		EJECUTASQL("UPDATE bmov SET liable='N' WHERE tipo_op='CH' AND numero='"+XDOCUM+"' AND codbanc='"+XCODBAN+"'")

		IF !EMPTY(mTRAN)
			EJECUTASQL("UPDATE bmov SET liable='N' WHERE transac='"+mTRAN+"'")

			mIDB := DAMEVAL("SELECT monto FROM bmov WHERE transac='"+mTRAN+  ;
                 "' AND SUBSTRING(numero,1,3)='IDB'",,'N')

			IF VALTYPE(mIDB) <> 'N'
				mIDB := 0
			ENDIF

			// DEVUELVE EL IDB
			IF mIDB <> 0
				//SELECT OTIN
				mNUMERO := "O"+SUBSTR(PROX_SQL("notinf"),2,7)
				aLISTA := {}
				AADD(aLISTA, {"TIPO_DOC", 'FE' })
				AADD(aLISTA, {"NUMERO",   mNUMERO })
				AADD(aLISTA, {"FECHA",    XFECHA })
				AADD(aLISTA, {"ORDEN",    "" })
				AADD(aLISTA, {"COD_CLI",  "" })  
				AADD(aLISTA, {"RIFCI",    "" })    
				AADD(aLISTA, {"NOMBRE",   "" })   
				AADD(aLISTA, {"DIREC",    "" })
				AADD(aLISTA, {"DIRE1",    "" })
				AADD(aLISTA, {"TOTALS",   mIDB })
				AADD(aLISTA, {"IVA",      0 })
				AADD(aLISTA, {"TOTALG",   mIDB })
				AADD(aLISTA, {"VENCE",    XVENCE })
				AADD(aLISTA, {"OBSERVA1", 'REVERSO DE IDB POR CHEQUE DEVUELO' })
				AADD(aLISTA, {"OBSERVA2", 'EMITIDO A PROVEEDOR REF# '+XNUMERO })

				mSQL := "INSERT INTO otin SET "
				aVALORES := {}
				LLENASQL(@mSQL, @aVALORES, aLISTA, mTRANSAC )
				EJECUTASQL(mSQL,aVALORES)

				//SELECT ITOTIN
				aLISTA := {}
				AADD(aLISTA, {"TIPO_DOC", 'FE' })
				AADD(aLISTA, {"NUMERO",   mNUMERO })
				AADD(aLISTA, {"CODIGO",   "IDB" })
				AADD(aLISTA, {"DESCRIP",  "IDB RECUPERADO" })
				AADD(aLISTA, {"PRECIO",   mIDB })
				AADD(aLISTA, {"IMPUESTO", 0 })
				AADD(aLISTA, {"IMPORTE",  mIDB })

				mSQL := "INSERT INTO itotin SET "
				aVALORES := {}
				LLENASQL(@mSQL, @aVALORES, aLISTA, mTRANSAC )
				EJECUTASQL(mSQL,aVALORES)

				//SELECT BANC
				//SEEK XCODBAN
				ACTUSAL(XCODBAN, XFECHA, mIDB)
				mSQL := "SELECT numcuent, banco, moneda, saldo FROM banc "
				mSQL += "WHERE codbanc='"+XCODBAN+"'"
				mREG := DAMEREG(mSQL)
				aLISTA := {}
				AADD(aLISTA, {"CODBANC",  XCODBAN })
				AADD(aLISTA, {"NUMCUENT", mREG[1] })
				AADD(aLISTA, {"BANCO",    mREG[2] })
				AADD(aLISTA, {"MONEDA",   mREG[3] })
				AADD(aLISTA, {"SALDO",    mREG[4] })
				AADD(aLISTA, {"FECHA",    XFECHA })
				AADD(aLISTA, {"BENEFI",   '' })
				AADD(aLISTA, {"TIPO_OP",  "NC" })
				AADD(aLISTA, {"NUMERO",   "DIDB"+mNUMERO })
				AADD(aLISTA, {"CONCEPTO", XOBSERVA1 })
				AADD(aLISTA, {"CONCEP2",  XOBSERVA2 })
				AADD(aLISTA, {"CONCEP3",  '' })
				AADD(aLISTA, {"MONTO",    mIDB })
				AADD(aLISTA, {"CLIPRO",   'P' })
				AADD(aLISTA, {"CODCP",    "IDB" })
				AADD(aLISTA, {"LIABLE",    "S" })
				AADD(aLISTA, {"NOMBRE",   "IMPUESTO AL DEBITO BANCARIO" })

				mSQL := "INSERT INTO bmov SET "
				aVALORES := {}
				LLENASQL(@mSQL, @aVALORES, aLISTA, mTRANSAC )
				EJECUTASQL(mSQL,aVALORES)

				aLISTA := {}
				AADD(aLISTA, {"TIPO_DOC",  "NC" })
				AADD(aLISTA, {"NUMERO",    mNUMERO })
				AADD(aLISTA, {"TIPO",      "NC" })
				AADD(aLISTA, {"MONTO",     mIDB })
				AADD(aLISTA, {"NUM_REF",   mNUMERO })
				AADD(aLISTA, {"FECHA",     XFECHA })
				AADD(aLISTA, {"BANCO",     XCODBAN })
				AADD(aLISTA, {"F_FACTURA", XFECHA })
				AADD(aLISTA, {"COD_CLI",   XCLIPRO })
				AADD(aLISTA, {"VENDEDOR",  '' })
				AADD(aLISTA, {"CLAVE",     '' })
				AADD(aLISTA, {"CAMBIO",    0 })
				AADD(aLISTA, {"COBRADOR",  '' })

				mSQL := "INSERT INTO sfpa SET "
				aVALORES := {}
				LLENASQL(@mSQL, @aVALORES, aLISTA, mTRANSAC )
				EJECUTASQL(mSQL,aVALORES)
			ENDIF
		ENDIF
	ENDIF

   // DEBITO BANCARIO
   IF mTBANCO != 'CAJ' .AND. XTIPOP="1"
      IF DAMEVAL("SELECT dbporcen FROM banc WHERE codbanc='"+XCODBAN+"' ",,'N') > 0
         IF SINO("Cargar Debito Bancario?",1) = 1
            CARGAIDB(XCODBAN, XFECHA, XMONTO, XNUMCHE , XNUMERO, mTRANSAC)
         ENDIF
      ENDIF
   ENDIF

   IF XTIPOP $ "245"
      // GENERA SPRM UNA ND
      mNUMERO   := PROX_SQL("num_nd")
      aLISTA := {}
      AADD(aLISTA, {"COD_PRV",  XCLIPRO })
      AADD(aLISTA, {"NOMBRE",   XNOMBRE })   
      AADD(aLISTA, {"TIPO_DOC", "ND" })
      AADD(aLISTA, {"NUMERO",   mNUMERO })   
      AADD(aLISTA, {"FECHA",    XFECHA })    
      AADD(aLISTA, {"MONTO",    XMONTO })
      AADD(aLISTA, {"IMPUESTO", 0 })
      AADD(aLISTA, {"VENCE",    XVENCE })    
      AADD(aLISTA, {"TIPO_REF", "PR" })
      AADD(aLISTA, {"NUM_REF",  XNUMERO })
      AADD(aLISTA, {"OBSERVA1", XOBSERVA1 }) 
      AADD(aLISTA, {"OBSERVA2", XOBSERVA2 }) 
      AADD(aLISTA, {"BANCO",    XCODBAN })
      AADD(aLISTA, {"NUMCHE",   XNUMCHE })
      AADD(aLISTA, {"TIPO_OP",  XTIPO })
      AADD(aLISTA, {"BENEFI",   XBENEFI })

      mSQL := "INSERT INTO sprm SET "
      aVALORES := {}
      LLENASQL(@mSQL, @aVALORES, aLISTA, mTRANSAC )
      EJECUTASQL(mSQL,aVALORES)
   ENDIF


	IF XTIPOP$'136'
		mNUMERO   := PROX_SQL("ndcli")
		DO WHILE .T.
			mSQL := "SELECT count(*) FROM smov "
			mSQL += "WHERE tipo_doc='ND' AND numero='"+mNUMERO+"' "
			IF DAMEVAL(mSQL,,'N') = 0
				EXIT
			ENDIF
			mNUMERO   := PROX_SQL("ndcli")
		ENDDO

		aLISTA := {}
		AADD(aLISTA, {"COD_CLI",  XCLIPRO })
		AADD(aLISTA, {"NOMBRE",   XNOMBRE })
		AADD(aLISTA, {"TIPO_DOC", "ND" })
		AADD(aLISTA, {"NUMERO",   mNUMERO })
		AADD(aLISTA, {"FECHA",    XFECHA })
		AADD(aLISTA, {"MONTO",    XMONTO })
		AADD(aLISTA, {"IMPUESTO", 0 })
		AADD(aLISTA, {"VENCE",    XVENCE })
		AADD(aLISTA, {"TIPO_REF", "PR" })
		AADD(aLISTA, {"NUM_REF",  XNUMERO })
		AADD(aLISTA, {"OBSERVA1", XOBSERVA1 })
		AADD(aLISTA, {"OBSERVA2", XOBSERVA2 })
		AADD(aLISTA, {"BANCO",    XCODBAN })
		AADD(aLISTA, {"FECHA_OP", XFECHA })
		AADD(aLISTA, {"NUM_OP",   XNUMCHE })
		AADD(aLISTA, {"TIPO_OP",  XTIPO })

		mSQL := "INSERT INTO smov SET "
		aVALORES := {}
		LLENASQL(@mSQL, @aVALORES, aLISTA, mTRANSAC  )
		EJECUTASQL(mSQL, aVALORES)
	ENDIF

	// GUARDA EGRESO E INGRESO EN PRMO
	mSQL := "UPDATE prmo SET negreso=?, ningreso=? WHERE numero=? "
	EJECUTASQL(mSQL,{XNEGRESO, XNINGRESO, XNUMERO} )

	IF SINO("Imprimir Documento? ",1) = 1
		IMPPRMO()
	ENDIF


RETURN("")

*/











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
		});

		';
		return $script;

	}



	function instalar(){
		if (!$this->db->table_exists('prmo')) {
			$mSQL="CREATE TABLE `prmo` (
			  `tipop` char(1) DEFAULT NULL,
			  `numero` varchar(8) NOT NULL DEFAULT '',
			  `fecha` date DEFAULT NULL,
			  `codban` char(2) DEFAULT NULL,
			  `tipo` char(2) DEFAULT NULL,
			  `numche` varchar(12) DEFAULT NULL,
			  `benefi` varchar(30) DEFAULT NULL,
			  `comprob` varchar(6) DEFAULT NULL,
			  `clipro` varchar(5) DEFAULT NULL,
			  `nombre` varchar(30) DEFAULT NULL,
			  `docum` varchar(12) DEFAULT NULL,
			  `banco` varchar(10) DEFAULT NULL,
			  `monto` decimal(13,2) DEFAULT NULL,
			  `cuotas` int(2) DEFAULT NULL,
			  `vence` date DEFAULT NULL,
			  `observa1` varchar(50) DEFAULT NULL,
			  `observa2` varchar(50) DEFAULT NULL,
			  `transac` varchar(8) DEFAULT NULL,
			  `estampa` date DEFAULT NULL,
			  `hora` varchar(8) DEFAULT NULL,
			  `usuario` varchar(12) DEFAULT NULL,
			  `cadano` int(6) DEFAULT NULL,
			  `apartir` int(6) DEFAULT NULL,
			  `negreso` char(8) DEFAULT NULL,
			  `ningreso` char(8) DEFAULT NULL,
			  `retencion` char(14) DEFAULT NULL,
			  `factura` char(12) DEFAULT NULL,
			  `remision` date DEFAULT NULL,
			  PRIMARY KEY (`numero`),
			  KEY `transaccion` (`transac`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('prmo');
		//if(!in_array('<#campo#>',$campos)){ }
	}

	//*******************************************
	//
	// Guarda cheques devueltos en PRMO
	//
	function prmochdev(){
		$id = $this->uri->segment($this->uri->total_segments());
		
		$transac = $this->datasis->prox_sql("ntransa");
		//LOCAL aLISTA, mSQL, aVALORES, mREG, mMEG, mTBANCO

		$mSQL = "SELECT a.*, b.recibe codban FROM sfpa a JOIN bcaj b ON a.deposito=b.numero WHERE a.id=$id";
		$reg  = $this->datasis->damereg($mSQL);
		
		if ( $reg['tipo'] <> 'CH' ){
			echo "Cheque ya devuelto";
			return;
		}
		

		$XNEGRESO  = "        ";
		$XNINGRESO = "        ";
		$XTIPO     = "ND";
		$XVENCE    = date('Y/m/d');
		$XFECHA    = date('Y/m/d');
		$XCODBAN   = $reg['codban'];
		$mTBANCO   = $this->datasis->dameval("SELECT tbanco FROM banc WHERE codbanc='$XCODBAN'");
		$XNUMERO   = $this->datasis->prox_sql("nprmo");
		$XNOMBRE   = $this->datasis->dameval("SELECT nombre FROM scli WHERE cliente='".$reg['cod_cli']."'");

		// Guarda en PRMO
		$aLISTA["tipop"]    = "3";
		$aLISTA["numero"]   = $XNUMERO;
		$aLISTA["fecha"]    = $XFECHA;
		$aLISTA["codban"]   = $XCODBAN;
		$aLISTA["tipo"]     = "ND";
		$aLISTA["numche"]   =  $reg['num_ref'];
		$aLISTA["benefi"]   =  "";
		$aLISTA["comprob"]  =  "";
		$aLISTA["clipro"]   =  $reg['cod_cli'];
		$aLISTA["nombre"]   =  $XNOMBRE ;
		$aLISTA["docum"]    =  $reg['deposito'];
		$aLISTA["banco"]    =  $reg['banco'] ;
		$aLISTA["monto"]    =  $reg['monto'] ;
		$aLISTA["cuotas"]   =  1 ;
		$aLISTA["vence"]    = $XVENCE ;
		$aLISTA["observa1"] = "CHEQUE DEVUELTO" ;
		$aLISTA["observa2"] = "" ;
		$aLISTA["cadano"]   = 1 ;
		$aLISTA["apartir"]  = $XFECHA;

		$aLISTA["usuario"]  = $this->secu->usuario() ;
		$aLISTA["transac"]  = $transac ;
		$aLISTA["estampa"]  = date('Y/m/d') ;
		$aLISTA["hora"]     = date('H:i:s') ;
		
		//$aLISTA["negreso"]  =  XNEGRESO ;
		//$aLISTA["ningreso"] = XNINGRESO ;
   
		$this->db->insert('prmo', $aLISTA);

  		// GUARDA EN BANCO

		//ACTUSAL(XCODBAN, XFECHA, XMONTO*IF(XTIPO$'CH,ND',-1,1) )
		$mREG = $this->datasis->damereg("SELECT numcuent, banco, saldo, tbanco FROM banc WHERE codbanc='$XCODBAN'");

		$mCUENTA   = $mREG['numcuent'];
		$mBANCO    = $mREG['banco'];
		$mSALDO    = $mREG['saldo'];
		$mTBANCO   = $mREG['tbanco'];
		$XCOMPROB  = $this->datasis->prox_sql("ncomprob");

		$aLISTA = array();
		$aLISTA["codbanc"]  = $XCODBAN;
		$aLISTA["numcuent"] = $mCUENTA;
		$aLISTA["banco"]    = $mBANCO;
		$aLISTA["saldo"]    = $mSALDO;
		$aLISTA["fecha"]    = $XFECHA;
		$aLISTA["tipo_op"]  = 'ND';
		$aLISTA["numero"]   = $reg['num_ref'];
		$aLISTA["concepto"] = "CHEQUE DEVUELTO CLIENTE ".$XNUMERO;
		$aLISTA["clipro"]   = 'C';
		$aLISTA["liable"]   = 'S';
		$aLISTA["concep2"]  = "CHEQUE DEVUELTO CLIENTE ".$reg['cod_cli'];
		$aLISTA["concep3"]  = "";
		$aLISTA["monto"]    = $reg['monto'];
		$aLISTA["codcp"]    = $reg['cod_cli'];
		$aLISTA["nombre"]   = $XNOMBRE;
		//$aLISTA["benefi"]   = XBENEFI;
		$aLISTA["comprob"]  = $XCOMPROB;
		$aLISTA["posdata"]  = $XFECHA;
		//$aLISTA["negreso"]  = XNEGRESO;

		$aLISTA["usuario"]   = $this->secu->usuario() ;
		$aLISTA["transac"]   = $transac ;
		$aLISTA["estampa"]   = date('Y/m/d') ;
		$aLISTA["hora"]      = date('H:i:s') ;


		$this->db->insert('bmov', $aLISTA);


		$i = 0;
		while ( $i == 0 ){
			$mNUMERO = $this->datasis->prox_sql("ndcli");
			$mSQL    = "SELECT count(*) FROM smov WHERE tipo_doc='ND' AND numero='$mNUMERO' ";
			$i       = $this->datasis->dameval($mSQL);
		}

		$aLISTA = array();
		$aLISTA["COD_CLI"]  = $reg['cod_cli'];
		$aLISTA["NOMBRE"]   = $XNOMBRE ;
		$aLISTA["TIPO_DOC"] = "ND";
		$aLISTA["NUMERO"]   = $mNUMERO;
		$aLISTA["FECHA"]    = $XFECHA;
		$aLISTA["MONTO"]    = $reg['monto'];
		$aLISTA["IMPUESTO"] = 0;
		$aLISTA["VENCE"]    = $XVENCE;
		$aLISTA["TIPO_REF"] = "PR";
		$aLISTA["NUM_REF"]  = $XNUMERO;
		$aLISTA["OBSERVA1"] = "CHEQUE DEVUELTO CLIENTE ".$XNUMERO;
		$aLISTA["OBSERVA2"] = "CHEQUE DEVUELTO CLIENTE ".$reg['cod_cli'];
		$aLISTA["BANCO"]    = $XCODBAN;
		$aLISTA["FECHA_OP"] = $XFECHA;
		$aLISTA["NUM_OP"]   = $reg['num_ref'];
		$aLISTA["TIPO_OP"]  = 'ND';

		$aLISTA["usuario"]   = $this->secu->usuario() ;
		$aLISTA["transac"]   = $transac ;
		$aLISTA["estampa"]   = date('Y/m/d') ;
		$aLISTA["hora"]      = date('H:i:s') ;
		$this->db->insert('smov', $aLISTA);
		$this->db->simple_query("UPDATE sfpa SET tipo='CD' WHERE id=$id");
		echo "Cheque Devuelto ";
	}


}

/*
require_once(BASEPATH.'application/controllers/validaciones.php');

class Prmo extends validaciones {
	function prmo(){
		parent::Controller(); 
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(206,1);
		//I use THISFILE, instead __FILE__ to prevent some documented php-bugs with higlight_syntax()&__FILE__
		//define ("THISFILE",   APPPATH."controllers/finanzas/". $this->uri->segment(2).EXT);
	}
	function index(){
		redirect("finanzas/prmo/filteredgrid");
	}
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		
		$filter = new DataFilter("Filtro de Otros Movimientos de Caja y Bancos", "prmo");
		
		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause="where";
		$filter->fechad->db_name =$filter->fechah->db_name="fecha";
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator=">="; 
		$filter->fechah->operator="<=";
		
		$filter->codban = new dropdownField("Caja/Banco", "codban");
		$filter->codban->option("","");
		$filter->codban->options("SELECT codbanc, banco FROM bmov ORDER BY banco ");
		
		$filter->banco = new dropdownField("Tipo", "tipo");
		$filter->banco->option("","");
		$filter->banco->option("1","Prestamo Otorgado");		
		$filter->banco->option("2","Prestamo Recibido");
		$filter->banco->option("3","Cheque Devuelto Cliente");
		$filter->banco->option("4","Cheque Devuelto Proveedor");
		$filter->banco->option("5","Deposito por Analizar");
		$filter->banco->option("6","Cargos Indevidos por el Banco");
		$filter->banco->option("7","Todos");
		
		$filter->clipro = new inputField("Cli/Prv", "monto");
		$filter->clipro->size=12;
		
		$filter->monto = new inputField("Monto", "monto");
		$filter->monto->size=12;
		
		$filter->buttons("reset","search");
		$filter->build();
		
		$uri = anchor('finanzas/prmo/dataedit/show/<#numero#>','<#numero#>');
		
		$grid = new DataGrid("Lista de Otros Movimientos de Caja y Bancos");
		$grid->order_by("numero","asc");
		$grid->per_page = 10;
		
		$grid->column("Numero",$uri);
		$grid->column("Fecha","<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Banco","banco");
		$grid->column("Cli/Prv","clipro");
		$grid->column("Monto","monto","align='right'");
		
		$grid->add("finanzas/prmo/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Otros Movimientos de Caja y Bancos</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
*/
?>
