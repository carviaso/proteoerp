<?php
/** * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Metodos extends Controller {

	//Genera las consulta para la contabilidad
	function _hace_regla($modulo, $mCONTROL, $mGRUPO, $mFDESDE=null ) {
		$cwhere = " a.$mCONTROL='$mGRUPO'";
		$query=$this->db->query("SELECT modulo, regla, tabla, fecha, comprob, origen, condicion, agrupar, cuenta, referen, concepto, debe, haber, ccosto, sucursal FROM reglascont WHERE modulo='$modulo'  ORDER BY tabla,regla");
		foreach ($query->result_array() as $fila){
			if ( $fila['tabla'] == "ITCASI" ) {
				$select ="
				$fila[fecha]    fecha,
				$fila[comprob]  comprob,
				'$fila[modulo]$fila[regla]' clave,
				$fila[cuenta]   cuenta,
				$fila[referen]  referen,
				$fila[concepto] concepto,
				$fila[debe]     debe,
				$fila[haber]    haber,
				$fila[sucursal] sucursal, ";
				$select.= (empty($fila['ccosto']) ? "'' ccosto" : $fila['ccosto'].' ccosto') ;
			}else{
				$select ="
				$fila[comprob] comprob,
				$fila[fecha] fecha,
				$fila[concepto] concepto,
				'$modulo' origen ";
			}
			$from    = $fila['origen'];
			$where   = (empty($fila['condicion'])? $cwhere:"$cwhere AND  $fila[condicion] ");
			$groupby = $fila['agrupar'];

			if(!empty($mFDESDE)){
				/*$alias   = '';
				$exp     = explode(' ',trim($fila['origen']));
				if(count($exp)>0){
					$ttabla  = array_shift($exp);
					$dbfdesde= $this->db->escape($mFDESDE);
					foreach($exp as $val) {
						if(strtoupper($val) == 'AS'){
							continue;
						}else{
							$alias=$val;
							break;
						}
					}
					$campos = $this->db->list_fields($ttabla);
					$where .= " AND ".$fila['fecha']." >= $dbfdesde";
				}*/
				$dbfdesde= $this->db->escape($mFDESDE);
				$where .= " AND ".$fila['fecha']." >= $dbfdesde";
			}

			$data ="SELECT $select FROM $from WHERE $where";
			$data.= (empty($groupby)? " ":" GROUP BY $groupby");
			if ( $fila['tabla'] == "ITCASI" )
				$itcasi[]  =$data;
			else
				$casi[]=$data;
		}
		$areglo['casi']  =$casi;
		$areglo['itcasi']=$itcasi;
		return $areglo;
	}
}
