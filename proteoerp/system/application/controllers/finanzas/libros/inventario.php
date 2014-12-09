<?php
class inventario{

	static function invresu($anomes){
		$CI =& get_instance();
		$mes = $anomes;
		$ameses = array( 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
		$anomeses = array( '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL', '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO', '09' => 'SEPTIEMBRE', '10' => 'OCTBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE');

		set_time_limit(300);

		$fname = tempnam("/tmp","invresu.xls");
		$CI->load->library("workbook", array("fname"=>$fname));
		$wb =& $CI->workbook;
		$ws =& $wb->addworksheet($mes);

		# ANCHO DE LAS COLUMNAS
		$ws->set_column('A:A',6);
		$ws->set_column('B:B',15);
		$ws->set_column('C:C',37);
		$ws->set_column('D:I',8);
		$ws->set_column('J:W',14);
		//$ws->set_column('Z:Z',12);

		# FORMATOS
		$h       =& $wb->addformat(array( "bold" => "1", "size" => "14", "merge" => "1"));
		$h1      =& $wb->addformat(array( "bold" => "1", "size" => "11", "align" => 'left'));
		$titulo  =& $wb->addformat(array( "bold" => "1", "size" =>  "9", "merge" => "0", "fg_color" => 'silver' ));
		$ht      =& $wb->addformat(array( "bold" => "1", "size" => "11", "merge" => "1", "fg_color" => 'green'  ));
		$cuerpo  =& $wb->addformat(array( "size" => "9" ));
		$numero  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9 ));
		$Tnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "fg_color" => 'silver' ));
		$Rnumero =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 9, "bold" => 1, "align" => 'right' ));

		# COMIENZA A ESCRIBIR
		$nomes = substr($mes,4,2);
		$nomes1 = $anomeses[$nomes];
		$hs = "REGISTRO DE DETALLADO DE ENTRADAS Y SALIDAS DE INVENTARIO DE MERCANCIAS, MATERIAS PRIMAS O PRODUCTOS EN PROCESOS ";
		$hs1 = "CORRESPONDIENTE AL MES DE ".$anomeses[$nomes]." DEL ".substr($mes,0,4);

		$ws->write(1, 0, $CI->datasis->traevalor('TITULO1') , $h1 );
		$ws->write(2, 0, "RIF: ".$CI->datasis->traevalor('RIF') , $h1 );
		$ws->write(4,0, $hs, $h );
		for ( $i=1; $i < 15; $i++ ) {
		   $ws->write_blank(4, $i,  $h );
		};
		$ws->write(5, 0, $hs1, $h );
		for ( $i=1; $i < 15; $i++ ) {
		   $ws->write_blank(5, $i,  $h );
		};

		$mm=6;
		$ws->write_string($mm  ,0, ""           , $titulo );
		$ws->write_string($mm+1,0, ""           , $titulo );
		$ws->write_string($mm+2,0, "Nro"        , $titulo );
		$ws->write_string($mm  ,1, "Item"       , $titulo );
		$ws->write_string($mm+1,1, "de"         , $titulo );
		$ws->write_string($mm+2,1, "Inventario" , $titulo );
		$ws->write_string($mm  ,2, ""           , $titulo );
		$ws->write_string($mm+1,2, ""           , $titulo );
		$ws->write_string($mm+2,2, "Descripcion", $titulo );

		$ws->write($mm,    3, "UNIDADES", $ht );
		$ws->write_string($mm+1,  3, "Existencia", $titulo );
		$ws->write_string($mm+2,  3, "Anterior",   $titulo );

		$ws->write_blank($mm,     4, $ht );
		$ws->write_blank($mm+1,   4, $titulo );
		$ws->write_string($mm+2,  4, "Entradas", $titulo );

		$ws->write_blank($mm,     5,  $ht );
		$ws->write_blank($mm+1,   5,  $titulo );
		$ws->write_string($mm+2,  5, "Salidas", $titulo );

		$ws->write_blank($mm,     6,  $ht );
		$ws->write_blank($mm+1,   6,  $titulo );
		$ws->write_string($mm+2,  6, "Retiros", $titulo );

		$ws->write_blank($mm,     7,  $ht );
		$ws->write_string($mm+1,  7,  "Auto", $titulo );
		$ws->write_string($mm+2,  7,  "Consumos", $titulo );

		$ws->write_blank($mm,     8,  $ht );
		$ws->write_blank($mm+1,   8,  $titulo );
		$ws->write_string($mm+2,  8,  "Existencias", $titulo );

		$ws->write($mm,    9,  "BOLIVARES", $ht );
		$ws->write_string($mm+1,  9,  "Valor", $titulo );
		$ws->write_string($mm+2,  9,  "Anterior", $titulo );

		$ws->write_blank($mm,    10,  $ht );
		$ws->write_blank($mm+1,  10,  $titulo );
		$ws->write_string($mm+2, 10,  "Entradas ", $titulo );
		$ws->write_blank($mm,    11,  $ht );
		$ws->write_blank($mm+1,  11,  $titulo );
		$ws->write_string($mm+2, 11,  "Salidas", $titulo );
		$ws->write_blank($mm,    12,  $ht );
		$ws->write_blank($mm+1,  12,  $titulo );
		$ws->write_string($mm+2, 12,  "Retiros", $titulo );
		$ws->write_blank($mm,    13,  $ht );
		$ws->write_string($mm+1, 13,  "Auto", $titulo );
		$ws->write_string($mm+2, 13,  "Consumos", $titulo );
		$ws->write_blank($mm,    14,  $ht );
		$ws->write_string($mm+1, 14,  "Valor de", $titulo );
		$ws->write_string($mm+2, 14,  "Existencia", $titulo );

		$mm++;
		$mm++;
		$mm++;

		$ii = 1;
		$mtiva = 'X';
		$mfecha = '2000-00-00';
		$tventas = $texenta = $tbase   = $timpue  = $treiva  = $tperci  = 0 ;
		$dd=$mm;  // desde

		$mSQL = "SELECT a.codigo, b.descrip, a.inicial, a.compras, a.ventas, a.trans, a.fisico, a.notas, a.final, a.minicial, a.mcompras, a.mventas, a.mtrans, a.mfisico, a.mnotas, a.mfinal FROM invresu a LEFT JOIN sinv b ON a.codigo=b.codigo WHERE mes=$mes ";
		$query = $CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$ws->write_string($mm,  0, $ii, $cuerpo );
				$ws->write_string($mm,  1, $row->codigo  , $cuerpo );  // codigo
				$ws->write_string($mm,  2, $row->descrip , $cuerpo );  // descrip
				$ws->write_number($mm,  3, $row->inicial , $numero );  // inicial
				$ws->write_number($mm,  4, $row->compras , $numero );  // entradas
				$ws->write_number($mm,  5, $row->ventas  , $numero );  // salidas
				$ws->write_number($mm,  6, $row->trans   , $numero );  // retiros
				$ws->write_number($mm,  7, $row->fisico  , $numero );  // autoconsumos
				$ws->write_number($mm,  8, $row->final   , $numero );  // final
				$ws->write_number($mm,  9, $row->minicial, $numero );  // minicial
				$ws->write_number($mm, 10, $row->mcompras, $numero );  // entradas
				$ws->write_number($mm, 11, $row->mventas , $numero );  // salidas
				$ws->write_number($mm, 12, $row->mtrans  , $numero );  // retiros
				$ws->write_number($mm, 13, $row->mfisico , $numero );  // autoconsumos
				$ws->write_number($mm, 14, $row->mfinal  , $numero );  // final
				$mm++;
				$ii++;
			}
		}

		$celda = $mm+1;
		$fventas = "=J$celda";   // VENTAS
		$fexenta = "=K$celda";   // VENTAS EXENTAS
		$fbase   = "=L$celda";   // BASE IMPONIBLE
		$fiva    = "=N$celda";   // I.V.A.

		$ws->write( $mm, 0,"Totales...",  $Tnumero );
		$ws->write_blank( $mm,  1,  $Tnumero );
		$ws->write_blank( $mm,  2,  $Tnumero );

		$ws->write_formula($mm,  3, "=SUM(D$dd:D$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula($mm,  4, "=SUM(E$dd:E$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula($mm,  5, "=SUM(F$dd:F$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula($mm,  6, "=SUM(G$dd:G$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula($mm,  7, "=SUM(H$dd:H$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula($mm,  8, "=SUM(I$dd:I$mm)", $Tnumero );   //"BASE IMPONIBLE"
		$ws->write_formula($mm,  9, "=SUM(J$dd:J$mm)", $Tnumero );   //"VENTAS + IVA"
		$ws->write_formula($mm, 10, "=SUM(K$dd:K$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula($mm, 11, "=SUM(L$dd:L$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula($mm, 12, "=SUM(M$dd:M$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula($mm, 13, "=SUM(N$dd:N$mm)", $Tnumero );   //"VENTAS EXENTAS"
		$ws->write_formula($mm, 14, "=SUM(O$dd:O$mm)", $Tnumero );   //"VENTAS EXENTAS"

		$wb->close();
		header("Content-type: application/x-msexcel; name=\"invresu.xls\"");
		header("Content-Disposition: inline; filename=\"invresu.xls\"");
		$fh=fopen($fname,"rb");
		fpassthru($fh);
		unlink($fname);
	}

}
