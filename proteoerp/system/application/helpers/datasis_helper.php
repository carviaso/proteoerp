<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');

function phpscript($file){
	$thisobject =& get_instance();
	$charset=$thisobject->config->item('charset');
	$path2file=site_url('recursos/scripts/'.$file);
	return '<script src="'. $path2file .'" type="text/javascript" charset="'.$charset.'"></script>' . "\n";
}

function nformat($numero,$num=null,$centimos=null,$miles=null){
	if(is_null($numero)) return null;
	$sig='';
	if($numero < 0){
		$sig='-';
		$numero=abs($numero);
	}
	if(is_null($centimos)) $centimos = (is_null(constant('RAPYD_DECIMALS'))) ? ',' : RAPYD_DECIMALS;
	if(is_null($miles))    $miles    = (is_null(constant('RAPYD_THOUSANDS')))? '.' : RAPYD_THOUSANDS;
	if(is_null($num))      $num      = (is_null(constant('RAPYD_NUM')))      ?  2  : RAPYD_NUM;
	if(!($numero > 0) OR (!is_numeric($numero)))$numero=0;
	return $sig.number_format($numero,$num,$centimos,$miles);
}

function htmlnformat($numero){
	$centimos = (is_null(constant('RAPYD_DECIMALS'))) ? ',' : RAPYD_DECIMALS;
	$numero   = nformat($numero);
	return str_replace(',','<span style="font-size:0.7em;">'.$centimos,$numero).'</span>';
}

function des_nformat($numero,$num=null,$centimos=null,$miles=null){
	if(empty($numero)) return null;
	if(is_null($centimos)) $centimos = (is_null(constant('RAPYD_DECIMALS'))) ? ',' : RAPYD_DECIMALS;
	if(is_null($miles))    $miles    = (is_null(constant('RAPYD_THOUSANDS')))? '.' : RAPYD_THOUSANDS;
	$numero=str_replace($miles,'',$numero);
	$numero=str_replace($centimos,'.',$numero);
	return floatval($numero);
}

function sinulo($val,$porcia=null){
	if(empty($val) OR is_null($val))
		return $porcia;
	return $val;
}

function siinulo($val,$si,$no){
	if(empty($val) OR is_null($val))
		return $si;
	return $no;
}

function moneyformat($numero){
	return nformat($numero,2);
}

function des_moneyformat($numero){
	return des_nformat($numero);
}

function cadAnum($num){
	$cana=strlen($num);
	$omag=0;
	$ban=false;
	for($i=$cana-1;$i>0;$i--){
		if($num[$i]=='.' OR $num[$i]==','){
			$ban=true;
			break;
		}
		$omag++;
	}

	$numero=intval(str_replace(array(',','.'),'',$num));
	if($ban)
		$omag=pow(10,$omag);
	else
		$omag=1;
	$numero=$numero/$omag;

	return $numero;
}

function jsescape($string){
	$string=str_replace("\r",'',$string);
	$string=str_replace("\n",'',$string);
	$string=preg_replace('/\s\s+/', ' ', $string);
	$string=addslashes($string);
	$string=str_replace('<','\<',$string);
	$string=str_replace('>','\>',$string);
	$string=str_replace(';','\;',$string);
	$string='\''.$string.'\'';
	return $string;
}
