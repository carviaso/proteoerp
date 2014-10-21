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

<fieldset  style='border: 1px outset #FEB404;background: #F6E688;'>
<legend>Orden</legend>
<table style='border-collapse:collapse;padding:0px;width:100%;'>
	<tr>
		<td class="littletablerowth"><?php echo $form->status->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->status->output; ?></td>
		<td class="littletablerowth"><?php echo $form->fecha->label;     ?></td>
		<td class="littletablerow"  ><?php echo $form->fecha->output;    ?></td>
		<td class="littletablerowth"><?php echo $form->numero->label;    ?></td>
		<td class="littletablerow"  ><?php echo $form->numero->output;   ?></td>
		<td class="littletablerowth"><?php echo $form->mercalib->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->mercalib->output; ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #E0FCF4;'>
<legend>Datos del Comprador</legend>
<table style='border-collapse:collapse;padding:0px;width:100%;'>
	<tr>
		<td class="littletablerowth"><?php echo $form->rifci->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->rifci->output; ?></td>
		<td class="littletablerowth"><?php echo $form->nombre->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->nombre->output; ?></td>
		<td class="littletablerowth"><?php echo $form->telefono->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->telefono->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"             rowspan='2'><?php echo $form->direccion->label;  ?></td>
		<td class="littletablerow"   colspan='3' rowspan='2'  ><?php echo $form->direccion->output; ?></td>
		<td class="littletablerowth"><?php echo $form->estado->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->estado->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->ciudad->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->ciudad->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->email->label;  ?></td>
		<td class="littletablerow"  colspan='3'><?php echo $form->email->output; ?></td>
	<tr>
</table>
</fieldset>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<legend>Datos de Envio</legend>
<table style='border-collapse:collapse;padding:0px;width:100%;'>
	<tr>
		<td class="littletablerowth" rowspan='2'><?php echo $form->envdirec->label;  ?></td>
		<td class="littletablerow"   rowspan='2'><?php echo $form->envdirec->output; ?></td>
		<td class="littletablerowth"><?php echo $form->envestado->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->envestado->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->envciudad->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->envciudad->output; ?></td>
	</tr>
</table>
</fieldset>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table style='border-collapse:collapse;padding:0px;width:100%;'>
	<tr>
		<td class="littletablerowth"><?php echo $form->envrifci->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->envrifci->output; ?></td>
		<td class="littletablerowth"><?php echo $form->envnombre->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->envnombre->output; ?></td>
		<td class="littletablerowth"><?php echo $form->envtelef->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->envtelef->output; ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #C7F688;'>
<legend>Forma de Pago y Envio</legend>
<table style='border-collapse:collapse;padding:0px;width:100%;'>
	<tr>
		<td class="littletablerowth"><?php echo $form->codbanc->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->codbanc->output; ?></td>
		<td class="littletablerowth"><?php echo $form->tipo_op->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tipo_op->output; ?></td>
		<td class="littletablerowth"><?php echo $form->fechadep->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->fechadep->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->num_ref->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->num_ref->output; ?></td>
		<td class="littletablerowth"><?php echo $form->totalg->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->totalg->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"           rowspan='2'><?php echo $form->observa->label;  ?></td>
		<td class="littletablerow" colspan='3' rowspan='2'><?php echo $form->observa->output; ?></td>
		<td class="littletablerowth"><?php echo $form->guia->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->guia->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->fechaenv->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->fechaenv->output; ?></td>
	</tr>
</table>
</fieldset>
<?php echo $form_end; ?>
