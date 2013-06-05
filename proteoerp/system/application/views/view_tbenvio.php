<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo (isset($form_begin))? $form_begin:'';
if($form->_status!='show'){ ?>
<script language="javascript" type="text/javascript">
$(function(){

	$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
	$(".inputnum").numeric(".");

	$('#codcli_org').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function(req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscascli'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#nomcli_des').val('');
							//$('#nomclides_val').text('');

						}else{
							$.each(data,
								function(i, val){
									sugiere.push( val );
								}
							);
						}
						add(sugiere);
					},
			})
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#codcli_org').attr("readonly", "readonly");

			$('#nomcli_org').val(ui.item.nombre);

			setTimeout(function() {  $("#codcli_org").removeAttr("readonly"); }, 1500);
		}
	});


	$('#codcli_des').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function(req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscascli'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#nomcli_des').val('');

						}else{
							$.each(data,
								function(i, val){
									sugiere.push( val );
								}
							);
						}
						add(sugiere);
					},
			})
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#codcli_des').attr("readonly", "readonly");

			$('#nomcli_des').val(ui.item.nombre);

			setTimeout(function() {  $("#codcli_des").removeAttr("readonly"); }, 1500);
		}
	});
});

function cvolumen(){
	var v1 = Number($('#v1').val());
	var v2 = Number($('#v2').val());
	var v3 = Number($('#v3').val());

	var total  = v1+v2+v3;
	var tarifa = JSON.parse($.ajax({ type: "POST", url: "<?php echo site_url($this->url.'tarifa') ?>", data: {q:total} ,dataType: "json",async: false }).responseText);
	$('#volumen').val(tarifa);
}

function ctarifa(){
	var peso = Number($('#peso').val());
	var org  = $('#codofi_org').val();
	var des  = $('#codofi_des').val();
	var total= 0;

	var tarifa = JSON.parse($.ajax({
		type: "POST",
		url: "<?php echo site_url($this->url.'tarifape') ?>",
		data: {'q':peso,'o':org,'d':des} ,
		dataType: "json",async: false }).responseText
		);

	$('#kilo').val(tarifa.distancia);
	$('#ipostel').val(tarifa.iposte);
	$('#envio').val(tarifa.monto);

	total = Number(tarifa.distancia)+Number(tarifa.iposte)+Number(tarifa.monto);

	$('#subtotal').val(total);
	$('#subtotal_val').text(nformat(total,2));

}
</script>
<?php } ?>

	<?php echo $container_tr; ?>
	<fieldset style='border: 1px outset #FEB404;background: #9CF180;'>
		<legend align="left">Oficinas</legend>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletablerow"><?php echo $form->codofi_org->label;   ?>*&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->codofi_org->output;  ?>&nbsp; </td>
				<td class="littletablerow"><?php echo $form->codofi_des->label;   ?>*&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->codofi_des->output;  ?>&nbsp; </td>
			</tr>
		</table>
	</fieldset>

	<table>
		<tr>
			<td>
				<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
					<legend align="left">Remitente</legend>
					<table>
						<tr>
							<td class="littletableheader"><?php echo $form->codcli_org->label;   ?>*&nbsp;</td>
							<td class="littletablerow"   ><?php echo $form->codcli_org->output;  ?>&nbsp; </td>
							<td class="littletableheader"><?php echo $form->telf_org->label;     ?>*&nbsp;</td>
							<td class="littletablerow"   ><?php echo $form->telf_org->output;    ?>&nbsp; </td>
						</tr><tr>
							<td class="littletableheader"><?php echo $form->nomcli_org->label;   ?>*&nbsp;</td>
							<td class="littletablerow" colspan='3'><?php echo $form->nomcli_org->output;  ?>&nbsp; </td>
						</tr>
					</table>
				</fieldset>
			</td><td>
				<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
					<legend align="left">Destinatario</legend>
					<table>
						<tr>
							<td class="littletableheader"><?php echo $form->codcli_des->label;   ?>*&nbsp;</td>
							<td class="littletablerow"   ><?php echo $form->codcli_des->output;  ?>&nbsp; </td>
							<td class="littletableheader"><?php echo $form->telf_des->label;     ?>*&nbsp;</td>
							<td class="littletablerow"   ><?php echo $form->telf_des->output;    ?>&nbsp; </td>
						</tr><tr>
							<td class="littletableheader"><?php echo $form->nomcli_des->label;   ?>*&nbsp;</td>
							<td class="littletablerow" colspan='3'><?php echo $form->nomcli_des->output;  ?>&nbsp; </td>
						</tr><tr>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
					<legend align="left">Direcci&oacute;n de entrega</legend>
					<?php echo $form->dirdes->label;   ?>*&nbsp;<?php echo $form->dirdes->output; ?>
				</fieldset>
			</td>
		</tr>
	</table>
	<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
		<legend align="left">Detalles del paquete</legend>
		<table>
			<tr>
				<td class="littletableheader"><?php echo $form->tipo->label;     ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->tipo->output;    ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->peso->label;     ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->peso->output;    ?>&nbsp; </td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->volumen->label;  ?>*&nbsp;</td>
				<td class="littletablerow" colspan='3'>
					<?php echo $form->v1->output.'x'.$form->v2->output.'x'.$form->v3->output.' '; ?>
					<?php echo $form->volumen->output; ?>&nbsp;
				</td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->kilo->label;     ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->kilo->output;    ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->puertap->label;  ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->puertap->output; ?>&nbsp; </td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->seguro->label;   ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->seguro->output;  ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->ipostel->label;  ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->ipostel->output; ?>&nbsp; </td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->cant->label;     ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->cant->output;    ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->envio->label;    ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->envio->output;   ?>&nbsp; </td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->descrip->label;  ?>*&nbsp;</td>
				<td class="littletablerow" colspan='3'><?php echo $form->descrip->output; ?>&nbsp; </td>
			</tr>
		</table>
	</fieldset>

		<legend align="left">Monto asegurado</legend>
		<table>
			<tr>
				<td class="littletableheader"><?php echo $form->facturaaseg->label;  ?>*&nbsp;</td>
				<td class="littletableheader"><?php echo $form->facturaaseg->output; ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->rifaseg->label;     ?>&nbsp; </td>
				<td class="littletablerow"   ><?php echo $form->rifaseg->output;    ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->nombreaseg->label;  ?>*&nbsp;</td>
				<td class="littletableheader"><?php echo $form->nombreaseg->output; ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->montoaseg->label;   ?>&nbsp; </td>
				<td class="littletablerow"   ><?php echo $form->montoaseg->output;  ?>&nbsp; </td>
			</tr>
		</table>
		</legend>












	<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
		<legend align="left">Totales</legend>
		<table>
			<tr>
				<td class="littletableheader"><?php echo $form->subtotal->label;  ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->subtotal->output; ?>&nbsp; </td>

				<td class="littletableheader"><?php echo $form->iva->label;  ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->iva->output; ?>&nbsp; </td>

				<td class="littletableheader"><?php echo $form->total->label;  ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->total->output; ?>&nbsp; </td>
			</tr>
		</table>
	</fieldset>
<?php echo (isset($form_end))? $form_end:''; ?>
<?php endif; ?>
