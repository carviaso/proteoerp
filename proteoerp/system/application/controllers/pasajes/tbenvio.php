<?php
class Tbenvio extends Controller {
	var $mModulo = 'TBENVIO';
	var $titp    = 'Envios';
	var $tits    = 'Envios';
	var $url     = 'pasajes/tbenvio/';

	function Tbenvio(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'TBENVIO', $ventana=0 );
	}

	function index(){
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
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

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('TBENVIO', 'JQ');
		$param['otros']       = $this->datasis->otros('TBENVIO', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		function tbenvioadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function tbenvioedit(){
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
		function tbenvioshow(){
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
		function tbenviodel() {
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/TBENVIO').'/\'+res.id+\'/id\'').';
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
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('nrofact');
		$grid->label('Nro.Factura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('fecenv');
		$grid->label('Fecha de Env&iacute;o');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('codofi_org');
		$grid->label('Oficina Orig.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('codofi_des');
		$grid->label('Oficina Dest.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('codcli_org');
		$grid->label('Cliente Orig.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('nomcli_org');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:200, maxlength: 200 }',
		));


		$grid->addField('telf_org');
		$grid->label('Tel&eacute;fono Orig.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('codcli_des');
		$grid->label('Cliente Dest.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('nomcli_des');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:200, maxlength: 200 }',
		));


		$grid->addField('telf_des');
		$grid->label('Telf dest.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('dirdes');
		$grid->label('Direc. dest.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:300, maxlength: 300 }',
		));


		$grid->addField('exon');
		$grid->label('Exonerado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('anula');
		$grid->label('Anulado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('kilo');
		$grid->label('Kilometros');
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


		$grid->addField('usua');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('puertap');
		$grid->label('Puertap');
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


		$grid->addField('fledes');
		$grid->label('Fledes');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:200, maxlength: 200 }',
		));


		$grid->addField('cant');
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
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
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


		$grid->addField('envio');
		$grid->label('Env&iacute;o');
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


		$grid->addField('volumen');
		$grid->label('Volumen');
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


		$grid->addField('seguro');
		$grid->label('Seguro');
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


		$grid->addField('ipostel');
		$grid->label('Ipostel');
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


		$grid->addField('ref');
		$grid->label('Ref');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('fom_pag');
		$grid->label('Fom_pag');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('nro_post');
		$grid->label('Nro_post');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('orden');
		$grid->label('Orden');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('codstat');
		$grid->label('Codstat');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('fecent');
		$grid->label('Fecent');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('fecrec');
		$grid->label('Fecrec');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('TBENVIO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('TBENVIO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('TBENVIO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('TBENVIO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: tbenvioadd, editfunc: tbenvioedit, delfunc: tbenviodel, viewfunc: tbenvioshow');

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
		$mWHERE = $grid->geneTopWhere('tbenvio');

		$response   = $grid->getData('tbenvio', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit($this->tits, 'tbenvio');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert', '_pre_insert' );
		$edit->pre_process( 'update', '_pre_update' );
		$edit->pre_process( 'delete', '_pre_delete' );

		$edit->nrofact = new inputField('Nro. Fact','nrofact');
		$edit->nrofact->rule='';
		$edit->nrofact->size =22;
		$edit->nrofact->maxlength =20;

		$edit->fecenv = new dateonlyField('Fecha env&iacute;o','fecenv');
		$edit->fecenv->rule='chfecha';
		$edit->fecenv->size =10;
		$edit->fecenv->maxlength =8;

		$edit->codofi_org = new dropdownField('Oficina Or&iacute;gen','codofi_org');
		$edit->codofi_org->options('SELECT TRIM(codofi) AS codifi, CONCAT_WS("-",TRIM(codofi),TRIM(desofi)) AS desofi FROM tbofici ORDER BY desofi');
		$edit->codofi_org->rule = 'required';
		$edit->codofi_org->style= 'width:180px;';

		$edit->codofi_des = new dropdownField('Oficina Destino','codofi_des');
		$edit->codofi_des->options('SELECT TRIM(codofi) AS codifi, CONCAT_WS("-",TRIM(codofi),TRIM(desofi)) AS desofi FROM tbofici ORDER BY desofi');
		$edit->codofi_des->rule = 'required';
		$edit->codofi_des->style= 'width:180px;';

		$edit->codcli_org = new inputField('Cliente','codcli_org');
		$edit->codcli_org->rule='';
		$edit->codcli_org->size =8;
		$edit->codcli_org->maxlength =20;

		$edit->nomcli_org = new inputField('Nombre','nomcli_org');
		$edit->nomcli_org->rule='';
		$edit->nomcli_org->maxlength =200;

		$edit->telf_org = new inputField('Tel&eacute;fono','telf_org');
		$edit->telf_org->rule='';
		$edit->telf_org->size =24;
		$edit->telf_org->maxlength =30;

		$edit->codcli_des = new inputField('Cliente','codcli_des');
		$edit->codcli_des->rule='';
		$edit->codcli_des->size =8;
		$edit->codcli_des->maxlength =20;

		$edit->nomcli_des = new inputField('Nombre','nomcli_des');
		$edit->nomcli_des->rule='';
		$edit->nomcli_des->maxlength =200;

		$edit->telf_des = new inputField('Tel&eacute;fono','telf_des');
		$edit->telf_des->rule='';
		$edit->telf_des->size =24;
		$edit->telf_des->maxlength =30;

		$edit->dirdes = new inputField('Direcci&oacute;n','dirdes');
		$edit->dirdes->rule='';
		$edit->dirdes->maxlength =300;

		$edit->exon = new inputField('Exonerado','exon');
		$edit->exon->rule='';
		$edit->exon->size =3;
		$edit->exon->maxlength =1;

		$edit->anula = new inputField('Anula','anula');
		$edit->anula->rule='';
		$edit->anula->size =3;
		$edit->anula->maxlength =1;

		$edit->kilo = new inputField('Recargo por Dist.','kilo');
		$edit->kilo->rule='numeric';
		$edit->kilo->css_class='inputnum';
		$edit->kilo->size =12;
		$edit->kilo->maxlength =10;

		$edit->usua = new inputField('Usuario','usua');
		$edit->usua->rule='';
		$edit->usua->size =22;
		$edit->usua->maxlength =20;

		$edit->puertap = new inputField('Domicilio','puertap');
		$edit->puertap->rule='numeric';
		$edit->puertap->css_class='inputnum';
		$edit->puertap->size =12;
		$edit->puertap->maxlength =10;

		$edit->fledes = new inputField('Fledes','fledes');
		$edit->fledes->rule='';
		$edit->fledes->size =3;
		$edit->fledes->maxlength =1;

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('Bolsa(s)'  ,'Bolsa(s)'  );
		$edit->tipo->option('Caja(s)'   ,'Caja(s)'   );
		$edit->tipo->option('Giro(s)'   ,'Giro(s)'   );
		$edit->tipo->option('Otro(s)'   ,'Otro(s)'   );
		$edit->tipo->option('Paquete(s)','Paquete(s)');
		$edit->tipo->option('Sobre Post','Sobre Post');
		$edit->tipo->option('Sobre(s)'  ,'Sobre(s)'  );
		$edit->tipo->rule = 'required';
		$edit->tipo->style= 'width:100px;';

		$edit->descrip = new inputField('Descripci&oacute;n','descrip');
		$edit->descrip->rule='';
		//$edit->descrip->size =202;
		$edit->descrip->maxlength =200;

		$edit->cant = new inputField('Cant.','cant');
		$edit->cant->rule='integer';
		$edit->cant->css_class='inputonlynum';
		$edit->cant->size =13;
		$edit->cant->maxlength =11;

		$edit->peso = new inputField('Peso Kg.','peso');
		$edit->peso->rule='numeric';
		$edit->peso->css_class = 'inputnum';
		$edit->peso->size =12;
		$edit->peso->onkeyup = 'ctarifa()';
		$edit->peso->maxlength =10;

		$edit->envio = new inputField('Monto del env&iacute;o','envio');
		$edit->envio->rule='numeric';
		$edit->envio->css_class='inputnum';
		$edit->envio->size =12;
		$edit->envio->maxlength =10;

		$edit->volumen = new inputField('Vol&uacute;men','volumen');
		$edit->volumen->rule='numeric';
		$edit->volumen->css_class='inputnum';
		$edit->volumen->size =12;
		$edit->volumen->maxlength =10;

		//Campos comdines
		$edit->v1 = new inputField('','v1');
		$edit->v1->rule='numeric';
		$edit->v1->css_class='inputnum';
		$edit->v1->onkeyup='cvolumen();';
		$edit->v1->size =5;
		$edit->v1->maxlength =10;

		$edit->v2 = new inputField('','v2');
		$edit->v2->rule='numeric';
		$edit->v2->css_class='inputnum';
		$edit->v2->onkeyup='cvolumen();';
		$edit->v2->size =5;
		$edit->v2->maxlength =10;

		$edit->v3 = new inputField('','v3');
		$edit->v3->rule='numeric';
		$edit->v3->css_class='inputnum';
		$edit->v3->onkeyup='cvolumen();';
		$edit->v3->size =5;
		$edit->v3->maxlength =10;

		$edit->subtotal = new inputField('subtotal','subtotal');
		$edit->subtotal->rule='numeric';
		$edit->subtotal->css_class='inputnum';
		$edit->subtotal->onkeyup='';
		$edit->subtotal->size =5;
		$edit->subtotal->maxlength =10;
		$edit->subtotal->type='inputhidden';

		$edit->iva = new inputField('Impuesto','iva');
		$edit->iva->rule='numeric';
		$edit->iva->css_class='inputnum';
		$edit->iva->onkeyup='';
		$edit->iva->size =5;
		$edit->iva->maxlength =10;
		$edit->iva->type='inputhidden';


		$edit->total = new inputField('Total','total');
		$edit->total->rule='numeric';
		$edit->total->css_class='inputnum';
		$edit->total->onkeyup='';
		$edit->total->size =5;
		$edit->total->maxlength =10;
		$edit->total->type='inputhidden';
		//Fin de los campos comodines

		$edit->seguro = new inputField('Seguro','seguro');
		$edit->seguro->rule='numeric';
		$edit->seguro->css_class='inputnum';
		$edit->seguro->size =12;
		$edit->seguro->maxlength =10;

		$edit->iva = new inputField('Iva','iva');
		$edit->iva->rule='numeric';
		$edit->iva->css_class='inputnum';
		$edit->iva->size =12;
		$edit->iva->maxlength =10;

		$edit->ipostel = new inputField('Ipostel','ipostel');
		$edit->ipostel->rule='numeric';
		$edit->ipostel->css_class='inputnum';
		$edit->ipostel->size =12;
		$edit->ipostel->maxlength =10;

		$edit->ref = new inputField('Ref','ref');
		$edit->ref->rule='';
		$edit->ref->size =22;
		$edit->ref->maxlength =20;

		$edit->fom_pag = new inputField('Fom_pag','fom_pag');
		$edit->fom_pag->rule='';
		$edit->fom_pag->size =22;
		$edit->fom_pag->maxlength =20;

		$edit->nro_post = new inputField('Nro_post','nro_post');
		$edit->nro_post->rule='';
		$edit->nro_post->size =22;
		$edit->nro_post->maxlength =20;

		$edit->orden = new inputField('Orden','orden');
		$edit->orden->rule='';
		$edit->orden->size =22;
		$edit->orden->maxlength =20;

		$edit->codstat = new inputField('Codstat','codstat');
		$edit->codstat->rule='';
		$edit->codstat->size =4;
		$edit->codstat->maxlength =2;

		$edit->fecent = new dateonlyField('Fecent','fecent');
		$edit->fecent->rule='chfecha';
		$edit->fecent->size =10;
		$edit->fecent->maxlength =8;

		$edit->fecrec = new dateonlyField('Fecrec','fecrec');
		$edit->fecrec->rule='chfecha';
		$edit->fecrec->size =10;
		$edit->fecrec->maxlength =8;

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
			$this->load->view('view_tbenvio', $conten);
		}
	}

	function _pre_insert($do){
		$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
		return false;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	//Tarifa por volumen
	function tarifa(){
		session_write_close();
		$mid = $this->input->post('q');

		if($mid !== false){
			$volumen=floatval($mid);

			if($volumen<=100){
				$val = 0;
			}elseif($volumen<=150){
				$val = $this->datasis->dameval("SELECT valor FROM tbprecio WHERE codpre='9' LIMIT 1");
			}elseif($volumen<=200){
				$val = $this->datasis->dameval("SELECT valor FROM tbprecio WHERE codpre='A' LIMIT 1");
			}else{
				$val = $this->datasis->dameval("SELECT valor FROM tbprecio WHERE codpre='B' LIMIT 1");
			}

			echo (empty($val))? 0: $val;
		}else{
			echo 0;
		}
	}

	//Tarifa por por peso
	function tarifape(){
		session_write_close();
		$mid = $this->input->post('q');
		$ori = $this->input->post('o'); //Origen
		$des = $this->input->post('d'); //Destino
		$rt=array('iposte'=>0,'monto'=>0,'distancia'=>0,'peso'=>0,'iva'=>0,'pdista'=>false);

		if((!empty($mid)) && (!empty($ori)) && (!empty($des))){
			$ivas = $this->datasis->ivaplica();

			$peso    =floatval($mid);
			$sobrante=$peso-10;

			$dbori = $this->db->escape($ori);
			$dbdes = $this->db->escape($des);
			$dista = $this->datasis->dameval("SELECT valor FROM tbdistan WHERE codori=${dbori} AND coddes=${dbdes} LIMIT 1");
			if(empty($dista)){
				$rt['pdista']=true;
				echo json_encode($rt);
				return true;
			}

			//Cargo por distancia
			if(floatval($dista)>7){ //mayor a 850
				$dis = $this->datasis->dameval("SELECT valor FROM tbprecio  WHERE codpre='8' LIMIT 1");
			}else{ //meno a 850
				$dis = $this->datasis->dameval("SELECT valor FROM tbprecio  WHERE codpre='7' LIMIT 1");
			}

			//Calculo por peso adicional
			if($sobrante>0){
				$sob  = $this->datasis->dameval("SELECT valor FROM tbprecio  WHERE codpre='1' LIMIT 1");
				$pad  = $peso*$sob;
				$val  = $pad;
				$ipt  = 0;
			}else{
				//Cargo por peso
				if($peso<=0.02){
					$val = $this->datasis->dameval("SELECT valor  FROM tbprecio  WHERE codpre='2' LIMIT 1");
					$ipt = $this->datasis->dameval("SELECT monipo FROM tbipostel WHERE codipo='1' LIMIT 1");
				}elseif($peso<=0.05){
					$val = $this->datasis->dameval("SELECT valor  FROM tbprecio  WHERE codpre='2' LIMIT 1");
					$ipt = $this->datasis->dameval("SELECT monipo FROM tbipostel WHERE codipo='2' LIMIT 1");
				}elseif($peso<=0.1){
					$val = $this->datasis->dameval("SELECT valor  FROM tbprecio  WHERE codpre='2' LIMIT 1");
					$ipt = $this->datasis->dameval("SELECT monipo FROM tbipostel WHERE codipo='3' LIMIT 1");
				}elseif($peso<=0.5){
					$val = $this->datasis->dameval("SELECT valor  FROM tbprecio  WHERE codpre='2' LIMIT 1");
					$ipt = $this->datasis->dameval("SELECT monipo FROM tbipostel WHERE codipo='4' LIMIT 1");
				}elseif($peso<=1){
					$val = $this->datasis->dameval("SELECT valor  FROM tbprecio  WHERE codpre='2' LIMIT 1");
					$ipt = $this->datasis->dameval("SELECT monipo FROM tbipostel WHERE codipo='5' LIMIT 1");
				}elseif($peso<=1.5){
					$val = $this->datasis->dameval("SELECT valor  FROM tbprecio  WHERE codpre='2' LIMIT 1");
					$ipt = $this->datasis->dameval("SELECT monipo FROM tbipostel WHERE codipo='6' LIMIT 1");
				}elseif($peso<=2){
					$val = $this->datasis->dameval("SELECT valor  FROM tbprecio  WHERE codpre='2' LIMIT 1");
					$ipt = $this->datasis->dameval("SELECT monipo FROM tbipostel WHERE codipo='7' LIMIT 1");
				}else{
					$val = $this->datasis->dameval("SELECT valor FROM tbprecio  WHERE codpre='3' LIMIT 1");
					$ipt = 0;
				}

				$pad = 0;
			}

			$rt['distancia']=$dis;
			$rt['iposte']   =$ipt;
			$rt['monto']    =$val;
			$rt['peso']     =$pad;
		}

		echo json_encode($rt);
	}

	function instalar(){
		if (!$this->db->table_exists('tbenvio')) {
			$mSQL="CREATE TABLE `tbenvio` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `nrofact` varchar(20) DEFAULT NULL,
			  `fecenv` date DEFAULT NULL,
			  `codofi_org` varchar(10) DEFAULT NULL,
			  `codofi_des` varchar(10) DEFAULT NULL,
			  `codcli_org` varchar(20) DEFAULT NULL,
			  `nomcli_org` varchar(200) DEFAULT NULL,
			  `telf_org` varchar(30) DEFAULT NULL,
			  `codcli_des` varchar(20) DEFAULT NULL,
			  `nomcli_des` varchar(200) DEFAULT NULL,
			  `telf_des` varchar(30) DEFAULT NULL,
			  `dirdes` varchar(300) DEFAULT NULL,
			  `exon` varchar(1) DEFAULT NULL,
			  `anula` varchar(1) DEFAULT NULL,
			  `kilo` decimal(10,2) DEFAULT NULL,
			  `usua` varchar(20) DEFAULT NULL,
			  `puertap` decimal(10,2) DEFAULT NULL,
			  `fledes` varchar(1) DEFAULT NULL,
			  `tipo` varchar(20) DEFAULT NULL,
			  `descrip` varchar(200) DEFAULT NULL,
			  `cant` int(11) DEFAULT NULL,
			  `peso` decimal(10,2) DEFAULT NULL,
			  `envio` decimal(10,2) DEFAULT NULL,
			  `volumen` decimal(10,2) DEFAULT NULL,
			  `seguro` decimal(10,2) DEFAULT NULL,
			  `iva` decimal(10,2) DEFAULT NULL,
			  `ipostel` decimal(10,2) DEFAULT NULL,
			  `ref` varchar(20) DEFAULT NULL,
			  `fom_pag` varchar(20) CHARACTER SET latin1 COLLATE latin1_bin DEFAULT NULL,
			  `nro_post` varchar(20) DEFAULT NULL,
			  `orden` varchar(20) DEFAULT NULL,
			  `codstat` varchar(2) DEFAULT NULL,
			  `fecent` date DEFAULT NULL,
			  `fecrec` date DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `codofi_org` (`codofi_org`),
			  KEY `anula` (`anula`),
			  KEY `nrofact` (`nrofact`),
			  KEY `fecenv` (`fecenv`),
			  KEY `codofi_des` (`codofi_des`),
			  KEY `orden` (`orden`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbenvio');
		//if(!in_array('id',$campos)){
		//	$this->db->simple_query('ALTER TABLE tbenvio DROP PRIMARY KEY');
		//	$this->db->simple_query('ALTER TABLE tbenvio ADD UNIQUE INDEX numero (numero)');
		//	$this->db->simple_query('ALTER TABLE tbenvio ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		//}
	}
}
