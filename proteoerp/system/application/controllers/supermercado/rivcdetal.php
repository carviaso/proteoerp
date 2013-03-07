<?php require_once(BASEPATH.'application/controllers/finanzas/common.php');
class rivcdetal extends Controller {
	var $mModulo = 'RIVCDETAL';
	var $titp    = 'Retenciones de clientes al detal';
	var $tits    = 'Retenciones de clientes al detal';
	var $url     = 'supermercado/rivcdetal/';

	function Rivcdetal(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'RIVCDETAL', $ventana=0 );
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
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$grid->wbotonadd(array('id'=>'reteprin', 'img'=>'images/print.png', 'alt' => 'Formato PDF', 'label'=>'Reimprimir Retención'));
		$WestPanel = $grid->deploywestp();

		$adic = array(
		array("id"=>"fedita", "title"=>"Agregar/Editar Registro"),
		array("id"=>"fborra", "title"=>"Eliminar registro")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('RIVCDETAL', 'JQ');
		$param['otros']       = $this->datasis->otros('RIVCDETAL', 'JQ');
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
		jQuery("#reteprin").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				'.$this->datasis->jwinopen(site_url('formatos/ver/RIVCDETAL').'/\'+id+"/id"').';
			} else { $.prompt("<h1>Por favor Seleccione un registro</h1>");}
		});';

		$bodyscript .= '
		function rivcdetaladd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function rivcdetaledit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fborra").html("");
					$("#fedita").html(data);
					$("#fedita").dialog({ buttons: { Ok: function() { $( this ).dialog( "close" ); } } });
					$("#fedita").dialog( "open" );
				});
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';


		$bodyscript .= '
		function rivcdetaldel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea anular el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						$("#fedita").html("");
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
			autoOpen: false, height: 500, width: 700, modal: true,
			buttons: {
			"Guardar": function() {
				var bValid = true;
				var murl = $("#df1").attr("action");
				allFields.removeClass( "ui-state-error" );
				$.ajax({
					type: "POST",
					dataType: "json",
					async: false,
					url: murl,
					data: $("#df1").serialize(),
					success: function(r,s,x){
						if ( r.status == "A" ) {
							$( "#fedita" ).dialog( "close" );
							grid.trigger("reloadGrid");
							apprise("Registro Guardado");
							'.$this->datasis->jwinopen(site_url('formatos/ver/RIVCDETAL').'/\'+r.pk.id+\'/id\'').';
							return true;
						} else {
							apprise(r.mensaje);
						}
					}
			})},
			"Cancelar": function() { $( this ).dialog( "close" ); }
			},
			close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
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
			close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
		});';

		$bodyscript .= '});'."\n";

		$bodyscript .= "\n</script>\n";
		$bodyscript .= "";
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


		$grid->addField('caja');
		$grid->label('Caja');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('cliente');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
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


		$grid->addField('emision');
		$grid->label('Emision');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('recepcion');
		$grid->label('Recepcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
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


		$grid->addField('reiva');
		$grid->label('Reiva');
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


		$grid->addField('comprob');
		$grid->label('Comprob');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('periodo');
		$grid->label('Periodo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('transac');
		$grid->label('Transac');
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
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
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


		$grid->addField('numeroch');
		$grid->label('Numeroch');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:12, maxlength: 12 }',
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

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('RIVCDETAL','INCLUIR%' ));
		//$grid->setEdit(   $this->datasis->sidapuede('RIVCDETAL','MODIFICA%'));
		$grid->setEdit(false);
		$grid->setDelete( $this->datasis->sidapuede('RIVCDETAL','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('RIVCDETAL','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: rivcdetaladd, editfunc: rivcdetaledit, delfunc: rivcdetaldel");

		$grid->setOnSelectRow('
			function(id){

			},
			afterInsertRow:
			function( rid, aData, rowe){
				if ( aData.anulado == "S" ){
					$(this).jqGrid( "setCell", rid, "id","", {color:"#FFFFFF", background:"#960F18" });
				}
			}
		');


		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		$grid->setOndblClickRow("");

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
		$mWHERE = $grid->geneTopWhere('rivcdetal');

		$response   = $grid->getData('rivcdetal', array(array()), array(), false, $mWHERE );
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
		$mcodp  = "grupo";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			//if(false == empty($data)){
			//	$check = $this->datasis->dameval("SELECT count(*) FROM rivcdetal WHERE $mcodp=".$this->db->escape($data[$mcodp]));
			//	if ( $check == 0 ){
			//		$this->db->insert('rivcdetal', $data);
			//		echo "Registro Agregado";
            //
			//		logusu('RIVCDETAL',"Registro ????? INCLUIDO");
			//	} else
			//		echo "Ya existe un registro con ese $mcodp";
			//} else
			//	echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM rivcdetal WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM rivcdetal WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE rivcdetal SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("rivcdetal", $data);
				logusu('RIVCDETAL',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('rivcdetal', $data);
				logusu('RIVCDETAL',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			//$meco = $this->datasis->dameval("SELECT $mcodp FROM rivcdetal WHERE id=$id");
			////$check =  $this->datasis->dameval("SELECT COUNT(*) FROM rivcdetal WHERE id='$id' ");
			//if ($check > 0){
			//	echo " El registro no puede ser eliminado; tiene movimiento ";
			//} else {
			//	$this->db->simple_query("DELETE FROM rivcdetal WHERE id=$id ");
			//	logusu('RIVCDETAL',"Registro ????? ELIMINADO");
			//	echo "Registro Eliminado";
			//}
		};
	}


	function dataedit(){
		$this->rapyd->load('dataedit');

		$script= '
		function getFactura(){
			numero= $("#numero").val();
			fecha = $("#fecha").val();
			caja  = $("#caja").val();

			if(numero!="" && fecha!="" && caja!=""){
				$.ajax({
					type: "POST",
					url: "'.site_url('supermercado/rivcdetal/getfac').'",
					data: { numero: numero, fecha: fecha, caja: caja },
					dataType: "json"
				}).done(function(data) {
					var  undef;
					if (data.gtotal === undef || data.nombres === undef || data.cliente === undef || data.impuestos===undef){
						$("#nombre_val").text("Factura no encontrada.");
						$("#impuesto").val(0);
						$("#impuesto_val").text(0);
						$("#monto").val(0);
						$("#cliente").val("");
						$("#nombre").val("");
						$("#cliente_val").text("");
						$("#monto_val").text("");
						$("#reiva").val(0);
					}else{
						$("#impuesto").val(data.impuestos);
						$("#impuesto_val").text(nformat(data.impuestos));
						$("#monto").val(data.gtotal);
						$("#cliente").val(data.cliente);
						$("#nombre").val(data.nombres);
						$("#nombre_val").text(data.nombres);
						$("#cliente_val").text(data.cliente);
						$("#monto_val").text(nformat(data.gtotal));
						$("#reiva").val(roundNumber(data.impuestos*0.75,2));
					}
				});
			}
		}

		$(function() {
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
			$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
			$("#emision").datepicker({ dateFormat: "dd/mm/yy" });
			$("#recepcion").datepicker({ dateFormat: "dd/mm/yy" });
		});';

		$edit = new DataEdit($this->tits, 'rivcdetal');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');

		$edit->numero = new inputField('N&uacute;mero de referencia','numero');
		$edit->numero->rule='max_length[8]|required';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;
		$edit->numero->onchange='getFactura()';
		$edit->numero->onkeyup ='getFactura()';
		$edit->numero->group='Detalles de la factura';

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha|required';
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->calendar=false;
		$edit->fecha->onchange='getFactura()';
		$edit->fecha->group='Detalles de la factura';

		$edit->caja = new dropdownField('Caja','caja');
		$edit->caja->option('','Seleccionar');
		$edit->caja->options('SELECT caja,caja AS lab FROM caja ORDER BY caja');
		$edit->caja->rule='max_length[5]|required';
		$edit->caja->group='Detalles de la factura';
		$edit->caja->onchange='getFactura()';
		$edit->caja->style='width:150px;';

		$edit->cliente = new inputField('Nombre del cliente','cliente');
		$edit->cliente->type='inputhidden';
		$edit->cliente->group='Detalles de la factura';

		$edit->nombre = new inputField('','nombre');
		$edit->nombre->rule='max_length[200]';
		$edit->nombre->type='inputhidden';
		$edit->nombre->in = 'cliente';

		$edit->monto = new inputField('Monto de la factura','monto');
		$edit->monto->rule='max_length[10]|numeric|required';
		$edit->monto->type='inputhidden';
		$edit->monto->group='Detalles de la factura';

		$edit->impuesto = new inputField('Impuesto de la factura','impuesto');
		$edit->impuesto->rule='numeric|required|mayorcero';
		$edit->impuesto->type='inputhidden';
		$edit->impuesto->group='Detalles de la factura';

		$edit->periodo = new inputField('Per&iacute;odo-Comprobante','periodo');
		$edit->periodo->rule='max_length[6]|required';
		$edit->periodo->size =6;
		$edit->periodo->insertValue=date('Ym');
		$edit->periodo->maxlength =6;
		$edit->periodo->group='Detalles de la retenci&oacute;n';

		$edit->emision = new dateField('Fecha de emisi&oacute;n','emision');
		$edit->emision->rule='chfecha|required';
		$edit->emision->size =10;
		$edit->emision->calendar=false;
		$edit->emision->maxlength =8;
		$edit->emision->group='Detalles de la retenci&oacute;n';

		$edit->recepcion = new dateField('Fecha de recepci&oacute;n','recepcion');
		$edit->recepcion->rule='chfecha|required';
		$edit->recepcion->insertValue=date('Ymd');
		$edit->recepcion->size =10;
		$edit->recepcion->calendar=false;
		$edit->recepcion->maxlength =8;
		$edit->recepcion->group='Detalles de la retenci&oacute;n';

		$edit->comprob = new inputField('','comprob');
		$edit->comprob->rule='max_length[8]|required';
		$edit->comprob->size =10;
		$edit->comprob->in = 'periodo';
		$edit->comprob->maxlength =8;

		$edit->reiva = new inputField('Monto retenido','reiva');
		$edit->reiva->rule='callback_chreiva|numeric|required|mayorcero';
		$edit->reiva->css_class='inputnum';
		$edit->reiva->size =12;
		$edit->reiva->maxlength =10;
		$edit->reiva->group='Detalles de la retenci&oacute;n';

		$edit->codbanc = new dropdownField('Caja para reintegro','codbanc');
		$edit->codbanc->option('','Seleccionar');
		$edit->codbanc->options("SELECT codbanc, CONCAT_WS('-',codbanc,banco) AS label FROM banc WHERE activo='S' ORDER BY codbanc");
		$edit->codbanc->rule='max_length[5]|required';
		$edit->codbanc->style='width:200px;';
		$edit->codbanc->group='Reintegro';

		$edit->numeroch = new inputField('N&uacute;mero de cheque','numeroch');
		$edit->numeroch->rule='max_length[12]|condi_required|callback_chobligaban';
		$edit->numeroch->size =14;
		$edit->numeroch->append('Solo si se paga con un cheque de banco.');
		$edit->numeroch->maxlength =12;
		$edit->numeroch->group='Reintegro';

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->origen  = new autoUpdateField('origen' ,'D','D'); //Detal

		//$edit->buttons('modify', 'save', 'undo', 'delete', 'back','add');
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

		//$data['content'] = $edit->output;
		/*$data['head']    = $this->rapyd->get_head();
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['script'] .= phpscript('nformat.js');
		$data['script'] .= $script;
		$data['title']   = heading($this->tits);
		$this->load->view('view_ventanas', $data);*/
	}

	function chobligaban($val){
		$ban=$this->input->post('codbanc');
		$tipo=common::_traetipo($ban);
		if($tipo!='CAJ'){
			if(empty($val)){
				$this->validation->set_message('chobligaban', 'El campo %s es obligatorio cuando el cargo es a un banco');
				return false;
			}
		}
		return true;
	}

	function chreiva($val){
		$impuesto=round($this->input->post('impuesto'),2);
		$reiva   =round($val,2);
		if($reiva > $impuesto){
			$this->validation->set_message('chreiva', 'El impuesto retenido es mayor al impuesto de la factura');
			return false;
		}
		return true;
	}

	function getfac(){
		$numero  = $this->input->post('numero');
		$fecha   = $this->input->post('fecha');
		$caja    = $this->input->post('caja');

		$retArray =  array();
		if($numero*$fecha*$caja == false){
			echo json_encode($retArray);
			return '';
		}else{
			$fecha = human_to_dbdate($fecha);
		}

		$dbnumero  = $this->db->escape($numero);
		$dbfecha   = $this->db->escape($fecha);
		$dbcaja    = $this->db->escape($caja);

		$mSQL="SELECT cliente, CONCAT_WS(' ',nombres,apellidos) AS nombres, gtotal,impuesto
		FROM viefac
		WHERE numero=$dbnumero AND fecha=$dbfecha AND caja=$dbcaja LIMIT 1";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row_array();

			$retArray['cliente']  = $row['cliente'];
			$retArray['nombres']  = utf8_encode($row['nombres']);
			$retArray['gtotal']   = $row['gtotal'];
			$retArray['impuestos']= $row['impuesto'];
		}

		echo json_encode($retArray);
		return true;
	}

	function _pre_insert($do){
		$dbnumero = $this->db->escape($do->get('numero'));
		$dbfecha  = $this->db->escape($do->get('fecha'));
		$dbcaja   = $this->db->escape($do->get('caja'));

		$mSQL = "SELECT COUNT(*) FROM rivcdetal WHERE numero=$dbnumero AND fecha=$dbfecha AND caja=$dbcaja AND anulado='N'";
		$cana = $this->datasis->dameval($mSQL);
		if($cana>0){
			$do->error_message_ar['pre_ins']='La factura en cuesti&oacute;n ya le fue aplicada una retenci&oacute;n.';
			return false;
		}

		$mSQL="SELECT COUNT(*) FROM viefac WHERE numero=$dbnumero AND fecha=$dbfecha AND caja=$dbcaja";
		$cana=$this->datasis->dameval($mSQL);
		if($cana > 0){

		}else{
			$do->error_message_ar['pre_ins']='La factura a la cual se le esta reteniendo no fue encontrada';
			return false;
		}

		$transac = $this->datasis->fprox_numero('ntransa');
		$do->set('transac', $transac);

		$mSQL = "DELETE FROM smov WHERE transac='$transac'";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'RIVC'); }

	}

	function _pre_update($do){
		return false;
	}

	function _pre_delete($do){
		$id        = $do->get('id');
		$transac   = $do->get('transac');
		$codbanc   = $do->get('codbanc');
		$monto     = $do->get('reiva');
		$anulado   = $do->get('anulado');
		$dbtransac= $this->db->escape($transac);

		if($anulado=='S'){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Esta retenci&oacute;n ya estaba anulada';
			return false;
		}

		$abono=$this->datasis->dameval("SELECT abonos FROM smov WHERE transac=${dbtransac}");
		if($abono==0){
			$mSQL="DELETE FROM smov WHERE transac=${dbtransac}";
			$this->db->simple_query($mSQL);

			$mSQL="DELETE FROM bmov WHERE transac=${dbtransac}";
			$this->db->simple_query($mSQL);

			$mSQL="UPDATE rivcdetal SET anulado='S' WHERE id=${id}";
			$this->db->simple_query($mSQL);

			$this->datasis->actusal($codbanc, date('Ymd'), $monto);
		}else{
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Los efectos relacionados han sido abonados, debe reversarlos para poder continuar.';
		}

		$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Retenci&oacute;n anulada';
		return false;
	}

	function _post_insert($do){
		$error    = 0;

		$transac  = $do->get('transac');
		$estampa  = $do->get('estampa');
		$hora     = $do->get('hora');
		$usuario  = $do->get('usuario');
		$monto    = $do->get('reiva');
		$efecha   = $do->get('emision');
		$fecha    = $do->get('recepcion');
		$ex_fecha = explode('-',$fecha);
		$numero   = $do->get('numero');
		$vence    = $ex_fecha[0].$ex_fecha[1].days_in_month($ex_fecha[1],$ex_fecha[0]);
		$nombre   = $do->get('nombre');

		$periodo  = $do->get('periodo');
		$cnumero  = $do->get('comprob');
		$comprob  = $periodo.$cnumero;

		//Falta implementar
		if($numero[0]=='N'){ //Es una nota de credito
			//$mnumnd = $this->datasis->fprox_numero('ndcli');
			////Devoluciones debe crear un NC si esta en el periodo
			//$mnumnc = $this->datasis->fprox_numero('nccli');
			//$data=array();
			//$data['cod_cli']    = 'REIVA';
			//$data['nombre']     = 'RETENCION DE I.V.A. POR COMPENSAR';
			//$data['tipo_doc']   = 'NC';
			//$data['numero']     = $mnumnc;
			//$data['fecha']      = $fecha;
			//$data['monto']      = $itmonto;
			//$data['impuesto']   = 0;
			//$data['abonos']     = 0;
			//$data['vence']      = $fecha;
			//$data['tipo_ref']   = 'DV';
			//$data['num_ref']    = tnumero;
			//$data['observa1']   = 'RET/IVA DETAL A DOC. NC'.$itnumero;
			//$data['estampa']    = $estampa;
			//$data['hora']       = $hora;
			//$data['transac']    = $transac;
			//$data['usuario']    = $usuario;
			//$data['codigo']     = 'NOCON';
			//$data['descrip']    = 'NOTA DE CONTABILIDAD';
			//$data['nroriva']    = $comprob;
			//$data['emiriva']    = $efecha;
            //
			//$mSQL = $this->db->insert_string('smov', $data);
			//$ban=$this->db->simple_query($mSQL);
			//if($ban==false){ memowrite($mSQL,'rivc'); }
		}else{ //Es una factura
			$mnumnd = $this->datasis->fprox_numero('ndcli');

			$data=array();
			$data['cod_cli']    = 'REIVA';
			$data['nombre']     = 'RETENCION DE I.V.A. POR COMPENSAR';
			$data['tipo_doc']   = 'ND';
			$data['numero']     = $mnumnd;
			$data['fecha']      = $fecha;
			$data['monto']      = $monto;
			$data['impuesto']   = 0;
			$data['abonos']     = 0;
			$data['vence']      = $vence;
			$data['tipo_ref']   = 'FC';
			$data['num_ref']    = $do->get('numero');
			$data['observa1']   = 'RET/IVA DETAL A DOC. FC'.$numero;
			$data['estampa']    = $estampa;
			$data['hora']       = $hora;
			$data['transac']    = $transac;
			$data['usuario']    = $usuario;
			$data['codigo']     = 'NOCON';
			$data['descrip']    = 'NOTA DE CONTABILIDAD';
			$data['nroriva']    = $comprob;
			$data['emiriva']    = $efecha;

			$mSQL = $this->db->insert_string('smov', $data);
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'RIVCSUPER'); }

			//Crea el reintegro
			$codbanc  = $do->get('codbanc');
			$datacar  = common::_traebandata($codbanc);
			$sp_fecha = date('Ymd');
			$ttipo    = $datacar['tbanco'];
			$moneda   = $datacar['moneda'];
			$negreso  = $this->datasis->fprox_numero('negreso');

			if($ttipo=='CAJ'){
				$numeroch = $this->datasis->fprox_numero('ncaja'.$codbanc);
				$tipo_op  = 'ND';
				$tipo1    = 'D' ;
			}else{
				$numeroch = $do->get('numeroch');
				$tipo_op  =  'CH';
				$tipo1    =  'C' ;
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
			$data['codcp']      = 'REIVA';
			$data['nombre']     = $nombre;
			$data['monto']      = $monto;
			$data['concepto']   = 'REINTEGRO RET/IVA DETAL';
			$data['concep2']    = ' CR'.$comprob;
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
			if($ban==false){ memowrite($sql,'rivcdetal'); $error++;}

			$this->datasis->actusal($codbanc, $sp_fecha, (-1)*$monto);
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary = implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		if (!$this->db->table_exists('rivcdetal')) {
			$mSQL="CREATE TABLE `rivcdetal` (
				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`numero` CHAR(8) NULL DEFAULT NULL COMMENT 'Numero de la factura',
				`fecha` DATE NULL DEFAULT NULL COMMENT 'Fecha de la factura',
				`caja` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Cajar de la factura',
				`cliente` VARCHAR(12) NULL DEFAULT NULL COMMENT 'Codigo de club',
				`nombre` VARCHAR(200) NULL DEFAULT NULL COMMENT 'Nombre del cliente',
				`emision` DATE NULL DEFAULT NULL COMMENT 'Emision del comprobante',
				`recepcion` DATE NULL DEFAULT NULL COMMENT 'Recepcion del comprobante',
				`monto` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Monto de la factura',
				`impuesto` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Impuesto de la factura',
				`reiva` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Cantidad retenida',
				`comprob` VARCHAR(8) NULL DEFAULT NULL COMMENT 'Numero de comprobante',
				`periodo` CHAR(8) NULL DEFAULT NULL,
				`transac` VARCHAR(8) NULL DEFAULT NULL,
				`usuario` VARCHAR(15) NULL DEFAULT NULL,
				`estampa` DATETIME NULL DEFAULT NULL,
				`hora` CHAR(8) NULL DEFAULT NULL,
				`origen` CHAR(1) NULL DEFAULT NULL,
				`codbanc` CHAR(2) NULL DEFAULT NULL,
				`numeroch` VARCHAR(12) NULL DEFAULT NULL,
				`anulado` CHAR(1) NULL DEFAULT 'N',
				PRIMARY KEY (`id`),
				INDEX `numero_fecha_caja` (`numero`, `fecha`, `caja`),
				INDEX `transac` (`transac`)
			)
			COMMENT='rivc de supermercado'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}
	}
}
