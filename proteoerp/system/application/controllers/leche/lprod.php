<?php
class Lprod extends Controller {
	var $mModulo = 'LPROD';
	var $titp    = 'Control de producci&oacute;n';
	var $tits    = 'Control de producci&oacute;n';
	var $url     = 'leche/lprod/';

	function Lprod(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'LPROD', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		$this->datasis->creaintramenu(array('modulo'=>'223','titulo'=>'Control de Producción','mensaje'=>'Control de Producción','panel'=>'LECHE','ejecutar'=>'leche/lprod','target'=>'popu','visible'=>'S','pertenece'=>'2','ancho'=>900,'alto'=>600));
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$grid->setHeight('150');
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$grid1->setHeight('180');
		$param['grids'][] = $grid1->deploy();

		// Configura los Paneles
		$readyLayout = $grid->readyLayout2( 212, 210, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'imprime', 'img'=>'assets/default/images/print.png','alt' => 'Reimprimir','label'=>'Reimprimir Documento'));

		if($this->datasis->sidapuede('LCIERRE','INCLUIR%')){
			$grid->wbotonadd(array('id'=>'bcierre', 'img'=>'images/candado.png' ,'alt' => 'Cierre Producci&oacute;n','label'=>'Cierre Producci&oacute;n'));
		}
		$WestPanel = $grid->deploywestp();

		//Panel Central
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		$adic = array(
			array('id'=>'fedita' , 'title'=>'Agregar/Editar Producci&oacute;n'),
			array('id'=>'fshow'  , 'title'=>'Mostrar producci&oacute;n')
		);

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']    = $WestPanel;
		$param['script']       = script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('LPROD', 'JQ');
		$param['otros']        = $this->datasis->otros('LPROD', 'JQ');
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
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		function lprodadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			});
		};';


		$bodyscript .= '
		function lproddel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						$("#fborra").html(data);
						$("#fborra").dialog( "open" );
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';


		$bodyscript .= '
		function lprodshow() {
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
		function lprodedit() {
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
		jQuery("#bcierre").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret      = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				var idcierre = $.ajax({ type: "POST", url: "'.site_url($this->url.'getcierre').'/"+ret.fecha, async: false }).responseText;

				if(idcierre == "0"){
					$.post("'.site_url('leche/lcierre/dataedit/').'/"+ret.fecha+"/create",
					function(data){
						$("#fedita").html(data);
						$("#fedita").dialog( "open" );
					});
				}else{
					var status = $.ajax({ type: "POST", url: "'.site_url($this->url.'getstatus').'/"+ret.fecha, async: false }).responseText;

					if(status=="A"){
						$.post("'.site_url('leche/lcierre/dataedit/').'/"+ret.fecha+"/modify/"+idcierre,
						function(data){
							$("#fedita").html(data);
							$("#fedita").dialog( "open" );
						});
					}else{
						$.post("'.site_url('leche/lcierre/dataedit/').'/show/"+idcierre,
						function(data){
							$("#fshow").html(data);
							$("#fshow").dialog( "open" );
						});
					}
				}
			} else {
				$.prompt("<h1>Por favor Seleccione un Movimiento</h1>");
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
				allFields.val( "" ).removeClass( "ui-state-error" );
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
								//'.$this->datasis->jwinopen(site_url('formatos/ver/LRECE').'/\'+res.id+\'/id\'').';
								return true;
							}else{
								apprise(json.mensaje);
							}
						}catch(e){
							$("#fedita").html(r);
						}
					}
				})
			},
			"Cancelar": function() {
					$("#fedita").html(""); $( this ).dialog( "close" );
				}
			},
			close: function() { $("#fedita").html(""); allFields.val( "" ).removeClass( "ui-state-error" );}
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
		$editar = "false";

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

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'left'",
			'edittype'      => "'text'",
			'width'         => 60,
			'editrules'     => '{ required:true }',
			//'editoptions'   => '{ size:5, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',

		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'left'",
			'edittype'      => "'text'",
			'width'         => 200,
			'editrules'     => '{ required:true }',
			//'editoptions'   => '{ size:20, maxlength: 20, dataInit: function (elem) { $(elem).numeric(); }  }',
		));


		$grid->addField('litros');
		$grid->label('Litros');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			//'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
		));

		$grid->addField('inventario');
		$grid->label('Inventario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
		));

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 12 }',
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
			}'
		);
		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('LPROD','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('LPROD','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('LPROD','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('LPROD','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: lprodadd, editfunc: lprodedit,delfunc: lproddel, viewfunc: lprodshow");

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
		$mWHERE = $grid->geneTopWhere('lprod');

		$response   = $grid->getData('lprod', array(array()), array(), false, $mWHERE, 'id','desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/****************************
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
				$check = $this->datasis->dameval("SELECT count(*) FROM lprod WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('lprod', $data);
					echo "Registro Agregado";

					logusu('LPROD',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM lprod WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM lprod WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE lprod SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("lprod", $data);
				logusu('LPROD',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('lprod', $data);
				logusu('LPROD',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM lprod WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM lprod WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM lprod WHERE id=$id ");
				logusu('LPROD',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		/*$grid->addField('id');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('id_lprod');
		$grid->label('Id_lprod');
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
		));*/


		$grid->addField('codrut');
		$grid->label('Ruta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('litros');
		$grid->label('Litros');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
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
		if ($id === 0 ){
			$id = $this->datasis->dameval("SELECT MAX(id) FROM lprod");
		}
		if(empty($id)) return "";

		$dbid  = $this->db->escape($id);
		$grid  = $this->jqdatagrid;
		$mSQL  = "SELECT * FROM itlprod WHERE id_lprod=$dbid";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setDatait(){
	}

	//***********************************
	// DataEdit
	//***********************************

	function dataedit(){
		$this->rapyd->load('datadetails','dataobject');

		$do = new DataObject('lprod');
		//$do->pointer('scli' ,'scli.cliente=rivc.cod_cli','sprv.tipo AS sprvtipo, sprv.reteiva AS sprvreteiva','left');
		$do->rel_one_to_many('itlprod' ,'itlprod' ,array('id'=>'id_lprod'));

		$edit = new DataDetails($this->tits, $do);
		$edit->on_save_redirect=false;

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert','_pre_insert');
		$edit->pre_process('update','_pre_update');
		$edit->pre_process('delete','_pre_delete');

		$edit->codigo = new inputField('Producto','codigo');
		$edit->codigo->rule='required';
		$edit->codigo->size =12;
		$edit->codigo->maxlength =10;

		$edit->descrip = new inputField('Descripci&oacute;n','descrip');
		$edit->descrip->type='inputhidden';
		$edit->descrip->size =12;
		$edit->descrip->maxlength =10;

		$edit->fecha = new dateField('Fecha','fecha');
		$edit->fecha->rule='chfecha|required';
		$edit->fecha->size =12;
		$edit->fecha->insertValue=date('Y-m-d');
		$edit->fecha->maxlength =8;
		$edit->fecha->calendar=false;

		$edit->inventario = new inputField('Leche de inventario','inventario');
		$edit->inventario->rule='max_length[12]|numeric|required';
		$edit->inventario->css_class='inputnum';
		$edit->inventario->size =12;
		$edit->inventario->insertValue='0';
		$edit->inventario->onkeyup='totalizar();';
		$edit->inventario->maxlength =12;

		$edit->litros = new inputField('Litros totales','litros');
		$edit->litros->rule='max_length[12]|numeric';
		$edit->litros->css_class='inputnum';
		$edit->litros->type='inputhidden';
		$edit->litros->size =14;
		$edit->litros->maxlength =12;

		$edit->peso = new inputField('Peso','peso');
		$edit->peso->rule='max_length[12]|numeric';
		$edit->peso->css_class='inputnum';
		$edit->peso->type='inputhidden';
		$edit->peso->size =14;
		$edit->peso->maxlength =12;

		//Inicio del detalle
		$edit->itid = new hiddenField('','itid_<#i#>');
		$edit->itid->db_name = 'id';
		$edit->itid->rel_id  = 'itlprod';

		$edit->itcodrut = new inputField('ruta','codrut_<#i#>');
		$edit->itcodrut->db_name = 'codrut';
		$edit->itcodrut->rule='max_length[4]';
		$edit->itcodrut->size =7;
		$edit->itcodrut->maxlength =4;
		$edit->itcodrut->rel_id   ='itlprod';

		$edit->itnombre = new inputField('ruta','itnombre_<#i#>');
		$edit->itnombre->db_name = 'nombre';
		$edit->itnombre->type='inputhidden';
		$edit->itnombre->size =14;
		$edit->itnombre->maxlength =12;
		$edit->itnombre->rel_id   ='itlprod';

		$edit->itlitros = new inputField('litros','itlitros_<#i#>');
		$edit->itlitros->db_name = 'litros';
		$edit->itlitros->rule='max_length[12]|numeric|required|mayorcero|callback_chlitros[<#i#>]';
		$edit->itlitros->css_class='inputnum';
		$edit->itlitros->size =14;
		$edit->itlitros->maxlength =12;
		$edit->itlitros->onkeyup='totalizar();';
		$edit->itlitros->rel_id   ='itlprod';
		//Fin del detalle

		$edit->usuario = new autoUpdateField('usuario', $this->secu->usuario(), $this->secu->usuario());

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
			//echo $edit->output;
			$conten['form']  =& $edit;
			$this->load->view('view_lprod', $conten);
		}
	}

	function chlitros($litros,$ind){
		$litros = round($litros,2);
		$ruta   = $this->input->post('codrut_'.$ind);
		$fecha  = human_to_dbdate($this->input->post('fecha'));
		$id     = $this->input->post('itid_'.$ind);
		if(!empty($id)){
			$ww='AND a.id <> '.$this->db->escape($id);
		}else{
			$ww='';
		}

		//$this->validation->set_message('chlitros',$fecha);
		//return false;

		$dbfecha= $this->db->escape($fecha);
		$dbruta = $this->db->escape($ruta);

		$usados = round($this->datasis->dameval("SELECT SUM(a.litros) FROM itlprod AS a JOIN lprod AS b ON a.id_lprod=b.id WHERE a.codrut=${dbruta} AND b.fecha=${dbfecha} ${ww}"),2);
		$recibi = round($this->datasis->dameval("SELECT SUM(litros)   FROM lrece   WHERE ruta=${dbruta} AND fecha=${dbfecha}"),2);

		$disponible = $recibi-$usados-$litros;
		if ($disponible < 0){
			if($recibi-$usados < 0) $disponible = 0; else $disponible = $recibi-$usados ;

			$this->validation->set_message('chlitros',"No hay suficiente leche recibida de la ruta ${ruta} para producir, disponible: ".nformat(abs($disponible)));
			return false;
		}else{
			return true;
		}
	}

	function getcierre($fecha){
		$dbfecha = $this->db->escape($fecha);
		$cierre  = $this->datasis->dameval("SELECT id FROM lcierre WHERE fecha=$dbfecha");
		if(empty($cierre)){
			echo '0';
		}else{
			echo $cierre;
		}
	}

	function getstatus($fecha){
		$dbfecha = $this->db->escape($fecha);
		$status = $this->datasis->dameval("SELECT status FROM lcierre WHERE fecha=$dbfecha");
		if($status=='A'){
			echo 'A';
		}else{
			echo 'C';
		}
	}


	function _pre_insert($do){
		//$do->set('fecha',date('Y-m-d'));
		$leche = $do->get('inventario');

		$fecha  = $do->get('fecha');
		$dbfecha= $this->db->escape($fecha);
		$cana   = $this->datasis->dameval("SELECT COUNT(*) FROM lcierre WHERE fecha=".$dbfecha);

		if($cana>0){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'Ya el d&iacute;a '.dbdate_to_human($fecha).' fue cerrado.';
			$do->error_message_ar['pre_upd'] = $do->error_message_ar['update'] = 'Ya el d&iacute;a '.dbdate_to_human($fecha).' fue cerrado.';
			return false;
		}

		$cana=$do->count_rel('itlprod');
		for($i=0;$i<$cana;$i++){
			$codrut = $do->get_rel('itlprod','codrut' ,$i);
			if(empty($codrut)){
				$do->rel_rm('itlprod',$i);
			}
			$leche += $do->get_rel('itlprod','itlitros' ,$i);
		}

		if($leche <= 0){
			$do->error_message_ar['pre_ins'] = $do->error_message_ar['insert'] = 'No puede tener una produccion sin leche como materia prima.';
			return false;
		}

		return true;
	}

	function _pre_update($do){
		return $this->_pre_insert($do);
	}

	function _pre_delete($do){
		$fecha  = $do->get('fecha');
		$dbfecha= $this->db->escape($fecha);
		$cana   = $this->datasis->dameval("SELECT COUNT(*) FROM lcierre WHERE fecha=".$dbfecha);

		if($cana>0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete'] = 'Ya el d&iacute;a '.dbdate_to_human($fecha).' fue cerrado.';
			return false;
		}
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

	function instalar(){
		if(!$this->db->table_exists('lprod')){
			$mSQL = "
			CREATE TABLE `lprod` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`descrip` VARCHAR(45) NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`peso` DECIMAL(12,2) NULL DEFAULT NULL,
				`litros` DECIMAL(12,2) NULL DEFAULT NULL,
				`inventario` DECIMAL(12,2) NULL DEFAULT NULL,
				`estampa` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
				`usuario` VARCHAR(15) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COMMENT='Control de produccion de lacteos'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->simple_query($mSQL);
		}


		if(!$this->db->table_exists('itlprod')){
			$mSQL = "
			CREATE TABLE `itlprod` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`id_lprod` INT(10) NOT NULL DEFAULT '0',
				`codrut` CHAR(4) NOT NULL DEFAULT '0',
				`nombre` VARCHAR(50) NOT NULL DEFAULT '0',
				`litros` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=0;";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('lcierre')){
			$mSQL = "CREATE TABLE `lcierre` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`fecha` DATE NULL DEFAULT NULL,
				`dia` VARCHAR(50) NULL DEFAULT NULL,
				`status` CHAR(1) NULL DEFAULT 'A',
				`recepcion` DECIMAL(12,2) NULL DEFAULT NULL,
				`enfriamiento` DECIMAL(12,2) NULL DEFAULT NULL,
				`requeson` DECIMAL(12,2) NULL DEFAULT NULL,
				`requesonteorico` DECIMAL(12,2) NULL DEFAULT NULL,
				`requesonreal` DECIMAL(12,2) NULL DEFAULT NULL,
				`usuario` VARCHAR(50) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COMMENT='Cierre de produccion de lacteos'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('itlcierre')){
			$mSQL = "CREATE TABLE `itlcierre` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`id_lcierre` INT(10) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`descrip` VARCHAR(45) NULL DEFAULT NULL,
				`unidades` DECIMAL(10,2) NULL DEFAULT NULL,
				`cestas` DECIMAL(10,2) NULL DEFAULT NULL,
				`peso` DECIMAL(10,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `id_lcierre` (`id_lcierre`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}
	}
}
