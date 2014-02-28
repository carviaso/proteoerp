<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class ventas{
	//Libro de ventas basado en Z
	function wlvcierrez($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');
		$aaa = $this->datasis->ivaplica($mes.'02');
		$tasa      = $aaa['tasa'];
		$redutasa  = $aaa['redutasa'];
		$sobretasa = $aaa['sobretasa'];

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$this->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0";
		$this->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET tipo='FC' WHERE tipo='FE'";
		$this->db->simple_query($mSQL);

		$mSQL="SELECT fecha,registro,referen,nfiscal,nhfiscal,numero,serial,rif,nombre,'' AS debito,'' AS credito,
		tipo,fafecta,exento,gtotal,general,'$tasa' AS tgeneral ,geneimpu,reducida,'$redutasa' AS treducida ,reduimpu,adicional,'$sobretasa' tadicional,adicimpu,0 AS reiva,
		comprobante,'' AS percibido,'' AS importacion,contribu,fuente,IF(tipo='CZ',0,1) AS ord
		FROM siva
		WHERE fechal BETWEEN $fdesde AND $fhasta AND libro='V' AND tipo<>'FA'  AND tipo IN ('FE','FC','NC','CZ')
		ORDER BY fecha, serial,numero,ord,nfiscal";
		//memowrite($mSQL);

		$export = $this->db->query($mSQL);
		$fname = tempnam('/tmp','lventas.xls');
		$this->load->library('workbook',array('fname' => $fname));
		$wb =& $this->workbook;
		$ws =& $wb->addworksheet($mes);

		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:F',11);
		$ws->set_column('G:G',37);
		$ws->set_column('H:H',11);
		$ws->set_column('I:V',20);
		// FORMATOS
		$h       =& $wb->addformat(array( 'bold' => 1, 'size' => 16, 'merge' => 1));
		$h1      =& $wb->addformat(array( 'bold' => 1, 'size' => 11, 'align' => 'left'));
		$titulo  =& $wb->addformat(array( 'bold' => 1, 'size' => 9 , 'merge' => 0, 'fg_color' => 'silver' ));
		$titulom =& $wb->addformat(array( 'bold' => 1, 'size' => 9 , 'merge' => 1, 'fg_color' => 'silver' ));
		$tt      =& $wb->addformat(array( 'size' => 9, 'merge'=> 1 , 'fg_color' => 'silver' ));
		$cuerpo  =& $wb->addformat(array( 'size' => 9 ));
		$cuerpoc =& $wb->addformat(array( 'size' => 9, 'align' => 'center', 'merge' => 1 ));
		$cuerpob =& $wb->addformat(array( 'size' => 9, 'align' => 'center', 'bold'  => 1, 'merge' => 1 ));
		$numero  =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9 ));
		$Tnumero =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9, 'bold' => 1, 'fg_color' => 'silver' ));
		$Rnumero =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9, 'bold' => 1, 'align'    => 'right' ));

		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = 'LIBRO DE VENTAS FISCAL CORRESPONDIENTE AL MES DE '.$anomeses[$nomes].' DEL '.substr($mes,0,4);
		$ws->write(1, 0, $this->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, 'RIF: '.$this->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<23; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};

		$mm=6;
		$mcel = 0;
		// TITULOS
		$ws->write_string( $mm,   $mcel, "", $titulo );
		$ws->write_string( $mm+1, $mcel, "Fecha", $titulo );
		$ws->write_string( $mm+2, $mcel, "", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Identificacion del Documento", $titulo );
		$ws->write_string( $mm+1, $mcel, "Nro. De", $titulo );
		$ws->write_string( $mm+2, $mcel, "CierreZ", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel,  $titulo );
		$ws->write_string( $mm+1, $mcel, "Tipo", $titulo );
		$ws->write_string( $mm+2, $mcel, "Doc.", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, "Contribuyentes", $titulo );
		$ws->write_string( $mm+2, $mcel, "Numero", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, "No Contribuyentes", $titulom );
		$ws->write_string( $mm+2, $mcel, "Inicial", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_blank( $mm+1, $mcel, $titulom );
		$ws->write_string( $mm+2, $mcel, "Final", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Nombre, Razon Social", $titulo );
		$ws->write_string( $mm+1, $mcel, "o Denominacion del ", $titulo );
		$ws->write_string( $mm+2, $mcel, "Comprador", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Numero", $titulo );
		$ws->write_string( $mm+1, $mcel, "del", $titulo );
		$ws->write_string( $mm+2, $mcel, "R.I.F.", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Total Ventas", $titulo );
		$ws->write_string( $mm+1, $mcel, "Incluyendo el", $titulo );
		$ws->write_string( $mm+2, $mcel, "I.V.A.", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Ventas',    $titulo );
		$ws->write_string( $mm+1, $mcel, 'Exentas o', $titulo );
		$ws->write_string( $mm+2, $mcel, 'no Sujetas',$titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'Valor', $titulo );
		$ws->write_string( $mm+1,$mcel, 'FOB Op.', $titulo );
		$ws->write_string( $mm+2,$mcel, 'Export', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'VENTAS GRAVADAS', $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA GENERAL $tasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, 'Base', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, 'Impuesto', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA ADICIONAL $sobretasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, 'Base', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, 'Impuesto', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA REDUCIDA $redutasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, 'Base', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, 'Impuesto', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'Ajuste a los', $titulo );
		$ws->write_string( $mm+1,$mcel, 'DB Fiscales', $titulo );
		$ws->write_string( $mm+2,$mcel, 'Per. Anterior', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "I.V.A. ", $titulo );
		$ws->write_string( $mm+1,$mcel, "Retenido", $titulo );
		$ws->write_string( $mm+2,$mcel, "Comprador", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Numero ", $titulo );
		$ws->write_string( $mm+1,$mcel, "de", $titulo );
		$ws->write_string( $mm+2,$mcel, "Comp.", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Fecha ", $titulo );
		$ws->write_string( $mm+1,$mcel, "de", $titulo );
		$ws->write_string( $mm+2,$mcel, "Recepcion", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "", $titulo );
		$ws->write_string( $mm+1, $mcel, "Serial", $titulo );
		$ws->write_string( $mm+2, $mcel, "fiscal", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "", $titulo );
		$ws->write_string( $mm+1, $mcel, "Afecta", $titulo );
		$ws->write_string( $mm+2, $mcel, "", $titulo );
		$mcel++;

		$mm +=3;
		$ii = $mm+1;
		$mtiva = 'X';
		$mfecha = '2000-00-00';
		$tventas=$texenta=$tbase=$timpue=$treiva=$tperci=$mforza=$contri=0;
		$finicial = '99999999';
		$ffinal   = '00000000';

		if ( $export->num_rows() > 0 ){
			foreach( $export->result() as $row ){
				// imprime contribuyente
				$fecha = substr($row->fecha,8,2)."/".substr($row->fecha,5,2)."/".substr($row->fecha,0,4);

				$ws->write_string( $mm, 0, $fecha,  $cuerpo );
				$ws->write_string( $mm, 1, $row->numero, $cuerpo );
				if ($row->tipo[0] == "X" )
					$ws->write_string( $mm, 2, "FC", $cuerpoc );
				elseif ( $row->tipo == "XC" )
					$ws->write_string( $mm, 2, "NC", $cuerpoc );
				elseif ( $row->tipo == "FE" )
					$ws->write_string( $mm, 2, "FC", $cuerpoc );
				else
					$ws->write_string( $mm, 2, $row->tipo, $cuerpoc ); // TIPO


				if($row->fuente=='FP'){
					$ws->write_string( $mm, 3, '' , $cuerpo );  // Nro. Documento
					$ws->write_string( $mm, 4, $row->nfiscal, $cuerpo );  // INICIAL
					$ws->write_string( $mm, 5, $row->nhfiscal, $cuerpo ); // FINAL
				}else{
					$ws->write_string( $mm, 3, $row->nfiscal, $cuerpo );  // Nro. Documento
					$ws->write_string( $mm, 4, '', $cuerpo ); // INICIAL
					$ws->write_string( $mm, 5, '', $cuerpo ); // FINAL
				}
				//$ws->write_string( $mm, 4, $row->nfiscal, $cuerpo );  // INICIAL
				//$ws->write_string( $mm, 5, $row->nhfiscal, $cuerpo ); // FINAL

				if ($row->tipo[0] == "X" )
					$ws->write_string( $mm, 6, "DOCUMENTO ANULADO", $cuerpo ); // NOMBRE
				else
					$ws->write_string( $mm, 6, $row->nombre, $cuerpo ); // NOMBRE
				$ws->write_string( $mm, 7, $row->rif, $cuerpo );            // CONTRIBUYENTE

				if ( $row->registro=='04' ) {
					$ws->write_number( $mm, 8, 0, $numero ); // VENTAS + IVA
					$ws->write_number( $mm, 9, 0, $numero ); // VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo ); // EXPORTACION
					$ws->write_number( $mm,11, 0, $numero ); // GENERAL
					$ws->write_number( $mm,12, 0, $numero ); // GENEIMPU
					$ws->write_number( $mm,13, 0, $numero ); // ADICIONAL
					$ws->write_number( $mm,14, 0, $numero ); // ADICIMPU
					$ws->write_number( $mm,15, 0, $numero ); // REDUCIDA
					$ws->write_number( $mm,16, 0, $numero ); // REDUIMPU
					$ws->write_number( $mm,17, $row->geneimpu + $row->adicimpu+$row->reduimpu, $numero ); // REDUIMPU
				} else {
					$ws->write_number( $mm, 8, $row->gtotal, $numero );    // VENTAS + IVA
					$ws->write_number( $mm, 9, $row->exento, $numero );    // VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );               // EXPORTACION
					$ws->write_number( $mm,11, $row->general  , $numero ); // GENERAL
					$ws->write_number( $mm,12, $row->geneimpu , $numero ); // GENEIMPU
					$ws->write_number( $mm,13, $row->adicional, $numero ); // ADICIONAL
					$ws->write_number( $mm,14, $row->adicimpu , $numero ); // ADICIMPU
					$ws->write_number( $mm,15, $row->reducida , $numero ); // REDUCIDA
					$ws->write_number( $mm,16, $row->reduimpu , $numero ); // REDUIMPU
					$ws->write_number( $mm,17, 0, $numero );               // REDUIMPU
				}

				$ws->write_number( $mm,18, $row->reiva      , $numero ); // IVA RETENIDO
				$ws->write_string( $mm,19, $row->comprobante, $cuerpo ); // NRO COMPROBANTE
				if($row->tipo=='NC') $ws->write_string( $mm,22, $row->referen    , $numero ); // NRO FACT AFECTA
				$fecharece = '';
				if ( !empty($row->fecharece) )
					$fecharece = substr($row->fecharece,8,2)."/".substr($row->fecharece,5,2)."/".substr($row->fecharece,0,4);
				$ws->write_string( $mm,20, $fecharece, $cuerpo ); // FECHA COMPROB
				$ws->write_string( $mm,21, $row->serial, $numero );
				$mm++;
			}
		}

		//Imprime el Ultimo
		$celda = $mm+1;

		$fventas   = "=I$celda";   // VENTAS
		$fexenta   = "=J$celda";   // VENTAS EXENTAS
		$ffob      = "=K$celda";   // BASE IMPONIBLE
		$fgeneral  = "=L$celda";   // general
		$fgeneimpu = "=M$celda";   // general
		$fadicional= "=N$celda";   // general
		$fadicimpu = "=O$celda";   // general
		$freducida = "=P$celda";   // general
		$freduimpu = "=Q$celda";   // general
		$fivaret   = "=R$celda";   // general
		//$fivaperu  = "=U$celda";   // general
		$fivajuste = "=S$celda";   // general

		$ws->write( $mm, 0,"Totales...",  $titulo );
		$ws->write_blank( $mm, 1,  $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm, 7,  $titulo );

		$ws->write_formula( $mm, 8, "=SUM(I$ii:I$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 9, "=SUM(J$ii:J$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,10, "=SUM(K$ii:K$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,11, "=SUM(L$ii:L$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,13, "=SUM(N$ii:N$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,16, "=SUM(Q$ii:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,17, "=SUM(R$ii:R$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,18, "=SUM(S$ii:S$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_blank( $mm, 19,  $Tnumero );
		$ws->write_blank( $mm, 20,  $Tnumero );
		$ws->write_blank( $mm, 21,  $Tnumero );
		$ws->write_blank( $mm, 22,  $Tnumero );
		//$ws->write_formula( $mm,20, "=SUM(U$ii:U$mm)", $Tnumero );   //IMPUESTO PERCIBIDO

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'RESUMEN DE VENTAS Y DEBITOS:', $titulo );
		$ws->write_blank( $mm+1, 1,  $titulo );
		$ws->write_blank( $mm  , 2, $titulo );
		$ws->write_blank( $mm+1, 2, $titulo );
		$ws->write_blank( $mm  , 3, $titulo );
		$ws->write_blank( $mm+1, 3, $titulo );
		$ws->write_blank( $mm  , 4, $titulo );
		$ws->write_blank( $mm+1, 4, $titulo );
		$ws->write_blank( $mm  , 5, $titulo );
		$ws->write_blank( $mm+1, 5, $titulo );
		$ws->write_blank( $mm  , 6, $titulo );
		$ws->write_blank( $mm+1, 6, $titulo );

		$ws->write($mm, 7, 'Items', $titulo );
		$ws->write_blank( $mm+1, 7, $titulo );
		$ws->write($mm,   8, 'Base'     , $titulo );
		$ws->write($mm+1, 8, 'Imponible', $titulo );
		$ws->write($mm, 9, 'Items', $titulo );
		$ws->write( $mm+1, 9, "  ", $titulo );
		$ws->write($mm,  10, 'Debito', $titulo );
		$ws->write($mm+1,10, 'Fiscal', $titulo );
		$ws->write($mm, 16, 'LEYENDAS:', $titulo );
		$ws->write_blank( $mm+1, 16,  $titulo );
		$ws->write_blank( $mm,   17,  $titulo );
		$ws->write_blank( $mm+1, 17,  $titulo );
		$ws->write($mm,   18, 'Tipo de Contribuyente',  $titulo );
		$ws->write($mm+1, 18, 'CO -Contribuyente',$cuerpo );
		$ws->write_blank( $mm,   19, $cuerpob );
		$ws->write_blank( $mm+1, 19, $cuerpo );
		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas no Gravadas:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "40" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda" , $Rnumero );
		$ws->write($mm, 16, 'Tipo de Documento', $cuerpob );
		$ws->write_blank( $mm, 17,  $cuerpob );
		$ws->write($mm, 18, 'NO -No Contribuyente', $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas de Exportacion:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "41" , $cuerpoc );
		$ws->write_formula($mm, 8, "=K$celda" , $Rnumero );
		$ws->write($mm, 16, 'FC -Factura', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, 'Tipo de Transaccion', $titulo );
		$ws->write_blank( $mm, 19,  $cuerpob );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Gravadas Alicuota General:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "42" , $cuerpoc );
		$ws->write_formula($mm, 8, "=L$celda" , $Rnumero );
		$ws->write($mm, 9, "43" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda" , $Rnumero );

		$ws->write($mm, 16, 'FE -Factura de Exportacion', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '01 -Registro', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota General + Adicional:', $h1 );
		$ws->write_blank(  $mm, 2,  $h1 );
		$ws->write_blank(  $mm, 3,  $h1 );
		$ws->write_blank(  $mm, 4,  $h1 );
		$ws->write_blank(  $mm, 5,  $h1 );
		$ws->write_blank(  $mm, 6,  $h1 );
		$ws->write($mm, 7, "442" , $cuerpoc );
		$ws->write_formula($mm, 8, "=N$celda" , $Rnumero );
		$ws->write($mm, 9, "452" , $cuerpoc );
		$ws->write_formula($mm, 10, "=O$celda" , $Rnumero );
		$ws->write($mm, 16, 'NC -Nota de Credito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '02 -Complemento', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;
		$ws->write($mm, 1, 'Total Ventas Internas Alicuota Reducida:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "443" , $cuerpoc );
		$ws->write_formula($mm, 8, "=P$celda" , $Rnumero );
		$ws->write($mm, 9, "453" , $cuerpoc );
		$ws->write_formula($mm, 10, "=Q$celda", $Rnumero );

		$ws->write($mm, 16, 'ND -Nota de Debito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '03 -Anulacion', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;
		$ws->write($mm, 1, 'Total Ventas y Creditos Fiscales para efectos de determinacion:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write($mm, 7, "46" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda+K$celda+L$celda+N$celda+P$celda" , $Rnumero );
		$ws->write($mm, 9, "47" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda+O$celda+Q$celda", $Rnumero );

		$ws->write($mm, 16, 'CR -Comprobante Ret.', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '04 -Ajuste', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total IVA retenido por el Comprador:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "66" , $cuerpoc );
		$ws->write_formula($mm, 10, "=S$celda", $Rnumero );

		$mm ++;
		$mm ++;
		$ws->write($mm, 1, 'Total Ajustes a los debitos fiscales de periodos Anteriores:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "48" , $cuerpoc );
		$ws->write_formula($mm, 10, "=R$celda", $Rnumero );
		$wb->close();

		header("Content-type: application/x-msexcel; name=\"lventas.xls\"");
		header("Content-Disposition: inline; filename=\"lventas.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
	}

	//Libro de ventas agrupado fiscal y no fiscal
	function _wlvexcel($mes,$fiscal=false) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');

		$aaa = $this->datasis->ivaplica($mes.'02');
		$tasa      = $aaa['tasa'];
		$redutasa  = $aaa['redutasa'];
		$sobretasa = $aaa['sobretasa'];
		if($fiscal){
			$group=', a.serial';
			$order=', serial';
		}else{
			$group='';
			$order=", IF(tipo IN ('FE','FC','XE','XC'),1,2)";
		}

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$this->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$this->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET tipo='FC' WHERE tipo='FE' ";
		$this->db->simple_query($mSQL);

		$mSQL  = "SELECT
				a.fecha,IF(a.tipo='NC',referen,'') AS referen,
				a.numero, a.serial , '' inicial, '' final,
				a.nfiscal,
				a.rif,
				IF( b.nomfis IS NOT NULL AND b.nomfis!='',b.nomfis,b.nombre) AS nombre,
				a.tipo,
				IF(a.referen=a.numero,'        ',a.referen) afecta,
				a.gtotal*IF(a.tipo='NC',-1,1)    ventatotal,
				a.exento*IF(a.tipo='NC',-1,1)    exento,
				a.general*IF(a.tipo='NC',-1,1)   base,
				a.impuesto*IF(a.tipo='NC',-1,1)  impuesto,
				a.reiva*IF(a.tipo='NC',-1,1)     reiva,
				a.general*IF(a.tipo='NC',-1,1)   general,
				a.geneimpu*IF(a.tipo='NC',-1,1)  geneimpu,
				a.adicional*IF(a.tipo='NC',-1,1) adicional,
				a.adicimpu*IF(a.tipo='NC',-1,1)  adicimpu,
				a.reducida*IF(a.tipo='NC',-1,1)  reducida,
				a.reduimpu*IF(a.tipo='NC',-1,1)  reduimpu,
				a.contribu, a.registro, a.comprobante, a.fecharece ,a.afecta
			FROM siva AS a
			LEFT JOIN scli AS b ON a.clipro=b.cliente
			WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.tipo<>'FA' AND a.contribu='CO'
			UNION
			SELECT
				a.fecha,'' referen,
				' ' numero, a.serial , min(a.numero) inicial, max(a.numero) final,
				' ' nfiscal,
				' ' rif,
				IF(a.manual='S','A NO CONTRIBUYENTES TOTAL DEL DIA POR FACTURACION MANUAL',IF(a.registro<>'04','A NO CONTRIBUYENTES TOTAL DEL DIA',b.nombre)) nombre,
				a.tipo AS tipo,
				' ' afecta,
				SUM(a.gtotal*IF(a.tipo='NC',-1,1))    ventatotal,
				SUM(a.exento*IF(a.tipo='NC',-1,1))    exento,
				SUM(a.general*IF(a.tipo='NC',-1,1))   base,
				SUM(a.impuesto*IF(a.tipo='NC',-1,1))  impuesto,
				SUM(a.reiva*IF(a.tipo='NC',-1,1))     reiva,
				SUM(a.general*IF(a.tipo='NC',-1,1))   general,
				SUM(a.geneimpu*IF(a.tipo='NC',-1,1))  geneimpu,
				SUM(a.adicional*IF(a.tipo='NC',-1,1)) adicional,
				SUM(a.adicimpu*IF(a.tipo='NC',-1,1))  adicimpu,
				SUM(a.reducida*IF(a.tipo='NC',-1,1))  reducida,
				SUM(a.reduimpu*IF(a.tipo='NC',-1,1))  reduimpu,
				'NO' contribu, a.registro, ' ' comprobante, null fecharece, a.afecta
			FROM siva AS a
			LEFT JOIN scli AS  b ON a.clipro=b.cliente
			WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.tipo<>'FA' AND a.contribu='NO' AND a.tipo IN ('FE','FC','NC')
			GROUP BY a.fecha, a.tipo, a.registro, a.manual $group
			ORDER BY fecha $order, numero ";

		$export = $this->db->query($mSQL);

		$fname = tempnam('/tmp','lventas.xls');
		$this->load->library('workbook',array('fname' => $fname));
		$wb =& $this->workbook;
		$ws =& $wb->addworksheet($mes);

		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:C',11);
		$ws->set_column('D:D',15);
		$ws->set_column('E:F',11);
		$ws->set_column('G:G',37);
		$ws->set_column('H:H',11);
		$ws->set_column('I:S',20);
		$ws->set_column('V:V',11);
		$ws->set_column('W:W',11);

		// FORMATOS
		$h      =& $wb->addformat(array( 'bold' => 1, 'size' => 16, 'merge' => 1));
		$h1     =& $wb->addformat(array( 'bold' => 1, 'size' => 11, 'align' => 'left'));

		$titulo =& $wb->addformat(array( 'bold' => 1, 'size' => 9, 'merge' => 0, 'fg_color' => 'silver' ));
		$tt     =& $wb->addformat(array( 'size' => 9, 'merge' => 1, 'fg_color' => 'silver' ));

		$cuerpo  =& $wb->addformat(array( 'size' => 9 ));
		$cuerpoc =& $wb->addformat(array( 'size' => 9, 'align' => 'center', 'merge' => 1 ));
		$cuerpob =& $wb->addformat(array( 'size' => 9, 'align' => 'center', 'merge' => 1 ,'bold' => 1));

		$numero  =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9 ));
		$Tnumero =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9, 'bold' => 1, 'fg_color' => 'silver'));
		$Rnumero =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9, 'bold' => 1, 'align'    => 'right' ));

		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = 'LIBRO DE VENTAS CORRESPONDIENTE AL MES DE '.$anomeses[$nomes].' DEL '.substr($mes,0,4);

		$ws->write(1, 0, $this->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$this->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<20; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};

		$mm=6;
		$mcel = 0;
		// TITULOS

		$ws->write_string( $mm,   $mcel, ''     , $titulo );
		$ws->write_string( $mm+1, $mcel, 'Fecha', $titulo );
		$ws->write_string( $mm+2, $mcel, ''     , $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Identificacion del Documento', $titulo );
		$ws->write_string( $mm+1, $mcel, 'Nro.', $titulo );
		$ws->write_string( $mm+2, $mcel, 'Caja', $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel,  $titulo );
		$ws->write_string( $mm+1, $mcel, 'Tipo', $titulo );
		$ws->write_string( $mm+2, $mcel, 'Doc.', $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, 'Contribuyentes', $titulo );
		$ws->write_string( $mm+2, $mcel, 'Numero', $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, 'No Contribuyentes', $titulo );
		$ws->write_string( $mm+2, $mcel, 'Inicial', $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel , $titulo );
		$ws->write_blank( $mm+1, $mcel , $titulo );
		$ws->write_string( $mm+2, $mcel, 'Final', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Nombre, Razon Social', $titulo );
		$ws->write_string( $mm+1, $mcel, 'o Denominacion del ', $titulo );
		$ws->write_string( $mm+2, $mcel, 'Comprador', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Numero', $titulo );
		$ws->write_string( $mm+1, $mcel, 'del', $titulo );
		$ws->write_string( $mm+2, $mcel, 'R.I.F.', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Total Ventas', $titulo );
		$ws->write_string( $mm+1, $mcel, 'Incluyendo el', $titulo );
		$ws->write_string( $mm+2, $mcel, 'I.V.A.', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Ventas',    $titulo );
		$ws->write_string( $mm+1, $mcel, 'Exentas o', $titulo );
		$ws->write_string( $mm+2, $mcel, 'no Sujetas',$titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'Valor', $titulo );
		$ws->write_string( $mm+1,$mcel, 'FOB Op.', $titulo );
		$ws->write_string( $mm+2,$mcel, 'Export', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'VENTAS GRAVADAS', $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA GENERAL $tasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, 'Base', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, 'Impuesto', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA ADICIONAL $sobretasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, 'Base', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, 'Impuesto', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA REDUCIDA $redutasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, "Base", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, "Impuesto", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Ajuste a los", $titulo );
		$ws->write_string( $mm+1,$mcel, "DB Fiscales", $titulo );
		$ws->write_string( $mm+2,$mcel, "Per. Anterior", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'I.V.A. ', $titulo );
		$ws->write_string( $mm+1,$mcel, 'Retenido', $titulo );
		$ws->write_string( $mm+2,$mcel, 'Comprador', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'Numero ', $titulo );
		$ws->write_string( $mm+1,$mcel, 'de', $titulo );
		$ws->write_string( $mm+2,$mcel, 'Comp.', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'Fecha ', $titulo );
		$ws->write_string( $mm+1,$mcel, 'de', $titulo );
		$ws->write_string( $mm+2,$mcel, 'Recepcion', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, '', $titulo );
		$ws->write_string( $mm+1, $mcel, ($fiscal) ? 'Serial' : 'N Fiscal', $titulo );
		$ws->write_string( $mm+2, $mcel, '', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Documento', $titulo );
		$ws->write_string( $mm+1, $mcel, 'Afectado', $titulo );
		$ws->write_string( $mm+2, $mcel, '', $titulo );
		$mcel++;

		if ($this->db->field_exists('sprv', 'sfac')){
			$ws->write_string( $mm,   $mcel, 'Facturacion', $titulo );
			$ws->write_string( $mm+1, $mcel, 'A', $titulo );
			$ws->write_string( $mm+2, $mcel, 'Terceros', $titulo );
			$mcel++;
		}else{

		}

		$mm +=3;
		$ii = $mm+1;
		$mtiva = 'X';
		$mfecha = '2000-00-00';
		$tventas=$texenta=$tbase=$timpue=$treiva=$tperci=$mforza=$contri=0;
		$finicial = '99999999';
		$ffinal   = '00000000';

		if ( $export->num_rows() > 0 ){
			foreach( $export->result() as $row ){
				// imprime contribuyente
				$fecha = substr($row->fecha,8,2)."/".substr($row->fecha,5,2)."/".substr($row->fecha,0,4);

				$ws->write_string( $mm, 0, $fecha,  $cuerpo );				// Fecha
				$ws->write_string( $mm, 1, ' ', $cuerpo );			// Numero de Caja

				if ($row->tipo[0] == 'X' )
					$ws->write_string( $mm, 2, 'FC', $cuerpoc );		// TIPO
				elseif ( $row->tipo == 'XC' )
					$ws->write_string( $mm, 2, 'NC', $cuerpoc );		// TIPO
				elseif ( $row->tipo == 'FE' )
					$ws->write_string( $mm, 2, 'FC', $cuerpoc );		// TIPO
				else
					$ws->write_string( $mm, 2, $row->tipo, $cuerpoc );	// TIPO

				$ws->write_string( $mm, 3, $row->numero , $cuerpo );		// Nro. Documento
				$ws->write_string( $mm, 4, $row->inicial, $cuerpo );		// INICIAL
				$ws->write_string( $mm, 5, $row->final  , $cuerpo );		// FINAL

				if($row->tipo=='FT'){
					$row->tercero   =$row->ventatotal;
					$row->ventatotal=0;
					$row->exento    =0;
					$row->general   =0;
					$row->geneimpu  =0;
					$row->adicional =0;
					$row->adicimpu  =0;
					$row->reducida  =0;
					$row->reduimpu  =0;
				}

				if ($row->tipo[0] == 'X' )
					$ws->write_string( $mm, 6, 'DOCUMENTO ANULADO', $cuerpo );			// NOMBRE
				else
					$ws->write_string( $mm, 6, $row->nombre, $cuerpo );		// NOMBRE
				$ws->write_string( $mm, 7, $row->rif, $cuerpo );			// CONTRIBUYENTE

				if ( $row->registro=='04' ) {
					$ws->write_number( $mm, 8, 0, $numero );		// VENTAS + IVA
					$ws->write_number( $mm, 9, 0, $numero );		// VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );		// EXPORTACION
					$ws->write_number( $mm,11, 0, $numero );		// GENERAL
					$ws->write_number( $mm,12, 0, $numero );		// GENEIMPU
					$ws->write_number( $mm,13, 0, $numero );		// ADICIONAL
					$ws->write_number( $mm,14, 0, $numero );		// ADICIMPU
					$ws->write_number( $mm,15, 0, $numero );		// REDUCIDA
					$ws->write_number( $mm,16, 0, $numero );		// REDUIMPU
					$ws->write_number( $mm,17, $row->geneimpu + $row->adicimpu+$row->reduimpu, $numero );		// REDUIMPU
				} else {
					$ws->write_number( $mm, 8, $row->ventatotal, $numero );	// VENTAS + IVA
					$ws->write_number( $mm, 9, $row->exento, $numero );   	// VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );				// EXPORTACION
					$ws->write_number( $mm,11, $row->general, $numero );	// GENERAL
					$ws->write_number( $mm,12, $row->geneimpu, $numero );	// GENEIMPU
					$ws->write_number( $mm,13, $row->adicional, $numero );	// ADICIONAL
					$ws->write_number( $mm,14, $row->adicimpu, $numero );	// ADICIMPU
					$ws->write_number( $mm,15, $row->reducida, $numero );	// REDUCIDA
					$ws->write_number( $mm,16, $row->reduimpu, $numero );	// REDUIMPU
					$ws->write_number( $mm,17, 0, $numero );		// REDUIMPU
				}

				$ws->write_number( $mm,18, $row->reiva, $numero );		// IVA RETENIDO
				$ws->write_string( $mm,19,($row->tipo=='CR') ? $row->numero:'', $cuerpo );	// NRO COMPROBANTE
				if($row->tipo=='CR'){
					if($fiscal)
						$afecta=$this->datasis->dameval("SELECT nfiscal FROM sfac WHERE tipo_doc='F' AND numero=".$this->db->escape($row->afecta));
					else
						$afecta=$row->afecta;
					$ws->write_string( $mm,22, $afecta , $numero ); //NRO FACT AFECTA
				}else{
					if($fiscal)
						$afecta=$this->datasis->dameval("SELECT nfiscal FROM sfac WHERE tipo_doc='F' AND numero=".$this->db->escape($row->referen));
					else
						$afecta=$row->referen;
					$ws->write_string( $mm,22, $afecta, $numero ); //NRO FACT AFECTA
				}
				$fecharece = '';
				if ( !empty($row->fecharece) )
					$fecharece = substr($row->fecharece,8,2).'/'.substr($row->fecharece,5,2).'/'.substr($row->fecharece,0,4);
				$ws->write_string( $mm,20, $fecharece, $cuerpo );	// FECHA COMPROB
				$ws->write_string( $mm,21, ($fiscal)? $row->serial : $row->nfiscal, $numero );
				if(isset($row->tercero)){
					$ws->write_number( $mm,23, $row->tercero, $numero); //Facturacion a tercero
				}
				$mm++;
			}
		}
		//Imprime el Ultimo
		$celda = $mm+1;

		$fventas    = "=I${celda}";   // VENTAS
		$fexenta    = "=J${celda}";   // VENTAS EXENTAS
		$ffob       = "=K${celda}";   // BASE IMPONIBLE
		$fgeneral   = "=L${celda}";   // general
		$fgeneimpu  = "=M${celda}";   // general
		$fadicional = "=N${celda}";   // general
		$fadicimpu  = "=O${celda}";   // general
		$freducida  = "=P${celda}";   // general
		$freduimpu  = "=Q${celda}";   // general
		$fivaret    = "=R${celda}";   // general
		//$fivaperu   = "=U${celda}";   // general

		$fivajuste = "=S${celda}";   // general

		$ws->write( $mm, 0,'Totales...',  $titulo );
		$ws->write_blank( $mm, 1,  $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm, 7,  $titulo );

		$ws->write_formula( $mm, 8, "=SUM(I$ii:I$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 9, "=SUM(J$ii:J$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,10, "=SUM(K$ii:K$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,11, "=SUM(L$ii:L$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,13, "=SUM(N$ii:N$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,16, "=SUM(Q$ii:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,17, "=SUM(R$ii:R$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,18, "=SUM(S$ii:S$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_blank( $mm, 19,  $Tnumero );
		$ws->write_blank( $mm, 20,  $Tnumero );
		$ws->write_blank( $mm, 21,  $Tnumero );
		$ws->write_blank( $mm, 22,  $Tnumero );
		if ($this->db->field_exists('sprv', 'sfac')){
			$ws->write_formula( $mm,23, "=SUM(X$ii:X$mm)", $Tnumero );   //Venta a tercero
		}

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'RESUMEN DE VENTAS Y DEBITOS:', $titulo );
		$ws->write_blank( $mm+1, 1,  $titulo );

		$ws->write_blank( $mm  , 2, $titulo );
		$ws->write_blank( $mm+1, 2, $titulo );

		$ws->write_blank( $mm  , 3, $titulo );
		$ws->write_blank( $mm+1, 3, $titulo );

		$ws->write_blank( $mm  , 4, $titulo );
		$ws->write_blank( $mm+1, 4, $titulo );

		$ws->write_blank( $mm  , 5, $titulo );
		$ws->write_blank( $mm+1, 5, $titulo );

		$ws->write_blank( $mm  , 6, $titulo );
		$ws->write_blank( $mm+1, 6, $titulo );

		$ws->write($mm, 7, 'Items', $titulo );
		$ws->write_blank( $mm+1, 7, $titulo );

		$ws->write($mm,   8, 'Base'     , $titulo );
		$ws->write($mm+1, 8, 'Imponible', $titulo );

		$ws->write($mm, 9, 'Items', $titulo );
		$ws->write( $mm+1, 9, "  ", $titulo );

		$ws->write($mm,  10, 'Debito', $titulo );
		$ws->write($mm+1,10, 'Fiscal', $titulo );

		$ws->write($mm, 16, 'LEYENDAS:', $titulo );
		$ws->write_blank( $mm+1, 16,  $titulo );

		$ws->write_blank( $mm,   17,  $titulo );
		$ws->write_blank( $mm+1, 17,  $titulo );

		$ws->write($mm,   18, 'Tipo de Contribuyente',  $titulo );
		$ws->write($mm+1, 18, 'CO -Contribuyente',$cuerpo );

		$ws->write_blank( $mm,   19, $cuerpob );
		$ws->write_blank( $mm+1, 19, $cuerpo );

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas no Gravadas:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "40" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda" , $Rnumero );
		$ws->write($mm, 16, 'Tipo de Documento', $cuerpob );
		$ws->write_blank( $mm, 17,  $cuerpob );
		$ws->write($mm, 18, 'NO -No Contribuyente', $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas de Exportacion:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "41" , $cuerpoc );
		$ws->write_formula($mm, 8, "=K$celda" , $Rnumero );
		$ws->write($mm, 16, 'FC -Factura', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, 'Tipo de Transaccion', $titulo );
		$ws->write_blank( $mm, 19,  $cuerpob );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Gravadas Alicuota General:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "42" , $cuerpoc );
		$ws->write_formula($mm, 8, "=L$celda" , $Rnumero );
		$ws->write($mm, 9, "43" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda" , $Rnumero );

		$ws->write($mm, 16, 'FE -Factura de Exportacion', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '01 -Registro', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota General + Adicional:', $h1 );
		$ws->write_blank(  $mm, 2,  $h1 );
		$ws->write_blank(  $mm, 3,  $h1 );
		$ws->write_blank(  $mm, 4,  $h1 );
		$ws->write_blank(  $mm, 5,  $h1 );
		$ws->write_blank(  $mm, 6,  $h1 );
		$ws->write($mm, 7, '442' , $cuerpoc );
		$ws->write_formula($mm, 8, "=N$celda" , $Rnumero );
		$ws->write($mm, 9, '452' , $cuerpoc );
		$ws->write_formula($mm, 10, "=O$celda" , $Rnumero );

		$ws->write($mm, 16, 'NC -Nota de Credito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '02 -Complemento', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota Reducida:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, '443' , $cuerpoc );
		$ws->write_formula($mm, 8, "=P$celda" , $Rnumero );
		$ws->write($mm, 9, '453' , $cuerpoc );
		$ws->write_formula($mm, 10, "=Q$celda", $Rnumero );

		$ws->write($mm, 16, 'ND -Nota de Debito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '03 -Anulacion', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas y Creditos Fiscales para efectos de determinacion:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write($mm, 7, '46' , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda+K$celda+L$celda+N$celda+P$celda" , $Rnumero );
		$ws->write($mm, 9, '47' , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda+O$celda+Q$celda", $Rnumero );

		$ws->write($mm, 16, 'CR -Comprobante Retencion', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '04 -Ajuste', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total IVA retenido por el Comprador:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, '66' , $cuerpoc );
		$ws->write_formula($mm, 10, "=S$celda", $Rnumero );

		$mm ++;
		$mm ++;
		$ws->write($mm, 1, 'Total Ajustes a los debitos fiscales de periodos Anteriores:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, '48' , $cuerpoc );
		$ws->write_formula($mm, 10, "=R$celda", $Rnumero );

		$wb->close();

		header("Content-type: application/x-msexcel; name=\"lventas.xls\"");
		header("Content-Disposition: inline; filename=\"lventas.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
	}

	//Libro de ventas no agrupado
	function wlvexcel1($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');

		$aaa = $this->datasis->ivaplica($mes."02");
		$tasa      = $aaa['tasa'];
		$redutasa  = $aaa['redutasa'];
		$sobretasa = $aaa['sobretasa'];

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$this->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$this->db->simple_query($mSQL);

		$mSQL  = "SELECT
				a.fecha,
				a.numero,
				a.nfiscal,
				a.rif,
				IF( MID(a.rif,1,1) NOT IN ('J','G') AND b.contacto IS NOT NULL AND b.contacto!='',b.contacto,a.nombre) AS nombre,
				a.tipo,
				IF(a.referen=a.numero,'        ',a.referen) afecta,
				a.gtotal*IF(a.tipo='NC',-1,1)    ventatotal,
				a.exento*IF(a.tipo='NC',-1,1)    exento,
				a.general*IF(a.tipo='NC',-1,1)   base,
				a.impuesto*IF(a.tipo='NC',-1,1)  impuesto,
				a.reiva*IF(a.tipo='NC',-1,1)     reiva,
				a.general*IF(a.tipo='NC',-1,1)	 general,
				a.geneimpu*IF(a.tipo='NC',-1,1)  geneimpu,
				a.adicional*IF(a.tipo='NC',-1,1) adicional,
				a.adicimpu*IF(a.tipo='NC',-1,1)  adicimpu,
				a.reducida*IF(a.tipo='NC',-1,1)  reducida,
				a.reduimpu*IF(a.tipo='NC',-1,1)  reduimpu,
				a.contribu, a.registro,a.afecta,a.fecharece,
				IF(a.tipo='NC',referen,'') AS referen
			FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
			WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.tipo<>'FA'
			ORDER BY a.fecha, IF(a.tipo IN ('FC','XE','XC'),1,2), numero ";

		$export = $this->db->query($mSQL);
		$fname = tempnam("/tmp","lventas.xls");
		$this->load->library("workbook",array("fname" => $fname));;
		$wb =& $this->workbook;
		$ws =& $wb->addworksheet($mes);

		# ANCHO DE LAS COLUMNAS
		$ws->set_column('A:A',11);
		$ws->set_column('B:B',37);
		$ws->set_column('C:C',11);
		$ws->set_column('D:D',6);
		$ws->set_column('E:E',11);
		$ws->set_column('F:F',6);
		$ws->set_column('G:G',11);
		$ws->set_column('H:H',6);
		$ws->set_column('I:U',12);

		# FORMATOS
		$h      =& $wb->addformat(array( "bold" => 1, "size" => 16, "merge" => 1));
		$h1     =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'left'));

		$titulo =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 0, "fg_color" => 'silver' ));
		$tt =& $wb->addformat(array( "size" => 9, "merge" => 1, "fg_color" => 'silver' ));

		$cuerpo  =& $wb->addformat(array( "size" => 9 ));
		$cuerpoc =& $wb->addformat(array( "size" => 9, "align" => 'center', "merge" => 1 ));
		$cuerpob =& $wb->addformat(array( "size" => 9, "align" => 'center', "bold" => 1, "merge" => 1 ));

		$numero  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));

		# COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = "LIBRO DE VENTAS CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL ".substr($mes,0,4);

		$ws->write(1, 0, $this->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$this->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<20; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};

		//$ws->write_string( $mm, $ii , $mvalor );
		$mm=6;

		// TITULOS
		$ws->write_string( $mm,   0, "", $titulo );
		$ws->write_string( $mm+1, 0, "Fecha", $titulo );
		$ws->write_string( $mm+2, 0, "", $titulo );

		$ws->write_string( $mm,   1, "Nombre, Razon Social", $titulo );
		$ws->write_string( $mm+1, 1, "o Denominacion del ", $titulo );
		$ws->write_string( $mm+2, 1, "Comprador", $titulo );

		$ws->write_string( $mm,   2, "", $titulo );
		$ws->write_string( $mm+1, 2, "R.I.F. o", $titulo );
		$ws->write_string( $mm+2, 2, "Cedula", $titulo );

		$ws->write_string( $mm,   3, "Tipo", $titulo );
		$ws->write_string( $mm+1, 3, "de", $titulo );
		$ws->write_string( $mm+2, 3, "Doc.", $titulo );

		$ws->write_string( $mm,   4, "Numero", $titulo );
		$ws->write_string( $mm+1, 4, "del", $titulo );
		$ws->write_string( $mm+2, 4, "Doc.", $titulo );

		$ws->write_string( $mm,   5, "Tipo", $titulo );
		$ws->write_string( $mm+1, 5, "de", $titulo );
		$ws->write_string( $mm+2, 5, "Trans", $titulo );

		$ws->write_string( $mm,   6, "Numero", $titulo );
		$ws->write_string( $mm+1, 6, "del Doc.", $titulo );
		$ws->write_string( $mm+2, 6, "Afectado", $titulo );

		$ws->write_string( $mm,   7, "Tipo ", $titulo );
		$ws->write_string( $mm+1, 7, "de", $titulo );
		$ws->write_string( $mm+2, 7, "Cont.", $titulo );

		$ws->write_string( $mm,   8, "Total Ventas", $titulo );
		$ws->write_string( $mm+1, 8, "Incluyendo el", $titulo );
		$ws->write_string( $mm+2, 8, "I.V.A.", $titulo );

		$ws->write_string( $mm,   9, "Ventas", $titulo );
		$ws->write_string( $mm+1, 9, "Exentas o", $titulo );
		$ws->write_string( $mm+2, 9, "no Sujetas", $titulo );

		$ws->write_string( $mm,  10, "Valor", $titulo );
		$ws->write_string( $mm+1,10, "FOB Op.", $titulo );
		$ws->write_string( $mm+2,10, "Export", $titulo );

		$ws->write_string( $mm,  11, "VENTAS GRAVADAS", $titulo );
		$ws->write_string( $mm+1,11, "ALICUOTA GENERAL $tasa%", $titulo );
		$ws->write_string( $mm+2,11, "Base", $titulo );

		$ws->write_blank(  $mm,  12, $titulo );
		$ws->write_blank(  $mm+1,12, $titulo );
		$ws->write_string( $mm+2,12, "Impuesto", $titulo );

		$ws->write_blank(  $mm,  13, $titulo );
		$ws->write_string( $mm+1,13, "ALICUOTA ADICIONAL $sobretasa%", $titulo );
		$ws->write_string( $mm+2,13, "Base", $titulo );

		$ws->write_blank(  $mm,  14, $titulo );
		$ws->write_blank(  $mm+1,14, $titulo );
		$ws->write_string( $mm+2,14, "Impuesto", $titulo );

		$ws->write_blank(  $mm,  15, $titulo );
		$ws->write_string( $mm+1,15, "ALICUOTA REDUCIDA $redutasa%", $titulo );
		$ws->write_string( $mm+2,15, "Base", $titulo );

		$ws->write_blank(  $mm,  16, $titulo );
		$ws->write_blank(  $mm+1,16, $titulo );
		$ws->write_string( $mm+2,16, "Impuesto", $titulo );

		$ws->write_string( $mm,  17, "I.V.A. ", $titulo );
		$ws->write_string( $mm+1,17, "Retenido", $titulo );
		$ws->write_string( $mm+2,17, "Comprador", $titulo );

		$ws->write_string( $mm,  18, "Numero ", $titulo );
		$ws->write_string( $mm+1,18, "de", $titulo );
		$ws->write_string( $mm+2,18, "Comp.", $titulo );

		$ws->write_string( $mm,  19, "Fecha ", $titulo );
		$ws->write_string( $mm+1,19, "de", $titulo );
		$ws->write_string( $mm+2,19, "Recepcion", $titulo );

		$ws->write_string( $mm,  20, "", $titulo );
		$ws->write_string( $mm+1,20, "I.V.A.", $titulo );
		$ws->write_string( $mm+2,20, "Percibido", $titulo );

		$mm +=3;

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


		if ( $export->num_rows() > 0 ){
			foreach( $export->result() as $row ) {
				// imprime contribuyente
				$fecha = substr($row->fecha,8,2)."/".$ameses[substr($row->fecha,5,2)-1]."/".substr($row->fecha,0,4);

				$ws->write_string( $mm, 0, $fecha,  $cuerpo );				// Fecha
				$ws->write_string( $mm, 1, $row->nombre, $cuerpo );		// Nombre
				$ws->write_string( $mm, 2, $row->rif, $cuerpo );			// RIF/CEDULA

				if ( $row->tipo == "XE" )
					$ws->write_string( $mm, 3, "FC", $cuerpoc );			// TIPO
				elseif ( $row->tipo == "XC" )
					$ws->write_string( $mm, 3, "NC", $cuerpoc );			// TIPO
				elseif ( $row->tipo == "FE" )
					$ws->write_string( $mm, 3, "FC", $cuerpoc );			// TIPO
				else
					$ws->write_string( $mm, 3, $row->tipo, $cuerpoc );	// TIPO

				$ws->write_string( $mm, 4, $row->numero, $cuerpo );		// Nro. Documento
				if ( substr($row->tipo,0,1) == "X" )
					$ws->write_string( $mm, 5, "03", $cuerpoc );		// TIPO Transac
				else
					$ws->write_string( $mm, 5, $row->registro, $cuerpoc );		// TIPO Transac
				$ws->write_string( $mm, 6, $row->referen, $cuerpo );		// DOC. AFECTADO
				$ws->write_string( $mm, 7, $row->contribu, $cuerpo );	// CONTRIBUYENTE
				$ws->write_number( $mm, 8, $row->ventatotal, $numero );	// VENTAS + IVA
				$ws->write_number( $mm, 9, $row->exento, $numero );   	// VENTAS EXENTAS
				$ws->write_number( $mm,10, 0, $cuerpo );   				// EXPORTACION
				$ws->write_number( $mm,11, $row->general, $numero );	// GENERAL
				$ws->write_number( $mm,12, $row->geneimpu, $numero );	// GENEIMPU
				$ws->write_number( $mm,13, $row->adicional, $numero );	// ADICIONAL
				$ws->write_number( $mm,14, $row->adicimpu, $numero );	// ADICIMPU
				$ws->write_number( $mm,15, $row->reducida, $numero );	// REDUCIDA
				$ws->write_number( $mm,16, $row->reduimpu, $numero );	// REDUIMPU
				$ws->write_number( $mm,17, $row->reiva, $numero );		// IVA RETENIDO
				if($row->tipo=='CR'){
					$afecta=$row->afecta;
					$ws->write_string( $mm,18, $row->numero, $numero ); //NRO FACT AFECTA
				}else{
					$ws->write_string( $mm,18, '', $numero ); //NRO COMPROBANTE
				}


				//$ws->write_string( $mm,18, $row['comprobante'], $cuerpo );	// NRO COMPROBANTE
				if(strlen(trim($row->fecharece))>0){
					$fecharece = substr($row->fecharece,8,2)."/".substr($row->fecharece,5,2)."/".substr($row->fecharece,0,4);
					$ws->write_string( $mm,19, $fecharece, $cuerpo );	// FECHA COMPROB
				}
				$ws->write_number( $mm,20, 0, $numero );	// IMPUESTO PERCIBIDO
				$mm++;
			}
		}

		//Imprime el Ultimo
		$celda = $mm+1;

		$fventas = "=I$celda";    // VENTAS
		$fexenta = "=J$celda";    // VENTAS EXENTAS
		$ffob    = "=K$celda";    // BASE IMPONIBLE
		$fgeneral  = "=L$celda";  // general
		$fgeneimpu = "=M$celda";  // general
		$fadicional = "=N$celda"; // general
		$fadicimpu  = "=O$celda"; // general
		$freducida = "=P$celda";  // general
		$freduimpu = "=Q$celda";  // general
		$fivaret   = "=R$celda";  // general
		$fivaperu  = "=U$celda";  // general

		$ws->write( $mm, 0,"Totales...",  $titulo );
		$ws->write_blank( $mm, 1,  $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm, 7,  $titulo );

		$ws->write_formula( $mm, 8, "=SUM(I$ii:I$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 9, "=SUM(J$ii:J$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,10, "=SUM(K$ii:K$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,11, "=SUM(L$ii:L$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,13, "=SUM(N$ii:N$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,16, "=SUM(Q$ii:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,17, "=SUM(R$ii:R$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_blank( $mm, 18,  $Tnumero );
		$ws->write_blank( $mm, 19,  $Tnumero );
		$ws->write_formula( $mm,20, "=SUM(U$ii:U$mm)", $Tnumero );   //IMPUESTO PERCIBIDO

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'RESUMEN DE VENTAS Y DEBITOS:', $titulo );
		$ws->write_blank( $mm+1, 1,  $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm+1, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm+1, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm+1, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm+1, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm+1, 6,  $titulo );
		$ws->write($mm, 7, 'Items', $titulo );
		$ws->write_blank( $mm+1, 7,  $titulo );
		$ws->write($mm,   8, 'Base', $titulo );
		$ws->write($mm+1, 8, 'Imponible', $titulo );
		$ws->write($mm, 9, 'Items', $titulo );
		$ws->write( $mm+1, 9, "  ", $titulo );
		$ws->write($mm,  10, 'Debito', $titulo );
		$ws->write($mm+1,10, 'Fiscal', $titulo );
		$ws->write($mm,11, 'Items', $titulo );
		$ws->write( $mm+1, 11, "  ",  $titulo );
		$ws->write($mm,12, 'IVA Ret.', $titulo );
		$ws->write($mm+1,12, 'Retetenido', $titulo );
		$ws->write($mm,13, 'Items', $titulo );
		$ws->write( $mm+1, 13, "  ",  $titulo );
		$ws->write($mm,  14, 'IVA', $titulo );
		$ws->write($mm+1,14, 'Percibido', $titulo );
		$ws->write($mm, 16, 'LEYENDAS:', $titulo );
		$ws->write_blank( $mm+1, 16,  $titulo );
		$ws->write_blank( $mm,   17,  $titulo );
		$ws->write_blank( $mm+1, 17,  $titulo );
		$ws->write($mm,   18, 'Tipo Contribuyente',  $cuerpob );
		$ws->write($mm+1, 18, 'CO -Contribuyente',$cuerpo );
		$ws->write_blank( $mm,   19, $cuerpob );
		$ws->write_blank( $mm+1, 19, $cuerpo );

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas no Gravadas:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "40" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda" , $Rnumero );
		$ws->write($mm, 16, 'Tipo de Documento', $cuerpob );
		$ws->write_blank( $mm, 17,  $cuerpob );
		$ws->write($mm, 18, 'NO -No Contribuyente', $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas de Exportacion:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "41" , $cuerpoc );
		$ws->write_formula($mm, 8, "=K$celda" , $Rnumero );
		$ws->write($mm, 16, 'FC -Factura', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, 'Tipo de Transaccion', $cuerpob );
		$ws->write_blank( $mm, 19,  $cuerpob );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Gravadas Alicuota General:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "42" , $cuerpoc );
		$ws->write_formula($mm, 8, "=L$celda" , $Rnumero );
		$ws->write($mm, 9, "43" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda" , $Rnumero );
		$ws->write_number($mm, 12, "0", $Rnumero );
		$ws->write_number($mm, 14, "0", $Rnumero );
		$ws->write($mm, 16, 'FE -Factura de Exportacion', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '01 -Registro', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota General + Adicional:', $h1 );
		$ws->write_blank(  $mm, 2,  $h1 );
		$ws->write_blank(  $mm, 3,  $h1 );
		$ws->write_blank(  $mm, 4,  $h1 );
		$ws->write_blank(  $mm, 5,  $h1 );
		$ws->write_blank(  $mm, 6,  $h1 );
		$ws->write($mm, 7, "442" , $cuerpoc );
		$ws->write_formula($mm, 8, "=N$celda" , $Rnumero );
		$ws->write($mm, 9, "452" , $cuerpoc );
		$ws->write_formula($mm, 10, "=O$celda" , $Rnumero );
		$ws->write_number($mm, 12, "0", $Rnumero );
		$ws->write_number($mm, 14, "0", $Rnumero );

		$ws->write($mm, 16, 'NC -Nota de Credito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '02 -Complemento', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota Reducida:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "443" , $cuerpoc );
		$ws->write_formula($mm, 8, "=P$celda" , $Rnumero );
		$ws->write($mm, 9, "453" , $cuerpoc );
		$ws->write_formula($mm, 10, "=Q$celda", $Rnumero );
		$ws->write_number($mm, 12, "0", $Rnumero );
		$ws->write_number($mm, 14, "0", $Rnumero );
		$ws->write($mm, 16, 'ND -Nota de Debito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '03 -Anulacion', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );

		$mm ++;
		$ws->write($mm, 1, 'Total Ventas y Creditos Fiscales para efectos de determinacion:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write($mm, 7, "46" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda+K$celda+L$celda+N$celda+P$celda" , $Rnumero );
		$ws->write($mm, 9, "47" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda+O$celda+Q$celda", $Rnumero );
		$ws->write($mm, 11, "66" , $cuerpoc );
		$ws->write_number($mm, 12, "0", $Rnumero );
		$ws->write($mm, 13, "68" , $cuerpoc );
		$ws->write_number($mm, 14, "0", $Rnumero );
		$ws->write($mm, 16, 'TK -Ticket de Caja', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '04 -Ajuste', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total IVA retenido por el Comprador:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm, 7,  $titulo );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "66" , $cuerpoc );
		$ws->write_formula($mm, 10, "=S$celda", $Rnumero );
		//$ws->write($mm, 11, "66" , $cuerpoc );
		//$ws->write_number($mm, 12, "0", $Rnumero );
		//$ws->write($mm, 13, "68" , $cuerpoc );
		//$ws->write_number($mm, 14, "0", $Rnumero );
		//$ws->write($mm, 16, 'TK -Ticket de Caja', $cuerpo );
		//$ws->write_blank( $mm+1, 16,  $cuerpo );
		//$ws->write($mm, 18, '04 -Ajuste', $cuerpo );
		//$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$wb->close();
		header("Content-type: application/x-msexcel; name=\"lventas.xls\"");
		header("Content-Disposition: inline; filename=\"lventas.xls\"");
		$fh=fopen($fname,'rb');
		fpassthru($fh);
		unlink($fname);
		//print "$header\n$data";
	}

	//Libro de ventas no agrupado version 2
	function wlvexcel2($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');

		$aaa = $this->datasis->ivaplica($mes.'02');
		$tasa      = $aaa['tasa'];
		$redutasa  = $aaa['redutasa'];
		$sobretasa = $aaa['sobretasa'];

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$this->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE siva SET tipo='FC' WHERE tipo='FE' ";
		$this->db->simple_query($mSQL);

		$mSQL  = "SELECT
				a.fecha,IF(a.tipo='NC',referen,'')referen,
				a.numero, '' inicial, ' ' final,
				a.nfiscal,
				a.rif,
				IF( b.nomfis IS NOT NULL AND b.nomfis!='',b.nomfis,b.nombre) AS nombre,
				a.tipo,
				a.afecta,
				a.gtotal*IF(a.tipo='NC',-1,1)    ventatotal,
				a.exento*IF(a.tipo='NC',-1,1)    exento,
				a.general*IF(a.tipo='NC',-1,1)   base,
				a.impuesto*IF(a.tipo='NC',-1,1)  impuesto,
				a.reiva*IF(a.tipo='NC',-1,1)     reiva,
				a.general*IF(a.tipo='NC',-1,1)	 general,
				a.geneimpu*IF(a.tipo='NC',-1,1)  geneimpu,
				a.adicional*IF(a.tipo='NC',-1,1) adicional,
				a.adicimpu*IF(a.tipo='NC',-1,1)  adicimpu,
				a.reducida*IF(a.tipo='NC',-1,1)  reducida,
				a.reduimpu*IF(a.tipo='NC',-1,1)  reduimpu,
				a.contribu, a.registro, a.comprobante, a.fecharece,a.serial
			FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
			WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.tipo<>'FA'
			ORDER BY a.fecha, IF(a.tipo IN ('FE','FC','XE','XC'),1,2), a.numero ";

		$export = $this->db->query($mSQL);

		$fname = tempnam('/tmp','lventas.xls');
		$this->load->library('workbook',array('fname' => $fname));
		$wb =& $this->workbook;
		$ws =& $wb->addworksheet($mes);

		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:F',11);
		$ws->set_column('G:G',37);
		$ws->set_column('H:U',11);

		// FORMATOS
		$h      =& $wb->addformat(array( 'bold' => 1, 'size' => 16, 'merge' => 1));
		$h1     =& $wb->addformat(array( 'bold' => 1, 'size' => 11, 'align' => 'left'));

		$titulo =& $wb->addformat(array( 'bold' => 1, 'size' => 9, 'merge' => 0, 'fg_color' => 'silver' ));
		$tt =& $wb->addformat(array( 'size' => 9, 'merge' => 1, 'fg_color' => 'silver' ));

		$cuerpo  =& $wb->addformat(array( 'size' => 9 ));
		$cuerpoc =& $wb->addformat(array( 'size' => 9, "align" => 'center', 'merge' => 1 ));
		$cuerpob =& $wb->addformat(array( 'size' => 9, "align" => 'center', 'bold' => 1, 'merge' => 1 ));

		$numero  =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9 ));
		$Tnumero =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9, 'bold' => 1, 'fg_color' => 'silver' ));
		$Rnumero =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9, 'bold' => 1, 'align' => 'right' ));


		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = 'LIBRO DE VENTAS CORRESPONDIENTE AL MES DE '.$anomeses[$nomes].' DEL '.substr($mes,0,4);

		$ws->write(1, 0, $this->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, 'RIF: '.$this->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<20; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};

		//$ws->write_string( $mm, $ii , $mvalor );

		$mm=6;
		$mcel = 0;
		// TITULOS

		$ws->write_string( $mm,   $mcel, '', $titulo );
		$ws->write_string( $mm+1, $mcel, 'Fecha', $titulo );
		$ws->write_string( $mm+2, $mcel, '', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Identificacion del Documento', $titulo );
		$ws->write_string( $mm+1, $mcel, 'Nro.', $titulo );
		$ws->write_string( $mm+2, $mcel, 'Caja', $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel,  $titulo );
		$ws->write_string( $mm+1, $mcel, 'Tipo', $titulo );
		$ws->write_string( $mm+2, $mcel, 'Doc.', $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, 'Contribuyentes', $titulo );
		$ws->write_string( $mm+2, $mcel, 'Numero', $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, 'No Contribuyentes', $titulo );
		$ws->write_string( $mm+2, $mcel, 'Inicial', $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_blank( $mm+1, $mcel, $titulo );
		$ws->write_string( $mm+2, $mcel, 'Final', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Nombre, Razon Social', $titulo );
		$ws->write_string( $mm+1, $mcel, 'o Denominacion del', $titulo );
		$ws->write_string( $mm+2, $mcel, 'Comprador', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Numero', $titulo );
		$ws->write_string( $mm+1, $mcel, 'del', $titulo );
		$ws->write_string( $mm+2, $mcel, 'R.I.F.', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Total Ventas', $titulo );
		$ws->write_string( $mm+1, $mcel, 'Incluyendo el', $titulo );
		$ws->write_string( $mm+2, $mcel, 'I.V.A.', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, 'Ventas',    $titulo );
		$ws->write_string( $mm+1, $mcel, 'Exentas o', $titulo );
		$ws->write_string( $mm+2, $mcel, 'no Sujetas',$titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'Valor', $titulo );
		$ws->write_string( $mm+1,$mcel, 'FOB Op.', $titulo );
		$ws->write_string( $mm+2,$mcel, 'Export', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'VENTAS GRAVADAS', $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA GENERAL $tasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, 'Base', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, 'Impuesto', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA ADICIONAL $sobretasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, 'Base', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, 'Impuesto', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA REDUCIDA $redutasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, 'Base', $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, 'Impuesto', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'Ajuste a los', $titulo );
		$ws->write_string( $mm+1,$mcel, 'DB Fiscales', $titulo );
		$ws->write_string( $mm+2,$mcel, 'Per. Anterior', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'I.V.A.', $titulo );
		$ws->write_string( $mm+1,$mcel, 'Retenido', $titulo );
		$ws->write_string( $mm+2,$mcel, 'Comprador', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'Numero', $titulo );
		$ws->write_string( $mm+1,$mcel, 'de', $titulo );
		$ws->write_string( $mm+2,$mcel, 'Comp.', $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, 'Fecha', $titulo );
		$ws->write_string( $mm+1,$mcel, 'de', $titulo );
		$ws->write_string( $mm+2,$mcel, "Recepcion", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, '', $titulo );
		$ws->write_string( $mm+1, $mcel, 'N. Fiscal', $titulo );
		$ws->write_string( $mm+2, $mcel, '', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, '', $titulo );
		$ws->write_string( $mm+1, $mcel, 'Afecta', $titulo );
		$ws->write_string( $mm+2, $mcel, '', $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, '', $titulo );
		$ws->write_string( $mm+1, $mcel, 'Serial', $titulo );
		$ws->write_string( $mm+2, $mcel, '', $titulo );
		$mcel++;

		$mm +=3;
		$ii = $mm+1;
		$mtiva   = 'X';
		$mfecha  = '2000-00-00';
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

		if ( $export->num_rows() > 0 ){
			foreach( $export->result() as $row ){
				// imprime contribuyente
				$fecha = substr($row->fecha,8,2).'/'.substr($row->fecha,5,2).'/'.substr($row->fecha,0,4);

				$ws->write_string( $mm, 0, $fecha,  $cuerpo );				// Fecha
				$ws->write_string( $mm, 1, ' ', $cuerpo );			// Numero de Caja

				if ($row->tipo[0] == 'X' )
					$ws->write_string( $mm, 2, 'FC', $cuerpoc );		// TIPO
				elseif ( $row->tipo == 'XC' )
					$ws->write_string( $mm, 2, 'NC', $cuerpoc );		// TIPO
				elseif ( $row->tipo == 'FE' )
					$ws->write_string( $mm, 2, 'FC', $cuerpoc );		// TIPO
				else
					$ws->write_string( $mm, 2, $row->tipo, $cuerpoc );	// TIPO

				$ws->write_string( $mm, 3, $row->numero, $cuerpo );		// Nro. Documento
				$ws->write_string( $mm, 4, $row->inicial, $cuerpo );	// INICIAL
				$ws->write_string( $mm, 5, $row->final, $cuerpo );		// FINAL

				if ($row->tipo[0] == 'X' )
					$ws->write_string( $mm, 6, 'DOCUMENTO ANULADO', $cuerpo );			// NOMBRE
				else
					$ws->write_string( $mm, 6, $row->nombre, $cuerpo );			// NOMBRE
				$ws->write_string( $mm, 7, $row->rif, $cuerpo );			// CONTRIBUYENTE

				if($row->registro=='04'){
					$ws->write_number( $mm, 8, 0, $numero );		// VENTAS + IVA
					$ws->write_number( $mm, 9, 0, $numero );		// VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );		// EXPORTACION
					$ws->write_number( $mm,11, 0, $numero );		// GENERAL
					$ws->write_number( $mm,12, 0, $numero );		// GENEIMPU
					$ws->write_number( $mm,13, 0, $numero );		// ADICIONAL
					$ws->write_number( $mm,14, 0, $numero );		// ADICIMPU
					$ws->write_number( $mm,15, 0, $numero );		// REDUCIDA
					$ws->write_number( $mm,16, 0, $numero );		// REDUIMPU
					$ws->write_number( $mm,17, $row->geneimpu + $row->adicimpu+$row->reduimpu, $numero );		// REDUIMPU
				}else{
					$ws->write_number( $mm, 8, $row->ventatotal, $numero );	// VENTAS + IVA
					$ws->write_number( $mm, 9, $row->exento, $numero );   	// VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );   					    // EXPORTACION
					$ws->write_number( $mm,11, $row->general, $numero );		// GENERAL
					$ws->write_number( $mm,12, $row->geneimpu, $numero );		// GENEIMPU
					$ws->write_number( $mm,13, $row->adicional, $numero );	// ADICIONAL
					$ws->write_number( $mm,14, $row->adicimpu, $numero );		// ADICIMPU
					$ws->write_number( $mm,15, $row->reducida, $numero );		// REDUCIDA
					$ws->write_number( $mm,16, $row->reduimpu, $numero );		// REDUIMPU
					$ws->write_number( $mm,17, 0, $numero );		// REDUIMPU
				}

				$ws->write_number( $mm,18, $row->reiva, $numero );		    // IVA RETENIDO
				$ws->write_string( $mm,19, $row->comprobante, $cuerpo );	// NRO COMPROBANTE
				$ws->write_string( $mm,22, $row->referen, $numero );
				$fecharece = '';
				if(!empty($row->fecharece))
					$fecharece = substr($row->fecharece,8,2).'/'.substr($row->fecharece,5,2).'/'.substr($row->fecharece,0,4);
				$ws->write_string( $mm,20, $fecharece, $cuerpo );	// FECHA COMPROB

				$ws->write_string( $mm,21, $row->afecta , $numero );
				/*if($row->tipo=='CR'){
					$ws->write_string( $mm,21, $row->afecta , $numero ); //NRO FACT AFECTA
				}else{
					$ws->write_string( $mm,21, $row->nfiscal, $numero ); //NRO FACT AFECTA
				}*/
				$ws->write_string( $mm,23, $row->serial , $numero );

				$mm++;
			}
		}

		//Imprime el Ultimo
		$celda = $mm+1;

		$fventas = "=I$celda";   // VENTAS
		$fexenta = "=J$celda";   // VENTAS EXENTAS

		$ffob    = "=K$celda";   // BASE IMPONIBLE

		$fgeneral  = "=L$celda";   // general
		$fgeneimpu = "=M$celda";   // general

		$fadicional = "=N$celda";   // general
		$fadicimpu  = "=O$celda";   // general

		$freducida = "=P$celda";   // general
		$freduimpu = "=Q$celda";   // general

		$fivaret   = "=R$celda";   // general
		//$fivaperu  = "=U$celda";   // general

		$fivajuste = "=S$celda";   // general

		$ws->write( $mm, 0,'Totales...',  $titulo );
		$ws->write_blank( $mm, 1,  $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm, 7,  $titulo );

		$ws->write_formula( $mm, 8, "=SUM(I$ii:I$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 9, "=SUM(J$ii:J$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,10, "=SUM(K$ii:K$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,11, "=SUM(L$ii:L$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,13, "=SUM(N$ii:N$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,16, "=SUM(Q$ii:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,17, "=SUM(R$ii:R$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,18, "=SUM(S$ii:S$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_blank( $mm, 19,  $Tnumero );
		$ws->write_blank( $mm, 20,  $Tnumero );
		$ws->write_blank( $mm, 21,  $titulo  );
		$ws->write_blank( $mm, 22,  $titulo  );
		$ws->write_blank( $mm, 23,  $titulo  );

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'RESUMEN DE VENTAS Y DEBITOS:', $titulo );
		$ws->write_blank( $mm+1, 1,  $titulo );
		$ws->write_blank( $mm  , 2,  $titulo );
		$ws->write_blank( $mm+1, 2,  $titulo );
		$ws->write_blank( $mm  , 3,  $titulo );
		$ws->write_blank( $mm+1, 3,  $titulo );
		$ws->write_blank( $mm  , 4,  $titulo );
		$ws->write_blank( $mm+1, 4,  $titulo );
		$ws->write_blank( $mm  , 5,  $titulo );
		$ws->write_blank( $mm+1, 5,  $titulo );
		$ws->write_blank( $mm  , 6,  $titulo );
		$ws->write_blank( $mm+1, 6,  $titulo );

		$ws->write($mm, 7, 'Items', $titulo );
		$ws->write_blank( $mm+1, 7,  $titulo );

		$ws->write($mm,   8, 'Base', $titulo );
		$ws->write($mm+1, 8, 'Imponible', $titulo );

		$ws->write($mm   , 9, 'Items', $titulo );
		$ws->write( $mm+1, 9, "  ", $titulo );

		$ws->write($mm,  10, 'Debito', $titulo );
		$ws->write($mm+1,10, 'Fiscal', $titulo );

		$ws->write($mm, 16, 'LEYENDAS:', $titulo );
		$ws->write_blank( $mm+1, 16,  $titulo );

		$ws->write_blank( $mm,   17,  $titulo );
		$ws->write_blank( $mm+1, 17,  $titulo );

		$ws->write($mm,   18, 'Tipo de Contribuyente',  $titulo );
		$ws->write($mm+1, 18, 'CO -Contribuyente',$cuerpo );

		$ws->write_blank( $mm,   19, $cuerpob );
		$ws->write_blank( $mm+1, 19, $cuerpo );

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas no Gravadas:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, '40' , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda" , $Rnumero );
		$ws->write($mm, 16, 'Tipo de Documento', $cuerpob );
		$ws->write_blank( $mm, 17,  $cuerpob );
		$ws->write($mm, 18, 'NO -No Contribuyente', $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas de Exportacion:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, '41' , $cuerpoc );
		$ws->write_formula($mm, 8, "=K$celda" , $Rnumero );
		$ws->write($mm, 16, 'FC -Factura', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, 'Tipo de Transaccion', $titulo );
		$ws->write_blank( $mm, 19,  $cuerpob );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Gravadas Alicuota General:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "42" , $cuerpoc );
		$ws->write_formula($mm, 8, "=L$celda" , $Rnumero );
		$ws->write($mm, 9, "43" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda" , $Rnumero );

		$ws->write($mm, 16, 'FE -Factura de Exportacion', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '01 -Registro', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota General + Adicional:', $h1 );
		$ws->write_blank(  $mm, 2,  $h1 );
		$ws->write_blank(  $mm, 3,  $h1 );
		$ws->write_blank(  $mm, 4,  $h1 );
		$ws->write_blank(  $mm, 5,  $h1 );
		$ws->write_blank(  $mm, 6,  $h1 );
		$ws->write($mm, 7, "442" , $cuerpoc );
		$ws->write_formula($mm, 8, "=N$celda" , $Rnumero );
		$ws->write($mm, 9, "452" , $cuerpoc );
		$ws->write_formula($mm, 10, "=O$celda" , $Rnumero );

		$ws->write($mm, 16, 'NC -Nota de Credito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '02 -Complemento', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota Reducida:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "443" , $cuerpoc );
		$ws->write_formula($mm, 8, "=P$celda" , $Rnumero );
		$ws->write($mm, 9, "453" , $cuerpoc );
		$ws->write_formula($mm, 10, "=Q$celda", $Rnumero );

		$ws->write($mm, 16, 'ND -Nota de Debito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '03 -Anulacion', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas y Creditos Fiscales para efectos de determinacion:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write($mm, 7, "46" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda+K$celda+L$celda+N$celda+P$celda" , $Rnumero );
		$ws->write($mm, 9, "47" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda+O$celda+Q$celda", $Rnumero );

		$ws->write($mm, 16, 'CR -Comprobante Ret.', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '04 -Ajuste', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total IVA retenido por el Comprador:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "66" , $cuerpoc );
		$ws->write_formula($mm, 10, "=S$celda", $Rnumero );

		$mm ++;
		$mm ++;
		$ws->write($mm, 1, 'Total Ajustes a los debitos fiscales de periodos Anteriores:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "48" , $cuerpoc );
		$ws->write_formula($mm, 10, "=R$celda", $Rnumero );

		$wb->close();

		header("Content-type: application/x-msexcel; name=\"lventas.xls\"");
		header("Content-Disposition: inline; filename=\"lventas.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
	}

	function wlvexcelfiscal($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');

		$aaa = $this->datasis->ivaplica($mes."02");
		$tasa      = $aaa['tasa'];
		$redutasa  = $aaa['redutasa'];
		$sobretasa = $aaa['sobretasa'];

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$this->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE siva SET tipo='FC' WHERE tipo='FE' ";
		$this->db->simple_query($mSQL);

		$mSQL  = "SELECT
				a.fecha,
				a.numero, '' inicial, ' ' final,
				a.nfiscal,
				a.rif,
				IF( b.nomfis IS NOT NULL AND b.nomfis!='',b.nomfis,b.nombre) AS nombre,
				a.tipo,
				IF(a.referen=a.numero,'        ',a.referen) afecta,
				a.gtotal*IF(a.tipo='NC',-1,1)    ventatotal,
				a.exento*IF(a.tipo='NC',-1,1)    exento,
				a.general*IF(a.tipo='NC',-1,1)   base,
				a.impuesto*IF(a.tipo='NC',-1,1)  impuesto,
				a.reiva*IF(a.tipo='NC',-1,1)     reiva,
				a.general*IF(a.tipo='NC',-1,1)	 general,
				a.geneimpu*IF(a.tipo='NC',-1,1)  geneimpu,
				a.adicional*IF(a.tipo='NC',-1,1) adicional,
				a.adicimpu*IF(a.tipo='NC',-1,1)  adicimpu,
				a.reducida*IF(a.tipo='NC',-1,1)  reducida,
				a.reduimpu*IF(a.tipo='NC',-1,1)  reduimpu,
				a.contribu, a.registro, a.comprobante, a.fecharece
			FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
			WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.tipo<>'FA' AND a.contribu='CO'
			UNION
			SELECT
				a.fecha,
				' ' numero, min(a.numero) inicial, max(a.numero) final,
				' ' nfiscal,
				' ' rif,
				'A NO CONTRIBUYENTES TOTAL DEL DIA' nombre,
				a.tipo,
				' ' afecta,
				sum(a.gtotal*IF(a.tipo='NC',-1,1))    ventatotal,
				sum(a.exento*IF(a.tipo='NC',-1,1))    exento,
				sum(a.general*IF(a.tipo='NC',-1,1))   base,
				sum(a.impuesto*IF(a.tipo='NC',-1,1))  impuesto,
				sum(a.reiva*IF(a.tipo='NC',-1,1))     reiva,
				sum(a.general*IF(a.tipo='NC',-1,1))	  general,
				sum(a.geneimpu*IF(a.tipo='NC',-1,1))  geneimpu,
				sum(a.adicional*IF(a.tipo='NC',-1,1)) adicional,
				sum(a.adicimpu*IF(a.tipo='NC',-1,1))  adicimpu,
				sum(a.reducida*IF(a.tipo='NC',-1,1))  reducida,
				sum(a.reduimpu*IF(a.tipo='NC',-1,1))  reduimpu,
				'NO' contribu, '01' registro, ' ' comprobante, null fecharece
			FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
			WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.tipo<>'FA' AND a.contribu='NO' AND a.tipo IN ('FE','FC','NC')
			GROUP BY a.fecha, a.tipo
			ORDER BY fecha, IF(tipo IN ('FE','FC','XE','XC'),1,2), numero ";
		$export = $this->db->query($mSQL);

		$fname = tempnam("/tmp","lventas.xls");
		$this->load->library("workbook",array("fname" => $fname));;
		$wb =& $this->workbook;
		$ws =& $wb->addworksheet($mes);

		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:F',11);
		$ws->set_column('G:G',37);
		$ws->set_column('H:U',11);

		// FORMATOS
		$h      =& $wb->addformat(array( "bold" => 1, "size" => 16, "merge" => 1));
		$h1     =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'left'));

		$titulo =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 0, "fg_color" => 'silver' ));
		$tt =& $wb->addformat(array( "size" => 9, "merge" => 1, "fg_color" => 'silver' ));

		$cuerpo  =& $wb->addformat(array( "size" => 9 ));
		$cuerpoc =& $wb->addformat(array( "size" => 9, "align" => 'center', "merge" => 1 ));
		$cuerpob =& $wb->addformat(array( "size" => 9, "align" => 'center', "bold" => 1, "merge" => 1 ));

		$numero  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));

		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = "LIBRO DE VENTAS CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL ".substr($mes,0,4);

		$ws->write(1, 0, $this->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$this->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<20; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};

		$mm=6;
		$mcel = 0;
		// TITULOS
		$ws->write_string( $mm,   $mcel, "", $titulo );
		$ws->write_string( $mm+1, $mcel, "Fecha", $titulo );
		$ws->write_string( $mm+2, $mcel, "", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Identificacion del Documento", $titulo );
		$ws->write_string( $mm+1, $mcel, "Nro.", $titulo );
		$ws->write_string( $mm+2, $mcel, "Caja", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel,  $titulo );
		$ws->write_string( $mm+1, $mcel, "Tipo", $titulo );
		$ws->write_string( $mm+2, $mcel, "Doc.", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, "Contribuyentes", $titulo );
		$ws->write_string( $mm+2, $mcel, "Numero", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, "No Contribuyentes", $titulo );
		$ws->write_string( $mm+2, $mcel, "Inicial", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_blank( $mm+1, $mcel, $titulo );
		$ws->write_string( $mm+2, $mcel, "Final", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Nombre, Razon Social", $titulo );
		$ws->write_string( $mm+1, $mcel, "o Denominacion del ", $titulo );
		$ws->write_string( $mm+2, $mcel, "Comprador", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Numero", $titulo );
		$ws->write_string( $mm+1, $mcel, "del", $titulo );
		$ws->write_string( $mm+2, $mcel, "R.I.F.", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Total Ventas", $titulo );
		$ws->write_string( $mm+1, $mcel, "Incluyendo el", $titulo );
		$ws->write_string( $mm+2, $mcel, "I.V.A.", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Ventas", $titulo );
		$ws->write_string( $mm+1, $mcel, "Exentas o", $titulo );
		$ws->write_string( $mm+2, $mcel, "no Sujetas", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Valor", $titulo );
		$ws->write_string( $mm+1,$mcel, "FOB Op.", $titulo );
		$ws->write_string( $mm+2,$mcel, "Export", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "VENTAS GRAVADAS", $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA GENERAL $tasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, "Base", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, "Impuesto", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA ADICIONAL $sobretasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, "Base", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, "Impuesto", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA REDUCIDA $redutasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, "Base", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, "Impuesto", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Ajuste a los", $titulo );
		$ws->write_string( $mm+1,$mcel, "DB Fiscales", $titulo );
		$ws->write_string( $mm+2,$mcel, "Per. Anterior", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "I.V.A. ", $titulo );
		$ws->write_string( $mm+1,$mcel, "Retenido", $titulo );
		$ws->write_string( $mm+2,$mcel, "Comprador", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Numero ", $titulo );
		$ws->write_string( $mm+1,$mcel, "de", $titulo );
		$ws->write_string( $mm+2,$mcel, "Comp.", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Fecha ", $titulo );
		$ws->write_string( $mm+1,$mcel, "de", $titulo );
		$ws->write_string( $mm+2,$mcel, "Recepcion", $titulo );
		$mcel++;

		$mm +=3;
		$ii = $mm+1;
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

		if ( $export->num_rows() > 0 ){
			foreach( $export->result() as $row ){
				// imprime contribuyente
				$fecha = substr($row->fecha,8,2)."/".substr($row->fecha,5,2)."/".substr($row->fecha,0,4);

				$ws->write_string( $mm, 0, $fecha,  $cuerpo );				// Fecha
				$ws->write_string( $mm, 1, ' ', $cuerpo );			// Numero de Caja

				if ($row->tipo[0] == "X" )
					$ws->write_string( $mm, 2, "FC", $cuerpoc );		// TIPO
				elseif ( $row->tipo == "XC" )
					$ws->write_string( $mm, 2, "NC", $cuerpoc );		// TIPO
				elseif ( $row->tipo == "FE" )
					$ws->write_string( $mm, 2, "FC", $cuerpoc );		// TIPO
				else
					$ws->write_string( $mm, 2, $row->tipo, $cuerpoc );	// TIPO

				$ws->write_string( $mm, 3, ($row->tipo=='CR')? '': $row->numero, $cuerpo );		// Nro. Documento
				$ws->write_string( $mm, 4, $row->inicial, $cuerpo );	// INICIAL
				$ws->write_string( $mm, 5, $row->final, $cuerpo );		// FINAL

				if ($row->tipo[0] == "X" )
					$ws->write_string( $mm, 6, "DOCUMENTO ANULADO", $cuerpo );	// NOMBRE
				else
					$ws->write_string( $mm, 6, $row->nombre, $cuerpo );		// NOMBRE
				$ws->write_string( $mm, 7, $row->rif, $cuerpo );			// CONTRIBUYENTE

				if ($row->registro=='04') {
					$ws->write_number( $mm, 8, 0, $numero );		// VENTAS + IVA
					$ws->write_number( $mm, 9, 0, $numero );		// VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );		// EXPORTACION
					$ws->write_number( $mm,11, 0, $numero );		// GENERAL
					$ws->write_number( $mm,12, 0, $numero );		// GENEIMPU
					$ws->write_number( $mm,13, 0, $numero );		// ADICIONAL
					$ws->write_number( $mm,14, 0, $numero );		// ADICIMPU
					$ws->write_number( $mm,15, 0, $numero );		// REDUCIDA
					$ws->write_number( $mm,16, 0, $numero );		// REDUIMPU
					$ws->write_number( $mm,17, $row->geneimpu + $row->adicimpu+$row->reduimpu, $numero );		// REDUIMPU
				}else{
					$ws->write_number( $mm, 8, $row->ventatotal, $numero );	// VENTAS + IVA
					$ws->write_number( $mm, 9, $row->exento, $numero );   	// VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );   		// EXPORTACION
					$ws->write_number( $mm,11, $row->general, $numero );	// GENERAL
					$ws->write_number( $mm,12, $row->geneimpu, $numero );	// GENEIMPU
					$ws->write_number( $mm,13, $row->adicional, $numero );	// ADICIONAL
					$ws->write_number( $mm,14, $row->adicimpu, $numero );	// ADICIMPU
					$ws->write_number( $mm,15, $row->reducida, $numero );	// REDUCIDA
					$ws->write_number( $mm,16, $row->reduimpu, $numero );	// REDUIMPU
					$ws->write_number( $mm,17, 0, $numero );		// REDUIMPU
				}

				$ws->write_number( $mm,18, $row->reiva, $numero );		// IVA RETENIDO
				$ws->write_string( $mm,19, ($row->tipo=='CR')? $row->numero: '', $cuerpo );	// NRO COMPROBANTE
				$fecharece = '';
				if ( !empty($row->fecharece) )
				$fecharece = substr($row->fecharece,8,2)."/".substr($row->fecharece,5,2)."/".substr($row->fecharece,0,4);
				$ws->write_string( $mm,20, $fecharece, $cuerpo );	// FECHA COMPROB
				$mm++;
			}
		}

		//Imprime el Ultimo
		$celda = $mm+1;

		$fventas    = "=I$celda";   // VENTAS
		$fexenta    = "=J$celda";   // VENTAS EXENTAS
		$ffob       = "=K$celda";   // BASE IMPONIBLE
		$fgeneral   = "=L$celda";   // general
		$fgeneimpu  = "=M$celda";   // general
		$fadicional = "=N$celda";   // general
		$fadicimpu  = "=O$celda";   // general
		$freducida  = "=P$celda";   // general
		$freduimpu  = "=Q$celda";   // general
		$fivaret    = "=R$celda";   // general
		//$fivaperu  = "=U$celda";   // general

		$fivajuste = "=S$celda";   // general

		$ws->write( $mm, 0,"Totales...",  $titulo );
		$ws->write_blank( $mm, 1,  $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm, 7,  $titulo );

		$ws->write_formula( $mm, 8, "=SUM(I$ii:I$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 9, "=SUM(J$ii:J$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,10, "=SUM(K$ii:K$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,11, "=SUM(L$ii:L$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,13, "=SUM(N$ii:N$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,16, "=SUM(Q$ii:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,17, "=SUM(R$ii:R$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,18, "=SUM(S$ii:S$mm)", $Tnumero );   //"BASE IMPONIBLE"

		//$ws->write_blank( $mm, 18,  $Tnumero );
		$ws->write_blank( $mm, 19,  $Tnumero );
		$ws->write_blank( $mm, 20,  $Tnumero );
		//$ws->write_formula( $mm,20, "=SUM(U$ii:U$mm)", $Tnumero );   //IMPUESTO PERCIBIDO

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'RESUMEN DE VENTAS Y DEBITOS:', $titulo );
		$ws->write_blank( $mm+1, 1,  $titulo );

		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm+1, 2,  $titulo );

		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm+1, 3,  $titulo );

		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm+1, 4,  $titulo );

		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm+1, 5,  $titulo );

		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm+1, 6,  $titulo );

		$ws->write($mm, 7, 'Items', $titulo );
		$ws->write_blank( $mm+1, 7,  $titulo );

		$ws->write($mm,   8, 'Base', $titulo );
		$ws->write($mm+1, 8, 'Imponible', $titulo );

		$ws->write($mm, 9, 'Items', $titulo );
		$ws->write( $mm+1, 9, "  ", $titulo );

		$ws->write($mm,  10, 'Debito', $titulo );
		$ws->write($mm+1,10, 'Fiscal', $titulo );

		$ws->write($mm, 16, 'LEYENDAS:', $titulo );
		$ws->write_blank( $mm+1, 16,  $titulo );

		$ws->write_blank( $mm,   17,  $titulo );
		$ws->write_blank( $mm+1, 17,  $titulo );

		$ws->write($mm,   18, 'Tipo de Contribuyente',  $titulo );
		$ws->write($mm+1, 18, 'CO -Contribuyente',$cuerpo );

		$ws->write_blank( $mm,   19, $cuerpob );
		$ws->write_blank( $mm+1, 19, $cuerpo );

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas no Gravadas:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "40" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda" , $Rnumero );
		$ws->write($mm, 16, 'Tipo de Documento', $cuerpob );
		$ws->write_blank( $mm, 17,  $cuerpob );
		$ws->write($mm, 18, 'NO -No Contribuyente', $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas de Exportacion:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "41" , $cuerpoc );
		$ws->write_formula($mm, 8, "=K$celda" , $Rnumero );
		$ws->write($mm, 16, 'FC -Factura', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, 'Tipo de Transaccion', $titulo );
		$ws->write_blank( $mm, 19,  $cuerpob );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Gravadas Alicuota General:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "42" , $cuerpoc );
		$ws->write_formula($mm, 8, "=L$celda" , $Rnumero );
		$ws->write($mm, 9, "43" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda" , $Rnumero );

		$ws->write($mm, 16, 'FE -Factura de Exportacion', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '01 -Registro', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota General + Adicional:', $h1 );
		$ws->write_blank(  $mm, 2,  $h1 );
		$ws->write_blank(  $mm, 3,  $h1 );
		$ws->write_blank(  $mm, 4,  $h1 );
		$ws->write_blank(  $mm, 5,  $h1 );
		$ws->write_blank(  $mm, 6,  $h1 );
		$ws->write($mm, 7, "442" , $cuerpoc );
		$ws->write_formula($mm, 8, "=N$celda" , $Rnumero );
		$ws->write($mm, 9, "452" , $cuerpoc );
		$ws->write_formula($mm, 10, "=O$celda" , $Rnumero );

		$ws->write($mm, 16, 'NC -Nota de Credito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '02 -Complemento', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota Reducida:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "443" , $cuerpoc );
		$ws->write_formula($mm, 8, "=P$celda" , $Rnumero );
		$ws->write($mm, 9, "453" , $cuerpoc );
		$ws->write_formula($mm, 10, "=Q$celda", $Rnumero );

		$ws->write($mm, 16, 'ND -Nota de Debito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '03 -Anulacion', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas y Creditos Fiscales para efectos de determinacion:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write($mm, 7, "46" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda+K$celda+L$celda+N$celda+P$celda" , $Rnumero );
		$ws->write($mm, 9, "47" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda+O$celda+Q$celda", $Rnumero );

		$ws->write($mm, 16, 'CR -Comprobante de Retencion', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '04 -Ajuste', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total IVA retenido por el Comprador:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "66" , $cuerpoc );
		$ws->write_formula($mm, 10, "=S$celda", $Rnumero );

		$mm ++;
		$mm ++;
		$ws->write($mm, 1, 'Total Ajustes a los debitos fiscales de periodos Anteriores:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "48" , $cuerpoc );
		$ws->write_formula($mm, 10, "=R$celda", $Rnumero );
		$wb->close();

		header("Content-type: application/x-msexcel; name=\"lventas.xls\"");
		header("Content-Disposition: inline; filename=\"lventas.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
	}

	//libro de ventas separado por sucursal
	function wlvexcelsucu($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');

		$aaa = $this->datasis->ivaplica($mes."02");
		$tasa      = $aaa['tasa'];
		$redutasa  = $aaa['redutasa'];
		$sobretasa = $aaa['sobretasa'];

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$this->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$this->db->simple_query($mSQL);

		$mSQL = "UPDATE siva SET tipo='FC' WHERE tipo='FE' ";
		$this->db->simple_query($mSQL);

		$mSQL  = "SELECT
				a.fecha,
				a.numero, '' inicial, ' ' final,
				a.nfiscal,
				a.rif,
				IF( b.nomfis IS NOT NULL AND b.nomfis!='',b.nomfis,b.nombre) AS nombre,
				a.tipo,
				IF(a.referen=a.numero,'        ',a.referen) afecta,
				a.gtotal*IF(a.tipo='NC',-1,1)    ventatotal,
				a.exento*IF(a.tipo='NC',-1,1)    exento,
				a.general*IF(a.tipo='NC',-1,1)   base,
				a.impuesto*IF(a.tipo='NC',-1,1)  impuesto,
				a.reiva*IF(a.tipo='NC',-1,1)     reiva,
				a.general*IF(a.tipo='NC',-1,1)	 general,
				a.geneimpu*IF(a.tipo='NC',-1,1)  geneimpu,
				a.adicional*IF(a.tipo='NC',-1,1) adicional,
				a.adicimpu*IF(a.tipo='NC',-1,1)  adicimpu,
				a.reducida*IF(a.tipo='NC',-1,1)  reducida,
				a.reduimpu*IF(a.tipo='NC',-1,1)  reduimpu,
				a.contribu, a.registro, a.comprobante, a.fecharece
			FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
			WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.tipo<>'FA' AND a.contribu='CO'
			UNION
			SELECT
				a.fecha,
				' ' numero, min(a.numero) inicial, max(a.numero) final,
				' ' nfiscal,
				' ' rif,
				'A NO CONTRIBUYENTES TOTAL DEL DIA' nombre,
				a.tipo,
				' ' afecta,
				sum(a.gtotal*IF(a.tipo='NC',-1,1))    ventatotal,
				sum(a.exento*IF(a.tipo='NC',-1,1))    exento,
				sum(a.general*IF(a.tipo='NC',-1,1))   base,
				sum(a.impuesto*IF(a.tipo='NC',-1,1))  impuesto,
				sum(a.reiva*IF(a.tipo='NC',-1,1))     reiva,
				sum(a.general*IF(a.tipo='NC',-1,1))	  general,
				sum(a.geneimpu*IF(a.tipo='NC',-1,1))  geneimpu,
				sum(a.adicional*IF(a.tipo='NC',-1,1)) adicional,
				sum(a.adicimpu*IF(a.tipo='NC',-1,1))  adicimpu,
				sum(a.reducida*IF(a.tipo='NC',-1,1))  reducida,
				sum(a.reduimpu*IF(a.tipo='NC',-1,1))  reduimpu,
				'NO' contribu, '01' registro, ' ' comprobante, null fecharece
			FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
			WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.tipo<>'FA' AND a.contribu='NO' AND a.tipo IN ('FE','FC','NC')
			GROUP BY MID(a.numero,1,1),a.fecha, a.tipo
			ORDER BY fecha, IF(tipo IN ('FE','FC','XE','XC'),1,2), numero ";

		$export = $this->db->query($mSQL);

		$fname = tempnam("/tmp","lventas.xls");
		$this->load->library("workbook",array("fname" => $fname));;
		$wb =& $this->workbook;
		$ws =& $wb->addworksheet($mes);

		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:F',11);
		$ws->set_column('G:G',37);
		$ws->set_column('H:U',11);

		// FORMATOS
		$h      =& $wb->addformat(array( "bold" => 1, "size" => 16, "merge" => 1));
		$h1     =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'left'));

		$titulo =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 0, "fg_color" => 'silver' ));
		$tt =& $wb->addformat(array( "size" => 9, "merge" => 1, "fg_color" => 'silver' ));

		$cuerpo  =& $wb->addformat(array( "size" => 9 ));
		$cuerpoc =& $wb->addformat(array( "size" => 9, "align" => 'center', "merge" => 1 ));
		$cuerpob =& $wb->addformat(array( "size" => 9, "align" => 'center', "bold" => 1, "merge" => 1 ));

		$numero  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));


		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = "LIBRO DE VENTAS CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL ".substr($mes,0,4);

		$ws->write(1, 0, $this->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$this->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<20; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};

		//$ws->write_string( $mm, $ii , $mvalor );

		$mm=6;
		$mcel = 0;
		// TITULOS
		$ws->write_string( $mm,   $mcel, "", $titulo );
		$ws->write_string( $mm+1, $mcel, "Fecha", $titulo );
		$ws->write_string( $mm+2, $mcel, "", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Identificacion del Documento", $titulo );
		$ws->write_string( $mm+1, $mcel, "Nro.", $titulo );
		$ws->write_string( $mm+2, $mcel, "Caja", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel,  $titulo );
		$ws->write_string( $mm+1, $mcel, "Tipo", $titulo );
		$ws->write_string( $mm+2, $mcel, "Doc.", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, "Contribuyentes", $titulo );
		$ws->write_string( $mm+2, $mcel, "Numero", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, "No Contribuyentes", $titulo );
		$ws->write_string( $mm+2, $mcel, "Inicial", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_blank( $mm+1, $mcel, $titulo );
		$ws->write_string( $mm+2, $mcel, "Final", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Nombre, Razon Social", $titulo );
		$ws->write_string( $mm+1, $mcel, "o Denominacion del ", $titulo );
		$ws->write_string( $mm+2, $mcel, "Comprador", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Numero", $titulo );
		$ws->write_string( $mm+1, $mcel, "del", $titulo );
		$ws->write_string( $mm+2, $mcel, "R.I.F.", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Total Ventas", $titulo );
		$ws->write_string( $mm+1, $mcel, "Incluyendo el", $titulo );
		$ws->write_string( $mm+2, $mcel, "I.V.A.", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Ventas", $titulo );
		$ws->write_string( $mm+1, $mcel, "Exentas o", $titulo );
		$ws->write_string( $mm+2, $mcel, "no Sujetas", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Valor", $titulo );
		$ws->write_string( $mm+1,$mcel, "FOB Op.", $titulo );
		$ws->write_string( $mm+2,$mcel, "Export", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "VENTAS GRAVADAS", $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA GENERAL $tasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, "Base", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, "Impuesto", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA ADICIONAL $sobretasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, "Base", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, "Impuesto", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA REDUCIDA $redutasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, "Base", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, "Impuesto", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Ajuste a los", $titulo );
		$ws->write_string( $mm+1,$mcel, "DB Fiscales", $titulo );
		$ws->write_string( $mm+2,$mcel, "Per. Anterior", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "I.V.A. ", $titulo );
		$ws->write_string( $mm+1,$mcel, "Retenido", $titulo );
		$ws->write_string( $mm+2,$mcel, "Comprador", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Numero ", $titulo );
		$ws->write_string( $mm+1,$mcel, "de", $titulo );
		$ws->write_string( $mm+2,$mcel, "Comp.", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Fecha ", $titulo );
		$ws->write_string( $mm+1,$mcel, "de", $titulo );
		$ws->write_string( $mm+2,$mcel, "Recepcion", $titulo );
		$mcel++;

		$mm +=3;
		$ii = $mm+1;
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

		if ( $export->num_rows() > 0 ){
			foreach( $export->result() as $row ){
				// imprime contribuyente
				$fecha = substr($row->fecha,8,2)."/".substr($row->fecha,5,2)."/".substr($row->fecha,0,4);

				$ws->write_string( $mm, 0, $fecha,  $cuerpo );				// Fecha
				$ws->write_string( $mm, 1, ' ', $cuerpo );			// Numero de Caja

				if ($row->tipo[0] == "X" )
					$ws->write_string( $mm, 2, "FC", $cuerpoc );		// TIPO
				elseif ( $row->tipo == "XC" )
					$ws->write_string( $mm, 2, "NC", $cuerpoc );		// TIPO
				elseif ( $row->tipo == "FE" )
					$ws->write_string( $mm, 2, "FC", $cuerpoc );		// TIPO
				else
					$ws->write_string( $mm, 2, $row->tipo, $cuerpoc );	// TIPO

				$ws->write_string( $mm, 3, $row->numero, $cuerpo );		// Nro. Documento
				$ws->write_string( $mm, 4, $row->inicial, $cuerpo );	// INICIAL
				$ws->write_string( $mm, 5, $row->final, $cuerpo );		// FINAL

				if ($row->tipo[0] == "X" )
					$ws->write_string( $mm, 6, "DOCUMENTO ANULADO", $cuerpo );			// NOMBRE
				else
					$ws->write_string( $mm, 6, $row->nombre, $cuerpo );			// NOMBRE
				$ws->write_string( $mm, 7, $row->rif, $cuerpo );			// CONTRIBUYENTE

				if ($row->registro=='04') {
					$ws->write_number( $mm, 8, 0, $numero );		// VENTAS + IVA
					$ws->write_number( $mm, 9, 0, $numero );    // VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );   	// EXPORTACION
					$ws->write_number( $mm,11, 0, $numero );		// GENERAL
					$ws->write_number( $mm,12, 0, $numero );		// GENEIMPU
					$ws->write_number( $mm,13, 0, $numero );		// ADICIONAL
					$ws->write_number( $mm,14, 0, $numero );		// ADICIMPU
					$ws->write_number( $mm,15, 0, $numero );		// REDUCIDA
					$ws->write_number( $mm,16, 0, $numero );		// REDUIMPU
					$ws->write_number( $mm,17, $row->geneimpu + $row->adicimpu+$row->reduimpu, $numero );		// REDUIMPU
				}else{
					$ws->write_number( $mm, 8, $row->ventatotal, $numero );	// VENTAS + IVA
					$ws->write_number( $mm, 9, $row->exento, $numero );   	// VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );   					    // EXPORTACION
					$ws->write_number( $mm,11, $row->general, $numero );		// GENERAL
					$ws->write_number( $mm,12, $row->geneimpu, $numero );		// GENEIMPU
					$ws->write_number( $mm,13, $row->adicional, $numero );	// ADICIONAL
					$ws->write_number( $mm,14, $row->adicimpu, $numero );		// ADICIMPU
					$ws->write_number( $mm,15, $row->reducida, $numero );		// REDUCIDA
					$ws->write_number( $mm,16, $row->reduimpu, $numero );		// REDUIMPU
					$ws->write_number( $mm,17, 0, $numero );		// REDUIMPU
				}

				$ws->write_number( $mm,18, $row->reiva, $numero );		    // IVA RETENIDO
				$ws->write_string( $mm,19, $row->comprobante, $cuerpo );	// NRO COMPROBANTE
				$fecharece = '';
				if ( !empty($row->fecharece) )
				$fecharece = substr($row->fecharece,8,2)."/".substr($row->fecharece,5,2)."/".substr($row->fecharece,0,4);
				$ws->write_string( $mm,20, $fecharece, $cuerpo );	// FECHA COMPROB
				$mm++;
			}
		}

		//Imprime el Ultimo
		$celda = $mm+1;

		$fventas = "=I$celda";   // VENTAS
		$fexenta = "=J$celda";   // VENTAS EXENTAS

		$ffob    = "=K$celda";   // BASE IMPONIBLE

		$fgeneral  = "=L$celda";   // general
		$fgeneimpu = "=M$celda";   // general

		$fadicional = "=N$celda";   // general
		$fadicimpu  = "=O$celda";   // general

		$freducida = "=P$celda";   // general
		$freduimpu = "=Q$celda";   // general

		$fivaret   = "=R$celda";   // general
		//$fivaperu  = "=U$celda";   // general

		$fivajuste = "=S$celda";   // general

		$ws->write( $mm, 0,"Totales...",  $titulo );
		$ws->write_blank( $mm, 1,  $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm, 7,  $titulo );

		$ws->write_formula( $mm, 8, "=SUM(I$ii:I$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 9, "=SUM(J$ii:J$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,10, "=SUM(K$ii:K$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,11, "=SUM(L$ii:L$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,13, "=SUM(N$ii:N$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,16, "=SUM(Q$ii:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,17, "=SUM(R$ii:R$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,18, "=SUM(S$ii:S$mm)", $Tnumero );   //"BASE IMPONIBLE"

		//$ws->write_blank( $mm, 18,  $Tnumero );
		$ws->write_blank( $mm, 19,  $Tnumero );
		$ws->write_blank( $mm, 20,  $Tnumero );
		//$ws->write_formula( $mm,20, "=SUM(U$ii:U$mm)", $Tnumero );   //IMPUESTO PERCIBIDO

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'RESUMEN DE VENTAS Y DEBITOS:', $titulo );
		$ws->write_blank( $mm+1, 1,  $titulo );

		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm+1, 2,  $titulo );

		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm+1, 3,  $titulo );

		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm+1, 4,  $titulo );

		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm+1, 5,  $titulo );

		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm+1, 6,  $titulo );

		$ws->write($mm, 7, 'Items', $titulo );
		$ws->write_blank( $mm+1, 7,  $titulo );

		$ws->write($mm,   8, 'Base', $titulo );
		$ws->write($mm+1, 8, 'Imponible', $titulo );

		$ws->write($mm, 9, 'Items', $titulo );
		$ws->write( $mm+1, 9, "  ", $titulo );

		$ws->write($mm,  10, 'Debito', $titulo );
		$ws->write($mm+1,10, 'Fiscal', $titulo );

		$ws->write($mm, 16, 'LEYENDAS:', $titulo );
		$ws->write_blank( $mm+1, 16,  $titulo );

		$ws->write_blank( $mm,   17,  $titulo );
		$ws->write_blank( $mm+1, 17,  $titulo );

		$ws->write($mm,   18, 'Tipo de Contribuyente',  $titulo );
		$ws->write($mm+1, 18, 'CO -Contribuyente',$cuerpo );

		$ws->write_blank( $mm,   19, $cuerpob );
		$ws->write_blank( $mm+1, 19, $cuerpo );

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas no Gravadas:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "40" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda" , $Rnumero );
		$ws->write($mm, 16, 'Tipo de Documento', $cuerpob );
		$ws->write_blank( $mm, 17,  $cuerpob );
		$ws->write($mm, 18, 'NO -No Contribuyente', $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas de Exportacion:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "41" , $cuerpoc );
		$ws->write_formula($mm, 8, "=K$celda" , $Rnumero );
		$ws->write($mm, 16, 'FC -Factura', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, 'Tipo de Transaccion', $titulo );
		$ws->write_blank( $mm, 19,  $cuerpob );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Gravadas Alicuota General:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "42" , $cuerpoc );
		$ws->write_formula($mm, 8, "=L$celda" , $Rnumero );
		$ws->write($mm, 9, "43" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda" , $Rnumero );

		$ws->write($mm, 16, 'FE -Factura de Exportacion', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '01 -Registro', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota General + Adicional:', $h1 );
		$ws->write_blank(  $mm, 2,  $h1 );
		$ws->write_blank(  $mm, 3,  $h1 );
		$ws->write_blank(  $mm, 4,  $h1 );
		$ws->write_blank(  $mm, 5,  $h1 );
		$ws->write_blank(  $mm, 6,  $h1 );
		$ws->write($mm, 7, "442" , $cuerpoc );
		$ws->write_formula($mm, 8, "=N$celda" , $Rnumero );
		$ws->write($mm, 9, "452" , $cuerpoc );
		$ws->write_formula($mm, 10, "=O$celda" , $Rnumero );

		$ws->write($mm, 16, 'NC -Nota de Credito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '02 -Complemento', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota Reducida:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "443" , $cuerpoc );
		$ws->write_formula($mm, 8, "=P$celda" , $Rnumero );
		$ws->write($mm, 9, "453" , $cuerpoc );
		$ws->write_formula($mm, 10, "=Q$celda", $Rnumero );

		$ws->write($mm, 16, 'ND -Nota de Debito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '03 -Anulacion', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas y Creditos Fiscales para efectos de determinacion:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write($mm, 7, "46" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda+K$celda+L$celda+N$celda+P$celda" , $Rnumero );
		$ws->write($mm, 9, "47" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda+O$celda+Q$celda", $Rnumero );

		$ws->write($mm, 16, 'CR -Comprobante Ret.', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '04 -Ajuste', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total IVA retenido por el Comprador:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "66" , $cuerpoc );
		$ws->write_formula($mm, 10, "=S$celda", $Rnumero );

		$mm ++;
		$mm ++;
		$ws->write($mm, 1, 'Total Ajustes a los debitos fiscales de periodos Anteriores:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "48" , $cuerpoc );
		$ws->write_formula($mm, 10, "=R$celda", $Rnumero );

		$wb->close();

		header("Content-type: application/x-msexcel; name=\"lventas.xls\"");
		header("Content-Disposition: inline; filename=\"lventas.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
	}

	//************************
	//LIBROS DE VENTAS FISCAL
	//************************

	function wlvexcel3($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		set_time_limit(300);
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');

		$aaa = $this->datasis->ivaplica($mes."02");
		$tasa      = $aaa['tasa'];
		$redutasa  = $aaa['redutasa'];
		$sobretasa = $aaa['sobretasa'];

		// ARREGLA SIVA PORSIA
		$mSQL = "UPDATE siva SET impuesto=0, geneimpu=0, exento=gtotal, stotal=gtotal, general=0 where geneimpu<0 and general>=0 ";
		$this->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET geneimpu=0, exento=exento+general, stotal=exento+general, general=0 WHERE geneimpu=0 and general<0  ";
		$this->db->simple_query($mSQL);
		$mSQL = "UPDATE siva SET tipo='FC' WHERE tipo='FE' ";
		$this->db->simple_query($mSQL);

		$mSQL  = "SELECT
				a.fecha,IF(a.tipo='NC',a.referen,'')referen,
				a.numero, '' inicial, ' ' final,
				a.nfiscal,
				a.rif,
				IF( b.nomfis IS NOT NULL AND b.nomfis!='',b.nomfis,b.nombre) AS nombre,
				a.tipo,
				IF(a.referen=a.numero,'        ',a.referen) afecta,
				a.gtotal*IF(a.tipo='NC',-1,1)    ventatotal,
				a.exento*IF(a.tipo='NC',-1,1)    exento,
				a.general*IF(a.tipo='NC',-1,1)   base,
				a.impuesto*IF(a.tipo='NC',-1,1)  impuesto,
				a.reiva*IF(a.tipo='NC',-1,1)     reiva,
				a.general*IF(a.tipo='NC',-1,1)   general,
				a.geneimpu*IF(a.tipo='NC',-1,1)  geneimpu,
				a.adicional*IF(a.tipo='NC',-1,1) adicional,
				a.adicimpu*IF(a.tipo='NC',-1,1)  adicimpu,
				a.reducida*IF(a.tipo='NC',-1,1)  reducida,
				a.reduimpu*IF(a.tipo='NC',-1,1)  reduimpu,
				a.contribu, a.registro, a.comprobante, a.fecharece,IF(c.tipo_doc ='D',(SELECT sfac.nfiscal FROM sfac WHERE c.factura=sfac.numero AND sfac.tipo_doc='F' LIMIT 1),'') afecta,c.maqfiscal
			FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
			JOIN sfac as c ON a.numero=c.numero AND a.tipo=IF(c.tipo_doc='F','FC','NC')
			WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.tipo<>'FA' AND a.contribu='CO'
			UNION
			SELECT
				a.fecha,IF(a.tipo='NC',a.referen,'')referen,
				' ' numero, min(a.nfiscal) inicial, max(a.nfiscal)  final,
				' ' nfiscal,
				' ' rif,
				IF(a.registro<>'04','A NO CONTRIBUYENTES TOTAL DEL DIA',b.nombre) nombre,
				IF(sum(a.gtotal*IF(a.tipo='NC',-1,1))>0,'FC','NC') tipo,
				' ' afecta,
				sum(a.gtotal*IF(a.tipo='NC',-1,1))    ventatotal,
				sum(a.exento*IF(a.tipo='NC',-1,1))    exento,
				sum(a.general*IF(a.tipo='NC',-1,1))   base,
				sum(a.impuesto*IF(a.tipo='NC',-1,1))  impuesto,
				sum(a.reiva*IF(a.tipo='NC',-1,1))     reiva,
				sum(a.general*IF(a.tipo='NC',-1,1))   general,
				sum(a.geneimpu*IF(a.tipo='NC',-1,1))  geneimpu,
				sum(a.adicional*IF(a.tipo='NC',-1,1)) adicional,
				sum(a.adicimpu*IF(a.tipo='NC',-1,1))  adicimpu,
				sum(a.reducida*IF(a.tipo='NC',-1,1))  reducida,
				sum(a.reduimpu*IF(a.tipo='NC',-1,1))  reduimpu,
				'NO' contribu, a.registro, ' ' comprobante, null fecharece,'' afecta,c.maqfiscal
			FROM siva a LEFT JOIN scli b ON a.clipro=b.cliente
			JOIN sfac as c ON a.numero=c.numero  AND a.tipo=IF(c.tipo_doc='F','FC','NC')
			WHERE a.fechal BETWEEN $fdesde AND $fhasta AND a.libro='V' AND a.tipo<>'FA' AND a.contribu='NO' AND a.tipo IN ('FE','FC','NC')
			GROUP BY a.fecha, a.registro,c.maqfiscal
			ORDER BY fecha, IF(tipo IN ('FE','FC','XE','XC'),1,2), numero ";
		//}
		$export = $this->db->query($mSQL);

		$fname = tempnam("/tmp","lventas.xls");
		$this->load->library("workbook",array("fname" => $fname));
		$wb =& $this->workbook;
		$ws =& $wb->addworksheet($mes);

		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:F',11);
		$ws->set_column('G:G',37);
		$ws->set_column('H:H',11);
		$ws->set_column('I:S',20);

		// FORMATOS
		$h      =& $wb->addformat(array( "bold" => 1, "size" => 16, "merge" => 1));
		$h1     =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'left'));

		$titulo =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 0, "fg_color" => 'silver' ));
		$tt =& $wb->addformat(array( "size" => 9, "merge" => 1, "fg_color" => 'silver' ));

		$cuerpo  =& $wb->addformat(array( "size" => 9 ));
		$cuerpoc =& $wb->addformat(array( "size" => 9, "align" => 'center', "merge" => 1 ));
		$cuerpob =& $wb->addformat(array( "size" => 9, "align" => 'center', "bold" => 1, "merge" => 1 ));

		$numero  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));

		// COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = "LIBRO DE VENTAS CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL ".substr($mes,0,4);

		$ws->write(1, 0, $this->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$this->datasis->traevalor('RIF') , $h1 );

		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i<20; $i++ ) {
			$ws->write_blank(4, $i,  $h );
		};

		$mm=6;
		$mcel = 0;
		// TITULOS

		$ws->write_string( $mm,   $mcel, "", $titulo );
		$ws->write_string( $mm+1, $mcel, "Fecha", $titulo );
		$ws->write_string( $mm+2, $mcel, "", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Identificacion del Documento", $titulo );
		$ws->write_string( $mm+1, $mcel, "Nro.", $titulo );
		$ws->write_string( $mm+2, $mcel, "Caja", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel,  $titulo );
		$ws->write_string( $mm+1, $mcel, "Tipo", $titulo );
		$ws->write_string( $mm+2, $mcel, "Doc.", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, "Contribuyentes", $titulo );
		$ws->write_string( $mm+2, $mcel, "Numero", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_string( $mm+1, $mcel, "No Contribuyentes", $titulo );
		$ws->write_string( $mm+2, $mcel, "Inicial", $titulo );
		$mcel++;

		$ws->write_blank( $mm,   $mcel, $titulo );
		$ws->write_blank( $mm+1, $mcel, $titulo );
		$ws->write_string( $mm+2, $mcel, "Final", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Nombre, Razon Social", $titulo );
		$ws->write_string( $mm+1, $mcel, "o Denominacion del ", $titulo );
		$ws->write_string( $mm+2, $mcel, "Comprador", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Numero", $titulo );
		$ws->write_string( $mm+1, $mcel, "del", $titulo );
		$ws->write_string( $mm+2, $mcel, "R.I.F.", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Total Ventas", $titulo );
		$ws->write_string( $mm+1, $mcel, "Incluyendo el", $titulo );
		$ws->write_string( $mm+2, $mcel, "I.V.A.", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "Ventas",    $titulo );
		$ws->write_string( $mm+1, $mcel, "Exentas o", $titulo );
		$ws->write_string( $mm+2, $mcel, "no Sujetas",$titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Valor", $titulo );
		$ws->write_string( $mm+1,$mcel, "FOB Op.", $titulo );
		$ws->write_string( $mm+2,$mcel, "Export", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "VENTAS GRAVADAS", $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA GENERAL $tasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, "Base", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, "Impuesto", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA ADICIONAL $sobretasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, "Base", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, "Impuesto", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_string( $mm+1,$mcel, "ALICUOTA REDUCIDA $redutasa%", $titulo );
		$ws->write_string( $mm+2,$mcel, "Base", $titulo );
		$mcel++;

		$ws->write_blank(  $mm,  $mcel, $titulo );
		$ws->write_blank(  $mm+1,$mcel, $titulo );
		$ws->write_string( $mm+2,$mcel, "Impuesto", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Ajuste a los", $titulo );
		$ws->write_string( $mm+1,$mcel, "DB Fiscales", $titulo );
		$ws->write_string( $mm+2,$mcel, "Per. Anterior", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "I.V.A. ", $titulo );
		$ws->write_string( $mm+1,$mcel, "Retenido", $titulo );
		$ws->write_string( $mm+2,$mcel, "Comprador", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Numero ", $titulo );
		$ws->write_string( $mm+1,$mcel, "de", $titulo );
		$ws->write_string( $mm+2,$mcel, "Comp.", $titulo );
		$mcel++;

		$ws->write_string( $mm,  $mcel, "Fecha ", $titulo );
		$ws->write_string( $mm+1,$mcel, "de", $titulo );
		$ws->write_string( $mm+2,$mcel, "Recepcion", $titulo );
		$mcel++;

		//$ws->write_string( $mm,   $mcel, "", $titulo );
		//$ws->write_string( $mm+1, $mcel, "N Fiscal", $titulo );
		//$ws->write_string( $mm+2, $mcel, "", $titulo );
		//$mcel++;

		$ws->write_string( $mm,   $mcel, "", $titulo );
		$ws->write_string( $mm+1, $mcel, "Afecta", $titulo );
		$ws->write_string( $mm+2, $mcel, "", $titulo );
		$mcel++;

		$ws->write_string( $mm,   $mcel, "", $titulo );
		$ws->write_string( $mm+1, $mcel, "Maquina", $titulo );
		$ws->write_string( $mm+2, $mcel, "Fiscal", $titulo );
		$mcel++;

		$mm +=3;
		$ii = $mm+1;
		$mtiva = 'X';
		$mfecha = '2000-00-00';
		$tventas=$texenta=$tbase=$timpue=$treiva=$tperci=$mforza=$contri=0;
		$finicial = '99999999';
		$ffinal   = '00000000';

		if ( $export->num_rows() > 0 ){
			foreach( $export->result() as $row ){
				// imprime contribuyente
				$fecha = substr($row->fecha,8,2)."/".substr($row->fecha,5,2)."/".substr($row->fecha,0,4);

				$ws->write_string( $mm, 0, $fecha,  $cuerpo );				// Fecha
				$ws->write_string( $mm, 1, ' ', $cuerpo );			// Numero de Caja

				if ($row->tipo[0] == "X" )
					$ws->write_string( $mm, 2, "FC", $cuerpoc );		// TIPO
				elseif ( $row->tipo == "XC" )
					$ws->write_string( $mm, 2, "NC", $cuerpoc );		// TIPO
				elseif ( $row->tipo == "FE" )
					$ws->write_string( $mm, 2, "FC", $cuerpoc );		// TIPO
				else
					$ws->write_string( $mm, 2, $row->tipo, $cuerpoc );	// TIPO

				$ws->write_string( $mm, 3, $row->nfiscal, $cuerpo );		// Nro. Control
				//$ws->write_string( $mm, 3, $row->numero, $cuerpo );		// Nro. Documento
				$ws->write_string( $mm, 4, $row->inicial, $cuerpo );	// INICIAL
				$ws->write_string( $mm, 5, $row->final, $cuerpo );		// FINAL

				if ($row->tipo[0] == "X" )
					$ws->write_string( $mm, 6, "DOCUMENTO ANULADO", $cuerpo );			// NOMBRE
				else
					$ws->write_string( $mm, 6, $row->nombre, $cuerpo );			// NOMBRE
				$ws->write_string( $mm, 7, $row->rif, $cuerpo );			// CONTRIBUYENTE

				if ( $row->registro=='04' ) {
					$ws->write_number( $mm, 8, 0, $numero );		// VENTAS + IVA
					$ws->write_number( $mm, 9, 0, $numero );		// VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );		// EXPORTACION
					$ws->write_number( $mm,11, 0, $numero );		// GENERAL
					$ws->write_number( $mm,12, 0, $numero );		// GENEIMPU
					$ws->write_number( $mm,13, 0, $numero );		// ADICIONAL
					$ws->write_number( $mm,14, 0, $numero );		// ADICIMPU
					$ws->write_number( $mm,15, 0, $numero );		// REDUCIDA
					$ws->write_number( $mm,16, 0, $numero );		// REDUIMPU
					$ws->write_number( $mm,17, $row->geneimpu + $row->adicimpu+$row->reduimpu, $numero );		// REDUIMPU
				} else {
					$ws->write_number( $mm, 8, $row->ventatotal, $numero );	// VENTAS + IVA
					$ws->write_number( $mm, 9, $row->exento, $numero );   	// VENTAS EXENTAS
					$ws->write_number( $mm,10, 0, $cuerpo );   					    // EXPORTACION
					$ws->write_number( $mm,11, $row->general, $numero );		// GENERAL
					$ws->write_number( $mm,12, $row->geneimpu, $numero );		// GENEIMPU
					$ws->write_number( $mm,13, $row->adicional, $numero );	// ADICIONAL
					$ws->write_number( $mm,14, $row->adicimpu, $numero );		// ADICIMPU
					$ws->write_number( $mm,15, $row->reducida, $numero );		// REDUCIDA
					$ws->write_number( $mm,16, $row->reduimpu, $numero );		// REDUIMPU
					$ws->write_number( $mm,17, 0, $numero );		// REDUIMPU
				}

				$ws->write_number( $mm,18, $row->reiva, $numero );		    // IVA RETENIDO
				$ws->write_string( $mm,19, $row->comprobante, $cuerpo );	// NRO COMPROBANTE
				//$ws->write_string( $mm,22, $row->referen, $numero ); //N� FACT AFECTA
				$fecharece = '';
				if ( !empty($row->fecharece) )
					$fecharece = substr($row->fecharece,8,2)."/".substr($row->fecharece,5,2)."/".substr($row->fecharece,0,4);
				$ws->write_string( $mm,20, $fecharece, $cuerpo );	// FECHA COMPROB
				//$ws->write_string( $mm,21, $row->nfiscal, $numero );
				$ws->write_string( $mm,21, $row->afecta, $numero );
				$ws->write_string( $mm,22, $row->maqfiscal, $numero );
				$mm++;
			}
		}

		//Imprime el Ultimo
		$celda = $mm+1;

		$fventas = "=I$celda";   // VENTAS
		$fexenta = "=J$celda";   // VENTAS EXENTAS

		$ffob    = "=K$celda";   // BASE IMPONIBLE

		$fgeneral  = "=L$celda";   // general
		$fgeneimpu = "=M$celda";   // general

		$fadicional = "=N$celda";   // general
		$fadicimpu  = "=O$celda";   // general

		$freducida = "=P$celda";   // general
		$freduimpu = "=Q$celda";   // general

		$fivaret   = "=R$celda";   // general
		//$fivaperu  = "=U$celda";   // general

		$fivajuste = "=S$celda";   // general

		$ws->write( $mm, 0,"Totales...",  $titulo );
		$ws->write_blank( $mm, 1,  $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write_blank( $mm, 7,  $titulo );

		$ws->write_formula( $mm, 8, "=SUM(I$ii:I$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm, 9, "=SUM(J$ii:J$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,10, "=SUM(K$ii:K$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,11, "=SUM(L$ii:L$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,12, "=SUM(M$ii:M$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,13, "=SUM(N$ii:N$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,14, "=SUM(O$ii:O$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula( $mm,15, "=SUM(P$ii:P$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula( $mm,16, "=SUM(Q$ii:Q$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_formula( $mm,17, "=SUM(R$ii:R$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula( $mm,18, "=SUM(S$ii:S$mm)", $Tnumero );   //"BASE IMPONIBLE"

		$ws->write_blank( $mm, 19,  $Tnumero );
		$ws->write_blank( $mm, 20,  $Tnumero );
		$ws->write_blank( $mm, 21,  $Tnumero );
		$ws->write_blank( $mm, 22,  $Tnumero );
		$ws->write_blank( $mm, 23,  $Tnumero );
		//$ws->write_formula( $mm,20, "=SUM(U$ii:U$mm)", $Tnumero );   //IMPUESTO PERCIBIDO

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'RESUMEN DE VENTAS Y DEBITOS:', $titulo );
		$ws->write_blank( $mm+1, 1,  $titulo );

		$ws->write_blank( $mm  , 2, $titulo );
		$ws->write_blank( $mm+1, 2, $titulo );

		$ws->write_blank( $mm  , 3, $titulo );
		$ws->write_blank( $mm+1, 3, $titulo );

		$ws->write_blank( $mm  , 4, $titulo );
		$ws->write_blank( $mm+1, 4, $titulo );

		$ws->write_blank( $mm  , 5, $titulo );
		$ws->write_blank( $mm+1, 5, $titulo );

		$ws->write_blank( $mm  , 6, $titulo );
		$ws->write_blank( $mm+1, 6, $titulo );

		$ws->write($mm, 7, 'Items', $titulo );
		$ws->write_blank( $mm+1, 7, $titulo );

		$ws->write($mm,   8, 'Base'     , $titulo );
		$ws->write($mm+1, 8, 'Imponible', $titulo );

		$ws->write($mm, 9, 'Items', $titulo );
		$ws->write( $mm+1, 9, "  ", $titulo );

		$ws->write($mm,  10, 'Debito', $titulo );
		$ws->write($mm+1,10, 'Fiscal', $titulo );

		$ws->write($mm, 16, 'LEYENDAS:', $titulo );
		$ws->write_blank( $mm+1, 16,  $titulo );

		$ws->write_blank( $mm,   17,  $titulo );
		$ws->write_blank( $mm+1, 17,  $titulo );

		$ws->write($mm,   18, 'Tipo de Contribuyente',  $titulo );
		$ws->write($mm+1, 18, 'CO -Contribuyente',$cuerpo );

		$ws->write_blank( $mm,   19, $cuerpob );
		$ws->write_blank( $mm+1, 19, $cuerpo );

		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas no Gravadas:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "40" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda" , $Rnumero );
		$ws->write($mm, 16, 'Tipo de Documento', $cuerpob );
		$ws->write_blank( $mm, 17,  $cuerpob );
		$ws->write($mm, 18, 'NO -No Contribuyente', $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas de Exportacion:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "41" , $cuerpoc );
		$ws->write_formula($mm, 8, "=K$celda" , $Rnumero );
		$ws->write($mm, 16, 'FC -Factura', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, 'Tipo de Transaccion', $titulo );
		$ws->write_blank( $mm, 19,  $cuerpob );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Gravadas Alicuota General:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "42" , $cuerpoc );
		$ws->write_formula($mm, 8, "=L$celda" , $Rnumero );
		$ws->write($mm, 9, "43" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda" , $Rnumero );

		$ws->write($mm, 16, 'FE -Factura de Exportacion', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '01 -Registro', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota General + Adicional:', $h1 );
		$ws->write_blank(  $mm, 2,  $h1 );
		$ws->write_blank(  $mm, 3,  $h1 );
		$ws->write_blank(  $mm, 4,  $h1 );
		$ws->write_blank(  $mm, 5,  $h1 );
		$ws->write_blank(  $mm, 6,  $h1 );
		$ws->write($mm, 7, "442" , $cuerpoc );
		$ws->write_formula($mm, 8, "=N$celda" , $Rnumero );
		$ws->write($mm, 9, "452" , $cuerpoc );
		$ws->write_formula($mm, 10, "=O$celda" , $Rnumero );

		$ws->write($mm, 16, 'NC -Nota de Credito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );

		$ws->write($mm, 18, '02 -Complemento', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas Internas Alicuota Reducida:', $h1 );
		$ws->write_blank( $mm, 2,  $h1 );
		$ws->write_blank( $mm, 3,  $h1 );
		$ws->write_blank( $mm, 4,  $h1 );
		$ws->write_blank( $mm, 5,  $h1 );
		$ws->write_blank( $mm, 6,  $h1 );
		$ws->write($mm, 7, "443" , $cuerpoc );
		$ws->write_formula($mm, 8, "=P$celda" , $Rnumero );
		$ws->write($mm, 9, "453" , $cuerpoc );
		$ws->write_formula($mm, 10, "=Q$celda", $Rnumero );

		$ws->write($mm, 16, 'ND -Nota de Debito', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '03 -Anulacion', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );

		$mm ++;

		$ws->write($mm, 1, 'Total Ventas y Creditos Fiscales para efectos de determinacion:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write($mm, 7, "46" , $cuerpoc );
		$ws->write_formula($mm, 8, "=J$celda+K$celda+L$celda+N$celda+P$celda" , $Rnumero );
		$ws->write($mm, 9, "47" , $cuerpoc );
		$ws->write_formula($mm, 10, "=M$celda+O$celda+Q$celda", $Rnumero );

		$ws->write($mm, 16, 'CR -Comprobante Ret.', $cuerpo );
		$ws->write_blank( $mm+1, 16,  $cuerpo );
		$ws->write($mm, 18, '04 -Ajuste', $cuerpo );
		$ws->write_blank( $mm, 19,  $cuerpo );
		$mm ++;
		$mm ++;

		$ws->write($mm, 1, 'Total IVA retenido por el Comprador:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "66" , $cuerpoc );
		$ws->write_formula($mm, 10, "=S$celda", $Rnumero );

		$mm ++;
		$mm ++;
		$ws->write($mm, 1, 'Total Ajustes a los debitos fiscales de periodos Anteriores:', $titulo );
		$ws->write_blank( $mm, 2,  $titulo );
		$ws->write_blank( $mm, 3,  $titulo );
		$ws->write_blank( $mm, 4,  $titulo );
		$ws->write_blank( $mm, 5,  $titulo );
		$ws->write_blank( $mm, 6,  $titulo );
		$ws->write( $mm, 7, ' ', $Rnumero );
		$ws->write_blank( $mm, 8,  $Rnumero );
		$ws->write($mm, 9, "48" , $cuerpoc );
		$ws->write_formula($mm, 10, "=R$celda", $Rnumero );

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

	function geneventasfiscal($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		$this->db->simple_query("DELETE FROM siva WHERE fechal = $fdesde AND fuente='FP'");

		$tasas = $this->_tasas($mes);
		$mivag = $tasas['general'];
		$mivar = $tasas['reducida'];
		$mivaa = $tasas['adicional'];

		$this->db->simple_query("UPDATE fiscalz SET caja='MAYO' WHERE caja='0001'");
		$this->db->simple_query("UPDATE fiscalz SET hora=CONCAT_WS(':',MINUTE(hora),SECOND(hora),'00') WHERE caja='MAYO' AND HOUR(hora)=0");

		$mSQL="SELECT
			caja,
			serial,
			numero,
			fecha,
			factura  AS factura,
			fecha1   AS fecha1,
			hora     AS hora,
			exento   AS exento,
			base     AS base,
			iva      AS iva,
			base1    AS base1,
			iva1     AS iva1,
			base2    AS base2,
			iva2     AS iva2,
			ncexento AS ncexento,
			ncbase   AS ncbase,
			nciva    AS nciva,
			ncbase1  AS ncbase1,
			nciva1   AS nciva1,
			ncbase2  AS ncbase2,
			nciva2   AS nciva2,
			MAX(ncnumero) AS ncnumero
		FROM fiscalz WHERE fecha BETWEEN $fdesde AND $fhasta";

		$query = $this->db->query($mSQL);
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
						$hdesde=$this->datasis->dameval("SELECT MAX(hora) FROM fiscalz WHERE fecha='{$row->fecha1}' AND serial='{$row->serial}'");
						if(empty($hora))
							$hdesde='0';
					}

					$cur=$this->datasis->damerow("SELECT MAX(factura) AS ff, MAX(ncnumero) AS nc FROM fiscalz WHERE fecha<'{$row->fecha}' AND serial='{$row->serial}'");
					if(count($cur)>0){
						$ncdesde =(empty($cur['nc'])) ? '00000001' : $cur['nc'];
						$ffdesde =(empty($cur['ff'])) ? '00000001' : $cur['ff'];
					}else{
						$ncdesde ='00000001';
						$ffdesde ='00000001';
					}
					$frealnum=$this->datasis->dameval("SELECT MIN(numero) AS numero FROM viefac WHERE fecha BETWEEN '$row->fecha1' AND '$row->fecha' AND caja='$row->caja' AND hora>='$hdesde' AND hora<'$hhasta'");
					$factor=$ffdesde-$frealnum;
					$ddesde[$row->caja]['factor']=$factor;
				}else{
					$ncdesde =$ddesde[$row->caja]['ncdesde'];
					$ffdesde =$ddesde[$row->caja]['ffdesde'];
					$factor  =$ddesde[$row->caja]['factor'];
				}
			}

			$mSQL = "INSERT INTO siva
			(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
			referen, planilla, clipro, nombre, contribu, rif, registro,
			nacional, exento, general, geneimpu,
			adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
			gtotal, reiva, fechal, fafecta)
				SELECT 0 AS id,
				'V' AS libro,
				IF(a.tipo_doc='D','NC',CONCAT(a.tipo_doc,'C')) AS tipo,
				'FA' AS fuente,
				'00' AS sucursal,
				a.fecha,
				IF(LENGTH(a.nfiscal)>0,a.nfiscal,a.numero) AS numero,
				' ' AS numhasta,
				' ' AS caja,
				a.nfiscal,
				'  ' AS nhfiscal,
				IF(a.tipo_doc='F',a.numero,a.factura ) AS referen,
				'  ' AS planilla,
				a.cod_cli AS clipro,
				IF(a.tipo_doc='X','DOCUMENTO ANULADO.......',a.nombre),
				IF(c.tiva='C' OR c.tiva='E','CO','NO') AS contribu,
				IF(a.rifci='',c.rifci,a.rifci),
				IF(b.fecha<'$fhasta','04', '01') AS registro,
				'S' AS nacional,
				a.exento*(a.tipo_doc<>'X')  AS exento,
				a.montasa*(a.tipo_doc<>'X') AS general,
				a.tasa*(a.tipo_doc<>'X') AS geneimpu,
				a.monadic*(a.tipo_doc<>'X') AS adicional,
				a.sobretasa*(a.tipo_doc<>'X') AS adicimpu,
				a.monredu*(a.tipo_doc<>'X') AS reducida,
				a.reducida*(a.tipo_doc<>'X')  AS reduimpu,
				a.totals*(a.tipo_doc<>'X') AS stotal,
				a.iva*(a.tipo_doc<>'X')    AS impuesto,
				a.totalg*(a.tipo_doc<>'X') AS gtotal,
				0 AS reiva,
				".$mes."01 AS fechal,
				0 AS fafecta
				FROM sfac AS a
				LEFT JOIN sfac AS b ON a.factura = b.numero AND a.tipo_doc='D'
				LEFT JOIN scli AS c ON a.cod_cli=c.cliente
				WHERE a.fecha BETWEEN $fdesde AND $fhasta AND MID(a.numero,1,1)<>'_' AND c.tiva IN ('C','E')";

			$flag=$this->db->simple_query($mSQL);
			if(!$flag) memowrite($mSQL,'genesfac');
		}
	}

	//Genera libro de ventas fiscal o no fiscal
	function _genesfac($mes,$fiscal=false) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;

		//Fecha del ultimo cambio de iva
		$mFECHAF = $this->datasis->dameval("SELECT max(fecha) FROM civa WHERE fecha<=${mes}01");
		// BORRA LA GENERADA ANTERIOR
		$this->db->simple_query("DELETE FROM siva WHERE EXTRACT(YEAR_MONTH FROM fechal) = $mes AND fuente='FA' ");
		// ARREGLA LAS TASAS NULAS EN SFAC
		$this->db->simple_query("UPDATE sfac SET tasa=0, montasa=0, reducida=0, monredu=0, sobretasa=0, monadic=0, exento=0 WHERE (tasa IS NULL OR montasa IS NULL) AND EXTRACT(YEAR_MONTH FROM fecha)=$mes ");
		// Arregla las factras malas
		$query = $this->db->query("SELECT transac FROM sfac WHERE abs(exento+montasa+monredu+monadic-totals)>0.2 AND EXTRACT(YEAR_MONTH FROM fecha)=$mes ");
		if ($query->num_rows() > 0)
			foreach ( $query->result() AS $row ) $this->_arreglatasa($row->transac);

		// ARREGLA LAS QUE TIENEN UNA SOLA TASA
		$mSQL = "UPDATE sfac SET tasa=iva, montasa=totals
			WHERE reducida=0 AND sobretasa=0 AND exento=0 AND EXTRACT(YEAR_MONTH FROM fecha)=$mes";
		$this->db->simple_query($mSQL);
		//consulta tipo que fue remplazada: IF(MID(a.numero,1,LOCATE('D',a.numero))='D','NC',CONCAT('F',a.tipo)) AS tipo
		if($fiscal){
			$mSQL = 'UPDATE sfac SET nfiscal  =null WHERE LENGTH(TRIM(nfiscal))  =0'; $this->db->simple_query($mSQL);
			$mSQL = 'UPDATE sfac SET maqfiscal=null WHERE LENGTH(TRIM(maqfiscal))=0'; $this->db->simple_query($mSQL);

			$nfiscal   ='a.nfiscal';
			#$where     =' AND a.nfiscal IS NOT NULL AND a.maqfiscal IS NOT NULL';
			$where     ='';
			$sivainsert=', serial';
			$sfacinsert=',a.maqfiscal';
		}else{
			$nfiscal   ='a.numero';
			$where     ='';
			$sivainsert='';
			$sfacinsert='';
		}

		if ($this->db->field_exists('sprv', 'sfac')){
			$seltipo="IF(a.sprv IS NULL OR LENGTH(TRIM(a.sprv))=0 ,IF(a.tipo_doc='D','NC',CONCAT(a.tipo_doc,'C')),'FT') AS tipo";
		}else{
			$seltipo="IF(a.tipo_doc='D','NC',CONCAT(a.tipo_doc,'C')) AS tipo";
		}

		$mSQL = "
			INSERT INTO siva
			(id, libro, tipo, fuente, sucursal, fecha, numero, numhasta,  caja, nfiscal,  nhfiscal,
			referen, planilla, clipro, nombre, contribu, rif, registro,
			nacional, exento, general, geneimpu,
			adicional, adicimpu,  reducida,  reduimpu, stotal, impuesto,
			gtotal, reiva, fechal, fafecta ,manual $sivainsert)
				SELECT 0 AS id,
				'V' AS libro,
				$seltipo ,
				'FA' AS fuente,
				'00' AS sucursal,
				a.fecha,
				IF(a.manual='S',a.nromanual,${nfiscal}) AS numero,
				' ' AS numhasta,
				' ' AS caja,
				a.nfiscal,
				'  ' AS nhfiscal,
				IF(a.tipo_doc='F',a.numero,a.factura ) AS referen,
				'  ' AS planilla,
				a.cod_cli AS clipro,
				IF(a.tipo_doc='X','DOCUMENTO ANULADO.......',a.nombre),
				IF(c.tiva='C' OR c.tiva='E','CO','NO') AS contribu,
				IF(a.rifci='',c.rifci,a.rifci),
				IF(b.fecha<'$mFECHAF','04', '01') AS registro,
				'S' AS nacional,
				a.exento*(a.tipo_doc<>'X')  AS exento,
				a.montasa*(a.tipo_doc<>'X') AS general,
				a.tasa*(a.tipo_doc<>'X') AS geneimpu,
				a.monadic*(a.tipo_doc<>'X') AS adicional,
				a.sobretasa*(a.tipo_doc<>'X') AS adicimpu,
				a.monredu*(a.tipo_doc<>'X') AS reducida,
				a.reducida*(a.tipo_doc<>'X')  AS reduimpu,
				a.totals*(a.tipo_doc<>'X') AS stotal,
				a.iva*(a.tipo_doc<>'X')    AS impuesto,
				a.totalg*(a.tipo_doc<>'X') AS gtotal,
				0 AS reiva,
				".$mes."01 AS fechal,
				0 AS fafecta,
				a.manual
				$sfacinsert
				FROM sfac AS a
				LEFT JOIN sfac AS b ON a.factura = b.numero AND a.tipo_doc='D'
				LEFT JOIN scli AS c ON a.cod_cli=c.cliente
				WHERE a.fecha BETWEEN $fdesde AND $fhasta AND MID(a.numero,1,1)<>'_' $where";

		$flag=$this->db->simple_query($mSQL);
		if(!$flag) memowrite($mSQL,'genesfac');

		// CARGA LAS RETENCIONES DE IVA DE CONTADO
		/*$mSQL = "SELECT * FROM sfpa WHERE tipo='RI' AND	EXTRACT(YEAR_MONTH FROM f_factura)=$mes AND tipo_doc='FE' ";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0) {
			foreach ( $query->result() AS $row ) {
				$mSQL = "UPDATE siva SET reiva=".$row->monto.", comprobante='20".$row->num_ref."' WHERE tipo='".$row->tipo_doc."' AND numero='".$row->numero."' AND libro='V' AND EXTRACT(YEAR_MONTH FROM fechal)=$mes ";
				$this->db->simple_query($mSQL);
			}
		}*/

		// CARGA LAS RETENCIONES DE IVA DESDE SMOV
		/*$mSQL = "SELECT a.tipo_doc, a.fecha, a.numero, c.nombre, c.rifci, a.cod_cli, b.monto,
				a.numero AS afecta, a.fecha AS fafecta, a.reteiva, a.transac, a.nroriva, a.emiriva, a.recriva
			FROM itccli AS a JOIN smov AS b ON a.transac=b.transac
				LEFT JOIN scli AS c ON a.cod_cli=c.cliente
			WHERE b.fecha BETWEEN $fdesde AND $fhasta AND b.cod_cli='REIVA'
				AND a.reteiva>0 AND b.monto>b.abonos ";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0) {
			foreach ( $query->result() AS $row ) {
				$mSQL = "UPDATE siva SET reiva=".$row->reteiva.", comprobante='".$row->nroriva."', fecharece='$row->recriva'  WHERE tipo='".$row->tipo_doc."' AND numero='".$row->numero."' AND libro='V' AND EXTRACT(YEAR_MONTH FROM fechal)=$mes ";
				$this->db->simple_query($mSQL);
			}
		}*/
	}

	function genesfaccierrez($mes) {
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;
		$this->db->simple_query("UPDATE sfac SET nfiscal=TRIM(nfiscal), maqfiscal=TRIM(maqfiscal) WHERE fecha BETWEEN $fdesde AND $fhasta");
		$this->db->simple_query("DELETE FROM siva WHERE fechal = $fdesde AND libro='V'");

		//$this->db->simple_query("DELETE FROM siva WHERE fechal = $fdesde AND fuente='FA'");
		$mFECHAF = $this->datasis->dameval("SELECT max(fecha) FROM civa WHERE fecha<=$mes"."01");
		$tasas = $this->_tasas($mes);
		$mivag = $tasas['general'];
		$mivar = $tasas['reducida'];
		$mivaa = $tasas['adicional'];

		//$this->db->simple_query("UPDATE fiscalz SET caja='MAYO' WHERE caja='0001'");
		//$this->db->simple_query("UPDATE fiscalz SET hora=CONCAT_WS(':',MINUTE(hora),SECOND(hora),'00') WHERE caja='MAYO' AND HOUR(hora)=0");

		$mSQL="SELECT caja,serial,numero,fecha,factura,fecha1,hora,
		exento  ,base  ,iva  ,base1  ,iva1  ,base2  ,iva2,
		ncexento,ncbase,nciva,ncbase1,nciva1,ncbase2,nciva2,ncnumero
		FROM fiscalz WHERE fecha BETWEEN $fdesde AND $fhasta ORDER BY fecha,serial,numero";
		//echo $mSQL;

		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$data['libro']     ='V';
			$data['fuente']    ='FP';
			$data['sucursal']  ='00';
			$data['tipo']      ='CZ';
			$data['nacional']  ='S';
			$data['fechal']    =$fdesde;

			foreach ($query->result() as $row){
				$data['serial']  =$row->serial;
				$data['fecha']   =$row->fecha;
				$data['caja']    =$row->caja;
				$data['hora']    =$row->hora;
				$data['numero']  =$row->numero;
				$antnum = $row->numero-1;

				$sql="SELECT factura AS ff, ncnumero AS nc FROM fiscalz WHERE numero={$antnum} AND serial='{$row->serial}'";
				//echo $sql."\n";
				//echo $row->numero."\n";

				$cur=$this->datasis->damerow($sql);
				if(count($cur)>0){
					$ncdesde =(empty($cur['nc'])) ? '00000001' : $cur['nc'];
					$ffdesde =(empty($cur['ff'])) ? '00000001' : $cur['ff'];
				}else{
					$ncdesde = $ffdesde ='00000001';
				}
				$nchasta=$row->ncnumero;
				$ffhasta=$row->factura;

				$mmSQL="SELECT
				'V' AS libro,
				IF(a.tipo_doc='D','NC',CONCAT(a.tipo_doc,'C')) AS tipo,
				'FA'   AS fuente,
				'00'   AS sucursal,
				a.fecha,
				'$row->numero' AS numero,
				' ' AS numhasta,
				' ' AS caja,
				a.nfiscal,
				'  ' AS nhfiscal,
				IF(a.tipo_doc='F',a.numero,a.factura ) AS referen,
				'  ' AS planilla,
				a.cod_cli AS clipro,
				IF(a.tipo_doc='X','DOCUMENTO ANULADO...',COALESCE(c.nomfis,c.nombre)) AS nombre,
				IF(c.tiva='C' OR c.tiva='E','CO','NO') AS contribu,
				IF(a.rifci='',c.rifci,a.rifci) AS rif,
				IF(b.fecha<'$mFECHAF','04', '01') AS registro,
				'S' AS nacional,
				a.exento*(a.tipo_doc<>'X')    AS exento,
				a.montasa*(a.tipo_doc<>'X')   AS general,
				a.tasa*(a.tipo_doc<>'X')      AS geneimpu,
				a.monadic*(a.tipo_doc<>'X')   AS adicional,
				a.sobretasa*(a.tipo_doc<>'X') AS adicimpu,
				a.monredu*(a.tipo_doc<>'X')   AS reducida,
				a.reducida*(a.tipo_doc<>'X')  AS reduimpu,
				a.totals*(a.tipo_doc<>'X')    AS stotal,
				a.iva*(a.tipo_doc<>'X')       AS impuesto,
				a.totalg*(a.tipo_doc<>'X')    AS gtotal,
				0 AS reiva,
				".$mes."01 AS fechal,
				0 AS fafecta,
				a.maqfiscal AS serial
				FROM sfac AS a
				LEFT JOIN sfac AS b ON a.factura = b.numero AND a.tipo_doc='D'
				LEFT JOIN scli AS c ON a.cod_cli=c.cliente
				WHERE MID(a.numero,1,1)<>'_' AND c.tiva IN ('C','E') AND a.maqfiscal='{$row->serial}'
				AND a.nfiscal>$ffdesde AND a.nfiscal<=$ffhasta";
				//echo $mmSQL."\n\n";

				$tt['exento']   =0;
				$tt['general']  =0;
				$tt['geneimpu'] =0;
				$tt['adicional']=0;
				$tt['adicimpu'] =0;
				$tt['reducida'] =0;
				$tt['reduimpu'] =0;
				$qquery = $this->db->query($mmSQL);
				if ($qquery->num_rows() > 0){
					foreach ($qquery->result_array() as $rrow){
						$rrow['serial']=$row->serial;
						$m = $this->db->insert_string('siva', $rrow);
						$q = $this->db->query($m);
						$fac=($rrow['tipo']=='NC') ? -1 : 1;
						//$fac=1;
						$tt['exento']   +=$fac*$rrow['exento']   ;
						$tt['general']  +=$fac*$rrow['general']  ;
						$tt['geneimpu'] +=$fac*$rrow['geneimpu'] ;
						$tt['adicional']+=$fac*$rrow['adicional'];
						$tt['adicimpu'] +=$fac*$rrow['adicimpu'] ;
						$tt['reducida'] +=$fac*$rrow['reducida'] ;
						$tt['reduimpu'] +=$fac*$rrow['reduimpu'] ;
					}
				}
				$data['nfiscal']  =$ffdesde;
				$data['nhfiscal'] =$row->factura;
				$data['nombre']   ='VENTAS A NO CONTRIBUYENTES';
				$data['exento']   =$row->exento-$tt['exento']   -$row->ncexento;
				$data['general']  =$row->base  -$tt['general']  -$row->ncbase  ;
				$data['geneimpu'] =$row->iva   -$tt['geneimpu'] -$row->nciva   ;
				$data['adicional']=$row->base2 -$tt['adicional']-$row->ncbase2 ;
				$data['adicimpu'] =$row->iva2  -$tt['adicimpu'] -$row->nciva2  ;
				$data['reducida'] =$row->base1 -$tt['reducida'] -$row->ncbase1 ;
				$data['reduimpu'] =$row->iva1  -$tt['reduimpu'] -$row->nciva1  ;
				$data['gtotal']   =$data['reduimpu']+$data['reducida']+$data['adicimpu']+$data['adicional']+$data['geneimpu']+$data['general']+$data['exento'];
				$mSQL_2 = $this->db->insert_string('siva', $data);
				$this->db->simple_query($mSQL_2);
			}
		}
	}

}
