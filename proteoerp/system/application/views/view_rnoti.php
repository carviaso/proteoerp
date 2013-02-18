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
					<td class="littletableheaderc"><?php echo $form->codprod->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->codprod->output;?></td>
					<td class="littletableheaderc"><?php echo $form->numfact->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->numfact->output; ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->descprod->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->descprod->output; ?></td>
					<td class="littletableheaderc"><?php echo $form->fechafact->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->fechafact->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->serial->label;   ?></td>
					<td class="littletablerow"    ><?php echo $form->serial->output;  ?></td>
					<td class="littletableheaderc"><?php echo $form->garantia->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->garantia->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->reporte->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->reporte->output;?></td>
					<td class="littletableheaderc"></td>
					<td class="littletablerow"    ></td>
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
					<td class="littletableheaderc"><?php echo $form->codcliente->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->codcliente->output; ?></td>
					<td class="littletableheaderc"><?php echo $form->nomcliente->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->nomcliente->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
	<tr>
<?php if ($estado == 'NOTIFICADO') { ?>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #F6F8FF;'>
			<table border='0' width="100%">
				<tr>
					<td class="littletableheaderc"><?php echo $form->falla->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->falla->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
<?php } elseif ($estado == 'RECIBIDO') {  ?>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #F6F8FF;'>
			<table border='0' width="100%">
				<tr>
					<th colspan='2' style="font-size:14pt;align:center;background:#FF8F35;"    >RECEPCION DE EQUIPO</th>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->frecep->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->frecep->output; ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->observacion->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->observacion->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td>


<?php } elseif ($estado == 'REVISADO') {  ?>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #F6F8FF;'>
			<table border='0' width="100%">
				<tr>
					<th colspan='2' style="font-size:14pt;align:center;background:#35A6FF;"    >REVISION Y DIAGNOSTICO DEL EQUIPO</th>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->fechadiag->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->fechadiag->output; ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->diagnostico->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->diagnostico->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td>

<?php } elseif ($estado == 'REPARADO') {  ?>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #F6F8FF;'>
			<table border='0' width="100%">
				<tr>
					<th colspan='2' style="font-size:14pt;align:center;background:#FFDF80;"    >REPARACION DEL EQUIPO</th>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->frepara->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->frepara->output; ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->repara->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->repara->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td>

<?php } elseif ($estado == 'ENTREGADO') {  ?>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #F6F8FF;'>
			<table border='0' width="100%">
				<tr>
					<th colspan='2' style="font-size:14pt;align:center;background:#84AA5C;" >ENTREGA DEL EQUIPO</th>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->fentrega->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->fentrega->output; ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->entrega->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->entrega->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
<?php } ?>

	</tr>
</table>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end?>
<?php endif; ?>
