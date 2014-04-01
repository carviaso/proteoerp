<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Otin extends Controller {
	var $mModulo = 'OTIN';
	var $titp    = 'OTROS INGRESOS';
	var $tits    = 'OTROS INGRESOS';
	var $url     = 'ventas/otin/';
	var $table   = 'otin';

	function Otin(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'OTIN', $ventana=0 );
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
		$grid->wbotonadd(array('id'=>'imprime',  'img'=>'assets/default/images/print.png','alt' => 'Reimprimir', 'label'=>'Reimprimir Documento'));
		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function ltransac(el, val, opts){
			var link=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return link;
		};';

		$param['WestPanel']    = $WestPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('OTIN', 'JQ');
		$param['otros']        = $this->datasis->otros('OTIN', 'JQ');
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
		function otinadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function otinedit(){
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
		function otinshow(){
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
		function otindel() {
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

		$bodyscript .= '
		function otinedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url('ventas/otin/dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

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
				if (id)	{
					var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
					window.open(\''.site_url($this->url.'printotin').'/\'+id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
				} else { $.prompt("<h1>Por favor Seleccione un registro</h1>");}
			});';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 500, width: 780, modal: true,
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
									'.$this->datasis->jwinopen(site_url($this->url.'printotin').'/\'+json.pk.id+\'/id\'').';
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

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('tipo_doc');
		$grid->label('Tipo.Doc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
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


		$grid->addField('orden');
		$grid->label('Orden');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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
			'width'         => 130,
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


		$grid->addField('direc');
		$grid->label('Direcci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('dire1');
		$grid->label('Direcci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('totals');
		$grid->label('Sub-total');
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
		$grid->label('Iva');
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
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('observa2');
		$grid->label('Observaci&oacute;n 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
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
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('nfiscal');
		$grid->label('N.fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('afecta');
		$grid->label('Afecta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('fafecta');
		$grid->label('Fafecta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
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


		$grid->addField('sucu');
		$grid->label('Sucursal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('depto');
		$grid->label('Depto.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 3 }',
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


		/*$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));*/


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					jQuery(gridId2).jqGrid("setGridParam",{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}'
		);
		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');
		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('OTIN','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('OTIN','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('OTIN','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('OTIN','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: otinadd, editfunc: otinedit, delfunc: otindel, viewfunc: otinshow");

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

	function printotin($id){
		$tipo=$this->datasis->dameval('SELECT tipo_doc FROM otin WHERE id='.$this->db->escape($id));

		if($tipo=='OT'){
			redirect('formatos/descargar/OTINOT/'.$id);
		}else{
			redirect($this->url.'dataprint/modify/'.$id);
		}
	}

	function dataprint($st,$uid){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Imprimir ingreso', 'otin');
		//$id=$edit->get_from_dataobjetct('id');

		$sfacforma=$this->datasis->traevalor('FORMATOSFAC','Especifica el metodo a ejecutar para descarga de formato de factura en Proteo Ej. descargartxt...');
		if(empty($sfacforma)) $sfacforma='descargar';
		$url=site_url('formatos/'.$sfacforma.'/OTINND/'.$uid);
		if(isset($this->back_url))
			$edit->back_url = site_url($this->back_url);
		else
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

		$edit->container = new containerField('impresion','La descarga se realizara en algunos segundos, en caso de no hacerlo haga click '.anchor('formatos/'.$sfacforma.'/OTINND/'.$uid,'aqui'));

		$edit->nfiscal = new inputField('N&uacute;mero f&iacute;scal','nfiscal');
		$edit->nfiscal->rule='max_length[12]|required';
		$edit->nfiscal->size =14;
		$edit->nfiscal->maxlength =12;
		$edit->nfiscal->autocomplete=false;

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
		$this->load->view('view_ventanas', $data);
	}

	function _pre_print_insert($do){ return false;}
	function _pre_print_delete($do){ return false;}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('otin');

		$response   = $grid->getData('otin', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM otin WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('otin', $data);
					echo "Registro Agregado";

					logusu('OTIN',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM otin WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM otin WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE otin SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("otin", $data);
				logusu('OTIN',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('otin', $data);
				logusu('OTIN',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM otin WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM otin WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM otin WHERE id=$id ");
				logusu('OTIN',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('tipo_doc');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
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


		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('precio');
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


		$grid->addField('transac');
		$grid->label('Transaci&oacute;n');
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
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('cantidad');
		$grid->label('Cantidad');
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


		$grid->addField('larga');
		$grid->label('Detalle');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
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
	function getdatait( $id = 0 ){
		if($id == 0 ){
			$id = $this->datasis->dameval('SELECT MAX(id) AS id FROM otin');
		}
		$dbid = intval($id);
		if(empty($dbid)) return '';
		$numero   = $this->datasis->dameval("SELECT numero FROM otin WHERE id=${dbid}");
		$dbnumero = $this->db->escape($numero);

		$orderby= '';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos = $this->db->list_fields('itotin');
			if(in_array($sidx,$campos)){
				$sidx   = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby= "ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM itotin WHERE numero=${dbnumero} ${orderby}";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setDatait(){
	}


	function dataedit(){
		$this->rapyd->load('datadetails','dataobject');

		$modbusp=array(
			'tabla'   =>'scli',
			'columnas'=> array(
				'cliente' =>'C&oacute;digo Cliente',
				'nombre'  =>'Nombre',
				'dire11'  =>'Direcci&oacute;n',
				'rifci'   =>'Rif/CI'
			),
			'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
			'retornar'=>array('cliente'=>'cod_cli'),
			'titulo'  =>'Buscar Cliente');

		$boton=$this->datasis->modbus($modbusp);

		 $modbus=array(
			'tabla'   =>'botr',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'nombre' =>'Descripci&oacute;n'
			),
			'filtro'  =>array('codigo' =>'C&oacute;digo','nombre'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','nombre'=>'descrip_<#i#>','nombre'=>'larga_<#i#>'),
			'p_uri'   =>array(4=>'<#i#>'),
			'where'   =>'tipo = "C"',
			'titulo'  =>'Buscar concepto');


		$do = new DataObject('otin');
		$do->rel_one_to_many('itotin', 'itotin', array('tipo_doc'=>'tipo_doc','numero'=>'numero'));
		$do->rel_one_to_many('sfpa'  , 'sfpa'  , array('numero','transac'));
		$do->pointer('scli' ,'scli.cliente=otin.cod_cli','scli.tipo AS sclitipo','left');
		$do->rel_pointer('itotin','botr','itotin.codigo=botr.codigo','botr.nombre AS botrnombre, botr.precio AS botrprecio, botr.iva AS botriva, botr.tipo AS botrtipo');

		$edit = new DataDetails('',$do);
		$edit->on_save_redirect=false;
		$edit->set_rel_title('sfpa'  ,'Forma de pago');
		$edit->set_rel_title('itotin','Concepto');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->tipo_doc = new dropdownField('Tipo', 'tipo_doc');
		$edit->tipo_doc->option('OT','Otro Ingreso');
		$edit->tipo_doc->option('OC','Otro Ingreso a Cr&eacute;dito');
		$edit->tipo_doc->option('ND','Nota de D&eacute;bito');
		$edit->tipo_doc->rule ='enum[ND,FC,OT]|required';
		$edit->tipo_doc->style='width:170px;';

		$edit->cajero= new dropdownField('Cajero', 'cajero');
		$edit->cajero->options('SELECT cajero,TRIM(nombre) AS nombre FROM scaj ORDER BY nombre');
		$edit->cajero->rule ='condi_required|callback_chobliga[OT]|cajerostatus';
		$edit->cajero->style='width:150px;';
		$edit->cajero->insertValue=$this->secu->getcajero();

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->rule= 'required';
		$edit->numero->mode= 'autohide';
		$edit->numero->maxlength=8;
		$edit->numero->when = array('show');

		$edit->fecha = new DateonlyField('Fecha', 'fecha');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->mode='autohide';
		$edit->fecha->rule='chfecha';
		$edit->fecha->size=12;
		$edit->fecha->calendar=false;

		$edit->vence = new DateonlyField('Vence', 'vence');
		$edit->vence->insertValue = date('Y-m-d');
		$edit->vence->rule='chfecha';
		$edit->vence->size = 12;
		$edit->vence->calendar=false;

		$edit->cliente = new inputField('Cliente'  , 'cod_cli');
		$edit->cliente->size = 10;
		$edit->cliente->rule='required|existescli';
		$edit->cliente->append($boton);

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 55;
		$edit->nombre->type = 'inputhidden';
		$edit->nombre->maxlength=40;

		$edit->rifci   = new inputField('RIF/CI'  , 'rifci');
		$edit->rifci->type      = 'inputhidden';
		$edit->rifci->size = 20;

		$edit->direc = new inputField('Direcci&oacute;n','direc');
		$edit->direc->type = 'inputhidden';
		$edit->direc->size = 55;

		$edit->dire1 = new inputField(' ','dire1');
		$edit->dire1->type = 'inputhidden';
		$edit->dire1->size = 55;

		$edit->iva  = new inputField('Impuesto', 'iva');
		$edit->iva->size     = 20;
		$edit->iva->rule     = 'numeric';
		$edit->iva->type     = 'inputhidden';
		$edit->iva->css_class= 'inputnum';
		$edit->iva->showformat ='decimal';

		$edit->totals  = new inputField('Sub-Total', 'totals');
		$edit->totals->size     = 20;
		$edit->totals->rule     = 'numeric';
		$edit->totals->type     = 'inputhidden';
		$edit->totals->css_class= 'inputnum';
		$edit->totals->showformat ='decimal';

		$edit->totalg  = new inputField('Total', 'totalg');
		$edit->totalg->size      = 20;
		$edit->totalg->rule      ='numeric';
		$edit->totalg->type      = 'inputhidden';
		$edit->totalg->css_class='inputnum';
		$edit->totalg->showformat ='decimal';

		$edit->observa1 = new inputField('Observaciones' , 'observa1');
		$edit->observa1->size = 40;

		$edit->observa2 = new inputField('Observaciones' , 'observa2');
		$edit->observa2->size = 40;

		$edit->orden  = new inputField('Orden','orden');
		$edit->orden->size = 12;

		$edit->dpto =  new dropdownField('Departamento', 'dpto');
		$edit->dpto->option('','Seleccionar');
		$edit->dpto->options('SELECT TRIM(depto) AS codigo, CONCAT_WS(\'-\',depto,TRIM(descrip)) AS label FROM dpto WHERE tipo=\'G\' ORDER BY depto');
		$edit->dpto->rule  ='required';
		$edit->dpto->style = 'width:100px';

		$edit->sucu =  new dropdownField('Sucursal', 'sucu');
		$edit->sucu->options('SELECT codigo,CONCAT(codigo,\'-\', sucursal) AS sucursal FROM sucu ORDER BY codigo');
		$edit->sucu->rule  ='required';
		$edit->sucu->style = 'width:100px';

		$edit->afecta = new inputField('Factura Afectada','afecta');
		$edit->afecta->rule='';
		$edit->afecta->size =10;
		$edit->afecta->maxlength =8;
		$edit->afecta->rule = 'condi_required|callback_chobliga[ND]';

		$edit->fafecta = new dateonlyField('Fecha de la factura afecta','fafecta');
		$edit->fafecta->rule='chfecha';
		$edit->fafecta->size =10;
		$edit->fafecta->maxlength =8;
		$edit->fafecta->calendar=false;
		$edit->fafecta->rule = 'condi_required|callback_chobliga[ND]';

		//******************************
		//Campos para el detalle
		//******************************
		$edit->codigo = new inputField('C&oacute;digo', 'codigo_<#i#>');
		$edit->codigo->size   = 10;
		$edit->codigo->db_name= 'codigo';
		$edit->codigo->rule   = 'required|existebotr';
		$edit->codigo->rel_id = 'itotin';
		$edit->codigo->append($this->datasis->p_modbus($modbus,'<#i#>'));

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip_<#i#>');
		$edit->descrip->size     = 30;
		$edit->descrip->db_name  = 'descrip';
		$edit->descrip->rel_id   = 'itotin';
		$edit->descrip->type     = 'inputhidden';
		//$edit->descrip->maxlength= 12;

		$edit->larga = new textareaField('', 'larga_<#i#>');
		$edit->larga->db_name  = 'larga';
		$edit->larga->rel_id   = 'itotin';
		$edit->larga->rows     = 2;
		$edit->larga->cols     = 35;

		$ivas=$this->datasis->ivaplica();
		$edit->tasaiva =  new dropdownField('IVA <#o#>', 'tasaiva_<#i#>');
		$edit->tasaiva->option($ivas['tasa']     ,$ivas['tasa'].'%');
		$edit->tasaiva->option($ivas['redutasa'] ,$ivas['redutasa'].'%');
		$edit->tasaiva->option($ivas['sobretasa'],$ivas['sobretasa'].'%');
		$edit->tasaiva->option('0','0.00%');
		$edit->tasaiva->db_name  ='tasaiva';
		$edit->tasaiva->rule     ='positive';
		$edit->tasaiva->style    ='30px';
		$edit->tasaiva->rel_id   ='itotin';
		$edit->tasaiva->onchange ='importe(<#i#>)';

		$edit->precio = new inputField('Precio', 'precio_<#i#>');
		$edit->precio->css_class = 'inputnum';
		$edit->precio->rule      = 'numeric|mayorcero';
		$edit->precio->size      = 10;
		$edit->precio->onkeyup   = 'importe(<#i#>)';
		$edit->precio->rel_id    = 'itotin';
		$edit->precio->showformat= 'decimal';
		$edit->precio->db_name   = 'precio';

		$edit->impuesto = new inputField('Impuesto', 'impuesto_<#i#>');
		$edit->impuesto->css_class = 'inputnum';
		$edit->impuesto->rule      = 'numeric';
		$edit->impuesto->size      = 6;
		$edit->impuesto->onkeyup   = 'importe(<#i#>)';
		$edit->impuesto->rel_id    = 'itotin';
		$edit->impuesto->showformat= 'decimal';
		$edit->impuesto->db_name   = 'impuesto';

		$edit->importe = new inputField('Total', 'importe_<#i#>');
		$edit->importe->db_name = 'importe';
		$edit->importe->rule    = 'numeric|mayorcero';
		$edit->importe->size    = 10;
		$edit->importe->type    = 'inputhidden';
		$edit->importe->rel_id  = 'itotin';
		$edit->importe->showformat ='decimal';
		$edit->importe->css_class  ='inputnum';
		//*******************************
		//fin de campos para detalle
		//*******************************

		//************************************************
		//fin de campos para detalle,inicio detalle2 sfpa
		//************************************************
		$edit->tipo = new  dropdownField('Tipo <#o#>', 'tipo_<#i#>');
		$edit->tipo->options('SELECT tipo, nombre FROM tarjeta WHERE activo=\'S\' ORDER BY nombre');
		$edit->tipo->db_name    = 'tipo';
		$edit->tipo->rel_id     = 'sfpa';
		$edit->tipo->insertValue= 'EF';
		$edit->tipo->style      = 'width:150px;';
		$edit->tipo->onchange   = 'sfpatipo(<#i#>)';
		//$edit->tipo->rule     = 'required';

		$edit->sfpafecha = new dateonlyField('Fecha','sfpafecha_<#i#>');
		$edit->sfpafecha->rel_id   = 'sfpa';
		$edit->sfpafecha->db_name  = 'fecha';
		$edit->sfpafecha->size     = 10;
		$edit->sfpafecha->maxlength= 8;
		$edit->sfpafecha->calendar = false;
		$edit->sfpafecha->rule     ='condi_required|callback_chtipo[<#i#>]';

		$edit->numref = new inputField('Numero <#o#>', 'num_ref_<#i#>');
		$edit->numref->size     = 12;
		$edit->numref->db_name  = 'num_ref';
		$edit->numref->rel_id   = 'sfpa';
		$edit->numref->rule     = 'condi_required|callback_chtipo[<#i#>]';

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
		$edit->banco->style  ='width:180px;';
		$edit->banco->rule   ='condi_required|callback_chtipo[<#i#>]';

		$edit->monto = new inputField('Monto <#o#>', 'monto_<#i#>');
		$edit->monto->db_name   = 'monto';
		$edit->monto->css_class = 'inputnum';
		$edit->monto->rel_id    = 'sfpa';
		$edit->monto->size      = 10;
		$edit->monto->rule      = 'condi_required|chpagopositivo[<#i#>]';
		$edit->monto->showformat='decimal';
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
			$conten['form'] =& $edit;
			$this->load->view('view_otin', $conten);
		}
	}

	function chobliga($val,$tipo){
		$tipo_doc=$this->input->post('tipo_doc');
		if($tipo_doc==$tipo && empty($val)){
			$this->validation->set_message('chobliga', 'El campo %s es obligatorio cuando el tipo es '.$tipo_doc);
			return false;
		}
		return true;
	}

	function dpto() {
		$this->rapyd->load('dataform');
		$campo='ccosto'.$this->uri->segment(4);
 		$script='
 		function pasar(){
			if($F("departa")!="-!-"){
				window.opener.document.getElementById("'.$campo.'").value = $F("departa");
				window.close();
			}else{
				alert("Debe elegir un departamento");
			}
		}';

		$form = new DataForm('');
		$form->script($script);

		$form->fdepar = new dropdownField("Departamento", "departa");
		$form->fdepar->option('-!-','Seleccion un departamento');
		$form->fdepar->options("SELECT depto,descrip FROM dpto WHERE tipo='G' ORDER BY descrip");
		$form->fdepar->onchange='pasar()';

		$form->build_form();

		$data['content'] =$form->output;
		$data['head']    =script('prototype.js').$this->rapyd->get_head();
		$data['title']   ='<h1>Seleccione un departamento</h1>';
		$this->load->view('view_detalle', $data);
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

	function chpagopositivo($val,$i){
		$val = floatval($val);
		$tipo=$this->input->post('tipo_doc');
		$this->validation->set_message('chpagopositivo', 'El campo %s debe ser positivo');

		if($tipo=='OT' && $val<=0){
			return false;
		}else{
			return true;
		}
	}

	function _pre_insert($do){
		$tipo_doc= $do->get('tipo_doc');
		$cajero  = $do->get('cajero');
		$cliente = $do->get('cod_cli');
		$fecha   = $do->get('fecha');
		$almacen = $this->secu->getalmacen();
		$usuario = $this->secu->usuario();
		$vd      = $this->secu->getvendedor();
		$estampa = date('Y-m-d');
		$hora    = date('H:i:s');

		$con=$this->db->query('SELECT tasa,redutasa,sobretasa FROM civa ORDER BY fecha desc LIMIT 1');
		if($con->num_rows() > 0){
			$t=$con->row('tasa');$rt=$con->row('redutasa');$st=$con->row('sobretasa');
		}else{
			$do->error_message_ar['pre_ins']='Debe cargar la tabla de IVA.';
			return false;
		}

		//Totaliza la factura
		$totals = $totalg = $iva = 0;
		$tasa=$montasa=$reducida=$monredu=$sobretasa=$monadic=$exento=0;
		$cana=$do->count_rel('itotin');
		for($i=0;$i<$cana;$i++){
			$itprecio   = $do->get_rel('itotin', 'precio'  , $i);
			$itimpuesto = $do->get_rel('itotin', 'impuesto', $i);
			$itiva      = $do->get_rel('itotin', 'tasaiva' , $i);
			$itdescrip  = trim($do->get_rel('itotin', 'descrip' , $i));
			$itlarga    = trim($do->get_rel('itotin', 'larga'   , $i));

			if($itiva-$t==0) {
				$tasa   +=$itimpuesto;
				$montasa+=$itprecio;
			}elseif($itiva-$rt==0) {
				$reducida+=$itimpuesto;
				$monredu +=$itprecio;
			}elseif($itiva-$st==0) {
				$sobretasa+=$itimpuesto;
				$monadic  +=$itprecio;
			}else{
				$exento += $itprecio;
			}
			$totals += $itprecio;
			$iva    += $itimpuesto;

			$do->set_rel('itotin','usuario'  ,$usuario   ,$i);
			$do->set_rel('itotin','estampa'  ,$estampa   ,$i);
			$do->set_rel('itotin','hora'     ,$hora      ,$i);

			$lend=strlen($itdescrip);
			if($itdescrip==substr($itlarga,0,$lend)){
				$do->set_rel('itotin', 'larga',substr($itlarga,$lend), $i);
			}

			$do->rel_rm_field('itotin','tasaiva',$i);//elimina el campo comodin
		}
		$totalg = $totals+$iva;
		//Fin de la totalizacion

		if($tipo_doc=='OT'){
			//Totaliza los pagos
			$sfpa=0;
			$cana=$do->count_rel('sfpa');
			for($i=0;$i<$cana;$i++){
				$sfpa_tipo = $do->get_rel('sfpa','tipo' ,$i);
				$sfpa_monto= $do->get_rel('sfpa','monto',$i);
				$do->set_rel('sfpa','cobrador',$cajero,$i);

				$sfpa+=$sfpa_monto;
			}
			$sfpa=round($sfpa,2);
			//Fin de la totalizacion del pago

			//Validaciones del pago
			if(abs($sfpa-$totalg)>0.02){
				$do->error_message_ar['pre_ins']='El monto del pago no coincide con el monto de la factura (Pago:'.$sfpa.', Factura:'.$totalg.')';
				return false;
			}
			//Fin de la validacion de pago

			//Valida que el cajero no este cerrado para la fecha
			$dbfecha = $this->db->escape($fecha);
			$mSQL = "SELECT COUNT(*) FROM rcaj WHERE fecha=${dbfecha} AND cajero=".$this->db->escape($cajero);
			$cana = $this->datasis->dameval($mSQL);
			if(!empty($cana)){
				$do->error_message_ar['pre_ins']="El cajero ${cajero} ya fue cerrado para la fecha en que se esta registrando este ingreso";
				return false;
			}
			//fin de la validacion del cajero
		}elseif($tipo_doc=='OC' || $tipo_doc=='ND'){
			$do->unset_rel('sfpa');
		}

		if($tipo_doc=='ND'){
			$numero = $this->datasis->fprox_numero('notind');
		}elseif($tipo_doc=='OC'){
			$numero = $this->datasis->fprox_numero('notif');
		}else{
			$numero = $this->datasis->fprox_numero('notiot');
		}

		$do->rm_get('cajero');

		$transac = $this->datasis->fprox_numero('ntransa');

		//Asigna campos a los detalles
		$cana=$do->count_rel('sfpa');
		for($i=0;$i<$cana;$i++){
			$sfpatipo  = $do->get_rel('sfpa','tipo'    ,$i);
			$sfpa_fecha= $do->get_rel('sfpa','fecha'   ,$i);

			if($sfpatipo=='EF')
				$do->set_rel('sfpa', 'fecha' , $fecha , $i);
			elseif(empty($sfpa_fecha)){
				$do->set_rel('sfpa', 'fecha' , $fecha , $i);
			}

			$do->set_rel('sfpa','num_ref'  ,'OTIN'.$numero,$i);
			$do->set_rel('sfpa','tipo_doc' ,$tipo_doc  ,$i);
			$do->set_rel('sfpa','cobrador' ,$cajero    ,$i);
			$do->set_rel('sfpa','transac'  ,$transac   ,$i);
			$do->set_rel('sfpa','vendedor' ,$vd        ,$i);
			$do->set_rel('sfpa','cod_cli'  ,$cliente   ,$i);
			$do->set_rel('sfpa','f_factura',$fecha     ,$i);
			$do->set_rel('sfpa','numero'   ,$numero    ,$i);
			$do->set_rel('sfpa','almacen'  ,$almacen   ,$i);
			$do->set_rel('sfpa','usuario'  ,$usuario   ,$i);
			$do->set_rel('sfpa','estampa'  ,$estampa   ,$i);
			$do->set_rel('sfpa','hora'     ,$hora      ,$i);
		}
		$cana=$do->count_rel('itotin');
		for($i=0;$i<$cana;$i++){
			$do->set_rel('itotin','transac' ,$transac ,$i);
			$do->set_rel('itotin','cantidad',1        ,$i);
		}
		//Fin de los campos del detalle

		$do->set('exento'   , $exento   );
		$do->set('tasa'     , $tasa     );
		$do->set('reducida' , $reducida );
		$do->set('sobretasa', $sobretasa);
		$do->set('montasa'  , $montasa  );
		$do->set('monredu'  , $monredu  );
		$do->set('monadic'  , $monadic  );
		$do->set('totalg'   , $totalg   );
		$do->set('totals'   , $totals   );
		$do->set('iva'      , $iva      );
		$do->set('numero'   , $numero   );
		$do->set('transac'  , $transac  );
		$do->set('usuario'  , $usuario  );
		$do->set('estampa'  , $estampa  );
		$do->set('hora'     , $hora     );

		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='No se puede  cambiar este documento, debe anularlo y volvero a registrar.';
		return false;
	}

	function _post_insert($do){
		$tipo_doc= $do->get('tipo_doc');
		$cod_cli = $do->get('cod_cli');
		$nombre  = $do->get('nombre');
		$numero  = $do->get('numero');
		$fecha   = $do->get('fecha');
		$monto   = $do->get('totalg');
		$estampa = $do->get('estampa');
		$hora    = $do->get('hora');
		$transac = $do->get('transac');
		$usuario = $do->get('usuario');
		$impuesto= $do->get('iva');
		$observa1= $do->get('observa1');
		$observa2= $do->get('observa2');

		if($tipo_doc=='ND' || $tipo_doc=='OC'){
			//Crea la CxC
			$data=array();
			$data['cod_cli']    = $cod_cli;
			$data['nombre']     = $nombre;
			$data['tipo_doc']   = 'ND';
			$data['numero']     = $numero;
			$data['fecha']      = $fecha;
			$data['monto']      = $monto;
			$data['impuesto']   = $impuesto;
			$data['abonos']     = 0;
			$data['vence']      = $fecha;
			$data['tipo_ref']   = '';
			$data['num_ref']    = '';
			$data['observa1']   = $observa1;
			$data['observa2']   = $observa2;
			$data['estampa']    = $estampa;
			$data['hora']       = $hora;
			$data['transac']    = $transac;
			$data['usuario']    = $usuario;
			$data['codigo']     = '';
			$data['descrip']    = '';

			$mSQL = $this->db->insert_string('smov', $data);
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'OTIN'); }
		}elseif($tipo_doc=='OT'){
			$cana=$do->count_rel('sfpa');
			for($i=0;$i<$cana;$i++){
				$monto = $do->get_rel('sfpa','monto',$i);
				$tipo  = $do->get_rel('sfpa','tipo' ,$i);
				$banco = $do->get_rel('sfpa','banco',$i);

				//Crea el movimiento en banco si aplica
				if($tipo == 'NC' || $tipo == 'DE'){
					$num_ref = $do->get_rel('sfpa','num_ref',$i);
					$ffecha  = $do->get_rel('sfpa','fecha'  ,$i);
					$dbbanco = $this->db->escape($banco);
					$rowban  = $this->datasis->damerow('SELECT numcuent,banco,saldo,moneda FROM banc WHERE codbanc='.$dbbanco);

					$data['codbanc']  = $banco;
					$data['numcuent'] = $rowban['numcuent'];
					$data['banco']    = $rowban['banco'];
					$data['saldo']    = $rowban['saldo'];
					$data['moneda']   = $rowban['moneda'];
					$data['tipo_op']  = $tipo;
					$data['numero']   = $num_ref;
					$data['fecha']    = $ffecha;
					$data['clipro']   = 'C';
					$data['codcp']    = $cod_cli;
					$data['nombre']   = $nombre;
					$data['monto']    = $monto;
					$data['concepto'] = $observa1;
					$data['concep2']  = $observa2;
					$data['benefi']   = '';
					$data['usuario']  = $usuario;
					$data['estampa']  = $estampa;
					$data['hora']     = $hora;
					$data['transac']  = $transac;
					$data['anulado']  = 'N';
					$data['liable']   = 'S';

					$mSQL = $this->db->insert_string('bmov', $data);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'OTIN'); }

					$this->datasis->actusal($banco, $ffecha, $monto);
				}
			}
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits numero:${tipo_doc}${numero} ${primary}");
	}

	function _pre_delete($do){
		$transac   = $do->get('transac');
		$fecha     = $do->get('fecha');
		$dbtransac = $this->db->escape($transac);
		$dbfecha   = $this->db->escape($fecha);

		$mSQL= 'SELECT COUNT(*) FROM smov WHERE abonos>0 AND transac='.$dbtransac;
		$cana= $this->datasis->dameval($mSQL);
		if(!empty($cana)){
			$do->error_message_ar['pre_del']='El registro tiene efectos abonado, debe reversar primero el abono antes de anular';
			return false;
		}

		$sfpa_cana=$do->count_rel('sfpa');
		for($i=0;$i<$sfpa_cana;$i++){
			$cajero  = $do->get_rel('sfpa','cobrador',$i);
			break;
		}

		if($sfpa_cana>0){
			$mSQL= 'SELECT COUNT(*) FROM bmov WHERE (concilia IS NOT NULL OR concilia<>\'0000-00-00\') AND transac='.$dbtransac;
			$cana= $this->datasis->dameval($mSQL);
			if(!empty($cana)){
				$do->error_message_ar['pre_del']='El registro tiene movimientos en bancos conciliados, debe reversar primero el abono antes de anular';
				return false;
			}

			$mSQL = "SELECT COUNT(*) FROM rcaj WHERE fecha=${dbfecha} AND cajero=".$this->db->escape($cajero);
			$cana = $this->datasis->dameval($mSQL);
			if(!empty($cana)){
				$do->error_message_ar['pre_del']="El cajero ${cajero} ya fue cerrado para la fecha del registro, debe revesar al cierre primero";
				return false;
			}
		}

		return true;
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits ${primary}");
	}

	function _post_delete($do){
		$tipo_doc = $do->get('tipo_doc');
		$numero   = $do->get('numero');
		$transac  = $do->get('transac');
		$codbanc  = $do->get('codbanc');
		$dbtransac= $this->db->escape($transac);

		$mSQL='SELECT codbanc,fecha,monto FROM bmov WHERE transac='.$dbtransac;
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $row){
			$fecha  = $row->fecha;
			$monto  = (-1)*$row->monto;
			$codbanc= $row->codbanc;
			$this->datasis->actusal($codbanc, $fecha, $monto);
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits numero:${tipo_doc}${numero} ${primary}");
	}

	function _post_print_update($do){
		$numero   = $do->get('numero');
		$tipo_doc = $do->get('tipo_doc');
		$nfiscal  = $do->get('nfiscal');

		logusu('otin',"Imprimio ${tipo_doc}${numero} factura ${nfiscal}");
	}

	function instalar(){
		$campos=$this->db->list_fields('otin');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE `otin` DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE `otin` ADD UNIQUE INDEX `numero` (`tipo_doc`, `numero`)');
			$this->db->simple_query('ALTER TABLE `otin` ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}
	}
}
