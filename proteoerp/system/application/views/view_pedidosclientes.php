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
              <td colspan=14 class="littletableheader">Encabezado</td>
            </tr>
            <tr>
              <td width="141" class="littletablerowth"><?php echo $form->presupuesto->label ?></td>
              <td width="179" class="littletablerow"><?php echo $form->presupuesto->output ?></td>
              <td width="131" class="littletablerowth"><?php echo $form->cliente->label ?></td>
              <td width="226" class="littletablerow"><?php echo $form->cliente->output ?></td>
              <td width="106" class="littletablerowth"><?php echo $form->rifci->label ?></td>
              <td width="168" class="littletablerow"> <?php echo $form->rifci->output ?></td>
              <td width="6" colspan="3" class="littletablerow"><span class="littletablerowth"></span>
              <span class="littletablerowth"> </span></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?php echo $form->numero->label ?></td>
              <td class="littletablerow"><?php echo $form->numero->output ?></td>
              <td class="littletablerowth"><?php echo $form->nombre->label ?></td>
              <td colspan="6" class="littletablerow"><?php echo $form->nombre->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?php echo $form->fecha->label ?></td>
              <td class="littletablerow"><?php echo $form->fecha->output ?></td>
              <td class="littletablerowth"><?php echo $form->direc->label ?></td>
              <td colspan="4" class="littletablerow"><?php echo $form->direc->output ?></td>
            </tr>
            <tr>
              <td class="littletablerowth"><?php echo $form->vende->label ?></td>
              <td class="littletablerow"><?php echo $form->vende->output ?></td>
              <td class="littletablerow"><?php echo $form->dire1->label ?></td>
              <td colspan="3" class="littletablerow"><?php echo $form->dire1->output ?></td>
            </tr>
          </table>
          <?php echo $form->detalle->output ?>
          <?php //echo $detalle ?>
          <table  width="100%" style="margin:0;width:100%;" > 
	         <tr>                                                           
	  	      <td colspan=10 class="littletableheader"><div align=""> Totales</div></td>      
          </tr>                                                          
	        <tr>                                                 
             <td width="112" class="littletablerowth"><?php echo $form->anticipo->label ?></td>
             <td width="114" class="littletablerow"><?php echo $form->anticipo->output ?></td>
             <td width="118" class="littletablerowth"><?php echo $form->iva->label ?></td>
             <td width="110" class="littletablerow"><?php echo $form->iva->output ?></td>
             <td width="102" class="littletablerowth"><?php echo $form->subtotal->label ?></td>
             <td width="197" class="littletablerow"><?php echo $form->subtotal->output ?></td>
         </tr>
         <tr>
             <td width="112" class="littletablerowth"><?php echo $form->referencia->label ?></td>
             <td class="littletablerow"><?php echo $form->referencia->output ?></td>
             <td width="118" class="littletablerowth"><?php echo $form->vence->label ?></td>
             <td class="littletablerow"><?php echo $form->vence->output ?></td>
             <td width="102" class="littletablerowth"><?php echo $form->total->label ?></td>
             <td class="littletablerow"><?php echo $form->total->output ?></td>
         </tr>
         </table>

<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
	  <td>
	<tr>
<table>
<?php endif; ?>
