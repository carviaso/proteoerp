<?php
class Rcobro extends Controller {
	var $mModulo = 'RCOBRO';
	var $titp    = 'RELACION DE COBRO';
	var $tits    = 'RELACION DE COBRO';
	var $url     = 'finanzas/rcobro/';

	function Rcobro(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->instalar();
		$this->datasis->modulo_nombre( 'RCOBRO', $ventana=0 );
	}

	function index(){
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
		$grid->wbotonadd(array('id'=>'imprime', 'img'=>'assets/default/images/print.png','alt' => 'Reimprimir', 'label'=>'Reimprimir Documento'));
		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		$param['script']       = '';
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('RCOBRO', 'JQ');
		$param['otros']        = $this->datasis->otros('RCOBRO', 'JQ');
		$param['centerpanel']  = $centerpanel;
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
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function rcobroadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function rcobroedit(){
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
		function rcobroshow(){
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
		function rcobrodel() {
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
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/RCOBRO').'/\'+id+\'/id\'').';
			} else {
				$.prompt("<h1>Por favor Seleccione un registro</h1>");
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
									apprise("Registro Guardado");
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									'.$this->datasis->jwinopen(site_url('formatos/ver/RCOBRO').'/\'+json.pk.id+\'/id\'').';
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

		$grid->addField('id');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 50,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'align'         => "'center'",
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
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


		$grid->addField('retorno');
		$grid->label('Retorno');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('vende');
		$grid->label('Vendedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('observa');
		$grid->label('Observaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('facturas');
		$grid->label('Facturas');
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
			'editoptions'   => '{ size:12, maxlength: 12 }',
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
			},
			afterInsertRow:
			function( rid, aData, rowe){
				if(aData.tipo == "P"){
					$(this).jqGrid( "setCell", rid, "tipo","",{color:"#000000", background:"#DCFFB5" });
				}else if(aData.tipo == "A"){
					//$(this).jqGrid( "setRowData", rid, "tipo",{color:"#000000", background:"#C90623" });
					$(this).jqGrid( "setCell", rid, "tipo","",{color:"#000000", background:"##FFDD00" });
				}
			}
		');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('RCOBRO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('RCOBRO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('RCOBRO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('RCOBRO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: rcobroadd, editfunc: rcobroedit, delfunc: rcobrodel, viewfunc: rcobroshow');

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
		$mWHERE = $grid->geneTopWhere('rcobro');

		$response   = $grid->getData('rcobro', array(array()), array(), false, $mWHERE, 'id','desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		echo 'Deshabilitado';
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('tipo_doc');
		$grid->label('Tipo Doc.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 30,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
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
			'width'         => 60,
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
			'width'         => 20,
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


		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));

		$grid->addField('rcobro');
		$grid->label('Rcobro');
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
		if($id === 0){
			$id = intval($this->datasis->dameval("SELECT MAX(id) FROM rcobro"));
		}
		if(empty($id)) return '';
		$grid     = $this->jqdatagrid;
		$mSQL     = "SELECT * FROM smov WHERE rcobro=${id}";
		$response = $grid->getDataSimple($mSQL);
		$rs       = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setDatait(){
	}

	//******************************************************************
	// DataEdit
	//******************************************************************
	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});';

		$do = new DataObject('rcobro');

		$do->rel_one_to_many('smov','smov',array('id'=>'rcobro'));
		$edit = new DataDetails($this->tits, $do );

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->tipo = new inputField('Tipo','tipo');
		$edit->tipo->rule='';
		$edit->tipo->size =3;
		$edit->tipo->maxlength =1;
		$edit->tipo->insertValue ='P';

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->size =10;
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->maxlength =8;
		$edit->fecha->calendar = false;

		$edit->retorno = new dateonlyField('Recepci&oacute;n','retorno');
		$edit->retorno->rule='chfecha';
		$edit->retorno->calendar=false;
		$edit->retorno->size =10;
		$edit->retorno->maxlength =8;
		$edit->retorno->calendar = false;
/*
		$edit->vende = new inputField('Vendedor','vende');
		$edit->vende->rule='';
		$edit->vende->size =5;
		$edit->vende->maxlength =5;
*/
		$edit->vende= new dropdownField('Cobrador', 'vende');
		$edit->vende->options('SELECT vendedor, CONCAT(vendedor," - ",nombre) nombre FROM vend WHERE tipo IN ("C","A") ORDER BY nombre');
		$edit->vende->style ="width:250px;";

		$edit->observa = new textareaField('Observaci&oacute;n','observa');
		$edit->observa->rule='';
		$edit->observa->cols = 70;
		$edit->observa->rows = 2;

		$edit->facturas = new inputField('Nro. de Facturas','facturas');
		$edit->facturas->rule      = 'integer';
		$edit->facturas->css_class = 'inputonlynum';
		$edit->facturas->type     = 'inputhidden';
		$edit->facturas->showformat = 'decimal';
		$edit->facturas->readonly  = true;

		$edit->monto = new inputField('Monto total','total');
		$edit->monto->db_name   = 'monto';
		$edit->monto->rule      = 'numeric';
		$edit->monto->css_class = 'inputnum';
		$edit->monto->type      = 'inputhidden';
		$edit->monto->showformat = 'decimal';
		$edit->monto->readonly  = true;

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));


		//**************************************************************
		// Detalle
		//**************************************************************

		$edit->itid = new hiddenField('Id','id_<#i#>');
		$edit->itid->rule    = '';
		$edit->itid->db_name = 'id';
		$edit->itid->rel_id  = 'smov';

		$edit->tipo_doc = new inputField('Tipo Doc.','tipo_doc_<#i#>');
		$edit->tipo_doc->db_name = 'tipo_doc';
		$edit->tipo_doc->rule='';
		$edit->tipo_doc->rel_id ='smov';
		$edit->tipo_doc->type     = 'inputhidden';

		$edit->numero = new inputField('N&uacute;mero','numero_<#i#>');
		$edit->numero->db_name   = 'numero';
		$edit->numero->rule      = '';
		$edit->numero->size      = 10;
		$edit->numero->maxlength = 8;
		$edit->numero->rel_id    = 'smov';

		$edit->fechad = new dateonlyField('Fecha','fechad_<#i#>');
		$edit->fechad->db_name  = 'fechad';
		$edit->fechad->rule     = 'chfecha';
		$edit->fechad->rel_id   = 'smov';
		$edit->fechad->calendar = false;
		$edit->fechad->readonly = true;
		$edit->fechad->type     = 'inputhidden';

		$edit->vence = new dateonlyField('Vence','vence_<#i#>');
		$edit->vence->db_name  = 'vence';
		$edit->vence->rule     = 'chfecha';
		$edit->vence->rel_id   = 'smov';
		$edit->vence->type     = 'inputhidden';
		$edit->vence->calendar = false;
		$edit->vence->readonly = true;
/*
		$edit->vd = new inputField('Vd','vd_<#i#>');
		$edit->vd->rule='';
		$edit->vd->size =7;
		$edit->vd->maxlength =5;
		$edit->vd->rel_id ='smov';

		$edit->cod_cli = new inputField('Cod_cli','cod_cli_<#i#>');
		$edit->cod_cli->rule='';
		$edit->cod_cli->size =7;
		$edit->cod_cli->maxlength =5;
		$edit->cod_cli->rel_id ='smov';
*/
		$edit->nombre = new inputField('Nombre','nombre_<#i#>');
		$edit->nombre->db_name = 'nombre';
		$edit->nombre->rule='';
		$edit->nombre->rel_id ='smov';
		$edit->nombre->type     = 'inputhidden';
		$edit->nombre->readonly = true;
/*
		$edit->referen = new inputField('Referen','referen_<#i#>');
		$edit->referen->rule='';
		$edit->referen->size =3;
		$edit->referen->maxlength =1;
		$edit->referen->rel_id ='smov';

		$edit->totals = new inputField('Totals','monto_<#i#>');
		$edit->totals->rule='numeric';
		$edit->totals->css_class='inputnum';
		$edit->totals->size =14;
		$edit->totals->maxlength =12;
		$edit->totals->rel_id ='sfac';
*/
		$edit->totalg = new inputField('Total','monto_<#i#>');
		$edit->totalg->db_name = 'monto';
		$edit->totalg->rule='numeric';
		$edit->totalg->css_class='inputnum';
		$edit->totalg->size =14;
		$edit->totalg->maxlength =12;
		$edit->totalg->rel_id ='smov';
		$edit->totalg->type     = 'inputhidden';
		$edit->totalg->readonly  = true;
		//******************************************************************

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
			$this->load->view('view_rcobro', $conten);
		}
	}

	function _pre_insert($do){
		$do->save_rel = false;

		$cana=$do->count_rel('smov');
		if($cana<=0){
			$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='Debe tener al menos un afecto por cobrar';
			return false;
		}

		return true;
	}

	function _pre_update($do){
		$do->save_rel = false;
		$id = $do->get('id');

		$cana=$do->count_rel('smov');
		if($cana<=0){
			$do->error_message_ar['pre_ins']=$do->error_message_ar['pre_upd']='Debe tener al menos un afecto por cobrar';
			return false;
		}

		return true;
	}

	function _pre_delete($do){
		$do->save_rel = false;
		$do->error_message_ar['pre_del']='';
		return false;
	}

	function _post_inserup($do){
		$id = $do->pk['id'];

		$cana=$do->count_rel('smov');
		for($i=0;$i<$cana;$i++){
			$itid = $do->get_rel('smov','id' ,$i);
			$mSQL = "UPDATE smov SET rcobro=${id} WHERE id=${itid}";
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'rcobro'); }
		}
	}

	function _post_insert($do){
		$this->_post_inserup($do);
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits ${primary} ");
	}

	function _post_update($do){
		$id = $do->get('id');
		$mSQL = "UPDATE smov SET rcobro=NULL WHERE rcobro=${id}";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'rcobro'); }

		$this->_post_inserup($do);

		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits ${primary} ");
	}

	function _post_delete($do){
		$id = $do->get('id');
		$mSQL = "UPDATE smov SET rcobro=NULL WHERE rcobro=${id}";

		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits ${primary} ");
	}

	function instalar(){
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		if(!$this->db->table_exists('rcobro')){
			$mSQL="CREATE TABLE `rcobro` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `tipo` char(1) NOT NULL COMMENT 'Tipo Pendiente, Cerrado',
			  `fecha` date DEFAULT NULL COMMENT 'Fecha de Entrega',
			  `retorno` date DEFAULT NULL COMMENT 'Fecha que recepcion',
			  `vende` char(5) DEFAULT NULL COMMENT 'Vendedor Cobrador',
			  `observa` text,
			  `facturas` int(11) DEFAULT NULL COMMENT 'Nro de Faturas',
			  `monto` decimal(17,2) DEFAULT NULL COMMENT 'Monto total de Facturas',
			  `estampa` date DEFAULT NULL,
			  `usuario` char(12) DEFAULT NULL,
			  `hora` char(8) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Relacion de cobro'";
			$this->db->query($mSQL);
		}

		$campos=$this->db->list_fields('smov');
		if(!in_array('rcobro',$campos)){
			$this->db->query("ALTER TABLE `smov` ADD COLUMN `rcobro` INT(11) NULL DEFAULT NULL AFTER `ncredito`");
		}
	}
}
