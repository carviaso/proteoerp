<?php
class compras{
	//Libro de compras contribuyente normal
	function wlcexcel($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$this->db->simple_query($mSQL);

		$aa = $this->datasis->ivaplica($mes.'02');
		$tasa      = $aa['tasa'];
		$redutasa  = $aa['redutasa'];
		$sobretasa = $aa['sobretasa'];

		if($this->db->field_exists('serie', 'siva') && $this->db->field_exists('serie', 'siva')){
			$dbcampo='COALESCE(a.serie,a.numero)';
		}else{
			$dbcampo='a.numero';
		}

		$mSQL = "SELECT DISTINCT a.sucursal, a.fecha, a.rif,
		    if(e.proveed IS NULL, if(d.nombre IS NULL or d.nombre='', a.nombre, d.nombre ), if(e.nombre IS NULL or e.nombre='', a.nombre, if(e.nomfis='',e.nombre,e.nomfis)) ) nombre,
		    a.contribu,
		    a.referen,
		    a.planilla,
		    '     ' nose,
		    IF(a.tipo='FC',${dbcampo},'        ') numero,
		    a.nfiscal,
		    IF(a.tipo='ND',${dbcampo},'        ') numnd,
		    IF(a.tipo='NC',${dbcampo},'        ') numnc,
		    a.registro oper,
		    '        ' compla,
		    SUM(a.gtotal   *IF(a.tipo='NC',-1,1)) gtotal,
		    SUM(a.exento   *IF(a.tipo='NC',-1,1)) exento,
		    SUM(a.general  *IF(a.tipo='NC',-1,1)) general,
		    SUM(a.geneimpu *IF(a.tipo='NC',-1,1)) geneimpu,
		    SUM(a.adicional*IF(a.tipo='NC',-1,1)) adicional,
		    SUM(a.adicimpu *IF(a.tipo='NC',-1,1)) adicimpu,
		    SUM(a.reducida *IF(a.tipo='NC',-1,1)) reducida,
		    SUM(a.reduimpu *IF(a.tipo='NC',-1,1)) reduimpu,
		    0 reiva,
		    ' ' nrocomp,
		    NULL AS emision,
		    ${dbcampo} numo, a.tipo tipo_doc, SUM(a.impuesto) AS impuesto, a.nacional,a.afecta
		    FROM siva AS a LEFT JOIN provoca AS d ON a.rif=d.rif
		                   LEFT JOIN sprv AS e ON a.clipro=e.proveed
		    WHERE libro='C' AND fechal BETWEEN ${fdesde} AND ${fhasta} AND a.fecha>0
		    GROUP BY a.fecha,a.tipo,numo,a.rif
		UNION
		    SELECT DISTINCT a.sucursal,
		    b.emision AS fecha,
		    d.rif,
		    IF(MID(b.transac,1,1)='_','ANULADO',d.nomfis) nombre,
		    a.contribu,
		    a.referen,
		    a.planilla,'  ' aaa,
		    '*       ' numero,
		    a.nfiscal,
		    '        ' numnd,
		    '        ' numnc,
		    a.registro oper,
		    a.referen,
		    a.gtotal   * 0,
		    a.exento   * 0,
		    a.general  * 0,
		    a.geneimpu * 0,
		    a.adicional* 0,
		    a.adicimpu * 0,
		    a.reducida * 0,
		    a.reduimpu * 0,
		    SUM(b.reiva*IF(a.tipo='NC',-1,1)*(MID(b.transac,1,1)<>'_')) reiva,
		    ' ' nrocomp,
		    b.emision, CONCAT(EXTRACT(YEAR_MONTH FROM fechal),b.nrocomp) numo, 'CR' AS tipo_doc,SUM(a.impuesto) AS impuesto, a.nacional,$dbcampo AS afecta
		    FROM siva AS a JOIN riva AS b ON a.numero=b.numero AND a.tipo=b.tipo_doc AND a.reiva=b.reiva
		              LEFT JOIN sprv AS d ON b.clipro=d.proveed
		    WHERE libro='C' AND fechal BETWEEN ${fdesde} AND ${fhasta} AND a.fecha>0 AND a.reiva>0
		    GROUP BY a.fecha,a.tipo,numo,a.rif
		    ORDER BY fecha,numo ";

		$fname = tempnam('/tmp','lcompras.xls');
		$this->load->library('workbook', array('fname'=>$fname));
		$wb = & $this->workbook ;
		$ws = & $wb->addworksheet($mes);

		# ANCHO DE LAS COLUMNAS
		$ws->set_column('A:A',6);
		$ws->set_column('B:B',10);
		$ws->set_column('C:C',6);
		$ws->set_column('D:E',10);
		$ws->set_column('F:F',37);
		$ws->set_column('G:G',14);
		$ws->set_column('H:W',12);
		$ws->set_column('X:X',25);

		// FORMATOS
		$h       =& $wb->addformat(array( "bold" => 1, "size" => 16, "merge" => 1));
		$h1      =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'center'));
		$h2      =& $wb->addformat(array( "bold" => 1, "size" => 14, "align" => 'left', "fg_color" => 'silver'  ));
		$h3      =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'right'));

		$titulo  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 0, "fg_color" => 'silver' ));
		$cuerpo  =& $wb->addformat(array( "size" => 9 ));
		$numero  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));

		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = "LIBRO DE COMPRAS CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL ".substr($mes,0,4)."   ".$mes;

		$ws->write(1, 0, $this->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$this->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<26; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};

		// TITULOS
		$mm=6;
		$ws->write_string( $mm,   0, "", $titulo );
		$ws->write_string( $mm+1, 0, "Oper.", $titulo );
		$ws->write_string( $mm+2, 0, "Nro.", $titulo );
		$ws->write_string( $mm,   1, "", $titulo );
		$ws->write_string( $mm+1, 1, "Fecha", $titulo );
		$ws->write_string( $mm+2, 1, "", $titulo );
		$ws->write_string( $mm,   2, "Documento", $titulo );
		$ws->write_string( $mm+1, 2, "Tipo",      $titulo );
		$ws->write_string( $mm+2, 2, "", $titulo );
		$ws->write_blank( $mm,    3,  $titulo );
		$ws->write_string( $mm+1, 3, "Numero",      $titulo );
		$ws->write_string( $mm+2, 3, "", $titulo );
		$ws->write_string( $mm,   4, "Numero de",    $titulo );
		$ws->write_string( $mm+1, 4, "Declaracion",  $titulo );
		$ws->write_string( $mm+2, 4, "de Aduanas",      $titulo );
		$ws->write_string( $mm,    5, "Nombre, Razon Social", $titulo );
		$ws->write_string( $mm+1,  5, "o", $titulo );
		$ws->write_string( $mm+2,  5, "Denominacion del Proveedor", $titulo );
		$ws->write_string( $mm,    6, "Numero", $titulo );
		$ws->write_string( $mm+1,  6, "de", $titulo );
		$ws->write_string( $mm+2,  6, "R.I.F.", $titulo );
		$ws->write_string( $mm,    7, "Total Compras", $titulo );
		$ws->write_string( $mm+1,  7, "incluyendo", $titulo );
		$ws->write_string( $mm+2,  7, "el I.V.A.", $titulo );
		$ws->write_string( $mm,    8, "Compras No", $titulo );
		$ws->write_string( $mm+1,  8, "Gravadas o sin", $titulo );
		$ws->write_string( $mm+2,  8, "derecho a C.F.", $titulo );
		$ws->write_string( $mm,    9, "Compras de Importacion Gravadas", $titulo );
		$ws->write_string( $mm+1,  9, "Alicuota General", $titulo );
		$ws->write_string( $mm+2,  9, "Base", $titulo );
		$ws->write_blank( $mm,    10, $titulo );
		$ws->write_blank( $mm+1,  10, $titulo );
		$ws->write_string( $mm+2, 10, "Impuesto", $titulo );
		$ws->write_blank( $mm,    11, $titulo );
		$ws->write_string( $mm+1, 11, "Alicuota Adicional",$titulo );
		$ws->write_string( $mm+2, 11, "Base", $titulo );
		$ws->write_blank( $mm,    12, $titulo );
		$ws->write_blank( $mm+1,  12, $titulo );
		$ws->write_string( $mm+2, 12, "Impuesto", $titulo );
		$ws->write_blank( $mm,    13, $titulo );
		$ws->write_string( $mm+1, 13, "Alicuota Reducida",$titulo );
		$ws->write_string( $mm+2, 13, "Base", $titulo );
		$ws->write_blank( $mm,    14, $titulo );
		$ws->write_blank( $mm+1,  14, $titulo );
		$ws->write_string( $mm+2, 14, "Impuesto", $titulo );
		$ws->write_string( $mm,   15, "Compras Internas Gravadas o con derecho a Credito Fiscal", $titulo );
		$ws->write_string( $mm+1, 15, "Alicuota General", $titulo );
		$ws->write_string( $mm+2, 15, "Base", $titulo );
		$ws->write_blank( $mm,    16, $titulo );
		$ws->write_blank( $mm+1,  16, $titulo );
		$ws->write_string( $mm+2, 16, "Impuesto", $titulo );
		$ws->write_blank( $mm,    17, $titulo );
		$ws->write_string( $mm+1, 17, "Alicuota Adicional",$titulo );
		$ws->write_string( $mm+2, 17, "Base", $titulo );
		$ws->write_blank( $mm,    18, $titulo );
		$ws->write_blank( $mm+1,  18, $titulo );
		$ws->write_string( $mm+2, 18, "Impuesto", $titulo );
		$ws->write_blank( $mm,    19, $titulo );
		$ws->write_string( $mm+1, 19, "Alicuota Reducida",$titulo );
		$ws->write_string( $mm+2, 19, "Base", $titulo );
		$ws->write_blank( $mm,    20, $titulo );
		$ws->write_blank( $mm+1,  20, $titulo );
		$ws->write_string( $mm+2, 20, "Impuesto", $titulo );
		$ws->write_string( $mm,   21, "Ajuste a los", $titulo );
		$ws->write_string( $mm+1, 21, "Creditos F.", $titulo );
		$ws->write_string( $mm+2, 21, "P. Anteriores", $titulo );
		$ws->write_string( $mm,   22, "I.V.A.", $titulo );
		$ws->write_string( $mm+1, 22, "Retenido al", $titulo );
		$ws->write_string( $mm+2, 22, "Vendedor", $titulo );
		$ws->write_string( $mm,   23, "Numero", $titulo );
		$ws->write_string( $mm+1, 23, "de", $titulo );
		$ws->write_string( $mm+2, 23, "Comprobante", $titulo );
		$ws->write_string( $mm,   24, " ", $titulo );
		$ws->write_string( $mm+1, 24, "Factura", $titulo );
		$ws->write_string( $mm+2, 24, "Afectada", $titulo );
		$ws->write_string( $mm,   25, " ", $titulo );
		$ws->write_string( $mm+1, 25, "Numero", $titulo );
		$ws->write_string( $mm+2, 25, "Fiscal", $titulo );

		$mm++;
		$mm++;
		$mm++;
		$ii = 1;
		$mtiva = 'X';
		$mfecha = '2000-00-00';
		$tventas = $texenta = $tbase = $timpue = $treiva = $tperci = 0 ;
		$dd=$mm;  // desde

		$mc = $this->db->query($mSQL);
		if ( $mc->num_rows() > 0 ) {
			foreach( $mc->result() as $row ) {
				$ws->write_string( $mm,  0, $ii, $cuerpo );
				$ws->write_string( $mm,  1, substr($row->fecha,8,2)."/".substr($row->fecha,5,2)."/".substr($row->fecha,0,4), $cuerpo );
				$ws->write_string( $mm,  2, $row->tipo_doc,  $cuerpo );
				$ws->write_string( $mm,  3, $row->numo,  $cuerpo );
				$ws->write_string( $mm,  4, $row->planilla,  $cuerpo );
				$ws->write_string( $mm,  5, $row->nombre,  $cuerpo );
				$ws->write_string( $mm,  6, $row->rif,  $cuerpo );
				if ($row->oper != '04' ){
					$ws->write_number( $mm,  7, $row->gtotal, $numero );
					$ws->write_number( $mm,  8, $row->exento, $numero );
					if($row->nacional=='N') $desp=9; else $desp=15;
					$ws->write_number( $mm, $desp, $row->general, $numero );
					$ws->write_number( $mm, $desp+1, $row->geneimpu, $numero );
					$ws->write_number( $mm, $desp+2, $row->adicional, $numero );
					$ws->write_number( $mm, $desp+3, $row->adicimpu, $numero );
					$ws->write_number( $mm, $desp+4, $row->reducida, $numero );
					$ws->write_number( $mm, $desp+5, $row->reduimpu, $numero );
				} else {
					$ws->write_number( $mm,  7, 0, $numero );
					$ws->write_number( $mm,  8, 0, $numero );
					$ws->write_number( $mm, 15, 0, $numero );
					$ws->write_number( $mm, 16, 0, $numero );
					$ws->write_number( $mm, 17, 0, $numero );
					$ws->write_number( $mm, 18, 0, $numero );
					$ws->write_number( $mm, 19, 0, $numero );
					$ws->write_number( $mm, 20, 0, $numero );
					$ws->write_number( $mm, 21, $row->impuesto, $numero );
				}
				$ws->write_number( $mm, 22, $row->reiva, $numero );
				$ws->write_string( $mm, 23, $row->nrocomp, $cuerpo );
				$ws->write_string( $mm, 24, $row->afecta, $cuerpo );
				$ws->write_string( $mm, 25, $row->nfiscal, $cuerpo );
				$mm++;
				$ii++;
			}
		}

		$celda = $mm+1;
		$fventas = "=G$celda";   // VENTAS
		$fexenta = "=H$celda";   // VENTAS EXENTAS
		$fbase   = "=P$celda";   // BASE IMPONIBLE
		$fiva    = "=Q$celda";   // I.V.A.

		$ws->write( $mm, 0,"Totales...",  $Tnumero );
		$ws->write_blank( $mm,  1,  $Tnumero );
		$ws->write_blank( $mm,  2,  $Tnumero );
		$ws->write_blank( $mm,  3,  $Tnumero );
		$ws->write_blank( $mm,  4,  $Tnumero );
		$ws->write_blank( $mm,  5,  $Tnumero );
		$ws->write_blank( $mm,  6,  $Tnumero );

		$ws->write_formula( $mm,  7, "=SUM(H$dd:H$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,  8, "=SUM(I$dd:I$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,  9, "=SUM(J$dd:J$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 10, "=SUM(K$dd:K$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm, 11, "=SUM(L$dd:L$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm, 12, "=SUM(M$dd:M$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 13, "=SUM(N$dd:N$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm, 14, "=SUM(O$dd:O$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm, 15, "=SUM(P$dd:P$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 16, "=SUM(Q$dd:Q$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm, 17, "=SUM(R$dd:R$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm, 18, "=SUM(S$dd:S$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 19, "=SUM(T$dd:T$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm, 20, "=SUM(U$dd:U$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm, 21, "=SUM(V$dd:V$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 22, "=SUM(W$dd:W$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_blank( $mm,  23,  $Tnumero );
		$ws->write_blank( $mm,  24,  $Tnumero );
		$ws->write_blank( $mm,  25,  $Tnumero );

		$mm ++;
		$mm ++;
		$ws->write_string($mm, 1, 'RESUMEN DE COMPRAS Y CREDITOS', $h2 );
		$ws->write_blank($mm, 2, $titulo );
		$ws->write_blank($mm, 3, $titulo );
		$ws->write_blank($mm, 4, $titulo );
		$ws->write_blank($mm, 5, $titulo );
		$ws->write($mm, 6, 'Items', $titulo );
		$ws->write($mm, 7, 'Base Imponible', $titulo );
		$ws->write($mm, 8, 'Items', $titulo );
		$ws->write($mm, 9, 'Credito Fiscal', $titulo );

		$mm ++;
		$ws->write($mm, 5, 'Total Compras no Gravadas o sin Derecho a Credito Fiscal', $h3 );
		$ws->write_blank($mm, 2, $h1 );
		$ws->write_blank($mm, 3, $h1 );
		$ws->write_blank($mm, 4, $h1 );
		//$ws->write_blank($mm, 5, $h1 );
		$ws->write($mm, 6, '30', $h1 );
		$ws->write_formula($mm, 7, "=I$celda" , $Rnumero );
		//$ws->write($mm, 8, '30', $h1 );
		//$ws->write_formula($mm, 7, "=H$celda" , $Rnumero );

		$mm ++;
		$mTot = $mm;
		$ws->write($mm, 5, 'Total Importaciones Gravadas por Alicuota General', $h3 );
		$ws->write_blank($mm, 2, $h1 );
		$ws->write_blank($mm, 3, $h1 );
		$ws->write_blank($mm, 4, $h1 );
		//$ws->write_blank($mm, 5, $h1 );
		$ws->write($mm, 6, '31', $h1 );
		$ws->write_formula($mm, 7, "=J$celda" , $Rnumero );
		$ws->write($mm, 8, '32', $h1 );
		$ws->write_formula($mm, 9, "=K$celda" , $Rnumero );

		$mm ++;
		$ws->write($mm, 5, 'Total Importaciones Gravadas por Alicuota General mas Adicional', $h3 );
		$ws->write_blank($mm, 2, $h1 );
		$ws->write_blank($mm, 3, $h1 );
		$ws->write_blank($mm, 4, $h1 );
		//$ws->write_blank($mm, 5, $h1 );
		$ws->write($mm, 6, '312', $h1 );
		$ws->write_formula($mm, 7, "=L$celda" , $Rnumero );
		$ws->write($mm, 8, '322', $h1 );
		$ws->write_formula($mm, 9, "=M$celda" , $Rnumero );

		$mm ++;
		$ws->write($mm, 5, 'Total Importaciones Gravadas por Alicuota Reducida', $h3 );
		$ws->write_blank($mm, 2, $h1 );
		$ws->write_blank($mm, 3, $h1 );
		$ws->write_blank($mm, 4, $h1 );
		//$ws->write_blank($mm, 5, $h1 );
		$ws->write($mm, 6, '313', $h1 );
		$ws->write_formula($mm, 7, "=N$celda" , $Rnumero );
		$ws->write($mm, 8, '323', $h1 );
		$ws->write_formula($mm, 9, "=O$celda" , $Rnumero );

		$mm ++;
		$ws->write($mm, 5, 'Total Compras Internas Gravadas por Alicuota General', $h3 );
		$ws->write_blank($mm, 2, $h1 );
		$ws->write_blank($mm, 3, $h1 );
		$ws->write_blank($mm, 4, $h1 );
		//$ws->write_blank($mm, 5, $h1 );
		$ws->write($mm, 6, '33', $h1 );
		$ws->write_formula($mm, 7, "=P$celda" , $Rnumero );
		$ws->write($mm, 8, '34', $h1 );
		$ws->write_formula($mm, 9, "=Q$celda" , $Rnumero );

		$mm ++;
		$ws->write($mm, 5, 'Total Compras Internas Gravadas por Alicuota General mas Adicional', $h3 );
		$ws->write_blank($mm, 2, $h1 );
		$ws->write_blank($mm, 3, $h1 );
		$ws->write_blank($mm, 4, $h1 );
		//$ws->write_blank($mm, 5, $h1 );
		$ws->write($mm, 6, '332', $h1 );
		$ws->write_formula($mm, 7, "=R$celda" , $Rnumero );
		$ws->write($mm, 8, '342', $h1 );
		$ws->write_formula($mm, 9, "=S$celda" , $Rnumero );

		$mm ++;
		$ws->write($mm, 5, 'Total Compras Internas Gravadas por Alicuota Reducida', $h3 );
		$ws->write_blank($mm, 2, $h1 );
		$ws->write_blank($mm, 3, $h1 );
		$ws->write_blank($mm, 4, $h1 );
		//$ws->write_blank($mm, 5, $h1 );
		$ws->write($mm, 6, '333', $h1 );
		$ws->write_formula($mm, 7, "=T$celda" , $Rnumero );
		$ws->write($mm, 8, '343', $h1 );
		$ws->write_formula($mm, 9, "=U$celda" , $Rnumero );

		$mm1=$mm;
		$mm ++;
		$ws->write($mm, 1, 'Total Compras y Creditos para efectos de determinacion', $h2 );
		$ws->write_blank($mm, 2, $h2 );
		$ws->write_blank($mm, 3, $h2 );
		$ws->write_blank($mm, 4, $h2 );
		$ws->write_blank($mm, 5, $h2 );
		$ws->write($mm, 6, '35', $titulo );
		$ws->write_formula($mm, 7, "=SUM(H$mTot:H$mm)" , $Tnumero );
		$ws->write($mm, 8, '36', $titulo );
		$ws->write_formula($mm, 9, "=SUM(J$mTot:J$mm)" , $Tnumero );

		$wb->close();
		header("Content-type: application/x-msexcel; name=\"lcompras.xls\"");
		header("Content-Disposition: inline; filename=\"lcompras.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
	}

	//***********************************************
	// GENERACION
	//***********************************************

	function genecompras($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		//Procesando Compras scst
		$this->db->simple_query('UPDATE scst SET montasa=0, tasa =0     WHERE montasa IS NULL');
		$this->db->simple_query('UPDATE scst SET monredu=0, reducida=0  WHERE monredu IS NULL');
		$this->db->simple_query('UPDATE scst SET monadic=0, sobretasa=0 WHERE monadic IS NULL');

		$this->db->simple_query("DELETE FROM siva WHERE EXTRACT(YEAR_MONTH FROM fechal) = $mes AND fuente='CP' ");
		$sql="UPDATE scst SET
		cexento=null,cgenera=null, civagen=null,cadicio=null, civaadi=null,creduci=null,civared=null,cstotal=null, cimpuesto=null,ctotal=null
		WHERE (cexento+cgenera+civagen+cadicio+civaadi+creduci+civared+cstotal+cimpuesto+ctotal=0 OR
		       cexento+cgenera+civagen+cadicio+civaadi+creduci+civared+cstotal+cimpuesto+ctotal IS NULL) AND recep BETWEEN $fdesde AND $fhasta";
		$this->db->simple_query($sql);

		// REVISAR COMPRAS
		$query = $this->db->query("SELECT control FROM scst WHERE abs(exento+montasa+monredu+monadic-montotot)>0.1 AND EXTRACT(YEAR_MONTH FROM fecha)=$mes ");

		// Procesando Compra
		if ($query->num_rows() > 0) foreach ($query->result() as $row) $this->scstarretasa( $row->control );
		// VER LO DE FACTURAS RECIBIDAS CON FECHA ANTERIOR
		//$mFECHAA = $this->datasis->dameval("SELECT fecha FROM civa WHERE fecha < (SELECT MAX(fecha) FROM siva) ORDER BY fecha DESC LIMIT 1");
		$mFECHAF = $this->datasis->dameval("SELECT max(fecha) FROM civa WHERE fecha<=$mes"."01");

		$tasas = $this->_tasas($mes);
		$mivag = $tasas['general'];
		$mivar = $tasas['reducida'];
		$mivaa = $tasas['adicional'];

		$campos = $this->db->list_fields('scst');
		$chsiva = $this->db->field_exists('serie', 'siva');
		if(in_array('serie'  ,$campos) && $chsiva){
			$msqlnum=',IF(LENGTH(b.serie)>0,b.serie,b.numero) AS serie';
			$addcamp=',serie';
		}elseif(in_array('nrorig'  ,$campos) && $chsiva){
			//Utiliza el orgen para el caso de supermercado
			$msqlnum=',IF(TRIM(b.nrorig)<>"",b.nrorig,b.numero) AS serie';
			$addcamp=',serie';
		}else{
			$msqlnum='';
			$addcamp='';
		}

		$mSQL = "INSERT INTO siva
			(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
			referen, planilla, clipro, nombre, contribu, rif, registro,
			nacional, exento, general, geneimpu,
			adicional,reduimpu, reducida,adicimpu,stotal, impuesto,
			gtotal, reiva, fechal, fafecta ${addcamp})
			SELECT 0 AS id,
			'C' AS libro,
			b.tipo_doc AS tipo,
			'CP' AS fuente,
			'00' AS sucursal,
			b.fecha,
			b.numero AS numero,
			' ' AS numhasta,
			' ' AS caja,
			b.nfiscal,
			'  ' AS nhfiscal,
			'        ' AS referen,
			'  ' AS planilla,
			b.proveed AS clipro,
			b.nombre,
			'CO' AS contribu,
			c.rif,
			IF(b.fecha<'${mFECHAF}','04', '01') AS registro,
			'S' AS nacional,
			COALESCE(b.cexento,b.exento)     AS exento,
			COALESCE(b.cgenera,b.montasa)    AS general,
			COALESCE(b.civagen,b.tasa)       AS geneimpu,
			COALESCE(b.cadicio,b.monadic)    AS adicional,
			COALESCE(b.civaadi,b.sobretasa)  AS reduimpu,
			COALESCE(b.creduci,b.monredu)    AS reducida,
			COALESCE(b.civared,b.reducida)   AS adicimpu,
			COALESCE(b.cstotal,b.montotot)   AS stotal,
			COALESCE(b.cimpuesto,b.montoiva) AS impuesto,
			COALESCE(b.ctotal,b.montonet)    AS gtotal,
			b.reteiva AS reiva,
			${mes}01 AS fechal,
			0 fafecta
			$msqlnum
		FROM itscst AS a JOIN scst as b ON a.control=b.control
		LEFT JOIN sprv AS c ON b.proveed=c.proveed
		WHERE b.recep BETWEEN ${fdesde} AND ${fhasta} AND b.actuali >= b.fecha AND c.tiva<>'I' AND b.tipo_doc<>'XX'
		GROUP BY b.control";

		// Procesando Compras scst
		$flag=$this->db->simple_query($mSQL);
		if(!$flag) memowrite($mSQL,'genecompras');

		$iivag=$mivag/100;
		$iivar=$mivar/100;
		$iivaa=$mivaa/100;

		if($this->db->table_exists('ordi')){
			$jjoin = 'LEFT JOIN ordi AS d ON a.control=d.control';
			$dua   = 'd.dua AS planilla';
		}else{
			$jjoin = '';
			$dua   = '\' \' AS planilla';
		}

		//para los productos importados
		$mSQL = "INSERT INTO siva
			(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
			referen, planilla, clipro, nombre, contribu, rif, registro,
			nacional, exento, general, geneimpu,
			adicional,reduimpu, reducida,adicimpu,stotal, impuesto,
			gtotal, reiva, fechal, fafecta)
			SELECT 0 AS id,
			'C' AS libro,
			b.tipo_doc AS tipo,
			'CP' AS fuente,
			'00' AS sucursal,
			b.fecha,
			b.numero,
			' ' AS numhasta,
			' ' AS caja,
			b.nfiscal,
			'  ' AS nhfiscal,
			'        ' AS referen,
			${dua},
			b.proveed AS clipro,
			b.nombre,
			'CO' AS contribu,
			c.rif,
			IF(b.fecha<'${mFECHAF}','04', '01') AS registro,
			'N' AS nacional,
			ROUND(b.exento,2)             AS exento,
			ROUND(b.tasa/${iivag},2)      AS general,
			ROUND(b.tasa,2)               AS geneimpu,
			ROUND(b.sobretasa/${iivaa},2) AS adicional,
			ROUND(b.reducida,2)           AS reduimpu,
			ROUND(b.reducida/${iivar},2)  AS reducida,
			ROUND(b.sobretasa,2)          AS adicimpu,
			b.montotot-b.descu+b.licor    AS stotal,
			b.montoiva                    AS impuesto,
			b.montonet-b.descu            AS gtotal,
			b.reteiva                     AS reiva,
			${mes}01                      AS fechal,
			0 fafecta
		FROM itscst AS a JOIN scst AS b ON a.control=b.control
		LEFT JOIN sprv AS c ON b.proveed=c.proveed
		${jjoin}
		WHERE b.recep BETWEEN ${fdesde} AND ${fhasta} AND b.actuali >= b.fecha AND c.tiva='I'
		GROUP BY b.control";

		$flag=$this->db->simple_query($mSQL);
		if(!$flag) memowrite($mSQL,'genecompras');

		$mSQL = "UPDATE siva SET gtotal=exento+general+geneimpu+adicional+reduimpu+reducida+adicimpu WHERE fuente='CP' AND libro='C' ";
		$this->db->simple_query($mSQL);
	}
}
