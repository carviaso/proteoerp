<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
class Scli extends validaciones {
	var $genesal = true;
	var $mModulo = 'SCLI';
	var $titp    = 'Clientes';
	var $tits    = 'Clientes';
	var $url     = 'ventas/scli/';

	function Scli(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->load->library('pi18n');
		$this->datasis->modulo_nombre( 'SCLI', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 1000, 650, 'ventas/scli' );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	//Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		$funciones = $this->funciones($param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'edocta',  'img'=>'images/pdf_logo.gif', 'alt' => 'Formato PDF',        'label'=>'Estado de Cuenta' ));
		$grid->wbotonadd(array('id'=>'editacr', 'img'=>'images/check.png',    'alt' => 'Cr&eacute;dito',     'label'=>'L&iacute;mite de Cr&eacute;dito'));
		$grid->wbotonadd(array('id'=>'gciud',   'img'=>'images/star.png',     'alt' => 'Gestionar ciudades', 'label'=>'Ciudades'));
		$grid->wbotonadd(array('id'=>'gclave',  'img'=>'images/candado.png',  'alt' => 'Clave para acceso',  'label'=>'Clave'));

		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita' , 'title'=>'Agregar/Editar Cliente'  ),
			array('id'=>'feditcr', 'title'=>'Cambia Limite de Credito'),
			array('id'=>'fshow'  , 'title'=>'Mostrar Registro'        ),
			array('id'=>'fborra' , 'title'=>'Eliminar Registro'       ),
			array('id'=>'fciud'  , 'title'=>'Gestionar ciudades'      )
		);

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);
		//$SouthPanel .= '<a href="'.site_url($this->url.'getdata').'" class="ayuda1">Ayuda</a>';

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['funciones']   = $funciones;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('SCLI', 'JQ');
		$param['otros']       = $this->datasis->otros('SCLI', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['postready']   = $this->postready();
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	// Despues del document ready
	//
	//******************************************************************
	function postready(){

		$consulrif=trim($this->datasis->traevalor('CONSULRIF'));

		// Busca la cedula en el CNE
		$postready = '
		function consulcne(campo){
			vrif=$("#"+campo).val();
			naci="V";
			if(vrif.length==0){
				alert("Debe introducir primero una Cedua de Identidad");
			}else{
				vrif=vrif.toUpperCase();
				$("#riffis").val(vrif);
				window.open("http://www.cne.gov.ve/web/registro_electoral/ce.php?nacionalidad="+vrif.substr(0,1)+"&cedula="+vrif.substr(1),"CONSULCNE","height=400,width=510");
			}
		};';

		// Buscar en el SENIAT
		$postready .= '
		function consulrif(campo){
			vrif=$("#"+campo).val();
			if(vrif.length==0){
				alert("Debe introducir primero un RIF");
			}else{
				vrif=vrif.toUpperCase();
				$("#riffis").val(vrif);
				window.open("'.$consulrif.'"+"?p_rif="+vrif,"CONSULRIF","height=350,width=410");
			}
		};';

		return $postready;
	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= '
		jQuery("#edocta").click( function(){
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("'.$ngrid.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('reportes/ver/SMOVECU/SCLI/').'/\'+ret.cliente').';
			} else { $.prompt("<h1>Por favor Seleccione un Cliente</h1>");}
		});
		';

		// Creditos
		$bodyscript .= '
		jQuery("#editacr").click( function(){
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url('ventas/scli/creditoedit/modify').'/"+id, function(data){
					$("#fedita").html("");
					$("#feditcr").html(data);
					$("#feditcr").dialog({height: 400, width: 650});
					$("#feditcr").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		});';

		// Clave de Acceso
		$bodyscript .= '
		jQuery("#gclave").click( function(){
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url('ventas/scli/claveedit/modify').'/"+id, function(data){
					$("#fedita").html("");
					$("#feditcr").html(data);
					$("#feditcr").dialog({height: 250, width: 400});
					$("#feditcr").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		});';



		$bodyscript .= '
		function scliadd() {
			$.post("'.site_url('ventas/scli/dataedit/create').'",
			function(data){
				$("#feditcr").html("");
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function scliedit() {
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url('ventas/scli/dataedit/modify').'/"+id, function(data){
					$("#feditcr").html("");
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		$bodyscript .= '
		function sclidel() {
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("'.$ngrid.'").getRowData(id);
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

		$bodyscript .= '
		function sclishow(){
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$botones ='
				"C.N.E.":   function() { consulcne("rifci"); },';

		// Marcas
		$bodyscript .= '
		$("#gciud").click(function(){
			$.post("'.site_url($this->url.'marcaform').'",
			function(data){
				$("#fciud").html(data);
				$("#fciud").dialog( "open" );
			});
		});';

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, $height = "470", $width = "800",'','',$botones );


/*
		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 550, width: 800, modal: true,
			buttons: {
				"Guardar": function() {
					var bValid = true;
					var murl = $("#df1").attr("action");
					var id  = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
					allFields.removeClass( "ui-state-error" );
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url: murl,
						data: $("#df1").serialize(),
						success: function(r,s,x){
							if ( r.length == 0 ) {
								$( "#fedita" ).dialog("close");
								grid.trigger("reloadGrid");
								$.prompt("<h1>Registro Guardado</h1>",{
									submit: function(e,v,m,f){
										setTimeout(function(){ $("'.$ngrid.'").jqGrid(\'setSelection\',id);}, 500);
									}
								});
								return true;
							} else {
								$("#fedita").html(r);
							}
						}

					}
				)},
				"SENIAT":   function() { consulrif("rifci"); },
				"C.N.E.":   function() { consulcne("rifci"); }
				"Cancelar": function() { $(this).dialog("close"); },
			},
			close: function(){
				allFields.val("").removeClass("ui-state-error");
				$("#fedita").html("");
			}
		});';
*/

		$bodyscript .= '
		$("#feditcr").dialog({
			autoOpen: false, height: 400, width: 650, modal: true,
			buttons: {
				"Guardar": function() {
					var bValid = true;
					var murl = $("#df1").attr("action");
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url: murl,
						data: $("#df1").serialize(),
						success: function(r,s,x){
							if(r.length == 0){
								$.prompt("Cambio Guardado");
								$( "#feditcr" ).dialog( "close" );
								grid.trigger("reloadGrid");
								return true;
							}else{
								$("#feditcr").html(r);
							}
						}
				})},
				"Cancelar": function(){
					$("#feditcr").html("");
					$(this).dialog("close");
				}
			},
			close: function(){
				$("#feditcr").html("");
			}
		});';

		$bodyscript .= '
		$("#fciud").dialog({
			autoOpen: false, height: 400, width: 320, modal: true,
			close: function() {
				$("#fshow").html("");
			}
		});';

		$bodyscript .= $this->jqdatagrid->bsfshow( $height = '500', $width = '700' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '300' );



		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}



	//****************************************
	//
	// funciones
	//
	function funciones($grid){

		$forma = "No tiene Acceso a Modificar Credito";
		if ( $this->datasis->puede_ejecuta('SCLILIMITE', 'SCLI') ) {
			if ( $this->datasis->puede_ejecuta('SCLITOLERA', 'SCLI') ) {
				if ( $this->datasis->puede_ejecuta('SCLIMAXTOLE', 'SCLI')) {
					$forma .= "<table align='center' width='95%'>";
					$forma .= "<tr><td>Tiene Credito:</td><td> <select name='credito' id='credito' title='Asignar o suspender Credito' value='\"+ret.credito+\"'><option value='S'>Activo</option><option value='N'>Suspender</option></select></td></tr>";
					$forma .= "<tr><td>Dias de Credito: </td><td><input class='inputnum' type='text' id='formap' name='formap' value='\"+ret.formap+\"' size='3' style='text-align:right;'></td></tr>";
					$forma .= "<tr><td>Monto Limite: </td><td><input class='inputnum' type='text' id='limite' name='limite' value='\"+ret.limite+\"' size='7' style='text-align:right;'></td></tr>";
					$forma .= "<tr><td>Margen de Tolerancia:</td><td><input class='inputnum' type='text' id='tolera' name='tolera' value='\"+ret.tolera+\"' size='7' style='text-align:right;'>%</td></tr>";
					$forma .= "<tr><td>Maxima Tolerancia:</td><td><input class='inputnum' type='text' id='maxtole' name='maxtole' value='\"+ret.maxtole+\"' size='7' style='text-align:right;'>%</td></tr>";
				} else {
					$forma .= "<table align=\'center\' width=\'95%\'>";
					$forma .= "<tr><td>Tiene Credito:</td><td> <select name=\'credito\' id=\'credito\' title=\'Asignar o suspender Credito\' value=\'\"+ret.credito+\"\'><option value=\'S\'>Activo</option><option value=\'N\'>Suspender</option></select></td></tr>";
					$forma .= "<tr><td>D&iacute;as de Cr&eacute;dito: </td><td><input class=\'inputnum\' type=\'text\' id=\'formap\' name=\'formap\' value=\'\"+ret.formap+\"\' size=\'3\' style=\'text-align:right;\'></td></tr>";
					$forma .= "<tr><td>Monto Limite: </td><td><span style=\'text-align:right;\'>\"+ret.limite+\"</span></td></tr>";
					$forma .= "<tr><td>Margen de Tolerancia:</td><td><input class=\'inputnum\' type=\'text\' id=\'tolera\' name=\'tolera\' value=\'\"+ret.tolera+\"\' size=\'7\' style=\'text-align:right;\'>%</td></tr>";
					$forma .= "<tr><td>Maxima Tolerancia:</td><td><span style=\'text-align:right;font-size:130%;\'>\"+ret.maxtole+\"%</td></tr>";
				}
			} else {
				$forma .= "<table align=\'center\' width=\'95%\'>";
				$forma .= "<tr><td width=\'40%\'>Tiene Credito:</td><td>\"+mcredito+\"</td></tr>";
				$forma .= "<tr><td>Dias de Credito:</td><td><input class=\'inputnum\' type=\'text\' id=\'formap\' name=\'formap\' value=\'\"+ret.formap+\"\' size=\'3\' style=\'text-align:right;\'></td></tr>";
				$forma .= "<tr><td>Monto Limite:</td><td><span  style=\'text-align:right;font-size:130%;\'>\"+ret.limite+\"</td></tr>";
				$forma .= "<tr><td>Margen de Tolerancia:</td><td><span style=\'text-align:right;font-size:130%;\'>\"+ret.tolera+\"%</td></tr>";
				$forma .= "<tr><td>Maxima Tolerancia:</td><td><span style=\'text-align:right;font-size:130%;\'>\"+ret.maxtole+\"%</td></tr>";
			}
			$forma .= "<tr><td colspan=\'2\'>Observaciones: </td></tr><tr><td colspan=\'2\'><textarea id=\'observa\' name=\'observa\' rows=\'3\' cols=\'50\' ></textarea></td></tr>";
			$forma .= "</table>";
		}

		// Busca el RIF en el SENIAT
		$funciones = '
		$("#tiva").change(function () { anomfis(); }).change();
		$("#maintabcontainer").tabs();
		';

		// Valida RIF o Cedula
		$funciones .= '
		function chrif(rif){
			rif.toUpperCase();
			var patt=/((^[VEJG][0-9])|(^[P][A-Z0-9]))/;
			if(patt.test(rif)){
				return true;
			}else{
				return false;
			}
		};';

		// Valida RIF o CI con mensaje
		$funciones .= '
		function rchrifci(value, colname) {
			value.toUpperCase();
			var patt=/((^[VEJG][0-9])|(^[P][A-Z0-9]))/;
			if( !patt.test(value) )
				return [false,"El Rif colocado no es correcto, por favor verifique con el SENIAT."];
			else
				return [true,""];
		};';

		// Fusionar Cliente
		$funciones .= '
		function fusionar(){
			var yurl = "";
			var id = jQuery("#newapi'.$grid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var mnuevo = "";
				var ret = jQuery("#newapi'.$grid.'").jqGrid(\'getRowData\',id);
				var mviejo = ret.cliente;
				$.prompt("<h1>Cambiar Codigo</h1>Cliente: <b>"+ret.nombre+"</b><br>Codigo Actual: <b>"+ret.cliente+"</b><br><br>Codigo Nuevo <input type=\'text\' id=\'codnuevo\' name=\'mcodigo\' size=\'6\' maxlength=\'5\' >",{
					buttons: { Cambiar:true, Salir:false},
					callback: function(e,v,m,f){
						mnuevo = f.mcodigo;
						if (v) {
							yurl = encodeURIComponent(mnuevo);
							$.ajax({
								url: "'.site_url('ventas/scli/scliexiste').'",
								global: false,
								type: "POST",
								data: ({ codigo : encodeURIComponent(mnuevo) }),
								dataType: "text",
								async: false,
								success: function(sino) {
									sclicambia(sino, mviejo, mnuevo, ret.nombre);
								},
								error: function(h,t,e) { apprise("Error..codigo="+yurl+" ",e) }
							});
						}
					}
				});
			} else
				$.prompt("<h1>Por favor Seleccione un Cliente</h1>");
		};

		function sclicambia( sino, mviejo, mnuevo, nviejo ) {
			//$.prompt(sino+" "+mviejo+" "+mnuevo);
			var aprueba = false;
			if (sino.substring(0,1)=="S"){
				apprise("<h1>FUSIONAR: Ya existe el cliente</h1><h2 style=\"background: #ffdddd;text-align:center;\">("+mnuevo+") "+sino.substring(1)+"</h2><p style=\"font-size:130%\">Si prosigue se eliminara el cliente ("+mviejo+") "+nviejo+"<br>y los movimientos seran agregados a ("+mnuevo+") </"+"p> <p style=\"align:center;font-size:150%\">Desea <strong>Fusionarlos?</"+"strong></"+"p>",
					{ "confirm":true, "textCancel":"Salir", "textOk":"Proseguir"},
					function(v){
						if (v) {
							sclifusdef(mnuevo, mviejo)
							jQuery(gridId1).trigger("reloadGrid");
						}
					}
				);
			} else {
				apprise("<h1>Sustitur Codigo actual</h1> <center><h2 style=\"background: #ddeedd\">"+mviejo+" por "+mnuevo+"</"+"h2></"+"center> <p style=\"font-size:130%\">Al cambiar de codigo del cliente, todos los movimientos y estadisticas <br>se cambiaran correspondientemente.</"+"p> ",
					{ "confirm":true, "textCancel":"Salir", "textOk":"Proseguir"},
					function(v){
						if (v) {
							sclifusdef(mnuevo, mviejo);
							jQuery(gridId1).trigger("reloadGrid");
						}
					}
				)
			}
		};

		function sclifusdef(mnuevo, mviejo){
			$.ajax({
				url: "'.site_url('ventas/scli/sclifusion').'",
				global: false,
				type: "POST",
				data: ({mviejo: encodeURIComponent(mviejo),
					mnuevo: encodeURIComponent(mnuevo) }),
				dataType: "text",
				async: false,
				success: function(sino) {
					alert("Cambio finalizado "+sino,"Finalizado Exitosamente")
				},
				error: function(h,t,e) {alert("Error..","Finalizado con Error" )}
			});
		};
		';

		// Memo del cliente
		$funciones .= '
		function sclimemo(){
			var id = jQuery("#newapi'.$grid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var mmensaje = "";
				var ret = jQuery("#newapi'.$grid.'").jqGrid(\'getRowData\',id);
				mmensaje = ret.mensaje;
				$.prompt("<h1>Observaciones:</h1>Cliente: <b>"+ret.nombre+"</b><br><textarea id=\'mensaje\' name=\'mensaje\' cols=\'50\' rows=\'5\' >"+ret.observa+"</textarea>",{
					buttons: { Guardar:true, Salir:false},
					callback: function(e,v,m,f){
						if (v) {
							$.ajax({
								url: "'.site_url('ventas/scli/sclimemo').'",
								global: false,
								type: "POST",
								data: ({ mensaje : encodeURIComponent(f.mensaje), mid:id }),
								dataType: "text",
								async: false,
								success: function(sino) {
									apprise(sino);
									jQuery(gridId1).trigger("reloadGrid");
								},
								error: function(h,t,e) { apprise("Error....."+e) }
							});
						}
					}
				});
			} else
				$.prompt("<h1>Por favor Seleccione un Cliente</h1>");
		}';

		// Limite de Credito
		$funciones .= '
		function sclilimite(){
			var id = jQuery("#newapi'.$grid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid.'").jqGrid(\'getRowData\',id);
				var mcredito;
				mcredito = ( ret.credito == "S" ) ? "Activo":"Suspendido";
				$.prompt("<h1>Limite de Credito</h1>'.$forma.'",{
					buttons: { Guardar:true, Salir:false},
					callback: function(e,v,m,f){
						var data  = "";
						var forma = "";
						if (v) {
							if (f.credito != "undefined") data = data+"&credito="+f.credito;
							if (f.formap  != "undefined") data = data+"&formap="+ f.formap;
							if (f.limite  != "undefined") data = data+"&limite="+ f.limite;
							if (f.tolera  != "undefined") data = data+"&tolera="+ f.tolera;
							if (f.maxtole != "undefined") data = data+"&maxtole="+f.maxtole;
							if (f.observa != "undefined") data = data+"&observa="+encodeURIComponent(f.observa);
							data = data+"&mid="+id;
							$.ajax({
								url: "'.site_url('ventas/scli/sclilimite').'",
								global: false,
								type: "POST",
								data: data,
								dataType: "text",
								async: false,
								success: function(sino) {
									apprise(sino);
									jQuery(gridId1).trigger("reloadGrid");
								},
								error: function(h,t,e) { apprise("Error....."+e) }
							});
						}
					}
				});
			} else
				$.prompt("<h1>Por favor Seleccione un Cliente</h1>");
		}';

		return $funciones;

	}


	//***********************************
	//
	//  Definicion del Grid y la Forma
	//
	//***********************************
	function defgrid( $deployed = false ){
		$i       = 1;
		$editar  = 'false';
		$linea   = 1;

		$link   = site_url('ajax/buscacpla');

		$mSQL = "SELECT grupo, CONCAT(grupo, ' ', gr_desc) banco FROM grcl ORDER BY grupo ";
		$agrupo  = $this->datasis->llenajqselect($mSQL, false );

		$mSQL = "SELECT codigo, CONCAT(codigo, ' ', nombre) nombre FROM zona ORDER BY codigo ";
		$azona  = $this->datasis->llenajqselect($mSQL, false );

		$mSQL = "SELECT TRIM(ciudad) ciudad, TRIM(ciudad) nombre FROM ciud ORDER BY ciudad ";
		$aciudad  = $this->datasis->llenajqselect($mSQL, false );

		$mSQL = "SELECT vendedor, concat( vendedor, ' ',TRIM(nombre)) nombre FROM vend ORDER BY nombre ";
		$avende  = $this->datasis->llenajqselect($mSQL, true );


		$grid  = new $this->jqdatagrid;

		$grid->addField('cliente');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:6, maxlength: 5 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$agrupo.',  style:"width:250px"}',
			'stype'         => "'text'",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('rifci');
		$grid->label('RIF/C.I.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ custom:true, custom_func: rchrifci }',
			'editoptions'   => '{ size:13, maxlength: 13 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"RIF o C.I." }'
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 45 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('nomfis');
		$grid->label('Raz&oacute;n Social');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 80 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: {"1":"Precio 1","2":"Precio 2","3":"Precio 3","4":"Precio 4", "5":"Mayor 5", "0":"Inactivo 0" }, style:"width:150px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$linea = $linea + 1;
		$grid->addField('contacto');
		$grid->label('Contacto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			//'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('tiva');
		$grid->label('Condici&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: {"C":"Contribuyente","E":"Especial","N":"No Contribuyente","R":"R. Exento", "O":"Otro"}, style:"width:150px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$linea = $linea + 1;
		$grid->addField('cuenta');
		$grid->label('Cta.Contable');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			//'editoptions'   => '{'.$grid->autocomplete($link, 'cuenta','cucucu','<div id=\"cucucu\"><b>"+ui.item.descrip+"</b></div>').'}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('canticipo');
		$grid->label('Cta.Anticipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			//'editoptions'   => '{'.$grid->autocomplete($link, 'canticipo','cacaca','<div id=\"cacaca\"><b>"+ui.item.descrip+"</b></div>').'}',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));


		$grid->addField('dire11');
		$grid->label('Direcci&oacute;n 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('credito');
		$grid->label('Cr&eacute;dito');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"S":"Activo","N":"Suspendido" }, style:"width:100px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$linea = $linea + 1;
		$grid->addField('dire12');
		$grid->label('Direcci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:40, maxlength: 40 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('mmargen');
		$grid->label('% Mayor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{ decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1, label:"Desc. Mayor %" }'
		));

		$grid->addField('formap');
		$grid->label('Dias CR');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 40,
			'editoptions'   => '{ size:5, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$linea = $linea + 1;
		$grid->addField('ciudad1');
		$grid->label('Ciudad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: '.$aciudad.', style:"width:300px" }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('zona');
		$grid->label('Zona');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'select'",
			'editrules'     => '{ edithidden:true, required:true }',
			'editoptions'   => '{ value: '.$azona.',  style:"width:220px"}',
			'stype'         => "'text'",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$linea = $linea + 1;
		$grid->addField('vendedor');
		$grid->label('Vendedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'select'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ value: '.$avende.',  style:"width:220px"}',
			'stype'         => "'text'",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('telefono');
		$grid->label('Tel&eacute;fono 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			//'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('telefon2');
		$grid->label('Tel&eacute;fono 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:30, maxlength: 25 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('porvend');
		$grid->label('Comisi&oacute;n V %');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));


		$grid->addField('socio');
		$grid->label('Socio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$linea = $linea + 1;
		$grid->addField('pais');
		$grid->label('Pa&iacute;s');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 180,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 18 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('cobrador');
		$grid->label('Cobrador');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'select'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ value: '.$avende.',  style:"width:220px"}',
			'stype'         => "'text'",
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('limite');
		$grid->label('L&iacute;mite');
		$grid->params(array(
			'hidden'        => 'true',
			'align'         => "'right'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$linea = $linea + 1;
		$grid->addField('repre');
		$grid->label('Representante');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('cirepre');
		$grid->label('Rep.C.I.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:10, maxlength: 13 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$linea = $linea + 1;
		$grid->addField('email');
		$grid->label('Email');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 180,
			'edittype'      => "'text'",
			//'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 18 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('porcobr');
		$grid->label('Comisi&oacute;n C %');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 40,
			'editrules'     => '{ required:false }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$linea = $linea + 1;
		$grid->addField('url');
		$grid->label('URL');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 180,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 18 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('fb');
		$grid->label('facebook');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 180,
			'edittype'      => "'text'",
			//'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 18 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$linea = $linea + 1;
		$grid->addField('pin');
		$grid->label('PIN');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 180,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:30, maxlength: 18 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:1 }'
		));

		$grid->addField('twitter');
		$grid->label('twitter');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 180,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:30, maxlength: 18 }',
			'formoptions'   => '{ rowpos:'.$linea.', colpos:2 }'
		));

		$grid->addField('fecha1');
		$grid->label('Fecha 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'hidden'        => 'true',
			'edittype'      => "'text'",
			'editrules'     => '{ required:false,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('mensaje');
		$grid->label('Mensaje');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));

		$grid->addField('observa');
		$grid->label('Observaci&oacute;n');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "{rows:2, cols:60}",
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

/*
		$grid->addField('modifi');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));
*/

		$grid->addField('sucursal');
		$grid->label('Sucursal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));


		$grid->addField('tolera');
		$grid->label('Toleracia');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('maxtole');
		$grid->label('Tolerancia Max.');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => 'false',
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
		$grid->setHeight('385');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
		function(id){
			if (id){
				var ret = jQuery(gridId1).jqGrid(\'getRowData\',id);
				$(gridId1).jqGrid("setCaption", ret.nombre+" U. Venta "+ret.fecha1);
				$.ajax({
					url: "'.base_url().$this->url.'resumen/"+id,
					success: function(msg){
						msg += "<center><img src=\''.site_url($this->url.'vcard').'/'.'"+id+"\' alt=\'vCard\' height=\'160\' width=\'160\'></center>";
						$("#ladicional").html(msg);
					}
				});
			}
		},
		afterInsertRow: function( rid, aData, rowe){
			if ( aData.tipo == "0" ){
				$(this).jqGrid( "setCell", rid, "cliente","", {color:"#FFFFFF", \'background-color\':"#AF1001" });
				$(this).jqGrid( "setCell", rid, "nombre", "", {color:"#FFFFFF", \'background-color\':"#AF1001" });
			}
		}'
		);

		$grid->setAfterSubmit('$.prompt(\'Respuesta:\'+a.responseText); return [true, a ];');

		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('SCLI','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('SCLI','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('SCLI','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('SCLI','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: scliadd, editfunc: scliedit, delfunc: sclidel, viewfunc: sclishow');


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
		$mWHERE = $grid->geneTopWhere('scli');

		$response   = $grid->getData('scli', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		$oper   = $this->input->post('oper');
		$id     = intval($this->input->post('id'));
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			echo 'Deshabilitado';
		}elseif($oper == 'edit'){

			$posibles=array('clave');
			foreach($data as $ind=>$val){
				if(!in_array($ind,$posibles)){
					echo 'Campo no permitido ('.$ind.')';
					return false;
				}
			}

			$this->db->where('id', $id);
			$this->db->update('scli', $data);
			logusu('SCLI',"Cliente id:${id} MODIFICADO");
			echo "Cliente Modificado";

		} elseif($oper == 'del') {
			echo 'Deshabilitado';
		}
	}


	//**************************************************
	//
	//  SUGIERE UN CODIGO DE CLIENTE
	//
	//**************************************************
	function proxcli( $mrifci='' ){
		$mcliente = '';
		$mvalor   = 0;
		$mmeco    = 0;
		$mrango   = $this->datasis->traevalor('SCLIRANGO');
		$mpiso    = '00000';
		$mtecho   = 'ZZZZZ';

		if($mrango == 'S'){
			$mcliente = str_pad($this->_numatri($this->datasis->prox_sql('ncodcli')),5,'0',STR_PAD_LEFT);
		}else{
			// GENERA POR CONVERSION DE CI
			if ( $mrifci != ''){
				$mmeco    = substr($mrifci,2,15);
				$mcliente = str_pad($this->_numatri($mmeco), 5, '0', STR_PAD_LEFT );
			}else
				$mcliente = str_pad($this->_numatri($this->datasis->prox_sql('ncodcli')),5,'0', STR_PAD_LEFT);
		}
		// REVISA POR SI ESTA REPETIDO
		while(true){
			if ($this->datasis->dameval("SELECT COUNT(*) FROM scli WHERE cliente=".$this->db->escape($mcliente)) == 0 )
				break;
			$mcliente = str_pad($this->_numatri($this->datasis->prox_sql('ncodcli')),5,'0',STR_PAD_LEFT);
		}
		return $mcliente;
	}


	//************************************************
	//
	//  PARA GENERAR CODIGOS
	//
	//************************************************
	function _numatri(){
		$numero = $this->datasis->prox_numero('ncodcli');
		$residuo= $numero;
		$mbase  = 36;
		$conve='';
		$mtempo  = $residuo % $mbase;
		while($residuo > $mbase-1){
			$residuo = intval($residuo/$mbase);
			if($mtempo >9 ){
				$conve .= chr($mtempo+55);
			}else{
				$conve .= $mtempo;
			}
			$mtempo  = $residuo % $mbase;
		}
		if($mtempo >9 ){
			$conve .= chr($mtempo+55);
		}else{
			$conve .= $mtempo;
		}
		return str_pad($conve, 5, '0', STR_PAD_LEFT);
	}

	//Resumen rapido
	function resumen() {
		$id = $this->uri->segment($this->uri->total_segments());

		$row = $this->datasis->damereg("SELECT cliente, credito, formap, limite, tolera, maxtole, observa, tipo FROM scli WHERE id=$id");

		$cod_cli  = $row['cliente'];
		$credito  = $row['credito'];
		$formap   = $row['formap'];
		$limite   = $row['limite'];
		$tolera   = $row['tolera'];
		$maxtole  = $row['maxtole'];
		$observa  = $row['observa'];
		$tipo     = $row['tipo'];

		if( $credito == 'S')
			$mcredito = 'Activo';
		else
			$mcredito = 'Suspendido';

		$saldo  = 0;
		$saldo  = $this->datasis->dameval("SELECT SUM(monto*IF(tipo_doc IN ('FC','ND','GI'),1,-1)) saldo FROM smov WHERE cod_cli=".$this->db->escape($cod_cli));

		$salida = '';

		$salida  .= '<table width="100%" cellspacing="0">';
		if ( $tipo == '0' )
			$salida .= '<tr style="background-color:#AF1001; color:#FFFFFF; font-size:14px;font-weight:bold;"><td colspan="2" align="center">CLIENTE INACTIVO</td></tr>'."\n";

		if ($tipo == 'S')
			$salida .= "<tr style='background-color:#AAEEAA;'><td colspan='2' align='center'><b>Credito $mcredito</b></td></tr>\n";
		else
			$salida .= "<tr style='background-color:#CCCCBB;'><td colspan='2' align='center'><b>Credito $mcredito</b></td></tr>\n";

		$salida .= "<tr style='background-color:#FFFFFF;'><td>Limite            </td><td align='right'>".nformat($limite)."  </td></tr>\n";
		$salida .= "<tr style='background-color:#EEEEEE;'><td>Tolerancia        </td><td align='right'>$tolera  </td></tr>\n";
		$salida .= "<tr style='background-color:#FFFFFF;'><td>Maxima Tolerancia </td><td align='right'>$maxtole </td></tr>\n";
		$salida .= "<tr style='background-color:#EEEEEE;'><td>Saldo Actual      </td><td align='right'>".nformat($saldo)."   </td></tr>\n";
		$salida .= "<tr style='background-color:#FBEC88;'><td>Credito Disponible</td><td align='right'><b>".nformat($limite-$saldo)."</b></td></tr>\n";
		$salida .= "</table>\n";

		if ( !empty($observa) )
			$salida .= "<br><b>Observaciones:</b><textarea cols='28' rows='4' readonly='readonly'>$observa</textarea>\n";

		echo $salida;
	}

	// **************************************
	//     DATAEDIT
	//
	// **************************************
	function dataedit(){
		$this->pi18n->cargar('scli','dataedit');
		$this->rapyd->load('dataedit');

		$mSCLId=array(
			'tabla'    => 'scli',
			'columnas' => array(
				'cliente'  => 'C&oacute;digo Socio',
				'nombre'   => 'Nombre',
				'cirepre'  => 'Rif/Cedula',
				'dire11'   => 'Direcci&oacute;n'),
			'filtro'   => array('cliente'=>'C&oacute;digo Socio','nombre'=>'Nombre'),
			'retornar' => array('cliente'=>'socio'),
			'titulo'   => 'Buscar Socio');

		$qformato=$this->datasis->formato_cpla();

		$mCPLA=array(
			'tabla'    => 'cpla',
			'columnas' => array(
				'codigo'   => 'C&oacute;digo',
				'descrip'  => 'Descripci&oacute;n'),
			'filtro'   => array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar' => array('codigo'=>'cuenta'),
			'titulo'   => 'Buscar Cuenta',
			'where'    => "codigo LIKE \"$qformato\"");

		$mANTI=array(
			'tabla'    => 'cpla',
			'columnas' => array(
				'codigo'   => 'C&oacute;digo',
				'descrip'  => 'Descripci&oacute;n'),
			'filtro'   => array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar' => array('codigo'=>'canticipo'),
			'titulo'   => 'Buscar Cuenta',
			'where'    => "codigo LIKE \"$qformato\"");


		$mTARIFA=array(
			'tabla'     => 'tarifa',
			'columnas'  => array(
				'id'        => 'Codigo',
				'actividad' => 'Actividad'),
			'filtro'   => array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar' => array('tarifa'=>'id'),
			'titulo'   => 'Buscar Tarifa');

		$boton = $this->datasis->modbus($mSCLId);
		$bcpla = $this->datasis->modbus($mCPLA);
		$banti = $this->datasis->modbus($mANTI,'canticipo');

		$consulrif     = trim($this->datasis->traevalor('CONSULRIF'));
		$lcuenta       = site_url('contabilidad/cpla/autocomplete/codigo');
		$lsocio        = site_url('ventas/scli/autocomplete/cliente');

		$link20=site_url('ventas/scli/scliexiste');
		$link21=site_url('ventas/scli/sclicodigo');

		$script ='
		<script type="text/javascript" >
		$(function() {
			$("#aniversario").datepicker({ dateFormat: "dd/mm/yy" });
			//Default Action
			$("#tiva").change(function () { anomfis(); }).change();

			$("#tarifa").autocomplete({
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscastarifa').'",
						type: "POST",
						dataType: "json",
						data: "q="+req.term,
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#tarifa").val("");
									$("#tactividad").val("");
									$("#tactividad_val").text("");
									$("#tminimo").val("");
									$("#tminimo_val").text("");
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
					$("#tarifa").attr("readonly", "readonly");

					$("#tarifa").val(ui.item.value);
					$("#tactividad").val(ui.item.actividad);
					$("#tactividad_val").text(ui.item.actividad);
					$("#tminimo").val(ui.item.minimo);
					$("#tminimo_val").text(ui.item.minimo);
					setTimeout(function() {  $("#tarifa").removeAttr("readonly"); }, 1500);
				}
			});

			$("#maintabcontainer").tabs();

			$("#rifci").focusout(function(){
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
								if($("#nomfis").val()==""){
									$("#nomfis").val(data.nombre);
								}
							}
						}
					});
				}
			});
		});

		function formato(row) {
			return row[0] + "-" + row[1];
		}

		function anomfis(){
			vtiva=$("#tiva").val();
			if(vtiva=="C" || vtiva=="E" || vtiva=="R"){
				$("#tr_nomfis").show();
				$("#tr_riffis").show();
			}else{
				$("#nomfis").val("");
				$("#riffis").val("");
				$("#tr_nomfis").hide();
				$("#tr_riffis").hide();
			}
		}

		function chrif(rif){
			rif.toUpperCase();
			var patt=/[EJPGV][0-9]{9} */g;
			if(patt.test(rif)){
				var factor= new Array(4,3,2,7,6,5,4,3,2);
				var v=0;
				if(rif[0]=="V"){
					v=1;
				}else if(rif[0]=="E"){
					v=2;
				}else if(rif[0]=="J"){
					v=3;
				}else if(rif[0]=="P"){
					v=4;
				}else if(rif[0]=="G"){
					v=5;
				}
				acum=v*factor[0];
				for(i=1;i<9;i++){
					acum=acum+parseInt(rif[i])*factor[i];
				}
				acum=11-acum%11;
				if(acum>=10 || acum<=0){
					acum=0;
				}
				return (acum==parseInt(rif[9]));
			}else{
				return true;
			}
		}
		</script>';

		$do = new DataObject('scli');
		$do->pointer('tarifa' ,'tarifa.id =scli.tarifa' ,'`tarifa`.`actividad`  AS tactividad, `tarifa`.`minimo`  AS tminimo'  ,'left');

		$edit = new DataEdit('Clientes', $do);
		$edit->on_save_redirect=false;
		$edit->pre_process('delete','_pre_del');
		$edit->pre_process('insert','_pre_ins');
		$edit->pre_process('update','_pre_udp');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->cliente = new inputField('C&oacute;digo', 'cliente');
		$edit->cliente->rule = 'trim|strtoupper|alpha_dash_slash|callback_chexiste';
		$edit->cliente->mode = 'autohide';
		$edit->cliente->size = 9;
		$edit->cliente->maxlength = 5;

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->rule = 'trim|strtoupper|required';
		$edit->nombre->size = 55;
		$edit->nombre->maxlength = 45;
		$edit->nombre->style = 'width:100%;';

		$edit->nomfis = new textareaField('Raz&oacute;n Social', 'nomfis');
		$edit->nomfis->rule = 'trim';
		$edit->nomfis->cols = 53;
		$edit->nomfis->rows =  2;
		$edit->nomfis->maxlength =200;
		$edit->nomfis->style = 'width:100%;';

		$edit->contacto = new inputField('Contacto', 'contacto');
		$edit->contacto->rule = 'trim';
		$edit->contacto->size = 55;
		$edit->contacto->maxlength = 40;
		$edit->contacto->style = 'width:100%;';

		$edit->grupo = new dropdownField('Grupo', 'grupo');
		$edit->grupo->option('','Seleccione un grupo');
		$edit->grupo->options('SELECT TRIM(grupo) AS grupo, CONCAT(TRIM(grupo)," ",TRIM(gr_desc)) gr_desc FROM grcl ORDER BY gr_desc');
		$edit->grupo->rule = 'required';
		$edit->grupo->size = 6;
		$edit->grupo->maxlength = 4;
		$edit->grupo->style = 'width:200px';
		$edit->grupo->insertValue = $this->datasis->dameval('SELECT TRIM(grupo) FROM grcl WHERE gr_desc like "CONSUMIDOR FINAL%"');

		$edit->rifci = new inputField($this->pi18n->msj('rifci','RIF/CI'), 'rifci');
		$edit->rifci->rule = 'trim|strtoupper|required|callback_chci';
		$edit->rifci->maxlength =13;
		$edit->rifci->size =13;

		$edit->dire11 = new inputField('Oficina','dire11');
		$edit->dire11->rule = 'trim';
		$edit->dire11->size      = 45;
		$edit->dire11->maxlength = 60;
		$edit->dire11->style = 'width:95%;';

		$edit->dire12 = new inputField('','dire12');
		$edit->dire12->rule = 'trim';
		$edit->dire12->size      = 45;
		$edit->dire12->maxlength = 40;
		$edit->dire12->style = 'width:95%;';

		$edit->ciudad1 = new dropdownField('Ciudad','ciudad1');
		$edit->ciudad1->rule = 'trim';
		$edit->ciudad1->option('','Seleccionar');
		$edit->ciudad1->options('SELECT TRIM(ciudad) codigo, TRIM(ciudad) AS ciudad FROM ciud ORDER BY ciudad');
		$edit->ciudad1->style = 'width:200px';
		$edit->ciudad1->insertValue = trim($this->datasis->traevalor('CIUDAD'));

		$edit->dire21 = new inputField('Env&iacute;o','dire21');
		$edit->dire21->rule = 'trim';
		$edit->dire21->size      = 45;
		$edit->dire21->maxlength = 40;
		$edit->dire21->style = 'width:95%;';

		$edit->dire22 = new inputField('','dire22');
		$edit->dire22->rule = 'trim';
		$edit->dire22->size      = 45;
		$edit->dire22->maxlength = 40;
		$edit->dire22->style = 'width:95%;';

		$edit->ciudad2 = new dropdownField('Ciudad','ciudad2');
		$edit->ciudad2->rule = 'trim';
		$edit->ciudad2->option('','Seleccionar');
		$edit->ciudad2->options('SELECT TRIM(ciudad) codigo, TRIM(ciudad) AS ciudad FROM ciud ORDER BY ciudad');
		$edit->ciudad2->style = 'width:200px';

		$edit->repre  = new inputField('Representante', 'repre');
		$edit->repre->rule = 'trim';
		$edit->repre->maxlength =40;
		$edit->repre->size = 40;

		$edit->cirepre = new inputField('C&eacute;dula de Rep.', 'cirepre');
		$edit->cirepre->rule = 'trim|strtoupper|callback_chci';
		$edit->cirepre->maxlength =13;
		$edit->cirepre->size = 14;

		$edit->socio = new inputField('Consorcio', 'socio');
		$edit->socio->rule = 'trim';
		$edit->socio->size = 6;
		$edit->socio->maxlength =5;
		$edit->socio->append($boton);

		$arr_tiva=$this->pi18n->arr_msj('tivaarr','N=No Contribuyente,C=Contribuyente,E=Especial,R=Regimen Exento,O=Otro');
		$edit->tiva = new dropdownField('Tipo Fiscal', 'tiva');
		$edit->tiva->options($arr_tiva);
		$edit->tiva->style = 'width:130px';
		$edit->tiva->insertValue = 'N';

		$lriffis='<a href="javascript:consulrif(\'riffis\');" title="Consultar RIF en el SENIAT" onclick=""> SENIAT</a>';
		$edit->riffis = new inputField('RIF F&iacute;scal', 'riffis');
		$edit->riffis->size = 13;
		$edit->riffis->maxlength =10;
		$edit->riffis->append($lriffis);

		$edit->zona = new dropdownField('Zona', 'zona');
		$edit->zona->rule = 'trim|required';
		$edit->zona->option('','Seleccionar');
		$edit->zona->options('SELECT TRIM(codigo) AS codigo, CONCAT(codigo," ", nombre) nombre FROM zona ORDER BY nombre');
		$edit->zona->style = 'width:166px';
		$edit->zona->insertValue = trim($this->datasis->traevalor('ZONAXDEFECTO'));

		$edit->entidad = new dropdownField('Estado','estado');
		$edit->entidad->style='width:166px;';
		$edit->entidad->option('','Seleccione un Estado');
		$edit->entidad->options('SELECT codigo, entidad FROM estado ORDER BY entidad');


		$edit->pais = new inputField('Pa&iacute;s','pais');
		$edit->pais->rule = 'trim';
		$edit->pais->size =20;
		$edit->pais->maxlength =30;

		$edit->email = new inputField('E-mail', 'email');
		$edit->email->rule = 'trim|valid_email';
		$edit->email->size =22;
		$edit->email->maxlength =100;

		$edit->cuenta = new inputField('Cta.Contable', 'cuenta');
		$edit->cuenta->rule='trim|existecpla';
		$edit->cuenta->append($bcpla);
		$edit->cuenta->size=15;
		$edit->cuenta->maxlength =15;
		$edit->cuenta->insertValue = $this->datasis->dameval('SELECT cuenta FROM grcl WHERE gr_desc like "CONSUMIDOR FINAL%"');

		$edit->canticipo = new inputField('Cta.Anticipo', 'canticipo');
		$edit->canticipo->rule='trim|existecpla';
		$edit->canticipo->append($banti);
		$edit->canticipo->size=15;
		$edit->canticipo->maxlength =15;

		$edit->telefono = new inputField('Tel&eacute;fonos', 'telefono');
		$edit->telefono->rule = 'trim';
		$edit->telefono->size=22;
		$edit->telefono->maxlength =30;

		$edit->telefon2 = new inputField('Fax', 'telefon2');
		$edit->telefon2->rule = 'trim';
		$edit->telefon2->size=22;
		$edit->telefon2->maxlength =25;

		$edit->pin = new inputField('Pin', 'pin');
		$edit->pin->rule = 'trim';
		$edit->pin->size=8;
		$edit->pin->maxlength = 9;

		$edit->url = new inputField('Url', 'url');
		$edit->url->rule = 'trim';
		$edit->url->size=60;
		$edit->url->maxlength =120;

		$edit->fb = new inputField('facebook', 'fb');
		$edit->fb->rule = 'trim';
		$edit->fb->size=20;
		$edit->fb->maxlength =120;

		$edit->twitter = new inputField('Twitter', 'twitter');
		$edit->twitter->rule = 'trim';
		$edit->twitter->size=20;
		$edit->twitter->maxlength =120;

		$edit->tipo = new dropdownField('Precio ', 'tipo');
		$edit->tipo->options(array('1'=> 'Precio 1','2'=>'Precio 2', '3'=>'Precio 3','4'=>'Precio 4','5'=>'Mayor','0'=>'Inactivo'));
		$edit->tipo->style = 'width:90px';

		$edit->formap = new inputField('D&iacute;as', 'formap');
		$edit->formap->css_class='inputnum';
		$edit->formap->rule='trim|integer';
		$edit->formap->maxlength =10;
		$edit->formap->size =6;

		$edit->limite = new inputField('L&iacute;mite', 'limite');
		$edit->limite->css_class='inputnum';
		$edit->limite->rule='trim|numeric';
		$edit->limite->maxlength =12;
		$edit->limite->size = 10;

		$edit->vendedor = new dropdownField('Vendedor', 'vendedor');
		$edit->vendedor->option('','Ninguno');
		$edit->vendedor->options("SELECT TRIM(vendedor) AS vd, CONCAT(vendedor,'-',nombre) AS nom FROM vend WHERE tipo IN ('V','A') ORDER BY vendedor");
		$edit->vendedor->style = 'width:250px';

		$edit->porvend = new inputField('Comisi&oacute;n%', 'porvend');
		$edit->porvend->css_class='inputnum';
		$edit->porvend->rule='trim|numeric';
		$edit->porvend->size=4;
		$edit->porvend->maxlength =5;

		$edit->cobrador = new dropdownField('Cobrador', 'cobrador');
		$edit->cobrador->option('','Ninguno');
		$edit->cobrador->options("SELECT TRIM(vendedor) AS vd, CONCAT(vendedor,'-',nombre) nombre FROM vend WHERE tipo IN ('C','A') ORDER BY vendedor");
		$edit->cobrador->style = 'width:250px';

		$edit->porcobr = new inputField('Comisi&oacute;n%', 'porcobr');
		$edit->porcobr->css_class='inputnum';
		$edit->porcobr->rule='trim|numeric';
		$edit->porcobr->size=4;
		$edit->porcobr->maxlength =5;

		$edit->observa = new textareaField('Observaci&oacute;n', 'observa');
		$edit->observa->rule = 'trim';
		$edit->observa->cols = 70;
		$edit->observa->rows =3;

		$edit->mensaje = new inputField('Mensaje', 'mensaje');
		$edit->mensaje->rule = 'trim';
		$edit->mensaje->size = 50;
		$edit->mensaje->maxlength =40;

		$edit->mmargen = new inputField('Des. Mayor%','mmargen');
		$edit->mmargen->css_class='inputnum';
		$edit->mmargen->size=5;
		$edit->mmargen->maxlength=5;

		$edit->upago = new inputField('Ultimo Pago', 'upago');
		$edit->upago->rule = 'trim';
		$edit->upago->size = 6;
		$edit->upago->maxlength =6;

		$edit->tarifa = new inputField('Tarifa', 'tarifa');
		$edit->tarifa->rule = 'trim|callback_chtarifa';
		$edit->tarifa->size = 6;

		$edit->tarimonto = new inputField('Tarifa ajustada', 'tarimonto');
		$edit->tarimonto->rule = 'trim';
		$edit->tarimonto->size = 6;

		$edit->tactividad = new inputField('', 'tactividad');
		$edit->tactividad->db_name     = 'tactividad';
		$edit->tactividad->pointer     = true;
		$edit->tactividad->type='inputhidden';
		$edit->tactividad->in = 'tarifa';

		$edit->tminimo = new inputField('', 'tminimo');
		$edit->tminimo->db_name     = 'tminimo';
		$edit->tminimo->pointer     = true;
		$edit->tminimo->showformat  = 'decimal';
		$edit->tminimo->type='inputhidden';

		$edit->sucursal = new dropdownField('Sucursal', 'sucursal');
		$edit->sucursal->rule = 'required';
		$edit->sucursal->style= 'width:150px;';
		$edit->sucursal->option('','Ninguna');
		$edit->sucursal->options('SELECT TRIM(codigo) AS codigo,sucursal FROM sucu WHERE codigo IS NOT NULL ORDER BY sucursal');

		$edit->aniversario = new dateonlyfield('Aniversario', 'aniversario');
		$edit->aniversario->maxlength=10;
		$edit->aniversario->size=14;
		$edit->aniversario->rule='chfecha';
		$edit->aniversario->calendar=false;

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']  =& $edit;
			$conten['script'] =  $script;
			$this->load->view('view_scli', $conten);
		}

/*
		if($this->genesal){
			$edit->build();
			$conten['form']   =& $edit;
			$conten['script'] =  $script;
			$data['content']  =  $this->load->view('view_scli', $conten);

		}else{
			$edit->on_save_redirect=false;
			$edit->build();
			if($edit->on_success()){
				//$rt= 'Cliente Guardado';
				$rt=array(
					'status' =>'A',
					'mensaje'=>'Cliente Guardado',
					'pk'     =>$edit->_dataobject->pk
				);
				$rt = json_encode($rt);
			}elseif($edit->on_error()){
				$rt= html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
			return $rt;
		}
*/

	}

	function dataeditexpress(){
		$this->rapyd->load('dataedit');

		$script ='
		<script type="text/javascript" >
		$(function() {
			$("#rifci").focusout(function() {
				rif=$(this).val();
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
			});
		});

		function chrif(rif){
			rif.toUpperCase();
			var patt=/[EJPGV][0-9]{9} * /g;
			if(patt.test(rif)){
				var factor= new Array(4,3,2,7,6,5,4,3,2);
				var v=0;
				if(rif[0]=="V"){
					v=1;
				}else if(rif[0]=="E"){
					v=2;
				}else if(rif[0]=="J"){
					v=3;
				}else if(rif[0]=="P"){
					v=4;
				}else if(rif[0]=="G"){
					v=5;
				}
				acum=v*factor[0];
				for(i=1;i<9;i++){
					acum=acum+parseInt(rif[i])*factor[i];
				}
				acum=11-acum%11;
				if(acum>=10 || acum<=0){
					acum=0;
				}
				return (acum==parseInt(rif[9]));
			}else{
				return true;
			}
		}
		</script>';

		$do = new DataObject('scli');

		$edit = new DataEdit('Ficha clientes', $do);
		$edit->back_save   = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		$edit->back_url = site_url('ajax/reccierraventana/N');

		$edit->pre_process('delete','_pre_del');
		$edit->pre_process('insert','_pre_ins');
		$edit->pre_process('update','_pre_udp');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->rifci = new inputField('RIF/CI', 'rifci');
		$edit->rifci->rule = 'trim|strtoupper|required|callback_chci';
		$edit->rifci->maxlength =13;
		$edit->rifci->size =13;

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->rule = 'trim|strtoupper|required';
		$edit->nombre->size = 55;
		$edit->nombre->maxlength = 45;
		$edit->nombre->style = 'width:95%;';

		$edit->grupo = new dropdownField('Grupo', 'grupo');
		$edit->grupo->option('','Seleccione un grupo');
		$edit->grupo->options('SELECT grupo, CONCAT(grupo," ",gr_desc) gr_desc FROM grcl ORDER BY gr_desc');
		$edit->grupo->rule = 'required';
		$edit->grupo->size = 6;
		$edit->grupo->maxlength = 4;
		$edit->grupo->style = 'width:220px';
		$edit->grupo->insertValue = $this->datasis->dameval('SELECT grupo FROM grcl WHERE gr_desc like "CONSUMIDOR FINAL%"');

		$edit->dire11 = new inputField('Direcci&oacute;n','dire11');
		$edit->dire11->rule = 'trim';
		$edit->dire11->size      = 45;
		$edit->dire11->maxlength = 40;
		$edit->dire11->style = 'width:95%;';

		$edit->ciudad1 = new dropdownField('Ciudad','ciudad1');
		$edit->ciudad1->rule = 'trim';
		$edit->ciudad1->option('','Seleccionar');
		$edit->ciudad1->options('SELECT ciudad codigo, ciudad FROM ciud ORDER BY ciudad');
		$edit->ciudad1->style = 'width:200px';
		$edit->ciudad1->insertValue = $this->datasis->traevalor("CIUDAD");

		$edit->tiva = new dropdownField('Tipo Fiscal', 'tiva');
		$edit->tiva->option('N','No Contribuyente');
		$edit->tiva->option('C','Contribuyente');
		$edit->tiva->option('E','Especial');
		$edit->tiva->option('R','Regimen Exento');
		$edit->tiva->option('O','Otro');
		$edit->tiva->style = 'width:130px';
		$edit->tiva->insertValue = 'N';
		$edit->tiva->rule='required|enum[N,C,E,R,O]';

		$edit->zona = new dropdownField('Zona', 'zona');
		$edit->zona->rule = 'trim|required';
		$edit->zona->option('','Seleccionar');
		$edit->zona->options('SELECT codigo, CONCAT(codigo," ", nombre) nombre FROM zona ORDER BY nombre');
		$edit->zona->style = 'width:166px';
		$edit->zona->insertValue = $this->datasis->traevalor("ZONAXDEFECTO");

		$edit->email = new inputField('E-mail', 'email');
		$edit->email->rule = 'trim|valid_email';
		$edit->email->size =22;
		$edit->email->maxlength =100;

		$edit->tipo = new autoUpdateField('tipo','1', '1');
		$edit->buttons('save', 'undo');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['script'] .= $script;
		$data['title']   = '';
		$this->load->view('view_ventanas_sola', $data);
	}

	//Dataedit express para servicio
	function dataeditexpresser(){
		$this->rapyd->load('dataedit');

		$script ='
<script type="text/javascript" >
$(function() {
	$("#rifci").focusout(function() {
		rif=$(this).val();
		if(!chrif(rif)){
			alert("Al parecer el Rif colocado no es correcto, por favor verifique con el SENIAT.");
		}
	});

	$("#tarifa").autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "'.site_url('ajax/buscastarifa').'",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$("#tarifa").val("");
							$("#tactividad").val("");
							$("#tactividad_val").text("");
							$("#tminimo").val("");
							$("#tminimo_val").text("");
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
			$("#tarifa").attr("readonly", "readonly");

			$("#tarifa").val(ui.item.value);
			$("#tactividad").val(ui.item.actividad);
			$("#tactividad_val").text(ui.item.actividad);
			$("#tminimo").val(ui.item.minimo);
			$("#tminimo_val").text(ui.item.minimo);
			setTimeout(function() {  $("#tarifa").removeAttr("readonly"); }, 1500);
		}
	});

});

function chrif(rif){
	rif.toUpperCase();
	var patt=/[EJPGV][0-9]{9} * /g;
	if(patt.test(rif)){
		var factor= new Array(4,3,2,7,6,5,4,3,2);
		var v=0;
		if(rif[0]=="V"){
			v=1;
		}else if(rif[0]=="E"){
			v=2;
		}else if(rif[0]=="J"){
			v=3;
		}else if(rif[0]=="P"){
			v=4;
		}else if(rif[0]=="G"){
			v=5;
		}
		acum=v*factor[0];
		for(i=1;i<9;i++){
			acum=acum+parseInt(rif[i])*factor[i];
		}
		acum=11-acum%11;
		if(acum>=10 || acum<=0){
			acum=0;
		}
		return (acum==parseInt(rif[9]));
	}else{
		return true;
	}
}
</script>';

		$do = new DataObject('scli');

		$edit = new DataEdit('Ficha clientes', $do);
		$edit->back_save   = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		$edit->back_url = site_url('ajax/reccierraventana/N');

		$edit->pre_process('delete','_pre_del');
		$edit->pre_process('insert','_pre_ins');
		$edit->pre_process('update','_pre_udp');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->tarifa = new inputField('Tarifa', 'tarifa');
		$edit->tarifa->rule = 'trim|callback_chtarifa|required';
		$edit->tarifa->size = 6;
		//$edit->tarifa->maxlength =15;

		$edit->tactividad = new inputField('', 'tactividad');
		$edit->tactividad->db_name     = 'tactividad';
		$edit->tactividad->pointer     = true;
		$edit->tactividad->type='inputhidden';
		$edit->tactividad->in = 'tarifa';

		$edit->upago = new dateonlyField('Fecha de &uacute;ltimo pago','fecha','Ym');
		$edit->upago->rule='chfecha|required';
		$edit->upago->dbformat='Ym';
		$edit->upago->insertValue = date('Y-m-d');
		$edit->upago->size =10;
		$edit->upago->maxlength =8;
		$edit->upago->calendar=true;

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->rule = 'trim|strtoupper|required';
		$edit->nombre->size = 55;
		$edit->nombre->maxlength = 45;
		$edit->nombre->style = 'width:95%;';

		$edit->rifci = new inputField('RIF/CI', 'rifci');
		$edit->rifci->rule = 'trim|strtoupper|required|callback_chci';
		$edit->rifci->maxlength =13;
		$edit->rifci->size =13;

		$edit->grupo = new dropdownField('Grupo', 'grupo');
		$edit->grupo->option('','Seleccione un grupo');
		$edit->grupo->options('SELECT grupo, CONCAT(grupo," ",gr_desc) gr_desc FROM grcl ORDER BY gr_desc');
		$edit->grupo->rule = 'required';
		$edit->grupo->size = 6;
		$edit->grupo->maxlength = 4;
		$edit->grupo->style = 'width:200px';
		$edit->grupo->insertValue = $this->datasis->dameval('SELECT grupo FROM grcl WHERE gr_desc like "CONSUMIDOR FINAL%"');

		$edit->dire11 = new inputField('Direcci&oacute;n','dire11');
		$edit->dire11->rule = 'trim|required';
		$edit->dire11->size      = 45;
		$edit->dire11->maxlength = 40;
		$edit->dire11->style = 'width:95%;';


		$edit->ciudad1 = new dropdownField('Ciudad','ciudad1');
		$edit->ciudad1->rule = 'trim';
		$edit->ciudad1->option('','Seleccionar');
		$edit->ciudad1->options('SELECT ciudad codigo, ciudad FROM ciud ORDER BY ciudad');
		$edit->ciudad1->style = 'width:200px';
		$edit->ciudad1->insertValue = $this->datasis->traevalor("CIUDAD");

		$edit->tiva = new dropdownField('Tipo Fiscal', 'tiva');
		$edit->tiva->option('N','No Contribuyente');
		$edit->tiva->option('C','Contribuyente');
		$edit->tiva->option('E','Especial');
		$edit->tiva->option('R','Regimen Exento');
		$edit->tiva->option('O','Otro');
		$edit->tiva->style = 'width:130px';
		$edit->tiva->insertValue = 'N';
		$edit->tiva->rule='required|enum[N,C,E,R,O]';

		$edit->zona = new dropdownField('Zona', 'zona');
		$edit->zona->rule = 'trim|required';
		$edit->zona->option('','Seleccionar');
		$edit->zona->options('SELECT codigo, CONCAT(codigo," ", nombre) nombre FROM zona ORDER BY nombre');
		$edit->zona->style = 'width:166px';
		$edit->zona->insertValue = $this->datasis->traevalor("ZONAXDEFECTO");

		$edit->email = new inputField('E-mail', 'email');
		$edit->email->rule = 'trim|valid_email';
		$edit->email->size =22;
		$edit->email->maxlength =100;

		$edit->tipo = new autoUpdateField('tipo','1', '1');
		$edit->buttons('save', 'undo');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['head']	.= style("jquery.alerts.css");
		$data['head']	.= style("redmond/jquery-ui.css");
		$data['head']   .= style('jquery.autocomplete.css');
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['script'] .= script("jquery-ui.js");
		$data['script'] .= script("jquery.alerts.js");
		$data['script'] .= $script;
		$data['title']   = '';
		$this->load->view('view_ventanas_sola', $data);
	}


	function filtergridcredi(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter('Gesti&oacute;n de l&iacute;mites de cr&eacute;dito');
		$sel=array('a.formap','a.limite' ,'a.tolera','a.maxtole','a.cliente','a.nombre','a.credito','b.motivo','a.id');
		$filter->db->select($sel);
		$filter->db->from('scli AS a');
		$filter->db->join('sclibitalimit AS b','a.cliente=b.cliente','left');
		$filter->db->group_by('a.cliente');

		$filter->cliente = new inputField('C&oacute;digo', 'cliente');
		$filter->cliente->db_name=  'a.cliente';
		$filter->cliente->size=6;

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->db_name=  'a.nombre';
		$filter->nombre->rule      ='max_length[45]';
		$filter->nombre->maxlength =45;

		$filter->limited = new inputField('L&iacute;mite','limited');
		$filter->limiteh = new inputField('L&iacute;mite','limiteh');
		$filter->limited->size    = $filter->limiteh->size =8;
		$filter->limited->clause  = $filter->limiteh->clause ='where';
		$filter->limited->db_name = $filter->limiteh->db_name='a.limite';
		$filter->limited->operator= '>=';
		$filter->limiteh->operator= '<=';
		$filter->limiteh->in      = 'limited';
		$filter->limited->css_class = 'inputonlynum';
		$filter->limiteh->css_class = 'inputonlynum';

		$filter->credito = new dropdownField('Cr&eacute;dito','credito');
		$filter->credito->db_name = 'a.credito';
		$filter->credito->option('' ,'Todos');
		$filter->credito->option('S','Activo');
		$filter->credito->option('N','Inactivo');
		$filter->credito->title = 'Si el cliente puede o no optar por cr&eacute;dito en la empresa';
		$filter->credito->style = 'width: 145px;';

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor('ventas/scli/creditoedit/modify/<#id#>','<#cliente#>');

		$grid = new DataGrid('');
		$grid->order_by('cliente');
		$grid->per_page = 20;

		$grid->column_orderby('Cliente',$uri   ,'cliente','align="left"');
		$grid->column_orderby('Nombre','nombre','nombre','align="left"');
		$grid->column_orderby('Cr&eacute;dito' ,'<#credito#>' ,'credito','align="center"');
		$grid->column_orderby('D&iacute;as'    ,'<nformat><#formap#></nformat>'  ,'formap' ,'align="right"');
		$grid->column_orderby('L&iacute;mite'  ,'<nformat><#limite#></nformat>'  ,'limite' ,'align="right"');
		$grid->column_orderby('Tolera'         ,'<nformat><#tolera#></nformat>%' ,'tolera' ,'align="right"');
		$grid->column_orderby('T.M&aacute;xima','<nformat><#maxtole#></nformat>%','maxtole','align="right"');
		$grid->column('Motivo','motivo');

		//$action = "javascript:window.location='".site_url('/reportes/ver/SCLILIMIT/SCLI')."'";
		//$grid->button('btn_reporte', 'Reporte', $action,'TR');
		$grid->build();

		$script= '<script type="text/javascript" >
		$(function() {
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});
		</script>';

		//$data['script']  = $script;
		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = heading('Gesti&oacute;n de l&iacute;mites de cr&eacute;dito');
		$this->load->view('view_ventanas', $data);
	}

	function vcard($id_scli){
		$dbid=$this->db->escape($id_scli);
		$scli=$this->datasis->damerow("SELECT contacto,nombre,telefono,telefon2,dire11 FROM scli WHERE id=$dbid");
		if(!empty($scli)){
			$this->load->library('Qr');
			$contacto=trim($scli['contacto']);
			$nombre  =trim($scli['nombre']);
			$telf1   =trim($scli['telefono']);
			$telf2   =trim($scli['telefon2']);
			$direc   =trim($scli['dire11']);
			if(!empty($contacto)){
				$empresa=$nombre;
				$nombre =$contacto;
			}else{
				$empresa='';
			}
			$text = "BEGIN:VCARD\n";
			$text.= "VERSION:2.1\n";
			$text.= "N:$nombre\n";
			$text.= "FN:$nombre\n";
			if(!empty($empresa)) $text.= "ORG:$empresa\n";
			//$text.= "TITLE:$cargo\n";
			if(!empty($telf1)) $text.= "TEL;WORK;VOICE:$telf1\n";
			if(!empty($telf2)) $text.= "TEL;WORK;VOICE:$telf2\n";
			$text.= "ADR;WORK:$direc\n";
			$text.= "END:VCARD";
			$this->qr->imgcode($text);
		}
	}

	function creditoedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('', 'scli');
		$edit->back_url = site_url('ajax/reccierraventana');
		$edit->back_save   = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		//$edit->back_url = site_url('ventas/scli/filtergridcredi');


		$edit->post_process('insert','_pos_credi_insert');
		$edit->post_process('update','_pos_credi_update');
		$edit->post_process('delete','_pos_credi_delete');
		$edit->pre_process( 'insert','_pre_credi_insert');
		$edit->pre_process( 'update','_pre_credi_update');
		$edit->pre_process( 'delete','_pre_credi_delete');

		$edit->cliente = new inputField('Cliente','cliente');
		$edit->cliente->rule='max_length[5]';
		$edit->cliente->size =7;
		$edit->cliente->maxlength =5;
		$edit->cliente->mode= 'autohide';

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[45]';
		$edit->nombre->in = 'cliente';
		$edit->nombre->mode = 'autohide';
		$edit->nombre->size =47;

		$edit->credito = new dropdownField('Cr&eacute;dito','credito');
		$edit->credito->rule = 'required|enum[S,N]';
		$edit->credito->option('S','Activo');
		$edit->credito->option('N','Inactivo');
		$edit->credito->title = 'Activar o Desactivar credito del Cliente';
		$edit->credito->style = 'width: 145px;';

		$edit->formap = new inputField('D&iacute;as de cr&eacute;dito','formap');
		$edit->formap->rule      = 'max_length[6]|numeric|positive|required';
		$edit->formap->title     = 'Dias de Credito';
		$edit->formap->autocomplete  = false;
		$edit->formap->css_class = 'inputonlynum';
		$edit->formap->size      = 15;
		$edit->formap->maxlength = 6;
		$edit->formap->append('Al ser cero se anulara el cr&eacute;dito');

		$edit->limite = new inputField('L&iacute;mite de cr&eacute;dito','limite');
		$edit->limite->rule='max_length[20]|integer|positive|required';
		$edit->limite->css_class='inputonlynum';
		$edit->limite->title = 'Monto de Credito';
		$edit->limite->size  = 15;
		$edit->limite->autocomplete  = false;
		$edit->limite->maxlength =20;
		$edit->limite->append('Al ser cero se anulara el cr&eacute;dito');

		$edit->tolera = new inputField('Tolerancia %','tolera');
		$edit->tolera->rule='max_length[9]|numeric|porcent|callback_chtolera|required';
		$edit->tolera->css_class='inputnum';
		$edit->tolera->title = '% de tolerancia por encima del monto limite';
		$edit->tolera->autocomplete  = false;
		$edit->tolera->size =5;
		$edit->tolera->maxlength =9;

		$edit->maxtole = new inputField('Max Tolerancia','maxtole');
		$edit->maxtole->rule='max_length[9]|numeric|porcent|required';
		$edit->maxtole->css_class='inputnum';
		$edit->maxtole->autocomplete  = false;
		$edit->maxtole->title = '% Maximo de tolerancia';
		$edit->maxtole->size =5;
		//$edit->maxtole->in='tolera';
		$edit->maxtole->maxlength =9;

		$edit->motivo = new textareaField('Motivo', 'motivo');
		$edit->motivo->title = 'Motivo del cambio en la pol&iacute;tica de cr&eacute;dito';
		$edit->motivo->cols = 50;
		$edit->motivo->rows = 4;
		$edit->motivo->rule = 'required';

		$plim = $this->datasis->sidapuede('SCLIOTR', 'SCLILIMITE()');   //
		$pext = $this->datasis->sidapuede('SCLIOTR', 'SCLITOLERA()');   //Extra credito
		$paxt = $this->datasis->sidapuede('SCLIOTR', 'SCLIMAXTOLE()');  //Asigna Extra credito

		if(!$plim){
			$edit->credito->mode = 'autohide';
			$edit->formap->mode  = 'autohide';
			$edit->limite->mode  = 'autohide';
			$edit->motivo->mode  = 'autohide';
		}
		if(!$pext) $edit->tolera->mode  = 'autohide';
		if(!$paxt) $edit->maxtole->mode = 'autohide';

		if($plim || $paxt || $pext){
			//$edit->buttons('modify', 'save');
		}
		//$edit->buttons( 'undo','back');
		$edit->build();

		$script= '<script type="text/javascript" >
		$(function() {
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});
		</script>';

		$data['content'] = $edit->output;
		$data['script']  = $script;
		$this->load->view('jqgrid/ventanajq', $data);
	}

	function claveedit(){
		//$this->pi18n->cargar('scli','dataedit');
		$this->rapyd->load('dataedit');
		$this->rapyd->uri->keep_persistence();

		$edit = new DataEdit('Cambio/Asignacion de Clave de Acceso', 'scli');
		$id=$edit->_dataobject->pk['id'];

		$edit->cliente = new inputField('Cliente', 'cliente');
		$edit->cliente->mode = 'autohide';
		$edit->cliente->when=array('show','modify');

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->mode = 'autohide';
		$edit->nombre->when=array('show','modify');

		$edit->clave = new inputField('Clave', 'clave');
		$edit->clave->type = 'password';
		$edit->clave->rule = 'matches[clave1]';
		$edit->clave->when = array('modify');

		$edit->clave1 = new inputField('Confirmaci&oacute;n', 'clave1');
		$edit->clave1->type    = 'password';
		$edit->clave1->db_name = 'clave';
		$edit->clave1->when    = array('modify');

		$edit->clave->size      = $edit->clave1->size = 10;
		$edit->clave->maxlength = $edit->clave1->maxlength = 12;

		$edit->build();

		$this->rapyd->jquery[]="$('#df1').submit(function(){
			if( $('#clave').val() != '' ) {
				pwEncrypt = $().crypt( {
					method: 'md5',
					source: $('#clave').val()
				});
				$('#clave').val(pwEncrypt);

				pwEncrypt = $().crypt( {
					method: 'md5',
					source: $('#clave1').val()
				});
				$('#clave1').val(pwEncrypt);
			}
			return true;
		});";

		$data['content'] = $edit->output;
		$this->load->view('jqgrid/ventanajq', $data);

	}

	//Permite crear un clientes desde otras interfaces
	function creascli(){
		$rifci=$this->input->post('rifci');
		if(preg_match('/[VEJG][0-9]{9}$/',$rifci)>0){
			$_POST['tiva']='C';
		}else{
			$_POST['tiva']='N';
		}
		$_POST['tipo']='1';
		$this->genesal=false;
		$rt=$this->dataedit();
		echo $rt;
	}

	//******************************************************************
	// Forma de Ciudades
	//
	function marcaform(){
		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'      => 'true',
			'align'       => "'center'",
			'width'       => 20,
			'editable'    => 'false',
			'editoptions' => '{readonly:true,size:10}'
			)
		);

		$grid->addField('ciudad');
		$grid->label('Ciudad');
		$grid->params(array(
			'width'     => 180,
			'editable'  => 'true',
			'edittype'  => "'text'",
			'editrules' => '{required:true}'
			)
		);

		$grid->showpager(true);
		$grid->setViewRecords(false);
		$grid->setWidth('300');
		$grid->setHeight('280');

		$grid->setUrlget(site_url('ventas/ciud/getdata/'));
		$grid->setUrlput(site_url('ventas/ciud/setdata/'));

		$mgrid = $grid->deploy();

		$msalida  = '<script type="text/javascript">'."\n";
		$msalida .= '
		$("#newapi'.$mgrid['gridname'].'").jqGrid({
			ajaxGridOptions : {type:"POST"}
			,jsonReader : { root:"data", repeatitems: false }
			'.$mgrid['table'].'
			,scroll: true
			,pgtext: null, pgbuttons: false, rowList:[]
		})
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'navGrid\',  "#pnewapi'.$mgrid['gridname'].'",{edit:false, add:false, del:true, search: false});
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'inlineNav\',"#pnewapi'.$mgrid['gridname'].'");
		$("#newapi'.$mgrid['gridname'].'").jqGrid(\'filterToolbar\');
		';

		$msalida .= "\n</script>\n";
		$msalida .= '<id class="anexos"><table id="newapi'.$mgrid['gridname'].'"></table>';
		$msalida .= '<div   id="pnewapi'.$mgrid['gridname'].'"></div></div>';

		echo $msalida;

	}


	//Crea un cliente desde Pers AJAX
	function creafrompers( $status=null, $id_pers=null ){
		if($status=='insert' && !empty($id_pers)){
			$codigo    = $this->input->post('codigo');
			$dbid_pers = $this->db->escape($id_pers);

			$query = $this->db->query("SELECT nacional,cedula,codigo,nombre,apellido,direc1,direc2 FROM pers WHERE id=".$dbid_pers);
			if($query->num_rows()>0){
				$row = $query->row();

				if(empty($codigo)){
					$codigo='E'.trim($row->codigo);
				}
				$cedula = trim($row->nacional).trim($row->cedula);
				$mSQL   = 'SELECT nombre FROM scli WHERE rifci='.$this->db->escape($cedula);
				$nomgua = $this->datasis->dameval($mSQL);
				if(!empty($nomgua)){
					echo 'Al parecer ya existen un cliente creado con el mismo documento de identidad';
					return ;
				}

				$nombre = trim($row->nombre).' '.trim($row->apellido);
				$_POST = array (
					'cliente'    => $codigo,
					'rifci'      => $cedula,
					'nombre'     => $nombre,
					'nomfis'     => $nombre,
					'contacto'   => '',
					'tipo'       => '1',
					'mmargen'    => '',
					'tiva'       => 'N',
					'zona'       => $this->datasis->traevalor('ZONAXDEFECTO'),
					'grupo'      => $this->datasis->dameval('SELECT grupo FROM grcl WHERE gr_desc like "%EMPLEADO%" OR gr_desc like "%TRABAJADOR%"'),
					'socio'      => '',
					'dire11'     => $row->direc1,
					'dire12'     => $row->direc2,
					'ciudad1'    => $this->datasis->traevalor('CIUDAD'),
					'dire21'     => '',
					'dire22'     => '',
					'ciudad2'    => '',
					'telefono'   => $row->telefono,
					'url'        => '',
					'telefon2'   => '',
					'fb'         => '',
					'pin'        => '',
					'email'      => '',
					'twitter'    => '',
					'repre'      => '',
					'cirepre'    => '',
					'vendedor'   => '',
					'porvend'    => '',
					'cobrador'   => '',
					'porcobr'    => '',
					'cuenta'     => $this->datasis->dameval('SELECT cuenta FROM grcl WHERE gr_desc like "CONSUMIDOR FINAL%"'),
					'mensaje'    => '',
					'observa'    => '',
					'tarifa'     => '',
					'tactividad' => '',
					'tminimo'    => '',
					'upago'      => '',
					'tarimonto'  => ''
				);

				$this->genesal=false;

				$rt = $this->dataedit();
				if(stripos($rt, 'guardado')!== false){
					echo '';
				}else{
					echo $rt;
				}
			}else{
				echo 'Registro no encontrado';
			}
		}
	}


	// Revisa si existe el codigo
	function scliexiste(){
		$cliente  = rawurldecode($this->input->post('codigo'));
		$dbcliente= $this->db->escape($cliente);
		$existe  = $this->datasis->dameval("SELECT COUNT(*) FROM scli WHERE cliente=${dbcliente}");
		$devo    = 'N ';
		if($existe > 0){
			$devo  ='S';
			$devo .= $this->datasis->dameval("SELECT nombre FROM scli WHERE cliente=${dbcliente}");
		}
		echo $devo;
	}

	function chtarifa($id){
		$dbid = $this->db->escape($id);
		$cana=$this->datasis->dameval("SELECT COUNT(*) FROM tarifa WHERE id=${dbid}");
		if($cana>0){
			return true;
		}else{
			$this->validation->set_message('chtarifa','El campo %s debe contener una tarifa v&aacute;lida.');
			return false;
		}
	}

	function chtolera($monto){
		$paxt=$this->secu->puede('1313');
		if($paxt){
			$maxtole=$this->input->post('maxtole');
		}else{
			$maxtole=$this->datasis->dameval('SELECT maxtole FROM scli WHERE id='.$this->rapyd->uri->get_edited_id());
		}

		if($monto>$maxtole){
			$this->validation->set_message('chtolera', 'La tolerancia no puede ser mayor que el margen m&aacute;ximo pautado');
			return false;
		}
		return true;
	}

	function chdfiscal($tiva){
		$nomfis=$this->input->post('nomfis');
		$riffis=$this->input->post('riffis');
		if($tiva=='C' OR $tiva=='E' OR $tiva=='R')
			if(empty($nomfis)){
				$this->validation->set_message('chdfiscal', "Debe introducir el nombre fiscal cuando el cliente es contribuyente");
				return false;
			}
			//elseif (empty($riffis)) {
			//	$this->validation->set_message('chdfiscal', "Debe introducir rif fiscal");
			//	return FALSE;
			//}
		return TRUE;
	}

	function _pre_credi_update($do){
		$cliente   = $do->get('cliente');
		$limite    = $do->get('limite');
		$dias      = $do->get('formap');
		$this->credi_motivo=$this->input->post('motivo');

		if(empty($limite) || empty($dias)){
			$do->set('tolera' ,'0');
			$do->set('maxtole','0');
			$do->set('limite' ,'0');
			$do->set('formap' ,'0');
			//$do->set('credito','N');
		}
		$do->rm_get('motivo');
		$dbcliente = $this->db->escape($cliente);

		$sel=array('limite','credito','tolera','maxtole','formap');
		$this->db->select($sel);
		$this->db->from('scli AS a');
		$this->db->where('cliente',$cliente);
		$query = $this->db->get();
		$row = $query->row();


		$this->limitsant   = $row->limite;
		$this->creditosant = $row->credito;
		$this->tolerasant  = $row->tolera;
		$this->maxtolesant = $row->maxtole;
		$this->formapsant  = $row->formap;
	}

	function _pos_credi_update($do){
		$codigo=$do->get('cliente');
		$limite=$do->get('limite');

		$data = array(
			'cliente'    => $codigo,
			'credito'    => $do->get('credito'),
			'creditoant' => $this->creditosant,
			'limite'     => $limite,
			'limiteant'  => $this->limitsant,
			'tolera'     => $do->get('tolera'),
			'toleraant'  => $this->tolerasant,
			'motivo'     => $this->credi_motivo,
			'formap'     => $do->get('formap'),
			'formapsant' => $this->formapsant,
			'maxtol'     => $do->get('maxtole'),
			'maxtolant'  => $this->maxtolesant,
			'estampa'    => date('Y-m-d H:i:s'),
			'usuario'    => $this->secu->usuario()
		);

		$this->db->insert('sclibitalimit', $data);
		logusu('scli',"CLIENTE ${codigo} MODIFICADO, LIMITE ".$this->limitsant.'-->'.$limite);
	}

	function _pre_credi_insert($do){ return false; }
	function _pre_credi_delete($do){ return false; }

	function _pre_del($do) {
		$codigo=$this->db->escape($do->get('cliente'));
		$check =  intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM sfac WHERE cod_cli=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM smov WHERE cod_cli=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM snot WHERE cod_cli=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM snte WHERE cod_cli=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM otin WHERE cod_cli=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM pfac WHERE cod_cli=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM pers WHERE enlace=${codigo}"));
		$check += intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM bmov WHERE clipro='C' AND codcp=${codigo}"));

		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
			return False;
		}
		return True;
	}

	function _pre_udp($do){
		$do->set('riffis',trim($do->get('rifci')));
		$nomfis = $do->get('nomfis');
		if ( empty( $nomfis ) ) {
			$do->set('nomfis',trim($do->get('nombre')));
		}

		$cliente   = $do->get('cliente');
		$dbcliente = $this->db->escape($cliente);
		$this->limitsant = $this->datasis->dameval('SELECT limite FROM scli WHERE cliente='.$dbcliente);
	}

	function _pre_ins($do) {
		$do->set('riffis',trim($do->get('rifci')));
		$nomfis = $do->get('nomfis');
		if ( empty( $nomfis ) ) {
			$do->set('nomfis',trim($do->get('nombre')));
		}

		$cliente = $do->get('cliente');
		if(empty($cliente)){
			$do->set('cliente',$this->_numatri());
		}
		return true;
	}

	function _post_insert($do){
		$codigo=$do->get('cliente');
		$limite=$do->get('limite');
		logusu('scli',"CLIENTE $codigo CREADO, LIMITE $limite");
	}

	function _post_update($do){
		$codigo=$do->get('cliente');
		$limite=$do->get('limite');
		logusu('scli',"CLIENTE ${codigo} MODIFICADO, LIMITE ".$this->limitsant.'-->'.$limite);
	}

	function _post_delete($do){
		$codigo=$do->get('cliente');
		$limite=$do->get('limite');
		logusu('scli',"CLIENTE $codigo ELIMINADO, LIMITE $limite");
	}

	function chexiste($codigo){
		$codigo  =$this->input->post('cliente');
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM scli WHERE cliente=${dbcodigo}");
		if ($check > 0){
			$mSQL_1=$this->db->query("SELECT nombre, rifci FROM scli WHERE cliente=${dbcodigo}");
			$row = $mSQL_1->row();
			$nombre =$row->nombre;
			$rifci  =$row->rifci;
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe para el cliente ${nombre}  ${rifci}");
			return false;
		}else {
			return true;
		}
	}

	function autocomplete($campo,$cod=FALSE){
		if($cod!==false){
			$cod=$this->db->escape_like_str($cod);
			$data['cliente']="SELECT cliente AS c1 ,nombre AS c2 FROM scli WHERE cliente LIKE '$cod%' ORDER BY cliente LIMIT 10";
			if(isset($data[$campo])){
				$query=$this->db->query($data[$campo]);
				if($query->num_rows() > 0){
					foreach($query->result() AS $row){
						echo $row->c1.'|'.$row->c2."\n";
					}
				}
			}
		}
	}

	function consulta(){
		$this->load->helper('openflash');
		$this->rapyd->load('datagrid');
		$fields = $this->db->field_data('scli');
		$url_pk = $this->uri->segment_array();
		$coun=0; $pk=array();
		foreach ($fields as $field){
			if($field->primary_key==1){
				$coun++;
				$pk[]=$field->name;
			}
		}
		$values=array_slice($url_pk,-$coun);
		$claves=array_combine (array_reverse($pk) ,$values );

		$mCodigo = $this->datasis->dameval("SELECT cliente FROM scli WHERE id=".$claves['id']."");

		$grid = new DataGrid('Ventas por Mes');
		$grid->db->_protect_identifiers=false;
		$grid->db->select( array('a.tipo_doc','a.fecha', 'a.numero', 'a.monto', 'a.abonos', 'a.monto-a.abonos saldo' ) );
		$grid->db->from('smov a');
		$grid->db->where('a.cod_cli', $mCodigo );
		$grid->db->where('a.monto <> a.abonos');
		$grid->db->where('a.tipo_doc IN ("FC","ND","GI") ' );
		$grid->db->orderby('a.fecha');

		$grid->column('Fecha',  'fecha' );
		$grid->column('Tipo',   "tipo_doc", 'align="CENTER"');
		$grid->column('Numero', "numero",   'align="LEFT"');
		$grid->column('Monto',  "<nformat><#monto#></nformat>",  'align="RIGHT"');
		$grid->column('Abonos', "<nformat><#abonos#></nformat>", 'align="RIGHT"');
		$grid->column('Saldo',  "<nformat><#saldo#></nformat>",  'align="RIGHT"');
		$grid->build();

		$nombre = $this->datasis->dameval("SELECT nombre FROM scli WHERE id=".$claves['id']." ");

		$data['content'] = $grid->output;
		$data["head"]     = script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$data['title']    = '<h1>Consulta de Clientes</h1>';
		$data["subtitle"] = "
			<div align='center' style='border: 2px outset #EFEFEF;background: #EFEFEF;font-size:18px'>
				<a href='javascript:javascript:history.go(-1)'>(".$mCodigo.") ".$nombre."</a>
			</div>";
		$this->load->view('view_ventanas', $data);
	}

	function sclimemo() {
		$mid     = $_REQUEST['mid'];
		$mensaje = urldecode($_REQUEST['mensaje']);

		$this->db->query("UPDATE scli SET observa=? WHERE id=$mid",array($mensaje));
		echo "Observaciones Guardadas";
	}

	function sclifusion() {
		$mviejo    = strtoupper($_REQUEST['mviejo']);
		$mnuevo    = strtoupper($_REQUEST['mnuevo']);

		//ELIMINAR DE SCLI
		$mYaEsta = $this->datasis->dameval("SELECT count(*) FROM scli WHERE cliente=".$this->db->escape($mnuevo));

		if ( $mYaEsta > 0 )
			$this->db->query("DELETE FROM scli WHERE cliente=".$this->db->escape($mviejo));
		else
			$this->db->query("UPDATE scli SET cliente=".$this->db->escape($mnuevo)." WHERE cliente=".$this->db->escape($mviejo));

		$this->db->query("UPDATE scli SET socio=".$this->db->escape($mnuevo)." WHERE socio=".$this->db->escape($mviejo));
		// SPRV
		$this->db->query("UPDATE sprv SET cliente=".$this->db->escape($mnuevo)." WHERE cliente=".$this->db->escape($mviejo));
		// SMOV
		$this->db->query("UPDATE smov SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// APAN
		$this->db->query("UPDATE apan SET clipro=".$this->db->escape($mnuevo)." WHERE clipro=".$this->db->escape($mviejo)." AND tipo='C' ");
		$this->db->query("UPDATE apan SET reinte=".$this->db->escape($mnuevo)." WHERE reinte=".$this->db->escape($mviejo)." AND tipo='P' ");
		// ITCCLI
		$this->db->query("UPDATE itccli SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// BMOV CLIPRO='C'  CODCP
		$this->db->query("UPDATE bmov SET codcp=".$this->db->escape($mnuevo)." WHERE codcp=".$this->db->escape($mviejo)." AND clipro='C'");
		// SFPA
		$this->db->query("UPDATE sfpa SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// OTIN
		$this->db->query("UPDATE otin SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// CRUC
		$this->db->query("UPDATE cruc SET cliente=".$this->db->escape($mnuevo)." WHERE cliente=".$this->db->escape($mviejo)." AND MID(tipo,1,1)='C' ");
		// CRUC
		$this->db->query("UPDATE cruc SET proveed=".$this->db->escape($mnuevo)." WHERE proveed=".$this->db->escape($mviejo)." AND MID(tipo,3,1)='C' ");
		// PRMO
		$this->db->query("UPDATE prmo SET clipro=".$this->db->escape($mnuevo)." WHERE clipro=".$this->db->escape($mviejo)." AND tipop IN ('1','3','6') ");
		// RIVC
		$this->db->query("UPDATE rivc SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));

		// FMAY
		if ( $this->datasis->istabla('fmay'))
			$this->db->query("UPDATE fmay SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// SFAC
		if ( $this->datasis->istabla('sfac') )
			$this->db->query("UPDATE sfac SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// PFAC
		if ( $this->datasis->istabla('pfac'))
			$this->db->query("UPDATE pfac SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// PRES
		if ( $this->datasis->istabla('pres'))
			$this->db->query("UPDATE pres SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// SPRE
		if ( $this->datasis->istabla('spre'))
			$this->db->query("UPDATE spre SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// ITPRES
		if ( $this->datasis->istabla('itpres'))
			$this->db->query("UPDATE itpres SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// SNTE
		if ( $this->datasis->istabla('snte'))
			$this->db->query("UPDATE snte SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));
		// SNOT
		if ( $this->datasis->istabla('snot'))
			$this->db->query("UPDATE snot SET cod_cli=".$this->db->escape($mnuevo)." WHERE cod_cli=".$this->db->escape($mviejo));

		logusu('SCLI',"Cambio/Fusion de cliente $mviejo ==> $mnuevo ");
		echo "Cambios concluidos ";

	}

	function sclilimite() {
		$mid     = isset($_REQUEST['mid'])     ? $_REQUEST['mid']     : -1;
		$credito = isset($_REQUEST['credito']) ? $_REQUEST['credito'] : '-';
		$formap  = isset($_REQUEST['formap'])  ? $_REQUEST['formap']  : -1;
		$limite  = isset($_REQUEST['limite'])  ? $_REQUEST['limite']  : -1;
		$tolera  = isset($_REQUEST['tolera'])  ? $_REQUEST['tolera']  : -1;
		$maxtole = isset($_REQUEST['maxtole']) ? $_REQUEST['maxtole'] : -1;
		$observa = isset($_REQUEST['observa']) ? $_REQUEST['observa'] : '';

		if ( $mid == -1 ){
			echo 'Error de id';
			return;
		}

		//actualiza scli
		if ($credito != '-') $data['credito'] = $credito;
		if ($formap  != -1 ) $data['formap']  = $formap;
		if ($limite  != -1 ) $data['limite']  = $limite;
		if ($maxtole != -1 ) $data['maxtole'] = $maxtole;
		if ($tolera  != -1 ) $data['tolera']  = $tolera;

		$cliente = $this->datasis->dameval("SELECT CONCAT(cliente, ' ', nombre) nombre FROM scli WHERE id=$mid ");
		$this->db->where('id', $mid);
		$this->db->update('scli', $data);

		logusu("SCLI", "Cambio de Limite: $cliente Observaciones: ".$observa);

		echo 'Cambio Efectuado';
	}


	function sclibusca() {
		$start    = isset($_REQUEST['start'])   ? $_REQUEST['start']  :  0;
		$limit    = isset($_REQUEST['limit'])   ? $_REQUEST['limit']  : 25;
		$cliente  = isset($_REQUEST['cliente']) ? $_REQUEST['cliente']: '';
		$semilla  = isset($_REQUEST['query'])   ? $_REQUEST['query']  : '';

		$semilla = trim($semilla);

		$mSQL = "SELECT cliente item, CONCAT(cliente, ' ', nombre) valor FROM scli WHERE tipo<>'0' ";
		if ( strlen($semilla)>0 ){
			$mSQL .= " AND ( cliente LIKE '$semilla%' OR nombre LIKE '%$semilla%' OR  rifci LIKE '%$semilla%') ";
		} else {
			if ( strlen($cliente)>0 ) $mSQL .= " AND cliente = '$cliente' ";
		}
		$mSQL .= "ORDER BY nombre ";
		$results = $this->db->count_all('scli');

		if ( empty($mSQL)) {
			echo '{success:true, message:"mSQL vacio, Loaded data", results: 0, data:'.json_encode(array()).'}';
		} else {
			$mSQL .= " limit $start, $limit ";
			$query = $this->db->query($mSQL);
			$arr = array();
			foreach ($query->result_array() as $row){
				$meco = array();
				foreach( $row as $idd=>$campo ) {
					$meco[$idd] = utf8_encode($campo);
				}
				$arr[] = $meco;
			}
			echo '{success:true, message:"'.$mSQL.'", results:'. $results.', data:'.json_encode($arr).'}';
		}
	}

	function scliventa($desp=0,$rifci=null){
		if(empty($rifci)){
			$rifci = $this->db->escape($rifci);
		}else{
			$rifci = $this->input->post('rifci');
		}

		$dbrifci  = $this->db->escape($rifci);
		$cod_cli  = $this->datasis->dameval("SELECT cliente FROM scli WHERE rifci=${dbrifci} LIMIT 1");
		$dbcod_cli= $this->db->escape($cod_cli);

		$arr_numas=array();
		$mSQL = "SELECT numero FROM sfac WHERE cod_cli=${dbcod_cli} ORDER BY numero DESC LIMIT ${desp},3";
		$query = $this->db->query($mSQL);
		if($query->num_rows() > 0){
			foreach($query->result() as $row){
				$arr_numas[]=$row->numero;
			}
		}

		if(count($arr_numas)>0){
			$this->load->helper('date');
			$in  = '\''.implode('\',\'',$arr_numas).'\'';
			$mSQL= "SELECT numa,tipoa,codigoa,numa,desca,fecha,cana FROM sitems WHERE numa IN (${in}) ORDER BY numa DESC,desca";
			$query = $this->db->query($mSQL);

			if($query->num_rows() > 0){
				echo '<table><tr>';
				echo '<th>Fecha - D&iacute;as</th>';
				echo '<th>N&uacute;mero</th>';
				echo '<th>C&oacute;digo</th>';
				echo '<th>Descripci&oacute;n</th>';
				echo '</tr>';

				$datetime2 = new DateTime();
				foreach($query->result() as $row){
					$datetime1 = new DateTime($row->fecha);
					$interval  = $datetime1->diff($datetime2);

					if($row->tipoa=='D'){
						$cana=(-1)*$row->cana;
					}else{
						$cana=$row->cana;
					}
					$dias = $interval->format('%a');
					if($dias<=7){
						$color = '#FFA66F';
					}elseif($dias<=30){
						$color = '#FFFF4D';
					}else{
						$color = '#73F373';
					}

					echo '<tr style="background: '.$color.'; border-radius:25px;">';
					echo '<td>'.$datetime1->format('d/m/Y ').' - <b>'.$dias.'</b></td>';
					echo '<td>'.$row->numa.'</td>';
					echo '<td>'.$row->codigoa.'</td>';
					echo '<td>'.$row->desca.'</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
		}
	}


	function instalar(){
		$seniat=$this->db->escape('http://contribuyente.seniat.gob.ve/BuscaRif/BuscaRif.jsp');
		$mSQL  ="REPLACE INTO valores (nombre,valor,descrip) VALUES ('CONSULRIF',$seniat,'Pagina de consulta de rif del seniat')";
		$this->db->simple_query($mSQL);

		$campos = array();
		$fields = $this->db->field_data('scli');
		foreach ($fields as $field){
			if  ($field->name=='formap' && $field->type!='int')     $this->db->simple_query('ALTER TABLE `scli`  CHANGE COLUMN `formap` `formap` INT(6) NULL DEFAULT 0');
			elseif($field->name=='email'  && $field->max_length!=100) $this->db->simple_query('ALTER TABLE `scli`  CHANGE COLUMN `email` `email` VARCHAR(100) NULL DEFAULT NULL');
			elseif($field->name=='clave'  && $field->max_length!=50)  $this->db->simple_query('ALTER TABLE `scli`  CHANGE COLUMN `clave` `clave` VARCHAR(50) NULL DEFAULT NULL');
			$campos[]=$field->name;
		}


		if (!in_array('id',$campos)){
			$mSQL='ALTER TABLE `scli` DROP PRIMARY KEY, ADD UNIQUE `cliente` (`cliente`)';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE `scli` ADD `id` INT AUTO_INCREMENT PRIMARY KEY';
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('sclibitalimit')){
			$mSQL="CREATE TABLE `sclibitalimit` (
				id         INT(11) NOT NULL AUTO_INCREMENT,
				cliente    CHAR(5) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
				credito    CHAR(1) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
				creditoant CHAR(1) NULL DEFAULT NULL,
				limite     BIGINT(20) NULL DEFAULT NULL,
				limiteant  BIGINT(20) NULL DEFAULT NULL,
				tolera     DECIMAL(9,2) NULL DEFAULT NULL,
				toleraant  DECIMAL(9,2) NULL DEFAULT NULL,
				maxtol     DECIMAL(9,2) NULL DEFAULT NULL,
				maxtolant  DECIMAL(9,2) NULL DEFAULT NULL,
				motivo     TEXT NULL,
				formap     DECIMAL(9,0) NULL DEFAULT NULL,
				formapsant DECIMAL(9,0) NULL DEFAULT NULL,
				estampa    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
				usuario    VARCHAR(12) NULL DEFAULT NULL,
				PRIMARY KEY (id),
				INDEX cliente (cliente)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('creditoant', 'sclibitalimit')){
			$mSQL="ALTER TABLE `sclibitalimit`
			ADD COLUMN `creditoant` CHAR(1) NULL DEFAULT NULL AFTER `credito`,
			ADD COLUMN `toleraant` DECIMAL(9,2) NULL DEFAULT NULL AFTER `tolera`,
			ADD COLUMN `maxtolant` DECIMAL(9,2) NULL DEFAULT NULL AFTER `maxtol`,
			ADD COLUMN `formap` INT(6) NULL DEFAULT NULL AFTER `maxtol`,
			ADD COLUMN `formapsant` INT(6) NULL DEFAULT NULL AFTER `formap`";
			$this->db->simple_query($mSQL);
		}

		//if(!in_array('modifi'     ,$campos)) $this->db->query("ALTER TABLE scli ADD COLUMN modifi      TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL");
		if(!in_array('credito'    ,$campos)) $this->db->query("ALTER TABLE scli ADD COLUMN credito     CHAR(1) NOT NULL DEFAULT 'N' AFTER `limite`");
		if(!in_array('sucursal'   ,$campos)) $this->db->query("ALTER TABLE scli ADD COLUMN sucursal    CHAR(2) NULL DEFAULT NULL");
		if(!in_array('mmargen'    ,$campos)) $this->db->query("ALTER TABLE scli ADD COLUMN mmargen     DECIMAL(7,2) NULL DEFAULT 0 COMMENT 'Margen al Mayor'");
		if(!in_array('tolera'     ,$campos)) $this->db->query("ALTER TABLE scli ADD COLUMN tolera      DECIMAL(9,2) NULL DEFAULT '0'");
		if(!in_array('maxtole'    ,$campos)) $this->db->query("ALTER TABLE scli ADD COLUMN maxtole     DECIMAL(9,2) NULL DEFAULT '0'");
		if(!in_array('url'        ,$campos)) $this->db->query('ALTER TABLE scli ADD COLUMN url         VARCHAR(120) NULL');
		if(!in_array('pin'        ,$campos)) $this->db->query('ALTER TABLE scli ADD COLUMN pin         VARCHAR(10) NULL');
		if(!in_array('fb'         ,$campos)) $this->db->query('ALTER TABLE scli ADD COLUMN fb          VARCHAR(120) NULL');
		if(!in_array('twitter'    ,$campos)) $this->db->query('ALTER TABLE scli ADD COLUMN twitter     VARCHAR(120) NULL');
		if(!in_array('upago'      ,$campos)) $this->db->query('ALTER TABLE scli ADD COLUMN upago       VARCHAR(6) NULL');
		if(!in_array('tarifa'     ,$campos)) $this->db->query('ALTER TABLE scli ADD COLUMN tarifa      VARCHAR(15) NULL');
		if(!in_array('tarimonto'  ,$campos)) $this->db->query("ALTER TABLE scli ADD COLUMN tarimonto   FLOAT UNSIGNED NULL DEFAULT NULL COMMENT 'unidades tributarias a cobrar por servicio'");
		if(!in_array('canticipo'  ,$campos)) $this->db->query("ALTER TABLE scli ADD COLUMN canticipo   VARCHAR(15) NULL DEFAULT NULL COMMENT 'Cuenta contable de Anticipo'");
		if(!in_array('estado'     ,$campos)) $this->db->query("ALTER TABLE scli ADD COLUMN estado      INT(11) NULL DEFAULT 0 COMMENT 'Estados o Entidades'");
		if(!in_array('aniversario',$campos)) $this->db->query('ALTER TABLE scli ADD COLUMN aniversario DATE NULL DEFAULT NULL');

		if(!$this->db->table_exists('tarifa')){
			$mSQL="CREATE TABLE `tarifa` (
				`tipo` VARCHAR(1) NULL DEFAULT NULL,
				`actividad` VARCHAR(150) NULL DEFAULT NULL,
				`minimo` DECIMAL(10,3) NULL DEFAULT NULL,
				`maximo` DECIMAL(10,3) NULL DEFAULT NULL,
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('sclibitalimit')){
			$mSQL="CREATE TABLE `sclibitalimit` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`cliente`    CHAR(5) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
				`credito`    CHAR(1) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
				`creditoant` CHAR(1) NULL DEFAULT NULL,
				`limite`     BIGINT(20) NULL DEFAULT NULL,
				`limiteant`  BIGINT(20) NULL DEFAULT NULL,
				`tolera`     DECIMAL(9,2) NULL DEFAULT NULL,
				`toleraant`  DECIMAL(9,2) NULL DEFAULT NULL,
				`maxtol`     DECIMAL(9,2) NULL DEFAULT NULL,
				`maxtolant`  DECIMAL(9,2) NULL DEFAULT NULL,
				`motivo`     TEXT NULL,
				`formap`     DECIMAL(9,0) NULL DEFAULT NULL,
				`formapsant` DECIMAL(9,0) NULL DEFAULT NULL,
				`estampa`    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
				`usuario`    VARCHAR(12) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `cliente` (`cliente`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('ciud');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE `ciud` DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE `ciud` ADD UNIQUE INDEX `ciudad` (`ciudad`)');
			$this->db->simple_query('ALTER TABLE `ciud` ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

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

	}
}
