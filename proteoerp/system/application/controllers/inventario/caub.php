<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
class Caub extends validaciones {
	var $data_type = null;
	var $data = null;
	var $titp='Almacenes';
	var $tits='Almacenes';
	var $url ='inventario/caub/';

	function caub(){
		parent::Controller();

		$this->load->helper('url');
		$this->load->helper('text');
		$this->datasis->modulo_id(307,1);
		$this->load->library("rapyd");
		$this->load->library('jqdatagrid');
	}

	function index(){
		$this->instalar();
		redirect('inventario/caub/jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
 	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		$funciones = 'jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid({ondblClickRow: function(id){ alert("id="+id); }});';

		$param['listados'] = $this->datasis->listados('CAUB', 'JQ');
		//$param['otros']    = $this->datasis->otros('CAUB', 'JQ');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		//Botones Panel Izq
		$WestPanel = $grid->deploywestp();

		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'));

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel']   = $EastPanel;
		//$param['LayoutStyle'] = $LayoutStyle;
		$param['SouthPanel']  = $SouthPanel;
		$param['funciones']   = $funciones;
		//$param['bodyscript']  = $bodyscript;
		$param['temas']       = array('proteo','darkness','anexos1');
		//$param['tabs']        = false;
		$param['encabeza']    = $this->titp;

		$this->load->view('jqgrid/crud2',$param);
	}

	function defgrid( $deployed = false ){
		$i = 1;
		$link  = site_url('ajax/buscacpla');

		$grid  = new $this->jqdatagrid;

		$grid->addField('ubica');
		$grid->label('C&oacute;digo');
		$grid->params(array(
				'width'       => 60,
				'editable'    => 'true',
				'edittype'    => "'text'",
				'search'      => 'false',
				'editoptions' => '{ size:5, maxlength: 4 }'
			)
		);

		$grid->addField('ubides');
		$grid->label('Nombre');
		$grid->params(array(
				'width'       => 180,
				'editable'    => 'true',
				'edittype'    => "'text'",
				'search'      => 'false',
				'editoptions' => '{ size:30, maxlength: 50 }'
			)
		);

		$grid->addField('sucursal');
		$grid->label('Sucursal');
		$grid->params(array(
				'width'         => 100,
				'editable'      => 'true',
				'edittype'      => "'select'",
				'editoptions'   => '{ dataUrl: "'.base_url().'ajax/ddsucu"}',
				'stype'         => "'select'",
				'searchoptions' => '{ dataUrl: "'.base_url().'ajax/ddsucu", sopt: ["eq", "ne"]}',
				'search'        => 'false'
			)
		);

		$grid->addField('gasto');
		$grid->label('Gasto');
		$grid->params(array(
				'align'       => "'center'",
				'width'       => 40,
				'editable'    => 'true',
				'search'      => 'false',
				'edittype'    => "'select'",
				'editoptions' => '{value: {"N":"No","S":"Si"} }'
			)
		);

		$grid->addField('invfis');
		$grid->label('Inv.F');
		$grid->params(array(
				'align'       => "'center'",
				'width'       => 40,
				'editable'    => 'true',
				'edittype'    => "'select'",
				'search'      => 'false',
				'editoptions' => '{value: {"N":"No", "S":"Si"} }'
			)
		);

		$grid->addField('tipo');
		$grid->label('Disp. Ventas');
		$grid->params(array(
				'align'       => "'center'",
				'width'       => 40,
				'editable'    => 'true',
				'edittype'    => "'select'",
				'search'      => 'false',
				'editoptions' => '{value: {"S":"Disponible", "N":"No Disponible"} }'
			)
		);

		$grid->addField('url');
		$grid->label('URL');
		$grid->params(array(
				'width'       => 200,
				'editable'    => 'true',
				'edittype'    => "'text'",
				'search'      => 'false',
				'editoptions' => '{ size:30, maxlength: 50 }'
			)
		);

		$grid->addField('odbc');
		$grid->label('ODBC');
		$grid->params(array(
				'width'       => 200,
				'editable'    => 'true',
				'edittype'    => "'text'",
				'search'      => 'false',
				'editoptions' => '{ size:30, maxlength: 50 }'
			)
		);

		$grid->addField('odbc');
		$grid->label('ODBC');
		$grid->params(array(
				'width'       => 200,
				'editable'    => 'true',
				'edittype'    => "'text'",
				'search'      => 'false',
				'editoptions' => '{ size:30, maxlength: 50 }'
			)
		);

		$grid->addField('warehouse');
		$grid->label('Warehouse');
		$grid->params(array(
				'width'       => 200,
				'editable'    => 'true',
				'edittype'    => "'text'",
				'search'      => 'false',
				'editoptions' => '{ size:30, maxlength: 50 }'
			)
		);

		$grid->addField('cu_cost');
		$grid->label('Cta.Costo');
		$grid->params(array(
				'width'       => 70,
				'frozen'      => 'true',
				'editable'    => 'true',
				'edittype'    => "'text'",
				'editoptions' => '{'.$grid->autocomplete($link, 'cu_cost','cucucu','<div id=\"cucucu\"><b>"+ui.item.descrip+"</b></div>').'}',
				'search'      => 'false'
			)
		);

		$grid->addField('cu_caja');
		$grid->label('Cta.Caja');
		$grid->params(array(
				'width'       => 70,
				'frozen'      => 'true',
				'editable'    => 'true',
				'edittype'    => "'text'",
				'editoptions' => '{'.$grid->autocomplete($link, 'cu_caja','cacaca','<div id=\"cacaca\"><b>"+ui.item.descrip+"</b></div>').'}',
				'search'      => 'false'
			)
		);

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
				'hidden'   => 'true',
				'align'    => "'center'",
				'frozen'   => 'true',
				'width'    => 50,
				'editable' => 'false',
				'search'   => 'false'
			)
		);

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('310');
		$grid->setTitle('Almacenes');
		$grid->setfilterToolbar(false);
		//$grid->setToolbar('true, "top"');
		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:350, closeOnEscape: true, top: 50, left:20, recreateForm:true ');
		$grid->setFormOptionsA('closeAfterAdd: true, mtype: "POST", width: 520, height:350, closeOnEscape: true, top: 50, left:20, recreateForm:true ');


		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(false);
		$grid->setRowNum(30);

		$grid->setShrinkToFit('false');

		#export buttons
		//$grid->setPdf(true,array('title' => 'Test pdf'));

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
	* Get data result as json
	*/
	function getData(){
		$grid       = $this->jqdatagrid;
		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('bcaj');
		$response   = $grid->getData('caub', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Put information
	*/
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$codigo = $this->input->post('ubica');

		$data = $_POST;

		unset($data['oper']);
		unset($data['id']);

		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('caub', $data);
				logusu('caub',"Almacen $codigo CREADO");
			}
			echo '';
			return;

		} elseif($oper == 'edit') {
			unset($data['ubica']);
			$this->db->where('id', $id);
			$this->db->update('caub', $data);
			logusu('caub',"Almacen $codigo MODIFICADO");
			return;

		} elseif($oper == 'del') {
			$check =  $this->datasis->dameval("SELECT COUNT(*) FROM itsinv WHERE alma='$codigo' AND existen>0");
			if ($check > 0){
				echo " El almacen no fuede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM caub WHERE id=$id ");
				logusu('caub',"Almacen $id ELIMINADO");
				echo "Almacen Eliminado";
			}

		};
	}

	function forma(){
		$salida = '
		<p class="validateTips">Todos los Campos son Necesarios.</p>
		<form>
		<fieldset>
			<label for="name">Nombre</label>
			<input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all" value="Meco" />
			<label for="email">Email</label>
			<input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all" />
			<label for="password">Password</label>
			<input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" />
		</fieldset>
		</form>';

		echo $salida;

	}

	// Set the function for a successful form submission
	function onSubmit($formValues) {
		$formValues = $formValues->contactFormPage->contactFormSection;

		if(!empty($formValues->name->middleInitial)) {
			$name = $formValues->name->firstName.' '.$formValues->name->middleInitial.' '.$formValues->name->lastName;
		} else {
			$name = $formValues->name->firstName.' '.$formValues->name->lastName;
		}

		$message['failureHtml'] = '<p style="margin-bottom: .5em;">Thanks for Contacting Us</p><p>Your message has been successfully sent.</p>';

		return $message;
	}

	function instalar(){
		$campos=$this->db->list_fields('caub');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE caub DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE caub ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE caub ADD UNIQUE INDEX ubica (ubica)');
		}

		if(!in_array('url',$campos)){
			$this->db->simple_query('ALTER TABLE caub ADD COLUMN url VARCHAR(100)');
		}

		if(!in_array('odbc',$campos)){
			$this->db->simple_query('ALTER TABLE caub ADD COLUMN odbc VARCHAR(100)');
		}

		if(!in_array('tipo',$campos)){
			$this->db->simple_query('ALTER TABLE caub ADD COLUMN tipo CHAR(1)');
		}

		$this->db->simple_query('UPDATE caub SET tipo="S" WHERE tipo="" OR tipo IS NULL ');
		$this->db->simple_query('UPDATE caub SET tipo="N" WHERE gasto="S" OR invfis = "S" ');

		if(!in_array('warehouse',$campos)){
			$this->db->simple_query('ALTER TABLE `caub` ADD COLUMN `warehouse` VARCHAR(100) NULL DEFAULT NULL COMMENT "Codigo de warehouse"');
		}

		$c=$this->datasis->dameval('SELECT COUNT(*) FROM caub WHERE ubica="AJUS"');
		if(!($c>0)) $this->db->simple_query('INSERT IGNORE INTO caub (ubica,ubides,gasto,invfis) VALUES ("AJUS","AJUSTES","S","N")');
		$this->db->simple_query('UPDATE caub SET ubides="AJUSTES", gasto="S",invfis="N" WHERE  ubica="AJUS" ');

		$c=$this->datasis->dameval("SELECT COUNT(*) FROM caub WHERE ubica='INFI'");
		if(!($c>0)) $this->db->simple_query("INSERT IGNORE INTO caub (ubica,ubides,gasto,invfis) VALUES ('INFI','INVENTARIO FISICO','S','S')");
		$this->db->simple_query("UPDATE caub SET ubides='INVENTARIO FISICO', gasto='S',invfis='S' WHERE ubica='INFI'");

	}
}
