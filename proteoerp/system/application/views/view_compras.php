<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itscst');
$scampos  ='<tr id="tr_itscst_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" ><b id="it_descrip_val_<#i#>"></b>'.$campos['descrip']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['cantidad']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['costo']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['importe']['field'];
$scampos .= $campos['sinvpeso']['field'].$campos['iva']['field'].'</td>';
$scampos .='<td class="littletablerow"><a href=# onclick="del_itscst(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itscst_cont =<?php echo $form->max_rel_count['itscst']; ?>;
var tasa_general=<?php echo $alicuota['tasa'];     ?>;
var tasa_reducid=<?php echo $alicuota['redutasa']; ?>;
var tasa_adicion=<?php echo $alicuota['sobretasa'];?>;
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

	$('input[name^="cantidad_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itscst();
			return false;
		}
	});

	$('#proveed').autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasprv'); ?>",
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
			$("#proveed").attr("readonly", "readonly");
			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);
			$('#proveed').val(ui.item.proveed);
			$('#sprvreteiva').val(ui.item.reteiva);
			setTimeout(function() {  $("#proveed").removeAttr("readonly"); }, 1500);
		}
	});
});

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
	totalizar();
}

function totalizar(){
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
	var tolera   =0.07

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
	$contribu= $this->datasis->traevalor('CONTRIBUYENTE');
	$rif     = $this->datasis->traevalor('RIF');
	if($contribu=='ESPECIAL' && strtoupper($rif[0])!='V'){
		echo "\t".'$("#reteiva").val(roundNumber(montoiva*porreten/100,2));';
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
		    add_itscst();
			return false;
		}
	});
	$("#costo_"+can).numeric(".");
	$("#importe_"+can).numeric(".");
	autocod(can);
	$('#codigo_'+can).focus();

	itscst_cont=itscst_cont+1;
}

function post_modbus_sinv(nind){
	ind=nind.toString();
	var cana=Number($("#cantidad_"+ind).val());
	if(cana<=0) $("#cantidad_"+ind).val(1);
	$('#cantidad_'+ind).focus();
	$('#cantidad_'+ind).select();
	$('#it_descrip_val_'+ind).text($('#descrip_'+ind).val());
	importe(nind);
	totalizar();
}

function cmontotot(){
	if(ctimeout > 0) clearTimeout(ctimeout);
	ctimeout=setTimeout('timecmontotot();', 1000);
}

function timecmontotot(){
	var totals  = 0;
	var vimporte = $("#montotot").val();
	var iva      = Number($("#montoiva").val());
	var arr=$('input[name^="importe_"]');
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

	$("#montonet_val").text(nformat(totals+iva,2));
	$("#montonet").val(totals+iva);

}

function cmontoiva(){
	var totals = Number($("#montotot").val());
	var iva    = Number($("#montoiva").val());

	$("#montonet_val").text(nformat(totals+iva,2));
	$("#montonet").val(totals+iva);
}

function post_modbus_sprv(){
	$('#nombre_val').text($('#nombre').val());
}

function del_itscst(id){
	id = id.toString();
	$('#tr_itscst_'+id).remove();
	totalizar();
}

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinvart'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
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
			if(cana<=0) $("#cantidad_"+id).val('1');
			$('#cantidad_'+id).focus();
			$('#cantidad_'+id).select();
			//post_modbus_sinv(parseInt(id));
			importe(parseInt(id));
			//totalizar();
			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}
</script>
<?php } ?>

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
			<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
			<table width="100%"  style="margin:0;width:100%;" cellspacing='2' cellpadding='2'>
				<tr>
					<td colspan="3">
						<table width="100%">
							<tr>
								<td class="littletablerowth" width='40'><?php echo $form->tipo->label  ?></td>
								<td class="littletablerow"   align='left' width='150'>  <?php echo $form->tipo->output ?></td>
								<td class="littletablerowth" align='right' width='100'><?php echo $form->proveed->label  ?>*</td>
								<td class="littletablerow">
									<?php echo $form->proveed->output ?>
									<b id='nombre_val'><?php echo $form->nombre->value ?></b>
									<?php echo $form->nombre->output.$form->sprvreteiva->output ?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style='border: 1px solid grey;'>
						<table width='100%'>
							<tr>
								<td class="littletablerowth"><?php echo $form->fecha->label  ?></td>
								<td class="littletablerow">  <?php echo $form->fecha->output ?></td>
							</tr><tr>
								<td class="littletablerowth"><?php echo $form->serie->label  ?></td>
								<td class="littletablerow">  <?php echo $form->serie->output ?></td>
							</tr>
						</table>
					</td><td style='border: 1px solid grey;'>
						<table width='100%'>
							<tr>
								<td class="littletablerowth"><?php echo $form->cfis->label  ?>*</td>
								<td class="littletablerow">  <?php echo $form->cfis->output ?></td>
							</tr><tr>
								<td class="littletablerowth"><?php echo $form->almacen->label  ?>*</td>
								<td class="littletablerow">  <?php echo $form->almacen->output ?></td>
							</tr>
						</table>
					</td><td style='border: 1px solid grey;'>
						<table width='100%'>
							<tr>
								<td class="littletablerowth"><?php echo $form->vence->label ?></td>
								<td class="littletablerow">  <?php echo $form->vence->output ?></td>
							</tr><tr>
								<td class="littletablerowth"><?php echo $form->actuali->label  ?></td>
								<td class="littletablerow">  <?php echo $form->actuali->output ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</fieldset>

		</tr>
	<tr>
</table>

		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:200px'>
		<table width='100%'>
			<tr id='__INPL__'>
				<th bgcolor='#7098D0'>C&oacute;digo     </th>
				<th bgcolor='#7098D0'>Descripci&oacute;n</th>
				<th bgcolor='#7098D0'>Cantidad          </th>
				<th bgcolor='#7098D0'>Precio            </th>
				<th bgcolor='#7098D0'>Importe           </th>
				<?php if($form->_status!='show') {?>
					<th bgcolor='#7098D0'>&nbsp;</th>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itscst'];$i++) {
				$it_codigo  = "codigo_$i";
				$it_desca   = "descrip_$i";
				$it_cana    = "cantidad_$i";
				$it_precio  = "costo_$i";
				$it_importe = "importe_$i";
				$it_peso    = "sinvpeso_$i";
				$it_iva     = "iva_$i";
				//$it_tipo    = "sinvtipo_$i";
			?>

			<tr id='tr_itscst_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output; ?></td>
				<td class="littletablerow" align="left" ><b id='it_descrip_val_<?php echo $i; ?>'><?php echo $form->$it_desca->value; ?></b>
				<?php echo $form->$it_desca->output;  ?>
				</td>
				<td class="littletablerow" align="right"><?php echo $form->$it_cana->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_precio->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_importe->output; ?>
				<?php echo $form->$it_peso->output.$form->$it_iva->output; ?>
				</td>
				<?php if($form->_status!='show') {?>
				<td class="littletablerow">
					<a href='#' onclick='del_itscst(<?php echo $i ?>);return false;'><?php echo img("images/delete.jpg");?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
		</table>
		</div>
<?php
/*
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		<br>
*/
?>

<table  width="100%" style="margin:0;width:100%;" border='0'>
<?php
//	<tr>
//		<td colspan=10 class="littletableheader">Totales</td>
//	</tr>
?>
	<tr>
		<td width="131" class="littletablerowth" align='right'><?php echo $container_bl ?></td>
<?php /*		<td width="131" class="littletablerowth" align='right'><?php echo $form->rislr->label;     ?></td>*/?>
		<td width="122" class="littletablerow"   align='right'><?php echo $form->rislr->output;    ?></td>
		<td width="125" class="littletablerowth" align='right'><?php echo $form->anticipo->label;  ?></td>
		<td width="125" class="littletablerow"   align='right'><?php echo $form->anticipo->output; ?></td>
		<td width="111" class="littletablerowth" align='right'><?php echo $form->montotot->label;  ?></td>
		<td width="139" class="littletablerow"   align='right'><?php echo $form->montotot->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth" align='right'><?php echo $form->riva->label;      ?></td>
		<td class="littletablerow"   align='right'><?php echo $form->riva->output;     ?></td>
		<td class="littletablerowth" align='right'><?php echo $form->inicial->label;   ?></td>
		<td class="littletablerow"   align='right'><?php echo $form->inicial->output;  ?></td>
		<td class="littletablerowth" align='right'><?php echo $form->montoiva->label;  ?></td>
		<td class="littletablerow"   align='right'><?php echo $form->montoiva->output; ?></td>
	</tr>

	<tr>
		<td class="littletablerowth" align='right'><?php echo $form->mdolar->label;   ?></td>
		<td class="littletablerow"   align='right'><?php echo $form->mdolar->output;  ?></td>
		<td class="littletablerowth" align='right'><?php echo $form->credito->label;  ?></td>
		<td class="littletablerow"   align='right'><?php echo $form->credito->output; ?></td>
		<td class="littletablerowth" align='right'><?php echo $form->montonet->label; ?></td>
		<td class="littletablerow"   align='right'><b id='montonet_val' style='font-size:18px;font-weight: bold' ><?php echo nformat($form->montonet->value); ?></b><?php echo $form->montonet->output; ?></td>
	</tr>
</table>
<table width="100%">
	<tr>
		<td class="littletableheader" width="100"><?php echo $form->observa1->label;    ?></td>
		<td><?php echo $form->observa1->output;   ?><?php echo $form->observa2->output;   ?><?php echo $form->observa3->output;?></td>
	</tr>
</table>

		<td>
	<tr>
<table>

<?php echo $form_end?>
<?php endif; ?>
