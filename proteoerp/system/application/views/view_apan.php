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

if($form->_status <> 'show'){

//$campos=$form->template_details('itcruc');

$scampos  ='<tr id="tr_itannc_<#i#>">';
$scampos .='<td class="littletablerow" align="left" ><input type="hidden" name="itnumero_<#i#>"  id="itnumero_<#i#>" ><input type="hidden" name="ittipo_<#i#>"  id="ittipo_<#i#>" ><span id="itnumero_<#i#>_val"></span><input type="hidden" name="itid_<#i#>"  id="itid_<#i#>" ></td>';
$scampos .='<td class="littletablerow" align="left" ><input type="hidden" name="itfecha_<#i#>"   id="itfecha_<#i#>"  ><span id="itfecha_<#i#>_val" ></span></td>';
$scampos .='<td class="littletablerow" align="right"><input type="hidden" name="itsaldo_<#i#>"   id="itsaldo_<#i#>"  ><span id="itsaldo_<#i#>_val" ></span></td>';
$scampos .='<td class="littletablerow" align="right"><input type="text" size="10" name="itmonto_<#i#>" id="itmonto_<#i#>" class="inputnum"></td>';
$scampos .='</tr>';
$campos=$form->js_escape($scampos);

$scampos  ='<tr id="tr_itefec_<#i#>">';
$scampos .='<td class="littletablerow" align="left" ><input type="hidden" name="itenumero_<#i#>" id="itenumero_<#i#>"><input type="hidden" name="itetipo_<#i#>" id="itetipo_<#i#>"><span id="itenumero_<#i#>_val"></span><input type="hidden" name="iteid_<#i#>"  id="iteid_<#i#>" ></td>';
$scampos .='<td class="littletablerow" align="left" ><input type="hidden" name="itefecha_<#i#>"  id="itefecha_<#i#>" ><span id="itefecha_<#i#>_val" ></span></td>';
$scampos .='<td class="littletablerow" align="right"><input type="hidden" name="itemonto_<#i#>"  id="itemonto_<#i#>" ><span id="itemonto_<#i#>_val" ></span></td>';
$scampos .='<td class="littletablerow" align="right"><input type="hidden" name="itesaldo_<#i#>"  id="itesaldo_<#i#>" ><span id="itesaldo_<#i#>_val" ></span></td>';
$scampos .='<td class="littletablerow" align="right"><input type="text" size="10" name="iteaplicar_<#i#>"  id="iteaplicar_<#i#>" class="inputnum"></td>';
$scampos .='</tr>';
$campos2=$form->js_escape($scampos);

?>
<script language="javascript" type="text/javascript">
var itannc_cont = 0;
var itefec_cont = 0;

$(document).ready(function() {
	$(".inputnum").numeric(".");
	$("#grid3_container").hide();
	$("#preinte").change(function(){
		if($(this).is(':checked')){
			$("#grid2_container").hide();
			$("#grid3_container").show();
		}else{
			$("#grid3_container").hide();
			$("#grid2_container").show();
			$("#reinte").val('');
			$("#reinte_val").text('');
		}
		cnota();
	});
});

function add_itannc(){
	var htm = <?php echo $campos; ?>;
	can = itannc_cont.toString();
	con = (itannc_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PNPL__").after(htm);
	$("#itmonto_"+can).numeric(".");
	itannc_cont=itannc_cont+1;
	return can;
}

function add_itefec(){
	var htm = <?php echo $campos2; ?>;
	var can = itefec_cont.toString();
	var con = (itefec_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PNPL2__").after(htm);
	$("#iteaplicar_"+can).numeric(".");
	itefec_cont=itefec_cont+1;
	return can;
}

function del_itannc(id){
	id = id.toString();
	obj='#tr_itannc_'+id;
	$(obj).remove();
}

function del_itefec(id){
	id = id.toString();
	obj='#tr_itefec_'+id;
	$(obj).remove();
}

function total(){
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
	return tmonto;
}

function totalefe(){
	var tmonto = 0;
	var arr=$('input[name^="iteaplicar_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind   = this.name.substring(pos+1);
			abono = Number(this.value);

			tmonto = tmonto+abono;
		}
	});

	return tmonto;
}

function cnota(){
	var mascara= "APLICA <#apl#>A <#efe#>";
	if($("#preinte").is(':checked')){
		<?php if ($form->tipo->insertValue == 'C') {  ?>
		mascara="CONVERSION DE <#apl#>A CxP";
		<?php }else{ ?>
		mascara="CONVERSION DE <#apl#>A CxC";
		<?php } ?>
	}

	var aplica = "";
	var efecto = "";

	//Aplicables
	var i = 0;
	var arr=$('input[name^="itmonto_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			var abono = Number(this.value);
			if(abono > 0){
				if(i > 0) aplica = aplica+",";
				var ind   = this.name.substring(pos+1);
				var tipo  = $("#ittipo_"+ind).val();
				var numero= $("#itnumero_"+ind).val();

				aplica = aplica+tipo+numero+" ";
				i = i+1;
			}
		}
	});

	//Efectos
	i = 0;
	var arr=$('input[name^="iteaplicar_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			var abono = Number(this.value);
			if(abono > 0){
				if(i > 0) efecto = efecto+",";
				var ind   = this.name.substring(pos+1);
				var tipo  = $("#itetipo_"+ind).val();
				var numero= $("#itenumero_"+ind).val();

				efecto = efecto+tipo+numero+" ";
				i = i+1;
			}
		}
	});

	mascara = mascara.replace(/<#apl#>/g,aplica);
	mascara = mascara.replace(/<#efe#>/g,efecto);

	$("#observa1").val(mascara);
}

function totaliza(){
	var tmonto = total();
	$("#monto").val(roundNumber(tmonto,2));
	tmonto = totalefe();
	$("#tefecto").html(roundNumber(tmonto,2));
}

function truncate(id){
	itcrud_cont = 0;
	$('tr[id^="tr_itefec_"]').remove();
	$('tr[id^="tr_itannc_"]').remove();
}
</script>
<?php } ?>

<?php echo $form->tipo->output; ?>
<fieldset  style='border: 1px outset #FEB404; background: #EDDA4E;'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr>
<?php if ($form->tipo->insertValue == 'C') {  ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>APLICACI&Oacute;N DE ANTICIPOS A CLIENTE</td>
<?php } elseif ($form->tipo->insertValue == 'P') { ?>
		<td style='font-size:14pt;text-align:center;font-weight:bold;'>APLICACI&Oacute;N DE ANTICIPOS A PROVEEDOR</td>
<?php } ?>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td><b class="littletablerowth"><?php echo $form->clipro->label;?></b><?php echo $form->clipro->output; ?><?php echo $form->nombre->output; ?></td>
		<td style='text-align:right;'>
			<b class="littletablerowth"><?php echo $form->preinte->label;?></b><?php echo $form->preinte->output;?>
		</td>
	</tr>
</table>
</fieldset>

<div align='center' style='border: 1px outset #EFEFEF;background: #EFEFFF '>
	<table style='width:100%;'>
		<tr>
			<td align='center'>
				<div id='grid1_container' style='overflow:auto;width:100%; height:210px; border: 1px outset #123;background: #FFFFFF; '>
					<table style='width:100%;' >
						<tr>
							<th colspan='4' class="littletableheaderdet">ANTICIPOS Y NC</th>
						</tr>

						<tr id='__PNPL__'>
							<th class="littletableheaderdet">N&uacute;mero</th>
							<th class="littletableheaderdet">Fecha</th>
							<th class="littletableheaderdet">Saldo</th>
							<th class="littletableheaderdet">Aplicar</th>
						</tr>
					</table>
				</div>
			</td>
			<td align='center'>
				<div id='grid2_container' style='overflow:auto;width:100%;height:210px; border: 1px outset #123;background: #FFFFFF;'>
					<table style='width:100%;'>
					<tr>
						<th colspan='5' class="littletableheaderdet">EFECTOS</th>
					</tr>
					<tr id='__PNPL2__'>
						<th class="littletableheaderdet">N&uacute;mero</th>
						<th class="littletableheaderdet">Fecha</th>
						<th class="littletableheaderdet">Monto</th>
						<th class="littletableheaderdet">Saldo</th>
						<th class="littletableheaderdet">Abono</th>
					</tr>
					</table>
				</div>
				<div id='grid3_container'  style='overflow:auto;width:100%;height:210px; border: 1px outset #123;background: #E4E4E4;'>
					<table>
						<tr>
							<td><b>Reintegrar a:</b></td>
							<td><?php echo $form->reinte->output; ?></td>
						</tr><tr>
							<td colspan='2'><span id='reinte_val' style='font-size:1.2em'></span></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td align='right'>
				<b class="littletablerowth">TOTAL ANTICIPOS</b><?php echo $form->monto->output;  ?>
			</td>
			<td align='right'>
				<b class="littletablerowth">TOTAL EFECTOS <span id='tefecto'>0.00</span></b>
			</td>
		</tr>

		
		
	</table>

</div>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr>
		<td class="littletablerowth">Observaciones:</td>
		<td class="littletablerow"  ><?php echo $form->observa1->output; ?></td>
	</tr>

</table>
</fieldset>
<?php echo $form_end; ?>
