<?php
class Rrepu extends Controller {
	var $mModulo = 'RREPU';
	var $titp    = 'SOLICITUD DE REPUESTO';
	var $tits    = 'SOLICITUD DE REPUESTO';
	var $url     = 'rma/rrepu/';

	function Rrepu(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'RREPU', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'351','titulo'=>'Solicitud de repuesto','mensaje'=>'Solicitud de repuesto','panel'=>'RMA','ejecutar'=>'rma/rrepu','target'=>'popu','visible'=>'S','pertenece'=>'3','ancho'=>900,'alto'=>600));
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
		array("id"=>"fedita",  "title"=>"Agregar/Editar Registro")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('RREPU', 'JQ');
		$param['otros']       = $this->datasis->otros('RREPU', 'JQ');
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
		function rrepuadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function rrepuedit() {
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
								'.$this->datasis->jwinopen(site_url('formatos/ver/RREPU').'/\'+res.id+\'/id\'').';
								return true;
							} else {
								apprise(json.mensaje);
							}
						}catch(e){
							$("#fedita").html(r);
						}
					}
			})},
			"Cancelar": function() { $( this ).dialog( "close" ); }
			},
			close: function() { allFields.val( "" ).removeClass( "ui-state-error" );}
		});';
		$bodyscript .= '});'."\n";

		$bodyscript .= "\n</script>\n";
		$bodyscript .= "";
		return $bodyscript;
	}

	//******************************************************************
	//Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

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


		$grid->addField('proveed');
		$grid->label('Cod provee');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('nombre');
		$grid->label('Proveedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:45, maxlength: 45 }',
		));


		$grid->addField('idrnoti');
		$grid->label('Num notifica');
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


		$grid->addField('codprod');
		$grid->label('Cod producto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('descprod');
		$grid->label('Descripcion producto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('serial');
		$grid->label('Serial');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:35, maxlength: 35 }',
		));


		$grid->addField('diagnostico');
		$grid->label('Diagnostico');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('repuesto');
		$grid->label('Repuesto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('cant');
		$grid->label('Cant');
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


		$grid->addField('reporte');
		$grid->label('Reporte');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('estado');
		$grid->label('Recibido');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('fecharecep');
		$grid->label('Fecha recep');
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

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('RREPU','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('RREPU','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('RREPU','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('RREPU','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("\t\taddfunc: rrepuadd,\n\t\teditfunc: rrepuedit");

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
	function getdata()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('rrepu');

		$response   = $grid->getData('rrepu', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/*******************************************************************
	* Guarda la Informacion
	*/
	function setData()
	{
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
				$check = $this->datasis->dameval("SELECT count(*) FROM rrepu WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('rrepu', $data);
					echo "Registro Agregado";

					logusu('rrepu',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM rrepu WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM rrepu WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE rrepu SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("rrepu", $data);
				logusu('rrepu',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('rrepu', $data);
				logusu('rrepu',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM rrepu WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM rrepu WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM rrepu WHERE id=$id ");
				logusu('RREPU',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


	//******************************************************************
	//
	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'rrepu');

		$script = '
		$("#idrnoti").autocomplete({
			source: function( req, add){
				$.ajax({
					url:  "'.site_url('ajax/buscarnoti').'",
					type: "POST",
					dataType: "json",
					data: "q="+req.term,
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$("#idrnoti").val("");
								$("#serial").val("");
								$("#codprod").val("");
								$("#descprod").val("");
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
				$("#idrnoti").attr("readonly", "readonly");
				$("#idrnoti" ).val(ui.item.idrnoti);
				$("#serial"  ).val(ui.item.serial);
				$("#codprod" ).val(ui.item.codprod);
				$("#descprod").val(ui.item.descprod);
				setTimeout(function() {  $("#idrnoti").removeAttr("readonly"); }, 1500);
			}
		});
		';


		$script .= "
		$('#proveed').autocomplete({
			delay: 600,
			autoFocus: true,
			source: function( req, add){
				$.ajax({
					url:  '".site_url('ajax/buscasprv')."',
					type: 'POST',
					dataType: 'json',
					data: 'q='+req.term,
					success:
						function(data){
							if(data.length==0){
								$('#nombre').val('');
								$('#nombre_val').text('');
								$('#proveed').val('');
							}else{
								var sugiere = [];
								$.each(data,
									function(i, val){
										sugiere.push( val );
									}
								);
								add(sugiere);
							}
						},
				})
			},
			minLength: 2,
			select: function( event, ui ) {
				$('#proveed').attr('readonly', 'readonly');
				$('#nombre').val(ui.item.nombre);
				$('#nombre_val').text(ui.item.nombre);
				$('#proveed').val(ui.item.proveed);
				setTimeout(function() { $('#proveed').removeAttr('readonly'); }, 1500);
			}
		});
		";



		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert' ,'_pre_insert' );
		$edit->pre_process('update' ,'_pre_update' );
		$edit->pre_process('delete' ,'_pre_delete' );

		$edit->id = new inputField('Numero','id');
		$edit->id->rule='integer';
		$edit->id->css_class='inputonlynum';
		$edit->id->size =8;
		$edit->id->maxlength =11;
		$edit->id->insertValue = '0';
		$edit->id->readonly = true;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->size =10;
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->maxlength =8;
		$edit->fecha->calendar = false;
		$edit->fecha->readonly = true;

		$edit->proveed = new inputField('Proveedor','proveed');
		$edit->proveed->rule='';
		$edit->proveed->size =7;
		$edit->proveed->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='';
		$edit->nombre->size =40;
		$edit->nombre->maxlength =45;
		$edit->nombre->readonly = true;

		$edit->idrnoti = new inputField('Notificacion','idrnoti');
		$edit->idrnoti->rule='integer';
		$edit->idrnoti->css_class='inputonlynum';
		$edit->idrnoti->size =8;
		$edit->idrnoti->maxlength =11;

		$edit->codprod = new inputField('Producto','codprod');
		$edit->codprod->rule='';
		$edit->codprod->size =15;
		$edit->codprod->maxlength =15;
		$edit->codprod->readonly = true;

		$edit->descprod = new inputField('Descripcion','descprod');
		$edit->descprod->rule='';
		$edit->descprod->size =35;
		$edit->descprod->maxlength =45;
		$edit->descprod->readonly = true;

		$edit->serial = new inputField('Serial','serial');
		$edit->serial->rule='';
		$edit->serial->size =30;
		$edit->serial->maxlength =35;
		$edit->serial->readonly = true;

		$edit->diagnostico = new textareaField('Diagnostico','diagnostico');
		$edit->diagnostico->rule='';
		$edit->diagnostico->cols = 60;
		$edit->diagnostico->rows = 4;
		$edit->diagnostico->readonly = true;

		$edit->repuesto = new textareaField('Repuesto','repuesto');
		$edit->repuesto->rule='';
		$edit->repuesto->cols = 60;
		$edit->repuesto->rows = 4;

		$edit->cant = new inputField('Cantidad','cant');
		$edit->cant->rule='integer';
		$edit->cant->css_class='inputonlynum';
		$edit->cant->size = 8;
		$edit->cant->maxlength = 11;
		$edit->cant->insertValue = '1';


		$edit->reporte = new inputField('Reporte','reporte');
		$edit->reporte->rule = '';
		$edit->reporte->size = 22;
		$edit->reporte->maxlength = 20;
		$edit->reporte->readonly = true;

		$edit->estado = new inputField('Recibido','estado');
		$edit->estado->rule = '';
		$edit->estado->size = 4;
		$edit->estado->maxlength = 2;

		$edit->fecharecep = new dateField('Fecha recepcion','fecharecep');
		$edit->fecharecep->rule = 'chfecha';
		$edit->fecharecep->size = 10;
		$edit->fecharecep->maxlength = 8;

		$edit->build();

		$script= '';

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']   =&  $edit;
			$conten['script'] =  '';
			$this->load->view('view_rrepu', $conten);
			//echo $edit->output;
		}
	}

	function _pre_insert($do){
		return true;
	}

	function _pre_update($do){
		return true;
	}

	function _pre_delete($do){
		return true;
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
}

?>
