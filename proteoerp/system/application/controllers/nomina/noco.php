<?php
class Noco extends Controller {
	var $mModulo='NOCO';
	var $titp='Contratos de Nomina';
	var $tits='Contratos de Nomina';
	var $url ='nomina/noco/';

	function Noco(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_nombre('NOCO',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('noco','id') ) {
			$this->db->simple_query('ALTER TABLE noco DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE noco ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE noco ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		if ( !$this->datasis->iscampo('itnoco','id') ) {
			$this->db->simple_query('ALTER TABLE itnoco DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE itnoco ADD UNIQUE INDEX codigo (codigo, concepto)');
			$this->db->simple_query('ALTER TABLE itnoco ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->modintramenu(900,700,"nomina/noco");
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$grid1   = $this->defgridit();
		$param['grids'][] = $grid1->deploy();

		$grid2   = $this->defgridcon();
		$param['grids'][] = $grid2->deploy();


		$readyLayout = '
	$(\'body\').layout({
		minSize: 30,
		north__size: 60,
		resizerClass: \'ui-state-default\',
		west__size: 212,
		west__onresize: function (pane, $Pane){jQuery("#west-grid").jqGrid(\'setGridWidth\',$Pane.innerWidth()-2);},
	});
	
	$(\'div.ui-layout-center\').layout({
		minSize: 30,
		resizerClass: "ui-state-default",
		center__paneSelector: ".centro-centro",
		south__paneSelector:  ".centro-sur",
		south__size: 260,
		center__onresize: function (pane, $Pane) {
			jQuery("#newapi'.$param['grids'][0]['gridname'].'").jqGrid(\'setGridWidth\',$Pane.innerWidth()-6);
			jQuery("#newapi'.$param['grids'][0]['gridname'].'").jqGrid(\'setGridHeight\',$Pane.innerHeight()-100);
		}
	});
	';


		$bodyscript = '
<script type="text/javascript">
var idnoco = 0;
$(function() {
	$( "input:submit, a, button", ".boton1" ).button();
});

jQuery("#boton1").click( function(){
	var id = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
	if (id)	{
		var ret = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getRowData\',id);
		window.open(\''.site_url('formatos/ver/APAN/').'\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
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
	</tr><tr>
		<td><div class="tema1"><table id="otros"></table></div></td>
	</tr>
</table>
</div>
<table id="west-grid" align="center">
	<tr>
		<td><div class="tema1 boton1"><a style="width:190px" href="#" id="boton1">Imprimir Contrato '.img(array('src' => 'images/pdf_logo.gif', 'alt' => 'Formato PDF',  'title' => 'Formato PDF', 'border'=>'0')).'</a></div></td>
	</tr>
</table>
<br>
<br>
<br>
<br>
<br>
<br>
<b>Ayuda:</b>
<hr>
Para <b>AGREGAR</b> un concepto al contrato, seleccione uno y haga doble click sobre un <b>"Conceptos Disponibles"</b>.
<br>
<hr>
Para <b>ELIMINAR</b> un concepto del contrato, seleccione uno y haga doble click sobre un <b>"Concepto del Contrato</b>".
<hr>


'.

'</div> <!-- #LeftPane -->
';

		$centerpanel = '
<div id="RightPane" class="ui-layout-center">
	<div class="centro-centro">
		<table id="newapi'.$param['grids'][0]['gridname'].'"></table>
		<div id="pnewapi'.$param['grids'][0]['gridname'].'"></div>
	</div>
	<div class="centro-sur" id="adicional" style="overflow:auto;">
	<table width="100%"><tr>
		<td><table id="newapi'.$param['grids'][1]['gridname'].'"></table></td>
		<td><table id="newapi'.$param['grids'][2]['gridname'].'"></table></td>
	</tr></table>
	</div>
</div> <!-- #RightPane -->
';


		$SouthPanel = '
<div id="BottomPane" class="ui-layout-south ui-widget ui-widget-content">
<p>'.$this->datasis->traevalor('TITULO1').'</p>
</div> <!-- #BottomPanel -->
';

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('NOCO', 'JQ');
		$param['otros']        = $this->datasis->otros('NOCO', 'JQ');
		
		$param['centerpanel']  = $centerpanel;
		//$param['funciones']    = $funciones;

		$param['temas']        = array('proteo','darkness','anexos1');

		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;

		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "true";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('tipo');
		$grid->label('Freq.');
		$grid->params(array(
			'align'         => "'center'",
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"S":"Semanal", "Q":"Quincenal", "B":"Bisemanal", "M":"Mensual" }, style:"width:170px"} ',
			'editrules'     => '{ required:true}',
			'formoptions'   => '{ label: "Frecuencia" }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('observa1');
		$grid->label('Observaciones 1');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('observa2');
		$grid->label('Observaciones 2');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));


		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Modificado" }'
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
		$grid->setHeight('220');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
			function(id){
				if (id){
					idnoco = id;
					jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+id+"/", page:1});
					jQuery(gridId2).trigger("reloadGrid");
				}
			}');
		$grid->setOndblClickRow('');


		//$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		//$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 480, height:220, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 480, height:220, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
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
		$mWHERE = $grid->geneTopWhere('noco');

		$response   = $grid->getData('noco', array(array()), array(), false, $mWHERE, 'codigo' );
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
				$codigo = $data['codigo'];
				$msql = "SELECT COUNT(0) FROM noco WHERE codigo=".$this->db->escape($codigo);
				if ($this->datasis->dameval($msql) == 0 ) {
					$this->db->insert('noco', $data);
					echo "Contrato Agregado";
					logusu('NOCO',"Contrato de Nomina ".$codigo." INCLUIDO");
				} else {
					echo "Codigo ya existe";
				}
			} else
			echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$codigo = $data['codigo'];
			unset($data['codigo']);
			$this->db->where('id', $id);
			$this->db->update('noco', $data);
			logusu('NOCO',"Contrato de Nomina ".$codigo." MODIFICADO");
			echo "Contrato Modificado";

		} elseif($oper == 'del') {
			$codigo = $this->datasis->dameval("SELECT codigo FROM noco WHERE id=$id");
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE trabaja=".$this->db->escape($codigo)." or contrato=".$this->db->escape($codigo));
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM noco   WHERE id=$id ");
				$this->db->simple_query("DELETE FROM itnoco WHERE codigo=".$this->db->escape($codigo) );
				logusu('NOCO',"Contrato de Nomina ".$codigo." ELIMINADO");
				echo "Contrato Eliminado";
			}
		};
	}


	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgridit( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;
/*
		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 5 }',
		));
*/

		$grid->addField('concepto');
		$grid->label('Cod.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 35,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));


		$grid->addField('descrip');
		$grid->label('Concepto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 35 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 35,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false',
			'hidden'        => 'true'
		));

		$grid->showpager(true);
		$grid->setWidth('310');
		$grid->setHeight('195');
		$grid->setTitle('Conceptos del Contrato');
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setOndblClickRow('
			,ondblClickRow: function(id){
				if (id){
					$.prompt( "Eliminar Concepto del Contrato? ",{
						buttons: { Eliminar:true, Cancelar:false},
						submit: function(e,v,m,f){
							if (v == true) {
								$.get("'.base_url().$this->url.'elimina/"+id,
								function(data){
									//alert(data);
									jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+idnoco+"/", page:1});
									jQuery(gridId2).trigger("reloadGrid");
								});
							}
						}
					});
				}
			}');


		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		//$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatait/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdatait()
	{
		$grid  = $this->jqdatagrid;
		$id    = $this->uri->segment(4);
		if ($id == false ){
			$id = $this->datasis->dameval("SELECT MIN(id) FROM noco");
		}
		$codigo = $this->datasis->dameval("SELECT codigo FROM noco WHERE id=$id");

		$grid    = $this->jqdatagrid;
		$mSQL    = "SELECT * FROM itnoco WHERE codigo=".$this->db->escape($codigo)." ORDER BY concepto ";
		$response   = $grid->getDataSimple($mSQL);
		$rs = $grid->jsonresult( $response);
		echo $rs;

	}



	/**
	* Agrega Concepto a Contrato
	*/
	function elimina()
	{
		$id     = $this->uri->segment(4);
		$idnoco = $this->uri->segment(5);
		$msql = "DELETE FROM itnoco WHERE id=$id";
		$this->db->query($msql);
		$rs = "Concepto eliminado del Contrato";
		echo $rs;
	}



	//******************************************************************
	//Definicion del Grid y la Forma
	//
	function defgridcon( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('concepto');
		$grid->label('Cod.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 35,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 4 }',
		));


		$grid->addField('descrip');
		$grid->label('Concepto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 35 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 35,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 1 }',
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false',
			'hidden'        => 'true'
		));


		$grid->showpager(true);
		$grid->setWidth('310');
		$grid->setHeight('195');
		$grid->setTitle('Conceptos Disponibles');
		$grid->setfilterToolbar(false);
		$grid->setToolbar('false', '"top"');

		$grid->setOndblClickRow('
			,ondblClickRow: function(id){
				if (id){
					$.prompt( "Agregar Concepto al Contrato? ",{
						buttons: { Agregar:1, Cancelar:0 },
						submit: function(e,v,m,f){
							if ( v == 1) {
								$.get("'.base_url().$this->url.'agrega/"+id+"/"+idnoco,
								function(data){
									//alert(data);
									jQuery(gridId2).jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdatait/').'/"+idnoco+"/", page:1});
									jQuery(gridId2).trigger("reloadGrid");
								});
							}
						}
					});
				}
			}');


		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(300);
		$grid->setShrinkToFit('false');

		#GET url
		$grid->setUrlget(site_url($this->url.'getdatacon/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdatacon()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('conc');

		$response   = $grid->getData('conc', array(array()), array(), false, $mWHERE, 'concepto' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}


	/**
	* Agrega Concepto a Contrato
	*/
	function agrega()
	{
		$id     = $this->uri->segment(4);
		$idnoco = $this->uri->segment(5);

		$codigo   = $this->datasis->dameval("SELECT codigo   FROM noco WHERE id=$idnoco");
		$concepto = $this->datasis->dameval("SELECT concepto FROM conc WHERE id=$id");
		$rs = "El Contrato ya contienen ese Concepto ".$concepto;
		
		$msql = "SELECT COUNT(*) FROM itnoco WHERE codigo=".$this->db->escape($codigo)." AND concepto=".$this->db->escape($concepto);

		if ($this->datasis->dameval($msql) == 0 ){
			$msql  = "INSERT IGNORE INTO itnoco ( codigo, concepto, descrip, tipo, grupo )
				  SELECT ".$this->db->escape($codigo)." codigo, concepto, descrip, tipo, grupo
				  FROM conc WHERE concepto=".$this->db->escape($concepto);
			$this->db->query($msql);
			$rs = "Concepto ".$concepto." agregado al Contrato";
		}
		echo $rs;
	}





/*
class Noco extends Controller {
	
	function noco(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		//$this->datasis->modulo_id(707,1);
		redirect("nomina/noco/extgrid");
	}

	function extgrid(){
		//$this->datasis->modulo_id(707,1);
		$script = $this->nocoextjs();
		$data["script"] = $script;
		$data['title']  = heading('Personal');
		$this->load->view('extjs/pers',$data);
	}
	
	function filtergrid() {		
		$this->rapyd->load("datagrid","datafilter");
		
		$filter = new DataFilter("Filtro de Contrato de Nomina",'noco');
		
		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo->size=10;
		
		$filter->nombre = new inputField("Nombre", "nombre");
		$filter->nombre->size=40;
		
		$filter->buttons("reset","search");
		$filter->build('dataformfiltro');
    
		$uri = anchor('nomina/noco/dataedit/show/<#codigo#>','<#codigo#>');
		$uri_2  = anchor('nomina/noco/dataedit/modify/<#codigo#>',img(array('src'=>'images/editar.png','border'=>'0','alt'=>'Editar','height'=>'12')));
    
		$mtool  = "<table background='#554455'><tr>";
		$mtool .= "<td>&nbsp;</td>";

		$mtool .= "<td>&nbsp;<a href='".base_url()."nomina/noco/dataedit/create'>";
		$mtool .= img(array('src' => 'images/agregar.jpg', 'alt' => 'Agregar Registro', 'title' => 'Agregar Registro','border'=>'0','height'=>'32'));
		$mtool .= "</a>&nbsp;</td>";

		$mtool .= "</tr></table>";
		
		$grid = new DataGrid($mtool);
		$grid->order_by("codigo","asc");
		$grid->per_page = 50;
		
		$grid->column('Acci&oacute;n',$uri_2,'align=center');
		$grid->column_orderby("C&oacute;digo",$uri,'codigo');
		$grid->column_orderby("Tipo","tipo",'tipo');
		$grid->column_orderby("Nombre","nombre",'nombre');
		$grid->column_orderby("Observaci&oacute;n","observa1",'observa1');
		$grid->column_orderby("Observaci&oacute;n","observa2",'observa2');
		
		//$grid->add("nomina/noco/dataedit/create");
		$grid->build('datagridST');
		
		//************ SUPER TABLE ************* 
		$extras = '
<script type="text/javascript">
//<![CDATA[
(function() {
	var mySt = new superTable("demoTable", {
	cssSkin : "sSky",
	fixedCols : 1,
	headerRows : 1,
	onStart : function () {	this.start = new Date();},
	onFinish : function () {document.getElementById("testDiv").innerHTML += "Finished...<br>" + ((new Date()) - this.start) + "ms.<br>";}
	});
})();
//]]>
</script>
';
		$style ='
<style type="text/css">
.fakeContainer { 
    margin: 5px;
    padding: 0px;
    border: none;
    width: 740px;
    height: 320px;
    overflow: hidden;
}
</style>	
';
// ****************************************


		$data['style']   = $style;
		$data['style']  .= style('superTables.css');
		$data['extras']  = $extras;		

		$data['content'] = $grid->output;
		$data['filtro']  = $filter->output;

		$data['title']  = heading('Contratos');
		$data['script'] = script('jquery.js');
		$data["script"].= script('superTables.js');
		$data['head']   = $this->rapyd->get_head();

		$this->load->view('view_ventanas', $data);
	}
	
	function dataedit(){
 		$this->rapyd->load('dataobject','datadetails');
 		$modbus=array(
			'tabla'   =>'conc',
			'columnas'=>array(
				'concepto' =>'Concepto',
				'tipo'=>'tipo',
				'descrip'=>'Descripci&oacute;n',
 				'grupo'=>'Grupo'),
			'filtro'  =>array('concepto'=>'C&ocaute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('concepto'=>'concepto_<#i#>','descrip'=>'descrip_<#i#>',
								'tipo'=>'it_tipo_<#i#>','grupo'=>'grupo_<#i#>'),
			'titulo'  =>'Buscar Cconcepto',
			'p_uri'=>array(4=>'<#i#>')
			);
 		$btn=$this->datasis->p_modbus($modbus,'<#i#>');
 		
		$do = new DataObject("noco");
		$do->rel_one_to_many('itnoco', 'itnoco', array('codigo'));
		
		$edit = new DataDetails('Contratos', $do);
		$edit->back_url = site_url('nomina/noco/index');
		$edit->set_rel_title('itnoco','Contratos <#o#>');

		//$edit->pre_process('insert' ,'_pre_insert');
		//$edit->pre_process('update' ,'_pre_update');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size = 10;
		$edit->codigo->rule= "required";
		$edit->codigo->mode="autohide";
		$edit->codigo->maxlength=8;
		
		$edit->nombre  = new inputField("Nombre", "nombre");
		$edit->nombre->maxlength=40;
		$edit->nombre->rule="required";
		$edit->nombre->size = 30;

		$edit->tipo = new dropdownField("Tipo", "tipo");
		$edit->tipo->style="width:110px";
		$edit->tipo->option("S","Semanal");
		$edit->tipo->option("Q","Quincenal");
		$edit->tipo->option("M","Mensual");
		$edit->tipo->option("O","Otro");
		
		$edit->observa1  = new inputField("Observaciones", "observa1");
		$edit->observa1->maxlength=60;
		$edit->observa1->size = 60;
		
		$edit->observa2  = new inputField("Observaci&oacute;n", "observa2");
		$edit->observa2->maxlength=60;
		$edit->observa2->size = 60;
		
		
		//Campos para el detalle
		
		$edit->concepto = new inputField("C&oacute;ncepto <#o#>", "concepto_<#i#>");
		$edit->concepto->size=11;
		$edit->concepto->db_name='concepto';
		$edit->concepto->append($btn);
		$edit->concepto->readonly=TRUE;
		$edit->concepto->rel_id = 'itnoco';
		
		$edit->descrip = new inputField("Descripci&oacute;n <#o#>", "descrip_<#i#>");
		$edit->descrip->size=45;
		$edit->descrip->db_name='descrip';
		$edit->descrip->maxlength=60;
		$edit->descrip->rel_id = 'itnoco';
		$edit->descrip->readonly=TRUE;
		
		$edit->it_tipo = new inputField("Tipo <#o#>", "it_tipo_<#i#>");
		$edit->it_tipo->size=2;
		$edit->it_tipo->db_name='tipo';
		$edit->it_tipo->rel_id = 'itnoco';
		$edit->it_tipo->readonly=TRUE;
		
		$edit->grupo = new inputField("Grupo <#o#>", "grupo_<#i#>");
		$edit->grupo->size=5;
		$edit->grupo->db_name='grupo';
		$edit->grupo->rel_id = 'itnoco';
		$edit->grupo->readonly=TRUE;

		//fin de campos para detalle

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back','add_rel');
		$edit->build();
		
		$conten['form']  =&  $edit;
		$data['content'] = $this->load->view('view_noco', $conten,true);
		$data['title']   = heading('Contratos de Nomina');
		$data['script']    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').phpscript('nformat.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').phpscript('nformat.js');
		$data["head"]   = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function _post_insert($do){
		$codigo=$do->get('codigo');
		logusu('noco',"Contrato de Nomina $codigo CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');
		logusu('noco',"Contrato de Nomina $codigo MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('codigo');
		logusu('noco',"Contrato de Nomina $codigo ELIMINADO");
	}
	function instala(){
		
		$sql="ALTER TABLE `noco`   ADD COLUMN 'id' INT(11) UNSIGNED NULL AUTO_INCREMENT AFTER observa2, DROP PRIMARY KEY, ADD UNIQUE INDEX codigo (codigo), ADD PRIMARY KEY (`id`)";
		$this->db->query($sql);	
		$sql="ALTER TABLE `itnoco` ADD COLUMN 'id' INT(11) UNSIGNED NULL AUTO_INCREMENT AFTER grupo, DROP PRIMARY KEY, ADD UNIQUE INDEX codigo (codigo, concepto), ADD PRIMARY KEY (`id`)";
		$this->db->query($sql);	

	}

	function manoco(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : 'contrato';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;
		$id      = isset($_REQUEST['id'])     ? $_REQUEST['id']      : 1;

		$query = $this->db->query("SELECT id, codigo, tipo, nombre, observa1, observa2 FROM noco ORDER BY codigo");

		$results = $this->db->count_all('noco');
		$arr = array();
		foreach ($query->result_array() as $row)
		{
			$meco = array();
			foreach( $row as $idd=>$campo ) {
				$meco[$idd] = utf8_encode($campo);
			}
			$arr[] = $meco;
		}
		echo '{success:true, message:"Loaded data" ,results:'. $results.', maestro:'.json_encode($arr).'}';
	}

	function itnoco(){
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']   : 'contrato';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : null;
		$codigo = isset($_REQUEST['codigo']) ? $_REQUEST['codigo']  : null;

		if ($codigo == null ) $codigo=$this->datasis->dameval("SELECT codigo FROM noco ORDER BY codigo LIMIT 1");
	
		$mSQL = "SELECT b.id, b.concepto, b.descrip, b.tipo, b.grupo FROM itnoco a JOIN conc b ON a.concepto=b.concepto WHERE a.codigo='$codigo'";
		$query = $this->db->query($mSQL);
		$results = $this->db->count_all('itnoco');

		$arr = array();
		foreach ($query->result_array() as $row)
		{
			$meco = array();
			foreach( $row as $idd=>$campo ) {
				$meco[$idd] = utf8_encode($campo);
			}
			$arr[] = $meco;
		}
		echo '{success:true, message:"Loaded data" ,results:'. $results.', detalle:'.json_encode($arr).'}';
	}

	function conc(){
		$codigo = isset($_REQUEST['codigo']) ? $_REQUEST['codigo']  : null;
		if ($codigo == null ) $codigo=$this->datasis->dameval("SELECT codigo FROM noco ORDER BY codigo LIMIT 1");
		$mSQL = "SELECT id, concepto, descrip, tipo, grupo FROM conc WHERE concepto NOT IN (SELECT concepto FROM itnoco WHERE codigo='$codigo') ORDER BY concepto";
		$query = $this->db->query($mSQL);
		$results = $this->db->count_all('conc');
		$arr = array();
		foreach ($query->result_array() as $row)
		{
			$meco = array();
			foreach( $row as $idd=>$campo ) {
				$meco[$idd] = utf8_encode($campo);
			}
			$arr[] = $meco;
		}
		echo '{success:true, message:"Loaded data" ,results:'. $results.', conceptos:'.json_encode($arr).'}';
	}

	function modificanoco(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data;

		$codigo = $data['codigo'];
		$nombre = trim($data['nombre']);
		unset($campos['codigo']);

		//print_r($campos);
		$mSQL = $this->db->update_string("noco", $campos,"id='".$data['id']."'" );
		$this->db->simple_query($mSQL);
		logusu('noco',"CONTRATOS $codigo NOMBRE  $nombre MODIFICADO");
		echo "{ success: true, message: 'Contrato Modificado'}";
	}

	function crearnoco(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data;
		$codigo = $data['codigo'];
		$nombre = trim($data['nombre']);

		if ( !empty($codigo) and !empty($nombre) ) {
			unset($campos['id']);
			// Revisa si existe ya ese contrato
			if ($this->datasis->dameval("SELECT COUNT(*) FROM noco WHERE codigo='$codigo'") == 0)
			{
				//print_r($campos);
				$mSQL = $this->db->insert_string("noco", $campos );
				$this->db->simple_query($mSQL);
				logusu('noco',"CONTRATOS $codigo NOMBRE  $nombre CREADO");
				echo "{ success: true, message: 'Contrato Modificado'}";
			} else {
				echo "{ success: false, message: 'Ya existe un contrato con ese Codigo!!'}";
			}
			
		} else {
			//echo "{ success: false, message: 'Ya existe un contrato con ese Codigo!!'}";
			// No 
		}
	}

	function eliminarnoco(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		//print_r($data);
		$campos = $data;
		$codigo = $data['codigo'];
		$pers   = $this->datasis->dameval("SELECT COUNT(*) FROM pers   WHERE contrato='$codigo'");
		$nomina = $this->datasis->dameval("SELECT COUNT(*) FROM nomina WHERE contrato='$codigo' ");
		if ($pers == 0 and $nomina==0 ){
			//print_r($campos);
			$mSQL = "DELETE FROM noco WHERE codigo='$codigo'";
			$this->db->simple_query($mSQL);
			$mSQL = "DELETE FROM itnoco WHERE codigo='$codigo'";
			$this->db->simple_query($mSQL);
			logusu('noco',"CONTRATOS $codigo NOMBRE  $nombre ELIMINADO");
			echo "{ success: true, message: 'Contrato Eliminado'}";
		} else {
			echo "{ success: false, message: 'Contrato con Movimiento!!'}";
		}
		//echo "{ success: false, message: 'Contrato con Movimiento!!'}";
	}


	function modifitnoco(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);

		$codigo = $data[0];
		
		// Borramos todo lo que tenga
		$mSQL = "DELETE FROM itnoco WHERE codigo='$codigo'";
		$this->db->simple_query($mSQL);
		$meco = "INSERT INTO itnoco (codigo, concepto, descrip, tipo, grupo) ";
		$meco .="SELECT '$codigo' codigo, concepto, descrip, tipo, grupo FROM conc WHERE concepto IN ( ";
		foreach( $data as $id=>$peo ){
			if($id > 0 ) {
				$meco .= "'$peo',";
			}			
		}
		$meco .= " 'XXXXXXXX' )";
		$this->db->simple_query($meco);
		echo "{ success: true, message: 'Contrato Modificado' }";
	}

	function copitab(){
		$desde = isset($_REQUEST['desde'])  ? $_REQUEST['desde']  : '';
		$hasta = isset($_REQUEST['hasta'])  ? $_REQUEST['hasta']  :  '';
		if ( $desde == '' or $hasta == '' ){
			echo "{ success: false, msg: 'Faltan contratos'}";
		} else if( $desde == $hasta  ){
			echo "{ success: false, msg: 'Contratos Iguales'}";
		} else {
			$query = "INSERT INTO notabu (contrato, ano, mes, dia, preaviso, vacacion, bonovaca, antiguedad, utilidades, prima) SELECT '$hasta' contrato, ano, mes, dia, preaviso, vacacion, bonovaca, antiguedad, utilidades, prima FROM notabu WHERE contrato='$desde'";
			$this->db->query($query);
			echo "{ success: true, msg: 'Tabla de Utilidades generada para $hasta'}";
		}
	}


//****************************************************************8
//
//
//
//****************************************************************8
	function nocoextjs(){
		$encabeza='<table width="100%" bgcolor="#2067B5"><tr><td align="left" width="100px"><img src="'.base_url().'assets/default/css/templete_01.jpg" width="120"></td><td align="center"><h1 style="font-size: 20px; color: rgb(255, 255, 255);" onclick="history.back()">CONTRATOS DE NOMINA</h1></td><td align="right" width="100px"><img src="'.base_url().'assets/default/images/cerrar.png" alt="Cerrar Ventana" title="Cerrar Ventana" onclick="parent.window.close()" width="25"></td></tr></table>';

		$mSQL = "SELECT codigo, CONCAT(codigo,' ',nombre) nombre, tipo FROM noco WHERE tipo<>'O' ORDER BY codigo";
		$contratos = $this->datasis->llenacombo($mSQL);

		$listados= $this->datasis->listados('noco');
		$otros=$this->datasis->otros('noco', 'nomina/noco');

		$urlajax = 'nomina/noco/';
		
		$script = "
<script type=\"text/javascript\">
var BASE_URL   = '".base_url()."';
var BASE_PATH  = '".base_url()."';
var BASE_ICONS = '".base_url()."assets/icons/';
var BASE_UX    = '".base_url()."assets/js/ext/ux';

Ext.Loader.setConfig({ enabled: true });
Ext.Loader.setPath('Ext.ux', BASE_UX);

var urlApp = '".base_url()."';
var codigoactual = '';

Ext.require([
	'Ext.grid.*',
	'Ext.ux.grid.FiltersFeature',
	'Ext.data.*',
	'Ext.util.*',
	'Ext.tree.*',
	'Ext.state.*',
	'Ext.form.*',
	'Ext.window.MessageBox',
	'Ext.tip.*',
	'Ext.ux.CheckColumn',
	'Ext.toolbar.Paging',
	'Ext.dd.*'
]);

var mxs = ((screen.availWidth/2)-400);
var mys = ((screen.availHeight/2)-300);

var tipos = new Ext.data.SimpleStore({
    fields: ['abre', 'todo'],
    data : [ ['Q','Quincenal'],['S','Semanal'],['B','Bisemanal'],['M','Mensual'],['O','Otros'] ]
});

//Column Model Contratos de Nomina
var NocoCol = 
	[
		{ header: 'id', dataIndex: 'id', width: 40, hidden: false }, 
		{ header: 'Codigo',   width:  60, sortable: true,  dataIndex: 'codigo', field: { type: 'textfield' }, filter: { type: 'string' }, editor: { allowBlank: false }}, 
		{ header: 'Tipo',     width:  40, sortable: false, dataIndex: 'tipo',
		field: { xtype: 'combobox', triggerAction: 'all', valueField:'abre', displayField:'todo', store: tipos, listClass: 'x-combo-list-small'},	filter: { type: 'string' }, editor: { allowBlank: false }}, 
		{ header: 'Nombre',   width: 250, sortable: true,  dataIndex: 'nombre',   field: { type: 'textfield' }, filter: { type: 'string' }, editor: { allowBlank: true }}, 
		{ header: 'Observa1', width: 250, sortable: true,  dataIndex: 'observa1', field: { type: 'textfield' }, filter: { type: 'string' }, editor: { allowBlank: true }},
		{ header: 'Observa2', width: 250, sortable: true,  dataIndex: 'observa2', field: { type: 'textfield' }, filter: { type: 'string' }, editor: { allowBlank: true }}
	];


//Column Model Detalle de NOCO
var ItNocoCol = 
	[
		{ header: 'id', dataIndex: 'id', width: 40, hidden: false }, 
		{ header: 'Concepto',    width:  60, sortable: true, dataIndex: 'concepto', field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Descripcion', width: 200, sortable: true, dataIndex: 'descrip',  field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'tipo',        width:  30, sortable: true, dataIndex: 'tipo',     field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Grupo',       width:  60, sortable: true, dataIndex: 'grupo',    field: { type: 'textfield' }, filter: { type: 'string' }}
	];

//Column Model Conceptos
var ConcCol = 
	[
		{ header: 'id', dataIndex: 'id', width: 40, hidden: false }, 
		{ header: 'Concepto',      width:  60, sortable: true, dataIndex: 'concepto', field:  { type: 'textfield' }, filter: { type: 'string'  } }, 
		{ header: 'Descripcion',   width: 200, sortable: true, dataIndex: 'descrip',  field:  { type: 'textfield' }, filter: { type: 'string'  } }, 
		{ header: 'tipo',          width:  30, sortable: true, dataIndex: 'tipo',     field:  { type: 'textfield' }, filter: { type: 'string'  } },
		{ header: 'Grupo',         width:  60, sortable: true, dataIndex: 'grupo',    field:  { type: 'textfield' }, filter: { type: 'string'  } }
	];

Ext.onReady(function(){
	// Un poco de hackeo para asinar los textos...
	if (Ext.MessageBox) {
		var mb = Ext.MessageBox;
		mb.bottomTb.items.each(function(b) {
		b.setText(mb.buttonText[b.itemId]);
		});
	}  

	/////////////////////////////////////////////////
	// Define los data model
	// Contratos
	Ext.define('Noco', {
		extend: 'Ext.data.Model',
		fields: ['id', 'codigo','tipo','nombre','observa1','observa2'],
		proxy: {
			type: 'ajax',
			noCache: false,
			api: {
				read   : urlApp + 'nomina/noco/manoco',
				create : urlApp + 'nomina/noco/crearnoco',
				update : urlApp + 'nomina/noco/modificanoco' ,
				destroy: urlApp + 'nomina/noco/eliminarnoco',
				method: 'POST'
			},
			reader: {
				type: 'json',
				root: 'maestro'
			}
		}
	});

	Ext.define('ItNoco', {
		extend: 'Ext.data.Model',
		fields: [ 'id', 'codigo', 'concepto', 'descrip','tipo','grupo'],
		proxy: {
			type: 'ajax',
			noCache: false,
			api: {
				read   : urlApp + 'nomina/noco/itnoco',
				update : urlApp + 'nomina/noco/modifitnoco',
				create : urlApp + 'nomina/noco/modifitnoco',
				destroy: urlApp + 'nomina/noco/modifinoco',
				method: 'POST'
			},
			reader: {
				type: 'json',
				root: 'detalle'
			}
		}
	});

	Ext.define('conc', {
		extend: 'Ext.data.Model',
		fields: ['id', 'concepto', 'descrip','tipo','grupo'],
		proxy: {
			type: 'rest',
			url : urlApp + 'nomina/noco/conc',
			reader: {
				type: 'json',
				root: 'conceptos'
			}
		}
	});

	//////////////////////////////////////////////////////////
	// create the Data Store
	var storeNoco = Ext.create('Ext.data.Store', {
		model: 'Noco',
		autoLoad: false,
		autoSync: true,
		method: 'POST',
	});

	var storeItNoco = Ext.create('Ext.data.Store', {
		model: 'ItNoco',
		autoLoad: false,
		autoSync: false,
		method: 'POST'
	});

	var storeCont = Ext.create('Ext.data.ArrayStore', {
		fields: [{type: 'string', name: 'codigo'},{type: 'string', name: 'nombre'}],
		data: [".$contratos."]
	});


	var storeConc = Ext.create('Ext.data.Store', {
		model: 'conc'
	});

	var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
		clicksToMoveEditor: 1,
		autoCancel: false
	});

	//////////////////////////////////////////////////////////////////
	// create the grid and specify what field you want
	// to use for the editor at each column.
	var gridNoco = Ext.create('Ext.grid.Panel', {
		store: storeNoco,
		columns: NocoCol,
		width: '100%',
		height: '100%',
		title: 'Contratos Laborales',
		frame: true,
		features: [ { ftype: 'filters', encode: 'json', local: false } ],
		tbar: [
			{
				text: 'Agregar',
				iconCls: 'icon-add',
				handler : function() {
					rowEditing.cancelEdit();
					// Create a model instance
					var r = Ext.create('Noco', {id: 0, codigo: '', tipo: 'Q', nombre: '', observa1: '', observa2: '' });
					storeNoco.insert(0, r);
					rowEditing.startEdit(0, 0);
				}
			},{
				itemId: 'eliminar',
				text: 'Eliminar',
				iconCls: 'icon-delete',
				handler: function() {
					var sm = gridNoco.getSelectionModel();
					rowEditing.cancelEdit();
					storeNoco.remove(sm.getSelection());
					if ( storeNoco.getCount() > 0) {
						sm.select(0);
					};
				},
				disabled: true
			}
		],
		plugins: [rowEditing],
			listeners: {
				'selectionchange': function(view, records) {
					if ( records[0] ){
						storeItNoco.load({ params: { codigo: records[0].data.codigo }});
						storeConc.load({ params: { codigo: records[0].data.codigo }});
						gridNoco.down('#eliminar').setDisabled(!records.length);
						codigoactual = records[0].data.codigo;
					}
				}
			}
	});

	// Create Grid 
	var gridItNoco = Ext.create('Ext.grid.Panel', {
		alias: 'widget.witnoco',
		store: storeItNoco,
		//margins: '0 2 0 0',
		//stripeRows : true,
		columns: ItNocoCol,
		width: '100%',
		height: '100%',
		title: 'Conceptos del Contrato',
		iconCls: 'icon-grid',
		frame: true,
		viewConfig: {
			plugins: {
				ptype: 'gridviewdragdrop',
				dragGroup: 'witnocoDDGroup',
				dropGroup: 'wconcDDGroup'
			}
		}
	});

	// Create Grid 
	var gridConc = Ext.create('Ext.grid.Panel', {
		store: storeConc,
		alias: 'widget.wiconc',
		columns: ConcCol,
		width: '100%',
		height: '100%',
		title: 'Conceptos disponibles',
		frame: true,
		iconCls: 'icon-grid',
		multiSelect: true,
		//margins: '0 2 0 0',
		stripeRows : true,
		viewConfig: {
			plugins: {
				ptype: 'gridviewdragdrop',
				dragGroup: 'wconcDDGroup',
				dropGroup: 'witnocoDDGroup'
			}
		}
	});

	var boton = Ext.create('Ext.Button',
		{
		text: 'Guardar',
		autoWidth: false,
		width: 85,
		scale: 'large',
		handler: function() {
			var resultado = [];
			resultado.push(codigoactual);
			if (storeItNoco.count() > 0 ){
				storeItNoco.each( function(registro){
					resultado.push(registro.get('concepto'));
				});
				Ext.Ajax.request({
					url   : urlApp + 'nomina/noco/modifitnoco',
					params: Ext.encode(resultado) 
				});				
			}
			//alert('resultado '+resultado);
		}
	});

//////************ MENU DE ADICIONALES /////////////////
".$listados."

	var mtabulador = Ext.create('Ext.form.Panel', {
		title: 'Copiar Tabulador',
		closable: true,
		alias:  'widget.mtabulador',
		height: 190,
		width: 360,
		floating: true,
		layout: 'anchor',
		frame: true, 
		modal: true,
		url: '".base_url().$urlajax."copitab',
		items:[{
			html: '<p style=\'background-color:#DFE9F6;text-align:center;\'>Copia el tabulador desde Otro Contrato<br></p>'
			},{
			xtype:'fieldset',
			columnWidth: 0.5,
			title: '',
			collapsible: false,
			defaults: { labelWidth:70, labelAlign: 'top' },
			layout: 'column',
			items :[
				{ xtype: 'combo', fieldLabel: 'Copiar desde el Contrato', name: 'desde', width:320, displayField: 'nombre', valueField: 'codigo', store: storeCont, id: 'cdesde' },
				{ xtype: 'combo', fieldLabel: 'Hasta el Contrato',        name: 'hasta', width:320, displayField: 'nombre', valueField: 'codigo', store: storeCont, id: 'chasta' }
			]
		}],
		buttons:[
			{text: 'Cerrar',
			iconCls: 'icon-cross',
			handler: function(){
				var mcontrato1 = Ext.getCmp('contrato1');
				mtabulador.hide();
			}
			},
			{text: 'Aplicar',
			iconCls: 'icon-accept',
			handler: function(){
				var form = this.up('form').getForm();
				form.submit({
					success: function(form, action){
						Ext.Msg.alert('Exito',action.result.msg);
						mtabulador.hide();
					},
					failure: function(form, action){
						Ext.Msg.alert('Error',action.result.msg);
					}
				})
			}
			},
			
		],
	});


//////************ FIN DE ADICIONALES /////////////////

	Ext.create('Ext.Viewport', {
		layout: {type: 'border',padding: 5},
		defaults: { split: true	},
		items: [
			{
				region: 'north',
				//collapsible: true,
				preventHeader: true,
				//title: 'North',
				//split: true,
				height: 40,
				minHeight: 40,
				html: '".$encabeza."'
			},{
				region:'west',
				width:200,
				border:false,
				autoScroll:true,
				title:'Lista de Opciones',
				collapsible:true,
				split:true,
				collapseMode:'mini',
				layoutConfig:{animate:true},
				layout: 'accordion',
				items: [
					{
						title:'Imprimir',
						defaults:{border:false},
						layout: 'fit',
						items:[{
							name: 'imprimir',
							id: 'imprimir',
							preventHeader: true,
							border:false,
							html: 'Para imprimir seleccione el Contrato '
						}]
					},{
						title:'Listados',
						border:false,
						layout: 'fit',
						items: gridListado
					},{
						title:'Otras Funciones',
						border:false,
						layout: 'fit',
						html: '".$otros."'
					}
				]
			},{
				region: 'center',
				layout: 'border',
				border: false,
				items: [ 
					{
						region: 'center',
						id:'areaNoco',
						cmargins: '0 0 0 0',
						collapsible: false,
						autoScroll:true,
						xtype: 'panel',
						items: gridNoco
					}
				]
			},{
				region: 'south',
				preventHeader: true,
				height: 300,
				layout: {type: 'border', padding: 5},
				items: [
					{
						preventHeader: true,
						region: 'center',
						xtype: 'panel',
						items: [
							{ html: '<p>Arrastre los conceptos de las grillas y luego presione el boton guardar</p>'},
							boton
						]
					},
					{
						preventHeader: true,
						id:'areaConc',
						width: '450%',
						region: 'east',
						split: true,
						xtype: 'panel',
						items: gridConc
					},
					{
						preventHeader: true,
						region: 'west',
						id:'areaItNoco',
						width: '450%',
						split: true,
						xtype: 'panel',
						items: gridItNoco
					}
				]
			}
		]
	});

	storeNoco.load();
	storeItNoco.load();
	storeConc.load();
	Ext.get('tabulador').on('click', function(e){ mtabulador.show();});

});
</script>
";
		return $script;	
	}
*/
}
?>
