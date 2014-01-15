<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itpfac');
$scampos  ='<tr id="tr_itpfac_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigoa']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['desca']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['cana']['field'].  '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['preca']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['tota']['field'];
for($o=1;$o<5;$o++){
	$it_obj   = "precio${o}";
	$scampos .= $campos[$it_obj]['field'];
}
$scampos .= $campos['itiva']['field'];
$scampos .= $campos['sinvtipo']['field'];
$scampos .= $campos['itpvp']['field'];
$scampos .= $campos['itcosto']['field'];
$scampos .= $campos['sinvpeso']['field'];
$scampos .= $campos['itmmargen']['field'];
$scampos .= $campos['itformcal']['field'];
$scampos .= $campos['itultimo']['field'];
$scampos .= $campos['itpond']['field'];
$scampos .= $campos['precat']['field'];
$scampos .= $campos['itpm']['field'].'</td>';
$scampos .= '<td class="littletablerow"  align="center"><a href=# onclick="del_itpfac(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
itpfac_cont=<?php echo $form->max_rel_count['itpfac']; ?>;

$(function(){
	/*$(document).keydown(function(e){
		if (e.which == 13) return false;
	});*/
	$("#mmargen").hide();
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });

	$(".inputnum").numeric(".");
	totalizar();
	for(var i=0;i < <?php echo $form->max_rel_count['itpfac']; ?>;i++){
		<?php if(!($faplica < $fenvia)){ ?>
		cdropdown(i);
		<?php }?>
		autocod(i.toString());
	}

	$('#cod_cli').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
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
							$('#sclitipo').val('1');
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

	$('input[name^="cana_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itpfac();
			return false;
		}
	});
});

function cal_dxapli(nind){
	ind=nind.toString();
	cana2  =parseFloat($("#cana_"+ind).val());
	preca2 =parseFloat($("#precat_"+ind).val());
	dxapli2=parseFloat($("#dxapli_"+ind).val());

	$.post("<?php echo site_url('ventas/pfac/cal_dxapli')?>",{ preca:preca2,dxapli:dxapli2 },function(data){
		if(data=='_||_'){
			alert("El descuento a aplicar debe contener solo numeros y '+'. ejemplo:2+2");
			$("#preca_"+ind).val(preca2);
			$("#dxapli_"+ind).val('');
			$("#dxapli_"+ind).focus();
		}else{
			data=parseFloat(data);
			imp=cana2*data;
			imp=Math.round(imp*100)/100;
			$("#preca_"+ind).val(data);
			$("#tota_"+ind).val(imp);

		}

	})
}

function importe(id){
	var ind     = id.toString();
	var cana    = Number($("#cana_"+ind).val());
	var preca   = Number($("#preca_"+ind).val());
	var tota = roundNumber(cana*preca,2);
	$("#tota_"+ind).val(tota);
	$("#tota_"+ind+'_val').text(nformat(tota,2));

	totalizar();
}

function totalizar(){
	var iva    =0;
	var totalg =0;
	var itiva  =0;
	var itpeso =0;
	var totals =0;
	var tota   =0;
	var peso   =0;
	var cana   =0;
	var arr=$('input[name^="tota_"]');
	sclitipo=$("#sclitipo").val();

	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			cana    = Number($("#cana_"+ind).val());
			itiva   = Number($("#itiva_"+ind).val());
			itpeso  = Number($("#sinvpeso_"+ind).val());
			tota    = Number(this.value);

			peso    = peso+(itpeso*cana);
			iva     = iva+tota*(itiva/100);
			totals  = totals+tota;

			if(sclitipo=='5'){
				$("#dxapli_"+ind).show();
			}
			else{
				$("#dxapli_"+ind).hide();
				$("#dxapli_"+ind).val('');
			}

		}
	});
	$("#peso").val(roundNumber(peso,2));
	$("#peso_val").text(nformat(peso,2));
	$("#totalg").val(roundNumber(totals+iva,2));
	$("#totals").val(roundNumber(totals,2));
	$("#iva").val(roundNumber(iva,2));

	$("#totalg_val").text(nformat(totals+iva,2));
	$("#totals_val").text(nformat(totals,2));
	$("#ivat_val").text(nformat(iva,2));
}

function add_itpfac(){
	var htm = <?php echo $campos; ?>;
	can = itpfac_cont.toString();
	con = (itpfac_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL__").after(htm);
	$("#cana_"+can).numeric(".");
	autocod(can);
	$('#codigoa_'+can).focus();
	$("#cana_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itpfac();
			return false;
		}
	});
	itpfac_cont=itpfac_cont+1;
}

function post_precioselec(ind,obj){
	if(obj.value=='o'){
		otro = prompt('Precio nuevo','');
		otro = Number(otro);
		if(otro>0){
			var opt=document.createElement("option");
			opt.text = nformat(otro,2);
			opt.value= otro;
			obj.add(opt,null);
			obj.selectedIndex=obj.length-1;
		}
	}
	importe(ind);
}

function post_modbus_scli(){
	var tipo  =Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
	//var cambio=confirm('¿Deseas cambiar los precios por los que tenga asginado el cliente?');

	var arr=$('select[name^="preca_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind = this.name.substring(pos+1);
			id  = Number(ind);
			this.selectedIndex=tipo;
			importe(id);
		}
	});
	totalizar();

	nombre = $('#nombre').val();
	direc  = $('#direc').val();
	rifci  = $('#rifci').val();
	$('#nombre_val').text(nombre);
	$('#rifci_val').text(rifci);
	$('#direc_val').text(direc);
}


function post_modbus_sinv(nind){
	ind=nind.toString();
	var tipo = Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
	$("#preca_"+ind).empty();
	var arr=$('#preca_'+ind);

	cdropdown(nind);

	if(tipo!=5){
		jQuery.each(arr, function(){
			$('#'+this.id).prop('selectedIndex', tipo);
		});
	}else{
		sclimmargen=parseFloat($("#mmargen").val());
		sinvmmargen=parseFloat($("#mmargen_"+ind).val());
		sinvformcal=$("#formcal_"+ind).val();
		sinvpond=parseFloat($("#pond_"+ind).val());
		sinvultimo=parseFloat($("#ultimo_"+ind).val());
		sinvpm=parseFloat($("#pm_"+ind).val());
		if(sinvformcal=='U'){
			p=sinvultimo+(sinvultimo*sinvpm/100);
		}

		if(sinvformcal=='P'){
			p=sinvultimo+(sinvultimo*sinvpm/100);
		}

		if(sinvformcal=='M'){
			if(sinvultimo>sinvpond){
				p=sinvultimo+(sinvultimo*sinvpm/100);
			}else{
				p=sinvultimo+(sinvultimo*sinvpm/100);
			}
		}
		p=Math.round(p*100)/100;

		r=((p*(100-sclimmargen)*(100-sinvmmargen))/(100*100));
		valor=Math.round(r*100)/100;

		var pprecio  = document.createElement("input");
		pprecio.setAttribute("id"    , "preca_"+ind);
		pprecio.setAttribute("name"  , "preca_"+ind);
		pprecio.setAttribute("size"  , "10");
		pprecio.setAttribute("value" , valor);
		pprecio.setAttribute("readonly" , "true");
		pprecio.setAttribute("align"    , "right");
		pprecio.setAttribute("class"    , "inputnum");
		$("#preca_"+ind).replaceWith(pprecio);
	}
	$("#cana_"+ind).text();
	descrip=$("#desca_"+ind).val();
	$("#desca_"+ind+'_val').text(descrip);
	importe(nind);
	totalizar();
}

function cdropdown(nind){
	var ind=nind.toString();
	var preca=$("#preca_"+ind).val();
	var pprecio  = document.createElement("select");

	pprecio.setAttribute("id"    , "preca_"+ind);
	pprecio.setAttribute("name"  , "preca_"+ind);
	pprecio.setAttribute("class" , "select");
	pprecio.setAttribute("style" , "width: 100px");
	pprecio.setAttribute("onchange" , "post_precioselec("+ind+",this)");

	var ban=0;
	var ii=0;
	var id='';

	if(preca==null || preca.length==0) ban=1;

	for(ii=1;ii<5;ii++){
		id =ii.toString();
		val=$("#precio"+id+"_"+ind).val();
		opt=document.createElement("option");
		opt.text =nformat(val,2);
		opt.value=val;
		pprecio.add(opt,null);
		if(val==preca){
			ban=1;
			pprecio.selectedIndex=ii-1;
		}
	}
	if(ban==0){
		opt=document.createElement("option");
		opt.text = nformat(preca,2);
		opt.value= preca;
		pprecio.add(opt,null);
		pprecio.selectedIndex=4;
	}

	opt=document.createElement("option");
	opt.text = 'Otro';
	opt.value= 'o';
	pprecio.add(opt,null);

	$("#preca_"+ind).replaceWith(pprecio);
}

function del_itpfac(id){
	id = id.toString();
	$('#tr_itpfac_'+id).remove();
	totalizar();
}

//Agrega el autocomplete
function autocod(id){
	$('#codigoa_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinv'); ?>",
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
			$('#codigoa_'+id).attr("readonly", "readonly");

			$('#codigoa_'+id).val(ui.item.codigo);
			$('#desca_'+id).val(ui.item.descrip);
			$('#precio1_'+id).val(ui.item.base1);
			$('#precio2_'+id).val(ui.item.base2);
			$('#precio3_'+id).val(ui.item.base3);
			$('#precio4_'+id).val(ui.item.base4);
			$('#itiva_'+id).val(ui.item.iva);
			$('#sinvtipo_'+id).val(ui.item.tipo);
			$('#sinvpeso_'+id).val(ui.item.peso);
			$('#itcosto_'+id).val(ui.item.pond);
			$('#itpvp_'+id).val(ui.item.base1);
			$('#cana_'+id).val('1');
			$('#cana_'+id).focus();
			$('#cana_'+id).select();

			var arr  = $('#preca_'+id);
			var tipo = Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
			cdropdown(id);
			post_modbus_sinv(id);
			//cdescrip(id);
			jQuery.each(arr, function() { this.selectedIndex=tipo; });
			importe(id);
			totalizar();
			setTimeout(function() {  $("#codigoa_"+id).removeAttr("readonly"); }, 1500);
		}
	});
}
</script>
<?php } ?>
<table align='center' width="100%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
</table>
<table align='center' width="95%">
	<tr>
		<td colspan='2'>
		<fieldset style='border: 1px outset #9AC8DA;background: #FFFDE9;'>
			<table width='100%'>
				<tr>
					<td class="littletableheader"><?php echo $form->cliente->label;  ?>*&nbsp;</td>
					<td class="littletablerow">   <?php echo $form->cliente->output,$form->sclitipo->output; ?>&nbsp;</td>
					<td class="littletablerow">   <?php echo $form->nombre->output;  ?>&nbsp;</td>
				</tr><tr>
					<td class="littletableheader"><?php echo $form->rifci->label;  ?>&nbsp;</td>
					<td class="littletablerow"   ><?php echo $form->rifci->output; ?>&nbsp;</td>
					<td class="littletableheader"><?php echo $form->direc->label;  ?>&nbsp;
					<span class="littletablerow" ><?php echo $form->direc->output; ?>&nbsp;</span></td>
				</tr>
			</table>
		</fieldset>
		</td>
	</tr><tr>
		<td colspan='2'>
		<fieldset style='border: 1px outset #9AC8DA;background: #FFFDE9;'>
			<table width='100%'>
				<tr>
					<?php if($form->_status=='show'){ ?>
					<td class="littletableheader"><?php echo $form->status->label   ?>&nbsp;</td>
					<td class="littletablerow"   ><?php echo $form->status->output  ?>&nbsp;</td>
					<?php } ?>
					<td class="littletableheader"><?php echo $form->fecha->label;   ?>*&nbsp;</td>
					<td class="littletablerow"   ><?php echo $form->fecha->output;  ?>&nbsp;</td>
					<td class="littletableheader"><?php echo $form->vd->label       ?>&nbsp;</td>
					<td class="littletablerow"   ><?php echo $form->vd->output      ?>&nbsp;</td>
					<td class="littletableheader"><?php echo $form->peso->label     ?>&nbsp;</td>
					<td class="littletablerow"   ><?php echo $form->peso->output    ?>&nbsp;</td>
				</tr>
			</table>
		</fieldset>
		</td>
	</tr><tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:200px'>
			<table width='100%'>
				<tr id='__INPL__'>
					<td bgcolor='#7098D0'><b>C&oacute;digo</b></td>
					<td bgcolor='#7098D0'><b>Descripci&oacute;n</b></td>
					<td bgcolor='#7098D0'><b>Cantidad</b></td>
					<td bgcolor='#7098D0'><b>Precio</b></td>
					<td bgcolor='#7098D0'><b>Importe</b></td>
					<?php if($form->_status!='show'  && !($faplica < $fenvia)) {?>
						<td  bgcolor='#7098D0' align='center'><b>&nbsp;</b></td>
					<?php } ?>
				</tr>

				<?php for($i=0;$i<$form->max_rel_count['itpfac'];$i++) {
					$it_codigoa  = "codigoa_${i}";
					$it_desca    = "desca_${i}";
					$it_cana     = "cana_${i}";
					$it_preca    = "preca_${i}";
					$it_tota     = "tota_${i}";
					$it_iva      = "itiva_${i}";
					$it_peso     = "sinvpeso_${i}";
					$it_tipo     = "sinvtipo_${i}";
					$it_costo    = "itcosto_${i}";
					$it_pvp      = "itpvp_${i}";
					$it_mmargen  = "itmmargen_${i}";
					//$it_dxapli   = "dxapli_${i}";
					$it_pond     = "itpond_${i}";
					$it_ultimo   = "itultimo_${i}";
					$it_formcal  = "itformcal_${i}";
					$it_pm       = "itpm_${i}";
					$it_precat   = "precat_${i}";

					$pprecios='';
					for($o=1;$o<5;$o++){
						$it_obj   = "precio${o}_${i}";
						$pprecios.= $form->$it_obj->output;
					}
					$pprecios .= $form->$it_iva->output;
					$pprecios .= $form->$it_peso->output;
					$pprecios .= $form->$it_tipo->output;
					$pprecios .= $form->$it_costo->output;
					$pprecios .= $form->$it_pvp->output;
					$pprecios .= $form->$it_mmargen->output;
					$pprecios .= $form->$it_pond->output;
					$pprecios .= $form->$it_ultimo->output;
					$pprecios .= $form->$it_formcal->output;
					$pprecios .= $form->$it_pm->output;
					$pprecios .= $form->$it_precat->output;
				?>

				<tr id='tr_itpfac_<?php echo $i; ?>'>
					<td class="littletablerow" align="left" ><?php echo $form->$it_codigoa->output;       ?></td>
					<td class="littletablerow" align="left" ><?php echo $form->$it_desca->output;         ?></td>
					<td class="littletablerow" align="right"><?php echo $form->$it_cana->output;          ?></td>
					<td class="littletablerow" align="right"><?php echo $form->$it_preca->output;         ?></td>
					<td class="littletablerow" align="right"><?php echo $form->$it_tota->output.$pprecios;?></td>

					<?php if($form->_status!='show' && !($faplica < $fenvia)) {?>
					<td class="littletablerow" align="center">
						<a href='#' onclick='del_itpfac(<?php echo $i ?>);return false;'><?php echo img("images/delete.jpg") ?></a>
					</td>
					<?php } ?>
				</tr>
				<?php } ?>
				<tr id='__UTPL__'>
					<td></td>
				</tr>
			</table>
		</div>
		<?php echo $container_bl ?>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
			<table width='100%'>
				<tr>
					<td class="littletableheader" align='center'><?php echo $form->observa->label; ?></td>
					<td class="littletableheader"               ><?php echo $form->totals->label;  ?></td>
					<td class="littletablerow" align='right'><b id='totals_val'><?php echo nformat($form->totals->value); ?></b><?php echo $form->totals->output; ?></td>
				<tr></tr>
					<td class="littletablerow"   ><?php echo $form->observa->output; ?></td>
					<td class="littletableheader"><?php echo $form->ivat->label;     ?></td>
					<td class="littletablerow" align='right'><b id='ivat_val'><?php echo nformat($form->ivat->value); ?></b><?php echo $form->ivat->output; ?></td>
				<tr></tr>
					<td class="littletablerow"   ><?php echo $form->observ1->output; ?></td>
					<td class="littletableheader"><?php echo $form->totalg->label;   ?></td>
					<td class="littletablerow" align='right' style='font-size:18px;font-weight: bold'><b id='totalg_val'><?php echo nformat($form->totalg->value); ?></b><?php echo $form->totalg->output; ?></td>
				</tr>
			</table>
		</fieldset>
		<?php echo $form->mmargen->output;  ?>
		<?php echo $form_end; ?>
		</td>
	</tr>
</table>
<?php endif; ?>
