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
              <td width="84" class="littletablerowth"><?php echo $form->tipo->label ?></td>
              <td width="96" class="littletablerow"><?php echo $form->tipo->output ?></td>
              <td width="92" class="littletablerowth"><?php echo $form->cliente->label ?></td>
              <td width="120" class="littletablerow"><?php echo $form->cliente->output ?></td>
              <td width="58" class="littletablerowth"><?php echo $form->rifci->label ?></td>
              <td width="303" class="littletablerow"><?php echo $form->rifci->output ?></td>
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
              <td class="littletablerowth"><?php echo $form->orden->label ?></td>
              <td class="littletablerow"><?php echo $form->orden->output ?></td>
              <td class="littletablerowth"><?php echo $form->dire1->label ?></td>
              <td colspan="3" class="littletablerow"><?php echo $form->dire1->output ?></td>
            </tr>
	     </table>
          <?php echo $form->detalle->output ?>
          <?php //echo $detalle ?>
         <table  width="100%" style="margin:0;width:100%;" > 
	    <tr>                                                           
	  	  <td colspan=8 class="littletableheader"><div align=""> Totales</div></td>      
	    </tr>                                                          
	    <tr>                                                 
          <td width="191" class="littletablerowth"><?php echo $form->vence->label ?></td>
		  <td width="352" class="littletablerowth"><?php echo $form->observaciones->label ?>  </td>
		  <td width="140" class="littletablerowth" ><?php echo $form->subtotal->label ?> </td>
		 <td width="286" class="littletablerow" ><?php echo $form->subtotal->output ?> </td>
       </tr>
       <tr>
  		 <td class="littletablerow" ><?php echo $form->vence->output ?></td>
		 <td class="littletablerow" ><?php echo $form->observaciones->output ?></td>
		 <td class="littletablerowth"><?php echo $form->iva->label ?></td>
		 <td class="littletablerow" ><?php echo $form->iva->output ?></td>
       </tr>
       <tr>
         <td class="littletablerow" >&nbsp;</td>
		 <td class="littletablerow" ><?php echo $form->observaciones1->output ?></td>
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
