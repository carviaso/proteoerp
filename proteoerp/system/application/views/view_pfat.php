<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:


foreach($form->detail_fields['itpfac'] AS $ind=>$data)
$campos[]=$data['field'];

$campos='<tr id="tr_itpfac_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>
<td><input type="hidden" name="tdec_<#i#>" value="" id="tdec_<#i#>" "/></td>';

$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itpfac(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$form->js_escape($campos);
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){
	?>
<input type="hidden" name="__p1" id="__p1" value="">
<input type="hidden" name="__p2" id="__p2" value="">
<input type="hidden" name="__p3" id="__p3" value="">
<input type="hidden" name="__p4" id="__p4" value="">
<input type="hidden" name="t_cli"
	id="t_cli" value="">


<script language="javascript" type="text/javascript">
itpfac_cont=<?php echo $form->max_rel_count['itpfac']?>;

$(document).ready(function() {
	//alert(itpfac_cont);
	for(i=0;i<itpfac_cont;i++){
		c=$("#cana_"+i.toString()).val();
		p=$("#mostrado_"+i.toString()).val();
		t=roundNumber(c*p,2);
		$("#importe_"+i.toString()).val(t);
	}
});

function ejecuta(i){
	tipo=$("#t_cli").val();
	tdec=$("#tdec_"+i.toString()).val();
	if(tipo=='' || tipo=='0')tipo='1';
	if(tdec=="") $("#tdec_"+i.toString()).val('N');
	precio=$("#__p"+tipo.toString()).val();
	$("#preca_"+i.toString()).val(precio);
	p1=$("#__p1").val();
	
	p=roundNumber($("#preca_"+i.toString()).val(),2);
	iva=$("#iva_"+i.toString()).val();
	piva=roundNumber(p*(1+iva/100),2);
	
	$("#pvp_"+i.toString()).val(p1);
	$("#mostrado_"+i.toString()).val(piva);
}
function valida(i){
	alert("Este monto no puede ser modificado manualmente");
	totalizar(i);
}

function totalizar(i){
	dec=$("#tdec_"+i.toString()).val();
	nd=2;
	c=0;
//	alert(dec);
	if(dec == 'N'){
		nd=0;
	}
	c=roundNumber($("#cana_"+i.toString()).val(),nd);
	p=roundNumber($("#preca_"+i.toString()).val(),2);
	iva=$("#iva_"+i.toString()).val();
	piva=roundNumber(p*(1+iva/100),2);
	$("#mostrado_"+i.toString()).val(piva);
	t=roundNumber(c*piva,2);
	b=roundNumber(c*p,2);
	$("#tota_"+i.toString()).val(b);	
	$("#importe_"+i.toString()).val(t);
	$("#cana_"+i.toString()).val(c);
}

function add_itpfac(){
	var htm = <?=$campos ?>;
	can = itpfac_cont.toString();
	con = (itpfac_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#cana_"+can).numeric(".");
	itpfac_cont=itpfac_cont+1;
}

$(function(){
	$(".inputnum").numeric(".");
});
					
function del_itpfac(id){
	id = id.toString();
	$('#tr_itpfac_'+id).remove();
}
</script>
	<?php }else{?>
<!--		<script language="javascript" type="text/javascript">-->
<!--			ver=$("#importe_0").val();-->
<!--			alert(ver);-->
<!--		</script>-->
	<?php } ?>

<table align='center' width="80%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
		<table width="100%" style="margin: 0; width: 100%;">

			<tr>
				<td class="littletableheader">N&uacute;mero</td>
				<td class="littletablerow"><?php  echo str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?>&nbsp;</td>
				<td class="littletableheader"><?=$form->cliente->label  ?>*&nbsp;</td>
				<td class="littletablerow"><?=$form->cliente->output ?>&nbsp;</td>
				<td class="littletableheader"><?=$form->rifci->label  ?>*&nbsp;</td>
				<td class="littletablerow"><?=$form->rifci->output ?>&nbsp;</td>
			</tr>
			<tr>

				<td class="littletableheader"><?=$form->fecha->label  ?>*&nbsp;</td>
				<td class="littletablerow"><?=$form->fecha->output ?>&nbsp;</td>
				<td class="littletableheader"><?=$form->nombre->label  ?>*&nbsp;</td>
				<td class="littletablerow"><?=$form->nombre->output ?>&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?=$form->vd->label  ?>&nbsp;</td>
				<td class="littletablerow"><?=$form->vd->output ?>&nbsp;</td>
				<td class="littletableheader"><?=$form->direc->label  ?>&nbsp;</td>
				<td class="littletablerow"><?=$form->direc->output ?>&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
			</tr>

			<?php if($form->_status=='show'){?>
			<tr>
				<td class="littletableheader"><?=$form->peso->label  ?>&nbsp;</td>
				<td class="littletablerow" align="left"><?=$form->peso->output ?>&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
			</tr>
			<?php }?>

		</table>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
		<table width='100%'>
			<tr>
				<td class="littletableheader">C&oacute;digo</td>
				<td class="littletableheader">Descripci&oacute;n</td>
				<td class="littletableheader">Cantidad</td>
				<td class="littletableheader">Precio</td>
				<td class="littletableheader">Importe</td>


				<?php if($form->_status!='show') {?>
				<!--				<td class="littletableheader">&nbsp;</td>-->
				<?php } ?>
			</tr>
			<?php for($i=0;$i<$form->max_rel_count['itpfac'];$i++) {
				$obj1="codigoa_$i";
				$obj2="desca_$i";
				$obj3="cana_$i";
				$obj4="preca_$i";
				$obj6="tota_$i";
				$obj5="mostrado_$i";
				$obj7="importe_$i";
				$obj8="iva_$i";
				$obj9="costo_$i";
				$obj10="pvp_$i";
				$obj11="tdec_$i";
				?>
			<tr id='tr_itpfac_<?=$i ?>'>
				<td class="littletablerow"><?=$form->$obj1->output ?></td>
				<td class="littletablerow"><?=$form->$obj2->output ?></td>
				<td class="littletablerow" align="right"><?=$form->$obj3->output ?></td>
				<td class="littletablerow" align="right"><?=$form->$obj5->output ?></td>


				<?php if($form->_status!='show') {?>
				<td class="littletablerow" align="right"><?=$form->$obj7->output ?></td>
				<td class="littletablerow" align="right"><?=$form->$obj4->output ?></td>
				<td class="littletablerow" align="right"><?=$form->$obj6->output ?></td>
				<td><?=$form->$obj10->output ?></td>
				<td><?=$form->$obj8->output ?></td>
				<td><?=$form->$obj9->output ?></td>
				<td><input type="hidden" name="<?=$obj11 ?>" id="<?=$obj11 ?>" /></td>
				<td class="littletablerow"><a href=#
					onclick='del_itpfac(<?=$i ?>);return false;'>Eliminar</a></td>
					<?php }else{ ?>
				<td class="littletablerow" align="right"><?=($form->$obj5->output*$form->$obj3->output) ?></td>
				<?php }?>
			</tr>
			<?php } ?>

			<tr id='__UTPL__'>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>


				<?php if($form->_status!='show') {?>
				<!--				<td class="littletablefooterb" align="right">&nbsp;</td>-->
				<?php } ?>
			</tr>
			<?php if ($form->_status =='show'){?>
			<tr>
				<td class="littletableheader"><?=$form->ivat->label  ?></td>
				<td class="littletablerow"><?=$form->ivat->output?></td>
				<td class="littletableheader"><?=$form->totals->label  ?></td>
				<td class="littletablerow"><?=$form->totals->output ?></td>
				<td class="littletableheader"><?=$form->totalg->label  ?></td>
				<td class="littletablerow"><?=$form->totalg->output ?></td>
			</tr>
			<?php }?>
		</table>

		<?php echo $form_end     ?> <?php echo $container_bl ?> <?php echo $container_br ?>
		</td>
	</tr>

</table>

		<?php endif; ?>