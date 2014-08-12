<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itssal');
$scampos  = '<tr id="tr_itssal_<#i#>">';
$scampos .= '<td class="littletablerow" align="left" >'.$campos['codigo']['field'].'</td>';
$scampos .= '<td class="littletablerow" align="left" >'.$campos['itdescrip']['field'].'</td>';
$scampos .= '<td class="littletablerow" align="right">'.$campos['cantidad']['field'].  '</td>';
$scampos .= '<td class="littletablerow" align="right">'.$campos['costo']['field']. '</td>';
$scampos .= '<td class="littletablerow" align="left" >'.$campos['concepto']['field']. '</td>';
$scampos .= '<td class="littletablerow"><a href=# onclick="del_itssal(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itssal_cont=<?php echo $form->max_rel_count['itssal']; ?>;
$(function(){
	$("#fecha").datepicker({   dateFormat: "dd/mm/yy" });
	$(".inputnum").numeric(".");
	for(var i=0;i < <?php echo $form->max_rel_count['itssal']; ?>;i++){
		autocod(i.toString());
		autoicon(i.toString());
	}
	chtipo();

});

function chtipo(){
	var tipo=$('#tipo').val();
	if(tipo=='E'){
		$('span[id^="mbI_"]').show();
		$('span[id^="mbE_"]').hide();
	}else{
		$('span[id^="mbE_"]').show();
		$('span[id^="mbI_"]').hide();
	}
}

//Agrega el autocomplete para el codigo
function autocod(id){
	$('#codigo_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinvart/N/S'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];

						if(data.length==0){
							$('#codigo_'+id).val("");
							$('#itdescrip_'+id).val("");
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
			$('#itdescrip_'+id).val(ui.item.descrip);
			$('#costo_'+id).val(ui.item.pond);
			if(cana<=0) $("#cantidad_"+id).val('1');
			$('#cantidad_'+id).focus();
			$('#cantidad_'+id).select();

			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}

//Agrega el autocomplete para el cocepto
function autoicon(id){
	$('#concepto_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscaicon'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term,"tipo":$('#tipo').val()},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#concepto_'+id).val("");
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
			$('#concepto_'+id).attr("readonly", "readonly");

			setTimeout(function() {  $('#concepto_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}

function add_itssal(){
	var htm = <?php echo $campos; ?>;
	can = itssal_cont.toString();
	con = (itssal_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#cantidad_"+can).numeric(".");
	autocod(can);
	chtipo();
	itssal_cont=itssal_cont+1;
}
function del_itssal(id){
	id = id.toString();
	$('#tr_itssal_'+id).remove();
}
</script>
<?php } ?>

<table align='center' width="100%">
	<tr>
		<td>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->tipo->label;   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->tipo->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->fecha->label;  ?>*&nbsp;</td>
				<td>
					<table width="100%" cellpadding="0" cellspacing="0">
						<tr>
							<td class="littletablerow">   <?php echo $form->fecha->output;  ?></td>
							<td class="littletableheader"><?php echo $form->almacen->label; ?>*&nbsp;</td>
							<td class="littletablerow">   <?php echo $form->almacen->output.$form->caububides->output; ?>&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->depto->label;    ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->depto->output;   ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->descrip->label;  ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->descrip->output; ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->cargo->label;   ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->cargo->output;  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->motivo->label;  ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->motivo->output; ?>&nbsp;</td>
			</tr>
		</table><br>
		</td>
	</tr>
	<tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:250px'>
		<table width='100%'>
			<tr>
				<td width='135' bgcolor='#7098D0' align='center'>C&oacute;digo</td>
				<td bgcolor='#7098D0'>Descripci&oacute;n</td>
				<td bgcolor='#7098D0'>Cantidad</td>
				<td bgcolor='#7098D0'>Costo</td>
				<td bgcolor='#7098D0'>Conceptos</td>
				<?php if($form->_status!='show'){?>
					<td bgcolor='#7098D0'>&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itssal'];$i++) {
				$it_codigo   = "codigo_${i}";
				$it_descrip  = "itdescrip_${i}";
				$it_cant     = "cantidad_${i}";
				$it_costo    = "costo_${i}";
				$it_concepto = "concepto_${i}";
			?>

			<tr id='tr_itssal_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output;  ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_descrip->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_cant->output;    ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_costo->output;   ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_concepto->output;?></td>
				<?php if($form->_status!='show') {?>
				<td class="littletablerow">
					<a href='#' onclick='del_itssal(<?php echo $i; ?>);return false;'><?php echo img("images/delete.jpg"); ?></a>
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
		<?php echo $form_end; ?>
	</tr>
</table>
<?php endif; ?>
