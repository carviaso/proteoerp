<p>
<table align='center' width='100%' border=0 background=/proteoerp/images/logopm.jpg>
	<table style="background-color:#FFDDDF;" width='100%'>
		<tr>
			<td>
				<table  align="center">
				<tr><td STYLE="font-size:24pt;text-align:center"><strong><?php echo $descrip ?></strong></td></tr>
				</table>
				
			  <table  align="center">  
				<tr>
					<td width="50%"> <strong>Departamento: </strong>    <?php echo $depto ?> </td>	
				   <td width="50%"><strong>Familia:      </strong>    <?php echo $familia?></td>
				</tr>			            
				<tr>               
					<td width="50%"> <strong>Grupo:        </strong>    <?php echo $grupo ?> </td>
					<td width="50%"> <strong>Marca:        </strong>    <?php echo $marca ?> </td>
				</tr>
				</table>
			
				<table  align="center" cellspan="2">
				<tr>
				  <td width="50%"><strong>C&oacute;digo:       </strong> <?php echo $codigo ?></td>
					<td width="50%"><strong>C&oacute;digo Barras:</strong> <?php echo $barras ?></td>
				</tr> 
				<tr>  
					<td width="50%"><strong>Referencia:          </strong> <?php echo $referen ?></td>
					<td width="50%"><strong>Existencia:          </strong> <?php echo $existen ?></td>
				</tr>	
				</table>
			</td>
		</tr>
	</table>
	
	<table width="100%" align="center" style="background-color:#FFFADA; font-size:18pt;">
		<tr align="center"><th>&nbsp;</th><th>Base </th><th>Precio  </th><th>Concepto</th></tr>
		<tr align="right"><td><strong>1:</strong></td> <td><?php echo $base1 ?>Bs.</td><td><?php echo $precio1 ?>Bs.</td><td align="left">* Precio Oferta</td></tr>
		<tr align="right"><td><strong>2:</strong></td> <td><?php echo $base2 ?>Bs.</td><td><?php echo $precio2 ?>Bs.</td><td align="left">* Cliente Preferencial</td></tr>
		<tr align="right"><td><strong>3:</strong></td> <td><?php echo $base3 ?>Bs.</td><td><?php echo $precio3 ?>Bs.</td><td align="left">* M&aacute;s de <?php echo $dvolum1 ?> unidades</b></td></tr>
		<tr align="right"><td><strong>4:</strong></td> <td><?php echo $base4 ?>Bs.</td><td><?php echo $precio4 ?>Bs.</td><td align="left">* M&aacute;s de <?php echo $dvolum2 ?> unidades</b></td></tr>	
	</table>

	<table width="100%">
		<tr>
			<td valign="top" align="right" width="30%">
				<table style="background-color:#E5E5E5" width="100%">
					<tr><th>Presentaci&oacute;n </th></tr>
					<tr><td><?php echo $fracxuni ?> <strong>X</strong> <?php echo $dempaq ?> <strong>=</strong> <?php echo $mempaq ?>               </td></tr>
					<tr><td><strong>Ensamblado:</strong> <?php echo ($ensambla='S')?'SI':($ensambla='N')?'NO':'' ?></td></tr>
					<tr><td><strong>Des/Epq:   </strong>                                                   </td></tr>
					<tr><td>                              <?php echo $empaque ?>                                   </td></tr>
				</table>
			</td>
		
			<td valign="top" align="center" width="20%">
				<table  style="background-color:#E5E5E5" width="100%">
					<tr align="right"><strong>Tama&ntilde;o: </strong><td><?php echo $tamano ?></td></tr>
					<tr align="right"><strong>Medida:        </strong><td><?php echo $medida ?></td></tr>
					<tr align="right"><strong>Serial:        </strong><td><?php echo $serial ?></td></tr>
					<tr align="right"><strong>Exis. Minima   </strong><td><?php echo $minimo ?></td></tr>
					<tr align="right"><strong>Exis. Maxima   </strong><td><?php echo $maximo ?></td></tr>	
				</table>
			</td>
			
			<td valign="top" align="left" width="50%">
				<?
				$mSQL_2  = $this->db->query($almacenes);
				$detalle =$mSQL_2->result();
				?>
				<table width="100%" border='0' style="background-color:#E5DDE5">
				<tr>
				<th>Alma                 </th>
				<th>Sucursal             </th>
				<th><?php echo $mempaq  ?>       </th>
				<th><?php echo $dempaq ?>        </th>
				</tr>
				<?php 
				$i=0;
				foreach($detalle AS $items ){$i++;
						?>
				<tr>
				<td><?php echo $items->ubica?>   </td>
				<td align="center"><?php echo $items->sucursal?>  </td>
				<td align="right"><?php echo $items->cantidad?></td>
				<td align="right"><?php echo $items->fraccion?></td>
				</tr>
				<?php 
				}
				?>
				</table>
			</td>
		</tr>
	</table>