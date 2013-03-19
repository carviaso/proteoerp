<?php
require_once(BASEPATH.'application/controllers/ventas/sfac.php');
class mensualidad extends sfac {

	var $titp='Mensualidad';
	var $tits='Cobro de servicios mensuales';
	var $url ='ventas/mensualidad/';

	function mensualidad(){
		parent::Controller();
		$this->back_dataedit='/datafilter';
		$this->load->library('rapyd');
		//$this->datasis->modulo_id(216,1);
	}

	//Para facturar servicios por mes
	function servxmes($status){
		$this->load->library('validation');
		$this->genesal=false;
		$this->back_url=$this->url.'filteredgrid';

		$cliente = $this->input->post('cod_cli');
		$nombre  = $this->input->post('fnombre');
		$cana    = $this->input->post('cana_0');
		$sfpatipo= trim($this->input->post('tipo_0'));
		$utribu  = $this->input->post('utribu');
		unset($_POST['utribu']);

		if($sfpatipo=='-' || empty($sfpatipo)){
			echo 'Debe seleccionar una forma de pago';
			return;
		}

		//Averigua si es residencial o no para cobrar iva
		$tipotari= trim($this->datasis->dameval('SELECT b.tipo FROM scli AS a JOIN tarifa AS b ON a.tarifa=b.id WHERE cliente='.$this->db->escape($cliente)));
		if($tipotari=='C'){
			$codigo = $this->datasis->traevalor('SINVTARIFAIVA','RELACIONA ARTICULO CON LOS SERVICOS CON IVA');
		}else{
			$codigo = $this->datasis->traevalor('SINVTARIFA','RELACIONA ARTICULO CON LOS SERVICIOS');
		}

		if(empty($cana) || empty($cliente)){
			echo 'Los campos de cliente y cantidad son obligatorios.';
			return;
		}

		if(!$this->validation->existescli($cliente)){
			echo 'El c&oacute;digo del cliente es inv&aacute;lido';
			return;
		}

		if(empty($nombre)){
			echo 'El campo nombre es necesario.';
			return;
		}else{
			$dbcliente=$this->db->escape($cliente);
			$nom= $this->datasis->dameval('SELECT nombre FROM scli WHERE cliente='.$dbcliente);
			if(empty($nom)){
				$dbnombre = $this->db->escape(utf8_decode($nombre));
				$mSQL="UPDATE scli SET nombre=$dbnombre WHERE cliente=$dbcliente";
				$this->db->simple_query($mSQL);
			}
		}

		$sclir  = $this->datasis->damereg("SELECT * FROM scli WHERE cliente= ".$this->db->escape($cliente));
		$sinvr  = $this->datasis->damereg("SELECT * FROM sinv WHERE codigo = ".$this->db->escape($codigo));

		$campos = $this->db->list_fields('sfac');
		if (!in_array('upago',$campos)){
			$mSQL="ALTER TABLE `sfac` ADD COLUMN `upago` INT(10) NULL DEFAULT NULL COMMENT 'Fecha desde que se pago el servicio mensual' AFTER `maestra`;";
			$this->db->simple_query($mSQL);
		}

		if ($status=='insert'){
			$meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');

			$date   = DateTime::createFromFormat('Ymd', $sclir['upago'].'01');

			$desde = dbdate_to_human($sclir['upago'].'01','m/Y');

			$tarifa= round($this->input->post('preca_0'),2);

			$_POST['pfac']        = '';
			$_POST['fecha']       = date('d/m/Y');
			$_POST['cajero']      = $this->secu->getcajero();
			$_POST['vd']          = $this->secu->getvendedor();
			$_POST['almacen']     = $this->secu->getalmacen();
			$_POST['tipo_doc']    = 'F';
			$_POST['factura']     = '';
			//$_POST['cod_cli']     = $row->cliente;
			$_POST['sclitipo']    = '1';
			$_POST['nombre']      = $sclir['nombre'];
			$_POST['rifci']       = $sclir['rifci'];
			$_POST['direc']       = $sclir['dire11'];
			$_POST['upago']       = $sclir['upago'];

			$monto  = 0;
			$costos = $this->_utributa($sclir['upago'],$cana);
			foreach($costos as $id=>$val){
				$preca = round($val[0]*$utribu,2);
				$tota  = $preca*$val[1];

				$date->add(new DateInterval('P1M'));
				$ind = 'codigoa_'.$id;  $_POST[$ind] = $codigo;
				$ind = 'desca_'.$id;    $_POST[$ind] = $sinvr['descrip'];
				$ind = 'cana_'.$id;     $_POST[$ind] = $val[1];
				$ind = 'preca_'.$id;    $_POST[$ind] = $preca;
				$ind = 'tota_'.$id;     $_POST[$ind] = $tota;
				$ind = 'precio1_'.$id;  $_POST[$ind] = 0;
				$ind = 'precio2_'.$id;  $_POST[$ind] = 0;
				$ind = 'precio3_'.$id;  $_POST[$ind] = 0;
				$ind = 'precio4_'.$id;  $_POST[$ind] = 0;
				$ind = 'itiva_'.$id;    $_POST[$ind] = round($sinvr['iva'],2);
				$ind = 'sinvpeso_'.$id; $_POST[$ind] = 0;
				$ind = 'sinvtipo_'.$id; $_POST[$ind] = 'Servicio';

				$ind = 'detalle_'.$id;
				if($val[1]>1){
					$_POST[$ind] = 'Desde '.$meses[$date->format('n')-1].' de '.$date->format('Y');
					$mm=$val[1]-1;
					$date->add(new DateInterval('P'.$mm.'M'));
					$_POST[$ind] .= ' hasta '.$meses[$date->format('n')-1].' de '.$date->format('Y');
				}else{
					$_POST[$ind] = 'Mes '.$meses[$date->format('n')-1].' de '.$date->format('Y');
				}

				$monto += $tota;
			}

			//$_POST['tipo_0']     = $this->input->post('fcodigo');
			$_POST['sfpafecha_0']  = '';
			//$_POST['num_ref_0']  = $this->input->post('fcomprob');
			$_POST['banco_0']      = '';
			$_POST['monto_0']      = $monto*(1+($sinvr['iva']/100)) ;

			if($monto<=0){
				echo 'Monto incorrecto.';
				return;
			}

			ob_start();
				parent::dataedit();
				$rt = ob_get_contents();
			@ob_end_clean();

			$getdata=json_decode($rt,true);
			if($getdata['status']=='A'){
				echo "Venta Guardada ".$getdata['pk']['id'];
			}else{
				echo $getdata['mensaje'];
			}

		}
	}

	function tarifa(){
		$cliente   = $this->input->post('cliente');
		$cana      = $this->input->post('cana');

		$dbcliente = $this->db->escape($cliente);
		$upago     = $this->datasis->dameval('SELECT upago FROM scli WHERE cliente='.$dbcliente);

		$cobros = $this->_utributa($upago,$cana);

		$mSQL="SELECT
			IF(a.tarimonto>0,ROUND(a.tarimonto,2), ROUND(b.minimo,2)) precio1
			FROM scli AS a
			JOIN tarifa AS b ON a.tarifa=b.id
			WHERE cliente=${dbcliente}";
		$tarifa = $this->datasis->dameval($mSQL);

		$monto = $cana = 0;
		foreach($cobros as $val){
			$monto+= $val[0]*$val[1];
			$cana += $val[1];
		}
		echo round($monto/$cana,2);
	}

	function _utributa($upago,$cana){
		$upago  = $upago.'01';

		$date   = DateTime::createFromFormat('Ymd', $upago);
		$date->add(new DateInterval("P1M"));
		$fdesde = $date->format('Ymd');

		$date   = DateTime::createFromFormat('Ymd', $upago);
		$date->add(new DateInterval("P${cana}M"));
		$hasta  = $date->format('Ym');
		$fhasta = $date->format('Ymd');

		$dbfdesde = $this->db->escape($fdesde);
		$dbfhasta = $this->db->escape($fhasta);

		$arr_tari=array();
		$tarifave=$this->datasis->dameval("SELECT valor FROM utributa WHERE fecha <= $dbfdesde ORDER BY fecha DESC LIMIT 1");

		$arr_tari[substr($upago,0,6)]=$tarifave;

		$mSQL = "SELECT valor,DATE_FORMAT(DATE_ADD(fecha,INTERVAL 1 MONTH),'%Y%m') AS fecha FROM utributa WHERE fecha BETWEEN $dbfdesde AND $dbfhasta";
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $row){
			$indfecha = str_replace('-','',$row->fecha);
			$arr_tari[$indfecha] = $row->valor;
		}

		$cobros   = array();
		$valor    = $tarifave;
		$cana     =  $i = 0;
		$date     = DateTime::createFromFormat('Ymd', $upago);
		$indfecha = date('Ym', mktime(0, 0, 0, $date->format('n')+1, 1,$date->format('Y')));
		while($indfecha <= $hasta){
			if(isset($arr_tari[$indfecha]) && $cana > 0){
				$cobros[]= array($valor,$cana);
				$cana    = 0;
				$valor   = $arr_tari[$indfecha];
			}

			$i++;
			$cana++;
			$indfecha = date('Ym', mktime(0, 0, 0, $date->format('n')+1+$i, 1,$date->format('Y')));
		}
		if($cana > 0){
			$cobros[]= array($valor,$cana);
		}
		return $cobros;
	}

	function _pre_insert($do){
		$rt=parent::_pre_insert($do);
		if($rt === false){
			return $rt;
		}

		$dbcliente = $this->db->escape($do->get('cod_cli'));
		$msql    = 'SELECT tarifa,upago FROM scli WHERE cliente='.$dbcliente;
		$row     = $this->datasis->damerow($msql);

		$upago   = trim($row['upago']).'01';
		$tarifa  = trim($row['tarifa']);
		if(empty($upago)) $upago=20120101;

		$cana = 0;
		$ccana=$do->count_rel('sitems');
		for($i = 0;$i < $ccana;$i++){
			//$detalle = $do->get_rel('sitems','detalle',$i);
			$cana    += $do->get_rel('sitems','cana'    ,$i);
		}

		$objdated = date_create(dbdate_to_human($upago,'Y-m-d'));
		$objdated->add(new DateInterval('P1M'));
		$desde   = date_format($objdated, 'm/Y');

		$objdate = date_create(dbdate_to_human($upago,'Y-m-d'));
		$objdate->add(new DateInterval('P'.$cana.'M'));
		$hasta   = date_format($objdate, 'm/Y');

		$this->_fhasta = date_format($objdate, 'Ym');
		//$det     = "Desde $desde hasta $hasta";
		//$do->set_rel('sitems','detalle',$det,$i);
	}

	function _post_insert($do){
		$rt = parent::_post_insert($do);

		if($rt === false){
			return $rt;
		}

		if(isset($this->_fhasta)){
			$dbcliente = $this->db->escape($do->get('cod_cli'));
			$dbupago   = $this->db->escape($this->_fhasta);
			$mSQL = "UPDATE scli SET upago=$dbupago WHERE cliente=$dbcliente";
			$this->db->simple_query($mSQL);
		}

		return true;
	}

	function _post_delete($do){
		$rt = parent::_post_delete($do);
		$upago  = $do->get('upago');
		$cliente= $do->get('cliente');

		$dbcliente = $this->db->escape($do->get('cod_cli'));
		$dbupago   = $this->db->escape($upago);
		$mSQL = "UPDATE scli SET upago=$dbupago WHERE cliente=$dbcliente";
		$this->db->simple_query($mSQL);

		return true;
	}


	function lote($status=null,$grupo,$upago,$tarifa){
		$this->load->helper('download');
		$this->genesal=false;
		$this->back_url=$this->url.'filteredgrid';

		$dbgrupo = $this->db->escape($grupo);
		$dbupago = $this->db->escape($upago);
		$dbtarifa= $this->db->escape($tarifa);
		$data   = '';

		if($status=='insert'){
			$codigo = $this->datasis->traevalor('SINVTARIFA');
			$iva    = $this->datasis->dameval("SELECT iva FROM sinv WHERE codigo=".$this->db->escape($codigo));
			$descrip= $this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo=".$this->db->escape($codigo));
			$ut     = $this->datasis->dameval("SELECT valor FROM utributa ORDER BY fecha DESC LIMIT 1");
			$cana   = 1;

			$mSQL="SELECT TRIM(a.nombre) AS nombre, TRIM(a.rifci) AS rifci, a.cliente, a.tipo , a.dire11 AS direc, round(b.minimo*$ut,2) precio1, a.upago, a.telefono, b.id codigo
				FROM scli AS a
				JOIN tarifa AS b ON a.tarifa=b.id
				WHERE a.grupo=$dbgrupo AND a.upago=$dbupago AND a.tarifa=$dbtarifa
				ORDER BY rifci";

			$query = $this->db->query($mSQL);
			foreach ($query->result() as $row){
				$dbcliente= $this->db->escape($row->cliente);
				$sql      = "SELECT SUM(monto*(tipo_doc IN ('FC','GI','ND'))) AS debe, SUM(monto*(tipo_doc IN ('NC','AB','AN'))) AS haber FROM smov WHERE cod_cli=$dbcliente";
				$qquery    = $this->db->query($sql);
				if ($qquery->num_rows() > 0){
					$rrow = $qquery->row();
					$saldo=$rrow->debe-$rrow->haber;
				}else{
					$saldo=0;
				}
				$saldo += $row->precio1*(1+($iva/100));
				$sql="UPDATE scli SET credito='S',tolera=10,maxtole=10,limite=$saldo,formap=30 WHERE cliente=$dbcliente";
				$this->db->simple_query($sql);

				$desde = dbdate_to_human($row->upago.'01','m/Y');

				$_POST['pfac']        = '';
				$_POST['fecha']       = date('d/m/Y');
				$_POST['cajero']      = $this->secu->getcajero();
				$_POST['vd']          = $this->secu->getvendedor();
				$_POST['almacen']     = $this->secu->getalmacen();
				$_POST['tipo_doc']    = 'F';
				$_POST['factura']     = '';
				$_POST['cod_cli']     = $row->cliente;
				$_POST['sclitipo']    = '1';
				$_POST['nombre']      = $row->nombre;
				$_POST['rifci']       = $row->rifci;
				$_POST['direc']       = $row->direc;
				$_POST['upago']       = $row->upago;
				$_POST['codigoa_0']   = $codigo;

				$_POST['desca_0']     = $descrip;

				$_POST['detalle_0']   = "Desde $desde";
				$_POST['cana_0']      = $cana;
				$_POST['preca_0']     = $row->precio1;

				$_POST['tota_0']      = $row->precio1;
				$_POST['precio1_0']   = 0;
				$_POST['precio2_0']   = 0;
				$_POST['precio3_0']   = 0;
				$_POST['precio4_0']   = 0;
				$_POST['itiva_0']     = round($iva,2);
				$_POST['sinvpeso_0']  = 0;
				$_POST['sinvtipo_0']  = 'Servicio';

				$_POST['tipo_0']       = '';
				$_POST['sfpafecha_0']  = '';
				$_POST['num_ref_0']    = '';
				$_POST['banco_0']      = '';
				$_POST['monto_0']      = $row->precio1*(1+($iva/100)) ;

				ob_start();
					parent::dataedit();
					$rt = ob_get_contents();
				@ob_end_clean();

				$getdata=json_decode($rt,true);

				if($getdata['status']=='A'){
					$id=$getdata['pk']['id'];
					$url=$this->_direccion='http://localhost/'.site_url('formatos/descargartxt/FACTSER/'.$id);
					$data .= file_get_contents($url);
					$data .= "<FIN>\r\n";
				}
			}
			force_download('inprin.prn', preg_replace("/[\r]*\n/","\r\n",$data));
		}
	}

	function factucred(){
		$this->rapyd->load('dataedit','dataform','datagrid');

		$form = new DataForm('ventas/mensualidad/factucred/modifica');

		$form->grupo = new dropdownField('Grupo', 'grupo');
		$form->grupo->option('','Seleccione un grupo');
		$form->grupo->options('SELECT grupo, CONCAT(grupo," ",gr_desc) gr_desc FROM grcl ORDER BY gr_desc');
		$form->grupo->rule = 'required';
		$form->grupo->size = 6;
		$form->grupo->maxlength = 4;
		$form->grupo->style = 'width:300px';
		//$form->grupo->insertValue = $this->datasis->dameval('SELECT grupo FROM grcl WHERE gr_desc like "CONSUMIDOR FINAL%"');

		$form->submit("btnsubmit","Consultar");
		$form->build_form();

		$tt='';
		if ($form->on_success()){
			$grupo  = trim($form->grupo->newValue);
			$dbgrupo= $this->db->escape($grupo);
			$ut     = $this->datasis->dameval("SELECT valor FROM utributa ORDER BY fecha DESC LIMIT 1");

			$link=anchor($this->url."lote/insert/$grupo/<#upago#>/<#tarifa#>",'Facturar Mes');
			$sel=array('COUNT(*) AS cana','CONCAT(a.upago,\'01\') AS pago','a.upago','TRIM(a.tarifa) AS tarifa','b.actividad',"SUM(b.minimo*$ut) AS monto");
			$grid = new DataGrid('Lista de efectos');
			$grid->db->select($sel);
			$grid->db->from('scli   AS a');
			$grid->db->join('tarifa AS b','a.tarifa=b.id');
			$grid->db->where('a.grupo',$grupo);
			$grid->db->where('a.upago IS NOT NULL');
			$grid->db->where('a.upago <>','');
			$grid->db->groupby('a.upago,a.tarifa');

			//$grid->column('Facturar'   );
			$grid->column_orderby('Cantidad'   , '<#cana#> '.$link ,'cana','align="right"');
			$grid->column_orderby('&Uacute;ltimo pago', '<dbdate_to_human><#pago#></dbdate_to_human>','upago','align="center"');
			$grid->column_orderby('Tarifa'     , '(<#tarifa#>) <#actividad#>','tarifa');
			$grid->column_orderby('Monto'      , '<nformat><#monto#></nformat>' ,'monto' ,'align="right"');
			$grid->build();

			$tt=$grid->output;
		}

		$data['content'] =$form->output.$tt;
		$data['head']    = script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head();
		$data['title']   = '<h1>Facturaci&oacute;n de servicio a cr&eacute;dito</h1>';
		$this->load->view('view_ventanas', $data);
	}
}
