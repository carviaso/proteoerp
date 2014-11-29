<style>
.tooltip{position: absolute;	background: #c0df71;color: #FFFFFF;border-radius:5px;font-family: "Lucida Grande", Lucida, Verdana, sans-serif;font-weight: bold;padding: 10px;margin-top: -10px;margin-left: 10px;z-index: 3;display: none;background-color: #B45F04;}
</style>
<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record' || strlen($form->output)==0):
	echo $form->output;
else:

$campos=$form->template_details('sitems');
$scampos  ='<tr id="tr_sitems_<#i#>" ondblclick="marcar(this)">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigoa']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['desca']['field'].$campos['detalle']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['cana']['field'].  '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['preca']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['tota']['field'];
for($o=1;$o<5;$o++){
	$it_obj   = "precio${o}";
	$scampos .= $campos[$it_obj]['field'];
}
$scampos .= $campos['itiva']['field'];
$scampos .= $campos['sinvtipo']['field'];
$scampos .= $campos['combo']['field'];
$scampos .= $campos['sinvpeso']['field'].'</td>';
$scampos .= '<td class="littletablerow"><a href=# onclick="del_sitems(<#i#>);return false;">'.img('images/delete.jpg').'</a></td></tr>';
$campos=$form->js_escape($scampos);

$sfpa_campos=$form->template_details('sfpa');
$sfpa_scampos  ='<tr id="tr_sfpa_<#i#>">';
$sfpa_scampos .='<td class="littletablerow" align="left" >'.$sfpa_campos['tipo']['field'].  '</td>';
//$sfpa_scampos .='<td class="littletablerow" align="center" >'.$sfpa_campos['sfpafecha']['field'].  '</td>';
$sfpa_scampos .='<td class="littletablerow" align="left" >'.$sfpa_campos['numref']['field'].'</td>';
$sfpa_scampos .='<td class="littletablerow" align="left" >'.$sfpa_campos['banco']['field']. '</td>';
$sfpa_scampos .='<td class="littletablerow" align="right">'.$sfpa_campos['monto']['field']. '</td>';
$sfpa_scampos .='<td class="littletablerow"><a href=# onclick="del_sfpa(<#i#>);return false;">'.img('images/delete.jpg').'</a></td></tr>';
$sfpa_campos=$form->js_escape($sfpa_scampos);

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
var sitems_cont=<?php echo $form->max_rel_count['sitems']; ?>;
var sfpa_cont  =<?php echo $form->max_rel_count['sfpa'];?>;
var sclidescu  =0; <?php // Porcentaje de descuento mayor dado en ficha de cliente ?>

$(function(){
	//Title direccion
	$("#nombre_val").mouseover(function(){
		var dirr = $(this).next().text();
		if( dirr.trim()!= "" ){
			$("#nombre_val").mousemove(function(e){
				var ppos=$(this).offset();
				$(this).next().css({left : ppos.left-80 , top: ppos.top-40});
				//$(this).next().css({left : e.pageX-80 , top: e.pageY-40});
			});
			eleOffset = $(this).offset();
			$(this).next().fadeIn("fast").css({
				left: eleOffset.left + $(this).outerWidth(),
				top: eleOffset.top
			});
		}
	}).mouseout(function(){
		$(this).next().fadeOut("fast");
	});

	$('#factura').attr('type', 'hidden');
	var manual = $("#manual").val();
	if(manual=='S'){
		$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	}
	$(".inputnum").numeric(".");

	totalizar();
	for(var i=0;i < <?php echo $form->max_rel_count['sitems']; ?>;i++){
		ind = i.toString();
		codigoa = $("#codigoa_"+ind).val();
		if(codigoa!=''){
			var combo  = $("#combo_"+ind).val();
			if(combo==''){
				cdropdown(i);
			}
			cdescrip(i);
		}
		autocod(i.toString());
		importe(i);
	}
	for(var i=0;i < <?php echo $form->max_rel_count['sfpa']; ?>;i++){
		sfpatipo(i);
		//$('#monto_'+i.toString()).focus(function (){ fresto(i.toString()); });
	}

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
							$('#sclitipo').val('1');

							$('#direc').val('');
							$('#direc_val').text('');

							$('#descuento').val('0');
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
			var meco;
			$('#cod_cli').attr("readonly", "readonly");

			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);

			$('#rifci').val(ui.item.rifci);
			$('#rifci_val').text(ui.item.rifci);

			$('#cod_cli').val(ui.item.cod_cli);
			if(Number(ui.item.tipo)>4){
				ui.item.tipo=4;
			}
			$('#sclitipo').val(ui.item.tipo);

			if ( ui.item.vendedor != ''){
				$('#vd').val(ui.item.vendedor);
			}

			meco = 'Direccion:'+ui.item.direc+" Telefono: "+ui.item.telefono+" Ciudad: "+ui.item.ciudad;
			$('#direc').val(meco);
			$('#direc_val').text(meco);

			var manual = $("#manual").val();
			if(manual=='S'){
				$('#descuento').val('0');
			}else{
				$('#descuento').val(ui.item.desc);
			}

			post_modbus_scli();
			setTimeout(function() {  $("#cod_cli").removeAttr("readonly"); }, 1500);
		}
	});

	$('#factura').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasfacdev'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#factura').val('');

							$('#nombre').val('');
							$('#nombre_val').text('');

							$('#rifci').val('');
							$('#rifci_val').text('');

							$('#cod_cli').val('');
							$('#sclitipo').val('1');

							$('#direc').val('');
							$('#direc_val').text('');

							truncate();
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
			$('#factura').attr("readonly", "readonly");
			$('#factura').val(ui.item.value);

			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);

			$('#rifci').val(ui.item.rifci);
			$('#rifci_val').text(ui.item.rifci);

			$('#cod_cli').val(ui.item.cod_cli);
			$('#sclitipo').val(ui.item.tipo);

			$('#direc').val(ui.item.direc);
			$('#direc_val').text(ui.item.direc);

			$('#vd').val(ui.item.vd);

			$('#descuento').val('0');

			truncate();
			$("#tipo_doc").val('D');
			$.ajax({
				url: "<?php echo site_url('ajax/buscasinvdev'); ?>",
				dataType: 'json',
				type: 'POST',
				data: {"q":ui.item.value},
				success: function(data){
						$.each(data,
							function(id, val){
								add_sitems();
								$('#codigoa_'+id).val(val.codigo);
								$('#detalle_'+id).val(val.detalle);
								$('#desca_'+id).val(val.descrip);
								$('#preca_'+id).val(val.preca);
								$('#precio1_'+id).val(val.base1);
								$('#precio2_'+id).val(val.base2);
								$('#precio3_'+id).val(val.base3);
								$('#precio4_'+id).val(val.base4);
								$('#itiva_'+id).val(val.iva);
								$('#sinvtipo_'+id).val(val.tipo);
								$('#sinvpeso_'+id).val(val.peso);
								$('#pond_'+id).val(val.pond);
								$('#ultimo_'+id).val(val.ultimo);
								$('#cana_'+id).val(val.cana);
								post_modbus_sinv(id);
							}
						);
					},
			});

			$.ajax({
				url: "<?php echo site_url('ajax/buscasfpadev'); ?>",
				dataType: 'json',
				type: 'POST',
				data: {"q":ui.item.value},
				success: function(data){
						$.each(data,
							function(id, val){
								add_sfpa();
								$('#tipo_'+id).val(val.tipo);
								$('#num_ref_'+id).val(val.num_ref);
								$('#banco_'+id).val(val.banco);
								$('#monto_'+id).val(val.monto);
							}
						);
						falta=faltante();
						if(falta>0){
							can=add_sfpa();
							$('#tipo_'+can).val('');
						}
					},
			});
			setTimeout(function() {  $("#factura").removeAttr("readonly"); }, 1500);
		}
	});

	$('input[name^="cana_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_sitems();
			return false;
		}
	});

	$('input[name^="codigoa_"]').keypress(function(e) {
		if(e.keyCode == 13) {
			return false;
		}
	});

	chreferen();
	//$("#scliexp").dialog({ autoOpen: false, height: 420, width: 400, modal: true });

<?php
	if(isset($form->error_string)) {
		if ( !empty($form->error_string) ) {
			$mensaje = preg_replace("/\r|\n/",'',$form->error_string);
?>
			var mensaje = "<?php echo "<h2 style='color:red;'>Advertencias</h2>".$mensaje; ?>";
			$.prompt(mensaje);
<?php
		}else{
			echo 'saldoven();';
		}
	}else{
		echo 'saldoven();';
	}
?>

	$('#cod_cli').keypress(function(e) {
		if(e.keyCode == 13) {
		    $('input[name^="codigoa_"]').first().focus();
			return false;
		}
	});

});

function itdevolver(numero){
	truncate();
	$('#tipo_doc').val('D');

	$('input[name="referen"]:radio').each(function(){
		if($(this).val() == 'M'){
			$(this).attr('checked','checked');
		}else{
			$(this).removeAttr('checked');
		}
	});

	$.ajax({
		url: "<?php echo site_url('ajax/buscasfacdev'); ?>",
		dataType: 'json',
		type: 'POST',
		data: {"q":numero},
		success: function(data){
			val=data[0];
			$('#factura').val(val.value);

			$('#nombre').val(val.nombre);
			$('#nombre_val').text(val.nombre);

			$('#rifci').val(val.rifci);
			$('#rifci_val').text(val.rifci);

			$('#cod_cli').val(val.cod_cli);
			$('#sclitipo').val(val.tipo);

			$('#direc').val(val.direc);
			$('#direc_val').text(val.direc);

			$('#vd').val(val.vd);

			$('#descuento').val('0');
		},
	});

	$.ajax({
		url: "<?php echo site_url('ajax/buscasinvdev'); ?>",
		dataType: 'json',
		type: 'POST',
		data: {"q":numero},
		success: function(data){
				$.each(data,
					function(id, val){
						add_sitems();
						$('#codigoa_'+id).val(val.codigo);
						$('#detalle_'+id).val(val.detalle);
						$('#desca_'+id).val(val.descrip);
						$('#preca_'+id).val(val.preca);
						$('#precio1_'+id).val(val.base1);
						$('#precio2_'+id).val(val.base2);
						$('#precio3_'+id).val(val.base3);
						$('#precio4_'+id).val(val.base4);
						$('#itiva_'+id).val(val.iva);
						$('#sinvtipo_'+id).val(val.tipo);
						$('#sinvpeso_'+id).val(val.peso);
						$('#pond_'+id).val(val.pond);
						$('#ultimo_'+id).val(val.ultimo);
						$('#cana_'+id).val(val.cana);
						post_modbus_sinv(id);
					}
				);
			},
	});

	$.ajax({
		url: "<?php echo site_url('ajax/buscasfpadev'); ?>",
		dataType: 'json',
		type: 'POST',
		data: {"q":numero},
		success: function(data){
				$.each(data,
					function(id, val){
						add_sfpa();
						$('#tipo_'+id).val(val.tipo);
						$('#num_ref_'+id).val(val.num_ref);
						$('#banco_'+id).val(val.banco);
						$('#monto_'+id).val(val.monto);
					}
				);
				falta=faltante();
				if(falta>0){
					can=add_sfpa();
					$('#tipo_'+can).val('');
				}
			},
	});
	setTimeout(function() {  $("#factura").removeAttr("readonly"); }, 1500);
}

function marcar(obj){
	var color = $(obj).css("background-color");
	if(color=='transparent'){
		$(obj).css("background-color", "#FFFF28");
	}else{
		$(obj).css("background-color", 'transparent');
	}
}

function aplicadesc(){
	var descu = Number($('#descuento').val());
	return descu;
}

function scliadd() {
	$.post("<?php echo site_url('ventas/scli/dataeditdialog/create') ?>", function(data){
		$('#scliexp').html(data);
		$('#scliexp').dialog('open');
	});
};

function limpiavacio(){
	//Limpia sitems
	var arr=$('input[name^="codigoa_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind  = this.name.substring(pos+1);
			if(this.value==''){
				del_sitems(parseInt(ind));
			}
		}
	});
}

function truncate(){
	$('tr[id^="tr_sitems_"]').remove();
	$('tr[id^="tr_sfpa_"]').remove();
	sitems_cont=sfpa_cont=0;
}

function importe(id){
	var ind     = id.toString();
	var cana    = Number($("#cana_"+ind).val());
	var preca   = Number($("#preca_"+ind).val());
	var iimporte= roundNumber(cana*preca,2);
	var itiva   = Number($('#itiva_'+ind).val());
	$("#tota_"+ind).val(iimporte);
	$("#tota_"+ind+"_val").text(nformat(iimporte*(1+(itiva/100)),2));

	totalizar();
}

function fresto(can){
	//var val =Number($('#monto_'+can).val());
	//var fal =faltante()+val;
	//$('#monto_'+can).val(fal);
	//$('#monto_'+can).select();
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

function totalizar(){
	var iva    =0;
	var totalg =0;
	var itiva  =0;
	var itpeso =0;
	var totals =0;
	var importe=0;
	var peso   =0;
	var cana   =0;
	var descu  =aplicadesc()/100;
	var descuento=0;
	var arr=$('input[name^="tota_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			cana    = Number($("#cana_"+ind).val());
			itiva   = Number($("#itiva_"+ind).val());
			itpeso  = Number($("#sinvpeso_"+ind).val());
			importe = Number(this.value);

			if(descu>0){
				itpreca = Number($("#preca_"+ind).val());
				if(!isNaN(itpreca)){
					nimporte  = roundNumber(itpreca*(1-descu),2)*cana;
					nimporte  = roundNumber(nimporte,2);
					descuento = descuento+(importe-nimporte);
					importe   = roundNumber(nimporte,2);
				}else{
					importe   = 0;
				}
			}

			peso    = peso+(itpeso*cana);
			iva     = iva+importe*(itiva/100);
			totals  = totals+importe;
		}
	});

	if(descuento>0){
		//descuento = roundNumber(descuento,2);
		$("#descuentomon_val").text(nformat(descuento,2));
	}else{
		$("#descuentomon_val").text(nformat(0,2));
	}
	totalg=totals+iva;
	$("#peso").val(roundNumber(peso,2));
	$("#totalg").val(roundNumber(totals+iva,2));
	$("#totals").val(roundNumber(totals,2));
	$("#iva").val(roundNumber(iva,2));
	$("#totalg_val").text(nformat(totalg,2));
	$("#totals_val").text(nformat(totals,2));
	$("#ivat_val").text(nformat(iva,2));

	resto=faltante();
	utmo =$('input[id^="monto_"]').first();
	hay  =Number(utmo.val());

	utmo.val(roundNumber(hay+resto,2));
}

function add_sitems(){
	var htm = <?php echo $campos; ?>;
	can = sitems_cont.toString();
	con = (sitems_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL__").after(htm);
	$("#cana_"+can).numeric(".");
	$("#cana_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_sitems();
			return false;
		}
	});
	autocod(can);
	$('#codigoa_'+can).focus();
	sitems_cont=sitems_cont+1;
	return can;
}

function add_sfpa(){
	var htm = <?php echo $sfpa_campos; ?>;
	can = sfpa_cont.toString();
	con = (sfpa_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__ITPL__sfpa").after(htm);
	falta =faltante();
	$("#monto_"+can).val(roundNumber(falta,2));

	//$('#monto_'+can).focus(function (){ fresto(can); });

	sfpa_cont=sfpa_cont+1;
	return can;
}

function fpaga( fp ){
	//Oculta uno y prende el otro
	if ( fp == 'M' ){
		$("#ditems01").hide();
		$("#fpefectivo").hide();
		$("#formapago").toggle();
	} else if ( fp == 'E' ) {
		$("#ditems01").hide();
		$("#formapago").hide();
		$("#fpefectivo").toggle();
	} else {
		$("#formapago").hide();
		$("#fpefectivo").hide();
		$("#ditems01").show();
	}
}

function fvuelto(){
	var vuelto;
	vuelto = roundNumber($('#pagacon').val()-$('#totalg').val(),2);
	$('#vuelto').html("Cambio: "+vuelto);
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
				$('#preca_'+ind).css('background-color:','#FFDD00');
			}
		}else if(repor.test(otro) && ittipo.substr(0,1)=='S'){
			otro = otro.replace("%","");
			$("#tota_"+ind).val('0');
			totalizar(ind);
			otro = Number(otro)/100;
			if(otro>0){
				var descu = aplicadesc()/100;
				var valor = Number($("#totalg").val())*otro/(1-descu);

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
	//var cambio=confirm('Deseas cambiar los precios por los que tenga asginado el cliente?');

	var arr=$('select[name^="preca_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind = this.name.substring(pos+1);
			id  = Number(ind);
			tipo=Number($('#sinvtipo_'+ind).val()); if(tipo>0) tipo=tipo-1;
			if(tipo!='Servicio'){
				//this.selectedIndex=tipo;
				$('#'+this.id).prop('selectedIndex', tipo);
				importe(id);
			}
		}
	});
	totalizar();
	saldoven();
	$('input[id^="codigoa_"]').first().focus();
}

function saldoven(){
	var codcli=$('#cod_cli').val();
	if(codcli!=''){
		$.ajax({
			url: "<?php echo site_url('ajax/ajaxsaldoscliven'); ?>",
			dataType: 'json',
			type: 'POST',
			data: {'clipro' : codcli},
			success: function(data){
				if(data>0){
					$.prompt("<span style='font-size:1.5em'>Cliente presenta saldo vencido de <b>"+nformat(data,2)+" Bs.</b> debe ponerse al d&iacute;a.</span>", {
						title: "Saldo vencido",
						buttons: { "Continuar": true },
						submit: function(e,v,m,f){
							$('input[id^="codigoa_"]').first().focus();
						}
					});
				}
			},
		});
	}
}

function post_modbus_sinv(nind){
	ind=nind.toString();
	var manual = $("#manual").val();
	var ctipo  = $("#sclitipo").val();
	var tipo   = Number(ctipo); if(tipo>0) tipo=tipo-1;
	var combo  = $("#combo_"+ind).val();
	//var codigo = $("#codigoa_"+ind).val();
	if(combo==''){
		$("#preca_"+ind).empty();

		if(manual!='S'){
			var arr=$('#preca_'+ind);
			cdropdown(nind);
			jQuery.each(arr, function(){
				//this.selectedIndex=tipo;
				$('#'+this.id).prop('selectedIndex', tipo);
			});
		}else{
			var prec = $('#precio'+ctipo+'_'+ind).val();
			if(prec!=undefined){
				$("#preca_"+ind).val(roundNumber(prec,2));
			}else{
				$("#preca_"+ind).val($('#precio1_'+ind).val());
			}
		}
	}
	cdescrip(nind);
	importe(nind);
	totalizar();
}

//Saca el dropdown de los precios
function cdropdown(nind){
	var manual  = $("#manual").val(); if(manual=='S') return true;
	var tipo_doc= $("#tipo_doc").val();
	var ind     = nind.toString();
	var combo   = $("#combo_"+ind).val();  if(combo!='') return true;
	var preca   = $("#preca_"+ind).val();
	var codigo  = $("#codigoa_"+ind).val(); if(codigo=='') return true;
	var itiva   = Number($('#itiva_'+ind).val());
	var pprecio = document.createElement("select");
	if(tipo_doc=='D') return false;

	if(manual=== 'S' ) {
		$("#preca_"+ind).attr('readonly',false);
		$("#preca_"+ind).attr("onchange" , "post_precioselec("+ind+",this)");
		return true;
	}

	pprecio.setAttribute("id"    , "preca_"+ind);
	pprecio.setAttribute("name"  , "preca_"+ind);
	pprecio.setAttribute("class" , "select");
	pprecio.setAttribute("style" , "width: 95px");
	pprecio.setAttribute("onchange" , "post_precioselec("+ind+",this)");

	var ban=0;
	var ii=0;
	var id='';

	if(preca==null || preca.length==0 || Number(preca)==0) ban=1;
	for(ii=1;ii<5;ii++){
		id  = ii.toString();
		val = Number($("#precio"+id+"_"+ind).val());
		ntt = val*(1+(itiva/100));
		opt = document.createElement("option");
		opt.text =nformat(ntt,2);
		opt.value=val;
		pprecio.add(opt,null);
		if(val == preca){
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
		pprecio.style = 'width: 95px;background-color:#FFDD00';
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
		var desca  = $("#desca_"+ind).val();
		var detalle= $("#detalle_"+ind).val();
		var ddetalle = document.createElement("textarea");
		ddetalle.setAttribute("id"    , "detalle_"+ind);
		ddetalle.setAttribute("name"  , "detalle_"+ind);
		ddetalle.setAttribute("class" , "textarea");
		ddetalle.setAttribute("cols"  , 43);
		ddetalle.setAttribute("rows"  , 2);
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
		ddeca.setAttribute("size"  , 45);
		ddeca.setAttribute("maxlength", 50);
		ddeca.setAttribute("readonly" ,"readonly");
		ddeca.setAttribute("value"    ,desca);
		$("#desca_"+ind).replaceWith(ddeca);
	}
}

//Agrega el autocomplete
function autocod(id){
	var ancho='.ui-autocomplete-input#codigoa_'+id;
	var ihtml;
	$('#codigoa_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinv'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term.trim(), "alma": $('#almacen').val().trim() },
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#codigoa_'+id).val('')
							$('#desca_'+id).val('');
							$('#precio1_'+id).val('');
							$('#precio2_'+id).val('');
							$('#precio3_'+id).val('');
							$('#precio4_'+id).val('');
							$('#itiva_'+id).val('');
							$('#sinvtipo_'+id).val('');
							$('#sinvpeso_'+id).val('');
							$('#pond_'+id).val('');
							$('#ultimo_'+id).val('');
							$('#cana_'+id).val('');
							post_modbus_sinv(Number(id));
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
			$('#codigoa_'+id).attr("readonly", "readonly");
			if(ui.item.tipo.substr(0,1)=='C'){
				$.ajax({
					url: "<?php echo site_url('ajax/buscasinvcombo'); ?>",
					dataType: 'json',
					type: 'POST',
					data: {"q":ui.item.codigo.trim()},
					success: function(data){
							$.each(data,
								function(iid, val){
									$('#codigoa_'+id).val(val.codigo);
									$('#desca_'+id).val(val.descrip);
									$('#preca_'+id).val(val.preca);
									$('#precio1_'+id).val(val.base1);
									$('#precio2_'+id).val(val.base2);
									$('#precio3_'+id).val(val.base3);
									$('#precio4_'+id).val(val.base4);
									$('#itiva_'+id).val(val.iva);
									$('#sinvtipo_'+id).val(val.tipo);
									$('#sinvpeso_'+id).val(val.peso);
									$('#pond_'+id).val(val.pond);
									$('#ultimo_'+id).val(val.ultimo);
									$('#cana_'+id).val(val.cana);
									$('#combo_'+id).val(val.combo);

									$('#cana_'+id).attr('readonly','readonly');
									$('#codigoa_'+id).attr('readonly','readonly');
									$('#desca_'+id).attr('readonly','readonly');
									$('#preca_'+id).attr('readonly','readonly');
									post_modbus_sinv(id);
									id=add_sitems();
								}
							);
						},
				});
			}else{
				$('#codigoa_'+id).val(ui.item.codigo);
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
				post_modbus_sinv(Number(id));

			}

			ihtml  = '<table style="width:100%;border-collapse:collapse;padding:0px;background:#EDEDFD">';
			ihtml += '<tr><td align="right">TIPO:</tdt><td><b>'    +ui.item.tipo+   '</b></td></tr>';
			ihtml += '<tr><td align="right">EXISTEN:</tdt><td><b>' +ui.item.existen+'</b></td></tr>';
			ihtml += '<tr><td align="right">I.V.A.:</td><td><b>'   +ui.item.iva+    '</b></td></tr>';
			ihtml += '<tr><td align="right">MARCA:</td><td><b>'    +ui.item.marca+  '</b></td></tr>';
			ihtml += '<tr><td align="right">PESO:</td><td><b>'     +ui.item.peso+   '</b></td></tr>';
			ihtml += '<tr><td align="right">UBICA:</td><td><b>'    +ui.item.ubica+  '</b></td></tr>';
			ihtml += '<tr><td align="right">EMPAQUE:</td><td><b>'  +ui.item.unidad+  '</b></td></tr>';
			ihtml += '</table>';


			$('#informa').html(ihtml);

			setTimeout(function() {  $('#codigoa_'+id).removeAttr("readonly"); }, 1500);
		},
		open: function() { $('#codigoa_'+id).autocomplete("widget").width(420) }
	})
	.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		.append( "<a><table style='width:100%;border-collapse:collapse;padding:0px;'><tr><td colspan='6' style='font-size:14px;color:#0B0B61;'><b>" + item.descrip + "</b></td></tr><tr><td>Codigo:</td><td>" + item.codigo + "</td><td>Precio: </td><td><b>" + item.base1 + "</b></td><td>Existencia:</td><td>" + item.existen + "</td><td></td></tr></table></a>" )
		.appendTo( ul );
	};
}

function del_sitems(id){
	id = id.toString();
	var combo = $('#combo_'+id).val().trim();
	$('#tr_sitems_'+id).remove();
	if(combo!=""){
		var arr=$('input[name^="combo_"]');
		jQuery.each(arr, function() {
			nom=this.name;
			pos=this.name.lastIndexOf('_');
			if(pos>0){
				if(combo == this.value.trim()){
					ind = this.name.substring(pos+1);
					$('#tr_sitems_'+ind).remove();
				}
			}
		});
	}
	totalizar();
	var arr = $('input[id^="codigoa_"]');
	if(arr.length<=0){
		add_sitems();
	}
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

function chreferen(){
	var fp;
	var pagac;
	var total;
	fp = $("input[name='referen']:radio:checked").val();
	if( fp == 'M' ){
		fpaga('M');
	} else if ( fp == 'E' ) {
		pagac = $('#pagacon').val();
		total = $('#totalg').val();
		if ( pagac < total ) {
			$('#pagacon').val(total);
		}
		fpaga('E');
	} else {
		fpaga(fp);
	}
}

function apldes(){
	var descu=aplicadesc();
	if(descu > 0){
		if(confirm("Seguro desea quitar el descuento de "+descu+"%?")){
			$('#descuento').val('0');
			sclidescu = descu;
			totalizar();
		}
	}else if(descu==0 && sclidescu>0){
		if(confirm("Seguro desea aplicar el descuento de "+sclidescu+"%?")){
			$('#descuento').val(sclidescu);
			sclidescu = 0;
			totalizar();
		}
	}
}
</script>
<?php } ?>
<table align='center' width="98%" cellpadding='0' cellspacing='0'>
	<tr>
		<td align=right><?php echo $container_tr; ?><?php echo $form->pfac->output.$form->snte->output; ?></td>
	</tr>
</table>
<?php
	// Campos hidden
	echo $form->manual->output;
	echo $form->tipo_doc->output;
	echo $form->cajero->output;
	echo $form->nombre->output;
	echo $form->factura->output;
?>
<table style="width:100%;border-collapse:collapse;padding:0px;" >
	<tr>
		<td>
			<table style="width:100%;border-collapse:collapse;padding:0px;background:#EFEFEF;" border='0'>
				<tr>
					<td class="littletableheader" width='20px' style='background:#EFEFEF;'>
						<?php if($form->_status!='show'){ ?>
						<a href="#" title="Agregar nuevo cliente" onClick="scliadd();" ><?php echo image('add1-.png','Agregar nuevo cliente',array('title'=>'Agregar nuevo cliente')); ?></a>
						<?php } ?>
					</td>
					<td class="littletablerow"  style='width:45px;align;right'><?php echo $form->cliente->label; ?>*</td>
					<td class="littletablerow"  style='width:75px;' align='center'><?php echo $form->cliente->output,$form->sclitipo->output.$form->upago->output; ?></td>
					<td class="littletablerow"  style='background:#E0E6F8;'> <div id='nombre_val' style='font-weight:bold;white-space:nowrap;'><?php echo $form->nombre->value;  ?>&nbsp;</div><div id='direc_val' class='tooltip'><?php echo $form->direc->value.$form->direc->output ?></div></td>
					<td class="littletablerow"  style='width:25px;background:#E0E6F8;text-align:right;'>RIF:</td>
					<td class="littletablerow"  style='width:75px;background:#E0E6F8;'  ><b id='rifci_val'><?php echo $form->rifci->value; ?></b><?php echo $form->rifci->output;   ?>&nbsp;</td>
					<td class="littletablerow"  style='width:35px;background:#EFEFEF;'>Vende</td>
					<td class="littletablerow"  style='width:80px;background:#EAFAEA;'><?php echo $form->vd->output; ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<div id='efecha' style='display:none;'></div>
<table style="width:100%;border-collapse:collapse;padding:0px;">
	<tr>
		<td align='left'>
		<div id='ditems01' style='overflow:auto;border: 1px solid #0B3861;background: #FAFAFA;height:335px;width:630px;'>
		<table width='100%' border='0' cellpadding='0' cellspacing='0'>
			<tr id='__INPL__'>
				<td class="littletableheaderdet" style='background:#0B3861;'><b>C&oacute;digo</b></td>
				<td class="littletableheaderdet" style='background:#0B3861;'><b>Descripci&oacute;n</b></td>
				<td class="littletableheaderdet" style='background:#0B3861;'><b>Cant.</b></td>
				<td class="littletableheaderdet" style='background:#0B3861;'><b>Precio</b></td>
				<td class="littletableheaderdet" style='background:#0B3861;'><b>Importe</b></td>
				<?php if($form->_status!='show') {?>
					<td bgcolor='#0B3861'><a href='#' id='addlink' onclick="add_sitems()" title='Agregar otro articulo'><?php echo img(array('src' =>"images/agrega4.png", 'height' => 26, 'alt'=>'Agregar otro producto', 'title' => 'Agregar otro producto', 'border'=>'0')); ?></a></td>
				<?php } ?>
			</tr>
			<?php for($i=0;$i<$form->max_rel_count['sitems'];$i++) {
				$it_codigo  = "codigoa_${i}";
				$it_desca   = "desca_${i}";
				$it_cana    = "cana_${i}";
				$it_preca   = "preca_${i}";
				$it_importe = "tota_${i}";
				$it_iva     = "itiva_${i}";
				$it_peso    = "sinvpeso_${i}";
				$it_tipo    = "sinvtipo_${i}";
				$it_ultimo  = "ultimo_${i}";
				$it_detalle = "detalle_${i}";
				$it_pond    = "pond_${i}";
				$it_combo   = "combo_${i}";
				$pprecios='';
				for($o=1;$o<5;$o++){
					$it_obj   = "precio${o}_${i}";
					$pprecios.= $form->$it_obj->output;
				}
				$pprecios .= $form->$it_iva->output;
				$pprecios .= $form->$it_peso->output;
				$pprecios .= $form->$it_tipo->output;
				$pprecios .= $form->$it_combo->output;
			?>
			<tr id='tr_sitems_<?php echo $i; ?>' ondblclick="marcar(this)">
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
				<td class="littletablerow">
					<a href='#' title='Eliminar fila' onclick='del_sitems(<?php echo $i ?>);return false;'><?php echo img('images/delete.jpg'); ?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
			<tr id='__UTPL__'>
				<td id='cueca'></td>
			</tr>
		</table>
		</div>

		<div id='formapago' style='display:none;overflow:auto;background: #FAFAFA;height:280px;width:630px;'>
		<table style="border-collapse:collapse;padding:0px;border: 1px solid #0B3861;">
			<tr>
				<td>
				<div style='overflow:auto;background: #FAFAFA;height:190px;'>
				<table id='sfpatable' style="width:100%;border-collapse:collapse;padding:0px;">
					<tr><td class="littletableheaderdet" colspan='5' style='text-align:center;font-weight:bold;background:#0B3861;'>PAGOS MULTIPLES</td></tr>
					<tr id='__ITPL__sfpa'>
						<td class="littletableheaderdet">Tipo</td>
						<td class="littletableheaderdet">N&uacute;mero</td>
						<td class="littletableheaderdet">Banco</td>
						<td class="littletableheaderdet">Monto</td>
						<?php if($form->_status!='show') {?>
							<td class="littletableheaderdet"><a href='#' onclick="add_sfpa()" title='Agregar otro pago'><?php echo img('images/agrega4.png'); ?></a></td>
						<?php } ?>
					</tr>
					<?php
					for($i=0; $i < $form->max_rel_count['sfpa']; $i++) {
						$tipo     = "tipo_${i}";
						//$sfpafecha= "sfpafecha_${i}";
						$numref   = "numref_${i}";
						$monto    = "monto_${i}";
						$banco    = "banco_${i}";
					?>
					<tr id='tr_sfpa_<?php echo $i; ?>'>
						<td class="littletablerow" nowrap        ><?php echo $form->$tipo->output      ?></td>
						<!--td class="littletablerow" align="center"><?php //echo $form->$sfpafecha->output ?></td-->
						<td class="littletablerow"               ><?php echo $form->$numref->output    ?></td>
						<td class="littletablerow"               ><?php echo $form->$banco->output     ?></td>
						<td class="littletablerow" align="right" ><?php echo $form->$monto->output     ?></td>
						<?php if($form->_status!='show') {?>
							<td class="littletablerow"><a href='#' onclick="del_sfpa(<?php echo $i; ?>);return false;"><?php echo img('images/delete.jpg'); ?></a></td>
						<?php } ?>
					</tr>
					<?php } ?>
					<tr id='__UTPL__sfpa'>
						<td colspan='<?php echo ($form->_status!='show')? '6':'5';  ?>' class="littletableheaderdet"></td>
					</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<table style="width:100%;border-collapse:collapse;padding:0px;border-top:3px solid #0B3861;">
						<tr>
							<td style='border-right:1px solid #0B3861;' align='center'>
							<input name="bpagar" value="Cerrar" onclick="fpaga('M')" class="button" type="button"></td>
							<td class="littletableheader" valign='top'><?php echo $form->observa->label; ?>&nbsp;&nbsp;</td>
							<td class='littletablerow'    valign='top'><?php echo $form->observa->output; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</div>
		<div id='fpefectivo' style='display:none;overflow:auto;background: #FAFAFA;height:280px;width:630px;'>
		<table style="border-collapse:collapse;padding:0px;border: 1px solid #0B3861;">
			<tr><td class="littletableheaderdet" colspan='3' style='text-align:center;font-size:18px;font-weight:bold;background:#0B3861;'>PAGO EN EFECTIVO</td></tr>
			<tr>
				<td class="littletableheader" valign='top'><?php echo $form->pagacon->label; ?>&nbsp;</td>
				<td class='littletablerow'    valign='top'><?php echo $form->pagacon->output; ?></td>
				<td class='littletablerow'    valign='right'><div id='vuelto' style='font-size:16px;font-weight:bold;'>0.00</div></td>
			</tr>
			<tr>
				<td class="littletableheader" valign='top'><?php echo $form->observa->label;  ?>&nbsp;&nbsp;</td>
				<td class='littletablerow'    colspan='2' ><?php echo $form->observa->output; ?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align='right'></td>
			</tr>
		</table>
		</div>
		</td>
		<td valign='top'>
			<table style="width:100%;border-collapse:collapse;padding:0px;border: 1px outset #9AC8DA">
			<tr>
				<td colspan='2' style='text-align:center;font-size:18px;font-weight:bold;background:#0B3861;color:#FFF;'>FORMA DE PAGO</td>
			</tr><tr>
				<?php $referen=$form->referen->value; ?>
				<td><input name="referen" value="P" type="radio" onchange='chreferen()' <?php echo ($referen=='P' || empty($referen))? 'checked="checked"':''; ?>>Pendiente&nbsp;</td>
				<td><input name="referen" value="E" type="radio" onchange='chreferen()' <?php echo ($referen=='E')? 'checked="checked"':''; ?>>Efectivo&nbsp;</td>
			</tr><tr>
				<td><input name="referen" value="M" type="radio" onchange='chreferen()' <?php echo ($referen=='M')? 'checked="checked"':''; ?>>Multiple/Otros&nbsp;</td>
				<td><input name="referen" value="C" type="radio" onchange='chreferen()' <?php echo ($referen=='C')? 'checked="checked"':''; ?>>Credito&nbsp;</td>
			</tr>
		</table>
		<br>
		<table style="width:100%;border-collapse:collapse;padding:0px;border: 1px outset #9AC8DA">
			<tr>
				<td class="littletableheader"><b><?php echo $form->almacen->label;  ?></b></td>
				<td class="littletablerow"   ><?php echo $form->almacen->output; ?></td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->fecha->label;    ?></td>
				<td class="littletablerow"   ><?php echo $form->fecha->output;   ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->orden->label;    ?></td>
				<td class="littletablerow"   ><?php echo $form->orden->output;   ?></td>
			</tr>
		</table>
		<div id='informa'></div>
		</td>
	</tr><tr>
		<td>
		<div style='overflow:auto;border: 1px solid #0B3861;background: #FAFAFA;width:630px;'>
		<table style="width:100%;border-collapse:collapse;padding:0px;">
			<tr>
				<td class="littletablerow"    align='left'   style='background:#CFCFCF;width:30px;'><span><?php echo $form->descuento->label;  ?></span></td>
				<td class="littletablerow"    align='center' style='background:#CFCFCF;'><b id='descuentomon_val' ondblclick='apldes();' style='cursor: hand'></b><?php echo $form->descuento->output; ?></td>

				<td class="littletableheader" align='right'><?php echo $form->totals->label; ?></td>
				<td class="littletablerow"    align='right' style='font-size:16px;'><b id='totals_val'><?php echo nformat($form->totals->value); ?></b><?php echo $form->totals->output; ?></td>
				<td class="littletableheader" align='right'><?php echo $form->ivat->label;    ?></td>
				<td class="littletablerow"    align='right' style='font-size:16px;'><b id='ivat_val'><?php echo nformat($form->ivat->value); ?></b><?php echo $form->ivat->output; ?></td>
				<td class="littletableheader" align='right'><?php echo $form->totalg->label;  ?></td>
				<td class="littletablerow"    align='right' style='font-size:16px;'><b id='totalg_val'><?php echo nformat($form->totalg->value); ?></b><?php echo $form->totalg->output; ?></td>
			</tr>
		</table>
		</div>
		</td>
		<td>
		<table style="width:100%;border-collapse:collapse;padding:0px;">
			<td class="littletableheader" align='right' width='70px'></td>
			<td class="littletablerow"    align='right' ><b></b></td>
		</table>
		</td>
	</tr>
</table>

<?php echo $form_end; ?>

<?php
if($form->_status=='show'){
	$transac=$form->get_from_dataobjetct('transac');
	$canasmov = $this->datasis->dameval('SELECT COUNT(*) AS cana FROM smov WHERE transac='.$this->db->escape($transac));
	if($canasmov>0){
?>
<br>
<table  width="100%" style="margin:0;width:100%;" >
	<tr>
		<td colspan=10 class="littletableheader">Movimientos relacionados</td>
	</tr>
	<?php
	$sql[]='SELECT cod_cli, nombre,tipo_doc, numero, monto, observa1 FROM smov WHERE transac='.$this->db->escape($transac).' ORDER BY num_ref,cod_cli';
	foreach($sql as $mSQL){
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
	?>
	<tr>
		<td class="littletablerowth" ><?php echo $row->cod_cli.' '.$row->nombre;    ?></td>
		<td class="littletablerowth" align='center'><?php echo $row->tipo_doc; ?></td>
		<td class="littletablerow"   ><?php echo $row->numero;   ?></td>
		<td class="littletablerowth" ><?php echo $row->observa1; ?></td>
		<td class="littletablerow"   align='right'><?php echo nformat($row->monto);?></td>
	</tr>
	<?php
			}
		}
	}?>

</table>
<?php
	}
}

endif; ?>
