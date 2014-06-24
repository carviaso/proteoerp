<?php
if($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'){
	echo $form->output;
}else{


	$campos=$form->template_details('smov');
	$scampos  ='<tr id="tr_smov_<#i#>" >';
	$scampos .='<td class="littletablerow" align="left" >'.$campos['tipo_doc']['field'].'</td>';
	$scampos .='<td class="littletablerow" align="left" >'.$campos['numero']['field'].'</td>';
	$scampos .='<td class="littletablerow" align="left" >'.$campos['fechad']['field'].'</td>';
	$scampos .='<td class="littletablerow" align="right">'.$campos['vence']['field'].'</td>';
	$scampos .='<td class="littletablerow" align="right">'.$campos['nombre']['field']. '</td>';
	$scampos .='<td class="littletablerow" align="right">'.$campos['totalg']['field'];
	$scampos .= $campos['itid']['field'].'</td>';
	$scampos .='<td class="littletablerow" align="center"><a href=# onclick="del_smov(<#i#>);return false;">'.img('images/delete.jpg').'</a></td></tr>';
	$campos = $form->js_escape($scampos);

}

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
echo $form_begin;
if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
var smov_cont=<?php echo $form->max_rel_count['smov']; ?>;
$("#facturas").val(smov_cont);

$(function(){
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	$(".inputnum").numeric(".");
	for(var i=0;i < <?php echo $form->max_rel_count['smov']; ?>;i++){
		autocod(i.toString());
	}
	totaliza();
	$('input[name^="cantidad_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_smov();
			return false;
		}
	});
});

//Agrega el autocomplete
function autocod(id){
	$('#numero_'+id).autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasmovrc'); ?>",
				type: 'POST',
				dataType: 'json',
				data: {'q':req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#numero_'+id).val('');
							$('#nombre_'+id).val('');
							$('#fechad_'+id).val('');
							$('#vence_'+id).val('');
							$('#monto_'+id).val('');
							$('#id_'+id).val('');
							$('#tipo_doc_'+id).val('');
							$('#nombre_'+id+'_val').text('');
							$('#fechad_'+id+'_val').text('');
							$('#vence_'+id+'_val').text('');
							$('#monto_'+id+'_val').text('');
							$('#tipo_doc_'+id+'_val').text('');
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
			$('#numero_'+id).attr('readonly','readonly');
			$('#numero_'+id).val(ui.item.numero);
			$('#nombre_'+id).val(ui.item.nombre);
			$('#fechad_'+id).val(ui.item.fecha);
			$('#vence_'+id).val(ui.item.vence);
			$('#monto_'+id).val(ui.item.monto);
			$('#id_'+id).val(ui.item.id);
			$('#tipo_doc_'+id).val(ui.item.tipo_doc);

			$('#nombre_'+id+'_val').text(ui.item.nombre);
			$('#fechad_'+id+'_val').text(ui.item.fecha);
			$('#vence_'+id+'_val').text(ui.item.vence);
			$('#monto_'+id+'_val').text(nformat(ui.item.monto,2));
			$('#tipo_doc_'+id+'_val').text(ui.item.tipo_doc);

			totaliza();
			setTimeout(function(){ $('#numero_'+id).removeAttr('readonly'); }, 1500);
		}
	});
}

function add_smov(){
	var htm = <?php echo $campos; ?>;
	can = smov_cont.toString();
	con = (smov_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PTPL__").after(htm);
	autocod(can);

	$("#numero_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_smov();
			return false;
		}
	});

	$("#numero_"+can).focus();
	smov_cont = smov_cont+1;
}

function totaliza(){
	var tmonto = 0;
	var sfacts = 0;
	var itmonto= 0;
	var arr=$('input[name^="monto_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind   = this.name.substring(pos+1);
			itmonto=Number(this.value);
			if(itmonto>0){
				sfacts = sfacts+1;
				tmonto = tmonto+itmonto;
			}
		}
	});

	$("#facturas").val(sfacts);
	$("#facturas_val").text(sfacts);
	$("#total").val(tmonto);
	$("#total_val").text(nformat(tmonto,2));
}

function del_smov(id){
	id = id.toString();
	$('#tr_smov_'+id).remove();
	smov_cont = smov_cont-1;
	$("#facturas").val(smov_cont);

}
</script>
<?php } ?>

	<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
	<table width='100%'>
		<tr>
			<td class="littletablerowth"><?php echo $form->vende->label;    ?></td>
			<td class="littletablerow"  ><?php echo $form->vende->output;   ?></td>
			<td class="littletablerowth"><?php echo $form->fecha->label;    ?></td>
			<td class="littletablerow"  ><?php echo $form->fecha->output;   ?></td>
		</tr><tr>
			<td class="littletablerowth"><?php echo $form->observa->label;  ?></td>
			<td class="littletablerow" colspan='3'><?php echo $form->observa->output; ?></td>
		</tr>
	</table>
	</fieldset>


	<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
	<div style='overflow:auto;border: 1px outset #9AC8DA;background: #FAFAFA;height:250px'>

	<table width='100%'>
		<tr id='__PTPL__'>
			<td bgcolor='#7098D0' align='center'>Tipo Doc.</td>
			<td bgcolor='#7098D0' align='center'>N&uacute;mero</td>
			<td bgcolor='#7098D0' align='center'>Fecha </td>
			<td bgcolor='#7098D0' align='center'>Vence </td>
			<td bgcolor='#7098D0' align='center'>Nombre</td>
			<td bgcolor='#7098D0' align='center'>Total </td>
			<?php if($form->_status!='show') {?>
				<td bgcolor='#7098D0' align='center'><a href='#' id='addlink' onclick="add_smov()" title='Agregar otra factura'><?php echo img('images/agrega4.png'); ?></a></td>
			<?php } ?>
		</tr>

		<?php for($i=0;$i<$form->max_rel_count['smov'];$i++) {
			$obj1  = "numero_${i}";
			$obj2  = "fechad_${i}";
			$obj3  = "vence_${i}";
			$obj4  = "nombre_${i}";
			$obj5  = "totalg_${i}";
			$obj6  = "itid_${i}";
			$obj7  = "tipo_doc_${i}";
		?>
		<tr id='tr_smov_<?php echo $i; ?>'>
			<td class="littletablerow"><?php echo $form->$obj7->output ?></td>
			<td class="littletablerow"><?php echo $form->$obj1->output ?></td>
			<td class="littletablerow"><?php echo $form->$obj2->output ?></td>
			<td class="littletablerow"><?php echo $form->$obj3->output ?></td>
			<td class="littletablerow"><?php echo $form->$obj4->output ?></td>
			<td class="littletablerow"><?php echo $form->$obj5->output.$form->$obj6->output; ?></td>
			<?php if($form->_status!='show') {?>
				<td class="littletablerow" align="center"><a href="#" onclick='del_smov(<?php echo $i; ?>);return false;'><?php echo img("images/delete.jpg"); ?></a></td>
			<?php } ?>
		</tr>
		<?php } ?>
	</table>
	</div>
	</fieldset>

	<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
	<table width='100%'>
		<tr>
			<td class="littletablerowth" width='200'>&nbsp;</td>
			<td class="littletablerowth"><?php echo $form->facturas->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->facturas->output; ?></td>
			<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
			<td class="littletablerow"  style='font-size:1.3em'><?php echo $form->monto->output; ?></td>
		</tr>
	</table>
<?php echo $form_end; ?>
