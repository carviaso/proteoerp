<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Sfacmovil extends Controller {
	var $mModulo='SFACMOVIL';
	var $titp='Facturaci&oacute;n ';
	var $tits='Facturaci&oacute;n';
	var $url ='ventas/sfacmovil/';
	var $genesal  = true;
	var $_creanfac= false;

	function Sfacmovil(){
		parent::Controller();
	}

	function index(){
		$data=array();
		$data['header']  = '';
		$data['content'] = $this->load->view('view_sfacmovil', $data,true);
		$data['footer']  = '';
		$data['script']  = '';
		$data['panel']   = '';
		$data['title']   = heading('Pre-factura');
		$this->load->view('view_ventanasjqm', $data);
	}

	function autentificar(){
		header('Content-Type: application/json');
		$usr=$this->input->post('usr');
		$pws=$this->input->post('pws');

		$r=array(
			'autent' => $this->secu->autentifica($usr,$pws),
			'nombre' => $this->secu->getnombre()
		);

		echo json_encode($r);
	}

	function cese(){
		header('Content-Type: application/json');
		$this->secu->cese();
		echo json_encode(true);
	}

	function chlogin(){
		header('Content-Type: application/json');
		echo json_encode($this->secu->es_logeado());
	}
}
