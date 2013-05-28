<?php

$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_scripts;
echo $form_begin?>
<table align='center' width="100%" >
	<tr>
	<td>
		<table width="100%"  style="margin:0;width:100%;">
		<tr>
			<td class="littletablerowth"><?php echo $form->proveed->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->proveed->output; ?></td>
			<td class="littletablerowth"><?php echo $form->nombre->label;     ?></td>
			<td class="littletablerow"  ><?php echo $form->nombre->output;    ?></td>
		</tr><tr>
			<td class="littletablerowth"><?php echo $form->fecha->label;      ?></td>
			<td class="littletablerow"  ><?php echo $form->fecha->output;     ?></td>
			<td class="littletablerowth"><?php echo $form->numero->label;     ?></td>
			<td class="littletablerow"  ><?php echo $form->numero->output;    ?></td>
		</tr>
		</table>


		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:250px'>
		<table width='100%'>
			<tr  id='__PTPL__'>
				<td class="littletableheaderdet">C&oacute;digo</td>
				<td class="littletableheaderdet">Descripci&oacute;n</td>
				<td class="littletableheaderdet">Precio</td>
				<td class="littletableheaderdet">Impuesto</td>
				<td class="littletableheaderdet">Importe</td>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itords'];$i++){
				$it_codigo  = "codigo_${i}";
				$it_descrip = "descrip_${i}";
				$it_precio  = "precio_${i}";
				$it_iva     = "iva_${i}";
				$it_importe = "importe_${i}";
			?>

			<tr id='tr_itsnot_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output;  ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_descrip->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_precio->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_iva->output;     ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_importe->output; ?></td>
			</tr>
			<?php } ?>
			<tr id='__UTPL__'>
				<td colspan='5'></td>
			</tr>
		</table>
		</div>

<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
	  <td>
	<tr>
<table>
<?php endif; ?>
<p>&nbsp;</p>
