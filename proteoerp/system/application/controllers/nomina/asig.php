<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
//asignacion
class Asig extends Controller {
	var $mModulo='ASIG';
	var $titp='Asignaciones Particulares';
	var $tits='Asignaciones Particulares';
	var $url ='nomina/asig/';

	function Asig(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('asig','id') ) {
			$this->db->simple_query('ALTER TABLE asig DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE asig ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE asig ADD UNIQUE INDEX codigo (codigo, concepto, fecha)');
		}
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
		window.open(\''.base_url().'formatos/ver/ASIG/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
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
		$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados'] = $this->datasis->listados('ASIG', 'JQ');
		$param['otros']    = $this->datasis->otros('ASIG', 'JQ');
		$param['temas']     = array('proteo','darkness','anexos1');
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
		$link = site_url('ajax/buscapers');
		$despues =
'				$("input#nombre").val(ui.item.nombre);';
		

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true }',
			'editoptions'   => '{'.$grid->autocomplete($link, 'codigo','aaaaaa','<div id=\"aaaaaa\"></div>',$despues).'}',
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 30, readonly:true }',
		));


		$link1    = site_url('ajax/buscaconc');
		$despues1 =
'				$("input#tipo").val(ui.item.tipo);
				$("input#descrip").val(ui.item.descrip);';
				//$("textarea#formula").val(ui.item.formula);

		$grid->addField('concepto');
		$grid->label('Concepto');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{'.$grid->autocomplete($link1, 'concepto','aaaaaa','<div id=\"aaaaaa\"></div>',$despues1).'}',
		));

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 1, readonly:true }',
		));


		$grid->addField('descrip');
		$grid->label('Descrip');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 35, readonly:true }',
		));

		$grid->addField('formula');
		$grid->label('Formula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ rows:3, cols: 50 }',
		));

/*
		$grid->addField('monto');
		$grid->label('Monto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'edittype'      => "'text'",
			'width'         => 90,
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));
*/
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
/*
		$grid->addField('cuota');
		$grid->label('Cuota');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			//'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));

		$grid->addField('cuotat');
		$grid->label('Nro. Cuotas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			//'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));
*/
		$grid->addField('valor');
		$grid->label('Monto/Cuota');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 90,
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
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

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:340, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:340, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
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
		$mWHERE = $grid->geneTopWhere('asig');

		$response   = $grid->getData('asig', array(array()), array(), false, $mWHERE );
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

		//Valida 
		if ($oper <> 'del'){
			$codigo   = $this->input->post('codigo');
			$concepto = $this->input->post('concepto');
			$check = $this->datasis->dameval('SELECT count(*) FROM pers WHERE codigo='.$this->db->escape($codigo));
			if ( $check == 0 ){
				echo "No se encontro esa persona en los registros ".$codigo;
				return;
			}
			$check = $this->datasis->dameval('SELECT count(*) FROM conc WHERE concepto='.$this->db->escape($concepto));
			if ( $check == 0 ){
				echo "no se encontro ese concepto en los registros ";
				return;
			}
		}

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('asig', $data);
				echo "Registro Agregado";
				logusu('ASIG',"Registro ".$codigo." ".$concepto." INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('asig', $data);
			logusu('ASIG',"Registro ".$codigo." ".$concepto." MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			$codigo   = $this->datasis->dameval("SELECT codigo   FROM asig WHERE id=$id");
			$concepto = $this->datasis->dameval("SELECT concepto FROM asig WHERE id=$id");
			$this->db->simple_query("DELETE FROM asig WHERE id=$id ");
			logusu('ASIG',"Registro ".$codigo." ".$concepto." ELIMINADO");
			echo "Registro Eliminado";
		};
	}

/*
class Asig extends Controller {

function asig(){
		parent::Controller(); 
		$this->load->library("rapyd");
	}

	function index(){
		if ( !$this->datasis->iscampo('asig','id') ) {
			$this->db->simple_query('ALTER TABLE asig DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE asig ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE asig ADD UNIQUE INDEX codigo (codigo, concepto, fecha)');
		}
		$this->datasis->modulo_id(702,1);
		$this->asigextjs();
	}

	function filteredgrid(){
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter2("Filtro de Asignaciones", 'asig');
		
		$filter->fecha = new DateField("Fecha", "fecha");
		$filter->fecha->size = 12;
		
		$filter->codigo = new inputField("Codigo de Trabajador","codigo");
		$filter->codigo->size=10;
		
		$filter->descrip = new inputField("Descripci&oacute;n de Concepto","descrip");
		$filter->descrip->size=25;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('nomina/asig/dataedit/show/<#codigo#>','<#codigo#>');
		
		function sta($status){
			switch($status){
				case "A":return "Asignaci&oacute;n";break;
				case "O":return "Otros";break;
				case "D":return "Devoluciones";break;
			}
		}
		
		$grid = new DataGrid("Lista de Asignaciones");
		$grid->order_by("codigo","asc");
		$grid->per_page = 10;
		$grid->use_function('sta');

		$grid->column("C&oacute;digo",$uri);
		$grid->column("Nombre","nombre");
		$grid->column("Concepto","concepto");
		$grid->column("Descripci&oacute;n","descrip");
		$grid->column("Monto"      ,"<nformat><#monto#></nformat>","align='right'");
		$grid->column("Fecha"      ,"<dbdate_to_human><#fecha#></dbdate_to_human>"         ,"align='center'");
				
		$grid->add("nomina/asig/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Asignaciones</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	

	}

	function dataedit()
 	{
		$this->rapyd->load("dataedit");
		$edit = new DataEdit("Asignaciones", "asig");
		
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
		
		$pers=array(                         
			'tabla'   =>'pers',                         
			'columnas'=>array(                         
			'codigo'  =>'Codigo',                         
			'cedula'  =>'Cedula',                         
			'nombre'  =>'Nombre',                         
			'apellido' =>'Apellido'),                         
			'filtro'  =>array('codigo'=>'C&oacute;digo','cedula'=>'Cedula'),
			'retornar'=>array('codigo'=>'codigo','nombre'=>'nombre'),     
			'titulo'  =>'Buscar Personal');                         
					                           
		$boton=$this->datasis->modbus($pers); 
		
		$edit->back_url = site_url("nomina/asig/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->codigo =  new inputField("C&oacute;digo","codigo");
		$edit->codigo->mode="autohide";
		$edit->codigo->maxlength=15;
		$edit->codigo->size =20;
		$edit->codigo->rule ="trim|required";
		$edit->codigo->append($boton);
		$edit->codigo->mode="autohide";
		
		$edit->nombre =  new inputField("Nombre", "nombre");
		$edit->nombre->rule ="trim|strtoupper|required";
		$edit->nombre->maxlength=30;
		$edit->nombre->size =40;
				
		$edit->concepto = new dropdownField("Concepto", "concepto");
	  $edit->concepto->options("SELECT concepto, descrip FROM conc ORDER BY CONCEPTO");
		$edit->concepto->style ="width:300px;";
		
		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style ="width:100px;";
		$edit->tipo->options(array("A"=> "Asignaci&oacute;n","O"=>"Otros","D"=> "Deducci&oacute;n"));
		
		$edit->descrip =   new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size =45;
		$edit->descrip->maxlength=35;
		$edit->descrip->rule ="strtoupper";
		
		$edit->formula =   new inputField("F&oacute;rmula", "formula");
		$edit->formula->size =80;
		$edit->formula->maxlength=150;
		
		$edit->monto = new inputField("Monto", "monto");
		$edit->monto->size = 13;
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='callback_positivo';
		
		$edit->fecha = new DateField("Fecha", "fecha");
		$edit->fecha->size = 12;
		
		$edit->cuota =  new inputField("Cuotas", "cuota");
		$edit->cuota->size = 13;
		$edit->cuota->maxlength=11;
		$edit->cuota->css_class='inputnum';
		$edit->cuota->rule='integer';
				
		$edit->cuotat = new inputField("Total de cuotas", "cuotat");
		$edit->cuotat->size =13;
		$edit->cuotat->maxlength=11;
		$edit->cuotat->css_class='inputnum';
		$edit->cuotat->rule='integer';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
 
		$data['content'] = $edit->output;           
    $data['title']   = "<h1>Asignaci&oacute;n</h1>";        
   	$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('asig',"ASIGNACION $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('asig',"ASIGNACION $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('nombre');
		logusu('asig',"ASIGNACION $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	//function chexiste($codigo){
	//	$codigo=$this->input->post('codigo');
	//	$check=$this->datasis->dameval("SELECT COUNT(*) FROM asig WHERE codigo='$codigo'");
	//	if ($check > 0){
	//		$nombre=$this->datasis->dameval("SELECT descrip FROM asig WHERE codigo='$codigo'");
	//		$this->validation->set_message('chexiste',"El codigo $codigo ya existe para la asignacion $nombre");
	//		return FALSE;
	//	}else {
//		return TRUE;
	//	}	
	//}
		
	function positivo($valor){
		if ($valor <= 0){
			$this->validation->set_message('positivo',"El campo Monto debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
*/

	function instalar(){
		$mSQL="ALTER TABLE asig ADD PRIMARY KEY (codigo);";
		$this->db->simple_query($mSQL);	
	}


	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"codigo","direction":"ASC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = "";

		$this->db->_protect_identifiers=false;
		$this->db->select('*');
		$this->db->from('asig');

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

		$results = $this->db->count_all('asig');
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
		$codigo   = $data['data']['codigo'];
		$concepto = $data['data']['concepto'];
		$fecha    = $data['data']['fecha'];
		
		$campos['nombre']  = $this->datasis->dameval("SELECT CONCAT(TRIM(nombre),' ',TRIM(apellido)) nombre FROM pers WHERE codigo='$codigo'");
		$campos['descrip'] = $this->datasis->dameval("SELECT descrip FROM conc WHERE concepto='$concepto'");
		
		unset($campos['id']);
		$mHay = $this->datasis->dameval("SELECT count(*) FROM asig WHERE codigo='$codigo' AND concepto='$concepto' AND fecha='$fecha'");
		if  ( $mHay > 0 ){
			echo "{ success: false, message: 'Ya existe una asignacion para ese trabajador $codigo'}";
		} else {
			$mSQL = $this->db->insert_string("asig", $campos );
			$this->db->simple_query($mSQL);
			logusu('divi',"ASIGNACION DE NOMINA $codigo CREADO");
			echo "{ success: true, message: ".$data['data']['codigo']."}";
		}
	}

	function modificar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];
		$codigo   = $campos['codigo'];
		$concepto = $campos['concepto'];

		$campos['nombre']  = $this->datasis->dameval("SELECT CONCAT(TRIM(nombre),' ',TRIM(apellido)) nombre FROM pers WHERE codigo='$codigo'");
		$campos['descrip'] = $this->datasis->dameval("SELECT descrip FROM conc WHERE concepto='$concepto'");

		unset($campos['codigo']);
		unset($campos['concepto']);
		unset($campos['id']);

		$mSQL = $this->db->update_string("asig", $campos,"id='".$data['data']['id']."'" );
		$this->db->simple_query($mSQL);
		logusu('asig',"ASIGNACIONES DE NOMINA ".$data['data']['id']." MODIFICADO");
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
	function asigextjs(){
		$encabeza='ASIGNACIONES DE NOMINA';
		$listados= $this->datasis->listados('asig');
		$otros=$this->datasis->otros('depa', 'asig');

		$mSQL = "SELECT concepto, CONCAT(concepto,' ',descrip) descrip FROM conc ORDER BY concepto ";
		$conc = $this->datasis->llenacombo($mSQL);

		$urlajax = 'nomina/asig/';
		$variables = "var mcliente = '', mcodigo='';";

		$funciones = "";
		
		$valida = "
		{ type: 'length', field: 'codigo',   min:  1 },
		{ type: 'length', field: 'concepto', min:  1 }
		";

		$columnas = "
		{ header: 'Codigo',      width:  60, sortable: true, dataIndex: 'codigo',   field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Nombre',      width: 220, sortable: true, dataIndex: 'nombre',   field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Concepto',    width:  70, sortable: true, dataIndex: 'concepto', field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Tipo',        width:  50, sortable: true, dataIndex: 'tipo',     field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Descripcion', width: 220, sortable: true, dataIndex: 'descrip',  field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Formula',     width: 220, sortable: true, dataIndex: 'formula',  field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Monto',       width: 120, sortable: true, dataIndex: 'monto',    field: { type: 'numeroc'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'Cuotas',      width: 120, sortable: true, dataIndex: 'cuotat',   field: { type: 'numeroc'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'valor',       width: 120, sortable: true, dataIndex: 'valor',    field: { type: 'numeroc'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'Fecha',       width:  70, sortable: true, dataIndex: 'fecha',    field: { type: 'date'      }, filter: { type: 'date'   }},
		{ header: 'Pagadas ',    width: 120, sortable: true, dataIndex: 'cuota',    field: { type: 'numeroc'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') }
	";

		$campos = "'id', 'codigo', 'nombre', 'concepto','tipo', 'descrip', 'formula','monto','cuotat','valor','fecha','cuota'";
		$filtros = "\tvar filters = {	ftype: 'filters',encode: 'json', local: false }; ";
		
		$camposforma = "
				{
						frame: false,
						border: false,
						labelAlign: 'right',
						defaults: { xtype:'fieldset', labelWidth:70 },
						style:'padding:4px',
						items:[	
								{ xtype: 'combo',         fieldLabel: 'Trabajador', name: 'codigo',   mode: 'remote', hideTrigger: true, typeAhead: true, forceSelection: true, valueField: 'item', displayField: 'valor', store: persStore, width: 400, id: 'codigo'},
								{ xtype: 'combo',         fieldLabel: 'Concepto',   name: 'concepto', store: [".$conc."], width: 400 },
								{ xtype: 'textareafield', fieldLabel: 'Formula',    name: 'formula',  allowBlank: false,   width: 400 }
						]
				},{
						layout: 'column',
						frame: false,
						border: false,
						labelAlign: 'right',
						defaults: {xtype:'fieldset'  },
						style:'padding:4px',
						items: [
								{ xtype: 'datefield',   fieldLabel: 'Fecha Inicio',   name: 'fecha',  labelWidth: 90, format: 'd/m/Y', submitFormat: 'Y-m-d', value: new Date(),columnWidth:0.45 },
								{ xtype: 'numberfield', fieldLabel: 'Total Cuotas',   name: 'cuotat', labelWidth:110, hideTrigger: true, fieldStyle: 'text-align: right', columnWidth:0.45, renderer : Ext.util.Format.numberRenderer('0,000.00') },
								{ xtype: 'numberfield', fieldLabel: 'Monto',          name: 'monto',  labelWidth: 90, hideTrigger: true, fieldStyle: 'text-align: right', columnWidth:0.45, renderer : Ext.util.Format.numberRenderer('0,000.00') },
								{ xtype: 'numberfield', fieldLabel: 'Cuotas Pagadas', name: 'cuota',  labelWidth:110, hideTrigger: true, fieldStyle: 'text-align: right', columnWidth:0.45, renderer : Ext.util.Format.numberRenderer('0,000.00') }
						]
				}
		";


		$stores = "
var persStore = new Ext.data.Store({
	fields: [ 'item', 'valor', 'sueldo'],
	autoLoad: false,
	autoSync: false,
	pageSize: 50,
	pruneModifiedRecords: true,
	totalProperty: 'results',
	proxy: {
		type: 'ajax',
		url : urlApp + 'nomina/pers/persbusca',
		extraParams: {  'codigo': mcodigo, 'origen': 'store' },
		reader: {
			type: 'json',
			totalProperty: 'results',
			root: 'data'
		}
	},
	method: 'POST'
});
		";

		$titulow = 'Asignaciones de Nomina';

		$dockedItems = "
				{ iconCls: 'icon-reset', itemId: 'close', text: 'Cerrar',   scope: this, handler: this.onClose },
				{ iconCls: 'icon-save',  itemId: 'save',  text: 'Guardar',  disabled: false, scope: this, handler: this.onSave }
		";

		$winwidget = "
				closable: false,
				closeAction: 'destroy',
				width: 450,
				height: 320,
				resizable: false,
				modal: true,
				items: [writeForm],
				listeners: {
					beforeshow: function() {
						var form = this.down('writerform').getForm();
						this.activeRecord = registro;
						if (registro) {
							mcodigo  = registro.data.codigo;
							persStore.proxy.extraParams.codigo = mcodigo ;
							persStore.load({ params: { 'codigo':  registro.data.cliente, 'origen': 'beforeform' } });
							form.loadRecord(registro);
							form.findField('codigo').setReadOnly(true);
							form.findField('concepto').setReadOnly(true);
						} else {
							form.findField('codigo').setReadOnly(false);
							form.findField('concepto').setReadOnly(false);
							mcodigo  = '';
						}						
					}
				}
";
		$features= "features: [ { ftype: 'filters', encode: 'json', local: false } ],";


		$data['encabeza']    = $encabeza;
		$data['listados']    = $listados;
		$data['otros']       = $otros;
		$data['urlajax']     = $urlajax;

		$data['variables']   = $variables;
		$data['funciones']   = $funciones;
		$data['valida']      = $valida;
		$data['stores']      = $stores;
		$data['columnas']    = $columnas;
		$data['campos']      = $campos;
		$data['camposforma'] = $camposforma;
		$data['titulow']     = $titulow;
		$data['dockedItems'] = $dockedItems;
		$data['winwidget']   = $winwidget;
		$data['filtros']     = $filtros;
		$data['features']    = $features;
		//$data['winmethod']   = $winmethod;
		
		$data['title']  = heading('Departamentos de Nomina');
		$this->load->view('extjs/extjsven',$data);
	}


}
?>