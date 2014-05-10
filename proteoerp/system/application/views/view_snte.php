<?php

$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itsnte');
$scampos  ='<tr id="tr_itsnte_<#i#>">';
$scampos .='<td class="littletablerow" align="left" nowrap>'.$campos['codigo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['desca']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['cana']['field'].  '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['precio']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['importe']['field'];
for($o=1;$o<5;$o++){
	$it_obj   = "precio${o}";
	$scampos .= $campos[$it_obj]['field'];
}
$scampos .= $campos['itiva']['field'];
$scampos .= $campos['sinvpeso']['field'];
$scampos .= $campos['sinvtipo']['field'].'</td>';
$scampos .= '<td class="littletablerow"><a href=# onclick="del_itsnte(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itsnte_cont=<?php echo $form->max_rel_count['itsnte']; ?>;

$(function(){
	$(".inputnum").numeric(".");
	$("#fecha").datepicker({dateFormat:"dd/mm/yy"});

	$('input[id^="cana_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itsnte();
			return false;
		}
	});

	$('input[id^="cana_"]').focus(function() {
		$(this).select();
	});

	totalizar();
	for(var i=0;i < <?php echo $form->max_rel_count['itsnte']; ?>;i++){
		cdropdown(i);
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
				data: {'q':req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#nombre').val('');
							$('#nombre_val').text('');

							$('#rifci').val('');
							$('#rifci_val').text('');
							$('#sclitipo').val('1');

							$('#dir_cli').val('');
							$('#dir_cli_val').text('');
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

			$('#dir_cli').val(ui.item.direc);
			$('#dir_cli_val').text(ui.item.direc);
			setTimeout(function() {  $("#cod_cli").removeAttr("readonly"); }, 1500);
			post_modbus_scli();
		}
	});

});

function importe(id){
	var ind     = id.toString();
	var cana    = Number($("#cana_"+ind).val());
	var precio  = Number($("#precio_"+ind).val());
	var iva     = Number($("#itiva_"+ind).val());
	var importe = roundNumber(cana*precio,2);
	$("#importe_"+ind).val(importe);
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
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			cana    = Number($("#cana_"+ind).val());
			itiva   = Number($("#itiva_"+ind).val());
			importe = Number(this.value);
			itpeso  = Number($("#sinvpeso_"+ind).val());

			peso    = peso+(itpeso*cana);
			iva     = iva+importe*(itiva/100);
			totals  = totals+importe;
		}
	});
	$("#peso").val(roundNumber(peso,2));
	$("#gtotal").val(roundNumber(totals+iva,2));
	$("#stotal").val(roundNumber(totals,2));
	$("#impuesto").val(roundNumber(iva,2));

	$("#peso_val").text(nformat(peso,2));
	$("#gtotal_val").text(nformat(totals+iva,2));
	$("#stotal_val").text(nformat(totals,2));
	$("#impuesto_val").text(nformat(iva,2));

}

function add_itsnte(){
	var htm = <?php echo $campos; ?>;
	can = itsnte_cont.toString();
	con = (itsnte_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL__").after(htm);
	$("#cana_"+can).numeric(".");
	cdropdown(itsnte_cont);
	autocod(can);
	$('#codigo_'+can).focus();

	$('#cana_'+can).focus(function() {
		$(this).select();
	});
	//$('#cana_'+can).select();

	$("#cana_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itsnte();
			return false;
		}
	});

	itsnte_cont=itsnte_cont+1;
}

function post_precioselec(ind,obj){
	if(obj.value=='o'){
		var itiva = Number($('#itiva_'+ind).val());
		otro = prompt('Precio nuevo','');
		otro = Number(otro);
		if(otro>0){
			var opt=document.createElement("option");
			opt.value= roundNumber(otro*100/(100+itiva),2);
			opt.text = nformat(otro,2);
			obj.add(opt,null);
			obj.selectedIndex=obj.length-1;
		}
	}
	importe(ind);
}

function post_modbus_scli(){
	var tipo  =Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;

	$('#nombre_val').text($('#nombre').val());
	$('#rifci_val').text($('#rifci').val());
	$('#dir_cli_val').text($('#dir_cli').val());

	//var cambio=confirm('Deseas cambiar los precios por los que tenga asginado el cliente?');

	var arr=$('select[name^="precio_"]');
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
}

function post_modbus_sinv(nind){
	ind=nind.toString();
	var tipo =Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
	$("#precio_"+ind).empty();
	var arr=$('#precio_'+ind);
	cdropdown(nind);
	cdescrip(nind);
	jQuery.each(arr, function() { this.selectedIndex=tipo; });
	importe(nind);
	$('#codigo_'+ind).focus();
	totalizar();
}

function cdropdown(nind){
	var ind     = nind.toString();
	var preca   = $("#precio_"+ind).val();
	var itiva   = Number($('#itiva_'+ind).val());
	var pprecio = document.createElement("select");

	pprecio.setAttribute("id"    , "precio_"+ind);
	pprecio.setAttribute("name"  , "precio_"+ind);
	pprecio.setAttribute("class" , "select");
	pprecio.setAttribute("style" , "width: 100px");
	pprecio.setAttribute("onchange" , "post_precioselec("+ind+",this)");

	var ban=0;
	var ii=0;
	var id='';

	if(preca==null || preca.length==0) ban=1;
	for(ii=1;ii<5;ii++){
		id =ii.toString();
		val=Number($("#precio"+id+"_"+ind).val());
		ntt=val*(1+(itiva/100));
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
		opt.text = nformat(Number(preca)*(1+(itiva/100)),2);
		opt.value= preca;
		pprecio.add(opt,null);
		pprecio.selectedIndex=4;
	}

	opt=document.createElement("option");
	opt.text = 'Otro';
	opt.value= 'o';
	pprecio.add(opt,null);

	$("#precio_"+ind).replaceWith(pprecio);
}

function del_itsnte(id){
	id = id.toString();
	$('#tr_itsnte_'+id).remove();
	totalizar();
}

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinvart'); ?>",
				type: "POST",
				dataType: "json",
				data: {'q': req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#codigo_'+id).val('');
							$('#desca_'+id).val('');
							$('#precio1_'+id).val('');
							$('#precio2_'+id).val('');
							$('#precio3_'+id).val('');
							$('#precio4_'+id).val('');
							$('#itiva_'+id).val('');
							$('#sinvtipo_'+id).val('');
							$('#sinvpeso_'+id).val('');
							$('#itcosto_'+id).val('');
							$('#itpvp_'+id).val('');
							$('#cana_'+id).val('');
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
			$('#desca_'+id).val(ui.item.descrip);
			$('#precio1_'+id).val(ui.item.base1);
			$('#precio2_'+id).val(ui.item.base2);
			$('#precio3_'+id).val(ui.item.base3);
			$('#precio4_'+id).val(ui.item.base4);
			$('#itiva_'+id).val(ui.item.iva);
			$('#sinvtipo_'+id).val(ui.item.tipo);
			$('#sinvpeso_'+id).val(ui.item.peso);
			$('#itcosto_'+id).val(ui.item.pond);
			$('#itpvp_'+id).val(ui.item.ultimo);
			$('#cana_'+id).val('1');
			$('#cana_'+id).focus();
			$('#cana_'+id).select();

			var arr  = $('#preca_'+id);
			var tipo = Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
			cdropdown(id);
			//cdescrip(id);
			jQuery.each(arr, function() { this.selectedIndex=tipo; });
			importe(id);
			totalizar();
			setTimeout(function(){ $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		}
	}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		.append( "<a><table style='width:100%;border-collapse:collapse;padding:0px;'><tr><td colspan='6' style='font-size:14px;color:#0B0B61;'><b>" + item.descrip + "</b></td></tr><tr><td>Codigo:</td><td>" + item.codigo + "</td><td>Precio: </td><td><b>" + item.base1 + "</b></td><td>Existencia:</td><td>" + item.existen + "</td><td></td></tr></table></a>" )
		.appendTo( ul );
	};
}
</script>
<?php } ?>

<table align='center' width="100%" border='0'>
	<tr>
		<td colspan=2>
			<table width='100%'><tr><td>
				<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
				<legend class="titulofieldset" style='color: #114411;'>Documento</legend>
				<table width="100%" style="margin: 0; width: 100%;">
				<tr>
					<td class="littletableheader"><?php echo $form->vende->label     ?>&nbsp;</td>
					<td class="littletablerow">   <?php echo $form->vende->output    ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheader"><?php echo $form->fecha->label;    ?>*&nbsp;</td>
					<td class="littletablerow">   <?php echo $form->fecha->output;   ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheader"><?php echo $form->peso->label;  ?>&nbsp;</td>
					<td class="littletablerow" align="left"><?php echo $form->peso->output; ?>&nbsp;</td>
				</tr>
				</table>
				</fieldset>
			</td><td>
				<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
				<legend class="titulofieldset" style='color: #114411;'>Cliente</legend>
				<table width="100%" style="margin: 0; width: 100%;">
				<tr>
					<td class="littletableheader"><?php echo $form->cliente->label;  ?>*&nbsp;</td>
					<td class="littletablerow">   <?php echo $form->cliente->output,$form->sclitipo->output,$form->nombre->output; ?>&nbsp;</td>

				</tr>
				<tr>
					<td class="littletableheader"><?php echo $form->dir_cli->label  ?>&nbsp;</td>
					<td class="littletablerow"   ><?php echo $form->dir_cli->output ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheader"><?php echo $form->almacen->label;     ?>&nbsp;</td>
					<td class="littletablerow">   <?php echo $form->almacen->output;    ?>&nbsp;</td>
				</tr>
				</table>
				</fieldset>
			</td></tr></table>
		</td>
	</tr>
</table>
<table align='center' width="100%">
	<tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:190px'>
		<table width='100%'>
			<tr id='__INPL__'>
				<td bgcolor='#7098D0'><b>C&oacute;digo</b></td>
				<td bgcolor='#7098D0'><b>Descripci&oacute;n</b></td>
				<td bgcolor='#7098D0'><b>Cantidad</b></td>
				<td bgcolor='#7098D0'><b>Precio</b></td>
				<td bgcolor='#7098D0'><b>Importe</b></td>
				<?php if($form->_status!='show') {?>
					<td bgcolor='#7098D0'>&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itsnte'];$i++) {
				$it_codigo  = "codigo_$i";
				$it_desca   = "desca_$i";
				$it_cana    = "cana_$i";
				$it_precio  = "precio_$i";
				$it_importe = "importe_$i";
				$it_iva     = "itiva_$i";
				$it_tipo    = "sinvtipo_$i";
				$it_peso    = "sinvpeso_$i";

				$pprecios='';
				for($o=1;$o<5;$o++){
					$it_obj   = "precio${o}_${i}";
					$pprecios.= $form->$it_obj->output;
				}
				$pprecios .= $form->$it_iva->output;
				$pprecios .= $form->$it_tipo->output;
				$pprecios .= $form->$it_peso->output;
			?>

			<tr id='tr_itsnte_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" nowrap><?php echo $form->$it_codigo->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_desca->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_cana->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_precio->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_importe->output.$pprecios;?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow">
					<a href='#' onclick='del_itsnte(<?=$i ?>);return false;'><?php echo img("images/delete.jpg");?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
		</table>
		</div>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
		<table width='100%'>
			<tr>
				<td class="littletableheader" width='100'><?php echo $form->observa->label;  ?>&nbsp;</td>
				<td class="littletablerow"    width='400'><?php echo $form->observa->output; ?>&nbsp;</td>

				<td class="littletableheader"><?php echo $form->impuesto->label;    ?></td>
				<td class="littletablerow" align='right'><b id='impuesto_val'><?php echo nformat($form->impuesto->value); ?></b><?php echo $form->impuesto->output; ?></td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->orden->label;  ?>&nbsp;</td>
				<td class="littletablerow" align="left"><?php echo $form->orden->output; ?>&nbsp;</td>

				<td class="littletableheader"><?php echo $form->stotal->label;  ?></td>
				<td class="littletablerow" align='right'><b id='stotal_val'><?php echo nformat($form->stotal->value); ?></b><?php echo $form->stotal->output; ?></td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->factura->label;  ?>&nbsp;</td>
				<td class="littletablerow" align="left"><?php echo $form->factura->output; ?>&nbsp;</td>

				<td class="littletableheader"><?php echo $form->gtotal->label;  ?></td>
				<td class="littletablerow" align='right'  style='font-size:18px;font-weight: bold'><b id='gtotal_val'><?php echo nformat($form->gtotal->value); ?></b> <?php echo $form->gtotal->output; ?> </td>
			</tr>
		</table>
		</fieldset>
		<?php echo $form_end; ?>
		</td>
	</tr>
</table>
<?php endif; ?>
