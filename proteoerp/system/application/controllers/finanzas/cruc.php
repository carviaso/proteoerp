<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Cruc extends Controller {
	var $mModulo = 'CRUC';
	var $titp    = 'Cruce de Cuentas';
	var $tits    = 'Cruce de Cuentas';
	var $url     = 'finanzas/cruc/';

	function Cruc(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'CRUC', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('cruc','id') ) {
			$this->db->simple_query('ALTER TABLE cruc DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE cruc ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE cruc ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	//Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('185');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$grid1->setHeight('140');
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 172, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'imprime','img'=>'assets/default/images/print.png','alt' => 'Reimprimir',          'label'=>'Reimprimir Documento', 'tema'=>'tema1'));
		$grid->wbotonadd(array('id'=>'fcc',    'img'=>'images/cruce.png','alt' => 'Cliente->Cliente',    'label'=>'Cliente->Cliente',     'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'fcp',    'img'=>'images/crucealto.png','alt' => 'Cliente->Proveedor',  'label'=>'Cliente->Proveedor',   'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'fpc',    'img'=>'images/cruce.png','alt' => 'Proveedor->Cliente',  'label'=>'Proveedor->Cliente',   'tema'=>'anexos'));
		$grid->wbotonadd(array('id'=>'fpp',    'img'=>'images/crucebajo.png','alt' => 'Proveedor->Proveedor','label'=>'Proveedor->Proveedor', 'tema'=>'anexos'));
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
			var meco=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return meco;
		};
		';

		$param['WestPanel']    = $WestPanel;
		$param['script']       = script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['funciones']   = $funciones;
		$param['listados']     = $this->datasis->listados('CRUC', 'JQ');
		$param['otros']        = $this->datasis->otros('CRUC', 'JQ');
		$param['centerpanel']  = $centerpanel;
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;
		$param['tamano']       = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);

	}

	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0, $grid1 ){
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		function tconsulta(transac){
			if (transac)	{
				window.open(\''.site_url('contabilidad/casi/localizador/transac/procesar').'/\'+transac, \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Transaccion invalida</h1>");
			}
		};
		';

		$bodyscript .= '
		function crucshow(){
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
		function crucdel() {
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
				window.open(\''.site_url('formatos/descargar/CRUCDE').'/\'+id, \'_blank\', \'width=300,height=200,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
			} else { $.prompt("<h1>Por favor Seleccione una Factura</h1>");}
		});';

		// Cruce Cliente Cliente
		$bodyscript .= '
		$("#fcc").click( function() {
			$.post("'.site_url($this->url.'declicli/create').'",
			function(data){
				$("#fedita").dialog( {height: 500, width: 620, title: "Cruce Cliente Cliente"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

		// Cruce Cliente Proveedor
		$bodyscript .= '
		$("#fcp").click( function() {
			$.post("'.site_url($this->url.'declipro/create').'",
			function(data){
				$("#fedita").dialog( {height: 500, width: 620, title: "Cruce Cliente Proveedor"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

		// Cruce Proveedor Proveedor
		$bodyscript .= '
		$("#fpp").click( function() {
			$.post("'.site_url($this->url.'depropro/create').'",
			function(data){
				$("#fedita").dialog( {height: 500, width: 620, title: "Cruce Proveedor Proveedor"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';


		// Cruce Proveedor Cliente
		$bodyscript .= '
		$("#fpc").click( function() {
			$.post("'.site_url($this->url.'deprocli/create').'",
			function(data){
				$("#fedita").dialog( {height: 500, width: 620, title: "Cruce Proveedor Cliente"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 400, width: 600, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/descargar/CRUCDE').'/\'+json.pk.id+\'/id\'').';
									return true;
								} else {
									$.prompt(json.mensaje);
								}
							}catch(e){
								$("#fedita").html(r);
							}
						}
				})},
				"Cancelar": function() {
					$("#fedita").html("");
					$( this ).dialog( "close" );
			}},
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
	//Definicion del Grid y la Forma
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


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 3 }',
		));


		$grid->addField('proveed');
		$grid->label('Envia');
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


		$grid->addField('saldoa');
		$grid->label('Saldo');
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


		$grid->addField('cliente');
		$grid->label('Recibe');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('nomcli');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('saldod');
		$grid->label('Saldo D.');
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


		$grid->addField('concept1');
		$grid->label('Concepto 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('concept2');
		$grid->label('Concepto 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
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
					jQuery(gridId2).jqGrid("setGridParam",{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}'
		);

		$grid->setFormOptionsE(''); //'closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA(''); //'closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(   false );  // $this->datasis->sidapuede('CRUC','INCLUIR%' ));
		$grid->setEdit(  false );  // $this->datasis->sidapuede('CRUC','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('CRUC','BORR_REG%'));
		$grid->setSearch( true ); //$this->datasis->sidapuede('CRUC','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		//$grid->setBarOptions("addfunc: crucadd, editfunc: crucedit, delfunc: crucdel, viewfunc: crucshow");

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

	/*******************************************************************
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('cruc');

		$response   = $grid->getData('cruc', array(array()), array(), false, $mWHERE, 'id','desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/*******************************************************************
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
				$check = $this->datasis->dameval("SELECT count(*) FROM cruc WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('cruc', $data);
					echo "Registro Agregado";

					logusu('CRUC',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM cruc WHERE id=$id");
			unset($data[$mcodp]);
			$this->db->where("id", $id);
			//$this->db->update('cruc', $data);
			//logusu('CRUC',"Grupo de Cliente  ".$nuevo." MODIFICADO");
			echo "$mcodp Modificado";

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM cruc WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM cruc WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				//$this->db->simple_query("DELETE FROM cruc WHERE id=$id ");
				//logusu('CRUC',"Registro ????? ELIMINADO");
				echo 'Registro Eliminado';
			}
		};
	}

	//******************************************************************
	//Definicion del Grid y la Forma
	//
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

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


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:3, maxlength: 3 }',
		));


		$grid->addField('onumero');
		$grid->label('O.N&uacute;mero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('ofecha');
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


		$grid->addField('oregist');
		$grid->label('O.Registro');
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


		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
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



	/*******************************************************************
	* Busca la data en el Servidor por json
	*/
	function getdatait( $id = 0 ){
		if($id === 0 ){
			$id = $this->datasis->dameval('SELECT MAX(id) AS id FROM cruc');
		}
		$dbid=intval($id);
		if(empty($dbid)) return '';
		$numero   = $this->datasis->dameval("SELECT numero FROM cruc WHERE id=${dbid}");
		$dbnumero = $this->db->escape($numero);

		$orderby= '';
		$sidx=$this->input->post('sidx');
		if($sidx){
			$campos = $this->db->list_fields('itcruc');
			if(in_array($sidx,$campos)){
				$sidx = trim($sidx);
				$sord   = $this->input->post('sord');
				$orderby="ORDER BY `${sidx}` ".(($sord=='asc')? 'ASC':'DESC');
			}
		}

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM itcruc WHERE numero=${dbnumero} ${orderby}";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/*******************************************************************
	* Guarda la Informacion
	*/
	function setDatait(){
	}


	//******************************************************************
	// Cruce Cliente Proveedor
	//
	function declipro(){
		$this->rapyd->load('dataedit');
		$edit = $this->_decruc();

		$script= '
		$(function() {
			$("#proveed").autocomplete({
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

					$.ajax({
						url: "'.site_url('ajax/buscasmov').'",
						dataType: "json",
						type: "POST",
						data: {"scli" : ui.item.value},
						success: function(data){
								truncate();
								$.each(data,
									function(id, val){
										can=add_itcruc();

										$("#itonumero_"+can).val(val.tipo_doc+val.numero);
										$("#itofecha_"+can ).val(val.fecha);
										$("#itpmonto_"+can  ).val(val.monto);
										$("#itpsaldo_"+can  ).val(val.saldo);
										$("#itmonto_"+can ).val("0");

										$("#itonumero_"+can+"_val").text(val.tipo_doc+val.numero);
										$("#itofecha_"+can+"_val" ).text(val.fecha);
										$("#itpmonto_"+can+"_val"  ).text(nformat(val.monto,2));
										$("#itpsaldo_"+can+"_val"  ).text(nformat(val.saldo,2));

										$("#itmonto_"+can ).focus(function(){
											var valor = $(this).val();
											if(valor=="" || valor=="0" || valor=="0.0" || valor=="0.00"){
												$(this).val(val.saldo);
												totaliza();
											}
										});
									}
								);
							},
					});

					var saldo= Number($.ajax({ type: "POST", url: "'.site_url('ajax/ajaxsaldoscli').'", async: false, data: {clipro: ui.item.value } }).responseText);
					$("#saldoa").val(roundNumber(saldo,2))

				}
			});

			$("#cliente").autocomplete({
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
									$("#cliente").val("");

									$("#nomcli").val("");
									$("#nomcli_val").text("");

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
					$("#cliente").attr("readonly", "readonly");

					$("#nomcli").val(ui.item.nombre);
					$("#nomcli_val").text(ui.item.nombre);

					setTimeout(function() {  $("#cliente").removeAttr("readonly"); }, 1500);

					var saldo= Number($.ajax({ type: "POST", url: "'.site_url('ajax/ajaxsaldosprv').'", async: false, data: {clipro: ui.item.value } }).responseText);
					$("#saldod").val(roundNumber(saldo,2))
				}
			});
		});';


		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->proveed->label = 'Cliente';
		$edit->cliente->label = 'Proveedor';

		$edit->tipo = new autoUpdateField('tipo','C-P','C-P');

		$this->_dataedit($edit);

	}


	//******************************************************************
	// Cruce Cliente Cliente
	//
	function declicli(){

		$this->rapyd->load('dataedit');
		$edit = $this->_decruc();

		$script= '
		$(function() {
			$("#proveed").autocomplete({
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

					$.ajax({
						url: "'.site_url('ajax/buscasmov').'",
						dataType: "json",
						type: "POST",
						data: {"scli" : ui.item.value},
						success: function(data){
								truncate();
								$.each(data,
									function(id, val){
										can=add_itcruc();

										$("#itonumero_"+can).val(val.tipo_doc+val.numero);
										$("#itofecha_"+can ).val(val.fecha);
										$("#itpmonto_"+can  ).val(val.monto);
										$("#itpsaldo_"+can  ).val(val.saldo);
										$("#itmonto_"+can ).val("0");

										$("#itonumero_"+can+"_val").text(val.tipo_doc+val.numero);
										$("#itofecha_"+can+"_val" ).text(val.fecha);
										$("#itpmonto_"+can+"_val"  ).text(nformat(val.monto,2));
										$("#itpsaldo_"+can+"_val"  ).text(nformat(val.saldo,2));

										$("#itmonto_"+can ).focus(function(){
											var valor = $(this).val();
											if(valor=="" || valor=="0" || valor=="0.0" || valor=="0.00"){
												$(this).val(val.saldo);
												totaliza();
											}
										});
									}
								);
							},
					});

					var saldo= Number($.ajax({ type: "POST", url: "'.site_url('ajax/ajaxsaldoscli').'", async: false, data: {clipro: ui.item.value } }).responseText);
					$("#saldoa").val(roundNumber(saldo,2))

				}
			});

			$("#cliente").autocomplete({
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
									$("#cliente").val("");

									$("#nomcli").val("");
									$("#nomcli_val").text("");

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
					$("#cliente").attr("readonly", "readonly");

					$("#nomcli").val(ui.item.nombre);
					$("#nomcli_val").text(ui.item.nombre);

					setTimeout(function() {  $("#cliente").removeAttr("readonly"); }, 1500);

					var saldo= Number($.ajax({ type: "POST", url: "'.site_url('ajax/ajaxsaldoscli').'", async: false, data: {clipro: ui.item.value } }).responseText);
					$("#saldod").val(roundNumber(saldo,2))
				}
			});
		});';


		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->proveed->label = 'Cede';

		$edit->cliente->label = 'Recibe';

		$edit->tipo = new autoUpdateField('tipo','C-C','C-C');

		$this->_dataedit($edit);

	}



	//******************************************************************
	// Cruce Proveedor Proveedor
	//
	function depropro(){

		$this->rapyd->load('dataedit');
		$edit = $this->_decruc();

		$script= '
		$(function() {
			$("#proveed").autocomplete({
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

					$.ajax({
						url: "'.site_url('ajax/buscasprm').'",
						dataType: "json",
						type: "POST",
						data: {"sprv" : ui.item.value},
						success: function(data){
								truncate();
								$.each(data,
									function(id, val){
										can=add_itcruc();

										$("#itonumero_"+can).val(val.tipo_doc+val.numero);
										$("#itofecha_"+can ).val(val.fecha);
										$("#itpmonto_"+can  ).val(val.monto);
										$("#itpsaldo_"+can  ).val(val.saldo);
										$("#itmonto_"+can ).val("0");

										$("#itonumero_"+can+"_val").text(val.tipo_doc+val.numero);
										$("#itofecha_"+can+"_val" ).text(val.fecha);
										$("#itpmonto_"+can+"_val"  ).text(nformat(val.monto,2));
										$("#itpsaldo_"+can+"_val"  ).text(nformat(val.saldo,2));

										$("#itmonto_"+can ).focus(function(){
											var valor = $(this).val();
											if(valor=="" || valor=="0" || valor=="0.0" || valor=="0.00"){
												$(this).val(val.saldo);
												totaliza();
											}
										});
									}
								);
							},
					});

					var saldo= Number($.ajax({ type: "POST", url: "'.site_url('ajax/ajaxsaldosprv').'", async: false, data: {clipro: ui.item.value } }).responseText);
					$("#saldoa").val(roundNumber(saldo,2))

				}
			});

			$("#cliente").autocomplete({
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
									$("#cliente").val("");

									$("#nomcli").val("");
									$("#nomcli_val").text("");

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
					$("#cliente").attr("readonly", "readonly");

					$("#nomcli").val(ui.item.nombre);
					$("#nomcli_val").text(ui.item.nombre);

					setTimeout(function() {  $("#cliente").removeAttr("readonly"); }, 1500);

					var saldo= Number($.ajax({ type: "POST", url: "'.site_url('ajax/ajaxsaldosprv').'", async: false, data: {clipro: ui.item.value } }).responseText);
					$("#saldod").val(roundNumber(saldo,2))
				}
			});
		});';

		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->proveed->label = 'Cede';

		$edit->cliente->label = 'Recibe';

		$edit->tipo = new autoUpdateField('tipo','P-P','P-P');

		$this->_dataedit($edit);

	}

	//******************************************************************
	// Cruce Proveedor Cliente
	//
	function deprocli(){
		$this->rapyd->load('dataedit');
		$edit = $this->_decruc();

		$script= '
		$(function() {
			$("#proveed").autocomplete({
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

					$.ajax({
						url: "'.site_url('ajax/buscasprm').'",
						dataType: "json",
						type: "POST",
						data: {"sprv" : ui.item.value},
						success: function(data){
								truncate();
								$.each(data,
									function(id, val){
										can=add_itcruc();

										$("#itonumero_"+can).val(val.tipo_doc+val.numero);
										$("#itofecha_"+can ).val(val.fecha);
										$("#itpmonto_"+can  ).val(val.monto);
										$("#itpsaldo_"+can  ).val(val.saldo);
										$("#itmonto_"+can ).val("0");

										$("#itonumero_"+can+"_val").text(val.tipo_doc+val.numero);
										$("#itofecha_"+can+"_val" ).text(val.fecha);
										$("#itpmonto_"+can+"_val"  ).text(nformat(val.monto,2));
										$("#itpsaldo_"+can+"_val"  ).text(nformat(val.saldo,2));

										$("#itmonto_"+can ).focus(function(){
											var valor = $(this).val();
											if(valor=="" || valor=="0" || valor=="0.0" || valor=="0.00"){
												$(this).val(val.saldo);
												totaliza();
											}
										});
									}
								);
							},
					});

					var saldo= Number($.ajax({ type: "POST", url: "'.site_url('ajax/ajaxsaldosprv').'", async: false, data: {clipro: ui.item.value } }).responseText);
					$("#saldoa").val(roundNumber(saldo,2))

				}
			});

			$("#cliente").autocomplete({
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
									$("#cliente").val("");

									$("#nomcli").val("");
									$("#nomcli_val").text("");

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
					$("#cliente").attr("readonly", "readonly");

					$("#nomcli").val(ui.item.nombre);
					$("#nomcli_val").text(ui.item.nombre);

					setTimeout(function() {  $("#cliente").removeAttr("readonly"); }, 1500);

					var saldo= Number($.ajax({ type: "POST", url: "'.site_url('ajax/ajaxsaldoscli').'", async: false, data: {clipro: ui.item.value } }).responseText);
					$("#saldod").val(roundNumber(saldo,2))
				}
			});
		});';


		$edit->script($script,'modify');
		$edit->script($script,'create');

		$edit->proveed->label = 'Proveedor';

		$edit->cliente->label = 'Cliente';

		$edit->tipo = new autoUpdateField('tipo','P-C','P-C');

		$this->_dataedit($edit);
	}


	//******************************************************************
	//   Dataedit Todos
	//
	function _decruc(){
		$this->rapyd->load('dataobject','datadetails');

		$do = new DataObject('cruc');
		$do->rel_one_to_many('itcruc', 'itcruc','numero');

		$edit = new DataDetails($this->tits, $do);
		$edit->on_save_redirect=false;

		$edit->post_process('insert', '_post_insert');
		$edit->post_process('update', '_post_update');
		$edit->post_process('delete', '_post_delete');
		$edit->pre_process( 'insert', '_pre_insert' );
		$edit->pre_process( 'update', '_pre_update' );
		$edit->pre_process( 'delete', '_pre_delete' );

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule      = '';
		$edit->numero->size      = 10;
		$edit->numero->maxlength =  8;
		$edit->numero->when      = array('show');

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule       = 'chfecha';
		$edit->fecha->size       = 10;
		$edit->fecha->maxlength  =  8;
		$edit->fecha->calendar   = false;
		$edit->fecha->insertValue= date('Y-m-d');

		$edit->proveed = new inputField('Proveedor','proveed');
		$edit->proveed->rule      = 'trim|required';
		$edit->proveed->size      =  6;
		//$edit->proveed->maxlength =  5;

		$edit->nombre = new inputField('Nombre','nombre');
		//$edit->nombre->type      = 'inputhidden';
		$edit->nombre->rule      = '';
		$edit->nombre->size      = 35;
		$edit->nombre->maxlength = 40;

		$edit->saldoa = new inputField('Saldo','saldoa');
		$edit->saldoa->rule      = 'numeric';
		$edit->saldoa->css_class = 'inputnum';
		$edit->saldoa->size      = 10;
		$edit->saldoa->maxlength = 16;

		$edit->cliente = new inputField('Cliente','cliente');
		$edit->cliente->rule      = 'trim|required';
		$edit->cliente->size      = 6;
		//$edit->cliente->maxlength = 5;

		$edit->nomcli = new inputField('Nomcli','nomcli');
		//$edit->nomcli->type      = 'inputhidden';
		$edit->nomcli->rule      = '';
		$edit->nomcli->size      = 35;
		$edit->nomcli->maxlength = 40;

		$edit->saldod = new inputField('Saldo','saldod');
		$edit->saldod->rule      = 'numeric';
		$edit->saldod->css_class = 'inputnum';
		$edit->saldod->size      = 10;
		$edit->saldod->maxlength = 16;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule       = 'numeric|positive';
		$edit->monto->css_class  = 'inputnum';
		$edit->monto->size       = 12;
		$edit->monto->maxlength  = 16;

		$edit->concept1 = new inputField('Concepto','concept1');
		$edit->concept1->rule      = '';
		$edit->concept1->size      = 42;
		$edit->concept1->maxlength = 40;

		$edit->concept2 = new inputField(' ','concept2');
		$edit->concept2->rule      = '';
		$edit->concept2->size      = 42;
		$edit->concept2->maxlength = 40;

		//inicio del detalle
		$edit->itonumero = new inputField('Numero','itonumero_<#i#>');
		$edit->itonumero->rule      = 'max_length[10]';
		$edit->itonumero->db_name   = 'onumero';
		$edit->itonumero->type      = 'inputhidden';
		$edit->itonumero->size      = 13;
		$edit->itonumero->maxlength = 10;
		$edit->itonumero->rel_id='itcruc';

		$edit->itofecha = new inputField('Fecha','itofecha_<#i#>');
		//$edit->itofecha->rule     = 'chfecha';
		$edit->itofecha->db_name  = 'ofecha';
		$edit->itofecha->type     = 'inputhidden';
		$edit->itofecha->size     = 10;
		$edit->itofecha->maxlength= 8;
		$edit->itofecha->rel_id='itcruc';

		$edit->itmonto = new inputField('Monto','itmonto_<#i#>');
		$edit->itmonto->rule      = 'max_length[17]|numeric';
		$edit->itmonto->css_class = 'inputnum';
		$edit->itmonto->db_name   = 'monto';
		$edit->itmonto->rel_id    = 'itcruc';
		$edit->itmonto->size      = 19;
		$edit->itmonto->maxlength = 17;
		//fin del detalle

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));

		return $edit;
	}


	//******************************************************************
	// Dataedit para todos
	//
	function _dataedit($edit){

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
			$this->load->view('view_cruc', $conten);
		}
	}


	function _pre_insert($do){
		$numero  = 'C'.$this->datasis->fprox_numero('ncruc',7);
		$trans   = $this->datasis->fprox_numero('ntransa');
		$tipo    = $do->get('tipo');
		$cliente = $do->get('cliente');
		$proveed = $do->get('proveed');

		if($tipo=='C-C' || $tipo=='P-P'){
			if($cliente == $proveed ){
				$do->error_message_ar['pre_ins']='Cruce entre el mismo no es valido';
				return false;
			}
		}

		$do->set('transac',$trans );
		$do->set('numero' ,$numero);

		$cana = $do->count_rel('itcruc');
		for($i=0;$i<$cana;$i++){
			$onumero = $do->get_rel('itcruc','onumero' ,$i);
			$monto   = $do->get_rel('itcruc','monto'   ,$i);

			if($monto == 0){
				$do->rel_rm('itcruc',$i);
				continue;
			}

			if(substr($onumero,0,2)=='FC' || substr($onumero,0,2)=='GI' || substr($onumero,0,2)=='ND'){
				$do->set_rel('itcruc','tipo','ADE',$i);
			}else{
				$do->set_rel('itcruc','tipo','APA',$i);
			}
		}

		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='Los cruces no se pueden editar, debe borrarlos y volverlos a procesar';
		return false;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
		return false;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);

		$tipo    = $do->get('tipo');
		$cliente = $do->get('cliente');
		$proveed = $do->get('proveed');
		$numero  = $do->get('numero');

		if($tipo == 'P-C'){
			// PROVEEDOR ----> CLIENTE
			$mNUMERO = $this->datasis->fprox_numero('nccli');
			$data = array();
			$data['cod_cli']  = $cliente;
			$data['nombre']   = $do->get('nomcli');
			$data['tipo_doc'] = 'NC';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $do->get('fecha');
			$data['monto']    = $do->get('monto');
			$data['abonos']   = 0;
			$data['vence']    = $do->get('fecha');
			$data['observa1'] = $do->get('concept1');
			$data['observa2'] = $do->get('concept2');
			$data['tipo_ref'] = 'CR';
			$data['num_ref']  = $do->get('numero');

			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');
			$data['transac']  = $do->get('transac');
			$data['usuario']  = $do->get('usuario');

			$this->db->insert('smov', $data);

			// DEBE ABONAR A DESDE
			$cana = $do->count_rel('itcruc');
			for( $i = 0; $i < $cana; $i++ ){
				$onumero = $do->get_rel('itcruc', 'onumero', $i);
				$montoit = $do->get_rel('itcruc', 'monto',   $i);
				$fechait = $do->get_rel('itcruc', 'ofecha',  $i);
				$tipoit  = $do->get_rel('itcruc', 'tipo',    $i);

				if($tipoit == 'APA'){
					$mSQL = "UPDATE smov SET abonos=abonos+".$montoit." WHERE tipo_doc='".substr($onumero,0,2)."'
					         AND numero='".substr($onumero,2,8)."'
					         AND fecha=".$fechait." AND cod_cli='".$cliente."' ";
					$this->db->query($mSQL);
				}
			}

			$mNUMERO = $this->datasis->fprox_numero('num_nc');

			$data = array();
			$data['cod_prv']  = $proveed;
			$data['nombre']   = $do->get('nombre');
			$data['tipo_doc'] = 'NC';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $do->get('fecha');
			$data['monto']    = $do->get('monto');
			$data['impuesto'] = 0;
			$data['abonos']   = $do->get('monto');
			$data['vence']    = $do->get('fecha');
			$data['observa1'] = $do->get('concept1');
			$data['observa2'] = $do->get('concept2');
			$data['tipo_ref'] = 'CR';
			$data['num_ref']  = $do->get('numero');

			$data['estampa'] = $do->get('estampa');
			$data['hora']    = $do->get('hora');
			$data['transac'] = $do->get('transac');
			$data['usuario'] = $do->get('usuario');

			$this->db->insert('sprm', $data);

			// DEBE ABONAR A DESDE
			$cana = $do->count_rel('itcruc');
			for( $i = 0; $i < $cana; $i++ ){
				$onumero = $do->get_rel('itcruc', 'onumero', $i);
				$montoit = $do->get_rel('itcruc', 'monto',   $i);
				$fechait = $do->get_rel('itcruc', 'ofecha',  $i);
				$tipoit  = $do->get_rel('itcruc', 'tipo',    $i);

				if($tipoit == 'ADE'){
					$mSQL = "UPDATE sprm SET abonos=abonos+".$montoit." WHERE tipo_doc='".substr($onumero,0,2)."'
					         AND numero='".substr($onumero,2,8)."'
					         AND cod_prv='".$proveed."' ";
					$this->db->query($mSQL);
				}
			}

		}elseif($tipo == 'C-P'){
			// CLIENTE ----> PROVEEDOR
			$mNUMERO = $this->datasis->fprox_numero('num_nc');
			$data['cod_prv']  = $cliente;
			$data['nombre']   = $do->get('nomcli');
			$data['tipo_doc'] = 'NC';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $do->get('fecha');
			$data['monto']    = $do->get('monto');
			$data['impuesto'] = 0;
			$data['abonos']   = 0;
			$data['vence']    = $do->get('fecha');
			$data['observa1'] = $do->get('concept1');
			$data['observa2'] = $do->get('concept2');
			$data['tipo_ref'] = 'CR';
			$data['num_ref']  = $do->get('numero');

			$data['estampa'] = $do->get('estampa');
			$data['hora']    = $do->get('hora');
			$data['transac'] = $do->get('transac');
			$data['usuario'] = $do->get('usuario');

			$this->db->insert('sprm', $data);

			// DEBE ABONAR A DESDE
			$cana = $do->count_rel('itcruc');
			for( $i = 0; $i < $cana; $i++ ){
				$onumero = $do->get_rel('itcruc', 'onumero', $i);
				$montoit = $do->get_rel('itcruc', 'monto',   $i);
				$fechait = $do->get_rel('itcruc', 'ofecha',  $i);
				$tipoit  = $do->get_rel('itcruc', 'tipo',    $i);
				if($tipoit == 'APA'){
					$mSQL = "UPDATE sprm SET abonos=abonos+".$montoit." WHERE tipo_doc='".substr($onumero,0,2)."'
					         AND numero='".substr($onumero,2,8)."'
					         AND cod_prv='".$cliente."' ";
					$this->db->query($mSQL);
				}
			}

			$mNUMERO = $this->datasis->fprox_numero('nccli');
			$data = array();
			$data['cod_cli']  = $proveed;
			$data['nombre']   = $do->get('nombre');
			$data['tipo_doc'] = 'NC';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $do->get('fecha');
			$data['monto']    = $do->get('monto');
			$data['abonos']   = $do->get('monto');
			$data['impuesto'] = 0;
			$data['vence']    = $do->get('fecha');
			$data['observa1'] = $do->get('concept1');
			$data['observa2'] = $do->get('concept2');
			$data['tipo_ref'] = 'CR';
			$data['num_ref']  = $do->get('numero');

			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');
			$data['transac']  = $do->get('transac');
			$data['usuario']  = $do->get('usuario');

			$this->db->insert('smov', $data);

			// DEBE ABONAR A DESDE
			$cana = $do->count_rel('itcruc');
			for( $i = 0; $i < $cana; $i++ ){
				$onumero = $do->get_rel('itcruc', 'onumero', $i);
				$montoit = $do->get_rel('itcruc', 'monto',   $i);
				$fechait = $do->get_rel('itcruc', 'ofecha',  $i);
				$tipoit  = $do->get_rel('itcruc', 'tipo',    $i);

				if($tipoit == 'ADE'){
					$mSQL = "UPDATE smov SET abonos=abonos+".$montoit." WHERE tipo_doc='".substr($onumero,0,2)."'
					         AND numero='".substr($onumero,2,8)."'
					         AND cod_cli='".$proveed."' ";
					$this->db->query($mSQL);
				}
			}

		}elseif($tipo == 'C-C'){
			// CLIENTE ----> CLIENTE
			$mNUMERO = $this->datasis->fprox_numero('nccli');
			$data = array();
			$data['cod_cli']  = $proveed;
			$data['nombre']   = $do->get('nombre');
			$data['tipo_doc'] = 'NC';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $do->get('fecha');
			$data['monto']    = $do->get('monto');
			$data['abonos']   = $do->get('monto');
			$data['impuesto'] = 0;
			$data['vence']    = $do->get('fecha');
			$data['observa1'] = $do->get('concept1');
			$data['observa2'] = $do->get('concept2');
			$data['tipo_ref'] = 'CR';
			$data['num_ref']  = $do->get('numero');

			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');
			$data['transac']  = $do->get('transac');
			$data['usuario']  = $do->get('usuario');

			$this->db->insert('smov', $data);

			// DEBE ABONAR A DESDE
			$cana = $do->count_rel('itcruc');
			for( $i = 0; $i < $cana; $i++ ){
				$onumero = $do->get_rel('itcruc', 'onumero', $i);
				$montoit = $do->get_rel('itcruc', 'monto',   $i);
				$fechait = $do->get_rel('itcruc', 'ofecha',  $i);
				$tipoit  = $do->get_rel('itcruc', 'tipo',    $i);

				if($tipoit == 'ADE'){
					$mSQL = "UPDATE smov SET abonos=abonos+".$montoit." WHERE tipo_doc='".substr($onumero,0,2)."'
					         AND numero='".substr($onumero,2,8)."'
					         AND cod_cli='".$proveed."' ";

					$this->db->query($mSQL);
				}
			}

			$mNUMERO = $this->datasis->fprox_numero('ndcli');
			$data = array();

			$data['cod_cli']  = $cliente;
			$data['nombre']   = $do->get('nomcli');
			$data['tipo_doc'] = 'ND';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $do->get('fecha');
			$data['monto']    = $do->get('monto');
			$data['abonos']   = 0;
			$data['vence']    = $do->get('fecha');
			$data['observa1'] = $do->get('concept1');
			$data['observa2'] = $do->get('concept1');
			$data['impuesto'] = 0;
			$data['tipo_ref'] = 'CR';
			$data['num_ref']  = $do->get('numero');

			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');
			$data['transac']  = $do->get('transac');
			$data['usuario']  = $do->get('usuario');

			$this->db->insert('smov', $data);

		}elseif($tipo == 'P-P'){
			// PROVEEDOR ----> PROVEEDOR
			$mNUMERO = $this->datasis->fprox_numero('num_nc');
			$data['cod_prv']  = $proveed;
			$data['nombre']   = $do->get('nombre');
			$data['tipo_doc'] = 'NC';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $do->get('fecha');
			$data['monto']    = $do->get('monto');
			$data['impuesto'] = 0;
			$data['abonos']   = $do->get('monto');
			$data['vence']    = $do->get('fecha');
			$data['observa1'] = $do->get('concept1');
			$data['observa2'] = $do->get('concept2');
			$data['tipo_ref'] = 'CR';
			$data['num_ref']  = $do->get('numero');

			$data['estampa']  = $do->get('estampa');
			$data['hora']     = $do->get('hora');
			$data['transac']  = $do->get('transac');
			$data['usuario']  = $do->get('usuario');

			$this->db->insert('sprm', $data);

			// DEBE ABONAR A DESDE
			$cana = $do->count_rel('itcruc');
			for($i=0; $i < $cana; $i++){
				$onumero = $do->get_rel('itcruc', 'onumero', $i);
				$montoit = $do->get_rel('itcruc', 'monto',   $i);
				$fechait = $do->get_rel('itcruc', 'ofecha',  $i);
				$tipoit  = $do->get_rel('itcruc', 'tipo',    $i);
				if($tipoit == 'ADE'){
					$mSQL = "UPDATE sprm SET abonos=abonos+".$montoit." WHERE tipo_doc='".substr($onumero,0,2)."'
					         AND numero='".substr($onumero,2,8)."'
					         AND cod_prv='".$proveed."' ";
					$this->db->query($mSQL);
				}
			}


			$mNUMERO = $this->datasis->fprox_numero('num_nd');
			$data = array();
			$data['cod_prv']  = $cliente;
			$data['nombre']   = $do->get('nomcli');
			$data['tipo_doc'] = 'ND';
			$data['numero']   = $mNUMERO;
			$data['fecha']    = $do->get('fecha');
			$data['monto']    = $do->get('monto');
			$data['impuesto'] = 0;
			$data['abonos']   = 0;
			$data['vence']    = $do->get('fecha');
			$data['observa1'] = $do->get('concept1');
			$data['observa2'] = $do->get('concept2');
			$data['tipo_ref'] = 'CR';
			$data['num_ref']  = $do->get('numero');

			$data['estampa'] = $do->get('estampa');
			$data['hora']    = $do->get('hora');
			$data['transac'] = $do->get('transac');
			$data['usuario'] = $do->get('usuario');

			$this->db->insert('sprm', $data);
		}

		logusu($do->table,"Creo curce de cuenta ${numero} ${primary}");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		$numero  = $do->get('numero');
		logusu($do->table,"Modifico cruce de cuenta ${numero} ${primary}");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		$numero  = $do->get('numero');
		logusu($do->table,"Elimino cruce de cuenta ${numero} ${primary}");
	}

	function instalar(){
		if (!$this->db->table_exists('cruc')) {
			$mSQL="CREATE TABLE `cruc` (
			  `numero`   varchar(8) NOT NULL DEFAULT '',
			  `fecha`    date          DEFAULT NULL,
			  `tipo`     char(3)       DEFAULT NULL,
			  `proveed`  varchar(5)    DEFAULT NULL,
			  `nombre`   varchar(40)   DEFAULT NULL,
			  `saldoa`   decimal(16,2) DEFAULT NULL,
			  `cliente`  varchar(5)    DEFAULT NULL,
			  `nomcli`   varchar(40)   DEFAULT NULL,
			  `saldod`   decimal(16,2) DEFAULT NULL,
			  `monto`    decimal(16,2) DEFAULT NULL,
			  `concept1` varchar(40)   DEFAULT NULL,
			  `concept2` varchar(40)   DEFAULT NULL,
			  `transac`  varchar(8)    DEFAULT NULL,
			  `estampa`  date          DEFAULT NULL,
			  `hora`     varchar(8)    DEFAULT NULL,
			  `usuario`  varchar(12)   DEFAULT NULL,
			  `id`       int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `numero` (`numero`),
			  KEY `transaccion` (`transac`),
			  KEY `fecha` (`fecha`)
			) ENGINE=MyISAM AUTO_INCREMENT=437 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
	}
}
