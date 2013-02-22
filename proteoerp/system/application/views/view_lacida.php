<?php
echo $form_scripts;
echo $form_begin;

//$mod=true;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

if($form->_status!='show'){?>

<script language="javascript" type="text/javascript">
</script>
<?php } ?>

<fieldset  style='border: 1px outset #1F1F1F;background: #D3DCFF;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth">Numero:</td>
		<td class="littletablerow"  ><?php echo str_pad($form->id->output,7,'0',STR_PAD_LEFT); ?></td>
		<td class="littletablerowth"><?php echo $form->fecha->label; ?></td>
		<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
	<tr>
</table>
</fieldset>
<br>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->ruta->label; ?></td>
		<td class="littletablerow" colspan='2'  ><?php echo $form->ruta->output; ?></td>
	<tr>
	<tr>
		<td class="littletablerowth">Vaquera:</td>
		<td class="littletablerow"  ><?php echo $form->vaquera->output; ?></td>
		<td class="littletablerow"  ><?php echo $form->nomvaca->output; ?></td>
	<tr>
</table>
</fieldset>
<br>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	</tr>
		<td class="littletablerowth"><?php echo $form->codigo->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->codigo->output; ?></td>
		<td class="littletablerowth"><?php echo $form->precio->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->precio->output; ?></td>
	</tr>
	</tr>
		<td class="littletablerowth"><?php echo $form->descrip->label;  ?></td>
		<td colspan="3" class="littletablerow"  ><?php echo $form->descrip->output; ?></td>
	</tr>
</table>
</fieldset>
<br>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	</tr>
		<td class="littletablerowth"><?php echo $form->litros->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->litros->output; ?></td>
		<td class="littletablerowth"><?php echo $form->acidez->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->acidez->output; ?></td>
		<td class="littletablerowth"><?php echo $form->alcohol->label; ?></td>
		<td class="littletablerow"  ><?php echo $form->alcohol->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->promedio->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->promedio->output; ?></td>
		<td class="littletablerowth"><?php echo $form->gadm->label;      ?></td>
		<td class="littletablerow"  ><?php echo $form->gadm->output;     ?></td>
		<td class="littletablerowth"><?php echo $form->pleche->label;    ?></td>
		<td class="littletablerow"  ><?php echo $form->pleche->output;   ?></td>
	</tr>
</table>
</fieldset>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->precioref->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->precioref->output; ?></td>
		<td class="littletablerowth"><?php echo $form->descuento->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->descuento->output; ?></td>
	</tr>
</table>
</fieldset>



<?php echo $form_end; ?>
