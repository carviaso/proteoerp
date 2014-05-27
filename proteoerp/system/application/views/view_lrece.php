<?php
echo $form_scripts;
echo $form_begin;

$container_tr=join('&nbsp;', $form->_button_container['TR']);
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$mod=true;

$campos=$form->template_details('itlrece');
//print_r($campos);

$scampos  ='<tr id="tr_itlrece_<#i#>" style="background:#E4E4E4;">';
$scampos .='<td align="left"><b>'.$campos['itlvacacodigo']['field'].' '.$campos['itlvacadescrip']['field'].'</b></td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itdensidad']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['ittemp']['field']    .'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itanimal']['field']  .'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itacidez']['field']  .'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['ith2o']['field']     .'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itcrios']['field']   .'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itbrix']['field']    .'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itgrasa']['field']   .'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itcloruros']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['italcohol']['field'] .'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itph']['field'] .'</td>';
$scampos .='<td class="littletablerow"><a href=# onclick="del_itlrece(<#i#>);return false;">'.img("images/delete.jpg").'</a></td>';
$scampos .='</tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
var itlrece_cont=<?php echo $form->max_rel_count['itlrece']; ?>;

function add_itlrece(){
	var htm = <?php echo $campos; ?>;
	can = itlrece_cont.toString();
	con = (itlrece_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__INPL__").after(htm);

	autocod(can);
	$('#vaquera_'+can).focus();
	itlrece_cont=itlrece_cont+1;
	return can;
}

function del_itlrece(id){
	id = id.toString();
	$('#tr_itlrece_'+id).remove();
	var arr = $('input[id^="vaquera_"]');
	if(arr.length<=0){
		add_itlrece();
	}
}

</script>
<?php } ?>
<table width='100%' style='font-size:11pt;background:#F2E69D;'>
	<tr>
		<td                           width='60'>N&uacute;mero:</td>
		<td style='font-weight:bold;' width='70'><?php echo str_pad(trim($form->id->output),7,'0',STR_PAD_LEFT);    ?></td>
		<td                           width='60' align='right'>Fecha:</td>
		<td style='font-weight:bold;' width='90'><?php echo $form->fecha->output; ?></td>
		<td                           width='50' align='right'>Ruta:</td>
		<td style='font-weight:bold;' width='50' align='left'><?php echo $form->ruta->output;  ?></td>
		<td style='font-weight:bold;'><?php echo $this->datasis->dameval("SELECT nombre FROM lruta WHERE codigo='".$form->ruta->value."'");  ?></td>
		<td                            align='right'>Alcohol:</td>
		<td style='font-weight:bold;'  align='left'><?php echo $form->alcohol->output;  ?></td>

	</tr>
</table>
<div style='border: 1px solid #9AC8DA;background: #FAFAFA'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr style='background:#030B7A;color:#FDFDFD;font-size:10pt;' id='__INPL__'>
		<th align="center">Vaquera</th>
		<th align="center">Dens</th>
		<th align="center">Temp.</th>
		<th align="center">Tipo</th>
		<th align="center">Acidez</th>
		<th align="center">Agua</th>
		<th align="center">Crio.</th>
		<th align="center">G.Brix</th>
		<th align="center">Grasa</th>
		<th align="center">Cloro</th>
		<th align="center">Alcoh.</th>
		<th align="center">pH</th>
		<?php if($form->_status!='show'){ ?>
		<th></th>
		<?php } ?>
	</tr>

<?php
	for($i=0;$i<$form->max_rel_count['itlrece'];$i++) {

		$it_densidad     = "itdensidad_${i}";
		$it_lista        = "itlista_${i}";
		$it_animal       = "itanimal_${i}";
		$it_crios        = "itcrios_${i}";
		$it_h2o          = "ith2o_${i}";
		$it_temp         = "ittemp_${i}";
		$it_brix         = "itbrix_${i}";
		$it_grasa        = "itgrasa_${i}";
		$it_acidez       = "itacidez_${i}";
		$it_cloruros     = "itcloruros_${i}";
		$it_dtoagua      = "itdtoagua_${i}";
		$it_id_lvaca     = "itid_lvaca_${i}";
		$it_id_lrece     = "itid_lrece_${i}";
		$it_id           = "itid_${i}";
		$it_lvacacodigo  = "itlvacacodigo_${i}";
		$it_lvacadescrip = "itlvacadescrip_${i}";
		$it_vaquera      = "itvaquera_${i}";
		$it_nombre       = "itnombre_${i}";
		$it_alcohol      = "italcohol_${i}";
		$it_ph           = "itph_${i}";

		echo $form->$it_lista->output.$form->$it_id->output.$form->$it_id_lvaca->output.$form->$it_nombre->output.$form->$it_vaquera->output;
?>

	<tr style='background:#E4E4E4;'  id='tr_itlrece_<?php echo $i ?>'>
		<td align='left'><b><?php echo $form->$it_lvacacodigo->output.' '.$form->$it_lvacadescrip->output; ?></b></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_densidad->output;?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_temp->output;    ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_animal->output;  ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_acidez->output;  ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_h2o->output;     ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_crios->output;   ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_brix->output;    ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_grasa->output;   ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_cloruros->output;?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_alcohol->output; ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_ph->output; ?></td>
		<?php if($form->_status!='show'){ ?>
		<td class="littletablerow"><a href=# onclick="del_itlrece(<?php echo $i; ?>);return false;"><?php echo img("images/delete.jpg"); ?></a></td>
		<?php } ?>
	</tr>
	<?php
	$mod=!$mod;
	} ?>
</table>
</div>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end; ?>
