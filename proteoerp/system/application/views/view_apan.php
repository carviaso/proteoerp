<?php
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
if($form->_status <> 'show'){ ?>

<script language="javascript" type="text/javascript">
</script>
<?php } ?>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->numero->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->numero->output; ?></td>
		<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
		<td class="littletablerowth"><?php echo $form->tipo->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tipo->output; ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->clipro->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->clipro->output; ?></td>
		<td class="littletablerowth"><?php echo $form->nombre->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->nombre->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->monto->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->monto->output; ?></td>
	</tr>
</table>
</fieldset>

	<!-- tr>
		<td class="littletablerowth"><?php echo $form->reinte->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->reinte->output; ?></td>
	</tr -->

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr>
		<td class="littletablerowth">Observaciones:</td>
		<td class="littletablerow"  ><?php echo $form->observa1->output; ?></td>
		<td class="littletablerowth">&nbsp;</td>
		<td class="littletablerow"  ><?php echo $form->observa2->output; ?></td>
	</tr>
</table>
</fieldset>
<?php echo $form_end; ?>


<?php
/*
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:
$detalle='';
if($form->tipo->output == 'Cliente') $detalle='itccli';
else $detalle='itppro';
foreach($form->detail_fields[$detalle] AS $ind=>$data) $campos[]=$data['field'];
$campos='<tr id="tr_'.$detalle.'_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos.=' <td class="littletablerow"><a href=\'#\' onclick="del_'.$detalle.'(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$form->js_escape($campos);

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
var itppro_cont =<?php echo $form->max_rel_count['itppro']; ?>;
var itccli_cont=<?php echo $form->max_rel_count['itccli'];?>;
$(document).ready(function() {
	$(".inputnum").numeric(".");
});

function add_itppro(){
	var htm = <?php echo $campos; ?>;
	can = itppro_cont.toString();
	con = (itppro_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	itppro_cont=itppro_cont+1;
}

function add_itccli(){
	var htm = <?php echo $campos; ?>;
	var can = itccli_cont.toString();
	var con = (itccli_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__itccli").before(htm);
	itccli_cont=itccli_cont+1;
}

function del_itppro(id){
	id = id.toString();
	obj='#tr_itppro_'+id;
	$(obj).remove();
}

function del_itccli(id){
	id = id.toString();
	obj='#tr_itccli_'+id;
	$(obj).remove();
}
</script>
<?php }?>
<table align='center' width="99%">
	<tr>
		<td align='right'><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td><div class="alert"> <?php if(isset($form->error_string)) echo $form->error_string; ?></div></td>
	</tr>
	<tr>
		<td>
		<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
		<legend class="titulofieldset" style='color: #114411;'>Anticipo</legend>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->numero->label  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->numero->output ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->clipro->label  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->clipro->output ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->nombre->label  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->nombre->output ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->tipo->label   ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->tipo->output  ?>&nbsp; </td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fecha->label  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->monto->label  ?>*&nbsp;</td>
				<td class="littletablerow" align="right">    <?php echo $form->monto->output ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->observa1->label  ?>*&nbsp;</td>
				<td class="littletablerow" colspan="3">   <?php echo $form->observa1->output.' _'.$form->observa2->output ?>&nbsp; </td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<?php if($detalle=='itppro'){?>
	<tr>
		<td>
		<fieldset style='border: 2px outset #9AC8DA;background: #EFEFFF;'>
		<legend class="titulofieldset" style='color: #114411;'>Detalle Tipo Proveedor</legend>
		<table width='100%'>
			<tr>
				<td class="littletableheaderdet">N&uacute;mero</td>
				<td class="littletableheaderdet">N&uacute;mero Ppro</td>
				<td class="littletableheaderdet">Tipo</td>
				<td class="littletableheaderdet">Tipo Documento</td>
				<td class="littletableheaderdet">Fecha</td>
				<td class="littletableheaderdet">Monto</td>
				<td class="littletableheaderdet">Abono</td>
			</tr>
			<?php for($i=0; $i < $form->max_rel_count['itppro']; $i++) {
				$obj1 ="itnumero_$i";
				$obj4 ="itnumppro_$i";
				$obj2 ="tipoppro_$i";
				$obj3 ="tipo_doc_$i";
				$obj5 ="itfechap_$i";
				$obj6 ="itmontop_$i";
				$obj7 ="itabonop_$i";
			?>
			<tr id='tr_itppro_<?=$i ?>'>
				<td class="littletablerow" nowrap><?php echo $form->$obj1->output ?></td>
				<td class="littletablerow" nowrap><?php echo $form->$obj4->output ?></td>
				<td class="littletablerow"><?php echo $form->$obj2->output ?></td>
				<td class="littletablerow"><?php echo $form->$obj3->output  ?></td>				
				<td class="littletablerow"><?php echo $form->$obj5->output  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$obj6->output  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$obj7->output  ?></td>
				<?php if($form->_status!='show') {?>
					<td class="littletablerow"><a href='#' onclick='del_itppro(<?php echo $i; ?>);return false;'>Eliminar</a></td>
				<?php } ?>
			</tr>
			<?php }?>
			<tr id='__UTPL__'>
				<td colspan='8' class="littletableheaderdet">&nbsp;</td>
			</tr>
			
		</table>
		</fieldset>
		<?php if( $form->_status != 'show') {?>
			<input name="btn_add_itppro" value="Aplicar anticipo" onclick="add_itppto()" class="button" type="button">
		<?php } ?>
		</td>
	</tr>
	<?php }else if($detalle=='itccli'){?>
		<tr>
		<td>
		<fieldset style='border: 2px outset #9AC8DA;background: #EFEFFF;'>
		<legend class="titulofieldset" style='color: #114411;'>Detalle Tipo Cliente</legend>
		<table width='100%'>
			<tr>
				<td class="littletableheaderdet">N&uacute;mero</td>
				<td class="littletableheaderdet">N&uacute;mero Ccli</td>
				<td class="littletableheaderdet">Tipo</td>
				<td class="littletableheaderdet">Tipo Documento</td>
				<td class="littletableheaderdet">Fecha</td>
				<td class="littletableheaderdet">Monto</td>
				<td class="littletableheaderdet">Abono</td>
			</tr>
			<?php for($i=0; $i < $form->max_rel_count['itccli']; $i++) {
				$obj1 ="itnumero_c_$i";
				$obj4 ="numccli_$i";
				$obj2 ="tipoccli_$i";
				$obj3 ="tipo_doc_c_$i";
				$obj5 ="itfechac_$i";
				$obj6 ="itmontoc_$i";
				$obj7 ="itabonoc_$i";
			?>
			<tr id='tr_itccli_<?=$i ?>'>
				<td class="littletablerow" nowrap><?php echo $form->$obj1->output ?></td>
				<td class="littletablerow" nowrap><?php echo $form->$obj4->output ?></td>
				<td class="littletablerow"><?php echo $form->$obj2->output ?></td>
				<td class="littletablerow"><?php echo $form->$obj3->output  ?></td>				
				<td class="littletablerow"><?php echo $form->$obj5->output  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$obj6->output  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$obj7->output  ?></td>
				<?php if($form->_status!='show') {?>
					<td class="littletablerow"><a href='#' onclick='del_itccli(<?php echo $i; ?>);return false;'>Eliminar</a></td>
				<?php } ?>
			</tr>
			<?php }?>
			<tr id='__UTPL__itccli'>
				<td colspan='8' class="littletableheaderdet">&nbsp;</td>
			</tr>
			
		</table>
		</fieldset>
		<?php if( $form->_status != 'show') {?>
			<input name="btn_add_itccli" value="Aplicar anticipo" onclick="add_itccli()" class="button" type="button">
		<?php } ?>
		</td>
	</tr>
	<?php }?>
	<?php if($form->_status == 'show'){ ?>
	<tr>
		<td>
			<fieldset style='border: 2px outset #8A0808;background: #FFFBE9;'>
			<legend class="titulofieldset" style='color: #114411;'>Informacion del Registro</legend>
			<table width='100%' cellspacing='1' >
				<tr style='font-size:12px;color:#0B3B0B;background-color: #F7BE81;'>
					<td align='center' >Usuario</td>
					<td align='center' >Nombre </td>
					<td align='center' >Fecha  </td>
					<td align='center' >Hora   </td>
					<td align='center' >Transacci&oacute;n</td>
				</tr>
				<tr>
					<?php
						$mSQL="SELECT us_nombre FROM usuario WHERE us_codigo='".trim($form->_dataobject->get('usuario'))."'";
						$us_nombre = $this->datasis->dameval($mSQL);
					
					?>
					<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('usuario'); ?>&nbsp;</td>
					<td class="littletablerow" align='center'><?php echo $us_nombre ?>&nbsp;</td>
					<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('estampa'); ?>&nbsp;</td>
					<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('hora'); ?>&nbsp;</td>
					<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('transac'); ?>&nbsp;</td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
	<?php } ?>
</table>
<?php endif; ?>
*/
?>
