<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
class xlsauto extends Controller{
	function xlsauto(){
		parent::Controller();
		if(!$this->secu->es_logeado()) show_404();
	}

	function index(){
		redirect('/xlsauto/repoauto');
	}

	function repoauto($mSQL){//ORDeR BY envia,recibe
		$this->load->library('XLSReporte');
		$xls= new xlsreporte($mSQL);
		$xls->tcols();
		$xls->Table();
		$xls->Output();
	}

	function repoauto2(){//ORDeR BY envia,recibe 
		$this->load->library('encrypt');
		$this->load->library('XLSReporte');

		$mSQL=$this->input->post('mSQL');
		$consulta = $this->encrypt->decode($mSQL);
		$xls= new xlsreporte($consulta);
		$xls->tcols();
		$xls->Table();
		$xls->Output();
	}

	function repo64($mSQL=null){
		if(empty($mSQL)){
			$mSQL=$this->input->get_post('mSQL');
		}else{
			$mSQL = str_replace("-porce-","%", $mSQL);
			$mSQL = urldecode($mSQL);
		}
		if($mSQL===false) return false;

		$this->load->library('encrypt');
		$this->load->library('XLSReporte');

		$mSQL = base64_decode($mSQL);
		$consulta = $this->encrypt->decode($mSQL);

		$xls= new xlsreporte($consulta);
		$xls->tcols();
		$xls->Table();
		$xls->Output();
		return true;
	}
}