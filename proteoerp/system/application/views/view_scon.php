<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itscon');
$scampos  ='<tr id="tr_itscon_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigo']['field'].'</td>';
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
$scampos .= '<td class="littletablerow"><a href=# onclick="del_itscon(<#i#>);return false;">'.img('images/delete.jpg').'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itscon_cont=<?php echo $form->max_rel_count['itscon']; ?>;
$(function(){

	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });

	$(document).keydown(function(e){
		if (e.which == 13) return false;
	});

	$(".inputnum").numeric(".");
	totalizar();

	for(var i=0;i < <?php echo $form->max_rel_count['itscon']; ?>;i++){
		cdropdown(i);
		autocod(i.toString());
	}

	$('#clipro').autocomplete({
		<?php if($opttipo=='C'){ ?>
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
								$('#cliprotipo').val('1');

								$('#direc1').val('');
								$('#direc1_val').text('');
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
				$('#clipro').attr("readonly", "readonly");
				$('#nombre').val(ui.item.nombre);
				$('#rifci').val(ui.item.rifci);
				$('#clipro').val(ui.item.cod_cli);
				$('#cliprotipo').val(ui.item.tipo);
				$('#direc1').val(ui.item.direc);
				post_modbus_scli();
				setTimeout(function() {  $("#clipro").removeAttr("readonly"); }, 1500);
			}
		<?php } else { ?>

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
							if(data.length==0){
								$('#nombre').val('');
								$('#nombre_val').text('');

								$('#rifci').val('');
								$('#rifci_val').text('');
								$('#cliprotipo').val('1');

								$('#direc1').val('');
								$('#direc1_val').text('');
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
				$('#clipro').attr("readonly", "readonly");

				$('#nombre').val(ui.item.nombre);
				$('#clipro').val(ui.item.clipro);
				$('#cliprotipo').val('1');
				$('#direc1').val(ui.item.direc);

				setTimeout(function(){ $('#clipro').removeAttr("readonly"); }, 1500);
				post_modbus_scli();
			}
		<?php } ?>
	});
});

function OnEnter(e,ind){
	var keynum;
	var keychar;
	var numcheck;

	if(window.event){ //IE
		keynum = e.keyCode;
	}else if(e.which){ //Netscape/Firefox/Opera
		keynum = e.which;
	}
	if(keynum==13){
		//dacodigo(ind);
		return false;
	}

	//keychar = String.fromCharCode(keynum);
	return true;
}


//Agrega el autocomplete
function autocod(id){
	var ancho='.ui-autocomplete-input#codigoa_'+id;
	var ihtml;
	$('#codigo_'+id).autocomplete({
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
							$('#codigo_'+id).val('')
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
			$('#codigo_'+id).attr("readonly", "readonly");
			if(ui.item.tipo.substr(0,1)=='C'){
				return true;
			}else{

				$('#codigo_'+id).val(ui.item.codigo);
				$('#desca_'+id).val(ui.item.descrip);
				$('#precio1_'+id).val(ui.item.base1);
				$('#precio2_'+id).val(ui.item.base2);
				$('#precio3_'+id).val(ui.item.base3);
				$('#precio4_'+id).val(ui.item.base4);
				$('#itiva_'+id).val(ui.item.iva);
				$('#sinvtipo_'+id).val(ui.item.tipo);
				$('#sinvpeso_'+id).val(ui.item.peso);
				$('#itpvp_'+id).val(ui.item.base1);
				$('#costo_'+id).val(ui.item.pond);
				$('#cana_'+id).val('1');
				$('#cana_'+id).focus();
				$('#cana_'+id).select();
				post_modbus_sinv(Number(id));

			}
			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		},
		open: function() { $('#codigo_'+id).autocomplete("widget").width(420) }
	})
	.data( "ui-autocomplete" )._renderItem = function( ul, item ) {
		return $( "<li>" )
		.append( "<a><table style='width:100%;border-collapse:collapse;padding:0px;'><tr><td colspan='6' style='font-size:14px;color:#0B0B61;'><b>" + item.descrip + "</b></td></tr><tr><td>Codigo:</td><td>" + item.codigo + "</td><td>Precio: </td><td><b>" + item.base1 + "</b></td><td>Existencia:</td><td>" + item.existen + "</td><td></td></tr></table></a>" )
		.appendTo( ul );
	};
}

function importe(id){
	var ind     = id.toString();
	var cana    = Number($("#cana_"+ind).val());
	var precio   = Number($("#precio_"+ind).val());
	var importe = roundNumber(cana*precio,2);
	$("#importe_"+ind).val(importe);

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

function add_itscon(){
	var htm = <?php echo $campos; ?>;
	can = itscon_cont.toString();
	con = (itscon_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PTPL__").after(htm);
	$("#cana_"+can).numeric(".");
	itscon_cont=itscon_cont+1;
	autocod(can);
	$('#codigo_'+can).focus();
}

function post_precioselec(ind,obj){
	if(obj.value=='o'){
		otro = prompt('Precio nuevo','');
		otro = Number(otro);
		if(!otro){
			obj.selectedIndex=0;
		}else{
			if(otro>0){
				var opt=document.createElement("option");
				opt.text = nformat(otro,2);
				opt.value= otro;
				obj.add(opt,null);
				obj.selectedIndex=obj.length-1;
			}
		}
	}
	importe(ind);
}

function post_modbus_scli(){
	var tipo  =Number($("#cliprotipo").val()); if(tipo>0) tipo=tipo-1;

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
	$('#direc1_val').text($('#direc1').val());
	$('#nombre_val').text($('#nombre').val());
	$('#rifci_val').text($('#rifci').val());
}

function post_modbus_sinv(nind){
	ind=nind.toString();
	var tipo =Number($("#cliprotipo").val()); if(tipo>0) tipo=tipo-1;
	$("#precio_"+ind).empty();
	var arr=$('#precio_'+ind);
	cdropdown(nind);
	jQuery.each(arr, function() { this.selectedIndex=tipo; });
	importe(nind);
	totalizar();
}

function cdropdown(nind){
	var ind=nind.toString();
	var preca=$("#precio_"+ind).val();
	var pprecio  = document.createElement("select");

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

	$("#precio_"+ind).replaceWith(pprecio);
}

function del_itscon(id){
	id = id.toString();
	$('#tr_itscon_'+id).remove();
	totalizar();
}
</script>
<?php } ?>

<table align='center' width="95%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<th colspan='5' class="littletableheader">Pr&eacute;stamo de Mercanc&iacute;a <b><?php if($form->_status=='show' or $form->_status=='modify' ) echo str_pad($form->numero->output,8,0,0); ?></b></th>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->tipod->label       ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->tipod->output      ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->clipro->label;  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->clipro->output,$form->cliprotipo->output,$form->nombre->output; ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fecha->label;   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->dir_clipro->label  ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->dir_clipro->output ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader">          <?php echo $form->asociado->label;  ?>&nbsp;</td>
				<td class="littletablerow" align="left"><?php echo $form->asociado->output; ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->almacen->label;     ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->almacen->output;    ?>&nbsp;</td>
			</tr>
		</table><br>
		</td>
	</tr>
	<tr>
		<td>
		<table width='100%'>
			<tr  id='__PTPL__'>
				<td class="littletableheader">C&oacute;digo</td>
				<td class="littletableheader">Descripci&oacute;n</td>
				<td class="littletableheader">Cantidad</td>
				<td class="littletableheader">Precio</td>
				<td class="littletableheader">Importe</td>
				<?php if($form->_status!='show') {?>
					<td class="littletableheader"><a href='#' onclick="add_itscon()" title='Agregar otro pago'><?php echo img('images/agrega4.png'); ?></a></td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itscon'];$i++) {
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

			<tr id='tr_itscon_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" nowrap><?php echo $form->$it_codigo->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_desca->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_cana->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_precio->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_importe->output.$pprecios;?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow">
					<a href='#' onclick='del_itscon(<?php echo $i; ?>);return false;'><?php echo img("images/delete.jpg"); ?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
		</table>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		</td>
	</tr>
	<tr>
		<td>
		<table width='100%'>
			<tr>
				<td class="littletableheader" colspan='4'>Res&uacute;men financiero</td>
			</tr><tr>
				<td><b><?php echo $form->peso->label;    ?></b></td>
				<td><?php echo $form->peso->output;   ?></td>
				<td><b><?php echo $form->stotal->label;  ?></b></td>
				<td align='right' style='font-size:1.2em'><?php echo $form->stotal->output;  ?></td>
			</tr><tr>
				<td><b><?php echo $form->observ1->label; ?></b></td>
				<td><?php echo $form->observ1->output;?></td>
				<td><b><?php echo $form->impuesto->label; ?></b></td>
				<td align='right' style='font-size:1.1em'><?php echo $form->impuesto->output;?></td>
			</tr><tr>
				<td></td>
				<td></td>
				<td><b><?php echo $form->gtotal->label;  ?></b></td>
				<td align='right' style='font-size:2em'><?php echo $form->gtotal->output;  ?></td>
			</tr>
		</table>
		<?php echo $form_end; ?>
		</td>
	</tr>
</table>
<?php endif; ?>
