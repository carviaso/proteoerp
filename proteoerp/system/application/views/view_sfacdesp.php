<style type="text/css">
<!--
.style1 {font-family: Arial, Helvetica, sans-serif; font-size: 14}
.style4 {font-size: 12}
.style5 {font-family: Arial, Helvetica, sans-serif; font-size: 12; }
-->
</style>
<table align='center'>
	<tr>
		<td align=right></td>
	</tr>
	<tr>
	  <td>
      <table  width="100%" style="margin:0;width:100%;" >
	    <tr bgcolor="#545294">                                                           
				<td align="center" class="littletableheaderb style1 style4">Codigo</td>
				<td align="center" class="littletableheaderb style1 style4">Descripcion</td>
				<td align="center" class="littletableheaderb style1 style4">Cantidad</td>
				<td align="center" class="littletableheaderb style1 style4">Unidad</td>
				<td align="center" class="littletableheaderb style1 style4">Paking</td>
				<td align="center" class="littletableheaderb style1 style4">Precio</td>
				<td align="center" class="littletableheaderb style1 style4">Total</td>            
				<td align="center" class="littletableheaderb style1 style4">Cant. a Despachar</td>				
				<td align="center" class="littletableheaderb style1 style4">Despachado</td>				
				<td align="center" class="littletableheaderb style1 style4">¿Despachar?</td>     
	    </tr>
		 <?php foreach ($detalle AS $items){?>
		 <form id="form1" name="form1" method="post" action="<?php echo site_url("ventas/sfacdespfyco/guardar/"); ?>">
		 <input type="hidden" name="numa"  value="<?php echo $items->numa ?>"/>
		 <input type="hidden" name="tipoa"  value="<?php echo $items->tipoa ?>"/>
	     <tr bgcolor="#717394">   
				<td class="littletablerow" a align="left" ><input name="codigoa[]"  type="text"  value="<?php echo $items->codigoa ?>"  readonly="false"/></td>
				<td class="littletablerow" a align="left"><span class="style5">
			    <?php echo $items->desca ?>
				</span></td>                 
				<td class="littletablerow" a align="center"><span class="style5">
			    <?php echo $items->cana ?>
				</span></td>
				<td class="littletablerow" a align="center"><span class="style5">
			    <?php echo $items->unidad ?>
				</span></td> 
				<td class="littletablerow" a align="center"><span class="style5">
			    <?php echo $items->clave ?>
				</span></td>         
				<td class="littletablerow" a align="right"><span class="style5">
			    <?php echo $items->preca ?>
				</span></td>									         		
				<td class="littletablerow" a align="right"><span class="style5">
			    <?php echo $items->tota ?>
				</span></td>    						
           		<td align="right" bgcolor="#2067B5" class="littletablerow" a><input name="ultidespachado[]" type="text"  value="" size="11"/> </td>
					<td align="right" bgcolor="#2067B5" class="littletablerow" a><input name="cdespacha[]" type="text"  value="<?php echo $items->cdespacha ?>" size="11" readonly="false"/> </td>
		   <td align="center" bgcolor="#2067B5" class="littletablerow"><span class="style5">
		     <select name="despacha[]">
			   <?php $valor=$items->despacha ?>
			   <?php if($valor=='I'){ ?>
			    <option>N</option>
		       <?php }else{?>
		       <option><?php echo $items->despacha ?></option>
			   <?php }?>
		       <?php IF($valor=='S'){?>
		       <option>N</option>
		       <?php }else{?>
		       <option>S</option>
		       <?php }?>
	        </select>
		   </span> </td>
		   </tr>
	     <tr>
		 <?php } ?>  
		  <td colspan="7" class="littletablerow" a align="right"><input name="Guardar" type="submit" id="Guardar" value="Guardar" /></td>
        </tr>
	    </form>
</table>
	  <td>
	<tr>
<table>
