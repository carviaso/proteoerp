<?php

$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itconv');
$scampos  ='<tr id="tr_itconv_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['descrip']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['entrada']['field'].  '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['salida']['field'];
$scampos .= $campos['costo']['field'].'</td>';
$scampos .= '<td class="littletablerow"><a href=# onclick="del_itconv(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itconv_cont=<?php echo $form->max_rel_count['itconv']; ?>;

$(function(){
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	$(".inputnum").numeric(".");
	for(var i=0;i < <?php echo $form->max_rel_count['itconv']; ?>;i++){
		autocod(i.toString());
	};
});

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinv'); ?>",
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
			$('#codigo').attr("readonly", "readonly");

			$('#codigo_'+id).val(ui.item.codigo);
			$('#descrip_'+id).val(ui.item.descrip);
			$('#descrip_'+id+'_val').text(ui.item.descrip);
			$("#costo_"+id).val(ui.item.ultimo);
			$('#entrada_'+id).focus();

			setTimeout(function() {  $("#codigo").removeAttr("readonly"); }, 1500);
		}
	});
}

function validaEnt(i){
	var entrada = Number($("#entrada_"+i).val());
	if(entrada>0)
		$("#salida_"+i).val('0');
	$("#entrada_"+i).val(entrada);
}

function validaSalida(i){
	var salida =Number($("#salida_"+i).val());
	if(salida>0)
		$("#entrada_"+i).val('0');
	$("#salida_"+i).val(salida)
}

function add_itconv(){
	var htm = <?php echo $campos; ?>;
	can = itconv_cont.toString();
	con = (itconv_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#entrada_"+can).numeric(".");
	$("#salida_"+can).numeric(".");
	//$("#costo_"+can).numeric(".");
	autocod(can);
	$('#codigo_'+can).focus();
	itconv_cont=itconv_cont+1;
}

function post_modbus_sinv(nind){
	id=nind.toString();
	descrip=$('#descrip_'+id).val();
	$('#descrip_'+id+'_val').text(descrip);
	$('#entrada_'+id).focus();
}

function del_itconv(id){
	id = id.toString();
	$('#tr_itconv_'+id).remove();
}
</script>
<?php } ?>

<table align='center' width="95%">
	<tr>
		<td>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->almacen->label   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->almacen->output  ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->fecha->label;    ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output;   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->observa1->label;  ?>&nbsp;</td>
				<td colspan='3' class="littletablerow">   <?php echo $form->observa1->output; ?>&nbsp;</td>
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
				<td             bgcolor='#7098D0' align='center'>Descripci&oacute;n</td>
				<td width='80'  bgcolor='#7098D0' align='center'>Entrada</td>
				<td width='80'  bgcolor='#7098D0' align='center'>Salida</td>
				<?php if($form->_status!='show') {?>
					<td width='20' bgcolor='#7098D0' align='center'>&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itconv'];$i++) {
				$it_codigo   = "codigo_$i";
				$it_descrip  = "descrip_$i";
				$it_salida   = "salida_$i";
				$it_entrada  = "entrada_$i";
				$it_costo    = "costo_$i";

				$pprecios = $form->$it_costo->output;
			?>

			<tr id='tr_itconv_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_descrip->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_entrada->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_salida->output.$pprecios;  ?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow">
					<a href='#' onclick='del_itconv(<?php echo $i ?>);return false;'><?php echo img("images/delete.jpg"); ?></a>
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
	</tr>
	<?php echo $form_end; ?>
</table>
<?php endif; ?>
