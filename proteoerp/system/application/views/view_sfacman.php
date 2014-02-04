<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('sitems');
$scampos  ='<tr id="tr_itspre_<#i#>">';
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
$scampos .= $campos['sinvpeso']['field'].'</td>';
$scampos .= '<td class="littletablerow"><a href=# onclick="del_itspre(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var sitems_cont=<?php echo $form->max_rel_count['sitems']; ?>;

$(function(){
	$(".inputnum").numeric(".");
	totalizar();
	for(var i=0;i < <?php echo $form->max_rel_count['sitems']; ?>;i++){
		cdropdown(i);
		cdescrip(i);
		autocod(i.toString());
	}

	$('#cod_cli').autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscascli'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
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
			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);

			$('#rifci').val(ui.item.rifci);
			$('#rifci_val').text(ui.item.rifci);

			$('#cod_cli').val(ui.item.cod_cli);
			$('#sclitipo').val(ui.item.tipo);

			$('#direc').val(ui.item.direc);
			$('#direc_val').text(ui.item.direc);
		}
	});

	$('#mandatario').autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscascli'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#mandanombre').val('');
							$('#mandanombre_val').text('');

							$('#mandarif').val('');
							$('#mandarif_val').text('');

							$('#mandadirec').val('');
							$('#mandadirec_val').text('');
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
			$('#mandatario').val(ui.item.cod_cli);

			$('#mandanombre').val(ui.item.nombre);
			$('#mandanombre_val').text(ui.item.nombre);

			$('#mandarif').val(ui.item.rifci);
			$('#mandarif_val').text(ui.item.rifci);

			$('#mandadirec').val(ui.item.direc);
			$('#mandadirec_val').text(ui.item.direc);
		}
	});
});

function importe(id){
	var ind     = id.toString();
	var cana    = Number($("#cana_"+ind).val());
	var preca   = Number($("#preca_"+ind).val());
	var importe = roundNumber(cana*preca,2);
	$("#tota_"+ind).val(importe);

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

			peso    = peso+(itpeso*cana);
			iva     = iva+importe*(itiva/100);
			totals  = totals+importe;
		}
	});
	$("#peso").val(roundNumber(peso,2));
	$("#totalg").val(roundNumber(totals+iva,2));
	$("#totals").val(roundNumber(totals,2));
	$("#iva").val(roundNumber(iva,2));
	$("#totalg_val").text(nformat(totals+iva,2));
	$("#totals_val").text(nformat(totals,2));
	$("#ivat_val").text(nformat(iva,2));
	autocod(0);
}

function add_sitems(){
	var htm = <?php echo $campos; ?>;
	can = sitems_cont.toString();
	con = (sitems_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL__").after(htm);
	$("#cana_"+can).numeric(".");
	autocod(can);
	$('#codigo_'+can).focus();
	sitems_cont=sitems_cont+1;
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
			tipo=$('#sinvtipo_'+id).val();
			if(tipo!='Servicio'){
				this.selectedIndex=tipo;
				importe(id);
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
	$('#codigoa_'+id).autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinv'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
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
			//id='0';
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

			var arr  = $('#preca_'+id);
			var tipo = Number($("#sclitipo").val()); if(tipo>0) tipo=tipo-1;
			cdropdown(id);
			cdescrip(id);
			jQuery.each(arr, function() { this.selectedIndex=tipo; });
			importe(id);
			totalizar();
		}
	});
}

function del_itspre(id){
	id = id.toString();
	$('#tr_itspre_'+id).remove();
	totalizar();
}
</script>
<?php } ?>
<table align='center' width="95%">
	<tr>
<?php if ($form->_status=='show') { ?>
		<td>
		<a href="#" onclick="window.open('<?php echo base_url() ?>formatos/verhtml/FACTURATER/<?php echo $form->numero->value.'/'.$form->tipo_doc->value ?>', '_blank', 'width=800, height=600, scrollbars=Yes, status=Yes, resizable=Yes, screenx='+((screen.availWidth/2)-400)+',screeny='+((screen.availHeight/2)-300)+'');" heigth="600" >
		<img src='<?php echo base_url() ?>images/html_icon.gif'></a>
		</td>
<?php } ?>
		<td align=right><?php echo $container_tr?></td>
	</tr>
</table>
<table align='center' width="100%">
	<tr>
		<td>
		<table width='100%'><tr><td>
			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9; min-height:105px;'>
			<legend class="titulofieldset" style='color: #114411;'>Documento</legend>
			<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->nfiscal->label; ?></td>
				<td class="littletablerow">   <?php echo $form->nfiscal->output; ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->numero->label  ?></td>
				<td class="littletablerow" align="left"><?php echo $form->numero->output; ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fecha->label;    ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output;   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->vence->label     ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->vence->output    ?>&nbsp;</td>
			</tr>
			</table>
			</fieldset>
		</td><td>
			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9; min-height:105px;'>
			<legend class="titulofieldset" style='color: #114411;'>Cliente</legend>
			<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->cliente->label;  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->cliente->output,$form->sclitipo->output; ?>&nbsp;</td>
				<td class="littletablerow">   <b id='nombre_val'><?php echo $form->nombre->value; ?></b><?php echo $form->nombre->output;  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->rifci->label; ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><b id='rifci_val'><?php echo $form->rifci->value; ?></b><?php echo $form->rifci->output;   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->direc->label  ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><b id='direc_val'><?php echo $form->direc->value; ?></b><?php echo $form->direc->output ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->vd->label     ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->vd->output    ?>&nbsp;</td>
			</tr>
			</table>
			</fieldset>
		</td><td>
			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9; min-height:105px;'>
			<legend class="titulofieldset" style='color: #114411;'>Mandatario</legend>
			<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->mandatario->label;  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->mandatario->output; ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->mandanombre->output;?>&nbsp;</td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->mandarif->label; ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->mandarif->output;   ?>&nbsp;</td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->mandadirec->label  ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->mandadirec->output ?>&nbsp;</td>
			</tr>
			</table>
			</fieldset>
		</td><tr></table>
		<br>
		</td>
	</tr><tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:200px'>
		<table width='100%' border='0'>
			<tr id='__INPL__'>
				<td bgcolor='#7098D0'><strong>C&oacute;digo</strong></td>
				<td bgcolor='#7098D0'><strong>Descripci&oacute;n</strong></td>
				<td bgcolor='#7098D0'><strong>Cantidad</strong></td>
				<td bgcolor='#7098D0'><strong>Precio</strong></td>
				<td bgcolor='#7098D0'><strong>Importe</strong></td>
				<?php if($form->_status!='show') {?>
					<td bgcolor='#7098D0'>&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['sitems'];$i++) {
				$it_codigo  = "codigoa_$i";
				$it_desca   = "desca_$i";
				$it_cana    = "cana_$i";
				$it_preca   = "preca_$i";
				$it_importe = "tota_$i";
				$it_iva     = "itiva_$i";
				$it_peso    = "sinvpeso_$i";
				$it_tipo    = "sinvtipo_$i";
				$it_ultimo  = "ultimo_$i";
				$it_detalle = "detalle_$i";
				$it_pond    = "pond_$i";
				$pprecios='';
				for($o=1;$o<5;$o++){
					$it_obj   = "precio${o}_${i}";
					$pprecios.= $form->$it_obj->output;
				}

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
				<td class="littletablerow">
					<a href='#' onclick='del_itspre(<?php echo $i; ?>);return false;'><?php echo img("images/delete.jpg"); ?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>

			<tr id='__UTPL__'>
				<td id='cueca'></td>
			</tr>
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
				<td class="littletableheader" width='100'></td>
				<td class="littletablerow"    width='350'></td>
				<td class="littletableheader">           <?php echo $form->totals->label;  ?></td>
				<td class="littletablerow" align='right'><b id='totals_val'><?php echo nformat($form->totals->value); ?></b><?php echo $form->totals->output; ?></td>

			<tr></tr>
				<td class="littletableheader">&nbsp;</td>
				<td class="littletablerow"   ></td>
				<td class="littletableheader"><?php echo $form->ivat->label;    ?></td>
				<td class="littletablerow" align='right'><b id='ivat_val'><?php echo nformat($form->ivat->value); ?></b><?php echo $form->ivat->output; ?></td>
			<tr></tr>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td class="littletableheader">           <?php echo $form->totalg->label;  ?></td>
				<td class="littletablerow" align='right' style='font-size:18px;font-weight: bold'><b id='totalg_val'><?php echo nformat($form->totalg->value); ?></b><?php echo $form->totalg->output; ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
</table>
<?php echo $form_end; ?>

<?php if($form->_status=='show'){ ?>
<br>
<table  width="100%" style="margin:0;width:100%;" >
	<tr>
		<td colspan=10 class="littletableheader">Movimientos relacionados</td>
	</tr>
	<?php
	$transac=$form->get_from_dataobjetct('transac');

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
<?php  } ?>

<?php endif; ?>
