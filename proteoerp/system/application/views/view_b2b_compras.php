<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';
echo $form_scripts;
echo $form_begin; ?>
<table align='center'>
	<tr>
		<td align=right>
			<?php echo $container_tr?>
		</td>
	</tr><tr>
		<td>
			<table width="100%"  style="margin:0;width:100%;">
				<tr>
					<td colspan=6 class="littletableheader">Datos de la compra</td>
				</tr><tr>
					<td width="100" class="littletablerowth"><?php echo $form->fecha->label     ?></td>
					<td width="100" class="littletablerow">  <?php echo $form->fecha->output    ?></td>
					<td width="100" class="littletablerowth"><?php echo $form->pcontrol->label  ?></td>
					<td width="100" class="littletablerow">  <?php echo $form->pcontrol->output ?></td>
					<td width="119" class="littletablerowth"><?php echo $form->proveedor->label ?></td>
					<td             class="littletablerow">  <?php echo $form->proveedor->output?></td>
				</tr><tr>
					<td class="littletablerowth"><?php echo $form->numero->label  ?></td>
					<td class="littletablerow">  <?php echo $form->numero->output ?></td>
					<td class="littletablerowth"><?php echo $form->tipo->label    ?></td>
					<td class="littletablerow">  <?php echo $form->tipo->output   ?></td>
					<td class="littletablerowth"><?php echo $form->nombre->label  ?></td>
					<td class="littletablerow">  <?php echo $form->nombre->output ?></td>
				</tr>
			</table>
			<?php echo $form->detalle->output ?>
			<table  width="100%" style="margin:0;width:100%;">
				<tr>
					<td colspan=3 class="littletableheader">Totales</td>
				</tr><tr>
					<td width="500" class="littletablerow">&nbsp;</td>
					<td width="111" class="littletablerowth"><?php echo $form->subt->label ?> </td>
					<td width="139" class="littletablerow" align='right'><?php echo $form->subt->output ?> </td>
				</tr><tr>
					<td class="littletablerowth">&nbsp;</td>
					<td class="littletablerowth"><?php echo $form->iva->label ?></td>
					<td class="littletablerow" align='right'><?php echo $form->iva->output ?></td>
				</tr><tr>
					<td class="littletablerowth">&nbsp;</td>
					<td class="littletablerowth"><?php echo $form->total->label ?></td>
					<td class="littletablerow" align='right'><?php echo $form->total->output ?></td>
				</tr>
			</table>
			<?php echo $form_end?>
			<?php echo $container_bl ?>
			<?php echo $container_br ?>
		<td>
	<tr>
</table>

<?php endif; ?>