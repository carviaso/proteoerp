<?php
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

if($form->_status <> 'show'){

$campos='\'\'';
//$campos=$form->template_details('itcruc');
//$scampos  ='<tr id="tr_itannc_<#i#>">';
//$scampos .='<td class="littletablerow" align="left" ><input type="hidden" name="itnumero_<#i#>"  id="itnumero_<#i#>" ><input type="hidden" name="ittipo_<#i#>"  id="ittipo_<#i#>" ><span id="itnumero_<#i#>_val"></span><input type="hidden" name="itid_<#i#>"  id="itid_<#i#>" ></td>';
//$scampos .='<td class="littletablerow" align="left" ><input type="hidden" name="itfecha_<#i#>"   id="itfecha_<#i#>"  ><span id="itfecha_<#i#>_val" ></span></td>';
//$scampos .='<td class="littletablerow" align="right"><input type="hidden" name="itsaldo_<#i#>"   id="itsaldo_<#i#>"  ><span id="itsaldo_<#i#>_val" ></span></td>';
//$scampos .='<td class="littletablerow" align="right"><input type="text" size="10" name="itmonto_<#i#>" id="itmonto_<#i#>" class="inputnum"></td>';
//$scampos .='</tr>';
//$campos=$form->js_escape($scampos);

?>
<script language="javascript" type="text/javascript">

$(document).ready(function() {
	$(".inputnum").numeric(".");
	$('#org').change(function() { traerutas(); });
	$('#dtn').change(function() { traerutas(); });
});

function traerutas(){
	var desde = $('#org').val();
	var hasta = $('#dtn').val();

	if(desde!='' && hasta!=''){
		$.ajax({
			url: "<?php echo site_url($this->url.'getruta'); ?>",
			dataType: "json",
			type: "POST",
			data: {"q1" : desde , "q2": hasta},

			success: function(data){
					$("#codrut option").remove();
					var cana = 0;
					$.each(data,
						function(id, val){
							option = $('<option />');
							option.attr('value', this.value).text(this.label);
							$('#codrut').append(option)
						}
					);
				},
		});
	}
}


</script>
<?php } ?>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->fecven->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->fecven->output; ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->org->label;     ?></td>
		<td class="littletablerow"  ><?php echo $form->org->output;    ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->dtn->label;     ?></td>
		<td class="littletablerow"  ><?php echo $form->dtn->output;    ?></td>
	</tr><tr>
		<td class="littletablerowth"><?php echo $form->codrut->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->codrut->output; ?></td>
	</tr>
</table>
</fieldset>

<?php /*
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
