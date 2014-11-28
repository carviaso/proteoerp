<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Common extends controller {

	static function _traetipo($codigo){
		$CI =& get_instance();
		$sql='SELECT tbanco FROM banc WHERE codbanc='.$CI->db->escape($codigo);
		return $CI->datasis->dameval($sql);
	}

	static function _traemoneda($codigo){
		$CI =& get_instance();
		$sql='SELECT moneda FROM banc WHERE codbanc='.$CI->db->escape($codigo);
		return $CI->datasis->dameval($sql);
	}

	static function _traebandata($codigo){
		$CI =& get_instance();
		$sql='SELECT tbanco,moneda,banco,saldo,depto,numcuent FROM banc WHERE codbanc='.$CI->db->escape($codigo);
		return $CI->datasis->damerow($sql);
	}

	function _traedatausr(){
		$CI =& get_instance();
		$usr=$CI->session->userdata('usuario');
		$sql='SELECT vendedor,cajero,sucursal,almacen FROM usuario WHERE us_codigo='.$CI->db->escape($usr);
		return $CI->datasis->damerow($sql);
	}

	static function _scajstatus($cajero){
		$CI =& get_instance();
		$dbcajero=$CI->db->escape($cajero);
		$mSQL = 'SELECT fechac,status FROM scaj WHERE cajero='.$dbcajero;
		$row  = $CI->datasis->damerow($mSQL);
		$factu= date('Y-m-d');
		if($row['fechac']==$factu && $row['status']=='C'){
			return 'C';
		}else{
			$fechaa=$CI->db->escape(date('Y-m-d'));
			$horaa =$CI->db->escape(date('H:m:s'));
			$sql='UPDATE scaj SET status=\'A\', fechaa='.$fechaa.', horaa='.$horaa.' WHERE cajero='.$dbcajero;
			//$ban=$CI->db->simple_query($sql);
			return 'A';
		}
	}

	//Para el autocomplete
	static function _automgas(){
	}
}
