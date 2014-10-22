<?php
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
if($form->_status <> 'show'){ ?>

<script language="javascript" type="text/javascript">
	$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
</script>
<?php } ?>
<?php echo $form->tipo->output; ?>

<fieldset  style='border: 1px outset #FEB404; background: #EDDA4E;'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr>
<?php if ($form->tipo->insertValue == 'DE') {  ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>DEPOSITO DE TARJETAS</td>
<?php } elseif ($form->tipo->insertValue == 'TR') { //Prestamo Otorgado ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>TRANSFERENCIAS ENTRE CUENTAS</td>
<?php } elseif ($form->tipo->insertValue == 'RM') { ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>REMESAS</td>
<?php } ?>
	</tr>
</table>
</fieldset>

<?php if ($form->tipo->insertValue == 'TR') {  ?>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->envia->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->envia->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->tipoe->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tipoe->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->numeroe->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->numeroe->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->recibe->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->recibe->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->concepto->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->concepto->output; ?></td>
	</tr>
</table>
</fieldset>
<?php } elseif ($form->tipo->insertValue == 'DE') {?>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%' border='0'>
	<tr>
		<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->envia->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->envia->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->recibe->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->recibe->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->tipor->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tipor->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->numeror->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->numeror->output; ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%' border='0'>
	<tr>
		<td class="littletablerowth"><?php echo $form->tarjeta->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tarjeta->output; ?></td>
		<td class="littletablerowth"><?php echo $form->comision->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->comision->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->tdebito->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tdebito->output; ?></td>
		<td class="littletablerowth"><?php echo $form->islr->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->islr->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
	</tr>
</table>
</fieldset>
<?php } ?>
<?php echo $form_end; ?>
