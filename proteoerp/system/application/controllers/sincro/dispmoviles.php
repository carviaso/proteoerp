<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
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

	function sincro($tabla,$uuid){
		session_write_close();
		header('Cache-Control: no-cache, must-revalidate');
		header('Content-type: application/json');
		$dbuuid = $this->db->escape($uuid);

		$vend=$this->datasis->dameval('SELECT vendedor FROM usuario WHERE uuid='.$dbuuid);

		if(empty($vend)){
			echo '[]';
			return '';
		}
		$dbvend = $this->db->escape($vend);

		if($this->db->char_set=='latin1'){
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

					//Convierte los caracteres de us-ascii
					$val =str_replace(chr(165),utf8_decode('Ñ'),$val);
					$val =str_replace(chr(164),utf8_decode('ñ'),$val);
					$val =str_replace(chr(166),utf8_decode('º'),$val);
					$val =str_replace(chr(167),utf8_decode('º'),$val);
					$val=utf8_encode($val);
					return $val;
				}
			};
		}else{
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
					//Convierte los caracteres de us-ascii
					$val =str_replace(chr(165),'Ñ',$val);
					$val =str_replace(chr(164),'ñ',$val);
					$val =str_replace(chr(166),'º',$val);
					$val =str_replace(chr(167),'º',$val);
					return $val;
				}
			};
		}

		if($tabla=='config'){
			$nombre  = $escape($this->datasis->dameval('SELECT nombre FROM vend WHERE vendedor='.$dbvend));
			$clave   = sha1($this->datasis->traevalor('CLAVESFAC'));
			$minpre  = intval($this->datasis->traevalor('SFACPRECIOMINIMO'));
			if($this->datasis->traevalor('CERIXCAMBIAPRECIO','Permite cambiar los precios en Cerix (S,N)')=='N'){
				$cprecio = 0;
			}else{
				$cprecio = 1;
			}

			$perpre  = 0;
			$rt=array(
				$clave  ,// v_pclave
				$cprecio,// v_cprecio
				$nombre ,// v_nvende
				$minpre ,// v_minpre
				$perpre  // v_perpre
			);

			echo json_encode(array($rt));
			return '';
		}

		$almacen = $this->datasis->dameval('SELECT almacen FROM usuario WHERE uuid='.$dbuuid);
		if(empty($almacen)){
			$existen='a.existen';
			$join   ='';
		}else{
			$existen='COALESCE(b.existen,0)';
			$join   ='LEFT JOIN itsinv AS b ON a.codigo=b.codigo AND b.alma='.$this->db->escape($almacen);
		}

		$mSQL = array();
		$mSQL['sinv'] = "SELECT
			id,
			TRIM(a.codigo)  AS codigo,
			TRIM(a.descrip) AS descrip,
			a.base1,
			a.base2,
			a.base3,
			a.base4,
			a.ultimo AS costo,
			a.iva    AS iva,
			a.bonifica,
			a.bonicant,
			UNIX_TIMESTAMP(a.fdesde) AS fdesde,
			UNIX_TIMESTAMP(a.fhasta) AS fhasta,
			${existen}*(a.activo='S') AS existen,
			TRIM(a.clave) AS clave,
			a.tdecimal,
			a.activo,
			a.exdes AS pedido
			FROM sinv AS a ${join}
			WHERE a.tipo='Articulo' AND a.base1>0 AND a.base2>0 AND a.base3>0 AND a.base3>0 AND a.ultimo>0 AND a.activo='S'";

		$mSQL['scli'] = "SELECT a.id,
			TRIM(a.cliente) AS cliente, TRIM(a.nombre) AS nombre,CONCAT_WS('-',TRIM(a.dire11),TRIM(a.dire12)) AS direc,
			TRIM(a.ciudad) AS ciudad,TRIM(a.telefono) AS telefono,TRIM(a.rifci) AS rifci,TRIM(a.email) AS email,
			TRIM(a.repre) AS repre,TRIM(a.tipo) AS tipo,
			COALESCE(SUM((b.monto-b.abonos)*(b.vence<=CURDATE())),0) AS vsaldo,
			0 AS csaldo,formap
			,COALESCE((SELECT SUM(aa.tipo_doc='F') FROM sfac AS aa WHERE cod_cli=a.cliente AND fecha>=CONCAT(EXTRACT(YEAR_MONTH FROM CURDATE()),'01')),0) AS numfac
			,COALESCE((SELECT SUM(aa.tipo_doc='D') FROM sfac AS aa WHERE cod_cli=a.cliente AND fecha>=CONCAT(EXTRACT(YEAR_MONTH FROM CURDATE()),'01')),0) AS numdev

			FROM scli AS a
			LEFT JOIN smov AS b ON a.cliente=b.cod_cli AND b.tipo_doc NOT IN ('AB','NC','AN') AND b.monto>b.abonos
			WHERE a.vendedor=${dbvend}
			GROUP BY a.cliente
			ORDER BY a.nombre LIMIT 1000";

		$mSQL['tarjeta'] = "SELECT id, TRIM(tipo) AS tipo,TRIM(nombre) AS nombre,tipo IN ('CH','DE') AS pideban FROM tarjeta";

		$mSQL['tban']    = "SELECT a.id,TRIM(cod_banc) AS cod_banc,TRIM(nomb_banc) AS nom_banc FROM tban";

		$sql  = $mSQL[$tabla];
		$data = $itdata = array();

		$query = $this->db->query($sql);

		//$itdata['sql'] = $sqlite[$tabla];
		foreach ($query->result_array() as $row){
			$itdata = array_map($escape,array_values($row));
			if($tabla=='scli'){
				if($row['vsaldo']>0){
					$dbscli = $this->db->escape($row['cliente']);
					$mSQL="SELECT CONCAT(tipo_doc,TRIM(numero)) AS Numero,DATE_FORMAT(fecha, '%Y/%m/%d') AS Fecha, ABS(DATEDIFF(fecha,CURDATE())) AS Dias,monto-abonos AS Saldo FROM smov WHERE tipo_doc NOT IN ('AB','NC','AN') AND monto>abonos AND vence<=CURDATE() AND cod_cli=${dbscli} ORDER BY fecha DESC";
					$qq = $this->db->query($mSQL);
					if($qq->num_rows() > 0){
						$itdata[]=json_encode($qq->result());
					}else{
						$itdata[]='';
					}

				}else{
					$itdata[]='';
				}
			}

			$data[] = $itdata;
			//$itdata['data'] = array_map($escape,array_values($row));
			//$data[] = $itdata;
		}

		echo json_encode($data);
		logusu('dispmoviles',"Sincronizo ${vend} ${uuid}");
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
	function pfac($uuid){
		session_write_close();
		$this->load->library('rapyd');

		//Para probar
		//$_POST = array(
		//	'idscli'  => 486,
		//	'observa' => '',
		//	'idsinv0' => 5198,
		//	'cana_0'  => 5,
		//	'preca_0' => 141.07
		//);
		//***********

		$sal=array('error'=>'','op'=>false,'numero'=>'');
		$i = 0;
		$rt=$this->secu->login_uuid($uuid);
		if($rt===false){
			$sal['error'] = 'Error de autentificacion.';
			$sal['op']    = false;
			$sal['numero']= '';
			echo json_encode($sal);
			return false;
		}

		$idscli  = $this->input->post('idscli');
		if(empty($idscli)){
			$sal['error'] = 'Error en la data.';
			$sal['op']    = false;
			$sal['numero']= '';
			echo json_encode($sal);
			return false;
		}

		$dbidscli= $this->db->escape($idscli);
		$mSQL="SELECT cliente,nombre,rifci,dire11,tipo FROM scli WHERE id=${dbidscli}";
		$sclirow = $this->datasis->damerow($mSQL);
		if(count($sclirow)!=5){
			$sal['error'] = 'Cliente no existe';
			$sal['op']    = false;
			$sal['numero']= '';
			echo json_encode($sal);
			return false;
		}
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
		$_POST['status']     = 'A';

		$_POST['totals'] = $_POST['iva'] = $_POST['totalg'] = $_POST['peso'] = $i = 0;

		while(1){
			if(!isset($_POST['idsinv'.$i])) break;
			$idsinv=$this->db->escape($_POST['idsinv'.$i]);
			unset($_POST['idsinv'.$i]);
			$mSQL="SELECT codigo,descrip,precio1,precio2,precio3,precio4,iva,peso,tipo,ultimo,pond,formcal FROM sinv WHERE id=${idsinv}";
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
		if($i==0){
			$sal['error'] = 'Pedido sin articulos';
			$sal['op']    = false;
			$sal['numero']= '';
			echo json_encode($sal);
			return false;
		}

		$this->genesal=false;
		ob_start();
			pfac::dataedit();
			$_result=ob_get_contents();
		@ob_end_clean();
		$res = json_decode($_result,true);

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
