<?php
echo $form_scripts;
echo $form_begin;

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';
if($form->_status <> 'show'){ ?>

<script language="javascript" type="text/javascript">
</script>
<?php } ?>

<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>
	<tr>
		<td class="littletablerowth"><?php echo $form->tipbus->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->tipbus->output; ?></td>
		<!-- td class="littletablerowth"><?php echo $form->desbus->label;  ?></td -->
		<td class="littletablerow"  ><?php echo $form->desbus->output; ?></td>

		<td class="littletablerowth"><?php echo $form->pisos->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->pisos->output; ?></td>

		<td class="littletablerowth"><?php echo $form->puestos->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->puestos->output; ?></td>


	</tr>
</table>
</fieldset>
<br>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<label>Planta Baja</label>
<table width='100%' cellpadding='0' cellspacing='0'>
	<tr>
<?php
		for( $i=11; $i>=0; $i-- ){
			$objeto="asiento$i";
			echo '<td class="littletablerow"  >'.$form->$objeto->output.'</td>';
		}
?>
	</tr>
	<tr>
<?php
		for( $i=23; $i>=12; $i-- ){
			$objeto="asiento$i";
			echo '<td class="littletablerow"  >'.$form->$objeto->output.'</td>';
		}
?>
	</tr>

	<tr>
		<td colspan='12'>&nbsp;</td>
	</tr>

	<tr>
<?php
		for( $i=35; $i>=24; $i-- ){
			$objeto="asiento$i";
			echo '<td class="littletablerow"  >'.$form->$objeto->output.'</td>';
		}
?>
	</tr>
	<tr>
<?php
		for( $i=47; $i>=36; $i-- ){
			$objeto="asiento$i";
			echo '<td class="littletablerow"  >'.$form->$objeto->output.'</td>';
		}
?>
	</tr>
</table>
</fieldset>
<br>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<label>Planta Alta</label>
<table width='100%' cellpadding='0' cellspacing='0'>
	<tr>
<?php
		for( $i=111; $i>=100; $i-- ){
			$objeto="asiento$i";
			echo '<td class="littletablerow"  >'.$form->$objeto->output.'</td>';
		}
?>
	</tr>
	<tr>
<?php
		for( $i=123; $i>=112; $i-- ){
			$objeto="asiento$i";
			echo '<td class="littletablerow"  >'.$form->$objeto->output.'</td>';
		}
?>
	</tr>

	<tr>
		<td colspan='12'>&nbsp;</td>
	</tr>

	<tr>
<?php
		for( $i=135; $i>=124; $i-- ){
			$objeto="asiento$i";
			echo '<td class="littletablerow"  >'.$form->$objeto->output.'</td>';
		}
?>
	</tr>
	<tr>
<?php
		for( $i=147; $i>=136; $i-- ){
			$objeto="asiento$i";
			echo '<td class="littletablerow"  >'.$form->$objeto->output.'</td>';
		}
?>
	</tr>


</table>
</fieldset>




<?php echo $form_end; ?>
