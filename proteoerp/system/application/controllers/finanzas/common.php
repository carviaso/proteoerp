<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Common extends controller {

	function _traetipo($codigo){
		$sql='SELECT tbanco FROM banc WHERE codbanc='.$this->db->escape($codigo);
		return $this->datasis->dameval($sql);
	}

	function _traemoneda($codigo){
		$sql='SELECT moneda FROM banc WHERE codbanc='.$this->db->escape($codigo);
		return $this->datasis->dameval($sql);
	}

	function _traebandata($codigo){
		$sql='SELECT tbanco,moneda,banco,saldo,depto,numcuent FROM banc WHERE codbanc='.$this->db->escape($codigo);
		return $this->datasis->damerow($sql);
	}

	function _traedatausr(){
		$usr=$this->session->userdata('usuario');
		$sql='SELECT vendedor,cajero,sucursal,almacen FROM usuario WHERE us_codigo='.$this->db->escape($usr);
		return $this->datasis->damerow($sql);
	}

	function _scajstatus($cajero){
		$dbcajero=$this->db->escape($cajero);
		$mSQL = 'SELECT fechac,status FROM scaj WHERE cajero='.$dbcajero;
		$row  = $this->datasis->damerow($mSQL);
		$factu= date('Y-m-d');
		if($row['fechac']==$factu && $row['status']=='C'){
			return 'C';
		}else{
			$fechaa=$this->db->escape(date('Y-m-d'));
			$horaa =$this->db->escape(date('H:m:s'));
			$sql='UPDATE scaj SET status=\'A\', fechaa='.$fechaa.', horaa='.$horaa.' WHERE cajero='.$dbcajero;
			//$ban=$this->db->simple_query($sql);
			return 'A';
		}
	}

	//Para el autocomplete
	function _automgas(){
	}
}
