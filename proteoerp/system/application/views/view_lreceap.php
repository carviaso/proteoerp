<?php
echo $form_scripts;
echo $form_begin;

$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$mod=true;

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';
?>
<script type="text/javascript">
function gettransporte(){
	var fechal = $("#fechal").val();
	$.ajax({
		url: "<?php echo site_url('ajax/lrecetrans'); ?>",
		dataType: 'json',
		type: 'POST',
		data: {'q': fechal},
		success: function(data){
				var transpdd=$('#transporte');
				transpdd.empty();
				transpdd.append($('<option></option>').val('').html('Seleccionar'));
				$.each(data,
					function(id, val){
						transpdd.append($('<option></option>').val(val.value).html(val.label));
					}
				);
			},
	});
}

$(function(){
	$('.inputnum').focus(function (){
		$(this).select();
	});
	$('.inputnum').click(function (){
		$(this).select();
	});

	$('.inputnum').numeric('.');
	calconeto();
	calcolitro();

	$("#fechal").datepicker({
		dateFormat:"dd/mm/yy",
		onSelect: function(dateText) {
				gettransporte();
		},
	});

	$("#fechar").datepicker({dateFormat:"dd/mm/yy"});

	$('#transporte').change(function() {
		var fechal= $('#fechal').val();
		var fechar= $('#fechar').val();
		var valor = $('#transporte').val();

		if(valor.length>0 && fechal==fechar){
			var year =Number(fechal.slice(-4));
			var month=Number(fechal.slice(3,5))-1;
			var day  =Number(fechal.slice(0,2));

			f = new Date();
			f.setFullYear(year,month,day-1);
			d=f.getDate();
			m=f.getMonth()+1;
			a=f.getFullYear();
			ffetch=pad(d.toString(),2,'0',1)+'/'+pad(m.toString(),2,'0',1)+'/'+a.toString();

			$('#fechar').val(ffetch);
		}else if(valor.length==0 && fechal!=fechar){
			$('#fechar').val(fechal);
		}
	});


});

function calcolitro(){
	var lleno    = Number($("#lleno").val());
	var densidad = Number($("#densidad").val());
	var neto     = Number($("#neto").val());
	var vacio    = Number($("#vacio").val());

	if ( densidad == '' ) {
		densidad = 1.0164;
		$('#densidad').val(1.0164);
		$('#densidad_val').val(1.0164);
	}

	if ( vacio == 0 ) {
		$('#litros').val(neto);
		$('#litros_val').text(roundNumber(neto,2));
	} else {
		$('#litros').val(neto/densidad);
		$('#litros_val').text(roundNumber(neto/densidad,2));
	}
}

function calconeto(){
	var lleno    = Number($("#lleno").val());
	var vacio    = Number($("#vacio").val());
	var densidad = Number($("#densidad").val());
	var neto = 0;

	neto = lleno-vacio;

	if ( densidad == '' ) densidad = 1.0164;

	$('#neto').val(lleno-vacio);
	$('#neto_val').text(roundNumber(lleno-vacio,2));

	if ( vacio == 0 ) {
		$('#litros').val(neto);
		$('#litros_val').text(roundNumber(neto,2));
	} else {
		$('#litros').val(neto/densidad);
		$('#litros_val').text(roundNumber(neto/densidad,2));
	}
}
</script>
<table width='100%' style='font-size:11pt;background:#BEFCB5;'>
	<tr>
		<td class="littletableheaderc"><?php echo $form->ruta->label;    ?></td>
		<td colspan='3' class="littletablerow"    ><?php echo $form->ruta->output;   ?></td>
	</tr>
	<tr>
		<td class="littletableheaderc"><?php echo $form->nombre->label;    ?></td>
		<td colspan='3' class="littletablerow"    ><?php echo $form->nombre->output;   ?></td>
	</tr>
	<tr>
		<td class="littletableheaderc"><?php echo $form->flete->label;    ?></td>
		<td colspan='3' class="littletablerow"    ><?php echo $form->flete->output;   ?></td>
	</tr>
	<tr>
		<td class="littletableheaderc"><?php echo $form->fechal->label;    ?></td>
		<td class="littletablerow"    ><?php echo $form->fechal->output;   ?></td>
		<td class="littletableheaderc"><?php echo $form->fechar->label;    ?></td>
		<td class="littletablerow"    ><?php echo $form->fechar->output;   ?></td>
	</tr>
</table>
<br />
<fieldset style='border: 1px outset #407E13;background: #FAFAFA;'>
<legend class='subtitulotabla'>Pesadas del Transporte</legend>
<table width='100%' >
	<tr>
		<td class="littletableheaderc">Lleno</td>
		<td class="littletableheaderc">Vacio</td>
		<td class="littletableheaderc">Neto</td>
		<td class="littletableheaderc">Densidad</td>
	</tr>
	<tr>
		<td class="littletablerow"><?php echo $form->lleno->output;?></td>
		<td class="littletablerow"><?php echo $form->vacio->output;?></td>
		<td class="littletablerow"><?php echo $form->neto->output; ?></td>
		<td class="littletablerow"><?php echo $form->densidad->output;?></td>
	</tr>
</table>
</fieldset>

<fieldset style='border: 1px outset #407E13;background: #FAFAFA;'>
<table align='center' >
	<tr >
		<td style='font-size: 14pt;font-weight:bold;' class="littletableheaderc">Total Litros: </td>
		<td style='font-size: 14pt;font-weight:bold;' class="littletablerow"><?php echo $form->litros->output;  ?></td>
	</tr>
</table>
</fieldset>
<fieldset style='border: 1px outset #407E13;background: #FAFAFA;'>
<table align='center' >
	<tr>
		<td class="littletablerow"><?php echo $form->transporte->label;?></td>
		<td class="littletablerow"><?php echo $form->transporte->output;?></td>
	</tr>
</table>
</fieldset>




<?php echo $container_bl.$container_br; ?>
<?php echo $form_end; ?>
