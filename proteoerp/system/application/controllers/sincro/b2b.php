<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
class b2b extends validaciones {

	function b2b(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('921',1);
	}

	function index(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');
		$bSPRV=$this->datasis->modbus($mSPRV);

		$mGRUP=array(
			'tabla'   =>'grup',
			'columnas'=>array(
				'grupo' =>'Grupo',
				'nom_grup'=>'Nombre'),
			'filtro'  =>array('grupo'=>'Grupo','nom_grup'=>'Nombre'),
			'retornar'=>array('grupo'=>'grupo'),
			'titulo'  =>'Buscar Grupo');
		$bGRUP=$this->datasis->modbus($mGRUP);

		$atts = array(
				'width'      => '400',
				'height'     => '300',
				'scrollbars' => 'yes',
				'status'     => 'yes',
				'resizable'  => 'yes',
				'screenx'   => "'+((screen.availWidth/2)-200)+'",
				'screeny'   => "'+((screen.availHeight/2)-150)+'"
			);

		$filter = new DataFilter('Filtro de B2B');
		$filter->db->select(array('a.id','a.proveed','a.usuario','a.depo AS depo','a.tipo','a.url','a.grupo','b.nombre','c.ubides'));
		$filter->db->from('b2b_config AS a');
		$filter->db->join('sprv AS b','b.proveed=a.proveed','left');
		$filter->db->join('caub AS c','c.ubica=a.depo','left');

		$filter->proveed = new inputField('C&oacute;digo local', 'proveed');
		$filter->proveed->append($bSPRV);
		$filter->proveed->size=25;

		$filter->depo = new dropdownField('Almac&eacute;n','depo');
		$filter->depo->option("","Seleccionar");
		$filter->depo->options("SELECT ubica, ubides FROM caub ORDER BY ubica");

		$filter->tipo = new dropdownField('Tipo','tipo');
		$filter->tipo->option('' ,'Selecione un tipo');
		$filter->tipo->option('I','Inventario');
		$filter->tipo->option('G','Gastos');

		$filter->grupo = new inputField('Grupo', 'grupo');
		$filter->grupo->append($bGRUP);
		$filter->grupo->size=25;

		$filter->buttons('reset','search');
		$filter->build();

		$acti =anchor_popup('/sincro/b2b/traecompra/<#id#>'  ,'Compras'       ,$atts);
		$acti3=anchor_popup('/sincro/b2b/traedevolu/<#id#>'  ,'Devoluciones'  ,$atts);
		$acti2=anchor_popup('/sincro/b2b/traeconsigna/<#id#>','Consignaciones',$atts);
		$link=anchor('/sincro/b2b/dataedit/show/<#id#>','<#id#>');
		$grid = new DataGrid('lista de conexiones definidas');
		$grid->order_by('id','asc');
		$grid->per_page = 15;

		$grid->column_orderby('N&uacute;mero' ,$link    ,'id');
		$grid->column_orderby('Proveedor'     ,'nombre' ,'proveed');
		$grid->column_orderby('Url'           ,'url'    ,'url');
		$grid->column_orderby('Usuario'       ,'usuario','usuario');
		$grid->column_orderby('Tipo'          ,'tipo'   ,'tipo');
		$grid->column_orderby('Almac&eacute;n','ubides' ,'depo');
		$grid->column_orderby('Grupo'         ,'grupo'  ,'grupo');
		$grid->column('Traer' ,$acti.' | '.$acti3.' | '.$acti2);
		$grid->add('sincro/b2b/dataedit/create');
		$action = "javascript:window.location='".site_url('sincro/b2b/traecomprauniq')."'";
		$grid->button('btn_uniq', 'Descarga Unitaria', $action, 'TR');
		$grid->build();

		$smenu['link']   =barra_menu('921');
		$data['content'] =$filter->output. $grid->output;
		$data['title']   = heading('B2B');
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$mGRUP=array(
			'tabla'   =>'grup',
			'columnas'=>array(
				'grupo' =>'Grupo',
				'nom_grup'=>'Nombre'),
			'filtro'  =>array('grupo'=>'Grupo','nom_grup'=>'Nombre'),
			'retornar'=>array('grupo'=>'grupo'),
			'titulo'  =>'Buscar Grupo');
		$bGRUP=$this->datasis->modbus($mGRUP);

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');
		$bSPRV=$this->datasis->modbus($mSPRV);

		$script='
		<script language="javascript" type="text/javascript">
			$(function(){ $(".inputnum").numeric("."); });
		</script>';

		$edit = new DataEdit('B2B', 'b2b_config');
		$edit->pre_process('insert','_pre_inup');
		$edit->pre_process('update','_pre_inup');
		$edit->back_url = site_url('sincro/b2b/index/');

		$edit->proveed = new inputField('C&oacute;digo local', 'proveed');
		$edit->proveed->size      =  15;
		$edit->proveed->maxlength =  15;
		$edit->proveed->rule      = 'required';
		$edit->proveed->append($bSPRV);

		$edit->prefijo = new inputField('Prefijo', 'prefijo');
		$edit->prefijo->size      =  6;
		$edit->prefijo->maxlength =  5;
		$edit->prefijo->rule      = 'required';
		$edit->prefijo->append('Prefijo de los c&oacute;digos provenientes de este proveedor.');

		$edit->url = new inputField('Direcci&oacute;n Url', 'url');
		$edit->url->insertValue='http://';
		$edit->url->size       =  50;
		$edit->url->maxlength  =  50;
		$edit->url->rule       = 'required|trim';
		$edit->url->append('Ej: http://www.ejemplo.com');

		$edit->puerto = new inputField('Puerto', 'puerto');
		$edit->puerto->insertValue=  80;
		$edit->puerto->size       =  5;
		$edit->puerto->maxlength  =  20;
		$edit->puerto->rule       = 'required|numeric';

		$edit->proteo = new inputField('Ruta a proteo', 'proteo');
		$edit->proteo->insertValue='proteoerp';
		$edit->proteo->size       =  20;
		$edit->proteo->rule       = 'trim';
		$edit->proteo->maxlength  =  20;

		$edit->usuario = new inputField('C&oacute;digo remoto', 'usuario');
		$edit->usuario->size      =  20;
		$edit->usuario->maxlength =  20;
		$edit->usuario->rule      = 'trim';
		$edit->usuario->rule      = 'required';

		$edit->clave = new inputField('Clave', 'clave');
		$edit->clave->size      =  10;
		$edit->clave->maxlength =  10;
		$edit->clave->rule      = 'trim';
		$edit->clave->rule      = 'required';

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('' ,'Seleccione un tipo');
		$edit->tipo->option('I','Inventario');
		$edit->tipo->option('G','Gastos');
		$edit->tipo->style ='50px';
		$edit->tipo->rule  ='required';

		$edit->depo = new dropdownField('Almac&eacute;n','depo');
		$edit->depo->option('','Seleccionar');
		$edit->depo->options('SELECT ubica,ubides FROM caub');
		$edit->depo->style ='250px';
		$edit->depo->rule  ='required';

		for($i=1;$i<=5;$i++){
			$obj='margen'.$i;
			$edit->$obj = new inputField('Margen '.$i, $obj);
			$edit->$obj->size      = 15;
			$edit->$obj->maxlength = 15;
			$edit->$obj->css_class = 'inputnum';
			$edit->$obj->rule      = 'callback_chporcent';
			$edit->$obj->group = 'Margenes de ganancia';
			$edit->$obj->autocomplete = false;
			if($i==5) $edit->$obj->append('Solo aplica a supermercados');
		}

		$edit->grupo = new inputField('Grupo', 'grupo');
		$edit->grupo->size      =  10;
		$edit->grupo->maxlength =  6;
		$edit->grupo->rule      = 'required';
		$edit->grupo->append($bGRUP);

		$edit->buttons('modify', 'save','undo', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head().$script;
		$data['title']   = heading('Editar b2b');
		$this->load->view('view_ventanas', $data);
	}

	//Para visualizar las transacciones descargadas
	function scstfilter(){
		$this->rapyd->load('datagrid','datafilter');
		$this->rapyd->uri->keep_persistence();

		$atts = array(
				'width'      => '800',
				'height'     => '600',
				'scrollbars' => 'yes',
				'status'     => 'yes',
				'resizable'  => 'yes',
				'screenx'   => "'+((screen.availWidth/2)-400)+'",
				'screeny'   => "'+((screen.availHeight/2)-300)+'"
			);
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

		$filter = new DataFilter('Filtro de Compras');
		$filter->db->select=array('numero','fecha','vence','nombre','montoiva','montonet','proveed','control');
		$filter->db->from('b2b_scst');

		$filter->fechad = new dateonlyField('Desde', 'fechad','d/m/Y');
		$filter->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause ='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator=">=";
		$filter->fechah->operator="<=";

		$filter->numero = new inputField('Factura', 'numero');
		$filter->numero->size=20;

		$filter->proveedor = new inputField('Proveedor','proveed');
		$filter->proveedor->append($boton);
		$filter->proveedor->db_name = 'proveed';
		$filter->proveedor->size=20;

		$action = "javascript:window.location='".site_url('sincro/b2b/index')."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'TR');
		$filter->buttons('reset','search');
		$filter->build();

		$uri = anchor('sincro/b2b/scstedit/show/<#id#>','<#numero#>');

		$grid = new DataGrid();
		$grid->order_by('fecha','desc');
		$grid->per_page = 15;
		$grid->column_orderby('Factura',$uri,'control');
		$grid->column_orderby('Fecha'  ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha',"align='center'");
		$grid->column_orderby('Vence'  ,'<dbdate_to_human><#vence#></dbdate_to_human>','vence',"align='center'");
		$grid->column_orderby('Nombre' ,'nombre','nombre');
		$grid->column_orderby('IVA'    ,'montoiva' ,'montoiva',"align='right'");
		$grid->column_orderby('Monto'  ,'montonet' ,'montonet',"align='right'");
		$grid->column_orderby('Control','pcontrol' ,'pcontrol',"align='right'");
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] =$filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   =heading('Compras de B2B');
		$this->load->view('view_ventanas', $data);
	}

	function scstedit(){
		$this->rapyd->load('dataedit','datadetalle','fields','datagrid');
		$this->rapyd->uri->keep_persistence();

		function exissinv($cen,$id=0){
			if(empty($cen)){
				$id--;
				$rt =form_button('create' ,'Crear','onclick="pcrear('.$id.');"');
				$rt.=form_button('asignar','Asig.','onclick="pasig('.$id.');"');
			}else{
				$rt='--';
			}
			return $rt;
		}

		$edit = new DataEdit('Compras','b2b_scst');
		$edit->back_url = 'sincro/b2b/scstfilter/';

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->mode ='autohide';
		$edit->fecha->size = 10;

		$edit->numero = new inputField("N&uacute;mero", "numero");
		$edit->numero->size = 15;
		$edit->numero->rule= 'required';
		$edit->numero->mode= 'autohide';
		$edit->numero->maxlength=8;

		$edit->proveedor = new inputField("Proveedor", "proveed");
		$edit->proveedor->size = 10;
		$edit->proveedor->maxlength=5;

		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size = 50;
		$edit->nombre->maxlength=40;

		$edit->almacen = new inputField("Almac&eacute;n", "depo");
		$edit->almacen->size = 15;
		$edit->almacen->maxlength=8;

		$edit->tipo = new dropdownField("Tipo", "tipo_doc");
		$edit->tipo->option("FC","FC");
		$edit->tipo->rule = "required";
		$edit->tipo->size = 20;
		$edit->tipo->style='width:150px;';

		$edit->subt  = new inputField("Sub-total", "montotot");
		$edit->subt->size = 20;
		$edit->subt->css_class='inputnum';

		$edit->iva  = new inputField("Impuesto", "montoiva");
		$edit->iva->size = 20;
		$edit->iva->css_class='inputnum';

		$edit->total  = new inputField("Total global", "montonet");
		$edit->total->size = 20;
		$edit->total->css_class='inputnum';

		$edit->pcontrol  = new inputField('Control', 'pcontrol');
		$edit->pcontrol->size = 12;

		//$numero =$edit->_dataobject->get('control');
		$id =$edit->_dataobject->get('id');
		$proveed=$this->db->escape($edit->_dataobject->get('proveed'));

		$atts = array(
			'width'     => '250',
			'height'    => '250',
			'scrollbars'=> 'no',
			'status'    => 'no',
			'resizable' => 'no',
			'screenx'   => "'+((screen.availWidth/2)-175)+'",
			'screeny'   => "'+((screen.availHeight/2)-175)+'"
		);
		$llink=anchor_popup('sincro/b2b/reasignaprecio/modify/<#id#>', '<b><#precio1#></b>', $atts);

		//Campos para el detalle
		$tabla=$this->db->database;
		$detalle = new DataGrid('');
		$select=array('a.*','b.descrip AS sinvdesc','a.codigo AS barras','a.costo AS pond','a.codigolocal  AS sinv','a.codigolocal');
		$detalle->db->select($select);
		$detalle->db->from('b2b_itscst AS a');
		$detalle->db->where('a.id_scst',$id);
		$detalle->db->join('sinv AS b','a.codigolocal=b.codigo','LEFT');
		$detalle->use_function('exissinv');
		$detalle->column('Codigo sistema'    ,'<sinulo><#codigolocal#>|No tiene</sinulo>' );
		$detalle->column('Codigo prov.'      ,'<#codigo#>'   );
		$detalle->column('Descrip. Proveedor','<#descrip#>'  );
		$detalle->column('Descrip. Sistema'  ,'<#sinvdesc#>' );
		$detalle->column('Cantidad'          ,'<#cantidad#>' ,"align='right'");
		$detalle->column('PVP'               ,$llink         ,"align='right'");
		$detalle->column('Costo'             ,'<#ultimo#>'   ,"align='right'");
		$detalle->column('Importe'           ,'<#importe#>'  ,"align='right'");
		$detalle->build();
		//echo $detalle->db->last_query();

		$edit->detalle=new freeField('detalle', 'detalle',$detalle->output);
		$accion="javascript:window.location='".site_url('sincro/b2b/cargacompra'.$edit->pk_URI())."'";
		$pcontrol=$edit->_dataobject->get('pcontrol');
		if(empty($pcontrol)){
			$edit->button_status('btn_cargar','Cargar',$accion,'TR','show');
		}else{
			$sql ='SELECT COUNT(*) FROM scst WHERE control='.$this->db->escape($pcontrol);
			$cana=$this->datasis->dameval($sql);
			if(empty($cana)) $edit->button_status('btn_cargar','Cargar',$accion,'TR','show');
		}
		$edit->buttons('save','undo','back');

		//$edit->script($script,'show');
		$edit->build();

		$this->rapyd->jquery[]='$("#dialog").dialog({
			autoOpen: false,
			show: "blind",
			hide: "explode"
		});
		$( "#opener" ).click(function() {
			$( "#dialog" ).dialog( "open" );
			return false;
		});';

		$conten['form']  =&  $edit;
		$data['content'] = $this->load->view('view_b2b_compras', $conten,true);
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Compras Descargadas');
		$this->load->view('view_ventanas', $data);
	}

	//Consignacion de inventario
	function sconfilter(){
		$this->rapyd->load('datagrid','datafilter');

		$atts = array(
		'width'      => '800',
		'height'     => '600',
		'scrollbars' => 'yes',
		'status'     => 'yes',
		'resizable'  => 'yes',
		'screenx'    => '0',
		'screeny'    => '0'
		);

		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'clipro'),
			'titulo'  =>'Buscar Proveedor');
		$boton=$this->datasis->modbus($modbus);

		$filter = new DataFilter('Filtro','b2b_scon');

		$filter->fechad = new dateonlyField('Desde', 'fechad');
		$filter->fechah = new dateonlyField('Hasta', 'fechah');
		$filter->fechad->clause  =$filter->fechah->clause ='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size = 30;

		$filter->clipro = new inputField("Proveedor",'clipro');
		$filter->clipro->size = 30;
		$filter->clipro->append($boton);

		$action = "javascript:window.location='".site_url('sincro/b2b/index')."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'TR');
		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('sincro/b2b/sconedit/show/<#id#>','<#numero#>');

		$grid = new DataGrid();
		$grid->order_by('numero','desc');
		$grid->per_page = 15;

		$grid->column_orderby('N&uacute;mero' ,$uri,'numero');
		$grid->column_orderby('Cargada' ,'<siinulo><#pid#>|No|Si</siinulo>','pid');
		$grid->column_orderby('Fecha'         ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha',"align='center'");
		$grid->column_orderby('Nombre'        ,'nombre','nombre');
		$grid->column_orderby('Sub.Total'     ,'<nformat><#stotal#></nformat>'  ,'stotal','align=\'right\'');
		$grid->column_orderby('IVA'           ,'<nformat><#impuesto#></nformat>','iva'   ,'align=\'right\'');
		$grid->column_orderby('Total'         ,'<nformat><#gtotal#></nformat>'  ,'gtotal','align=\'right\'');

		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Consignaci&oacute;n de inventario');
		$this->load->view('view_ventanas', $data);
	}

	function sconedit(){
		$this->rapyd->load('dataobject','datadetails');

		$do = new DataObject('b2b_scon');
		$do->rel_one_to_many('itscon', 'b2b_itscon', array('id'=>'id_scon'));
		$do->rel_pointer('itscon','sinv','b2b_itscon.codigo=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');

		$edit = new DataDetails('Pr&eacute;stamo de inventario', $do);
		$edit->back_url = site_url('inventario/psinv/filteredgrid');
		$edit->set_rel_title('itscon','Producto <#o#>');

		$edit->back_url = site_url('sincro/b2b/sconfilter'); //$this->back_dataedit;

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength=8;
		$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');
		$edit->numero->mode='autohide';

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->readonly  = true;
		$edit->peso->size      = 10;
		$edit->peso->mode      = 'autohide';

		$edit->pid = new inputField('Ref. local', 'pid');
		$edit->pid->mode      = 'autohide';

		$edit->clipro = new inputField('Proveedor','clipro');
		$edit->clipro->size     = 6;
		$edit->clipro->maxlength= 5;
		$edit->clipro->rule     = 'required';
		$edit->clipro->mode     = 'autohide';

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 25;
		$edit->nombre->maxlength=40;
		$edit->nombre->autocomplete=false;
		$edit->nombre->mode='autohide';

		$edit->almacen = new  dropdownField ('Almac&eacute;n', 'almacen');
		$edit->almacen->options('SELECT ubica, CONCAT(ubica,\' \',ubides) nombre FROM caub ORDER BY ubica');
		$edit->almacen->rule = 'required';
		$edit->almacen->style='width:200px;';
		$edit->almacen->size = 5;
		$edit->almacen->mode='autohide';

		$edit->asociado = new inputField('Asociado', 'asociado');
		$edit->asociado->mode='autohide';
		$edit->asociado->size = 10;

		$edit->direc1 = new inputField('Direcci&oacute;n', 'direc1');
		$edit->direc1->mode= 'autohide';
		$edit->direc1->size = 37;

		$edit->observ1 = new inputField('Observaci&oacute;n', 'observ1');
		$edit->observ1->size = 37;

		$edit->tipod = new dropdownField('Tipo de movimiento', 'tipod');
		$edit->tipod->option('E','Entregado');
		$edit->tipod->option('R','Recibido');
		$edit->tipod->mode = 'autohide';
		$edit->tipod->style='width:160px';

		//Campos para el detalle
		$edit->codigo = new inputField('C&oacute;digo <#o#>', 'codigo_<#i#>');
		$edit->codigo->size     = 12;
		$edit->codigo->db_name  = 'codigo';
		$edit->codigo->readonly = true;
		$edit->codigo->rel_id   = 'itscon';
		$edit->codigo->rule     = 'required';

		$edit->codigolocal = new inputField('C&oacute;digo local <#o#>', 'codigolocal_<#i#>');
		$edit->codigolocal->size     = 12;
		$edit->codigolocal->db_name  = 'codigolocal';
		$edit->codigolocal->readonly = true;
		$edit->codigolocal->rel_id   = 'itscon';
		$edit->codigolocal->rule     = 'required';

		$edit->desca = new inputField('Descripci&oacute;n <#o#>', 'desca_<#i#>');
		$edit->desca->size=36;
		$edit->desca->db_name='desca';
		$edit->desca->maxlength=45;
		$edit->desca->readonly  = true;
		$edit->desca->rel_id='itscon';

		$edit->cana = new inputField('Cantidad <#o#>', 'cana_<#i#>');
		$edit->cana->db_name  = 'cana';
		$edit->cana->css_class= 'inputnum';
		$edit->cana->rel_id   = 'itscon';
		$edit->cana->maxlength= 10;
		$edit->cana->size     = 5;
		$edit->cana->rule     = 'required|positive';
		$edit->cana->autocomplete=false;
		$edit->cana->readonly = true;
		$edit->cana->onkeyup  ='importe(<#i#>)';

		$edit->recibido = new inputField('Cantidad recibida <#o#>', 'recibido_<#i#>');
		$edit->recibido->db_name  = 'recibido';
		$edit->recibido->css_class= 'inputnum';
		$edit->recibido->rel_id   = 'itscon';
		$edit->recibido->maxlength= 10;
		$edit->recibido->size     = 5;
		$edit->recibido->rule     = 'required|positive';
		$edit->recibido->autocomplete=false;
		$edit->recibido->onkeyup  ='importe(<#i#>)';

		$edit->precio = new inputField('Precio <#o#>', 'precio_<#i#>');
		$edit->precio->db_name   = 'precio';
		$edit->precio->css_class = 'inputnum';
		$edit->precio->rel_id    = 'itscon';
		$edit->precio->size      = 10;
		$edit->precio->rule      = 'required|positive|callback_chpreca[<#i#>]';
		$edit->precio->readonly  = true;

		$edit->importe = new inputField('Importe <#o#>', 'importe_<#i#>');
		$edit->importe->db_name='importe';
		$edit->importe->size=10;
		$edit->importe->readonly=false;
		$edit->importe->css_class='inputnum';
		$edit->importe->rel_id   ='itscon';

		for($i=1;$i<=4;$i++){
			$obj='precio'.$i;
			$edit->$obj = new hiddenField('Precio <#o#>', $obj.'_<#i#>');
			$edit->$obj->db_name   = 'sinv'.$obj;
			$edit->$obj->rel_id    = 'itscon';
			$edit->$obj->pointer   = true;
		}
		$edit->itiva = new hiddenField('', 'itiva_<#i#>');
		$edit->itiva->db_name  = 'iva';
		$edit->itiva->rel_id   = 'itscon';

		$edit->sinvpeso = new hiddenField('', 'sinvpeso_<#i#>');
		$edit->sinvpeso->db_name   = 'sinvpeso';
		$edit->sinvpeso->rel_id    = 'itscon';
		$edit->sinvpeso->pointer   = true;

		$edit->sinvtipo = new hiddenField('', 'sinvtipo_<#i#>');
		$edit->sinvtipo->db_name   = 'sinvtipo';
		$edit->sinvtipo->rel_id    = 'itscon';
		$edit->sinvtipo->pointer   = true;
		//fin de campos para detalle

		$edit->impuesto = new inputField('Impuesto', 'impuesto');
		$edit->impuesto->size = 20;
		$edit->impuesto->mode='autohide';
		$edit->impuesto->css_class='inputnum';
		$edit->impuesto->readonly=false;

		$edit->stotal = new inputField('Sub.Total', 'stotal');
		$edit->stotal->size = 20;
		$edit->stotal->mode='autohide';
		$edit->stotal->css_class='inputnum';

		$edit->gtotal = new inputField('Total', 'gtotal');
		$edit->gtotal->size = 20;
		$edit->gtotal->mode='autohide';
		$edit->gtotal->css_class='inputnum';

		$pid=$edit->get_from_dataobjetct('pid');
		if(empty($pid)){
			$action = "javascript:window.location='".site_url('sincro/b2b/cargascon/'.$edit->_dataobject->pk['id'])."'";
			$edit->button_status('btn_conci', 'Cargar Consignaci&oacute;n', $action, 'TR','show');
		}

		$edit->buttons('save', 'undo', 'delete', 'back');
		$edit->build();

		$conten['form']  =&  $edit;
		$data['content'] = $this->load->view('view_b2b_scon', $conten,true);
		$data['title']   = heading('Consiganci&oacute;n de inventario');
		$data['head']    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head().phpscript('nformat.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}


//****************************************************
// Metodos para gestionar transacciones como compras
//****************************************************
	function traedevolu($par,$ultimo=null){
		$rt = $this->_trae_devolu($par,$ultimo);
		if($rt==0){
			$str=$this->comprasCargadas.' transacciones descargadas';
		}else{
			$str='Hubo problemas durante la  descarga, se generar&oacute;n centinelas';
		}
		$data['content'] = $str;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Devoluciones Descargadas');
		$this->load->view('view_ventanas_sola', $data);
	}

	function traecompra($par,$ultimo=null){
		$rt = $this->_trae_compra($par,$ultimo);
		if($rt==0){
			$str=$this->comprasCargadas.' transacciones descargadas';
		}else{
			$str='Hubo problemas durante la  descarga, se generar&oacute;n centinelas';
		}
		$data['content'] = $str;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Compras Descargadas');
		$this->load->view('view_ventanas_sola', $data);
	}

	function traecomprauniq(){
		$this->rapyd->load('dataform');

		$form = new DataForm('sincro/b2b/traecomprauniq/process');

		$form->par = new dropdownField('Proveedor', 'proveed');
		$form->par->option('','Seleccionar');
		$form->par->options('SELECT a.id,b.nombre FROM b2b_config AS a JOIN sprv AS b ON a.proveed=b.proveed ORDER BY b.nombre');
		$form->par->rule='required';

		$form->tipo = new dropdownField('Tipo de transacci&oacute;n', 'tipo');
		$form->tipo->option('scst','Compra');
		$form->tipo->option('devo','Devolucion');
		$form->tipo->option('scon','Consignacion');
		$form->tipo->rule='required';

		$form->numero = new inputField('Referencia','numero');
		$form->numero->rule = 'required';
		$form->numero->size = 10;

		$action = "javascript:window.location='".site_url('sincro/b2b/index')."'";
		$form->button('btn_regresar', 'Regresar', $action, 'BR');

		$form->submit('btnsubmit','Traer');
		$form->build_form();

		$msj='';
		if ($form->on_success()){
			$id     = $form->par->newValue;
			$ultimo = $form->numero->newValue;
			if($form->tipo->newValue=='scst'){
				$this->_trae_compra($id,$ultimo,true);
				$msj=$this->comprasCargadas.' transacciones descargadas';
			}elseif($form->tipo->newValue=='devol'){
				$this->_trae_devolu($id,$ultimo,true);
				$msj=$this->comprasCargadas.' transacciones descargadas';
			}else{
				$msj='M&eacute;todo aun no definido';
			}
		}

		$data['content'] = $msj.$form->output;
		$data['title']   = heading('Traer transacci&oacute;nes');
		$data['head']    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head().phpscript('nformat.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function _trae_compra_devolu($id,$ultimo,$uniq,$metodo){
		$this->comprasCargadas=0;
		if(is_null($id)) return false; else $id=$this->db->escape($id);
		$contribu=$this->datasis->traevalor('CONTRIBUYENTE');
		$rif     =$this->datasis->traevalor('RIF');

		$config=$this->datasis->damerow("SELECT proveed,grupo,puerto,proteo,url,usuario,clave,tipo,depo,margen1,margen2,margen3,margen4,margen5,prefijo FROM b2b_config WHERE id=${id}");
		if(count($config)==0) return false;
		$prefijo= (empty($config['prefijo']))? '':$config['prefijo'];

		$er=0;
		$this->load->helper('url');
		$server_url = reduce_double_slashes(trim($config['url']).'/'.trim($config['proteo']).'/'.'rpcserver');

		$this->load->library('xmlrpc');
		$this->xmlrpc->xmlrpc_defencoding=$this->config->item('charset');
		//$this->xmlrpc->set_debug(TRUE);
		$puerto= (empty($config['puerto'])) ? 80 : $config['puerto'];

		$this->xmlrpc->server($server_url , $puerto);
		$this->xmlrpc->method($metodo);

		if(is_null($ultimo)){
			$dbtipo = ($metodo=='cea')? "'FC'" : "'NC'";
			$ufac=$this->datasis->dameval('SELECT MAX(numero) FROM b2b_scst WHERE proveed='.$this->db->escape($config['proveed']).' AND tipo_doc='.$dbtipo );
			if(empty($ufac)) $ufac=0;
		}elseif(is_numeric($ultimo)){
			$ufac=$ultimo;
		}else{
			$ufac=0;
		}

		$request = array($ufac,$config['proveed'],$config['usuario'],md5($config['clave']),$uniq);
		$this->xmlrpc->request($request);

		if (!$this->xmlrpc->send_request()){
			memowrite($this->xmlrpc->display_error(),'B2B');
			return true;
		}else{
			$res=$this->xmlrpc->display_response();
			foreach($res AS $ind=>$compra){
				$this->comprasCargadas++;
				$arr=unserialize($compra);
				foreach($arr['scst'] AS $in => $val) $arr[$in]=base64_decode($val);

				$proveed=$config['proveed'];
				$pnombre=$this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$this->db->escape($proveed));

				$iid=$this->datasis->dameval('SELECT id FROM b2b_scst WHERE proveed='.$this->db->escape($proveed).' AND numero='.$this->db->escape($arr['numero']));
				if(!empty($iid)){
					$this->db->simple_query('DELETE FROM b2b_scst WHERE id='.$iid);
					$this->db->simple_query('DELETE FROM b2b_itscst WHERE id_scst='.$iid);
				}

				$data['proveed']  = $proveed;
				$data['nombre']   = $pnombre;
				$data['tipo_doc'] = ($metodo=='cea')? 'FC' : 'NC';
				$data['depo']     = $config['depo'];
				$data['fecha']    = $arr['fecha'];
				$data['vence']    = $arr['vence'];
				$data['numero']   = $arr['numero'];
				$data['nfiscal']  = $arr['nfiscal'];
				$data['serie']    = $arr['numero'];
				$data['montotot'] = $arr['totals'];
				$data['montoiva'] = $arr['iva'];
				$data['montonet'] = $arr['totalg'];

				if($contribu=='ESPECIAL' and strtoupper($rif[0])!='V'){
					$por_rete=$this->datasis->dameval('SELECT reteiva FROM sprv WHERE proveed='.$this->db->escape($proveed));
					if($por_rete!=100){
						$por_rete=0.75;
					}else{
						$por_rete=$por_rete/100;}
					$data['reteiva']=round($data['montoiva']*$por_rete,2);
				}

				$data['reducida'] =$arr['monredu'];
				$data['tasa']     =$arr['montasa'];
				$data['sobretasa']=$arr['monadic'];
				$data['monredu']  =$arr['monredu'];
				$data['montasa']  =$arr['montasa'];
				$data['monadic']  =$arr['monadic'];

				$data['ctotal']   =$arr['totalg'];
				$data['cstotal']  =$arr['totals'];
				$data['cexento']  =$arr['exento'];
				$data['cimpuesto']=$arr['iva'];
				$data['cgenera']  =$arr['montasa'];
				$data['civagen']  =$arr['tasa'];
				$data['cadicio']  =$arr['monadic'];
				$data['civaadi']  =$arr['sobretasa'];
				$data['creduci']  =$arr['monredu'];
				$data['civared']  =$arr['reducida'];

				$mSQL=$this->db->insert_string('b2b_scst',$data);

				$rt=$this->db->simple_query($mSQL);
				if(!$rt){
					memowrite($mSQL,'B2B');
					$maestro=false;
					$er++;
				}else{
					$id_scst=$this->db->insert_id();
					$maestro=true;
				}

				if($maestro){
					$itscst =& $arr['itscst'];
					foreach($itscst AS $in => $aarr){
						foreach($aarr AS $i=>$val) $arr[$in][$i]=base64_decode($val);

						//Arregla los precios en caso de llegar malos
						for($j=1;$j<5;$j++){
							$ind='precio'.$j;
							if($arr[$in][$ind]<0 && $j>1){
								$ind2='precio'.$i-1;
								$arr[$in][$ind]=$arr[$in][$ind];
							}elseif($arr[$in][$ind]<0 && $j==1){
								$arr[$in][$ind]=round(($arr[$in]['preca']*100/(100-$config['margen1'])),2);
							}
						}

						$barras=trim($arr[$in]['barras']);
						$ddata['id_scst']  = $id_scst;
						$ddata['proveed']  = $proveed;
						$ddata['fecha']    = $data['fecha'];
						$ddata['numero']   = $data['numero'];
						$ddata['depo']     = $data['depo'];
						$ddata['codigo']   = $prefijo.trim($arr[$in]['codigoa']);
						$ddata['descrip']  = $arr[$in]['desca'];
						$ddata['cantidad'] = $arr[$in]['cana'];
						$ddata['costo']    = $arr[$in]['preca'];
						$ddata['importe']  = $arr[$in]['tota'];
						$ddata['garantia'] = 0;
						$ddata['ultimo']   = $arr[$in]['preca'];
						$ddata['precio1']  = ($config['margen1']==0)? $arr[$in]['precio1'] : round(($arr[$in]['preca']*100/(100-$config['margen1'])),2);
						$ddata['precio2']  = ($config['margen2']==0)? $arr[$in]['precio2'] : round(($arr[$in]['preca']*100/(100-$config['margen2'])),2);
						$ddata['precio3']  = ($config['margen3']==0)? $arr[$in]['precio3'] : round(($arr[$in]['preca']*100/(100-$config['margen3'])),2);
						$ddata['precio4']  = ($config['margen4']==0)? $arr[$in]['precio4']*0.99 : round(($arr[$in]['preca']*100/(100-$config['margen4'])),2);
						$ddata['montoiva'] = $arr[$in]['tota']*($arr[$in]['iva']/100);
						$ddata['iva']      = $arr[$in]['iva'];
						$ddata['barras']   = $barras;

						//procedimiento de determinacion del codigo del articulo en sistema local
						$codigolocal=false;
						if(!empty($barras)){
							$mSQL_p = 'SELECT codigo FROM sinv';
							$bbus   = array('codigo','barras','alterno');
							$query=$this->_gconsul($mSQL_p,$barras,$bbus);
							if($query){
								$row = $query->row();
								$codigolocal=$row->codigo;
							}
						}
						if($codigolocal==false && !empty($ddata['codigo'])){
							$mSQL_p = 'SELECT codigo FROM sinv';
							$bbus   = array('codigo','barras','alterno');
							$query=$this->_gconsul($mSQL_p,$ddata['codigo'],$bbus);
							if($query){
								$row = $query->row();
								$codigolocal=$row->codigo;
								//Relaciona el nuevo codigo de barras
								if(!empty($barras)){
									$arr_barraspos=array('codigo'=>$codigolocal,'suplemen'=>$barras);
									$mSQL=$this->db->insert_string('barraspos',$arr_barraspos);
									$rt=$this->db->simple_query($mSQL);
									if(!$rt){
										memowrite($mSQL,'B2B');
										$er++;
									}
								}
							}
						}
						//if($codigolocal===false AND $this->db->table_exists('sinvprov')){
						//	$codigolocal=$this->datasis->dameval('SELECT codigo FROM sinvprov WHERE proveed='.$this->db->escape($proveed).' AND codigop='.$this->db->escape($arr[$in]['codigoa']));
						//}

						//Si no existe lo crea
						if(empty($codigolocal)){
							$base1 = ($arr[$in]['precio1']*100)/(100+$arr[$in]['iva']);
							$base2 = ($arr[$in]['precio2']*100)/(100+$arr[$in]['iva']);
							$base3 = ($arr[$in]['precio3']*100)/(100+$arr[$in]['iva']);
							$base4 = ($arr[$in]['precio4']*99)/(100+$arr[$in]['iva']);
							$invent['codigo']   = $ddata['codigo'];
							$invent['barras']   = $barras;
							$invent['grupo']    = $config['grupo'];
							$invent['prov1']    = $proveed;
							$invent['descrip']  = $arr[$in]['desca'];
							$invent['existen']  = $arr[$in]['cana'];
							$invent['pond']     = $arr[$in]['preca'];
							$invent['ultimo']   = $arr[$in]['preca'];
							$invent['unidad']   = $arr[$in]['unidad'];
							$invent['tipo']     = $arr[$in]['tipo'];
							$invent['tdecimal'] = $arr[$in]['tdecimal'];
							$invent['margen1']  = round(100-($arr[$in]['preca']*100/$base1),2);
							$invent['margen2']  = round(100-($arr[$in]['preca']*100/$base2),2);
							$invent['margen3']  = round(100-($arr[$in]['preca']*100/$base3),2);
							$invent['margen4']  = round(100-($arr[$in]['preca']*100/$base4),2);
							$invent['base1']    = round($base1,2);
							$invent['base2']    = round($base2,2);
							$invent['base3']    = round($base3,2);
							$invent['base4']    = round($base4,2);
							$invent['precio1']  = ($config['margen1']==0)? $arr[$in]['precio1'] : round(($arr[$in]['preca']*100/(100-$config['margen1'])),2);
							$invent['precio2']  = ($config['margen2']==0)? $arr[$in]['precio2'] : round(($arr[$in]['preca']*100/(100-$config['margen2'])),2);
							$invent['precio3']  = ($config['margen3']==0)? $arr[$in]['precio3'] : round(($arr[$in]['preca']*100/(100-$config['margen3'])),2);
							$invent['precio4']  = ($config['margen4']==0)? $arr[$in]['precio4']*0.99 : round(($arr[$in]['preca']*100/(100-$config['margen4'])),2);
							$invent['iva']      = $arr[$in]['iva'];
							$invent['redecen']  = 'N';
							$invent['activo']   = 'S';
							$invent['formcal']  = 'U';
							$invent['clase']    = 'C';
							$invent['garantia'] = 0;

							$mSQL=$this->db->insert_string('sinv',$invent);
							$rt=$this->db->simple_query($mSQL);
							if(!$rt){
								memowrite($mSQL,'B2B');
								$er++;
							}else{
								$codigolocal=$ddata['codigo'];
							}
						}
						$ddata['codigolocal'] = $codigolocal;

						$mSQL=$this->db->insert_string('b2b_itscst',$ddata);
						$rt=$this->db->simple_query($mSQL);
						if(!$rt){
							memowrite($mSQL,'B2B');
							$er++;
						}else{
							//Ingresa los codigos de barras adicionales
							if(isset($arr[$in]['suplemen']) && !empty($arr[$in]['suplemen'])){
								$bbarras=explode('|',$arr[$in]['suplemen']);
								$barraspos=array('codigo'=>$codigolocal);
								foreach( $bbarras as $bar){
									$bar=trim($bar);
									if(preg_match('/^[0-9A-Za-z]+$/', $bar)>0){
										$dbbar=$this->db->escape($bar);

										$csinv=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM sinv WHERE codigo=$dbbar OR barras=$dbbar");
										$cbarr=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM barraspos WHERE suplemen=$dbbar");

										$csinv=(empty($csinv))? 0: $csinv;
										$cbarr=(empty($cbarr))? 0: $cbarr;
										if($csinv+$cbarr ==0){
											$barraspos['suplemen']=$bar;
											$mSQL=$this->db->insert_string('barraspos',$barraspos);
											$rt=$this->db->simple_query($mSQL);
											if(!$rt){
												memowrite($mSQL,'B2B');
												$er++;
											}
										}
									}
								}
							}
							//Fin de los codigos de barras adicionales
						}
					}
					if($er==0){
						if(!$this->_cargacompra($id_scst)) $er=false;
					}

					//Carga el inventario
					/*$ddata=array();
					$sinv=&$arr['sinv'];
					foreach($sinv as $in => $aarr){
						foreach($aarr AS $i=>$val)
							$sinv[$in][$i]=base64_decode($val);
						$sinv[$in]['proveed']  = $proveed;
						$mSQL=$this->db->insert_string('b2b_sinv',$sinv[$in]);
						$mSQL.=' ON DUPLICATE KEY UPDATE precio1='.$sinv[$in]['precio1'].',precio2='.$sinv[$in]['precio2'].',precio3='.$sinv[$in]['precio3'].',precio4='.$sinv[$in]['precio4'];
						$rt=$this->db->simple_query($mSQL);
						if(!$rt) memowrite($mSQL,'B2B');
					}*/
				}
			}
		}
		return $er;
	}

	function _trae_devolu($id=null,$ultimo=null,$uniq=false){
		return $this->_trae_compra_devolu($id,$ultimo,$uniq,'dea');
	}

	function _trae_compra($id=null,$ultimo=null,$uniq=false){
		return $this->_trae_compra_devolu($id,$ultimo,$uniq,'cea');
	}

	function cargacompra($id){
		$rt=$this->_cargacompra($id);
		redirect('sincro/b2b/scstedit/show/'.$id);
	}

	function _cargacompra($id){
		$error   =0;
		$pcontrol=$this->datasis->dameval('SELECT pcontrol FROM b2b_scst WHERE  id='.$this->db->escape($id));

		if(empty($pcontrol)){
			$eexiste = 0;
		}else{
			$eexiste = $this->datasis->dameval('SELECT COUNT(*) FROM scst WHERE  control='.$this->db->escape($pcontrol));
			if(empty($eexiste)){
				$eexiste = 0;
			}
		}

		$noencuentra = $this->datasis->dameval("SELECT COUNT(*) FROM b2b_itscst AS a JOIN sinv AS b ON a.codigo=b.codigo WHERE b.codigo IS NULL AND a.id_scst=".$this->db->escape($id));
		if($noencuentra > 0){
			memowrite('Hay productos no asociados al inventario local en la compra id:'.$id);
			return false;
		}

		$cana=$this->datasis->dameval('SELECT COUNT(*) FROM b2b_itscst AS a LEFT JOIN sinv AS b ON a.codigolocal=b.codigo WHERE a.numero IS NULL AND id_scst='.$this->db->escape($id));
		if($cana==0 AND $eexiste==0){
			$control=$this->datasis->fprox_numero('nscst');
			$transac=$this->datasis->fprox_numero('ntransa');
			//$tt['montotot']=$tt['montoiva']=$tt['montonet']=0;
			$estampa=date('Y-m-d');
			$hora   =date('h:m:s');

			$query = $this->db->query('SELECT fecha,numero,proveed,depo,codigolocal AS codigo,descrip,cantidad,devcant,devfrac,costo,importe,iva,montoiva,garantia,ultimo,precio1,precio2,precio3,precio4,licor FROM b2b_itscst WHERE id_scst=?',array($id));
			if ($query->num_rows() > 0){
				foreach ($query->result_array() as $itrow){
					$itdata=array();
					$itdata['fecha']     = $itrow['fecha']   ;
					$itdata['numero']    = $itrow['numero']  ;
					$itdata['proveed']   = $itrow['proveed'] ;
					$itdata['depo']      = $itrow['depo']    ;
					$itdata['codigo']    = $itrow['codigo']  ;
					$itdata['descrip']   = $itrow['descrip'] ;
					$itdata['cantidad']  = $itrow['cantidad'];
					$itdata['devcant']   = $itrow['devcant'] ;
					$itdata['devfrac']   = $itrow['devfrac'] ;
					$itdata['costo']     = $itrow['costo']   ;
					$itdata['importe']   = $itrow['importe'] ;
					$itdata['iva']       = $itrow['iva']     ;
					$itdata['montoiva']  = $itrow['montoiva'];
					$itdata['garantia']  = $itrow['garantia'];
					$itdata['ultimo']    = $itrow['ultimo']  ;
					$itdata['precio1']   = $itrow['precio1'] ;
					$itdata['precio2']   = $itrow['precio2'] ;
					$itdata['precio3']   = $itrow['precio3'] ;
					$itdata['precio4']   = $itrow['precio4'] ;
					$itdata['licor']     = $itrow['licor']   ;


					$itdata['usuario']   = $this->session->userdata('usuario');
					$itdata['estampa']   = $estampa;
					$itdata['hora']      = $hora   ;
					$itdata['control']   = $control;
					$itdata['transac']   = $transac;

					//$tt['montotot']+=$itrow['importe'];
					//$tt['montoiva']+=$itrow['montoiva'];
					//$tt['montonet']+=$itrow['importe']+$itrow['montoiva'];
					$mSQL=$this->db->insert_string('itscst',$itdata);
					$rt=$this->db->simple_query($mSQL);
					if(!$rt){ memowrite($mSQL,'B2B'); $error++;}
				}
			}

			$query = $this->db->query('SELECT fecha,numero,depo,proveed,nombre,montotot,montoiva,montonet,vence,tipo_doc,
				peso,usuario,nfiscal,exento,sobretasa,reducida,tasa,montasa,monredu,monadic,TRIM(serie) AS serie,
				cimpuesto,ctotal,cstotal,civaadi,cadicio,civared,creduci,civagen,cgenera,cexento,reteiva
				FROM b2b_scst WHERE id=?',array($id));
			if ($query->num_rows() > 0){
				$data=array();

				$row = $query->row_array();
				$data['fecha']     = $row['fecha']    ;
				$data['numero']    = $row['numero']   ;
				$data['proveed']   = $row['proveed']  ;
				$data['nombre']    = $row['nombre']   ;
				$data['depo']      = $row['depo']     ;
				$data['montotot']  = $row['montotot'] ;
				$data['montoiva']  = $row['montoiva'] ;
				$data['montonet']  = $row['montonet'] ;
				$data['vence']     = $row['vence']    ;
				$data['tipo_doc']  = $row['tipo_doc'] ;
				$data['peso']      = $row['peso']     ;
				$data['nfiscal']   = $row['nfiscal']  ;
				$data['exento']    = $row['exento']   ;
				$data['sobretasa'] = $row['sobretasa'];
				$data['reducida']  = $row['reducida'] ;
				$data['cimpuesto'] = $row['cimpuesto'];
				$data['ctotal']    = $row['ctotal']   ;
				$data['cstotal']   = $row['cstotal']  ;
				$data['civaadi']   = $row['civaadi']  ;
				$data['cadicio']   = $row['cadicio']  ;
				$data['civared']   = $row['civared']  ;
				$data['creduci']   = $row['creduci']  ;
				$data['civagen']   = $row['civagen']  ;
				$data['cgenera']   = $row['cgenera']  ;
				$data['cexento']   = $row['cexento']  ;
				$data['reteiva']   = $row['reteiva']  ;
				$data['tasa']      = $row['tasa']     ;
				$data['montasa']   = $row['montasa']  ;
				$data['monredu']   = $row['monredu']  ;
				$data['monadic']   = $row['monadic']  ;
				$data['serie']     = $row['serie']    ;

				$data['anticipo']  = 0;
				$data['inicial']   = 0;
				$data['credito']   = 0;
				$data['estampa']   = $estampa;
				$data['hora']      = $hora;
				$data['control']   = $control;
				$data['transac']   = $transac;
				$data['usuario']   = $this->session->userdata('usuario');

				if(empty($row['serie'])){
					$data['serie'] = $row['numero'];
				}
				/*else{
					$data['numero']= substr($row['serie'],-8);
				}*/

				if(empty($data['nfiscal'])){
					$data['nfiscal']=substr($row['serie'],-12);
				}

				//$row['montotot'] =$tt['montotot'];
				//$row['montoiva'] =$tt['montoiva'];
				//$row['montonet'] =$tt['montonet'];

				$mSQL=$this->db->insert_string('scst',$data);
				$rt=$this->db->simple_query($mSQL);
				if(!$rt){memowrite($mSQL,'B2B'); $error++;}
			}

			$mSQL="UPDATE b2b_scst SET pcontrol='${control}' WHERE id=".$this->db->escape($id);
			$rt=$this->db->simple_query($mSQL);
			if(!$rt){memowrite($mSQL,'B2B'); $error++; }
		}
		return ($error==0) ? true : false;
	}
//****************************************************
// Metodos para gestionar las consignaciones
//****************************************************
	function traeconsigna($par,$ultimo=null){
		$rt=$this->_trae_consigna($par,$ultimo);
		if($rt==0){
			$str=$this->consignaCargadas.' transacciones descargadas';
		}else{
			$str='Hubo problemas durante la  descarga, se generar&oacute;n centinelas';
		}
		$data['content'] = $str;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Consignaciones Descargadas');
		$this->load->view('view_ventanas_sola', $data);
	}

	function _trae_consigna($id=null,$ultimo=null){
		$this->consignaCargadas=0;
		if(is_null($id)) return false; else $id=$this->db->escape($id);
		$contribu=$this->datasis->traevalor('CONTRIBUYENTE');
		$rif     =$this->datasis->traevalor('RIF');

		$config=$this->datasis->damerow("SELECT proveed,grupo,puerto,proteo,url,usuario,clave,tipo,depo,margen1,margen2,margen3,margen4,margen5 FROM b2b_config WHERE id=$id");
		if(count($config)==0) return false;

		$er=0;
		$this->load->helper('url');
		$server_url = reduce_double_slashes(trim($config['url']).'/'.trim($config['proteo']).'/'.'rpcserver');

		$this->load->library('xmlrpc');
		$this->xmlrpc->xmlrpc_defencoding=$this->config->item('charset');
		//$this->xmlrpc->set_debug(TRUE);
		$puerto= (empty($config['puerto'])) ? 80 : $config['puerto'];

		$this->xmlrpc->server($server_url , $puerto);
		$this->xmlrpc->method('consiea');

		if(is_null($ultimo)){
			$ufac=$this->datasis->dameval('SELECT MAX(asociado) FROM b2b_scon WHERE clipro='.$this->db->escape($config['proveed']));
			if(empty($ufac)) $ufac=0;
		}elseif(is_numeric($ultimo)){
			$ufac=$ultimo;
		}else{
			$ufac=0;
		}

		$request = array($ufac,$config['proveed'],$config['usuario'],md5($config['clave']));
		$this->xmlrpc->request($request);

		if (!$this->xmlrpc->send_request()){
			memowrite($this->xmlrpc->display_error(),'B2B');
			return true;
		}else{
			$res=$this->xmlrpc->display_response();
			foreach($res AS $ind=>$exdata){
				$this->consignaCargadas++;
				$arr=unserialize($exdata);
				foreach($arr['scon'] AS $in => $val) $arr[$in]=base64_decode($val);

				$proveed=$config['proveed'];
				$cliente=$this->datasis->dameval('SELECT TRIM(cliente) AS scli FROM sprv WHERE proveed='.$this->db->escape($proveed));
				if(!empty($cliente)){
					$tipo   ='C';
					$clipro =$this->db->escape($cliente);
					$pnombre=$this->datasis->dameval('SELECT nombre FROM scli WHERE cliente='.$clipro);
					$pdirec1=$this->datasis->dameval('SELECT dire11 FROM scli WHERE cliente='.$clipro);
				}else{
					$tipo   ='P';
					$clipro =$this->db->escape($proveed);
					$pnombre=$this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$this->db->escape($clipro));
					$pdirec1=$this->datasis->dameval('SELECT direc1 FROM sprv WHERE proveed='.$this->db->escape($clipro));
				}

				$cc=$this->datasis->dameval('SELECT COUNT(*) FROM b2b_scon WHERE numero='.$this->db->escape($arr['numero']).' AND tipo='.$this->db->escape($tipo).' AND clipro='.$clipro);
				if($cc==0){
					$data=array();
					$data['numero']   = $arr['numero'];
					$data['fecha']    = $arr['fecha'];
					$data['tipo']     = $tipo;
					$data['tipod']    = ($arr['tipod']=='E')? 'R':'E';
					$data['status']   = 'T';
					$data['clipro']   = $proveed;
					$data['direc1']   = $pdirec1;
					$data['almacen']  = $config['depo'];
					$data['nombre']   = $pnombre;
					$data['asociado'] = $arr['numero'];
					$data['observ1']  = $arr['observ1'];
					$data['stotal']   = $arr['stotal'];
					$data['impuesto'] = $arr['impuesto'];
					$data['gtotal']   = $arr['gtotal'];
					$data['peso']     = $arr['peso'];
					$mSQL=$this->db->insert_string('b2b_scon',$data);

					$rt=$this->db->simple_query($mSQL);
					if(!$rt){
						memowrite($mSQL,'B2B');
						$maestro=false;
						$er++;
					}else{
						$id_scon=$this->db->insert_id();
						$maestro=true;
					}
				}else{
					$maestro=false;
				}

				if($maestro){
					$itscon =& $arr['itscon'];
					foreach($itscon AS $in => $aarr){
						foreach($aarr AS $i=>$val) $arr[$in][$i]=base64_decode($val);

						//Arregla los precios en caso de llegar malos
						for($j=1;$j<5;$j++){
							$ind='precio'.$j;
							if($arr[$in][$ind]<0 && $j>1){
								$ind2='precio'.$i-1;
								$arr[$in][$ind]=$arr[$in][$ind];
							}elseif($arr[$in][$ind]<0 && $j==1){
								$arr[$in][$ind]=round(($arr[$in]['precio']*100/(100-$config['margen1'])),2);
							}
						}
						$ddata=array();

						$ddata['numero']     =$arr[$in]['numero'];
						$ddata['codigo']     =trim($arr[$in]['codigo']);
						$ddata['desca']      =$arr[$in]['desca'];
						$ddata['cana']       =$arr[$in]['cana'];
						$ddata['recibido']   =$arr[$in]['cana'];
						$ddata['precio']     =$arr[$in]['precio'];
						$ddata['importe']    =$arr[$in]['importe'];
						$ddata['iva']        =$arr[$in]['iva'];
						$ddata['id_scon']    =$id_scon;

						$barras=trim($arr[$in]['barras']);

						//procedimiento de determinacion del codigo del articulo en sistema local
						$codigolocal=false;
						if(!empty($barras)){
							$mSQL_p = 'SELECT codigo FROM sinv';
							$bbus   = array('codigo','barras','alterno');
							$query=$this->_gconsul($mSQL_p,$barras,$bbus);
							if($query){
								$row = $query->row();
								$codigolocal=$row->codigo;
							}
						}elseif(!empty($ddata['codigo'])){
							$mSQL_p = 'SELECT codigo FROM sinv';
							$bbus   = array('codigo','barras','alterno');
							$query=$this->_gconsul($mSQL_p,$ddata['codigo'],$bbus);
							if($query){
								$row = $query->row();
								$codigolocal=$row->codigo;
								$barras=$codigolocal;
							}
						}

						//Si no existe lo crea
						if(empty($codigolocal)){
							$base1 = ($arr[$in]['precio1']*100)/(100+$arr[$in]['iva']);
							$base2 = ($arr[$in]['precio2']*100)/(100+$arr[$in]['iva']);
							$base3 = ($arr[$in]['precio3']*100)/(100+$arr[$in]['iva']);
							$base4 = ($arr[$in]['precio4']*100)/(100+$arr[$in]['iva']);
							$invent['codigo']   = $barras;
							$invent['grupo']    = $config['grupo'];
							$invent['prov1']    = $proveed;
							$invent['descrip']  = $arr[$in]['desca'];
							$invent['existen']  = $arr[$in]['cana'];
							$invent['pond']     = $arr[$in]['precio'];
							$invent['ultimo']   = $arr[$in]['precio'];
							$invent['unidad']   = $arr[$in]['unidad'];
							$invent['tipo']     = $arr[$in]['tipo'];
							$invent['tdecimal'] = $arr[$in]['tdecimal'];
							$invent['margen1']  = round(100-($arr[$in]['precio']*100/$base1),2);
							$invent['margen2']  = round(100-($arr[$in]['precio']*100/$base2),2);
							$invent['margen3']  = round(100-($arr[$in]['precio']*100/$base3),2);
							$invent['margen4']  = round(100-($arr[$in]['precio']*100/$base4),2);
							$invent['base1']    = round($base1,2);
							$invent['base2']    = round($base2,2);
							$invent['base3']    = round($base3,2);
							$invent['base4']    = round($base4,2);
							$invent['precio1']  = ($config['margen1']==0)? $arr[$in]['precio1'] : round(($arr[$in]['preca']*100/(100-$config['margen1'])),2);
							$invent['precio2']  = ($config['margen2']==0)? $arr[$in]['precio2'] : round(($arr[$in]['preca']*100/(100-$config['margen2'])),2);
							$invent['precio3']  = ($config['margen3']==0)? $arr[$in]['precio3'] : round(($arr[$in]['preca']*100/(100-$config['margen3'])),2);
							$invent['precio4']  = ($config['margen4']==0)? $arr[$in]['precio4'] : round(($arr[$in]['preca']*100/(100-$config['margen4'])),2);
							$invent['iva']      = $arr[$in]['iva'];
							$invent['redecen']  = 'N';
							$invent['activo']   = 'S';
							$invent['formcal']  = 'U';
							$invent['clase']    = 'C';
							$invent['garantia'] = 0;

							$mSQL=$this->db->insert_string('sinv',$invent);
							$rt=$this->db->simple_query($mSQL);
							if(!$rt){
								memowrite($mSQL,'B2B');
								$er++;
							}else{
								$codigolocal=$barras;
							}
						}
						$ddata['codigolocal'] = $codigolocal;

						$mSQL=$this->db->insert_string('b2b_itscon',$ddata);
						$rt=$this->db->simple_query($mSQL);
						if(!$rt){
							memowrite($mSQL,'B2B');
							$er++;
						}
					}
				}
			}
		}
		return $er;
	}

	function cargascon($id){
		$rt=$this->_cargascon($id);
		//var_dump($rt);
		redirect('sincro/b2b/sconedit/show/'.$id);
	}

	function _cargascon($id){
		$er=0;

		$pid=$this->datasis->dameval('SELECT pid FROM b2b_scon WHERE  id='.$this->db->escape($id));
		if(empty($pid)){
			$eexisten = 0;
		}else{
			$eexisten = $this->datasis->dameval('SELECT COUNT(*) FROM scon WHERE id='.$this->db->escape($pid));
			if(empty($eexisten)){
				$eexisten = 0;
			}
		}

		if($eexisten == 0){
			$cana=$this->datasis->dameval('SELECT COUNT(*) FROM b2b_itscon AS a LEFT JOIN sinv AS b ON a.codigolocal=b.codigo WHERE b.codigo IS NULL AND a.id_scon='.$this->db->escape($id));
			if($cana>0){ return 1; }

			$query=$this->db->query('SELECT numero,fecha,status,direc1,clipro,tipo,almacen,nombre,asociado,observ1,stotal,impuesto,gtotal,tipod,peso,pid FROM b2b_scon WHERE id = ?',$id);
			if ($query->num_rows() > 0){
				$row = $query->row();

				if(strtoupper($row->tipo)=='P'){
					$numero = $this->datasis->fprox_numero('nsconp');
				}else{
					$numero = $this->datasis->fprox_numero('nsconc');
				}
				$almacen=$row->almacen;
				$tipod  =$row->tipod;

				$data['numero']   =$numero;
				$data['fecha']    =$row->fecha;
				$data['clipro']   =$row->clipro;
				$data['almacen']  =$row->almacen;
				$data['nombre']   =$row->nombre;
				$data['direc1']   =$row->direc1;
				$data['asociado'] =$row->asociado;
				$data['observ1']  =$row->observ1;
				$data['stotal']   =$row->stotal;
				$data['impuesto'] =$row->impuesto;
				$data['gtotal']   =$row->gtotal;
				$data['peso']     =$row->peso;
				$data['tipod']    =$row->tipod;
				$data['tipo']     =$row->tipo;
				$data['origen']   ='R'; //Remoto
				$data['status']   ='C'; //Cerrado

				$mSQL=$this->db->insert_string('scon',$data);
				$rt=$this->db->simple_query($mSQL);
				if(!$rt){
					memowrite($mSQL,'B2B');
					$maestro=false;
					$er++;
				}else{
					$id_scon=$this->db->insert_id();
					$maestro=true;
				}

				if($maestro){
					$data=array();
					$importe=$impuesto=0;
					$qquery=$this->db->query('SELECT numero,codigo,codigolocal,desca,cana,recibido,precio,importe,iva FROM b2b_itscon WHERE recibido <> 0 AND  id_scon = ?',$id);
					foreach ($qquery->result() as $rrow){
						$data['numero']   = $numero;
						$data['id_scon']  = $id_scon;
						$data['codigo']   = $rrow->codigolocal;
						$data['desca']    = $rrow->desca;
						$data['cana']     = $rrow->recibido;
						$data['recibido'] = $rrow->recibido;
						$data['precio']   = $rrow->precio;
						$data['importe']  = $rrow->recibido*$rrow->precio;
						$data['iva']      = $rrow->iva;
						$importe += $data['importe'];
						$impuesto+= $rrow->precio*($rrow->iva)/100;

						$mSQL=$this->db->insert_string('itscon',$data);
						$rt  =$this->db->simple_query($mSQL);
						if(!$rt){
							memowrite($mSQL,'B2B');
							$er++;
						}

						$cc=$this->datasis->dameval('SELECT COUNT(*) FROM itsinv WHERE codigo='.$this->db->escape($rrow->codigolocal).' AND alma='.$this->db->escape($almacen));
						if($cc==0 or is_null($cc)){
							$itsinvdat=array();
							$itsinvdat['codigo'] = $rrow->codigolocal;
							$itsinvdat['alma']   = $almacen;
							$itsinvdat['existen']= 0;

							$mSQL=$this->db->insert_string('itsinv',$itsinvdat);
							$rt  =$this->db->simple_query($mSQL);
							if(!$rt){
								memowrite($mSQL,'B2B');
								$er++;
							}
						}

					}
					$mSQL="UPDATE b2b_scon SET pid=$id_scon WHERE id=".$this->db->escape($id);
					$rt=$this->db->simple_query($mSQL);
					if(!$rt){
						memowrite($mSQL,'B2B');
						$er++;
					}

					//Actualiza las cantidades en inventario
					$fact=($tipod=='R')? 1:-1;
					$mSQL='UPDATE sinv JOIN itscon ON sinv.codigo=itscon.codigo SET sinv.existen=sinv.existen+('.$fact.')*(itscon.cana) WHERE itscon.id_scon='.$this->db->escape($id_scon);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'B2B'); }
					$mSQL='UPDATE itsinv JOIN itscon ON itsinv.codigo=itscon.codigo SET itsinv.existen=itsinv.existen+('.$fact.')*(itscon.cana) WHERE itscon.id_scon='.$this->db->escape($id_scon).' AND itsinv.alma='.$this->db->escape($almacen);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'B2B'); }
				}
			}
		}
		return $er;
	}
//****************************************************
// Metodos para gestionar transacciones como gasto
//****************************************************

	function cargagasto(){

	}

	function _cargagasto(){

	}

	function reasignaprecio(){
		$this->rapyd->load('dataedit');
		$edit = new DataEdit('Cambios de precios','b2b_itscst');
		$edit->descrip  = new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->mode = 'autohide';

		for($i=1;$i<5;$i++){
			$obj='precio'.$i;
			$edit->$obj = new inputField('Precio '.$i, $obj);
			$edit->$obj->css_class='inputnum';
			$edit->$obj->rule ='numeric';
			$edit->$obj->size = 10;
		}

		$edit->buttons('modify','save');
		$edit->build();
		$this->rapyd->jquery[]='$(window).unload(function() { window.opener.location.reload(); });';
		$data['content'] =$edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   ='';
		$this->load->view('view_ventanas_sola', $data);
	}

	function sprvexits($proveed){
		$mSQL='SELECT COUNT(*) FROM sprv WHERE proveed='.$this->db->escape($proveed);
		$cana=$this->datasis->dameval($mSQL);
		if($cana==0){
			$error="El proveedor dado no exite";
			$this->validation->set_message('sprvexits',$error);
			return false;
		}
		return true;
	}

	function noexiste($barras){
		$mSQL='SELECT COUNT(*) FROM sinv WHERE codigo='.$this->db->escape($barras);
		$cana=$this->datasis->dameval($mSQL);
		if($cana!=0){
			$error="El c&oacute;digo de barras '$barras' existe en el iventario, la equivalencia se debe aplicar en un producto que no exista";
			$this->validation->set_message('noexiste',$error);
			return false;
		}
		return true;
	}

	function siexiste($barras){
		$mSQL='SELECT COUNT(*) FROM sinv WHERE codigo='.$this->db->escape($barras);
		$cana=$this->datasis->dameval($mSQL);
		if($cana==0){
			$error="El c&oacute;digo de barras '$barras' no existe en el iventario";
			$this->validation->set_message('siexiste',$error);
			return false;
		}
		return true;
	}


	function dummy(){
		echo "<p aling='center'>Redirigiendo la p&aacute;gina</p>";
	}

	function _gconsul($mSQL_p,$cod_bar,$busca,$suple=null){
		if(!empty($suple) AND $this->db->table_exists('suple')){
			$mSQL  ="SELECT codigo FROM suple WHERE suplemen='${cod_bar}' LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				$row = $query->row();
				$busca  =array($suple);
				$cod_bar=$row->codigo;
			}
		}
		foreach($busca AS $b){
			$mSQL  =$mSQL_p." WHERE ${b}='${cod_bar}' LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				return $query;
			}
		}
		if ($this->db->table_exists('barraspos')) {
			$mSQL  ="SELECT codigo FROM barraspos WHERE suplemen=".$this->db->escape($cod_bar)." LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				$row = $query->row();
				$cod_bar=$row->codigo;

				$mSQL  =$mSQL_p." WHERE codigo='${cod_bar}' LIMIT 1";
				$query = $this->db->query($mSQL);
				if($query->num_rows() == 0)
					return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
		return $query;
	}

	function _pre_inup($do){
		for($i=1;$i<6;$i++){
			$mar='margen'.$i;
			$$mar=round($do->get($mar),2); //optenemos el margen
		}

		if($margen1>=$margen2 && $margen2>=$margen3 && $margen3>=$margen4 && $margen4>=$margen5){
			return true;
		}else{
			$do->error_message_ar['pre_upd'] = 'Los margenes deben cumplir con:<br> Margen 1 mayor o igual al Margen 2 mayor o igual al  Margen 3 mayor o igual al Margen 4 mayor o igual al Margen 5';
			return false;
		}
	}

	function instalar(){
		if (!$this->db->table_exists('b2b_config')) {
			$mSQL="CREATE TABLE `b2b_config` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`proveed` CHAR(5) NOT NULL COMMENT 'Codigo del proveedor',
				`prefijo` VARCHAR(50) NOT NULL COMMENT 'Prefijo para los codigos de productos',
				`url` VARCHAR(100) NOT NULL,
				`puerto` INT(5) NOT NULL DEFAULT '80',
				`proteo` VARCHAR(20) NOT NULL DEFAULT 'proteoerp',
				`usuario` VARCHAR(100) NOT NULL COMMENT 'Codigo de cliente en el proveedor',
				`clave` VARCHAR(100) NOT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL COMMENT 'I para inventario G para gasto',
				`depo` VARCHAR(4) NULL DEFAULT NULL COMMENT 'Almacen',
				`margen1` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Margen para el precio1',
				`margen2` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Margen para el precio 2',
				`margen3` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Margen para el precio3',
				`margen4` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Margen para el precio4',
				`margen5` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Margen para el precio5 (solo supermercado)',
				`grupo` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Grupo por defecto',
				PRIMARY KEY (`id`)
			)
			COMMENT='Configuracion para los b2b'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1;";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('b2b_scst')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `b2b_scst` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `fecha` date DEFAULT NULL,
			  `numero` varchar(8) DEFAULT NULL,
			  `proveed` varchar(5) DEFAULT NULL,
			  `nombre` varchar(30) DEFAULT NULL,
			  `depo` varchar(4) DEFAULT NULL,
			  `montotot` decimal(17,2) DEFAULT NULL,
			  `montoiva` decimal(17,2) DEFAULT NULL,
			  `montonet` decimal(17,2) DEFAULT NULL,
			  `vence` date DEFAULT NULL,
			  `tipo_doc` char(2) DEFAULT NULL,
			  `control` varchar(8) NOT NULL DEFAULT '',
			  `peso` decimal(12,2) DEFAULT NULL,
			  `estampa` date DEFAULT NULL,
			  `hora` varchar(8) DEFAULT NULL,
			  `usuario` varchar(12) DEFAULT NULL,
			  `nfiscal` varchar(12) DEFAULT NULL,
			  `exento` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `sobretasa` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `reducida` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `tasa` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `montasa` decimal(17,2) DEFAULT NULL,
			  `monredu` decimal(17,2) DEFAULT NULL,
			  `monadic` decimal(17,2) DEFAULT NULL,
			  `serie` char(12) DEFAULT NULL,
			  `pcontrol` char(8) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `proveednum` (`proveed`,`numero`),
			  KEY `proveedor` (`proveed`)
			) ENGINE=MyISAM AUTO_INCREMENT=1";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('b2b_itscst')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `b2b_itscst` (
			  `id_scst` int(11) DEFAULT NULL,
			  `fecha` date DEFAULT NULL,
			  `numero` varchar(8) DEFAULT NULL,
			  `proveed` varchar(5) DEFAULT NULL,
			  `depo` varchar(4) DEFAULT NULL,
			  `codigo` varchar(15) DEFAULT NULL,
			  `descrip` varchar(45) DEFAULT NULL,
			  `cantidad` decimal(10,3) DEFAULT NULL,
			  `devcant` decimal(10,3) DEFAULT NULL,
			  `devfrac` int(4) DEFAULT NULL,
			  `costo` decimal(17,2) DEFAULT NULL,
			  `importe` decimal(17,2) DEFAULT NULL,
			  `iva` decimal(5,2) DEFAULT NULL,
			  `montoiva` decimal(17,2) DEFAULT NULL,
			  `garantia` int(3) DEFAULT NULL,
			  `ultimo` decimal(17,2) DEFAULT NULL,
			  `precio1` decimal(15,2) DEFAULT NULL,
			  `precio2` decimal(15,2) DEFAULT NULL,
			  `precio3` decimal(15,2) DEFAULT NULL,
			  `precio4` decimal(15,2) DEFAULT NULL,
			  `estampa` date DEFAULT NULL,
			  `hora` varchar(8) DEFAULT NULL,
			  `usuario` varchar(12) DEFAULT NULL,
			  `licor` decimal(10,2) DEFAULT '0.00',
			  `barras` varchar(15) DEFAULT NULL,
			  `codigolocal` varchar(15) DEFAULT NULL,
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  KEY `id_scst` (`id_scst`),
			  KEY `fecha` (`fecha`),
			  KEY `codigo` (`codigo`),
			  KEY `proveedor` (`proveed`),
			  KEY `numero` (`numero`)
			) ENGINE=MyISAM AUTO_INCREMENT=1";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('scon','origen')){
			$mSQL="ALTER TABLE scon ADD COLUMN origen CHAR(1) NOT NULL DEFAULT 'L' COMMENT 'L= Local, R=Remoto' AFTER peso;";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','reteiva')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN `reteiva` DECIMAL(17,2) NULL DEFAULT '0.00' AFTER `reducida`;";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','cexento')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN  `cexento` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','cgenera')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `cgenera` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','civagen')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `civagen` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','creduci')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `creduci` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','civared')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `civared` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','cadicio')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `cadicio` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','civaadi')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `civaadi` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','cstotal')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `cstotal` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','ctotal')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `ctotal` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','cimpuesto')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `cimpuesto` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('b2b_scon')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `b2b_scon` (
				`numero` CHAR(8) NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL,
				`tipod` CHAR(1) NULL DEFAULT NULL,
				`status` CHAR(1) NULL DEFAULT 'T',
				`asociado` CHAR(8) NULL DEFAULT NULL,
				`clipro` CHAR(5) NULL DEFAULT NULL,
				`almacen` CHAR(4) NULL DEFAULT NULL,
				`nombre` CHAR(40) NULL DEFAULT NULL,
				`direc1` CHAR(40) NULL DEFAULT NULL,
				`direc2` CHAR(40) NULL DEFAULT NULL,
				`observ1` CHAR(33) NULL DEFAULT NULL,
				`observ2` CHAR(33) NULL DEFAULT NULL,
				`stotal` DECIMAL(12,2) NULL DEFAULT NULL,
				`impuesto` DECIMAL(12,2) NULL DEFAULT NULL,
				`gtotal` DECIMAL(12,2) NULL DEFAULT NULL,
				`peso` DECIMAL(10,3) NULL DEFAULT NULL,
				`pid` INT(15) NULL DEFAULT NULL,
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `numero` (`numero`, `tipo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('b2b_itscon')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `b2b_itscon` (
				`numero` CHAR(8) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`codigolocal` VARCHAR(15) NULL DEFAULT NULL,
				`desca` VARCHAR(40) NULL DEFAULT NULL,
				`cana` DECIMAL(5,0) NULL DEFAULT NULL,
				`recibido` DECIMAL(5,0) NULL DEFAULT NULL,
				`precio` DECIMAL(12,2) NULL DEFAULT NULL,
				`importe` DECIMAL(12,2) NULL DEFAULT NULL,
				`iva` DECIMAL(8,2) NULL DEFAULT NULL,
				`id_scon` INT(15) UNSIGNED NOT NULL,
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `id_scon` (`id_scon`),
				INDEX `numero_codigo` (`numero`, `codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			var_dump($this->db->simple_query($mSQL));
		}
	}
}
