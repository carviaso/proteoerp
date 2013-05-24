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
$scampos .='<td class="littletablerow"><a href=\'#\' onclick="del_itsnot(<#i#>);return false;">'.img('images/delete.jpg').'</a></td>';
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
});

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinv'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#codigoa_'+id).val('')
							$('#desca_'+id).val('');
							$('#precio1_'+id).val('');
							$('#precio2_'+id).val('');
							$('#precio3_'+id).val('');
							$('#precio4_'+id).val('');
							$('#itiva_'+id).val('');
							$('#sinvtipo_'+id).val('');
							$('#sinvpeso_'+id).val('');
							$('#pond_'+id).val('');
							$('#ultimo_'+id).val('');
							$('#cana_'+id).val('');
							post_modbus_sinv(id);
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
			$('#codigoa_'+id).attr("readonly", "readonly");

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

			post_modbus_sinv(Number(id));

			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}

function add_itsnot(){
	var htm = <?php echo $campos; ?>;
	can = itsnot_cont.toString();
	con = (itsnot_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PTPL__").after(htm);
	$("#cant_"+can).numeric(".");
	itsnot_cont=itsnot_cont+1;
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
				<td class="littletableheader"><?php echo $form->factura->label;  ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->factura->output; ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->cliente->label;  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->cliente->output; ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->nombre->output;  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fecha->label;    ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output;   ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->fechafa->label   ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fechafa->output  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->peso->label  ?>&nbsp;</td>
				<td class="littletablerow" align="left"><?php echo $form->peso->output ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->observa1->label  ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->observa1->output ?>&nbsp;</td>
			</tr>
		</table><br>
		</td>
	</tr>
	<tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:190px'>
		<table width='100%'>
			<tr  id='__PTPL__'>
				<td class="littletableheaderdet">C&oacute;digo</td>
				<td class="littletableheaderdet">Descripci&oacute;n</td>
				<td class="littletableheaderdet">Cantidad</td>
				<td class="littletableheaderdet">Saldo</td>
				<td class="littletableheaderdet">Entrega</td>
				<?php if($form->_status!='show') {?>
					<td class="littletableheaderdet">&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itsnot'];$i++){
				$it_codigo  = "codigo_${i}";
				$it_descrip = "descrip_${i}";
				$it_cant    = "cant_${i}";
				$it_saldo   = "saldo_${i}";
				$it_entrega = "entrega_${i}";
				$it_fact    = "itfactura_${i}";
			?>

			<tr id='tr_itsnot_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output;  ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_descrip->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_cant->output;    ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_saldo->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_entrega->output; ?></td>
				<?php if($form->_status!='show') {?>
				<td class="littletablerow"><a href='#' onclick="del_itsnot(<?php echo $i; ?>);return false;"><?php echo img('images/delete.jpg'); ?></a></td>
				<?php } ?>
			</tr>
			<?php } ?>
		</table>
		</div>
		<?php echo $container_bl.$container_br.$form_end; ?>
		</td>
	</tr>
</table>
<?php endif; ?>
