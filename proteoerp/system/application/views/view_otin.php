<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itotin');
$scampos  ='<tr id="tr_itotin_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigo']['field'].   '</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['descrip']['field'].$campos['larga']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['precio']['field'].   '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['tasaiva']['field'].   '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['impuesto']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['importe']['field'].  '</td>';
$scampos .='<td class="littletablerow"><a href="#" onclick="del_itotin(<#i#>);return false;">'.img('images/delete.jpg').'</a></td>';
$scampos .='</tr>';
$campos   =$form->js_escape($scampos);

$sfpa_campos=$form->template_details('sfpa');
$sfpa_scampos  ='<tr id="tr_sfpa_<#i#>">';
$sfpa_scampos .='<td class="littletablerow" align="left"  >'.$sfpa_campos['tipo']['field'].  '</td>';
$sfpa_scampos .='<td class="littletablerow" align="center">'.$sfpa_campos['sfpafecha']['field'].  '</td>';
$sfpa_scampos .='<td class="littletablerow" align="left"  >'.$sfpa_campos['numref']['field'].'</td>';
$sfpa_scampos .='<td class="littletablerow" align="left"  >'.$sfpa_campos['banco']['field']. '</td>';
$sfpa_scampos .='<td class="littletablerow" align="right" >'.$sfpa_campos['monto']['field']. '</td>';
$sfpa_scampos .='<td class="littletablerow"><a href=# onclick="del_sfpa(<#i#>);return false;">'.img('images/delete.jpg').'</a></td></tr>';
$sfpa_campos=$form->js_escape($sfpa_scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){

	$sfpade=$sfpach="<option value=''>Ninguno</option>";
	$mSQL="SELECT cod_banc,nomb_banc FROM tban WHERE cod_banc<>'CAJ'";
	$query = $this->db->query($mSQL);
	foreach ($query->result() as $row){
		$sfpach.="<option value='".trim($row->cod_banc)."'>".trim($row->nomb_banc)."</option>";
	}
	$mSQL="SELECT codbanc AS cod_banc,CONCAT_WS(' ',TRIM(banco),numcuent) AS nomb_banc FROM banc WHERE tbanco <> 'CAJ' ORDER BY nomb_banc";
	$query = $this->db->query($mSQL);
	foreach ($query->result() as $row){
		$sfpade.="<option value='".trim($row->cod_banc)."'>".trim($row->nomb_banc)."</option>";
	}
?>

<script language="javascript" type="text/javascript">
var itotin_cont=<?php echo $form->max_rel_count['itotin']; ?>;
var sfpa_cont  =<?php echo $form->max_rel_count['sfpa'];   ?>;

$(function(){
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	$("#vence").datepicker({ dateFormat: "dd/mm/yy" });
	$("#fafecta").datepicker({ dateFormat: "dd/mm/yy" });
	$('input[name^="sfpafecha_"]').datepicker({ dateFormat: "dd/mm/yy" });
	$(".inputnum").numeric(".");
	totalizar();

	for(var i=0;i < <?php echo $form->max_rel_count['itotin']; ?>;i++){
		autocod(i.toString());
		importe(i);
	}

	$('#tipo_doc').change(function (){
		var tipo = $(this).val();
		if(tipo=='OT'){
			$('#tsfpa').show();
			$('#ffafecta').hide();
		}else if(tipo=='OC'){
			$('#tsfpa').hide();
			$('#ffafecta').hide();
		}else{
			$('#tsfpa').hide();
			$('#ffafecta').show();
		}
	});

	$('#cod_cli').autocomplete({
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
							$('#nombre').val('');
							$('#nombre_val').text('');

							$('#rifci').val('');
							$('#rifci_val').text('');

							$('#direc').val('');
							$('#direc_val').text('');
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
			$('#cod_cli').attr("readonly", "readonly");

			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);

			$('#rifci').val(ui.item.rifci);
			$('#rifci_val').text(ui.item.rifci);

			$('#cod_cli').val(ui.item.cod_cli);
			$('#sclitipo').val(ui.item.tipo);

			$('#direc').val(ui.item.direc);
			$('#direc_val').text(ui.item.direc);
			setTimeout(function() {  $("#cod_cli").removeAttr("readonly"); }, 1500);
		}
	});

	$('#afecta').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function(req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscaafecta'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#afecta').val('');
							$('#fafecta').val('');
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
			$('#afecta').attr("readonly", "readonly");
			$('#afecta').val(ui.item.value);
			$('#fafecta').val(ui.item.fecha);
			setTimeout(function() {  $("#afecta").removeAttr("readonly"); }, 1500);
		}
	});

	$('#tipo_doc').change();
});

function add_sfpa(){
	var htm = <?php echo $sfpa_campos; ?>;
	can = sfpa_cont.toString();
	con = (sfpa_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__ITPL__sfpa").after(htm);
	falta =faltante();
	$("#monto_"+can).val(roundNumber(falta,2));
	$("#sfpafecha_"+can).datepicker({ dateFormat: "dd/mm/yy" });
	sfpa_cont=sfpa_cont+1;
	return can;
}

function del_sfpa(id){
	id = id.toString();
	$('#tr_sfpa_'+id).remove();
	totalizar();
	var arr = $('input[id^="monto_"]');
	if(arr.length<=0){
		add_sfpa();
	}
}

function sfpatipo(id){
	id     = id.toString();
	tipo   = $("#tipo_"+id).val();
	sfpade = <?php echo $form->js_escape($sfpade); ?>;
	sfpach = <?php echo $form->js_escape($sfpach); ?>;
	banco  = $("#banco_"+id).val();
	if(tipo=='DE' || tipo=='NC'){
		$("#banco_"+id).html(sfpade);
	}else{
		$("#banco_"+id).html(sfpach);
	}
	$("#banco_"+id).val(banco);
	return true;
}

//Totaliza el monto por pagar
function apagar(){
	var pago=0;
	jQuery.each($('input[id^="monto_"]'), function() {
		pago+=Number($(this).val());
	});
	return pago;
}

//Determina lo que falta por pagar
function faltante(){
	totalg= Number($("#totalg").val());
	paga  = apagar();
	resto = totalg-paga;
	return resto;
}

function importe(id){
	var ind      = id.toString();
	var tasa     = Number($("#tasaiva_"+ind).val())/100;
	var precio   = Number($("#precio_"+ind).val());

	var impuesto = roundNumber(tasa*precio,2);
	var vimporte = roundNumber(precio+impuesto,2);

	$("#impuesto_"+ind).val(roundNumber(impuesto,2));
	$("#importe_"+ind).val(vimporte);

	$("#importe_"+ind+"_val").text(nformat(vimporte,2));
	$("#impuesto_"+ind+"_val").text(nformat(impuesto,2));
	totalizar();
}

function totalizar(){
	var iva    =0;
	var totalg =0;
	var totals =0;

	var impuesto=0;
	var vimporte=0;
	var vprecio =0;
	var arr=$('input[name^="importe_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind      = this.name.substring(pos+1);
			impuesto = Number($("#impuesto_"+ind).val());
			vimporte = Number(this.value);
			vprecio  = Number($("#precio_"+ind).val());

			iva     = iva+impuesto;
			totalg  = totalg+vimporte;
			totals  = totals+vprecio;
		}
	});
	$("#totalg_val").text(nformat(totalg,2));
	$("#totals_val").text(nformat(totals,2));
	$("#iva_val").text(nformat(iva,2));

	$("#totalg").val(roundNumber(totalg,2));
	$("#totals").val(roundNumber(totals,2));
	$("#iva").val(roundNumber(iva,2));

	var tipo = $('#tipo_doc').val();
	if(tipo=='OT'){
		resto=faltante();
		utmo =$('input[id^="monto_"]').first();
		hay  =Number(utmo.val());
		utmo.val(roundNumber(hay+resto,2));
	}
}

function add_itotin(){
	var htm = <?php echo $campos; ?>;
	can = itotin_cont.toString();
	con = (itotin_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PTPL__").after(htm);
	autocod(can);
	itotin_cont=itotin_cont+1;
	return can;
}

function post_modbus_botr(nind){
	ind=nind.toString();
	importe(nind);
	totalizar();
}

function del_itotin(id){
	id = id.toString();
	$('#tr_itotin_'+id).remove();
	totalizar();
}


//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/autobotr/C'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#codigo_'+id).val('')
							$('#descrip_'+id).val('');
							$('#larga_'+id).val('');
							//post_modbus_sinv(id);
						}else{
							$.each(data,
								function(i, val){
									sugiere.push( val );
								}
							);
							add(sugiere);
						}
					},
			})
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#codigo_'+id).attr("readonly", "readonly");

			$('#codigo_'+id).val(ui.item.codigo);
			$('#descrip_'+id).val(ui.item.descrip);
			$('#larga_'+id).val(ui.item.descrip);
			$('#precio_'+id).focus();
			$('#precio_'+id).select();

			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}
</script>
<?php } ?>

<table align='center' width="100%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
		<fieldset style='border: 1px outset #9AC8DA;background: #FFFFFF;'>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<th colspan='4' class="littletableheader">Otros Ingresos <b><?php if($form->_status=='show' or $form->_status=='modify' ) echo str_pad($form->numero->output,8,0,0); ?></b></th>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->tipo_doc->label  ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->tipo_doc->output ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->cliente->label;  ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->cliente->output.$form->rifci->output; ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fecha->label;    ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->fecha->output;   ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->nombre->label;   ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->nombre->output;  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->vence->label;    ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->vence->output;   ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->direc->label     ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->direc->output    ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->dpto->label      ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->dpto->output     ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->sucu->label      ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->sucu->output     ?>&nbsp;</td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:190px'>
		<table width='100%'>
			<tr id='__PTPL__'>
				<td class="littletableheaderdet">C&oacute;digo</td>
				<td class="littletableheaderdet">Nombre</td>
				<td class="littletableheaderdet">Precio</td>
				<td class="littletableheaderdet">Tasa</td>
				<td class="littletableheaderdet">Impuesto</td>
				<td class="littletableheaderdet">Importe</td>
				<?php if($form->_status!='show') {?>
				<td class="littletableheaderdet">&nbsp;</td>
				<?php } ?>
			</tr>
			<?php for($i=0;$i<$form->max_rel_count['itotin'];$i++) {
				$it_codigo   = "codigo_${i}";
				$it_descrip  = "descrip_${i}";
				$it_impuesto = "impuesto_${i}";
				$it_precio   = "precio_${i}";
				$it_importe  = "importe_${i}";
				$it_tasaiva  = "tasaiva_${i}";
				$it_larga    = "larga_${i}";

				if($form->_status=='show'){
					$ivaval=nformat(round($form->$it_impuesto->value/$form->$it_precio->value,2)*100,2);
				}else{
					$ivaval=$form->$it_tasaiva->output;
				}
			?>
			<tr id='tr_itotin_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" nowrap><?php echo $form->$it_codigo->output;  ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_descrip->output.$form->$it_larga->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_precio->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $ivaval;                    ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_impuesto->output;?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_importe->output; ?></td>
				<?php if($form->_status!='show') {?>
				<td class="littletablerow"><a href='#' onclick="del_itotin(<?php echo $i; ?>);return false;"><?php echo img('images/delete.jpg'); ?></a></td>
				<?php } ?>
			</tr>
			<?php } ?>
		</table>
		</div>
		<?php echo $container_bl.$container_br ?></td>
	</tr><tr>
		<td>
		<table width='100%' id='tsfpa'>
			<tr id='__ITPL__sfpa'>
				<td class="littletableheaderdet">Tipo</td>
				<td class="littletableheaderdet">Fecha</td>
				<td class="littletableheaderdet">N&uacute;mero</td>
				<td class="littletableheaderdet">Banco</td>
				<td class="littletableheaderdet">Monto</td>
				<?php if($form->_status!='show') {?>
					<td class="littletableheaderdet"></td>
				<?php } ?>
			</tr>
			<?php

			for($i=0; $i < $form->max_rel_count['sfpa']; $i++) {
				$tipo      = "tipo_${i}";
				$sfpafecha = "sfpafecha_${i}";
				$numref    = "numref_${i}";
				$monto     = "monto_${i}";
				$banco     = "banco_${i}";
			?>
			<tr id='tr_sfpa_<?php echo $i; ?>'>
				<td class="littletablerow" nowrap><?php echo $form->$tipo->output      ?></td>
				<td class="littletablerow" align="center"><?php echo $form->$sfpafecha->output ?></td>
				<td class="littletablerow">       <?php echo $form->$numref->output    ?></td>
				<td class="littletablerow">       <?php echo $form->$banco->output     ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$monto->output ?></td>
				<?php if($form->_status!='show') {?>
					<td class="littletablerow"><a href='#' onclick="del_sfpa(<?php echo $i; ?>);return false;"><?php echo img('images/delete.jpg'); ?></a></td>
				<?php } ?>
			</tr>
			<?php } ?>
			<tr id='__UTPL__sfpa'>
				<td colspan='<?php echo ($form->_status!='show')? '6':'5';  ?>' class="littletableheaderdet"  align='center'>
					<?php if($form->_status!='show'){
						echo $form->cajero->label.'* '.$form->cajero->output;
					}else{
						echo '&nbsp;';
					} ?>
				</td>
			</tr>
		</table>


		<fieldset style='border: 1px outset #9AC8DA;background: #FFFFFF;' id='ffafecta'>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->afecta->label;   ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->afecta->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->fafecta->label;  ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->fafecta->output; ?>&nbsp;</td>
			</tr>
		</table>
		</fieldset>


		</td>
	</tr><tr>
		<td>
		<fieldset style='border: 1px outset #9AC8DA;background: #FFFDE9;'>
		<table width='100%'>
			<tr>
				<td class="littletablerowth"><?php echo $form->observa1->label  ?></td>
				<td class="littletablerowth"><?php echo $form->totals->label    ?></td>
				<td class="littletablerow" align="right" style='font-size:1.2em;font-weight:bold;' ><?php echo $form->totals->output   ?></td>
			</tr><tr>
				<td class="littletablerow"  ><?php echo $form->observa1->output ?></td>
				<td class="littletablerowth"><?php echo $form->iva->label       ?></td>
				<td class="littletablerow" align="right" style='font-size:1.2em;font-weight:bold;' ><?php echo $form->iva->output      ?></td>
			</tr><tr>
				<td class="littletablerow"  ><?php echo $form->observa2->output ?></td>
				<td class="littletablerowth"><?php echo $form->totalg->label    ?></td>
				<td class="littletablerow" align="right" style='font-size:1.5em;font-weight:bold;' ><?php echo $form->totalg->output   ?></td>
			</tr>
		</table>
		</fieldset>
		<?php echo $form_end; ?></td>
	</tr>
</table>
<?php endif; ?>
