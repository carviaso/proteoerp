<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Chat extends Controller {
	function __construct()
	{
		parent::__construct();
		if(!isset($this->session)) $this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('file');
		if($this->secu->usuario()=='')
		{
			redirect('bienvenido');
		}
	}

	function index()
	{
		$this->load->view('chat/header');
		$this->load->view('chat/chat');
		$this->load->view('chat/footer');
	}

	function agregar()
	{
		$mensaje = $this->input->post('mensaje');
		if ( !empty($mensaje) ) {
			$data['fecha']   = date("Y-m-d");
			$data['hora']    = date("g:i A");
			$data['para']    = $this->input->post('usuario');
			$data['usuario'] = $this->secu->usuario();
			$data['mensaje'] = $this->input->post('mensaje');
			$this->db->insert('chat',$data);
		}
	}

	function actualizar()
	{
		$mensaje = '';
		$usuario = $this->db->escape($this->secu->usuario());
		$mSQL = "SELECT * FROM chat WHERE usuario=".$usuario." OR para IN ('-',".$usuario.") ORDER BY id DESC LIMIT 15";
		$query = $this->db->query($mSQL);
		if ( $query->num_rows() > 0 ) {
			foreach( $query->result() as $row ) {
				$mensaje = "<div class='msgln'>(".substr($row->hora,0,5).") <b>".$row->usuario."</b>: ".$row->mensaje."<br></div>\n".$mensaje;
			}
		}
		echo $mensaje;
	}

	function status()
	{
		$mensaje = '';
		$usuario = $this->db->escape($this->secu->usuario());
		$mSQL = "SELECT * FROM chat WHERE usuario=".$usuario." OR para IN ('-',".$usuario.") ORDER BY id DESC LIMIT 15";
		$query = $this->db->query($mSQL);
		if ( $query->num_rows() > 0 ) {
			foreach( $query->result() as $row ) {
				$mensaje = "<div class='msgln'>(".substr($row->hora,0,5).") <b>".$row->usuario."</b>: ".$row->mensaje."<br></div>\n".$mensaje;
			}
		}
		echo strlen($mensaje);
	}


}
?>
