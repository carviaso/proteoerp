<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';
echo $form_scripts;
echo $form_begin;

$atts = array(
  'width'      => '800',
  'height'     => '600',
  'scrollbars' => 'yes',
  'status'     => 'yes',
  'resizable'  => 'yes',
  'screenx'    => '0',
  'screeny'    => '0'
);

?>
<style>
	.bien:link,.bien:visited,.bien:hover,.bien:active {color:#1E890A;}
	.regu:link,.regu:visited,.regu:hover,.regu:active {color:#DFBD00;}
	.malo:link,.malo:visited,.malo:hover,.malo:active {color:#CF0000;}
</style>
<table align='center'>
	<tr>
		<td align=right>
			<?php echo $container_tr?>
		</td>
	</tr><tr>
		<td>
			<table width="100%"  style="margin:0;width:100%;">
				<tr>
					<td colspan=6 class="littletableheader">Datos de la compra</td>
				</tr><tr>
					<td width="100" class="littletablerowth"><?php echo $form->fecha->label     ?></td>
					<td width="100" class="littletablerow">  <?php echo $form->fecha->output    ?></td>
					<td width="100" class="littletablerowth"><?php echo $form->pcontrol->label  ?></td>
					<td width="100" class="littletablerow">  <?php
						if(empty($form->pcontrol->value)>0){
							echo 'No cargados';
						}else{
							$id_scst=$this->datasis->dameval('SELECT id FROM scst WHERE control='.$this->db->escape($form->pcontrol->value));

							if(empty($id_scst)){
								echo 'Eliminada';
							}else{
								echo anchor_popup('compras/scst/dataedit/show/'.$id_scst,$form->pcontrol->value, $atts);
							}
						}
						?></td>
					<td width="119" class="littletablerowth"><?php echo $form->proveedor->label  ?></td>
					<td             class="littletablerow">  <?php echo $form->proveedor->output?></td>
				</tr><tr>
					<td class="littletablerowth"><?php echo $form->numero->label  ?></td>
					<td class="littletablerow">  <?php echo $form->numero->output ?></td>
					<td class="littletablerowth"><?php echo $form->tipo->label    ?></td>
					<td class="littletablerow">  <?php echo $form->tipo->output   ?></td>
					<td class="littletablerowth"><?php echo $form->nombre->label  ?></td>
					<td class="littletablerow">  <?php echo $form->nombre->output ?></td>
				</tr>
			</table>
			<?php echo $form->detalle->output ?>
			<table  width="100%" style="margin:0;width:100%;">
				<tr>
					<td colspan=3 class="littletableheader">Totales</td>
				</tr><tr>
					<td width="500" class="littletablerow">Cantidad de reglones: <b><?php echo $carti; ?></b></td>
					<td width="111" class="littletablerowth"><?php echo $form->subt->label ?> </td>
					<td width="139" class="littletablerow" align='right'><?php echo $form->subt->output ?> </td>
				</tr><tr>
					<td class="littletablerow">Cantidad de unidades: <b><?php echo $form->unidades->output; ?></td>
					<td class="littletablerowth"><?php echo $form->iva->label ?></td>
					<td class="littletablerow" align='right'><?php echo $form->iva->output ?></td>
				</tr><tr>
					<td class="littletablerowth">&nbsp;</td>
					<td class="littletablerowth"><?php echo $form->total->label ?></td>
					<td class="littletablerow" align='right'><?php echo $form->total->output ?></td>
				</tr>
			</table>
			<?php echo $form_end?>
			<?php echo $container_bl ?>
			<?php echo $container_br ?>
		<td>
	<tr>
</table>

<?php endif; ?>
