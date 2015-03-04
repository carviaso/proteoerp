<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/

$container_bl=(count($form->_button_container['BL'])>0)? $form->_button_container['BL'][0]:'';
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:
$aplrete=false;
$campos=$form->template_details('itscst');
$scampos  ='<tr id="tr_itscst_<#i#>"  ondblclick="marcar(this)">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" ><b id="it_descrip_val_<#i#>"></b>'.$campos['descrip']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['cantidad']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['costo']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['importe']['field'];
$scampos .= $campos['sinvpeso']['field'].$campos['iva']['field'].$campos['nentrega']['field'].'</td>';
//$scampos .='<td class="littletablerow" align="right">'.$campos['precio1']['field'].'</td>';
$scampos .='<td class="littletablerow"><a href=# onclick="del_itscst(<#i#>);return false;">'.img('images/delete.jpg').'</a></td></tr>';
$campos=$form->js_escape($scampos);

$ccampos=$form->detail_fields['gereten'];
$cgereten ='<tr id="tr_gereten_<#i#>">';
//$cgereten.=' <td class="littletablerow">'.join('</td><td align="right">',$ggereten).'</td>';
$cgereten.=' <td class="littletablerow" nowrap>       '.$ccampos['codigorete']['field'].'</td>';
$cgereten.=' <td class="littletablerow" align="right">'.$ccampos['base']['field']      .'</td>';
$cgereten.=' <td class="littletablerow" align="right">'.$ccampos['porcen']['field']    .'</td>';
$cgereten.=' <td class="littletablerow" align="right">'.$ccampos['monto']['field']     .'</td>';
$cgereten.=' <td class="littletablerow" align="center"><a href=\'#\' onclick="del_gereten(<#i#>);return false;">'.img('images/delete.jpg').'</a></td></tr>';
$cgereten=$form->js_escape($cgereten);

$cscstordc = $form->detail_fields['scstordc']['ordc']['field'];
$cscstordc = $form->js_escape($cscstordc);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
if($form->_status!='show'){
	$rete=array();
	$mSQL='SELECT TRIM(codigo) AS codigo,TRIM(CONCAT_WS("-",codigo,activida)) AS activida ,base1,tari1,pama1,TRIM(tipo) AS tipo FROM rete ORDER BY codigo';
	$query = $this->db->query($mSQL);
	if ($query->num_rows() > 0){
		foreach ($query->result() as $row){
			$ind='_'.$row->codigo;
			$rete[$ind]=array($row->activida,$row->base1,$row->tari1,$row->pama1,$row->tipo);
		}
	}
	$json_rete=json_encode($rete);

	$sql='SELECT TRIM(a.codbanc) AS codbanc,tbanco FROM banc AS a';
	$query = $this->db->query($sql);
	$comis=array();
	if ($query->num_rows() > 0){
		foreach ($query->result() as $row){
			$ind='_'.$row->codbanc;
			$comis[$ind]['tbanco']  =$row->tbanco;
		}
	}
	$json_comis=json_encode($comis);
	$contribu= $this->datasis->traevalor('CONTRIBUYENTE');
	$rif     = $this->datasis->traevalor('RIF');
?>
<script language="javascript" type="text/javascript">
var itscst_cont =<?php echo $form->max_rel_count['itscst']; ?>;
var tasa_general=<?php echo $alicuota['tasa'];     ?>;
var tasa_reducid=<?php echo $alicuota['redutasa']; ?>;
var tasa_adicion=<?php echo $alicuota['sobretasa'];?>;
var gereten_cont=<?php echo $form->max_rel_count['gereten'];?>;
var oaplrete    =<?php echo $form->aplrete->value; ?>;

var comis    = <?php echo $json_comis; ?>;
var rete     = <?php echo $json_rete;  ?>;
var ctimeout = -1;
$(function(){
	$(".inputnum").numeric(".");

	$("#fecha").datepicker({   dateFormat: "dd/mm/yy" });
	$("#vence").datepicker({   dateFormat: "dd/mm/yy" });
	$("#actuali").datepicker({ dateFormat: "dd/mm/yy" });

	totalizar();
	for(var i=0;i < <?php echo $form->max_rel_count['itscst']; ?>;i++){
		autocod(i.toString());
	}

	$("#montotot").focusout(function(){
		cmontotot();
	});

	$("#montoiva").focusout(function(){
		cmontoiva();
	});

	$('input[name^="cantidad_"]').keypress(function(e) {
		if(e.keyCode == 13) {
			var nom=this.name
			var pos=this.name.lastIndexOf('_');
			if(pos>0){
				var ind = this.name.substring(pos+1);
				$('#costo_'+ind).focus();
				$('#costo_'+ind).select();
			}
			return false;
		}
	});

	$('input[name^="costo_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itscst();
			return false;
		}
	});

	$('#proveed').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasprv'); ?>",
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
			$("#proveed").attr("readonly", "readonly");
			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);
			$('#proveed').val(ui.item.proveed);
			$('#sprvreteiva').val(ui.item.reteiva);
			setTimeout(function(){ $("#proveed").removeAttr("readonly"); }, 1500);
			$('#serie').change();
			post_modbus_sprv();
		}
	});

	$('#serie').change(function (){
		var proveed = $('#proveed').val();
		var numero  = $('#serie').val();
		var tipo    = $('#tipo_doc').val();

		if(numero!='' && proveed!='' && tipo!=''){
			$.ajax({
				url: "<?php echo site_url('ajax/scstdupli'); ?>",
				dataType: 'json',
				type: 'POST',
				data: {'proveed' : proveed, 'numero': numero, 'tipo_doc':tipo},
				success: function(data){
					if(data.status=='A'){
						$.prompt("<span style='font-size:1.5em'>Ya existe un registro con el mismo n&uacute;mero y el mismo proveedor de fecha <b>"+data.fecha+"</b>, contro <b>"+data.nfiscal+"</b> y monto <b>"+nformat(data.monto,2)+"</b></span>", {
							title: "Posible registro duplicado",
							buttons: { "Continuar": true }
						});
					}
				},
			});
		}
	});
	chtipodoc();

	$('#fafecta').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscascstdev'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term, "sprv":$('#proveed').val()},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#fafecta').val('');
							$('#aplrete').val(oaplrete);
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
			$('#fafecta').attr("readonly", "readonly");
			$('#fafecta').val(ui.item.value);
			$('#aplrete').val(ui.item.aplrete);

			if(ui.item.control!=''){
				$('#sprvreteiva').val(ui.item.reteiva);
				truncate();
				$("#tipo_doc").val('NC');
				$.ajax({
					url: "<?php echo site_url('ajax/buscaitscstdev'); ?>",
					dataType: 'json',
					type: 'POST',
					data: {'q':ui.item.control},
					success: function(data){
						$.each(data,
							function(id, val){
								add_itscst();
								$('#codigo_'+id).val(val.codigo);
								$('#descrip_'+id).val(val.descrip);
								$('#it_descrip_val_'+id).text(val.descrip);
								$('#iva_'+id).val(val.iva);
								$('#sinvpeso_'+id).val(val.peso);
								$('#costo_'+id).val(val.pond);
								$('#precio1_'+id).val(val.precio1);
								$('#cantidad_'+id).val(val.cana);

								nind=Number(id);
								post_modbus_sinv(nind);
							}
						);
					},
				});

				if(ui.item.msj != null){
					//$.prompt(ui.item.msj);
					alert(ui.item.msj);
				}
			}
			setTimeout(function(){ $("#fafecta").removeAttr("readonly"); }, 1500);
		}
	});

	pintane();
});

function pintane(){
	var arr=$('input[name^="nentrega_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			if(this.value!=''){
				ind = this.name.substring(pos+1);

				itnentrega= this.value.trim();
				if(itnentrega!=null){
					$('#codigo_'+ind).attr('readonly','readonly');
					$('#codigo_'+ind).attr('title'   ,'Nota de entrega control '+this.value);
					$('#codigo_'+ind).css('background','#95ACFE');
				}else{
					$('#codigo_'+ind).css('background','#FFFFFF');
				}

			}
		}
	});
}

function marcar(obj){
	var color = $(obj).css("background-color");
	if(color=='transparent'){
		$(obj).css("background-color", "#FFFF28");
	}else{
		$(obj).css("background-color", 'transparent');
	}
}

function chtipodoc(){
	var tipo=$('#tipo_doc').val();
	if(tipo=='NC'){
		$('#td_fafecta').show();
		$('#fafecta').attr('type','text');
	}else if(tipo=='FC'){
		$('#td_fafecta').hide();
		$('#fafecta').attr('type','hidden');
	}else{
		$('#td_fafecta').hide();
		$('#fafecta').attr('type','hidden');
	}
}

function importe(id){
	var ind     = id.toString();
	var cana    = Number($("#cantidad_"+ind).val());
	var precio  = Number($("#costo_"+ind).val());

	var iimporte= roundNumber(cana*precio,2);
	$("#importe_"+ind).val(iimporte);
	totalizar();
}

function costo(id){
	var ind     = id.toString();
	var cana    = Number($("#cantidad_"+ind).val());
	var importe = Number($("#importe_"+ind).val());
	if(cana>0){
		var precio  = roundNumber(importe/cana,2);
		$("#costo_"+ind).val(precio);
	}else{
		$("#importe_"+ind).val('0.0');
	}
	totalizar(1);
}

function buscarep(id,codigo){
	codigo=codigo.trim();
	var arr=$('input[name^="codigo_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			if(this.value!=''){
				ind     = this.name.substring(pos+1);
				if(ind!=id){
					itcodigo= this.value.trim();
					if(itcodigo==codigo){
						alert('El codigo introducido ya esta repetido ('+codigo+')');
						$('#codigo_'+ind).focus();
						$('#codigo_'+ind).select();
						$('#tr_itscst_'+id).css("background-color", "#FFFF28");
					}
				}
			}
		}
	});
}

function totalizar(taca){
	if(taca){
		var tolera = 0;
	}else{
		var tolera = 0.07;
	}
	var iva      =0;
	var totalg   =0;
	var itiva    =0;
	var itpeso   =0;
	var totals   =0;
	var importe  =0;
	var peso     =0;
	var cana     =0;
	var cexento  =0;
	var cgenera  =0;
	var civagen  =0;
	var creduci  =0;
	var civared  =0;
	var cadicio  =0;
	var civaadi  =0;
	var montotot =0;
	var montoiva =0;

	var arr=$('input[name^="importe_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			if(this.value!=''){
				ind     = this.name.substring(pos+1);
				cana    = Number($("#cantidad_"+ind).val());
				itiva   = Number($("#iva_"+ind).val());
				importe = Number(this.value);
				itpeso  = Number($("#sinvpeso_"+ind).val());

				peso    = peso+(itpeso*cana);
				iva     = importe*(itiva/100);
				totals  = totals+importe;

				if(itiva-tasa_general==0){
					cgenera = cgenera+importe;
					civagen = civagen+iva;
				}else if(itiva-tasa_reducid==0){
					creduci = creduci+importe;
					civared = civared+iva;
				}else if(itiva-tasa_adicion==0){
					cadicio = cadicio+importe;
					civaadi = civaadi+iva;
				}else{
					cexento = cexento+importe;
				}
			}
		}
	});

	civas=roundNumber((cgenera*tasa_general+creduci*tasa_reducid+cadicio*tasa_adicion)/100,2);
	montotot = Number($("#montotot").val());
	montoiva = Number($("#montoiva").val());
	porreten = Number($("#sprvreteiva").val());
	tipodoc  = $("#tipo_doc").val();

	$("#peso").val(roundNumber(peso,2));

	if(Math.abs(totals-montotot) >= tolera ){
		$("#montotot").val(roundNumber(totals,2));
	}else{
		totals = montotot;
	}
	if(Math.abs(civas-montoiva) >=tolera ){
		$("#montoiva").val(roundNumber(civas,2));
		montoiva = civas;
	}else{
		iva = montoiva;
	}

	<?php
	if($contribu=='ESPECIAL' && strtoupper($rif[0])!='V'){
		$aplrete=true;
		echo "\tvar aplrete=Number($('#aplrete').val());\n";
		echo "\tif(tipodoc=='FC' || (tipodoc=='NC' && aplrete>0)){ ";
		echo '$("#reteiva").val(roundNumber(montoiva*porreten/100,2));';
		echo '}';
	}
	?>

	$("#montonet").val(roundNumber(totals+civas,2));
	$("#peso_val").text(nformat(peso,2));
	$("#montonet_val").text(nformat(totals+civas,2));
	$("#montotot_val").text(nformat(totals,2));
}

//Calcula los montos que van a CxP
function ctotales(){
	var base=0;
	var impu=0;
	base += Number($("#cexento").val());
	base += Number($("#cgenera").val());
	base += Number($("#creduci").val());
	base += Number($("#cadicio").val());

	impu += Number($("#civaadi").val());
	impu += Number($("#civagen").val());
	impu += Number($("#civared").val());

	$("#cstotal").val(roundNumber(base,2));
	$("#ctotal").val(roundNumber(base+impu,2));
	$("#cimpuesto").val(roundNumber(impu,2));

	$("#cimpuesto_val").text(nformat(impu,2));
	$("#ctotal_val").text(nformat(base+impu,2));
	$("#cstotal_val").text(nformat(base,2));
}

function add_itscst(){
	var htm = <?php echo $campos; ?>;
	can = itscst_cont.toString();
	con = (itscst_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL__").after(htm);
	$("#cantidad_"+can).numeric(".");
	$("#cantidad_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    $('#costo_'+can).focus();
			$('#costo_'+can).select();
			return false;
		}
	});

	$("#costo_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itscst();
			return false;
		}
	});


	$("#costo_"+can).numeric(".");
	$("#importe_"+can).numeric(".");
	autocod(can);
	$('#codigo_'+can).focus();

	itscst_cont=itscst_cont+1;
	return can;
}

function post_modbus_sinv(nind){
	ind=nind.toString();
	var cana=Number($("#cantidad_"+ind).val());
	if(cana<=0) $("#cantidad_"+ind).val(1);
	$('#cantidad_'+ind).focus();
	$('#cantidad_'+ind).select();
	$('#it_descrip_val_'+ind).text($('#descrip_'+ind).val());
	buscarep(ind,$("#codigo_"+ind).val());
	importe(nind);
	totalizar();
}

function cmontotot(){
	//if(ctimeout > 0) clearTimeout(ctimeout);
	//ctimeout=setTimeout('timecmontotot();', 1000);
	timecmontotot();
}

function timecmontotot(){
	var totals   = 0;
	var vimporte = $("#montotot").val();
	var iva      = Number($("#montoiva").val());
	var arr      = $('input[name^="importe_"]');
	jQuery.each(arr, function() {
		totals  = totals+Number(this.value);
	});

	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			id  = Number(this.name.substring(pos+1));
			val = Number(this.value);
			part= val/totals;
			$(this).val(roundNumber(vimporte*part,2));
			costo(id);
		}
	});

	//$("#montonet_val").text(nformat(totals+iva,2));
	//$("#montonet").val(totals+iva);

}

function cmontoiva(){
	var totals = Number($("#montotot").val());
	var iva    = Number($("#montoiva").val());

	<?php
	if($aplrete){
		echo "var aplrete  = Number($('#aplrete').val());\n";
		echo 'var porreten = Number($("#sprvreteiva").val());'."\n";
		echo 'var tipodoc  = $("#tipo_doc").val();'."\n";

		echo "\tif(tipodoc=='FC' || (tipodoc=='NC' && aplrete>0)){ ";
		echo '$("#reteiva").val(roundNumber(iva*porreten/100,2));';
		echo '}';
	}
	?>

	//cmontotot();
	$("#montonet_val").text(nformat(totals+iva,2));
	$("#montonet").val(totals+iva);
}

function post_modbus_sprv(){
	$('#nombre_val').text($('#nombre').val());
	var tipo    = $('#tipo_doc').val();


	$.ajax({
		url: "<?php echo site_url('ajax/ajaxsanncprov'); ?>",
		dataType: 'json',
		type: 'POST',
		data: {'clipro' : $("#proveed").val()},
		success: function(data){
			if(data>0){
				alert("Proveedor presenta anticipos y/o NC por un monto de "+nformat(data,2)+" Bs.");
				//	$.prompt("<span style='font-size:1.5em'>Proveedor presenta anticipos y/o Notas de cr&eacute;dito por un monto de <b>"+nformat(data,2)+" Bs.</b></span>", {
				//		title: "Saldo por aplicar",
				//		buttons: { "Continuar": true }
				//	});
			}
		},
	});

	if(tipo=='FC'){
		$.ajax({
			url: "<?php echo site_url('ajax/traeordc'); ?>",
			dataType: 'json',
			type: 'POST',
			data: {'cod_prv' : $("#proveed").val()},
			success: function(data){
				if(data != null){
					var pant  = "<table id='tordc'></table>";

					var promt = $.prompt(pant, {
						position: { width: 450 },
						title: "El proveedor posee la siguiente lista de ordenes y notas de entrega",
						buttons: { "Continuar": true },
						submit: function(e,v,m,f){
							var srows = jQuery("#tordc").jqGrid('getGridParam','selarrrow');
							if(srows.length>0){
								var arr_num = [];
								for(var i=0;i < srows.length;i++){
									ret  = $("#tordc").getRowData(srows[i]);
									if(ret.id.search(/^OC_[0-9]+$/i) >= 0){
										arr_num.push(ret.numero);
									}
								}

								$.ajax({
									url: "<?php echo site_url('ajax/traeitordc'); ?>",
									dataType: 'json',
									type: 'POST',
									data: {'ids' : srows},
									success: function(dat){
										if(dat=== null) return false;
										if(dat.length>0){
											truncate();
											var can;
											var item;
											for(var i=0;i < dat.length;i++){
												id   = add_itscst();
												item = dat[i];

												$('#codigo_'+id).val(item.codigo);
												$('#descrip_'+id).val(item.descrip);
												$('#it_descrip_val_'+id).text(item.descrip);
												$('#iva_'+id).val(item.iva);
												$('#sinvpeso_'+id).val(item.peso);
												$('#costo_'+id).val(item.pond);
												$('#precio1_'+id).val(item.precio1);
												$('#cantidad_'+id).val(item.cantidad);
												if(item.control!=null){
													$('#nentrega_'+id).val(item.control);
												}

												importe(parseInt(id));
												if(item.activo=='N'){
													$('#tr_itscst_'+id).css("background-color","#FF7A46");
												}else{
													$('#tr_itscst_'+id).css("background-color", "transparent");
												}
											}

											var numero,ret,can;
											var htm = <?php echo $cscstordc; ?>;
											$('input[id^="ordc_"]').remove();
											for(var i=0;i < arr_num.length;i++){
												can = i.toString();
												html = htm.replace(/<#i#>/g,can);
												$("#divgereten").after(html);
												$("#ordc_"+can).val(arr_num[i]);
											}
											pintane();
										}
									},
								});
							}
						}
					});

					jQuery("#tordc").jqGrid({
						datatype: "local",
						height: 230,
						colNames:["id","Tipo","N&uacute;mero","Fecha", "Peso","Monto"],
						colModel:[
							{name:"id"     , index:"id"     , key: true, hidden: true },
							{name:"tipo"   , index:"tipo"   , width:30 , align:"center"},
							{name:"numero" , index:"numero" , width:80 , align:"center"},
							{name:"fecha"  , index:"fecha"  , width:70 , align:"center"},
							{name:"peso"   , index:"peso"   , width:90 , align:"right"  , sorttype:"float"},
							{name:"monto"  , index:"monto"  , width:100, align:"right"  , sorttype:"float"}
						],
						multiselect: true,
						caption: "Seleccione los efectos que desee importar",
						rowNum:20,
						afterInsertRow:
							function( rid, aData, rowe){
								if(aData.tipo == 'OC'){
									$(this).jqGrid( "setCell", rid, "tipo","", {color:"#FFFFFF", background:"#166D05" });
								}else{
									$(this).jqGrid( "setCell", rid, "tipo","", {color:"#FFFFFF", background:"#95ACFE" });
								}
							},
					});

					for(var i=0;i<data.length;i++){
						jQuery("#tordc").jqGrid('addRowData',data[i].id,data[i]);
					}

				}
			},
		});
	}

}

function del_itscst(id){
	id = id.toString();
	$('#tr_itscst_'+id).remove();
	totalizar();
}

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscascstart'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term,'sprv':$('#proveed').val()},
				success:
					function(data){
						var sugiere = [];

						if(data.length==0){
							$('#codigo_'+id).val("");
							$('#descrip_'+id).val("");
							$('#it_descrip_val_'+id).text("");
							$('#iva_'+id).val(0);
							$('#sinvpeso_'+id).val(0);
							$('#costo_'+id).val(0);
							$('#cantidad_'+id).val('');
							$('#precio1_'+id).val('');
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
			$('#codigo_'+id).attr("readonly", "readonly");

			var cana=Number($("#cantidad_"+id).val());
			$('#codigo_'+id).val(ui.item.codigo);
			$('#descrip_'+id).val(ui.item.descrip);
			$('#it_descrip_val_'+id).text(ui.item.descrip);
			$('#iva_'+id).val(ui.item.iva);
			$('#sinvpeso_'+id).val(ui.item.peso);
			$('#costo_'+id).val(ui.item.pond);
			$('#precio1_'+id).val(ui.item.precio1);
			if(cana<=0) $("#cantidad_"+id).val('1');
			$('#cantidad_'+id).focus();
			$('#cantidad_'+id).select();
			//post_modbus_sinv(parseInt(id));
			importe(parseInt(id));
			//totalizar();
			if(ui.item.activo=='N'){
				$('#tr_itscst_'+id).css("background-color","#FF7A46");
			}else{
				$('#tr_itscst_'+id).css("background-color", "transparent");
			}

			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
			buscarep(id,ui.item.codigo);
			codesta(ui.item.codigo);
		}
	});
}

function codesta(mcodigo){
	$.post( "<?php echo site_url('ajax/codesta'); ?>/", {mCOD: mcodigo})
	.done( function( data ) { $( "#idcodesta" ).html( data );
	});
}

function truncate(){
	$('tr[id^="tr_itscst_"]').remove();
	itscst_cont=0;
}

//retencion
function importerete(nind){
	var ind=nind.toString();
	var codigo  = $("#codigorete_"+ind).val();
	if(codigo.length>0){
		//var tari1   = Number($("#porcen_"+ind).val());
		var importe = Number($("#base_"+ind).val());
		var base1   = Number(eval('rete._'+codigo+'[1]'));
		var tari1   = Number(eval('rete._'+codigo+'[2]'));
		var pama1   = Number(eval('rete._'+codigo+'[3]'));

		var tt=codigo.substring(0,1);
		if(tt=='1')
			monto=(importe*base1*tari1)/10000;
		else if(importe>pama1)
			monto=((importe-pama1)*base1*tari1)/10000;
		else
			monto = 0;

		$("#monto_"+ind).val(roundNumber(monto,2));
		$("#monto_"+ind+'_val').text(nformat(monto,2));
	}
	totalizar();
}

function totalrete(){
	monto=0;
	arr  =$('input[name^="monto_"]');
	jQuery.each(arr, function() {
		monto=monto+Number(this.value);
	});
	$("#reten").val(monto);
	$("#reten_val").text(nformat(monto,2));
	return monto;
}

function post_codigoreteselec(nind,cod){
	var ind=nind.toString();
	var porcen=eval('rete._'+cod+'[2]');
	var base1 =eval('rete._'+cod+'[1]');
	$("#porcen_"+ind).val(porcen);
	$("#porcen_"+ind+"_val").text(nformat(porcen,2));
	importerete(nind);
}

function add_gereten(){
	var htm = <?php echo $cgereten; ?>;
	var can = gereten_cont.toString();
	var con = (gereten_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PTPL__gereten").after(htm);
	gereten_cont=gereten_cont+1;
}

function del_gereten(id){
	id = id.toString();
	obj='#tr_gereten_'+id;
	$(obj).remove();
	totalizar();
}

function truncate_gereten(){
	$('tr[id^="tr_gereten_"]').remove();
	gereten_cont=0;
}
//fin de retenciones
</script>
<?php }
echo $form->aplrete->output;
?>

<table width='100%' align='center'>
<?php
$nana='NONO';
if (!$solo){
?>
	<tr>
		<td align="right">
			<?php echo $container_tr; ?>
		</td>
	</tr>
<?php
}
?>
	<tr>
		<td>
			<fieldset  style='border: 1px outset #FEB404;background:#FFFCE8;'>
			<table style="margin:0;width:100%;" cellspacing='0' cellpadding='0' border='0'>
				<tr>
					<td style='border-right: 1px solid grey;width:210px;'>
						<table width='100%' border='0'>
							<tr>
								<td class="littletablerowth" ><?php echo $form->tipo->label  ?></td>
								<td class="littletablerow" align='left' ><?php echo $form->tipo->output   ?></td>
							</tr><tr>
								<td class="littletablerowth"><?php echo $form->almacen->label  ?>*</td>
								<td class="littletablerow">  <?php echo $form->almacen->output ?></td>
							</tr>
						</table>
					</td>
					<td style='border-left: 1px solid grey;'>
						<table width='100%' border='0'>
							<tr>
								<td class="littletablerowth" align='right'><?php echo $form->proveed->label ?>*</td>
								<td  colspan='3' class="littletablerow">
									<?php echo $form->proveed->output ?>
									<b id='nombre_val'><?php echo $form->nombre->value ?></b>
									<?php echo $form->nombre->output.$form->sprvreteiva->output ?></td>
								<td class="littletablerowth"><?php echo $form->fecha->label  ?></td>
								<td class="littletablerow">  <?php echo $form->fecha->output ?></td>
							</tr><tr>
								<td class="littletablerowth" align='right'><?php echo $form->serie->label  ?></td>
								<td class="littletablerow">  <?php echo $form->serie->output ?></td>
								<td class="littletablerowth"><?php echo $form->cfis->label  ?>*</td>
								<td class="littletablerow">  <?php echo $form->cfis->output ?></td>
								<td class="littletablerowth"><?php echo $form->vence->label ?></td>
								<td class="littletablerow">  <?php echo $form->vence->output ?></td>
							</tr>
						</table>
					</td>


					<!--td style='border: 1px solid grey;'>
						<table width='100%'>

								<td class="littletablerowth" align='right' id='td_fafecta'><?php echo $form->fafecta->label ?>*</td>
								<td class="littletablerow"   align='right'><?php echo $form->fafecta->output ?></td>


							<tr>
								<td class="littletablerowth"><?php echo $form->serie->label  ?></td>
								<td class="littletablerow">  <?php echo $form->serie->output ?></td>
							</tr><tr>
								<td class="littletablerowth"><?php echo $form->cfis->label  ?>*</td>
								<td class="littletablerow">  <?php echo $form->cfis->output ?></td>
							</tr>
						</table>
					</td><td style='border: 1px solid grey;'>
						<table width='100%'>
							<tr>
								<td class="littletablerowth"><?php echo $form->fecha->label  ?></td>
								<td class="littletablerow">  <?php echo $form->fecha->output ?></td>
							</tr><tr>
								<td class="littletablerowth"><?php echo $form->vence->label ?></td>
								<td class="littletablerow">  <?php echo $form->vence->output ?></td>

							<!--/tr><tr>
								<td class="littletablerowth"><?php echo $form->actuali->label  ?></td>
								<td class="littletablerow">  <?php echo $form->actuali->output ?></td-->
							</tr>
						</table>
					</td-->
				</tr>
			</table>
			</fieldset>
		</tr>
	<tr>
</table>

<table width='100%'>
	<tr><td style="width:680px;">
<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:200px;width:680px'>
<table width='100%'>
	<tr id='__INPL__'>
		<th bgcolor='#7098D0'>C&oacute;digo     </th>
		<th bgcolor='#7098D0'>Descripci&oacute;n</th>
		<th bgcolor='#7098D0'>Cantidad          </th>
		<th bgcolor='#7098D0'>Precio            </th>
		<th bgcolor='#7098D0'>Importe           </th>
		<!-- <th bgcolor='#7098D0'>PVP               </th> -->
		<?php if($form->_status!='show') {?>
			<th bgcolor='#7098D0'>&nbsp;</th>
		<?php } ?>
	</tr>

	<?php for($i=0;$i<$form->max_rel_count['itscst'];$i++) {
		$it_codigo  = "codigo_${i}";
		$it_desca   = "descrip_${i}";
		$it_cana    = "cantidad_${i}";
		$it_precio  = "costo_${i}";
		$it_importe = "importe_${i}";
		$it_peso    = "sinvpeso_${i}";
		$it_iva     = "iva_${i}";
		$it_pvp     = "precio1_${i}";
		$it_nentrega= "nentrega_${i}";
	?>

	<tr id='tr_itscst_<?php echo $i; ?>'  ondblclick="marcar(this)">
		<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output; ?></td>
		<td class="littletablerow" align="left" ><b id='it_descrip_val_<?php echo $i; ?>'><?php echo $form->$it_desca->value; ?></b>
		<?php echo $form->$it_desca->output;  ?>
		</td>
		<td class="littletablerow" align="right"><?php echo $form->$it_cana->output;   ?></td>
		<td class="littletablerow" align="right"><?php echo $form->$it_precio->output; ?></td>
		<td class="littletablerow" align="right"><?php echo $form->$it_importe->output; ?>
		<?php echo $form->$it_peso->output.$form->$it_iva->output.$form->$it_nentrega->output; ?>
		</td>
		<?php if($form->_status!='show') {?>
		<td class="littletablerow">
			<a href='#' onclick='del_itscst(<?php echo $i ?>);return false;'><?php echo img('images/delete.jpg');?></a>
		</td>
		<?php } ?>
	</tr>
	<?php } ?>
</table>
</div>

<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;width:680px'>
<table style="margin:0;width:100%;" border='0'>
	<tr>
		<td width="100" class="littletablerowth" align='left'><?php echo $container_bl ?></td>
		<?php /*<td width="131" class="littletablerowth" align='right'><?php echo $form->rislr->label;     ?></td>*/?>
		<td width="10" class="littletablerow"   align='right'><?php echo $form->rislr->output;    ?></td>
		<td class="littletablerowth" align='right'><?php echo $form->riva->label;     ?></td>
		<td class="littletablerow"   align='left' ><?php echo $form->riva->output;     ?></td>
		<td width="111" class="littletablerowth" align='right'><?php echo $form->montotot->label;  ?></td>
		<td width="139" class="littletablerow"   align='right'><?php echo $form->montotot->output; ?></td>
	</tr><tr>
		<!--td class="littletableheader" width="100"  rowspan='2'><?php echo $form->observa1->label;    ?></td-->
		<td colspan='4' rowspan='2'><?php echo $form->observa1->output;   ?><?php echo $form->observa2->output;   ?><?php echo $form->observa3->output;?></td>
		<td class="littletablerowth" align='right'><?php echo $form->montoiva->label;  ?></td>
		<td class="littletablerow"   align='right'><?php echo $form->montoiva->output; ?></td>
	</tr><tr>
		<td class="littletablerowth" align='right'><?php echo $form->montonet->label; ?></td>
		<td class="littletablerow"   align='right'><b id='montonet_val' style='font-size:18px;font-weight: bold' ><?php echo nformat($form->montonet->value); ?></b><?php echo $form->montonet->output; ?></td>
	</tr>
</table>
</div>

<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:75px;width:680px;' id='divgereten'>
<table width='100%'>
	<tr id='__PTPL__gereten'>
		<td class="littletableheaderdet">Retenci&oacute;n ISLR</td>
		<td class="littletableheaderdet">Base</td>
		<td class="littletableheaderdet" align="right">%</td>
		<td class="littletableheaderdet" align="right">Monto</td>
		<?php if($form->_status!='show') {?>
			<td class="littletableheaderdet" align='center'><a href='#' onclick="add_gereten()" title='Agregar otro concepto'><?php echo img(array('src' =>"images/agrega4.png", 'height' => 16, 'alt'=>'Agregar otro concepto', 'title' => 'Agregar otro concepto', 'border'=>'0')); ?></a></td>
		<?php } ?>
	</tr>
	<?php for($i=0; $i < $form->max_rel_count['gereten']; $i++) {
		$it_codigorete= "codigorete_$i";
		//$it_actividad = "actividad_$i";
		$it_base      = "base_$i";
		$it_porcen    = "porcen_$i";
		$it_monto     = "monto_$i";
	?>
	<tr id='tr_gereten_<?php echo $i; ?>'>
		<td class="littletablerow" nowrap><?php echo $form->$it_codigorete->output ?></td>
		<td class="littletablerow" align="right"><?php echo $form->$it_base->output      ?></td>
		<td class="littletablerow" align="right"><?php echo $form->$it_porcen->output    ?></td>
		<td class="littletablerow" align="right"><?php echo $form->$it_monto->output     ?></td>
		<?php if($form->_status!='show') {?>
			<td class="littletablerow" align="center"><a href='#' onclick='del_gereten(<?php echo $i; ?>);return false;'><?php echo img("images/delete.jpg"); ?></a></td>
		<?php }
	}?>
	</tr>
</table>
</div>
</td><td valign='top'>
	<fieldset  style='border: 1px outset #FEB404;background:#FFFCE8;'>
	<table style="width:100%">
		<tr>
			<td class="littletablerowth" align='left' id='td_fafecta'><?php echo $form->fafecta->label ?></td>
			<tr></tr>
			<td class="littletablerow"   align='left' id='td_fafecta'><?php echo $form->fafecta->output ?></td>
		</tr>
	</table>
	</fieldset>
	<div id='idcodesta'></div>
</td>
</tr>
</table>
<?php
for($i=0;$i<$form->max_rel_count['scstordc'];$i++){
	$it_ordc  = "ordc_${i}";
	echo $form->$it_ordc->output;
}
echo $form_end?>
<?php endif; ?>
