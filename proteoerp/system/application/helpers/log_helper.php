<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function logusu($modulo,$comentario){
	if(empty($modulo) || empty($comentario)) return false;
	$CI =& get_instance();
	$usr=$CI->session->userdata('usuario');
	if(empty($usr)) $usr='#AU#';
	$dbusr       = $CI->db->escape($usr);
	$dbcomentario= $CI->db->escape($comentario);
	$dbmodulo    = $CI->db->escape($modulo);

	$mSQL="INSERT INTO logusu (usuario,fecha,hora,modulo,comenta) VALUES (${dbusr},CURDATE(),CURTIME(),${dbmodulo},${dbcomentario})";
	return $CI->db->simple_query($mSQL);
}

function memowrite($comentario=NULL,$nfile='salida',$modo='wb'){
	if(empty($comentario)) return false;
	$CI =& get_instance();
	$CI->load->helper('file');
	if (!write_file("./system/logs/${nfile}.log", $comentario,$modo)){
		return false;
	}
	return true;
}

function memoborra($nfile='salida'){
	return unlink("./system/logs/${nfile}.log");
}
