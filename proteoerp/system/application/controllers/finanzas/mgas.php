<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
class Mgas extends validaciones {

	var $mModulo = 'MGAS';
	var $titp    = 'Maestro de gastos';
	var $tits    = 'Maestro de gastos';
	var $url     = 'finanzas/mgas/';

	function Mgas(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
	}

	function index(){
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		$this->datasis->modulo_nombre( 'MGAS', $ventana=0 );
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

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

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
		$param['listados']    = $this->datasis->listados('MGAS', 'JQ');
		$param['otros']       = $this->datasis->otros('MGAS', 'JQ');
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
		function mgasadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function mgasedit(){
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
		function mgasshow(){
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
		function mgasdel() {
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
		$("#fedita").dialog({
			autoOpen: false, height: 400, width: 700, modal: true,
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
			autoOpen: false, height: 400, width: 700, modal: true,
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

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
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


		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('nom_grup');
		$grid->label('Nombre de grupo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
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


		$grid->addField('medida');
		$grid->label('Medida');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('fraxuni');
		$grid->label('Fracci&oacute;n x Unidad');
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


		$grid->addField('minimo');
		$grid->label('M&iacute;nimo');
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


		$grid->addField('maximo');
		$grid->label('M&aacute;ximo');
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


		$grid->addField('ultimo');
		$grid->label('&Uacute;ltimo');
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


		$grid->addField('promedio');
		$grid->label('Promedio');
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


		$grid->addField('unidades');
		$grid->label('Unidades');
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


		$grid->addField('fraccion');
		$grid->label('Fracci&oacute;n');
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


		$grid->addField('cuenta');
		$grid->label('Cuenta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		/*$grid->addField('tasa1');
		$grid->label('Tasa');
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


		$grid->addField('base1');
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


		$grid->addField('desde1');
		$grid->label('Desde');
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


		$grid->addField('tasa2');
		$grid->label('Tasa2');
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


		$grid->addField('base2');
		$grid->label('Base2');
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


		$grid->addField('desde2');
		$grid->label('Desde2');
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


		$grid->addField('tasa3');
		$grid->label('Tasa3');
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


		$grid->addField('base3');
		$grid->label('Base3');
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


		$grid->addField('desde3');
		$grid->label('Desde3');
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


		$grid->addField('tasa4');
		$grid->label('Tasa4');
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


		$grid->addField('base4');
		$grid->label('Base4');
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


		$grid->addField('desde4');
		$grid->label('Desde4');
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
		));*/


		$grid->addField('amorti');
		$grid->label('Amortizaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('dacumu');
		$grid->label('Depreciaci&oacute;n Acumulada');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('rica');
		$grid->label('Ret.ICA');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('reten');
		$grid->label('Retenci&oacute;n Natural');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('retej');
		$grid->label('Retenci&oacute;n Jur&iacute;dica');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
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
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('MGAS','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('MGAS','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('MGAS','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('MGAS','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: mgasadd, editfunc: mgasedit, delfunc: mgasdel, viewfunc: mgasshow');

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
		$mWHERE = $grid->geneTopWhere('mgas');

		$response   = $grid->getData('mgas', array(array()), array(), false, $mWHERE );
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
		$mcodp  = "codigo";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM mgas WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('mgas', $data);
					echo "Registro Agregado";

					logusu('MGAS',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM mgas WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM mgas WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE mgas SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("mgas", $data);
				logusu('MGAS',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('mgas', $data);
				logusu('MGAS',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$codigo = $this->datasis->dameval("SELECT codigo FROM mgas WHERE id=$id");
			$check  = $this->datasis->dameval("SELECT COUNT(*) FROM gitser WHERE codigo=".$this->db->escape($codigo));
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM mgas WHERE id=$id ");
				logusu('MGAS',"Registro ".$this->db->escape($codigo)." ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$link=site_url('finanzas/mgas/ultimo');

		$script ='
		function ultimo(){
			$.ajax({
				url: "'.$link.'",
				success: function(msg){
				  alert( "El ultimo codigo ingresado fue: " + msg );
			}
		});
		}

		function grupo(){
			t=$("#grupo").val();
			a=$("#grupo :selected").text();
			$("#nom_grup").val(a);
		}

		$(function() {
			$(".inputnum").numeric(".");
			$("#grupo").change(function(){
				t=$("#grupo").val();
				a=$("#grupo :selected").text();
				$("#nom_grup").val(a);
			}).change();
		});';

		$qformato=$this->datasis->formato_cpla();

		$mCPLA=array(
			'tabla'    => 'cpla',
			'columnas' => array(
			'codigo'   => 'C&oacute;digo',
			'descrip'  => 'Descripci&oacute;n'),
			'filtro'   => array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar' => array('codigo'=>'cuenta'),
			'titulo'   => 'Buscar Cuenta',
			'where'    => "codigo LIKE \"$qformato\"",
		);

		$bcpla =$this->datasis->modbus($mCPLA);

		$atts = array(
				'width'     =>'800',
				'height'    =>'600',
				'scrollbars'=>'yes',
				'status'    =>'yes',
				'resizable' =>'yes',
				'screenx'   =>'5',
				'screeny'   =>'5');

		$edit = new DataEdit('Maestro de Gastos', 'mgas');
		$edit->script($script, 'create');
		$edit->script($script, 'modify');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert', '_pre_insert');
		$edit->pre_process( 'update', '_pre_update');
		$edit->pre_process( 'delete', '_pre_delete');


		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo codigo ingresado" onclick="">Consultar ultimo codigo</a>';
		$edit->codigo= new inputField('C&oacute;digo', 'codigo');
		$edit->codigo->mode='autohide';
		$edit->codigo->size = 12;
		$edit->codigo->maxlength = 6;
		$edit->codigo->rule = 'trim|required||alpha_numeric|strtoupper|callback_chexiste';
		$edit->codigo->append($ultimo);

		$edit->descrip= new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->size = 30;

		$edit->tipo= new dropdownField('Tipo', 'tipo');
		$edit->tipo->style ='width:100px;';
		$edit->tipo->option('G','Gasto');
		$edit->tipo->option('I','Inventario');
		$edit->tipo->option('S','Suministro');
		$edit->tipo->option('A','Activo Fijo');

		$edit->grupo= new dropdownField('Grupo', 'grupo');
		$edit->grupo->options('SELECT grupo, CONCAT(grupo," - ",nom_grup) nom_grup from grga order by nom_grup');
		$edit->grupo->style ="width:200px;";

		$lcuent=anchor_popup("/contabilidad/cpla/dataedit/create",image('list_plus.png','Agregar',array("border"=>"0")),$atts);
		$edit->cuenta    = new inputField("Cta. Contable", "cuenta");
		$edit->cuenta->size = 12;
		$edit->cuenta->maxlength = 15;
		$edit->cuenta->rule = 'trim|existecpla';
		$edit->cuenta->append($bcpla);
		$edit->cuenta->append($lcuent);
		$edit->cuenta->readonly=true;

/*
		$edit->iva = new inputField("IVA", "iva");
		$edit->iva->css_class='inputnum';
		$edit->iva->size =9;
		$edit->iva->maxlength =5;
		$edit->iva->rule ="trim";
*/

		$ivas=$this->datasis->ivaplica();
		$edit->iva = new dropdownField('IVA %', 'iva');
		foreach($ivas as $tasa=>$ivamonto){
			$edit->iva->option($ivamonto,nformat($ivamonto));
		}
		$edit->iva->style='width:100px;';
		$edit->iva->insertValue=$ivas['tasa'];
		//$edit->iva->onchange='calculos(\'S\');';

		//$edit->medida    = new inputField("Unidad Medida", "medida");
		//$edit->medida->size = 5;

		$AddUnidad='<a href="javascript:add_unidad();" title="Haz clic para Agregar una unidad nueva">'.image('list_plus.png','Agregar',array("border"=>"0")).'</a>';
		$edit->medida = new dropdownField('Medida','medida');
		$edit->medida->style='width:100px;';
		$edit->medida->option('','Seleccionar');
		$edit->medida->options('SELECT unidades, unidades AS valor FROM unidad ORDER BY unidades');
		$edit->medida->append($AddUnidad);

		$edit->fraxuni   = new inputField('Cant. X Caja', 'fraxuni');
		$edit->fraxuni->css_class='inputnum';//no sirve
		$edit->fraxuni->group = 'Existencias';
		$edit->fraxuni->size = 5;

		$edit->ultimo    = new inputField('Costo', 'ultimo');
		$edit->ultimo->css_class='inputnum';//no sirve
		$edit->ultimo->size = 9;

		$edit->promedio  = new inputField('Promedio', 'promedio');
		$edit->promedio->css_class='inputnum';//no sirve
		$edit->promedio->size = 9;

		$edit->minimo    = new inputField('M&iacute;nima', 'minimo');
		$edit->minimo->css_class='inputnum';//no sirve
		$edit->minimo->group = 'Existencias';
		$edit->minimo->size = 5;

		$edit->maximo    = new inputField('M&aacute;xima', 'maximo');
		$edit->maximo->css_class='inputnum';//no sirve
		$edit->maximo->group = 'Existencias';
		$edit->maximo->size = 5;

		$edit->unidades  = new inputField('Cajas', 'unidades');
		$edit->unidades->css_class='inputnum';//no sirve
		$edit->unidades->group = 'Existencias';
		$edit->unidades->size = 5;

		$edit->fraccion  = new inputField('Fracci&oacute;nes', 'fraccion');
		$edit->fraccion->css_class='inputnum';//no sirve
		$edit->fraccion->group = 'Existencias';
		$edit->fraccion->size = 5;

		$edit->reten= new dropdownField('Natural.', 'reten');
		$edit->reten->option('','Ninguno');
		$edit->reten->options('SELECT codigo, CONCAT(codigo," - ",activida) val FROM rete WHERE tipo="NR" ORDER BY codigo');
		$edit->reten->style ='width:220px;';

		$edit->retej= new dropdownField("Jur&iacute;dica.", "retej");
		$edit->retej->option('','Ninguno');
		$edit->retej->options('SELECT codigo, CONCAT(codigo," - ",activida) val FROM rete WHERE tipo="JD" ORDER BY codigo');
		$edit->retej->style ='width:220px;';

		$codigo=$edit->_dataobject->get('codigo');
		$edit->almacenes = new containerField('almacenes',$this->_detalle($codigo));
		$edit->almacenes->when = array('show','modify');

		//$edit->buttons("modify", "save", "undo", "back");
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
			$this->load->view('view_mgas', $conten);
		}

	}

	function chexiste(){
		$codigo=$this->input->post('codigo');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM mgas WHERE codigo='$codigo'");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT descrip FROM mgas WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el gasto $nombre");
			return FALSE;
		}
	}

	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT codigo FROM mgas ORDER BY codigo DESC LIMIT 1");
		echo $ultimo;
	}

	function _detalle($codigo){
		$salida='';
		/*
		if(!empty($codigo)){
			$this->rapyd->load('dataedit','datagrid');

			$grid = new DataGrid('Cantidad por almac&eacute;n');
			$grid->db->select('ubica,locali,cantidad,fraccion');
			$grid->db->from('ubic');
			$grid->db->where('codigo',$codigo);

			$grid->column("Almacen"          ,"ubica" );
			$grid->column("Ubicaci&oacute;n" ,"locali");
			$grid->column("Cantidad"         ,"cantidad",'align="RIGHT"');
			$grid->column("Fracci&oacute;n"  ,"fraccion",'align="RIGHT"');

			$grid->build();
			$salida=$grid->output;
		}*/
		return $salida;
	}

	function consulta(){
		$this->rapyd->load('datagrid');
		$fields = $this->db->field_data('mgas');
		$url_pk = $this->uri->segment_array();
		$coun=0;
		$pk=array();

		foreach ($fields as $field){
			if($field->primary_key==1){
				$coun++;
				$pk[]=$field->name;
			}
		}
		$pk[]='codigo';

		$values=array_slice($url_pk,-$coun);
		$claves=array_combine (array_reverse($pk) ,$values );

		$grid = new DataGrid('Ultimos Movimientos');
		$grid->db->select( array('a.fecha', 'a.numero','a.descrip', 'a.proveed', 'b.nombre', 'a.precio', 'a.iva', 'a.importe') );
		$grid->db->from('gitser a');
		$grid->db->join('sprv b','a.proveed=b.proveed');
		$grid->db->where('a.codigo', $claves['codigo'] );
		$grid->db->where('a.fecha >', "curdate()-365" );
		$grid->db->orderby('fecha DESC');
		$grid->db->limit(6);

		$grid->column("Fecha",      "fecha" );
		$grid->column("Descripcion","descrip" );
		$grid->column("Proveed",    "proveed");
		//$grid->column("Nombre"  ,"nombre");
		$grid->column("Monto"   ,"<nformat><#precio#></nformat>",'align="RIGHT"');
		$grid->build();

		$grid1 = new DataGrid('Totales por Mes');
		$grid1->db->select( array('a.fecha', 'a.descrip', 'a.proveed', 'b.nombre', 'sum(a.precio) monto', 'a.iva', 'a.importe') );
		$grid1->db->from('gitser a');
		$grid1->db->join('sprv b','a.proveed=b.proveed');
		$grid1->db->where('a.codigo', $claves['codigo'] );
		$grid1->db->where('a.fecha >', "curdate()-365" );
		$grid1->db->groupby('fecha DESC ');
		$grid1->db->limit(6);

		$grid1->column("Fecha", "fecha" );
		$grid1->column("Monto", "<nformat><#monto#></nformat>",'align="RIGHT"');

		$grid1->build();

		$grid2 = new DataGrid('Totales por Proveedor');
		$grid2->db->select( array('a.fecha', 'a.proveed', 'b.nombre', 'sum(a.precio) monto') );
		$grid2->db->from('gitser a');
		$grid2->db->join('sprv b','a.proveed=b.proveed');
		$grid2->db->where('a.codigo', $claves['codigo'] );
		$grid2->db->where('a.fecha >', "curdate()-365" );
		$grid2->db->groupby('a.proveed');
		$grid2->db->orderby('monto DESC ');
		$grid2->db->limit(6);

		$grid2->column("Proveed" ,"proveed");
		$grid2->column("Nombre"  ,"nombre");
		$grid2->column("Monto"   ,"<nformat><#monto#></nformat>",'align="RIGHT"');

		$grid2->build();

		$descrip = $this->datasis->dameval("SELECT descrip FROM mgas WHERE codigo='".$claves['codigo']."'");
		$data['content'] = "
		<table width='100%'>
			<tr>
				<td valign='top'>
					<div style='border: 2px outset #EFEFEF;background: #EFEFFF '>".
					$grid1->output."
					</div>".
				"</td>
				<td valign='top'>
					<div style='border: 2px outset #EFEFEF;background: #EFFFEF '>".
					$grid2->output."
					</div>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<div style='border: 2px outset #EFEFEF;background: #FFFDE9 '>".
					$grid->output."
					</div>
				</td>
			</tr>
		</table>";

		$data["head"]     = script("plugins/jquery.numeric.pack.js");
		$data["head"]    .= script("plugins/jquery.floatnumber.js");
		$data["head"]    .= $this->rapyd->get_head();

		$data['title']    = '<h1>Consulta de Maestro de Gasto</h1>';
		$data["subtitle"] = "<div align='center' style='border: 2px outset #EFEFEF;background: #EFEFEF '><a href='javascript:javascript:history.go(-1)'>(".$claves['codigo'].") ".$descrip."</a></div>";
		$this->load->view('view_ventanas', $data);

	}

	function _pre_insert($do){
		//$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_update($do){
		//$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _pre_delete($do){
		$dbcodigo = $this->db->escape($do->get('codigo'));
		$check = 0;

		$check += $this->datasis->dameval('SELECT COUNT(*) AS cana FROM gitser WHERE codigo='.$dbcodigo);
		$check += $this->datasis->dameval('SELECT COUNT(*) AS cana FROM itords WHERE codigo='.$dbcodigo);
		$check += $this->datasis->dameval("SELECT COUNT(*) AS cana FROM conc   WHERE ctade=${dbcodigo} AND tipod='G'");
		$check += $this->datasis->dameval("SELECT COUNT(*) AS cana FROM conc   WHERE ctaac=${dbcodigo} AND tipoa='G'");

		if($check>0){
			$do->error_message_ar['pre_del']='No se puede eliminar el registro por tener movimiento.';
			return false;
		}
		return true;
	}

	function _post_insert($do){
		$primary = implode(',',$do->pk);
		$codigo  = $do->get('codigo');
		logusu($do->table,"Creo $this->tits codigo: ${codigo} id: ${primary}");
	}

	function _post_update($do){
		$primary = implode(',',$do->pk);
		$codigo  = $do->get('codigo');
		logusu($do->table,"Modifico $this->tits codigo: ${codigo} id: ${primary}");
	}

	function _post_delete($do){
		$primary = implode(',',$do->pk);
		$codigo  = $do->get('codigo');
		logusu($do->table,"Elimino $this->tits codigo: ${codigo} id: ${primary}");
	}

	function instalar(){
		$campos=$this->db->list_fields('mgas');
		if (!in_array('id',$campos)){
			$mSQL="ALTER TABLE `mgas`
			ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT,
			DROP PRIMARY KEY,
			ADD UNIQUE INDEX `unico` (`codigo`),
			ADD PRIMARY KEY (`id`);";
			$this->db->simple_query($mSQL);
		}

		if (!in_array('reten',$campos)) {
			$mSQL="ALTER TABLE mgas ADD COLUMN reten VARCHAR(4) NULL DEFAULT NULL AFTER rica, ADD COLUMN retej VARCHAR(4) NULL DEFAULT NULL AFTER reten";
			$this->db->simple_query($mSQL);
		}
	}
}
