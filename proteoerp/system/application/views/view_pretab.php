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
		<td class="littletablerow"  ><?php echo $form->fecha->value; ?></td>
		<td class="littletablerowth" align='center'><?php echo $form->frec->label." ".$frec;  ?></td>
		<td class="littletablerowth"><?php echo $form->total->label;  ?></td>
		<td class="littletablerow"  ><?php echo $form->total->value; ?></td>
	</tr>
</table>
</fieldset>
<br>
<fieldset  style='border: 1px outset #FEB404;background: #FFFCE8;'>
<table width='100%'>

<?php
		$query = $this->db->query("DESCRIBE pretab");
		$i = 0;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if ( substr($row->Field,0,1) == 'c' && $row->Field != 'codigo' && substr($row->Field,1,1) != '9' ) {
					$reg     = $this->datasis->damereg('SELECT descrip, formula FROM conc WHERE concepto="'.substr($row->Field,1,4).'"');
					$nombre  = $reg['descrip'];
					$formula = $reg['formula'];

					if ( strpos($formula, 'MONTO') ) {
						$obj = $row->Field;
						
						if ( $i == 0 ) 
							echo '		<tr>';
							
						echo '			<td class="littletablerowth">'.$form->$obj->label.'</td>';
						echo '			<td class="littletablerow"  >'.$form->$obj->output.'</td>';
						if ( $i == 1 ) {
							echo '		</tr>';
							$i = 0;
						} else
							$i = 1;

					}
				}
			}
		}
?>		
		
</table>
</fieldset>
<?php echo $form_end; ?>
