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
              <td width="100" class="littletablerowth">Fecha</td>
              <td width="100" class="littletablerow"><?php echo $fecha ?></td>
            </tr>
            <tr>
              <td class="littletablerowth">Fecha de Factura</td>
              <td class="littletablerow"><?php echo $fechafa ?></td>
              <td class="littletablerowth">Nombre</td>
              <td colspan="3" class="littletablerow"><?php echo $nombre ?></td>
            </tr>
            <tr>
              <td class="littletablerowth">Factura</td>
              <td class="littletablerow"><?php echo $factura?></td>
            </tr>
          </table>
          <?php echo $items ?>