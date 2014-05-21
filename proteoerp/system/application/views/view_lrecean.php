<?php
echo $form_scripts;
echo $form_begin;

$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$mod=true;

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';
?>
<script type="text/javascript">
$(function(){
	descuagua()
});

function descuagua(){
	var h2o  = Number($("#h2o").val());
	var neto = Number($("#litros").val());
	if ( h2o == '' ){
		h2o = 0;
	}
	$('#dtoagua').val(roundNumber(neto*h2o/100,2));
	$('#dtoagua_val').text(roundNumber(neto*h2o/100,2));
}
</script>
<span style='text-align:center;font-size:14pt;'>
    <?php echo img(array('src' =>"images/lab.png",  'height' => 18, 'alt' => 'Laboratorio',    'title' => 'Laboratorio', 'border'=>'0'));	?>
	Analisis de Laboratorio
</span>
<br />
<div style='border: 1px solid #9AC8DA;background: #FAFAFA'>
<table width='100%' style='font-size:11pt;background:#FAFAFA;'>
	<tr>
		<td class="littletableheaderc"><?php echo $form->observa->label;    ?></td>
		<td class="littletablerow"    ><?php echo $form->observa->output;   ?></td>
	</tr>
</table>
</div>

<div style='border: 1px solid #9AC8DA;background: #FAFAFA'>
<table width='100%' align='center'>
	<tr>
		<td class="littletableheaderc">Litros</td>
		<td class="littletablerow"><?php echo $form->litros->output;  ?></td>
		<td class="littletableheaderc"><?php echo $form->temp->label;    ?></td>
		<td class="littletablerow"    ><?php echo $form->temp->output;   ?></td>
	</tr>
	<tr>
		<td class="littletableheaderc"><?php echo $form->animal->label;  ?></td>
		<td class="littletablerow"    ><?php echo $form->animal->output; ?></td>
		<td class="littletableheaderc"><?php echo $form->acidez->label;  ?></td>
		<td class="littletablerow"    ><?php echo $form->acidez->output; ?></td>
	</tr><tr>
		<td class="littletableheaderc"><?php echo $form->h2o->label;     ?></td>
		<td class="littletablerow"    ><?php echo $form->h2o->output;    ?></td>
		<td class="littletableheaderc"><?php echo $form->crios->label;   ?></td>
		<td class="littletablerow"    ><?php echo $form->crios->output;  ?></td>
	</tr><tr>
		<td class="littletableheaderc"><?php echo $form->brix->label;    ?></td>
		<td class="littletablerow"    ><?php echo $form->brix->output;   ?></td>
		<td class="littletableheaderc"><?php echo $form->grasa->label;   ?></td>
		<td class="littletablerow"    ><?php echo $form->grasa->output;  ?></td>
	</tr><tr>
		<td class="littletableheaderc"><?php echo $form->cloruros->label;?></td>
		<td class="littletablerow"    ><?php echo $form->cloruros->output;?></td>
		<td class="littletableheaderc"><?php echo $form->alcohol->label;?></td>
		<td class="littletablerow"    ><?php echo $form->alcohol->output;?></td>
	</tr><tr>
		<td class="littletableheaderc"><?php echo $form->dtoagua->label; ?></td>
		<td class="littletablerow"    ><?php echo $form->dtoagua->output;?></td>
		<td class="littletableheaderc"><?php echo $form->ph->label; ?></td>
		<td class="littletablerow"    ><?php echo $form->ph->output;?></td>
	</tr>
	
</table>
</div>

<?php 
echo $form->id_lrece->label; 
echo $form->id_lrece->output;
?>


<?php echo $container_bl.$container_br; ?>
<?php echo $form_end; ?>
