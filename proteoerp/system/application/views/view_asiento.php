<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:
 
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_scripts;
echo $form_begin?>
<table align='center'>
	<tr>
		<td align=right>
			<?php echo $container_tr?>
		</td>
	</tr>
	<tr>
		<td>
<table style="margin:0;width:98%;">
	<tr>
		<td colspan=5 class="littletableheader">Encabezado</td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->fecha->label ?></td>
		<td class="littletablerow"><?php echo $form->fecha->output ?></td>
		<td class="littletablerowth" align='center'><center><?php echo $form->status->label ?></center></td>
		<td class="littletablerowth"><?php echo $form->descrip->label ?></td>
		<td class="littletablerow" style="width:300px;" ><?php echo $form->descrip->output ?></td>
	</tr>
	<tr>	
		<td class="littletablerowth"><?php echo $form->comprob->label ?></td>
		<td class="littletablerow"><?php echo $form->comprob->output ?></td>
		<td class="littletablerow" align='center'><?php echo $form->status->output ?></td>
		<td class="littletablerowth">Cuenta</td>
		<td class="littletablerow">&nbsp;</td>
	</tr>
</table>
<?php echo $form->detalle->output ?> <?php //echo $detalle ?>
<table style="margin:0;width:98%;">
	<tr>
		<td colspan=6 class="littletableheader">Totales</td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->debe->label ?></td>
		<td class="littletablerow" ><?php echo $form->debe->output?></td>
		<td class="littletablerowth"><?php echo $form->haber->label   ?></td>
		<td class="littletablerow"><?php echo $form->haber->output  ?></td>
		<td class="littletablerowth"><?php echo $form->total->label   ?></td>
		<td class="littletablerow"><?php echo $form->total->output  ?></td>
	</tr>
</table>

<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>