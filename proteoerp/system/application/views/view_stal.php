
<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_tl=join("&nbsp;", $form->_button_container["TL"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:


if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>
<script language="javascript" type="text/javascript">

</script>
<?php } ?>
<table align='center' width="95%">
	<tr>
		<td align=left ><?php echo $container_tl?></td>
		<td align=right><?php echo $container_tr?></td>
	</tr>
</table>
<table align='center' width="95%">
	<tr>
		<td>
		<table width='100%'><tr><td>
			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
			<legend class="titulofieldset" style='color: #114411;'>Datos de Producto</legend>
			<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader" ><?php echo $form->serial->label  ?>&nbsp;</td>
				<td class="littletablerow"    ><?php echo $form->serial->output ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"  ><?php echo $form->codigo->label;   ?>&nbsp;</td>
				<td class="littletablerow"     ><?php echo $form->codigo->output;  ?>&nbsp;</td>
				<td class="littletableheader"  ><?php echo $form->descrip->label     ?>&nbsp;</td>
				<td class="littletablerow"     ><?php echo $form->descrip->output    ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->marca->label;   ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->marca->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->modelo->label   ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->modelo->output  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->valor->label    ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->valor->output   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader" ><?php echo $form->ubica->label;   ?>&nbsp;</td>
				<td class="littletablerow"    ><?php echo $form->ubica->output;  ?>&nbsp;</td>
			</tr>
			</table>
			</fieldset>
		</td>
		</tr></table>
		</td>
	</tr>
	
	<tr>
		<td>
		<table width='100%'><tr><td>
			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
			<legend class="titulofieldset" style='color: #114411;'>Datos de Taller</legend>
			<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->numero->label   ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->numero->output  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->fecha->label    ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->fecha->output   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"          ><?php echo $form->falla->label;    ?>&nbsp;</td>
				<td class="littletablerow"    colspan=3><?php echo $form->falla->output;   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"           ><?php echo $form->observa->label   ?>&nbsp;</td>
				<td class="littletablerow"    colspan=3 ><?php echo $form->observa->output  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"           ><?php echo $form->receptor->label;   ?>&nbsp;</td>
				<td class="littletablerow"              ><?php echo $form->receptor->output.$form->nom_rec->output;  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"           ><?php echo $form->tecnico->label;   ?>&nbsp;</td>
				<td class="littletablerow"    colspan=3 ><?php echo $form->tecnico->output.$form->nom_tec->output;  ?>&nbsp;</td>
			</tr>
			</table>
			</fieldset>
		</td>
		</tr></table>
		</td>
	</tr>
	<tr>
		<td>
		<table width='100%'><tr><td>
			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
			<legend class="titulofieldset" style='color: #114411;'>Datos de Venta</legend>
			<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->factura->label;   ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->factura->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->fecha_fac->label  ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->fecha_fac->output ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"            ><?php echo $form->cod_cli->label;                         ?>&nbsp;</td>
				<td class="littletablerow"    colspan=3  ><?php echo $form->cod_cli->output.$form->nombre->output;  ?>&nbsp;</td>
			</tr>
			</table>
			</fieldset>
		</td>
		</tr></table>
		</td>
	</tr>
	<!--
	<tr>
		<td>
		<table width='100%'><tr><td>
			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
			<legend class="titulofieldset" style='color: #114411;'>Otros Datos</legend>
			<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->salida->label;   ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->salida->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->estatus->label     ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->estatus->output    ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->tipo->label;   ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->tipo->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->horas->label  ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->horas->output ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->codnue->label;   ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->codnue->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->serinu->label  ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->serinu->output ?>&nbsp;</td>
			</tr>
			</table>
			</fieldset>
		</td>
		</tr></table>
		</td>
	</tr>
	-->
	<tr>
		<td>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		</td>
	</tr>
</table>
<?php endif; ?>
