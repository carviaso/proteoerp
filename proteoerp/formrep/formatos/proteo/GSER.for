<?php
$this->load->plugin('numletra');
if(count($parametros)==0) show_error('Faltan parametros ');
$id = $parametros[0];

$mSQL_1 = $this->db->query("SELECT * FROM gser  WHERE id=$id ");

if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row    = $mSQL_1->row();

$fecha  = dbdate_to_human($row->fecha);
$vence  = dbdate_to_human($row->vence);
$ffecha = dbdate_to_human($row->ffactura);

$tipo_doc = $row->tipo_doc;
$numero   = $row->numero;
$nombre   = $row->nombre;
$proveed  = $row->proveed;
$totpre   = $row->totpre;
$totiva   = $row->totiva;
$totbruto = $row->totbruto;
$reten    = $row->reten;
$totneto  = $row->totneto;
$reteiva  = $row->reteiva;
$anticipo = $row->anticipo;
$credito  = $row->credito;
$monto1   = $row->monto1;
$cheque1  = $row->cheque1;
$codb1    = $row->codb1;
$orden    = $row->orden;
$serie    = $row->serie;

$mSQL_2  = $this->db->query("SELECT numero,fecha,proveed,codigo,descrip,precio,iva,importe from gitser WHERE idgser=$id ");
$detalle = $mSQL_2->result();

$numcuent = '';
$banco    = '';

$pagina   = 0;
$maxlinea = 22;

if ($monto1>0){
	$cuenta = $this->db->query("SELECT numcuent, banco FROM banc WHERE  codbanc='$codb1'");
	$row2=$cuenta->row();
	$numcuent=$row2->numcuent;
	$banco=$row2->banco;
}

// Cintillo
$encabeza = $this->cintillo();
$encabeza .='
			<div class="page" style="font-size: 7pt">
				<table style="width:100%;font-size:7pt;" class="header">
					<tr>
						<td><h1 style="text-align: left">Comprobante de Egresos</h1></td>
						<td><h1 style="text-align: right">Numero: '.$serie.'</h1></td>
					</tr>
				</table>
			</div>
';

// ENCABEZADO PRIMERA PAGINA
$encabeza1p = '
			<table style="width: 100%; font-size: 8pt;" border="0">
				<tr class="even_row">
					<td style="width:110px;">Proveedor:</td>
					<td colspan=5><b>'.$nombre.' ('.$proveed.')</b></td>
				</tr>
				<tr class="odd_row">
					<td>Numero:</td>
					<td><b>'.$numero.'</b></td>
					<td align="right">Fecha:</td>
					<td> <b>'.$fecha.'</b></td>
					<td align="right">Fecha Doc.:</td>
					<td> <b>'.$ffecha.'</b></td>
				</tr>
				<tr class="even_row">
					<td>Tipo Doc.:</td>
					<td> <b>'.$tipo_doc.'</b></td>
					<td align="right">Vence:</td>
					<td> <b>'.$vence.'</b></td>
					<td align="right">Orden/Compra:</td>
					<td> <b>'.$orden.'</b></td>
				</tr>
				<tr class="odd_row">
					<td>Forma de Pago: <b></b></td>';
if ($monto1 > 0 ){
	$encabeza1p .= '
					<td colspan="5">
					<table width="100%">
						<tr>
							<td>Banco/Caja:</td>
							<td><b>('.$codb1.')'.$banco.'</b></td>
							<td>Cuenta:<b></td>
							<td>'.$numcuent.'</b></td>
							<td>Debito No:</td>
							<td><b>'.$cheque1.'</b></td>
							<td>Monto Bs.F.:</td>
							<td><b>'.$monto1.'</b></td>
						</tr>
					</table>';
} else {
	$encabeza1p .= '
					<td colspan="5"><b>CREDITO</b>';

}
	$encabeza1p .= '
					</td>
				</tr>
			</table>
';

$encatabla = '
			<tr style="background-color:black;border-style:solid;color:white;font-weight:bold">
					<th>Codigo</th>
					<th>Descripcion</th>
					<th>Precio</th>
					<th>Iva</th>
					<th>Importe</th>
			</tr>
';

$continua = '
			</tbody>
			<tfoot>
			<tr>
				<td colspan="6" style="text-align: right;font-size:16px"><strong>Continua.........</strong></td>
			</tr>
			</tfoot>
			</table>
			</div>
		</div></td>
	</tr>
</table>
';


//Genera el HTML
echo $this->rephead('REPHEAD','Gasto o Egresos '.$serie);
echo '<body>';
echo $this->scriptphp();

///////////////////////////////////////////////////////////

echo '<div id="body">';

$principal  = '	<table style="width: 100%;">'."\n";
$principal .= '		<thead>'."\n";
$principal .= '		<tr>'."\n";
$principal .= '			<td>'."\n";
$principal .= $encabeza."\n";
$principal .= '			<div class="page" style="font-size: 7pt">'."\n";
$principal .= $encabeza1p."\n";
$principal .= '			</div>'."\n";
$principal .= '			</td>'."\n";
$principal .= '		</tr>'."\n";
$principal .= '		</thead>'."\n";
$principal .= '		<tr>'."\n";
$principal .= '			<td>'."\n";
$principal .= '				<div id="content">'."\n";
$principal .= '				<div class="page" style="font-size: 7pt">'."\n";
$principal .= '				<table class="change_order_items">'."\n";
$principal .= '					<thead>'."\n";
$principal .= $encatabla."\n";
$principal .= '					</thead>'."\n";
$principal .= '					<tbody>'."\n";

$tprecio=0;
$tiva=0;
$timporte=0;
$mod=FALSE;
$i=0;
foreach ($detalle AS $items){
	$i++;
	if ( $pagina == 0 ) {
		echo $principal;
		$pagina = $pagina+1;
	};

	$tprecio  += $items->precio;
	$tiva     += $items->iva;
	$timporte += $items->importe;

	$estilo   = 'even_row';
	if( $mod) $estilo = 'odd_row';

	echo '					<tr class="'.$estilo.'">';
	echo '						<td style="text-align: center">'.$items->codigo.'</td>'."\n";
	echo '						<td>'.$items->descrip.'</td>'."\n";
	echo '						<td style="text-align: right">'.$items->precio.'</td>'."\n";
	echo '						<td style="text-align: right">'.$items->iva.'</td>'."\n";
	echo '						<td style="text-align: right">'.$items->importe.'</td>'."\n";
	echo '					</tr>'."\n";

//							if($i%10==0) echo "<p STYLE='page-break-after: always'></p>";
	if( $i%$maxlinea == 0) {
		$pagina = $pagina+1;
		$i = 0;
		echo $continua;   //Pie de pagina cuando continua
		echo '<p style=\'page-break-after: always\'></p>'."\n"; // salto de Pagina
		echo $principal;  //Encabezado Principal
	};

	$mod = ! $mod;
}
while ( $i < $maxlinea ){
	$i++;
	echo '					<tr class="'.$estilo.'">';
	echo '						<td style="text-align: center">&nbsp;</td>'."\n";
	echo '						<td>&nbsp;</td>'."\n";
	echo '						<td style="text-align: right">&nbsp;</td>'."\n";
	echo '						<td style="text-align: right">&nbsp;</td>'."\n";
	echo '						<td style="text-align: right">&nbsp;</td>'."\n";
	echo '					</tr>'."\n";
}

?>
					</tbody>
					<tfoot>
					<tr>
						<td  colspan="2" style="text-align: right;border:1px solid black;"><b>Totales </b></td>
						<td  style="text-align:right;border:1px solid black;"><b><?php echo nformat($tprecio) ?></b></td>
						<td  style="text-align:right;border:1px solid black;"><b><?php echo nformat($tiva) ?></b></td>
						<td  style="text-align:right;border:1px solid black;"><b><?php echo nformat($timporte) ?></b></td>
					</tr>
					</tfoot>
				</table>

				<table style="border:1px solid grey;font-size:8pt;width:100%;">
					<tr>
						<td style="text-align:left;">      <b>RETENCION DE I.S.L.R:</b></td>
						<td class="change_order_total_col"><b><?php echo $reten ?></b></td>
						<td style="text-align: right;"><b>ANTICIPOS RECIBIDOS:</b></td>
						<td class="change_order_total_col"><b><?php echo nformat($anticipo) ?></b></td>
						<td style="text-align:right;"><b>MONTO NETO:</b></td>
						<td class="change_order_total_col"><b><?php echo $totneto ?></b></td>
					</tr>
					<tr>
						<td style="text-align:left;"><b>RETENCION DE I.V.A:</b></td>
						<td class="change_order_total_col"><b><?php echo $reteiva ?></b></td>
						<td style="text-align:right;"><b>MONTO PAGADO:</b></td>
						<td class="change_order_total_col"><b><?php echo $monto1 ?></b></td>
						<td style="text-align: right;"><b>MONTO A CREDITO:</b></td>
						<td class="change_order_total_col"><b><?php echo $credito ?></b></td>
					</tr>
				</table>
				<br>
				<table style="width:100%;border:1px solid grey;text-align:center;font-size:8pt;" class="header">
					<tr style="height:100px;">
						<td>&nbsp;<br><br><br></td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr style="background-color:#EAEAEA;">
						<td><b>Elaborado por: </b></td>
						<td><b>Auditoria:     </b></td>
						<td><b>Autorizado por:</b></td>
						<td><b>Aprobado:      </b></td>
					</tr>
				</table>
				</div>
    		</div>
			</td>
		</tr>
	</table>
</div>
</body>
</html>
