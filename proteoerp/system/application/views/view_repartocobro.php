<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
echo $form_begin;
?>
<script language="javascript" type="text/javascript">
var bmov_cont =0;

$(function(){
	$(".inputnum").numeric(".");
	jQuery("#trepa").jqGrid({
		datatype: "local",
		height: 250,
		colNames:["Fecha","Tipo","Factura","Cliente","Nombre","F.Pago","Saldo"],
		colModel:[
			{name:"fecha"  , index:"fecha"  , width:60  },
			{name:"tipo"   , index:"tipo"   , width:20 , hidden:true },
			{name:"numero" , index:"numero" , width:60 , align:"left" ,
				formatter: function (cellvalue, options, rowObject){
					return rowObject.tipo+rowObject.numero;
				}
			},
			{name:"cliente", index:"cliente", width:50  , align:"center"},
			{name:"nombre" , index:"nombre" , width:210 , align:"left"  },
			{name:"repcob" , index:"repcob" , width:180 , align:"center",
				formatter: function (cellvalue, options, rowObject){
					var chef = '';
					var chch = '';
					var chmi = '';
					var rt   = '';

					if(rowObject.repcob=='EF'){
						chef='checked';
					} else if(rowObject.repcob=='CH'){
						chch='checked';
					} else if(rowObject.repcob=='MI'){
						chmi='checked';
					}

					rt=rt+'<input type="radio" id="itpagoef_'+rowObject.id+'"  name="itpago['+rowObject.id+']" value="EF" onchange="totalizar()" ondblclick="this.checked=false;totalizar();" '+chef+'> <b style="font-size:0.8em;font-weight: bold;color:green;" >Efectivo </b>';
					rt=rt+'<input type="radio" id="itpagoch_'+rowObject.id+'"  name="itpago['+rowObject.id+']" value="CH" onchange="totalizar()" ondblclick="this.checked=false;totalizar();" '+chch+'> <b style="font-size:0.8em;font-weight: bold;color:blue;"  >Cheque   </b>';
					rt=rt+'<input type="radio" id="itpagomi_'+rowObject.id+'"  name="itpago['+rowObject.id+']" value="MI" onchange="totalizar()" ondblclick="this.checked=false;totalizar();" '+chmi+'> <b style="font-size:0.8em;font-weight: bold;color:black;" >Mixto    </b>';

					return rt;
				}
			},
			{
				name: "monto",
				index:"monto",
				width:95,
				align:"right",
				sorttype:"float",
				formatter: function (cellvalue, options, rowObject) {
					return '<b>'+nformat(cellvalue,2)+'</b>'+'<input type="hidden" name="itmonto_'+rowObject.id+'" id="itmonto_'+rowObject.id+'" value="'+cellvalue+'">';
				}
			}
		],
		multiselect: false,
		caption: "Facturas por Despachar",
		rowNum:9000000000,
		onSelectRow:
			function(id){
				if (id){
					//var ret = $(gridId1).getRowData(id);
					//$.get('<?php echo site_url('finanzas/bconci/localizador'); ?>'+'/'+id,
					//	function(data){
					//		$("#traconsul").html(data);
					//});
					//alert(id);
				}
			}

		//multiselect: true
	});

	//llena la tabla
	$.ajax({
	url: "<?php echo site_url('ajax/buscacobrep'); ?>",
	dataType: "json",
	type: "POST",
	data: {"id" : <?php echo $id; ?> },
	success: function(data){
			var cana = 0;
			$.each(data,
				function(id, val){
					//val.rowid = id;
					if(jQuery.isEmptyObject(jQuery('#trepa').jqGrid('getRowData', val.id))){
						jQuery('#trepa').jqGrid('addRowData',val.id,val);
					}
				}
			);
			totalizar();
		},
	});
	//fin llena la tabla

});

function mtotal(){
	var monto=0;
	var arr=$("input[id^='itmonto_']");
	jQuery.each(arr, function() {
		nom=this.id;
		pos=this.id.lastIndexOf('_');
		if(pos>0){
			monto  = monto + Number(this.value);
		}
	});
	return monto;
}

function totalizar(){
	var total = 0;
	var mi    = 0;
	var ef    = 0;
	var ch    = 0;
	var falta = 0;

	var arr=$("input[name^='itpago']:radio");
	jQuery.each(arr, function() {
		nom=this.id;
		pos=this.id.lastIndexOf('_');
		if(pos>0){
			ind = this.id.substring(pos+1);
			if(this.checked){
				//alert(nom);
				monto  = Number($('#itmonto_'+ind).val());
				tipo   = this.value;

				if(tipo=='CH'){
					ch = ch + monto;
					total  = total + monto;
				}else if(tipo=='EF'){
					ef = ef + monto;
					total  = total + monto;
				}else if(tipo=='MI'){
					mi = mi + monto;
					total  = total + monto;
				}else{
					falta= falta + monto;
				}
			}
		}
	});
	falta= (falta+mtotal())-total;

	$("#ch_val").text(nformat(ch,2));
	$("#ef_val").text(nformat(ef,2));
	$("#mi_val").text(nformat(mi,2));
	$("#fa_val").text(nformat(falta,2));

	$("#monto_val" ).text(nformat(total,2));
	$("#monto" ).val(roundNumber(total,2));
}

</script>

<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>

<table id="trepa"></table>

<table style='font-size:1.5em; width:80%' align='center'>
	<tr>
		<td>Efectivo  </td><td style='text-align:right'><span id='ef_val'style='color:green'></span></td>
		<td>Cheque    </td><td style='text-align:right'><span id='ch_val'style='color:blue' ></span></td>
	</tr><tr>
		<td>Mixto     </td><td style='text-align:right'><span id='mi_val'style='color:black'></span></td>
		<td>Por cobrar</td><td style='text-align:right'><span id='fa_val'style='color:red'  ></span></td>
	</tr><tr>
		<td colspan='4' style='font-size:1.5em;font-weight: bold;text-align:center'><?php echo $form->monto->label;  ?> <?php echo $form->monto->output; ?></td>
	</tr>
</table>
<p style='text-align:center;font-size:0.8em'>Para desincorporar haga doble click en la forma de pago seleccionada.</p>
<?php echo $form_end; ?>
