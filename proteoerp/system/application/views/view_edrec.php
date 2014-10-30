<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'){
	echo $form->output;
} else {
	$html='<tr id="tr_editrec_<#i#>">';
	$campos=$form->template_details('editrec');
	foreach($campos as $nom=>$nan){
		$pivot=$nan['field'];
		$align = (strpos($pivot,'inputnum')) ? 'align="right"' : '';
		$html.='<td class="littletablerow" '.$align.'>'.$pivot.'</td>';
	}
}
if($form->_status!='show') {
	$html.='<td class="littletablerow"><a href=# onclick=\'del_editrec(<#i#>);return false;\'>'.img('images/delete.jpg').'</a></td>';
}
$html.='</tr>';
$campos=$form->js_escape($html);
if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
echo $form_begin;
if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
editrec_cont=<?php echo $form->max_rel_count['editrec'] ?>;
$(function(){
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	$("#vence").datepicker({dateFormat:"dd/mm/yy"});
	$(".inputnum").numeric(".");

	for(var i=0;i < <?php echo $form->max_rel_count['editrec']; ?>;i++){
		autocod(i.toString());
	}
	$('input[name^="cuota_"]').keypress(function(e) {
		if(e.keyCode == 13) {
		    add_editrec();
			return false;
		}
	});
});


function add_editrec(){
	var htm = <?php echo $campos; ?>;
	can = editrec_cont.toString();
	con = (editrec_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	$("#cantidad_"+can).numeric(".");
	$("#codigo_"  +can).focus();

	autocod(can);
	$("#cuota_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_editrec();
			return false;
		}
	});
	editrec_cont=editrec_cont+1;
}

function del_editrec(id){
	id = id.toString();
	$('#tr_editrec_'+id).remove();
}

function totalizar(){
	tp=tb=ti=ite=0;

	arr=$('input[name^="cuota_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind = this.name.substring(pos+1);
			tp1=Number($("#cuota_"+ind).val());
			ite=Number($("#total_"+ind).val());
			tp=tp+tp1;
			tb=tb+ite;
		}
	});
	$("#cuota").val(roundNumber(tp,2));
	$("#cuota_val").text(nformat(tp,2));
	$("#total").val(roundNumber(tb,2));
	$("#total_val").text(nformat(tb,2));
}

function cuotatot(i){
	ind    = i.toString();
	total  = Number($("#total_"+ind).val());
	alicu  = Number($("#alicuota_"+ind).val());

	impor  = total*alicu;
	$("#cuota_"+ind).val(roundNumber(impor,2));
	$("#cuota_"+ind+"_val").text(nformat(impor,2));
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
							$('#detalle_'+id).val('');
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
			$('#codigo_' +id).attr("readonly", "readonly");
			$('#codigo_' +id).val(ui.item.codigo);
			$('#detalle_'+id).val(ui.item.descrip);
			$('#total_'  +id).focus();
			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}

</script>
<?php } ?>
	<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
	<table width='100%'>
		<tr>
			<td class="littletablerowth"><?php echo $form->inmueble->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->inmueble->output; ?></td>
		</tr>
	</table>
	</fieldset>


	<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
	<table width='100%'>
		<tr>
<?php if($form->_status == 'show') { ?>	
			<td class="littletablerowth"><?php echo $form->numero->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->numero->output; ?></td>
<?php } ?>
			<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->fecha->output; ?></td>
			<td class="littletablerowth"><?php echo $form->vence->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->vence->output; ?></td>
			<td class="littletablerowth"><?php echo $form->status->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->status->output; ?></td>
		</tr>
	</table>
	</fieldset>
	<table width='100%'>
		<tr>
			<td>
			<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:230px'>
			<table width='100%'>
				<tr>
					<td bgcolor='#7098D0' width="80">tipo</td>
					<td bgcolor='#7098D0' width="80">codigo</td>
					<td bgcolor='#7098D0' width="80">detalle</td>
					<td bgcolor='#7098D0' width="80">total</td>
					<td bgcolor='#7098D0' width="80">alicuota</td>
					<td bgcolor='#7098D0' width="80">cuota</td>
				<?php if($form->_status!='show') {?>
					<td class="littletableheaderdet"><a href='#' onclick="add_editrec()" title='Agregar una linea'><?php echo img('images/agrega4.png'); ?></a></td>
				<?php } ?>
				</tr>
				<?php for($i=0;$i<$form->max_rel_count['editrec'];$i++) {
					$obj1 = "tipo_$i";
					$obj2 = "codigo_$i";
					$obj3 = "detalle_$i";
					$obj4 = "totald_$i";
					$obj5 = "alicuotad_$i";
					$obj6 = "cuotad_$i";
				?>
				<tr id='tr_editrec_<?php echo $i; ?>'>
					<td class="littletablerow"><?php echo $form->$obj1->output ?></td>
					<td class="littletablerow"><?php echo $form->$obj2->output ?></td>
					<td class="littletablerow"><?php echo $form->$obj3->output ?></td>
					<td class="littletablerow"><?php echo $form->$obj4->output ?></td>
					<td class="littletablerow"><?php echo $form->$obj5->output ?></td>
					<td class="littletablerow"><?php echo $form->$obj6->output ?></td>
					<?php if($form->_status!='show') {?>
						<td class="littletablerow"><a href="#" onclick='del_editrec(<?php echo $i; ?>);return false;'><?php echo img("images/delete.jpg"); ?></a></td>
					<?php } ?>
				</tr>
				<?php } ?>
				<tr id='__UTPL__'>
					<td class="littletablefooterb" align="right">&nbsp;</td>
					<td class="littletablefooterb" align="right">&nbsp;</td>
					<td class="littletablefooterb" align="right">&nbsp;</td>
					<td class="littletablefooterb" align="right">&nbsp;</td>
					<td class="littletablefooterb" align="right">&nbsp;</td>
					<td class="littletablefooterb" align="right">&nbsp;</td>
					<?php if($form->_status!='show') {?>
					<td class="littletablefooterb" align="right">&nbsp;</td>
					<?php } ?>
				</tr>
			</table>
			</div>
			</td>
		</tr>
	</table>
	<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
	<table style='border-collapse:collapse;padding:0px;width:100%;'>
		<tr>
			<td class="littletablerowth" rowspan='4'><?php echo $form->observa->label;  ?></td>
			<td class="littletablerow"   rowspan='4'><?php echo $form->observa->output; ?></td>
		</tr><tr>
			<td class="littletablerowth"><?php echo $form->total->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->total->output; ?></td>
		</tr><tr>
			<td class="littletablerowth"><?php echo $form->cuota->label;  ?></td>
			<td class="littletablerow"  ><?php echo $form->cuota->output; ?></td>
		</tr>
	</table>
	</fieldset>

<?php echo $form_end; ?>
