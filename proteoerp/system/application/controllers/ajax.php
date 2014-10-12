<?php
/** ProteoERP
 *  FUNCIONES INVOCADAS POR AJAX
 *
 *
 *
 *  BUSQUEDAS
 *		PROVEEDORES 	buscasprv
 *
 *		CLIENTES	buscascli
 *
 *		INVENTARIO	buscasinv
 *				buscascstart   (Busca sinv solo articulos para compras con codigos alternos)
 *				buscasinvart   (Busca sinv solo articulos)
 *
 *		FACTURAS	buscasfacdev   (Busca facturas para aplicarles devolucion)
 *
 *		FORMAS DE PAGO	buscasfpadev   (Busca las formas de pago de una factura para devolverlos)
 *
 *		PLAN DE CUENTAS buscacpla
 *
 *
 *
 *
 *
 *
*/
class Ajax extends Controller {
	var $autolimit=50; //Limite en el autocomplete;

	function Ajax(){
		parent::Controller();
		session_write_close();
	}

	function index(){

	}

	//***************************************
	//           Auto complete
	//***************************************
	function buscasprv(){
		$mid  = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		$qdb  = $this->db->escape('%'.$mid.'%');
		$qdbi = $this->db->escape($mid.'%');
		$qmid = $this->db->escape($mid);

		$data = '[]';
		if($mid !== false){
			$retArray = $retorno = array();

			//Cheque si existe el codigo
			$mSQL="SELECT id,TRIM(nombre) AS nombre, TRIM(rif) AS rif, proveed, direc1 AS direc, reteiva
				FROM sprv WHERE proveed=${qmid} LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() == 1){
				$row = $query->row_array();
				$retArray['value']   = $row['proveed'];
				$retArray['label']   = '('.$row['rif'].') '.$this->en_utf8($row['nombre']);
				$retArray['rif']     = $row['rif'];
				$retArray['nombre']  = $this->en_utf8($row['nombre']);
				$retArray['proveed'] = $row['proveed'];
				$retArray['direc']   = $this->en_utf8($row['direc']);
				$retArray['reteiva'] = $row['reteiva'];
				$retArray['id']      = $row['id'];
				array_push($retorno, $retArray);
				$ww=" AND proveed<>${qmid}";
			}else{
				$ww='';
			}

			$mSQL="SELECT id,TRIM(nombre) AS nombre, TRIM(rif) AS rif, proveed, direc1 AS direc, reteiva
				FROM sprv WHERE (rif LIKE ${qdb} OR nombre LIKE ${qdb} OR proveed=${qdbi}) ${ww}
				ORDER BY rif LIMIT ".$this->autolimit;
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']   = $row['proveed'];
					$retArray['label']   = '('.$row['rif'].') '.$this->en_utf8($row['nombre']);
					$retArray['rif']     = $row['rif'];
					$retArray['nombre']  = $this->en_utf8($row['nombre']);
					$retArray['proveed'] = $row['proveed'];
					$retArray['direc']   = $this->en_utf8($row['direc']);
					$retArray['reteiva'] = $row['reteiva'];
					$retArray['id']      = $row['id'];
					array_push($retorno, $retArray);
				}
			}
			if(count($retorno)>0){
				$data = json_encode($retorno);
			}
		}
		echo $data;
		return true;
	}

	/**************************************************************
	 *
	 *  BUSCA LOS CHEQUES DE CLIENTES
	 *
	*/
	function buscachequecli(){
		$mid     = $this->input->post('q');
		$cod_cli = $this->input->post('cod_cli');
		if($mid == false) $mid  = $this->input->post('term');


		$data = '[ ]';
		if($mid !== false && $cod_cli !== false ){
			$qmid = $this->db->escape($mid);
			$qdb  = $this->db->escape('%'.$mid.'%');
			$mcod_cli = $this->db->escape($cod_cli);

			$retArray = $retorno = array();

			//Mira si existe el codigo
			$mSQL="SELECT id, num_ref, fecha, monto
				FROM sfpa WHERE cod_cli=${mcod_cli} AND num_ref LIKE ${qdb} AND tipo='CH'
				ORDER BY fecha DESC LIMIT ".$this->autolimit;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']   = $row['num_ref'];
					$retArray['label']   = $row['fecha'].' '.$this->en_utf8($row['num_ref'].' '.number_format($row['monto'],2));
					$retArray['fecha']   = $row['fecha'];
					$retArray['num_ref'] = $this->en_utf8($row['num_ref']);
					$retArray['monto']   = $row['monto'];
					$retArray['id']      = $row['id'];
					array_push($retorno, $retArray);
				}
			}

			if(count($data)>0)
				$data = json_encode($retorno);
		}
		echo $data;
		return true;
	}

	/**************************************************************
	 *
	 *  BUSCA LOS CHEQUES DE PROVEEDORES
	 *
	*/
	function buscachequeprv(){
		$mid     = $this->input->post('q');
		$cod_prv = $this->input->post('cod_prv');
		$codbanc = $this->input->post('codbanc');

		if($mid == false) $mid  = $this->input->post('term');


		$data = '[ ]';
		if($mid !== false && $cod_prv !== false && $codbanc !== false ){
			$qmid     = $this->db->escape($mid);
			$qdb      = $this->db->escape('%'.$mid.'%');
			$mcod_prv = $this->db->escape($cod_prv);
			$mcodbanc = $this->db->escape($codbanc);

			$retArray = $retorno = array();

			//Mira si existe el codigo
			$mSQL="SELECT id, numero, fecha, monto FROM bmov
				WHERE clipro='P' AND codcp=${mcod_prv} AND numero LIKE ${qdb} AND tipo_op='CH' AND codbanc=${codbanc}
				ORDER BY fecha DESC LIMIT ".$this->autolimit;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']   = $row['numero'];
					$retArray['label']   = $row['fecha'].' '.$this->en_utf8($row['numero'].' '.number_format($row['monto'],2));
					$retArray['fecha']   = $row['fecha'];
					$retArray['numero'] = $this->en_utf8($row['numero']);
					$retArray['monto']   = $row['monto'];
					$retArray['id']      = $row['id'];
					array_push($retorno, $retArray);
				}
			}

			if(count($data)>0)
				$data = json_encode($retorno);
		}
		echo $data;
		return true;
	}



	/**************************************************************
	 *
	 *  BUSCA LOS CLIENTES
	 *
	*/
	function buscascli(){
		$mid  = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		$qmid = $this->db->escape($mid);
		$qdb  = $this->db->escape('%'.$mid.'%');

		$data = '[ ]';
		if($mid !== false){
			$retArray = $retorno = array();
			$sel ='id,TRIM(nombre) AS nombre, TRIM(rifci) AS rifci, cliente, tipo, CONCAT(TRIM(dire11)," ",dire12) AS direc, CONCAT(TRIM(telefono)," ",telefon2) telefono, mmargen, ciudad1, estado, vendedor';

			//Mira si existe el codigo
			$mSQL="SELECT ${sel}
				FROM scli WHERE cliente=${qmid} AND tipo<>0  LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() == 1){
				$row = $query->row_array();
				$retArray['value']   = $row['cliente'];
				$retArray['label']   = '('.$row['rifci'].') '.$this->en_utf8($row['nombre']);
				$retArray['rifci']   = $row['rifci'];
				$retArray['nombre']  = $this->en_utf8($row['nombre']);
				$retArray['cod_cli'] = $row['cliente'];
				$retArray['tipo']    = $row['tipo'];
				$retArray['telef']   = trim($row['telefono']);
				$retArray['direc']   = $this->en_utf8($row['direc']);
				$retArray['desc']    = floatval($row['mmargen']);
				$retArray['telefono']= $this->en_utf8($row['telefono']);
				$retArray['ciudad']  = $this->en_utf8($row['ciudad1']);
				$retArray['estado']  = $this->en_utf8($row['estado']);
				$retArray['vendedor']= $row['vendedor'];
				$retArray['id']      = $row['id'];
				array_push($retorno, $retArray);
				$ww=" AND cliente<>${qmid}";
			}else{
				$ww='';
			}

			$mSQL="SELECT ${sel}
				FROM scli WHERE (cliente LIKE ${qdb} OR rifci LIKE ${qdb} OR nombre LIKE ${qdb}) AND tipo<>0 ${ww}
				ORDER BY rifci LIMIT ".$this->autolimit;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']   = $row['cliente'];
					$retArray['label']   = '('.$row['rifci'].') '.$this->en_utf8($row['nombre']);
					$retArray['rifci']   = $row['rifci'];
					$retArray['nombre']  = $this->en_utf8($row['nombre']);
					$retArray['cod_cli'] = $row['cliente'];
					$retArray['tipo']    = $row['tipo'];
					$retArray['telef']   = trim($row['telefono']);
					$retArray['direc']   = $this->en_utf8($row['direc']);
					$retArray['desc']    = floatval($row['mmargen']);
					$retArray['telefono']= $this->en_utf8($row['telefono']);
					$retArray['ciudad']  = $this->en_utf8($row['ciudad1']);
					$retArray['estado']  = $this->en_utf8($row['estado']);
					$retArray['vendedor']= $row['vendedor'];
					$retArray['id']      = $row['id'];
					array_push($retorno, $retArray);
				}
			}
			if(count($data)>0)
				$data = json_encode($retorno);
		}
		echo $data;
		return true;
	}

	/**************************************************************
	 *
	 *   BUSCA LOS PRINCIPIOS ACTIVOS PARA SUNDECOB
	 *
	*/
	function buscasundecob($tabla){

		$data = '[{ }]';
		if (in_array($tabla,array('dcomercial','forma','marca','material','pactivo','rubro','subrubro','unidad'))) {
			$mid  = $this->input->post('q');
			$qdb  = $this->db->escape('%'.$mid.'%');

			if($mid !== false){
				$retArray = $retorno = array();
				$mSQL="SELECT codigo, TRIM(descrip) AS descrip
					FROM sc_$tabla
					WHERE (codigo LIKE ${qdb} OR descrip LIKE ${qdb})
					ORDER BY descrip LIMIT ".$this->autolimit;
				$query = $this->db->query($mSQL);
				if ($query->num_rows() > 0){
					foreach( $query->result_array() as  $row ) {
						$retArray['value']    = $row['codigo'];
						$retArray['label']    = '('.$row['codigo'].') '.$this->en_utf8($row['descrip']);
						$retArray['descrip']  = $this->en_utf8($row['descrip']);
						array_push($retorno, $retArray);
					}
				}
				if(count($data)>0)
					$data = json_encode($retorno);
			}
		}
		echo $data;
		return true;
	}

	function buscastarifa(){
		$mid  = $this->input->post('q');
		$qdb  = $this->db->escape('%'.$mid.'%');

		$data = '[{ }]';
		if($mid !== false){
			$retArray = $retorno = array();
			$mSQL  = "SELECT minimo, actividad, id
			FROM tarifa
			WHERE actividad LIKE $qdb
			ORDER BY actividad LIMIT ".$this->autolimit;
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']    = $row['id'];
					$retArray['minimo']   = $row['minimo'];
					$retArray['actividad']= $this->en_utf8($row['actividad']);
					$retArray['label']    = $this->en_utf8($row['actividad'].' ( '.$row['minimo'].')');
					array_push($retorno, $retArray);
				}
			}
			if(count($data)>0)
				$data = json_encode($retorno);
		}
		echo $data;
		return true;
	}

	/**************************************************************
	 *
	 *  BUSCA LOS CLIENTES PARA COBRO DE SERVICIO
	 *
	*/
	function buscascliser(){
		$mid  = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		$qmid = $this->db->escape($mid);
		$qdb  = $this->db->escape('%'.$mid.'%');

		$ut = $this->datasis->dameval("SELECT valor FROM utributa ORDER BY fecha DESC LIMIT 1");

		$data = '[ ]';
		if($mid !== false){
			$retArray = $retorno = array();
			$mSQL="SELECT TRIM(a.nombre) AS nombre, TRIM(a.rifci) AS rifci, a.cliente, a.tipo , a.dire11 AS direc,
				IF(a.tarimonto>0,ROUND(a.tarimonto*$ut,2), ROUND(b.minimo*$ut,2)) precio1, a.upago, a.telefono, b.id codigo,
				IF(a.tarimonto>0,a.tarimonto,b.minimo) AS utribu,b.tipo AS taritipo
				FROM scli AS a
				JOIN tarifa AS b ON a.tarifa=b.id
				WHERE (cliente LIKE ${qdb} OR rifci LIKE ${qdb} OR nombre LIKE ${qdb})
				ORDER BY rifci LIMIT ".$this->autolimit;
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$dt1 = DateTime::createFromFormat('Ymd',$row['upago'].'01');
					$dt2 = new DateTime();
					$interval = $dt1->diff($dt2);

					$retArray['value']    = $row['cliente'];
					$retArray['label']    = '('.$row['rifci'].') '.$this->en_utf8($row['nombre']);
					$retArray['rifci']    = $row['rifci'];
					$retArray['nombre']   = $this->en_utf8($row['nombre']);
					$retArray['cod_cli']  = $this->en_utf8($row['cliente']);
					$retArray['codigo']   = $row['codigo'];
					$retArray['tipo']     = $row['tipo'];
					$retArray['precio1']  = $row['precio1'];
					$retArray['telefono'] = $row['telefono'];
					$retArray['upago']    = $row['upago'];
					$retArray['utribu']   = $row['utribu'];
					$retArray['taritipo'] = $row['taritipo'];
					$retArray['direc']    = $this->en_utf8($row['direc']);
					$retArray['cana']     = $interval->format('%m')+$interval->format('%Y')*12;
					array_push($retorno, $retArray);
				}
			}
			if(count($data)>0)
				$data = json_encode($retorno);
		}
		echo $data;
		return true;
	}

	/**************************************************************
	 *
	 *  BUSCA LAS RUTAS DE VAQUERAS
	 *
	*/
	function buscalruta(){
		$mid  = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		$fecha = $this->input->post('fecha');
		if($fecha == false){
			$fecha=date('Y-m-d');
		}else{
			$edate   = explode('/',$fecha);
			$fecha   = date('Y-m-d',mktime(0, 0, 0, $edate[1],$edate[0],$edate[2]));
		}

		$qmid  = $this->db->escape($mid);
		$qdb   = $this->db->escape('%'.$mid.'%');
		$qfecha =$this->db->escape($fecha);

		$data = '[ ]';
		if($mid !== false){
			$retArray = $retorno = array();
			$mSQL="SELECT DISTINCT TRIM(a.nombre) AS nombre, TRIM(a.codigo) AS codigo
				FROM lruta AS a
				JOIN lrece AS b ON a.codigo=b.ruta AND b.fecha=${qfecha}
				WHERE (a.codigo LIKE ${qdb} OR a.nombre LIKE ${qdb})
				ORDER BY a.nombre LIMIT ".$this->autolimit;
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']    = $this->en_utf8($row['codigo']);
					$retArray['label']    = $this->en_utf8('('.$row['codigo'].') '.$row['nombre']);
					$retArray['nombre']   = $this->en_utf8($row['nombre']);
					array_push($retorno, $retArray);
				}
			}
			if(count($data)>0)
				$data = json_encode($retorno);
		}
		echo $data;
		return true;
	}

	/**************************************************************
	 *
	 *  BUSCA LAS VAQUERAS
	 *
	*/
	function buscalvaca(){
		$mid  = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		$qmid = $this->db->escape($mid);
		$qdb  = $this->db->escape('%'.$mid.'%');

		$data = '[ ]';
		if($mid !== false){
			$retArray = $retorno = array();
			$mSQL="SELECT a.id,TRIM(a.codigo) AS codigo, TRIM(a.nombre) AS nombre
				FROM lvaca AS a
				WHERE (a.codigo LIKE ${qdb} OR a.nombre LIKE ${qdb})
				ORDER BY a.nombre LIMIT ".$this->autolimit;
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']    = $this->en_utf8($row['codigo']);
					$retArray['label']    = $this->en_utf8('('.$row['codigo'].') '.$row['nombre']);
					$retArray['nombre']   = $this->en_utf8($row['nombre']);
					$retArray['id']       = $row['id'];
					array_push($retorno, $retArray);
				}
			}
			if(count($data)>0)
				$data = json_encode($retorno);
		}
		echo $data;
		return true;
	}

	/**************************************************************
	 *
	 *  BUSCA LOS INVENTARIO
	 *
	*/
	function buscasinv(){
		$comodin= $this->datasis->traevalor('COMODIN');
		$mid    = $this->input->post('q');
		$alma   = $this->input->post('alma');
		$vnega  = trim(strtoupper($this->datasis->traevalor('VENTANEGATIVA')));

		if($vnega=='N'){
			$wvnega=' AND IF(MID(a.tipo,1,1) IN ("S","C"),1,e.existen>0) ';
		}else{
			$wvnega='';
		}

		if($mid == false) $mid = $this->input->post('term');

		if(strlen($comodin)==1 && $comodin!='%' && $mid!==false){
			$mid=str_replace($comodin,'%',$mid);
		}
		$qdb  = $this->db->escape($mid.'%');
		$qba  = $this->db->escape($mid);

		$data = '[]';

		if($mid !== false){
			$vdescu=$this->datasis->traevalor('DESCUFIJO');
			if($vdescu=='S') $colnom='a.descufijo'; else $colnom='0';
			if($alma === false){
				//Vemos si aplica descuento solo promocional
				if($this->db->table_exists('sinvpromo')){
					$mSQL="
					SELECT DISTINCT TRIM(a.descrip) AS descrip, TRIM(a.codigo) AS codigo, a.unidad,
					a.precio1,precio2,precio3,precio4, a.iva,a.existen,a.tipo,a.peso, a.ultimo, a.pond, a.barras, ${colnom} AS descufijo,c.margen AS dgrupo,d.margen AS promo, a.existen, a.marca, a.ubica
					FROM sinv AS a
					LEFT JOIN barraspos AS b ON a.codigo=b.codigo
					LEFT JOIN grup AS c ON a.grupo=c.grupo
					LEFT JOIN sinvpromo AS d ON a.codigo=d.codigo
					WHERE (a.codigo LIKE ${qdb} OR a.descrip LIKE  ${qdb} OR a.barras LIKE ${qdb} OR b.suplemen=${qba}) AND a.activo='S'
					ORDER BY a.descrip LIMIT ".$this->autolimit;
				}else{
					$mSQL="
					SELECT DISTINCT TRIM(a.descrip) AS descrip, TRIM(a.codigo) AS codigo, a.unidad,
					a.precio1,precio2,precio3,precio4, a.iva,a.existen,a.tipo,a.peso, a.ultimo, a.pond, a.barras, ${colnom} AS descufijo, c.margen AS dgrupo,0 AS promo, a.existen, a.marca, a.ubica
					FROM sinv AS a
					LEFT JOIN barraspos AS b ON a.codigo=b.codigo
					LEFT JOIN grup AS c ON a.grupo=c.grupo
					WHERE (a.codigo LIKE ${qdb} OR a.descrip LIKE  ${qdb} OR a.barras LIKE ${qdb} OR b.suplemen=${qba}) AND a.activo='S'
					ORDER BY a.descrip LIMIT ".$this->autolimit;
				}
			}else{
				$almadb  = $this->db->escape($alma);
				if($this->db->table_exists('sinvpromo')){
					$mSQL="
					SELECT DISTINCT TRIM(a.descrip) descrip, TRIM(a.codigo) codigo, a.marca, a.ubica, a.unidad,
					a.precio1,precio2,precio3,precio4, a.iva,a.existen,a.tipo,a.peso, a.ultimo, a.pond, a.barras, ${colnom} AS descufijo,c.margen AS dgrupo,d.margen AS promo, COALESCE(e.existen,0) existen
					FROM sinv AS a
					LEFT JOIN barraspos AS b ON a.codigo=b.codigo
					LEFT JOIN grup      AS c ON a.grupo=c.grupo
					LEFT JOIN sinvpromo AS d ON a.codigo=d.codigo
					LEFT JOIN itsinv    AS e ON a.codigo=e.codigo AND e.alma=${almadb}
					WHERE (a.codigo LIKE ${qdb} OR a.descrip LIKE  ${qdb} OR a.barras LIKE ${qdb} OR b.suplemen=${qba}) AND a.activo='S' ${wvnega}
					ORDER BY a.descrip LIMIT ".$this->autolimit;
				}else{
					$mSQL="
					SELECT DISTINCT TRIM(a.descrip) descrip, TRIM(a.codigo) codigo, a.marca, a.ubica, a.unidad,
					a.precio1,precio2,precio3,precio4, a.iva,a.existen,a.tipo,a.peso, a.ultimo, a.pond, a.barras, ${colnom} AS descufijo, c.margen AS dgrupo,0 AS promo, COALESCE(e.existen,0) existen
					FROM sinv AS a
					LEFT JOIN barraspos AS b ON a.codigo=b.codigo
					LEFT JOIN grup      AS c ON a.grupo=c.grupo
					LEFT JOIN itsinv    AS e ON a.codigo=e.codigo AND e.alma=${almadb}
					WHERE (a.codigo LIKE ${qdb} OR a.descrip LIKE  ${qdb} OR a.barras LIKE ${qdb} OR b.suplemen=${qba}) AND a.activo='S' ${wvnega}
					ORDER BY a.descrip LIMIT ".$this->autolimit;
				}
			}
			//Fin del descuento promocional

			$retArray = $retorno = array();
			$cana=1;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {

					if($row['descufijo']>0){
						$descufijo=$row['descufijo']/100;
					}elseif($row['promo']>0){
						$descufijo=$row['promo']/100;
					}elseif($row['dgrupo']>0){
						$descufijo=$row['dgrupo']/100;
					}else{
						$descufijo = 0;
					}
					if($descufijo>1) $descufijo = 0;

					$retArray['label']   = '('.$row['codigo'].')'.$this->en_utf8($row['descrip']).' Bs.'.$row['precio1'].'  '.$row['existen'].'';
					$retArray['value']   = $row['codigo'];
					$retArray['codigo']  = $row['codigo'];
					$retArray['cana']    = $cana;
					$retArray['tipo']    = $row['tipo'];
					$retArray['peso']    = $row['peso'];
					$retArray['ultimo']  = $row['ultimo'];
					$retArray['pond']    = $row['pond'];
					$retArray['base1']   = round(($row['precio1']*100/(100+$row['iva']))*(1-$descufijo),2);
					$retArray['base2']   = round($row['precio2']*100/(100+$row['iva']),2);
					$retArray['base3']   = round($row['precio3']*100/(100+$row['iva']),2);
					$retArray['base4']   = round($row['precio4']*100/(100+$row['iva']),2);
					$retArray['descrip'] = $this->en_utf8($row['descrip']);
					$retArray['barras']  = $row['barras'];
					//$retArray['descrip'] = wordwrap($row['descrip'], 25, '<br />');
					$retArray['iva']     = $row['iva'];
					$retArray['existen'] = (empty($row['existen']))? 0 : round($row['existen'],2);
					$retArray['marca']   = $row['marca'];
					$retArray['ubica']   = $row['ubica'];
					$retArray['unidad']  = $row['unidad'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}


	/**************************************************************
	 *
	 *  BUSCA LOS INVENTARIO DESDE AFUERA DE WP
	 *
	*/
	function buscasinvex(){
		$comodin= $this->datasis->traevalor('COMODIN');

		$mid    = $this->uri->segment($this->uri->total_segments());

		if($mid == false) {
			echo 'Consulta Vacia';
			return;
		}

		if(strlen($comodin)==1 && $comodin!='%' && $mid!==false){
			$mid=str_replace($comodin,'%',$mid);
		}
		$qdb  = $this->db->escape($mid.'%');
		$qba  = $this->db->escape($mid);

		$data = '[]';
		if($mid !== false){

			// Busca
			$mSQL="
			SELECT DISTINCT TRIM(a.descrip) AS descrip, TRIM(a.codigo) AS codigo,
			a.precio1, precio2, precio3, precio4, a.iva, if(a.existen>=0,a.existen,0) existen,
			a.tipo,a.peso, a.ultimo, a.pond, a.barras, a.marca, a.modelo
			FROM sinv AS a
			WHERE (a.codigo LIKE ${qdb} OR a.descrip LIKE  ${qdb} OR a.barras LIKE ${qdb}) AND a.activo='S'
			ORDER BY if(a.existen>0,0,1), a.descrip LIMIT 30";

			$retArray = $retorno = array();

			$cana=1;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {

					$retArray['codigo']  = $row['codigo'];
					$retArray['tipo']    = $row['tipo'];
					$retArray['peso']    = $row['peso'];
					$retArray['ultimo']  = $row['ultimo'];
					$retArray['pond']    = $row['pond'];
					$retArray['base1']   = round($row['precio1']*100/(100+$row['iva']),2);
					$retArray['base2']   = round($row['precio2']*100/(100+$row['iva']),2);
					$retArray['base3']   = round($row['precio3']*100/(100+$row['iva']),2);
					$retArray['base4']   = round($row['precio4']*100/(100+$row['iva']),2);
					$retArray['descrip'] = $this->en_utf8($row['descrip']);
					$retArray['barras']  = $row['barras'];
					$retArray['iva']     = $row['iva'];
					$retArray['existen'] = $row['existen'];
					$retArray['marca']   = $row['marca'];
					$retArray['modelo']  = $row['modelo'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}




	/**************************************************************
	 *
	 *  BUSCA LOS INVENTARIO
	 *
	*/
	function buscarnoti(){
		$comodin= $this->datasis->traevalor('COMODIN');
		$mid    = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		if(strlen($comodin)==1 && $comodin!='%' && $mid!==false){
			$mid=str_replace($comodin,'%',$mid);
		}
		$qdb  = $this->db->escape($mid.'%');
		$qba  = $this->db->escape($mid);

		$data = '[]';
		if($mid !== false){
			$retArray = $retorno = array();

			$mSQL="
				SELECT DISTINCT a.id AS numero, a.serial AS codigo,
				a.precio1,precio2,precio3,precio4, a.iva,a.existen,a.tipo,a.peso, a.ultimo, a.pond, a.barras
				FROM rnoti AS a
				WHERE (a.id LIKE $qdb OR a.nomcliente LIKE  $qdb OR a.serial=$qba) a.estado<>'ENTREGADO'
				ORDER BY a.id LIMIT ".$this->autolimit;
			$cana=1;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']   = '('.$row['codigo'].')'.$this->en_utf8($row['descrip']).' Bs.'.$row['precio1'].'  '.$row['existen'].'';
					$retArray['value']   = $row['codigo'];
					$retArray['codigo']  = $row['codigo'];
					$retArray['cana']    = $cana;
					$retArray['tipo']    = $row['tipo'];
					$retArray['peso']    = $row['peso'];
					$retArray['ultimo']  = $row['ultimo'];
					$retArray['pond']    = $row['pond'];
					$retArray['base1']   = round($row['precio1']*100/(100+$row['iva']),2);
					$retArray['base2']   = round($row['precio2']*100/(100+$row['iva']),2);
					$retArray['base3']   = round($row['precio3']*100/(100+$row['iva']),2);
					$retArray['base4']   = round($row['precio4']*100/(100+$row['iva']),2);
					$retArray['descrip'] = $this->en_utf8($row['descrip']);
					$retArray['barras']  = $row['barras'];
					//$retArray['descrip'] = wordwrap($row['descrip'], 25, '<br />');
					$retArray['iva']     = $row['iva'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//Trae el precio 1 para las tarifas de estacionamiento
	function buscaprecio1(){
		$mid  = $this->input->post('q');

		$data=array(0.0,0.0);
		if($mid !== false){
			$dbmid = $this->db->escape($mid);
			$retArray = $retorno = array();
			$mSQL="SELECT base1,base2,iva FROM sinv WHERE codigo=${dbmid} LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				$row  = $query->row();
				$data = array($row->base1,$row->base2,$row->iva);
			}
		}
		echo json_encode($data);
		return true;
	}

	//Busca los transportistas para las recepciones
	function lrecetrans(){
		$mid    = $this->input->post('q');

		$data = '[]';
		if($mid !== false && preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/', $mid)){
			$date    = DateTime::createFromFormat('d/m/Y', $mid);
			$dbfecha = $this->db->escape($date->format('Y-m-d'));

			$dbmid = $this->db->escape($mid);
			$retArray = $retorno = array();

			$mSQL="SELECT id, CONCAT(ruta, ' ', nombre) AS val
			FROM lrece
			WHERE fechal=${dbfecha} AND MID(ruta,1,1)='G'
			ORDER BY nombre";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']    = $this->en_utf8(trim($row['val']));
					$retArray['value']    = $row['id'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//******************************************************************
	//Busca los efectos para ser conciliados
	function buscaconci(){
		$mid  = $this->input->post('codbanc');
		$mid2 = $this->input->post('fecha');

		$data = '[]';
		if($mid !== false && preg_match("/^(0[1-9]|1[012])\/[0-9]{4}$/", $mid2)){

			$edate   = explode('/',$mid2);
			$fecha   = date('Y-m-d',mktime(0, 0, 0, $edate[0]+1,0,$edate[1]));
			$dbfecha = $this->db->escape($fecha);

			$dbmid = $this->db->escape($mid);
			$retArray = $retorno = array();

			$mSQL="SELECT id,numero, tipo_op AS tipo, fecha, monto,concilia
			FROM bmov
			WHERE codbanc=${dbmid} AND anulado<>'S' AND liable<>'N'
			AND fecha <= ${dbfecha}
			AND (concilia='0000-00-00' OR concilia IS NULL OR concilia=${dbfecha})
			ORDER BY fecha, numero";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$objdate = date_create($row['fecha']);

					$retArray['id']      = $row['id'];
					$retArray['numero']  = $row['numero'];
					$retArray['tipo']    = $row['tipo'];
					$retArray['fecha']   = $objdate->format('d/m/Y');
					$retArray['monto']   = $row['monto'];
					$retArray['concilia']= (empty($row['concilia']) || $row['concilia']=='0000-00-00')? false : true;

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}
		}
		echo $data;
	}

	//******************************************************************
	//Busca icon
	function buscaicon(){
		$comodin= $this->datasis->traevalor('COMODIN');
		$mid    = $this->input->post('q');
		if(strlen($comodin)==1 && $comodin!='%' && $mid!==false){
			$mid=str_replace($comodin,'%',$mid);
		}
		$qdb    = $this->db->escape('%'.$mid.'%');
		$qba    = $this->db->escape($mid);
		$tipo   = $this->input->post('tipo');

		$data = '[]';
		if($mid !== false && $tipo!==false){
			if($tipo=='E'){
				$tipo='I';
			}elseif($tipo=='S'){
				$tipo='E';
			}else{
				echo $data;
				return;
			}

			$dbtipo = $this->db->escape($tipo);
			$retArray = $retorno = array();

			$mSQL="SELECT TRIM(a.codigo) AS codigo, a.concepto
				FROM icon AS a
				WHERE (a.codigo LIKE ${qdb} OR a.concepto LIKE  ${qdb}) AND tipo=${dbtipo}
				ORDER BY a.concepto LIMIT ".$this->autolimit;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']    = $this->en_utf8('('.$row['codigo'].') '.trim($row['concepto']));
					$retArray['value']    = $row['codigo'];
					$retArray['concepto'] = $this->en_utf8(trim($row['concepto']));
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//Busca sinv solo articulos para compras con codigos alternos
	function buscascstart(){
		$comodin= $this->datasis->traevalor('COMODIN');
		$mid    = $this->input->post('q');
		if(strlen($comodin)==1 && $comodin!='%' && $mid!==false){
			$mid=str_replace($comodin,'%',$mid);
		}
		$qdb    = $this->db->escape($mid.'%');
		$qba    = $this->db->escape($mid);
		$sprv   = $this->input->post('sprv');

		$data = '[{ }]';
		if($mid !== false){
			$retArray = $retorno = array();

			$mSQL="SELECT DISTINCT TRIM(a.descrip) AS descrip, TRIM(a.codigo) AS codigo, a.precio1,precio2,precio3,precio4, a.iva,a.existen,a.tipo
				,a.peso, a.ultimo, a.pond,a.activo
				FROM sinv AS a
				LEFT JOIN barraspos AS b ON a.codigo=b.codigo ";
			if(!empty($sprv)){
				$dbsprv = $this->db->escape($sprv);
				$mSQL.="LEFT JOIN sinvprov  AS c ON c.proveed=${dbsprv} AND c.codigo=a.codigo";
				$ww = 'OR c.codigop='.$qdb;
			}else{
				$ww ='';
			}
			$mSQL.=" WHERE (a.codigo LIKE ${qdb} OR a.descrip LIKE  ${qdb} OR a.barras LIKE ${qdb} OR b.suplemen=${qba} OR a.alterno LIKE ${qba} ${ww}) AND a.tipo='Articulo'
				ORDER BY a.descrip LIMIT ".$this->autolimit;
			$cana=1;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']   = $this->en_utf8('('.$row['codigo'].') '.$row['descrip'].' '.$row['precio1'].' Bs. - '.$row['existen']);
					$retArray['value']   = $this->en_utf8($row['codigo']);
					$retArray['codigo']  = $this->en_utf8($row['codigo']);
					$retArray['cana']    = $cana;
					$retArray['tipo']    = $row['tipo'];
					$retArray['peso']    = $row['peso'];
					$retArray['ultimo']  = $row['ultimo'];
					$retArray['pond']    = $row['pond'];
					$retArray['existen'] = floatval($row['existen']);
					$retArray['base1']   = $row['precio1']*100/(100+$row['iva']);
					$retArray['base2']   = $row['precio2']*100/(100+$row['iva']);
					$retArray['base3']   = $row['precio3']*100/(100+$row['iva']);
					$retArray['base4']   = $row['precio4']*100/(100+$row['iva']);
					$retArray['descrip'] = $this->en_utf8($row['descrip']);
					//$retArray['descrip'] = wordwrap($row['descrip'], 25, '<br />');
					$retArray['iva']     = $row['iva'];
					$retArray['activo']  = $row['activo'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//Busca sinv solo articulos
	function buscasinvart($activo='S',$chneg='N'){
		$alma   = $this->input->post('alma');

		if($activo=='S'){
			$activo=' AND a.activo=\'S\'';
		}else{
			$activo='';
		}

		$comodin= $this->datasis->traevalor('COMODIN');
		$mid    = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		if(strlen($comodin)==1 && $comodin!='%' && $mid!==false){
			$mid=str_replace($comodin,'%',$mid);
		}
		$qdb  = $this->db->escape($mid.'%');
		$qba  = $this->db->escape($mid);

		$data = '[]';
		if($mid !== false){
			$retArray = $retorno = array();

			if(empty($alma)){
				$mcana='a.existen';
				$mjoin='';
			}else{
				$mcana='c.existen';
				$mjoin='LEFT JOIN itsinv AS c ON a.codigo=c.codigo AND c.alma='.$this->db->escape($alma);
			}

			if($chneg!='S'){
				$vnega  = trim(strtoupper($this->datasis->traevalor('VENTANEGATIVA')));
				if($vnega=='N'){
					$wvnega=" AND ${mcana}>0 ";
				}else{
					$wvnega='';
				}
			}else{
				$wvnega='';
			}

			$mSQL="SELECT DISTINCT TRIM(a.descrip) AS descrip, TRIM(a.codigo) AS codigo, a.precio1,precio2,precio3,precio4, a.iva,${mcana},a.tipo
				,a.peso, a.ultimo, a.pond
				FROM sinv AS a
				LEFT JOIN barraspos AS b ON a.codigo=b.codigo
				${mjoin}
				WHERE (a.codigo LIKE ${qdb} OR a.descrip LIKE  ${qdb} OR a.barras LIKE ${qdb} OR b.suplemen=${qba} OR a.alterno LIKE ${qba})
					${activo} AND a.tipo='Articulo' ${wvnega}
				ORDER BY a.descrip LIMIT ".$this->autolimit;
			$cana=1;

			$query = $this->db->query($mSQL);
			if($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']   = '('.$this->en_utf8($row['codigo']).') '.$this->en_utf8($row['descrip']).' '.$row['precio1'].' Bs. - '.round($row['existen'],2);
					$retArray['value']   = $this->en_utf8($row['codigo']);
					$retArray['codigo']  = $this->en_utf8($row['codigo']);
					$retArray['cana']    = $cana;
					$retArray['tipo']    = $row['tipo'];
					$retArray['peso']    = floatval($row['peso']);
					$retArray['ultimo']  = $row['ultimo'];
					$retArray['pond']    = $row['pond'];
					$retArray['existen'] = (empty($row['existen']))? 0 : round($row['existen'],2);
					$retArray['base1']   = round($row['precio1']*100/(100+$row['iva']),2);
					$retArray['base2']   = round($row['precio2']*100/(100+$row['iva']),2);
					$retArray['base3']   = round($row['precio3']*100/(100+$row['iva']),2);
					$retArray['base4']   = round($row['precio4']*100/(100+$row['iva']),2);
					$retArray['descrip'] = $this->en_utf8($row['descrip']);
					//$retArray['descrip'] = wordwrap($row['descrip'], 25, '<br />');
					$retArray['iva']     = $row['iva'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	function ordpart(){
		$ordp  = $this->input->post('ordp');
		$esta  = $this->input->post('esta');
		$tipo  = $this->input->post('tipo');

		$data = '[]';
		if($ordp !== false &&  $esta !== false &&  $tipo!== false){
			$dbnumero=$this->db->escape($ordp);
			if($tipo=='E'){
				$mSQL="SELECT c.codigo,COALESCE(b.descrip,c.descrip) AS descrip
				,SUM(COALESCE(b.cantidad*IF(tipoordp='E',-1,1),0)) AS tracana
				,c.cantidad
				FROM stra AS a
				JOIN itstra AS b ON a.numero=b.numero
				RIGHT JOIN ordpitem AS c ON a.ordp=c.numero AND b.codigo=c.codigo
				WHERE c.numero=${dbnumero}
				GROUP BY c.codigo";

				$retArray=$retorno=array();
				$query = $this->db->query($mSQL);
				if($query->num_rows() > 0){
					foreach( $query->result_array() as  $row ) {
						$cana=$row['cantidad']+$row['tracana'];
						if($cana>0){
							$retArray['codigo']   = trim($row['codigo']);
							$retArray['cantidad'] = $cana;
							$retArray['descrip']  = $this->en_utf8(trim($row['descrip']));

							array_push($retorno, $retArray);
						}
					}
					$data = json_encode($retorno);
				}
			}else{
				$dbesta=$this->db->escape($esta);
				$mSQL="SELECT b.codigo, b.descrip
				SUM(b.cantidad*IF(a.tipoordp='E',1,-1)) AS cantidad
				FROM stra AS a
				JOIN itstra AS b ON a.numero=b.numero
				WHERE a.ordp=${dbnumero} AND a.esta=${dbesta}
				GROUP BY b.codigo";

				$retArray=$retorno=array();
				$query = $this->db->query($mSQL);
				if ($query->num_rows() > 0){
					foreach( $query->result_array() as  $row ) {
						$cana=$row['cantidad'];
						if($cana>0){
							$retArray['codigo']   = trim($row['codigo']);
							$retArray['cantidad'] = $cana;
							$retArray['descrip']  = $this->en_utf8(trim($row['descrip']));

							array_push($retorno, $retArray);
						}
					}
					$data = json_encode($retorno);
				}
			}
			echo $data;
		}

	}

	//******************************************************************
	// Chequea que el gasto no este duplicado
	//
	function gserdupli(){
		$proveed = $this->input->post('proveed');
		$tipo_doc= $this->input->post('tipo_doc');
		$numero  = $this->input->post('numero');
		//$rt=array('status'=>'B','control'=>'','fecha'=>'','serie'=>'','monto'=>0,'nfiscal'=>'','tipo_doc'=>'');
		$rt=array('status'=>'B');

		if($proveed!==false && $numero !== false && $tipo_doc!== false){
			$dbproveed = $this->db->escape(trim($proveed));
			$dbnumero  = $this->db->escape(trim($numero ));
			$dbtipo_doc= $this->db->escape(trim($tipo_doc));
			$row = $this->datasis->damerow("SELECT totbruto AS monto,tipo_doc,fecha,serie,nfiscal FROM gser WHERE tipo_doc<>'XX' AND tipo_doc=${dbtipo_doc} AND proveed=${dbproveed} AND (numero=${dbnumero}  OR serie=${dbnumero})");
			if(!empty($row)){
				$rt['status']   = 'A';
				$rt['control']  = $row['nfiscal'];
				$rt['fecha']    = $this->_datehuman($row['fecha']);
				$rt['serie']    = $row['serie'];
				$rt['monto']    = $row['monto'];
				$rt['nfiscal']  = $row['nfiscal'];
				//$rt['tipo_doc'] = $row['tipo_doc'];
			}
		}
		echo json_encode($rt);
	}

	//******************************************************************
	// Chequea que el gasto no este duplicado
	//
	function scstdupli(){
		$proveed = $this->input->post('proveed');
		$tipo_doc= $this->input->post('tipo_doc');
		$numero  = $this->input->post('numero');
		$rt=array('status'=>'B');

		if($proveed!==false && $numero !== false && $tipo_doc!== false){
			$dbproveed = $this->db->escape(trim($proveed));
			$dbnumero  = $this->db->escape(trim($numero ));
			$dbtipo_doc= $this->db->escape(trim($tipo_doc));
			$row = $this->datasis->damerow("SELECT montonet AS monto,tipo_doc,fecha,serie,nfiscal FROM scst WHERE tipo_doc<>'XX' AND tipo_doc=${dbtipo_doc} AND proveed=${dbproveed} AND (numero=${dbnumero}  OR serie=${dbnumero})");
			if(!empty($row)){
				$rt['status']   = 'A';
				$rt['control']  = $row['nfiscal'];
				$rt['fecha']    = $this->_datehuman($row['fecha']);
				$rt['serie']    = $row['serie'];
				$rt['monto']    = $row['monto'];
				$rt['nfiscal']  = $row['nfiscal'];
				//$rt['tipo_doc'] = $row['tipo_doc'];
			}
		}
		echo json_encode($rt);
	}


	//******************************************************************
	//Busca facturas para aplicarles devolucion o nota de despacho
	//
	function buscasfacdev(){
		$mid   = $this->input->post('q');
		$data = '[{ }]';
		if($mid !== false){
			//$scli  = $this->input->post('scli');
			//$sclidb= $this->db->escape($scli);
			if($mid[0]!='0'){
				$mid=str_pad($mid, 8, '0', STR_PAD_LEFT);
			}

			if(strlen($mid)<8){
				$mid=$mid.'%';
			}
			$qdb   = $this->db->escape($mid);

			$retArray = $retorno = array();

			$mSQL="SELECT a.numero, a.totalg, a.cod_cli, TRIM(b.nombre) AS nombre, TRIM(b.rifci) AS rifci, b.tipo, b.dire11 AS direc,a.fecha,vd
				FROM  sfac AS a
				JOIN scli AS b ON a.cod_cli=b.cliente
				WHERE a.numero LIKE ${qdb} AND a.tipo_doc='F' AND MID(a.numero,1,1)<>'_'
				ORDER BY numero DESC LIMIT ".$this->autolimit;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']   = $this->en_utf8($row['numero'].'-'.$row['nombre'].' '.$row['totalg'].' Bs.');
					$retArray['value']   = $row['numero'];
					$retArray['cod_cli'] = $row['cod_cli'];
					$retArray['rifci']   = $row['rifci'];
					$retArray['fecha']   = $this->_datehuman($row['fecha']);
					$retArray['tipo']    = $row['tipo'];
					$retArray['direc']   = $this->en_utf8($row['direc']);
					$retArray['nombre']  = $this->en_utf8($row['nombre']);
					$retArray['totalg']  = $row['totalg'];
					$retArray['vd']      = $row['vd'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}else{
				$retArray[0]['label']   = 'No se consiguieron facturas para aplicar';
				$retArray[0]['value']   = '';
				$retArray[0]['cod_cli'] = '';
				$retArray[0]['rifci']   = '';
				$retArray[0]['fecha']   = '';
				$retArray[0]['tipo']    = '';
				$retArray[0]['nombre']  = '';
				$retArray[0]['direc']   = '';
				$retArray['vd']         = '';
				$data = json_encode($retArray);
			}
		}
		echo $data;
	}

	//******************************************************************
	//Buscar factura de compra para devolver
	//
	function buscascstdev(){
		$mid   = $this->input->post('q');
		$sprv  = $this->input->post('sprv');
		$data = '[ ]';
		if($mid !== false && !empty($sprv)){

			$dbsprv= $this->db->escape($sprv);
			$qdb   = $this->db->escape($mid.'%');

			$retArray = $retorno = array();

			$mSQL="SELECT a.numero, a.montonet AS totalg,a.nombre, TRIM(b.nombre) AS nombre, TRIM(b.rif) AS rif,a.fecha,control,b.reteiva
				FROM  scst AS a
				JOIN sprv AS b ON a.proveed=b.proveed
				WHERE a.serie LIKE ${qdb} AND a.tipo_doc='FC' AND a.proveed=${dbsprv}
				ORDER BY numero DESC LIMIT ".$this->autolimit;

			$query = $this->db->query($mSQL);
			if($query->num_rows() > 0){
				if(date('d')<=15){
					$pdia  ='01';
					$dia   ='15';
				}else{
					$pdia  ='16';
					$dia   =date('d', mktime(0, 0, 0, date('n'), 0));
				}
				$fechai =date('Ym'.$pdia);
				$fechac =date('Ym'.$dia );

				foreach( $query->result_array() as  $row ) {
					$fecha  = str_replace('-','',$row['fecha']);
					$aplrete= $fecha>=$fechai && $fecha<=$fechac;

					$retArray['label']   = $this->en_utf8($row['numero'].'-'.$row['nombre'].' '.$this->_datehuman($row['fecha']).' '.$row['totalg'].' Bs.');
					$retArray['value']   = $row['numero'];
					$retArray['control'] = $row['control'];
					$retArray['rif']     = $row['rif'];
					$retArray['reteiva'] = ($aplrete)? floatval($row['reteiva']) : 0;
					$retArray['fecha']   = $this->_datehuman($row['fecha']);
					$retArray['msj']     = null;
					$retArray['aplrete'] = intval($aplrete);

					$contribu= $this->datasis->traevalor('CONTRIBUYENTE');
					$rif     = $this->datasis->traevalor('RIF');
					if($contribu=='ESPECIAL' && strtoupper($rif[0])!='V'){
						$retArray['msj'] = ($aplrete)? null :'No se realizara la retención de impuesto por estar fuera de período '.date($pdia.'/m/Y').' - '.date($dia.'/m/Y');
					}

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}else{
				$retArray[0]['label']   = 'No se consiguieron facturas';
				$retArray[0]['value']   = '';
				$retArray[0]['control'] = '';
				$retArray[0]['rif']     = '';
				$retArray[0]['fecha']   = '';
				$retArray[0]['msj']     = null;
				$retArray[0]['aplrete'] = 1;
				$data = json_encode($retArray);
			}
		}else{
			$retArray[0]['label']   = 'Por favor seleccione un proveedor primero';
			$retArray[0]['value']   = '';
			$retArray[0]['control'] = '';
			$retArray[0]['rif']     = '';
			$retArray[0]['fecha']   = '';
			$retArray[0]['msj']     = null;
			$retArray[0]['aplrete'] = 1;
			$data = json_encode($retArray);
		}
		echo $data;
	}

	//******************************************************************
	//Busca las formas de pago de una factura para devolverlos
	//
	function buscasfpadev(){
		$mid = $this->input->post('q');

		$data = '[{ }]';
		if($mid !== false){
			$dbfactura = $this->db->escape($mid);
			$referen   = $this->datasis->dameval('SELECT referen FROM sfac WHERE tipo_doc=\'F\' AND numero='.$dbfactura);
			$retArray = $retorno = array();
			$mSQL="SELECT SUM(ROUND((bb.cana-bb.dev)*preca*(1+bb.iva/100),2)) AS monto FROM (
				SELECT aa.cana,SUM(COALESCE(d.cana,0)) AS dev,aa.codigo,aa.iva,aa.preca
				FROM (SELECT SUM(b.cana) AS cana,TRIM(a.codigo) AS codigo,a.iva,b.preca,b.numa
				FROM sinv AS a
				JOIN sitems AS b ON a.codigo=b.codigoa
				WHERE b.numa=$dbfactura AND b.tipoa='F'
				GROUP BY b.codigoa,b.preca) AS aa
				LEFT JOIN sfac   AS c  ON aa.numa=c.factura AND c.tipo_doc='D'
				LEFT JOIN sitems AS d ON c.numero=d.numa AND c.tipo_doc=d.tipoa AND aa.codigo=d.codigoa AND aa.preca=d.preca
				GROUP BY aa.codigo,aa.preca
				) AS bb";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $id=>$row ) {
					$retArray['tipo']    = ($referen=='C')? '' : 'EF';
					$retArray['monto']   = round($row['monto'],2);
					$retArray['num_ref'] = '';
					$retArray['banco']   = '';
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//******************************************************************
	//Busca los articulos de una factura para devolverlos
	//
	function buscasinvdev(){
		$mid = $this->input->post('q');

		$data = '[ ]';
		if($mid !== false){
			$dbfactura = $this->db->escape($mid);
			$retArray = $retorno = array();
			$mSQL="SELECT
				aa.descrip,aa.cana,SUM(COALESCE(d.cana,0)) AS dev,aa.codigo, aa.precio1,aa.precio2,aa.precio3,aa.precio4,
				aa.iva,aa.existen,aa.tipo,aa.peso, aa.ultimo, aa.pond,aa.preca,aa.detalle
				FROM (SELECT TRIM(a.descrip) AS descrip,SUM(b.cana) AS cana,TRIM(a.codigo) AS codigo, a.precio1,a.precio2,a.precio3,a.precio4,
				a.iva,a.existen,a.tipo,a.peso, a.ultimo, a.pond,b.preca,b.numa,b.detalle
				FROM sinv AS a
				JOIN sitems AS b ON a.codigo=b.codigoa
				WHERE b.numa=$dbfactura AND b.tipoa='F'
				GROUP BY b.codigoa,b.preca) AS aa
				LEFT JOIN sfac   AS c  ON aa.numa=c.factura AND c.tipo_doc='D'
				LEFT JOIN sitems AS d ON c.numero=d.numa AND c.tipo_doc=d.tipoa AND aa.codigo=d.codigoa AND aa.preca=d.preca
				GROUP BY aa.codigo,aa.preca
				ORDER BY aa.descrip";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $id=>$row ) {
					if(empty($row['cana'])) $row['cana']=0;
					if(empty($row['dev']))  $row['dev'] =0;
					$saldo = $row['cana']-$row['dev'];
					if($saldo <=0) continue;
					$retArray['codigo']  = $this->en_utf8($row['codigo']);
					$retArray['detalle'] = $this->en_utf8(trim($row['detalle']));
					$retArray['cana']    = $saldo;
					$retArray['tipo']    = $row['tipo'];
					$retArray['peso']    = $row['peso'];
					$retArray['ultimo']  = $row['ultimo'];
					$retArray['pond']    = $row['pond'];
					$retArray['preca']   = round($row['preca'],2);
					$retArray['base1']   = round($row['precio1']*100/(100+$row['iva']),2);
					$retArray['base2']   = round($row['precio2']*100/(100+$row['iva']),2);
					$retArray['base3']   = round($row['precio3']*100/(100+$row['iva']),2);
					$retArray['base4']   = round($row['precio4']*100/(100+$row['iva']),2);
					$retArray['descrip'] = $this->en_utf8($row['descrip']);
					$retArray['iva']     = $row['iva'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}


	//******************************************************************
	//Busca los articulos de un combo
	//
	function buscasinvcombo(){
		$mid = $this->input->post('q');

		$data = '[ ]';
		if($mid !== false){
			$dbcombo = $this->db->escape($mid);
			$retArray = $retorno = array();
			$mSQL="SELECT a.codigo,a.descrip,b.tipo,b.ultimo,b.pond,
			a.precio,b.peso,b.precio1,
			b.iva,a.cantidad
			FROM sinvcombo AS a
			JOIN sinv AS b ON a.codigo=b.codigo
			WHERE a.combo=${dbcombo}";

			$query = $this->db->query($mSQL);
			if($query->num_rows() > 0){
				foreach( $query->result_array() as $id=>$row ){

					if($row['precio']>0){
						$precio=round($row['precio'],2);
					}else{
						$precio=round($row['precio1']*100/(100+$row['iva']),2);
					}

					$retArray['codigo']  = $this->en_utf8($row['codigo']);
					$retArray['cana']    = $row['cantidad'];
					$retArray['tipo']    = $row['tipo'];
					$retArray['peso']    = $row['peso'];
					$retArray['ultimo']  = $row['ultimo'];
					$retArray['pond']    = $row['pond'];
					$retArray['preca']   = $precio;
					$retArray['base1']   = $precio;
					$retArray['base2']   = $precio;
					$retArray['base3']   = $precio;
					$retArray['base4']   = $precio;
					$retArray['descrip'] = $this->en_utf8($row['descrip']);
					$retArray['iva']     = $row['iva'];
					$retArray['combo']   = $mid;
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//******************************************************************
	//Buscar los articulos para devolver compras
	//
	function buscaitscstdev(){
		$mid = $this->input->post('q');

		$data = '[ ]';
		if($mid !== false){
			$dbcontrol= $this->db->escape($mid);
			$retArray = $retorno = array();

			$mSQL="SELECT a.codigo,b.descrip,a.iva,b.peso,b.pond,b.precio1,a.cantidad
				FROM itscst AS a
				JOIN sinv AS b ON a.codigo=b.codigo
				WHERE a.control=${dbcontrol}";

			$query = $this->db->query($mSQL);
			if($query->num_rows() > 0){
				foreach($query->result_array() as  $id=>$row){
					$retArray['codigo']  = $this->en_utf8(trim($row['codigo']));
					$retArray['descrip'] = $this->en_utf8(trim($row['descrip']));
					$retArray['iva']     = $row['iva'];
					$retArray['peso']    = $row['peso'];
					$retArray['pond']    = $row['pond'];
					$retArray['cana']    = $row['cantidad'];
					$retArray['precio1'] = round($row['precio1'],2);
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//******************************************************************
	//Busca los articulos de una factura para despacharlos
	//
	function buscasinvsnot(){
		$mid = $this->input->post('q');

		$data = '[]';
		if($mid !== false){
			$dbfactura = $this->db->escape($mid);
			$retArray = $retorno = array();
			$mSQL="SELECT TRIM(a.codigoa) AS codigoa, a.desca, a.cana,SUM(IF(d.tipo='D',-1,1)*e.entrega) AS entregado
			FROM (
				SELECT  aa.codigoa, aa.desca,aa.numa, SUM(aa.cana) AS cana
				FROM sitems AS aa
				WHERE aa.numa=${dbfactura} AND aa.tipoa='F' AND aa.despacha<>'S'
				GROUP BY aa.codigoa
			) AS a
			JOIN sinv   AS c ON a.codigoa=c.codigo AND c.tipo='Articulo'
			LEFT JOIN itsnot AS e ON a.numa=e.factura AND e.codigo=a.codigoa
			LEFT JOIN snot   AS d ON  d.numero=e.numero
			GROUP BY a.codigoa
			ORDER BY a.desca";

			//Restas las devoluciones
			$arr_devolu=array();
			$mSQL_2="SELECT  TRIM(aa.codigoa) AS codigoa, SUM(aa.cana) AS cana
				FROM sitems AS aa
				JOIN sfac   AS bb ON bb.tipo_doc=aa.tipoa AND aa.numa=bb.numero
				WHERE bb.factura=${dbfactura} AND bb.tipo_doc='D'
				GROUP BY aa.codigoa";
			$query = $this->db->query($mSQL_2);
			if ($query->num_rows() > 0){
				foreach( $query->result() as $row ) {
					$arr_devolu[$row->codigoa]=$row->cana;
				}
			}

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $id=>$row ) {
					$codigo = $row['codigoa'];
					if(empty($row['entregado'])) $row['entregado']=0;
					if(isset($arr_devolu[$codigo])) $row['entregado'] += $arr_devolu[$codigo];
					if(empty($row['cana']))      $row['cana'] =0;
					$saldo = $row['cana']-$row['entregado'];
					if($saldo <=0) continue;

					$retArray['codigo']  = $this->en_utf8(trim($row['codigoa']));
					$retArray['descrip'] = $this->en_utf8(trim($row['desca']));
					$retArray['saldo']   = $saldo;
					$retArray['cant']    = $row['cana'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}


	//******************************************************************
	//Busca los articulos que esten por rma
	//
	function buscastrarma(){
		$sprv = $this->input->post('sprv');
		$alma = $this->input->post('alma');

		$data = '[ ]';
		if($sprv !== false && $alma !== false){
			$dbsprv = $this->db->escape($sprv);
			$dbalma = $this->db->escape($alma);
			$retArray = $retorno = array();
			$mSQL="SELECT a.codigo, SUM(IF(b.envia=$dbalma,-1,1)*a.cantidad) AS cantidad, a.descrip
				FROM itstra AS a
				JOIN stra AS b ON a.numero=b.numero
				WHERE b.proveed=$dbsprv AND (b.envia=$dbalma OR b.recibe=$dbalma)
				GROUP BY a.codigo
				HAVING cantidad>0";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $id=>$row ) {
					$retArray['codigo']  = $this->en_utf8($row['codigo']);
					$retArray['descrip'] = $this->en_utf8($row['descrip']);
					$retArray['cantidad']= $row['cantidad'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//******************************************************************
	//  Busca los efectos que se deben para los cruces
	//
	function buscasprm(){
		$mid = $this->input->post('sprv');

		$data = '[ ]';
		if($mid !== false){
			$dbsprv   = $this->db->escape($mid);
			$retArray = $retorno = array();
			$mSQL="SELECT id, fecha,monto, numero, tipo_doc, monto-abonos AS saldo
			FROM sprm
			WHERE tipo_doc IN ('FC','ND','GI') AND abonos<monto AND cod_prv=${dbsprv} ORDER BY fecha";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $id=>$row ) {
					$retArray['numero']  = trim($row['numero']);
					$retArray['tipo_doc']= trim($row['tipo_doc']);
					$retArray['fecha']   = $row['fecha'];
					$retArray['monto']   = $row['monto'];
					$retArray['saldo']   = $row['saldo'];
					$retArray['id']      = $row['id'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}


	//******************************************************************
	//Busca los efectos que pueden aplicarse
	//
	function buscasmovapan($tipo=null){
		$mid = $this->input->post('scli');

		if($tipo=='annc'){
			$ww = 'tipo_doc IN (\'AN\',\'NC\')';
		}else{
			$ww = 'tipo_doc IN (\'FC\',\'ND\',\'GI\')';
		}

		$data = '[ ]';
		if($mid !== false){
			$dbscli   = $this->db->escape($mid);
			$retArray = $retorno = array();
			$mSQL="SELECT id, fecha,monto, numero, tipo_doc, monto-abonos AS saldo
			FROM smov
			WHERE ${ww} AND abonos<monto AND cod_cli=${dbscli}
			ORDER BY fecha ASC";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $id=>$row ) {
					$retArray['numero']   = trim($row['numero']);
					$retArray['tipo_doc'] = trim($row['tipo_doc']);
					$retArray['fecha']    = $row['fecha'];
					$retArray['monto']    = $row['monto'];
					$retArray['saldo']    = $row['saldo'];
					$retArray['id']       = $row['id'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//******************************************************************
	// Busca los efectos que pueden aplicarse a proveedor
	//
	function buscasprmapan($tipo=null){
		$mid = $this->input->post('sprv');

		if($tipo=='annc'){
			$ww = 'tipo_doc IN (\'AN\',\'NC\')';
		}else{
			$ww = 'tipo_doc IN (\'FC\',\'ND\',\'GI\')';
		}

		$data = '[ ]';
		if($mid !== false){
			$dbsprv   = $this->db->escape($mid);
			$retArray = $retorno = array();
			$mSQL="SELECT id, fecha,monto, numero, tipo_doc, monto-abonos AS saldo
			FROM sprm
			WHERE ${ww} AND abonos<monto AND cod_prv=${dbsprv}";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $id=>$row ) {
					$retArray['numero']  = trim($row['numero']);
					$retArray['tipo_doc']= trim($row['tipo_doc']);
					$retArray['fecha']   = $row['fecha'];
					$retArray['monto']   = $row['monto'];
					$retArray['saldo']   = $row['saldo'];
					$retArray['id']      = $row['id'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//******************************************************************
	//Busca los efectos que se deben para los cruces
	//
	function buscasmov(){
		$mid = $this->input->post('scli');

		$data = '[ ]';
		if($mid !== false){
			$dbscli   = $this->db->escape($mid);
			$retArray = $retorno = array();
			$mSQL="SELECT id, fecha,monto, numero, tipo_doc, monto-abonos AS saldo
			FROM smov
			WHERE tipo_doc IN ('FC','ND','GI') AND abonos<monto AND cod_cli=${dbscli} ORDER BY fecha";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $id=>$row ) {
					$retArray['numero']  = trim($row['numero']);
					$retArray['tipo_doc']= trim($row['tipo_doc']);
					$retArray['fecha']   = $row['fecha'];
					$retArray['monto']   = $row['monto'];
					$retArray['saldo']   = $row['saldo'];
					$retArray['id']      = $row['id'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}


	//******************************************************************
	//Busca los efectos que se deben para los cruces
	//
	function buscasmovrc(){
		$mid = $this->input->post('q');

		$data = '[ ]';
		if($mid !== false){
			$dbnumero = $this->db->escape($mid.'%');

			$retArray = $retorno = array();
			$mSQL="SELECT a.id, a.fecha, a.vence, a.monto, a.numero, a.tipo_doc, a.monto-a.abonos AS saldo, b.nombre, a.cod_cli
			FROM smov a JOIN scli b ON a.cod_cli=b.cliente
			WHERE a.tipo_doc IN ('FC','ND','GI') AND a.abonos < a.monto AND a.numero like ${dbnumero}";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $id=>$row ) {
					$retArray['label']  = $row['tipo_doc'].$row['numero'].' '.$this->_datehuman($row['fecha']).' '.$row['saldo'].' '.$this->en_utf8($row['nombre']);
					$retArray['value']  = $row['numero'];
					$retArray['tipo_doc']= trim($row['tipo_doc']);
					$retArray['fecha']   = $this->_datehuman($row['fecha']);
					$retArray['vence']   = $this->_datehuman($row['vence']);
					$retArray['nombre']  = $this->en_utf8($row['nombre']);
					$retArray['monto']   = $row['monto'];
					$retArray['saldo']   = $row['saldo'];
					$retArray['id']      = $row['id'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}


	//******************************************************************
	//Busca la factura afectada para otin
	//
	function buscaafecta(){
		$comodin= $this->datasis->traevalor('COMODIN');
		$mid    = $this->input->post('q');

		if(strlen($comodin)==1 && $comodin!='%' && $mid!==false){
			$mid=str_replace($comodin,'%',$mid);
		}
		$qdb  = $this->db->escape('%'.$mid.'%');

		$data = '[]';
		if($mid !== false){
			$retArray = $retorno = array();

			$mSQL="SELECT numero,fecha,totalg FROM sfac WHERE numero LIKE ${qdb} LIMIT ".$this->autolimit;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']  = '('.$row['numero'].') '.$row['totalg'];
					$retArray['value']  = $row['numero'];
					$retArray['fecha']  = $this->_datehuman($row['fecha']);

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//******************************************************************
	//Saldo de proveedor
	function ajaxsaldosprv(){
		$mid = $this->input->post('clipro');

		if($mid !== false){
			$dbsprv = $this->db->escape($mid);

			$this->db->select_sum('a.monto*IF(tipo_doc IN ("FC","ND","GI"),1,-1)','saldo');
			$this->db->from('sprm AS a');
			$this->db->where('a.cod_prv',$mid);
			$q=$this->db->get();
			$row = $q->row_array();
			echo (empty($row['saldo']))? 0: $row['saldo'];
		}else{
			echo 0;
		}
	}

	//******************************************************************
	//Saldo de cliente
	function ajaxsaldoscli(){
		$mid = $this->input->post('clipro');

		if($mid !== false){
			$this->db->select_sum('a.monto*IF(tipo_doc IN ("FC","ND","GI"),1,-1)','saldo');
			$this->db->from('smov AS a');
			$this->db->where('a.cod_cli',$mid);
			$q=$this->db->get();
			$row = $q->row_array();
			echo (empty($row['saldo']))? 0: $row['saldo'];
		}else{
			echo 0;
		}
	}

	//******************************************************************
	//Saldo de cliente vencido
	function ajaxsaldoscliven(){
		$mid = $this->input->post('clipro');

		if($mid !== false){
			$this->db->select_sum('a.monto*IF(tipo_doc IN ("FC","ND","GI"),1,-1)','saldo');
			$this->db->from('smov AS a');
			$this->db->where('a.cod_cli',$mid);
			$this->db->where('a.vence <=',date('Y-m-d'));
			$q=$this->db->get();
			$row = $q->row_array();
			echo (empty($row['saldo']))? 0: $row['saldo'];
		}else{
			echo 0;
		}
	}

	//******************************************************************
	//Saldo pendiente de proveedor
	function ajaxsanncprov(){
		$mid = $this->input->post('clipro');

		if($mid !== false){
			$this->db->select_sum('(a.monto-abonos)*(tipo_doc IN ("AN","NC"))','saldo');
			$this->db->from('sprm AS a');
			$this->db->where('a.cod_prv',$mid);
			$this->db->where('a.monto > a.abonos');
			$q=$this->db->get();
			$row = $q->row_array();
			echo (empty($row['saldo']))? 0: $row['saldo'];
		}else{
			echo 0;
		}
	}

	//******************************************************************
	// Busca Plan de cuentas
	//
	function buscacpla(){
		$mid   = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		$qdb   = $this->db->escape($mid.'%');

		$data = '[{ }]';
		if($mid !== false){
			$qformato=$this->datasis->formato_cpla();
			$retArray = $retorno = array();

			$mSQL="SELECT codigo, descrip, departa, ccosto
			FROM cpla WHERE codigo LIKE $qdb AND codigo LIKE \"$qformato\"";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']    = $row['codigo'].'-'.$this->en_utf8($row['descrip']);
					$retArray['value']    = $row['codigo'];
					$retArray['descrip']  = $this->en_utf8($row['descrip']);
					$retArray['departa']  = $row['departa'];
					$retArray['ccosto']   = $row['ccosto'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}else{
				//Busca por Descripcion
				$qdb   = $this->db->escape('%'.$mid.'%');
				$mSQL="SELECT codigo, descrip, departa, ccosto FROM cpla WHERE descrip LIKE $qdb AND codigo LIKE \"$qformato\"";;
				$query = $this->db->query($mSQL);
				if ($query->num_rows() > 0){
					foreach( $query->result_array() as  $row ) {
						$retArray['label']    = $row['codigo'].'-'.$this->en_utf8($row['descrip']);
						$retArray['value']    = $row['codigo'];
						$retArray['descrip']  = $this->en_utf8($row['descrip']);
						$retArray['departa']  = $row['departa'];
						$retArray['ccosto']   = $row['ccosto'];

						array_push($retorno, $retArray);
					}
					$data = json_encode($retorno);
				}else{
					$retArray[0]['label']    = 'No se consiguieron cuentas';
					$retArray[0]['value']    = '';
					$retArray[0]['descrip']  = '';
					$retArray[0]['departa']  = '';
					$retArray[0]['ccosto']   = '';

					$data = json_encode($retArray);
				}
			}
		}
		echo $data;
	}

	//******************************************************************
	//Autocomplete para buscar las reservaciones
	function buscares(){
		$mid   = $this->input->post('q');
		$qdb   = $this->db->escape('%'.$mid.'%');
		$scli  = $this->input->post('scli');

		$data = '[{ }]';
		if($mid !== false){
			$qformato=$this->datasis->formato_cpla();
			$retArray = $retorno = array();
			if(!empty($scli)) $ww='AND cliente='.$this->db->escape($scli); else $ww='';

			$mSQL="SELECT a.id,a.numero,a.fecha,a.cliente,a.edificacion,a.inmueble,a.reserva,b.nombre,b.rifci, b.tipo AS sclitipo,dire11 AS direc,c.uso
			FROM edres AS a
			JOIN scli AS b ON a.cliente=b.cliente
			JOIN edinmue AS c ON a.inmueble=c.id
			WHERE numero LIKE $qdb  $ww";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']    = $row['numero'].'-'.$this->en_utf8($row['nombre']);
					$retArray['value']    = $row['numero'];
					$retArray['nombre']   = $this->en_utf8($row['nombre']);
					$retArray['edifi']    = $row['edificacion'];
					$retArray['inmue']    = $row['inmueble'];
					$retArray['rifci']    = $row['rifci'];
					$retArray['cliente']  = $row['cliente'];
					$retArray['sclitipo'] = $row['sclitipo'];
					$retArray['direc']    = $row['direc'];
					$retArray['uso']      = $row['uso'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}else{
				$retArray['label']    = 'No se consiguieron reservaciones';
				$retArray['value']    = '';
				$retArray['nombre']   = '';
				$retArray['edifi']    = '';
				$retArray['inmue']    = '';
				$retArray['rifci']    = '';
				$retArray['cliente']  = '';
				$retArray['sclitipo'] = '';
				$retArray['direc']    = '';
				$retArray['uso']      = '';

				$data = json_encode($retArray);
			}
		}
		echo $data;
	}

	//******************************************************************
	//Autocomplete para mgas
	function automgas(){
		$q   = $this->input->post('q');
		if($q === false) $q  = $this->input->post('term');

		$data = '[{ }]';
		if($q!==false){
			$mid = $this->db->escape('%'.$q.'%');
			$mSQL = "SELECT a.codigo, a.descrip
				FROM mgas AS a
			WHERE a.codigo LIKE ${mid} OR a.descrip LIKE ${mid} ORDER BY a.descrip LIMIT ".$this->autolimit;

			$query = $this->db->query($mSQL);
			$retArray = array();
			$retorno = array();
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']      = $row['codigo'];
					$retArray['label']      = trim($row['codigo']).' - '.$this->en_utf8(trim($row['descrip']));
					$retArray['codigo']     = $this->en_utf8(trim($row['codigo']));
					$retArray['descrip']    = $this->en_utf8(trim($row['descrip']));

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}
		}
		echo $data;
	}

	//******************************************************************
	//Autocomplete para botr
	function autobotr($tipo=null){
		$q   = $this->input->post('q');

		$data = '[]';
		if($q!==false){
			if(!empty($tipo)){
				$ww = 'AND a.tipo='.$this->db->escape($tipo);
			}else{
				$ww = '';
			}
			$mid = $this->db->escape('%'.$q.'%');
			$mSQL = "SELECT a.codigo, a.nombre descrip
				FROM botr AS a
			WHERE (a.codigo LIKE ${mid} OR a.nombre LIKE ${mid}) ${ww}
			ORDER BY a.nombre LIMIT ".$this->autolimit;

			$query = $this->db->query($mSQL);
			$retArray = array();
			$retorno = array();
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']      = $row['codigo'];
					$retArray['label']      = trim($row['codigo']).' - '.$this->en_utf8(trim($row['descrip']));
					$retArray['codigo']     = $this->en_utf8(trim($row['codigo']));
					$retArray['descrip']    = $this->en_utf8(trim($row['descrip']));

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}
		}
		echo $data;
	}


	//******************************************************************
	//Autocomplete para las labores de sinv
	function buscaordplabor(){
		$mid   = $this->input->post('q');
		$data = '[{ }]';
		if($mid!==false){
			$mid  = $this->db->escape($mid);
			$mSQL = "SELECT a.estacion,a.nombre,a.actividad,a.tunidad,a.tiempo
				FROM sinvplabor AS a
			WHERE a.producto=${mid}";

			$query = $this->db->query($mSQL);
			$retArray = array();
			$retorno = array();
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['nombre']   = $this->en_utf8(trim($row['nombre']));
					$retArray['actividad']= $row['actividad'];
					$retArray['tunidad']  = $row['tunidad'];
					$retArray['tiempo']   = $row['tiempo'];
					$retArray['estacion'] = $row['estacion'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}
		}
		echo $data;
	}

	//******************************************************************
	//Autocomplete para las recetas de sinv
	function buscaordpitem(){
		$mid   = $this->input->post('q');
		$data = '[{ }]';
		if($mid!==false){
			$mid  = $this->db->escape($mid);
			$mSQL = "SELECT a.codigo, a.descrip,a.cantidad,a.merma,a.ultimo
				FROM sinvpitem AS a
			WHERE a.producto=${mid}";

			$query = $this->db->query($mSQL);
			$retArray = array();
			$retorno = array();
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['codigo']  = $this->en_utf8(trim($row['codigo']));
					$retArray['descrip'] = $this->en_utf8(trim($row['descrip']));
					$retArray['merma']   = (empty($row['merma']))? 0 : $row['merma'];
					$retArray['cantidad']= $row['cantidad'];
					$retArray['ultimo']  = $row['ultimo'];

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}
		}
		echo $data;
	}

	//******************************************************************
	//Para cerrar la ventana luego de una operacion exitosa
	//
	function reccierraventana($reload=null){
		if($reload!='N') $rr='$(window).unload(function() { window.opener.location.reload(); });'; else $rr='';
		$script='
		<script language="javascript" type="text/javascript">
		$(function(){
			'.$rr.'
			window.close();
		});
		</script>';

		$data['content'] = '<center>Operaci&oacute;n Exitosa</center>';
		$data['head']    = script('jquery.js').$script;
		$data['title']   = '';
		$this->load->view('view_ventanas', $data);
	}

	function buscasinv2(){
		$comodin=$this->datasis->traevalor('COMODIN');
		$mid  = $this->input->post('q');
		if(strlen($comodin)==1){
			$mid=str_replace($comodin,'%',$mid);
		}

		$data = '[]';
		if($mid !== false){
			$retArray = $retorno = array();

			$mSQL="SELECT DISTINCT TRIM(a.descrip) AS descrip, TRIM(a.codigo) AS codigo, a.precio1,precio2,precio3,precio4, a.iva,a.existen,a.tipo
				,a.peso, a.ultimo, a.pond,a.formcal,a.id FROM sinv AS a
				WHERE a.codigo LIKE ".$this->db->escape($mid.'%')." AND a.activo='S'
				ORDER BY a.descrip";

			$query = $this->db->query($mSQL);
			$cant=$query->num_rows();
			if(!($cant>0)){
				$mSQL="SELECT DISTINCT TRIM(a.descrip) AS descrip, TRIM(a.codigo) AS codigo, a.precio1,precio2,precio3,precio4, a.iva,a.existen,a.tipo
				,a.peso, a.ultimo, a.pond,a.formcal,a.id FROM sinv AS a
				WHERE a.codigo LIKE ".$this->db->escape('%'.$mid.'%')." AND a.activo='S'
				ORDER BY a.descrip";
				$query = $this->db->query($mSQL);
				$cant=$query->num_rows();
			}

			if(!($cant>0)){
				$mSQL="SELECT DISTINCT TRIM(a.descrip) AS descrip, TRIM(a.codigo) AS codigo, a.precio1,precio2,precio3,precio4, a.iva,a.existen,a.tipo
				,a.peso, a.ultimo, a.pond,a.formcal,a.id FROM sinv AS a
				WHERE a.descrip LIKE ".$this->db->escape($mid.'%')." AND a.activo='S'
				ORDER BY a.descrip";
				$query = $this->db->query($mSQL);
				$cant=$query->num_rows();
			}

			if(!($cant>0)){
				$mSQL="SELECT DISTINCT TRIM(a.descrip) AS descrip, TRIM(a.codigo) AS codigo, a.precio1,precio2,precio3,precio4, a.iva,a.existen,a.tipo
				,a.peso, a.ultimo, a.pond,a.formcal,a.id FROM sinv AS a
				WHERE a.descrip LIKE ".$this->db->escape('%'.$mid.'%')." AND a.activo='S'
				ORDER BY a.descrip";
				$query = $this->db->query($mSQL);
				$cant=$query->num_rows();
			}

			$cana=1;
			if ($cant > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']   = '('.$row['codigo'].') '.$row['descrip'].' '.$row['precio1'].' Bs. - '.$row['existen'];
					$retArray['value']   = $row['codigo'];
					$retArray['codigo']  = $row['codigo'];
					$retArray['cana']    = $cana;
					$retArray['tipo']    = $row['tipo'];
					$retArray['peso']    = $row['peso'];
					$retArray['ultimo']  = $row['ultimo'];
					$retArray['pond']    = $row['pond'];
					$retArray['formcal'] = $row['formcal'];
					$retArray['id']      = $row['id'];
					$retArray['base1']   = round($row['precio1']*100/(100+$row['iva']),2);
					$retArray['base2']   = round($row['precio2']*100/(100+$row['iva']),2);
					$retArray['base3']   = round($row['precio3']*100/(100+$row['iva']),2);
					$retArray['base4']   = round($row['precio4']*100/(100+$row['iva']),2);
					$retArray['descrip'] = $this->en_utf8($row['descrip']);
					//$retArray['descrip'] = wordwrap($row['descrip'], 25, '<br />');
					$retArray['iva']     = $row['iva'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//******************************************************************
	//  Busca aranceles
	//
	function buscaaran(){
		$comodin= $this->datasis->traevalor('COMODIN');
		$mid    = $this->input->post('q');
		if(strlen($comodin)==1 && $comodin!='%' && $mid!==false){
			$mid=str_replace($comodin,'%',$mid);
		}
		$qdb  = $this->db->escape($mid.'%');
		$qba  = $this->db->escape($mid);

		$data = '[{ }]';
		if($mid !== false){
			$retArray = $retorno = array();
			$mSQL="SELECT a.codigo, a.descrip, a.tarifa
			FROM aran AS a
			WHERE a.descrip LIKE ${qdb} OR ca.odigo LIKE ${qdb}";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']   = '('.$row['codigo'].') '.$row['descrip'].' '.$row['tarifa'];
					$retArray['value']   = $row['codigo'];
					$retArray['tarifa']  = $row['tarifa'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	function sprvfc(){
		$sprv   = $this->input->post('sprv');
		$mid    = $this->input->post('q');

		if(!($sprv!==false && $mid!==false)){
			return true;
		}
		$comodin= $this->datasis->traevalor('COMODIN');
		if(strlen($comodin)==1 && $comodin!='%' && $mid!==false){
			$mid=str_replace($comodin,'%',$mid);
		}
		$data = '[{ }]';
		if($mid !== false){
			$qdb   = $this->db->escape($mid.'%');
			$dbsprv= $this->db->escape($sprv);

			if(date('d')<=15){
				$pdia  ='01';
				$dia   ='15';
			}else{
				$pdia  ='16';
				$dia   =date('d', mktime(0, 0, 0, date('n'), 0));
			}
			$fechai =date('Ym'.$pdia);
			$fechac =date('Ym'.$dia );

			$retArray = $retorno = array();
			$mSQL="SELECT a.numero, a.fecha
			FROM gser AS a
			WHERE a.numero LIKE ${qdb} AND a.tipo_doc='FC' AND a.proveed=${dbsprv}
			UNION ALL
			SELECT b.numero, b.fecha
			FROM scst AS b
			WHERE b.numero LIKE ${qdb} AND b.tipo_doc='FC' AND b.proveed=${dbsprv}";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$fecha  = str_replace('-','',$row['fecha']);

					$retArray['label']   = '('.$row['numero'].') '.$this->_datehuman($row['fecha']);
					$retArray['value']   = $row['numero'];
					$retArray['fecha']   = $this->_datehuman($row['fecha']);
					$retArray['aplrete'] = $fecha>=$fechai && $fecha<=$fechac;

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
	        }
		}
		echo $data;
	}

	//******************************************************************
	//Funcion para traer los clientes en los pedidos ligeros
	//
	function scliex(){
		$comodin= $this->datasis->traevalor('COMODIN');
		$mid    = $this->input->post('q');
		if(strlen($comodin)==1 && $comodin!='%' && $mid!==false){
			$mid=str_replace($comodin,'%',$mid);
		}
		$qdb  = $this->db->escape($mid.'%');
		$qba  = $this->db->escape($mid);

		$data = '[{ }]';
		if($mid !== false){
			$retArray = $retorno = array();

			//Cheque si existe el codigo
			$mSQL="SELECT TRIM(nombre) AS nombre, TRIM(rifci) AS rifci, cliente
				FROM scli WHERE cliente=${qba} LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() == 1){
				$row = $query->row_array();

				$retArray['rifci']   = $row['rifci'];
				$retArray['nombre']  = $this->en_utf8($row['nombre']);
				$retArray['cod_cli'] = $row['cliente'];
				array_push($retorno, $retArray);
				$ww=" AND cliente<>${qba}";
			}else{
				$ww='';
			}

			$mSQL="SELECT TRIM(nombre) AS nombre, TRIM(rifci) AS rifci, cliente
				FROM scli WHERE (cliente LIKE ${qdb} OR rifci LIKE ${qdb} OR nombre LIKE ${qdb}) $ww
				ORDER BY rifci LIMIT ".$this->autolimit;

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['rifci']   = $row['rifci'];
					$retArray['nombre']  = $this->en_utf8($row['nombre']);
					$retArray['cod_cli'] = $row['cliente'];
					array_push($retorno, $retArray);
				}
			}
			if(count($data)>0)
				$data = json_encode($retorno);
		}
		echo $data;
		return true;
	}

	// Para JQGRID
	function ddsucu(){
		$mSQL = "SELECT TRIM(codigo) codigo, CONCAT(TRIM(codigo),' ',TRIM(sucursal)) sucursal FROM sucu ORDER BY codigo";
		echo $this->datasis->llenaopciones($mSQL, true);
	}

	function ddtarjeta(){
		$mSQL = "SELECT tipo, CONCAT(tipo,' ',nombre) nombre FROM tarjeta WHERE activo!='N' AND tipo NOT IN ('EF', 'DE', 'NC','RI','IR','RP')";
		echo $this->datasis->llenaopciones($mSQL, true);
	}

	function ddbanco(){
		$mSQL = "SELECT cod_banc, CONCAT(cod_banc, ' ', nomb_banc) banco FROM tban WHERE cod_banc<>'CAJ' ORDER BY nomb_banc ";
		echo $this->datasis->llenaopciones($mSQL, true);
	}

	function ddbanc(){
		$tipo = $this->uri->segment(3);
		$id   = $this->uri->segment(4);
		$mSQL  = "SELECT codbanc, CONCAT(codbanc, ' ', banco, numcuent) banco ";
		$mSQL .= "FROM banc ";
		$mSQL .= "WHERE activo='S'  ";
		if ( $tipo == 'B' ) $mSQL .= " AND tbanco<>'CAJ' ";
		if ( $tipo == 'C' ) $mSQL .= " AND tbanco='CAJ' ";
		$mSQL .= "ORDER BY (tbanco='CAJ'), codbanc ";
		echo $this->datasis->llenaopciones($mSQL, true, $id);
	}


	function ddusuario(){
		$mSQL = "SELECT us_codigo, CONCAT(us_codigo, ' ', us_nombre) us_nombre FROM usuario ORDER BY us_codigo";
		echo $this->datasis->llenaopciones($mSQL, true);
	}

	function ddcajero(){
		$mSQL = "SELECT cajero, CONCAT(cajero, ' ', nombre) nombre FROM scaj ORDER BY nombre";
		echo $this->datasis->llenaopciones($mSQL, true);
	}
	function ddcaub(){
		$mSQL = "SELECT ubica, CONCAT(ubica, ' ', ubides) ubides FROM caub ORDER BY ubica ";
		echo $this->datasis->llenaopciones($mSQL, true);
	}
	function ddvende(){
		$mSQL = "SELECT TRIM(vendedor) vendedor, CONCAT(trim(vendedor), ' ', trim(nombre)) nombre FROM vend ORDER BY vendedor ";
		echo $this->datasis->llenaopciones($mSQL, true);
	}

	function dddivi(){
		$mSQL = "SELECT division, CONCAT(division,' ',descrip) descrip  FROM divi ORDER BY division";
		echo $this->datasis->llenaopciones($mSQL, true);
	}

	function dddepag(){
		$mSQL = "SELECT depto, CONCAT(depto,' ',descrip) descrip FROM dpto WHERE tipo='G' ORDER BY depto";
		echo $this->datasis->llenaopciones($mSQL, true);
	}

	function dddepai(){
		$mSQL = "SELECT depto, CONCAT(depto,' ',descrip) descrip FROM dpto WHERE tipo='I' ORDER BY depto";
		echo $this->datasis->llenaopciones($mSQL, true);
	}
	function ddgrcl(){
		$mSQL = "SELECT grupo, CONCAT(grupo, ' ', gr_desc) banco FROM grcl ORDER BY grupo ";
		echo $this->datasis->llenaopciones($mSQL, true);
	}



	//******************************************************************
	//          BUSCA GASTO
	//
	function buscamgas(){
		$mid  = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		$qdb  = $this->db->escape('%'.$mid.'%');
		$qmid = $this->db->escape($mid.'%');

		$data = '[{ }]';
		if($mid !== false){
			$retArray = $retorno = array();
			$mSQL="SELECT TRIM(descrip) AS nombre, codigo FROM mgas WHERE descrip LIKE ${qdb} OR codigo LIKE ${qmid} ORDER BY descrip LIMIT 20";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']   = $row['codigo'];
					$retArray['label']   = $this->en_utf8($row['nombre']).'('.$row['codigo'].') ';
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}
		}
		echo $data;
		return true;
	}

	//******************************************************************
	// BUSCA GASTO o PROVEEDOR
	//
	function buscasprvmgas(){
		$tipo  = $this->input->post('cargo');
		$cta   = $this->input->post('acelem');

		if ( $cta == 'ctade')
			$tipo = substr($tipo,0,1);
		else
			$tipo = substr($tipo,2,1);

		if ( $tipo == 'P')
			$this->buscasprv();
		else
			$this->buscamgas();
	}

	//******************************************************************
	// BUSCA TRABAJADOR
	//
	function buscapers(){
		$mid  = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		$qdb  = $this->db->escape('%'.$mid.'%');
		$qmid = $this->db->escape($mid.'%');

		$data = '[]';
		if($mid !== false){
			$retArray = $retorno = array();
			$mSQL="	SELECT codigo, CONCAT(TRIM(apellido),', ',TRIM(nombre),' (',nacional,TRIM(cedula),')') AS label,
					CONCAT(TRIM(apellido),', ',TRIM(nombre))  nombre, sueldo, enlace,TRIM(cedula) AS cedula, nacional
				FROM pers WHERE nombre LIKE ${qdb} OR apellido LIKE ${qdb} OR codigo LIKE ${qmid}  OR enlace LIKE ${qmid}
				ORDER BY nombre LIMIT 20";
			$query = $this->db->query($mSQL);

			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']   = $row['codigo'];
					$retArray['label']   = $this->en_utf8($row['label']);
					$retArray['nombre']  = $this->en_utf8($row['nombre']);
					$retArray['sueldo']  = $row['sueldo'];
					$retArray['cedula']  = $row['cedula'];
					$retArray['nacional']= $row['nacional'];
					$retArray['enlace']  = $this->en_utf8($row['enlace']);
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}
		}
		echo $data;
		return true;
	}

	//******************************************************************
	//          BUSCA CONCEPTO DE NOMINA
	//
	function buscaconc(){
		$mid  = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		$qdb  = $this->db->escape('%'.$mid.'%');
		$qmid = $this->db->escape($mid.'%');

		$data = '[{ }]';
		if($mid !== false){
			$retArray = $retorno = array();
			$mSQL="	SELECT concepto, CONCAT(TRIM(descrip),' (',concepto,')') AS label,
					IF(tipo='A','Asignacion',IF(tipo='D','Deduccion','Otros')) tipo,
					TRIM(descrip) descrip, formula
				FROM conc WHERE descrip LIKE ${qdb} OR concepto LIKE ${qmid} ORDER BY concepto LIMIT 20";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']   = $row['concepto'];
					$retArray['label']   = $this->en_utf8($row['label']);
					$retArray['tipo']    = $this->en_utf8($row['tipo']);
					$retArray['descrip'] = $this->en_utf8($row['descrip']);
					$retArray['formula'] = $this->en_utf8($row['formula']);
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}
		}
		echo $data;
		return true;
	}


	//******************************************************************
	//      BUSCA EFECTOS DE CLIENTE
	//
	function buscasmovep(){
		$mid  = $this->input->post('q');
		if($mid == false) $mid  = $this->input->post('term');

		$qdb  = $this->db->escape('%'.$mid.'%');
		$qmid = $this->db->escape($mid.'%');

		$cod_cli = $this->input->post('cargo');

		$data = '[{ }]';
		if($mid !== false){
			$retArray = $retorno = array();
			$mSQL="	SELECT numero, CONCAT(tipo_doc, numero, ' ', fecha, ' Monto:', monto-abonos) label, tipo_doc, monto-abonos monto, abonos FROM smov
				WHERE cod_cli=".$this->db->escape($cod_cli)." AND monto>abonos AND tipo_doc IN ('FC','ND') AND numero LIKE ${qmid}
				ORDER BY tipo_doc, numero LIMIT 20";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['value']    = $row['numero'];
					$retArray['label']    = $this->en_utf8($row['label']);
					$retArray['tipo_doc'] = $row['tipo_doc'];
					$retArray['monto']    = $row['monto'];
					$retArray['abonos']   = $row['abonos'];
					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}
		}
		echo $data;
		return true;
	}

	//******************************************************************
	//  BUSCA PROVEEDOR EN SPRV Y PROVOCA
	//
	function ajaxsprv(){
		$rif=$this->input->post('rif');
		if($rif!==false){
			$dbrif=$this->db->escape($rif);
			$nombre=$this->datasis->dameval("SELECT nombre FROM provoca WHERE rif=${dbrif}");
			if(empty($nombre)){
				$nombre=$this->datasis->dameval("SELECT nombre FROM sprv WHERE rif=${dbrif}");
			}
			if(empty($nombre)){
				if(preg_match("/(^[VEJG][0-9]{9}[[:blank:]]*$)/", $rif)>0){
					$t=$this->_crif($rif);
					$nombre=$t['nombre'];
				}elseif(preg_match("/(^[VE][0-9]+[[:blank:]]*$)/", $rif)>0){
					$t=$this->_cced($rif);
					$nombre=$t['nombre'];
				}
			}
			echo trim($nombre);
		}
	}

	function _datehuman($fecha){
		$edate   = explode('-',$fecha);
		$fecha   = date('d/m/Y',mktime(0, 0, 0, $edate[1],$edate[2],$edate[0]));
		return $fecha;
	}

	function _humandate($fecha){
		$edate   = explode('/',$fecha);
		$fecha   = date('Y-m-d',mktime(0, 0, 0, $edate[1],$edate[0],$edate[2]));
		return $fecha;
	}

	//******************************************************************
	//  TRAE LOS DATOS DEL PARTICIPANTE EN EVENTOS
	//
	function traeparti(){
		$rifci = $this->input->post('rifci');
		$t=array(
			'error'    => 1,
			'msj'      => 'Cedula o rif no valido',
			'telefono' => '',
			'email'    => '',
			'sector'   => ''
		);
		if($rifci == false) echo json_encode($t);
		$cedula = $this->db->escape($rifci);
		$mSQL = "SELECT count(*) FROM proparti WHERE cedula=".$cedula;
		if ( $this->datasis->dameval($mSQL) > 0 ) {
			$mSQL = "SELECT telefono, email, sector FROM proparti WHERE cedula=".$cedula;
			$row = $this->datasis->damerow($mSQL);
			$t['error']    = 0;
			$t['msj']      = 'Registro encontrado';
			$t['telefono'] = $row['telefono'];
			$t['email']    = $row['email'];
			$t['sector']   = $row['sector'];
		}
		echo json_encode($t);
	}


	//******************************************************************
	//  CONSULTA LA CEDULA O RIF EN INTERNET
	//
	function traerif(){
		$rifci = $this->input->post('rifci');
		$t=array(
			'error' =>1,
			'msj'   =>'Cedula o rif no valido',
			'nombre'=>'',
			'tasa'  =>75
		);

		if($rifci == false) echo json_encode($t);

		if(preg_match("/(^[VEJG][0-9]{9}[[:blank:]]*$)/", $rifci)>0){
			$t=$this->_crif($rifci);
		}elseif(preg_match("/(^[VE][0-9]+[[:blank:]]*$)/", $rifci)>0){
			$t=$this->_cced($rifci);
		}
		echo json_encode($t);
	}

	function _crif($rif){
		$rt=array(
			'error' =>0,
			'msj'   =>'',
			'nombre'=>'',
			'tasa'  =>75
		);

		$url='http://contribuyente.seniat.gob.ve/getContribuyente/getrif?rif='.urlencode(strtoupper($rif));
		$result = @file_get_contents($url);
		if($result===false){
			$rt['error']=1;
			$rt['msj']  ='Recurso no disponible';
		}else{
			$result=str_replace('<rif:' ,'<' ,$result);
			$result=str_replace('</rif:','</',$result);

			if(stripos($result,'450 El Rif del Contribuyente No es')===false){
				$xml=simplexml_load_string($result);
				$linea = preg_replace('/\(.*\)/', '', $xml->Nombre);
				$rt['nombre'] = utf8_encode($linea);
				$rt['tasa']   = floatval($xml->Tasa);
			}else{
				$rt['error']=1;
				$rt['msj']  ='Contribuyente no encontrado';
			}
		}
		return $rt;
	}

	function _cced($ced){
		$rt=array(
			'error' =>0,
			'msj'   =>'',
			'nombre'=>'',
			'tasa'  =>75
		);

		$postdata = http_build_query(array(
			'nacionalidad' => strtoupper($ced[0]),
			'cedula'       => substr($ced,1)
			)
		);
		$opts = array('http' =>array(
				'method'  => 'GET',
				'timeout' => 7,
				'header'  => 'Content-type: application/x-www-form-urlencoded',
			)
		);
		$context = stream_context_create($opts);
		$result = @file('http://www.cne.gob.ve/web/registro_electoral/ce.php?'.$postdata, false, $context);
		if($result===false){
			$rt['error']=1;
			$rt['msj']  ='Recurso no disponible';
		}else{
			$act=false;
			foreach($result as $line){
				if($act){
					$linea=html_entity_decode(strip_tags($line));
					break;
				}elseif(stripos($line,'Nombre')!==false){
					$act=true;
				}
			}
			if(isset($linea)){
				$linea = preg_replace('/\(.*\)/', '', $linea);
				$rt['nombre'] = utf8_encode(trim($linea));
			}else{
				$rt['error']=1;
				$rt['msj']  ='Cedula no encontrada';
			}
		}
		return $rt;
	}

	//******************************************************************
	// Busca precios de pasajes
	//
	function consultaprecio(){
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');

		$this->rapyd->load("dataform");
		$mSQL = "todavia";

		$codofiorg = $this->input->post('codofiorg');
		$codofides = '00'; //$this->input->post('codofides');

		$form = new DataForm(site_url('ajax/consultaprecio/process'));

		// Origen
		$form->codofiorg = new dropdownField('Origen','codofiorg');
		$form->codofiorg->option('00','Seleccione');
		$form->codofiorg->options("SELECT codofi, desofi FROM pllanos_pasaje.tbofici WHERE codofi>0 ORDER BY desofi");
		$form->codofiorg->style = 'width:180px;';

		// Destino
		$form->codofides = new dropdownField('Destino.','codofides');
		$form->codofides->option('00','Seleccione');
		$form->codofides->options("SELECT codofi, desofi FROM pllanos_pasaje.tbofici WHERE codofi>0 ORDER BY desofi ");
		$form->codofides->style = 'width:180px;';

		$form->submit = new submitField("Buscar","btn_submit");

		$form->build_form();

		$salida  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		$salida .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
		$salida .= '<head>'."\n";
		$salida .= '<meta http-equiv="Content-type" content="text/html; charset=<?=$this->config->item(\'charset\'); ?>" >'."\n";
		$salida .= '<title>ProteoERP<?php if(isset($title)) echo \':\'.preg_replace(\'/<[^>]*>/\', \'\', $title); ?></title>'."\n";
		$salida .= style("ventanas.css");

		$salida .= script('jquery-min.js');
		$salida .= script('jquery-migrate-min.js'); //SOLO PARA JQUERY 1.9 - 2.0
		$salida .= phpscript('nformat.js');
		$salida .= script('jquery-ui.custom.min.js');

		$salida .= style('themes/ui.jqgrid.css');
		$salida .= script('i18n/grid.locale-sp.js');
		$salida .= script('jquery.jqGrid.min.js');

		$salida .= style('themes/proteo/proteo.css');
		$salida .= '</head>'."\n";
		$salida .= '<body><center>'."\n";
		$salida .= '<form action="'.site_url('ajax/consultaprecio/process').'" method="post" id="df1"><div class="alert"></div>'."\n";
		$titu = "Destino";

		//if($form->on_success()){
		if ( $codofiorg > 0 || $codofides > 0 ) {

			$titu = "Destino";
			if ( $codofiorg == 0 && $codofides > 0 ) {
				$mSQL ='SELECT a.codofiorg, b.desofi desorg, a.codofides, b.desofi desdes, a.prec_02 buscama, a.prec_01 ejecutivo,';
				$titu = "Origen";
			} else
				$mSQL ='SELECT a.codofiorg, b.desofi desorg, a.codofides, c.desofi desdes, a.prec_02 buscama, a.prec_01 ejecutivo,';

			$mSQL .='d.valsegu seguro, d.vtasa tasa, round(a.prec_02+d.valsegu+d.vtasa,2) total_buscama,  round(a.prec_01+d.valsegu+d.vtasa,2) total_ejecutivo
					FROM pllanos_pasaje.tbprecios a
					JOIN pllanos_pasaje.tbofici b ON a.codofiorg=b.codofi
					JOIN pllanos_pasaje.tbofici c ON a.codofides=c.codofi
					JOIN pllanos_pasaje.tbparam d ON a.codofiorg=d.codofiori
			        WHERE a.codofiorg>0 AND a.codofides>0 AND a.prec_01>0 AND a.prec_02>0 ';

			if ( $codofiorg > 0 )
				$mSQL .=' AND a.codofiorg = '.$this->db->escape($codofiorg);

			if ( $codofides > 0 )
				$mSQL .=' AND a.codofides = '.$this->db->escape($codofides);

			$mSQL .=' ORDER by a.codofiorg, a.codofides';
			$mSQL .=' LIMIT 40';

			$query = $this->db->query($mSQL);
			$rs = "";

			if ($query->num_rows() > 0){
				$rs .= "\n<div style='width:500px;'>\n";
				$rs  = "<table id='bprecios'>\n";
				$rs .= "<thead>\n";
				$rs .= "\t<tr>\n";
				$rs .= "<th>$titu</th>\n";
				$rs .= "<th>Seguro</th>\n";
				$rs .= "<th>Tasa</th>\n";
				$rs .= "<th>Ejecutivo</th>\n";
				$rs .= "<th>Total_E</th>\n";
				$rs .= "<th>Buscama</th>\n";
				$rs .= "<th>Total_B</th>\n";

				$rs .= "\t</tr>\n";
				$rs .= "</thead>\n";
				$rs .= "<tbody>\n";
				foreach( $query->result() as  $row ){
					$rs .= "\t<tr>\n";
					$rs .= "<td>".$row->desdes."</td>\n";
					$rs .= "<td style='text-align:right'>".$row->seguro."</td>\n";
					$rs .= "<td align='right'>".$row->tasa."</td>\n";
					$rs .= "<td align='right'>".$row->buscama."</td>\n";
					$rs .= "<td align='right'>".$row->total_buscama."</td>\n";
					$rs .= "<td align='right'>".$row->ejecutivo."</td>\n";
					$rs .= "<td align='right'>".$row->total_ejecutivo."</td>\n";
					$rs .= "\t</tr>\n";
				}
				$rs .= "</tbody>\n";
				$rs .= "</table>\n";
				$rs .= "</div>\n";
			}
		}else{
			$rs ='';
		}

		$salida .= "\n<table><tr>";
		//$salida .= "<td>Origen: ".$form->codofiorg->output."</td><td>Destino: ".$form->codofides->output."</td>";
		$salida .= "<td>Origen: ".$form->codofiorg->output."</td>";
		$salida .= "<td>".$form->submit->output."</td>";
		$salida .= "</tr></table>";
		$salida .= '</form>';

		$salida .= $rs;

		$salida .= '
<script type="text/javascript">
		$(document).ready(function() {
			tableToGrid("#bprecios",{
				width:"600",
				height:"250",
				colModel: [
				{name: "'.$titu.'",   id: "'.$titu.'",   width: 200 },
				{name: "Seguro",    id: "Seguro",    width:  50, align:"center" },
				{name: "Tasa",      id: "Tasa",      width:  50, align:"center" },
				{name: "Ejecutivo", id: "Ejecutivo", width:  70, align:"right" },
				{name: "Total_E",    id: "Total_E",    width:  70, align:"right", title: "Total" },
				{name: "Buscama",   id: "Buscama",   width:  70, align:"right" },
				{name: "Total_B",    id: "Total_B",    width:  70, align:"right", title: "Total" },
				]

			 });
		})
</script>';

		$salida .= 	"Seleccione un Origen";

		$salida .= '</center></body>';
		$salida .= '</html>';

		echo $salida;

	}

	function saldocuenta(){
		$cuenta  = $this->input->get('cuenta');
		$dbcuenta= $this->db->escape($cuenta);

		$mSQL="SELECT SUM(debe-haber) AS val FROM datasis.itcasi WHERE cuenta=${dbcuenta}";
		$monto=$this->datasis->dameval($mSQL);
		echo $monto;
	}

	function traeordc(){
		$cod_prv = $this->input->post('cod_prv');
		header('Content-Type: application/json');
		if(!empty($cod_prv)){
			$dbcod_prv = $this->db->escape($cod_prv);
			$mSQL="SELECT id,numero,fecha,montotot AS monto,peso  FROM ordc WHERE status IN ('PE','BA') AND proveed=${dbcod_prv} ORDER BY fecha DESC LIMIT 10";
			$query = $this->db->query($mSQL);
			if($query->num_rows()>0){
				echo json_encode($query->result());
			}else{
				echo json_encode(null);
			}
		}else{
			echo json_encode(null);
		}
	}

	function traeitordc(){
		$ids = $this->input->post('ids');
		if(is_array($ids)){
			header('Content-Type: application/json');
			$sel=array('TRIM(c.descrip) AS descrip', 'TRIM(c.codigo) AS codigo', 'c.precio1', 'c.precio2', 'c.precio3', 'c.precio4',
				'c.iva','c.existen','c.tipo','c.peso','c.ultimo', 'c.pond','c.activo','SUM(b.cantidad-b.recibido) AS cantidad');
			$this->db->select($sel);
			$this->db->from('ordc   AS a');
			$this->db->join('itordc AS b','a.numero=b.numero');
			$this->db->join('sinv   AS c','b.codigo=c.codigo');
			$this->db->where_in('a.id',$ids);
			$this->db->group_by('c.codigo');
			$this->db->where_in('a.status',array('PE','BA'));

			$query = $this->db->get();
			if($query->num_rows() > 0){
				$rt=array();
				foreach ($query->result_array() as $row){
					$row['descrip']=$this->en_utf8($row['descrip']);
					$rt[]=$row;
				}

				echo json_encode($rt);
			}else{
				echo json_encode(null);
			}
		}else{
			echo json_encode(null);
		}
	}

	function rifrep($tipo='C'){
		$rifci  = $this->input->post('rifci');
		$codigo = trim($this->input->post('codigo'));
		$rt    = array('rt'=>false,'msj'=>'');

		if($rifci!==false){

			$rifci   = strtoupper(str_replace(array('-',' ','.'),'',$rifci));
			$dbrifci = $this->db->escape($rifci);

			if($tipo=='C'){
				$mSQL="SELECT nombre FROM scli WHERE rifci=${dbrifci}";
				if(!empty($codigo)){
					$mSQL.=" AND cliente<>".$this->db->escape($codigo);
				}
			}else{
				$mSQL="SELECT nombre FROM sprv WHERE rif=${dbrifci}";
				if(!empty($codigo)){
					$mSQL.=" AND proveed<>".$this->db->escape($codigo);
				}
			}

			$query = $this->db->query($mSQL);

			if($query->num_rows() > 0){
				$rt['rt'] = true;
				$rt['msj'] = '<b>Ya existen registros con el mismo rif o c&eacute;dula:</b> <ul>';
				foreach ($query->result() as $row){
					$rt['msj'] .= '<li>'.htmlspecialchars($this->en_utf8($row->nombre)).'</li>';
				}
				$rt['msj'] .= '</ul> Puede estar repitiendo el registro';
			}
		}
		echo json_encode($rt);
	}

	function en_utf8($str){
		if($this->config->item('charset')=='UTF-8' && $this->db->char_set=='latin1'){
			return utf8_encode($str);
		}else{
			return $str;
		}
	}

	function get_municipio(){
		$entidad = $this->input->post('estado');
		echo "<option value=''>Seleccione un Municipio ($entidad)</option>";
		if(!empty($entidad)){
			$mSQL = $this->db->query("SELECT codigo, municipio FROM municipios WHERE entidad=$entidad ORDER BY municipio");
			if($mSQL){
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->codigo."'>".$fila->municipio."</option>";
				}
			}
		}
	}

	function get_parroquia(){
		$entidad   = $this->input->post('entidad');
		$municipio = $this->input->post('municipio');
		if(!empty($municipio) && !empty($entidad)){
			$mSQL=$this->db->query("SELECT codigo, parroquia FROM parroquias WHERE entidad=$entidad AND municipio=$municipio ORDER BY parroquia");
			if($mSQL){
				echo "<option value=''>Seleccione una Parroquia</option>";
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->codigo."'>".$fila->parroquia."</option>";
				}
			}
		}else{
			echo "<option value=''>Seleccione un Municipio primero</option>";
		}
	}

	function get_evento(){
		$campana = $this->input->post('campana');
		if(!empty($campana)){
			$mSQL=$this->db->query("SELECT id, nombre FROM proevent WHERE campana=$campana ORDER BY nombre");
			if($mSQL){
				echo "<option value=''>Seleccione un Evento</option>";
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->id."'>".$fila->nombre."</option>";
				}
			}
		}else{
			echo "<option value=''>Seleccione una Campa&acute;a primero</option>";
		}
	}


	function get_asislista(){
		$campana = $this->input->post('campana');
		$evento  = $this->input->post('evento');
		if( $campana>0 && $evento > 0 ){
			$mSQL=$this->db->query("SELECT * FROM proasiste WHERE campana=$campana AND evento=$evento ORDER BY id DESC");
			if($mSQL){
				echo "<table width='100%'>";
				foreach($mSQL->result() AS $fila ){
					echo "<tr><td>".$fila->cedula."</td><td>".$fila->nombre."</td><td>".$fila->telefono."</td></tr>";
				}
			}
		}else{
			echo "</table>";
		}
	}


	//******************************************************************
	//   Mostrar Estadistica
	//
	function codesta($mCOD = ''){
		$mRET    = 0;
		$mSQL    = '';
		$mVSEMA  = 0;
		$mVMES   = 0;
		$mV3MES  = 0;
		$mV6MES  = 0;
		$mFRACCI = 0;
		$salida  = ''; 
		
		if ($mCOD == '') $mCOD = $this->input->post('mCOD');

		$mCODIGO = $this->db->escape($mCOD);

		$mSQL  = "
		SELECT MID(CONCAT(TRIM(a.proveed),' ',b.nombre),1,20) proveed, a.fecha, a.cantidad, a.costo 
		FROM itscst a JOIN sprv b ON a.proveed=b.proveed
		WHERE a.codigo=${mCODIGO} ORDER BY a.fecha DESC LIMIT 4";
		$query = $this->db->query($mSQL);

		if ($query->num_rows() > 0){
			$salida  = "<table id='ucompra' style='border-collapse:collapse;padding:0px;width:100%;' >\n";
			$salida .= "<thead style='background:#FFFF00;border-bottom: 1px solid black;'>\n";
			$salida .= "\t<tr>\n";
			$salida .= "<th colspan='4'>ULTIMAS COMPRAS</th>\n";
			$salida .= "\t</tr><tr>\n";
			$salida .= "<th>Fecha</th>\n";
			$salida .= "<th>Cantidad</th>\n";
			$salida .= "<th>Costo</th>\n";
			$salida .= "\t</tr>\n";
			$salida .= "</thead>\n";
			$salida .= "<tbody>\n";
			foreach( $query->result() as  $row ){
				$salida .= "\t<tr style='background:#DFDFDF;'>\n";
				$salida .= "<td align='center' colspan='3'  >".$row->proveed." </td>\n";
				$salida .= "\t</tr><tr style='background:#FFFFFF;border-bottom: 1px solid black;'>\n";
				$salida .= "<td align='left'  >".$row->fecha."   </td>\n";
				$salida .= "<td align='center'>".round($row->cantidad)."</td>\n";
				$salida .= "<td align='right' >".$row->costo."   </td>\n";
				$salida .= "\t</tr>\n";
			}
			$salida .= "</tbody>\n";
			$salida .= "</table>\n";
		}

		$mSQL   = "
		SELECT SUM(cantidad) 
		FROM costos 
		WHERE origen='3I' AND codigo=${mCODIGO} AND fecha >= ADDDATE(CURDATE(),-365)";
		$mV6MES = $this->datasis->dameval($mSQL)/2;

		$mSQL   = "
		SELECT SUM(cantidad) 
		FROM costos 
		WHERE origen='3I' AND codigo=${mCODIGO} AND fecha >= ADDDATE(CURDATE(),-180)";
		$mV3MES = $this->datasis->dameval($mSQL)/2;

		$mSQL   = "
		SELECT SUM(cantidad) 
		FROM costos 
		WHERE origen='3I' AND codigo=${mCODIGO} AND fecha >=ADDDATE(CURDATE(),-90)";
		$mVMES  = $this->datasis->dameval($mSQL)/3;

		$mSQL   = "
		SELECT SUM(cantidad) 
		FROM costos 
		WHERE origen='3I' AND codigo=${mCODIGO} AND fecha >= ADDDATE(CURDATE(),-60)";
		$mVSEMA = $this->datasis->dameval($mSQL)/8;

		$salida .= "<table style='background:#FFBF00;border: 1px solid black;width:100%;'>";
		$salida .= "<tr><td>Venta por Semana</td><td align='right'>".round($mVSEMA,2)."</td></tr>";
		$salida .= "<tr><td>Venta por Mes    </td><td align='right'>".round($mVMES,2)."</td></tr>";
		$salida .= "<tr><td>Venta por 3 Meses</td><td align='right'>".round($mV3MES,2)."</td></tr>";
		$salida .= "<tr><td>Venta por 6 Meses</td><td align='right'>".round($mV6MES,2)."</td></tr>";
		$salida .= "</tabla>";

		$mFRACCI = $this->datasis->dameval("SELECT fracci FROM sinv WHERE codigo=${mCODIGO} ");
		$mPEDIDO = round($this->datasis->dameval("SELECT exord  FROM sinv WHERE codigo=${mCODIGO} "));

		$mSQL = "SELECT alma, existen FROM itsinv WHERE codigo=${mCODIGO} LIMIT 4 ";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$salida .= "<br><table id='texiste' style='border-collapse:collapse;padding:0px;width:100%;' >\n";
			$salida .= "<thead style='background:#CFCFCF;border-bottom: 1px solid black;'>\n";
			$salida .= "\t<tr>\n";
			$salida .= "<th>Almacen</th>\n";
			$salida .= "<th>Existencia</th>\n";
			$salida .= "\t</tr>\n";
			$salida .= "</thead>\n";
			$salida .= "<tbody>\n";
			foreach( $query->result() as  $row ){
				$salida .= "\t<tr style='background:#EFEFEF;'>\n";
				$salida .= "<td align='center'>".$row->alma."   </td>\n";
				$salida .= "<td align='right' >".$row->existen."</td>\n";
				$salida .= "\t</tr>\n";
			}
			$salida .= "</tbody>\n";
			$salida .= "</table>\n";
		}

		$mSQL = "SELECT DATE_FORMAT(b.fecha,'%d/%c/%Y') fecha, b.numero, b.status, b.proveed, cantidad-recibido saldo FROM itordc a JOIN ordc b ON a.numero=b.numero AND a.codigo=${mCODIGO} AND b.status IN ('PE','BA') ORDER BY a.numero DESC LIMIT 2";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$salida .= "<table id='texiste' style='border-collapse:collapse;padding:0px;width:100%;background:#74DF00;' >\n";
			$salida .= "<thead style='background:#CFCFCF;border-bottom: 1px solid black;'>\n";
			$salida .= "\t<tr>\n";
			$salida .= "<th colspan='4'>TOTAL PEDIDOS ".$mPEDIDO."</th>\n";
			$salida .= "\t</tr><tr>\n";
			$salida .= "<th>Prov/</th>\n";
			$salida .= "<th>Fecha</th>\n";
			$salida .= "<th>Cant.</th>\n";
			$salida .= "\t</tr>\n";
			$salida .= "</thead>\n";
			$salida .= "<tbody>\n";
			foreach( $query->result() as  $row ){
				$salida .= "\t<tr style='background:#EFEFEF;'>\n";
				$salida .= "<td align='center'>".$row->proveed."   </td>\n";
				$salida .= "<td align='right' >".$row->fecha."</td>\n";
				$salida .= "<td align='right' >".$row->saldo."</td>\n";
				$salida .= "\t</tr>\n";
			}
			$salida .= "</tbody>\n";
			$salida .= "</table>\n";
		}
		echo $salida;
	}
}
