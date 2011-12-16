<?php
//tbanco
class Tban extends Controller {

	function tban() {
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index() {
		if ( !$this->datasis->iscampo('tban','id') ) {
			$this->db->simple_query('ALTER TABLE tban DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE tban ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE tban ADD UNIQUE INDEX cod_banc (cod_banc)');
		}
		$this->datasis->modulo_id(512,1);
		//redirect("finanzas/tban/filteredgrid");
		$this->tbanextjs();
	}

	function filteredgrid() {
		
		$this->rapyd->load("datafilter","datagrid");
		$filter = new DataFilter("Filtro de Tablas de Bancos", "tban");

		$filter->codbanc = new inputField("C&oacute;digo", "cod_banc");
		$filter->codbanc->size=5;

		$filter->banco = new inputField("Nombre de Banco", "nomb_banc");
		$filter->banco->size=30;

		$filter->buttons("reset","search");
		$filter->build();

		$uri = anchor('finanzas/tban/dataedit/show/<#cod_banc#>','<#cod_banc#>');

		$grid = new DataGrid("Lista de Tabla de Bancos");
		$grid->per_page = 12;

		$grid->column_orderby("C&oacute;digo",$uri,"cod_banc");
		$grid->column_orderby("Banco","nomb_banc","nomb_banc");
		$grid->column("Tipo","tipotra");
		$grid->column("Formaca","formaca");

		$grid->add("finanzas/tban/dataedit/create");
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = "<h1>Tabla de Bancos</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}

	function dataedit(){
		
		$this->rapyd->load("dataedit");
		
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
		
		$edit = new DataEdit("Tabla de Bancos", "tban");
		
		$edit->back_url = site_url("finanzas/tban/filteredgrid");
		$edit->script($script, "create");
		$edit->script($script, "modify");
		
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		
		$edit->codbanc = new inputField("C&oacute;digo", "cod_banc");
		$edit->codbanc->rule = "required|callback_chexiste";
		$edit->codbanc->mode="autohide";
		$edit->codbanc->size =5;
		$edit->codbanc->maxlength=3;
		
		$edit->nombre = new inputField("Nombre del Banco", "nomb_banc");
		$edit->nombre->size = 40;
		$edit->nombre->maxlength=30;
		$edit->nombre->rule = "strtoupper|required";
		
		$edit->url = new inputField("URL","url");
		$edit->url->size =35;
		$edit->url->maxlength=30;
		
		$edit->tipo = new dropdownField("Tipo de Transacci&oacute;n", "tipotra");
		$edit->tipo->option("DE","DE");
		$edit->tipo->option("NC","NC");
		$edit->tipo->style='width:80px';
		
		$edit->formaca = new dropdownField("Forma de Carga", "formaca");
		$edit->formaca->option("BRUTA","BRUTA");
		$edit->formaca->option("NETA","NETA");
		$edit->formaca->option("NETO","NETO");
		$edit->formaca->style='width:90px';

		$edit->tcredito = new inputField("Comison T.Credito%","comitc");
		$edit->tcredito->size =8;
		$edit->tcredito->maxlength=6;
		$edit->tcredito->css_class='inputnum';
		$edit->tcredito->rule='numeric';

		$edit->tdebito = new inputField("Comision T.Debito%","comitd");
		$edit->tdebito->size =8;
		$edit->tdebito->maxlength=6;
		$edit->tdebito->css_class='inputnum';
		$edit->tdebito->rule='numeric';

		$edit->retencion = new inputField("Retencion ISLR%","impuesto");
		$edit->retencion->size =8;
		$edit->retencion->maxlength=6;
		$edit->retencion->css_class='inputnum';
		$edit->retencion->rule='numeric';
		
		$edit->idb = new inputField("Debito Bancario%","debito");
		$edit->idb->size =8;
		$edit->idb->maxlength=6;
		$edit->idb->css_class='inputnum';
		$edit->idb->rule='numeric';

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$data['content'] = str_replace("120px;", "160px;background-color: #EFEFEF", $edit->output);
		$data['title']   = "<h1>Tabla de Bancos</h1>";        
		$data["head"]    = script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);  
	}

	function _post_insert($do){
		$codigo=$do->get('cod_banc');
		$nombre=$do->get('nomb_banc');
		logusu('tban',"BANCO $codigo NOMBRE  $nombre CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('cod_banc');
		$nombre=$do->get('nomb_banc');
		logusu('tban',"BANCO $codigo NOMBRE  $nombre  MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('cod_banc');
		$nombre=$do->get('nomb_banc');
		logusu('tban',"BANCO $codigo NOMBRE  $nombre  ELIMINADO ");
	}

	function chexiste($codigo){
		$codigo=$this->input->post('cod_banc');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM tban WHERE cod_banc='$codigo'");
		if ($chek > 0){
			$banco=$this->datasis->dameval("SELECT nomb_banc FROM tban WHERE cod_banc='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el banco $banco");
			return FALSE;
		}else {
		return TRUE;
		}
	}

	function grid(){
		$start   = isset($_REQUEST['start'])  ? $_REQUEST['start']   :  0;
		$limit   = isset($_REQUEST['limit'])  ? $_REQUEST['limit']   : 50;
		$sort    = isset($_REQUEST['sort'])   ? $_REQUEST['sort']    : '[{"property":"cod_banc","direction":"ASC"}]';
		$filters = isset($_REQUEST['filter']) ? $_REQUEST['filter']  : null;
		
		$where = $this->datasis->extjsfiltro($filters);
	
		$this->db->_protect_identifiers=false;
		$this->db->select('*');
		$this->db->from('tban');
		if (strlen($where)>1) $this->db->where($where, NULL, FALSE); 

		if (strlen($where)>1) $this->db->where($where, NULL, FALSE);
		$sort = json_decode($sort, true);
		if ( count($sort) == 0 ) $this->db->order_by( 'cod_banc', 'asc' );
		
		for ( $i=0; $i<count($sort); $i++ ) {
			$this->db->order_by($sort[$i]['property'],$sort[$i]['direction']);
		}

		$this->db->limit($limit, $start);
		$query = $this->db->get();
		$results = $this->db->count_all('tban');

		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data", results:'. $results.', data:'.json_encode($arr).'}';
	}


	function crear(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos   = $data['data'];
		$cod_banc = $campos['cod_banc'];

		if ( !empty($cod_banc) ) {
			unset($campos['id']);
			// Revisa si existe ya ese contrato
			if ($this->datasis->dameval("SELECT COUNT(*) FROM tban WHERE cod_banc='$cod_banc'") == 0)
			{
				$mSQL = $this->db->insert_string("tban", $campos );
				$this->db->simple_query($mSQL);
				logusu('tban',"TABLA DE BANCO $cod_banc CREADO");
				echo "{ success: true, message: 'Tabla de Banco Agregado'}";
			} else {
				echo "{ success: false, message: 'Ya existe un banco con ese Codigo ($cod_banc)!!'}";
			}
			
		} else {
			echo "{ success: false, message: 'Codigo vacio ($cod_banc)!!'}";
		}
	}

	function modificar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$cod_banc = $campos['cod_banc'];
		unset($campos['cod_banc']);
		unset($campos['id']);

		$mSQL = $this->db->update_string("tban", $campos,"id='".$data['data']['id']."'" );
		$this->db->simple_query($mSQL);
		logusu('tban',"TABLA DE BANCOS $cod_banc ID ".$data['data']['id']." MODIFICADO");
		echo "{ success: true, message: 'Tabla de Banco Modificado -> ".$data['data']['cod_banc']."'}";
	}

	function eliminar(){
		$js= file_get_contents('php://input');
		$data= json_decode($js,true);
		$campos = $data['data'];

		$cod_banc = $campos['cod_banc'];
		$chek =  $this->datasis->dameval("SELECT COUNT(*) FROM banc WHERE tbanco='$cod_banc'");

		if ($chek > 0){
			echo "{ success: false, message: 'Tabla de Banco no puede ser Borrado'}";
		} else {
			$this->db->simple_query("DELETE FROM tban WHERE cod_banc='$cod_banc'");
			logusu('tban',"TABLA DE BANCO $cod_banc ELIMINADO");
			echo "{ success: true, message: 'Tabla de banco Eliminado'}";
		}
	}


//0414 376 0149 juan picapiedras

//****************************************************************8
//
//
//
//****************************************************************8
	function tbanextjs(){
		$encabeza='TABLA DE BANCOS';
		$listados= $this->datasis->listados('tban');
		$otros=$this->datasis->otros('tban', 'finanzas/tban');

		$urlajax = 'finanzas/tban/';
		$variables = "var mcuenta = ''";
		$funciones = "";
		$valida = "";
		
		//{ header: 'id',          width:  30, sortable: true, dataIndex: 'id' }, 
		$columnas = "
		{ header: 'Codigo',      width:  50, sortable: true, dataIndex: 'cod_banc',  field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'Nombre',      width: 200, sortable: true, dataIndex: 'nomb_banc', field: { type: 'textfield' }, filter: { type: 'string' } }, 
		{ header: 'Tipo',        width:  40, sortable: true, dataIndex: 'tipotra',   field: { type: 'textfield' }, filter: { type: 'string' } },
		{ header: 'Deposito',    width:  60, sortable: true, dataIndex: 'formaca',   field: { type: 'textfield' }, filter: { type: 'string' } },
		{ header: 'Debito',      width:  80, sortable: true, dataIndex: 'debito',    field: { type: 'numeroc'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'Comision TD', width:  80, sortable: true, dataIndex: 'comitd',    field: { type: 'numeroc'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'Comision TC', width:  80, sortable: true, dataIndex: 'comitc',    field: { type: 'numeroc'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'I.S.L.R',     width:  80, sortable: true, dataIndex: 'impuesto',  field: { type: 'numeroc'   }, filter: { type: 'numeric' }, align: 'right',renderer : Ext.util.Format.numberRenderer('0,000.00') },
		{ header: 'Url',         width: 150, sortable: true, dataIndex: 'url',       field: { type: 'textfield' }, filter: { type: 'string' } }
	";

		$campos = "'id', 'cod_banc', 'nomb_banc', 'url', 'debito', 'comitc', 'comitd', 'impuesto', 'tipotra', 'formaca'";

		$camposforma = "
							{
							frame: false,
							border: false,
							labelAlign: 'right',
							defaults: { xtype:'fieldset', labelWidth:70 },
							style:'padding:4px',
							items: [
									{ xtype: 'textfield', fieldLabel: 'Codigo', name: 'cod_banc',  allowBlank: false, width: 120, id: 'cod_banc' },
									{ xtype: 'textfield', fieldLabel: 'Nombre', name: 'nomb_banc', allowBlank: false, width: 400, },
									{ xtype: 'combo',     fieldLabel: 'Tipo',   name: 'tipotra',   store: [['NC','Nota Credito'],['DE','Deposito']], width: 180 }
								]
							},{
								frame: false,
								border: false,
								labelAlign: 'right',
								defaults: {xtype:'fieldset'  },
								style:'padding:4px',
								layout: 'column',
								items: [
									{ xtype: 'numberfield', fieldLabel: 'Debito Bancario',  name: 'debito',   width:160, hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00'), labelWidth: 100  },
									{ xtype: 'numberfield', fieldLabel: 'Comision TD',      name: 'comitd',   width:180, hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00'), labelWidth: 120  },
									{ xtype: 'numberfield', fieldLabel: 'I.S.L.R',          name: 'impuesto', width:160, hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00'), labelWidth: 100  },
									{ xtype: 'numberfield', fieldLabel: 'Comision TC',      name: 'comitc',   width:180, hideTrigger: true, fieldStyle: 'text-align: right',  renderer : Ext.util.Format.numberRenderer('0,000.00'), labelWidth: 120  },
								]
							}
		";

		$titulow = 'Tabla de Bancos';

		$dockedItems = "
				{ iconCls: 'icon-reset', itemId: 'close', text: 'Cerrar',   scope: this, handler: this.onClose },
				{ iconCls: 'icon-save',  itemId: 'save',  text: 'Guardar',  disabled: false, scope: this, handler: this.onSave }
		";

		$winwidget = "
				closable: false,
				closeAction: 'destroy',
				width: 450,
				height: 280,
				resizable: false,
				modal: true,
				items: [writeForm],
				listeners: {
					beforeshow: function() {
						var form = this.down('writerform').getForm();
						this.activeRecord = registro;
						
						if (registro) {
							form.findField('cod_banc').setReadOnly(true);
							form.loadRecord(registro);
						} else {
							form.findField('cod_banc').setReadOnly(false);
							mcuenta  = '';
						}
					}
				}
";

		$stores = "";

		$features = "features: [ filters],";
		$filtros = "var filters = { ftype: 'filters', encode: 'json', local: false }; ";


		$data['listados']    = $listados;
		$data['otros']       = $otros;
		$data['encabeza']    = $encabeza;
		$data['urlajax']     = $urlajax;
		$data['variables']   = $variables;
		$data['funciones']   = $funciones;
		$data['valida']      = $valida;
		$data['columnas']    = $columnas;
		$data['campos']      = $campos;
		$data['stores']      = $stores;
		$data['camposforma'] = $camposforma;
		$data['titulow']     = $titulow;
		//$data['dockedItems'] = $dockedItems;
		$data['winwidget']   = $winwidget;
		$data['features']    = $features;
		$data['filtros']     = $filtros;
		
		$data['title']  = heading('Tabla de Bancos');
		$this->load->view('extjs/extjsven',$data);
		
	}



}
?>