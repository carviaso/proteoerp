<?php
//require_once(BASEPATH.'application/controllers/validaciones.php');

class Scaj extends Controller {
	var $mModulo = 'SCAJ';
	var $titp    = 'Cajeros';
	var $tits    = 'Cajeros';
	var $url     = 'ventas/scaj/';

	function scaj(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SCAJ', $ventana=0 );
	}

	function index(){
		$this->instalar();
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
		//$grid->wbotonadd(array('id'=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
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
		$param['listados']    = $this->datasis->listados('SCAJ', 'JQ');
		$param['otros']       = $this->datasis->otros('SCAJ', 'JQ');
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
		function scajadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function scajedit(){
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
		function scajshow(){
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
		function scajdel() {
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
			autoOpen: false, height: 450, width: 700, modal: true,
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/SCAJ').'/\'+res.id+\'/id\'').';
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
			autoOpen: false, height: 450, width: 700, modal: true,
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

		$grid->addField('cajero');
		$grid->label('Cajero');
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
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('clave');
		$grid->label('Clave');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:6, maxlength: 6 }',
		));


		$grid->addField('fechaa');
		$grid->label('Fecha Apertura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('horaa');
		$grid->label('Hora Apertura');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('apertura');
		$grid->label('Apertura');
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


		$grid->addField('fechac');
		$grid->label('Fecha Cierre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('horac');
		$grid->label('Hora Cierre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));


		$grid->addField('cierre');
		$grid->label('Cierre');
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


		$grid->addField('status');
		$grid->label('Estatus');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('directo');
		$grid->label('Directo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:60, maxlength: 60 }',
		));


		$grid->addField('mesai');
		$grid->label('Mesa Inicial');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('mesaf');
		$grid->label('Mesa Final');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('horai');
		$grid->label('Hora Inicial');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('horaf');
		$grid->label('Hora Final');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('caja');
		$grid->label('Caja');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('almacen');
		$grid->label('Almac&eacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('vendedor');
		$grid->label('Vendedor');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
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

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('SCAJ','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('SCAJ','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('SCAJ','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('SCAJ','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: scajadd, editfunc: scajedit, delfunc: scajdel, viewfunc: scajshow");


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
		$mWHERE = $grid->geneTopWhere('scaj');

		$response   = $grid->getData('scaj', array(array()), array(), false, $mWHERE );
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
		$mcodp  = '??????';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM scaj WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('scaj', $data);
					echo "Registro Agregado";

					logusu('SCAJ',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM scaj WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM scaj WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE scaj SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("scaj", $data);
				logusu('SCAJ',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('scaj', $data);
				logusu('SCAJ',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM scaj WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM scaj WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM scaj WHERE id=$id ");
				logusu('SCAJ',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
			$("#fechaa").datepicker({dateFormat:"dd/mm/yy"});
			$("#fechac").datepicker({dateFormat:"dd/mm/yy"});
		});';

		$edit = new DataEdit('', 'scaj');
		$edit->on_save_redirect=false;
		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->cajero = new inputField('C&oacute;digo', 'cajero');
		$edit->cajero->rule = 'trim|strtoupper|required|callback_chexiste|alpha_numeric';
		$edit->cajero->mode = 'autohide';
		$edit->cajero->maxlength=5;
		$edit->cajero->autocomplete=false;
		$edit->cajero->size = 6;

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->maxlength=30;
		$edit->nombre->autocomplete=false;
		$edit->nombre->rule='trim|strtoupper|required';
		$edit->nombre->size =30;

		$edit->clave = new inputField('Clave', 'clave');
		$edit->clave->maxlength=6;
		$edit->clave->autocomplete=false;
		$edit->clave->rule='trim|required';
		$edit->clave->size = 7;

		$edit->status = new dropdownField('Estatus', 'status');
		$edit->status->rule = 'required|enum[A,C]';
		$edit->status->options(array('C'=> 'Cerrado','A'=>'Abierto'));
		$edit->status->style='width:110px';

		$edit->almacen = new dropdownField('Almac&eacute;n', 'almacen');
		$edit->almacen->option('','Seleccionar');
		$edit->almacen->options("SELECT ubica, ubides FROM caub WHERE gasto='N' ORDER BY ubides");
		$edit->almacen->rule ='required';
		$edit->almacen->style='width:150px';

		$edit->caja = new dropdownField('Caja', 'caja');
		$edit->caja->option('','Seleccionar');
		$edit->caja->options("SELECT codbanc, concat(codbanc,' ',banco) banco FROM banc WHERE tbanco='CAJ' ORDER BY codbanc");
		$edit->caja->rule ='required';
		$edit->caja->style='width:250px';

/*
		$edit->caja->size=4;
		$edit->caja->maxlength=2;
		$edit->caja->rule='trim|callback_ccaja';
*/

		$edit->directo = new inputField('Directorio','directo');
		$edit->directo->size=55;
		$edit->directo->rule='trim';
		$edit->directo->maxlength=60;

		$edit->mesai = new inputField('Mesa desde', 'mesai');
		$edit->mesai->maxlength=4;
		$edit->mesai->size=6;
		$edit->mesai->rule='trim';
		$edit->mesai->group='Mesas';

		$edit->mesaf  = new inputField('Mesa hasta', 'mesaf');
		$edit->mesaf->maxlength=4;
		$edit->mesaf->size=6;
		$edit->mesaf->rule='trim';
		$edit->mesaf->group='Mesas';

		$edit->horai  = new inputField('Desde', 'horai');
		$edit->horai->maxlength=8;
		$edit->horai->size=10;
		$edit->horai->rule='trim|callback_chhora';
		$edit->horai->append('hh:mm:ss');
		$edit->horai->group="Hora feliz";

		$edit->horaf  = new inputField('Hasta', 'horaf');
		$edit->horaf->maxlength=8;
		$edit->horaf->size=10;
		$edit->horaf->rule='trim|callback_chhora';
		$edit->horaf->append('hh:mm:ss');
		$edit->horaf->group='Hora feliz';

		$edit->fechaa = new dateonlyfield('Fecha', 'fechaa');
		$edit->fechaa->maxlength=12;
		$edit->fechaa->size=12;
		$edit->fechaa->rule='chfecha';
		$edit->fechaa->group='Apertura';
		$edit->fechaa->calendar=false;

		$edit->horaa  = new inputField('Hora', 'horaa');
		$edit->horaa->maxlength=12;
		$edit->horaa->size=12;
		$edit->horaa->rule='trim|callback_chhora';
		$edit->horaa->append('hh:mm:ss');
		$edit->horaa->group='Apertura';

		$edit->apertura =new inputField('Monto', 'apertura');
		$edit->apertura->maxlength=12;
		$edit->apertura->size=12;
		$edit->apertura->group='Apertura';
		$edit->apertura->css_class='inputnum';
		$edit->apertura->rule='numeric';

		$edit->fechac = new dateonlyfield('Fecha', 'fechac');
		$edit->fechac->maxlength=12;
		$edit->fechac->size=12;
		$edit->fechac->rule='chfecha';
		$edit->fechac->group='Apertura';
		$edit->fechac->calendar=false;


		$edit->horac  = new inputField('Hora', 'horac');
		$edit->horac->maxlength=8;
		$edit->horac->size=12;
		$edit->horac->rule='trim|callback_chhora';
		$edit->horac->append('hh:mm:ss');
		$edit->horac->group='Apertura';

		$edit->cierre   =new inputField('Monto', 'cierre');
		$edit->cierre->maxlength=12;
		$edit->cierre->size=12;
		$edit->cierre->group='Apertura';
		$edit->cierre->css_class='inputnum';
		$edit->cierre->rule='trim|numeric';

		//$edit->buttons('modify','save','undo','delete','back');
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
			$data['content'] = $this->load->view('view_scaj', $conten );
		}
	}

	function _pre_delete($do) {
		$codigo=$this->db->escape($do->get('cajero'));
		$tables = $this->db->list_tables();
		$sum=0;
		if(in_array('vieite',$tables)) $sum+=$this->datasis->dameval("SELECT COUNT(*) FROM vieite WHERE cajero=${codigo}");
		if(in_array('fmay'  ,$tables)) $sum+=$this->datasis->dameval("SELECT COUNT(*) FROM fmay   WHERE cajero=${codigo}");
		if(in_array('sfac'  ,$tables)) $sum+=$this->datasis->dameval("SELECT COUNT(*) FROM sfac   WHERE cajero=${codigo}");

		if($sum != 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='No se puede borrar un cajero con ventas';
			return false;
		}else
			return true;
	}

	function _post_insert($do){
		$codigo=$do->get('cajero');
		$nombre=$do->get('nombre');
		$status=$do->get('status');
		logusu('scaj',"CAJERO ${codigo} NOMBRE ${nombre} STATUS ${status} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('cajero');
		$nombre=$do->get('nombre');
		$status=$do->get('status');
		logusu('scaj',"CAJERO ${codigo} NOMBRE ${nombre} STATUS ${status} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('cajero');
		$nombre=$do->get('nombre');
		$status=$do->get('status');
		logusu('scaj',"CAJERO ${codigo} NOMBRE ${nombre} STATUS ${status} ELIMINADO");
	}

	//VALIDACIONES
	function chexiste($codigo){
		$codigo  =$this->input->post('cajero');
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM scaj WHERE cajero=$dbcodigo");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT nombre FROM scaj WHERE cajero=$dbcodigo");
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe para el cajero ${nombre}");
			return false;
		}else {
		return true;
		}
	}

	function ccaja($caja){
		$dbcaja=$this->db->escape($caja);
		$cant  =$this->datasis->dameval("SELECT COUNT(*) FROM banc WHERE codbanc=${dbcaja}");
		if($cant==0){
			$this->validation->set_message('ccaja',"El codigo de caja '${caja}' no existe");
			return false;
		}
		return true;
	}

	function instalar(){

		//$mSQL="CREATE TABLE IF NOT EXISTS `vieite` (
		//	`numero` char(8) default NULL,
		//	`fecha` date default '0000-00-00',
		//	`codigo` char(15) default NULL,
		//	`precio` decimal(10,2) default '0.00',
		//	`monto` decimal(18,2) default '0.00',
		//	`cantidad` decimal(12,3) default NULL,
		//	`impuesto` decimal(6,2) default '0.00',
		//	`costo` decimal(18,2) default '0.00',
		//	`almacen` char(4) default NULL,
		//	`cajero` char(5) default NULL,
		//	`caja` char(5) NOT NULL default '',
		//	`referen` char(15) default NULL,
		//	KEY `fecha` (`fecha`),
		//	KEY `codigo` (`codigo`)
		//	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ventas por articulo'";
		//$this->db->simple_query($mSQL);

		//$mSQL="CREATE TABLE IF NOT EXISTS `fmay` (
		//	`fecha` date default NULL,
		//	`numero` varchar(8) NOT NULL default '',
		//	`presup` varchar(8) default NULL,
		//	`almacen` varchar(4) default NULL,
		//	`cod_cli` varchar(5) default NULL,
		//	`nombre` varchar(40) default NULL,
		//	`vence` date default NULL,
		//	`vende` varchar(5) default NULL,
		//	`stotal` decimal(17,2) default '0.00',
		//	`impuesto` decimal(17,2) default '0.00',
		//	`gtotal` decimal(17,2) default '0.00',
		//	`tipo` char(1) default NULL,
		//	`observa1` varchar(40) default NULL,
		//	`observa2` varchar(40) default NULL,
		//	`observa3` varchar(40) default NULL,
		//	`porcenta` decimal(17,2) default '0.00',
		//	`descuento` decimal(17,2) default '0.00',
		//	`cajero` varchar(5) default NULL,
		//	`dire1` varchar(30) default NULL,
		//	`dire2` varchar(30) default NULL,
		//	`rif` varchar(15) default NULL,
		//	`nit` varchar(15) default NULL,
		//	`exento` decimal(17,2) default '0.00',
		//	`transac` varchar(8) default NULL,
		//	`estampa` date default NULL,
		//	`hora` varchar(5) default NULL,
		//	`usuario` varchar(12) default NULL,
		//	`nfiscal` varchar(12) NOT NULL default '0',
		//	`tasa` decimal(19,2) default NULL,
		//	`reducida` decimal(19,2) default NULL,
		//	`sobretasa` decimal(17,2) default NULL,
		//	`montasa` decimal(17,2) default NULL,
		//	`monredu` decimal(17,2) default NULL,
		//	`monadic` decimal(17,2) default NULL,
		//	`cedula` varchar(13) default NULL,
		//	`dirent1` varchar(40) default NULL,
		//	`dirent2` varchar(40) default NULL,
		//	`dirent3` varchar(40) default NULL,
		//	PRIMARY KEY  (`numero`)
		//	) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		//$this->db->simple_query($mSQL);

		$this->db->simple_query('UPDATE scaj SET cajero=TRIM(cajero)');
		$campos=$this->db->list_fields('scaj');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE scaj DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE scaj ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE scaj ADD UNIQUE INDEX cajero (cajero)');
		}

	}
}
