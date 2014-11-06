<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	$meco = $form->output;
	$meco = str_replace('class="tablerow"','class="tablerow" style="font-size:20px; align:center;" ',$meco);
	echo $meco;
	//echo $meco."</td><td align='center'>".img("images/borrar.jpg");
else:

$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);

if ($form->_status=='modify'){
	$container_co=join('&nbsp;', $form->_button_status[$form->_status]['CO']);
	$container_it=join('&nbsp;', $form->_button_status[$form->_status]['IT']);
	$container_la=join('&nbsp;', $form->_button_status[$form->_status]['LA']);
}elseif ($form->_status=='create'){
	$container_co=join('&nbsp;', $form->_button_status[$form->_status]['CO']);
	$container_it=join('&nbsp;', $form->_button_status[$form->_status]['IT']);
	$container_la=join('&nbsp;', $form->_button_status[$form->_status]['LA']);
}else{
	$container_co = $container_it = $container_la = '';
}

echo $form_begin;

$campos=$form->template_details('sinvcombo');
$scampos  ='<tr id="tr_sinvcombo_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itcodigo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['itdescrip']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itcantidad']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itprecio']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itultimo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['itpond']['field'];
$ocultos=array('precio1','formcal');
foreach($ocultos as $obj){
	$obj2='it'.$obj;
	$scampos.=$campos[$obj2]['field'];
}
$scampos .= '</td>';
$scampos .= '<td class="littletablerow"  align="center"><a href=# onclick="del_sinvcombo(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

$campos2   =$form->template_details('sinvpitem');
$scampos2  ='<tr id="tr_sinvpitem_<#i#>">';
$scampos2 .='<td class="littletablerow" align="left" >'.$campos2['it2codigo']['field'].'</td>';
$scampos2 .='<td class="littletablerow" align="left" >'.$campos2['it2descrip']['field'].'</td>';
$scampos2 .='<td class="littletablerow" align="right">'.$campos2['it2cantidad']['field'].'</td>';
$scampos2 .='<td class="littletablerow" align="left" >'.$campos2['itunidad']['field'].'</td>';
$scampos2 .='<td class="littletablerow" align="right">'.$campos2['itfactor']['field'].'</td>';
$scampos2 .='<td class="littletablerow" align="right">'.$campos2['it2merma']['field'];
$ocultos2=array('ultimo','pond','formcal','id_sinv');
foreach($ocultos2 as $obj){
	$obj2='it2'.$obj;
	$scampos2.=$campos2[$obj2]['field'];
}
$scampos2 .='</td>';
$scampos2 .='<td class="littletablerow"  align="center"><a href=# onclick="del_sinvpitem(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos2=$form->js_escape($scampos2);


$campos3   =$form->template_details('sinvplabor');
$scampos3  ='<tr id="tr_sinvplabor_<#i#>">';
$scampos3 .='<td class="littletablerow" align="left" >'.$campos3['it3estacion']['field'].'</td>';
$scampos3 .='<td class="littletablerow" align="left" >'.$campos3['it3actividad']['field'].'</td>';
$scampos3 .='<td class="littletablerow" align="right">'.$campos3['it3tunidad']['field'].'</td>';
$scampos3 .='<td class="littletablerow" align="right">'.$campos3['it3tiempo']['field'].'</td>';
$scampos3 .='<td class="littletablerow"  align="center"><a href=# onclick="del_sinvplabor(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos3 = $form->js_escape($scampos3);

$link  = site_url('inventario/common/add_marc');
$link4 = site_url('inventario/common/get_marca');
$link5 = site_url('inventario/common/add_unidad');
$link6 = site_url('inventario/common/get_unidad');
$link7 = site_url('inventario/sinv/ultimo');
$link8 = site_url('inventario/sinv/sugerir');
$link9 = site_url('inventario/common/add_depto');
$link10= site_url('inventario/common/get_depto');
$link11= site_url('inventario/common/add_linea');
$link12= site_url('inventario/common/get_linea');
$link13= site_url('inventario/common/add_grupo');
$link14= site_url('inventario/common/get_grupo');
?>
<style >
.ui-autocomplete {max-height: 150px;overflow-y: auto;max-width: 600px;}
html.ui-autocomplete {height: 150px;width: 600px;}
#maintabcontainer ul { font-size: 9px; }
</style>

<?php if($form->_status!='show'){ ?>
<script language="javascript" type="text/javascript">
sinvcombo_cont =<?php echo $form->max_rel_count['sinvcombo'];  ?>;
sinvpitem_cont =<?php echo $form->max_rel_count['sinvpitem'];  ?>;
sinvplabor_cont=<?php echo $form->max_rel_count['sinvplabor']; ?>;

function submitkardex() {
	window.open('', "kpopup", "width=800,height=600,resizeable,scrollbars");
	document.kardex.submit();
}

function chtipo(){
	tipo=$("#tipo").val().substr(0,1);

	if(tipo=='C'){
		$("#litab7").show();
	}else{
		$("#litab7").hide();
		$("#tab7").hide();
	}

	if(tipo=='F'){
		$("#base1").attr("readonly", "readonly");
		$("#base2").attr("readonly", "readonly");
		$("#base3").attr("readonly", "readonly");
		$("#base4").attr("readonly", "readonly");
		$("#margen1").attr("readonly", "readonly");
		$("#margen2").attr("readonly", "readonly");
		$("#margen3").attr("readonly", "readonly");
		$("#margen4").attr("readonly", "readonly");
		$("#precio1").attr("readonly", "readonly");
		$("#precio2").attr("readonly", "readonly");
		$("#precio3").attr("readonly", "readonly");
		$("#precio4").attr("readonly", "readonly");
		$("#pond").attr("readonly", "readonly");
		$("#ultimo").attr("readonly", "readonly");
		post_enlace();
	}else{
		$("#base1").removeAttr("readonly");
		$("#base2").removeAttr("readonly");
		$("#base3").removeAttr("readonly");
		$("#base4").removeAttr("readonly");
		$("#margen1").removeAttr("readonly");
		$("#margen2").removeAttr("readonly");
		$("#margen3").removeAttr("readonly");
		$("#margen4").removeAttr("readonly");
		$("#precio1").removeAttr("readonly");
		$("#precio2").removeAttr("readonly");
		$("#precio3").removeAttr("readonly");
		$("#precio4").removeAttr("readonly");
		$("#pond").removeAttr("readonly");
		$("#ultimo").removeAttr("readonly");
	}
}

$(function(){

	$('.inputnum').focus(function (){ $(this).select(); });
	$('.inputnum').click(function (){ $(this).select(); });

	$("#fdesde").datepicker({ dateFormat: "dd/mm/yy" });
	$("#fhasta").datepicker({ dateFormat: "dd/mm/yy" });
	$('#maintabcontainer').tabs();
	$(".inputnum").numeric(".");

	tipo=$("#tipo").val().substr(0,1);
	if(tipo=='C'){
		totalizarcombo();
	}
	for(var i=0;i < <?php echo $form->max_rel_count['sinvpitem']; ?>;i++){
		autocodpitem(i.toString());
	}
	$('input[name^="itcantidad_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_sinvcombo();
			return false;
		}
	});

	$('input[name^="it2cantidad_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_sinvpitem();
			return false;
		}
	});

	$("#tipo").change(function(){
		chtipo();
	});

	$("#aumento").keyup(function(){
		var tipo  = $("#tipo").val().substr(0,1);
		if(tipo=='F'){
			post_enlace();
		}
	});

	$("#fracci").keyup(function(){
		var tipo  = $("#tipo").val().substr(0,1);
		if(tipo=='F'){
			post_enlace();
		}
	});

	$("#depto").change(function(){dpto_change(); });
	$("#linea").change(function(){ $.post('<?php echo $link14 ?>',{ linea:$(this).val() },function(data){$("#grupo").html(data);}) });
	$("#tdecimal").change(function(){
		var clase;
		if($(this).attr("value")=="S") clase="inputnum"; else clase="inputonlynum";
		$("#exmin").unbind();$("#exmin").removeClass(); $("#exmin").addClass(clase);
		$("#exmax").unbind();$("#exmax").removeClass(); $("#exmax").addClass(clase);
		$("#exord").unbind();$("#exord").removeClass(); $("#exord").addClass(clase);
		$("#exdes").unbind();$("#exdes").removeClass(); $("#exdes").addClass(clase);
		$(".inputnum").numeric(".");
		$(".inputonlynum").numeric("0");
	});
	cambioprecio('S');

	$('#enlace').autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinvart'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#cdescrip').val('');
							$('#cdescrip_val').text('');
							$('#cbase1').val('');
							$('#cbase2').val('');
							$('#cbase3').val('');
							$('#cbase4').val('');
							$('#cultimo').val('');
							$('#cformcal').val('');
							$('#cpond').val('');
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
			$("#enlace").attr("readonly", "readonly");
			iva = Number(ui.item.iva);
			$('#cdescrip').val(ui.item.descrip);
			$('#cdescrip_val').text(ui.item.descrip);
			$('#cbase1').val(ui.item.base1);
			$('#cbase2').val(ui.item.base2);
			$('#cbase3').val(ui.item.base3);
			$('#cbase4').val(ui.item.base4);
			$('#cultimo').val(ui.item.ultimo);
			$('#cpond').val(ui.item.pond);
			$('#cformcal').val(ui.item.formcal);
			$('#iva').val(ui.item.iva);

			post_enlace();
			setTimeout(function() {  $("#enlace").removeAttr("readonly"); }, 1500);
		}
	});
	for(var i=0;i < sinvcombo_cont;i++){
		autocod(i.toString());
	}
	chtipo();


	$('#ubica').autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscaubica'); ?>",
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
		minLength: 0,
		select: function( event, ui ) {
			$("#ubica").attr("readonly", "readonly");
			$('#ubica').val(ui.item.value);
			setTimeout(function() {  $("#ubica").removeAttr("readonly"); }, 1500);
		}
	});

	$("#linfe").change(function(){
		var val=$(this).val();
		if(val=='S'){
			$("#lindia").show();
		}else{
			$("#lindia").hide();
		}
	});

	$("#linfe").change();

});

function post_enlace(){
	var aumento = Number($('#aumento').val());
	var fracci  = Number($('#fracci').val());
	var pond    = Number($('#cpond').val());
	var ultimo  = Number($('#cultimo').val());
	var base1   = Number($('#cbase1').val());
	var base2   = Number($('#cbase2').val());
	var base3   = Number($('#cbase3').val());
	var base4   = Number($('#cbase4').val());

	if(fracci<=0){
		fracci=1;
		$('#fracci').val('1');
	}

	$('#base1').val(roundNumber(base1*(1+(aumento/100))/fracci,2));
	$('#base2').val(roundNumber(base2*(1+(aumento/100))/fracci,2));
	$('#base3').val(roundNumber(base3*(1+(aumento/100))/fracci,2));
	$('#base4').val(roundNumber(base4*(1+(aumento/100))/fracci,2));
	$('#pond').val(roundNumber(pond/fracci,2));
    $('#ultimo').val(roundNumber(ultimo/fracci,2));
	cambiobase('S');
}

function totalizarcombo(){
	var tpond=tultimo=tprecio=cont=0;
	var arr=$('input[name^="itcantidad_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			codigo  = $('itcodigo_'+ind).val();
			if(codigo!=''){
				precio  = Number($("#itprecio_"+ind).val());
				cana    = Number($("#itcantidad_"+ind).val());
				pond    = Number($("#itpond_"+ind).val());
				ultimo  = Number($("#itultimo_"+ind).val());
				tpond   = tpond   +cana*pond;
				tultimo = tultimo +cana*ultimo;
				tprecio = tprecio +cana*precio;
				cont    = cont+1;
			}
		}
	});

	if(cont>0){
		$("#base1").val(roundNumber(tprecio,2));
		$("#base2").val(roundNumber(tprecio,2));
		$("#base3").val(roundNumber(tprecio,2));
		$("#base4").val(roundNumber(tprecio,2));
		$("#pond").val(roundNumber(tpond,2));
		$("#ultimo").val(roundNumber(tultimo,2));
		cambiobase('S');
	}
}

function add_sinvcombo(){
	var htm = <?php echo $campos; ?>;
	can = sinvcombo_cont.toString();
	con = (sinvcombo_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL_SINVCOMBO__").after(htm);
	$("#itcantidad_"+can).numeric(".");
	autocod(can);
	$('#itcodigo_'+can).focus();
	$("#itcantidad_"+can).keypress(function(e) {
		if(e.keyCode == 13){
		    add_sinvcombo();
			return false;
		}
	});
	sinvcombo_cont=sinvcombo_cont+1;
}

function post_modbus_sinv(nind){
	ind=nind.toString();

	$("#itprecio_"+ind).empty();
	var arr=$('#itprecio_'+ind);

	descrip=$("#itdescrip_"+ind).val();
	$("#itdescrip_"+ind+'_val').text(descrip);

	descrip=$("#itultimo_"+ind).val();
	$("#itultimo_"+ind+'_val').text(descrip);

	descrip=$("#itpond_"+ind).val();
	$("#itpond_"+ind+'_val').text(descrip);

	totalizarcombo();
}

function del_sinvcombo(id){
	id = id.toString();
	$('#tr_sinvcombo_'+id).remove();
	totalizarcombo();
}

//Agrega el autocomplete del combo
function autocod(id){
	$('#itcodigo_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		open: function(){
			setTimeout(function() {$('.ui-autocomplete').css('z-index', 99999999999999); }, 0);
		},
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinv2'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#itcodigo_'+id).val('');
							$('#itdescrip_'+id).val('');
							$('#itprecio_'+id).val('');
							$('#itpond_'+id).val('');
							$('#itultimo_'+id).val('');
							$('#itformcal_'+id).val('');
							$('#itcantidad_'+id).val('');
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
		minLength: 1,
		select: function( event, ui ) {
			$('#itcodigo_'+id).attr("readonly", "readonly");

			$('#itcodigo_'+id).val(ui.item.codigo);
			$('#itdescrip_'+id).val(ui.item.descrip);
			$('#itprecio_'+id).val(ui.item.base1);
			$('#itpond_'+id).val(ui.item.pond);
			$('#itultimo_'+id).val(ui.item.ultimo);
			$('#itformcal_'+id).val(ui.item.formcal);
			$('#itcantidad_'+id).val('1');
			$('#itcantidad_'+id).focus();
			$('#itcantidad_'+id).select();
			post_modbus_sinv(id);
			totalizarcombo();

			setTimeout(function(){  $('#itcodigo_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}

function add_sinvpitem(){
	var htm = <?php echo $campos2; ?>;
	can = sinvpitem_cont.toString();
	con = (sinvpitem_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL_SINVPITEM__").after(htm);
	$("#it2cantidad_"+can).numeric(".");
	$("#it2merma_"+can).numeric(".");
	autocodpitem(can);
	$('#it2codigo_'+can).focus();
	$("#it2cantidad_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_sinvpitem();
			return false;
		}
	});
	sinvpitem_cont=sinvpitem_cont+1;
}

function del_sinvpitem(id){
	id = id.toString();
	$('#tr_sinvpitem_'+id).remove();
	totalizarpitem();
}

function totalizarpitem(){
	var tota   = 0;
	var arr=$('input[name^="it2cantidad_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			cana    = Number($("#it2cantidad_"+ind).val());
			pond    = Number($("#it2pond_"+ind).val());
			ultimo  = Number($("#it2ultimo_"+ind).val());
			formcal = $("#it2formcal_"+ind).val();
			tp      = Math.round(cana * pond  *100)/100;
			tu      = Math.round(cana * ultimo*100)/100;
			//alert(cana+':'+pond+':'+ultimo+':'+formcal);
			switch(formcal){
			case 'P': t=tp;
			break;
			case 'U': t=tu;
			break;
			case 'M':{if(tp>tu)
				t=tp;
				else
				t=tu;}
			break;
			default: t=tu;
			}

			tota=tota+t;
		}
	});
	tota=roundNumber(tota,2);
}

function autocodpitem(id){
	$('#it2codigo_'+id).autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinv2'); ?>",
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
		autoFocus: true,
		select: function( event, ui ){
			$('#it2codigo_'+id).val(ui.item.codigo);
			$('#it2descrip_'+id).val(ui.item.descrip);
			$('#it2pond_'+id).val(ui.item.pond);
			$('#it2ultimo_'+id).val(ui.item.ultimo);
			$('#it2formcal_'+id).val(ui.item.formcal);
			$('#it2id_sinv_'+id).val(ui.item.id);

			$('#it2cantidad_'+id).val('1');
			$('#it2cantidad_'+id).focus();
			$('#it2cantidad_'+id).select();
			post_modbus_sinvpitem(id);
			totalizarpitem();
		}
	});
}

function post_modbus_sinvpitem(nind){
	ind=nind.toString();

	$("#it2precio_"+ind).empty();
	var arr=$('#it2precio_'+ind);

	descrip=$("#it2descrip_"+ind).val();
	$("#it2descrip_"+ind+'_val').text(descrip);

	descrip=$("#it2ultimo_"+ind).val();
	$("#it2ultimo_"+ind+'_val').text(descrip);

	descrip=$("#it2pond_"+ind).val();
	$("#it2pond_"+ind+'_val').text(descrip);

	descrip=$("#it2formcal_"+ind).val();
	$("#it2formcal_"+ind+'_val').text(descrip);

	totalizarpitem();
}

function truncatecombo(){
	$('tr[id^="tr_sinvcombo_"]').remove();
	sinvcombo_cont=0;
}

function add_sinvplabor(){
	var htm = <?php echo $campos3; ?>;
	can = sinvplabor_cont.toString();
	con = (sinvplabor_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL_SINVPLABOR__").after(htm);
	$("#it3tiempo_"+can).numeric(".");
	$('#it3estacion_'+can).focus();
	$("#it3segundos_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_sinvplabor();
			return false;
		}
	});
	sinvplabor_cont=sinvplabor_cont+1;
}
function del_sinvplabor(id){
	id = id.toString();
	$('#tr_sinvplabor_'+id).remove();
}

function add_marca(){
	marca=prompt("Introduza el nombre de la MARCA a agregar");
	if(marca==null){
	} else {
		$.ajax({
			type: "POST",
			processData:false,
			url: '<?php echo $link ?>',
			data: "valor="+encodeURIComponent(marca),
			success: function(msg){
				if(msg=="s.i"){
					marca=marca.substr(0,30);
					$.post('<?php echo $link4 ?>',{ x:"" },function(data){$("#marca").html(data);$("#marca").val(marca);})
				} else {
					alert("Disculpe. En este momento no se ha podido agregar la marca, por favor intente mas tarde");
				}
			}
		});
	}
}

function add_unidad(){
	unidad=prompt("Introduza el nombre de la UNIDAD a agregar");
	if(unidad==null){
	}else{
		$.ajax({
		 type: "POST",
		 processData:false,
			url: '<?php echo $link5 ?>',
			data: "valor="+encodeURIComponent(unidad),
			success: function(msg){
				if(msg=="s.i"){
					unidad=unidad.substr(0,8);
					$.post('<?php echo $link6 ?>',{ x:"" },function(data){$("#unidad").html(data);$("#unidad").val(unidad);})
				}
				else{
					alert("Disculpe. En este momento no se ha podido agregar la unidad, por favor intente mas tarde");
				}
			}
		});
	}
}

function add_depto(){
	depto=prompt("Introduza el nombre del DEPARTAMENTO a agregar");
	if(depto==null){
	}else{
		$.ajax({
		 type: "POST",
		 processData:false,
			url: '<?php echo $link9 ?>',
			data: "valor="+encodeURIComponent(depto),
			success: function(msg){
				if(msg=="Y.a-Existe"){
					alert("Ya existe un Departamento con esa Descripcion");
				}
				else{
					if(msg=="N.o-SeAgrego"){
						alert("Disculpe. En este momento no se ha podido agregar el departamento, por favor intente mas tarde");
					}else{
						$.post('<?php echo $link10 ?>',{ x:"" },function(data){$("#depto").html(data);$("#depto").val(msg);})
					}
				}
			}
		});
	}
}

function add_linea(){
	deptoval=$("#depto").val();
	if(deptoval==""){
		alert("Debe seleccionar un Departamento al cual agregar la linea");
	}else{
		linea=prompt("Introduza el nombre de la LINEA a agregar al DEPARTAMENTO seleccionado");
		if(linea==null){
		}else{
			$.ajax({
			type: "POST",
			processData:false,
				url: '<?php echo $link11 ?>',
				data: "valor="+encodeURIComponent(linea)+"&&valor2="+encodeURIComponent(deptoval),
				success: function(msg){
					if(msg=="Y.a-Existe"){
						alert("Ya existe una Linea con esa Descripcion");
					}else{
						if(msg=="N.o-SeAgrego"){
							alert("Disculpe. En este momento no se ha podido agregar la linea, por favor intente mas tarde");
						}else{
							$.post('<?php echo $link12 ?>',{ depto:deptoval },function(data){$("#linea").html(data);$("#linea").val(msg);})
						}
					}
				}
			});
		}
	}
}

function add_grupo(){
	lineaval=$("#linea").val();
	deptoval=$("#depto").val();
	if(lineaval==""){
		alert("Debe seleccionar una Linea a la cual agregar el departamento");
	}else{
		grupo=prompt("Introduza el nombre del GRUPO a agregar a la LINEA seleccionada");
		if(grupo==null){
		}else{
			$.ajax({
				type: "POST",
				processData:false,
				url: '<?php echo $link13 ?>',
				data: "valor="+encodeURIComponent(grupo)+"&&valor2="+encodeURIComponent(lineaval)+"&&valor3="+encodeURIComponent(deptoval),
				success: function(msg){
					if(msg=="Y.a-Existe"){
						alert("Ya existe una Linea con esa Descripcion");
					}else{
						if(msg=="N.o-SeAgrego"){
							alert("Disculpe. En este momento no se ha podido agregar la linea, por favor intente mas tarde");
						}else{
							$.post('<?php echo $link14 ?>',{ linea:lineaval },function(data){$("#grupo").html(data);$("#grupo").val(msg);})
						}
					}
				}
			});
		}
	}
};

function dpto_change(){
	$.post('<?php echo $link12 ?>',{ depto:$("#depto").val() },function(data){$("#linea").html(data);})
	$.post('<?php echo $link14 ?>',{ linea:'' },function(data){$("#grupo").html(data);})
}

function ultimo(){
	$.ajax({
		url: '<?php echo $link7; ?>',
		success: function(msg){
			alert( "El &uacute;ltimo c&oacute;digo ingresado fue: " + msg );
		}
	});
}

function sugerir(){
	$.ajax({
		url: '<?php echo $link8 ?>',
		success: function(msg){
			if(msg){
				$("#codigo").val(msg);
			} else {
				alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
			}
		}
	});
}

function almubica( id, almacen){
	mubica = prompt("Introduza la ubicacion ("+almacen+")");
	if( mubica==null ){
	}else{
		$.ajax({
		 type: "POST",
		 //processData:false,
			url: '<?php echo site_url('inventario/sinv/almubica/') ?>',
			data: {ubica:mubica, mid:id, alma:almacen },
			dataType: "text",
			async: false,
			success: function(msg){
				alert("Mensaje="+msg);
			}
		});
	}
}


</script>
<?php }else{
//script cuando es show
$id=$form->get_from_dataobjetct('id');

$link20=site_url('inventario/sinv/sinvcodigoexiste');
$link21=site_url('inventario/sinv/sinvcodigo');
$link25=site_url('inventario/sinv/sinvbarras');
$link27=site_url('inventario/sinv/sinvpromo');

$link28=site_url('inventario/sinv/sinvproveed/');
$link29=site_url('inventario/sinv/sinvsprv/'.$id);

$link30=site_url('inventario/sinv/sinvborrasuple/');
$link35=site_url('inventario/sinv/sinvborraprv/');

$link40=site_url('inventario/sinv/sinvdescu/'.$id);
$link41=site_url('inventario/sinv/sinvcliente/');
?>
<style type="text/css">
	#maintabcontainer {width: 780px; margin: 5px auto; }
	#maintabcontainer ul { font-size: 8px; }
	div#sinvprv label { display:block; }
	div#sinvprv input { display:block; }
	div#sinvprv input.text { margin-bottom:12px; width:95%; padding: .4em; }
	div#sinvprv fieldset { padding:0; border:0; margin-top:20px; }
	div#sinvprv h1 { font-size: 1.2em; margin: .6em 0; }
	div#sinvdescu label { display:block; }
	div#sinvdescu input { display:block; }
	div#sinvdescu input.text { margin-bottom:12px; width:95%; padding: .4em; }
	div#sinvdescu select { display:block; }
	div#sinvdescu select.text { margin-bottom:12px; width:95%; padding: .4em; }
	div#sinvdescu fieldset { padding:0; border:0; margin-top:20px; }
	div#sinvdescu h1 { font-size: 1.2em; margin: .6em 0; }
	.ui-dialog .ui-state-error { padding: .3em; }
	.validateTips { border: 1px solid transparent; padding: 0.3em; }
</style>

<script language="javascript" type="text/javascript">
function isNumeric(value) {
  if (value == null || !value.toString().match(/^[-]?\d*\.?\d*$/)) return false;
  return true;
}

$(document).ready(function() {
	requeridos(true);
	$("#dialog:ui-dialog").dialog("destroy");

	var proveedor = $("#proveedor"),
		cod_prv   = $("#cod_prv"),
		codigo    = $("#codigo"),
		cod_cli   = $("#cod_cli"),
		descuento = $("#descuento"),
		tipo      = $("#tipo"),
		allFields = $([]).add( proveedor ).add( codigo ).add( cod_prv ),
		tips      = $(".validateTips");

	$("#sinvprv").dialog({
		autoOpen: false,
		height: 300,
		width: 350,
		modal: true,
		buttons: {
			"Guardar Codigo": function() {
				var bValid = true;
				allFields.removeClass( "ui-state-error" );

				bValid = bValid && checkLength( proveedor, "proveedor", 3, 50 );
				bValid = bValid && checkLength( cod_prv, "cod_prv", 1, 5 );
				bValid = bValid && checkLength( codigo, "codigo", 6, 15 );

				if ( bValid ) {
					$.ajax({
						  url: '<?php echo $link29 ?>'+'/'+cod_prv.val()+'/'+codigo.val(),
						  //context: document.body,
						  success: function(msg){
						    alert("Terminado: "+msg);
						  }
					});
					$(this).dialog("close");
				}
			},
			Cancelar: function() {
				$(this).dialog("close");
			}
		},
		close: function() {
			allFields.val("").removeClass("ui-state-error");
		}
	});

	$("#sinvdescu").dialog({
		autoOpen: false,
		height: 350,
		width: 350,
		modal: true,
		buttons: {
			"Guardar Descuento": function() {
				var bValid = true;
				allFields.removeClass( "ui-state-error" );

				if ( bValid ) {
					$.ajax({
						url: '<?php echo $link40 ?>'+"/"+cod_cli.val()+"/"+descuento.val()+"/"+tipo.val(),
						success: function(msg){
							alert("Terminado: "+msg);
						}
					});
					$( this ).dialog( "close" );
				}
			},
			Cancelar: function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			allFields.val("").removeClass("ui-state-error");
		}
	});

	$("#proveedor").autocomplete({
		source: function( req, add){
			$.ajax({
				url: '<?php echo $link28 ?>',
				type: "POST",
				dataType: "json",
				data: {"tecla":req.term},
				success:
					function(data) {
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
		minLength: 3,
		select: function(evento, ui){
			$("#cod_prv").val(ui.item.codigo);
		}
	});

	$( "#cliente" ).autocomplete({
		source: function( req, add){
			$.ajax({
				url: '<?php echo $link41 ?>',
				type: "POST",
				dataType: "json",
				data: {"tecla":req.term},
				success:
					function(data) {
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
		minLength: 3,
		select: function(evento, ui){
			$("#cod_cli").val(ui.item.codigo);
		}
	});

	$("#modalDiv").dialog({
        modal: true,
        autoOpen: false,
        height: "380",
        width: "320",
        draggable: true,
        resizeable: true,
        title: "Unidades"
    });

	$("#goToMyPage").click(function() {
		url = "/proteoerp/inventario/unidad";
		$("#modalDiv").dialog("open");
		$("#modalIFrame").attr("src",url);
		return false;
	});
	$( "#maintabcontainer" ).tabs();
});

function updateTips( t ) {
	tips.text(t).addClass( "ui-state-highlight" );
	setTimeout(function() {
		tips.removeClass( "ui-state-highlight", 1500 );
	},500);
}

function checkLength( o, n, min, max ) {
	if ( o.val().length > max || o.val().length < min ) {
		o.addClass( "ui-state-error" );
		updateTips( "Length of " + n + " must be between " +
			min + " and " + max + "." );
		return false;
	} else {
		return true;
	}
}

function checkRegexp( o, regexp, n ) {
	if(!( regexp.test( o.val() ) ) ) {
		o.addClass( "ui-state-error" );
		updateTips( n );
		return false;
	} else {
		return true;
	}
}

function sinvcodigo(mviejo){
	var yurl = "";
	//var mcodigo=jPrompt("Ingrese el Codigo a cambiar ");
	jPrompt("Codigo Nuevo","" ,"Codigo Nuevo", function(mcodigo){
		if( mcodigo==null ){
			jAlert("Cancelado por el usuario","Informacion");
		} else if( mcodigo=="" ) {
			jAlert("Cancelado,  Codigo vacio","Informacion");
		} else {
			yurl = encodeURIComponent(mcodigo);
			$.ajax({
				url: '<?php echo site_url('inventario/sinv/sinvcodigoexiste'); ?>',
				global: false,
				type: "POST",
				data: ({ codigo : mcodigo }),
				dataType: "text",
				async: false,
				success: function(sino) {
					if (sino.substring(0,1)=="S"){
						jConfirm(
							"Ya existe el codigo <div style=\"font-size: 200%;font-weight: bold \">"+mcodigo+"</"+"div>"+sino.substring(1)+"<p>si prosigue se eliminara el producto anterior y<br/> todo el movimiento de este, pasara al codigo "+mcodigo+"</"+"p> <p style=\"align: center;\">Desea <strong>Fusionarlos?</"+"strong></"+"p>",
							"Confirmar Fusion",
							function(r){
								if(r){
									sinvcodigocambia("S", mviejo, mcodigo);
								}
							}
						);
					} else {
						jConfirm(
							"Sustitur el codigo actual  por: <center><h2 style=\"background: #ddeedd\">"+mcodigo+"</"+"h2></"+"center> <p>Al cambiar de codigo el producto, todos los<br/> movimientos y estadisticas se cambiaran<br/> correspondientemente.</"+"p> ",
							"Confirmar cambio de codigo",
							function(r){
								if(r){
									sinvcodigocambia("N", mviejo, mcodigo);
								}
							}
						)
					}
				},
				error: function(h,t,e){
					jAlert("Error..codigo="+yurl+" ",e);
				}
			});
		}
	})
};

function sinvcodigocambia( mtipo, mviejo, mcodigo ) {
	$.ajax({
		url: '<?php echo site_url('inventario/sinv/sinvcodigo'); ?>',
		global: false,
		type: "POST",
		data: ({ tipo:  mtipo,viejo: mviejo, codigo: mcodigo }),
		dataType: "text",
		async: false,
		success: function(sino) {
			jAlert("Cambio finalizado "+sino,"Finalizado Exitosamente");
		},
		error: function(h,t,e) {
			jAlert("Error..","Finalizado con Error" );
		}
	});

	if( mtipo=="N" ) {
		location.reload(true);
	} else {
		location.replace('<?php echo site_url("inventario/sinv/filteredgrid") ?>');
	}
}

function sinvbarras(mcodigo){
	var yurl = "";
	jPrompt("Nuevo Codigo de Barras","" ,"Codigo Barras", function(mbarras){
		if( mbarras==null ){
			jAlert("Cancelado por el usuario","Informacion");
		}else if( mbarras=="" ){
			jAlert("Cancelado,  Codigo vacio","Informacion");
		}else{
			$.ajax({
				url: '<?php echo $link25 ?>',
				global: false,
				type: "POST",
				data: ({ id : mcodigo, codigo : mbarras }),
				dataType: "text",
				async: false,
				success: function(sino)  { jAlert( sino,"Informacion"); },
				error:   function(h,t,e) { jAlert("Error..codigo="+mbarras+" <p>"+e+"</"+"p>","Error"); }
			});
		}
	})
};

function sinvpromo(mcodigo){
	jPrompt("Descuento Promocional","" ,"Descuento", function(margen){
		if( margen==null ){
			jAlert("Cancelado por el usuario","Informacion");
		}else if( margen=="" ){
			jAlert("Cancelado,  Codigo vacio","Informacion");
		}else{
			if(isNumeric(margen)){
				$.ajax({
					url: '<?php echo $link27 ?>',
					global: false,
					type: "POST",
					data: ({ id : mcodigo, margen : margen }),
					dataType: "text",
					async: false,
					success: function(sino)  { jAlert( sino,"Informacion"); },
					error:   function(h,t,e) { jAlert("Error..codigo="+margen+" <p>"+e+"</"+"p>","Error"); }
				});
			} else { jAlert("Entrada no numerica","Alerta") }
		}
	});
}

// Descuento por Cliente
function sinvdescu(mcodigo){
	$( "#sinvdescu" ).dialog( "open" );
}
// Codigo de producto en el Proveedor
function sinvproveed(mcodigo){
	$( "#sinvprv" ).dialog( "open" );
}

function sinvborrasuple(mcodigo){
	jConfirm(
		"Desea eliminar este codigo suplementario?<p><b>"+mcodigo+"</"+"b></"+"p>",
		"Confirmar Borrado",
		function(r){
			if(r){
				$.ajax({
					url: '<?php echo $link30 ?>',
					global: false,
					type: "POST",
					data: ({ codigo : mcodigo }),
					dataType: "text",
					async: false,
					success: function(sino)  { jAlert( sino,"Informacion"); },
					error:   function(h,t,e) { jAlert("Error..codigo="+mcodigo+" <p>"+e+"</"+"p>","Error"); }
				});
			}
		}
	);
}

function sinvborraprv(mproveed, mcodigo){
	jConfirm(
		"Desea eliminar este codigo de proveedor?<p><strong>"+mcodigo+"</"+"strong></"+"p>",
		"Confirmar Borrado",
		function(r){
			if(r){
				$.ajax({
					url: '<?php echo $link35 ?>',
					global: false,
					type: "POST",
					data: ({ proveed : mproveed, codigo : mcodigo }),
					dataType: "text",
					async: false,
					success: function(sino)  { jAlert( sino,"Informacion"); },
					error:   function(h,t,e) { jAlert("Error..codigo="+mcodigo+" <p>"+e+"</"+"p>","Error"); }
				});
			}
		}
	);
};
</script>
<?php }
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';
?>

<fieldset style='border: 1px outset #9AC8DA;background: #FFFFF9;'>
<table border='0' width="100%">
	<tr>
		<td colspan='2' valign='top'>
			<table border='0' width="100%" cellpadding='0' cellspacing='0'>
				<tr>
					<td class="littletableheaderc"><?php echo $form->codigo->label; ?></td>
					<td class="littletablerow"><?php echo $form->codigo->output; ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'>Alterno</td>
					<td class="littletablerow"><?php echo $form->alterno->output   ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->barras->label ?></td>
					<td class="littletablerow">    <?php echo $form->barras->output     ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'>Caja</td>
					<td class="littletablerow">
						<?php
						if($form->_status=='show'){
							if(!empty($form->enlace->value)){
								$dbenlace=$this->db->escape($form->enlace->value);
								$mID = $this->datasis->dameval('SELECT id FROM sinv WHERE codigo='.$dbenlace);
								echo anchor('inventario/sinv/dataedit/show/'.$mID,$form->enlace->value);
							}
						}else{
							echo $form->enlace->output.$form->cdescrip->output;
						}
						?>
					</td>
				</tr>
			</table>
		</td>
		<td colspan='2' valign='top'>
			<table border='0' width="100%" cellpadding='0' cellspacing='0'>
				<tr>
					<td class='littletableheaderc'><?php echo $form->descrip->label  ?></td>
					<td colspan="3" class="littletablerow"><?php echo $form->descrip->output; ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'>Adicional</td>
					<td colspan="3" class="littletablerow"><?php echo $form->descrip2->output; ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->marca->label;  ?></td>
					<td class="littletablerow"> <?php echo $form->marca->output;    ?></td>
					<td class='littletableheaderc'><?php echo $form->ubica->label;  ?></td>
					<td class="littletablerow">    <?php echo $form->ubica->output; ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->modelo->label;   ?></td>
					<td class="littletablerow">    <?php echo $form->modelo->output;  ?></td>
					<td class='littletableheaderc'><?php echo $form->garantia->label; ?></td>
					<td class="littletablerow">    <?php echo $form->garantia->output;?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan='4'>
			<table width="100%" border='0' style="border-collapse;border:1px dashed">
				<tr>
					<td class="littletablerow"><?php echo $form->tipo->label;    ?></td>
					<td class="littletablerow"><?php echo $form->tipo->output;   ?></td>
					<td class="littletablerow"><?php echo $form->activo->label;  ?></td>
					<td class="littletablerow"><?php echo $form->activo->output; ?></td>
					<td class="littletablerow"><?php echo $form->iva->label;     ?></td>
					<td class="littletablerow"><?php echo $form->iva->output;    ?></td>
					<td class="littletablerow"><?php echo $form->exento->label;  ?></td>
					<td class="littletablerow"><?php echo $form->exento->output; ?></td>
					<td class="littletablerow"><?php echo $form->clase->label    ?></td>
					<td class="littletablerow"><?php echo $form->clase->output   ?></td>

				</tr>
			</table>
		</td>
	</tr>
</table>
</fieldset>

<div id="maintabcontainer">
	<ul>
		<li><a href="#tab1">Parametros</a></li>
		<li><a href="#tab2">Precios</a></li>
		<li><a href="#tab3">Existencias</a></li>

		<?php if ( $this->datasis->traevalor('SUNDECOP') == 'S') { ?>
		<li><a href="#tab4">Sundecop</a></li>
		<?php } ?>

		<li><a href="#tab5">Promociones</a></li>
		<li><a href="#tab6">Descuentos al Mayor</a></li>

		<?php if(($form->_dataobject->get('tipo')=='Combo' && $form->_status=='show') || $form->_status!='show'){?>
		<li id="litab7"><a href="#tab7">Articulos del Combo</a></li>
		<?php }?>

		<?php if ( $this->datasis->traevalor('SINVPRODUCCION','Activa el modo de produccion') == 'S') { ?>
		<li><a href="#tab8">Ingredientes</a></li>
		<li><a href="#tab9">Labores     </a></li>
		<?php } ?>
		<li><a href="#tab10">Ficha Tec.</a></li>
	</ul>
	<div id="tab1" style='background:#EFEFFF'>
	<table width="100%" border='0'>
	<tr>
		<td colspan='2' valign='top'>
			<table border='0' width="100%" style='border-collapse;border: 1px dotted;' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='littletableheaderc'><?php echo $form->tdecimal->label ?></td>
					<td class="littletablerow"><?php echo $form->tdecimal->output   ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->serial->label ?></td>
					<td class="littletablerow"><?php echo $form->serial->output   ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->clave->label ?></td>
					<td class="littletablerow"><?php echo $form->clave->output   ?></td>
				</tr>
			</table>
		</td>
		<td valign='top' align='center'>
			<table border='0'  width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='littletableheaderc'><?php echo $form->fracci->label ?></td>
					<td class="littletablerow"><?php echo $form->fracci->output   ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->peso->label ?></td>
					<td class="littletablerow"><?php echo $form->peso->output   ?></td>
					<td class="littletablerow"><?php echo $this->datasis->traevalor('SINVPESO','Kg.') ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->unidad->label ?></td>
					<td class="littletablerow"><?php echo $form->unidad->output   ?></td>
				</tr>
			</table>
		</td>
		<td valign='top' align='center'>
			<table border='0' width='100%' style='border-collapse;border: 1px dotted' cellpadding='0' cellspacing='0'>
				<tr>
					<td width='50' class='littletableheaderc'><?php echo $form->alto->label ?></td>
					<td class="littletablerow"><?php echo $form->alto->output?></td>
					<td class="littletablerow" align='left'><?php echo $this->datasis->traevalor('SINVDIMENCIONES','cm.') ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->ancho->label ?></td>
					<td class="littletablerow"><?php echo $form->ancho->output   ?></td>
					<td class="littletablerow"><?php echo $this->datasis->traevalor('SINVDIMENCIONES','cm.') ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->largo->label; ?></td>
					<td class="littletablerow"><?php echo $form->largo->output;   ?></td>
					<td class="littletablerow"><?php echo $this->datasis->traevalor('SINVDIMENCIONES','cm.') ?></td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	<table width="100%" border='0'>
	<tr>
		<td valign='top' align='left'>
			<table border='0' cellpadding='0' cellspacing='0'>
				<tr>
					<td width='80' class='littletableheaderc'><?php echo $form->depto->label ?></td>
					<td nowrap class="littletablerow"><?php echo $form->depto->output   ?></td>
				</tr>
				<tr style="height:14px">
					<td class='littletableheaderc'><?php echo $form->linea->label ?></td>
					<td class="littletablerow" id='td_linea'><?php echo $form->linea->output?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->grupo->label ?></td>
					<td nowrap class="littletablerow" id='td_grupo'><?php echo $form->grupo->output   ?></td>
				</tr>
			</table>
		</td>
		<td>
			<table border='0' style='border-collapse;border: 1px dotted' cellpadding='0' cellspacing='0'>
				<tr>
					<td class='littletableheaderc'><?php echo $form->maxven->label ?></td>
					<td class="littletablerow"><?php  echo $form->maxven->output   ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->minven->label ?></td>
					<td class="littletablerow"><?php echo $form->minven->output?></td>
				</tr>
				<tr>
					<td class='littletableheaderc' style="text-align:center" colspan='2'><b>*(0) inactiva</b></td>
				</tr>
			</table>
		</td>
		<td valign='top'  align='left'>
			<table border='0' width="100%" cellpadding='0' cellspacing='0'>
				<tr>
					<td class='littletableheaderc'><?php echo $form->linfe->label ?></td>
					<td class="littletablerow" style='width:90px'>    <?php echo $form->linfe->output.$form->lindia->output   ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->comision->label ?></td>
					<td class="littletablerow"><?php echo $form->comision->output   ?></td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	<table width="100%" border='0'>
	<tr>
		<td class='littletableheaderc'><?php echo $form->url->label ?></td>
		<td class="littletablerow"><?php echo $form->url->output; ?></td>
	</tr>
	</table>
</div>
<div id="tab2" style='background:#EFEFFF'>
	<table width='100%'>
	<tr>
		<td valign='top'>
			<fieldset style='border: 1px outset #B45F04;background: #FFEFFF;'>
			<legend class="titulofieldset" >Costos</legend>
			<table width='100%' border="0" cellpadding='0' cellspacing='0'>
				<tr>
					<td class="littletableheaderc"><?php echo $form->ultimo->label   ?></td>
					<td class="littletablerow"    ><?php echo $form->ultimo->output.$form->cultimo->output ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->pond->label    ?></td>
					<td class="littletablerow"    ><?php echo $form->pond->output.$form->cpond->output   ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->standard->label    ?></td>
					<td class="littletablerow"    ><?php echo $form->standard->output   ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->formcal->label ?></td>
					<td class="littletablerow"    ><?php echo $form->formcal->output.$form->cformcal->output ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->redecen->label ?></td>
					<td class="littletablerow"    ><?php echo $form->redecen->output?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->dolar->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->dolar->output; ?></td>
				</tr>
				<tr>
					<td class='littletableheaderc'><?php echo $form->margenu->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->margenu->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
		<td valign='top'>
			<fieldset style='border: 1px outset #B45F04;background: #FFEFFF;'>
			<legend class="titulofieldset" style='font-size:16' >Precios</legend>
			<table width='100%' cellpadding='0' cellspacing='0'>
				<tr>
					<td class="littletableheader" style='background: #3B240B;color: #FFEEFF;font-weight: bold;text-align:center;'>&nbsp;</td>
					<td class="littletableheader" style='background: #3B240B;color: #FFEEFF;font-weight: bold;text-align:center;'>Margen</td>
					<td class="littletableheader" style='background: #3B240B;color: #FFEEFF;font-weight: bold;text-align:center;'>Base  </td>
					<td class="littletableheader" style='background: #3B240B;color: #FFEEFF;font-weight: bold;text-align:center;'>Precio</td>
				</tr>
			  	<tr>
					<td class="littletableheaderc" align="center">1</td>
					<td class="littletablerow" align='center'><?php echo $form->margen1->output ?></td>
					<td class="littletablerow" align='center'><?php echo $form->base1->output   ?></td>
					<td class="littletablerow" align='center'><?php echo $form->precio1->output.$form->cbase1->output; ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc" align="center">2</td>
					<td class="littletablerow" align='center'><?php echo $form->margen2->output ?></td>
					<td class="littletablerow" align='center'><?php echo $form->base2->output   ?></td>
					<td class="littletablerow" align='center'><?php echo $form->precio2->output.$form->cbase2->output; ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc" align="center">3</td>
					<td class="littletablerow" align='center'><?php echo $form->margen3->output ?></td>
					<td class="littletablerow" align='center'><?php echo $form->base3->output   ?></td>
					<td class="littletablerow" align='center'><?php echo $form->precio3->output.$form->cbase3->output; ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc" align="center">4</td>
					<td class="littletablerow" align='center'><?php echo $form->margen4->output ?></td>
					<td class="littletablerow" align='center'><?php echo $form->base4->output   ?></td>
					<td class="littletablerow" align='center'><?php echo $form->precio4->output.$form->cbase4->output; ?></td>
				</tr>
			</table>
			</fieldset>
			<fieldset style='border: 1px outset #B45F04;background: #FFEFFF;'>
			<table width='100%' cellspacing='0'>
				<tr>
					<td class='littletableheaderc' align="right">Precio M&iacute;nimo</td>
					<td class="littletablerow"                  ><?php echo $form->premin->output; ?></td>
					<td class='littletableheaderc' align="right">Aumento (%) venta detallada</td>
					<td class="littletablerow"                  ><?php echo $form->aumento->output; ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
	</table>
</div>

<?php if(($form->_dataobject->get('tipo')=='Combo' && $form->_status=='show') || $form->_status!='show'){?>
<div id="tab7" style='background:#EFEFFF'>
	<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:130px'>
		<table width='100%' cellpadding='0' cellspacing='0'>
			<tr id='__INPL_SINVCOMBO__'>
				<td bgcolor='#7098D0'><b>C&oacute;digo</b></td>
				<td bgcolor='#7098D0'><b>Descripci&oacute;n</b></td>
				<td bgcolor='#7098D0' style='text-align:center;'><b>Cantidad</b></td>
				<td bgcolor='#7098D0' style='text-align:right;' ><b>Precio</b></td>
				<td bgcolor='#7098D0' style='text-align:right;' ><b>&Uacute;ltimo</b></td>
				<td bgcolor='#7098D0' style='text-align:right;' ><b>Ponderado</b></td>
				<?php if($form->_status!='show') {?>
				<td  bgcolor='#7098D0' align='center'><b>&nbsp;</b></td>
				<?php } ?>
			</tr>
			<?php
			for($i=0;$i<$form->max_rel_count['sinvcombo'];$i++) {
				$itcodigo   = "itcodigo_$i";
				$itdescrip  = "itdescrip_$i";
				$itcantidad = "itcantidad_$i";
				$itprecio   = "itprecio_$i";
				$itultimo   = "itultimo_$i";
				$itpond     = "itpond_$i";

				$oculto='';
				foreach($ocultos as $obj){
					$obj2='it'.$obj.'_'.$i;
					$oculto.=$form->$obj2->output;
				}
			?>
			<tr id='tr_sinvcombo_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" nowrap><?php echo $form->$itcodigo->output;       ?></td>
				<td class="littletablerow" align="left"       ><?php echo $form->$itdescrip->output;      ?></td>
				<td class="littletablerow" align="right"      ><?php echo $form->$itcantidad->output;     ?></td>
				<td class="littletablerow" align="right"      ><?php echo $form->$itprecio->output;      ?></td>
				<td class="littletablerow" align="right"      ><?php echo $form->$itultimo->output;       ?></td>
				<td class="littletablerow" align="right"      ><?php echo $form->$itpond->output.$oculto; ?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow" align="center">
					<a href='#' onclick='del_sinvcombo(<?php echo $i ?>);return false;'><?php echo img("images/delete.jpg") ?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
		</table>
		</div>
		<?php echo $container_co ?>
</div>
<?php } ?>

<?php if ( $this->datasis->traevalor('SINVPRODUCCION') == 'S') { ?>
<div id="tab8" style='background:#EFEFFF'>
	<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:170px'>
		<table width='100%' cellpadding='0' cellspacing='0'>
			<tr id='__INPL_SINVPITEM__'>
				<td bgcolor='#7098D0'              ><b>C&oacute;digo     </b></td>
				<td bgcolor='#7098D0'              ><b>Descripci&oacute;n</b></td>
				<td bgcolor='#7098D0' align='right'><b>Cantidad          </b></td>
				<td bgcolor='#7098D0' align='right'><b>Merma &#37;       </b></td>
				<td bgcolor='#7098D0'              ><b>Unidad;</b>       </td>
				<td bgcolor='#7098D0' align='right'><b>Merma &#37;       </b></td>

				<?php if($form->_status!='show') {?>
					<td  bgcolor='#7098D0' align='center'><b>&nbsp;</b></td>
				<?php } ?>
			</tr>
			<?php
			for($i=0;$i<$form->max_rel_count['sinvpitem'];$i++){
				$it2codigo   = "it2codigo_${i}";
				$it2descrip  = "it2descrip_${i}";
				$it2cantidad = "it2cantidad_${i}";
				$it2merma    = "it2merma_${i}";

				$itunidad    = "itunidad_${i}";
				$itfactor    = "itfactor_${i}";

				$it2formcal  = "it2formcal_${i}";
				$it2pond     = "it2pond_${i}";
				$it2ultimo   = "it2ultimo_${i}";
				$it2id_sinv  = "it2id_sinv_${i}";
			?>
			<tr id='tr_sinvpitem_<?php echo $i;?>'>
				<td class="littletablerow" align="left" nowrap><?php echo $form->$it2codigo->output;   ?></td>
				<td class="littletablerow" align="left"       ><?php echo $form->$it2descrip->output;  ?></td>
				<td class="littletablerow" align="right"      ><?php echo $form->$it2cantidad->output; ?></td>
				<td class="littletablerow" align="left"       ><?php echo $form->$itunidad->output ?></td>
				<td class="littletablerow" align="right"      ><?php echo $form->$itfactor->output ?></td>
				<td class="littletablerow" align="right"      ><?php echo $form->$it2merma->output.$form->$it2pond->output.$form->$it2ultimo->output.$form->$it2formcal->output.$form->$it2id_sinv->output; ?></td>
				<?php if($form->_status!='show'){?>
				<td class="littletablerow" align="center">
					<a href='#' onclick='del_sinvpitem(<?php echo $i ?>);return false;'><?php echo img("images/delete.jpg") ?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
			<tr id='__UTPL_SINVPITEM__'>
			</tr>
		</table>
	</div>
	<?php echo $container_it ?>
</div>
<div id="tab9" style='background:#EFEFFF'>
	<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:170px'>
		<table width='100%'>
			<tr id='__INPL_SINVPLABOR__'>
				<td bgcolor='#7098D0' align='left' ><b>Estaci&oacute;n</b></td>
				<td bgcolor='#7098D0' align='left' ><b>Actividad      </b></td>
				<td bgcolor='#7098D0' align='right'><b>U. Tiempo      </b></td>
				<td bgcolor='#7098D0' align='right'><b>Tiempo         </b></td>
				<?php if($form->_status!='show') {?>
					<td  bgcolor='#7098D0' align='center'><b>&nbsp;</b></td>
				<?php } ?>
			</tr>
			<?php
			for($i=0;$i<$form->max_rel_count['sinvplabor'];$i++){
				$it3estacion = "it3estacion_$i";
				$it3nombre   = "it3nombre_$i";
				$it3actividad= "it3actividad_$i";
				$it3tunidad  = "it3tunidad_$i";
				$it3tiempo   = "it3tiempo_$i";
			?>
			<tr id='tr_sinvplabor_<?php echo $i;?>'>
				<td class="littletablerow" align="left" nowrap><?php echo $form->$it3estacion->output;  ?></td>
				<td class="littletablerow" align="left"       ><?php echo $form->$it3actividad->output; ?></td>
				<td class="littletablerow" align="right"      ><?php echo $form->$it3tunidad->output;   ?></td>
				<td class="littletablerow" align="right"      ><?php echo $form->$it3tiempo->output;    ?></td>
				<?php if($form->_status!='show'){?>
				<td class="littletablerow" align="center">
					<a href='#' onclick='del_sinvplabor(<?php echo $i ?>);return false;'><?php echo img('images/delete.jpg') ?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
			<tr id='__UTPL_SINVPLABOR__'>
			</tr>
		</table>
	</div>
	<?php echo $container_la ?>
</div>
<?php } ?>

<div id="tab3" style='background:#EFEFFF'>
	<table width='100%'>
	<tr>
  		<td valign="top">
			<fieldset  style='border: 2px outset #FEB404;background: #FFFCE8;'>
			<legend class="titulofieldset" >Existencias</legend>
			<table width='100%' border='0' cellpadding='0' cellspacing='0' >
				<tr>
					<td class="littletableheaderc"><?php echo $form->existen->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->existen->output ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->exmin->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->exmin->output ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->exmax->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->exmax->output ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->exord->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->exord->output ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->exdes->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->exdes->output ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->vnega->label  ?></td>
					<td class="littletablerow"    ><?php echo $form->vnega->output ?></td>
				</tr>
			</table>
			</fieldset>
		</td>
  		<td valign="top">
			<fieldset  style='border: 2px outset #FEB404;background: #FFFCE8;'>
			<legend class="titulofieldset">Ubicaciones</legend>
			<div  style='height:110px;overflow:auto;'>
			<table id='simpletabla' width='100%' border='0' cellpadding='0' cellspacing='0' >
				<tbody>
				<tr>
					<td class="simplehead">Almac&eacute;n</td>
					<td class="simplehead">Ubicaci&oacute;n</td>
				</tr>

<?php
		//Ubicaciones
		$query=$this->db->query('SELECT alma, ubica FROM sinvalub WHERE codigo='.$this->db->escape($form->_dataobject->get('codigo')).' ORDER BY alma');
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				echo "\t\t\t\t<tr><td><a href='javascript:almubica(".$form->_dataobject->get('id').", \"".$row->alma."\")'>".$row->alma."</a></td><td>".$row->ubica."</td></tr>\n";
			}
		}
?>
				</tbody>
			</table>
			</div>
			</fieldset>
		</td>

		<?php if( !empty($form->almacenes->output)) { ?>
		<td valign="top">
			<fieldset style='border: 1px outset #FEB404;background: #FFFCE8;'>
			<legend class="titulofieldset" >Almacenes</legend>
			<div  style='height:110px;overflow:auto;'>
			<?php echo $form->almacenes->output ?>
			</div>
			</fieldset>
		</td>
		<?php } ?>

	</tr>
	</table>
</div>

<?php if ( $this->datasis->traevalor('SUNDECOP') == 'S') { ?>
<div id="tab4" style='background:#EFEFFF'>

	<table width='100%'>
	<tr>
		<td valign='top'>
			<fieldset  style='border: 2px outset #FEB404;background: #FFFCE8;'>
			<legend class="titulofieldset" >Ventas</legend>
			<table width='100%' >
				<tr>
					<td class="littletableheader" ><?php echo $form->mpps->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->mpps->output; ?></td>
					<td class="littletableheader" ><?php echo $form->rubro->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->rubro->output;?></td>
				</tr>
				<tr>
					<td class="littletableheader" ><?php echo $form->cpe->label;       ?></td>
					<td class="littletablerow"    ><?php echo $form->cpe->output;      ?></td>
					<td class="littletableheader" ><?php echo $form->subrubro->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->subrubro->output; ?></td>
				</tr>
				<tr>
					<td class="littletableheader" ><?php echo $form->cunidad->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->cunidad->output; ?></td>
					<td class="littletableheader" ><?php echo $form->cmarca->label;   ?></td>
					<td class="littletablerow"    ><?php echo $form->cmarca->output;  ?></td>
				</tr>
				<tr>
					<td class="littletableheader" ><?php echo $form->cmaterial->label;  ?></td>
					<td class="littletablerow"    ><?php echo $form->cmaterial->output; ?></td>
					<td class="littletableheader" ><?php echo $form->cforma->label;     ?></td>
					<td class="littletablerow"    ><?php echo $form->cforma->output;    ?></td>
				</tr>

			</table>
			</fieldset>
		</td>
	</tr>
	</table>

</div>
<?php } ?>


<div id="tab5" style='background:#EFEFFF'>
	<table width='100%'>
		<tr>
			<td>
<?php if($form->_status!='show'){ ?>
				<fieldset style='border: 1px outset #8A0808;background: #FFFBE9;'>
				<legend class="titulofieldset" >Descuentos</legend>
				<table border=0 width='100%'>
				<tr>
					<td valign="top"><?php
						$margen =  $this->datasis->dameval("SELECT margen FROM grup WHERE grupo='".$form->_dataobject->get('grupo')."'");
						if ($margen > 0 ) {
							echo "Descuento por Grupo ";
							echo $margen."% ";
							echo "Precio ".nformat($form->precio1->value * (100-$margen)/100);
						} else echo "No tiene descuento por grupo";
						?>
					</td>
				</tr>
				<tr>
					<td valign="top"><?php
						$margen =  $this->datasis->dameval("SELECT margen FROM sinvpromo WHERE codigo='".addslashes($form->_dataobject->get('codigo'))."'");
						if ($margen > 0 ) {
							echo "Descuento por Promoci&oacute;n ".$margen."% ";
							echo "Precio ".nformat($form->precio1->value * (100-$margen)/100);
						} else echo "No tiene descuento promocional";

						?>
					</td>
				</tr>
				</table>
				</fieldset>
				<?php } ?>
			</td>
		</tr>
	</table>
	<br/>
<?php
$query = $this->db->query("SELECT CONCAT(codigo,' ', descrip,' ',fracci) producto, id FROM sinv WHERE MID(tipo,1,1)='F' AND enlace=".$this->db->escape($form->_dataobject->get('codigo')));
if ($query->num_rows()>0 ) {
?>
	<fieldset style='border: 2px outset #8A0808;background: #FFFBE9;'>
	<legend class="titulofieldset" >Productos Derivados</legend>
	<table width='100%'>
	<tr>
		<?php
			$m = 1;
			foreach($query->result() as $row ){
				if ( $m > 4 ) {
					echo "</tr><tr>";
					$m = 1;
				}
				echo "<td class='littletablerow'>";
				echo anchor('inventario/sinv/dataedit/show/'.$row->id,$row->producto);
				echo "</td>";
				$m += 1;
			}
			?>
	</tr>
	</table>
	</fieldset>
<?php }  // rows>0  ?>
</div>

<div id="tab6" style='background:#EFEFFF'>
	<table width='100%'>
		<tr>
			<td colspan="2">
				<fieldset style='border: 1px outset #8A0808;background: #FFFBE9;'>
				<legend class="titulofieldset" >Descuentos al Mayor</legend>
				<table width='100%'>
				<tr>
					<td class="littletablerow" align='right'><?php echo $form->mmargen->label      ?>%</td>
					<td class="littletablerow" align='left' ><?php echo $form->mmargen->output     ?></td>
					<td class="littletablerow" align='right'><?php echo $form->pm->label           ?>%</td>
					<td class="littletablerow" align='left' ><?php echo $form->pm->output          ?></td>
					<td class="littletablerow" align='left' ><?php echo $form->mmargenplus->label  ?>%</td>
					<td class="littletablerow" align='right'><?php echo $form->mmargenplus->output ?></td>
				</tr>
				</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td>
				<fieldset style='border: 1px outset #8A0808;background: #FFFBE9;'>
				<legend class="titulofieldset" >Bonos por volumen</legend>
				<table width='100%'>
				<tr>
					<td class="littletablerow" >Fecha de inicio</td>
					<td class="littletablerow" align='right'><?php echo $form->fdesde->output ?></td>
					<td class="littletablerow">Fecha de fin</td>
					<td class="littletablerow" align='right'><?php echo $form->fhasta->output ?></td>
				</tr><tr>
					<td class="littletablerow">Si compra</td>
					<td class="littletablerow" align='right'><?php echo $form->bonicant->output ?></td>
					<td class="littletablerow">se bonifica con </td>
					<td class="littletablerow" align='right'><?php echo $form->bonifica->output ?></td>
				</tr><tr>
					<td colspan='4' class="littletablerow" >&nbsp;</td>
				</tr>
				</table>
				</fieldset>
			</td><td>
				<fieldset style='border: 1px outset #8A0808;background: #FFFBE9;'>
				<legend class="titulofieldset" >Descuentos por escalas</legend>
				<table width='100%'>
				<tr>
					<td class="littletablerow">1- </td>
					<td class="littletablerow" align='right'><?php echo $form->pescala1->output ?>%</td>
					<td class="littletablerow">Si lleva</td>
					<td class="littletablerow" align='right'><?php echo $form->escala1->output ?></td>
				</tr>
				<tr>
					<td class="littletablerow">2- </td>
					<td class="littletablerow" align='right'><?php echo $form->pescala2->output ?>%</td>
					<td class="littletablerow">Si lleva</td>
					<td class="littletablerow" align='right'><?php echo $form->escala2->output ?></td>
				</tr>
				<tr>
					<td class="littletablerow">3- </td>
					<td class="littletablerow" align='right'><?php echo $form->pescala3->output ?>%</td>
					<td class="littletablerow">Si lleva</td>
					<td class="littletablerow" align='right'><?php echo $form->escala3->output ?></td>
				</tr>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
</div>
<div id="tab10" style='background:#EFEFFF'>
	<table width='100%'>
		<tr>
			<td>
				<fieldset style='border: 1px outset #8A0808;background: #FFFBE9;'>
				<legend class="titulofieldset" >Ficha t&eacute;nica</legend>
				<table width='100%'>
				<tr>
					<td class="littletablerow"><?php echo $form->ficha->output ?></td>
				</tr>
				</table>
				</fieldset>
			</td>
		</tr>
	</table>
</div>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end?>

<?php if($form->_status=='show'){ ?>
<div style="display: none">
	<form action="'.base_url().'/inventario/kardex/filteredgrid/search/osp" method="post" id="kardex" name="kardex" target="kpopup">
		<input type="text" name="codigo" value="'.$mcodigo.'" />
		<input type="text" name="ubica"  value="" />
		<input type="text" name="fecha"  value="'.dbdate_to_human($mfdesde).'" />
		<input type="text" name="fechah" value="'.dbdate_to_human($mfhasta).'" />
		<input type="submit" />
	</form>
</div>

<div id="sinvprv" title="Agregar c&oacute;digo de Proveedor">
	<p class="validateTips">Codigo del proveedor para este producto</p>
	<form>
	<fieldset>
		<label for="proveedor">Proveedor</label>
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td>
					<input type="text" size="80" name="proveedor" id="proveedor" class="text ui-widget-content ui-corner-all" />
				</td>
				<td>
					<input type="text" readonly="readonly" size="8" name="cod_prv" id="cod_prv" class="text ui-widget-content ui-corner-all" />
				</td>
			</tr>
		</table>
		<label for="codigo">C&oacute;digo</label>
		<input type="text" name="codigo" id="codigo" value="" class="text ui-widget-content ui-corner-all" />
	</fieldset>
	</form>
</div>

<div id="sinvdescu" title="Agregar Descuento">
	<p class="validateTips">Descuento para este producto</p>
	<form>
	<fieldset>
		<label for="cliente">Cliente</label>
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td>
					<input type="text" size="80" name="cliente" id="cliente" class="text ui-widget-content ui-corner-all" />
				</td>
				<td>
					<input type="text" readonly="readonly" size="8" name="cod_cli" id="cod_cli" class="text ui-widget-content ui-corner-all" />
				</td>
			</tr>
		</table>
		<label for="descuento">Porcentaje %</label>
		<input type="text" name="descuento" id="descuento" value="" class="text ui-widget-content ui-corner-all" />
		<label for="descuento">Aplicaci&oacute;n del Porcentaje</label>
		<select name="tipo" id="tipo" value="D" class="text ui-widget-content ui-corner-all" >
			<option value="D">Descuento: Precio1 - Porcentaje</option>
			<option value="A">Aumento: Costo + Porcentaje</option>
		</select>
	</fieldset>
	</form>
</div>
<?php } ?>
<?php endif; ?>
