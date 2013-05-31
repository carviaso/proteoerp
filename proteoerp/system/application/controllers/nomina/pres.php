<?php
//adelantoprestamos
class Pres extends Controller {
	var $mModulo='PRES';
	var $titp='Descuento de Prestamos por Nomina';
	var $tits='Descuento de Prestamos por Nomina';
	var $url ='nomina/pres/';

	function Pres(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('pres','id') ) {
			$this->db->simple_query('ALTER TABLE pres DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE pres ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE pres ADD UNIQUE INDEX cliente (cod_cli, tipo_doc, numero )');
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

		$bodyscript = '<script type="text/javascript">
		jQuery("#a1").click( function(){
			var id = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $param['grids'][0]['gridname'].'").jqGrid(\'getRowData\',id);
				window.open(\''.base_url().'formatos/ver/PRES/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
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
		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('PRES', 'JQ');
		$param['otros']       = $this->datasis->otros('PRES', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
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

		$grid  = new $this->jqdatagrid;
		$link = site_url('ajax/buscapers');
		$despues ='
								$("input#nombre").val(ui.item.nombre);
								$("input#cod_cli").val(ui.item.enlace);
								_cargo = ui.item.enlace;';

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{'.$grid->autocomplete($link, 'codigo','aaaaaa','<div id=\"aaaaaa\"></div>',$despues,'\'#editmod\'+gridId1.substring(1)').'}', 
			'formoptions'   => '{ label:"Codigo del Trabajador" }',
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30, readonly: true }',
		));


		$grid->addField('cod_cli');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5, readonly: true }',
			'formoptions'   => '{ label:"Enlace Administrativo" }',
		));

		$link1 = site_url('ajax/buscasmovep');
		$despues1 ='
								$("input#tipo_doc").val(ui.item.tipo_doc);
								$("input#monto").val(ui.item.monto);
								';


		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, '.$grid->autocomplete($link1, 'codigo','aaaaaa','<div id=\"aaaaaa\"></div>',$despues1,'\'#editmod\'+gridId1.substring(1)').'}',
			'formoptions'   => '{ label:"Numero de Efecto" }',
		));

		$grid->addField('tipo_doc');
		$grid->label('Tipo_doc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 2 }',
			'formoptions'   => '{ label:"Tipo de Documento" }',
		));


		$grid->addField('monto');
		$grid->label('Monto');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{label:"Monto adeudado",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }',
			//'editoptions'   => '{ defaultValue:"'.date('Y-m-d').'"}'
		));


		$grid->addField('nroctas');
		$grid->label('Nro.Cuotas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ defaultValue:"1",size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); },
				dataEvents: [{
					type: "change", fn: function(e){
						var cuotas = Number($(e.target).val());
						var monto  = Number($("input#monto").val());
						var cuota  = 0;
						if ( cuotas==0) { cuotas=1; };
						cuota = monto/cuotas;
						$("input#cuota").val(cuota);
					}
				}]
	
			}',
			'formatter'     => "'number'",
			'formatoptions' => '{label:"Numero de Cuotas", decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('cuota');
		$grid->label('Cuota');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ defaultValue:"0", size:10, maxlength: 10, readonly: true, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{label:"Descuento por Nomina",decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('apartir');
		$grid->label('Apartir');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Inicio del descuento" }'
		));

		$grid->addField('cadano');
		$grid->label('Intervalo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"1":"Cada Nomina", "2":"Cas 2 Nominas"} }',
			'editrules'     => '{ required:true}',
		));

		$grid->addField('observ1');
		$grid->label('Observaciones');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			//'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 46 }',
		));

		$grid->addField('oberv2');
		$grid->label('Obervaciones');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			//'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 46 }',
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
			'formoptions'   => '{ label:"Fecha" }'
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

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:450, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:450, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
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

/*

#editmodnewapi_14591061.ui-widget.ui-widget-content.ui-corner-all.ui-jqdialog.jqmID2 2
#edithdnewapi_14591061.ui-jqdialog-titlebar.ui-widget-header.ui-corner-all.ui-helper-clearfix 3Modificar registro
#editcntnewapi_14591061.ui-jqdialog-content.ui-widget-content 4
 
 
 
*/


	/**
	* Busca la data en el Servidor por json
	*/
	function getdata()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('pres');

		$response   = $grid->getData('pres', array(array()), array(), false, $mWHERE );
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

		// Valida
		if($oper != 'del'){
			$codigo   = $this->input->post('codigo');
			$cod_cli  = $this->input->post('cod_cli');
			$numero   = $this->input->post('numero');
			$tipo_doc = $this->input->post('tipo_doc');
			$check = $this->datasis->dameval('SELECT count(*) FROM pers WHERE codigo='.$this->db->escape($codigo));
			if ( $check == 0 ){
				echo "No se encontro esa persona en los registros ".$codigo;
				return;
			}
			$check = $this->datasis->dameval('SELECT count(*) FROM scli WHERE cliente='.$this->db->escape($cod_cli));
			if ( $check == 0 ){
				echo "No se encontro el enlace Administrativo ".$cod_cli;
				return;
			}
			$check = $this->datasis->dameval('SELECT count(*) FROM smov WHERE cod_cli='.$this->db->escape($cod_cli)." AND tipo_doc='$tipo_doc' AND numero='$numero' AND monto>abonos " );
			if ( $check == 0 ){
				echo "No se encontro la deuda a cobrar ".$tipo_doc.$numero;
				return;
			}
			$data['nombre']  = $this->datasis->dameval("SELECT CONCAT(TRIM(nombre),' ',TRIM(apellido)) nombre FROM pers WHERE codigo='$codigo'");
		}
		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				//Busca si ya existe
				$check = $this->datasis->dameval('SELECT count(*) FROM pres WHERE cod_cli='.$this->db->escape($cod_cli)." AND tipo_doc='$tipo_doc' AND numero='$numero' " );
				if ( $check == 0 ) {
					$this->db->insert('pres', $data);
					echo "Registro Agregado";
					logusu('PRES',"Registro  INCLUIDO");
				} else
				echo "Registro ya Agregado!!!";
				
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$this->db->where('id', $id);
			$this->db->update('pres', $data);
			logusu('PRES',"Registro ????? MODIFICADO");
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pres WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM pres WHERE id=$id ");
				logusu('PRES',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}


	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"fecha","direction":"ASC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;

		$where = $this->datasis->extjsfiltro($filters);
		$this->db->_protect_identifiers=false;
		$this->db->select('*');
		$this->db->from('pres');
		if (strlen($where)>1) $this->db->where($where, NULL, FALSE); 

		$sort = json_decode($sort, true);
		for ($i=0;$i<count($sort);$i++) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);
		$query = $this->db->get();

		$results = $this->db->count_all('pres');
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

	function crear() {
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];
		$codigo   = $data['data']['codigo'];
		$fecha    = $data['data']['fecha'];
		
		$campos['nombre']  = $this->datasis->dameval("SELECT CONCAT(TRIM(nombre),' ',TRIM(apellido)) nombre FROM pers WHERE codigo='$codigo'");
		
		unset($campos['id']);
		$mHay = $this->datasis->dameval("SELECT count(*) FROM pres WHERE codigo='$codigo' AND fecha='$fecha' ");
		if  ( $mHay > 0 ){
			echo "{ success: false, message: 'Ya existe un registro igual para ese trabajador $codigo fecha $fecha'}";
		} else {
			$mSQL = $this->db->insert_string("pres", $campos );
			$this->db->simple_query($mSQL);
			logusu('pres',"PRESTAMO DE NOMINA $codigo/$fecha CREADO");
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
		logusu('pres',"PRESTAMO DE NOMINA ".$data['data']['id']." MODIFICADO");
		echo "{ success: true, message: 'Prestamo de nomina Modificado '}";
	}

	function eliminar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos= $data['data'];

		$departa = $data['data']['departa'];
		
		// VERIFICAR SI PUEDE
		$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pers WHERE depto='$departa'");

		if ($check > 0){
			echo "{ success: false, message: 'Prestamo de nomina, no puede ser Borrado'}";
		} else {
			$this->db->simple_query("DELETE FROM depa WHERE departa='$departa'");
			logusu('pres',"PRESTAMO DE NOMINA $departa ELIMINADO");
			echo "{ success: true, message: 'Prestamo de nomina Eliminado'}";
		}
	}



//****************************************************************
//
//
//
//****************************************************************
	function presextjs(){
		$encabeza='DESCUENTO DE PRESTAMOS POR NOMINA';
		$listados= $this->datasis->listados('pres');
		$otros=$this->datasis->otros('pres', 'pres');

		$urlajax = 'nomina/pres/';
		$variables = "var mcodigo = '';";

		$funciones = "";
		
		$valida = "
		{ type: 'length', field: 'codigo', min:  1 }
		";

		$columnas = "
		{ header: 'Codigo',      width:  60, sortable: true, dataIndex: 'codigo',   field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Nombre',      width: 220, sortable: true, dataIndex: 'nombre',   field: { type: 'textfield' }, filter: { type: 'string' }},
		{ header: 'Fecha',       width:  70, sortable: true, dataIndex: 'fecha',    field: { type: 'date'      }, filter: { type: 'date'   }},
		{ header: 'Cliente',     width:  60, sortable: true, dataIndex: 'cod_cli',  field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Tipo',        width:  40, sortable: true, dataIndex: 'tipo_doc', field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Numero',      width:  60, sortable: true, dataIndex: 'numero',   field: { type: 'textfield' }, filter: { type: 'string' }}, 
		{ header: 'Monto',       width: 120, sortable: true, dataIndex: 'monto',    field: { type: 'numeric'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'A partir',    width:  70, sortable: true, dataIndex: 'apartir',  field: { type: 'date'      }, filter: { type: 'date'   }},
		{ header: 'Cuota',       width: 120, sortable: true, dataIndex: 'cuota',    field: { type: 'numeric'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'Observacion', width: 220, sortable: true, dataIndex: 'observ1',  field: { type: 'textfield' }, filter: { type: 'string' }},
	";

		$campos = "'id', 'codigo', 'nombre', 'fecha','cod_cli', 'tipo_doc', 'numero', 'monto','apartir','cuota','nroctas','cadano','observ1', 'oberv2'";
		$filtros = "var filters = { ftype: 'filters',encode: 'json', local: false }; ";
		
		$camposforma = "
				{
						frame: false,
						border: false,
						labelAlign: 'right',
						defaults: { xtype:'fieldset', labelWidth:70 },
						style:'padding:4px',
						items:[	
							{
								xtype: 'combo',
								fieldLabel: 'Trabajador',
								name: 'codigo',
								mode: 'remote',
								hideTrigger: true,
								typeAhead: true,
								forceSelection: true,
								valueField: 'item',
								displayField: 'valor',
								store: persStore,
								width: 410,
								id: 'codigo',
								listeners: { select: function(combo, record, index){
									var sele   = combo.getValue();
									var i = 0;
									var msueldo = 0;
									for ( i=0; i < combo.store.count();i=i+1 ){
										if ( combo.store.getAt(i).get('item') == sele ){
											msueldo=combo.store.getAt(i).get('sueldo');
										}
									}
									Ext.getCmp('sueldoa').setValue(msueldo);
									Ext.getCmp('sueldo').setValue(msueldo);
									
									
								}}
							}
						]
				},{
						layout: 'column',
						frame: false,
						border: false,
						labelAlign: 'right',
						defaults: {xtype:'fieldset', labelWidth: 80  },
						style:'padding:4px',
						items: [
							{ xtype: 'datefield',   fieldLabel: 'Fecha',       name: 'fecha', width:180, format: 'd/m/Y', submitFormat: 'Y-m-d', labelWidth: 40 },
							{ xtype: 'textfield',   fieldLabel: 'Enlace Administrativo',     name: 'cod_cli',   width:230, allowBlank: true, labelWidth: 140 },
							{ xtype: 'textfield',   fieldLabel: 'Tipo',        name: 'tipo_doc',  width: 80, allowBlank: true, labelWidth: 40 },
							{ xtype: 'textfield',   fieldLabel: 'Numero',      name: 'numero',    width:150, allowBlank: true, labelWidth: 60 },
							{ xtype: 'numberfield', fieldLabel: 'Monto',       name: 'monto',     width:180, hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00'), id: 'monto', labelWidth: 60  },
							{ xtype: 'numberfield', fieldLabel: 'Cuota',       name: 'cuota',     width:140, hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00'), labelWidth: 40  },
							{ xtype: 'datefield',   fieldLabel: 'A partir de', name: 'apartir',   width:160, format: 'd/m/Y', submitFormat: 'Y-m-d', labelWidth: 70 },
							{ xtype: 'numberfield', fieldLabel: 'Frec.',  name: 'cadano',    width: 90, hideTrigger: false, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00'), labelWidth: 40  },
							{ xtype: 'textfield',   fieldLabel: 'Observacion', name: 'observ1',   width:410, allowBlank: true }
						]
				}
		";


		$stores = "
var persStore = new Ext.data.Store({
	fields: [ 'item', 'valor', 'sueldo'],
	autoLoad: false,
	autoSync: false,
	name: 'Pers',
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
				height: 260,
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
						} else {
							form.findField('codigo').setReadOnly(false);
							mcodigo  = '';
						}
					}
				}
";
		$features = "features: [ filters],";

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
		$data['features']    = $features;
		$data['filtros']     = $filtros;
		
		$data['title']  = heading('Departamentos de Nomina');
		$this->load->view('extjs/extjsven',$data);
	}
}

?>
