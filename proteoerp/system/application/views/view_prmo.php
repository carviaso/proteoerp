<?php
echo $form_scripts;
echo $form_begin;

if($form->_status <> 'show'){ ?>

<script language="javascript" type="text/javascript">
</script>
<?php } ?>

<?php echo $form->tipop->output;  ?>
<?php echo $form->numero->output; ?>

<table width='100%' celspacing='2'>
	<tr>
<?php if ($form->tipop->value == 1) { //Prestamo Otorgado ?>
		<td>
		<fieldset  style='border: 1px outset #FEB404; background: #EDDA4E;'>
		<table width='100%' cellspacing='0' cellpadding='0'>
			<tr>
				<td style='font-size:14pt;text-align:center;font-weight:bold;'>PRESTAMO OTORGADO</td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #FEB404; background: #EFECDA;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->clipro->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->clipro->output; ?></td>
				<td class="littletablerowth"><?php echo $form->nombre->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->nombre->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
				<td class="littletablerowth" align='right'><?php echo $form->vence->label;  ?></td>
				<td class="littletablerow"   align='left' ><?php echo $form->vence->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #FEB404;background: #EFECDA;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->codban->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->codban->output; ?></td>
				<td class="littletablerowth"><?php echo $form->tipo->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->tipo->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->benefi->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->benefi->output; ?></td>
				<td class="littletablerowth"><?php echo $form->numche->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->numche->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
		<table width='100%' cellspacing='0' cellpadding='0'>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa1->label;  ?></td>
				<td colspan='3' class="littletablerow"  ><?php echo $form->observa1->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa2->label;  ?></td>
				<td  colspan='3' class="littletablerow"  ><?php echo $form->observa2->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>

<?php } elseif ($form->tipop->value == 2) { // Prestamo Recibido?>
		<td>
		<fieldset  style='border: 1px outset #FEB404; background: #24D8D8;'>
		<table width='100%' cellspacing='0' cellpadding='0'>
			<tr>
				<td style='font-size:14pt;text-align:center;font-weight:bold;'>PRESTAMO RECIBIDO</td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>

		<td>
		<fieldset  style='border: 1px outset #919994; background: #D5E0D9;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->clipro->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->clipro->output; ?></td>
				<td class="littletablerowth"><?php echo $form->nombre->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->nombre->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994;background: #E3EFE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
				<td class="littletablerowth" align='right'><?php echo $form->vence->label;  ?></td>
				<td class="littletablerow"   align='left' ><?php echo $form->vence->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994;background: #D5E0D9;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->codban->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->codban->output; ?></td>
				<td class="littletablerowth"><?php echo $form->tipo->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->tipo->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->numche->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->numche->output; ?></td>
				<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994;background: #E3EFE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa1->label;  ?></td>
				<td colspan='3' class="littletablerow"  ><?php echo $form->observa1->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa2->label;  ?></td>
				<td  colspan='3' class="littletablerow"  ><?php echo $form->observa2->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>


<?php } elseif ($form->tipop->value == 3) { // Cheque Devuelto Cliente   ?>
		<td>
		<fieldset  style='border: 1px outset #FEB404; background: #EDDA4E;'>
		<table width='100%' cellspacing='0' cellpadding='0'>
			<tr>
				<td style='font-size:14pt;text-align:center;font-weight:bold;'>CHEQUE DEVUELTO DE CLIENTE</td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #FEB404; background: #EFECDA;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->clipro->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->clipro->output; ?></td>
				<td class="littletablerowth"><?php echo $form->nombre->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->nombre->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
				<!-- td class="littletablerowth" align='right'><?php echo $form->vence->label;  ?></td>
				<td class="littletablerow"   align='left' ><?php echo $form->vence->output; ?></td -->
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994; background: #D5E0D9;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->codban->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->codban->output; ?></td>
				<td class="littletablerowth"><?php echo $form->numche->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->numche->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->docum->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->docum->output; ?></td>
				<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
			</tr>
		</table>
		<?php echo $form->tipo->output; ?>

		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa1->label;  ?></td>
				<td colspan='3' class="littletablerow"  ><?php echo $form->observa1->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa2->label;  ?></td>
				<td  colspan='3' class="littletablerow"  ><?php echo $form->observa2->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>


<?php } elseif ($form->tipop->value == 4) { // Cheque Devuelto Proveedor ?>
		<td>
		<fieldset  style='border: 1px outset #FEB404; background: #24D8D8;'>
		<table width='100%' cellspacing='0' cellpadding='0'>
			<tr>
				<td style='font-size:14pt;text-align:center;font-weight:bold;'>CHEQUE DEVUELTO A PROVEEDOR</td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994; background: #D5E0D9;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->clipro->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->clipro->output; ?></td>
				<td class="littletablerowth"><?php echo $form->nombre->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->nombre->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994;background: #E3EFE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
				<!--td class="littletablerowth" align='right'><?php echo $form->vence->label;  ?></td>
				<td class="littletablerow"   align='left' ><?php echo $form->vence->output; ?></td -->
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994; background: #D5E0D9;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->codban->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->codban->output; ?></td>
				<td class="littletablerowth"><?php echo $form->numche->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->numche->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->docum->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->docum->output; ?></td>
				<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
			</tr>
		</table>
		</fieldset>
		<?php echo $form->tipo->output; ?>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994;background: #E3EFE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa1->label;  ?></td>
				<td colspan='3' class="littletablerow"  ><?php echo $form->observa1->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa2->label;  ?></td>
				<td  colspan='3' class="littletablerow"  ><?php echo $form->observa2->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>

<?php } elseif ($form->tipop->value == 5) { // Deposito por Analizar     ?>
		<td>
		<fieldset  style='border: 1px outset #FEB404; background: #EDDA4E;'>
		<table width='100%' cellspacing='0' cellpadding='0'>
			<tr>
				<td style='font-size:14pt;text-align:center;font-weight:bold;'>DEPOSITOS POR ANALIZAR</td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>

	<tr>
		<td>
		<fieldset  style='border: 1px outset #FEB404;background: #EFECDA;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->codban->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->codban->output; ?></td>
				<td class="littletablerowth"><?php echo $form->tipo->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->tipo->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->numche->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->numche->output; ?></td>
				<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa1->label;  ?></td>
				<td colspan='3' class="littletablerow"  ><?php echo $form->observa1->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa2->label;  ?></td>
				<td  colspan='3' class="littletablerow"  ><?php echo $form->observa2->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>

<?php } elseif ($form->tipop->value == 6) { // Cargo Indebido en Banco   ?>
		<td>
		<fieldset  style='border: 1px outset #FEB404; background: #24D8D8;'>
		<table width='100%' cellspacing='0' cellpadding='0'>
			<tr>
				<td style='font-size:14pt;text-align:center;font-weight:bold;'>CARGO INDEBIDO EN BANCO</td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994; background: #D5E0D9;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->codban->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->codban->output; ?></td>
				<td class="littletablerowth"><?php echo $form->tipo->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->tipo->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->numche->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->numche->output; ?></td>
				<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994;background: #E3EFE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994; background: #D5E0D9;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->clipro->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->clipro->output; ?></td>
				<td class="littletablerowth"><?php echo $form->nombre->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->nombre->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset  style='border: 1px outset #919994;background: #E3EFE8;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa1->label;  ?></td>
				<td colspan='3' class="littletablerow"  ><?php echo $form->observa1->output; ?></td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa2->label;  ?></td>
				<td  colspan='3' class="littletablerow"  ><?php echo $form->observa2->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>

<?php } ?>
	</tr>
</table>
<?php
if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
?>
<?php echo $form_end; ?>
