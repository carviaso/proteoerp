<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH . 'application/controllers/validaciones.php');
class pfaclitemayor extends validaciones{
	var $genesal=true;
	var $url ='ventas/pfaclitemayor/';

	function pfaclitemayor(){
		parent :: Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('142',1);
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datagrid', 'datafilter');
		$this->rapyd->uri->keep_persistence();

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$atts2 = array(
			'width'      => '480',
			'height'     => '240',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '980',
			'screeny'    => '760'
		);

		$scli = array(
			'tabla' => 'scli',
			'columnas' => array(
				'cliente' => 'C&oacute;digo Cliente',
				'nombre' => 'Nombre',
				'contacto' => 'Contacto'),
			'filtro' => array('cliente' => 'C&oacute;digo Cliente', 'nombre' => 'Nombre'),
			'retornar' => array('cliente' => 'cod_cli'),
			'titulo' => 'Buscar Cliente');

		$boton = $this->datasis->modbus($scli);

		$filter = new DataFilter('Filtro de Pedidos Clientes al mayor', 'pfac');

		$filter->fechad = new dateonlyField('Desde', 'fechad');
		$filter->fechah = new dateonlyField('Hasta', 'fechah');
		$filter->fechad->clause = $filter->fechah->clause   = 'where';
		$filter->fechad->db_name = $filter->fechah->db_name = 'fecha';
		$filter->fechah->size = $filter->fechad->size = 10;
		$filter->fechah->rule  = $filter->fechad->rule = 'trim';
		$filter->fechad->operator = '>=';
		$filter->fechah->operator = '<=';
		$filter->fechad->group = 'uno';
		$filter->fechah->group = 'uno';

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size = 10;
		$filter->numero->rule  = 'trim';
		$filter->numero->group = 'dos';

		$filter->cliente = new inputField('Cliente', 'cod_cli');
		$filter->cliente->size = 8;
		$filter->cliente->append($boton);
		$filter->cliente->rule  = 'trim';
		$filter->cliente->group = 'dos';

		$filter->buttons('reset', 'search');
		$filter->build('dataformfiltro');

		$uri  = anchor($this->url.'dataedit/<raencode><#cod_cli#></raencode>/show/<#id#>', '<#numero#>');
		$uri2 = anchor_popup('formatos/verhtml/PFAC/<#numero#>', 'Ver HTML', $atts);
		$uri3 = anchor('ventas/sfac_add/creafrompfac/<#numero#>/create', 'Facturar');

		$grid = new DataGrid('');
		$grid->order_by('numero', 'desc');
		$grid->per_page = 50;

		$grid->column_orderby('N&uacute;mero', $uri ,'numero');
		$grid->column_orderby('Factura'      , "<siinulo><#factura#>|$uri3|<#factura#></siinulo>",'factura');
		$grid->column_orderby('Fecha'        , '<dbdate_to_human><#fecha#></dbdate_to_human>','fecha', 'align=\'center\'');
		$grid->column_orderby('Cliente'      , 'cod_cli','cod_cli');
		$grid->column_orderby('Nombre'       , 'nombre','nombre');
		$grid->column_orderby('IVA'          , '<nformat><#iva#></nformat>'   , 'iva',    "align=right");
		$grid->column_orderby('Total'        , '<nformat><#totalg#></nformat>', 'totalg', "align=right");
		$grid->column_orderby('Status'       , 'status', 'status');

		$grid->add($this->url.'filterscli');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		//$data['head']   .= script('jquery-min.js');
		//$data['head']   .= script('nformat.js');
		//$data['head']   .= script('jquery-ui.custom.min.js');

		$data['title']   = heading('Pedidos Clientes');
		$this->load->view('view_ventanas', $data);
	}

	function filterscli(){
		$url=$this->url.'filteredgrid';
		$ven=$this->secu->getvendedor();
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence($url, $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : $url;

		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter('Selecci&oacute;n de Clientes', 'scli');
		//$filter->db->where('vendedor',$ven);
		$filter->button('btn_back',RAPYD_BUTTON_BACK,"javascript:window.location='".site_url($back)."'", 'BL');
		$filter->nombre= new inputField('Nombre','nombre');

		$filter->rifci= new inputField('CI/RIF','rifci');
		$filter->rifci->size=15;

		$filter->vd = new  dropdownField ('Vendedor', 'vendedor');
		$filter->vd->option('','Todos');
		$filter->vd->options('SELECT vendedor, CONCAT(vendedor,\' \',nombre) nombre FROM vend ORDER BY vendedor');
		$filter->vd->style='width:200px;';
		$filter->vd->insertValue=$ven;

		$filter->buttons('reset','search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/<raencode><#cliente#></raencode>/create','<#cliente#>');

		$grid = new DataGrid('Seleccione el clientes');
		$grid->order_by('nombre','asc');
		$grid->per_page=20;
		$grid->column_orderby('Cliente',$uri,'cliente');
		$grid->column_orderby('Nombre','nombre','nombre');
		$grid->column_orderby('RIF/CI','rifci');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = heading('Clientes');
		$data['head']    = $this->rapyd->get_head();
		$data['extras']  = '';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($cliente){
		if(!$this->_exitescli($cliente)) redirect($this->url.'filterscli');

		$this->rapyd->load('dataobject', 'dataedit');
		$this->rapyd->uri->keep_persistence();
		$this->load->helper('form');

		$do = new DataObject('pfac');
		$do->rel_one_to_many('itpfac', 'itpfac', array('numero' => 'numa'));
		$do->pointer('scli' , 'scli.cliente=pfac.cod_cli', 'scli.tipo AS sclitipo', 'left');
		$do->rel_pointer('itpfac', 'sinv', 'itpfac.codigoa=sinv.codigo', 'sinv.descrip AS sinvdescrip,
			sinv.iva AS sinviva,
			sinv.pond AS sinvpond,
			sinv.mmargen AS sinvmmargen,
			sinv.ultimo sinvultimo,sinv.formcal AS sinvformcal,
			sinv.pm AS sinvpm,
			sinv.existen AS pexisten,
			sinv.marca AS pmarca,
			sinv.descrip AS pdesca,
			sinv.escala1  AS sinvescala1,
			sinv.pescala1 AS sinvpescala1,
			sinv.escala2  AS sinvescala2,
			sinv.pescala2 AS sinvpescala2,
			sinv.escala3  AS sinvescala3,
			sinv.pescala3 AS sinvpescala3');
		$do->order_by('itpfac','sinv.marca',' ');
		$do->order_by('itpfac','sinv.descrip',' ');

		$edit = new DataEdit('Pedidos', $do);
		$edit->back_url = site_url('ventas/pfaclitemayor/filteredgrid');
		//$edit->set_rel_title('itpfac', 'Producto <#o#>');

		$edit->pre_process('insert' , '_pre_insert');
		$edit->pre_process('update' , '_pre_update');
		$edit->pre_process('delete' , '_pre_delete');
		$edit->post_process('insert', '_post_insert');
		$edit->post_process('update', '_post_update');
		$edit->post_process('delete', '_post_delete');

		$edit->fecha = new inputField('Fecha', 'fecha');
		$edit->fecha->insertValue = date('Y-m-d');
		//$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;

		$usr=$this->session->userdata('usuario');
		$vd=$this->datasis->dameval("SELECT vendedor FROM usuario WHERE us_codigo='$usr'");
		$edit->vd = new hiddenField ('Vendedor', 'vd');
		$edit->vd->value = $vd;

		$edit->mmargen = new inputField('mmargen', 'mmargen');

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode = 'autohide';
		$edit->numero->maxlength = 8;
		$edit->numero->apply_rules = false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when = array('show', 'modify');

		$edit->cliente = new hiddenField('Cliente', 'cod_cli');
		$edit->cliente->insertValue=$cliente;
		//$edit->cliente->options("SELECT cliente, nombre FROM scli WHERE vendedor='$vd' LIMIT 5");

		$edit->rifci = new inputField('RIF/CI', 'rifci');
		$edit->rifci->autocomplete = false;
		$edit->rifci->size = 15;
		$edit->rifci->type ='inputhidden';

		$edit->direc = new inputField('Direcci&oacute;n', 'direc');
		$edit->direc->size = 40;
		$edit->direc->type ='inputhidden';

		$edit->observa = new inputField('Observaciones', 'observa');
		$edit->observa->size = 25;

		$edit->observ1 = new inputField('Observaciones', 'observ1');
		$edit->observ1->size = 25;

		//Descuento por grupo y cliente
		$sel=array('a.mmargen','b.margen');
		$this->db->select($sel);
		$this->db->from('scli AS a');
		$this->db->join('zona AS b','a.zona=b.codigo','left');
		$this->db->where('a.cliente',$cliente);
		$qdes=$this->db->get();
		if ($qdes->num_rows() > 0){
			$rdes = $qdes->row();

		}else{
			$rdes = new stdClass;
			$rdes->mmargen = 0;
			$rdes->margen  = 0;
		}


		// Campos para el detalle
		$i=0;
		$sel=array('a.codigo','a.descrip','a.existen','a.marca','a.iva','e.sinv_id'
		,'ROUND(IF(formcal="U",ultimo,IF(formcal="P",pond,GREATEST(ultimo,pond)))*(100+a.mmargen)/100,2) AS precio'
		,'IF(formcal="U",ultimo,IF(formcal="P",pond,GREATEST(ultimo,pond))) AS costo'
		,'a.mmargen'
		,'a.mmargenplus'
		,'c.margen AS DM'
		,'d.margen AS DG'
		,'a.escala1','a.pescala1'
		,'a.escala2','a.pescala2'
		,'a.escala3','a.pescala3');
		$this->db->distinct();
		$this->db->select($sel);
		$this->db->from('sinv AS a');
		$this->db->join('sinvfot AS e','a.id=e.sinv_id','left');
		$this->db->where('a.activo','S');
		$this->db->where('a.tipo','Articulo');
		$this->db->orderby('a.marca');
		$this->db->orderby('a.descrip');
		$numero=$edit->get_from_dataobjetct('numero');
		if($numero!==false){
			$dbnumero=$this->db->escape($numero);
			$this->db->join('itpfac AS b','a.codigo=b.codigoa AND b.numa='.$dbnumero);
		}
		$this->db->join('marc AS c','a.marca=c.marca');
		$this->db->join('grup AS d','a.grupo=d.grupo');

		$renglones=$this->datasis->traevalor('PFACMAYRENGLONES','Limites de renglones en el pedido al mayor');
		if(empty($renglones)) $renglones=300;
		$this->db->limit($renglones);

		$query = $this->db->get();

		foreach ($query->result() as $row){
			$obj='codigoa_'.$i;
			$edit->$obj = new hiddenField('C&oacute;digo <#o#>', $obj);
			$edit->$obj->ind     = $i;
			$edit->$obj->size    = 12;
			$edit->$obj->db_name = 'codigoa';
			$edit->$obj->rel_id  = 'itpfac';
			$edit->$obj->rule    = 'callback_chcodigoa';
			$edit->$obj->insertValue=$row->codigo;

			$obj='desca_'.$i;
			$desca=ucfirst(strtolower($row->descrip));
			if(!empty($row->sinv_id)){
				$urldir = $this->config->slash_item('base_url').'images/foto.gif';
				$desca .= ' <img src="'.$urldir.'" onclick="verimage(\''.$row->sinv_id.'\')">';
			}
			$edit->$obj = new freeField($obj,$obj,$desca);
			$edit->$obj->ind = $i;

			$obj='pexisten_'.$i;
			$edit->$obj = new freeField($obj,$obj,$row->existen);
			$edit->$obj->ind = $i;
			$edit->$obj->pointer=true;

			$obj='cana_'.$i;
			$edit->$obj = new inputField('Cantidad <#o#>', $obj);
			$edit->$obj->ind          = $i;
			$edit->$obj->db_name      = 'cana';
			$edit->$obj->css_class    = 'inputnum';
			$edit->$obj->rel_id       = 'itpfac';
			$edit->$obj->maxlength    = 10;
			$edit->$obj->size         = 5;
			$edit->$obj->autocomplete = false;
			$edit->$obj->style        = "height:25px;font-size:14";
			$edit->$obj->onkeyup      = "cescala('$i')";
			$edit->$obj->rule         = "callback_chescala[$i]";

			$obj='pmarca_'.$i;
			$edit->$obj = new inputField('', $obj);
			$edit->$obj->ind     = $i;
			$edit->$obj->db_name = 'pmarca';
			$edit->$obj->rel_id  = 'itpfac';
			$edit->$obj->pointer = true;
			$edit->$obj->insertValue=$row->marca;

			$obj='preca_'.$i;
			$edit->$obj = new inputField('Precio <#o#>', $obj);
			$edit->$obj->ind        = $i;
			$edit->$obj->db_name    = 'preca';
			$edit->$obj->css_class  = 'inputnum';
			$edit->$obj->rel_id     = 'itpfac';
			$edit->$obj->type       = 'inputhidden';
			$edit->$obj->insertValue= $row->precio;
			$edit->$obj->rule       = 'positive|callback_chpreca[<#i#>]';

			$obj='itiva_'.$i;
			$edit->$obj = new hiddenField('', $obj);
			$edit->$obj->ind        = $i;
			$edit->$obj->db_name    = 'iva';
			$edit->$obj->rel_id     = 'itpfac';
			$edit->$obj->insertValue= $row->iva;

			$obj='dxapli_'.$i;
			$edit->$obj         = new autoUpdateField('dxapli','0','0');
			$edit->$obj->rel_id = 'itpfac';
			$edit->$obj->ind    = $i;

			$obj='dxm_'.$i;
			if($row->DM>0){
				$edit->$obj = new checkboxField('dxm', $obj, $row->DM,'0');
				$edit->$obj->insertValue = 0;
				$edit->$obj->onchange = "cprecio('$i')";
			}else{
				$edit->$obj = new autoUpdateField('dxm','0','0');
			}
			$edit->$obj->db_name= 'dxm';
			$edit->$obj->ind    = $i;
			$edit->$obj->rel_id = 'itpfac';

			$obj='dxg_'.$i;
			if($row->DG>0){
				$edit->$obj = new checkboxField('dxg', $obj, $row->DG,'0');
				$edit->$obj->insertValue = 0;
				$edit->$obj->onchange = "cprecio('$i')";
			}else{
				$edit->$obj    = new autoUpdateField('dxg','0','0');
			}
			$edit->$obj->ind     = $i;
			$edit->$obj->db_name = 'dxg';
			$edit->$obj->rel_id  = 'itpfac';

			$obj='dxz_'.$i;
			if($rdes->margen>0){
				$edit->$obj = new checkboxField('dxz', $obj, $rdes->margen,'0');
				$edit->$obj->insertValue = 0;
				$edit->$obj->onchange = "cprecio('$i')";
			}else{
				$edit->$obj    = new autoUpdateField('dxz','0','0');
			}
			$edit->$obj->rel_id      = 'itpfac';
			$edit->$obj->db_name     = 'dxz';
			$edit->$obj->ind         = $i;

			$obj='dxc_'.$i;
			if($rdes->mmargen>0){
				$edit->$obj = new checkboxField('dxe', $obj, $rdes->mmargen,'0');
				$edit->$obj->insertValue = 0;
				$edit->$obj->onchange = "cprecio('$i')";
			}else{
				$edit->$obj    = new autoUpdateField('dxe','0','0');
			}
			$edit->$obj->rel_id      = 'itpfac';
			$edit->$obj->db_name     = 'dxc';
			$edit->$obj->ind         = $i;

			$obj='dxp_'.$i;
			if($row->mmargenplus>0){
				$edit->$obj = new checkboxField('dxp', $obj, $row->mmargenplus,'0');
				$edit->$obj->insertValue = 0;
				$edit->$obj->onchange = "cprecio('$i')";
			}else{
				$edit->$obj    = new autoUpdateField('dxp','0','0');
			}
			$edit->$obj->rel_id      = 'itpfac';
			$edit->$obj->db_name     = 'dxp';
			$edit->$obj->ind         = $i;

			$p_es=0;
			for($u=1;$u<4;$u++){
				$nom="escala${u}";
				$obj="${nom}_${i}";
				$edit->$obj = new hiddenField('', $obj);
				$edit->$obj->insertValue = $row->$nom;
				$edit->$obj->rel_id      = 'itpfac';
				$edit->$obj->db_name     = 'sinv'.$nom;
				$edit->$obj->ind         = $i;
				$edit->$obj->pointer     = true;

				$nom="pescala${u}";
				$obj="${nom}_${i}";
				$edit->$obj = new hiddenField('', $obj);
				$edit->$obj->insertValue = $row->$nom;
				$edit->$obj->rel_id      = 'itpfac';
				$edit->$obj->db_name     = 'sinv'.$nom;
				$edit->$obj->ind         = $i;
				$edit->$obj->pointer     = true;
				$p_es+=$row->$nom;
			}

			$gdxe=$edit->get_from_dataobjetct_rel('itpfac','dxe',$i);

			$obj='dxe_'.$i;
			if($p_es>0){
				$gdxe=$edit->get_from_dataobjetct_rel('itpfac','dxe',$i);
				$edit->$obj = new checkboxField('dxe', $obj, ($gdxe==false)?'0':$gdxe,'0');
				$edit->$obj->onchange = "cescala('$i')";
			}else{
				$edit->$obj    = new autoUpdateField('dxp','0','0');
			}
			//$edit->$obj->insertValue = 0;
			$edit->$obj->rel_id      = 'itpfac';
			$edit->$obj->db_name     = 'dxe';
			$edit->$obj->ind         = $i;

			$obj='sinvmmargen_'.$i;
			$edit->$obj = new hiddenField('', $obj);
			$edit->$obj->insertValue = $row->mmargen;
			$edit->$obj->rel_id      = 'itpfac';
			$edit->$obj->db_name     = 'sinvmmargen';
			$edit->$obj->ind         = $i;
			$edit->$obj->pointer     = true;

			$obj='costo_'.$i;
			$edit->$obj = new hiddenField('', $obj);
			$edit->$obj->insertValue = $row->costo;
			$edit->$obj->rel_id      = 'itpfac';
			$edit->$obj->db_name     = 'costo';
			$edit->$obj->ind         = $i;

			$obj='tota_'.$i;
			$edit->$obj = new hiddenField('', $obj);
			$edit->$obj->insertValue = 0;
			$edit->$obj->rel_id      = 'itpfac';
			$edit->$obj->db_name     = 'tota';
			$edit->$obj->ind         = $i;

			$i++;
		}
		$sinvcana=$i;
		// fin de campos para detalle

		$edit->ivat = new inputField('Impuesto', 'iva');
		$edit->ivat->css_class = 'inputnum';
		$edit->ivat->type      = 'inputhidden';
		$edit->ivat->readonly  = true;
		$edit->ivat->size = 10;

		$edit->totals = new inputField('Sub-Total', 'totals');
		$edit->totals->css_class = 'inputnum';
		$edit->totals->type      = 'inputhidden';
		$edit->totals->readonly = true;
		$edit->totals->size = 10;

		$edit->totalg = new inputField('Monto Total', 'totalg');
		$edit->totalg->css_class = 'inputnum';
		$edit->totalg->type      = 'inputhidden';
		$edit->totalg->readonly = true;
		$edit->totalg->size = 10;

		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->usuario = new autoUpdateField('usuario',$this->secu->usuario(),$this->secu->usuario());

		$control=$this->rapyd->uri->get_edited_id();

		if($edit->getstatus()=='show'){
			$action = "javascript:window.location='".site_url($this->url.'filterscli')."'";
			$edit->button('btn_add', 'Agregar', $action, 'TR');
		}

		$edit->buttons('save','undo', 'modify','delete', 'back');

		if($this->genesal){
			$edit->build();

			$conten['cana']  = $sinvcana;
			$conten['form']  = & $edit;
			$conten['title'] = heading('Pedidos No. '.$edit->numero->value);
			$data['head']    = style('mayor/estilo.css');
			$data['script']  = script('jquery.js');
			$data['script'] .= phpscript('nformat.js');
			$data['content'] = $this->load->view('view_pfaclitemayor', $conten,true);
			$data['title']   = '';
			$this->load->view('view_ventanas_lite', $data);

		}else{
			$edit->on_save_redirect=false;
			$edit->build();

			if($edit->on_success()){
				echo 'Pedido Guardado';
			}elseif($edit->on_error()){
				echo html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}
		}
	}

	function crea(){
		$npfactemp=$this->datasis->fprox_numero('npfactemp');
		$npfactemp=substr($npfactemp,1);
		$query="INSERT INTO itpfac(`numa`,`codigoa`,`desca`,`cana`,`preca`,`tota`,`iva`)
		SELECT '_".$npfactemp."',codigo,descrip            ,0     ,precio1,0     ,iva FROM sinv WHERE activo='S'";
		$this->db->query($query);
		$query="INSERT INTO pfac(`numero`,`fecha`) VALUES('_".$npfactemp."',CURDATE())";
		$this->db->query($query);
		$id=$this->db->insert_id();
		redirect('ventas/pfaclite/dataedit/modify/'.$id);
	}

	function _pre_insert($do){
		//Chequea que llege al menos un articulo
		$ccana = 0;
		$cana  = $do->count_rel('itpfac');
		for($i=0;$i < $cana;$i++){
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			if($itcana>0){
				$ccana++;
			}
		}
		if($ccana<=0){
			$do->error_message_ar['pre_ins']=$do->error_message_ar['insert']='No puede enviar un pedido sin art&iacute;culos';
			return false;
		}

		$numero = $this->datasis->fprox_numero('npfac');
		$do->set('numero', $numero);
		$fecha = $do->get('fecha');
		$vd    = $this->secu->getvendedor();
		$do->set('vd',$vd);
		$do->set('status','P');

		$cod_cli = $do->get('cod_cli');
		$rrow    = $this->datasis->damerow('SELECT nombre,rifci,zona,vendedor FROM scli WHERE cliente='.$this->db->escape($cod_cli));
		if($rrow!=false){
			$do->set('nombre',$rrow['nombre']);
			$do->set('rifci' ,$rrow['rifci']);
			$do->set('zona'  ,$rrow['zona']);
			$do->set('vd'    ,$rrow['vendedor']);
		}

		$iva = $totals = 0;
		for($i = 0;$i < $cana;$i++){
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			if($itcana>0){
				$itcodigo = $do->get_rel('itpfac', 'codigoa', $i);
				$itpreca  = $do->get_rel('itpfac', 'preca', $i);
				$itiva    = $do->get_rel('itpfac', 'iva', $i);
				$ittota   = $itpreca * $itcana;
				$do->set_rel('itpfac', 'tota'    , $ittota, $i);
				$do->set_rel('itpfac', 'fecha'   , $fecha , $i);
				$do->set_rel('itpfac', 'vendedor', $vd    , $i);
				$dbcodigoa= $this->db->escape($itcodigo);
				$desca    = $this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo=$dbcodigoa");
				$do->set_rel('itpfac', 'desca'   , $desca , $i);

				$iva    += $ittota * ($itiva / 100);
				$totals += $ittota;
				$do->set_rel('itpfac', 'mostrado', $iva + $ittota, $i);
			}else{
				//$do->rel_rm('itpfac',$i);
			}
		}
		$totalg = $totals + $iva;

		$do->set('totals' , round($totals , 2));
		$do->set('totalg' , round($totalg , 2));
		$do->set('iva'    , round($iva    , 2));
		return true;
	}

	function _pre_update($do){
		$error='';
		$codigo = $do->get('numero');
		$fecha  = $do->get('fecha');
		$vd     = $do->get('vd');
		$fenvia = $do->get('fenvia');
		$faplica= $do->get('faplica');

		$iva = $totals = 0;
		//$cana  = $do->count_rel('itpfac');
		$claves=array_keys($do->data_rel['itpfac']);
		foreach($claves as $i){
			$codigoa = $do->get_rel('itpfac', 'codigoa', $i);
			$itcana  = $do->get_rel('itpfac', 'cana'   , $i);
			$itpreca = $do->get_rel('itpfac', 'preca'  , $i);
			$itiva   = $do->get_rel('itpfac', 'iva'    , $i);

			if(($faplica < $fenvia)){
				$itdxapli = $do->get_rel('itpfac', 'dxapli', $i);
				$itprecat = $this->input->post("precat_$i");
				if(!$itdxapli)
				$itdxapli=' ';

				$itpreca  = $this->cal_dxapli($itprecat,$itdxapli);
				if(1*$itpreca>0){
					$do->set_rel('itpfac', 'preca'  , $itpreca, $i);
					$do->set('faplica',date('Y-m-d'));
				}else{
					$error.="Error. El descuento por aplicar es incorrecto para el codigo $codigoa</br>";
				}
			}

			$ittota  = $itpreca * $itcana;
			$do->set_rel('itpfac', 'tota'    , $ittota, $i);
			$do->set_rel('itpfac', 'fecha'   , $fecha , $i);
			$do->set_rel('itpfac', 'vendedor', $vd    , $i);

			$dbcodigoa= $this->db->escape($codigoa);
			$desca    = $this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo=$dbcodigoa");
			$do->set_rel('itpfac', 'desca'   , $desca , $i);

			$iva    += $ittota*$itiva/100;
			$totals += $ittota;
			$do->set_rel('itpfac', 'mostrado', $iva + $ittota, $i);
		}
		$totalg = $totals + $iva;

		$do->set('totals' , round($totals , 2));
		$do->set('totalg' , round($totalg , 2));
		$do->set('iva'    , round($iva    , 2));

		$mSQL='UPDATE sinv JOIN itpfac ON sinv.codigo=itpfac.codigoa SET sinv.exdes=sinv.exdes-itpfac.cana WHERE itpfac.numa='.$this->db->escape($codigo);
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'pfac'); }

		if(!empty($error)){
			$do->error_message_ar['pre_ins']=$error;
			$do->error_message_ar['pre_upd']=$error;
			return false;
		}
		return true;
	}

	function chescala($cana,$ind){
		$codigo  =$this->input->post('codigoa_'.$ind);

		$dbcodigo=$this->db->escape($codigo);
		$clave   =$this->datasis->dameval('SELECT clave FROM sinv WHERE codigo='.$dbcodigo);
		if(preg_match('/VX(?P<esc>\d+)/', $clave, $matches)>0){
			if($cana % $matches['esc'] == 0){
				return true;
			}else{
				$this->validation->set_message('chescala', "La cantidad del producto $codigo debe ser multiplo de ".$matches['esc']);
				return false;
			}
		}else{
			return true;
		}
	}

	function chcodigoa($codigo){
		$cana=$this->datasis->dameval('SELECT COUNT(*) FROM sinv WHERE activo=\'S\' AND codigo='.$this->db->escape($codigo));
		if(empty($cana) || $cana==0){
			$this->validation->set_message('chcodigoa', 'El campo %s contiene un codigo no v&aacute;lido o inactivo');
			return false;
		}
		return true;
	}

	function _post_insert($do){
		$claves=array_keys($do->data_rel['itpfac']);
		foreach($claves as $i){
			$itcodigo= $do->get_rel('itpfac', 'codigoa', $i);
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			$mSQL = "UPDATE sinv SET exdes=exdes+$itcana WHERE codigo=".$this->db->escape($itcodigo);

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'pfac'); }
		}

		$codigo = $do->get('numero');
		logusu('pfac', "Pedido $codigo CREADO");
	}

	function enviar($id){
		$ide=$this->db->escape($id);
		$this->db->query("UPDATE pfac SET fenvia=CURDATE() WHERE id=$ide");
		redirect("ventas/pfaclite/dataedit/show/$id");
	}


	function _post_update($do){
		$claves=array_keys($do->data_rel['itpfac']);
		foreach($claves as $i){
			$itcodigo= $do->get_rel('itpfac', 'codigoa', $i);
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			$mSQL = "UPDATE sinv SET exdes=exdes+$itcana WHERE codigo=".$this->db->escape($itcodigo);

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'pfac'); }
		}
		$codigo = $do->get('numero');
		logusu('pfac', "Pedido $codigo MODIFICADO");
	}

	function _pre_delete($do){
		$codigo = $do->get('numero');
		$mSQL='UPDATE sinv JOIN itpfac ON sinv.codigo=itpfac.codigoa SET sinv.exdes=sinv.exdes-itpfac.cana WHERE itpfac.numa='.$this->db->escape($codigo);
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'pfac'); }
		return true;
	}

	function _post_delete($do){
		$codigo = $do->get('numero');
		logusu('pfac', "Pedido $codigo ELIMINADO");
	}

	function _exitescli($cliente){
		$dbscli= $this->db->escape($cliente);
		$mSQL  = "SELECT COUNT(*) AS cana FROM scli WHERE cliente=$dbscli";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if( $row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function instalar(){
		if(!$this->db->field_exists('dxm', 'itpfac')){
			$mSQL="ALTER TABLE `itpfac`
			CHANGE COLUMN `dxapli` `dxapli` VARCHAR(20) NULL COMMENT 'descuento por aplicar' AFTER `id`,
			ADD COLUMN `dxm` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento por marca' AFTER `dxapli`,
			ADD COLUMN `dxg` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento por grupo' AFTER `dxm`,
			ADD COLUMN `dxz` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento por zona' AFTER `dxg`,
			ADD COLUMN `dxc` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento por cliente' AFTER `dxz`,
			ADD COLUMN `dxe` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento por escala' AFTER `dxc`,
			ADD COLUMN `dxp` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento plus' AFTER `dxe`,
			ADD COLUMN `escala` INT(8) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'cantidad para la escala' AFTER `dxe`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('margen', 'zona')){
			$mSQL="ALTER TABLE `zona` ADD COLUMN `margen` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' AFTER `descrip`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('margen', 'marc')){
			$mSQL="ALTER TABLE `marc` ADD COLUMN `margen` DOUBLE(5,2) UNSIGNED NOT NULL DEFAULT '0' AFTER `marca`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('escala1', 'sinv')){
			$mSQL="ALTER TABLE `sinv`
			ADD COLUMN `escala1` DECIMAL(12,2) NULL DEFAULT '0.00' AFTER `pmb`,
			ADD COLUMN `pescala1` DECIMAL(5,2) NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala1' AFTER `escala1`,
			ADD COLUMN `escala2` DECIMAL(12,2) NULL DEFAULT '0.00' AFTER `pescala1`,
			ADD COLUMN `pescala2` DECIMAL(5,2) NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala2' AFTER `escala2`,
			ADD COLUMN `escala3` DECIMAL(12,2) NULL DEFAULT '0.00' AFTER `pescala2`,
			ADD COLUMN `pescala3` DECIMAL(5,2) NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala3' AFTER `escala3`";
			$this->db->simple_query($mSQL);

			$mSQL="ALTER TABLE `sinv` ADD COLUMN `mmargenplus` DECIMAL(7,2) NULL DEFAULT '0.00' COMMENT 'Margen al Mayor' AFTER `mmargen`";
			$this->db->simple_query($mSQL);
		}

	}
}
