<?php
echo $form_begin;

$container_tr=join('&nbsp;', $form->_button_container['TR']);
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$mod=true;

$campos=$form->template_details('itlcierre');
$scampos  ='<tr id="tr_itlcierre_<#i#>">';
$scampos .='<td class="littletablerow" align="center">'.$campos['itcodigo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left"  >'.$campos['itdescrip']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itunidades']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itcestas']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center">'.$campos['itpeso']['field'].'</td>';
$scampos .='<td class="littletablerow" ><a href=# onclick="del_itlcierre(<#i#>);return false;">'.img('images/delete.jpg').'</a></td>';
$scampos .='</tr>';
$campos=$form->js_escape($scampos);

if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
var itlcierre_cont =<?php echo $form->max_rel_count['itlcierre']; ?>;

$(function(){
	$("#fecha").datepicker({ dateFormat: "dd/mm/yy" });
	$(".inputnum").numeric(".");
	$('.inputnum').focus(function (){ $(this).select(); });
	$('.inputnum').click(function (){ $(this).select(); });

	for(var i=0;i < <?php echo $form->max_rel_count['itlcierre']; ?>;i++){
		autocod(i.toString());
	}

<?php
	if($this->rapyd->uri->is_set('create')){
		$sel=array('a.codigo','a.descrip');
		$this->db->select($sel);
		$this->db->from('lprod AS a');
		$this->db->where('a.fecha',$fecha);
		$this->db->group_by('a.codigo');
		$this->db->order_by('a.codigo');

		$query = $this->db->get();
		foreach ($query->result() as $row){
			$jscodigo  = $form->js_escape($row->codigo);
			$jsdescrip = $form->js_escape($row->descrip);
			echo "\t".'truncate();'."\n";
			echo "\t".'ccan=add_itlcierre();'."\n";
			echo "\t".'$("#itcodigo_"+ccan).val('.$jscodigo.');'."\n";
			echo "\t".'$("#itdescrip_"+ccan).val('.$jsdescrip.');'."\n";
			echo "\t".'$("#itdescrip_"+ccan+"_val").text('.$jsdescrip.');'."\n";
		}
	}
?>
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
	$("#__UTPL__lcierre").before(htm);

	$("#itcestas_"+can).numeric(".");
	$("#itunidades_"+can).numeric(".");
	autocod(can);
	$('#itcodigo_'+can).focus();

	itlcierre_cont=itlcierre_cont+1;
	return can;
}

function autocod(can){
		$('#itcodigo_'+can).autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinv'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$('#itcodigo_'+can).val('')
							$('#itdescrip_'+can).val('');
							$('#itdescrip_'+can+'_val').text('');
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
			$('#itcodigo_'+can).attr("readonly", "readonly");

			$('#itcodigo_'+can).val(ui.item.codigo);
			$('#itdescrip_'+can).val(ui.item.descrip);
			$('#itdescrip_'+can+'_val').text(ui.item.descrip);

			totalizar();
			setTimeout(function() {  $('#itcodigo_'+can).removeAttr("readonly"); }, 1500);
		}
	});
}

function del_itlcierre(id){
	id = id.toString();
	$('#tr_itlcierre_'+id).remove();
}

function totalizar(){
	var litros = Number($('#inventario').val());

	var arr=$('input[name^="itlitros_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			litros = litros+Number(Math.abs(this.value));
		}
	});

	$("#litros").val(roundNumber(litros,2));
	$("#litros_val").text(nformat(litros,2));
}

</script>
<?php } ?>

<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table width='100%' style='font-size:11pt;background:#F2E69D;'>
	<tr>
		<td><b><?php echo $form->dia->label;     ?></b></td>
		<td>   <?php echo $form->dia->output;    ?></td>
		<td><b><?php echo $form->enfriamiento->label;     ?></b></td>
		<td>   <?php echo $form->enfriamiento->output;    ?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->fecha->label;     ?></b></td>
		<td>   <?php echo $form->fecha->output;    ?></td>
		<td><b><?php echo $form->requeson->label; ?></b></td>
		<td>   <?php echo $form->requeson->output; ?></td>
	</tr>
</table>
<div style='border: 1px solid #9AC8DA;background: #FAFAFA'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr style='background:#030B7A;color:#FDFDFD;font-size:10pt;'>
		<th align="center">C&oacute;digo</th>
		<th align="center">Descripci&oacute;n</th>
		<th align="center">Unidades</th>
		<th align="center">Cestas</th>
		<th align="center"><?php
			$it_peso = 'itpeso_0';
			if(isset($form->$it_peso))
				echo $form->$it_peso->label;
		?></th>
		<?php if($form->_status!='show'){ ?>
		<th></th>
		<?php } ?>
	</tr>

<?php
	for($i=0;$i<$form->max_rel_count['itlcierre'];$i++) {
		$it_codigo   ='itcodigo_'.$i;
		$it_descrip  ='itdescrip_'.$i;
		$it_cestas   ='itcestas_'.$i;
		$it_unidades ='itunidades_'.$i;
		$it_peso     ='itpeso_'.$i;
?>
	<tr id='tr_itlcierre_<?php echo $i; ?>'>
		<td class="littletablerow" align="center"><?php echo $form->$it_codigo->output;    ?></td>
		<td class="littletablerow" align="left"  ><?php echo $form->$it_descrip->output;   ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_unidades->output;  ?></td>
		<td class="littletablerow" align="center"><?php echo $form->$it_cestas->output;    ?></td>
		<td class="littletablerow" align="center"><?php if(isset($form->$it_peso)) echo $form->$it_peso->output;      ?></td>
		<?php if($form->_status!='show'){ ?>
		<td><a href='#' onclick='del_itlcierre(<?php echo $i ?>);return false;'><?php echo img("images/delete.jpg");?></a></td>
		<?php } ?>
	</tr>
	<?php
	$mod=!$mod;
	} ?>
	<tr id='__UTPL__lcierre'>
		<td colspan='<?php echo ($form->_status!='show')? 6: 5 ?>' class="littletableheaderdet">&nbsp;</td>
	</tr>
</table>
</div>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end; ?>
