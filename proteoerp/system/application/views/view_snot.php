<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itsnot');
$scampos  ='<tr id="tr_itsnot_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['descrip']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['cant']['field'].  '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['saldo']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['entrega']['field'];
$scampos .='<td class="littletablerow"><a href=# onclick="del_itsnot(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itsnot_cont=<?php echo $form->max_rel_count['itsnot']; ?>;

$(function(){
	$(".inputnum").numeric(".");
});

function add_itsnot(){
	var htm = <?php echo $campos; ?>;
	can = itsnot_cont.toString();
	con = (itsnot_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
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
				<th colspan='5' class="littletableheader">Nota de Despacho <b><?php if($form->_status=='show' or $form->_status=='modify' ) echo str_pad($form->numero->output,8,0,0); ?></b></th>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fecha->label;    ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output;   ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->cliente->label;  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->cliente->output; ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->nombre->output;  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fechafa->label     ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fechafa->output    ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->factura->label; ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->factura->output;?>&nbsp;</td>
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
		<table width='100%'>
			<tr>
				<th colspan='6' class="littletableheader">Lista de Nota de despacho</th>
			</tr>
			<tr>
				<td class="littletableheader">C&oacute;digo</td>
				<td class="littletableheader">Descripci&oacute;n</td>
				<td class="littletableheader">Cantidad</td>
				<td class="littletableheader">Saldo</td>
				<td class="littletableheader">Entrega</td>
				<?php if($form->_status!='show') {?>
					<td class="littletableheader">&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itsnot'];$i++) {
				$it_codigo  = "codigo_$i";
				$it_descrip   = "descrip_$i";
				$it_cant    = "cant_$i";
				$it_saldo   = "saldo_$i";
				$it_entrega = "entrega_$i";
				$it_fact     = "itfactura_$i";

				$pprecios='';
			?>

			<tr id='tr_itsnot_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_descrip->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_cant->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_saldo->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_entrega->output?></td>
				<?php if($form->_status!='show') {?>
				<td class="littletablerow">
					<a href='#' onclick='del_itsnot(<?php echo $i ?>);return false;'>Eliminar</a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>

			<tr id='__UTPL__'>
				<td id='cueca'></td>
			</tr>
		</table>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>
		<?php echo $form_end; ?>
		</td>

	</tr>
</table>
<?php endif; ?>
