<?php
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
//echo $form->_status;

if ($form->_status=='delete' OR $form->_action=='delete'):
echo $form->output;
else:
$link=site_url('inventario/presupuesto/get_tipo');

foreach($form->detail_fields['itsinvlist'] AS $ind=>$data)
$campos[]=$data['field'];
$campos='<tr id="tr_itsinvlist_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itsinvlist(<#i#>);return false;">Eliminar</a></td></tr>';
$campos = $form->js_escape($campos);
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){
	//	//$uri  =$this->uri->uri_string();
//		$uri  =$this->datasis->get_uri();
//		$idt=$this->datasis->dameval("SELECT id FROM modbus WHERE idm='sinv' AND uri='$uri'");
//		$modblink=site_url('/buscar/index/'.$idt.'/<#i#>');

	?>


<script language="javascript" type="text/javascript">
itsinvlist_cont=<?php echo $form->max_rel_count['itsinvlist'] ?>;

					
function add_itsinvlist(){
	var htm = <?php echo $campos ?>;
	can = itsinvlist_cont.toString();
	con = (itsinvlist_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	itsinvlist_cont=itsinvlist_cont+1;
}
					
function del_itsinvlist(id){
	id = id.toString();
	$('#tr_itsinvlist_'+id).remove();
}
</script>
	<?php } ?>

<table align='center' width="80%">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
		<table width="100%" style="margin: 0; width: 100%;">
			<tr>
				<td colspan=6 class="bigtableheader">Listado <?php  if ($form->_status=="create")echo "Nuevo ";
				else echo "Numero:".str_pad(trim($form->numero->output),8,0,STR_PAD_LEFT) ?>
				</td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->nombre->label   ?>&nbsp;</td>
				<td class="littletablerow"><?php echo $form->nombre->output  ?>&nbsp;</td>
				<td class="littletablerowth">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->fecha->label   ?>&nbsp;</td>
				<td class="littletablerow"><?php echo $form->fecha->output  ?>&nbsp;</td>
				<td class="littletablerowth">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->concepto->label   ?>&nbsp;</td>
				<td class="littletablerow"><?php echo $form->concepto->output  ?>&nbsp;</td>
				<td class="littletablerowth">&nbsp;</td>
				<td class="littletablerow">&nbsp;</td>
			</tr>
			<tr>
				<td class="littletablerowth"><?php echo $form->usu->label   ?>*&nbsp;</td>
				<td class="littletablerow"><?php echo $form->usu->output  ?>&nbsp;</td>
			</tr>

		</table>
		<br />

		<table width='100%'>
			<tr>
				<td class="littletableheaderb">Codigo</td>
				<?php if($form->_status!='show') {?>
				<td class="littletableheaderb">&nbsp;</td>
				<?php } ?>
			</tr>
			<?php for($i=0;$i<$form->max_rel_count['itsinvlist'];$i++) {
				$obj0="itcodigo_$i";
				$obj1="itdescrip_$i"
				?>
			<tr id='tr_itsinvlist_<?php echo $i ?>'>
				<td class="littletablerow"><?php echo $form->$obj0->output ?></td>
				<td class="littletablerow"><?php echo $form->$obj1->output ?></td>
				<?php if($form->_status!='show') {?>
				<td class="littletablerow"><a href=#
					onclick='del_itsinvlist(<?php echo $i ?>);return false;'>Eliminar</a></td>
					<?php } ?>
			</tr>
			<?php } ?>

			<tr id='__UTPL__'>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<?php if($form->_status!='show') {?>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<?php } ?>
			</tr>
		</table>

		<?php echo $form_end     ?> <?php echo $container_bl ?> <?php echo $container_br ?>
		</td>
	</tr>
</table>

		<?php endif; ?>