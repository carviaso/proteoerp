<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

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
              <td width="100" class="littletablerowth"><?php echo $form->codigo->label ?></td>
              <td width="100" class="littletablerow"><?php echo $form->codigo->output ?></td>
              <td width="119" class="littletablerowth"><?php echo $form->precio->label ?></td>
              <td width="137" class="littletablerow"><?php echo $form->precio->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?php echo $form->descri1->label ?></td>
              <td class="littletablerow"><?php echo $form->descri1->output ?></td>
              <td class="littletablerowth"><?php echo $form->fecha->label ?></td>
              <td class="littletablerow"><?php echo $form->fecha->output ?></td>
            </tr>
        </table>
        <?php echo $form->detalle->output ?>
        <?php //echo $detalle ?>
        <table  width="100%" style="margin:0;width:100%;" > 
	    <tr>                                                           
	  	<td colspan=10 class="littletableheader">Totales</td>      
	    </tr>                                                          
	    <tr>                                                 
      	<td width="102" class="littletablerowth"><?php echo $form->rela->label ?> </td>
		  	<td width="97" class="littletablerow"><?php echo $form->rela->output ?> </td>
	    	<td width="66" class="littletablerowth"><?php echo $form->total->label ?> </td>
		  	<td width="115" class="littletablerow" ><?php echo $form->total->output ?> </td>
      </tr>
</table>

<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
	  <td>
	<tr>
<table>
<?php endif; ?>
