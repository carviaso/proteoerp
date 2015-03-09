<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itretc');
$scampos  ='<tr id="tr_itretc_<#i#>">';
$scampos .='<td class="littletablerow" align="left" ><b id="tipo_doc_val_<#i#>"></b>'.$campos['it_tipo_doc']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['it_numero']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right"><b id="gtotal_val_<#i#>"></b>'.$campos['it_gtotal']['field'].$campos['it_impuesto']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['it_base']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['it_codigorete']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['it_monto']['field'].'</td>';
$scampos .='<td class="littletablerow"><a href=# onclick="del_itretc(<#i#>);return false;">'.img('images/delete.jpg').'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
$anulado='N';
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itretc_cont =<?php echo $form->max_rel_count['itretc']; ?>;

$(function(){
	$("#emision").datepicker({ dateFormat: "dd/mm/yy" });
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	com=false;
	$(document).keydown(function(e){
		if (18 == e.which) {
			com=true;
			return false;
		}
		if (com && (e.which == 61 || e.which == 107)) {
			add_itretc();
			com=false;
			return false;
		}else if (com && e.which != 16 && e.which == 17){
			com=false;
		}
		return true;
	});


	$(".inputnum").numeric(".");
	totalizar();
	for(var i=0;i < <?php echo $form->max_rel_count['itretc']; ?>;i++){
		autocod(i.toString());
	}

	$('#cod_cli').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('finanzas/retc/buscascli'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#nombre').val('');
							$('#nombre_val').text('');

							$('#rif').val('');
							$('#rif_val').text('');
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
			$('#cod_cli').attr("readonly", "readonly");
			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);
			$('#rif').val(ui.item.rifci);
			$('#rif_val').text(ui.item.rifci);
			$('#cod_cli').val(ui.item.cod_cli);
			setTimeout(function() {  $("#cod_cli").removeAttr("readonly"); }, 1500);
		}
	});

});

function totalizar(){
	var gtotal    =0;
	var impuesto  =0;
	var monto     =0;
	var itmonto   =0;
	var itimpuesto=0;
	var itgtotal  =0;

	var arr=$('input[name^="itmonto_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind       = this.name.substring(pos+1);
			ittipo_doc= $("#tipo_doc_"+ind).val();

			itmonto   = Number(Math.abs(this.value));
			itimpuesto= Number(Math.abs($("#impuesto_"+ind).val()));
			itgtotal  = Number(Math.abs($("#gtotal_"+ind).val()));
			if(ittipo_doc=='D' || ittipo_doc=='NC' ){
				itmonto   = (-1)*itmonto;
				itimpuesto= (-1)*itimpuesto;
				itgtotal  = (-1)*itgtotal;
			}

			impuesto=impuesto+itimpuesto;
			monto   =monto+itmonto;
			gtotal  =gtotal+itgtotal;
		}
	});

	$("#monto").val(roundNumber(monto,2));
	$("#impuesto").val(roundNumber(impuesto,2));
	$("#gtotal").val(roundNumber(gtotal,2));

	$('#monto_val').text(nformat(monto,2));
	$('#impuesto_val').text(nformat(impuesto,2));
	$('#gtotal_val').text(nformat(gtotal,2));
}

function add_itretc(){
	var htm = <?php echo $campos; ?>;
	can = itretc_cont.toString();
	con = (itretc_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL__").after(htm);
	$("#itmonto_"+can).numeric(".");
	autocod(can);
	$('#numero_'+can).focus();

	itretc_cont=itretc_cont+1;
}


function del_itretc(id){
	id = id.toString();
	$('#tr_itretc_'+id).remove();
	totalizar();
}

function post_codigoreteselec(){

}

//Agrega el autocomplete
function autocod(id){
	$('#numero_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('finanzas/retc/buscasfac'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+encodeURIComponent(req.term)+"&scli="+encodeURIComponent($("#cod_cli").val()),
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#tipo_doc_'+id).val('');
							$('#tipo_doc_val_'+id).text('');

							$('#gtotal_'+id).val('');
							$('#gtotal_val_'+id).text('');

							$('#impuesto_'+id).val('');
							$('#impuesto_val_'+id).text('');

							$('#itmonto_'+id).val('');

							$('#fecha_'+id+'_val').text('');
							$('#fecha_'+id).val('');

							totalizar();
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
		minLength: 4,
		select: function( event, ui ) {
			$('#numero_'+id).attr("readonly", "readonly");

			$('#tipo_doc_'+id).val(ui.item.tipo_doc);
			$('#tipo_doc_val_'+id).text(ui.item.tipo_doc);

			$('#numero_'+id).val(ui.item.value);

			$('#gtotal_'+id).val(ui.item.gtotal);
			$('#gtotal_val_'+id).text(nformat(ui.item.gtotal,2));

			$('#impuesto_'+id).val(ui.item.impuesto);
			$('#impuesto_val_'+id).text(nformat(ui.item.impuesto,2));

			totalizar();
			setTimeout(function(){  $('#numero_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}
</script>
<?php }else $anulado=$form->get_from_dataobjetct('anulado'); ?>

<table width='100%' align='center'>
	<tr>
		<td align=right>
			<?php echo $container_tr; ?>
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%"  style="margin:0;width:100%;" cellspacing='2' cellpadding='2'>
				<tr>
					<td colspan=11 class="littletableheader"><?php echo ($anulado=='S')? '<b style=\'color:red;\'>Documento Anulado<b>' : 'Encabezado'; ?></td>
				</tr>
				<tr>
					<td>
					<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;min-height:100px;min-width:300px;'>
						<table>
							<tr>
								<td class="littletablerowth"><?php echo $form->cod_cli->label  ?>*</td>
								<td class="littletablerow">  <?php echo $form->cod_cli->output ?></td>
							</tr><tr>
								<td class="littletablerowth"><?php echo $form->rif->label  ?>*</td>
								<td class="littletablerow">  <b id='rif_val'><?php echo $form->rif->value; ?></b><?php echo $form->rif->output ?></td>
							</tr><tr>
								<td class="littletablerowth"><?php echo $form->nombre->label  ?></td>
								<td class="littletablerow">  <b id='nombre_val'><?php echo $form->nombre->value ?></b><?php echo $form->nombre->output ?></td>
							</tr>
						</table>
					</fieldset>
					</td><td>
					<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;min-height:100px;'>
						<table>
							<tr>
								<td class="littletablerowth"><?php echo $form->emision->label  ?>*</td>
								<td class="littletablerow">  <?php echo $form->emision->output ?></td>
							</tr><tr>
								<td class="littletablerowth"><?php echo $form->fecha->label  ?>*</td>
								<td class="littletablerow">  <?php echo $form->fecha->output ?></td>
							</tr><tr>
								<td class="littletablerowth"><?php echo $form->operacion->label  ?></td>
								<td class="littletablerow">  <?php echo $form->operacion->output ?></td>
							</tr>
						</table>
					</fieldset>
					</td>
				</tr>
			</table>
		</tr>
	<tr>
</table>

		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:190px'>
		<table width='100%'>
			<tr id='__INPL__'>
				<th bgcolor='#7098D0'>Tipo    </th>
				<th bgcolor='#7098D0'>N&uacute;mero</th>
				<th bgcolor='#7098D0'>Monto del Efecto</th>
				<th bgcolor='#7098D0'>Base</th>
				<th bgcolor='#7098D0'>Concepto</th>
				<th bgcolor='#7098D0'>Monto retenido</th>
				<?php if($form->_status!='show') {?>
					<th bgcolor='#7098D0'>&nbsp;</th>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itretc'];$i++) {
				$it_tipo_doc  = "it_tipo_doc_$i";
				$it_numero    = "it_numero_$i";
				$it_gtotal    = "it_gtotal_$i";
				$it_impuesto  = "it_impuesto_$i";
				$it_monto     = "it_monto_$i";
				$it_codigorete= "it_codigorete_$i";
				$it_base      = "it_base_$i";
			?>

			<tr id='tr_itretc_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" >
					<b id='tipo_doc_val_<?php echo $i ?>'><?php echo $form->$it_tipo_doc->value; ?></b><?php echo $form->$it_tipo_doc->output; ?>
				</td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_numero->output;   ?></td>
				<td class="littletablerow" align="right">
					<b id='gtotal_val_<?php echo $i ?>'><?php echo $form->$it_gtotal->value; ?></b>
					<?php echo $form->$it_gtotal->output.$form->$it_impuesto->output;    ?>
				</td>
				<td class="littletablerow" align="right"><?php echo $form->$it_base->output;       ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_codigorete->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_monto->output;      ?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow">
					<a href='#' onclick='del_itretc(<?php echo $i ?>);return false;'><?php echo img('images/delete.jpg');?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
		</table>
		</div>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		<br>

		<table  width="100%" style="margin:0;width:100%;" >
			<tr>
				<td colspan=10 class="littletableheader">Totales</td>
			</tr><tr>
				<td class="littletablerowth" align='left' ><?php echo $form->codbanc->label;   ?></td>
				<td class="littletablerow"   align='left' ><?php echo $form->codbanc->output;  ?></td>
				<td class="littletablerowth" align='right'><?php echo $form->gtotal->label;    ?></td>
				<td class="littletablerow"   align='right'><b id='gtotal_val'><?php echo nformat($form->gtotal->value);     ?></b><?php echo $form->gtotal->output;   ?></td>
			</tr><tr>
				<td class="littletablerowth" align='left' ></td>
				<td class="littletablerow"   align='left' ></td>
				<td class="littletablerowth" align='right'><?php echo $form->impuesto->label;  ?></td>
				<td class="littletablerow"   align='right'><b id='impuesto_val'><?php echo nformat($form->impuesto->value); ?></b><?php echo $form->impuesto->output; ?></td>
			</tr><tr>
				<td class="littletablerowth" align='left' ></td>
				<td class="littletablerow"   align='left' ></td>
				<td class="littletablerowth" align='right'><?php echo $form->monto->label; ?></td>
				<td class="littletablerow"   align='right'><b id='monto_val'><?php echo nformat($form->monto->value);       ?></b><?php echo $form->monto->output;    ?></td>
			</tr>
		</table>

	  <td>
	<tr>
<table>
<?php echo $form_end?>

<?php endif; ?>
