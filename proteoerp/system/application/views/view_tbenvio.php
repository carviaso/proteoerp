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
var pseguro=0;
var pfledes=0;
var venvio =0;
var vautoriza=0;
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
	totalizar();
});

function cvolumen(){
	var v1 = Number($('#v1').val());
	var v2 = Number($('#v2').val());
	var v3 = Number($('#v3').val());

	var total  = v1+v2+v3;
	var tarifa = JSON.parse($.ajax({ type: "POST", url: "<?php echo site_url($this->url.'tarifa') ?>", data: {q:total} ,dataType: "json",async: false }).responseText);
	$('#volumen').val(tarifa);
	$('#volumen_val').text(nformat(tarifa,2));
	totalizar();
}

function ctarifa(){
	var peso = Number($('#peso').val());
	var org  = $('#codofi_org').val();
	var des  = $('#codofi_des').val();
	var tasa = 12;
	var total= 0;
	var iva  = 0;

	var tarifa = JSON.parse($.ajax({
		type: "POST",
		url: "<?php echo site_url($this->url.'tarifape') ?>",
		data: {'q':peso,'o':org,'d':des} ,
		dataType: "json",async: false }).responseText
		);

	pseguro = tarifa.seguro;
	pfledes = tarifa.fledes;
	venvio  = tarifa.monto;
	$('#kilo').val(tarifa.distancia);
	$('#ipostel').val(tarifa.iposte);
	$('#envio').val(tarifa.monto);
	$('#tasa').val(tarifa.iva);

	$('#kilo_val').text(nformat(tarifa.distancia,2));
	$('#ipostel_val').text(nformat(tarifa.iposte,2));
	$('#envio_val').text(nformat(tarifa.monto,2));

	fseguro();
	totalizar();
}

function fseguro(){
	var monto  = Number($('#montoaseg').val());
	var vseguro= roundNumber(monto*pseguro/100,2);

	$('#seguro').val(vseguro);
	$('#seguro_val').text(nformat(vseguro,2));
	totalizar();
}

function fexon(){
	if($('#exon').is(':checked')){
		var forma = $.prompt("<span id='pventana'>Cargando...</span>", {
			title: "Confirmación de exoneración",
			buttons: { "Verificar": true, "Salir": false },
			/*close: function(e,v,m,f){
					alert('cierra');
					var autoriza = $("#autoriza").val();
					if(autoriza.length == 0){
						$('#exon').removeAttr("checked");
					}
				},*/
			submit: function(e,v,m,f){
				if(v){
					var oficina = $("#codofi_org").val();
					if(vautoriza > 0){
						var verif = JSON.parse($.ajax({
							type: "POST",
							url: "<?php echo site_url($this->url.'verifica') ?>",
							data: {'oficina':oficina,'numero':vautoriza} ,
							dataType: "json",async: false }).responseText
						);

						if(verif){
							$('#autoriza').val(vautoriza);
							return true;
						}else{
							$('#_resul').text('no verificado');
							return false;
						}
					}else{
						return false;
					}
				}else{
					var autoriza = $("#autoriza").val();
					if(autoriza.length == 0){
						$('#exon').removeAttr("checked");
					}
					return true;
				}

				//console.log("Value clicked was: "+ v);
			}
		});
		forma.bind('promptloaded', function(e){
			var oficina = $("#codofi_org").val();
			var msj = 'Por favor espere mientras se genera el numero de confirmaci&oacute;n.';
			$('#pventana').html(msj);

			if(oficina.length>0){
				$.ajax({
					dataType: "json",
					type: 'POST',
					url : '<?php echo site_url($this->url.'autoriza') ?>',
					data: {'oficina' : oficina },
					success: function (data){
						vautoriza=data.numero;
						msj= 'Generado '+data.numero+' <span id="_resul">no verificado</span>';
						$('#pventana').html(msj);
					}
				});
			}else{
				//no tiene oficina de origen
			}

		});
	}else{
		$('#autoriza').val('');
	}
	return false;
	totalizar();
}

function ffledes(){
	if($('#fledes').is(':checked')){
		var nenvio=roundNumber(venvio*(100+pfledes)/100,2);
		$('#envio').val(nenvio);
		$('#envio_val').text(nformat(nenvio,2));
	}else{
		$('#envio').val(venvio);
		$('#envio_val').text(nformat(venvio,2));
	}
	totalizar();
}

function totalizar(){
	var tasa   = Number($('#tasa').val());
	var envio  = Number($('#envio').val());
	var ipostel= Number($('#ipostel').val());
	var kilo   = Number($('#kilo').val());
	var volumen= Number($('#volumen').val());
	var puertap= Number($('#puertap').val());
	var vseguro= Number($('#seguro').val());
	var base   = volumen+envio+kilo+puertap;
	var iva    = roundNumber(base*tasa/100,2);

	$('#subtotal').val(base);
	$('#subtotal_val').text(nformat(base,2));

	$('#iva').val(iva);
	$('#iva_val').text(nformat(iva,2));

	var total = base+iva+ipostel+vseguro;
	$('#total').val(total);
	$('#total_val').text(nformat(total,2));

}
</script>
<?php }
echo $container_tr;
?>

<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
	<legend align="left">Oficinas</legend>
	<table width="100%" style="margin: 0; width: 100%;">
		<tr>
			<td class="littletablerow"><?php echo $form->codofi_org->label;   ?>*</td>
			<td class="littletablerow" colspan='2'><?php echo $form->codofi_org->output;  ?></td>
			<td class="littletablerow"><?php echo $form->codofi_des->label;   ?>*</td>
			<td class="littletablerow" colspan='2'><?php echo $form->codofi_des->output;  ?></td>
		</tr>
	</table>
</fieldset>

<table width="100%" style='border-collapse: collapse;'>
	<tr>
		<td>
			<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
				<legend align="left">Remitente</legend>
				<table>
					<tr>
						<td class="littletableheader"><?php echo $form->codcli_org->label;   ?>*</td>
						<td class="littletablerow"   ><?php echo $form->codcli_org->output;  ?></td>
						<td class="littletableheader"><?php echo $form->telf_org->label;     ?>*</td>
						<td class="littletablerow"   ><?php echo $form->telf_org->output;    ?></td>
					</tr><tr>
						<td class="littletableheader"><?php echo $form->nomcli_org->label;   ?>*</td>
						<td class="littletablerow" colspan='3'><?php echo $form->nomcli_org->output;  ?> </td>
					</tr>
				</table>
			</fieldset>
		</td><td>
			<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
				<legend align="left">Destinatario</legend>
				<table>
					<tr>
						<td class="littletableheader"><?php echo $form->codcli_des->label;   ?>*</td>
						<td class="littletablerow"   ><?php echo $form->codcli_des->output;  ?> </td>
						<td class="littletableheader"><?php echo $form->telf_des->label;     ?>*</td>
						<td class="littletablerow"   ><?php echo $form->telf_des->output;    ?> </td>
					</tr><tr>
						<td class="littletableheader"><?php echo $form->nomcli_des->label;   ?>*</td>
						<td class="littletablerow" colspan='3'><?php echo $form->nomcli_des->output;  ?> </td>
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
	<table style='margin-left: auto;margin-right: auto;'>
		<tr>
			<td class="littletableheader"><?php echo $form->tipo->label;     ?>*</td>
			<td class="littletablerow"   ><?php echo $form->tipo->output;    ?></td>
			<td class="littletableheader"><?php echo $form->volumen->label;  ?>*</td>
			<td class="littletablerow" colspan='3'>
				<?php echo $form->v1->output.'x'.$form->v2->output.'x'.$form->v3->output.' '.$form->volumen->output; ?>
			</td>
		</tr><tr>
			<td class="littletableheader"><?php echo $form->cant->label;     ?>*</td>
			<td style='text-align:right' class="littletablerow"   ><?php echo $form->cant->output;    ?> </td>
			<td class="littletableheader"><?php echo $form->peso->label;     ?>*</td>
			<td style='text-align:right' class="littletablerow"   ><?php echo $form->peso->output;    ?> </td>
			<td class="littletableheader"><?php echo $form->envio->label;    ?>*</td>
			<td style='text-align:right' class="littletablerow"   ><?php echo $form->envio->output;   ?> </td>
		</tr><tr>
			<td class="littletableheader"><?php echo $form->kilo->label;     ?>*</td>
			<td style='text-align:right' class="littletablerow"   ><?php echo $form->kilo->output;    ?> </td>
			<td class="littletableheader"><?php echo $form->ipostel->label;  ?>*</td>
			<td style='text-align:right' class="littletablerow"   ><?php echo $form->ipostel->output; ?> </td>
			<td class="littletableheader"><?php echo $form->puertap->label;  ?>*</td>
			<td style='text-align:right' class="littletablerow"   ><?php echo $form->puertap->output; ?> </td>
		</tr><tr>
			<td class="littletableheader"><?php echo $form->descrip->label;  ?>*</td>
			<td class="littletablerow" colspan='3'><?php echo $form->descrip->output; ?> </td>
			<td class="littletableheader" ><?php echo $form->seguro->label;   ?>*</td>
			<td class="littletablerow"    style='text-align:right'><?php echo $form->seguro->output;  ?> </td>
		</tr>
	</table>
</fieldset>

<table style='border-collapse: collapse;'  width="100%">
	<tr><td>
		<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
			<legend align="left" >Detalles del seguro</legend>
			<table style='margin-left: auto;margin-right: auto;'>
				<tr>
					<td class="littletableheader"><?php echo $form->facturaaseg->label;  ?></td>
					<td class="littletablerow"   ><?php echo $form->facturaaseg->output; ?></td>
					<td class="littletableheader"><?php echo $form->rifaseg->label;      ?></td>
					<td class="littletablerow"   ><?php echo $form->rifaseg->output;     ?></td>
					<td class="littletableheader"><?php echo $form->montoaseg->label;    ?></td>
					<td class="littletablerow"   ><?php echo $form->montoaseg->output;   ?></td>
				</tr><tr>
					<td class="littletableheader"><?php echo $form->nombreaseg->label;   ?></td>
					<td class="littletablerow" colspan='5'><?php echo $form->nombreaseg->output;  ?></td>
				</tr>
			</table>
		</fieldset>
	</td><td>
		<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
			<legend align="left" >Opciones</legend>
			<table style='margin-left: auto;margin-right: auto;'>
				<tr>
					<td class="littletableheader"><?php echo $form->exon->label;    ?></td>
					<td class="littletablerow"   ><?php echo $form->exon->output.$form->autoriza->output;   ?></td>
				</tr><tr>
					<td class="littletableheader"><?php echo $form->fledes->label;  ?></td>
					<td class="littletablerow"   ><?php echo $form->fledes->output; ?></td>
				</tr>
			</table>
		</fieldset>
	</td></tr>
</table>

<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
	<legend align="left">Totales</legend>
	<table width="100%">
		<tr style='font-weight:bold;'>
			<td class="littletableheader" style='text-align:right'><?php echo $form->subtotal->label;  ?>*&nbsp;</td>
			<td class="littletablerow"    style='text-align:right'><?php echo $form->subtotal->output; ?> &nbsp;</td>
			<td class="littletableheader" style='text-align:right'><?php echo $form->iva->label;       ?>*&nbsp;</td>
			<td class="littletablerow"    style='text-align:right'><?php echo $form->iva->output;      ?> &nbsp;</td>
			<td class="littletableheader" style='text-align:right'><?php echo $form->total->label;     ?>*&nbsp;</td>
			<td class="littletablerow"    style='text-align:right;font-size:2em'><?php echo $form->total->output;    ?>&nbsp;</td>
		</tr>
	</table>
</fieldset>

<?php
echo $form->tasa->output;
echo (isset($form_end))? $form_end:'';
endif;
?>
