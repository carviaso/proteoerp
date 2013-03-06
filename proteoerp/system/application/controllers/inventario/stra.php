<?php
class Stra extends Controller {
	var $mModulo = 'STRA';
	var $titp    = 'Transferencias de Inventario';
	var $tits    = 'Transferencias de Inventario';
	var $url     = 'inventario/stra/';
	var $genesal=true;

	function Stra(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'STRA', $ventana=0 );
	}

	function index(){
		$this->instalar();
		//$mSQL = "UPDATE itstra AS a JOIN stra AS b ON a.numero=b.numero SET a.idstra=b.id";
		//$this->db->simple_query($mSQL);
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	//Layout en la Ventana
	//******************************************************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 165, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'boton1','img'=>'assets/default/images/print.png','alt'=> 'Imprimir transferencia', 'label'=>'Reimprimir Documento'));
		$grid->wbotonadd(array('id'=>'brma'  ,'img'=>'images/caja-cerrada.png','alt'=> 'Movimiento por RMA', 'label'=>'Traslado por RMA'));
		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );


		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar Transferencia'),
			array('id'=>'fshow' ,  'title'=>'Ver Transferencia')
		);

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('STRA', 'JQ');
		$param['otros']        = $this->datasis->otros('STRA', 'JQ');
		$param['centerpanel']  = $centerpanel;
		//$param['funciones']    = $funciones;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	//Funciones de los Botones
	//******************************************************************
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function straadd() {
			$.post("'.site_url('inventario/stra/dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function strashow() {
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
		function straedit() {
			$.prompt("No se pueden modificar, debe hacer un reverso!");
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
		jQuery("#brma").click( function(){
			$.post("'.site_url('inventario/stra/dataeditrma/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			});
		});';

		$bodyscript .= '
		jQuery("#boton1").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('formatos/ver/STRA').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
			} else { $.prompt("<h1>Por favor Seleccione una tranferencia</h1>");}
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
						type: "POST", dataType: "text", async: false,
						url: murl,
						data: $("#df1").serialize(),
						success: function(r,s,x){
							try{
								var json = JSON.parse(r);
								if (json.status == "A"){
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									//window.open(\''.site_url('formatos/ver/STRA').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
									'.$this->datasis->jwinopen(site_url('formatos/ver/STRA').'/\'+json.pk.id+\'/id\'').';
									//apprise("Registro Guardado");
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
						$(this).dialog( "close" );
					},
				},
				close: function() {
					$("#fshow").html("");
				}
			});';


		$bodyscript .= '});'."\n";

		$bodyscript .= "\n</script>\n";
		return $bodyscript;
	}

	//******************************************************************
	//Definicion del Grid y la Forma
	//******************************************************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('numero');
		$grid->label('N&uacute;;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
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


		$grid->addField('envia');
		$grid->label('Env&iacute;a');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('recibe');
		$grid->label('Recibe');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('observ1');
		$grid->label('Observaciones');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('observ2');
		$grid->label('Observaciones 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('totalg');
		$grid->label('Monto total');
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


		/*$grid->addField('tratot');
		$grid->label('Tratot');
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
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
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


		$grid->addField('gasto');
		$grid->label('Gasto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		/*$grid->addField('numeen');
		$grid->label('Numeen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('numere');
		$grid->label('Numere');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));*/


		$grid->addField('ordp');
		$grid->label('O.Prod.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('esta');
		$grid->label('Estaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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
			'align'    => "'center'",
			'width'    => 60,
			'editable' => 'false',
			'search'   => 'false',
			'hidden'   => 'true'
		));*/

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('192');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}'
		);

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('STRA','INCLUIR%' ));
		$grid->setEdit( false );  //  $this->datasis->sidapuede('STRA','MODIFICA%'));
		$grid->setDelete(false);
		//$grid->setDelete( $this->datasis->sidapuede('STRA','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('STRA','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: straadd,editfunc: straedit,viewfunc: strashow');

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

	//******************************************************************
	// Busca la data en el Servidor por json
	//******************************************************************
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('stra');

		$response   = $grid->getData('stra', array(array()), array(), false, $mWHERE, 'numero', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion
	//******************************************************************
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$mcodp  = "numero";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);

		if($oper == 'edit'){
			$numero = $data['numero'];
			unset($data['numero']);
			$this->db->where("id", $id);
			$this->db->update('stra', $data);
			logusu('STRA',"Transferencias  ".$numero." MODIFICADO");
			echo "Transferencia Modificada";
		};

	}

	//******************************************************************
	//Definicion del Grid y la Forma
	//******************************************************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
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

/*
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


		$grid->addField('anteri');
		$grid->label('Anteri');
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

		$grid->showpager( true );
		$grid->setWidth('');
		$grid->setHeight('140');
		//$grid->setTitle($this->titp);
		$grid->setfilterToolbar( false );
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd( false );    // $this->datasis->sidapuede('ITSTRA','INCLUIR%' ));
		$grid->setEdit(false );    // $this->datasis->sidapuede('ITSTRA','MODIFICA%'));
		$grid->setDelete(false );  // $this->datasis->sidapuede('ITSTRA','BORR_REG%'));
		$grid->setSearch( false ); // $this->datasis->sidapuede('ITSTRA','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		//$grid->setBarOptions("\t\taddfunc: itstraadd,\n\t\teditfunc: itstraedit");

		#Set url
		//$grid->setUrlput(site_url($this->url.'setdata/'));

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
	//******************************************************************
	function getdatait($id = 0){
		if ($id === 0 ){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM stra");
		}
		if(empty($id)) return '';
		$numero   = $this->datasis->dameval("SELECT numero FROM stra WHERE id=$id");

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM itstra WHERE numero='$numero' ORDER BY descrip ";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;

	}

	//******************************************************************
	// Guarda la Informacion
	//******************************************************************
	function setDatait(){
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
				$check = $this->datasis->dameval("SELECT count(*) FROM itstra WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('itstra', $data);
					echo "Registro Agregado";

					logusu('ITSTRA',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM itstra WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM itstra WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE itstra SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("itstra", $data);
				logusu('ITSTRA',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('itstra', $data);
				logusu('ITSTRA',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM itstra WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM itstra WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM itstra WHERE id=$id ");
				logusu('ITSTRA',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}


	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n',
				'precio1' =>'Precio 1',
				'precio2' =>'Precio 2',
				'precio3' =>'Precio 3',
				'existen' =>'Existencia',
				'peso'=>'Peso'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descrip_<#i#>'),
			'where'   =>'activo = "S" AND tipo="Articulo"',
			'script'  =>array('post_modbus("<#i#>")'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  =>'Busqueda de producto en inventario');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$script="
		function post_add_itstra(id){
			$('#cantidad_'+id).numeric('.');
			return true;
		}";

		$do = new DataObject('stra');
		$do->rel_one_to_many('itstra', 'itstra', 'numero');
		//$do->rel_pointer('itstra','sinv','itstra.codigo=sinv.codigo','sinv.descrip as sinvdescrip');

		$edit = new DataDetails('Transferencia', $do);
		//$edit->back_url = $this->back_dataedit;
		$edit->set_rel_title('itstra','Producto <#o#>');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');
		$edit->post_process('insert','_post_insert');

		$edit->numero= new inputField('N&uacute;mero', 'numero');
		$edit->numero->mode='autohide';
		$edit->numero->size=10;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->fecha = new  dateonlyField('Fecha', 'fecha');
		$edit->fecha->rule='required|chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		$edit->envia = new dropdownField('Env&iacute;a', 'envia');
		$edit->envia->option('','Seleccionar');
		$edit->envia->options('SELECT ubica, CONCAT(ubides," (",ubica,")") FROM caub WHERE invfis<>"S" ORDER BY ubides');
		$edit->envia->rule ='required';
		$edit->envia->style='width:200px;';

		$edit->recibe = new dropdownField('Recibe', 'recibe');
		$edit->recibe->option('','Seleccionar');
		$edit->recibe->options('SELECT ubica, CONCAT(ubides," (",ubica,")") FROM caub WHERE invfis<>"S" ORDER BY ubides');
		$edit->recibe->rule ='required|callback_chrecibe';
		$edit->recibe->style='width:200px;';

		$edit->observ1 = new inputField('Observaci&oacute;n','observ1');
		$edit->observ1->rule='max_length[60]|trim';
		$edit->observ1->size =32;
		$edit->observ1->maxlength =30;

		//**************************************************************
		// Comienza el Detalle
		//**************************************************************
		$edit->codigo = new inputField('C&oacute;digo <#o#>', 'codigo_<#i#>');
		$edit->codigo->db_name='codigo';
		$edit->codigo->append($btn);
		$edit->codigo->rule = 'trim|required';
		$edit->codigo->rel_id='itstra';
		$edit->codigo->maxlength=15;
		$edit->codigo->size     =15;

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip_<#i#>');
		$edit->descrip->db_name  = 'descrip';
		$edit->descrip->rel_id   = 'itstra';
		$edit->descrip->type     = 'inputhidden';
		$edit->descrip->maxlength= 45;
		$edit->descrip->size     = 40;

		$edit->cantidad = new inputField('Cantidad', 'cantidad_<#i#>');
		$edit->cantidad->db_name  ='cantidad';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->rel_id   ='itstra';
		$edit->cantidad->rule     ='numeric|mayorcero|required';
		$edit->cantidad->maxlength=10;
		$edit->cantidad->autocomplete=false;
		$edit->cantidad->size     =10;
		//Fin del detalle

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		$edit->buttons('save', 'undo', 'add','back','add_rel');

		if($this->genesal){
			$edit->on_save_redirect=false;
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
				$data['content'] = $this->load->view('view_stra', $conten, false);
			}
		}else{
			$edit->on_save_redirect=false;
			$edit->build();
			if($edit->on_success()){
				$rt= 'Transferencia Guardada';
			}elseif($edit->on_error()){
				$rt= html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
			return $rt;
		}
	}

	function dataeditrma(){
		$this->rapyd->load('dataobject','datadetails');
		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n',
				'precio1' =>'Precio 1',
				'precio2' =>'Precio 2',
				'precio3' =>'Precio 3',
				'existen' =>'Existencia',
				'peso'=>'Peso'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descrip_<#i#>'),
			'where'   =>'activo = "S" AND tipo="Articulo"',
			'script'  =>array('post_modbus("<#i#>")'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  =>'Busqueda de producto en inventario');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$script="
		function post_add_itstra(id){
			$('#cantidad_'+id).numeric('.');
			return true;
		}";

		$do = new DataObject('stra');
		$do->pointer('sprv' ,'sprv.proveed=stra.proveed','sprv.nombre AS sprvnombre','left');
		$do->rel_one_to_many('itstra', 'itstra', 'numero');
		//$do->rel_pointer('itstra','sinv','itstra.codigo=sinv.codigo','sinv.descrip as sinvdescrip');

		$edit = new DataDetails('Transferencia', $do);
		//$edit->back_url = $this->back_dataedit;
		$edit->set_rel_title('itstra','Producto <#o#>');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');
		$edit->post_process('insert','_post_insert');

		$edit->numero= new inputField('N&uacute;mero', 'numero');
		$edit->numero->mode='autohide';
		$edit->numero->size=10;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->proveed = new inputField('Proveedor', 'proveed');
		$edit->proveed->rule     = 'trim|required';
		$edit->proveed->size     = 8;
		$edit->proveed->maxlength= 5;
		$edit->proveed->autocomplete=false;
		$edit->proveed->rule     = 'required';

		$edit->nombre = new inputField('', 'sprvnombre');
		$edit->nombre->db_name  = 'sprvnombre';
		$edit->nombre->pointer  = true;
		$edit->nombre->type     = 'inputhidden';
		$edit->nombre->rule     = 'required';

		$edit->fecha = new  dateonlyField('Fecha', 'fecha');
		$edit->fecha->rule='required|chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =14;

		$edit->envia = new dropdownField('Env&iacute;a', 'envia');
		$edit->envia->option('','Seleccionar');
		$edit->envia->options('SELECT ubica, CONCAT(ubides," (",ubica,")") FROM caub WHERE invfis<>"S" ORDER BY ubides');
		$edit->envia->rule ='required|callback_crma';
		$edit->envia->style='width:180px;';

		$edit->recibe = new dropdownField('Recibe', 'recibe');
		$edit->recibe->option('','Seleccionar');
		$edit->recibe->options('SELECT ubica, CONCAT(ubides," (",ubica,")") FROM caub WHERE invfis<>"S" ORDER BY ubides');
		$edit->recibe->rule ='required|callback_chrecibe|callback_crma';
		$edit->recibe->style='width:180px;';

		$edit->condiciones = new textareaField('Condiciones:','condiciones');
		$edit->condiciones->rule  = 'trim|required';
		$edit->condiciones->style = 'width:98%;';
		$edit->condiciones->cols = 70;
		$edit->condiciones->rows = 3;

		//**************************************************************
		// Comienza el Detalle
		//**************************************************************
		$edit->codigo = new inputField('C&oacute;digo <#o#>', 'codigo_<#i#>');
		$edit->codigo->db_name='codigo';
		$edit->codigo->append($btn);
		$edit->codigo->rule = 'trim|required';
		$edit->codigo->rel_id='itstra';
		$edit->codigo->maxlength=15;
		$edit->codigo->size     =15;

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip_<#i#>');
		$edit->descrip->db_name  = 'descrip';
		$edit->descrip->rel_id   = 'itstra';
		$edit->descrip->type     = 'inputhidden';
		$edit->descrip->maxlength= 45;
		$edit->descrip->size     = 40;

		$edit->cantidad = new inputField('Cantidad', 'cantidad_<#i#>');
		$edit->cantidad->db_name  ='cantidad';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->rel_id   ='itstra';
		$edit->cantidad->rule     ='numeric|mayorcero|required';
		$edit->cantidad->maxlength=10;
		$edit->cantidad->autocomplete=false;
		$edit->cantidad->size     =10;
		//Fin del detalle

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		$edit->buttons('save', 'undo', 'add','back','add_rel');

		if($this->genesal){
			$edit->on_save_redirect=false;
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
				$data['content'] = $this->load->view('view_strarma', $conten, false);
			}
		}else{
			$edit->on_save_redirect=false;
			$edit->build();
			if($edit->on_success()){
				$rt= 'Transferencia Guardada';
			}elseif($edit->on_error()){
				$rt= html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
			return $rt;
		}
	}


	//******************************************************************
	//  Aparta Mercancia en Ordenes de Produccion
	//******************************************************************
	function dataeditordp($numero,$esta){
		if(!isset($_POST['codigo_0'])){
			//SELECT c.codigo
			//,COALESCE(b.cantidad*IF(tipoordp='E',-1,1),0) AS tracana
			//,c.cantidad
			//FROM stra AS a
			//JOIN itstra AS b ON a.numero=b.numero
			//RIGHT JOIN ordpitem AS c ON a.ordp=c.numero AND b.codigo=c.codigo
			//WHERE c.numero='00000019'
		}
		$id_ordp=$this->datasis->dameval('SELECT id FROM ordp WHERE numero='.$this->db->escape($numero));
		$this->back_dataedit='inventario/ordp/dataedit/show/'.$id_ordp;
		$this->rapyd->load('dataobject','datadetails');
		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n',
				'precio1'=>'Precio 1',
				'precio2'=>'Precio 2',
				'precio3'=>'Precio 3',
				'existen'=>'Existencia',
				'peso'=>'Peso'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descrip_<#i#>'),
			'where'   =>'activo = "S" AND tipo="Articulo"',
			'script'  =>array('post_modbus("<#i#>")'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  =>'Busqueda de producto en inventario');
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$script="
		function post_add_itstra(id){
			$('#cantidad_'+id).numeric('.');
			return true;
		}";

		$do = new DataObject('stra');
		$do->rel_one_to_many('itstra', 'itstra', 'numero');
		//$do->rel_pointer('itstra','sinv','itstra.codigo=sinv.codigo','sinv.descrip as sinvdescrip');

		$edit = new DataDetails('Transferencia', $do);
		$edit->back_url = $this->back_dataedit;
		$edit->set_rel_title('itstra','Producto <#o#>');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->pre_process('insert','_pre_ordp_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');
		$edit->post_process('insert','_post_insert');

		$edit->numero= new inputField('N&uacute;mero', 'numero');
		$edit->numero->mode='autohide';
		$edit->numero->size=10;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->ordp= new inputField('Orden de producci&oacute;n', 'ordp');
		$edit->ordp->mode='autohide';
		$edit->ordp->size=10;
		$edit->ordp->rule='required|callback_chordp';
		$edit->ordp->insertValue=$numero;
		$edit->ordp->when=array('show','modify');

		$edit->fecha = new  dateonlyField('Fecha', 'fecha');
		$edit->fecha->rule='required|chfecha';
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size =12;

		$edit->esta = new  dropdownField('Estaci&oacute;n', 'esta');
		$edit->esta->option('','Seleccionar');
		$edit->esta->options('SELECT estacion,CONCAT(estacion,\'-\',nombre) AS lab FROM esta ORDER BY estacion');
		$edit->esta->rule   = 'required';
		$edit->esta->insertValue=$esta;
		$edit->esta->style  = 'width:150px;';

		$edit->tipoordp = new  dropdownField('Tipo de movimiento', 'tipoordp');
		$edit->tipoordp->option('','Seleccionar');
		$edit->tipoordp->option('E','Entrega');
		$edit->tipoordp->option('R','Retiro' );
		$edit->tipoordp->rule   = 'required|enum[E,R]';
		$edit->tipoordp->style  = 'width:150px;';

		$edit->observ1 = new inputField('Observaci&oacute;n','observ1');
		$edit->observ1->rule='max_length[60]|trim';
		$edit->observ1->size =32;
		$edit->observ1->maxlength =30;

		//comienza el detalle
		$edit->codigo = new inputField('C&oacute;digo <#o#>', 'codigo_<#i#>');
		$edit->codigo->db_name='codigo';
		$edit->codigo->append($btn);
		$edit->codigo->rule = 'trim|required|sinvexiste';
		$edit->codigo->rel_id='itstra';
		$edit->codigo->maxlength=15;
		$edit->codigo->size     =15;

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip_<#i#>');
		$edit->descrip->db_name  = 'descrip';
		$edit->descrip->type     = 'inputhidden';
		$edit->descrip->rel_id   = 'itstra';
		$edit->descrip->maxlength= 45;
		$edit->descrip->size     = 40;

		$edit->cantidad = new inputField('Cantidad', 'cantidad_<#i#>');
		$edit->cantidad->db_name  ='cantidad';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->rel_id   ='itstra';
		$edit->cantidad->rule     ='numeric|mayorcero|required';
		$edit->cantidad->maxlength=10;
		$edit->cantidad->autocomplete=false;
		$edit->cantidad->size     =10;
		//Fin del detalle

		$edit->estampa = new autoUpdateField('estampa',date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		$accion="javascript:buscaprod()";
		$edit->button_status('btn_terminar','Traer insumos',$accion,'TR','create');

		$edit->buttons('save', 'undo', 'back','add_rel');

		if($this->genesal){
			$edit->build();
			$conten['form']  =& $edit;
			$data['content'] = $this->load->view('view_stra_ordp', $conten,true);
			$data['style']   = style('redmond/jquery-ui.css');

			$data['script']  = script('jquery.js');
			$data['script'] .= script('jquery-ui.js');
			$data['script'] .= script("jquery-impromptu.js");
			$data['script'] .= script('plugins/jquery.numeric.pack.js');
			$data['script'] .= script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
			$data['script'] .= script('plugins/jquery.floatnumber.js');
			$data['script'] .= phpscript('nformat.js');
			$data['content'] = $this->load->view('view_stra_ordp', $conten,true);
			$data['head']    = $this->rapyd->get_head();
			$data['title']   = heading('Transferencias de inventario para producci&oacute;n');
			$this->load->view('view_ventanas', $data);
		}else{
			$edit->on_save_redirect=false;
			$edit->build();

			if($edit->on_success()){
				$rt= 'Transferencia Guardada';
			}elseif($edit->on_error()){
				$rt= html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
			return $rt;
		}
	}

	function chordp($numero){
		$this->db->from('ordp');
		$this->db->where('status <>','T');
		$this->db->where('numero',$numero);
		$cana=$this->db->count_all_results();
		if($cana>0){
			return true;
		}
		$this->validation->set_message('chordp','No existe una orden de producci&oacute;n abierta con el n&uacute;mero '.$numero);
		return false;
	}

	//******************************************************************
	//Hace la consolidacion del cierre de los productos lacteos
	//******************************************************************
	function consolidar($id=null){
		if(empty($id)){
			$rt=array(
				'status' =>'B',
				'mensaje'=> utf8_encode('Faltan parametros.'),
				'pk'     =>''
			);
			echo json_encode($rt);
			return false;
		}
		$dbid = $this->db->escape($id);
		$error= 0;
		$lcierre_cana=$this->datasis->dameval('SELECT COUNT(*) FROM lcierre WHERE status=\'A\' AND id='.$dbid);
		if($lcierre_cana>0){

			$it_cana  = $this->datasis->dameval('SELECT COUNT(*) FROM itlcierre WHERE peso >0 AND id_lcierre='.$dbid);
			if(empty($it_cana)){
				$rt=array(
					'status' =>'B',
					'mensaje'=> utf8_encode('No puede cerrar el día sin producción.'),
					'pk'     =>''
				);
				echo json_encode($rt);
				return false;
			}

			$fecha    = $this->datasis->dameval('SELECT fecha FROM lcierre WHERE id='.$dbid);
			$dbfecha  = $this->db->escape($fecha);
			$sinvlec  = $this->datasis->damerow('SELECT codigo,descrip FROM sinv WHERE descrip LIKE \'%LECHE%CRUDA%\' LIMIT 1');
			$almacen  = $this->datasis->traevalor('ALMACEN','Almacen principal');

			$inventario= $this->datasis->dameval("SELECT SUM(inventario)        AS val FROM lprod WHERE fecha=$dbfecha"); //Litros usado de inventario
			$recibido  = $this->datasis->dameval("SELECT SUM(litros)            AS val FROM lrece WHERE fecha=$dbfecha"); //Litros recibidos
			$producido = $this->datasis->dameval("SELECT SUM(litros-inventario) AS val FROM lprod WHERE fecha=$dbfecha"); //Litros recibidos usados en produccion

			$enfria = $recibido-$producido; //Litros que quedan para enfriar

			$this->genesal=false;
			$mSQL="INSERT IGNORE INTO caub  (ubica,ubides,gasto) VALUES ('PROD','ALMACEN DE PRODUCCION','S')";
			$this->db->simple_query($mSQL);

			//Saca los productos realizados
			$_POST=array(
				'btn_submit' => 'Guardar',
				'envia'      => 'PROD',
				'fecha'      => date('d/m/Y'),
				'recibe'     => $almacen,
				'observ1'    => 'PRODUCCION DE LACTEOS '.$id
			);

			$sel=array('a.codigo','b.descrip','a.peso');
			$this->db->select($sel);
			$this->db->from('itlcierre AS a');
			$this->db->join('sinv AS b','a.codigo=b.codigo');
			$this->db->where('a.id_lcierre' , $id);
			$this->db->where('a.peso >' , 0);
			$mSQL_2 = $this->db->get();
			$row =$mSQL_2->result();

			foreach ($row as $ind=>$itrow){
				$iind='codigo_'.$ind;
				$_POST[$iind] = $itrow->codigo;
				$iind='descrip_'.$ind;
				$_POST[$iind] = $itrow->descrip;
				$iind='cantidad_'.$ind;
				$_POST[$iind] = $itrow->peso;
			}
			//Mete la leche sobrante del dia a inventario
			if($enfria > 0){
				$ind++;
				$iind='codigo_'.$ind;
				$_POST[$iind] = $sinvlec['codigo'];
				$iind='descrip_'.$ind;
				$_POST[$iind] = $sinvlec['descrip'];
				$iind='cantidad_'.$ind;
				$_POST[$iind] = $enfria ;
			}
			//Fin de la leche sobrante

			$rt=$this->dataedit();
			if(strripos($rt,'Guardada')){
				$data = array('status' => 'C');
				$this->db->where('id', $id);
				$this->db->update('lcierre', $data);
			}else{
				$error++;
			}
			//fin de los productos realizados

			//Limpia las validaciones
			$this->validation->_error_array    = array();
			$this->validation->_rules          = array();
			$this->validation->_fields         = array();
			$this->validation->_error_messages = array();
			//Fin de la limpieza de validaciones

			//Consume la leche usada de inventario
			if($inventario>0){
				$_POST=array(
					'btn_submit' => 'Guardar',
					'envia'      => 'PROD',
					'fecha'      => date('d/m/Y'),
					'recibe'     => $almacen,
					'observ1'    => 'PRODUCCION DE LACTEOS '.$id.' CONSUMO DE LECHE'
				);

				$ind=0;
				$iind='codigo_'.$ind;
				$_POST[$iind] = $sinvlec['codigo'];
				$iind='descrip_'.$ind;
				$_POST[$iind] = $sinvlec['descrip'];
				$iind='cantidad_'.$ind;
				$_POST[$iind] = $inventario;

				$rt=$this->dataedit();
				if(!strripos($rt,'Guardada')){
					$error++;
				}
			}
			//Fin de la leche usada en inventario

			if($error==0){
				$rt=array(
					'status' =>'A',
					'mensaje'=> utf8_encode('Cierre realizado.'),
					'pk'     =>''
				);
				echo json_encode($rt);
				return true;
			}else{
				$rt=array(
					'status' =>'B',
					'mensaje'=> utf8_encode('Hubo problema al realizar el cierre.'),
					'pk'     =>''
				);
				echo json_encode($rt);
				return true;
			}
		}else{
			$rt=array(
				'status' =>'B',
				'mensaje'=> utf8_encode('Registro no encontrado o día ya fue cerrado.'),
				'pk'     =>''
			);
			echo json_encode($rt);
			return false;
		}
	}


	//******************************************************************
	//Hace la reservacion del material para una orden de produccion
	//******************************************************************
	function creadordp($id_ordp){
		$url='inventario/ordp/dataedit/show/'.$id_ordp;
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence($url, $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ? $persistence['back_uri'] : $url;

		$this->genesal=false;
		$mSQL="INSERT IGNORE INTO caub  (ubica,ubides,gasto) VALUES ('APRO','APARTADO DE PRODUCCION','N')";
		$this->db->simple_query($mSQL);
		$mSQL="INSERT IGNORE INTO caub  (ubica,ubides,gasto) VALUES ('PROD','ALMACEN DE PRODUCCION','S')";
		$this->db->simple_query($mSQL);

		$sel=array('a.fecha','a.almacen','a.numero','a.status','a.cana','a.reserva');
		$this->db->select($sel);
		$this->db->from('ordp AS a');
		$this->db->join('sinv AS b','a.codigo=b.codigo');
		$this->db->where('a.id' , $id_ordp);
		$mSQL_1 = $this->db->get();

		if($mSQL_1->num_rows() > 0){
			$row = $mSQL_1->row();
			$cana= $row->cana;
			if($row->reserva=='N'){
				$_POST=array(
					'btn_submit' => 'Guardar',
					'envia'      => $row->almacen,
					'fecha'      => dbdate_to_human($row->fecha),
					'recibe'     => 'APRO',
					'observ1'    => 'ORDEN DE PRODUCCION '.$row->numero
				);

				$sel=array('a.codigo','b.descrip','a.cantidad');
				$this->db->select($sel);
				$this->db->from('ordpitem AS a');
				$this->db->join('sinv AS b','a.codigo=b.codigo');
				$this->db->where('a.id_ordp' , $id_ordp);
				$mSQL_2 = $this->db->get();
				$ordpitem_row =$mSQL_2->result();

				foreach ($ordpitem_row as $id=>$itrow){
					$ind='codigo_'.$id;
					$_POST[$ind] = $itrow->codigo;
					$ind='descrip_'.$id;
					$_POST[$ind] = $itrow->descrip;
					$ind='cantidad_'.$id;
					$_POST[$ind] = $itrow->cantidad*$cana;
				}
				$rt=$this->dataedit();
				if(strripos($rt,'Guardada')){
					$data = array('status' => 'P','reserva'=>'S');
					$this->db->where('id', $id_ordp);
					$this->db->update('ordp', $data);
				}

				echo $rt.' '.anchor($back,'regresar');
			}else{
				redirect($back);
			}
		}else{
			exit();
		}
	}

	//******************************************************************
	//Termina la produccion
	//******************************************************************
	function creadordpt($id_ordp){
		$error=0;
		$url='inventario/ordp/dataedit/show/'.$id_ordp;
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence($url, $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ? $persistence['back_uri'] : $url;

		$this->genesal=false;
		$mSQL="INSERT IGNORE INTO caub (ubica,ubides,gasto) VALUES ('APRO','APARTADO DE PRODUCCION','N')";
		$this->db->simple_query($mSQL);
		$mSQL="INSERT IGNORE INTO caub (ubica,ubides,gasto) VALUES ('PROD','ALMACEN DE PRODUCCION','S')";
		$this->db->simple_query($mSQL);

		$sel=array('a.fecha','a.almacen','a.numero','a.status','a.cana','a.codigo','b.descrip');
		$this->db->select($sel);
		$this->db->from('ordp AS a');
		$this->db->join('sinv AS b','a.codigo=b.codigo');
		$this->db->where('a.id' , $id_ordp);
		$mSQL_1 = $this->db->get();

		if($mSQL_1->num_rows() > 0){
			$row = $mSQL_1->row();
			$codigo = $row->codigo;
			$cana   = $row->cana;
			if($row->status=='C'){
				//Hace la transferencia de lo producido al almacen
				$_POST=array(
					'btn_submit' => 'Guardar',
					'envia'      => 'PROD',
					'fecha'      => dbdate_to_human($row->fecha),
					'recibe'     => $row->almacen,
					'observ1'    => 'FIN ORDEN DE PROD. '.$row->numero
				);

				$id='1';
				$ind='codigo_'.$id;   $_POST[$ind] = $codigo;
				$ind='descrip_'.$id;  $_POST[$ind] = $row->descrip;
				$ind='cantidad_'.$id; $_POST[$ind] = $cana;

				$rt=$this->dataedit();
				if(strripos($rt,'Guardada')){
					$data = array('status' => 'T');
					$this->db->where('id', $id_ordp);
					$this->db->update('ordp', $data);
				}

				//Calcula los costos
				$itcosto=0;
				$sel=array('a.cantidad','a.costo','a.fijo');
				$this->db->select($sel);
				$this->db->from('ordpitem AS a');
				$this->db->join('sinv AS b','a.codigo=b.codigo');
				$this->db->where('a.id_ordp' , $id_ordp);
				$mSQL_2 = $this->db->get();
				$ordpitem_row =$mSQL_2->result();

				foreach ($ordpitem_row as $itrow){
					$itcosto+=($itrow->fijo=='S')? $itrow->costo : $itrow->costo*$itrow->cantidad;
				}

				$sel=array('a.porcentaje','a.tipo');
				$this->db->select($sel);
				$this->db->from('ordpindi AS a');
				$this->db->where('a.id_ordp' , $id_ordp);
				$mSQL_4 = $this->db->get();
				$ordpindi_row =$mSQL_4->result();
				$costo=$itcosto;
				foreach ($ordpindi_row as $itrow){
					$costo += ($itrow->tipo=='M')? $itrow->porcentaje/$cana :$itrow->porcentaje*$itcosto/100;
				}
				$costo=round($costo,2);

				$data = array('ultimo' => $costo,'formcal'=>'U');
				$this->db->where('codigo', $codigo);
				$this->db->update('sinv', $data);
				$dbcodigo=$this->db->escape($codigo);

				$mSQL="UPDATE sinv SET
				pond   = IF(existen IS NULL,${costo},(existen*pond+${costo}*${cana})/(existen+${cana})),
				base1  = ${costo}*100/(100-margen1),
				base2  = ${costo}*100/(100-margen2),
				base3  = ${costo}*100/(100-margen3),
				base4  = ${costo}*100/(100-margen4),
				precio1= ${costo}*100*(1+(iva/100))/(100-margen1),
				precio2= ${costo}*100*(1+(iva/100))/(100-margen2),
				precio3= ${costo}*100*(1+(iva/100))/(100-margen3),
				precio4= ${costo}*100*(1+(iva/100))/(100-margen4)
				WHERE codigo=${dbcodigo}";

				$ban=$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'straordp'); $error++; }

				echo $rt.' '.anchor($back,'regresar');
			}else{
				redirect($back);
			}
		}else{
			exit();
		}
	}

	function chrecibe($recibe){
		$envia=$this->input->post('envia');
		if($recibe!=$envia){
			return true;
		}
		$this->validation->set_message('chrecibe','El almac&eacute;n que env&iacute;a no puede ser igual a que recibe');
		return false;
	}

	function _pre_ordp_insert($do){
		if($do->get('tipoordp')=='E'){
			$do->set('envia' ,'APRO');
			$do->set('recibe','PROD');
		}else{
			$do->set('envia' ,'PROD');
			$do->set('recibe','APRO');
		}

		$this->_pre_insert($do);
	}

	function _pre_insert($do){
		$numero=$this->datasis->fprox_numero('nstra');
		$do->set('numero',$numero);
		$transac = $this->datasis->fprox_numero('ntransa');
		$do->set('transac', $transac);

		$cana = $do->count_rel('itstra'); $error=0;
		for($i = 0;$i < $cana;$i++){
			$itcodigo  = $do->get_rel('itstra', 'codigo'  ,$i);
			$dbitcodigo=$this->db->escape($itcodigo);
			$sinvrow=$this->datasis->damerow('SELECT iva,precio1,precio2,precio3,precio4,ultimo,descrip FROM sinv WHERE codigo='.$dbitcodigo);

			$do->set_rel('itstra', 'precio1',  $sinvrow['precio1'], $i);
			$do->set_rel('itstra', 'precio2',  $sinvrow['precio2'], $i);
			$do->set_rel('itstra', 'precio3',  $sinvrow['precio3'], $i);
			$do->set_rel('itstra', 'precio4',  $sinvrow['precio4'], $i);
			$do->set_rel('itstra', 'iva'    ,  $sinvrow['iva']    , $i);
			$do->set_rel('itstra', 'costo'  ,  $sinvrow['ultimo'] , $i);
			$do->set_rel('itstra', 'descrip',  $sinvrow['descrip'], $i);
		}
		return true;
	}

	function _post_insert($do){
		$envia   = $do->get('envia');
		$recibe  = $do->get('recibe');
		$dbenvia = $this->db->escape($envia);
		$dbrecibe= $this->db->escape($recibe);

		$cana = $do->count_rel('itstra'); $error=0;
		for($i = 0;$i < $cana;$i++){
			$itcana    = floatval($do->get_rel('itstra', 'cantidad',$i));
			$itcodigo  = $do->get_rel('itstra', 'codigo'  ,$i);
			$dbitcodigo=$this->db->escape($itcodigo);

			$mSQL="INSERT INTO itsinv (codigo,alma,existen) VALUES (${dbitcodigo},${dbenvia},-${itcana}) ON DUPLICATE KEY UPDATE existen=existen-${itcana}";
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ memowrite($mSQL,'stra'); $error++;}

			$mSQL="INSERT INTO itsinv (codigo,alma,existen) VALUES (${dbitcodigo},${dbrecibe},${itcana}) ON DUPLICATE KEY UPDATE existen=existen+${itcana}";
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ memowrite($mSQL,'stra'); $error++;}
		}

		$codigo=$do->get('numero');
		logusu('stra',"TRANSFERENCIA $codigo CREADO");
		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='No se pueden modificar las tranferencias.';
		return false;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='No se pueden eliminar';
		return false;
	}

	function instalar(){
		$campos=$this->db->list_fields('stra');

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE stra DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE stra ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE stra ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('proveed',$campos)){
			$this->db->simple_query('ALTER TABLE `stra` ADD COLUMN `proveed` CHAR(5) NULL DEFAULT NULL COMMENT \'Para el caso de las transferencias por RMS\'');
		}

		if(!in_array('ordp',$campos)){
			$mSQL="ALTER TABLE `stra`
			ADD COLUMN `ordp` VARCHAR(8) NULL DEFAULT NULL,
			ADD COLUMN `esta` VARCHAR(5) NULL DEFAULT NULL ";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('tipoordp',$campos)){
			$mSQL="ALTER TABLE `stra` ADD COLUMN `tipoordp` CHAR(1) NULL DEFAULT NULL COMMENT 'Si es entrega a estacion o retiro de estacion'";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('condiciones',$campos)){
			$mSQL="ALTER TABLE `stra` ADD COLUMN `condiciones` TEXT NULL DEFAULT NULL";
			$this->db->simple_query($mSQL);
		}
	}
}