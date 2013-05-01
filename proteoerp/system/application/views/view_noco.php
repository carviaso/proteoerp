<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
echo $form->output;
else:

$campos=$form->template_details('itnoco');
$scampos  ='<tr id="tr_itnoco_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['concepto']['field'].   '</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['descrip']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['it_tipo']['field'].   '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['grupo']['field']. '</td>';

$scampos .= '<td class="littletablerow"><a href=# onclick="del_itnoco(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itnoco_cont=<?php echo $form->max_rel_count['itnoco']; ?>;

$(function(){
	$(".inputnum").numeric(".");
});

function add_itnoco(){
	var htm = <?php echo $campos; ?>;
	can = itnoco_cont.toString();
	con = (itnoco_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	itnoco_cont=itnoco_cont+1;
}
function del_itnoco(id){
	id = id.toString();
	$('#tr_itnoco_'+id).remove();
}
</script>
<?php } ?>

<table align='center' width="95%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
		<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
		<legend class="titulofieldset" style='color: #114411;'>Contrato de N&oacute;mina</legend>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td class="littletableheader"><?php echo $form->codigo->label;    ?>*&nbsp;</td>
				<td class="littletablerow"><?php echo $form->codigo->output;   ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->nombre->label;  ?>&nbsp;</td>
				<td class="littletablerow"><?php echo $form->nombre->output; ?>&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->tipo->label     ?>&nbsp;</td>
				<td class="littletablerow"><?php echo $form->tipo->output    ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->observa1->label; ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->observa1->output;   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletablerow">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->observa2->output ?>&nbsp;</td>
			</tr>
		</table>
		</fieldset>
		<br>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset style='border: 2px outset #9AC8DA;background: #EFEFFF;'>
		<legend class="titulofieldset" style='color: #114411;'>Lista de Conceptos</legend>
		<table width='100%'>
			<tr>
				<td class="littletableheaderdet">C&oacute;ncepto</td>
				<td class="littletableheaderdet">Descripci&oacute;n</td>
				<td class="littletableheaderdet">Tipo</td>
				<td class="littletableheaderdet">Grupo</td>
				<?php if($form->_status!='show') {?>
				<td class="littletableheaderdet">&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itnoco'];$i++) {
				$concep  = "concepto_$i";
				$descrip = "descrip_$i";
				$tipo    = "it_tipo_$i";
				$grupo   = "grupo_$i";
				?>

			<tr id='tr_itnoco_<?php echo $i; ?>'>
				<td class="littletablerow" align="left"><?php echo $form->$concep->output; ?></td>
				<td class="littletablerow" align="left"><?php echo $form->$descrip->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$tipo->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$grupo->output;   ?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow"><a href='#'
					onclick='del_itnoco(<?php echo $i; ?>);return false;'>Eliminar</a></td>
					<?php } ?>
			</tr>
			<?php } ?>

			<tr id='__UTPL__'>
				<td id='cueca' colspan="4" class="littletableheaderdet"></td>
			</tr>
		</table>
		</fieldset>
		<?php echo $container_bl ?> <?php echo $container_br ?></td>
		<?php echo $form_end; ?></td>
	</tr>
</table>
		<?php endif; ?>
