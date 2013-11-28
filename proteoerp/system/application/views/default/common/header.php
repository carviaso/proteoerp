
<table width='100%'>
	<tr>
		<td width='25%'>
			&nbsp;
		</td>
		<td>
			<h2><?php 
				if(($this->db->char_set=='latin1') && ($this->config->item('charset')=='UTF-8')){ 
					echo utf8_encode($this->datasis->traevalor('TITULO1')); 
				}else{
					echo $this->datasis->traevalor('TITULO1'); 
				}?>
			</h2>
<?php if ( $this->secu->es_logeado() == false ){ ?>
<p class="miniblanco1">
<?php 
	if(($this->db->char_set=='latin1') && ($this->config->item('charset')=='UTF-8')){
		echo utf8_encode($this->datasis->traevalor('TITULO2')).'<br>'.utf8_encode($this->datasis->traevalor('TITULO3'));
	}else{
		echo $this->datasis->traevalor('TITULO2').'<br>'.$this->datasis->traevalor('TITULO3');
	}
	echo '<br>RIF <b>'.$this->datasis->traevalor('RIF').'</b>';
?>
</p>
<?php } ?>
		</td>
		<td width='25%'>
			<?php echo $idus ?>
		</td>
	</tr>

</table>
<?php 
	//<img src="<?php echo base_url() ? >images/logo.jpg" height="38px" alt="Logotipo" >
	echo $menu; //Tabs 
?>
<?php
/*
<table align='center' border='0' width='99%' cellpadding='0' cellspacing='0'>
<tr>
	<td width="30%">&nbsp;</td>
	<td width="40%" align='center' NOWRAP><h2><?php 
	if(($this->db->char_set=='latin1') && ($this->config->item('charset')=='UTF-8')){ 
		echo utf8_encode($this->datasis->traevalor('TITULO1')); 
	}else{
		echo $this->datasis->traevalor('TITULO1'); 
	} ?></h2></td>
	<td width="30%" align="right"  NOWRAP><?php echo $idus ?></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td align="center" NOWRAP><p class="miniblanco1"><?php 
	if(($this->db->char_set=='latin1') && ($this->config->item('charset')=='UTF-8')){
		echo utf8_encode($this->datasis->traevalor('TITULO2')).'<br>'.utf8_encode($this->datasis->traevalor('TITULO3'));
	}else{
		echo $this->datasis->traevalor('TITULO2').'<br>'.$this->datasis->traevalor('TITULO3');
	}
	echo '<br>RIF '.$this->datasis->traevalor('RIF');?></p></td>
	<td align="right"  NOWRAP><img src="<?php echo base_url() ?>images/logo.jpg" height="38px" alt="Logotipo" ></td>
</tr>
</table>

*/
?>
