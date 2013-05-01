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
              <td width="100" class="littletablerowth">Fecha</td>
              <td width="100" class="littletablerow">  <?php echo $fecha?></td>
              <td width="100" class="littletablerowth">Orden</td>
              <td width="100" class="littletablerow">  <?php echo $orden ?></td>
              <td width="119" class="littletablerowth">Proveedor</td>
              <td colspan="3" class="littletablerow">  <?php echo $proveed ?></td>
            </tr>
            <tr>
              <td class="littletablerowth">Numero</td>
              <td class="littletablerow"><?php echo $numero ?></td>
              <td class="littletablerowth">Codigo Fiscal</td>
              <td class="littletablerow"><?php echo $nfiscal ?></td>
              <td class="littletablerowth">Nombre</td>
              <td colspan="3" class="littletablerow"><?php echo $nombre ?></td>
            </tr>
            <tr>
              <td class="littletablerowth">Tipo</td>
              <td class="littletablerow">  <?php echo $tipo_doc ?></td>
              <td class="littletablerowth">Almacen</td>
              <td class="littletablerow">  <?php echo $depo ?></td>
              <td class="littletablerowth">Vence</td>
              <td width="99" class="littletablerow"><?php echo $vence ?></td>
              <td width="44" class="littletablerow"><span class="littletablerowth">
                Peso
              </span></td>
              <td width="99" class="littletablerow"><?php echo $peso ?></td>
                </tr>
          </table>
          <?php echo $items ?>
          <?php //echo $detalle ?>
<table  width="100%" style="margin:0;width:100%;" > 
	  <tr>                                                           
	  	<td colspan=10 class="littletableheader">Totales</td>      
	 </tr>                                                          
	 <tr>                                                 
	  <td width="131" class="littletablerowth">R.ISLR  </td>
		<td width="122" class="littletablerow" ><?php echo $reten ?> </td>
    <td width="125" class="littletablerowth">Anticipo </td>
		<td width="125" class="littletablerow"><?php echo $anticipo ?> </td>
		<td width="111" class="littletablerowth" >Subtotal</td>
		<td width="139" class="littletablerow" ><?php echo $montotot ?> </td>
      </tr>
      <tr>
    <td class="littletablerowth">R.IVA</td>
		<td class="littletablerow" > <?php echo $reteiva ?></td>
    <td class="littletablerowth">Contado</td>
		<td class="littletablerow" > <?php echo $inicial ?></td>
		<td class="littletablerowth">IVA</td>
		<td class="littletablerow" > <?php echo $montoiva ?></td>
      </tr>
      <tr>
    <td class="littletablerowth">Monto US $</td>
		<td class="littletablerow" > <?php echo $mdolar ?></td>
    <td class="littletablerowth">Credito</td>
		<td class="littletablerow" > <?php echo $credito ?></td>
		<td class="littletablerowth">Total</td>
		<td class="littletablerow" > <?php echo $montonet ?></td>
      </tr>
</table>
	  <td>
	<tr>
<table>