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
              <td width="100" class="littletablerowth">Codigo</td>
              <td width="100" class="littletablerow">  <?php echo $codigo; ?></td>
              <td width="100" class="littletablerowth">Precio</td>
              <td width="100" class="littletablerow">  <?php echo $precio; ?></td>
            </tr>
            <tr>
              <td width="100" class="littletablerowth">Descripcion</td>
              <td width="250" class="littletablerow">  <?php echo $descri1; ?></td>
              <td width="100" class="littletablerowth">Fecha</td>
              <td width="100" class="littletablerow">  <?php echo $fecha; ?></td>
            </tr>
          </table>
          <?php echo $items; ?>
          <?php //echo $detalle ?>
<table  width="100%" style="margin:0;width:100%;" > 
	  <tr>                                                           
	  	<td colspan=10 class="littletableheader">Totales</td>      
	 </tr>                                                          
	 <tr>                                                 
    <td width="100" class="littletablerowth">Relacion Costo/Precio</td>
    <td width="100" class="littletablerow">  <?php echo $rela; ?></td>
    <td width="100" class="littletablerowth">Total</td>
    <td width="100" class="littletablerow">  <?php echo $costo; ?></td>
   </tr>
</table>

	  <td>
	<tr>
<table>