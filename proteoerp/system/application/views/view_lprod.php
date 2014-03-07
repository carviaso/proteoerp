<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
echo $form_scripts;
echo $form_begin;

$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$mod=true;

if($form->_status!='show'){

$campos=$form->template_details('itlprod');
$scampos  ='<tr id="tr_itlprod_<#i#>">';
$scampos .='<td class="littletablerow" align="center" >'.$campos['itcodrut']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center" >'.$campos['itnombre']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center" >'.$campos['itlitros']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center" >'.$campos['itbufala']['field'].'</td>';
$scampos .='<td class="littletablerow"><a href=# onclick="del_itlprod(<#i#>);return false;">'.img('images/delete.jpg').'</a></td>';
$scampos .='</td></tr>';
$campos=$form->js_escape($scampos);

?>

<script language="javascript" type="text/javascript">
var itlprod_cont =<?php echo $form->max_rel_count['itlprod']; ?>;

$(function(){
	$(".inputnum").numeric(".");
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	for(var i=0;i < <?php echo $form->max_rel_count['itlprod']; ?>;i++){
		autocod(i.toString());
	}

	$('#codigo').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinv'); ?>",
				type: "POST",
				dataType: "json",
				data: {'q':req.term,'fecha':$("#fecha").val()},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#codigo').val('')
							$('#descrip').val('');
							$('#descrip_val').text('');
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
			$('#codigo').attr("readonly", "readonly");

			$('#codigo').val(ui.item.codigo);
			$('#descrip').val(ui.item.descrip);
			$('#descrip_val').text(ui.item.descrip);
			$('#inventario').focus();
			$('#inventario').select();

			totalizar();
			setTimeout(function() {  $('#codigo').removeAttr("readonly"); }, 1500);
		}
	});

	totalizar();
});

function totalizar(){
	var litros = Number($('#inventario').val());

	//litros de vaca
	var arr=$('input[name^="itlitros_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			litros = litros+Number(Math.abs(this.value));
		}
	});

	//litros de bufala
	var arr=$('input[name^="itbufala_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			litros = litros+Number(Math.abs(this.value));
		}
	});

	$("#litros").val(roundNumber(litros,2));
	$("#litros_val").text(nformat(litros,2));
}

function add_itlprod(){
	var htm = <?php echo $campos; ?>;
	can = itlprod_cont.toString();
	con = (itlprod_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__lprod").before(htm);

	$("#itlitros_"+can).numeric(".");
	autocod(can);
	$('#codrut_'+can).focus();

	itlprod_cont=itlprod_cont+1;
}

function del_itlprod(id){
	id = id.toString();
	$('#tr_itlprod_'+id).remove();
	totalizar();
}

//Agrega el autocomplete
function autocod(id){
	$('#codrut_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscalruta'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term,'fecha':$('#fecha').val()},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#codrut_'+id).val('');

							$('#itnombre_'+id).val('');
							$('#itnombre_'+id+'_val').text('');

							//totalizar();
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
		minLength: 0,
		select: function( event, ui ) {
			$('#codrut_'+id).attr("readonly", "readonly");

			$('#codrut_'+id).val(ui.item.value);
			$('#itnombre_'+id).val(ui.item.nombre);
			$('#itnombre_'+id+'_val').text(ui.item.nombre);
			$('#itlitros_'+id).focus();

			totalizar();
			setTimeout(function() {  $('#codrut_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}
</script>
<?php } ?>

<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table width='100%' style='font-size:11pt;background:#F2E69D;'>
	<tr>
		<td><b><?php echo $form->litros->label;      ?></b></td>
		<td style='text-align:right;'> <?php echo $form->litros->output;     ?>&nbsp;</td>
		<td><b><?php echo $form->codigo->label;      ?></b></td>
		<td>   <?php echo $form->codigo->output;    ?></td>
		<td colspan='3'>   <?php echo $form->descrip->output;    ?></td>
	</tr><tr>
		<td><b><?php echo $form->fecha->label;       ?></b></td>
		<td>   <?php echo $form->fecha->output;      ?></td>
		<td><b><?php echo $form->reciclaje->label;    ?></b></td>
		<td>   <?php echo $form->reciclaje->output;  ?></td>
		<td><b><?php echo $form->grasa->label;       ?></b></td>
		<td>   <?php echo $form->grasa->output;      ?></td>
		<td><b><?php echo $form->inventario->label;  ?></b></td>
		<td>   <?php echo $form->inventario->output; ?></td>
	</tr>
</table>
<div style='border: 1px solid #9AC8DA;background: #FAFAFA'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr style='background:#030B7A;color:#FDFDFD;font-size:10pt;'>
		<th align="center">Ruta</th>
		<th align="center">Nombre</th>
		<th align="center">Litros Vac.</th>
		<th align="center">Litros Buf.</th>
		<?php if($form->_status!='show') {?>
			<th align="center">&nbsp;</th>
		<?php } ?>
	</tr>

<?php
	for($i=0;$i<$form->max_rel_count['itlprod'];$i++) {
		$it_id      = "itid_${i}";
		$it_codrut  = "itcodrut_${i}";
		$it_nombre  = "itnombre_${i}";
		$it_litros  = "itlitros_${i}";
		$it_bufala  = "itbufala_${i}";
?>
	<tr id='tr_itlprod_<?php echo $i; ?>'>
		<td class="littletablerow" align="center"><?php echo $form->$it_codrut->output; if(isset($form->$it_id)) echo $form->$it_id->output;  ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_nombre->output;  ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_litros->output;  ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_bufala->output;  ?></td>
		<?php if($form->_status!='show') { ?>
			<td class="littletablerow"><a href=# onclick="del_itlprod(<?php echo $i; ?>);return false;"><?php echo img('images/delete.jpg'); ?></a></td>
		<?php } ?>
	</tr>
	<?php
	$mod=!$mod;
	} ?>
	<tr id='__UTPL__lprod'>
		<td colspan='<?php echo ($form->_status!='show')? 5: 4 ?>' class="littletableheaderdet">&nbsp;</td>
	</tr>
</table>
</div>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end; ?>
