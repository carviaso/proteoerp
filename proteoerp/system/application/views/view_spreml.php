<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);


echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
if($form->_status <> 'show'){ ?>

<?php } 

//echo $container_tr;
?>

<table style='border: 1px solid #0B0B61;border-collapse:collapse;padding:0px;width:100%;'>
	<tr><th colspan='4' style='background: #0B0B61;color:white;' align='left'>SU PEDIDO</th></tr>
	<tr>
		<td class="littletablerowth" width='92px'><?php echo $form->numero->label;   ?></td>
		<td class="littletablerow"                ><?php echo $form->numero->output;  ?></td>
		<td class="littletablerowth" width='140px'><?php echo $form->mercalib->label; ?></td>
		<td class="littletablerow"                ><?php echo $form->mercalib->output;?></td>
	</tr>
</table>
	<div id="tab1" style='background:#EFEFFF'>
		<table style='border: 1px solid #0B0B61;border-collapse:collapse;padding:0px;width:100%;'>
			<tr><th colspan='4' style='background: #0B0B61;color:white;' align='left'>DATOS DEL COMPRADOR</th></tr>
			<tr>
				<td colspan='2'>
				<table style="border-collapse:collapse;padding:0px;"><tr>
					<td class="littletablerowth" width='92px'><?php echo $form->rifci->label;  ?></td>
					<td class="littletablerow"                ><?php echo $form->rifci->output; ?></td>
					<td class="littletablerowth"              ><?php echo $form->nombre->label; ?></td>
					<td class="littletablerow"                ><?php echo $form->nombre->output;?></td>
				</tr></table>
				</td>
			</tr>
			<tr>
				<td class="littletablerowth" width='92px' ><?php echo $form->direccion->label;  ?></td>
				<td class="littletablerow"                 ><?php echo $form->direccion->output; ?></td>
			</tr>
			<tr>
				<td colspan='2'>
				<table style="border-collapse:collapse;padding:0px;">
				<tr>
					<td class="littletablerowth" width='92px'><?php echo $form->estado->label;   ?></td>
					<td class="littletablerow"                ><?php echo $form->estado->output;  ?></td>
					<td class="littletablerowth" width='95px' ><?php echo $form->telefono->label; ?></td>
					<td class="littletablerow"                ><?php echo $form->telefono->output;?></td>
				</tr>
				<tr>
					<td class="littletablerowth"><?php echo $form->ciudad->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->ciudad->output; ?></td>
					<td class="littletablerowth"><?php echo $form->email->label;   ?></td>
					<td class="littletablerow"  ><?php echo $form->email->output;  ?></td>
				</tr>
				</table>
				</td>
			</tr>
		</table>
	</div>
	<div id="tab2" style='background:#EFEFF4;'>
		<table style='border: 1px solid #0B0B61;border-collapse:collapse;padding:0px;width:100%;'>
			<tr>
				<th colspan='4' style='background: #0B0B61;color:white;' align='left'>DATOS PARA EL ENVIO</th>
			</tr>
			<tr>
				<td class="littletablerowth" width='92px'><?php echo $form->envdirec->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->envdirec->output; ?></td>
			</tr>
			<tr>
				<td colspan='2'>
				<table style="border-collapse:collapse;padding:0px;width:100%;">
				<tr>
					<td class="littletablerowth" width='92px' ><?php echo $form->envestado->label;   ?></td>
					<td class="littletablerow"                ><?php echo $form->envestado->output;  ?></td>
					<td class="littletablerowth"><?php echo $form->envciudad->label;  ?></td>
					<td class="littletablerow"  ><?php echo $form->envciudad->output; ?></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td colspan='2' align='center'>
				<table style="border-collapse:collapse;padding:0px;WIDTH:95%;background: #E0F2F7;border: 1px solid #0080FF;"><tr>
				<tr>
					<th colspan='4' style='background: #0080FF;color:white;' align='left'>SI EL QUE RECIBE ES OTRA PERSONA</th>
				</tr>
					<td class="littletablerowth" width='92px'><?php echo $form->envrifci->label;  ?></td>
					<td class="littletablerow"                ><?php echo $form->envrifci->output; ?></td>
					<td class="littletablerowth"              ><?php echo $form->envnombre->label;  ?></td>
					<td class="littletablerow"                ><?php echo $form->envnombre->output; ?></td>
				<tr>
					<td class="littletablerowth"><?php echo $form->envtelef->label; ?></td>
					<td class="littletablerow"  colspan='2' ><?php echo $form->envtelef->output;?></td>
					<td class="littletablerow"  >&nbsp;</td>
				</tr>

				</tr></table>
				</td>
			</tr>
		</table>
	</div>
	<div id="tab3" style='background:#EFEFF4;'>
		<table style='border: 1px solid #0B0B61;border-collapse:collapse;padding:0px;width:100%;'>
			<tr><th colspan='4' style='background: #0B0B61;color:white;' align='left'>FORMA DE PAGO</th></tr>
			<tr>
				<td class="littletablerowth" width='92px' ><?php echo $form->tipo_op->label;  ?></td>
				<td class="littletablerow"                ><?php echo $form->tipo_op->output; ?></td>
				<td class="littletablerowth"              ><?php echo $form->codbanc->label;  ?></td>
				<td class="littletablerow"                ><?php echo $form->codbanc->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->fechadep->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->fechadep->output; ?></td>
				<td class="littletablerowth"><?php echo $form->num_ref->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->num_ref->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->totalg->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->totalg->output; ?></td>
				<td class="littletablerowth">&nbsp;</td>
				<td class="littletablerow"  >&nbsp;</td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa->label;  ?></td>
				<td class="littletablerow" colspan='3' ><?php echo $form->observa->output; ?></td>
			</tr>
		</table>
	</div>

<input name="btn_submit" value="Guardar" onclick="" class="button" type="submit">
&nbsp;
<input name="btn_undo" value="Cancelar" onclick="javascript:window.location='/proteoerp/ventas/spreml/'" class="button" type="button">


<?php echo $form_end; ?>
