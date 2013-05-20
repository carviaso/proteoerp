<?php
class Datalog extends Controller {

	function Datalog(){
		parent::Controller();
	}

	function index($mclave){
		$dbmclave=$this->db->escape($mclave);
		$comando = $this->datasis->dameval("SELECT ejecuta FROM enlacedp WHERE clave=$dbmclave ");
		echo $comando;
	}

	function accion($mclave){
		$query = $this->db->query('SELECT usuario,ejecuta FROM enlacedp WHERE clave='.$this->db->escape($mclave));
		if ($query->num_rows() > 0){
			$row=$query->row();
			$nombre = $this->datasis->dameval('SELECT us_nombre FROM usuario WHERE us_codigo='.$this->db->escape($row->usuario));
			$sess_data = array('usuario' => $row->usuario,'nombre'  => $nombre,'logged_in'=> TRUE );
			$this->session->set_userdata($sess_data);

			$this->db->query('DELETE FROM enlacedp WHERE clave='.$this->db->escape($mclave));
			redirect($row->ejecuta);
		}
	}
}