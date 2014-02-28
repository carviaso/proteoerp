<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class ccli extends Controller {
	var $titp='Cobro a clientes';
	var $tits='Cobro a cliente';
	var $url ='finanzas/ccli/';

	function ccli(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('524',1);
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp);
		$sel=array('TRIM(b.cliente) AS cod_cli','b.nombre','SUM(a.monto-a.abonos) AS saldo','b.rifci');
		$filter->db->select($sel);

		$filter->db->from('scli AS b');
		$filter->db->join('smov AS a',"a.cod_cli=b.cliente AND a.tipo_doc IN ('FC','ND','GI') AND a.monto > a.abonos",'left');
		$filter->db->groupby('b.cliente');

		$filter->cod_cli = new inputField('Cliente','cod_cli');
		$filter->cod_cli->rule      = 'max_length[5]';
		$filter->cod_cli->size      = 7;
		$filter->cod_cli->db_name   = 'a.cod_cli';
		$filter->cod_cli->maxlength = 5;

		$filter->nombre = new inputField('Nombre','nombre');
		$filter->nombre->rule      = 'max_length[8]';
		$filter->nombre->size      = 10;
		$filter->nombre->db_name   = 'a.nombre';
		$filter->nombre->maxlength = 8;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/<raencode><#cod_cli#></raencode>/create','<#cod_cli#>');

		$grid = new DataGrid('Seleccione el cliente');
		$grid->order_by('fecha','desc');
		$grid->per_page = 40;

		$grid->column_orderby('Cliente'    ,$uri,'cod_cli','align="left"');
		$grid->column_orderby('Rif/CI'     ,'rifci','rifci','align="left"');
		$grid->column_orderby('Nombre'     ,'nombre','nombre','align="left"');
		$grid->column_orderby('Saldo'      ,'<nformat><sinulo><#saldo#>|0</sinulo></nformat>','saldo','align="right"');

		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = heading($this->titp);
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($cliente){
		if(!$this->_exitescli($cliente)) redirect($this->url.'filteredgrid');
		$dbcliente=$this->db->escape($cliente);
		$scli_nombre=$this->datasis->dameval("SELECT nombre FROM scli WHERE cliente=$dbcliente");
		$scli_rif   =$this->datasis->dameval("SELECT rifci FROM scli WHERE cliente=$dbcliente");

		$cajero=$this->secu->getcajero();
		if(empty($cajero)) show_error('El usuario debe tener registrado un cajero para poder usar este modulo');

		$this->rapyd->load('dataobject','datadetails');
		$this->rapyd->uri->keep_persistence();

		$do = new DataObject('smov');
		$do->rel_one_to_many('itccli', 'itccli', array(
			'tipo_doc'=>'tipoccli',
			'numero'  =>'numccli',
			'cod_cli' =>'cod_cli',
			'fecha'   =>'fecha')
		);
		$do->rel_one_to_many('sfpa'  , 'sfpa'  , array(
			'transac' =>'transac',
			'numero'  =>'numero',
			'tipo_doc'=>'tipo_doc',
			'fecha'   =>'fecha')
		);
		$do->order_by('itccli','itccli.fecha');

		$edit = new DataDetails('Cobro a cliente', $do);
		$edit->back_url = site_url('finanzas/ccli/filteredgrid');
		$edit->set_rel_title('itccli', 'Producto <#o#>');
		$edit->set_rel_title('itccli', 'Forma de pago <#o#>');

		$edit->pre_process('insert' , '_pre_insert');
		$edit->pre_process('update' , '_pre_update');
		$edit->pre_process('delete' , '_pre_delete');
		$edit->post_process('insert', '_post_insert');
		$edit->post_process('update', '_post_update');
		$edit->post_process('delete', '_post_delete');

		$edit->cod_cli = new hiddenField('Cliente','cod_cli');
		$edit->cod_cli->rule ='max_length[5]';
		$edit->cod_cli->size =7;
		$edit->cod_cli->insertValue=$cliente;
		$edit->cod_cli->maxlength =5;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='max_length[40]';
		$edit->nombre->size =42;
		$edit->nombre->maxlength =40;

		$edit->tipo_doc = new  dropdownField('Tipo doc.', 'tipo_doc');
		$edit->tipo_doc->option('AB','Abono');
		$edit->tipo_doc->option('NC','Nota de credito');
		$edit->tipo_doc->option('AN','Anticipo');
		$edit->tipo_doc->style='width:140px;';
		$edit->tipo_doc->rule ='enum[AB,NC,AN]|required';

		$edit->codigo = new  dropdownField('Motivo', 'codigo');
		$edit->codigo->option('','Ninguno');
		$edit->codigo->options('SELECT TRIM(codigo) AS cod, nombre FROM botr WHERE tipo=\'C\' ORDER BY nombre');
		$edit->codigo->style='width:200px;';
		$edit->codigo->rule ='';

		$edit->numero = new inputField('N&uacute;mero','numero');
		$edit->numero->rule='max_length[8]';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->fecdoc = new dateonlyField('Fecha','fecdoc');
		$edit->fecdoc->size =10;
		$edit->fecdoc->maxlength =8;
		$edit->fecdoc->insertValue=date('Y-m-d');
		$edit->fecdoc->rule ='chfecha|required';

		$edit->monto = new inputField('Total','monto');
		$edit->monto->rule='max_length[17]|numeric';
		$edit->monto->css_class='inputnum';
		$edit->monto->size =19;
		$edit->monto->maxlength =17;
		$edit->monto->type='inputhidden';

		$edit->observa1 = new  textareaField('Concepto:','observa1');
		$edit->observa1->cols = 70;
		$edit->observa1->rows = 2;
		$edit->observa1->style='width:100%;';

		$edit->observa2 = new  textareaField('','observa2');
		$edit->observa2->cols = 70;
		$edit->observa2->rows = 2;
		$edit->observa2->style='width:100%;';
		$edit->observa2->when=array('show');

		$edit->usuario = new autoUpdateField('usuario' ,$this->secu->usuario(),$this->secu->usuario());
		$edit->estampa = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora    = new autoUpdateField('hora'    ,date('H:i:s'), date('H:i:s'));
		$edit->fecha   = new autoUpdateField('fecha'   ,date('Ymd'), date('Ymd'));

		//************************************************
		//inicio detalle itccli
		//************************************************
		$i=0;
		$edit->detail_expand_except('itccli');
		$sel=array('a.tipo_doc','a.numero','a.fecha','a.monto','a.abonos','a.monto - a.abonos AS saldo');
		$this->db->select($sel);
		$this->db->from('smov AS a');
		$this->db->where('a.cod_cli',$cliente);
		$transac=$edit->get_from_dataobjetct('transac');
		if($transac!==false){
			$tipo_doc =$edit->get_from_dataobjetct('tipo_doc');
			$dbtransac=$this->db->escape($transac);
			$this->db->join('itccli AS b','a.tipo_doc = b.tipoccli AND a.numero=b.numccli AND a.transac='.$dbtransac);
			$this->db->where('a.tipo_doc',$tipo_doc);
		}else{
			$this->db->where('a.monto > a.abonos');
			$this->db->where_in('a.tipo_doc',array('FC','ND','GI'));
		}
		$this->db->order_by('a.fecha');
		$query = $this->db->get();
		//echo $this->db->last_query();
		foreach ($query->result() as $row){
			$obj='cod_cli_'.$i;
			$edit->$obj = new autoUpdateField('cod_cli',$cliente,$cliente);
			$edit->$obj->rel_id  = 'itccli';
			$edit->$obj->ind     = $i;

			$obj='tipo_doc_'.$i;
			$edit->$obj = new inputField('Tipo_doc',$obj);
			$edit->$obj->db_name='tipo_doc';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='max_length[2]';
			$edit->$obj->insertValue=$row->tipo_doc;
			$edit->$obj->size =4;
			$edit->$obj->maxlength =2;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='numero_'.$i;
			$edit->$obj = new inputField('Numero',$obj);
			$edit->$obj->db_name='numero';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='max_length[8]';
			$edit->$obj->insertValue=$row->numero;
			$edit->$obj->size =10;
			$edit->$obj->maxlength =8;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='fecha_'.$i;
			$edit->$obj = new dateonlyField('Fecha',$obj);
			$edit->$obj->db_name='fecha';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='chfecha';
			$edit->$obj->insertValue=$row->fecha;
			$edit->$obj->size =10;
			$edit->$obj->maxlength =8;
			$edit->$obj->ind       = $i;
			$edit->$obj->type='inputhidden';

			$obj='monto_'.$i;
			$edit->$obj = new inputField('Monto',$obj);
			$edit->$obj->db_name='monto';
			$edit->$obj->rel_id = 'itccli';
			$edit->$obj->rule='max_length[18]|numeric';
			$edit->$obj->css_class='inputnum';
			$edit->$obj->size =20;
			$edit->$obj->insertValue=$row->monto;
			$edit->$obj->maxlength =18;
			$edit->$obj->ind       = $i;
			$edit->$obj->showformat='decimal';
			$edit->$obj->type='inputhidden';

			$obj='saldo_'.$i;
			$edit->$obj = new freeField($obj,$obj,nformat($row->saldo));
			$edit->$obj->ind = $i;

	        $obj='abono_'.$i;
			$edit->$obj = new inputField('Abono',$obj);
			$edit->$obj->db_name      = 'abono';
			$edit->$obj->rel_id       = 'itccli';
			$edit->$obj->rule         = "max_length[18]|numeric|positive|callback_chabono[$i]";
			$edit->$obj->css_class    = 'inputnum';
			$edit->$obj->showformat   = 'decimal';
			$edit->$obj->autocomplete = false;
			$edit->$obj->disable_paste= true;
			$edit->$obj->size         = 15;
			$edit->$obj->maxlength    = 18;
			$edit->$obj->ind          = $i;
			$edit->$obj->onfocus      = 'itsaldo(this,'.round($row->saldo,2).');';

	        $obj='ppago_'.$i;
			$edit->$obj = new inputField('Pronto Pago',$obj);
			$edit->$obj->db_name      = 'ppago';
			$edit->$obj->rel_id       = 'itccli';
			$edit->$obj->rule         = "max_length[18]|numeric|positive|callback_chppago[$i]";
			$edit->$obj->css_class    = 'inputnum';
			$edit->$obj->showformat   = 'decimal';
			$edit->$obj->autocomplete = false;
			$edit->$obj->disable_paste= true;
			$edit->$obj->size         = 15;
			$edit->$obj->maxlength    = 18;
			$edit->$obj->ind          = $i;
			$edit->$obj->onchange     = "itppago(this,'$i');";

			$i++;
		}
		//************************************************
		//fin de campos para detalle,inicio detalle2 sfpa
		//************************************************
		$edit->tipo = new  dropdownField('Tipo <#o#>', 'tipo_<#i#>');
		$edit->tipo->option('','Ninguno');
		$edit->tipo->options('SELECT tipo, nombre FROM tarjeta WHERE activo=\'S\' ORDER BY nombre');
		$edit->tipo->db_name  = 'tipo';
		$edit->tipo->rel_id   = 'sfpa';
		$edit->tipo->style    = 'width:160px;';
		$edit->tipo->rule     = 'condi_required|callback_chsfpatipo[<#i#>]';
		$edit->tipo->insertValue='EF';
		$edit->tipo->onchange   = 'sfpatipo(<#i#>)';

		$edit->sfpafecha = new dateonlyField('Fecha','sfpafecha_<#i#>');
		$edit->sfpafecha->rel_id   = 'sfpa';
		$edit->sfpafecha->db_name  = 'fecha';
		$edit->sfpafecha->size     = 10;
		$edit->sfpafecha->maxlength= 8;
		$edit->sfpafecha->rule ='condi_required|chitfecha|callback_chtipo[<#i#>]';

		$edit->numref = new inputField('Numero <#o#>', 'num_ref_<#i#>');
		$edit->numref->size     = 12;
		$edit->numref->db_name  = 'num_ref';
		$edit->numref->rel_id   = 'sfpa';
		$edit->numref->rule     = 'condi_required|callback_chtipo[<#i#>]';

		$edit->banco = new dropdownField('Banco <#o#>', 'banco_<#i#>');
		$edit->banco->option('','Ninguno');
		$edit->banco->options('SELECT cod_banc,nomb_banc
			FROM tban
			WHERE cod_banc<>\'CAJ\'
		UNION ALL
			SELECT codbanc,CONCAT_WS(\' \',TRIM(banco),numcuent)
			FROM banc
			WHERE tbanco <> \'CAJ\' ORDER BY nomb_banc');
		$edit->banco->db_name='banco';
		$edit->banco->rel_id ='sfpa';
		$edit->banco->style  ='width:200px;';
		$edit->banco->rule   = 'condi_required|callback_chtipo[<#i#>]';

		$edit->itmonto = new inputField('Monto <#o#>', 'itmonto_<#i#>');
		$edit->itmonto->db_name     = 'monto';
		$edit->itmonto->css_class   = 'inputnum';
		$edit->itmonto->rel_id      = 'sfpa';
		$edit->itmonto->size        = 10;
		$edit->itmonto->rule        = 'condi_required|positive|callback_chmontosfpa[<#i#>]';
		$edit->itmonto->showformat  = 'decimal';
		$edit->itmonto->autocomplete= false;
		//************************************************
		// Fin detalle 2 (sfpa)
		//************************************************

		$edit->buttons('save','undo','back','add_rel','add');
		$edit->build();

		$conten['cana']  = $i;
		$conten['form']  = & $edit;
		$conten['title'] = heading("Cobro a cliente: ($cliente) $scli_nombre $scli_rif");

		$data['head']    = style('estilo.css');
		$data['head']   .= $this->rapyd->get_head();

		$data['script']  = script('jquery.js');
		$data['script'] .= script('plugins/jquery.numeric.pack.js');
		$data['script'] .= script('plugins/jquery.floatnumber.js');
		$data['script'] .= phpscript('nformat.js');

		$data['content'] = $this->load->view('view_ccli.php', $conten,true);
		$data['title']   = '';
		$this->load->view('view_ventanas', $data);
	}

	function chsfpatipo($val){
		$tipo=$this->input->post('tipo_doc');
		if($tipo=='NC') {
			return true;
		}
		$this->validation->set_message('chsfpatipo', 'El campo %s es obligatorio');
		if(empty($val)){
			return false;
		}else{
			return true;
		}
	}

	function chfuturo($fecha){
		$fdoc=timestampFromInputDate($fecha);
		$fact=mktime();

		if($fdoc > $fact){
			$this->validation->set_message('chfuturo', 'No puede meter un efecto a futuro');
			return false;
		}
		return true;
	}

	function chtipo($val,$i){
		$tipo=$this->input->post('tipo_'.$i);
		if(empty($tipo)) return true;
		$this->validation->set_message('chtipo', 'El campo %s es obligatorio');

		if(empty($val) && ($tipo=='NC' || $tipo=='DP'))
			return false;
		else
			return true;
	}

	function chmontosfpa($monto){
		$tipo   = $this->input->post('tipo_doc');
		if($tipo=='NC'){
			return true;
		}
		if(empty($monto) || $monto==0){
			$this->validation->set_message('chmontosfpa', "El campo %s es obligatorio");
			return false;
		}
		return true;
	}

	function chppago($monto,$i){
		$tipo   = $this->input->post('tipo_doc');
		if($tipo=='NC' && $monto>0){
			$this->validation->set_message('chppago', "No se puede hacer pronto pago cuando el tipo de documento es una nota de cr&eacute;dito");
			return false;
		}
		return true;
	}

	function chabono($monto,$i){
		$tipo   = $this->input->post('tipo_doc_'.$i);
		$ppago  = $this->input->post('ppago_'.$i);
		$numero = $this->input->post('numero_'.$i);
		$cod_cli= $this->input->post('cod_cli');
		$fecha  = human_to_dbdate($this->input->post('fecha_'.$i));

		$this->db->select(array('monto - abonos AS saldo'));
		$this->db->from('smov');
		$this->db->where('tipo_doc',$tipo);
		$this->db->where('numero'  ,$numero);
		$this->db->where('fecha'   ,$fecha);
		$this->db->where('cod_cli' ,$cod_cli);

		$query = $this->db->get();
		$row   = $query->row();

		if ($query->num_rows() == 0) return false;
		$saldo = $row->saldo;

		if(($monto+$ppago)<=$saldo){
			return true;
		}else{
			$this->validation->set_message('chabono', "No se le puede abonar al efecto $tipo-$numero un monto mayor al saldo");
			return false;
		}
	}

	function cuenta($cliente){
		if(!$this->_exitescli($cliente)) redirect($this->url.'filterscli');
		//SELECT a.tipo_doc,a.numero,a.monto,c.tipo_doc,c.numero,c.monto
		//FROM smov AS a
		//JOIN itccli AS b ON a.tipo_doc=b.tipoccli AND a.numero=b.numccli
		//JOIN smov AS c ON b.tipo_doc=c.tipo_doc AND b.numero=c.numero
		//WHERE
		//(c.tipo_doc=$dbtipo_doc AND c.numero=$dbnumero)
		//OR
		//(a.tipo_doc=$dbtipo_doc AND a.numero=$dbnumero)

		$do = new DataObject('smov');
		$r1 = array('tipo_doc' => 'numccli' ,'numero'=>'numccli');
		$r2 = array('tipo_doc' => 'tipo_doc','numero'=>'numero' );

		$do->rel_many_to_many('smov', 'smov','itccli',$r1,$r2);
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

	function _pre_insert($do){
		$cliente  =$do->get('cod_cli');
		$estampa = $do->get('estampa');
		$hora    = $do->get('hora');
		$usuario = $do->get('usuario');
		$cod_cli = $do->get('cod_cli');
		$tipo_doc= $do->get('tipo_doc');
		$fecha   = $do->get('fecha');
		$concepto= $do->get('observa1');
		$itabono=$sfpamonto=$ppagomonto=0;

		$rrow    = $this->datasis->damerow('SELECT nombre,rifci,dire11,dire12 FROM scli WHERE cliente='.$this->db->escape($cliente));
		if($rrow!=false){
			$do->set('nombre',$rrow['nombre']);
			$do->set('dire1' ,$rrow['dire11']);
			$do->set('dire2' ,$rrow['dire12']);
		}

		//Totaliza el abonado
		$rel='itccli';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$itabono += $do->get_rel($rel, 'abono', $i);
			$pppago   = $do->get_rel($rel, 'ppago', $i);
			if(empty($pppago)){
				$do->set_rel($rel,'ppago',0,$i);
			}else{
				$ppagomonto += $do->get_rel($rel, 'ppago', $i);
			}
		}
		$itabono=round($itabono,2);

		//Totaliza lo pagado
		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpamonto+=$do->get_rel($rel, 'monto', $i);
		}
		$sfpamonto=round($sfpamonto,2);

		//Realiza las validaciones
		$cajero=$this->secu->getcajero();
		$this->load->library('validation');
		$rt=$this->validation->cajerostatus($cajero);
		if(!$rt){
			$do->error_message_ar['pre_ins']='El cajero usado ('.$cajero.') esta cerrado para esta fecha';
			return false;
		}

		if($tipo_doc=='NC'){
			$do->truncate_rel('sfpa');
			if($itabono==0){
				$do->error_message_ar['pre_ins']='Si crea una nota de credito debe relacionarla con algun movimiento';
				return false;
			}
		}elseif($tipo_doc=='AN'){
			$do->truncate_rel('itccli');
			if($itabono!=0){
				$do->error_message_ar['pre_ins']='Un anticipo no puede estar relacionado con algun efecto, en tal caso seria un abono';
				return false;
			}else{
				$itabono=$sfpamonto;
			}
		}else{
			if(abs($sfpamonto-$itabono)>0.01){
				$do->error_message_ar['pre_ins']='El monto cobrado no coincide con el monto de la la transacci&oacute;n';
				return false;
			}
		}
		//fin de las validaciones
		$do->set('monto',$itabono);

		$dbcliente= $this->db->escape($cliente);
		$rowscli  = $this->datasis->damerow('SELECT nombre,dire11,dire12 FROM scli WHERE cliente='.$dbcliente);
		$do->set('nombre', $rowscli['nombre']);
		$do->set('dire1' , $rowscli['dire11']);
		$do->set('dire2' , $rowscli['dire12']);

		$transac  = $this->datasis->fprox_numero('ntransa');

		if($tipo_doc=='AB'){
			$mnum = $this->datasis->fprox_numero('nabcli');
		}elseif($tipo_doc=='GI'){
			$mnum = $this->datasis->fprox_numero('ngicli');
		}elseif($tipo_doc=='NC'){
			$mnum = $this->datasis->fprox_numero('nccli');
		}else{
			$mnum = $this->datasis->fprox_numero('nancli');
		}
		$do->set('vence'  , $fecha);
		$do->set('numero' , $mnum);
		$do->set('transac', $transac);

		$rel='itccli';
		$observa=array();
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$itabono = $do->get_rel($rel, 'abono'   , $i);
			$ittipo  = $do->get_rel($rel, 'tipo_doc', $i);
			$itnumero= $do->get_rel($rel, 'numero'  , $i);
			if(empty($itabono) || $itabono==0){
				$do->rel_rm($rel,$i);
			}else{
				$observa[]=$ittipo.$itnumero;
				$do->set_rel($rel, 'tipoccli', $tipo_doc, $i);
				$do->set_rel($rel, 'cod_cli' , $cod_cli , $i);
				$do->set_rel($rel, 'estampa' , $estampa , $i);
				$do->set_rel($rel, 'hora'    , $hora    , $i);
				$do->set_rel($rel, 'usuario' , $usuario , $i);
				$do->set_rel($rel, 'transac' , $transac , $i);
				$do->set_rel($rel, 'mora'    , 0, $i);
				$do->set_rel($rel, 'reten'   , 0, $i);
				$do->set_rel($rel, 'cambio'  , 0, $i);
				$do->set_rel($rel, 'reteiva' , 0, $i);
			}
		}

		if(empty($concepto)){
			if(count($observa)>0){
				$observa='PAGA '.implode(',',$observa);
				$do->set('observa1' , substr($observa,0,50));
				if(strlen($observa)>50) $do->set('observa2' , substr($observa,50));
			}
		}else{
			$do->set('observa1' , substr($concepto,0,50));
			if(strlen($concepto)>50) $do->set('observa2' , substr($concepto,50));
		}

		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpatipo=$do->get_rel($rel, 'tipo_doc', $i);
			if($sfpatipo=='EF') $do->set_rel($rel, 'fecha' , $fecha , $i);

			$do->set_rel($rel,'estampa'  , $estampa , $i);
			$do->set_rel($rel,'hora'     , $hora    , $i);
			$do->set_rel($rel,'usuario'  , $usuario , $i);
			$do->set_rel($rel,'transac'  , $transac , $i);
			$do->set_rel($rel,'f_factura', $fecha   , $i);
			$do->set_rel($rel,'cod_cli'  ,$cliente  , $i);
			$do->set_rel($rel,'cobro'    ,$fecha    , $i);
			$do->set_rel($rel,'vendedor' ,$this->secu->getvendedor(),$i);
			$do->set_rel($rel,'cobrador' ,$this->secu->getcajero()  ,$i);
			$do->set_rel($rel,'almacen'  ,$this->secu->getalmacen() ,$i);
		}
		$this->ppagomonto=$ppagomonto;

		$do->set('mora'    ,0);
		$do->set('reten'   ,0);
		$do->set('cambio'  ,0);
		$do->set('reteiva' ,0);
		$do->set('ppago'   ,$ppagomonto);
		$do->set('codigo'  ,'NOCON');
		$do->set('descrip' ,'NOTA DE CONTABILIDAD');
		$do->set('vendedor', $this->secu->getvendedor());
		return true;
	}

	function _post_insert($do){
		$cliente  =$do->get('cod_cli');
		$dbcliente=$this->db->escape($cliente);

		$rel_id='itccli';
		$cana = $do->count_rel($rel_id);
		if($cana>0){
			if($this->ppagomonto>0){
				//Crea la NC por Pronto pago
				$mnumnc = $this->datasis->fprox_numero('nccli');

				$dbdata=array();
				$dbdata['cod_cli']    = $cliente;
				$dbdata['nombre']     = $do->get('nombre');
				$dbdata['dire1']      = $do->get('dire1');
				$dbdata['dire2']      = $do->get('dire2');
				$dbdata['tipo_doc']   = 'NC';
				$dbdata['numero']     = $mnumnc;
				$dbdata['fecha']      = $do->get('fecha');
				$dbdata['monto']      = $this->ppagomonto;
				$dbdata['impuesto']   = 0;
				$dbdata['abonos']     = $this->ppagomonto;
				$dbdata['vence']      = $do->get('fecha');
				$dbdata['tipo_ref']   = 'AB';
				$dbdata['num_ref']    = $do->get('numero');
				$dbdata['observa1']   = 'DESCUENTO POR PRONTO PAGO';
				$dbdata['estampa']    = $do->get('estampa');
				$dbdata['hora']       = $do->get('hora');
				$dbdata['transac']    = $do->get('transac');
				$dbdata['usuario']    = $do->get('usuario');
				$dbdata['codigo']     = 'DEPPC';
				$dbdata['descrip']    = 'DESCUENTO PRONTO PAGO';
				$dbdata['fecdoc']     = $do->get('fecha');
				$dbdata['nroriva']    = '';
				$dbdata['emiriva']    = '';
				$dbdata['reten']      = 0;
				$dbdata['cambio']     = 0;
				$dbdata['mora']       = 0;

				$mSQL = $this->db->insert_string('smov', $dbdata);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'ccli'); }

				$itdbdata=array();
				$itdbdata['cod_cli']  = $cliente;
				$itdbdata['numccli']  = $mnumnc;
				$itdbdata['tipoccli'] = 'NC';
				$itdbdata['estampa']  = $do->get('estampa');
				$itdbdata['hora']     = $do->get('hora');
				$itdbdata['transac']  = $do->get('transac');
				$itdbdata['usuario']  = $do->get('usuario');
				$itdbdata['fecha']    = $do->get('fecha');
				$itdbdata['monto']    = $this->ppagomonto;
				$itdbdata['reten']    = 0;
				$itdbdata['cambio']   = 0;
				$itdbdata['mora']     = 0;

				unset($dbdata);
			}

			foreach($do->data_rel[$rel_id] AS $i=>$data){
				$tipo_doc = $data['tipo_doc'];
				$numero   = $data['numero'];
				$fecha    = $data['fecha'];
				$monto    = $data['abono'];
				$ppago    = (empty($data['ppago']))? 0: $data['ppago'];

				$dbtipo_doc = $this->db->escape($tipo_doc);
				$dbnumero   = $this->db->escape($numero  );
				$dbfecha    = $this->db->escape($fecha   );
				$dbmonto    = $monto+$ppago;

				$mSQL="UPDATE smov SET abonos=abonos+$dbmonto WHERE tipo_doc=$dbtipo_doc AND numero=$dbnumero AND cod_cli=$dbcliente LIMIT 1";

				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'ccli'); }

				if($ppago > 0 ){
					$itdbdata['tipo_doc'] = $tipo_doc;
					$itdbdata['numero']   = $numero;
					$itdbdata['abono']    = $ppago;

					$mSQL = $this->db->insert_string('itccli', $itdbdata);
					$ban=$this->db->simple_query($mSQL);
					if($ban==false){ memowrite($mSQL,'ccli'); }
				}
			}
		}

		$rel='sfpa';
		$cana = $do->count_rel($rel);
		for($i = 0;$i < $cana;$i++){
			$sfpatipo = $do->get_rel($rel, 'tipo', $i);
			$codbanc  = $do->get_rel($rel,'banco',$i);
			$dbcodbanc= $this->db->escape($codbanc);
			$monto    = $do->get_rel($rel,'monto',$i);
			//Si es deposito en banco o transferencia crea el movimiento
			if($sfpatipo=='DE' || $sfpatipo=='NC'){
				$sql ='SELECT tbanco,moneda,banco,saldo,depto,numcuent FROM banc WHERE codbanc='.$dbcodbanc;
				$fila=$this->datasis->damerow($sql);

				$ffecha  = $do->get_rel($rel,'fecha',$i);
				$itdbdata=array();
				$itdbdata['codbanc']  = $codbanc;
				$itdbdata['moneda']   = $fila['moneda'];
				$itdbdata['numcuent'] = $fila['numcuent'];
				$itdbdata['banco']    = $fila['banco'];
				$itdbdata['saldo']    = $fila['saldo']+$monto;
				$itdbdata['tipo_op']  = $do->get_rel($rel,'tipo',$i);
				$itdbdata['numero']   = $do->get_rel($rel,'num_ref',$i);
				$itdbdata['fecha']    = $ffecha;
				$itdbdata['clipro']   = 'C';
				$itdbdata['codcp']    = $cliente;
				$itdbdata['nombre']   = $do->get('nombre');
				$itdbdata['monto']    = $monto;
				$itdbdata['concepto'] = 'INGRESO POR COBRANZA';
				$itdbdata['status']   = 'P';
				$itdbdata['liable']   = 'S';
				$itdbdata['transac']  = $do->get('transac');
				$itdbdata['usuario']  = $do->get('usuario');
				$itdbdata['estampa']  = $do->get('estampa');
				$itdbdata['hora']     = $do->get('hora');
				$itdbdata['anulado']  = 'N';
				$mSQL = $this->db->insert_string('bmov', $itdbdata);
				$ban=$this->db->simple_query($mSQL);
				if($ban==false){ memowrite($mSQL,'ccli'); }

				$sfecha=str_replace('-','',$ffecha);
				$mSQL="CALL sp_actusal($dbcodbanc,'$sfecha',$monto)";
				$ban=$this->db->simple_query($mSQL);
				if($ban==false) memowrite($mSQL,'ccli');
			}
		}
	}

	function _pre_update($do){
		return false;
	}

	function _pre_delete($do){
		return false;
	}

}
