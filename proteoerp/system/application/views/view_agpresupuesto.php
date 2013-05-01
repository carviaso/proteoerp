<table align='center'>
	<tr>
		<td align=right></td>
	</tr>
	<tr>
		<td>
          <table width="100%"  style="margin:0;width:100%;">
            <tr>
              <td colspan=11 class="littletableheader">Encabezado</td>
            </tr>
            <tr>
              <td width="100" class="littletablerowth">Numero</td>
              <td width="100" class="littletablerow">  <?php echo $numero?></td>
              <td width="100" class="littletablerowth">Cliente</td>
              <td width="100" class="littletablerow"><?php echo $cod_cli ?></td>
              <td width="100" class="littletablerowth">Rif/Cedula</td>
              <td width="100" class="littletablerow"><?php echo $rifci ?></td>
            </tr>
            <tr>
              <td class="littletablerowth">Fecha</td>
              <td class="littletablerow"><?php echo $fecha ?></td>
              <td class="littletablerowth">Nombre</td>
              <td colspan="3" class="littletablerow"><?php echo $nombre ?></td>
            </tr>
            <tr>
              <td class="littletablerowth">Vendedor</td>
              <td class="littletablerow"><?php echo $vd?></td>
              <td class="littletablerowth">Dirección</td>
              <td colspan="3" class="littletablerow"><?php echo $direc ?></td>
            </tr>
            <tr>
              <td class="littletablerowth">Peso</td>
              <td class="littletablerow"><?php echo $peso ?></td>
              <td class="littletablerowth"></td>
              <td colspan="3" class="littletablerow"><?php echo $dire1?></td>
            </tr>
          </table>
          <?php echo $items ?>
          <?php //echo $detalle ?>
<table  width="100%" style="margin:0;width:100%;" > 
	 <tr>                                                           
	  <td colspan=10 class="littletableheader">Totales</td>      
	 </tr>
	 <tr>
     <td class="littletablerow"><?php echo $condi1 ?></td>
     <td class="littletablerowth">I.V.A</td>
     <td class="littletablerow"><?php echo $iva ?></td>
     <td class="littletablerowth">Sub-Total</td>                       
     <td colspan="3" class="littletablerow"><?php echo $totals ?></td>
   </tr>
   <tr>
     <td class="littletablerow"><?php echo $condi2 ?></td>
     <td class="littletablerowth">Adelanto</td>
     <td class="littletablerow"><?php echo $inicial ?></td>
     <td class="littletablerowth">Total</td>                       
     <td colspan="3" class="littletablerow"><?php echo $totalg ?></td>
   </tr>                                                         
</table>
	  <td>
	<tr>
<table>