<?php
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
if($form->_status <> 'show'){ ?>

<script language="javascript" type="text/javascript">
$(function() {
	$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
});
</script>
<?php } ?>

<?php echo $form->tipo->output; ?>

<fieldset  style='border: 1px outset #FEB404; background: #EDDA4E;'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr>
<?php if ($form->tipo->value == 'C-P') {  ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE CLIENTE -> PROVEEDOR</td>
<?php } elseif ($form->tipo->value == 'C-C') { //Prestamo Otorgado ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE CLIENTE -> CLIENTE</td>
<?php } elseif ($form->tipo->value == 'P-P') { ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE PROVEEDOR -> PROVEEDOR</td>
<?php } elseif ($form->tipo->value == 'P-C') { //Prestamo Otorgado ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE PROVEEDOR -> CLIENTE</td>
<?php } ?>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->numero->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->numero->output; ?></td>
		<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
		<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
	</tr>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<label class="littletablerowth" ><?php echo $form->proveed->label;  ?></label>
<table width='100%'>
	<tr>
		<td class="littletablerow"  ><?php echo $form->proveed->output; ?></td>
		<td class="littletablerow"  ><?php echo $form->nombre->output; ?></td>
		<td class="littletablerowth"><?php echo $form->saldoa->label;  ?></td>
		<td class="littletablerow" colspan='2'><?php echo $form->saldoa->output; ?></td>
	</tr>
</table>
</fieldset>

<div align='center' style='border: 3px outset #EFEFEF;background: #EFEFFF '>
<div id='grid1_container' style='width:400px;height:150px'></div>
</div>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<label class="littletablerowth" align='left'><?php echo $form->cliente->label;  ?></label>
<table width='100%'>
	<tr>
		<td class="littletablerow"  ><?php echo $form->cliente->output; ?></td>
		<td class="littletablerow"  ><?php echo $form->nomcli->output; ?></td>
		<td class="littletablerowth"><?php echo $form->saldod->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->saldod->output; ?></td>
	</tr>
</table>
</fieldset>


</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr>
		<td class="littletablerowth"><?php echo $form->concept1->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->concept1->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->concept2->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->concept2->output; ?></td>
	</tr>
</table>
</fieldset>
<?php echo $form_end; ?>
