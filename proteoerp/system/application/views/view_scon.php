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
$scampos .= '<td class="littletablerow"><a href=# onclick="del_itscon(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itscon_cont=<?php echo $form->max_rel_count['itscon']; ?>;
var invent = (<?php echo $inven; ?>);
$(function(){
	$(document).keydown(function(e){
		if (e.which == 13) return false;
	});

	$(".inputnum").numeric(".");
	totalizar();
	for(var i=0;i < <?php echo $form->max_rel_count['itscon']; ?>;i++){
		cdropdown(i);
	}
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
		dacodigo(ind);
		return false;
	}

	//keychar = String.fromCharCode(keynum);
	return true;
}

function dacodigo(nind){
	ind=nind.toString();
	var codigo = $("#codigo_"+ind).val();
	var eeval;
	eval('eeval= typeof invent._'+codigo);

	var descrip='';
	if(eeval != "undefined"){
		eval('descrip=invent._'+codigo+'[0]');
		eval('tipo   =invent._'+codigo+'[1]');
		eval('base1  =invent._'+codigo+'[2]');
		eval('base2  =invent._'+codigo+'[3]');
		eval('base3  =invent._'+codigo+'[4]');
		eval('base4  =invent._'+codigo+'[5]');
		eval('itiva  =invent._'+codigo+'[6]');
		eval('peso   =invent._'+codigo+'[7]');
		eval('precio1=invent._'+codigo+'[8]');
		eval('pond   =invent._'+codigo+'[9]');

		$("#desca_"+ind).val(descrip);
		$("#precio1_"+ind).val(base1);
		$("#precio2_"+ind).val(base2);
		$("#precio3_"+ind).val(base3);
		$("#precio4_"+ind).val(base4);
		$("#itiva_"+ind).val(itiva);
		$("#sinvtipo_"+ind).val(tipo);
		$("#sinvpeso_"+ind).val(peso);
		$("#itpvp_"+ind).val(precio1);
		$("#itcosto_"+ind).val(pond);
	}else{
		$("#desca_"+ind).val('');
		$("#precio1_"+ind).val('');
		$("#precio2_"+ind).val('');
		$("#precio3_"+ind).val('');
		$("#precio4_"+ind).val('');
		$("#itiva_"+ind).val('');
		$("#sinvtipo_"+ind).val('');
		$("#sinvpeso_"+ind).val('');
		$("#itpvp_"+ind).val('');
		$("#itcosto_"+ind).val('');
	}
	post_modbus_sinv(nind);
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
	
}

function add_itscon(){
	var htm = <?php echo $campos; ?>;
	can = itscon_cont.toString();
	con = (itscon_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#cana_"+can).numeric(".");
	itscon_cont=itscon_cont+1;
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
	//var cambio=confirm('�Deseas cambiar los precios por los que tenga asginado el cliente?');

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
				<th colspan='5' class="littletableheader">Pr&eacute;stamo de inventario <b><?php if($form->_status=='show' or $form->_status=='modify' ) echo str_pad($form->numero->output,8,0,0); ?></b></th>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fecha->label;   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->clipro->label;  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->clipro->output,$form->cliprotipo->output,$form->nombre->output; ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->tipod->label  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->tipod->output ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->dir_clipro->label  ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->dir_clipro->output ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader">          <?php echo $form->asociado->label;  ?>&nbsp;</td>
				<td class="littletablerow" align="left"><?php echo $form->asociado->output; ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->observ1->label; ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->observ1->output;?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader">          <?php echo $form->peso->label;  ?>&nbsp;</td>
				<td class="littletablerow" align="left"><?php echo $form->peso->output; ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->almacen->label;     ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->almacen->output;    ?>&nbsp;</td>
			</tr>
		</table><br>
		</td>
	</tr>
	<tr>
		<td>
		<table width='100%'>
			<tr>
				<th colspan='6' class="littletableheader">Lista de Art&iacute;culos</th>
			</tr>
			<tr>
				<td class="littletableheader">C&oacute;digo</td>
				<td class="littletableheader">Descripci&oacute;n</td>
				<td class="littletableheader">Cantidad</td>
				<td class="littletableheader">Precio</td>
				<td class="littletableheader">Importe</td>
				<?php if($form->_status!='show') {?>
					<td class="littletableheader">&nbsp;</td>
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
					<a href='#' onclick='del_itscon(<?php echo $i; ?>);return false;'>Eliminar</a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>

			<tr id='__UTPL__'>
				<td id='cueca'></td>
			</tr>
		</table>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		</td>
	</tr>
	<tr>
		<td>
		<table width='100%'>
			<tr>
				<th colspan='6' class="littletableheader">Res&uacute;men Financiero</th>
			</tr>
			<tr>
				<td class="littletableheader">           <?php echo $form->impuesto->label;    ?></td>
				<td class="littletablerow" align='right'><?php echo $form->impuesto->output;   ?></td>
				<td class="littletableheader">           <?php echo $form->stotal->label;  ?></td>
				<td class="littletablerow" align='right'><?php echo $form->stotal->output; ?></td>
				<td class="littletableheader">           <?php echo $form->gtotal->label;  ?></td>
				<td class="littletablerow" align='right'><?php echo $form->gtotal->output; ?></td>
			</tr>
		</table>
		<?php echo $form_end; ?>
		</td>
	</tr>
</table>
<?php endif; ?>