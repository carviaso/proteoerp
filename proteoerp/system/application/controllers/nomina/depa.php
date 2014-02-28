<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
//departamento
class Depa extends Controller {
	var $mModulo='DEPA';
	var $titp='Departamento de Nomina';
	var $tits='Departamento de Nomina';
	var $url ='nomina/depa/';

	function Depa(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('depa','id') ) {
			$this->db->simple_query('ALTER TABLE depa DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE depa ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE depa ADD UNIQUE INDEX dividepa (division, departa)');
		};
		$this->datasis->modintramenu( 620, 505, substr($this->url,0,-1) );
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
		window.open(\''.site_url('formatos/ver/DEPA/').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
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
		$param['listados']   = $this->datasis->listados('DEPA', 'JQ');
		$param['otros']      = $this->datasis->otros('DEPA', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['anexos']     = 'anexos1';
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

		$mSQL = "SELECT division, CONCAT(division, ' ', descrip) descrip FROM divi ORDER BY division ";
		$adivision = $this->datasis->llenajqselect($mSQL, false );

		$mSQL  = "SELECT depto, CONCAT( depto, ' ', descrip) nombre FROM dpto WHERE tipo='G' ORDER BY depto ";
		$aenlace   = $this->datasis->llenajqselect($mSQL, false );


		$grid  = new $this->jqdatagrid;

		$grid->addField('departa');
		$grid->label('Codigo');
		$grid->params(array(
			'align'    => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
		));

		$grid->addField('depadesc');
		$grid->label('Departamento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));

		$grid->addField('division');
		$grid->label('Division');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$adivision.',  style:"width:250px"}',
			'stype'         => "'text'",


		));

		$grid->addField('descrip');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));

		$grid->addField('enlace');
		$grid->label('Enlace');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$aenlace.',  style:"width:250px"}',
			'stype'         => "'text'"
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'   => 'true',
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 40,
			'editable' => 'false',
			'search'   => 'false'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 400, height:210, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});}');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 400, height:210, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});}');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
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
		$mWHERE = $grid->geneTopWhere('depa');

		$response   = $grid->getData('depa', array(array()), array(), false, $mWHERE );
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
			if(false == empty($data)){
				$departa = $this->input->post('departa');
				$this->db->insert('depa', $data);
				$this->db->simple_query("UPDATE depa a JOIN divi b ON a.division=b.division SET a.descrip=b.descrip");
				echo "Registro Agregado ".$departa;
				logusu('DEPA',"Registro ".$departa." INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$departa  = $this->input->post('departa');
			unset($data['departa']);
			$this->db->where('id', $id);
			$this->db->update('depa', $data);
			$this->db->simple_query("UPDATE depa a JOIN divi b ON a.division=b.division SET a.descrip=b.descrip");
			logusu('DEPA',"Registro ".$departa." MODIFICADO");
			echo "Registro Modificado ".$departa;

		} elseif($oper == 'del') {
			$departa = $this->datasis->dameval("SELECT departa FROM depa WHERE id=$id");
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE depto='$departa'");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM depa WHERE id=$id ");
				logusu('DEPA',"Registro ".$departa." ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}
}

/*
class Depa extends Controller {

	function depa(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		if ( !$this->datasis->iscampo('depa','id') ) {
			$this->db->simple_query('ALTER TABLE depa DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE depa ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE depa ADD UNIQUE INDEX dividepa (division, departa)');
		}
		$this->datasis->modulo_id(706,1);
		//redirect("nomina/depa/extgrid");
		$this->depaextjs();
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Departamentos", 'depa');

		$filter->division = new inputField("Departamento","departa");
		$filter->division->size=8;

		$filter->depadesc = new inputField("Descripcion","depadesc");
		$filter->depadesc->size=30;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/depa/dataedit/show/<#division#>/<#departa#>','<#division#>');

		$grid = new DataGrid("Lista de Departamentos");
		$grid->order_by("division","asc");
		$grid->per_page = 20;

		$grid->column("Divisi&oacute;n",$uri);
		$grid->column("Descripci&oacute;n","descrip");
		$grid->column("Departamento","departa");
		$grid->column("Descripci&oacute;n","depadesc");
		$grid->add("nomina/depa/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Departamentos</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("Departamento", "depa");
		$edit->back_url = site_url("nomina/depa/filteredgrid");

		$edit->pre_process('delete' ,'_pre_del');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

	   $div=array(
	  'tabla'   =>'divi',
	  'columnas'=>array(
		'division' =>'C&oacute;digo de Division',
		'descrip' =>'Descripcion'),
	  'filtro'  =>array('division'=>'C&oacute;digo de Division','descrip'=>'Descripcion'),
	  'retornar'=>array('division'=>'division','descrip'=>'descrip'),
	  'titulo'  =>'Buscar Division');

		$boton=$this->datasis->modbus($div);

		$depto=array(
	  'tabla'   =>'dept',
	  'columnas'=>array(
		'codigo' =>'C&oacute;digo de Enlace',
		'departam' =>'Descripcion'),
	  'filtro'  =>array('division'=>'C&oacute;digo de Enlace','departam'=>'Descripcion'),
	  'retornar'=>array('codigo'=>'enlace'),
	  'titulo'  =>'Buscar Enlace');

		$boton1=$this->datasis->modbus($depto);

		$edit->division =  new inputField("Divisi&oacute;n", "division");
		$edit->division->mode="autohide";
		$edit->division->maxlength=8;
		$edit->division->size=9;
		$edit->division->rule="required|callback_chexiste";
		$edit->division->append($boton);

		$edit->descrip =  new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->maxlength=30;
		$edit->descrip->size =35;
		$edit->descrip->rule="strtoupper|required";

		$edit->departa =  new inputField("Departamento", "departa");
		$edit->departa->rule="required";
		$edit->departa->mode="autohide";
		$edit->departa->maxlength=8;
		$edit->departa->size=9;

		$edit->depadesc =  new inputField("Descripci&oacute;n", "depadesc");
		$edit->depadesc->maxlength=30;
		$edit->depadesc->size =35;
		$edit->depadesc->rule="strtoupper|required";

		$edit->enlace =  new inputField("Enlace","enlace");
		$edit->enlace->maxlength=3;
		$edit->enlace->size=5;
		$edit->enlace->append($boton1);

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		$data['content'] = $edit->output;
    $data['title']   = "<h1>Departamentos</h1>";
    $data["head"]    = $this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);
	}
	function _pre_del($do) {
		$codigo=$do->get('departa');
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE depto='$codigo'");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Cliente con Movimiento no puede ser Borrado';
			return False;
		}
		return True;
	}
	function _post_insert($do){
		$division=$do->get('division');
		$codigo=$do->get('departa');
		$nombre=$do->get('depadesc');
		logusu('depa',"DIVISION $division DEPARTAMENTO $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$division=$do->get('division');
		$codigo=$do->get('departa');
		$nombre=$do->get('depadesc');
		logusu('depa',"DIVISION $division DEPARTAMENTO $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$division=$do->get('division');
		$codigo=$do->get('departa');
		$nombre=$do->get('depadesc');
		logusu('depa',"DIVISION $division DEPARTAMENTO $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	function chexiste($division){
		$departa=$this->input->post('departa');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM depa WHERE division='$division' AND departa='$departa'");
		if ($check > 0){
			$nombre=$this->datasis->dameval("SELECT depadesc FROM depa WHERE division='$division' AND departa='$departa'");
			$this->validation->set_message('chexiste',"La division $division departamento $departa nombre $nombre ya existe");
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
		$this->db->from('depa');

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

		$results = $this->db->count_all('depa');
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
		$departa  = $data['data']['departa'];

		unset($campos['id']);
		$mHay = $this->datasis->dameval("SELECT count(*) FROM depa WHERE departa='".$departa."'");
		if  ( $mHay > 0 ){
			echo "{ success: false, message: 'Ya existe ese departamento $departa'}";
		} else {
			$mSQL = $this->db->insert_string("depa", $campos );
			$this->db->simple_query($mSQL);
			logusu('divi',"DEPARTAMENTO DE NOMINA $division CREADO");
			echo "{ success: true, message: ".$data['data']['departa']."}";
		}
	}

	function modificar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];
		$departa  = $campos['departa'];
		$division = $campos['division'];
		unset($campos['departa']);
		unset($campos['id']);
		$campos['descrip'] = $this->datasis->dameval("SELECT descrip FROM divi WHERE division='$division'");

		$mSQL = $this->db->update_string("depa", $campos,"id='".$data['data']['id']."'" );
		$this->db->simple_query($mSQL);
		logusu('divi',"DEPARTAMENTO DE NOMINA ".$data['data']['departa']." MODIFICADO");
		echo "{ success: true, message: 'Departamento de nomina Modificado '}";
	}

	function eliminar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos= $data['data'];

		$departa = $data['data']['departa'];

		// VERIFICAR SI PUEDE
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE depto='$departa'");

		if ($check > 0){
			echo "{ success: false, message: 'Departamento de nomina, no puede ser Borrado'}";
		} else {
			$this->db->simple_query("DELETE FROM depa WHERE departa='$departa'");
			logusu('depa',"DIVISION DE NOMINA $departa ELIMINADO");
			echo "{ success: true, message: 'Departamento de nomina Eliminado'}";
		}
	}



//****************************************************************
//
//
//
//****************************************************************

	function depaextjs(){
		$encabeza='DEPARTAMENTO DE NOMINA';
		$listados= $this->datasis->listados('depa');
		$otros=$this->datasis->otros('depa', 'depa');

		$mSQL = "SELECT division, CONCAT(division,' ',descrip) descrip FROM divi ORDER BY division ";
		$divi = $this->datasis->llenacombo($mSQL);

		$mSQL = "SELECT depto, CONCAT(depto,' ',descrip) descrip FROM dpto WHERE tipo='G' ORDER BY depto ";
		$dpto = $this->datasis->llenacombo($mSQL);

		$urlajax = 'nomina/depa/';
		$variables = "";

		$funciones = "";

		$valida = "
		{ type: 'length', field: 'division', min:  1 },
		{ type: 'length', field: 'depadesc', min:  1 },
		{ type: 'length', field: 'departa',  min:  1 }
		";


		$columnas = "
		{ header: 'Departamento', width:  60, sortable: true, dataIndex: 'departa',  field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Descripcion',  width: 220, sortable: true, dataIndex: 'depadesc', field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Division',     width:  50, sortable: true, dataIndex: 'division', field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Descripcion.', width: 220, sortable: true, dataIndex: 'descrip',  field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Enlace',       width:  50, sortable: true, dataIndex: 'enlace',   field: { type: 'textfield' }, filter: { type: 'string' }}
	";

		$campos = "'id', 'departa','depadesc','division', 'descrip', 'enlace'";

		$camposforma = "
			{
			frame: false,
			border: false,
			labelAlign: 'right',
			defaults: { xtype:'fieldset', labelWidth:80 },
			style:'padding:4px',
			items:[
				{ xtype: 'textfield', fieldLabel: 'Departamento', name: 'departa',  allowBlank: false,  width: 150 },
				{ xtype: 'textfield', fieldLabel: 'Descripcion',  name: 'depadesc', allowBlank: false,  width: 400 },
				{ xtype: 'combo',     fieldLabel: 'Division',     name: 'division', store: [".$divi."], width: 400 },
				{ xtype: 'combo',     fieldLabel: 'Enlace',       name: 'enlace',   store: [".$dpto."], width: 400 }
			]}
		";


		$titulow = 'Departamentos de Nomina';

		$dockedItems = "
				{ iconCls: 'icon-reset', itemId: 'close', text: 'Cerrar',   scope: this, handler: this.onClose },
				{ iconCls: 'icon-save',  itemId: 'save',  text: 'Guardar',  disabled: false, scope: this, handler: this.onSave }
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

		$data['title']  = heading('Departamentos de Nomina');
		$this->load->view('extjs/extjsven',$data);

	}

}
 */
?>
