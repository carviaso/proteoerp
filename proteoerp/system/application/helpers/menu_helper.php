<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function arr_menu($nivel=1,$pertenece=NULL){
	if($nivel!=1 AND $pertenece===NULL) $nivel=1;
	if($nivel>1){
		$mmodulo=opciones_nivel($nivel-1);
		$esde=" AND MID(a.modulo,1,$mmodulo)='$pertenece'";
	}else{
		$esde='';
	}
	$CI =& get_instance();
	$CI->load->database('default',TRUE);
	$modulo=opciones_nivel($nivel);

	if ($CI->session->userdata('logged_in')){
		$mSQL="SELECT a.modulo, a.titulo, a.mensaje, a.target, a.ejecutar, a.panel, a.ancho, a.alto  FROM intramenu AS a ";
		$usr=$CI->session->userdata('usuario');
		if ($CI->datasis->essuper() or $pertenece===0)
			$mSQL .= "WHERE ";
		else
			$mSQL .= "JOIN intrasida AS b ON a.modulo=b.modulo WHERE b.usuario='$usr' AND b.acceso='S' AND ";

		$mSQL .="visible='S' AND CHAR_LENGTH(a.modulo)=${modulo} $esde ORDER BY a.panel, a.orden, a.modulo";

		$query = $CI->db->query($mSQL);
		$retorna=$query->result_array();
	}else{
		$retorna=array();
	}
	return $retorna;
}

function arr2link($arr,$utf8c=false){
	$att = array(
		'width'      => $arr['ancho'],
		'height'     => $arr['alto'],
		'scrollbars' => 'Yes',
		'status'     => 'Yes',
		'resizable'  => 'Yes',
		'screenx'    => "'+((screen.availWidth/2)-".intval($arr['ancho']/2).")+'",
		'screeny'    => "'+((screen.availHeight/2)-".intval($arr['alto']/2).")+'" );
	$indi=parsePattern($arr['ejecutar']);

	if($utf8c){
		$arr['titulo'] =utf8_encode($arr['titulo']) ;
		$arr['mensaje']=utf8_encode($arr['mensaje']);
	}

	if($arr['target']=='popu'){
		$ejecutar=anchor_popup($indi, $arr['titulo'], $att);
	}elseif($arr['target']=='javascript'){
		$ejecutar="<a href='javascript:".str_replace('\'',"\\'",$indi)."' title='".htmlentities($arr['mensaje'],ENT_COMPAT,'UTF-8')."'>".htmlentities($arr['titulo'],ENT_COMPAT,'UTF-8')."</a>";
	}else{
		$ejecutar=anchor($indi, $arr['titulo']);
	}
	return $ejecutar;
}

function arr2panel($arr){
	$retorna=array();
	foreach($arr as $op ){
		$retorna[$op['panel']][]= array('titulo'=>htmlentities($op['titulo']),'mensaje'=>htmlentities($op['mensaje']),'ejecutar'=>$op['ejecutar'],'target'=>$op['target'],'ancho'=>$op['ancho'],'alto'=>$op['alto']);
	}
	return $retorna;
}

function opciones_nivel($modulo=1){
	$CI =& get_instance();
	$niveles=$CI->config->item('niveles_menu');
	$nivel=explode(',',$niveles);
	if($modulo>count($nivel) or $modulo<=0)
		$modulo=count($nivel);
	$acu=0;
	for($i=0;$i<$modulo;$i++){
		$acu+=$nivel[$i];
	}
	return $acu;
}

function barra_menu($modulo=NULL){
	if(empty($modulo)) return;
	$arr=arr_menu(3,$modulo);
	$retorna=array();
	foreach ($arr as $op){
		$retorna[]=arr2link($op);
	}
	return $retorna;
}

function parsePattern($pattern){
	$template = $pattern;
	$parsedcount = 0;
	$salida=array();
	while (strpos($template,'#>')>0) {
		$parsedcount++;
		$parsedfield = substr($template,strpos($template,'<#')+2,strpos($template,'#>')-strpos($template,'<#')-2);
		$CI =& get_instance();
		$remp=$CI->uri->segment($parsedfield);
		$template = str_replace("<#".$parsedfield ."#>",$remp,$template);
	}
	return $template;
}
