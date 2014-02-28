<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Rpcserver extends Controller {

	function index(){
		$this->load->library('xmlrpc');
		$this->xmlrpc->xmlrpc_defencoding=$this->config->item('charset');
		$this->load->library('xmlrpcs');
		$this->xmlrpcs->xmlrpc_defencoding=$this->config->item('charset');

		$config['functions']['sprecios']  = array('function' => 'Rpcserver.precio_supermer');
		$config['functions']['ttiket']    = array('function' => 'Rpcserver.traer_tiket');
		$config['functions']['cea']       = array('function' => 'Rpcserver.ComprasEmpresasAsociadas');
		$config['functions']['dea']       = array('function' => 'Rpcserver.DevolucionesEmpresasAsociadas');
		$config['functions']['consiea']   = array('function' => 'Rpcserver.ConsignacionesEmpresasAsociadas');
		$config['functions']['consinu']   = array('function' => 'Rpcserver.NumConsignacionesEmpresasAsociadas');
		$config['functions']['montven']   = array('function' => 'Rpcserver.MontosVentas');
		$config['functions']['ventanainf']= array('function' => 'Rpcserver.ventanainf');

		$this->xmlrpcs->initialize($config);
		$this->xmlrpcs->serve();
	}

	function precio_supermer($request){
		$parameters = $request->output_parameters();

		$codigo=$parameters['0'];
		$query = $this->db->query("SELECT precio1,precio2,precio3,precio4,precio5, descrip, barras FROM maes WHERE codigo=".$this->db->escape($codigo));

		if ($query->num_rows() > 0){
			$row = $query->row();
			$response = array(
					array(
						0 => $row->precio1,
						1 => $row->precio2,
						2 => $row->precio3,
						3 => $row->precio4,
						4 => $row->precio5,
						5 => $row->descrip,
						6 => $row->barras),
					'struct');
		}else{
			$response = array(
				array(),
				'struct');
		}
		return $this->xmlrpc->send_response($response);
	}

	function traer_tiket($request){
		$parameters = $request->output_parameters();
		$fechad=$parameters['0'];

		$query = $this->db->query("SELECT id,padre,pertenece,prioridad,usuario,contenido,estampa,actualizado,estado FROM tiket WHERE estampa>'$fechad' AND estampa<=NOW() AND usuario<>'TRANF'");
		//$query = $this->db->query("SELECT id,padre,pertenece,prioridad,usuario,contenido,estampa,actualizado,estado FROM tiket LIMIT 3");

		$tiket=array();
		if ($query->num_rows() > 0){
			foreach ($query->result_array() as $row){
				foreach($row AS $ind=>$val){
					$row[$ind]=base64_encode($val);
				}
				$tiket[] = serialize($row);
			}
		}else{
			$response = array(array(),'struct');
		}
		$response = array($tiket,'struct');
		return $this->xmlrpc->send_response($response);
	}

	function _comprasdev($request,$tipo_doc){
		$dbtipo_doc = $this->db->escape($tipo_doc);
		$parameters = $request->output_parameters();

		$ult_ref=intval($parameters['0']);
		$cod_cli=$parameters['1'];
		$usr    =$parameters['2'];
		$pwd    =$parameters['3'];
		if(isset($parameters['4']))
			$uniq = $parameters['4'];
		else
			$uniq = false;

		$op = ($uniq) ? '=' : '>';
		$cant   =5;

		$compras=array();
		if($this->secu->cliente($usr,$pwd)){
			$mSQL="SELECT numero,fecha,vence,TRIM(nfiscal) AS nfiscal,totals,totalg,iva,exento,tasa,reducida,sobretasa,montasa,monredu,monadic FROM sfac WHERE cod_cli=? AND numero $op ? AND tipo_doc=${dbtipo_doc} LIMIT ${cant}";
			$query = $this->db->query($mSQL,array($usr,$ult_ref));
			$barr_exis=$this->db->table_exists('barraspos');
			//memowrite($this->db->last_query(),'B2B');
			if ($query->num_rows() > 0){
				$pivot=array();
				foreach ($query->result_array() as $row){
					$numero=$row['numero'];
					//Prepara el encabezado
					foreach($row AS $ind=>$val){
						$row[$ind]=base64_encode($val);
					}
					$pivot['scst']=$row;

					//Prepara los articulos
					$it=array();
					$mmSQL="SELECT TRIM(a.codigoa) AS codigoa,TRIM(a.desca) AS desca,a.cana,a.preca,a.tota AS tota,a.iva,
					TRIM(b.barras) AS barras,b.precio1,b.precio1 AS precio2,b.precio1 AS precio3,b.precio1 AS precio4,
					b.unidad, b.tipo, b.tdecimal
						FROM sitems AS a
						JOIN sinv AS b ON a.codigoa=b.codigo
						WHERE numa=? AND tipoa=${dbtipo_doc}";
					$qquery = $this->db->query($mmSQL,array($numero));
					foreach ($qquery->result_array() as $rrow){
						foreach($rrow AS $ind=>$val){
							$rrow[$ind]=base64_encode($val);
						}
						if($barr_exis){
							$sql= "SELECT GROUP_CONCAT(suplemen SEPARATOR '|') AS suplemen FROM barraspos WHERE suplemen REGEXP '^[0-9]+$' AND codigo=".$this->db->escape($rrow['codigoa']);
							$suple=$this->datasis->dameval($sql);
							if(strlen($suple)>0){
								$rrow['suplemen'] = base64_encode($suple);
							}
						}
						$it[]=$rrow;
					}
					$pivot['itscst']=$it;
					$compras[]=serialize($pivot);
				}
			}
		}else{
			return $this->xmlrpc->send_error_message('100', 'Acceso Negado');
		}

		$response = array($compras,'struct');
		return $this->xmlrpc->send_response($response);
	}


	function ComprasEmpresasAsociadas($request){
		return $this->_comprasdev($request,'F');
	}

	function DevolucionesEmpresasAsociadas($request){
		return $this->_comprasdev($request,'D');
	}

	function ConsignacionesEmpresasAsociadas($request){
		$parameters = $request->output_parameters();

		$ult_ref=intval($parameters['0']);
		$cod_cli=$parameters['1'];
		$usr    =$parameters['2'];
		$pwd    =$parameters['3'];
		$cant   =5;

		if($this->db->table_exists('scon') && $this->db->table_exists('itscon')){
			$consignacion=array();
			if($this->secu->cliente($usr,$pwd)){
				$mSQL="SELECT numero,fecha,status,observ1,stotal,impuesto,gtotal,peso,tipod,id
					FROM scon
					WHERE clipro=? AND numero > ?
					AND tipo='C' AND origen='L' LIMIT $cant";
				$query = $this->db->query($mSQL,array($usr,$ult_ref));
				//memowrite($this->db->last_query(),'B2Ba');
				if ($query->num_rows() > 0){
					$pivot=array();
					foreach ($query->result_array() as $row){
						$id=$row['id'];
						//Prepara el encabezado
						foreach($row AS $ind=>$val){
							$row[$ind]=base64_encode($val);
						}
						$pivot['scon']=$row;

						//Prepara los articulos
						$it=array();
						$mmSQL="SELECT a.numero,TRIM(a.codigo) AS codigo,TRIM(a.desca) AS desca,SUM(a.cana) AS cana ,
							a.precio,SUM(a.importe) AS importe,a.iva,b.barras,b.precio1,b.precio2 AS precio2,
							b.precio3 AS precio3,b.precio4 AS precio4,b.unidad, b.tipo, b.tdecimal
							FROM itscon AS a JOIN sinv AS b ON a.codigo=b.codigo
							WHERE a.id_scon=? GROUP BY a.codigo";
						$qquery = $this->db->query($mmSQL,array($id));
						//memowrite($this->db->last_query(),'B2Ba');
						foreach ($qquery->result_array() as $rrow){
							foreach($rrow AS $ind=>$val){
								$rrow[$ind]=base64_encode($val);
							}
							$it[]=$rrow;
						}
						$pivot['itscon']=$it;

						$consignacion[]=serialize($pivot);
					}
				}
			}else{
				return $this->xmlrpc->send_error_message('100', 'Acceso Negado');
			}
		}else{
			return $this->xmlrpc->send_error_message('101', 'Servicio no esta disponible');
		}

		$response = array($consignacion,'struct');
		return $this->xmlrpc->send_response($response);
	}

	function NumConsignacionesEmpresasAsociadas($request){
		$parameters = $request->output_parameters();

		$asoc   =$parameters['0'];
		$cod_cli=$parameters['1'];
		$usr    =$parameters['2'];
		$pwd    =$parameters['3'];

		if($this->db->table_exists('scon')){
			if($this->secu->cliente($usr,$pwd)){
				$mSQL="SELECT numero FROM scon WHERE clipro=? AND asociado = ? AND origen='R' LIMIT 1";
				$query = $this->db->query($mSQL,array($usr,$asoc));

				if ($query->num_rows() > 0){
					$row = $query->row_array();
					$numero=array($row['numero']);
				}else{
					$numero=array();
				}
			}else{
				return $this->xmlrpc->send_error_message('100', 'Acceso Negado');
			}
		}else{
			return $this->xmlrpc->send_error_message('101', 'Servicio no esta disponible');
		}
		$response = array($numero, 'array');
		return $this->xmlrpc->send_response($response);
	}

	function MontosVentas($request){
		$parameters = $request->output_parameters();

		$fecha = $this->db->escape($parameters['0']);
		$clave = $parameters['1'];

		$mSQL="SELECT
		 SUM(totals*IF(tipo_doc='D',-1,1)) AS totales
		 FROM sfac
		 WHERE fecha=$fecha AND tipo_doc<>'X' AND MID(numero,1,1)<>'_'";
		$row=$this->datasis->damerow($mSQL);

		$fdesde=date('Ym');
		$mSQL="SELECT
		 SUM(totals*IF(tipo_doc='D',-1,1)) AS acumulado
		 FROM sfac
		 WHERE fecha >=${fdesde}01 AND fecha<=$fecha AND tipo_doc<>'X' AND MID(numero,1,1)<>'_'";
		$row2=$this->datasis->damerow($mSQL);

		$data=array('diaria'=>$row['totales'],'acumulada'=>$row2['acumulado']);

		$response = array($data,'struct');
		return $this->xmlrpc->send_response($response);
	}

	function ProductosEmpresasAsociadas($request){
		/*$parameters = $request->output_parameters();

		$codigo =$parameters['0'];
		$usr    =$parameters['1'];
		$pwd    =$parameters['2'];

		$mSQL="SELECT codigo,grupo,descrip,descrip2,unidad,ubica,tipo,clave,comision,enlace,prov1,prepro1,pfecha1,prov2,prepro2,pfecha2,prov3,prepro3,pfecha3,pond,ultimo,pvp_s,pvp_bs,pvpprc,contbs,contprc,mayobs,mayoprc,exmin,exord,exdes,existen,fechav,fechac,iva,fracci,codbar,barras,exmax,margen1,margen2,margen3,margen4,base1,base2,base3,base4,precio1,precio2,precio3,precio4,serial,tdecimal,activo,dolar,redecen,formcal,fordeci,garantia,costotal,fechac2,peso,pondcal,alterno,aumento,modelo,marca,clase,oferta,fdesde,fhasta,derivado,cantderi,ppos1,ppos2,ppos3,ppos4,linea,depto,id,gasto,bonifica,bonicant,standard FROM sinv WHERE codigo=?";
		$query = $this->db->query($mSQL,array($codigo));

		$compras=array();
		if ($query->num_rows() > 0){
			foreach ($query->result_array() as $row){
				foreach($row AS $ind=>$val){
					$row[$ind]=base64_encode($val);
				}
				$sinv[] = serialize($row);
			}
		}

		$response = array($sinv,'struct');
		return $this->xmlrpc->send_response($response);*/
	}

	function ventanainf(){
		$data[]=array('ender','ochoa');
		$response = array($data,'struct');
		return $this->xmlrpc->send_response($response);
	}
}
