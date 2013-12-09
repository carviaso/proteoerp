<?php require_once(BASEPATH.'application/controllers/validaciones.php'); 
class Botr extends Controller {
	var $mModulo='BOTR';
	var $titp='Otros Concepto de Contabilidad';
	var $tits='Otros Concepto de Contabilidad';
	var $url ='finanzas/botr/';

	function Botr(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
		$this->datasis->modulo_nombre( 'BOTR', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('botr','id') ) {
			$this->db->simple_query('ALTER TABLE botr DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE botr ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE botr ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->modintramenu( 750, 470, 'finanzas/botr' );
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
		window.open(\''.site_url('formatos/ver/BOTR/').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
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
		$param['listados'] = $this->datasis->listados('BOTR', 'JQ');
		$param['otros']    = $this->datasis->otros('BOTR', 'JQ');
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
		$link  = site_url('ajax/buscacpla');

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
				'search'      => 'true',
				'editable'    => $editar,
				'width'       => 50,
				'edittype'    => "'text'",
				'editoptions' => '{ size:5, maxlength: 5 }'
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
				'search'        => 'true',
				'editable'      => $editar,
				'width'         => 250,
				'edittype'      => "'text'",
				'editoptions' => '{ size:30, maxlength: 30 }'
		));

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
				'editable'    => 'true',
				'edittype'    => "'select'",
				'search'      => 'false',
				'width'         => 50,
				'editoptions' => '{value: {"C":"Cliente", "P":"Proveedor", "O":"Otro"} }'
		));

		$grid->addField('clase');
		$grid->label('Clase');
		$grid->params(array(
				'editable'    => 'true',
				'edittype'    => "'select'",
				'width'         => 50,
				'search'      => 'false',
				'editoptions' => '{value: {"E":"Entrada", "S":"Salida", "N":"Ninguno"} }'
		));

		$grid->addField('cuenta');
		$grid->label('Cuenta');
		$grid->params(array(
				'frozen'      => 'true',
				'editable'    => 'true',
				'edittype'    => "'text'",
				'width'         => 80,
				'editoptions' => '{'.$grid->autocomplete($link, 'cuenta','cucucu','<div id=\"cucucu\"><b>"+ui.item.descrip+"</b></div>').'}',
				'search'      => 'false'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('250');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 450, height:250, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 450, height:250, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
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
		$mWHERE = $grid->geneTopWhere('botr');
		$response   = $grid->getData('botr', array(array()), array(), false, $mWHERE, 'codigo', 'asc' );
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
			// Busca si esta Repetido
			if ($this->datasis->dameval("SELECT COUNT(*) FROM botr WHERE codigo=".$this->db->escape($data['codigo'])) > 0){
				echo " Codigo Repetido!!!";
			} else {
				if( false == empty($data) ) {
					$this->db->insert('botr', $data);
					echo " Registro Agregado";
				}
			}

		} elseif($oper == 'edit') {
			$codigo = $this->datasis->dameval("SELECT codigo FROM botr WHERE id=$id");
			unset($data['codigo']);   // No cambia el Codigo
			$this->db->where('id', $id);
			$this->db->update('botr', $data);
			echo " Registro Modificado";

		} elseif($oper == 'del') {
			$codigo = $this->datasis->dameval("SELECT codigo FROM botr WHERE id=$id");
			$check  =  $this->datasis->dameval("SELECT count(*) FROM bmov   WHERE clipro='O' AND codcp=".$this->db->escape($codigo));
			$check +=  $this->datasis->dameval("SELECT count(*) FROM itotin WHERE codigo=".$this->db->escape($codigo));
			$check +=  $this->datasis->dameval("SELECT count(*) FROM smov   WHERE codigo=".$this->db->escape($codigo));
			$check +=  $this->datasis->dameval("SELECT count(*) FROM sprm   WHERE codigo=".$this->db->escape($codigo));
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento $codigo";
			} else {
				$this->db->simple_query("DELETE FROM botr WHERE id=$id ");
				logusu('botr',"Registro $codigo ELIMINADO");
				echo " Registro Eliminado";
			}
		};
	}

}
?>
