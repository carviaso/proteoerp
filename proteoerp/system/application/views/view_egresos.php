<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' OR $form->_action=='delete'):
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
              <td colspan=11 class="littletableheader">Encabezado</td>
            </tr>
            <tr>
              <td width="92" class="littletablerowth"><?php echo $form->orden->label ?></td>
              <td width="89" class="littletablerowth"><?php echo $form->fecha->label ?></td>
              <td width="95" class="littletablerow"><?php echo $form->fecha->output ?></td>
              <td width="111" class="littletablerowth"><?php echo $form->codigo->label ?></td>
              <td width="95" class="littletablerow"><?php echo $form->codigo->output ?></td>
			  <td width="92" class="littletablerow"><span class="littletablerowth">
			    <?php echo $form->vencimiento->label ?>			  </td>
			  <td width="159" class="littletablerow"><?php echo $form->vencimiento->output ?></td>
            </tr>
            <tr>
              <td class="littletablerow"><?php echo $form->orden->output ?></td>
              <td class="littletablerowth"><?php echo $form->numero->label ?></td>
              <td class="littletablerow"><?php echo $form->numero->output ?></td>
              <td class="littletablerowth"><?php echo $form->nombre->label ?></td>
              <td colspan="3" class="littletablerow"><?php echo $form->nombre->output ?></td>
            </tr>
          </table>
          <?php echo $form->detalle->output ?>
          <?php //echo $detalle ?>

<table  width="100%" style="margin:0;width:100%;" > 
	 <tr>                                                           
	  	<td colspan=13 class="littletableheader">Totales</td>      
	 </tr>                                                          
	 <tr>                                                 
	    <td width="59" class="littletablerowth"><?php echo $form->banco->label ?>       &nbsp;</td>
	   	<td width="18" class="littletablerow"  ><?php echo $form->banco->output ?>      &nbsp;</td>	
      <td width="9" class="littletablerow"   ><?php echo $form->tipo1->label ?>       &nbsp;</td>
      <td width="10" class="littletablerow"  >&nbsp;<?php echo $form->tipo1->output ?>&nbsp;</td>
      <!--
      <td width="17" class="littletablerowth"></td>
	   	<td width="17" class="littletablerowth"></td>
	   	<td width="194" class="littletablerow" ></td>-->
			<td width="197" class="littletablerowth"><?php echo $form->rislr->label ?>  </td>
	   	<td width="206" class="littletablerow" ><?php echo $form->rislr->output ?></td>
    </tr>
    <tr>
        <td class="littletablerowth"><?php echo $form->numero1->label ?></td>
		<td colspan="6" class="littletablerow" ><?php echo $form->numero1->output ?></td>
        <td class="littletablerowth"><?php echo $form->riva->label ?></td>
		<td class="littletablerow" ><?php echo $form->riva->output ?></td>
      </tr>
      <tr>
        <td class="littletablerowth"><?php echo $form->comprob->label ?></td>
		<td colspan="6" class="littletablerow" ><?php echo $form->comprob->output ?></td>
        <td class="littletablerowth"><?php echo $form->anticipo->label ?></td>
		<td class="littletablerow" ><?php echo $form->anticipo->output ?></td>
      </tr>
      <tr>
        <td class="littletablerowth"><?php echo $form->contado->label ?></td>
       	<td colspan="6" class="littletablerow" ><?php echo $form->contado->output ?></td>
        <td class="littletablerowth"><?php echo $form->totalneto->label ?></td>
        <td class="littletablerow" ><?php echo $form->totalneto->output ?></td>
      </tr>
      <tr>
        <td class="littletablerowth"><?php echo $form->credito->label ?></td>
        <td colspan="3" class="littletablerow" ><?php echo $form->credito->output ?></td>
	    <td class="littletablerow" ><span class="littletablerowth">
	      <?php echo $form->tipo->label ?>
	    </td>
	    <td class="littletablerow" ><?php echo $form->tipo->output ?></td>
	    <td colspan="6" class="littletablerow" >&nbsp;</td>
      </tr>
      <tr>
        <td class="littletablerowth"><?php echo $form->beneficiario->label ?></td>
        <td colspan="6" class="littletablerow" ><?php echo $form->beneficiario->output ?></td>
        <td class="littletablerowth"><?php echo $form->monto->label ?></td>
        <td colspan="2" class="littletablerow" ><?php echo $form->monto->output ?></td>
      </tr>
</table>

<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
	  <td>
	<tr>
<table>
<?php endif; ?>
