<?php
//echo $form_scripts;
echo $form_begin;
$frec = '';
if ( $form->frec->value == 'Q' )
	$frec = 'Quncenal';
elseif ( $form->frec->value == 'S' )
	$frec = 'Semanal';
elseif ( $form->frec->value == 'B' )
	$frec = 'Bi-Semanal';
elseif ( $form->frec->value == 'M' )
	$frec = 'Mensual';


if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
if($form->_status <> 'show'){ ?>


<?php } ?>
<script language="javascript" type="text/javascript">
$(function(){
	$(".inputnum").numeric(".");
	$('.inputnum').focus(function (){ $(this).select(); });
});
</script>
<?php echo $form->id->output; ?>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->nombre->label;  ?></td>
		<td colspan="2" class="littletablerow"  ><?php echo $form->nombre->value; ?></td>
		<td class="littletablerowth"><?php echo $form->codigo->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->codigo->value; ?></td>
	</tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->fecha->label;  ?></td>
		<td class="littletablerow"  ><?php echo dbdate_to_human($form->fecha->value); ?></td>
		<td class="littletablerowth" align='center'><?php echo $form->frec->label." ".$frec;  ?></td>
		<td class="littletablerowth"><?php echo $form->total->label;  ?></td>
		<td class="littletablerow"  ><?php echo nformat($form->total->value); ?></td>
	</tr>
</table>
</fieldset>
<br>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
<?php
	$i = false;
	foreach($arr_concs as $concepto){
		$obj = 'c'.$concepto;

		if(!$i){ echo '		<tr>'; }
		echo '			<td class="littletablerowth">'.$form->$obj->label.'</td>';
		echo '			<td class="littletablerow"  >'.$form->$obj->output.'</td>';
		if($i){  echo '		</tr>'; }
		$i= !$i;
	}
?>
</table>
</fieldset>
<?php echo $form_end; ?>
