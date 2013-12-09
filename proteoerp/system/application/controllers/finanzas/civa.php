<?php
//cambioiva
class Civa extends Controller {
	var $mModulo='CIVA';
	var $titp='Cambio de IVA';
	var $tits='Cambio de IVA';
	var $url ='finanzas/civa/';

	function Civa(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'CIVA', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('civa','id') ) {
			$this->db->simple_query('ALTER TABLE civa DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE civa ADD UNIQUE INDEX fecha (fecha)');
			$this->db->simple_query('ALTER TABLE civa ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->modintramenu( 600, 400, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grid'] = $grid->deploy();

		$bodyscript = '
<script type="text/javascript">
$(function() {
	$( "input:submit, a, button", ".otros" ).button();
});

jQuery("#a1").click( function(){
	var id = jQuery("#newapi'. $param['grid']['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
	if (id)	{
		var ret = jQuery("#newapi'. $param['grid']['gridname'].'").jqGrid(\'getRowData\',id);
		window.open(\''.site_url('formatos/ver/CIVA/').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
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
		//$param['SouthPanel'] = $SouthPanel;
		$param['listados'] = $this->datasis->listados('CIVA', 'JQ');
		$param['otros']    = $this->datasis->otros('CIVA', 'JQ');
		$param['tema1']     = 'darkness';
		$param['anexos']    = 'anexos1';
		$param['bodyscript'] = $bodyscript;
		$param['tabs'] = false;
		$param['encabeza'] = $this->titp;
		$this->load->view('jqgrid/crud',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "true";

		$grid  = new $this->jqdatagrid;

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


		$grid->addField('tasa');
		$grid->label('Tasa');
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


		$grid->addField('redutasa');
		$grid->label('Redutasa');
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


		$grid->addField('sobretasa');
		$grid->label('Sobretasa');
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


		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'width'    => 40,
			'editable' => 'false',
			'search'   => 'false',
			'hidden'   => 'true'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
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
		$mWHERE = $grid->geneTopWhere('civa');

		$response   = $grid->getData('civa', array(array()), array(), false, $mWHERE );
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
				$this->db->insert('civa', $data);
				echo "Registro Agregado";
				logusu('CIVA',"Registro ????? INCLUIDO");
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			//unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('civa', $data);
			logusu('CIVA',"Registro ????? MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM civa WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM civa WHERE id=$id ");
				logusu('CIVA',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}
}


/*
class Civa extends Controller {
	var $data_type = null;
	var $data = null;
	 
	function civa(){
		parent::Controller(); 
		$this->load->helper('url');
		$this->load->helper('text');
		$this->load->library("rapyd");
		define ("THISFILE",   APPPATH."controllers/nomina". $this->uri->segment(2).EXT);
   }
  function index(){
    $this->datasis->modulo_id(509,1);
    	redirect("finanzas/civa/filteredgrid");
   }
	function filteredgrid(){
		$this->rapyd->load("datafilter","datagrid");
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter("Filtro de Cambio de IVA", 'civa');
		
		$filter->fecha = new dateonlyField("Fecha", "fecha",'d/m/Y');
		$filter->fecha->clause  =$filter->fecha->clause="where";
		$filter->fecha->size=12;
		
		$filter->tasa= new inputField("Tasa","Tasa");
		$filter->tasa->size=12;
		$filter->tasa->maxlength=6;
	 	
		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('finanzas/civa/dataedit/show/<#fecha#>','<dbdate_to_human><#fecha#></dbdate_to_human>');

		$grid = new DataGrid("Lista de Cambio de IVA");
		$grid->order_by("fecha","asc");
		$grid->per_page = 20;

		$grid->column("Fecha",$uri);
		$grid->column("Tasa","tasa");
		$grid->column("Tasa Reducida","redutasa");
		$grid->column("Tasa Adicional","sobretasa");
			  	  						
		$grid->add("finanzas/civa/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();
		
		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Cambio de Iva</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	function dataedit()
 	{
		$this->rapyd->load("dataedit");

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit("Cambio de IVA", "civa");
		$edit->back_url = site_url("finanzas/civa/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->fecha = new DateField("Fecha", "fecha");
		$edit->fecha->mode="autohide";
		$edit->fecha->rule= 'required';
		//$edit->fecha->rule= 'required|callback_chexiste';
		$edit->fecha->size = 12;
		
		$edit->tasa= new inputField("Tasa", "tasa");
		$edit->tasa->size =8;
		$edit->tasa->maxlength=6;
		$edit->tasa->rule= "trim|required|numeric";
		$edit->tasa->css_class='inputnum';
		
		$edit->redutasa = new inputField("Tasa Reducida", "redutasa");
		$edit->redutasa->size =8;
		$edit->redutasa->maxlength=6;
		$edit->redutasa->css_class='inputnum';
		$edit->redutasa->rule='trim|numeric';
		
		$edit->sobretasa =new inputField("Tasa Adicional", "sobretasa");
		$edit->sobretasa->size =8;
		$edit->sobretasa->maxlength=6;
		$edit->sobretasa->css_class='inputnum';
		$edit->sobretasa->rule='trim|numeric';
		
		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
 
		$data['content'] = $edit->output;           
    $data['title']   = "<h1>Cambio de Iva</h1>";        
    $data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
    $this->load->view('view_ventanas', $data);  
	}
	function _post_insert($do){
		$fecha=$do->get('fecha');
		$tasa=$do->get('tasa');
		logusu('civa',"CAMBIO DE IVA $fecha TASA $tasa CREADO");
	}
	function _post_update($do){
		$fecha=$do->get('fecha');
		$tasa=$do->get('tasa');
		logusu('civa',"CAMBIO DE IVA $fecha TASA $tasa MODIFICADO");
	}
	function _post_delete($do){
		$fecha=$do->get('fecha');
		$tasa=$do->get('tasa');
		logusu('civa',"CAMBIO DE IVA $fecha TASA $tasa  ELIMINADO ");
	}
	function chexiste($fecha){
		$fecha=$this->input->post('fecha');
		//echo 'aquiii'.$fecha;
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM civa WHERE fecha='$fecha'");
		if ($check > 0){
			$tasa=$this->datasis->dameval("SELECT tasa FROM civa WHERE fecha='$fecha'");
			$this->validation->set_message('chexiste',"La fecha $fecha ya existe para la tasa $tasa");
			return FALSE;
		}else {
  		return TRUE;
		}	
	}
}
*/
?>
