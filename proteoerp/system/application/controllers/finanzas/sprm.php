<?php 
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
		$grid->wbotonadd(array('id'=>'imprime',   'img'=>'assets/default/images/print.png', 'alt' => 'Imprimir',      'label'=>'Reimprimir Documento', 'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'princheque','img'=>'images/check.png',                'alt' => 'Emitir Cheque', 'label'=>'Imprimir cheque',      'tema'=>'anexos'));
		$grid->wbotonadd(array("id"=>"abonos",   "img"=>"images/check.png",     "alt" => 'Abonos',          "label"=>"Pago o Abono"));
		$WestPanel = $grid->deploywestp();


		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fabono',  'title'=>'Abonar a Proveedor')
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
		jQuery("#princheque").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				if(ret.tipo_op=="CH"){
					window.open(\''.site_url($this->url.'/impcheque').'/\'+id, \'_blank\', \'width=300,height=400,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-200), screeny=((screen.availWidth/2)-150)\');
				}else{
					$.prompt("<h1>El efecto seleccionado no possee cheques</h1>");
				}
			} else {
				$.prompt("<h1>Por favor Seleccione una Egreso</h1>");
			}
		});';

		$bodyscript .= '
		jQuery("#imprime").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url($this->url.'sprmprint').'/\'+id, \'_blank\', \'width=300,height=300,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});';

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



		//Abonos
		$bodyscript .= '
			$( "#abonos" ).click(function() {
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

			$( "#fabono" ).dialog({
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
										'.$this->datasis->jwinopen(site_url('formatos/ver/PPROABC').'/\'+res.id').';
										$( "#fabono" ).dialog( "close" );
										return [true, a ];
									} else {
										apprise("<div style=\"font-size:16px;font-weight:bold;background:red;color:white\">Error:</div> <h1>"+res.mensaje+"</h1>");
									}
								}
							});
						}
					},
					Cancel: function() { $( this ).dialog( "close" ); }
				},
				close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
			});';



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
									'.$this->datasis->jwinopen(site_url('formatos/ver/SPRM').'/\'+res.id+\'/id\'').';
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




	//******************************************************************
	//Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

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
		$grid->label('Numero Completo');
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
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
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
			return "Registro Agregado";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('sprm', $data);
			return "Registro $id Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sprm WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				//$this->db->simple_query("DELETE FROM sprm WHERE id=$id ");
				//logusu('sprm',"Registro ????? ELIMINADO");
				echo "Registro no Eliminado";
			}
		};
	}


	function tabla() {
		$id = $this->uri->segment($this->uri->total_segments());

		$row = $this->datasis->damereg("SELECT cod_prv, tipo_doc, numero, estampa, transac FROM sprm WHERE id=$id");

		$transac  = $row['transac'];
		$cod_prv  = $row['cod_prv'];
		$numero   = $row['numero'];
		$tipo_doc = $row['tipo_doc'];
		$estampa  = $row['estampa'];
		$salida   = '';

		if (!empty($transac)){
			$td1  = "<td style='border-style:solid;border-width:1px;border-color:#78FFFF;' valign='top' align='center'>\n";
			$td1 .= "<table width='98%'>\n<caption style='background-color:#5E352B;color:#FFFFFF;font-style:bold'>";

			// Movimientos Relacionados en Proveedores SPRM
			$mSQL = "SELECT cod_prv, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
				FROM sprm WHERE transac='$transac' AND id<>$id ORDER BY cod_prv ";
			$query = $this->db->query($mSQL);
			$salida = '<table width="100%"><tr>';
			$saldo  = 0;
			if ( $query->num_rows() > 0 ){
				$salida .= $td1;
				$salida .= "Movimiento en Proveedores</caption>";
				$salida .= "<tr bgcolor='#E7E3E7'><td>Nombre</td><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row)
				{
					if ( $row['tipo_doc'] == 'FC' ) {
						$saldo = $row['monto']-$row['abonos'];
					}
					$salida .= "<tr>";
					$salida .= "<td>".$row['cod_prv'].'-'.$row['nombre']."</td>";
					$salida .= "<td>".$row['tipo_doc']."</td>";
					$salida .= "<td>".$row['numero'].  "</td>";
					$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
					$salida .= "</tr>";
				}
				if ($saldo <> 0)
					$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo). "</td></tr>";
				$salida .= "</table></td>";
			}

			// Movimientos Relacionados en SMOV
			$mSQL = "SELECT cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
				FROM smov WHERE transac='$transac' ORDER BY cod_cli ";
			$query = $this->db->query($mSQL);
			$saldo = 0;
			if ( $query->num_rows() > 0 ){
				$salida .= $td1;
				$salida .= "Movimiento en Clientes</caption>";
				$salida .= "<tr bgcolor='#e7e3e7'><td>Nombre</td><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row)
				{
					if ( $row['tipo_doc'] == 'FC' ) {
						$saldo = $row['monto']-$row['abonos'];
					}
					$salida .= "<tr>";
					$salida .= "<td>".$row['cod_cli'].'-'.$row['nombre']."</td>";
					$salida .= "<td>".$row['tipo_doc']."</td>";
					$salida .= "<td>".$row['numero'].  "</td>";
					$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
					$salida .= "</tr>";
				}
				$salida .= "</table></td>";
			}

			//Retencion de IVA RIVA
			$mSQL = "
				SELECT periodo, nrocomp, reiva FROM riva WHERE tipo_doc='$tipo_doc' AND numero='$numero' AND MID(transac,1,1)<>'_'";
				"UNION ALL
				SELECT periodo, nrocomp, reiva FROM riva WHERE transac='$transac' AND MID(transac,1,1)<>'_'
				";
			$query = $this->db->query($mSQL);
			if ( $query->num_rows() > 0 ){
				$salida .= $td1;
				$salida .= "Retenciones de IVA</caption>";
				$salida .= "<tr bgcolor='#e7e3e7'><td>Periodo</td><td align='center'>Numero</td><td align='center'>Monto</tr>";
				foreach ($query->result_array() as $row)
				{
					$salida .= "<tr>";
					$salida .= "<td>".$row['periodo']."</td>";
					$salida .= "<td>".$row['nrocomp'].  "</td>";
					$salida .= "<td align='right'>".nformat($row['reiva']).   "</td>";
					$salida .= "</tr>";
				}
				$salida .= "</table></td>";
			}


			//if ( $tipo_doc <> 'FC' ){
				$mSQL = "
					SELECT tipo_doc, numero, montonet FROM scst a WHERE a.transac='$transac'
					UNION ALL
					SELECT tipo_doc, numero, totneto  FROM gser a WHERE a.transac='$transac'
					";
				$query = $this->db->query($mSQL);
				if ( $query->num_rows() > 0 ){
					$salida .= $td1;
					$salida .= "Gasto/Compra</caption>";
					$salida .= "<tr bgcolor='#e7e3e7'><td>Tipo</td><td align='center'>Numero</td><td align='center'>Monto</tr>";
					foreach ($query->result_array() as $row)
					{
						$salida .= "<tr>";
						$salida .= "<td>".$row['tipo_doc']."</td>";
						$salida .= "<td>".$row['numero'].  "</td>";
						$salida .= "<td align='right'>".nformat($row['montonet']).   "</td>";
						$salida .= "</tr>";
					}
					$salida .= "</table></td>";
				}
			//}

			// Movimientos Relacionados ITPPRO
			$mSQL = "SELECT tipo_doc, numero, monto, abono FROM itppro WHERE transac='$transac' ";
			$query = $this->db->query($mSQL);
			if ( $query->num_rows() == 0 ){
				$mSQL = "SELECT tipoppro tipo_doc, numppro numero, monto, abono FROM itppro WHERE tipo_doc='$tipo_doc' AND numero='$numero'";
				$query = $this->db->query($mSQL);
			}

			if ( $query->num_rows() > 0 ){
				$saldo = 0;
				$salida .= $td1;
				$salida .= "Movimientos Relacionados</caption>";
				$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td><td align='center'>Abono</td></tr>";
				foreach ($query->result_array() as $row)
				{
					$saldo += $row['abono'];
					$salida .= "<tr>";
					$salida .= "<td>".$row['tipo_doc']."</td>";
					$salida .= "<td>".$row['numero'].  "</td>";
					$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
					$salida .= "<td align='right'>".nformat($row['abono']).   "</td>";
					$salida .= "</tr>";
				}
				$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'><b>Saldo : ".nformat($saldo). "</b></td></tr>";
				$salida .= "</table></td>";
			}

			// Movimiento en Caja/Bancos
			$mSQL = "SELECT codbanc, tipo_op, numero, monto FROM bmov WHERE transac='$transac' AND monto<>0";
			$query = $this->db->query($mSQL);
			$saldo = 0;
			if ( $query->num_rows() > 0 ){
				$salida .= $td1;
				$salida .= "Movimiento en Caja y/o Bancos</caption>";
				$salida .= "<tr bgcolor='#e7e3e7'><td>Bco</td><td>Tipo</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row)
				{
					$salida .= "<tr>";
					$salida .= "<td>".$row['codbanc']. "</td>";
					$salida .= "<td>".$row['tipo_op']."</td>";
					$salida .= "<td>".$row['numero']."</td>";
					$salida .= "<td align='right'>".nformat($row['monto'])."</td>";
					$salida .= "</tr>";
				}
				$salida .= "</table></td>";
			}

			// Prestamos PRMO
			$mSQL = "SELECT tipop, codban, if(observa2='',observa1,observa2) observa, monto FROM prmo WHERE transac='$transac' AND clipro='$cod_prv' AND monto<>0";
			$query = $this->db->query($mSQL);
			$saldo = 0;
			if ( $query->num_rows() > 0 ){
				$salida .= $td1;
				$salida .= "Prestamos</caption>";
				$salida .= "<tr bgcolor='#e7e3e7'><td></td><td>Bco</td><td>Observacion</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row)
				{
					$salida .= "<tr>";
					$salida .= "<td>".$row['tipop']."</td>";
					$salida .= "<td>".$row['codban']."</td>";
					$salida .= "<td>".$row['observa']."</td>";
					$salida .= "<td align='right'>".nformat($row['monto'])."</td>";
					$salida .= "</tr>";
				}
				$salida .= "</table></td>";
			}

			//Cruce de Cuentas
			$mSQL = "
				SELECT b.tipo tipo, b.proveed codcp, MID(b.nombre,1,25) nombre, a.onumero, a.monto, b.numero, b.fecha
				FROM itcruc AS a JOIN cruc AS b ON a.numero=b.numero
				WHERE b.proveed='$cod_prv' AND b.transac='$transac' AND a.onumero!='$tipo_doc$numero'
				UNION ALL
				SELECT b.tipo tipo, b.cliente codcp, MID(b.nomcli,1,25) nombre, a.onumero, a.monto, b.numero, b.fecha
				FROM itcruc AS a JOIN cruc AS b ON a.numero=b.numero
				WHERE b.cliente='$cod_prv' AND b.transac='$transac' ORDER BY onumero
				";

			$query = $this->db->query($mSQL);
			$saldo = 0;
			if ( $query->num_rows() > 0 ){
				$salida .= $td1;
				$salida .= "Cruce de Cuentas</caption>";
				$salida .= "<tr bgcolor='#e7e3e7'><td>Nombre</td><td>Codigo</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
				foreach ($query->result_array() as $row)
				{
					$salida .= "<tr>";
					$salida .= "<td>(".$row['tipo'].') '.$row['nombre']."</td>";
					$salida .= "<td>".$row['codcp']."</td>";
					$salida .= "<td>".$row['onumero'].  "</td>";
					$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
					$salida .= "</tr>";
				}
				$salida .= "</table></td>";
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
	}
}
