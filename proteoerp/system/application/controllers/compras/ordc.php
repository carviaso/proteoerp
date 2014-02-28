<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
class Ordc extends Controller {
	var $mModulo = 'ORDC';
	var $titp    = 'Orden de Compras';
	var $tits    = 'Orden de Compras';
	var $url     = 'compras/ordc/';
	var $chrepetidos = array();

	function Ordc(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'ORDC', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 950, 650, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		$readyLayout = $grid->readyLayout2( 212, 232, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'imprime', 'img'=>'assets/default/images/print.png', 'alt' => 'Reimprimir Documento', 'label'=>'Imprimir Orden'));
		$grid->wbotonadd(array('id'=>'imprim2', 'img'=>'assets/default/images/print.png', 'alt' => 'Reimprimir Documento', 'label'=>'Imprimir S/Precio'));
		$farma=$this->datasis->traevalor('IMPFISCAL','Indica si se usa o no impresoras fiscales, esto activa opcion para cierre X y Z');


		include(APPPATH.'config/database'.EXT);
		if(isset($db['farmax'])){
			$grid->wbotonadd(array('id'=>'efarmasis', 'img'=>'images/arrow_up.png', 'alt' => 'Enviar orden a FarmaSIS', 'label'=>'Enviar a FarmaSIS'));
		}
		//$grid->wbotonadd(array('id'=>'agregar',  'img'=>'images/agrega4.png' , 'alt' => 'Agregar'    , 'label'=>'Agregar Orden'       ));
		//$grid->wbotonadd(array('id'=>'modifica', 'img'=>'images/editar.png'  , 'alt' => 'Modificar'  , 'label'=>'Modificar Orden'    ));
		$WestPanel = $grid->deploywestp();

		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			//array('id'=>'fne'   ,  'title'=>'Agregar/Editar Orden de Compra'),
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('ORDC', 'JQ');
		$param['otros']        = $this->datasis->otros('ORDC', 'JQ');
		$param['centerpanel']  = $centerpanel;
		//$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0, $grid1 ){
		$bodyscript ='<script type="text/javascript">';

		$bodyscript .= '
		function ordcadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function ordcedit(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
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
		function ordcshow(){
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

		if($this->datasis->sidapuede('SCST','TODOS')){
			$bodyscript .= '
			function scstshow(id){
				$.post("'.site_url('compras/scst/solo/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog("open");
				});
			};';
		}else{
			$bodyscript .= '
			function scstshow(id){
				$.prompt("<h1>No tiene acceso a compras</h1>");
			};';
		}

		$bodyscript .= '
		function ordcdel() {
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

		$bodyscript .='
		jQuery("#efarmasis").click(function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = jQuery("#newapi'. $grid0.'").jqGrid(\'getRowData\',id);
				var url = "'.site_url($this->url.'enviafarmasis').'/"+ret.numero;
				$.get(url, function(data){
					$.prompt("<h1>"+data+"</h1>");
					jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
				});
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';

		$bodyscript .='
		jQuery("#imprime").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = jQuery("#newapi'. $grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('formatos/ver/ORDC').'/\'+id+"/", \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
			}else{ $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});';

		$bodyscript .='
		jQuery("#imprim2").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = jQuery("#newapi'. $grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('formatos/ver/ORDC').'/\'+id+"/S", \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
			}else{ $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
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
								if(json.status == "A"){
									apprise("Registro Guardado");
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									'.$this->datasis->jwinopen(site_url('formatos/ver/ORDC').'/\'+json.pk.id+\'/id\'').';
									if(typeof(bus_sug)=="object") bus_sug.close();
									return true;
								} else {
									apprise(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
							}
						}
					});
				},
				"Cancelar": function() {
					if(typeof(bus_sug)=="object") bus_sug.close();
					$("#fedita").html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				if(typeof(bus_sug)=="object") bus_sug.close();
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

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('status');
		$grid->label('Estatus');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 45,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
			'cellattr'      => 'function(rowId, tv, aData, cm, rdata){
				var tips = "";
				if(aData.status !== undefined){
					if(aData.status=="PE"){
						tips = "Pendiente";
					}else if(aData.status=="CE"){
						tips = "Cerrado";
					}else if(aData.status=="BA"){
						tips = "BackOrder";
					}else{
						tips = "Factura Guardada";
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

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

/*
		$grid->addField('almacen');
		$grid->label('Almacen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));
*/

		$grid->addField('proveed');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre Proveedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));


		$grid->addField('montotot');
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


		$grid->addField('montoiva');
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

		$grid->addField('montonet');
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

/*
		$grid->addField('montofac');
		$grid->label('Montofac');
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

		$grid->addField('condi');
		$grid->label('Condiciones');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('codban');
		$grid->label('Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));


		$grid->addField('tipo_op');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));


		$grid->addField('cheque');
		$grid->label('Cheque');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));


		$grid->addField('comprob');
		$grid->label('Comprobante');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 6 }',
		));

		$grid->addField('anticipo');
		$grid->label('Anticipo');
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
		$grid->addField('fechafac');
		$grid->label('Fechafac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('arribo');
		$grid->label('Arribo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
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


		$grid->addField('mdolar');
		$grid->label('Mdolar');
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

/*
		$grid->addField('benefi');
		$grid->label('Benefi');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));
*/

		$grid->addField('condi1');
		$grid->label('Condiciones 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));


		$grid->addField('condi2');
		$grid->label('Condiciones 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));


		$grid->addField('condi3');
		$grid->label('Condiciones 3');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));


		$grid->addField('transac');
		$grid->label('Transaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
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


		$grid->addField('hora');
		$grid->label('Hora');
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

		$grid->addField('cliente');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
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

		$grid->setOnSelectRow('function(id){
				if (id){
					var ret = $("#titulos").getRowData(id);
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
					$.ajax({
						url: "'.base_url().$this->url.'tabla/"+id,
						success: function(msg){
							$("#ladicional").html(msg);
						}
					});
				}
			},
			afterInsertRow:
			function( rid, aData, rowe){
				if ( aData.status == "PE"  ){
					$(this).jqGrid( "setCell", rid, "status", "", {color:"#FFFFFF", background:"#166D05" });
				} else if ( aData.status == "BA" ){
					$(this).jqGrid( "setCell", rid, "status", "", {color:"#000000", background:"#FCE40C" });
				} else {
					$(this).jqGrid( "setCell", rid, "status", "", {color:"#FFFFFF", background:"#06276B" });
				}
			}
		');

		$grid->setOndblClickRow('');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 460, height:280, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 460, height:280, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setBarOptions('addfunc: ordcadd, editfunc: ordcedit, delfunc: ordcdel, viewfunc: ordcshow');

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('ORDC','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('ORDC','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('ORDC','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('ORDC','BUSQUEDA%'));
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
		$mWHERE = $grid->geneTopWhere('ordc');

		$response   = $grid->getData('ordc', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

/*
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

		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));


		$grid->addField('proveed');
		$grid->label('Proveed');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('depo');
		$grid->label('Depo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));
*/

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 15 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 45 }',
		));


		$grid->addField('cantidad');
		$grid->label('Cantidad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('recibido');
		$grid->label('Recibido');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 70,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


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


		$grid->addField('importe');
		$grid->label('Importe');
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
			'width'         => 60,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('montoiva');
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

/*
		$grid->addField('control');
		$grid->label('Control');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));
*/

		$grid->addField('garantia');
		$grid->label('Garant&iacute;a');
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

/*
		$grid->addField('ultimo');
		$grid->label('Ultimo');
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


		$grid->addField('precio1');
		$grid->label('Precio1');
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


		$grid->addField('precio2');
		$grid->label('Precio2');
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


		$grid->addField('precio3');
		$grid->label('Precio3');
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
		$grid->label('Precio4');
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


		$grid->addField('transac');
		$grid->label('Transac');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
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


		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
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
*/

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('170');
		//$grid->setTitle($this->titp);
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){if (id){var ret = $("#titulos").getRowData(id);}},
			cellEdit: true,
			cellsubmit: "remote",
			cellurl: "'.site_url($this->url.'setdatait/').'"
		');
		$grid->setOndblClickRow('');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setRowNum(100);
		$grid->setShrinkToFit('false');

		#Set url
		//$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatait/'));

		if($deployed){
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdatait(){
		$id = $this->uri->segment(4);
		if($id == false){
			$id = $this->datasis->dameval("SELECT MAX(id) AS id FROM ordc");
		}
		$dbid=intval($id);
		$numero  = $this->datasis->dameval("SELECT numero FROM ordc WHERE id=${dbid}");
		$dbnumero= $this->db->escape($numero);

		$orderby= '';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos = $this->db->list_fields('itordc');
			if(in_array($sidx,$campos)){
				$sidx = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM itordc WHERE numero=${dbnumero} ${orderby}";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;

	}

	function filteredgrid(){
		echo 'Opcion Eliminada';
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descrip_<#i#>','pond'=>'costo_<#i#>','iva'=>'iva_<#i#>','peso'=>'sinvpeso_<#i#>'),
			'p_uri'=>array(4=>'<#i#>'),
			'script'  => array('post_modbus_sinv(<#i#>)'),
			'titulo'  =>'Buscar Art&iacute;culo',
			'where'   =>'activo = "S"');

		$sprvbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  => array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=> array('proveed'=>'proveed', 'nombre'=>'nombre'),
			'script'  => array('post_modbus_sprv()'),
			'titulo'  =>'Buscar Proveedor');


		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$do = new DataObject('ordc');
		$do->rel_one_to_many('itordc', 'itordc', 'numero');
		$do->pointer('sprv' ,'sprv.proveed=ordc.proveed','sprv.nombre AS sprvnombre','left');
		$do->rel_pointer('itordc','sinv','itordc.codigo=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');

		$edit = new DataDetails('Orden de Compra', $do);
		$edit->on_save_redirect=false;
		$edit->back_url = site_url('compras/ordc/filteredgrid');
		$edit->set_rel_title('itordc','Producto <#o#>');

		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 12;
		$edit->fecha->calendar = false;

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength=8;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->type      = 'inputhidden';
		$edit->peso->readonly  = true;
		$edit->peso->size      = 10;

		$edit->proveed = new inputField('Proveedor', 'proveed');
		$edit->proveed->size     = 7;
		$edit->proveed->maxlength= 5;
		$edit->proveed->autocomplete=false;
		$edit->proveed->rule     = 'required';
		$edit->proveed->append($this->datasis->modbus($sprvbus));

		$edit->nombre = new hiddenField('Nombre', 'nombre');
		$edit->nombre->size = 50;
		$edit->nombre->maxlength=40;

		$edit->status = new  dropdownField ('Estatus', 'status');
		$edit->status->option('','');
		$edit->status->option('PE','Pendiente');
		$edit->status->option('CE','Cerrado');
		$edit->status->option('BA','BackOrder');
		$edit->status->style='width:100px;';
		$edit->status->when=array('show');

		$edit->arribo = new DateonlyField('Fecha de Arribo', 'arribo','d/m/Y');
		$edit->arribo->insertValue = date('Y-m-d');
		$edit->arribo->rule = 'required';
		$edit->arribo->mode = 'autohide';
		$edit->arribo->size = 12;
		$edit->arribo->calendar = false;

		$edit->fechafac = new DateonlyField('Fecha Factura', 'fechafac','d/m/Y');
		$edit->fechafac->insertValue = date('Y-m-d');
		$edit->fechafac->rule = 'required';
		$edit->fechafac->mode = 'autohide';
		$edit->fechafac->size = 12;
		$edit->fechafac->calendar = false;
		$edit->fechafac->when=array('show');


		//**************************
		//  Campos para el detalle
		//**************************
		$edit->codigo = new inputField('C&oacute;digo', 'codigo_<#i#>');
		$edit->codigo->size=10;
		$edit->codigo->db_name='codigo';
		$edit->codigo->append($this->datasis->p_modbus($modbus,'<#i#>'));
		$edit->codigo->autocomplete=false;
		$edit->codigo->db_name  = 'codigo';
		$edit->codigo->rule     = 'required|callback_chcodigoa|callback_chrepetidos';
		$edit->codigo->rel_id   = 'itordc';

		$edit->descrip = new inputField('Descripci&oacute;n <#o#>', 'descrip_<#i#>');
		$edit->descrip->size=36;
		$edit->descrip->db_name='descrip';
		$edit->descrip->type = 'inputhidden';
		$edit->descrip->maxlength=50;
		$edit->descrip->readonly  = true;
		$edit->descrip->rel_id='itordc';

		//$edit->cantidad = new inputField('Cantidad <#o#>', 'cantidad_<#i#>');
		$edit->cantidad = new inputField('Cantidad', 'cantidad_<#i#>');
		$edit->cantidad->db_name      = 'cantidad';
		$edit->cantidad->css_class    = 'inputnum';
		$edit->cantidad->rel_id       = 'itordc';
		$edit->cantidad->maxlength    = 10;
		$edit->cantidad->size         =  8;
		$edit->cantidad->autocomplete = false;
		$edit->cantidad->onkeyup      = 'importe(<#i#>)';
		$edit->cantidad->rule         = 'required|positive';
		$edit->cantidad->showformat   = 'decimal';

		$edit->costo = new inputField('Costo', 'costo_<#i#>');
		$edit->costo->css_class       = 'inputnum';
		$edit->costo->rule            = 'required|positive';
		$edit->costo->onkeyup         = 'importe(<#i#>)';
		$edit->costo->size            = 10;
		$edit->costo->autocomplete    = false;
		$edit->costo->db_name         = 'costo';
		$edit->costo->rel_id          = 'itordc';
		$edit->costo->showformat      = 'decimal';

		$edit->importe = new inputField('Importe', 'importe_<#i#>');
		$edit->importe->db_name       = 'importe';
		$edit->importe->size          = 12;
		$edit->importe->rel_id        = 'itordc';
		$edit->importe->autocomplete  = false;
		$edit->importe->onkeyup       = 'costo(<#i#>)';
		$edit->importe->css_class     = 'inputnum';
		$edit->importe->showformat    = 'decimal';
		$edit->importe->readonly      = true;

		$edit->sinvpeso = new hiddenField('', 'sinvpeso_<#i#>');
		$edit->sinvpeso->db_name      = 'sinvpeso';
		$edit->sinvpeso->rel_id       = 'itordc';
		$edit->sinvpeso->pointer      = true;
		$edit->sinvpeso->showformat   = 'decimal';

		$edit->iva = new hiddenField('Impuesto', 'iva_<#i#>');
		$edit->iva->db_name           = 'iva';
		$edit->iva->rel_id            = 'itordc';
		$edit->iva->showformat        = 'decimal';

		for($i=1;$i<=4;$i++){
			$obj='precio'.$i;
			$edit->$obj = new hiddenField('Precio <#o#>', $obj.'_<#i#>');
			$edit->$obj->db_name   = 'sinv'.$obj;
			$edit->$obj->rel_id    = 'itordc';
			$edit->$obj->pointer   = true;
		}

		$edit->ultimo = new hiddenField('', 'ultimo_<#i#>');
		$edit->ultimo->db_name   = 'ultimo';
		$edit->ultimo->rel_id    = 'itordc';
		$edit->ultimo->pointer   = true;

		$edit->pond = new hiddenField('', 'pond_<#i#>');
		$edit->pond->db_name='pond';
		$edit->pond->rel_id ='itordc';
		$edit->pond->pointer= true;

		//**************************
		//fin de campos para detalle
		//**************************
		$edit->condi1 = new inputField('Condiciones', 'condi1');
		$edit->condi1->size      = 40;
		$edit->condi1->maxlength = 40;

		$edit->condi2 = new inputField('Condiciones', 'condi2');
		$edit->condi2->size      = 40;
		$edit->condi2->maxlength = 40;

		$edit->condi3 = new inputField('Condiciones', 'condi3');
		$edit->condi3->size      = 40;
		$edit->condi3->maxlength = 40;

		$edit->montoiva = new inputField('Impuesto', 'montoiva');
		$edit->montoiva->css_class ='inputnum';
		$edit->montoiva->type='inputhidden';
		$edit->montoiva->readonly  =true;
		$edit->montoiva->size      = 10;

		$edit->montotot = new inputField('Sub-Total', 'montotot');
		$edit->montotot->css_class ='inputnum';
		$edit->montotot->type='inputhidden';
		$edit->montotot->readonly  =true;
		$edit->montotot->size      = 10;

		$edit->montonet = new inputField('Monto Total', 'montonet');
		$edit->montonet->css_class ='inputnum';
		$edit->montonet->type='inputhidden';
		$edit->montonet->readonly  =true;
		$edit->montonet->size      = 10;

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		//$edit->buttons('modify', 'save', 'undo', 'delete', 'exit','add_rel');
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
			$conten['form']  =&  $edit;
			$this->load->view('view_ordc', $conten);
		}
	}

	function chcodigoa($codigo){
		$cana=$this->datasis->dameval('SELECT COUNT(*) FROM sinv WHERE activo=\'S\' AND codigo='.$this->db->escape($codigo));
		if(empty($cana) || $cana==0){
			$this->validation->set_message('chcodigoa', 'El campo %s contiene un codigo no v&aacute;lido o inactivo');
			return false;
		}
		return true;
	}

	function chrepetidos($cod){
		if(array_search($cod, $this->chrepetidos)===false){
			$this->chrepetidos[]=$cod;
			return true;
		}else{
			$this->validation->set_message('chrepetidos', 'El producto '.$cod.' esta repetido');
			return false;
		}
	}

	function bussug(){
		$this->rapyd->load('datagrid','datafilter','fields');
		//$uri   = anchor('compras/ordc/dataedit/show/<#codigo#>','<#codigo#>');
		$uri = '<a href="javascript:void(0);" class="articulo" onclick="oselect(<jse><#codigo#></jse>,<jse><#descrip#></jse>,<jse><#iva#></jse>,<jse><#peso#></jse>,<jse><#ultimo#></jse>,<jse><#sug#></jse>,<#id#>)"><#codigo#></a>';

		$script = '
		var vals = new Array();

		$(function(){
			$(".inputnum").numeric(".");
			$(\'input[name^="campo"]\').focus(function() {
				obj  = $(this);
				obj.select();
			});
			marcacod();
		});

		function marcacod(){
			vals = window.opener.$(\'input[name^="codigo_"]\').map(function(){return $(this).val().replace(/^\s+/g,\'\').replace(/\s+$/g,\'\'); }).get();

			var arr=$(".articulo");
			jQuery.each(arr, function(){
				codigo=$(this).text();
				$(this).css("color" ,"");
				$(this).attr("title","Haga click para agregarlo a la lista.");
				for (key in vals){
					if(vals[key] == codigo){
						$(this).css("color","red");
						$(this).attr("title", "Producto ya esta en la lista.");
						break;
					}
				}
			});

		}

		function oselect(codigo,descrip,iva,peso,costo,cantidad,idd){
			var sug = Number($("#campo_"+idd).val());
			if(sug>0){
				if(window.opener !== null){
					var id=window.opener.add_itordc();

					window.opener.document.getElementById("codigo_"+id).value  =codigo;
					window.opener.document.getElementById("descrip_"+id).value =descrip;
					window.opener.document.getElementById("iva_"+id).valuel    =iva;
					window.opener.document.getElementById("sinvpeso_"+id).value=peso;
					window.opener.document.getElementById("costo_"+id).value   =costo;
					window.opener.document.getElementById("cantidad_"+id).value=sug;
					window.opener.post_modbus_sinv(id);
					window.opener.importe(id);
					window.opener.totalizar();
					marcacod();
				}else{
					alert("Ventana de destino no existe");
				}
			}else{
				alert("La cantidad sugerida debe ser mayor a cero");
			}
			return false;
		}';

		function jse($string){
			$string=str_replace("\r",'',$string);
			$string=str_replace("\n",'',$string);
			$string=preg_replace('/\s\s+/', ' ', $string);
			$string=addslashes($string);
			$string=str_replace('<','\<',$string);
			$string=str_replace('>','\>',$string);
			$string=str_replace(';','\;',$string);
			$string=str_replace('\\"',"'+String.fromCharCode(34)+'",$string);
			$string='\''.$string.'\'';

			return $string;
		}

		function divi($dividendo,$divisor){
			if($divisor>0){
				return ceil($dividendo/$divisor);
			}else{
				return 0;
			}
		}

		$filter = new DataFilter('');
		$filter->script($script);

		$filter->db->select(array('a.id','SUM(b.cana) AS venta',
			'TRIM(a.codigo) AS codigo','a.descrip','a.exmax','a.exmin','a.existen','a.ultimo',
			'IF(a.exmax>a.existen,CEIL(a.exmax-IF(a.existen>0,a.existen,0)),0) AS sug',
			'a.pfecha1','a.prov1','a.peso','a.iva')
		);
		$filter->db->from('sinv AS a');
		$filter->db->join('sitems AS b','a.codigo=b.codigoa AND b.tipoa="F" AND b.fecha >= DATE_SUB(CURDATE(),INTERVAL 30 DAY)','left');
		//$filter->db->where('a.existen <= a.exmin');
		$filter->db->where('a.activo','S');
		$filter->db->where('a.tipo','Articulo');
		$filter->db->groupby('a.codigo');

		$filter->codigo = new inputField('C&oacute;digo','codigo');
		$filter->codigo->db_name   ='a.codigo';
		$filter->codigo->rule      ='max_length[15]';
		$filter->codigo->size      =10;
		$filter->codigo->maxlength =15;

		$filter->descrip = new inputField('Descripci&oacute;n','descrip');
		$filter->descrip->rule      ='max_length[45]';
		$filter->descrip->db_name   ='a.descrip';
		$filter->descrip->size      =47;
		$filter->descrip->maxlength =45;
		$filter->descrip->in = 'codigo';

		$filter->buttons('reset', 'search');
		$filter->build();

		$grid = new DataGrid();
		$grid->use_function('jse','divi');

		$grid->order_by('codigo','desc');
		$grid->per_page = 40;

		$campo = new inputField('Campo', 'sug');
		$campo->grid_name='campo_<#id#>';
		$campo->status   ='modify';
		$campo->size     =6;
		$campo->autocomplete=false;
		$campo->css_class   ='inputnum';
		$campo->disable_paste=true;

		$grid->column_orderby('C&oacute;digo',$uri ,'codigo');
		$grid->column_orderby('Descripci&oacute;n' ,'descrip' ,'descrip');
		$grid->column('M&aacute;x-Min'          ,'<nformat><#exmax#>|0</nformat><b>-</b><nformat><#exmin#>|0</nformat>', "align='center'");
		$grid->column_orderby('Existencia'         ,'<nformat><#existen#></nformat>' ,'existen' , "align='right'");
		//$grid->column('Sugerido'            ,'<b><nformat><#sug#>|0</nformat></b>'   , "align='right'");
		$grid->column('Sugerido'            ,$campo , "align='right'");
		$grid->column('&Uacute;ltimo costo' ,'<nformat><#ultimo#></nformat>', "align='right'");
		$grid->column('&Uacute;ltima compra','<dbdate_to_human><#pfecha1#></dbdate_to_human> <#prov1#>');
		$grid->column('Ventas S.','<nformat><divi><#venta#>|4</divi>|0</nformat>', "align='right'");
		$grid->column('Ventas Q.','<nformat><divi><#venta#>|2</divi>|0</nformat>', "align='right'");
		$grid->column('Ventas M.','<nformat><#venta#>|0</nformat>', "align='right'");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = heading('Productos sugueridos');
		$data['head']    = script('jquery.js');
		$data['head']   .= $this->rapyd->get_head();
		$data['head']   .= phpscript('nformat.js');
		$data['head']   .= script('plugins/jquery.numeric.pack.js');
		$data['head']   .= script('plugins/jquery.floatnumber.js');
		$this->load->view('view_ventanas', $data);
	}

	function _pre_insert($do){
		$iva=$totals=0;
		$cana=$do->count_rel('itordc');
		for($i=0;$i<$cana;$i++){
			$itcana    = $do->get_rel('itordc','cantidad',$i);
			$itpreca   = $do->get_rel('itordc','costo',$i);
			$itiva     = $do->get_rel('itordc','iva',$i);
			$itimporte = $itpreca*$itcana;
			$do->set_rel('itordc','importe' ,$itimporte,$i);

			$iva    +=$itimporte*($itiva/100);
			$totals +=$itimporte;
			//$do->set_rel('itspre','mostrado',$iva+$totals,$i);
		}
		$totalg = $totals+$iva;

		$do->set('montonet' ,round($totals ,2));
		$do->set('montotot' ,round($totalg ,2));
		$do->set('montoiva' ,round($iva    ,2));
		$do->set('status'   ,'PE');

		$numero =$this->datasis->fprox_numero('nordc');
		$transac=$this->datasis->fprox_numero('ntransa');
		$usuario=$do->get('usuario');
		$estampa=date('Ymd');
		$hora   =date('H:i:s');

		$do->set('estampa',$estampa);
		$do->set('hora'   ,$hora);
		$do->set('numero' ,$numero);
		$do->set('transac',$transac);

		for($i=0;$i<$cana;$i++){
			$do->set_rel('itordc','estampa' ,$estampa,$i);
			$do->set_rel('itordc','hora'    ,$hora   ,$i);
			$do->set_rel('itordc','transac' ,$transac,$i);
			$do->set_rel('itordc','usuario' ,$usuario,$i);;
		}
		return true;
	}

	function _pre_update($do){
		$numero   = $do->get('numero');
		$iva=$totals=0;
		$cana=$do->count_rel('itordc');
		for($i=0;$i<$cana;$i++){
			$itcodigo  = $do->get_rel('itordc','codigo'  ,$i);
			$itcana    = $do->get_rel('itordc','cantidad',$i);
			$itpreca   = $do->get_rel('itordc','costo'   ,$i);
			$itiva     = $do->get_rel('itordc','iva'     ,$i);
			$itimporte = $itpreca*$itcana;
			$do->set_rel('itordc','importe' ,$itimporte,$i);

			$iva    +=$itimporte*($itiva/100);
			$totals +=$itimporte;
			//$do->set_rel('itspre','mostrado',$iva+$totals,$i);
		}
		$totalg = $totals+$iva;

		$do->set('montonet' ,round($totals ,2));
		$do->set('montotot' ,round($totalg ,2));
		$do->set('montoiva' ,round($iva    ,2));

		$dbnumero = $this->db->escape($numero);
		$query = $this->db->query('SELECT cantidad, codigo FROM itordc WHERE numero='.$dbnumero);
		foreach ($query->result() as $row){
			$itcana   = $row->cantidad;
			$itcodigo = $row->codigo;
			$mSQL = "UPDATE sinv SET exord=exord-${itcana} WHERE codigo=".$this->db->escape($itcodigo);
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'ordc'); }
		}

		return true;
	}

	function _pre_delete($do){

		return true;
	}

	function _post_update($do){
		$codigo=$do->get('numero');

		$cana = $do->count_rel('itordc');
		for($i = 0;$i < $cana;$i++){
			$itcodigo= $do->get_rel('itordc', 'codigo'  ,$i);
			$itcana  = $do->get_rel('itordc', 'cantidad',$i);
			$mSQL = "UPDATE sinv SET exord=exord+${itcana} WHERE codigo=".$this->db->escape($itcodigo);

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'ordc'); }
		}

		logusu('ordc',"O.Compra ${codigo} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('numero');

		$cana = $do->count_rel('itordc');
		for($i = 0;$i < $cana;$i++){
			$itcodigo= $do->get_rel('itordc', 'codigo'  ,$i);
			$itcana  = $do->get_rel('itordc', 'cantidad',$i);
			$mSQL = "UPDATE sinv SET exord=exord-${itcana} WHERE codigo=".$this->db->escape($itcodigo);

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'ordc'); }
		}

		logusu('ordc',"O.Compra ${codigo} ELIMINADO");
	}

	function _post_insert($do){
		$codigo=$do->get('numero');

		$cana = $do->count_rel('itordc');
		for($i = 0;$i < $cana;$i++){
			$itcodigo= $do->get_rel('itordc', 'codigo'  ,$i);
			$itcana  = $do->get_rel('itordc', 'cantidad',$i);
			$mSQL = "UPDATE sinv SET exord=exord+${itcana} WHERE codigo=".$this->db->escape($itcodigo);

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'ordc'); }
		}

		logusu('ordc',"O.Compra ${codigo} CREADO");
	}

	function tabla(){
		$id      = $this->uri->segment($this->uri->total_segments());
		$dbid    = intval($id);
		$row     = $this->datasis->damerow("SELECT numero,transac  FROM ordc WHERE id=${dbid}");
		if(empty($row)){
			echo 'Registro no encontrado';
			return '';
		}
		$numero   = $row['numero'];
		$transac  = $row['transac'];
		$dbtransac= $this->db->escape($transac);

		$mSQL = "SELECT cod_prv, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos FROM sprm WHERE transac=${dbtransac} ORDER BY cod_prv";
		$query = $this->db->query($mSQL);
		$codprv = '';
		$salida = '';
		$saldo = 0;
		if($query->num_rows() > 0 ){
			$salida = '<br><table width=\'100%\' border=\'1\'>';
			$salida .= "<tr bgcolor='#e7e3e7'><td>Tp</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
			foreach($query->result_array() as $row){
				if($codprv != $row['cod_prv']){
					$codprv = $row['cod_prv'];
					$salida .= '<tr bgcolor=\'#c7d3c7\'>';
					$salida .= '<td colspan=\'4\'>'.trim($row['nombre']).'</td>';
					$salida .= '</tr>';
				}
				if($row['tipo_doc'] == 'FC'){
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= '<tr>';
				$salida .= '<td>'.$row['tipo_doc'].'</td>';
				$salida .= '<td>'.$row['numero'].  '</td>';
				$salida .= '<td align=\'right\'>'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '<tr bgcolor=\'#D7C3C7\'><td colspan=\'4\' align=\'center\'>Saldo : '.nformat($saldo).'</td></tr>';
			$salida .= '</table>';
		}

		$dbnumero = $this->db->escape($numero);
		$mSQL = "SELECT b.serie,b.fecha,b.tipo_doc,b.montonet AS monto,b.id FROM scstordc AS a JOIN scst AS b ON a.compra=b.control WHERE a.orden=${dbnumero} ORDER BY b.numero";
		$query = $this->db->query($mSQL);
		if($query->num_rows()>0){
			$salida  = '<br><table width=\'100%\' border=\'1\'>';
			$salida .= '<tr bgcolor=\'#E7E3E7\'><td align=\'center\' colspan=\'3\'><b>Compras relacionadas</b></td></tr>';
			$salida .= '<tr bgcolor=\'#E7E3E7\'><td>N&uacute;mero</td><td align=\'center\'>Fecha</td><td align=\'right\'>Monto</td></tr>';
			foreach($query->result_array() as $row){
				$salida .= '<tr>';
				$salida .= '<td><a href="javascript:scstshow(\''.$row['id'].'\')">'.$row['tipo_doc'].$row['serie'].'</a></td>';
				$salida .= '<td align=\'center\'>'.dbdate_to_human($row['fecha']).'</td>';
				$salida .= '<td align=\'right\' >'.nformat($row['monto']).'</td>';
				$salida .= '</tr>';
			}
			$salida .= '</table>';
		}

		echo $salida;
	}

	function _farmaurl($opt='farmax'){
		return Pedidos::_farmaurl($opt);
	}

	function enviafarmasis($numero){
		$dbnumero=str_pad(intval($numero), 8, '0', STR_PAD_LEFT);
		$status  = $this->datasis->dameval("SELECT status FROM ordc WHERE numero =${dbnumero}");
		//if($status!='PE'){
		//	echo 'La orden ya fue procesada.';
		//	return;
		//}
		require_once(APPPATH.'/controllers/farmacia/pedidos.php');

		$columnas = array('a.codigo', 'd.barras', 'b.descrip AS desca','a.cantidad AS pedir');
		$this->db->select($columnas);
		$this->db->from('itordc     AS a');
		$this->db->join('sinv       AS b','a.codigo=b.codigo');
		$this->db->join('farmaxasig AS d','a.codigo=d.abarras');
		$this->db->where('a.numero =',$dbnumero);
		$this->db->groupby('a.codigo');
		$this->db->distinct();
		$sql = $this->db->get();

		$_POST['apedir']=array();
		if($sql->num_rows() > 0){
			foreach ($sql->result() as $row){
				$_POST['apedir'][]=$row->barras.'#'.ceil($row->pedir);
			}
		}else{
			echo 'No existen productos';
		}

		$rt=Pedidos::_guardapedido();
		if($rt['error']==0){
			$this->db->simple_query("UPDATE ordc SET status='CE' WHERE numero =${dbnumero}");
			echo $rt['msj'];
		}else{
			echo $rt['msj'].' '.$rt['error'];
		}
	}

	function instalar(){
		$campos=$this->db->list_fields('ordc');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE ordc DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE ordc ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE ordc ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}
	}
}
