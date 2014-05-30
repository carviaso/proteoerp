<?php
ob_start('comprimir_pagina');

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);
$container_tl=join('&nbsp;', $form->_button_container['TL']);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
if($form->_status!='show'){ ?>
<script language="javascript" type="text/javascript">

var importes= new Array();
function total(id){
	cana=Number(document.getElementById("cana_"+id).value);
	var e= document.getElementById("preca_"+id);
	var preca = Number(e.value);
	importes[id]=Math.round(cana*preca*100);
	totalizar();
}

function totalizar(){
	var totalg  =0;
	var ivas    =0;
	var impuesto=0;
	for(var i in importes){
		iva    = document.getElementById("itiva_"+i).value;
		impuesto+=Math.round(importes[i]*iva/100);
		totalg  +=importes[i]+(Math.round(importes[i]*iva/100));
	}
	document.getElementById("impuesto_value").innerHTML=impuesto/100;
	document.getElementById("totalg_value").innerHTML  =totalg/100;
}
</script>
<?php } ?>

<style>
#saldoven
{
background:yellow;
/* Chrome, Safari, Opera */
-webkit-animation-name:anisalven;
-webkit-animation-duration:2s;
-webkit-animation-timing-function:linear;
-webkit-animation-delay:1s;
-webkit-animation-iteration-count:infinite;
-webkit-animation-direction:alternate;
-webkit-animation-play-state:running;
/* Standard syntax */
animation-name:anisalven;
animation-duration:2s;
animation-timing-function:linear;
animation-delay:1s;
animation-iteration-count:infinite;
animation-direction:alternate;
animation-play-state:running;
}

/* Chrome, Safari, Opera */
@-webkit-keyframes anisalven
{
from {background:yellow;}
to {background:#32FFD3;}
}

/* Standard syntax */
@keyframes anisalven
{
from {background:yellow;}
to {background:#32FFD3}
}
</style>

<table align='center' width="100%">
	<tr>
		<td><?php echo $form->numero->value.'-'.$form->nombre->value.$form->cliente->output; ?></td>
		<td align=right><?php echo $container_tl.$container_tr;?></td>
	</tr>
	<?php if(isset($saldo) && $saldo>0){ ?>
	<tr>
		<td colspan='2' align='center'>Saldo vencido: <b id='saldoven' style='font-size:1.7em;color:#FF0800;'><?php echo htmlnformat($saldo);?></b></td>
	</tr>
	<?php } ?>

</table>

<table width='100%' <?php echo ($form->_status!='show'?' border="0" cellpadding="0" cellspacing="0"':'') ?>>
	<col>
	<?php if($act_meta) echo '<col align=\'center\'>'; ?>
	<col>
	<col>
	<col class="colbg2">
	<thead>
	<tr id='__INPL__'>
		<td><b>Art&iacute;culo</b></td>
		<?php if($act_meta) echo '<td><b>Meta</b></td>'; ?>
		<td align='center'><b>IVA </b></td>
		<td align='center'><b>Disp/Exis. </b></td>
		<td align='center'><b>Cant. </b></td>
		<td><b>Precio</b></td>
	</tr>
	</thead>
	<tbody>
	<?php
	$pmarcat=$js_ctotal='';
	$i=0;

	$arreglo=$it=$a=array();

	if($form->_status!='create'){
		$a=$form->_dataobject->get_all();
		if(isset($a['itpfac'])){
			foreach($a['itpfac'] as $k=>$v){
				$it[$v['codigoa']]=$v;
			}
		}
	}

	if($form->_status=='show' ){
		if(isset($a['itpfac'])){
			foreach($a['itpfac'] as $k=>$v){
				if(array_key_exists($v['codigoa'],$sinv)){
					$arreglo[$k]         =$sinv[$v['codigoa']];
					$arreglo[$k]['preca']=$v['preca'];
					$arreglo[$k]['cana'] =$v['cana'];
				}
			}
		}
	}else{
		foreach($sinv as $k=>$v){
			$arreglo[$k]=$v;
			if(array_key_exists($k,$it)){
				$arreglo[$k]['preca']=$it[$k]['preca'];
				$arreglo[$k]['cana'] =$it[$k]['cana'];
			}else{
				$arreglo[$k]['preca']=null;
				$arreglo[$k]['cana'] =0;
			}
		}
	}

	foreach($arreglo as $row) {

		$pmarca  = $row['marca'];
		$peso    = $row['peso'];
		$pdesca  = $row['descrip'].' '.nformat($peso).' KG';
		$codigoa = trim($row['codigo']);
		$preca   = $row['preca'];
		$cana    = floatval($row['cana']);
		$precio1 = $row['precio1'];
		$precio2 = $row['precio2'];
		$precio1 = $row['precio1'];
		$existen = $row['existen'];

		if(isset($pedido[$codigoa])){
			$row['exdes']=$pedido[$codigoa];
		}else{
			$row['exdes']=0;
		}

		if(isset($lleva[$codigoa])){
			$plleva=$lleva[$codigoa];
		}else{
			$plleva=0;
		}

		$pexisten= ($row['existen']>$row['exdes'])?$row['existen']-$row['exdes']+$plleva:0+$plleva;
		if($form->_status!='show'){
			if($pexisten>0 || $plleva>0){
				$val = floatval($this->input->post('cana_'.$i));
				if($val>0){
					$vval=$val;
				}elseif($cana>0){
					$vval=$cana;
				}else{
					$vval='';
				}

				$f_cana='<input type="text" autocomplete="off" onkeyup="total(\''.$i.'\')" maxlength="10" style="height:30px;font-size:18px;background-color:rgba(255,255,255,0.5);text-align:right;width:100%" size="4" class="inputnum" id="cana_'.$i.'" value="'.$vval.'" name="cana_'.$i.'">';
			}else{
				$f_cana =nformat($cana);
			}

			$js_ctotal.= "total('${i}');\n";
		}else{
			$f_cana =nformat($cana);
		}

		$f_codigoa='<input id="codigoa_'.$i.'" type="hidden" value='.$this->db->escape($row['codigo']).' name="codigoa_'.$i.'"> <span id="codigoa_'.$i.'_val">'.$row['codigo'].'</span>';

		if($pmarcat!=$pmarca){
			$pmarcat=$pmarca;
	 ?>
		<tr class='rowgroup'>
			<td colspan="5"><?php echo $pmarca; ?></td>
		</tr>
		<?php
		}
	?>
	<tr id='tr_itpfac_<?php echo $i; ?>' <?php echo ($i%2 == 0) ? 'class="odd"' : '';?> >
		<td><p class='miniblanco'><?php echo $f_codigoa ?></p>
			<?php echo $pdesca    ?></td>
		<?php if($act_meta){ ?>
		<td align='center'>
			<?php if($row['meta']>0){ ?>
				<b class='miniblanco' style='color:#<?php echo color(ceil($row['vendido']*100/$row['meta'])); ?>'><?php echo nformat($row['vendido'],0).'/'.nformat($row['meta'],0); ?></b>
			<?php }else{
				echo '-';
			} ?>
		</td>
		<?php } ?>
		<td align='right'><b style='font-size:0.7em'><?php echo $sinv[$codigoa]['iva']; ?>%</b></td>
		<td align='right'><?php
		if($status=='show'){
			echo ' ';
		}else{
			echo '<b>'.snformat($pexisten).'/</b><span style="font-size:0.8em;color:#FFD900">'.snformat($existen).'</span>';
		}
		?></td>
		<td align='right'><?php if(isset($f_cana)){ echo $f_cana; }else{ echo nformat(0); }  ?></td>
		<td align='right'><?php
			if($form->_status=='show'){
				echo nformat($preca);
			}else{
				$data = array(
					'name'  => 'itiva_'.$i,
					'id'    => 'itiva_'.$i,
					'value' => $sinv[$codigoa]['iva'],
					'type'  => 'hidden'
				);
				echo form_input($data);

				$data = array(
					'name'  => 'itdesca_'.$i,
					'id'    => 'itdesca_'.$i,
					'value' => $sinv[$codigoa]['descrip'],
					'type'  => 'hidden'
				);
				echo form_input($data);

				$data = array(
					'name'  => 'preca_'.$i,
					'id'    => 'preca_'.$i,
					'value' => round($sinv[$codigoa]['precio'.$tiposcli],2),
					'type'  => 'hidden'
				);
				echo form_input($data).nformat($sinv[$codigoa]['precio'.$tiposcli]);
			}
		?></td>
	</tr>
	<?php $i++;
	} ?>
</tbody>
</table>

<table width='100%'>
	<tr bgcolor='#5a7600'>
		<td align='right'><b>Impuesto</b> <b id='impuesto_value' style='font-size:1em'><?php echo nformat($form->iva->value); ?></b></td>
		<td align='right'><b><?php echo $form->totalg->label;  ?></b></td>
		<td align='right'><b id='totalg_value' style='font-size:1.2em'><?php echo nformat($form->totalg->value); ?></b><?php echo $form->totalg->output; ?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->observa->label;   ?></b></td>
		<td colspan='2'><?php echo $form->observa->output;   ?></td>
	</tr>
</table>
<?php if(!empty($js_ctotal)){ ?>
<script language="javascript" type="text/javascript">
	<?php echo $js_ctotal; ?>
</script>
<?php } ?>
<?php echo $form_end; ?>
<?php endif;
ob_end_flush();

function color($i=0){
	$max=255;
	$min=0;

	$r=($i<50)? $max: ceil(-($i-50)*(($max-$min)/50)  +$max);
	$g=($i<50)? ceil(-$i*(($min-$max)/50)  +$min): $max;
	$b=0;
	$color=str_pad( strtoupper(dechex($r)),2, '0', STR_PAD_LEFT).str_pad( strtoupper(dechex($g)),2, '0', STR_PAD_LEFT).str_pad( strtoupper(dechex($b)),2, '0', STR_PAD_LEFT);
	return $color;
}

// Función para eliminar todos los espacios en blanco
function comprimir_pagina($buffer) {
    $busca = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
    $reemplaza = array('>','<','\\1');
    return preg_replace($busca, $reemplaza, $buffer);
    //return $buffer;
}
?>
