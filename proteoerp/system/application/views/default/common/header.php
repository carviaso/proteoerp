<div id="header">
<table align='center' border='0' width='99%' cellpadding='0' cellspacing='0'>
<tr>
	<td width="30%">&nbsp;</td>
	<td width="40%" align='center' NOWRAP><h2><?php echo $this->datasis->traevalor('TITULO1');  ?></h2></td>
	<td width="30%" align="right" NOWRAP><?php echo $idus ?></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td align="center" NOWRAP><p class="miniblanco1"><?php echo $this->datasis->traevalor('TITULO2').'<br>'.$this->datasis->traevalor('TITULO3').'<br>RIF '.$this->datasis->traevalor('RIF')?><p></td>
	<td align="right"  NOWRAP><img src="<?php echo base_url() ?>images/logo.jpg" height="38px" alt="Logotipo" ></td>
</tr>
</table>
</div>
<?php echo $menu ?>