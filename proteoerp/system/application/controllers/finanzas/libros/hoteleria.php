<?php
class hoteleria{

	static function generest($mes) {
		$CI =& get_instance();
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		// BORRA LA GENERADA ANTERIOR
		$CI->db->simple_query("DELETE FROM siva WHERE EXTRACT(YEAR_MONTH FROM fechal) = $mes AND fuente='FR' ");
		// ARREGLA LAS TASAS NULAS EN SFAC
		$CI->db->simple_query("UPDATE rfac SET tasa=0, montasa=0, reducida=0, monredu=0, sobretasa=0, monadic=0, exento=0 WHERE (tasa IS NULL OR montasa IS NULL) AND EXTRACT(YEAR_MONTH FROM fecha)=$mes ");
		$mSQL = "INSERT INTO siva
			(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
			referen, planilla, clipro, nombre, contribu, rif, registro,
			nacional, exento, general, geneimpu,
			adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
			gtotal, reiva, fechal, fafecta,serial)
			SELECT 0 AS id,
			'V' AS libro,
			IF(a.tipo='D','NC',CONCAT('F',a.tipo)) AS tipo,
			'FR' AS fuente,
			'00' AS sucursal,
			a.fecha,
			a.nfiscal,
			' ' AS numhasta,
			' ' AS caja,
			a.nfiscal AS nfiscal,
			a.nfiscal AS nhfiscal,
			IF(a.tipo='E',a.numero,a.numero ) AS referen,
			'  ' AS planilla,
			a.cod_cli AS clipro,
			IF(a.tipo='X','DOCUMENTO ANULADO.......',c.nombre),
			IF(c.tiva='C','CO','NO') AS contribu,
			IF(c.rifci='',a.rifci,c.rifci),
			'01' AS registro,
			'S' AS nacional,
			a.servicio*(a.tipo<>'X')  AS exento,
			a.stotal*(a.tipo<>'X') AS general,
			a.impuesto*(a.tipo<>'X') AS geneimpu,
			0 AS adicional,
			0 AS adicimpu,
			0 AS reducida,
			0 AS reduimpu,
			a.stotal*(a.tipo<>'X') AS stotal,
			a.impuesto*(a.tipo<>'X')    AS impuesto,
			a.gtotal*(a.tipo<>'X') AS gtotal,
			0 AS reiva,
			".$mes."01 AS fechal,
			0 AS fafecta,
			a.maqfiscal AS serial
			FROM rfac AS a
			LEFT JOIN scli AS c ON a.cod_cli=c.cliente
			WHERE a.fecha BETWEEN $fdesde AND $fhasta AND a.tipo NOT IN ('P','T')";
		$flag=$CI->db->simple_query($mSQL);
		if(!$flag) memowrite($mSQL,'generest');

		// CARGA LAS RETENCIONES DE IVA DE CONTADO
		$mSQL = "SELECT * FROM sfpa WHERE tipo='RI' AND	EXTRACT(YEAR_MONTH FROM f_factura)=$mes AND tipo_doc='FE' ";
		$query = $CI->db->query($mSQL);
		if ($query->num_rows() > 0) {
			foreach ( $query->result() AS $row ){
				$mSQL = "UPDATE siva SET reiva=".$row->monto.", comprobante='20".$row->num_ref."' WHERE tipo='".$row->tipo_doc."' AND numero='".$row->numero."' AND libro='V' AND EXTRACT(YEAR_MONTH FROM fechal)=$mes ";
				$flag=$CI->db->simple_query($mSQL); if(!$flag) memowrite($mSQL,'generest');
			}
		}

		// CARGA LAS RETENCIONES DE IVA DESDE SMOV
		$mSQL = "SELECT a.tipo_doc, a.fecha, a.numero, c.nombre, c.rifci, a.cod_cli, b.monto,
				a.numero AS afecta, a.fecha AS fafecta, a.reteiva, a.transac, a.nroriva, a.emiriva, a.recriva
			FROM itccli AS a JOIN smov AS b ON a.transac=b.transac
				LEFT JOIN scli AS c ON a.cod_cli=c.cliente
			WHERE b.fecha BETWEEN $fdesde AND $fhasta AND b.cod_cli='REIVA'
				AND a.reteiva>0 AND b.monto>b.abonos ";

		$query = $CI->db->query($mSQL);
		if ($query->num_rows() > 0) {
			foreach ( $query->result() AS $row ){
				$mSQL = "UPDATE siva SET reiva=".$row->reteiva.", comprobante='".$row->nroriva."', fecharece='$row->recriva'  WHERE tipo='".$row->tipo_doc."' AND numero='".$row->numero."' AND libro='V' AND EXTRACT(YEAR_MONTH FROM fechal)=$mes ";
				$flag=$CI->db->simple_query($mSQL); if(!$flag) memowrite($mSQL,'generest');
			}
		}
	}

	static function genehotel($mes) {
		$CI =& get_instance();
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		// BORRA LA GENERADA ANTERIOR
		$CI->db->simple_query("DELETE FROM siva WHERE EXTRACT(YEAR_MONTH FROM fechal) = $mes AND fuente='FH' ");
		// ARREGLA LAS TASAS NULAS EN SFAC
		$CI->db->simple_query("UPDATE hfac SET tasa=0, montasa=0, reducida=0, monredu=0, sobretasa=0, monadic=0, exento=0 WHERE (tasa IS NULL OR montasa IS NULL) AND EXTRACT(YEAR_MONTH FROM fecha)=$mes ");

		$mSQL = "INSERT INTO siva
			(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
			referen, planilla, clipro, nombre, contribu, rif, registro,
			nacional, exento, general, geneimpu,
			adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
			gtotal, reiva, fechal, fafecta)
			SELECT 0 AS id,
			'V' AS libro,
			IF(a.tipo='D','NC',IF(a.tipo='F','FC',CONCAT('F',a.tipo))) AS tipo,
			'FH' AS fuente,
			'00' AS sucursal,
			a.fecha_ou,
			a.num_fac,
			' ' AS numhasta,
			' ' AS caja,
			' ' AS nfiscal,
			'  ' AS nhfiscal,
			IF(a.tipo='E',a.num_fac,a.num_fac ) AS referen,
			'  ' AS planilla,
			a.cod_cli AS clipro,
			IF(a.tipo='X','DOCUMENTO ANULADO.......',if(c.nombre='',a.nombre,c.nombre)),
			IF(c.tiva='C','CO','NO') AS contribu,
			IF(c.rifci='', a.cedula, c.rifci ),
			'01' AS registro,
			'S' AS nacional,
			0   AS exento,
			a.total*(a.tipo<>'X') AS general,
			a.iva*(a.tipo<>'X') AS geneimpu,
			0 AS adicional,
			0 AS adicimpu,
			0 AS reducida,
			0 AS reduimpu,
			a.total*(a.tipo<>'X') AS stotal,
			a.iva*(a.tipo<>'X')    AS impuesto,
			a.totalg*(a.tipo<>'X') AS gtotal,
			0 AS reiva,
			".$mes."01 AS fechal,
			0 AS fafecta
			FROM hfac AS a
			LEFT JOIN scli AS c ON a.cod_cli=c.cliente
			WHERE a.fecha_ou BETWEEN $fdesde AND $fhasta AND a.tipo NOT IN ('P','T')";
		$flag=$CI->db->simple_query($mSQL); if(!$flag) memowrite($mSQL,'genehotel');

		//CARGA LAS RETENCIONES DE IVA DE CONTADO
		$mSQL = "SELECT * FROM sfpa WHERE tipo='RI' AND	f_factura BETWEEN $fdesde AND $fhasta AND tipo_doc='FE' ";
		$query = $CI->db->query($mSQL);
		if ($query->num_rows() > 0) {
			foreach ( $query->result() AS $row ) {
				$mSQL = "UPDATE siva SET reiva=".$row->monto.", comprobante='20".$row->num_ref."' WHERE tipo='".$row->tipo_doc."' AND numero='".$row->numero."' AND libro='V' AND fechal BETWEEN $fdesde AND $fhasta ";
				$flag=$CI->db->simple_query($mSQL); if(!$flag) memowrite($mSQL,'genehotel');
			}
		}

		//CARGA LAS RETENCIONES DE IVA DESDE SMOV
		$mSQL = "SELECT a.tipo_doc, a.fecha, a.numero, c.nombre, c.rifci, a.cod_cli, b.monto,
				a.numero AS afecta, a.fecha AS fafecta, a.reteiva, a.transac, a.nroriva, a.emiriva, a.recriva
			FROM itccli AS a JOIN smov AS b ON a.transac=b.transac
				LEFT JOIN scli AS c ON a.cod_cli=c.cliente
			WHERE b.fecha BETWEEN $fdesde AND $fhasta AND b.cod_cli='REIVA'
				AND a.reteiva>0 AND b.monto>b.abonos ";

		$query = $CI->db->query($mSQL);
		if ($query->num_rows() > 0) {
			foreach ( $query->result() AS $row ){
				$mSQL = "UPDATE siva SET reiva=".$row->reteiva.", comprobante='".$row->nroriva."', fecharece='$row->recriva'  WHERE tipo='".$row->tipo_doc."' AND numero='".$row->numero."' AND libro='V' AND fechal BETWEEN $fdesde AND $fhasta ";
				$flag=$CI->db->simple_query($mSQL); if(!$flag) memowrite($mSQL,'genehotel');
			}
		}
	}
}
