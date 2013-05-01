<?php

$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);
$container_tr=join("&nbsp;", $form->_button_container["TR"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:
//$link=site_url('presupuesto/requisicion/getadmin');

foreach($form->detail_fields['itccont'] AS $ind=>$data)
$campos[]=$data['field'];
$campos='<tr id="tr_itccont_<#i#>"><td class="littletablerow">'.join('</td><td>',$campos).'</td>';
$campos=str_replace("\n",'',$campos);
$campos.=' <td class="littletablerow"><a href=# onclick="del_itccont(<#i#>);return false;">Eliminar</a></td></tr>';
$campos=$form->js_escape($campos);
if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){
	?>

<script language="javascript" type="text/javascript">
itccont_cont=<?php echo $form->max_rel_count['itccont'] ?>;

function add_itccont(){
	var htm = <?php echo $campos ?>;
	can = itccont_cont.toString();
	con = (itccont_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	itccont_cont=itccont_cont+1;
	
}
					
function del_itccont(id){
	id = id.toString();
	$('#tr_itccont_'+id).remove();
}
function cal_monto(i){
	id=i.toString();
	cantidad=parseFloat($("#cantidad_"+id).val()); 
	precio=parseFloat($("#precio_"+id).val());  
	monto=cantidad*precio;
	//alert(monto);
	if(!isNaN(monto))
	$("#monto_"+id).val(monto);
	cal_total();
}
$(function() {
	$(".inputnum").numeric(".");
	cal_total();
});
function cal_total(){
	tot=can=0;
	for(i=0;i<itccont_cont;i++){
		id=i.toString();
		valor=parseFloat($("#monto_"+id).val());
		if(!isNaN(valor))
			tot=tot+valor;		
		$("#base").val(tot);	
	}
	base=parseFloat($("#base").val()); 
	impuesto=base*12/100;
	//alert('hola');
	if(!isNaN(impuesto))
	$("#impuesto").val(impuesto);
	$("#tota").val(base+impuesto);
}
</script>
	<?php } ?>
<table align='center' style="margin:0;width:100%;">
	<tr>
		<td align=right><?php echo $container_tr?></td>
	</tr>
	<tr>
		<td>
<table style="margin:0;width:100%;">
	<tr>
		<td colspan=6 class="littletableheader">Encabezado</td>
	</tr>
	<tr>
		<td  class="littletablerowth" width="25%"><?php echo $form->obrap->label ?></td>
		<td  class="littletablerow" width="20%"><?php echo $form->obrap->output ?></td>
	  </tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->numero->label ?></td>
		<td class="littletablerow"><?php echo $form->numero->output ?></td>
	  </tr>
	<tr>
		<td class="littletablerowth"><?php echo $form->fecha->label ?></td>
		<td class="littletablerow"><?php echo $form->fecha->output ?></td>
	  </tr>
	<tr>
		<td  class="littletablerowth"><?php echo $form->tipo->label ?></td>
		<td  class="littletablerow"><?php echo $form->tipo->output ?></td>
	  </tr>
	<tr>
		<td colspan="6" class="littletableheader" align="left" >Datos del Contratista</td>
	  </tr>
	<tr>
	  <td class="littletablerowth"><?php echo $form->cod_prv->label ?></td>
	  <td class="littletablerow"><?php echo $form->cod_prv->output  ?></td>
	  <td class="littletablerowth"><?php echo $form->rif->label ?></td>
	  <td colspan="3" class="littletablerow"><?php echo $form->rif->output ?></td>
	  </tr>
	<tr>
	  <td class="littletablerowth"><?php echo $form->nombre->label ?></td>
	  <td class="littletablerow"><?php echo $form->nombre->output  ?></td>
	  <td class="littletablerowth"><?php echo $form->telefono->label ?></td>
	  <td class="littletablerow"><?php echo $form->telefono->output ?></td>
	  <td class="littletablerowth"><?php echo $form->email->label ?></td>
	  <td class="littletablerow"><?php echo $form->email->output ?></td>
	</tr>
	<tr>
	  <td colspan="6" class="littletablerowth"><?php echo $form->direccion->label  ?></td>
	 </tr>
	<tr>
	  <td colspan="6" class="littletablerow"><?php echo $form->direccion->output ?></td>
	  </tr>
	  <tr>
	  <td colspan="6" class="littletablerowth"><?php echo $form->detalles->label ?></td>
	  </tr>
	  <tr>
	  	  <td colspan="6" class="littletablerow"><?php echo $form->detalles->output ?></td>
  	  </tr>
	  	<tr>
		<td colspan="6" class="littletableheader" align="left" >Condiciones Comerciales </td>
	  </tr>
    	<tr>
	  	  <td class="littletablerowth"><?php echo $form->fecha_inicio->label ?></td>
  	      <td class="littletablerow"><?php echo $form->fecha_inicio->output ?></td>
        <td colspan="4" class="littletablerowth"><?php echo $form->retencion->label ?></td>
      	</tr>
    	<tr>
	  	  <td class="littletablerowth"><?php echo $form->fecha_final->label ?></td>
  	    <td class="littletablerow"><?php echo $form->fecha_final->output ?></td>
	  	  <td colspan="4" class="littletablerow"><?php echo $form->retencion->output ?></td>
      </tr>
	</table>		
	<table width='100%'>
		<tr>                                                           
	  	  <td colspan=7 class="littletableheader">Artículos, Trabajos o Servicios Contratados</td>      
	    </tr>  
			<tr>
				<td class="littletableheader" align="center">Partida</td>
				<td class="littletableheader" align="center">Descripci&oacute;n</td>
				<td class="littletableheader" align="center">Unidad de Medida</td>
				<td class="littletableheader" align="right">Cantidad</td>
				<td class="littletableheader" align="right">Precio Unitario</td>
				<td class="littletableheader" align="right">Importe Total Bs</td>
				
				<?php if($form->_status!='show') {?>
				<td class="littletableheader">&nbsp;</td>
				<?php } ?>
			</tr>
			<?php for($i=0;$i<$form->max_rel_count['itccont'];$i++) {
				$obj1="partida_$i";
				$obj2="descrip_$i";
				$obj3="unidad_$i";
				$obj4="cantidad_$i";
				$obj5="precio_$i";
				$obj6="monto_$i";
			?>
			<tr id='tr_itccont_<?php echo $i ?>'>
				<td class="littletablerow" align="left"><?php echo $form->$obj1->output ?></td>
				<td class="littletablerow" align="left"><?php echo $form->$obj2->output ?></td>
				<td class="littletablerow" align="center"><?php echo $form->$obj3->output ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$obj4->output ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$obj5->output ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$obj6->output ?></td>
				
				<?php if($form->_status!='show') {?>
				<td class="littletablerow"><a href=#
					onclick='del_itccont(<?php echo $i ?>);return false;'>Eliminar</a></td>
					<?php } ?>
			</tr>
			<?php } ?>

			<tr id='__UTPL__'>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				
				<?php if($form->_status!='show') {?>
				<td class="littletablefooterb" align="right">&nbsp;</td>
				<?php } ?>
			</tr>
		</table>
<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
		<td>
	<tr>
<table>
<table style="margin:0;width:100%;" > 
	    <tr>                                                           
	  	  <td colspan=6 class="littletableheader">Totales</td>      
	    </tr>                                                         
	    <tr>                                                 
		  <td  class="littletablerowth"  width="90%" align="right"><?php echo $form->base->label ?> </td>
		  <td  class="littletablerow"    width="10%" align="right"><?php echo $form->base->output ?> </td>
       </tr>
       <tr><?php $iva=$this->datasis->dameval('SELECT tasa FROM civa ORDER BY fecha DESC');?>
	 	 <td class="littletablerowth" align="right"><?php echo $form->impuesto->label.' '.$iva.'%' ?></td>
		 <td class="littletablerow"   align="right"><?php echo $form->impuesto->output ?></td>
       </tr>
	   <tr>
		 <td class="littletablerowth" align="right"><?php echo $form->tota->label ?></td>
		 <td class="littletablerow"   align="right"><?php echo $form->tota->output ?></td>
      </tr>
	   <tr>
	     <td class="littletablerowth" align="right" colspan="2"><?php echo $form->itccont->output ?></span></td>
    </tr>
</table>
<?php endif; ?>