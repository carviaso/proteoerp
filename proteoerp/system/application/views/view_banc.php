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
			<fieldset style='border: 1px outset #9AC8DA;background: #FFFDE9;'>
			<table border=0 width="100%">
			<tr>
				<td width="90"    class="littletableheaderc"><?php echo $form->codbanc->label; ?></td>
				<td width="80"    class="littletablerow"    ><?php echo $form->codbanc->output;?></td>
				<td align='right' class="littletableheaderc"><?php echo $form->activo->label; ?></td>
				<td align='left'  class="littletablerow"    ><?php echo $form->activo->output;?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->tbanco->label; ?></td>
				<td colspan='3' class="littletablerow"   ><?php echo $form->tbanco->output;?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"           ><?php echo $form->banco->label; ?></td>
				<td colspan='3' class="littletablerow"   ><?php echo $form->banco->output;?></td>
			</tr>
			</table>
			</fieldset>
		</td>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #FFFDE9;'>
			<table border=0 width="100%">
			<tr>
				<td class="littletableheaderc"><?php echo $form->sucur->label; ?></td>
				<td class="littletablerow"    ><?php echo $form->sucur->output;?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->depto->label; ?> </td>
				<td class="littletablerow"    ><?php echo $form->depto->output;?> </td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->numcuent->label; ?></td>
				<td class="littletablerow"    ><?php echo $form->numcuent->output;?></td>
			</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
<table  width="100%" border='0'>
	<tr>
		<td valign='top'>
			<fieldset style='border: 1px outset #9AC8DA;background: #E0ECF8;'>
			<table width= "100%" >
				<tr>
					<td width='60px' class="littletableheaderc"><?php echo $form->nombre->label; ?></td>
					<td              class="littletablerow"    ><?php echo $form->nombre->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->telefono->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->telefono->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->dire1->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->dire1->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->dire2->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->dire2->output;?></td>
				</tr>
			</table>
			</fieldset>
		</td>


		<td  valign="top">
			<fieldset style='border: 1px outset #9AC8DA;background: #E0ECF8;'>
			<table style="height: 100%;width: 100%">
				<tr>
					<td  width="95" class="littletableheaderc"><?php echo $form->moneda->label; ?></td>
					<td             class="littletablerow"    ><?php echo $form->moneda->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->tipocta->label; ?> </td>
					<td class="littletablerow"    ><?php echo $form->tipocta->output;?> </td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->proxch->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->proxch->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->dbporcen->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->dbporcen->output;?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
<table  width="100%" border='0'>
	<tr>
		<td>
			<fieldset style='border: 1px outset #8A0808;background: #FFFBE9;'>
			<table width= '100%' >
				<tr>
					<td class="littletableheaderc"><?php echo $form->codprv->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->codprv->output;?>
					<?php
					if(!empty($form->codprv->value)){
						$mSQL = 'SELECT nombre FROM sprv WHERE proveed='.$this->db->escape(trim($form->codprv->value));
						echo $this->datasis->dameval($mSQL);
					}
					?>
					</td>
				</tr>

				<tr>
					<td class="littletableheaderc"><?php echo $form->gastocom->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->gastocom->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->gastoidb->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->gastoidb->output;?></td>
				</tr>
				<tr>
				<td class="littletableheaderc"><?php echo $form->cuenta->label ?></td>
				<td  class="littletablerow"   ><?php echo $form->cuenta->output;?>
				<?php
					if(!empty($form->cuenta->value)){
						$dbcuenta=$this->db->escape(trim($form->cuenta->value));
						$mSQL = "SELECT descrip FROM cpla WHERE codigo=${dbcuenta}";
						echo $this->datasis->dameval($mSQL);
					}
				?>
				</td>
			</tr>
			</table>
			</fieldset>
		</td>
		<?php if( $form->_status == 'show') { ?>
		<td valign='top'>
			<table width= '100%' >
				<tr>
					<td>
						<table width= '100%' >
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td align='center' style='font-size:14;font-weight: bold'>SALDO ACTUAL</td>
							</tr>
						</table>
					<td>
				</tr>
				<tr>
					<td>
					<?php if($form->saldo->value >= 0 ) { ?>
					<fieldset style='border: 6px outset #407E13;background: #0B610B;'>
					<?php } else { ?>
					<fieldset style='border: 6px outset #8A0808;background: #B40404;'>
					<?php } ?>
					<table width= '100%' >
						<tr>
							<td align='center' style='font-size:18;font-weight: bold;color:#FFFFFF'><? echo nformat($form->saldo->value); ?></td>
						</tr>
					</table>
					</fieldset>
					</td>
				</tr>
			</table>
		</td>
		<?php } ?>
	</tr>
</table>
<?php echo $container_bl.$container_br; ?>
<?php echo $form_end?>
