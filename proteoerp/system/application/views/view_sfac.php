<?php
$container_bl=join('&nbsp;', $form->_button_container["BL"]);
$container_br=join('&nbsp;', $form->_button_container["BR"]);
$container_tr=join('&nbsp;', $form->_button_container["TR"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

//echo $form_scripts;
//echo $form_begin;
if($form->_status!='show'){
?>

<script language="javascript" type="text/javascript">
var sitems_cont =<?php echo $form->max_rel_count['sitems']; ?>;
var sfpa_cont=<?php echo $form->max_rel_count['sfpa'];?>;

$(document).ready(function() {
	$(".inputnum").numeric(".");
});

function add_sitems(){
	var htm = <?php echo $campos; ?>;
	can = sitems_cont.toString();
	con = (sitems_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__").before(htm);
	sitems_cont=sitems_cont+1;
}

function add_sfpa(){
	var htm = <?php echo $campossfpa; ?>;
	var can = sfpa_cont.toString();
	var con = (sfpa_cont+1).toString();
	htm = htm.replace(/<#i#>/g,can);
	htm = htm.replace(/<#o#>/g,con);
	$("#__UTPL__sfpa").before(htm);
	sfpa_cont=sfpa_cont+1;
}

function del_sfpa(id){
	id = id.toString();
	obj='#tr_sfpa_'+id;
	$(obj).remove();
}

function del_sitems(id){
	id = id.toString();
	obj='#tr_sitems_'+id;
	$(obj).remove();
}
</script>
<?php } ?>

<table align='center' width="99%">
	<tr>
		<td>
			<table width='100%'>
				<tr>
					<td>&nbsp;</td>
					<td width='40' valign='bottom'>&nbsp;
						<a href='javascript:sfacreiva("<?php echo $form->_dataobject->get('id'); ?>")'>
						<?php
							$propiedad = array('src' => 'images/retencion.gif', 'alt' => 'Retencion de IVA', 'title' => 'Retencion de IVA','border'=>'0','height'=>'30');
							echo img($propiedad);
						?>
						</a>
					</td>
					<td width='40' valign='bottom'>&nbsp;
						<a href='javascript:void(0);'
						onclick="window.open('<?php echo base_url(); ?>formatos/verhtml/FACTURA/<?php echo $form->tipo_doc->value.'/'.$form->numero->value; ?>', '_blank', 'width=800, height=600, scrollbars=Yes, status=Yes, resizable=Yes, screenx='+((screen.availWidth/2)-400)+',screeny='+((screen.availHeight/2)-300)+'');" >
						<?php
							$propiedad = array('src' => 'images/reportes.gif', 'alt' => 'Imprimir Documento', 'title' => 'Imprimir Documento','border'=>'0','height'=>'30');
							echo img($propiedad);
						?>
						</a>
					</td>
					<td align='right'><?php echo $container_tr?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td><div class="alert"> <?php if(isset($form->error_string)) echo $form->error_string; ?></div></td>
	</tr><tr>
		<td>
			<table width="100%"><tr><td>
				<fieldset style='border: 1px solid #9AC8DA;background: #FFFDE9;'>
				<table style="margin: 0; ">
					<tr>
						<td class="littletableheader"><?php echo $form->fecha->label  ?>&nbsp;</td>
						<td class="littletablerow">   <?php echo $form->fecha->output ?>&nbsp; </td>
					</tr>
					<tr>
						<td class="littletableheader" width='80'><?php echo $form->nfiscal->label ?>&nbsp; </td>
						<td class="littletablerow">   <?php echo $form->nfiscal->output ?>&nbsp; </td>
					</tr>
					<tr>
						<td class="littletableheader"><?php echo $form->maqfiscal->label ?>&nbsp;</td>
						<td class="littletablerow">   <?php echo $form->maqfiscal->output ?>&nbsp; </td>
					</tr>
				</table>
				</fieldset>
				</td><td>
				<?php $scliid = $this->datasis->dameval("SELECT id FROM scli WHERE cliente='".$form->cliente->value."'");?>
				<fieldset style='border: 1px solid #9AC8DA;background: #FFFDE9;'>
				<a href="#" onclick="window.open('<?php echo base_url()?>ventas/scli/dataedit/show/<?php echo $scliid ?>', '_blank', 'width=800, height=600, scrollbars=Yes, status=Yes, resizable=Yes, screenx='+((screen.availWidth/2)-400)+',screeny='+((screen.availHeight/2)-300)+'');" heigth="600" >


				<?php echo $form->cliente->value ?></a>]</legend>
				<table border='0' width="100%" style="margin: 0; width: 100%;">
					<tr>
						<td class="littletableheader"><?php echo $form->nombre->label  ?>&nbsp;</td>
						<td class="littletablerow">   <?php echo $form->nombre->output ?>&nbsp; </td>
						<td class="littletableheader"><?php echo $form->rifci->label  ?>&nbsp;</td>
						<td class="littletablerow">   <?php echo $form->rifci->output ?>&nbsp; </td>
					</tr>
					<tr>
						<td class="littletableheader"><?php echo $form->direc->label  ?>&nbsp;</td>
						<td class="littletablerow" colspan='3'><?php echo $form->direc->output ?>&nbsp;</td>
					</tr>
					<tr>
						<td class="littletableheader"><?php echo $form->vd->label     ?>&nbsp;</td>
						<td class="littletablerow">   <?php echo $form->vd->output    ?>&nbsp; </td>
						<td class="littletableheader"><?php echo $form->peso->label   ?>&nbsp;</td>
						<td class="littletablerow">   <?php echo $form->peso->output  ?>&nbsp; </td>
					</tr>
				</table>
				</fieldset>
				</td></tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<div align='center' style='border: 3px outset #EFEFEF;background: #EFEFFF '>
			<div id='grid1_container' style='width:740px;height:250px'></div>
			</div>
		</td>
	</tr>
	<tr>
		<td align='center'>
			<fieldset style='border: 1px solid #9AC8DA;background: #FFFBE9;'>
			<legend class="subtitulotabla" style='color: #114411;'>Totales</legend>
			<table width='100%'>
				<tr>
					<td class="littletableheader"><strong><?php echo $form->exento->label ?>&nbsp;</strong></td>
					<td style='font-size: 14px;font-weight: bold'align='right' ><?php echo nformat($form->exento->value)?>&nbsp;</td>
					<td class="littletableheader"><strong><?php echo $form->ivat->label ?>&nbsp;</strong></td>
					<td style='font-size: 14px;font-weight: bold' align='right'>   <?php echo nformat($form->ivat->value)  ?>&nbsp;</td>
				</tr><tr>
					<td class="littletableheader"><strong><?php echo $form->totals->label ?>&nbsp;</strong></td>
					<td style='font-size: 22px;font-weight: bold'align='right' ><?php echo nformat($form->totals->value)?>&nbsp;</td>
					<td class="littletableheader"><strong><?php echo $form->totalg->label ?>&nbsp;</strong></td>
					<td style='font-size: 22px;font-weight: bold' align='right'>   <?php echo nformat($form->totalg->value) ?>&nbsp;</td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>

<div id="maintabcontainer">
	<ul>
		<li><a href="#tab1">Observaciones</a></li>
		<li><a href="#tab2">Forma de Pago</a></li>
		<li><a href="#tab3">Informaci&oacute;n Adicional</a></li>
	</ul>
	<div id="tab1">
		<fieldset style='border: 1px solid #9AC8DA;background: #FFFBE9;'>
		<legend class="subtitulotabla" style='color: #114411;'>Observaciones</legend>
		<table width='100%'>
			<tr>
				<td class="littletableheader" >Observaciones</td>
			</tr><tr>
				<td class="littletablerow"><?php echo $form->observa->output  ?>&nbsp;</td>
			</tr><tr>
				<td class="littletablerow"><?php echo $form->observ1->output  ?>&nbsp;</td>
			</tr><tr>
				<td>
				<table width='100%'>
				<tr>
					<td class="littletableheader"><?php echo $form->ciudad->label  ?>&nbsp;</td>
					<td class="littletablerow">   <?php echo $form->ciudad->output ?>&nbsp;</td>
					<td class="littletableheader"><?php echo $form->zona->label    ?>&nbsp;</td>
					<td class="littletablerow">   <?php echo $form->zona->output   ?>&nbsp;</td>
				</tr>
				</table>
				</td>
			</tr>
		</table>
		</fieldset>
	</div>
	<div id="tab2">
		<fieldset style='border: 1px solid #9AC8DA;background: #EFEFFF;'>
		<legend class="subtitulotabla" style='color: #114411;'>Forma de Pago</legend>
		<table width='100%'>
			<?php
			if( $form->referen->value == 'C' ) { ?>
			<tr>
				<td class="littletableheaderdet">Tipo</td>
				<td class="littletableheaderdet">Numero</td>
				<td class="littletableheaderdet">Banco</td>
				<td class="littletableheaderdet">Saldo</td>
			</tr>
			<?php } else { ?>
			<tr>
				<td class="littletableheaderdet">Tipo</td>
				<td class="littletableheaderdet">Numero</td>
				<td class="littletableheaderdet">Banco</td>
				<td class="littletableheaderdet">Monto</td>
			</tr>
			<?php }
			if( $form->referen->value == 'C' ) {
			echo "
			<tr id='tr_sfpa_1'>
				<td class='littletablerow'nowrap>CR</td>
				<td class='littletablerow'>Vence ".dbdate_to_human($form->vence->value)."</td>
				<td class='littletablerow'></td>
				<td class='littletablerow' align='right'>";
				echo nformat($this->datasis->dameval("SELECT monto-abonos FROM smov WHERE transac=".$form->transac->value.""));
				echo "</td>";
			}
			//echo "		</tr>";

			for($i=0; $i < $form->max_rel_count['sfpa']; $i++) {
				$tipo   = "tipo_$i";
				$numref = "numref_$i";
				$monto  = "monto_$i";
				$banco  = "banco_$i";
			?>
			<tr id='tr_sfpa_<?php echo $i; ?>'>
				<td class="littletablerow" nowrap><?php echo $form->$tipo->output ?></td>
				<td class="littletablerow"><?php echo $form->$numref->output ?></td>
				<td class="littletablerow"><?php echo $form->$banco->output ?></td>
				<td class="littletablerow" align="right"><?php echo $form->$monto->output ?></td>
			<?php } ?>
			</tr>
			<tr id='__UTPL__sfpa'>
				<td colspan='9' class="littletableheaderdet">&nbsp;</td>
			</tr>
		</table>
		</fieldset>
	</div>
	<div id="tab3">
		<?php if($form->reiva->value > 0 ){
			// Busca la ND
			$mSQL     = "SELECT cod_cli, '' banco, tipo_doc, numero, fecha, vence, monto, abonos, transac FROM smov WHERE nroriva='".$form->creiva->value."' AND monto=".$form->reiva->value." AND cod_cli='REIVA'";
			$reivand  = $this->datasis->damerow($mSQL) ;
			$mSQL     = "SELECT cod_cli, '' banco, tipo_doc, numero, fecha, vence, monto, abonos, transac FROM smov WHERE nroriva='".$form->creiva->value."' AND monto=".$form->reiva->value." AND cod_cli='".$form->cliente->value."' AND transac='".$reivand['transac']."'";
			$reivanc  = $this->datasis->damerow($mSQL) ;
			$mSQL     = "SELECT codcp cod_cli, codbanc banco, tipo_op tipo_doc, numero, fecha, fecha vence, monto, 0 abonos, transac FROM bmov WHERE transac='".$reivand['transac']."'";
			$reivach  = $this->datasis->damerow($mSQL) ;
			$mSQL     = "SELECT cod_prv cod_cli, '' banco, tipo_doc, numero, fecha, vence, monto, abonos, transac FROM sprm WHERE monto=".$form->reiva->value." AND cod_prv='REINT' AND transac='".$reivand['transac']."'";
			$reivapr  = $this->datasis->damerow($mSQL) ;
		?>
		<fieldset style='border: 1px solid ##8A0808;background: #FFFBE9;'>
		<legend class="subtitulotabla" style='color: #114411;'>Retencion de IVA Nro. <?php echo $form->creiva->value ?></legend>
		<table width='100%' cellspacing='1' >
			<tr style='font-size:12px;color:#0B3B0B;background-color: #F7BE81;'>
				<td align='center' >Cliente</td>
				<td align='center' >B/Caja</td>
				<td align='center' >Tipo</td>
				<td align='center' >Comprobante</td>
				<td align='center' >Fecha  </td>
				<td align='center' >Emision</td>
				<td align='center' >Monto</td>
			</tr>
			<?php if( count($reivand) > 0 ) {
			echo "\t\t\t\t<tr>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivand['cod_cli']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivand['banco']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivand['tipo_doc']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivand['numero']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivand['fecha']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivand['vence']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow' align='right'>".$reivand['monto']."&nbsp;</td>";
			echo "\t\t\t\t</tr>";
			}?>
			<?php if( count($reivanc) > 0 ) {
			echo "\t\t\t\t<tr>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivanc['cod_cli']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivanc['banco']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivanc['tipo_doc']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivanc['numero']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivanc['fecha']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivanc['vence']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow' align='right'>".$reivanc['monto']."&nbsp;</td>";
			echo "\t\t\t\t</tr>";
			}?>
			<?php if( count($reivach) > 0 ) {
			echo "<tr>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivach['cod_cli']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivach['banco']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivach['tipo_doc']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivach['numero']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivach['fecha']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivach['vence']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow' align='right'>".$reivach['monto']."&nbsp;</td>";
			echo "\t\t\t\t</tr>";
			}?>
			<?php if( count($reivapr) > 0 ) {
			echo "<tr>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivapr['cod_cli']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivapr['banco']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivapr['tipo_doc']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivapr['numero']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivapr['fecha']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow'>".$reivapr['vence']."&nbsp;</td>";
			echo "\t\t\t\t\t<td class='littletablerow' align='right'>".$reivapr['monto']."&nbsp;</td>";
			echo "\t\t\t\t</tr>";
			}?>
		</table>
		</fieldset>
		<?php } ?>

		<fieldset style='border: 1px solid ##8A0808;background: #FFFBE9;'>
		<legend class="subtitulotabla" style='color: #114411;'>Informacion del Registro</legend>
		<table width='98%' cellspacing='1' >
			<tr style='font-size:12px;color:#0B3B0B;background-color: #F7BE81;'>
				<td align='center' >Usuario</td>
				<td align='center' >Nombre </td>
				<td align='center' >Fecha  </td>
				<td align='center' >Hora   </td>
				<td align='center' >Transacci&oacute;n</td>
				<td align='center' >Cajero</td>
			</tr><tr>
				<?php
					$mSQL="SELECT us_nombre FROM usuario WHERE us_codigo='".trim($form->_dataobject->get('usuario'))."'";
					$us_nombre = $this->datasis->dameval($mSQL);
				?>
				<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('usuario'); ?>&nbsp;</td>
				<td class="littletablerow" align='center'><?php echo $us_nombre ?>&nbsp;</td>
				<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('estampa'); ?>&nbsp;</td>
				<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('hora'); ?>&nbsp;</td>
				<td class="littletablerow" align='center'><?php echo $form->_dataobject->get('transac'); ?>&nbsp;</td>
				<td class="littletablerow" align='center'><?php echo $form->cajero->output   ?>&nbsp;</td>
			</tr>
		</table>
		</fieldset>
	</div>
</div>
</div>
<?php endif; ?>
