<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos=$form->template_details('itedcont');
$scampos  ='<tr id="tr_itedcont_<#i#>">';
$scampos .='<td class="littletablerow" align="center"><b id=\'giro_num_<#i#>\'></b></td>';
$scampos .='<td class="littletablerow" align="left"  >'.$campos['it_especial']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left"  >'.$campos['it_vencimiento']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right" >'.$campos['it_monto']['field'].'</td>';
$scampos .='<td class="littletablerow" align="center"><a href=# onclick="del_itedcont(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
if($form->_status!='show'){ ?>

<script language="javascript" type="text/javascript">
var itedcont_cont=<?php echo $form->max_rel_count['itedcont'];?>;

$(function(){
	$("#edificacion").change(function(){ edif_change(''); });
	$("#inmueble").change(function(){ inmu_change(this.value); });
	$("#mt2,#precioxmt2").keyup(function(){ totalizar(); cmontos(); });
	$('#inicial').keyup(function(){ pagofinal(); });
	$('#financiable').keyup(function(){ pagofinal(); distrib(); });
	$('input[id^="it_monto"]').keyup(function(){ distrib(); });
	$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
	$('input[id^="it_vencimiento"]').datepicker({dateFormat:"dd/mm/yy"});

	$(".inputnum").numeric(".");
	totalizar();

	$('#cliente').autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscascli'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
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
			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);

			$('#cliente').val(ui.item.cliente);

			$('#rifci').val(ui.item.rifci);
			$('#rifci_val').text(ui.item.rifci);

			$('#sclitipo').val(ui.item.tipo);

			$('#direc').val(ui.item.direc);
			$('#direc_val').text(ui.item.direc);
		}
	});

	$('#numero_edres').autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscares'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
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
			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);

			$('#rifci').val(ui.item.rifci);
			$('#rifci_val').text(ui.item.rifci);

			$('#cliente').val(ui.item.cliente);
			$('#sclitipo').val(ui.item.tipo);

			$('#direc').val(ui.item.direc);
			$('#direc_val').text(ui.item.direc);

			$('#edificacion').val(ui.item.edifi);
			edif_change(ui.item.inmue);

			$('#uso').val(ui.item.uso);
			//$('#inmueble').val(ui.item.inmue);
		}
	});
	distrib();
	fechgiro();
});

function enumeragiro(){
	enu=0;
	arr=$('[id^="giro_num_"]');
	jQuery.each(arr, function() {
		enu+=1;
		$(this).text(enu);
	});
}

function pagofinal(){
	monto=Number($('#monto').val());
	inici=Number($('#inicial').val());
	finan=Number($('#financiable').val());

	firma=monto-inici-finan;
	$('#firma').val(roundNumber(firma,2));
	$('#firma_val').text(nformat(firma,2));
}

function inmu_change(val){
	$.ajax({
		type: 'POST',
		url: "<?php echo site_url('construccion/common/get_dinmue'); ?>",
		dataType: 'json',
		data: "inmueble="+val,
		success: function(data){
			$('#mt2').val(data.area);
			$('#precioxmt2').val(data.preciomt2);

			totalizar();
			cmontos();
		}
	});
}

function edif_change(par){
	$.post("<?php echo site_url('construccion/common/get_inmue'); ?>",{ edif:$("#edificacion").val() },
		function(data){
			$("#inmueble").html(data);
			if(par.length>0){
				$("#inmueble").val(par);
				inmu_change(par);
			}
		})
}

function truncate(){
	$('tr[id^="tr_edcont_"]').remove();
	itedcont_cont=0;
}

//Determina lo que falta por pagar
function faltante(){
	totalg=Number($("#totalg").val());
	paga  = apagar();
	resto = totalg-paga;
	return resto;
}

function totalizar(){
	mts    = Number($('#mt2').val());
	precio = Number($('#precioxmt2').val());
	monto  = roundNumber(mts*precio,2);
	//alert(monto);
	$('#monto').val(monto);
	$('#monto_val').text(nformat(monto,2));
}

//Calcula los montos iniciales,financiables y finales
function cmontos(){
	var monto=Number($('#monto').val());
	var inici=monto*0.2;
	var finan=monto*0.3;
	var firma=monto*0.5;

	$('#inicial').val(roundNumber(inici,2));
	$('#financiable').val(roundNumber(finan,2));
	$('#firma').val(roundNumber(firma,2));
	$('#firma_val').text(nformat(firma,2));
	distrib();
}

//Distribuye el monto financiable entre los giros
function distrib(){
	var arr  =$('input[id^="it_vencimiento_"]');
	var finan=Number($('#financiable').val());
	var giros=$('input[id^="it_monto"]');
	var desgiro=0;
	var cgiro=0

	jQuery.each(giros, function(){
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			especial= $('#it_especial_'+ind).val();

			if(especial=='S'){
				desgiro += Number($(this).val());
			}else{
				cgiro += 1;
			}
		}
	});
	var mmonto=roundNumber((finan-desgiro)/cgiro,2);
	//giros.val(roundNumber((finan-desgiro)/cgiro,2));

	jQuery.each(giros, function(){
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			especial= $('#it_especial_'+ind).val();
			if(especial=='N'){
				$(this).val(mmonto);
			}
		}
	});
}

//Para el calculo de las fechas de los giros
function fechgiro(){
	//inicio=arr.first().val();
	var arr  =$('input[id^="it_vencimiento_"]');
	inicio=$('#fecha').val(); //toma como fecha inicial la fecha del contrato
	year =Number(inicio.slice(-4));
	month=Number(inicio.slice(3,5))-1;
	day  =Number(inicio.slice(0,2));
	var i=1;
	f = new Date();
	jQuery.each(arr, function(){
		g=month+"/"+day+"/"+year;

		f.setFullYear(year,month+i,day);
		d=f.getDate();
		m=f.getMonth()+1;
		a=f.getFullYear();
		ffetch=pad(d.toString(),2,'0',1)+'/'+pad(m.toString(),2,'0',1)+'/'+a.toString();

		$(this).val(ffetch);
		i+=1;
	});
}

//Calcula el total de los giros
function totagiro(){
	var giros=$('input[id^="it_monto"]');
	var mgiros=0;

	jQuery.each(giros, function(){
		mgiros+=Number($(this).val());
	});
	$('#financiable').val(roundNumber(mgiros,2));
	pagofinal();

}

function add_itedcont(){
	var htm = <?php echo $campos; ?>;
	can = itedcont_cont.toString();
	con = (itedcont_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	itedcont_cont += 1;
	distrib();
	fechgiro();
	enumeragiro();
	$('#it_monto_'+can).keyup(function(){ distrib(); });
	$('#it_monto_'+can).numeric(".");
	$('input[id^="it_vencimiento"]').datepicker({dateFormat:"dd/mm/yy"});
	return can;
}

function del_itedcont(id){
	id=id.toString();
	$('#tr_itedcont_'+id).remove();
	distrib();
	var arr = $('input[id^="it_monto_"]');
	if(arr.length<=0){
		add_itedcont();
	}
	enumeragiro();
}
</script>
<?php } ?>
<table align='center' width="95%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
</table>

<table align='center' width="100%">
	<tr>
		<td>
			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
			<!--legend class="titulofieldset" style='color: #114411;'>Documento <?php echo $form->status->output; ?> <?php echo $form->numero->output; ?></legend-->
			<table style="margin: 0;">
			<tr>
				<td class="littletableheader"><?php echo $form->edificacion->label;   ?>*</td>
				<td class="littletablerow">   <?php echo $form->edificacion->output;  ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->fecha->label;  ?>*&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->fecha->output; ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->mt2->label;    ?>*</td>
				<td class="littletablerow">   <?php echo $form->mt2->output;   ?>&nbsp; </td>
				<td class="littletableheader"><?php echo $form->precioxmt2->label;   ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->precioxmt2->output;  ?>&nbsp;</td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->inmueble->label;  ?>*</td>
				<td class="littletablerow"   ><?php echo $form->inmueble->output; ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->uso->label;    ?>*</td>
				<td colspan='3' class="littletablerow">   <?php echo $form->uso->output;   ?>&nbsp; </td>
				<td class="littletableheader">         <?php echo $form->numero_edres->label;    ?>&nbsp;</td>
				<td class="littletablerow" ><?php echo $form->numero_edres->output;   ?>&nbsp; </td>
			</tr>
			</table>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		</td>
	</tr>
</table>

<table width='100%'>
	<tr>
		<td width='360px'>
		<div style='overflow:auto;border: 1px outset #9AC8DA;background: #FAFAFA;height:300px;width:360px;'>
		<table style="width:100%;border-collapse:collapse;padding:0px;">
			<tr id='__INPL__'>
				<td class="littletableheaderdet" align='center'><b>Nro.</b></td>
				<td class="littletableheaderdet" align='center'><b>Giro</b></td>
				<td class="littletableheaderdet" align='center'><b>Vence</b></td>
				<td class="littletableheaderdet" align='center'><b>Monto</b></td>
				<?php if($form->_status!='show') {?>
					<td bgcolor='#7098D0'>&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itedcont'];$i++) {
				$it_vencimiento  = "it_vencimiento_$i";
				$it_especial     = "it_especial_$i";
				$it_monton       = "it_monto_$i";
				if($form->_status=='show')
					$it_id = $form->_dataobject->get_rel('itedcont','id',$i);
			?>

			<tr id='tr_itedcont_<?php echo $i; ?>'>
				<td class="littletablerow" align="center"><b id='giro_num_<?php echo $i; ?>'><?php $o=$i+1;?> <?php echo $i+1; if($form->_status=='show'){ echo anchor("construccion/edcont/letracambio/$it_id/$o/letra.xml", 'Letra'); } ?></b></td>
				<td class="littletablerow" align="left"  ><?php echo $form->$it_especial->output; ?></td>
				<td class="littletablerow" align="left"  ><?php echo $form->$it_vencimiento->output; ?></td>
				<td class="littletablerow" align="right" ><?php echo $form->$it_monton->output;   ?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow" align='center'>
					<a href='#' onclick='del_itedcont(<?php echo $i ?>);return false;'><?php echo img("images/delete.jpg"); ?></a>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>
			<tr id='__UTPL__'>
				<td></td>
			</tr>
		</table>
		</div>
		<?php echo $container_bl ?>
		<?php echo $container_br ?>

		</td>
		<td valign='top'>

			<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
			<table style="margin: 0;">
			<tr>
				<td class="littletableheader"><?php echo $form->cliente->label;  ?>*&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->cliente->output.$form->sclitipo->output; ?>&nbsp;</td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->nombre->label;  ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->nombre->output;  ?>&nbsp;</td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->rifci->label; ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->rifci->output;   ?>&nbsp;</td>
			</tr><tr>
				<td class="littletableheader"><?php echo $form->direc->label  ?>&nbsp;</td>
				<td class="littletablerow"   ><?php echo $form->direc->output ?>&nbsp;</td>
			</tr>
			</table>
			</fieldset>


		<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
		<table width='100%'>
			<tr>
				<td class="littletableheader">           <?php echo $form->inicial->label;  ?></td>
				<td class="littletablerow" align='right'><?php echo $form->inicial->output; ?></td>
			</tr>
			<tr>
				<td class="littletableheader">           <?php echo $form->financiable->label;  ?></td>
				<td class="littletablerow" align='right'><?php echo $form->financiable->output; ?></td>
			</tr>
			<tr>
				<td class="littletableheader">           <?php echo $form->firma->label;  ?></td>
				<td class="littletablerow" align='right'><?php echo $form->firma->output; ?></td>
			</tr>
			<tr>
				<td class="littletableheader">           <?php echo $form->monto->label;  ?></td>
				<td class="littletablerow" align='right'><?php echo $form->monto->output; ?></td>
			<tr>
		</table>
		</fieldset>
		</td>
	</tr>
</table>
<?php echo $form_end; ?>
<?php endif; ?>
