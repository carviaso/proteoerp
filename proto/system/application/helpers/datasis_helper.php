<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function phpscript($file){
	$thisobject =& get_instance();
	$charset=$thisobject->config->item('charset');
	$path2file=site_url('recursos/scripts/'.$file);
	return '<script src="'. $path2file .'" type="text/javascript" charset="'.$charset.'"></script>' . "\n";
}

function nformat($numero,$num=null,$centimos=null,$miles=null){
	if(empty($numero)) return null;
	if(is_null($centimos)) $centimos = (is_null(constant("RAPYD_DECIMALS"))) ? ',' : RAPYD_DECIMALS;
	if(is_null($miles))    $miles    = (is_null(constant("RAPYD_THOUSANDS")))? '.' : RAPYD_THOUSANDS;
	if(is_null($num))      $num      = (is_null(constant("RAPYD_NUM")))      ?  2  : RAPYD_NUM;
	if(!($numero > 0) OR (!is_numeric($numero)))$numero=0;
	return number_format($numero,$num,$centimos,$miles);
}

function des_nformat($numero,$num=null,$centimos=null,$miles=null){
	if(empty($numero)) return null;
	if(is_null($centimos)) $centimos = (is_null(constant("RAPYD_DECIMALS"))) ? ',' : RAPYD_DECIMALS;
	if(is_null($miles))    $miles    = (is_null(constant("RAPYD_THOUSANDS")))? '.' : RAPYD_THOUSANDS;
	$numero=str_replace($miles,'',$numero);
	$numero=str_replace($centimos,'.',$numero);
	return floatval($numero);
}

function moneyformat($numero){
	return nformat($numero,2);
}

function des_moneyformat($numero){
	return des_nformat($numero);
}
?>
