<?php
class Tbautobus extends Controller {
	var $mModulo = 'TBAUTOBUS';
	var $titp    = 'AUTOBUSES';
	var $tits    = 'AUTOBUSES';
	var $url     = 'pasajes/tbautobus/';

	function Tbautobus(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'TBAUTOBUS', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->istabla('view_tbautobus') ) {

			$mSQL = "CREATE ALGORITHM = UNDEFINED VIEW view_tbautobus AS select a.codbus, a.codacc, b.nomacc, a.capasidad, a.tipbus, a.placa, a.marca, a.modelo, a.ano, a.serialc, a.serialm, a.color, a.id 
					FROM tbautobus a JOIN tbaccio b ON a.codacc=b.codacc";
			$this->db->query($mSQL);
		}

		$this->datasis->creaintramenu(array('modulo'=>'168','titulo'=>'Autobuses','mensaje'=>'AUTOBUSES','panel'=>'PASAJES','ejecutar'=>'pasajes/tbautobus','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
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
		$grid->wbotonadd(array("id"=>"bgastos", "img"=>"images/unidad.gif",  "alt" => "Gastos", "label"=>"Gastos", "tema"=>"anexos"));
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
		$param['listados']    = $this->datasis->listados('TBAUTOBUS', 'JQ');
		$param['otros']       = $this->datasis->otros('TBAUTOBUS', 'JQ');
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
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function tbautobusadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		$("#bgastos").click(function(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'gastoforma').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( { title:"GASTOS DEL AUTOBUS", width: 350, height: 370 } );
					$("#fshow").dialog( "open" );
				});

			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';

		$bodyscript .= '
		function tbautobusedit(){
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
		function tbautobusshow(){
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
		function tbautobusdel() {
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/TBAUTOBUS').'/\'+res.id+\'/id\'').';
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
	// Formato de la ventana
	//
	function gastoforma( $id = 0 ){
		$msalida = '<script type="text/javascript">'."\n";
		$msalida .= 'var mid='.$id.";\n";

		$codbus  = $this->datasis->dameval("SELECT codbus FROM tbautobus WHERE id=$id");

		$mSQL   = "SELECT codgas, CONCAT(codgas, ' ', nomgas) descrip FROM tbgastos ORDER BY codgas ";
		$gastos = $this->datasis->llenajqselect($mSQL, false );
		
		$msalida .= '
		$("#bpos1").jqGrid({
			url:\''.site_url($this->url.'gastos').'/\'+mid,
			ajaxGridOptions: { type: "POST"},
			jsonReader: { root: "data", repeatitems: false},
			datatype: "json",
			hiddengrid: false,
			postdata: { tboficiid: "wapi"},
			width:  330,
			height: 200, 
			colNames:[\'id\', \'Codigo\',\'Gasto\', \'Porcentaje\'],
			colModel:[
				{name:\'id\',      index:\'id\',      width: 10, hidden:true},
				{name:\'codgas\',  index:\'codgas\',  width: 30, editable:true, edittype: \'select\', editoptions: { value: '.$gastos.', style:"width:160px"}},
				{name:\'nomgas\',  index:\'nomgas\',  width:100, editable:true, editoptions: {readonly:\'readonly\'}},
				{name:\'porcdes\', index:\'porcdes\', width: 40, editable:true, editoptions: {size:10,maxlength:10,dataInit:function(elem){$(elem).numeric();}},formatter:\'number\',formatoptions:{decimalSeparator:".",thousandsSeparator:",",decimalPlaces:2}, align:\'right\' }
			],
			rowNum:1000,
			pginput: false,
			pgbuttons: false,
			rowList:[],
			pager: \'#pbpos1\',
			sortname: \'id\',
			viewrecords: false,
			sortorder: "desc",
			editurl: \''.site_url($this->url.'gastos').'/\'+mid,
			caption: "Gastos"
		});
		jQuery("#bpos1").jqGrid(\'navGrid\',"#pbpos1",
			{edit:true, add:true, del:true, search: false },
			{ beforeShowForm: function(frm){ $(\'#codgas\').hide(); }},
			{ beforeShowForm: function(frm){ $(\'#codgas\').show(); } }
		);
		';
		$msalida .= "\n</script>\n";
		$msalida .= "<div class=\"tema1\"><center><table id=\"bpos1\"></table></div><div id='pbpos1'></center></div>\n";
	
		echo $msalida;

	}

	//******************************************************************
	// Gastos
	//
	function gastos($id = 0){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$mid    = $this->input->post('id');
		$data   = $_POST;
		$codbus = $this->datasis->dameval("SELECT codbus FROM tbautobus WHERE id=".$this->db->escape($id));

		unset($data['oper']);
		unset($data['id']);

		if($oper == 'add'){
			if(false == empty($data)){
				unset($data['nomgas']);
				$check = $this->datasis->dameval("SELECT count(*) FROM tbgasbus WHERE codgas=".$this->db->escape($data['codgas']));
				if ( $check == 0 ){ 
					$data['codbus'] = $codbus;
					$this->db->insert('tbgasbus', $data);
					logusu('TBAUTOBUS',"GASTO ".$this->db->escape($data['codgas'])." INCLUIDO");
					echo "Registro Agregado id=".$id;
				} else
					echo "Ya existe un descuento con ese codigo";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			unset($data['codgas']);
			unset($data['nomgas']);
			$this->db->where("id", $mid);
			$this->db->update('tbgasbus', $data);
			logusu('TBAUTOBUS',"Gasto Modificado ".$mid." MODIFICADO");
			echo "Gasto Modificado";
			
		} elseif ($oper == 'del'){
			// Borra
			$this->db->query("DELETE FROM tbgasbus WHERE id=$mid ");
			logusu('TBAUTOBUS',"GASTO ELIMINADO");
			echo "Descuento Eliminado";


		} elseif ( $oper == false ) {
				
			$this->db->select(array('a.id', 'a.codgas', 'b.nomgas', 'a.porcdes'));
			$this->db->from('tbgasbus a');
			$this->db->join('tbgastos b','a.codgas=b.codgas');
			$this->db->where('a.codbus',$codbus);
			$this->db->orderby('a.codgas');

			$rs = $this->datasis->codificautf8($this->db->get()->result_array());
			$response['data'] = $rs;
			$rs = json_encode( $response);
			echo $rs;

		}
	}


	//******************************************************************
	//Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codbus');
		$grid->label('Codigo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('codacc');
		$grid->label('CosAcc.');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('nomacc');
		$grid->label('Nombre del Accionista');
		$grid->params(array(
			//'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));

		$grid->addField('capasidad');
		$grid->label('Cap.');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
		));

		$grid->addField('tipbus');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));

		$grid->addField('placa');
		$grid->label('Placa');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));


		$grid->addField('marca');
		$grid->label('Marca');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('modelo');
		$grid->label('Modelo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('ano');
		$grid->label('Año');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: "", decimalPlaces: 0 }'
		));


		$grid->addField('serialc');
		$grid->label('Serial Carroceria');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('serialm');
		$grid->label('Serial Motor');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('color');
		$grid->label('Color');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


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
		$grid->setAdd(    $this->datasis->sidapuede('TBAUTOBUS','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('TBAUTOBUS','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('TBAUTOBUS','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('TBAUTOBUS','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: tbautobusadd, editfunc: tbautobusedit, delfunc: tbautobusdel, viewfunc: tbautobusshow");

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
		$mWHERE = $grid->geneTopWhere('view_tbautobus');

		$response   = $grid->getData('view_tbautobus', array(array()), array(), false, $mWHERE, "codbus" );
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
		$mcodp  = "??????";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM tbautobus WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('tbautobus', $data);
					echo "Registro Agregado";

					logusu('TBAUTOBUS',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM tbautobus WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM tbautobus WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE tbautobus SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("tbautobus", $data);
				logusu('TBAUTOBUS',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('tbautobus', $data);
				logusu('TBAUTOBUS',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM tbautobus WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM tbautobus WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM tbautobus WHERE id=$id ");
				logusu('TBAUTOBUS',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});
		';

		$edit = new DataEdit($this->tits, 'tbautobus');

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

		$script= ' 
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});		';
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->codbus = new inputField('Codigo','codbus');
		$edit->codbus->mode ='autohide';
		$edit->codbus->rule='required';
		$edit->codbus->size =12;
		$edit->codbus->maxlength =10;

		$edit->codacc = new dropdownField('Accionista','codacc');
		$edit->codacc->options('SELECT codacc, CONCAT(codacc," ",nomacc) nombre FROM tbaccio ORDER BY codacc');
		$edit->codacc->mode ='autohide';
		$edit->codacc->rule='required';
		//$edit->codacc->size =12;
		//$edit->codacc->maxlength =10;

		$edit->capasidad = new inputField('Capacidad','capasidad');
		$edit->capasidad->rule='';
		$edit->capasidad->size =10;
		$edit->capasidad->maxlength =8;

		$edit->tipbus = new dropdownField('Tipo bus','tipbus');
		$edit->tipbus->options("SELECT tipbus, CONCAT(tipbus,' ',desbus) descripcion FROM tbmodbus ORDER BY tipbus");
		$edit->tipbus->rule='';
		$edit->tipbus->size =12;
		$edit->tipbus->maxlength =10;

		$edit->placa = new inputField('Placa','placa');
		$edit->placa->rule='required';
		$edit->placa->size =12;
		$edit->placa->maxlength =10;

		$edit->marca = new dropdownField('Marca','marca');
		$edit->marca->options("SELECT marca, marca nombre FROM marc ORDER BY marca");
		$edit->marca->rule='required';
		//$edit->marca->size =22;
		//$edit->marca->maxlength =20;

		$edit->modelo = new inputField('Modelo','modelo');
		$edit->modelo->rule='';
		$edit->modelo->size =42;
		$edit->modelo->maxlength =40;

		$edit->ano = new inputField('Año','ano');
		$edit->ano->rule='integer';
		$edit->ano->css_class='inputonlynum';
		$edit->ano->size =5;
		$edit->ano->maxlength =4;

		$edit->serialc = new inputField('Serial Carroceria','serialc');
		$edit->serialc->rule='';
		$edit->serialc->size =52;
		$edit->serialc->maxlength =50;

		$edit->serialm = new inputField('Serial Motor','serialm');
		$edit->serialm->rule='';
		$edit->serialm->size =52;
		$edit->serialm->maxlength =50;

		$edit->color = new inputField('Color','color');
		$edit->color->rule='';
		$edit->color->size =52;
		$edit->color->maxlength =50;

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			echo $edit->output;
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

	function instalar(){
		if (!$this->db->table_exists('tbautobus')) {
			$mSQL="CREATE TABLE `tbautobus` (
			  `codbus` varchar(10) NOT NULL DEFAULT '' COMMENT 'Cod de Autobus',
			  `codacc` varchar(10) NOT NULL DEFAULT '' COMMENT 'Accionista -> tbaccio',
			  `capasidad` double NOT NULL DEFAULT '0' COMMENT 'Nro de Asientos',
			  `tipbus` varchar(10) NOT NULL DEFAULT '' COMMENT 'Tipo de unidad ->tbmodbus',
			  `placa` varchar(10) NOT NULL DEFAULT '' COMMENT 'Nro Matricula',
			  `marca` varchar(20) NOT NULL DEFAULT '' COMMENT 'Marca',
			  `modelo` varchar(40) NOT NULL DEFAULT '' COMMENT 'Modelo',
			  `ano` int(11) NOT NULL DEFAULT '2001' COMMENT 'Anno',
			  `serialc` varchar(50) NOT NULL DEFAULT '' COMMENT 'Serial Carroceria',
			  `serialm` varchar(50) NOT NULL DEFAULT '' COMMENT 'Serial Motor',
			  `color` varchar(50) NOT NULL DEFAULT '' COMMENT 'Serial Motor',
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Serial Motor',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codbus` (`codbus`)
			) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=latin1 COMMENT='Autobus'";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbautobus');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

?>
