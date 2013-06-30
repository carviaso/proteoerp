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
		jQuery("#tbrutas").jqGrid('setGridParam',{url: '<?php echo site_url('pasajes/tbreserva/getbrutas'); ?>/'+desde+'/'+hasta+'/'+fecha }).trigger('reloadGrid');
	}
}


$("#tbrutas").jqGrid({
	ajaxGridOptions: { type: "POST"},
	jsonReader: { root: "data", repeatitems: false},
	ondblClickRow: puestos,
	url:     '<?php echo site_url('pasajes/tbreserva/getbrutas').'/0/0/0/0/0';  ?>',
	editurl: '<?php echo site_url('pasajes/tbreserva/'); ?>',
	datatype: "json",
	rowNum:  12,
	height: 180, 
	rowList:[],
	toolbar: [false],
	width:  740,
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
		var ret  = $("#tbrutas").getRowData(id);
		$.post("<?php echo site_url('pasajes/tbreserva/puestos/'); ?>/"+id+"/"+$('#fecven').val(), function(data){
			$("#puestos").html(data);
			$('#puestos').buttonset();
			$("#codrut").val(ret.codrut);
		});
	}
}

//Cuenta los puestos seleccionados
function resepu( ppp, aaa ){ 
	var monto  = 0;
	var nropas = 0;
	var id = $("#tbrutas").jqGrid('getGridParam','selrow');
	var menores  = $("#menores").val();
	var ancianos = $("#ancianos").val();

	nropas = $("input:checked").length;

	if(id){
		var ret  = $("#tbrutas").getRowData(id);
		$("#nropas").html(nropas);
		$('#monto').html( moneyformat(ret.precio*nropas) );
		$('#resumen').show();
	} else {
		$("#nropas").html(0.00);
		$('#monto').html(0.00);
	}
}


function reserva(indice){
	alert("Indice="+indice);
}

function fmenores(mtipo){
	var mm  = +$("#menores").val();
	var aa  = +$("#ancianos").val();
	var id  = $("#tbrutas").jqGrid('getGridParam','selrow');
	var ret = $("#tbrutas").getRowData(id);
	var nropas = 0;
	var cc = mm+aa;

	nropas = $("input:checked").length;

	// Si tiene demasiados ninos
	if ( cc > nropas ){
		if ( aa == 0 ){ 
			mm = nropas;
			$("#menores" ).val(mm); 
		} else if ( mm == 0 ){ 
			aa = nropas;
			$("#ancianos" ).val(aa); 
		} else {
			$("#menores" ).val(0); 
			$("#ancianos" ).val(0); 
			alert('No pueden haber mas menores y ancianos que puestos');
		}
	}
	
	//No pueden viajar ninos solos
	if ( mm == nropas ){
		alert('Viajan solo Menores');
	}
	
	//if ( mtipo == 'M'){
		$('#monto').html( moneyformat(ret.precio*nropas) );
		$('#madulto').html(moneyformat(ret.precio*(nropas-mm-aa))); 
		$('#mmenor').html(moneyformat(ret.precio*mm));
		$('#manci').html(moneyformat(ret.precio*aa));
/*
	} else {
		$('#madulto').html(); 
		$('#mmenor').html();
		$('#manci').html();
	}*/
}
</script>
<?php } 
echo $form->codrut->output;
?>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->org->label;     ?></td>
		<td class="littletablerow"  ><?php echo $form->org->output;    ?></td>

		<td class="littletablerowth"><?php echo $form->dtn->label;     ?></td>
		<td class="littletablerow"  ><?php echo $form->dtn->output;    ?></td>

		<td class="littletablerowth"><?php echo $form->fecven->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->fecven->output; ?></td>
	</tr>
</table>
</fieldset>
<br>
<div class="tema1">
	<table id="tbrutas"></table>
</div>
<br>
<div id='puestos' name='puestos' style='margin-left:0em;' ></div>
<br>
<table id='resumen' style='font-size:1.5em;display:none;'>
	<tr>
		<td class="littletablerowth">Pasajes:</td>
		<td><div id='nropas'  ></div></td>
		<td class="littletablerowth">Precio</td>
		<td class="littletablerow"><div id='monto'></div></td>
		<td class="littletablerowth"><?php echo $form->nomcli->label;     ?></td>
		<td class="littletablerow" ><?php echo $form->nomcli->output;    ?></td>
	</tr>

</table>

<?php 
echo $form_end; 
/*
<table id='resumen' style='font-size:1.5em;display:none;'>
	<tr>
		<td class="littletablerowth">Pasajes:    </td><td><div id='nropas'  ><> </div></td>

		<td class="littletablerowth"><?php echo $form->menores->label;     ?></td>
		<td class="littletablerow"  ><?php echo $form->menores->output;    ?></td>


		<td class="littletablerowth"><?php echo $form->ancianos->label;     ?></td>
		<td class="littletablerow"  ><?php echo $form->ancianos->output;    ?></td>


		<td class="littletablerowth">Total Precio</td><td class="littletablerow"><div id='monto'> </div></td>
	<tr></tr>
		<td class="littletablerowth">Adultos: </td><td><div id='madulto' ></div></td>
		<td class="littletablerowth">Menores: </td><td><div id='mmenor'  ></div></td>
		<td class="littletablerowth">Ancianos:</td><td><div id='manci'   ></div></td>
		<td class="littletablerowth"><?php echo $form->nomcli->label;     ?></td>
		<td class="littletablerow"  ><?php echo $form->nomcli->output;    ?></td>
	</tr>

</table>
*/
?>
