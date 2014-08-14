<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itsnot');
$scampos  ='<tr id="tr_itsnot_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigo']['field'] .'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['descrip']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['cant']['field']   .'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['saldo']['field']  .'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['entrega']['field'].'</td>';
//$scampos .='<td class="littletablerow"><a href=\'#\' onclick="del_itsnot(<#i#>);return false;">'.img('images/delete.jpg').'</a></td>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itsnot_cont=<?php echo $form->max_rel_count['itsnot']; ?>;

$(function(){
	$(".inputnum").numeric(".");
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });

	$('#cod_cli').autocomplete({
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
			$('#cod_cli').attr("readonly", "readonly");

			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);

			setTimeout(function() {  $("#cod_cli").removeAttr("readonly"); }, 1500);
		}
	});



	$('#factura').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasfacdev'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#factura').val('');

							$('#nombre').val('');
							$('#nombre_val').text('');

							$('#rifci').val('');
							$('#rifci_val').text('');

							$('#cod_cli').val('');
							$('#cod_cli_val').text('');

							$('#fechafa').val('');
							$('#fechafa_val').text('');

							truncate();
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
			$('#factura').attr("readonly", "readonly");
			$('#factura').val(ui.item.value);

			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);

			$('#fechafa').val(ui.item.fecha);
			$('#fechafa_val').text(ui.item.fecha);

			$('#cod_cli').val(ui.item.cod_cli);
			$('#cod_cli_val').text(ui.item.cod_cli);

			truncate();
			$.ajax({
				url: "<?php echo site_url('ajax/buscasinvsnot'); ?>",
				dataType: 'json',
				type: 'POST',
				data: {"q":ui.item.value},
				success: function(data){
					if(data.length==0){
						$.prompt("<span style='font-size:1.5em'>No hay productos para despachar.</span>", {
							title: "Factura ya despachada",
							buttons: { "Continuar": true }
						});
					}else{
						$.each(data,
							function(id, val){
								add_itsnot();
								$('#codigo_'+id).val(val.codigo);
								$('#codigo_'+id+'_val').text(val.codigo);
								$('#descrip_'+id).val(val.descrip);
								$('#descrip_'+id+'_val').text(val.descrip);
								$('#cant_'+id).val(val.cant);
								$('#cant_'+id+'_val').text(nformat(val.cant,2));
								$('#saldo_'+id).val(val.saldo);
								$('#saldo_'+id+'_val').text(nformat(val.saldo,2));
							}
						);
					}
				},
			});
			setTimeout(function() {  $("#factura").removeAttr("readonly"); }, 1500);
		}
	});
});

function truncate(){
	$('tr[id^="tr_itsnot_"]').remove();
	itsnot_cont = 0;
}


function add_itsnot(){
	var htm = <?php echo $campos; ?>;
	can = itsnot_cont.toString();
	con = (itsnot_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#cant_"+can).numeric(".");
	$("#entrega_"+can).numeric(".");
	itsnot_cont=itsnot_cont+1;
	return can;
}
function del_itsnot(id){
	id = id.toString();
	$('#tr_itsnot_'+id).remove();
}
</script>
<?php } ?>

<table align='center' width="100%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<th colspan='5' class="littletableheaderdet">Nota de Despacho <b><?php if($form->_status=='show' or $form->_status=='modify' ) echo str_pad($form->numero->output,8,0,0); ?></b></th>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->factura->label;  ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->factura->output; ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->cliente->label;  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->cliente->output; ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->nombre->output;  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fecha->label;    ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output;   ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->fechafa->label   ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->fechafa->output  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->tipo->label  ?>&nbsp;</td>
				<td class="littletablerow" align="left"><?php echo $form->tipo->output ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->observa1->label  ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->observa1->output ?>&nbsp;</td>
			</tr>
		</table><br>
		</td>
	</tr>
	<tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:250px'>
		<table width='100%'>
			<tr  id='__PTPL__'>
				<td class="littletableheaderdet">C&oacute;digo</td>
				<td class="littletableheaderdet">Descripci&oacute;n</td>
				<td class="littletableheaderdet">Cantidad</td>
				<td class="littletableheaderdet">Saldo</td>
				<td class="littletableheaderdet">Entrega</td>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itsnot'];$i++){
				$it_codigo  = "codigo_${i}";
				$it_descrip = "descrip_${i}";
				$it_cant    = "cant_${i}";
				$it_saldo   = "saldo_${i}";
				$it_entrega = "entrega_${i}";
			?>

			<tr id='tr_itsnot_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output;  ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_descrip->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_cant->output;    ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_saldo->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_entrega->output; ?></td>
			</tr>
			<?php } ?>
			<tr id='__UTPL__'>
				<td colspan='5'></td>
			</tr>
		</table>
		</div>
		<?php echo $container_bl.$container_br.$form_end; ?>
		</td>
	</tr>
</table>
<?php endif; ?>
