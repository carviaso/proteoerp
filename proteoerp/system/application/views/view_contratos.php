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
<table align='center' style="margin:0;width:98%;">
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
		<td class="littletablerowth"><?php echo $form->codigo->label ?></td>
		<td class="littletablerow"><?php echo $form->codigo->output ?></td>
		<td class="littletablerowth" align='rigth'><center><?php echo $form->tipo->label ?></center></td>
		<td class="littletablerow" ><?php echo $form->tipo->output ?></td>
	</tr>
	<tr>	
		<td class="littletablerowth"><?php echo $form->nombre->label ?></td>
		<td class="littletablerow" colspan='3'><?php echo $form->nombre->output ?></td>
	</tr>
</table>
<?php echo $form->detalle->output ?>
<table style="margin:0;width:98%;">
	<tr>
		<td class="littletableheader"><?php echo $form->observa1->label ?></td>
	</tr>
	<tr>
		<td class="littletablerow"  ><?php echo $form->observa1->output ?></td>
	</tr>
	<tr>	
		<td class="littletablerow"  ><?php echo $form->observa2->output ?></td>
	</tr>
</table>


<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
		<td>
	<tr>
<table>
<?php endif; ?>