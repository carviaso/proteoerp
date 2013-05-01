<html>
<?php
foreach ($monedas AS $moneda){
$mon=$moneda->moneda;
?>
<table width="100%" align="center" border="0" >
	<tr>
	  <td colspan="2" bgcolor="#2067B5" align="center" style="color:#FFFFFF" ><b>MONEDA <?=$moneda->moneda ?></d></td>
  </tr>
	<tr><td>
		
			<?php $ban2=true;
			foreach ($grupo AS $item){?>
			<?php $a="<?php echo $item->tipocta?>"?>
			
			    <?php $total=0;$class='odd';$c=1;?>			    
			    <?php 
			    $ban=true;
			    foreach ($detalle AS $items){?>
			    <?php $b="<?php echo $items->tipocta?>";$mon2=$items->moneda; ?>
					<?php if(($a==$b)&&($mon==$mon2)){
								if($ban2){
									$ban2=false;
									?>
									<div align="center" style="background:#B0D1F5" ><b>BANCOS</b></div>
									<table width="100%" align="center" border="0">
									<tr>
									    <td width="10%"  class="littletablerowth"><b>C&oacute;digo  </b></td>
									    <td width="40%"  class="littletablerowth"><b>Banco   </b></td>
									    <td width="25%"  class="littletablerowth"><b>Cuenta  </b></td>
									    <td width="25%"  class="littletablerowth"><b>Saldo   </b></td>
									  </tr>
									</table>								
									<?php
									}
									if($ban){																	
									?>
										<table width="100%" align="center" border="0">
			  							<tr>
			  							  <td colspan="2" bgcolor="#D4D2D0" class="tableheader"><b><?php echo $item->tipocta2 ?></b></td>
			  							</tr>
			  							<tr>
			  						  	<td colspan="2" >
						<?php }?>
						
					<?php $total+=$items->saldo;if(($c%2)==0)$class='even';else $class='odd'; $c++;?>
			 
			   <table width="100%" align="center" border="0" >
			      <tr class="<?php echo $class?>" >
			        <td width="10%" class="littletablerow"><? echo anchor("/finanzas/analisisbanc/meses/$items->codbanc/$ano",$items->codbanc);$t=$items->codbanc;//movimientos/$items->codbanc
			         ?></td>
			        <td width="40%" class="littletablerow"><?php echo $items->banco ?></td>
			        <td width="25%" class="littletablerow"><?php echo $items->numcuent ?></td>
			        <td width="25%" class="littletablerow"><div align="right"><?echo number_format($items->saldo,2,',','.'); ?></div></td>			        
			      </tr>
			    </table>
			    		<?php 
			    		if($ban){
								$ban=false;
			    		?>
							</td>
							 </tr>
							 <tr >
							   <td bgcolor="#D4D2D0" class="tableheader"><div align="right"><b>Total:</b></div></td>
							   <td bgcolor="#D4D2D0" width="25%" class="tableheader"><b><div align="right"><? echo number_format($total,2,',','.'); ?></div></b></td>
							 </tr>
							</table>			    		
				  		<?php
							}
				   } ?>
					<?php } ?>
				
				
			<?php } ?>
			
				

			<?php $ban2=true;
			foreach ($grupo2 AS $item2){?>
			<?php $a="<?php echo $item2->tipocta?>"?>
			
			    <?php $total=0;$class='odd';$c=1;?>			    
			    <?php foreach ($detalle2 AS $items2){?>
			    <?php $b="<?php echo $items->tipocta?>";$mon2=$items2->moneda; ?>
					<?php if(($a==$b)&&($mon==$mon2)){
								if($ban2){
									$ban2=false;
									?>
									<div align="center" style="background:#B0D1F5" ><b>CAJAS</b></div>
									<table width="100%" align="center" border="0">
									<tr>
									    <td width="10%"  class="littletablerowth"><b>C&oacute;digo  </b></td>
									    <td width="40%"  class="littletablerowth"><b>Banco   </b></td>
									    <td width="25%"  class="littletablerowth"><b>Cuenta  </b></td>
									    <td width="25%"  class="littletablerowth"><b>Saldo   </b></td>
									  </tr>
									</table>
									
									<?php
									}									
									if($ban){
									?>
										<table width="100%" align="center" border="0">
			  							<tr>
			  							  <td colspan="2" bgcolor="#D4D2D0" class="tableheader"><b><?php echo $item2->tipocta2 ?></d></td>
			  							</tr>
			  							<tr>
			  						  	<td colspan="2" >
						<?php }?>
						
					<?php $total+=$items2->saldo;if(($c%2)==0)$class='even';else $class='odd'; $c++;?>
			 
			   <table width="100%" align="center" border="0" >
			      <tr class="<?php echo $class?>" >
			        <td width="10%" class="littletablerow"><? echo anchor("/finanzas/analisisbanc/meses/$items->codbanc/$ano",$items2->codbanc);$t=$items->codbanc; //movimientos/$items->codbanc
			        ?></td>
			        <td width="40%" class="littletablerow"><?php echo $items2->banco ?></td>
			        <td width="25%" class="littletablerow"><?php echo $items2->numcuent ?></td>
			        <td width="25%" class="littletablerow"><div align="right"><?echo number_format($items2->saldo,2,',','.'); ?></div></td>			        
			      </tr>
			    </table>
			    	<?php
			    	if($ban){
								$ban=false;
			    	?>
					  </td>
					  </tr>
					  <tr >
					    <td bgcolor="#D4D2D0" class="tableheader"><div style="color:#6B5130" align="right"><b>Total:</b></div></td>
					    <td bgcolor="#D4D2D0" width="25%" class="tableheader"><b><div style="color:#6B5130" align="right"><? echo number_format($total,2,',','.'); ?></div></b></td>
					  </tr>
					</table>
			    
				<?php }
				  } ?>
					<?php } ?>    
				
			<?php } ?>
		</td>
		</tr>
		
<?php } ?>
</table>
</html>