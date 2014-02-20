<?php
class ingresos{
	function genesmov($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		$this->db->simple_query("DELETE FROM siva WHERE EXTRACT(YEAR_MONTH FROM fechal) = $mes AND fuente='MC' ");

		$mSQL= "SELECT a.*,b.rifci, c.numero AS afecta, c.fecha AS fafecta
				FROM smov AS a LEFT JOIN scli AS b ON a.cod_cli=b.cliente
				LEFT JOIN itccli AS c ON a.numero=c.numccli AND a.tipo_doc=c.tipoccli
				LEFT JOIN grcl AS d ON b.grupo=d.grupo
				WHERE a.fecha BETWEEN $fdesde AND $fhasta
				AND a.tipo_doc IN ('NC')
				AND d.clase!='I'
				AND a.observa1 NOT LIKE '%DEVOLUCION%'
				AND a.codigo!='NOCON'
				AND a.codigo!='' AND a.cod_cli<>'REIVA'";

		//Procesando CxC smov    "
		$query = $this->db->query($mSQL);
		$mNUMERO  = 'ASDFGHJK';
		$mTIPO_DOC = 'XX';

		foreach ( $query->result() as $row ){
			if ( $row->tipo_doc == 'NC' ){
				if ($mTIPO_DOC == $row->tipo_doc AND $mNUMERO == $row->numero ) continue;
				$mNUMERO = $row->numero;
				$mTIPO_DOC = $row->tipo_doc;
			}
			$referen = $row->num_ref;
			$registro = '01';
			if ( !empty($row->afecta) ) {
				$referen = $row->afecta;
				$aaa = $this->datasis->ivaplica($row->fafecta);
				$bbb = $this->datasis->ivaplica($row->fecha);
				if ( $aaa != $bbb )  $registro='04';
			}

			$stotal = $row->monto-$row->impuesto;
			$data=array();
			$data['libro']    = 'V';
			$data['tipo']     = $row->tipo_doc;
			$data['fuente']   = 'MC';
			$data['sucursal'] = '00';
			$data['fecha']    = $row->fecha;
			$data['numero']   = $row->numero;
			$data['clipro']   = $row->cod_cli;
			$data['nombre']   = $row->nombre;
			$data['contribu'] = 'CO';
			$data['rif']      = $row->rifci;
			$data['registro'] = $registro;
			$data['nacional'] = 'S';
			$data['referen']  = $referen;
			$data['general']  = (empty($row->montasa))?  0 :$row->montasa;
			$data['geneimpu'] = (empty($row->tasa))?     0 :$row->tasa;
			$data['reducida'] = (empty($row->monredu))?  0 :$row->monredu;
			$data['reduimpu'] = (empty($row->reducida))? 0 :$row->reducida;
			$data['adicional']= (empty($row->monadic))?  0 :$row->monadic;
			$data['adicimpu'] = (empty($row->sobretasa))?0 :$row->sobretasa;
			$data['exento']   = (empty($row->exento))?   0 :$row->exento;
			$data['impuesto'] = (empty($row->impuesto))? 0 :$row->impuesto;
			$data['gtotal']   = (empty($row->monto))?    0 :$row->monto;
			$data['stotal']   = $stotal;
			$data['reiva']    = $row->reteiva;
			$data['fechal']   = $mes.'01';
			$data['fafecta']  = $row->fafecta;
			$data['nfiscal']  = $row->nfiscal;

			$mSQL = $this->db->insert_string('siva', $data);
			$flag=$this->db->simple_query($mSQL);
			if(!$flag){ memowrite($mSQL,'genesmov'); }
		}

		// RETENCIONES DE IVA DEL MISMO MES
		/*$mSQL = "SELECT b.fecha, a.numero, c.nombre, c.rifci, a.cod_cli,
						a.numero AS afecta, a.fecha AS fafecta, a.reteiva, a.transac, a.nroriva
				FROM itccli AS a JOIN smov AS b ON a.transac=b.transac
					 LEFT JOIN scli AS c ON a.cod_cli=c.cliente
				WHERE b.fecha BETWEEN $fdesde AND $fhasta AND b.cod_cli='REIVA'
					AND a.reteiva>0 AND b.monto>b.abonos
					AND EXTRACT(YEAR_MONTH FROM a.fecha)=EXTRACT(YEAR_MONTH FROM b.fecha) ";

		$querE b.fecha BETWEEN $fdesde AND $fhasta AND b.cod_cli='REIVA'
		foreach ( $query->result() as $row ){
			$mSQL = "UPDATE siva SET reiva=$row->reteiva, comprobante=$row->nroriva WHERE tipo='FC' AND numero='$row->numero' AND libro='V' AND EXTRACT(YEAR_MONTH FROM fechal)=$mes ";
			$flag=$this->db->simple_query($mSQL);
			if(!$flag) memowrite($mSQL,'genesmov');
		}*/

		// RETENCIONES DE IVA
		$mSQL = "SELECT b.fecha, a.numero, c.nombre, c.rifci, COALESCE(c.cliente,a.cod_cli) AS cod_cli, a.numero AS afecta, a.fecha AS fafecta, (a.reteiva) reteiva, a.transac, a.nroriva, a.emiriva, if(a.recriva IS NULL, a.estampa, a.recriva) recriva, d.nfiscal
			FROM itccli AS a
			JOIN smov AS b ON a.transac=b.transac
			LEFT JOIN scli AS c ON a.cod_cli=c.cliente
			JOIN sfac AS d ON a.numero=d.numero AND d.tipo_doc='F'
			WHERE b.cod_cli='REIVA' AND a.reteiva>0 AND b.fecha BETWEEN ${fdesde} AND ${fhasta} AND a.nroriva IS NOT NULL
			UNION ALL
			SELECT b.fecha, a.numero, IF(LENGTH(TRIM(e.nomfis))>0,e.nomfis,e.nombre) AS nombre, e.rifci, a.clipro, a.factura AS afecta, d.fecha AS fafecta, b.monto, a.transac, a.retencion, a.fecha, a.fecha, d.nfiscal
			FROM smov AS b
			JOIN prmo AS a ON a.transac=b.transac
			LEFT JOIN sfac AS d ON a.factura=d.numero AND d.tipo_doc='F'
			LEFT JOIN scli AS e ON d.cod_cli=e.cliente
			WHERE b.fecha BETWEEN ${fdesde} AND ${fhasta} AND b.cod_cli='REIVA'
			UNION ALL
			SELECT b.fecha, '000000000000' AS numero, IF(LENGTH(TRIM(f.nomfis))>0,f.nomfis,f.nombre) AS nombre, f.rifci AS rifci, a.proveed AS clipro, MID(d.onumero,3,8) AS afecta, d.ofecha AS fafecta, d.monto, a.transac, '00000000000000' AS retencion, a.fecha, a.fecha, e.nfiscal AS nfiscal
			FROM smov AS b
			JOIN cruc AS a ON a.transac=b.transac
			JOIN itcruc AS d ON a.numero=d.numero
			JOIN sfac AS e ON MID(d.onumero,3,8)=e.numero AND e.tipo_doc=MID(d.onumero,1,1)
			JOIN scli AS f ON e.cod_cli=f.cliente
			WHERE b.fecha BETWEEN ${fdesde} AND ${fhasta} AND b.cod_cli='REIVA'
			UNION ALL
			SELECT b.fecha fecha, a.numero numero, d.nombre nombre, d.rifci rifci, d.cliente cod_cli, a.numero afecta, a.fecha fafecta, IF(c.tipo_doc='NC',-1,1)*a.reiva reteiva, a.transac transac, CONCAT(b.periodo, b.nrocomp) nroriva, b.emision emiriva, b.fecha recriva, a.nfiscal nfiscal
			FROM  itrivc a
			JOIN rivc b ON a.idrivc = b.id
			JOIN smov c ON b.transac = c.transac  AND a.numero=c.num_ref
			JOIN scli d ON b.cod_cli = d.cliente
			WHERE c.cod_cli='REIVA' AND b.fecha BETWEEN ${fdesde} AND ${fhasta}
			UNION ALL
			SELECT a.f_factura fecha, a.numero, IF(LENGTH(TRIM(c.nomfis))>0,c.nomfis,c.nombre) AS nombre, c.rifci, a.cod_cli, a.numero AS afecta, a.fecha AS fafecta, a.monto reteiva, a.transac, a.num_ref nroiva, a.fecha emiriva, a.fecha recriva, d.nfiscal
			FROM sfpa a
			JOIN smov b ON a.f_factura=b.fecha AND a.monto=b.monto AND a.numero=MID(b.observa1,12,8)
			JOIN scli c ON a.cod_cli=c.cliente
			JOIN sfac d ON a.transac=d.transac
			WHERE a.f_factura BETWEEN ${fdesde} AND ${fhasta} AND a.tipo='RI' AND b.tipo_doc='ND'
		";
		$query = $this->db->query($mSQL);

		$data=array();
		foreach ( $query->result() as $row ){
			$data['libro']      ='V';
			$data['tipo']       ='CR';
			$data['fuente']     ='MC';
			$data['sucursal']   ='99';
			$data['fecha']      =$row->emiriva;
			$data['numero']     =(empty($row->nroriva))? 'ARREGLAR' : $row->nroriva;
			$data['clipro']     =$row->cod_cli;
			$data['nombre']     =$row->nombre;
			$data['contribu']   ='CO';
			$data['rif']        =$row->rifci;
			$data['registro']   ='01';
			$data['nacional']   ='S';
			$data['referen']    ='';
			$data['exento']     =0;
			$data['general']    =0;
			$data['geneimpu']   =0;
			$data['reducida']   =0;
			$data['reduimpu']   =0;
			$data['adicional']  =0;
			$data['adicimpu']   =0;
			$data['impuesto']   =0;
			$data['gtotal']     =0;
			$data['stotal']     =0;
			$data['reiva']      =$row->reteiva;
			$data['comprobante']='';
			$data['fecharece']  =$row->recriva;
			$data['fechal']     =$mes.'01';
			$data['nfiscal']    =$row->nfiscal;
			$data['fafecta']    =$row->fafecta;
			$data['afecta']     =$row->afecta;

			$mSQL = $this->db->insert_string('siva', $data);

			$flag=$this->db->simple_query($mSQL);
			if(!$flag) memowrite($mSQL,'genesmov');
		}

		//Retenciones de rivc
		/*
		if($this->db->table_exists('rivc')){
			$data=array();
			$mSQL="SELECT a.fecha, CONCAT(a.periodo,a.nrocomp) AS nroriva, c.nombre, c.rifci, a.cod_cli,
					b.numero AS afecta, b.fecha AS fafecta, b.reiva AS reteiva, a.transac, a.emision,
					a.fecha AS recriva, '' AS nfiscal, b.tipo_doc
				FROM rivc AS a
				JOIN itrivc AS b ON a.id=b.idrivc
				LEFT JOIN scli AS c ON a.cod_cli=c.cliente
				WHERE a.anulado='N' AND b.reiva>0
					AND a.fecha BETWEEN $fdesde AND $fhasta";
			$query = $this->db->query($mSQL);

			foreach ( $query->result() as $row ){
				$factor=($row->tipo_doc=='F')? 1:-1;
				$data['libro']      ='V';
				$data['tipo']       ='CR';
				$data['fuente']     ='MC';
				$data['sucursal']   ='99';
				$data['fecha']      =$row->emision;
				$data['numero']     =$row->nroriva;
				$data['clipro']     =$row->cod_cli;
				$data['nombre']     =$row->nombre;
				$data['contribu']   ='CO';
				$data['rif']        =$row->rifci;
				$data['registro']   ='01';
				$data['nacional']   ='S';
				$data['referen']    ='';
				$data['exento']     =0;
				$data['general']    =0;
				$data['geneimpu']   =0;
				$data['reducida']   =0;
				$data['reduimpu']   =0;
				$data['adicional']  =0;
				$data['adicimpu']   =0;
				$data['impuesto']   =0;
				$data['gtotal']     =0;
				$data['stotal']     =0;
				$data['reiva']      =$factor*$row->reteiva;
				$data['comprobante']='';
				$data['fecharece']  =$row->recriva;
				$data['fechal']     =$mes.'01';
				$data['nfiscal']    =$row->nfiscal;
				$data['fafecta']    =$row->fafecta;
				$data['afecta']     =$row->afecta;

				$mSQL = $this->db->insert_string('siva', $data);
				$flag=$this->db->simple_query($mSQL);
				if(!$flag) memowrite($mSQL,'genesmov');
			}
		}*/

		//RETENCIONES ANTERIORES PENDIENTES
		/*$mSQL = "SELECT * FROM smov WHERE fecha<".$mes."01 AND cod_cli='REIVA'
				 AND control IS NULL AND monto>abonos AND (tipo_ref<>'PR' OR tipo_ref IS NULL) ";
		$query = $this->db->query($mSQL);

		foreach ( $query->result() as $row ){
			$mSQL = "SELECT COUNT(*) FROM sfpa WHERE tipo_doc='FE' AND tipo='RI'
					AND fecha='".$row->fecha."' AND '$row->observa1' LIKE CONCAT('%',numero,'%')";
			if ( $this->datasis->dameval($mSQL) <= 0 ) continue;
			$mSQL = "SELECT numero, cod_cli, transac
					FROM sfpa
					WHERE tipo_doc='FE' AND tipo='RI' AND fecha='".$row->fecha."' AND
					'".$row->observa1."' LIKE CONCAT('%',numero,'%')";

			$query1 = $this->db->query($mSQL);
			$mREG   = $query1->result();
			$transac = $mREG->transac;
			$nombre = dameval("select nombre from sfac where transac='$transac'");
			$rif    = dameval("select rifci  from sfac where transac='$transac'");
			$mSQL = "INSERT INTO siva SET
						libro    = 'V',
						tipo     = 'RI',
						fuente   = 'FA',
						sucursal = '00',
						fecha    = '$row->fecha',
						numero   = '$row->numero',
						referen  = '$row->numero',
						clipro   = '$row->cod_cli',
						nombre   = '$nombre',
						contribu = 'CO',
						rif      = '$rif',
						registro = '01',
						nacional = 'S',
						exento   = 0,
						fafecta  = '$row->fecha',
						general  = 0,
						geneimpu = 0,
						reducida = 0,
						reduimpu = 0,
						adicional= 0,
						adicimpu = 0,
						impuesto = 0,
						gtotal   = 0,
						stotal   = 0,
						reiva    = ".$row->monto.",
						fechal   = ".$mes."01 ";
			$flag=$this->db->simple_query($mSQL);
			if(!$flag) memowrite($mSQL,'genesmov');
		}*/
	}

	function geneotin($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		$this->db->simple_query("DELETE FROM siva WHERE EXTRACT(YEAR_MONTH FROM fechal) = $mes AND fuente='OT' ");
		$mSQL = "INSERT INTO siva
				(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
				referen, planilla, clipro, nombre, contribu, rif, registro,
				nacional, exento, general, geneimpu,
				adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
				gtotal, reiva, fechal, fafecta )
				SELECT 0 AS id,
				'V' AS libro,
				a.tipo_doc AS tipo,
				'OT' AS fuente,
				'00' AS sucursal,
				b.fecha,
				b.numero,
				' ' AS numhasta,
				' ' AS caja,
				b.nfiscal AS nfiscal,
				'  ' AS nhfiscal,
				b.afecta AS referen,
				'  ' AS planilla,
				b.cod_cli AS clipro,
				b.nombre,
				'CO' AS contribu,
				c.rifci,
				'01' AS registro,
				'S' AS nacional,
				b.exento exento,
				b.montasa general,
				b.tasa geneimpu,
				b.monadic adicional,
				b.sobretasa adicimpu,
				b.monredu reducida,
				b.reducida reduimpu,
				b.totals stotal,
				b.iva AS impuesto,
				b.totalg AS gtotal,
				0 AS reiva,
				".$mes."01 fechal,
				b.fafecta fafecta
				FROM itotin AS a JOIN otin AS b ON a.numero=b.numero AND a.tipo_doc=b.tipo_doc
				LEFT JOIN scli AS c ON b.cod_cli=c.cliente LEFT JOIN grcl AS d ON c.grupo=d.grupo
				WHERE d.clase!='I' AND
				b.fecha BETWEEN ${fdesde} AND ${fhasta}
				AND (b.iva > 0 OR b.tipo_doc IN ('FC','ND') )
				GROUP BY a.tipo_doc,a.numero ";
		$flag=$this->db->simple_query($mSQL);
		if(!$flag) memowrite($mSQL,'geneotin');
	}
}
