<style type="text/css">
<!--
.Estilo1 {font-size: 9px}
.Estilo3 {font-size: 9px; font-weight: bold; }
.Estilo5 {font-size: 10px; color: #FF0000; }
.Estilo6 {font-size: 14px; color: #FF0000; }
.Estilo8 {font-size: 14px}
.Estilo10 {font-size: 24px}
.Estilo12 {
	color: #0000FF;
	font-weight: bold;
	font-size: 14px;
}
.Estilo14 {font-size: 9px; color: #000000; }
.Estilo15 {font-size: 12px}
-->
</style>
<script type="text/javascript" language="javascript">
function foc(){
	href="#arriba";	
	form1.cod.focus();	
}
</script>
<body onLoad="foc();" bgcolor="#CCCCCC">
<p><a name="arriba" ></a></p>
<form id="form1" name="form1" method="post" action="../../inventario/conprecio/precios">
  <strong>C&oacute;digo</strong>    
      <input type="text" name="cod" id="cod" value="" />   
    <span class="Estilo1">
      <?php if($ban)echo "<a class='Estilo12' href='#abajo'>Ver Imagen</a>";?>
    </span>
<?php
if($ban){
?></form>
<table width="100%" border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
	  <tr>
	    <td width="5%" bgcolor="#E1E1E1" ><span class="Estilo3">Descripci&oacute;n:</span></td>
	    <td bgcolor="#FFFFFF" ><span class="Estilo1"><? echo $descrip?></span></td>
	  </tr>
	  <tr>
	    <td bgcolor="#E1E1E1" ><span class="Estilo3">Marca:</span></td>
	    <td bgcolor="#FFFFFF" ><span class="Estilo1"><? echo $marca?></span></td>
	  </tr>
	  <tr>
	    <td bgcolor="#E1E1E1" ><span class="Estilo3">Modelo:</span></td>
	    <td bgcolor="#FFFFFF" ><span class="Estilo1"><? echo $modelo?></span></td>
	  </tr>
	  <tr>
	    <td bgcolor="#E1E1E1" ><span class="Estilo3">Precio: </span></td>
	    <td bgcolor="#FFFFFF" ><span class="Estilo8"> <? echo $precio1?>. </span><span class="Estilo3"> Iva: </span><span class="Estilo8"> <? echo $iva?>. </span><span class="Estilo3"> Total: </span><span class="Estilo10"><strong> <? echo $total?>.</strong></span></td>
      </tr>
	  <tr>
	    <td bgcolor="#E1E1E1" ><span class="Estilo3">Existencia:</span></td>
	    <td bgcolor="#FFFFFF" ><span class="Estilo1"><? echo $existen?></span></td>
	  </tr>
</table>
</br>	
	<?php 
	if(!empty($query)){
	?>
		<a name="abajo"></a>
		<table width="100%" border="0">
	<?php
		$num=$query->num_rows();
		$i=0;
		foreach($query->result_array() as $row){
	?>			
			<tr>
			  <td background="#arriba" scope="col"><a name="<?php echo $i; ?>"><a class="Estilo12" onClick="javascript:foc();" >Buscar</a></td>
			  <td scope="col"><?php if($i>0){$ant=$i-1;echo "<a class='Estilo12' href='#$ant'>Atras</a>";} ?></td>
		      <td scope="col"><?php if($i < $num-1){$sig=$i+1;echo "<a class='Estilo12' href='#$sig'>Siguiente</a>";} ?></td>
		  </tr>			
			<tr>
        <td width="14%" scope="col"><div align="left"><img src="<?=base_url()?>uploads/inventario/Image/<?php echo $row['nombre']; ?>" alt="principal" width="100" height="100"></div></td>
        <td width="86%" colspan="2" bgcolor="#FFFFFF" scope="col"><span class="Estilo14"><?php echo $row['comentario']?></span></td>
  		</tr>
        <tr>
		  <td colspan="3" <?php if($row['principal']=='S')echo "bgcolor='#FFFFFF'";?> scope="col"><div align="center"><span class="Estilo14"><strong><?php if($row['principal']=='S'){echo "Foto Principal";}else{ echo "&nbsp;"; }?>
	      </strong></span></div></td>
		  </tr>	
	<?php
			$i++;
		}
	?>
	</table>
<?php
	}
	?>	
<?php
}else{
?> 
    
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
	  <tr>
	    <th scope="col"><span class="Estilo6"><? echo $msg1 ?></span></th>
	  </tr>
	  <tr>
	    <td>&nbsp;</td>
  </tr>
	  <tr>
	    <td><span class="Estilo5"><? echo $msg2 ?></span></td>
	  </tr>
	  <tr>
	    <td><span class="Estilo5"><? echo $msg3 ?></span></td>
	  </tr>
	  <tr>
	    <td><span class="Estilo5"><? echo $msg4 ?></span></td>
	  </tr>
	</table>
<p>
      <?php
}
?>
</p>
    
    
</body>
</html>
