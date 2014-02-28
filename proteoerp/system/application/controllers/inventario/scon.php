<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class scon extends Controller {

	function scon(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('320',1);
		$this->back_dataedit='inventario/scon/index';
	}

	function index() {
		$this->rapyd->load('datagrid','datafilter');

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'   => "'+((screen.availWidth/2)-400)+'",
			'screeny'   => "'+((screen.availHeight/2)-300)+'"
		);

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
		'cliente' =>'C&oacute;digo Cliente',
		'nombre'=>'Nombre',
		'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cod_cli'),
		'titulo'  =>'Buscar Cliente');

		$boton=$this->datasis->modbus($scli);


		$filter = new DataFilter('Filtro de consignaciones','scon');

		$filter->fechad = new dateonlyField('Desde', 'fechad');
		$filter->fechah = new dateonlyField('Hasta', 'fechah');
		$filter->fechad->clause  =$filter->fechah->clause ='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size = 30;

		$filter->factura = new inputField('Factura', 'factura');
		$filter->factura->size = 30;

		$filter->cliente = new inputField('Cliente/Proveedor','clipro');
		$filter->cliente->size = 30;
		$filter->cliente->append($boton);

		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('inventario/scon/dataedit/<#tipo#>/show/<#id#>','<#numero#>');
		$uri2 = anchor_popup('formatos/verhtml/PSINV/<#id#>','Ver HTML',$atts);

		function asoc($id,$origen,$asociado,$clipro,$numero){
			$asociado=trim($asociado);
			if($origen=='L'){
				$atts = array(
					'width'      => '400',
					'height'     => '300',
					'scrollbars' => 'yes',
					'status'     => 'yes',
					'resizable'  => 'yes',
					'screenx'   => "'+((screen.availWidth/2)-200)+'",
					'screeny'   => "'+((screen.availHeight/2)-150)+'"
				);
				if(empty($asociado)){
					$asociado='Ninguno';
				}
				$acti =anchor_popup('/inventario/scon/traeasoc/'.raencode($id).'/'.raencode($clipro).'/'.raencode($numero) ,$asociado,$atts);
			}else{
				$acti=$asociado;
			}
			return $acti;
		}

		$grid = new DataGrid();
		$grid->use_function('asoc');
		$grid->order_by('numero','desc');
		$grid->per_page = 15;

		$grid->column_orderby('N&uacute;mero' ,$uri,'numero');
		$grid->column_orderby('Asociado'      ,'<asoc><#id#>|<#origen#>|<#asociado#>|<#clipro#>|<#numero#></asoc>','asociado');
		$grid->column_orderby('Fecha'         ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha','align=\'center\'');
		$grid->column_orderby('Nombre'        ,'nombre','nombre');
		$grid->column_orderby('Mov.'          ,'tipod','tipod');
		$grid->column_orderby('Sub.Total'     ,'<nformat><#stotal#></nformat>'  ,'stotal','align=\'right\'');
		$grid->column_orderby('IVA'           ,'<nformat><#impuesto#></nformat>','iva'   ,'align=\'right\'');
		$grid->column_orderby('Total'         ,'<nformat><#gtotal#></nformat>'  ,'gtotal','align=\'right\'');
		//$grid->column_orderby("Vista",$uri2,"align='center'");

		$grid->add('inventario/scon/agregar');
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Consignaci&oacute;n de inventario');
		$this->load->view('view_ventanas', $data);
	}

	function agregar(){

		$ul=array();
		$ul[] = anchor('inventario/scon/dataedit/P/create','Consignaci&oacute;n de <b>proveedor</b>').': Recibir o Devolver mercanc&iacute;a a proveedor.';
		//$ul[] = anchor('inventario/scon/dataedit/P/create','Devolver Consignacion recibida por proveedor');
		$ul[] = anchor('inventario/scon/dataedit/C/create','Consignaci&oacute;n de <b>cliente</b>').': Recibir o Devolver mercanc&iacute;a a cliente.';
		//$ul[] = anchor('inventario/scon/dataedit/C/create','Devolver Consignacion dada a cliente');

		$data['content'] = heading('Seleccione una modalidad:',2);
		$data['content'].= ul($ul).anchor('inventario/scon/index','Regresar');
		$data['title']   = heading('Inventario a consignaci&oacute;n');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function devoscon(){
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

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
		'cliente' =>'C&oacute;digo Cliente',
		'nombre'=>'Nombre',
		'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cod_cli'),
		'titulo'  =>'Buscar Cliente');

		$boton=$this->datasis->modbus($scli);

		$filter = new DataFilter('Filtro de consignaciones','scon');
		$filter->db->select(array('a.clipro','a.nombre','SUM(IF(a.tipod=\'E\',1,-1)*a.gtotal) AS saldo'));
		$filter->db->from('scon AS a');
		$filter->db->join('itscon AS b','a.id=b.id_scon');
		$filter->db->where('a.tipo','C');
		$filter->db->groupby('a.clipro');

		$filter->cliente = new inputField('Cliente/Proveedor','clipro');
		$filter->cliente->size = 30;
		$filter->cliente->append($boton);

		$filter->buttons('reset','search');
		$filter->build();

		$grid = new DataGrid();
		$grid->order_by('numero','desc');
		$grid->per_page = 15;

		$grid->column_orderby('Cliente' ,'(<#clipro#>)-<#nombre#>','nombre');
		$grid->column_orderby('Saldo'   ,'<nformat><#stotal#></nformat>'  ,'stotal','align=\'right\'');
		//$grid->column_orderby('Editar'   ,'<nformat><#gtotal#></nformat>'  ,'gtotal');
		//$grid->column_orderby("Vista",$uri2,"align='center'");

		$grid->add('inventario/scon/agregar');
		$grid->build();
		echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Consignaci&oacute;n de inventario');
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($opttipo){
		$opt_key = array_search($opttipo,array('C','P'));
		if($opt_key===false){
			show_404('');
		}
		$this->rapyd->load('dataobject','datadetails');

		$modbus=array(
		'tabla'   =>'sinv',
		'columnas'=>array(
				'codigo'  =>'C&oacute;digo',
				'descrip' =>'Descripci&oacute;n',
				'precio1' =>'Precio 1',
				'precio2' =>'Precio 2',
				'precio3' =>'Precio 3',
				'existen' =>'Existencia',
				),
		'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
		'p_uri'   =>array(4=>'<#i#>'),
		'where'   => '`activo` = "S" AND `tipo` = "Articulo"',
		'script'  => array('post_modbus_sinv(<#i#>)'),
		'titulo'  =>'Buscar Art&iacute;culo');

		if($opttipo=='C'){
			$mCLIPRO=array(
			'tabla'   =>'scli',
			'columnas'=>array(
				'cliente' =>'C&oacute;digo Cliente',
				'nombre'=>'Nombre',
				'cirepre'=>'Rif/Cedula',
				'dire11'=>'Direcci&oacute;n',
				'tipo'=>'Tipo'),
			'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
			'retornar'=>array('cliente'=>'clipro','nombre'=>'nombre',
							'dire11'=>'direc1','tipo'=>'cliprotipo'),
			'titulo'  =>'Buscar Cliente',
			'script'  => array('post_modbus_scli()'));

			$modbus['retornar']=array(
				'codigo' =>'codigo_<#i#>',
				'descrip'=>'desca_<#i#>',
				'base1'  =>'precio1_<#i#>',
				'base2'  =>'precio2_<#i#>',
				'base3'  =>'precio3_<#i#>',
				'base4'  =>'precio4_<#i#>',
				'iva'    =>'itiva_<#i#>',
				'peso'   =>'sinvpeso_<#i#>',
				'tipo'   =>'sinvtipo_<#i#>',
			);
		}else{
			$mCLIPRO=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'direc1'=>'Direcci&oacute;n',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'clipro','nombre'=>'nombre',
							'direc1'=>'direc1'),
			'titulo'  =>'Buscar Proveedor');

			$modbus['retornar']=array(
				'codigo' =>'codigo_<#i#>',
				'descrip'=>'desca_<#i#>',
				'ultimo' =>'precio1_<#i#>',
				'ultimo' =>'precio2_<#i#>',
				'ultimo' =>'precio3_<#i#>',
				'ultimo' =>'precio4_<#i#>',
				'iva'    =>'itiva_<#i#>',
				'peso'   =>'sinvpeso_<#i#>',
				'tipo'   =>'sinvtipo_<#i#>',
			);
		}
		$btnc =$this->datasis->modbus($mCLIPRO);
		$btn  =$this->datasis->p_modbus($modbus,'<#i#>');

		$do = new DataObject('scon');
		$do->rel_one_to_many('itscon', 'itscon', array('id'=>'id_scon'));
		if($opttipo=='C'){
			$do->pointer('scli' ,'scli.cliente=scon.clipro','scli.tipo AS cliprotipo','left');
			$do->rel_pointer('itscon','sinv','itscon.codigo=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');
		}else{
			//$do->pointer('sprv' ,'sprv.proveed=psinv.clipro','"1" AS `cliprotipo`','left');
			$do->rel_pointer('itscon','sinv','itscon.codigo=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.ultimo AS sinvprecio1, sinv.ultimo AS sinvprecio2, sinv.ultimo AS sinvprecio3, sinv.ultimo AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');
		}

		$edit = new DataDetails('Inventario a consignaci&oacute;n', $do);
		$edit->back_url = site_url('inventario/scon/filteredgrid');
		$edit->set_rel_title('itscon','Producto <#o#>');

		$edit->back_url = $this->back_dataedit;

		$edit->pre_process('insert' ,'_pre_insert');
		$edit->pre_process('update' ,'_pre_update');
		$edit->pre_process('delete' ,'_pre_delete');
		$edit->post_process('insert','_post_insert');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;

		$edit->tipod = new dropdownField('Tipo de movimiento', 'tipod');
		$edit->tipod->option('E','Entregado');
		$edit->tipod->option('R','Recibido');
		$edit->tipod->rule ='required';
		$edit->tipod->insertValue= ($opttipo=='C') ? 'E' : 'R';
		$edit->tipod->style='width:160px';

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode='autohide';
		$edit->numero->maxlength=8;
		//$edit->numero->apply_rules=false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when=array('show','modify');

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->readonly  = true;
		$edit->peso->size      = 10;

		$edit->clipro = new inputField(($opttipo=='C') ? 'Cliente':'Proveedor','clipro');
		$edit->clipro->size = 6;
		$edit->clipro->maxlength=5;
		$edit->clipro->rule = 'required';
		$edit->clipro->readonly=true;
		$edit->clipro->append($btnc);

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 25;
		$edit->nombre->maxlength=40;
		$edit->nombre->autocomplete=false;

		$edit->asociado = new inputField('Doc. Asociado', 'asociado');
		$edit->asociado->mode='autohide';
		$edit->asociado->size = 10;

		$edit->almacen = new  dropdownField ('Almac&eacute;n', 'almacen');
		$edit->almacen->options('SELECT ubica, CONCAT(ubica,\' \',ubides) nombre FROM caub ORDER BY ubica');
		$edit->almacen->rule = 'required';
		$edit->almacen->style='width:200px;';
		$edit->almacen->size = 5;

		$edit->observ1 = new inputField('Observaci&oacute;n', 'observ1');
		$edit->observ1s->size = 37;

		$edit->dir_clipro = new inputField("Direcci&oacute;n","direc1");
		$edit->dir_clipro->size = 37;

		//Para saber que precio se le va a dar al cliente
		$edit->cliprotipo = new hiddenField('', 'cliprotipo');
		$edit->cliprotipo->db_name     = 'cliprotipo';
		$edit->cliprotipo->pointer     = true;
		$edit->cliprotipo->insertValue = 1;

		//Campos para el detalle
		$edit->codigo = new inputField('C&oacute;digo <#o#>', 'codigo_<#i#>');
		$edit->codigo->size     = 12;
		$edit->codigo->db_name  = 'codigo';
		$edit->codigo->onkeyup   = 'OnEnter(event,<#i#>)';
		$edit->codigo->autocomplete=false;
		$edit->codigo->rel_id   = 'itscon';
		$edit->codigo->rule     = 'required';
		$edit->codigo->append($btn);

		$edit->desca = new inputField('Descripci&oacute;n <#o#>', 'desca_<#i#>');
		$edit->desca->size=34;
		$edit->desca->db_name='desca';
		$edit->desca->maxlength=50;
		$edit->desca->readonly  = true;
		$edit->desca->rel_id='itscon';

		$edit->cana = new inputField('Cantidad <#o#>', 'cana_<#i#>');
		$edit->cana->db_name  = 'cana';
		$edit->cana->css_class= 'inputnum';
		$edit->cana->rel_id   = 'itscon';
		$edit->cana->maxlength= 10;
		$edit->cana->size     = 6;
		$edit->cana->rule     = 'required|positive';
		$edit->cana->autocomplete=false;
		$edit->cana->onkeyup  ='importe(<#i#>)';

		$edit->precio = new inputField('Precio <#o#>', 'precio_<#i#>');
		$edit->precio->db_name   = 'precio';
		$edit->precio->css_class = 'inputnum';
		$edit->precio->rel_id    = 'itscon';
		$edit->precio->size      = 10;
		if($opttipo=='C'){
			$edit->precio->rule      = 'required|positive|callback_chpreca[<#i#>]';
		}else{
			$edit->precio->rule      = 'required|positive';
		}
		$edit->precio->readonly  = true;

		$edit->importe = new inputField('Importe <#o#>', 'importe_<#i#>');
		$edit->importe->db_name='importe';
		$edit->importe->size=10;
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

		$edit->impuesto  = new inputField('Impuesto', 'impuesto');
		$edit->impuesto->size = 20;
		$edit->impuesto->css_class='inputnum';

		$edit->stotal  = new inputField('Sub.Total', 'stotal');
		$edit->stotal->size = 20;
		$edit->stotal->css_class='inputnum';

		$edit->gtotal  = new inputField('Total', 'gtotal');
		$edit->gtotal->size = 20;
		$edit->gtotal->css_class='inputnum';

		$edit->tipo = new autoUpdateField('tipo',$opttipo,$opttipo);

		$edit->buttons('save', 'undo', 'back','add_rel');
		$edit->build();

		$inven=array();
		if($opttipo=='C'){
			$titulo='Consignaci&oacute;n a Cliente';
			$query=$this->db->query('SELECT TRIM(codigo) AS codigo ,TRIM(descrip) AS descrip,tipo,base1,base2,base3,base4,iva,peso,precio1,pond FROM sinv WHERE activo=\'S\'');
			$edit->tipo  = new autoUpdateField('tipo','C','C');
		}else{
			$titulo='Consignaci&oacute;n a Proveedor';
			$query=$this->db->query('SELECT TRIM(codigo) AS codigo ,TRIM(descrip) AS descrip,tipo,ultimo AS base1,ultimo AS base2,ultimo AS base3,ultimo AS base4,iva,peso,precio1,pond FROM sinv WHERE activo=\'S\'');
			$edit->tipo  = new autoUpdateField('tipo','R','R');
		}
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$ind='_'.$row->codigo;
				$inven[$ind]=array($row->descrip,$row->tipo,$row->base1,$row->base2,$row->base3,$row->base4,$row->iva,$row->peso,$row->precio1,$row->pond);
			}
		}
		$jinven=json_encode($inven);

		$conten['inven'] =$jinven;
		$conten['form']  =&  $edit;
		$data['content'] = $this->load->view('view_scon', $conten,true);
		$data['title']   = heading($titulo);
		$data['head']    = script('jquery.js').script('jquery-ui.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head().phpscript('nformat.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function _pre_insert($do){
		$tipo=$do->get('tipo');
		if($tipo=='C'){
			$numero = $this->datasis->fprox_numero('nsconc');
		}else{
			$numero = $this->datasis->fprox_numero('nsconp');
		}

		$fecha  = $do->get('fecha');

		$iva=$stotal=0;
		$cana=$do->count_rel('itscon');
		for($i=0;$i<$cana;$i++){
			$itcana    = $do->get_rel('itscon','cana',$i);
			$itprecio  = $do->get_rel('itscon','precio',$i);
			$itiva     = $do->get_rel('itscon','iva',$i);
			$itimporte = $itprecio*$itcana;
			$iiva      = $itimporte*($itiva/100);

			$do->set_rel('itscon','importe',$itimporte,$i);
			$do->set_rel('itscon','numero' ,$numero,$i);

			$iva    +=$iiva ;
			$stotal +=$itimporte;
		}

		$gtotal=$stotal+$iva;
		$do->set('numero'  ,$numero);
		$do->set('stotal'  ,round($stotal,2));
		$do->set('gtotal'  ,round($gtotal,2));
		$do->set('impuesto',round($iva   ,2));
		$do->set('status'  ,'T');

		return true;
	}

	function _post_insert($do){
		$tipod  = $do->get('tipod');
		$codigo = $do->get('numero');
		$id     = $do->get('id');
		$almacen= $do->get('almacen');
		$tipo   = $do->get('tipo');
		$fact   = ($tipod=='E') ? -1 : 1;

		$mSQL='UPDATE sinv JOIN itscon ON sinv.codigo=itscon.codigo SET sinv.existen=sinv.existen+('.$fact.')*(itscon.cana) WHERE itscon.id_scon='.$this->db->escape($id);
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'scon'); }

		$mSQL='UPDATE itsinv JOIN itscon ON itsinv.codigo=itscon.codigo SET itsinv.existen=itsinv.existen+('.$fact.')*(itscon.cana) WHERE itscon.id_scon='.$this->db->escape($id).' AND itsinv.alma='.$this->db->escape($almacen);
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'scon'); }

		$codigo=$do->get('numero');
		logusu('scon',"Prestamo de inventario $codigo CREADO");
	}

	function _pre_delete($do){
		return false;
	}

	function chpreca($preca,$ind){
		$codigo  = $this->input->post('codigo_'.$ind);
		$precio4 = $this->datasis->dameval('SELECT base4 FROM sinv WHERE codigo='.$this->db->escape($codigo));
		if($precio4<0) $precio4=0;

		if($preca<$precio4){
			$this->validation->set_message('chpreca', 'El art&iacute;culo '.$codigo.' debe contener un precio de al menos '.nformat($precio4));
			return false;
		}else{
			return true;
		}
	}

	function traeasoc($id,$scli,$numero){
		$num=$this->_traerasociado($scli,$numero);
		if($num===false){
			echo '<center>No se encontro n&uacute;mero asociado</center>';
		}
		elseif(empty($num)){
			echo '<center>No se encontro n&uacute;mero asociado, probablemente no fue cargado en la sucursal</center>';
			$dbid  =$this->db->escape($id);
			$sql = $this->db->update_string('scon',array('asociado' => $num),"id = $dbid");
			$this->db->simple_query($sql);
		}else{
			$dbid  =$this->db->escape($id);
			$sql = $this->db->update_string('scon',array('asociado' => $num),"id = $dbid");
			$this->db->simple_query($sql);
			echo '<center>El N&uacute;mero Asociado es:'.$num.'</center>';
		}
	}

	function _traerasociado($scli,$numero){
		$dbscli=$this->db->escape($scli);

		$sql="SELECT b.proveed,b.grupo,b.puerto,b.proteo,b.url,b.usuario,b.clave,b.tipo,b.depo,b.margen1,b.margen2,b.margen3,b.margen4,b.margen5 FROM sprv AS a JOIN b2b_config AS b ON a.proveed=b.proveed WHERE a.cliente=${dbscli}";
		$config=$this->datasis->damerow($sql);
		if(count($config)==0) return false;

		$er=0;
		$this->load->helper('url');
		$server_url = reduce_double_slashes($config['url'].'/'.$config['proteo'].'/'.'rpcserver');

		$this->load->library('xmlrpc');
		$this->xmlrpc->xmlrpc_defencoding=$this->config->item('charset');
		//$this->xmlrpc->set_debug(TRUE);
		$puerto= (empty($config['puerto'])) ? 80 : $config['puerto'];

		$this->xmlrpc->server($server_url , $puerto);
		$this->xmlrpc->method('consinu');

		$request = array($numero,$config['proveed'],$config['usuario'],md5($config['clave']));
		$this->xmlrpc->request($request);

		if (!$this->xmlrpc->send_request()){
			memowrite($this->xmlrpc->display_error(),'scon');
			return false;
		}else{
			$res=$this->xmlrpc->display_response();
			if(isset($res[0]))
				return $res[0];
			else
				return null;
		}
		return null;
	}

	function _pre_update($do){
		return false;
	}

	function instalar(){
		if (!$this->db->table_exists('scon')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `scon` (
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
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `numero` (`numero`, `tipo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('itscon')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `itscon` (
				`numero` CHAR(8) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`desca` VARCHAR(40) NULL DEFAULT NULL,
				`cana` DECIMAL(5,0) NULL DEFAULT NULL,
				`recibido` DECIMAL(5,0) NULL DEFAULT NULL,
				`precio` DECIMAL(12,2) NULL DEFAULT NULL,
				`importe` DECIMAL(12,2) NULL DEFAULT NULL,
				`iva` DECIMAL(8,2) NULL DEFAULT NULL,
				`id_scon` INT(15) UNSIGNED NOT NULL,
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `id_scon` (`id_scon`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('scon','origen')){
			$mSQL="ALTER TABLE scon ADD COLUMN origen CHAR(1) NOT NULL DEFAULT 'L' AFTER peso";
			var_dump($this->db->simple_query($mSQL));
		}
	}
}
