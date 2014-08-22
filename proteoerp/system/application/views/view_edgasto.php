<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
if($form->_status <> 'show'){ ?>

<script language="javascript" type="text/javascript">
</script>
<?php } ?>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->aplicacion->label;  ?></td>
		<td class="littletablerow" colspan='4' ><?php echo $form->aplicacion->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->tipo_doc->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tipo_doc->output; ?></td>
		<td class="littletablerowth"><?php echo $form->numero->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->numero->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
		<td class="littletablerowth"><?php echo $form->causado->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->causado->output; ?></td>
	</tr>
</table>
</fieldset>

<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->proveed->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->proveed->output; ?></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->rif->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->rif->output; ?></td>
		<td class="littletablerow"  ><?php echo $form->proveedor->output; ?></td>
	</tr>
</table>
<br>



<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->partida->label;  ?></td>
		<td class="littletablerow" colspan='6' ><?php echo $form->partida->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->detalle->label;  ?></td>
		<td class="littletablerow" colspan='6' ><?php echo $form->detalle->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->base->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->base->output; ?></td>
		<td class="littletablerowth"><?php echo $form->iva->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->iva->output; ?></td>
		<td class="littletablerowth"><?php echo $form->total->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->total->output; ?></td>
	</tr>
</table>
</fieldset>



<?php echo $form_end; ?>
