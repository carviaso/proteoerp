<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once('common.php');
class Sprm extends Controller {
	var $mModulo='SPRM';
	var $titp='Movimiento de Proveedor';
	var $tits='Movimiento de Proveedor';
	var $url ='finanzas/sprm/';

	function Sprm(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		if (!$this->datasis->sidapuede('SPRM', 'TODOS')) {
			redirect('/bienvenido/noautorizado');
		}
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

		$readyLayout = $grid->readyLayout2( 212, 140, $param['grids'][0]['gridname']);

		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'] );

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));


		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'imprime'   ,'img'=>'assets/default/images/print.png', 'alt' => 'Imprimir',      'label'=>'Reimprimir Documento', 'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'princheque','img'=>'images/check.png'  , 'alt' => 'Emitir Cheque'   , 'label'=>'Imprimir cheque',      'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'pago'      ,'img'=>'images/dinero.png' , 'alt' => 'Pago a proveedor', 'label'=>'Pago a proveedor'));
		//$grid->wbotonadd(array('id'=>'bncpro'    ,'img'=>'images/dinero.png' , 'alt' => 'NC a FC pagada'  , 'label'=>'NC a FC pagada'));
		$WestPanel = $grid->deploywestp();


		$adic = array(
			array('id'=>'fedita'  , 'title'=>'Agregar/Editar Registro'),
			array('id'=>'fabono'  , 'title'=>'Abonar a Proveedor'),
			array('id'=>'fsprvsel', 'title'=>'Seleccionar proveedor'),
			array('id'=>'fborra'  , 'title'=>'Borrar registro'),
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
		$param['listados']     = $this->datasis->listados('SPRM', 'JQ');
		$param['otros']        = $this->datasis->otros('SPRM', 'JQ');

		$param['centerpanel']  = $centerpanel;
		$param['funciones']    = $funciones;
		$param['tema1']        = 'darkness';
		$param['anexos']       = 'anexos1';
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;

		$this->load->view('jqgrid/crud2',$param);
	}



	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		function tconsulta(transac){
			if (transac)	{
				window.open(\''.site_url('contabilidad/casi/localizador/transac/procesar').'/\'+transac, \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Transaccion invalida</h1>");
			}
		};';

		$bodyscript .= '
		function sprmadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function sprmedit(){
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
		function sprmshow(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("#newapi'.$grid0.'").getRowData(id);
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
		function sprmdel() {
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
		jQuery("#princheque").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				if(ret.tipo_op=="CH"){
					window.open(\''.site_url($this->url.'impcheque').'/\'+id, \'_blank\', \'width=300,height=400,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-200), screeny=((screen.availWidth/2)-150)\');
				}else{
					$.prompt("<h1>El efecto seleccionado no possee cheques</h1>");
				}
			} else {
				$.prompt("<h1>Por favor Seleccione una Egreso</h1>");
			}
		});';

		$bodyscript .= '
		jQuery("#bncpro").click( function(){
			$.post("'.site_url($this->url.'ncppro').'/create", function(data){
				$("#fabono").html(data);
				$("#fabono").dialog( "open" );
			});
		});';

		$bodyscript .= '
		jQuery("#imprime").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url($this->url.'sprmprint').'/\'+id, \'_blank\', \'width=300,height=300,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});';


		//Abonos
		$bodyscript .= '
			$("#abonos").click(function() {
				var id  = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
				if (id)	{
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url('finanzas/ppro/formaabono').'/"+id, function(data){
						$("#fpreabono").html("");
						$("#fabono").html(data);
					});
					$( "#fabono" ).dialog( "open" );
				} else { $.prompt("<h1>Por favor Seleccione un Proveedor</h1>");}
			});

			$("#fabono").dialog({
				autoOpen: false, height: 470, width: 790, modal: true,
				buttons: {
					"Abonar": function() {
						var bValid = true;
						var rows = $("#abonados").jqGrid("getGridParam","data");
						var paras = new Array();
						for(var i=0;i < rows.length; i++){
							var row=rows[i];
							paras.push($.param(row));
						}
						allFields.removeClass( "ui-state-error" );
						if ( bValid ) {
							// Coloca el Grid en un input
							$("#fgrid").val(JSON.stringify(paras));
							$.ajax({
								type: "POST", dataType: "html", async: false,
								url:"'.site_url("finanzas/ppro/abono").'",
								data: $("#abonoforma").serialize(),
								success: function(r,s,x){
									var res = $.parseJSON(r);
									if ( res.status == "A"){
										apprise(res.mensaje);
										grid.trigger("reloadGrid");
										'.$this->datasis->jwinopen(site_url($this->url.'sprmprint').'/\'+res.pk.id').';
										$( "#fabono" ).dialog( "close" );
										return [true, a ];
									} else {
										$.prompt("<div style=\"font-size:16px;font-weight:bold;background:red;color:white\">Error:</div> <h1>"+res.mensaje+"</h1>");
									}
								}
							});
						}
					},
					Cancel: function() { $( this ).dialog( "close" ); }
				},
				close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
			});';

		$bodyscript .= '
		jQuery("#pago").click( function(){
			$.post("'.site_url($this->url.'selsprv/').'",
				function(data){
					$("#fsprvsel").html(data);
					$("#fsprvsel").dialog("open");
					var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
					if(id){
						var ret    = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
						var cod_prv= ret.cod_prv;
						$("#cod_prv").val(cod_prv);
						$("#cod_prv").focus();
						$("#cod_prv").autocomplete("search", cod_prv);
					}
				}
			);
		});';

		$bodyscript .= '
			$("#fsprvsel").dialog({
				autoOpen: false, height: 430, width: 540, modal: true,
				buttons: {
					"Seleccionar": function() {
						var id_sprv=$("#id_sprv").val();
						if(id_sprv){
							$.get("'.site_url($this->url.'pprov').'"+"/"+id_sprv+"/create", function(data) {
								$("#fedita").html(data);
								$("#fedita").dialog("open");
								$("#fsprvsel").html("");
								$("#fsprvsel").dialog("close");
							});
						}else{
							$.prompt("<b>Debe seleccionar un proveedor primero.</b>");
						}
					},
					Cancel: function() {
						$("#fcobroser").html("");
						$("#fsprvsel").dialog( "close" );
					}
				},
				close: function() {
					$("#fsprvsel").html("");
				}
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
									$("#fedita").dialog( "close" );
									grid.trigger("reloadGrid");
									'.$this->datasis->jwinopen(site_url('formatos/ver/SPRM').'/\'+res.pk.id+\'/id\'').';
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
				allFields.val("").removeClass( "ui-state-error" );
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
	//Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('cod_prv');
		$grid->label('Prov.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
		));

		$grid->addField('nombre');
		$grid->label('Nombre del Proveedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
		));


		$grid->addField('tipo_doc');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => '"center"',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
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


		$grid->addField('abonos');
		$grid->label('Abonos');
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


		$grid->addField('tipo_ref');
		$grid->label('Ref. Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('num_ref');
		$grid->label('Ref.N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
		));


		$grid->addField('observa1');
		$grid->label('Observaciones 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
		));


		$grid->addField('observa2');
		$grid->label('Observaciones 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
		));


		$grid->addField('banco');
		$grid->label('Cta.Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('tipo_op');
		$grid->label('Operaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));

/*
		$grid->addField('comprob');
		$grid->label('Comprobante');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
		));
*/

		$grid->addField('numche');
		$grid->label('Nro.Cheque');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
		));


		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
		));


		$grid->addField('ppago');
		$grid->label('Pronto Pago');
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


		$grid->addField('nppago');
		$grid->label('Nro.P.Pago');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
		));


		$grid->addField('reten');
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


		$grid->addField('nreten');
		$grid->label('Nro.Reten.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
		));


		$grid->addField('mora');
		$grid->label('Mora');
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



		$grid->addField('benefi');
		$grid->label('Beneficiario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
		));

/*
		$grid->addField('control');
		$grid->label('Control');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
		));
*/

		$grid->addField('transac');
		$grid->label('Transaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'formatter'     => 'ltransac'
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
		));


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
		));

/*
		$grid->addField('cambio');
		$grid->label('Cambio');
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

		$grid->addField('pmora');
		$grid->label('P.Mora');
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

		$grid->addField('reteiva');
		$grid->label('Rete.IVA');
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

		$grid->addField('nfiscal');
		$grid->label('Nro. Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 120,
			'edittype'      => "'text'",
			'formoptions'   => '{ label:"Numero fiscal" }',
			'editoptions'   => '{ size:15, maxlength: 20 }'
		));

		$grid->addField('montasa');
		$grid->label('Base G.');
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

		$grid->addField('monredu');
		$grid->label('Base R.');
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

		$grid->addField('monadic');
		$grid->label('Base A.');
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

		$grid->addField('tasa');
		$grid->label('Impuesto G.');
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

		$grid->addField('reducida');
		$grid->label('Impuesto R.');
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

		$grid->addField('sobretasa');
		$grid->label('Impuesto A.');
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

		$grid->addField('exento');
		$grid->label('Exento');
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

		$grid->addField('fecdoc');
		$grid->label('Fec.Doc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('afecta');
		$grid->label('Afecta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 100,
			'edittype'      => "'text'",
			'formoptions'   => '{ label:"Factura Afectada" }',
			'editoptions'   => '{ size:10, maxlength: 10 }'
		));

		$grid->addField('fecapl');
		$grid->label('Aplicada');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('serie');
		$grid->label('N&uacute;mero Completo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 120,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:15, maxlength: 20 }',
		));

/*
		$grid->addField('depto');
		$grid->label('Depto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));
*/

		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Modificado" }'
		));
/*
		$grid->addField('negreso');
		$grid->label('Negreso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
		));

		$grid->addField('ndebito');
		$grid->label('Ndebito');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
		));

		$grid->addField('causado');
		$grid->label('Causado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
		));
*/

		$mSQL = "SELECT cod_banc, CONCAT(cod_banc, ' ', nomb_banc) descrip FROM tban ORDER BY cod_banc ";
		$tbanco  = $this->datasis->llenajqselect($mSQL, true );

		$grid->addField('tbanco');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 40,
			'edittype'      => "'select'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{value: '.$tbanco.',  style:"width:300px" }',
			'editrules'     => '{ required:true}',
		));



		$grid->addField('posdata');
		$grid->label('Fecha Pago');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:false,date:true}',
			'formoptions'   => '{ label:"Fecha Pago" }'
		));



		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 60,
			'editable' => $editar,
			'search'   => 'false'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('240');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 420, height:200, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 420, height:200, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setonSelectRow('
			function(id){
				$.ajax({
					url: "'.site_url($this->url.'tabla').'/"+id,
					success: function(msg){
						//alert( "El ultimo codigo ingresado fue: " + msg );
						$("#radicional").html(msg);
					}
				});
			}
		');


		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(   $this->datasis->sidapuede('SPRM','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('SPRM','BORR_REG%'));
		$grid->setSearch(true);

		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');
		$grid->setBarOptions('delfunc: sprmdel');

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
		$mWHERE = $grid->geneTopWhere('sprm');

		$response   = $grid->getData('sprm', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				//$this->db->insert('sprm', $data);
			}
			return 'Registro Agregado';

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('sprm', $data);
			return "Registro $id Modificado";

		} elseif($oper == 'del'){
			echo "Accion no disponible";
		}
	}


	function tabla() {
		$id  = intval($this->uri->segment($this->uri->total_segments()));

		$row = $this->datasis->damerow("SELECT cod_prv, tipo_doc, numero, estampa, transac FROM sprm WHERE id=${id}");
		if(!empty($row)){
			$transac  = $row['transac'];
			$cod_prv  = $row['cod_prv'];
			$numero   = $row['numero'];
			$tipo_doc = $row['tipo_doc'];
			$estampa  = $row['estampa'];
			$salida   = '';
		}else{
			echo 'Registro no encontrado ('.$id.')';
			return '';
		}

		$dbtipo_doc= $this->db->escape($tipo_doc);
		$dbnumero  = $this->db->escape($numero);
		$dbcod_prv = $this->db->escape($cod_prv);

		if(!empty($transac)){
			$dbtransac = $this->db->escape($transac);
			$dbcod_prv = $this->db->escape($cod_prv);
			$td1  = "<td style='border-style:solid;border-width:1px;border-color:#78FFFF;' valign='top' align='center'>\n";
			$td1 .= "<table width='98%'>\n<caption style='background-color:#5E352B;color:#FFFFFF;font-style:bold'>";

			// Movimientos Relacionados en Proveedores SPRM
			$mSQL = "SELECT cod_prv, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
				FROM sprm WHERE transac=${dbtransac} AND id<>${id} ORDER BY cod_prv ";
			$query = $this->db->query($mSQL);
			$salida = '<table width="100%"><tr>';
			$saldo  = 0;
			if($query->num_rows() > 0){
				$salida .= $td1;
				$salida .= 'Movimiento en Proveedores</caption>';
				$salida .= "<tr bgcolor='#E7E3E7'><td>Nombre</td><td>Tp</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row){
					if($row['tipo_doc'] == 'FC' ) {
						$saldo = $row['monto']-$row['abonos'];
					}
					$salida .= '<tr>';
					$salida .= '<td>'.$row['cod_prv'].'-'.$row['nombre'].'</td>';
					$salida .= '<td>'.$row['tipo_doc'].'</td>';
					$salida .= '<td>'.$row['numero'].  '</td>';
					$salida .= "<td align='right'>".nformat($row['monto']).'</td>';
					$salida .= '</tr>';
				}
				if ($saldo <> 0)
					$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo).'</td></tr>';
				$salida .= '</table></td>';
			}

			// Movimientos Relacionados en SMOV
			$mSQL = "SELECT cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
				FROM smov WHERE transac=${dbtransac} ORDER BY cod_cli ";
			$query = $this->db->query($mSQL);
			$saldo = 0;
			if($query->num_rows() > 0){
				$salida .= $td1;
				$salida .= 'Movimiento en Clientes</caption>';
				$salida .= "<tr bgcolor='#e7e3e7'><td>Nombre</td><td>Tp</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row){
					if($row['tipo_doc'] == 'FC'){
						$saldo = $row['monto']-$row['abonos'];
					}
					$salida .= '<tr>';
					$salida .= '<td>'.$row['cod_cli'].'-'.$row['nombre'].'</td>';
					$salida .= '<td>'.$row['tipo_doc'].'</td>';
					$salida .= '<td>'.$row['numero'].  '</td>';
					$salida .= "<td align='right'>".nformat($row['monto']).'</td>';
					$salida .= '</tr>';
				}
				$salida .= '</table></td>';
			}

			//Retencion de IVA RIVA
			$mSQL = "
				SELECT periodo, nrocomp, reiva FROM riva WHERE tipo_doc=${dbtipo_doc} AND numero=${dbnumero} AND MID(transac,1,1)<>'_'";
				"UNION ALL
				SELECT periodo, nrocomp, reiva FROM riva WHERE transac=${dbtransac} AND MID(transac,1,1)<>'_'
				";
			$query = $this->db->query($mSQL);
			if($query->num_rows() > 0){
				$salida .= $td1;
				$salida .= 'Retenciones de IVA</caption>';
				$salida .= "<tr bgcolor='#e7e3e7'><td>Per&iacute;odo</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row){
					$salida .= '<tr>';
					$salida .= '<td>'.$row['periodo'].'</td>';
					$salida .= '<td>'.$row['nrocomp'].'</td>';
					$salida .= "<td align='right'>".nformat($row['reiva']).'</td>';
					$salida .= '</tr>';
				}
				$salida .= '</table></td>';
			}

			$mSQL = "
				SELECT a.tipo_doc, a.numero, a.montonet,a.fecha FROM scst AS a WHERE a.transac=${dbtransac}
				UNION ALL
				SELECT a.tipo_doc, a.numero, a.totneto,a.fecha  FROM gser AS a WHERE a.transac=${dbtransac}";
			$query = $this->db->query($mSQL);
			if($query->num_rows() > 0){
				$salida .= $td1;
				$salida .= 'Gasto/Compra</caption>';
				$salida .= "<tr bgcolor='#e7e3e7'><td>Tipo</td><td align='center'>N&uacute;mero</td><td align='center'>Fecha</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row){
					$salida .= '<tr>';
					$salida .= '<td>'.$row['tipo_doc'].'</td>';
					$salida .= '<td>'.$row['numero'].  '</td>';
					$salida .= '<td align=\'center\'>'.dbdate_to_human($row['fecha']).   '</td>';
					$salida .= '<td align=\'right\'>'.nformat($row['montonet']).'</td>';
					$salida .= '</tr>';
				}
				$salida .= '</table></td>';
			}

			// Movimientos Relacionados ITPPRO
			if($tipo_doc=='AB' || $tipo_doc=='AN' || $tipo_doc=='NC'){
				$mSQL = "SELECT tipo_doc, numero, monto, abono FROM itppro WHERE tipoppro=${dbtipo_doc} AND numppro=${dbnumero} AND cod_prv=${dbcod_prv}";
			}else{
				$mSQL = "SELECT tipoppro tipo_doc, numppro numero, monto, abono FROM itppro WHERE tipo_doc=${dbtipo_doc} AND numero=${dbnumero} AND cod_prv=${dbcod_prv}";
			}
			$query = $this->db->query($mSQL);

			if($query->num_rows() > 0){
				$saldo = 0;
				$salida .= $td1;
				$salida .= 'Movimientos Relacionados</caption>';
				$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td><td align='center'>Abono</td></tr>";
				foreach ($query->result_array() as $row){
					$saldo += $row['abono'];
					$salida .= '<tr>';
					$salida .= '<td>'.$row['tipo_doc'].'</td>';
					$salida .= '<td>'.$row['numero'].  '</td>';
					$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
					$salida .= '<td align=\'right\'>'.nformat($row['abono']).'</td>';
					$salida .= '</tr>';
				}
				$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'><b>Monto Aplicado : ".nformat($saldo).'</b></td></tr>';
				$salida .= '</table></td>';
			}

			// Movimiento en Caja/Bancos
			$mSQL = "SELECT codbanc, tipo_op, numero, monto FROM bmov WHERE transac=${dbtransac} AND monto<>0";
			$query = $this->db->query($mSQL);
			$saldo = 0;
			if($query->num_rows() > 0){
				$salida .= $td1;
				$salida .= 'Movimiento en Caja y/o Bancos</caption>';
				$salida .= "<tr bgcolor='#e7e3e7'><td>Bco</td><td>Tipo</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row){
					$salida .= '<tr>';
					$salida .= '<td>'.$row['codbanc'].'</td>';
					$salida .= '<td>'.$row['tipo_op'].'</td>';
					$salida .= '<td>'.$row['numero'].'</td>';
					$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
					$salida .= '</tr>';
				}
				$salida .= '</table></td>';
			}

			// Prestamos PRMO
			$mSQL = "SELECT tipop, codban, if(observa2='',observa1,observa2) observa, monto FROM prmo WHERE transac=${dbtransac} AND clipro=${dbcod_prv} AND monto<>0";
			$query = $this->db->query($mSQL);
			$saldo = 0;
			if($query->num_rows() > 0){
				$salida .= $td1;
				$salida .= 'Prestamos</caption>';
				$salida .= "<tr bgcolor='#e7e3e7'><td></td><td>Bco</td><td>Observaci&oacute;n</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row){
					$salida .= '<tr>';
					$salida .= '<td>'.$row['tipop'].'</td>';
					$salida .= '<td>'.$row['codban'].'</td>';
					$salida .= '<td>'.$row['observa'].'</td>';
					$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
					$salida .= '</tr>';
				}
				$salida .= '</table></td>';
			}

			//Cruce de Cuentas
			$mSQL = "
				SELECT b.tipo tipo, b.proveed codcp, MID(b.nombre,1,25) nombre, a.onumero, a.monto, b.numero, b.fecha
				FROM itcruc AS a JOIN cruc AS b ON a.numero=b.numero
				WHERE b.proveed=${dbcod_prv} AND b.transac=${dbtransac} AND a.onumero!='${tipo_doc}${numero}'
				UNION ALL
				SELECT b.tipo tipo, b.cliente codcp, MID(b.nomcli,1,25) nombre, a.onumero, a.monto, b.numero, b.fecha
				FROM itcruc AS a JOIN cruc AS b ON a.numero=b.numero
				WHERE b.cliente=${dbcod_prv} AND b.transac=${dbtransac} ORDER BY onumero";

			$query = $this->db->query($mSQL);
			$saldo = 0;
			if( $query->num_rows() > 0 ){
				$salida .= $td1;
				$salida .= 'Cruce de Cuentas</caption>';
				$salida .= "<tr bgcolor='#e7e3e7'><td>Nombre</td><td>C&oacute;digo</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row){
					$salida .= '<tr>';
					$salida .= '<td>('.$row['tipo'].') '.$row['nombre'].'</td>';
					$salida .= '<td>'.$row['codcp'].'</td>';
					$salida .= '<td>'.$row['onumero'].'</td>';
					$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
					$salida .= '</tr>';
				}
				$salida .= '</table></td>';
			}
		}
		echo $salida.'</tr></table>';
	}

	function sprmprint($id){
		$dbid = $this->db->escape($id);
		$tipo = $this->datasis->dameval('SELECT tipo_doc FROM sprm WHERE id='.$dbid);

		switch($tipo){
			case 'NC':
				//Chequea si viene de una retencion
				$mSQL='SELECT a.id
				FROM riva AS a
				JOIN sprm AS b ON a.transac=b.transac AND a.emision=b.fecha
				WHERE b.id='.$dbid;

				$rivc_id=$this->datasis->dameval($mSQL);
				if(!empty($rivc_id)){
					redirect('formatos/ver/RIVA/'.$rivc_id);
					break;
				}else{
					redirect('formatos/descargar/PPRONC/'.$id);
					echo 'Formato no definido';
				}

				break;
			case 'AN':
				redirect('formatos/descargar/PPROANC/'.$id);
				break;
			case 'AB':
				redirect('formatos/descargar/PPROABC/'.$id);
				break;
			case 'FC':
				//Chequea si vino de scst
				$mSQL='SELECT a.id
				FROM scst AS a
				JOIN sprm AS b ON a.transac=b.transac AND a.tipo_doc="FC" AND a.numero=b.numero AND a.recep=b.fecha
				WHERE b.id='.$dbid;
				$scst_id=$this->datasis->dameval($mSQL);
				if(!empty($sfac_id)){
					redirect('formatos/descargar/COMPRA/'.$scst_id);
					break;
				}

				//Chequea si vino de gser
				$mSQL='SELECT a.id
				FROM gser AS a
				JOIN sprm AS b ON a.transac=b.transac AND a.tipo_doc="FC" AND a.numero=b.numero AND a.fecha=b.fecha
				WHERE b.id='.$dbid;
				$gser_id=$this->datasis->dameval($mSQL);
				if(!empty($gser_id)){
					redirect('formatos/descargar/GSER/'.$gser_id);
					break;
				}

				break;
			case 'ND':
				//Chequea si viene de una retencion
				$mSQL='SELECT a.id
				FROM riva AS a
				JOIN sprm AS b ON a.transac=b.transac AND a.emision=b.fecha
				WHERE b.id='.$dbid;
				$rivc_id=$this->datasis->dameval($mSQL);
				if(!empty($rivc_id)){
					redirect('formatos/ver/RIVA/'.$rivc_id);
					break;
				}else{
					echo 'Formato no definido';
				}
				break;
			default:
				echo 'Formato no definido';
		}
	}

	function impcheque($id_gser){
		$dbid=$this->db->escape($id_gser);
		$fila=$this->datasis->damerow('SELECT a.banco,a.tipo_op,a.benefi,b.nombre,a.monto FROM sprm AS a JOIN sprv AS b ON a.cod_prv=b.proveed WHERE a.id='.$dbid);
		$fila['benefi']= trim($fila['benefi']);
		$fila['nombre']= trim($fila['nombre']);

		$banco  = Common::_traetipo($fila['banco']);

		if($banco!='CAJ' && $fila['tipo_op']=='CH'){
			$this->load->library('cheques');
			$nombre = (empty($fila['benefi']))? $fila['nombre']: $fila['benefi'];
			$monto  = $fila['monto'];
			$fecha  = date('Y-m-d');
			$banco  = $banco;
			$this->cheques->genera($nombre,$monto,$banco,$fecha,true);
		}else{
			echo 'Egreso no fue pagado con cheque de banco';
		}
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('', 'sprm');
		$edit->on_save_redirect=false;
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

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
		$do->error_message_ar['pre_ins']='Accion no permitida';
		return false;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='Accion no permitida';
		return false;
	}

	function _pre_delete($do){
		$id         = $do->get('id');
		$transac    = $do->get('transac');
		$tipo_doc   = $do->get('tipo_doc');
		$cod_prv    = $do->get('cod_prv');
		$numero     = $do->get('numero');
		$reteiva    = $do->get('reteiva');
		$abonos     = floatval($do->get('abonos'));

		$dbid       = $this->db->escape($id);
		$dbtransac  = $this->db->escape($transac);
		$dbtipo_doc = $this->db->escape($tipo_doc);
		$dbcod_prv  = $this->db->escape($cod_prv);
		$dbnumero   = $this->db->escape($numero);
		$dbfecha    = $this->db->escape($do->get('fecha'));

		if(empty($transac)){ return true; }

		$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM casi WHERE comprob=${dbtransac}"));
		if($cana>0){
			$do->error_message_ar['pre_del']='El efecto ya esta en contabilidad, no puede ser modificado ni eliminado.';
			return false;
		}

		if($tipo_doc=='ND' || $tipo_doc=='NC'){
			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM cruc WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Movimiento originado a partir de un cruce, debe eliminarlo por el modulo respectivo.';
				return false;
			}

			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM rivc WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Movimiento originado en retenciones de clientes, debe eliminarlo por el modulo respectivo.';
				return false;
			}
		}

		if($tipo_doc=='AN'){
			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM ords WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Anticipo proveniente de una orden de servicio, debe eliminarla por el modulo respectivo.';
				return false;
			}

			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM ordc WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Anticipo proveniente de una orden de compra, debe eliminarla por el modulo respectivo.';
				return false;
			}

			if($abonos>0){
				$do->error_message_ar['pre_del']='Anticipo aplicado, no puede ser eliminado';
				return false;
			}
		}

		if($tipo_doc=='FC'){
			$do->error_message_ar['pre_del']='Factura proveniente de una compra o gasto, debe eliminarla del modulo respectivo.';
			return false;
		}

		if($tipo_doc=='ND'){
			$do->error_message_ar['pre_del']='Debe eliminarlo desde el modulo originario.';
			return false;
		}

		if($tipo_doc=='NC'){
			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM scst WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Movimiento proveniente de compra, debe reversarlo por el modulo respectivo.';
				return false;
			}

			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM gser WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Movimiento proveniente de gasto, debe reversarlo por el modulo respectivo.';
				return false;
			}
		}

		if($tipo_doc=='AB'){
			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM gser WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Abono proveniente de un gasto, debe eliminarlo del modulo respectivo.';
				return false;
			}
		}

		return true;
	}

	function _post_delete($do){
		$transac    = $do->get('transac');
		$tipo_doc   = $do->get('tipo_doc');
		$numero     = $do->get('numero');

		$dbtransac  = $this->db->escape($transac);
		$dbtipo_doc = $this->db->escape($tipo_doc);
		$dbnumero   = $this->db->escape($numero);

		//Deshace las aplicaciones del efecto a eliminar
		$mSQL = "SELECT tipo_doc,numero,numppro,tipoppro,monto,abono,cod_prv,fecha,reteiva FROM itppro WHERE transac=${dbtransac}";
		$query = $this->db->query($mSQL);
		foreach($query->result() as $row){
			$it_tipo_doc= $this->db->escape($row->tipo_doc);
			$it_cod_prv = $this->db->escape($row->cod_prv);
			$it_numero  = $this->db->escape($row->numero);
			$it_fecha   = $this->db->escape($row->fecha);
			$it_reteiva = floatval($row->reteiva);
			$it_abono   = floatval($row->abono);

			if($tipo_doc='NC'){
				$monto=$it_abono-$it_reteiva;
			}else{
				$monto=$it_abono;
			}
			$mSQL="UPDATE sprm SET abonos=abonos-(${monto}) WHERE tipo_doc=${it_tipo_doc} AND numero=${it_numero} AND cod_prv=${it_cod_prv}";
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'sprm'); }
		}
		//Fin

		//Elimina los movimientos de banco
		$mSQL = "SELECT codbanc,fecha,monto,tipo_op,numero,id FROM bmov WHERE transac=${dbtransac}";
		$query = $this->db->query($mSQL);
		foreach($query->result() as $row){
			$it_id       = $row->id;
			$it_fecha    = $row->fecha;
			$it_monto    = floatval($row->monto);

			$sfecha = str_replace('-','',$it_fecha);
			$this->datasis->actusal($row->codbanc, $sfecha, $it_monto);
			//$mSQL  = "UPDATE bmov SET liable='N', anulado='S' ";
			$mSQL = "DELETE FROM bmov WHERE id=${it_id}";
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'sprm'); }
		}
		//Fin de la eliminacion de los movimientos de banco

		//Anula la retencion de IVA
		if($this->datasis->dameval("SELECT COUNT(*) FROM riva WHERE transac=${dbtransac}") > 0){
			$mTRANULA = '_'.$this->datasis->fprox_numero('rivanula',7);
			$this->db->simple_query("UPDATE riva SET transac='${mTRANULA}' WHERE transac=${dbtransac}");
		}
		//Fin de la anulacion de iva

		$mSQL="DELETE FROM itppro WHERE transac=${dbtransac}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sprm'); }

		$mSQL="DELETE FROM sprm WHERE transac=${dbtransac} AND tipo_doc IN ('AB','ND','NC')";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sprm'); }

		//Elimina el gasto asociado
		$mSQL="DELETE FROM gser WHERE transac=${dbtransac}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sprm'); }

		$mSQL="DELETE FROM gitser WHERE transac=${dbtransac}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'sprm'); }
		//Fin de la eliminacion del gasto asociado

		logusu($do->table,"EFECTO ${tipo_doc}${numero} ELIMINADO");
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function selsprv(){
		$this->rapyd->load('dataform');

		$script="
		$('#df1').keypress(function(e){
			if(e.which == 13) return false;
		});

		$('#cod_prv').autocomplete({
			source: function( req, add){
				$.ajax({
					url:  '".site_url('ajax/buscasprv')."',
					type: 'POST',
					dataType: 'json',
					data: {'q':req.term},
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$('#id_sprv').val('');

								$('#nombre').val('');
								$('#nombre_val').text('');

								$('#rifci').val('');
								$('#rifci_val').text('');

								$('#direc').val('');
								$('#direc_val').text('');

								$('#saldo_val').text('');
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
				$('#cod_prv').attr('readonly', 'readonly');

				$('#id_sprv').val(ui.item.id);

				$('#nombre').val(ui.item.nombre);
				$('#nombre_val').text(ui.item.nombre);

				$('#rifci').val(ui.item.rif);
				$('#rifci_val').text(ui.item.rif);

				$('#cod_prv').val(ui.item.proveed);

				$('#direc').val(ui.item.direc);
				$('#direc_val').text(ui.item.direc);
				setTimeout(function() {  $('#cod_prv').removeAttr('readonly'); }, 1500);

				var saldo= $.ajax({ type: 'POST', url: '".site_url($this->url.'ajaxsaldo')."', async: false, data: {cod_prv: ui.item.proveed} }).responseText;
				$('#saldo_val').text(nformat(saldo,2));
			}
		});";

		$form = new DataForm($this->url.'sclise/process');
		$form->script($script);

		$form->proveed = new inputField('Proveedor', 'cod_prv');

		$form->id = new hiddenField('', 'id_sprv');
		$form->id->in='proveed';

		$form->nombre = new freeField('Nombre','nombre','<b id=\'nombre_val\'></b>');

		$form->rif    = new freeField('RIF','rif','<b id=\'rifci_val\'></b>');

		$form->direc  = new freeField('Direcci&oacute;n','direc','<b id=\'direc_val\'></b>');

		$form->saldo  = new freeField('Saldo','saldo','<b style="font-size:2em" id=\'saldo_val\'></b>');

		$form->build_form();

		echo $form->output;
	}

	function ajaxsaldo(){
		$cod_prv = $this->input->post('cod_prv');
		if($cod_prv!==false){
			$this->db->select_sum('a.monto - a.abonos','saldo');
			$this->db->from('sprm AS a');
			$this->db->where('a.cod_prv',$cod_prv);
			$this->db->where('a.monto > a.abonos');
			$this->db->where_in('a.tipo_doc',array('FC','ND','GI'));
			$q=$this->db->get();
			$row = $q->row_array();
			echo (empty($row['saldo']))? 0: $row['saldo'];
		}else{
			echo 0;
		}
	}

	//*****************************************
	// Nota de credito a factura pagada
	//*****************************************
	function ncppro(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Nota de credito a proveedor', 'sprm');
		$edit->on_save_redirect=false;

		$edit->pre_process('insert' , '_pre_ncppro_insert');
		$edit->pre_process('update' , '_pre_ncppro_update');
		$edit->pre_process('delete' , '_pre_ncppro_delete');
		$edit->post_process('insert', '_post_ncppro_insert');

		$edit->cod_prv = new inputField('Proveedor','cod_prv');
		$edit->cod_prv->rule ='max_length[5]';
		$edit->cod_prv->size =8;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->type='inputhidden';
		$edit->nombre->in='cod_prv';

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->codigo = new  dropdownField('Motivo', 'codigo');
		$edit->codigo->option('','Seleccionar');
		$edit->codigo->options('SELECT TRIM(codigo) AS cod, nombre FROM botr WHERE tipo=\'P\' ORDER BY nombre');
		$edit->codigo->style='width:200px;';
		$edit->codigo->rule ='required';

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->size =12;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->calendar = false;
		$edit->fecha->rule ='chfecha|required';

		$edit->observa1 = new  textareaField('Concepto:','observa1');
		$edit->observa1->cols = 70;
		$edit->observa1->rows = 2;
		$edit->observa1->style='width:100%;';

		$edit->observa2 = new  textareaField('','observa2');
		$edit->observa2->cols = 70;
		$edit->observa2->rows = 2;
		$edit->observa2->style='width:100%;';
		$edit->observa2->when=array('show');

		$edit->nfiscal = new inputField('Control F&iacute;scal','nfiscal');
		$edit->nfiscal->rule='required';
		$edit->nfiscal->size =15;
		$edit->nfiscal->maxlength =17;

		$edit->serie = new inputField('N&uacute;mero','serie');
		$edit->serie->rule='required';
		$edit->serie->size =15;
		$edit->serie->maxlength =17;

		$edit->afecta = new inputField('F.Afectada','afecta');
		$edit->afecta->rule='required';
		$edit->afecta->size =15;
		$edit->afecta->maxlength =12;

		$edit->fecapl = new dateonlyField('Fecha','fecapl');
		$edit->fecapl->size =12;
		$edit->fecapl->maxlength =8;
		$edit->fecapl->type='inputhidden';
		$edit->fecapl->calendar = false;
		$edit->fecapl->rule ='chfecha|required';

		$edit->depto = new  dropdownField('Asignar a departamento', 'depto');
		$edit->depto->option('','Seleccionar');
		$edit->depto->options('SELECT depto,CONCAT_WS(\'-\',depto,TRIM(descrip)) AS descrip FROM dpto WHERE tipo=\'G\' ORDER BY descrip');
		$edit->depto->style='width:200px;';
		$edit->depto->rule ='required';

		$ivas = $this->datasis->ivaplica();
		$edit->ptasa = new inputField('','ptasa');
		$edit->ptasa->rule='numeric';
		$edit->ptasa->type='inputhidden';
		$edit->ptasa->insertValue=$ivas['tasa'];
		$edit->ptasa->showformat='decimal';

		$edit->preducida = new inputField('','preducida');
		$edit->preducida->rule='numeric';
		$edit->preducida->type='inputhidden';
		$edit->preducida->insertValue=$ivas['redutasa'];
		$edit->preducida->showformat='decimal';

		$edit->padicional = new inputField('','padicional');
		$edit->padicional->rule='numeric';
		$edit->padicional->type='inputhidden';
		$edit->padicional->insertValue=$ivas['sobretasa'];
		$edit->padicional->showformat='decimal';

		//bases de los impuestos
		$edit->montasa = new inputField('Montasa','montasa');
		$edit->montasa->rule      ='max_length[17]|numeric|positive';
		$edit->montasa->css_class ='inputnum';
		$edit->montasa->size      =19;
		$edit->montasa->maxlength =17;
		$edit->montasa->rule='condi_required';

		$edit->monredu = new inputField('Monredu','monredu');
		$edit->monredu->rule      ='max_length[17]|numeric|positive';
		$edit->monredu->css_class ='inputnum';
		$edit->monredu->size      =19;
		$edit->monredu->maxlength =17;
		$edit->monredu->rule='condi_required';

		$edit->monadic = new inputField('Monadic','monadic');
		$edit->monadic->rule      ='max_length[17]|numeric|positive';
		$edit->monadic->css_class ='inputnum';
		$edit->monadic->size      =19;
		$edit->monadic->maxlength =17;
		$edit->monadic->rule='condi_required';
		//fin de las bases de los impuestos

		$edit->tasa = new inputField('general','tasa');
		$edit->tasa->rule      ='max_length[17]|numeric';
		$edit->tasa->css_class ='inputnum';
		$edit->tasa->size      =12;
		$edit->tasa->maxlength =17;
		$edit->tasa->rule='condi_required|callback_chmontasa[G]';

		$edit->reducida = new inputField('reducida','reducida');
		$edit->reducida->rule      ='max_length[17]|numeric|positive';
		$edit->reducida->css_class ='inputnum';
		$edit->reducida->size      =12;
		$edit->reducida->maxlength =17;
		$edit->reducida->rule='condi_required|callback_chmontasa[R]';

		$edit->sobretasa = new inputField('adicional','sobretasa');
		$edit->sobretasa->rule      ='max_length[17]|numeric|positive';
		$edit->sobretasa->css_class ='inputnum';
		$edit->sobretasa->size      =12;
		$edit->sobretasa->maxlength =17;
		$edit->sobretasa->rule='condi_required|callback_chmontasa[A]|positive';

		$edit->exento = new inputField('Exento','exento');
		$edit->exento->rule      ='max_length[17]|numeric';
		$edit->exento->css_class ='inputnum';
		$edit->exento->size      =19;
		$edit->exento->maxlength =17;
		$edit->exento->rule='condi_required|positive';

		$edit->reteiva = new inputField('Ret. IVA','reteiva');
		$edit->reteiva->rule      ='max_length[17]|numeric';
		$edit->reteiva->css_class ='inputnum';
		$edit->reteiva->size      =19;
		$edit->reteiva->maxlength =17;
		$edit->reteiva->insertValue='0';
		$edit->reteiva->rule='condi_required|callback_chobligatipo[NC]|positive';

		$edit->monto = new inputField('Total a pagar','monto');
		$edit->monto->rule='required|max_length[17]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =19;
		$edit->monto->maxlength =17;
		$edit->monto->type='inputhidden';

		//Campos comodines
		$edit->sprvreteiva = new hiddenField('','sprvreteiva');
		$edit->aplrete     = new hiddenField('','aplrete');
		//Fin de los campos comodines

		$edit->tipo_doc= new autoUpdateField('tipo_doc','NC', 'NC');
		$edit->usuario = new autoUpdateField('usuario' ,$this->secu->usuario(),$this->secu->usuario());
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora'    ,date('H:i:s'), date('H:i:s'));

		$arr_ptasa = array();
		$edit->apltasa = new dropdownField('', 'apltasa');
		$mSQL='SELECT fecha,tasa,redutasa,sobretasa FROM civa ORDER BY fecha DESC LIMIT 3';
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $row){
			$arr_ptasa[] = array(floatval($row->tasa),floatval($row->redutasa),floatval($row->sobretasa));
			$edit->apltasa->option($row->fecha,dbdate_to_human($row->fecha));
		}
		$edit->apltasa->onchange='chapltasa()';
		$edit->apltasa->style='width:100px;';
		$edit->apltasa->rule='required';

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);

			echo json_encode($rt);
		}else{

			$conten['json_ptasa']= json_encode($arr_ptasa);
			$conten['form']      =& $edit;
			$conten['title']     = heading('Nota de cr&eacute;dito a factura pagada de proveedor');

			$this->load->view('view_ncppro', $conten);
		}

	}
	//*****************************************
	// Fin Nota de credito a factura pagada
	//*****************************************

	function _pre_ncppro_insert($do){

		$montasa   = floatval($do->get('montasa'  ));
		$monredu   = floatval($do->get('monredu'  ));
		$monadic   = floatval($do->get('monadic'  ));
		$tasa      = floatval($do->get('tasa'     ));
		$reducida  = floatval($do->get('reducida' ));
		$sobretasa = floatval($do->get('sobretasa'));
		$exento    = floatval($do->get('exento'   ));

		$impuesto= $tasa+$reducida+$sobretasa;
		$monto   = $montasa+$monredu+$monadic+$tasa+$reducida+$sobretasa+$exento;

		$transac   = $this->datasis->prox_sql('ntransa' ,8);
		$mcontrol  = $this->datasis->prox_sql('nsprm'   ,8);
		$mncausado = $this->datasis->prox_sql('ncausado',8);

		$do->rm_get('aplrete');
		$do->rm_get('sprvrete');

		$do->set('vence'   , $fecha);
		$do->set('causado' , $mncausado);
		$do->set('negreso' , '');
		$do->set('ndebito' , '');
		$do->set('monto'   , $monto);
		$do->set('impuesto', $impuesto);
		$do->set('reten'   , 0 );
		$do->set('ppago'   , 0 );
		$do->set('control' , $mcontrol);
		$do->set('cambio'  , 0 );
		$do->set('nfiscal' , '') ;
		$do->set('mora'    , 0 );
		$do->set('comprob' , '');
		$do->set('banco'   , '');
		$do->set('tipo_op' , '');
		$do->set('numche'  , '');
		$do->set('benefi'  , '');
		$do->set('posdata' , '');
		$do->set('abonos'  , 0);
		$do->set('fecapl'  ,$fecha);
		$do->set('fecdoc'  ,$itfecha);

	}

	function _post_ncppro_insert($do){
		$this->_post_pprv_insert($do);
	}

	function _pre_ncppro_update($do){
		return false;
	}

	function _pre_ncppro_delete($do){
		return false;
	}

	//*****************************************
	// Inicio pago a proveedor
	//*****************************************
	function pprov($id_sprv){
		$id_sprv=intval($id_sprv);
		$row = $this->datasis->damerow('SELECT proveed,nombre,rif FROM sprv WHERE id='.$id_sprv);
		if(empty($row)){
			echo 'El usuario debe tener registrado un cajero para poder usar este modulo';
			return '';
		}
		$proveed     = $row['proveed'];
		$sprv_nombre = $row['nombre'];
		$sprv_rif    = $row['rif'];


		if(date('d')<=15){
			$pdia  ='01';
			$dia   ='15';
		}else{
			$pdia  ='16';
			$dia   =date('d', mktime(0, 0, 0, date('n'), 0));
		}
		$rivafechai =date('Ym'.$pdia);
		$rivafechac =date('Ym'.$dia );

		$this->rapyd->load('dataobject','datadetails');

		$do = new DataObject('sprm');
		$do->rel_one_to_many('itppro', 'itppro', array(
			'tipo_doc'=>'tipoppro',
			'numero'  =>'numppro',
			'cod_prv' =>'cod_prv',
			'transac' =>'transac')
		);
		$do->order_by('itppro','itppro.fecha');

		$edit = new DataDetails('Pago a proveedor', $do);
		$edit->on_save_redirect=false;
		$edit->set_rel_title('itppro', 'Efecto <#o#>');
		//$edit->set_rel_title('sfpa'  , 'Forma de pago <#o#>');

		$edit->pre_process('insert' , '_pre_pprv_insert');
		$edit->pre_process('update' , '_pre_pprv_update');
		$edit->pre_process('delete' , '_pre_pprv_delete');
		$edit->post_process('insert', '_post_pprv_insert');
		//$edit->post_process('delete', '_post_pprv_delete');

		$edit->cod_prv = new hiddenField('Proveedor','cod_prv');
		$edit->cod_prv->rule ='max_length[5]';
		$edit->cod_prv->size =7;
		$edit->cod_prv->insertValue=$proveed;
		$edit->cod_prv->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[40]';
		$edit->nombre->size =42;
		$edit->nombre->maxlength =40;

		$edit->tipo_doc = new  dropdownField('Tipo doc.', 'tipo_doc');
		$edit->tipo_doc->option('AB','Abono');
		$edit->tipo_doc->option('NC','Nota de credito');
		$edit->tipo_doc->option('AN','Anticipo');
		$edit->tipo_doc->onchange='chtipodoc()';
		$edit->tipo_doc->style='width:140px;';
		$edit->tipo_doc->rule ='enum[AB,NC,AN]|required';

		$edit->codigo = new  dropdownField('Motivo', 'codigo');
		$edit->codigo->option('','Seleccionar');
		$edit->codigo->options('SELECT TRIM(codigo) AS cod, nombre FROM botr WHERE tipo=\'P\' ORDER BY nombre');
		$edit->codigo->style='width:200px;';
		$edit->codigo->rule ='condi_required|callback_chobligatipo[NC]';

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->size =12;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->calendar = false;
		$edit->fecha->rule ='chfecha|required';

		$edit->monto = new inputField('Total a pagar','monto');
		$edit->monto->rule='required|max_length[17]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =19;
		$edit->monto->maxlength =17;
		$edit->monto->type='inputhidden';

		$edit->observa1 = new  textareaField('Concepto:','observa1');
		$edit->observa1->cols = 70;
		$edit->observa1->rows = 2;
		$edit->observa1->style='width:100%;';

		$edit->observa2 = new  textareaField('','observa2');
		$edit->observa2->cols = 70;
		$edit->observa2->rows = 2;
		$edit->observa2->style='width:100%;';
		$edit->observa2->when=array('show');

		$edit->depto = new  dropdownField('Asignar a departamento', 'depto');
		$edit->depto->option('','Seleccionar');
		$edit->depto->options('SELECT depto,CONCAT_WS(\'-\',depto,TRIM(descrip)) AS descrip FROM dpto WHERE tipo=\'G\' ORDER BY descrip');
		$edit->depto->style='width:180px;';
		$edit->depto->rule ='condi_required|callback_chdepto';

		$edit->usuario = new autoUpdateField('usuario' ,$this->secu->usuario(),$this->secu->usuario());
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora'    ,date('H:i:s'), date('H:i:s'));

		//Campos propios de las NC

		//Campos comodines
		$arr_ptasa = array();
		$edit->apltasa = new dropdownField('', 'apltasa');
		$mSQL='SELECT fecha,tasa,redutasa,sobretasa FROM civa ORDER BY fecha DESC LIMIT 3';
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $row){
			$arr_ptasa[] = array(floatval($row->tasa),floatval($row->redutasa),floatval($row->sobretasa));
			$edit->apltasa->option($row->fecha,dbdate_to_human($row->fecha));
		}
		$edit->apltasa->onchange='chapltasa()';
		$edit->apltasa->style='width:100px;';
		$edit->apltasa->rule='condi_required|callback_chobligatipo[NC]';

		$ivas = $this->datasis->ivaplica();
		$edit->ptasa = new inputField('','ptasa');
		$edit->ptasa->rule='numeric';
		$edit->ptasa->type='inputhidden';
		$edit->ptasa->insertValue=$ivas['tasa'];
		$edit->ptasa->showformat='decimal';

		$edit->preducida = new inputField('','preducida');
		$edit->preducida->rule='numeric';
		$edit->preducida->type='inputhidden';
		$edit->preducida->insertValue=$ivas['redutasa'];
		$edit->preducida->showformat='decimal';

		$edit->padicional = new inputField('','padicional');
		$edit->padicional->rule='numeric';
		$edit->padicional->type='inputhidden';
		$edit->padicional->insertValue=$ivas['sobretasa'];
		$edit->padicional->showformat='decimal';
		//Fin de los comodines

		$edit->serie = new inputField('N&uacute;mero','serie');
		$edit->serie->rule='condi_required|callback_chobligatipo[NC]';
		$edit->serie->size =15;
		$edit->serie->maxlength =17;

		$edit->nfiscal = new inputField('Control F&iacute;scal','nfiscal');
		$edit->nfiscal->rule='condi_required|callback_chobligatipo[NC]';
		$edit->nfiscal->size =15;
		$edit->nfiscal->maxlength =17;

		$edit->montasa = new inputField('Montasa','montasa');
		$edit->montasa->rule      ='max_length[17]|numeric|positive';
		$edit->montasa->css_class ='inputnum';
		$edit->montasa->size      =19;
		$edit->montasa->maxlength =17;
		$edit->montasa->rule='condi_required|callback_chobligatipo[NC]';

		$edit->monredu = new inputField('Monredu','monredu');
		$edit->monredu->rule      ='max_length[17]|numeric|positive';
		$edit->monredu->css_class ='inputnum';
		$edit->monredu->size      =19;
		$edit->monredu->maxlength =17;
		$edit->monredu->rule='condi_required|callback_chobligatipo[NC]';

		$edit->monadic = new inputField('Monadic','monadic');
		$edit->monadic->rule      ='max_length[17]|numeric|positive';
		$edit->monadic->css_class ='inputnum';
		$edit->monadic->size      =19;
		$edit->monadic->maxlength =17;
		$edit->monadic->rule='condi_required|callback_chobligatipo[NC]';

		$edit->tasa = new inputField('general','tasa');
		$edit->tasa->rule      ='max_length[17]|numeric';
		$edit->tasa->css_class ='inputnum';
		$edit->tasa->size      =12;
		$edit->tasa->maxlength =17;
		$edit->tasa->rule='condi_required|callback_chobligatipo[NC]|callback_chmontasa[G]';

		$edit->reducida = new inputField('reducida','reducida');
		$edit->reducida->rule      ='max_length[17]|numeric|positive';
		$edit->reducida->css_class ='inputnum';
		$edit->reducida->size      =12;
		$edit->reducida->maxlength =17;
		$edit->reducida->rule='condi_required|callback_chobligatipo[NC]|callback_chmontasa[R]';

		$edit->sobretasa = new inputField('adicional','sobretasa');
		$edit->sobretasa->rule      ='max_length[17]|numeric|positive';
		$edit->sobretasa->css_class ='inputnum';
		$edit->sobretasa->size      =12;
		$edit->sobretasa->maxlength =17;
		$edit->sobretasa->rule='condi_required|callback_chobligatipo[NC]|callback_chmontasa[A]|positive';

		$edit->exento = new inputField('exento','exento');
		$edit->exento->rule      ='max_length[17]|numeric';
		$edit->exento->css_class ='inputnum';
		$edit->exento->size      =19;
		$edit->exento->maxlength =17;
		$edit->exento->rule='condi_required|callback_chobligatipo[NC]|positive';

		$edit->reteiva = new inputField('Ret. IVA','reteiva');
		$edit->reteiva->rule      ='max_length[17]|numeric';
		$edit->reteiva->css_class ='inputnum';
		$edit->reteiva->size      =19;
		$edit->reteiva->maxlength =17;
		$edit->reteiva->insertValue='0';
		$edit->reteiva->rule='condi_required|callback_chobligatipo[NC]|positive';

		//Para la retencion de iva si aplica
		$contribu= trim($this->datasis->traevalor('CONTRIBUYENTE'));
		$rif     = trim($this->datasis->traevalor('RIF'));
		if($contribu=='ESPECIAL' && strtoupper($rif[0])!='V'){
			$por_rete=$this->datasis->dameval('SELECT reteiva FROM sprv WHERE proveed='.$this->db->escape($proveed));
			if($por_rete!=100){
				$por_rete=0.75;
			}else{
				$por_rete=$por_rete/100;
			}
		}else{
			$por_rete=-1;
		}
		//fin de la retencion

		//Fin de los campos para la nc

		//Detalle del pago
		$edit->banco = new dropdownField('Banco', 'banco');
		$edit->banco->option('','Seleccionar');
		$edit->banco->options('SELECT TRIM(codbanc) AS codbanc,CONCAT_WS(\' \',TRIM(codbanc),TRIM(banco),numcuent) FROM banc ORDER BY banco');
		$edit->banco->style  = 'width:200px;';
		$edit->banco->rule   = 'condi_required|callback_chbanc';

		$edit->tipo_op = new  dropdownField('Tipo', 'tipo_op');
		$edit->tipo_op->option('CH','Cheque');
		$edit->tipo_op->option('ND','Nota de debito');
		$edit->tipo_op->style='width:150px;';
		$edit->tipo_op->rule ='condi_required|enum[CH,ND]|callback_chtipoop';

		$edit->numche = new inputField('N&uacute;mero', 'numche');
		$edit->numche->size     = 12;
		$edit->numche->rule     = 'condi_required|callback_chtipo';

		$edit->benefi = new inputField('Beneficiario', 'benefi');
		$edit->benefi->size       = 12;
		$edit->benefi->rule       = 'condi_required|callback_chtipo';
		$edit->benefi->style      = 'width:90%;';
		$edit->benefi->insertValue= $sprv_nombre;

		$edit->posdata = new dateonlyField('Fecha','posdata');
		$edit->posdata->size =12;
		$edit->posdata->maxlength =8;
		$edit->posdata->insertValue=date('Y-m-d');
		$edit->posdata->calendar = false;
		$edit->posdata->rule ='condi_required|chfecha';
		//Fin del detalle del pago

		//************************************************
		//inicio detalle itppro
		//************************************************
		$i=0;
		$arr_ivas=array();
		$edit->detail_expand_except('itppro');
		$sel=array('a.tipo_doc','a.numero','a.fecha','a.vence','a.monto','a.abonos','a.monto - a.abonos AS saldo','impuesto','reteiva','montasa','monredu','monadic','tasa','reducida','sobretasa','exento');
		$this->db->select($sel);
		$this->db->from('sprm AS a');
		$this->db->where('a.cod_prv',$proveed);
		$transac=$edit->get_from_dataobjetct('transac');
		if($transac!==false){
			$tipo_doc =$edit->get_from_dataobjetct('tipo_doc');
			$dbtransac=$this->db->escape($transac);
			$this->db->join('itppro AS b','a.tipo_doc = b.tipoccli AND a.numero=b.numccli AND a.transac='.$dbtransac);
			$this->db->where('a.tipo_doc',$tipo_doc);
		}else{
			$this->db->where('a.monto > a.abonos');
			$this->db->where_in('a.tipo_doc',array('FC','ND','GI'));
		}
		$this->db->order_by('a.fecha');
		$query = $this->db->get();
		//echo $this->db->last_query();
		foreach ($query->result() as $row){

			$row->montasa   = floatval($row->montasa);
			$row->monredu   = floatval($row->monredu);
			$row->monadic   = floatval($row->monadic);
			$row->tasa      = floatval($row->tasa);
			$row->reducida  = floatval($row->reducida);
			$row->sobretasa = floatval($row->sobretasa);
			$row->exento    = floatval($row->exento);

			if($row->montasa+$row->monredu+$row->monadic+$row->tasa+$row->reducida+$row->sobretasa+$row->exento > 0){
				$arr_ivas[$i]=array(
					'montasa'  =>$row->montasa  ,
					'monredu'  =>$row->monredu  ,
					'monadic'  =>$row->monadic  ,
					'tasa'     =>$row->tasa     ,
					'reducida' =>$row->reducida ,
					'sobretasa'=>$row->sobretasa,
					'exento'   =>$row->exento
				);
			}else{
				$arr_ivas[$i]=array(
					'montasa'  =>$row->monto-$row->impuesto,
					'monredu'  =>0,
					'monadic'  =>0,
					'tasa'     =>floatval($row->impuesto),
					'reducida' =>0,
					'sobretasa'=>0,
					'exento'   =>$row->exento
				);
			}

			$obj='cod_prv_'.$i;
			$edit->$obj = new autoUpdateField('cod_prv',$proveed,$proveed);
			$edit->$obj->rel_id  = 'itppro';
			$edit->$obj->ind     = $i;

			$obj='tipo_doc_'.$i;
			$edit->$obj = new inputField('Tipo_doc',$obj);
			$edit->$obj->db_name='tipo_doc';
			$edit->$obj->rel_id = 'itppro';
			$edit->$obj->rule='max_length[2]';
			$edit->$obj->insertValue=$row->tipo_doc;
			$edit->$obj->size =4;
			$edit->$obj->maxlength =2;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='numero_'.$i;
			$edit->$obj = new inputField('Numero',$obj);
			$edit->$obj->db_name='numero';
			$edit->$obj->rel_id = 'itppro';
			$edit->$obj->rule='max_length[8]';
			$edit->$obj->insertValue=$row->numero;
			$edit->$obj->size =10;
			$edit->$obj->maxlength =8;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='fecha_'.$i;
			$edit->$obj = new dateonlyField('Fecha',$obj);
			$edit->$obj->db_name='fecha';
			$edit->$obj->rel_id = 'itppro';
			$edit->$obj->rule='chfecha';
			$edit->$obj->insertValue=$row->fecha;
			$edit->$obj->size =10;
			$edit->$obj->maxlength =8;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='monto_'.$i;
			$edit->$obj = new inputField('Monto',$obj);
			$edit->$obj->db_name='monto';
			$edit->$obj->rel_id = 'itppro';
			$edit->$obj->rule='max_length[18]|numeric';
			$edit->$obj->css_class='inputnum';
			$edit->$obj->size =20;
			$edit->$obj->insertValue=$row->monto;
			$edit->$obj->maxlength =18;
			$edit->$obj->ind       = $i;
			$edit->$obj->showformat='decimal';
			$edit->$obj->type='inputhidden';

			$obj='riva_'.$i;
			$edit->$obj = new hiddenField('riva',$obj);
			$edit->$obj->db_name='riva';
			$edit->$obj->rel_id = 'itppro';
			$fecha  = str_replace('-','',$row->fecha);
			if(floatval($row->reteiva)>0){
				if($fecha>=$rivafechai && $fecha<=$rivafechac){
					$aplrete='S';
				}else{
					$aplrete='V';
				}
			}else{
				$aplrete='N';
			}
			$edit->$obj->insertValue= $aplrete;
			$edit->$obj->ind        = $i;
			$edit->$obj->showformat ='decimal';

			$obj='saldo_'.$i;
			$edit->$obj = new freeField($obj,$obj,nformat($row->saldo));
			$edit->$obj->ind = $i;

			$obj='vence_'.$i;
			$edit->$obj = new freeField($obj,$obj,dbdate_to_human($row->vence));
			$edit->$obj->ind = $i;

			$obj='abono_'.$i;
			$edit->$obj = new inputField('Abono',$obj);
			$edit->$obj->db_name      = 'abono';
			$edit->$obj->rel_id       = 'itppro';
			$edit->$obj->rule         = "max_length[18]|numeric|positive|callback_chabono[$i]";
			$edit->$obj->css_class    = 'inputnum';
			$edit->$obj->showformat   = 'decimal';
			$edit->$obj->autocomplete = false;
			$edit->$obj->disable_paste= true;
			$edit->$obj->size         = 15;
			$edit->$obj->maxlength    = 18;
			$edit->$obj->ind          = $i;
			$edit->$obj->onfocus      = 'itsaldo(this,'.round($row->saldo,2).');';

			$i++;
		}
		//************************************************
		//fin de campos para detalle
		//************************************************

		$edit->tipo_doc = new  dropdownField('Tipo doc.', 'tipo_doc');
		if($i>0){
			$edit->tipo_doc->option('AB','Abono');
			$edit->tipo_doc->option('NC','Nota de credito');
		}else{
			$edit->tipo_doc->insertValue='AN';
		}
		$edit->tipo_doc->option('AN','Anticipo');
		$edit->tipo_doc->onchange='chtipodoc()';
		$edit->tipo_doc->style='width:140px;';
		$edit->tipo_doc->rule ='enum[AB,NC,AN]|required';

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
			$conten['json_ptasa']= json_encode($arr_ptasa);
			$conten['json_ivas'] = json_encode($arr_ivas);
			$conten['cana']      = $i;
			$conten['form']      = & $edit;
			$conten['title']     = heading("Pago a proveedor: (${proveed}) ${sprv_nombre} ${sprv_rif}");
			$conten['por_rete']  = $por_rete;

			$this->load->view('view_pprv', $conten);
		}
	}

	function chdepto($dpto){
		$tipo  = $this->input->post('tipo_doc');
		if($tipo=='AN' && empty($dpto)){
			$this->validation->set_message('chdepto', 'El campo %s es necesario para las notas de credito.');
			return false;
		}
		return true;
	}

	function chbanc($val){
		$tipo  = $this->input->post('tipo_doc');
		if($tipo=='NC') return true;
		if(empty($val)){
			$this->validation->set_message('chbanc', 'El campo %s es obligatorio.');
			return false;
		}else{
			if(!$this->validation->existeban($val)){
				$this->validation->set_message('chbanc', 'El banco propuesto en el campo %s no existe.');
				return false;
			}
		}
		return true;
	}

	function chtipoop($val){
		$tipo  = $this->input->post('tipo_doc');
		if($tipo=='NC') return true;
		$banco  = $this->input->post('banco');
		$dbbanco= $this->db->escape($banco);
		$tbanco = trim($this->datasis->dameval("SELECT tbanco FROM banc WHERE codbanc=${dbbanco}"));

		if($tbanco=='CAJ' && $val=='CH'){
			$this->validation->set_message('chtipoop', 'El campo %s no puede ser cheque cuando se paga por caja.');
			return false;
		}
		return true;
	}

	function chppago($monto,$i){
		$tipo   = $this->input->post('tipo_doc');
		$monto  = floatval($monto);
		$itmonto= floatval($this->input->post('abono_'.$i));
		if($tipo=='NC' && $monto>0){
			$this->validation->set_message('chppago', 'No se puede hacer pronto pago cuando el tipo de documento es una nota de cr&eacute;dito.');
			return false;
		}

		if($itmonto<=0 && $monto>0){
			$this->validation->set_message('chppago', 'No se puede hacer pronto pago cuando a un efecto que no esta abonado.');
			return false;
		}
		return true;
	}

	function chmontasa($impuesto,$tipo){
		if($tipo=='R'){
			$base  = floatval($this->input->post('monredu'));
			$ptasa = floatval($this->input->post('preducida'));
		}elseif($tipo=='A'){
			$base  = floatval($this->input->post('monadic'));
			$ptasa = floatval($this->input->post('psobretasa'));
		}else{
			$base  = floatval($this->input->post('montasa'));
			$ptasa = floatval($this->input->post('ptasa'));
		}
		$ptasa = $ptasa/100;
		$impuesto=floatval($impuesto);
		if(abs($impuesto-($base*$ptasa))>0.2){
			$this->validation->set_message('chmontasa', 'El monto del impuesto %s no coincide con la tasa.');
			return false;
		}
		return true;
	}

	function _pre_pprv_insert($do){
		$tipo_doc = $do->get('tipo_doc');
		$estampa  = $do->get('estampa');
		$usuario  = $do->get('usuario');
		$hora     = $do->get('hora');
		$transac  = $do->get('transac');
		$cod_prv  = $do->get('cod_prv');
		$banco    = $do->get('banco');
		$dbcod_prv= $this->db->escape($cod_prv);
		$tipo_op  = $do->get('tipo_op');
		$fecha    = $do->get('fecha');
		$codigo   = $do->get('codigo');
		$reteiva  = floatval($do->get('reteiva'));

		$this->ppagodata=$ivadata=array(
			'montasa'  =>0,
			'monredu'  =>0,
			'monadic'  =>0,
			'tasa'     =>0,
			'reducida' =>0,
			'sobretasa'=>0,
			'exento'   =>0
		);

		//Elimina los comodines
		$do->rm_get('ptasa');
		$do->rm_get('preducida');
		$do->rm_get('padicional');
		$do->rm_get('apltasa');

		$cfecha=intval(str_replace('-','',$fecha));
		$ppago=$totalab=$impuesto=$ppimpuesto=0;
		$arr_itimpuestos=array();
		//Totaliza el abonado
		$rel='itppro';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$itabono  = floatval($do->get_rel($rel, 'abono', $i));
			$itpppago = floatval($do->get_rel($rel, 'ppago', $i));
			$ittipo   = $do->get_rel($rel, 'tipo_doc', $i);
			$itnumero = $do->get_rel($rel, 'numero'  , $i);
			$itfecha  = $do->get_rel($rel, 'fecha'   , $i);

			if(empty($itabono) || $itabono==0){
				continue;
			}else{
				$totalab   += $itabono;
				$dbittipo   = $this->db->escape($ittipo);
				$dbitnumero = $this->db->escape($itnumero);

				$citfecha  = intval(str_replace('-','',$itfecha));
				if($citfecha>$cfecha){
					$do->error_message_ar['pre_ins']='No puede pagar un efecto con una fecha anterior a su emision como el caso de '.$ittipo.$itnumero;
					return false;
				}

				$rrow=$this->datasis->damerow("SELECT impuesto,monto,montasa,monredu,monadic,tasa,reducida,sobretasa,exento FROM sprm WHERE cod_prv=${dbcod_prv} AND tipo_doc=${dbittipo} AND numero=${dbitnumero}");
				if(empty($rrow)){
					$do->error_message_ar['pre_ins']='Efecto inexistente '.$ittipo.$itnumero;
					return false;
				}
				$itimpuesto = floatval($rrow['impuesto']);
				$itmonto    = floatval($rrow['monto']);
				$arr_itimpuestos[$i]=round($itabono*$itimpuesto/$itmonto,2);

				if($tipo_doc=='NC'){

					$ivadata['montasa'  ] += floatval($rrow['montasa'  ])*$itabono/$itmonto;
					$ivadata['monredu'  ] += floatval($rrow['monredu'  ])*$itabono/$itmonto;
					$ivadata['monadic'  ] += floatval($rrow['monadic'  ])*$itabono/$itmonto;
					$ivadata['tasa'     ] += floatval($rrow['tasa'     ])*$itabono/$itmonto;
					$ivadata['reducida' ] += floatval($rrow['reducida' ])*$itabono/$itmonto;
					$ivadata['sobretasa'] += floatval($rrow['sobretasa'])*$itabono/$itmonto;
					$ivadata['exento'   ] += floatval($rrow['exento'   ])*$itabono/$itmonto;
				}

				if(empty($itpppago)){
					$do->set_rel($rel,'ppago',0,$i);
					$itpppago=0;
				}else{
					$ppago     += $do->get_rel($rel, 'ppago', $i);
					$ppimpuesto+= round($itpppago*$itimpuesto/$itmonto,2);

					$this->ppagodata['montasa'  ]= floatval($rrow['montasa'  ])*$itpppago/$itmonto;
					$this->ppagodata['monredu'  ]= floatval($rrow['monredu'  ])*$itpppago/$itmonto;
					$this->ppagodata['monadic'  ]= floatval($rrow['monadic'  ])*$itpppago/$itmonto;
					$this->ppagodata['tasa'     ]= floatval($rrow['tasa'     ])*$itpppago/$itmonto;
					$this->ppagodata['reducida' ]= floatval($rrow['reducida' ])*$itpppago/$itmonto;
					$this->ppagodata['sobretasa']= floatval($rrow['sobretasa'])*$itpppago/$itmonto;
					$this->ppagodata['exento'   ]= floatval($rrow['exento'   ])*$itpppago/$itmonto;
				}

				$impuesto  += round(($itabono-$itpppago)*$itimpuesto/$itmonto,2);
			}

			$do->rel_rm_field($rel,'riva',$i);
		}
		$totalab=round($totalab,2);
		$this->ppimpuesto=$ppimpuesto;

		if($tipo_doc=='NC'){
			$montasa   = $do->get('montasa'  );
			$monredu   = $do->get('monredu'  );
			$monadic   = $do->get('monadic'  );
			$tasa      = $do->get('tasa'     );
			$reducida  = $do->get('reducida' );
			$sobretasa = $do->get('sobretasa');
			$exento    = $do->get('exento'   );

			//Limpia la forma de pago ya que no se necesita para una NC
			$do->set('banco'  ,'');
			$do->set('tipo_op','');
			$do->set('numche' ,'');
			$do->set('benefi' ,'');
			$do->set('posdata','');

			$iivast = $montasa+$monredu+$monadic+$tasa+$reducida+$sobretasa+$exento;

			if(abs($iivast-$totalab)>0.2){
				$do->error_message_ar['pre_ins']='El monto detallado de los impuestos no coincide con el monto total.';
				return false;
			}

			if($montasa<=0 && $ivadata['montasa']>0){
				$do->error_message_ar['pre_ins']='No puede realizar una NC con impuesto general si los documentos afectados no tienen este impuesto.';
				return false;
			}

			if($monredu<=0 && $ivadata['monredu']>0){
				$do->error_message_ar['pre_ins']='No puede realizar una NC con impuesto reducido si los documentos afectados no tienen este impuesto.';
				return false;
			}

			if($sobretasa<=0 && $ivadata['sobretasa']>0){
				$do->error_message_ar['pre_ins']='No puede realizar una NC con impuesto adicional si los documentos afectados no tienen este impuesto.';
				return false;
			}

			if($exento<=0 && $ivadata['exento']>0){
				$do->error_message_ar['pre_ins']='No puede realizar una NC con monto exento si los documentos afectados no tienen este monto.';
				return false;
			}
		}else{
			$reteiva=0;
		}

		//Inicio Validaciones
		if($tipo_doc=='NC' && $ppago>0){
			$do->error_message_ar['pre_ins']='No puede tener una nota de credito con pronto pago.';
			return false;
		}
		if($tipo_doc=='NC' || $tipo_doc=='AB'){
			//Debe borrar los detalles del pago
			if($totalab==0){
				$do->error_message_ar['pre_ins']='Debe relacionar el pago con algun movimiento';
				return false;
			}
		}elseif($tipo_doc=='AN'){

			$do->truncate_rel('itppro');
			if($totalab!=0){
				$do->error_message_ar['pre_ins']='Un anticipo no puede estar relacionado con algun efecto, en tal caso seria un abono';
				return false;
			}
			$totalab= floatval($do->get('monto'));
			if($totalab<=0){
				$do->error_message_ar['pre_ins']='Debe colocar el monto del anticipo.';
				return false;
			}
			$ppago  = 0;
		}
		//Fin de las validaciones


		$transac  = $this->datasis->prox_sql('ntransa',8);
		$mcontrol = $this->datasis->prox_sql('nsprm'  ,8);
		if($tipo_doc!='NC'){
			$mnroegre  = $this->datasis->prox_sql('negreso',8);
			$bdata     = Common::_traebandata($banco);
			$tbanco    = $bdata['tbanco'];
			$mndebito  = ($tipo_op == 'ND' && $tbanco != 'CAJ')? $this->datasis->prox_sql('ndebito',8) : '';
			$mncausado = '';
		}else{
			$mnroegre  = '';
			$mndebito  = '';
			$mncausado = $this->datasis->prox_sql('ncausado',8);
		}

		if($tipo_doc == 'AB'){
			$xnumero = $this->datasis->prox_sql('num_ab',8);
		}elseif($tipo_doc == 'AN'){
			$xnumero = $this->datasis->prox_sql('num_an',8);
		}else{
			$xnumero = substr($do->get('serie'),-8);
		}
		$do->set('numero',$xnumero);

		$row=$this->datasis->damerow("SELECT nombre FROM sprv WHERE proveed=".$this->db->escape($cod_prv));
		if(!empty($row)){
			$do->set('nombre' ,$row['nombre']);
		}

		$rel='itppro';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$itabono  = floatval($do->get_rel($rel, 'abono', $i));
			if(empty($itabono) || $itabono==0){
				$do->rel_rm($rel,$i);
			}else{
				$ittipo    = $do->get_rel($rel, 'tipo_doc', $i);
				$itnumero  = $do->get_rel($rel, 'numero'  , $i);
				$itimpuesto= $arr_itimpuestos[$i];
				if(!empty($impuesto)){
					$rriva = $reteiva*$itimpuesto/$impuesto;
				}else{
					$rriva = 0;
				}

				$do->set_rel($rel, 'tipoppro', $tipo_doc, $i);
				$do->set_rel($rel, 'cod_prv' , $cod_prv , $i);
				$do->set_rel($rel, 'estampa' , $estampa , $i);
				$do->set_rel($rel, 'hora'    , $hora    , $i);
				$do->set_rel($rel, 'usuario' , $usuario , $i);
				$do->set_rel($rel, 'transac' , $transac , $i);
				$do->set_rel($rel, 'mora'    , 0        , $i);
				$do->set_rel($rel, 'reteiva' , $rriva   , $i);
				$do->set_rel($rel, 'reten'   , 0, $i);
				$do->set_rel($rel, 'cambio'  , 0, $i);
				//$do->set_rel($rel, 'reteiva' , 0, $i);
			}
		}

		$observa=$do->get('observa1');
		$do->set('observa1',substr($observa,0 ,50));
		$obs2 =  substr($observa,50,50);
		if($obs2!==false){
			$do->set('observa2',$obs2);
		}

		$do->set('vence'   , $fecha);
		$do->set('causado' , $mncausado);
		$do->set('negreso' , $mnroegre);
		$do->set('ndebito' , $mndebito);
		$do->set('monto'   , $totalab-$ppago);
		$do->set('impuesto', $impuesto);
		$do->set('reten'   , 0);
		$do->set('ppago'   , $ppago);
		$do->set('control' , $mcontrol);
		$do->set('cambio'  , 0 );
		if($tipo_doc=='NC'){
			$serie = $do->get('serie');
			$do->set('serie',substr($serie,-8));
		}else{
			$do->set('nfiscal' , '');
		}
		$do->set('mora'    , 0 );
		$do->set('comprob' , '');
		if($tipo_doc=='AB' || $tipo_doc=='NC'){
			$do->set('abonos'  , $totalab-$ppago);
			$do->set('fecapl',$fecha);
			if($tipo_doc=='NC'){
				$do->set('fecdoc',$itfecha);
			}else{
				$do->set('fecdoc',$fecha);
			}
		}else{
			$do->set('abonos'  , 0);
		}
		$do->set('transac', $transac);
		if($tipo_doc!='NC'){
			$do->set('numche' , str_pad($do->get('numche'), 12,'0', STR_PAD_LEFT));
		}
		if(!empty($codigo)){
			$dbcodigo = $this->db->escape($codigo);
			$do->set('descrip' ,$this->datasis->dameval("SELECT TRIM(nombre) AS val FROM botr WHERE codigo=${dbcodigo}"));
		}

		return true;
	}

	function _pre_pprv_update($do){
		return false;
	}

	function _pre_pprv_delete($do){
		return false;
	}

	function _post_pprv_insert($do){
		$cod_prv = $do->get('cod_prv');
		$numero  = $do->get('numero');
		$tipo_doc= $do->get('tipo_doc');
		$ppago   = $do->get('ppago');
		$fecha   = $do->get('fecha');
		$mnumero = $do->get('numero');
		$nombre  = $do->get('nombre');
		$estampa = $do->get('estampa');
		$usuario = $do->get('usuario');
		$hora    = $do->get('hora');
		$transac = $do->get('transac');
		$tipo_op = $do->get('tipo_op');
		$banco   = $do->get('banco');
		$benefi  = $do->get('benefi');
		$totalab = $do->get('monto');
		$impuesto= $do->get('impuesto');
		$numche  = $do->get('numche');
		$observa1= $do->get('observa1');
		$observa2= $do->get('observa2');
		$mnroegre= $do->get('negreso');
		$mndebito= $do->get('ndebito');
		$posdata = $do->get('posdata');
		$reteiva = $do->get('reteiva');
		$nfiscal = $do->get('nfiscal');

		$do->set('vence',$fecha);

		//Aplica el abono
		$ppobserva=array();
		$rel='itppro';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$itabono  = floatval($do->get_rel($rel, 'abono'   , $i));
			$ittipo   = $do->get_rel($rel, 'tipo_doc', $i);
			$itnumero = $do->get_rel($rel, 'numero'  , $i);
			$itfecha  = $do->get_rel($rel, 'fecha'   , $i);
			$itpppago = $do->get_rel($rel, 'ppago'   , $i);
			if($itpppago>0){
				$ppobserva[] = $ittipo.$itnumero;
			}
			$dbittipo   = $this->db->escape($ittipo  );
			$dbitnumero = $this->db->escape($itnumero);
			$dbcod_prv  = $this->db->escape($cod_prv );
			$dbitfecha  = $this->db->escape($itfecha );

			$mSQL = "UPDATE sprm SET abonos=abonos+${itabono}, preabono=0, preppago=0
			WHERE tipo_doc=${dbittipo} AND numero=${dbitnumero} AND cod_prv=${dbcod_prv}";
			$this->db->query($mSQL);
		}

		//Crea Movimiento en Bancos
		if($tipo_doc!='NC'){

			$bdata = Common::_traebandata($banco);

			$data = array();
			$data['codbanc']  = $banco;
			$data['numcuent'] = trim($bdata['numcuent']);
			$data['banco']    = trim($bdata['banco']);
			$data['saldo']    = $bdata['saldo']-$totalab;
			$data['fecha']    = $fecha;
			$data['tipo_op']  = $tipo_op;
			$data['numero']   = $numche;
			$data['concepto'] = $observa1;
			$data['concep2']  = $observa2;
			$data['monto']    = $totalab;
			$data['clipro']   = 'P' ;
			$data['codcp']    = $cod_prv;
			$data['nombre']   = $nombre;
			$data['benefi']   = $benefi;
			$data['posdata']  = $posdata;
			$data['negreso']  = $mnroegre;
			$data['ndebito']  = $mndebito;
			$data['usuario']  = $usuario;
			$data['estampa']  = $estampa;
			$data['hora']     = $hora;
			$data['transac']  = $transac;
			$this->db->insert('bmov',$data);
			$this->datasis->actusal($banco, $fecha, (-1)*$totalab);
		}

		// Si tiene pronto pago genera la NC
		if($ppago > 0){
			$mnumero   = $this->datasis->prox_sql('num_nc',8);
			$mcontrol  = $this->datasis->prox_sql('nsprm' ,8);

			$data = array();
			$data['tipo_doc'] = 'NC';
			$data['numero']   = $mnumero;
			$data['cod_prv']  = $cod_prv;
			$data['nombre']   = $nombre;
			$data['fecha']    = $fecha;
			$data['monto']    = $ppago;
			$data['impuesto'] = round($this->ppimpuesto,2) ;
			$data['vence']    = $fecha;
			$data['observa1'] = 'DESC. P.PAGO A '.$tipo_doc.$numero; //implode(',',$ppobserva);
			$data['codigo']   = 'DESPP';
			$data['descrip']  = 'DESCUENTO PRONTO PAGO';
			$data['abonos']   = $ppago;
			$data['control']  = $mcontrol;
			$data['usuario']  = $usuario;
			$data['estampa']  = $estampa;
			$data['hora']     = $hora;
			$data['transac']  = $transac;

			$this->db->insert('sprm',$data);
		}

		if($tipo_doc=='NC' && $reteiva>0 ){
			$montasa   = $do->get('montasa'  );
			$monredu   = $do->get('monredu'  );
			$monadic   = $do->get('monadic'  );
			$tasa      = $do->get('tasa'     );
			$reducida  = $do->get('reducida' );
			$sobretasa = $do->get('sobretasa');
			$exento    = $do->get('exento'   );
			$mcontrol  = $this->datasis->prox_sql('nsprm' ,8);

			//Crea la nota de debito
			$mnumnd = $this->datasis->fprox_numero('num_nd');
			$sprm=array();
			$sprm['cod_prv']    = $cod_prv;
			$sprm['nombre']     = $nombre;
			$sprm['tipo_doc']   = 'ND';
			$sprm['numero']     = $mnumnd;
			$sprm['fecha']      = $fecha;
			$sprm['monto']      = $reteiva;
			$sprm['impuesto']   = 0;
			$sprm['abonos']     = $reteiva;
			$sprm['vence']      = $fecha;
			$sprm['tipo_ref']   = $tipo_doc;
			$sprm['num_ref']    = $mnumero;
			$sprm['observa1']   = 'RET/IVA A '.$tipo_doc.$mnumero;
			$sprm['estampa']    = $estampa;
			$sprm['hora']       = $hora;
			$sprm['transac']    = $transac;
			$sprm['usuario']    = $usuario;
			$sprm['control']    = $mcontrol;
			$sprm['codigo']     = '';
			$sprm['descrip']    = '';
			$mSQL = $this->db->insert_string('sprm', $sprm);
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ memowrite($mSQL,'sprm'); $error++; }

			//Abona la Factura
			$mSQL = 'UPDATE sprm SET abonos=abonos+'.$reteiva.' WHERE cod_prv="'.$cod_prv.'" AND tipo_doc="'.$tipo_doc.'" AND numero="'.$mnumero.'"';
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ memowrite($mSQL,'scst'); $error++;}

			$cana = $do->count_rel($rel);
			for($i = 0;$i < $cana;$i++){
				$itabono  = floatval($do->get_rel($rel, 'abono'   , $i));
				$ittipo   = $do->get_rel($rel, 'tipo_doc', $i);
				$itnumero = $do->get_rel($rel, 'numero'  , $i);
				$itfecha  = $do->get_rel($rel, 'fecha'   , $i);
				$abono    = $do->get_rel($rel, 'reteiva'   , $i);

				//$itpppago = $do->get_rel($rel, 'ppago'   , $i);

				$dbittipo   = $this->db->escape($ittipo  );
				$dbitnumero = $this->db->escape($itnumero);
				$dbitfecha  = $this->db->escape($itfecha );

				$sprm=array();
				$sprm['numppro']    = $mnumnd;
				$sprm['tipoppro']   = 'ND';
				$sprm['cod_prv']    = $cod_prv;
				$sprm['tipo_doc']   = $ittipo;
				$sprm['numero']     = $itnumero;
				$sprm['fecha']      = $fecha;
				$sprm['monto']      = $reteiva;
				$sprm['abono']      = $abono;

				$sprm['estampa']    = $estampa;
				$sprm['hora']       = $hora;
				$sprm['transac']    = $transac;
				$sprm['usuario']    = $usuario;
				$mSQL = $this->db->insert_string('itppro', $sprm);

				$ban=$this->db->query($mSQL);
				if(!$ban){ memowrite($mSQL,'sprm'); $error++; }

				//$mSQL = "UPDATE sprm SET abonos=abonos+${itabono}, preabono=0, preppago=0
				//WHERE tipo_doc=${dbittipo} AND numero=${dbitnumero} AND cod_prv=${dbcod_prv}";
				//$this->db->query($mSQL);
		}

/*
			$sprm=array();
			$sprm['numppro']    = $mnumnd;
			$sprm['tipoppro']   = 'ND';
			$sprm['cod_prv']    = $cod_prv;
			$sprm['tipo_doc']   = $tipo_doc;
			$sprm['numero']     = $mnumero;
			$sprm['fecha']      = $fecha;
			$sprm['monto']      = $reteiva;
			$sprm['abono']      = $reteiva;

			$sprm['estampa']    = $estampa;
			$sprm['hora']       = $hora;
			$sprm['transac']    = $transac;
			$sprm['usuario']    = $usuario;
			$mSQL = $this->db->insert_string('itppro', $sprm);

			$ban=$this->db->query($mSQL);
			if(!$ban){ memowrite($mSQL,'sprm'); $error++; }
*/


			//Crea la nota de credito
			$mnumnc  = $this->datasis->fprox_numero('num_nc');
			$mcontrol= $this->datasis->prox_sql('nsprm' ,8);
			$sprm=array();
			$sprm['cod_prv']   = 'REIVA';
			$sprm['nombre']    = 'RETENCION DE I.V.A. POR COMPENSAR';
			$sprm['tipo_doc']  = 'NC';
			$sprm['numero']    = $mnumnc;
			$sprm['fecha']     = $fecha;
			$sprm['monto']     = $reteiva;
			$sprm['impuesto']  = 0;
			$sprm['abonos']    = 0;
			$sprm['vence']     = $fecha;
			$sprm['tipo_ref']  = '';
			$sprm['num_ref']   = '';
			$sprm['observa1']  = 'RET/IVA A '.$tipo_doc.$mnumero;
			$sprm['estampa']   = $estampa;
			$sprm['hora']      = $hora;
			$sprm['transac']   = $transac;
			$sprm['usuario']   = $usuario;
			$sprm['control']   = $mcontrol;
			$sprm['codigo']    = 'NOCON';
			$sprm['descrip']   = 'NOTA DE CONTABILIDAD';
			$mSQL = $this->db->insert_string('sprm', $sprm);
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ memowrite($mSQL,'scst'); $error++;}

			//Crea la retencion
			$niva    = $this->datasis->fprox_numero('niva');

			$riva['nrocomp']    = $niva;
			$riva['emision']    = $fecha;
			$riva['periodo']    = substr($fecha,0,6) ;
			$riva['tipo_doc']   = $tipo_doc;
			$riva['fecha']      = $fecha;
			$riva['numero']     = $numero;
			$riva['nfiscal']    = $nfiscal;
			$riva['afecta']     = $itnumero;
			$riva['clipro']     = $cod_prv;
			$riva['nombre']     = $nombre;
			$riva['rif']        = $this->datasis->dameval('SELECT rif FROM sprv WHERE proveed='.$this->db->escape($cod_prv));
			$riva['exento']     = $exento;
			$riva['tasa']       = $this->input->post('ptasa');
			$riva['tasaadic']   = $this->input->post('padicional');
			$riva['tasaredu']   = $this->input->post('preducida');
			$riva['general']    = $montasa;
			$riva['geneimpu']   = $tasa ;
			$riva['adicional']  = $monadic;
			$riva['adicimpu']   = $sobretasa;
			$riva['reducida']   = $monredu;
			$riva['reduimpu']   = $reducida;
			$riva['stotal']     = $totalab-$impuesto;
			$riva['impuesto']   = $impuesto;
			$riva['gtotal']     = $totalab;
			$riva['reiva']      = $reteiva;
			$riva['transac']    = $transac;
			$riva['estampa']    = $estampa;
			$riva['hora']       = $hora;
			$riva['usuario']    = $usuario;
			$mSQL=$this->db->insert_string('riva', $riva);
			$ban =$this->db->simple_query($mSQL);
			if(!$ban){ memowrite($mSQL,'scst'); $error++; }

		}//Fin de la retencion

		logusu('PPRO',"Abono a proveedor CREADO Prov=${cod_prv}  Numero=${numero}");
	}

	//**********************************************
	// Fin de pago a proveedor
	//***********************************************
	//Obliga el campo segun el tipo
	function chobligatipo($val,$tipo){
		$tipo_doc = $this->input->post('tipo_doc');
		if($tipo_doc==$tipo && $val==''){
			$this->validation->set_message('chobligatipo', "El campo %s es necesario cuando el tipo es ${tipo}");
			return false;
		}
		return true;
	}

	//Chequea los campos de numero y fecha en las formas de pago
	//cuando deban corresponder
	function chtipo($val){
		$tipo_doc = $this->input->post('tipo_doc');
		if($tipo_doc=='NC') return true;

		$tipo=$this->input->post('tipo_op');
		if(empty($tipo)) return true;
		$this->validation->set_message('chtipo', 'El campo %s es obligatorio');

		if(empty($val) && ($tipo=='CH'))
			return false;
		else
			return true;
	}

	function instalar(){
		$campos=$this->db->list_fields('sprm');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE sprm DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE sprm ADD UNIQUE INDEX `unico` (`cod_prv`, `tipo_doc`, `numero`)');
			$this->db->simple_query('ALTER TABLE sprm ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('tbanco',$campos)){
			$this->db->simple_query('ALTER TABLE sprm ADD COLUMN `tbanco` CHAR(3)');
		}

		$itcampos=$this->db->list_fields('itppro');
		if(!in_array('id',$itcampos)){
			$mSQL="ALTER TABLE `itppro` ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `reteiva`, ADD PRIMARY KEY (`id`)";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('modificado',$itcampos)){
			$mSQL="ALTER TABLE `itppro` ADD COLUMN `modificado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `id`, ADD INDEX `modificado` (`modificado`)";
			$this->db->simple_query($mSQL);
		}
	}

/*
DEVOLUCIONES
========================================================================
Al hacer una devolucion si la factura a aplicar esta pendiente cargar automaticamente
La retencion debe advertir si tiene 15 dias colocarla por defecto sino colocar 0

Cuando reversa la devolucion se joden los saldos de la FC
En itppro no borra la transaccion reversada

NOTA DE CREDITO
========================================================================
La ND por dev de reiva no la carga al saldo


*/

}
