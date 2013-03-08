<?php require_once(BASEPATH.'application/controllers/validaciones.php');
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
		if ( !$this->datasis->iscampo('apan','id') ) {
			$this->db->simple_query('ALTER TABLE apan DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE apan ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE apan ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->modintramenu( 900, 600, substr($this->url,0,-1) );
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
		$grid->wbotonadd(array('id'=>'fcliente', 'img'=>'images/agrega4.png',  'alt' => 'Anticipo de Cliente',   'label'=>'Anticipo de Cliente'   ));
		$grid->wbotonadd(array('id'=>'fproveed', 'img'=>'images/agrega4.png',  'alt' => 'Anticipo de Proveedor', 'label'=>'Anticipo de Proveedor' ));
		$grid->wbotonadd(array('id'=>'fimprime', 'img'=>'images/pdf_logo.gif', 'alt' => 'Imprimir Documento',    'label'=>'Imprimir Documento'    ));
		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = "radicional", $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$funciones = '';

		$adic = array(
		array("id"=>"fedita",  "title"=>"Agregar/Editar Registro")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('APAN', 'JQ');
		$param['otros']        = $this->datasis->otros('APAN', 'JQ');
		
		$param['centerpanel']  = $centerpanel;
		//$param['funciones']    = $funciones;

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

		// Anticipo a Cliente
		$bodyscript .= '
		$("#fcliente").click( function() {
			$.post("'.site_url($this->url.'decliente/create').'",
			function(data){
				$("#fedita").dialog( {height: 450, width: 620, title: "Aplicacion de Anticipo a Cliente"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';

		// Anticipo a Cliente
		$bodyscript .= '
		$("#fproveed").click( function() {
			$.post("'.site_url($this->url.'deproveed/create').'",
			function(data){
				$("#fedita").dialog( {height: 450, width: 620, title: "Aplicacion de Anticipo a Proveedor"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';

		$bodyscript .= '
		jQuery("#fimprime").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\'/proteoerp/formatos/ver/APAN/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});
		';

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
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/APAN').'/\'+res.id+\'/id\'').';
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
	//   Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('numero');
		$grid->label('Numero');
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
		$grid->label('Observa1');
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
		$grid->label('Transac');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 8 }',
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
		$grid->setAfterSubmit("-");
		$grid->setOndblClickRow('');

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(false);
		$grid->setSearch(false);
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
	function getdata()
	{
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
	function setData()
	{
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('apan', $data);
				echo "Registro Agregado";

				logusu('APAN',"Registro ????? INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('apan', $data);
			logusu('APAN',"Registro ????? MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM apan WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM apan WHERE id=$id ");
				logusu('APAN',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	//Definicion del Grid del Item
	//
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('origen');
		$grid->label('Origen');
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
		$grid->label('Numero');
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
		$grid->setAfterSubmit("-");
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
	function getdatait()
	{
		$id = $this->uri->segment(4);
		if ($id){
			$transac = $this->datasis->dameval("SELECT transac FROM apan WHERE id=$id");
			$grid       = $this->jqdatagrid;
			$mSQL = "
				SELECT 'Cliente' origen, cod_cli, fecha, CONCAT(tipoccli,numccli) anticipo, CONCAT(tipo_doc, numero) numero, monto, abono, ppago, reten, reteiva, id
				FROM itccli WHERE transac='$transac' 
				UNION ALL
				SELECT 'Prveed' origen, cod_prv, fecha, CONCAT(tipoppro,numppro) anticipo, CONCAT(tipo_doc, numero) numero, monto, abono, ppago, reten, reteiva, id
				FROM itppro WHERE transac='$transac'
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

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
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

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$edit->numero = new inputField('Numero','numero');
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
		$edit->clipro->rule      = '';
		$edit->clipro->size      = 7;
		$edit->clipro->maxlength = 5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule      = '';
		$edit->nombre->size      = 30;
		$edit->nombre->maxlength = 30;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule      = 'numeric';
		$edit->monto->css_class = 'inputnum';
		$edit->monto->size      = 10;
		$edit->monto->maxlength = 17;

		$edit->reinte = new inputField('Reinte','reinte');
		$edit->reinte->rule      = '';
		$edit->reinte->size      = 7;
		$edit->reinte->maxlength = 5;

		$edit->observa1 = new inputField('Observa1','observa1');
		$edit->observa1->rule      = '';
		$edit->observa1->size      = 52;
		$edit->observa1->maxlength = 50;

		$edit->observa2 = new inputField('Observa2','observa2');
		$edit->observa2->rule      = '';
		$edit->observa2->size      = 52;
		$edit->observa2->maxlength = 50;

		$edit->transac = new inputField('Transac','transac');
		$edit->transac->rule      = '';
		$edit->transac->size      = 10;
		$edit->transac->maxlength =  8;

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		//$this->_dataedit($edit);

		return $edit;

/*
		$edit->build();
		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form'] =&  $edit;
			$this->load->view('view_apan', $conten);
		}
*/
	}


	//******************************************************************
	// Cruce Cliente Proveedor
	//
	function decliente(){
		$this->rapyd->load('dataedit');
		$edit = $this->_deapan();

		$script= '';

		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->clipro->label = 'Cliente';

		$edit->tipo = new autoUpdateField('tipo','C','C');

		$this->_dataedit($edit);

	}

	//******************************************************************
	// Cruce Cliente Proveedor
	//
	function deproveed(){
		$this->rapyd->load('dataedit');
		$edit = $this->_deapan();

		$script= '';

		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->clipro->label = 'Proveedor';

		$edit->tipo = new autoUpdateField('tipo','P','P');

		$this->_dataedit($edit);

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








/*
class apan extends validaciones {

	function apan(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		if ( !$this->datasis->iscampo('apan','id') ) {
			$this->db->simple_query('ALTER TABLE apan DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE apan ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE apan ADD UNIQUE INDEX numero (numero)');
			echo "Indice ID Creado";
		}
		$this->datasis->modulo_id(505,1);
		$this->apanextjs();
		//redirect("finanzas/apan/filteredgrid");
	}



	//******************************************************************
	// Dataedit para todos
	//
	function dataedit($tipo)	{
		$this->rapyd->load('dataobject','datadetails');
		$do = new DataObject("apan");
		$title="";
		if($tipo=='P'){
			$do->rel_one_to_many('itppro', 'itppro', array('transac'=>'transac'));
			$title='itppro';
		}
		else {
			$do->rel_one_to_many('itccli', 'itccli', array('transac'=>'transac'));
			$title='itccli';
		}

		$edit = new DataDetails('Aplicaci&oacute;n de Anticipos', $do);
		$edit->back_url = site_url('finanzas/apan/filteredgrid');
		$edit->set_rel_title($title,'Anticipo <#o#>');

		$edit->numero = new inputField("N&uacute;mero", "numero");
		$edit->numero->mode="autohide";
		$edit->numero->size =12;
		$edit->numero->rule="trim|required";
		$edit->numero->maxlength=8;

		$edit->fecha = new DateonlyField("Fecha", "fecha");
		$edit->fecha->size = 12;
		$edit->fecha->rule="required|chfecha";
		$edit->fecha->insertValue = date("Y-m-d");

		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->option("C","Cliente");
		$edit->tipo->option("P","Proveedor");
		$edit->tipo->style="width:100px";
			
		$edit->clipro =new inputField("Codigo", "clipro");
		$edit->clipro->rule='trim|required';
		$edit->clipro->size =12;
		$edit->clipro->readonly=true;

		$edit->nombre =   new inputField("Nombre", "nombre");
		$edit->nombre->size =30;
		$edit->nombre->rule = "trim|strtoupper";
		$edit->nombre->readonly=true;

		$edit->monto =    new inputField("Monto", "monto");
		$edit->monto->size = 12;
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='trim|numeric';
		$edit->monto->maxlengxlength=0;
		$edit->monto->rule='positive';

		$edit->reinte =   new inputField("Convertido", "reinte");
		$edit->reinte->rule='trim|required';
		$edit->reinte->size =12;
		$edit->reinte->readonly=true;

		$edit->nombreintes=new inputField("Nombre","nombreintes");
		$edit->nombreintes->size=30;
		$edit->nombreintes->readonly=true;

		$edit->observa1 = new inputField("Observaciones", "observa1");
		$edit->observa1->rule='trim';
		$edit->observa1->size =50;
		$edit->observa1->maxlength=50;

		$edit->observa2 = new inputField("", "observa2");
		$edit->observa2->rule='trim';
		$edit->observa2->size =50;
		$edit->observa2->maxlength=50;

		//Detalles itppro
		if($tipo=='P'){
			$edit->tipoppro = new inputField("Tipo <#o#>","tipoppro_<#i#>");
			$edit->tipoppro->db_name = "tipoppro";
			$edit->tipoppro->rel_id  = 'itppro';
			$edit->tipoppro->rule='trim|required';
			$edit->tipoppro->size =10;
			$edit->tipoppro->readonly=true;

			$edit->tipo_doc = new inputField("Tipo Documento <#o#>","tipo_doc_<#i#>");
			$edit->tipo_doc->db_name = "tipo_doc";
			$edit->tipo_doc->rel_id  = 'itppro';
			$edit->tipo_doc->rule='trim|required';
			$edit->tipo_doc->size =10;
			$edit->tipo_doc->readonly=true;

			$edit->itnumero = new inputField("N&uacute;mero <#o#>","itnumero_<#i#>");
			$edit->itnumero->db_name = "numero";
			$edit->itnumero->rel_id  = 'itppro';
			$edit->itnumero->rule='trim|required';
			$edit->itnumero->size =10;
			$edit->itnumero->readonly=true;

			$edit->itnumppro = new inputField("N&uacute;mero <#o#>","itnumppro_<#i#>");
			$edit->itnumppro->db_name = "numppro";
			$edit->itnumppro->rel_id  = 'itppro';
			$edit->itnumppro->rule='trim|required';
			$edit->itnumppro->size =10;
			$edit->itnumppro->readonly=true;

			$edit->itfechap = new DateonlyField("Fecha", "itfechap_<#i#>");
			$edit->itfechap->db_name = "fecha";
			$edit->itfechap->rel_id  = 'itppro';
			$edit->itfechap->size = 12;
			$edit->itfechap->rule="required|chfecha";
			$edit->itfechap->insertValue = date("Y-m-d");

			$edit->itmontop = new inputField("Monto <#o#>", "itmontop_<#i#>");
			$edit->itmontop->db_name='monto';
			$edit->itmontop->css_class='inputnum';
			$edit->itmontop->rel_id   ='itppro';
			$edit->itmontop->size=3;
			$edit->itmontop->rule='positive';

			$edit->itabonop = new inputField("Abono <#o#>", "itabonop_<#i#>");
			$edit->itabonop->db_name='abono';
			$edit->itabonop->css_class='inputnum';
			$edit->itabonop->rel_id   ='itppro';
			$edit->itabonop->size=3;
			$edit->itabonop->rule='positive';
		}
		//Detalles itccli
		if($tipo=='C'){
			$edit->tipoccli = new inputField("Tipo <#o#>","tipoccli_<#i#>");
			$edit->tipoccli->db_name = "tipoccli";
			$edit->tipoccli->rel_id  = 'itccli';
			$edit->tipoccli->rule='trim|required';
			$edit->tipoccli->size =10;
			$edit->tipoccli->readonly=true;

			$edit->tipo_doc_c = new inputField("Tipo Documento <#o#>","tipo_doc_C<#i#>");
			$edit->tipo_doc_c->db_name = "tipo_doc";
			$edit->tipo_doc_c->rel_id  = 'itccli';
			$edit->tipo_doc_c->rule='trim|required';
			$edit->tipo_doc_c->size =10;
			$edit->tipo_doc_c->readonly=true;

			$edit->itnumero_c = new inputField("N&uacute;mero <#o#>","itnumero_c_<#i#>");
			$edit->itnumero_c->db_name = "numero";
			$edit->itnumero_c->rel_id  = 'itccli';
			$edit->itnumero_c->rule='trim|required';
			$edit->itnumero_c->size =10;
			$edit->itnumero_c->readonly=true;

			$edit->numccli = new inputField("N&uacute;mero <#o#>","numccli_<#i#>");
			$edit->numccli->db_name = "numccli";
			$edit->numccli->rel_id  = 'itccli';
			$edit->numccli->rule='trim|required';
			$edit->numccli->size =10;
			$edit->numccli->readonly=true;

			$edit->itfechac = new DateonlyField("Fecha", "itfechac_<#i#>");
			$edit->itfechac->db_name = "fecha";
			$edit->itfechac->rel_id  = 'itccli';
			$edit->itfechac->size = 12;
			$edit->itfechac->rule="required|chfecha";
			$edit->itfechac->insertValue = date("Y-m-d");

			$edit->itmontoc = new inputField("Monto <#o#>", "itmontoc_<#i#>");
			$edit->itmontoc->db_name='monto';
			$edit->itmontoc->css_class='inputnum';
			$edit->itmontoc->rel_id   ='itccli';
			$edit->itmontoc->size=3;
			$edit->itmontoc->rule='positive';

			$edit->itabonoc = new inputField("Abono <#o#>", "itabonoc_<#i#>");
			$edit->itabonoc->db_name='abono';
			$edit->itabonoc->css_class='inputnum';
			$edit->itabonoc->rel_id   ='itccli';
			$edit->itabonoc->size=3;
			$edit->itabonoc->rule='positive';
		}
		///fin de detalles
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$conten['form']  =&  $edit;
		$data['content'] = $this->load->view('view_apan', $conten,true);
		$data['title']   = "<h1>Aplicaci&oacute;n de Anticipos</h1>";
		$data["script"]  = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js");
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
*/

	function instalar(){
		//$sql="ALTER TABLE `apan`  DROP PRIMARY KEY";
		//$this->db->query($sql);
		$sql="ALTER TABLE `apan`  ADD COLUMN `id` INT(10) NULL AUTO_INCREMENT AFTER `usuario`,  ADD PRIMARY KEY (`id`)";
		$this->db->query($sql);
	}


/*
	function griditapan(){
		$numero   = isset($_REQUEST['numero'])  ? $_REQUEST['numero']   :  '';
		if ($numero == '' ){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM apan ")  ;
		} else
			$id = $this->datasis->dameval("SELECT id FROM apan WHERE numero='$numero' ")  ;

		$transac  =  $this->datasis->dameval("SELECT transac FROM apan WHERE id=$id ")  ;
		
	
		$mSQL = "
SELECT
'1' origen, cod_cli, fecha, tipo_doc, numero, monto, abono, ppago, reten, reteiva
FROM itccli WHERE transac='$transac' 
UNION ALL
SELECT
'2' origen, cod_prv, fecha, tipo_doc, numero, monto, abono, ppago, reten, reteiva
FROM itppro WHERE transac='$transac'
";
	}

		$funciones = "
function renderScli(value, p, record) {
	var mreto='';
	if ( record.data.cod_cli == '' ){
		mreto = '{0}';
	} else {
		mreto = '<a href=\'javascript:void(0);\' onclick=\"window.open(\''+urlAjax+'sclibu/{1}\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx='+mxs+',screeny='+mys+'\');\" heigth=\"600\">{0}</a>';
	}
	return Ext.String.format(mreto,	value, record.data.numero );
}

function renderSinv(value, p, record) {
	var mreto='';
	mreto = '<a href=\'javascript:void(0);\' onclick=\"window.open(\''+urlApp+'inventario/sinv/dataedit/show/{1}\', \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx='+mxs+',screeny='+mys+'\');\" heigth=\"600\">{0}</a>';
	return Ext.String.format(mreto,	value, record.data.codid );
}
	";
*/

}
?>
