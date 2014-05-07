<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Grup extends Controller {
	var $mModulo = 'GRUP';
	var $titp    = 'Grupos de Inventario';
	var $tits    = 'Grupos de Inventario';
	var $url     = 'inventario/grup/';

	function Grup(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'GRUP', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 700, 500, substr($this->url,0,-1) );
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
			array('id'=>'fedita', 'title'=>'Agregar/Editar Registro'),
			array('id'=>'fborra', 'title'=>'Eliminar registro')
		);

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function fstatus(el, val, opts){
			var meco=\'<div><img src="'.base_url().'images/S.gif" width="20" height="18" border="0" /></div>\';
			if ( el == "B" ){
				meco=\'<div><img src="'.base_url().'images/N.gif" width="20" height="20" border="0" /></div>\';
			}
			return meco;
		}';

		$param['WestPanel']   = $WestPanel;
		$param['funciones']   = $funciones;

		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('GRUP', 'JQ');
		$param['otros']       = $this->datasis->otros('GRUP', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function grupadd() {
			$.post("'.site_url('inventario/grup/dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function grupedit() {
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url('inventario/grup/dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		//Borrar
		$bodyscript .= '
		function grupdel() {
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
			autoOpen: false, height: 450, width: 500, modal: true,
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

		$grid->addField('status');
		$grid->label('Estatus');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 45,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
			'formatter'     => 'fstatus'
		));


		$grid->addField('depto');
		$grid->label('Depto.');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));

		$grid->addField('linea');
		$grid->label('L&iacute;nea');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));

		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));


		$grid->addField('nom_grup');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));

/*
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
*/

		$grid->addField('comision');
		$grid->label('Comisi&oacute;n');
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



		$grid->addField('cu_inve');
		$grid->label('Cta.Inventario');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_cost');
		$grid->label('Cta.Costo');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_venta');
		$grid->label('Cta.Venta');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_devo');
		$grid->label('Cta.Devoluci&oacute;n');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('margen');
		$grid->label('Margen Venta');
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


		$grid->addField('margenc');
		$grid->label('Margen Compra');
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

		$grid->addField('precio');
		$grid->label('Precio M&iacute;nimo');
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
		$grid->setAdd(    $this->datasis->sidapuede('GRUP','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('GRUP','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('GRUP','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('GRUP','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: grupadd,editfunc: grupedit, delfunc: grupdel');

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
		$mWHERE = $grid->geneTopWhere('grup');

		$response   = $grid->getData('grup', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Busca la data en el Servidor por json Cuando se llama desde otra clase
	*/
	function getdataE(){
		$id = $this->uri->segment(4);
		if ($id == false ){
			$id = $this->datasis->dameval("SELECT id FROM line ORDER BY depto, linea LIMIT 1");
		}
		$depto = $this->datasis->dameval("SELECT depto FROM line WHERE id=$id");
		$linea = $this->datasis->dameval("SELECT linea FROM line WHERE id=$id");
		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM grup WHERE depto='$depto' AND linea='$linea'";
		$response   = $grid->getDataSimple($mSQL);
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
		$mcodp  = 'grupo';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM grup WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('grup', $data);
					echo "Registro Agregado";

					logusu('GRUP',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM grup WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM grup WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE grup SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("grup", $data);
				logusu('GRUP',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('grup', $data);
				logusu('GRUP',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$codigo = $this->datasis->dameval("SELECT grupo FROM grup WHERE id=$id");
			$check  =  $this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE grupo='$codigo'");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM grup WHERE id=$id ");
				logusu('GRUP',"Grupo $codigo ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit($status='',$id=''){
		$this->rapyd->load('dataobject','dataedit');

		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link=site_url('inventario/grup/ultimo');
		$link2=site_url('inventario/common/sugerir_grup');

		$script ='
			$(function() {
				$(".inputnum").numeric(".");
			});

			function ultimo(){
				$.ajax({
					url: "'.$link.'",
					success: function(msg){
					  alert( "El ultimo codigo ingresado fue: " + msg );
					}
				});
			}

			function sugerir(){
				$.ajax({
						url: "'.$link2.'",
						success: function(msg){
							if(msg){
								$("#grupo").val(msg);
							}
							else{
								alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
							}
						}
					});
			}

			function get_grupo(){
				return true;
			}

			function get_linea(){
				$.ajax({
					type: "POST",
					url: "'.site_url('reportes/sinvlineas').'",
					data: $("#dpto").serialize(),
					success: function(msg){
						$("#td_linea").html(msg);
					},
					error: function(msg){
						alert("Error en la comunicaci&oacute;n");
					}
				});
			}';

		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'where'=>"codigo LIKE \"$qformato\"",
			'p_uri'=>array(4=>'<#i#>')
		);

		$bcu_venta = $this->datasis->p_modbus($modbus,'cu_venta');
		$bcu_inve  = $this->datasis->p_modbus($modbus,'cu_inve' );
		$bcu_cost  = $this->datasis->p_modbus($modbus,'cu_cost' );
		$bcu_devo  = $this->datasis->p_modbus($modbus,'cu_devo' );

		$do = new DataObject('grup');
		$do->set('tipo', 'I');
		if($status=='create' && !empty($id)){
			$do->load($id);
			$do->set('grupo', '');
		}

		$edit = new DataEdit('',$do);
		$edit->on_save_redirect=false;
		$edit->script($script, "modify");
		$edit->script($script, "create");

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->depto = new dropdownField('Departamento', 'dpto');
		$edit->depto->db_name='depto';
		$edit->depto->rule ='required';
		$edit->depto->onchange = 'get_linea();';
		$edit->depto->option('','Seleccionar');
		$edit->depto->options("SELECT depto, CONCAT( depto, '-',descrip) descrip FROM dpto WHERE tipo='I' ORDER BY depto");

		$edit->linea = new dropdownField("L&iacute;nea","linea");
		$edit->linea->rule ='required';
		if($edit->_status=='modify' or $edit->_status=='show' or $edit->_status=='idle' or $edit->_status=='create'){
			$depto = ($this->input->post('dpto')===false) ? $edit->_dataobject->get("depto") : $this->input->post('dpto');
			$edit->linea->options("SELECT linea, CONCAT( linea, '-', descrip) descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$edit->linea->option('','Seleccione un Departamento');
		}

		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->grupo =  new inputField('C&oacute;digo', 'grupo');
		$edit->grupo->mode='autohide';
		$edit->grupo->maxlength=4;
		$edit->grupo->size =6;
		$edit->grupo->rule ='trim|strtoupper|required|callback_chexiste|alpha_numeric';
		$edit->grupo->append($sugerir);
		$edit->grupo->append($ultimo);

		$edit->nom_grup =  new inputField('Nombre del Grupo', 'nom_grup');
		$edit->nom_grup->size = 40;
		$edit->nom_grup->maxlength=40;
		$edit->nom_grup->rule = 'trim|strtoupper|required';

		$edit->comision = new inputField('Comisi&oacute;n. %', 'comision');
		$edit->comision->size = 10;
		$edit->comision->maxlength=10;
		$edit->comision->css_class='inputnum';
		$edit->comision->rule='trim|callback_chporcent|numeric|callback_positivo';

		$edit->status = new dropdownField('Estatus', 'status');
		$edit->status->option('A','Activo');
		$edit->status->option('B','Bloqueado');
		$edit->status ->style='width:120px;';

		$edit->margen = new inputField('Margen de Venta', 'margen');
		$edit->margen->size = 10;
		$edit->margen->maxlength=10;
		$edit->margen->css_class='inputnum';
		$edit->margen->group='Margenes';
		$edit->margen->insertValue='0';
		$edit->margen->rule='trim|callback_chporcent|callback_positivo';

		$edit->margenc = new inputField('Margen de Compra', 'margenc');
		$edit->margenc->size = 10;
		$edit->margenc->maxlength=10;
		$edit->margenc->insertValue='0';
		$edit->margenc->css_class='inputnum';
		$edit->margenc->group='Margenes';
		$edit->margenc->rule='trim|callback_chporcent|numeric|callback_positivo';

		$edit->precio = new dropdownField('Precio M&iacute;nimo', 'precio');
		$edit->precio->option('0','No Aplica');
		//$edit->precio->option('1','Precio 1');
		$edit->precio->option('2','Precio 2');
		$edit->precio->option('3','Precio 3');
		$edit->precio->option('4','Precio 4');
		$edit->precio->style='width:120px;';

		$edit->cu_inve =new inputField('Cuenta Inventario', 'cu_inve');
		$edit->cu_inve->size = 18;
		$edit->cu_inve->maxlength=15;
		$edit->cu_inve->rule ='trim|existecpla';
		$edit->cu_inve->append($bcu_inve);
		$edit->cu_inve->group='Cuentas contables';

		$edit->cu_cost =new inputField('Cuenta Costo', 'cu_cost');
		$edit->cu_cost->size = 18;
		$edit->cu_cost->maxlength=15;
		$edit->cu_cost->rule ='trim|existecpla';
		$edit->cu_cost->append($bcu_cost);
		$edit->cu_cost->group='Cuentas contables';

		$edit->cu_venta  =new inputField('Cuenta Venta', 'cu_venta');
		$edit->cu_venta->size =18;
		$edit->cu_venta->maxlength=15;
		$edit->cu_venta->rule ='trim|existecpla';
		$edit->cu_venta->append($bcu_venta);
		$edit->cu_venta->group='Cuentas contables';

		$edit->cu_devo = new inputField('Cuenta Devoluci&oacute;n','cu_devo');
		$edit->cu_devo->size = 18;
		$edit->cu_devo->maxlength=15;
		$edit->cu_devo->rule ='trim|existecpla';
		$edit->cu_devo->append($bcu_devo);
		$edit->cu_devo->group='Cuentas contables';

		//$edit->buttons("modify", "save", "undo", "delete", "back");
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

	function _post_insert($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grup',"GRUPO DE INVENTARIO ${codigo} NOMBRE  ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo = $do->get('grupo');
		$nombre = $do->get('nom_grup');
		$linea  = $this->db->escape($do->get('linea'));
		$depto  = $this->db->escape($do->get('depto'));
		// Cambia todos los productos de inv
		$this->db->simple_query('UPDATE sinv SET linea='.$linea.', depto='.$depto.' WHERE grupo='.$this->db->escape($codigo));
		logusu('grup',"GRUPO DE INVENTARIO ${codigo} NOMBRE  ${nombre}  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('grupo');
		$nombre=$do->get('nom_grup');
		logusu('grup',"GRUPO DE INVENTARIO ${codigo} NOMBRE  ${nombre}  ELIMINADO ");
	}

	function _pre_delete($do) {
		$codigo  = $do->get('grupo');
		$dbcodigo= $this->db->escape($codigo);
		$check  =  $this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE grupo=${dbcodigo}");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='El grupo contiene productos, por ello no puede ser eliminado.';
			return false;
		}
		return true;
	}

	function chexiste($codigo){
		$codigo  = $this->input->post('grupo');
		$dbcodigo= $this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM grup WHERE grupo=${dbcodigo}");
		if ($check > 0){
			$grupo=$this->datasis->dameval("SELECT nom_grup FROM grup WHERE grupo=${dbcodigo}");
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe.");
			return false;
		}else {
			return true;
		}
	}

	function ultimo(){
		$ultimo=$this->datasis->dameval('SELECT grupo FROM grup ORDER BY grupo DESC LIMIT 1');
		echo $ultimo;
	}

	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo','El campo comisi&oacute;n debe ser positivo');
			return false;
		}
		return true;
	}

	function instalar(){
		$campos=$this->db->list_fields('grup');

		if(!in_array('id'  ,$campos)){
			$this->db->simple_query('ALTER TABLE grup DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grup ADD UNIQUE INDEX grupo (grupo)');
			$this->db->simple_query('ALTER TABLE grup ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('precio'  ,$campos)){
			$this->db->simple_query("ALTER TABLE grup ADD COLUMN precio CHAR(1) NULL DEFAULT '0'");
		}

		if(!in_array('margen',$campos)){
			$mSQL="ALTER TABLE `grup`
			ADD COLUMN `margen` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `comision`,
			ADD COLUMN `margenc` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `margen`";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('status',$campos)){
			$mSQL="ALTER TABLE `grup` ADD COLUMN `status` CHAR(1) NULL DEFAULT NULL";
			$this->db->simple_query($mSQL);
		}
	}
}
