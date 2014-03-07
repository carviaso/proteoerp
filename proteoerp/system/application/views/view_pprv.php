<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
ob_start('comprimir_pagina');

$container_bl = join('&nbsp;', $form->_button_container['BL']);
$container_br = join('&nbsp;', $form->_button_container['BR']);
$container_tr = join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_begin;
$dbproveed=$this->db->escape($form->cod_prv->value);
$nomprv=$this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed=${dbproveed}");

if($form->getstatus()!='show'){
?>
<script type="text/javascript">
var objivas =<?php echo $json_ivas;  ?>;
var objptasa=<?php echo $json_ptasa; ?>;

$(function(){
	$(".inputnum").numeric(".");

	$('input[name^="abono_"]').keyup(function(){
		totaliza();
	});

	$('input[name^="abono_"]').focusout(function(){
		totaliza();
	});

	$('form').submit(function() {
		var r=confirm("Confirma guardar las transacciones?");
		return r;
	});

	$('#fecha').datepicker({ dateFormat: "dd/mm/yy" });
	$('#posdata').datepicker({ dateFormat: "dd/mm/yy" });

	$('#montasa'  ).focus(function (){ invasdif('montasa','tasa'     ,Number($("#ptasa"     ).val())/100); });
	$('#monredu'  ).focus(function (){ invasdif('monredu','reducida' ,Number($("#preducida" ).val())/100); });
	$('#monadic'  ).focus(function (){ invasdif('monadic','sobretasa',Number($("#psobretasa").val())/100); });
	$('#tasa'     ).focus(function (){ invasdif('montasa','tasa'     ,Number($("#ptasa"     ).val())/100); });
	$('#reducida' ).focus(function (){ invasdif('monredu','reducida' ,Number($("#preducida" ).val())/100); });
	$('#sobretasa').focus(function (){ invasdif('monadic','sobretasa',Number($("#psobretasa").val())/100); });
	$('#exento'   ).focus(function (){ invasdif('exento' ,'E'        ,0);     });


	$('#montasa').keyup(function (){
		var ptasa = Number($("#ptasa").val())/100;
		var base  = Number($('#montasa').val());
		$('#tasa').val(roundNumber(base*ptasa,2));
	});

	$('#monredu').keyup(function (){
		var ptasa = Number($("#preducida").val())/100;
		var base  = Number($('#monredu').val());
		$('#reducida').val(roundNumber(base*ptasa,2));
	});

	$('#monadic').keyup(function (){
		var ptasa = Number($("#padicional").val())/100;
		var base  = Number($('#monadic').val());
		$('#sobretasa').val(roundNumber(base*ptasa,2));
	});

	$('#tasa').keyup(function (){
		var ptasa    = Number($("#ptasa").val())/100;
		var impuesto = Number($('#tasa').val());
		$('#montasa').val(roundNumber(impuesto*ptasa,2));
	});

	$('#reducida').keyup(function (){
		var ptasa    = Number($("#preducida").val())/100;
		var impuesto = Number($('#reducida').val());
		$('#monredu').val(roundNumber(impuesto*ptasa,2));
	});

	$('#sobretasa').keyup(function (){
		var ptasa    = Number($("#padiciona").val())/100;
		var impuesto = Number($('#sobretasa').val());
		$('#monadic').val(roundNumber(impuesto*ptasa,2));
	});

	$('#exento').keyup(function (){
		invasdif('exento','E',0);
	});

	chtipodoc();
	totaliza();
});

function invasdif(base,iva,ptasa){
	var total     = Number($('#monto').val());
	var exento    = Number($('#exento').val());
	var sobretasa = Number($('#sobretasa').val());
	var reducida  = Number($('#reducida').val());
	var tasa      = Number($('#tasa').val());
	var monadic   = Number($('#monadic').val());
	var monredu   = Number($('#monredu').val());
	var montasa   = Number($('#montasa').val());
	var itota = sobretasa+exento+reducida+tasa+monadic+monredu+montasa;
	var diff  = total-itota;

	if(diff!=0){
		var bbase = diff/(1+ptasa);
		var iiva  = bbase*ptasa;

		$('#'+base).val(roundNumber(bbase,2));
		if(iva!='E'){
			$('#'+iva).val(roundNumber(iiva,2));
		}
	}
}

function chtipodoc(){
	var tipo=$('#tipo_doc').val();
	if(tipo=='NC'){
		$('#aplefectos').show();
		$('#aplpago').hide();
		/*$('input[name^="ppago_"]').val('');
		$('input[name^="ppago_"]').hide('');
		$('#ppagotit').hide(); */
		$('#monto_val').show();
		$('#monto').attr('type','hidden');
		$('#trdpto').hide();
		$('#trnd').show();
		$('#ncadic').show();
		$('#fpago').hide();
		$('#trnd2').show();
	}else if(tipo=='AN'){
		$('#aplefectos').hide();
		$('input[name^="abono_"]').val("");
		/*$('input[name^="ppago_"]').val("");*/
		$('#aplpago').show();
		$('#monto_val').hide();
		$('#monto').attr('type','text');
		$('#trdpto').show();
		$('#trnd').hide();
		$('#ncadic').hide();
		$('#fpago').show();
		$('#trnd2').hide();
		totaliza();
	}else{
		$('#aplefectos').show();
		$('#aplpago').show();
		/*$('input[name^="ppago_"]').show('');
		$('#ppagotit').show();*/
		$('#monto_val').show();
		$('#monto').attr('type','hidden');
		$('#trdpto').hide();
		$('#trnd').hide();
		$('#ncadic').hide();
		$('#fpago').show();
		$('#trnd2').hide();
	}
}

function totaliza(){
	var stota =0;
	var sppago=0;
	var i=0;
	var montasa  =0;
	var monredu  =0;
	var monadic  =0;
	var tasa     =0;
	var reducida =0;
	var sobretasa=0;
	var exento   =0;
	var arr  = $('input[name^="abono_"]');
	var mascara= "PAGA ";
	var tipo=$('#tipo_doc').val();

	jQuery.each(arr, function(){
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			i       = parseInt(ind);
			num     = Number(this.value);
			/*ppago   = Number($('#ppago_'+ind).val());*/
			ppago   = 0;
			monto   = Number($('#monto_'+ind).val());
			tipo_doc= $('#tipo_doc_'+ind).val();
			numero  = $('#numero_'+ind).val();
			if(!isNaN(num) && num>0){
				mascara= mascara+tipo_doc+numero+', ';

				montasa  = montasa  +(num*objivas[i].montasa  /monto);
				monredu  = monredu  +(num*objivas[i].monredu  /monto);
				monadic  = monadic  +(num*objivas[i].monadic  /monto);
				tasa     = tasa     +(num*objivas[i].tasa     /monto);
				reducida = reducida +(num*objivas[i].reducida /monto);
				sobretasa= sobretasa+(num*objivas[i].sobretasa/monto);
				exento   = exento   +(num*objivas[i].exento   /monto);

				stota += num;
				if(!isNaN(ppago)){
					sppago += ppago;
				}
			}else{
				this.value='';
			}
		}
	});

	montasa  =roundNumber(montasa  ,2);
	monredu  =roundNumber(monredu  ,2);
	monadic  =roundNumber(monadic  ,2);
	tasa     =roundNumber(tasa     ,2);
	reducida =roundNumber(reducida ,2);
	sobretasa=roundNumber(sobretasa,2);
	exento   =roundNumber(exento   ,2);

	$('#montasa'  ).val(montasa  );
	$('#monredu'  ).val(monredu  );
	$('#monadic'  ).val(monadic  );
	$('#tasa'     ).val(tasa     );
	$('#reducida' ).val(reducida );
	$('#sobretasa').val(sobretasa);
	$('#exento'   ).val(exento   );

	<?php if($por_rete>=0){ ?>
	if(tipo=='NC'){
		$('#reteiva').val(roundNumber((tasa+reducida+sobretasa)*<?php echo $por_rete; ?>,2));
	}
	<?php } ?>


	$('#monto').val(roundNumber(stota-sppago ,2));
	$('#monto_val').text(nformat(stota-sppago ,2));
	if(stota>0){
		$("#observa1").val(mascara);
	}else{
		$("#observa1").val('');
	}
}


function itsaldo(obj,saldo){
	if(obj.value.length==0){
		obj.value=saldo;
		totaliza();
	}
}

function itppago(obj,ind){
	var monto=0;
	var valor=Number(obj.value);
	var nval=0;

	if(valor==NaN){
		obj.value='0';
	}else if(valor<0){
		monto=Number($('#monto_'+ind).val());
		nval=monto*valor*-1/100;
		obj.value=roundNumber(nval,2);
		totaliza();
	}
}

function chapltasa(){
	var ind = $("#apltasa option:selected").index();
	$('#ptasa'     ).val(roundNumber(objptasa[ind][0],2));
	$('#preducida' ).val(roundNumber(objptasa[ind][1],2));
	$('#padicional').val(roundNumber(objptasa[ind][2],2));
	$('#ptasa_val'     ).text(nformat(objptasa[ind][0],2));
	$('#preducida_val' ).text(nformat(objptasa[ind][1],2));
	$('#padicional_val').text(nformat(objptasa[ind][2],2));

	var base = 0;
	var general  = Number($('#montasa').val())+Number($('#tasa').val());
	var reducido = Number($('#monredu').val())+Number($('#reducida').val());
	var adicional= Number($('#monadic').val())+Number($('#sobretasa').val());

	base = roundNumber(general*100/(100+objptasa[ind][0]),2);
	$('#montasa').val(base);
	$('#tasa').val(roundNumber(general-base,2));

	base = roundNumber(reducido*100/(100+objptasa[ind][1]),2);
	$('#monredu').val(base);
	$('#reducida').val(roundNumber(reducido-base,2));

	base = roundNumber(adicional*100/(100+objptasa[ind][2]),2);
	$('#monadic').val(base);
	$('#sobretasa').val(roundNumber(adicional-base,2));

}

</script>
<?php } ?>
<?php
echo $title;
?>
<table align='center' width="100%">
	<tr>
		<td colspan='4'><?php echo $form->numero->value.$form->cod_prv->output ?></td>
		<td align=right><?php echo $container_tr;?></td>
	</tr>
	<tr>
		<td><?php echo $form->tipo_doc->label;  ?>*</td>
		<td><?php echo $form->tipo_doc->output; ?></td>
		<td><span id='trdpto'><?php echo $form->depto->label; ?>* <?php echo $form->depto->output; ?></span></td>
		<td><?php echo $form->fecha->label;    ?>*</td>
		<td><?php echo $form->fecha->output;   ?></td>
	</tr>
	<tr id='trnd'>
		<td><?php echo $form->serie->label;  ?>*</td>
		<td><?php echo $form->serie->output; ?></td>
		<td></td>
		<td><?php echo $form->nfiscal->label;  ?>*</td>
		<td><?php echo $form->nfiscal->output; ?></td>
	</tr>
	<tr id='trnd2'>
		<td colspan='5' align='center'><?php echo $form->codigo->label.'* '. $form->codigo->output;  ?></td>
	</tr>
</table>
<?php if($cana>0){ ?>
<table width='100%' align='center' id='aplefectos'>
	<col>
	<col class="colbg1">
	<col class="colbg1">
	<col class="colbg1">
	<col class="colbg2">
	<thead>
		<tr>
			<td class="littletableheaderdet"><b>Documento</b></td>
			<td align="center" class="littletableheaderdet"><b>Fecha</b></td>
			<td align="center" class="littletableheaderdet"><b>Vence</b></td>
			<td align="right"  class="littletableheaderdet"><b>Monto</b></td>
			<td align="center" class="littletableheaderdet"><b>Saldo</b></td>
			<td align="right"  class="littletableheaderdet"><b>Abonar</b></td>
			<!-- <td align="right"  class="littletableheaderdet"><b id='ppagotit'>P.Pago</b></td> -->
		</tr>
	</thead>
	<tbody>
	<?php
	$pmarcat='';
	for($i=0;$i<$cana;$i++) {
		$it_tipo_doc = "tipo_doc_${i}";
		$it_numero   = "numero_${i}";
		$it_fecha    = "fecha_${i}";
		$it_monto    = "monto_${i}";
		$it_abono    = "abono_${i}";
		$it_saldo    = "saldo_${i}";
		//$it_ppago    = "ppago_${i}";
		$it_vence    = "vence_${i}";
	?>
	<tr id='tr_itccli_<?php echo $i; ?>' <?php echo ($i%2 == 0) ? 'class="odd"' : '';?> >
		<td><?php echo $form->$it_tipo_doc->output;?>-<?php echo $form->$it_numero->output;?></td>
		<td align="center"><?php echo $form->$it_fecha->output; ?></td>
		<td align="center"><?php echo $form->$it_vence->output; ?></td>
		<td align="right" ><?php echo $form->$it_monto->output; ?></td>
		<td align="right" ><?php echo $form->$it_saldo->output; ?></td>
		<td align="right" ><?php echo $form->$it_abono->output; ?></td>
	</tr>
	<?php } ?>
	</tbody>
</table>
<?php } ?>

<table width="100%">
	<tfoot>
	<tr>
		<td align="right" style='font-size: 1.6em;'><b><?php echo $form->monto->label; ?></b></td>
		<td align="right" style='font-size: 1.6em;font-weight: bold;'><?php echo $form->monto->output; ?></td>
	</tr>
	</tfoot>
</table>

<p style='text-align:center'>
<table id='ncadic' style='background:#F2E69D;margin-left:auto;margin-right:auto;'>
	<tr>
		<td class="littletableheaderdet">Tasa</td>
		<td class="littletableheaderdet">Base</td>
		<td class="littletableheaderdet">Impuesto</td>
	</tr><tr>
		<td colspan='3' align="center">Aplicar tasa de fecha <?php echo $form->apltasa->output;  ?></td>
	</tr><tr>
		<td align="right"><?php echo $form->ptasa->output;      ?></td>
		<td align="right"><?php echo $form->montasa->output;    ?></td>
		<td align="right"><?php echo $form->tasa->output;       ?></td>
	</tr><tr>
		<td align="right"><?php echo $form->preducida->output;  ?></td>
		<td align="right"><?php echo $form->monredu->output;    ?></td>
		<td align="right"><?php echo $form->reducida->output;   ?></td>
	</tr><tr>
		<td align="right"><?php echo $form->padicional->output; ?></td>
		<td align="right"><?php echo $form->monadic->output;    ?></td>
		<td align="right"><?php echo $form->sobretasa->output;  ?></td>
	</tr><tr>
		<td align="right">Exento</td>
		<td align="right"><?php echo $form->exento->output;  ?></td>
		<td align="right"></td>
	</tr><?php if($por_rete>=0){ ?>
	<tr>
		<td align="center" colspan='3'><?php echo  $form->reteiva->label.' '.$form->reteiva->output;?></td>
	</tr>
	<?php } ?>
</table>
</p>
<?php echo $container_br.$container_bl;?>

<table align='center' style='width:100%;font-size:10pt;background:#F2E69D;' id='fpago'>
	<tr>
		<td><?php echo $form->banco->label;    ?>*</td>
		<td><?php echo $form->banco->output;   ?></td>
		<td><?php echo $form->tipo_op->label;  ?>*</td>
		<td><?php echo $form->tipo_op->output; ?></td>
	</tr><tr>
		<td><?php echo $form->numche->label;   ?></td>
		<td><?php echo $form->numche->output;  ?></td>
		<td><?php echo $form->posdata->label;  ?></td>
		<td><?php echo $form->posdata->output; ?></td>
	</tr><tr>
		<td><?php echo $form->benefi->label ?></td>
		<td colspan='3'><?php echo $form->benefi->output ?></td>
	</tr>
</table>
<table width='100%'>
	<tr>
		<td align='center'><b>Concepto:</b><br><?php echo $form->observa1->output.$form->observa2->output; ?></td>
	</tr>
</table>
<?php echo $form_end; ?>

<?php endif; ?>
<?php
ob_end_flush();
// Función para eliminar todos los espacios en blanco
function comprimir_pagina($buffer) {
    $busca = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
    $reemplaza = array('>','<','\\1');
    return preg_replace($busca, $reemplaza, $buffer);
}
?>
