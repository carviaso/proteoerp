<?php

$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:
 
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_scripts;
echo $form_begin?>
<table align='center'>
	<tr>
		<td align=right>
			<?php echo $container_tr?>
		</td>
	</tr>
	<tr>
		<td>
          <table width="100%"  style="margin:0;width:100%;">
            <tr>
            <td colspan=13 class="littletableheader">Encabezado</td>
            </tr>
            <tr>
              <td width="100" class="littletablerowth"><?php echo $form->tipo->label ?></td>
              <td width="100" class="littletablerow"><?php echo $form->tipo->output ?></td>
              <td width="119" class="littletablerowth"><?php echo $form->cliente->label ?></td>
              <td width="137" class="littletablerow"><?php echo $form->cliente->output ?></td>
              <td width="50" class="littletablerowth"><?php echo $form->rifci->label ?></td>
              <td width="300" colspan="2" class="littletablerow"><?php echo $form->rifci->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?php echo $form->numero->label ?></td>
              <td class="littletablerow"><?php echo $form->numero->output ?></td>
              <td class="littletablerowth"><?php echo $form->nombre->label ?></td>
              <td colspan="5" class="littletablerow"><?php echo $form->nombre->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?php echo $form->fecha->label ?></td>
              <td class="littletablerow"><?php echo $form->fecha->output ?></td>
              <td class="littletablerowth"><?php echo $form->direc->label ?></td>
              <td colspan="3" class="littletablerow"><?php echo $form->direc->output ?></td>
              </tr>
            <tr>
              <td class="littletablerowth"><?php echo $form->vende->label ?></td>
              <td class="littletablerow"><?php echo $form->vende->output ?></td>
              <td class="littletablerowth"><?php echo $form->dire1->label ?></td>
              <td colspan="3" class="littletablerow"><?php echo $form->dire1->output ?></td>
            </tr>
        </table>
        <?php echo $form->detalle->output ?>
        <?php //echo $detalle ?>
        <table  width="100%" style="margin:0;width:100%;" > 
	    <tr>                                                           
	  	<td colspan=10 class="littletableheader">Totales</td>      
	    </tr>                                                          
	    <tr>                                                 
      	<td width="102" class="littletablerowth"><?php echo $form->orden->label ?> </td>
		  	<td width="97" class="littletablerow"><?php echo $form->orden->output ?> </td>
	    	<td width="66" class="littletablerowth"><?php echo $form->iva->label ?> </td>
		  	<td width="115" class="littletablerow" ><?php echo $form->iva->output ?> </td>
		  	<td width="93" class="littletablerowth" ><?php echo $form->subtotal->label ?> </td>
		  	<td width="280" class="littletablerow" ><?php echo $form->subtotal->output ?> </td>
       </tr>
       <tr>
        <td class="littletablerowth"><?php echo $form->formapago->label ?></td>
	 	 		<td class="littletablerow" ><?php echo $form->formapago->output ?></td>
        <td class="littletablerowth"><?php echo $form->inicial->label ?></td>
		 		<td class="littletablerow" ><?php echo $form->inicial->output ?></td>
		 		<td class="littletablerowth"><?php echo $form->total->label ?></td>
		 		<td class="littletablerow" ><?php echo $form->total->output ?></td>
       </tr>
</table>

<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
	  <td>
	<tr>
<table>
<?php endif; ?>
