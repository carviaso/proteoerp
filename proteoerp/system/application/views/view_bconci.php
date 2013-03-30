<?php
echo $form_begin;

$container_tr=join('&nbsp;', $form->_button_container['TR']);
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$mod=true;

$scampos  ='<tr id="tr_bmov_<#i#>">';
$scampos .='<td class="littletablerow" align="center"><span id="itfecha_<#i#>_val"  ></span></td>';
$scampos .='<td class="littletablerow" align="left"  ><span id="ittipo_<#i#>_val"   ></span></td>';
$scampos .='<td class="littletablerow" align="left"  ><span id="itnumero_<#i#>_val" ></span><input type="hidden" name="ittipo_<#i#>" id="ittipo_<#i#>"></td>';
$scampos .='<td class="littletablerow" align="right" ><span id="itmonto_<#i#>_val"  ></span><input type="hidden" name="itmonto_<#i#>" id="itmonto_<#i#>"></td>';
$scampos .='<td class="littletablerow" align="center"><input type="checkbox" name="itid_<#i#>" id="itid_<#i#>" onchange="totalizar()"></td>';
$scampos .='</tr>';
$campos=$form->js_escape($scampos);

if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
var bmov_cont =0;

$(function(){
	$("#fecha").datepicker({
		dateFormat:"mm/yy",
		onSelect: function(dateText) {
			cambiaban();
		},
	});

	$("#codbanc").change(function(){
		cambiaban();
	});

	$(".inputnum").numeric(".");
});

function cambiaban(){
	var codban=$('#codbanc').val();
	var fecha =$('#fecha').val();
	if(codbanc!='' && fecha!=''){
		truncate();
		$.ajax({
			url: "<?php echo site_url('ajax/buscaconci'); ?>",
			dataType: "json",
			type: "POST",
			data: {"codbanc" : codban , "fecha": fecha},
			success: function(data){
					$.each(data,
						function(id, val){
							can= add_itbmov();

							$("#itid_" +can).val(val.id);
							$("#itnumero_" +can+"_val").text(val.numero);
							$("#ittipo_"   +can+"_val").text(val.tipo);
							$("#itfecha_"  +can+"_val").text(val.fecha);
							$("#itmonto_"  +can+"_val").text(nformat(val.monto,2));
							$("#itmonto_"  +can).val(val.monto);
							$("#ittipo_"   +can).val(val.tipo);
						}
					);
				},
		});
	}
}

function truncate(){
	bmov_cont =0;
	$('tr[id^="tr_bmov_"]').remove();
}

function add_itbmov(){
	var htm = <?php echo $campos; ?>;
	can = bmov_cont.toString();
	con = (bmov_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__bmov").before(htm);

	bmov_cont=bmov_cont+1;
	return can;
}

function del_itbmov(id){
	id = id.toString();
	$('#tr_bmov_'+id).remove();
}

function totalizar(){
	var total = 0;
	var arr=$('input[name^="itid_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind    = this.name.substring(pos+1);
			if(this.checked){
				monto  = Number($('#itmonto_'+ind).val());
				tipo   = $('#ittipo_'+ind).val();
				if(tipo=='CH' || tipo=='ND'){
					monto=(-1)*monto;
				}
				total  = total+Number(monto);
			}
		}
	});

	//$("#total").val(roundNumber(total,2));
	$("#conciliado").text(nformat(total,2));
}

</script>
<?php } ?>

<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table width='100%' style='font-size:11pt;background:#F2E69D;'>
	<tr>
		<td><b><?php echo $form->codbanc->label;     ?></b></td>
		<td colspan='3'><?php echo $form->codbanc->output;    ?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->fecha->label;     ?></b></td>
		<td>   <?php echo $form->fecha->output;    ?></td>
		<td><b><?php echo $form->deposito->label;  ?></b></td>
		<td>   <?php echo $form->deposito->output; ?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->saldoi->label;    ?></b></td>
		<td>   <?php echo $form->saldoi->output;   ?></td>
		<td><b><?php echo $form->credito->label;   ?></b></td>
		<td>   <?php echo $form->credito->output;  ?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->saldof->label;    ?></b></td>
		<td>   <?php echo $form->saldof->output;   ?></td>
		<td><b><?php echo $form->cheque->label;    ?></b></td>
		<td>   <?php echo $form->cheque->output;   ?></td>
	</tr>
	<tr>
		<td><b>Conciliado</b></td>
		<td><span id='conciliado'></span></td>
		<td><b><?php echo $form->debito->label;    ?></b></td>
		<td>   <?php echo $form->debito->output;   ?></td>
	</tr>
</table>
<div style='border: 1px solid #9AC8DA;background: #FAFAFA'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr style='background:#030B7A;color:#FDFDFD;font-size:10pt;'>
		<th align="center">Fecha</th>
		<th align="center">Tipo</th>
		<th align="center">N&uacute;mero</th>
		<th align="center">Monto</th>
		<th align="center">&nbsp;</th>
	</tr>


	<tr id='__UTPL__bmov'>
		<td colspan='5' class="littletableheaderdet">&nbsp;</td>
	</tr>
</table>
</div>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end; ?>
