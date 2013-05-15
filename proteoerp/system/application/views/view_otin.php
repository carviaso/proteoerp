<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
echo $form->output;
else:

$campos=$form->template_details('itotin');
$scampos  ='<tr id="tr_itotin_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigo']['field'].   '</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['descrip']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['precio']['field'].   '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['impuesto']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['importe']['field'].  '</td>';
$scampos .='<td class="littletablerow"><a href=# onclick="del_itotin(<#i#>);return false;">Eliminar</a></td></tr>';
$campos   =$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itotin_cont=<?php echo $form->max_rel_count['itotin']; ?>;

$(function(){
	$(".inputnum").numeric(".");
	totalizar();
});

function importe(id){
	var ind      = id.toString();
	var impuesto = Number($("#impuesto_"+ind).val());
	var precio   = Number($("#precio_"+ind).val());
	var importe  = roundNumber(precio+impuesto,2);

	$("#importe_"+ind).val(importe);
	totalizar();
}

function totalizar(){
	var iva     =0;
	var totalg  =0;
	var totals =0;

	var impuesto=0;
	var importe=0;
	var arr=$('input[name^="importe_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind      = this.name.substring(pos+1);
			impuesto = Number($("#impuesto_"+ind).val());
			importe  = Number(this.value);

			iva     = iva+impuesto;
			totals  = totals+importe;
		}
	});
	$("#totalg").val(roundNumber(totals+iva,2));
	$("#totals").val(roundNumber(totals,2));
	$("#iva").val(roundNumber(iva,2));
}

function add_itotin(){
	var htm = <?php echo $campos; ?>;
	can = itotin_cont.toString();
	con = (itotin_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PTPL__").after(htm);
	itotin_cont=itotin_cont+1;
}

function post_modbus_botr(nind){
	ind=nind.toString();
	importe(nind);
	totalizar();
}
function del_itotin(id){
	id = id.toString();
	$('#tr_itotin_'+id).remove();
	totalizar();
}
</script>
<?php } ?>

<table align='center' width="95%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<th colspan='5' class="littletableheader">Otros Ingresos <b><?php if($form->_status=='show' or $form->_status=='modify' ) echo str_pad($form->numero->output,8,0,0); ?></b></th>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fecha->label;    ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output;   ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->cliente->label;  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->cliente->output; ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->nombre->output;  ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->orden->label     ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->orden->output    ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->rifci->label; ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->rifci->output;   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->tipo_doc->label  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->tipo_doc->output ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->direc->label  ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->direc->output ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->vence->label;    ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->vence->output;   ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->dire1->label  ?>&nbsp;</td>
				<td class="littletablerow" colspan='2'><?php echo $form->dire1->output ?>&nbsp;</td>
			</tr>
		</table>
		<br>
		</td>
	</tr>
	<tr>
		<td>
		<table width='100%'>
			<tr>
				<th colspan='6' class="littletableheader">Lista de Ingresos</th>
			</tr>
			<tr id='__PTPL__'>
				<td class="littletableheader">C&oacute;digo</td>
				<td class="littletableheader">Nombre</td>
				<td class="littletableheader">Precio</td>
				<td class="littletableheader">Impuesto</td>
				<td class="littletableheader">Importe</td>
				<?php if($form->_status!='show') {?>
				<td class="littletableheader">&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itotin'];$i++) {
				$it_codigo  = "codigo_${i}";
				$descrip    = "descrip_${i}";
				$impuesto   = "impuesto_${i}";
				$it_precio  = "precio_${i}";
				$it_importe = "importe_${i}";
				?>

			<tr id='tr_itotin_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$descrip->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_precio->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$impuesto->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_importe->output;?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow"><a href='#'
					onclick='del_itotin(<?php echo $i ?>);return false;'>Eliminar</a></td>
					<?php } ?>
			</tr>
			<?php } ?>

		</table>
		<?php echo $container_bl ?> <?php echo $container_br ?></td>
	</tr>
	<tr>
		<td>
		<table width='100%'>
			<tr>
				<th colspan='6' class="littletableheader">Res&uacute;men Financiero</th>
			</tr><tr>
				<td class="littletablerowth"><?php echo $form->vence->label    ?></td>
				<td class="littletablerowth"><?php echo $form->observa1->label ?></td>
				<td class="littletablerowth"><?php echo $form->totals->label   ?></td>
				<td class="littletablerow"  ><?php echo $form->totals->output  ?></td>
			</tr><tr>
				<td class="littletablerow"  ><?php echo $form->vence->output   ?></td>
				<td class="littletablerow"  ><?php echo $form->observa->output ?></td>
				<td class="littletablerowth"><?php echo $form->iva->label      ?></td>
				<td class="littletablerow"  ><?php echo $form->iva->output     ?></td>
			</tr><tr>
				<td class="littletablerow">&nbsp;</td>
				<td class="littletablerow"  ><?php echo $form->observa1->output ?></td>
				<td class="littletablerowth"><?php echo $form->totalg->label    ?></td>
				<td class="littletablerow"  ><?php echo $form->totalg->output   ?></td>
			</tr>
		</table>
		<?php echo $form_end; ?></td>
	</tr>
</table>
<?php endif; ?>
