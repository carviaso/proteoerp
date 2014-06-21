<?php
//******************************************************************
// View

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'){
	echo $form->output;
} else {
	$html='<tr id="tr_smov_<#i#>">';
	$campos = $form->template_details('smov');
	foreach($campos as $nom=>$nan){
		$pivot=$nan['field'];
		$align = (strpos($pivot,'inputnum')) ? 'align="right"' : '';
		$html.='<td class="littletablerow" '.$align.'>'.$pivot.'</td>';
	}
}
if($form->_status!='show') {
	$html.='<td class="littletablerow"><a href=# onclick=\'del_smov(<#i#>);return false;\'>'.img('images/delete.jpg').'</a></td>';
}
$html.='</tr>';
$campos = $form->js_escape($html);
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
			$('#numero_'+id).attr('readonly','readonly');
			$('#numero_'+id).val(ui.item.numero);
			$('#nombre_'+id).val(ui.item.nombre);
			$('#fechad_'+id).val(ui.item.fecha);
			$('#vence_'+id).val(ui.item.vence);
			$('#monto_'+id).val(ui.item.monto);
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
	$("#__UTPL__").before(htm);

	//$("#facturas").val(can);
	//$("#numero_"+can).focus();

	autocod(can);

	$("#numero_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_smov();
			return false;
		}
	});

	smov_cont = smov_cont+1;
	$("#facturas").val(smov_cont);

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
		</tr>
		<tr>
			<td class="littletablerowth"><?php echo $form->observa->label;  ?></td>
			<td colspan='3' class="littletablerow" ><?php echo $form->observa->output; ?></td>
		</tr>
	</table>
	</fieldset>

	<table width='100%' bgcolor='#7098D0'>
		<tr>
			<td bgcolor='#7098D0' width="75"  align='center'>Numero</td>
			<td bgcolor='#7098D0' width="80"  align='center'>Fecha </td>
			<td bgcolor='#7098D0' width="80"  align='center'>Vence </td>
			<td bgcolor='#7098D0' width="270" align='center'>Nombre</td>
			<td bgcolor='#7098D0' width="110" align='center'>Total </td>
			<td bgcolor='#7098D0' align='center'><a href='#' id='addlink' onclick="add_smov()" title='Agregar otra factura'><?php echo img('images/agrega4.png'); ?></a></td>
		</tr>
	</table>
	<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:250px'>
	<table width='100%'>
		<?php for($i=0;$i<$form->max_rel_count['smov'];$i++) {
			$obj1  = "numero_$i";
			$obj2  = "fechad_$i";
			$obj3  = "vence_$i";
			$obj4  = "nombre_$i";
			$obj5  = "totalg_$i";
		?>
		<tr id='tr_smov_<?php echo $i; ?>'>
			<td class="littletablerow"><?php echo $form->$obj1->output ?></td>
			<td class="littletablerow"><?php echo $form->$obj2->output ?></td>
			<td class="littletablerow"><?php echo $form->$obj3->output ?></td>
			<td class="littletablerow"><?php echo $form->$obj4->output ?></td>
			<td class="littletablerow"><?php echo $form->$obj5->output ?></td>
			<?php if($form->_status!='show') {?>
				<td class="littletablerow"><a href="#" onclick='del_smov(<?php echo $i; ?>);return false;'><?php echo img("images/delete.jpg"); ?></a></td>
			<?php } ?>
		</tr>
		<?php } ?>
		<tr id='__UTPL__'>
			<td class="littletablefooterb" align="right">&nbsp;</td>
			<td class="littletablefooterb" align="right">&nbsp;</td>
			<td class="littletablefooterb" align="right">&nbsp;</td>
			<td class="littletablefooterb" align="right">&nbsp;</td>
			<td class="littletablefooterb" align="right">&nbsp;</td>
			<td class="littletablefooterb" align="right">&nbsp;</td>
			<?php if($form->_status!='show') {?>
			<td class="littletablefooterb" align="right">&nbsp;</td>
			<?php } ?>
		</tr>
	</table>
	</div>

	<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
	<table width='100%'>
		<tr>
			<td align='left'><a href='#' id='addlink' onclick="add_smov()" title='Agregar otra factura'><?php echo img('images/agrega4.png'); ?></a></td>
			<td class="littletablerowth" width='200'>&nbsp;</td>
			<td class="littletablerowth"><?php echo $form->facturas->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->facturas->output; ?></td>
			<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
		</tr>
	</table>
	</fieldset>

<?php echo $form_end; ?>
