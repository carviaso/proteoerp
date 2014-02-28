<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/validaciones.php');
class Rret extends validaciones {

	function Rret(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id('12A',1);
		//$this->load->database();
	}

	function index(){
		redirect('ventas/rret/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();


		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0');

		$sel=array('SUM(a.monto) AS monto','a.estampa AS fecha','GROUP_CONCAT(tipo) AS tipo','b.nombre','a.cajero','a.cierre','a.id');
		$filter = new DataFilter('Filtro de Retiros de Caja');
		$filter->db->select($sel);
		$filter->db->from('rret AS a');
		$filter->db->join('scaj AS b','a.cajero=b.cajero');
		$filter->db->group_by('estampa','cajero');

		$filter->fechad = new dateonlyField('Desde', 'fechad','d/m/Y');
		$filter->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		$filter->fechah->size=$filter->fechad->size=11;
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';

		$filter->cajero = new dropdownField('Cajero', 'cajero');
		$filter->cajero->option('','Todos');
		$filter->cajero->db_name = 'a.cajero';
		$filter->cajero->options('SELECT TRIM(cajero) AS cajero, nombre FROM scaj ORDER BY nombre');

		$filter->buttons('reset','search');
		$filter->build();

		$grid = new DataGrid('Lista de Retiros de Caja');
		$grid->order_by('fecha','asc');
		$grid->per_page = 7;

		$formato= anchor_popup('formatos/verhtml/RRET/<#id#>', 'Ver formato',$atts);

		$grid->column_orderby('Fecha' ,'<dbdate_to_human><#fecha#>|d/m/Y H:i:s</dbdate_to_human>','fecha','align="center"');
		$grid->column_orderby('Cajero','(<#cajero#>) <#nombre#>','cajero','align="left"');
		$grid->column_orderby('Cierre','<sinulo><#cierre#>|No Aplicado</sinulo>','cajero','align="left"');
		$grid->column_orderby('Tipo'  ,'tipo'  ,'tipo'  ,'align="left"');
		$grid->column_orderby('Monto' ,'<nformat><#monto#></nformat>','monto','align="right"');
		$grid->column('Formato',$formato);

		$grid->add('ventas/rret/crear');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = '<h1>Retiros de Caja</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function crear(){
		$this->rapyd->load('dataform');

		$form = new DataForm('ventas/rret/crear/process');

		$cajero=$this->secu->getcajero();
		$form->cajero = new dropdownField('Cajero a realizar retiro', 'cajero');
		$form->cajero->rule='required';
		$form->cajero->insertValue=$cajero;
		$form->cajero->option('','Seleccionar');
		$form->cajero->options('SELECT TRIM(cajero) AS cajero,CONCAT_WS("-",cajero,nombre) FROM scaj ORDER BY cajero');

		$form->submit('btnsubmit','Retirar');
		$form->build_form();

		if($form->on_success()){
			redirect('ventas/rret/retiro/'.$form->cajero->newValue);
		}

		$data['content'] = $form->output;
		$data['title']   = '<h1>Retiros de Caja</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function retiro($cajero=NULL){
		//Para cuando venga de datasis y sin parametros
		if(is_null($cajero)){
			$data['content'] = 'No ha seleccionado ning&uacute;n cajero. no puede realizar el retiro '.anchor('ventas/rret/crear','Regresar');
			$data['title']   = heading('Retiro para el cajero '.$cajero);
			$data['head']    =
			$this->load->view('view_ventanas', $data);
			return;
		}else{
			$this->db->select(array('a.status'));
			$this->db->from('scaj AS a');
			$this->db->where('a.cajero',$cajero);
			$query = $this->db->get();

			if ($query->num_rows() > 0){
				$row = $query->row();
				if($row->status=='C'){
					//Cajero cerrado
					$data['content'] = 'El cajero seleccionado fue cerrado. no puede realizar el retiro '.anchor('ventas/rret/crear','Regresar');
					$data['title']   = heading('Retiro para el cajero '.$cajero);
					$data['head']    =
					$this->load->view('view_ventanas', $data);
					return;
				}
			}else{
				//Cajero no existe
				$data['content'] = 'El cajero seleccionado no existe. no puede realizar el retiro '.anchor('ventas/rret/crear','Regresar');
				$data['title']   = heading('Retiro para el cajero '.$cajero);
				$data['head']    =
				$this->load->view('view_ventanas', $data);
				return;
			}
		}

		$this->rapyd->load('dataform');

		$form = new DataForm('ventas/rret/retiro/'.$cajero.'/process');

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
				}else{
					$form->$obj->css_class='cefectivo';
				}
				$sobj=$obj;
			}
		}
		$arr=array('OEFE'=>'Otras denominaciones');
		foreach($arr AS $obj=>$titulo){
			$form->$obj = new inputField($titulo, $obj);
			$form->$obj->style='text-align:right';
			$form->$obj->css_class='efectivo';
			$form->$obj->rule='numeric';
			$form->$obj->size=10;
			$form->$obj->autocomplete=false;
		}
		//Fin del efectivo

		//Inicio otras formas de pago
		$c_otrp=0;
		$mSQL='SELECT a.tipo,a.nombre FROM tarjeta a WHERE a.tipo=\'CH\'';
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
			}
		}
		//Fin otras formas de pago

		//Inicio tabla Resumen
		$arr=array('TOTR'=>'Total de otras formas de pago','TEFE'=>'Total Efectivo','TGLOB'=>'Total Global');
		foreach($arr AS $obj=>$titulo){
			$form->$obj = new inputField($titulo, $obj);
			$form->$obj->style='text-align:right';
			$form->$obj->css_class='efectivo';
			$form->$obj->rule='numeric';
			$form->$obj->readonly=true;
			$form->$obj->size=10;
			$form->$obj->autocomplete=false;
		}

		$form->$obj->readonly=false;
		//$form->$obj->rule='required';
		$form->$obj->insertValue='';
		//fin Resumen

		$form->submit('btnsubmit','Hacer retiro');
		$form->build_form();

		$this->rapyd->jquery[]='var denomi='.json_encode($denomi).';';
		$this->rapyd->jquery[]='$(":input:not(input[name^=\'cOT\'])").numeric(".");';
		$this->rapyd->jquery[]='$(\'input[name^="cEFE"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$(\'input[name^="cOTR"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$(\'input[name^="FEFE"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$(\'input[name^="OEFE"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$("#df1").submit(function() { return confirm("Estas seguro de realizar el Retiro de caja?"); })';
		$this->rapyd->jquery[]='$(".cefectivo").bind("keyup",
			function() {
				obj=this.name;
				mul=eval("denomi."+obj);
				valor=$(this).val();
				$("#c"+obj).val(roundNumber(mul*valor,2));
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

			$("#TEFE").val(roundNumber(TEFE,2));
			$("#TOTR").val(roundNumber(TOTR,2));
			$("#TGLOB").val(roundNumber(TOTR+TEFE,2));
		}';
		$this->rapyd->jquery[]='$("input[name^=\'cOT\']").calculator( {showOn: "button",useThemeRoller:true,onClose: function(value, inst) { gtotal(); }, onClose: function(value, inst) { gtotal(); }} );';

		//hace el retiro de caja
		if ($form->on_success()){
			$estampa=date('Y-m-d H:i:s');
			//$dbcajero = $this->db->escape($cajero);
			$fecha=date('Ymd');
			$arr=array();

			$mSQL='SELECT tipo FROM tarjeta';
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				$monto=0;

				foreach ($query->result() as $row){
					if($row->tipo == 'EF'){
						$nobj='TEFE';
					}else{
						$nobj='cOTR'.$row->tipo;
					}

					if(isset($form->$nobj)){
						$recibido=$form->$nobj->newValue;
						if($recibido>0){
							$arr['tipo']   = $row->tipo;
							$arr['cajero'] = $cajero;
							$arr['monto']  = $recibido;
							$arr['fecha']  = $fecha;
							$arr['estampa']= $estampa;
							$monto+=$recibido;

							$mSQL = $this->db->insert_string('rret', $arr);
							$this->db->simple_query($mSQL);
						}
					}
				}
			}
			$iid=$this->db->insert_id();
			redirect('ventas/rret/retirohecho/'.$cajero.'/'.$monto.'/'.$iid);
		}

		$attr=array(
			'class'  => 'ui-state-default ui-corner-all',
			'onclick'=> "javascript:window.location='".site_url('ventas/rret/filteredgrid')."'",
			'value'  => 'Regresar'
		);

		$cont['form']    = &$form;
		$cont['c_efe']   = $c_efe*2;
		$cont['c_otrp']  = $c_otrp;
		$cont['regresa'] = form_button($attr,'Regresar');
		$data['content'] = $this->load->view('view_rret',$cont, true);
		$data['title']   = heading('Retiro para el cajero '.$cajero);
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js').style('jquery.calculator.css');
		$data['head']   .= script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['head']   .= script('plugins/jquery.calculator.min.js');
		$data['head']   .= script('plugins/jquery.calculator-es.js');

		$this->load->view('view_ventanas', $data);
	}

	function retirohechocaj($cajero,$monto,$id=0){
		$nombre=$this->datasis->dameval('SELECT nombre FROM scaj WHERE cajero='.$this->db->escape($cajero));

		if($id==0)
			$descarga=anchor('formatos/descargar/RRET/'.$id,'Imprimir');
		else
			$descarga='';

		$data['content'] = "<h1>Retiro realizado al cajero <b>$cajero - $nombre</b></h1>";
		$data['content'].= "<p>por un monto de <b>".nformat($monto).'</b> '.$descarga.'</p>';
		$data['content'].= '<center>'.anchor('ventas/rret/retirocaj','Regresar').'</center>';
		$data['title']   = '<h1>Retiros de Caja</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}


	function retirohecho($cajero,$monto,$id=0){
		$nombre=$this->datasis->dameval('SELECT nombre FROM scaj WHERE cajero='.$this->db->escape($cajero));

		if($id!=0)
			$descarga=anchor('formatos/descargar/RRET/'.$id,'Imprimir');
		else
			$descarga='';

		$data['content'] = "<h1>Retiro realizado al cajero <b>$cajero - $nombre</b></h1>";
		$data['content'].= '<p>por un monto de <b>'.nformat($monto).'</b> '.$descarga.'</p>';
		$data['content'].= '<center>'.anchor('ventas/rret','Regresar').'</center>';

		$data['title']   = '<h1>Retiros de Cajero</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function instalar(){
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

		$this->db->simple_query($mSQL);
	}

	function retirocaj(){
		//Para cuando venga de datasis y sin parametros
		$cajero = $this->datasis->dameval("SELECT cajero FROM usuario WHERE us_codigo=".trim($this->db->escape($this->session->userdata('usuario')))." ");
		if( is_null($cajero) ){
			echo "Cajero Invalido";
			return;
		}

		$this->rapyd->load('dataform');

		$form = new DataForm('ventas/rret/retiro/'.$cajero.'/process');

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
				}else{
					$form->$obj->css_class='cefectivo';
				}
				$sobj=$obj;
			}
		}
		$arr=array('OEFE'=>'Otras denominaciones');
		foreach($arr AS $obj=>$titulo){
			$form->$obj = new inputField($titulo, $obj);
			$form->$obj->style='text-align:right';
			$form->$obj->css_class='efectivo';
			$form->$obj->rule='numeric';
			$form->$obj->size=10;
			$form->$obj->autocomplete=false;
		}
		//Fin del efectivo

		//Inicio otras formas de pago
		$c_otrp=0;
		$mSQL='SELECT a.tipo,a.nombre FROM tarjeta a WHERE a.tipo=\'CH\'';
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
			}
		}
		//Fin otras formas de pago

		//Inicio tabla Resumen
		$arr=array('TOTR'=>'Total de otras formas de pago','TEFE'=>'Total Efectivo','TGLOB'=>'Total Global');
		foreach($arr AS $obj=>$titulo){
			$form->$obj = new inputField($titulo, $obj);
			$form->$obj->style='text-align:right';
			$form->$obj->css_class='efectivo';
			$form->$obj->rule='numeric';
			$form->$obj->readonly=true;
			$form->$obj->size=10;
			$form->$obj->autocomplete=false;
		}

		$form->$obj->readonly=false;
		//$form->$obj->rule='required';
		$form->$obj->insertValue='';
		//fin Resumen

		$form->submit('btnsubmit','Hacer retiro');
		$form->build_form();

		$this->rapyd->jquery[]='var denomi='.json_encode($denomi).';';
		$this->rapyd->jquery[]='$(":input:not(input[name^=\'cOT\'])").numeric(".");';
		$this->rapyd->jquery[]='$(\'input[name^="cEFE"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$(\'input[name^="cOTR"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$(\'input[name^="FEFE"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$(\'input[name^="OEFE"]\').bind("keyup",function() { gtotal(); });';
		$this->rapyd->jquery[]='$("#df1").submit(function() { return confirm("Estas seguro de realizar el Retiro de caja?"); })';
		$this->rapyd->jquery[]='$(".cefectivo").bind("keyup",
			function() {
				obj=this.name;
				mul=eval("denomi."+obj);
				valor=$(this).val();
				$("#c"+obj).val(roundNumber(mul*valor,2));
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

			$("#TEFE").val(roundNumber(TEFE,2));
			$("#TOTR").val(roundNumber(TOTR,2));
			$("#TGLOB").val(roundNumber(TOTR+TEFE,2));
		}';
		$this->rapyd->jquery[]='$("input[name^=\'cOT\']").calculator( {showOn: "button",useThemeRoller:true,onClose: function(value, inst) { gtotal(); }, onClose: function(value, inst) { gtotal(); }} );';

		//hace el retiro de caja
		if ($form->on_success()){
			//$dbcajero = $this->db->escape($cajero);
			$fecha=date('Ymd');
			$arr=array();

			$mSQL='SELECT tipo FROM tarjeta';
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				$monto=0;

				foreach ($query->result() as $row){
					if($row->tipo == 'EF'){
						$nobj='TEFE';
					}else{
						$nobj='cOTR'.$row->tipo;
					}

					if(isset($form->$nobj)){
						$recibido=$form->$nobj->newValue;
						if($recibido>0){
							$arr['tipo']   = $row->tipo;
							$arr['cajero'] = $cajero;
							$arr['monto']  = $recibido;
							$arr['fecha']  = $fecha;
							$monto+=$recibido;

							$mSQL = $this->db->insert_string('rret', $arr);
							$this->db->simple_query($mSQL);
						}
					}
				}
			}
			redirect('ventas/rret/retirohecho/'.$cajero.'/'.$monto);
		}

		$attr=array(
			'class'  => 'ui-state-default ui-corner-all',
			'onclick'=> "javascript:window.location='".site_url('ventas/rret/retirocaj')."'",
			'value'  => 'Regresar'
		);

		$cont['form']    = &$form;
		$cont['c_efe']   = $c_efe*2;
		$cont['c_otrp']  = $c_otrp;
		$cont['regresa'] = form_button($attr,'Regresar');
		$data['content'] = $this->load->view('view_rret',$cont, true);
		$data['title']   = heading('Retiro para el cajero '.$cajero);
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js').style('jquery.calculator.css');
		$data['head']   .= script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['head']   .= script('plugins/jquery.calculator.min.js');
		$data['head']   .= script('plugins/jquery.calculator-es.js');

		$this->load->view('view_ventanas', $data);
	}

}
