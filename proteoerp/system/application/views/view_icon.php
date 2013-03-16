<?php

$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;

if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
$(function(){
		


});
</script>
<?php } ?>

<?php echo $form->tipo->output; ?>

<fieldset  style='border: 1px outset #FEB404; background: #EDDA4E;'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr>
	<?php if ($form->tipo->value == 'I') { //Ingreso ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CONCEPTOS DE INGRESOS</td>
	<?php } else { ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CONCEPTOS DE EGRESOS</td>
	<?php } ?>
	</tr>
</table>
</fieldset>
<br>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->codigo->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->codigo->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->concepto->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->concepto->output; ?></td>
	</tr>
</table>
</fieldset>
<br>

<?php if ($form->tipo->value == 'E') { //Prestamo Otorgado ?>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->gasto->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->gasto->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->gastode->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->gastode->output; ?></td>
	</tr>
</table>
</fieldset>
<?php } else { ?>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->ingreso->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->ingreso->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->ingresod->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->ingresod->output; ?></td>
	</tr>
</table>
</fieldset>
<?php } ?>

<table align='center' width="95%">
	<tr>
		<td>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		</td>
	</tr>
</table>
<?php endif; ?>
