<?php
class Prof extends Controller {
	var $mModulo='PROF';
	var $titp='Profesiones';
	var $tits='Profesiones';
	var $url ='nomina/prof/';

	function Prof(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('prof','id') ) {
			$this->db->simple_query('ALTER TABLE prof DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE prof ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE prof ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->modintramenu( 450, 500, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$bodyscript = '
<script type="text/javascript">
$(function() {
	$( "input:submit, a, button", ".otros" ).button();
});

jQuery("#a1").click( function(){
	var id = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
	if (id)	{
		var ret = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getRowData\',id);
		window.open(\''.site_url('formatos/ver/PROF/').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
	} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
});
</script>
';

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$WestPanel = '
<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
<div class="anexos">

<table id="west-grid" align="center">
	<tr>
		<td><div class="tema1"><table id="listados"></table></div></td>
	</tr>
	<tr>
		<td><div class="tema1"><table id="otros"></table></div></td>
	</tr>
</table>

<table id="west-grid" align="center">
	<tr>
		<td></td>
	</tr>
</table>
</div>
'.
//		<td><a style="width:190px" href="#" id="a1">Imprimir Copia</a></td>
'</div> <!-- #LeftPane -->
';

		$SouthPanel = '
<div id="BottomPane" class="ui-layout-south ui-widget ui-widget-content">
<p>'.$this->datasis->traevalor('TITULO1').'</p>
</div> <!-- #BottomPanel -->
';
		//$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados'] = $this->datasis->listados('PROF', 'JQ');
		$param['otros']    = $this->datasis->otros('PROF', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['anexos']    = 'anexos1';
		$param['bodyscript'] = $bodyscript;
		$param['tabs'] = false;
		$param['encabeza'] = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "true";

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'   => 'true',
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 60,
			'editable' => 'false',
			'search'   => 'false'
		));

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 8 }',
		));

		$grid->addField('profesion');
		$grid->label('Profesion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 350,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 40 }',
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:false, mtype: "POST", width: 350, height:150, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,   mtype: "POST", width: 350, height:150, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
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
		$mWHERE = $grid->geneTopWhere('prof');

		$response   = $grid->getData('prof', array(array()), array(), false, $mWHERE, 'codigo' );
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
			$profes = $this->input->post('codigo');
			if(false == empty($data)){
				$this->db->insert('prof', $data);
				logusu('PROF',"Registro $profes CREADO");
				echo "Registro Agregado";
			} else
				echo "Procesos de Agregar Fallo!!!";
			

		} elseif($oper == 'edit') {
			$profes = $this->datasis->dameval("SELECT codigo FROM prof WHERE id=$id");
			$profnu = $this->input->post('codigo');
			$this->db->where('id', $id);
			$this->db->update('prof', $data);
			$this->db->simple_query("UPDATE pers SET profes=? WHERE profes=?",array($profnu, $profes));
			logusu('PROF',"Registro $profes MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			$profes = $this->datasis->dameval("SELECT codigo FROM prof WHERE id=$id");
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE profes='$profes' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene personal de esta profesion";
			} else {
				$this->db->simple_query("DELETE FROM prof WHERE id=$id ");
				logusu('PROF',"Registro $profes ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}
}


/*
	class prof extends Controller{
		function prof(){
			parent::Controller();
			$this->load->library("rapyd");
			$this->datasis->modulo_id(700,1);
		}
		function index(){
			if ( !$this->datasis->iscampo('prof','id') ) {
				$this->db->simple_query('ALTER TABLE prof DROP PRIMARY KEY');
				$this->db->simple_query('ALTER TABLE prof ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
				$this->db->simple_query('ALTER TABLE prof ADD UNIQUE INDEX codigo (codigo)');
			}
			$this->datasis->modulo_id(700,1);
			$this->profextjs();
			//redirect("nomina/prof/filteredgrid");	
		}
		
		function filteredgrid(){
			$this->rapyd->load("datafilter","datagrid");
			
			$filter = new DataFilter("Filtro de Profesiones",'prof');

			$filter->codigo= new inputField("Codigo","codigo");
			$filter->codigo->size=20;
			$filter->codigo->maxlength=8;
			
			$filter->profesion= new inputField("Profesion","profesion");
			$filter->profesion->size=20;
			$filter->profesion->maxlength=40;
			
			$filter->buttons("reset","search");
			$filter->build();
			
			$uri = anchor('nomina/prof/dataedit/show/<#codigo#>','<#codigo#>');
			
			$grid = new DataGrid("Lista de Profesiones");
			$grid->per_page=15;
			
			$grid->column("Codigo",$uri);
			$grid->column("Profesiones","profesion");
			
			$grid->add("nomina/prof/dataedit/create");
			$grid->build();
			
			$data['content'] = $filter->output.$grid->output;
			$data['title']   = "<h1>Profesiones</h1>";
			$data["head"]    = $this->rapyd->get_head();	
			$this->load->view('view_ventanas', $data);	
		}
		
		function dataedit(){
			$this->rapyd->load("dataedit");
			
			$edit = new DataEdit("Profesiones","prof");
			$edit->back_url = site_url("nomina/prof/filteredgrid");
			
			$edit->post_process('insert','_post_insert');
			$edit->post_process('update','_post_update');
			$edit->post_process('delete','_post_delete');
						
			$edit->codigo = new inputField("Codigo", "codigo");
			$edit->codigo->size =10;
			$edit->codigo->mode="autohide";
			$edit->codigo->rule="strtoupper|required|callback_chexiste";
			$edit->codigo->maxlength =8;

			$edit->profesion = new inputField("Profesion", "profesion");
			$edit->profesion->size =40;
			$edit->profesion->rule="strtoupper|required";
			$edit->profesion->maxlength =40;
					  
			$edit->buttons("modify", "save", "undo", "delete", "back");
			$edit->build();
			
			$data['content'] = $edit->output; 		
			$data['title']   = "<h1>Profesiones</h1>";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
			
		function _post_insert($do){
			$codigo=$do->get('codigo');
			$nombre=$do->get('profesion');
			logusu('prof',"PROFESION $codigo NOMBRE $nombre CREADA");
		}
		
		function _post_update($do){
			$codigo=$do->get('codigo');
			$nombre=$do->get('profesion');
			logusu('prof',"PROFESION $codigo NOMBRE $nombre MODIFICADA");
		}
		
		function _post_delete($do){
			$codigo=$do->get('codigo');
			$nombre=$do->get('profesion');
			logusu('prof',"PROFESION $codigo NOMBRE $nombre ELIMINADA");
	  	}
		
		function chexiste($codigo){
			$check=$this->datasis->dameval("SELECT COUNT(*) FROM prof WHERE codigo='$codigo'");
			if ($check > 0){
				$profesion=$this->datasis->dameval("SELECT profesion FROM prof WHERE codigo='$codigo'");
				$this->validation->set_message('chexiste',"El codigo $codigo ya existe para la profesion $profesion");
				return FALSE;
			}else {
				return TRUE;
			}
		}

	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"codigo","direction":"ASC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = $this->datasis->extjsfiltro($filters);
		$this->db->_protect_identifiers=false;
		$this->db->select('*');
		$this->db->from('prof');
		if (strlen($where)>1) $this->db->where($where, NULL, FALSE); 
		$sort = json_decode($sort, true);
		for ($i=0;$i<count($sort);$i++) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);
		$query = $this->db->get();

		$results = $this->db->count_all('prof');

		$arr = $this->datasis->codificautf8($query->result_array());

		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function crear() {
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];
		$codigo = $data['data']['codigo'];
		unset($campos['id']);
		$mHay = $this->datasis->dameval("SELECT count(*) FROM prof WHERE codigo='".$codigo."'");
		if  ( $mHay > 0 ){
			echo "{ success: false, message: 'Ya existe ese proveedor'}";
		} else {
			$mSQL = $this->db->insert_string("prof", $campos );
			$this->db->simple_query($mSQL);
			logusu('prof',"PROFESIONES $codigo CREADO");
			echo "{ success: true, message: ".$data['data']['codigo']."}";
		}
	}

	function modificar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];
		$codigo = $campos['codigo'];
		unset($campos['codigo']);
		unset($campos['id']);

		$mSQL = $this->db->update_string("prof", $campos,"id='".$data['data']['id']."'" );
		$this->db->simple_query($mSQL);
		logusu('prof',"PROFESION ".$data['data']['codigo']." MODIFICADO");
		echo "{ success: true, message: 'Profesion Modificada '}";
	}

	function eliminar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$codigo = $data['data']['codigo'];
		
		// VERIFICAR SI PUEDE
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE profes='$codigo'");

		if ($check > 0){
			echo "{ success: false, message: 'Profesion no puede ser Borrado'}";
		} else {
			$this->db->simple_query("DELETE FROM carg WHERE cargo='$cargo'");
			logusu('carg',"CARGO DE NOMINA $cargo ELIMINADO");
			echo "{ success: true, message: 'Cargo de nomina Eliminado'}";
		}
	}



//****************************************************************
//
//
//
//****************************************************************

	function profextjs(){
		$encabeza='PROFESIONES';
		$listados= $this->datasis->listados('prof');
		$otros=$this->datasis->otros('prof', 'prof');

		$urlajax = 'nomina/prof/';
		$variables = "";
		
		$funciones = "";
		
		$valida = "
		{ type: 'length', field: 'codigo',    min:  1 },
		{ type: 'length', field: 'profesion', min:  1 }
		";
		

		$columnas = "
		{ header: 'Codigo',    width: 100, sortable: true, dataIndex: 'codigo',    field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Profesion', width: 300, sortable: true, dataIndex: 'profesion', field: { type: 'textfield' }, filter: { type: 'string' }}
	";

		$campos = "'id', 'codigo', 'profesion'";
		$camposforma = "
			{
			frame: false,
			tborder: false,
			labelAlign: 'right',
			defaults: { xtype:'fieldset', labelWidth:70 },
			style:'padding:4px',
			items:[	
				{ xtype: 'textfield',   fieldLabel: 'Codigo',    name: 'codigo',    allowBlank: false, width: 200 },
				{ xtype: 'textfield',   fieldLabel: 'Profesion', name: 'profesion', allowBlank: false, width: 400 }
			]}
		";



		$titulow = 'Profesiones';

		$dockedItems = "
				{ iconCls: 'icon-reset', itemId: 'close', text: 'Cerrar',   scope: this, handler: this.onClose },
				{ iconCls: 'icon-save',  itemId: 'save',  text: 'Guardar',  disabled: false, scope: this, handler: this.onSave }
		";

		$winwidget = "
				closable: false,
				closeAction: 'destroy',
				width: 450,
				height: 200,
				resizable: false,
				modal: true,
				items: [writeForm],
				listeners: {
					beforeshow: function() {
						var form = this.down('writerform').getForm();
						this.activeRecord = registro;
						if (registro) {
							form.loadRecord(registro);
						}
					}
				}
";

		$features= "features: [ { ftype: 'filters', encode: 'json', local: false } ],";


		$data['listados']    = $listados;
		$data['otros']       = $otros;
		$data['encabeza']    = $encabeza;
		$data['urlajax']     = $urlajax;
		$data['variables']   = $variables;
		$data['funciones']   = $funciones;
		$data['valida']      = $valida;
		$data['columnas']    = $columnas;
		$data['campos']      = $campos;
		$data['camposforma'] = $camposforma;
		$data['titulow']     = $titulow;
		$data['dockedItems'] = $dockedItems;
		$data['winwidget']   = $winwidget;
		$data['features']    = $features;

		
		$data['title']  = heading('Cargos de Nomina');
		$this->load->view('extjs/extjsven',$data);
		
	}

}
 */
?>