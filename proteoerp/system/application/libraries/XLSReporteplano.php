<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
require_once 'XLSReporte.php';

class XLSReporteplano extends XLSReporte  {

	function XLSReporteplano($mSQL=''){
		parent::XLSReporte($mSQL);
	}

	function setGrupo($param){
		if(is_array($param))
			$data=$param;
		else
			$data= func_get_args();
		foreach($data as $sale){
			if (in_array($sale, $this->DBfieldsName)){
				$this->AddCol($sale,20,ucfirst($sale),'L',7);
			}
		}
	}

	function setGrupoLabel($label){

	}
}
