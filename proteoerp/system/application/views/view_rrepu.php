<?php
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	$meco = $form->output;
	$meco = str_replace('class="tablerow"','class="tablerow" style="font-size:20px; align:center;" ',$meco);
	echo $meco."</td><td align='center'>".img("images/borrar.jpg");
else:
?>

<?php echo $script;       ?>
<?php echo $form_scripts; ?>
<?php echo $form_begin;   ?>

<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>

<table border='0' width="100%">
	<tr>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #F6F8FF;'>
			<table border='0' width="100%">
				<tr>
					<td class="littletablerowth"><?php echo $form->id->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->id->output; ?></td>
					<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>

	<tr>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #F6F8FF;'>
			<table border='0' width="100%">

				<tr>
					<td class="littletablerowth"><?php echo $form->idrnoti->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->idrnoti->output; ?></td>
					<td class="littletablerowth"><?php echo $form->serial->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->serial->output; ?></td>
				</tr>

				<tr>
					<td class="littletablerowth"><?php echo $form->codprod->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->codprod->output; ?></td>
					<td class="littletablerowth"><?php echo $form->descprod->label;  ?></td>
					<td colspan='3' class="littletablerow"  ><?php echo $form->descprod->output; ?></td>
				</tr>

			</table>
			</fieldset>
		</td>
	</tr>


	<tr>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #F6F8FF;'>
			<table border='0' width="100%">
				<tr>
					<td class="littletablerowth"><?php echo $form->proveed->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->proveed->output; ?></td>
					<td class="littletablerowth"><?php echo $form->nombre->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->nombre->output; ?></td>
				</tr>



<?php /*
				<tr>
					<td class="littletablerowth"><?php echo $form->diagnostico->label;  ?></td>
					<td colspan='3' class="littletablerow"  ><?php echo $form->diagnostico->output; ?></td>
				</tr>
*/?>

				<tr>
					<td class="littletablerowth"><?php echo $form->repuesto->label;  ?></td>
					<td colspan='3' class="littletablerow"  ><?php echo $form->repuesto->output; ?></td>
				</tr>
				<tr>
					<td class="littletablerowth"><?php echo $form->cant->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->cant->output; ?></td>
				</tr>

<?php /*

				<tr>
					<td class="littletablerowth"><?php echo $form->reporte->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->reporte->output; ?></td>
				</tr>
				<tr>
					<td class="littletablerowth"><?php echo $form->estado->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->estado->output; ?></td>
				</tr>
				<tr>
					<td class="littletablerowth"><?php echo $form->fecharecep->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->fecharecep->output; ?></td>
				</tr>
*/?>

			</table>
			</fieldset>
		</td>
	</tr>
</table>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end?>
<?php endif; ?>
