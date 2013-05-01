<?php 
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	$meco = $form->output;
	$meco = str_replace('class="tablerow"','class="tablerow" style="font-size:20px; align:center;" ',$meco);
	echo $meco."</td><td align='center'>".img("images/borrar.jpg");
else:
?>
<?php echo $form_scripts?>
<?php echo $form_begin?>

<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table border='0' width='100%' style='background: #EEEEEE'>
	<tr>
		<td width='40' align='center'>
			<?php if($form->_status=='show'){ ?>
			<a href='<?php echo base_url()."ventas/scli/consulta/".$form->_dataobject->get('id'); ?>'>
			<?php
				$propiedad = array('src' => 'images/ojos.png', 'alt' => 'Consultar Movimiento', 'title' => 'Consultar Detalles','border'=>'0','height'=>'25');
				echo img($propiedad);
			?>
			</a>
		</td>
		<td width='40' align='center'>
			<a href='javascript:fusionar("<?php echo $form->_dataobject->get('cliente'); ?>")'>
			<?php
				$propiedad = array('src' => 'images/fusionar.png', 'alt' => 'Cambio de Codigo', 'title' => 'Cambio de codigo','border'=>'0','height'=>'30','width'=>'32');
				echo img($propiedad);
			?>
			</a>
		</td>

		</td>
		<td align='center' valign='middle' width='40'>
			<?php } ?>
			<a href='<?php echo base_url()."reportes/index/scli" ?>'>
			<?php
				$propiedad = array('src' => 'images/reportes.gif', 'alt' => 'Reportes', 'title' => 'Reportes','border'=>'0','height'=>'20');
				echo img($propiedad);
			?>
			</a>
		</td>
		<td align='center' valign='middle'>
			<?php  if ($form->tipo->value=='0') echo "<div style='font-size:14px;font-weight:bold;color: #B40404'>***DESACTIVADO***</div>"; ?>&nbsp;
		</td>
		<td align='right'><?php echo $container_tr; ?></td>
	</tr>
</table>
<fieldset style='border: 2px outset #9AC8DA;background: #FFFDE9;'>
<legend class="titulofieldset" style='color: #114411;'>Identificacion</legend>
<table border='0' width="100%">
	<tr>
		<td>
			<table border='0' width="100%">
				<tr>
					<td width='100' class="littletableheaderc"><?php echo $form->cliente->label;  ?></td>
					<td width='150' class="littletablerow"    ><?php echo $form->cliente->output; ?></td>
					<td width='60'  class="littletableheaderc"><?php echo $form->rifci->label;    ?></td>
					<td class="littletablerow"><?php echo $form->docui->output; ?></td>
					<td><?php echo $form->rifci->output; ?></td>
					<td><?php echo $form->crc->output;   ?></td>
				</tr>

				<?php if($form->_status!='show'){ ?>
				<tr>
					<td class="littletableheaderc"><?php echo $form->nombre1->label; ?></td>
					<td colspan='5' class="littletablerow"><?php echo $form->nombre1->output;   ?></td>
				</tr>

				<tr id='tr_nombre2'>
					<td class="littletableheaderc"><?php echo $form->nombre2->label;   ?></td>
					<td colspan='5' class="littletablerow"><?php echo $form->nombre2->output;   ?></td>
				</tr>

				<tr id='tr_apellido1'>
					<td class="littletableheaderc"><?php echo $form->apellido1->label; ?></td>
					<td colspan='5' class="littletablerow"><?php echo $form->apellido1->output; ?></td>
				</tr>

				<tr id='tr_apellido2'>
					<td class="littletableheaderc"><?php echo $form->apellido2->label; ?></td>
					<td colspan='5' class="littletablerow"><?php echo $form->apellido2->output; ?></td>
				</tr>
				<?php }else{ ?>
				<tr id='tr_nombre'>
					<td class="littletableheaderc"><?php echo $form->nombre->label; ?></td>
					<td colspan='5' class="littletablerow"><?php echo $form->nombre->output; ?></td>
				</tr>
				<?php } ?>

				<tr>
					<td class="littletableheaderc"><?php echo $form->contacto->label  ?></td>
					<td colspan='5' class="littletablerow"><?php echo $form->contacto->output ?></td>
				</tr>

			</table>
		</td>
		<td valign='top' width='25%'>
			<table border=0 width="100%">
				<tr>
					<td class="littletableheaderc"> <?php echo $form->tipo->label ?></td>
					<td class="littletablerow"> <?php echo $form->tipo->output ?></td>
				</tr>
				<tr>
					<td colspan='2'>
			<fieldset style='border: 1px dotted #8A0808;background: #FFFBE9;'>
			<legend class="titulofieldset" style='color: #114411;'>Credito</legend>
			<table width= '100%' >
				<tr>
					<td class="littletableheaderc"><?php echo $form->formap->label  ?></td>
					<td class="littletablerow"><?php echo $form->formap->output ?></td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->limite->label  ?></td>
					<td class="littletablerow">    <?php echo $form->limite->output ?></td>
				</tr>				
			</table>
			</fieldset>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width='100%' style='border: 1px dotted #8A0808;background: #FFFBE9;'>
	<tr>
		<td class="littletableheaderc"><?php echo $form->grupo->label ?></td>
		<td class="littletablerow"><?php echo $form->grupo->output?></td>
		<td class="littletableheaderc"><?php echo $form->tiva->label  ?></td>
		<td class="littletablerow"><?php echo $form->tiva->output ?></td>
		<td  width="70" class="littletableheaderc"><?php echo $form->zona->label  ?></td>
		<td class="littletablerow"><?php echo $form->zona->output ?></td>
	</tr>
</table>
<table width= '100%' style='border: 1px dotted #8A0808;background: #FFFBE9;'>
	<tr>
		<td class="littletableheaderc">Cuenta Contable</td>
		<td class="littletablerow"    ><?php echo $form->cuenta->output; ?>
		<?php
		if ( $form->_status == 'show' ) {
			$mSQL = "SELECT descrip FROM cpla WHERE codigo='".trim($form->cuenta->output)."'";
			echo $this->datasis->dameval($mSQL);
		}
		?>
		</td>
		<td class="littletableheaderc">Cliente Asociado</td>
		<td class="littletablerow"><?php echo $form->socio->output ?></td>
<?php if (!empty($form->socio->value)) { ?>
		<td class="littletablerow"><?php echo $this->datasis->dameval("SELECT nombre FROM scli WHERE cliente='".$form->socio->value."'") ?></td>
<?php }; ?>
	</tr>
</table>
</fieldset>






<div id="maintabcontainer">
	<ul>
		<li><a href="#tab1">Direcciones</a></li>
		<li><a href="#tab2">Valores</a></li>
		<li><a href="#tab3">Anexo</a></li>
		<li><a href="#tab4">Historia</a></li>
	</ul>
	<div id="tab1"  style='background:#eeffff'>
	<table border='0' width="100%">
	<tr>
		<td valign='top' width='50%' style='border-top: 1px dotted; border-right: 1px dotted'>
			<table border='0' width='100%' >
				<tr>
					<td colspan='2' class="littletableheaderc">Direccion de Oficina</td>
				</tr>
				<tr>
					<td colspan='2' class="littletablerow"><?php echo $form->dire11->output ?>&nbsp;</td>
				</tr>
				<tr>
					<td colspan='2' class="littletablerow"><?php echo $form->dire12->output ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->ciudad1->label   ?></td>
					<td class="littletablerow" ><?php echo $form->ciudad1->output  ?>&nbsp;</td>
				</tr>
			</table>
		</td>
		<td valign='top' width='50%' style='border-top: 1px dotted; border-left: 1px dotted'>
			<table border='0'  width='100%'>
				<tr>
					<td colspan='2' class="littletableheaderc">Direccion de Envio</td>
				</tr>
				<tr>
					<td colspan='2' class="littletablerow"><?php echo $form->dire21->output ?>&nbsp;</td>
				</tr>
				<tr>
					<td colspan='2' class="littletablerow"><?php echo $form->dire22->output ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->ciudad2->label ?></td>
					<td class="littletablerow" style='font-size:11;'><?php echo $form->ciudad2->output ?>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	<br />
	<table style='height: 100%;width: 100%;border: 1px dotted;'>
	<tr>
		<td class="littletableheaderc"><?php echo $form->telefono->label  ?></td>
		<td class="littletablerow"    ><?php echo $form->telefono->output ?></td>
		<td class="littletableheaderc"><?php echo $form->telefon2->label  ?></td>
		<td class="littletablerow"    ><?php echo $form->telefon2->output ?></td>
	</tr>
	<tr>
		<td class="littletableheaderc"><?php echo $form->pais->label  ?> </td>
		<td class="littletablerow"    ><?php echo $form->pais->output ?> </td>
		<td class="littletableheaderc"><?php echo $form->email->label  ?></td>
		<td class="littletablerow"    ><?php echo $form->email->output ?></td>
	</tr>
	</table>
	</div>

        <div id="tab2">
	<fieldset style='border: 2px outset #8A0808;background: #FFFBE9;'>
	<table border='0' >
	<tr>
		<td class="littletableheaderc">Representante Legal</td>
		<td class="littletablerow"><?php echo $form->repre->output ?></td>
		<td class="littletableheaderc">C.I.</td>
		<td class="littletablerow"><?php echo $form->cirepre->output ?></td>
	</tr>
	</table>
	<br />
	<table >
	<tr>
		<td class="littletableheaderc"><?php echo $form->vendedor->label  ?></td>
		<td class="littletablerow"><?php echo $form->vendedor->output ?></td>
		<td class="littletableheaderc">Comision %</td>
		<td class="littletablerow"><?php echo $form->porvend->output ?></td>
	</tr>				
	<tr>
		<td class="littletableheaderc"><?php echo $form->cobrador->label  ?></td>
		<td class="littletablerow"><?php echo $form->cobrador->output ?></td>
		<td class="littletableheaderc">Comision %</td>
		<td class="littletablerow"><?php echo $form->porcobr->output ?></td>
	</tr>				
	</table>
	</fieldset>
        </div>

        <div id="tab3">
	<fieldset style='border: 2px outset #8A0808;background: #FFFBE9;'>
	<table width= '100%' >
		<tr>
			<td class="littletableheaderc"><?php echo $form->mensaje->label  ?></td>
			<td class="littletablerow"><?php echo $form->mensaje->output ?></td>
		</tr>				
		<tr>
			<td class="littletableheaderc"><?php echo $form->observa->label  ?></td>
			<td class="littletablerow"><?php echo $form->observa->output ?></td>
		</tr>				
	</table>
	</fieldset>
        </div>

        <div id="tab4">
	<fieldset style='border: 2px outset #8A0808;background: #FFFBE9;'>

<?php if($form->_status=='show'){ ?>
	<table  width='100%'>
	<tr>
		<td width='60%'>
			<table border='0' width='100%'>
				<tr>
					<td width='70%' class="littletableheaderc">Primera Compra</td>
					<td class='littletablerow'>
					<?php
					$query = $this->db->query("SELECT fecha, tipo_doc,numero, totalg FROM sfac WHERE cod_cli='".$form->cliente->value."' AND tipo_doc='F'ORDER BY fecha LIMIT 1 ");
					if($query->num_rows() == 0) {
						echo "No tiene compras registradas";
					} else {
						$row = $query->row_array();
						echo '<table><tr>';
					        echo "<td class='littletablerow'>".dbdate_to_human($row['fecha'])."</td>";
					        echo "<td class='littletablerow'>".$row['tipo_doc'].$row['numero']."</td>";
					        echo "<td class='littletablerow'>Bs.</td>";
					        echo "<td width='70' class='littletablerow' align='right'>".nformat($row['totalg'])."</td>";
						echo '</tr></table>';
					}
					?>
					</td>
				</tr>				
				<tr>
					<td class="littletableheaderc">Ultima Compra</td>
					<td class='littletablerow'>
						<?php
					$query = $this->db->query("SELECT fecha, tipo_doc,numero, totalg FROM sfac WHERE cod_cli='".$form->cliente->value."' AND tipo_doc='F'ORDER BY fecha DESC LIMIT 1 ");
					if($query->num_rows() == 0) {
						echo "No tiene compras registradas";
					} else { 
						$row = $query->row_array(); ?>
						<table>
							<tr>
								<td class='littletablerow'><?php echo dbdate_to_human($row['fecha']) ?></td>
								<td class='littletablerow'><?php echo $row['tipo_doc'].$row['numero']; ?></td>
								<td class='littletablerow'>Bs.</td>
								<td width='70' class='littletablerow' align='right'><?php echo nformat($row['totalg']) ?></td>
							</tr>
						</table>
					<?php } ?>
					</td>
				</tr>				
				<tr>
					<?php
					$mSQL = "
					SELECT sum(monto-abonos) saldo
					FROM smov
					WHERE cod_cli='".$form->cliente->value."' AND tipo_doc IN ('FC','ND','GI') AND monto>abonos AND vence>curdate()";
					$query = $this->db->query($mSQL);
					if($query->num_rows() == 0) {
						echo "No tiene efectos pendientes";
					} else {
						$row = $query->row_array();
						echo '<td class="littletableheaderc">Monto Vencido</td>';
						echo "<td width='100' class='littletablerow' align='left'>".nformat($row['saldo'])."</td>";
					}
					?>
				</tr>
				<tr>
					<?php
					$mSQL = "
					SELECT sum(monto-abonos) saldo
					FROM smov
					WHERE cod_cli='".$form->cliente->value."' AND tipo_doc IN ('AN','NC') AND monto>abonos ";
					$query = $this->db->query($mSQL);
					if($query->num_rows() == 0) {
						echo "No tiene Anticipos";
					} else {
						$row = $query->row_array();
						echo '<td class="littletableheaderc">Anticipos pendientes</td>';
						echo "<td width='100' class='littletablerow' align='left'>".nformat($row['saldo'])."</td>";
					}
					?>
				</tr>					
			</table>
		</td>
		<td>
			<table border='0' width='100%'>
				<tr>
					<td>
					<?php
					$query = $this->db->query("SELECT sum(monto*IF(tipo_doc IN ('AB','NC','AN'),0,1)) debe, sum(monto*IF(tipo_doc IN ('AB','NC','AN'),1,0)) haber, sum(monto*IF(tipo_doc IN ('AB','NC','AN'),-1,1)) saldo FROM smov WHERE cod_cli='".$form->cliente->value."'");
					if($query->num_rows() == 0) {
						echo "No tiene creditos registradas";
					} else {
						$row = $query->row_array(); ?>
						<table width="100%">
							<tr>
								<td class="littletableheaderc">Total Comprado</td>
								<td width='100' class='littletablerow' align='right'><?php echo nformat($row['debe']); ?></td>
							</tr>
							<tr>
								<td class="littletableheaderc">Total Pagado</td>
								<td width='100' class='littletablerow' align='right'><?php echo nformat($row['haber']); ?></td>
							</tr>
							<tr>
								<td class="littletableheaderc">Saldo Actual</td>
								<td width='100' class='littletablerow' align='right'><?php echo nformat($row['saldo']); ?></td>
							</tr>
							<tr>
								<td class="littletableheaderc">Credito Disponible</td>
								<td width='100' class='littletablerow' align='right'><?php echo nformat($form->limite->value-$row['saldo']); ?></td>
							</tr>
					<?php } ?>
						</table>
					</td>
				</tr>				
			</table>
		</td>
	</tr>				
	</table>
<?php } else {
echo "<center><h1>Informacion no Disponible en este momento </h1><center>";
}; ?>
</fieldset>

    </div>


</div>

<?php echo $container_bl.$container_br; ?>
<?php echo $form_end?>
<?php endif; ?>