<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class ventassuper{
	static function wlvexcelpdv1($mes) {
		$CI =& get_instance();
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');
		$tasa = $CI->datasis->traevalor('TASA');

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$CI->db->simple_query($mSQL);

		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$CI->db->simple_query($mSQL);

		$mSQL = "UPDATE club SET cedula=CONCAT('V',cedula) WHERE MID(cedula,1,1) IN ('0','1','2','3','4','5','6','7','8','9')  ";
		$CI->db->simple_query($mSQL);

		$mSQL ="SELECT a.fecha AS fecha, a.numero AS numero, a.numero AS final, c.cedula AS rif,
		    CONCAT(c.nombres,' ', c.apellidos) AS nombre,
		' ' AS numnc,
		    ' ' AS numnd,
		    IF(MID(a.numero,1,2)='NC','NC','FC') AS tipo_doc,
		' ' AS afecta,
		    SUM(a.monto) ventatotal,
		    SUM(a.monto*(a.impuesto=0)) exento,
		ROUND(SUM(a.monto*(a.impuesto>0)*100/(100+a.impuesto)),2) base,
		    '14%' AS alicuota,
		    SUM(a.monto*(a.impuesto>0) - a.monto*(a.impuesto>0)*100/(100+a.impuesto)) AS cgimpu,
		0 AS reiva,
		    ' ' comprobante,
		    ' ' fecharece,
		' ' impercibido,
		    ' ' importacion,
		    IF(c.cedula IS NOT NULL,IF(MID(c.cedula,1,1) IN ('V','E'), IF(CHAR_LENGTH(MID(c.cedula,2,10))=9,'SI','NO'), 'SI' ), 'NO') tiva,
		b.tipo,
		    a.numero numa,
		    a.caja
		FROM vieite a
		    LEFT JOIN viefac b ON a.numero=b.numero and a.caja=b.caja
		    LEFT JOIN club c ON b.cliente=c.cod_tar
		WHERE EXTRACT(YEAR_MONTH FROM a.fecha)=$mes
		    GROUP BY a.fecha, a.caja, numa
		    UNION
		SELECT
		    a.fecha AS fecha,
		    IF(a.tipo='FC', a.numero, '        ' ) AS NUMERO,
		'        ' AS FINAL,
		    a.rif AS RIF,
		    IF( MID(a.rif,1,1) NOT IN ('J','G') AND b.contacto IS NOT NULL AND b.contacto!='',b.contacto,a.nombre) AS NOMBRE,
		IF(a.tipo='NC',a.numero,'        ') AS NUMNC,
		    IF(a.tipo='ND',a.numero,'        ') AS NUMND,
		    a.tipo AS TIPO_DOC,
		IF(a.referen=a.numero,'        ',a.referen) AS AFECTA,
		    a.gtotal*IF(a.tipo='NC',-1,1) VENTATOTAL,
		    a.exento*IF(a.tipo='NC',-1,1)  EXENTO,
		a.general*IF(a.tipo='NC',-1,1) BASE,
		    '$tasa%' AS ALICUOTA,
		    a.impuesto*IF(a.tipo='NC',-1,1) AS CGIMPU,
		a.reiva*IF(a.tipo='NC',-1,1),
		    '              ' COMPROBANTE,
		    '            ' FECHACOMP,
		'            ' IMPERCIBIDO,
		    '            ' IMPORTACION,
		    'SI' tiva, a.tipo, a.numero numa, 'MAYO' caja
		    FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
		    WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.fuente<>'PV' AND a.tipo<>'RI'
		    UNION
		    SELECT
		a.fecha AS fecha,
		    IF(a.tipo='FC', a.numero, '        ' ) AS NUMERO,
		    a.numero AS FINAL,
		a.rif AS RIF,
		    IF(MID(a.rif,1,1) NOT IN ('J','G') AND b.contacto IS NOT NULL AND b.contacto!='',b.contacto,a.nombre) AS NOMBRE,
		    IF(a.tipo='NC',a.numero,'        ') AS NUMNC,
		    IF(a.tipo='ND',a.numero,'        ') AS NUMND,
		a.tipo AS TIPO_DOC,
		    IF(a.referen=a.numero,'        ',a.referen) AS AFECTA,
		    a.gtotal*IF(a.tipo='NC',-1,1) VENTATOTAL,
		a.exento*IF(a.tipo='NC',-1,1)  EXENTO,
		    a.general*IF(a.tipo='NC',-1,1) BASE,
		    '$tasa%' AS ALICUOTA,
		a.impuesto*IF(a.tipo='NC',-1,1) AS CGIMPU,
		    a.reiva*IF(a.tipo='NC',-1,1),
		    c.nroriva COMPROBANTE,
		c.emiriva FECHACOMP,
		    '            ' IMPERCIBIDO,
		    '            ' IMPORTACION,
		'SI' tiva, a.tipo, a.numero numa, 'MAYO' caja
		    FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente LEFT JOIN itccli c ON a.numero=c.numero AND a.clipro=c.cod_cli
		    WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.fuente<>'PV' AND a.tipo='RI'
		ORDER BY fecha, caja, numa ";

		$export = $CI->db->query($mSQL);
		$fname = tempnam("/tmp","lventas.xls");
		$CI->load->library("workbook",array("fname" => $fname));;
		$wb =& $CI->workbook;
		$ws =& $wb->addworksheet($mes);

		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:A',11);
		$ws->set_column('B:B',6);
		$ws->set_column('C:D',8.5);
		$ws->set_column('E:E',11.5);
		$ws->set_column('F:F',37);
		$ws->set_column('K:P',12);

		// FORMATOS
		$h      =& $wb->addformat(array( "bold" => 1, "size" => 16, "merge" => 1));
		$h1     =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'left'));
		$titulo =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 0, "fg_color" => 'silver' ));
		$cuerpo =& $wb->addformat(array( "size" => 9 ));
		$numero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));

		// COMIENZA A ESCRIBIR
		//$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$mes];
		$hs = "LIBRO DE VENTAS CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL $year";

		$ws->write(1, 0, $CI->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$CI->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<20; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};

		$mm=6;

		// TITULOS
		$ws->write_string( $mm, 0, "", $titulo );
		$ws->write_string( $mm, 1, "", $titulo );
		$ws->write_string( $mm, 2, "Factura", $titulo );
		$ws->write_string( $mm, 3, "Factura", $titulo );
		$ws->write_string( $mm, 4, "R.I.F. o", $titulo );
		$ws->write_string( $mm, 5, "Nombre del", $titulo );
		$ws->write_string( $mm, 6, "Numero de", $titulo );
		$ws->write_string( $mm, 7, "Numero de", $titulo );
		$ws->write_string( $mm, 8, "Tipo", $titulo );
		$ws->write_string( $mm, 9, "Documento", $titulo );
		$ws->write_string( $mm,10, "Ventas", $titulo );
		$ws->write_string( $mm,11, "Ventas", $titulo );
		$ws->write_string( $mm,12, "Base", $titulo );
		$ws->write_string( $mm,13, "", $titulo );
		$ws->write_string( $mm,14, "Monto de", $titulo );
		$ws->write_string( $mm,15, "I.V.A. ", $titulo );
		$ws->write_string( $mm,16, "Numero de ", $titulo );
		$ws->write_string( $mm,17, "Fecha del", $titulo );
		$ws->write_string( $mm,18, "Impuesto ", $titulo );
		$ws->write_string( $mm,19, "Numero", $titulo );
		$ws->write_string( $mm,20, "Contri-", $titulo );
		$mm++;
		$ws->write_string( $mm, 0, "Fecha", $titulo );
		$ws->write_string( $mm, 1, "Caja", $titulo );
		$ws->write_string( $mm, 2, "Inicial", $titulo );
		$ws->write_string( $mm, 3, "Final", $titulo );
		$ws->write_string( $mm, 4, "Cedula", $titulo );
		$ws->write_string( $mm, 5, "Contribuyente", $titulo );
		$ws->write_string( $mm, 6, "N.Credito", $titulo );
		$ws->write_string( $mm, 7, "N.Debito.", $titulo );
		$ws->write_string( $mm, 8, "Doc.", $titulo );
		$ws->write_string( $mm, 9, "Afectado", $titulo );
		$ws->write_string( $mm,10, "Totales", $titulo );
		$ws->write_string( $mm,11, "Exentas", $titulo );
		$ws->write_string( $mm,12, "Imponible", $titulo );
		$ws->write_string( $mm,13, "Alicuota %", $titulo );
		$ws->write_string( $mm,14, "I.V.A.", $titulo );
		$ws->write_string( $mm,15, "Retenido", $titulo );
		$ws->write_string( $mm,16, "Comprobante", $titulo );
		$ws->write_string( $mm,17, "Comprobante", $titulo );
		$ws->write_string( $mm,18, "Percibido", $titulo );
		$ws->write_string( $mm,19, "Importacion", $titulo );
		$ws->write_string( $mm,20, "buyente", $titulo );
		$mm++;
		$ii = $mm;
		$mtiva = 'X';
		$mfecha = '2000-00-00';
		$tventas = 0 ;
		$texenta = 0 ;
		$tbase   = 0 ;
		$timpue  = 0 ;
		$treiva  = 0 ;
		$tperci  = 0 ;
		$finicial = '99999999';
		$ffinal   = '00000000';
		$mforza = 0;
		$contri = 0;
		$caja = 'zytrdsefg';

		foreach( $export->result() as  $row ) {
			if ($caja == 'zytrdsefg') $caja=$row->caja;
			// chequea la fecha
			if ( $mfecha == $row->fecha ) {
				// Dentro del dia
				if ($caja == $row->caja) {
					if ( $row->tiva == 'SI' ) {
						$mforza = 1;
						$contri = 1;
					} else {
						if ( $row->tipo == 'NC' ) {
							$mforza = 1;
							$contri = 1;
						} else {
							$mforza = 0;
							$contri = 0;
							if ($finicial == '99999999') $finicial=$row->numero;
						}
					}
				} else {
					if ($finicial == '99999999') $finicial=$row->numero;
					$mforza = 1;
					if ( $row->tiva == 'SI' )
					$contri = 1;
					else
					$contri = 0;
				}
			}else {
				// Imprime todo
				if ($finicial == '99999999') $finicial=$row->numero;
				$mforza = 1;
				if ( $row->tiva == 'SI' )
				$contri = 1;
				else
				$contri = 0;
				if ( $row->tipo == 'NC' ) $contri = 1;
			}

			if ( ($finicial == '99999999' or empty($finicial)) and !empty($row->numero) ) {
			}

			if ( $mforza ) {
				// si tventas > 0 imprime totales
				if ( $tventas <> 0 ) {
					if ( $finicial == '99999999' ) $finicial = $ffinal;
					$fecha = substr($mfecha,8,2)."/".substr($mfecha,5,2)."/".substr($mfecha,0,4);
					$ws->write_string( $mm, 0, $fecha,  $cuerpo );		// Fecha
					$ws->write_string( $mm, 1, $caja,  $cuerpo );		// Fecha
					$ws->write_string( $mm, 2, $finicial, $cuerpo );       	// Factura Inicial
					$ws->write_string( $mm, 3, $ffinal, $cuerpo );         	// Factura Final
					$ws->write_string( $mm, 4, '  ******  ', $cuerpo );    	// RIF/CEDULA
					$ws->write_string( $mm, 5, 'VENTAS A NO CONTRIBUYENTES', $cuerpo );    // Nombre
					$ws->write_string( $mm, 6, '', $cuerpo );          		// Nro. N.C.
					$ws->write_string( $mm, 7, '', $cuerpo );    		// Nro. N.D.
					$ws->write_string( $mm, 8, 'RE', $cuerpo );    		// TIPO
					$ws->write_string( $mm, 9, '' , $cuerpo );    		// DOC. AFECTADO
					$ws->write_number( $mm,10, $tventas, $numero );    		// VENTAS + IVA
					$num = $mm + 1;
					$ws->write_formula( $mm,11,"=K$num - M$num - O$num" , $numero );   // EXENTO
					$ws->write_number( $mm,12,$timpue/0.14, $numero );   		// BASE IMPONIBLE
					$ws->write_number( $mm,13, 14, $numero );   		// ALICOUTA %
					$ws->write_number( $mm,14,$timpue , $numero );  // I.V.A."
					$ws->write_number( $mm,15, 0, $numero );         	// IVA RETENIDO
					$ws->write_string( $mm,16, '', $cuerpo );        	// NRO COMPROBANTE
					$ws->write_string( $mm,17, '', $cuerpo );        	// ECHA COMPROB
					$ws->write_number( $mm,18, $tperci, $numero );   	// IMPUESTO PERCIBIDO
					$ws->write_string( $mm,19, 0, $cuerpo );         	// IMPORTACION
					$ws->write_string( $mm,20, 'NO', $cuerpo );      	// CONTRIBUYENTE
					$tventas = 0 ;
					$texenta = 0 ;
					$tbase   = 0 ;
					$timpue  = 0 ;
					$treiva  = 0 ;
					$tperci  = 0 ;
					if ( $row->tipo_doc == 'FC' )
						$finicial = $row->numero;
					else
						$finicial = '99999999';
					$mm++;
					$caja = $row->caja;
				}
			}
			if ($contri) {
				// imprime contribuyente
				$fecha = $row->fecha;
				$fecha = substr($fecha,8,2)."/".$ameses[substr($fecha,5,2)-1]."/".substr($fecha,0,4);
				$ws->write_string( $mm, 0, $fecha,           $cuerpo );        // Fecha
				$ws->write_string( $mm, 1, $row->caja,       $cuerpo );     // Caja
				$ws->write_string( $mm, 2, $row->numero,     $cuerpo );   // Factura Inicial
				$ws->write_string( $mm, 3, '',               $cuerpo );             // Factura Final
				$ws->write_string( $mm, 4, $row->rif,        $cuerpo );   // RIF/CEDULA
				$ws->write_string( $mm, 5, $row->nombre,     $cuerpo );   // Nombre
				$ws->write_string( $mm, 6, $row->numnc,      $cuerpo );   // Nro. N.C.
				$ws->write_string( $mm, 7, $row->numnd,      $cuerpo );   // Nro. N.D.
				$ws->write_string( $mm, 8, $row->tipo_doc,   $cuerpo );   // TIPO
				$ws->write_string( $mm, 9, $row->afecta,     $cuerpo );   // DOC. AFECTADO
				$ws->write_number( $mm,10, $row->ventatotal, $numero );   // VENTAS + IVA
				$ws->write_number( $mm,11, $row->exento,     $numero );   // VENTAS EXENTAS
				$ws->write_number( $mm,12, $row->base,       $numero );   // BASE IMPONIBLE
				$ws->write_number( $mm,13, $row->alicuota,   $numero );   // ALICOUTA %
				$num = $mm+1;
				$ws->write_formula( $mm,14,"=M$num*N$num/100" , $numero );   //I.V.A.
				$ws->write_number( $mm,15, $row->reiva,         $numero );   // IVA RETENIDO
				$ws->write_string( $mm,16, $row->comprobante,   $cuerpo );   // NRO COMPROBANTE
				$ws->write_string( $mm,17, $row->fecharece,     $cuerpo );   // FECHA COMPROB
				$ws->write_number( $mm,18, $row->impercibido,   $numero );   // IMPUESTO PERCIBIDO
				$ws->write_string( $mm,19, $row->importacion,   $cuerpo );   // IMPORTACION
				$ws->write_string( $mm,20, $row->tiva,          $cuerpo );   // CONTRIBUYENTE
				$finicial = '99999999';
				$mm++;
			} else {
				// Totaliza
				$tventas += $row->ventatotal ;
				$texenta += $row->exento ;
				$tbase   += $row->base ;
				$timpue  += $row->cgimpu ;
				$treiva  += $row->reiva ;
				$tperci  += $row->impercibido ;
				if ( $finicial == '99999999' ) $finicial=$row->numero;
				if ( substr($row->final,0,2)!='NC' ) $ffinal=$row->final;
			};

			$mfecha = $row->fecha;
			$caja = $row->caja;
		}

		//Imprime el Ultimo
		if ( $tventas <> 0 ) {
		$fecha = substr($mfecha,8,2)."/".$ameses[substr($mfecha,5,2)-1]."/".substr($mfecha,0,4);
		$ws->write_string( $mm, 0, $fecha,  $cuerpo );         			// Fecha
		$ws->write_string( $mm, 1, $caja,  $cuerpo );         			// Caja
		$ws->write_string( $mm, 2, $finicial, $cuerpo );				//"Factura Inicial"
		$ws->write_string( $mm, 3, $ffinal, $cuerpo );				//"Factura Final"
		$ws->write_string( $mm, 4, '  ******  ', $cuerpo );				//"RIF/CEDULA"
		$ws->write_string( $mm, 5, 'VENTAS A NO CONTRIBUYENTES', $cuerpo );		//"Nombre"
		$ws->write_string( $mm, 6, '', $cuerpo );					//"Nro. N.C."
		$ws->write_string( $mm, 7, '', $cuerpo );					//"Nro. N.D."
		$ws->write_string( $mm, 8, 'RE', $cuerpo );					//"TIPO"
		$ws->write_string( $mm, 9, '' , $cuerpo );					//"DOC. AFECTADO"
		$ws->write_number( $mm,10, $tventas, $numero );				//"VENTAS + IVA"
		//    $ws->write_number( $mm,11, $texenta, $numero );				//"VENTAS EXENTAS"
		$num = $mm+1;

		$ws->write_formula( $mm,11,"=K$num - M$num - O$num" , $numero );   // EXENTO
		//    $ws->write_number( $mm,12, $tbase, $numero );				//"BASE IMPONIBLE"
		$ws->write_number( $mm,12, $timpue/0.14, $numero );   		// BASE IMPONIBLE
		$ws->write_number( $mm,13, 14, $numero );					//"ALICOUTA %"

		//    $ws->write_formula( $mm,14,"=L$num*M$num/100" , $numero );			//"I.V.A."
		$ws->write_number( $mm,14,$timpue , $numero );  // I.V.A."

		//    $ws->write_number( $mm,14, $timpue, $numero );   //"I.V.A."
		$ws->write_number( $mm,15, 0, $numero );   //"IVA RETENIDO"
		$ws->write_string( $mm,16, '', $cuerpo );   //"NRO COMPROBANTE"
		$ws->write_string( $mm,17, '', $cuerpo );   //"FECHA COMPROB"
		$ws->write_number( $mm,18, $tperci, $numero );   //"IMPUESTO PERCIBIDO"
		$ws->write_string( $mm,19, 0, $cuerpo );   //"IMPORTACION"
		$ws->write_string( $mm,20, 'NO', $cuerpo );   //"CONTRIBUYENTE"
		$mm++;
		}

		$celda = $mm+1;
		$fventas = "=K$celda";   // VENTAS
		$fexenta = "=L$celda";   // VENTAS EXENTAS
		$fbase   = "=M$celda";   // BASE IMPONIBLE
		$fiva    = "=O$celda";   // I.V.A.

		$ws->write( $mm, 0,"Totales...",  $Tnumero );
		$ws->write_blank( $mm, 1,  $Tnumero );
		$ws->write_blank( $mm, 2,  $Tnumero );
		$ws->write_blank( $mm, 3,  $Tnumero );
		$ws->write_blank( $mm, 4,  $Tnumero );
		$ws->write_blank( $mm, 5,  $Tnumero );
		$ws->write_blank( $mm, 6,  $Tnumero );
		$ws->write_blank( $mm, 7,  $Tnumero );
		$ws->write_blank( $mm, 8,  $Tnumero );
		$ws->write_blank( $mm, 9,  $Tnumero );

		$ws->write_formula( $mm,10, "=SUM(K$ii:K$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,11, "=SUM(L$ii:L$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"BASE IMPONIBLE"
		//$ws->write_formula( $mm,13, "=SUM(M7:M$mm)", $Tnumero );   //"ALICOUTA %"
		$ws->write_blank( $mm, 13,  $Tnumero );
		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"I.V.A."
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"IVA RETENIDO"

		$ws->write_blank( $mm, 16,  $Tnumero );
		$ws->write_blank( $mm, 17,  $Tnumero );
		$ws->write_formula( $mm,18, "=SUM(S$ii:S$mm)", $Tnumero );   //"IMPUESTO PERCIBIDO"

		$ws->write_blank( $mm, 19,  $Tnumero );
		$ws->write_blank( $mm, 20,  $Tnumero );
		$mm ++;
		$mm ++;
		$ws->write($mm, 3, 'RESUMEN:', $h1 );
		$ws->write($mm, 5, 'Ventas Internas no Gravadas:', $h1 );
		$ws->write_formula($mm, 10, "=L$celda" , $Rnumero );

		$mm ++;
		$ws->write($mm, 5, 'Ventas de Exportacion:', $h1 );
		$ws->write_formula($mm, 10, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 12, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 14, "=0+0" , $Rnumero );

		$mm ++;
		$ws->write($mm,5, 'Ventas Internas Alicuota General:', $h1 );
		$ws->write_formula($mm, 10, "=M$celda+O$celda" , $Rnumero );
		$ws->write_formula($mm, 12, "=M$celda" , $Rnumero );
		$ws->write_formula($mm, 14, "=O$celda" , $Rnumero );
		$mm ++;

		$ws->write($mm,5, 'Ventas Internas Alicuota General + Adicional:', $h1 );
		$ws->write_formula($mm, 10, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 12, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 14, "=0+0" , $Rnumero );
		$mm ++;

		$ws->write($mm,5, 'Ventas Internas Alicuota Reducida:', $h1 );
		$ws->write_formula($mm, 10, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 12, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 14, "=0+0" , $Rnumero );
		$mm ++;

		$wb->close();
		header("Content-type: application/x-msexcel; name=\"lventas.xls\"");
		header("Content-Disposition: inline; filename=\"lventas.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
	}

	//libro de ventas con punto de ventas
	static function wlvexcelpdv($mes,$modalidad='M') {
		$CI =& get_instance();
		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));

		if($modalidad=='Q1'){
			$fdesde=$mes.'01';
			$fhasta=$mes.'15';
		}elseif($modalidad=='Q2'){
			$fdesde=$mes.'16';
			$fhasta=$mes.$udia;
		}else{
			$fdesde=$mes.'01';
			$fhasta=$mes.$udia;
		}

		$tasas = $CI->_tasas($mes);
		$mivag = $tasas['general'];
		$mivar = $tasas['reducida'];
		$mivaa = $tasas['adicional'];

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$CI->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$CI->db->simple_query($mSQL);
		$mSQL = "UPDATE club SET cedula=CONCAT('V',cedula) WHERE MID(cedula,1,1) IN ('0','1','2','3','4','5','6','7','8','9')  ";
		$CI->db->simple_query($mSQL);

		// TRATA DE PONER FACTURA AFECTADA DESDE FMAY
		$mSQL = "UPDATE siva a JOIN fmay b ON a.numero=b.numero AND a.fuente='FA' AND b.tipo='D' AND a.fecha=b.fecha SET a.referen=presup WHERE  EXTRACT(YEAR_MONTH FROM a.fechal)=$mes  ";
		$CI->db->simple_query($mSQL);

		// PONE NRO FISCAL EN SIVA
		//$mSQL = "UPDATE siva a JOIN fmay b ON a.numero=b.numero AND a.fuente='FA' AND b.tipo='D' AND a.fecha=b.fecha SET a.referen=presup WHERE  EXTRACT(YEAR_MONTH FROM a.fechal)=$mes  ";
		//$CI->db->simple_query($mSQL);

		// PARA HACERLO MENSUAL
		$SQL[] ="SELECT
		 a.fecha  fecha,
		 a.numero numero,
		 a.numero final,
		 c.cedula rif,
		 CONCAT(c.nombres,' ', c.apellidos) AS nombre,
		 ' ' AS numnc,
		 ' ' AS numnd,
		 IF(MID(a.numero,1,2)='NC','NC','FC') AS tipo_doc,
		 '        ' AS afecta,
		 SUM(a.monto) ventatotal,
		 SUM(a.monto*(a.impuesto=0)) exento,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivag.")*100/(100+a.impuesto)),2) baseg,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivar.")*100/(100+a.impuesto)),2) baser,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivaa.")*100/(100+a.impuesto)),2) basea,
		 a.impuesto AS alicuota,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivag.") - a.monto*(a.impuesto=".$mivag.")*100/(100+a.impuesto)),2) AS impug,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivar.") - a.monto*(a.impuesto=".$mivar.")*100/(100+a.impuesto)),2) AS impur,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivaa.") - a.monto*(a.impuesto=".$mivaa.")*100/(100+a.impuesto)),2) AS impua,
		 0 AS reiva,
		 ' ' comprobante,
		 ' ' fechacomp,
		 ' ' impercibido,
		 ' ' importacion,
		 IF(c.cedula IS NOT NULL,IF(MID(c.cedula,1,1) IN ('V','E'), IF(CHAR_LENGTH(MID(c.cedula,2,10))=9,'SI','NO'), 'SI' ), 'NO') tiva,
		 b.tipo,
		 a.numero numa,
		 a.caja, d.nfiscal
		 FROM vieite a
		 LEFT JOIN viefac b ON a.numero=b.numero and a.caja=b.caja
		 LEFT JOIN club c ON b.cliente=c.cod_tar
		 LEFT JOIN dine d ON a.fecha=d.fecha AND a.caja=d.caja AND a.cajero=d.cajero
		 WHERE a.fecha >=$fdesde AND a.fecha<=$fhasta
		 GROUP BY a.fecha, a.caja, numa";

		$SQL[]="SELECT
		 a.fecha AS fecha,
		 a.numero NUMERO,
		 '        ' AS FINAL,
		 a.rif AS RIF,
		 IF( MID(a.rif,1,1) NOT IN ('J','G') AND b.contacto IS NOT NULL AND b.contacto!='',b.contacto,a.nombre) AS NOMBRE,
		 '        ' NUMNC,
		 '        ' NUMND,
		 a.tipo AS TIPO_DOC,
		 IF(a.referen=a.numero,'        ',a.referen) AS afecta,
		 a.gtotal*IF(a.tipo='NC',-1,1)  ventatotal,
		 a.exento*IF(a.tipo='NC',-1,1)  exento,
		 a.general*IF(a.tipo='NC',-1,1) baseg,
		 a.reducida*IF(a.tipo='NC',-1,1) baser,
		 a.adicional*IF(a.tipo='NC',-1,1) basea,
		 '$mivag%' AS ALICUOTA,
		 a.geneimpu*IF(a.tipo='NC',-1,1) AS impug,
		 a.reduimpu*IF(a.tipo='NC',-1,1) AS impur,
		 a.adicimpu*IF(a.tipo='NC',-1,1) AS impua,
		 a.reiva*IF(a.tipo='NC',-1,1),
		 '            ' comprobante,
		 '            ' fechacomp,
		 '            ' impercibido,
		 '            ' importacion,
		 'SI' tiva,
		 a.tipo,
		 a.numero numa,
		 'MAYO' caja, nfiscal
		 FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
		 WHERE a.fechal = $fdesde AND a.fecha between $fdesde  AND $fhasta  AND a.libro='V' AND a.fuente<>'PV' AND a.tipo<>'RI'";

		//RETENCIONES
		$SQL[]="SELECT
		a.emiriva,
		'    ',
		'    ',
		b.rifci,
		b.nombre,
		'    ',
		'    ',
		'RI',
		a.numero,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		a.reteiva,
		a.nroriva,
		a.recriva frecep ,
		'    ',
		'    ',
		'SI',
		'RI',
		'numa',
		'MAYO',
		'    '
		FROM itccli as a JOIN scli as b on a.cod_cli=b.cliente LEFT JOIN fmay c ON a.numero=c.numero AND c.tipo='C'
		WHERE a.recriva BETWEEN $fdesde AND $fhasta";

		$SQL[]="SELECT
		 a.fecha,
		 '    ',
		 '    ',
		 c.cedula,
		 CONCAT(c.nombres,' ',c.apellidos),
		 '    ',
		 '    ',
		 'RI',
		 a.numero,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 a.monto,
		 CONCAT('20',a.num_ref),
		 a.f_factura ,
		 '    ',
		 '    ',
		 'SI',
		 'RI',
		 'numa',
		 'MAYO',
		 '    '
		 FROM viepag a JOIN viefac b ON a.numero=b.numero AND a.f_factura=b.fecha
		 LEFT JOIN club c ON b.cliente=c.cod_tar
		 WHERE a.tipo='RI' AND a.f_factura BETWEEN $fdesde AND $fhasta";

		//fin de las retenciones

		$SQL[]= "SELECT
		 a.fecha   fecha,
		 a.numero  NUMERO,
		 '        ' AS FINAL,
		 '  ---***---' AS RIF,
		 'FACTURA ANULADA***FACTURA ANULADA' NOMBRE,
		 '        ' NUMNC,
		 '        ' NUMND,
		 'FC' TIPO_DOC,
		 '        ' AFECTA,
		 0 VENTATOTAL,
		 0 EXENTO,
		 0 baseg,
		 0 baser,
		 0 basea,
		 '$mivag%' AS ALICUOTA,
		 0 impug,
		 0 impur,
		 0 impua,
		 0 reiva,
		 '   ' COMPROBANTE,
		 '   ' FECHACOMP,
		 '   ' IMPERCIBIDO,
		 '   ' IMPORTACION,
		 'SI' tiva,
		 'FC',
		 a.numero numa,
		 'MAYO' caja, nfiscal
		 FROM fmay a
		 WHERE a.fecha>=$fdesde AND a.fecha<=$fhasta AND a.tipo='A'
		ORDER BY fecha, caja, numa ";
		$mSQL=implode(" UNION ALL ",$SQL);
		//memowrite($mSQL);

		$export = $CI->db->query($mSQL);

		$fname = tempnam("/tmp","lventas.xls");
		$CI->load->library("Workbookbig",array("fname" => $fname));
		$wb =& $CI->workbookbig;
		$ws =& $wb->addworksheet($mes);


		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:A',6);
		$ws->set_column('B:B',8);
		$ws->set_column('C:C',40);
		$ws->set_column('D:E',10);
		$ws->set_column('F:F',6);
		$ws->set_column('G:K',11);
		$ws->set_column('L:U',16);

		// FORMATOS
		$h      =& $wb->addformat(array( "bold" => 1, "size" => 16, "merge" => 1));
		$h1     =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'left'));
		$titulo =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 0, "fg_color" => 'silver' ));
		$cuerpo =& $wb->addformat(array( "size" => 9 ));
		$numero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));
		$tm =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 'silver' ));

		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = "LIBRO DE VENTAS CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL ".substr($mes,0,4);

		$ws->write(1, 0, $CI->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$CI->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<20; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};
		$mm=6;

		// TITULOS
		$ws->write_string( $mm, 0, "", $titulo );
		$ws->write_string( $mm, 1, "", $titulo );
		$ws->write_string( $mm, 2, "", $titulo );
		$ws->write_string( $mm, 3, "", $titulo );
		$ws->write_string( $mm, 4, "", $titulo );
		$ws->write_string( $mm, 5, "Tipo", $titulo );
		$ws->write_string( $mm, 6, "Numero de", $titulo );
		$ws->write_string( $mm, 7, "Numero de", $titulo );
		$ws->write_string( $mm, 8, "Control", $titulo );
		$ws->write_string( $mm, 9, "Control", $titulo );
		$ws->write_string( $mm,10, "Numero de", $titulo );
		$ws->write_string( $mm,11, "Ventas", $titulo );
		$ws->write_string( $mm,12, "Total Ventas", $titulo );
		$ws->write_string( $mm,13, "Ventas Exentas", $titulo );
		$ws->write_string( $mm,14, "Valor FOB", $titulo );
		$ws->write_string( $mm,15, "VENTAS GRAVADAS", $tm );
		$ws->write_blank( $mm,16,  $tm );
		$ws->write_blank( $mm,17,  $tm );
		$ws->write_blank( $mm,18,  $tm );
		$ws->write_blank( $mm,19,  $tm );
		$ws->write_blank( $mm,20,  $tm );
		$ws->write_string( $mm,21, "IVA", $titulo );
		$ws->write_string( $mm,22, "Numero", $titulo );
		$ws->write_string( $mm,23, "Fecha", $titulo );
		$ws->write_string( $mm,24, "", $titulo );

		$mm++;
		$ws->write_string( $mm, 0, "Num.", $titulo );
		$ws->write_string( $mm, 1, "Fecha", $titulo );
		$ws->write_string( $mm, 2, "Nombre, Razon Social o Denominacion ", $titulo );
		$ws->write_string( $mm, 3, "RIF o", $titulo );
		$ws->write_string( $mm, 4, "Numero", $titulo );
		$ws->write_string( $mm, 5, "de", $titulo );
		$ws->write_string( $mm, 6, "Documento", $titulo );
		$ws->write_string( $mm, 7, "Documento.", $titulo );
		$ws->write_string( $mm, 8, "Fiscal", $titulo );
		$ws->write_string( $mm, 9, "Fiscal", $titulo );
		$ws->write_string( $mm,10, "Documento", $titulo );
		$ws->write_string( $mm,11, "A", $titulo );
		$ws->write_string( $mm,12, "Incluyendo", $titulo );
		$ws->write_string( $mm,13, "o no sujetas", $titulo );
		$ws->write_string( $mm,14, "Operacion", $titulo );

		$ws->write_string( $mm,15, "Alicuota General $mivag", $tm );
		$ws->write_blank( $mm,16,  $tm );
		$ws->write_string( $mm,17, "Alicuota Adicional $mivaa", $tm );
		$ws->write_blank( $mm,18,  $tm );
		$ws->write_string( $mm,19, "Alicuota Reducida $mivar", $tm );
		$ws->write_blank( $mm,20,  $tm );
		$ws->write_string( $mm,21, "Retenido", $titulo );
		$ws->write_string( $mm,22, "de", $titulo );
		$ws->write_string( $mm,23, "de", $titulo );
		$ws->write_string( $mm,24, "I.V.A.", $titulo );
		$mm++;
		$ws->write_string( $mm, 0, "Oper.", $titulo );
		$ws->write_string( $mm, 1, "", $titulo );
		$ws->write_string( $mm, 2, "del Comprador", $titulo );
		$ws->write_string( $mm, 3, "Cedula", $titulo );
		$ws->write_string( $mm, 4, "Caja", $titulo );
		$ws->write_string( $mm, 5, "Doc.", $titulo );
		$ws->write_string( $mm, 6, "Inicial", $titulo );
		$ws->write_string( $mm, 7, "Final", $titulo );
		$ws->write_string( $mm, 8, "Inicial", $titulo );
		$ws->write_string( $mm, 9, "Final", $titulo );
		$ws->write_string( $mm,10, "Afectado", $titulo );
		$ws->write_string( $mm,11, "Contrib.", $titulo );
		$ws->write_string( $mm,12, "el I.V.A.", $titulo );
		$ws->write_string( $mm,13, "a Impuesto", $titulo );
		$ws->write_string( $mm,14, "Exportacion", $titulo );

		$ws->write_string( $mm,15, "Base", $titulo );
		$ws->write_string( $mm,16, "Impuesto", $titulo );
		$ws->write_string( $mm,17, "Base", $titulo );
		$ws->write_string( $mm,18, "Impuesto", $titulo );
		$ws->write_string( $mm,19, "Base", $titulo );
		$ws->write_string( $mm,20, "Impuesto", $titulo );
		$ws->write_string( $mm,21, "Comprador", $titulo );
		$ws->write_string( $mm,22, "Comprobante", $titulo );
		$ws->write_string( $mm,23, "Emision", $titulo );
		$ws->write_string( $mm,24, "Percibido", $titulo );

		$mm++;
		$ii = $mm;
		$mtiva = 'X';
		$mfecha = '2000-00-00';
		$tventas = $texenta = $tbaseg  = $tbaser  = $tbasea  = $timpug  = $timpur  = $timpua  = $treiva  = $tperci  = $mforza = $contri =0 ;
		$finicial = '99999999';
		$ffinal   = '00000000';
		$fiscali  = $fiscalf  = '';
		$caja = 'zytrdsefg';

		foreach( $export->result() as  $row ) {
			if ( empty($nfiscali) ) $nfiscali = $row->nfiscal;
			if ($caja == 'zytrdsefg') $caja=$row->caja;
			// chequea la fecha
			if ( $mfecha == $row->fecha ) {
				// Dentro del dia
				if($caja == $row->caja) {
					if($row->tiva == 'SI') {
						$mforza = $contri = 1;
					} else {
						if ( $row->tipo == 'NC' ) {
							$mforza = $contri = 1;
						} else {
							$mforza = $contri = 0;
							if ($finicial == '99999999') $finicial=$row->numero;
						};
					};
				}else {
					if ($finicial == '99999999') $finicial=$row->numero;
					$mforza = 1;
					if ( $row->tiva == 'SI' ) {
						$contri = 1;
					} else {
						$contri = 0;
					}
				};
			} else {
				// Imprime todo
				if ($finicial == '99999999') $finicial=$row->numero;
				$mforza = 1;
				if ( $row->tiva == 'SI' ) {
					$contri = 1;
				} else {
					$contri = 0;
				};
				if ( $row->tipo == 'NC' ) {
					$contri = 1;
				};
			};

			if ( $mforza ) {
				// si tventas > 0 imprime totales
				if ( $tventas <> 0 ) {
					if ( $finicial == '99999999' ) $finicial = $ffinal;
					$ws->write_string( $mm,  0, $mm-8, $cuerpo );
					$fecha = substr($mfecha,8,2)."-".$ameses[substr($mfecha,5,2)-1]."-".substr($mfecha,0,4);
					$ws->write_string( $mm, 1, $fecha,  $cuerpo );		// Fecha
					$ws->write_string( $mm, 2, 'VENTAS A NO CONTRIBUYENTES', $cuerpo );    // Nombre
					$ws->write_string( $mm, 3, '  ******  ', $cuerpo );   // RIF/CEDULA
					$ws->write_string( $mm, 4, $caja,  $cuerpo );		      // Fecha
					$ws->write_string( $mm, 5, 'FC', $cuerpo );    		    // TIPO

					$ws->write_string( $mm, 6, $finicial, $cuerpo );      // Factura Inicial
					$ws->write_string( $mm, 7, $ffinal,   $cuerpo );      // Factura Final
					$ws->write_string( $mm, 8, $nfiscali, $cuerpo );      // Nro. N.C.
					$ws->write_string( $mm, 9, $nfiscali, $cuerpo );    	// Nro. N.D.

					$ws->write_string( $mm,10, $row->afecta, $cuerpo );   // DOC. AFECTADO
					$ws->write_string( $mm,11, 'NO',      $cuerpo );      // CONTRIBUYENTE
					$ws->write_number( $mm,12, $tventas,  $numero );      // VENTAS + IVA
					$ws->write_number( $mm,13, $texenta,  $numero );      // EXENTAS
					$ws->write_number( $mm,14, 0,         $numero );      // FOB

					$ws->write_number( $mm,15, $tbaseg, $numero );       // IVA RETENIDO
					$ws->write_number( $mm,16, $timpug, $numero );       // NRO COMPROBANTE
					$ws->write_number( $mm,17, $tbasea, $numero );       // ECHA COMPROB
					$ws->write_number( $mm,18, $timpua, $numero );   	   // IMPUESTO PERCIBIDO
					$ws->write_number( $mm,19, $tbaser, $numero );       // IMPORTACION
					$ws->write_number( $mm,20, $timpur, $numero );       // IMPORTACION

					$ws->write_number( $mm,21, $treiva,     $numero );   // IVA RETENIDO
					$ws->write_string( $mm,22, '',		$cuerpo );         // NRO COMPROBANTE
					$ws->write_string( $mm,23, '',		$cuerpo );         // FECHA COMPROB
					$ws->write_number( $mm,24, $tperci,	$numero );       // IMPUESTO PERCIBIDO

					$tventas = $texenta = $tbaseg  = $tbaser  = $tbasea  = $timpug  = $timpur  = $timpua  = $treiva  = $tperci  = 0;
					if ( $row->tipo_doc == 'FC' ) {
						$finicial = $row->numero;
					} else {
						$finicial = '99999999';
					};
					$mm++;
					$nfiscali = '';
					$caja = $row->caja;
				};
			};
			if ( $contri ) {
				// imprime contribuyente
				$fecha = $row->fecha;
				$ws->write_string( $mm,  0, $mm-8, $cuerpo );
				$fecha = substr($fecha,8,2)."-".$ameses[substr($fecha,5,2)-1]."-".substr($fecha,0,4);
				$ws->write_string( $mm, 1, $fecha,           $cuerpo );        // Fecha
				$ws->write_string( $mm, 2, $row->nombre,     $cuerpo );   // Nombre
				$ws->write_string( $mm, 3, $row->rif,        $cuerpo );   // RIF/CEDULA
				$ws->write_string( $mm, 4, $row->caja,       $cuerpo );   // Caja
				$ws->write_string( $mm, 5, $row->tipo_doc,   $cuerpo );   // Tipo_doc
				$ws->write_string( $mm, 6, $row->numero,     $cuerpo );   // Factura Inicial
				$ws->write_string( $mm, 7, '',               $cuerpo );   // Factura Final
				$ws->write_string( $mm, 8, substr($row->nfiscal,5,8),    $cuerpo );   // Fiscal Inicial
				$ws->write_string( $mm, 9, '',               $cuerpo );   // Fiscal Final
				$ws->write_string( $mm,10, $row->afecta,     $cuerpo );   // DOC. AFECTADO
				$ws->write_string( $mm,11, $row->tiva,       $cuerpo );   // CONTRIBUYENTE
				$ws->write_number( $mm,12, $row->ventatotal, $numero );   // VENTAS + IVA
				$ws->write_number( $mm,13, $row->exento,     $numero );   // VENTAS EXENTAS
				$ws->write_number( $mm,14, 0,                $numero );   // EXPORTACION FOB
				$ws->write_number( $mm,15, $row->baseg,      $numero );   // Base G
				$ws->write_number( $mm,16, $row->impug,      $numero );   // IVA G
				$ws->write_number( $mm,17, $row->basea,      $numero );   // Base A
				$ws->write_number( $mm,18, $row->impua,      $numero );   // IVA A
				$ws->write_number( $mm,19, $row->baser,      $numero );   // BASE R
				$ws->write_number( $mm,20, $row->impur,      $numero );   // IVA R
				$num = $mm+1;
				//$ws->write_formula( $mm,14,"=M$num*N$num/100" , $numero );   //I.V.A.
				$ws->write_number( $mm,21, $row->reiva,         $numero );   // IVA RETENIDO
				$ws->write_string( $mm,22, $row->comprobante,   $cuerpo );   // NRO COMPROBANTE
				$ws->write_string( $mm,23, $row->fechacomp,     $cuerpo );   // FECHA COMPROB
				$ws->write_number( $mm,24, $row->impercibido,   $numero );   // IMPUESTO PERCIBIDO
				//$ws->write_string( $mm,19, $row->importacion,   $cuerpo );   // IMPORTACION
				$finicial = '99999999';
				$mm++;
			} else {
				// Totaliza
				$tventas += $row->ventatotal ;
				$texenta += $row->exento ;
				$tbaseg  += $row->baseg  ;
				$tbaser  += $row->baser  ;
				$tbasea  += $row->basea  ;
				$timpug  += $row->impug ;
				$timpur  += $row->impur ;
				$timpua  += $row->impua ;
				$treiva  += $row->reiva  ;
				$tperci  += $row->impercibido ;
				if ( $finicial == '99999999' ) $finicial=$row->numero;
				if ( substr($row->final,0,2)!='NC')	$ffinal=$row->final;
			};
			$mfecha = $row->fecha;
			$caja = $row->caja;
			$nfiscali = $row->nfiscal;
		}
		//Imprime el Ultimo

		if ( $tventas <> 0 ) {
			if ( $finicial == '99999999' ) $finicial = $ffinal;
			$ws->write_string( $mm,  0, $mm-8, $cuerpo );
			$fecha = substr($mfecha,8,2)."-".$ameses[substr($mfecha,5,2)-1]."-".substr($mfecha,0,4);
			$ws->write_string( $mm, 1, $fecha,  $cuerpo );		// Fecha
			$ws->write_string( $mm, 2, 'VENTAS A NO CONTRIBUYENTES', $cuerpo );    // Nombre
			$ws->write_string( $mm, 3, '  ******  ', $cuerpo );    	// RIF/CEDULA
			$ws->write_string( $mm, 4, $caja,  $cuerpo );		  // Fecha
			$ws->write_string( $mm, 5, 'FC', $cuerpo );    		// TIPO

			$ws->write_string( $mm, 6, $finicial, $cuerpo );      // Factura Inicial
			$ws->write_string( $mm, 7, $ffinal,   $cuerpo );      // Factura Final
			$ws->write_string( $mm, 8, '',        $cuerpo );      // Nro. N.C.
			$ws->write_string( $mm, 9, '',        $cuerpo );    	// Nro. N.D.

			$ws->write_string( $mm,10, $row->afecta, $cuerpo );   // DOC. AFECTADO
			$ws->write_string( $mm,11, 'NO',      $cuerpo );      // CONTRIBUYENTE
			$ws->write_number( $mm,12, $tventas,  $numero );    	// VENTAS + IVA
			$ws->write_number( $mm,13, $texenta, $numero );    	  // EXENTAS
			$ws->write_number( $mm,14, 0,         $numero );    	// FOB

			$ws->write_number( $mm,15, $tbaseg, $numero );    // IVA RETENIDO
			$ws->write_number( $mm,16, $timpug, $numero );    // NRO COMPROBANTE
			$ws->write_number( $mm,17, $tbasea, $numero );    // ECHA COMPROB
			$ws->write_number( $mm,18, $timpua, $numero );   	// IMPUESTO PERCIBIDO
			$ws->write_number( $mm,19, $tbaser, $numero );    // IMPORTACION
			$ws->write_number( $mm,20, $timpur, $numero );    // IMPORTACION

			$ws->write_number( $mm,21, $treiva, $numero );   // IVA RETENIDO
			$ws->write_string( $mm,22, '',	$cuerpo );       // NRO COMPROBANTE
			$ws->write_string( $mm,23, '',	$cuerpo );       // FECHA COMPROB
			$ws->write_number( $mm,24, $tperci,	$numero );   // IMPUESTO PERCIBIDO
		};

		$celda = $mm+1;
		$fventas = "=M$celda";   // VENTAS
		$fexenta = "=N$celda";   // VENTAS EXENTAS
		$fbase   = "=M$celda";   // BASE IMPONIBLE
		$fiva    = "=O$celda";   // I.V.A.

		$ws->write_string( $mm, 0,"Totales...",  $tm );
		$ws->write_blank( $mm, 1,  $tm );
		$ws->write_blank( $mm, 2,  $tm );
		$ws->write_blank( $mm, 3,  $tm );
		$ws->write_blank( $mm, 4,  $tm );
		$ws->write_blank( $mm, 5,  $tm );
		$ws->write_blank( $mm, 6,  $tm );
		$ws->write_blank( $mm, 7,  $tm );
		$ws->write_blank( $mm, 8,  $tm );
		$ws->write_blank( $mm, 9,  $tm );
		$ws->write_blank( $mm,10,  $tm );
		$ws->write_blank( $mm,11,  $tm );

		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,13, "=SUM(N$ii:N$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,16, "=SUM(Q$ii:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,17, "=SUM(R$ii:R$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,18, "=SUM(S$ii:S$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,19, "=SUM(T$ii:T$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,20, "=SUM(U$ii:U$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,21, "=SUM(V$ii:V$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_blank( $mm, 22,  $Tnumero );
		$ws->write_blank( $mm, 23,  $Tnumero );
		$ws->write_formula( $mm,24, "=SUM(Y$ii:Y$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$mm ++;
		$mm ++;
		$ws->write($mm, 4, 'RESUMEN DE VENTAS Y DEBITOS', $tm );
		$ws->write_blank( $mm, 5,  $tm );
		$ws->write_blank( $mm, 6,  $tm );
		$ws->write_blank( $mm, 7,  $tm );
		$ws->write_blank( $mm, 8,  $tm );
		$ws->write_blank( $mm, 9,  $tm );
		$ws->write_blank( $mm,10,  $tm );
		$ws->write_blank( $mm,11,  $tm );

		$ws->write_string($mm, 12, 'Base Imponible', $titulo );
		$ws->write_string($mm, 13, 'Debito Fiscal',  $titulo );

		//$ws->write_string($mm, 15, 'IVA Retenido', $titulo );
		//$ws->write_string($mm, 16, 'IVA Percibido',$titulo );

		$mm++;
		$ws->write($mm, 4, 'Total Ventas Internas no Gravadas:', $h1 );
		$ws->write_formula($mm, 12, "=N$celda" , $Rnumero );
		$mm ++;
		$ws->write($mm, 4, 'Total Ventas de Exportacion:', $h1 );
		$ws->write_formula($mm, 12, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 13, "=0+0" , $Rnumero );
		$mm ++;
		$ws->write($mm,4, 'Total Ventas Internas Gravadas Alicuota General:', $h1 );
		$ws->write_formula($mm, 12, "=P$celda" , $Rnumero );
		$ws->write_formula($mm, 13, "=Q$celda" , $Rnumero );
		$mm ++;
		$ws->write($mm,4, 'Total Ventas Internas Gravadas Alicuota General + Adicional:', $h1 );
		$ws->write_formula($mm, 12, "=R$celda" , $Rnumero );
		$ws->write_formula($mm, 13, "=S$celda" , $Rnumero );
		$mm ++;
		$ws->write($mm,4, 'Total Ventas InternasGravadas Alicuota Reducida:', $h1 );
		$ws->write_formula($mm, 12, "=T$celda" , $Rnumero );
		$ws->write_formula($mm, 13, "=U$celda" , $Rnumero );
		$mm ++;
		$wb->close();
		header("Content-type: application/x-msexcel; name=\"lventas.xls\"");
		header("Content-Disposition: inline; filename=\"lventas.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
		// print "$header\n$data";
	}

	//libro de ventas con punto de ventas fiscal
	static function wlvexcelpdvfiscal($mes,$modalidad='M') {
		$CI =& get_instance();
		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));

		if($modalidad=='Q1'){
			$fdesde=$mes.'01';
			$fhasta=$mes.'15';
		}elseif($modalidad=='Q2'){
			$fdesde=$mes.'16';
			$fhasta=$mes.$udia;
		}else{
			$fdesde=$mes.'01';
			$fhasta=$mes.$udia;
		}

		$tasas = $CI->_tasas($mes);
		$mivag = $tasas['general'];
		$mivar = $tasas['reducida'];
		$mivaa = $tasas['adicional'];

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$CI->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$CI->db->simple_query($mSQL);
		$mSQL = "UPDATE club SET cedula=CONCAT('V',cedula) WHERE MID(cedula,1,1) IN ('0','1','2','3','4','5','6','7','8','9')  ";
		$CI->db->simple_query($mSQL);

		// TRATA DE PONER FACTURA AFECTADA DESDE FMAY
		$mSQL = "UPDATE siva a JOIN fmay b ON a.numero=b.numero AND a.fuente='FA' AND b.tipo='D' AND a.fecha=b.fecha SET a.referen=presup WHERE  EXTRACT(YEAR_MONTH FROM a.fechal)=$mes  ";
		$CI->db->simple_query($mSQL);

		// PONE NRO FISCAL EN SIVA
		//$mSQL = "UPDATE siva a JOIN fmay b ON a.numero=b.numero AND a.fuente='FA' AND b.tipo='D' AND a.fecha=b.fecha SET a.referen=presup WHERE  EXTRACT(YEAR_MONTH FROM a.fechal)=$mes  ";
		//$CI->db->simple_query($mSQL);

		// PARA HACERLO MENSUAL
		$SQL[] ="SELECT
		 a.fecha  fecha,
		 a.numero numero,
		 a.numero final,
		 c.cedula rif,
		 CONCAT(c.nombres,' ', c.apellidos) AS nombre,
		 ' ' AS numnc,
		 ' ' AS numnd,
		 IF(MID(a.numero,1,2)='NC','NC','FC') AS tipo_doc,
		 '        ' AS afecta,
		 SUM(a.monto) ventatotal,
		 SUM(a.monto*(a.impuesto=0)) exento,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivag.")*100/(100+a.impuesto)),2) baseg,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivar.")*100/(100+a.impuesto)),2) baser,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivaa.")*100/(100+a.impuesto)),2) basea,
		 a.impuesto AS alicuota,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivag.") - a.monto*(a.impuesto=".$mivag.")*100/(100+a.impuesto)),2) AS impug,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivar.") - a.monto*(a.impuesto=".$mivar.")*100/(100+a.impuesto)),2) AS impur,
		 ROUND(SUM(a.monto*(a.impuesto=".$mivaa.") - a.monto*(a.impuesto=".$mivaa.")*100/(100+a.impuesto)),2) AS impua,
		 0 AS reiva,
		 ' ' comprobante,
		 ' ' fechacomp,
		 ' ' impercibido,
		 ' ' importacion,
		 IF(c.cedula REGEXP '^[VEJG][0-9]{9}$', 'SI' , 'NO') tiva,
		 b.tipo,
		 a.numero numa,
		 a.caja, d.nfiscal,
		 e.serial
		 FROM vieite a
		 LEFT JOIN viefac AS b ON a.numero=b.numero and a.caja=b.caja
		 LEFT JOIN fiscalz AS e ON a.caja=e.caja AND a.fecha=e.fecha
		 LEFT JOIN club c ON b.cliente=c.cod_tar
		 LEFT JOIN dine d ON a.fecha=d.fecha AND a.caja=d.caja AND a.cajero=d.cajero
		 WHERE a.fecha >=$fdesde AND a.fecha<=$fhasta AND c.cedula REGEXP '^[VEJG][0-9]{9}$'
		 GROUP BY a.fecha, a.caja, numa";
		//memowrite($mSQL[0]);

		//RETENCIONES
		$SQL[]="SELECT
		a.emiriva,
		'    ',
		'    ',
		b.rifci,
		b.nombre,
		'    ',
		'    ',
		'RI',
		a.numero,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		a.reteiva,
		a.nroriva,
		a.recriva frecep ,
		'    ',
		'    ',
		'SI',
		'RI',
		'numa',
		'MAYO',
		'    ',
		' ' AS serial
		FROM itccli as a JOIN scli as b on a.cod_cli=b.cliente LEFT JOIN fmay c ON a.numero=c.numero AND c.tipo='C'
		WHERE a.recriva BETWEEN $fdesde AND $fhasta";

		$SQL[]="SELECT
		 a.fecha,
		 '    ',
		 '    ',
		 c.cedula,
		 CONCAT(c.nombres,' ',c.apellidos),
		 '    ',
		 '    ',
		 'RI',
		 a.numero,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 a.monto,
		 CONCAT('20',a.num_ref),
		 a.f_factura ,
		 '    ',
		 '    ',
		 'SI',
		 'RI',
		 'numa',
		 'MAYO',
		 '    ',
		 ' ' AS serial
		 FROM viepag a JOIN viefac b ON a.numero=b.numero AND a.f_factura=b.fecha
		 LEFT JOIN club c ON b.cliente=c.cod_tar
		 WHERE a.tipo='RI' AND a.f_factura BETWEEN $fdesde AND $fhasta";
		//fin de las retenciones

		//VENTAS AL MAYOR
		$SQL[]="SELECT
		 a.fecha AS fecha,
		 a.numero NUMERO,
		 '        ' AS FINAL,
		 a.rif AS RIF,
		 IF( MID(a.rif,1,1) NOT IN ('J','G') AND b.contacto IS NOT NULL AND b.contacto!='',b.contacto,a.nombre) AS NOMBRE,
		 '        ' NUMNC,
		 '        ' NUMND,
		 a.tipo AS TIPO_DOC,
		 IF(a.referen=a.numero,'        ',a.referen) AS afecta,
		 a.gtotal*IF(a.tipo='NC',-1,1)  ventatotal,
		 a.exento*IF(a.tipo='NC',-1,1)  exento,
		 a.general*IF(a.tipo='NC',-1,1) baseg,
		 a.reducida*IF(a.tipo='NC',-1,1) baser,
		 a.adicional*IF(a.tipo='NC',-1,1) basea,
		 '$mivag%' AS ALICUOTA,
		 a.geneimpu*IF(a.tipo='NC',-1,1) AS impug,
		 a.reduimpu*IF(a.tipo='NC',-1,1) AS impur,
		 a.adicimpu*IF(a.tipo='NC',-1,1) AS impua,
		 a.reiva*IF(a.tipo='NC',-1,1),
		 '            ' comprobante,
		 '            ' fechacomp,
		 '            ' impercibido,
		 '            ' importacion,
		 'SI' tiva,
		 a.tipo,
		 a.numero numa,
		 'MAYO' caja, nfiscal,
		 ' ' AS serial
		 FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
		 WHERE a.fechal = $fdesde AND a.fecha between $fdesde  AND $fhasta  AND a.libro='V' AND a.fuente<>'PV' AND a.tipo<>'RI'";

		//FACTURAS ANULADAS
		$SQL[]= "SELECT
		 a.fecha   fecha,
		 a.numero  NUMERO,
		 '        ' AS FINAL,
		 '  ---***---' AS RIF,
		 'FACTURA ANULADA***FACTURA ANULADA' NOMBRE,
		 '        ' NUMNC,
		 '        ' NUMND,
		 'FC' TIPO_DOC,
		 '        ' AFECTA,
		 0 VENTATOTAL,
		 0 EXENTO,
		 0 baseg,
		 0 baser,
		 0 basea,
		 '$mivag%' AS ALICUOTA,
		 0 impug,
		 0 impur,
		 0 impua,
		 0 reiva,
		 '   ' COMPROBANTE,
		 '   ' FECHACOMP,
		 '   ' IMPERCIBIDO,
		 '   ' IMPORTACION,
		 'SI' tiva,
		 'FC',
		 a.numero numa,
		 'MAYO' caja, nfiscal,
		 'SERIAL' AS serial
		 FROM fmay a
		 WHERE a.fecha>=$fdesde AND a.fecha<=$fhasta AND a.tipo='A'
		ORDER BY fecha, caja, numa ";
		$mSQL=implode(" UNION ALL ",$SQL);
		//memowrite($mSQL);

		$export = $CI->db->query($mSQL);

		$acumulador=array(
			'FC'=>array('exento'=>0,
				'base'  =>0,
				'iva'   =>0,
				'base1' =>0,
				'iva1'  =>0,
				'base2' =>0,
				'iva2'  =>0),
			'NC'=>array('exento'=>0,
				'base'  =>0,
				'iva'   =>0,
				'base1' =>0,
				'iva1'  =>0,
				'base2' =>0,
				'iva2'  =>0),
				'NTO'=>0,
				'VTO'=>0);
		$fname = tempnam("/tmp","lventas.xls");
		$CI->load->library("workbook",array("fname" => $fname));
		$wb =& $CI->workbook;
		$ws =& $wb->addworksheet($mes);

		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:A',6);
		$ws->set_column('B:B',8);
		$ws->set_column('C:C',40);
		$ws->set_column('D:E',10);
		$ws->set_column('F:F',6 );
		$ws->set_column('G:K',11);
		$ws->set_column('L:U',16);
		$ws->set_column('Z:Z',12.5);

		// FORMATOS
		$h      =& $wb->addformat(array( "bold" => 1, "size" => 16, "merge" => 1));
		$h1     =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'left'));
		$titulo =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 0, "fg_color" => 'silver' ));
		$cuerpo =& $wb->addformat(array( "size" => 9 ));
		$numero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));
		$tm =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 'silver' ));

		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = "LIBRO DE VENTAS CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL ".substr($mes,0,4);

		$ws->write(1, 0, $CI->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$CI->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<20; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};
		$mm=6;

		// TITULOS
		$ws->write_string( $mm, 0, "", $titulo );
		$ws->write_string( $mm, 1, "", $titulo );
		$ws->write_string( $mm, 2, "", $titulo );
		$ws->write_string( $mm, 3, "", $titulo );
		$ws->write_string( $mm, 4, "", $titulo );
		$ws->write_string( $mm, 5, "Tipo", $titulo );
		$ws->write_string( $mm, 6, "Numero de", $titulo );
		$ws->write_string( $mm, 7, "Numero de", $titulo );
		$ws->write_string( $mm, 8, "Control", $titulo );
		$ws->write_string( $mm, 9, "Control", $titulo );
		$ws->write_string( $mm,10, "Numero de", $titulo );
		$ws->write_string( $mm,11, "Ventas", $titulo );
		$ws->write_string( $mm,12, "Total Ventas", $titulo );
		$ws->write_string( $mm,13, "Ventas Exentas", $titulo );
		$ws->write_string( $mm,14, "Valor FOB", $titulo );
		$ws->write_string( $mm,15, "VENTAS GRAVADAS", $tm );
		$ws->write_blank( $mm,16,  $tm );
		$ws->write_blank( $mm,17,  $tm );
		$ws->write_blank( $mm,18,  $tm );
		$ws->write_blank( $mm,19,  $tm );
		$ws->write_blank( $mm,20,  $tm );
		$ws->write_string( $mm,21, "IVA", $titulo );
		$ws->write_string( $mm,22, "Numero", $titulo );
		$ws->write_string( $mm,23, "Fecha", $titulo );
		$ws->write_string( $mm,24, "", $titulo );
		$ws->write_string( $mm,25, "Serial", $titulo );

		$mm++;
		$ws->write_string( $mm, 0, "Num.", $titulo );
		$ws->write_string( $mm, 1, "Fecha", $titulo );
		$ws->write_string( $mm, 2, "Nombre, Razon Social o Denominacion ", $titulo );
		$ws->write_string( $mm, 3, "RIF o", $titulo );
		$ws->write_string( $mm, 4, "Numero", $titulo );
		$ws->write_string( $mm, 5, "de", $titulo );
		$ws->write_string( $mm, 6, "Documento", $titulo );
		$ws->write_string( $mm, 7, "Documento.", $titulo );
		$ws->write_string( $mm, 8, "Fiscal", $titulo );
		$ws->write_string( $mm, 9, "Fiscal", $titulo );
		$ws->write_string( $mm,10, "Documento", $titulo );
		$ws->write_string( $mm,11, "A", $titulo );
		$ws->write_string( $mm,12, "Incluyendo", $titulo );
		$ws->write_string( $mm,13, "o no sujetas", $titulo );
		$ws->write_string( $mm,14, "Operacion", $titulo );
		$ws->write_string( $mm,15, "Alicuota General $mivag", $tm );
		$ws->write_blank( $mm,16,  $tm );
		$ws->write_string( $mm,17, "Alicuota Adicional $mivaa", $tm );
		$ws->write_blank( $mm,18,  $tm );
		$ws->write_string( $mm,19, "Alicuota Reducida $mivar", $tm );
		$ws->write_blank( $mm,20,  $tm );
		$ws->write_string( $mm,21, "Retenido", $titulo );
		$ws->write_string( $mm,22, "de", $titulo );
		$ws->write_string( $mm,23, "de", $titulo );
		$ws->write_string( $mm,24, "I.V.A.", $titulo );
		$ws->write_string( $mm,25, "Serial", $titulo );

		$mm++;
		$ws->write_string( $mm, 0, "Oper.", $titulo );
		$ws->write_string( $mm, 1, "", $titulo );
		$ws->write_string( $mm, 2, "del Comprador", $titulo );
		$ws->write_string( $mm, 3, "Cedula", $titulo );
		$ws->write_string( $mm, 4, "Caja", $titulo );
		$ws->write_string( $mm, 5, "Doc.", $titulo );
		$ws->write_string( $mm, 6, "Inicial", $titulo );
		$ws->write_string( $mm, 7, "Final", $titulo );
		$ws->write_string( $mm, 8, "Inicial", $titulo );
		$ws->write_string( $mm, 9, "Final", $titulo );
		$ws->write_string( $mm,10, "Afectado", $titulo );
		$ws->write_string( $mm,11, "Contrib.", $titulo );
		$ws->write_string( $mm,12, "el I.V.A.", $titulo );
		$ws->write_string( $mm,13, "a Impuesto", $titulo );
		$ws->write_string( $mm,14, "Exportacion", $titulo );
		$ws->write_string( $mm,15, "Base", $titulo );
		$ws->write_string( $mm,16, "Impuesto", $titulo );
		$ws->write_string( $mm,17, "Base", $titulo );
		$ws->write_string( $mm,18, "Impuesto", $titulo );
		$ws->write_string( $mm,19, "Base", $titulo );
		$ws->write_string( $mm,20, "Impuesto", $titulo );
		$ws->write_string( $mm,21, "Comprador", $titulo );
		$ws->write_string( $mm,22, "Comprobante", $titulo );
		$ws->write_string( $mm,23, "Emision", $titulo );
		$ws->write_string( $mm,24, "Percibido", $titulo );
		$ws->write_string( $mm,25, "Serial", $titulo );

		$mm++;
		$ii = $mm+1;
		$mtiva = 'X';
		$mfecha = '2000-00-00';
		$tventas = $texenta = $tbaseg  = $tbaser  = $tbasea  = $timpug  = $timpur  = $timpua  = $treiva  = $tperci  = $mforza = $contri =0 ;
		$finicial = '99999999';
		$ffinal   = '00000000';
		$fiscali  = $fiscalf  = '';
		$caja = 'zytrdsefg';

		foreach( $export->result() as  $row ) {
			if ( empty($nfiscali) ) $nfiscali = $row->nfiscal;
			if ($caja == 'zytrdsefg') $caja=$row->caja;
			// chequea la fecha
			if ( $mfecha == $row->fecha ) {
				// Dentro del dia
				if($caja == $row->caja) {
					if($row->tiva == 'SI') {
						$mforza = $contri = 1;
					} else {
						if ( $row->tipo == 'NC' ) {
							$mforza = $contri = 1;
						} else {
							$mforza = $contri = 0;
							if ($finicial == '99999999') $finicial=$row->numero;
						};
					};
				}else {
					if ($finicial == '99999999') $finicial=$row->numero;
					$mforza = 1;
					if ( $row->tiva == 'SI' ) {
						$contri = 1;
					} else {
						$contri = 0;
					}
					//Inicio de consultas al cierre z
					$DB1 = $CI->load->database('default', TRUE);
					$qquery = $DB1->query("SELECT * FROM fiscalz WHERE caja='$caja' AND fecha='$mfecha' AND serial='$serial'");
					if ($qquery->num_rows() > 0){
						foreach ($qquery->result() as $fila){

							$venfc = $fila->exento+$fila->base+$fila->iva+$fila->base1+$fila->iva1+$fila->base2+$fila->iva2;
							$vennc = $fila->ncexento+$fila->ncbase+$fila->nciva+$fila->ncbase1+$fila->nciva1+$fila->ncbase2+$fila->nciva2;

							$ventot  = ($venfc         >$acumulador['VTO']         ) ? $venfc       -$acumulador['VTO']           : 0 ;
							$excen   = ($fila->exento  >$acumulador['FC']['exento']) ? $fila->exento-$acumulador['FC']['exento']  : 0 ;
							$base    = ($fila->base    >$acumulador['FC']['base']  ) ? $fila->base  -$acumulador['FC']['base']    : 0 ;
							$iva     = ($fila->iva     >$acumulador['FC']['iva']   ) ? $fila->iva   -$acumulador['FC']['iva']     : 0 ;
							$base1   = ($fila->base1   >$acumulador['FC']['base1'] ) ? $fila->base1 -$acumulador['FC']['base1']   : 0 ;
							$iva1    = ($fila->iva1    >$acumulador['FC']['iva1']  ) ? $fila->iva1  -$acumulador['FC']['iva1']    : 0 ;
							$base2   = ($fila->base2   >$acumulador['FC']['base2'] ) ? $fila->base2 -$acumulador['FC']['base2']   : 0 ;
							$iva2    = ($fila->iva2    >$acumulador['FC']['iva2']  ) ? $fila->iva2  -$acumulador['FC']['iva2']    : 0 ;

							$ncventot= ($vennc         >$acumulador['NTO']         ) ? $acumulador['NTO']         -$vennc         : 0 ;
							$ncexcen = ($fila->ncexento>$acumulador['NC']['exento']) ? $acumulador['NC']['exento']-$fila->ncexento: 0 ;
							$ncbase  = ($fila->ncbase  >$acumulador['NC']['base']  ) ? $acumulador['NC']['base']  -$fila->ncbase  : 0 ;
							$nciva   = ($fila->nciva   >$acumulador['NC']['iva']   ) ? $acumulador['NC']['iva']   -$fila->nciva   : 0 ;
							$ncbase1 = ($fila->ncbase1 >$acumulador['NC']['base1'] ) ? $acumulador['NC']['base1'] -$fila->ncbase1 : 0 ;
							$nciva1  = ($fila->nciva1  >$acumulador['NC']['iva1']  ) ? $acumulador['NC']['iva1']  -$fila->nciva1  : 0 ;
							$ncbase2 = ($fila->ncbase2 >$acumulador['NC']['base2'] ) ? $acumulador['NC']['base2'] -$fila->ncbase2 : 0 ;
							$nciva2  = ($fila->nciva2  >$acumulador['NC']['iva2']  ) ? $acumulador['NC']['iva2']  -$fila->nciva2  : 0 ;

							$acumulador['VTO']         -=$venfc       ;
							$acumulador['FC']['exento']-=$fila->exento;
							$acumulador['FC']['base']  -=$fila->base  ;
							$acumulador['FC']['iva']   -=$fila->iva   ;
							$acumulador['FC']['base1'] -=$fila->base1 ;
							$acumulador['FC']['iva1']  -=$fila->iva1  ;
							$acumulador['FC']['base2'] -=$fila->base2 ;
							$acumulador['FC']['iva2']  -=$fila->iva2  ;

							$acumulador['NTO']         -=$vennc         ;
							$acumulador['NC']['exento']-=$fila->ncexento;
							$acumulador['NC']['base']  -=$fila->ncbase  ;
							$acumulador['NC']['iva']   -=$fila->nciva   ;
							$acumulador['NC']['base1'] -=$fila->ncbase1 ;
							$acumulador['NC']['iva1']  -=$fila->nciva1  ;
							$acumulador['NC']['base2'] -=$fila->ncbase2 ;
							$acumulador['NC']['iva2']  -=$fila->nciva2  ;

							// VENTAS
							$fecha = substr($mfecha,8,2)."-".$ameses[substr($mfecha,5,2)-1]."-".substr($mfecha,0,4);
							$ws->write_string( $mm, 1, $fecha,  $cuerpo );		// Fecha
							$ws->write_string( $mm, 2, 'VENTAS A NO CONTRIBUYENTES', $cuerpo );    // Nombre
							$ws->write_string( $mm, 3, '  ******  ', $cuerpo );   // RIF/CEDULA
							$ws->write_string( $mm, 4, $caja,  $cuerpo );		      // Fecha
							$ws->write_string( $mm, 5, 'FC', $cuerpo );    		    // TIPO

							$ws->write_string( $mm, 6, $fila->numero, $cuerpo );     // Factura Inicial
							$ws->write_string( $mm, 7, '----', $cuerpo );     // Factura Final
							$ws->write_string( $mm, 8, '----', $cuerpo );     // Nro. N.C.
							$ws->write_string( $mm, 9, '----', $cuerpo );    	// Nro. N.D.

							$ws->write_string( $mm,10, '----' , $cuerpo );   // DOC. AFECTADO
							$ws->write_string( $mm,11, '----' , $cuerpo );   // CONTRIBUYENTE
							$ws->write_number( $mm,12, $ventot, $numero );   // VENTAS + IVA
							$ws->write_number( $mm,13, $excen , $numero );   // EXENTAS
							$ws->write_number( $mm,14, 0      , $numero );   // FOB

							$ws->write_number( $mm,15,$base  , $numero );    // BASE ALICUOTA GENERAL
							$ws->write_number( $mm,16,$iva   , $numero );    // IMPUESTO
							$ws->write_number( $mm,17,$base1 , $numero );    // BASE ALICUOTA ADICIONAL
							$ws->write_number( $mm,18,$iva1  , $numero );    // IMPUESTO
							$ws->write_number( $mm,19,$base2 , $numero );    // BASE ALICUOTA REDUCIDA
							$ws->write_number( $mm,20,$iva2  , $numero );    // IMPUESTO

							$ws->write_number( $mm,21, 0              , $numero ); // IVA RETENIDO
							$ws->write_string( $mm,22, ' '            , $cuerpo ); // NRO COMPROBANTE
							$ws->write_string( $mm,23, $fila->fecha   , $cuerpo ); // FECHA COMPROB
							$ws->write_number( $mm,24, 0              , $numero ); // IMPUESTO PERCIBIDO
							$ws->write_string( $mm,25, $serial        , $numero ); // SERIAL

							//NOTAS DE CREDITO
							$mm++;
							$ws->write_string( $mm, 1, $fecha,  $cuerpo );		// Fecha
							$ws->write_string( $mm, 2, 'NOTAS DE CREDITO A NO CONTRIBUYENTES', $cuerpo );    // Nombre
							$ws->write_string( $mm, 3, '  ******  ', $cuerpo );   // RIF/CEDULA
							$ws->write_string( $mm, 4, $caja,  $cuerpo );		      // Fecha
							$ws->write_string( $mm, 5, 'FC', $cuerpo );    		    // TIPO

							$ws->write_string( $mm, 6, $fila->numero, $cuerpo );         // Factura Inicial
							$ws->write_string( $mm, 7, '----', $cuerpo );         // Factura Final
							$ws->write_string( $mm, 8, '----', $cuerpo );         // Nro. N.C.
							$ws->write_string( $mm, 9, '----', $cuerpo );    	    // Nro. N.D.

							$ws->write_string( $mm,10, '----'      , $cuerpo );    // DOC. AFECTADO
							$ws->write_string( $mm,11, '----'      , $cuerpo );    // CONTRIBUYENTE
							$ws->write_number( $mm,12, $ncventot   , $numero );     // VENTAS + IVA
							$ws->write_number( $mm,13, $ncexcen, $numero );        // EXENTAS
							$ws->write_number( $mm,14, 0           , $numero );    // FOB

							$ws->write_number( $mm,15, $ncbase  , $numero );       // BASE ALICUOTA GENERAL
							$ws->write_number( $mm,16, $nciva   , $numero );       // IMPUESTO
							$ws->write_number( $mm,17, $ncbase1 , $numero );       // BASE ALICUOTA ADICIONAL
							$ws->write_number( $mm,18, $nciva1  , $numero );       // IMPUESTO
							$ws->write_number( $mm,19, $ncbase2 , $numero );       // BASE ALICUOTA REDUCIDA
							$ws->write_number( $mm,20, $nciva2  , $numero );       // IMPUESTO

							$ws->write_number( $mm,21, 0              , $numero ); // IVA RETENIDO
							$ws->write_string( $mm,22, ''   , $cuerpo );           // NRO COMPROBANTE
							$ws->write_string( $mm,23, ''   , $cuerpo );           // FECHA COMPROB
							$ws->write_number( $mm,24, 0              , $numero ); // IMPUESTO PERCIBIDO
							$ws->write_string( $mm,25, $serial        , $numero ); // SERIAL
						}
					}

					$acumulador=array(
						'FC'=>array('exento'=>0,
							'base'  =>0,
							'iva'   =>0,
							'base1' =>0,
							'iva1'  =>0,
							'base2' =>0,
							'iva2'  =>0),
						'NC'=>array('exento'=>0,
							'base'  =>0,
							'iva'   =>0,
							'base1' =>0,
							'iva1'  =>0,
							'base2' =>0,
							'iva2'  =>0),
						'NTO'=>0,
						'VTO'=>0 );
					//Fin inicio de conusltas al cierre z
				};
			} else {
				// Imprime todo
				if ($finicial == '99999999') $finicial=$row->numero;
				$mforza = 1;
				if ( $row->tiva == 'SI' ) {
					$contri = 1;
				} else {
					$contri = 0;
				};
				if ( $row->tipo == 'NC' ) {
					$contri = 1;
				} ;
			};
			if ( $contri ) {
				if($row->tipo_doc == 'NC'){
					$acumulador['NTO']         +=abs($row->ventatotal);
					$acumulador['NC']['exento']+=abs($row->exento);
					$acumulador['NC']['base']  +=abs($row->baseg );
					$acumulador['NC']['base1'] +=abs($row->basea );
					$acumulador['NC']['base2'] +=abs($row->baser );
					$acumulador['NC']['iva']   +=abs($row->impug );
					$acumulador['NC']['iva1']  +=abs($row->impua );
					$acumulador['NC']['iva2']  +=abs($row->impur );
				}elseif($row->tipo_doc == 'FC'){
					$acumulador['VTO']         +=$row->ventatotal;
					$acumulador['FC']['exento']+=$row->exento;
					$acumulador['FC']['base']  +=$row->baseg ;
					$acumulador['FC']['base1'] +=$row->basea ;
					$acumulador['FC']['base2'] +=$row->baser ;
					$acumulador['FC']['iva']   +=$row->impug ;
					$acumulador['FC']['iva1']  +=$row->impua ;
					$acumulador['FC']['iva2']  +=$row->impur ;
				}

				// imprime contribuyente
				$fecha = $row->fecha;
				$ws->write_string( $mm,  0, $mm-8, $cuerpo );
				$fecha = substr($fecha,8,2)."-".$ameses[substr($fecha,5,2)-1]."-".substr($fecha,0,4);
				$ws->write_string( $mm, 1, $fecha,           $cuerpo );             // Fecha
				$ws->write_string( $mm, 2, $row->nombre,     $cuerpo );             // Nombre
				$ws->write_string( $mm, 3, $row->rif,        $cuerpo );             // RIF/CEDULA
				$ws->write_string( $mm, 4, $row->caja,       $cuerpo );             // Caja
				$ws->write_string( $mm, 5, $row->tipo_doc,   $cuerpo );             // Tipo_doc
				$ws->write_string( $mm, 6, $row->numero,     $cuerpo );             // Factura Inicial
				$ws->write_string( $mm, 7, '',               $cuerpo );             // Factura Final
				$ws->write_string( $mm, 8, substr($row->nfiscal,5,8),    $cuerpo ); // Fiscal Inicial
				$ws->write_string( $mm, 9, '',               $cuerpo );             // Fiscal Final
				$ws->write_string( $mm,10, $row->afecta,     $cuerpo );             // DOC. AFECTADO
				$ws->write_string( $mm,11, $row->tiva,       $cuerpo );             // CONTRIBUYENTE
				$ws->write_number( $mm,12, $row->ventatotal, $numero );             // VENTAS + IVA
				$ws->write_number( $mm,13, $row->exento,     $numero );             // VENTAS EXENTAS
				$ws->write_number( $mm,14, 0,                $numero );             // EXPORTACION FOB
				$ws->write_number( $mm,15, $row->baseg,      $numero );             // Base G
				$ws->write_number( $mm,16, $row->impug,      $numero );             // IVA G
				$ws->write_number( $mm,17, $row->basea,      $numero );             // Base A
				$ws->write_number( $mm,18, $row->impua,      $numero );             // IVA A
				$ws->write_number( $mm,19, $row->baser,      $numero );             // BASE R
				$ws->write_number( $mm,20, $row->impur,      $numero );             // IVA R
				$num = $mm+1;
				//$ws->write_formula( $mm,14,"=M$num*N$num/100" , $numero );        //I.V.A.
				$ws->write_number( $mm,21, $row->reiva,         $numero );          // IVA RETENIDO
				$ws->write_string( $mm,22, $row->comprobante,   $cuerpo );          // NRO COMPROBANTE
				$ws->write_string( $mm,23, $row->fechacomp,     $cuerpo );          // FECHA COMPROB
				$ws->write_number( $mm,24, $row->impercibido,   $numero );          // IMPUESTO PERCIBIDO
				$ws->write_string( $mm,25, $row->serial,        $cuerpo );          // SERIAL
				//$ws->write_string( $mm,19, $row->importacion,   $cuerpo );        // IMPORTACION
				$finicial = '99999999';
				$mm++;
			} else {
				// Totaliza
				$tventas += $row->ventatotal ;
				$texenta += $row->exento ;
				$tbaseg  += $row->baseg  ;
				$tbaser  += $row->baser  ;
				$tbasea  += $row->basea  ;
				$timpug  += $row->impug ;
				$timpur  += $row->impur ;
				$timpua  += $row->impua ;
				$treiva  += $row->reiva  ;
				$tperci  += $row->impercibido ;
				if ( $finicial == '99999999' ) $finicial=$row->numero;
				if ( substr($row->final,0,2)!='NC')	$ffinal=$row->final;
			};
			$mfecha   = $row->fecha;
			$caja     = $row->caja;
			$nfiscali = $row->nfiscal;
			$serial   = $row->serial;
		}

		$celda = $mm+1;
		$fventas = "=M$celda";   // VENTAS
		$fexenta = "=N$celda";   // VENTAS EXENTAS
		$fbase   = "=M$celda";   // BASE IMPONIBLE
		$fiva    = "=O$celda";   // I.V.A.

		$ws->write_string( $mm, 0,"Totales...",  $tm );
		$ws->write_blank( $mm, 1,  $tm );
		$ws->write_blank( $mm, 2,  $tm );
		$ws->write_blank( $mm, 3,  $tm );
		$ws->write_blank( $mm, 4,  $tm );
		$ws->write_blank( $mm, 5,  $tm );
		$ws->write_blank( $mm, 6,  $tm );
		$ws->write_blank( $mm, 7,  $tm );
		$ws->write_blank( $mm, 8,  $tm );
		$ws->write_blank( $mm, 9,  $tm );
		$ws->write_blank( $mm,10,  $tm );
		$ws->write_blank( $mm,11,  $tm );

		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,13, "=SUM(N$ii:N$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,16, "=SUM(Q$ii:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,17, "=SUM(R$ii:R$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,18, "=SUM(S$ii:S$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,19, "=SUM(T$ii:T$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,20, "=SUM(U$ii:U$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,21, "=SUM(V$ii:V$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_blank( $mm, 22,  $Tnumero );
		$ws->write_blank( $mm, 23,  $Tnumero );
		$ws->write_formula( $mm,24, "=SUM(Y$ii:Y$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$mm ++;
		$mm ++;
		$ws->write($mm, 4, 'RESUMEN DE VENTAS Y DEBITOS', $tm );
		$ws->write_blank( $mm, 5,  $tm );
		$ws->write_blank( $mm, 6,  $tm );
		$ws->write_blank( $mm, 7,  $tm );
		$ws->write_blank( $mm, 8,  $tm );
		$ws->write_blank( $mm, 9,  $tm );
		$ws->write_blank( $mm,10,  $tm );
		$ws->write_blank( $mm,11,  $tm );

		$ws->write_string($mm, 12, 'Base Imponible', $titulo );
		$ws->write_string($mm, 13, 'Debito Fiscal',  $titulo );

		//$ws->write_string($mm, 15, 'IVA Retenido', $titulo );
		//$ws->write_string($mm, 16, 'IVA Percibido',$titulo );

		$mm++;
		$ws->write($mm, 4, 'Total Ventas Internas no Gravadas:', $h1 );
		$ws->write_formula($mm, 12, "=N$celda" , $Rnumero );
		$mm ++;
		$ws->write($mm, 4, 'Total Ventas de Exportacion:', $h1 );
		$ws->write_formula($mm, 12, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 13, "=0+0" , $Rnumero );
		$mm ++;
		$ws->write($mm,4, 'Total Ventas Internas Gravadas Alicuota General:', $h1 );
		$ws->write_formula($mm, 12, "=P$celda" , $Rnumero );
		$ws->write_formula($mm, 13, "=Q$celda" , $Rnumero );
		$mm ++;
		$ws->write($mm,4, 'Total Ventas Internas Gravadas Alicuota General + Adicional:', $h1 );
		$ws->write_formula($mm, 12, "=R$celda" , $Rnumero );
		$ws->write_formula($mm, 13, "=S$celda" , $Rnumero );
		$mm ++;
		$ws->write($mm,4, 'Total Ventas InternasGravadas Alicuota Reducida:', $h1 );
		$ws->write_formula($mm, 12, "=T$celda" , $Rnumero );
		$ws->write_formula($mm, 13, "=U$celda" , $Rnumero );
		$mm ++;
		$wb->close();
		header("Content-type: application/x-msexcel; name=\"lventas.xls\"");
		header("Content-Disposition: inline; filename=\"lventas.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
		// print "$header\n$data";
	}

	//libro de ventas con punto de ventas fiscal nuevo
	static function wlvexcelpdvfiscal2($mes,$modalidad='M') {
		$CI =& get_instance();
		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));

		if($modalidad=='Q1'){
			$fdesde=$mes.'01';
			$fhasta=$mes.'15';
		}elseif($modalidad=='Q2'){
			$fdesde=$mes.'16';
			$fhasta=$mes.$udia;
		}else{
			$fdesde=$mes.'01';
			$fhasta=$mes.$udia;
		}

		$tasas = $CI->_tasas($mes);
		$mivag = $tasas['general'];
		$mivar = $tasas['reducida'];
		$mivaa = $tasas['adicional'];

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$CI->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$CI->db->simple_query($mSQL);
		$mSQL = "UPDATE club SET cedula=CONCAT('V',cedula) WHERE MID(cedula,1,1) IN ('0','1','2','3','4','5','6','7','8','9')  ";
		$CI->db->simple_query($mSQL);

		// TRATA DE PONER FACTURA AFECTADA DESDE FMAY
		$mSQL = "UPDATE siva a JOIN fmay b ON a.numero=b.numero AND a.fuente='FA' AND b.tipo='D' AND a.fecha=b.fecha SET a.referen=presup WHERE  EXTRACT(YEAR_MONTH FROM a.fechal)=$mes  ";
		$CI->db->simple_query($mSQL);

		// PONE NRO FISCAL EN SIVA
		//$mSQL = "UPDATE siva a JOIN fmay b ON a.numero=b.numero AND a.fuente='FA' AND b.tipo='D' AND a.fecha=b.fecha SET a.referen=presup WHERE  EXTRACT(YEAR_MONTH FROM a.fechal)=$mes  ";
		//$CI->db->simple_query($mSQL);

		// PARA HACERLO MENSUAL
		$SQL[]="SELECT
		 a.fecha,
		 a.numero,
		 a.numhasta AS final,
		 a.rif,
		 a.nombre,
		 0 AS numnc,
		 0 AS numnd,
		 a.tipo AS tipo_doc,
		 '' AS afecta,
		 a.gtotal    AS ventatotal,
		 a.exento    AS exento,
		 a.general   AS baseg,
		 a.reducida  AS baser,
		 a.adicional AS basea,
		 a.impuesto  AS alicuota,
		 a.geneimpu  AS impug,
		 a.reduimpu  AS impur,
		 a.adicimpu  AS impua,
		 a.reiva,
		 a.comprobante,
		 '' AS fechacomp,
		 '' AS impercibido,
		 '' AS importacion,
		 '' AS tiva,
		 a.tipo,
		 a.numero numa,
		 a.caja,
		 a.nfiscal,
		 a.serial AS serial,
		 a.hora,
		 a.cierrez
		 FROM siva a
		 WHERE a.fechal = {$mes}01 AND a.libro='V' AND a.fuente='FP'";

		//RETENCIONES
		$SQL[]="SELECT
		a.emiriva,
		'    ',
		'    ',
		b.rifci,
		b.nombre,
		'    ',
		'    ',
		'RI',
		COALESCE(c.nfiscal,'NO ENCONTRADO') AS afecta,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		a.reteiva,
		a.nroriva,
		a.recriva frecep ,
		'    ',
		'    ',
		'SI',
		'RI',
		'numa',
		'MAYO',
		'    ',
		' ' AS serial,
		0 AS hora,
		'' AS cierrez
		FROM itccli AS a
		JOIN scli   AS b on a.cod_cli=b.cliente
		LEFT JOIN fmay c ON a.numero=c.numero AND c.tipo='C'
		WHERE a.recriva BETWEEN ${fdesde} AND ${fhasta}";

		$SQL[]="SELECT
		 a.fecha,
		 '    ',
		 '    ',
		 c.cedula,
		 CONCAT(c.nombres,' ',c.apellidos),
		 '    ',
		 '    ',
		 'RI',
		 a.numero,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 0,
		 a.monto,
		 CONCAT('20',a.num_ref),
		 a.f_factura ,
		 '    ',
		 '    ',
		 'SI',
		 'RI',
		 'numa',
		 'MAYO',
		 '    ',
		 ' ' AS serial,
		 0 AS hora,
		 '' AS cierrez
		 FROM viepag a JOIN viefac b ON a.numero=b.numero AND a.f_factura=b.fecha
		 LEFT JOIN club c ON b.cliente=c.cod_tar
		 WHERE a.tipo='RI' AND a.f_factura BETWEEN $fdesde AND $fhasta";
		//fin de las retenciones

		$mSQL=implode(" UNION ALL ",$SQL);
		$mSQL.=' ORDER BY `fecha` ASC, `caja` ASC, `hora` ASC';

		$export = $CI->db->query($mSQL);

		 //$fname = tempnam("/tmp","lventas.xls");
		//$CI->load->library("workbookbig",$fname);
		//$wb =& $CI->workbookbig;
		//$ws =& $wb->addworksheet($mes);

		$fname = tempnam("/tmp","lventas.xls");
		$CI->load->library("workbook",array("fname" => $fname));
		$wb =& $CI->workbook;
		$ws =& $wb->addworksheet($mes);

		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:A',6);
		$ws->set_column('B:B',11);
		$ws->set_column('C:C',40);
		$ws->set_column('D:E',12);
		$ws->set_column('F:F',6 );
		$ws->set_column('G:K',11);
		$ws->set_column('L:U',16);
		$ws->set_column('W:W',15);
		$ws->set_column('X:X',11);
		$ws->set_column('Z:Z',12.5);

		// FORMATOS
		$h      =& $wb->addformat(array( "bold" => 1, "size" => 16, "merge" => 1));
		$h1     =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'left'));
		$titulo =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 0, "fg_color" => 'silver' ));
		$cuerpo =& $wb->addformat(array( "size" => 9 ));
		$numero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));
		$tm =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 'silver' ));

		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = "LIBRO DE VENTAS CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL ".substr($mes,0,4);

		$ws->write(1, 0, $CI->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$CI->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<20; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};
		$mm=6;

		// TITULOS
		$ws->write_string( $mm, 0, "", $titulo );
		$ws->write_string( $mm, 1, "", $titulo );
		$ws->write_string( $mm, 2, "", $titulo );
		$ws->write_string( $mm, 3, "", $titulo );
		$ws->write_string( $mm, 4, "", $titulo );
		$ws->write_string( $mm, 5, "Tipo", $titulo );
		$ws->write_string( $mm, 6, "Numero de", $titulo );
		$ws->write_string( $mm, 7, "Numero de", $titulo );
		$ws->write_string( $mm, 8, "Control", $titulo );
		$ws->write_string( $mm, 9, "Control", $titulo );
		$ws->write_string( $mm,10, "Numero de", $titulo );
		$ws->write_string( $mm,11, "Ventas", $titulo );
		$ws->write_string( $mm,12, "Total Ventas", $titulo );
		$ws->write_string( $mm,13, "Ventas Exentas", $titulo );
		$ws->write_string( $mm,14, "Valor FOB", $titulo );
		$ws->write_string( $mm,15, "VENTAS GRAVADAS", $tm );
		$ws->write_blank(  $mm,16,  $tm );
		$ws->write_blank(  $mm,17,  $tm );
		$ws->write_blank(  $mm,18,  $tm );
		$ws->write_blank(  $mm,19,  $tm );
		$ws->write_blank(  $mm,20,  $tm );
		$ws->write_string( $mm,21, "IVA", $titulo );
		$ws->write_string( $mm,22, "Numero", $titulo );
		$ws->write_string( $mm,23, "Fecha", $titulo );
		$ws->write_string( $mm,24, "", $titulo );
		$ws->write_string( $mm,25, "", $titulo );
		$ws->write_string( $mm,25, "", $titulo );
		$ws->write_string( $mm,26, "", $titulo );


		$mm++;
		$ws->write_string( $mm, 0, "Num.", $titulo );
		$ws->write_string( $mm, 1, "Fecha", $titulo );
		$ws->write_string( $mm, 2, "Nombre, Razon Social o Denominacion ", $titulo );
		$ws->write_string( $mm, 3, "RIF o", $titulo );
		$ws->write_string( $mm, 4, "Numero", $titulo );
		$ws->write_string( $mm, 5, "de", $titulo );
		$ws->write_string( $mm, 6, "Documento", $titulo );
		$ws->write_string( $mm, 7, "Documento.", $titulo );
		$ws->write_string( $mm, 8, "Fiscal", $titulo );
		$ws->write_string( $mm, 9, "Fiscal", $titulo );
		$ws->write_string( $mm,10, "Documento", $titulo );
		$ws->write_string( $mm,11, "A", $titulo );
		$ws->write_string( $mm,12, "Incluyendo", $titulo );
		$ws->write_string( $mm,13, "o no sujetas", $titulo );
		$ws->write_string( $mm,14, "Operacion", $titulo );
		$ws->write_string( $mm,15, "Alicuota General $mivag", $tm );
		$ws->write_blank(  $mm,16,  $tm );
		$ws->write_string( $mm,17, "Alicuota Adicional $mivaa", $tm );
		$ws->write_blank(  $mm,18,  $tm );
		$ws->write_string( $mm,19, "Alicuota Reducida $mivar", $tm );
		$ws->write_blank(  $mm,20,  $tm );
		$ws->write_string( $mm,21, "Retenido", $titulo );
		$ws->write_string( $mm,22, "de", $titulo );
		$ws->write_string( $mm,23, "de", $titulo );
		$ws->write_string( $mm,24, "I.V.A.", $titulo );
		$ws->write_string( $mm,25, "Serial", $titulo );
		$ws->write_string( $mm,26, "Cierre Z", $titulo );


		$mm++;
		$ws->write_string( $mm, 0, "Oper.", $titulo );
		$ws->write_string( $mm, 1, "", $titulo );
		$ws->write_string( $mm, 2, "del Comprador", $titulo );
		$ws->write_string( $mm, 3, "Cedula", $titulo );
		$ws->write_string( $mm, 4, "Caja", $titulo );
		$ws->write_string( $mm, 5, "Doc.", $titulo );
		$ws->write_string( $mm, 6, "Inicial", $titulo );
		$ws->write_string( $mm, 7, "Final", $titulo );
		$ws->write_string( $mm, 8, "Inicial", $titulo );
		$ws->write_string( $mm, 9, "Final", $titulo );
		$ws->write_string( $mm,10, "Afectado", $titulo );
		$ws->write_string( $mm,11, "Contrib.", $titulo );
		$ws->write_string( $mm,12, "el I.V.A.", $titulo );
		$ws->write_string( $mm,13, "a Impuesto", $titulo );
		$ws->write_string( $mm,14, "Exportacion", $titulo );
		$ws->write_string( $mm,15, "Base", $titulo );
		$ws->write_string( $mm,16, "Impuesto", $titulo );
		$ws->write_string( $mm,17, "Base", $titulo );
		$ws->write_string( $mm,18, "Impuesto", $titulo );
		$ws->write_string( $mm,19, "Base", $titulo );
		$ws->write_string( $mm,20, "Impuesto", $titulo );
		$ws->write_string( $mm,21, "Comprador", $titulo );
		$ws->write_string( $mm,22, "Comprobante", $titulo );
		$ws->write_string( $mm,23, "Emision", $titulo );
		$ws->write_string( $mm,24, "Percibido", $titulo );
		$ws->write_string( $mm,25, "", $titulo );
		$ws->write_string( $mm,26, "", $titulo );


		$mm++;
		$ii = $mm+1;
		$mtiva = 'X';
		$mfecha = '2000-00-00';
		$tventas = $texenta = $tbaseg  = $tbaser  = $tbasea  = $timpug  = $timpur  = $timpua  = $treiva  = $tperci  = $mforza = $contri =0 ;
		$finicial = '99999999';
		$ffinal   = '00000000';
		$fiscali  = $fiscalf  = '';
		$caja = 'zytrdsefg';

		foreach( $export->result() as  $row ) {
			if ( empty($nfiscali) ) $nfiscali = $row->nfiscal;
			if ($caja == 'zytrdsefg') $caja=$row->caja;

			// imprime contribuyente
			$fecha     = $row->fecha;
			$fecha     = substr($fecha,8,2)."/".substr($fecha,5,2)."/".substr($fecha,0,4);
			if(empty($row->fechacomp)){
				$fechacomp = '';
			}else{
				$fechacomp = substr($row->fechacomp,8,2)."/".substr($row->fechacomp,5,2)."/".substr($row->fechacomp,0,4);
			}

			$ws->write_string( $mm, 0, $mm-8, $cuerpo );
			$ws->write_string( $mm, 1, $fecha,           $cuerpo );             // Fecha
			$ws->write_string( $mm, 2, $row->nombre,     $cuerpo );             // Nombre
			$ws->write_string( $mm, 3, $row->rif,        $cuerpo );             // RIF/CEDULA
			$ws->write_string( $mm, 4, $row->caja,       $cuerpo );             // Caja
			$ws->write_string( $mm, 5, $row->tipo_doc,   $cuerpo );             // Tipo_doc
			$ws->write_string( $mm, 6, $row->numero,     $cuerpo );             // Factura Inicial
			$ws->write_string( $mm, 7, $row->final,      $cuerpo );             // Factura Final
			$ws->write_string( $mm, 8, substr($row->nfiscal,5,8),    $cuerpo ); // Fiscal Inicial
			$ws->write_string( $mm, 9, '',               $cuerpo );             // Fiscal Final
			$ws->write_string( $mm,10, $row->afecta,     $cuerpo );             // DOC. AFECTADO
			$ws->write_string( $mm,11, $row->tiva,       $cuerpo );             // CONTRIBUYENTE
			$ws->write_number( $mm,12, $row->ventatotal, $numero );             // VENTAS + IVA
			$ws->write_number( $mm,13, $row->exento,     $numero );             // VENTAS EXENTAS
			$ws->write_number( $mm,14, 0,                $numero );             // EXPORTACION FOB
			$ws->write_number( $mm,15, $row->baseg,      $numero );             // Base G
			$ws->write_number( $mm,16, $row->impug,      $numero );             // IVA G
			$ws->write_number( $mm,17, $row->basea,      $numero );             // Base A
			$ws->write_number( $mm,18, $row->impua,      $numero );             // IVA A
			$ws->write_number( $mm,19, $row->baser,      $numero );             // BASE R
			$ws->write_number( $mm,20, $row->impur,      $numero );             // IVA R
			$num = $mm+1;
			//$ws->write_formula( $mm,14,"=M$num*N$num/100" , $numero );        //I.V.A.
			$ws->write_number( $mm,21, $row->reiva,         $numero );          // IVA RETENIDO
			$ws->write_string( $mm,22, $row->comprobante,   $cuerpo );          // NRO COMPROBANTE
			$ws->write_string( $mm,23, $fechacomp,          $cuerpo );          // FECHA COMPROB
			$ws->write_number( $mm,24, $row->impercibido,   $numero );          // IMPUESTO PERCIBIDO
			$ws->write_string( $mm,25, $row->serial,        $cuerpo );          // SERIAL
			$ws->write_string( $mm,26, $row->cierrez,       $cuerpo );          // CIERRE Z
			//$ws->write_string( $mm,19, $row->importacion,   $cuerpo );        // IMPORTACION
			$finicial = '99999999';
			$mm++;

			$mfecha   = $row->fecha;
			$caja     = $row->caja;
			$nfiscali = $row->nfiscal;
			$serial   = $row->serial;
		}

		$celda = $mm+1;
		$fventas = "=M$celda";   // VENTAS
		$fexenta = "=N$celda";   // VENTAS EXENTAS
		$fbase   = "=M$celda";   // BASE IMPONIBLE
		$fiva    = "=O$celda";   // I.V.A.

		$ws->write_string( $mm, 0,"Totales...",  $tm );
		$ws->write_blank( $mm, 1,  $tm );
		$ws->write_blank( $mm, 2,  $tm );
		$ws->write_blank( $mm, 3,  $tm );
		$ws->write_blank( $mm, 4,  $tm );
		$ws->write_blank( $mm, 5,  $tm );
		$ws->write_blank( $mm, 6,  $tm );
		$ws->write_blank( $mm, 7,  $tm );
		$ws->write_blank( $mm, 8,  $tm );
		$ws->write_blank( $mm, 9,  $tm );
		$ws->write_blank( $mm,10,  $tm );
		$ws->write_blank( $mm,11,  $tm );

		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,13, "=SUM(N$ii:N$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,16, "=SUM(Q$ii:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,17, "=SUM(R$ii:R$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,18, "=SUM(S$ii:S$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,19, "=SUM(T$ii:T$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,20, "=SUM(U$ii:U$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,21, "=SUM(V$ii:V$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_blank(   $mm,22,  $Tnumero );
		$ws->write_blank(   $mm,23,  $Tnumero );
		$ws->write_formula( $mm,24, "=SUM(Y$ii:Y$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_blank(   $mm,25,  $tm );

		$mm ++;
		$mm ++;
		$ws->write($mm, 4, 'RESUMEN DE VENTAS Y DEBITOS', $tm );
		$ws->write_blank( $mm, 5,  $tm );
		$ws->write_blank( $mm, 6,  $tm );
		$ws->write_blank( $mm, 7,  $tm );
		$ws->write_blank( $mm, 8,  $tm );
		$ws->write_blank( $mm, 9,  $tm );
		$ws->write_blank( $mm,10,  $tm );
		$ws->write_blank( $mm,11,  $tm );

		$ws->write_string($mm, 12, 'Base Imponible', $titulo );
		$ws->write_string($mm, 13, 'Debito Fiscal',  $titulo );

		$mm++;
		$ws->write($mm, 4, 'Total Ventas Internas no Gravadas:', $h1 );
		$ws->write_formula($mm, 12, "=N$celda" , $Rnumero );
		$mm ++;
		$ws->write($mm, 4, 'Total Ventas de Exportacion:', $h1 );
		$ws->write_formula($mm, 12, "=0+0" , $Rnumero );
		$ws->write_formula($mm, 13, "=0+0" , $Rnumero );
		$mm ++;
		$ws->write($mm,4, 'Total Ventas Internas Gravadas Alicuota General:', $h1 );
		$ws->write_formula($mm, 12, "=P$celda" , $Rnumero );
		$ws->write_formula($mm, 13, "=Q$celda" , $Rnumero );
		$mm ++;
		$ws->write($mm,4, 'Total Ventas Internas Gravadas Alicuota General + Adicional:', $h1 );
		$ws->write_formula($mm, 12, "=R$celda" , $Rnumero );
		$ws->write_formula($mm, 13, "=S$celda" , $Rnumero );
		$mm ++;
		$ws->write($mm,4, 'Total Ventas InternasGravadas Alicuota Reducida:', $h1 );
		$ws->write_formula($mm, 12, "=T$celda" , $Rnumero );
		$ws->write_formula($mm, 13, "=U$celda" , $Rnumero );
		$mm ++;
		$wb->close();
		header("Content-type: application/x-msexcel; name=\"lventas.xls\"");
		header("Content-Disposition: inline; filename=\"lventas.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
	}

	//***********************************************
	// GENERACION
	//***********************************************

	static function geneventasfiscalpdv($mes) {
		$CI =& get_instance();
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		$CI->db->simple_query("DELETE FROM siva WHERE fechal = ${fdesde} AND fuente='FP'");

		$tasas = $CI->_tasas($mes);
		$mivag = $tasas['general'];
		$mivar = $tasas['reducida'];
		$mivaa = $tasas['adicional'];

		$CI->db->simple_query("UPDATE fiscalz SET caja='MAYO' WHERE caja='0001'");
		$CI->db->simple_query("UPDATE fiscalz SET hora=CONCAT_WS(':',MINUTE(hora),SECOND(hora),'00') WHERE caja='MAYO' AND HOUR(hora)=0");

		$mSQL="SELECT
			caja,
			serial,
			numero,
			fecha,
			MAX(factura)  AS factura,
			MIN(fecha1)   AS fecha1,
			MAX(hora)     AS hora,
			SUM(exento)   AS exento,
			SUM(base)     AS base,
			SUM(iva)      AS iva,
			SUM(base1)    AS base1,
			SUM(iva1)     AS iva1,
			SUM(base2)    AS base2,
			SUM(iva2)     AS iva2,
			SUM(ncexento) AS ncexento,
			SUM(ncbase)   AS ncbase,
			SUM(nciva)    AS nciva,
			SUM(ncbase1)  AS ncbase1,
			SUM(nciva1)   AS nciva1,
			SUM(ncbase2)  AS ncbase2,
			SUM(nciva2)   AS nciva2,
			MAX(ncnumero) AS ncnumero
		FROM fiscalz WHERE fecha BETWEEN ${fdesde} AND ${fhasta} GROUP BY caja,fecha,serial";

		$query = $CI->db->query($mSQL);

		if ($query->num_rows() > 0){
			$data['libro']     ='V';
 			$data['fuente']    ='FP';
 			$data['sucursal']  ='00';
 			$data['tipo']      ='CZ';
 			$data['nacional']  ='S';
 			$data['fechal']    =$fdesde;

			foreach ($query->result() as $row){

				$data['serial']    =$row->serial;
				$data['fecha']     =$row->fecha;
				$data['caja']      =$row->caja;
				$data['hora']      =$row->hora;
				$hhasta=$row->hora;

				if(!isset($ddesde[$row->caja])){
					if($row->fecha1 == $row->fecha){
						$hdesde='0';
					}else{
						$hdesde=$CI->datasis->dameval("SELECT MAX(hora) FROM fiscalz WHERE fecha='{$row->fecha1}' AND caja='{$row->caja}'");
						if(empty($hora))
							$hdesde='0';
					}

					$cur=$CI->datasis->damerow("SELECT MAX(factura) AS ff, MAX(ncnumero) AS nc FROM fiscalz WHERE fecha<'{$row->fecha}' AND caja='{$row->caja}'");
					if(count($cur)>0){
						$ncdesde =(empty($cur['nc'])) ? '00000001' : $cur['nc'];
						$ffdesde =(empty($cur['ff'])) ? '00000001' : $cur['ff'];
					}else{
						$ncdesde ='00000001';
						$ffdesde ='00000001';
					}
					$frealnum=$CI->datasis->dameval("SELECT MIN(numero) AS numero FROM viefac WHERE fecha BETWEEN '$row->fecha1' AND '$row->fecha' AND caja='$row->caja' AND hora>='${hdesde}' AND hora<'${hhasta}'");
					$factor=$ffdesde-$frealnum;
					$ddesde[$row->caja]['factor']=$factor;
				}else{
					$ncdesde =$ddesde[$row->caja]['ncdesde'];
					$ffdesde =$ddesde[$row->caja]['ffdesde'];
					$factor  =$ddesde[$row->caja]['factor'];
				}

				//Para ventas al mayor
				if(eregi('^MAY.$',$row->caja)){
					$mSQL_1 ="INSERT INTO siva
						(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
						referen, planilla, clipro, nombre, contribu, rif, registro,
						nacional, exento, general, geneimpu,
						adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
						gtotal, reiva, fechal, fafecta,hora)
						SELECT
						0 AS id,
						'V' AS libro,
						IF(b.tipo='D','NC','FC') AS tipo,
						'FP' AS fuente,
						'00' AS sucursal,
						b.fecha,
						b.numero AS numa,
						b.numero AS final,
						'$row->caja' AS caja,
						b.nfiscal,
						'  ' AS nhfiscal,
						'' AS referen,
						'  ' AS planilla,
						b.cod_cli AS clipro,
						b.nombre AS nombre,
						'CO' AS contribu,
						c.rifci,
						'01' AS registro,
						'S' AS nacional,
						IF(b.tipo='D',-1,1)*sum(IF(a.iva=0     ,1,0)*a.importe)           AS exento,
						IF(b.tipo='D',-1,1)*sum(IF(a.iva=$mivag,1,0)*a.importe)           AS general,
						IF(b.tipo='D',-1,1)*sum(IF(a.iva=$mivag,1,0)*a.importe*a.iva/100) AS geneimpu,
						IF(b.tipo='D',-1,1)*sum(IF(a.iva=$mivaa,1,0)*a.importe)           AS adicional,
						IF(b.tipo='D',-1,1)*sum(IF(a.iva=$mivaa,1,0)*a.importe*a.iva/100) AS adicimpu,
						IF(b.tipo='D',-1,1)*sum(IF(a.iva=$mivar,1,0)*a.importe)           AS reducida,
						IF(b.tipo='D',-1,1)*sum(IF(a.iva=$mivar,1,0)*a.importe*a.iva/100) AS reduimpu,
						IF(b.tipo='D',-1,1)*b.stotal AS stotal,
						IF(b.tipo='D',-1,1)*b.impuesto AS impuesto,
						IF(b.tipo='D',-1,1)*b.gtotal AS gtotal,
						0 AS reiva,
						".$mes."01 AS fechal,
						0 AS fafecta ,
						b.hora
						FROM itfmay AS a JOIN fmay AS b ON a.numero=b.numero AND a.fecha=b.fecha
						LEFT JOIN scli AS c ON b.cod_cli=c.cliente
						WHERE b.fecha BETWEEN '$row->fecha1' AND '$row->fecha'
						AND b.hora>='$hdesde' AND b.hora<'$hhasta' AND b.tipo!='A' AND c.tiva='C'
						GROUP BY a.fecha,a.numero";
						//echo $mSQL_1;
				}else{
					$mSQL_1 ="INSERT INTO siva
						(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
						serial, planilla, clipro, nombre, contribu, rif, registro,
						nacional, exento, general, geneimpu,
						adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
						gtotal, reiva, fechal, fafecta,hora) SELECT
						 0  AS id,
						'V' AS libro,
						IF(MID(a.numero,1,2)='NC','NC','FC') AS tipo_doc,
						'FP'                                 AS fuente,
						CONCAT('0',MID(b.caja,1,1))          AS sucursal,
						a.fecha          AS fecha,
						LPAD(CAST(a.numero AS UNSIGNED)+($factor),8,'0') AS numa,
						LPAD(CAST(a.numero AS UNSIGNED)+($factor),8,'0') AS final,
						b.caja,
						' '       AS nfiscal,
						' '       AS nhfiscal,
						e.serial  AS referen,
						' '       AS planilla,
						b.cliente AS clipro,
						CONCAT(c.nombres,' ', c.apellidos) AS nombre,
						'CO'                               AS contribu,
						c.cedula                           AS rif,
						'01'                               AS registro,
						'S'                                AS nacional,
						SUM(a.monto*(a.impuesto=0)) exento,
						ROUND(SUM(a.monto*(a.impuesto=".$mivag.")*100/(100+a.impuesto)),2) baseg,
						ROUND(SUM(a.monto*(a.impuesto=".$mivag.")-a.monto*(a.impuesto=".$mivag.")*100/(100+a.impuesto)),2) AS impug,
						ROUND(SUM(a.monto*(a.impuesto=".$mivaa.")*100/(100+a.impuesto)),2) AS basea,
						ROUND(SUM(a.monto*(a.impuesto=".$mivaa.")-a.monto*(a.impuesto=".$mivaa.")*100/(100+a.impuesto)),2) AS impua,
						ROUND(SUM(a.monto*(a.impuesto=".$mivar.")*100/(100+a.impuesto)),2) AS baser,
						ROUND(SUM(a.monto*(a.impuesto=".$mivar.")-a.monto*(a.impuesto=".$mivar.")*100/(100+a.impuesto)),2) AS impur,
						ROUND(SUM((a.monto*100)/(100+a.impuesto)),2)           AS stotal,
						ROUND(SUM(a.monto-((a.monto*100)/(100+a.impuesto))),2) AS impuesto,
						SUM(a.monto) AS gtotal,
						0            AS reiva,
						{$mes}01     AS fechal,
						0            AS fafecta,
						b.hora
					FROM vieite AS a
					LEFT JOIN viefac AS b ON a.numero=b.numero and a.caja=b.caja
					LEFT JOIN fiscalz AS e ON a.caja=e.caja AND a.fecha=e.fecha
					LEFT JOIN club c ON b.cliente=c.cod_tar
					LEFT JOIN dine d ON a.fecha=d.fecha AND a.caja=d.caja AND a.cajero=d.cajero
					WHERE a.fecha BETWEEN '$row->fecha1' AND '$row->fecha' AND c.cedula REGEXP '^[VEJG][0-9]{9}$'
					AND a.caja='$row->caja' AND b.hora>='${hdesde}' AND b.hora<'${hhasta}'
					GROUP BY a.fecha, a.caja";
				}

				$flag=$CI->db->simple_query($mSQL_1);
				if($flag==false){ memowrite($mSQL_1,'geneventasfiscal'); }

				$mSQL_2="SELECT tipo,
					SUM(exento)    AS exento,
					SUM(general)   AS baseg,
					SUM(reducida)  AS baser,
					SUM(adicional) AS basea,
					SUM(geneimpu)  AS ivag,
					SUM(reduimpu)  AS ivar,
					SUM(adicimpu)  AS ivaa
				FROM siva WHERE caja='{$row->caja}' AND fecha BETWEEN '$row->fecha1' AND '$row->fecha' AND hora>='$hdesde' AND hora<'$hhasta' AND tipo IN ('FC','NC') AND fuente='FP'
				GROUP BY tipo";

				$query_2 = $CI->db->query($mSQL_2);
				$cant=$query_2->num_rows();
				$NC=$FC=FALSE;

				foreach ($query_2->result() as $rrow){
					if($rrow->tipo=='FC'){ $FC=TRUE;
						$data['nombre']    = 'VENTAS A NO CONTRIBUYENTE';
						$data['exento']    = $row->exento-$rrow->exento;
						$data['general']   = $row->base  -$rrow->baseg;
						$data['reducida']  = $row->base1 -$rrow->baser;
						$data['adicional'] = $row->base2 -$rrow->basea;
						$data['geneimpu']  = $row->iva   -$rrow->ivag;
						$data['reduimpu']  = $row->iva1  -$rrow->ivar;
						$data['adicimpu']  = $row->iva2  -$rrow->ivaa;
						$data['numhasta']  = $row->factura;
						$data['numero']    = $ffdesde;
					}elseif($rrow->tipo=='NC'){ $NC=TRUE;
						$data['nombre']    = 'NOTAS DE CREDITO A NO CONTRIBUYENTE';
						$data['exento']    = (-1)*($row->ncexento+$rrow->exento);
						$data['general']   = (-1)*($row->ncbase  +$rrow->baseg);
						$data['reducida']  = (-1)*($row->ncbase1 +$rrow->baser);
						$data['adicional'] = (-1)*($row->ncbase2 +$rrow->basea);
						$data['geneimpu']  = (-1)*($row->nciva   +$rrow->ivag);
						$data['reduimpu']  = (-1)*($row->nciva1  +$rrow->ivar);
						$data['adicimpu']  = (-1)*($row->nciva2  +$rrow->ivaa);
						$data['numhasta']  = $row->ncnumero;
						$data['numero']    = $ncdesde;
					}
					$data['stotal']   =$data['exento']+$data['general']+$data['reducida']+$data['adicional'];
					$data['impuesto'] =$data['geneimpu']+$data['reduimpu']+$data['adicimpu'];
					$data['gtotal']   =$data['stotal']+$data['impuesto'];

					if($data['gtotal']!=0){
						$mmSQL =$CI->db->insert_string('siva', $data);
						$flag=$CI->db->simple_query($mmSQL);
						if(!$flag){memowrite($mmSQL,'geneventasfiscal'); return 0; };
					}
				}

				//Si no hay ventas a contribuyente
				if(!$NC){
					$data['nombre']    = 'NOTAS DE CREDITO A NO CONTRIBUYENTE';
					$data['exento']    = (-1)*($row->ncexento);
					$data['general']   = (-1)*($row->ncbase  );
					$data['reducida']  = (-1)*($row->ncbase1 );
					$data['adicional'] = (-1)*($row->ncbase2 );
					$data['geneimpu']  = (-1)*($row->nciva   );
					$data['reduimpu']  = (-1)*($row->nciva1  );
					$data['adicimpu']  = (-1)*($row->nciva2  );
					$data['numhasta']  = $row->ncnumero;
					$data['numero']    = $ncdesde;

					$data['stotal']   =$data['exento']+$data['general']+$data['reducida']+$data['adicional'];
					$data['impuesto'] =$data['geneimpu']+$data['reduimpu']+$data['adicimpu'];
					$data['gtotal']   =$data['stotal']+$data['impuesto'];

					if($data['gtotal']<0){
						$mmSQL =$CI->db->insert_string('siva', $data);
						$flag=$CI->db->simple_query($mmSQL);
						if(!$flag){echo $mmSQL; memowrite($mmSQL,'geneventasfiscal'); return 0; };
					}
				}
				if(!$FC){
					$data['nombre']    = 'VENTAS A NO CONTRIBUYENTE';
					$data['exento']    = $row->exento;
					$data['general']   = $row->base  ;
					$data['reducida']  = $row->base1 ;
					$data['adicional'] = $row->base2 ;
					$data['geneimpu']  = $row->iva   ;
					$data['reduimpu']  = $row->iva1  ;
					$data['adicimpu']  = $row->iva2  ;
					$data['numhasta']  = $row->factura;
					$data['numero']    = $ffdesde;

					$data['stotal']   =$data['exento']+$data['general']+$data['reducida']+$data['adicional'];
					$data['impuesto'] =$data['geneimpu']+$data['reduimpu']+$data['adicimpu'];
					$data['gtotal']   =$data['stotal']+$data['impuesto'];

					if($data['gtotal']>0){
						$mmSQL =$CI->db->insert_string('siva', $data);
						$flag=$CI->db->simple_query($mmSQL);
						if(!$flag){memowrite($mmSQL,'geneventasfiscal'); return 0; };
					}
				}
				$ddesde[$row->caja]=array('ncdesde'=>str_pad($row->ncnumero+1,8,"0", STR_PAD_LEFT),'ffdesde'=>str_pad($row->factura+1,8,"0", STR_PAD_LEFT),'factor'=>$factor);
				$hdesde =$row->hora;
			}
		}
	}


	static function genesfacfiscalpdv($mes) {
		$CI =& get_instance();
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		$CI->db->simple_query("DELETE FROM siva WHERE fechal = ${fdesde} AND fuente='FP'");

		$tasas = $CI->_tasas($mes);
		$mivag = $tasas['general'];
		$mivar = $tasas['reducida'];
		$mivaa = $tasas['adicional'];

		$CI->db->simple_query("UPDATE sfacfis SET total=baseg+ivag+baser+ivar+basea+ivaa+exento");
		$CI->db->simple_query("UPDATE fiscalz SET caja='MAYO' WHERE caja='0001'");
		$CI->db->simple_query("UPDATE fiscalz SET hora=CONCAT_WS(':',MINUTE(hora),SECOND(hora),'00') WHERE caja='MAYO' AND HOUR(hora)=0");

		$mSQL="SELECT
			a.caja,
			a.serial,
			a.numero,
			a.fecha,
			b.fecha    AS fecdesde,
			a.factura  AS factura,
			b.factura  AS fcdesde,
			a.fecha1   AS fecha1,
			a.hora     AS hora,
			a.exento   AS exento,
			a.base     AS base,
			a.iva      AS iva,
			a.base1    AS base1,
			a.iva1     AS iva1,
			a.base2    AS base2,
			a.iva2     AS iva2,
			a.ncexento AS ncexento,
			a.ncbase   AS ncbase,
			a.nciva    AS nciva,
			a.ncbase1  AS ncbase1,
			a.nciva1   AS nciva1,
			a.ncbase2  AS ncbase2,
			a.nciva2   AS nciva2,
			b.ncnumero AS ncdesde,
			a.ncnumero AS ncnumero
		FROM fiscalz AS a
		LEFT JOIN fiscalz AS b ON a.numero-1 = b.numero AND a.serial=b.serial
		WHERE a.fecha BETWEEN ${fdesde} AND ${fhasta}
		";

		$query = $CI->db->query($mSQL);

		if ($query->num_rows() > 0){
			$data['libro']     ='V';
			$data['fuente']    ='FP';
			$data['sucursal']  ='00';
			$data['tipo']      ='CZ';
			$data['nacional']  ='S';
			$data['fechal']    =$fdesde;

			$emSQL = "INSERT INTO siva
				(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
				serial, planilla, clipro, nombre, contribu, rif, registro,
				nacional, exento, general, geneimpu,
				adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
				gtotal, reiva, fechal, fafecta,hora,cierrez) ";

			foreach ($query->result() as $row){

				$data['serial'] = $row->serial;
				$data['fecha']  = $row->fecha;
				$data['caja']   = $row->caja;
				$data['hora']   = $row->hora;
				$data['cierrez']= $row->numero;
				$hhasta=$row->hora;

				$ffdesde = $row->fcdesde;
				$ffhasta = $row->factura;
				$ncdesde = $row->ncdesde;
				$nchasta = $row->ncnumero;

				$dbffdesde = $CI->db->escape($ffdesde);
				$dbffhasta = $CI->db->escape($ffhasta);
				$dbncdesde = $CI->db->escape($ncdesde);
				$dbnchasta = $CI->db->escape($nchasta);
				$dbserial  = $CI->db->escape($row->serial);
				$dbcierrez = $CI->db->escape($row->numero);

				//Para ventas al mayor
				if(preg_match('/^MAY.*$/',$row->caja)){
					if($ffhasta>$ffdesde){
						$mSQL_1 = $emSQL."SELECT
							NULL AS id,
							'V' AS libro,
							IF(b.tipo='D','NC','FC') AS tipo,
							'FP' AS fuente,
							'00' AS sucursal,
							b.fecha,
							MID(b.nfiscal,-8) AS numa,
							MID(b.nfiscal,-8) AS final,
							'$row->caja' AS caja,
							'' AS ndfiscal,
							'' AS nhfiscal,
							$dbserial AS referen,
							'' AS planilla,
							b.cod_cli AS clipro,
							b.nombre AS nombre,
							'CO' AS contribu,
							c.rifci,
							'01' AS registro,
							'S' AS nacional,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=0       ,1,0)*a.importe)           AS exento,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=${mivag},1,0)*a.importe)           AS general,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=${mivag},1,0)*a.importe*a.iva/100) AS geneimpu,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=${mivaa},1,0)*a.importe)           AS adicional,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=${mivaa},1,0)*a.importe*a.iva/100) AS adicimpu,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=${mivar},1,0)*a.importe)           AS reducida,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=${mivar},1,0)*a.importe*a.iva/100) AS reduimpu,
							IF(b.tipo='D',-1,1)*SUM(a.importe) AS stotal,
							IF(b.tipo='D',-1,1)*SUM(a.importe*a.iva/100) AS impuesto,
							IF(b.tipo='D',-1,1)*SUM(a.importe*(1+(iva/100))) AS gtotal,
							0 AS reiva,
							".$mes."01 AS fechal,
							0 AS fafecta ,
							b.hora,
							$dbcierrez AS cierrez
							FROM itfmay AS a
							JOIN fmay AS b ON a.numero=b.numero AND a.fecha=b.fecha
							LEFT JOIN scli AS c ON b.cod_cli=c.cliente
							WHERE b.nfiscal>${ffdesde} AND b.nfiscal<=${ffhasta}
							AND b.fecha BETWEEN '$row->fecha1' AND '$row->fecha'
							AND  b.tipo IN ('E','C') AND c.tiva='C'
							GROUP BY a.fecha,a.numero";

						$flag=$CI->db->simple_query($mSQL_1);
						if($flag==false){
							memowrite('1'.$mSQL_1,'genesfacfiscalpdv');
						}
					}

					if($nchasta>$ncdesde){
						$mSQL_1 = $emSQL."SELECT
							NULL AS id,
							'V' AS libro,
							IF(b.tipo='D','NC','FC') AS tipo,
							'FP' AS fuente,
							'00' AS sucursal,
							b.fecha,
							MID(b.nfiscal,-8) AS numa,
							MID(b.nfiscal,-8) AS final,
							'$row->caja' AS caja,
							'' AS ndfiscal,
							'' AS nhfiscal,
							$dbserial AS referen,
							'' AS planilla,
							b.cod_cli AS clipro,
							b.nombre AS nombre,
							'CO' AS contribu,
							c.rifci,
							'01' AS registro,
							'S' AS nacional,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=0     ,1,0)*a.importe)           AS exento,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=$mivag,1,0)*a.importe)           AS general,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=$mivag,1,0)*a.importe*a.iva/100) AS geneimpu,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=$mivaa,1,0)*a.importe)           AS adicional,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=$mivaa,1,0)*a.importe*a.iva/100) AS adicimpu,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=$mivar,1,0)*a.importe)           AS reducida,
							IF(b.tipo='D',-1,1)*SUM(IF(a.iva=$mivar,1,0)*a.importe*a.iva/100) AS reduimpu,
							IF(b.tipo='D',-1,1)*b.stotal AS stotal,
							IF(b.tipo='D',-1,1)*b.impuesto AS impuesto,
							IF(b.tipo='D',-1,1)*b.gtotal AS gtotal,
							0 AS reiva,
							".$mes."01 AS fechal,
							0 AS fafecta ,
							b.hora,
							$dbcierrez AS cierrez
							FROM itfmay AS a
							JOIN fmay AS b ON a.numero=b.numero AND a.fecha=b.fecha
							LEFT JOIN scli AS c ON b.cod_cli=c.cliente
							WHERE b.nfiscal>$ncdesde AND b.nfiscal<=$nchasta
							AND b.fecha BETWEEN '$row->fecha1' AND '$row->fecha'
							AND  b.tipo='D' AND c.tiva='C'
							GROUP BY a.fecha,a.numero";

						$flag=$CI->db->simple_query($mSQL_1);
						if($flag==false){
							memowrite('1'.$mSQL_1,'genesfacfiscalpdv');
						}
					}
				}else{ //para las ventas al detal

					if($ffhasta>$ffdesde){
						$mSQL_1 = $emSQL."SELECT
							 NULL  AS id,
							'V' AS libro,
							IF(a.tipo_doc='NC','NC','FC') AS tipo_doc,
							'FP'                                 AS fuente,
							CONCAT('0',MID(a.caja,1,1))          AS sucursal,
							a.fecha          AS fecha,
							a.numero AS numa,
							a.numero AS final,
							a.caja,
							' '       AS nfiscal,
							' '       AS nhfiscal,
							a.serial  AS referen,
							' '       AS planilla,
							c.cod_tar AS clipro,
							CONCAT(c.nombres,' ', c.apellidos) AS nombre,
							'CO'                               AS contribu,
							a.rifci                            AS rif,
							'01'                               AS registro,
							'S'                                AS nacional,
							a.exento,
							a.baseg,
							a.ivag AS impug,
							a.basea,
							a.ivaa AS impua,
							a.baser,
							a.ivar AS impur,
							a.baseg+a.basea+a.baser AS stotal,
							a.ivag+a.ivar+a.ivaa    AS impuesto,
							a.total AS gtotal,
							0       AS reiva,
							${mes}01     AS fechal,
							0            AS fafecta,
							a.hora,
							${dbcierrez} AS cierrez
						FROM sfacfis AS a
						LEFT JOIN viefac AS b ON a.fecha=b.fecha AND a.cajero=b.cajero AND a.referencia=b.numero
						LEFT JOIN club c ON b.cliente = c.cod_tar
						WHERE
						a.numero>${dbffdesde} AND a.numero<=${dbffhasta} AND a.serial=${dbserial}
						AND a.rifci REGEXP '^[VEJG][0-9]{9}$'
						AND a.tipo_doc='FC'";
						$flag=$CI->db->simple_query($mSQL_1);
						if($flag==false){
							memowrite('2'.$mSQL_1,'genesfacfiscalpdv');
						}
					}

					if($nchasta>$ncdesde){
						$mSQL_1 = $emSQL."SELECT
							 NULL  AS id,
							'V' AS libro,
							IF(a.tipo_doc='NC','NC','FC') AS tipo_doc,
							'FP'                                 AS fuente,
							CONCAT('0',MID(a.caja,1,1))          AS sucursal,
							a.fecha          AS fecha,
							a.numero AS numa,
							a.numero AS final,
							a.caja,
							' '       AS nfiscal,
							' '       AS nhfiscal,
							a.serial  AS referen,
							' '       AS planilla,
							c.cod_tar AS clipro,
							CONCAT(c.nombres,' ', c.apellidos) AS nombre,
							'CO'                               AS contribu,
							c.cedula                           AS rif,
							'01'                               AS registro,
							'S'                                AS nacional,
							(-1)*a.exento,
							(-1)*a.baseg,
							(-1)*a.ivag AS impug,
							(-1)*a.basea,
							(-1)*a.ivaa AS impua,
							(-1)*a.baser,
							(-1)*a.ivar AS impur,
							(-1)*(a.baseg+a.basea+a.baser) AS stotal,
							(-1)*(a.ivag+a.ivar+a.ivaa)    AS impuesto,
							(-1)*a.total AS gtotal,
							0       AS reiva,
							${mes}01     AS fechal,
							0            AS fafecta,
							a.hora,
							${dbcierrez} AS cierrez
						FROM sfacfis AS a
						LEFT JOIN viefac AS b ON a.fecha=b.fecha AND a.cajero=b.cajero AND a.referencia=b.numero
						LEFT JOIN club c ON b.cliente = c.cod_tar
						#LEFT JOIN club c ON a.rifci = c.cedula
						WHERE
						a.numero>${dbncdesde} AND a.numero<=${dbnchasta} AND a.serial=${dbserial}
						AND c.cedula REGEXP '^[VEJG][0-9]{9}$'
						AND a.tipo_doc='NC' GROUP BY c.cedula";
						$flag=$CI->db->simple_query($mSQL_1);
						if($flag==false){
							memowrite('3'.$mSQL_1,'genesfacfiscalpdv');
						}
					}
				}

				$NC=$FC=false;

				$mSQL_2="SELECT tipo,
					SUM(exento)    AS exento,
					SUM(general)   AS baseg,
					SUM(reducida)  AS baser,
					SUM(adicional) AS basea,
					SUM(geneimpu)  AS ivag,
					SUM(reduimpu)  AS ivar,
					SUM(adicimpu)  AS ivaa
				FROM siva WHERE  numero>${dbffdesde} AND numero<=${dbffhasta}  AND serial=$dbserial AND tipo='FC' AND fuente='FP'
				GROUP BY tipo";
				$query_2 = $CI->db->query($mSQL_2);
				$cant=$query_2->num_rows();

				if ($query_2->num_rows() > 0){
					$rrow = $query_2->row();
					$FC=true;
					$data['nombre']    = 'VENTAS A NO CONTRIBUYENTE';
					$data['exento']    = $row->exento-$rrow->exento;
					$data['general']   = $row->base  -$rrow->baseg;
					$data['reducida']  = $row->base1 -$rrow->baser;
					$data['adicional'] = $row->base2 -$rrow->basea;
					$data['geneimpu']  = $row->iva   -$rrow->ivag;
					$data['reduimpu']  = $row->iva1  -$rrow->ivar;
					$data['adicimpu']  = $row->iva2  -$rrow->ivaa;
					$data['numhasta']  = $ffhasta;
					$data['numero']    = str_pad($ffdesde+1,8,'0',STR_PAD_LEFT);
					$data['hora']      = '23:59:59';

					$data['contribu'] = 'NO';
					$data['stotal']   =$data['exento']+$data['general']+$data['reducida']+$data['adicional'];
					$data['impuesto'] =$data['geneimpu']+$data['reduimpu']+$data['adicimpu'];
					$data['gtotal']   =$data['stotal']+$data['impuesto'];

					if($data['gtotal']!=0){
						$mmSQL =$CI->db->insert_string('siva', $data);
						$flag=$CI->db->simple_query($mmSQL);
						if(!$flag){memowrite('4'.$mmSQL,'genesfacfiscalpdv'); return 0; };
					}
				}

				$mSQL_2="SELECT tipo,
					SUM(exento)    AS exento,
					SUM(general)   AS baseg,
					SUM(reducida)  AS baser,
					SUM(adicional) AS basea,
					SUM(geneimpu)  AS ivag,
					SUM(reduimpu)  AS ivar,
					SUM(adicimpu)  AS ivaa
				FROM siva WHERE  numero>${dbncdesde} AND numero<=${dbnchasta}  AND serial=$dbserial AND tipo='NC' AND fuente='FP'
				GROUP BY tipo";
				$query_2 = $CI->db->query($mSQL_2);
				$cant=$query_2->num_rows();

				if ($query_2->num_rows() > 0){
					$rrow = $query_2->row();

					$NC=true;
					$data['nombre']    = 'NOTAS DE CREDITO A NO CONTRIBUYENTE';
					$data['exento']    = (-1)*($row->ncexento+$rrow->exento);
					$data['general']   = (-1)*($row->ncbase  +$rrow->baseg);
					$data['reducida']  = (-1)*($row->ncbase1 +$rrow->baser);
					$data['adicional'] = (-1)*($row->ncbase2 +$rrow->basea);
					$data['geneimpu']  = (-1)*($row->nciva   +$rrow->ivag);
					$data['reduimpu']  = (-1)*($row->nciva1  +$rrow->ivar);
					$data['adicimpu']  = (-1)*($row->nciva2  +$rrow->ivaa);
					$data['numhasta']  = $nchasta;
					$data['numero']    = str_pad($ncdesde+1,8,'0',STR_PAD_LEFT);
					$data['hora']      = '23:59:59';

					$data['contribu'] = 'NO';
					$data['stotal']   =$data['exento']+$data['general']+$data['reducida']+$data['adicional'];
					$data['impuesto'] =$data['geneimpu']+$data['reduimpu']+$data['adicimpu'];
					$data['gtotal']   =$data['stotal']+$data['impuesto'];

					if($data['gtotal']!=0){
						$mmSQL =$CI->db->insert_string('siva', $data);
						$flag=$CI->db->simple_query($mmSQL);
						if(!$flag){memowrite('4'.$mmSQL,'genesfacfiscalpdv'); return 0; };
					}
				}

				//Si no hay ventas a contribuyente
				if(!$NC){ //$NC=false;
					$data['nombre']    = 'NOTAS DE CREDITO A NO CONTRIBUYENTE';
					$data['exento']    = (-1)*($row->ncexento);
					$data['general']   = (-1)*($row->ncbase  );
					$data['reducida']  = (-1)*($row->ncbase1 );
					$data['adicional'] = (-1)*($row->ncbase2 );
					$data['geneimpu']  = (-1)*($row->nciva   );
					$data['reduimpu']  = (-1)*($row->nciva1  );
					$data['adicimpu']  = (-1)*($row->nciva2  );
					$data['numhasta']  = $row->ncnumero;
					$data['numero']    = str_pad($ncdesde+1,8,'0',STR_PAD_LEFT);
					$data['hora']      = '23:59:59';

					$data['contribu'] = 'NO';
					$data['stotal']   =$data['exento']+$data['general']+$data['reducida']+$data['adicional'];
					$data['impuesto'] =$data['geneimpu']+$data['reduimpu']+$data['adicimpu'];
					$data['gtotal']   =$data['stotal']+$data['impuesto'];

					if($data['gtotal']!=0){
						$mmSQL =$CI->db->insert_string('siva', $data);
						$flag=$CI->db->simple_query($mmSQL);
						if($flag ==false ){ memowrite('5'.$mmSQL,'genesfacfiscalpdv'); return 0; };
					}
				}
				if(!$FC){ //$FC=false;
					$data['nombre']    = 'VENTAS A NO CONTRIBUYENTE';
					$data['exento']    = $row->exento;
					$data['general']   = $row->base  ;
					$data['reducida']  = $row->base1 ;
					$data['adicional'] = $row->base2 ;
					$data['geneimpu']  = $row->iva   ;
					$data['reduimpu']  = $row->iva1  ;
					$data['adicimpu']  = $row->iva2  ;
					$data['numhasta']  = $row->factura;
					$data['numero']    = str_pad($ffdesde+1,8,'0',STR_PAD_LEFT);
					$data['hora']      = '23:59:59';

					$data['contribu'] = 'NO';
					$data['stotal']   =$data['exento']+$data['general']+$data['reducida']+$data['adicional'];
					$data['impuesto'] =$data['geneimpu']+$data['reduimpu']+$data['adicimpu'];
					$data['gtotal']   =$data['stotal']+$data['impuesto'];

					if($data['gtotal']!=0){
						$mmSQL =$CI->db->insert_string('siva', $data);
						$flag=$CI->db->simple_query($mmSQL);
						if(!$flag){memowrite('6'.$mmSQL,'genesfacfiscalpdv'); return 0; };
					}
				}
			}
		}
	}

	static function genesfmay($mes) {
		$CI =& get_instance();
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		// BORRA LA GENERADA ANTERIOR
		$CI->db->simple_query("DELETE FROM siva WHERE EXTRACT(YEAR_MONTH FROM fechal) = $mes AND fuente='FA' ");
		// ARREGLA LAS TASAS NULAS EN SFAC
		$CI->db->simple_query("UPDATE sfac SET tasa=0, montasa=0, reducida=0, monredu=0, sobretasa=0, monadic=0, exento=0 WHERE (tasa IS NULL OR montasa IS NULL) AND EXTRACT(YEAR_MONTH FROM fecha)=$mes ");
		// Arregla las factras malas
		$query = $CI->db->query("SELECT transac FROM sfac WHERE abs(exento+montasa+monredu+monadic-totals)>0.2 AND EXTRACT(YEAR_MONTH FROM fecha)=$mes ");
		if ($query->num_rows() > 0)
			foreach ( $query->result() AS $row ) $CI->_arreglatasa($row->transac);

		// ARREGLA LAS QUE TIENEN UNA SOLA TASA
		$mSQL = "UPDATE sfac SET tasa=iva, montasa=totals
			WHERE reducida=0 AND sobretasa=0 AND exento=0 AND EXTRACT(YEAR_MONTH FROM fecha)=$mes";
		$CI->db->simple_query($mSQL);
		$tasas=$CI->_tasas($mes);
		$mTASA=$tasas['general'];

		//Ventas al mayor
		$mSQL = "INSERT INTO siva
			(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
			referen, planilla, clipro, nombre, contribu, rif, registro,
			nacional, exento, general, geneimpu,
			adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
			gtotal, reiva, fechal, fafecta)
			SELECT 0 AS id,
			'V' AS libro,
			IF(b.tipo='D','NC','FC') AS tipo,
			'FA' AS fuente,
			'00' AS sucursal,
			b.fecha,
			b.numero,
			' ' AS numhasta,
			' ' AS caja,
			b.nfiscal,
			'  ' AS nhfiscal,
			'' AS referen,
			'  ' AS planilla,
			b.cod_cli AS clipro,
			b.nombre AS nombre,
			'CO' AS contribu,
			c.rifci,
			'01' AS registro,
			'S' AS nacional,
			SUM(IF(a.iva=0,1,0)*a.importe) AS exento,
			SUM(IF(a.iva=$mTASA,1,0)*a.importe) AS general,
			SUM(IF(a.iva=$mTASA,1,0)*a.importe*a.iva/100) AS geneimpu,
			SUM(IF(a.iva>$mTASA,1,0)*a.importe)  AS adicional,
			SUM(IF(a.iva>$mTASA,1,0)*a.importe*a.iva/100) AS adicimpu,
			SUM(IF(a.iva<$mTASA AND a.iva>0,1,0)*a.importe) AS reducida,
			SUM(IF(a.iva<$mTASA AND a.iva>0,1,0)*a.importe*a.iva/100) AS reduimpu,
			b.stotal AS stotal,
			b.impuesto AS impuesto,
			b.gtotal AS gtotal,
			0 AS reiva,
			".$mes."01 AS fechal,
			0 AS fafecta
			FROM itfmay AS a JOIN fmay AS b ON a.numero=b.numero AND a.fecha=b.fecha
			LEFT JOIN scli AS c ON b.cod_cli=c.cliente
			WHERE b.fecha BETWEEN $fdesde AND $fhasta AND b.tipo!='A'
			GROUP BY a.fecha,a.numero ";
		$flag=$CI->db->simple_query($mSQL);
		if(!$flag) memowrite($mSQL,'genesfmay');

		$mSQL = "INSERT INTO siva
			(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
			referen, planilla, clipro, nombre, contribu, rif, registro,
			nacional, exento, general, geneimpu,
			adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
			gtotal, reiva, fechal, fafecta)
			SELECT 0 AS id,
			'V' AS libro,
			IF(b.tipo='D','NC','FC') AS tipo,
			'FA' AS fuente,
			'00' AS sucursal,
			b.fecha,
			b.numero,
			' ' AS numhasta,
			' ' AS caja,
			b.nfiscal,
			'  ' AS nhfiscal,
			'' AS referen,
			'  ' AS planilla,
			b.cod_cli AS clipro,
			b.nombre AS nombre,
			'CO' AS contribu,
			'ANULADA' AS rifci,
			'01' AS registro,
			'S' AS nacional,
			0 AS exento,
			0 AS general,
			0 AS geneimpu,
			0 AS adicional,
			0 AS adicimpu,
			0 AS reducida,
			0 AS reduimpu,
			0 AS stotal,
			0 AS impuesto,
			0 AS gtotal,
			0 AS reiva,
			".$mes."01 AS fechal,
			0 AS fafecta
			FROM itfmay AS a JOIN fmay AS b ON a.numero=b.numero AND a.fecha=b.fecha
			LEFT JOIN scli AS c ON b.cod_cli=c.cliente
			WHERE EXTRACT(YEAR_MONTH FROM b.fecha)=$mes AND b.tipo='A'
			GROUP BY a.fecha,a.numero ";
		//$CI->db->simple_query($mSQL);
	}
}
