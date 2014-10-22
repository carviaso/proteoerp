<?php echo $form_scripts?>
<?php echo $form_begin?>
<?php
$container_tr=join('&nbsp;', $form->_button_container['TR']);
$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
?>
<script language="javascript" type="text/javascript">
// Agrega Proveedor
function add_proveed(){
	var mtbanco = encodeURIComponent($("#tbanco").val());
	var mcodprv = encodeURIComponent($("#codprv").val());
	var mnombre = encodeURIComponent($("#nombre").val());

	var estados = {
		state0: {
			html : "<h1>Crear Banco como Proveedores</h1><br/><center>R.I.F. del Banco: <input name='mrif' value='' id='mrif' class='input' size='15' style='' maxlength='13' type='text'></center><br/>",
			buttons: { "Crear Proveedor": true, "Cancelar": false },
			focus: "input[name='mrif']",
			submit: function(e,v,m,f){
				if (v) {
					if( f.mrif==null ){
						alert("Cancelado por el usuario"); // No se da
					} else if( f.mrif=="" ) {
						$.prompt.goToState("state0");
					} else {
						e.preventDefault();
						$.ajax({
							url: "<?php echo site_url("finanzas/banc/agreprv/"); ?>",
							global: false,
							type: "POST",
							data: ({ rif : encodeURIComponent(f.mrif), nombre : mnombre, tbanco : mtbanco, codprv : mcodprv }),
							dataType: "text",
							async: false,
							success: function(sino) {
								$.prompt.getStateContent('state1').find('#in_prome1').text(sino);
								$.prompt.goToState('state1');
							},
							error: function(h,t,e)  {
								$.prompt.getStateContent('state1').find('#in_prome1').text("Error..RIF="+f.mrif+" "+e);
								$.prompt.goToState('state1');
						}});
					}
				}
			}
		},
		state1: {
			html: "<h1><div id='in_prome1'></div></h1>",
			buttons: { Volver: -1, Salir: 0  },
			submit: function(e,v,m,f){
				if ( v == 0) $.prompt.close()
				else $.prompt.goToState("state0");
				e.preventDefault();
			}
		}};
		$.prompt(estados);
};

</script>


<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table width="100%">
	<tr>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #FAFAFA;'>
			<table border=0 width="100%">
			<tr>
				<td width="90"    class="littletableheaderc"><?php echo $form->codbanc->label; ?></td>
				<td width="80"    class="littletablerow"    ><?php echo $form->codbanc->output;?></td>
				<td align='right' class="littletableheaderc"><?php echo $form->activo->label; ?></td>
				<td align='left'  class="littletablerow"    ><?php echo $form->activo->output;?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->tbanco->label; ?></td>
				<td colspan='3' class="littletablerow"   ><?php echo $form->tbanco->output;?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"           ><?php echo $form->banco->label; ?></td>
				<td colspan='3' class="littletablerow"   ><?php echo $form->banco->output;?></td>
			</tr>
			</table>
			</fieldset>
		</td>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #FAFAFA;'>
			<table border=0 width="100%">
			<tr>
				<td class="littletableheaderc"><?php echo $form->sucur->label; ?></td>
				<td class="littletablerow"    ><?php echo $form->sucur->output;?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->depto->label; ?> </td>
				<td class="littletablerow"    ><?php echo $form->depto->output;?> </td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->numcuent->label; ?></td>
				<td class="littletablerow"    ><?php echo $form->numcuent->output;?></td>
			</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
<table  width="100%" border='0'>
	<tr>
		<td valign='top'>
			<fieldset style='border: 1px outset #9AC8DA;background: #FAFAFA;'>
			<table width= "100%" >
				<tr>
					<td width='60px' class="littletableheaderc"><?php echo $form->nombre->label; ?></td>
					<td              class="littletablerow"    ><?php echo $form->nombre->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->telefono->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->telefono->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->dire1->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->dire1->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->dire2->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->dire2->output;?></td>
				</tr>
			</table>
			</fieldset>
		</td>


		<td  valign="top">
			<fieldset style='border: 1px outset #9AC8DA;background: #FAFAFA;'>
			<table style="height: 100%;width: 100%">
				<tr>
					<td  width="95" class="littletableheaderc"><?php echo $form->moneda->label; ?></td>
					<td             class="littletablerow"    ><?php echo $form->moneda->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->tipocta->label; ?> </td>
					<td class="littletablerow"    ><?php echo $form->tipocta->output;?> </td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->proxch->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->proxch->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->dbporcen->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->dbporcen->output;?></td>
				</tr>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
<table  width="100%" border='0'>
	<tr>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #FAFAFA;'>
			<table width= '100%' >
				<tr>
					<td class="littletableheaderc"><?php echo $form->gastocom->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->gastocom->output;?></td>
					<td class="littletableheaderc"><?php echo $form->rif->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->rif->output;?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->gastoidb->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->gastoidb->output;?></td>
					<td class="littletableheaderc"><?php echo $form->formula->label; ?></td>
					<td class="littletablerow"    ><?php echo $form->formula->output;?></td -->
				</tr>
				<tr>
				<td class="littletableheaderc"><?php echo $form->cuenta->label ?></td>
				<td class="littletablerow" colspan='3'  ><?php echo $form->cuenta->output;?>
				<?php
					if(!empty($form->cuenta->value)){
						$dbcuenta=$this->db->escape(trim($form->cuenta->value));
						$mSQL = "SELECT descrip FROM cpla WHERE codigo=${dbcuenta}";
						echo $this->datasis->dameval($mSQL);
					}
				?>
				</td>
			</tr>
			</table>
			</fieldset>
		</td>
		<?php if( $form->_status == 'show') { ?>
		<td valign='top'>
			<table width= '100%' >
				<tr>
					<td>
						<table width= '100%' >
							<tr>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td align='center' style='font-size:14;font-weight: bold'>SALDO ACTUAL</td>
							</tr>
						</table>
					<td>
				</tr>
				<tr>
					<td>
					<?php if($form->saldo->value >= 0 ) { ?>
					<fieldset style='border: 6px outset #407E13;background: #0B610B;'>
					<?php } else { ?>
					<fieldset style='border: 6px outset #8A0808;background: #B40404;'>
					<?php } ?>
					<table width= '100%' >
						<tr>
							<td align='center' style='font-size:18;font-weight: bold;color:#FFFFFF'><? echo nformat($form->saldo->value); ?></td>
						</tr>
					</table>
					</fieldset>
					</td>
				</tr>
			</table>
		</td>
		<?php } ?>
	</tr>
</table>
<?php echo $container_bl.$container_br; ?>
<?php echo $form_end?>
