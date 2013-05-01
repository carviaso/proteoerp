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
              <td width="100" class="littletablerowth"><?php echo $form->huesped->label ?></td>
              <td colspan="3" class="littletablerow"><?php echo $form->huesped->output ?></td>
              <td class="littletablerow"><span class="littletablerowth">
                <?php echo $form->folio->label ?>
              </span></td>
              <td colspan="3" class="littletablerow"><?php echo $form->folio->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?php echo $form->fecha_in->label ?></td>
              <td width="100" class="littletablerow"><?php echo $form->fecha_in->output ?></td>
              <td width="119" class="littletablerowth"><?php echo $form->cuenta->label ?></td>
              <td class="littletablerow"><?php echo $form->cuenta->output ?></td>
              <td class="littletablerow"><span class="littletablerowth">
                <?php echo $form->otro->label ?>
              </span></td>
              <td colspan="3" class="littletablerow"><?php echo $form->otro->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?php echo $form->fecha_ou->label ?></td>
              <td class="littletablerow"><?php echo $form->fecha_ou->output ?></td>
              <td class="littletablerowth"><?php echo $form->habit->label ?></td>
              <td class="littletablerow"><?php echo $form->habit->output ?></td>
              <td class="littletablerow"><span class="littletablerowth">
                <?php echo $form->total->label ?>
              </span></td>
              <td width="78" class="littletablerow"><?php echo $form->total->output ?></td>
            </tr>
        </table>  
        
        <?php echo $form->detalle->output ?>
        <?php //echo $detalle ?>
<table  width="100%" style="margin:0;width:100%;" > 
	 <tr>                                                           
	  	<td colspan=13 class="littletableheader">Totales</td>      
	 </tr>                                                          
	 <tr>                                                 
	    <td width="59" class="littletablerowth"><?php echo $form->saldo->label ?>       &nbsp;</td>
	   	<td width="18" class="littletablerow"  ><?php echo $form->saldo->output ?>      &nbsp;</td>	
    </tr>
	  <td>
	<tr>
<table>
<?php echo $container_bl ?>
<?php echo $container_br ?>
<?php endif; ?>