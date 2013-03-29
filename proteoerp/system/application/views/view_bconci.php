<?php
echo $form_begin;

$container_tr=join('&nbsp;', $form->_button_container['TR']);
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$mod=true;

$campos=$form->template_details('bmov');
$scampos  ='<tr id="tr_bmov_<#i#>">';
$scampos .='<td class="littletablerow" align="center">'.$campos['itfecha']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left"  >'.$campos['ittipo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itnumero']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itmonto']['field'].'</td>';
$scampos .='</tr>';
$campos=$form->js_escape($scampos);

if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
var bmov_cont =<?php echo $form->max_rel_count['bmov']; ?>;

$(function(){
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	$(".inputnum").numeric(".");

});

function truncate(){
	var arr=$('input[name^="itcodigo_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			val=this.value;
			if(val==''){
				ind = this.name.substring(pos+1);
				del_itlcierre(parseInt(ind));
			}
		}
	});

	//$('tr[id^="tr_itlcierre_"]').remove();
	//itlcierre_cont=0;
}

function add_itlcierre(){
	var htm = <?php echo $campos; ?>;
	can = itlcierre_cont.toString();
	con = (itlcierre_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__bmov").before(htm);

	$("#itmonto_"+can).numeric(".");
	$('#itmonto_'+can).focus();

	bmov_cont=bmov_cont+1;
	return can;
}

function del_bmov(id){
	id = id.toString();
	$('#tr_itlcierre_'+id).remove();
}

function totalizar(){

	var arr=$('input[name^="itmonto_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			litros = litros+Number(Math.abs(this.value));
		}
	});

	//$("#litros").val(roundNumber(litros,2));
	//$("#litros_val").text(nformat(litros,2));
}

</script>
<?php } ?>

<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table width='100%' style='font-size:11pt;background:#F2E69D;'>
	<tr>
		<td><b><?php echo $form->codbanc->label;     ?></b></td>
		<td colspan='3'><?php echo $form->codbanc->output;    ?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->fecha->label;     ?></b></td>
		<td>   <?php echo $form->fecha->output;    ?></td>
		<td><b><?php echo $form->deposito->label;  ?></b></td>
		<td>   <?php echo $form->deposito->output; ?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->saldoi->label;  ?></b></td>
		<td>   <?php echo $form->saldoi->output; ?></td>
		<td><b><?php echo $form->credito->label; ?></b></td>
		<td>   <?php echo $form->credito->output;?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->saldof->label;  ?></b></td>
		<td>   <?php echo $form->saldof->output; ?></td>
		<td><b><?php echo $form->cheque->label;  ?></b></td>
		<td>   <?php echo $form->cheque->output; ?></td>
	</tr>
	<tr>
		<td><b></b></td>
		<td>   </td>
		<td><b><?php echo $form->debito->label;  ?></b></td>
		<td>   <?php echo $form->debito->output; ?></td>
	</tr>
</table>
<div style='border: 1px solid #9AC8DA;background: #FAFAFA'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr style='background:#030B7A;color:#FDFDFD;font-size:10pt;'>
		<th align="center">Fecha</th>
		<th align="center">Tipo</th>
		<th align="center">N&uacute;mero</th>
		<th align="center">Monto</th>
		<th align="center">&nbsp;</th>
	</tr>

<?php
	for($i=0;$i<$form->max_rel_count['bmov'];$i++) {
		$it_fecha    ='itfecha_'.$i;
		$it_tipo     ='ittipo_'.$i;
		$it_numero   ='itnumero_'.$i;
		$it_monto    ='itmonto_'.$i;
?>
	<tr id='tr_itlcierre_<?php echo $i; ?>'>
		<td class="littletablerow" align="center"><?php echo $form->$it_fecha->output;    ?></td>
		<td class="littletablerow" align="left"  ><?php echo $form->$it_tipo->output;   ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_numero->output;  ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_monto->output;    ?></td>
	</tr>
	<?php
	$mod=!$mod;
	} ?>
	<tr id='__UTPL__bmov'>
		<td colspan='<?php echo ($form->_status!='show')? 6: 5 ?>' class="littletableheaderdet">&nbsp;</td>
	</tr>
</table>
</div>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end; ?>
