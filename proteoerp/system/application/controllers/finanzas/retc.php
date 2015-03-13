<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include('common.php');

class Retc extends Controller {
	var $mModulo = 'RETC';
	var $titp    = 'Modulo de Retenciones ISLR de clientes';
	var $tits    = 'Modulo de Retenciones ISLR de clientes';
	var $url     = 'finanzas/retc/';

	function Retc(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'RETC', $ventana=0, 'Retenciones de ISLR' );
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
			array('id'=>'fedita', 'title'=>'Agregar/Editar registro'),
			array('id'=>'fborra', 'title'=>'Eliminar registro'),
			array('id'=>'fshow' , 'title'=>'Mostrar registro')
		);

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function ltransac(el, val, opts){
			var link=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return link;
		};';

		$param['WestPanel']    = $WestPanel;
		//$param['script']       = script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('RETC', 'JQ');
		$param['otros']        = $this->datasis->otros('RECT', 'JQ');
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
		function retcadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function retcedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		$bodyscript .= '
		function retcshow() {
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				$.post("'.site_url($this->url.'dataedit/show').'/"+id,
					function(data){
						$("#fshow").html(data);
						$("#fshow").dialog( "open" );
					});
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
			}
		};';

		$bodyscript .= '
		function retcdel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea anular el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						$("#fborra").html(data);
						$("#fborra").dialog( "open" );
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
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
		$("#fedita").dialog({
			autoOpen: false, height: 570, width: 795, modal: true,
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
								//'.$this->datasis->jwinopen(site_url('formatos/ver/RETC').'/\'+json.pk.id+\'/id\'').';
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
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				$("#fshow").html("");
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		$("#fborra").dialog({
			autoOpen: false, height: 300, width: 300, modal: true,
			buttons: {
				"Aceptar": function() {
					$( this ).dialog( "close" );
					grid.trigger("reloadGrid");
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		jQuery("#imprime").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/RETC').'/\'+id+\'/id\'').';
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
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


		$grid->addField('emision');
		$grid->label('Emisi&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
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


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:200, maxlength: 200 }',
		));


		$grid->addField('rif');
		$grid->label('Rif');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:14, maxlength: 14 }',
		));


		//$grid->addField('stotal');
		//$grid->label('Stotal');
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


		$grid->addField('gtotal');
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


		$grid->addField('monto');
		$grid->label('Retenido');
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


		$grid->addField('origen');
		$grid->label('Origen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('codbanc');
		$grid->label('Codbanc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('tipo_op');
		$grid->label('Tipo_op');
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


		$grid->addField('sprmreinte');
		$grid->label('Sprmreinte');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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


		$grid->addField('operacion');
		$grid->label('Operacion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('anulado');
		$grid->label('Anulado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
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
					jQuery(gridId2).jqGrid("setGridParam",{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			},afterInsertRow:
			function( rid, aData, rowe){
				if(aData.anulado=="S"){
					$(this).jqGrid( "setCell", rid, "nrocomp","", {color:"#FFFFFF", background:"#FF2C14" });
				}
			}'
		);

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setEdit(false);
		$grid->setAdd(    $this->datasis->sidapuede('RETC','INCLUIR%' ));
		$grid->setDelete( $this->datasis->sidapuede('RETC','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('RETC','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: retcadd, editfunc: retcedit, delfunc: retcdel, viewfunc: retcshow');

		$grid->setAfterInsertRow('
			function( rid, aData, rowe){
				if(aData.anulado=="S"){
					$(this).jqGrid( "setCell", rid, "emision","", {color:"#FFFFFF", background:"#FF2C14" });
				}
			}'
		);

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
		$mWHERE = $grid->geneTopWhere('retc');

		$response   = $grid->getData('retc', array(array()), array(), false, $mWHERE, 'id','desc' );
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
			echo 'Deshabilitado';
		} elseif($oper == 'edit') {
			echo 'Deshabilitado';
		} elseif($oper == 'del') {
			echo 'Deshabilitado';
		};
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

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


		$grid->addField('idretc');
		$grid->label('Idretc');
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


		$grid->addField('tipo_doc');
		$grid->label('Tipo Doc.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
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
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('codigorete');
		$grid->label('Codigo Ret.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('nfiscal');
		$grid->label('N.Fiscal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
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


		$grid->addField('gtotal');
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


		$grid->addField('monto');
		$grid->label('Retenido');
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
		if($id == 0){
			$id = $this->datasis->dameval('SELECT MAX(id) FROM retc');
		}
		if(empty($id)) return '';
		$dbid = $this->db->escape($id);

		$orderby= '';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos = $this->db->list_fields('itretc');
			if(in_array($sidx,$campos)){
				$sidx = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid = $this->jqdatagrid;
		$mSQL = "SELECT * FROM itretc WHERE idretc=${dbid} ${orderby}";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setDatait(){
	}

	//***********************************
	// DataEdit
	//***********************************

	function dataedit(){
		$this->rapyd->load('datadetails','dataobject');

		$do = new DataObject('retc');
		//$do->pointer('scli' ,'scli.cliente=retc.cod_cli','sprv.tipo AS sprvtipo, sprv.reteiva AS sprvreteiva','left');
		$do->rel_one_to_many('itretc' ,'itretc' ,array('id'=>'idretc'));

		$edit = new DataDetails($this->tits, $do);
		$edit->on_save_redirect=false;
		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );


		$edit->emision = new dateField('F.Emisi&oacute;n','emision');
		$edit->emision->rule='chfecha|required';
		$edit->emision->size =12;
		$edit->emision->maxlength =8;
		$edit->emision->calendar=false;

		$edit->fecha = new dateField('F.Recepci&oacute;n','fecha');
		$edit->fecha->rule='chfecha|required';
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;
		$edit->fecha->maxlength =8;
		$edit->fecha->calendar=false;

		$edit->cod_cli = new inputField('Cliente','cod_cli');
		$edit->cod_cli->rule='max_length[5]|required|strtoupper|existescli';
		$edit->cod_cli->size =10;
		//$edit->cod_cli->maxlength =5;

		$edit->nombre = new hiddenField('Nombre','nombre');
		$edit->nombre->rule='max_length[200]';
		$edit->nombre->size =42;
		$edit->nombre->maxlength =200;

		$edit->rif = new hiddenField('RIF','rif');
		$edit->rif->rule='max_length[14]|strtoupper';
		$edit->rif->size =16;
		$edit->rif->maxlength =14;
		$edit->rif->autocomplete = false;

		$edit->operacion = new radiogroupField('Operaci&oacute;n', 'operacion', array('R'=>'Reintegrar','A'=>'Anticipo','P'=>'CxP'));
		$edit->operacion->insertValue='A';
		$edit->operacion->rule='required';

		$edit->stotal = new hiddenField('Sub-total','stotal');
		$edit->stotal->rule='max_length[15]|numeric';
		$edit->stotal->css_class='inputnum';
		$edit->stotal->size =17;
		$edit->stotal->maxlength =15;

		$edit->impuesto = new hiddenField('Impuesto','impuesto');
		$edit->impuesto->rule='max_length[15]|numeric';
		$edit->impuesto->css_class='inputnum';
		$edit->impuesto->size =17;
		$edit->impuesto->maxlength =15;

		$edit->gtotal = new hiddenField('Total de facturas','gtotal');
		$edit->gtotal->rule='max_length[15]|numeric';
		$edit->gtotal->css_class='inputnum';
		$edit->gtotal->size =17;
		$edit->gtotal->maxlength =15;

		$edit->monto = new hiddenField('Total Retenido','monto');
		$edit->monto->rule='max_length[15]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =17;
		$edit->monto->maxlength =15;

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->origen  = new autoUpdateField('origen' ,'R','R');

		$edit->modificado = new inputField('modificado','modificado');
		$edit->modificado->rule='max_length[8]';
		$edit->modificado->size =10;
		$edit->modificado->maxlength =8;

		//****************************
		//Inicio del Detalle
		//****************************
		$edit->it_tipo_doc = new hiddenField('tipo_doc','tipo_doc_<#i#>');
		$edit->it_tipo_doc->db_name='tipo_doc';
		$edit->it_tipo_doc->rule='max_length[2]|enum[NC,ND,F,D]|required';
		$edit->it_tipo_doc->size =4;
		$edit->it_tipo_doc->maxlength =1;
		$edit->it_tipo_doc->rel_id ='itretc';

		$edit->it_numero = new inputField('numero','numero_<#i#>');
		$edit->it_numero->db_name='numero';
		$edit->it_numero->rule='max_length[12]|required|callback_chrepetidos|callback_chfac[<#i#>]';
		$edit->it_numero->size =14;
		$edit->it_numero->maxlength =12;
		$edit->it_numero->title = 'Para mejorar la b&uacute;squeda coloque el tipo de documento seguido del n&uacute;mero, Ej D000001 si es una devoluci&oacute;n, F12345 si es una factura o NC0001 si una nota de cre&dacute;ito';
		$edit->it_numero->rel_id ='itretc';
		$edit->it_numero->autocomplete = false;

		$edit->it_codigorete = new dropdownField('','codigorete_<#i#>');
		$edit->it_codigorete->option('','Seleccionar');
		$edit->it_codigorete->options('SELECT TRIM(codigo) AS codigo,TRIM(CONCAT_WS("-",tipo,codigo,activida)) AS activida FROM rete ORDER BY tipo,codigo');
		$edit->it_codigorete->db_name='codigorete';
		$edit->it_codigorete->rule   ='required';
		$edit->it_codigorete->style  ='width: 300px';
		$edit->it_codigorete->rel_id ='itretc';
		$edit->it_codigorete->onchange='post_codigoreteselec(<#i#>,this.value)';

		$edit->it_base = new inputField('base','base_<#i#>');
		$edit->it_base->db_name='base';
		$edit->it_base->rule='max_length[15]|numeric';
		$edit->it_base->css_class='inputnum';
		$edit->it_base->size =12;
		$edit->it_base->maxlength =15;
		$edit->it_base->showformat ='decimal';
		$edit->it_base->rel_id ='itretc';
		$edit->it_base->onkeyup='importerete(<#i#>)';

		$edit->it_impuesto = new hiddenField('impuesto','impuesto_<#i#>');
		$edit->it_impuesto->db_name='impuesto';
		$edit->it_impuesto->rule='max_length[15]|numeric';
		$edit->it_impuesto->css_class='inputnum';
		$edit->it_impuesto->size =17;
		$edit->it_impuesto->maxlength =15;
		$edit->it_impuesto->showformat ='decimal';
		$edit->it_impuesto->rel_id ='itretc';

		$edit->it_gtotal = new hiddenField('gtotal','gtotal_<#i#>');
		$edit->it_gtotal->db_name='gtotal';
		$edit->it_gtotal->rule='max_length[15]|numeric';
		$edit->it_gtotal->css_class='inputnum';
		$edit->it_gtotal->size =17;
		$edit->it_gtotal->maxlength =15;
		$edit->it_gtotal->rel_id ='itretc';
		$edit->it_gtotal->showformat ='decimal';
		$edit->it_gtotal->autocomplete = false;

		$edit->it_monto = new inputField('monto','itmonto_<#i#>');
		$edit->it_monto->db_name='monto';
		$edit->it_monto->rule='max_length[15]|nocero|numeric';
		$edit->it_monto->css_class='inputnum';
		$edit->it_monto->size =12;
		$edit->it_monto->maxlength =15;
		$edit->it_monto->rel_id ='itretc';
		$edit->it_monto->onkeyup ='totalizar()';
		$edit->it_monto->autocomplete = false;
		$edit->it_monto->disable_paste= true;
		$edit->it_monto->showformat ='decimal';
		//****************************
		//Fin del Detalle
		//****************************

		$edit->codbanc = new dropdownField('Caja','codbanc');
		$edit->codbanc->option('','Seleccionar');
		$edit->codbanc->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' AND tbanco='CAJ' ORDER BY codbanc");
		$edit->codbanc->rule='max_length[5]|condi_required|callback_chcaja';
		$edit->codbanc->style='width:200px;';


		//$edit->buttons('save', 'undo','delete', 'back','add_rel','add');
		//$edit->buttons('save', 'undo', 'back','add_rel');
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
			$conten['form']  =& $edit;
			$this->load->view('view_retc', $conten);
		}
	}

	//*****************************
	// Metodos de chequeo
	//*****************************
	function chrepetidos($numero){
		if(!isset($this->ch_repetido)) $this->ch_repetido=array();

		if(array_search($numero,$this->ch_repetido)===false){
			$this->ch_repetido[]=$numero;
			return true;
		}else{
			$this->validation->set_message('chrepetidos', 'La factura '.$numero.' esta repetida');
			return false;
		}
	}

	function chcajero($cajero){
		$op=$this->input->post('operacion');
		if($op=='R' && empty($cajero)){
			$this->validation->set_message('chcajero', 'El campo %s es obligatorio cuando la operaci&oacute;n es reintegro');
			return false;
		}
		return true;
	}

	function chfac($numero,$ind){
		$cod_cli = $this->input->post('cod_cli');
		$tipo_doc= $this->input->post('tipo_doc_'.$ind);
		$fecha   = $this->input->post('fecha');

		if($tipo_doc=='NC' || $tipo_doc=='ND'){
			$ww=' WHERE numero='.$this->db->escape($numero).' AND cod_cli='.$this->db->escape($cod_cli).' AND tipo_doc='.$this->db->escape($tipo_doc);
			$mSQL='SELECT COUNT(*) FROM smov '.$ww;
		}else{
			$ww=' WHERE numero='.$this->db->escape($numero).' AND cod_cli='.$this->db->escape($cod_cli).' AND tipo_doc='.$this->db->escape($tipo_doc);
			$mSQL='SELECT COUNT(*) FROM sfac '.$ww;
		}
		$cana=$this->datasis->dameval($mSQL);

		if($cana!=1){
			$this->validation->set_message('chfac', 'El documento '.$numero.' no pertenece al cliente '.$cod_cli);
			return false;
		}

		return true;
	}

	function chcaja($caja){
		$op=$this->input->post('operacion');
		if($op=='R' && empty($caja)){
			$this->validation->set_message('chcaja', 'El campo %s es obligatorio cuando la operaci&oacute;n es reintegro');
			return false;
		}
		return true;
	}

	function chobligaban($val){
		$ban=$this->input->post('cargo');
		$tipo=common::_traetipo($ban);
		if($tipo!='CAJ'){
			if(empty($val)){
				$this->validation->set_message('chobligaban', 'El campo %s es obligatorio cuando el cargo es a un banco');
				return false;
			}
		}
		return true;
	}

	function chclave($clave){
		$cajero  = $this->input->post('cajero');
		$dbclave = $this->db->escape($clave);
		$dbcajero= $this->db->escape($cajero);
		$op=$this->input->post('operacion');

		if(empty($cajero) || $op!='R'){
			return true;
		}
		$ch    = $this->datasis->dameval("SELECT COUNT(*) FROM scaj WHERE cajero=${dbcajero} AND clave=${dbclave}");
		if($ch>0){
			return true;
		}
		$this->validation->set_message('chclave', 'Clave o cajeo inv&aacute;lido');
		return false;
	}

	function chdupli($numero){
		$scli=$this->input->post('cod_cli');
		$mSQL='SELECT COUNT(*) FROM retc WHERE nrocomp='.$this->db->escape($numero).' AND cod_cli='.$this->db->escape($scli);
		$cana=$this->datasis->dameval($mSQL);
		if($cana >0 ){
			$this->validation->set_message('chdupli', 'Ya existe un registro guardado con el mismo numero de comprobante y al mismo cliente.');
			return false;
		}
		return true;
	}

	//*****************************
	//Metodos para autocomplete
	//*****************************
	function buscasfac(){
		session_write_close();
		$mid   = trim($this->input->post('q'));

		$scli  = $this->input->post('scli');
		$sclidb= $this->db->escape($scli);

		$rete=0.75;
		$data = '{}';
		if(empty($scli)){
			$retArray[0]['label']   = 'Debe seleccionar un cliente primero';
			$retArray[0]['value']   = '';
			$retArray[0]['gtotal']  = 0;
			$retArray[0]['impuesto']= 0;
			$retArray[0]['fecha']   = '';
			$retArray[0]['tipo_doc']= '';
			$data = json_encode($retArray);
			echo $data;
			return;
		}
		if(!preg_match('/(?P<tipo>[a-zA-Z]+)?(?P<numero>\d+)/', $mid, $match)){
			$retArray[0]['label']   = 'Parametro de busqueda no valido';
			$retArray[0]['value']   = '';
			$retArray[0]['gtotal']  = 0;
			$retArray[0]['impuesto']= 0;
			$retArray[0]['fecha']   = '';
			$retArray[0]['tipo_doc']= '';
			$data = json_encode($retArray);
			echo $data;
			return;
		}

		if($mid !== false){
			$retArray = $retorno = array();

			if(!empty($match['tipo'])){
				$match['tipo'] = strtoupper($match['tipo']);
				if(strlen($match['tipo'])>1 && substr($match['tipo'],-1)=='M'){
					//Es una factura manual
					$match['tipo'] = substr($match['tipo'],0,-1);
				}
				$wwtipo  = ' AND a.tipo_doc='.$this->db->escape($match['tipo']);
				$smovtipo= '';
			}else{
				$wwtipo  = '';
				$smovtipo= ' AND a.tipo_doc IN (\'NC\',\'ND\')';
			}

			$dbnumero = $this->db->escape('%'.$match['numero'].'%');

			$mSQLs=array();
			if(empty($match['tipo']) || $match['tipo']=='F' || $match['tipo']=='D' || $match['tipo']=='T'){
				$mSQLs[] = "SELECT a.tipo_doc, a.numero, a.totalg, a.fecha,a.iva
				FROM  retc AS c
				JOIN itretc AS b ON c.id=b.idretc AND c.anulado='N'
				RIGHT JOIN sfac AS a ON a.tipo_doc=b.tipo_doc AND a.numero=b.numero
				WHERE a.cod_cli=${sclidb} AND a.numero LIKE ${dbnumero} ${wwtipo} AND b.numero IS NULL AND a.tipo_doc <> 'X'";
			}

			if(empty($match['tipo']) || $match['tipo']=='NC' || $match['tipo']=='ND'){
				$mSQLs[] = "SELECT a.tipo_doc, a.numero, a.monto AS totalg, a.fecha, a.impuesto AS iva
				FROM  retc AS c
				JOIN itretc AS b ON c.id=b.idretc AND c.anulado='N'
				RIGHT JOIN smov AS a ON a.tipo_doc=b.tipo_doc AND a.numero=b.numero
				LEFT  JOIN sfac AS d ON a.transac=d.transac
				WHERE a.cod_cli=${sclidb} AND a.numero LIKE ${dbnumero} ${wwtipo}
					AND b.numero IS NULL
					AND d.numero IS NULL
					AND a.observa1 NOT LIKE 'RET%'
					AND a.impuesto>0 ${smovtipo}";
			}
			if(count($mSQLs)>0){
				$mSQL = implode(' UNION ALL ',$mSQLs).' ORDER BY numero DESC LIMIT 10';
				$query = $this->db->query($mSQL);
				if ($query->num_rows() > 0){
					foreach( $query->result_array() as  $row ) {
						$retArray['label']   = $row['tipo_doc'].'-'.$row['numero'].' '.$row['totalg'].' Bs.';
						$retArray['value']   = $row['numero'];
						$retArray['gtotal']  = $row['totalg'];
						$retArray['impuesto']= $row['iva'];
						$retArray['fecha']   = dbdate_to_human($row['fecha']);
						$retArray['tipo_doc']= $row['tipo_doc'];

						array_push($retorno, $retArray);
					}
					$data = json_encode($retorno);
				}else{
					$retArray[0]['label']   = 'No se consiguieron efectos para aplicar';
					$retArray[0]['value']   = '';
					$retArray[0]['cod_cli'] = '';
					$retArray[0]['nombre']  = '';
					$retArray[0]['gtotal']  = 0;
					$retArray[0]['impuesto']= 0;
					$retArray[0]['fecha']   = '';
					$retArray[0]['tipo_doc']= '';

					$data = json_encode($retArray);
				}
			}
		}
		echo $data;
	}

	function buscascli(){
		$mid  = $this->input->post('q');
		$qmid = $this->db->escape($mid);
		$qdb  = $this->db->escape('%'.$mid.'%');

		$data = '{[ ]}';
		if($mid !== false){
			$retArray = $retorno = array();

			//Cheque si existe el codigo
			$mSQL="SELECT TRIM(nombre) AS nombre, TRIM(rifci) AS rifci, cliente, tipo
				FROM scli WHERE cliente=${qmid} LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() == 1){
				$row = $query->row_array();

				$retArray['value']   = $row['cliente'];
				$retArray['label']   = '('.$row['rifci'].') '.utf8_encode($row['nombre']);
				$retArray['rifci']   = $row['rifci'];
				$retArray['nombre']  = utf8_encode($row['nombre']);
				$retArray['cod_cli'] = $row['cliente'];
				$retArray['tipo']    = $row['tipo'];

				array_push($retorno, $retArray);
				$ww=" AND cliente<>${qmid}";
			}else{
				$ww='';
			}

			$mSQL="SELECT TRIM(nombre) AS nombre, TRIM(rifci) AS rifci, cliente, tipo
				FROM scli WHERE (cliente LIKE ${qdb} OR rifci LIKE ${qdb} OR nombre LIKE ${qdb}) $ww
				ORDER BY rifci LIMIT 10";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']   = $row['cliente'];
					$retArray['label']   = '('.$row['rifci'].') '.utf8_encode($row['nombre']);
					$retArray['rifci']   = $row['rifci'];
					$retArray['nombre']  = utf8_encode($row['nombre']);
					$retArray['cod_cli'] = $row['cliente'];
					$retArray['tipo']    = $row['tipo'];

					array_push($retorno, $retArray);
				}
			}
			if(count($data)>0)
				$data = json_encode($retorno);
		}
		echo $data;
		return true;
	}

	//*****************************
	//    Pre y Pos procesos
	//*****************************
	function _pre_insert($do){
		$estampa = $do->get('estampa');
		$hora    = $do->get('hora');
		$usuario = $do->get('usuario');
		$cod_cli = $do->get('cod_cli');
		$op      = $do->get('operacion');
		//Actualiza los datos del cliente
		$rrow=$this->datasis->damerow('SELECT nombre,rifci FROM scli WHERE cliente='.$this->db->escape($cod_cli));
		if($rrow!=false){
			$do->set('nombre',$rrow['nombre']);
			$do->set('rif'   ,$rrow['rifci']);
		}

		$stotal=$impuesto=$gtotal=$monto=0;

		//Borra la clave ya que solo se usa para comprobar
		$do->rm_get('clave');

		//Si no es un reintegro borra el cajero y caja, no se necesitan
		if($op!='R'){
			$do->rm_get('codbanc');
			$do->rm_get('numche');
			$do->rm_get('cajero');
		}else{
			$do->set('tipo_op','ND');
		}

		$rel='itretc';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$ittipo_doc   = $do->get_rel($rel, 'tipo_doc', $i);
			$itbase       = abs($do->get_rel($rel, 'base', $i));
			$itmonto      = abs($do->get_rel($rel, 'monto', $i));
			$itttnumero   = $do->get_rel($rel, 'numero'  , $i);
			$dbitnumero   = $this->db->escape($itttnumero );
			$dbittipo_doc = $this->db->escape($ittipo_doc);

			if($ittipo_doc=='F' || $ittipo_doc=='D'){
				$sql="SELECT nfiscal,totals,totalg,iva,fecha FROM sfac WHERE numero=${dbitnumero} AND tipo_doc=${dbittipo_doc}";
				$query = $this->db->query($sql);
				if ($query->num_rows() > 0){
					$row = $query->row();

					if($itbase>($row->totalg-$row->iva)){
						$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='No puede tener una base mayor al monto del efecto '.$ittipo_doc.$itttnumero.' ('.nformat($row->totalg-$row->iva).')';
						return false;
					}

					$do->set_rel($rel, 'fecha'   , $row->fecha  , $i);
					$do->set_rel($rel, 'nfiscal' , $row->nfiscal, $i);
					//$do->set_rel($rel, 'stotal ' , $row->totalg-$row->iva, $i);
					$do->set_rel($rel, 'monto'   , $itmonto     , $i);

					//Totales del encabezado
					$fac=($ittipo_doc=='D')? -1:1; //Para restar las devoluciones
					$impuesto += ($fac*$row->iva);
					$gtotal   += ($fac*$row->totalg);
					$monto    += ($fac*$itmonto);
				}
			}else{ //Para el caso en que sean notas de credito por algun otro concepto fuera de sfac
				$sql="SELECT nfiscal,monto AS totals, monto AS totalg,impuesto AS iva,fecha FROM smov WHERE numero=${dbitnumero} AND tipo_doc=${dbittipo_doc}";
				$query = $this->db->query($sql);
				if ($query->num_rows() > 0){
					$row = $query->row();

					if($itbase>($row->totalg-$row->iva)){
						$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='No puede tener una base mayor al monto del efecto '.$ittipo_doc.$itttnumero.' ('.nformat($row->totalg-$row->iva).')';
						return false;
					}

					$do->set_rel($rel, 'fecha'   , $row->fecha  , $i);
					$do->set_rel($rel, 'nfiscal' , $row->nfiscal, $i);
					$do->set_rel($rel, 'monto'   , $itmonto     , $i);

					//Totales del encabezado
					$fac=($ittipo_doc=='NC')? -1:1; //Para restar las devoluciones
					$impuesto += ($fac*$row->iva);
					$gtotal   += ($fac*$row->totalg);
					$monto    += ($fac*$itmonto);
				}
			}

			$do->set_rel($rel, 'estampa', $estampa, $i);
			$do->set_rel($rel, 'hora'   , $hora   , $i);
			$do->set_rel($rel, 'usuario', $usuario, $i);
		}

		$transac = $this->datasis->fprox_numero('ntransa');
		$do->set('transac', $transac);

		$rel='itretc';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$do->set_rel($rel, 'transac', $transac, $i);
		}


		$do->set('impuesto' ,$impuesto);
		$do->set('gtotal'   ,$gtotal);
		$do->set('stotal'   ,$gtotal-$impuesto);
		$do->set('monto'    ,$monto);

		return true;
	}

	function _pre_update($do){
		return false;
	}

	function _pre_delete($do){
		$id        = $do->get('id');
		$transac   = $do->get('transac');
		$sprmreinte= $do->get('sprmreinte');
		$error=0;

		$dbtransac= $this->db->escape($transac);
		$mSQL="SELECT a.cod_cli,a.nombre,a.tipo_doc,a.numero,a.fecha,a.monto,a.impuesto,a.abonos,a.vence,a.tipo_ref,a.num_ref,a.fecdoc,a.ningreso FROM smov AS a WHERE transac=${dbtransac}";
		$query = $this->db->query($mSQL);

		$rel='itretc';
		$restodat=array();
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$ittipo_doc = $do->get_rel($rel, 'tipo_doc', $i);
			$itnumero   = $do->get_rel($rel, 'numero', $i);
			$itfecha    = $do->get_rel($rel, 'fecha', $i);
			$restodat[$ittipo_doc.$itnumero]=$itfecha;
		}

		$sqls=array();
		//Reversa los cruces, anticipos y notas de debito
		foreach ($query->result() as $row){
			$dbcod_cli = $this->db->escape($row->cod_cli);
			$dbtipo_doc= $this->db->escape($row->tipo_doc);
			$dbnumero  = $this->db->escape($row->numero);
			$dbfecha   = $this->db->escape($row->fecha);
			$ww = "cod_cli=${dbcod_cli} AND tipo_doc=${dbtipo_doc} AND numero=${dbnumero} AND fecha=${dbfecha} AND transac=${dbtransac}";

			if($row->tipo_doc=='NC'){
				$mmSQL="SELECT numccli,tipoccli,monto FROM itccli WHERE numero=${dbnumero} AND  fecha=${dbfecha} AND tipo_doc='NC' AND cod_cli=${dbcod_cli} AND tipoccli='FC'";
				$qquery = $this->db->query($mmSQL);

				if ($qquery->num_rows() > 0){
					foreach ($qquery->result() as $rrow){
						$numero       = $rrow->numccli;
						$tipo_doc     = $rrow->tipoccli;
						$dbitnumero   = $this->db->escape($numero);
						$dbittipo_doc = $this->db->escape($tipo_doc);
						$itmonto      = $rrow->monto;
						$tiposfac     = 'FC';
						$iind         = substr($tipo_doc,0,1).$numero;
						if(isset($restodat[$iind])){
							$fecha        = $restodat[$iind];
							$dbfecha = $this->db->escape($fecha);

							$sqls[] = "UPDATE smov SET abonos=abonos-(${itmonto}) WHERE numero=${dbitnumero}  AND cod_cli=${dbcod_cli} AND tipo_doc='${tiposfac}' AND fecha=${dbfecha}";
						}else{
							$error++;
						}
					}
				}
			}elseif($row->tipo_doc=='AN'){
				//Chequea que los anticipos no se hallan aplicado
				if($row->abonos>0){
					$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Algunos de los movimientos asociados han sido aplicados, debe reversarlos antes de proceder';
					return false;
				}
			}elseif($row->tipo_doc=='ND'){
				//Chequea que las notas de debito no esten aplicadas
				if($row->abonos>0){
					$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Algunos de los movimientos asociados han sido aplicados, debe reversarlos antes de proceder';
					return false;
				}
			}
			$sqls[]="DELETE FROM smov WHERE ${ww}";
		}

		//Reversa la cuenta por pagar (Si la hubo)
		if(!empty($sprmreinte)){
			$dbsprmreinte=$this->db->escape($sprmreinte);
			//Chequea que no este abonado
			$abonos=$this->datasis->dameval("SELECT abonos FROM sprm WHERE numero=${dbsprmreinte} AND transac=$dbtransac");
			if($abonos>0){
				$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Algunos de los movimientos asociados han sido aplicados, debe reversarlos antes de proceder';
				return false;
			}
			$sqls[]="DELETE FROM sprm WHERE numero=${dbsprmreinte} AND transac=${dbtransac}";
		}

		//Reversa el reintegro (Si lo hubo)
		$mmSQL="SELECT codbanc,monto FROM bmov WHERE transac=${dbtransac} AND clipro='C'";
		$qquery = $this->db->query($mmSQL);

		$saldo=0;
		if ($qquery->num_rows() > 0){
			foreach ($qquery->result() as $rrow){
				$codbanc    = $rrow->codbanc;
				$sp_fecha   = date('Ymd');
				$monto      = $rrow->monto;
				$saldo     += $monto;
			}
			$sqls[]="DELETE FROM bmov WHERE transac=${dbtransac} AND clipro='C'";
		}
		$sqls[]="UPDATE retc SET anulado='S' WHERE id=".$this->db->escape($id);

		$sqls[]="DELETE FROM itccli WHERE transac=${dbtransac}";

		if($error==0){
			foreach($sqls as $sql){
				//echo "$sql \n".br();
				$ban=$this->db->simple_query($sql);
				if($ban==false){
					$error++;
					memowrite($sql,'retc');
				}
			}
			if($saldo>0){
				$this->datasis->actusal($codbanc, date('Ymd'), $saldo);
			}
		}

		if($error>0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Hubo problemas en la trasaccion, se generar&acute;n centinelas';
		}else{
			$primary =implode(',',$do->pk);
			logusu($do->table,"Anulo Retencion de cliente id: ${primary}");
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Retencion anulada';
		}
		return false;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		$error   = 0;
		$montan  = 0; //Monto para anticipar
		$sobrante= 0; //Monto sobrante para anticipar, reitegrar o pagar

		$transac   = $do->get('transac');
		$estampa   = $do->get('estampa');
		$hora      = $do->get('hora');
		$cod_cli   = $do->get('cod_cli');
		$nombre    = $do->get('nombre');
		$estampa   = $do->get('estampa');
		$usuario   = $do->get('usuario');
		$hora      = $do->get('hora');
		$operacion = $do->get('operacion');
		$id        = $do->get('id');
		$dbcod_cli = $this->db->escape($cod_cli);

		//$reinte  = $this->uri->segment($this->uri->total_segments());
		$efecha   = $do->get('emision');
		$fecha    = $do->get('fecha');
		$ex_fecha = explode('-',$fecha);
		$vence    = $ex_fecha[0].$ex_fecha[1].days_in_month($ex_fecha[1],$ex_fecha[0]);

		$mSQL = "DELETE FROM smov WHERE transac='${transac}'";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'RETC'); }

		$rel='itretc';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$ittipo_doc  = $do->get_rel($rel, 'tipo_doc', $i);
			$itnumero    = $do->get_rel($rel, 'numero'  , $i);
			$itmonto     = $do->get_rel($rel, 'monto'   , $i);
			$itfecha     = $do->get_rel($rel, 'fecha'   , $i);

			$dbitnumero   = $this->db->escape($itnumero);
			$dbittipo_doc = $this->db->escape($ittipo_doc);

			//Chequea que su origen sea sfac
			if($ittipo_doc=='F' || $ittipo_doc=='D'){
				$sql="SELECT referen,reiva,factura,cod_cli,nombre FROM sfac WHERE numero=${dbitnumero} AND tipo_doc=${dbittipo_doc}";
				$query = $this->db->query($sql);
				if($query->num_rows() > 0){
					$row = $query->row();

					$anterior  = $row->reiva;
					$itreferen = $row->referen;
					$itfactura = $row->factura;
				}

			}else{//En caso de provenir de smov
				if($ittipo_doc=='NC'){
					$itreferen = 'E';
				}else{
					$itreferen = 'C';
				}
			}

			//Chequea si es credito y si tiene saldo
			if($itreferen=='C'){
				$iittipo_doc   = ($ittipo_doc=='F')? 'FC' : $ittipo_doc;
				$dbiittipo_doc = $this->db->escape($iittipo_doc);
				$saldo = floatval($this->datasis->dameval("SELECT monto-abonos AS val FROM smov WHERE tipo_doc=${dbiittipo_doc} AND numero=${dbitnumero}"));
			}else{
				$saldo = 0;
			}

			//Si es una factura o una nota de debito por causa o no causa de una RP
			if($ittipo_doc == 'F' || $ittipo_doc=='ND'){
				//Si el saldo es 0  o menor que el monto retenido
				if($saldo==0 || $itmonto>$saldo){
					$sobrante+=$itmonto;
				}else{
					//Como tiene saldo suficiente crea una NC y la aplica a la FC

					$mnumnc = $this->datasis->fprox_numero('nccli');
					$data=array();
					$data['cod_cli']    = $cod_cli;
					$data['nombre']     = $nombre;
					$data['tipo_doc']   = 'NC';
					$data['numero']     = $mnumnc;
					$data['fecha']      = $fecha;
					$data['monto']      = $itmonto;
					$data['impuesto']   = 0;
					$data['abonos']     = $itmonto;
					$data['vence']      = $fecha;
					$data['tipo_ref']   = $ittipo_doc;
					$data['num_ref']    = $do->get_rel($rel,'numero',$i);
					$data['observa1']   = 'APLICACION DE RET/ISLR A FC'.$do->get_rel($rel,'numero',$i);
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['transac']    = $transac;
					$data['usuario']    = $usuario;
					$data['codigo']     = 'NOCON';
					$data['descrip']    = 'NOTA DE CONTABILIDAD';
					$data['fecdoc']     = $itfecha;

					$mSQL = $this->db->insert_string('smov', $data);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'retc'); }

					//Aplica la NC a la FC o ND segun sea el caso
					$data=array();
					$data['numccli']    = $itnumero;
					$data['tipoccli']   = ($ittipo_doc=='F')? 'FC' : 'ND';
					$data['cod_cli']    = $cod_cli;
					$data['tipo_doc']   = 'NC';
					$data['numero']     = $mnumnc;
					$data['fecha']      = $fecha;
					$data['monto']      = $itmonto;
					$data['abono']      = $itmonto;
					$data['ppago']      = 0;
					$data['reten']      = 0;
					$data['cambio']     = 0;
					$data['mora']       = 0;
					$data['transac']    = $transac;
					$data['estampa']    = $estampa;
					$data['hora']       = $hora;
					$data['usuario']    = $usuario;
					$data['reteiva']    = 0;

					$mSQL = $this->db->insert_string('itccli', $data);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'retc');}

					// Abona la factura
					$tiposfac = ($ittipo_doc=='F')? 'FC':'ND';
					$mSQL = "UPDATE smov SET abonos=abonos+$itmonto WHERE numero='$itnumero' AND cod_cli='$cod_cli' AND tipo_doc='$tiposfac'";
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'retc'); }
				}

				$mnumnd = $this->datasis->fprox_numero('ndcli');
				$data=array();
				$data['cod_cli']    = 'RETEN';
				$data['nombre']     = 'RETENCION DE I.S.L.R.';
				$data['tipo_doc']   = 'ND';
				$data['numero']     = $mnumnd;
				$data['fecha']      = $fecha;
				$data['monto']      = $itmonto;
				$data['impuesto']   = 0;
				$data['abonos']     = 0;
				$data['vence']      = $vence;
				$data['tipo_ref']   = 'FC';
				$data['num_ref']    = $do->get_rel($rel, 'numero'  , $i);
				$data['observa1']   = 'RET/ISLR DE '.$cod_cli.' A DOC. FC'.$do->get_rel($rel,'numero', $i);
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['transac']    = $transac;
				$data['usuario']    = $usuario;
				$data['codigo']     = 'NOCON';
				$data['descrip']    = 'NOTA DE CONTABILIDAD';


				$mSQL = $this->db->insert_string('smov', $data);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'RETC'); }
			}else{
			//Si es una devolucion
				// Devoluciones genera un ND al cliente
				$mnumnd = $this->datasis->fprox_numero('ndcli');
				$data=array();
				$data['cod_cli']    = $cod_cli;
				$data['nombre']     = $nombre;
				$data['tipo_doc']   = 'ND';
				$data['numero']     = $mnumnd;
				$data['fecha']      = $fecha;
				$data['monto']      = $itmonto;
				$data['impuesto']   = 0;
				$data['vence']      = $fecha;
				$data['tipo_ref']   = ($ittipo_doc=='F')? 'FC' : 'DV';
				$data['num_ref']    = $itnumero;
				$data['observa1']   = 'RET/ISLR DE '.$cod_cli.' A DOC. '.$ittipo_doc.$itnumero;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['transac']    = $transac;
				$data['usuario']    = $usuario;

				$mSQL = $this->db->insert_string('smov', $data);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'retc'); }

				//Devoluciones debe crear un NC si esta en el periodo
				$mnumnc = $this->datasis->fprox_numero('nccli');
				$data=array();
				$data['cod_cli']    = 'RETEN';
				$data['nombre']     = 'RETENCION DE I.S.L.R. POR COMPENSAR';
				$data['tipo_doc']   = 'NC';
				$data['numero']     = $mnumnc;
				$data['fecha']      = $fecha;
				$data['monto']      = $itmonto;
				$data['impuesto']   = 0;
				$data['abonos']     = 0;
				$data['vence']      = $fecha;
				$data['tipo_ref']   = ($ittipo_doc=='F')? 'FC' : ($ittipo_doc=='NC')? 'NC' : 'DV';
				$data['num_ref']    = $itnumero;
				$data['observa1']   = 'RET/ISLR DE '.$cod_cli.' A DOC.'.$ittipo_doc.$itnumero;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['transac']    = $transac;
				$data['usuario']    = $usuario;
				$data['codigo']     = 'NOCON';
				$data['descrip']    = 'NOTA DE CONTABILIDAD';

				$mSQL = $this->db->insert_string('smov', $data);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'retc'); }


				if($ittipo_doc <> 'NC'){
					//Aplica la NC a la ND si es posible a RETEN
					//$mnumnd; $fecha;
					$this->db->select(array('a.numero','a.fecha','a.monto - a.abonos AS saldo'));
					$this->db->from('smov   AS a');
					$this->db->join('itretc AS b' , 'a.transac=b.transac AND a.fecha=b.fecha AND a.num_ref=b.numero');
					$this->db->where('b.numero'   , $itfactura);
					$this->db->where('b.tipo_doc' , 'F');
					$this->db->where('a.tipo_doc' , 'ND');
					$this->db->where('a.cod_cli'  , 'RETEN');
					$qquery=$this->db->get();

					if ($qquery->num_rows() == 1){
						$rrrow = $qquery->row();
						if($rrrow->saldo >= $itmonto){
							$data=array();
							$data['numccli']    = $mnumnc;
							$data['tipoccli']   = 'NC';
							$data['cod_cli']    = $cod_cli;
							$data['tipo_doc']   = 'ND';
							$data['numero']     = $rrrow->numero;
							$data['fecha']      = $rrrow->fecha;
							$data['monto']      = $itmonto;
							$data['abono']      = $itmonto;
							$data['ppago']      = 0;
							$data['reten']      = 0;
							$data['cambio']     = 0;
							$data['mora']       = 0;
							$data['transac']    = $transac;
							$data['estampa']    = $estampa;
							$data['hora']       = $hora;
							$data['usuario']    = $usuario;
							$data['reteiva']    = 0;

							$mSQL = $this->db->insert_string('itccli', $data);
							$ban=$this->db->simple_query($mSQL);
							if($ban==false){ memowrite($mSQL,'retc');}

							//Abona la ND
							$dbfecha =$this->db->escape($rrrow->fecha);
							$dbnumero=$this->db->escape($rrrow->numero);
							$mSQL="UPDATE smov SET abonos=abonos+${itmonto}
							WHERE
							cod_cli ='RETEN' AND
							tipo_doc='ND' AND
							numero  = ${dbnumero} AND
							fecha   = ${dbfecha}";
							$ban=$this->db->simple_query($mSQL);
							if($ban==false){ memowrite($mSQL,'retc');}

							//Abona la NC
							$dbfecha =$this->db->escape($fecha);
							$dbnumero=$this->db->escape($mnumnc);
							$mSQL="UPDATE smov SET abonos=monto
							WHERE
							cod_cli ='RETEN' AND
							tipo_doc='NC' AND
							numero  = ${dbnumero} AND
							fecha   = ${dbfecha}";
							$ban=$this->db->simple_query($mSQL);
							if($ban==false){ memowrite($mSQL,'retc');}
						}
					}
				}
			}
		}

		//Chequea si es un reintegro para crear un solo egreso de caja
		//$totneto  = $do->get('reiva');
		if($sobrante>0){
			if($operacion=='A' && $sobrante>0){
				$mnumant = $this->datasis->fprox_numero('nancli');

				$data=array();
				$data['cod_cli']    = $cod_cli;
				$data['nombre']     = $nombre;
				$data['tipo_doc']   = 'AN';
				$data['numero']     = $mnumant;
				$data['fecha']      = $fecha;
				$data['monto']      = $sobrante;
				$data['impuesto']   = 0;
				$data['vence']      = $fecha;
				$data['tipo_ref']   = 'CR';
				$data['num_ref']    = '';
				$data['observa1']   = 'RET/ISLR DE '.$cod_cli;
				$data['usuario']    = $usuario;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['transac']    = $transac;
				$data['fecdoc']     = $fecha;

				$mSQL = $this->db->insert_string('smov', $data);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'RETC'); }

			}elseif($operacion=='R' && $sobrante>0){
				$codbanc  = $do->get('codbanc');
				$datacar  = common::_traebandata($codbanc);
				$sp_fecha = date('Ymd');
				$ttipo    = $datacar['tbanco'];
				$moneda   = $datacar['moneda'];
				$negreso  = $this->datasis->fprox_numero('negreso');

				if($ttipo=='CAJ'){
					$numeroch = $this->datasis->fprox_numero('ncaja'.$codbanc);
					$tipo_op= 'ND';
					$tipo1  = 'D' ;
				}else{
					//Pago con banco, falta implementar
					$numeroch = 'NUMERO CHEQUE';
					$tipo_op=  'CH';
					$tipo1  =  'C' ;
				}

				$data=array();
				$data['codbanc']    = $codbanc;
				$data['moneda']     = $moneda;
				$data['numcuent']   = $datacar['numcuent'];
				$data['banco']      = $datacar['banco'];
				$data['saldo']      = $datacar['saldo'];
				$data['tipo_op']    = $tipo_op;
				$data['numero']     = $numeroch;
				$data['fecha']      = date('Y-m-d');
				$data['clipro']     = 'C';
				$data['codcp']      = $cod_cli;
				$data['nombre']     = $nombre;
				$data['monto']      = $sobrante;
				$data['concepto']   = 'REINTEGRO RET/ISLR DE '.$cod_cli;
				$data['concep2']    = '';
				$data['benefi']     = '';
				$data['posdata']    = '';
				$data['abanco']     = '';
				$data['liable']     = ($ttipo=='CAJ') ? 'S': 'N';;
				$data['transac']    = $transac;
				$data['usuario']    = $usuario;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['anulado']    = 'N';
				$data['susti']      = '';
				$data['negreso']    = $negreso;

				$sql=$this->db->insert_string('bmov', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'retc'); $error++;}

				$this->datasis->actusal($codbanc, $sp_fecha, (-1)*$sobrante);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'retc'); $error++; }

			}elseif($operacion=='P' && $sobrante>0){ //Lo manda a cuenta por pagar

				$causado = $this->datasis->fprox_numero('ncausado');

				$mnsprm = $this->datasis->fprox_numero('num_nd');
				$data=array();
				$data['cod_prv']    = 'REINT';
				$data['nombre']     = 'REINTEGROS CLIENTES';
				$data['tipo_doc']   = 'ND';
				$data['numero']     = $mnsprm;
				$data['fecha']      = $fecha;
				$data['monto']      = $sobrante;
				$data['impuesto']   = 0;
				$data['abonos']     = 0;
				$data['vence']      = $fecha;
				$data['observa1']   = 'CARGO A CXC RET/ISLR ';
				$data['observa2']   = 'DEL CLIENTE '.$cod_cli;
				$data['tipo_ref']   = 'CR';
				$data['num_ref']    = '';
				$data['transac']    = $transac;
				$data['estampa']    = $estampa;
				$data['hora']       = $hora;
				$data['usuario']    = $usuario;
				$data['reteiva']    = 0;
				$data['montasa']    = 0;
				$data['monredu']    = 0;
				$data['monadic']    = 0;
				$data['tasa']       = 0;
				$data['reducida']   = 0;
				$data['sobretasa']  = 0;
				$data['exento']     = 0;
				$data['causado']    = $causado;
				$data['codigo']     = 'NOCON';
				$data['descrip']    = 'NOTA DE CONTABILIDAD';

				$sql=$this->db->insert_string('sprm', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'retc'); $error++;}

				$sql='UPDATE retc SET sprmreinte='.$this->db->escape($mnsprm).' WHERE id='.$this->db->escape($id);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'retc'); $error++;}
			}
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits ID ${primary}");

		return true;
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits ${primary}");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Anulo $this->tits ${primary}");
	}

	function instalar(){
		$this->datasis->creaintramenu(array('modulo'=>'52E','titulo'=>'Retenciones de ISLR Clientes','mensaje'=>'Registro de retenciones ISLR de clientes','panel'=>'CLIENTES','ejecutar'=>'finanzas/retc','target'=>'popu','visible'=>'S','pertenece'=>'5','ancho'=>900,'alto'=>600));

		if (!$this->db->table_exists('retc')) {
			$mSQL="CREATE TABLE `retc` (
				`id` INT(6) NOT NULL AUTO_INCREMENT,
				`emision` DATE NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`cod_cli` VARCHAR(5) NULL DEFAULT NULL,
				`nombre` VARCHAR(200) NULL DEFAULT NULL,
				`rif` VARCHAR(14) NULL DEFAULT NULL,
				`stotal` DECIMAL(15,2) NULL DEFAULT NULL,
				`impuesto` DECIMAL(15,2) NULL DEFAULT NULL,
				`gtotal` DECIMAL(15,2) NULL DEFAULT NULL,
				`monto` DECIMAL(15,2) NULL DEFAULT NULL,
				`estampa` DATE NULL DEFAULT NULL,
				`hora` CHAR(8) NULL DEFAULT NULL,
				`usuario` VARCHAR(12) NULL DEFAULT NULL,
				`modificado` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`transac` VARCHAR(8) NULL DEFAULT NULL,
				`origen` CHAR(1) NULL DEFAULT NULL,
				`codbanc` CHAR(2) NULL DEFAULT NULL,
				`tipo_op` CHAR(2) NULL DEFAULT NULL,
				`numche` VARCHAR(12) NULL DEFAULT NULL,
				`sprmreinte` VARCHAR(8) NULL DEFAULT NULL,
				`cajero` VARCHAR(5) NULL DEFAULT NULL,
				`operacion` CHAR(1) NOT NULL,
				`anulado` CHAR(1) NULL DEFAULT 'N',
				PRIMARY KEY (`id`),
				INDEX `modificado` (`modificado`),
				INDEX `nrocomp_cod_cli` (`cod_cli`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('itretc')) {
			$mSQL="CREATE TABLE `itretc` (
				`id` INT(6) NOT NULL AUTO_INCREMENT,
				`idretc` INT(6) NULL DEFAULT NULL,
				`tipo_doc` CHAR(2) NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`numero` VARCHAR(8) NULL DEFAULT NULL,
				`codigorete` VARCHAR(4) NULL DEFAULT NULL,
				`nfiscal` CHAR(12) NULL DEFAULT NULL,
				`base` DECIMAL(15,2) NULL DEFAULT NULL,
				`impuesto` DECIMAL(15,2) NULL DEFAULT NULL,
				`gtotal` DECIMAL(15,2) NULL DEFAULT NULL,
				`monto` DECIMAL(15,2) NULL DEFAULT NULL,
				`transac` CHAR(8) NULL DEFAULT NULL,
				`estampa` DATE NULL DEFAULT NULL,
				`hora` CHAR(8) NULL DEFAULT NULL,
				`usuario` CHAR(12) NULL DEFAULT NULL,
				`ffactura` DATE NULL DEFAULT '0000-00-00',
				`modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (`id`),
				INDEX `tipo_doc_numero` (`tipo_doc`, `numero`),
				INDEX `Numero` (`numero`),
				INDEX `modificado` (`modificado`),
				INDEX `rivatra` (`transac`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->simple_query($mSQL);
		}
	}

}
