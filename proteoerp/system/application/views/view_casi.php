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

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itcasi');
$scampos  ='<tr id="tr_itcasi_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['cuenta']['field'].  '</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['referen']['field']. '</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['concepto']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itdebe']['field'].  '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['ithaber']['field']. '</td>';
$scampos .='<td rowspan="2" class="littletablerow"><a href=# onclick="del_itcasi(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$scampos .='</tr>';
$scampos .='<tr id="tr_itcasi_<#i#>_<#i#>">';
$scampos .='<td colspan="5" class="littletablerow" align="center"> <b>Centro de costo:</b> '.$campos['itccosto']['field'];
$scampos .=' <b>Sucursal:</b> '.$campos['itsucursal']['field'];
$scampos .= $campos['cpladeparta']['field'];
$scampos .= $campos['cplaccosto']['field'].'</td>';
$jscampos=$form->js_escape($scampos);
$jscosto =$form->js_escape($campos['itccosto']['field']);
$jssucus =$form->js_escape($campos['itsucursal']['field']);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo (isset($form_begin))? $form_begin:'';
if($form->_status!='show'){ ?>

<script language="javascript" type='text/javascript'>
var itcasi_cont=<?php echo $form->max_rel_count['itcasi']; ?>;

$(function(){
	$( "#fecha" ).datepicker({ dateFormat: "dd/mm/yy" });
	$(".inputnum").numeric(".");
	totaliza();

	var arr=$('input[name^="cpladeparta_"]');
	jQuery.each(arr, function() {
		var departa=this.value;
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			if(departa!='S'){
				$("#itccosto_"+ind).empty();
				$("#itsucursal_"+ind).empty();
				$("#itccosto_"+ind).append($('<option></option>').val('').html('No aplica'));
				$("#itsucursal_"+ind).append($('<option></option>').val('').html('No aplica'));
			}
			autocod(ind);

			$("#itdebe_"+ind).focusout(function(){
				var valor = $(this).val();
				if(valor==''){
					$(this).val('0');
				}
				totaliza();
			});

			$("#ithaber_"+ind).focusout(function(){
				var valor = $(this).val();
				if(valor==''){
					$(this).val('0');
				}
				totaliza();
			});

			$("#ithaber_"+ind).focus(function(){
				$(this).select();
			});

			$("#itdebe_"+ind).focus(function(){
				$(this).select();
			});

		}
	});

});

//Agrega el autocomplete
function autocod(id){
	$('#cuenta_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscacpla'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						$.each(data,
							function(i, val){
								sugiere.push( val );
							}
						);
						add(sugiere);
					},
			})
		},
		minLength: 2,
		select: function( event, ui ) {

			$('#cuenta_'+id).attr("readonly", "readonly");
			$('#cuenta_'+id).val(ui.item.codigo);
			$('#concepto_'+id).val(ui.item.descrip);
			$('#cpladeparta_'+id).val(ui.item.departa);
			$('#cplacosto_'+id).val(ui.item.ccosto);
			post_modbus(Number(id));
			setTimeout(function(){ $('#cuenta_'+id).removeAttr("readonly"); }, 1500);
			traesaldo(ui.item.value);
		}
	});
}

function validaDebe(i){
	var debe = Number($("#itdebe_"+i).val());
	if(debe>0)
		 $("#ithaber_"+i).val('0');
	totaliza();
}

function validaHaber(i){
	var haber =Number($("#ithaber_"+i).val());
	if(haber>0)
		$("#itdebe_"+i).val('0');
	totaliza();
}

function add_itcasi(){
	var htm = <?php echo $jscampos; ?>;
	can = itcasi_cont.toString();
	con = (itcasi_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PTPL__").after(htm);
	$("#itdebe_"+can).numeric(".");
	$("#ithaber_"+can).numeric(".");

	$("#itdebe_"+can).focusout(function(){
		var valor = $(this).val();
		if(valor==''){
			$(this).val('0');
		}
		totaliza();
	});
	$("#ithaber_"+can).focusout(function(){
		var valor = $(this).val();
		if(valor==''){
			$(this).val('0');
		}
		totaliza();
	});
	$("#ithaber_"+can).focus(function(){
		$(this).select();
	});
	$("#itdebe_"+can).focus(function(){
		$(this).select();
	});
	$("#ithaber_"+can).numeric(".");
	post_modbus(itcasi_cont);
	itcasi_cont=itcasi_cont+1;
	autocod(can);
}

function totaliza(){
	var debe=0;
	var haber=0;
	var arr=$('input[name^="itdebe_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			ithaber = Number($("#ithaber_"+ind).val());
			itdebe  = Number(this.value);

			haber = haber + ithaber;
			debe  = debe + itdebe;
			//post_modbus(Number(ind));
		}
	});
	total=debe-haber;
	$("#debe").val(roundNumber(debe,2));
	$("#haber").val(roundNumber(haber,2));
	$("#total").val(roundNumber(total,2));

	$("#debe_val").text(nformat(debe,2));
	$("#haber_val").text(nformat(haber,2));
	$("#total_val").text(nformat(total,2));
	if(total>0){
		$("#total_val").css("color",'blue');
	}else if(total==0){
		$("#total_val").css("color",'green');
	}else{
		$("#total_val").css("color",'red');
	}
}

function del_itcasi(id){
	id = id.toString();
	$('#tr_itcasi_'+id).remove();
	$('#tr_itcasi_'+id+'_'+id).remove();
	totaliza();
}

function post_modbus(nind){
	var ind=nind.toString();
	var concepto=$('#concepto_'+ind).val();
	$('#concepto_'+ind+'_val').text(concepto);
	var departa=$("#cpladeparta_"+ind).val();
	if(departa=='S'){
		var jscosto=<?php echo $jscosto; ?>;
		var jssucus=<?php echo $jssucus; ?>;
		$("#itccosto_"+ind).replaceWith(jscosto.replace(/<#i#>/g,ind));
		$("#itsucursal_"+ind).replaceWith(jssucus.replace(/<#i#>/g,ind));
	}else{
		$("#itccosto_"+ind).empty();
		$("#itsucursal_"+ind).empty();
		$("#itccosto_"+ind).append($('<option></option>').val('').html('No aplica'));
		$("#itsucursal_"+ind).append($('<option></option>').val('').html('No aplica'));
	}
}

function traesaldo(cu){
	if(cu!=''){
		var saldo= $.ajax({ type: "POST", data:{cuenta:cu},url: "<?php echo site_url('ajax/saldocuenta') ?>", async: false }).responseText;
		$('#saldocta').html('Saldo de la cuenta '+cu+' <b style="font-size:1.4em">'+nformat(saldo,2)+'</b>');
	}else{
			$('#saldocta').text('');
	}

}
</script>
<?php } ?>

<table align='center' width="100%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
		<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->comprob->label;  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->comprob->output; ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->status->label;   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->status->output;  ?>&nbsp; <span id='saldocta'></span></td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->fecha->label;   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->descrip->label;  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->descrip->output; ?>&nbsp;</td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<div id='grid_container' style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:230px'>
		<table width='100%'>
			<tr  id='__PTPL__' style='font-size:1.1em;font-weight:bold;'>
				<td bgcolor='#7098D0' >Cuenta</td>
				<td bgcolor='#7098D0' >Referencia</td>
				<td bgcolor='#7098D0' >Concepto</td>
				<td bgcolor='#7098D0' >Debe</td>
				<td bgcolor='#7098D0' >Haber</td>
				<?php if($form->_status!='show') {?>
					<td bgcolor='#7098D0' ><a href='#' onclick="add_itcasi()" title='Agregar otra cuenta'><?php echo img('images/agrega4.png'); ?></a></td>
				<?php } ?>
			</tr>
			<?php for($i=0;$i<$form->max_rel_count['itcasi'];$i++) {
				$it_cuenta     = "cuenta_$i";
				$it_concepto   = "concepto_$i";
				$it_referen    = "referen_$i";
				$it_debe       = "itdebe_$i";
				$it_haber      = "ithaber_$i";
				$it_ccosto     = "itccosto_$i";
				$it_sucursal   = "itsucursal_$i";
				$it_cplaccosto = "cplaccosto_$i";
				$it_cpladeparta= "cpladeparta_$i";
				$pprecios = $form->$it_cplaccosto->output;
				$pprecios .= $form->$it_cpladeparta->output;
			?>
			<tr id='tr_itcasi_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" nowrap><?php echo $form->$it_cuenta->output;  ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_referen->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_concepto->output;?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_debe->output;    ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_haber->output;   ?></td>
				<?php if($form->_status!='show') {?>
				<td rowspan='2' class="littletablerow">
					<a href='#' onclick='del_itcasi(<?php echo $i ?>);return false;'><?php echo img("images/delete.jpg"); ?></a>
				</td>
				<?php } ?>
			</tr>
			<tr id='tr_itcasi_<?php echo $i.'_'.$i; ?>'>
				<td colspan='5' class="littletablerow" align='center'>
					<b>Centro de costo:</b> <?php echo $form->$it_ccosto->output;  ?>
					<b>Sucursal:</b> <?php echo $form->$it_sucursal->output; echo $pprecios; ?>
				</td>
			</tr>
			<?php } ?>
		</table>
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<table width='100%'>
			<tr style='font-size:1em;'>
				<td style='font-size:1em;font-weight:bold;'><?php echo $form->debe->label;   ?></td>
				<td align='right'><?php echo $form->debe->output;  ?></td>
				<td rowspan='2' style='font-size:2.3em;font-weight:bold;'><?php echo $form->total->label;  ?></td>
				<td rowspan='2' style='font-size:2.3em;font-weight:bold;text-align:right'><?php echo $form->total->output; ?></td>
			</tr>
			<tr >
				<td style='font-weight:bold;'><?php echo $form->haber->label;  ?></td>
				<td align='right'><?php echo $form->haber->output; ?></td>
			</tr>
		</table>
		<?php echo (isset($form_end))? $form_end:''; ?>
		</td>
	</tr>
</table>
<?php endif; ?>
