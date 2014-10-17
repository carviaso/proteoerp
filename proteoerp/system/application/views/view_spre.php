<?php

$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itspre');
$scampos  ='<tr id="tr_itspre_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['desca']['field'].$campos['detalle']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['cana']['field'].  '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['preca']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['importe']['field'];
for($o=1;$o<5;$o++){
	$it_obj   = "precio${o}";
	$scampos .= $campos[$it_obj]['field'];
}
$scampos .= $campos['itiva']['field'];
$scampos .= $campos['sinvtipo']['field'];
$scampos .= $campos['ultimo']['field'];
$scampos .= $campos['pond']['field'];
$scampos .= $campos['sinvpeso']['field'].'</td>';
$scampos .= '<td class="littletablerow"  align="center"><a href=# onclick="del_itspre(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itspre_cont=<?php echo $form->max_rel_count['itspre']; ?>;

$(function(){

	$(".inputnum").numeric(".");
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	$("#fechadep").datepicker({dateFormat:"dd/mm/yy"});
	totalizar();
	for(var i=0;i < <?php echo $form->max_rel_count['itspre']; ?>;i++){
		cdropdown(i);
		cdescrip(i);
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
							$('#rifci').val('');
							$('#cod_cli').val('');
							$('#sclitipo').val('');
							$('#direc').val('');
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
			$('#cod_cli').attr("readonly", "readonly");

			$('#nombre').val(ui.item.nombre);
			$('#rifci').val(ui.item.rifci);
			$('#cod_cli').val(ui.item.cod_cli);
			$('#sclitipo').val(ui.item.tipo);
			$('#direc').val(ui.item.direc);
			$('#vd').val(ui.item.vendedor);

			setTimeout(function() {  $('#cod_cli').removeAttr("readonly"); }, 1500);
		}
	});

	$('input[name^="cana_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itspre();
			return false;
		}
	});
});

function importe(id){
	var ind     = id.toString();
	var cana    = Number($("#cana_"+ind).val());
	var preca   = Number($("#preca_"+ind).val());
	var iva     = Number($("#itiva_"+ind).val());
	var importe = roundNumber(cana*preca,2);
	$("#importe_"+ind).val(importe);
	//$("#importe_"+ind+"_val").text(nformat(importe));
	$("#importe_"+ind+"_val").text(nformat(importe*(100+iva)/100,2));

	totalizar();
}

function totalizar(){
	var iva    =0;
	var totalg =0;
	var itiva  =0;
	var itpeso =0;
	var totals =0;
	var importe=0;
	var peso   =0;
	var cana   =0;
	var arr=$('input[name^="importe_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			cana    = Number($("#cana_"+ind).val());
			itiva   = Number($("#itiva_"+ind).val());
			itpeso  = Number($("#sinvpeso_"+ind).val());
			importe = Number(this.value);

			peso    = peso+(itpeso*cana);
			iva     = iva+importe*(itiva/100);
			totals  = totals+importe;
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

function add_itspre(){
	var htm = <?php echo $campos; ?>;
	can = itspre_cont.toString();
	con = (itspre_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL__").after(htm);
	$("#cana_"+can).numeric(".");
	$("#cana_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itspre();
			return false;
		}
	});
	autocod(can);
	$('#codigo_'+can).focus();
	itspre_cont=itspre_cont+1;
}

function post_precioselec(ind,obj){
	if(obj.value=='o'){
		var itiva = Number($('#itiva_'+ind).val());
		var ittipo= $('#sinvtipo_'+ind).val();
		var renum = /^[0-9]+\.?[0-9*]*$/;
		var repor = /^[0-9]+\.?[0-9*]*%$/;

		otro = prompt('Precio nuevo','');
		if(renum.test(otro)){
			otro = Number(otro);
			if(otro>0){
				var opt=document.createElement("option");
				opt.text = nformat(otro,2);
				opt.value= roundNumber(otro*100/(100+itiva),2);
				obj.add(opt,null);
				obj.selectedIndex=obj.length-1;
			}
		}else if(repor.test(otro) && ittipo.substr(0,1)=='S'){
			otro = otro.replace("%","");
			$("#importe_"+ind).val('0');
			totalizar(ind);
			otro = Number(otro)/100;
			if(otro>0){
				var valor=Number($("#totalg").val())*otro;
				var opt=document.createElement("option");
				opt.text = nformat(valor,2);
				opt.value= roundNumber(valor*100/(100+itiva),2);
				obj.add(opt,null);
				obj.selectedIndex=obj.length-1;
			}
		}else{
			var ctipo  = $("#sclitipo").val();
			var tipo  = Number(ctipo); if(tipo>0) tipo=tipo-1;
			obj.selectedIndex=tipo;
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
		if(!otro){
			obj.selectedIndex=tipo;
		}else{
			if(pos>0){
				ind = this.name.substring(pos+1);
				id  = Number(ind);
				tipo=$('#sinvtipo_'+ind).val();
				if(tipo!='Servicio'){
					this.selectedIndex=tipo;
					importe(id);
				}
			}
		}
	});
	totalizar();
}

function post_modbus_sinv(nind){
	ind=nind.toString();
	var tipo =Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
	$("#preca_"+ind).empty();
	var arr=$('#preca_'+ind);
	cdropdown(nind);
	cdescrip(nind);
	jQuery.each(arr, function() { this.selectedIndex=tipo; });
	importe(nind);
	totalizar();
}

function cdropdown(nind){
	var ind=nind.toString();
	var preca=$("#preca_"+ind).val();
	var itiva   = Number($('#itiva_'+ind).val());
	var pprecio = document.createElement("select");

	pprecio.setAttribute("id"    , "preca_"+ind);
	pprecio.setAttribute("name"  , "preca_"+ind);
	pprecio.setAttribute("class" , "select");
	pprecio.setAttribute("style" , "width: 100px");
	pprecio.setAttribute("onchange" , "post_precioselec("+ind+",this)");

	var ban=0;
	var ii=0;
	var id='';

	if(preca==null || preca.length==0 || Number(preca)==0) ban=1;
	for(ii=1;ii<5;ii++){
		id =ii.toString();
		val=Number($("#precio"+id+"_"+ind).val());
		ntt = val*(1+(itiva/100));
		opt=document.createElement("option");
		opt.text =nformat(ntt,2);
		opt.value=val;
		pprecio.add(opt,null);
		if(val==preca){
			ban=1;
			pprecio.selectedIndex=ii-1;
		}
	}
	if(ban==0){
		opt=document.createElement("option");
		//opt.text = nformat(preca ,2);
		opt.text = nformat(Number(preca)*(1+(itiva/100)),2);
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

//Cambia el campo descripcion en caso ser servicio
function cdescrip(nind){
	var ind=nind.toString();
	var tipo =$("#sinvtipo_"+ind).val();

	if(tipo=='Servicio'){
		var desca  =$("#desca_"+ind).val();
		var detalle=$("#detalle_"+ind).val();
		var ddetalle = document.createElement("textarea");
		ddetalle.setAttribute("id"    , "detalle_"+ind);
		ddetalle.setAttribute("name"  , "detalle_"+ind);
		ddetalle.setAttribute("class" , "textarea");
		ddetalle.setAttribute("cols"  , 34);
		ddetalle.setAttribute("rows"  , 3);
		$("#detalle_"+ind).replaceWith(ddetalle);

		if(detalle.length==0){
			$("#detalle_"+ind).val(desca);
		}else{
			$("#detalle_"+ind).val(detalle);
		}

		var ddesca = document.createElement("input");
		ddesca.setAttribute("type"  , "hidden");
		ddesca.setAttribute("id"    , "desca_"+ind);
		ddesca.setAttribute("name"  , "desca_"+ind);
		ddesca.setAttribute("value" , desca);
		$("#desca_"+ind).replaceWith(ddesca);
	}else{
		var ddetalle = document.createElement("input");
		ddetalle.setAttribute("type", "hidden");
		ddetalle.setAttribute("id"    , "detalle_"+ind);
		ddetalle.setAttribute("name"  , "detalle_"+ind);
		ddetalle.setAttribute("value" , "");
		$("#detalle_"+ind).replaceWith(ddetalle);

		var desca = $("#desca_"+ind).val();
		var ddeca = document.createElement("input");
		ddeca.setAttribute("id"    , "desca_"+ind);
		ddeca.setAttribute("name"  , "desca_"+ind);
		ddeca.setAttribute("class" , "input");
		ddeca.setAttribute("size"  , 36);
		ddeca.setAttribute("maxlength", 50);
		ddeca.setAttribute("readonly" ,"readonly");
		ddeca.setAttribute("value"    ,desca);
		$("#desca_"+ind).replaceWith(ddeca);
	}
}

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
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
			$('#codigo_'+id).attr("readonly", "readonly");

			$('#codigo_'+id).val(ui.item.codigo);
			$('#desca_'+id).val(ui.item.descrip);
			$('#precio1_'+id).val(ui.item.base1);
			$('#precio2_'+id).val(ui.item.base2);
			$('#precio3_'+id).val(ui.item.base3);
			$('#precio4_'+id).val(ui.item.base4);
			$('#itiva_'+id).val(ui.item.iva);
			$('#sinvtipo_'+id).val(ui.item.tipo);
			$('#sinvpeso_'+id).val(ui.item.peso);
			$('#pond_'+id).val(ui.item.pond);
			$('#ultimo_'+id).val(ui.item.ultimo);
			$('#cana_'+id).val('1');
			$('#cana_'+id).focus();
			$('#cana_'+id).select();

			var tipo = Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
			$("#preca_"+id).empty();
			cdropdown(id);
			cdescrip(id);

			var arr  = $('#preca_'+id);
			jQuery.each(arr, function() { this.selectedIndex=tipo; });
			importe(id);
			totalizar();

			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		}
	}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		.append( "<a><table style='width:100%;border-collapse:collapse;padding:0px;'><tr><td colspan='6' style='font-size:14px;color:#0B0B61;'><b>" + item.descrip + "</b></td></tr><tr><td>Codigo:</td><td>" + item.codigo + "</td><td>Precio: </td><td><b>" + item.base1 + "</b></td><td>Existencia:</td><td>" + item.existen + "</td><td></td></tr></table></a>" )
		.appendTo( ul );
	};
}

function del_itspre(id){
	id = id.toString();
	$('#tr_itspre_'+id).remove();
	totalizar();
}

</script>
<?php } ?>

<table align='center' width="100%">
	<tr>
		<td>
		<table width='100%'>
			<tr>
				<td>
				<table width="100%" style="margin: 0; width: 100%;border:1px solid #9AC8DA;">
					<tr>
						<td class="littletableheader"><?php echo $form->cliente->label;  ?></td>
						<td class="littletablerow"   ><?php echo $form->cliente->output,$form->sclitipo->output; ?></td>
						<td class="littletableheader"         ><?php echo $form->rifci->label; ?>&nbsp;</td>
						<td class="littletablerow"            ><?php echo $form->rifci->output;   ?>&nbsp;</td>
						<td class="littletableheader"><?php echo $form->fecha->label;    ?></td>
						<td class="littletablerow"   ><?php echo $form->fecha->output;   ?></td>
					</tr><tr>
						<td class="littletableheader"><?php echo $form->nombre->label;  ?></td>
						<td class="littletablerow" colspan='5'><?php echo $form->nombre->output;  ?>&nbsp;</td>

					</tr><tr>
						<td class="littletableheader"         ><?php echo $form->direc->label  ?>&nbsp;</td>
						<td class="littletablerow" colspan='5'><?php echo $form->direc->output ?>&nbsp;</td>
					</tr><tr>
						<td class="littletableheader"         ><?php echo $form->dire1->label  ?>&nbsp;</td>
						<td class="littletablerow" colspan='5'><?php echo $form->dire1->output ?>&nbsp;</td>
					</tr>
				</table>
				</td>
				<td>
				<table width="100%" style="margin:0; width:100%;border:1px solid #9AC8DA;">
					<tr>
						<td class="littletableheader"><?php echo $form->vd->label     ?>&nbsp;</td>
						<td class="littletablerow">   <?php echo $form->vd->output    ?>&nbsp;</td>
					</tr><tr>
						<td class="littletableheader"><?php echo $form->telefono->label;    ?>*&nbsp;</td>
						<td class="littletablerow">   <?php echo $form->telefono->output;   ?>&nbsp;</td>
					</tr><tr>
						<td class="littletableheader"><?php echo $form->email->label  ?>&nbsp;</td>
						<td class="littletablerow"   ><?php echo $form->email->output ?>&nbsp;</td>
					</tr><tr>
						<td class="littletableheader"><?php echo $form->mercalib->label  ?>&nbsp;</td>
						<td class="littletablerow"   ><?php echo $form->mercalib->output ?>&nbsp;</td>
					</tr>
				</table>
				</td>
			<tr>
		</table>
		</td>
	</tr><tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:200px;width:550px;'>
		<table width='100%' border='0'>
			<tr id='__INPL__'>
				<th bgcolor='#7098D0'><strong>C&oacute;digo</strong></th>
				<th bgcolor='#7098D0'><strong>Descripci&oacute;n</strong></th>
				<th bgcolor='#7098D0'><strong>Cantidad</strong></th>
				<th bgcolor='#7098D0'><strong>Precio</strong></th>
				<th bgcolor='#7098D0'><strong>Importe</strong></th>
				<?php if($form->_status!='show') {?>
					<th bgcolor='#7098D0'>&nbsp;</th>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itspre'];$i++) {
				$it_codigo  = "codigo_${i}";
				$it_desca   = "desca_${i}";
				$it_cana    = "cana_${i}";
				$it_preca   = "preca_${i}";
				$it_importe = "importe_${i}";
				$it_iva     = "itiva_${i}";
				$it_ultimo  = "ultimo_${i}";
				$it_pond    = "pond_${i}";
				$it_peso    = "sinvpeso_${i}";
				$it_tipo    = "sinvtipo_${i}";
				$it_ultimo  = "ultimo_${i}";
				$it_detalle = "detalle_${i}";
				$it_pond    = "pond_${i}";
				$pprecios='';
				for($o=1;$o<5;$o++){
					$it_obj   = "precio${o}_${i}";
					$pprecios.= $form->$it_obj->output;
				}
				$pprecios .= $form->$it_ultimo->output;
				$pprecios .= $form->$it_pond->output;
				$pprecios .= $form->$it_iva->output;
				$pprecios .= $form->$it_peso->output;
				$pprecios .= $form->$it_tipo->output;
			?>

			<tr id='tr_itspre_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output; ?></td>
				<td class="littletablerow" align="left" ><?php
					if($form->_status=='show' && strlen($form->$it_detalle->value)>0){
						echo  '<pre>'.htmlspecialchars($form->$it_detalle->value).'</pre>';
					}else{
						echo $form->$it_desca->output.$form->$it_detalle->output;
					}
				?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_cana->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_preca->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_importe->output.$pprecios;?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow" align='center'>
					<a href='#' onclick='del_itspre(<?php echo $i ?>);return false;'><?php echo img("images/delete.jpg"); ?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>

			<tr id='__UTPL__'>
				<td id='cueca'></td>
			</tr>
		</table>
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;width:550px;'>
		<table width='100%' border='0'>
			<tr>
				<td><?php if($form->_status!='show'){?>
						<a href="#" onClick="add_itspre();"><?php echo image('add1-.png'); ?></a>
					<?php } ?>
				</td>
				<td class="littletableheader" width='100' rowspan='2'><?php echo $form->observa->label;  ?></td>
				<td class="littletablerow"    width='350' rowspan='2'><?php echo $form->observa->output; ?></td>
				<td class="littletableheader"                        ><?php echo $form->totals->label;   ?></td>
				<td class="littletablerow" align='right'             ><b id='totals_val'><?php echo nformat($form->totals->value); ?></b><?php echo $form->totals->output; ?></td>
			</tr><tr>
				<td>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->ivat->label;    ?></td>
				<td class="littletablerow" align='right'><b id='ivat_val'><?php echo nformat($form->ivat->value); ?></b><?php echo $form->ivat->output; ?></td>
			</tr><tr>
				<td class="littletableheader">&nbsp;</td>
				<td class="littletableheader"           ><?php echo $form->peso->label;  ?>&nbsp;</td>
				<td class="littletablerow" align="left" ><?php echo $form->peso->output; ?>&nbsp;</td-->
				<td class="littletableheader"><?php echo $form->totalg->label;  ?></td>
				<td class="littletablerow" align='right' style='font-size:18px;font-weight: bold'><b id='totalg_val'><?php echo nformat($form->totalg->value); ?></b><?php echo $form->totalg->output; ?></td>
			</tr>
		</table>
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;width:100%;'>
		<table width='100%' border='0'>
			<tr>
				<td colspan='6'><b>ANTICIPO</b></td>
				<td class="littletableheader"><?php echo $form->codbanc->label;  ?></td>
				<td class="littletablerow"   ><?php echo $form->codbanc->output; ?></td>
				<td class="littletableheader"><?php echo $form->tipo_op->label;  ?></td>
				<td class="littletablerow"   ><?php echo $form->tipo_op->output; ?></td>
				<td class="littletableheader"><?php echo $form->fechadep->label;  ?></td>
				<td class="littletablerow"   ><?php echo $form->fechadep->output; ?></td>
				<td class="littletableheader"><?php echo $form->num_ref->label;  ?></td>
				<td class="littletablerow"   ><?php echo $form->num_ref->output; ?></td-->
			</tr>
		</table>
		</div>
		</td>
	</tr>
</table>
<?php echo $form_end; ?>
<?php endif; ?>
