<?php echo $form_scripts; ?>
<?php echo $form_begin;   ?>
<?php
if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>

<table style="width:100%;border-collapse:collapse;padding:0px;">
	<tr>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #FFFDFF;'>
			<table style="width:100%;border-collapse:collapse;padding:0px;">
			<tr>
				<td             class="littletableheaderc"><?php echo $form->proveed->label;  ?></td>
				<td width='130' class="littletablerow"    ><?php echo $form->proveed->output; ?></td>
				<td             class="littletableheaderc"><?php echo $form->rif->label;  ?></td>
				<td             class="littletablerow"    ><?php echo $form->rif->output.'<a href="javascript:consulrif(\'rifci\');" title="Consultar RIF en el SENIAT">'.image('system-search.png','Consultar RIF en el SENIAT',array("border"=>"0")).'</a>'; ?></td>
			</tr><tr>
				<td             class="littletableheaderc"><?php echo $form->nombre->label;  ?></td>
				<td colspan='3' class="littletablerow"    ><?php echo $form->nombre->output; ?></td>
			</tr><tr>
				<td             class="littletableheaderc"><?php echo $form->contacto->label;  ?></td>
				<td colspan='3' class="littletablerow"    ><?php echo $form->contacto->output; ?></td>
			</tr><tr>
				<td             class="littletablerow"><?php echo $form->nomfis->label; ?></td>
				<td colspan='3' class="littletablerow"><?php echo $form->nomfis->output ?></td>
			</tr>
			</table>
			</fieldset>
		</td>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #FFFDFF;'>
			<table style="width:100%;border-collapse:collapse;padding:0px;">
			<tr>
				<td class="littletableheaderc"><?php echo $form->grupo->label;   ?></td>
				<td class="littletablerow"    ><?php echo $form->grupo->output;  ?></td>
			</tr><tr>
				<td class="littletableheaderc"><?php echo $form->tipo->label;    ?></td>
				<td class="littletablerow"    ><?php echo $form->tipo->output;   ?></td>
			</tr><tr>
				<td class="littletableheaderc"><?php echo $form->tiva->label;    ?></td>
				<td class="littletablerow"    ><?php echo $form->tiva->output;   ?></td>
			</tr><tr>
				<td class="littletableheaderc"><?php echo $form->url->label;     ?></td>
				<td class="littletablerow"    ><?php echo $form->url->output.'<a href="javascript:iraurl();" title="Abrir sitio Web">'.image('system-search.png','Abrir sitio Web',array("border"=>"0")).'</a>';    ?></td>
			<tr><tr>
				<td class="littletableheaderc"><?php echo $form->codigo->label;  ?></td>
				<td class="littletablerow"    ><?php echo $form->codigo->output; ?></td>
			</tr>
			</table>
			</fieldset>
		</td>
	</tr>
<!--/table>

<table style="width:100%;border-collapse:collapse;padding:0px;"-->
	<tr>
		<td valign='top'>
			<fieldset style='border: 1px outset #9AC8DA;background: #F8FCFF;'>
			<!--legend class="titulofieldset" style='color: #114411;'>Direcci&oacute;n</legend-->
			<table style="width:100%;border-collapse:collapse;padding:0px;">
				<tr>
					<td class="littletablerow"><?php echo $form->direc1->label; ?>&nbsp;</td>
					<td class="littletablerow"><?php echo $form->direc1->output; ?>&nbsp;</td>
				<tr></tr>
					<td class="littletablerow"><?php echo $form->estado->label; ?>&nbsp;</td>
					<td class="littletablerow"><?php echo $form->estado->output; ?>&nbsp;</td>
				</tr>
			</table>
			</fieldset>
		</td>
		<td valign='top'>
			<fieldset style='border: 1px outset #9AC8DA;background: #F8FCFF;'>
			<!--legend class="titulofieldset" style='color: #114411;'>Contacto</legend-->
			<table style="width:100%;border-collapse:collapse;padding:0px;">
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
<!--/table>
<table style="width:100%;border-collapse:collapse;padding:0px;" -->
	<tr>
		<td>
			<fieldset style='border: 1px outset #8A0808;background: #FFFBFA;'>
			<table style="width:100%;border-collapse:collapse;padding:0px;" >
				<tr>
					<td class="littletableheaderc">Banco 1</td>
					<td class="littletablerow"><?php echo $form->banco1->output;  ?></td>
					<td class="littletablerow"><?php echo $form->cuenta1->output; ?></td>
				</tr><tr>
					<td class="littletableheaderc">Banco 2</td>
					<td class="littletablerow"><?php echo $form->banco2->output;  ?></td>
					<td class="littletablerow"><?php echo $form->cuenta2->output; ?></td>
				</tr><tr>
					<td class="littletablerow" colspan='2'><?php echo $form->prefpago->label;  ?></td>
					<td class="littletablerow"><?php echo $form->prefpago->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td><td>
			<fieldset style='border: 1px outset #8A0808;background: #FFFBFA;'>
			<table style="width:100%;border-collapse:collapse;padding:0px;" >
				<tr>
					<td class="littletableheaderc"><?php echo $form->cuenta->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->cuenta->output; ?></td>
				</tr><tr>
					<td class="littletableheaderc"><?php echo $form->canticipo->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->canticipo->output;?></td>
				<tr><tr>
					<td class="littletableheaderc"><?php echo $form->reteiva->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->reteiva->output; ?></td>
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
endif;
?>
