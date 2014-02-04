<span style="font-size:18px">
Fecha Ultimo Kardex es: <strong><?php echo dbdate_to_human($fsfac) ?></strong></br>
Venta del <strong><?php echo dbdate_to_human($fsfac) ?></strong> es: <span style="color:red;font-size:22px"><strong><?=nformat($vendido) ?></strong></span></br>
Efectivo del <strong><?php echo dbdate_to_human($fsfac) ?></strong> es: <span style="color:blue;font-size:22px"><strong><?=nformat($efectivo) ?></strong></span></br>
Credito del <strong><?php echo dbdate_to_human($fsfac) ?></strong> es: <span style="color:yellow;font-size:22px"><strong><?=nformat($credito) ?></strong></span></br>
</span>
