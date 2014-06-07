<?php
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
if($form->_status <> 'show'){

$campos=$form->template_details('itcruc');

$scampos  ='<tr id="tr_itcruc_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itonumero']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itofecha']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itpmonto']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itpsaldo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itmonto']['field'].'</td>';
$scampos .='</tr>';
$campos=$form->js_escape($scampos);

?>
<script language="javascript" type="text/javascript">
var itcrud_cont = <?php echo $form->max_rel_count['itcruc']; ?>;;

function add_itcruc(){
	var htm = <?php echo $campos; ?>;
	can = itcrud_cont.toString();
	con = (itcrud_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PNPL__").after(htm);
	$("#itmonto_"+can).numeric(".");
	$("#itmonto_"+can).keyup(function (e){ totaliza(); });
	$("#itmonto_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
			return false;
		}
	});
	itcrud_cont=itcrud_cont+1;
	return can;
}

function totaliza(){
	var tmonto = 0;
	var arr=$('input[name^="itmonto_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind   = this.name.substring(pos+1);
			abono = Number(this.value);

			tmonto = tmonto+abono;
		}
	});
	$("#monto").val(roundNumber(tmonto,2));
	$("#monto_val").text(nformat(tmonto,2));
}

function truncate(id){
	itcrud_cont = 0;
	$('tr[id^="tr_itcruc_"]').remove();
}

$(function() {
	$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
	$(".inputnum").numeric(".");
});
</script>
<?php } ?>

<fieldset  style='border: 1px outset #FEB404; background: #EDDA4E;'>
<table style='width:100%;' cellspacing='0' cellpadding='0'>
	<tr>
<?php if ($form->tipo->insertValue == 'C-P') {  ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE CLIENTE &#45; PROVEEDOR <?php echo $form->numero->output; ?></td>
<?php } elseif ($form->tipo->insertValue == 'C-C') { ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE CLIENTE &#45; CLIENTE <?php echo $form->numero->output; ?></td>
<?php } elseif ($form->tipo->insertValue == 'P-P') { ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE PROVEEDOR &#45; PROVEEDOR <?php echo $form->numero->output; ?></td>
<?php } elseif ($form->tipo->insertValue == 'P-C') { ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE PROVEEDOR &#45; CLIENTE <?php echo $form->numero->output; ?></td>
<?php } ?>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<label style='font-size:1.3em;font-weight:bold;' ><?php echo $form->proveed->label;  ?></label>
<table style='width:100%;'>
	<tr>
		<td class="littletablerow"  ><?php echo $form->proveed->output; ?></td>
		<td class="littletablerow"  ><?php echo $form->nombre->output;  ?></td>
		<td class="littletablerowth" style='text-align:right'><?php echo $form->saldoa->label;   ?></td>
		<td class="littletablerow"   style='text-align:right'><?php echo $form->saldoa->output;  ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table style='width:100%;'>
	<tr>
		<td class="littletablerowth"><?php echo $form->fecha->label;   ?></td>
		<td class="littletablerow"  ><?php echo $form->fecha->output;  ?></td>
		<td class="littletablerowth" style='text-align:right'><?php echo $form->monto->label;   ?></td>
		<td class="littletablerow"   style='text-align:right;font-weight:bold;font-size:1.3em;'><?php echo $form->monto->output;  ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;'>
<div align='center' style='border: 1px outset #EFEFEF;background: #EFEFFF '>
	<div id='grid1_container' style='width:100%;height:150px;overflow:auto'>
		<table style='width:100%;'>
			<tr id='__PNPL__'>
				<th class="littletableheaderdet">N&uacute;mero</th>
				<th class="littletableheaderdet">Fecha</th>
				<th class="littletableheaderdet">Monto</th>
				<th class="littletableheaderdet">Saldo</th>
				<th class="littletableheaderdet">Abono</th>
			</tr>
			<?php for($i=0;$i<$form->max_rel_count['itcruc'];$i++) {
				$it_onumero  = "itonumero_${i}";
				$it_ofecha   = "itofecha_${i}";
				$it_pmonto   = "itpmonto_${i}";
				$it_psaldo   = "itpsaldo_${i}";
				$it_monto    = "itmonto_${i}";

			?>
			<tr id="tr_itcruc_<?php echo $i; ?>">
				<td class="littletablerow" align="left" ><?php echo $form->$it_onumero->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_ofecha->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_pmonto->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_psaldo->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_monto->output;   ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #F2F2F2;'>
<label style='font-size:1.3em;font-weight:bold;' align='left'><?php echo $form->cliente->label;  ?></label>
<table style='width:100%;'>
	<tr>
		<td class="littletablerow"  ><?php echo $form->cliente->output; ?></td>
		<td class="littletablerow"  ><?php echo $form->nomcli->output;  ?></td>
		<td class="littletablerowth" style='text-align:right'><?php echo $form->saldod->label;   ?></td>
		<td class="littletablerow"   style='text-align:right'><?php echo $form->saldod->output;  ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table style='width:100%;' cellspacing='0' cellpadding='0'>
	<tr>
		<td class="littletablerowth"><?php echo $form->concept1->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->concept1->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->concept2->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->concept2->output; ?></td>
	</tr>
</table>
</fieldset>
<?php echo $form_end; ?>
