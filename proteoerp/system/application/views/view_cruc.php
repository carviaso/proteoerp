<?php
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

$tipo = $form->tipo->insertValue;
if(empty($tipo)){
	$tipo = $form->tipo->value;
}
if($form->_status <> 'show'){

$campos=$form->template_details('itcruc');

$scampos  ='<td class="littletablerow" align="left" >'.$campos['itonumero']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itofecha']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itpmonto']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itpsaldo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itmonto']['field'].$campos['ittipo']['field'].'</td>';
$scampos .='</tr>';
$campos   =$form->js_escape('<tr id="tr_itcruc_<#i#>">'.$scampos);
$camposapa=$form->js_escape('<tr id="tr_itcrucapa_<#i#>">'.$scampos);

$tancho='150';
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

function add_itcrucapa(){
	var htm = <?php echo $camposapa; ?>;
	can = itcrud_cont.toString();
	con = (itcrud_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PAPANPL__").after(htm);
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
			ittipo=$('#ittipo_'+ind).val();
			if(ittipo=='ADE'){
				abono = Number(this.value);
				tmonto = tmonto+abono;
			}
		}
	});
	$("#monto").val(roundNumber(tmonto,2));
	$("#monto_val").text(nformat(tmonto,2));
}

function totalizaapa(){
	var tmonto = 0;
	var arr=$('input[name^="itmonto_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind   = this.name.substring(pos+1);
			ittipo=$('#ittipo_'+ind).val();
			if(ittipo=='APA'){
				abono = Number(this.value);
				tmonto = tmonto+abono;
			}
		}
	});
	return tmonto;
}

function truncate(){
	//itcrud_cont = 0;
	$('tr[id^="tr_itcruc_"]').remove();
}

function truncateapa(){
	//itcrud_cont = 0;
	$('tr[id^="tr_itcrucapa_"]').remove();
}

function coment(){
	var cliente=$('#cliente').val();
	var proveed=$('#proveed').val();

	if(cliente!='' && proveed!=''){
		$('#concept1').val('Cruce de '+proveed+' con '+cliente);
	}
}

$(function(){
	$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
	$(".inputnum").numeric(".");

	var arr=$('input[name^="itmonto_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind   = this.name.substring(pos+1);
			ittipo=$('#ittipo_'+ind).val();
			if(ittipo=='ADE'){
				$(this).focus(function(){
					var valor = $(this).val();
					if(valor=="" || valor=="0" || valor=="0.0" || valor=="0.00"){
						$(this).val($('#itpsaldo_'+ind).val());
						totaliza();
					}
				});
			}else{
				$(this).focus(function(){
					var monto   = Number($("#monto").val());
					var montoapa= totalizaapa();
					var valor   = $(this).val();
					var aplsaldo= monto-montoapa;

					if(valor=="" || valor=="0" || valor=="0.0" || valor=="0.00"){
						saldo = Number($('#itpsaldo_'+ind).val());
						if(aplsaldo>saldo){
							$(this).val(saldo);
						}else{
							$(this).val(aplsaldo);
						}
					}
				});

			}
		}
	});
});
</script>
<?php } ?>

<fieldset  style='border: 1px outset #FEB404; background: #EDDA4E;'>
<table style='width:100%;' cellspacing='0' cellpadding='0'>
	<tr>
<?php if($tipo == 'C-P'){  $tancho='90'; ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE CLIENTE &#45; PROVEEDOR <?php echo $form->numero->output; ?></td>
<?php }elseif ($tipo == 'C-C'){ ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE CLIENTE &#45; CLIENTE <?php echo $form->numero->output; ?></td>
<?php }elseif ($tipo == 'P-P'){ ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>CRUCE PROVEEDOR &#45; PROVEEDOR <?php echo $form->numero->output; ?></td>
<?php }elseif ($tipo == 'P-C'){ $tancho='90'; ?>
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
	<div id='grid1_container' style='width:100%;height:<?php echo $tancho; ?>px;overflow:auto'>
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
				$it_tipo     = "ittipo_${i}";

				if($form->$it_tipo->value!='ADE'){
					continue;
				}
			?>
			<tr id="tr_itcruc_<?php echo $i; ?>">
				<td class="littletablerow" align="left" ><?php echo $form->$it_onumero->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_ofecha->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_pmonto->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_psaldo->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_monto->output.$form->$it_tipo->output; ?></td>
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

<?php if($tipo == 'C-P' || $tipo == 'P-C'){  ?>
<fieldset  style='border: 1px outset #FEB404;'>
<div align='center' style='border: 1px outset #EFEFEF;background: #EFEFFF '>
	<div id='grid2_container' style='width:100%;height:<?php echo $tancho; ?>px;overflow:auto'>
		<table style='width:100%;'>
			<tr id='__PAPANPL__'>
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
				$it_tipo     = "ittipo_${i}";

				if($form->$it_tipo->value!='APA'){
					continue;
				}
			?>
			<tr id="tr_itcrucapa_<?php echo $i; ?>">
				<td class="littletablerow" align="left" ><?php echo $form->$it_onumero->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_ofecha->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_pmonto->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_psaldo->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_monto->output.$form->$it_tipo->output; ?></td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>
</fieldset>
<?php } ?>

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
