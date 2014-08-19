<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
if($form->_status <> 'show'){ ?>

<script language="javascript" type="text/javascript">
$('#propietario').autocomplete({
	delay: 600,
	autoFocus: true,
	source: function(req, add){
		$.ajax({
			url:  "<?php echo site_url('ajax/buscascli'); ?>",
			type: "POST",
			dataType: "json",
			data: {"q":req.term},
			success:
				function(data){
					var sugiere = [];
					if(data.length==0){
						$('#nompro').html('');
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
	minLength: 2,
	select: function( event, ui ) {
		var meco;
		$('#propietario').attr("readonly", "readonly");
		$('#nompro').html(ui.item.nombre);
		$('#propietario').val(ui.item.cod_cli);
		setTimeout(function() {  $("#propietario").removeAttr("readonly"); }, 1500);
	}
});

$('#ocupante').autocomplete({
	delay: 600,
	autoFocus: true,
	source: function(req, add){
		$.ajax({
			url:  "<?php echo site_url('ajax/buscascli'); ?>",
			type: "POST",
			dataType: "json",
			data: {"q":req.term},
			success:
				function(data){
					var sugiere = [];
					if(data.length==0){
						$('#nomocu').html('');
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
	minLength: 2,
	select: function( event, ui ) {
		var meco;
		$('#ocupante').attr("readonly", "readonly");
		$('#nomocu').html(ui.item.nombre);
		$('#ocupante').val(ui.item.cod_cli);
		setTimeout(function() {  $("#ocupante").removeAttr("readonly"); }, 1500);
	}
});


</script>
<?php } ?>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->codigo->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->codigo->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->descripcion->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->descripcion->output; ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->edificacion->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->edificacion->output; ?></td>
		<td class="littletablerowth"><?php echo $form->status->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->status->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->ubicacion->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->ubicacion->output; ?></td>
		<td class="littletablerowth"><?php echo $form->uso->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->uso->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->objeto->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->objeto->output; ?></td>
		<td class="littletablerowth"><?php echo $form->usoalter->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->usoalter->output; ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->caracteristicas->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->caracteristicas->output; ?></td>
	</tr>
</table>
</fieldset>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->area->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->area->output; ?></td>
		<td class="littletablerowth"><?php echo $form->preciomt2c->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->preciomt2c->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->deposito->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->deposito->output; ?></td>
		<td class="littletablerowth"><?php echo $form->preciomt2e->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->preciomt2e->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->estaciona->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->estaciona->output; ?></td>
		<td class="littletablerowth"><?php echo $form->preciomt2a->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->preciomt2a->output; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->alicuota->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->alicuota->output; ?></td>
	</tr>
</table>
</fieldset>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth" width='100'><?php echo $form->propietario->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->propietario->output; ?></td>
		<td class="littletablerow"  ><div id='nompro'>&nbsp;</div></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->ocupante->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->ocupante->output; ?></td>
		<td class="littletablerow"  ><div id='nomocu'>&nbsp;</div></td>
	</tr>
</table>
</fieldset>

<?php echo $form_end; ?>
