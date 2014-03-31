<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
//Grupos de Inventario
require_once(BASEPATH.'application/controllers/inventario/grup.php');
//lineasinventario
class Line extends Controller {
	var $mModulo = 'LINE';
	var $titp    = 'Lineas de Inventario';
	var $tits    = 'Lineas de Inventario';
	var $url     = 'inventario/line/';

	function Line(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'LINE', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->modintramenu( 800, 635, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//$Grupo
		$grid1 = Grup::defgrid();
		$grid1->setUrlput(site_url('/inventario/grup/setdata/'));

		#GET url
		$grid1->setUrlget(site_url('/inventario/grup/getdataE/'));
		$grid1->setTitle("Grupos de Inventario");
		$grid1->setfilterToolbar(false);
		$grid1->setHeight('160');
		$grid1->setOndblClickRow('
			,ondblClickRow: function(id){
				grupedit();
				return;
			}');

		$param['grids'][] = $grid1->deploy();

		$readyLayout = $grid->readyLayout2( 212, 210, $param['grids'][0]['gridname'],$param['grids'][1]['gridname']);

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"reversar", "img"=>"images/arrow_up.png", "alt" => 'Actualizar/Reversar', "label"=>"Actualizar Reversar"));
		$WestPanel = $grid->deploywestp();

		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = 'radicional', $param['grids'][0]['gridname'], $param['grids'][1]['gridname'] );

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Linea de Inventario'),
			array('id'=>'fgrupo',  'title'=>'Editar Grupo'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '
		function fstatus(el, val, opts){
			var meco=\'<div><img src="'.base_url().'images/S.gif" width="20" height="18" border="0" /></div>\';
			if ( el == "B" ){
				meco=\'<div><img src="'.base_url().'images/N.gif" width="20" height="20" border="0" /></div>\';
			}
			return meco;
		}
		';

		$bodyGrid1 = Grup::bodyscript( $param['grids'][1]['gridname'] );
		//Cambiamos grid por grid1
		$bodyGrid1 = str_replace('grid.trigger(','grid1.trigger(',$bodyGrid1);
		//$bodyGrid1 = str_replace('fedita','fgrupo',$bodyGrid1);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['readyLayout']  =$readyLayout;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('LINE', 'JQ');
		$param['funciones']   = $funciones;
		$param['centerpanel']  = $centerpanel;
		$param['otros']       = $this->datasis->otros('LINE', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyGrid1.$bodyscript;
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
		function lineadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function lineedit(){
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
		function lineshow(){
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
		function linedel() {
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
			autoOpen: false, height: 350, width: 400, modal: true,
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
			autoOpen: false, height: 350, width: 400, modal: true,
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

		$grid->addField('linea');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('depto');
		$grid->label('Depto.');
		$grid->params(array(
			'hidden'        => 'true',
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


		$grid->addField('cu_cost');
		$grid->label('Cta. Costo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('cu_inve');
		$grid->label('Cta. Inventario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('cu_venta');
		$grid->label('Cta. Ventas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('cu_devo');
		$grid->label('Cta. Devoluciones');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('desdepto');
		$grid->label('Desc. Dpto');
		$grid->params(array(
//			'hidden'        => 'true',
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
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

		$grid->setGrouping('desdepto');

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('208');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
			if (id){
				var ret = $("#titulos").getRowData(id);
				jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url('inventario/grup/getdataE/').'/"+id+"/", page:1});
				jQuery(gridId2).trigger("reloadGrid");
			}}
		');


		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('LINE','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('LINE','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('LINE','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('LINE','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: lineadd, editfunc: lineedit, delfunc: linedel, viewfunc: lineshow');

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
		$mWHERE = $grid->geneTopWhere('line');

		$response   = $grid->getData('view_line', array(array()), array(), false, $mWHERE, 'depto, linea' );
		//$response   = $grid->getData('line', array(array("table" => "dpto", "join" => "line.depto=dpto.depto", "fields" => array("descrip desdepto"))), array(), false, $mWHERE, 'linea' );

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
		$mcodp  = 'linea';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT COUNT(*) AS cana FROM line WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('line', $data);
					echo "Registro Agregado";

					logusu('LINE',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM line WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM line WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE line SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("line", $data);
				logusu('LINE',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('line', $data);
				logusu('LINE',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM line WHERE id=$id");
			$check =  $this->datasis->dameval("SELECT COUNT(*) AS cana FROM grup WHERE linea='".$meco."' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM line WHERE id=$id ");
				logusu('LINE',"Registro $meco ELIMINADO");
				echo "Linea Eliminada";
			}
		};
	}

	function dataedit($status='',$id=''){
		$this->rapyd->load('dataobject','dataedit');

		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link =site_url('inventario/line/ultimo');
		$link2=site_url('inventario/common/sugerir_line');

		$script='
		function ultimo(){ $.ajax({ url: "'.$link.'", success: function(msg){ alert( "El ultimo codigo ingresado fue: " + msg );}});}

		function sugerir(){
			$.ajax({
			url: "'.$link2.'",
			success: function(msg){
				if(msg){
					$("#linea").val(msg);
				} else {
					alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
				}
			}
			});
		}';

		$modbus=array(
			'tabla'   =>'cpla',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'
			),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'<#i#>'),
			'titulo'  =>'Buscar Cuenta',
			'where'   =>"codigo LIKE \"$qformato\"",
			'p_uri'   =>array(4=>'<#i#>')
		);

		$bcu_venta = $this->datasis->p_modbus($modbus,'cu_venta');
		$bcu_inve  = $this->datasis->p_modbus($modbus,'cu_inve' );
		$bcu_cost  = $this->datasis->p_modbus($modbus,'cu_cost' );
		$bcu_devo  = $this->datasis->p_modbus($modbus,'cu_devo' );

		$mdepto=array(
			'tabla'   =>'dept',
			'columnas'=>array(
				'codigo'  =>'C&oacute;odigo',
				'departam'=>'Nombre'
			),
			'filtro'  =>array('codigo'=>'C&oacute;digo','nombre'=>'Nombre'),
			'retornar'=>array('codigo'=>'depto'),
			'titulo'  =>'Buscar Departamento'
		);

		$boton=$this->datasis->modbus($mdepto);

		$do = new DataObject('line');
		if($status=='create' && !empty($id)){
			$do->load($id);
			$do->set('linea', '');
		}

		$edit = new DataEdit('', $do);
		$edit->on_save_redirect=false;
		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->pre_process( 'delete','_pre_delete' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->dpto = new dropdownField('Departamento', 'depto');
		$edit->dpto->option('','Seleccionar');
		$edit->dpto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");
		$edit->dpto->rule ='required';
		$edit->dpto->style='width:250px;';

		$ultimo ='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo</a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio">Sugerir C&oacute;digo </a>';
		$edit->linea =  new inputField('C&oacute;digo', 'linea');
		$edit->linea->mode='autohide';
		$edit->linea->size =4;
		$edit->linea->rule ='trim|strtoupper|required|callback_chexiste|alpha_numeric';
		$edit->linea->maxlength=2;
		$edit->linea->append($sugerir);
		$edit->linea->append($ultimo);

		$edit->descrip =  new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->size = 35;
		$edit->descrip->rule= 'trim|strtoupper|required';
		$edit->descrip->maxlength=30;

		$edit->cu_inve =new inputField('Cuenta Inventario', 'cu_inve');
		$edit->cu_inve->size = 18;
		$edit->cu_inve->maxlength=15;
		$edit->cu_inve->rule ='trim|existecpla';
		$edit->cu_inve->append($bcu_inve);

		$edit->cu_cost =new inputField('Cuenta Costo', 'cu_cost');
		$edit->cu_cost->size = 18;
		$edit->cu_cost->maxlength=15;
		$edit->cu_cost->rule ='trim|existecpla';
		$edit->cu_cost->append($bcu_cost);

		$edit->cu_venta  =new inputField('Cuenta Venta', 'cu_venta');
		$edit->cu_venta->size =18;
		$edit->cu_venta->maxlength=15;
		$edit->cu_venta->rule ='trim|existecpla';
		$edit->cu_venta->append($bcu_venta);

		$edit->cu_devo = new inputField('Cuenta Devoluci&oacute;n','cu_devo');
		$edit->cu_devo->size = 18;
		$edit->cu_devo->maxlength=15;
		$edit->cu_devo->rule ='trim|existecpla';
		$edit->cu_devo->append($bcu_devo);

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

	function _pre_insert($do){
		return true;
	}

	function _pre_update($do){
		return true;
	}

	function _pre_delete($do) {
		$codigo  =$do->get('linea');
		$dbcodigo=$this->db->escape($codigo);
		$check =  $this->datasis->dameval("SELECT COUNT(*) AS cana FROM grup WHERE linea=${dbcodigo}");
		if($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='La l&iacute;nea contiene grupos, por ello no puede ser eliminada.';
			return false;
		}
		return true;
	}

	function _post_insert($do){
		  $codigo=$do->get('linea');
		  $nombre=$do->get('descrip');
		  logusu('line',"LINEA DE INVENTARIO ${codigo} NOMBRE  ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('linea');
		$nombre=$do->get('descrip');
		logusu('line',"LINEA DE INVENTARIO ${codigo} NOMBRE  ${nombre}  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('linea');
		$nombre=$do->get('descrip');
		logusu('line',"LINEA DE INVENTARIO ${codigo} NOMBRE  ${nombre}  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo  =$this->input->post('linea');
		$dbcodigo=$this->db->escape($codigo);
		$check=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM line WHERE linea=${dbcodigo}");
		if($check > 0){
			$linea=$this->datasis->dameval("SELECT descrip FROM line WHERE linea=${dbcodigo}");
			$this->validation->set_message('chexiste',"El codigo ${codigo} ya existe para la linea ${linea}");
			return false;
		}else{
			return true;
		}
	}

	function ultimo(){
		$ultimo=$this->datasis->dameval('SELECT linea FROM line ORDER BY linea DESC LIMIT 1');
		echo $ultimo;
	}

	function instalar(){
		$campos=$this->db->list_fields('line');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE line DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE line ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE line ADD UNIQUE INDEX linea (linea)');
		}

		if(!$this->db->table_exists('view_line')) {
			$mSQL = 'CREATE ALGORITHM=UNDEFINED DEFINER=`'.$this->db->username.'`@`'.$this->db->hostname.'`
				SQL SECURITY INVOKER VIEW `view_line` AS
				select `a`.`linea` AS `linea`,`a`.`descrip` AS `descrip`,`a`.`cu_cost` AS `cu_cost`,`a`.`cu_inve` AS `cu_inve`,`a`.`cu_venta` AS `cu_venta`,`a`.`cu_devo` AS `cu_devo`,`a`.`depto` AS `depto`,`a`.`id` AS `id`,concat(`b`.`depto`, " ", `b`.`descrip`) AS `desdepto` from (`line` `a` join `dpto` `b` on((`a`.`depto` = `b`.`depto`)))';
			$this->db->simple_query($mSQL);
		}
	}
}
