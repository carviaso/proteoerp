<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Sfac extends Controller {
	var $mModulo='SFAC';
	var $titp='Facturaci&oacute;n ';
	var $tits='Facturaci&oacute;n';
	var $url ='ventas/sfac/';
	var $genesal  = true;
	var $_creanfac= false;

	function Sfac(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SFAC', 0, 'Facturacion' );
		$this->vnega  = trim(strtoupper($this->datasis->traevalor('VENTANEGATIVA')));
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 900, 650, 'ventas/sfac' );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	//Ventana principal de facturacion
	//
	function jqdatag(){
		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'fimprime',  'img'=>'assets/default/images/print.png','alt' => 'Reimprimir Documento','tema'=>'anexos', 'label'=>'Imprimir'));
		$grid->wbotonadd(array('id'=>'precierre','img'=>'images/dinero.png',              'alt' => 'Cierre de Caja',      'tema'=>'anexos', 'label'=>'Cierre de Caja'));
		$grid->wbotonadd(array('id'=>'fmanual',  'img'=>'images/mano.png',                'alt' => 'Factura Manual',      'tema'=>'anexos', 'label'=>'Factura Manual'));
		$grid->wbotonadd(array('id'=>'bdevolu',  'img'=>'images/dinero.png',              'alt' => 'Devolver Factura',    'tema'=>'anexos', 'label'=>'Devolver'));
		$grid->wbotonadd(array('id'=>'nccob',    'img'=>'images/check.png', 'alt' => 'Nota de credito a factura pagada', 'label'=>'NC a Factura Cobrada'));

		$fiscal=$this->datasis->traevalor('IMPFISCAL','Indica si se usa o no impresoras fiscales, esto activa opcion para cierre X y Z');
		if($fiscal=='S'){
			$WpAdic = "<tr><td>
				<div class=\"anexos\">
					<table cellpadding='0' cellspacing='0'>
						<tr>
							<td style='vertical-align:top;'><div class='botones'><a style='width:94px;text-align:left;vertical-align:top;' href='#' id='bcierrex'>".img(array('src'=>'assets/default/images/print.png', 'height'=>15, 'alt'=>'Realizar cierre X', 'title'=>'Cierre X', 'border'=>'0'))." Cierre X</a></div></td>
							<td style='vertical-align:top;'><div class='botones'><a style='width:94px;text-align:left;vertical-align:top;' href='#' id='bcierrez'>".img(array('src'=>'assets/default/images/print.png', 'height'=>15, 'alt'=>'Realizar cierre Z', 'title'=>'Cierre Z', 'border'=>'0'))." Cierre Z</a></div></td>
						</tr>
					</table>
				</div>
			</td></tr>";
			$grid->setWpAdicional($WpAdic);
		}


		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita' , 'title'=>'Agregar Factura Fecha '.date('d/m/Y')),
			array('id'=>'scliexp', 'title'=>'Ficha de Cliente' ),
			array('id'=>'fshow'  , 'title'=>'Mostrar registro' ),
			array('id'=>'fborra' , 'title'=>'Anula Factura'    ),
			array('id'=>'fncob'  , 'title'=>'NC a factura cobrada')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);


		$funciones = '
		function ltransac(el, val, opts){
			var link=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return link;
		};';

		$param['WestPanel']    = $WestPanel;
		//$param['script']       = script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('SFAC', 'JQ');
		$param['otros']        = $this->datasis->otros('SFAC', 'JQ');
		$param['centerpanel']  = $centerpanel;
		$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	// Ventana principal de facturacion de servicios
	//
	function jqmes(){
		$mModulo='SFAC';

		$grid = $this->defgrid( false, 'false' );
		$grid->setAdd(false);
		#Set url
		$grid->setUrlput(site_url($this->url.'setdatam/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatam/'));
		$grid->setTitle("Facturacion de Servicio Mensual");

		//$grid->params['editable'] = 'true';

		$param['grids'][] = $grid->deploy();
		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'cobroser' ,'img' =>'images/agrega4.png',              'alt' => 'Cobro de Servicio','label'=>'Cobro de Servicio'));
		$grid->wbotonadd(array('id'=>'imptxt'   ,'img' =>'assets/default/images/print.png', 'alt' => 'Imprimir Servicio','label'=>'Imprimir Factura'));
		$grid->wbotonadd(array('id'=>'precierre','img' =>'images/dinero.png',               'alt' => 'Cierre de Caja'   ,'label'=>'Cierre de Caja'));
		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fcobroser', 'title'=>'Cobro de servicio'),
			array('id'=>'fimpser'  , 'title'=>'Imprimir Factura' ),
			array('id'=>'fborra'   , 'title'=>'Anula Factura'    ),
			array('id'=>'scliexp'  , 'title'=>'Ficha de Cliente' )
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function ltransac(el, val, opts){
			var link=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return link;
		};';

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		//$param['listados']     = $this->datasis->listados('SFAC', 'JQ');
		//$param['otros']        = $this->datasis->otros('SFAC', 'JQ');
		$param['centerpanel']  = $centerpanel;
		$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = 'Cobro de Servicio';
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );

		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript($grid0, $grid1){
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= '
		function tconsulta(transac){
			if (transac)	{
				window.open(\''.site_url('contabilidad/casi/localizador/transac/procesar').'/\'+transac, \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Transacci&oacute;n invalida</h1>");
			}
		};';

		$bodyscript .= $this->jqdatagrid->bsshow('sfac', $ngrid, $this->url );

		$bodyscript .= '
		function sfacadd(){
			$.post(xurl+"/N/create",
			function(data){
				$("#fimpser").html("");
				$("#fedita").dialog({ title:"Agregar Factura Fecha '.date('d/m/Y').'" });
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
				$("#cod_cli").focus();
			})
		};';


		$bodyscript .= '
		function sfacedit() {
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("'.$ngrid.'").getRowData(id);
				if(ret.referen=="P"){
					$.post(xurl+"/"+ret.manual+"/modify/"+id, function(data){
						$("#fborra").html("");
						$("#fimpser").html("");
						$("#fedita").html(data);
						$("#fedita").dialog("open");
						$("#cod_cli").focus();
					});
				}else{
					$.prompt("<h1>Solo se pueden modificar las facturas pendientes</h1>");
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';


		$bodyscript .= '
		function sfacdel() {
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea anular la Factura o Devolucion?")){
					var ret    = $("'.$ngrid.'").getRowData(id);
					mId = id;
					$.post(xurl+"/do_delete/"+id, function(data){
						$("#fedita").html("");
						$("#fimpser").html("");
						try{
							var json = JSON.parse(data);
							if(json.status == "A"){
								jQuery("'.$ngrid.'").trigger("reloadGrid");
								return true;
							}else{
								apprise(json.mensaje);
							}
						}catch(e){
							$("#fborra").html(data);
							$("#fborra").dialog("open");
							jQuery("'.$ngrid.'").trigger("reloadGrid");
						}
						jQuery("'.$ngrid.'").trigger("reloadGrid");
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';


		$bodyscript .= '
		$("#nccob").click( function(){
			var id  = $("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			var id = $("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("'.$ngrid.'").getRowData(id);
				if(ret.numero.substr(0,1)!="_" && ret.tipo_doc=="F"){
					$.post("'.site_url('finanzas/smov/ncfac').'/"+ret.numero+"/create",
						function(data){
							$("#fncob").html(data);
							$("#fncob").dialog("open");
						}
					);
				}else{
					$.prompt("<h1>No puede realizar esta operaci&oacute;n con el documento seleccionado</h1>");
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';


		$bodyscript .= '$(function() { ';

		$bodyscript .= '
		$("#bdevolu").click( function() {
			var id = jQuery("'. $ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("'.$ngrid.'").getRowData(id);
				if(ret.numero.substr(0,1)=="_"){
					alert("Debe seleccionar una factura.");
					return false;
				}

				if(ret.tipo_doc!="F"){
					alert("Debe seleccionar una factura.");
					return false;
				}
				$.post(xurl+"/N/create",
				function(data){
					$("#fimpser").html("");
					$("#fedita").dialog({ title:"Agregar Devolucion Fecha '.date('d/m/Y').'" });
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
					$("#factura").val(ret.numero);
					$("#tipo_doc").val("D");
					itdevolver(ret.numero);
				});
			}
		});';

		$bodyscript .= '
			$("#fmanual").click( function() {
				$.post(xurl+"/S/create",
				function(data){
					$("#fimpser").html("");
					$("#fedita").html(data);
					$("#fedita").dialog({ title:"Agregar Factura ******** MANUAL ********" });
					$("#fedita").dialog( "open" );
				})
			});';

		// Para imprimir despacho desde aqui
		$ndespa = $this->datasis->dameval('SELECT COUNT(*) FROM formatos WHERE nombre="NDESPACHO" AND proteo IS NOT NULL');
		if ( $ndespa == 0)
			$bodyscript .= '
			$("#fimprime").click( function(){
				var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					var ret = jQuery("'.$ngrid.'").jqGrid(\'getRowData\',id);
					window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
				} else {
					$.prompt("<h1>Por favor Seleccione una Factura</h1>");
				}
			});';
		else
			$bodyscript .= '
			$("#fimprime").click( function(){
				var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id){
					var ret = jQuery("'.$ngrid.'").jqGrid(\'getRowData\',id);
					$.prompt("<h1>Imprimir Documento</h1>Cliente: <b>"+ret.nombre+"</b><br>Factura Nro: <b>"+ret.numero+"</b><br><br> ",{
					buttons: { Factura: 1, Despacho: 2, Salir: 0},
					submit: function(e,v,m,f){
						if ( v == 1 ){
							window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
						} else if( v == 2 ){
							window.open(\''.site_url('formatos/ver/NDESPACHO').'/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes\');
						}
					}
					});
				} else {
					$.prompt("<h1>Por favor Seleccione una Factura</h1>");
				}
			});';



		$bodyscript .= '
			$("#boton2").click( function(){
				window.open(\''.site_url('ventas/sfac/dataedit/create').'\', \'_blank\', \'width=900,height=700,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			});';

		$fiscal=$this->datasis->traevalor('IMPFISCAL','Indica si se usa o no impresoras fiscales, esto activa opcion para cierre X y Z');
		if($fiscal=='S'){
			$bodyscript .= '
			$("#bcierrex").click( function(){
				window.open(\''.site_url('formatos/descargartxt/CIERREX').'\', \'_blank\', \'width=300,height=300,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			});';

			$bodyscript .= '
			$("#bcierrez").click( function(){
				window.open(\''.site_url('formatos/descargartxt/CIERREZ').'\', \'_blank\', \'width=300,height=300,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			});';
		}

		//Precierre
		$bodyscript .= '
			$("#precierre").click( function(){
				//$.prompt("<h1>Seguro que desea hacer cierre?</h1>")
				window.open(\''.site_url('ventas/rcaj/precierre/99/').'/'.$this->secu->getcajero().'\', \'_blank\', \'width=900,height=700,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-350)\');
			});';

		//Prepara Pago o Abono
		$bodyscript .= '
			$("#cobroser").click(function() {
				$.post("'.site_url('ventas/sfac/fcobroser').'", function(data){
					$("#fcobroser").html(data);
				});
				$( "#fcobroser" ).dialog( "open" );
			});';

		$bodyscript .= '
			$("#imptxt").click(function(){
				var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					$.post("'.site_url('ventas/sfac/dataprintser/modify').'/"+id, function(data){
						$("#fimpser").html(data);
					});
					$("#fimpser").dialog( "open" );
				}else{
					$.prompt("<h1>Por favor Seleccione un Registro</h1>");
				}
			});';

		$bodyscript .= '
			$("#fimpser").dialog({
				autoOpen: false, height: 420, width: 400, modal: true,
				buttons: {
					"Guardar": function() {
						var bValid = true;
						var murl = $("#df1").attr("action");
						$.ajax({
							type: "POST",
							dataType: "html",
							async: false,
							url: murl,
							data: $("#df1").serialize(),
							success: function(r,s,x){
								try{
									var json = JSON.parse(r);
									if (json.status == "A"){
										$("#fimpser").dialog( "close" );
										jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
										return true;
									} else {
										apprise(json.mensaje);
									}
								}catch(e){
									$("#fimpser").html(r);
								}
							}
						})},
					"Imprimir": function() {
							var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
							location.href="'.site_url('formatos/descargartxt/FACTSER').'/"+id;
					},
					"Cancelar": function() {
						$("#fimpser").html("");
						$( this ).dialog( "close" );
					}
				},
				close: function() {
					$("#fimpser").html("");
				}
			});';

		$bodyscript .= '
			$("#fshow").dialog({
				autoOpen: false, height: 550, width: 870, modal: true,
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
						$( this ).dialog( "close" );
					},
				},
				close: function() {
					$("#fborra").html("");
				}
			});';

		$sfacforma=$this->datasis->traevalor('FORMATOSFAC');
		if(empty($sfacforma)) $sfacforma='descargar';
		//Agregar Factura
		$bodyscript .= '
			$("#fedita").dialog({
				autoOpen: false, height: 550, width: 870, modal: true,
				buttons: {
					"Guardar": function() {
						if($("#scliexp").dialog( "isOpen" )===true) {
							$("#scliexp").dialog("close");
						}

						if($("#df1").length > 0){
							var bValid = true;
							var murl = $("#df1").attr("action");
							limpiavacio();
							$.ajax({
								type: "POST",
								dataType: "html",
								async: false,
								url: murl,
								data: $("#df1").serialize(),
								success: function(r,s,x){
									try {
										var json = JSON.parse(r);
										if(json.status == "A" ) {
											if(json.manual == "N"){
												$("#fedita").dialog("close");
												window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
												jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
												return true;
											}else{
												//$( "#fedita" ).dialog( "close" );
												$.post("'.site_url($this->url.'dataedit/S/create').'",
												function(data){
													$("#fedita").html(data);
												})
												//alert("Factura guardada");
												window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
												jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
												return true;
											}
										} else {
											apprise(json.mensaje);
										}
									} catch(e) {
										$("#fedita").html(r);
									}
								}
							});
						}
					},
					"Guardar y Seguir": function() {
						if($("#scliexp").dialog( "isOpen" )===true) {
							$("#scliexp").dialog("close");
						}

						if($("#df1").length > 0){
							var murl = $("#df1").attr("action");
							limpiavacio();
							$.ajax({
								type: "POST",
								dataType: "html",
								async: false,
								url: murl,
								data: $("#df1").serialize(),
								success: function(r,s,x){
									try {
										var json = JSON.parse(r);
										if(json.status == "A" ) {
											if(json.manual == "N"){
												$.post(xurl+"/create/"+idactual,function(data){$("#fedita").html(data);});
												window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
												jQuery("'.$ngrid.'").trigger("reloadGrid");
												return true;
											}else{
												$.post("'.site_url($this->url.'dataedit/S/create').'",
												function(data){
													$("#fedita").html(data);
												})
												//alert("Factura guardada");
												window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
												return true;
											}
										} else {
											apprise(json.mensaje);
										}
									} catch(e) {
										$("#fedita").html(r);
									}
								}
							});
						}
					},
					"Cancelar": function() {
						$("#fedita").html("");
						$( this ).dialog( "close" );
						$("#newapi'.$grid0.'").trigger("reloadGrid");
						if($("#scliexp").dialog( "isOpen" )===true) {
							$("#scliexp").dialog("close");
						}
					}
				},
				close: function() {
					$("#fedita").html("");
					if($("#scliexp").dialog( "isOpen" )===true) {
						$("#scliexp").dialog("close");
					}
				}
			});';


		$bodyscript .= '
			$("#fcobroser" ).dialog({
				autoOpen: false, height: 430, width: 540, modal: true,
				buttons: {
					"Guardar": function() {
						$.post("'.site_url('ventas/mensualidad/servxmes/insert').'", { cod_cli: $("#fcliente").val(),cana_0: $("#fmespaga").val(),tipo_0: $("#fcodigo").val(),num_ref_0: $("#fcomprob").val(),preca_0: $("#ftarifa").val(),fnombre: $("#fnombre").val(),utribu: $("#utribu").val()},
							function(data) {
								if( data.substr(0,14) == "Venta Guardada"){
									$("#fcobroser").dialog( "close" );
									jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
									//apprise(data);
									$("#fcobroser").html("");
									$.post("'.site_url('ventas/sfac/dataprintser/modify').'/"+data.substr(15,10), function(data){
										$("#fimpser").html(data);
									});
									$("#fimpser").dialog( "open" );
									return true;
								}else{
									apprise("<div style=\"font-size:16px;font-weight:bold;background:red;color:white\">Error:</div> <h1>"+data);
								}
							}
						);
					},
					Cancel: function() {
						$("#fcobroser").html("");
						$( this ).dialog( "close" );
					}
				},
				close: function() {
					$("#fcobroser").html("");
				}
			});';

		$bodyscript .= '
			$("#fncob").dialog({
				autoOpen: false, height: 350, width: 500, modal: true,
				buttons: {
					"Guardar": function(){
						var bValid = true;
						var murl = $("#df1").attr("action");
						$.ajax({
							type: "POST",
							dataType: "html",
							async: false,
							url: murl,
							data: $("#df1").serialize(),
							success: function(r,s,x){
								try{
									var json = JSON.parse(r);
									if(json.status == "A"){
										$("#fncob").dialog("close");
										//jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
										window.open(\''.site_url('finanzas/smov/smovprint').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
										return true;
									}else{
										apprise(json.mensaje);
									}
								}catch(e){
									$("#fncob").html(r);
								}
							}
						})
					},
					"Cancelar": function() {
						$("#fncob").html("");
						$( this ).dialog( "close" );
					}
				},
				close: function(){
					$("#fncob").html("");
				}
			});';

		$bodyscript .= '
		$("#scliexp").dialog({
			autoOpen:false, modal:true, width:500, height:350,
			buttons: {
				"Guardar": function(){
					var murl = $("#sclidialog").attr("action");
					$.ajax({
						type: "POST", dataType: "json", async: false,
						url: murl,
						data: $("#sclidialog").serialize(),
						success: function(r,s,x){
							if(r.status=="B"){
								$("#sclidialog").find(".alert").html(r.mensaje);
							}else{
								$("#scliexp").dialog( "close" );

								$("#cod_cli").val(r.data.cliente);

								$("#nombre").val(r.data.nombre);
								$("#nombre_val").text(r.data.nombre);

								$("#rifci").val(r.data.rifci);
								$("#rifci_val").text(r.data.rifci);

								$("#sclitipo").val(r.data.tipo);

								$("#direc").val(r.data.direc);
								$("#direc_val").text(r.data.direc);

								$("#descuento").val("0");
								return true;
							}
						}
					});

				},
				"Cancelar": function(){
					$("#scliexp").html("");
					$(this).dialog("close");
				}
			},
			close: function(){
				$("#scliexp").html("");
			}
		});';

		$bodyscript .= '});';
		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false, $xmes = 'true' ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('tipo_doc');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 1 }',
			'cellattr'      => 'function(rowId, tv, aData, cm, rdata){
				var tips = "";
				if(aData.numero !== undefined){
					if(aData.tipo_doc=="X"){
						tips = "Factura Anulada";
					}else if(aData.numero.substr(0, 1) == "_"){
						tips = "Factura Pendiente";
					}else{
						tips = "Factura Guardada";
					}
				}
				return \'title="\'+tips+\'"\';
			}'
		));

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 65,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 75,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }',
			//'searchoptions' => "{ sopt:['eq','ne','le','lt','gt','ge']}"
		));

		$grid->addField('vence');
		$grid->label('Vence');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 75,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$mSQL = "SELECT vendedor, concat( vendedor, ' ',TRIM(nombre)) nombre FROM vend ORDER BY nombre ";
		$avende  = $this->datasis->llenajqselect($mSQL, true );

		$grid->addField('vd');
		$grid->label('Vendedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 50,
			'edittype'      => "'select'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ value: '.$avende.',  style:"width:200px"}',
			'stype'         => "'text'",
		));

		$grid->addField('cod_cli');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('rifci');
		$grid->label('RIF/CI');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 13 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 170,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));

		$grid->addField('referen');
		$grid->label('Ref.');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
			'cellattr'      => 'function(rowId, tv, aData, cm, rdata){
				var tips = "";
				if(aData.referen !== undefined){
					if(aData.referen=="P"){
						tips = "Pendiente";
					}else if(aData.referen=="E"){
						tips = "Contado en Efectivo";
					}else if(aData.referen=="M"){
						tips = "Mixto";
					}else{
						tips = aData.referen;
					}
				}
				return \'title="\'+tips+\'"\';
			}'
		));

		$grid->addField('totals');
		$grid->label('Sub Total');
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

		$grid->addField('iva');
		$grid->label('I.V.A.');
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

		$grid->addField('totalg');
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

		$grid->addField('bultos');
		$grid->label('Bultos');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('orden');
		$grid->label('Orden');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 10 }',
		));


		$grid->addField('inicial');
		$grid->label('Inicial');
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



		$grid->addField('status');
		$grid->label('Estatus');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));


		$grid->addField('devolu');
		$grid->label('Devoluci&oacute;n');
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


		$grid->addField('cajero');
		$grid->label('Cajero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('almacen');
		$grid->label('Almac&eacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));

		$grid->addField('montasa');
		$grid->label('Base G.');
		$grid->params(array(
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


		$grid->addField('monredu');
		$grid->label('Base R.');
		$grid->params(array(
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


		$grid->addField('monadic');
		$grid->label('Base A.');
		$grid->params(array(
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


		$grid->addField('tasa');
		$grid->label('Impuesto G.');
		$grid->params(array(
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


		$grid->addField('reducida');
		$grid->label('Impuesto R.');
		$grid->params(array(
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


		$grid->addField('sobretasa');
		$grid->label('Impuesto A.');
		$grid->params(array(
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


		$grid->addField('exento');
		$grid->label('Exento');
		$grid->params(array(
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

		$grid->addField('peso');
		$grid->label('Peso');
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


		$grid->addField('factura');
		$grid->label('Factura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
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
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('transac');
		$grid->label('Transacci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
			'formatter'     => 'ltransac'
		));


		$grid->addField('nfiscal');
		$grid->label('No.Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:15, maxlength: 12 }',
		));


		$grid->addField('entregado');
		$grid->label('Entregado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 75,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:false, date:true}',
			'formoptions'   => '{ label:"Fecha de Entrega" }'
		));


		$grid->addField('zona');
		$grid->label('Zona');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));


		$grid->addField('ciudad');
		$grid->label('Ciudad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));

		$grid->addField('comiadi');
		$grid->label('Bono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('comision');
		$grid->label('Comisi&oacute;n');
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


		$grid->addField('pagada');
		$grid->label('Pagada');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('manual');
		$grid->label('Manual');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

		$grid->addField('modificado');
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


		$grid->addField('sepago');
		$grid->label('Sepago');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));


		$grid->addField('dias');
		$grid->label('D&iacute;as');
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

		$grid->addField('maqfiscal');
		$grid->label('Maq.Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:15, maxlength: 20 }',
		));


		$grid->addField('dmaqfiscal');
		$grid->label('Devolu.M.Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:15, maxlength: 20 }',
		));


		$grid->addField('observa');
		$grid->label('Observaci&oacute;n 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 50 }',
			'formoptions'   => '{ label:"Observacion 1" }'
		));

		$grid->addField('observ1');
		$grid->label('Observaci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $xmes,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:30, maxlength: 50 }',
			'formoptions'   => '{ label:"Observacion 2" }'
		));

		$grid->addField('maestra');
		$grid->label('Maestra');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
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
		$grid->setHeight('165');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					//jQuery(gridId2).setGridParam({datatype: "json"});
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
					$.ajax({
						url: "'.base_url().$this->url.'tabla/"+id,
						success: function(msg){
							$("#ladicional").html(msg);
						}
					});
				}
			}
		');

		$grid->setAfterInsertRow('
			function( rid, aData, rowe){
				if(aData.numero !== undefined){
					if(aData.tipo_doc == "X"){
						$(this).jqGrid( "setCell", rid, "tipo_doc","", {color:"#FFFFFF", background:"#C90623" });
					}else if(aData.numero.substr(0, 1) == "_"){
						$(this).jqGrid( "setCell", rid, "tipo_doc","", {color:"#FFFFFF", background:"#FFDD00" });
					}
				}
			}
		');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 450, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 450, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons

		//$grid->setEdit(  true);
		$grid->setEdit(  $this->datasis->sidapuede('SFAC','MODIFICA%'));
		$grid->setAdd(   $this->datasis->sidapuede('SFAC','INCLUIR%' ));
		$grid->setDelete($this->datasis->sidapuede('SFAC','BORR_REG%'));
		$grid->setSearch($this->datasis->sidapuede('SFAC','BUSQUEDA%'));

		$grid->setRowNum(30);
		$grid->setBarOptions('addfunc: sfacadd, editfunc: sfacedit, delfunc: sfacdel, viewfunc: sfacshow');
		$grid->setShrinkToFit('false');

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
	//Busca la data en el Servidor por json
	function getdata(){
		$grid = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('sfac');

		$response   = $grid->getData('sfac', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	//Busca la data en el Servidor por json
	function getdatam(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('sfac');
		$mWHERE[] = array('', 'fecha', date('Ymd'), '' );
		$mWHERE[] = array('', 'usuario', $this->session->userdata('usuario'),'');

		$response   = $grid->getData('sfac', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	//Guarda la Informacion
	function setData(){
		$oper   = $this->input->post('oper');
		$id     = intval($this->input->post('id'));
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($id>0){
			if($oper == 'edit') {
				if(empty($data['entregado'])) unset($data['entregado']);

				$posibles=array('entregado','bultos','nfiscal','maqfiscal','comiadi','observa','observ1','dmaqfiscal','vd');
				foreach($data as $ind=>$val){
					if(!in_array($ind,$posibles)){
						echo 'Campo no permitido ('.$ind.')';
						return false;
					}
				}

				$row = $this->datasis->damerow("SELECT tipo_doc, numero,vd,transac, cod_cli AS cliente,fecha FROM sfac WHERE id=${id}");
				if(empty($row)){
					echo 'Registro no encontrado';
					return false;
				}

				$this->db->where('id', $id);
				$this->db->update('sfac', $data);

				if($row['vd']!=$data['vd']){
					$this->db->where('id_sfac', $id);
					$this->db->update('sitems', array('vendedor'=>$data['vd']));

					$this->db->where('numero'   , $row['numero']);
					$this->db->where('cod_cli'  , $row['cliente']);
					$this->db->where('transac'  , $row['transac']);
					$this->db->update('smov', array('vendedor'=>$data['vd']));

					$this->db->where('numero'   , $row['numero']);
					$this->db->where('f_factura', $row['fecha']);
					$this->db->where('cod_cli'  , $row['cliente']);
					$this->db->where('transac'  , $row['transac']);
					$this->db->update('sfpa', array('vendedor'=>$data['vd']));
				}

				$numero=$row['numero'];
				logusu('sfac',"Factura ${numero} ${id} MODIFICADO");
				echo 'Registro Modificado';

			} elseif($oper == 'del') {
				echo 'Deshabilitado';
			}
		}
	}

	//******************************************************************
	//Guarda la Informacion
	function setDatam(){
		echo 'Deshabilitado';
	}

	//******************************************************************
	//Definicion del Grid y la Forma
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigoa');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));


		$grid->addField('desca');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));


		$grid->addField('cana');
		$grid->label('Cantidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('preca');
		$grid->label('Precio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 85,
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
			'width'         => 90,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		//$grid->addField('fecha');
		//$grid->label('Fecha');
		//$grid->params(array(
		//	'search'        => 'true',
		//	'editable'      => $editar,
		//	'width'         => 70,
		//	'align'         => "'center'",
		//	'edittype'      => "'text'",
		//	'editrules'     => '{ required:true,date:true}',
		//	'formoptions'   => '{ label:"Fecha" }'
		//));


		//$grid->addField('vendedor');
		//$grid->label('Vendedor');
		//$grid->params(array(
		//	'search'        => 'true',
		//	'editable'      => $editar,
		//	'width'         => 50,
		//	'edittype'      => "'text'",
		//	'editrules'     => '{ required:true}',
		//	'editoptions'   => '{ size:30, maxlength: 5 }',
		//));


		$grid->addField('costo');
		$grid->label('Costo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('utilidad');
		$grid->label('Utilidad');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('porcen');
		$grid->label('Margen%');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 77,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('iva');
		$grid->label('IVA');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 40,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		//$grid->addField('comision');
		//$grid->label('Comisi&oacute;n');
		//$grid->params(array(
		//	'search'        => 'true',
		//	'editable'      => $editar,
		//	'align'         => "'right'",
		//	'edittype'      => "'text'",
		//	'width'         => 100,
		//	'editrules'     => '{ required:true }',
		//	'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
		//	'formatter'     => "'number'",
		//	'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		//));


		//$grid->addField('cajero');
		//$grid->label('Cajero');
		//$grid->params(array(
		//	'search'        => 'true',
		//	'editable'      => $editar,
		//	'width'         => 50,
		//	'edittype'      => "'text'",
		//	'editrules'     => '{ required:true}',
		//	'editoptions'   => '{ size:30, maxlength: 5 }',
		//));


		$grid->addField('despacha');
		$grid->label('Despacha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));


		$grid->addField('pvp');
		$grid->label('Precio 1');
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

		$grid->addField('precio4');
		$grid->label('Precio 4');
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


		$grid->addField('detalle');
		$grid->label('Detalle');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('fdespacha');
		$grid->label('F.Despacho');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('udespacha');
		$grid->label('U.Despacho');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('combo');
		$grid->label('Combo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
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
		$grid->setHeight('190');
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(false);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');
		$grid->setOndblClickRow('');

		//$grid->footerrow=true;
		//$grid->setGridComplete('
		//	function(){
		//		//var cana = $(this).jqGrid("getGridParam", "records")+1;
		//		//$(this).jqGrid("addRowData", cana, {codigoa:"Total:",desca:"",cana:"",costo:"averr"});
        //
		//		var totalutil = $(this).jqGrid("getCol", "utilidad", false, "sum");
		//		var totalsub  = $(this).jqGrid("getCol", "tota"    , false, "sum");
		//		var totalcosto= $(this).jqGrid("getCol", "costo"   , false, "sum");
		//		//	grid.jqGrid("footerData", "set", { DriverEn: "Total FTE:", FTEValue: sum });
		//		$(this).jqGrid("footerData", "set", { codigoa: "TOTALES:",tota:totalsub,costo:totalcosto,utilidad: totalutil});
		//	}'
		//);

		//$grid->setGridComplete('
		//	function(){
		//		//alert($(this).getGridParam("datatype"));
		//		$(this).setGridParam({datatype: "local"});
		//	}
		//');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdatait/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatait/'));

		if($deployed){
			return $grid->deploy();
		}else{
			return $grid;
		}
	}

	//******************************************************************
	//Busca la data en el Servidor por json
	function getdatait(){
		$id = $this->uri->segment(4);
		if($id === false){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM sfac");
		}
		if(empty($id)) return '';
		$dbid     = $this->db->escape($id);
		$row      = $this->datasis->damerow("SELECT tipo_doc,numero FROM sfac WHERE id=${dbid}");
		if(empty($row)){
			return null;
		}

		$tipo_doc = $row['tipo_doc'];
		$numero   = $row['numero'];
		$dbtipo_doc = $this->db->escape($tipo_doc);
		$dbnumero   = $this->db->escape($numero);

		$orderby= '';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos   = $this->db->list_fields('sitems');
			$campos[] = 'utilidad';
			$campos[] = 'porcen';
			if(in_array($sidx,$campos)){
				$sidx = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT *,(preca-costo)*cana AS utilidad,((preca*100/costo)-100) AS porcen FROM sitems WHERE tipoa=${dbtipo_doc} AND numa=${dbnumero} ${orderby}";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	//Guarda la Informacion
	function setdatait(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;
	}

	//******************************************************************
	//Forma de Cobro de Servicio
	function fcobroser(){
		$mSQL    = "SELECT tipo, CONCAT(tipo, ' ', nombre) descrip FROM tarjeta WHERE tipo NOT IN ('DE','NC','IR') ORDER BY tipo ";
		$tarjeta = $this->datasis->llenaopciones($mSQL, true, 'fcodigo');
		$ivas=$this->datasis->ivaplica();

		$salida = '
		<script type="text/javascript">

		var totaliza = function (){
			var taritipo = $("#taritipo").val();
			var iva = 1;
			if(taritipo=="C"){
				iva = 1+'.($ivas['tasa']/100).';
			}
			var meses = Number($("#fmespaga").val());
			var monto = Number($("#ftarifa").val());
			var pagado= Number($("#pagado").val());
			var total = meses*monto*iva;
			var vuelto= 0;

			if(pagado>total) vuelto = pagado-total;
			$("#fmonto").val(nformat(total,2));
			$("#montotot").text(nformat(total,2));
			$("#vuelto").text(nformat(vuelto,2));
		}

		var mespaga = function (){
		$.post("'.site_url('ventas/mensualidad/tarifa').'",{cliente: $("#fcliente").val() , cana: $("#fmespaga").val()  }, function(data) {
			var monto  = roundNumber(Number(data),2);
			var cana   = $("#fmespaga").val();
			var utribu = $("#utribu").val();
			$("#ftarifa").val(roundNumber(monto*utribu,2));
			totaliza();
		});

		}

		$("#fmespaga").keyup(mespaga);
		$("#pagado").keyup(totaliza);
		$(".inputonlynum").numeric();

		$("#fcliente").autocomplete({
			source: function( req, add){
				$.ajax({
					url:  "'.site_url('ajax/buscascliser').'",
					type: "POST",
					dataType: "json",
					data: {"q":req.term},
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$("#fcliente").val("");
								$("#fnombre").val("");
								$("#fdire11").val("");
								$("#ftelefono").val("");
								$("#ftarifa").val("");
								$("#fupago").val("");
								$("#utribu").val("0");
								$("#utribu_val").text("0,000");
								$("#taritipo").val("");
								$("#fmespaga").val("12");
								apprise("Cliente inexistente");
							}else{
								$.each(data,
									function(i, val){
										sugiere.push( val );
									}
								);
							}
							add(sugiere);
							totaliza();
						},
					})
				},
				minLength: 2,
				select: function( event, ui ){
					$("#fcliente").attr("readonly", "readonly");

					$("#fcliente").val(ui.item.value);
					$("#fnombre").val(ui.item.nombre);
					$("#ftelefono").val(ui.item.telefono);
					$("#ftarifa").val(ui.item.precio1);
					$("#fcodtar").val(ui.item.codigo);
					$("#fdire11").val(ui.item.direc);
					$("#fupago").val(ui.item.upago);
					$("#taritipo").val(ui.item.taritipo);
					$("#utribu").val(ui.item.utribu);
					$("#utribu_val").text(nformat(ui.item.utribu,3));
					$("#fmespaga").val(ui.item.cana);
					mespaga();
					setTimeout(function() {  $("#fcliente").removeAttr("readonly"); }, 1500);
				}
			});
		</script>

		<div style="background-color:#D0D0D0;font-weight:bold;font-size:14px;text-align:center"><table width="100%"><tr><td>Cobro de Servicios Mensuales</td><td></td><td> </td></tr></table></div>
		<p class="validateTips"></p>
		<form id="formcobroser">
		<fieldset style="border: 2px outset #9AC8DA;background: #FFFDE9;">
		<table width="90%" align="center" border="0">
		<tr>
			<td class="CaptionTD" align="right">Cliente:</td>
			<td>&nbsp;<input name="fcliente" id="fcliente" type="text" value="" maxlength="12" size="12" />
			<a href="'.site_url('ventas/scli/dataeditexpresser/create').'" target="_blank" onClick="window.open(this.href, this.target, \'width=500,height=550,screenx=\'+((screen.availWidth/2)-250)+\',screeny=\'+((screen.availHeight/2)-200)); return false;">'.image('add1-.png').'</a>
			</td>
			<td class="CaptionTD" align="right">Tel&eacute;fono: </td>
			<td>&nbsp;<input name="ftelefono" id="ftelefono" type="text" value="" maxlength="12" size="12" /></td>
		</tr>
		<tr>
			<td class="CaptionTD" align="right">Nombre: </td>
			<td colspan="3">&nbsp;<input name="fnombre" id="fnombre" value="" size="50" ></td>
		</tr>
		<tr>
			<td class="CaptionTD" align="right">Direcci&oacute;n: </td>
			<td colspan="3">&nbsp;<input name="fdire11" id="fdire11" value="" size="50"></td>
		</tr>
		<tr>
			<td class="CaptionTD" align="right">&nbsp;</td>
			<td colspan="3">&nbsp;<input name="fdire12" id="fdire12" value="" size="50"></td>
		</tr>
		</table>

		</fieldset>
		<fieldset style="border: 2px outset #9AC8DA;background: #FFFDE9;">
		<table width="90%" align="center" border="0">
		<tr>
			<td class="CaptionTD" align="right">&Uacute;ltimo Pago: </td>
			<td>&nbsp;<input name="fupago" id="fupago" type="text" value="201112" maxlength="6" size="9" class="inputonlynum" /></td>
			<td  class="CaptionTD"  align="right">Unidades Trub.</td>
			<td>&nbsp;<b id="utribu_val">0,000</b><input type="hidden" name="utribu"  id="utribu"  value="0" />
				<input type="hidden" name="fcodtar"  id="fcodtar"  value="" />
				<input type="hidden" name="taritipo" id="taritipo" value="" />
			</td>
			<td  class="CaptionTD"  align="right">Monto</td>
			<td>&nbsp;<input name="ftarifa" id="ftarifa" type="text" value="" maxlength="12" size="12"  class="inputonlynum" /></td>
		</tr>
		</table>
		</fieldset>

		</fieldset>
		<fieldset style="border: 2px outset #9AC8DA;background: #FFFDE9;">
		<table width="90%" align="center" border="0">
		<tr>
			<td class="CaptionTD" align="right">Nro de meses que paga: </td>
			<td>&nbsp;<input name="fmespaga" id="fmespaga" type="text" value="12" maxlength="12" size="8"  class="inputonlynum" /></td>
		</tr>
		</table>
		</fieldset>

		<fieldset style="border: 2px outset #9AC8DA;background: #FFFDE9;">
		<table width="90%" align="center" border="0">
		<tr>
			<td class="CaptionTD" align="right">Forma de Pago</td>
			<td>&nbsp;'.$tarjeta.'</td>
			<td  class="CaptionTD"  align="right">N&uacute;mero</td>
			<td>&nbsp;<input name="fcomprob" id="fcomprob" type="text" value="" maxlength="12" size="12" /></td>
		</tr>
		<tr>
			<td align="right">Paga con:</td>
			<td ><input name="pagado" id="pagado" type="text" value="" maxlength="12" size="12"  class="inputonlynum" /></td>
			<td colspan="2" align="center"><div style="font-size:12px;font-weight:bold">Vuelto: <span id="vuelto">0,00</span></div></td>
		</tr>


		</tr>
		</table>
		</fieldset>

		<input id="fmonto"   name="fmonto"   type="hidden">
		<input id="fsele"    name="fsele"    type="hidden">
		<input id="fid"      name="fid"      type="hidden" value="">
		<input id="fgrid"    name="fgrid"    type="hidden">
		<br>
		<center><table id="abonados"><table></center>
		<table width="100%">
		<tr>
			<td align="center"><div id="grantotal" style="font-size:20px;font-weight:bold">Monto a pagar: <span id="montotot">0,00</span></div></td>
		</tr>
		</table>
		</form>';
		echo $salida;
	}

	//******************************************************************
	//Json para llena la tabla de inventario
	function sfacsitems() {
		$numa    = $this->uri->segment($this->uri->total_segments());
		$tipoa   = $this->uri->segment($this->uri->total_segments()-1);
		$dbnuma  = $this->db->escape($numa);
		$dbtipoa = $this->db->escape($tipoa );


		$mSQL  = 'SELECT a.codigoa, a.desca, a.cana, a.preca, a.tota, a.iva, IF(a.pvp < a.preca, a.preca, a.pvp)  pvp, ROUND(100-a.preca*100/IF(a.pvp<a.preca,a.preca, a.pvp),2) descuento, ROUND(100-ROUND(a.precio4*100/(100+a.iva),2)*100/a.preca,2) precio4, a.detalle, a.fdespacha, a.udespacha, a.bonifica, b.id url ';
		$mSQL .= "FROM sitems a LEFT JOIN sinv b ON a.codigoa=b.codigo WHERE a.tipoa=${dbtipoa} AND a.numa=${dbnuma} ";
		$mSQL .= "ORDER BY a.codigoa";

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			$retArray = array();
			foreach( $query->result_array() as  $row ) {
				$retArray[] = $row;
			}
			$data = json_encode($retArray);
			$ret = "{data:" . $data .",\n";
			$ret .= "recordType : 'array'}";
		} else {
			$ret = '{data : []}';
		}
		echo $ret;
	}

	//******************************************************************
	//Recibir retencion de IVA
	function sfacreiva(){
		$reinte = $this->uri->segment($this->uri->total_segments());
		$efecha = $this->uri->segment($this->uri->total_segments()-1);
		$fecha  = $this->uri->segment($this->uri->total_segments()-2);
		$numero = $this->uri->segment($this->uri->total_segments()-3);
		$id     = intval($this->uri->segment($this->uri->total_segments()-4));
		$mdevo  = 'Exito';

		//memowrite("efecha=$efecha, fecha=$fecha, numero=$numero, id=$id, reinte=$reinte","sfacreiva");

		// status de la factura
		$fecha  = substr($fecha, 6,4).substr($fecha, 3,2).substr($fecha, 0,2);
		$efecha = substr($efecha,6,4).substr($efecha,3,2).substr($efecha,0,2);

		$tipo_doc = $this->datasis->dameval("SELECT tipo_doc FROM sfac WHERE id=${id}");
		$referen  = $this->datasis->dameval("SELECT referen  FROM sfac WHERE id=${id}");
		$numfac   = $this->datasis->dameval("SELECT numero   FROM sfac WHERE id=${id}");
		$cod_cli  = $this->datasis->dameval("SELECT cod_cli  FROM sfac WHERE id=${id}");
		$monto    = $this->datasis->dameval("SELECT ROUND(iva*0.75,2)  FROM sfac WHERE id=${id}");
		$factura  = $this->datasis->dameval("SELECT factura  FROM sfac WHERE id=${id}");

		$anterior = $this->datasis->dameval("SELECT reiva FROM sfac WHERE id=${id}");
		$usuario = addslashes($this->session->userdata('usuario'));

		if(strlen($numero) == 14){
			if($anterior == 0){
				$mSQL = "UPDATE sfac SET reiva=round(iva*0.75,2), creiva='${numero}', freiva='${fecha}', ereiva='${efecha}' WHERE id=${id}";
				$this->db->simple_query($mSQL);
				//memowrite($mSQL,"sfacreivaSFAC");

				$transac = $this->datasis->prox_sql('ntransa');
				$transac = str_pad($transac, 8, "0", STR_PAD_LEFT);

				if ($referen == 'C') {
					$saldo =  $this->datasis->dameval("SELECT monto-abonos FROM smov WHERE tipo_doc='FC' AND numero='${numfac}'");
				}

				if ( $tipo_doc == 'F') {
					if ($referen == 'E') {
						// FACTURA PAGADA AL CONTADO GENERA ANTICIPO
						$mnumant = $this->datasis->prox_sql("nancli");
						$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);

						$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
						SELECT cod_cli, nombre, 'AN' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
							CONCAT('RET/IVA DE ',cod_cli,' A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
							curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
						FROM sfac WHERE id=${id}";
						$this->db->simple_query($mSQL);
						$mdevo = "<h1 style='color:green;'>EXITO</h1>Retenci&oacute;n Guardada, Anticipo Generado por factura pagada al contado";
					} elseif ($referen == 'C') {
						// Busca si esta cancelada
						$tiposfac = 'FC';
						if ( $tipo_doc == 'D') $tiposfac = 'NC';
						$mSQL = "SELECT monto-abonos saldo FROM smov WHERE numero='${numfac}' AND cod_cli='${cod_cli}' AND tipo_doc='${tiposfac}'";
						$saldo = $this->datasis->dameval($mSQL);
						if ( $saldo < $monto ) {  // crea anticipo
							$mnumant = $this->datasis->prox_sql("nancli");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
							SELECT cod_cli, nombre, 'AN' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
								CONCAT('APLICACION DE RETENCION A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
								curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
							FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);
							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Anticipo Generado por factura ya pagada";
							memowrite($mSQL,"sfacreivaAN");
						} else {
							$mnumant = $this->datasis->prox_sql("nccli");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, codigo, descrip, nroriva, emiriva )
								SELECT cod_cli, nombre, 'NC' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, reiva abonos, freiva vence,
								CONCAT('APLICACION DE RETENCION A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
								curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario,
								'NOCON 'codigo, 'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
								FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);

							// ABONA A LA FACTURA
							$mSQL = "UPDATE smov SET abonos=abonos+$monto WHERE numero='${numfac}' AND cod_cli='${cod_cli}' AND tipo_doc='$tiposfac'";
							$this->db->simple_query($mSQL);

							//Crea la relacion en ccli
							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Nota de Credito generada y aplicada a la factura";
						}
					}
					$mnumant = $this->datasis->prox_sql("ndcli");
					$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
					$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, usuario, transac, codigo, descrip, nroriva, emiriva )
						SELECT 'REIVA' cod_cli, 'RETENCION DE I.V.A. POR COMPENSAR' nombre, 'ND' tipo_doc, '$mnumant' numero, freiva fecha,
						reiva monto, 0 impuesto, 0 abonos, freiva vence, CONCAT('RET/IVA DE ',cod_cli,' A ',tipo_doc,numero) observa1,
						IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref, curdate() estampa,
						curtime() hora, '".$usuario."' usuario, '$transac' transac, 'NOCON 'codigo,
						'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
					FROM sfac WHERE id=$id";
					$this->db->simple_query($mSQL);
					memowrite($mSQL,"sfacreivaND");
				} else {
					// DEVOLUCIONES GENERA ND AL CLIENTE
					$mnumant = $this->datasis->prox_sql("ndcli");
					$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);

					$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
					SELECT cod_cli, nombre, 'ND' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
						CONCAT('RET/IVA DE ',cod_cli,' A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
						CURDATE() estampa, CURTIME() hora, '${transac}' transac, '".$usuario."' usuario, creiva, ereiva
					FROM sfac WHERE id=${id}";
					$this->db->simple_query($mSQL);
					$mdevo = "<h1 style='color:green;'>EXITO</h1>Retenci&oacute;n Guardada, Anticipo Generado por factura pagada al contado";

					// Debe abonar la ND si existe un AN
					/*
					if ($referen == 'E') {
						// DEVOLUCIONES PAGADA AL CONTADO GENERA
						$mnumant = $this->datasis->prox_sql("ndcli");
						$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);

						$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
						SELECT cod_cli, nombre, 'ND' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
							CONCAT('RET/IVA DE ',cod_cli,' A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
							curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
						FROM sfac WHERE id=$id";
						$this->db->simple_query($mSQL);
						$mdevo = "<h1 style='color:green;'>EXITO</h1>Retencion Guardada, Anticipo Generado por factura pagada al contado";
					} elseif ($referen == 'C') {
						// B
						$tiposfac = 'FC';
						if ( $tipo_doc == 'D') $tiposfac = 'NC';
						$mSQL = "SELECT monto-abonos saldo FROM smov WHERE numero='$numfac' AND cod_cli='$cod_cli' AND tipo_doc='$tiposfac'";
						$saldo = $this->datasis->dameval($mSQL);
						if ( $saldo < $monto ) {  // crea anticipo
							$mnumant = $this->datasis->prox_sql("nancli");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO smov  (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, nroriva, emiriva )
							SELECT cod_cli, nombre, 'AN' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, freiva vence,
								CONCAT('APLICACION DE RETENCION A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
								curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario, creiva, ereiva
							FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);
							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Anticipo Generado por factura ya pagada";
							memowrite($mSQL,"sfacreivaAN");
						} else {
							$mnumant = $this->datasis->prox_sql("nccli");
							$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
							$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, transac, usuario, codigo, descrip, nroriva, emiriva )
								SELECT cod_cli, nombre, 'NC' tipo_doc, '$mnumant' numero, freiva fecha, reiva monto, 0 impuesto, reiva abonos, freiva vence,
								CONCAT('APLICACION DE RETENCION A DOC. ',tipo_doc,numero) observa1, IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref,
								curdate() estampa, curtime() hora, '$transac' transac, '".$usuario."' usuario,
								'NOCON 'codigo, 'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
								FROM sfac WHERE id=$id";
							$this->db->simple_query($mSQL);

							// ABONA A LA FACTURA
							$mSQL = "UPDATE smov SET abonos=abonos+$monto WHERE numero='$numfac' AND cod_cli='$cod_cli' AND tipo_doc='$tiposfac'";
								$this->db->simple_query($mSQL);

							//Crea la relacion en ccli

							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Nota de Credito generada y aplicada a la factura";
						}
					}*/

					//Devoluciones debe crear un NC si esta en el periodo
					$mnumant = $this->datasis->prox_sql("nccli");
					$mnumant = str_pad($mnumant, 8, "0", STR_PAD_LEFT);
					$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, usuario, transac, codigo, descrip, nroriva, emiriva )
						SELECT 'REIVA' cod_cli, 'RETENCION DE I.V.A. POR COMPENSAR' nombre, 'NC' tipo_doc, '$mnumant' numero, freiva fecha,
						reiva monto, 0 impuesto, 0 abonos, freiva vence, CONCAT('RET/IVA DE ',cod_cli,' A ',tipo_doc,numero) observa1,
						IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref, curdate() estampa,
						curtime() hora, '".$usuario."' usuario, '$transac' transac, 'NOCON 'codigo,
						'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
					FROM sfac WHERE id=$id";
					$this->db->simple_query($mSQL);
					memowrite($mSQL,"sfacreivaND");

				}
			}else{
				$mdevo = "<h1 style='color:red;'>ERROR</h1>Retenci&oacute;n ya aplicada";
			}
		}else
			$mdevo = "<h1 style='color:red;'>ERROR</h1>Longitud del comprobante menor a 14 caracteres, corrijalo y vuelva a intentar";
		echo $mdevo;
	}

	//******************************************************************
	// Reintegrar retencion de IVA
	function sfacreivaef(){
		$id     = intval($this->uri->segment($this->uri->total_segments()));
		$reinte = 0;
		$numero = rawurldecode($this->input->post('numero'));
		$fecha  = rawurldecode($this->input->post('fecha'));
		$efecha = rawurldecode($this->input->post('efecha'));
		$caja   = rawurldecode($this->input->post('caja'));
		$cheque = rawurldecode($this->input->post('cheque'));
		$benefi = rawurldecode($this->input->post('benefi'));

		$mdevo  = 'Exito';

		//memowrite("efecha=${efecha}, fecha=${fecha}, numero=${numero}, id=${id}, caja=${caja}, cheque=${cheque}, benefi=${benefi}",'sfacreivaef');

		// status de la factura
		$fecha  = substr($fecha, 6,4).substr($fecha, 3,2).substr($fecha, 0,2);
		$efecha = substr($efecha,6,4).substr($efecha,3,2).substr($efecha,0,2);

		$tipo_doc = $this->datasis->dameval("SELECT tipo_doc FROM sfac WHERE id=${id}");
		$referen  = $this->datasis->dameval("SELECT referen  FROM sfac WHERE id=${id}");
		$numfac   = $this->datasis->dameval("SELECT numero   FROM sfac WHERE id=${id}");
		$cod_cli  = $this->datasis->dameval("SELECT cod_cli  FROM sfac WHERE id=${id}");
		$monto    = $this->datasis->dameval("SELECT ROUND(iva*0.75,2)  FROM sfac WHERE id=${id}");
		$anterior = $this->datasis->dameval("SELECT reiva FROM sfac WHERE id=${id}");

		$usuario  = addslashes($this->session->userdata('usuario'));
		$codbanc  = substr($caja,0,2);

		$dbcodbanc= $this->db->escape($codbanc);
		$dbcheque = $this->db->escape($cheque);
		$dbfecha  = $this->db->escape($fecha);
		$dbnumero = $this->db->escape($numero);
		$dbefecha = $this->db->escape($efecha);
		$dbcaja   = $this->db->escape($caja  );
		$dbcheque = $this->db->escape($cheque);
		$dbbenefi = $this->db->escape($benefi);
		$dbnumfac = $this->db->escape($numfac);

		$verla = 0;

		if ($codbanc == '__') {
			$tbanco  = '';
			$cheque  = '';
		} else {
			$tbanco  = $this->datasis->dameval("SELECT tbanco FROM banc WHERE codbanc=${dbcodbanc}");
			$cheque  = str_pad($cheque, 12, '0', STR_PAD_LEFT);
			$query   = "SELECT COUNT(*) AS cana FROM bmov WHERE tipo_op='CH' AND codbanc=${dbcodbanc} AND numero=${dbcheque}";
			if($tbanco != 'CAJ'){
				$verla = $this->datasis->dameval($query);
			}
		}

		if($verla == 0){
			if ( strlen($numero) == 14 ){
				if($anterior == 0){
					$mSQL = "UPDATE sfac SET reiva=ROUND(iva*0.75,2), creiva=${dbnumero}, freiva=${dbfecha}, ereiva=${dbefecha} WHERE id=${id}";
					$this->db->simple_query($mSQL);
					//memowrite($mSQL,"sfacreivaSFAC");

					$transac = $this->datasis->prox_sql('ntransa');
					$transac = str_pad($transac, 8, "0", STR_PAD_LEFT);

					if($codbanc == '__'){  // manda a cxp
						if ( $tipo_doc == 'F' ) {
							// crea un registro en sprm
							$this->db->simple_query($mSQL);
							$mnumant  = $this->datasis->prox_sql('num_nd');
							$mnumant  = str_pad($mnumant, 8, '0', STR_PAD_LEFT);
							$dbobserva= $this->db->escape("REINTEGRO POR RETENCION A DOCUMENTO ${numfac}");
							$mSQL = "INSERT INTO sprm (cod_prv, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, usuario, transac, codigo, descrip )
								SELECT 'REINT' cod_prv, 'REINTEGRO A CLIENTE' nombre, 'ND' tipo_doc, '${mnumant}' numero, freiva fecha,
								reiva monto, 0 impuesto, 0 abonos, freiva vence, ${dbobserva} observa1,
									IF(tipo_doc='F','FC', 'DV') tipo_ref, numero num_ref, CURDATE() estampa,
								CURTIME() hora, '".$usuario."' usuario, '${transac}' transac, 'NOCON' codigo,
								'NOTA DE CONTABILIDAD' descrip
							FROM sfac WHERE id=${id}";
							$this->db->simple_query($mSQL);

							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Nota de Cr&eacute;dito generada y ND en CxP por Reintero (REINT) ";
						} else {
							//Devoluciones
						}


					}else{
						if($tbanco == 'CAJ'){
							$m = 1;
							while ( $m > 0 ) {
								$cheque = $this->datasis->prox_sql("ncaja${codbanc}");
								$cheque = str_pad($cheque, 12, '0', STR_PAD_LEFT);
								$m = $this->datasis->dameval("SELECT COUNT(*) AS cana FROM bmov WHERE codbanc=${dbcodbanc} AND tipo_op='ND' AND numero=${dbcheque}");
							}
						}

						$negreso = $this->datasis->prox_sql('negreso');
						$negreso = str_pad($negreso, 8, '0', STR_PAD_LEFT);

						$saldo = 0;
						if ($referen == 'C') {
							$saldo =  $this->datasis->dameval("SELECT monto-abonos FROM smov WHERE tipo_doc='FC' AND numero=${dbnumfac}");
						}
						if ( $tipo_doc == 'F' ) {
							// crea un registro en bmov
							$dbconcepto="REINTEGRO DE RETENCION APLICADA A FC ${numfac}";
							$mSQL  = "INSERT INTO bmov ( codbanc, moneda, numcuent, banco, saldo, tipo_op, numero,fecha, clipro, codcp, nombre, monto, concepto, benefi, posdata, liable, transac, usuario, estampa, hora, negreso ) ";
							$mSQL .= "SELECT ${dbcodbanc} codbanc, b.moneda, b.numcuent, ";
							$mSQL .= "b.banco, b.saldo, IF(b.tbanco='CAJ','ND','CH') tipo_op, ${dbcheque} numero, ";
							$mSQL .= "a.freiva, 'C' clipro, a.cod_cli codcp, a.nombre, a.reiva monto, ";
							$mSQL .= "${dbconcepto} concepto, ";
							$mSQL .= "${dbbenefi} benefi, a.freiva posdata, 'S' liable, '${transac}' transac, ";
							$mSQL .= "'${usuario}' usuario, CURDATE() estampa, CURTIME() hora, '${negreso}' negreso ";
							$mSQL .= "FROM sfac a JOIN banc b ON b.codbanc=${dbcodbanc}";
							$mSQL .= "WHERE a.id=${id}";
							$this->db->simple_query($mSQL);

							$mdevo = "<h1 style='color:green;'>EXITO</h1>Cambios Guardados, Nota de Cr&eacute;dito generada y cargo en caja generado";
						} else {
							//Devoluciones
						}
					}
					if($tipo_doc == 'F'){
						$this->db->simple_query($mSQL);
						$mnumant  = $this->datasis->prox_sql('ndcli');
						$mnumant  = str_pad($mnumant, 8, '0', STR_PAD_LEFT);
						$dbobserva= $this->db->escape("APLICACION DE RETENCION A DOCUMENTO ${numfac}");
						$mSQL = "INSERT INTO smov (cod_cli, nombre, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, tipo_ref, num_ref, estampa, hora, usuario, transac, codigo, descrip, nroriva, emiriva )
							SELECT 'REIVA' cod_cli, 'RETENCION DE IVA POR COMPENSAR' nombre, 'ND' tipo_doc, '${mnumant}' numero, freiva fecha,
							reiva monto, 0 impuesto, 0 abonos, freiva vence, ${dbobserva} observa1,
								IF(tipo_doc='F','FC', 'DV' ) tipo_ref, numero num_ref, CURDATE() estampa,
							CURTIME() hora, '".$usuario."' usuario, '${transac}' transac, 'NOCON' codigo,
							'NOTA DE CONTABILIDAD' descrip, creiva, ereiva
						FROM sfac WHERE id=${id}";
						$this->db->simple_query($mSQL);
					} else {
						//Devoluciones
					}
				}else{
					$mdevo = "<h1 style='color:red;'>ERROR</h1>Retenci&oacute;n ya aplicada";
				}
			}else{
				$mdevo = "<h1 style='color:red;'>ERROR</h1>Longitud del comprobante menor a 14 caracteres, corrijalo y vuelva a intentar";
			}
		}else{
			$mdevo = "<h1 style='color:red;'>ERROR</h1>Un cheque con ese n&uacute;mero ya existe (${cheque}) ";
		}
		echo $mdevo;
	}

	//******************************************************************
	// json para llena la tabla de inventario
	function sfacsig() {
		$numa    = $this->uri->segment($this->uri->total_segments());
		$tipoa   = $this->uri->segment($this->uri->total_segments()-1);
		$dbnuma  = $this->db->escape($numa);
        $dbtipoa = $this->db->escape($tipoa);

		$mSQL  = 'SELECT a.codigoa, a.desca, a.cana, a.preca, a.tota, a.iva, IF(a.pvp < a.preca, a.preca, a.pvp)  pvp, ROUND(100-a.preca*100/IF(a.pvp<a.preca,a.preca, a.pvp),2) descuento, ROUND(100-ROUND(a.precio4*100/(100+a.iva),2)*100/a.preca,2) precio4, a.detalle, a.fdespacha, a.udespacha, a.bonifica, b.id url ';
		$mSQL .= "FROM sitems a LEFT JOIN sinv b ON a.codigoa=b.codigo WHERE a.tipoa=${dbtipoa} AND a.numa=${dbnuma} ";
		$mSQL .= "ORDER BY a.codigoa";

		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			$retArray = array();
			foreach( $query->result_array() as  $row ) {
				$retArray[] = $row;
			}
			$data = json_encode($retArray);
			$ret = "{data:" . $data .",\n";
			$ret .= "recordType : 'array'}";
		} else {
			$ret = '{data : []}';
		}
		echo $ret;
	}

	function creadpfacf($numero){
		$this->rapyd->load('dataform');

		$form = new DataForm('ventas/sfac/creadpfac/'.$numero);
		$form->title('Sellecione el Almac&eacute;n');

		$form->alma = new dropdownField('Almac&eacute;n', 'alma');
		$form->alma->options("SELECT ubica,ubides FROM caub WHERE invfis='N' AND gasto='N'");

		$form->submit('btnsubmit','Facturar');
		$form->build_form();

		$data['content'] = $form->output;
		$data['title']   = heading('Convertir Pedido en Factura');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function creadpfac($numero){
		$alma    =$this->input->post('alma');
		$numeroe =$this->db->escape($numero);
		$user    =$this->session->userdata('usuario');
		$nsfac   =$this->datasis->fprox_numero('nsfac');
		$transac =$this->datasis->fprox_numero('transac');
		$almae   =$this->db->escape($alma);

		/*CREA ENCABEZADO DE LA FACTURA SFAC*/
		$query="
		INSERT INTO sfac (`tipo_doc`,`numero`,`fecha`,`vence`,`vd`,`cod_cli`,`rifci`,`nombre`,`direc`,`dire1`,`referen`,`iva`,`inicial`,`totals`,`totalg`,`observa`,`observ1`,`cajero`,`almacen`,`peso`,`pedido`,`usuario`,`estampa`,`hora`,`transac`,`zona`,`ciudad`,`comision`,`exento`,`tasa`,`reducida`,`sobretasa`,`montasa`,`monredu`,`monadic`)
		SELECT 'F','$nsfac',a.fecha,DATE_ADD(a.fecha, INTERVAL (SELECT b.formap FROM scli b WHERE b.cliente=a.cod_cli) DAY) vence,
		a.vd,a.cod_cli,a.rifci,a.nombre,a.direc,a.dire1,'C' referen,a.iva,0 inicial,a.totals,a.totalg,a.observa,a.observ1,
		a.cajero,$almae,a.peso,a.numero,'$user',now() estampa,CURTIME() hora,'$transac',a.zona,a.ciudad,0,SUM(d.tota)*(d.iva=0) exento,
		ROUND(SUM(d.tota*(SELECT tasa FROM civa e ORDER BY fecha desc LIMIT 1)/100)*(d.iva=(SELECT tasa FROM civa e ORDER BY fecha desc LIMIT 1))) tasa,
		ROUND(SUM(d.tota*(SELECT redutasa FROM civa e ORDER BY fecha desc LIMIT 1)/100)*(d.iva=(SELECT redutasa FROM civa e ORDER BY fecha desc LIMIT 1))) redutasa,
		ROUND(SUM(d.tota*(SELECT sobretasa FROM civa e ORDER BY fecha desc LIMIT 1)/100)*(d.iva=(SELECT sobretasa FROM civa e ORDER BY fecha desc LIMIT 1))) sobretasa,
		ROUND(SUM(d.tota)*(d.iva=(SELECT tasa FROM civa e ORDER BY fecha desc LIMIT 1))) montasa,
		ROUND(SUM(d.tota)*(d.iva=(SELECT redutasa FROM civa e ORDER BY fecha desc LIMIT 1))) monredu,
		ROUND(SUM(d.tota)*(d.iva=(SELECT sobretasa FROM civa e ORDER BY fecha desc LIMIT 1))) monadic
		FROM pfac a
		JOIN itpfac d ON a.numero=d.numa
		WHERE a.numero=${numeroe}";

		$this->db->query($query);
		$id_sfac=$this->db->insert_id();

		/*CREA ENCABEZADO DE LA FACTURA SFAC*/
		$query="
		INSERT INTO sitems (`tipoa`,`numa`,`codigoa`,`desca`,`cana`,`preca`,`tota`,`iva`,`fecha`,`vendedor`,`costo`,`pvp`,`cajero`,`mostrado`,`usuario`,`estampa`,`hora`,`transac`,`precio4`,`id_sfac`)
		SELECT 'F','$nsfac',d.codigoa,d.desca,d.cana,d.preca,d.tota,d.iva,CURDATE(),d.vendedor,d.costo,d.pvp,
		d.cajero,d.mostrado,'$user' usuario,NOW() estampa,CURTIME(),'$transac',c.precio4,$id_sfac idsfac
		FROM pfac a
		JOIN itpfac d ON a.numero=d.numa
		JOIN sinv c ON d.codigoa=c.codigo
		WHERE a.numero=${numeroe}";

		$this->db->query($query);

		$query="
		INSERT IGNORE INTO smov ( cod_cli, nombre, dire1, dire2, tipo_doc, numero, fecha, monto, impuesto, abonos, vence, observa1, estampa, usuario, hora, transac, tasa, montasa, reducida, monredu, sobretasa, monadic, exento )
		SELECT cod_cli, nombre, direc, dire1, tipo_doc, numero, fecha, totalg, iva,   0 abonos, vence,
		if(tipo_doc='D', 'DEVOLUCION EN VENTAS', 'FACTURA DE CREDITO' ) observa1, estampa, usuario, hora, transac, tasa, montasa, reducida, monredu, sobretasa, monadic, exento
		FROM sfac WHERE transac='$transac' AND referen='C'
		LIMIT 1";

		$this->db->query($query);

		$query="
		SELECT a.codigoa,b.almacen,-1*a.cana cana
		FROM sitems a
		JOIN sfac b ON a.id_sfac=b.id
		JOIN caub c ON b.almacen=c.ubica
		WHERE b.transac='$transac'
		";

		$query=$this->db->query($query);
		foreach($query->result as $row)
		$this->datasis->sinvcarga($row->codigoa,$row->almacen,$row->cana);

		redirect("ventas/sfac/dataedit/show/$id_sfac");
	}

	function tabla() {
		$id  = $this->uri->segment($this->uri->total_segments());
		$dbid= $this->db->escape($id);
		$row = $this->datasis->damerow("SELECT cod_cli,transac,referen,tipo_doc,numero,reparto,fecha FROM sfac WHERE id=${dbid}");
		if(!empty($row)){
			$cliente   = $row['cod_cli'];
			$transac   = $row['transac'];
			$referen   = $row['referen'];
			$tipo_doc  = $row['tipo_doc'].'C';
			$numero    = $row['numero'];
			$reparto   = $row['reparto'];
			$fecha     = $row['fecha'];
		}else{
			return false;
		}

		$dbcliente = $this->db->escape($cliente);
		$dbtransac = $this->db->escape($transac);
		$dbtipo_doc= $this->db->escape($tipo_doc);
		$dbnumero  = $this->db->escape($numero);
		$dbfecha   = $this->db->escape($fecha);

		$salida = '<br><table width=\'100%\' border=\'1\'>';
		$pago  = 0;
		$encab = false;
		//Revisa formas de pago sfpa
		$mSQL = "SELECT tipo, num_ref, monto FROM sfpa WHERE transac=${dbtransac} AND numero=${dbnumero} AND monto<>0";
		$query = $this->db->query($mSQL);
		if($query->num_rows() > 0){
			$encab = true;
			$salida .= '<tr bgcolor=\'#E7E3E7\'><td colspan=\'3\'>Forma de Pago</td></tr>';
			$salida .= '<tr bgcolor=\'#E7E3E7\'><td>Tipo</td><td align=\'center\'>N&uacute;mero</td><td align=\'center\'>Monto</td></tr>';
			foreach ($query->result_array() as $row){
				$salida .= '<tr>';
				$salida .= '<td>'.$row['tipo'].'</td>';
				$salida .= '<td>'.$row['num_ref'].'</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
				$pago += $row['monto'];
			}
		}

		$mSQL = "SELECT tipoccli AS tipo, numccli AS numero,abono AS monto,reteiva,reten,mora,ppago,cambio FROM itccli WHERE tipo_doc=${dbtipo_doc} AND numero=${dbnumero}";
		$qquery = $this->db->query($mSQL);
		if($qquery->num_rows() > 0){
			if(!$encab){
				$salida .= '<tr bgcolor=\'#E7E3E7\'><td colspan=\'3\'>Forma de Pago</td></tr>';
				$salida .= '<tr bgcolor=\'#E7E3E7\'><td>Tipo</td><td align=\'center\'>N&uacute;mero</td><td align=\'center\'>Monto</td></tr>';
			}
			foreach($qquery->result_array() as $row){
				$salida .= '<tr bgcolor=\'#D3D3FF\'>';
				$salida .= '<td>'.$row['tipo'].'</td>';
				$salida .= '<td>'.$row['numero'].'</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
				$pago += $row['monto'];
			}
		}
		if($pago>0){
			$salida .= '<tr bgcolor=\'#E7E3E7\'><td colspan=\'3\' align=\'right\'>Total: <b>'.nformat($pago).'</b></td></tr>';
		}
		$salida .= '</table>';

		//Cuentas por Cobrar
		$mSQL = "SELECT cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos FROM smov WHERE cod_cli=${dbcliente} AND abonos<>monto AND tipo_doc<>'AB' ORDER BY fecha DESC ";
		$query = $this->db->query($mSQL);
		$saldo = 0;
		if($query->num_rows() > 0){
			$salida .= '<br><table width=\'100%\' border=\'1\'>';
			$salida .= '<tr bgcolor=\'#E7E3E7\'><td colspan=\'3\'>Movimiento Pendientes en CxC</td></tr>';
			$salida .= '<tr bgcolor=\'#E7E3E7\'><td>Tp</td><td align=\'center\'>N&uacute;mero</td><td align=\'center\'>Monto</td></tr>';
			$i = 1;
			foreach($query->result_array() as $row){
				if($i < 6){
					$salida .= '<tr>';
					$salida .= '<td>'.$row['tipo_doc'].'</td>';
					$salida .= '<td>'.$row['numero'].  '</td>';
					$salida .= '<td align=\'right\'>'.nformat($row['monto']-$row['abonos']).'</td>';
					$salida .= '</tr>';
				}
				if($i == 6){
					$salida .= '<tr>';
					$salida .= '<td colspan=\'3\'>Mas......</td>';
					$salida .= '</tr>';
				}
				if($row['tipo_doc'] == 'FC' or $row['tipo_doc'] == 'ND' or $row['tipo_doc'] == 'GI'){
					$saldo += $row['monto']-$row['abonos'];
				}else{
					$saldo -= $row['monto']-$row['abonos'];
				}
				$i ++;
			}
			$salida .= '<tr bgcolor=\'#D7C3C7\'><td colspan=\'4\' align=\'center\'>Saldo : '.nformat($saldo).'</td></tr>';
			$salida .= '</table>';
		}
		$query->free_result();

		// Revisa movimiento de bancos
		$mSQL  = "SELECT codbanc, numero, monto FROM bmov WHERE transac=${dbtransac}";
		$query = $this->db->query($mSQL);
		if($query->num_rows() > 0){
			$salida .= '<br><table width=\'100%\' border=\'1\'>';
			$salida .= '<tr bgcolor=\'#e7e3e7\'><td colspan=\'3\'>Movimiento en Caja o Banco</td></tr>';
			$salida .= '<tr bgcolor=\'#e7e3e7\'><td>Bco.</td><td align=\'center\'>N&uacute;mero</td><td align=\'center\'>Monto</td></tr>';
			foreach ($query->result_array() as $row){
				$salida .= '<tr>';
				$salida .= '<td>'.$row['codbanc'].'</td>';
				$salida .= '<td>'.$row['numero']. '</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '</table>';
		}
		//Revisa las devoluciones
		$mSQL  = "SELECT numero, fecha, totalg FROM sfac WHERE factura=${dbnumero} AND tipo_doc='D'";
		$query = $this->db->query($mSQL);
		if($query->num_rows() > 0){
			$salida .= '<br><table width=\'100%\' border=\'1\'>';
			$salida .= '<tr bgcolor=\'#e7e3e7\'><td colspan=\'3\'>Devoluciones</td></tr>';
			$salida .= '<tr bgcolor=\'#e7e3e7\'><td>N&uacute;mero</td><td align=\'center\'>Fecha</td><td align=\'center\'>Peso</td></tr>';
			foreach ($query->result_array() as $row){
				$salida .= '<tr>';
				$salida .= '<td>'.$row['numero'].'</td>';
				$salida .= '<td>'.$row['fecha']. '</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['totalg']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '</table>';
		}


		// Revisa los despachos
		$mSQL  = "SELECT numero, fecha, peso FROM snot WHERE factura=${dbnumero}";
		$query = $this->db->query($mSQL);
		if($query->num_rows() > 0){
			$salida .= '<br><table width=\'100%\' border=\'1\'>';
			$salida .= '<tr bgcolor=\'#e7e3e7\'><td colspan=\'3\'>Despachos realizados</td></tr>';
			$salida .= '<tr bgcolor=\'#e7e3e7\'><td>N&uacute;mero</td><td align=\'center\'>Fecha</td><td align=\'center\'>Peso</td></tr>';
			foreach ($query->result_array() as $row){
				$salida .= '<tr>';
				$salida .= '<td>'.$row['numero'].'</td>';
				$salida .= '<td>'.$row['fecha']. '</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['peso']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '</table>';
		}

		// Revisa los repartos
		if(!empty($reparto)){
			$salida .= '<p style=\'text-align:center\'>Reparto: '.str_pad($reparto,8,'0',0).'</p>';
		}

		$row=$this->datasis->damerow("SELECT SUM(tipo_doc IN ('F','T')) AS fac,SUM(tipo_doc='D') AS dev,SUM(tipo_doc='X') AS anu FROM sfac WHERE fecha=${dbfecha} AND MID(numero,1,1)<>'_'");
		if(!empty($row)){
			$salida .= "<p style='text-align:center'>Transacciones del d&iacute;a ".$fecha.": Facturas <b>$row[fac]</b>, Devoluciones <b>$row[dev]</b>, Anuladas <b>$row[anu]</b></p>";
		}
		echo $salida;
	}

	function sclibu(){
		$numero   = $this->uri->segment(4);
		$dbnumero = $this->db->escape($numero);
		$id = $this->datasis->dameval("SELECT b.id FROM sfac a JOIN scli b ON a.cod_cli=b.cliente WHERE numero=${dbnumero}");
		redirect('ventas/scli/dataedit/show/'.$id);
	}

	//******************************************************************
	// Forma de facturacion
	//
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		if(isset($this->_creanfac) && $this->_creanfac){
			//Para hacer el corte por maxlin en las facturas pendientes
			$this->rapyd->uri->un_set('update');
			$this->rapyd->uri->set('insert');
		}

		$manual = $this->uri->segment(4);
		if($manual <> 'S') $manual = 'N';

		$do = new DataObject('sfac');
		$do->rel_one_to_many('sitems', 'sitems', array('id'=>'id_sfac'));
		$do->rel_one_to_many('sfpa'  , 'sfpa'  , array('numero','transac'));
		$do->pointer('scli' ,'scli.cliente=sfac.cod_cli','scli.tipo AS sclitipo','left');
		$do->rel_pointer('sitems','sinv','sitems.codigoa=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');

		$edit = new DataDetails('', $do);
		$edit->on_save_redirect=false;
		$edit->cid='df1';

		$edit->set_rel_title('sitems','Producto <#o#>');
		$edit->set_rel_title('sfpa'  ,'Forma de pago <#o#>');

		$edit->pre_process( 'insert', '_pre_insert' );
		$edit->pre_process( 'update', '_pre_update' );
		$edit->pre_process( 'delete', '_pre_delete' );
		$edit->post_process('insert', '_post_insert');
		$edit->post_process('update', '_post_update');
		$edit->post_process('delete', '_post_delete');

		$edit->sclitipo = new hiddenField('', 'sclitipo');
		$edit->sclitipo->db_name     = 'sclitipo';
		$edit->sclitipo->pointer     = true;
		$edit->sclitipo->insertValue = 1;

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->rule = 'required|chfecha';
		$edit->fecha->calendar = false;
		$edit->fecha->size = 12;
		if($manual <> 'S'){
			$edit->fecha->insertValue = date('Y-m-d');
			$edit->fecha->readonly    = true;
		}

		//$edit->tipo_doc = new  dropdownField('Documento', 'tipo_doc');
		$edit->tipo_doc = new  hiddenField('Documento', 'tipo_doc');
		$edit->tipo_doc->insertValue = 'F';

		$edit->manual = new hiddenField('Manual', 'manual');
		$edit->manual->insertValue = $manual;

		$edit->vd = new  dropdownField ('Vendedor', 'vd');
		$edit->vd->options('SELECT TRIM(vendedor) AS vendedor, CONCAT(vendedor,\' \',nombre) nombre FROM vend ORDER BY vendedor');
		$edit->vd->style='width:100px;';
		$edit->vd->insertValue=trim($this->secu->getvendedor());

		$alma = $this->secu->getalmacen();
		if(empty($alma)){
			$edit->almacen= new dropdownField ('Almac&eacute;n', 'almacen');
			$edit->almacen->options('SELECT ubica,ubides FROM caub WHERE gasto="N" ORDER BY ubides');
			$edit->almacen->rule='required';
			$edit->almacen->style='width:130px;';
			$alma = $this->datasis->traevalor('ALMACEN');
			$edit->almacen->insertValue=$alma;
		} else {
			$edit->almacen= new inputField ('Almac&eacute;n', 'almacen');
			$edit->almacen->readonly  = true;
			$edit->almacen->size      = 8;
			$edit->almacen->insertValue=$alma;
		}

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength=8;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->factura = new inputField('Factura', 'factura');
		$edit->factura->size = 12;
		$edit->factura->mode='autohide';
		$edit->factura->maxlength=8;
		$edit->factura->rule='condi_required|callback_chfactura';

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->readonly  = true;
		$edit->peso->size      = 10;

		$edit->cliente = new inputField('Cliente','cod_cli');
		$edit->cliente->size = 8;
		$edit->cliente->autocomplete=false;
		$edit->cliente->rule='required|trim|existescli';

		$edit->bultos = new hiddenField('Bultos', 'bultos');
		$edit->bultos->css_class = 'inputnum';
		$edit->bultos->size = 10;

		$edit->nombre = new hiddenField('Nombre', 'nombre');
		$edit->nombre->size = 25;
		$edit->nombre->maxlength=40;
		$edit->nombre->readonly =true;
		$edit->nombre->autocomplete=false;
		$edit->nombre->rule= 'required';

		$edit->upago = new hiddenField('Ultimo pago de servicio', 'upago');
		$edit->upago->readonly =true;
		$edit->upago->autocomplete=false;

		$edit->rifci   = new hiddenField('RIF/CI','rifci');
		$edit->rifci->autocomplete=false;
		$edit->rifci->readonly =true;
		$edit->rifci->size = 15;

		$edit->direc = new hiddenField('Direcci&oacute;n','direc');
		$edit->direc->readonly =true;
		$edit->direc->size = 40;

		$edit->cajero = new autoUpdateField('cajero' ,$this->secu->getcajero(), $this->secu->getcajero());


		$edit->descuento = new hiddenField('Desc.','descuento');
		$edit->descuento->insertValue = '0';

		$edit->descu1 = new inputField('Descuento 1', 'descu1');
		$edit->descu1->css_class   = 'inputnum';
		$edit->descu1->size        = 5;
		$edit->descu1->insertValue = '0';
		$edit->descu1->onkeyup     ='totalizar()';

		$edit->descu2 = new inputField('Descuento 2', 'descu2');
		$edit->descu2->css_class   = 'inputnum';
		$edit->descu2->size        = 5;
		$edit->descu2->insertValue = '0';
		$edit->descu2->onkeyup     ='totalizar()';

		$edit->descu3 = new inputField('Descuento 3', 'descu3');
		$edit->descu3->css_class   = 'inputnum';
		$edit->descu3->size        = 5;
		$edit->descu3->insertValue = '0';
		$edit->descu3->onkeyup     ='totalizar()';

		$edit->orden = new inputField ('Orden', 'orden');
		$edit->orden->size = 12;

		//***********************************
		//  Campos para el detalle 1 sitems
		// todo campo que se agrege aqui debe
		// tomarse en cuenta en el post_process
		// seccion maxlin ****
		//***********************************
		$edit->codigoa = new inputField('C&oacute;digo <#o#>', 'codigoa_<#i#>');
		$edit->codigoa->size     = 12;
		$edit->codigoa->db_name  = 'codigoa';
		$edit->codigoa->rel_id   = 'sitems';
		$edit->codigoa->rule     = 'required';

		$edit->lote = new inputField('Lote <#o#>', 'lote_<#i#>');
		$edit->lote->db_name  = 'lote';
		$edit->lote->css_class= 'inputnum';
		$edit->lote->rel_id   = 'sitems';
		$edit->lote->maxlength= 5;
		$edit->lote->size     = 4;
		$edit->lote->autocomplete=false;
		//$edit->lote->showformat ='decimal';
		$edit->lote->disable_paste=true;

		$edit->desca = new inputField('Descripci&oacute;n <#o#>', 'desca_<#i#>');
		$edit->desca->size=40;
		$edit->desca->db_name='desca';
		$edit->desca->maxlength=50;
		$edit->desca->readonly  = true;
		$edit->desca->rel_id='sitems';

		$edit->cana = new inputField('Cantidad <#o#>', 'cana_<#i#>');
		$edit->cana->db_name  = 'cana';
		$edit->cana->css_class= 'inputnum';
		$edit->cana->rel_id   = 'sitems';
		$edit->cana->maxlength= 10;
		$edit->cana->size     = 6;
		$edit->cana->rule     = 'required|positive|callback_chcanadev[<#i#>]|callback_chcananeg[<#i#>]';
		$edit->cana->autocomplete=false;
		$edit->cana->onkeyup  ='importe(<#i#>)';
		$edit->cana->showformat ='decimal';
		$edit->cana->disable_paste=true;

		$edit->preca = new inputField('Precio <#o#>', 'preca_<#i#>');
		$edit->preca->db_name   = 'preca';
		$edit->preca->css_class = 'inputnum';
		$edit->preca->rel_id    = 'sitems';
		$edit->preca->size      = 9;
		$edit->preca->rule      = 'required|positive|callback_chpreca[<#i#>]';
		$edit->preca->onkeyup   = 'post_precioselec(<#i#>,this);';
		$edit->preca->showformat ='decimal';

		$pidescu = $this->datasis->traevalor('SFACDESCU','ApLica Descuentos en facturacio');

		if ( $pidescu == 'S' ){
			$edit->descu = new inputField('Descuento <#o#>', 'descu_<#i#>');
			$edit->descu->db_name  = 'descu';
			$edit->descu->css_class= 'inputnum';
			$edit->descu->rel_id   = 'sitems';
			$edit->descu->maxlength= 6;
			$edit->descu->size     = 4;
			$edit->descu->autocomplete=false;
			$edit->descu->onkeyup  ='importe(<#i#>)';
			$edit->descu->showformat ='decimal';
			$edit->descu->disable_paste=true;
			$edit->descu->insertValue = 0;
		} else {
			$edit->descu = new hiddenField('Descuento <#o#>', 'descu_<#i#>');
			$edit->descu->db_name  = 'descu';
			$edit->descu->rel_id   = 'sitems';
		}


		$edit->detalle = new hiddenField('', 'detalle_<#i#>');
		$edit->detalle->db_name  = 'detalle';
		$edit->detalle->rel_id   = 'sitems';

		$edit->tota = new inputField('Importe <#o#>', 'tota_<#i#>');
		$edit->tota->db_name='tota';
		$edit->tota->type      ='inputhidden';
		$edit->tota->size=10;
		$edit->tota->css_class='inputnum';
		$edit->tota->rel_id   ='sitems';
		$edit->tota->showformat ='decimal';

		for($i=1;$i<4;$i++){
			$obj='precio'.$i;
			$edit->$obj = new hiddenField('Precio <#o#>', $obj.'_<#i#>');
			$edit->$obj->db_name   = 'sinv'.$obj;
			$edit->$obj->rel_id    = 'sitems';
			$edit->$obj->pointer   = true;
		}

		$edit->precio4 = new hiddenField('', 'precio4_<#i#>');
		$edit->precio4->db_name   = 'precio4';
		$edit->precio4->rel_id    = 'sitems';

		$edit->combo = new hiddenField('', 'combo_<#i#>');
		$edit->combo->db_name   = 'combo';
		$edit->combo->rel_id    = 'sitems';

		$edit->itiva = new hiddenField('', 'itiva_<#i#>');
		$edit->itiva->db_name  = 'iva';
		$edit->itiva->rel_id   = 'sitems';

		$edit->sinvpeso = new hiddenField('', 'sinvpeso_<#i#>');
		$edit->sinvpeso->db_name   = 'sinvpeso';
		$edit->sinvpeso->rel_id    = 'sitems';
		$edit->sinvpeso->pointer   = true;

		$edit->sinvtipo = new hiddenField('', 'sinvtipo_<#i#>');
		$edit->sinvtipo->db_name   = 'sinvtipo';
		$edit->sinvtipo->rel_id    = 'sitems';
		$edit->sinvtipo->pointer   = true;

		//************************************************
		//fin de campos para detalle,inicio detalle2 sfpa
		//************************************************
		$edit->tipo = new  dropdownField('Tipo <#o#>', 'tipo_<#i#>');
		$edit->tipo->option('','CREDITO');
		$edit->tipo->options('SELECT tipo, nombre FROM tarjeta WHERE activo=\'S\' ORDER BY nombre');
		$edit->tipo->db_name    = 'tipo';
		$edit->tipo->rel_id     = 'sfpa';
		$edit->tipo->insertValue= 'EF';
		$edit->tipo->style      = 'width:150px;';
		$edit->tipo->onchange   = 'sfpatipo(<#i#>)';
		//$edit->tipo->rule     = 'required';
/*
		$edit->sfpafecha = new dateonlyField('Fecha','sfpafecha_<#i#>');
		$edit->sfpafecha->rel_id   = 'sfpa';
		$edit->sfpafecha->db_name  = 'fecha';
		$edit->sfpafecha->size     = 10;
		$edit->sfpafecha->maxlength= 8;
		$edit->sfpafecha->calendar = false;
		$edit->sfpafecha->rule ='condi_required|callback_chtipo[<#i#>]';
*/
		$edit->numref = new inputField('Numero <#o#>', 'num_ref_<#i#>');
		$edit->numref->size     = 12;
		$edit->numref->db_name  = 'num_ref';
		$edit->numref->rel_id   = 'sfpa';
		$edit->numref->rule     = 'condi_required|callback_chtipo[<#i#>]';

		$edit->banco = new dropdownField('Banco <#o#>', 'banco_<#i#>');
		$edit->banco->option('','Ninguno');
		$edit->banco->options('SELECT cod_banc,nomb_banc
		FROM tban WHERE cod_banc<>\'CAJ\'
		UNION ALL
		SELECT codbanc,CONCAT_WS(\' \',TRIM(banco),numcuent)
		FROM banc WHERE tbanco <> \'CAJ\' ORDER BY nomb_banc');

		$edit->banco->db_name='banco';
		$edit->banco->rel_id ='sfpa';
		$edit->banco->style  ='width:150px;';
		$edit->banco->rule   ='condi_required|callback_chtipo[<#i#>]';

		$edit->monto = new inputField('Monto <#o#>', 'monto_<#i#>');
		$edit->monto->db_name   = 'monto';
		$edit->monto->css_class = 'inputnum';
		$edit->monto->rel_id    = 'sfpa';
		$edit->monto->size      = 10;
		$edit->monto->rule      = 'required|mayorcero';
		$edit->monto->showformat ='decimal';
		//************************************************
		// Fin detalle 2 (sfpa)
		//************************************************

		$edit->ivat = new hiddenField('I.V.A', 'iva');
		$edit->ivat->css_class ='inputnum';
		$edit->ivat->readonly  =true;
		$edit->ivat->size      = 10;

		$edit->totals = new hiddenField('Sub-Total', 'totals');
		$edit->totals->css_class ='inputnum';
		$edit->totals->readonly  =true;
		$edit->totals->size      = 10;

		$edit->totalg = new hiddenField('Total', 'totalg');
		$edit->totalg->css_class ='inputnum';
		$edit->totalg->readonly  =true;
		$edit->totalg->size      = 10;

		$edit->observa       = new textareaField('Observaci&oacute;n', 'observa');
		$edit->observa->cols = 50;
		$edit->observa->rows = 3;

		$edit->pagacon = new inputField('Paga con', 'pagacon');
		$edit->pagacon->css_class   = 'inputnum';
		$edit->pagacon->size        = 10;
		$edit->pagacon->insertValue = '0.00';
		$edit->pagacon->onkeyup    = 'fvuelto()';

		$edit->nfiscal   = new inputField('No.Fiscal', 'nfiscal');
		$edit->observ1   = new inputField('Observaci&oacute;n', 'observ1');
		$edit->zona      = new inputField('Zona', 'zona');
		$edit->ciudad    = new inputField('Ciudad', 'ciudad');
		$edit->exento    = new inputField('Exento', 'exento');
		$edit->maqfiscal = new inputField('Mq.Fiscal', 'maqfiscal');
		$edit->pfac      = new hiddenField('Presupuesto'    , 'pfac');
		$edit->snte      = new hiddenField('Nota de entrega', 'snte');

		$edit->reiva     = new inputField('Retencion de IVA', 'reiva');
		$edit->creiva    = new inputField('Comprobante', 'creiva');
		$edit->freiva    = new inputField('Fecha', 'freiva');
		$edit->ereiva    = new inputField('Emision', 'ereiva');

		$edit->usuario   = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa   = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora      = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));

		$sfacreferen   = trim($this->datasis->traevalor('SFACREFEREN','Forma de pago predeterminada en facturacion Ej. E'));
		$edit->referen = new radiogroupField('', 'referen', array('P'=>'Dejar Pendiente','E'=>'Efectivo','C'=>'Credito','M'=>'Multiple/Otros'));
		$edit->referen->insertValue = empty($sfacreferen)? 'P': $sfacreferen;
		$edit->referen->onchange    = 'chreferen()';

		if($manual=='S'){
			$edit->referen->when=array('');
		}else{
			$edit->referen->when=array('create','modify');
		}
		//Fin de los campos comodines

		$edit->buttons('add_rel');
		if(!empty($this->_url)) $edit->_process_uri=$this->_url; //Necesario cuando se crea desde presupuesto o pedido

		$edit->build();

		if($edit->on_success()){

			if(isset($this->_sfacmaestra)){
				$numero = $edit->_dataobject->get('numero');
				if($numero==$this->_sfacmaestra){
					$rt=array(
						'status' =>'A',
						'mensaje'=>'Registro guardado',
						'pk'     =>$edit->_dataobject->pk,
						'manual' =>$manual,
						'vuelto' =>$edit->pagacon->newValue - $edit->totalg->newValue
					);
					echo json_encode($rt);
				}
			}else{
				$rt=array(
					'status' =>'A',
					'mensaje'=>'Registro guardado',
					'pk'     =>$edit->_dataobject->pk,
					'manual' =>$manual,
					'vuelto' =>$edit->pagacon->newValue - $edit->totalg->newValue
				);
				echo json_encode($rt);
			}
		}else{
			if($this->genesal){
				$conten['form']  =& $edit;
				//$this->load->view('view_sfac_add', $conten);
				$this->load->view('view_sfac_pos1', $conten);
			}else{
				$rt=array(
					'status' =>'B',
					'mensaje'=> html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string)),
					'pk'     =>'',
					'manual' =>$manual,
					'vuelto' =>0.00
				);
				echo json_encode($rt);
			}
		}
	}

	function dataprintser($st,$uid){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Imprimir factura', 'sfac');
		$id=$edit->get_from_dataobjetct('id');
		$urlid=$edit->pk_URI();
		$sfacforma=$this->datasis->traevalor('FORMATOSFAC','Especifica el metodo a ejecutar para descarga de formato de factura en Proteo Ej. descargartxt...');
		if(empty($sfacforma)) $sfacforma='descargartxt';
		$url=site_url('formatos/'.$sfacforma.'/FACTURA'.$urlid);
		if(isset($this->back_url))
			$edit->back_url = site_url($this->back_url);
		else
			$edit->back_url = site_url('ajax/reccierraventana');

		$edit->back_save   = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;

		$edit->pre_process('insert' ,'_pre_print_insert');
		$edit->pre_process('delete' ,'_pre_print_delete');

		$edit->nfiscal = new inputField('Control F&iacute;scal','nfiscal');
		$edit->nfiscal->rule='max_length[12]|required';
		$edit->nfiscal->size =14;
		$edit->nfiscal->maxlength =12;
		$edit->nfiscal->autocomplete=false;

		$edit->tipo_doc = new inputField('Factura','tipo_doc');
		$edit->tipo_doc->rule='max_length[1]';
		$edit->tipo_doc->size =3;
		$edit->tipo_doc->mode='autohide';
		$edit->tipo_doc->maxlength =1;

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->mode='autohide';
		$edit->numero->size =10;
		$edit->numero->in='tipo_doc';
		$edit->numero->maxlength =8;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule = 'chfecha';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;
		$edit->fecha->maxlength =8;

		$edit->cod_cli = new inputField('Cliente','cod_cli');
		$edit->cod_cli->rule='max_length[5]';
		$edit->cod_cli->size =7;
		$edit->cod_cli->mode='autohide';
		$edit->cod_cli->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[40]';
		$edit->nombre->size =42;
		$edit->nombre->mode='autohide';
		$edit->nombre->in='cod_cli';
		$edit->nombre->maxlength =40;

		$edit->rifci = new inputField('Rif/Ci','rifci');
		$edit->rifci->rule='max_length[13]';
		$edit->rifci->size =15;
		$edit->rifci->mode='autohide';
		$edit->rifci->maxlength =13;

		$total   = $edit->get_from_dataobjetct('totalg');
		$edit->totalg = new freeField('<b>Monto a pagar</b>','monto','<b id="vh_monto" style="font-size:2em">'.nformat($total).'</b>');

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);

			echo json_encode($rt);
		}else{
			$rt=array(
				'status' =>'B',
				'mensaje'=> utf8_encode(html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string))),
				'pk'     =>''
			);
			//echo json_encode($rt);
			echo $edit->output;
		}
	}


	//******************************************************************
	// Guarda el Nro Fiscal
	//
	function guardafiscal() {
		$factura = $this->input->post('factura');
		$nfiscal = $this->input->post('nfiscal');
		$this->db->where('id',$factura);
		$this->db->update('sfac',array('nfiscal'=>$nfiscal));
		echo 'Nro fiscal Guardado';
	}

	//******************************************************************
	//  Reimprimir Factura y cambia Nro Fiscal
	//
	function dataprint( $st, $uid ){
		$referen = $this->datasis->dameval('SELECT referen FROM sfac WHERE id='.$this->db->escape($uid));
		if($referen=='P'){
			redirect('formatos/descargar/FACTURA/'.$uid);
		}

		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Imprimir factura', 'sfac');

		$sfacforma=$this->datasis->traevalor('FORMATOSFAC','Especifica el metodo a ejecutar para descarga de formato de factura en Proteo Ej. descargartxt...');

		if(empty($sfacforma)) $sfacforma='descargar';
			$url=site_url('formatos/'.$sfacforma.'/FACTURA/'.$uid);

		if(isset($this->back_url))
			$edit->back_url = site_url($this->back_url);
		else
			$edit->back_url = site_url('ajax/reccierraventana/N');

		$edit->back_save   = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;

		$edit->post_process('update','_post_print_update');
		$edit->pre_process( 'insert','_pre_print_insert');
		$edit->pre_process( 'delete','_pre_print_delete');

		$manual = $this->datasis->dameval('SELECT manual FROM sfac WHERE id='.$this->db->escape($uid));
		if($manual!='S'){
			$edit->container = new containerField('impresion','La descarga se realizara en algunos segundos, en caso de no hacerlo haga click '.anchor('formatos/'.$sfacforma.'/FACTURA/'.$uid,'aqui'));
		}else{
			$edit->container = new containerField('impresion','Haga click '.anchor('formatos/descargar/FACTURA/'.$uid,'aqui').' para descargar el comprobante de registro');
		}

		$edit->nfiscal = new inputField('Control f&iacute;scal','nfiscal');
		$edit->nfiscal->rule='max_length[12]|required';
		$edit->nfiscal->size =14;
		$edit->nfiscal->maxlength =12;
		$edit->nfiscal->autocomplete=false;

		if($manual=='S'){
			$edit->nromanual = new inputField('N&uacute;mero de factura manual','nromanual');
			$edit->nromanual->rule='max_length[14]|required';
			$edit->nromanual->size =18;
			$edit->nromanual->maxlength =14;
			$edit->nromanual->autocomplete=false;
		}

		$chkval   = false;
		$mmsj     = 'Dato sugerido por el sistema, no esta guardado';
		$tipo     = $edit->get_from_dataobjetct('tipo_doc');
		$dbtipo   = $this->db->escape($tipo );
		$dbcajero = $this->db->escape($edit->get_from_dataobjetct('cajero'));
		$numfis   = trim($edit->get_from_dataobjetct('nfiscal'));
		$fiscal   = $this->datasis->traevalor('IMPFISCAL','Indica si se usa o no impresoras fiscales, esto activa opcion para cierre X y Z');
		if($fiscal=='S' && $manual!='S'){
			if(empty($numfis)){
				$num      = $this->datasis->dameval("SELECT MAX(nfiscal) FROM sfac WHERE cajero=${dbcajero} AND tipo_doc=${dbtipo} AND MID(numero,1,1)!='_'");
				if($tipo=='D'){
					$nums = trim($this->datasis->dameval("SELECT MAX(nfiscal) AS nf FROM smov WHERE tipo_doc IN ('NC') AND fecha=CURDATE() AND nfiscal IS NOT NULL"));
					if($nums>$num){
						$num=$nums;
					}
				}
				if(!empty($num)){
					$nn       = $num+1;
					$edit->nfiscal->updateValue=str_pad($nn,8,'0',STR_PAD_LEFT);
					$edit->nfiscal->style = 'background-color:#FFDD00';
				}
			}

			$edit->maqfiscal = new inputField('Serial m&aacute;quina f&iacute;scal','maqfiscal');
			$edit->maqfiscal->rule='max_length[15]|strtoupper';
			$edit->maqfiscal->size =16;
			$edit->maqfiscal->maxlength =15;

			$smaqfiscal=trim($edit->get_from_dataobjetct('maqfiscal'));
			if(empty($smaqfiscal)){
				$maqfiscal=$this->datasis->dameval("SELECT maqfiscal FROM sfac WHERE cajero=${dbcajero} AND tipo_doc=${dbtipo} AND MID(numero,1,1)!='_' ORDER BY id DESC LIMIT 1");
				$edit->maqfiscal->updateValue=trim($maqfiscal);
				$edit->maqfiscal->style = 'background-color:#FFDD00';
				$edit->maqfiscal->title = $mmsj;
				$chkval = true;
			}

			if($tipo=='D'){
				$edit->dmaqfiscal = new inputField('Serial m&aacute;quina f&iacute;scal de la factura de or&iacute;gen','dmaqfiscal');
				$edit->dmaqfiscal->rule='max_length[15]|strtoupper';
				$edit->dmaqfiscal->size =16;
				$edit->dmaqfiscal->maxlength =15;

				$dmaqfiscal=trim($edit->get_from_dataobjetct('dmaqfiscal'));
				if(empty($dmaqfiscal)){
					$dbnumero=$this->db->escape($edit->get_from_dataobjetct('factura'));
					$mfiscal=$this->datasis->dameval("SELECT maqfiscal FROM sfac WHERE numero=${dbnumero} AND tipo_doc='F'");
					$edit->dmaqfiscal->updateValue=$mfiscal;
					$edit->dmaqfiscal->style = 'background-color:#FFDD00';
					$edit->dmaqfiscal->title = $mmsj;
					$chkval = true;
				}
			}
		}elseif($manual!='S'){
			$numfis = trim($edit->get_from_dataobjetct('nfiscal'));
			if(empty($numfis)){
				$numf = trim($this->datasis->dameval("SELECT MAX(nfiscal) AS nf FROM sfac WHERE cajero=${dbcajero} AND tipo_doc<>'X' AND MID(numero,1,1)!='_' AND fecha=CURDATE()"));
				$nums = trim($this->datasis->dameval("SELECT MAX(nfiscal) AS nf FROM smov WHERE tipo_doc IN ('NC','FC') AND fecha=CURDATE() AND nfiscal IS NOT NULL"));

				if($numf>$nums){
					$num=$numf;
				}else{
					$num=$nums;
				}

				if(!empty($num)){
					$arr_num  = explode('-',$num);
					$last     = count($arr_num)-1;
					if($last>=0){
						if(is_numeric($arr_num[$last])){
							$long = strlen($arr_num[$last]);
							$arr_num[$last] = $arr_num[$last]+1;
							$arr_num[$last] = str_pad($arr_num[$last],$long,'0',STR_PAD_LEFT);
							$nn = implode('-',$arr_num);
							$edit->nfiscal->updateValue=$nn;
							$edit->nfiscal->style = 'background-color:#FFDD00';
							$edit->nfiscal->title = $mmsj;
							$chkval = true;
						}
					}
				}
			}
		}

		$edit->tipo_doc = new inputField('Factura','tipo_doc');
		$edit->tipo_doc->mode='autohide';

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->mode='autohide';
		$edit->numero->in='tipo_doc';

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->mode = 'autohide';

		$edit->cod_cli = new inputField('Cliente','cod_cli');
		$edit->cod_cli->mode='autohide';

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->mode='autohide';
		$edit->nombre->in='cod_cli';

		$edit->rifci = new inputField('Rif/Ci','rifci');
		$edit->rifci->mode='autohide';

		$total   = $edit->get_from_dataobjetct('totalg');
		$edit->totalg = new freeField('<b>Monto a pagar</b>','monto','<b id="vh_monto" style="font-size:2em">'.nformat($total).'</b>');

		$ccopia=intval($this->datasis->dameval('SELECT COUNT(*) AS cana FROM formatos WHERE nombre=\'FACTURA\' AND proteo LIKE \'%$copia%\''));
		$nfiscal = $edit->get_from_dataobjetct('nfiscal');
		if(!empty($nfiscal) && $ccopia>0){
			$edit->container2 = new containerField('copia','<p style="font-size:0.8em; text-align:center;margin:0">Si desea una copia con encabezado haga click '.anchor('formatos/descargar/FACTURA/'.$uid.'/COPIA','aqui').'</p>');
		}

		$edit->buttons('save');
		$edit->build();

		$tipo_doc = $edit->get_from_dataobjetct('tipo_doc');
		if($tipo_doc=='F'){
			$maestra  = $edit->get_from_dataobjetct('maestra');
			$numero   = $edit->get_from_dataobjetct('numero');
			$dbnumero = $this->db->escape($numero);
			$dbmaestra= $this->db->escape($maestra );
			if(!empty($maestra) && $maestra!=$numero){
				$ww= "numero=${dbmaestra} OR maestra=${dbmaestra}";
			}else{
				$ww= "maestra=${dbnumero}";
			}

			$mSQL="SELECT id,numero,nfiscal FROM sfac WHERE ${ww} ORDER BY numero LIMIT 100";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				$cont='';
				$ft  =true;
				foreach ($query->result() as $row){
					if($row->numero==$numero) continue;
					if(empty($row->nfiscal)){
						$adi=' *';
						if($ft){
							//$edit->back_save = false;
							$edit->back_url = $edit->back_uri = $edit->_postprocess_uri= $this->url.'dataprint/modify/'.$row->id;
							$ft=false;
						}
					}else{
						$adi=' ('.$row->nfiscal.')';
					}
					$cont.= ' '.anchor($this->url.'dataprint/modify/'.$row->id,$row->numero.$adi).br();
				}
				if(!empty($cont)){
					$edit->free = new freeField('Facturas relacionadas','maestro',$cont);
				}
			}
			$edit->build();
		}

		if($st=='modify' && $manual!='S'){
			$script= '<script type="text/javascript" >
			$(function() {
				setTimeout(\'window.location="'.$url.'"\',100);';
				//if($chkval){
				//	$script.= '$(window).bind("beforeunload", function() {
				//		return confirm("No se ha guardado los campos resaltados, seguro desea salir?");
				//	});';
				//}
			$script.='});
			</script>';
		}else{
			$script='';
		}

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['script']  = script('jquery.js');
		$data['script'] .= script('plugins/jquery.numeric.pack.js');
		$data['script'] .= script('plugins/jquery.floatnumber.js');
		$data['script'] .= $script;
		$data['title']   = heading($this->tits);
		$this->load->view('view_ventanas', $data);
	}

	function _pre_print_insert($do){ return false;}
	function _pre_print_delete($do){ return false;}

	//Chequea que el precio de los articulos de la devolucion
	//sean los facturados y que no sean menores al precio 4
	function chpreca($val,$i){
		$tipo_doc = $this->input->post('tipo_doc');
		$codigo   = $this->input->post('codigoa_'.$i);
		$manual   = $this->input->post('manual');
		if($manual=='S') return true;
		$dbcodigo = $this->db->escape($codigo);
		$val      = floatval($val);

		if($tipo_doc == 'D'){
			$factura  = $this->input->post('factura');
			$dbfactura= $this->db->escape($factura);

			if(!isset($this->devperca)){
				$this->devpreca=array();
				$mSQL="SELECT b.codigoa,b.preca
				FROM sitems AS b
				WHERE b.numa=${dbfactura} AND b.tipoa='F'
				GROUP BY b.codigoa,b.preca";
				$query = $this->db->query($mSQL);
				foreach ($query->result() as $row){
					$ind=trim($row->codigoa);
					$this->devpreca[$ind][]=$row->preca;
				}
			}

			if(isset($this->devpreca[$codigo])){
				$rt=false;
				$this->validation->set_message('chpreca', 'El art&iacute;culo '.$codigo.' se esta devolviendo por un monto distinto al facturado que fue de '.implode(', ',$this->devpreca[$codigo]));
				foreach($this->devpreca[$codigo] AS $precio){
					if($precio-$val==0){
						$rt=true;
					}
				}
				return $rt;
			}else{
				$this->validation->set_message('chpreca', 'El art&iacute;culo '.$codigo.' no fue facturado');
				return false;
			}
		}elseif($tipo_doc == 'F'){
			$combo = $this->input->post('combo_'.$i);

			if(!empty($combo)){
				$dbcombo = $this->db->escape($combo);
				$precio  = $this->datasis->dameval("SELECT a.precio FROM sinvcombo AS a WHERE a.combo=${dbcombo} AND a.codigo=${dbcodigo}");
				if(empty($precio)){
					$this->validation->set_message('chpreca', 'El art&iacute;culo "'.$codigo.'" no parece ser del combo "'.$combo.'" o presenta problema con el precio');
					return false;
				}
				if(abs($val-$precio)!=0){
					$this->validation->set_message('chpreca', 'El art&iacute;culo "'.$codigo.'" del combo "'.$combo.'" debe contener un precio igual a '.nformat($precio));
					return false;
				}
				return true;
			}else{
				if(!isset($this->sclitipo)){
					$cliente  = $this->input->post('cod_cli');
					$this->sclitipo = $this->datasis->dameval('SELECT tipo FROM scli WHERE cliente='.$this->db->escape($cliente));
				}
				if($this->sclitipo=='5'){
					$precio4 = floatval($this->datasis->dameval('SELECT ultimo FROM sinv WHERE codigo='.$dbcodigo));
				}else{
					$precio4 = floatval($this->datasis->dameval('SELECT ROUND(precio4*100/(100+iva),2) FROM sinv WHERE codigo='.$dbcodigo));
				}
				$this->validation->set_message('chpreca', 'El art&iacute;culo '.$codigo.' debe contener un precio de al menos '.nformat($precio4));
				if(empty($precio4)) $precio4=0; else $precio4=round($precio4,2);
				if($val>=$precio4){
					return true;
				}
			}
		}
		return false;
	}

	//Chequea el cajero
	function chcajero($scaj){
		$referen=$this->input->post('referen');

		if($referen===false) return true; //En estos caso se evalua en el pre-process
		if($referen=='E' || $referen=='M'){
			if(empty($scaj)){
				$this->validation->set_message('chcajero', 'No posee cajero asignado');
				return false;
			}
			$rt=$this->validation->cajerostatus($scaj);
			if(isset($this->validation->_error_messages['cajerostatus'])){
				$this->validation->set_message('chcajero', $this->validation->_error_messages['cajerostatus']);
			}else{
				$this->validation->set_message('chcajero', "El cajero ${scaj} ya fue cerrado para esta fecha");
			}
			return $rt;
		}else{
			return true;
		}

	}

	//Chequea si puede o no vender negativo
	function chcananeg($val,$i){
		$tipo_doc = $this->input->post('tipo_doc');
		$almacen  = $this->input->post('almacen');
		if($this->vnega=='N' && $tipo_doc=='F'){
			$snte     = $this->input->post('snte');
			if(!empty($snte)){
				return true;
			}

			$codigo   = $this->input->post('codigoa_'.$i);
			$dbcodigo = $this->db->escape($codigo);
			$dbalmacen= $this->db->escape($almacen);
			$tipo     = trim($this->datasis->dameval("SELECT tipo FROM sinv WHERE codigo=${dbcodigo}"));
			if(!empty($tipo)){
				if($tipo[0]=='S') return true;
			}else{
				$this->validation->set_message('chcananeg', 'El art&iacute;culo '.htmlspecialchars($codigo).' no tiene existencia');
				return false;
			}
			$mSQL    = "SELECT SUM(a.existen) AS cana FROM itsinv AS a JOIN caub AS b ON a.alma=b.ubica AND b.tipo<>'N' WHERE a.codigo=${dbcodigo} AND b.ubica=${dbalmacen}";
			$existen = floatval($this->datasis->dameval($mSQL));
			$val     = floatval($val);
			if($val>$existen){
				$this->validation->set_message('chcananeg', 'El art&iacute;culo '.htmlspecialchars($codigo).' no tiene cantidad suficiente para facturarse ('.nformat($existen).')');
				return false;
			}
		}
		return true;
	}

	//Chequea que la cantidad devuelta no sea mayor que la facturada
	function chcanadev($val,$i){
		$tipo_doc = $this->input->post('tipo_doc');
		$factura  = $this->input->post('factura');
		$codigo   = $this->input->post('codigoa_'.$i);
		$precio   = number_format($this->input->post('preca_'.$i),2);

		if($tipo_doc=='D'){
			$dbfactura=$this->db->escape($factura);

			if(!isset($this->devitems)){
				$this->devitems=array();

				$mSQL="SELECT
				aa.cana,SUM(COALESCE(d.cana,0)) AS dev,aa.codigo AS codigoa,aa.preca
				FROM (SELECT SUM(b.cana) AS cana,TRIM(a.codigo) AS codigo,b.preca,b.numa
				FROM sinv AS a
				JOIN sitems AS b ON a.codigo=b.codigoa
				WHERE b.numa=${dbfactura} AND b.tipoa='F'
				GROUP BY b.codigoa,b.preca) AS aa
				LEFT JOIN sfac   AS c  ON aa.numa=c.factura AND c.tipo_doc='D'
				LEFT JOIN sitems AS d ON c.numero=d.numa AND c.tipo_doc=d.tipoa AND aa.codigo=d.codigoa AND aa.preca=d.preca
				GROUP BY aa.codigo,aa.preca";

				$query = $this->db->query($mSQL);
				foreach ($query->result() as $row){
					$ind =trim($row->codigoa);
					$ind2=number_format($row->preca,2);
					$c=(empty($row->cana))? 0 : $row->cana;
					$d=(empty($row->dev))?  0 : $row->dev;
					$this->devitems[$ind][$ind2]=$c-$d;
				}
			}

			if(isset($this->devitems[$codigo][$precio])){
				if($val <= $this->devitems[$codigo][$precio]){
					return true;
				}
				$this->validation->set_message('chcanadev', 'Esta devolviendo m&aacute;s de lo que se facturo del art&iacute;culo '. htmlspecialchars($codigo).' puede devolver m&aacute;ximo '.implode(', ',$this->devitems[$codigo]));
			}else{
				$this->validation->set_message('chcanadev', 'El art&iacute;culo '. htmlspecialchars($codigo).' no se puede devolver, nunca fue facturado o ya esta devuelto');
			}
			return false;
		}
		return true;
	}

	//Chequea los campos de numero y fecha en las formas de pago
	//cuando deban corresponder
	function chtipo($val,$i){
		$tipo=$this->input->post('tipo_'.$i);
		if(empty($tipo)) return true;
		$this->validation->set_message('chtipo', 'El campo %s es obligatorio');

		if(empty($val) && ($tipo=='NC' || $tipo=='DP' || $tipo=='DE'))
			return false;
		else
			return true;
	}

	//Chequea que la factura a devolver exista
	function chfactura($factura){
		$tipo_doc=$this->input->post('tipo_doc');
		$this->validation->set_message('chfactura', 'El campo %s debe contener un numero de factura v&aacute;lido');
		if($tipo_doc=='D' && empty($factura)){
			return false;
		}
		return true;
	}

	function _pre_insert($do,$action='I'){

		$cliente  = $do->get('cod_cli');
		$tipoa    = $do->get('tipo_doc');
		$manual   = $do->get('manual');
		$fecha    = $do->get('fecha');
		$estampa  = $do->get('estampa');
		$referen  = $do->get('referen');
		$cajero   = $do->get('cajero');
		$numero   = $do->get('numero');
		$tipo_doc = $do->get('tipo_doc');
		$descuento= floatval($do->get('descuento'));

		$descu1   = floatval($do->get('descu1'));
		$descu2   = floatval($do->get('descu2'));
		$descu3   = floatval($do->get('descu3'));

		$globaldes=$descuento/100;

		if($tipo_doc=='D'){
			$factura = trim($do->get('factura'));
			if($factura[0]=='_'){
				$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='No puede devolver una prefactura';
				return false;
			}
		}

		$dbcliente=$this->db->escape($cliente);
		if(empty($cajero) && $referen!='C' && $referen!='P'){
			$cajero=$this->secu->getcajero();
			if(empty($cajero)){
				$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='El usuario debe tener un cajero asignado';
				return false;
			}
			$do->set('cajero',$cajero);
		}

		//Totaliza la factura
		$totalg = 0;
		$cana   = $do->count_rel('sitems');
		if($cana <= 0){
			$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='Debe tener al menos un producto';
			return false;
		}
		for($i=0;$i<$cana;$i++){
			$itcana   = $do->get_rel('sitems', 'cana' , $i);
			$itpreca  = $do->get_rel('sitems', 'preca', $i);
			$itiva    = $do->get_rel('sitems', 'iva'  , $i);
			$itdescu  = $do->get_rel('sitems', 'descu', $i);

			if( $descuento > 0 || $itdescu > 0 || $descu1 > 0 || $descu2 > 0 || $descu3 > 0 ){
				if( $descuento > 0 ){
					$itpreca = round($itpreca*(1-$globaldes),8);
				}
				if( $itdescu > 0 ){
					$itpreca = round($itpreca*(100-$itdescu)/100,8);
				}
				if( $descu1 > 0 ){
					$itpreca = round($itpreca*(100-$descu1)/100,8);
				}
				if( $descu2 > 0 ){
					$itpreca = round($itpreca*(100-$descu2)/100,8);
				}
				if( $descu3 > 0 ){
					$itpreca = round($itpreca*(100-$descu3)/100,8);
				}
				$do->set_rel('sitems','preca',$itpreca,$i);
			}
			$itimporte = round($itpreca*$itcana,2);
			$iva       = $itimporte*($itiva/100);
			$totalg   += $itimporte+$iva;
		}


		$totalg = round($totalg,2);
		//Fin de la totalizacion de facturas

		if($referen=='P'){
			if($manual=='S'){
				$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='No se puede dejar una factura pendiente manual';
				return false;
			}
			$do->truncate_rel('sfpa');
		}elseif($referen=='E'){
			$do->truncate_rel('sfpa');
			$do->set_rel('sfpa','tipo' ,'EF'   ,0);
			$do->set_rel('sfpa','monto',$totalg,0);
		}elseif($referen=='C'){
			$do->truncate_rel('sfpa');
			$do->set_rel('sfpa','tipo' ,''     ,0);
			$do->set_rel('sfpa','monto',$totalg,0);
		}

		$con=$this->db->query('SELECT tasa,redutasa,sobretasa FROM civa ORDER BY fecha desc LIMIT 1');
		if($con->num_rows() > 0){
			$t=$con->row('tasa');$rt=$con->row('redutasa');$st=$con->row('sobretasa');
		}else{
			$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='Debe cargar la tabla de IVA.';
			return false;
		}

		//Totaliza los pagos
		$sfpa=$tcredito=0;
		$cana=$do->count_rel('sfpa');
		for($i=0;$i<$cana;$i++){
			$sfpa_tipo = $do->get_rel('sfpa','tipo',$i);
			$sfpa_monto= floatval($do->get_rel('sfpa','monto',$i));
			$sfpa+=$sfpa_monto;
			if(empty($sfpa_tipo)) $tcredito+=$sfpa_monto;
		}
		$sfpa=round($sfpa,2);
		//Fin de la totalizacion del pago

		//Valida el cajero (obligado si hay forma de pago)
		if(round(abs($sfpa-$tcredito),2)>0){
			$chcaj=$this->validation->cajerostatus($cajero);
			if(!$chcaj){
				if(isset($this->validation->_error_messages['cajerostatus'])){
					$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']=$this->validation->_error_messages['cajerostatus'];
				}else{
					$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='Cajero inexistente o cerrado para la fecha';
				}
				return false;
			}
		}
		//fin de la validacion del cajero

		//Validaciones del pago
		if(abs($sfpa-$totalg)>0.02 && $referen!='P'){
			$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='El monto del pago no coincide con el monto de la factura (Pago:'.$sfpa.', Factura:'.$totalg.') ';
			return false;
		}
		//Fin de la validacion de pago

		//Calcula totalizacion y corte por maxlin
		$maxlin=intval($this->datasis->traevalor('MAXLIN'));
		$this->_creanfac=false;

		$tasa=$montasa=$reducida=$monredu=$sobretasa=$monadic=$exento=$totalg=0;
		$cana=$do->count_rel('sitems');
		for($i=0;$i<$cana;$i++){

			//Aplica el corte segun maxlin
			if($maxlin>0 && $i>=$maxlin && $manual!='S' && $referen!='P'){
				$this->_creanfac=true;
				$do->rel_rm('sitems',$i);
				continue;
			}
			//fin del corte por maxlin

			$itcana    = $do->get_rel('sitems','cana' ,$i);
			$itpreca   = round($do->get_rel('sitems','preca',$i),2);
			$itiva     = $do->get_rel('sitems','iva'  ,$i);
			$itimporte = $itpreca*$itcana;
			$iva       = $itimporte*($itiva/100);

			if($itiva-$t==0) {
				$tasa   +=$iva;
				$montasa+=$itimporte;
			}elseif($itiva-$rt==0) {
				$reducida+=$iva;
				$monredu +=$itimporte;
			}elseif($itiva-$st==0) {
				$sobretasa+=$iva;
				$monadic  +=$itimporte;
			}else{
				$exento += $itimporte;
			}

			$totalg    +=$itimporte+$iva;
		}
		$totalg = round($totalg,2);
		//Fin de la totalizacion de los montos

		//Ajusta la forma de pago en caso de limitar las facturas
		if($this->_creanfac){
			$sfpa=0;
			$laid=0;
			$cana=$do->count_rel('sfpa');
			for($i=0;$i<$cana;$i++){
				if($sfpa>=$totalg){
					$do->rel_rm('sfpa',$i);
					continue;
				}
				$sfpa_tipo = $do->get_rel('sfpa','tipo',$i);
				$sfpa_monto= $do->get_rel('sfpa','monto',$i);
				$sfpa+=$sfpa_monto;
				$laid=$i;
			}
			if($sfpa>$totalg){
				$ult = $do->get_rel('sfpa','monto',$laid);
				$do->set_rel('sfpa','monto',$ult-($sfpa-$totalg),$laid);

			}
			$sfpa=round($sfpa,2);
		}

		//Calcula el credito
		$cana    = $do->count_rel('sfpa');
		$credito = 0;
		for($i=0;$i<$cana;$i++){
			$sfpa_tipo = $do->get_rel('sfpa','tipo',$i);
			$sfpa_monto= $do->get_rel('sfpa','monto',$i);
			if(empty($sfpa_tipo)) $credito+=$sfpa_monto;
		}
		//Fin del calculo a credito

		if($manual=='S' && $fecha!=$estampa && $credito-$sfpa_monto!=0 ){
			$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='Una factura manual solo se puede pagar en efectivo si es el mismo d&iacute;a, en caso contrario se debe cargar a cr&eacute;dito y luego hacer la cobranza.';
			return false;
		}

		$do->set('exento'   ,$exento   );
		$do->set('tasa'     ,$tasa     );
		$do->set('reducida' ,$reducida );
		$do->set('sobretasa',$sobretasa);
		$do->set('montasa'  ,$montasa  );
		$do->set('monredu'  ,$monredu  );
		$do->set('monadic'  ,$monadic  );
		if($manual!='S'){
			$do->set('fecha' ,date('Y-m-d'));
		}

		$fecha  = $do->get('fecha');
		//Validacion del limite de credito del cliente
		if($tcredito>0 && $tipoa=='F' && $manual!='S' && $referen!='P'){
			$rrow = $this->datasis->damerow("SELECT limite,formap,credito,tolera,TRIM(socio) AS socio FROM scli WHERE cliente=${dbcliente}");
			if($rrow!=false){
				if(empty($rrow['tolera']))  $rrow['tolera'] =0;
				if(empty($rrow['limite']))  $rrow['limite'] =0;
				if(empty($rrow['credito'])) $rrow['credito']='N';

				$cdias   = (empty($rrow['formap']))? 0: $rrow['formap'];
				$pcredito= $rrow['credito'];
				$tolera  = (100+$rrow['tolera'])/100;
				$socio   = $rrow['socio'];
				$limite  = $rrow['limite']*$tolera;
			}else{
				$limite  = $cdias  = $tolera = 0;
				$pcredito= 'N';
				$socio   = null;
			}

			//Chequea la cuenta propia
			$mSQL="SELECT SUM(monto*(tipo_doc IN ('FC','GI','ND'))) AS debe, SUM(monto*(tipo_doc IN ('NC','AB','AN'))) AS haber FROM smov WHERE cod_cli=${dbcliente}";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				$row = $query->row();
				$saldo=$row->debe-$row->haber;
			}else{
				$saldo=0;
			}

			if($tcredito > ($limite-$saldo) || $cdias<=0 || $pcredito=='N'){
				$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='El cliente no tiene suficiente cr&eacute;dito propio';
				return false;
			}

			//Chequea la cuenta de sus asociados (si es responsables de otros clientes)
			$mSQL="SELECT SUM(a.monto*(a.tipo_doc IN ('FC','GI','ND'))) AS debe, SUM(a.monto*(a.tipo_doc IN ('NC','AB','AN'))) AS haber
				FROM smov AS a
				JOIN scli AS b ON a.cod_cli=b.socio
				WHERE b.socio=${dbcliente}";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				$row = $query->row();
				$asaldo=$row->debe-$row->haber;
			}else{
				$asaldo=0;
			}

			if($tcredito > ($limite-$saldo-$asaldo) || $cdias<=0 || $pcredito=='N'){
				$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='El cliente no tiene suficiente cr&eacute;dito de grupo';
				return false;
			}

			//Chequea el credito de su maestro (si es subordinado)
			if(!empty($socio)){
				$dbsocio= $this->db->escape($socio);
				$rrow   = $this->datasis->damerow("SELECT limite,formap,credito,tolera,socio FROM scli WHERE cliente=${dbsocio}");
				if($rrow!=false){
					if(empty($rrow['tolera']))  $rrow['tolera'] =0;
					if(empty($rrow['limite']))  $rrow['limite'] =0;
					if(empty($rrow['credito'])) $rrow['credito']='N';

					$mastercdias   = (empty($rrow['formap']))? 0: $rrow['formap'];
					$mastercredito = $rrow['credito'];
					$mastertolera  = (100+$rrow['tolera'])/100;
					$mastersocio   = $rrow['socio'];
					$masterlimite  = $rrow['limite']*$tolera;
				}else{
					$masterlimite = $mastercdias = $mastertolera = 0;
					$mastercredito= 'N';
					$mastersocio  = null;
				}

				$mSQL="SELECT SUM(a.monto*(a.tipo_doc IN ('FC','GI','ND'))) AS debe, SUM(a.monto*(a.tipo_doc IN ('NC','AB','AN'))) AS haber
				FROM smov AS a
				JOIN scli AS b ON a.cod_cli=b.socio
				WHERE b.socio=${dbsocio}";
				$query = $this->db->query($mSQL);
				if ($query->num_rows() > 0){
					$row = $query->row();
					$mastersaldo=$row->debe-$row->haber;
				}else{
					$mastersaldo=0;
				}

				if($tcredito > ($masterlimite-$saldo-$mastersaldo) || $mastercdias<=0 || $mastercredito=='N'){
					$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='El fiador del cliente no tiene suficiente saldo';
					return false;
				}
			}
			$objdate = date_create($fecha);
			$objdate->add(new DateInterval('P'.$cdias.'D'));
			$vence   = date_format($objdate, 'Y-m-d');
			$do->set('vence',$vence);
		}else{
			$do->set('vence',$fecha);
		}
		//Fin de las validaciones

		$rrow    = $this->datasis->damerow('SELECT nombre,rifci,dire11,dire12,zona FROM scli WHERE cliente='.$dbcliente);
		if($rrow!=false){
			$do->set('nombre',$rrow['nombre']);
			$do->set('direc' ,$rrow['dire11']);
			$do->set('dire1' ,$rrow['dire12']);
			$do->set('zona'  ,$rrow['zona']);
		}

		//Marca si la factura viene de otro lado
		$this->snte = (isset($_POST['snte']))? $_POST['snte'] : '';
		$do->rm_get('snte');
		if(empty($this->snte) && substr($numero,0,1)=='_'){
			$dbfactura=$this->db->escape($numero);
			$mSQL="SELECT numero FROM snte WHERE factura=${dbfactura}";
			$query = $this->db->query($mSQL);
			if($query->num_rows() > 0){
				$arr_snte=array();
				foreach ($query->result() as $row){
					$arr_snte[]=$row->numero;
				}
				$this->snte=implode('-',$arr_snte);
			}
		}

		$this->pfac = (isset($_POST['pfac']))? $_POST['pfac'] : '';
		$do->rm_get('pfac');
		if(empty($this->pfac) && substr($numero,0,1)=='_'){
			$dbfactura=$this->db->escape($numero);
			$mSQL="SELECT numero FROM pfac WHERE factura=${dbfactura}";
			$query = $this->db->query($mSQL);
			if($query->num_rows() > 0){
				$arr_pfac=array();
				foreach ($query->result() as $row){
					$arr_pfac[]=$row->numero;
				}
				$this->pfac=implode('-',$arr_pfac);
			}
		}
		//Fin de la marca

		//Determina el numero de factura
		if($referen=='P'){
			if($action=='U'){
				$numero  = $do->get('numero');
			}else{
				$numero = '_'.$this->datasis->fprox_numero('nsfacp',7);
			}
		}else{
			if($tipoa=='F'){
				if($manual!='S'){
					$numero = $this->datasis->fprox_numero('nsfac');
				}else{
					$numero = 'M'.$this->datasis->fprox_numero('nsfacman',7);
				}
			}else{
				if($manual!='S'){
					$numero = $this->datasis->fprox_numero('nccli');
				}else{
					$numero = 'M'.$this->datasis->fprox_numero('nccliman',7);
				}
			}
		}
		$do->set('numero' ,$numero);
		//Fin del numero de factura

		//Determina la transaccion
		if($referen=='P'){
			if($action=='U'){
				$transac = $do->get('transac');
			}else{
				$transac = $this->datasis->fprox_numero('ntransap');
			}
		}else{
			$transac = $this->datasis->fprox_numero('ntransa');
			$do->set('referen',($credito>0)? 'C': 'E');
		}
		$do->set('transac',$transac);
		//Fin de la transaccion

		$vd     = $do->get('vd');
		$cajero = $do->get('cajero');
		$almacen= $do->get('almacen');
		$estampa= $do->get('estampa');
		$usuario= $do->get('usuario');
		$hora   = $do->get('hora');

		$iva=$totals=$tpeso=0;
		$cana=$do->count_rel('sitems');
		for($i=0;$i<$cana;$i++){
			$itcodigo  = $do->get_rel('sitems','codigoa',$i);
			$itcana    = floatval($do->get_rel('sitems','cana'   ,$i));
			$itpreca   = floatval($do->get_rel('sitems','preca'  ,$i));
			$itiva     = floatval($do->get_rel('sitems','iva'    ,$i));
			$itimporte = $itpreca*$itcana;
			$do->set_rel('sitems','tota'    ,$itimporte,$i);
			//$do->set_rel('sitems','mostrado',$itimporte*(1+($itiva/100)),$i);
			$do->set_rel('sitems','mostrado',0,$i);


			$rowval = $this->datasis->damerow('SELECT pond, base1,precio4,peso FROM sinv WHERE codigo='.$this->db->escape($itcodigo));
			if(empty($rowval)){
				$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='Producto no encontrado ('.$itcodigo.') '.$cana;
				//$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']=print_r($do->data_rel,true);
				return false;
			}
			$tpeso += floatval($rowval['peso'])*$itcana;
			$do->set_rel('sitems','costo'  , $rowval['pond']   ,$i);
			$do->set_rel('sitems','pvp'    , $rowval['base1']  ,$i);
			$do->set_rel('sitems','precio4', $rowval['precio4'],$i);

			$iva    +=$itimporte*($itiva/100);
			$totals +=$itimporte;

			$do->set_rel('sitems','despacha','N'     ,$i);
			$do->set_rel('sitems','numa'    ,$numero ,$i);
			$do->set_rel('sitems','tipoa'   ,$tipoa  ,$i);
			$do->set_rel('sitems','transac' ,$transac,$i);
			$do->set_rel('sitems','fecha'   ,$fecha  ,$i);
			$do->set_rel('sitems','vendedor',$vd     ,$i);
			$do->set_rel('sitems','usuario' ,$usuario,$i);
			$do->set_rel('sitems','estampa' ,$estampa,$i);
			$do->set_rel('sitems','hora'    ,$hora   ,$i);
		}
		$totalg = $totals+$iva;

		$cana=$do->count_rel('sfpa');
		for($i=0;$i<$cana;$i++){
			$sfpatip   = $do->get_rel('sfpa', 'tipo',$i);
			if(!empty($sfpatip)){
				$sfpa_monto= $do->get_rel('sfpa','monto'    ,$i);
				$sfpa_fecha= $do->get_rel('sfpa','fecha'    ,$i);
				if($tipoa=='D'){
					$sfpa_monto *= -1;
				}

				if($sfpatip=='EF'){
					$do->set_rel('sfpa', 'fecha' , $fecha , $i);
				}elseif(empty($sfpa_fecha)){
					$do->set_rel('sfpa', 'fecha' , $fecha , $i);
				}

				$do->set_rel('sfpa','tipo_doc' ,($tipoa=='F')? 'FE':'DE',$i);
				$do->set_rel('sfpa','transac'  ,$transac   ,$i);
				$do->set_rel('sfpa','vendedor' ,$vd        ,$i);
				$do->set_rel('sfpa','cod_cli'  ,$cliente   ,$i);
				$do->set_rel('sfpa','f_factura',$fecha     ,$i);
				$do->set_rel('sfpa','cobrador' ,$cajero    ,$i);
				$do->set_rel('sfpa','numero'   ,$numero    ,$i);
				$do->set_rel('sfpa','almacen'  ,$almacen   ,$i);
				$do->set_rel('sfpa','usuario'  ,$usuario   ,$i);
				$do->set_rel('sfpa','estampa'  ,$estampa   ,$i);
				$do->set_rel('sfpa','hora'     ,$hora      ,$i);
				$do->set_rel('sfpa','monto'    ,$sfpa_monto,$i);
			}else{
				$do->rel_rm('sfpa',$i);
			}
		}

		$do->set('inicial',($credito>0)? $totalg-$credito : 0);
		$do->set('totals' ,round($totals ,2));
		$do->set('totalg' ,round($totalg ,2));
		$do->set('iva'    ,round($iva    ,2));
		$do->set('peso'   ,round($tpeso  ,2));

		if(isset($this->_sfacmaestra)){
			$do->set('maestra',$this->_sfacmaestra);
		}else{
			$do->set('maestra',$numero);
		}
		return true;
	}

	function _pre_update($do){
		$numero   = $do->get('numero');
		$dbid     = $this->db->escape($do->get('id'));
		$referendb= $this->datasis->dameval('SELECT referen FROM sfac WHERE id='.$dbid);

		if($this->_creanfac){
			//Para hacer el corte por maxlin en las facturas pendientes
			$do->loaded=false;
			$this->rapyd->uri->un_set('modify');
			$this->rapyd->uri->set('create');
			return $this->_pre_insert($do,'I');
		}elseif((substr($numero,0,1)=='_' && $referendb=='P')){
			return $this->_pre_insert($do,'U');
		}else{
			$do->error_message_ar['pre_upd']='No se pueden modificar facturas guardadas';
			return false;
		}
	}


	function _pre_delete($do){
		//$do = new DataObject('sfac');
		//$do->rel_one_to_many('sitems', 'sitems', array('id'=>'id_sfac'));
		//$do->rel_one_to_many('sfpa'  , 'sfpa'  , array('numero','transac'));
		//$do->load($id);

		$tipo_doc = $do->get('tipo_doc');
		$numero   = $do->get('numero');
		$transac  = $do->get('transac');
		$referen  = $do->get('referen');
		$sprv     = $do->get('sprv');

		//Pasa si es una prefactura
		if($numero[0]=='_' && $tipo_doc=='F'){
			return true;
		}

		$fecha     = $do->get('fecha');
		$referen   = $do->get('referen');
		$cajero    = $do->get('cajero');
		$mandatario= $do->get('mandatario');
		$inicial   = floatval($do->get('inicial'));

		$dbtransac  = $this->db->escape($transac);
		$dbtipo_doc = $this->db->escape($tipo_doc);
		$dbnumero   = $this->db->escape($numero);
		$dbfecha    = $this->db->escape($fecha);
		$dbmandatario= $this->db->escape($mandatario);
		$hoy        = date('Y-m-d');

		if($tipo_doc=='X'){
			$do->error_message_ar['pre_del']='El documento ya esta anulada.';
			return false;
		}

		$mSQL ="SELECT abonos FROM smov WHERE numero=${dbnumero} AND fecha=${dbfecha} AND transac=${dbtransac}";
		$abono=floatval($this->datasis->dameval($mSQL));
		if($abono-$inicial>0){
			$do->error_message_ar['pre_del']='No se puede anular el documento por tener abonos.';
			return false;
		}

		if($fecha != $hoy){
			if($referen!='C'){
				$do->error_message_ar['pre_del']='No se puede anular documentos de dias pasados.';
				return false;
			}elseif($inicial>0 || $abono>0){
				$do->error_message_ar['pre_del']='No se puede anular el documento por tener abonos.';
				return false;
			}
		}

		if($tipo_doc=='F'){
			$mSQL = "SELECT COUNT(*) AS cana FROM sfac WHERE tipo_doc='D' AND factura=${dbnumero}";
			$cana = $this->datasis->dameval($mSQL);
			if($cana>0){
				$do->error_message_ar['pre_del'] = 'No se puede anular una factura con devolucion.';
				return false;
			}
		}elseif($tipo_doc=='T'){

			$mSQL ="SELECT abonos FROM smov WHERE cod_cli=${dbmandatario} AND fecha=${dbfecha} AND transac=${dbtransac} AND tipo_doc='ND'";
			$abono=floatval($this->datasis->dameval($mSQL));
			if($abono>0){
				$do->error_message_ar['pre_del']='No se puede anular el documento por tener abonos al mandatario.';
				return false;
			}
		}

		$tercero=false;
		if(!empty($sprv)){
			$dbsprv= $this->db->escape($sprv);
			$mSQL  = "SELECT abonos FROM sprm WHERE cod_prv=${dbsprv} AND fecha=${dbfecha} AND transac=${dbtransac}";
			$abono = floatval($this->datasis->dameval($mSQL));
			if($abono>0){
				$do->error_message_ar['pre_del'] = 'No se puede anular el documento por tener una cuenta a terceros abonada.';
				return false;
			}
			$tercero=true;
		}

		$mSQL="SELECT COUNT(*) AS cana
			FROM stra   AS a
			JOIN itstra AS b ON a.numero=b.numero
			JOIN sitems AS c ON b.codigo=c.codigoa AND c.numa=${dbnumero}
			WHERE a.envia='INFI' AND a.fecha>=${dbfecha}";
		$invfis=intval($this->datasis->dameval($mSQL));
		if($invfis>0){
			$do->error_message_ar['pre_del'] = 'No se puede anular el documento por tener un inventario físico intercalado, debe realizar la operación inversa.';
			return false;
		}

		//Descuento del inventario
		$factor=($tipo_doc=='F' || $tipo_doc=='T')? 1:-1;
		$almacen=$do->get('almacen');
		$dbalma = $this->db->escape($almacen);
		$cana=$do->count_rel('sitems');
		for($i=0;$i<$cana;$i++){
			$itcana    = $do->get_rel('sitems','cana',$i);
			$itcodigoa = $do->get_rel('sitems','codigoa',$i);
			$dbcodigoa = $this->db->escape($itcodigoa);

			$this->datasis->sinvcarga($itcodigoa, $almacen, $factor*$itcana);

		}

		if($tipo_doc=='D'){
			$dbnnumero= $this->db->escape('N'.substr($numero,1));


			//Deshace las aplicaciones del efecto a eliminar
			$mSQL = "SELECT tipo_doc,numero,numccli,tipoccli,monto,abono,cod_cli,fecha FROM itccli WHERE transac=${dbtransac}";
			$query = $this->db->query($mSQL);
			foreach($query->result() as $row){
				$it_tipo_doc= $this->db->escape($row->tipo_doc);
				$it_cod_cli = $this->db->escape($row->cod_cli);
				$it_numero  = $this->db->escape($row->numero);
				$it_fecha   = $this->db->escape($row->fecha);
				$it_abono   = floatval($row->abono);

				$mSQL="UPDATE smov SET abonos=abonos-(${it_abono}) WHERE tipo_doc=${it_tipo_doc} AND numero=${it_numero} AND cod_cli=${it_cod_cli}";
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'sfac'); }
			}
			//Fin

			$mSQL="DELETE FROM itccli WHERE transac=${dbtransac}";
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'sfac'); }

		}else if($tipo_doc=='T'){
			$dbnnumero= $this->db->escape('T'.substr($numero,1));
			$mSQL="DELETE FROM smov WHERE cod_cli=${dbmandatario} AND transac=${dbtransac} AND tipo_doc='ND'";
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'sfac'); }

		}else{
			$dbnnumero= $dbnumero;
		}

		if($tercero){
			$mSQL ="DELETE FROM sprm WHERE cod_prv=${dbsprv} AND fecha=${dbfecha} AND transac=${dbtransac}";
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'sfac'); }
		}

		$mSQL ="DELETE FROM smov WHERE numero=${dbnumero} AND fecha=${dbfecha} AND transac=${dbtransac}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sfac'); }

		$mSQL="DELETE FROM sfpa WHERE transac=${dbtransac} AND numero=${dbnumero}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sfac'); }

		$mSQL="UPDATE sfac SET tipo_doc='X', numero=${dbnnumero} WHERE tipo_doc=${dbtipo_doc} AND numero=${dbnumero}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sfac'); }

		$mSQL="UPDATE sitems SET tipoa='X', numa=${dbnnumero} WHERE tipoa=${dbtipo_doc} AND numa=${dbnumero}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sfac'); }

		logusu('sfac',"Anulo factura ${tipo_doc}${numero}");
		$do->error_message_ar['pre_del']='Factura '.$numero.' anulada';

		$upago   = trim($do->get('upago'));
		$cliente = trim($do->get('cod_cli'));
		if(!empty($upago)){
			$dbcliente = $this->db->escape($cliente);
			$dbupago   = $this->db->escape($upago);
			$mSQL = "UPDATE scli SET upago=${dbupago} WHERE cliente=${dbcliente}";
			$this->db->simple_query($mSQL);
		}

		//Chequea si es una venta vehicular
		if($this->db->table_exists('sinvehiculo') && $tipo_doc=='F'){
			$id=$do->get('id');
			$this->db->simple_query("UPDATE sinvehiculo SET id_sfac=NULL WHERE id_sfac=${id}");

		}
		return false;
	}

	function _post_insert($do){

		$numero  = $do->get('numero');
		$fecha   = $do->get('fecha');
		$vence   = $do->get('vence');
		$totneto = $do->get('totalg');
		$hora    = $do->get('hora');
		$usuario = $do->get('usuario');
		$transac = $do->get('transac');
		$nombre  = $do->get('nombre');
		$cod_cli = $do->get('cod_cli');
		$estampa = $do->get('estampa');
		$anticipo= round(floatval($do->get('inicial')),2);
		$referen = $do->get('referen');
		$tipo_doc= $do->get('tipo_doc');
		$iva     = $do->get('iva');
		$direc   = $do->get('direc');
		$dire1   = $do->get('dire1');
		$vd      = $do->get('vd');

		$exento   = $do->get('exento'   );
		$tasa     = $do->get('tasa'     );
		$reducida = $do->get('reducida' );
		$sobretasa= $do->get('sobretasa');
		$montasa  = $do->get('montasa'  );
		$monredu  = $do->get('monredu'  );
		$monadic  = $do->get('monadic'  );
		$maestra  = $do->get('maestra'  );
		$error    = 0;

		$dbcod_cli= $this->db->escape($cod_cli);
		$sql="UPDATE scli SET fecha1=LEAST(${fecha},fecha1), fecha2=${fecha} WHERE cliente=${dbcod_cli}";
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'sfac'); $error++;}

		//Si viene de pfac
		if(strlen($this->pfac)>7 && $tipo_doc == 'F'){
			$ent_saldos=array();
			$arr_pfac=explode('-',$this->pfac);
			foreach($arr_pfac as $pfac){
				if(strlen($pfac)>7){
					$dbpfac=$this->db->escape($pfac);
					$this->db->where('numero', $pfac);
					$this->db->update('pfac', array('factura' => $maestra,'status' => 'C'));

					$dbpfac=$this->db->escape($pfac);
					$sql="UPDATE itpfac AS c JOIN sinv   AS d ON d.codigo=c.codigoa
					SET d.exdes=IF(d.exdes>c.cana,d.exdes-c.cana,0)
					WHERE c.numa = ${dbpfac}";
					$ban=$this->db->simple_query($sql);
					if($ban==false){ memowrite($sql,'sfac'); $error++;}

					//Descuenta lo entregado
					$cana=$do->count_rel('sitems');
					for($i=0;$i<$cana;$i++){
						$itcana    = floatval($do->get_rel('sitems','cana',$i));
						$itcodigoa = $do->get_rel('sitems','codigoa',$i);
						$dbcodigoa = $this->db->escape($itcodigoa);

						if(!isset($ent_saldos[$itcodigoa])){
							$ent_saldos[$itcodigoa] = $itcana;
						}else{
							$ent_saldos[$itcodigoa]+= $itcana;
						}

						$drow=$this->datasis->damerow("SELECT entregado,cana FROM itpfac WHERE numa=${dbpfac} AND codigoa=${dbcodigoa}");
						if(!empty($drow)){
							$drow['cana']      = floatval($drow['cana']     );
							$drow['entregado'] = floatval($drow['entregado']);
							if($drow['entregado']< 0) $drow['entregado']=0;

							if($ent_saldos[$itcodigoa]+$drow['entregado'] > $drow['cana']){
								$itent=$drow['cana']-$drow['entregado'];
							}else{
								$itent=$ent_saldos[$itcodigoa];
							}
							$ent_saldos[$itcodigoa] = $ent_saldos[$itcodigoa]-$itent;

							$sql= "UPDATE itpfac SET entregado=IF(entregado+${itent}>cana,cana,entregado+${itent}) WHERE numa=${dbpfac} AND codigoa=${dbcodigoa}";
							$ban=$this->db->simple_query($sql);
							if($ban==false){ memowrite($sql,'sfac'); $error++;}
						}
					}

				}
			}
		}

		//Si viene de snte
		if(strlen($this->snte)>7 && $tipo_doc == 'F'){
			$arr_snte=explode('-',$this->snte);
			foreach($arr_snte as $snte){
				if(strlen($snte)>7){
					$this->db->where('numero', $snte);
					$this->db->update('snte' , array('factura' => $maestra,'tipo' => 'E'));
				}
			}
		}

		if($referen=='P'){
			logusu($do->table,"Creo pre-factura ${tipo_doc}${numero}");
			return true;
		}elseif($referen=='C'){
			$error   = 0;

			if($tipo_doc=='F'){
				//Inserta en smov
				$data=array();
				$data['cod_cli']  = $cod_cli;
				$data['nombre']   = $nombre;
				$data['dire1']    = $direc;
				$data['dire2']    = $dire1;
				$data['tipo_doc'] = 'FC';
				$data['numero']   = $numero;
				$data['fecha']    = $fecha;
				$data['monto']    = $totneto;
				$data['impuesto'] = $iva;
				$data['abonos']   = $anticipo;
				$data['vence']    = $vence;
				$data['tipo_ref'] = '';
				$data['num_ref']  = '';
				$data['observa1'] = 'FACTURA DE CREDITO';
				$data['estampa']  = $estampa;
				$data['hora']     = $hora;
				$data['transac']  = $transac;
				$data['usuario']  = $usuario;
				$data['codigo']   = '';
				$data['descrip']  = '';
				$data['montasa']  = $montasa;
				$data['monredu']  = $monredu;
				$data['monadic']  = $monadic;
				$data['tasa']     = $tasa;
				$data['reducida'] = $reducida;
				$data['sobretasa']= $sobretasa;
				$data['exento']   = $exento;
				$data['vendedor'] = $vd;


				$sql= $this->db->insert_string('smov', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'sfac'); $error++;}

				//Chequea si debe crear el abono

				if($anticipo>0){
					$mnumab = $this->datasis->fprox_numero('nabcli');

					$data=array();
					$data['cod_cli']  = $cod_cli;
					$data['nombre']   = $nombre;
					$data['dire1']    = $direc;
					$data['dire2']    = $dire1;
					$data['tipo_doc'] = 'AB';
					$data['numero']   = $mnumab;
					$data['fecha']    = $fecha;
					$data['monto']    = $anticipo;
					$data['impuesto'] = 0;
					$data['vence']    = $fecha;
					$data['tipo_ref'] = 'FC';
					$data['num_ref']  = $numero;
					$data['observa1'] = 'ABONO POR INCIAL DE FACTURA '.$numero;
					$data['usuario']  = $usuario;
					$data['estampa']  = $estampa;
					$data['hora']     = $hora;
					$data['transac']  = $transac;
					$data['fecdoc']   = $fecha;

					$mSQL = $this->db->insert_string('smov', $data);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'sfac'); }

					//Aplica la AB a la FC
					$data=array();
					$data['numccli']    = $mnumab; //numero abono
					$data['tipoccli']   = 'AB';
					$data['cod_cli']    = $cod_cli;
					$data['tipo_doc']   = ($tipo_doc=='F')? 'FC' : 'DV';
					$data['numero']     = $numero;
					$data['fecha']      = $fecha;
					$data['monto']      = $totneto;
					$data['abono']      = $anticipo;
					$data['ppago']      = 0;
					$data['reten']      = 0;
					$data['cambio']     = 0;
					$data['mora']       = 0;
					$data['transac']    = $transac;
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['usuario']    = $usuario;
					$data['reteiva']    = 0;
					$data['nroriva']    = '';
					$data['emiriva']    = '';
					$data['recriva']    = '';

					$mSQL = $this->db->insert_string('itccli', $data);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'sfac');}
				}
			}else{ //Si es devolucion
				$factura   = $do->get('factura');
				$dbfactura = $this->db->escape($factura);
				$debe      = $this->datasis->dameval("SELECT monto-abonos AS saldo FROM smov WHERE tipo_doc='FC' AND numero=${dbfactura}");
				$haber     = $totneto;
				if(empty($debe)) $debe=0;

				if($debe >= $haber){
					$abono=$haber;
				}else{
					$abono=$debe;
				}

				$data=array();
				$data['cod_cli']    = $cod_cli;
				$data['nombre']     = $nombre;
				$data['dire1']      = $direc;
				$data['dire2']      = $dire1;
				$data['tipo_doc']   = 'NC';
				$data['numero']     = $numero;
				$data['fecha']      = $fecha;
				$data['monto']      = $totneto;
				$data['impuesto']   = $iva;
				$data['abonos']     = $abono;
				$data['vence']      = $fecha;
				$data['tipo_ref']   = 'DV';
				$data['num_ref']    = $numero;
				$data['observa1']   = 'POR DEVOLUCION DE '.$factura ;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['transac']    = $transac;
				$data['usuario']    = $usuario;
				$data['codigo']     = '';
				$data['descrip']    = '';

				$mSQL = $this->db->insert_string('smov', $data);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'sfac');}

				if($abono>0){
					//Aplica la NC a la FC
					$data=array();
					$data['numccli']    = $numero;
					$data['tipoccli']   = 'NC';
					$data['cod_cli']    = $cod_cli;
					$data['tipo_doc']   = 'FC';
					$data['numero']     = $factura;
					$data['fecha']      = $fecha;
					$data['monto']      = $totneto;
					$data['abono']      = $abono;
					$data['ppago']      = 0;
					$data['reten']      = 0;
					$data['cambio']     = 0;
					$data['mora']       = 0;
					$data['transac']    = $transac;
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['usuario']    = $usuario;
					$data['reteiva']    = 0;
					$data['nroriva']    = '';
					$data['emiriva']    = '';
					$data['recriva']    = '';

					$sql= $this->db->insert_string('itccli', $data);
					$ban=$this->db->simple_query($sql);
					if($ban==false){ memowrite($sql,'sfac'); $error++;}

					$dbcod_cli = $this->db->escape($cod_cli);
					$sql="UPDATE smov SET abonos=abonos+${abono} WHERE tipo_doc='FC' AND cod_cli=${dbcod_cli} AND numero=${dbfactura}";
					$ban=$this->db->simple_query($sql);
					if($ban==false){ memowrite($sql,'sfac'); $error++;}
				}
			}
		}

		//Descuento del inventario
		$almacen=$do->get('almacen');
		$dbalma = $this->db->escape($almacen);
		$cana=$do->count_rel('sitems');
		for($i=0;$i<$cana;$i++){
			$itcana    = $do->get_rel('sitems','cana',$i);
			$itcodigoa = $do->get_rel('sitems','codigoa',$i);
			$dbcodigoa = $this->db->escape($itcodigoa);

			$factor=($tipo_doc=='F')? -1:1;

			if(!(strlen($this->snte)>7 && $tipo_doc == 'F')){
				$this->datasis->sinvcarga($itcodigoa, $almacen, $factor*$itcana);
			}
		}

		//Chequea si es una venta vehicular
		if($this->db->table_exists('sinvehiculo') && $tipo_doc=='D'){
			$factura  =$do->get('factura');
			$dbfactura=$this->db->escape($factura);
			$id=$this->datasis->dameval("SELECT id FROM sfac WHERE numero=${dbfactura} AND tipo_doc='F'");
			if(!empty($id)){
				$this->db->simple_query("UPDATE sinvehiculo SET id_sfac=NULL WHERE id_sfac=${id}");
			}
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits ${tipo_doc}${numero}");

		if($this->_creanfac){
			//Realiza el corte por maxlin ****
			$maxlin=intval($this->datasis->traevalor('MAXLIN'));
			for($i=0;$i<$maxlin;$i++){
				unset($_POST["codigoa_$i"]);
				unset($_POST["desca_$i"]);
				unset($_POST["cana_$i"]);
				unset($_POST["preca_$i"]);
				unset($_POST["detalle_$i"]);
				unset($_POST["tota_$i"]);
				unset($_POST["precio1_$i"]);
				unset($_POST["precio2_$i"]);
				unset($_POST["precio3_$i"]);
				unset($_POST["precio4_$i"]);
				unset($_POST["itiva_$i"]);
				unset($_POST["sinvpeso_$i"]);
				unset($_POST["sinvtipo_$i"]);
				unset($_POST["combo_$i"]);
				unset($_POST["lote_$i"]);
				unset($_POST["descu_$i"]);
				unset($_POST["mostrado_$i"]);
				unset($_POST["tota_$i"]);
			}
			//Fin del corte por maxlin


			//Realiza el corte de pago por maxlin
			$cana = $do->count_rel('sfpa');

			$lasid= ($cana>0)? $cana-1 : 0;

			$monto  = floatval($_POST["monto_${lasid}"]);
			$ajmonto= floatval($do->get_rel('sfpa','monto',$lasid));

			if($cana>0){
				if($monto>$ajmonto){
					$_POST["monto_${lasid}"]=$monto-$ajmonto;
				}else{
					unset($_POST["tipo_${lasid}"]);
					//unset($_POST["sfpafecha_${lasid}"]);
					unset($_POST["num_ref_${lasid}"]);
					unset($_POST["banco_${lasid}"]);
					unset($_POST["monto_${lasid}"]);
				}
			}else{
				//$_POST["tipo_${lasid}"]      ='';
				//$_POST["sfpafecha_${lasid}"] ='';
				//$_POST["num_ref_${lasid}"]   ='';
				//$_POST["banco_${lasid}"]     ='';
				$_POST["monto_${lasid}"]     =$_POST['totalg']-$do->get('totalg');
			}

			if($lasid>0){
				for($i=0;$i<$lasid;$i++){
					unset($_POST["tipo_${i}"]);
					//unset($_POST["sfpafecha_${i}"]);
					unset($_POST["num_ref_${i}"]);
					unset($_POST["banco_${i}"]);
					unset($_POST["monto_${i}"]);
				}
			}
			$_POST['totals']=$_POST['totals']-$do->get('totals');
			$_POST['totalg']=$_POST['totalg']-$do->get('totalg');
			$_POST['iva']   =$_POST['iva']-$do->get('iva');
			//Fin del corte de pago por maxlin

			//Limpia las validaciones
			$this->validation->_error_array    = array();
			$this->validation->_rules          = array();
			$this->validation->_fields         = array();
			$this->validation->_error_messages = array();
			//Fin de la limpieza de validaciones

			if(!isset($this->_sfacmaestra)){
				$this->_sfacmaestra=$do->get('numero');
			}

			$this->dataedit();
		}
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		//logusu($do->table,"Modifico $this->tits ${primary} ");
		$this->_post_insert($do);
	}

	function _post_delete($do){
		$numero   = $do->get('numero');
		$tipo_doc = $do->get('tipo_doc');

		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino ${tipo_doc}${numero} $this->tits $primary ");
	}

	function _post_print_update($do){
		$numero   = $do->get('numero');
		$tipo_doc = $do->get('tipo_doc');
		$nfiscal  = $do->get('nfiscal');

		logusu($do->table,"Imprimio ${tipo_doc}${numero} factura $nfiscal");
	}

	//******************************************************************
	// Crea una factura desde un pedido
	//
	function creafrompfac($manual,$numero,$status=null){
		$this->_url = $this->url.'dataedit/insert';

		$sel=array('a.cod_cli','b.nombre','b.tipo','b.rifci','b.dire11 AS direc'
		,'a.totals','a.iva','a.totalg','TRIM(a.factura) AS factura','a.vd','c.almacen','a.bultos');
		$this->db->select($sel);
		$this->db->from('pfac AS a');
		$this->db->join('scli AS b','a.cod_cli=b.cliente');
		$this->db->join('vend AS c','c.vendedor=a.vd','left');
		$this->db->where('a.numero',$numero);
		$this->db->where_in('a.status',array('P','A'));
		$query = $this->db->get();

		if ($query->num_rows() > 0 && $status=='create'){
			$row = $query->row();
			if(empty($row->factura)){
				$_POST=array(
					'btn_submit' => 'Guardar',
					'fecha'      => inputDateFromTimestamp(mktime(0,0,0)),
					'cajero'     => trim($this->secu->getcajero()),
					'vd'         => (empty($row->vd))? trim($this->secu->getvendedor()) :  trim($row->vd),
					'almacen'    => (empty($row->almacen))? trim($this->secu->getalmacen()) : trim($row->almacen),
					'tipo_doc'   => 'F',
					'factura'    => '',
					'cod_cli'    => $row->cod_cli,
					'sclitipo'   => (intval($row->tipo)>4)? 4 : (intval($row->tipo)==0)? 1 : $row->tipo,
					'nombre'     => rtrim($row->nombre),
					'rifci'      => $row->rifci,
					'direc'      => rtrim($row->direc),
					'totals'     => $row->totals,
					'iva'        => $row->iva,
					'totalg'     => $row->totalg,
					'pfac'       => $numero,
				);

				$itsel=array('a.codigoa','b.descrip AS desca','a.cana - a.entregado AS cana','a.preca','a.tota','b.iva',
				'ROUND(b.precio1*100/(100+b.iva),2) AS precio1',
				'ROUND(b.precio2*100/(100+b.iva),2) AS precio2',
				'ROUND(b.precio3*100/(100+b.iva),2) AS precio3',
				'ROUND(b.precio4*100/(100+b.iva),2) AS precio4','b.tipo','b.peso');
				$this->db->select($itsel);
				$this->db->from('itpfac AS a');
				$this->db->join('sinv AS b','b.codigo=a.codigoa');
				$this->db->where('a.numa',$numero);
				$this->db->where('a.cana >',0);
				$qquery = $this->db->get();
				$i=0;

				foreach ($qquery->result() as $itrow){
					$_POST["codigoa_${i}"]  = rtrim($itrow->codigoa);
					$_POST["lote_${i}"]     = '0';
					$_POST["desca_${i}"]    = rtrim($itrow->desca);
					$_POST["cana_${i}"]     = $itrow->cana;
					$_POST["preca_${i}"]    = $itrow->preca;
					$_POST["tota_${i}"]     = $itrow->tota;
					$_POST["precio1_${i}"]  = $itrow->precio1;
					$_POST["precio2_${i}"]  = $itrow->precio2;
					$_POST["precio3_${i}"]  = $itrow->precio3;
					$_POST["precio4_${i}"]  = $itrow->precio4;
					$_POST["descu_${i}"]    = 0;
					$_POST["itiva_${i}"]    = $itrow->iva;
					$_POST["sinvpeso_${i}"] = $itrow->peso;
					$_POST["sinvtipo_${i}"] = $itrow->tipo;
					$_POST["detalle_${i}"]  = '';
					$_POST["combo_$i"]      = '';
					$i++;
				}

				// SFAC
				$_POST['bultos'] = $row->bultos;
				$i=0;
				$_POST["tipo_${i}"]      = '';
				//$_POST["sfpafecha_${i}"] = '';
				$_POST["num_ref_${i}"]   = '';
				$_POST["banco_${i}"]     = '';
				$_POST["monto_${i}"]     = 0;

				$this->dataedit();
			}else{
				echo 'Pedido ya facturado';
			}
		}else{
			echo 'Pedido cerrado o ya fue facturado';
		}
	}

	//******************************************************************
	// Crea una factura desde un presupuesto
	//
	function creafromspre($manual,$numero,$status=null){
		$this->_url= $this->url.'dataedit/insert';

		$sel=array('a.cod_cli','b.nombre','b.tipo','b.rifci','b.dire11 AS direc'
		,'a.totals','a.iva','a.totalg');
		$this->db->select($sel);
		$this->db->from('spre AS a');
		$this->db->join('scli AS b','a.cod_cli=b.cliente','left');
		$this->db->where('a.numero',$numero);
		$query = $this->db->get();

		if ($query->num_rows() > 0 && $status=='create'){
			$row = $query->row();

			$_POST=array(
				'btn_submit' => 'Guardar',
				'fecha'      => inputDateFromTimestamp(mktime(0,0,0)),
				'cajero'     => $this->secu->getcajero(),
				'vd'         => $this->secu->getvendedor(),
				'almacen'    => $this->secu->getalmacen(),
				'tipo_doc'   => 'F',
				'factura'    => '',
				'cod_cli'    => $row->cod_cli,
				'sclitipo'   => $row->tipo,
				'nombre'     => rtrim($row->nombre),
				'rifci'      => $row->rifci,
				'direc'      => rtrim($row->direc),
				'totals'     => $row->totals,
				'iva'        => $row->iva,
				'totalg'     => $row->totalg,
				'pfac'       => $numero,
				'bultos'     => 0,
			);

			$itsel=array('a.codigo','b.descrip AS desca','a.cana','a.preca','a.importe AS tota','b.iva',
			'b.precio1','b.precio2','b.precio3','b.precio4','b.tipo','b.peso');
			$this->db->select($itsel);
			$this->db->from('itspre AS a');
			$this->db->join('sinv AS b','b.codigo=a.codigo');
			$this->db->where('a.numero',$numero);
			$this->db->where('a.cana >','0');
			$qquery = $this->db->get();
			$i=0;

			foreach ($qquery->result() as $itrow){
				$_POST["codigoa_${i}"]  = rtrim($itrow->codigo);
				$_POST["lote_${i}"]     = '0';
				$_POST["desca_${i}"]    = rtrim($itrow->desca);
				$_POST["cana_${i}"]     = $itrow->cana;
				$_POST["preca_${i}"]    = $itrow->preca;
				$_POST["tota_${i}"]     = $itrow->tota;
				$_POST["precio1_${i}"]  = $itrow->precio1;
				$_POST["precio2_${i}"]  = $itrow->precio2;
				$_POST["precio3_${i}"]  = $itrow->precio3;
				$_POST["precio4_${i}"]  = $itrow->precio4;
				$_POST["descu_${i}"]    = 0;
				$_POST["itiva_${i}"]    = $itrow->iva;
				$_POST["sinvpeso_${i}"] = $itrow->peso;
				$_POST["sinvtipo_${i}"] = $itrow->tipo;
				$_POST["detalle_${i}"]  = '';
				$_POST["combo_$i"]      = '';
				$i++;
			}

			//sfpa
			$i=0;
			$_POST["tipo_${i}"]      = '';
			//$_POST["sfpafecha_${i}"] = '';
			$_POST["num_ref_${i}"]   = '';
			$_POST["banco_${i}"]     = '';
			$_POST["monto_${i}"]     = 0;

			$this->dataedit();
		}else{
			echo 'Presupuesto no existe';
		}
	}

	//******************************************************************
	// Crea una factura desde un presupuesto
	function creafromspreml( $numero, $status=null ){
		$this->_url= $this->url.'dataedit/insert';

		$this->db->from('spreml AS a');
		$this->db->where('a.numero',$numero);
		$query = $this->db->get();
		if ($query->num_rows() <> 1 ){
			echo 'Orden no existe '.$numero;
			return false;
		}

		// Crea Cliente si no existe
		$spreml   = $query->row();
		$rifci    = $spreml->rifci;
		$mSQL = 'SELECT count(*) FROM scli WHERE rifci='.$this->db->escape($rifci);
		$hay = $this->datasis->dameval($mSQL);
		if ($hay == 0 ){
			//CREA EL CLIENTE
			$cod_cli = $this->datasis->proxcli($spreml->rifci);
			$data = array (
				'cliente'    => $cod_cli,
				'rifci'      => $spreml->rifci,
				'nombre'     => $spreml->nombre,
				'nomfis'     => $spreml->nombre,
				'mercalib'   => $spreml->mercalib,
				'estado'     => $spreml->estado,
				'tipo'       => '1',
				'tiva'       => 'N',
				'zona'       => $this->datasis->traevalor('ZONAXDEFECTO'),
				'grupo'      => $this->datasis->dameval('SELECT grupo FROM grcl WHERE gr_desc like "%CONSUMIDOR%" OR gr_desc like "%FINAL%" OR gr_desc like "%PARTICULAR%"'),
				'socio'      => $row->estado,
				'dire11'     => substr($spreml->direccion,0,40),
				'dire12'     => substr($spreml->direccion,40,40),
				'ciudad1'    => $spreml->ciudad,
				'telefono'   => $spreml->telefono,
				'fb'         => '',
				'pin'        => '',
				'email'      => $spreml->email,
				'twitter'    => '',
				'cuenta'     => $this->datasis->dameval('SELECT cuenta FROM grcl WHERE gr_desc like "CONSUMIDOR FINAL%"'),
				'observa'    => ''
				);
				$this->db->insert('scli',$data);
		} else {
			$mSQL    = 'SELECT cliente FROM scli WHERE rifci='.$this->db->escape($rifci);
			$cod_cli = $this->datasis->dameval($mSQL);
		}
		// Coloca el cliente en spre
		$this->db->where('numero',$numero);
		$this->db->update('spre',array('cod_cli'=>$cod_cli));

		$sel=array('a.cod_cli','b.nombre','b.tipo','b.rifci','b.dire11 AS direc'
		,'a.totals','a.iva','a.totalg');
		$this->db->select($sel);
		$this->db->from('spre AS a');
		$this->db->join('scli AS b','a.cod_cli=b.cliente','left');
		$this->db->where('a.numero',$numero);
		$query = $this->db->get();

		if ($query->num_rows() > 0 && $status=='insert'){
			$row = $query->row();
			$_POST=array(
				'btn_submit' => 'Guardar',
				'fecha'      => inputDateFromTimestamp(mktime(0,0,0)),
				'cajero'     => $this->secu->getcajero(),
				'vd'         => $this->secu->getvendedor(),
				'almacen'    => $this->secu->getalmacen(),
				'tipo_doc'   => 'F',
				'factura'    => '',
				'cod_cli'    => $row->cod_cli,
				'sclitipo'   => $row->tipo,
				'nombre'     => rtrim($row->nombre),
				'rifci'      => $row->rifci,
				'direc'      => rtrim($row->direc),
				'totals'     => $row->totals,
				'iva'        => $row->iva,
				'totalg'     => $row->totalg,
				'pfac'       => $numero,
				'referen'    => 'M',
				'snte'       => ''
			);

			$itsel=array('a.codigo','b.descrip AS desca','a.cana','a.preca','a.importe AS tota','b.iva',
			'b.precio1','b.precio2','b.precio3','b.precio4','b.tipo','b.peso');
			$this->db->select($itsel);
			$this->db->from('itspre AS a');
			$this->db->join('sinv AS b','b.codigo=a.codigo');
			$this->db->where('a.numero',$numero);
			$this->db->where('a.cana >','0');
			$qquery = $this->db->get();
			$i=0;
			foreach ($qquery->result() as $itrow){
				$_POST["codigoa_${i}"]  = rtrim($itrow->codigo);
				$_POST["lote_${i}"]     = '0';
				$_POST["desca_${i}"]    = rtrim($itrow->desca);
				$_POST["cana_${i}"]     = $itrow->cana;
				$_POST["preca_${i}"]    = $itrow->preca;
				$_POST["tota_${i}"]     = $itrow->tota;
				$_POST["precio1_${i}"]  = $itrow->precio1;
				$_POST["precio2_${i}"]  = $itrow->precio2;
				$_POST["precio3_${i}"]  = $itrow->precio3;
				$_POST["precio4_${i}"]  = $itrow->precio4;
				$_POST["descu_${i}"]    = 0;
				$_POST["itiva_${i}"]    = $itrow->iva;
				$_POST["sinvpeso_${i}"] = $itrow->peso;
				$_POST["sinvtipo_${i}"] = $itrow->tipo;
				$_POST["detalle_${i}"]  = '';
				$_POST["combo_${i}"]    = '';
				$i++;
			}
			// SFPA
			$i=0;
			$_POST["tipo_${i}"]      = $spreml->tipo_op;
			$_POST["sfpafecha_${i}"] = $spreml->fechadep;
			$_POST["num_ref_${i}"]   = $spreml->num_ref;
			$_POST["banco_${i}"]     = $spreml->codbanc;
			$_POST["monto_${i}"]     = $row->totalg;

			ob_start();
				$this->dataedit();
				$rt = ob_get_contents();
			@ob_end_clean();
			$id=0;
			$getdata=json_decode($rt,true);
			if($getdata['status']=='A'){
				$id = $getdata['pk']['id'];
				$transac = $this->datasis->damerow("SELECT usuario, transac, estampa, hora FROM sfac WHERE id=".$id);

				$this->db->where('numero',$numero);
				$this->db->update("spreml",array( 'status'=>'F','transac'=>$transac['transac']));

				// Guarda el deposito
				$sfpatipo  = $spreml->tipo_op;
				$codbanc   = $spreml->codbanc;
				$monto     = $spreml->totalg;
				$fecha     = $spreml->fechadep;
				$num_ref   = $spreml->num_ref;
				$dbcodbanc = $this->db->escape($codbanc);

				// Crea en bmov el movimiento
				$sql ='SELECT tbanco,moneda,banco,saldo,depto,numcuent FROM banc WHERE codbanc='.$dbcodbanc;
				$fila=$this->datasis->damerow($sql);

				$ffecha  = $fecha;
				$itdbdata=array();
				$itdbdata['codbanc']  = $codbanc;
				$itdbdata['moneda']   = $fila['moneda'];
				$itdbdata['numcuent'] = $fila['numcuent'];
				$itdbdata['banco']    = $fila['banco'];
				$itdbdata['saldo']    = $monto;
				$itdbdata['tipo_op']  = $sfpatipo;
				$itdbdata['numero']   = $num_ref;
				$itdbdata['fecha']    = $fecha;
				$itdbdata['clipro']   = 'C';
				$itdbdata['codcp']    = $cod_cli;
				$itdbdata['nombre']   = $spreml->nombre;
				$itdbdata['monto']    = $monto;
				$itdbdata['bruto']    = $monto;
				$itdbdata['concepto'] = 'INGRESO POR ORDEN ML';
				$itdbdata['concep2']  = '';
				$itdbdata['status']   = 'P';
				$itdbdata['liable']   = 'S';

				$itdbdata['transac']  = $transac['transac'];
				$itdbdata['usuario']  = $transac['usuario'];
				$itdbdata['estampa']  = $transac['estampa'];
				$itdbdata['hora']     = $transac['hora'];

				$itdbdata['negreso']  = '';
				$itdbdata['anulado']  = 'N';
				$mSQL = $this->db->insert_string('bmov', $itdbdata);
				$ban=$this->db->simple_query($mSQL);
				//if($ban==false){ memowrite($mSQL,'ccli'); }

				$fecha=str_replace( '-', '', $fecha );
				$this->datasis->actusal($codbanc, $fecha, $monto);

				echo $rt;
			}else{
				echo $rt;
			}
		}else{
			echo 'Orden no existe';
		}

	}

	//******************************************************************
	// Crea una factura desde una nota de entrega
	function creafromsnte($manual,$numero,$status=null){
		$this->_url= $this->url.'dataedit/insert';

		$sel=array('a.cod_cli','b.nombre','b.tipo','b.rifci','b.dire11 AS direc'
		,'a.stotal AS totals','a.impuesto AS iva','a.gtotal AS totalg','TRIM(a.factura) AS factura','a.vende AS vd','c.almacen');
		$this->db->select($sel);
		$this->db->from('snte AS a');
		$this->db->join('scli AS b','a.cod_cli=b.cliente');
		$this->db->join('vend AS c','c.vendedor=a.vende','left');
		$this->db->where('a.numero',$numero);
		$this->db->where('a.tipo','E');
		$query = $this->db->get();

		if ($query->num_rows() > 0 && $status=='create'){
			$row = $query->row();
			if(empty($row->factura)){
				$_POST=array(
					'btn_submit' => 'Guardar',
					'fecha'      => inputDateFromTimestamp(mktime(0,0,0)),
					'cajero'     => $this->secu->getcajero(),
					'vd'         => (empty($row->vd))? $this->secu->getvendedor() :  $row->vd,
					'almacen'    => (empty($row->almacen))? $this->secu->getalmacen() : $row->almacen,
					'tipo_doc'   => 'F',
					'factura'    => '',
					'cod_cli'    => $row->cod_cli,
					'sclitipo'   => $row->tipo,
					'nombre'     => rtrim($row->nombre),
					'rifci'      => $row->rifci,
					'direc'      => rtrim($row->direc),
					'totals'     => $row->totals,
					'iva'        => $row->iva,
					'totalg'     => $row->totalg,
					'snte'       => $numero,
				);

				$itsel=array('a.codigo AS codigoa','b.descrip AS desca','a.cana','a.precio AS preca','a.importe AS tota','b.iva',
				'b.precio1','b.precio2','b.precio3','b.precio4','b.tipo','b.peso');
				$this->db->select($itsel);
				$this->db->from('itsnte AS a');
				$this->db->join('sinv AS b','b.codigo=a.codigo');
				$this->db->where('a.numero',$numero);
				$this->db->where('a.cana >','0');
				$qquery = $this->db->get();
				$i=0;

				foreach ($qquery->result() as $itrow){
					$_POST["codigoa_${i}"]  = rtrim($itrow->codigoa);
					$_POST["lote_${i}"]     = 0;
					$_POST["desca_${i}"]    = rtrim($itrow->desca);
					$_POST["cana_${i}"]     = $itrow->cana;
					$_POST["preca_${i}"]    = $itrow->preca;
					$_POST["tota_${i}"]     = $itrow->tota;
					$_POST["precio1_${i}"]  = $itrow->precio1;
					$_POST["precio2_${i}"]  = $itrow->precio2;
					$_POST["precio3_${i}"]  = $itrow->precio3;
					$_POST["precio4_${i}"]  = $itrow->precio4;
					$_POST["descu_${i}"]    = 0;
					$_POST["itiva_${i}"]    = $itrow->iva;
					$_POST["sinvpeso_${i}"] = $itrow->peso;
					$_POST["sinvtipo_${i}"] = $itrow->tipo;
					$_POST["detalle_${i}"]  = '';
					$_POST["combo_$i"]      = '';
					$i++;
				}

				//sfpa
				$i=0;
				$_POST["tipo_${i}"]      = '';
				//$_POST["sfpafecha_${i}"] = '';
				$_POST["num_ref_${i}"]   = '';
				$_POST["banco_${i}"]     = '';
				$_POST["monto_${i}"]     = 0;

				$this->dataedit();
			}else{
				echo 'Nota de entrega ya facturada';
			}
		}else{
			echo 'Nota de entrega no encontrada';
		}
	}

	//******************************************************************
	// Crea una factura desde varias nota de entrega
	function creafromsntes($manual,$numero,$status=null){
		$this->_url= $this->url.'dataedit/insert';
		$ids=explode('-',$numero);

		$sel=array('a.cod_cli','b.nombre','b.tipo','b.rifci','b.dire11 AS direc'
		,'SUM(a.stotal) AS totals','SUM(a.impuesto) AS iva','SUM(a.gtotal) AS totalg','GROUP_CONCAT(TRIM(a.factura) SEPARATOR "") AS factura',
		'a.vende AS vd','c.almacen','GROUP_CONCAT(TRIM(a.numero) SEPARATOR "-") AS numeros');
		$this->db->select($sel);
		$this->db->from('snte AS a');
		$this->db->join('scli AS b','a.cod_cli=b.cliente');
		$this->db->join('vend AS c','c.vendedor=a.vende','left');
		$this->db->where_in('a.id',$ids);
		$this->db->group_by('a.cod_cli');
		$this->db->where('a.tipo','E');
		$query = $this->db->get();

		if ($query->num_rows() > 0 && $status=='create'){
			if($query->num_rows()>1){
				echo 'Las notas no pertenecen a un mismo cliente';
				return false;
			}

			$row = $query->row();
			if(empty($row->factura)){

				$_POST=array(
					'btn_submit' => 'Guardar',
					'fecha'      => inputDateFromTimestamp(mktime(0,0,0)),
					'cajero'     => $this->secu->getcajero(),
					'vd'         => (empty($row->vd))? $this->secu->getvendedor() :  $row->vd,
					'almacen'    => (empty($row->almacen))? $this->secu->getalmacen() : $row->almacen,
					'tipo_doc'   => 'F',
					'factura'    => '',
					'cod_cli'    => $row->cod_cli,
					'sclitipo'   => $row->tipo,
					'nombre'     => rtrim($row->nombre),
					'rifci'      => $row->rifci,
					'direc'      => rtrim($row->direc),
					'totals'     => $row->totals,
					'iva'        => $row->iva,
					'totalg'     => $row->totalg,
					'snte'       => $row->numeros,
				);

				$itsel=array('a.codigo AS codigoa','b.descrip AS desca','SUM(a.cana) AS cana','SUM(a.importe) AS tota',
				'b.iva','b.precio1','b.precio2','b.precio3','b.precio4','b.tipo','b.peso');
				$this->db->select($itsel);
				$this->db->from('snte AS c');
				$this->db->join('itsnte AS a','a.numero=c.numero');
				$this->db->join('sinv AS b','b.codigo=a.codigo');
				$this->db->where_in('c.id',$ids);
				$this->db->where('a.cana >','0');
				$this->db->group_by('a.codigo');
				$qquery = $this->db->get();
				$i=0;

				foreach ($qquery->result() as $itrow){
					$preca=round($itrow->tota/$itrow->cana,2);

					$_POST["codigoa_${i}"]  = rtrim($itrow->codigoa);
					$_POST["lote_${i}"]     = 0;
					$_POST["desca_${i}"]    = rtrim($itrow->desca);
					$_POST["cana_${i}"]     = $itrow->cana;
					$_POST["preca_${i}"]    = $preca;
					$_POST["tota_${i}"]     = $preca*$itrow->cana;
					$_POST["precio1_${i}"]  = $itrow->precio1;
					$_POST["precio2_${i}"]  = $itrow->precio2;
					$_POST["precio3_${i}"]  = $itrow->precio3;
					$_POST["precio4_${i}"]  = $itrow->precio4;
					$_POST["descu_${i}"]    = 0;
					$_POST["itiva_${i}"]    = $itrow->iva;
					$_POST["sinvpeso_${i}"] = $itrow->peso;
					$_POST["sinvtipo_${i}"] = $itrow->tipo;
					$_POST["detalle_${i}"]  = '';
					$_POST["combo_$i"]      = '';
					$i++;
				}

				//sfpa
				$i=0;
				$_POST["tipo_${i}"]      = '';
				//$_POST["sfpafecha_${i}"] = '';
				$_POST["num_ref_${i}"]   = '';
				$_POST["banco_${i}"]     = '';
				$_POST["monto_${i}"]     = 0;

				$this->dataedit();
			}else{
				echo 'Algunas notas ya estan facturadas '.$row->numeros;
			}
		}else{
			echo 'Nota de entrega no encontrada';
		}
	}

	//******************************************************************
	// Crea una factura desde una nota de entrega
	function creafrommovil($manual,$status=null){
		$this->_url= $this->url.'dataedit/insert';
		$this->genesal=false;

		$rt=array(
			'status' => 'B',
			'mensaje'=> 'Debe asignarle un cajero al usuario',
			'pk'     => '',
			'manual' => $manual,
			'vuelto' => 0.00
		);

		$cajero = $this->secu->getcajero();
		$vende  = $this->secu->getvendedor();
		$almace = $this->secu->getalmacen();

		if(empty($cajero )){
			$rt['mensaje'] = 'Debe asignarle un cajero al usuario';
			echo json_encode($rt);
			return false;
		}

		if(empty($vende )){
			$rt['mensaje'] = 'Debe asignarle un vendedor al usuario';
			echo json_encode($rt);
			return false;
		}

		if(empty($almace)){
			$rt['mensaje'] = 'Debe asignarle un almacen al usuario';
			echo json_encode($rt);
			return false;
		}

		$post  = $_POST;
		$_POST=array(
			'btn_submit' => 'Guardar',
			'fecha'      => inputDateFromTimestamp(mktime(0,0,0)),
			'cajero'     => $cajero,
			'vd'         => $vende ,
			'almacen'    => $almace,
			'tipo_doc'   => 'F',
			'factura'    => '',
			'referen'    => 'P',
			'cod_cli'    => $post['scli']['cliente'],
			'sclitipo'   => $post['scli']['tipo'],
			'nombre'     => $post['scli']['nombre'],
			'rifci'      => $post['scli']['rifci'],
			'direc'      => $post['scli']['direc'],
			'totals'     => 0,
			'iva'        => 0,
			'totalg'     => 0,
		);
		$tipop=intval($post['scli']['tipo']);

		$i=$totals=$totalg=$iva=0;
		foreach($post['sitems'] as $val){
			if($tipop==4){
				$preca=floatval($val['base4']);
			}elseif($tipop==3){
				$preca=floatval($val['base3']);
			}elseif($tipop==2){
				$preca=floatval($val['base4']);
			}else{
				$preca=floatval($val['base1']);
			}
			$tota = round($preca*floatval($val['cana']),2);

			$iiva   = round($tota*($val['iva']/100),2);
			$iva    += $iiva;
			$totals += $tota;
			$totalg += $tota+$iiva ;

			$_POST["codigoa_${i}"]  = rtrim($val['codigo']);
			$_POST["lote_${i}"]     = 0;
			$_POST["desca_${i}"]    = rtrim($val['descrip']);
			$_POST["cana_${i}"]     = $val['cana'];
			$_POST["preca_${i}"]    = $preca;
			$_POST["tota_${i}"]     = $tota;
			$_POST["precio1_${i}"]  = $val['base1'];
			$_POST["precio2_${i}"]  = $val['base2'];
			$_POST["precio3_${i}"]  = $val['base3'];
			$_POST["precio4_${i}"]  = $val['base4'];
			$_POST["descu_${i}"]    = 0;
			$_POST["itiva_${i}"]    = $val['iva'];
			$_POST["sinvpeso_${i}"] = $val['sinvpeso'];
			$_POST["sinvtipo_${i}"] = $val['sinvtipo'];
			$_POST["detalle_${i}"]  = '';
			$_POST["combo_${i}"]    = '';
			$i++;
		}

		$_POST['totals'] = $totals;
		$_POST['iva']    = $iva;
		$_POST['totalg'] = $totalg;

		//sfpa
		$i=0;
		$_POST["tipo_${i}"]      = '';
		$_POST["num_ref_${i}"]   = '';
		$_POST["banco_${i}"]     = '';
		$_POST["monto_${i}"]     = $totals;

		//print_r($_POST);

		$this->dataedit();
	}

	// Factura Mensualidades
	function lote($status=null){
		$this->load->helper('download');
		$this->genesal=false;

		$data   = '';
		if($status=='insert'){
			$mSQL="SELECT a.id, a.numero contrato, TRIM(b.nombre) AS nombre, TRIM(b.rifci) AS rifci, b.cliente, b.tipo,
					a.codigo, b.dire11 AS direc, a.cantidad, a.precio, a.base, b.telefono, a.descrip, c.iva, IF(a.upago<a.inicio, a.inicio, DATE_ADD(a.upago, INTERVAL 1 MONTH) ) upago,
					EXTRACT(YEAR_MONTH FROM inicio ) inicio, b.vendedor
					FROM sclicont a JOIN scli b ON a.cliente=b.cliente JOIN sinv c ON a.codigo=c.codigo
				WHERE a.status = 'A'
				ORDER BY b.rifci";
			$query = $this->db->query($mSQL);

			foreach ($query->result() as $row){
				$saldo = 0;
				$dbcliente= $this->db->escape($row->cliente);
				$sql      = "SELECT SUM(monto*(tipo_doc IN ('FC','GI','ND'))) AS debe, SUM(monto*(tipo_doc IN ('NC','AB','AN'))) AS haber FROM smov WHERE cod_cli=${dbcliente}";
				$qquery   = $this->db->query($sql);
				if ($qquery->num_rows() > 0){
					$rrow = $qquery->row();
					$saldo= $rrow->debe-$rrow->haber;
				}
				$saldo += $row->base*(1+($row->iva/100))+1;
				$sql="UPDATE scli SET credito='S',tolera=10,maxtole=10,limite=${saldo},formap=if(formap=0,30,formap) WHERE cliente=${dbcliente}";
				$this->db->query($sql);

				$upago = $row->upago;

				$desde    = substr($row->upago,5,2).' del '.substr($row->upago,0,4);

				$contrato = $row->contrato;
				$_POST['btn_submit']  = 'Guardar';
				$_POST['pfac']        = '';
				$_POST['fecha']       = date('d/m/Y');
				$_POST['cajero']      = '';
				$_POST['vd']          = $row->vendedor;
				$_POST['almacen']     = '0001'; //$this->secu->getalmacen();
				$_POST['tipo_doc']    = 'F';
				$_POST['referen']     = 'C';
				$_POST['factura']     = '';
				$_POST['cod_cli']     = $row->cliente;
				$_POST['sclitipo']    = '1';
				$_POST['nombre']      = $row->nombre;
				$_POST['rifci']       = $row->rifci;
				$_POST['direc']       = $row->direc;
				$_POST['upago']       = $row->upago;

				//$_POST['observ1']     = $upago;

				$_POST['codigoa_0']   = $row->codigo;
				$_POST["lote_0"]      = 0;
				$_POST['desca_0']     = $row->descrip;
				$_POST['detalle_0']   = "Contrato Nro. ${contrato} correspondiente al mes ${desde}";
				$_POST['cana_0']      = $row->cantidad;
				$_POST['preca_0']     = $row->precio;
				$_POST['tota_0']      = $row->base;
				$_POST['precio1_0']   = 0;
				$_POST['precio2_0']   = 0;
				$_POST['precio3_0']   = 0;
				$_POST['precio4_0']   = 0;
				$_POST["descu_0"]     = 0;
				$_POST['itiva_0']     = $row->iva;
				$_POST['sinvpeso_0']  = 0;
				$_POST['sinvtipo_0']  = 'Servicio';
				$_POST['tipo_0']      = '';
				$_POST['sfpafecha_0'] = '';
				$_POST['num_ref_0']   = '';
				$_POST['banco_0']     = '';
				$_POST['monto_0']     = $row->precio*(1+($row->iva/100)) ;
				$_POST['snte']        = '';

				ob_start();
					$this->dataedit();
					$rt = ob_get_contents();
				@ob_end_clean();

				$getdata=json_decode($rt,true);
				if($getdata['status']=='A'){
					$id=$getdata['pk']['id'];
					$this->db->simple_query("UPDATE sclicont SET upago='$upago' WHERE id=".$row->id);
				}else{
					echo $getdata['mensaje'];
				}
			}
		}
	}

	function instalar(){
		$campos = $this->db->list_fields('sfac');

		if(!in_array('freiva',$campos)){
			$this->db->query("ALTER TABLE sfac ADD freiva DATE ");
		}

		if(!in_array('ereiva',$campos)){
			$this->db->query("ALTER TABLE sfac ADD ereiva DATE AFTER freiva");
		}

		if(!in_array('entregado',$campos)){
			$this->db->query("ALTER TABLE sfac ADD entregado DATE ");
			$this->db->query("UPDATE sfac SET entregado=fecha");
		}

		if(!in_array('comiadi',$campos)){
			$this->db->query("ALTER TABLE sfac ADD comiadi DECIMAL(10,2) DEFAULT 0 ");
		}

		if(!in_array('upago',$campos)){
			$this->db->query("ALTER TABLE sfac ADD upago INT(10)");
		}

		if(!in_array('manual',$campos)){
			$this->db->query("ALTER TABLE sfac ADD COLUMN manual CHAR(50) NULL DEFAULT 'N'");
		}

		if(!in_array('descuento',$campos)){
			$this->db->query("ALTER TABLE sfac ADD COLUMN descuento DECIMAL(10,2) NULL DEFAULT '0'");
		}

		if(!in_array('pagacon',$campos)){
			$this->db->query("ALTER TABLE sfac ADD COLUMN pagacon DECIMAL(10,2) NULL DEFAULT '0'");
		}

		if(!in_array('maestra',$campos)){
			$this->db->query("ALTER TABLE sfac ADD COLUMN maestra VARCHAR(8) NULL DEFAULT '' AFTER descuento");
		}

		if(!in_array('reparto',$campos)){
			$mSQL="ALTER TABLE sfac ADD COLUMN reparto INT(11) NULL DEFAULT 0 AFTER manual";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('rcobro',$campos)){
			$mSQL="ALTER TABLE sfac ADD COLUMN rcobro INT(11) NULL DEFAULT 0 AFTER reparto";
			$this->db->query($mSQL);
		}

		if(!in_array('basecomi',$campos)){
			$mSQL="ALTER TABLE `sfac` ADD COLUMN `basecomi` DECIMAL(17,2) NULL DEFAULT '0.00'";
			$this->db->query($mSQL);
		}

		if(!in_array('repcob',$campos)){
			$mSQL="ALTER TABLE `sfac` ADD COLUMN `repcob` CHAR(2) NULL DEFAULT NULL COMMENT 'Cobro en reparto' AFTER `basecomi`";
			$this->db->query($mSQL);
		}

		if(!in_array('descu1',$campos)) $this->db->query("ALTER TABLE sfac ADD COLUMN descu1 DECIMAL(10,2) NULL DEFAULT '0' AFTER descuento ");
		if(!in_array('descu2',$campos)) $this->db->query("ALTER TABLE sfac ADD COLUMN descu2 DECIMAL(10,2) NULL DEFAULT '0' AFTER descu1 ");
		if(!in_array('descu3',$campos))	$this->db->query("ALTER TABLE sfac ADD COLUMN descu3 DECIMAL(10,2) NULL DEFAULT '0' AFTER descu2 ");

		if(!in_array('bultos',$campos)) $this->db->query("ALTER TABLE sfac ADD COLUMN bultos INT(10) NULL DEFAULT '0' ");


		if(!in_array('id'  ,$campos)){
			$this->db->simple_query('ALTER TABLE sfac DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE sfac ADD UNIQUE INDEX tipo_doc, numero (numero)');
			$this->db->simple_query('ALTER TABLE sfac ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		$campos = $this->db->list_fields('sitems');

		if(!in_array('descu',$campos)){
			$this->db->query("ALTER TABLE sitems ADD descu DECIMAL(10,2) DEFAULT '0' AFTER descuento");
		}

		if(!in_array('lote',$campos)){
			$this->db->query("ALTER TABLE sitems ADD lote INT(11) DEFAULT '0' AFTER descu");
		}
	}
}
