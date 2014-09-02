<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');

class Apan extends Controller {
	var $mModulo='APAN';
	var $titp='Aplicacion de Anticipos y NC';
	var $tits='Aplicacion de Anticipos y NC';
	var $url ='finanzas/apan/';

	function Apan(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'APAN', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 1024, 700, substr($this->url,0,-1) );
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

		$readyLayout = $grid->readyLayout2( 212	, 150, $param['grids'][0]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'fimprime', 'img'=>'assets/default/images/print.png','alt' => 'Formato PDF',      'label'=>'Reimprimir Documento'));
		$grid->wbotonadd(array('id'=>'fcliente', 'img'=>'images/agrega4.png' , 'alt' => 'Anticipo de Cliente'  , 'label'=>'Anticipo de Cliente'   ));
		$grid->wbotonadd(array('id'=>'fproveed', 'img'=>'images/agrega4.png' , 'alt' => 'Anticipo de Proveedor', 'label'=>'Anticipo de Proveedor' ));
		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$funciones = '
		function ltransac(el, val, opts){
			var link=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return link;
		};';

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('APAN', 'JQ');
		$param['otros']        = $this->datasis->otros('APAN', 'JQ');

		$param['centerpanel']  = $centerpanel;
		$param['funciones']    = $funciones;

		$param['temas']        = array('proteo','darkness','anexos1');

		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;

		$this->load->view('jqgrid/crud2',$param);
	}


	//******************************************************************
	//  Funciones de los Botones
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
		function apanadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function apanedit(){
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
		function apanshow(){
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
		function apandel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'decliente/do_delete').'/"+id, function(data){
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
				jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
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

		// Anticipo a Cliente
		$bodyscript .= '
		$("#fcliente").click( function() {
			$.post("'.site_url($this->url.'decliente/create').'",
			function(data){
				$("#fedita").dialog( {height: 450, width: 750, title: "Aplicacion de Anticipo a Cliente"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

		// Anticipo a Cliente
		$bodyscript .= '
		$("#fproveed").click( function() {
			$.post("'.site_url($this->url.'deproveed/create').'",
			function(data){
				$("#fedita").dialog( {height: 450, width: 750, title: "Aplicacion de Anticipo a Proveedor"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

		$bodyscript .= '
		jQuery("#fimprime").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('formatos/ver/APANCO/').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
			}else{ $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
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
									$("#fedita").dialog( "close" );
									grid.trigger("reloadGrid");
									'.$this->datasis->jwinopen(site_url('formatos/ver/APANCO').'/\'+json.pk.id+\'/id\'').';
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


	//******************************************************************
	//   Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
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


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));


		$grid->addField('clipro');
		$grid->label('Cli/Prv');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
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


		$grid->addField('reinte');
		$grid->label('Reintegro');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));


		$grid->addField('observa1');
		$grid->label('Observaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 50 }',
		));

/*
		$grid->addField('observa2');
		$grid->label('Observa2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 50 }',
		));
*/

		$grid->addField('transac');
		$grid->label('Transaci&oacute;n');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
			'formatter'     => 'ltransac'
		));


		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
		));

		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Modificado" }'
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
		$grid->setHeight('230');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}
		');
		$grid->setFormOptionsE('-');
		$grid->setFormOptionsA('-');
		$grid->setAfterSubmit('-');
		$grid->setOndblClickRow('');

		#show/hide navigations buttons
		$grid->setEdit(false);
		$grid->setAdd(false);
		$grid->setDelete(true);
		//$grid->setDelete($this->datasis->sidapuede('APAN','ELIMINA%'));
		$grid->setSearch($this->datasis->sidapuede('APAN','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		//$grid->setBarOptions("addfunc: apanadd, editfunc: apanedit, delfunc: apandel, viewfunc: apanshow");
		$grid->setBarOptions("delfunc: apandel");

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
		$mWHERE = $grid->geneTopWhere('apan');

		$response   = $grid->getData('apan', array(array()), array(), false, $mWHERE, 'id', 'desc' );
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

		//unset($data['oper']);
		//unset($data['id']);
		//if($oper == 'add'){
		//	if(false == empty($data)){
		//		$this->db->insert('apan', $data);
		//		echo 'Registro Agregado';
        //
		//		logusu('APAN','Registro ????? INCLUIDO');
		//	} else
		//	echo 'Fallo Agregado!!!';
        //
		//} elseif($oper == 'edit') {
		//	//unset($data['ubica']);
		//	$this->db->where('id', $id);
		//	$this->db->update('apan', $data);
		//	logusu('APAN','Registro ????? MODIFICADO');
		//	echo 'Registro Modificado';
        //
		//} elseif($oper == 'del') {
		//	//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM apan WHERE id='$id' ");
		//	if ($check > 0){
		//		echo ' El registro no puede ser eliminado; tiene movimiento ';
		//	} else {
		//		$this->db->simple_query("DELETE FROM apan WHERE id=$id");
		//		logusu('APAN','Registro ????? ELIMINADO');
		//		echo 'Registro Eliminado';
		//	}
		//};
	}

	//******************************************************************
	//Definicion del Grid del Item
	//
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('origen');
		$grid->label('Or&iacute;gen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));

		$grid->addField('anticipo');
		$grid->label('Anticipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
		));

		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
		));

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 90,
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


		$grid->addField('abono');
		$grid->label('Abono');
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


		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false',
			'hidden'        => 'true'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight(100);
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('-');
		$grid->setFormOptionsA('-');
		$grid->setAfterSubmit('-');
		$grid->setOndblClickRow('');


		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(100);
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

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function getdatait(){
		$id  = $this->uri->segment(4);
		$dbid= $this->db->escape($id);
		if ($id){
			$transac = $this->datasis->dameval('SELECT transac FROM apan WHERE id='.$dbid);
			$grid       = $this->jqdatagrid;
			$mSQL = "
				SELECT 'Cliente' origen, cod_cli, fecha, CONCAT(tipoccli,numccli) anticipo, CONCAT(tipo_doc, numero) numero, monto, abono, ppago, reten, reteiva, id
				FROM itccli WHERE transac='${transac}'
				UNION ALL
				SELECT 'Prveed' origen, cod_prv, fecha, CONCAT(tipoppro,numppro) anticipo, CONCAT(tipo_doc, numero) numero, monto, abono, ppago, reten, reteiva, id
				FROM itppro WHERE transac='${transac}'
			";

			$response   = $grid->getDataSimple($mSQL);
			$rs = $grid->jsonresult( $response);
		} else
			$rs ='';
		echo $rs;
	}


	//******************************************************************
	// Dataedit para todos
	//
	function _dataedit($edit){
		$this->rapyd->load('dataedit');

		$edit->build();

		$act = true;
		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
			$act = false;
		}

		if ($edit->on_error()){
			$rt=array(
				'status' =>'B',
				'mensaje'=>preg_replace('/<[^>]*>/', '', $edit->error_string),
				'pk'     =>null,
			);
			echo json_encode($rt);
			$act = false;
		}

		if($act){
			$conten['form'] =&  $edit;
			$this->load->view('view_apan', $conten);
		}
	}

	//******************************************************************
	// DataEdit Compartido
	//
	function _deapan(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});
		';

		$edit = new DataEdit($this->tits, 'apan');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		//$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule      = '';
		$edit->numero->size      = 10;
		$edit->numero->maxlength = 8;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule      = 'chfecha';
		$edit->fecha->size      = 10;
		$edit->fecha->maxlength = 8;
		$edit->fecha->calendar  = false;
		$edit->fecha->insertValue= date('Y-m-d');
		$edit->fecha->readonly  = true;

		$edit->tipo = new hiddenField('Tipo','tipo');
		$edit->tipo->rule      = '';
		$edit->tipo->size      = 3;
		$edit->tipo->maxlength = 1;

		$edit->clipro = new inputField('Clipro','clipro');
		$edit->clipro->rule      = 'required';
		$edit->clipro->size      = 7;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule      = '';
		$edit->nombre->size      = 30;
		$edit->nombre->maxlength = 30;
		$edit->nombre->type ='inputhidden';

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule      = 'numeric|mayorcero';
		$edit->monto->css_class = 'inputnum';
		$edit->monto->size      = 10;
		$edit->monto->maxlength = 17;

		$edit->preinte = new checkboxField('Reintegrar', 'preinte', 'S','N');
		$edit->preinte->insertValue = 'N';

		$edit->reinte = new inputField('Reinte','reinte');
		$edit->reinte->rule      = '';
		$edit->reinte->size      = 7;
		$edit->reinte->maxlength = 5;

		$edit->observa1 = new textareaField('Observa1','observa1');
		$edit->observa1->cols = 70;
		$edit->observa1->rows = 2;

		//$edit->observa1 = new textField('Observa1','observa1');
		//$edit->observa1->rule      = '';
		//$edit->observa1->size      = 52;
		//$edit->observa1->maxlength = 50;

		/*$edit->observa2 = new inputField('Observa2','observa2');
		$edit->observa2->rule      = '';
		$edit->observa2->size      = 52;
		$edit->observa2->maxlength = 50;*/

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		return $edit;
	}


	//******************************************************************
	// Anticipo de Cliente
	//
	function decliente(){
		$this->rapyd->load('dataedit');
		$edit = $this->_deapan();

		$script= '
		$(function() {
			$("#clipro").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscascli').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#clipro").val("");

									$("#nombre").val("");
									$("#nombre_val").text("");

									$("#saldo_val").text("");
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
					$("#clipro").attr("readonly", "readonly");

					$("#nombre").val(ui.item.nombre);
					$("#nombre_val").text(ui.item.nombre);

					setTimeout(function() {  $("#clipro").removeAttr("readonly"); }, 1500);

					truncate();

					$.ajax({
						url: "'.site_url('ajax/buscasmovapan/annc').'",
						dataType: "json",
						type: "POST",
						data: {"scli" : ui.item.value},
						success: function(data){
							$.each(data,
								function(id, val){
									can= add_itannc();

									$("#itnumero_" +can).val(val.numero);
									$("#ittipo_"   +can).val(val.tipo_doc);
									$("#itfecha_"  +can).val(val.fecha);
									$("#itsaldo_"  +can).val(val.saldo);
									$("#itid_"     +can).val(val.id);
									$("#itnumero_" +can+"_val").text(val.tipo_doc+val.numero);
									$("#itfecha_"  +can+"_val").text(val.fecha);
									$("#itsaldo_"  +can+"_val").text(nformat(val.saldo,2));

									$("#itmonto_"+can ).focus(function(){
										var valor = $(this).val();
										if(valor=="" || valor=="0" || valor=="0.0" || valor=="0.00"){
											$(this).val(val.saldo);
											totaliza();
										}
										$(this).select();
										cnota();
									});
								}
							);
						},
					});

					$.ajax({
						url: "'.site_url('ajax/buscasmovapan/fcndgi').'",
						dataType: "json",
						type: "POST",
						data: {"scli" : ui.item.value},
						success: function(data){
								$.each(data,
									function(id, val){
										can= add_itefec();

										$("#itenumero_" +can).val(val.numero);
										$("#itetipo_"   +can).val(val.tipo_doc);
										$("#itefecha_"  +can).val(val.fecha);
										$("#itesaldo_"  +can).val(val.saldo);
										$("#itemonto_"  +can).val(val.monto);
										$("#iteid_"     +can).val(val.id);
										$("#itenumero_" +can+"_val").text(val.tipo_doc+val.numero);
										$("#itefecha_"  +can+"_val").text(val.fecha);
										$("#itesaldo_"  +can+"_val").text(nformat(val.saldo,2));
										$("#itemonto_"  +can+"_val").text(nformat(val.monto,2));

										$("#iteaplicar_"+can ).focus(function(){
											totaliza();
											var valor  = $(this).val();
											var nvalor = 0;
											var saldo  = Number(val.saldo);
											var aplica = totalefe();
											var monto  = Number($("#monto").val());
											if(valor=="" || valor=="0" || valor=="0.0" || valor=="0.00"){
												nvalor = Number(valor);
												if(monto >= aplica+saldo){
													$(this).val(val.saldo);
												}else{
													if(monto-aplica==0){
														$(this).val("");
													}else{
														$(this).val(roundNumber(monto-aplica,2));
													}
												}
											}else{
												if(aplica>monto){
													$(this).val("");
												}
											}
											$(this).select();
											cnota();
										});
									}
								);
							},
					});

				}
			});


			$("#reinte").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscasprv').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#reinte").val("");
									$("#reinte_val").text("");
								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
								}
								add(sugiere);
							},
					});
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#reinte").attr("readonly", "readonly");
					$("#reinte_val").text(ui.item.nombre);
					setTimeout(function(){ $("#reinte").removeAttr("readonly"); }, 1500);
				}
			});

		});';

		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->clipro->label = 'Cliente';

		$edit->tipo = new autoUpdateField('tipo','C','C');

		$this->_dataedit($edit);

	}

	//******************************************************************
	// Anticipo de Proveedor
	//
	function deproveed(){
		$this->rapyd->load('dataedit');
		$edit = $this->_deapan();

		$script= '
		$(function() {
			$("#clipro").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscasprv').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#proveed").val("");

									$("#nombre").val("");
									$("#nombre_val").text("");

									$("#saldo_val").text("");
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
					$("#proveed").attr("readonly", "readonly");

					$("#nombre").val(ui.item.nombre);
					$("#nombre_val").text(ui.item.nombre);

					setTimeout(function() {  $("#proveed").removeAttr("readonly"); }, 1500);

					truncate();

					$.ajax({
						url: "'.site_url('ajax/buscasprmapan/annc').'",
						dataType: "json",
						type: "POST",
						data: {"sprv" : ui.item.value},
						success: function(data){
								$.each(data,
									function(id, val){
										can= add_itannc();

										$("#itnumero_" +can).val(val.numero);
										$("#ittipo_"   +can).val(val.tipo_doc);
										$("#itfecha_"  +can).val(val.fecha);
										$("#itsaldo_"  +can).val(val.saldo);
										$("#itid_"     +can).val(val.id);
										$("#itnumero_" +can+"_val").text(val.tipo_doc+val.numero);
										$("#itfecha_"  +can+"_val").text(val.fecha);
										$("#itsaldo_"  +can+"_val").text(nformat(val.saldo,2));

										$("#itmonto_"+can ).focus(function(){
											var valor = $(this).val();
											if(valor=="" || valor=="0" || valor=="0.0" || valor=="0.00"){
												$(this).val(val.saldo);
												totaliza();
											}
											$(this).select();
											cnota();
										});
									}
								);
							},
					});

					$.ajax({
						url: "'.site_url('ajax/buscasprmapan/fcndgi').'",
						dataType: "json",
						type: "POST",
						data: {"sprv" : ui.item.value},
						success: function(data){
								$.each(data,
									function(id, val){
										can= add_itefec();

										$("#itenumero_" +can).val(val.numero);
										$("#itetipo_"   +can).val(val.tipo_doc);
										$("#itefecha_"  +can).val(val.fecha);
										$("#itemonto_"  +can).val(val.monto);
										$("#itesaldo_"  +can).val(val.saldo);
										$("#iteid_"     +can).val(val.id);
										$("#itenumero_" +can+"_val").text(val.tipo_doc+val.numero);
										$("#itefecha_"  +can+"_val").text(val.fecha);
										$("#itesaldo_"  +can+"_val").text(nformat(val.saldo,2));
										$("#itemonto_"  +can+"_val").text(nformat(val.monto,2));

										$("#iteaplicar_"+can ).focus(function(){
											totaliza();
											var valor  = $(this).val();
											var nvalor = 0;
											var aplica = totalefe();
											var monto  = Number($("#monto").val());
											var saldo  = Number(val.saldo);
											if(valor=="" || valor=="0" || valor=="0.0" || valor=="0.00"){
												nvalor = Number(valor);
												if(monto >= aplica+saldo){
													$(this).val(val.saldo);
												}else{
													if(monto-aplica==0){
														$(this).val("");
													}else{
														$(this).val(roundNumber(monto-aplica,2));
													}
												}
											}else{
												if(aplica>monto){
													$(this).val("");
												}
											}
											$(this).select();
											cnota();
										});
									}
								);
							},
					});
				}
			});

			$("#reinte").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscascli').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#reinte").val("");
									$("#reinte_val").text("");
								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
								}
								add(sugiere);
							},
					});
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#reinte").attr("readonly", "readonly");
					$("#reinte_val").text(ui.item.nombre);
					setTimeout(function(){ $("#reinte").removeAttr("readonly"); }, 1500);
				}
			});

		});';

		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->clipro->label = 'Proveedor';

		$edit->tipo = new autoUpdateField('tipo','P','P');

		$this->_dataedit($edit);

	}

	function _pre_insert($do){
		$ttipo   = $do->get('tipo');
		$clipro  = $do->get('clipro');
		$estampa = $do->get('estampa');
		$hora    = $do->get('hora');
		$usuario = $do->get('usuario');
		$reinte  = $do->get('reinte');
		$observa = $do->get('observa1');
		$dbclipro= $this->db->escape($clipro);

		$preinte = $this->input->post('preinte');
		$do->rm_get('preinte');

		//Calcula los movimientos aplicables
		$arr_apl = array();
		$i=$aplicar=0;
		while(true){
			$ind = 'itnumero_'.$i; $numero = $this->input->post($ind);
			$ind = 'ittipo_'.$i;   $tipo   = $this->input->post($ind);
			$ind = 'itfecha_'.$i;  $fecha  = $this->input->post($ind);

			if($numero === false || $tipo === false || $fecha === false ){
				break;
			}
			if(empty($numero)|| empty($tipo) || empty($fecha)){
				break;
			}

			$ind = 'itsaldo_'.$i;  $itsaldo= floatval($this->input->post($ind));
			$ind = 'itmonto_'.$i;  $monto  = $this->input->post($ind);
			$ind = 'itid_'.$i;     $id     = intval($this->input->post($ind));

			if($ttipo=='C'){
				$rsaldo = floatval($this->datasis->dameval("SELECT monto-abonos AS saldo FROM smov WHERE id=${id}"));
			}else{
				$rsaldo = floatval($this->datasis->dameval("SELECT monto-abonos AS saldo FROM sprm WHERE id=${id}"));
			}
			if(!empty($monto)){
				if(!is_numeric($monto)){
					$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'El monto del efecto '.$tipo.$numero.' no es num&eacute;rico.';
					return false;
					break;
				}

				$monto = floatval($monto);
				if($monto > $rsaldo){
					$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'El monto del efecto '.$tipo.$numero.' es mayor al saldo disponible '.nformat($rsaldo).'.';
					return false;
					break;
				}

				$aplicar += $monto;
				$arr_apl[] = array('id'=>$id,'numero'=>$numero,'tipo'=>$tipo,'fecha'=>$fecha,'monto'=>$monto);
			}
			$i++;
		}
		//Fin de los aplicables


		$arr_efe=array();
		//Chequea si se reintegra
		if($preinte!='S'){
			//Calcula los efectos a los que se aplica

			$i=$efectos=0;
			while(true){
				$ind = 'itenumero_'.$i; $numero = $this->input->post($ind);
				$ind = 'itetipo_'.$i;   $tipo   = $this->input->post($ind);
				$ind = 'itefecha_'.$i;  $fecha  = $this->input->post($ind);

				if($numero === false || $tipo === false || $fecha === false ){
					break;
				}
				if(empty($numero)|| empty($tipo) || empty($fecha)){
					break;
				}

				$ind = 'iteaplicar_'.$i; $abono  = $this->input->post($ind);
				$ind = 'itemonto_'.$i;   $monto  = $this->input->post($ind);
				$ind = 'iteid_'.$i;      $id     = $this->input->post($ind);

				if($ttipo=='C'){
					$rsaldo = floatval($this->datasis->dameval("SELECT monto-abonos AS saldo FROM smov WHERE id=${id}"));
				}else{
					$rsaldo = floatval($this->datasis->dameval("SELECT monto-abonos AS saldo FROM sprm WHERE id=${id}"));
				}

				if(!empty($abono)){
					if(!is_numeric($abono)){
						$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'El monto del efecto a aplicar '.$tipo.$numero.' no es num&eacute;rico.';
						return false;
						break;
					}

					$abono = floatval($abono);
					if($abono > $rsaldo){
						$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'El monto del efecto a aplicar '.$tipo.$numero.' es mayor al saldo disponible '.nformat($rsaldo).'.';
						return false;
						break;
					}

					$efectos += $abono;
					if($abono > $monto){
						$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'El monto del efecto a aplicar '.$tipo.$numero.' no puede exceder su saldo.';
						return false;
						break;
					}
					$arr_efe[] = array('id'=>$id,'numero'=>$numero,'tipo'=>$tipo,'fecha'=>$fecha,'abono'=>$abono,'monto'=>$monto);
				}
				$i++;
			}
			if($efectos<=0){
				$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'El monto de los efectos a aplicar debe ser mayor a cero.';
				return false;
			}
			//Fin de los efectos


			if($aplicar-$efectos != 0){
				$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'El monto a aplicar es diferente al aplicado. '."$aplicar-$efectos";
				return false;
			}

		}

		$dbreinte = $this->db->escape($reinte);
		if($preinte=='S'){
			if(empty($reinte)){
				$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'Si es un reintegro debe seleccionar a quien se le reintegra.';
				return false;
			}else{
				if($ttipo=='P'){
					$can=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM scli WHERE cliente=${dbreinte}"));
					if($can <= 0){
						$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'El cliente elegido para el reintegro no existe.';
						return false;
					}
				}else{
					$can=intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM sprv WHERE proveed=${dbreinte}"));
					if($can <= 0){
						$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'El proveedor elegido para el reintegro no existe.';
						return false;
					}
				}
			}
		}

		$transac = $this->datasis->fprox_numero('ntransa');
		$numero  = $this->datasis->fprox_numero('napan');
		$do->set('transac',$transac);
		$do->set('numero' ,$numero );
		$do->set('fecha'  ,date('Y-m-d'));

		$mSQLs = array();
		if($preinte=='S'){
			$fecha    = $do->get('fecha');
			if($ttipo=='P'){ //Aplica a proveedor
				$mNOMBRE  = $this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$dbclipro);
				$mNUMERO = $this->datasis->fprox_numero('num_nd');
				$data = array();
				$data['cod_prv']  = $clipro;
				$data['nombre']   = $mNOMBRE ;
				$data['tipo_doc'] = 'ND';
				$data['numero']   = $mNUMERO;
				$data['fecha']    = $fecha;
				$data['monto']    = $aplicar;
				$data['impuesto'] = 0;
				$data['abonos']   = 0;
				$data['vence']    = $fecha;
				$data['observa1'] = substr($observa,0,50);
				$data['observa2'] = (strlen($observa)>=50)? substr($observa,50):'';
				$data['tipo_ref'] = 'AP';
				$data['estampa']  = $do->get('estampa');
				$data['hora']     = $do->get('hora');
				$data['usuario']  = $do->get('usuario');
				$data['transac']  = $transac;
				$data['num_ref']  = $numero;
				$sql = $this->db->insert_string('sprm', $data);
				$ban = $this->db->simple_query($sql);
				$iid = $this->db->insert_id();
				$arr_efe[] = array('id'=>$iid,'numero'=>$mNUMERO,'tipo'=>'ND','fecha'=>$fecha,'abono'=>$aplicar,'monto'=>$aplicar);

				$mNUMERO  = $this->datasis->fprox_numero('ndcli');
				$mNOMBRE  = $this->datasis->dameval('SELECT nombre FROM scli WHERE cliente='.$dbreinte);
				$data = array();
				$data['cod_cli']  = $reinte;
				$data['nombre']   = $mNOMBRE;
				$data['tipo_doc'] = 'ND';
				$data['numero']   = $mNUMERO;
				$data['fecha']    = $fecha;
				$data['monto']    = $aplicar;
				$data['abonos']   = 0;
				$data['vence']    = $fecha;
				$data['observa1'] = substr($observa,0,50);
				$data['observa2'] = (strlen($observa)>=50)? substr($observa,50):'';
				$data['impuesto'] = 0;
				$data['tipo_ref'] = 'AP';
				$data['estampa']  = $do->get('estampa');
				$data['hora']     = $do->get('hora');
				$data['usuario']  = $do->get('usuario');
				$data['transac']  = $transac;
				$data['num_ref']  = $numero;
				$mSQLs[] = $this->db->insert_string('smov', $data);

			}else{ //Aplica a cliente

				$mNUMERO  = $this->datasis->fprox_numero('ndcli');
				$mNOMBRE  = $this->datasis->dameval('SELECT nombre FROM scli WHERE cliente='.$dbclipro);
				$data=array();
				$data['cod_cli']  = $clipro;
				$data['nombre']   = $mNOMBRE;
				$data['tipo_doc'] = 'ND';
				$data['numero']   = $mNUMERO;
				$data['fecha']    = $fecha;
				$data['monto']    = $aplicar;
				$data['impuesto'] = 0;
				$data['abonos']   = 0;
				$data['vence']    = $fecha;
				$data['observa1'] = substr($observa,0,50);
				$data['observa2'] = (strlen($observa)>=50)? substr($observa,50):'';
				$data['tipo_ref'] = 'AP';
				$data['estampa']  = $do->get('estampa');
				$data['hora']     = $do->get('hora');
				$data['usuario']  = $do->get('usuario');
				$data['transac']  = $transac;
				$data['num_ref']  = $numero;
				$sql = $this->db->insert_string('smov', $data);
				$ban = $this->db->simple_query($sql);
				$iid = $this->db->insert_id();
				$arr_efe[] = array('id'=>$iid,'numero'=>$mNUMERO,'tipo'=>'ND','fecha'=>$fecha,'abono'=>$aplicar,'monto'=>$aplicar);

				$mNUMERO  = $this->datasis->fprox_numero('num_nd');
				$mNOMBRE  = $this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$dbreinte);
				$data = array();
				$data['cod_prv']  = $reinte;
				$data['nombre']   = $mNOMBRE;
				$data['tipo_doc'] = 'ND';
				$data['numero']   = $mNUMERO;
				$data['fecha']    = $fecha;
				$data['monto']    = $aplicar;
				$data['abonos']   = 0;
				$data['vence']    = $fecha;
				$data['observa1'] = substr($observa,0,50);
				$data['observa2'] = (strlen($observa)>=50)? substr($observa,50):'';
				$data['impuesto'] = 0;
				$data['tipo_ref'] = 'AP';
				$data['estampa']  = $do->get('estampa');
				$data['hora']     = $do->get('hora');
				$data['usuario']  = $do->get('usuario');
				$data['transac']  = $transac;
				$data['num_ref']  = $numero;
				$mSQLs[] = $this->db->insert_string('sprm', $data);
			}
		}

		$data = array();
		$data['transac'] = $transac;
		$data['estampa'] = $estampa;
		$data['hora']    = $hora;
		$data['usuario'] = $usuario;
		$data['ppago']=$data['reteiva']=$data['reten']=$data['cambio']=$data['mora']=0;
		$saldoefe=0;
		$centi = '';

		foreach($arr_apl as $apl){

			$saldoapl = $apl['monto'];
			do{
				if($saldoefe<=0){
					$efe = array_shift($arr_efe);
					if(empty($efe)) break;
				}

				$data['numero']    = $efe['numero'];
				$data['tipo_doc']  = $efe['tipo'];
				$data['fecha']     = $efe['fecha'];
				$data['monto']     = $efe['monto'];

				if($saldoapl >= $efe['abono']){
					$data['abono'] = $efe['abono'];
					$saldoapl      = $saldoapl-$efe['abono'];
					$saldoefe      = 0;
				}else{
					$data['abono'] = $saldoapl;
					$saldoefe      = $apl['monto']-$saldoapl;
					$saldoapl      = 0;
				}

				if($ttipo=='C'){
					$data['numccli']  = $apl['numero'];
					$data['tipoccli'] = $apl['tipo'];
					$data['cod_cli']  = $clipro;
					$data['reteiva']  = '';
					$data['nroriva']  = '';
					$data['emiriva']  = '';
					$data['recriva']  = '';
					$mSQLs[] = $this->db->insert_string('itccli', $data);
					$mSQLs[] = 'UPDATE smov SET abonos=abonos+'.$efe['abono'].' WHERE id='.$this->db->escape($efe['id']);
				}elseif($ttipo == 'P'){
					$data['numppro']  = $apl['numero'];
					$data['tipoppro'] = $apl['tipo'];
					$data['cod_prv']  = $clipro;
					$data['preten']   = '';
					$data['creten']   = '';
					$data['breten']   = '';
					$mSQLs[] = $this->db->insert_string('itppro', $data);
					$mSQLs[] = 'UPDATE sprm SET abonos=abonos+'.$efe['abono'].' WHERE id='.$this->db->escape($efe['id']);
				}
			}while($saldoapl>0);

			if($ttipo=='C'){
				$mSQLs[] = 'UPDATE smov SET abonos=abonos+'.$apl['monto'].' WHERE id='.$this->db->escape($apl['id']);
			}elseif($ttipo=='P'){
				$mSQLs[] = 'UPDATE sprm SET abonos=abonos+'.$apl['monto'].' WHERE id='.$this->db->escape($apl['id']);
			}
		}


		//$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = print_r($arr_apl,true);
		//return false;

		$do->set('observa1',substr($observa,0,50));
		$do->set('observa2',(strlen($observa)>=50)? substr($observa,50):'');
		//$obs = wordwrap($observa, 50,"\n");

		$this->_sqls=$mSQLs;
		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']= 'Registro no se puede modificar, debe reversarlo y volverlo a hacer.';
		return false;
	}

	function _pre_delete($do){

		$do->error_message_ar['pre_del']='';
		return true;
	}

	function _post_insert($do){
		$numero = $do->get('numero');
		foreach($this->_sqls as $mSQL){
			$rt=$this->db->simple_query($mSQL);
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits numero ${numero} id $primary ");
	}

	function _post_update($do){
		$numero = $do->get('numero');
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits numero ${numero} id $primary ");
	}

	function _post_delete($do){
		$numero  = $do->get('numero');
		$transac = $do->get('transac');
		$tipo    = $do->get('tipo');
		$clipro  = $do->get('clipro');
		$dbtransa= $this->db->escape($transac);
		$dbclipro= $this->db->escape($clipro);

		if($tipo=='C'){
			$query = $this->db->query('SELECT numccli,tipoccli,numero,tipo_doc,fecha,abono FROM itccli WHERE transac='.$dbtransa.' AND cod_cli='.$dbclipro);

			foreach ($query->result() as $row){
				$dbnumero = $this->db->escape($row->numccli);
				$dbtipo   = $this->db->escape($row->tipoccli);
				$dbtnumero= $this->db->escape($row->numero);
				$dbttipo  = $this->db->escape($row->tipo_doc);
				$abono    = $row->abono;

				$mSQL = "UPDATE smov SET abonos=abonos-${abono} WHERE cod_cli=${dbclipro} AND tipo_doc=${dbttipo} AND numero=${dbtnumero}";
				$this->db->simple_query($mSQL);
				$mSQL = "UPDATE smov SET abonos=abonos-${abono} WHERE cod_cli=${dbclipro} AND tipo_doc=${dbtipo}  AND numero=${dbnumero}";
				$this->db->simple_query($mSQL);
			}
			$mSQL='DELETE FROM itccli WHERE transac='.$dbtransa.' AND cod_cli='.$dbclipro;
			$this->db->simple_query($mSQL);

		}elseif($tipo=='P'){
			$query = $this->db->query('SELECT numppro,tipoppro,numero,tipo_doc,fecha,abono FROM itppro WHERE transac='.$dbtransa.' AND cod_prv='.$dbclipro);

			foreach ($query->result() as $row){
				$dbnumero = $this->db->escape($row->numppro);
				$dbtipo   = $this->db->escape($row->tipoppro);
				$dbtnumero= $this->db->escape($row->numero);
				$dbttipo  = $this->db->escape($row->tipo_doc);
				$abono    = $row->abono;

				$mSQL = "UPDATE sprm SET abonos=abonos-${abono} WHERE cod_prv=${dbclipro} AND tipo_doc=${dbttipo} AND numero=${dbtnumero}";
				$this->db->simple_query($mSQL);
				$mSQL = "UPDATE sprm SET abonos=abonos-${abono} WHERE cod_prv=${dbclipro} AND tipo_doc=${dbtipo}  AND numero=${dbnumero}";
				$this->db->simple_query($mSQL);
			}
			$mSQL='DELETE FROM itppro WHERE transac='.$dbtransa.' AND cod_prv='.$dbclipro;
			$this->db->simple_query($mSQL);
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits numero ${numero} id ${primary} ");
	}

	function instalar(){
		$campos=$this->db->list_fields('apan');
		if(!in_array('id',$campos)){
			$this->db->query('ALTER TABLE apan DROP PRIMARY KEY');
			$this->db->query('ALTER TABLE apan ADD UNIQUE INDEX numero (numero)');
			$this->db->query('ALTER TABLE apan ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		$itcampos=$this->db->list_fields('itccli');
		if(!in_array('id',$itcampos)){
			$mSQL="ALTER TABLE `itccli` ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `recriva`, ADD PRIMARY KEY (`id`)";
			$this->db->simple_query($mSQL);
		}
	}
}
