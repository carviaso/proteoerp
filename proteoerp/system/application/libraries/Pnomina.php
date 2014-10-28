<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class fnomina {

	var $ci;
	var $CODIGO;

	var $fdesde;
	var $fhasta;

	var $arr_notabu=array();

	function fnomina(){
		$this->ci =& get_instance();
	}

	function SUELDO_MES(){
		$CODIGO  = $this->ci->db->escape($this->CODIGO);
		$SUELDOA = 0;
		$mFRECU  = $this->ci->datasis->dameval("SELECT b.tipo FROM pers a JOIN noco b ON a.contrato=b.codigo WHERE a.codigo=${CODIGO}");
		$mSUELDO = floatval($this->ci->datasis->dameval("SELECT sueldo FROM pers WHERE codigo=${CODIGO}"));

		if($mFRECU == 'O') $mFRECU  = $this->ci->datasis->dameval("SELECT tipo FROM pers WHERE codigo=${CODIGO}");
		if($mFRECU == 'S') $SUELDOA = $mSUELDO*52/12;
		if($mFRECU == 'B') $SUELDOA = $mSUELDO*26/12;
		if($mFRECU == 'Q') $SUELDOA = $mSUELDO*2;
		if($mFRECU == 'M') $SUELDOA = $mSUELDO;
		//memowrite($SUELDOA,"SUELDO_MES");
		return $SUELDOA;
	}

	function SUELDO_QUI(){
		$CODIGO=$this->ci->db->escape($this->CODIGO);
		$SUELDOA = 0;
		$mFRECU  = $this->ci->datasis->dameval("SELECT b.tipo FROM pers a JOIN noco b ON a.contrato=b.codigo WHERE a.codigo=${CODIGO}");
		$mMONTO  = floatval($this->ci->datasis->dameval("SELECT sueldo FROM pers WHERE codigo=${CODIGO}"));

		if($mFRECU == 'O') $mFRECU = $this->ci->datasis->dameval("SELECT tipo FROM pers WHERE codigo=$CODIGO");
		if($mFRECU == 'S') $SUELDOA = $mMONTO*52/24;
		if($mFRECU == 'B') $SUELDOA = $mMONTO*26/24;
		if($mFRECU == 'Q') $SUELDOA = $mMONTO;
		if($mFRECU == 'M') $SUELDOA = $mMONTO/2;
		//return $SUELDOA;
	}

	function SUELDO_SEM(){
		$CODIGO  = $this->ci->db->escape($this->CODIGO);
		$SUELDOA = 0;
		$mFRECU  = $this->ci->datasis->dameval("SELECT b.tipo FROM pers a JOIN noco b ON a.contrato=b.codigo WHERE a.codigo=${CODIGO}");
		$mMONTO  = floatval($this->ci->datasis->dameval("SELECT sueldo FROM pers WHERE codigo=${CODIGO}"));

		if($mFRECU == 'O') $mFRECU  = $this->ci->datasis->dameval("SELECT tipo FROM pers WHERE codigo=${CODIGO}");
		if($mFRECU == 'S') $SUELDOA = $mMONTO;
		if($mFRECU == 'B') $SUELDOA = $mMONTO/2;
		if($mFRECU == 'Q') $SUELDOA = $mMONTO*24/52;
		if($mFRECU == 'M') $SUELDOA = $mMONTO*12/52 ;
		//memowrite($SUELDOA,"SUELDO_SEM");

		return $SUELDOA;
	}

	// CALCULA EL SUELDO POR DIA
	function SUELDO_DIA(){
		$CODIGO  = $this->ci->db->escape($this->CODIGO);
		$SUELDOA = 0;
		$mFRECU  = $this->ci->datasis->dameval("SELECT b.tipo FROM pers a JOIN noco b ON a.contrato=b.codigo WHERE a.codigo=$CODIGO");
		$mMONTO  = $this->SUELDO;
		//$this->ci->datasis->dameval("SELECT sueldo FROM pers WHERE codigo=$CODIGO");

		if($mFRECU == 'O') $mFRECU  = $this->ci->datasis->dameval("SELECT tipo FROM pers WHERE codigo=${CODIGO}");
		if($mFRECU == 'S') $SUELDOA = $mMONTO/7 ;
		if($mFRECU == 'B') $SUELDOA = $mMONTO/14;
		if($mFRECU == 'Q') $SUELDOA = $mMONTO/15;
		if($mFRECU == 'M') $SUELDOA = $mMONTO/30 ;
		return $SUELDOA;
	}

	// CALCULA EL SUELDO PROMEDIO POR DIA
	function SUELDO_DIA_PROM(){
		$CODIGO  = $this->ci->db->escape($this->CODIGO);
		$SUELDOA = 0;
		$mFRECU  = $this->ci->datasis->dameval("SELECT b.tipo FROM pers a JOIN noco b ON a.contrato=b.codigo WHERE a.codigo=${CODIGO}");
		$mMONTO  = $this->SPROME;

		if($mFRECU == 'O') $mFRECU  = $this->ci->datasis->dameval("SELECT tipo FROM pers WHERE codigo=${CODIGO}");
		if($mFRECU == 'S') $SUELDOA = $mMONTO/7 ;
		if($mFRECU == 'B') $SUELDOA = $mMONTO/14;
		if($mFRECU == 'Q') $SUELDOA = $mMONTO/15;
		if($mFRECU == 'M') $SUELDOA = $mMONTO/30 ;
		return $SUELDOA;
	}


	function SUELDO_HOR(){
		$SUELDOA = $this->SUELDO_DIA()/8;
		return $SUELDOA;
	}

	function ANTIGUEDAD( $mHASTA = '' ){
		$CODIGO = $this->ci->db->escape($this->CODIGO);
		$mDESDE = $this->ci->datasis->dameval("SELECT ingreso FROM pers WHERE codigo=${CODIGO}");
		if ( $mHASTA == '' ) $mHASTA = date('Y-m-d');

		$desde = new DateTime($mDESDE);
		$hasta = new DateTime($mHASTA);
		$anti  = $desde->diff($hasta);
		//memowrite('Antiguedad: Ano='.$anti->format('%y').' Mes='.$anti->format('%m').' dia='.$anti->format('%d'),'Antiguedad');

		return array( $anti->format('%y'), $anti->format('%m'), $anti->format('%d') );
	}

	function TRAESALDO($mmCONC){
		$CODIGO = $this->ci->db->escape($this->CODIGO);
		$mmCONC = $this->ci->db->escape($mmCONC);
		$mTCONC = intval($this->ci->datasis->dameval("SELECT COUNT(*) AS cana FROM prenom WHERE codigo=${CODIGO} AND concepto=${mmCONC}"));
		if($mTCONC == 1)
			$mTEMPO = $this->ci->datasis->dameval("SELECT valor FROM prenom WHERE codigo=${CODIGO} AND concepto=${mmCONC}");
		return $mTEMPO;
	}

	function TABUSCA($par){
		$CODIGO   = $this->ci->db->escape($this->CODIGO);
		$mREG     = $this->ANTIGUEDAD();
		$XTRABAJA = $this->ci->datasis->dameval('SELECT trabaja FROM prenom LIMIT 1');
		$mTABLA   = $this->NOTABU( $XTRABAJA, $mREG[0], $mREG[1], $mREG[2] );
		$mVALOR   = 0;

		if(in_array(strtoupper($par),array_map('strtoupper',$arr))){
			$mVALOR = $mTABLA[strtolower($par)];
		}

		/*if ( strtoupper($par) == 'PREAVISO'   ) $mVALOR = $mTABLA['preaviso'  ];
		if ( strtoupper($par) == 'VACACIONES' ) $mVALOR = $mTABLA['vacaciones'];
		if ( strtoupper($par) == 'BONOVACA'   ) $mVALOR = $mTABLA['bonovaca'  ];
		if ( strtoupper($par) == 'ANTIGUEDAD' ) $mVALOR = $mTABLA['antiguedad'];
		if ( strtoupper($par) == 'UTILIDADES' ) $mVALOR = $mTABLA['utilidades'];*/

		return $mVALOR;
	}

	//******************************************************************
	//  Busca en notabu
	//
	function NOTABU( $mCONTRATO, $mANO, $mMES, $mDIA ){

		if(count($this->arr_notabu)>0){
			$mSQL='SHOW FULL COLUMNS FROM notabu';
			$query = $this->ci->db->query($mSQL);
			foreach($query->result() as $row){
				if(in_array(trim($row->Field),array('contrato','ano','mes','dia','id')))
				$this->arr_notabu[]=trim($row->Field);
			}
		}
		$campos=implode(',',$this->arr_notabu);

		$dbcontrato = $this->ci->db->escape($mCONTRATO);
		$mSQL  = 'SELECT '.$campos;
		$mSQL .= " FROM notabu WHERE ano<=${mANO} AND mes<=${mMES} AND dia<=${mDIA}";
		$mSQL .= " AND contrato=${dbcontrato} ";
		$mSQL .= 'ORDER BY ano DESC, mes DESC, dia DESC ';
		$mSQL .= 'LIMIT 1';

		$rt  = $this->ci->datasis->damereg($mSQL);
		if(empty($rt)){
			foreach($campos as $val){
				$rt[$val]=0;
			}
		}

		return $rt;
	}

	function SUELDO_INT(){
		return 1;
	}

	//******************************************************************
	// Suma por Grupo
	//
	function GRUPO($parr){
		$CODIGO= $this->ci->db->escape($this->CODIGO);
		$mSQL  = "SELECT SUM(a.valor) cuenta FROM prenom a WHERE a.codigo=${CODIGO} AND a.grupo regexp '[${parr}]+' AND MID(a.concepto,1,1)<9";
		$query = $this->ci->db->query($mSQL);
		$row   = $query->row();
		$suma  = floatval($row->cuenta);
		return $suma;
	}

	//******************************************************************
	// Trae un concepto
	//
	function TRAE($parr){
		$CODIGO   = $this->ci->db->escape($this->CODIGO);
		$mSQL  = "SELECT a.valor FROM prenom a WHERE a.codigo=${CODIGO} AND a.concepto=".$this->ci->db->escape($parr);
		$query = $this->ci->db->query($mSQL);
		$row   = $query->row();
		$suma  = floatval($row->valor);
		return $suma;
	}


	//******************************************************************
	// Suma de todas las asignaciones
	//
	function ASIGNA(){
		$CODIGO   = $this->ci->db->escape($this->CODIGO);
		$mSQL  = "SELECT SUM(valor) cuenta FROM prenom WHERE codigo=${CODIGO} AND tipo='A' AND MID(concepto,1,1)<9 ";
		$query = $this->ci->db->query($mSQL);
		$row   = $query->row();
		$suma  = floatval($row->cuenta);
		return $suma;
	}

	//******************************************************************
	// Reposo
	//
	function REPOSO(){
		// VER SI ESTA EN REPOSO
		$mSQL  = "SELECT inicio, final FROM preposo WHERE codigo=".$this->ci->db->escape($this->CODIGO)." AND inicio<='".$this->fhasta."' AND final>'".$this->fdesde."'";
		//memowrite($mSQL, 'Reposo');
		$query = $this->ci->db->query($mSQL);
		$diasefect = 0;

		$reposos = $query->num_rows();
		if ( $query->num_rows() > 0 ){
			if ( $reposos == 1 ) {
				// Busca cuantos dias hay entre el periodo
				$row     = $query->row();
				$inicial = $row->inicio;
				$final   = min( $row->final, $this->fhasta);

				$d = new DateTime($inicial);
				$h = new DateTime($final);
				$diasantes = $d->diff($h)->format('%a');

				$d = new DateTime($this->fdesde);
				$h = new DateTime($final);
				$diasduran = $d->diff($h)->format('%a');

				$diasefect = min($diasantes-3, $diasduran);
				if ( $diasefect < 0 ) $diasefect = 0;

				if ( $row->final > $this->fhasta ){
					$diasefect = $diasefect+1 ;
				}
			}
		}

		return $diasefect;
	}

	//******************************************************************
	// Calcula los lunes del periodo
	//
	function SEMANAS(){
		$desde = $this->fdesde;
		$hasta = $this->fhasta;

		$first_date = strtotime($desde.' -1 days');
		$first_date = strtotime(date('M d Y',$first_date).' next Monday');

		$last_date = strtotime($hasta.' +1 days');
		$last_date = strtotime(date('M d Y',$last_date).' last Monday');

		$dias = floor(($last_date - $first_date)/(7*86400)) + 1;

		return $dias;
	}

	//******************************************************************
	//Sueldo promedio mensual aplicable a liquidacion
	function SUELPROM(){
		$desde  = $this->fdesde;
		$hasta  = $this->fhasta;
		$dbdesde= $this->ci->db->escape($desde);
		$dbhasta= $this->ci->db->escape($hasta);

		$CODIGO  = $this->ci->db->escape($this->CODIGO);

		$mSQL = "SELECT SUM(valor) AS monto
			FROM nomina AS a
			JOIN conc AS b ON a.concepto=b.concepto
			WHERE liquida='S' AND codigo=${CODIGO} AND fecha BETWEEN ${dbdesde} AND ${dbhasta}";

		$sueldo = floatval($this->ci->datasis->dameval($mSQL));
		return $sueldo;
	}

	function SUELDOPROD(){
		return $this->SUELPROM()/30;
	}

	function DIASUTILIDAD(){
		$util=$this->ci->datasis->traevalor('DIASUTILIDAD','Dias de utilidad anual que paga la empresa');
		return floatval($util);
	}
	//fin liquidacion
	//******************************************************************

}

//**********************************************************************
//
//
class Pnomina extends fnomina {

	var $MONTO  = 0;
	var $SUELDO = 0;
	var $SPROME = 0;
	var $DIAS   = 0;

	var $VARI1 = 0;
	var $VARI2 = 0;
	var $VARI3 = 0;
	var $VARI4 = 0;
	var $VARI5 = 0;
	var $VARI6 = 0;


	function pnomina(){
		parent::fnomina();
	}

	//******************************************************************
	// Evalua la Formula del concepto
	//
	function evalform($formula){
		$MONTO  = $this->MONTO;
		$SUELDO = $this->SUELDO;
		$SPROME = $this->SPROME;
		$DIAS   = $this->DIAS;

		$VARI1 = $this->VARI1;
		$VARI2 = $this->VARI2;
		$VARI3 = $this->VARI3;
		$VARI4 = $this->VARI4;
		$VARI5 = $this->VARI5;
		$VARI6 = $this->VARI6;

		$SMINIMO = $this->ci->datasis->traevalor('SUELDOMINIMO');

		$fformula = $this->_traduce($formula);

		if ( strpos($formula,'REPOSO') )
			memowrite($formula.' == >> '.$fformula, 'Formula');

		$retorna='$rt='.$fformula.';';

		eval($retorna);
		return $rt;
	}

	function _traduce($formula){
		$CODIGO = $this->ci->db->escape($this->CODIGO);

		$qq = $this->ci->db->query("SELECT valor FROM utributa ORDER BY fecha DESC LIMIT 1");
		$rr = $qq->row_array();
		$aa = each($rr);
		$ut = $aa[1];

		//Trata el caso de las funciones que retornan arreglo
		$antf='';
		if(preg_match_all('/ANTIGUEDAD\((?P<arg>[^\)]*)\)\[(?P<ind>[0-9]+)\]/',$formula, $mat)>0){
			foreach($mat[0] as $id=>$rp){
				if($rp!=$antf){
					$arg=$mat['arg'][$id];
					$ind=$mat['ind'][$id];

					$nf='$'."this->_getarray(ANTIGUEDAD(${arg}),${ind})";
					$formula=str_replace($rp,$nf,$formula);
					$antf=$rp;
				}
			}
		}
		//Finaliza el caso de las funciones que retornan arreglos

		//Transforma los if
		$long = strlen($formula);
		$pos  = $long+1;
		while(1){
			$desp=$pos-$long-1;
			if(abs($desp)>=$long-1) break;
			$pos=strrpos($formula,'IF(',$desp);
			if($pos===false) break;
			$ig=null;
			$remp='?';
			for($i=$pos+2; $i<$long;$i++){
				if(preg_match('/[\'"]/',$formula[$i])>0 and is_null($ig)){
					$ig=$formula[$i];
				}elseif($formula[$i]==$ig and is_null($ig)===false){
					$ig=null;
				}elseif(is_null($ig)){
					switch ($formula[$i]) {
						case ',':
							$formula[$i]=$remp;
							$remp=':';
							break;
						case '(':
							$pila[]=$formula[$i];
							break;
						case ')':
							array_pop($pila);
							break;
					}
				}
				if(count($pila)==0) break;
			}
		}
		$formula=str_replace('IF(','(',$formula);
		//fin de if

		$metodos=get_class_methods('fnomina');
		foreach($metodos AS $metodo){
			$formula=str_replace($metodo.'(','$this->'.$metodo.'(',$formula);
		}

		$query = $this->ci->db->query("SELECT * FROM pers WHERE codigo=${CODIGO}");
		if ($query->num_rows() > 0){
			$rows = $query->row_array();

			foreach($rows AS $ind=>$valor){
				if($ind!='fnomina'){
					$valor=trim($valor);
					$ind='X'.strtoupper($ind);
					$formula=str_replace($ind,$valor,$formula);
				}
			}
		}
		$formula=str_replace('SUELDO_PROMEDIO', '$SPROME',  $formula);
		$formula=str_replace('XMONTO',          '$MONTO',   $formula);
		$formula=str_replace('XSUELDO',         '$SUELDO',  $formula);
		$formula=str_replace('XDIAS',           '$DIAS',    $formula);
		$formula=str_replace('DIAS_TRABAJADOS', '$DIAS',    $formula);
		$formula=str_replace('SUELDO_MINIMO',   '$SMINIMO', $formula);

		$formula=str_replace('XVARI1','$VARI1',$formula);
		$formula=str_replace('XVARI2','$VARI2',$formula);
		$formula=str_replace('XVARI3','$VARI3',$formula);
		$formula=str_replace('XVARI4','$VARI4',$formula);
		$formula=str_replace('XVARI5','$VARI5',$formula);
		$formula=str_replace('XVARI6','$VARI6',$formula);

		$formula=str_replace('XUT',$ut,$formula);
		$formula=str_replace('VAL(','floatval(',$formula);
		$formula=str_replace('TRAEVALOR','$this->ci->datasis->traevalor',$formula);

		$formula=str_replace('.AND.','&&'   ,$formula);
		$formula=str_replace('.OR.' ,'||'   ,$formula);
		$formula=str_replace('.NOT.','!'    ,$formula);
		$formula=str_replace('.T.'  ,'true' ,$formula);
		$formula=str_replace('.F.'  ,'false',$formula);

		return $formula;
	}

	//******************************************************************
	//  Crea Pretab => Tabla de Prenomina Resumen
	//
	function creapretab(){
		$prenom  ='prenom';
		$pretab  ='pretab';

		$this->ci->db->query("DROP TABLE IF EXISTS  ${pretab}");
		$mSQL  = "CREATE TABLE ${pretab} (";
		$mSQL .= "	codigo   CHAR(15)      NOT NULL DEFAULT '', ";
		$mSQL .= "	frec     CHAR(1)       NULL DEFAULT NULL, ";
		$mSQL .= "	fecha    DATE          NULL DEFAULT NULL, ";
		$mSQL .= "	nombre   CHAR(80)      NULL DEFAULT NULL, ";
		$mSQL .= "	total    DECIMAL(17,2) NULL DEFAULT '0.00',";

		$query = $this->ci->db->query("SELECT concepto FROM ${prenom} GROUP BY concepto ");
		foreach ($query->result() as $row){
			$mSQL .= "	c".$row->concepto." DECIMAL(17,2) DEFAULT 0.00, ";
		}
		$mSQL .= "	id       INT(11)       NOT NULL AUTO_INCREMENT, ";
		$mSQL .= "	PRIMARY KEY (id), ";
		$mSQL .= "	UNIQUE INDEX codigo (codigo) ";
		$mSQL .= ") ";
		$mSQL .= "COLLATE='latin1_swedish_ci' ";
		$mSQL .= "ENGINE=MyISAM; ";
		$this->ci->db->query($mSQL);

		// -- LLENA PRETAB
		$mSQL = "
		INSERT IGNORE INTO pretab (codigo, frec, fecha, nombre)
		SELECT a.codigo, b.tipo, a.fecha, a.nombre
		FROM prenom a JOIN noco b ON a.contrato=b.codigo
		GROUP BY a.codigo";
		$this->ci->db->query($mSQL);

	}

	//******************************************************************
	//  Crea Pretab => Tabla de Prenomina Resumen
	//
	function llenapretab(){
		$prenom  ='prenom';
		$pretab  ='pretab';
		$query = $this->ci->db->query("SELECT codigo, concepto, valor FROM ${prenom}");
		if ( $query->num_rows() > 0 ) {
			foreach( $query->result() as $row){
				$mSQL = "UPDATE ${pretab} SET c".$row->concepto."=".$row->valor.', total=total+'.$row->valor.' WHERE codigo="'.$row->codigo.'"';
				$this->ci->db->query($mSQL);
			}
		}
	}

	function _getarray($arr,$ind){
		if(isset($arr[$ind])){
			return $arr[$ind];
		}else{
			return 0;
		}
	}
}
