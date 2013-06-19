<?php
require_once(BASEPATH . 'application/controllers/ventas/pfac.php');
class Dispmoviles extends Controller {

	function Dispmoviles(){
		parent::Controller();
		//$this->load->library("rapyd");
		//$this->sucu=$this->datasis->traevalor('NROSUCU');
	}

	function index(){

	}

//***********************
//  Interfaces uri
//***********************
	function uri($clave,$metodo,$vend,$cajero){
		$obj='_'.$metodo;
		if(!method_exists($this,$obj)) show_404('page');
		//$this->$obj($vend);
	}

	function sincro($tabla,$uuid,$matriz){
		session_write_close();
		header('Cache-Control: no-cache, must-revalidate');
		header('Content-type: application/json');

		$vend=$this->datasis->dameval("SELECT vendedor FROM usuario WHERE uuid=".$this->db->escape($uuid));

		if(empty($vend)){
			echo '[]';
			return '';
		}
		$dbvend = $this->db->escape($vend);

		$escape = function($val){
			if(is_numeric($val)){
				$val=$val+0;
				if(is_infinite($val))
					return 0;
				else
					return $val;
			}elseif(is_null($val)){
				return '';
			}else{
				return $val;
			}
		};

		$mSQL = array();

		$mSQL['sinv'] = "SELECT id,
			TRIM(codigo) AS codigo, TRIM(descrip) AS descrip,
			base1,
			base2,
			base3,
			base4,
			ultimo AS costo, iva,1 AS bonifica,10 AS bonicant,
			UNIX_TIMESTAMP(fdesde) AS fdesde ,UNIX_TIMESTAMP(fhasta) AS fhasta,
			existen,TRIM(clave) AS clave,tdecimal
			FROM sinv
			WHERE activo='S' AND tipo='Articulo' AND base1>0 AND base2>0 AND base3>0 AND base3>0 AND ultimo>0";
		$mSQL['scli'] = "SELECT a.id,
			TRIM(a.cliente) AS cliente, TRIM(a.nombre) AS nombre,CONCAT_WS('-',TRIM(a.dire11),TRIM(a.dire12)) AS direc,
			TRIM(a.ciudad) AS ciudad,TRIM(a.telefono) AS telefono,TRIM(a.rifci) AS rifci,TRIM(a.email) AS email,
			TRIM(a.repre) AS repre,TRIM(a.tipo) AS tipo,
			COALESCE(SUM((b.monto-b.abonos)*(b.vence<=CURDATE())),0) AS vsaldo,
			0 AS csaldo,formap
			FROM scli AS a
			LEFT JOIN smov AS b ON a.cliente=b.cod_cli AND b.tipo_doc NOT IN ('AB','NC','AN') AND b.monto>b.abonos
			WHERE a.vendedor=$dbvend
			GROUP BY a.cliente
			ORDER BY a.nombre LIMIT 1000";
		$mSQL['tarjeta'] = "SELECT id, TRIM(tipo) AS tipo,TRIM(nombre) AS nombre,tipo IN ('CH','DE') AS pideban FROM tarjeta";
		$mSQL['tban']    = "SELECT a.id,TRIM(cod_banc) AS cod_banc,TRIM(nomb_banc) AS nom_banc FROM tban";

		$sqlite['sinv']    = 'INSERT OR REPLACE INTO sinv_'.$matriz.'    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?);';
		$sqlite['scli']    = 'INSERT OR REPLACE INTO scli_'.$matriz.'    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?);';
		$sqlite['tarjeta'] = 'INSERT OR REPLACE INTO tarjeta_'.$matriz.' VALUES (?,?,?,?);';
		$sqlite['tban']    = 'INSERT OR REPLACE INTO tban_'.$matriz.'    VALUES (?,?,?,?);';

		$sql  = $mSQL[$tabla];
		$data = $itdata = array();

		$query = $this->db->query($sql);

		//$itdata['sql'] = $sqlite[$tabla];
		foreach ($query->result_array() as $row){
			$data[] = array_map($escape,array_values($row));
			//$itdata['data'] = array_map($escape,array_values($row));
			//$data[] = $itdata;
		}

		echo json_encode($data);
	}

	function ping($uuid){
		$rt=$this->secu->login_uuid($uuid);
		if($rt===false){
			echo '0';
		}else{
			echo '1';
		}
	}

	//Recibe y guarda los pedidos de Cerix
	function pfac($uuid,$matri){
		$this->load->library('rapyd');
		$rt=$this->secu->login_uuid($uuid);
		if($rt===false){
			echo 0;
			return false;
		}

		$idscli=$this->db->escape($_POST['idscli']);
		$mSQL="SELECT cliente,nombre,rifci,dire11,tipo FROM scli WHERE id=$idscli";
		$sclirow = $this->datasis->damerow($mSQL);
		if(count($sclirow)!=5) return false;
		unset($_POST['idscli']);

		if(isset($_POST['observa'])){
			$observa=$_POST['observa'].' '.'Cerix '.$uuid;
		}else{
			$observa = '';
		}

		$_POST['btn_submit'] = 'Guardar';
		$_POST['fecha']      = date('d/m/Y');
		$_POST['vd']         = $this->secu->vendedor;
		$_POST['cod_cli']    = $sclirow['cliente'];
		$_POST['sclitipo']   = $sclirow['tipo'];
		$_POST['nombre']     = $sclirow['nombre'];
		$_POST['rifci']      = $sclirow['rifci'];
		$_POST['direc']      = $sclirow['dire11'];
		$_POST['observa']    = $observa;
		$_POST['observ1']    = '';
		$_POST['mmargen']    = 0;

		$_POST['totals'] = $_POST['iva'] = $_POST['totalg'] = $_POST['peso'] = $i = 0;

		while(1){
			if(!isset($_POST['idsinv'.$i])) break;
			$idsinv=$this->db->escape($_POST['idsinv'.$i]);
			unset($_POST['idsinv'.$i]);
			$mSQL="SELECT codigo,descrip,precio1,precio2,precio3,precio4,iva,peso,tipo,ultimo,pond,formcal FROM sinv WHERE id=$idsinv";
			$sinvrow = $this->datasis->damerow($mSQL);
			if(count($sinvrow)!=12) continue;

			//$_POST['cana_'.$i]    = 0;
			//$_POST['preca_'.$i]   = 66.75;
			$_POST['codigoa_'.$i] = $sinvrow['codigo'];
			$_POST['desca_'.$i]   = $sinvrow['descrip'];
			$_POST['dxapli_'.$i]  = '';
			$_POST['tota_'.$i]    = $_POST['cana_'.$i]*$_POST['preca_'.$i];
			$_POST['precio1_'.$i] = $sinvrow['precio1'];
			$_POST['precio2_'.$i] = $sinvrow['precio2'];
			$_POST['precio3_'.$i] = $sinvrow['precio3'];
			$_POST['precio4_'.$i] = $sinvrow['precio4'];
			$_POST['itiva_'.$i]   = $sinvrow['iva'];
			$_POST['sinvpeso_'.$i]= $sinvrow['peso'];
			$_POST['sinvtipo_'.$i]= $sinvrow['tipo'];
			$_POST['itcosto_'.$i] = $sinvrow['ultimo'];
			$_POST['itpvp_'.$i]   = $sinvrow['precio1'];
			$_POST['mmargen_'.$i] = 0;
			$_POST['pond_'.$i]    = $sinvrow['pond'];
			$_POST['ultimo_'.$i]  = $sinvrow['ultimo'];
			$_POST['formcal_'.$i] = $sinvrow['formcal'];
			$_POST['pm_'.$i]      = 0;
			$_POST['precat_'.$i]  = 0;

			$iva=round($_POST['tota_'.$i] *$_POST['itiva_'.$i]/100,2);
			$_POST['totals']    += $_POST['tota_'.$i]  ;
			$_POST['iva']       += $iva;
			$_POST['totalg']    += $_POST['tota_'.$i]+$iva ;

			$i++;
		}


		$this->genesal=false;
		ob_start();
			pfac::dataedit();
			$_result=ob_get_contents();
		@ob_end_clean();
		$res = json_decode($_result);

		$sal=array('error','op','numero');
		if($res['status']=='A'){
			$sal['error'] = '';
			$sal['op']    = true;
			$sal['numero']= $this->insert_numero;
		}else{
			$sal['error'] = $res['mensaje'];
			$sal['op']    = true;
			$sal['numero']= '';
		}
		echo json_encode($sal);

	}

	function _pre_insert($do){
		$this->load->library('rapyd');
		pfac::_pre_insert($do);
	}

	function _post_insert($do){
		$this->load->library('rapyd');
		pfac:: _post_insert($do);
	}

	function _pre_update($do){
		$this->load->library('rapyd');
		return false;
	}

	function _post_update($do){
		$this->load->library('rapyd');
		return false;
	}

	function _pre_delete($do){
		$this->load->library('rapyd');
		return false;
	}

	function _post_delete($do){
		return false;
	}

//***********************
// Metodos para exportar
//***********************
	function _data($vend){
		$metodos=array('sinv2','scli','tarjeta','tban');
		foreach($metodos AS $metodo){
			$obj='_'.$metodo;
			$this->$obj($vend);
			echo "{%%}\n";
		}
	}


	function _sinv($vend){
		set_time_limit(600);
		$query = $this->db->query("SELECT  codigo, descrip, precio1,precio2,precio3,precio4,margen1,margen2,margen3,margen4, base1,base2,base3,base4,ultimo AS costo, iva FROM sinv LIMIT 10");
		foreach ($query->result_array() as $row){
			$cadena=implode('{%}',$row)."\n";
			echo $cadena;
		}
	}

	function _sinv2($vend){
		set_time_limit(600);
		$query = $this->db->query("SELECT  codigo, descrip, base1,base2,base3,base4,ultimo AS costo, iva,1 AS bonifica,10 AS bonicant, UNIX_TIMESTAMP(fdesde),UNIX_TIMESTAMP(fhasta) FROM sinv WHERE activo='S' AND tipo='Articulo' AND base1>0 AND base2>0 AND base3>0 AND base3>0 AND ultimo>0  LIMIT 120");
		foreach ($query->result_array() as $row){
			$cadena=implode('{%}',$row)."\n";
			echo $cadena;
		}
	}

	function _scli($vend){
		set_time_limit(600);
		$query = $this->db->query("SELECT cliente, nombre,dire11,ciudad,telefono,rifci,email,repre,tipo, 0 AS vsaldo,0 AS csaldo,formap FROM scli ORDER BY nombre LIMIT 150");
		foreach ($query->result_array() as $row){
			$cadena=implode('{%}',$row)."\n";
			#$cadena=str_replace('','',$cadena);
		echo $cadena;
		}
	}

	function _tarjeta($vend){
		set_time_limit(600);
		$query = $this->db->query("SELECT tipo,nombre,tipo IN ('CH','DE') FROM tarjeta");
		foreach ($query->result_array() as $row){
			$cadena=implode('{%}',$row)."\n";
			echo $cadena;
		}
	}

	function _tban($vend){
		set_time_limit(600);
		$query = $this->db->query("SELECT cod_banc,nomb_banc FROM tban");
		foreach ($query->result_array() as $row){
			$cadena=implode('{%}',$row)."\n";
			echo $cadena;
		}
	}

}
