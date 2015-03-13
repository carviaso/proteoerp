<?php echo $form_scripts?>
<?php echo $form_begin?>
<?php 
$container_tr=join("&nbsp;", $form->_button_container["TR"]);
$container_bl=join("&nbsp;", $form->_button_container["BL"]);
$container_br=join("&nbsp;", $form->_button_container["BR"]);

$rete=array();
$mSQL='SELECT TRIM(codigo) AS codigo,TRIM(CONCAT_WS("-",codigo,activida)) AS activida ,base1,tari1,pama1,TRIM(tipo) AS tipo FROM rete ORDER BY codigo';
$query = $this->db->query($mSQL);
if ($query->num_rows() > 0){
	foreach ($query->result() as $row){
		$ind='_'.$row->codigo;
		$rete[$ind]=array($row->activida,$row->base1,$row->tari1,$row->pama1,$row->tipo);
	}
}
$json_rete=json_encode($rete);


/*
<script language="javascript" type="text/javascript">
var rete     = <?php echo $json_rete;  ?>;

function importerete(){
	var codigo  = $("#codigorete").val();
	if(codigo.length>0){
		var importe = Number($("#base").val());
		var base1   = Number(eval('rete._'+codigo+'[1]'));
		var tari1   = Number(eval('rete._'+codigo+'[2]'));
		var pama1   = Number(eval('rete._'+codigo+'[3]'));

		var tt=codigo.substring(0,1);
		if(tt=='1')
			monto=(importe*base1*tari1)/10000;
		else if(importe>pama1)
			monto=((importe-pama1)*base1*tari1)/10000;
		else
			monto = 0;

		$("#reten").val(roundNumber(monto,2));
		$("#reten_val").text(nformat(monto,2));
	}
	totaliza();
}

function post_codigoreteselec(cod){
	var porcen=eval('rete._'+cod+'[2]');
	var base1 =eval('rete._'+cod+'[1]');
	$("#porcen").val(porcen);
	$("#porcen_val").text(nformat(porcen,2));
	importerete();
}


</script>

*/
?>
<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table border=0 width="100%">
	<tr>
		<td colspan='2'>
			<fieldset style='border: 1px outset #9AC8DA;background: #FFFDE9;'>
			<table style="width:100%;border-collapse:collapse;padding:0px;">
			<tr>
				<td class="littletableheaderc"><?php echo $form->codbanc->label;   ?></td>
				<td class="littletablerow"    ><?php echo $form->codbanc->output;  ?></td>
				<td class="littletableheaderc"><?php echo $form->sucursal->label;   ?></td>
				<td class="littletablerow"    ><?php echo $form->sucursal->output;  ?></td>
				<td class="littletableheaderc"><?php echo $form->departa->label;   ?></td>
				<td class="littletablerow"    ><?php echo $form->departa->output;  ?></td>
			</tr>
			</table>
			</fieldset>
		</td>
	</tr>

	<tr>
		<td>
			<fieldset style='border: 1px outset #9AC8DA;background: #FFFDE9;'>
			<table border=0 width="100%">
			<tr>
				<td class="littletableheaderc"><?php echo $form->rif->label;  ?></td>
				<td class="littletablerow"    ><?php echo $form->rif->output; ?></td>
				<td class="littletableheaderc"><?php echo $form->proveedor->label;  ?></td>
				<td class="littletablerow" colspan='3'   ><?php echo $form->proveedor->output; ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->numfac->label;    ?></td>
				<td class="littletablerow"    ><?php echo $form->numfac->output;   ?></td>
				<td class="littletableheaderc"><?php echo $form->fechafac->label;  ?></td>
				<td class="littletablerow"    ><?php echo $form->fechafac->output; ?></td>
				<td class="littletableheaderc"><?php echo $form->nfiscal->label; ?></td>
				<td class="littletablerow"    ><?php echo $form->nfiscal->output;?></td>
			</tr>

			</table>
			</fieldset>
		</td>
	</tr>
</table>

<fieldset style='border: 1px outset #9AC8DA;background: #E0ECF8;'>
<table  width="100%" border='0'>
	<tr>
		<td valign='top'>
			<table width= "100%" >
				<tr>
					<td class="littletableheaderc"><?php echo $form->codigo->label; ?></td>
					<td class="littletablerow">    <?php echo $form->codigo->output; ?>&nbsp;</td>
					<td class="littletableheaderc"><?php echo $form->descrip->label; ?></td>
					<td class="littletablerow">    <?php echo $form->descrip->output; ?>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<table  width="100%" border='0'>
	<tr>
		<td valign='top'>
			<table style="width:100%;border-collapse:collapse;padding:0px;" >
				<tr>
					<td class="littletableheaderc"><?php echo $form->exento->label; ?></td>
					<td class="littletablerow">    <?php echo $form->exento->output; ?>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->montasa->label;  ?></td>
					<td class="littletablerow">    <?php echo $form->montasa->output; ?>&nbsp;</td>
					<td class="littletableheaderc"><?php echo $form->tasa->label;  ?></td>
					<td class="littletablerow">    <?php echo $form->tasa->output; ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->monredu->label;  ?></td>
					<td class="littletablerow">    <?php echo $form->monredu->output; ?>&nbsp;</td>
					<td class="littletableheaderc"><?php echo $form->reducida->label;   ?></td>
					<td class="littletablerow">    <?php echo $form->reducida->output;  ?>&nbsp;</td>
				</tr>
				<tr>
					<td class="littletableheaderc"><?php echo $form->monadic->label;   ?></td>
					<td class="littletablerow">    <?php echo $form->monadic->output;  ?>&nbsp;</td>
					<td class="littletableheaderc"><?php echo $form->sobretasa->label;   ?></td>
					<td class="littletablerow">    <?php echo $form->sobretasa->output;  ?>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</fieldset>

<table  width="100%" border='0'>
	<tr>
		<td width='70%' align='right' class="littletableheaderc"><?php echo $form->importe->label; ?>: </td>
		<td class="littletablerow"><?php echo $form->importe->output; ?>&nbsp;</td>
	</tr>
</table>


<!--fieldset style='border: 1px outset #9AC8DA;background: #E0ECF8;'>
<table  width="100%" border='0'>
	<tr>
		<td valign='top'>
			<table width= "100%" >
			<tr>
				<td class="littletableheaderc"><?php echo $form->codigorete->label; ?>: </td>
				<td class="littletablerow"    ><?php echo $form->codigorete->output; ?>&nbsp;</td>
				<td class="littletableheaderc"><?php echo $form->base->label; ?>: </td>
				<td class="littletablerow"    ><?php echo $form->base->output; ?>&nbsp;</td>
				<td class="littletableheaderc"><?php echo $form->porcen->label; ?>: </td>
				<td class="littletablerow"    ><?php echo $form->porcen->output; ?>&nbsp;</td>
				<td class="littletableheaderc"><?php echo $form->reten->label; ?>: </td>
				<td class="littletablerow"    ><?php echo $form->reten->output; ?>&nbsp;</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</fieldset>

<table  width="100%" border='0'>
	<tr>
		<td width='70%' align='right' class="littletableheaderc"><?php echo $form->apagar->label; ?>: </td>
		<td class="littletablerow"><?php echo $form->apagar->output; ?>&nbsp;</td>
	</tr>
</table-->


<?php echo $container_bl.$container_br; ?>
<?php echo $form_end?>
