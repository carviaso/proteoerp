<?php 
include('marc.php');
class Unidad extends Controller{
	var $genesal=true;
	var $url ='inventario/unidad/';
	var $titp = 'Unidades';
	var $tits = 'Unidades';

	function unidad(){
		parent::Controller(); 
		$this->load->library("rapyd");
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('30C',1);
	}

	function index(){
		//redirect("inventario/unidad/filteredgrid");
		if ( !$this->datasis->iscampo('unidad','id') ) {
			$this->db->simple_query('ALTER TABLE unidad DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE unidad ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE unidad ADD UNIQUE INDEX unidades (unidades)');
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

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
		array("id"=>"fedita",  "title"=>"Agregar/Editar Registro")
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		//$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('UNIDAD', 'JQ');
		$param['otros']       = $this->datasis->otros('UNIDAD', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}


	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = "";
		return $bodyscript;
	}


	//******************************************************************
	//
	//
	function defgrid($deployed = false ){
		//$mecho = $this->uri->segment($this->uri->total_segments());
		
		$url ='inventario/unidad/';
		$titp = 'Unidades';

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('ID');
		$grid->params(array('align' => "'center'",
							'width' => 20,
							'editable' => 'false',
							'editoptions' => '{readonly:true,size:10}'
			)
		);

		$grid->addField('unidades');
		$grid->label('Nombre');
		$grid->params(array('width' => 180,
							'editable' => 'true',
							'edittype' => "'text'",
							'editrules' => '{required:true}'
			)
		);

		#show paginator
		$grid->showpager(true);
		
		$grid->setViewRecords(true);

		#width
		$grid->setWidth('250');
		#height
		$grid->setHeight('260');
		#table title
		$grid->setTitle($titp);

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setView(false);
		$grid->setRowNum(20);
		//$grid->setRowList('[]');
		#export buttons
		//$grid->setPdf(true,array('title' => 'Test pdf'));

		#GET url
		$grid->setUrlget(site_url($url.'getdata/'));

		#Set url
		$grid->setUrlput(site_url($url.'setdata/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Get data result as json
	*/
	function getData()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('unidad');

		$response   = $grid->getData('unidad', array(array()), array(), false, $mWHERE, 'unidades' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Put information
	*/
	function setData()
	{
		$this->load->library('jqdatagrid');
		$oper = $this->input->post('oper');
		// ver si puede borrar
		if ($oper == 'del') {
			// si tiene personas no puede borrar
			$id   = $this->input->post('id');
			$mSQL = "SELECT COUNT(*) FROM sinv a JOIN unidad b ON a.unidad=b.unidades WHERE b.id=$id";
			if ($this->datasis->dameval($mSQL) == 0 ){
				$grid             = $this->jqdatagrid;
				$response         = $grid->operations('unidad','id');
				echo 'Registro Borrado!!!';
			} else {
				echo 'No se puede borrar, existen productos con esta unidad';
			}			
		} else {
			$grid             = $this->jqdatagrid;
			$response         = $grid->operations('unidad','id');
			echo 'Registro Actualizado';
		}
	}



/*
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		//$uri = anchor('inventario/unidad/dataedit/show/<raencode><#unidades#></raencode>','<#unidades#>');

		$grid = new DataGrid("Unidades ",'unidad');
		$grid->order_by("unidades","asc");
		$grid->per_page = 60;

		$grid->column_sigma("Unidades", "unidades", "",      "width:210, editor: { type: 'text'} ");
		$grid->column_sigma('Productos',"produ",    "float", "align: 'right', width:80 ");

		//$grid->add("inventario/unidad/dataedit/create");
		//$grid->build();

		$mtool = '';

		//$sigmaA     = $grid->sigmaDsConfig();
		$sigmaA     = $grid->sigmaDsConfig("unidad","unidades","inventario/unidad/");
		$dsOption   = $sigmaA["dsOption"];
		$grupver    = "
function ver(value, record, columnObj, grid, colNo, rowNo){
	var url = '';
	url = '<a href=\"#\" onclick=\"window.open(\'".base_url()."inventario/unidad/dataedit/show/'+value+ '\', \'_blank\', \'width=800, height=600, scrollbars=Yes, status=Yes, resizable=Yes, screenx='+((screen.availWidth/2)-400)+',screeny='+((screen.availHeight/2)-300)+'\')\"; heigth=\"600\" >';
	url = url +value+'</a>';
	return url;
}
";
		$colsOption = $sigmaA["colsOption"];
		$gridOption = $sigmaA["gridOption"];
		$gridGuarda = "
function guardar(value, oldValue, record, col, grid) {
	var murl='';
	if (oldValue == '') { oldValue='__VACIO__'};
	murl = '".base_url()."/inventario/unidad/modifica/'+encodeURIComponent(oldValue)+'/'+col.id+'/'+encodeURIComponent(value);
	if ( value != oldValue ) { $.ajax({url: murl,context: document.body}); }
};
";

		 $gridGo = "
var mygrid=new Sigma.Grid(gridOption);
mygrid.saveURL = '".base_url()."inventario/unidad/controlador',
mygrid.width = 310;
mygrid.height = 400;
mygrid.toolbarContent = 'nav | reload | add del save | print ';
Sigma.Util.onLoad( Sigma.Grid.render(mygrid) );
";
		$SigmaCont = "<center><div id=\"grid1_container\" style=\"width:310px;height:400px;\"></div><center>";
		$grid->add("inventario/marc/dataedit/create");
		$grid->build('datagridSG');

		$data['style']  = style("redmond/jquery-ui.css");
		$data['style'] .= style('gt_grid.css');

		$data['script']  = script("jquery.js");
		$data['script'] .= script("gt_msg_es.js");
		$data['script'] .= script("gt_grid_all.js");
		$data['script'] .= "<script type=\"text/javascript\" >\n";
		$data['script'] .= $dsOption.$grupver."\n";
		$data['script'] .= $colsOption."\n";
		$data['script'] .= $gridOption;
		$data['script'] .= $gridGuarda;
		$data['script'] .= $gridGo;
		$data['script'] .= "\n</script>";

		$data['content'] = $mtool.$SigmaCont;  //$grid->output;
		$data['title']   = "<h1>Unidades</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_simple', $data);	
	}
*/

	//******************************************************************
	//
	//
	function dataedit(){
		$this->rapyd->load("dataedit");

		$edit = new DataEdit("Unidad","unidad");
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->back_url = site_url("inventario/unidad/filteredgrid");

		$edit->unidades =  new inputField("Unidad",'unidades');
		$edit->unidades ->size = 15;
		$edit->unidades ->maxlength=30;
		$edit->unidades ->rule = "trim|strtoupper|required";

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		if($this->genesal){
			$edit->build();
			$data['content'] = $edit->output;
			$data['title']   = heading('Unidad');
			$data['head']    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}else{
			$edit->on_save_redirect=false;
			$edit->build();

			if($edit->on_success()){
				echo 'Pedido Guardado';
			}elseif($edit->on_error()){
				echo html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
		}
	}

/*
	// sigma grid
	function controlador(){
		if (isset($_POST["_gt_json"]) ) {
			$json=json_decode(stripslashes($_POST["_gt_json"]));
			if($json->{'action'} == 'load') {
				$pageNo   = $json->{'pageInfo'}->{'pageNum'};
				$pageSize = $json->{'pageInfo'}->{'pageSize'};
				$filter = '';

				if(isset($json->{'sortInfo'}[0]->{'columnId'})){
					$sortField = $json->{'sortInfo'}[0]->{'columnId'};
				} else {
					$sortField = "unidades";
				}

				if(isset($json->{'sortInfo'}[0]->{'sortOrder'})){
					$sortOrder = $json->{'sortInfo'}[0]->{'sortOrder'};
				} else {
					$sortOrder = "ASC";
				}

				for ($i = 0; $i < count($json->{'filterInfo'}); $i++) {
					if($json->{'filterInfo'}[$i]->{'logic'} == "equal"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . "='" . $json->{'filterInfo'}[$i]->{'value'} . "' ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "notEqual"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . "!='" . $json->{'filterInfo'}[$i]->{'value'} . "' ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "less"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . "<" . $json->{'filterInfo'}[$i]->{'value'} . " ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "lessEqual"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . "<=" . $json->{'filterInfo'}[$i]->{'value'} . " ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "great"){
							$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . ">" . $json->{'filterInfo'}[$i]->{'value'} . " ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "greatEqual"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . ">=" . $json->{'filterInfo'}[$i]->{'value'} . " ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "like"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . " LIKE '%" . $json->{'filterInfo'}[$i]->{'value'} . "%' ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "startWith"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . " LIKE '" . $json->{'filterInfo'}[$i]->{'value'} . "%' ";
					}elseif($json->{'filterInfo'}[$i]->{'logic'} == "endWith"){
						$filter .= $json->{'filterInfo'}[$i]->{'columnId'} . " LIKE '%" . $json->{'filterInfo'}[$i]->{'value'} . "' ";
					}
					$filter .= " AND ";
				}


				//to get how many total records.
				$mSQL = "SELECT count(*) FROM unidad WHERE $filter unidades IS NOT NULL";
				$totalRec = $this->datasis->dameval($mSQL);
 
 				//make sure pageNo is inbound
				if($pageNo<1||$pageNo>ceil(($totalRec/$pageSize))){
					$pageNo = 1;
				}
 
				$mSQL = "SELECT a.unidades, count(*) produ ";
				$mSQL .= "FROM unidad a LEFT JOIN sinv b ON a.unidades=b.unidad WHERE $filter a.unidades IS NOT NULL GROUP BY a.unidades ORDER BY ".$sortField." ".$sortOrder." LIMIT ".($pageNo - 1)*$pageSize.", ".$pageSize;
				$query = $this->db->query($mSQL);
				if ($query->num_rows() > 0){
					$retArray = array();
					foreach( $query->result_array() as  $row ) {
						if ( $row['unidades'] != '' ) $retArray[] = $row;
					}
					$data = json_encode($retArray);
					$ret = "{data:" . $data .",\n";
					$ret .= "pageInfo:{totalRowNum:" . $totalRec . "},\n";
					$ret .= "recordType : 'object'}";
				} else {
					$ret = '{data : []}';
				}
				echo $ret;
				//memowrite($ret.$mSQL.$retArray,'unidades');

			}else if($json->{'action'} == 'save'){
				
				for ($i = 0; $i < count($json->{'insertedRecords'}); $i++) {
					$unidades = $json->{'insertedRecords'}[$i]->{'unidades'};
					$mSQL = "INSERT IGNORE INTO unidad SET unidades='".addslashes($unidades)."'";
					$this->db->simple_query($mSQL);
				}
				for ($i = 0; $i < count($json->{'deletedRecords'}); $i++) {
					$unidades = $json->{'deletedRecords'}[$i]->{'unidades'};
					$mSQL = "SELECT COUNT(*) FROM sinv WHERE marca='".addslashes($unidades)."'";
					if ( $this->datasis->dameval($mSQL) == 0 ) {
						$mSQL = "DELETE FROM marc WHERE marca='".addslashes($unidades)."'";
						$this->db->simple_query($mSQL);
					}
				}
			}
		} else {
			// no hay _gt_json
			echo '{data : []}';
		}
	}
*/

	function modifica(){
		$valor = $this->uri->segment($this->uri->total_segments());
		$campo = $this->uri->segment($this->uri->total_segments()-1);
		$grupo = $this->uri->segment($this->uri->total_segments()-2);
		if ( $grupo == '__VACIO__') $grupo = "";
		// Si ya exsite se borra
		$mSQL = "SELECT COUNT(*) FROM unidad WHERE unidades='".addslashes($valor)."' ";
		if ( $this->datasis->dameval($mSQL) == 0 ) {
			$mSQL = "UPDATE unidad SET ".$campo."='".addslashes($valor)."' WHERE unidades='".addslashes($grupo)."' ";
			$this->db->simple_query($mSQL);
		};
		$mSQL = "UPDATE sinv SET unidad='".addslashes($valor)."' WHERE unidad='".addslashes($grupo)."' ";

		$this->db->simple_query($mSQL);
		
		echo "$valor $campo $grupo";
	}

}
