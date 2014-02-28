<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Sqlinex{

	var $ci;
	var $separador;
	var $ignore;
	var $limpiar;
	var $dupli;
	var $dupli_field;
	var $dupli_condi;

	function Sqlinex(){
		$this->ci =& get_instance();
		$this->ci->load->helper('xml');
		$this->ci->db->_escape_char='';
		$this->ci->db->_protect_identifiers=false;
		$this->separador='-#-';
		$this->ignore   =false;
		$this->limpiar  =true;
		$this->dupli    =false;
		$this->dupli_field=array();
		$this->dupli_condi='';
	}

	function onduply($campos,$condi=null){
		$this->dupli_field = $campos;
		$this->dupli_condi = $condi ;
		$this->ignore   =false;
		$this->limpiar  =false;
		$this->dupli    =true;
	}

	function import($arch){
		if(file_exists($arch)){
			$file=fopen($arch,"r");
			while (!feof($file)){
				$mSQL=fgets($file);
				if(strlen($mSQL)>0){
					$rt=$this->ci->db->simple_query($mSQL);
					if($rt===FALSE) memowrite($mSQL,'sqlinex');
				}
			}
			fclose($file);
		}else{
			show_error('Archivo no existe '.$arch);
		}
	}

	function export($datas,$nomb='sqlinex'){
		$nombre=$nomb.'.txt';
		header('Content-Type: "text/plain"');
		header('Content-Disposition: attachment; filename="'.$nombre.'"');
		header("Content-Transfer-Encoding: binary");
		header('Expires: 0');
		header('Pragma: no-cache');

		foreach($datas AS $data){
			if(isset($data['select']) AND count($data['select'])>0 )$this->ci->db->select($data['select']);
			if(isset($data['where'])) $this->ci->db->where($data['where'],NULL,FALSE); else $data['where']='';
			if(isset($data['distinc']) AND $data['distinc']) $this->ci->db->distinct();

			$this->ci->db->from($data['table']);
			$query = $this->ci->db->get();

			if ($query->num_rows() > 0){
				$mSQL="DELETE FROM $data[table]"; if(!empty($data['where'])) $mSQL.=' WHERE '.$data['where'];
				$mSQL.="\n";
				echo $mSQL;

				foreach ($query->result_array() as $row){
					$mSQL = $this->ci->db->insert_string($data['table'], $row);
					$mSQL.="\n";
					echo $mSQL;
				}
			}
		}
		exit();
	}

	function exportzip($datas,$nomb='sqlinex',$add_encrip=NULL){
		$nombre=tempnam('/tmp', $nomb);
		$handle = fopen($nombre, 'w');

		foreach($datas AS $data){
			if(isset($data['select']) AND count($data['select'])>0 )$this->ci->db->select($data['select']);
			if(isset($data['where'])) $this->ci->db->where($data['where'],NULL,FALSE); else $data['where']='';
			if(isset($data['distinc']) AND $data['distinc']) $this->db->distinct();

			$this->ci->db->from($data['table']);
			$query = $this->ci->db->get();

			if ($query->num_rows() > 0){
				if($this->limpiar){
					$mSQL="DELETE FROM $data[table]"; if(!empty($data['where'])) $mSQL.=' WHERE '.$data['where'];
					$mSQL.="\n";
					fwrite($handle, $mSQL);
				}

				foreach ($query->result_array() as $row){
					$mSQL = $this->ci->db->insert_string($data['table'], $row);
					if(isset($data['ignore']) && $data['ignore']){
						$mSQL='INSERT IGNORE '.substr($mSQL,6);
					}
					$mSQL.="\n";
					fwrite($handle, $mSQL);
				}
			}
		}
		fclose($handle);
		$firma=md5_file($nombre);
		$this->ci->load->library('encrypt');

		if(!empty($add_encrip)) $firma=$add_encrip.$this->separador.$firma;
		$firma=$this->ci->encrypt->encode($firma);
		$this->ci->load->library('zip');
		$this->ci->zip->add_data('firma.txt',$firma);

		$this->ci->zip->read_file($nombre);
		$this->ci->zip->download($nomb.'.zip');
		unlink($nombre);
	}

	function exportunbufferzip($datas,$nomb='sqlinex',$add_encrip=NULL){
		$nombre=tempnam('/tmp', $nomb);
		$handle = fopen($nombre, 'w');

		//ON DUPLICATE KEY UPDATE `cedula`=IF(VALUES(`modifi`)>`modifi`,VALUES(`cedula`),`cedula`),`direc1`=IF(VALUES(`modifi`)>`modifi`,VALUES(`direc1`),`direc1`)

		foreach($datas AS $data){
			if(isset($data['select']) AND count($data['select'])>0 )$this->ci->db->select($data['select']);
			if(isset($data['where'])) $this->ci->db->where($data['where'],NULL,FALSE); else $data['where']='';
			if(isset($data['distinc']) AND $data['distinc']) $this->ci->db->distinct();

			if(isset($data['join']) AND is_array($data['join'])){
				foreach($data['join'] AS $ddata){
					if(isset($ddata['side'])) $side=$ddata['side']; else $side=null;
					$this->ci->db->join($ddata['table'],$ddata['on'],$side);
				}
			}
			if(isset($data['wherejoin'])) $this->ci->db->where($data['wherejoin'],NULL,FALSE);

			$this->ci->db->from($data['table']);
			$mSQL=$this->ci->db->_compile_select();
			//echo $mSQL;
			$this->ci->db->_reset_select();
			//$query = $this->ci->db->get();
			//memowrite($mSQL);
			if($this->ci->db->dbdriver=='mysqli'){
				$query= mysqli_query($this->ci->db->conn_id, $mSQL, MYSQLI_USE_RESULT);
				$ff   = 'mysqli_fetch_assoc';
				$fl   = 'mysqli_free_result';
			}else{
				$query= mysql_unbuffered_query($mSQL,$this->ci->db->conn_id);
				$ff   = 'mysql_fetch_assoc';
				$fl   = 'mysql_free_result';
			}

			if ($query!==false){
				if(isset($data['dupli'])) $data['limpiar']=false;
				if(!isset($data['limpiar'])) $data['limpiar']=true;
				if($data['limpiar']){
					$mSQL="DELETE FROM $data[table]"; if(!empty($data['where'])) $mSQL.=' WHERE '.$data['where'];
					$mSQL.="\n";
					fwrite($handle, $mSQL);
				}

				while($row = $ff($query)){
				$mSQL = $this->ci->db->insert_string($data['table'], $row);
					if(isset($data['dupli'])){
						$data['ignore']   = false;
						$data['limpiar']  = false;

						$ccampo=array();
						foreach($data['dupli'] as $campos){
							if(isset($data['condi'])){
								$ccampo[]="`${campos}`= IF(".$data['condi'].",VALUES(`${campos}`),`${campos}`)";
							}else{
								$ccampo[]="`${campos}`= VALUES(`${campos}`)";
							}
						}
						$mSQL.=' ON DUPLICATE KEY UPDATE '.implode(',',$ccampo);
					}elseif(isset($data['ignore']) && $data['ignore']){
						$mSQL='INSERT IGNORE '.substr($mSQL,6);
					}
					$mSQL.="\n";
					fwrite($handle, $mSQL);

				}
				$fl($query);
			}
		}
		fclose($handle);
		$firma=md5_file($nombre);
		$this->ci->load->library('encrypt');

		if(!empty($add_encrip)) $firma=$add_encrip.$this->separador.$firma;
		$firma=$this->ci->encrypt->encode($firma);
		$this->ci->load->library('zip');
		$this->ci->zip->add_data('firma.txt',$firma);

		$this->ci->zip->read_file($nombre);
		$this->ci->zip->download($nomb.'.zip');
		unlink($nombre);
	}
}
