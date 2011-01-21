<?php require_once(APPPATH.'/controllers/crm/contenedor.php');

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

		$filter = new DataFilter('Filtro de &Oacute;rdenes de importaci&oacute;n','ordi');

		$filter->numero = new inputField('N&uacute;mero','numero');
		$filter->numero->size=15;

		$filter->fecha = new dateonlyField('Fecha', 'fecha');
		$filter->fecha->size=12;

		$filter->proveed = new inputField('Proveedor', 'proveed');
		$filter->proveed->size=12;
		$filter->proveed->append($boton);

		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('import/ordi/dataedit/show/<#numero#>','<str_pad><#numero#>|8|0|0</str_pad>');
		$uri2 = anchor_popup('formatos/verhtml/ORDI/<#numero#>','Ver HTML',$atts);

		$grid = new DataGrid('Lista');
		$grid->use_function('str_pad');
		$grid->order_by('numero','desc');
		$grid->per_page = 5;

		$grid->column_orderby('N&uacute;mero',$uri,'numero');
		$grid->column_orderby('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha','align=\'center\'');
		$grid->column_orderby('Proveedor'    ,'proveed','proveed');
		$grid->column_orderby('Nombre'       ,'nombre','nombre');
		$grid->column_orderby('Monto Fob.'   ,'<nformat><#montofob#></nformat>','montofob','align=\'right\'');
		$grid->column_orderby('Monto Total'  ,'<nformat><#montotot#></nformat>','montotot','align=\'right\'');
		$grid->column('Vista',$uri2,'align=\'center\'');

		$grid->add('import/ordi/dataedit/create','Agregar nueva orden');
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = '<h1>Orden de importaci&oacute;n</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');
		$monedalocal='Bs';
		//print_r(get_defined_constants());

		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'descrip'),
			'retornar'=>array('codigo'=>'codigo_<#i#>','descrip'=>'descrip_<#i#>'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  =>'Buscar Producto en inventario');
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
			'where'   =>'tipo IN (3,4)');
		$boton=$this->datasis->modbus($sprv);

		$aran=array(
			'tabla'   =>'aran',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n',
				'tarifa'=>'Tarifas'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'descrip'),
			'retornar'=>array('codigo'=>'codaran_<#i#>','tarifa'=>'arancel_<#i#>'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  =>'Buscar Aranceles',
			'script'  =>array('calcula()'));
		$aran=$this->datasis->p_modbus($aran,'<#i#>');

		$asprv=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'agente','nombre'=>'nomage'),
			'titulo'  =>'Buscar Proveedor');
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
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->dua= new inputField('Declaraci&oacute;n &uacute;nida de aduana', 'dua');
		$edit->dua->size=10;

		$edit->fecha = new  dateonlyField('Fecha','fecha');
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
		$edit->proveed->rule     ='trim|required';
		$edit->proveed->maxlength=5;
		$edit->proveed->size     =7;
		$edit->proveed->append($boton);

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->rule     ='trim';
		$edit->nombre->maxlength=40;
		$edit->nombre->size     =40;

		$edit->agente = new inputField('Agente aduanal', 'agente');
		$edit->agente->rule     ='trim';
		$edit->agente->maxlength=5;
		$edit->agente->size     =7;
		$edit->agente->append($aboton);

		$edit->nomage = new inputField('Nombre de agente', 'nomage');
		$edit->nomage->rule     ='trim';
		$edit->nomage->maxlength=40;
		$edit->nomage->size     =40;

		$arr=array(
			'montofob' =>'Total factura extrangera $',
			'gastosi'  =>'Gastos Internacionales $',
			'montocif' =>'Monto FOB+gastos Internacionales $',
			//'aranceles'=>'Suma del Impuesto Arancelario '.$monedalocal,
			'gastosn'  =>'Gastos Nacionales Bs',
			'montotot' =>'Monto CIF + Gastos Nacionales '.$monedalocal,
			'montoiva' =>'Monto del iva '.$monedalocal);

		foreach($arr as $obj => $etiq){
			$edit->$obj = new inputField($etiq, $obj);
			$edit->$obj->rule     ='trim';
			$edit->$obj->maxlength=20;
			$edit->$obj->size     =10;
			$edit->$obj->css_class= 'inputnum';
		}

		$edit->arribo = new dateonlyField('Fecha de Llegada', 'arribo');
		$edit->arribo->rule     ='chfecha';
		$edit->arribo->maxlength=8;
		$edit->arribo->size     =10;

		$edit->factura = new inputField('Nro. Factura', 'factura');
		$edit->factura->rule     ='trim';
		$edit->factura->maxlength=20;
		$edit->factura->size     =10;

		$edit->cambioofi = new inputField('Cambio Oficial', 'cambioofi');
		$edit->cambioofi->css_class= 'inputnum';
		$edit->cambioofi->rule     ='trim|required';
		$edit->cambioofi->maxlength=17;
		$edit->cambioofi->size     =10;

		$edit->cambioreal = new inputField('Cambio Real', 'cambioreal');
		$edit->cambioreal->css_class= 'inputnum';
		$edit->cambioreal->rule     ='trim|required';
		$edit->cambioreal->maxlength=17;
		$edit->cambioreal->size     =10;

		$edit->peso = new inputField('Peso Total', 'peso');
		$edit->peso->css_class= 'inputnum';
		$edit->peso->rule     ='trim';
		$edit->peso->maxlength=12;
		$edit->peso->size     =10;

		$edit->condicion = new textareaField('Condiciones', 'condicion');
		$edit->condicion->rule ='trim';
		$edit->condicion->cols = 37;
		$edit->condicion->rows = 3;

		//comienza el detalle
		$edit->codigo = new inputField('C&oacute;digo <#o#>','codigo_<#i#>');
		$edit->codigo->db_name  = 'codigo';
		$edit->codigo->rule     = 'trim|required';
		$edit->codigo->rel_id   = 'itordi';
		$edit->codigo->maxlength= 15;
		$edit->codigo->size     = 10;
		$edit->codigo->append($btn);

		$edit->descrip = new inputField('Descripci&oacute;n <#o#>','descrip_<#i#>');
		$edit->descrip->db_name  ='descrip';
		$edit->descrip->rel_id   ='itordi';
		$edit->descrip->maxlength=35;
		$edit->descrip->size     =30;

		$edit->cantidad = new inputField('Cantidad <#o#>','cantidad_<#i#>');
		$edit->cantidad->db_name  = 'cantidad';
		$edit->cantidad->css_class= 'inputnum';
		$edit->cantidad->rel_id   = 'itordi';
		$edit->cantidad->rule     = 'numeric';
		$edit->cantidad->maxlength= 10;
		$edit->cantidad->size     = 7;

		$arr=array(
			'costofob'    =>'costofob'    ,
			'importefob'  =>'importefob'  ,
			//'relgastosi'  =>'gastosi'     ,
			//'costocif'    =>'costocif'    ,
			//'importecif'  =>'importecif'  ,
			//'montoaran'   =>'montoaran'   ,
			//'relgastosn'  =>'gastosn'     ,
			//'costofinal'  =>'costofinal'  ,
			//'importefinal'=>'importefinal',
			//'iva'           =>'iva'    ,
			);
		foreach($arr as $obj=>$db){
			$edit->$obj = new inputField(ucfirst("$obj <#o#>"), "${obj}_<#i#>");
			$edit->$obj->db_name  = $db;
			$edit->$obj->css_class= 'inputnum';
			$edit->$obj->rel_id   = 'itordi';
			$edit->$obj->rule     ='trim';
			$edit->$obj->maxlength=20;
			$edit->$obj->size     =10;
		}

		/*$edit->iva = new inputField('IVA <#o#>', 'iva_<#i#>');
		$edit->iva->db_name  = 'iva';
		$edit->iva->rel_id   = 'itordi';
		$edit->iva->rule     ='trim';
		$edit->iva->maxlength=7;
		$edit->iva->size     =6;*/

		$edit->codaran = new inputField('Codaran <#o#>', 'codaran_<#i#>');
		$edit->codaran->db_name  = 'codaran';
		$edit->codaran->rel_id   = 'itordi';
		$edit->codaran->rule     ='trim';
		$edit->codaran->maxlength=15;
		$edit->codaran->size     =10;
		$edit->codaran->readonly =true;
		$edit->codaran->append($aran);

		$arr=array(
			'arancel',
			//'participam',
			//'participao'
		);
		foreach($arr as $obj){
			$edit->$obj = new inputField(ucfirst("$obj <#o#>"), "${obj}_<#i#>");
			$edit->$obj->db_name  = $obj;
			$edit->$obj->css_class= 'inputnum';
			$edit->$obj->rel_id   = 'itordi';
			$edit->$obj->rule     ='trim';
			$edit->$obj->maxlength= 7;
			$edit->$obj->size     = 5;
			$edit->$obj->readonly =true;
		}

		/*$arr=array('precio1','precio2','precio3','precio4');
		foreach($arr as $obj){
			$edit->$obj = new inputField(ucfirst("$obj <#o#>"), "${obj}_<#i#>");
			$edit->$obj->db_name  = $obj;
			$edit->$obj->css_class= 'inputnum';
			$edit->$obj->rel_id   = 'itordi';
			$edit->$obj->rule     ='trim';
			$edit->$obj->maxlength=15;
			$edit->$obj->size     =10;
		}*/
		//Termina el detalle

		$edit->ordeni  = new autoUpdateField('status','A','A');

		$stat=$edit->_dataobject->get('status');
		if($stat!='C'){
			$accion="javascript:window.location='".site_url('import/ordi/cargarordi/'.$edit->_dataobject->pk['numero'])."'";
			$edit->button_status('btn_cargar','Cargar',$accion,'TR','show');

			$action = "javascript:window.location='".site_url('import/ordi/calcula/'.$edit->_dataobject->pk['numero'])."'";
			$edit->button_status('btn_recalculo', 'Calcular valores', $action, 'TR','show');

			$action = "javascript:window.location='".site_url('import/ordi/arancif/'.$edit->_dataobject->pk['numero'])."'";
			$edit->button_status('btn_arancif', 'Reajustar los aranceles', $action, 'TR','show');

			$edit->buttons('modify','save','delete','add_rel');
		}

		$edit->buttons('undo','back');
		$edit->build();

		$auto_aran=site_url('import/ordi/autocomplete/codaran');
		$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
		/*$this->rapyd->jquery[]='$(\'input[name^="codara"]\').autocomplete("'.$auto_aran.'",{
			delay:10,
			//minChars:2,
			matchSubset:1,
			matchContains:1,
			cacheLength:10,
			formatItem:formato,
			width:200,
			autoFill:true,
			onItemSelect: function(li) { 
				srt=li.innerHTML;
				arr=srt.split("-");
				str=arr[1].replace(/^\s*|\s*$/g,"");
				str=str.substring(0,str.length-1);
				num=des_nformat(str);
				$("#arancel_1").val(num);
			},
		});';*/

		//$data['content'] = $edit->output;
		//$data['smenu']   = $this->load->view('view_sub_menu','205',true);

		if($edit->_status=='show'){
			$conten['peroles'][] = $this->_showgeri($edit->_dataobject->pk['numero'],$stat)  ;
			$conten['peroles'][] = $this->_showgeser($edit->_dataobject->pk['numero'],$stat) ;
			$conten['peroles'][] = $this->_showordiva($edit->_dataobject->pk['numero'],$stat);

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
		$data['title']   =  '<h1>Orden de importaci&oacute;n</h1>';
		$data['head']    =  $this->rapyd->get_head().phpscript('nformat.js').script('plugins/jquery.autocomplete.js').style('jquery.autocomplete.css');
		$this->load->view('view_ventanas', $data); 
	}

	function autocomplete($campo,$cod=FALSE){
		if($cod!==false){
			$cod=$this->db->escape_like_str($cod);
			$qformato=$this->datasis->formato_cpla();
			$data['codaran']="SELECT codigo AS c1 ,tarifa AS c2, descrip AS c3 FROM aran WHERE codigo LIKE '$cod%' ORDER BY codigo LIMIT 10";
			if(isset($data[$campo])){
			$query=$this->db->query($data[$campo]);
				if($query->num_rows() > 0){
					foreach($query->result() AS $row){
						echo $row->c1.'|'.nformat($row->c2)."%\n";
					}
				}
			}
		}
	}

	function _showgeri($id,$stat='C'){
		$this->rapyd->load('datagrid');

		$grid = new DataGrid('Lista de gastos internacionales');
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

		$uri=anchor('import/ordi/gseri/'.$id.'/modify/<#id#>','<sinulo><#numero#>|No tiene</sinulo>');

		$grid->column('N. Factura',$uri);
		$grid->column('Proveedor','<#proveed#>-<#nombre#>');
		$grid->column('Fecha'    ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
		$grid->column('Concepto' ,'concepto');
		$grid->column('Monto'    ,'<nformat><#monto#></nformat>','align=\'right\'');

		if($stat!='C') $grid->add('import/ordi/gseri/'.$id.'/create','Agregar gasto internacional');
		$grid->build();

		return ($grid->recordCount > 0) ? $grid->output : $grid->_button_container['TR'][0];
	}

	function _showgeser($id,$stat='C'){
		$this->rapyd->load('datagrid');

		$grid = new DataGrid('Lista de gastos nacionales','gser');
		$grid->db->where('ordeni',$id);
		$grid->use_function('str_pad');
		$grid->order_by('numero','desc');

		$grid->column('N. Factura','numero');
		$grid->column('Proveedor' ,'proveed');
		$grid->column('Nombre'    ,'nombre');
		$grid->column('Fecha'     ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
		//$grid->column('Concepto'  ,'concepto');
		$grid->column('Monto'     ,'<nformat><#totpre#></nformat>','align=\'right\'');

		if($stat!='C') $grid->add('import/ordi/gser/'.$id,'Agregar/Eliminar gasto nacional');
		$grid->build();

		return ($grid->recordCount > 0) ? $grid->output : $grid->_button_container['TR'][0];
	}

	function _showordiva($id,$stat='C'){
		$this->rapyd->load('datagrid');

		$grid = new DataGrid('Lista de impuestos al valor agregado','ordiva');
		$grid->db->where('ordeni',$id);
		$grid->use_function('str_pad');
		$grid->order_by('id','desc');
		$grid->per_page = 5;

		$uri=anchor('import/ordi/ordiva/'.$id.'/modify/<#id#>','<nformat><#tasa#></nformat>%');

		$grid->column('Tasa'          ,$uri);
		$grid->column('Base Imponible','<nformat><#base#></nformat>','align=\'right\'');
		$grid->column('IVA'           ,'<nformat><#montoiva#></nformat>','align=\'right\'');
		$grid->column('Concepto'      ,'concepto');

		if($stat!='C') $grid->add('import/ordi/ordiva/'.$id.'/create','Agregar monto de tasa');
		$grid->build();

		return ($grid->recordCount > 0) ? $grid->output : $grid->_button_container['TR'][0];
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
		$edit->back_url = site_url('import/ordi/dataedit/show/'.$ordi);
		$edit->post_process('insert','_post_gseri');
		$edit->post_process('update','_post_gseri');
		$edit->post_process('delete','_post_gseri');

		$edit->proveed = new inputField('Proveedor', 'proveed');
		$edit->proveed->rule     ='trim';
		$edit->proveed->maxlength=5;
		$edit->proveed->size     =7;
		$edit->proveed->append($boton);

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->rule     ='trim';
		$edit->nombre->maxlength=40;
		$edit->nombre->size     =40;
		$edit->nombre->in       ='proveed';
		$edit->nombre->readonly =true;
		$edit->nombre->append('Dejar vacio si es el mismo proveedor de la orden de importaci&oacute;n');

		$edit->fecha = new DateonlyField('Fecha','fecha','d/m/Y');
		$edit->fecha->rule= 'required';
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 10;

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size     = 10;
		$edit->numero->maxlength=8;

		$edit->concepto = new inputField('Concepto', 'concepto');
		$edit->concepto->size     = 35;
		$edit->concepto->maxlength= 40;

		$edit->monto = new inputField2('Monto $','monto');
		$edit->monto->rule= 'required|numeric';
		$edit->monto->size = 20;
		$edit->monto->css_class='inputnum';

		$edit->ordeni  = new autoUpdateField('ordeni',$ordi,$ordi);
		$edit->usuario = new autoUpdateField('usuario', $this->session->userdata('usuario'), $this->session->userdata('usuario'));
		$edit->hora    = new autoUpdateField('hora',date('h:i:s'),date('h:i:s'));

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
		$data['content'] = $edit->output;
		$data['title']   = '<h1>Gasto de importaci&oacute;n</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function ordiva($ordi){
		$this->rapyd->load('dataobject','dataedit');
		$fecha = $this->datasis->dameval("SELECT fecha FROM ordi WHERE numero=$ordi");
		$iva   = $this->datasis->ivaplica($fecha);

		$jsc='function calcula(){
			if($("#tasa").val().length>0) tasa=parseFloat($("#tasa").val()); else tasa=0;
			if($("#base").val().length>0) base=parseFloat($("#base").val()); else base=0;
			$("#montoiva").val(roundNumber(base*(tasa/100),2));
		}';

		$edit = new DataEdit('Impuestos', 'ordiva');

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

		$edit->base = new inputField2('Base imponible','base');
		$edit->base->rule= 'required|numeric';
		$edit->base->size = 20;
		$edit->base->css_class='inputnum';

		$edit->montoiva = new inputField2('IVA ','montoiva');
		$edit->montoiva->rule= 'required|numeric';
		$edit->montoiva->size = 20;
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
			//$this->rapyd->jquery[]='calcula();';
			$this->rapyd->jquery[]='$("#tasa").change(function() { calcula(); });';
			$this->rapyd->jquery[]='$("#base,#montoiva").bind("keyup",function() { calcula(); });';
		}

		if($edit->_status=='modify'){
			$jsm='<script language="javascript" type="text/javascript">
			function calcula(){
				tasa='.$edit->tasa->value.';
				if($("#base").val().length>0) base=parseFloat($("#base").val()); else base=0;
				$("#montoiva").val(roundNumber(base*(tasa/100),2));
			}
			</script>';
			$data['script'] =$jsm;
		}
		$data['content'] = $edit->output;
		$data['title']   = '<h1>Impuestos</h1>';
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
		$filter->db->where("(ordeni IS NULL or ordeni=$ordi )");

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
		$filter->button('btn_regresa', 'Regresar', $action, 'BL');

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

		$grid->column('N&uacute;mero','numero');
		$grid->column('Fecha'   ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
		$grid->column('Vence'   ,'<dbdate_to_human><#vence#></dbdate_to_human>','align=\'center\'');
		$grid->column('Nombre'  ,'nombre');
		$grid->column('IVA'     ,'<nformat><#totiva#></nformat>' ,'align=\'right\'');
		$grid->column('Monto'   ,'<nformat><#totpre#></nformat>','align=\'right\'');
		$grid->column('Enlace'  ,'<checker><#ordeni#>|<#proveed#>|<#fecha#>|<#numero#>|'.$ordi.'</checker>','align=\'center\'');
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
		$data['title']   = '<h1>Relacion de gastos nacionales</h1>';
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
		$this->_calcula($id);

		$url = site_url('formatos/verhtml/ORDI/'.$id);
		$data['content'] = "<iframe src ='$url' width='100%' height='450'><p>Tu navegador no soporta iframes.</p></iframe>";
		$data['head']    = $this->rapyd->get_head();
		$data['title']   ='<h1>Recalculo de la relaci&oacute;n de gastos nacionales '.anchor("import/ordi/dataedit/show/$id",'regresar').'</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function _calcula($id){
		$modo='m'; //'m' para el calculo en base al monto, 'o' para el peso
		$dbid=$this->db->escape($id);

		$mSQL="SELECT SUM(a.importefob) AS montofob, SUM(b.peso) AS pesotota
			FROM itordi AS a
			JOIN sinv AS b ON a.codigo=b.codigo
			WHERE numero=$dbid";
		$row=$this->datasis->damerow($mSQL);

		$pesotota=$row['pesotota'];
		$montofob=$row['montofob'];
		$gastosi =$this->datasis->dameval("SELECT SUM(monto)    AS gastosi  FROM gseri WHERE ordeni=$dbid");
		$gastosn =$this->datasis->dameval("SELECT SUM(totpre)   AS gastosn  FROM gser  WHERE ordeni=$dbid");
		$montoiva=$this->datasis->dameval("SELECT SUM(montoiva) AS montoiva FROM ordiva WHERE ordeni=$dbid");
		$baseiva =$this->datasis->dameval("SELECT SUM(base)     AS base     FROM ordiva WHERE ordeni=$dbid");
		if(empty($gastosn))  $gastosn =0;
		if(empty($gastosi))  $gastosi =0;
		if(empty($montoiva)) $montoiva=0;
		if(empty($baseiva))  $baseiva =0;

		$mSQL="SELECT cambioofi, cambioreal FROM ordi WHERE numero=$dbid";
		$row=$this->datasis->damerow($mSQL);

		$cambioofi =$row['cambioofi'];
		$cambioreal=$row['cambioreal'];

		if($modo=='m'){
			$participa='participam'; //m para el monto;
		}else{
			$participa='participao'; //o para el peso;
		}

		//Calcula las participaciones
		$mSQL="UPDATE itordi AS a JOIN sinv AS b ON a.codigo=b.codigo SET a.participao=b.peso/$pesotota, a.iva=b.iva WHERE a.numero=$dbid";
		$this->db->simple_query($mSQL);
		$mSQL="UPDATE itordi SET participam=importefob/$montofob WHERE numero=$dbid";
		$this->db->simple_query($mSQL);

		//Gastos
		$mSQL="UPDATE itordi SET gastosi=$participa*$gastosi WHERE numero=$dbid";
		$this->db->simple_query($mSQL);
		$mSQL="UPDATE itordi SET gastosn=$participa*$gastosn WHERE numero=$dbid";
		$this->db->simple_query($mSQL);

		//CIF costo,seguro y flete (fob+gastos internacionales)
		$mSQL="UPDATE itordi SET importecif=($participa*$gastosi)+importefob WHERE numero=$dbid";
		$this->db->simple_query($mSQL);
		$mSQL="UPDATE itordi SET costocif=importecif/cantidad WHERE numero=$dbid";
		$this->db->simple_query($mSQL);
		$mSQL="UPDATE itordi SET importeciflocal=importecif*$cambioofi WHERE numero=$dbid";
		$this->db->simple_query($mSQL);

		//Monto del arancel (debe ser en moneda local)
		$mSQL="UPDATE itordi SET montoaran=IF(arancif>0,arancif,importeciflocal)*(arancel/100) WHERE numero=$dbid";
		$this->db->simple_query($mSQL);

		//Total en moneda local
		$mSQL="UPDATE itordi SET importefinal=importeciflocal+montoaran+gastosn WHERE numero=$dbid";
		$this->db->simple_query($mSQL);
		$mSQL="UPDATE itordi SET costofinal=importefinal/cantidad WHERE numero=$dbid";
		$this->db->simple_query($mSQL);

		//Calculo de los precios
		$mSQL="UPDATE itordi AS a JOIN sinv AS b ON a.codigo=b.codigo SET 
			a.precio1=(a.costofinal*100/(100-b.margen1))*(1+(b.iva/100)),
			a.precio2=(a.costofinal*100/(100-b.margen2))*(1+(b.iva/100)),
			a.precio3=(a.costofinal*100/(100-b.margen3))*(1+(b.iva/100)),
			a.precio4=(a.costofinal*100/(100-b.margen4))*(1+(b.iva/100))
			WHERE numero=$dbid";
		$this->db->simple_query($mSQL);

		$mSQL="SELECT SUM(montoaran) AS aranceles, SUM(importecif) AS montocif  FROM itordi WHERE numero=$dbid";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row_array();
			$importecif     =(empty($row['montocif']))? 0: $row['montocif']*$cambioofi; //montocif en moneda local
			$row['gastosi'] =$gastosi;
			$row['gastosn'] =$gastosn;
			$row['montoiva']=$montoiva;
			$row['montotot']=$importecif+$gastosn;
			$row['montoexc']=$importecif-$baseiva;//monto excento
			$row['cargoval']=($row['montocif']*$cambioreal)-($row['montocif']*$cambioofi);// Diferencia dolar real e imaginario

			$where = "numero=$dbid";
			$str = $this->db->update_string('ordi', $row, $where);
			$this->db->simple_query($str);
		}

		/*$mmSQL ='SELECT ';
		$mmSQL.="codigo,cantidad, descrip, $participa*100 AS participa,";
		$mmSQL.="costofob,ROUND(costofob*$cambioofi,2) AS fobbs, ";                                                //valor unidad fob
		$mmSQL.="importefob,ROUND(importefob*$cambioofi,2) AS totfobbs,ROUND(gastosi*$cambioofi,2) AS gastosibs,"; //Valores totales
		$mmSQL.='costocif,importecif, ';                                                                           //Valores CIF en BS
		$mmSQL.='arancel,montoaran,gastosn, ';                                                                     //Arancel1
		$mmSQL.='costofinal,importefinal, ';                                                                       //calculo al oficial
		$mmSQL.="ROUND((montoaran+gastosn+((importecif/$cambioofi)*$cambioreal))/cantidad, 2)AS costofinal2,";     //calculo al real
		$mmSQL.="ROUND(montoaran+gastosn+((importecif/$cambioofi)*$cambioreal),2) AS importefinal2 ";              //calculo real
		$mmSQL.='FROM (itordi)';
		$mmSQL.="WHERE numero = $dbid";*/
		return true;
	}

	function cargarordi($control){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('dataform');

		$form = new DataForm("import/ordi/cargarordi/$control/process");

		$form->almacen = new  dropdownField ('Almac&eacute;n', 'almacen');
		$form->almacen->option('','Seleccionar');
		$form->almacen->options("SELECT ubica,CONCAT_WS('-',ubica,ubides) AS val FROM caub WHERE gasto='N' and invfis='N' ORDER BY ubides");
		$form->almacen->rule = 'required';

		/*for($i=1;$i<5;$i++){
			$obj='margen'.$i;
			$form->$obj = new inputField('Margen precio '.$i,$obj);
			$form->$obj->size = 8;
			$form->$obj->rule = 'required';
			$form->$obj->css_class='inputnum';
		}*/

		$form->submit('btnsubmit','Guardar');
		$form->build_form();

		if ($form->on_success()){
			$almacen= $form->almacen->newValue;
			$rt=$this->_cargarordi($control,$almacen);
			if($rt===false){
				$data['content']  = $this->error_string.br();
			}else{
				$data['content']  = "Orden cargada bajo el numero de control $rt ".br();
			}

			$data['content'] .= anchor('import/ordi/dataedit/show/'.$control,'Regresar');
		}else{
			$data['content'] = $form->output;
		}

		$data['head']    = $this->rapyd->get_head();
		$data['title']   = '<h1>Cargar orden de importaci&oacute;n '.str_pad($control,8,0,0).'</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function _cargarordi($id,$depo){
		$error =0;
		$status=$this->datasis->dameval('SELECT status FROM ordi WHERE numero='.$this->db->escape($id));
		$cambioofi =1;
		$cambioreal=1;

		if($status!='C'){
			$SQL='SELECT fecha, fecha AS recep,factura AS numero,proveed,nombre,fecha AS vence FROM ordi WHERE numero=?';
			$query=$this->db->query($SQL,array($id));
			if($query->num_rows()==1){
				$control = $this->datasis->fprox_numero('nscst');
				$transac = $this->datasis->fprox_numero('ntransac');
				$row     = $query->row_array();
				$numero  = substr($row['numero'],-8);
				$serie   = $row['numero'];
				$fecha   = $row['fecha'];
				$proveed = $row['proveed'];

				$row['tipo_doc'] = 'FC';
				$row['serie']    = $serie;
				$row['depo']     = $depo;
				$row['numero']   = $numero;
				$row['control']  = $control;
				$row['transac']  = $transac;
				$row['nfiscal']  = $numero;
				$row['depo']     = $depo;
				$row['montonet'] = 0;
				$row['montoiva'] = 0;
				$row['montotot'] = 0;
				$row['exento']   = 0;
				$row['sobretasa']= 0;
				$row['reducida'] = 0;
				$row['tasa']     = 0;
				$costoreal       = 0;
				$importereal     = 0;
				$tasas=$this->datasis->ivaplica($fecha);

				$itdata=array();
				$sql='SELECT a.codigo,a.descrip,a.cantidad,a.costofinal,a.importefinal,b.iva,
					ROUND(montoaran+gastosn+(costocif*'.$cambioreal.')  ,2) AS costoreal,
					ROUND(montoaran+gastosn+(importecif*'.$cambioreal.'),2) AS importereal,
					precio1,precio2,precio3,precio4
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
						$itdata['costo']   = $itrow->costofinal;
						$itdata['importe'] = $itrow->importefinal;
						$itdata['iva']     = $itrow->iva;
						$itdata['montoiva']= $itrow->importefinal*($itrow->iva/100);
						$itdata['estampa'] = date('Y-m-d');
						$itdata['hora']    = date('h:i:s');
						$itdata['usuario'] = $this->session->userdata('usuario');
						$itdata['ultimo']  = $itrow->costofinal;
						$itdata['precio1'] = $itrow->precio1;
						$itdata['precio2'] = $itrow->precio2;
						$itdata['precio3'] = $itrow->precio3;
						$itdata['precio4'] = $itrow->precio4;
						$mSQL=$this->db->insert_string('itscst', $itdata);
						$ban=$this->db->simple_query($mSQL);
						if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
    
						$row['montonet'] += $itdata['importe']+$itdata['montoiva'];
						$row['montoiva'] += $itdata['montoiva'];
						$row['montotot'] += $itdata['importe'];
						$row['exento']   += ($itrow->iva==0) ? $itdata['importe'] : 0;
						$row['sobretasa']+= ($itrow->iva==$tasas['sobretasa']) ? $itdata['montoiva']: 0;
						$row['reducida'] += ($itrow->iva==$tasas['redutasa'])  ? $itdata['montoiva']: 0;
						$row['tasa']     += ($itrow->iva==$tasas['tasa'])      ? $itdata['montoiva']: 0;
						$costoreal       += $itrow->costoreal;
						$importereal     += $itrow->importereal;
					}
				}

				$row['cstotal']  =0;
				$row['ctotal']   =0;
				$row['cimpuesto']=$row['montoiva'];
				$row['cexento']  =$row['exento'];
				$row['cgenera']  =$row['tasa'];
				$row['civagen']  =$row['tasa']*($tasas['tasa']/100);
				$row['creduci']  =$row['reducida'];
				$row['civared']  =$row['reducida']*($tasas['redutasa']/100);
				$row['cadicio']  =$row['sobretasa'];
				$row['civaadi']  =$row['sobretasa']*($tasas['sobretasa']/100);

				$ssql='SELECT tasa,base,montoiva FROM ordiva WHERE ordeni=?';
				$qqquery=$this->db->query($ssql,array($id));
				if($qqquery->num_rows()>0){
					foreach ($qqquery->result() as $ivarow){
						if($ivarow->tasa==$tasas['tasa']){
							$row['cgenera']  =$ivarow->base;
							$row['civagen']  =$ivarow->montoiva;
						}elseif($ivarow->tasa==$tasas['sobretasa']){
							$row['cadicio']  =$ivarow->base;
							$row['civaadi']  =$ivarow->montoiva;
						}elseif($ivarow->tasa==$tasas['redutasa']){
							$row['creduci']  =$ivarow->base;
							$row['civared']  =$ivarow->montoiva;
						}
					}
				}
				$row['cexento']  = $row['montonet']-($row['creduci']+$row['cadicio']+$row['cgenera']);
				$row['cexento'] += $importereal-$row['montonet'];

				$row['cstotal']  =$row['montotot'];
				$row['ctotal']   =$row['montonet'];
    
				$mSQL=$this->db->insert_string('scst', $row);
				$ban=$this->db->simple_query($mSQL);
				if(!$ban){ memowrite($mSQL,'ordi'); $error++; }
    
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
				$gastosn=$this->datasis->dameval('SELECT SUM(totpre) FROM gser WHERE ordeni='.$dbnum);
				if(empty($gastosn)) $gastosn=0;
				$mSQL="UPDATE ordi SET gastosn=$gastosn WHERE numero=$dbnum";
				$this->db->simple_query($mSQL);
				echo '1';
			}else{
				echo '0';
			}
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
		$monto =$this->datasis->dameval("SELECT SUM(monto) FROM gseri WHERE ordeni=$ordeni");
		if(empty($monto)) $monto=0;

		$data  = array('gastosi' => $monto);
		$where = "numero= $ordeni";
		$str = $this->db->update_string('ordi', $data, $where);
		$this->db->simple_query($str);
		return true;
	}

	function _pre_insert($do){
		$transac=$this->datasis->fprox_numero('transac');
		$usuario=$this->session->userdata('usuario');

		$do->set('usuario',$usuario);
		$do->set('transac',$transac);
		$do->set('estampa',date('ymd'));
		$do->set('hora'   ,date('H:i:s'));

		//Crea el cotenedor
		$data['usuario']    = $usuario;
		$data['status']     = 'A';
		$data['fecha']      = date('Ymd');
		$data['titulo']     = 'Importación '.$do->get('numero');
		$data['proveed']    = $do->get('proveed');
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
		logusu('stra',"ORDI $codigo CREADO");

		$peso=$this->datasis->dameval("SELECT SUM(b.peso) AS peso FROM itordi AS a JOIN sinv AS b ON a.codigo=b.codigo AND a.numero=$codigo");
		if(empty($peso)) $peso=0;
		$data  = array('peso' => $peso);
		$where = "numero= $codigo";
		$str = $this->db->update_string('ordi', $data, $where);
		$this->db->simple_query($str);

		return true;
	}

	function _post_update($do){
		$codigo=$do->get('numero');
		logusu('ordi',"ORDI $codigo MODIFICADO");

		$peso=$this->datasis->dameval("SELECT SUM(b.peso) AS peso FROM itordi AS a JOIN sinv AS b ON a.codigo=b.codigo AND a.numero=$codigo");
		if(empty($peso)) $peso=0;
		$data  = array('peso' => $peso);
		$where = "numero= $codigo";
		$str = $this->db->update_string('ordi', $data, $where);
		$this->db->simple_query($str);
		return true;
	}

	function _post_delete($do){
		$numero  =$do->get('numero');
		$dbnumero=$this->db->escape($numero);
		$mSQL="DELETE FROM gseri WHERE ordeni=$dbnumero";
		$this->db->simple_query($mSQL);

		$mSQL="DELETE FROM ordiva WHERE ordeni=$dbnumero";
		$this->db->simple_query($mSQL);

		$mSQL="UPDATE gser SET ordeni=null WHERE ordeni=$dbnumero";
		$this->db->simple_query($mSQL);

		logusu('ordi',"ORDI orden de importacion numero  $numero ELIMINADO");
		return true;
	}

	function instalar(){
		$mSQL='ALTER TABLE `gser`  ADD COLUMN `ordeni` INT(15) UNSIGNED NULL DEFAULT NULL AFTER `compra`';
		var_dump($this->db->simple_query($mSQL));

		$mSQL="CREATE TABLE `ordi` (
		`numero` int(15) unsigned NOT NULL AUTO_INCREMENT,
		`fecha` date DEFAULT NULL,
		`status` char(1) NOT NULL DEFAULT '' COMMENT 'Estatus de la Compra Abierto, Eliminado y Cerrado',
		`proveed` varchar(5) DEFAULT NULL COMMENT 'Proveedor',
		`nombre` varchar(40) DEFAULT NULL COMMENT 'Nombre del Proveedor',
		`agente` char(5) DEFAULT NULL COMMENT 'Agente Aduanal (Proveedor)',
		`nomage` varchar(40) DEFAULT NULL COMMENT 'Agente Aduanal (Proveedor)',
		`montofob` decimal(19,2) DEFAULT NULL COMMENT 'Total de la Factura extranjera',
		`gastosi` decimal(19,2) DEFAULT NULL COMMENT 'Gastos Internacionales (Fletes, Seguros, etc)',
		`montocif` decimal(19,2) DEFAULT NULL COMMENT 'Monto FOB+gastos Internacionales',
		`aranceles` decimal(19,2) DEFAULT NULL COMMENT 'Suma del Impuesto Arancelario',
		`gastosn` decimal(19,2) DEFAULT NULL COMMENT 'Gastos Nacionales',
		`montotot` decimal(19,2) DEFAULT NULL COMMENT 'Monto CIF + Gastos Nacionales',
		`montoiva` decimal(19,2) DEFAULT NULL COMMENT 'Monto del IVA pagado',
		`montoexc` decimal(12,2) DEFAULT NULL,
		`arribo` date DEFAULT NULL COMMENT 'Fecha de Llegada',
		`factura` varchar(20) DEFAULT NULL COMMENT 'Nro de Factura',
		`cambioofi` decimal(17,2) NOT NULL DEFAULT '0.00' COMMENT 'Cambio Fiscal US$ X Bs.',
		`cambioreal` decimal(17,2) NOT NULL DEFAULT '0.00' COMMENT 'Cambio Efectivamente Aplicado',
		`peso` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Peso total',
		`condicion` text,
		`transac` varchar(8) NOT NULL DEFAULT '',
		`estampa` date NOT NULL DEFAULT '0000-00-00',
		`usuario` varchar(12) NOT NULL DEFAULT '',
		`hora` varchar(8) NOT NULL DEFAULT '',
		`dua` char(30) DEFAULT NULL COMMENT 'DECLARACION UNICA ADUANAS',
		`cargoval` decimal(19,2) DEFAULT NULL COMMENT 'Diferencia Cambiara $ oficial y aplicado',
		`control` varchar(8) DEFAULT NULL COMMENT 'Apuntador a la factura con la que se relaciono',
		`crm` int(11) unsigned DEFAULT NULL COMMENT 'Apuntador al conetendor',
		PRIMARY KEY (`numero`)
		) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC";
		var_dump($this->db->simple_query($mSQL));

		$mSQL="CREATE TABLE `itordi` (
		`numero` int(15) unsigned NOT NULL,
		`fecha` date DEFAULT NULL,
		`codigo` char(15) DEFAULT NULL,
		`descrip` char(45) DEFAULT NULL,
		`cantidad` decimal(10,3) DEFAULT NULL,
		`costofob` decimal(17,2) DEFAULT NULL,
		`importefob` decimal(17,2) DEFAULT NULL,
		`gastosi` decimal(17,2) DEFAULT NULL,
		`costocif` decimal(17,2) DEFAULT NULL,
		`importecif` decimal(17,2) DEFAULT NULL,
		`codaran` char(15) DEFAULT NULL,
		`arancel` decimal(7,2) DEFAULT NULL,
		`montoaran` decimal(17,2) DEFAULT NULL,
		`gastosn` decimal(17,2) DEFAULT NULL,
		`costofinal` decimal(17,2) DEFAULT NULL,
		`importefinal` decimal(17,2) DEFAULT NULL,
		`participam` decimal(7,4) DEFAULT NULL,
		`participao` decimal(7,4) DEFAULT NULL,
		`iva` decimal(17,2) DEFAULT NULL,
		`precio1` decimal(15,2) DEFAULT NULL,
		`precio2` decimal(15,2) DEFAULT NULL,
		`precio3` decimal(15,2) DEFAULT NULL,
		`precio4` decimal(15,2) DEFAULT NULL,
		`estampa` date DEFAULT NULL,
		`hora` char(8) DEFAULT NULL,
		`usuario` char(12) DEFAULT NULL,
		`id` int(20) unsigned NOT NULL AUTO_INCREMENT,
		PRIMARY KEY (`id`),
		KEY `numero` (`numero`)
		) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED";
		var_dump($this->db->simple_query($mSQL));
	}
}





