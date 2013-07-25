<?php
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
if($form->_status <> 'show'){ ?>

<script language="javascript" type="text/javascript">
</script>
<?php } ?>

<fieldset  style='border: 1px outset #FEB404;background: #FCFFFC;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->concepto->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->concepto->output; ?></td>
		<td class="littletablerowth"><?php echo $form->tipo->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tipo->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->descrip->label;  ?></td>
		<td class="littletablerow" ><?php echo $form->descrip->output; ?></td>
		<td class="littletablerowth"><?php echo $form->grupo->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->grupo->output; ?></td>
		<!--td class="littletablerowth"><?php echo $form->aplica->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->aplica->output; ?></td-->
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->encab1->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->encab1->output; ?></td>
		<td class="littletablerowth"><?php echo $form->encab2->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->encab2->output; ?></td>
	</tr>
</table>
</fieldset>


<fieldset  style='border: 1px outset #FEB404;background: #FCFFFC;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->formula->label;  ?></td>
	</tr>
	<tr>
		<td class="littletablerow" ><?php echo $form->formula->output; ?></td>
	</tr>
</table>
</fieldset>


<fieldset  style='border: 1px outset #FEB404;background: #FCFFFC;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->tipod->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tipod->output; ?></td>
		<td class="littletablerowth"><?php echo $form->ctade->label;  ?></td>
		<td class="littletablerow" id="td_ctade" ><?php echo $form->ctade->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->tipoa->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tipoa->output; ?></td>
		<td class="littletablerowth"><?php echo $form->ctaac->label;  ?></td>
		<td class="littletablerow" id="td_ctaac" ><?php echo $form->ctaac->output; ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FCFFFC;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->liquida->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->liquida->output; ?></td>
		<td class="littletablerowth"><?php echo $form->psueldo->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->psueldo->output; ?></td>
		<td class="littletablerowth"><?php echo $form->dias->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->dias->output; ?></td>
	</tr>
</table>
</fieldset>

<?php echo $form_end; ?>
