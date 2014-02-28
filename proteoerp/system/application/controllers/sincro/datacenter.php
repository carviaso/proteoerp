<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
require_once(APPPATH.'/controllers/sincro/importar.php');
class Datacenter extends Controller {

	var $timeout =900;
	var $noborra=false;

	function Datacenter(){
		parent::Controller();
		$this->geneticket=true;
		$this->load->helper('string');
		$this->load->library('rapyd');
		$this->load->library('encrypt');
		$this->sucu = $this->datasis->traevalor('NROSUCU');
		$this->clave=sha1($this->config->item('encryption_key'));

		$this->dir=reduce_double_slashes($this->config->item('uploads_dir').'/traspasos');
		//$this->dir='./uploads/traspasos/';
		$path=reduce_double_slashes(FCPATH.'/uploads/traspasos');
		if(!file_exists($path)) if(!mkdir($path)) exit("Error: no se pudo crear el directorio $path");
		if(!is_writable($path)) exit("Error: no tiene permisos de escritura en $path");
		if(empty($this->sucu)) redirect('supervisor/valores/dataedit/show/NROSUCU');
	}

	function index($fecha=null){
		$this->noborra=false;
		$this->_traedatacenter('*','datacenter',$fecha);
	}

	function traer($sucu,$metodo){
		$metodo='datacenter'.$metodo;
		$this->_traedatacenter($sucu,$metodo);
	}


	function _traedatacenter($psucu='*',$metodo,$fecha=null){
		$obj='_'.str_replace('_','',$metodo);
		if(empty($fecha)) $fecha = date('Ymd');
		//if(!method_exists(importar,$obj)) { echo "Error metodo $metodo no existe \n"; return false;}
		if(!$this->__chekfecha($fecha)){ echo "Error fecha no valida \n"; return false;}
		if($psucu!='*') $where='AND codigo ='.$this->db->escape($psucu); else $where='';

		$sucu  = $this->sucu;
		$query = $this->db->query("SELECT * FROM sucumon WHERE codigo<>$sucu AND activo='S' $where");

		if ($query->num_rows() > 0){
			$result=$query->result();
			foreach ($result as $row){
				$config['hostname'] = 'localhost';
				$config['username'] = 'datasis';
				$config['password'] = '';
				$config['database'] = trim($row->db_nombre);
				$config['dbdriver'] = 'mysql';
				$config['dbprefix'] = '';
				$config['pconnect'] = FALSE;
				$config['db_debug'] = TRUE;
				$config['cache_on'] = FALSE;
				$config['cachedir'] = "";
				$config['char_set'] = 'latin1';
				$config['dbcollat'] = 'latin1_swedish_ci';
				$this->db=$this->load->database($config,true);

				echo 'Sucursal '.$row->sucursal.' en '.$this->db->database.' '.dbdate_to_human($fecha).': '.$metodo;
				$rt=importar::$obj($row->codigo,$fecha);
				echo " $rt \n";
			}
		}
	}

//***********************
//  Metodos de Validacion
//***********************
	function __chekfecha($fecha){
		return importar::__chekfecha($fecha);
	}

//***********************
//  Metodos Generales
//***********************
	function __traerzip($sucu,$dir_url,$iden=null){
		$rt=importar::__traerzip($sucu,$dir_url,$iden);
		return $rt;
	}

	function __cargazip($nombre=null){
		$rt=importar::__cargazip($nombre);
		return $rt;
	}

}
