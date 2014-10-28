<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(APPPATH.'/controllers/crm/contenedor.php');

class Ordi extends Controller {

	var $error_string='';

	function Ordi(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('205',1);
	}

	function index(){
		redirect('import/ordi/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&acute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor',
			'where'   =>'tipo IN (3,4)');
		$boton=$this->datasis->modbus($modbus);

		$atts = array('width'=>'800','height'=> '600', 'scrollbars' => 'yes', 'status'=> 'yes','resizable'=> 'yes', 'screenx'=> '0','screeny'=> '0');

		$filter = new DataFilter('Filtro','ordi');

		$filter->numero = new inputField('N&uacute;mero','numero');
		$filter->numero->size=15;

		$filter->fecha = new dateonlyField('Fecha', 'fecha');
		$filter->fecha->size=12;

		$filter->proveed = new inputField('Proveedor', 'proveed');
		$filter->proveed->size=12;
		$filter->proveed->append($boton);

		$filter->status = new dropdownField('Estatus', 'status');
		$filter->status->option('' ,'Todas');
		$filter->status->option('A','No liquidadas');
		$filter->status->option('C','Liquidadas');
		$filter->status->style ="width:120px;";

		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('import/ordi/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|0</str_pad>');
		$uri2 = anchor_popup('formatos/verhtml/ORDI/<#numero#>','Ver HTML',$atts);

		$grid = new DataGrid('Lista');
		$grid->use_function('str_pad');
		$grid->order_by('numero','desc');
		$grid->per_page = 15;

		$grid->column_orderby('N&uacute;mero',$uri,'numero');
		$grid->column_orderby('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha','align=\'center\'');
		$grid->column_orderby('Proveedor'    ,'proveed','proveed');
		$grid->column_orderby('Nombre'       ,'nombre','nombre');
		$grid->column_orderby('Monto Fob.'   ,'<nformat><#montofob#></nformat>','montofob','align=\'right\'');
		$grid->column_orderby('Monto Total'  ,'<nformat><#montotot#></nformat>','montotot','align=\'right\'');
		$grid->column('Vista',$uri2,'align=\'center\'');

		$grid->add('import/ordi/dataedit/create','Agregar nuevo registro');
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = heading('Importaciones');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		$monedalocal='Bs';

		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descrip_<#i#>'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  => 'Buscar Producto en inventario',
			'script'  =>array('post_sinv_modbus("<#i#>")'));
		$btn=$this->datasis->p_modbus($modbus,'<#i#>');

		$sprv=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed','nombre'=>'nombre'),
			'titulo'  =>'Buscar Proveedor',
			'where'   =>'tipo IN (3,4)',
			'script'  =>array('post_sprv_modbus()'));
		$boton=$this->datasis->modbus($sprv);

		$aran=array(
			'tabla'   =>'aran',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n',
				'tarifa'=>'Tarifas'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codaran_<#i#>','tarifa'=>'arancel_<#i#>'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  =>'Buscar Aranceles',
			'script'  =>array('aranpresis(<#i#>)'));
		$aran=$this->datasis->p_modbus($aran,'<#i#>');

		$asprv=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'agente','nombre'=>'nomage'),
			'titulo'  =>'Buscar Proveedor',
			'script'  =>array('post_sprv_modbus()'));
		$aboton=$this->datasis->modbus($asprv,'agsprv');

		$script="
		function post_add_itstra(id){
			$('#cantidad_'+id).numeric(".");
			return true;
		}
		function formato(row) {
			return row[0] + "-" + row[1];
		}";

		$do = new DataObject('ordi');
		$do->rel_one_to_many('itordi', 'itordi', 'numero');

		$edit = new DataDetails('ordi', $do);

		$_status  = $do->get('status');
		$_control = $do->get('control');
		if($_status=='C'){
			$dbcontrol= $this->db->escape($_control);
			$scstcana = $this->datasis->dameval('SELECT COUNT(*) FROM scst WHERE tipo_doc<>\'XX\' AND control='.$dbcontrol);
			if(empty($scstcana)){
				$_id =  $do->get('id');
				$this->db->simple_query('UPDATE ordi SET status="A", control=NULL WHERE id='.$_id);
				$do->set('control','');
				$do->set('status','A');
			}
		}


		$edit->back_url = site_url('import/ordi/filteredgrid');
		$edit->set_rel_title('itstra','Producto <#o#>');
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'delete','_pre_delete');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->numero= new inputField('N&uacute;mero', 'numero');
		$edit->numero->mode='autohide';
		$edit->numero->size=10;
		//$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->dua= new inputField('Declaraci&oacute;n &uacute;nica de aduana', 'dua');
		$edit->dua->size=10;

		$edit->fecha = new  dateonlyField('Fecha de Factura','fecha');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->maxlength=8;
		$edit->fecha->size =10;

		/*$edit->status = new dropdownField('Estatus', 'status');
		$edit->status->option('A','Abierto');
		$edit->status->option('C','Cerrado');
		$edit->status->option('E','Eliminado');
		$edit->status->rule  = 'required';
		$edit->status->style = 'width:120px';*/

		$edit->proveed = new inputField('Proveedor', 'proveed');
		$edit->proveed->rule     ='trim|required|existesprv';
		$edit->proveed->maxlength=5;
		$edit->proveed->size     =7;
		$edit->proveed->append($boton);

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->rule     ='trim';
		$edit->nombre->maxlength=40;
		$edit->nombre->size     =40;
		$edit->nombre->type ='inputhidden';

		$edit->agente = new inputField('Agente aduanal', 'agente');
		$edit->agente->rule     ='trim|existesprv|required';
		$edit->agente->maxlength=5;
		$edit->agente->size     =7;
		$edit->agente->append($aboton);

		$edit->nomage = new inputField('Nombre de agente', 'nomage');
		$edit->nomage->rule     ='trim';
		$edit->nomage->maxlength=40;
		$edit->nomage->size     =40;
		$edit->nomage->type ='inputhidden';

		$arr=array(
			'montofob' =>'Total factura extranjera $',
			//'aranceles'=>'Suma del Impuesto Arancelario '.$monedalocal,
			);

		foreach($arr as $obj => $etiq){
			$edit->$obj = new inputField($etiq, $obj);
			$edit->$obj->rule     ='trim';
			$edit->$obj->maxlength=20;
			$edit->$obj->size     =10;
			$edit->$obj->css_class= 'inputnum';
			$edit->$obj->autocomplete= false;
			$edit->$obj->showformat  = 'decimal';
			$edit->$obj->type ='inputhidden';
		}

		$arr=array(
			'gastosi'  =>'Gastos Internacionales $',
			'montocif' =>'Monto FOB+gastos Internacionales $',
			'gastosn'  =>'Gastos Nacionales '.$monedalocal,
			'montotot' =>'Monto CIF + Gastos Nacionales '.$monedalocal,
			'montoiva' =>'Monto del iva '.$monedalocal);

		foreach($arr as $obj => $etiq){
			$edit->$obj = new inputField($etiq, $obj);
			$edit->$obj->rule     ='trim';
			$edit->$obj->maxlength=20;
			$edit->$obj->size     =10;
			$edit->$obj->css_class= 'inputnum';
			$edit->$obj->autocomplete= false;
			$edit->$obj->showformat  = 'decimal';
			$edit->$obj->when=array('show');
		}

		$edit->arribo = new dateonlyField('Fecha de Llegada', 'arribo');
		$edit->arribo->rule     ='chfecha';
		$edit->arribo->maxlength=8;
		$edit->arribo->size     =10;

		$edit->factura = new inputField('Nro. Factura', 'factura');
		$edit->factura->rule     ='trim';
		$edit->factura->maxlength=20;
		$edit->factura->size     =10;
		$edit->factura->autocomplete= false;

		$edit->cambioofi = new inputField('Cambio Oficial', 'cambioofi');
		$edit->cambioofi->css_class= 'inputnum';
		$edit->cambioofi->rule     ='trim|required';
		$edit->cambioofi->maxlength=17;
		$edit->cambioofi->size     =10;
		$edit->cambioofi->autocomplete= false;
		$edit->cambioofi->insertValue = 6.3;
		$edit->cambioofi->showformat  = 'decimal';

		$edit->cambioreal = new inputField('Cambio Real', 'cambioreal');
		$edit->cambioreal->css_class= 'inputnum';
		$edit->cambioreal->rule     ='trim|required';
		$edit->cambioreal->maxlength=17;
		$edit->cambioreal->size     =10;
		$edit->cambioreal->autocomplete= false;
		$edit->cambioreal->showformat  = 'decimal';

		$edit->peso = new inputField('Peso Total', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->rule      = 'trim';
		$edit->peso->maxlength = 12;
		$edit->peso->size      = 10;
		$edit->peso->showformat= 'decimal';
		$edit->peso->when=array('show');

		$edit->condicion = new textareaField('Condiciones', 'condicion');
		$edit->condicion->rule ='trim';
		$edit->condicion->cols = 37;
		$edit->condicion->rows = 3;

		$edit->estimadif = new inputField('Diferencia en estimaci&oacute;n', 'estimadif');
		$edit->estimadif->css_class = 'inputnum';
		$edit->estimadif->maxlength = 12;
		$edit->estimadif->size      = 10;
		$edit->estimadif->showformat= 'decimal';
		$edit->estimadif->when=array('show');

		//*********************
		//comienza el detalle
		//*********************
		$edit->codigo = new inputField('C&oacute;digo <#o#>','codigo_<#i#>');
		$edit->codigo->db_name  = 'codigo';
		$edit->codigo->rule     = 'trim|required|existesinv';
		$edit->codigo->rel_id   = 'itordi';
		$edit->codigo->maxlength= 15;
		$edit->codigo->size     = 10;
		$edit->codigo->append($btn);
		$edit->codigo->autocomplete= false;

		$edit->descrip = new inputField('Descripci&oacute;n <#o#>','descrip_<#i#>');
		$edit->descrip->db_name  ='descrip';
		$edit->descrip->rel_id   ='itordi';
		$edit->descrip->maxlength=35;
		$edit->descrip->size     =30;
		$edit->descrip->autocomplete= false;
		$edit->descrip->type ='inputhidden';

		$edit->cantidad = new inputField('Cantidad <#o#>','cantidad_<#i#>');
		$edit->cantidad->db_name  = 'cantidad';
		$edit->cantidad->css_class= 'inputnum';
		$edit->cantidad->rel_id   = 'itordi';
		$edit->cantidad->rule     = 'numeric|mayorcero|required';
		$edit->cantidad->maxlength= 10;
		$edit->cantidad->size     = 7;
		$edit->cantidad->autocomplete= false;
		$edit->cantidad->showformat  = 'decimal';

		$arr=array(
			'costofob'   =>'costofob'    ,
			'importefob' =>'importefob'  ,
		);
		foreach($arr as $obj=>$db){
			$edit->$obj = new inputField(ucfirst("$obj <#o#>"), "${obj}_<#i#>");
			$edit->$obj->db_name  = $db;
			$edit->$obj->css_class= 'inputnum';
			$edit->$obj->rel_id   = 'itordi';
			$edit->$obj->rule     = 'trim|mayorcero|required';
			$edit->$obj->maxlength= 20;
			$edit->$obj->size     = 10;
			$edit->$obj->autocomplete= false;
			$edit->$obj->showformat  = 'decimal';
			if($obj=='importefob') $edit->$obj->type ='inputhidden';
		}

		$edit->codaran = new inputField('Codaran <#o#>', 'codaran_<#i#>');
		$edit->codaran->db_name  = 'codaran';
		$edit->codaran->rel_id   = 'itordi';
		$edit->codaran->rule     = 'trim|required';
		$edit->codaran->maxlength= 15;
		$edit->codaran->size     = 10;
		//$edit->codaran->readonly = true;
		$edit->codaran->append($aran);

		$edit->arancel = new inputField('arancel <#o#>','arancel_<#i#>');
		$edit->arancel->db_name   = 'arancel';
		$edit->arancel->css_class = 'inputnum';
		$edit->arancel->rel_id    = 'itordi';
		$edit->arancel->rule      = 'trim';
		$edit->arancel->maxlength = 7;
		$edit->arancel->size      = 5;
		$edit->arancel->readonly  = true;
		$edit->arancel->autocomplete= false;
		$edit->arancel->showformat  = 'decimal';
		$edit->arancel->type ='inputhidden';
		//Termina el detalle

		$edit->ordeni  = new autoUpdateField('status','A','A');

		$stat=$edit->_dataobject->get('status');
		if($stat!='C'){
			$accion="javascript:window.location='".site_url('import/ordi/cargarordi/'.$edit->_dataobject->pk['numero'])."'";
			$edit->button_status('btn_cargar','Cargar',$accion,'BR','show');

			$action = "javascript:window.location='".site_url('import/ordi/calcula/'.$edit->_dataobject->pk['numero'])."'";
			$edit->button_status('btn_recalculo', 'Calcular valores', $action, 'BR','show');

			$action = "javascript:window.location='".site_url('import/ordi/arancif/'.$edit->_dataobject->pk['numero'])."'";
			$edit->button_status('btn_arancif', 'Reajustar los aranceles', $action, 'BR','show');

			$edit->buttons('modify','save','delete','add_rel');
		}else{
			$id=$edit->get_from_dataobjetct('numero');
			$this->db->where('ordeni', $id);
			$this->db->from('ordiestima');
			if($this->db->count_all_results()>0){
				$accion="javascript:window.location='".site_url('import/ordi/gserestima'.$edit->pk_URI())."'";
				$edit->button_status('btn_ginpo','Agregar gasto real',$accion,'BR','show');
			}
		}
		$accion="javascript:window.location='".site_url('import/limport/liqui/'.$edit->_dataobject->pk['numero'])."'";
		$edit->button_status('btn_liqui','Descargar Caldeco',$accion,'BR','show');

		$accion="javascript:window.location='".site_url('formatos/ver/ORDI'.$edit->pk_URI())."'";
		$edit->button_status('btn_imprime','Imprimir',$accion,'BR','show');

		$edit->buttons('undo','back');
		$edit->build();

		$auto_aran=site_url('import/ordi/autocomplete/codaran');
		//$this->rapyd->jquery[]='$(".inputnum").numeric(".");';

		if($edit->_status=='show'){
			$conten['peroles'][] = $this->_showgeri($edit->_dataobject->pk['numero'],$stat)  ;
			$conten['peroles'][] = $this->_showgeser($edit->_dataobject->pk['numero'],$stat) ;
			$conten['peroles'][] = $this->_showordiva($edit->_dataobject->pk['numero'],$stat);
			//$conten['peroles'][] = $this->_showordiestima($edit->_dataobject->pk['numero'],$stat);

			$crm=$edit->_dataobject->get('crm');
			if(!empty($crm)){
				$adici=array($edit->_dataobject->pk['numero']);
				$this->prefijo='crm_';
				$conten['peroles'][] = Contenedor::_showAdjuntos($crm,'import/ordi/adjuntos',$adici);
				$conten['peroles'][] = Contenedor::_showEventos($crm,'import/ordi/eventos',$adici);
				$conten['peroles'][] = Contenedor::_showComentarios($crm,'import/ordi/comentarios',$adici);
			}
		}

		$conten['form']  =& $edit;
		$data['content'] =  $this->load->view('view_ordi',$conten,true);

		$data['style']   = style('redmond/jquery-ui.css');

		$data['script']  = script('jquery.js');
		$data['script'] .= script('jquery-ui.js');
		$data['script'] .= script('plugins/jquery.floatnumber.js');
		$data['script'] .= script('plugins/jquery.numeric.pack.js');
		$data['script'] .= script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		$data['script'] .= phpscript('nformat.js');
		$data['head']    = $this->rapyd->get_head();
		$data['title']   =  heading('Importaciones');
		$this->load->view('view_ventanas', $data);
	}

	function _showordiestima($id,$stat='C'){
		$this->rapyd->load('datagrid');

		$grid = new DataGrid('Lista de gastos internacionales');
		$select=array('a.concepto','a.monto','a.id');
		$grid->db->select($select);
		$grid->db->from('ordiestima AS a');
		$grid->db->where('a.ordeni',$id);

		$grid->use_function('str_pad');
		$grid->order_by('a.concepto','desc');
		$status=$this->datasis->dameval('SELECT status FROM ordi WHERE numero='.$this->db->escape($id));

		$uri=($status!='C')?anchor('import/ordi/ordiestima/'.$id.'/modify/<#id#>','Modificar') :'Fijo';

		$grid->column('Modificar',$uri);
		$grid->column('Concepto' ,'concepto');
		$grid->column('Monto'    ,'<nformat><#monto#></nformat>','align=\'right\'');

		if($stat!='C') $grid->add('import/ordi/ordiestima/'.$id.'/create','Agregar estimacion');
		$grid->build();

		if($grid->recordCount > 0){
			return $grid->output;
		}elseif($stat!='C'){
			return $grid->_button_container['TR'][0];
		}else{
			return '';
		}
	}

	function _showgeri($id,$stat='C'){
		$this->rapyd->load('datagrid2');

		$grid = new DataGrid2('Lista de gastos internacionales');
		$grid->totalizar('monto');
		$select=array('a.numero','a.id','a.concepto','a.monto','a.fecha',
			'IF(LENGTH(a.proveed)=0,b.proveed,b.proveed) AS proveed',
			'IF(LENGTH(a.proveed)=0,b.nombre,b.nombre) AS nombre'
			);
		$grid->db->select($select);
		$grid->db->from('gseri AS a');
		$grid->db->join('ordi AS b','b.numero=a.ordeni');
		$grid->db->where('ordeni',$id);

		$grid->use_function('str_pad');
		$grid->order_by('a.numero','desc');
		$status=$this->datasis->dameval('SELECT status FROM ordi WHERE numero='.$this->db->escape($id));

		$uri=($status!='C')?anchor('import/ordi/gseri/'.$id.'/modify/<#id#>','<sinulo><#numero#>|No tiene</sinulo>') :'<sinulo><#numero#>|No tiene</sinulo>';

		$grid->column('N. Factura',$uri);
		$grid->column('Proveedor','<#proveed#>-<#nombre#>');
		$grid->column('Fecha'    ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
		$grid->column('Concepto' ,'concepto');
		$grid->column('Monto'    ,'<nformat><#monto#></nformat>','align=\'right\'');

		if($stat!='C') $grid->add('import/ordi/gseri/'.$id.'/create','Agregar gasto internacional');
		$grid->build();

		if($grid->recordCount > 0){
			return $grid->output;
		}elseif($stat!='C'){
			return $grid->_button_container['TR'][0];
		}else{
			return '';
		}
	}

	function _showgeser($id,$stat='C'){
		$this->rapyd->load('datagrid2');

		$tablagrid=$pivot=array();
		$sel= array('numero','proveed','nombre','fecha','totpre');
		$this->db->select($sel);
		$this->db->from('gser');
		$this->db->where('ordeni',$id);
		$this->db->where('tipo_doc <>','XX');
		$this->db->order_by('numero','desc');
		$query=$this->db->get();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$pivot = array(
					'numero'  => $row->numero,
					'proveed' => $row->proveed,
					'nombre'  => $row->nombre,
					'fecha'   => $row->fecha,
					'totpre'  => $row->totpre,
					'estima'  => 'N',
					'id'      => ''
				);
				$tablagrid[]=$pivot;
			}
		}

		$sel= array('concepto','monto','id');
		$this->db->select($sel);
		$this->db->from('ordiestima');
		$this->db->where('ordeni',$id);
		$query=$this->db->get();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$pivot = array(
					'numero'  => 'Estimado',
					'proveed' => '',
					'nombre'  => $row->concepto,
					'fecha'   => '',
					'totpre'  => $row->monto,
					'estima'  => 'S',
					'id'      => $row->id
				);

				$tablagrid[]=$pivot;
			}
		}
		$status=$this->datasis->dameval('SELECT status FROM ordi WHERE numero='.$this->db->escape($id));


		function glink($estima,$idestima,$numero,$id,$sta){
			if($sta=='C') return $numero;
			if($estima=='S'){
				$rt=anchor('import/ordi/ordiestima/'.$id.'/show/'.$idestima,$numero);
			}else{
				$rt=$numero;
			}
			return $rt;
		}

		$grid = new DataGrid2('Lista de gastos nacionales',$tablagrid);
		$grid->totalizar('totpre');
		$grid->use_function('glink');

		$grid->column('N. Factura','<glink><#estima#>|<#id#>|<#numero#>|'.$id.'|'.$status.'</glink>');
		$grid->column('Proveedor' ,'proveed');
		$grid->column('Nombre'    ,'nombre');
		$grid->column('Fecha'     ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
		//$grid->column('Concepto'  ,'concepto');
		$grid->column('Monto'     ,'<nformat><#totpre#></nformat>','align=\'right\'');

		if($stat!='C'){ $grid->add('import/ordi/gser/'.$id,'Agregar/Eliminar gasto nacional'); }
		$grid->build();

		if($grid->recordCount > 0){
			$text='<div class="alert" style="text-align:center;">***En los gastos nacionales deben estar incluidos los gastos de araceles e iva.***</div>';
			return $grid->output.$text;
		}elseif($stat!='C'){
			return $grid->_button_container['TR'][0];
		}else{
			return '';
		}
	}

	function _showordiva($id,$stat='C'){
		$this->rapyd->load('datagrid2');

		$grid = new DataGrid2('Lista de impuestos al valor agregado','ordiva');
		$grid->totalizar('montoiva');
		$grid->db->where('ordeni',$id);
		$grid->use_function('str_pad');
		$grid->order_by('id','desc');
		$grid->per_page = 5;
		$status=$this->datasis->dameval('SELECT status FROM ordi WHERE numero='.$this->db->escape($id));

		$uri=($status!='C')? anchor('import/ordi/ordiva/'.$id.'/modify/<#id#>','<nformat><#tasa#></nformat>%'):'<nformat><#tasa#></nformat>%';

		$grid->column('Tasa'          ,$uri);
		$grid->column('Base Imponible','<nformat><#base#></nformat>','align=\'right\'');
		$grid->column('IVA'           ,'<nformat><#montoiva#></nformat>','align=\'right\'');
		$grid->column('Concepto'      ,'concepto');

		if($stat!='C') $grid->add('import/ordi/ordiva/'.$id.'/create','Agregar IVA');
		$grid->build();
		//echo $grid->db->last_query();


		if($grid->recordCount > 0){
			return $grid->output;
		}elseif($stat!='C'){
			return $grid->_button_container['TR'][0];
		}else{
			return '';
		}
	}

	function gseri($ordi){
		$this->rapyd->load('dataobject','dataedit');

		$sprv=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed','nombre'=>'nombre'),
			'titulo'  =>'Buscar Proveedor',
			'where'   =>'tipo IN (3,4)');
		$boton=$this->datasis->modbus($sprv);

		$edit = new DataEdit('Gastos internacionales', 'gseri');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;

		$edit->back_url = site_url('import/ordi/dataedit/show/'.$ordi);
		$edit->post_process('insert','_post_gseri');
		$edit->post_process('update','_post_gseri');
		$edit->post_process('delete','_post_gseri');

		$edit->proveed = new inputField('Proveedor', 'proveed');
		$edit->proveed->rule     ='trim|existesprv';
		$edit->proveed->maxlength=5;
		$edit->proveed->size     =7;
		$edit->proveed->append($boton);

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->rule     ='trim';
		$edit->nombre->maxlength=40;
		$edit->nombre->size     =40;
		$edit->nombre->in       ='proveed';
		$edit->nombre->readonly =true;
		$edit->nombre->append('Dejar vacio si es el mismo proveedor de la importaci&oacute;n');

		$edit->fecha = new DateonlyField('Fecha','fecha','d/m/Y');
		$edit->fecha->rule= 'required';
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 10;

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size     = 10;
		$edit->numero->maxlength=8;
		$edit->numero->autocomplete=false;

		$edit->concepto = new inputField('Concepto', 'concepto');
		$edit->concepto->size     = 35;
		$edit->concepto->maxlength= 40;
		$edit->concepto->autocomplete=false;

		$edit->monto = new inputField2('Monto $','monto');
		$edit->monto->rule= 'required|numeric|mayorcero';
		$edit->monto->size = 20;
		$edit->monto->css_class='inputnum';
		$edit->monto->autocomplete=false;

		$edit->ordeni  = new autoUpdateField('ordeni',$ordi,$ordi);
		$edit->usuario = new autoUpdateField('usuario', $this->session->userdata('usuario'), $this->session->userdata('usuario'));
		$edit->hora    = new autoUpdateField('hora',date('h:i:s'),date('h:i:s'));

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
		$this->rapyd->jquery[]='$("#proveed").autocomplete({
			source: function( req, add){
				$.ajax({
					url:  "'.site_url('ajax/buscasprv').'",
					type: "POST",
					dataType: "json",
					data: "q="+req.term,
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$("#nombre").val("");
								//$("#nombre_val").text("");
							}else{
								$.each(data,
									function(i, val){
										sugiere.push( val );
									}
								);
							}
							add(sugiere);
						},
				})
			},
			minLength: 1,
			select: function( event, ui ) {
				$("#proveed").val(ui.item.proveed);
				$("#nombre").val(ui.item.nombre);
				//$("#nombre_val").text(ui.item.nombre);
			}
		});';


		$data['content'] = $edit->output;
		$data['title']   = heading('Gasto de importaci&oacute;n');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function ordiva($ordi){
		$this->rapyd->load('dataobject','dataedit');
		$fecha = $this->datasis->dameval("SELECT fecha FROM ordi WHERE numero=${ordi}");
		$iva   = $this->datasis->ivaplica($fecha);

		$jsc='function calcula(){
			if($("#tasa").val().length>0){
				tasa=parseFloat($("#tasa").val());
				if($("#base").val().length>0) base=parseFloat($("#base").val()); else base=0;
				$("#montoiva").val(roundNumber(base*(tasa/100),2));
			}
		}
		function calculaiva(){
			if($("#tasa").val().length>0){
				tasa=parseFloat($("#tasa").val());
				if($("#montoiva").val().length>0) montoiva=parseFloat($("#montoiva").val()); else montoiva=0;
				$("#base").val(roundNumber(montoiva*100/tasa,2));
			}
		}';

		$edit = new DataEdit(' ', 'ordiva');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;

		$edit->back_url = site_url('import/ordi/dataedit/show/'.$ordi);
		$edit->post_process('insert','_post_ordiva');
		$edit->post_process('update','_post_ordiva');
		$edit->post_process('delete','_post_ordiva');

		$edit->id = new inputField2('Numero','id');
		$edit->id->mode= 'autohide';
		$edit->id->when=array('modify');

		$edit->tasa =  new dropdownField('Tasa %','tasa');
		foreach($iva AS $nom=>$val){
			$edit->tasa->option($val,nformat($val).'%');
		}
		$edit->tasa->rule  = 'required|numeric';
		$edit->tasa->style = 'width:100px';
		$edit->tasa->mode  = 'autohide';
		$edit->tasa->append('<span style="color:black;"> Vigente para la fecha <b>'.dbdate_to_human($fecha).'</b></span>');

		$edit->base = new inputField('Base imponible','base');
		$edit->base->rule= 'required|numeric';
		$edit->base->size = 15;
		$edit->base->css_class='inputnum';
		$edit->base->autocomplete= false;

		$edit->montoiva = new inputField('IVA ','montoiva');
		$edit->montoiva->rule= 'required|numeric';
		$edit->montoiva->size = 15;
		$edit->montoiva->autocomplete= false;
		$edit->montoiva->css_class='inputnum';

		$edit->concepto = new inputField2('Concepto','concepto');
		$edit->concepto->rule= 'max_length[100]';
		$edit->concepto->max_size = 100;

		$edit->ordeni  = new autoUpdateField('ordeni',$ordi,$ordi);

		$edit->script($jsc,'create');
		//$edit->script($jsm,'modify');
		$accion="javascript:window.location='".site_url('import/ordi/cargarordi'.$edit->pk_URI())."'";
		$edit->button_status('btn_cargar','Cargar',$accion,'TR','show');

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		if($edit->_status!='show'){
			$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
			$this->rapyd->jquery[]='$("#tasa").change(function() { calcula(); });';
			$this->rapyd->jquery[]='$("#base").bind("keyup",function() { calcula(); });';
			$this->rapyd->jquery[]='$("#montoiva").bind("keyup",function() { calculaiva(); });';
		}

		if($edit->_status=='modify'){
			$jsm='<script language="javascript" type="text/javascript">
			function calcula(){
				tasa='.$edit->tasa->value.';
				if($("#base").val().length>0) base=parseFloat($("#base").val()); else base=0;
				$("#montoiva").val(roundNumber(base*(tasa/100),2));
			}
			function calculaiva(){
				tasa='.$edit->tasa->value.';
				if($("#montoiva").val().length>0) montoiva=parseFloat($("#montoiva").val()); else montoiva=0;
				$("#base").val(roundNumber(montoiva*100/tasa,2));
			}
			</script>';
			$data['script'] =$jsm;
		}
		$data['content'] = $edit->output;
		$data['title']   = '<h1>Impuestos IVA</h1>';
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function gser($ordi){
		$this->rapyd->load('datagrid','datafilter');

		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;digo Proveedor',
			'nombre'=>'Nombre',
			'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');
		$boton=$this->datasis->modbus($modbus);

		$filter = new DataFilter('Filtro de Egresos');
		$filter->db->select('numero,fecha,vence,nombre,totiva,totneto,totpre,proveed,ordeni');
		$filter->db->from('gser');
		$filter->db->where("(ordeni IS NULL or ordeni=$ordi or ordeni=0)");
		$filter->db->where('tipo_doc <>','XX');

		$filter->fechad = new dateonlyField('Desde', 'fechad','d/m/Y');
		$filter->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		//$filter->fechad->insertValue = date('Y-m-d');
		//$filter->fechah->insertValue = date('Y-m-d');
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size=20;

		$filter->proveedor = new inputField('Proveedor','proveed');
		$filter->proveedor->append($boton);
		$filter->proveedor->db_name = 'proveed';
		$filter->proveedor->size=20;

		$filter->monto  = new inputField2('Monto ','totpre');
		$filter->monto->clause='where';
		$filter->monto->operator='=';
		$filter->monto->size = 20;
		$filter->monto->css_class='inputnum';

		$filter->checkbox = new checkboxField('Solo gastos asociados?', 'ordeni', $ordi,'');

		$action = "javascript:window.location='".site_url('import/ordi/dataedit/show/'.$ordi)."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'TR');

		$accion="javascript:window.location='".site_url('import/ordi/ordiestima/'.$ordi.'/create')."'";
		$filter->button('btn_estima','Agregar estimaci&oacute;n',$accion,'BL');

		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('finanzas/gser/dataedit/show/<#fecha#>/<#numero#>/<#proveed#>','<#numero#>');

		$grid = new DataGrid();
		$grid->order_by('numero','desc');
		$grid->use_function('checker');
		$grid->per_page = 15;

		function checker($conci,$proveed,$fecha,$numero,$ordi){
			$arr=array($fecha,$numero,$proveed,$ordi);
			if(empty($conci)){
				return form_checkbox($proveed.$fecha.$numero, serialize($arr));
			}else{
				return form_checkbox($proveed.$fecha.$numero, serialize($arr),TRUE);
			}
		}

		$grid->column_orderby('N&uacute;mero','numero','numero');
		$grid->column_orderby('Fecha'   ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha','align=\'center\'');
		$grid->column_orderby('Vence'   ,'<dbdate_to_human><#vence#></dbdate_to_human>','vence','align=\'center\'');
		$grid->column_orderby('Nombre'  ,'nombre','nombre');
		$grid->column_orderby('Monto'   ,'<nformat><#totpre#></nformat>','totpre','align=\'right\'');
		$grid->column_orderby('Enlace'  ,'<checker><#ordeni#>|<#proveed#>|<#fecha#>|<#numero#>|'.$ordi.'</checker>','ordeni','align=\'center\'');
		$grid->build();
		//echo $grid->db->last_query();

		$this->rapyd->jquery[]='$(":checkbox:not(#ordeni)").change(function(){
			name=$(this).attr("name");
			$.post("'.site_url('import/ordi/agordi').'",{ data: $(this).val()},
			function(data){
					if(data=="1"){
					return true;
				}else{
					$("input[name=\'"+name+"\']").removeAttr("checked");
					alert("Hubo un error, comuniquese con soporte tecnico: "+data);
					return false;
				}
			});
		});';

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = '<h1>Relaci&oacute;n de gastos nacionales</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function gserestima($ordi){
		$this->rapyd->load('datagrid','datafilter');

		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;digo Proveedor',
			'nombre'=>'Nombre',
			'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');
		$boton=$this->datasis->modbus($modbus);

		$filter = new DataFilter('Filtro de Egresos');
		$filter->db->select(array('b.id','a.id AS gser_id','a.numero','a.fecha','a.vence','a.nombre','a.totiva','a.totneto','a.totpre','a.proveed'));
		$filter->db->from('gser AS a');
		$filter->db->join('ordiestgser AS b','a.id=b.id_gser AND b.id_ordi='.$this->db->escape($ordi),'left');
		$filter->db->where("(a.ordeni IS NULL OR a.ordeni=0)");
		$filter->db->where('a.tipo_doc <>','XX');

		$filter->fechad = new dateonlyField('Desde', 'fechad','d/m/Y');
		$filter->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		//$filter->fechad->insertValue = date('Y-m-d');
		//$filter->fechah->insertValue = date('Y-m-d');
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size=20;

		$filter->proveedor = new inputField('Proveedor','proveed');
		$filter->proveedor->append($boton);
		$filter->proveedor->db_name = 'proveed';
		$filter->proveedor->size=20;

		$filter->monto  = new inputField2('Monto ','totpre');
		$filter->monto->clause='where';
		$filter->monto->operator='=';
		$filter->monto->size = 20;
		$filter->monto->css_class='inputnum';

		$action = "javascript:window.location='".site_url('import/ordi/dataedit/show/'.$ordi)."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'TR');

		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('finanzas/gser/dataedit/show/<#fecha#>/<#numero#>/<#proveed#>','<#numero#>');

		$grid = new DataGrid();
		$grid->order_by('numero','desc');
		$grid->use_function('checker');
		$grid->per_page = 15;

		function checker($id,$gser_id,$ordi){
			$arr=array($gser_id,$ordi);
			if(empty($id)){
				$sel=false;
			}else{
				$sel=true;
			}
			return form_checkbox($gser_id, serialize($arr),$sel);
		}

		$grid->column_orderby('N&uacute;mero','numero','numero');
		$grid->column_orderby('Fecha'   ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha','align=\'center\'');
		$grid->column_orderby('Vence'   ,'<dbdate_to_human><#vence#></dbdate_to_human>','vence','align=\'center\'');
		$grid->column_orderby('Nombre'  ,'nombre','nombre');
		$grid->column_orderby('Monto'   ,'<nformat><#totpre#></nformat>','totpre','align=\'right\'');
		$grid->column_orderby('Enlace'  ,'<checker><#id#>|<#gser_id#>|'.$ordi.'</checker>','ordeni','align=\'center\'');
		$grid->build();

		$this->rapyd->jquery[]='$(":checkbox:not(#ordeni)").change(function(){
			name=$(this).attr("name");
			$.post("'.site_url('import/ordi/agordiesti').'",{ data: $(this).val()},
			function(data){
					if(data=="1"){
					return true;
				}else{
					$("input[name=\'"+name+"\']").removeAttr("checked");
					alert("Hubo un error, comuniquese con soporte tecnico: "+data);
					return false;
				}
			});
		});';

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Conciliaci&oacute;n de gastos nacionales');
		$this->load->view('view_ventanas', $data);
	}

	function ordiestima($ordi){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Estimaci&oacute;n de gastos nacionales', 'ordiestima');
		$edit->back_url = site_url('import/ordi/dataedit/show/'.$ordi);
		$edit->back_save   = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;

		$edit->concepto = new inputField('Concepto','concepto');
		$edit->concepto->rule='max_length[100]|required|strtoupper';
		$edit->concepto->size =50;
		$edit->concepto->maxlength =100;

		$edit->monto = new inputField('Monto','monto');
		$edit->monto->rule='max_length[10]|numeric|required';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =12;
		$edit->monto->maxlength =10;

		$edit->ordeni  = new autoUpdateField('ordeni',$ordi,$ordi);

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$script= '<script type="text/javascript" >
		$(function() {
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});
		</script>';

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['script'] .= $script;
		$data['title']   = heading('Estimaci&oacute;n de gastos nacionales');
		$this->load->view('view_ventanas', $data);
	}

	function adjuntos($id,$ordi){
		$this->crm_back=site_url('import/ordi/dataedit/show/'.$ordi);
		$this->prefijo='crm_';
		contenedor::adjuntos($id);
	}

	function comentarios($id,$ordi){
		$this->crm_back=site_url('import/ordi/dataedit/show/'.$ordi);
		$this->prefijo='crm_';
		contenedor::comentario($id);
	}

	function eventos($id,$ordi){
		$this->crm_back=site_url('import/ordi/dataedit/show/'.$ordi);
		$this->prefijo='crm_';
		contenedor::eventos($id);
	}

	function calcula($id){
		$rt=$this->_calcula($id);

		$url = site_url('formatos/verhtml/ORDI/'.$id);

		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Recalculo de los valores');
		if($rt)
			$data['content'] = '<h1>Recalculo concluido, puede <a href="#" onclick="fordi.print();">imprimir</a> la importaci&oacute;n o haga click '.anchor("import/ordi/dataedit/show/$id",'aqui').' para regresar</h1>';
		else
			$data['content'] = '<h1>Opps! hubo problemas en el recalculo, se gener&oacute; un centinela '.anchor("import/ordi/dataedit/show/$id",'regresar').'</h1>';
		$data['content'].= "<iframe name='fordi' src ='$url' width='100%' height='450'><p>Tu navegador no soporta iframes.</p></iframe>";
		$this->load->view('view_ventanas', $data);
	}

	function _calcula($id){
		$modo='m'; //'m' para el calculo en base al monto, 'o' para el peso
		$dbid=$this->db->escape($id);
		$error=0;

		$mSQL="SELECT SUM(a.importefob) AS montofob, SUM(b.peso) AS pesotota
			FROM itordi AS a
			JOIN sinv AS b ON a.codigo=b.codigo
			WHERE numero=${dbid}";
		$row=$this->datasis->damerow($mSQL);

		$pesotota= $row['pesotota'];
		$montofob= $row['montofob'];
		$estimac = $this->datasis->dameval("SELECT SUM(monto)    AS monto    FROM ordiestima WHERE ordeni=${dbid}");
		$gastosi = $this->datasis->dameval("SELECT SUM(monto)    AS gastosi  FROM gseri  WHERE ordeni=${dbid}");
		$gsermon = $this->datasis->dameval("SELECT SUM(totpre)   AS gastosn  FROM gser   WHERE ordeni=${dbid} AND tipo_doc<>'XX'");
		$montoiva= $this->datasis->dameval("SELECT SUM(montoiva) AS montoiva FROM ordiva WHERE ordeni=${dbid}");
		$baseiva = $this->datasis->dameval("SELECT SUM(base)     AS base     FROM ordiva WHERE ordeni=${dbid}");
		if(empty($estimac))  $estimac =0;
		if(empty($gsermon))  $gsermon =0;
		if(empty($gastosi))  $gastosi =0;
		if(empty($montoiva)) $montoiva=0;
		if(empty($baseiva))  $baseiva =0;
		$gastosn = $estimac+$gsermon;

		$mSQL="SELECT cambioofi, cambioreal FROM ordi WHERE numero=${dbid}";
		$row=$this->datasis->damerow($mSQL);

		$cambioofi =$row['cambioofi'];
		$cambioreal=$row['cambioreal'];

		if($modo=='m'){
			$participa='participam'; //m para el monto;
		}else{
			$participa='participao'; //o para el peso;
		}

		//Calcula las participaciones
		$mSQL="UPDATE itordi AS a JOIN sinv AS b ON a.codigo=b.codigo SET a.participao=b.peso/${pesotota}, a.iva=b.iva WHERE a.numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
		$mSQL="UPDATE itordi SET participam=importefob/${montofob} WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

		//CIF costo,seguro y flete (fob+gastos internacionales)
		$mSQL="UPDATE itordi SET importecif=(${participa}*${gastosi})+importefob WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
		$mSQL="UPDATE itordi SET costocif=importecif/cantidad WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
		$mSQL="UPDATE itordi SET importeciflocal=importecif*${cambioofi},importecifreal=importecif*${cambioreal} WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

		//Monto del arancel (debe ser en moneda local)
		$mSQL="UPDATE itordi SET montoaran=IF(arancif>0,arancif,importeciflocal)*(arancel/100) WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

		//Gastos
		$montoaran =$this->datasis->dameval("SELECT SUM(montoaran) AS montoaran FROM itordi WHERE numero=${dbid}");
		$montoiva  =$this->datasis->dameval("SELECT SUM(montoiva)  AS montoiva  FROM ordiva WHERE ordeni=${dbid}");
		$ggastosn=$gastosn-$montoaran-$montoiva;
		$mSQL="UPDATE itordi SET gastosi=${participa}*${gastosi} WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
		$mSQL="UPDATE itordi SET gastosn=${participa}*${ggastosn} WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

		//Total en moneda local
		$mSQL="UPDATE itordi SET importefinal=importeciflocal+montoaran+gastosn WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
		$mSQL="UPDATE itordi SET costofinal=importefinal/cantidad WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
		$mSQL="UPDATE itordi SET importereal=importecifreal+montoaran+gastosn WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
		$mSQL="UPDATE itordi SET costoreal=importereal/cantidad WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

		//Calculo de los precios
		$mSQL="UPDATE itordi AS a JOIN sinv AS b ON a.codigo=b.codigo SET
			a.precio1=(costoreal*100/(100-b.margen1))*(1+(b.iva/100)),
			a.precio2=(costoreal*100/(100-b.margen2))*(1+(b.iva/100)),
			a.precio3=(costoreal*100/(100-b.margen3))*(1+(b.iva/100)),
			a.precio4=(costoreal*100/(100-b.margen4))*(1+(b.iva/100))
			WHERE numero=${dbid}";
		$ban=$this->db->simple_query($mSQL);
		if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

		$montocif=0;
		$mSQL="SELECT SUM(montoaran) AS aranceles, SUM(importecif) AS montocif  FROM itordi WHERE numero=${dbid}";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row_array();
			//$importecif     =(empty($row['montocif']))? 0: $row['montocif']*$cambioofi; //montocif en moneda local
			$importecif     =(empty($row['montocif']))? 0: $row['montocif']*$cambioreal; //montocif en moneda local
			$row['peso']    =$pesotota;
			$row['gastosi'] =$gastosi;
			$row['gastosn'] =$ggastosn;
			$row['montoiva']=$montoiva;
			$row['montotot']=$importecif+$gastosn;
			$row['montoexc']=$row['montotot']-$baseiva-$montoiva;//monto exento
			$row['cargoval']=($row['montocif']*$cambioreal)-($row['montocif']*$cambioofi);// Diferencia dolar real y oficial
			$montocif=floatval($gastosi);

			$where = "numero=${dbid}";
			$str = $this->db->update_string('ordi', $row, $where);
			$ban=$this->db->simple_query($str);
			if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
		}

		/*$mmSQL ='SELECT ';
		$mmSQL.="a.codigo,a.cantidad, a.descrip, $participa*100 AS participa,";
		$mmSQL.="a.costofob,ROUND(a.costofob*$cambioofi,2) AS fobbs, ";                                                  //valor unidad fob
		$mmSQL.="a.importefob,ROUND(a.importefob*$cambioofi,2) AS totfobbs,ROUND(a.gastosi*$cambioofi,2) AS gastosibs,"; //Valores totales
		$mmSQL.='a.costocif,a.importeciflocal, ';                                                                        //Valores CIF en BS
		$mmSQL.='a.arancel,a.montoaran,a.gastosn, ';                                                                     //Arancel1
		$mmSQL.='a.costofinal,a.importefinal, ';                                                                         //calculo al oficial
		$mmSQL.="ROUND((a.montoaran+a.gastosn+(a.importecif*$cambioreal))/a.cantidad, 2)AS costofinal2,";     //calculo al real
		$mmSQL.="ROUND(a.montoaran+a.gastosn+(a.importecif*$cambioreal),2) AS importefinal2 ";                //calculo real
		$mmSQL.='FROM (itordi AS a)';
		$mmSQL.="WHERE a.numero = $dbid";*/

		return ($error==0) ? true: false;
	}

	function cargarordi($control){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('dataform');

		$form = new DataForm("import/ordi/cargarordi/$control/process");

		$form->almacen = new  dropdownField ('Almac&eacute;n', 'almacen');
		$form->almacen->option('','Seleccionar');
		$form->almacen->options("SELECT ubica,CONCAT_WS('-',ubica,ubides) AS val FROM caub WHERE gasto='N' and invfis='N' ORDER BY ubides");
		$form->almacen->insertValue=$this->datasis->traevalor('ALMACEN');
		$form->almacen->rule = 'required';

		$form->fecha = new dateonlyField('Fecha de llegada de la mercanc&iacute;a', 'fecha','d/m/Y');
		$form->fecha->insertValue = date('Y-m-d');
		$form->fecha->rule='required';
		$form->fecha->size=10;

		$form->precios = new radiogroupField('Pol&iacute;tica de precios', 'precios',
		array(
			'S'=>'Cambiar precios repetando los margenes',
			'N'=>'Cambiar margenes repetando los precios'
		));
		$form->precios->insertValue='S';
		$form->precios->rule='required';

		$action = 'javascript:window.location=\'' . site_url('import/ordi/dataedit/show/'.$control).'\'';
		$form->button('btn_regresar', 'Regresar', $action, 'BR');

		$form->submit('btnsubmit','Guardar');
		$form->build_form();

		if ($form->on_success()){
			$almacen  = $form->almacen->newValue;
			$actualiza= $form->fecha->newValue;
			$cprecios = $form->precios->newValue;
			$this->_calcula($control);
			$rt=$this->_cargarordi($control,$almacen,$actualiza,$cprecios);
			if($rt===false){
				$data['content']  = $this->error_string.br();
			}else{
				$data['content']  = "Liquidaci&oacute;n cargada bajo el numero de control $rt ".br();
			}

			$data['content'] .= anchor('import/ordi/dataedit/show/'.$control,'Regresar');
		}else{
			$data['content'] = $form->output;
		}

		$data['head']    = $this->rapyd->get_head();
		$data['title']   = '<h1>Cargar liquidaci&oacute;n de importaci&oacute;n '.str_pad($control,8,0,0).'</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function _cargarordi($id,$depo,$actualiza,$cprecios){
		$error =0;
		$status=$this->datasis->dameval('SELECT status FROM ordi WHERE numero='.$this->db->escape($id));
		//$gacona =$this->datasis->dameval("SELECT SUM(totpre)   AS gastosn  FROM gser  WHERE ordeni=$id"); //gastos comunes nacionales

		if($status!='C'){
			$SQL='SELECT fecha, fecha AS recep,factura AS numero,proveed,nombre,fecha AS vence,peso,montocif AS mdolar,montoexc AS exento,cambioofi, cambioreal,dua,transac FROM ordi WHERE numero=?';
			$query=$this->db->query($SQL,array($id));
			if($query->num_rows()==1){
				$control = $this->datasis->fprox_numero('nscst');
				$transac = $row['transac'];
				$row     = $query->row_array();
				$numero  = substr($row['numero'],-8);
				$serie   = $row['numero'];
				$fecha   = $row['fecha'];
				$proveed = $row['proveed'];

				$row['recep']    = $actualiza;
				$row['tipo_doc'] = 'FC';
				$row['serie']    = $serie;
				$row['depo']     = $depo;
				$row['numero']   = $numero;
				$row['control']  = $control;
				$row['transac']  = $transac;
				$row['nfiscal']  = $numero;
				$row['depo']     = $depo;
				$row['montonet'] = 0;
				$row['montotot'] = 0;
				//$row['exento']   = 0;
				$row['sobretasa']= 0;
				$row['reducida'] = 0;
				$row['tasa']     = 0;
				$row['observa1'] = 'LIQUIDACION DE IMPORTACION D.U.A. '.$row['dua'].' numero '.$id;
				$row['estampa']  = date('Ymd');
				$row['hora']     = date('H:i:s');
				$costoreal       = 0;
				$importereal     = 0;
				$costocifreal    = 0;
				$importecifreal  = 0;
				$cambioofi       = $row['cambioofi'];
				$cambioreal      = $row['cambioreal'];
				unset($row['dua']);
				unset($row['cambioofi']);
				unset($row['cambioreal']);
				$tasas=$this->datasis->ivaplica($fecha);

				$itdata=array();
				$sql='SELECT a.codigo,a.descrip,a.cantidad,a.costofinal,a.importefinal,b.iva,a.importecif,a.montoaran,a.gastosn,
					a.costoreal,a.importereal,a.importecifreal,a.importecifreal/a.cantidad AS costocifreal,
					a.precio1,a.precio2,a.precio3,a.precio4
					FROM itordi AS a JOIN sinv AS b ON a.codigo=b.codigo WHERE a.numero=?';
				$qquery=$this->db->query($sql,array($id));
				if($qquery->num_rows()>0){
					foreach ($qquery->result() as $itrow){
						$itdata['control'] = $control;
						$itdata['transac'] = $transac;
						$itdata['proveed'] = $proveed;
						$itdata['depo']    = $depo;
						$itdata['codigo']  = $itrow->codigo;
						$itdata['descrip'] = $itrow->descrip;
						$itdata['cantidad']= $itrow->cantidad;
						$itdata['fecha']   = $fecha;
						$itdata['numero']  = $numero;
						$itdata['costo']   = $itrow->costoreal;
						$itdata['importe'] = $itrow->importereal;
						$itdata['iva']     = $itrow->iva;
						$itdata['montoiva']= $itrow->importefinal*($itrow->iva/100);
						$itdata['estampa'] = date('Y-m-d');
						$itdata['hora']    = date('h:i:s');
						$itdata['usuario'] = $this->session->userdata('usuario');
						$itdata['ultimo']  = $itrow->costoreal;
						$itdata['precio1'] = $itrow->precio1;
						$itdata['precio2'] = $itrow->precio2;
						$itdata['precio3'] = $itrow->precio3;
						$itdata['precio4'] = $itrow->precio4;

						$mSQL=$this->db->insert_string('itscst', $itdata);
						$ban=$this->db->simple_query($mSQL);
						if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

						$costoreal      += $itrow->costoreal;
						$importereal    += $itrow->importereal;
						$costocifreal   += $itrow->costocifreal;
						$importecifreal += $itrow->importecifreal;

						//Actualiza el inventario
						$this->datasis->sinvcarga($itrow->codigo,$depo,$itrow->cantidad);
						$dbcodigo = $this->db->escape($itrow->codigo);

						$mSQL='UPDATE sinv SET
							pond=(existen*IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))+'.$itrow->cantidad*$itrow->costoreal.')/(existen+'.$itrow->cantidad.'),
							prov3=prov2, prepro3=prepro2, pfecha3=pfecha2, prov2=prov1, prepro2=prepro1, pfecha2=pfecha1,
							prov1='.$this->db->escape($proveed).',
							prepro1='.$itrow->costoreal.',
							pfecha1='.$this->db->escape($fecha).',
							ultimo='.$itrow->costoreal.'
							WHERE codigo='.$dbcodigo;
						$ban=$this->db->simple_query($mSQL);
						if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

						//Cambia los precios
						if($cprecios=='S'){
							//$mSQL='UPDATE sinv SET
							//	precio1='.$this->db->escape($itrow->precio1).',
							//	precio2='.$this->db->escape($itrow->precio2).',
							//	precio3='.$this->db->escape($itrow->precio3).',
							//	precio4='.$this->db->escape($itrow->precio4).'
							//	WHERE codigo='.$dbcodigo;
							//$ban=$this->db->simple_query($mSQL);
							//if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
							$mSQL='UPDATE sinv SET
								base1=ROUND(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*10000/(100-margen1))/100,
								base2=ROUND(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*10000/(100-margen2))/100,
								base3=ROUND(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*10000/(100-margen3))/100,
								base4=ROUND(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*10000/(100-margen4))/100,
								precio1=ROUND(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*100*(100+iva)/(100-margen1))/100,
								precio2=ROUND(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*100*(100+iva)/(100-margen2))/100,
								precio3=ROUND(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*100*(100+iva)/(100-margen3))/100,
								precio4=ROUND(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*100*(100+iva)/(100-margen4))/100,
								activo="S"
							WHERE codigo='.$dbcodigo;
							$ban=$this->db->simple_query($mSQL);
							if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
						}else{
							$mSQL='UPDATE sinv SET
								base1=ROUND(precio1*10000/(100+iva))/100,
								base2=ROUND(precio2*10000/(100+iva))/100,
								base3=ROUND(precio3*10000/(100+iva))/100,
								base4=ROUND(precio4*10000/(100+iva))/100,
								margen1=ROUND(10000-(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*10000/base1))/100,
								margen2=ROUND(10000-(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*10000/base2))/100,
								margen3=ROUND(10000-(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*10000/base3))/100,
								margen4=ROUND(10000-(IF(formcal="P",pond,IF(formcal="U",ultimo,GREATEST(pond,ultimo)))*10000/base4))/100,
								activo="S"
							WHERE codigo='.$dbcodigo;
							$ban=$this->db->simple_query($mSQL);
							if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
						}
						//Fin de la actualizacion de inventario
					}
				}

				$row['cstotal'] = $importecifreal;
				$row['ctotal']  = $importecifreal;
				$row['cexento'] = $importecifreal;
				$row['montotot']= $importereal;
				$row['apagar']  = $importecifreal;

				//Al proveedor internacional no se le paga iva
				//por esa razon todos los montos son cero
				$baseeimpu=0;
				$row['cimpuesto']=0;
				$row['cgenera']  =0;
				$row['civagen']  =0;
				$row['cadicio']  =0;
				$row['civaadi']  =0;
				$row['creduci']  =0;
				$row['civared']  =0;

				$row['montasa']   = 0;
				$row['tasa']      = 0;
				$row['monadic']   = 0;
				$row['sobretasa'] = 0;
				$row['monredu']   = 0;
				$row['reducida']  = 0;

				$ssql='SELECT tasa,base,montoiva FROM ordiva WHERE ordeni=?';
				$qqquery=$this->db->query($ssql,array($id));
				if($qqquery->num_rows()>0){
					foreach ($qqquery->result() as $ivarow){
						if($ivarow->tasa==$tasas['tasa']){
							$row['montasa']   = $ivarow->base;
							$row['tasa']      = $ivarow->montoiva;
						}elseif($ivarow->tasa==$tasas['sobretasa']){
							$row['monadic']   = $ivarow->base;
							$row['sobretasa'] = $ivarow->montoiva;
						}elseif($ivarow->tasa==$tasas['redutasa']){
							$row['monredu']   = $ivarow->base;
							$row['reducida']  = $ivarow->montoiva;
						}
						$baseeimpu += ($ivarow->tasa!=0)? $ivarow->base: 0 ;
					}
				}

				$row['montoiva'] = $row['tasa']+$row['sobretasa']+$row['reducida'];
				$row['montonet'] = $importereal+$row['tasa']+$row['sobretasa']+$row['reducida'];
				//$row['exento']   = ($baseeimpu>$importereal)? 0 : $importereal-$baseeimpu;
				$row['credito']  = $importecifreal;
				$row['usuario']  = $this->session->userdata('usuario');
				$row['anticipo'] = $row['flete']=$row['otros']=$row['reten']=$row['ppago']=0;
				$row['inicial']  = 0;

				$mSQL=$this->db->insert_string('scst', $row);
				$ban=$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

				//Carga la CxP
				$mSQL='DELETE FROM sprm WHERE transac='.$this->db->escape($transac);
				$ban=$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

				$sprm=array();
				$causado = $this->datasis->fprox_numero('ncausado');
				$sprm['cod_prv']  = $proveed;
				$sprm['nombre']   = $row['nombre'];
				$sprm['tipo_doc'] = 'FC';
				$sprm['numero']   = $numero;
				$sprm['fecha']    = $actualiza;
				$sprm['vence']    = $actualiza;
				$sprm['monto']    = $importecifreal;
				$sprm['impuesto'] = $row['cimpuesto'];
				$sprm['abonos']   = 0;
				$sprm['observa1'] = 'IMPORTACION '.$id.' MONTO EN DOLARES '.nformat($row['mdolar']);
				$sprm['reteiva']  = 0;
				$sprm['causado']  = $causado;
				$sprm['estampa']  = date('Y-m-d H:i:s');
				$sprm['usuario']  = $this->session->userdata('usuario');
				$sprm['hora']     = date('H:i:s');
				$sprm['transac']  = $transac;

				$mSQL=$this->db->insert_string('sprm', $sprm);
				$ban =$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
				//Fin de la carga de la CxP

				$mSQL='UPDATE scst SET `actuali`='.$actualiza.' WHERE `control`='.$this->db->escape($control);
				$ban=$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'ordi'); $error++; }

				//$mSQL = $this->db->update_string('ordi', array('status'=>'A','control'=>$control), 'numero='.$this->db->escape($id)); //Solo para pruebas
				$mSQL = $this->db->update_string('ordi', array('status'=>'C','control'=>$control), 'numero='.$this->db->escape($id));
				$ban=$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
				if($error>0){
					$this->error_string='Hubo algunos errores, se genero un centinela';
					return false;
				}else{
					return $control;
				}
			}else{
				$this->error_string='Orden no existe';
				return false;
			}
		}else{
			$this->error_string='No se puede cargar una orden que ya fue cerrada';
			return false;
		}
	}

	function arancif($id){
		$this->rapyd->load('datagrid','fields');

		$error='';
		if($this->input->post('pros')!==FALSE){
			$pmontos  =$this->input->post('arancif');
			foreach($pmontos AS $iid=>$cant){
				if(!is_numeric($cant)){
					$error.="$cant no es un valor num&eacute;rico<br>";
				}else{
					$data  = array('arancif' => $cant);
					$dbid=$this->db->escape($iid);
					$where = "id = $dbid";
					$mSQL  = $this->db->update_string('itordi', $data, $where);
					$this->db->simple_query($mSQL);
				}
			}
		}
		$this->_calcula($id);

		$ggrid =form_open('/import/ordi/arancif/'.$id);
		$monto = new inputField('Arancif','arancif');
		$monto->grid_name   = 'arancif[<#id#>]';
		$monto->status      = 'modify';
		$monto->size        = 12;
		$monto->autocomplete= false;
		$monto->css_class   = 'inputnum';

		$expli='En caso de que en la aduana calcule el valor del arancel en base a un costo estad&iacute;stico diferente puede asignar el nuevo costo en los campos siguientes, en caso de dejarlo en cero se tomar&aacute; el valor del importe CIF real.';

		$select=array('a.codigo','a.descrip','a.cantidad','a.importecif','a.id','a.arancif','a.montoaran','a.arancel','a.importeciflocal');
		$grid = new DataGrid($expli);
		$grid->db->select($select);
		$grid->db->from('itordi AS a');
		$grid->db->join('ordi AS b','a.numero=b.numero');
		$grid->db->where('a.numero',$id);
		//$grid->order_by('a.numero','desc');

		$grid->column_orderby('C&oacute;digo'     ,'codigo'    ,'codigo'   );
		$grid->column_orderby('Descripci&oacute;n','descrip'   ,'descrip'  );
		$grid->column_orderby('Cantidad'          ,'<nformat><#cantidad#></nformat>'  ,'cantidad'      ,'align=\'right\'');
		$grid->column_orderby('Importe CIF Real'   ,'<nformat><#importeciflocal#></nformat>','importeciflocal'    ,'align=\'right\'');
		$grid->column_orderby('Monto del arancel'  ,'<b><nformat><#montoaran#></nformat></b> (<nformat><#arancel#></nformat>%)' ,'montoaran','align=\'right\'');
		$grid->column('Importe CIF estad&iacute;stico en moneda local',$monto     ,'align=\'right\'');
		$grid->submit('pros', 'Guardar y calcular','BR');
		$grid->button('btn_reg', 'Regresar',"javascript:window.location='".site_url('/import/ordi/dataedit/show/'.$id)."'", 'BR');
		$grid->build();
		//echo $grid->db->last_query();

		$ggrid.=$grid->output;
		$ggrid.=form_close();

		$script ='
		<script type="text/javascript">
		$(function() {
			$(".inputnum").numeric(".");
		});
		</script>';

		$data['content'] = '<div class=\'alert\'>'.$error.'</div>'.$ggrid;
		$data['title']   = '<h1>Asignaci&oacute;n en los montos estad&iacute;sticos para el c&aacute;lculo de los aranceles</h1>';
		$data['script']  = $script;
		$data['head']    = $this->rapyd->get_head().script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$this->load->view('view_ventanas', $data);
	}

	function agordi(){
		$data=$this->input->post('data');

		if($data!==false){
			$pk=unserialize($data);

			$mSQL  = 'UPDATE `gser` SET `ordeni` = IF(ordeni IS NULL,'.$pk[3].',NULL)';
			$mSQL .= ' WHERE fecha = '.$this->db->escape($pk[0]);
			$mSQL .= ' AND numero  = '.$this->db->escape($pk[1]);
			$mSQL .= ' AND proveed = '.$this->db->escape($pk[2]);

			//echo $mSQL;
			if($this->db->simple_query($mSQL)){
				$dbnum=$this->db->escape($pk[3]);
				$gastosn=$this->datasis->dameval('SELECT SUM(totpre) FROM gser WHERE tipo_doc<>\'XX\' AND ordeni='.$dbnum);
				if(empty($gastosn)) $gastosn=0;
				$mSQL="UPDATE ordi SET gastosn=$gastosn WHERE numero=$dbnum";
				$this->db->simple_query($mSQL);
				echo '1';
			}else{
				echo '0';
			}
		}
	}

	function agordiesti(){
		$data=$this->input->post('data');

		if($data!==false){
			$pk=unserialize($data);
			$gser_id=$pk[0];
			$ordi   =$pk[1];

			$this->db->where('id_ordi', $ordi);
			$this->db->where('id_gser', $gser_id);
			$this->db->from('ordiestgser');
			$cana=$this->db->count_all_results();

			$data = array(
				'id_ordi' => $ordi,
				'id_gser' => $gser_id
			);
			if($cana>0){
				$this->db->delete('ordiestgser', $data);
			}else{
				$this->db->insert('ordiestgser', $data);
			}

			$db_ordi=$this->db->escape($ordi);

			$gsereal = $this->datasis->dameval("SELECT SUM(totpre) AS monto FROM (`gser` AS a) JOIN `ordiestgser` AS b ON `a`.`id`=`b`.`id_gser` AND b.id_ordi=$db_ordi");
			if(empty($gsereal)) $gsereal=0;

			$estimac = $this->datasis->dameval("SELECT SUM(monto)    AS monto    FROM ordiestima WHERE ordeni=$db_ordi");
			if(empty($estimac)) $estimac=0;

			$estimadif=$estimac-$gsereal;
			$this->db->where('numero',$ordi);
			$this->db->update('ordi' ,array('estimadif' => $estimadif));
			echo '1';
		}
	}

	//crea un contenedor para asociarlo
	//con el crm
	function contenedor($id){

	}

	function _post_ordiva($do){
		$ordeni=$do->get('ordeni');
		$monto =$this->datasis->dameval("SELECT SUM(montoiva) FROM ordiva WHERE ordeni=$ordeni");
		if(empty($monto)) $monto=0;

		$data  = array('montoiva' => $monto);
		$where = "numero= $ordeni";
		$str = $this->db->update_string('ordi', $data, $where);
		$this->db->simple_query($str);
		return true;
	}

	function _post_gseri($do){
		$ordeni=$do->get('ordeni');
		$this->_calcula($ordeni);
		/*$monto =$this->datasis->dameval("SELECT SUM(monto) FROM gseri WHERE ordeni=$ordeni");
		if(empty($monto)) $monto=0;

		$data  = array('gastosi' => $monto,'montocif'=>"montofob+$monto");
		$where = "numero= $ordeni";
		$str = $this->db->update_string('ordi', $data, $where);
		//$this->db->simple_query($str);
		*/
		return true;
	}

	function _pre_insert($do){
		$transac= $this->datasis->fprox_numero('ntransa');
		$usuario= $this->session->userdata('usuario');
		$sprv   = $do->get('proveed');
		$agente = $do->get('agente');
		$dbsprv  =$this->db->escape($sprv);
        $dbagente=$this->db->escape($agente);

		$do->set('nombre'   ,$this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$dbsprv));
		$do->set('nomage'   ,$this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$dbagente));
		$do->set('usuario'  ,$usuario);
		$do->set('transac'  ,$transac);
		$do->set('estampa'  ,date('Ymd'));
		$do->set('hora'     ,date('H:i:s'));
		$do->set('estimadif',0);

		//Crea el cotenedor
		$data['usuario']    = $usuario;
		$data['status']     = 'A';
		$data['fecha']      = date('Ymd');
		$data['titulo']     = 'Importación '.$do->get('numero');
		$data['proveed']    = $sprv;
		$data['descripcion']= 'Importación al proveedor '.$do->get('proveed').' numero '.$do->get('numero');
		//$data['condiciones']= '';
		//$data['definicion'] = '';
		//$data['tipo']       = '';
		$str = $this->db->insert_string('crm_contenedor', $data);
		$this->db->simple_query($str);
		$do->set('crm',$this->db->insert_id());

		return true;
	}

	function _pre_delete($do){
		$status=$do->get('status');
		if($status!='A'){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Opps Disculpe....!, no se puede borrar una orden cuyo estatus es diferente a \'A\'';
			return false;
		}
	}

	function _post_insert($do){
		$codigo=$do->get('numero');
		$peso=$this->datasis->dameval("SELECT SUM(b.peso) AS peso FROM itordi AS a JOIN sinv AS b ON a.codigo=b.codigo AND a.numero=${codigo}");
		if(empty($peso)) $peso=0;
		$data  = array('peso' => $peso);
		$where = "numero= ${codigo}";
		$str = $this->db->update_string('ordi', $data, $where);
		$this->db->simple_query($str);
		logusu('ordi',"ORDI $codigo CREADO");
		return true;
	}

	function _post_update($do){
		$codigo=$do->get('numero');
		$peso=$this->datasis->dameval("SELECT SUM(b.peso) AS peso FROM itordi AS a JOIN sinv AS b ON a.codigo=b.codigo AND a.numero=${codigo}");
		if(empty($peso)) $peso=0;
		$data  = array('peso' => $peso);
		$where = "numero= $codigo";
		$str = $this->db->update_string('ordi', $data, $where);
		$this->db->simple_query($str);
		logusu('ordi',"ORDI ${codigo} MODIFICADO");
		return true;
	}

	function _post_delete($do){
		$numero  =$do->get('numero');
		$dbnumero=$this->db->escape($numero);
		$mSQL="DELETE FROM gseri WHERE ordeni=${dbnumero}";
		$this->db->simple_query($mSQL);

		$mSQL="DELETE FROM ordiva WHERE ordeni=${dbnumero}";
		$this->db->simple_query($mSQL);

		$mSQL="UPDATE gser SET ordeni=null WHERE ordeni=${dbnumero}";
		$this->db->simple_query($mSQL);

		logusu('ordi',"ORDI orden de importacion numero ${numero} ELIMINADO");
		return true;
	}

	function instalar(){
		if(!$this->db->field_exists('ordeni', 'gser')){
			$mSQL='ALTER TABLE `gser`  ADD COLUMN `ordeni` INT(15) UNSIGNED NULL DEFAULT NULL AFTER `compra`';
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->table_exists('itordi')){
			$mSQL="CREATE TABLE `itordi` (
				`numero` INT(15) UNSIGNED NOT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`codigo` CHAR(15) NULL DEFAULT NULL,
				`descrip` CHAR(45) NULL DEFAULT NULL,
				`cantidad` DECIMAL(10,3) NULL DEFAULT NULL,
				`costofob` DECIMAL(17,2) NULL DEFAULT NULL,
				`importefob` DECIMAL(17,2) NULL DEFAULT NULL,
				`gastosi` DECIMAL(17,2) NULL DEFAULT NULL,
				`costocif` DECIMAL(17,2) NULL DEFAULT NULL,
				`importecif` DECIMAL(17,2) NULL DEFAULT NULL,
				`importeciflocal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'importe cif en moneda local',
				`importecifreal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'importe cif en moneda local al cambio real',
				`codaran` CHAR(15) NULL DEFAULT NULL,
				`arancel` DECIMAL(7,2) NULL DEFAULT NULL,
				`montoaran` DECIMAL(17,2) NULL DEFAULT NULL,
				`gastosn` DECIMAL(17,2) NULL DEFAULT NULL,
				`costofinal` DECIMAL(17,2) NULL DEFAULT NULL,
				`importefinal` DECIMAL(17,2) NULL DEFAULT NULL,
				`costoreal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'costo unitario al dolar real',
				`importereal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'importe al dolar real',
				`participam` DECIMAL(9,6) NULL DEFAULT NULL,
				`participao` DECIMAL(9,6) NULL DEFAULT NULL,
				`arancif` DECIMAL(17,4) NULL DEFAULT '0.0000' COMMENT 'Monto del valor en base al cual se calcula el motoaran',
				`iva` DECIMAL(17,2) NULL DEFAULT NULL,
				`precio1` DECIMAL(15,2) NULL DEFAULT NULL,
				`precio2` DECIMAL(15,2) NULL DEFAULT NULL,
				`precio3` DECIMAL(15,2) NULL DEFAULT NULL,
				`precio4` DECIMAL(15,2) NULL DEFAULT NULL,
				`estampa` DATE NULL DEFAULT NULL,
				`hora` CHAR(8) NULL DEFAULT NULL,
				`usuario` CHAR(12) NULL DEFAULT NULL,
				`id` INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `numero` (`numero`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=FIXED
			AUTO_INCREMENT=1";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('ordi')) {
			$mSQL="CREATE TABLE `ordi` (
				`numero` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				`fecha` DATE NULL DEFAULT NULL,
				`status` CHAR(1) NOT NULL DEFAULT '' COMMENT 'Estatus de la Compra Abierto, Eliminado y Cerrado',
				`proveed` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Proveedor',
				`nombre` VARCHAR(40) NULL DEFAULT NULL COMMENT 'Nombre del Proveedor',
				`agente` CHAR(5) NULL DEFAULT NULL COMMENT 'Agente Aduanal (Proveedor)',
				`nomage` VARCHAR(40) NULL DEFAULT NULL COMMENT 'Agente Aduanal (Proveedor)',
				`montofob` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Total de la Factura extranjera',
				`gastosi` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Gastos Internacionales (Fletes, Seguros, etc)',
				`montocif` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Monto FOB+gastos Internacionales',
				`aranceles` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Suma del Impuesto Arancelario',
				`gastosn` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Gastos Nacionales',
				`montotot` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Monto CIF + Gastos Nacionales',
				`montoiva` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Monto del IVA pagado',
				`montoexc` DECIMAL(12,2) NULL DEFAULT NULL,
				`arribo` DATE NULL DEFAULT NULL COMMENT 'Fecha de Llegada',
				`factura` VARCHAR(20) NULL DEFAULT NULL COMMENT 'Nro de Factura',
				`cambioofi` DECIMAL(17,2) NOT NULL DEFAULT '0.00' COMMENT 'Cambio Fiscal US$ X Bs.',
				`cambioreal` DECIMAL(17,2) NOT NULL DEFAULT '0.00' COMMENT 'Cambio Efectivamente Aplicado',
				`peso` DECIMAL(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Peso total',
				`condicion` TEXT NULL,
				`transac` VARCHAR(8) NOT NULL DEFAULT '',
				`estampa` DATE NOT NULL DEFAULT '0000-00-00',
				`usuario` VARCHAR(12) NOT NULL DEFAULT '',
				`hora` VARCHAR(8) NOT NULL DEFAULT '',
				`dua` CHAR(30) NULL DEFAULT NULL COMMENT 'DECLARACION UNICA ADUANAS',
				`cargoval` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Diferencia Cambiara $ oficial y aplicado',
				`control` VARCHAR(8) NULL DEFAULT NULL COMMENT 'Apuntador a la factura con la que se relaciono',
				`crm` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'Apuntador al conetendor',
				`estimadif` DECIMAL(10,2) NULL DEFAULT '0' COMMENT 'Diferencia en la estimacion',
				PRIMARY KEY (`numero`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DYNAMIC
			AUTO_INCREMENT=1";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->table_exists('ordiva')){
			$mSQL="CREATE TABLE `ordiva` (
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				`ordeni` INT(15) UNSIGNED NULL DEFAULT NULL,
				`tasa` DECIMAL(7,2) NULL DEFAULT NULL,
				`base` DECIMAL(10,2) NULL DEFAULT NULL,
				`montoiva` DECIMAL(10,2) NULL DEFAULT NULL,
				`concepto` VARCHAR(100) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `ordi` (`ordeni`, `tasa`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT
			AUTO_INCREMENT=0";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->table_exists('gseri')){
			$mSQL="CREATE TABLE `gseri` (
			`ordeni` INT(15) UNSIGNED NOT NULL,
			`fecha` DATE NOT NULL DEFAULT '0000-00-00',
			`numero` VARCHAR(8) NOT NULL DEFAULT '',
			`concepto` VARCHAR(40) NULL DEFAULT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT NULL,
				`proveed` VARCHAR(5) NULL DEFAULT '',
				`nombre` VARCHAR(30) NULL DEFAULT '',
				`usuario` VARCHAR(12) NULL DEFAULT NULL,
				`estampa` DATE NULL DEFAULT NULL,
				`hora` VARCHAR(8) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DYNAMIC
			AUTO_INCREMENT=0";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->field_exists('importecifreal', 'itordi')){
			$mSQL="ALTER TABLE `itordi`  ADD COLUMN `importecifreal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'importe cif en moneda local al cambio real' AFTER `importeciflocal`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->field_exists('costoreal', 'itordi')){
			$mSQL="ALTER TABLE `itordi`  ADD COLUMN `costoreal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'costo unitario al dolar real' AFTER `importefinal`,  ADD COLUMN `importereal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'importe al dolar real' AFTER `costoreal`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->field_exists('estimadif', 'ordi')){
			$mSQL="ALTER TABLE `ordi`ADD COLUMN `estimadif` DECIMAL(10,2) NULL DEFAULT '0' COMMENT 'Diferencia en la estimacion' AFTER `crm`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->table_exists('ordiestima')){
			$mSQL="CREATE TABLE `ordiestima` (
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				`ordeni` INT(15) UNSIGNED NULL DEFAULT NULL,
				`monto` DECIMAL(10,2) NULL DEFAULT NULL,
				`concepto` VARCHAR(100) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `ordi` (`ordeni`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			var_dump($this->db->simple_query($mSQL));
		}

		$this->prefijo='crm_';
		contenedor::instalar();
	}
}
