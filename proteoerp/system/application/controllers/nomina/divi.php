<?php
//division
class Divi extends Controller {
	var $mModulo='DIVI';
	var $titp='Divisiones de Nomina';
	var $tits='Divisiones de Nomina';
	var $url ='nomina/divi/';

	function Divi(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('divi','id') ) {
			$this->db->simple_query('ALTER TABLE divi DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE divi ADD UNIQUE INDEX division (division)');
			$this->db->simple_query('ALTER TABLE divi ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->modintramenu( 430, 505, substr($this->url,0,-1) );
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
		window.open(\''.site_url('formatos/ver/DIVI/').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
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
		$param['listados']   = $this->datasis->listados('DIVI', 'JQ');
		$param['otros']      = $this->datasis->otros('DIVI', 'JQ');
		$param['temas']     = array('proteo','darkness','anexos1');
		$param['anexos']     = 'anexos1';
		$param['bodyscript'] = $bodyscript;
		$param['tabs']       = false;
		$param['encabeza']   = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "true";

		$grid  = new $this->jqdatagrid;

		$grid->addField('division');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 8 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 300,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));


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


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 350, height:140, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 350, height:140, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
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
		$mWHERE = $grid->geneTopWhere('divi');

		$response   = $grid->getData('divi', array(array()), array(), false, $mWHERE );
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
			$division = $this->input->post('division');
			if(false == empty($data)){
				$this->db->insert('divi', $data);
				echo "Registro Agregado ".$division;
				logusu('DIVI',"Registro ".$division." INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$division = $this->input->post('division');
			unset($data['division']);
			$this->db->where('id', $id);
			$this->db->update('divi', $data);
			logusu('DIVI',"Registro ".$division." MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			$division = $this->datasis->dameval("SELECT division FROM divi WHERE id=$id");
			$check  =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE divi='$division'");
			$check +=  $this->datasis->dameval("SELECT COUNT(*) FROM depa WHERE division='$division'");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM divi WHERE id=$id ");
				logusu('DIVI',"Registro ".$division." ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}
}

/*
class Divi extends Controller {

	function divi(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}

	function index(){
		$this->datasis->modulo_id(705,1);
		if ( !$this->datasis->iscampo('divi','id') ) {
			$this->db->simple_query('ALTER TABLE divi DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE divi ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE divi ADD UNIQUE INDEX division (division)');
		}
		$this->datasis->modulo_id(701,1);
		redirect("nomina/divi/extgrid");
		//redirect("nomina/divi/filteredgrid");
	}

	function extgrid(){
		$this->datasis->modulo_id(705,1);
		$this->diviextjs();
	}


	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Divisi&oacute;n", 'divi');
		
		$filter->division = new inputField("Divisi&oacute;n", "division");
		$filter->division->size=8;

		$filter->descrip = new inputField("Descripcion","descrip");
		$filter->descrip->size=30;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/divi/dataedit/show/<#division#>','<#division#>');

		$grid = new DataGrid("Lista de Divisiones");
		$grid->order_by("division","asc");
		$grid->per_page = 10;
		$grid->column("Divisi&oacute;n",$uri);
		$grid->column("Descripci&oacute;n","descrip");
		$grid->add("nomina/divi/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Divisiones</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit()
 	{
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("Divisi&oacute;n", "divi");
		$edit->back_url = site_url("nomina/divi/filteredgrid");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
	
		$edit->division =  new inputField("Divisi&oacute;n", "division");
		$edit->division->rule="required|callback_chexiste";
		$edit->division->mode="autohide";
		$edit->division->maxlength=8;
		$edit->division->size=9;
		
		$edit->descrip =  new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->maxlength=30;
		$edit->descrip->size =35;
		$edit->descrip->rule="strtoupper|required";
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;           
    $data['title']   = "<h1>Divisiones</h1>";        
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	function _pre_del($do) {
		$codigo=$do->get('division');
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE divi='$codigo'");
		$check += $this->datasis->dameval("SELECT COUNT(*) FROM depa WHERE divi='$codigo'");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
			return False;
		}
		return True;
	}
	function _post_insert($do){
		$codigo=$do->get('division');
		$nombre=$do->get('descrip');
		logusu('divi',"DIVISION $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('division');
		$nombre=$do->get('descrip');
		logusu('divi',"DIVISION $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('division');
		$nombre=$do->get('descrip');
		logusu('divi',"DIVISION $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($codigo){
		$codigo=$this->input->post('division');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM divi WHERE division='$codigo'");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT descrip FROM divi WHERE division='$codigo'");
			$this->validation->set_message('chexiste',"La division $codigo nombre $nombre ya existe");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}

	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"division","direction":"ASC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = "";

		$this->db->_protect_identifiers=false;
		$this->db->select('*');
		$this->db->from('divi');

		//Buscar posicion 0 Cero
		if (isset($_REQUEST['filter'])){
			$filter = json_decode($_REQUEST['filter'], true);
			if (is_array($filter)) {
				$qs = "";
				for ($i=0;$i<count($filter);$i++){
					switch($filter[$i]['type']){
					case 'string' : $qs .= " ".$filter[$i]['field']." LIKE '%".$filter[$i]['value']."%'"; 
						Break;
					case 'list' :
						if (strstr($filter[$i]['value'],',')){
							$fi = explode(',',$filter[$i]['value']);
							for ($q=0;$q<count($fi);$q++){
								$fi[$q] = "'".$fi[$q]."'";
							}
							$filter[$i]['value'] = implode(',',$fi);
								$qs .= " AND ".$filter[$i]['field']." IN (".$filter[$i]['value'].")";
						}else{
							$qs .= " AND ".$filter[$i]['field']." = '".$filter[$i]['value']."'";
						}
						Break;
					case 'boolean' : $qs .= " AND ".$filter[$i]['field']." = ".($filter[$i]['value']); 
						Break;
					case 'numeric' :
						switch ($filter[$i]['comparison']) {
							case 'ne' : $qs .= " AND ".$filter[$i]['field']." != ".$filter[$i]['value']; 
								Break;
							case 'eq' : $qs .= " AND ".$filter[$i]['field']." = ".$filter[$i]['value']; 
								Break;
							case 'lt' : $qs .= " AND ".$filter[$i]['field']." < ".$filter[$i]['value']; 
								Break;
							case 'gt' : $qs .= " AND ".$filter[$i]['field']." > ".$filter[$i]['value']; 
								Break;
						}
						Break;
					case 'date' :
						switch ($filter[$i]['comparison']) {
							case 'ne' : $qs .= " AND ".$filter[$i]['field']." != '".date('Y-m-d',strtotime($filter[$i]['value']))."'"; 
								Break;
							case 'eq' : $qs .= " AND ".$filter[$i]['field']." = '".date('Y-m-d',strtotime($filter[$i]['value']))."'"; 
								Break;
							case 'lt' : $qs .= " AND ".$filter[$i]['field']." < '".date('Y-m-d',strtotime($filter[$i]['value']))."'"; 
								Break;
							case 'gt' : $qs .= " AND ".$filter[$i]['field']." > '".date('Y-m-d',strtotime($filter[$i]['value']))."'"; 
								Break;
						}
						Break;
					}
				}
				$where .= $qs;
				$this->db->where($where,null, false);
				
			}
		}
		
		$sort = json_decode($sort, true);
		for ($i=0;$i<count($sort);$i++) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);
		$query = $this->db->get();

		$results = $this->db->count_all('divi');
		$arr = array();
		foreach ($query->result_array() as $row)
		{
			$meco = array();
			foreach( $row as $idd=>$campo ) {
				$meco[$idd] = utf8_encode($campo);
			}
			$arr[] = $meco;
		}
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function crear() {
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];
		$division = $data['data']['division'];
		unset($campos['id']);
		$mHay = $this->datasis->dameval("SELECT count(*) FROM divi WHERE division='".$division."'");
		if  ( $mHay > 0 ){
			echo "{ success: false, message: 'Ya existe esa division division=$division'}";
		} else {
			$mSQL = $this->db->insert_string("divi", $campos );
			$this->db->simple_query($mSQL);
			logusu('divi',"DIVISIONES DE NOMINA $division CREADO");
			echo "{ success: true, message: ".$data['data']['division']."}";
		}
	}

	function modificar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];
		$division = $campos['division'];
		unset($campos['division']);
		unset($campos['id']);

		$mSQL = $this->db->update_string("divi", $campos,"id='".$data['data']['id']."'" );
		$this->db->simple_query($mSQL);
		logusu('divi',"DIVISION DE NOMINA ".$data['data']['division']." MODIFICADO");
		echo "{ success: true, message: 'Proveedor ocacional Modificado '}";
	}

	function eliminar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos= $data['data'];

		$division = $data['data']['division'];
		
		// VERIFICAR SI PUEDE
		$check  =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE divi='$division'");
		$check +=  $this->datasis->dameval("SELECT COUNT(*) FROM depa WHERE division='$division'");

		if ($check > 0){
			echo "{ success: false, message: 'Division asignado a personal, no puede ser Borrado'}";
		} else {
			$this->db->simple_query("DELETE FROM divi WHERE division='$division'");
			logusu('carg',"DIVISION DE NOMINA $division ELIMINADO");
			echo "{ success: true, message: 'Division de nomina Eliminado'}";
		}
	}



//****************************************************************
//
//
//
//****************************************************************

	function diviextjs(){
		$encabeza='DIVISIONES DE NOMINA';
		$listados= $this->datasis->listados('divi');
		$otros=$this->datasis->otros('divi', 'divi');

		$urlajax = 'nomina/divi/';
		$variables = "";
		
		$funciones = "";
		
		$valida = "
		{ type: 'length', field: 'division', min:  1 },
		{ type: 'length', field: 'descrip',  min:  1 }
		";
		

		$columnas = "
		{ header: 'Division',     width:  50, sortable: true, dataIndex: 'division', field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Descripcion.', width: 300, sortable: true, dataIndex: 'descrip',  field: { type: 'textfield' }, filter: { type: 'string' }}
	";

		$campos = "'id', 'division', 'descrip'";
		
		$camposforma = "
			\t\t\t\t{
			\t\t\t\tframe: false,
			\t\t\t\tborder: false,
			\t\t\t\tlabelAlign: 'right',
			\t\t\t\tdefaults: { xtype:'fieldset', labelWidth:70 },
			\t\t\t\tstyle:'padding:4px',
			\t\t\t\titems:[	
			\t\t\t\t	{ xtype: 'textfield',   fieldLabel: 'Division',    name: 'division', allowBlank: false, width: 200 },
			\t\t\t\t	{ xtype: 'textfield',   fieldLabel: 'Descripcion', name: 'descrip',  allowBlank: false, width: 400 },
			\t\t\t\t]}
		";


		$titulow = 'Divisiones de Nomina';

		$dockedItems = "
				\t\t\t\t{ iconCls: 'icon-reset', itemId: 'close', text: 'Cerrar',   scope: this, handler: this.onClose },
				\t\t\t\t{ iconCls: 'icon-save',  itemId: 'save',  text: 'Guardar',  disabled: false, scope: this, handler: this.onSave }
		";

		$winwidget = "
				closable: false,
				closeAction: 'destroy',
				width: 450,
				height: 250,
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
		
		$data['title']  = heading('Division de Nomina');
		$this->load->view('extjs/extjsven',$data);
		
	}

}
*/
?>