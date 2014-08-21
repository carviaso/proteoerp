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
		<td class="littletablerowth"><?php echo $form->numero->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->numero->output; ?></td>
		<td class="littletablerowth"><?php echo $form->status->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->status->output; ?></td>
		<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->inicio->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->inicio->output; ?></td>
		<td class="littletablerowth"><?php echo $form->final->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->final->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->cliente->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->cliente->output; ?></td>
		<td class="littletablerow" colspan='4'  ><div id='nombre' style='font-weight:bold;'></div></td>
	</tr>
</table>
</fieldset>


<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr style='font-size:12px;color:#0B3B0B;background-color: #F7BE81;'>
		<th><?php echo $form->codigo->label;   ?></th>
		<th><?php echo $form->descrip->label;  ?></th>
		<th><?php echo $form->cantidad->label; ?></th>
		<th><?php echo $form->base->label;     ?></th>
	</tr>
	<tr>
		<td class="littletablerow" valign='top' ><?php echo $form->codigo->output; ?></td>
		<td class="littletablerow" valign='top' ><?php echo $form->descrip->output; ?></td>
		<td class="littletablerow" valign='top' ><?php echo $form->cantidad->output; ?></td>
		<td class="littletablerow" valign='top' ><?php echo $form->base->output; ?></td>
	</tr>
	<tr>
		<td colspan='2' class="littletablerow"  >&nbsp;</td>
		<td class="littletablerowth"><?php echo $form->iva->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->iva->output; ?></td>
	</tr>
	<tr>
		<td colspan='2' class="littletablerow"  >&nbsp;</td>
		<td class="littletablerowth"><?php echo $form->precio->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->precio->output; ?></td>
	</tr>
</table>
</fieldset>
<?php echo $form_end; ?>
