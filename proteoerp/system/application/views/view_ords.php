<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itordc_cont=<?php echo $form->max_rel_count['itords']; ?>;
$(function(){
	$(".inputnum").numeric(".");
	totalizar();

	$("#fecha").datepicker({    dateFormat: "dd/mm/yy" });
	for(var i=0;i < <?php echo $form->max_rel_count['itords']; ?>;i++){
		autocod(i.toString());
	}

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
			setTimeout(function() {  $("#proveed").removeAttr("readonly"); }, 1500);
		}
	});
});

function totalizar(){
	tp=tb=ti=ite=0;

	arr=$('input[name^="importe_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind = this.name.substring(pos+1);
			tp1=Number($("#precio_"+ind).val());
			ite=Number(this.value);

			tp=tp+tp1;
			tb=tb+ite;
		}
	});

	$("#totpre").val(roundNumber(tp,2));
	$("#totpre_val").text(nformat(tp,2));
	$("#totbruto").val(roundNumber(tb,2));
	$("#totbruto_val").text(nformat(tb,2));
	totiva=roundNumber(tb-tp,2);
	$("#totiva").val(totiva);
	$("#totiva_val").text(nformat(totiva,2));
}

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/automgas'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q" :req.term},
				success:
					function(data){
						var sugiere = [];

						if(data.length==0){
							$('#codigo_'+id).val('');
							$('#descrip_'+id).val('');
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
			$('#codigo_'+id).attr("readonly", "readonly");

			$('#codigo_'+id).val(ui.item.codigo);
			$('#descrip_'+id).val(ui.item.descrip);
			$('#precio_'+id).focus();
			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}
</script>
<?php } ?>
<table align='center' width="100%" >
	<tr>
	<td>
		<table width="100%"  style="margin:0;width:100%;">
		<tr>
			<td class="littletablerowth"><?php echo $form->proveed->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->proveed->output; ?></td>
			<td class="littletablerowth"><?php echo $form->nombre->label;     ?></td>
			<td class="littletablerow"  ><?php echo $form->nombre->output;    ?></td>
		</tr><tr>
			<td class="littletablerowth"><?php echo $form->fecha->label;      ?></td>
			<td class="littletablerow"  ><?php echo $form->fecha->output;     ?></td>
			<td class="littletablerowth"><?php echo $form->numero->label;     ?></td>
			<td class="littletablerow"  ><?php echo $form->numero->output;    ?></td>
		</tr>
		</table>


		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:250px'>
		<table width='100%'>
			<tr  id='__PTPL__'>
				<td class="littletableheaderdet">C&oacute;digo</td>
				<td class="littletableheaderdet">Descripci&oacute;n</td>
				<td class="littletableheaderdet">Precio</td>
				<td class="littletableheaderdet">Impuesto</td>
				<td class="littletableheaderdet">Importe</td>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itords'];$i++){
				$it_codigo  = "codigo_${i}";
				$it_descrip = "descrip_${i}";
				$it_precio  = "precio_${i}";
				$it_iva     = "iva_${i}";
				$it_importe = "importe_${i}";
			?>

			<tr id='tr_itsnot_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output;  ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_descrip->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_precio->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_iva->output;     ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_importe->output; ?></td>
			</tr>
			<?php } ?>
			<tr id='__UTPL__'>
				<td colspan='5'></td>
			</tr>
		</table>
		</div>
<?php echo $container_bl.$container_br; ?>
		<legend class="titulofieldset" style='color: #114411;'>Totales</legend>
			<table width='100%'>
				<tr>
					<td class="littletableheader" align='right'><?php echo $form->totpre->label  ?>&nbsp;</td>
					<td class="littletablerow"    align='right'><?php echo $form->totpre->output; ?>&nbsp;</td>
				</tr><tr>
					<td class="littletableheader" align='right'><?php echo $form->totiva->label  ?>&nbsp;</td>
					<td class="littletablerow"    align='right'><?php echo $form->totiva->output ?>&nbsp;</td>
				</tr><tr>
					<td class="littletableheader" align='right'><?php echo $form->totbruto->label  ?>&nbsp;</td>
					<td class="littletablerow"    align='right'><?php echo $form->totbruto->output ?>&nbsp;</td>
				</tr>
			</table>
		</fieldset>

	  <td>
	<tr>
<table>
<?php echo $form_end; ?>
<?php endif; ?>
<p>&nbsp;</p>
