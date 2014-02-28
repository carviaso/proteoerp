<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
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
		height: 250,
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
		caption: "Efectos liables. Seleccionar: <a style='color:#5BF2E5;font-weight:bold;' href='javascript:tildatodos()'>Todos</a> <a style='color:#5BF2E5;font-weight:bold;' href='javascript:destildatodos()'>Ninguno</a> <a style='color:#5BF2E5;font-weight:bold;' href='javascript:inviertetodos()'>Invertir</a>",
		rowNum:9000000000,
		onSelectRow:
			function(id){
				if (id){
					//var ret = $(gridId1).getRowData(id);
					$.get('<?php echo site_url('finanzas/bconci/localizador'); ?>'+'/'+id,
						function(data){
							$("#traconsul").html(data);
					});
				}
			}

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

function tildatodos(){
	$('[id^="itid_"]:checkbox').each(function(){
		if(!$(this).is(':checked')){
			$(this).attr('checked',true);
			tilda($(this).attr('value'));
		}
	});
}

function destildatodos(){
	$('[id^="itid_"]:checkbox').each(function(){
		if($(this).is(':checked')){
			$(this).attr('checked',false);
			tilda($(this).attr('value'));
		}
	});
}

function inviertetodos(){
	$('[id^="itid_"]:checkbox').each(function(){
		$(this).attr("checked", !$(this).attr("checked"));
		tilda($(this).attr('value'));
	});
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

	var saldoi = Number($('#saldoi').val());
	var saldof = Number($('#saldof').val());
	var tconcil= saldof-saldoi-total;
	$("#tconcil").text(nformat(tconcil,2));

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

	var cdeposito= Number($("#cdeposito").val());
	var ccheque  = Number($("#ccheque"  ).val());
	var cdebito  = Number($("#cdebito"  ).val());
	var ccredito = Number($("#ccredito" ).val());

	$("#ddeposito").text(nformat(cdeposito-de,2));
	$("#dcheque"  ).text(nformat(ccheque  +ch,2));
	$("#ddebito"  ).text(nformat(cdebito  +nd,2));
	$("#dcredito" ).text(nformat(ccredito -nc,2));
}

</script>
<?php } ?>

<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table style='width:100%'>
	<tr>
		<td colspan='2'>
			<table style='width:100%;font-size:11pt;background:#F2E69D;'>
				<tr>
					<td><b><?php echo $form->codbanc->label;?>*</b></td>
					<td colspan='3'><?php echo $form->codbanc->output;  ?></td>
				</tr><tr>
					<td style='width:25%'><b><?php echo $form->deposito->label;  ?></b></td>
					<td style='width:25%' align="right"><?php echo $form->deposito->output;  ?></td>
					<td style='width:25%' align="right"><?php echo $form->cdeposito->output; ?></td>
					<td style='width:25%' align="right"><span id='ddeposito'><?php echo ($form->_status=='show')? nformat($form->deposito->value-$form->cdeposito->value):nformat(0); ?></span></td>
				</tr><tr>
					<td><b><?php echo $form->credito->label;   ?></b></td>
					<td align="right"><?php echo $form->credito->output;   ?></td>
					<td align="right"><?php echo $form->ccredito->output;  ?></td>
					<td align="right"><span id='dcredito'><?php echo ($form->_status=='show')? nformat($form->credito->value-$form->ccredito->value):nformat(0); ?></span></td>
				</tr><tr>
					<td><b><?php echo $form->cheque->label;    ?></b></td>
					<td align="right"><?php echo $form->cheque->output;   ?></td>
					<td align="right"><?php echo $form->ccheque->output;  ?></td>
					<td align="right"><span id='dcheque'><?php echo ($form->_status=='show')? nformat($form->cheque->value-$form->ccheque->value):nformat(0); ?></span></td>
				</tr><tr>
					<td><b><?php echo $form->debito->label;    ?></b></td>
					<td align="right"><?php echo $form->debito->output;   ?></td>
					<td align="right"><?php echo $form->cdebito->output;  ?></td>
					<td align="right"><span id='ddebito'><?php echo ($form->_status=='show')? nformat($form->debito->value-$form->cdebito->value):nformat(0); ?></span></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
		<?php if($form->_status!='show'){ ?>
			<table id="tliable"></table>
		<?php } ?>
		</td>
		<td style='vertical-align:text-top;width:100%;'>
			<table style='font-size:11pt;width:100%;background:#C4C6FF;vertical-align:text-top;'>
				<tr>
					<td><b><?php echo $form->fecha->label;   ?>*</b></td>
					<td align="right"><?php echo $form->fecha->output;  ?></td>
				</tr><tr>
					<td><b><?php echo $form->saldoi->label;  ?>*</b></td>
					<td align="right"><?php echo $form->saldoi->output; ?></td>
				</tr><tr>
					<td><b><?php echo $form->saldof->label;  ?>*</b></td>
					<td align="right"><?php echo $form->saldof->output; ?></td>
				</tr>
			</table>

			<p style='text-align:center;font-size:2em'>
				<?php if($form->_status!='show'){ ?>
				<span style='font-size:1.2em;color:#1900FF' id='tconcil'>0,0</span>
				<br><span style='font-size:0.5em;color:#1900FF'>Monto por conciliar</span><br>
				<?php } ?>
				<span style='font-size:1.8em;' id='conciliado'><?php
				$tota = $form->deposito->value+$form->credito->value-$form->cheque->value-$form->debito->value;
				echo ($form->_status=='show')? nformat($tota):nformat(0); ?></span>
				<br><span style='font-size:0.5em;'>Monto conciliado</span>
			</p>
			<?php if($form->_status!='show'){ ?>
			<div id='traconsul' style='border: 1px solid #9AC8DA;background: #FAFAFA;overflow-y: auto;max-height:100px;'>
				<p style="text-align:center">Seleccione cualquier efecto liable para ver los detalles.</p>
			</div>
			<?php } ?>
		</td>
	</tr>
</table>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end; ?>
