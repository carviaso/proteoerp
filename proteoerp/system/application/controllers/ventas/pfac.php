<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Pfac extends Controller {
	var $mModulo = 'PFAC';
	var $titp    = 'Pedidos de Clientes';
	var $tits    = 'Pedidos de Clientes';
	var $url     = 'ventas/pfac/';
	var $genesal = true;

	function Pfac(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PFAC', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 900, 650, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('185');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$grid1->setHeight('190');
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 220, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'imprime', 'img'=>'assets/default/images/print.png','alt' => 'Reimprimir', 'label'=>'Reimprimir Documento'));
		$grid->wbotonadd(array('id'=>'bffact' , 'img'=>'images/star.png'                ,'alt' => 'Facturar'  , 'label'=>'Facturar'));

		$WestPanel = $grid->deploywestp();


		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'ffact' , 'title'=>'Convertir en factura'),
			array('id'=>'fedita', 'title'=>'Agregar/Editar Pedido'),
			array('id'=>'fshow' , 'title'=>'Mostrar Registro'),
			array('id'=>'fborra', 'title'=>'Eliminar Registro')
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
		$param['listados']     = $this->datasis->listados('PFAC', 'JQ');
		$param['otros']        = $this->datasis->otros('PFAC', 'JQ');
		$param['centerpanel']  = $centerpanel;
		$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);

	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '<script type="text/javascript">'."\n";

		$bodyscript .= '
		function tconsulta(transac){
			if (transac)	{
				window.open(\''.site_url('contabilidad/casi/localizador/transac/procesar').'/\'+transac, \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Transacci&oacute;n invalida</h1>");
			}
		};';

		$bodyscript .= '
		function pfacadd(){
			$.post("'.site_url('ventas/pfac/dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			});
		}';

		$bodyscript .= '
		function pfacedit() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("#newapi'.$grid0.'").getRowData(id);
				if(ret.status == "P"){
					mId = id;
					$.post("'.site_url('ventas/pfac/dataedit/modify').'/"+id, function(data){
						$("#fedita").html(data);
						$("#fedita").dialog( "open" );
					});
				}else{
					$.prompt("<h1>Por favor Seleccione un Registro con status P</h1>");
				}
			}
		}';

		$bodyscript .= '
		function pfacdel(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if(json.status == "A"){
								apprise("Registro eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
							}else{
								apprise("Registro no se puede eliminado");
							}
						}catch(e){
							$("#fborra").html(data);
							$("#fborra").dialog("open");
						}
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		}';

		$bodyscript .= '
		function pfacshow(){
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
		}';

		//Wraper de javascript
		$bodyscript .= '
		$(function() {
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
		jQuery("#imprime").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);

				if(ret.factura != null && ret.factura != false){
					$.prompt("<h2>Qu&eacute; documento dese imprimir?</h2>",{
						buttons: { Pedido: true, Factura: false },
						submit: function(e,v,m,f){
							if(v){
								window.open(\''.site_url('formatos/ver/PFAC/').'/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes\');
							}else{
								window.open(\''.site_url($this->url.'sfacprint').'/\'+ret.factura, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
							}
						}
					});
				}else{
					window.open(\''.site_url('formatos/ver/PFAC/').'/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes\');
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un pedido</h1>");
			}
		});';

		$bodyscript .= '
		$("#bffact").click(function(){
			var id = $("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				$.post("'.site_url('ventas/sfac/creafrompfac/N').'/"+ret.numero+"/create",
				function(data){
					$("#ffact").html(data);
					$("#ffact").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un pedido</h1>");}
		});';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 500, width: 800, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/PFAC').'/\'+res.id+\'/id\'').';
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

		//Convierte Factura
		$bodyscript .= '
			$("#ffact").dialog({
				autoOpen: false, height: 550, width: 840, modal: true,
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
									if ( json.status == "A" ) {
										if ( json.manual == "N" ) {
											$( "#ffact" ).dialog( "close" );
											jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
											window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
											return true;
										}else{
											$.post("'.site_url($this->url.'dataedit/S/create').'",
											function(data){
												$("#ffact").html(data);
											})
											window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
											return true;
										}
									}else{
										apprise(json.mensaje);
									}
								}catch(e){
									$("#ffact").html(r);
								}
							}
						});
					},
					"Cancelar": function() {
						$("#ffact").html("");
						$( this ).dialog( "close" );
						$("#newapi'.$grid0.'").trigger("reloadGrid");
					}
				},
				close: function() {
					$("#ffact").html("");
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

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('status');
		$grid->label('Estatus');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
			'cellattr'      => 'function(rowId, tv, aData, cm, rdata){
				var tips = "";
				if(aData.status !== undefined){
					if(aData.status=="F"){
						tips = "Facturado";
					}else if(aData.status=="B"){
						tips = "BackOrder";
					}else if(aData.status=="C"){
						tips = "Cerrado";
					}else if(aData.status=="T"){
						tips = "Temporar";
					}else if(aData.status=="P"){
						tips = "Pendiente";
					}else if(aData.status=="A"){
						tips = "Internet";
					}else if(aData.status=="X"){
						tips = "Anulado";
					}else{
						tips = "Interno";
					}
				}
				return \'title="\'+tips+\'"\';
			}'
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


		$grid->addField('vd');
		$grid->label('Vendedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('cod_cli');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('rifci');
		$grid->label('RIF/CI');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:13, maxlength: 13 }',
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

/*
		$grid->addField('direc');
		$grid->label('Direc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('dire1');
		$grid->label('Dire1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));

*/

		$grid->addField('referen');
		$grid->label('Referencia');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 110,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
		));


		$grid->addField('totals');
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


		$grid->addField('iva');
		$grid->label('IVA');
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


		$grid->addField('observa');
		$grid->label('Observaci&oacute;n 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('observ1');
		$grid->label('Observaci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('cajero');
		$grid->label('Cajero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
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
			'formatter'     => 'ltransac'
		));

		$grid->addField('zona');
		$grid->label('Zona');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('ciudad');
		$grid->label('Ciudad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('presup');
		$grid->label('Presupuesto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('anticipo');
		$grid->label('Anticipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
		));


		$grid->addField('entregar');
		$grid->label('Entregar');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		/*$grid->addField('numant');
		$grid->label('Numant');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('ftoma');
		$grid->label('Ftoma');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));*/


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


		/*$grid->addField('fenvia');
		$grid->label('Fenvia');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('faplica');
		$grid->label('Faplica');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));*/


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

		$grid->setOnSelectRow('
			function(id){
				if (id){
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
					var ret = $(this).getRowData(id);
					$("#ladicional").text(ret.observa+ret.observ1);
				}
			},
			afterInsertRow:
			function( rid, aData, rowe){
				if(aData.status == "P"){
					$(this).jqGrid( "setCell", rid, "status","", {color:"#FFFFFF", background:"#008B00" });
				}else if(aData.status =="F"){
					$(this).jqGrid( "setCell", rid, "status","", {color:"#FFFFFF", background:"#2F3CAD" });
				}else if(aData.status =="B"){
					$(this).jqGrid( "setCell", rid, "status","", {color:"#FFFFFF", background:"#FFDD00" });
				}else if(aData.status =="C"){
					$(this).jqGrid( "setCell", rid, "status","", {color:"#000000", background:"#F0FFFF" });
				}else if(aData.status =="X"){
					$(this).jqGrid( "setCell", rid, "status","", {color:"#FFFFFF", background:"#C90623" });
				}else if(aData.status =="T"){
					$(this).jqGrid( "setCell", rid, "status","", {color:"#FFFFFF", background:"#D5D1CF" });
				}else if(aData.status =="A"){
					$(this).jqGrid( "setCell", rid, "status","", {color:"#FFFFFF", background:"#1EA961" });
				}else{
					$(this).jqGrid( "setCell", rid, "status","", {color:"#FFFFFF", background:"#660088" });
				}
			}'
		);



		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('PFAC','1' ));
		$grid->setEdit(   $this->datasis->sidapuede('PFAC','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PFAC','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PFAC','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');
		$grid->setOndblClickRow('');

		$grid->setBarOptions('addfunc: pfacadd, editfunc: pfacedit, delfunc: pfacdel, viewfunc: pfacshow');

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
		$mWHERE = $grid->geneTopWhere('pfac');

		$response   = $grid->getData('pfac', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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
		$mcodp  = 'numero';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			echo 'Deshabilitado';
		}elseif($oper == 'edit'){
			echo 'Deshabilitado';
		} elseif($oper == 'del') {
			echo 'Deshabilitado';
		}
	}


	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

/*
		$grid->addField('tipoa');
		$grid->label('Tipoa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));
*/

		$grid->addField('numa');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('codigoa');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('desca');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
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


		$grid->addField('iva');
		$grid->label('Tasa');
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


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('vendedor');
		$grid->label('Vendedor');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

/*
		$grid->addField('costo');
		$grid->label('Costo');
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
*/

		$grid->addField('entregado');
		$grid->label('Entregado');
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

/*
		$grid->addField('pos');
		$grid->label('Pos');
		$grid->params(array(
			'hidden'        => 'true',
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
*/

		$grid->addField('pvp');
		$grid->label('PVP');
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

/*
		$grid->addField('comision');
		$grid->label('Comision');
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
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('mostrado');
		$grid->label('Mostrado');
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
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
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
*/
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
/*
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

		$grid->addField('dxapli');
		$grid->label('Dxapli');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));
*/

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


		#Set url
		$grid->setUrlput(site_url($this->url.'setdatait/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatait/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdatait($id=0){
		if($id === 0 ){
			$id = $this->datasis->dameval('SELECT MAX(id) AS val FROM pfac');
		}
		if(empty($id)) return '';
		$id = intval($id);
		$numero   = $this->datasis->dameval("SELECT numero FROM pfac WHERE id=${id}");
		$dbnumero = $this->db->escape($numero);

		$orderby= '';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos = $this->db->list_fields('itpfac');
			if(in_array($sidx,$campos)){
				$sidx = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM itpfac WHERE numa=${dbnumero} AND cana>0 ${orderby}";
		$response= $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setdatait(){

	}

	function dataedit(){
		$this->rapyd->load('dataobject', 'datadetails');

		$modbus = array(
			'tabla' => 'sinv',
			'columnas'=>array(
				'codigo'  =>'C&oacute;digo',
				'descrip' =>'Descripci&oacute;n',
				'precio1' =>'Precio 1',
				'precio2' =>'Precio 2',
				'precio3' =>'Precio 3',
				'existen' =>'Existencia',
			),
			'filtro' => array('codigo' => 'C&oacute;digo', 'descrip' => 'Descripci&oacute;n'),
			'retornar'  => array(
				'codigo'  => 'codigoa_<#i#>',
				'descrip' => 'desca_<#i#>',
				'base1'   => 'precio1_<#i#>',
				'base2'   => 'precio2_<#i#>',
				'base3'   => 'precio3_<#i#>',
				'base4'   => 'precio4_<#i#>',
				'iva'     => 'itiva_<#i#>',
				'tipo'    => 'sinvtipo_<#i#>',
				'peso'    => 'sinvpeso_<#i#>',
				'base1'   => 'itpvp_<#i#>',
				'pond'    => 'itcosto_<#i#>',
				'pond'    => 'pond_<#i#>',
				'mmargen' => 'mmargen_<#i#>',
				'formcal' => 'formcal_<#i#>',
				'ultimo'  => 'ultimo_<#i#>',
				'pm'      => 'pm_<#i#>',
			),
			'p_uri' => array(4 => '<#i#>'),
			'titulo' => 'Buscar Articulo',
			'where' => '`activo` = "S"',
			'script' => array('post_modbus_sinv(<#i#>)')
		);
		$btn = $this->datasis->p_modbus($modbus, '<#i#>');

		$mSCLId = array(
			'tabla'    => 'scli',
			'columnas' => array(
				'cliente' => 'C&oacute;digo Cliente',
				'nombre'  => 'Nombre',
				'cirepre' => 'Rif/Cedula',
				'dire11'  => 'Direcci&oacute;n',
				'tipo' => 'Tipo'),
			'filtro'   => array('cliente' => 'C&oacute;digo Cliente', 'nombre' => 'Nombre'),
			'retornar' => array('cliente' => 'cod_cli', 'nombre' => 'nombre', 'rifci' => 'rifci',
				'dire11' => 'direc', 'tipo' => 'sclitipo','mmargen'=>'mmargen'),
			'titulo' => 'Buscar Cliente',
			'script' => array('post_modbus_scli()'));
		$boton = $this->datasis->modbus($mSCLId);

		$do = new DataObject('pfac');
		$do->rel_one_to_many('itpfac', 'itpfac', array('numero' => 'numa'));
		$do->pointer('scli' , 'scli.cliente=pfac.cod_cli', 'scli.tipo AS sclitipo', 'left');
		$do->rel_pointer('itpfac', 'sinv', 'itpfac.codigoa=sinv.codigo', 'sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo,sinv.precio1 As sinvprecio1,sinv.pond AS sinvpond,sinv.mmargen as sinvmmargen,sinv.ultimo sinvultimo,sinv.formcal sinvformcal,sinv.pm sinvpm,itpfac.preca precat');

		$edit = new DataDetails('Pedidos', $do);
		$edit->on_save_redirect=false;
		$edit->back_url = site_url('ventas/pfac/filteredgrid');
		$edit->set_rel_title('itpfac', 'Producto <#o#>');

		$edit->pre_process( 'insert', '_pre_insert');
		$edit->pre_process( 'update', '_pre_update');
		$edit->pre_process( 'delete', '_pre_delete');
		$edit->post_process('insert', '_post_insert');
		$edit->post_process('update', '_post_update');
		$edit->post_process('delete', '_post_delete');

		$fenvia  = strtotime($edit->get_from_dataobjetct('fenvia'));
		$faplica = strtotime($edit->get_from_dataobjetct('faplica'));
		$hoy     = strtotime(date('Y-m-d'));

		$edit->fecha = new DateonlyField('Fecha', 'fecha', 'd/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 12;
		$edit->fecha->calendar=false;

		$edit->status= new dropdownField ('Estatus', 'status');
		$edit->status->options(array(
			'P'=>'Pendiente',
			'C'=>'Cerrado',
			'A'=>'Internet',
			'B'=>'BackOrder',
			'X'=>'Anulado',
			'T'=>'Temporal',
			'V'=>'V.Externo',//Estatus locales de vendores ambulantes (Enviado)
			'U'=>'V.Externo',//Estatus locales de vendores ambulantes (Por enviar)
		));
		$edit->status->style = 'width:200px;';
		$edit->status->when = array('show');
		$edit->status->rule='enum[P,I]';

		$edit->vd = new dropdownField ('Vendedor', 'vd');
		$edit->vd->options('SELECT vendedor, CONCAT(vendedor,\' \',nombre) nombre FROM vend ORDER BY vendedor');
		$edit->vd->style = 'width:200px;';

		$edit->mmargen = new inputField('mmargen', 'mmargen');

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode = 'autohide';
		$edit->numero->maxlength = 8;
		$edit->numero->apply_rules = false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when = array('show', 'modify');

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->readonly = true;
		$edit->peso->size = 10;
		$edit->peso->type ='inputhidden';

		$edit->cliente = new inputField('Cliente', 'cod_cli');
		$edit->cliente->size = 6;
		$edit->cliente->rule = 'required';
		$edit->cliente->maxlength = 5;
		if(!($faplica < $fenvia)) $edit->cliente->append($boton);
		$edit->cliente->autocomplete=false;

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 30;
		$edit->nombre->maxlength = 40;
		$edit->nombre->rule = 'required';
		$edit->nombre->type ='inputhidden';

		$edit->rifci = new inputField('RIF/CI', 'rifci');
		$edit->rifci->autocomplete = false;
		$edit->rifci->size = 15;
		$edit->rifci->type ='inputhidden';

		$edit->direc = new inputField('Direcci&oacute;n', 'direc');
		$edit->direc->size = 40;
		$edit->direc->type ='inputhidden';

		$edit->observa = new inputField('Observaciones', 'observa');
		$edit->observa->size = 40;

		$edit->observ1 = new inputField('Observaciones', 'observ1');
		$edit->observ1->size = 40;

		// Para saber que precio se le va a dar al cliente
		$edit->sclitipo = new hiddenField('', 'sclitipo');
		$edit->sclitipo->db_name = 'sclitipo';
		$edit->sclitipo->pointer = true;
		$edit->sclitipo->insertValue = 1;

		// Campos para el detalle
		$edit->codigoa = new inputField('C&oacute;digo <#o#>', 'codigoa_<#i#>');
		$edit->codigoa->size = 12;
		$edit->codigoa->db_name = 'codigoa';
		$edit->codigoa->rel_id = 'itpfac';
		$edit->codigoa->rule = 'required|callback_chcodigoa';
		//$edit->codigoa->onkeyup = 'OnEnter(event,<#i#>)';
		if(!($faplica < $fenvia))
		$edit->codigoa->append($btn);

		$edit->desca = new inputField('Descripci&oacute;n <#o#>', 'desca_<#i#>');
		$edit->desca->size = 32;
		$edit->desca->db_name = 'desca';
		$edit->desca->maxlength = 50;
		$edit->desca->readonly = true;
		$edit->desca->rel_id = 'itpfac';
		$edit->desca->type='inputhidden';

		$edit->cana = new inputField('Cantidad <#o#>', 'cana_<#i#>');
		$edit->cana->db_name = 'cana';
		$edit->cana->css_class = 'inputnum';
		$edit->cana->rel_id = 'itpfac';
		$edit->cana->maxlength = 10;
		$edit->cana->size = 5;
		$edit->cana->rule = 'required|positive';
		$edit->cana->autocomplete = false;
		$edit->cana->onkeyup = 'importe(<#i#>)';
		//$edit->cana->insertValue=1;

		$edit->preca = new inputField('Precio <#o#>', 'preca_<#i#>');
		$edit->preca->db_name = 'preca';
		$edit->preca->css_class = 'inputnum';
		$edit->preca->rel_id = 'itpfac';
		$edit->preca->size = 10;
		$edit->preca->rule = 'required|positive|callback_chpreca[<#i#>]';
		$edit->preca->readonly = true;

		//$edit->dxapli = new inputField('Descuento <#o#>', 'dxapli_<#i#>');
		//$edit->dxapli->db_name = 'dxapli';
		//$edit->dxapli->rel_id = 'itpfac';
		//$edit->dxapli->size = 1;
		//$edit->dxapli->rule = 'trim';
		//$edit->dxapli->onchange="cal_dxapli(<#i#>)";

		$edit->tota = new inputField('importe <#o#>', 'tota_<#i#>');
		$edit->tota->db_name = 'tota';
		$edit->tota->size = 8;
		$edit->tota->css_class = 'inputnum';
		$edit->tota->rel_id = 'itpfac';
		$edit->tota->type='inputhidden';

		for($i = 1;$i <= 4;$i++){
			$obj = 'precio' . $i;
			$edit->$obj = new hiddenField('Precio <#o#>', $obj . '_<#i#>');
			$edit->$obj->db_name = 'sinv' . $obj;
			$edit->$obj->rel_id = 'itpfac';
			$edit->$obj->pointer = true;
		}

		$edit->itiva = new hiddenField('', 'itiva_<#i#>');
		$edit->itiva->db_name = 'iva';
		$edit->itiva->rel_id = 'itpfac';

		$edit->itpvp = new hiddenField('', 'itpvp_<#i#>');
		$edit->itpvp->db_name = 'pvp';
		$edit->itpvp->rel_id = 'itpfac';

		$edit->itcosto = new hiddenField('', 'itcosto_<#i#>');
		$edit->itcosto->db_name = 'costo';
		$edit->itcosto->rel_id = 'itpfac';

		$edit->sinvpeso = new hiddenField('', 'sinvpeso_<#i#>');
		$edit->sinvpeso->db_name = 'sinvpeso';
		$edit->sinvpeso->rel_id = 'itpfac';
		$edit->sinvpeso->pointer = true;

		$edit->sinvtipo = new hiddenField('', 'sinvtipo_<#i#>');
		$edit->sinvtipo->db_name = 'sinvtipo';
		$edit->sinvtipo->rel_id = 'itpfac';
		$edit->sinvtipo->pointer = true;

		$edit->itmmargen = new hiddenField('', 'mmargen_<#i#>');
		$edit->itmmargen->db_name = 'sinvmmargen';
		$edit->itmmargen->rel_id = 'itpfac';
		$edit->itmmargen->pointer = true;

		$edit->itpond = new hiddenField('', 'pond_<#i#>');
		$edit->itpond->db_name = 'sinvpond';
		$edit->itpond->rel_id  = 'itpfac';
		$edit->itpond->pointer = true;

		$edit->itultimo = new hiddenField('', 'ultimo_<#i#>');
		$edit->itultimo->db_name = 'sinvultimo';
		$edit->itultimo->rel_id  = 'itpfac';
		$edit->itultimo->pointer = true;

		$edit->itformcal = new hiddenField('', 'formcal_<#i#>');
		$edit->itformcal->db_name = 'sinvformcal';
		$edit->itformcal->rel_id  = 'itpfac';
		$edit->itformcal->pointer = true;

		$edit->itpm = new hiddenField('', 'pm_<#i#>');
		$edit->itpm->db_name = 'sinvpm';
		$edit->itpm->rel_id  = 'itpfac';
		$edit->itpm->pointer = true;

		$edit->precat = new hiddenField('', 'precat_<#i#>');
		$edit->precat->db_name = 'precat';
		$edit->precat->rel_id  = 'itpfac';
		$edit->precat->pointer = true;
		// fin de campos para detalle

		$edit->ivat = new hiddenField('Impuesto', 'iva');
		$edit->ivat->css_class = 'inputnum';
		$edit->ivat->readonly = true;
		$edit->ivat->size = 10;

		$edit->totals = new hiddenField('Sub-Total', 'totals');
		$edit->totals->css_class = 'inputnum';
		$edit->totals->readonly = true;
		$edit->totals->size = 10;

		$edit->totalg = new hiddenField('Monto Total', 'totalg');
		$edit->totalg->css_class = 'inputnum';
		$edit->totalg->readonly = true;
		$edit->totalg->size = 10;

		$edit->usuario = new autoUpdateField('usuario', $this->secu->usuario(), $this->secu->usuario());
		$edit->estampa = new autoUpdateField('estampa', date('Ymd')  , date('Ymd')  );
		$edit->hora    = new autoUpdateField('hora'   , date('H:i:s'), date('H:i:s'));

		$control=$this->rapyd->uri->get_edited_id();

		//if($fenvia < $hoy){
		//	$edit->buttons( 'delete', 'back','add_rel');
		//	$accion="javascript:window.location='".site_url('ventas/pfaclite/enviar/'.$control)."'";
		//	$edit->button_status('btn_envia'  ,'Enviar Pedido'         ,$accion,'TR','show');
		//}elseif($faplica < $fenvia){
		//	$hide=array('vd','peso','cliente','nombre','rifci','direc','observa','observ1','codigoa','desca','cana');
		//	foreach($hide as $value)
		//	$edit->$value->type="inputhidden";
        //
		//	$accion="javascript:window.location='".site_url('ventas/pfac/dataedit/modify/'.$control)."'";
		//	$edit->button_status('btn_envia'  ,'Aplicar Descuentos'         ,$accion,'TR','show');
        //
		//	$edit->buttons( 'delete', 'back');
        //
		//}else{
		//	$edit->buttons( 'delete', 'back', 'add_rel');
        //
		//}
		//$edit->buttons('add_rel');
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' => 'A',
				'mensaje'=> 'Registro guardado',
				'pk'     => $edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			if($this->genesal){
				$conten['form']    =& $edit;
				$conten['hoy']     = $hoy;
				$conten['fenvia']  = $fenvia;
				$conten['faplica'] = $faplica;
				$data['content']   = $this->load->view('view_pfac', $conten);
			}else{
				$rt=array(
					'status' =>'B',
					'mensaje'=> utf8_encode(html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string))),
					'pk'     =>''
				);
				echo json_encode($rt);
			}

		}

	}

	function creapfac(){
		foreach($_POST as $ind=>$val){
			$matches=array();
			$_POST['fecha']=date('d/m/Y');

			if(preg_match('/codigoa_(?P<id>\d+)/', $ind, $matches) > 0){
				$id     = $matches['id'];
				$precio = $_POST['precio_'.$id];
				$iva    = $_POST['itiva_'.$id];
				$_POST['preca_'.$id] = round($precio*100/(100+$iva),2);
			}
		}
		//print_r($_POST);
		$this->genesal=false;
		$rt=$this->dataedit();
		echo $rt;
	}

	function _pre_insert($do){
		$status  = $do->get('status');
		if(empty($status)){
			$do->set('status' , 'P');
		}
		$modoiva = $this->datasis->traevalor('MODOIVA');

		//$transac = $this->datasis->fprox_numero('ntransa');
		//$do->set('transac', $transac);
		$fecha = $do->get('fecha');
		$vd    = $do->get('vd');

		$cod_cli  = $do->get('cod_cli');
		$dbcod_cli= $this->db->escape($cod_cli);
		$scli     = $this->datasis->damerow("SELECT rifci,nombre,CONCAT(TRIM(dire11),' ',TRIM(dire12)) direc,CONCAT(TRIM(dire21),' ',TRIM(dire22)) dire1,zona,ciudad1 AS ciudad FROM scli WHERE cliente=${dbcod_cli}");
		if(empty($scli)){
			$do->error_message_ar['pre_ins']='Cliente inexistente.';
			return false;
		}
		$do->set('rifci' ,$scli['rifci'] );
		$do->set('nombre',$scli['nombre']);
		$do->set('direc' ,$scli['direc'] );
		$do->set('dire1' ,$scli['dire1'] );
		$do->set('zona'  ,trim($scli['zona']));
		$do->set('ciudad',trim($scli['ciudad']));

		$numero  = $this->datasis->fprox_numero('npfac');
		$do->set('numero', $numero);

		$iva = $totals = 0;
		$cana = $do->count_rel('itpfac');
		for($i = 0;$i < $cana;$i++){
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			$itpreca = $do->get_rel('itpfac', 'preca', $i);
			$itiva   = $do->get_rel('itpfac', 'iva', $i);
			$ittota  = $itpreca * $itcana;
			$do->set_rel('itpfac', 'tota'    , $ittota, $i);
			$do->set_rel('itpfac', 'fecha'   , $fecha , $i);
			$do->set_rel('itpfac', 'vendedor', $vd , $i);

			$iva    += $ittota * ($itiva / 100);
			$totals += $ittota;

			if($modoiva=='N'){
				$mostrado= $itpreca;
			}else{
				$mostrado= round($itpreca*(100+$itiva)/100,2);
			}

			$do->set_rel('itpfac', 'mostrado', $mostrado, $i);
		}
		$totalg = $totals + $iva;

		$do->set('totals' , round($totals , 2));
		$do->set('totalg' , round($totalg , 2));
		$do->set('iva'    , round($iva    , 2));
		return true;
	}

	function _pre_update($do){
		$error='';
		$codigo = $do->get('numero');
		$fecha  = $do->get('fecha');
		$vd     = $do->get('vd');
		$fenvia = $do->get('fenvia');
		$faplica= $do->get('faplica');

		$iva = $totals = 0;
		$cana = $do->count_rel('itpfac');
		for($i = 0;$i < $cana;$i++){
			$codigoa = $do->get_rel('itpfac', 'codigoa', $i);
			$itcana  = $do->get_rel('itpfac', 'cana'   , $i);
			$itpreca = $do->get_rel('itpfac', 'preca'  , $i);
			$itiva   = $do->get_rel('itpfac', 'iva'    , $i);

			if(($faplica < $fenvia)){
				$itdxapli = $do->get_rel('itpfac', 'dxapli', $i);
				$itprecat = $this->input->post("precat_$i");
				if(!$itdxapli)
				$itdxapli=' ';

				$itpreca  = $this->cal_dxapli($itprecat,$itdxapli);
				if(1*$itpreca>0){
					$do->set_rel('itpfac', 'preca'  , $itpreca, $i);
					$do->set('faplica',date('Y-m-d'));
				}else{
					$error.="Error. El descuento por aplicar es incorrecto para el codigo $codigoa</br>";
				}
			}

			$ittota  = $itpreca * $itcana;
			$do->set_rel('itpfac', 'tota'    , $ittota, $i);
			$do->set_rel('itpfac', 'fecha'   , $fecha , $i);
			$do->set_rel('itpfac', 'vendedor', $vd    , $i);

			$iva    += $ittota*$itiva/100;
			$totals += $ittota;
			$do->set_rel('itpfac', 'mostrado', $iva + $ittota, $i);
		}
		$totalg = $totals + $iva;

		$do->set('totals' , round($totals , 2));
		$do->set('totalg' , round($totalg , 2));
		$do->set('iva'    , round($iva    , 2));

		$dbnuma=$this->db->escape($codigo);
		$mSQL  ="UPDATE itpfac AS c JOIN sinv   AS d ON d.codigo=c.codigoa
			SET d.exdes=IF(d.exdes>c.cana,d.exdes-c.cana,0)
			WHERE c.numa = ${dbnuma}";

		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'pfac'); }

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
		return true;
	}

	function chcodigoa($codigo){
		$cana=$this->datasis->dameval('SELECT COUNT(*) AS val FROM sinv WHERE activo=\'S\' AND MID(tipo,1,1)="A" AND codigo='.$this->db->escape($codigo));
		if(empty($cana) || $cana==0){
			$this->validation->set_message('chcodigoa', 'El campo %s contiene un codigo de producto no v&aacute;lido o inactivo');
			return false;
		}
		return true;
	}

	function _post_insert($do){
		$cana = $do->count_rel('itpfac');
		for($i = 0;$i < $cana;$i++){
			$itcodigo= $do->get_rel('itpfac', 'codigoa',$i);
			$itcana  = $do->get_rel('itpfac', 'cana'   ,$i);
			$mSQL = "UPDATE sinv SET exdes=exdes+${itcana} WHERE codigo=".$this->db->escape($itcodigo);

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'pfac'); }
		}

		$codigo = $do->get('numero');
		$this->insert_numero=$codigo;
		logusu('pfac', "Pedido ${codigo} CREADO");
	}

	function chpreca($preca, $ind){
		$codigo = $this->input->post('codigoa_' . $ind);
		$precio4 = $this->datasis->dameval('SELECT base4 FROM sinv WHERE codigo='.$this->db->escape($codigo));
		if($precio4 < 0) $precio4 = 0;

		if($preca < $precio4){
			$this->validation->set_message('chpreca', 'El art&iacute;culo '.$codigo.' debe contener un precio de al menos '.nformat($precio4));
			return false;
		}else{
			return true;
		}
	}

	function enviar($id,$dir='pfac'){
		$ide=$this->db->escape($id);
		$this->db->query("UPDATE pfac SET fenvia=CURDATE() WHERE id=${ide}");
		redirect("ventas/${dir}/dataedit/show/${id}");
	}

	function aplicar($numero){

	}

	function _post_update($do){
		$cana = $do->count_rel('itpfac');
		for($i=0;$i<$cana;$i++){
			$itcodigo= $do->get_rel('itpfac', 'codigoa', $i);
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			$mSQL = "UPDATE sinv SET exdes=exdes+${itcana} WHERE codigo=".$this->db->escape($itcodigo);

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'pfac'); }
		}
		$codigo = $do->get('numero');
		logusu('pfac', "Pedido ${codigo} MODIFICADO");
	}

	function cal_dxapli($preca=null,$dxapli=null){
		$p=null;
		if(!($preca && $dxapli)){
			$preca =$this->input->post('preca');
			$dxapli=$this->input->post('dxapli');
			$p=true;
		}

		$desc  =explode('+',$dxapli);
		$error='';

		$precio=$preca;
		foreach($desc as $value){
			if(strlen(trim($value))>0){

				if( $value>0)
				$precio=$precio-($precio*$value/100);
				else
				$error='_||_';
			}
		}

		if($p){
			if(empty($error) && 1*$precio>0)
				echo round($precio);
			else
				echo '_||_';
		}else{
			if(empty($error) && 1*$precio>0)
			return $precio;
		}

	}

	function _pre_delete($do){
		$codigo = $do->get('numero');
		$mSQL='UPDATE sinv JOIN itpfac ON sinv.codigo=itpfac.codigoa SET sinv.exdes=sinv.exdes-itpfac.cana WHERE itpfac.numa='.$this->db->escape($codigo);
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'pfac'); }
		return true;
	}

	function _post_delete($do){
		$codigo = $do->get('numero');
		logusu('pfac', "Pedido ${codigo} ELIMINADO");
	}

	function sfacprint($factura){
		$dbnumero=$this->db->escape($factura);
		$mSQL='SELECT a.id FROM sfac AS a WHERE a.tipo_doc="F" AND a.numero='.$dbnumero;
		$sfac_id=$this->datasis->dameval($mSQL);
		if(!empty($sfac_id)){
			redirect('ventas/sfac/dataprint/modify/'.$sfac_id);
		}else{
			echo 'Factura no encontrada';
		}
	}

	function sclibu(){
		$numero  = $this->uri->segment(4);
		$dbnumero= $this->db->escape($numero);
		$id = $this->datasis->dameval("SELECT b.id FROM pfac a JOIN scli b ON a.cod_cli=b.cliente WHERE numero=${dbnumero}");
		redirect('ventas/scli/dataedit/show/'.$id);
	}

	function instalar(){
		$campos=$this->db->list_fields('pfac');

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE pfac DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE pfac ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE pfac ADD UNIQUE INDEX numero (numero)');
		}

		if(!in_array('fenvia',$campos)){
			$this->db->query("ALTER TABLE `pfac`  ADD COLUMN `fenvia` DATE NULL DEFAULT '0000-00-00' COMMENT 'fecha en que el vendedor termino el pedido'");
		}

		if(!in_array('faplica',$campos)){
			$this->db->query("ALTER TABLE `pfac`  ADD COLUMN `faplica` DATE NULL DEFAULT '0000-00-00' COMMENT 'fecha en que se aplicaron los descuentos'");
		}

		if(!in_array('reserva',$campos)){
			$this->db->query("ALTER TABLE `pfac`  ADD COLUMN `reserva` CHAR(1) NOT NULL DEFAULT 'N'");
		}

		$itcampos=$this->db->list_fields('itpfac');
		if(!in_array('dxapli',$itcampos)){
			$this->db->query("ALTER TABLE `itpfac`  ADD COLUMN `dxapli` VARCHAR(20) NOT NULL COMMENT 'descuento por aplicar'");
		}
	}
}
