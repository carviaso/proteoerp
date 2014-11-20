<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class secu{
	var $ci;
	var $db;
	var $dbindex = 'default';
	var $cajero  = '';
	var $vendedor= '';
	var $almacen = '';
	var $sucursal= '';
	var $nombre  = '';
	var $_datac  = false;

	function secu(){
		$this->ci =& get_instance();
		$this->db =$this->ci->load->database($this->dbindex,TRUE);
	}

	function es_logeado(){
		if($this->ci->session->userdata('logged_in')){
			return true;
		}
		return false;
	}

	function usuario(){
		return $this->ci->session->userdata('usuario');
	}

	function autentifica($usr,$pws){
		if(empty($usr)) return false;
		$dbusr=$this->ci->db->escape($usr);
		$dbpws=sha1($pws);

		if($this->es_interno()){
			$ww='';
		}else{
			$ww=' AND remoto=\'S\'';
		}

		$mSQL="SELECT us_nombre FROM usuario WHERE us_codigo=${dbusr} AND SHA(us_clave)='${dbpws}' AND activo='S' ${ww}";
		$cursor=$this->ci->db->query($mSQL);
		if($cursor->num_rows() > 0){
			$rr  = $cursor->row_array();
			$sal = each($rr);
			$sess_data = array('usuario' => $usr,'nombre'  => $sal[1],'logged_in'=> true );
		}else{
			$sess_data = array('logged_in'=> false);
		}
		$this->ci->session->set_userdata($sess_data);
		if($sess_data['logged_in']){
			logusu('MENU','Entro en Proteo');
			return true;
		}else{
			return false;
		}
	}

	function login_uuid($uuid){
		$sel=array('cajero','vendedor','almacen','sucursal','us_nombre');
		$this->db->select($sel);
		$this->db->from('usuario');
		$this->db->where('uuid',$uuid);
		$this->db->limit(1);
		$query = $this->db->get();

		if ($query->num_rows() > 0){
			$row = $query->row_array();

			$this->cajero  = $row['cajero']  ;
			$this->vendedor= $row['vendedor'];
			$this->almacen = $row['almacen'] ;
			$this->sucursal= $row['sucursal'];
			$this->nombre  = $row['us_nombre'];
			$this->_datac=true;
			return true;
		}
		return false;
	}

	function _getdata(){
		if($this->es_logeado() && $this->_datac==false){
			$sel=array('cajero','vendedor','almacen','sucursal','us_nombre');
			$this->db->select($sel);
			$this->db->from('usuario');
			$this->db->where('us_codigo',$this->usuario());
			$this->db->limit(1);
			$query = $this->db->get();

			if ($query->num_rows() > 0){
				$row = $query->row_array();

				$this->cajero  = $row['cajero']  ;
				$this->vendedor= $row['vendedor'];
				$this->almacen = $row['almacen'] ;
				$this->sucursal= $row['sucursal'];
				$this->nombre  = $row['us_nombre'];
			}
			$this->_datac=true;
		}
	}

	function getcajero(){
		$this->_getdata();
		return $this->cajero;
	}

	function getnombre(){
		$this->_getdata();
		return $this->nombre;
	}


	function getvendedor(){
		$this->_getdata();
		return $this->vendedor;
	}

	function getalmacen(){
		$this->_getdata();
		return $this->almacen;
	}

	function getsucursal(){
		$this->_getdata();
		return $this->sucursal;
	}

	function essuper(){
		if ($this->es_logeado()){
			$usuario = $this->db->escape($this->ci->session->userdata('usuario'));
			$query = $this->db->query("SELECT COUNT(*) AS cana FROM usuario WHERE us_codigo=$usuario AND supervisor='S'");
			if ($query->num_rows() > 0){
				$row = $query->row();
				return ($row->cana>0) ? TRUE : FALSE;
			}
		}
		return FALSE;
	}

	function puede($id){
		if ($this->es_logeado()){
			$usuario = $this->db->escape($this->ci->session->userdata('usuario'));
			$id      = $this->db->escape($id);
			//$query = $this->db->query("SELECT COUNT(*) AS cana FROM intrasida WHERE usuario=$usuario AND id=$id"); //Tortuga
			$query = $this->db->query("SELECT COUNT(*) AS cana FROM intrasida WHERE usuario=$usuario AND modulo=$id"); //Proteo
			if ($query->num_rows() > 0){
				$row = $query->row();
				return ($row->cana>0) ? TRUE : FALSE;
			}
		}
		return FALSE;
	}

	function es_shell(){
		return (isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])) ? TRUE : FALSE;
	}


	function es_interno(){
		return $this->ip_interno($_SERVER['REMOTE_ADDR']);
	}

	function ip_interno($ip){
		//10.0.0.0    - 10.255.255.255  | 10.0.0.0/8
		//172.16.0.0  - 172.31.255.255  | 172.16.0.0/12
		//192.168.0.0 - 192.168.255.255 | 192.168.0.0/16
		return (preg_match("/^(10\\..+|192\\.168\\..+|172\\.(1[6-9]|2[0-9]|3[01])\\..+)$/", $ip)>0) ? TRUE : FALSE;
	}

	function cliente($codigo,$pws){
		$codigo = $this->db->escape($codigo);
		$pws    = $this->db->escape($pws);
		$query = $this->db->query("SELECT COUNT(*) AS cana FROM scli WHERE cliente=$codigo AND clave=$pws");
		if ($query->num_rows() > 0){
			$row = $query->row();
			return ($row->cana>0) ? TRUE : FALSE;
		}
		return FALSE;
	}
}
