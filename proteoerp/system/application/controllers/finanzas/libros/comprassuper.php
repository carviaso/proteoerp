<?php
//Libro de compras para supermercado
class comprassuper{
	static function wlcsexcel($mes) {
		$CI =& get_instance();
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$CI->db->simple_query($mSQL);

		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$CI->db->simple_query($mSQL);
		$tasa = $CI->datasis->traevalor('TASA');

		$mSQL = "SELECT DISTINCT
			a.sucursal,
			IF(f.fecdoc IS NOT NULL, IF(a.tipo='NC',f.fecapl,f.fecdoc), a.fecha ) fecha,
			a.rif,
			IF(SUBSTR(a.rif,1,1)='V' AND d.nombre IS NOT NULL,d.nombre,IF(substr(a.rif,1,1)='J' AND d.nombre IS NOT NULL, d.nombre , a.nombre)) AS nombre,
			a.contribu,
			a.referen,
			a.planilla,'  ' meco1,
			COALESCE(e.nrorig,a.serie,a.numero) AS numero,
			a.nfiscal,
			IF(a.tipo='ND',a.numero,'        ') numnd,
			IF(a.tipo='NC',a.numero,'        ') numnc,
			IF(a.tipo='FC','01-Reg','03-Reg') oper,
			'        ' compla,
			SUM(a.gtotal*IF(a.tipo='NC',-1,1)) gtotal,
			SUM(a.exento*IF(a.tipo='NC',-1,1)) exento,
			SUM(a.general*IF(a.tipo='NC',-1,1)) general,
			SUM(a.geneimpu*IF(a.tipo='NC',-1,1)) geneimpu,
			SUM(a.adicional*IF(a.tipo='NC',-1,1)) adicional,
			SUM(a.adicimpu*IF(a.tipo='NC',-1,1)) adicimpu,
			SUM(a.reducida*IF(a.tipo='NC',-1,1)) reducida,
			SUM(a.reduimpu*IF(a.tipo='NC',-1,1)) reduimpu,
			SUM(b.reiva*IF(a.tipo='NC',-1,1)) reiva,
			CONCAT(EXTRACT(YEAR_MONTH FROM fechal),b.nrocomp) nrocomp,
			b.emision, a.numero numo, a.tipo
		FROM siva AS a
		LEFT JOIN riva    AS b ON a.numero=b.numero AND a.clipro=b.clipro AND a.tipo=b.tipo_doc AND MID(b.transac,1,1)<>'_'
		LEFT JOIN provoca AS d ON a.rif=d.rif
		LEFT JOIN scst    AS e ON a.numero=e.numero AND a.tipo=e.tipo_doc AND a.clipro=e.proveed AND a.fuente='CP'
		LEFT JOIN sprm    AS f ON a.numero=f.numero AND a.clipro=f.cod_prv AND f.tipo_doc='NC'
		WHERE libro='C' AND fechal BETWEEN $fdesde AND $fhasta AND a.fecha>0
		GROUP BY a.fecha,a.tipo,numo,a.rif
		UNION ALL
		SELECT DISTINCT a.sucursal,
			IF(e.recep IS NULL,a.fecha, a.fecha ) fecha,
			d.rif,
			d.nombre,
			a.contribu,
			a.referen,
			a.planilla,'  ' meco2,
			'*       ' numero,
			a.nfiscal,
			'        ' numnd,
			'        ' numnc,
			'01-Reg' oper,
			a.referen,
			a.gtotal   * 0,
			a.exento   * 0,
			a.general  * 0,
			a.geneimpu * 0,
			a.adicional* 0,
			a.adicimpu * 0,
			a.reducida * 0,
			a.reduimpu * 0,
			SUM(b.reiva*IF(a.tipo='NC',-1,1)) reiva,
			CONCAT(EXTRACT(YEAR_MONTH FROM fechal),b.nrocomp) nrocomp,
			b.emision, a.numero numo, a.tipo
		FROM siva AS a JOIN riva b ON a.numero=b.numero and a.clipro!=b.clipro AND a.tipo=b.tipo_doc AND MID(b.transac,1,1)<>'_' AND a.reiva=b.reiva
		LEFT JOIN sprv d ON b.clipro=d.proveed
		LEFT JOIN scst e ON a.numero=e.numero AND a.tipo=e.tipo_doc AND a.clipro=e.proveed  AND a.fuente='CP'
		WHERE libro='C' AND fechal BETWEEN $fdesde AND $fhasta AND a.fecha>0 AND a.reiva>0
		GROUP BY a.fecha,a.tipo,numo,a.rif
		ORDER BY fecha,numo " ;

		$export = $CI->db->query($mSQL);

		$fname = tempnam("/tmp","lcompras.xls");

		$CI->load->library("workbook", array("fname"=>$fname));
		$wb = & $CI->workbook ;
		$ws = & $wb->addworksheet($mes);

		# ANCHO DE LAS COLUMNAS
		$ws->set_column('A:A',4);
		$ws->set_column('B:C',11);
		$ws->set_column('D:D',45);
		$ws->set_column('E:F',5);
		$ws->set_column('G:H',11);
		$ws->set_column('I:I',6);
		$ws->set_column('J:J',11);
		$ws->set_column('K:K',6);
		$ws->set_column('L:T',16);
		$ws->set_column('X:X',16);
		$ws->set_column('U:U',18);
		$ws->set_column('V:V',11);

		// FORMATOS
		$h       =& $wb->addformat(array( "bold" => 1, "size" => 16, "merge" => 1, "top" => 1));
		$h1      =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'left'));
		$titulo  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 0, "fg_color" => 'silver' ));
		$cuerpo  =& $wb->addformat(array( "size" => 9 ));
		$numero  =& $wb->addformat(array(  "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));
		$tm      =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 'silver' ));

		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = "LIBRO DE COMPRAS CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL ".substr($mes,0,4);

		$ws->write(1, 0, $CI->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$CI->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<24; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};

		// TITULOS
		$mm=6;

		// PONE FONDO
		for ( $i=0; $i<24; $i++ ) {
			$ws->write_blank( $mm,   $i, $titulo );
			$ws->write_blank( $mm+1, $i, $titulo );
			$ws->write_blank( $mm+2, $i, $titulo );
		}

		$ws->write_string( $mm,  0, "", $titulo );
		$ws->write_string( $mm,  1, "Fecha", $titulo );
		$ws->write_string( $mm,  2, "", $titulo );
		$ws->write_string( $mm,  3, "", $titulo );
		$ws->write_string( $mm,  4, "Tipo", $titulo );
		$ws->write_string( $mm,  5, "Tipo", $titulo );
		$ws->write_string( $mm,  6, "Numero", $titulo );
		$ws->write_string( $mm,  7, "Numero", $titulo );
		$ws->write_string( $mm,  8, "Tipo", $titulo );
		$ws->write_string( $mm,  9, "Numero", $titulo );
		$ws->write_string( $mm, 10, "Tipo de", $titulo );
		$ws->write_string( $mm, 11, "Total Compras", $titulo );
		$ws->write_string( $mm, 12, "Compras sin", $titulo );
		$ws->write_string( $mm, 13, "COMPRAS GRAVADAS O CON DERECHO A CREDITO FISCAL", $tm );
		$ws->write_blank(  $mm, 14, $tm);
		$ws->write_blank(  $mm, 15, $tm);
		$ws->write_blank(  $mm, 16, $tm);
		$ws->write_blank(  $mm, 17, $tm);
		$ws->write_blank(  $mm, 18, $tm);
		$ws->write_string( $mm, 19,"I.V.A.", $titulo );
		$ws->write_string( $mm, 20, "Numero",$titulo );
		$ws->write_string( $mm, 21, "Fecha",$titulo );
		$ws->write_string( $mm, 22,  "I.V.A.", $titulo );
		$ws->write_string( $mm, 23,  "Anticipo", $titulo );
		$mm++;
		$ws->write_string( $mm,  0, "Oper.", $titulo );
		$ws->write_string( $mm,  1, "de la", $titulo );
		$ws->write_string( $mm,  2, "R.I.F.", $titulo );
		$ws->write_string( $mm,  3, "Nombre,", $titulo );
		$ws->write_string( $mm,  4, "de", $titulo );
		$ws->write_string( $mm,  5, "de", $titulo );
		$ws->write_string( $mm,  6, "del", $titulo );
		$ws->write_string( $mm,  7, "Control", $titulo );
		$ws->write_string( $mm,  8, "de", $titulo );
		$ws->write_string( $mm,  9, "Documento", $titulo );
		$ws->write_string( $mm, 10, "Compra", $titulo );
		$ws->write_string( $mm, 11, "Incluyendo", $titulo );
		$ws->write_string( $mm, 12, "Derecho a", $titulo );
		$ws->write_string( $mm, 13, "ALICUOTA GENERAL", $tm );
		$ws->write_blank(  $mm, 14, $tm);

		$ws->write_string( $mm, 15, "ALICUOTA ADICIONAL", $tm );
		$ws->write_blank(  $mm, 16, $tm);

		$ws->write_string( $mm, 17, "ALICUOTA REDUCIDA",$tm );
		$ws->write_blank(  $mm, 18, $tm);

		$ws->write_string( $mm, 19, "Retenido",$titulo );
		$ws->write_string( $mm, 20, "de", $titulo );
		$ws->write_string( $mm, 21, "de",$titulo );
		$ws->write_string( $mm, 22, "Retenido", $titulo );
		$ws->write_string( $mm, 23, "de I.V.A. por", $titulo );
		$mm++;
		$ws->write_string( $mm,  0, "Nro.", $titulo );
		$ws->write_string( $mm,  1, "Factura", $titulo );
		$ws->write_string( $mm,  2, "Proveedor", $titulo );
		$ws->write_string( $mm,  3, "Denominacion o Razon Social", $titulo );
		$ws->write_string( $mm,  4, "Prov.", $titulo );
		$ws->write_string( $mm,  5, "Doc.", $titulo );
		$ws->write_string( $mm,  6, "Documento", $titulo );
		$ws->write_string( $mm,  7, "Fiscal", $titulo );
		$ws->write_string( $mm,  8, "Trans.", $titulo );
		$ws->write_string( $mm,  9, "Afectado", $titulo );
		$ws->write_string( $mm, 10, "Nac/Imp.", $titulo );
		$ws->write_string( $mm, 11, "El I.V.A.", $titulo );
		$ws->write_string( $mm, 12, "Cred. Fiscal", $titulo );
		$ws->write_string( $mm, 13, "Base", $titulo );
		$ws->write_string( $mm, 14, "Impuesto", $titulo );
		$ws->write_string( $mm, 15, "Base", $titulo );
		$ws->write_string( $mm, 16, "Impuesto", $titulo );
		$ws->write_string( $mm, 17, "Base", $titulo );
		$ws->write_string( $mm, 18, "Impuesto", $titulo );
		$ws->write_string( $mm, 19, "Al Vendedor", $titulo );
		$ws->write_string( $mm, 20, "Comprobante", $titulo );
		$ws->write_string( $mm, 21, "Emision", $titulo );
		$ws->write_string( $mm, 22, "a Terceros", $titulo );
		$ws->write_string( $mm, 23, "Importacion", $titulo );

		$mm++;
		$ii = 1;
		$mtiva = 'X';
		$mfecha = '2000-00-00';
		$tventas = $texenta = $tbase   = $timpue  = $treiva  = $tperci  = 0 ;
		$dd=$mm;  // desde

		foreach( $export->result() as $row ) {
			$ws->write_string( $mm,  0, $ii, $cuerpo );
			$ws->write_string( $mm,  1, substr($row->fecha,8,2).'/'.substr($row->fecha,5,2).'/'.substr($row->fecha,0,4), $cuerpo );
			$ws->write_string( $mm,  2, $row->rif,      $cuerpo );
			$ws->write_string( $mm,  3, $row->nombre,   $cuerpo );
			$ws->write_string( $mm,  4, $row->contribu, $cuerpo );
			$ws->write_string( $mm,  5, $row->tipo,     $cuerpo );
			$ws->write_string( $mm,  6, $row->numero,   $cuerpo );
			$ws->write_string( $mm,  7, $row->nfiscal,  $cuerpo );
			$ws->write_string( $mm,  8, $row->oper,     $cuerpo );
			$ws->write_string( $mm,  9, $row->referen,  $cuerpo );
			$ws->write_string( $mm, 10, 'Nac.',         $cuerpo );

			$ws->write_number( $mm, 11, $row->gtotal,   $numero );
			$ws->write_number( $mm, 12, $row->exento,   $numero );
			$ws->write_number( $mm, 13, $row->general,  $numero );
			$ws->write_number( $mm, 14, $row->geneimpu, $numero );
			$ws->write_number( $mm, 15, $row->adicional,$numero );
			$ws->write_number( $mm, 16, $row->adicimpu, $numero );
			$ws->write_number( $mm, 17, $row->reducida, $numero );
			$ws->write_number( $mm, 18, $row->reduimpu, $numero );
			$ws->write_number( $mm, 19, $row->reiva,    $numero );

			$ws->write_string( $mm, 20, $row->nrocomp, $cuerpo );
			if ( !empty($row->emision) ) {
				$ws->write_string( $mm, 21, substr($row->emision,8,2).'/'.substr($row->emision,5,2).'/'.substr($row->emision,0,4), $cuerpo );
			} else {
				$ws->write_string( $mm, 21, $row->emision, $cuerpo );
			}
			$ws->write_number( $mm, 22, 0, $numero );
			$ws->write_number( $mm, 23, 0, $numero );
			$mm++;
			$ii++;
		}

		$celda = $mm+1;
		$fventas = "=J$celda";   // VENTAS
		$fexenta = "=K$celda";   // VENTAS EXENTAS
		$fbase   = "=L$celda";   // BASE IMPONIBLE
		$fiva    = "=N$celda";   // I.V.A.

		$ws->write_string( $mm, 0,"Totales...",  $tm );
		$ws->write_blank( $mm,  1,  $tm );
		$ws->write_blank( $mm,  2,  $tm );
		$ws->write_blank( $mm,  3,  $tm );
		$ws->write_blank( $mm,  4,  $tm );
		$ws->write_blank( $mm,  5,  $tm );
		$ws->write_blank( $mm,  6,  $tm );
		$ws->write_blank( $mm,  7,  $tm );
		$ws->write_blank( $mm,  8,  $tm );

		$ws->write_blank( $mm,  9,  $tm );
		$ws->write_blank( $mm, 10,  $tm );

		$ws->write_formula( $mm, 11, "=SUM(L$dd:L$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 12, "=SUM(M$dd:M$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm, 13, "=SUM(N$dd:N$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm, 14, "=SUM(O$dd:O$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 15, "=SUM(P$dd:P$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm, 16, "=SUM(Q$dd:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm, 17, "=SUM(R$dd:R$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 18, "=SUM(S$dd:S$mm)", $Tnumero );   //"VENTAS EXENTAS"

		$ws->write_formula( $mm, 19, "=SUM(T$dd:T$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm, 22, "=SUM(W$dd:W$mm)", $Tnumero );   //"VENTAS EXENTAS"

		$ws->write_formula( $mm, 23, "=SUM(X$dd:X$mm)", $Tnumero );   //"VENTAS EXENTAS"

		$mm ++;
		$mm ++;
		$ws->write_string($mm,  3, 'RESUMEN DE COMPRAS Y CREDITOS:', $tm );
		$ws->write_blank($mm,   4,$tm);
		$ws->write_blank($mm,   5,$tm);
		$ws->write_blank($mm,   6,$tm);
		$ws->write_blank($mm,   7,$tm);
		$ws->write_blank($mm,   8,$tm);
		$ws->write_blank($mm,   9,$tm);
		$ws->write_blank($mm,  10,$tm);
		$ws->write_string($mm, 11, 'Base Imponible', $titulo );
		$ws->write_string($mm, 12, 'Credito Fiscal', $titulo );

		$ws->write_string($mm,  14, 'IVA Retenido', $titulo );
		$ws->write_string($mm,  15, 'Anticipo IVA', $titulo );

		$mm ++;
		$ws->write_string($mm,   3, 'Total Compras no Gravadas o/y sin dereco a Credito', $h1 );
		$ws->write_formula($mm, 11, "=M$celda" , $Rnumero );

		$mm ++;
		$ws->write_string($mm,   3, 'Total Importaciones Gravadas por Alicuota General:', $h1 );
		$ws->write_formula($mm, 11, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 12, "=0+0" , $Rnumero );

		$ws->write_formula($mm, 14, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 15, "=0+0" , $Rnumero );

		$mm ++;
		$ws->write_string($mm,   3, 'Total Importaciones Gravadas por Alicuota General mas Adicional:', $h1 );
		$ws->write_formula($mm, 11, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 12, "=0+0" , $Rnumero );

		$ws->write_formula($mm, 14, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 15, "=0+0" , $Rnumero );

		$mm ++;
		$ws->write_string($mm,   3, 'Total Importaciones Gravadas por Alicuota Reducida:', $h1 );
		$ws->write_formula($mm, 11, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 12, "=0+0" , $Rnumero );

		$ws->write_formula($mm, 14, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 15, "=0+0" , $Rnumero );

		$mm ++;
		$ws->write_string($mm,   3, 'Total Compras Internas Gravadas por Alicuota General:', $h1 );
		$ws->write_formula($mm, 11, "=N$celda" , $Rnumero );
		$ws->write_formula($mm, 12, "=O$celda" , $Rnumero );

		$ws->write_formula($mm, 14, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 15, "=0+0" , $Rnumero );

		$mm ++;
		$ws->write_string($mm,   3, 'Total Compras Internas Gravadas por Alicuota General mas Adicional:', $h1 );
		$ws->write_formula($mm, 11, "=P$celda" , $Rnumero );
		$ws->write_formula($mm, 12, "=Q$celda" , $Rnumero );

		$ws->write_formula($mm, 14, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 15, "=0+0" , $Rnumero );

		$mm ++;
		$ws->write_string($mm,   3, 'Total Compras Internas Gravadas por Alicuota Reducida:', $h1 );
		$ws->write_formula($mm, 11, "=R$celda" , $Rnumero );
		$ws->write_formula($mm, 12, "=S$celda" , $Rnumero );

		$ws->write_formula($mm, 14, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 15, "=0+0" , $Rnumero );

		$mm ++;
		$wb->close();
		header("Content-type: application/x-msexcel; name=\"lcompras.xls\"");
		header("Content-Disposition: inline; filename=\"lcompras.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
	}
}
