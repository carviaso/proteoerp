<?php echo $form_scripts; ?>
<?php echo $form_begin;   ?>
<?php
//$container_tr=join("&nbsp;", $form->_button_container["TR"]);
//$container_bl=join("&nbsp;", $form->_button_container["BL"]);
//$container_br=join("&nbsp;", $form->_button_container["BR"]);
?>
<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>

<?php
/*
<table border=0 width="100%">
	<tr>
		<td>
			<?php if($form->_status=='show'){ ?>
			<a href='<?php echo base_url()."compras/sprv/consulta/".$form->_dataobject->get('id'); ?>' >
			<?php
				$propiedad = array('src' => 'images/ojos.png', 'alt' => 'Consultar Movimiento', 'title' => 'Consultar Detalles','border'=>'0','height'=>'25');
				echo img($propiedad);
			?>
			</a>
			<?php } ?>
		</td>
		<td align='center' valign='middle'>
			<?php  if ($form->tipo->value=='0') echo "<div style='font-size:14px;font-weight:bold;color: #B40404'>***DESACTIVADO***</div>"; ?>&nbsp;
		</td>
		<td align='right'><?php echo $container_tr; ?></td>
	</tr>
</table>
*/
?>

<fieldset style='border: 1px outset #9AC8DA;background: #FFFDE9;'>
<table border=0 width="100%">
	<tr>
		<td colspan='2'>
			<table border=0 width="100%">
			<tr>
				<td width="70" class="littletableheaderc"><?=$form->proveed->label  ?></td>
				<td width='140' class="littletablerow" ><?=$form->proveed->output ?></td>
				<td class="littletableheaderc"><?=$form->rif->label ?></td>
				<td align='right' class="littletablerow"><?=$form->rif->output ?></td>
			</tr><tr>
				<td class="littletableheaderc"><?=$form->nombre->label ?></td>
				<td colspan='3' class="littletablerow"><?=$form->nombre->output?></td>
			</tr><tr>
				<td class="littletableheaderc"><?=$form->contacto->label  ?></td>
				<td colspan='3' class="littletablerow"><?=$form->contacto->output ?></td>
			</tr><tr>
				<td class="littletablerow"><?=$form->nomfis->label;?></td>
				<td colspan='3' class="littletablerow"><?=$form->nomfis->output?></td>
			</tr>
			</table>
		</td>
		<td>
			<table border=0 width="100%">
			<tr>
				<td width='50' class="littletableheaderc"><?=$form->grupo->label ?></td>
				<td  class="littletablerow"><?=$form->grupo->output?></td>
			</tr><tr>
				<td class="littletableheaderc"> <?=$form->tipo->label ?></td>
				<td class="littletablerow"> <?=$form->tipo->output ?></td>
			</tr><tr>
				<td class="littletableheaderc"><?=$form->tiva->label  ?></td>
				<td class="littletablerow"><?=$form->tiva->output ?></td>
			</tr><tr>
				<td class="littletableheaderc"><?=$form->reteiva->label  ?></td>
				<td class="littletablerow"><?=$form->reteiva->output ?></td>
			<tr><tr>
				<td class="littletablerow"><?php echo $form->cuenta->label;?></td>
				<td class="littletablerow"><?php echo $form->cuenta->output;?></td>
			</tr>
			<tr><tr>
				<td class="littletablerow"><?php echo $form->canticipo->label;?></td>
				<td class="littletablerow"><?php echo $form->canticipo->output;?></td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</fieldset>

<table  width="100%" border='0'>
	<tr>
		<td valign='top'>
			<fieldset style='border: 1px outset #9AC8DA;background: #E0ECF8;'>
			<legend class="titulofieldset" style='color: #114411;'>Direcci&oacute;n</legend>
			<table width="100%" cellspacing='1' cellpadding='1'>
				<tr>
					<td class="littletablerow"><?php echo $form->direc1->output ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletablerow"><?php echo $form->direc2->output ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletablerow" style='font-size:11;'><?php echo $form->direc3->output  ?>&nbsp;</td>
				</tr>
			</table>
			</fieldset>
		</td>
		<td valign='top'>
			<fieldset style='border: 1px outset #9AC8DA;background: #E0ECF8;'>
			<legend class="titulofieldset" style='color: #114411;'>Contacto</legend>
			<table width= "100%" cellspacing='0' cellpadding='0'>
				<tr>
					<td class="littletableheaderc"><?php echo $form->telefono->label ?></td>
					<td class="littletablerow"><?php echo $form->telefono->output ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->email->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->email->output ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
<table  width="100%" border='0' >
	<tr>
		<td>
			<fieldset style='border: 1px outset #8A0808;background: #FFFBE9;'>
			<table width= '100%' >
				<tr>
					<td class="littletableheaderc">Banco</td>
					<td class="littletableheaderc">Cuenta</td>
				</tr><tr>
					<td class="littletablerow"><?php echo $form->banco1->output;  ?></td>
					<td class="littletablerow"><?php echo $form->cuenta1->output; ?></td>
				</tr><tr>
					<td class="littletablerow"><?php echo $form->banco2->output;  ?></td>
					<td class="littletablerow"><?php echo $form->cuenta2->output; ?></td>
				</tr><tr>
					<td class="littletablerow"><?php echo $form->prefpago->label;  ?></td>
					<td class="littletablerow"><?php echo $form->prefpago->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td><td>
			<fieldset style='border: 1px outset #8A0808;background: #FFFBE9;'>
			<table width= '100%' >
				<tr>
					<td class="littletableheaderc"><?php echo $form->cliente->label  ?></td>
					<td class="littletablerow"><?php echo $form->cliente->output ?></td>
				<tr><tr>
					<td class="littletableheaderc"><?php echo $form->codigo->label  ?></td>
					<td class="littletablerow"><?php echo $form->codigo->output ?></td>
				<tr><tr>
					<td colspan='2' class="littletableheaderc"><?php echo $form->url->label  ?>
					<?php echo $form->url->output ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
		<?php if( $form->_status == 'show') {  ?>
		<?php } ?>
	</tr>
</table>
<?php echo $container_bl.$container_br; ?>
<?php echo $form_end?>
<?php
	//foreach (get_object_vars($form) as $label=>$tiene)
	//	echo "$label => $tiene <br>";
	//echo '<pre>';print_r($form->grupo->request);echo '</pre>'
?>
