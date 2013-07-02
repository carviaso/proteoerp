<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$ccampos=$form->detail_fields['itords'];
$campos='<tr id="tr_itords_<#i#>">';
$campos.=' <td class="littletablerow">'.$ccampos['codigo']['field'].'</td>';
$campos.=' <td class="littletablerow">'.$ccampos['descrip']['field'].'</td>';
$campos.=' <td class="littletablerow" align="right">'.$ccampos['precio']['field'].'</td>';
$campos.=' <td class="littletablerow" align="right">'.$ccampos['tasaiva']['field'].'</td>';
$campos.=' <td class="littletablerow" align="right">'.$ccampos['iva']['field'].'</td>';
$campos.=' <td class="littletablerow" align="right">'.$ccampos['importe']['field'].'</td>';
$campos.=' <td class="littletablerow">'.$ccampos['departa']['field'].'</td>';
$campos.=' <td class="littletablerow">'.$ccampos['sucursal']['field'].'</td>';
$campos.=' <td class="littletablerow" align="center"><a href=\'#\' onclick="del_itords(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($campos);

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itordc_cont=<?php echo $form->max_rel_count['itords']; ?>;
var departa  = '';
var sucursal = '';
$(function(){
	$(".inputnum").numeric(".");
	totalizar();

	$("#fecha").datepicker({    dateFormat: "dd/mm/yy" });
	for(var i=0;i < <?php echo $form->max_rel_count['itords']; ?>;i++){
		autocod(i.toString());
	}

	$('#proveed').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasprv'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
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
			$("#proveed").attr("readonly", "readonly");
			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);
			$('#proveed').val(ui.item.proveed);
			setTimeout(function() {  $("#proveed").removeAttr("readonly"); }, 1500);
		}
	});
});

function totalizar(){
	tp=tb=ti=ite=0;

	arr=$('input[name^="importe_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind = this.name.substring(pos+1);
			tp1=Number($("#precio_"+ind).val());
			ite=Number(this.value);

			tp=tp+tp1;
			tb=tb+ite;
		}
	});

	$("#totpre").val(roundNumber(tp,2));
	$("#totpre_val").text(nformat(tp,2));
	$("#totbruto").val(roundNumber(tb,2));
	$("#totbruto_val").text(nformat(tb,2));
	totiva=roundNumber(tb-tp,2);
	$("#totiva").val(totiva);
	$("#totiva_val").text(nformat(totiva,2));
}

function importe(i){
	ind    = i.toString();
	precio = Number($("#precio_"+ind).val());
	iva    = Number($("#tasaiva_"+ind).val());
	miva   = precio*iva/100;
	impor  = precio+miva;
	$("#iva_"+ind).val(miva);
	$("#importe_"+ind).val(roundNumber(impor,2));
	$("#iva_"+ind+"_val").text(nformat(miva,2));
	$("#importe_"+ind+"_val").text(nformat(impor,2));
	totalizar();
}

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/automgas'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q" :req.term},
				success:
					function(data){
						var sugiere = [];

						if(data.length==0){
							$('#codigo_'+id).val('');
							$('#descrip_'+id).val('');
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
			$('#codigo_'+id).attr("readonly", "readonly");

			$('#codigo_'+id).val(ui.item.codigo);
			$('#descrip_'+id).val(ui.item.descrip);
			$('#precio_'+id).focus();
			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}

//Para que el proximo registro tenga el mismo departamento
function gdeparta(val){
	departa=val;
}

//Para que el proximo registro tenga la misma sucursal
function gsucursal(val){
	sucursal=val;
}

function add_itords(){
	var htm = <?php echo $campos; ?>;
	can = itordc_cont.toString();
	con = (itordc_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PTPL__").after(htm);
	$("#departa_"+can).val(departa);
	$("#sucursal_"+can).val(sucursal);
	autocod(itordc_cont);
	itordc_cont=itordc_cont+1;
}

function del_itords(id){
	id = id.toString();
	obj='#tr_itords_'+id;
	$(obj).remove();
	totalizar();
}
</script>
<?php } ?>
<table align='center' width="100%" >
	<tr>
	<td>
		<fieldset style='border: 1px outset #9AC8DA;background: #FFFDE9;'>
		<table width="100%"  style="margin:0;width:100%;">
			<tr>
				<td class="littletablerowth"><?php echo $form->proveed->label;  ?></td>
				<td class="littletablerow"  ><?php echo $form->proveed->output; ?></td>
				<td class="littletablerowth"><?php echo $form->nombre->label;   ?></td>
				<td class="littletablerow"  ><?php echo $form->nombre->output;  ?></td>
			</tr><tr>
				<td class="littletablerowth"><?php echo $form->fecha->label;    ?></td>
				<td class="littletablerow"  ><?php echo $form->fecha->output;   ?></td>
				<td class="littletablerowth"><?php echo $form->numero->label;   ?></td>
				<td class="littletablerow"  ><?php echo $form->numero->output;  ?></td>
			</tr>
		</table>
		</fieldset>


		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:250px'>
		<table width='100%'>
			<tr  id='__PTPL__'>
				<td class="littletableheaderdet">C&oacute;digo</td>
				<td class="littletableheaderdet">Descripci&oacute;n</td>
				<td class="littletableheaderdet">Precio</td>
				<td class="littletableheaderdet">Tasa</td>
				<td class="littletableheaderdet">Impuesto</td>
				<td class="littletableheaderdet">Importe</td>
				<td class="littletableheaderdet">Depto.</td>
				<td class="littletableheaderdet">Sucursal</td>
				<?php if($form->_status!='show') {?>
					<td class="littletableheaderdet" >&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itords'];$i++){
				$it_codigo  = "codigo_${i}";
				$it_descrip = "descrip_${i}";
				$it_precio  = "precio_${i}";
				$it_tasaiva = "tasaiva_${i}";
				$it_iva     = "iva_${i}";
				$it_importe = "importe_${i}";
				$it_departa = "departa_${i}";
				$it_sucursal= "sucursal_${i}";
			?>

			<tr id='tr_itsnot_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" ><?php echo $form->$it_codigo->output;  ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_descrip->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_precio->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_tasaiva->output; ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_iva->output;     ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_importe->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_departa->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_sucursal->output;?></td>
				<?php if($form->_status!='show') {?>
					<td class="littletablerow" align="center"><a href='#' onclick='del_itords(<?php echo $i; ?>);return false;'><?php echo img("images/delete.jpg"); ?></a></td>
				<?php } ?>
			</tr>
			<?php } ?>
			<tr id='__UTPL__'>
				<td colspan='<?php echo ($form->_status!='show')? 8 : 9; ?>'></td>
			</tr>
		</table>
		</div>
<?php echo $container_bl.$container_br; ?>
		<legend class="titulofieldset" style='color: #114411;'>Totales</legend>
			<table width='100%'>
				<tr>
					<td ><?php echo $form->condi->label  ?></td>
					<td class="littletableheader" align='right'><?php echo $form->totpre->label  ?>&nbsp;</td>
					<td class="littletablerow"    align='right'><?php echo $form->totpre->output; ?>&nbsp;</td>
				</tr><tr>
					<td rowspan='2'><?php echo $form->condi->output;  ?></td>
					<td class="littletableheader" align='right'><?php echo $form->totiva->label  ?>&nbsp;</td>
					<td class="littletablerow"    align='right'><?php echo $form->totiva->output ?>&nbsp;</td>
				</tr><tr>
					<td class="littletableheader" align='right'><?php echo $form->totbruto->label  ?>&nbsp;</td>
					<td class="littletablerow"    align='right'><?php echo $form->totbruto->output ?>&nbsp;</td>
				</tr>
			</table>
		</fieldset>

	  <td>
	<tr>
<table>
<?php echo $form_end; ?>
<?php endif; ?>
