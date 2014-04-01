<?php
//$container_bl=join('&nbsp;', $form->_button_container['BL']);
//$container_br=join('&nbsp;', $form->_button_container['BR']);
//$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

	$html='<tr id="tr_itstra_<#i#>">';
	$campos=$form->template_details('itstra');
	foreach($campos as $nom=>$nan){
		$pivot=$nan['field'];
		$align = (strpos($pivot,'inputnum')) ? 'align="right"' : '';
		$html.='<td class="littletablerow" '.$align.'>'.$pivot.'</td>';
	}
	if($form->_status!='show') {
		$html.='<td class="littletablerow"><a href=# onclick=\'del_itstra(<#i#>);return false;\'>'.img('images/delete.jpg').'</a></td>';
	}
	$html.='</tr>';


$campos=$form->js_escape($html);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
itstra_cont=<?php echo $form->max_rel_count['itstra'] ?>;

$(function(){

	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	$(".inputnum").numeric(".");
	for(var i=0;i < <?php echo $form->max_rel_count['itstra']; ?>;i++){
		autocod(i.toString());
	}

	$('input[name^="cantidad_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itstra();
			return false;
		}
	});
});

function post_modbus(id){
	//var id      = i.toString();
	var descrip = $('#descrip_'+id).val();
	$('#descrip_'+id+'_val').text(descrip);
	$('#cantidad_'+id).focus();
}

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinvart/N'); ?>",
				type: 'POST',
				dataType: 'json',
				data: {"q":req.term,"alma":$('#envia').val()},
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
			$('#codigo_'+id).attr('readonly','readonly');

			$('#codigo_'+id).val(ui.item.codigo);
			$('#descrip_'+id).val(ui.item.descrip);
			post_modbus(id);

			setTimeout(function(){ $('#codigo_'+id).removeAttr('readonly'); }, 1500);
		}
	});
}

function add_itstra(){
	var htm = <?php echo $campos; ?>;
	can = itstra_cont.toString();
	con = (itstra_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#cantidad_"+can).numeric(".");
	$("#codigo_"+can).focus();
	autocod(can);
	$("#cantidad_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itstra();
			return false;
		}
	});

	itstra_cont=itstra_cont+1;
}

function del_itstra(id){
	id = id.toString();
	$('#tr_itstra_'+id).remove();
}
</script>
<?php } ?>

<table align='center' width='100%'>
	<tr>
		<td>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->envia->label   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->envia->output  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->fecha->label   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->recibe->label  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->recibe->output ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->observ1->label  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->observ1->output ?>&nbsp;</td>

			</tr>
		</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:250px'>

		<table width='100%'>
			<tr>
				<td width="160" bgcolor='#7098D0'>C&oacute;digo</td>
				<td bgcolor='#7098D0'>Descripci&oacute;n</td>
				<td width="110" align="center" bgcolor='#7098D0'>Cantidad</td>
				<td width="20" bgcolor='#7098D0'>&nbsp;</td>
			</tr>
			<?php for($i=0;$i<$form->max_rel_count['itstra'];$i++) {
				$obj1="codigo_$i";
				$obj2="descrip_$i";
				$obj3="cantidad_$i";
			?>
			<tr id='tr_itstra_<?php echo $i; ?>'>
				<td class="littletablerow"><?php echo $form->$obj1->output ?></td>
				<td class="littletablerow"><?php echo $form->$obj2->output ?></td>
				<td class="littletablerow"align="right"><?php echo $form->$obj3->output ?></td>
				<?php if($form->_status!='show') {?>
					<td class="littletablerow"><a href="#" onclick='del_itstra(<?php echo $i; ?>);return false;'><?php echo img("images/delete.jpg"); ?></a></td>
				<?php } ?>
			</tr>
			<?php } ?>

			<tr id='__UTPL__'>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<?php if($form->_status!='show') {?>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<?php } ?>
			</tr>
		</table>
		</div>
		<?php echo $form_end ?> <?php echo $container_bl ?> <?php echo $container_br ?>
		</td>
	</tr>
</table>
<?php endif; ?>
