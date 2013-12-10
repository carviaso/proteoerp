<style>
.rotado {
	border:1px solid ;
	border-radius:3px;
	background:grey;
	font-weight:bold;
	height:20px;
	margin:0px
	width:55px;
	-webkit-transform: rotate(-90deg);
	-moz-transform:    rotate(-90deg);
	-o-transform:      rotate(-90deg);
	text-align:center;
	display: inline-block;
}
</style>

<?php echo $form_scripts?>
<?php echo $form_begin?>
<?php
$container_tr=join('&nbsp;', $form->_button_container['TR']);
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
?>
<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table width="100%">
	<tr>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #FFFFFF;'>
			<table style="height:100%;width:100%;border-collapse:collapse;">
				<tr>
					<td class="littletableheaderc"><?php echo $form->cajero->label  ?>*</td>
					<td class="littletablerow"    ><?php echo $form->cajero->output ?></td>
					<td class="littletableheaderc"><?php echo $form->status->label   ?>*</td>
					<td class="littletablerow"    ><?php echo $form->status->output  ?></td>
				</tr><tr>
					<td class="littletableheaderc"><?php echo $form->nombre->label  ?>*</td>
					<td class="littletablerow"    ><?php echo $form->nombre->output ?></td>
					<td class="littletableheaderc"><?php echo $form->almacen->label  ?>*</td>
					<td class="littletablerow"    ><?php echo $form->almacen->output ?></td>
				</tr><tr>
					<td class="littletableheaderc"><?php echo $form->caja->label    ?>*</td>
					<td class="littletablerow"    ><?php echo $form->caja->output   ?></td>
					<td class="littletableheaderc"><?php echo $form->clave->label    ?>*</td>
					<td class="littletablerow"    ><?php echo $form->clave->output   ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>

<table style="width:100%;border-collapse:collapse;padding:0px;">
	<tr>
		<td valign='top'>
			<fieldset style='border: 1px outset #9AC8DA;background: #EAEAEA;'>
			<table style="height:100%;width:100%;border-collapse:collapse;">
				<tr>
					<td rowspan='3' style='margin:0px;height:20px;padding:0px;' ><div class='rotado' style='padding:5px 9px;font-size:16px;'>INICIO</div></td>
					<td class="littletableheaderc"><?php echo $form->fechaa->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->fechaa->output ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->horaa->label   ?></td>
					<td class="littletablerow"    ><?php echo $form->horaa->output  ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->apertura->label   ?></td>
					<td class="littletablerow" style='font-size:11;'><?php echo $form->apertura->output  ?>&nbsp;</td>
				</tr>
			</table>
			</fieldset>
		</td>
		<td  valign="top">
		<fieldset style='border: 1px outset #9AC8DA;background: #EAEAEA;'>
			<table style="height:100%;width:100%;border-collapse:collapse;">
				<tr>
					<td rowspan='3' style='margin:0px;'><div class='rotado' style='padding:5px;font-size:15px;'>CIERRE</div></td>
					<td class="littletableheaderc"><?php echo $form->fechac->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->fechac->output ?></td>
				</tr><tr>
					<td class="littletableheaderc"><?php echo $form->horac->label   ?></td>
					<td class="littletablerow"    ><?php echo $form->horac->output  ?></td>
				</tr><tr>
					<td class="littletableheaderc"><?php echo $form->cierre->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->cierre->output ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>


<table width="100%" border='0'>
	<tr>
		<td width='50%'>
			<fieldset style='border: 1px outset #9AC8DA;background: #FFFFFF;'>
			<legend class="titulofieldset" style='color: #114411;'>Mesas Restaurant</legend>
			<table style="height:100%;width:100%;border-collapse:collapse;">
				<tr>
					<td class="littletableheaderc"><?php echo $form->mesai->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->mesai->output ?></td>
				</tr><tr>
					<td class="littletableheaderc"><?php echo $form->mesaf->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->mesaf->output ?></td>
				</tr>
			</table>
			</fieldset>
		</td><td>
			<fieldset style='border: 1px outset #9AC8DA;background: #FFFFFF;'>
			<legend class="titulofieldset" style='color: #114411;'>Hora Feliz Restaurant</legend>
			<table style="height:100%;width:100%;border-collapse:collapse;">
				<tr>
					<td class="littletableheaderc"><?php echo $form->horai->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->horai->output ?></td>
				</tr><tr>
					<td class="littletableheaderc"><?php echo $form->horaf->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->horaf->output ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr><tr>
		<td colspan='2' class="littletableheaderc">
		<fieldset style='border: 1px outset #9AC8DA;background: #FFFFFF;'>
			<span class="littletableheaderc">Directorio de trabajo:</span>
			<span class="littletablerow"><?php echo $form->directo->output ?></span>
		</fieldset>
		</td>
	</tr>
</table>
<?php echo $container_bl.$container_br.$form_end; ?>
