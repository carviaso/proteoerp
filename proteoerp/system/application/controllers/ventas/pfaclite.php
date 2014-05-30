<?php
/**
 * ProteoERP
 *
 * @autor    Ender Ochoa, Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH . 'application/controllers/validaciones.php');
class pfaclite extends validaciones{
	var $genesal= true;
	var $limit  = 1000;
	var $url    = 'ventas/pfaclite/';

	function pfaclite(){
		parent :: Controller();
		$this->load->library('rapyd');
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->datasis->modulo_id(143,1);
		$this->rapyd->load('datagrid', 'datafilter');

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
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

		$vd=trim($this->secu->getvendedor());

		$filter = new DataFilter('Filtro de Pedidos Clientes', 'pfac');

		$filter->fechad = new dateonlyField('Fecha Desde', 'fechad');
		$filter->fechah = new dateonlyField('Fecha Hasta', 'fechah');
		$filter->fechad->clause = $filter->fechah->clause   = 'where';
		$filter->fechad->db_name = $filter->fechah->db_name = 'fecha';
		$filter->fechah->size = $filter->fechad->size = 10;
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

		$accion="javascript:window.location='".site_url('ventas/pfaclite/load')."'";
		$filter->button('btn_load','Subir desde Excel',$accion,'TR');

		if(strlen($vd)>0){
			$filter->db->where('vd',$vd);
			$accion="javascript:window.location='".site_url('ventas/pfaclite/pfl')."'";
			$filter->button('btn_pfl','Descargar Hoja de Excel',$accion,'TR');
		}

		$filter->buttons('reset', 'search');
		$filter->build('dataformfiltro');

		function hfactura($status,$factura,$numero,$vence=null,$act=false){
			if($status=='P'){        //Pendiente
				if($act){
					//$rt = anchor('ventas/sfac_add/creafrompfac/'.$numero.'/create', 'Pendiente');
					$rt = 'Pendiente';
				}else{
					$rt = 'Pendiente';
				}
			}elseif($status=='C'){   //Cerrado
				if(!empty($factura)){
					$rt = $factura;
				}elseif(!empty($vence)){
					$rt = 'Expirado';
				}else{
					$rt = 'Cerrado';
				}
			}elseif($status=='B'){   //BackOrder
				$rt = 'BackOrder';
			}elseif($status=='A'){   //Anulado
				$rt = 'Anulado';
			}elseif($status=='T'){   //Temporal
				$rt = 'Temporal';
			}elseif($status=='E' || $status=='U'){ //Estatus locales de vendores ambulantes (Enviado y por enviar )
				$rt = 'V.Externo';
			}else{
				$rt = 'Desconocido';
			}
			return $rt;
		}

		$uri  = anchor('ventas/pfaclite/dataedit/<raencode><#cod_cli#></raencode>/show/<#id#>', '<#numero#>');
		$uri2 = anchor_popup('formatos/verhtml/PFAC/<#numero#>', 'Ver HTML', $atts);
		$uri3 = anchor('ventas/sfac_add/creafrompfac/<#numero#>/create', 'Facturar');

		$grid = new DataGrid('Lista de pedidos realizados');
		$grid->use_function('hfactura');
		$grid->order_by('numero', 'desc');
		$grid->per_page = 50;

		$grid->column_orderby('N&uacute;mero', $uri ,'numero');
		if($this->secu->puede('103')){
			$grid->column_orderby('Factura'     , '<hfactura><#status#>|<#factura#>|<#numero#>|<#vence#>|1</hfactura>','factura');
		}else{
			$grid->column_orderby('Factura'     , '<hfactura><#status#>|<#factura#>|<#numero#>|<#vence#></hfactura>','factura');
		}
		$grid->column_orderby('Fecha'        , '<dbdate_to_human><#fecha#></dbdate_to_human> <#hora#>','fecha', 'align=\'center\'');
		$grid->column_orderby('Cliente'      , 'cod_cli','cod_cli');
		$grid->column_orderby('Nombre'       , 'nombre' ,'nombre' );
		if(!(strlen($vd)>0))
			$grid->column_orderby('Vend.'     , 'vd'     ,'vd');
		$grid->column_orderby('Peso'         , '<nformat><#peso#></nformat>'  , 'peso'  , 'align=\'right\'');
		$grid->column_orderby('Total'        , '<nformat><#totalg#></nformat>', 'totalg', 'align=\'right\'');

		if(strlen($vd)>0){
			$grid->add($this->url.'filterscli','Incluir nuevo pedido');
		}
		$grid->build();

		$data['content'] = $filter->output.$grid->output;

		$data['script']  = script('jquery.js');
		$data['script'] .= script('jquery-ui.js');
		$data['script'] .= script('plugins/jquery.numeric.pack.js');
		$data['script'] .= script('plugins/jquery.floatnumber.js');

		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Pedidos Clientes');
		$this->load->view('view_ventanas', $data);
	}

	function vencepedido(){
		$sel=array('b.codigoa','SUM(b.cana) AS cana');
		$this->db->select($sel);
		$this->db->from('pfac AS a');
		$this->db->join('itpfac AS b','a.numero=b.numa');
		$this->db->where('a.fecha < DATE_SUB(CURDATE(),INTERVAL 5 DAY)');
		$this->db->where('a.status','P');
		$this->db->where('b.codigoa IS NOT NULL');
		$this->db->group_by('b.codigoa');
		$query=$this->db->get();

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$dbcodigo= $this->db->escape($row->codigoa);
				$itcana  = $row->cana;

				$mSQL = "UPDATE sinv SET exdes=IF(exdes IS NULL OR exdes>=${itcana} , 0 , exdes-${itcana}) WHERE codigo=$dbcodigo";
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'pfaclite'); }
			}
			$mSQL="UPDATE pfac SET status='C', vence=CURDATE() WHERE fecha < DATE_SUB(CURDATE(),INTERVAL 5 DAY) AND status='P'";
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'pfaclite'); }
		}

		$sel=array('b.codigoa','SUM(b.cana) AS cana');
		$this->db->select($sel);
		$this->db->from('pfac AS a');
		$this->db->join('itpfac AS b','a.numero=b.numa');
		$this->db->where('a.status','P');
		$this->db->where('b.codigoa IS NOT NULL');
		$this->db->group_by('b.codigoa');
		$query=$this->db->get();

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$dbcodigo= $this->db->escape($row->codigoa);
				$itcana  = $row->cana;

				$mSQL = "UPDATE sinv SET exdes=${itcana} WHERE codigo=${dbcodigo}";
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'pfaclite'); }
			}
		}
	}

	function filterscli(){
		$this->datasis->modulo_id(143,1);
		$vd   = trim($this->secu->getvendedor());
		$caub = trim($this->secu->getalmacen());
		if(empty($vd) || empty($caub)){
			show_error('Usuario no tiene asignado vendedor, cajero o almacen, debe asignarlo primero para poder usar este modulo');
		}

		$url=$this->url.'filteredgrid';
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence($url, $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri']))? $persistence['back_uri'] : $url;
		$vd   = $this->secu->getvendedor();

		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter('Lista de clientes asignados al vendedor '.$vd);
		$filter->button('btn_back',RAPYD_BUTTON_BACK,"javascript:window.location='".site_url($back)."'", 'BL');
		$dbvd = $this->db->escape($vd);

		$dbfini=$this->db->escape(date('Y-m-d', mktime(0, 0, 0, date('n'),1)));
		$sel=array('a.cliente','a.nombre','a.rifci','SUM(b.numero IS NOT NULL) AS rayado');
		$filter->db->select($sel);
		$filter->db->from('scli AS a');
		$filter->db->join('sfac AS b',"b.cod_cli=a.cliente AND b.vd=${dbvd} AND b.fecha>=${dbfini}",'left');
		$filter->db->where("( a.vendedor = ${dbvd} OR a.cobrador=${dbvd} )");
		$filter->db->where('a.tipo <>','0');
		$filter->db->groupby('a.cliente');

		$filter->cliente = new inputField('C&oacute;digo', 'cliente');
		$filter->cliente->size = 8;
		//$filter->cliente->append($boton);

		$filter->nombre= new inputField('Nombre','nombre');
		$filter->nombre->db_name = 'a.nombre';

		$filter->rifci= new inputField('CI/RIF','rifci');
		$filter->rifci->size=15;

		$filter->buttons('reset','search');
		$filter->build();

		if(!empty($vd)){
			$mSQL="SELECT COUNT(*) AS cana FROM scli WHERE ( vendedor = ${dbvd} OR cobrador=${dbvd} ) AND tipo<>0";
			$clientes=$this->datasis->dameval($mSQL);

			$mSQL="SELECT COUNT(*) AS cana FROM sfac WHERE vd = ${dbvd} AND fecha>=${dbfini}";
			$facturas=$this->datasis->dameval($mSQL);

			$mSQL="SELECT COUNT(*) AS cca FROM (SELECT 1 AS cana FROM scli AS a JOIN sfac AS b ON b.cod_cli=a.cliente WHERE b.fecha>=${dbfini} AND a.vendedor=${dbvd} GROUP BY a.cliente) AS aa";
			$atendidos=$this->datasis->dameval($mSQL);

			$mSQL="SELECT SUM(c.peso*a.cana*IF(a.tipoa='F',1,-1)) AS peso FROM sitems AS a JOIN sfac AS b ON a.numa=b.numero AND a.tipoa=b.tipo_doc JOIN sinv AS c ON a.codigoa=c.codigo WHERE b.vd = ${dbvd} AND a.tipoa<>'X' AND b.fecha>=${dbfini}";
			$ttpeso=nformat(floatval($this->datasis->dameval($mSQL))/1000,3);

			$efe = htmlnformat($atendidos*100/$clientes);
			$frace = "<p style='text-align:center;font-weight: bold;'>Clientes atendidos: <span style='font-size:1.5em; color:#000063'>${atendidos}</span>/${clientes} Efectividad: <span style='font-size:1.5em; color:#000063'>${efe}%</span> Facturas: ${facturas} Peso: ${ttpeso}T</p>";
		}else{
			$frace='';
		}

		$uri = anchor($this->url.'dataedit/<raencode><#cliente#></raencode>/create','<#cliente#>');

		$grid = new DataGrid('Seleccione el cliente al cual se le va a realizar el pedido');
		$grid->use_function('htmlspecialchars');
		$grid->order_by('nombre','asc');
		$grid->per_page=20;
		$grid->column_orderby('#Fact.','rayado','rayado',"align='right'");
		$grid->column_orderby('Cliente',$uri,'cliente');
		$grid->column_orderby('Nombre','<htmlspecialchars><#nombre#>|2|ISO-8859-1</htmlspecialchars>','nombre');
		$grid->column_orderby('RIF/CI','rifci');
		$grid->build();

		$data['content'] = $filter->output.$frace.$grid->output;
		$data['title']   = heading('Clientes');
		$data['head']    = $this->rapyd->get_head();
		$data['extras']  = '';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($cliente='',$status='',$id=''){
		$this->datasis->modulo_id(143,1);

		if(!$this->_exitescli($cliente)) redirect($this->url.'filterscli');

		$this->db->select_sum('a.monto*IF(tipo_doc IN ("FC","ND","GI"),1,-1)','saldo');
		$this->db->from('smov AS a');
		$this->db->where('a.cod_cli',$cliente);
		$q=$this->db->get();
		$row = $q->row_array();
		$saldo = (empty($row['saldo']))? 0: $row['saldo'];

		$this->rapyd->load('dataobject', 'datadetails');
		$this->load->helper('form');

		$vd   = $this->secu->getvendedor();
		$dbvd = $this->db->escape($vd);

		$do = new DataObject('pfac');
		$do->rel_one_to_many('itpfac', 'itpfac', array('numero' => 'numa'));
		$do->pointer('scli' , 'scli.cliente=pfac.cod_cli', 'scli.tipo AS sclitipo', 'left');
		$do->rel_pointer('itpfac', 'sinv', 'itpfac.codigoa=sinv.codigo', '
			sinv.iva AS sinviva,
			sinv.existen AS pexisten,
			sinv.marca AS pmarca,
			sinv.descrip AS pdesca,
			sinv.peso AS ppeso');
		$do->order_by('itpfac','sinv.marca',' ');
		$do->order_by('itpfac','sinv.descrip',' ');

		$edit = new DataDetails('Pedidos', $do);
		$edit->back_url = site_url('ventas/pfaclite/filteredgrid');
		$edit->set_rel_title('itpfac', 'Producto <#o#>');

		$edit->pre_process( 'insert', '_pre_insert' );
		$edit->pre_process( 'update', '_pre_update' );
		$edit->pre_process( 'delete', '_pre_delete' );
		$edit->post_process('insert', '_post_insert');
		$edit->post_process('update', '_post_update');
		$edit->post_process('delete', '_post_delete');

		$fenvia  =strtotime($edit->get_from_dataobjetct('fenvia'));
		$faplica =strtotime($edit->get_from_dataobjetct('faplica'));
		$hoy     =strtotime(date('Y-m-d'));

		$edit->fecha = new inputField('Fecha', 'fecha');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;

		$edit->mmargen = new inputField('mmargen', 'mmargen');

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 10;
		$edit->numero->mode = 'autohide';
		$edit->numero->maxlength = 8;
		$edit->numero->apply_rules = false; //necesario cuando el campo es clave y no se pide al usuario
		$edit->numero->when = array('show', 'modify');

		$edit->cliente = new hiddenField('Cliente', 'cod_cli');
		$edit->cliente->insertValue=$cliente;
		$edit->cliente->rule='required';

		$dbcliente=$this->db->escape($cliente);
		$nombre=$this->datasis->dameval("SELECT nombre FROM scli WHERE cliente=${dbcliente}");
		$edit->nombre = new freeField('Nombre','nombre',$nombre);

		$edit->observa = new inputField('Observaciones', 'observa');
		$edit->observa->size = 40;

		// Campos para el detalle
		$edit->codigoa = new inputField('C&oacute;digo <#o#>', 'codigoa_<#i#>');
		$edit->codigoa->size = 12;
		$edit->codigoa->db_name = 'codigoa';
		$edit->codigoa->rel_id  = 'itpfac';
		$edit->codigoa->rule    = 'callback_chcodigoa';
		$edit->codigoa->type    = 'inputhidden';

		$edit->pdesca = new inputField('Descripci&oacute;n <#o#>', 'pdesca_<#i#>');
		$edit->pdesca->size      = 32;
		$edit->pdesca->db_name   = 'desca';
		$edit->pdesca->maxlength = 50;
		$edit->pdesca->readonly  = true;
		$edit->pdesca->rel_id    = 'itpfac';
		$edit->pdesca->type      = 'inputhidden';
		$edit->pdesca->pointer   = true;

		$edit->itdesca = new hiddenField('descrip', 'itdesca_<#i#>');
		$edit->itdesca->insertValue = '';
		$edit->itdesca->db_name     = 'desca';
		$edit->itdesca->rel_id      = 'itpfac';

		$edit->pexisten = new inputField('Existencia <#o#>', 'pexisten_<#i#>');
		$edit->pexisten->size    = 10;
		$edit->pexisten->db_name = 'pexisten';
		$edit->pexisten->rel_id  = 'itpfac';
		$edit->pexisten->type    = 'inputhidden';
		$edit->pexisten->pointer = true;

		$edit->cana = new inputField('Cantidad <#o#>', 'cana_<#i#>');
		$edit->cana->db_name = 'cana';
		$edit->cana->css_class = 'inputnum';
		$edit->cana->rel_id = 'itpfac';
		$edit->cana->maxlength = 10;
		$edit->cana->size = 4;
		$edit->cana->rule = 'positive|callback_chcana[<#i#>]';
		$edit->cana->autocomplete = false;
		$edit->cana->onkeyup = 'total(\'<#i#>\')';
		$edit->cana->style ="height: 30px; font-size: 18px;";

		$edit->preca = new dropdownField('Precio <#o#>', 'preca_<#i#>');
		$edit->preca->db_name   = 'preca';
		$edit->preca->css_class = 'inputnum';
		$edit->preca->rel_id    = 'itpfac';
		$edit->preca->rule      = 'positive|callback_chpreca[<#i#>]';

		$edit->itiva = new hiddenField('', 'itiva_<#i#>');
		$edit->itiva->db_name = 'iva';
		$edit->itiva->rel_id  = 'itpfac';

		$edit->pmarca = new inputField('', 'pmarca_<#i#>');
		$edit->pmarca->db_name = 'pmarca';
		$edit->pmarca->rel_id  = 'itpfac';
		$edit->pmarca->pointer = true;
		// fin de campos para detalle

		$edit->totalg = new hiddenField('Monto Total', 'totalg');
		$edit->totalg->css_class = 'inputnum';
		$edit->totalg->readonly = true;
		$edit->totalg->size = 10;

		$edit->iva = new hiddenField('Impuesto', 'iva');
		$edit->iva->css_class = 'inputnum';
		$edit->iva->readonly = true;
		$edit->iva->size = 10;

		$edit->usuario = new autoUpdateField('usuario',$this->session->userdata('usuario'), $this->session->userdata('usuario'));
		$edit->estampa = new autoUpdateField('estampa',date('Ymd')  , date('Ymd'));
		$edit->hora    = new autoUpdateField('hora'   ,date('H:i:s'), date('H:i:s'));

		$control=$this->rapyd->uri->get_edited_id();

		if($edit->getstatus()=='show'){
			$action = "javascript:window.location='".site_url($this->url.'filterscli')."'";
			$edit->button('btn_add', 'Incluir nuevo pedido', $action, 'TR');
		}

		$iusr= $edit->get_from_dataobjetct('usuario');
		if(($fenvia < $hoy) && ($iusr == $this->secu->usuario())){
			$edit->buttons('modify', 'save', 'delete', 'undo', 'back','add_rel');
			$PFACRESERVA=$this->datasis->traevalor('PFACRESERVA','indica si un pedido descuenta de inventario los producto');
			if($PFACRESERVA=='S'){
				$accion="javascript:window.location='".site_url('ventas/pfaclite/reserva/'.$control)."/pfaclite'";
				$edit->button_status('btn_envia','Enviar Pedido',$accion,'TR','show');
			}
		}else{
			$edit->buttons('save', 'undo', 'back', 'add_rel');
		}

		//$edit->button_status('btn_load','Subir desde Excel',$accion,'TL','show');
		//$edit->button_status('btn_load','Subir desde Excel',$accion,'TL','create');
		//$edit->button_status('btn_load','Subir desde Excel',$accion,'TL','modify');

		$alma     = $this->secu->getalmacen();
		$dbalma   = $this->db->escape($alma);
		$tiposcli = $this->datasis->dameval("SELECT tipo FROM scli WHERE cliente=${dbcliente}");
		if($tiposcli<1) $tiposcli=1; elseif($tiposcli>4) $tiposcli=4;

		$sel=array('TRIM(a.codigo) AS codigo','a.descrip'
		,'a.precio1','a.precio2','a.precio3','a.precio4','a.exdes'
		,'a.marca','b.existen','a.iva','a.peso');

		$this->db->from('sinv AS a');
		$this->db->where('a.activo','S');
		$this->db->where('a.tipo'  ,'Articulo');
		$this->db->group_by('a.codigo');
		$this->db->order_by('a.marca , a.descrip , a.peso');
		$this->db->limit($this->limit);

		$act_meta=false;
		if($status=='create' || $status=='insert'){
			$this->db->join('itsinv AS b','a.codigo=b.codigo AND b.alma='.$dbalma);
			$this->db->where('b.existen > 0');
			if($this->db->table_exists('metas')){
				$pmargen=$this->datasis->dameval('SELECT pmargen FROM vend WHERE vendedor='.$dbvd);
				if(empty($pmargen)){
					$pmargen=0;
				}else{
					$pmargen=$pmargen/100;
				}
				$mmes=date('Ym');
				$uday=days_in_month(substr($mmes,4),substr($mmes,0,4));
				$this->db->join('metas  AS c','a.codigo=c.codigo AND c.fecha='.$mmes,'left');
				$this->db->join('sitems AS d','d.codigoa=c.codigo AND vendedor='.$dbvd.' AND d.fecha BETWEEN '.$mmes.'01 AND '.$mmes.$uday,'left');
				$sel[]="COALESCE(c.cantidad,0)*${pmargen} AS meta";
				$sel[]='COALESCE(SUM(d.cana*IF(tipoa=\'D\',-1,1)),0) AS vendido';
				$act_meta=true;
			}
		}elseif($status=='show'){
			$this->db->join('itsinv AS b','a.codigo=b.codigo');
		}else{
			$this->db->where('b.existen > 0');
			$this->db->join('itsinv AS b','a.codigo=b.codigo AND b.alma='.$dbalma);
		}
		$this->db->select($sel);
		$sinv=$this->db->get();

		$sinv=$sinv->result_array();
		$sinv_arr=array();
		foreach($sinv as $k=>$v){
			$sinv_arr[$v['codigo']]=array(
				 'descrip' => $v['descrip']
				,'precio1' => $v['precio1']*100/(100+$v['iva'])
				,'precio2' => $v['precio2']*100/(100+$v['iva'])
				,'precio3' => $v['precio3']*100/(100+$v['iva'])
				,'precio4' => $v['precio4']*100/(100+$v['iva'])
				,'marca'   => $v['marca']
				,'existen' => $v['existen']
				,'iva'     => $v['iva']
				,'peso'    => $v['peso']
				,'codigo'  => $v['codigo']
				,'exdes'   => $v['exdes']
			);
			if($act_meta){
				$sinv_arr[$v['codigo']]['meta']   = $v['meta'];
				$sinv_arr[$v['codigo']]['vendido']= $v['vendido'];
			}
		}

		$pedido=array();
		if($status=='create' || $status=='insert' || $status=='modify'  || $status=='update'){
			$vds=array();
			$mmSQL="SELECT TRIM(vendedor) AS vd FROM usuario WHERE almacen=${dbalma}";
			$qquery = $this->db->query($mmSQL);
			foreach($qquery->result() as $rrow){ $vds[]=$this->db->escape($rrow->vd); }
			$vds=implode(',',$vds);
			$mmSQL="SELECT TRIM(a.codigoa) AS codigo,SUM(a.cana) AS cana
				FROM itpfac AS a
				JOIN pfac AS b ON b.numero=a.numa
			WHERE b.status='P' AND b.vd IN (${vds})
			GROUP BY a.codigoa";
			$qquery = $this->db->query($mmSQL);
			foreach($qquery->result() as $rrow){
				$pedido[$rrow->codigo]=$rrow->cana;
			}
		}

		if($this->genesal){
			$edit->build();

			$conten['status']  = $status;
			$conten['pedido']  = $pedido;
			$conten['saldo']   = $saldo;
			$conten['act_meta']= $act_meta;
			$conten['tiposcli']= $tiposcli;
			$conten['form']    = & $edit;
			$conten['sinv']    = $sinv_arr;
			$data['content']   = $this->load->view('view_pfaclite', $conten,true);
			$data['head']      = style('mayor/estilo.css');
			//$data['title']     = heading('Pedidos No. '.$edit->numero->value);
			$data['title']     = heading('Pedidos ligeros');
			$this->load->view('view_ventanas_lite', $data);
		}else{
			$edit->on_save_redirect=false;
			$edit->build();

			if($edit->on_success()){
				$numero=$edit->_dataobject->get('numero');
				$rt = 'Pedido del cliente '.$edit->cliente->value.' - '.$edit->nombre->value.' ha sido guardado bajo el n&uacute;mero '.$numero.'.';
			}elseif($edit->on_error()){
				$rt = html_entity_decode(preg_replace('/<[^>]*>/', '', $edit->error_string));
			}else{
				$rt='';
			}
			return $rt;
		}
	}

	function chcana($cana,$i){
		$codigo   = $this->input->post('codigoa_'.$i);
		$dbcodigo = $this->db->escape($codigo);
		$alma     = $this->secu->getalmacen();
		$dbalma   = $this->db->escape($alma);

		$this->validation->set_message('chcana', 'No existe cantidad suficiente para el art&iacute;culo '.$codigo);
		//return false;
		//$udp=$this->rapyd->uri->is_set('update');
		//if($udp){
		//	$arrurl = $this->uri->segment_array();
		//	$id     = array_pop($arrurl);
		//	$numa   = $this->datasis->dameval("SELECT numero FROM pfac WHERE id=".$this->db->escape($id));
		//	$dbnuma = $this->db->escape($numa);
        //
		//	$mSQL="SELECT  COALESCE(b.existen,0)-COALESCE(a.exdes,0)+COALESCE(c.cana,0) AS cana
		//	FROM sinv AS a
		//	LEFT JOIN itsinv AS b ON a.codigo=b.codigo
		//	LEFT JOIN itpfac AS c ON a.codigo=c.codigoa AND c.numa=${dbnuma}
		//	WHERE a.codigo=${dbcodigo} AND b.alma=${dbalma}";
		//}else{
		//	$mSQL="SELECT  COALESCE(b.existen,0)-COALESCE(a.exdes,0) AS cana
		//	FROM sinv AS a
		//	LEFT JOIN itsinv AS b ON a.codigo=b.codigo
		//	WHERE a.codigo=${dbcodigo} AND b.alma=${dbalma}";
		//}
		$mSQL="SELECT  COALESCE(b.existen,0) AS cana
			FROM sinv AS a
			LEFT JOIN itsinv AS b ON a.codigo=b.codigo
			WHERE a.codigo=${dbcodigo} AND b.alma=${dbalma}";

		$hay=floatval($this->datasis->dameval($mSQL));
		if(empty($hay))  return false;
		if($cana > $hay) return false;
		return true;
	}

	function _exitescli($cliente){
		$dbscli= $this->db->escape($cliente);
		$mSQL  = "SELECT COUNT(*) AS cana FROM scli WHERE cliente=${dbscli}";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if( $row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function _pre_insert($do){
		$numero  = $do->get('numero');
		$usuario = $do->get('usuario');
		$estampa = $do->get('estampa');
		$hora    = $do->get('hora');
		$modoiva = $this->datasis->traevalor('MODOIVA');

		$tcana=0;
		for($i = 0;$i < $do->count_rel('itpfac');$i++){
			$itcana  = floatval($do->get_rel('itpfac', 'cana', $i));
			if($itcana>0){
				$tcana++;
			}
		}
		if($tcana<=0){
			$do->error_message_ar['pre_ins']='No puede guardar un pedido sin productos.';
			return false;
		}

		$cod_cli  = $do->get('cod_cli');
		$dbcod_cli= $this->db->escape($cod_cli);
		$scli     = $this->datasis->damerow("SELECT rifci,nombre,CONCAT(TRIM(dire11),' ',TRIM(dire12)) direc,CONCAT(TRIM(dire21),' ',TRIM(dire22)) dire1,zona,ciudad1 AS ciudad FROM scli WHERE cliente=${dbcod_cli}");
		if(empty($scli)){
			$do->error_message_ar['pre_ins']='Cliente inexistente.';
			return false;
		}

		if(empty($numero)){
			$numero = $this->datasis->fprox_numero('npfac');
			$do->set('numero', $numero);
			$ntransac = $this->datasis->fprox_numero('ntransa');
			$do->set('transac', $ntransac);
			$fecha = date('%Y%m%d');
		}else{
			$fecha=$do->get('fecha');
		}

		$do->set('rifci' ,$scli['rifci'] );
		$do->set('nombre',$scli['nombre']);
		$do->set('direc' ,$scli['direc'] );
		$do->set('dire1' ,$scli['dire1'] );
		$do->set('zona'  ,trim($scli['zona']));
		$do->set('ciudad',trim($scli['ciudad']));
		$do->set('status','P');

		$vd=$this->input->post('vd');
		if(empty($vd)) $vd=$this->secu->getvendedor();
		$do->set('vd',$vd);

		$transac = $do->get('transac');

		$iva = $totals = $tpeso =0;
		$borrar=array();
		for($i = 0;$i < $do->count_rel('itpfac');$i++){
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			if($itcana>0){
				$itpreca = $do->get_rel('itpfac','preca'  ,$i);
				$itcodigo= $do->get_rel('itpfac','codigoa',$i);
				$itiva   = $do->get_rel('itpfac','iva'    ,$i)/100;

				$ittota  = $itpreca*$itcana;

				if($modoiva=='N'){
					$mostrado= $itpreca;
				}else{
					$mostrado= round($itpreca*(1+$itiva),2);
				}

				$rowval = $this->datasis->damerow('SELECT descrip,pond, base1,precio4,peso FROM sinv WHERE codigo='.$this->db->escape($itcodigo));
				if(!empty($rowval)){
					$do->set_rel('itpfac', 'desca'    , $rowval['descrip'] , $i);
					$do->set_rel('itpfac', 'pvp'      , $rowval['base1'] , $i);
					$tpeso += floatval($rowval['peso'])*$itcana;
				}

				$do->set_rel('itpfac', 'tota'    , $ittota  , $i);
				$do->set_rel('itpfac', 'fecha'   , $fecha   , $i);
				$do->set_rel('itpfac', 'vendedor', $vd      , $i);
				$do->set_rel('itpfac', 'mostrado', $mostrado, $i);

				$do->set_rel('itpfac', 'transac', $transac , $i);
				$do->set_rel('itpfac', 'usuario', $usuario , $i);
				$do->set_rel('itpfac', 'estampa', $estampa , $i);
				$do->set_rel('itpfac', 'hora'   , $hora    , $i);

				$iva    += $ittota*$itiva;
				$totals += $ittota;
			}else{
				$borrar[$i]=$i;
			}
		}
		$borrar=array_reverse($borrar,true);
		foreach($borrar AS $value){
			array_splice($do->data_rel['itpfac'],$value,1);
		}
		$totalg = $totals + $iva;

		$do->set('totals' , round($totals , 2));
		$do->set('totalg' , round($totalg , 2));
		$do->set('iva'    , round($iva    , 2));
		$do->set('peso'   , round($tpeso  , 2));
		return true;
	}

	function _post_insert($do){
		$cana = $do->count_rel('itpfac');
		for($i = 0;$i < $cana;$i++){
			$itcodigo= $do->get_rel('itpfac', 'codigoa', $i);
			$itcana  = $do->get_rel('itpfac', 'cana', $i);
			$mSQL = "UPDATE sinv SET exdes=IF(exdes IS NULL,${itcana},exdes+${itcana}) WHERE codigo=".$this->db->escape($itcodigo);

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'pfaclite'); }
		}
		$codigo  = $do->get('numero');
		$dbcodigo= $this->db->escape($codigo);

		//Guarda el peso del pedido
		$sql='SELECT SUM(b.peso*a.cana) AS peso
		FROM itpfac AS a
		JOIN sinv   AS b ON a.codigoa=b.codigo
		WHERE a.numa='.$dbcodigo;
		$peso = $this->datasis->dameval($sql);
		$sql = "UPDATE pfac SET peso=${peso} WHERE numero=${dbcodigo}";
		$ban = $this->db->simple_query($sql);
		//Fin del peso del pedido

		logusu('pfac', "Pedido ${codigo} CREADO");
	}

	function _pre_update($do){
		$factura= trim($do->get('factura'));
		if(!empty($factura)){
			$do->error_message_ar['pre_upd']='El pedido ya fue facturado con el n&uacute;mero '.$factura.' no puede modificarlo';
			return false;
		}
		if($do->get('status') != 'P'){
			$do->error_message_ar['pre_upd']='Pedido ya procesado, no puede ser modificado';
			return false;
		}

		$numa   = $do->get('numero');
		$dbnuma = $this->db->escape($numa);

		$sql="UPDATE itpfac AS c JOIN sinv   AS d ON d.codigo=c.codigoa
		SET d.exdes=IF(d.exdes>c.cana,d.exdes-c.cana,0)
		WHERE c.numa = ${dbnuma}";
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'pfaclite'); $error++;}

		return $this->_pre_insert($do);
	}

	function _post_update($do){
		$cana = $do->count_rel('itpfac');
		for($i = 0;$i < $cana;$i++){
			$itcodigo= $do->get_rel('itpfac', 'codigoa', $i);
			$itcana  = $do->get_rel('itpfac', 'cana', $i);

			$mSQL = "UPDATE sinv SET exdes=exdes+${itcana} WHERE codigo=".$this->db->escape($itcodigo);
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'pfaclite'); }
		}
		$codigo  = $do->get('numero');
		$dbcodigo= $this->db->escape($codigo);

		//Guarda el peso del pedido
		$sql='SELECT SUM(b.peso*a.cana) AS peso
		FROM itpfac AS a
		JOIN sinv   AS b ON a.codigoa=b.codigo
		WHERE a.numa='.$dbcodigo;
		$peso = $this->datasis->dameval($sql);
		$sql = "UPDATE pfac SET peso=${peso} WHERE numero=${dbcodigo}";
		$ban = $this->db->simple_query($sql);
		//Fin del peso del pedido


		logusu('pfac', "Pedido $codigo MODIFICADO");
	}

	function _pre_delete($do){
		$status = $do->get('status');
		$codigo = $do->get('numero');
		if($status!='C'){
			$mSQL='UPDATE sinv JOIN itpfac ON sinv.codigo=itpfac.codigoa SET sinv.exdes=IF(sinv.exdes>itpfac.cana,sinv.exdes-itpfac.cana,0) WHERE itpfac.numa='.$this->db->escape($codigo);
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'pfaclite'); }
		}
		return true;
	}

	function _post_delete($do){
		$codigo = $do->get('numero');
		logusu('pfaclite', "Pedido $codigo ELIMINADO");
	}

	function load(){
		$this->datasis->modulo_id(143,1);
		$this->load->library('path');
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/archivos');
		$this->upload_path =$path->getPath().'/';

		$this->rapyd->load('dataform');
		$form = new DataForm('ventas/pfaclite/load/insert');
		$form->title('Cargar Archivo de Productos (xls)');

		$form->archivo = new uploadField('Archivo','archivo');
		$form->archivo->upload_path   = '';
		$form->archivo->allowed_types = 'xls';
		$form->archivo->delete_file   = false;
		$form->archivo->upload_root   = '/tmp';
		$form->archivo->rule          = 'required';
		$form->archivo->append("Solo archivos en formato xls (Excel 97-2003)");

		$accion="javascript:window.location='".site_url('ventas/pfaclite/filteredgrid')."'";
		$form->button('btn_pfl','Regresar',$accion,'TR');

		$form->submit('btnsubmit','Enviar');
		$form->build_form();

		$rti='';
		if ($form->on_success()){
			$arch= '/tmp/'.$form->archivo->upload_data['file_name'];
			$rt=$this->nread($arch);
			$rti="<p>$rt</p>";
		}

		$data['content'] = $rti.$form->output;
		$data['title']   = heading('Cargar Pedido desde Excel');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_lite', $data);
	}

	function nread($arch){
		$this->load->library('Spreadsheet_Excel_Reader');
		$rt='';
		$this->spreadsheet_excel_reader->setOutputEncoding('CP1251');
		$this->spreadsheet_excel_reader->read($arch);
		array_shift($this->spreadsheet_excel_reader->sheets);
		$hojas=count($this->spreadsheet_excel_reader->sheets);
		for($i=0;$i<$hojas;$i++){
			$o=0;
			$genefec ='';
			$msjfalla=array();
			foreach($this->spreadsheet_excel_reader->sheets[$i]['cells'] as $id=>$row){

				if($id==1){
					$genefec=(isset($row[8]))?$row[8]:'';
					$vd =(preg_match('/^.*\((?P<vd>\w+)\).*$/',$row[1], $matches)>0)? $matches['vd'] : $this->secu->getvendedor();
				}elseif($id==2){
					$_POST['cod_cli']=(isset($row[1]))?$row[1]:'';
				}elseif($id>3){
					if(empty($_POST['cod_cli'])) continue;
					$codigo  =trim($row[8]);
					if(empty($codigo)) continue;
					if(empty($row[9]) || $row[9]<1) continue;
					$dbcodigo=$this->db->escape($codigo);

					$mSQL="SELECT  COALESCE(b.existen,0)-COALESCE(a.exdes,0) AS cana
						FROM sinv AS a
						LEFT JOIN itsinv AS b ON a.codigo=b.codigo
						WHERE a.codigo=$dbcodigo";
					$hay=$this->datasis->dameval($mSQL);

					if($hay<=0){
						$msjfalla[]="Producto $codigo sin existencia, no se registro, cliente $_POST[cod_cli].";
						continue;
					}

					$iva = $this->datasis->dameval('SELECT iva FROM sinv WHERE codigo='.$dbcodigo);
					$_POST['codigoa_'.$o] = $codigo;

					if($row[9]>$hay){
						$_POST['cana_'.$o] = $hay;
						$msjfalla[]="Producto $codigo entro en falla, se pidio $row[9] y se registro $hay, cliente $_POST[cod_cli].";
					}else{
						$_POST['cana_'.$o] = $row[9];
					}
					$_POST['preca_'.$o]   = $row[5];
					$_POST['iva_'.$o]     = $iva;
					$o++;
				}else{
					continue;
				}
			}

			$_POST['vd'] = $vd;
			$_POST['observa']='Fuera de linea, Hoja del '.$genefec;
			if(!empty($_POST['cod_cli']) && $o>0){
				$this->genesal=false;
				$rrt=$this->dataedit($_POST['cod_cli'],'','');
				$rt.=$rrt.'<br />';
			}
			if(count($msjfalla)>0){
				$rt.=implode(br(),$msjfalla).br();
			}
			$_POST=array();
		}

		if(file_exists($arch)){
			unlink($arch);
		}
		return $rt;
	}

	function reserv($id){
		$error='';
		$PFACRESERVA=$this->datasis->traevalor('PFACRESERVA','indica si un pedido descuenta de inventario los producto');
		if($PFACRESERVA=='S'){
			$usr=$this->session->userdata('usuario');
			$vd['vendedor']=$this->datasis->dameval("SELECT vendedor FROM usuario WHERE us_codigo='$usr'");
			$vd['almacen'] =$this->datasis->dameval("SELECT almacen FROM usuario WHERE us_codigo='$usr'");

			$this->rapyd->load('dataobject');
			$do = new DataObject('pfac');
			$do->rel_one_to_many('itpfac', 'itpfac', array('numero' => 'numa'));
			$do->load($id);

			$sinv=$this->db->query("SELECT * FROM sinv");
			$sinv=$sinv->result_array();
			$sinv2=array();
			$sinviva=array();
			foreach($sinv as $k=>$v){
				$sinv2[$v['codigo']]=$v;
			}
			$sinv=$sinv2;
			unset($sinv2);
			$iva = $totals = 0;
			for($i=0;$i < $do->count_rel('itpfac');$i++){
				$codigoa  = $do->get_rel('itpfac','codigoa'  ,$i);
				$cana     = $do->get_rel('itpfac','cana'     ,$i);

				$existen  =$this->datasis->dameval("SELECT existen FROM itsinv WHERE alma='".$vd['almacen']."' AND codigo='$codigoa'");
				if($cana>$existen){
					$error.="ERROR. La cantidad solicitada(".nformat($cana).") es mayor a la existente (".nformat($existen).") para ($codigoa).</br>";
				}
				$codigoae = $this->db->escape($codigoa);
				$sinv   =$this->datasis->damerow("SELECT precio1,iva FROM sinv WHERE codigo=$codigoae");
				$precio1=$sinv['precio1'];
				$itiva  =$sinv['iva'];
				$do->set_rel('itpfac','preca',$precio1,$i);
				$ittota  = $precio1 * $cana;
				$do->set_rel('itpfac', 'tota'    , $ittota, $i);

				$iva    += $ittota * ($itiva / 100);
				$totals += $ittota;
				$do->set_rel('itpfac', 'mostrado', $iva + $ittota, $i);
			}
			$totalg = $totals + $iva;

			$do->set('totals' , round($totals , 2));
			$do->set('totalg' , round($totalg , 2));
			$do->set('iva'    , round($iva    , 2));

			if(empty($error)){
				for($i=0;$i < $do->count_rel('itpfac');$i++){
					$codigoa  = $do->get_rel('itpfac','codigoa'  ,$i);
					$cana     = $do->get_rel('itpfac','cana'     ,$i);
					$this->datasis->sinvcarga( $codigoa, $vd['almacen'], -1*$cana);
					$this->datasis->sinvcarga( $codigoa, 'PEDI', $cana);
				}
			}
			$fenvia=date('Ymd');
			$do->set('reserva','S');
			$do->set('fenvia' ,$fenvia);
			if(empty($error))
				$do->save();
			else
			return $error;
		}
	}

	function reserva($id,$dir='pfac'){
		$error='';
		$error.=$this->reserv($id);
		if(empty($error)){
			logusu('pfaclite',"Reservo pedido $id");
			redirect("ventas/$dir/dataedit/show/$id");
		}else{
			$error="<div class='alert'>$error</div>";
			logusu('pfaclite',"Reservo pedido $id. con ERROR:$error ");
			$data['content'] = $error.anchor("ventas/pfaclite/dataedit/show/$id",'Regresar');
			$data['title']   = " Pedidos ";
			$data["head"]    = $this->rapyd->get_head();
			$this->load->view('view_ventanas', $data);
		}
	}

	function pfl(){
		if(!extension_loaded('perl')) show_error('Se necesita la extenci&oacute;n perl, comuniquese con soporte t&eacute;cnico');
		$this->datasis->modulo_id(143,1);

		$vd   = $this->secu->getvendedor();
		$vnom = $this->datasis->dameval('SELECT nombre FROM vend WHERE vendedor='.$this->db->escape($vd));
		$dbvd = $this->db->escape($vd);

		$db   = $this->db->database;
		$host = $this->db->hostname;
		$usr  = $this->db->username;
		$pwd  = $this->db->password;
		$fname= 'pfl_'.date('d-m-Y').'.xls';
		$comp = $this->datasis->traevalor('TITULO1');
		$rif  = $this->datasis->traevalor('RIF');
		$key  = $this->datasis->traevalor('RIF');
		$prot = ''; //Colocar # para desactivar

		header("Content-type: application/x-msexcel; name=\"${fname}\"");
		header("Content-Disposition: inline; filename=\"${fname}\"");

		$pl=<<<PERL_END
use strict;
use DBI();
use Spreadsheet::WriteExcel;

my \$workbook   = Spreadsheet::WriteExcel->new(\\*STDOUT);
\$workbook->compatibility_mode();
\$workbook->set_properties(
	title    => 'Hoja de pedidos fuera de linea',
	company  => '$comp',
	comments => 'firma',
);

my \$worksheet0 = \$workbook->add_worksheet("clientes");
#\$worksheet0->hide_gridlines(2);
$prot\$worksheet0->protect('clientes');
my \$dbh = DBI->connect("DBI:mysql:database=$db;host=$host","$usr", "$pwd",{'RaiseError' => 1});
my \$mSQL="SELECT TRIM(cliente) AS cliente, TRIM(nombre) AS nombre ,TRIM(rifci) AS rifci,tipo
FROM scli
WHERE vendedor=$dbvd
ORDER BY cliente  LIMIT 300";

my \$mfil= 2;
my \$scli;
my \$count_scli=\$mfil;
my \$sth = \$dbh->prepare(\$mSQL);
\$sth->execute();

my \$lock = \$workbook->add_format();
\$lock->set_locked(1);
\$lock->set_hidden();

my \$unlock = \$workbook->add_format();
\$unlock->set_locked(0);

my \$ftit = \$workbook->add_format();
\$ftit->set_align('merge');
\$ftit->set_valign('vcenter');
\$ftit->set_fg_color(23);
\$ftit->set_color(1);
\$ftit->set_bold();
\$ftit->set_locked(1);

\$worksheet0->write_string(0, 0 ,'$comp $rif');
\$worksheet0->set_column(0,0, 8 );
\$worksheet0->set_column(1,1, 46);
\$worksheet0->set_column(2,2, 12);
\$worksheet0->set_column(3,3, 5 );

\$worksheet0->write_string( \$mfil, 0,'Codigo',\$ftit);
\$worksheet0->write_string( \$mfil, 1,'Nombre',\$ftit);
\$worksheet0->write_string( \$mfil, 2,'Rif'   ,\$ftit);
\$worksheet0->write_string( \$mfil, 3,'Tipo'  ,\$ftit);
\$mfil++;

while(my \$row = \$sth->fetchrow_hashref()){
	if(\$count_scli<3){ \$scli=\$row->{'cliente'}; }
	\$count_scli++;
	\$worksheet0->write_string( \$mfil, 0,\$row->{'cliente'},\$lock);
	\$worksheet0->write_string( \$mfil, 1,\$row->{'nombre'} ,\$lock);
	\$worksheet0->write_string( \$mfil, 2,\$row->{'rifci'}  ,\$lock);
	\$worksheet0->write( \$mfil, 3,\$row->{'tipo'},\$lock);
	\$mfil++;
}
\$count_scli++;
\$workbook->define_name('DpScli', '=clientes!\$A\$4:\$A$'.\$count_scli);

my \$ffot = \$workbook->add_format();
\$ffot->set_fg_color(23);
\$ffot->set_color(1);
\$ffot->set_bold();
\$ffot->set_locked(1);

my \$fbod0 = \$workbook->add_format();
\$fbod0->set_locked(1);
\$fbod0->set_border(1);
\$fbod0->set_hidden();

my \$fbod1 = \$workbook->add_format();
\$fbod1->set_locked(1);
\$fbod1->set_border(1);

my \$fbod2 = \$workbook->add_format();
\$fbod2->set_locked(1);
\$fbod2->set_border(1);

\$mfil=0;

\$fbod0->set_fg_color(26);
\$fbod1->set_fg_color(41);
\$fbod2->set_fg_color(42);

my \$fcod = \$workbook->add_format();
\$fcod->set_locked(1);
\$fcod->set_border(1);
\$fcod->set_bold();

my \$fedi = \$workbook->add_format(locked => 0);
\$fedi->set_fg_color(31);
\$fedi->set_border(1);
#\$fedi->set_locked(0);

my \$fgru = \$workbook->add_format();
\$fgru->set_fg_color(32);
\$fgru->set_locked(1);
\$fgru->set_color(1);
\$fgru->set_bold();

my \$fpre = \$workbook->add_format();
\$fpre->set_locked();
\$fpre->set_bold();

my @months = qw(Ene Feb Mar Abr May Jun Jul Ago Sep Oct Nov Dic);
my @weekDays = qw(Domingo Lunes Martes Miercoles Jueves Viernes Sabado Domingo);
my \$second;
my \$minute;
my \$hour;
my \$dayOfMonth;
my \$month;
my \$yearOffset;
my \$dayOfWeek;
my \$dayOfYear;
my \$daylightSavings;
(\$second, \$minute, \$hour, \$dayOfMonth, \$month, \$yearOffset, \$dayOfWeek, \$dayOfYear, \$daylightSavings) = localtime();
my \$year = 1900 + \$yearOffset;
my \$theTime = "\$weekDays[\$dayOfWeek] \$dayOfMonth/\$month/\$year \$hour:\$minute:\$second";
my \$mmfil= \$mfil+1;
my \$grup = '';

my \$formula = "=IF(ISNA(VLOOKUP(A2,clientes!A4:D\$count_scli,200,0)),1,VLOOKUP(A2,clientes!A4:D\$count_scli,200,0))";
my \$fformul = "=IF(ISNA(VLOOKUP(A2,clientes!A4:D\$count_scli,200,0)),\"Seleccione el cliente en la celda A2\",VLOOKUP(A2,clientes!A4:D\$count_scli,200,0))";
my \$count;
my \$sinv_cant;
my \$i=0;
my \$worksheet;

for (\$count = 1; \$count <= 10; \$count++) {
	\$worksheet = \$workbook->add_worksheet(\$count);
	\$worksheet->hide_gridlines(2);

	my \$vlookup = \$worksheet->store_formula(\$formula);
	@\$vlookup = map {s/_ref2d/_ref2dV/;\$_} @\$vlookup;

	my \$vvlookup = \$worksheet->store_formula(\$fformul);
	@\$vvlookup = map {s/_ref2d/_ref2dV/;\$_} @\$vvlookup;

	$prot\$worksheet->protect("00\$count");
	\$worksheet->set_zoom(75);
	\$worksheet->set_column(0,0, 30);
	\$worksheet->set_column(1,2, 5 );
	\$worksheet->set_column(5,5, 15);
	\$worksheet->set_column(6,6, 8 );
	\$worksheet->set_column(7,7, 15);

	\$worksheet->write_string(0, 0,'Representante: ($vd) $vnom',\$lock);
	\$worksheet->write_string(0, 7,\$theTime ,\$lock);
	\$worksheet->write_blank(1, 0,\$unlock);

	\$worksheet->repeat_formula('I2',\$vlookup,\$lock,('200','4') x 2);
	\$worksheet->repeat_formula('B2',\$vvlookup,\$lock,('200','2') x 2);

	\$mfil=2;
	\$worksheet->write_string(\$mfil, 0,'Producto' ,\$ftit);
	\$worksheet->write_string(\$mfil, 1,'Presenta' ,\$ftit);
	\$worksheet->write_blank(\$mfil , 2,\$ftit);
	\$worksheet->write_string(\$mfil, 3,'Peso'     ,\$ftit);
	\$worksheet->write_string(\$mfil, 4,'Precio'   ,\$ftit);
	\$worksheet->write_string(\$mfil, 5,'Total Bs.',\$ftit);
	\$worksheet->write_string(\$mfil, 6,'Exis.'    ,\$ftit);
	\$worksheet->write_string(\$mfil, 7,'Cod. SAP' ,\$ftit);
	\$worksheet->write_string(\$mfil, 8,'Pedido'   ,\$ftit);
	\$worksheet->freeze_panes(\$mfil+1,0,\$mfil+1,0);
	\$worksheet->set_row(\$mfil, 22);
}
\$mfil++;
\$mmfil= \$mfil+1;
\$grup = '';

\$mSQL = "SELECT a.peso, a.codigo, a.descrip, a.marca AS grupo, a.marca AS nom_grup, a.unidad, IF(a.existen<a.exdes,0,a.existen-a.exdes) AS existen,
	round(a.precio1*100/(100+a.iva),2) AS base1,
	round(a.precio2*100/(100+a.iva),2) AS base2,
	round(a.precio3*100/(100+a.iva),2) AS base3,
	round(a.precio4*100/(100+a.iva),2) AS base4
FROM sinv AS a
JOIN grup AS b ON a.grupo=b.grupo
WHERE a.activo='S' AND a.tipo='Articulo'
ORDER BY a.marca, a.descrip LIMIT 500";

\$sth  = \$dbh->prepare(\$mSQL);
\$sth->execute();


while(my \$row = \$sth->fetchrow_hashref()){
	\$mmfil=\$mfil+1;
	if(\$grup ne \$row->{'grupo'}){
		foreach \$worksheet (\$workbook->sheets()) {
			if(\$worksheet->get_name() eq 'clientes'){ next; }
			\$worksheet->write_string(\$mfil, 0,\$row->{'nom_grup'},\$fgru);
			\$worksheet->write_blank( \$mfil, 1,\$fgru);
			\$worksheet->write_blank( \$mfil, 2,\$fgru);
			\$worksheet->write_blank( \$mfil, 3,\$fgru);
			\$worksheet->write_blank( \$mfil, 4,\$fgru);
			\$worksheet->write_blank( \$mfil, 5,\$fgru);
			\$worksheet->write_blank( \$mfil, 6,\$fgru);
			\$worksheet->write_blank( \$mfil, 7,\$fgru);
			\$worksheet->write_blank( \$mfil, 8,\$fgru);
		}
		\$mfil++;
		\$mmfil=\$mfil+1;
		\$grup=\$row->{'grupo'};
	}

	foreach \$worksheet (\$workbook->sheets()){
		if(\$worksheet->get_name() eq 'clientes'){ next; }
		\$worksheet->write_string( \$mfil, 0,\$row->{'descrip'} ,\$fbod0);
		\$worksheet->write( \$mfil, 1,\$row->{'peso'}    ,\$fbod0);
		\$worksheet->write_string( \$mfil, 2,\$row->{'unidad'}  ,\$fbod0);
		\$worksheet->write_formula(\$mfil, 3,"=I\$mmfil*B\$mmfil",\$fbod0,90);
		\$worksheet->write_formula(\$mfil, 4,'=IF(\$I\$2=2,'.\$row->{'base2'}.',IF(\$I\$2=3,'.\$row->{'base3'}.',IF(\$I\$2=4,'."\$row->{'base4'},\$row->{'base1'})))",\$fbod0);
		\$worksheet->write_formula(\$mfil, 5,"=I\$mmfil*E\$mmfil",\$fbod0);
		\$worksheet->write( \$mfil, 6,\$row->{'existen'}   ,\$fbod0 );
		\$worksheet->write_string( \$mfil, 7,\$row->{'codigo'}   ,\$fcod );
		\$worksheet->write_number( \$mfil, 8,0                   ,\$fedi );
		\$worksheet->data_validation(\$mfil, 8, {
			validate => 'integer',
			criteria => '>=',
			value    => 0,
		});
	}
	\$mfil++;
}
\$sinv_cant=\$mfil;
\$sth->finish();

foreach \$worksheet (\$workbook->sheets()) {
	if(\$worksheet->get_name() eq 'clientes'){ next; }
	\$worksheet->write_string( \$mfil, 0,'Totales...'     ,\$ffot);
	\$worksheet->write_blank(  \$mfil, 1,\$ffot);
	\$worksheet->write_blank(  \$mfil, 2,\$ffot);
	\$worksheet->write_formula(\$mfil, 3,"=SUM(D4:D\$mfil)",\$ffot);
	\$worksheet->write_blank(  \$mfil, 4,\$ffot);
	\$worksheet->write_formula(\$mfil, 5,"=SUM(F4:F\$mfil)",\$ffot);
	\$worksheet->write_blank(  \$mfil, 6 ,\$ffot);
	\$worksheet->write_blank(  \$mfil, 7 ,\$ffot);
	\$worksheet->write_formula(\$mfil, 8,"=SUM(I4:I\$mfil)",\$ffot);

	\$worksheet->data_validation(1, 0, {
		validate      => 'list',
		dropdown      => 1,
		input_title   => 'Cliente',
		input_message => 'Seleccione el cliente al cual se le va a realizar el pedido',
		value         => '=DpScli',
	});
}

#\$worksheet->autofilter(2, 0,\$mfil ,7);
\$dbh->disconnect();
\$workbook->sheets(1)->activate();
\$workbook->close();

__END__

PERL_END;

		//file_put_contents('excel.pl', $pl);
		$perl = new Perl();
		$pobj = $perl->eval($pl);
	}

	function instalar(){
		$query="ALTER TABLE `pfac`  ADD COLUMN `id` INT NULL AUTO_INCREMENT AFTER `fenvia`,
		ADD PRIMARY KEY (`id`),  ADD UNIQUE INDEX `numero` (`numero`)";
		$this->db->simple_query($query);
	}
}
