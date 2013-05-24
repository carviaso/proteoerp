<?php require_once(BASEPATH.'application/controllers/validaciones.php');
//lineasinventario
 class Fami extends validaciones {
	
	function fami(){
		parent::Controller(); 
		$this->load->library("rapyd");
	  //$this->datasis->modulo_id(306,1);
	}
    function index(){
    	redirect("supermercado/fami/filteredgrid");
    }
 
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro por Familias");
		
		$filter->db->select("familia, a.descrip AS descrip,b.depto AS dpto, b.descrip AS depto, a.cu_inve AS cu_inve, a.cu_venta AS cu_venta, a.cu_cost AS cu_cost, a.cu_devo AS cu_devo");
		$filter->db->from("fami AS a");
		$filter->db->join("dpto AS b","a.depto=b.depto");
		
		$filter->familia = new inputField("C&oacute;digo Familia", "familia");
		$filter->familia->size=20;
		
		$filter->descrip = new inputField("Descripci&oacute;n","descrip");
		$filter->descrip->size=20;
		
		$filter->depto = new inputField("Departamento","b.descrip");
		$filter->depto->size=20;
		
		$filter->buttons("reset","search");
		$filter->build();

		$uri   = anchor('supermercado/fami/dataedit/show/<raencode><#dpto#></raencode>/<raencode><#familia#></raencode>','<#familia#>');
		$uri_2 = anchor('supermercado/fami/dataedit/create/<raencode><#dpto#></raencode>/<raencode><#familia#></raencode>','Duplicar');

		$grid = new DataGrid("Lista de Familias de Inventario");
		$grid->order_by("familia","asc");
		$grid->per_page = 20;

		$grid->column("C&oacute;digo Familia"     ,$uri       ,"align='center'");
		$grid->column("Descripci&oacute;n"               ,"descrip"  ,"align='left'");
		$grid->column("Departamento"              ,"depto"    ,"align='left'");
		$grid->column("Cuenta Costo"              ,"cu_cost"  ,"align='center'");
		$grid->column("Cuenta Inventario"         ,"cu_inve"  ,"align='center'");
		$grid->column("Cuenta Venta"              ,"cu_venta" ,"align='center'");
		$grid->column("Cuenta Devoluci&oacute;n"  ,"cu_devo"  ,"align='center'");
		$grid->column("Duplicar"                  ,$uri_2     ,"align='center'");


		$grid->add("supermercado/fami/dataedit/create");
		$grid->build();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Familias de Inventario</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
	function dataedit($status='',$id='',$id2='')//
 	{
		$this->rapyd->load("dataobject","dataedit");

		$qformato=$this->qformato=$this->datasis->formato_cpla();
		$link=site_url('supermercado/fami/ultimo');
		$link2=site_url('supermercado/fami/sugerir');

		$script='
		$(document).ready(function(){
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
							$("#familia").val(msg);
						}
						else{
							alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
						}
					}
				});
		}		
		';

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

		$do = new DataObject("fami");
		if(($status=="create") && !empty($id) && !empty($id2)){
			$do->load(array("depto"=> $id,"familia"=> $id2));
		}

		$edit = new DataEdit("L&iacute;nea de Inventario",$do);
		$edit->back_url = site_url("supermercado/fami/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->pre_process('delete','_pre_del');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->depto = new dropdownField("Departamento", "depto");
		$edit->depto->option("","");
		$edit->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");
		$edit->depto->rule ="required";
		$edit->depto->style='width:250px;';
		
		$ultimo='<a href="javascript:ultimo();" title="Consultar ultimo c&oacute;digo ingresado"> Consultar ultimo c&oacute;digo </a>';
		$sugerir='<a href="javascript:sugerir();" title="Sugerir un C&oacute;digo aleatorio"> Sugerir C&oacute;digo </a>';
		$edit->familia =  new inputField("C&oacute;digo Familia", "familia");
		$edit->familia->mode="autohide";
		$edit->familia->size =4;
		$edit->familia->rule ="trim|strtoupper|required|callback_chexiste";
		$edit->familia->maxlength=2;
		$edit->familia->append($sugerir);
		$edit->familia->append($ultimo);
				
		$edit->descrip =  new inputField("Descripci&oacute;n", "descrip");
		$edit->descrip->size = 35;
		$edit->descrip->rule= "trim|strtoupper|required";
		$edit->descrip->maxlength=30;
		
		for($i=1;$i<=5;$i++){
			$obj="margen$i";
			$edit->$obj = new inputField("Margen $i. %", $obj);
			$edit->$obj->size = 18;
			$edit->$obj->maxlength=7;
			$edit->$obj->css_class='inputnum';
			$edit->$obj->rule='trim|numeric|callback_positivo';
		}
		
		$edit->cu_inve =new inputField("Cuenta Inventario", "cu_inve");
		$edit->cu_inve->size = 18;
		$edit->cu_inve->maxlength=15;
		$edit->cu_inve->rule ="trim|existecpla";
		$edit->cu_inve->append($bcu_inve);
		
		$edit->cu_cost =new inputField("Cuenta Costo", "cu_cost");
		$edit->cu_cost->size = 18;
    $edit->cu_cost->maxlength=15;
    $edit->cu_cost->rule ="trim|existecpla";
    $edit->cu_cost->append($bcu_cost);
		
		$edit->cu_venta  =new inputField("Cuenta Venta", "cu_venta");
		$edit->cu_venta->size =18;
		$edit->cu_venta->maxlength=15;
		$edit->cu_venta->rule ="trim|existecpla";
		$edit->cu_venta->append($bcu_venta);
		
    $edit->cu_devo = new inputField("Cuenta Devoluci&oacute;n","cu_devo");
    $edit->cu_devo->size = 18;
    $edit->cu_devo->maxlength=15;
    $edit->cu_devo->rule ="trim|existecpla";
    $edit->cu_devo->append($bcu_devo);
    
    
    	 		   	
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
 
		$data['content'] = $edit->output;           
    $data['title']   = "<h1>Familias de Inventario</h1>";        
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").$this->rapyd->get_head();//script("plugins/jquery.floatnumber.js").
    $this->load->view('view_ventanas', $data);  
	}
	function _post_insert($do){
		$codigo=$do->get('linea');
		$nombre=$do->get('descrip');
		logusu('line',"LINEA DE INVENTARIO $codigo NOMBRE  $nombre CREADO");
	}
	function _post_update($do){
		$codigo=$do->get('linea');
		$nombre=$do->get('descrip');
		logusu('line',"LINEA DE INVENTARIO $codigo NOMBRE  $nombre  MODIFICADO");
	}
	function _post_delete($do){
		$codigo=$do->get('linea');
		$nombre=$do->get('descrip');
		logusu('line',"LINEA DE INVENTARIO $codigo NOMBRE  $nombre  ELIMINADO ");
	}
	
	function chexiste($codigo){
		$codigo=$this->input->post('linea');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM line WHERE linea='$codigo'");
		if ($check > 0){
			$linea=$this->datasis->dameval("SELECT descrip FROM line WHERE linea='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para la linea $linea");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
	
	function _pre_del($do) {
		$codigo=$do->get('line');
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM grup WHERE linea='$codigo'");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='La l&iacute;nea contiene grupos, por ello no puede ser eliminada. Elimine primero todos los grupos que pertenezcan a esta l&iacute;nea';
			return False;
		}
		return True;
	}
	
	function ultimo(){
		$ultimo=$this->datasis->dameval('SELECT familia FROM fami ORDER BY familia DESC LIMIT 1');
		echo $ultimo;
	}
	
	function sugerir(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,3,0) FROM serie LEFT JOIN fami ON LPAD(familia,3,0)=LPAD(hexa,3,0) WHERE valor<4095 AND familia IS NULL LIMIT 1");
		echo $ultimo;
	}
	
	function positivo($valor){
		if ($valor < 0){
			$this->validation->set_message('positivo',"El campo margen debe ser positivo");
			return FALSE;
		}
  	return TRUE;
	}
}
?>