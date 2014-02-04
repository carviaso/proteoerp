<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

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
		$html.='<td class="littletablerow"><a href=# onclick=\'del_itordi(<#i#>);return false;\'>'.img('images/delete.jpg').'</a></td>';
	}
	$html.='</tr>';


/*foreach($form->detail_fields['itstra'] AS $ind=>$data)
	$campos[]=$data['field'];
$campos='<tr id="tr_itstra_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itstra(<#i#>);return false;">Eliminar</a></td></tr>';*/
$campos=$form->js_escape($html);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
itstra_cont=<?php echo $form->max_rel_count['itstra'] ?>;

$(function(){
	$(".inputnum").numeric(".");
	for(var i=0;i < <?php echo $form->max_rel_count['itstra']; ?>;i++){
		autocod(i.toString());
	}
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
				url:  "<?php echo site_url('ajax/buscasinvart'); ?>",
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
			$('#codigo_'+id).val(ui.item.codigo);
			$('#descrip_'+id).val(ui.item.descrip);
			post_modbus(id);
		}
	});
}

function truncate(){
	$('tr[id^="tr_itstra_"]').remove();
	itstra_cont=0;
}

function add_itstra(){
	var htm = <?php echo $campos; ?>;
	can = itstra_cont.toString();
	con = (itstra_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#cantidad_"+can).numeric(".");
	itstra_cont=itstra_cont+1;
}

function del_itstra(id){
	id = id.toString();
	$('#tr_itstra_'+id).remove();
}

function buscaprod(){
	ttipo=$('#tipoordp').val();
	truncate();
	if(ttipo=='E' || ttipo=='R'){
		parr={esta: $('#esta').val(),ordp: $('#ordp').val(),tipo: ttipo};
		$.ajax({
			url:  "<?php echo site_url('ajax/ordpart'); ?>",
			type: "POST",
			dataType: "json",
			data: $.param(parr),
			success:
				function(data){
					var sugiere = [];
					if(data.length==0){
						alert('No hay articulos');
					}else{
						$.each(data,
							function(i, item){
								add_itstra();
								id = i.toString();
								$('#codigo_'+id).val(item.codigo);
								$('#descrip_'+id).val(item.descrip);
								$('#cantidad_'+id).val(item.cantidad);
								post_modbus(id);
							}
						);
					}
				},
		});
	}else{
		alert('Debe seleccionar un tipo primero');
	}
}
</script>
<?php } ?>

<table align='center' width='100%'>
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<th colspan='4' class="littletableheader">Transferencia <b><?php if($form->_status=='show' or $form->_status=='modify' ) echo str_pad($form->numero->output,8,0,0); ?></b></th>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->tipoordp->label   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->tipoordp->output  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->fecha->label   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->esta->label  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->esta->output ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->observ1->label  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->observ1->output ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->ordp->label  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->ordp->output ?>&nbsp;</td>
				<td class="littletableheader">&nbsp;</td>
				<td class="littletablerow">   &nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
		<td>
		<table width='100%'>
			<tr>
				<th colspan='4' class="littletableheader">Lista de Art&iacute;culos</th>
			</tr>
			<tr>
				<td class="littletableheader">C&oacute;digo</td>
				<td class="littletableheader">Descripci&oacute;n</td>
				<td class="littletableheader">Cantidad</td>
				<?php if($form->_status!='show') {?>
					<td class="littletableheader">&nbsp;</td>
				<?php } ?>
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
					<td class="littletablerow"><a href=#onclick='del_itstra(<?php echo $i; ?>);return false;'><?php echo img("images/delete.jpg"); ?></a></td>
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
		<?php echo $form_end ?> <?php echo $container_bl ?> <?php echo $container_br ?>
		</td>
	</tr>
</table>
<?php endif; ?>
