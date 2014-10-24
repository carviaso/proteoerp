<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');
class limport extends Controller {

	function limport() {
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('205',1);
		$this->load->helper('date');
	}

	function liqui($lnumero) {
		$dbnumero=$this->db->escape($lnumero);

		$mSQL_1="SELECT numero, fecha, status, proveed, nombre, agente, nomage, montofob, gastosi, montocif,
		aranceles, gastosn, montotot, montoiva, montoexc, arribo, factura, cambioofi, cambioreal, peso, transac,
		estampa, usuario, hora, dua, cargoval, control, crm
		FROM ordi WHERE numero=${dbnumero}";

		$fnombre='liquidacion.xls';
		$fname = tempnam('/tmp',$fnombre);

		$this->load->library('workbook', array('fname'=>$fname));
		$wb = & $this->workbook ;
		$ws = & $wb->addworksheet($lnumero);

		// ANCHO DE LAS COLUMNAS
		$ws->set_column('A:A',10);
		$ws->set_column('B:B',35);
		$ws->set_column('C:C',6);
		$ws->set_column('D:E',10);
		$ws->set_column('F:F',6);
		$ws->set_column('G:I',10);
		$ws->set_column('J:J',6);
		$ws->set_column('K:Q',10);

		// FORMATOS
		$h       =& $wb->addformat(array( "bold" => 1, "size" => 14, "align" => 'left'));
		$h0      =& $wb->addformat(array( "bold" => 1, "size" => 10, "align" => 'left'));
		$h1      =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'center'));
		$h2      =& $wb->addformat(array( "bold" => 1, "size" => 14, "align" => 'left', "fg_color" => 'silver'  ));
		$h3      =& $wb->addformat(array( "bold" => 1, "size" => 9 ));
		$h3->set_merge();
		$h4      =& $wb->addformat(array( "bold" => 1, "size" => 9 , "align" => 'right',"num_format" => '#,##0.00'));
		$codesc  =& $wb->addformat(array( "bold" => 0, "size" => 8 , "align" => 'left', "fg_color" => 26  ));
		$codesc->set_border(1);
		$numcer  =& $wb->addformat(array( "bold" => 0, "size" => 8 , "align" => 'right', "fg_color" => 26  ));
		$numcer->set_border(1);
		$numpri  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 8 , "fg_color" => 44 ));
		$numpri->set_border(1);
		$numseg  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 8 , "fg_color" => 42 ));
		$numseg->set_border(1);
		$numter  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 8 , "fg_color" => 41 ));
		$numter->set_border(1);
		$numcua  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 8 , "fg_color" => 41 ));
		$numcua->set_border(1);
		$numqui  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 8 , "fg_color" => 45 ));
		$numqui->set_border(1);

		$titulo  =& $wb->addformat(array( "bold" => 1, "size" => 8, "merge" => 1, "fg_color" => 'silver', 'align'=>'vcenter' ));
		$titulo->set_text_wrap();
		$titulo->set_text_h_align(2);
		$titulo->set_border(1);
		$titulo->set_merge();

		$titpri  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 26 ));
		$titpri->set_text_wrap();
		$titpri->set_border(1);
		$titpri->set_merge();

		$titseg  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 44 ));
		$titseg->set_text_wrap();
		$titseg->set_border(1);
		$titseg->set_merge();

		$titter  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 42 ));
		$titter->set_text_wrap();
		$titter->set_border(1);
		$titter->set_merge();

		$titcua  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 41 ));
		$titcua->set_text_wrap();
		$titcua->set_border(1);
		$titcua->set_merge();

		$titqui  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 45, 'align'=>'vcenter' ));
		//$titqui->set_text_v_align(6);
		$titqui->set_text_wrap();
		$titqui->set_border(1);
		$titqui->set_merge();

		$cuerpo  =& $wb->addformat(array( 'size' => 9 ));

		$Tnumero =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9, 'bold' => 1, 'fg_color' => 'silver' ));
		$Rnumero =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9, 'bold' => 1, 'align'    => 'right' ));

		$enc  = $this->db->query($mSQL_1);
		$row1 = $enc->row();

		$proveed   =$row1->proveed;
		$nombre    =$row1->nombre;
		$agente    =$row1->agente;
		$nomage    =$row1->nomage;
		$numerol   =$row1->numero;
		$fecha     =dbdate_to_human($row1->fecha);
		$arribo    =dbdate_to_human($row1->arribo);
		$factura   =$row1->factura;
		$dua       =$row1->dua;
		$montofob  =$row1->montofob;
		$gastosi   =$row1->gastosi;
		$montocif  =$row1->montocif;
		$aranceles =$row1->aranceles;
		$gastosn   =$row1->gastosn;
		$montotot  =$row1->montotot;
		$montoiva  =$row1->montoiva;
		$montoexc  =$row1->montoexc;
		$cambioofi =$row1->cambioofi;
		$cambioreal=$row1->cambioreal;

		// COMIENZA A ESCRIBIR
		$ws->write(1, 0, $this->datasis->traevalor('TITULO1') , $h );
		$ws->write(2, 0, $this->datasis->traevalor('TITULO2') , $h0 );
		$ws->write(3, 0, 'RIF: '.$this->datasis->traevalor('RIF') , $h0 );
		$ws->write(4, 0, " ",$h1 );

		$ws->write(1, 8, 'Orden de Importación Nº'.str_pad($lnumero, 8, '0', STR_PAD_LEFT), $h );
		$ws->write(2, 8, 'Fecha: '.$fecha , $h0 );
		$ws->write(3, 8, 'Fecha de LLegada :'.$arribo, $h0 );
		$ws->write(4, 8, ' ',$h1 );

		$ws->write(5, 0, 'Proveedor: ('.$proveed.')'.$nombre,$h0);
		for ( $i=1; $i<6; $i++ ) {
			$ws->write_blank(5, $i,  $h0 );
		};

		$ws->write(6, 0, 'Agente Aduanal: ('.$agente.')'.$nomage,$h0);
		for ( $i=1; $i<6; $i++ ) {
			$ws->write_blank(6, $i,  $h0 );
		};

		$hs = 'Liquidación de Importación (PARA USO INTERNO)';
		$ws->write(8,0, $hs, $h );
		for ( $i=1; $i<6; $i++ ) {
			$ws->write_blank(8, $i,  $h );
		};

		$ws->write(5, 8, 'Nro. de Factura:'.$factura , $h0 );
		$ws->write(6, 8, 'D.U.A: '.$dua , $h0 );

		// TITULOS
		$mm=9;
		$ws->write_string( $mm,   0, 'Productos', $titpri );
		$ws->write_string( $mm+1, 0, 'Código', $titulo );

		$ws->write_blank(   $mm,  1,  $titpri);
		$ws->write_string( $mm+1, 1, 'Descripción', $titulo );

		$ws->write_blank(  $mm,   2, $titpri );
		$ws->write_string( $mm+1, 2, 'Cant.',$titulo );

		$ws->write_string( $mm,   3, 'Monto en moneda extrajera',$titseg );
		$ws->write_string( $mm+1, 3, 'Precio FOB', $titulo );

		$ws->write_blank(  $mm,   4, $titseg );
		$ws->write_string( $mm+1, 4, 'Monto FOB',  $titulo );

		$ws->write_blank(  $mm,   5, $titseg );
		$ws->write_string( $mm+1, 5, 'Part%', $titulo );

		$ws->write_blank(  $mm,   6, $titseg );
		$ws->write_string( $mm+1, 6, 'Gastos Exterior', $titulo );

		$ws->write_blank(  $mm,   7, $titseg );
		$ws->write_string( $mm+1, 7, 'Monto CIF', $titulo );

		$ws->write_string( $mm,   8, 'Liquidación a Cambio Oficial '.$cambioofi, $titter );
		$ws->write_string( $mm+1, 8, 'Monto CIF Oficial ', $titulo );

		$ws->write_blank(  $mm,   9, $titter );
		$ws->write_string( $mm+1, 9, '%', $titulo );

		$ws->write_blank(  $mm,  10, $titter );
		$ws->write_string( $mm+1,10, 'Arancel',$titulo );

		$ws->write_blank(  $mm,  11, $titter );
		$ws->write_string( $mm+1,11, 'Gastos Nacionales',$titulo );

		$ws->write_blank(  $mm,  12, $titter );
		$ws->write_string( $mm+1,12, 'Importe Nacional',$titulo );

		$ws->write_string( $mm,  13, 'Liquidación a cambio real '.$cambioreal,$titcua );
		$ws->write_string( $mm+1,13, 'Monto CIF Real '.$cambioreal,$titulo );

		$ws->write_blank(  $mm,  14,  $titcua );
		$ws->write_string( $mm+1,14, 'Importe Nacional Cambio Real ', $titulo );

		$ws->write_blank(  $mm,  15, $titcua );
		$ws->write_string( $mm+1,15, 'Costo unitario',$titulo);

		$ws->write_string( $mm,  16, ' ', $titqui );
		$ws->write_string( $mm+1,16, 'Cargo Valor',$titqui);

		$mm=$mm+2;
		$dd=$mm+1;

		$mSQL = "SELECT numero, fecha, codigo, descrip, cantidad, costofob, importefob, gastosi, costocif,
		importecif, importeciflocal, importecifreal,codaran, arancel, montoaran, gastosn, costofinal, importefinal, participam*100 AS part,
		participao, arancif, iva, precio1, precio2, precio3, precio4, estampa, hora, usuario, id,
		importecifreal-importeciflocal AS cargo,
		importecifreal+gastosn+montoaran AS importenac,
		(importecifreal+gastosn+montoaran)/cantidad AS cunitario
		FROM itordi WHERE numero=${dbnumero}";

		$mc=$this->db->query($mSQL);
		if($mc->num_rows() > 0){
			foreach( $mc->result() as $row ) {
				$ws->write_string( $mm,  0,  $row->codigo         , $codesc );
				$ws->write_string( $mm,  1,  $row->descrip        , $codesc );
				$ws->write_number( $mm,  2,  $row->cantidad       , $numcer );
				$ws->write_number( $mm,  3,  $row->costofob       , $numpri );
				$ws->write_number( $mm,  4,  $row->importefob     , $numpri );
				$ws->write_number( $mm,  5,  $row->part           , $numpri );
				$ws->write_number( $mm,  6,  $row->gastosi        , $numpri );
				$ws->write_number( $mm,  7,  $row->importecif     , $numpri );
				$ws->write_number( $mm,  8,  $row->importeciflocal, $numseg );
				$ws->write_number( $mm,  9,  $row->arancel        , $numseg );
				$ws->write_number( $mm,  10, $row->montoaran      , $numseg );
				$ws->write_number( $mm,  11, $row->gastosn        , $numseg );
				$ws->write_number( $mm,  12, $row->importefinal   , $numseg );
				$ws->write_number( $mm,  13, $row->importecifreal , $numter );
				$ws->write_number( $mm,  14, $row->importenac     , $numter );
				$ws->write_number( $mm,  15, $row->cunitario      , $numter );
				$ws->write_number( $mm,  16, $row->cargo          , $numqui );
				$mm++;
			}
		}

		$celda = $mm+1;

		$ws->write_blank(  $mm,  0, $Tnumero );
		$ws->write_string( $mm,  1, 'Totales...',$Tnumero );
		$ws->write_blank(  $mm,  2, $Tnumero );
		$ws->write_blank(  $mm,  3, $Tnumero );
		$ws->write_formula($mm,  4, "=SUM(E$dd:E$mm)", $Tnumero );
		$ws->write_formula($mm,  5, "=SUM(F$dd:F$mm)", $Tnumero );
		$ws->write_formula($mm,  6, "=SUM(G$dd:G$mm)", $Tnumero );
		$ws->write_formula($mm,  7, "=SUM(H$dd:H$mm)", $Tnumero );
		$ws->write_formula($mm,  8, "=SUM(I$dd:I$mm)", $Tnumero );
		$ws->write_blank(  $mm,  9, $Tnumero );
		$ws->write_formula($mm, 10, "=SUM(K$dd:K$mm)", $Tnumero );
		$ws->write_formula($mm, 11, "=SUM(L$dd:L$mm)", $Tnumero );
		$ws->write_formula($mm, 12, "=SUM(M$dd:M$mm)", $Tnumero );
		$ws->write_formula($mm, 13, "=SUM(N$dd:N$mm)", $Tnumero );
		$ws->write_formula($mm, 14, "=SUM(O$dd:O$mm)", $Tnumero );
		$ws->write_blank(  $mm, 15,  $Tnumero );
		$ws->write_formula($mm, 16, "=SUM(Q$dd:Q$mm)",$Tnumero );

		$mm =$mm+2;

		$ws->write_string($mm,  2, 'Costo en US$',$titulo);
		$ws->write_blank( $mm,  3, $titulo);
		$ws->write_blank( $mm,  4, $titulo);

		$ws->write_string($mm,  5, 'Costo en Bs',$titulo);
		$ws->write_blank( $mm,  6, $titulo);
		$ws->write_blank( $mm,  7, $titulo);

		$ws->write_string($mm,  8, 'Resumen de Liquidacion',$titulo);
		$ws->write_blank( $mm,  9, $titulo);
		$ws->write_blank( $mm, 10, $titulo);

		$mm++;
		$montobase= $this->datasis->dameval("SELECT SUM(base) AS base FROM ordiva WHERE ordeni=$dbnumero");
		$montototal=$montocif*$cambioreal+$gastosn+$aranceles;

		$ws->write_string($mm,  2, 'Monto FOB:'         ,$h3);
		$ws->write_blank( $mm,  3,                       $h3);
		$ws->write_number($mm,  4, $montofob            ,$h4);
		$ws->write_string($mm,  5, 'Monto CIF:'         ,$h3);
		$ws->write_blank( $mm,  6,                       $h3);
		$ws->write_number($mm,  7, $montocif*$cambioreal,$h4);
		$ws->write_string($mm,  8, 'Monto Exento:'      ,$h3);
		$ws->write_blank( $mm,  9,                       $h3);
		$ws->write_number($mm, 10, $montoexc            ,$h4);

		$mm ++;
		$ws->write_string($mm,  2, 'Gastos Exterior:'  ,$h3);
		$ws->write_blank( $mm,  3,                      $h3);
		$ws->write_number($mm,  4, $gastosi            ,$h4);
		$ws->write_string($mm,  5, 'Gastos Nacionales:',$h3);
		$ws->write_blank( $mm,  6,                      $h3);
		$ws->write_number($mm,  7, $gastosn            ,$h4);
		$ws->write_string($mm,  8, 'Base Imponible:'   ,$h3);
		$ws->write_blank( $mm,  9,                      $h3);
		$ws->write_number($mm, 10, $montobase          ,$h4);

		$mm ++;
		$ws->write_string($mm,  2, 'Monto CIF:'  ,$h3);
		$ws->write_blank( $mm,  3,                $h3);
		$ws->write_number($mm,  4, $montocif     ,$h4);
		$ws->write_string($mm,  5, 'Aranceles:'  ,$h3);
		$ws->write_blank( $mm,  6,                $h3);
		$ws->write_number($mm,  7, $aranceles,    $h4);
		$ws->write_string($mm,  8, 'Monto IVA:'  ,$h3);
		$ws->write_blank( $mm,  9,                $h3);
		$ws->write_number($mm, 10, $montoiva     ,$h4);

		$mm ++;
		$ws->write_string($mm,  2, ' ',$h3 );
		$ws->write_blank( $mm,  3, $h3);
		$ws->write_string($mm,  4, ' ',$h3 );
		$ws->write_string($mm,  5, 'Monto Final:',$h3);
		$ws->write_blank( $mm,  6, $h3);
		$ws->write_number($mm,  7, $montototal   ,$h4);
		$ws->write_string($mm,  8, 'Monto Total Bs:',$h3);
		$ws->write_blank( $mm,  9, $h3);
		$ws->write_number($mm, 10, $montoiva+$montobase+$montototal-$montobase, $h4);

		$wb->close();
		header("Content-type: application/x-msexcel; name=\"$fnombre\"");
		header("Content-Disposition: inline; filename=\"$fnombre\"");
		$fh=fopen($fname,'rb');
		fpassthru($fh);
		unlink($fname);
	}
}
