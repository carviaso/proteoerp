<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
class Rcaj extends validaciones {
	var $target = 'popu';

	function Rcaj(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('12A',1);
	}

	function index(){
		$this->instalar();
		$this->target = $this->datasis->dameval('SELECT target FROM intramenu WHERE ejecutar="ventas/rcaj" OR ejecutar="ventas/rcaj/"');
		redirect('ventas/rcaj/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		$this->target = $this->datasis->dameval('SELECT target FROM intramenu WHERE ejecutar="ventas/rcaj" OR ejecutar="ventas/rcaj/"');

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0');

		$recep = anchor('ventas/rcaj/forcierre/','Recepcion de Caja');
		//$filter = new DataFilter($titulo);
		$filter = new DataFilter('');
		$filter->fecha = new dateonlyField('Fecha','fecha','d/m/Y');
		$filter->fecha->db_name='c.f_factura';
		$filter->fecha->size =11;
		$filter->fecha->clause='where';
		$filter->fecha->operator='=';
		$filter->fecha->insertValue=date('Y-m-d');

/*
		$filter->cajero = new dropdownField('Cajero', 'cajero');
		$filter->cajero->db_name='c.cobrador';
		$filter->cajero->option('','Todos');
		$filter->cajero->options('SELECT cajero, nombre FROM scaj ORDER BY nombre');
*/
		$filter->buttons('reset','search');
		$filter->build();

		$data['content'] = $filter->output;

		function iconcaja($cajero,$fecha,$numero='',$tipo='',$caja,$reve=0){
			$cajero=trim($cajero);
			$fecha =trim($fecha);
			$numero=trim($numero);
			$caja  =trim($caja);
			if(empty($caja)) $caja='99';
			//echo $cajero;
			//var_dump(empty($numero));

			$atts=array('align'=>'LEFT','border'=>'0');
			$fecha=str_replace('-','',$fecha);
			$atRI = array(
				'width'     => '800','height' => '600',
				'scrollbars'=> 'yes','status' => 'yes',
				'resizable' => 'yes','screenx'=> '0',
				'screeny'   => '0');

			$CI =& get_instance();
			if(empty($numero)){

				return image('caja_abierta.gif',"Cajero Abierto: $cajero",$atts).'<h3>Abierto</h3><center>'.anchor("ventas/rcaj/precierre/${caja}/${cajero}/${fecha}", 'Pre-cerrar cajero').'</center>';
			}else{
				$reversar=($reve==1) ? anchor('ventas/rcaj/reversar/'.$numero, 'Reversar'):'';
				if($tipo=='T'){
					return image('caja_precerrada.gif',"Cajero Pre-Cerrado: ${cajero}",$atts).'<h3>'.anchor("ventas/rcaj/forcierre/${numero}/", 'Cerrar cajero').'</h3><center>'.anchor('formatos/ver/RECAJA/'.$numero, ' Ver cuadre de caja').br().$reversar.'</center>';
				}else{
					return image('caja_cerrada.gif',"Cajero Cerrado: ${cajero}",$atts).'<h3>Cerrado</h3><center>'.anchor('formatos/ver/RECAJA/'.$numero, ' Ver cuadre de caja').br().$reversar.'</center>';
				}
			}
		}
		$data['forma'] ='';
		if($this->rapyd->uri->is_set('search') && !empty($filter->fecha->value)){
			$fecha=$filter->fecha->value;

			$urip = anchor('formatos/ver/RECAJA/<#numero#>','Descargar html');
			$urih = anchor_popup('formatos/verhtml/RECAJA/<#numero#>', ' Ver cuadre pantalla',$atts);
			//anchor('formatos/ver/RECAJA/<#numero#>'    ,'Descargar pdf');
			$grid = new DataGrid('Lista de Cierres de caja');
			$grid->order_by('fecha','desc');
			$grid->per_page=15;

			$grid = new DataGrid('Recepci&oacute;n de cajas para la fecha: '.$filter->fecha->value);

			$select=array('c.cobrador AS cajero','c.f_factura AS fecha','a.tipo','a.recibido','a.numero','d.nombre','d.caja');
			$grid->db->select($select);
			$grid->db->from('sfpa AS c');
			$grid->db->join('scaj AS d','c.cobrador=d.cajero');
			$grid->db->join('rcaj AS a','a.cajero=c.cobrador AND a.fecha=c.f_factura','left');
			$grid->db->groupby('c.cobrador');
			$grid->use_function('iconcaja');

			$reve=$this->secu->puede('12A0');
			$grid->column('Numero'     ,'<sinulo><#numero#>|---</sinulo>','align=\'center\'');
			$grid->column('Fecha'      ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
			$grid->column('Cajero'     ,'<#cajero#>-<#nombre#>','align=\'center\'');
			$grid->column('Entregado por el cajero'   ,'<sinulo><nformat><#recibido#></nformat>|0.00</sinulo>','align=\'right\'');
			$grid->column('Status/Caja','<iconcaja><#cajero#>|<#fecha#>|<#numero#>|<#tipo#>|<#caja#>|'.$reve.'</iconcaja>','align="center"');
			$grid->column('Ver html'   ,"<siinulo><#numero#>|---|$urih</siinulo>",'align=\'center\'');
			$grid->build();
			$data['content'] .= $grid->output;
		}
		$data['title']   = '<h1>Recepci&oacute;n de Caja</h1>';
		$data['head']    = $this->rapyd->get_head();
		$data['target']  = $this->target;
		$this->load->view('view_ventanas', $data);
	}


	function precierre($caja=NULL,$cajero=NULL,$fecha=NULL){

		//Para cuando venga de datasis y sin parametros
		if(is_null($caja) || is_null($cajero) || is_null($fecha)){
			$redir=false;
			$usuario = $this->session->userdata('usuario');
			$mSQL    = 'SELECT a.cajero,b.caja FROM usuario AS a LEFT JOIN scaj AS b ON a.cajero=b.cajero WHERE us_codigo= ?';
			$query   = $this->db->query($mSQL,array($usuario));
			$rrow    = $query->first_row();
			$cajero  = trim($rrow->cajero);
			$caja    = trim($rrow->caja);
			if(empty($caja )) $caja = '99';
			$fecha   = date('Ymd');
			$url_submit='ventas/rcaj/precierre/process';
			if(empty($cajero)) return;
		}else{
			$url_submit="ventas/rcaj/precierre/${caja}/${cajero}/${fecha}/process";
			$redir=true;
		}

		$dbcajero = $this->db->escape($cajero);
		$dbfecha  = $this->db->escape($fecha);
		$this->rapyd->load('dataform');
		$cana=$this->datasis->dameval('SELECT COUNT(*) AS cana FROM rcaj WHERE cajero='.$dbcajero.' AND fecha='.$dbfecha);
		if($cana>0){
			$data['content'] = 'Cajero '.$cajero.' ya fue cerrado para la fecha '.dbdate_to_human($fecha).' ';
			if($redir) $data['content'] .= anchor('ventas/rcaj/filteredgrid/search','Regresar');
			$data['title']   = '<h1>Recepci&oacute;n de cajas</h1>';
			$data['head']    = $this->rapyd->get_head().script('jquery.pack.js');
			$this->load->view('view_ventanas', $data);
			return ;
		}

		//Chequea que exista la caja
		$cana=$this->datasis->dameval('SELECT COUNT(*) AS cana FROM banc WHERE codbanc='.$this->db->escape($caja));
		if(empty($cana)){
			$data['content'] = 'La caja de ventas y cobro '.$caja.' no existe, debe crearla o asignarle una caja existente al cajero '.$cajero.' para poder realizar el cierre.';
			if($redir) $data['content'] .= br().anchor('ventas/rcaj/filteredgrid/search','Regresar');
			$data['title']   = '<h1>Recepci&oacute;n de cajas</h1>';
			$data['head']    = $this->rapyd->get_head().script('jquery.pack.js');
			$this->load->view('view_ventanas', $data);
			return ;
		}
		//Fin de la existencia de la caja

		$form = new DataForm($url_submit);

		$attr=array(
			'class'  => 'ui-state-default ui-corner-all',
			'onclick'=> "javascript:window.location='".site_url('ventas/rcaj/filteredgrid')."'",
			'value'  => 'Regresar'
		);

		//Inicio del efectivo
		$denomi=array();
		$c_efe=0;
		$query = $this->db->query('SELECT a.tipo,a.denomina,b.cambiobs*a.denomina equivalencia,a.nombre FROM monebillet a JOIN mone b ON a.moneda=b.moneda ORDER BY a.tipo,a.moneda,a.denomina DESC');
		foreach ($query->result() as $i=>$row){
			$arr=array('EFE','cEFE');
			$nn=$arr[0];
			$denomi[$nn]=$row->denomina;
			$c_efe++;
			foreach($arr AS $o=>$nobj){
				$obj = $nobj.$i;
				$denomi[$obj]=$row->denomina;
				$form->$obj = new inputField($row->nombre, $obj);
				$form->$obj->group=($row->tipo=='BI') ? 'Billetes': 'Monedas';
				$form->$obj->size=5+5*$o;
				$form->$obj->style='text-align:right';
				$form->$obj->rule='numeric';
				$form->$obj->autocomplete=false;
				if($o==1){
					$form->$obj->in=$sobj;
					$form->$obj->readonly=true;
					$form->$obj->type='inputhidden';
				}else{
					$form->$obj->css_class='cefectivo';
				}
				$sobj=$obj;
			}
		}
		$arr=array('OEFE'=>'Otras denominaciones','FEFE'=>'Fondo de caja (-)');
		foreach($arr AS $obj=>$titulo){
			$form->$obj = new inputField($titulo, $obj);
			$form->$obj->style='text-align:right';
			$form->$obj->css_class='efectivo';
			$form->$obj->rule='numeric';
			$form->$obj->size=10;
			$form->$obj->autocomplete=false;
		}
		//Fin del efectivo

		//Calculo del monto en retenciones e ISLR
		$retenciones=array();
		$sel=array('TRIM(tipo) AS tipo','SUM(monto) AS monto');
		$this->db->select($sel);
		$this->db->from('sfpa');
		$this->db->where('cobrador',$cajero);
		$this->db->where('fecha',$fecha);
		$this->db->where_in('tipo',array('RI','IR'));
		$this->db->group_by('tipo');
		$query = $this->db->get();
		foreach ($query->result() as $row){
			$retenciones[$row->tipo] = $row->monto;
		}

		//Inicio otras formas de pago
		$c_otrp=0;
		$mSQL='SELECT a.tipo,a.nombre FROM tarjeta a WHERE a.tipo NOT IN (\'EF\',\'RP\',\'NC\',\'ND\') AND a.activo=\'S\'';
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $i=>$row){
			$c_otrp++;
			$arr=array('cOTR');
			foreach($arr AS $o=>$nobj){
				$obj = $nobj.$row->tipo;
				$form->$obj = new inputField($row->nombre, $obj);
				$form->$obj->style='text-align:right';
				$form->$obj->size=10;
				$form->$obj->rule='numeric';
				$form->$obj->autocomplete=false;
				if(in_array($row->tipo,array('RI','IR','RP'))){
					$form->$obj->readonly=true;
					$form->$obj->type='inputhidden';
					$form->$obj->showformat='decimal';
					$form->$obj->insertValue=(isset($retenciones[$row->tipo]))? $retenciones[$row->tipo] : 0;
				}

			}
		}
		//Fin otras formas de pago

		//Inicio tabla Resumen
		$arr=array('TOTR'=>'Total de otras formas de pago','TEFE'=>'Total Efectivo','TGLOB'=>'Total Global','UFAC'=>'Ultima Factura');
		foreach($arr AS $obj=>$titulo){
			$form->$obj = new inputField($titulo, $obj);
			$form->$obj->style='text-align:right';
			$form->$obj->css_class='efectivo';
			$form->$obj->rule='numeric';
			$form->$obj->readonly=true;
			$form->$obj->size=10;
			$form->$obj->autocomplete=false;
			$form->$obj->type='inputhidden';
		}
		$form->$obj->type='';

		$form->$obj->readonly=false;
		//$form->$obj->rule='required';
		$form->$obj->insertValue='';
		//fin Resumen

		$form->submit('btnsubmit','Cerrar cajero');
		$form->build_form();

		$this->rapyd->jquery[]='var denomi='.json_encode($denomi).';';
		$this->rapyd->jquery[]='$(":input").numeric(".");';
		$this->rapyd->jquery[]='$(":input").focus(function (){ $(this).select(); } );';
		$this->rapyd->jquery[]='$(":input").click(function (){ $(this).select(); } );';
		$this->rapyd->jquery[]='$(\'input[name^="cEFE"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$(\'input[name^="cOTR"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$(\'input[name^="FEFE"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$(\'input[name^="OEFE"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$("#df1").submit(function() { return confirm("Estas seguro de realizar el Pre-Cierre?"); })';
		$this->rapyd->jquery[]='$(".cefectivo").bind("keyup",
			function() {
				obj=this.name;
				mul=eval("denomi."+obj);
				valor=$(this).val();
				val=mul*valor;
				$("#c"+obj).val(roundNumber(val,2));
				$("#c"+obj+"_val").text(nformat(val,2));
				gtotal();
			});';
		$this->rapyd->jquery[]='function gtotal(){
			TEFE=TOTR=0;
			$(\'input[name^="cEFE"]\').each(function(i,e){
				if($(this).val().length>0)    TEFE = TEFE+parseFloat($(this).val());
			});
			$(\'input[name^="cOTR"]\').each(function(i,e){
				if($(this).val().length>0)
					TOTR = TOTR+parseFloat($(this).val());
			});
			if($("#OEFE").val().length>0) TEFE=TEFE+parseFloat($("#OEFE").val());
			if($("#FEFE").val().length>0) TEFE=TEFE-parseFloat($("#FEFE").val())

			$("#TEFE").val(roundNumber(TEFE,2));
			$("#TOTR").val(roundNumber(TOTR,2));
			$("#TGLOB").val(roundNumber(TOTR+TEFE,2));

			$("#TEFE_val").text(nformat(TEFE,2));
			$("#TOTR_val").text(nformat(TOTR,2));
			$("#TGLOB_val").text(nformat(TOTR+TEFE,2));
		}';
		$this->rapyd->jquery[]='$("input[name^=\'cOT\']").not("input[id$=\'IR\']").not("input[id$=\'RI\']").not("input[id$=\'RP\']").calculator( {showOn: "button",useThemeRoller:true,onClose: function(value, inst) { gtotal(); }, onClose: function(value, inst) { gtotal(); }} );';
		$this->rapyd->jquery[]='gtotal();';

		//hace el precierre
		if ($form->on_success()){
			$dbfecha  = $this->db->escape($fecha);
			$dbcajero = $this->db->escape($cajero);

			$mSQL="SELECT c.tipo, IFNULL(SUM(aa.monto),0) AS monto FROM
				(SELECT b.tipo, b.monto AS monto
				FROM sfac AS a
				JOIN sfpa AS b ON a.transac=b.transac
				WHERE a.fecha=${dbfecha} AND a.referen='E' AND b.cobrador=${dbcajero} AND a.tipo_doc<>'X' AND MID(a.numero,1,1)<>'_' AND b.tipo<>'RP'
				UNION ALL
				SELECT e.tipo,e.monto AS monto
				FROM sfpa AS e
				WHERE e.f_factura=${dbfecha} AND e.cobrador=${dbcajero} AND e.tipo_doc IN ('AB','AN')
				UNION ALL
				SELECT d.tipo,d.monto AS monto
				FROM sfpa AS d
				WHERE d.f_factura=${dbfecha} AND d.cobrador=${dbcajero} AND d.tipo_doc = 'CC'
				) AS aa
				RIGHT JOIN tarjeta AS c ON aa.tipo=c.tipo GROUP BY c.tipo";
			//echo $mSQL;
			//exit();

			//Toma en cuenta los retiros
			$rret=array();
			if ($this->db->table_exists('rret')){
				$retiquery = $this->db->query("SELECT tipo,SUM(monto) AS monto FROM rret WHERE cajero=${dbcajero} AND fecha=${dbfecha} AND cierre IS NULL GROUP BY tipo");
				foreach ($retiquery->result() as $rreti){
					$rret[$rreti->tipo] = $rreti->monto;
				}
			}

			//Toma en cuenta los cambios de cheque
			$ccheq=0;
			$ccquery = $this->db->query("SELECT SUM(d.monto) AS monto FROM sfpa AS d WHERE d.f_factura=${dbfecha} AND d.cobrador=${dbcajero} AND d.tipo_doc = 'CC'");
			foreach ($ccquery->result() as $ccrow){
				$ccheq += $ccrow->monto;
			}

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				$str='';
				$ingreso= $rrecibido=$parcial=0;
				$numero = $this->datasis->fprox_numero('ningreso');
				$transac= $this->datasis->fprox_numero('ntransa');
				$arr    = array('numero'=>$numero);

				foreach ($query->result() as $row){
					if($row->tipo == 'EF'){
						$nobj='TEFE';
					}else{
						$nobj='cOTR'.$row->tipo;
					}

					$recibido = (isset($form->$nobj))? (empty($form->$nobj->newValue))? 0.00 :floatval($form->$nobj->newValue) : 0.00;
					if(array_key_exists($row->tipo, $rret)) $recibido += $rret[$row->tipo];
					if($row->monto!=0 || $recibido!=0){
						$monto = ($row->tipo=='EF')? $row->monto-$ccheq : $row->monto;
						$str.= $row->tipo.' '.$recibido.'  ';
						$arr['tipo']       = $row->tipo;
						$arr['recibido']   = $recibido;
						$arr['sistema']    = $monto;
						$arr['diferencia'] = $recibido-$monto;
						$ingreso   += $monto;
						$rrecibido += $recibido;
						$mSQL = $this->db->insert_string('itrcaj', $arr);
						$this->db->query($mSQL);
					}
				}
				$arr = array(
					'numero'  => $numero,
					'transac' => $transac,
					'fecha'   => $fecha,
					'cajero'  => $cajero,
					'caja'    => $caja,
					'observa' => $str,
					'usuario' => $this->secu->usuario(),
					'tipo'    => ($rrecibido+$ingreso == 0)? 'F':'T',
					'recibido'=> $rrecibido,
					'ingreso' => $ingreso,
					'parcial' => $parcial
				);
				$mSQL = $this->db->insert_string('rcaj', $arr);
				$this->db->query($mSQL);

				$dbnumero=$this->db->escape($numero);

				$mSQL="UPDATE sfpa JOIN sfac ON sfac.numero=sfpa.numero AND sfpa.tipo_doc=CONCAT(sfac.tipo_doc, IF(sfac.referen='M','E',sfac.referen))
				SET sfpa.cierre=${dbnumero}
				WHERE sfpa.f_factura=${dbfecha}    AND SUBSTRING(sfpa.tipo_doc,2,1)!='X' AND sfpa.cobrador=${dbcajero} ";
				$this->db->query($mSQL);

				if($this->db->table_exists('rret')){
					$mSQL="UPDATE rret SET cierre=${dbnumero} WHERE cajero=${dbcajero} AND fecha=${dbfecha} AND cierre IS NULL";
					$this->db->query($mSQL);
				}
			}
			logusu('rcaj',"Pre-cerro cajero ${cajero} de ${fecha}");
			if($redir){
				redirect('ventas/rcaj/filteredgrid/search');
			}else{
				redirect('ventas/rcaj/precierre');
			}
		}

		$attr=array(
			'class'  => 'ui-state-default ui-corner-all',
			'onclick'=> "javascript:window.location='".site_url('ventas/rcaj/filteredgrid/search')."'",
			'value'  => 'Regresar'
		);

		$cont['form']    = &$form;
		$cont['c_efe']   = $c_efe*2;
		$cont['c_otrp']  = $c_otrp;
		$cont['regresa'] = form_button($attr,'Regresar');
		$data['content'] = $this->load->view('view_rcaj',$cont, true);
		$data['title']   = '<h1>Recepci&oacute;n de cajero '.$cajero.' Fecha '.dbdate_to_human($fecha).'</h1>';
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['head']   .= style('jquery.calculator.css');
		$data['head']   .= script('plugins/jquery.calculator.min.js');
		$data['head']   .= script('plugins/jquery.calculator-es.js');
		$this->load->view('view_ventanas', $data);
	}

	function forcierre($numero){
		$this->rapyd->load('dataform');

		$dbnumero = $this->db->escape($numero);
		$cana=$this->datasis->dameval('SELECT COUNT(*) FROM rcaj WHERE tipo="T" AND numero='.$dbnumero);

		if($cana<1){
			$data['content'] = 'El efecto a cerrar es inv&aacute;lido o ya fue cerrado '.anchor('ventas/rcaj/filteredgrid/search','Regresar');
			$data['title']   = '<h1>Recepci&oacute;n de cajas</h1>';
			$data['head']    = $this->rapyd->get_head().script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
			$this->load->view('view_ventanas', $data);
			return ;
		}else{
			$caja=$this->datasis->dameval('SELECT caja FROM rcaj WHERE tipo="T" AND numero='.$dbnumero);
			if(empty($caja)){
				$data['content'] = 'Falta registro de la caja '.anchor('ventas/rcaj/filteredgrid/search','Regresar');
				$data['title']   = '<h1>Recepci&oacute;n de cajas</h1>';
				$data['head']    = $this->rapyd->get_head().script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
				$this->load->view('view_ventanas', $data);
				return ;
			}
		}

		$nomcajero = $this->datasis->dameval('SELECT CONCAT(TRIM(a.cajero),b.nombre) cajero FROM rcaj a JOIN scaj b ON a.cajero=b.cajero WHERE a.tipo="T" AND a.numero='.$dbnumero);

		$form = new DataForm("ventas/rcaj/forcierre/${numero}/process");

		$attr=array(
			'class'  => 'ui-state-default ui-corner-all',
			'onclick'=> "javascript:window.location='".site_url('ventas/rcaj/filteredgrid')."'",
			'value'  => 'Regresar'
		);

		$totales=array(0,0,0,0);
		$retiros=array();
		$sel=array('TRIM(tipo) AS tipo','SUM(monto) AS monto');
		$this->db->select($sel);
		$this->db->from('rret');
		$this->db->where('cierre',$numero);
		$this->db->group_by('tipo');
		$query = $this->db->get();
		foreach ($query->result() as $row){
			$retiros[$row->tipo] = $row->monto;
			$totales[0]+=$row->monto;
		}

		$mSQL="SELECT TRIM(c.tipo) AS tipo,0 AS retiro,c.nombre,b.recibido,b.sistema,b.diferencia,a.caja
		FROM rcaj    AS a
		JOIN itrcaj  AS b ON a.numero=b.numero
		JOIN tarjeta AS c ON c.tipo=b.tipo
		WHERE a.numero=${dbnumero} AND c.tipo<>'RP'";

		$query = $this->db->query($mSQL);
		if($query->num_rows()>0){
			$arr=array('retiro','recibido','sistema','diferencia');
			foreach ($query->result() as $i=>$row){
				foreach($arr AS $o=>$nobj){
					$obj = $nobj.$row->tipo;
					$totales[$o]+=$row->$nobj;
					$form->$obj = new inputField('('.$row->tipo.') '.$row->nombre, $obj);
					$form->$obj->style='text-align:right';
					$form->$obj->css_class ='inputnum';
					if($nobj=='retiro'){
						$form->$obj->insertValue= (isset($retiros[$row->tipo]))? $retiros[$row->tipo] : '0' ;
						$form->$obj->showformat='decimal';
					}else{
						$form->$obj->insertValue=$row->$nobj;
						$form->$obj->rule='numeric';
					}

					$form->$obj->size=10;
					$form->$obj->autocomplete=false;

					if($o!=1 || in_array($row->tipo,array('RI','IR'))) {
						$form->$obj->readonly=true;
						$form->$obj->type='inputhidden';
					}
				}
			}

			foreach($arr AS $o=>$nobj){
				$obj = 't'.$nobj;
				$form->$obj = new inputField('<b>Totales:</b>', $obj);
				$form->$obj->style='text-align:right';
				$form->$obj->size=10;
				$form->$obj->insertValue=$totales[$o];
				$form->$obj->rule='numeric';
				$form->$obj->autocomplete=false;
				if($o==0) $sobj=$obj; else $form->$obj->in=$sobj;
				$form->$obj->readonly=true;
				$form->$obj->type='inputhidden';
			}
		}

		$b_fiscal=$this->datasis->traevalor('USAMAQFISCAL','Activa el modo fiscal en el cierre de caja');
		if($b_fiscal=='S'){
			$form->x_venta = new inputField('Total Venta seg&uacute;n cierre fiscal','xventa');
			$form->x_venta->rule      ='max_length[17]|numeric|required';
			$form->x_venta->css_class ='inputnum';
			$form->x_venta->size      =19;
			$form->x_venta->maxlength =17;
			$form->x_venta->autocomplete=false;

			$form->x_viva = new inputField('Total IVA seg&uacute;n cierre fiscal','xviva');
			$form->x_viva->rule      ='max_length[17]|numeric|required';
			$form->x_viva->css_class ='inputnum';
			$form->x_viva->size      =19;
			$form->x_viva->maxlength =17;
			$form->x_viva->autocomplete=false;

			$form->x_devo = new inputField('Total de notas de cr&eacute;dito seg&uacute;n cierre fiscal','xdevo');
			$form->x_devo->rule      ='max_length[17]|numeric|required';
			$form->x_devo->css_class ='inputnum';
			$form->x_devo->size      =19;
			$form->x_devo->maxlength =17;
			$form->x_devo->autocomplete=false;

			$form->x_diva = new inputField('Total de IVA seg&uacute;n cierre fiscal','xdiva');
			$form->x_diva->rule      ='max_length[17]|numeric|required';
			$form->x_diva->css_class ='inputnum';
			$form->x_diva->size      =19;
			$form->x_diva->maxlength =17;
			$form->x_diva->autocomplete=false;

			$form->x_maqfiscal = new inputField('Serial M&aacute;quina F&iacute;scal','maqfiscal');
			$form->x_maqfiscal->rule      ='max_length[17]|strtoupper|required';
			$form->x_maqfiscal->size      =19;
			$form->x_maqfiscal->maxlength =17;
			$form->x_maqfiscal->autocomplete=false;

			$form->x_ultimafc = new inputField('N&uacute;mero &uacute;ltima Factura','ultimafc');
			$form->x_ultimafc->rule      ='max_length[10]|required';
			$form->x_ultimafc->size      =12;
			$form->x_ultimafc->maxlength =10;
			$form->x_ultimafc->autocomplete=false;

			$form->x_ultimanc = new inputField('N&uacute;mero &uacute;ltima NC','ultimanc');
			$form->x_ultimanc->rule      ='max_length[10]|required';
			$form->x_ultimanc->size      =12;
			$form->x_ultimanc->maxlength =10;
			$form->x_ultimanc->autocomplete=false;
		}

		$form->button('btn_reg', 'Regresar',"javascript:window.location='".site_url('ventas/rcaj/filteredgrid/search')."'", 'BL');
		$form->submit('btnsubmit','Cerrar cajero');
		$form->build_form();

		$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
		$this->rapyd->jquery[]='$(":input").click(function (){ $(this).select(); } );';
		$this->rapyd->jquery[]='$(":input").focus(function (){ $(this).select(); } );';
		$this->rapyd->jquery[]='$(\'input[name^="recibido"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$(\'input[name^="recibido"]\').bind("mouseleave",function() { gtotal(); });';
		$this->rapyd->jquery[]='$("#df1").submit(function() { return confirm("Estas seguro de realizar el Cierre?"); })';
		$this->rapyd->jquery[]='function gtotal(){
			TRECI=TSIS=TDIFE=0;
			$(\'input[name^="recibido"]\').each(function(i,e){
				nombre=this.name;
				tipo=nombre.substring(nombre.length-2,nombre.length);

				if($(this).val().length>0){
					recibido   =parseFloat($(this).val());
					sistema    =parseFloat($("#sistema"+tipo).val());
					diferencia=recibido-sistema;
					$("#diferencia"+tipo).val(roundNumber(diferencia,2));
					$("#diferencia"+tipo+"_val").text(nformat(diferencia,2));
				}
				if($(this).val().length>0) TRECI = TRECI+parseFloat($(this).val());
			});

			$(\'input[name^="diferencia"]\').each(function(i,e){
				if($(this).val().length>0){
					pval=parseFloat($(this).val());
					TDIFE = TDIFE+pval;
				}
			});

			$("#trecibido").val(roundNumber(TRECI,2));
			$("#tdiferencia").val(roundNumber(TDIFE,2));
			$("#trecibido_val").text(nformat(TRECI,2));
			$("#tdiferencia_val").text(nformat(TDIFE,2));
			$("#tretiro_val").text(nformat($("#tretiro").val(),2));
			$("#tsistema_val").text(nformat($("#tsistema").val(),2));
		}';
		$this->rapyd->jquery[]='gtotal();';

		//Cierre de caja
		if ($form->on_success()){
			$usuario = $this->secu->usuario();
			$estampa = date('Y-m-d');
			$hora    = date('H:i:s');

			$mSQL="SELECT a.fecha,c.tipo,c.nombre ,b.recibido,b.sistema,b.diferencia, a.transac
			FROM rcaj    AS a
			JOIN itrcaj  AS b ON a.numero=b.numero
			JOIN tarjeta AS c ON c.tipo=b.tipo
			WHERE a.numero=${dbnumero}";

			$query = $this->db->query($mSQL);
			if($query->num_rows()>0){
				$str='';
				$arr=array();
				$rrecibido=$sistema=$depositos=0;
				foreach ($query->result() as $i=>$row){
					$nobj='recibido'.$row->tipo;
					$recibido = (isset($form->$nobj))? (empty($form->$nobj->newValue))? 0.00 :floatval($form->$nobj->newValue) : 0.00;
					if($row->sistema>0 || $recibido>0){
						$str.= $row->tipo.' '.$recibido.'  ';
						$arr['recibido']   = $recibido;
						$arr['sistema']    = $row->sistema;
						$arr['diferencia'] = $recibido-$row->sistema;
						$arr['numero']     = $numero;
						$arr['cierre']     = 'S';
						$arr['tipo']       = $row->tipo;

						if($row->tipo=='DE'){
							$depositos += $recibido;
						}else{
							$rrecibido += $recibido;
						}
						$sistema += $row->sistema;

						$mmSQL = $this->db->insert_string('itrcaj', $arr);
						$this->db->query($mmSQL);
					}
				}
				$rcajfecha=$this->db->escape($row->fecha);
				$transac=$row->transac;

				$arr = array(
					'tipo'     => 'F',
					'recibido' => $rrecibido+$depositos,
					'observa'  => $str,
				);

				if($b_fiscal=='S'){
					$arr['xventa']    = $form->x_venta->newValue;
					$arr['xviva']     = $form->x_viva->newValue;
					$arr['xdevo']     = $form->x_devo->newValue;
					$arr['xdiva']     = $form->x_diva->newValue;
					$arr['maqfiscal'] = $form->x_maqfiscal->newValue;
					$arr['ultimafc']  = $form->x_ultimafc->newValue;
					$arr['ultimanc']  = $form->x_ultimanc->newValue;
				}
				$where = 'numero='.$this->db->escape($numero);
				$mmSQL = $this->db->update_string('rcaj', $arr, $where);
				$this->db->query($mmSQL);

				//cierra el cajero

				$cajero=$this->datasis->dameval('SELECT cajero FROM rcaj WHERE numero='.$dbnumero);
				$sifact=$this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE cajero=".$this->db->escape($cajero)." AND fecha > ${rcajfecha}");
				if($sifact==0){
					$arr= array('status'=>'C',
						'fechac'=>date('Ymd'),
						'horac' =>date('h:i:s'),
						'cierre'=>$rrecibido+$depositos,
						'caja'  =>$caja
					);

					$where = 'cajero='.$this->db->escape($cajero);
					$mmSQL = $this->db->update_string('scaj', $arr, $where);
					$ban=$this->db->query($mmSQL);
					if($ban==false) memowrite($mmSQL,'rcaj');
				}

				//Inicio de las transacciones ISLR
				$mmSQL = "SELECT a.monto,a.fecha,a.numero,c.nombre,a.transac
				FROM sfpa AS a
				JOIN rcaj AS b ON a.fecha=b.fecha AND a.cobrador=b.cajero
				JOIN scli AS c ON a.cod_cli=c.cliente
				WHERE b.numero=${dbnumero} AND a.tipo='IR'";
				$qquery = $this->db->query($mmSQL);

				foreach ($qquery->result() as $rrow){
					$XNUMERO = $this->datasis->fprox_numero('ndcli');

					$data['tipo_doc'] = 'ND';
					$data['numero']   = $XNUMERO;
					$data['cod_cli']  = 'RETEN';
					$data['nombre']   = 'RETENCION DE ISLR';
					$data['fecha']    = $rrow->fecha;
					$data['monto']    = $rrow->monto;
					$data['impuesto'] = 0;
					$data['vence']    = date('Ymd',mktime(0, 0, 0, substr($rrow->fecha,5,2)+1, 3, substr($rrow->fecha,0,4)));
					$data['observa1'] = 'RET/ISLR DE FE '.$rrow->numero;
					$data['observa2'] = 'CLIENTE '.$rrow->nombre;
					$data['banco']    = '';
					$data['tipo_op']  = '';
					$data['num_op']   = '';
					$data['reten']    = 0 ;
					$data['ppago']    = 0 ;
					$data['control']  = '';
					$data['cambio']   = 0 ;
					$data['mora']     = 0 ;
					$data['abonos']   = 0 ;
					$data['transac']  = $rrow->transac;
					$data['usuario']  = $usuario;
					$data['estampa']  = $estampa;
					$data['hora']     = $hora;

					$mSQL = $this->db->insert_string('smov', $data);
					$ban=$this->db->query($mSQL);
					if($ban==false) memowrite($mSQL,'rcaj');
				}
				//Fin de las retenciones ISLR

				//Crea el movimiento en bmov
				$mSQL  = 'SELECT fecha, cajero FROM rcaj WHERE numero='.$dbnumero;
				$query = $this->db->query($mSQL);
				$row   = $query->first_row();
				$fecha = $row->fecha;
				$sfecha= str_replace('','-',$fecha);
				$cajero= $row->cajero;

				$nbmov=$this->_banprox($caja);
				$mSQL = 'SELECT moneda, numcuent,banco,saldo FROM banc WHERE codbanc= ? ';
				$query= $this->db->query($mSQL,array($caja));
				$row  = $query->first_row();

				$data = array();
				$data['codbanc']    =$caja;
				$data['moneda']     =$row->moneda;
				$data['numcuent']   =$row->numcuent;
				$data['banco']      =$row->banco;
				$data['saldo']      =$row->saldo;
				$data['tipo_op']    ='NC';
				$data['numero']     =$nbmov;
				$data['fecha']      =$fecha;
				$data['clipro']     ='O';
				$data['codcp']      ='VENT';
				$data['nombre']     ='INGRESOS DIARIOS';
				$data['monto']      =$rrecibido;
				$data['concepto']   ="ENTREGA FINAL CAJERO ${cajero} DIA ".dbdate_to_human($fecha);
				$data['transac']    =$transac;
				$data['usuario']    =$usuario;
				$data['estampa']    =$estampa;
				$data['hora']       =$hora;

				$mSQL = $this->db->insert_string('bmov', $data);
				$ban=$this->db->query($mSQL);
				if($ban==false) memowrite($mSQL,'rcaj');
				//Fin del movimiento en bmov

				//Monto por depositos
				if($depositos>0){
					$nbmov=$this->_banprox($caja);
					$data = array();
					$data['codbanc']    =$caja;
					$data['moneda']     =$row->moneda;
					$data['numcuent']   =$row->numcuent;
					$data['banco']      =$row->banco;
					$data['saldo']      =$row->saldo;
					$data['tipo_op']    ='NC';
					$data['numero']     =$nbmov;
					$data['fecha']      =$fecha;
					$data['clipro']     ='O';
					$data['codcp']      ='VENT';
					$data['nombre']     ='INGRESOS DIARIOS';
					$data['monto']      =$depositos;
					$data['concepto']   ="DEPOSITOS RECIBIDOS CAJERO ${cajero} DIA ".dbdate_to_human($fecha);
					$data['transac']    =$transac;
					$data['usuario']    =$usuario;
					$data['estampa']    =$estampa;
					$data['hora']       =$hora;

					$mSQL = $this->db->insert_string('bmov', $data);
					$ban=$this->db->query($mSQL);
					if($ban==false) memowrite($mSQL,'rcaj');
				}
				//Fin del monto por deposito

				//Actualiza el saldo en la caja
				$this->datasis->actusal($caja, $sfecha, $rrecibido+$depositos);

				//Crea la diferencia en caja si la hay
				$dif=$rrecibido+$depositos-$sistema;
				if($dif!=0.00){
					$mSQL = 'SELECT COUNT(*) AS n  FROM banc WHERE codbanc="DF"';
					$query= $this->db->query($mSQL);
					$row  = $query->first_row();
					if($row->n==0){
						$data['codbanc'] ='DF';
						$data['tbanco']  ='CAJ';
						$data['moneda']  ='Bs';
						$data['banco']   ='CAJA';
						//$data['nombre']  ='DIFERENCIA EN CAJA';
						$data['numcuent']='DIFERENCIA EN CAJA';
						$data['activo']  ='S';
						$data['tipocta'] ='C';
						$data['monto']   = 0;
						$data['saldo']   = 0;
						$data['concepto'] ="SOBRANTE EN CAJERO ${cajero} DIA ".dbdate_to_human($fecha);

						$mSQL = $this->db->insert_string('banc', $data);
						$ban=$this->db->query($mSQL);
						if($ban==false) memowrite($mSQL,'rcaj');
					}

					$nbmov=$this->_banprox('DF');
					$mSQL = 'SELECT moneda, numcuent,banco,saldo FROM banc WHERE codbanc="DF"';
					$query= $this->db->query($mSQL);
					$row  = $query->first_row();

					if($dif<0){ // crea la NC a causa del faltante de caja
						$data = array();
						$data['codbanc']    ='DF';
						$data['moneda']     =$row->moneda;
						$data['numcuent']   =$row->numcuent;
						$data['banco']      =$row->banco;
						$data['saldo']      =$row->saldo;
						$data['tipo_op']    ='NC';
						$data['numero']     =$nbmov;
						$data['fecha']      =$fecha;
						$data['clipro']     ='O';
						$data['codcp']      ='VENT';
						$data['nombre']     ='INGRESOS DIARIOS';
						$data['monto']      =abs($dif);
						$data['concepto']   ="FALTANTE EN CAJA ${caja} CAJERO ${cajero} DIA ".dbdate_to_human($fecha);
						$data['transac']    =$transac;
						$data['usuario']    =$usuario;
						$data['estampa']    =$estampa;
						$data['hora']       =$hora;

						$mSQL = $this->db->insert_string('bmov', $data);
						$ban=$this->db->query($mSQL);
						if($ban==false) memowrite($mSQL,'rcaj');

					}else{ //Crea la ND a causa del sobrante de caja
						$data = array();
						$data['codbanc']    ='DF';
						$data['moneda']     =$row->moneda;
						$data['numcuent']   =$row->numcuent;
						$data['banco']      =$row->banco;
						$data['saldo']      =$row->saldo;
						$data['tipo_op']    ='ND';
						$data['numero']     =$nbmov;
						$data['fecha']      =$fecha;
						$data['clipro']     ='O';
						$data['codcp']      ='VENT';
						$data['nombre']     ='INGRESOS DIARIOS';
						$data['monto']      =abs($dif);
						$data['concepto']   ="SOBRANTE EN CAJA ${caja} CAJERO ${cajero} DIA ".dbdate_to_human($fecha);
						$data['transac']    =$transac;
						$data['usuario']    =$usuario;
						$data['estampa']    =$estampa;
						$data['hora']       =$hora;

						$mSQL = $this->db->insert_string('bmov', $data);
						$ban=$this->db->query($mSQL);
						if($ban==false) memowrite($mSQL,'rcaj');
					}

					$this->datasis->actusal('DF', $sfecha, $dif);
				}

				//Crea los movimientos bmov a consecuencia de los pagos con depositos
				//$mSQL="INSERT IGNORE INTO bmov ( codbanc, tipo_op, numero, fecha, clipro, codcp, nombre, monto, concepto, status, liable, transac, usuario, estampa, hora, anulado)
				//SELECT a.banco codbanc, a.tipo tipo_op, a.num_ref numero, a.fecha, 'C' clipro, a.cod_cli codcp, b.nombre, a.monto, 'INGRESO POR COBRANZA' concepto, 'P' status, 'S' liable, a.transac, a.usuario, a.estampa, a.hora, 'N' anulado
				//FROM sfpa a JOIN scli b ON a.cod_cli=b.cliente
				//WHERE a.tipo='DE' AND tipo_doc='FE' AND fecha=${dbfecha}";
				//$ban=$this->db->query($mSQL);
				//if($ban==false) memowrite($mSQL,'rcaj');

				logusu('rcaj',"Cerro cajero ${cajero} de ${fecha}");

				redirect('ventas/rcaj/filteredgrid/search');
			}
		}

		$attr=array(
			'class'  => 'ui-state-default ui-corner-all',
			'onclick'=> "javascript:window.location='".site_url('ventas/rcaj/filteredgrid/search')."'",
			'value'  => 'Regresar'
		);

		$credito=$this->datasis->dameval("SELECT SUM((a.totalg-a.inicial)*IF(a.tipo_doc='D',-1,1)) AS credito
		FROM sfac AS a
		JOIN rcaj AS b ON a.fecha=b.fecha AND b.cajero=a.cajero
		WHERE a.referen='C' AND b.numero=${dbnumero}");

		$rp=0;
		$mSQL = "SELECT SUM(a.monto) AS rp
		FROM sfpa AS a JOIN rcaj AS b ON a.fecha=b.fecha AND a.cobrador=b.cajero
		WHERE b.numero=${dbnumero} AND a.tipo='RP'";
		$rp+=$this->datasis->dameval($mSQL);

		//Toma en cuenta los cambios de cheque
		$ccheq=0;
		$ccquery = $this->db->query("SELECT SUM(d.monto) AS monto
		FROM sfpa AS d JOIN rcaj AS b ON d.fecha=b.fecha AND d.cobrador=b.cajero
		WHERE b.numero=${dbnumero} AND d.tipo_doc = 'CC'");
		foreach ($ccquery->result() as $ccrow){
			$ccheq += $ccrow->monto;
		}

		$cont['b_fiscal']= $b_fiscal;
		$cont['rp']      = $rp    ;
		$cont['cc']      = $ccheq ;
		$cont['retiros'] = $retiros;
		$cont['credito'] = (empty($credito))? 0 : $credito;
		$cont['form']    = &$form;
		$data['content'] = $this->load->view('view_rcajcierre',$cont, true);
		//$data['content'] = $form->output;
		$data['title']   = heading('Recepci&oacute;n de Caja '.$nomcajero);
		$data['head']    = $this->rapyd->get_head().script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function reversar($numero){
		if($this->secu->puede('12A0')){
			$this->rapyd->uri->keep_persistence();
			$persistence = $this->rapyd->session->get_persistence('ventas/rcaj/filteredgrid', $this->rapyd->uri->gfid);
			$back= (isset($persistence['back_uri'])) ? $persistence['back_uri'] : 'ventas/rcaj/filteredgrid';

			$rt=$this->_reversar($numero);
		}
		redirect($back);
	}

	function _reversar($numero){
		$dbnumero= $this->db->escape($numero);
		$mSQL    = 'SELECT tipo, transac FROM rcaj WHERE numero='.$dbnumero;
		$query   = $this->db->query($mSQL);
		$er      = 0;

		if ($query->num_rows() > 0){
			$row = $query->row();
			if($row->tipo=='F'){
				//Reversa las ISLR
				$mmSQL = "SELECT a.monto,a.fecha,a.numero,a.transac
				FROM sfpa AS a
				JOIN rcaj AS b ON a.fecha=b.fecha AND a.cobrador=b.cajero
				WHERE b.numero=${dbnumero} AND a.tipo='IR'";
				$qquery = $this->db->query($mmSQL);
				foreach ($qquery->result() as $rrow){
					$this->db->where('cod_cli' , 'RETEN');
					$this->db->where('tipo_doc', 'ND');
					$this->db->where('fecha'   , $rrow->fecha);
					$this->db->where('transac' , $rrow->transac);
					$this->db->where('monto'   , $rrow->monto);
					$this->db->delete('smov');
				}
				//Fin del reverso de las ISLR

				$transac  = $row->transac;
				$dbtransac= $this->db->escape($transac);
				$sfecha   = date('Ymd');

				//Reversa los movimientos de caja
				$mmSQL='SELECT codbanc,monto,tipo_op FROM bmov WHERE transac='.$dbtransac;
				$qquery = $this->db->query($mmSQL);
				if ($qquery->num_rows() > 0){
					$rrow = $qquery->row();
					$caja = $rrow->codbanc;
					$monto= ($rrow->tipo_op=='NC') ? $rrow->monto : (-1)*$rrow->monto;

					$this->datasis->actusal($caja, $sfecha, $monto);
					$er +=$ban;
				}
				$mSQL='DELETE FROM bmov WHERE transac='.$dbtransac;
				$ban =$this->db->query($mSQL);
				if($ban==false) memowrite($mSQL,'rcaj');
				$er +=$ban;
			}

			$mSQL='DELETE FROM rcaj   WHERE numero='.$dbnumero;
			$ban =$this->db->query($mSQL);
			if($ban==false) memowrite($mSQL,'rcaj');
			$er +=$ban;
			$mSQL='DELETE FROM itrcaj WHERE numero='.$dbnumero;
			$ban =$this->db->query($mSQL);
			if($ban==false) memowrite($mSQL,'rcaj');
			$er +=$ban;
			$mSQL='UPDATE rret SET cierre=NULL WHERE cierre='.$dbnumero;
			$ban =$this->db->query($mSQL);
			if($ban==false) memowrite($mSQL,'rcaj');
			$er +=$ban;
		}
		logusu('rcaj',"Reverso de cierre de caja numero $numero");

		return ($er>0) ? false: true;
	}

	function _banprox($codban){
		$nom      = 'nBAN'.$codban;
		$dbcodban = $this->db->escape($codban);
		$proxch   = intval($this->datasis->dameval('SELECT TRIM(proxch) AS val FROM banc WHERE codbanc='.$dbcodban));
		while(1){
			$numero  = $this->datasis->fprox_numero($nom,12);
			$inumero = intval($numero);
			if($proxch>$inumero){
				$this->db->query("INSERT INTO ${nom} VALUES(${proxch}, '_USR', NOW())");
				continue;
			}
			$dbnumero= $this->db->escape($numero);

			$mSQL = 'SELECT COUNT(*) AS n FROM bmov WHERE numero='.$dbnumero;
			$query= $this->db->query($mSQL);
			$row  = $query->first_row('array');
			if($row['n']==0) break;
		}
		return $numero;
	}

	function instalar(){

		if(!$this->db->table_exists('rret')){
			$mSQL="CREATE TABLE `rret` (
			`id` int(20) unsigned NOT NULL AUTO_INCREMENT,
			`cierre` varchar(8) DEFAULT NULL,
			`cajero` varchar(5) DEFAULT NULL,
			`tipo` char(2) DEFAULT NULL,
			`monto` decimal(12,2) DEFAULT NULL,
			`fecha` date DEFAULT NULL,
			`estampa` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			KEY `Index 2` (`tipo`,`cajero`)
			) ENGINE=MyISAM COMMENT='Retiros de caja'";

			$this->db->query($mSQL);
		}

		if(!$this->db->table_exists('itrcaj')){
			$mSQL="CREATE TABLE `itrcaj` (
				`numero` VARCHAR(8) NOT NULL DEFAULT '' COLLATE 'latin1_swedish_ci',
				`tipo` VARCHAR(15) NOT NULL DEFAULT '' COLLATE 'latin1_swedish_ci',
				`cierre` CHAR(1) NOT NULL DEFAULT 'N' COLLATE 'latin1_swedish_ci',
				`recibido` DECIMAL(17,2) NULL DEFAULT NULL,
				`sistema` DECIMAL(17,2) NULL DEFAULT NULL,
				`diferencia` DECIMAL(17,2) NULL DEFAULT NULL,
				PRIMARY KEY (`numero`, `tipo`, `cierre`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->query($mSQL);
		}


		$campos=$this->db->list_fields('rcaj');
		if(!in_array('xventa',   $campos)) $this->db->query('ALTER TABLE rcaj ADD COLUMN xventa DECIMAL(17,2) NULL DEFAULT 0');
		if(!in_array('xviva',    $campos)) $this->db->query('ALTER TABLE rcaj ADD COLUMN xviva DECIMAL(17,2) NULL DEFAULT 0');
		if(!in_array('xdevo',    $campos)) $this->db->query('ALTER TABLE rcaj ADD COLUMN xdevo DECIMAL(17,2) NULL DEFAULT 0');
		if(!in_array('xdiva',    $campos)) $this->db->query('ALTER TABLE rcaj ADD COLUMN xdiva DECIMAL(17,2) NULL DEFAULT 0');
		if(!in_array('maqfiscal',$campos)) $this->db->query('ALTER TABLE rcaj ADD COLUMN maqfiscal VARCHAR(17) NULL ');
		if(!in_array('ultimafc', $campos)) $this->db->query('ALTER TABLE rcaj ADD COLUMN ultimafc VARCHAR(10) NULL ');
		if(!in_array('ultimanc', $campos)) $this->db->query('ALTER TABLE rcaj ADD COLUMN ultimanc VARCHAR(10) NULL ');


		$itcampos=$this->db->list_fields('itrcaj');
		if(!in_array('cierre',$itcampos)){
			$mSQL="ALTER TABLE `itrcaj`  ADD COLUMN `cierre` CHAR(1) NOT NULL DEFAULT 'N' AFTER `tipo`";
			$this->db->query($mSQL);
		}

		if( !$this->db->field_exists('cierre', 'sfpa')){
			$mSQL="ALTER TABLE sfpa  ADD COLUMN cierre CHAR(8) DEFAULT '' AFTER hora";
			$this->db->query($mSQL);
		}
	}
}
