<?php
echo $form_begin;

$container_tr=join('&nbsp;', $form->_button_container['TR']);
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);

if($form->_status!='show'){

	if($form->_status=='create'){
		$jsfecha  = '$(\'#fecha\').val()';
		$jscodban = '$(\'#codbanc\').val()';
	}else{
		$jsfecha  = $form->js_escape(dbdate_to_human($form->fecha->value, $form->fecha->format));
		$jscodban = $form->js_escape($form->codbanc->value);
	}
?>
<script language="javascript" type="text/javascript">
var bmov_cont =0;

$(function(){
	$("#fecha").datepicker({
		dateFormat:"mm/yy",
		onSelect: function(dateText) {
			cambiaban();
		},
	});

	$("#codbanc").change(function(){
		cambiaban();
	});

	$(".inputnum").numeric(".");

	jQuery("#tliable").jqGrid({
		datatype: "local",
		height: 230,
		colNames:["Fecha","Tipo", "N&uacute;mero", "Monto"," "],
		colModel:[
			{name:"fecha"  , index:"fecha"  , width:70   },
			{
				name: "tipo",
				index:"tipo",
				align:"center",
				width:45,
				formatter: function (cellvalue, options, rowObject) {
					return cellvalue+'<input type="hidden" name="ittipo_'+rowObject.id+'" id="ittipo_'+rowObject.id+'" value="'+cellvalue+'">';
				}
			},
			{name:"numero" , index:"numero" , width:100 , align:"right" },
			{
				name: "monto",
				index:"monto",
				width:100,
				align:"right",
				sorttype:"float",
				formatter: function (cellvalue, options, rowObject) {
					return nformat(cellvalue,2)+'<input type="hidden" name="itmonto_'+rowObject.id+'" id="itmonto_'+rowObject.id+'" value="'+cellvalue+'">';
				}
			},
			{
				name:'concilia',
				width:40,
				align:"center",
				formatter: function (cellvalue, options, rowObject) {
					if(cellvalue){
						checkp = 'checked="checked"';
					}else{
						checkp = '';
					}
					return '<input type="checkbox" name="itid_'+rowObject.id+'" id="itid_'+rowObject.id+'" value="'+rowObject.id+'" onchange="tilda('+rowObject.id+')" '+checkp+'>';
				}
			}

		],
		multiselect: false,
		caption: "Efectos liables",
		rowNum:9000000000,
		//multiselect: true
	});

	cambiaban();
});


function cambiaban(){
	var codbanc= <?php echo $jscodban; ?>;
	var fecha  = <?php echo $jsfecha ; ?>;
	if(codbanc!='' && fecha!=''){
		jQuery("#tliable").jqGrid("clearGridData",true).trigger("reloadGrid");
		$.ajax({
			url: "<?php echo site_url('ajax/buscaconci'); ?>",
			dataType: "json",
			type: "POST",
			data: {"codbanc" : codbanc , "fecha": fecha},
			success: function(data){
					var cana = 0;
					$.each(data,
						function(id, val){
							//val.rowid = id;
							jQuery("#tliable").jqGrid("addRowData",val.id,val);
							cana ++;
						}
					);
					totalizar();
				},
		});
	}
}

function actualizalist(){
	var codbanc= <?php echo $jscodban; ?>;
	var fecha  = <?php echo $jsfecha ; ?>;
	if(codbanc!='' && fecha!=''){

		//Eliminas las no tildadas
		var lista = jQuery("#tliable").getDataIDs();
		for(i=0;i<lista.length;i++){
			//rowData = jQuery("#tliable").getRowData(lista[i]);
			if(!$('#itid_'+lista[i]).is(':checked')){
				jQuery("#tliable").delRowData(lista[i]);
			}
		}
		//fin de la eliminacion de las no tildadas

		$.ajax({
			url: "<?php echo site_url('ajax/buscaconci'); ?>",
			dataType: "json",
			type: "POST",
			data: {"codbanc" : codbanc , "fecha": fecha},
			success: function(data){
					var cana = 0;
					$.each(data,
						function(id, val){
							//val.rowid = id;
							if(jQuery.isEmptyObject(jQuery("#tliable").jqGrid('getRowData', val.id))){
								jQuery("#tliable").jqGrid("addRowData",val.id,val);
							}
						}
					);
					totalizar();
				},
		});
	}
}

function del_itbmov(id){
	id = id.toString();
	$('#tr_bmov_'+id).remove();
}

function tilda(id){
	if(id>0){
		//Realiza lo marca de conciliado
		var fecha =<?php echo $jsfecha ; ?>;
		$.ajax({
			url: "<?php echo site_url('finanzas/bconci/concilia'); ?>",
			dataType: "json",
			type: "POST",
			data: {"id" : id ,"fecha": fecha, act : $('#itid_'+id).is(':checked')},
			success: function(data){
				if(data.status=='A'){
					jQuery("#tliable").jqGrid('getLocalRow', id).concilia = ! jQuery("#tliable").jqGrid('getLocalRow', id).concilia;
				}else{
					$('#itid_'+id).attr("checked", !$('#itid_'+id).attr("checked"));
					$.prompt(data.msj);
				}
				totalizar();
			}
		}).fail(function() { $('#itid_'+id).attr("checked", !$('#itid_'+id).attr("checked")); });
		//fin de la marca
	}
}

function totalizar(){

	var total = 0;
	var nc = 0;
	var nd = 0;
	var ch = 0;
	var de = 0;
	var arr=$('input[name^="itid_"]');
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind = this.name.substring(pos+1);
			if(this.checked){
				monto  = Number($('#itmonto_'+ind).val());
				tipo   = $('#ittipo_'+ind).val();
				if(tipo=='CH' || tipo=='ND'){
					monto=(-1)*monto;
				}
				total  = total+monto;
				if(tipo == 'CH'){
					ch = ch+monto;
				}else if(tipo == 'ND'){
					nd = nd+monto;
				}else if(tipo == 'NC'){
					nc = nc+monto;
				}else if(tipo == 'DE'){
					de = de+monto;
				}
			}
		}
	});

	//$("#total").val(roundNumber(total,2));
	$("#conciliado").text(nformat(total,2));
	$("#deposito").val(roundNumber(de,2));
	$("#cheque"  ).val(roundNumber((-1)*ch,2));
	$("#debito"  ).val(roundNumber((-1)*nd,2));
	$("#credito" ).val(roundNumber(nc,2));

	$("#deposito_val").text(nformat(de,2));
	$("#cheque_val"  ).text(nformat((-1)*ch,2));
	$("#debito_val"  ).text(nformat((-1)*nd,2));
	$("#credito_val" ).text(nformat(nc,2));
}

</script>
<?php } ?>

<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table width='100%' style='font-size:11pt;background:#F2E69D;'>
	<tr>
		<td><b><?php echo $form->codbanc->label;     ?></b></td>
		<td colspan='3'><?php echo $form->codbanc->output;  ?><!-- <a href='#' onclick='actualizalist();return false;'>Actualizar</a> --></td>
	</tr>
	<tr>
		<td><b><?php echo $form->fecha->label;     ?></b></td>
		<td>   <?php echo $form->fecha->output;    ?></td>
		<td><b><?php echo $form->deposito->label;  ?></b></td>
		<td align="right">   <?php echo $form->deposito->output; ?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->saldoi->label;    ?></b></td>
		<td>   <?php echo $form->saldoi->output;   ?></td>
		<td><b><?php echo $form->credito->label;   ?></b></td>
		<td align="right">   <?php echo $form->credito->output;  ?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->saldof->label;    ?></b></td>
		<td>   <?php echo $form->saldof->output;   ?></td>
		<td><b><?php echo $form->cheque->label;    ?></b></td>
		<td align="right">   <?php echo $form->cheque->output;   ?></td>
	</tr>
	<tr>
		<td><b>Conciliado</b></td>
		<td><span id='conciliado'></span></td>
		<td><b><?php echo $form->debito->label;    ?></b></td>
		<td align="right">   <?php echo $form->debito->output;   ?></td>
	</tr>
</table>
<div style='border: 1px solid #9AC8DA;background: #FAFAFA'>
<table>
	<tr>
		<td><table id="tliable"></table></td>
		<td><p style='text-align:center;font-size:2.2em'>Seleccione el banco y la fecha a conciliar, luego marque en la tabla las transacciones conciliadas.</p></td>
	</tr>
</table>
</div>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end; ?>
