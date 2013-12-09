<?php
//cargos
class Carg extends Controller {
	var $mModulo='CARG';
	var $titp='CARGOS';
	var $tits='CARGOS';
	var $url ='nomina/carg/';

	function Carg(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('carg','id') ) {
			$this->db->simple_query('ALTER TABLE carg DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE carg ADD UNIQUE INDEX cargo (cargo)');
			$this->db->simple_query('ALTER TABLE carg ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->modintramenu( 530, 500, substr($this->url,0,-1) );
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
		window.open(\''.site_url('formatos/ver/CARG/').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
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
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('CARG', 'JQ');
		$param['otros']        = $this->datasis->otros('CARG', 'JQ');
		$param['temas']        = array('proteo','darkness','anexos1');
		$param['anexos']       = 'anexos1';
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

		$grid->addField('cargo');
		$grid->label('Cargo');
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


		$grid->addField('sueldo');
		$grid->label('Sueldo');
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


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 370, height:200, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 370, height:200, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
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
		$mWHERE = $grid->geneTopWhere('carg');

		$response   = $grid->getData('carg', array(array()), array(), false, $mWHERE, 'cargo' );
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
				$this->db->insert('carg', $data);
				logusu('CARG',"Registro ".$data['cargo']." AGREGADO");
				echo "Registro Agregado";
			} else 
				echo "Fallo Agregado";

		} elseif($oper == 'edit') {
			$cargo = $this->datasis->dameval("SELECT cargo FROM carg WHERE id=$id");
			$carnu = $this->input->post('cargo');
			$this->db->where('id', $id);
			$this->db->update('carg', $data);
			$this->db->simple_query("UPDATE pers SET cargo=? WHERE cargo=?",array($carnu, $cargo));
			logusu('CARG',"Registro ".$data['cargo']." MODIFICADO ");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			$cargo = $this->datasis->dameval("SELECT cargo FROM carg WHERE id=$id");
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE cargo=".$this->db->escape($cargo));
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene personal Asociado ";
			} else {
				$this->db->simple_query("DELETE FROM carg WHERE id=$id ");
				logusu('CARG',"Registro $cargo ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}
}
?>