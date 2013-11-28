<?php
$container_tr=join('&nbsp;', $form->_button_container['TR']);
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	$meco = $form->output;
	$meco = str_replace('class="tablerow"','class="tablerow" style="font-size:20px; align:center;" ',$meco);
	echo $meco."</td><td align='center'>".img("images/borrar.jpg");
else:
	echo $script; 
	echo $form_scripts;
	echo "\n<div id='diveditor' style='font-size:'10px;'>\n"; 
	echo $form_begin; 
?>
<style>
#maintabcontainer ul { font-size: 9px; }
</style>

<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<fieldset style='border: 1px outset #9AC8DA;background: #F6F8FF;'>
<table border='0' width="100%"  >
	<tr>
		<td>
			<table border='0' width="100%" cellspacing='0' cellpadding='0' >
				<tr>
					<td width="100" class="littletableheaderc"><?php echo $form->cliente->label  ?></td>
					<td width="100" class="littletablerow" ><?php    echo $form->cliente->output ?></td>
					<td class="littletableheaderc"><?php             echo $form->rifci->label    ?></td>
					<td class="littletablerow"><?php                 echo $form->rifci->output   ?></td>
					<td class="littletablerow" align="right"><?php   echo $form->tiva->output    ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->nombre->label ?></td>
					<td colspan='4' class="littletablerow"><?php echo $form->nombre->output?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->nomfis->label ?></td>
					<td colspan='4' class="littletablerow"><?php echo $form->nomfis->output ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->contacto->label  ?></td>
					<td colspan='4' class="littletablerow"><?php echo $form->contacto->output ?></td>
				</tr>
			</table>
		</td>
		<td valign='top' width='25%'>
			<table border=0 width="100%"  cellspacing='0' cellpadding='0'>
				<tr>
					<td class="littletableheaderc"> <?php echo $form->tipo->label ?></td>
					<td class="littletablerow"><?php     echo $form->tipo->output ?></td>
				</tr><tr>
					<td colspan='2'>
					<fieldset style='border: 1px dotted #8AF8F8;background: #FAFAFF;'>
						<table width= '100%'  cellspacing='0' cellpadding='0'>
							<tr>
								<td colspan="2" class="littletableheaderc">
									<?php echo $form->entidad->label;  ?>
									<?php echo $form->entidad->output;  ?>

								</td>
							</tr><tr>
								<td colspan="2" class="littletableheaderc">
									<?php echo $form->zona->label  ?>
									<?php echo $form->zona->output ?></td>
							</tr>
						</table>
					</fieldset>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width='100%' style='border: 1px dotted #8AF8F8;background: #FFFBE9;'>
	<tr>
		<td class="littletableheaderc"><?php echo $form->grupo->label ?></td>
		<td class="littletablerow"><?php echo $form->grupo->output?></td>
		<td class="littletableheaderc"><?php echo $form->socio->label ?></td>
		<td class="littletablerow"><?php echo $form->socio->output ?></td>
<?php if (!empty($form->socio->value)) { ?>
		<td class="littletablerow"><?php echo $this->datasis->dameval("SELECT nombre FROM scli WHERE cliente='".$form->socio->value."'") ?></td>
<?php }; ?>
	</tr>
</table>
</fieldset>

<div id="maintabcontainer">
	<ul>
		<li><a href="#tab1">Direcciones</a></li>
		<li><a href="#tab2">Valores</a></li>
		<li><a href="#tab3">Anexo</a></li>
	</ul>
	<div id="tab1" style='background:#EEFFFF'>
	<table border='0' width="100%">
	<tr>
		<td valign='top' width='50%'>
			<table border='0' width='100%'  cellspacing='0' cellpadding='0'>
				<tr>
					<td colspan='2' class="littletableheaderc">Direcci&oacute;n de Oficina</td>
				</tr><tr>
					<td colspan='2' class="littletablerow"><?php echo $form->dire11->output ?>&nbsp;</td>
				</tr><tr>
					<td colspan='2' class="littletablerow"><?php echo $form->dire12->output ?>&nbsp;</td>
				</tr><tr>
					<td class="littletableheaderc"><?php echo $form->ciudad1->label   ?></td>
					<td class="littletablerow" ><?php    echo $form->ciudad1->output  ?>&nbsp;</td>
				</tr>
			</table>
		</td>
		<td valign='top' width='50%'>
			<table border='0'  width='100%' cellspacing='0' cellpadding='0'>
				<tr>
					<td colspan='2' class="littletableheaderc">Direcci&oacute;n de Env&iacute;o</td>
				</tr><tr>
					<td colspan='2' class="littletablerow"><?php echo $form->dire21->output ?>&nbsp;</td>
				</tr><tr>
					<td colspan='2' class="littletablerow"><?php echo $form->dire22->output ?>&nbsp;</td>
				</tr><tr>
					<td class="littletableheaderc"><?php echo $form->ciudad2->label ?></td>
					<td class="littletablerow" style='font-size:11;'><?php echo $form->ciudad2->output ?>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	<br />
	<table style='height: 100%;width: 100%;border: 1px dotted; cellspacing:0, cellpadding:0' >
	<tr>
		<td width="70" class="littletableheaderc"><?php echo $form->telefono->label  ?></td>
		<td class="littletablerow"    ><?php echo $form->telefono->output ?></td>
		<td class="littletableheaderc"><?php echo $form->url->label       ?></td>
		<td colspan="3" class="littletablerow" ><?php echo $form->url->output ?></td>
	</tr>
	<tr>
		<td class="littletableheaderc"><?php echo $form->telefon2->label  ?></td>
		<td class="littletablerow"    ><?php echo $form->telefon2->output ?></td>
		<td class="littletableheaderc"><?php echo $form->fb->label        ?></td>
		<td class="littletablerow"    ><?php echo $form->fb->output       ?></td>
		<td class="littletableheaderc"><?php echo $form->pin->label       ?></td>
		<td class="littletablerow"    ><?php echo $form->pin->output      ?></td>
	</tr>
	<tr>
		<td class="littletableheaderc"><?php echo $form->email->label    ?></td>
		<td class="littletablerow"    ><?php echo $form->email->output   ?></td>
		<td class="littletableheaderc"><?php echo $form->twitter->label  ?></td>
		<td class="littletablerow"    ><?php echo $form->twitter->output ?></td>
	</tr>
	</table>
	</div>
        <div id="tab2" style='background:#EEFFFF'>
		<table width="100%" border='0' >
			<tr>
				<td class="littletableheaderc">Representante Legal</td>
				<td class="littletablerow"><?php echo $form->repre->output ?></td>
				<td class="littletableheaderc">C.I.</td>
				<td class="littletablerow"><?php echo $form->cirepre->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->vendedor->label  ?></td>
				<td class="littletablerow"><?php echo $form->vendedor->output ?></td>
				<td class="littletableheaderc">Comisi&oacute;n %</td>
				<td class="littletablerow"><?php echo $form->porvend->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->cobrador->label  ?></td>
				<td class="littletablerow"><?php echo $form->cobrador->output ?></td>
				<td class="littletableheaderc">Comisi&oacute;n %</td>
				<td class="littletablerow"><?php echo $form->porcobr->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc">Cuenta Contable</td>
				<td class="littletablerow"    ><?php echo $form->cuenta->output; ?>
				<?php
				if(!empty($form->cuenta->value)){
					$dbcuenta=$this->db->escape(trim($form->cuenta->value));
					$mSQL = "SELECT descrip FROM cpla WHERE codigo=${dbcuenta} limit 1";
					echo $this->datasis->dameval($mSQL);
				}
				?>
				</td>
				<td class="littletableheaderc"><?php echo $form->sucursal->label;    ?></td>
				<td class="littletablerow"    ><?php echo $form->sucursal->output;   ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc">Cuenta Anticipo</td>
				<td class="littletablerow"    ><?php echo $form->canticipo->output; ?>
				<?php
				if(!empty($form->canticipo->value)){
					$dbcanticipo=$this->db->escape(trim($form->canticipo->value));
					$mSQL = "SELECT descrip FROM cpla WHERE codigo=${dbcanticipo} LIMIT 1";
					echo $this->datasis->dameval($mSQL);
				}
				?>
				</td>
				<td class="littletableheaderc"><?php echo $form->aniversario->label;    ?></td>
				<td class="littletablerow"    ><?php echo $form->aniversario->output;   ?></td>
			</tr>
		</table>
		<table>
			<tr>
				<td class="littletableheaderc"><?php echo $form->mmargen->label; ?></td>
				<td class="littletablerow"    ><?php echo $form->mmargen->output; ?></td>
			</tr>
		</table>

	</tr>
	</table>
	</fieldset>
        </div>
        <div id="tab3" style='background:#EEFFFF'>
			<fieldset style='border: 1px outset #8AF8F8;background:#EEFFFF;'>
			<table width= '100%' >
				<tr>
					<td class="littletableheaderc"><?php echo $form->mensaje->label;  ?></td>
					<td class="littletablerow"><?php     echo $form->mensaje->output; ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->observa->label;  ?></td>
					<td class="littletablerow"><?php     echo $form->observa->output; ?></td>
				</tr>
			</table>
			</fieldset>
			<fieldset style='border: 1px outset #FEB404; background: #EFECDA;'>
			<table width= '100%' >
			<tr>
				<td class="littletableheaderc" width='100'><?php echo $form->tarifa->label;  ?></td>
				<td class="littletablerow"><?php
					echo $form->tarifa->output.$form->tactividad->output;
					echo ' ('.$form->tminimo->output.')';
					?></td>
				<td class="littletableheaderc"><?php echo $form->upago->label;  ?></td>
				<td class="littletablerow"><?php echo $form->upago->output; ?></td>
				<td class="littletableheaderc"><?php echo $form->tarimonto->label;  ?></td>
				<td class="littletablerow"><?php echo $form->tarimonto->output; ?></td>
			</tr>
		</table>
	</div>
</div>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end?>
</div>
<?php endif; ?>
