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

function print_r(theObj){
	var a='';
   if(theObj.constructor == Array || theObj.constructor == Object){
      a=a+"<ul>";
      for(var p in theObj){
         if(theObj [p] .constructor == Array || theObj [p] .constructor == Object){
            a=a+"<li> ["+p+"]  => "+typeof(theObj)+"</li>";
            a=a+"<ul>";
            print_r(theObj [p] );
            a=a+"</ul>";
         } else {
            a=a+"<li> ["+p+"]  => "+theObj [p] +"</li>";
         }
      }
      a=a+"</ul>";
   }
   alert(a);
}

var importes= new Array();
function total(id){
	cana=Number(document.getElementById("cana_"+id).value);
	var e= document.getElementById("preca_"+id);
	var preca = Number(e.value);
	importes[id]=Math.round(cana*preca*100);
	totalizar();
}

function totalizar(){
	var totalg=0;
	var ivas  =0;
	for(var i in importes){
		iva    = document.getElementById("iva_"+i).value;
		totalg+= importes[i]+(Math.round(importes[i]*iva/100));
	}
	document.getElementById("totalg_value").innerHTML=totalg/100;
}
</script>
<?php } ?>

<table align='center' width="100%">
	<tr>
		<td><?php echo $form->numero->value.'-'.$form->nombre->value.$form->cliente->output; ?></td>
		<td align=right><?php echo $container_tl.$container_tr;?></td>
	</tr>
	<?php if(isset($saldo) && $saldo>0){ ?>
	<tr>
		<td colspan='2' align='center'>Saldo: <?php echo nformat($saldo);?></td>
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
		<td><b>Exis. </b></td>
		<td><b>Cant. </b></td>
		<td><b>Precio</b></td>
	</tr>
	</thead>
	<tbody>
	<?php
	$pmarcat='';
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
			$pexisten= ($row['existen']>$row['exord'])?$row['existen']-$row['exord']:0;
			$peso    = $row['peso'];
			$pdesca  = $row['descrip'].' '.nformat($peso).' KG';
			$codigoa = $row['codigo'];
			$preca   = $row['preca'];
			$cana    = $row['cana'];
			$precio1 = $row['precio1'];
			$precio2 = $row['precio2'];
			$precio1 = $row['precio1'];
			if($form->_status!='show'){
				$obj = 'cana_'.$i;
				if(isset($form->$obj)){
					$f_cana=$form->$obj->output;
				}else{
					$f_cana='<input type="text" autocomplete="off" onkeyup="total(\''.$i.'\')" maxlength="10" style="height:30px;font-size:18px" size="4" class="inputnum" id="cana_'.$i.'" value="'.($cana>0?$cana:'').'" name="cana_'.$i.'">';
				}
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
		<td align='right'><?php echo nformat($pexisten)  ?>  </td>
		<td align='right'><?php echo $f_cana;   ?></td>
		<td align='right'><?php
			if($form->_status=='show'){
				echo nformat($preca);
			}else{
				$data = array(
					'name'  => 'iva_'.$i,
					'id'    => 'iva_'.$i,
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
		<td align='right'><b><?php echo $form->totalg->label;  ?></b></td>
		<td align='right'><b id='totalg_value'><?php echo nformat($form->totalg->value); ?></b><?php echo $form->totalg->output; ?></td>
	</tr>
	<tr>
		<td><b><?php echo $form->observa->label;   ?></b></td>
		<td><?php echo $form->observa->output;   ?></td>
	</tr>
</table>

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
