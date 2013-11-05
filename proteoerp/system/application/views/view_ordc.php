<?php
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

$campos   = $form->template_details('itordc');
$scampos  ='<tr id="tr_itordc_<#i#>">';
$scampos .='<td class="littletablerow" align="left" >'.$campos['codigo']['field'].'</td>';
$scampos .='<td class="littletablerow" align="left" >'.$campos['descrip']['field'].'</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['cantidad']['field'].  '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['costo']['field']. '</td>';
$scampos .='<td class="littletablerow" align="right">'.$campos['importe']['field'];

for($o=1;$o<5;$o++){
	$it_obj   = "precio${o}";
	$scampos .= $campos[$it_obj]['field'];
}
$scampos .= $campos['iva']['field'];
$scampos .= $campos['ultimo']['field'];
$scampos .= $campos['pond']['field'];
$scampos .= $campos['sinvpeso']['field'].'</td>';
$scampos .= '<td class="littletablerow"><a href=# onclick="del_itordc(<#i#>);return false;">'.img("images/delete.jpg").'</a></td></tr>';
$campos=$form->js_escape($scampos);

if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>';

//echo $form_scripts;
echo $form_begin;
if($form->_status!='show'){
 $atts = array(
              'width'      => '800',
              'height'     => '600',
              'scrollbars' => 'yes',
              'status'     => 'yes',
              'resizable'  => 'yes',
              'screenx'    => '0',
              'screeny'    => '0'
            );

$sug = anchor_popup('compras/ordc/bussug', 'Buscar Sugerencias', $atts);

?>

<script language="javascript" type="text/javascript">
var itordc_cont=<?php echo $form->max_rel_count['itordc']; ?>;

$(function(){
	$(".inputnum").numeric(".");
	totalizar();

	$("#fecha").datepicker({    dateFormat: "dd/mm/yy" });
	$("#arribo").datepicker({   dateFormat: "dd/mm/yy" });
	//$("#fechafac").datepicker({ dateFormat: "dd/mm/yy" });

	for(var i=0;i < <?php echo $form->max_rel_count['itordc']; ?>;i++){
		autocod(i.toString());
	}

	$('#proveed').autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasprv'); ?>",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						$.each(data,
							function(i, val){
								sugiere.push( val );
							}
						);
						add(sugiere);
					},
			})
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#proveed').attr("readonly", "readonly");

			$('#nombre').val(ui.item.nombre);
			$('#nombre_val').text(ui.item.nombre);
			$('#proveed').val(ui.item.proveed);

			setTimeout(function() {  $('#proveed').removeAttr("readonly"); }, 1500);
		}
	});
});

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		delay: 600,
		autoFocus: true,
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscascstart'); ?>",
				type: "POST",
				dataType: "json",
				data: {'q':req.term,sprv:$('#sprv').val()},
				success:
					function(data){
						var sugiere = [];

						if(data.length==0){
							$('#codigo_'+id).val("");
							$('#descrip_'+id).val("");
							$('#descrip_'+id+'_val').text("");
							$('#iva_'+id).val(0);
							$('#sinvpeso_'+id).val(0);
							$('#costo_'+id).val(0);
							$('#cantidad_'+id).val('');
						}else{
							$.each(data,
								function(i, val){
									sugiere.push( val );
								}
							);
						}
						add(sugiere);
					},
			})
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#codigo_'+id).attr("readonly", "readonly");

			var cana=Number($("#cantidad_"+id).val());
			$('#codigo_'+id).val(ui.item.codigo);
			$('#descrip_'+id).val(ui.item.descrip);
			$('#descrip_'+id+'_val').text(ui.item.descrip);
			$('#iva_'+id).val(ui.item.iva);
			$('#sinvpeso_'+id).val(ui.item.peso);
			$('#costo_'+id).val(ui.item.pond);
			if(cana<=0) $("#cantidad_"+id).val('1');
			$('#cantidad_'+id).focus();
			$('#cantidad_'+id).select();
			//post_modbus_sinv(parseInt(id));
			importe(parseInt(id));
			//totalizar();
			setTimeout(function() {  $('#codigo_'+id).removeAttr("readonly"); }, 1500);
		}
	});
}

function importe(id){
	var ind     = id.toString();
	var cana    = Number($("#cantidad_"+ind).val());
	var preca   = Number($("#costo_"+ind).val());
	var importe = roundNumber(cana*preca,2);
	$("#importe_"+ind).val(importe);

	totalizar();
}

function totalizar(){
	var iva    =0;
	var totalg =0;
	var iva    =0;
	var itpeso =0;
	var totals =0;
	var importe=0;
	var peso   =0;
	var cana   =0;
	var arr=$('input[name^="importe_"]');
	jQuery.each(arr, function() {
		nom=this.name
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			cana    = Number($("#cantidad_"+ind).val());
			iva     = Number($("#iva_"+ind).val());
			itpeso  = Number($("#sinvpeso_"+ind).val());
			importe = Number(this.value);

			peso    = peso+(itpeso*cana);
			iva     = iva+importe*(iva/100);
			totals  = totals+importe;
		}
	});
	$("#peso").val(roundNumber(peso,2));
	$("#montonet").val(roundNumber(totals+iva,2));
	$("#montotot").val(roundNumber(totals,2));
	$("#montoiva").val(roundNumber(iva,2));

	$("#peso_val").text(nformat(peso,2));
	$("#montonet_val").text(nformat(totals+iva,2));
	$("#montotot_val").text(nformat(totals,2));
	$("#montoiva_val").text(nformat(iva,2));

}

function post_modbus_sinv(id){
	var cana    = Number($("#cantidad_"+id).val());
	var descrip = $('#descrip_'+id).val();

	if(cana<=0) $("#cantidad_"+id).val('1');
	$('#cantidad_'+id).focus();
	$('#cantidad_'+id).select();
	$('#descrip_'+id+'_val').text(descrip);
}

function add_itordc(){
	var htm = <?php echo $campos; ?>;
	can = itordc_cont.toString();
	con = (itordc_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__PTPL__").after(htm);
	$("#cantidad_"+can).numeric(".");
	itordc_cont=itordc_cont+1;
	autocod(can);
	return can;
}

function del_itordc(id){
	id = id.toString();
	$('#tr_itordc_'+id).remove();
	totalizar();
}
</script>
<?php } ?>

<table align='center' width="100%">
	<tr>
		<td>
		<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
		<table width="100%" style="margin: 0; width: 100%;">

			<tr>
				<td class="littletableheader" width="90"> <?php echo $form->proveed->label;  ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->proveed->output ?><b id='nombre_val'><?php echo $form->nombre->value ?></b><?php echo $form->nombre->output ?></td>
				<td class="littletableheader" width="110"><?php echo $form->fecha->label;    ?>*&nbsp;</td>
				<td class="littletablerow"    width="150"><?php echo $form->fecha->output;   ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->status->label;    ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->status->output;   ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->arribo->label     ?>&nbsp;</td>
				<td class="littletablerow">   <?php echo $form->arribo->output    ?>&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->peso->label  ?>&nbsp;</td>
				<td class="littletablerow" align="left"><?php echo $form->peso->output ?>&nbsp;</td>
				<td class="littletableheader"><?php echo $form->fechafac->label  ?>&nbsp;</td>
				<td class="littletablerow" >  <?php echo $form->fechafac->output ?>&nbsp;</td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<div style='overflow:auto;border: 1px solid #9AC8DA;background: #FAFAFA;height:200px'>
		<table width='100%'>

			<tr id='__PTPL__'>
				<td class="littletableheaderdet">C&oacute;digo</td>
				<td class="littletableheaderdet">Descripci&oacute;n</td>
				<td class="littletableheaderdet">Cantidad</td>
				<td class="littletableheaderdet">Precio</td>
				<td class="littletableheaderdet">Importe</td>
				<?php if($form->_status!='show') {?>
					<td class="littletableheaderdet">&nbsp;</td>
				<?php } ?>
			</tr>

			<?php for($i=0;$i<$form->max_rel_count['itordc'];$i++) {
				$it_codigo    = "codigo_$i";
				$it_descrip   = "descrip_$i";
				$it_cantidad  = "cantidad_$i";
				$it_costo     = "costo_$i";
				$it_importe   = "importe_$i";
				$it_iva       = "iva_$i";
				$it_ultimo    = "ultimo_$i";
				$it_pond      = "pond_$i";
				$it_peso      = "sinvpeso_$i";
				$it_tipo      = "sinvtipo_$i";

				$pprecios='';
				for($o=1;$o<5;$o++){
					$it_obj   = "precio${o}_${i}";
					$pprecios.= $form->$it_obj->output;
				}
				$pprecios .= $form->$it_ultimo->output;
				$pprecios .= $form->$it_pond->output;
				$pprecios .= $form->$it_iva->output;
				$pprecios .= $form->$it_peso->output;
			?>

			<tr id='tr_itordc_<?php echo $i; ?>'>
				<td class="littletablerow" align="left" nowrap><?php echo $form->$it_codigo->output; ?></td>
				<td class="littletablerow" align="left" ><?php echo $form->$it_descrip->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_cantidad->output;   ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_costo->output;  ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$it_importe->output.$pprecios;?></td>

				<?php if($form->_status!='show') {?>
				<td class="littletablerow">
					<a href='#' onclick='del_itordc(<?php echo $i ?>);return false;'><?php echo img("images/delete.jpg");?></a>
				</td>

				<?php } ?>
			</tr>
			<?php } ?>
		</table>
		</div>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset style='border: 2px outset #9AC8DA;background: #EFEFFF;'>
		<table width='100%'>
			<tr>
				<td rowspan='3' align='center'>
					<?php echo $container_bl.$container_br; ?>
					<p>
					<?php if($form->_status!='show'){ ?>
						<a href="javascript:void(0);" style='font-size:1.2em;text-decoration:none;font-weight:bold;color:#166D05' onclick="bus_sug=window.open('/proteoerp/compras/ordc/bussug', 'bussug', 'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=0,screeny=0');">Buscar Sugerencias</a>
					<?php } ?>
					</p>
				</td>
				<td class="littletablerow"   align='center'><?php echo $form->condi1->output;  ?>&nbsp;</td>
				<td class="littletablerowth" align="right" ><?php echo $form->montotot->label; ?></td>
				<td class="littletablerow"   align="right" style='font-size:1.2em;font-weight:bold;'><?php echo $form->montotot->output; ?></td>
			</tr><tr>
				<td class="littletablerow"   align='center'><?php echo $form->condi2->output;  ?>&nbsp;</td>
				<td class="littletablerowth" align="right" ><?php echo $form->montoiva->label; ?></td>
				<td class="littletablerow"   align="right" style='font-size:1.2em;font-weight:bold;'><?php echo $form->montoiva->output;   ?></td>
			</tr><tr>
				<td class="littletablerow"   align='center'><?php echo $form->condi3->output;  ?>&nbsp;</td>
				<td class="littletablerowth" align="right" ><?php echo $form->montonet->label; ?></td>
				<td class="littletablerow"   align="right" style='font-size:1.5em;font-weight:bold;'><?php echo $form->montonet->output; ?></td>
			</tr>
		</table>
		<?php echo $form_end; ?>
		</td>
	</tr>
	<?php if($form->_status == 'show'){ ?>
	<tr>
		<td>
			<fieldset style='border: 2px outset #8A0808;background: #FFFBE9;'>
			<legend class="titulofieldset" style='color: #114411;'>Informaci&oacute;n del Registro</legend>
			<table width='100%' cellspacing='1' >
				<tr style='font-size:12px;color:#0B3B0B;background-color: #F7BE81;'>
					<td align='center' >Usuario</td>
					<td align='center' >Nombre </td>
					<td align='center' >Fecha  </td>
					<td align='center' >Hora   </td>
					<td align='center' >Transacci&oacute;n</td>
				</tr>
				<tr>
					<?php
						$mSQL='SELECT us_nombre FROM usuario WHERE us_codigo='.$this->db->escape(trim($form->_dataobject->get('usuario')));
						$us_nombre = $this->datasis->dameval($mSQL);

					?>
					<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('usuario'); ?>&nbsp;</td>
					<td class="littletablerow" align='center'><?php echo $us_nombre ?>&nbsp;</td>
					<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('estampa'); ?>&nbsp;</td>
					<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('hora'); ?>&nbsp;</td>
					<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('transac'); ?>&nbsp;</td>
				</tr>
				<tr style='font-size:12px;color:#0B3B0B;background-color: #F7BE81;'>
					<td colspan='5' >&nbsp;</td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
	<?php } ?>
</table>
<?php endif; ?>
