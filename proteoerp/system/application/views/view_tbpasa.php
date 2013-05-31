<?php
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

if($form->_status <> 'show'){

$campos='\'\'';

?>
<script language="javascript" type="text/javascript">

$(document).ready(function() {
	$(".inputnum").numeric(".");
	$('#org').change(function() { traerutas(); });
	$('#dtn').change(function() { traerutas(); });
	$('#fecven').change(function() { traerutas(); });
});

function traerutas(){
	var desde = $('#org').val();
	var hasta = $('#dtn').val();
	var fecha = $('#fecven').val();
	if(desde!='' && hasta!=''){
		jQuery("#tbrutas").jqGrid('setGridParam',{url: '<?php echo site_url('pasajes/tbpasa/getbrutas'); ?>/'+desde+'/'+hasta+'/'+fecha }).trigger('reloadGrid');
	}
}


$("#tbrutas").jqGrid({
	ajaxGridOptions: { type: "POST"},
	jsonReader: { root: "data", repeatitems: false},
	ondblClickRow: puestos,
	url:     '<?php echo site_url('pasajes/tbpasa/getbrutas').'/0/0/0/0/0';  ?>',
	editurl: '<?php echo site_url('pasajes/tbpasa/'); ?>',
	datatype: "json",
	rowNum:  12,
	height: 180, 
	rowList:[],
	toolbar: [false],
	width:  680,
	hiddengrid: false,
	postdata: { tboficiid: "wapi"},
	colNames:['id', 'Ruta','Salida', 'Toque','Orden', 'Unidad', 'Origen', 'Destino', 'Precio'],
	colModel:[
		{name:'id',      index:'id',      width: 10, hidden:true},
		{name:'codrut',  index:'codrut',  width: 25, editable:false, search: true, align:'center' },
		{name:'horsal',  index:'horsal',  width: 30, editable:false, search: true, align:'center' },
		{name:'hora',    index:'hora',    width: 30, editable:false, search: true, align:'center' },
		{name:'orden',   index:'orden',   width: 25, editable:false, search: true, align:'center' },
		{name:'tipuni',  index:'tipuni',  width: 30, editable:false, search: true, align:'center' },
		{name:'origen',  index:'origen',  width:110, editable:false, search: true },
		{name:'destino', index:'destino', width:110, editable:false, search: true },
		{name:'precio',  index:'precio',  width: 40, editable:false, search: true, editoptions: {size:10,maxlength:10,dataInit:function(elem){$(elem).numeric();}},formatter:'number',formatoptions:{decimalSeparator:".",thousandsSeparator:",",decimalPlaces:2}, align:'right' },

	],
});
		
function puestos(){
	var id = $("#tbrutas").jqGrid('getGridParam','selrow');
	if(id){
		$.post("<?php echo site_url('pasajes/tbpasa/puestos/'); ?>/"+id+"/"+$('#fecven').val(), function(data){
			$("#puestos").html(data);
		});
	}
}

function reserva(indice){
	alert("Indice="+indice);
}

</script>
<?php } ?>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->org->label;     ?></td>
		<td class="littletablerowth"><?php echo $form->dtn->label;     ?></td>
		<td class="littletablerowth"><?php echo $form->fecven->label;  ?></td>
	</tr><tr>
		<td class="littletablerow"  ><?php echo $form->org->output;    ?></td>
		<td class="littletablerow"  ><?php echo $form->dtn->output;    ?></td>
		<td class="littletablerow"  ><?php echo $form->fecven->output; ?></td>
	</tr>
</table>
</fieldset>

<br>
<div class="tema1">
	<table id="tbrutas"></table>
</div>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->nacio->label;     ?></td>
		<td class="littletablerowth"><?php echo $form->codcli->label;     ?></td>
		<td class="littletablerowth"><?php echo $form->nomcli->label;  ?></td>
	</tr><tr>
		<td class="littletablerow"  ><?php echo $form->nacio->output;    ?></td>
		<td class="littletablerow"  ><?php echo $form->codcli->output;    ?></td>
		<td class="littletablerow"  ><?php echo $form->nomcli->output; ?></td>
	</tr>
</table>
</fieldset>


<div id='puestos' name='puestos' ></div>


<?php 
/*
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->codrut->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->codrut->output; ?></td>
	</tr>


<div align='center' style='border: 1px outset #EFEFEF;background: #EFEFFF '>
	<table style='width:100%;'>
		<tr>
			<td align='center'>
				<div id='grid1_container' style='overflow:auto;width:100%; height:210px; border: 1px outset #123;background: #FFFFFF; '>
					<table style='width:100%;' >
						<tr>
							<th colspan='4' class="littletableheaderdet">ANTICIPOS O NC</th>
						</tr>

						<tr id='__PNPL__'>
							<th class="littletableheaderdet">N&uacute;mero</th>
							<th class="littletableheaderdet">Fecha</th>
							<th class="littletableheaderdet">Saldo</th>
							<th class="littletableheaderdet">Aplicar</th>
						</tr>
					</table>
				</div>
			</td>
			<td align='center'>
				<div id='grid2_container' style='overflow:auto;width:100%;height:210px; border: 1px outset #123;background: #FFFFFF;'>
					<table style='width:100%;'>
					<tr>
						<th colspan='5' class="littletableheaderdet">EFECTOS</th>
					</tr>
					<tr id='__PNPL2__'>
						<th class="littletableheaderdet">N&uacute;mero</th>
						<th class="littletableheaderdet">Fecha</th>
						<th class="littletableheaderdet">Monto</th>
						<th class="littletableheaderdet">Saldo</th>
						<th class="littletableheaderdet">Abono</th>
					</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</div>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%' cellspacing='0' cellpadding='0'>
	<tr>
		<td class="littletablerowth">Observaciones:</td>
		<td class="littletablerow"  ><?php echo $form->observa1->output; ?></td>
	</tr>

</table>
</fieldset>
*/ ?>
<?php echo $form_end; ?>
