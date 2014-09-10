<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Smov extends Controller {
	var $mModulo='SMOV';
	var $titp='Movimiento de Clientes';
	var $tits='Movimiento de Clientes';
	var $url ='finanzas/smov/';

	function Smov(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_id('525',1);
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'525','titulo'=>'Movimiento de Clientes','mensaje'=>'Movimiento de Clientes','panel'=>'CLIENTES','ejecutar'=>'finanzas/smov','target'=>'popu','visible'=>'S','pertenece'=>'5','ancho'=>900,'alto'=>600));
		$this->instalar();
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

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'bimpri', 'img'=>'assets/default/images/print.png', 'alt' => 'Formato PDF'    , 'label'=>'Reimprimir Documento'));
		$grid->wbotonadd(array('id'=>'cobro' , 'img'=>'images/dinero.png' , 'alt' => 'Cobro a cliente', 'label'=>'Cobro a Cliente'     ));
		//$grid->wbotonadd(array('id'=>'nccob' , 'img'=>'images/check.png'  , 'alt' => 'Nota de credito a factura pagada', 'label'=>'NC a Factura Cobrada'));
		$WestPanel = $grid->deploywestp();

		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'] );

		$adic = array(
			array('id'=>'fedita'  , 'title'=>'Agregar Registro'),
			array('id'=>'fsclisel', 'title'=>'Seleccionar cliente'),
			array('id'=>'fborra'  , 'title'=>'Eliminar Registro')
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
		$param['listados']     = $this->datasis->listados('SMOV', 'JQ');
		$param['otros']        = $this->datasis->otros('SMOV', 'JQ');
		$param['centerpanel']  = $centerpanel;
		$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$this->load->view('jqgrid/crud2',$param);

	}

	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0 ){

		$bodyscript  = '<script type="text/javascript">';

		$bodyscript .= '
		function tconsulta(transac){
			if (transac)	{
				window.open(\''.site_url('contabilidad/casi/localizador/transac/procesar').'/\'+transac, \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Transaccion invalida</h1>");
			}
		};';

		$bodyscript .= '
		function smovdel() {
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
							$("#fborra").dialog( "open" );
						}
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function selscli() {
			$.post("'.site_url($this->url.'selscli/').'/"+id,
				function(data){
					$("#fsclisel").html(data);
					$("#fsclisel").dialog( "open" );
				}
			);
		};';

		$bodyscript .= '$(function() { ';

		$bodyscript .= '
		jQuery("#bimpri").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url($this->url.'smovprint').'/\'+id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			}else{
				$.prompt("<h1>Por favor Seleccione un Movimiento</h1>");
			}
		});
		';

		$bodyscript .= '
		jQuery("#cobro").click( function(){
			$.post("'.site_url($this->url.'selscli/').'",
				function(data){
					$("#fsclisel").html(data);
					$("#fsclisel").dialog("open");
					var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
					if(id){
						var ret    = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
						var cod_cli= ret.cod_cli;
						$("#cod_cli").val(cod_cli);
						$("#cod_cli").focus();
						$("#cod_cli").autocomplete("search", cod_cli);
					}
				}
			);
		});';

		//$bodyscript .= '
		//jQuery("#nccob").click( function(){
		//	var id  = $("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
		//	var ret = $("#newapi'.$grid0.'").getRowData(id);
        //
		//	$.post("'.site_url($this->url.'ncfac').'/create",
		//		function(data){
		//			$("#fedita").html(data);
		//			$("#fedita").dialog({ height: 300, width: 500 });
		//			$("#fedita").dialog("open");
		//		}
		//	);
		//});';

		$bodyscript .= '
			$("#fedita").dialog({
				autoOpen: false, height: 500, width: 800, modal: true,
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
										$("#fedita").dialog("close");
										jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
										window.open(\''.site_url($this->url.'smovprint').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
										return true;
									}else{
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
				close: function(){
					$("#fedita").html("");
				}
			});';

		$bodyscript .= '
			$("#fsclisel").dialog({
				autoOpen: false, height: 430, width: 540, modal: true,
				buttons: {
					"Seleccionar": function() {
						var id_scli=$("#id_scli").val();
						if(id_scli){
							$.get("'.site_url($this->url.'ccli').'"+"/"+$("#id_scli").val()+"/create", function(data) {
								$("#fedita").html(data);
								$("#fedita").dialog("open");
								$("#fsclisel").html("");
								$("#fsclisel").dialog("close");
							});
						}else{
							apprise("<b>Debe seleccionar un cliente primero.</b>");
						}
					},
					Cancel: function() {
						$("#fcobroser").html("");
						$("#fsclisel").dialog( "close" );
					}
				},
				close: function() {
					$("#fsclisel").html("");
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
	//
	//Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i = 1;

		$grid  = new $this->jqdatagrid;

		$grid->addField('cod_cli');
		$grid->label('Cliente');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 60
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'       => 'true',
			'editable'     => 'false',
			'width'        => 200,
			'edittype'     => "'text'",
		));


		$grid->addField('tipo_doc');
		$grid->label('Tipo');
		$grid->params(array(
			'align'        => "'center'",
			'search'       => 'true',
			'editable'     => 'false',
			'width'        => 40,
			'edittype'     => "'text'",
		));


		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'align'        => "'center'",
			'search'       => 'true',
			'editable'     => 'false',
			'width'        => 70,
			'edittype'     => "'text'",
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
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
			'editable'      => 'false',
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
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('abonos');
		$grid->label('Abonos');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 80,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('vence');
		$grid->label('Vence');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('tipo_ref');
		$grid->label('Tipo/Ref');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('num_ref');
		$grid->label('Num.Ref');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'edittype'      => "'text'",
		));


		$grid->addField('observa1');
		$grid->label('Observaci&oacute;n 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 200,
			'edittype'      => "'text'",
		));


		$grid->addField('observa2');
		$grid->label('Observaci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 200,
			'edittype'      => "'text'",
		));

		$grid->addField('banco');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('tipo_op');
		$grid->label('Tipo.Op');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('fecha_op');
		$grid->label('Fecha.Op');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('num_op');
		$grid->label('Num.Op');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 120,
			'edittype'      => "'text'",
		));


		$grid->addField('ppago');
		$grid->label('P.Pago');
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


		$grid->addField('reten');
		$grid->label('R.ISLR');
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


		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 60,
			'edittype'      => "'text'",
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 200,
			'edittype'      => "'text'",
		));


		$grid->addField('control');
		$grid->label('Control');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'edittype'      => "'text'",
		));


		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));


		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 60,
			'edittype'      => "'text'",
		));


		$grid->addField('transac');
		$grid->label('Transac&iacute;on');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
			'edittype'      => "'text'",
			'formatter'     => 'ltransac'
		));


		$grid->addField('origen');
		$grid->label('Or&iacute;gen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('cambio');
		$grid->label('Cambio');
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


		$grid->addField('mora');
		$grid->label('I.Mora');
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


		$grid->addField('reteiva');
		$grid->label('R.IVA');
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


		$grid->addField('vendedor');
		$grid->label('Vendedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 50,
			'edittype'      => "'text'",
		));


		$grid->addField('nfiscal');
		$grid->label('N.Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 80,
			'edittype'      => "'text'",
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


		$grid->addField('fecdoc');
		$grid->label('F.Documento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('nroriva');
		$grid->label('N.Ret.IVA');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 120,
			'edittype'      => "'text'",
		));


		$grid->addField('emiriva');
		$grid->label('Emision R.IVA');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('codcp');
		$grid->label('Cod.CP');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 50,
			'edittype'      => "'text'",
		));


		$grid->addField('depto');
		$grid->label('Depto.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('maqfiscal');
		$grid->label('Maq.Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 100,
			'edittype'      => "'text'",
		));


		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 130,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('ningreso');
		$grid->label('N.Ingreso');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));


		$grid->addField('ncredito');
		$grid->label('N.Cr&eacute;dito');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 70,
			'edittype'      => "'text'",
		));

		/*$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 50,
			'editable' => 'false',
			'search'   => 'false'
		));*/


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('240');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		//$grid->setDelete(true);
		$grid->setDelete( $this->datasis->sidapuede('SMOV','BORR_REG%'));
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');
		$grid->setBarOptions('delfunc: smovdel');

		$grid->setonSelectRow('
			function(id){
				$.ajax({
					url: "'.base_url().$this->url.'tabla/"+id,
					success: function(msg){
						$("#radicional").html(msg);
					}
				});
			}
		');

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
		$grid  = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('smov');
		//if ( !empty($mWHERE)) print_r($mWHERE);

		$response   = $grid->getData('smov', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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
		$dbid   = $this->db->escape($id);
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			echo '';
			return '';
		}elseif($oper == 'edit') {
			//unset($data['ubica']);
			$monto =  $this->datasis->dameval("SELECT monto FROM smov WHERE id=${dbid}");
			$data['abonos'] = abs($data['abonos']);
			if($data['abonos'] > $monto ) $data['abonos'] = $monto;
			$this->db->where('id', $id);
			$this->db->update('smov', $data);
			return 'Movimiento Modificado';
		}elseif($oper == 'del'){
			return 'Deshabilidado';
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('', 'smov');
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
		$cod_cli    = $do->get('cod_cli');
		$numero     = $do->get('numero');
		$reteiva    = $do->get('reteiva');
		$abonos     = floatval($do->get('abonos'));

		$dbid       = $this->db->escape($id);
		$dbtransac  = $this->db->escape($transac);
		$dbtipo_doc = $this->db->escape($tipo_doc);
		$dbcod_cli  = $this->db->escape($cod_cli);
		$dbnumero   = $this->db->escape($numero);
		$dbfecha    = $this->db->escape($do->get('fecha'));

		if(empty($transac)){ return true; }

		$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM casi WHERE comprob=${dbtransac}"));
		if($cana>0){
			$do->error_message_ar['pre_del']='El efecto ya esta en contabilidad, no puede ser modificado ni eliminado.';
			return false;
		}

		$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM sfac WHERE transac=${dbtransac}"));
		if($cana>0){
			$do->error_message_ar['pre_del']='Movimiento originado en facturacion, debe eliminarlo por el modulo respectivo.';
			return false;
		}

		/*if($tipo_doc=='FC'){
			$do->error_message_ar['pre_del']='Las facturas solo se pueden anular desde el modulo de facturacion.';
			return false;
		}*/

		if($tipo_doc=='GI'){
			$do->error_message_ar['pre_del']='Los giros no se pueden anular desde este modulo .';
			return false;
		}

		if($tipo_doc=='ND' || $tipo_doc=='NC'){
			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM cruc WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Movimiento originado a partir de un cruce, debe eliminarlo por el modulo respectivo.';
				return false;
			}

			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM otin WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Movimiento originado en otros ingresos, debe eliminarlo por el modulo respectivo.';
				return false;
			}

			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM rivc WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Movimiento originado en retenciones de clientes, debe eliminarlo por el modulo respectivo.';
				return false;
			}
		}

		if($tipo_doc=='AN' && $abonos>0){
			$do->error_message_ar['pre_del']='Anticipo ya esta aplicado, debe reversar su aplicacion primero.';
			return false;
		}

		if($tipo_doc=='AN'){
			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM ordc WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Movimiento originado en orden de compra, debe eliminarlo por el modulo respectivo.';
				return false;
			}

			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM ords WHERE transac=${dbtransac}"));
			if($cana>0){
				$do->error_message_ar['pre_del']='Movimiento originado en orden de servicio, debe eliminarlo por el modulo respectivo.';
				return false;
			}
		}

		//Chequea movimientos principales
		$mSQL = "SELECT tipo_doc,cod_cli,monto,abonos,reteiva,numero,fecha FROM smov WHERE transac=${dbtransac} AND id<>${dbid}";
		$query = $this->db->query($mSQL);
		foreach($query->result() as $row){
			$it_tipo_doc= $row->tipo_doc;
			$it_cod_cli = $row->cod_cli;
			$it_numero  = $row->numero;
			$it_monto   = floatval($row->monto);
			$it_abonos  = floatval($row->abonos);
			$it_reteiva = floatval($row->reteiva);

			if($it_tipo_doc=='AB'){
				$do->error_message_ar['pre_del']="Este movimiento fue originado por el abono ${it_numero}, no se puede eliminar a menos que elimine el abono.";
				return false;
			}

			if(($it_cod_cli=='REIVA' || $it_cod_cli=='RETEN') && $it_abonos>0){
				$do->error_message_ar['pre_del']="Este movimiento no puede ser eliminado debido a que la ${it_tipo_doc}${it_numero} ya fue cruzada.";
				return false;
			}
		}
		//Fin de los movimientos principales

		//Chequea las formas de pago
		$mSQL = "SELECT cobrador,tipo FROM sfpa WHERE transac=${dbtransac}";
		$query = $this->db->query($mSQL);
		foreach($query->result() as $row){
			$it_tipo     = $row->tipo;
			$it_cobrador = $row->cobrador;
			$dbcobrador  = $this->db->escape($it_cobrador);

			$cana=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM rcaj WHERE cajero=${dbcobrador} AND fecha=${dbfecha}"));
			if($cana > 0){
				$do->error_message_ar['pre_del']="Este cajero al que cobro este movimiento ya fue cerrado para la fecha del efecto.";
				return false;
			}

			//Debe chequear si esta conciliado
			//if($it_tipo=='DE'){
			//	$do->error_message_ar['pre_del']="El efecto ya fue conciliado, no se puede elimnar";
			//	return false;
			//}

		}
		//Fin de las formas de pago

		//Chequea las aplicaciones al movimiento que se va a borrar
		$mSQL = "SELECT numccli,tipoccli FROM itccli WHERE tipo_doc=${dbtipo_doc} AND numero=${dbnumero} AND cod_cli=${dbcod_cli} AND fecha=${dbfecha}";
		$query = $this->db->query($mSQL);
		if($query->num_rows()>0){
			$efeafec=array();
			foreach($query->result() as $row){
				$efeafec[]=$row->tipoccli.$row->numccli;
			}
			$do->error_message_ar['pre_del']='El efecto esta siendo afectado por: '.implode(',',$efeafec);
			return false;
		}
		//Fin

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
			if($ban==false){ memowrite($mSQL,'smov'); }
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
			$this->datasis->actusal($row->codbanc, $sfecha, (-1)*$it_monto);
			//$mSQL  = "UPDATE bmov SET liable='N', anulado='S' ";
			$mSQL  = 'DELETE FROM  bmov ';
			$mSQL .= "WHERE id=${it_id}";
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'smov'); }
		}
		//Fin de la eliminacion de los movimientos de banco

		$mSQL = "DELETE FROM sfpa WHERE tipo_doc=${dbtipo_doc} AND numero=${dbnumero} AND transac=${dbtransac}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'smov'); }

		$mSQL="DELETE FROM smov WHERE transac=${dbtransac}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'smov'); }

		$mSQL="DELETE FROM itccli WHERE transac=${dbtransac}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'smov'); }

		$primary =implode(',',$do->pk);
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


	function selscli(){
		$this->rapyd->load('dataform');

		$script="$('#cod_cli').autocomplete({
			source: function( req, add){
				$.ajax({
					url:  '".site_url('ajax/buscascli')."',
					type: 'POST',
					dataType: 'json',
					data: {'q':req.term},
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$('#id_scli').val('');

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
				$('#cod_cli').attr('readonly', 'readonly');

				$('#id_scli').val(ui.item.id);

				$('#nombre').val(ui.item.nombre);
				$('#nombre_val').text(ui.item.nombre);

				$('#rifci').val(ui.item.rifci);
				$('#rifci_val').text(ui.item.rifci);

				$('#cod_cli').val(ui.item.cod_cli);

				$('#direc').val(ui.item.direc);
				$('#direc_val').text(ui.item.direc);
				setTimeout(function() {  $('#cod_cli').removeAttr('readonly'); }, 1500);

				var saldo= $.ajax({ type: 'POST', url: '".site_url($this->url.'ajaxsaldo')."/'+ui.item.id, async: false, data: {cod_cli: ui.item.cod_cli } }).responseText;
				$('#saldo_val').text(nformat(saldo,2));
			}
		});";

		$form = new DataForm($this->url.'sclise/process');
		$form->script($script);

		$form->cliente = new inputField('Cliente', 'cod_cli');
		//$form->cliente->rule = "trim|required|max_length[20]";

		$form->id = new hiddenField('', 'id_scli');
		$form->id->in='cliente';

		$form->nombre = new freeField('Nombre','nombre','<b id=\'nombre_val\'></b>');

		$form->rif    = new freeField('RIF/CI','rif','<b id=\'rifci_val\'></b>');

		$form->direc  = new freeField('Direcci&oacute;n','direc','<b id=\'direc_val\'></b>');

		$form->saldo  = new freeField('Saldo','saldo','<b style="font-size:2em" id=\'saldo_val\'></b>');

		//$form->body = new textareaField("Body", "body");
		//$form->body->rule = "required";
		//$form->body->rows = 10;

		$form->build_form();

		echo $form->output;
	}

	function smovprint($id){
		$dbid = $this->db->escape($id);
		$tipo = $this->datasis->dameval('SELECT tipo_doc FROM smov WHERE id='.$dbid);

		switch($tipo){
			case 'NC':
				//Chequea si viene de sfac
				$mSQL='SELECT a.id
				FROM sfac AS a
				JOIN smov AS b ON a.transac=b.transac AND a.tipo_doc="D" AND a.numero=b.numero AND a.fecha=b.fecha
				WHERE b.id='.$dbid;
				$sfac_id=$this->datasis->dameval($mSQL);
				if(!empty($sfac_id)){
					redirect('ventas/sfac/dataprint/modify/'.$sfac_id);
					break;
				}

				//Chequea si viene de una retencion de cliente
				$mSQL='SELECT a.id
				FROM rivc AS a
				JOIN smov AS b ON a.transac=b.transac AND a.fecha=b.fecha
				WHERE b.id='.$dbid;
				$rivc_id=$this->datasis->dameval($mSQL);
				if(!empty($sfac_id)){
					redirect('formatos/ver/RIVC/'.$rivc_id);
					break;
				}

				//Chequea si viene de otin
				$mSQL='SELECT a.id
				FROM otin AS a
				JOIN smov AS b ON a.transac=b.transac AND a.fecha=b.fecha
				WHERE b.id='.$dbid;
				$otin_id=$this->datasis->dameval($mSQL);
				if(!empty($otin_id)){
					redirect('ventas/otin/printotin/'.$otin_id);
					break;
				}

				redirect($this->url.'dataprint/modify/'.$id);
				break;
			case 'AN':
				redirect('formatos/descargar/CCLIAN/'.$id);
				break;
			case 'AB':
				redirect('formatos/descargar/CCLIAB/'.$id);
				break;
			case 'GI':
				redirect('formatos/descargar/CCLIGI/'.$id);
				break;
			case 'FC':
				$mSQL='SELECT a.id
				FROM sfac AS a
				JOIN smov AS b ON a.transac=b.transac AND a.tipo_doc="F" AND a.numero=b.numero AND a.fecha=b.fecha
				WHERE b.id='.$dbid;
				$sfac_id=$this->datasis->dameval($mSQL);
				if(!empty($sfac_id))
					redirect('ventas/sfac/dataprint/modify/'.$sfac_id);
				break;
			case 'ND':
				//Chequea si viene de una retencion de cliente
				$mSQL='SELECT a.id
				FROM rivc AS a
				JOIN smov AS b ON a.transac=b.transac AND a.fecha=b.fecha
				WHERE b.id='.$dbid;
				$rivc_id=$this->datasis->dameval($mSQL);
				if(!empty($sfac_id)){
					redirect('formatos/ver/RIVC/'.$rivc_id);
					break;
				}

				//Chequea si viene de otin
				$mSQL='SELECT a.id
				FROM otin AS a
				JOIN smov AS b ON a.transac=b.transac AND a.fecha=b.fecha
				WHERE b.id='.$dbid;
				$otin_id=$this->datasis->dameval($mSQL);
				if(!empty($otin_id)){
					redirect('ventas/otin/printotin/'.$otin_id);
					break;
				}

				redirect('formatos/descargar/CCLIND/'.$id);
				break;
			default:
				echo 'Formato no definido';
		}
	}

	function ajaxsaldo(){
		$cod_cli = $this->input->post('cod_cli');

		if($cod_cli!==false){
			$this->db->select_sum('a.monto - a.abonos','saldo');
			$this->db->from('smov AS a');
			$this->db->where('a.cod_cli',$cod_cli);
			$this->db->where('a.monto > a.abonos');
			$this->db->where_in('a.tipo_doc',array('FC','ND','GI'));
			$q=$this->db->get();
			$row = $q->row_array();
			echo (empty($row['saldo']))? 0: $row['saldo'];
		}else{
			echo 0;
		}
	}

	//************************************
	// Cobro a clientes
	//************************************

	function ccli($id_scli){
		$id_scli=intval($id_scli);
		$row = $this->datasis->damerow("SELECT cliente,nombre,rifci FROM scli WHERE id=${id_scli}");
		if(empty($row)){
			echo 'Cliente inexistente';
			return '';
		}
			$cliente     = $row['cliente'];
			$dbcliente   = $this->db->escape($cliente);
			$scli_nombre = $row['nombre'];
			$scli_rif    = $row['rifci'];


		$cajero=$this->secu->getcajero();
		if(empty($cajero)){
			echo 'El usuario debe tener registrado un cajero para poder usar este modulo';
			exit();
		}

		$this->rapyd->load('dataobject','datadetails');
		$this->rapyd->uri->keep_persistence();

		$do = new DataObject('smov');
		$do->rel_one_to_many('itccli', 'itccli', array(
			'tipo_doc'=>'tipoccli',
			'numero'  =>'numccli',
			'cod_cli' =>'cod_cli',
			'transac' =>'transac',)
		);
		$do->rel_one_to_many('sfpa'  , 'sfpa'  , array(
			'transac' =>'transac',
			'numero'  =>'numero',
			'tipo_doc'=>'tipo_doc',
			'fecha'   =>'fecha')
		);
		$do->order_by('itccli','itccli.fecha');

		$edit = new DataDetails('Cobro a cliente', $do);
		$edit->on_save_redirect=false;
		$edit->back_url = site_url('finanzas/ccli/filteredgrid');
		$edit->set_rel_title('itccli', 'Efecto <#o#>');
		$edit->set_rel_title('sfpa'  , 'Forma de pago <#o#>');

		$edit->pre_process('insert' , '_pre_ccli_insert');
		$edit->pre_process('update' , '_pre_ccli_update');
		$edit->pre_process('delete' , '_pre_ccli_delete');
		$edit->post_process('insert', '_post_ccli_insert');
		//$edit->post_process('delete', '_post_delete');

		$edit->cod_cli = new hiddenField('Cliente','cod_cli');
		$edit->cod_cli->rule ='max_length[5]';
		$edit->cod_cli->size =7;
		$edit->cod_cli->insertValue=$cliente;
		$edit->cod_cli->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[40]';
		$edit->nombre->size =42;
		$edit->nombre->maxlength =40;

		$edit->codigo = new  dropdownField('Motivo', 'codigo');
		$edit->codigo->option('','Seleccionar');
		$edit->codigo->options('SELECT TRIM(codigo) AS cod, nombre FROM botr WHERE tipo=\'C\' ORDER BY nombre');
		$edit->codigo->style='width:200px;';
		$edit->codigo->rule ='';

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->fecdoc = new dateonlyField('Fecha','fecdoc');
		$edit->fecdoc->db_name='fecha';
		$edit->fecdoc->size =12;
		$edit->fecdoc->maxlength =8;
		$edit->fecdoc->insertValue=date('Y-m-d');
		$edit->fecdoc->calendar = false;
		$edit->fecdoc->rule ='chfecha|required';

		$edit->monto = new inputField('Total','monto');
		$edit->monto->rule='max_length[17]|numeric';
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

		$edit->codigo = new  dropdownField('Motivo', 'codigo');
		$edit->codigo->option('','Ninguno');
		$edit->codigo->options('SELECT TRIM(codigo) AS cod, nombre FROM botr WHERE tipo=\'C\' ORDER BY nombre');
		$edit->codigo->style='width:200px;';
		$edit->codigo->rule ='condi_required|callback_chobligatipo[NC]';

		$edit->usuario = new autoUpdateField('usuario' ,$this->secu->usuario(),$this->secu->usuario());
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora'    ,date('H:i:s'), date('H:i:s'));
		//$edit->fecha   = new autoUpdateField('fecha'   ,date('Ymd'), date('Ymd'));

		//************************************************
		//inicio detalle itccli
		//************************************************
		$i=0;
		$arr_ivas=array();
		$edit->detail_expand_except('itccli');
		$sel=array('a.tipo_doc','a.numero','a.fecha','a.monto','a.abonos','a.monto - a.abonos AS saldo');
		$this->db->select($sel);
		$this->db->from('smov AS a');
		$this->db->where('a.cod_cli',$cliente);
		$transac=$edit->get_from_dataobjetct('transac');
		if($transac!==false){
			$tipo_doc =$edit->get_from_dataobjetct('tipo_doc');
			$dbtransac=$this->db->escape($transac);
			$this->db->join('itccli AS b','a.tipo_doc = b.tipoccli AND a.numero=b.numccli AND a.transac='.$dbtransac);
			$this->db->where('a.tipo_doc',$tipo_doc);
		}else{
			$this->db->where('a.monto > a.abonos');
			$this->db->where_in('a.tipo_doc',array('FC','ND','GI'));
		}
		$this->db->order_by('a.fecha');
		$query = $this->db->get();
		//echo $this->db->last_query();
		foreach ($query->result() as $row){
			//$arr_ivas[$i]=array('');

			$obj='cod_cli_'.$i;
			$edit->$obj = new autoUpdateField('cod_cli',$cliente,$cliente);
			$edit->$obj->rel_id  = 'itccli';
			$edit->$obj->ind     = $i;

			$obj='tipo_doc_'.$i;
			$edit->$obj = new inputField('Tipo_doc',$obj);
			$edit->$obj->db_name='tipo_doc';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='max_length[2]';
			$edit->$obj->insertValue=$row->tipo_doc;
			$edit->$obj->size =4;
			$edit->$obj->maxlength =2;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='numero_'.$i;
			$edit->$obj = new inputField('Numero',$obj);
			$edit->$obj->db_name='numero';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='max_length[8]';
			$edit->$obj->insertValue=$row->numero;
			$edit->$obj->size =10;
			$edit->$obj->maxlength =8;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='fecha_'.$i;
			$edit->$obj = new dateonlyField('Fecha',$obj);
			$edit->$obj->db_name='fecha';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='chfecha';
			$edit->$obj->insertValue=$row->fecha;
			$edit->$obj->size =10;
			$edit->$obj->maxlength =8;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='monto_'.$i;
			$edit->$obj = new inputField('Monto',$obj);
			$edit->$obj->db_name='monto';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='max_length[18]|numeric';
			$edit->$obj->css_class='inputnum';
			$edit->$obj->size =20;
			$edit->$obj->insertValue=$row->monto;
			$edit->$obj->maxlength =18;
			$edit->$obj->ind       = $i;
			$edit->$obj->showformat='decimal';
			$edit->$obj->type='inputhidden';

			$obj='saldo_'.$i;
			$edit->$obj = new freeField($obj,$obj,nformat($row->saldo));
			$edit->$obj->ind = $i;

	        $obj='abono_'.$i;
			$edit->$obj = new inputField('Abono',$obj);
			$edit->$obj->db_name      = 'abono';
			$edit->$obj->rel_id       = 'itccli';
			$edit->$obj->rule         = "max_length[18]|numeric|positive|callback_chabono[$i]";
			$edit->$obj->css_class    = 'inputnum';
			$edit->$obj->showformat   = 'decimal';
			$edit->$obj->autocomplete = false;
			$edit->$obj->disable_paste= true;
			$edit->$obj->size         = 15;
			$edit->$obj->maxlength    = 18;
			$edit->$obj->ind          = $i;
			$edit->$obj->onfocus      = 'itsaldo(this,'.round($row->saldo,2).');';

	        $obj='ppago_'.$i;
			$edit->$obj = new inputField('Pronto Pago',$obj);
			$edit->$obj->db_name      = 'ppago';
			$edit->$obj->rel_id       = 'itccli';
			$edit->$obj->rule         = "max_length[18]|numeric|positive|callback_chppago[$i]";
			$edit->$obj->css_class    = 'inputnum';
			$edit->$obj->showformat   = 'decimal';
			$edit->$obj->autocomplete = false;
			$edit->$obj->disable_paste= true;
			$edit->$obj->size         = 15;
			$edit->$obj->maxlength    = 18;
			$edit->$obj->ind          = $i;
			$edit->$obj->onchange     = "itppago(this,'$i');";

			$i++;
		}
		$edit->tipo_doc = new  dropdownField('Tipo doc.', 'tipo_doc');
		if($i>0){
			$edit->tipo_doc->option('AB','Abono');
			$edit->tipo_doc->option('NC','Nota de credito');
		}else{
			$edit->tipo_doc->insertValue='AN';
		}
		$edit->tipo_doc->option('AN','Anticipo');
		$edit->tipo_doc->style='width:140px;';
		$edit->tipo_doc->onchange='chtipodoc()';
		$edit->tipo_doc->rule ='enum[AB,NC,AN]|required';

		//************************************************
		//fin de campos para detalle,inicio detalle2 sfpa
		//************************************************
		$edit->tipo = new  dropdownField('Tipo <#o#>', 'tipo_<#i#>');
		$edit->tipo->option('','Ninguno');
		$edit->tipo->options('SELECT tipo, nombre FROM tarjeta WHERE activo=\'S\' ORDER BY nombre');
		$edit->tipo->db_name  = 'tipo';
		$edit->tipo->rel_id   = 'sfpa';
		$edit->tipo->style    = 'width:160px;';
		$edit->tipo->rule     = 'condi_required|callback_chsfpatipo[<#i#>]';
		$edit->tipo->insertValue='EF';
		$edit->tipo->onchange = 'sfpatipo(<#i#>)';
		$edit->tipo->rule     = 'callback_chbmovrep[<#i#>]';

		$edit->sfpafecha = new dateonlyField('Fecha','sfpafecha_<#i#>');
		$edit->sfpafecha->rel_id   = 'sfpa';
		$edit->sfpafecha->db_name  = 'fecha';
		$edit->sfpafecha->size     = 12;
		$edit->sfpafecha->maxlength= 8;
		$edit->sfpafecha->calendar = false;
		$edit->sfpafecha->rule ='condi_required|chitfecha|callback_chtipo[<#i#>]';

		$edit->numref = new inputField('Numero <#o#>', 'num_ref_<#i#>');
		$edit->numref->size     = 15;
		$edit->numref->db_name  = 'num_ref';
		$edit->numref->rel_id   = 'sfpa';
		$edit->numref->rule     = 'trim|condi_required|callback_chtipo[<#i#>]';

		$edit->banco = new dropdownField('Banco <#o#>', 'banco_<#i#>');
		$edit->banco->option('','Ninguno');
		$edit->banco->options('SELECT cod_banc,nomb_banc
			FROM tban
			WHERE cod_banc<>\'CAJ\'
		UNION ALL
			SELECT codbanc,CONCAT_WS(\' \',TRIM(banco),numcuent)
			FROM banc
			WHERE tbanco <> \'CAJ\' ORDER BY nomb_banc');
		$edit->banco->db_name='banco';
		$edit->banco->rel_id ='sfpa';
		$edit->banco->style  ='width:200px;';
		$edit->banco->rule   = 'condi_required|callback_chtipo[<#i#>]';

		$edit->itmonto = new inputField('Monto <#o#>', 'itmonto_<#i#>');
		$edit->itmonto->db_name     = 'monto';
		$edit->itmonto->css_class   = 'inputnum';
		$edit->itmonto->rel_id      = 'sfpa';
		$edit->itmonto->size        = 14;
		$edit->itmonto->rule        = 'condi_required|positive|callback_chmontosfpa[<#i#>]';
		$edit->itmonto->showformat  = 'decimal';
		$edit->itmonto->autocomplete= false;
		//************************************************
		// Fin detalle 2 (sfpa)
		//************************************************

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
			$conten['cana']  = $i;
			$conten['form']  = & $edit;
			$conten['title'] = heading("Cobro a cliente: (${cliente}) ${scli_nombre} ${scli_rif}");

			$data['content'] = $this->load->view('view_ccli.php', $conten);
		}
	}

	function _pre_ccli_insert($do){

		$cliente  =$do->get('cod_cli');
		$estampa = $do->get('estampa');
		$hora    = $do->get('hora');
		$usuario = $do->get('usuario');
		$cod_cli = $do->get('cod_cli');
		$tipo_doc= $do->get('tipo_doc');
		$fecha   = $do->get('fecha');
		$concepto= $do->get('observa1');
		$codigo  = $do->get('codigo');
		$itabono = $sfpamonto=$ppagomonto=$impuesto=$ppimpuesto=0;
		$dbcod_cli=$this->db->escape($cod_cli);
		$this->ppagodata=$ivadata=array(
			'montasa'  =>0,
			'monredu'  =>0,
			'monadic'  =>0,
			'tasa'     =>0,
			'reducida' =>0,
			'sobretasa'=>0,
			'exento'   =>0
		);

		$rrow = $this->datasis->damerow('SELECT nombre,rifci,dire11,dire12 FROM scli WHERE cliente='.$this->db->escape($cliente));
		if($rrow!=false){
			$do->set('nombre',$rrow['nombre']);
			$do->set('dire1' ,$rrow['dire11']);
			$do->set('dire2' ,$rrow['dire12']);
		}

		$cfecha=intval(str_replace('-','',$fecha));
		//Totaliza el abonado
		$rel='itccli';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$itaabono = $do->get_rel($rel, 'abono', $i);
			$pppago   = $do->get_rel($rel, 'ppago', $i);
			$itfecha  = $do->get_rel($rel, 'fecha', $i);

			if(empty($itaabono) || $itaabono==0){
				$do->rel_rm($rel,$i);
			}else{
				$citfecha  = intval(str_replace('-','',$itfecha));
				if($citfecha>$cfecha){
					$do->error_message_ar['pre_ins']='No puede pagar un efecto con una fecha anterior a su emision como el caso de '.$ittipo.$itnumero;
					return false;
				}

				$itabono += floatval($itaabono);
				if(empty($pppago)){
					$do->set_rel($rel,'ppago',0,$i);
				}else{
					$ppagomonto += $do->get_rel($rel, 'ppago', $i);
				}
			}
		}
		$itabono=round($itabono,2);

		//Totaliza lo pagado
		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpamonto+=$do->get_rel($rel, 'monto', $i);
		}
		$sfpamonto=round($sfpamonto,2);

		//Realiza las validaciones
		$cajero=$this->secu->getcajero();
		$this->load->library('validation');
		$rt=$this->validation->cajerostatus($cajero);
		if(!$rt){
			$do->error_message_ar['pre_ins']='El cajero usado ('.$cajero.') esta cerrado para esta fecha';
			return false;
		}

		if($tipo_doc=='NC'){
			$do->truncate_rel('sfpa');
			if($itabono==0){
				$do->error_message_ar['pre_ins']='Si crea una nota de credito debe relacionarla con algun movimiento';
				return false;
			}
		}elseif($tipo_doc=='AN'){
			$do->truncate_rel('itccli');
			if($itabono!=0){
				$do->error_message_ar['pre_ins']='Un anticipo no puede estar relacionado con algun efecto, en tal caso seria un abono';
				return false;
			}else{
				$itabono=$sfpamonto;
			}
		}else{
			if(abs($sfpamonto-$itabono)>0.01){
				$do->error_message_ar['pre_ins']='El monto cobrado no coincide con el monto de la la transacci&oacute;n';
				return false;
			}
			if($itabono==0){
				$do->error_message_ar['pre_ins']='Un Abono tiene que estar relacionado con una deuda, de lo contrario seria un anticipo.';
				return false;
			}
		}
		//fin de las validaciones
		$do->set('monto',$itabono);

		$dbcliente= $this->db->escape($cliente);
		$rowscli  = $this->datasis->damerow('SELECT nombre,dire11,dire12 FROM scli WHERE cliente='.$dbcliente);
		if(empty($rowscli)){
			$do->error_message_ar['pre_ins']='Cliente inexistente.';
			return false;
		}
		$do->set('nombre', $rowscli['nombre']);
		$do->set('dire1' , $rowscli['dire11']);
		$do->set('dire2' , $rowscli['dire12']);

		$transac  = $this->datasis->fprox_numero('ntransa');
		$ningreso = $this->datasis->fprox_numero('ningreso');
		if($tipo_doc!='AN'){
			$control  = $this->datasis->fprox_numero('nsmov');
		}else{
			$control = '';
		}

		if($tipo_doc=='AB'){
			$mnum = $this->datasis->fprox_numero('nabcli');
		}elseif($tipo_doc=='GI'){
			$mnum = $this->datasis->fprox_numero('ngicli');
		}elseif($tipo_doc=='NC'){
			$mnum = $this->datasis->fprox_numero('nccli');
		}else{
			$mnum = $this->datasis->fprox_numero('nancli');
		}
		$do->set('vence'  , $fecha);
		$do->set('numero' , $mnum);
		$do->set('transac', $transac);
		$do->set('control', $control);

		$rel='itccli';
		$observa=array();
		$cana = $do->count_rel($rel);
		if($cana>0){
			$iis=array_keys($do->data_rel[$rel]);
			foreach($iis as $i){
				$itabono = floatval($do->get_rel($rel, 'abono'   , $i));
				$ittipo  = $do->get_rel($rel, 'tipo_doc', $i);
				$itnumero= $do->get_rel($rel, 'numero'  , $i);
				//$itmonto = floatval($do->get_rel($rel, 'monto'  , $i));

				$dbittipo   = $this->db->escape($ittipo);
				$dbitnumero = $this->db->escape($itnumero);

				$rrow=$this->datasis->damerow("SELECT impuesto,monto,montasa,monredu,monadic,tasa,reducida,sobretasa,exento  FROM smov WHERE cod_cli=${dbcod_cli} AND tipo_doc=${dbittipo} AND numero=${dbitnumero}");
				if(empty($rrow)){
					$do->error_message_ar['pre_ins']='Efecto inexistente '.$ittipo.$itnumero;
					return false;
				}
				$itimpuesto = floatval($rrow['impuesto']);
				$itmonto    = floatval($rrow['monto']);

				$impuesto  += $itabono*$itimpuesto/$itmonto;

				$observa[]=$ittipo.$itnumero;
				$do->set_rel($rel, 'tipoccli', $tipo_doc, $i);
				$do->set_rel($rel, 'cod_cli' , $cod_cli , $i);
				$do->set_rel($rel, 'estampa' , $estampa , $i);
				$do->set_rel($rel, 'hora'    , $hora    , $i);
				$do->set_rel($rel, 'usuario' , $usuario , $i);
				$do->set_rel($rel, 'transac' , $transac , $i);
				$do->set_rel($rel, 'monto'   , $itmonto , $i);
				$do->set_rel($rel, 'mora'    , 0, $i);
				$do->set_rel($rel, 'reten'   , 0, $i);
				$do->set_rel($rel, 'cambio'  , 0, $i);
				$do->set_rel($rel, 'reteiva' , 0, $i);

				if($tipo_doc=='NC'){
					$ivadata['montasa'  ]+= floatval($rrow['montasa'  ])*$itabono/$itmonto;
					$ivadata['monredu'  ]+= floatval($rrow['monredu'  ])*$itabono/$itmonto;
					$ivadata['monadic'  ]+= floatval($rrow['monadic'  ])*$itabono/$itmonto;
					$ivadata['tasa'     ]+= floatval($rrow['tasa'     ])*$itabono/$itmonto;
					$ivadata['reducida' ]+= floatval($rrow['reducida' ])*$itabono/$itmonto;
					$ivadata['sobretasa']+= floatval($rrow['sobretasa'])*$itabono/$itmonto;
					$ivadata['exento'   ]+= floatval($rrow['exento'   ])*$itabono/$itmonto;
				}

				$pppago = $do->get_rel($rel, 'ppago', $i);
				if($pppago>0){
					$ppimpuesto += $pppago*$itimpuesto/$itmonto;

					$this->ppagodata['montasa'  ]+= floatval($rrow['montasa'  ])*$pppago/$itmonto;
					$this->ppagodata['monredu'  ]+= floatval($rrow['monredu'  ])*$pppago/$itmonto;
					$this->ppagodata['monadic'  ]+= floatval($rrow['monadic'  ])*$pppago/$itmonto;
					$this->ppagodata['tasa'     ]+= floatval($rrow['tasa'     ])*$pppago/$itmonto;
					$this->ppagodata['reducida' ]+= floatval($rrow['reducida' ])*$pppago/$itmonto;
					$this->ppagodata['sobretasa']+= floatval($rrow['sobretasa'])*$pppago/$itmonto;
					$this->ppagodata['exento'   ]+= floatval($rrow['exento'   ])*$pppago/$itmonto;
				}
			}
		}

		if($tipo_doc=='NC'){
			$do->set('montasa'  ,round($ivadata['montasa'  ],2));
			$do->set('monredu'  ,round($ivadata['monredu'  ],2));
			$do->set('monadic'  ,round($ivadata['monadic'  ],2));
			$do->set('tasa'     ,round($ivadata['tasa'     ],2));
			$do->set('reducida' ,round($ivadata['reducida' ],2));
			$do->set('sobretasa',round($ivadata['sobretasa'],2));
			$do->set('exento'   ,round($ivadata['exento'   ],2));
		}

		if(empty($concepto)){
			if(count($observa)>0){
				$observa='PAGA '.implode(',',$observa);
				$do->set('observa1' , substr($observa,0,50));
				if(strlen($observa)>50) $do->set('observa2' , substr($observa,50));
			}
		}else{
			$do->set('observa1' , substr($concepto,0,50));
			if(strlen($concepto)>50) $do->set('observa2' , substr($concepto,50));
		}

		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpatipo=$do->get_rel($rel, 'tipo_doc', $i);
			if($sfpatipo=='EF') $do->set_rel($rel, 'fecha' , $fecha , $i);

			$do->set_rel($rel,'estampa'  , $estampa , $i);
			$do->set_rel($rel,'hora'     , $hora    , $i);
			$do->set_rel($rel,'usuario'  , $usuario , $i);
			$do->set_rel($rel,'transac'  , $transac , $i);
			$do->set_rel($rel,'f_factura', $fecha   , $i);
			$do->set_rel($rel,'cod_cli'  ,$cliente  , $i);
			$do->set_rel($rel,'cobro'    ,$fecha    , $i);
			$do->set_rel($rel,'vendedor' ,$this->secu->getvendedor(),$i);
			$do->set_rel($rel,'cobrador' ,$this->secu->getcajero()  ,$i);
			$do->set_rel($rel,'almacen'  ,$this->secu->getalmacen() ,$i);
		}
		$this->ppagomonto=$ppagomonto;
		$this->ppimpuesto=$ppimpuesto;

		$do->set('mora'    ,0);
		$do->set('reten'   ,0);
		$do->set('cambio'  ,0);
		$do->set('reteiva' ,0);
		$do->set('impuesto',$impuesto);
		$do->set('ppago'   ,$ppagomonto);
		$do->set('ningreso',$ningreso);
		$do->set('vendedor', $this->secu->getvendedor());

		$this->ppagodata['montasa'  ] = round($this->ppagodata['montasa'  ],2);
		$this->ppagodata['monredu'  ] = round($this->ppagodata['monredu'  ],2);
		$this->ppagodata['monadic'  ] = round($this->ppagodata['monadic'  ],2);
		$this->ppagodata['tasa'     ] = round($this->ppagodata['tasa'     ],2);
		$this->ppagodata['reducida' ] = round($this->ppagodata['reducida' ],2);
		$this->ppagodata['sobretasa'] = round($this->ppagodata['sobretasa'],2);
		$this->ppagodata['exento'   ] = round($this->ppagodata['exento'   ],2);

		if(!empty($codigo)){
			$dbcodigo = $this->db->escape($codigo);
			$do->set('descrip' ,$this->datasis->dameval("SELECT TRIM(nombre) AS val FROM botr WHERE codigo=${dbcodigo}"));
		}

		return true;
	}

	function _post_ccli_insert($do){
		$numero   =$do->get('numero');
		$cliente  =$do->get('cod_cli');
		$dbcliente=$this->db->escape($cliente);
		$impuesto =$do->get('impuesto');
		$ttipo_doc=$do->get('tipo_doc');
		$concepto =$do->get('observa1');
		$ningreso =$do->get('ningreso');
		$ffecha   =$do->get('fecha');
		$dbffecha =$this->db->escape($ffecha);

		$rel_id='itccli';
		$cana = $do->count_rel($rel_id);
		if($cana>0){
			if($this->ppagomonto>0){
				//Crea la NC por Pronto pago
				$mnumnc  = $this->datasis->fprox_numero('nccli');
				$control = $this->datasis->fprox_numero('nsmov');

				$dbdata=array();
				$dbdata['cod_cli']    = $cliente;
				$dbdata['nombre']     = $do->get('nombre');
				$dbdata['dire1']      = $do->get('dire1');
				$dbdata['dire2']      = $do->get('dire2');
				$dbdata['tipo_doc']   = 'NC';
				$dbdata['numero']     = $mnumnc;
				$dbdata['fecha']      = $do->get('fecha');
				$dbdata['monto']      = $this->ppagomonto;
				$dbdata['impuesto']   = $this->ppimpuesto;
				$dbdata['abonos']     = $this->ppagomonto;
				$dbdata['vence']      = $do->get('fecha');
				$dbdata['tipo_ref']   = 'AB';
				$dbdata['num_ref']    = $do->get('numero');
				$dbdata['observa1']   = 'DESC. P.PAGO EN ABONO QUE '.$concepto;
				$dbdata['estampa']    = $do->get('estampa');
				$dbdata['hora']       = $do->get('hora');
				$dbdata['transac']    = $do->get('transac');
				$dbdata['usuario']    = $do->get('usuario');
				$dbdata['codigo']     = 'DEPPC';
				$dbdata['descrip']    = 'DESCUENTO PRONTO PAGO';
				$dbdata['fecdoc']     = $do->get('fecha');
				$dbdata['control']    = $control;
				$dbdata['nroriva']    = '';
				$dbdata['emiriva']    = '';
				$dbdata['reten']      = 0;
				$dbdata['cambio']     = 0;
				$dbdata['mora']       = 0;
				$dbdata['montasa']    = $this->ppagodata['montasa'  ];
				$dbdata['monredu']    = $this->ppagodata['monredu'  ];
				$dbdata['monadic']    = $this->ppagodata['monadic'  ];
				$dbdata['tasa']       = $this->ppagodata['tasa'     ];
				$dbdata['reducida']   = $this->ppagodata['reducida' ];
				$dbdata['sobretasa']  = $this->ppagodata['sobretasa'];
				$dbdata['exento']     = $this->ppagodata['exento'   ];

				$mSQL = $this->db->insert_string('smov', $dbdata);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'ccli'); }

				$itdbdata=array();
				$itdbdata['cod_cli']  = $cliente;
				$itdbdata['numccli']  = $mnumnc;
				$itdbdata['tipoccli'] = 'NC';
				$itdbdata['estampa']  = $do->get('estampa');
				$itdbdata['hora']     = $do->get('hora');
				$itdbdata['transac']  = $do->get('transac');
				$itdbdata['usuario']  = $do->get('usuario');
				$itdbdata['fecha']    = $do->get('fecha');
				$itdbdata['reten']    = 0;
				$itdbdata['cambio']   = 0;
				$itdbdata['mora']     = 0;

				unset($dbdata);
			}

			foreach($do->data_rel[$rel_id] AS $i=>$data){
				$tipo_doc = $data['tipo_doc'];
				$numero   = $data['numero'];
				$fecha    = $data['fecha'];
				$monto    = $data['abono'];
				$aplmonto = $data['monto'];
				$ppago    = (empty($data['ppago']))? 0: $data['ppago'];

				$dbtipo_doc = $this->db->escape($tipo_doc);
				$dbnumero   = $this->db->escape($numero  );
				$dbfecha    = $this->db->escape($fecha   );
				$dbmonto    = $monto+$ppago;

				$mSQL="UPDATE smov SET abonos=abonos+${dbmonto} WHERE tipo_doc=${dbtipo_doc} AND numero=${dbnumero} AND cod_cli=${dbcliente} LIMIT 1";
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'ccli'); }

				if($ttipo_doc=='AB' && $tipo_doc=='FC'){
					$mSQL="UPDATE sfac SET pagada=${dbffecha} WHERE tipo_doc='F' AND numero=${dbnumero} AND cod_cli=${dbcliente} LIMIT 1";
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'ccli'); }
				}

				if($ppago > 0){
					$itdbdata['tipo_doc'] = $tipo_doc;
					$itdbdata['numero']   = $numero;
					$itdbdata['abono']    = $ppago;
					$itdbdata['ppago']    = $ppago;
					$itdbdata['monto']    = $aplmonto;

					$mSQL = $this->db->insert_string('itccli', $itdbdata);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'ccli'); }
				}
			}
		}

		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpatipo = $do->get_rel($rel, 'tipo', $i);
			$codbanc  = $do->get_rel($rel,'banco',$i);
			$dbcodbanc= $this->db->escape($codbanc);
			$monto    = $do->get_rel($rel,'monto',$i);

			//Si es deposito en banco o transferencia crea el movimiento
			if($sfpatipo=='DE' || $sfpatipo=='NC'){
				$sql ='SELECT tbanco,moneda,banco,saldo,depto,numcuent FROM banc WHERE codbanc='.$dbcodbanc;
				$fila=$this->datasis->damerow($sql);

				$ffecha  = $do->get_rel($rel,'fecha',$i);
				$itdbdata=array();
				$itdbdata['codbanc']  = $codbanc;
				$itdbdata['moneda']   = $fila['moneda'];
				$itdbdata['numcuent'] = $fila['numcuent'];
				$itdbdata['banco']    = $fila['banco'];
				$itdbdata['saldo']    = $fila['saldo']+$monto;
				$itdbdata['tipo_op']  = $do->get_rel($rel,'tipo',$i);
				$itdbdata['numero']   = str_pad($do->get_rel($rel,'num_ref',$i), 12,'0', STR_PAD_LEFT);
				$itdbdata['fecha']    = $ffecha;
				$itdbdata['clipro']   = 'C';
				$itdbdata['codcp']    = $cliente;
				$itdbdata['nombre']   = $do->get('nombre');
				$itdbdata['monto']    = $monto;
				$itdbdata['bruto']    = $monto;
				$itdbdata['concepto'] = 'INGRESO POR COBRANZA';
				$itdbdata['concep2']  = $concepto;
				$itdbdata['status']   = 'P';
				$itdbdata['liable']   = 'S';
				$itdbdata['transac']  = $do->get('transac');
				$itdbdata['usuario']  = $do->get('usuario');
				$itdbdata['estampa']  = $do->get('estampa');
				$itdbdata['hora']     = $do->get('hora');
				$itdbdata['negreso']  = $ningreso;
				$itdbdata['anulado']  = 'N';
				$mSQL = $this->db->insert_string('bmov', $itdbdata);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'ccli'); }

				$sfecha=str_replace('-','',$ffecha);
				$this->datasis->actusal($codbanc, $sfecha, $monto);
			}
		}
		logusu('smov',"Cobro a cliente ${ttipo_doc}${numero} creado");
	}

	function _pre_ccli_update($do){
		return false;
	}

	function _pre_ccli_delete($do){
		return false;
	}

	function chsfpatipo($val){
		$tipo=$this->input->post('tipo_doc');
		if($tipo=='NC') {
			return true;
		}
		$this->validation->set_message('chsfpatipo', 'El campo %s es obligatorio');
		if(empty($val)){
			return false;
		}else{
			return true;
		}
	}

	function chfuturo($fecha){
		$fdoc=timestampFromInputDate($fecha);
		$fact=mktime();

		if($fdoc > $fact){
			$this->validation->set_message('chfuturo', 'No puede meter un efecto a futuro');
			return false;
		}
		return true;
	}

	function chtipo($val,$i){
		$tipo=$this->input->post('tipo_'.$i);
		if(empty($tipo)) return true;
		$this->validation->set_message('chtipo', 'El campo %s es obligatorio');

		if(empty($val) && ($tipo=='NC' || $tipo=='DP' || $tipo=='DE'))
			return false;
		else
			return true;
	}

	function chbmovrep($tipo,$i){
		$this->validation->set_message('chbmovrep', 'Ya existe un movimiento en banco con las mismas caracteristicas dadas previamente registrado.');
		//$tipo   = $this->input->post('tipo_'.$i);
		if(empty($tipo)) return true;

		if($tipo=='NC' || $tipo=='DP' || $tipo=='DE'){
			$codban = $this->input->post('banco_'.$i);
			$numref = $this->input->post('num_ref_'.$i);
			$numero = str_pad(trim($numref), 12,'0', STR_PAD_LEFT);

			$dbtipo   = $this->db->escape($tipo);
			$dbnumero = $this->db->escape($numero);
			$dbcodban = $this->db->escape($codban);

			$mSQL = "SELECT COUNT(*) AS cana FROM bmov WHERE tipo_op=${dbtipo} AND numero=${dbnumero} AND codbanc=${dbcodban}";
			$cana = intval($this->datasis->dameval($mSQL));
			if($cana>0){
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
	}

	function chmontosfpa($monto){
		$tipo   = $this->input->post('tipo_doc');
		if($tipo=='NC'){
			return true;
		}
		if(empty($monto) || $monto==0){
			$this->validation->set_message('chmontosfpa', "El campo %s es obligatorio");
			return false;
		}
		return true;
	}

	function chppago($monto,$i){
		$tipo   = $this->input->post('tipo_doc');
		$itmonto= floatval($this->input->post('abono_'.$i));
		if($tipo=='NC' && $monto>0){
			$this->validation->set_message('chppago', "No se puede hacer pronto pago cuando el tipo de documento es una nota de cr&eacute;dito");
			return false;
		}

		if($itmonto<=0 && floatval($monto)>0){
			$this->validation->set_message('chppago', "No se puede hacer pronto pago cuando a un efecto que no esta abonado.");
			return false;
		}
		return true;
	}

	//Obliga el campo segun el tipo
	function chobligatipo($val,$tipo){
		$tipo_doc = $this->input->post('tipo_doc');
		if($tipo_doc==$tipo && empty($val)){
			$this->validation->set_message('chobligatipo', "El campo %s es necesario cuando el tipo es ${tipo}");
			return false;
		}
		return true;
	}

	function chabono($monto,$i){
		$tipo   = $this->input->post('tipo_doc_'.$i);
		$ppago  = $this->input->post('ppago_'.$i);
		$numero = $this->input->post('numero_'.$i);
		$cod_cli= $this->input->post('cod_cli');
		$fecha  = human_to_dbdate($this->input->post('fecha_'.$i));

		$this->db->select(array('monto - abonos AS saldo'));
		$this->db->from('smov');
		$this->db->where('tipo_doc',$tipo);
		$this->db->where('numero'  ,$numero);
		$this->db->where('fecha'   ,$fecha);
		$this->db->where('cod_cli' ,$cod_cli);

		$query = $this->db->get();
		$row   = $query->row();

		if ($query->num_rows() == 0) return false;
		$saldo = $row->saldo;

		if(($monto+$ppago)<=$saldo){
			return true;
		}else{
			$this->validation->set_message('chabono', "No se le puede abonar al efecto $tipo-$numero un monto mayor al saldo");
			return false;
		}
	}

	//************************************
	// Fin cobro a clientes
	//************************************

	function _exitescli($cliente){
		$dbscli= $this->db->escape($cliente);
		$mSQL  = "SELECT COUNT(*) AS cana FROM scli WHERE cliente=${dbscli}";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if( $row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function tabla() {
		$id  = $this->uri->segment($this->uri->total_segments());
		$dbid= $this->db->escape($id);

		$row = $this->datasis->damerow("SELECT cod_cli, tipo_doc, numero, estampa, transac FROM smov WHERE id=${dbid}");
		if(empty($row)){
			echo 'Registro no encontrado';
			return '';
		}

		$transac   = $row['transac'];
		$cod_cli   = $row['cod_cli'];
		$numero    = $row['numero'];
		$tipo_doc  = $row['tipo_doc'];
		$estampa   = $row['estampa'];

		if(empty($transac)){
			echo 'Movimiento sin relaciones.';
			return '';
		}

		$dbtipo_doc= $this->db->escape($tipo_doc);
		$dbnumero  = $this->db->escape($numero);
		$dbtiponum = $this->db->escape($tipo_doc.$numero);
		$dbestampa = $this->db->escape($estampa);
		$dbtransac = $this->db->escape($transac);
		$dbcod_cli = $this->db->escape($cod_cli);


		$td1  = "<td style='border-style:solid;border-width:1px;border-color:#78FFFF;' valign='top' align='center'>\n";
		$td1 .= "<table width='98%'>\n<caption style='background-color:#5E352B;color:#FFFFFF;font-style:bold'>";

		// Movimientos Relacionados en Proveedores SPRM
		$mSQL = "SELECT cod_prv, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
			FROM sprm WHERE transac=${dbtransac} ORDER BY cod_prv ";
		$query = $this->db->query($mSQL);
		$salida = '<table width="100%"><tr>';
		$saldo  = 0;
		if($query->num_rows() > 0 ){
			$salida .= $td1;
			$salida .= 'Movimiento en Proveedores</caption>';
			$salida .= "<tr bgcolor='#E7E3E7'><td>Nombre</td><td>Tp</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				if ( $row['tipo_doc'] == 'FC' ) {
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= '<tr>';
				$salida .= '<td>'.trim($row['cod_prv']).'-'.$row['nombre'].'</td>';
				$salida .= '<td>'.$row['tipo_doc'].'</td>';
				$salida .= '<td>'.$row['numero'].  '</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
			}
			if ($saldo <> 0)
				$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo).'</td></tr>';
			$salida .= '</table></td>';
		}

		// Movimientos Relacionados en SMOV
		$mSQL = "SELECT cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
			FROM smov WHERE transac=${dbtransac} AND id<>${dbid} ORDER BY cod_cli ";
		$query = $this->db->query($mSQL);
		$saldo = 0;
		if($query->num_rows() > 0){
			$salida .= $td1;
			$salida .= 'Movimiento en Clientes</caption>';
			$salida .= "<tr bgcolor='#e7e3e7'><td>Nombre</td><td>Tp</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				if ( $row['tipo_doc'] == 'FC' ) {
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= '<tr>';
				$salida .= '<td>'.$row['cod_cli'].'-'.$row['nombre'].'</td>';
				$salida .= '<td>'.$row['tipo_doc'].'</td>';
				$salida .= '<td>'.$row['numero'].  '</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '</table></td>';
		}

		//Retencion de IVA RIVC
		$mSQL = "
			SELECT b.periodo, b.nrocomp, a.reiva
			FROM itrivc a JOIN rivc b ON a.idrivc=b.id WHERE a.tipo_doc=IF('$tipo_doc'='FC','F','D') AND a.numero=${dbnumero} AND b.anulado<>'S'
			UNION ALL
			SELECT b.periodo, b.nrocomp, a.reiva
			FROM itrivc a JOIN rivc b ON a.idrivc=b.id WHERE a.transac=${dbtransac}";
		$query = $this->db->query($mSQL);
		if($query->num_rows() > 0){
			$salida .= $td1;
			$salida .= 'Retenciones de IVA</caption>';
			$salida .= "<tr bgcolor='#e7e3e7'><td>Per&iacute;odo</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</tr>";
			foreach ($query->result_array() as $row){
				$salida .= '<tr>';
				$salida .= '<td>'.$row['periodo'].'</td>';
				$salida .= '<td>'.$row['nrocomp'].'</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['reiva']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '</table></td>';
		}

		// Factura Relacionada SFAC
		if($tipo_doc <> 'FC'){
			$mSQL = "SELECT tipo_doc, numero, totalg FROM sfac a WHERE a.transac=${dbtransac}";
			$query = $this->db->query($mSQL);
			if ( $query->num_rows() > 0 ){
				$salida .= $td1;
				$salida .= 'Factura Relacionada</caption>';
				$salida .= "<tr bgcolor='#e7e3e7'><td>Tipo</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</tr>";
				foreach ($query->result_array() as $row){
					$salida .= '<tr>';
					$salida .= '<td>'.$row['tipo_doc'].'</td>';
					$salida .= '<td>'.$row['numero'].'</td>';
					$salida .= '<td align=\'right\'>'.nformat($row['totalg']).'</td>';
					$salida .= '</tr>';
				}
				$salida .= '</table></td>';
			}
		}

		// Movimientos Relacionados ITCCLI
		$mSQL = "SELECT tipo_doc, numero, monto, abono FROM itccli WHERE transac=${dbtransac} AND estampa=${dbestampa}";
		$query = $this->db->query($mSQL);
		if($query->num_rows() == 0){
			$mSQL = "SELECT tipoccli tipo_doc, numccli numero, monto, abono FROM itccli WHERE tipo_doc=${dbtipo_doc} AND numero=${dbnumero} ";
			$query = $this->db->query($mSQL);
		}
		if($query->num_rows() > 0){
			$saldo = 0;
			$salida .= $td1;
			$salida .= 'Movimientos Relacionados</caption>';
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td><td align='center'>Abono</td></tr>";
			foreach ($query->result_array() as $row){
				$saldo += $row['abono'];
				$salida .= '<tr>';
				$salida .= '<td>'.$row['tipo_doc'].'</td>';
				$salida .= '<td>'.$row['numero'].'</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['abono']).'</td>';
				$salida .= '</tr>';
			}
			if ($saldo <> 0)
				$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'><b>Saldo : ".nformat($saldo). "</b></td></tr>";
			$salida .= '</table></td>';
		}

		// FORMA DE PAGO SFPA
		$mSQL = "SELECT CONCAT(a.tipo,' ', b.nombre) tipo, a.num_ref, a.banco, a.monto
			FROM sfpa a JOIN tarjeta b ON a.tipo=b.tipo WHERE a.transac=${dbtransac} AND a.monto<>0";
		$query = $this->db->query($mSQL);
		$codcli = 'XXXXXXXXXXXXXXXX';
		$saldo = 0;
		if($query->num_rows() > 0){
			$salida .= $td1;
			$salida .= 'Formas de Pago</caption>';
			$salida .= "<tr bgcolor='#e7e3e7'><td>Forma de Pago</td><td align='center'>N&uacute;mero</td><td align='center'>Banco</td> <td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				$salida .= '<tr>';
				$salida .= '<td>'.$row['tipo'].'</td>';
				$salida .= '<td>'.$row['num_ref'].'</td>';
				$salida .= '<td>'.$row['banco'].'</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '</table></td>';
		}

		// Prestamos PRMO
		$mSQL = "SELECT if(observa2='',observa1,observa2) observa, monto FROM prmo WHERE transac=${dbtransac} AND clipro=${dbcod_cli} AND monto<>0";
		$query = $this->db->query($mSQL);
		$saldo = 0;
		if($query->num_rows() > 0){
			$salida .= $td1;
			$salida .= 'Prestamos</caption>';
			$salida .= "<tr bgcolor='#e7e3e7'><td>Observaci&oacute;n</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				$salida .= '<tr>';
				$salida .= '<td>'.$row['observa'].'</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '</table></td>';
		}

		//Cruce de Cuentas ITCRUC
		$mSQL = "
			SELECT b.tipo tipo, b.proveed codcp, MID(b.nombre,1,25) nombre, a.onumero, a.monto, b.numero, b.fecha
			FROM itcruc AS a JOIN cruc AS b ON a.numero=b.numero
			WHERE b.proveed=${dbcod_cli} AND b.transac=${dbtransac} AND a.onumero!=${dbtiponum}
			UNION ALL
			SELECT b.tipo tipo, b.cliente codcp, MID(b.nomcli,1,25) nombre, a.onumero, a.monto, b.numero, b.fecha
			FROM itcruc AS a JOIN cruc AS b ON a.numero=b.numero
			WHERE b.cliente=${dbcod_cli} AND b.transac=${dbtransac} ORDER BY onumero";
		$query = $this->db->query($mSQL);
		$saldo = 0;
		if($query->num_rows() > 0){
			$salida .= $td1;
			$salida .= 'Cruce de Cuentas</caption>';
			$salida .= "<tr bgcolor='#e7e3e7'><td>Nombre</td><td>C&oacute;digo</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				$salida .= '<tr>';
				$salida .= '<td>('.trim($row['tipo']).') '.$row['nombre']."</td>";
				$salida .= '<td>'.$row['codcp'].'</td>';
				$salida .= '<td>'.$row['onumero'].'</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '</table></td>';
		}

		//Cruces implicado

		$mSQL="SELECT e.numero,e.tipo, d.monto AS abono
			FROM `itcruc` AS d
			JOIN cruc AS e ON d.numero=e.numero
			WHERE e.tipo LIKE 'C%' AND e.proveed=${dbcod_cli} AND `d`.`onumero`= ${dbtiponum}
			UNION ALL
			SELECT e.numero,e.tipo, d.monto AS abono
			FROM `itcruc` AS d
			JOIN cruc AS e ON d.numero=e.numero
			WHERE e.tipo LIKE '%C' AND e.cliente=${dbcod_cli} AND `d`.`onumero`= ${dbtiponum}
			";
		$query = $this->db->query($mSQL);
		$saldo = 0;
		if($query->num_rows() > 0){
			$saldo = 0;
			$salida .= $td1;
			$salida .= 'Cruces implicado</caption>';
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				$saldo += $row['abono'];
				$salida .= '<tr>';
				$salida .= '<td>'.$row['tipo'].'</td>';
				$salida .= '<td>'.$row['numero'].'</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['abono']).'</td>';
				$salida .= '</tr>';
			}
			if ($saldo <> 0)
				$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'><b>Saldo : ".nformat($saldo). "</b></td></tr>";
			$salida .= '</table></td>';
		}
		echo $salida.'</tr></table>';
	}

	//******************************************************************
	//  Nota de credito a factura pagada
	//
	function ncfac(){
		$id = $this->uri->segment(4);
		$numero = '';
		$cod_cli = '';
		$nombre  = '';
		$monto   = 0.00;

		if ($id && $id!='insert') {
			$transac = $this->datasis->dameval("SELECT transac FROM sfac WHERE tipo_doc='F' AND numero=".$this->db->escape($id));
			$numero  = $this->datasis->dameval("SELECT numero  FROM sfac WHERE transac=".$transac);
			$cod_cli = $this->datasis->dameval("SELECT cod_cli FROM sfac WHERE transac=".$transac);
			$nombre  = $this->datasis->dameval("SELECT nombre  FROM sfac WHERE transac=".$transac);
			$monto   = $this->datasis->dameval("SELECT totalg  FROM sfac WHERE transac=".$transac);
		}
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");

			$("#num_ref").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscasfacdev').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#factura").val("");

									$("#nombre").val("");
									$("#nombre_val").text("");

									$("#cod_cli").val("");
									$("#cod_cli_val").text("");

									$("#sfacmonto").val("");
									$("#sfacmonto_val").text("");

								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
									add(sugiere);
								}
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#factura").attr("readonly", "readonly");
					$("#factura").val(ui.item.value);

					$("#nombre").val(ui.item.nombre);
					$("#nombre_val").text(ui.item.nombre);

					$("#cod_cli").val(ui.item.cod_cli);
					$("#cod_cli_val").text(ui.item.cod_cli);

					$("#sfacmonto").val(nformat(ui.item.totalg,2));
					$("#sfacmonto_val").text(nformat(ui.item.totalg,2));

					setTimeout(function() {  $("#factura").removeAttr("readonly"); }, 1500);
				}
			});
		});
		function limpiavacio(){ }
		';

		$edit = new DataEdit('', 'smov');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_ncfac_insert');
		$edit->post_process('update','_post_ncfac_update');
		$edit->post_process('delete','_post_ncfac_delete');
		$edit->pre_process( 'insert', '_pre_ncfac_insert');
		$edit->pre_process( 'update', '_pre_ncfac_update');
		$edit->pre_process( 'delete', '_pre_ncfac_delete');

		$edit->num_ref = new inputField('Factura','num_ref');
		//$edit->num_ref->rule='required|existesfac';
		$edit->num_ref->type= 'inputhidden';
		$edit->num_ref->insertValue=$numero;
		$edit->num_ref->size =10;
		$edit->num_ref->maxlength =8;
		$edit->num_ref->group='Detalles de la factura afectada';

		$edit->cod_cli = new inputField('Cliente','cod_cli');
		//$edit->cod_cli->rule='required|existescli';
		$edit->cod_cli->type = 'inputhidden';
		$edit->cod_cli->insertValue=$cod_cli;
		$edit->cod_cli->group='Detalles de la factura afectada';

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule= '';
		$edit->nombre->type= 'inputhidden';
		$edit->nombre->in  = 'cod_cli';
		$edit->nombre->insertValue=$nombre;
		$edit->nombre->group='Detalles de la factura afectada';

		$edit->sfacmonto = new inputField('Monto de la factura','sfacmonto');
		$edit->sfacmonto->type = 'inputhidden';
		$edit->sfacmonto->insertValue=$monto;
		$edit->sfacmonto->group='Detalles de la factura afectada';

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha|callback_chfuturo|required';
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->size =12;
		$edit->fecha->maxlength =8;
		$edit->fecha->readonly=true;
		$edit->fecha->calendar=false;

		$edit->monto = new inputField('Monto de la NC','monto');
		$edit->monto->rule='numeric|mayorcero|required';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =19;
		$edit->monto->maxlength =17;

		$edit->codigo = new  dropdownField('Motivo', 'codigo');
		$edit->codigo->option('','Seleccionar');
		$edit->codigo->options('SELECT TRIM(codigo) AS cod, nombre FROM botr WHERE tipo=\'C\' ORDER BY nombre');
		$edit->codigo->style='width:280px;';
		$edit->codigo->rule ='required';

		$edit->observa1 = new inputField('Observaci&oacute;n','observa1');
		$edit->observa1->rule='required|trim|strtoupper';
		$edit->observa1->size =52;
		//$edit->observa1->maxlength =50;

		//$edit->observa2 = new inputField('Observa2','observa2');
		//$edit->observa2->rule='';
		//$edit->observa2->size =52;
		//$edit->observa2->maxlength =50;

		$edit->tipo_ref = new autoUpdateField('tipo_ref','F','F');
		$edit->tipo_doc = new autoUpdateField('tipo_doc' ,'NC', 'NC');
		$edit->usuario  = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa  = new autoUpdateField('estampa',date('Ymd'), date('Ymd'));
		$edit->hora     = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));

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

	function _post_ncfac_insert($do){
		$numero = $do->get('numero');
		logusu('smov',"Nota de credito ${numero} creado");
	}

	function _post_ncfac_update($do){
		$numero = $do->get('numero');
		logusu('smov',"Nota de credito ${numero} modificado");
	}

	function _post_ncfac_delete($do){
		$numero = $do->get('numero');
		logusu('smov',"Nota de credito ${numero} eliminado");
	}

	function _pre_ncfac_insert($do){
		$fecha    = $do->get('fecha');
		$factura  = $do->get('num_ref');
		$dbfactura= $this->db->escape($factura);
		$codigo   = $do->get('codigo');
		$fecha    = date('Y-m-d');
		$do->rm_get('sfacmonto');
		$do->set('fecha'   ,$fecha);
		$do->set('fecha_op',$fecha);

		$monto  = floatval($do->get('monto'));

		$mSQL= 'SELECT transac,cod_cli,nombre,direc,dire1,totalg,iva,exento,tasa,reducida,sobretasa,montasa,monredu,monadic,numero FROM sfac WHERE tipo_doc=\'F\' AND numero='.$dbfactura;
		$row = $this->datasis->damerow($mSQL);
		if(empty($row)){
			$do->error_message_ar['pre_ins']='Factura inexistente.';
			return false;
		}
		if($monto>floatval($row['totalg'])){
			$do->error_message_ar['pre_ins']='No puede  hacer una nota de credito por un monto mayor al de la factura '.nformat($monto);
			return false;
		}
		$do->set('cod_cli', $row['cod_cli']);
		$do->set('nombre' , $row['nombre']);
		$do->set('dire1'  , $row['direc']);
		$do->set('dire2'  , $row['dire1']);

		$exento    =floatval($row['exento']);
		$tasa      =floatval($row['tasa']);
		$reducida  =floatval($row['reducida']);
		$sobretasa =floatval($row['sobretasa']);
		$montasa   =floatval($row['montasa']);
		$monredu   =floatval($row['monredu']);
		$monadic   =floatval($row['monadic']);
		$iva       =floatval($row['iva']);
		$totalg    =floatval($row['totalg']);
		$ttransac  =$row['transac'];
		$dbttransac=$this->db->escape($ttransac);

		$saldo=abs(floatval($this->datasis->dameval('SELECT monto-abonos FROM smov WHERE tipo_doc=\'FC\' AND numero='.$dbfactura.' AND transac='.$dbttransac)));
		if($saldo>0){
			$do->error_message_ar['pre_ins']='La factura seleccionada todavia tiene saldo disponible';
			return false;
		}
		$factor=$monto/$totalg;

		$do->set('abonos'   ,0);
		$do->set('vence'    ,$fecha);
		$do->set('exento'   ,round($factor*$exento   ,2));
		$do->set('tasa'     ,round($factor*$tasa     ,2));
		$do->set('reducida' ,round($factor*$reducida ,2));
		$do->set('sobretasa',round($factor*$sobretasa,2));
		$do->set('montasa'  ,round($factor*$montasa  ,2));
		$do->set('monredu'  ,round($factor*$monredu  ,2));
		$do->set('monadic'  ,round($factor*$monadic  ,2));
		$do->set('impuesto' ,round($factor*$iva      ,2));

		$transac  = $this->datasis->fprox_numero('ntransa');
		$control  = $this->datasis->fprox_numero('nsmov');
		$mnumnc   = $this->datasis->fprox_numero('nccli');
		$ningreso = $this->datasis->fprox_numero('ningreso');
		$do->set('transac' , $transac);
		$do->set('numero'  , $mnumnc);
		$do->set('vence'   , $fecha);
		$do->set('control' , $control);
		$do->set('ningreso', $ningreso);
		$do->set('ppago'   , 0);
		$do->set('reten'   , 0);
		$do->set('mora'    , 0);

		$dbcodigo = $this->db->escape($codigo);
		$do->set('descrip' ,$this->datasis->dameval("SELECT TRIM(nombre) AS val FROM botr WHERE codigo=${dbcodigo}"));

		return true;
	}

	function _pre_ncfac_update($do){
		$do->error_message_ar['pre_upd']='No es permitido modificar el registro';
		return false;
	}

	function _pre_ncfac_delete($do){
		$do->error_message_ar['pre_del']='';
		return false;
	}
	//*********************************
	//Fin Nota de credito a factura pagada
	//*********************************

	function dataprint($st,$uid){
		$this->rapyd->load('dataedit');

		$sfacforma=$this->datasis->traevalor('FORMATOSFAC');
		if(empty($sfacforma)) $sfacforma='descargar';

		$url=site_url('formatos/'.$sfacforma.'/CCLINC/'.$uid);
		$edit = new DataEdit('Imprimir Nota de Cr&eacute;dito', 'smov');
		//$id=$edit->get_from_dataobjetct('id');


		$edit->back_url = site_url('ajax/reccierraventana/N');

		$edit->back_save   = true;
		$edit->back_delete = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		//$edit->on_save_redirect   = false;

		$edit->post_process('update','_post_print_update');
		$edit->pre_process( 'insert','_pre_print_insert');
		$edit->pre_process( 'delete','_pre_print_delete');

		$edit->container = new containerField('impresion','La descarga se realizara en algunos segundos, en caso de no hacerlo haga click '.anchor('formatos/'.$sfacforma.'/CCLINC/'.$uid,'aqui'));

		$edit->tipo_doc = new inputField('Nota de Cr&eacute;dito','tipo_doc');
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

		//$edit->rifci = new inputField('Rif/Ci','rifci');
		//$edit->rifci->mode='autohide';

		$edit->nfiscal = new inputField('Nro. Control','nfiscal');
		$edit->nfiscal->rule='max_length[15]|strtoupper|required';
		$edit->nfiscal->size=16;
		$edit->nfiscal->maxlength =15;

		$fiscal=$this->datasis->traevalor('IMPFISCAL','Indica si se usa o no impresoras fiscales, esto activa opcion para cierre X y Z');
		if($fiscal=='S'){
			$edit->maqfiscal = new inputField('Serial m&aacute;quina f&iacute;scal','maqfiscal');
			$edit->maqfiscal->rule='max_length[15]|strtoupper|required';
			$edit->maqfiscal->size =16;
			$edit->maqfiscal->maxlength =15;
		}

		$edit->buttons('save', 'undo');
		$edit->build();

		if($st=='modify'){
			$script= '<script type="text/javascript" >
			$(function() {
				setTimeout(\'window.location="'.$url.'"\',100);
			});
			</script>';
		}else{
			$script='';
		}

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['script'] .= $script;
		$data['title']   = heading($this->tits);
		$this->load->view('view_ventanas_sola', $data);
	}

	function _post_print_update(){
		return false;
	}

	function _pre_print_insert(){
		return false;
	}

	function _pre_print_delete(){
		return false;
	}

	function giro(){

	}

	function instalar(){
		$campos=$this->db->list_fields('smov');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE `smov` DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE `smov` ADD UNIQUE INDEX `unico` (`cod_cli`, `tipo_doc`, `numero`, `fecha`)');
			$this->db->simple_query('ALTER TABLE `smov` ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		$itcampos=$this->db->list_fields('itccli');
		if(!in_array('id',$itcampos)){
			$mSQL="ALTER TABLE `itccli` ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `recriva`, ADD PRIMARY KEY (`id`)";
			$this->db->simple_query($mSQL);
		}
	}
}
